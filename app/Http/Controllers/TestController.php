<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestAnswer;
use App\Models\TestCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isStudent()) {
                abort(403, 'Faqat talabalar test topshira oladi');
            }
            return $next($request);
        });
    }

    /**
     * Mavjud testlar ro'yxati
     */
    public function index()
    {
        $categories = TestCategory::active()
            ->with(['tests' => function($query) {
                $query->active()
                    ->withCount('questions')
                    ->with('audioFiles');
            }])
            ->get();

        $userAttempts = Auth::user()->listeningTestAttempts()
            ->with('test')
            ->get()
            ->groupBy('test_id');

        return view('student.tests', compact('categories', 'userAttempts'));
    }

    /**
     * Test haqida ma'lumot
     */
    public function show(Test $test)
    {
        if (!$test->is_active) {
            abort(404);
        }

        $test->load(['category', 'questions', 'audioFiles']);
        
        $userAttempts = Auth::user()->listeningTestAttempts()
            ->where('test_id', $test->id)
            ->latest()
            ->get();

        $canAttempt = Auth::user()->canAttemptTest($test->id);
        $inProgressAttempt = Auth::user()->getCurrentAttempt($test->id);

        return view('student.tests', compact('test', 'userAttempts', 'canAttempt', 'inProgressAttempt'));
    }

    /**
     * Testni boshlash
     */
    public function start(Test $test)
    {
        if (!$test->is_active) {
            abort(404);
        }

        // Ruxsat tekshirish
        if (!Auth::user()->canAttemptTest($test->id)) {
            return redirect()
                ->route('student.tests.index')
                ->with('error', 'Sizning urinishlar limitingiz tugagan.');
        }

        // Davom etayotgan test bormi tekshirish
        $inProgressAttempt = Auth::user()->getCurrentAttempt($test->id);
        if ($inProgressAttempt) {
            return redirect()->route('student.tests.take', [
                'test' => $test->slug,
                'attempt' => $inProgressAttempt->id
            ]);
        }

        // Yangi attempt yaratish
        $attempt = TestAttempt::create([
            'user_id' => Auth::id(),
            'test_id' => $test->id,
            'started_at' => now(),
            'status' => 'in_progress'
        ]);

        return redirect()->route('student.tests.take', [
            'test' => $test->slug,
            'attempt' => $attempt->id
        ]);
    }

    /**
     * Test topshirish sahifasi
     */
    public function take(Test $test, TestAttempt $attempt)
    {
        // Tekshirishlar
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()
                ->route('student.tests.index')
                ->with('info', 'Bu test allaqachon yakunlangan.');
        }

        // Vaqt tugaganmi tekshirish
        if ($attempt->getRemainingTime() <= 0) {
            // Testni avtomatik yakunlash
            DB::beginTransaction();
            try {
                // Attemptni yakunlash
                $attempt->complete();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
            }
            
            return redirect()
                ->route('student.tests.index')
                ->with('warning', 'Test vaqti tugadi va avtomatik yakunlandi.');
        }

        $test->load(['questions' => function($query) {
            $query->orderBy('part_number')->orderBy('question_number');
        }, 'audioFiles', 'category']);

        // Saqlangan javoblarni yuklash
        $savedAnswers = $attempt->testAnswers()
            ->pluck('user_answer', 'test_question_id')
            ->toArray();

        // Test tipiga qarab tegishli view'ni qaytarish
        if ($test->type === 'listening') {
            return redirect()->route('listening.test.part', [
                'test' => $test->slug,
                'part' => 1,
                'attemptCode' => $attempt->attempt_code
            ]);
        } elseif ($test->type === 'reading') {
            return redirect()->route('reading.test.part', [
                'test' => $test->slug,
                'part' => 1,
                'attemptCode' => $attempt->attempt_code
            ]);
        } else {
            return view('student.tests', compact('test', 'attempt', 'savedAnswers'));
        }
    }

    /**
     * Javobni saqlash (AJAX)
     */
    public function saveAnswer(Request $request, Test $test, TestAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id() || $attempt->status !== 'in_progress') {
            return response()->json(['success' => false, 'message' => 'Ruxsat berilmagan'], 403);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:test_questions,id',
            'answer' => 'nullable|string'
        ]);

        // Javobni saqlash yoki yangilash
        TestAnswer::updateOrCreate(
            [
                'test_attempt_id' => $attempt->id,
                'test_question_id' => $validated['question_id']
            ],
            [
                'user_answer' => $validated['answer']
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Testni yakunlash
     */
    public function submit(Request $request, Test $test, TestAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id() || $attempt->status !== 'in_progress') {
            abort(403);
        }

        DB::beginTransaction();

        try {
            // Barcha javoblarni saqlash
            if ($request->has('answers')) {
                foreach ($request->answers as $questionId => $answer) {
                    if (empty($answer)) continue;
                    
                    TestAnswer::updateOrCreate(
                        [
                            'test_attempt_id' => $attempt->id,
                            'test_question_id' => $questionId
                        ],
                        [
                            'user_answer' => $answer
                        ]
                    );
                }
            }

            // Javoblarni tekshirish va ball hisoblash
            foreach ($attempt->testAnswers as $testAnswer) {
                $testAnswer->checkAndScore();
            }

            // Attemptni yakunlash
            $attempt->complete();

            DB::commit();

            return redirect()
                ->route('student.tests.index')
                ->with('success', 'Test muvaffaqiyatli yakunlandi!');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()
                ->back()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Test natijasi
     */
    public function result(Test $test, TestAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        if ($attempt->status !== 'completed') {
            // Test tipiga qarab tegishli route'ga yo'naltirish
            if ($test->type === 'listening') {
                return redirect()->route('listening.test.part', [
                    'test' => $test->slug,
                    'part' => 1,
                    'attemptCode' => $attempt->attempt_code
                ])->with('warning', 'Test hali yakunlanmagan.');
            } elseif ($test->type === 'reading') {
                return redirect()->route('reading.test.part', [
                    'test' => $test->slug,
                    'part' => 1,
                    'attemptCode' => $attempt->attempt_code
                ])->with('warning', 'Test hali yakunlanmagan.');
            } else {
                return redirect()->route('student.tests.index')
                    ->with('warning', 'Test hali yakunlanmagan.');
            }
        }

        $attempt->load(['testAnswers.testQuestion']);

        // Test tipiga qarab tegishli view'ni qaytarish
        if ($test->type === 'listening') {
            return view('listening.result', compact('test', 'attempt'));
        } elseif ($test->type === 'reading') {
            return view('reading.result', compact('test', 'attempt'));
        } else {
            return view('student.tests', compact('test', 'attempt'));
        }
    }

    /**
     * Test tarixim
     */
    public function history()
    {
        $attempts = Auth::user()->listeningTestAttempts()
            ->with(['test.category'])
            ->completed()
            ->latest()
            ->paginate(15);

        $stats = Auth::user()->getListeningStats();

        return view('student.tests.history', compact('attempts', 'stats'));
    }
}