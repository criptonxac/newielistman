<?php

namespace App\Http\Controllers;

use App\Enums\TestType;
use App\Models\Test;
use App\Models\TestCategory;
use App\Models\TestQuestion;
use App\Models\UserTestAttempt;
use App\Models\UserAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TestController extends Controller
{
    public function show(Test $test, Request $request)
    {
        $test->load(['category', 'questions']);
        
        return view('tests.show', compact('test'));
    }

    public function start(Test $test, Request $request)
    {
        $sessionId = session()->getId();
        
        // Agar user login qilgan bo'lsa, user_id dan foydalanish
        $userId = auth()->id();
        
        // Avval mavjud attempt borligini tekshirish
        $existingAttempt = UserTestAttempt::where('test_id', $test->id)
            ->where(function ($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            return redirect()->route('tests.take', ['test' => $test, 'attempt' => $existingAttempt]);
        }

        // Yangi attempt yaratish
        $attempt = UserTestAttempt::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'test_id' => $test->id,
            'started_at' => now(),
            'total_questions' => $test->questions()->count(),
            'status' => 'in_progress'
        ]);

        return redirect()->route('tests.take', ['test' => $test, 'attempt' => $attempt]);
    }

    public function take(Test $test, UserTestAttempt $attempt, Request $request)
    {
        // Authorization check
        if (!$this->canAccessAttempt($attempt)) {
            abort(403, 'Bu testga kirishga ruxsatingiz yo\'q.');
        }

        $test->load(['questions']);
        $attempt->load(['userAnswers']);

        // Javoblarni organish
        $answers = $attempt->userAnswers->keyBy('test_question_id');

        return view('tests.take', compact('test', 'attempt', 'answers'));
    }

    public function submitAnswer(Request $request, Test $test, UserTestAttempt $attempt)
    {
        if (!$this->canAccessAttempt($attempt)) {
            return response()->json(['error' => 'Ruxsat berilmagan'], 403);
        }

        $request->validate([
            'question_id' => 'required|exists:test_questions,id',
            'answer' => 'required|string'
        ]);

        $question = TestQuestion::findOrFail($request->question_id);
        
        // Javobning to'g'riligini tekshirish
        $isCorrect = $question->isCorrectAnswer($request->answer);
        $pointsEarned = $isCorrect ? $question->points : 0;

        // Javobni saqlash yoki yangilash
        UserAnswer::updateOrCreate(
            [
                'user_test_attempt_id' => $attempt->id,
                'test_question_id' => $question->id
            ],
            [
                'user_answer' => $request->answer,
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned,
                'answered_at' => now()
            ]
        );

        return response()->json([
            'success' => true,
            'is_correct' => $isCorrect,
            'points_earned' => $pointsEarned
        ]);
    }

    public function submitTest(Test $test, UserTestAttempt $attempt, Request $request)
    {
        if (!$this->canAccessAttempt($attempt)) {
            abort(403);
        }

        $attempt->update([
            'completed_at' => now(),
            'status' => 'completed'
        ]);

        $attempt->calculateScore();

        return redirect()->route('tests.results', ['test' => $test, 'attempt' => $attempt]);
    }

    public function complete(Test $test, UserTestAttempt $attempt)
    {
        if (!$this->canAccessAttempt($attempt)) {
            abort(403);
        }

        $attempt->update([
            'completed_at' => now(),
            'status' => 'completed'
        ]);

        $attempt->calculateScore();

        return redirect()->route('tests.results', ['test' => $test, 'attempt' => $attempt]);
    }

    public function results(Test $test, UserTestAttempt $attempt)
    {
        if (!$this->canAccessAttempt($attempt) || $attempt->status !== 'completed') {
            abort(403);
        }

        $attempt->load(['userAnswers.testQuestion', 'test.category']);

        return view('tests.results', compact('test', 'attempt'));
    }

    private function canAccessAttempt(UserTestAttempt $attempt): bool
    {
        $userId = auth()->id();
        $sessionId = session()->getId();

        return ($userId && $attempt->user_id == $userId) || 
               (!$userId && $attempt->session_id == $sessionId);
    }
    
    /**
     * Listening familiarisation testlarni ko'rsatish
     */
    public function showListeningFamiliarisation()
    {
        $category = TestCategory::where('name', 'Listening')->first();
        
        $tests = Test::where('test_category_id', $category->id)
            ->where('type', TestType::FAMILIARISATION)
            ->where('is_active', true)
            ->get();
            
        return view('tests.public-familiarisation', [
            'tests' => $tests,
            'category' => 'Listening',
            'pageTitle' => 'IELTS Listening Familiarisation Tests'
        ]);
    }
    
    /**
     * Reading familiarisation testlarni ko'rsatish
     */
    public function showReadingFamiliarisation()
    {
        $category = TestCategory::where('name', 'Academic Reading')->first();
        
        $tests = Test::where('test_category_id', $category->id)
            ->where('type', TestType::FAMILIARISATION)
            ->where('is_active', true)
            ->get();
            
        return view('tests.public-familiarisation', [
            'tests' => $tests,
            'category' => 'Reading',
            'pageTitle' => 'IELTS Academic Reading Familiarisation Tests'
        ]);
    }
    
    /**
     * Writing familiarisation testlarni ko'rsatish
     */
    public function showWritingFamiliarisation()
    {
        $category = TestCategory::where('name', 'Academic Writing')->first();
        
        $tests = Test::where('test_category_id', $category->id)
            ->where('type', TestType::FAMILIARISATION)
            ->where('is_active', true)
            ->get();
            
        return view('tests.public-familiarisation', [
            'tests' => $tests,
            'category' => 'Writing',
            'pageTitle' => 'IELTS Academic Writing Familiarisation Tests'
        ]);
    }
}
