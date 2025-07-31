<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestAnswer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ListeningTestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Start a new listening test attempt
     */
    public function start(Test $test, Request $request)
    {
        // Check if user can take this test
        if (!$test->canUserTakeTest(Auth::user())) {
            return redirect()->back()->with('error', 'Bu testni topshira olmaysiz.');
        }

        // Check if test is listening type
        if (!$test->isListeningTest()) {
            return redirect()->back()->with('error', 'Bu listening test emas.');
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
            'time_remaining_seconds' => ($test->time_limit ?? 30) * 60,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('listening.unified', ['test' => $test->slug, 'attempt' => $attempt->attempt_code]);
    }

    /**
     * Show unified listening test (all parts in one page)
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

        // Get questions grouped by parts
        $questionsByPart = $test->questions()
            ->orderBy('part_number')
            ->orderBy('question_number_in_part')
            ->get()
            ->groupBy('part_number');

        // Get existing answers
        $existingAnswers = TestAnswer::where('attempt_id', $attempt->id)
            ->get()
            ->keyBy('question_id');

        // Audio URL
        $audioUrl = $test->hasAudio() ? $test->audio_url : null;

        return view('tests.listening.unified', compact(
            'test',
            'attempt', 
            'questionsByPart',
            'existingAnswers',
            'audioUrl'
        ));
    }

    /**
     * Show specific part
     */
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

    public function part4(Test $test, $attemptCode)
    {
        return $this->showPart($test, $attemptCode, 4);
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

        // Audio URL
        $audioUrl = $test->hasAudio() ? $test->audio_url : null;

        // Part configuration
        $partConfig = $test->getPartConfig($part);

        return view("tests.listening.part{$part}", compact(
            'test',
            'attempt',
            'questions',
            'existingAnswers',
            'audioUrl',
            'partConfig',
            'part'
        ));
    }

    /**
     * Save individual answer (AJAX)
     */
    public function saveAnswer(Request $request, Test $test, $attemptCode)
    {
        $attempt = TestAttempt::where('attempt_code', $attemptCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($attempt->status === 'completed') {
            return response()->json(['error' => 'Test allaqachon yakunlangan'], 400);
        }

        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required',
            'question_type' => 'required|string'
        ]);

        $question = Question::where('id', $request->question_id)
            ->where('test_id', $test->id)
            ->firstOrFail();

        // Process answer based on question type
        $processedAnswer = $this->processAnswer($request->answer, $request->question_type);

        // Save or update answer
        $testAnswer = TestAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
            ],
            [
                'user_answer' => $processedAnswer['user_answer'],
                'answer_data' => $processedAnswer['answer_data'],
                'last_updated_at' => now(),
                'time_spent_seconds' => $request->time_spent ?? 0,
                'audio_position_when_answered' => $request->audio_position ?? null,
            ]
        );

        // If this is the first time answering
        if (!$testAnswer->first_answered_at) {
            $testAnswer->update(['first_answered_at' => now()]);
        } else {
            $testAnswer->increment('answer_changes_count');
        }

        // Update attempt progress
        $this->updateAttemptProgress($attempt);

        return response()->json([
            'success' => true,
            'message' => 'Javob saqlandi',
            'answer_id' => $testAnswer->id,
            'progress' => $this->getAttemptProgress($attempt)
        ]);
    }

    /**
     * Submit all answers and complete test
     */
    public function submitAnswers(Request $request, Test $test, $attemptCode)
    {
        $attempt = TestAttempt::where('attempt_code', $attemptCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($attempt->status === 'completed') {
            return redirect()->route('listening.complete', ['test' => $test->slug, 'attempt' => $attemptCode]);
        }

        DB::beginTransaction();

        try {
            // Save any remaining answers from the request
            if ($request->has('answers')) {
                foreach ($request->answers as $questionId => $answer) {
                    if (!empty($answer)) {
                        $question = Question::find($questionId);
                        if ($question && $question->test_id == $test->id) {
                            $processedAnswer = $this->processAnswer($answer, $question->question_format);
                            
                            TestAnswer::updateOrCreate(
                                [
                                    'attempt_id' => $attempt->id,
                                    'question_id' => $questionId,
                                ],
                                [
                                    'user_answer' => $processedAnswer['user_answer'],
                                    'answer_data' => $processedAnswer['answer_data'],
                                    'last_updated_at' => now(),
                                ]
                            );
                        }
                    }
                }
            }

            // Complete the attempt
            $attempt->update([
                'status' => 'completed',
                'completed_at' => now(),
                'time_spent_seconds' => ($test->time_limit * 60) - ($attempt->time_remaining_seconds ?? 0),
            ]);

            // Calculate score
            $this->calculateScore($attempt);

            DB::commit();

            return redirect()->route('listening.complete', ['test' => $test->slug, 'attempt' => $attemptCode]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Show completion page
     */
    public function complete(Test $test, $attemptCode)
    {
        $attempt = TestAttempt::where('attempt_code', $attemptCode)
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->with(['test', 'testAnswers.question'])
            ->firstOrFail();

        return view('tests.listening.complete', compact('test', 'attempt'));
    }

    /**
     * Get time remaining (AJAX)
     */
    public function getTimeRemaining(Request $request, Test $test, $attemptCode)
    {
        $attempt = TestAttempt::where('attempt_code', $attemptCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($attempt->status === 'completed') {
            return response()->json(['time_remaining' => 0, 'status' => 'completed']);
        }

        // Calculate time remaining
        $elapsed = now()->diffInSeconds($attempt->started_at);
        $totalTime = ($test->time_limit ?? 30) * 60;
        $timeRemaining = max(0, $totalTime - $elapsed);

        // Update attempt
        $attempt->update(['time_remaining_seconds' => $timeRemaining]);

        // Auto-complete if time is up
        if ($timeRemaining <= 0 && $attempt->status !== 'completed') {
            $this->autoCompleteAttempt($attempt);
            return response()->json(['time_remaining' => 0, 'status' => 'completed', 'auto_completed' => true]);
        }

        return response()->json([
            'time_remaining' => $timeRemaining,
            'status' => $attempt->status,
            'elapsed' => $elapsed
        ]);
    }

    /**
     * Process answer based on question type
     */
    private function processAnswer($rawAnswer, $questionType): array
    {
        $result = [
            'user_answer' => '',
            'answer_data' => null
        ];

        switch ($questionType) {
            case 'gap_filling':
            case 'note_completion':
            case 'sentence_completion':
            case 'summary_completion':
            case 'short_answer':
                // Clean text input
                $result['user_answer'] = trim(strip_tags($rawAnswer));
                break;

            case 'multiple_choice':
                $result['user_answer'] = $rawAnswer;
                break;

            case 'matching':
            case 'classification':
                // Store as JSON for complex matching
                if (is_array($rawAnswer)) {
                    $result['answer_data'] = $rawAnswer;
                    $result['user_answer'] = json_encode($rawAnswer);
                } else {
                    $result['user_answer'] = $rawAnswer;
                }
                break;

            case 'map_labeling':
                // Store map coordinates and labels
                if (is_array($rawAnswer)) {
                    $result['answer_data'] = $rawAnswer;
                    $result['user_answer'] = json_encode($rawAnswer);
                } else {
                    $result['user_answer'] = $rawAnswer;
                }
                break;

            case 'flow_chart':
            case 'table_completion':
                // Multiple inputs for one question
                if (is_array($rawAnswer)) {
                    $result['answer_data'] = $rawAnswer;
                    $result['user_answer'] = implode('; ', array_filter($rawAnswer));
                } else {
                    $result['user_answer'] = $rawAnswer;
                }
                break;

            default:
                $result['user_answer'] = $rawAnswer;
        }

        return $result;
    }

    /**
     * Calculate IELTS listening score
     */
    private function calculateScore(TestAttempt $attempt)
    {
        $testAnswers = TestAnswer::where('attempt_id', $attempt->id)
            ->with('question')
            ->get();

        $correctAnswers = 0;
        $totalQuestions = $testAnswers->count();

        foreach ($testAnswers as $answer) {
            if ($this->isAnswerCorrect($answer)) {
                $correctAnswers++;
                $answer->update([
                    'is_correct' => true,
                    'points_earned' => 1
                ]);
            } else {
                $answer->update([
                    'is_correct' => false,
                    'points_earned' => 0
                ]);
            }
        }

        // IELTS Band Score conversion
        $bandScore = $this->convertToBandScore($correctAnswers, $totalQuestions);
        $percentage = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;

        $attempt->update([
            'correct_answers' => $correctAnswers,
            'score' => $bandScore,
            'percentage' => $percentage,
            'answered_questions' => $testAnswers->count(),
            'detailed_results' => [
                'band_score' => $bandScore,
                'correct_answers' => $correctAnswers,
                'total_questions' => $totalQuestions,
                'percentage' => $percentage,
                'parts_breakdown' => $this->getPartsBreakdown($testAnswers)
            ]
        ]);
    }

    /**
     * Check if answer is correct
     */
    private function isAnswerCorrect(TestAnswer $answer): bool
    {
        $question = $answer->question;
        $userAnswer = trim(strtolower($answer->user_answer));
        
        // Get acceptable answers
        $acceptableAnswers = $question->acceptable_answers ?? [];
        
        // If no acceptable answers defined, use correct_answer
        if (empty($acceptableAnswers) && $question->correct_answer) {
            $acceptableAnswers = [trim(strtolower($question->correct_answer))];
        }

        // Check against all acceptable answers
        foreach ($acceptableAnswers as $correctAnswer) {
            $correctAnswer = trim(strtolower($correctAnswer));
            
            if ($question->case_sensitive ?? false) {
                if ($answer->user_answer === $correctAnswer) {
                    return true;
                }
            } else {
                if ($userAnswer === $correctAnswer) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Convert correct answers to IELTS band score
     */
    private function convertToBandScore(int $correctAnswers, int $totalQuestions): float
    {
        if ($totalQuestions != 40) {
            // If not standard 40 questions, use percentage
            $percentage = ($correctAnswers / $totalQuestions) * 100;
            return min(9.0, max(1.0, round($percentage / 11.1, 1)));
        }

        // Standard IELTS Listening band score conversion (40 questions)
        $bandScores = [
            39 => 9.0, 38 => 8.5, 37 => 8.5, 36 => 8.0, 35 => 8.0,
            34 => 7.5, 33 => 7.5, 32 => 7.0, 31 => 7.0, 30 => 6.5,
            29 => 6.5, 28 => 6.0, 27 => 6.0, 26 => 6.0, 25 => 5.5,
            24 => 5.5, 23 => 5.0, 22 => 5.0, 21 => 5.0, 20 => 4.5,
            19 => 4.5, 18 => 4.0, 17 => 4.0, 16 => 4.0, 15 => 3.5,
            14 => 3.5, 13 => 3.0, 12 => 3.0, 11 => 3.0, 10 => 2.5,
            9 => 2.5, 8 => 2.0, 7 => 2.0, 6 => 2.0, 5 => 1.5,
            4 => 1.5, 3 => 1.0, 2 => 1.0, 1 => 1.0, 0 => 0.0
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
        
        for ($part = 1; $part <= 4; $part++) {
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
     * Auto-complete attempt when time runs out
     */
    private function autoCompleteAttempt(TestAttempt $attempt)
    {
        $attempt->update([
            'status' => 'completed',
            'completed_at' => now(),
            'time_spent_seconds' => ($attempt->test->time_limit ?? 30) * 60,
        ]);

        $this->calculateScore($attempt);
    }

    /**
     * Generate unique attempt code
     */
    private function generateAttemptCode(): string
    {
        do {
            $code = 'LA' . strtoupper(substr(uniqid(), -8)); // LA = Listening Attempt
        } while (TestAttempt::where('attempt_code', $code)->exists());

        return $code;
    }
}