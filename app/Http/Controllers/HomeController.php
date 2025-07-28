<?php

namespace App\Http\Controllers;

use App\Models\TestCategory;
use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\User;
use App\Models\UserTestAttempt;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $testCategories = TestCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->with('activeTests')
            ->get();

        // So'nggi test statistikasi
        $totalTests = Test::where('is_active', true)->count();
        $totalAttempts = UserTestAttempt::count();
        $completedTests = UserTestAttempt::where('status', 'completed')->count();
        
        // Database statistikasi
        $totalUsers = User::count();
        $totalStudents = User::where('role', User::ROLE_STUDENT)->count();
        $totalTeachers = User::where('role', User::ROLE_TEACHER)->count();
        $totalQuestions = TestQuestion::count();

        $featuredTests = Test::where('is_active', true)
            ->where('type', 'familiarisation')
            ->with('category')
            ->take(3)
            ->get();

        return view('home', compact(
            'testCategories',
            'totalTests', 
            'totalAttempts',
            'completedTests',
            'featuredTests',
            'totalUsers',
            'totalStudents',
            'totalTeachers',
            'totalQuestions'
        ));
    }
}
