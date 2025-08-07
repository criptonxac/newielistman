<?php

namespace App\Http\Controllers;

use App\Models\WritingTest;
use App\Models\AppTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WritingTestCrudController extends Controller
{
    /**
     * Constructor - Middleware qo'shish
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isAdmin() && !Auth::user()->isTeacher()) {
                abort(403, 'Faqat admin va teacher foydalanuvchilar writing test boshqara oladi');
            }
            return $next($request);
        });
    }

    /**
     * Writing Test - Barcha testlar ro'yxati
     */
    public function index(Request $request)
    {
        $query = WritingTest::with('appTest');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('appTest', function($appQuery) use ($search) {
                      $appQuery->where('title', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by app_test_id
        if ($request->filled('app_test_id')) {
            $query->where('app_test_id', $request->get('app_test_id'));
        }

        $writingTests = $query->orderBy('id', 'desc')->paginate(15);
        $appTests = AppTest::active()->get();

        $layout = Auth::user()->isAdmin() ? 'layouts.main' : 'layouts.teacher';
        return view('writing-tests.index', compact('writingTests', 'appTests', 'layout'));
    }

    /**
     * Writing Test - Yangi test yaratish formasi
     */
    public function create()
    {
        $appTests = AppTest::active()->get();
        $layout = Auth::user()->isAdmin() ? 'layouts.main' : 'layouts.teacher';
        return view('writing-tests.create', compact('appTests', 'layout'));
    }

    /**
     * Writing Test - Yangi testni saqlash
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'app_test_id' => 'required|exists:app_test,id',
            'title' => 'required|string|max:255',
            'questions' => 'nullable|string', // Frontend dan JSON string keladi (ixtiyoriy)
            'answer' => 'nullable|string', // Frontend dan JSON string keladi (ixtiyoriy)
            // Qo'shimcha maydonlar (ixtiyoriy, agar frontend dan alohida kelsa)
            'question_title' => 'nullable|string|max:255',
            'question_content' => 'nullable|string',
            'question_instructions' => 'nullable|string',
            'answer_sample' => 'nullable|string',
            'answer_criteria' => 'nullable|string',
        ]);

        // Questions ma'lumotlarini JSON formatda tayyorlash
        $questionsData = [
            'title' => $validated['question_title'] ?? '',
            'content' => $validated['question_content'] ?? '',
            'instructions' => $validated['question_instructions'] ?? ''
        ];
        
        // Answer ma'lumotlarini JSON formatda tayyorlash
        $answerData = [
            'sample' => $validated['answer_sample'] ?? '',
            'criteria' => $validated['answer_criteria'] ?? ''
        ];
        
        // Agar JSON string kelgan bo'lsa, uni decode qilib, yangi ma'lumotlar bilan birlashtirish
        if (!empty($validated['questions'])) {
            $existingQuestions = json_decode($validated['questions'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($existingQuestions)) {
                $questionsData = array_merge($existingQuestions, array_filter($questionsData));
            }
        }
        
        if (!empty($validated['answer'])) {
            $existingAnswer = json_decode($validated['answer'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($existingAnswer)) {
                $answerData = array_merge($existingAnswer, array_filter($answerData));
            }
        }

        // Faqat asosiy maydonlarni saqlash
        WritingTest::create([
            'app_test_id' => $validated['app_test_id'],
            'title' => $validated['title'],
            'questions' => json_encode($questionsData),
            'answer' => json_encode($answerData)
        ]);

        return redirect()
            ->route('writing-tests.index')
            ->with('success', 'Writing test muvaffaqiyatli yaratildi!');
    }

    /**
     * Writing Test - Testni ko'rish
     */
    public function show(WritingTest $writingTest)
    {
        $writingTest->load('appTest');
        $layout = Auth::user()->isAdmin() ? 'layouts.main' : 'layouts.teacher';
        return view('writing-tests.show', compact('writingTest', 'layout'));
    }

    /**
     * Writing Test - Testni tahrirlash formasi
     */
    public function edit(WritingTest $writingTest)
    {
        $appTests = AppTest::active()->get();
        $layout = Auth::user()->isAdmin() ? 'layouts.main' : 'layouts.teacher';
        return view('writing-tests.edit', compact('writingTest', 'appTests', 'layout'));
    }

    /**
     * Writing Test - Testni yangilash
     */
    public function update(Request $request, WritingTest $writingTest)
    {
        $validated = $request->validate([
            'app_test_id' => 'required|exists:app_test,id',
            'title' => 'required|string|max:255',
            'questions' => 'nullable|string', // Frontend dan JSON string keladi (ixtiyoriy)
            'answer' => 'nullable|string', // Frontend dan JSON string keladi (ixtiyoriy)
            // Qo'shimcha maydonlar (ixtiyoriy, agar frontend dan alohida kelsa)
            'question_title' => 'nullable|string|max:255',
            'question_content' => 'nullable|string',
            'question_instructions' => 'nullable|string',
            'answer_sample' => 'nullable|string',
            'answer_criteria' => 'nullable|string',
        ]);

        // Mavjud ma'lumotlarini olish
        $existingQuestions = json_decode($writingTest->questions, true) ?? [];
        $existingAnswer = json_decode($writingTest->answer, true) ?? [];
        
        // Questions ma'lumotlarini yangilash
        $questionsData = [
            'title' => $validated['question_title'] ?? $existingQuestions['title'] ?? '',
            'content' => $validated['question_content'] ?? $existingQuestions['content'] ?? '',
            'instructions' => $validated['question_instructions'] ?? $existingQuestions['instructions'] ?? ''
        ];
        
        // Answer ma'lumotlarini yangilash
        $answerData = [
            'sample' => $validated['answer_sample'] ?? $existingAnswer['sample'] ?? '',
            'criteria' => $validated['answer_criteria'] ?? $existingAnswer['criteria'] ?? ''
        ];
        
        // Agar JSON string kelgan bo'lsa, uni decode qilib, yangi ma'lumotlar bilan birlashtirish
        if (!empty($validated['questions'])) {
            $newQuestions = json_decode($validated['questions'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($newQuestions)) {
                $questionsData = array_merge($questionsData, array_filter($newQuestions));
            }
        }
        
        if (!empty($validated['answer'])) {
            $newAnswer = json_decode($validated['answer'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($newAnswer)) {
                $answerData = array_merge($answerData, array_filter($newAnswer));
            }
        }

        // Faqat asosiy maydonlarni yangilash
        $writingTest->update([
            'app_test_id' => $validated['app_test_id'],
            'title' => $validated['title'],
            'questions' => json_encode($questionsData),
            'answer' => json_encode($answerData)
        ]);

        return redirect()
            ->route('writing-tests.index')
            ->with('success', 'Writing test muvaffaqiyatli yangilandi!');
    }

    /**
     * Writing Test - Testni o'chirish
     */
    public function destroy(WritingTest $writingTest)
    {
        $writingTest->delete();

        return redirect()
            ->route('writing-tests.index')
            ->with('success', 'Writing test muvaffaqiyatli o\'chirildi!');
    }
}
