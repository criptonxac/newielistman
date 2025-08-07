<?php

namespace App\Http\Controllers;

use App\Enums\TestStatus;
use App\Enums\TestType;
use App\Models\AppTest;
use Illuminate\Http\Request;

class TestTypeController extends Controller
{
    /**
     * Test turlariga ko'ra testlarni ko'rsatish
     */
    public function showByType(Request $request, $type = null)
    {
        $types = TestType::cases();
        $statuses = TestStatus::cases();
        
        // Agar type berilmagan bo'lsa, birinchi turni olish
        if (!$type) {
            $type = TestType::FAMILIARISATION->value;
        }
        
        // Testlarni type bo'yicha olish
        $tests = AppTest::where('type', $type)
            ->where('is_active', true)
            ->get();
        
        return view('tests.by-type', [
            'tests' => $tests,
            'types' => $types,
            'statuses' => $statuses,
            'currentType' => $type,
            'pageTitle' => 'Testlar: ' . ucfirst($type)
        ]);
    }
}
