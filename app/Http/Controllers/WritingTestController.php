<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\UserTestAttempt;
use Illuminate\Http\Request;

class WritingTestController extends Controller
{
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

        return redirect()->route('writing.task1', ['test' => $test->slug, 'attempt' => $attempt->id]);
    }

    public function task1(Test $test, $attempt)
    {
        $attempt = UserTestAttempt::findOrFail($attempt);
        // QuestionRenderer servisini ishlatish
        $questionRenderer = new QuestionRenderer();
        return view('tests.writing.task1', compact('test', 'attempt', 'questionRenderer'));
    }

    public function task2(Test $test, $attempt)
    {
        $attempt = UserTestAttempt::findOrFail($attempt);
        // QuestionRenderer servisini ishlatish
        $questionRenderer = new QuestionRenderer();
        return view('tests.writing.task2', compact('test', 'attempt', 'questionRenderer'));
    }

    public function submitAnswers(Test $test, $attempt, Request $request)
    {
        $attempt = UserTestAttempt::findOrFail($attempt);
        
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
        
        return redirect()->route('writing.complete', ['test' => $test->slug, 'attempt' => $attempt->id]);
    }

    public function complete(Test $test, $attempt)
    {
        $attempt = UserTestAttempt::findOrFail($attempt);
        
        // Testni yakunlash
        $attempt->status = 'completed';
        $attempt->completed_at = now();
        $attempt->save();
        
        return view('tests.writing.complete', compact('test', 'attempt'));
    }
}
