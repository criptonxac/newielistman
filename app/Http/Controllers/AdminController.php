<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Test;
use App\Models\TestCategory;
use App\Models\UserTestAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || !auth()->user()->isAdmin()) {
                abort(403, 'Faqat adminlar kirish huquqiga ega.');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_teachers' => User::where('role', 'teacher')->count(),
            'total_tests' => Test::count(),
            'total_attempts' => UserTestAttempt::count(),
        ];

        // Demo data for charts
        $weeklyStats = collect([
            ['date' => 'Dushanba', 'count' => 45],
            ['date' => 'Seshanba', 'count' => 62],
            ['date' => 'Chorshanba', 'count' => 38],
            ['date' => 'Payshanba', 'count' => 71],
            ['date' => 'Juma', 'count' => 89],
            ['date' => 'Shanba', 'count' => 56],
            ['date' => 'Yakshanba', 'count' => 43],
        ]);

        $categoryStats = collect([
            ['name' => 'Listening', 'count' => 156],
            ['name' => 'Academic Reading', 'count' => 134],
            ['name' => 'General Reading', 'count' => 98],
            ['name' => 'Academic Writing', 'count' => 87],
            ['name' => 'General Writing', 'count' => 76],
        ]);

        return view('admin.dashboard', compact('stats', 'weeklyStats', 'categoryStats'));
    }

    public function users()
    {
        $users = User::paginate(20);
        return view('admin.users', compact('users'));
    }

    public function tests()
    {
        $tests = Test::with('category')->paginate(20);
        return view('admin.tests', compact('tests'));
    }
}