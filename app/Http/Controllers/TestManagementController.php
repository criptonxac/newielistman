<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\TestCategory;
use App\Models\TestQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TestManagementController extends Controller
{
    /**
     * Test yaratish va tahrirlash uchun controller
     * Bu controller orqali o'qituvchi va adminlar test yaratishi mumkin
     */
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\CheckRole::class.':admin,teacher');
    }
    
    /**
     * Testlar ro'yxatini ko'rsatish
     */
    public function index(Request $request)
    {
        // Test-management.index view'ni to'g'ridan-to'g'ri render qilish
        $query = Test::with('category');
        
        // Type bo'yicha filterlash
        if ($request->has('type') && in_array($request->type, ['familiarisation', 'sample', 'practice'])) {
            $query->where('type', $request->type);
        }
        
        $tests = $query->orderBy('created_at', 'desc')->get();
        $layout = 'layouts.test-management';
        
        return view('test-management.index', compact('tests', 'layout'));
    }
    
    /**
     * Yangi test yaratish formasini ko'rsatish
     */
    public function create()
    {
        $categories = TestCategory::all();
        $layout = 'layouts.test-management';
        return view('test-management.create', compact('categories', 'layout'));
    }
    
    /**
     * Yangi testni saqlash
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reading_passage' => 'nullable|string',
            'test_category_id' => 'required|exists:test_categories,id',
            'type' => 'required|in:familiarisation,sample,practice',
            'duration_minutes' => 'nullable|integer|min:1',
            'instructions' => 'nullable|array',
            'is_active' => 'boolean',
            'is_timed' => 'boolean'
        ]);
        
        // Unikal slug yaratish
        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug;
        $counter = 1;
        
        // Slug unikal bo'lmaguncha raqam qo'shib borish
        while (Test::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        
        $validated['slug'] = $slug;
    
        // Test avtomatik ravishda faol bo'lishi kerak
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }
        
        // Instructions ni JSON formatga o'tkazish
        if (isset($validated['instructions'])) {
            $validated['instructions'] = json_encode($validated['instructions']);
        }
        
        $test = Test::create($validated);
            
        return redirect()->route('test-management.questions.create', $test)
            ->with('success', 'Test muvaffaqiyatli yaratildi. Endi savollarni qo\'shing.');
    }
    
    /**
     * Testni tahrirlash formasini ko'rsatish
     */
    public function edit(Test $test)
    {
        $categories = TestCategory::all();
        $layout = 'layouts.test-management';
        return view('test-management.edit', compact('test', 'categories', 'layout'));
    }
    
    /**
     * Testni yangilash
     */
    public function update(Request $request, Test $test)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reading_passage' => 'nullable|string',
            'test_category_id' => 'required|exists:test_categories,id',
            'type' => 'required|in:familiarisation,sample,practice',
            'duration_minutes' => 'nullable|integer|min:1',
            'instructions' => 'nullable|array',
            'is_active' => 'boolean',
            'is_timed' => 'boolean'
        ]);
        
        // Slug yangilash
        $validated['slug'] = Str::slug($validated['title']);
        
        // Instructions ni JSON formatga o'tkazish
        if (isset($validated['instructions'])) {
            $validated['instructions'] = json_encode($validated['instructions']);
        }
        
        $test->update($validated);
        
        return redirect()->route('test-management.index')
            ->with('success', 'Test muvaffaqiyatli yangilandi.');
    }
    
    /**
     * Testni o'chirish
     */
    public function destroy(Test $test)
    {
        $test->delete();
        
        return redirect()->route('test-management.index')
            ->with('success', 'Test muvaffaqiyatli o\'chirildi.');
    }
    
    /**
     * Savollar yaratish sahifasini ko'rsatish
     */
    public function createQuestions(Test $test, Request $request)
    {
        $layout = 'layouts.test-management';
        
        // Test uchun savollarni olish va pagination qilish
        $questions = $test->questions()->orderBy('sort_order')->paginate(10);
        
        // Pagination URL manzillarini qayta sozlash
        $questions->withPath(route('test-management.questions.create', $test->id));
        
        return view('test-management.questions.create', compact('test', 'layout', 'questions'));
    }
    
    /**
     * Bitta savolni saqlash (pagination bilan)
     */
    public function storeQuestion(Request $request, Test $test)
    {
        // Savol turini to'g'rilash
        if ($request->has('question_type')) {
            // multiple_answer va drag_drop qiymatlarini to'g'rilash
            if ($request->question_type === 'multiple_answer' || $request->question_type === 'drag_drop') {
                $request->merge(['question_type' => 'matching']);
            }
        }
        
        // Savollar sonini tekshirish (mavjud savollar 40 tadan oshmasligi kerak)
        $currentQuestionCount = $test->questions()->count();
        
        if ($currentQuestionCount >= 40) {
            return redirect()->back()->with('error', "Testda savollar soni 40 tadan oshmasligi kerak. Hozirgi savol soni: {$currentQuestionCount}");
        }
        
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|string|in:multiple_choice,fill_blank,essay,short_answer,true_false,matching',
            'options' => 'nullable|array',
            'correct_answer' => 'nullable|string',
            'correct_answers' => 'nullable|array',
            'mapping_target' => 'nullable|string',
            'points' => 'required|integer|min:1',
            'sort_order' => 'required|integer|min:1'
        ]);
        
        $question = new TestQuestion();
        $question->test_id = $test->id;
        
        // Keyingi savol raqamini aniqlash
        $lastQuestion = TestQuestion::where('test_id', $test->id)->orderBy('question_number', 'desc')->first();
        $question->question_number = $lastQuestion ? $lastQuestion->question_number + 1 : 1;
        
        $question->question_text = $validated['question_text'];
        $question->question_type = $validated['question_type'];
        
        // Options ni JSON formatga o'tkazish
        if (isset($validated['options'])) {
            $question->options = json_encode($validated['options']);
        }
        
        // Multiple correct answers or single correct answer
        if (isset($validated['correct_answers']) && is_array($validated['correct_answers'])) {
            $question->correct_answer = json_encode($validated['correct_answers']);
        } else if (isset($validated['correct_answer'])) {
            $question->correct_answer = $validated['correct_answer'];
        }
        
        // Mapping target for drag-and-drop
        if (isset($validated['mapping_target'])) {
            $question->mapping_target = $validated['mapping_target'];
        }
        
        $question->points = $validated['points'];
        $question->sort_order = $validated['sort_order'];
        $question->save();
        
        // Test savollar sonini yangilash
        $test->total_questions = $test->questions()->count();
        $test->save();
        
        return redirect()->route('test-management.questions.create', $test)
            ->with('success', 'Savol muvaffaqiyatli qo\'shildi.');
    }
    
    /**
     * Savollarni saqlash
     */
    public function storeQuestions(Request $request, Test $test)
    {
        // Agar GET so'rovi bo'lsa, createQuestions metodiga yo'naltirish
        if ($request->isMethod('get')) {
            return $this->createQuestions($test, $request);
        }
        
        // Savol turlarini to'g'rilash
        if ($request->has('questions')) {
            foreach ($request->questions as $key => $question) {
                if (isset($question['question_type'])) {
                    // multiple_answer va drag_drop qiymatlarini to'g'rilash
                    if ($question['question_type'] === 'multiple_answer' || $question['question_type'] === 'drag_drop') {
                        $request->merge([
                            'questions.' . $key . '.question_type' => 'matching'
                        ]);
                    }
                }
            }
        }
        
        // Savollar sonini tekshirish (mavjud + yangi savollar 40 tadan oshmasligi kerak)
        $currentQuestionCount = $test->questions()->count();
        $newQuestionsCount = count($request->questions ?? []);
        
        // Agar yangi savollar qo'shilayotgan bo'lsa (yangi ID lar bo'lmasa)
        $newQuestions = collect($request->questions ?? [])->filter(function($item, $key) {
            return !isset($item['id']) || empty($item['id']);
        });
        
        $newQuestionsCount = $newQuestions->count();
        $totalQuestions = $currentQuestionCount + $newQuestionsCount;
        
        if ($totalQuestions > 40) {
            return redirect()->back()->with('error', "Testda savollar soni 40 tadan oshmasligi kerak. Hozir: {$currentQuestionCount}, qo'shilmoqchi: {$newQuestionsCount}");
        }
        
        $validated = $request->validate([
            'questions' => 'required|array',
            'questions.*.question_text' => 'required|string',
            'questions.*.question_type' => 'required|string|in:multiple_choice,fill_blank,essay,short_answer,true_false,matching',
            'questions.*.options' => 'nullable|array',
            'questions.*.correct_answer' => 'nullable|string',
            'questions.*.correct_answers' => 'nullable|array',
            'questions.*.mapping_target' => 'nullable|string',
            'questions.*.points' => 'required|integer|min:1',
            'questions.*.sort_order' => 'required|integer|min:1',
        ]);
        
        // Savollarni saqlash
        $lastQuestion = TestQuestion::where('test_id', $test->id)->orderBy('question_number', 'desc')->first();
        $nextQuestionNumber = $lastQuestion ? $lastQuestion->question_number + 1 : 1;
        
        foreach ($validated['questions'] as $questionData) {
            $question = new TestQuestion();
            $question->test_id = $test->id;
            $question->question_number = $nextQuestionNumber++;
            $question->question_text = $questionData['question_text'];
            $question->question_type = $questionData['question_type'];
            $question->options = isset($questionData['options']) ? json_encode($questionData['options']) : null;
            
            // Multiple correct answers or single correct answer
            if (isset($questionData['correct_answers']) && is_array($questionData['correct_answers'])) {
                $question->correct_answer = json_encode($questionData['correct_answers']);
            } else if (isset($questionData['correct_answer'])) {
                $question->correct_answer = $questionData['correct_answer'];
            }
            
            // Mapping target for drag-and-drop
            if (isset($questionData['mapping_target'])) {
                $question->mapping_target = $questionData['mapping_target'];
            }
            
            $question->points = $questionData['points'];
            $question->sort_order = $questionData['sort_order'];
            $question->save();
        }
        
        // Audio fayllarni saqlash
        if ($request->hasFile('audio_files')) {
            $resources = [];
            
            foreach ($request->file('audio_files') as $key => $file) {
                $path = $file->store('test_audio', 'public');
                $resources[$key] = [
                    'type' => 'audio',
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName()
                ];
            }
            
            // Test resources ni yangilash
            $test->resources = json_encode($resources);
            $test->save();
        }
        
        // Test savollar sonini yangilash
        $test->total_questions = $test->questions()->count();
        $test->save();
        
        // Always redirect to the next page after saving
        if ($request->has('current_page')) {
            $currentPage = (int)$request->input('current_page');
            
            // If we're on page 4 (last page), redirect to index
            if ($currentPage >= 4) {
                return redirect()->route('test-management.index')
                    ->with('success', 'Barcha savollar muvaffaqiyatli qo\'shildi.');
            }
            
            // Otherwise, redirect to the next page
            return redirect()->route('test-management.questions.add', [
                $test->id,
                'page' => $currentPage + 1
            ])->with('success', 'Savollar muvaffaqiyatli qo\'shildi. Keyingi sahifaga o\'tildi.');
        }
        
        return redirect()->route('test-management.questions.add', [
            $test->id,
            'page' => 2
        ])->with('success', 'Savollar muvaffaqiyatli qo\'shildi. Keyingi sahifaga o\'tildi.');
    }
    
    /**
     * Savollarni tahrirlash sahifasini ko'rsatish
     */
    public function editQuestions(Test $test)
    {
        $test->load('questions');
        return view('test-management.questions.edit', compact('test'));
    }
    
    /**
     * Savollarni yangilash
     */
    public function updateQuestions(Request $request, Test $test)
    {
        // Savol turlarini to'g'rilash
        if ($request->has('questions')) {
            foreach ($request->questions as $key => $question) {
                if (isset($question['question_type'])) {
                    // multiple_answer va drag_drop qiymatlarini to'g'rilash
                    if ($question['question_type'] === 'multiple_answer' || $question['question_type'] === 'drag_drop') {
                        $request->merge([
                            'questions.' . $key . '.question_type' => 'matching'
                        ]);
                    }
                }
            }
        }
        
        // Savollar sonini tekshirish (mavjud + yangi savollar 40 tadan oshmasligi kerak)
        $currentQuestionCount = $test->questions()->count();
        $newQuestions = collect($request->questions ?? [])->filter(function($item, $key) {
            return !isset($item['id']) || empty($item['id']);
        });
        
        $newQuestionsCount = $newQuestions->count();
        $totalQuestions = $currentQuestionCount + $newQuestionsCount;
        
        if ($totalQuestions > 40) {
            return redirect()->back()->with('error', "Testda savollar soni 40 tadan oshmasligi kerak. Hozir: {$currentQuestionCount}, qo'shilmoqchi: {$newQuestionsCount}");
        }
        
        $validated = $request->validate([
            'questions' => 'required|array',
            'questions.*.question_text' => 'required|string',
            'questions.*.question_type' => 'required|string|in:multiple_choice,fill_blank,essay,short_answer,true_false,matching',
            'questions.*.points' => 'required|integer|min:1',
            'questions.*.correct_answer' => 'nullable|string',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*' => 'nullable|string',
            'questions.*.sort_order' => 'required|integer',
            'questions.*.mapping_target' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Mavjud savollarni yangilash va yangilarini qo'shish
            foreach ($request->questions as $key => $questionData) {
                if (isset($questionData['id']) && !empty($questionData['id'])) {
                    // Mavjud savolni yangilash
                    $question = TestQuestion::findOrFail($questionData['id']);
                    $question->update([
                        'question_text' => $questionData['question_text'],
                        'question_type' => $questionData['question_type'],
                        'points' => $questionData['points'],
                        'correct_answer' => $questionData['correct_answer'] ?? null,
                        'options' => $questionData['options'] ?? null,
                        'sort_order' => $questionData['sort_order'] ?? 0,
                        'mapping_target' => $questionData['mapping_target'] ?? null,
                    ]);
                } else {
                    // Yangi savol qo'shish
                    $question = new TestQuestion([
                        'test_id' => $test->id,
                        'question_text' => $questionData['question_text'],
                        'question_type' => $questionData['question_type'],
                        'points' => $questionData['points'],
                        'correct_answer' => $questionData['correct_answer'] ?? null,
                        'options' => $questionData['options'] ?? null,
                        'sort_order' => $questionData['sort_order'] ?? 0,
                        'question_number' => $test->questions()->count() + 1,
                        'mapping_target' => $questionData['mapping_target'] ?? null,
                    ]);
                    $question->save();
                }
            }
            
            // Formada ko'rsatilmagan savollarni o'chirish
            $existingQuestionIds = $test->questions()->pluck('id')->toArray();
            $submittedQuestionIds = collect($request->questions)
                ->filter(function($item) {
                    return isset($item['id']) && !empty($item['id']);
                })
                ->pluck('id')
                ->toArray();
            
            $questionIdsToDelete = array_diff($existingQuestionIds, $submittedQuestionIds);
            if (!empty($questionIdsToDelete)) {
                TestQuestion::whereIn('id', $questionIdsToDelete)->delete();
            }
            
            // Savollar sonini yangilash
            $test->update([
                'total_questions' => $test->questions()->count(),
            ]);
            
            DB::commit();
            
            return redirect()->route('test-management.index')
                ->with('success', 'Savollar muvaffaqiyatli yangilandi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Yangi savol qo'shish sahifasini ko'rsatish
     */
    public function addQuestion(Test $test, Request $request)
    {
        // Get current page from request or default to page 1
        $currentPage = $request->input('page', 1);
        
        // Ensure currentPage is within valid range (1-4)
        $currentPage = max(1, min(4, (int)$currentPage));
        
        $layout = 'layouts.test-management';
        return view('test-management.questions.add', compact('test', 'layout', 'currentPage'));
    }
    
    /**
     * Enumlarni jadval ko'rinishida ko'rsatish
     */
    public function showEnums()
    {
        $layout = 'layouts.test-management';
        return view('test-management.enums', compact('layout'));
    }
}
