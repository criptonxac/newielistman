<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\UserTestAttempt;
use App\Services\QuestionRenderer;
use Illuminate\Http\Request;

class ReadingTestController extends Controller
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

        return redirect()->route('reading.part1', ['test' => $test->slug, 'attempt' => $attempt->id]);
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
