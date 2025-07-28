<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TestCategory;
use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\UserTestAttempt;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class FakeTestDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Avval mavjud user'larning role'larini to'g'irlaymiz
        DB::table('users')->where('email', 'admin@ielts-platform.com')->update(['role' => 'admin']);
        DB::table('users')->where('name', 'IELTS Admin')->update(['role' => 'admin']);
        DB::table('users')->where('name', 'Demo Teacher')->update(['role' => 'teacher']);
        DB::table('users')->where('name', 'Demo Student')->update(['role' => 'student']);
        DB::table('users')->where('name', 'Jaska')->update(['role' => 'student']);
        
        // Boshqa barcha user'larni student qilamiz
        DB::table('users')
            ->whereNotIn('name', ['IELTS Admin', 'Demo Teacher'])
            ->where(function($query) {
                $query->whereNull('role')->orWhere('role', '');
            })
            ->update(['role' => 'student']);

        // Test kategoriyalarini yaratamiz
        $categories = [
            ['name' => 'IELTS Listening', 'slug' => 'ielts-listening', 'description' => 'IELTS Listening skill tests'],
            ['name' => 'IELTS Reading', 'slug' => 'ielts-reading', 'description' => 'IELTS Reading skill tests'],
            ['name' => 'IELTS Writing', 'slug' => 'ielts-writing', 'description' => 'IELTS Writing skill tests'],
            ['name' => 'IELTS Speaking', 'slug' => 'ielts-speaking', 'description' => 'IELTS Speaking skill tests'],
        ];

        foreach ($categories as $categoryData) {
            TestCategory::firstOrCreate(
                ['name' => $categoryData['name']],
                $categoryData
            );
        }

        // Kategoriyalarni olamiz
        $listeningCategory = TestCategory::where('name', 'IELTS Listening')->first();
        $readingCategory = TestCategory::where('name', 'IELTS Reading')->first();
        $writingCategory = TestCategory::where('name', 'IELTS Writing')->first();
        $speakingCategory = TestCategory::where('name', 'IELTS Speaking')->first();

        // Test'larni yaratamiz
        $tests = [
            // Listening Tests
            [
                'title' => 'IELTS Academic Listening Familiarisation Test',
                'slug' => 'ielts-academic-listening-familiarisation-test',
                'description' => 'Practice listening test for IELTS Academic',
                'test_category_id' => $listeningCategory->id,
                'duration_minutes' => 30,
                'total_questions' => 40
            ],
            [
                'title' => 'IELTS Listening Familiarisation Test',
                'slug' => 'ielts-listening-familiarisation-test',
                'description' => 'General listening practice test',
                'test_category_id' => $listeningCategory->id,
                'duration_minutes' => 30,
                'total_questions' => 40
            ],
            [
                'title' => 'Advanced Listening Practice Test 1',
                'slug' => 'advanced-listening-practice-test-1',
                'description' => 'Advanced level listening test',
                'test_category_id' => $listeningCategory->id,
                'duration_minutes' => 35,
                'total_questions' => 40
            ],

            // Reading Tests
            [
                'title' => 'IELTS Academic Reading Familiarisation Test',
                'slug' => 'ielts-academic-reading-familiarisation-test',
                'description' => 'Practice reading test for IELTS Academic',
                'test_category_id' => $readingCategory->id,
                'duration_minutes' => 60,
                'total_questions' => 40
            ],
            [
                'title' => 'Academic Reading Sample Task 1',
                'slug' => 'academic-reading-sample-task-1',
                'description' => 'Sample academic reading task',
                'test_category_id' => $readingCategory->id,
                'duration_minutes' => 60,
                'total_questions' => 40
            ],

            // Writing Tests
            [
                'title' => 'IELTS Academic Writing Familiarisation Test',
                'slug' => 'ielts-academic-writing-familiarisation-test',
                'description' => 'Practice writing test for IELTS Academic',
                'test_category_id' => $writingCategory->id,
                'duration_minutes' => 60,
                'total_questions' => 2
            ],
            [
                'title' => 'Academic Writing Sample Task 1',
                'slug' => 'academic-writing-sample-task-1',
                'description' => 'Sample academic writing task',
                'test_category_id' => $writingCategory->id,
                'duration_minutes' => 60,
                'total_questions' => 2
            ],

            // Speaking Tests
            [
                'title' => 'IELTS Speaking Practice Test 1',
                'slug' => 'ielts-speaking-practice-test-1',
                'description' => 'Speaking skill practice test',
                'test_category_id' => $speakingCategory->id,
                'duration_minutes' => 15,
                'total_questions' => 3
            ],
        ];

        foreach ($tests as $testData) {
            $test = Test::firstOrCreate(
                ['title' => $testData['title']],
                $testData
            );

            // Har bir test uchun savollar yaratamiz
            for ($i = 1; $i <= $testData['total_questions']; $i++) {
                TestQuestion::firstOrCreate([
                    'test_id' => $test->id,
                    'question_text' => "Sample question {$i} for {$test->title}",
                ], [
                    'test_id' => $test->id,
                    'question_text' => "Sample question {$i} for {$test->title}",
                    'question_type' => 'multiple_choice',
                    'question_number' => $i,
                    'options' => json_encode([
                        'A' => 'Option A',
                        'B' => 'Option B', 
                        'C' => 'Option C',
                        'D' => 'Option D'
                    ]),
                    'correct_answer' => $faker->randomElement(['A', 'B', 'C', 'D']),
                    'sort_order' => $i
                ]);
            }
        }

        // Yangi student user'lar yaratamiz
        for ($i = 1; $i <= 15; $i++) {
            $user = User::firstOrCreate([
                'email' => "student{$i}@example.com"
            ], [
                'name' => $faker->name,
                'email' => "student{$i}@example.com",
                'password' => bcrypt('password'),
                'role' => 'student',
                'email_verified_at' => now()
            ]);

            // Har bir user uchun test urinishlarini yaratamiz
            $allTests = Test::all();
            
            foreach ($allTests as $test) {
                // Har bir test uchun 70% ehtimol bilan urinish yaratamiz
                if ($faker->boolean(70)) {
                    $score = $faker->numberBetween(40, 95);
                    $completedAt = $faker->dateTimeBetween('-2 months', 'now');
                    
                    UserTestAttempt::firstOrCreate([
                        'user_id' => $user->id,
                        'test_id' => $test->id,
                    ], [
                        'user_id' => $user->id,
                        'test_id' => $test->id,
                        'total_score' => $score,
                        'total_questions' => $test->total_questions,
                        'status' => 'completed',
                        'completed_at' => $completedAt,
                        'created_at' => $completedAt,
                        'updated_at' => $completedAt
                    ]);
                }
            }
        }

        // Mavjud user'lar uchun ham test urinishlarini yaratamiz
        $existingStudents = User::where('role', 'student')->get();
        $allTests = Test::all();

        foreach ($existingStudents as $student) {
            // Agar bu user'ning test urinishlari yo'q bo'lsa
            if ($student->testAttempts()->count() == 0) {
                foreach ($allTests as $test) {
                    // 60% ehtimol bilan test urinishi yaratamiz
                    if ($faker->boolean(60)) {
                        $score = $faker->numberBetween(35, 90);
                        $completedAt = $faker->dateTimeBetween('-1 month', 'now');
                        
                        UserTestAttempt::create([
                            'user_id' => $student->id,
                            'test_id' => $test->id,
                            'total_score' => $score,
                            'total_questions' => $test->total_questions,
                            'status' => 'completed',
                            'completed_at' => $completedAt,
                            'created_at' => $completedAt,
                            'updated_at' => $completedAt
                        ]);
                    }
                }
            }
        }

        $this->command->info('Fake test data created successfully!');
        $this->command->info('Categories: ' . TestCategory::count());
        $this->command->info('Tests: ' . Test::count());
        $this->command->info('Questions: ' . TestQuestion::count());
        $this->command->info('Students: ' . User::where('role', 'student')->count());
        $this->command->info('Test Attempts: ' . UserTestAttempt::count());
    }
}
