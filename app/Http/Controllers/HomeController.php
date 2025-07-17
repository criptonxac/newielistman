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
        $testCategories = TestCategory::active()
            ->ordered()
            ->with(['activeTests' => function ($query) {
                $query->byType('familiarisation')->take(3);
            }])
            ->get();

        // So'nggi test statistikasi
        $totalTests = Test::active()->count();
        $totalAttempts = UserTestAttempt::count();
        $completedTests = UserTestAttempt::completed()->count();

        $featuredTests = Test::active()
            ->byType('familiarisation')
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
