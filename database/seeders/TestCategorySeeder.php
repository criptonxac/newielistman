<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Listening',
                'slug' => 'listening',
                'description' => 'IELTS Listening testida 4 ta yozuv tinglaysiz va ular asosida 40 ta savolga javob berasiz. Test taxminan 30 daqiqa davom etadi.',
                'icon' => 'fas fa-headphones',
                'duration_minutes' => 30,
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Academic Reading',
                'slug' => 'academic-reading',
                'description' => 'IELTS Academic Reading testida 3 ta uzun matnni o\'qib, jami 40 ta savolga javob berasiz. Test 60 daqiqa davom etadi.',
                'icon' => 'fas fa-book-open',
                'duration_minutes' => 60,
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Academic Writing',
                'slug' => 'academic-writing',
                'description' => 'IELTS Academic Writing testida 2 ta yozma vazifani bajarasiz. Birinchi vazifa - grafik, jadval yoki diagramma tahlili, ikkinchisi - esse yozish.',
                'icon' => 'fas fa-pen',
                'duration_minutes' => 60,
                'is_active' => true,
                'sort_order' => 3
            ]
        ];

        foreach ($categories as $category) {
            \App\Models\TestCategory::create($category);
        }
    }
}
