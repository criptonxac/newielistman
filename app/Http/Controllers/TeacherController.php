
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Test;
use App\Models\TestCategory;
use App\Models\UserTestAttempt;
use App\Models\TestQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || (!auth()->user()->isTeacher() && !auth()->user()->isAdmin())) {
                abort(403, 'Faqat o\'qituvchilar kirish huquqiga ega.');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $stats = [
            'total_tests' => Test::count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_attempts' => UserTestAttempt::count(),
            'completed_attempts' => UserTestAttempt::completed()->count(),
            'today_attempts' => UserTestAttempt::today()->count(),
        ];

        $recentAttempts = UserTestAttempt::with(['user', 'test'])
            ->completed()
            ->latest()
            ->limit(10)
            ->get();

        return view('teacher.dashboard', compact('stats', 'recentAttempts'));
    }

    public function tests()
    {
        $tests = Test::with(['category', 'attempts'])->paginate(20);
        return view('teacher.tests.index', compact('tests'));
    }

    public function createTest()
    {
        $categories = TestCategory::all();
        return view('teacher.tests.create', compact('categories'));
    }

    public function storeTest(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'test_category_id' => 'required|exists:test_categories,id',
            'type' => 'required|in:familiarisation,sample',
            'duration_minutes' => 'nullable|integer|min:1',
            'total_questions' => 'required|integer|min:1',
            'instructions' => 'nullable|array',
            'is_timed' => 'boolean',
            'is_active' => 'boolean'
        ]);

        $data = $request->all();
        $data['slug'] = \Str::slug($request->title);
        
        $test = Test::create($data);

        return redirect()->route('teacher.tests.questions', $test)->with('success', 'Test yaratildi! Endi savollar qo\'shing.');
    }

    public function showTest(Test $test)
    {
        $test->load(['category', 'questions', 'attempts.user']);
        return view('teacher.tests.show', compact('test'));
    }

    public function editTest(Test $test)
    {
        $categories = TestCategory::all();
        return view('teacher.tests.edit', compact('test', 'categories'));
    }

    public function updateTest(Request $request, Test $test)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'test_category_id' => 'required|exists:test_categories,id',
            'type' => 'required|in:familiarisation,sample',
            'duration_minutes' => 'nullable|integer|min:1',
            'total_questions' => 'required|integer|min:1',
            'is_timed' => 'boolean',
            'is_active' => 'boolean'
        ]);

        $test->update($request->all());

        return redirect()->route('teacher.tests')->with('success', 'Test yangilandi!');
    }

    public function questions(Test $test)
    {
        $questions = $test->questions()->ordered()->get();
        return view('teacher.tests.questions', compact('test', 'questions'));
    }

    public function createQuestion(Test $test)
    {
        return view('teacher.tests.create-question', compact('test'));
    }

    public function storeQuestion(Request $request, Test $test)
    {
        $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,fill_blank,essay',
            'options' => 'nullable|array',
            'correct_answer' => 'required_unless:question_type,essay|string',
            'points' => 'required|integer|min:1',
            'explanation' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['test_id'] = $test->id;
        $data['question_number'] = $test->questions()->count() + 1;
        $data['sort_order'] = $data['question_number'];

        TestQuestion::create($data);

        return redirect()->route('teacher.tests.questions', $test)->with('success', 'Savol qo\'shildi!');
    }

    public function results()
    {
        $attempts = UserTestAttempt::with(['user', 'test.category'])
            ->completed()
            ->latest()
            ->paginate(20);

        return view('teacher.results', compact('attempts'));
    }

    public function studentResults(User $student)
    {
        if (!$student->isStudent()) {
            abort(404);
        }

        $attempts = $student->completedAttempts()
            ->with(['test.category'])
            ->latest()
            ->paginate(10);

        return view('teacher.student-results', compact('student', 'attempts'));
    }
}
