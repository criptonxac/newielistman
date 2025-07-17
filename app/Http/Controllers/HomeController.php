<?php

namespace App\Http\Controllers;

use App\Models\TestCategory;
use App\Models\Test;
use App\Models\UserTestAttempt;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
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
            'featuredTests'
        ));
    }
}
