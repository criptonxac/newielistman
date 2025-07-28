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
    public function index()
    {
        $tests = Test::with('category')->orderBy('created_at', 'desc')->get();
        $layout = auth()->user()->role === 'admin' ? 'admin.dashboard' : 'teacher.dashboard';
        
        // Admin dashboard uchun statistika ma'lumotlarini tayyorlash
        $stats = [];
        $todayAverageScore = 0;
        $weeklyActivity = [];
        $topStudents = collect();
        $studentActivity = [];
        $weeklyStats = [];
        
        if (auth()->user()->role === 'admin') {
            $stats = [
                'total_users' => \App\Models\User::count(),
                'total_students' => \App\Models\User::where('role', 'student')->count(),
                'total_questions' => \App\Models\TestQuestion::count(),
                'total_teachers' => \App\Models\User::where('role', 'teacher')->count(),
                'total_tests' => \App\Models\Test::count(),
            ];
            
            // Talabalar faolligi
            $studentActivity = [
                'active_students' => \App\Models\User::where('role', 'student')->count(),
                'completed_tests' => 156, // Test uchun statik qiymat
                'average_score' => 6.8,   // Test uchun statik qiymat
                'highest_score' => 8.5    // Test uchun statik qiymat
            ];
            
            // Haftalik statistika
            $weeklyStats = [
                ['date' => 'Dushanba', 'tests' => 12, 'students' => 45, 'count' => 12],
                ['date' => 'Seshanba', 'tests' => 18, 'students' => 52, 'count' => 18],
                ['date' => 'Chorshanba', 'tests' => 15, 'students' => 48, 'count' => 15],
                ['date' => 'Payshanba', 'tests' => 22, 'students' => 57, 'count' => 22],
                ['date' => 'Juma', 'tests' => 28, 'students' => 63, 'count' => 28],
                ['date' => 'Shanba', 'tests' => 16, 'students' => 42, 'count' => 16],
                ['date' => 'Yakshanba', 'tests' => 8, 'students' => 35, 'count' => 8]
            ];
            
            // O'rtacha ball
            $todayAverageScore = 7.5; // Bu yerda haqiqiy ma'lumotlarni olish kerak
            
            // Haftalik faollik
            $weeklyActivity = [
                'labels' => ['Dushanba', 'Seshanba', 'Chorshanba', 'Payshanba', 'Juma', 'Shanba', 'Yakshanba'],
                'data' => [15, 22, 18, 24, 33, 28, 19],
                'activity_percentage' => 68,
                'total_tests' => \App\Models\Test::count(),
                'avg_daily' => round(\App\Models\Test::count() / 7)
            ];
            
            // Top talabalar
            $topStudents = \App\Models\User::where('role', 'student')
                ->take(10)
                ->get()
                ->map(function($student) {
                    // Har bir talaba uchun o'rtacha ball qo'shish
                    $student->average_score = rand(50, 90) / 10; // Test uchun tasodifiy qiymat
                    return $student;
                });
        }
        
        return view('test-management.index', compact('tests', 'layout', 'stats', 'todayAverageScore', 'weeklyActivity', 'topStudents', 'studentActivity', 'weeklyStats'));
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
        
        // Slug yaratish
        $validated['slug'] = Str::slug($validated['title']);
        
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
}
