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
    ->middleware(['auth', 'verified', \App\Http\Middleware\CheckRole::class . ':' . User::ROLE_ADMIN])
    ->controller(AdminController::class)
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
    ->middleware(['auth', 'verified', \App\Http\Middleware\CheckRole::class . ':' . User::ROLE_ADMIN . ',' . User::ROLE_TEACHER])
    ->group(function () {
        Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
        Route::get('/students', [TeacherController::class, 'students'])->name('students');
        Route::get('/results', [TeacherController::class, 'results'])->name('results');
        Route::get('/export/user/{user}', [TeacherController::class, 'exportUser'])->name('export.user');
    });

// Student routes
Route::prefix('student')
    ->name('student.')
    ->middleware(['auth', 'verified', \App\Http\Middleware\CheckRole::class . ':' . User::ROLE_ADMIN . ',' . User::ROLE_STUDENT])
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

// Public familiarisation test routes (dynamic data from database)
Route::get('/tests/ielts-listening-familiarisation', [TestController::class, 'showListeningFamiliarisation'])->name('tests.public.listening');

Route::get('/tests/ielts-reading-familiarisation', [TestController::class, 'showReadingFamiliarisation'])->name('tests.public.reading');

Route::get('/tests/ielts-writing-familiarisation', [TestController::class, 'showWritingFamiliarisation'])->name('tests.public.writing');

// Enum routes
Route::get('/enums', [App\Http\Controllers\EnumController::class, 'index'])->name('enums.index');

// Test type routes
Route::get('/tests/by-type/{type?}', [App\Http\Controllers\TestTypeController::class, 'showByType'])->name('tests.by-type');

// Role-based dashboard redirect
// Test boshqaruvi (admin va o'qituvchilar uchun)
use App\Http\Controllers\TestManagementController;

Route::middleware(['auth'])->prefix('test-management')->name('test-management.')
    ->group(function () {
        Route::middleware([\App\Http\Middleware\CheckRole::class . ':' . User::ROLE_ADMIN . ',' . User::ROLE_TEACHER])
            ->group(function () {
                Route::get('/', [TestManagementController::class, 'index'])->name('index');
                Route::get('/create', [TestManagementController::class, 'create'])->name('create');
                Route::post('/', [TestManagementController::class, 'store'])->name('store');
                Route::get('/{test}/edit', [TestManagementController::class, 'edit'])->name('edit')->where('test', '[0-9]+');
                Route::put('/{test}', [TestManagementController::class, 'update'])->name('update')->where('test', '[0-9]+');
                Route::delete('/{test}', [TestManagementController::class, 'destroy'])->name('destroy')->where('test', '[0-9]+');

                // Savollar boshqaruvi
                Route::get('/{test}/questions/create', [TestManagementController::class, 'createQuestions'])->name('questions.create')->where('test', '[0-9]+');
                Route::get('/{test}/questions/add', [TestManagementController::class, 'addQuestion'])->name('questions.add')->where('test', '[0-9]+');
                Route::post('/{test}/questions', [TestManagementController::class, 'storeQuestions'])->name('questions.store')->where('test', '[0-9]+');
                Route::get('/{test}/questions/edit', [TestManagementController::class, 'editQuestions'])->name('questions.edit')->where('test', '[0-9]+');
                
                // Enum jadvalini ko'rsatish
                Route::get('/enums', [TestManagementController::class, 'showEnums'])->name('enums');
            });
    });

Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboardRedirect'])->middleware(['auth', 'verified'])->name('dashboard');

// Direct admin access (development only)
Route::get('/admin-direct', [App\Http\Controllers\AdminController::class, 'adminDirect'])->name('admin.direct');
