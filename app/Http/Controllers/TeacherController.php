<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Test;
use App\Models\TestCategory;
use App\Models\UserTestAttempt;
use App\Models\TestQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || (!auth()->user()->isTeacher() && !auth()->user()->isAdmin())) {
                abort(403, 'Faqat o\'qituvchilar kirish huquqiga ega.');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $stats = [
            'total_tests' => Test::count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_attempts' => UserTestAttempt::count(),
        ];

        return view('teacher.dashboard', compact('stats'));
    }

    public function students()
    {
        $students = User::where('role', 'student')->paginate(20);
        return view('teacher.students', compact('students'));
    }

    public function results()
    {
        $results = UserTestAttempt::with(['user', 'test'])
            ->latest()
            ->paginate(20);
        return view('teacher.results', compact('results'));
    }
}