<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Test;
use App\Models\TestCategory;
use App\Models\TestQuestion;
use App\Models\UserTestAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_teachers' => User::where('role', 'teacher')->count(),
            'total_questions' => TestQuestion::count(),
            'total_tests' => Test::count(),
            'total_attempts' => UserTestAttempt::count(),
        ];

        // Bugungi o'rtacha ball
        $todayAverageScore = UserTestAttempt::today()
            ->completed()
            ->avg('total_score') ?? 0;

        // Eng yaxshi 10 talaba (o'rtacha ball bo'yicha)
        $topStudents = User::where('role', User::ROLE_STUDENT)
            ->withAvg('completedAttempts', 'total_score')
            ->orderByDesc('completed_attempts_avg_total_score')
            ->take(10)
            ->get()
            ->map(function($user) {
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'average_score' => round($user->completed_attempts_avg_total_score ?? 0, 1),
                    'total_tests' => $user->completedAttempts()->count()
                ];
            });



        // Talaba faolligi statistikalari
        $studentActivity = [
            'active_students' => User::where('role', User::ROLE_STUDENT)
                ->whereHas('testAttempts', function($query) {
                    $query->where('created_at', '>=', now()->subDays(7));
                })->count(),
            'completed_tests' => UserTestAttempt::completed()->count(),
            'average_score' => round(UserTestAttempt::completed()->avg('total_score') ?? 0, 1),
            'highest_score' => UserTestAttempt::completed()->max('total_score') ?? 0
        ];

        // Agar haqiqiy ma'lumotlar bo'lmasa, demo ma'lumotlar
        if ($studentActivity['completed_tests'] == 0) {
            $studentActivity = [
                'active_students' => 24,
                'completed_tests' => 156,
                'average_score' => 73.8,
                'highest_score' => 94.5
            ];
        }

        // Haftalik statistika (haqiqiy ma'lumotlar)
        $weeklyStats = collect();
        $totalWeeklyTests = 0;
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = UserTestAttempt::whereDate('created_at', $date->toDateString())->count();
            $totalWeeklyTests += $count;
            $weeklyStats->push([
                'date' => $date->format('D'),
                'count' => $count
            ]);
        }

        // Haftalik faollik foizi
        $weeklyActivity = [
            'total_tests' => $totalWeeklyTests,
            'active_days' => $weeklyStats->where('count', '>', 0)->count(),
            'avg_daily' => $totalWeeklyTests > 0 ? round($totalWeeklyTests / 7, 1) : 0,
            'activity_percentage' => $weeklyStats->where('count', '>', 0)->count() > 0 ?
                round(($weeklyStats->where('count', '>', 0)->count() / 7) * 100, 1) : 0
        ];

        // Agar haqiqiy ma'lumotlar bo'lmasa, demo ma'lumotlar
        if ($totalWeeklyTests == 0) {
            $weeklyActivity = [
                'total_tests' => 47,
                'active_days' => 6,
                'avg_daily' => 6.7,
                'activity_percentage' => 85.7
            ];

            // Demo haftalik statistika
            $weeklyStats = collect([
                ['date' => 'Mon', 'count' => 8],
                ['date' => 'Tue', 'count' => 12],
                ['date' => 'Wed', 'count' => 0],
                ['date' => 'Thu', 'count' => 9],
                ['date' => 'Fri', 'count' => 15],
                ['date' => 'Sat', 'count' => 3],
                ['date' => 'Sun', 'count' => 0]
            ]);
        }

        return view('admin.dashboard', compact('stats', 'weeklyStats', 'todayAverageScore', 'topStudents', 'studentActivity', 'weeklyActivity'));
    }

    public function index(Request $request)
    {
        $users = User::paginate(20);
        return view('admin.users', compact('users'));
    }

    // Users CRUD Methods
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,teacher,student'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'Foydalanuvchi muvaffaqiyatli yaratildi!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:admin,teacher,student'
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('admin.users')
            ->with('success', 'Foydalanuvchi muvaffaqiyatli yangilandi!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting the current admin user
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')
                ->with('error', 'Siz o\'zingizni o\'chira olmaysiz!');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'Foydalanuvchi muvaffaqiyatli o\'chirildi!');
    }

    public function tests(Request $request)
    {
        // Test kategoriyalari va testlarni olish
        $categories = TestCategory::with(['tests' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->get();

        // Barcha testlar
        $tests = Test::with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.tests', compact('categories', 'tests'));
    }

    /**
     * Foydalanuvchi rollariga qarab dashboard ko'rsatish
     */
    public function dashboardRedirect()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'teacher':
                return redirect()->route('teacher.dashboard');
            case 'student':
                return redirect()->route('student.dashboard');
            default:
                return redirect()->route('home');
        }
    }

}
