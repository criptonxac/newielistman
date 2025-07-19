
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Test;
use App\Models\TestCategory;
use App\Models\UserTestAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TestResultsExport;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || !auth()->user()->isAdmin()) {
                abort(403, 'Faqat adminlar kirish huquqiga ega.');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_teachers' => User::where('role', 'teacher')->count(),
            'total_tests' => Test::count(),
            'total_attempts' => UserTestAttempt::count(),
            'completed_attempts' => UserTestAttempt::completed()->count(),
            'today_attempts' => UserTestAttempt::today()->count(),
            'this_week_attempts' => UserTestAttempt::thisWeek()->count(),
            'this_month_attempts' => UserTestAttempt::thisMonth()->count(),
        ];

        // Chart uchun ma'lumotlar
        $weeklyStats = UserTestAttempt::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $categoryStats = UserTestAttempt::join('tests', 'user_test_attempts.test_id', '=', 'tests.id')
            ->join('test_categories', 'tests.test_category_id', '=', 'test_categories.id')
            ->select('test_categories.name', DB::raw('COUNT(*) as count'))
            ->groupBy('test_categories.name')
            ->get();

        return view('admin.dashboard', compact('stats', 'weeklyStats', 'categoryStats'));
    }

    public function users()
    {
        $users = User::with('testAttempts')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,teacher,student',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'is_active' => true
        ]);

        return redirect()->route('admin.users')->with('success', 'Foydalanuvchi muvaffaqiyatli yaratildi!');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,teacher,student',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'is_active' => 'boolean'
        ]);

        $user->update($request->only(['name', 'email', 'role', 'phone', 'date_of_birth', 'is_active']));

        if ($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.users')->with('success', 'Foydalanuvchi ma\'lumotlari yangilandi!');
    }

    public function destroyUser(User $user)
    {
        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('admin.users')->with('error', 'Oxirgi adminni o\'chirib bo\'lmaydi!');
        }

        $user->delete();
        return redirect()->route('admin.users')->with('success', 'Foydalanuvchi o\'chirildi!');
    }

    public function statistics()
    {
        $students = User::where('role', 'student')
            ->with(['completedAttempts' => function($query) {
                $query->with('test.category');
            }])
            ->get();

        return view('admin.statistics', compact('students'));
    }

    public function exportResults()
    {
        return Excel::download(new TestResultsExport, 'test-results-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function tests()
    {
        $tests = Test::with('category')->paginate(20);
        return view('admin.tests.index', compact('tests'));
    }

    public function createTest()
    {
        $categories = TestCategory::all();
        return view('admin.tests.create', compact('categories'));
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
            'is_timed' => 'boolean',
            'is_active' => 'boolean'
        ]);

        $data = $request->all();
        $data['slug'] = \Str::slug($request->title);
        
        Test::create($data);

        return redirect()->route('admin.tests')->with('success', 'Test muvaffaqiyatli yaratildi!');
    }
}
