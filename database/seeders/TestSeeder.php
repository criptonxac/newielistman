<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tests = [
            // Listening Tests
            [
                'title' => 'IELTS Listening Familiarisation Test',
                'slug' => 'ielts-listening-familiarisation',
                'description' => 'Bu IELTS Familiarisation testida 4 ta yozuvni tinglaysiz va ular asosida 40 ta savolga javob berasiz.',
                'test_category_id' => 1, // Listening
                'type' => 'familiarisation',
                'duration_minutes' => null,
                'total_questions' => 40,
                'instructions' => [
                    'Test 4 ta qismdan iborat',
                    'Har bir yozuv faqat bir marta ijro etiladi',
                    'Savollarni oldindan o\'qish uchun vaqt beriladi',
                    'Javoblarni test davomida yozib borishingiz mumkin'
                ],
                'is_active' => true,
                'is_timed' => false
            ],
            // Academic Reading Tests
            [
                'title' => 'IELTS Academic Reading Familiarisation Test',
                'slug' => 'ielts-academic-reading-familiarisation',
                'description' => 'Bu IELTS Familiarisation testida 3 ta uzun matnni o\'qib, jami 40 ta savolga javob berasiz.',
                'test_category_id' => 2, // Academic Reading
                'type' => 'familiarisation',
                'duration_minutes' => null,
                'total_questions' => 40,
                'instructions' => [
                    'Test 3 ta qismdan iborat',
                    'Har bir qismda bir yoki bir nechta matn',
                    'Savollar turli xil: multiple choice, true/false, matching',
                    'Vaqt chegarasi yo\'q - o\'z vaqtingizda ishlang'
                ],
                'is_active' => true,
                'is_timed' => false
            ],
            // Academic Writing Tests
            [
                'title' => 'IELTS Academic Writing Familiarisation Test',
                'slug' => 'ielts-academic-writing-familiarisation',
                'description' => 'Bu IELTS Familiarisation testida 2 ta yozma vazifani bajarasiz.',
                'test_category_id' => 3, // Academic Writing
                'type' => 'familiarisation',
                'duration_minutes' => null,
                'total_questions' => 2,
                'instructions' => [
                    'Task 1: Grafik, jadval yoki diagramma tahlili (150 so\'z)',
                    'Task 2: Esse yozish (250 so\'z)',
                    'Task 2 Task 1 dan ko\'ra ko\'proq ball beradi',
                    'Vaqt chegarasi yo\'q - o\'z vaqtingizda yozing'
                ],
                'is_active' => true,
                'is_timed' => false
            ],
            // Sample Tests
            [
                'title' => 'Academic Writing Sample Task 1',
                'slug' => 'academic-writing-sample-task-1',
                'description' => 'Task 1 da sizdan jarayon bosqichlarini, obyekt yoki hodisani tasvirlab berish yoki biror narsa qanday ishlashini tushuntirish so\'ralishi mumkin.',
                'test_category_id' => 3, // Academic Writing
                'type' => 'sample',
                'duration_minutes' => null,
                'total_questions' => 1,
                'instructions' => [
                    'Minimal 150 so\'z yozing',
                    'Grafik ma\'lumotlarini tahlil qiling',
                    'Asosiy tendentsiyalarni ta\'riflang',
                    'Raqamlarni solishtiring'
                ],
                'is_active' => true,
                'is_timed' => false
            ],
            [
                'title' => 'Academic Writing Sample Task 2', 
                'slug' => 'academic-writing-sample-task-2',
                'description' => 'Task 2 da sizdan nuqtai nazar yoki argumentga javob sifatida esse yozish so\'raladi.',
                'test_category_id' => 3, // Academic Writing
                'type' => 'sample',
                'duration_minutes' => null,
                'total_questions' => 1,
                'instructions' => [
                    'Minimal 250 so\'z yozing',
                    'O\'z fikriyzni mantiqiy asoslang',
                    'Misollar keltiring',
                    'Xulosa yozing'
                ],
                'is_active' => true,
                'is_timed' => false
            ],
            // General Training Reading Test
            [
                'title' => 'IELTS General Training Reading Test',
                'slug' => 'ielts-general-training-reading',
                'description' => 'Bu IELTS General Training Reading testida 3 ta qismdan iborat matnlarni o\'qib, savolarga javob berasiz.',
                'test_category_id' => 2, // Reading
                'type' => 'practice',
                'duration_minutes' => 60,
                'total_questions' => 40,
                'instructions' => [
                    'Test 3 ta qismdan iborat',
                    'Har bir qismda turli xil matnlar',
                    'Vaqt chegarasi: 60 daqiqa',
                    'Barcha javoblarni javoblar varaqasiga ko\'chiring'
                ],
                'is_active' => true,
                'is_timed' => true
            ],
        ];

        foreach ($tests as $test) {
            \App\Models\Test::create($test);
        }
    }
}
