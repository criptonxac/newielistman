<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestResultController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user() || (!Auth::user()->isAdmin() && !Auth::user()->isTeacher())) {
                abort(403, 'Ruxsat berilmagan');
            }
            return $next($request);
        });
    }

    /**
     * Barcha natijalar
     */
    public function index(Request $request)
    {
        $query = TestAttempt::with(['user', 'test.category'])
            ->completed();

        // Teacher faqat o'z testlarining natijalarini ko'radi
        if (Auth::user()->isTeacher()) {
            $query->whereHas('test', function($q) {
                $q->where('created_by', Auth::id());
            });
        }

        // Filterlar
        if ($request->filled('test_id')) {
            $query->where('test_id', $request->test_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('completed_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('completed_at', '<=', $request->date_to);
        }

        $attempts = $query->latest('completed_at')->paginate(20);

        // Filter uchun ma'lumotlar
        $tests = Test::when(Auth::user()->isTeacher(), function($q) {
                $q->where('created_by', Auth::id());
            })
            ->orderBy('title')
            ->get();
            
        $students = User::where('role', 'student')
            ->orderBy('name')
            ->get();

        return view('teacher.results.index', compact('attempts', 'tests', 'students'));
    }

    /**
     * Bitta natija batafsil
     */
    public function show(TestAttempt $attempt)
    {
        // Teacher faqat o'z testining natijasini ko'rishi mumkin
        if (Auth::user()->isTeacher() && $attempt->test->created_by !== Auth::id()) {
            abort(403);
        }

        $attempt->load(['user', 'test.category', 'testAnswers.testQuestion']);

        return view('teacher.results.show', compact('attempt'));
    }

    /**
     * Test bo'yicha statistika
     */
    public function statistics(Test $test)
    {
        // Teacher faqat o'z testining statistikasini ko'rishi mumkin
        if (Auth::user()->isTeacher() && $test->created_by !== Auth::id()) {
            abort(403);
        }

        $stats = [
            'total_attempts' => $test->attempts()->completed()->count(),
            'unique_students' => $test->attempts()->completed()->distinct('user_id')->count('user_id'),
            'average_score' => $test->attempts()->completed()->avg('score'),
            'highest_score' => $test->attempts()->completed()->max('score'),
            'lowest_score' => $test->attempts()->completed()->min('score'),
            'pass_rate' => $test->attempts()->completed()->where('score', '>=', $test->pass_score)->count()
        ];

        if ($stats['total_attempts'] > 0) {
            $stats['pass_rate'] = round(($stats['pass_rate'] / $stats['total_attempts']) * 100, 2);
        }

        // Savollar bo'yicha statistika
        $questionStats = [];
        foreach ($test->questions as $question) {
            $totalAnswers = $question->answers()
                ->whereHas('testAttempt', function($q) {
                    $q->where('status', 'completed');
                })
                ->count();
                
            $correctAnswers = $question->answers()
                ->whereHas('testAttempt', function($q) {
                    $q->where('status', 'completed');
                })
                ->where('is_correct', true)
                ->count();

            $questionStats[] = [
                'question' => $question,
                'total_answers' => $totalAnswers,
                'correct_answers' => $correctAnswers,
                'accuracy' => $totalAnswers > 0 ? round(($correctAnswers / $totalAnswers) * 100, 2) : 0
            ];
        }

        // Vaqt bo'yicha taqsimot
        $attempts = $test->attempts()
            ->completed()
            ->latest()
            ->limit(20)
            ->get();

        return view('teacher.results.statistics', compact('test', 'stats', 'questionStats', 'attempts'));
    }

    /**
     * Natijalarni export qilish (Excel)
     */
    public function export(Request $request)
    {
        // Bu yerda Excel export logikasi bo'ladi
        // Maatwebsite/Excel package ishlatilishi mumkin
    }
}