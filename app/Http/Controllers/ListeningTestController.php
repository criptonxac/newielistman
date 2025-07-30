<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\UserTestAttempt;
use App\Services\QuestionRenderer;
use Illuminate\Http\Request;

class ListeningTestController extends Controller
{
    /**
     * Unified listening test view that shows all parts in one page
     */
    public function unifiedTest(Test $test, $attempt)
    {
        $attempt = UserTestAttempt::findOrFail($attempt);
        
        // Get all questions for the test
        $allQuestions = $test->questions()->orderBy('sort_order')->get();
        
        // Divide questions into parts
        $part1Questions = $allQuestions->take(10);
        $part2Questions = $allQuestions->skip(10)->take(10);
        $part3Questions = $allQuestions->skip(20)->take(10);
        $part4Questions = $allQuestions->skip(30)->take(10);
        
        // Get audio files for the test
        $audioFiles = [
            'part1' => asset('audio/listening-part1.mp3'),
            'part2' => asset('audio/listening-part2.mp3'),
            'part3' => asset('audio/listening-part3.mp3'),
            'part4' => asset('audio/listening-part4.mp3'),
        ];
        
        $userAnswers = json_decode($attempt->answers, true) ?? [];
        
        // QuestionRenderer servisini ishlatish
        $questionRenderer = new QuestionRenderer();
        
        return view('tests.listening.listening-test', compact(
            'test', 
            'attempt', 
            'part1Questions', 
            'part2Questions', 
            'part3Questions', 
            'part4Questions',
            'userAnswers', 
            'questionRenderer',
            'audioFiles'
        ));
    }
    public function start(Test $test, Request $request)
{
    // Foydalanuvchi autentifikatsiyadan o'tgan bo'lsa, user_id ni olish
    $userId = auth()->id();
    $sessionId = session()->getId();
    
    // Yangi test urinishini yaratish
    $attempt = UserTestAttempt::create([
        'user_id' => $userId,
        'test_id' => $test->id,
        'session_id' => $sessionId,
        'started_at' => now(),
        'total_questions' => $test->total_questions,
        'status' => 'in_progress'
    ]);

    // Unified test view ga yo'naltirish
    return redirect()->route('listening.unified', ['test' => $test->slug, 'attempt' => $attempt->id]);
}

    public function part1(Test $test, UserTestAttempt $attempt)
{
    if (!$this->canAccessAttempt($attempt)) {
        abort(403, 'Bu testga kirishga ruxsatingiz yo\'q.');
    }
    
    // Get questions for part 1 (first 10 questions)
    $questions = $test->questions()->where('sort_order', '<=', 10)->orderBy('sort_order')->get();
    $userAnswers = json_decode($attempt->answers, true) ?? [];
    
    // QuestionRenderer servisini ishlatish
    $questionRenderer = new QuestionRenderer();
    
    return view('tests.listening.part1', compact('test', 'attempt', 'questions', 'userAnswers', 'questionRenderer'));
}

    public function part2(Test $test, UserTestAttempt $attempt)
    {
        if (!$this->canAccessAttempt($attempt)) {
            abort(403, 'Bu testga kirishga ruxsatingiz yo\'q.');
        }
        
        // Get questions for part 2 (questions 11-20)
        $questions = $test->questions()->where('part', 2)->orWhere(function($query) {
            $query->where('sort_order', '>', 10)->where('sort_order', '<=', 20);
        })->orderBy('sort_order')->get();
        $userAnswers = json_decode($attempt->answers, true) ?? [];
        
        // QuestionRenderer servisini ishlatish
        $questionRenderer = new QuestionRenderer();
        
        return view('tests.listening.part2', compact('test', 'attempt', 'questions', 'userAnswers', 'questionRenderer'));
    }

    public function part3(Test $test, UserTestAttempt $attempt)
    {
        if (!$this->canAccessAttempt($attempt)) {
            abort(403, 'Bu testga kirishga ruxsatingiz yo\'q.');
        }
        
        // Get questions for part 3 (questions 21-30)
        $questions = $test->questions()->where('part', 3)->orWhere(function($query) {
            $query->where('sort_order', '>', 20)->where('sort_order', '<=', 30);
        })->orderBy('sort_order')->get();
        $userAnswers = json_decode($attempt->answers, true) ?? [];
        
        // QuestionRenderer servisini ishlatish
        $questionRenderer = new QuestionRenderer();
        
        return view('tests.listening.part3', compact('test', 'attempt', 'questions', 'userAnswers', 'questionRenderer'));
    }

    public function part4(Test $test, UserTestAttempt $attempt)
    {
        if (!$this->canAccessAttempt($attempt)) {
            abort(403, 'Bu testga kirishga ruxsatingiz yo\'q.');
        }
        
        // Get questions for part 4 (questions 31-40)
        $questions = $test->questions()->where('part', 4)->orWhere(function($query) {
            $query->where('sort_order', '>', 30)->where('sort_order', '<=', 40);
        })->orderBy('sort_order')->get();
        $userAnswers = json_decode($attempt->answers, true) ?? [];
        
        // QuestionRenderer servisini ishlatish
        $questionRenderer = new QuestionRenderer();
        
        return view('tests.listening.part4', compact('test', 'attempt', 'questions', 'userAnswers', 'questionRenderer'));
    }

    public function submitAnswers(Request $request, Test $test, UserTestAttempt $attempt)
{
    if (!$this->canAccessAttempt($attempt)) {
        return redirect()->back()->with('error', 'Ruxsat berilmagan');
    }
    // Attempt yakunlandi deb belgilash
    $attempt->status = 'completed';
    $attempt->completed_at = now();
    $attempt->save();
    
    // Natijalar sahifasiga yo'naltirish
    return redirect()->route('tests.results', [
        'test' => $test->slug,
        'attempt' => $attempt->id
    ])->with('success', 'Test muvaffaqiyatli yakunlandi!');
}

    public function saveAnswer(Request $request, Test $test, UserTestAttempt $attempt)
    {
        if (!$this->canAccessAttempt($attempt)) {
            return response()->json(['error' => 'Ruxsat berilmagan'], 403);
        }

        $request->validate([
            'question_id' => 'required',
            'answer' => 'required|string'
        ]);

        // Mavjud javoblarni olish
        $answers = json_decode($attempt->answers, true) ?: [];
        
        // Yangi javobni qo'shish yoki mavjud javobni yangilash
        $answers[$request->question_id] = $request->answer;
        
        // Javoblarni saqlash
        $attempt->answers = json_encode($answers);
        $attempt->save();

        return response()->json([
            'success' => true,
            'message' => 'Javob saqlandi',
            'question_id' => $request->question_id,
            'answer' => $request->answer
        ]);
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

        return redirect()->route('tests.results', ['test' => $test->slug, 'attempt' => $attempt->id]);
    }

    private function canAccessAttempt(UserTestAttempt $attempt): bool
    {
        $userId = auth()->id();
        $sessionId = session()->getId();

        return ($userId && $attempt->user_id == $userId) || 
               (!$userId && $attempt->session_id == $sessionId);
    }
}
