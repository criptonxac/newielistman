<?php

namespace App\Http\Controllers;

use App\Enums\TestStatus;
use App\Enums\TestType;
use App\Enums\UserRole;
use Illuminate\Http\Request;

class EnumController extends Controller
{
    /**
     * Barcha enumlarni ko'rsatish
     */
    public function index()
    {
        $enums = [
            'TestType' => [
                'title' => 'Test turlari',
                'values' => TestType::toArray()
            ],
            'TestStatus' => [
                'title' => 'Test holatlari',
                'values' => TestStatus::toArray()
            ],
            'UserRole' => [
                'title' => 'Foydalanuvchi rollari',
                'values' => UserRole::toArray()
            ]
        ];
        
        return view('enums.index', compact('enums'));
    }
}
