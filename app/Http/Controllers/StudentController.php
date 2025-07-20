<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Test;
use App\Models\TestCategory;
use App\Models\UserTestAttempt;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || !auth()->user()->isStudent()) {
                abort(403, 'Faqat talabalar kirish huquqiga ega.');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $user = auth()->user();
        
        $stats = [
            'completed_tests' => $user->testAttempts()->whereNotNull('completed_at')->count(),
            'average_score' => $user->testAttempts()->whereNotNull('completed_at')->avg('score') ?? 0,
            'highest_score' => $user->testAttempts()->whereNotNull('completed_at')->max('score') ?? 0,
            'total_time' => $user->testAttempts()->whereNotNull('completed_at')->sum('duration') ?? 0,
        ];

        $recent_attempts = $user->testAttempts()
            ->with('test')
            ->latest()
            ->limit(5)
            ->get();

        $test_categories = TestCategory::withCount('tests')->get();

        return view('student.dashboard', compact('stats', 'recent_attempts', 'test_categories'));
    }

    public function tests()
    {
        $categories = TestCategory::with('tests')->get();
        return view('student.tests', compact('categories'));
    }

    public function results()
    {
        $user = auth()->user();
        $attempts = $user->testAttempts()
            ->with('test')
            ->latest()
            ->paginate(10);

        return view('student.results', compact('attempts'));
    }
}