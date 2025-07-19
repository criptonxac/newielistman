
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
            'total_tests' => $user->testAttempts()->count(),
            'completed_tests' => $user->completedAttempts()->count(),
            'average_score' => $user->getAverageScore(),
            'best_score' => $user->completedAttempts()->max('total_score') ?? 0,
        ];

        $recentAttempts = $user->testAttempts()
            ->with('test.category')
            ->latest()
            ->limit(5)
            ->get();

        $categories = TestCategory::with(['activeTests' => function($query) {
            $query->where('is_active', true);
        }])->get();

        return view('student.dashboard', compact('stats', 'recentAttempts', 'categories'));
    }

    public function tests()
    {
        $categories = TestCategory::with(['activeTests' => function($query) {
            $query->where('is_active', true);
        }])->get();

        return view('student.tests', compact('categories'));
    }

    public function results()
    {
        $user = auth()->user();
        $attempts = $user->completedAttempts()
            ->with(['test.category'])
            ->latest()
            ->paginate(10);

        return view('student.results', compact('attempts'));
    }

    public function profile()
    {
        return view('student.profile');
    }
}
