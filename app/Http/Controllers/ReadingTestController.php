<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestAnswer;
use App\Models\Question;
use App\Services\QuestionRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReadingTestController extends Controller
{
    /**
     * Unified reading test view that shows all parts in one page
     */
    public function unifiedTest(Test $test, $attemptCode)
    {
        $attempt = TestAttempt::where('attempt_code', $attemptCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        // Update attempt status
        if ($attempt->status === 'started') {
            $attempt->update(['status' => 'in_progress']);
        }
        
        // Get all questions for the test grouped by parts
        $questionsByPart = $test->questions()
            ->orderBy('part_number')
            ->orderBy('question_number_in_part')
            ->get()
            ->groupBy('part_number');
        
        // Get existing answers
        $existingAnswers = TestAnswer::where('attempt_id', $attempt->id)
            ->get()
            ->keyBy('question_id');
        
        // Get passages for the test
        $passages = [];
        try {
            // Try to get passages from relationship if it exists
            $passages = $test->passages()->get();
            
            // If no passages found, create default ones
            if ($passages->isEmpty()) {
                // Create default passages from passage field if available
                if (!empty($test->passage)) {
                    $passages = collect([
                        (object)[
                            'title' => 'Reading Passage 1',
                            'content' => $test->passage,
                            'part' => 1
                        ],
                        (object)[
                            'title' => 'Reading Passage 2', 
                            'content' => $test->passage,
                            'part' => 2
                        ],
                        (object)[
                            'title' => 'Reading Passage 3',
                            'content' => $test->passage,
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
        
        return view('tests.reading.reading-test', compact(
            'test', 
            'attempt', 
            'questionsByPart',
            'existingAnswers',
            'passages'
        ));
    }
    public function start(Test $test, Request $request)
    {
        // Check if user can take this test
        if (!$test->canUserAttempt(Auth::user()->id)) {
            return redirect()->back()->with('error', 'Bu testni topshira olmaysiz.');
        }

        // Check if test is reading type
        if ($test->type !== 'reading') {
            return redirect()->back()->with('error', 'Bu reading test emas.');
        }

        // Create new attempt
        $attempt = TestAttempt::create([
            'user_id' => Auth::id(),
            'test_id' => $test->id,
            'attempt_code' => $this->generateAttemptCode(),
            'status' => 'started',
            'started_at' => now(),
            'current_part' => 1,
            'current_question' => 1,
            'total_questions' => $test->questions()->count(),
            'time_remaining_seconds' => ($test->time_limit ?? 60) * 60,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('reading.unified', ['test' => $test->slug, 'attempt' => $attempt->attempt_code]);
    }

    public function part1(Test $test, $attemptCode)
    {
        return $this->showPart($test, $attemptCode, 1);
    }

    public function part2(Test $test, $attemptCode)
    {
        return $this->showPart($test, $attemptCode, 2);
    }

    public function part3(Test $test, $attemptCode)
    {
        return $this->showPart($test, $attemptCode, 3);
    }
    
    /**
     * Show a specific part
     */
    private function showPart(Test $test, $attemptCode, int $part)
    {
        $attempt = TestAttempt::where('attempt_code', $attemptCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Update current part
        $attempt->update(['current_part' => $part]);

        // Get questions for this part
        $questions = $test->questions()
            ->where('part_number', $part)
            ->orderBy('question_number_in_part')
            ->get();

        // Get existing answers for this part
        $existingAnswers = TestAnswer::where('attempt_id', $attempt->id)
            ->whereIn('question_id', $questions->pluck('id'))
            ->get()
            ->keyBy('question_id');

        // Get passage for this part
        $passage = null;
        try {
            $passage = $test->passages()->where('part', $part)->first();
        } catch (\Exception $e) {
            // Fallback content if passage not found
            $userContent = '<p>Bu yerda IELTS Reading Test uchun matn bo\'lishi kerak. Siz o\'quvchilar uchun tayyorlangan matnni ko\'rmoqdasiz.</p>';
            $passage = (object)[
                'title' => 'Reading Passage ' . $part,
                'content' => $userContent,
                'part' => $part
            ];
        }

        return view('tests.reading.part' . $part, compact(
            'test', 
            'attempt', 
            'questions', 
            'existingAnswers', 
            'passage'
        ));
    }
    
    public function submitAnswers(Request $request, Test $test, $attemptCode)
    {
        $attempt = TestAttempt::where('attempt_code', $attemptCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        // Only save answers for POST requests
        if ($request->isMethod('post')) {
            // Process and save each answer
            $answers = $request->input('answers', []);
            
            foreach ($answers as $questionId => $rawAnswer) {
                $question = Question::find($questionId);
                if (!$question) continue;
                
                // Process answer based on question type
                $processedAnswer = $this->processAnswer($rawAnswer, $question->question_format);
                
                // Save or update answer
                TestAnswer::updateOrCreate(
                    ['attempt_id' => $attempt->id, 'question_id' => $questionId],
                    [
                        'raw_answer' => is_array($rawAnswer) ? json_encode($rawAnswer) : $rawAnswer,
                        'processed_answer' => is_array($processedAnswer) ? json_encode($processedAnswer) : $processedAnswer,
                        'is_correct' => $this->isAnswerCorrect($question, $processedAnswer),
                        'answered_at' => now(),
                    ]
                );
            }
            
            // Update attempt progress
            $this->updateAttemptProgress($attempt);
            
            // Check if this is the final submission
            if ($request->has('complete')) {
                return $this->completeAttempt($test, $attempt);
            }
            
            // Redirect to next part or stay on current page
            $nextRoute = $request->input('next_route');
            if ($nextRoute) {
                return redirect()->route($nextRoute, ['test' => $test->slug, 'attempt' => $attemptCode]);
            }
            
            return redirect()->route('reading.unified', ['test' => $test->slug, 'attempt' => $attemptCode]);
        }
        
        // For GET requests, redirect back to the test
        return redirect()->route('reading.unified', ['test' => $test->slug, 'attempt' => $attemptCode]);
    }

    public function complete(Test $test, $attemptCode)
    {
        $attempt = TestAttempt::where('attempt_code', $attemptCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        // Complete the test if not already completed
        if ($attempt->status !== 'completed') {
            $this->completeAttempt($test, $attempt);
        }
        
        // Get test results
        $testAnswers = TestAnswer::where('attempt_id', $attempt->id)->get();
        $correctAnswers = $testAnswers->where('is_correct', true)->count();
        $totalQuestions = $attempt->total_questions;
        $score = $this->convertToBandScore($correctAnswers, $totalQuestions);
        $partsBreakdown = $this->getPartsBreakdown($testAnswers);
        
        return view('tests.reading.complete', compact(
            'test', 
            'attempt', 
            'correctAnswers', 
            'totalQuestions', 
            'score', 
            'partsBreakdown'
        ));
    }
    
    /**
     * Save a single answer via AJAX
     */
    public function saveAnswer(Request $request, Test $test, $attemptCode)
    {
        $attempt = TestAttempt::where('attempt_code', $attemptCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();
            
        // Validate request
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'nullable',
        ]);
        
        $questionId = $request->input('question_id');
        $rawAnswer = $request->input('answer');
        
        // Find question
        $question = Question::find($questionId);
        if (!$question) {
            return response()->json(['error' => 'Question not found'], 404);
        }
        
        // Process answer based on question type
        $processedAnswer = $this->processAnswer($rawAnswer, $question->question_format);
        
        // Save or update answer
        $answer = TestAnswer::updateOrCreate(
            ['attempt_id' => $attempt->id, 'question_id' => $questionId],
            [
                'raw_answer' => is_array($rawAnswer) ? json_encode($rawAnswer) : $rawAnswer,
                'processed_answer' => is_array($processedAnswer) ? json_encode($processedAnswer) : $processedAnswer,
                'is_correct' => $this->isAnswerCorrect($question, $processedAnswer),
                'answered_at' => now(),
            ]
        );
        
        // Update attempt progress
        $this->updateAttemptProgress($attempt);
        
        // Get progress data for frontend
        $progress = $this->getAttemptProgress($attempt);
        
        return response()->json([
            'success' => true,
            'answer_id' => $answer->id,
            'progress' => $progress,
        ]);
    }
    
    /**
     * Get time remaining for the test attempt
     */
    public function getTimeRemaining(Test $test, $attemptCode)
    {
        $attempt = TestAttempt::where('attempt_code', $attemptCode)
            ->where('user_id', auth()->id())
            ->where('test_id', $test->id)
            ->firstOrFail();
            
        // If test is already completed, return 0
        if ($attempt->status === 'completed') {
            return response()->json([
                'time_remaining' => 0,
                'status' => 'completed'
            ]);
        }
        
        // Calculate time remaining
        $startedAt = Carbon::parse($attempt->started_at);
        $timeLimit = $test->time_limit ?? 60; // Default 60 minutes for reading test
        $timeSpentSeconds = now()->diffInSeconds($startedAt);
        $timeRemainingSeconds = max(0, ($timeLimit * 60) - $timeSpentSeconds);
        
        // Update time remaining in the attempt
        $attempt->update([
            'time_remaining_seconds' => $timeRemainingSeconds,
            'time_spent_seconds' => $timeSpentSeconds
        ]);
        
        // Auto-complete if time is up
        if ($timeRemainingSeconds <= 0 && $attempt->status !== 'completed') {
            $this->autoCompleteAttempt($attempt);
            return response()->json([
                'time_remaining' => 0,
                'status' => 'completed',
                'message' => 'Test time expired'
            ]);
        }
        
        return response()->json([
            'time_remaining' => $timeRemainingSeconds,
            'formatted_time' => gmdate('i:s', $timeRemainingSeconds),
            'status' => $attempt->status,
            'progress' => $this->getAttemptProgress($attempt)
        ]);
    }
    
    /**
     * Process answer based on question type
     */
    private function processAnswer($rawAnswer, $questionType)
    {
        if (empty($rawAnswer)) {
            return '';
        }
        
        switch ($questionType) {
            case 'multiple_choice':
                // Return selected option
                return is_array($rawAnswer) ? implode(',', $rawAnswer) : $rawAnswer;
                
            case 'gap_filling':
            case 'short_answer':
                // Normalize text answers
                if (is_array($rawAnswer)) {
                    return array_map(function($item) {
                        return $this->normalizeAnswer($item);
                    }, $rawAnswer);
                }
                return $this->normalizeAnswer($rawAnswer);
                
            default:
                // Default processing
                return is_array($rawAnswer) ? json_encode($rawAnswer) : $rawAnswer;
        }
    }
    
    /**
     * Normalize text answer for comparison
     */
    private function normalizeAnswer($text)
    {
        if (empty($text)) return '';
        
        // Convert to lowercase
        $text = mb_strtolower($text);
        
        // Remove extra spaces
        $text = preg_replace('/\s+/', ' ', trim($text));
        
        // Remove punctuation
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);
        
        return $text;
    }
    
    /**
     * Check if answer is correct
     */
    private function isAnswerCorrect($question, $processedAnswer)
    {
        $acceptableAnswers = $question->acceptable_answers ?? [];
        
        if (empty($acceptableAnswers)) {
            return false;
        }
        
        // For array answers (like matching questions)
        if (is_array($processedAnswer)) {
            // Each element must match one of the acceptable answers
            foreach ($processedAnswer as $answer) {
                $found = false;
                foreach ($acceptableAnswers as $acceptable) {
                    if ($this->answersMatch($answer, $acceptable)) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) return false;
            }
            return true;
        }
        
        // For string answers
        foreach ($acceptableAnswers as $acceptable) {
            if ($this->answersMatch($processedAnswer, $acceptable)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Compare two answers for equality
     */
    private function answersMatch($answer, $acceptable)
    {
        // Normalize acceptable answer
        $acceptable = $this->normalizeAnswer($acceptable);
        
        // Direct match
        if ($answer === $acceptable) {
            return true;
        }
        
        // Check if answer is within acceptable answers (for multiple choice)
        if (strpos($acceptable, ',') !== false) {
            $options = explode(',', $acceptable);
            return in_array($answer, array_map('trim', $options));
        }
        
        return false;
    }
    
    /**
     * Convert correct answers to IELTS band score
     */
    private function convertToBandScore(int $correctAnswers, int $totalQuestions)
    {
        if ($totalQuestions != 40) {
            // If not standard 40 questions, use percentage
            $percentage = ($correctAnswers / $totalQuestions) * 100;
            return min(9.0, max(1.0, round($percentage / 11.1, 1)));
        }
        
        // Standard IELTS Reading band score conversion (40 questions)
        $bandScores = [
            39 => 9.0, 38 => 9.0, 37 => 8.5, 36 => 8.5, 35 => 8.0,
            34 => 8.0, 33 => 7.5, 32 => 7.5, 31 => 7.0, 30 => 7.0,
            29 => 6.5, 28 => 6.5, 27 => 6.0, 26 => 6.0, 25 => 5.5,
            24 => 5.5, 23 => 5.0, 22 => 5.0, 21 => 4.5, 20 => 4.5,
            19 => 4.0, 18 => 4.0, 17 => 3.5, 16 => 3.5, 15 => 3.0,
            14 => 3.0, 13 => 2.5, 12 => 2.5, 11 => 2.0, 10 => 2.0,
            9 => 1.5, 8 => 1.5, 7 => 1.0, 6 => 1.0, 5 => 1.0,
            4 => 1.0, 3 => 1.0, 2 => 1.0, 1 => 1.0, 0 => 0.0
        ];
        
        return $bandScores[$correctAnswers] ?? 1.0;
    }
    
    /**
     * Get parts breakdown for detailed results
     */
    private function getPartsBreakdown($testAnswers)
    {
        $breakdown = [];
        
        foreach ($testAnswers->groupBy('question.part_number') as $part => $answers) {
            $correct = $answers->where('is_correct', true)->count();
            $total = $answers->count();
            
            $breakdown["part_{$part}"] = [
                'correct' => $correct,
                'total' => $total,
                'percentage' => $total > 0 ? round(($correct / $total) * 100, 1) : 0
            ];
        }
        
        return $breakdown;
    }
    
    /**
     * Update attempt progress
     */
    private function updateAttemptProgress(TestAttempt $attempt)
    {
        $answeredQuestions = TestAnswer::where('attempt_id', $attempt->id)->count();
        $totalQuestions = $attempt->total_questions;

        $attempt->update([
            'answered_questions' => $answeredQuestions,
            'parts_progress' => $this->calculatePartsProgress($attempt)
        ]);
    }

    /**
     * Calculate progress for each part
     */
    private function calculatePartsProgress(TestAttempt $attempt)
    {
        $progress = [];
        
        for ($part = 1; $part <= 3; $part++) { // Reading has 3 parts
            $partQuestions = Question::where('test_id', $attempt->test_id)
                ->where('part_number', $part)
                ->pluck('id');
                
            $answeredInPart = TestAnswer::where('attempt_id', $attempt->id)
                ->whereIn('question_id', $partQuestions)
                ->count();
                
            $progress["part_{$part}"] = [
                'answered' => $answeredInPart,
                'total' => $partQuestions->count(),
                'percentage' => $partQuestions->count() > 0 ? 
                    round(($answeredInPart / $partQuestions->count()) * 100, 1) : 0
            ];
        }
        
        return $progress;
    }

    /**
     * Get attempt progress
     */
    private function getAttemptProgress(TestAttempt $attempt)
    {
        return [
            'answered_questions' => $attempt->answered_questions,
            'total_questions' => $attempt->total_questions,
            'percentage' => $attempt->total_questions > 0 ? 
                round(($attempt->answered_questions / $attempt->total_questions) * 100, 1) : 0,
            'parts_progress' => $attempt->parts_progress
        ];
    }

    /**
     * Complete the attempt and calculate score
     */
    private function completeAttempt(Test $test, TestAttempt $attempt)
    {
        // Mark attempt as completed
        $attempt->update([
            'status' => 'completed',
            'completed_at' => now(),
            'time_spent_seconds' => ($test->time_limit ?? 60) * 60 - ($attempt->time_remaining_seconds ?? 0),
        ]);
        
        // Calculate score
        $testAnswers = TestAnswer::where('attempt_id', $attempt->id)->get();
        $correctAnswers = $testAnswers->where('is_correct', true)->count();
        $score = $this->convertToBandScore($correctAnswers, $attempt->total_questions);
        
        // Update score
        $attempt->update([
            'score' => $score,
            'correct_answers' => $correctAnswers,
        ]);
        
        return $attempt;
    }

    /**
     * Auto-complete attempt when time runs out
     */
    private function autoCompleteAttempt(TestAttempt $attempt)
    {
        $test = Test::find($attempt->test_id);
        
        $attempt->update([
            'status' => 'completed',
            'completed_at' => now(),
            'time_spent_seconds' => ($test->time_limit ?? 60) * 60,
        ]);

        return $this->completeAttempt($test, $attempt);
    }

    /**
     * Generate unique attempt code
     */
    private function generateAttemptCode(): string
    {
        do {
            $code = 'RA' . strtoupper(substr(uniqid(), -8)); // RA = Reading Attempt
        } while (TestAttempt::where('attempt_code', $code)->exists());

        return $code;
    }
}
