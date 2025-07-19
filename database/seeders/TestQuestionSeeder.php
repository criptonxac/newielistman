<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Listening Test Questions (Test ID: 1)
        $listeningQuestions = [
            [
                'test_id' => 1,
                'question_number' => 1,
                'question_type' => 'fill_blank',
                'question_text' => 'The speaker mentions that the library opens at _______ on weekdays.',
                'correct_answer' => '8:30',
                'acceptable_answers' => ['8:30', 'eight thirty', 'half past eight'],
                'points' => 1,
                'explanation' => 'The speaker clearly states that the library opening time is 8:30 AM on weekdays.',
                'sort_order' => 1
            ],
            [
                'test_id' => 1,
                'question_number' => 2,
                'question_type' => 'multiple_choice',
                'question_text' => 'What is the main topic of the conversation?',
                'options' => [
                    'Library services',
                    'University enrollment',
                    'Course selection',
                    'Accommodation booking'
                ],
                'correct_answer' => 'Library services',
                'points' => 1,
                'explanation' => 'The conversation focuses on library services and facilities.',
                'sort_order' => 2
            ]
        ];

        // Academic Reading Test Questions (Test ID: 2)
        $readingQuestions = [
            [
                'test_id' => 2,
                'question_number' => 1,
                'question_type' => 'true_false',
                'question_text' => 'According to the passage, climate change affects all regions equally.',
                'correct_answer' => 'False',
                'points' => 1,
                'explanation' => 'The passage indicates that climate change affects different regions differently.',
                'sort_order' => 1
            ],
            [
                'test_id' => 2,
                'question_number' => 2,
                'question_type' => 'multiple_choice',
                'question_text' => 'The main cause of deforestation mentioned in the text is:',
                'options' => [
                    'Natural disasters',
                    'Urban development',
                    'Agricultural expansion',
                    'Industrial pollution'
                ],
                'correct_answer' => 'Agricultural expansion',
                'points' => 1,
                'explanation' => 'The text specifically mentions agricultural expansion as the primary driver of deforestation.',
                'sort_order' => 2
            ]
        ];

        // Academic Writing Test Questions (Test ID: 3)
        $writingQuestions = [
            [
                'test_id' => 3,
                'question_number' => 1,
                'question_type' => 'essay',
                'question_text' => 'The chart below shows the percentage of households in owned and rented accommodation in England and Wales between 1918 and 2011. Summarise the information by selecting and reporting the main features, and make comparisons where relevant.',
                'points' => 33,
                'explanation' => 'This is Task 1. You should write at least 150 words describing the main trends shown in the chart.',
                'sort_order' => 1
            ],
            [
                'test_id' => 3,
                'question_number' => 2,
                'question_type' => 'essay',
                'question_text' => 'Some people think that universities should provide graduates with the knowledge and skills needed in the workplace. Others think that the true function of a university should be to give access to knowledge for its own sake, regardless of whether the course is useful to an employer. What, in your opinion, should be the main function of a university?',
                'points' => 67,
                'explanation' => 'This is Task 2. You should write at least 250 words presenting a clear position on this issue.',
                'sort_order' => 2
            ]
        ];

        // Sample Test Questions
        $sampleQuestions = [
            // Academic Writing Sample Task 1 (Test ID: 4)
            [
                'test_id' => 4,
                'question_number' => 1,
                'question_type' => 'essay',
                'question_text' => 'The diagrams below show the design for a wind turbine and its location. Summarise the information by selecting and reporting the main features, and make comparisons where relevant.',
                'points' => 100,
                'explanation' => 'Describe the wind turbine design and explain the optimal location factors.',
                'sort_order' => 1
            ],
            // Academic Writing Sample Task 2 (Test ID: 5)
            [
                'test_id' => 5,
                'question_number' => 1,
                'question_type' => 'essay',
                'question_text' => 'In some countries, many more people are choosing to live alone nowadays than in the past. Do you think this is a positive or negative development?',
                'points' => 100,
                'explanation' => 'Present your opinion with clear arguments and examples.',
                'sort_order' => 1
            ]
        ];

        // General Training Reading Questions (Test ID: 6)
        $gtReadingQuestions = [
            [
                'test_id' => 6,
                'question_number' => 1,
                'question_type' => 'fill_blank',
                'question_text' => 'Complete the sentence: The new community center will be open from _______ to _______.',
                'correct_answer' => '9 AM to 9 PM',
                'acceptable_answers' => ['9 AM to 9 PM', '9:00 to 21:00', 'nine to nine'],
                'points' => 1,
                'explanation' => 'The operating hours are clearly stated in the notice.',
                'sort_order' => 1
            ],
            [
                'test_id' => 6,
                'question_number' => 2,
                'question_type' => 'multiple_choice',
                'question_text' => 'What is the main purpose of the community center?',
                'options' => [
                    'Sports activities only',
                    'Educational programs only', 
                    'Mixed community activities',
                    'Senior citizen programs only'
                ],
                'correct_answer' => 'Mixed community activities',
                'points' => 1,
                'explanation' => 'The text mentions various types of community programs.',
                'sort_order' => 2
            ]
        ];

        // Insert all questions
        $allQuestions = array_merge(
            $listeningQuestions,
            $readingQuestions, 
            $writingQuestions,
            $sampleQuestions,
            $gtReadingQuestions
        );

        foreach ($allQuestions as $question) {
            \App\Models\TestQuestion::create($question);
        }
    }
}
