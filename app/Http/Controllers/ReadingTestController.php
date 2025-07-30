<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\UserTestAttempt;
use App\Services\QuestionRenderer;
use Illuminate\Http\Request;

class ReadingTestController extends Controller
{
    /**
     * Unified reading test view that shows all parts in one page
     */
    public function unifiedTest(Test $test, $attempt)
    {
        $attempt = UserTestAttempt::findOrFail($attempt);
        
        // Get all questions for the test
        $allQuestions = $test->questions()->orderBy('sort_order')->get();
        
        // Assign question numbers sequentially
        $questionNumber = 1;
        foreach ($allQuestions as $question) {
            $question->question_number = $questionNumber++;
        }
        
        // Divide questions into parts
        $part1Questions = $allQuestions->take(13);
        $part2Questions = $allQuestions->skip(13)->take(13);
        $part3Questions = $allQuestions->skip(26)->take(14);
        
        // Get passages for the test
        $passages = [];
        try {
            // Try to get passages from relationship if it exists
            $passages = $test->passages()->get();
            
            // If no passages found, create default ones
            if ($passages->isEmpty()) {
                // Create default passages from reading_passage field if available
                if (!empty($test->reading_passage)) {
                    $passages = collect([
                        (object)[
                            'title' => 'Reading Passage 1',
                            'content' => $test->reading_passage,
                            'part' => 1
                        ],
                        (object)[
                            'title' => 'Reading Passage 2',
                            'content' => $test->reading_passage,
                            'part' => 2
                        ],
                        (object)[
                            'title' => 'Reading Passage 3',
                            'content' => $test->reading_passage,
                            'part' => 3
                        ]
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Error loading test passages: ' . $e->getMessage());
            // Create passages with user content as fallback
            $userContent = '<p>Bu yerda IELTS Reading Test uchun matn bo\'lishi kerak. Siz o\'quvchilar uchun tayyorlangan matnni ko\'rmoqdasiz. Bu matn o\'quvchilarning o\'qish ko\'nikmalarini rivojlantirish va baholash uchun ishlatiladi.</p>
<p>IELTS Reading testida odatda ilmiy, akademik va umumiy mavzulardagi matnlar beriladi. Har bir qismda 13-14 ta savol bo\'lib, jami 40 ta savolga javob berishingiz kerak.</p>
<p>Matnni diqqat bilan o\'qing va berilgan savollarga javob bering. Vaqtingizni to\'g\'ri taqsimlang - Reading testi uchun 60 daqiqa beriladi.</p>';
            
            $passages = collect([
                (object)[
                    'title' => 'Reading Passage 1',
                    'content' => $userContent,
                    'part' => 1
                ],
                (object)[
                    'title' => 'Reading Passage 2',
                    'content' => $userContent,
                    'part' => 2
                ],
                (object)[
                    'title' => 'Reading Passage 3',
                    'content' => $userContent,
                    'part' => 3
                ]
            ]);
        }
        
        $userAnswers = $attempt->answers ?? [];
        
        // QuestionRenderer servisini ishlatish
        $questionRenderer = new QuestionRenderer();
        
        return view('tests.reading.reading-test', compact(
            'test', 
            'attempt', 
            'part1Questions', 
            'part2Questions', 
            'part3Questions', 
            'userAnswers', 
            'questionRenderer',
            'passages'
        ));
    }
    public function start(Test $test, Request $request)
    {
        // Testni boshlash
        $attempt = UserTestAttempt::create([
            'test_id' => $test->id,
            'user_id' => auth()->check() ? auth()->id() : null,
            'status' => 'in_progress',
            'answers' => [],
            'score' => 0,
            'max_score' => $test->max_score ?? 40,
            'total_questions' => $test->total_questions ?? 40,
            'started_at' => now(),
        ]);

        // Unified view-ga yo'naltirish
        return redirect()->route('reading.unified', ['test' => $test->slug, 'attempt' => $attempt->id]);
    }

    public function part1(Test $test, $attempt)
    {
        $attempt = UserTestAttempt::findOrFail($attempt);
        
        // Get questions for part 1 (first 13 questions)
        $questions = $test->questions()->orderBy('sort_order')->take(13)->get();
        $userAnswers = $attempt->answers ?? [];
        
        // QuestionRenderer servisini ishlatish
        $questionRenderer = new QuestionRenderer();
        
        return view('tests.reading.part1', compact('test', 'attempt', 'questions', 'userAnswers', 'questionRenderer'));
    }

    public function part2(Test $test, $attempt)
    {
        $attempt = UserTestAttempt::findOrFail($attempt);
        
        // Get questions for part 2 (questions 14-26)
        $questions = $test->questions()->orderBy('sort_order')->skip(13)->take(13)->get();
        $userAnswers = $attempt->answers ?? [];
        
        // QuestionRenderer servisini ishlatish
        $questionRenderer = new QuestionRenderer();
        
        return view('tests.reading.part2', compact('test', 'attempt', 'questions', 'userAnswers', 'questionRenderer'));
    }

    public function part3(Test $test, $attempt)
    {
        $attempt = UserTestAttempt::findOrFail($attempt);
        
        // Get questions for part 3 (questions 27-40)
        $questions = $test->questions()->orderBy('sort_order')->skip(26)->take(14)->get();
        $userAnswers = $attempt->answers ?? [];
        
        // QuestionRenderer servisini ishlatish
        $questionRenderer = new QuestionRenderer();
        
        return view('tests.reading.part3', compact('test', 'attempt', 'questions', 'userAnswers', 'questionRenderer'));
    }

    public function submitAnswers(Test $test, $attempt, Request $request)
    {
        $attempt = UserTestAttempt::findOrFail($attempt);
        
        // Faqat POST metodi orqali kelgan so'rovlardagina javoblarni saqlash
        if ($request->isMethod('post')) {
            // Javoblarni saqlash
            $currentAnswers = $attempt->answers ?: [];
            $newAnswers = $request->input('answers', []);
            
            $answers = array_merge($currentAnswers, $newAnswers);
            $attempt->answers = $answers;
            $attempt->save();
            
            // Keyingi qismga yo'naltirish yoki testni yakunlash
            $nextRoute = $request->input('next_route');
            if ($nextRoute) {
                return redirect()->route($nextRoute, ['test' => $test->slug, 'attempt' => $attempt->id]);
            }
            
            return redirect()->route('reading.complete', ['test' => $test->slug, 'attempt' => $attempt->id]);
        }
        
        // GET metodi orqali kelgan so'rovlarda oldingi sahifaga qaytarish
        return redirect()->route('reading.part3', ['test' => $test->slug, 'attempt' => $attempt->id]);
    }

    public function complete(Test $test, $attempt)
    {
        $attempt = UserTestAttempt::findOrFail($attempt);
        
        // Testni yakunlash
        $attempt->status = 'completed';
        $attempt->completed_at = now();
        $attempt->save();
        
        return view('tests.reading.complete', compact('test', 'attempt'));
    }
}
