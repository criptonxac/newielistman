<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TestCategory;
use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\UserTestAttempt;
use Faker\Factory as Faker;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Create test categories
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

        // Get created categories
        $listeningCategory = TestCategory::where('name', 'IELTS Listening')->first();
        $readingCategory = TestCategory::where('name', 'IELTS Reading')->first();
        $writingCategory = TestCategory::where('name', 'IELTS Writing')->first();
        $speakingCategory = TestCategory::where('name', 'IELTS Speaking')->first();

        // Create tests for each category
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
            [
                'title' => 'IELTS General Reading Practice Test',
                'slug' => 'ielts-general-reading-practice-test',
                'description' => 'General reading practice test',
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
            [
                'title' => 'IELTS Writing Task 2 Practice',
                'slug' => 'ielts-writing-task-2-practice',
                'description' => 'Essay writing practice test',
                'test_category_id' => $writingCategory->id,
                'duration_minutes' => 60,
                'total_questions' => 1
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
            [
                'title' => 'Advanced Speaking Test',
                'slug' => 'advanced-speaking-test',
                'description' => 'Advanced level speaking test',
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

            // Create sample questions for each test
            for ($i = 1; $i <= $testData['total_questions']; $i++) {
                // Check if question already exists
                $existingQuestion = TestQuestion::where('test_id', $test->id)
                    ->where('question_number', $i)
                    ->first();
                
                if (!$existingQuestion) {
                    // Create new question if it doesn't exist
                    TestQuestion::create([
                        'test_id' => $test->id,
                        'question_text' => "Sample question {$i} for {$test->title}",
                        'question_number' => $i,
                        'question_type' => 'multiple_choice',
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
        }

        // Create student users
        for ($i = 1; $i <= 20; $i++) {
            $user = User::firstOrCreate([
                'email' => "student{$i}@example.com"
            ], [
                'name' => $faker->name,
                'email' => "student{$i}@example.com",
                'password' => bcrypt('password'),
                'role' => 'student',
                'email_verified_at' => now()
            ]);

            // Create test attempts for each user
            $allTests = Test::all();
            
            foreach ($allTests as $test) {
                // Random chance of taking each test (70% chance)
                if ($faker->boolean(70)) {
                    $score = $faker->numberBetween(40, 95);
                    $completedAt = $faker->dateTimeBetween('-3 months', 'now');
                    // Completed date clone for started_at calculation
                    $completedAtClone = clone $completedAt;
                    $startedAt = $faker->dateTimeBetween($completedAtClone->modify('-30 minutes'), $completedAt);
                    
                    // Check if attempt already exists
                    $existingAttempt = UserTestAttempt::where('user_id', $user->id)
                        ->where('test_id', $test->id)
                        ->first();
                    
                    if (!$existingAttempt) {
                        // Create new attempt if it doesn't exist
                        UserTestAttempt::create([
                            'user_id' => $user->id,
                            'test_id' => $test->id,
                            'total_score' => $score,
                            'total_questions' => $test->total_questions,
                            'correct_answers' => round($score * $test->total_questions / 100),
                            'status' => 'completed',
                            'completed_at' => $completedAt,
                            'started_at' => $startedAt,
                            'created_at' => $completedAt,
                            'updated_at' => $completedAt
                        ]);
                    }
                }
            }
        }

        $this->command->info('Test data seeded successfully!');
        $this->command->info('Created:');
        $this->command->info('- 4 Test Categories (Listening, Reading, Writing, Speaking)');
        $this->command->info('- ' . Test::count() . ' Tests');
        $this->command->info('- ' . TestQuestion::count() . ' Test Questions');
        $this->command->info('- 20 Student Users');
        $this->command->info('- ' . UserTestAttempt::count() . ' Test Attempts');
    }
}
