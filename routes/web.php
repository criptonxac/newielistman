<?php


use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TestCategoryController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

// Admin routes
Route::prefix('admin')->name('admin.')
    ->middleware(['auth', 'verified', 'role:' . User::ROLE_ADMIN])
    ->conroller(AdminController::class)
    ->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/users', 'index')->name('users');
        Route::post('/users', 'store')->name('users.store');
        Route::put('/users/{id}', 'update')->name('users.update');
        Route::delete('/users/{id}', 'destroy')->name('users.destroy');
        Route::get('/tests', 'tests')->name('tests');
    });

// Teacher routes
Route::prefix('teacher')
    ->name('teacher.')
    ->middleware(['auth', 'verified', 'role:' . User::ROLE_ADMIN . ',' . User::ROLE_TEACHER])
    ->group(function () {
        Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
        Route::get('/students', [TeacherController::class, 'students'])->name('students');
        Route::get('/results', [TeacherController::class, 'results'])->name('results');
        Route::get('/export/user/{user}', [TeacherController::class, 'exportUser'])->name('export.user');
    });

// Student routes
Route::prefix('student')
    ->name('student.')
    ->middleware(['auth', 'verified', 'role:' . User::ROLE_ADMIN . ',' . User::ROLE_STUDENT])
    ->group(function () {
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
        Route::get('/tests', [StudentController::class, 'tests'])->name('tests');
        Route::get('/results', [StudentController::class, 'results'])->name('results');
    });

// Asosiy sahifa
Route::get('/', [HomeController::class, 'index'])->name('home');

// Test kategoriyalari
Route::prefix('categories')
    ->name('categories.')
    ->controller(TestCategoryController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('{category:slug}', 'show')->name('show');
    });

// Statik sahifalar
Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/help', function () {
    return view('pages.help');
})->name('help');

// Testlar
Route::prefix('tests')
    ->name('tests.')
    ->group(function () {
        Route::get('/{test:slug}', [TestController::class, 'show'])->name('show');
        Route::post('/{test:slug}/start', [TestController::class, 'start'])->name('start');
        Route::get('/{test:slug}/take/{attempt}', [TestController::class, 'take'])->name('take');
        Route::post('/{test:slug}/attempt/{attempt}/answer', [TestController::class, 'submitAnswer'])->name('submit-answer');
        Route::post('/{test:slug}/attempt/{attempt}/submit', [TestController::class, 'submitTest'])->name('submit');
        Route::post('/{test:slug}/attempt/{attempt}/complete', [TestController::class, 'complete'])->name('complete');
        Route::get('/{test:slug}/attempt/{attempt}/results', [TestController::class, 'results'])->name('results');
    });

// Listening Test Parts
use App\Http\Controllers\ListeningTestController;

Route::prefix('listening')
    ->name('listening.')
    ->group(function () {
        Route::get('/{test:slug}/start', [ListeningTestController::class, 'start'])->name('start');
        Route::get('/{test:slug}/part1/{attempt}', [ListeningTestController::class, 'part1'])->name('part1');
        Route::get('/{test:slug}/part2/{attempt}', [ListeningTestController::class, 'part2'])->name('part2');
        Route::get('/{test:slug}/part3/{attempt}', [ListeningTestController::class, 'part3'])->name('part3');
        Route::get('/{test:slug}/part4/{attempt}', [ListeningTestController::class, 'part4'])->name('part4');
        Route::post('/{test:slug}/attempt/{attempt}/answers', [ListeningTestController::class, 'submitAnswers'])->name('submit-answers');
        Route::post('/{test:slug}/attempt/{attempt}/save-answer', [ListeningTestController::class, 'saveAnswer'])->name('save-answer');
        Route::get('/{test:slug}/attempt/{attempt}/complete', [ListeningTestController::class, 'complete'])->name('complete');
    });

// Reading Test Parts
use App\Http\Controllers\ReadingTestController;

Route::prefix('reading')
    ->name('reading.')
    ->group(function () {
        Route::get('/{test:slug}/start', [ReadingTestController::class, 'start'])->name('start');
        Route::get('/{test:slug}/part1/{attempt}', [ReadingTestController::class, 'part1'])->name('part1');
        Route::get('/{test:slug}/part2/{attempt}', [ReadingTestController::class, 'part2'])->name('part2');
        Route::get('/{test:slug}/part3/{attempt}', [ReadingTestController::class, 'part3'])->name('part3');
        Route::match(['get', 'post'], '/{test:slug}/attempt/{attempt}/answers', [ReadingTestController::class, 'submitAnswers'])->name('submit-answers');
        Route::get('/{test:slug}/attempt/{attempt}/complete', [ReadingTestController::class, 'complete'])->name('complete');
    });

// Writing Test Parts
use App\Http\Controllers\WritingTestController;

Route::prefix('writing')
    ->name('writing.')
    ->group(function () {
        Route::get('/{test:slug}/start', [WritingTestController::class, 'start'])->name('start');
        Route::get('/{test:slug}/task1/{attempt}', [WritingTestController::class, 'task1'])->name('task1');
        Route::get('/{test:slug}/task2/{attempt}', [WritingTestController::class, 'task2'])->name('task2');
        Route::post('/{test:slug}/attempt/{attempt}/answers', [WritingTestController::class, 'submitAnswers'])->name('submit-answers');
        Route::get('/{test:slug}/attempt/{attempt}/complete', [WritingTestController::class, 'complete'])->name('complete');
    });

// Interactive test sahifalari - yangi partlar bo'yicha testlar
Route::get('/listening-test', function () {
    // Listening test kategoriyasini topish
    $category = \App\Models\TestCategory::where('name', 'Listening')->first();
    $test = $category ? $category->activeTests->first() : null;

    if ($test) {
        return redirect()->route('listening.start', ['test' => $test->slug]);
    }

    return view('tests.listening');
})->name('tests.listening');

Route::get('/reading-test', function () {
    // Reading test kategoriyasini topish
    $category = \App\Models\TestCategory::where('name', 'Academic Reading')->first();
    $test = $category ? $category->activeTests->first() : null;

    if ($test) {
        return redirect()->route('reading.start', ['test' => $test->slug]);
    }

    return view('tests.reading');
})->name('tests.reading');

Route::get('/writing-test', function () {
    // Writing test kategoriyasini topish
    $category = \App\Models\TestCategory::where('name', 'Academic Writing')->first();
    $test = $category ? $category->activeTests->first() : null;

    if ($test) {
        return redirect()->route('writing.start', ['test' => $test->slug]);
    }

    return view('tests.writing');
})->name('tests.writing');

// Public familiarisation test routes (static data, dashboard-style)
Route::get('/tests/ielts-listening-familiarisation', function () {
    // Static test data for public viewing
    $staticTests = [
        (object)[
            'id' => 1,
            'title' => 'IELTS Listening Familiarisation Test',
            'description' => 'familiarisation test',
            'category' => (object)['name' => 'Listening'],
            'questions_count' => 40,
            'duration' => 30,
            'difficulty' => 'Beginner',
            'status' => 'Cheklanmagan'
        ],
        (object)[
            'id' => 2,
            'title' => 'Listening Sample Test 1',
            'description' => 'sample test',
            'category' => (object)['name' => 'Listening'],
            'questions_count' => 40,
            'duration' => 30,
            'difficulty' => 'Beginner',
            'status' => 'Cheklanmagan'
        ],
        (object)[
            'id' => 3,
            'title' => 'Listening Sample Test 2',
            'description' => 'sample test',
            'category' => (object)['name' => 'Listening'],
            'questions_count' => 40,
            'duration' => 30,
            'difficulty' => 'Beginner',
            'status' => 'Cheklanmagan'
        ]
    ];

    return view('tests.public-familiarisation', [
        'tests' => $staticTests,
        'category' => 'Listening',
        'pageTitle' => 'IELTS Listening Familiarisation Tests'
    ]);
})->name('tests.public.listening');

Route::get('/tests/ielts-reading-familiarisation', function () {
    // Static test data for public viewing
    $staticTests = [
        (object)[
            'id' => 4,
            'title' => 'IELTS Academic Reading Familiarisation Test',
            'description' => 'familiarisation test',
            'category' => (object)['name' => 'Academic Reading'],
            'questions_count' => 40,
            'duration' => 60,
            'difficulty' => 'Beginner',
            'status' => 'Cheklanmagan'
        ],
        (object)[
            'id' => 5,
            'title' => 'Academic Reading Sample Task 1',
            'description' => 'sample test',
            'category' => (object)['name' => 'Academic Reading'],
            'questions_count' => 40,
            'duration' => 60,
            'difficulty' => 'Beginner',
            'status' => 'Cheklanmagan'
        ],
        (object)[
            'id' => 6,
            'title' => 'Academic Reading Sample Task 2',
            'description' => 'sample test',
            'category' => (object)['name' => 'Academic Reading'],
            'questions_count' => 40,
            'duration' => 60,
            'difficulty' => 'Beginner',
            'status' => 'Cheklanmagan'
        ]
    ];

    return view('tests.public-familiarisation', [
        'tests' => $staticTests,
        'category' => 'Reading',
        'pageTitle' => 'IELTS Academic Reading Familiarisation Tests'
    ]);
})->name('tests.public.reading');

Route::get('/tests/ielts-writing-familiarisation', function () {
    // Static test data for public viewing
    $staticTests = [
        (object)[
            'id' => 7,
            'title' => 'IELTS Academic Writing Familiarisation Test',
            'description' => 'familiarisation test',
            'category' => (object)['name' => 'Academic Writing'],
            'questions_count' => 2,
            'duration' => 60,
            'difficulty' => 'Beginner',
            'status' => 'Cheklanmagan'
        ],
        (object)[
            'id' => 8,
            'title' => 'Academic Writing Sample Task 1',
            'description' => 'sample test',
            'category' => (object)['name' => 'Academic Writing'],
            'questions_count' => 1,
            'duration' => 20,
            'difficulty' => 'Beginner',
            'status' => 'Cheklanmagan'
        ],
        (object)[
            'id' => 9,
            'title' => 'Academic Writing Sample Task 2',
            'description' => 'sample test',
            'category' => (object)['name' => 'Academic Writing'],
            'questions_count' => 1,
            'duration' => 40,
            'difficulty' => 'Beginner',
            'status' => 'Cheklanmagan'
        ]
    ];

    return view('tests.public-familiarisation', [
        'tests' => $staticTests,
        'category' => 'Writing',
        'pageTitle' => 'IELTS Academic Writing Familiarisation Tests'
    ]);
})->name('tests.public.writing');

// Role-based dashboard redirect
// Test boshqaruvi (admin va o'qituvchilar uchun)
use App\Http\Controllers\TestManagementController;

Route::middleware(['auth'])->prefix('test-management')->name('test-management.')
    ->group(function () {
        Route::middleware(['role:' . User::ROLE_ADMIN . ',' . User::ROLE_TEACHER])
            ->group(function () {
                Route::get('/', [TestManagementController::class, 'index'])->name('index');
                Route::get('/create', [TestManagementController::class, 'create'])->name('create');
                Route::post('/', [TestManagementController::class, 'store'])->name('store');
                Route::get('/{test}/edit', [TestManagementController::class, 'edit'])->name('edit');
                Route::put('/{test}', [TestManagementController::class, 'update'])->name('update');
                Route::delete('/{test}', [TestManagementController::class, 'destroy'])->name('destroy');

                // Savollar boshqaruvi
                Route::get('/{test}/questions/create', [TestManagementController::class, 'createQuestions'])->name('questions.create');
                Route::get('/{test}/questions/add', [TestManagementController::class, 'addQuestion'])->name('questions.add');
                Route::post('/{test}/questions', [TestManagementController::class, 'storeQuestions'])->name('questions.store');
                Route::get('/{test}/questions/edit', [TestManagementController::class, 'editQuestions'])->name('questions.edit');
            });
    });

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
