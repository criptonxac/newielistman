<?php


use App\Http\Controllers\HomeController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TestCategoryController;
use Illuminate\Support\Facades\Route;

// Asosiy sahifa
Route::get('/', [HomeController::class, 'index'])->name('home');

// Test kategoriyalari
Route::get('/categories', [TestCategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [TestCategoryController::class, 'show'])->name('categories.show');

// Statik sahifalar
Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/help', function () {
    return view('pages.help');
})->name('help');

// Testlar
Route::prefix('tests')->name('tests.')->group(function () {
    Route::get('/{test:slug}', [TestController::class, 'show'])->name('show');
    Route::post('/{test:slug}/start', [TestController::class, 'start'])->name('start');
    Route::get('/{test:slug}/take/{attempt}', [TestController::class, 'take'])->name('take');
    Route::post('/{test:slug}/attempt/{attempt}/answer', [TestController::class, 'submitAnswer'])->name('submit-answer');
    Route::post('/{test:slug}/attempt/{attempt}/submit', [TestController::class, 'submitTest'])->name('submit');
    Route::post('/{test:slug}/attempt/{attempt}/complete', [TestController::class, 'complete'])->name('complete');
    Route::get('/{test:slug}/attempt/{attempt}/results', [TestController::class, 'results'])->name('results');
});

// Role-based dashboard redirect
Route::get('/dashboard', function () {
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
})->middleware(['auth', 'verified'])->name('dashboard');

// Direct admin access (development only)
Route::get('/admin-direct', function () {
    // Create temporary admin session
    $adminUser = App\Models\User::where('role', 'admin')->first();
    if (!$adminUser) {
        // Create admin user if not exists
        $adminUser = App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@ielts.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }
    
    auth()->login($adminUser);
    return redirect()->route('admin.dashboard');
})->name('admin.direct');

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('users');
    Route::get('/tests', [App\Http\Controllers\AdminController::class, 'tests'])->name('tests');
});

// Teacher routes
Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\TeacherController::class, 'dashboard'])->name('dashboard');
    Route::get('/students', [App\Http\Controllers\TeacherController::class, 'students'])->name('students');
    Route::get('/results', [App\Http\Controllers\TeacherController::class, 'results'])->name('results');
});

// Student routes
Route::prefix('student')->name('student.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/tests', [App\Http\Controllers\StudentController::class, 'tests'])->name('tests');
    Route::get('/results', [App\Http\Controllers\StudentController::class, 'results'])->name('results');
});



require __DIR__.'/auth.php';
