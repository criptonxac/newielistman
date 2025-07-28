<?php

namespace App\Http\Controllers;

use App\Models\TestCategory;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = auth()->user();

        $stats = [
            'completed_tests' => $user->testAttempts()->whereNotNull('completed_at')->count(),
            'average_score' => $user->testAttempts()->whereNotNull('completed_at')->avg('total_score') ?? 0,
            'highest_score' => $user->testAttempts()->whereNotNull('completed_at')->max('total_score') ?? 0,
            'total_time' => 0, // Duration ustuni mavjud emas, 0 qilib qo'yamiz
        ];

        $recent_attempts = $user->testAttempts()
            ->with('test')
            ->latest()
            ->limit(5)
            ->get();

        $test_categories = TestCategory::withCount('tests')->get();

        return view('student.dashboard', compact('stats', 'recent_attempts', 'test_categories'));
    }

    public function tests(Request $request)
    {
        // Faqat admin va teacher tomonidan yaratilgan testlarni ko'rsatish
        $categories = TestCategory::with(['tests' => function ($query) {
            $query->where('is_active', true); // Faqat faol testlarni ko'rsatish
        }])->get();

        return view('student.tests', compact('categories'));
    }

    public function results(Request $request)
    {
        $user = auth()->user();
        $attempts = $user->testAttempts()
            ->with('test')
            ->latest()
            ->paginate(10);

        return view('student.results', compact('attempts'));
    }
}
