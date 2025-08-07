<?php

namespace App\Http\Controllers;

use App\Enums\TestStatus;
use App\Enums\TestType;
use App\Enums\UserRole;
use App\Models\TestCategory;
use App\Models\AppTest;
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
        $totalTests = AppTest::where('is_active', true)->count();
        $totalAttempts = UserTestAttempt::count();
        $completedTests = UserTestAttempt::where('status', TestStatus::COMPLETED)->count();
         
        // Database statistikasi
        $totalUsers = User::count();
        $totalStudents = User::where('role', UserRole::STUDENT)->count();
        $totalTeachers = User::where('role', UserRole::TEACHER)->count();
        $totalQuestions = TestQuestion::count();

        $featuredTests = AppTest::where('is_active', true)
            ->where('type', TestType::FAMILIARISATION)
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
