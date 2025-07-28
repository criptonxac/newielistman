<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\UserTestAttempt;
use App\Services\QuestionRenderer;
use Illuminate\Http\Request;

class ListeningTestController extends Controller
{
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

        // Part 1 ga yo'naltirish
        return redirect()->route('listening.part1', ['test' => $test->slug, 'attempt' => $attempt->id]);
    }

    public function part1(Test $test, UserTestAttempt $attempt)
    {
        if (!$this->canAccessAttempt($attempt)) {
            abort(403, 'Bu testga kirishga ruxsatingiz yo\'q.');
        }
        
        // Get questions for part 1 (first 10 questions)
        $questions = $test->questions()->where('part', 1)->orWhere('sort_order', '<=', 10)->orderBy('sort_order')->get();
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
            return response()->json(['error' => 'Ruxsat berilmagan'], 403);
        }

        $request->validate([
            'answers' => 'required|array',
        ]);

        $answers = $request->answers;
        
        // Javoblarni saqlash logikasi
        // Bu yerda javoblarni saqlash uchun kod yoziladi

        return response()->json([
            'success' => true,
            'message' => 'Javoblar saqlandi'
        ]);
    }

    public function saveAnswer(Request $request, Test $test, UserTestAttempt $attempt)
    {
        if (!$this->canAccessAttempt($attempt)) {
            return response()->json(['error' => 'Ruxsat berilmagan'], 403);
        }

        $request->validate([
            'question_number' => 'required',
            'answer' => 'required|string'
        ]);

        // Javobni saqlash logikasi
        // Bu yerda ma'lumotlar bazasiga saqlash kodi bo'lishi kerak
        // Hozircha faqat muvaffaqiyatli javob qaytaramiz

        return response()->json([
            'success' => true,
            'message' => 'Javob saqlandi',
            'question' => $request->question_number,
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
