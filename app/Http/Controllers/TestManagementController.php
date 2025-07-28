<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\TestCategory;
use App\Models\TestQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        // Admin dashboard'ga yo'naltirish
        $adminController = new AdminController();
        return $adminController->dashboard($request);
    }
    
    /**
     * Yangi test yaratish formasini ko'rsatish
     */
    public function create()
    {
        $categories = TestCategory::all();
        $layout = auth()->user()->role === 'admin' ? 'admin.dashboard' : 'teacher.dashboard';
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
        $layout = auth()->user()->role === 'admin' ? 'admin.dashboard' : 'teacher.dashboard';
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
        // Testga bog'liq barcha ma'lumotlarni o'chirish
        $test->delete();
        
        return redirect()->route('test-management.index')
            ->with('success', 'Test muvaffaqiyatli o\'chirildi.');
    }
    
    /**
     * Savollar yaratish sahifasini ko'rsatish
     */
    public function createQuestions(Test $test)
    {
        $test->load('questions');
        $layout = auth()->user()->role === 'admin' ? 'admin.dashboard' : 'teacher.dashboard';
        return view('test-management.questions.create', compact('test', 'layout'));
    }
    
    /**
     * Savollarni saqlash
     */
    public function storeQuestions(Request $request, Test $test)
    {
        $validated = $request->validate([
            'questions' => 'required|array',
            'questions.*.question_text' => 'required|string',
            'questions.*.question_type' => 'required|string',
            'questions.*.options' => 'nullable|array',
            'questions.*.correct_answer' => 'nullable|string',
            'questions.*.correct_answers' => 'nullable|array',
            'questions.*.points' => 'required|integer|min:1',
            'questions.*.sort_order' => 'required|integer|min:1',
            'questions.*.mapping_target' => 'nullable|string',
            'audio_files' => 'nullable|array',
            'audio_files.*' => 'nullable|file|mimes:mp3,wav,ogg|max:102400',
        ]);
        
        // Savollarni saqlash
        foreach ($validated['questions'] as $questionData) {
            $question = new TestQuestion();
            $question->test_id = $test->id;
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
        
        return redirect()->route('test-management.index')
            ->with('success', 'Savollar muvaffaqiyatli qo\'shildi.');
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
     * Yangi savol qo'shish sahifasini ko'rsatish
     */
    public function addQuestion(Test $test)
    {
        $layout = auth()->user()->role === 'admin' ? 'admin.dashboard' : 'teacher.dashboard';
        return view('test-management.questions.add', compact('test', 'layout'));
    }
    
    /**
     * Enumlarni jadval ko'rinishida ko'rsatish
     */
    public function showEnums()
    {
        $layout = auth()->user()->role === 'admin' ? 'admin.dashboard' : 'teacher.dashboard';
        return view('test-management.enums', compact('layout'));
    }
}
