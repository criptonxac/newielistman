<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestCategory;
use App\Models\TestQuestion;
use App\Models\TestAudioFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TestManagementController extends Controller
{
    public function __construct()
    {
        // Faqat admin va teacher kirishi mumkin
        $this->middleware(function ($request, $next) {
            if (!Auth::user() || (!Auth::user()->isAdmin() && !Auth::user()->isTeacher())) {
                abort(403, 'Ruxsat berilmagan');
            }
            return $next($request);
        });
    }

    /**
     * Testlar ro'yxati
     */
    public function index()
    {
        $tests = Test::with(['category', 'questions', 'audioFiles'])
            ->when(Auth::user()->isTeacher(), function($query) {
                // Teacher faqat o'z testlarini ko'radi
                $query->where('created_by', Auth::id());
            })
            ->latest()
            ->paginate(15);

        return view('admin.tests.index', compact('tests'));
    }

    /**
     * Yangi test yaratish formasi
     */
    public function create()
    {
        $categories = TestCategory::active()->get();
        return view('admin.tests.create', compact('categories'));
    }

    /**
     * Yangi test saqlash
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:practice,mock,real',
            'duration_minutes' => 'required|integer|min:1',
            'pass_score' => 'required|integer|min:0|max:100',
            'attempts_allowed' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');

        $test = Test::create($validated);

        return redirect()
            ->route('admin.tests.questions.create', $test->id)
            ->with('success', 'Test muvaffaqiyatli yaratildi. Endi savollar qo\'shing.');
    }

    /**
     * Test tahrirlash formasi
     */
    public function edit(Test $test)
    {
        // Teacher faqat o'z testini tahrirlashi mumkin
        if (Auth::user()->isTeacher() && $test->created_by !== Auth::id()) {
            abort(403);
        }

        $categories = TestCategory::active()->get();
        return view('admin.tests.edit', compact('test', 'categories'));
    }

    /**
     * Test yangilash
     */
    public function update(Request $request, Test $test)
    {
        // Teacher faqat o'z testini yangilashi mumkin
        if (Auth::user()->isTeacher() && $test->created_by !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:practice,mock,real',
            'duration_minutes' => 'required|integer|min:1',
            'pass_score' => 'required|integer|min:0|max:100',
            'attempts_allowed' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $test->update($validated);

        return redirect()
            ->route('admin.tests.index')
            ->with('success', 'Test muvaffaqiyatli yangilandi.');
    }

    /**
     * Test o'chirish
     */
    public function destroy(Test $test)
    {
        // Teacher faqat o'z testini o'chirishi mumkin
        if (Auth::user()->isTeacher() && $test->created_by !== Auth::id()) {
            abort(403);
        }

        // Audio fayllarni o'chirish
        foreach ($test->audioFiles as $audio) {
            $audio->deleteFile();
        }

        $test->delete();

        return redirect()
            ->route('admin.tests.index')
            ->with('success', 'Test muvaffaqiyatli o\'chirildi.');
    }

    /**
     * Test savollarini qo'shish/tahrirlash sahifasi
     */
    public function createQuestions(Test $test)
    {
        // Teacher faqat o'z testiga savol qo'shishi mumkin
        if (Auth::user()->isTeacher() && $test->created_by !== Auth::id()) {
            abort(403);
        }

        $questions = $test->questions()
            ->orderBy('part_number')
            ->orderBy('question_number')
            ->paginate(10);

        $layout = Auth::user()->isAdmin() ? 'layouts.admin' : 'layouts.teacher';

        return view('admin.tests.questions.create', compact('test', 'questions', 'layout'));
    }

    /**
     * Savollarni saqlash
     */
    public function storeQuestions(Request $request, Test $test)
    {
        // Teacher faqat o'z testiga savol qo'shishi mumkin
        if (Auth::user()->isTeacher() && $test->created_by !== Auth::id()) {
            abort(403);
        }

        DB::beginTransaction();

        try {
            // Audio fayllarni yuklash (Listening uchun)
            if ($test->category->slug === 'listening' && $request->hasFile('audio_files')) {
                foreach ($request->file('audio_files') as $index => $audioFile) {
                    $path = $audioFile->store('audio/tests/' . $test->id, 'public');
                    
                    TestAudioFile::create([
                        'test_id' => $test->id,
                        'file_path' => $path,
                        'file_name' => $audioFile->getClientOriginalName(),
                        'part_number' => $request->audio_parts[$index] ?? 1,
                        'play_order' => $index + 1,
                        'auto_play' => true
                    ]);
                }
            }

            // Savollarni yangilash/qo'shish
            if ($request->has('questions')) {
                foreach ($request->questions as $questionId => $questionData) {
                    if (is_numeric($questionId)) {
                        // Mavjud savolni yangilash
                        $question = TestQuestion::where('id', $questionId)
                            ->where('test_id', $test->id)
                            ->first();
                        
                        if ($question) {
                            $question->update($questionData);
                        }
                    } else {
                        // Yangi savol qo'shish
                        $questionData['test_id'] = $test->id;
                        TestQuestion::create($questionData);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.tests.questions.create', $test->id)
                ->with('success', 'Savollar muvaffaqiyatli saqlandi!');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Yangi savol qo'shish
     */
    public function addQuestion(Test $test)
    {
        // Teacher faqat o'z testiga savol qo'shishi mumkin
        if (Auth::user()->isTeacher() && $test->created_by !== Auth::id()) {
            abort(403);
        }

        $questionNumber = $test->questions()->count() + 1;
        $layout = Auth::user()->isAdmin() ? 'layouts.admin' : 'layouts.teacher';

        return view('admin.tests.questions.add', compact('test', 'questionNumber', 'layout'));
    }

    /**
     * Savolni o'chirish
     */
    public function deleteQuestion(Test $test, TestQuestion $question)
    {
        // Teacher faqat o'z testidagi savolni o'chirishi mumkin
        if (Auth::user()->isTeacher() && $test->created_by !== Auth::id()) {
            abort(403);
        }

        if ($question->test_id !== $test->id) {
            abort(404);
        }

        $question->delete();

        // Savol raqamlarini qayta tartibga solish
        $test->questions()
            ->where('part_number', $question->part_number)
            ->where('question_number', '>', $question->question_number)
            ->decrement('question_number');

        return response()->json(['success' => true]);
    }

    /**
     * Test preview (ko'rish)
     */
    public function preview(Test $test)
    {
        // Teacher faqat o'z testini ko'rishi mumkin
        if (Auth::user()->isTeacher() && $test->created_by !== Auth::id()) {
            abort(403);
        }

        $test->load(['questions', 'audioFiles', 'category']);

        return view('admin.tests.preview', compact('test'));
    }

    /**
     * Test natijalarini ko'rish
     */
    public function results(Test $test)
    {
        // Teacher faqat o'z testining natijalarini ko'rishi mumkin
        if (Auth::user()->isTeacher() && $test->created_by !== Auth::id()) {
            abort(403);
        }

        $attempts = $test->attempts()
            ->with(['user', 'testAnswers.testQuestion'])
            ->completed()
            ->latest()
            ->paginate(20);

        return view('admin.tests.results', compact('test', 'attempts'));
    }

    /**
     * Test nusxalash
     */
    public function duplicate(Test $test)
    {
        DB::beginTransaction();

        try {
            // Test nusxasi
            $newTest = $test->replicate();
            $newTest->title = $test->title . ' (Nusxa)';
            $newTest->created_by = Auth::id();
            $newTest->save();

            // Savollarni nusxalash
            foreach ($test->questions as $question) {
                $newQuestion = $question->replicate();
                $newQuestion->test_id = $newTest->id;
                $newQuestion->save();
            }

            // Audio fayllarni nusxalash
            foreach ($test->audioFiles as $audio) {
                $newAudio = $audio->replicate();
                $newAudio->test_id = $newTest->id;
                
                // Fayl nusxasi
                if (Storage::disk('public')->exists($audio->file_path)) {
                    $newPath = 'audio/tests/' . $newTest->id . '/' . basename($audio->file_path);
                    Storage::disk('public')->copy($audio->file_path, $newPath);
                    $newAudio->file_path = $newPath;
                }
                
                $newAudio->save();
            }

            DB::commit();

            return redirect()
                ->route('admin.tests.edit', $newTest->id)
                ->with('success', 'Test muvaffaqiyatli nusxalandi!');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()
                ->back()
                ->with('error', 'Nusxalashda xatolik: ' . $e->getMessage());
        }
    }
}