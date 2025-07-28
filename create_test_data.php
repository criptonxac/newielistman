<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TestCategory;
use App\Models\Test;

// Test kategoriyalarini yaratish
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
    ],
];

// Kategoriyalarni yaratish
$categoryIds = [];
foreach ($categories as $categoryData) {
    $category = TestCategory::firstOrCreate(
        ['slug' => $categoryData['slug']],
        $categoryData
    );
    $categoryIds[$categoryData['slug']] = $category->id;
    echo "Kategoriya yaratildi: " . $category->name . " (ID: " . $category->id . ")\n";
}

// Testlarni yaratish
$tests = [
    // Listening Tests
    [
        'title' => 'IELTS Listening Test 1',
        'slug' => 'ielts-listening-test-1',
        'description' => 'Bu IELTS Listening testida 4 ta yozuvni tinglaysiz va ular asosida 40 ta savolga javob berasiz.',
        'test_category_id' => $categoryIds['listening'],
        'type' => 'practice',
        'duration_minutes' => 30,
        'total_questions' => 40,
        'instructions' => [
            'Test 4 ta qismdan iborat',
            'Har bir yozuv faqat bir marta ijro etiladi',
            'Savollarni oldindan o\'qish uchun vaqt beriladi',
            'Javoblarni test davomida yozib borishingiz mumkin'
        ],
        'is_active' => true,
        'is_timed' => true
    ],
    [
        'title' => 'IELTS Listening Test 2',
        'slug' => 'ielts-listening-test-2',
        'description' => 'Bu IELTS Listening testida 4 ta yozuvni tinglaysiz va ular asosida 40 ta savolga javob berasiz.',
        'test_category_id' => $categoryIds['listening'],
        'type' => 'practice',
        'duration_minutes' => 30,
        'total_questions' => 40,
        'instructions' => [
            'Test 4 ta qismdan iborat',
            'Har bir yozuv faqat bir marta ijro etiladi',
            'Savollarni oldindan o\'qish uchun vaqt beriladi',
            'Javoblarni test davomida yozib borishingiz mumkin'
        ],
        'is_active' => true,
        'is_timed' => true
    ],
    
    // Academic Reading Tests
    [
        'title' => 'IELTS Academic Reading Test 1',
        'slug' => 'ielts-academic-reading-test-1',
        'description' => 'Bu IELTS Academic Reading testida 3 ta uzun matnni o\'qib, jami 40 ta savolga javob berasiz.',
        'test_category_id' => $categoryIds['academic-reading'],
        'type' => 'practice',
        'duration_minutes' => 60,
        'total_questions' => 40,
        'instructions' => [
            'Test 3 ta qismdan iborat',
            'Har bir qismda bir yoki bir nechta matn',
            'Savollar turli xil: multiple choice, true/false, matching',
            'Vaqt chegarasi 60 daqiqa'
        ],
        'is_active' => true,
        'is_timed' => true
    ],
    [
        'title' => 'IELTS Academic Reading Test 2',
        'slug' => 'ielts-academic-reading-test-2',
        'description' => 'Bu IELTS Academic Reading testida 3 ta uzun matnni o\'qib, jami 40 ta savolga javob berasiz.',
        'test_category_id' => $categoryIds['academic-reading'],
        'type' => 'practice',
        'duration_minutes' => 60,
        'total_questions' => 40,
        'instructions' => [
            'Test 3 ta qismdan iborat',
            'Har bir qismda bir yoki bir nechta matn',
            'Savollar turli xil: multiple choice, true/false, matching',
            'Vaqt chegarasi 60 daqiqa'
        ],
        'is_active' => true,
        'is_timed' => true
    ],
    
    // Academic Writing Tests
    [
        'title' => 'IELTS Academic Writing Test 1',
        'slug' => 'ielts-academic-writing-test-1',
        'description' => 'Bu IELTS Academic Writing testida 2 ta yozma vazifani bajarasiz.',
        'test_category_id' => $categoryIds['academic-writing'],
        'type' => 'practice',
        'duration_minutes' => 60,
        'total_questions' => 2,
        'instructions' => [
            'Task 1: Grafik, jadval yoki diagramma tahlili (150 so\'z)',
            'Task 2: Esse yozish (250 so\'z)',
            'Task 2 Task 1 dan ko\'ra ko\'proq ball beradi',
            'Vaqt chegarasi 60 daqiqa'
        ],
        'is_active' => true,
        'is_timed' => true
    ],
    [
        'title' => 'IELTS Academic Writing Test 2',
        'slug' => 'ielts-academic-writing-test-2',
        'description' => 'Bu IELTS Academic Writing testida 2 ta yozma vazifani bajarasiz.',
        'test_category_id' => $categoryIds['academic-writing'],
        'type' => 'practice',
        'duration_minutes' => 60,
        'total_questions' => 2,
        'instructions' => [
            'Task 1: Grafik, jadval yoki diagramma tahlili (150 so\'z)',
            'Task 2: Esse yozish (250 so\'z)',
            'Task 2 Task 1 dan ko\'ra ko\'proq ball beradi',
            'Vaqt chegarasi 60 daqiqa'
        ],
        'is_active' => true,
        'is_timed' => true
    ],
];

// Testlarni yaratish
foreach ($tests as $testData) {
    $test = Test::firstOrCreate(
        ['slug' => $testData['slug']],
        $testData
    );
    echo "Test yaratildi: " . $test->title . " (ID: " . $test->id . ")\n";
}

echo "Barcha test ma'lumotlari yaratildi!\n";
