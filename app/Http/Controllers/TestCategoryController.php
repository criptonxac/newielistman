<?php

namespace App\Http\Controllers;

use App\Models\TestCategory;
use Illuminate\Http\Request;

class TestCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = TestCategory::active()
            ->ordered()
            ->withCount(['activeTests'])
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function show(TestCategory $category, Request $request)
    {
        $category->load(['activeTests' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        return view('categories.show', compact('category'));
    }
}
