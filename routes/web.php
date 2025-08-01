<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AudioController;
use App\Http\Controllers\EnumController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListeningTestController;
use App\Http\Controllers\ReadingTestController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TestCategoryController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TestManagementController;
use App\Http\Controllers\TestResultController;
use App\Http\Controllers\TestTypeController;
use App\Http\Controllers\WritingTestController;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ==========================================
// PUBLIC ROUTES
// ==========================================

// Home page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Static pages
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/help', [HomeController::class, 'help'])->name('help');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Test information pages
Route::get('/ielts-info', [HomeController::class, 'ieltsInfo'])->name('ielts.info');
Route::get('/test-format', [HomeController::class, 'testFormat'])->name('test.format');

// Public demo/familiarisation tests (no auth required)
Route::prefix('demo')->name('demo.')->group(function () {
    Route::get('/listening', [TestController::class, 'showListeningFamiliarisation'])->name('listening');
    Route::get('/reading', [TestController::class, 'showReadingFamiliarisation'])->name('reading');
    Route::get('/writing', [TestController::class, 'showWritingFamiliarisation'])->name('writing');
});

// Test categories (public browsing)
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [TestCategoryController::class, 'index'])->name('index');
    Route::get('/{category:slug}', [TestCategoryController::class, 'show'])->name('show');
});

// Test types (public)
Route::get('/tests/by-type/{type?}', [TestTypeController::class, 'showByType'])->name('tests.by-type');

// Enum data (public)
Route::get('/enums', [EnumController::class, 'index'])->name('enums.index');

// ==========================================
// AUTHENTICATION & DASHBOARD
// ==========================================

// Role-based dashboard redirect
Route::get('/dashboard', [AdminController::class, 'dashboardRedirect'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// ==========================================
// ADMIN ROUTES
// ==========================================
// Direct access to admin panel
Route::get('/admin', function() {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified', 'admin'])->name('admin.direct');

Route::prefix('admin')->name('admin.')
    ->middleware(['auth', 'verified', 'admin'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // User management
        Route::controller(AdminController::class)->group(function () {
            Route::get('/users', 'index')->name('users');
            Route::get('/users/list', 'index')->name('users.index');
            Route::post('/users', 'store')->name('users.store');
            Route::put('/users/{user}', 'update')->name('users.update');
            Route::delete('/users/{user}', 'destroy')->name('users.destroy');
            Route::get('/users/{user}/export', 'exportUser')->name('users.export');
        });

        // Test overview
        Route::get('/tests', [AdminController::class, 'tests'])->name('tests');

        // System settings
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    });

// ==========================================
// TEACHER ROUTES
// ==========================================
Route::prefix('teacher')->name('teacher.')
    ->middleware(['auth', 'verified'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');

        // Students management
        Route::get('/students', [TeacherController::class, 'students'])->name('students');
        Route::get('/students/{user}/export', [TeacherController::class, 'exportUser'])->name('students.export');

        // Test results management
        Route::controller(TestResultController::class)->group(function () {
            Route::get('/results', 'index')->name('results.index');
            Route::get('/results/{attempt}', 'show')->name('results.show');
            Route::get('/results/test/{test}/statistics', 'statistics')->name('results.statistics');
            Route::get('/results/export', 'export')->name('results.export');
            Route::post('/results/{attempt}/grade', 'grade')->name('results.grade');
        });

        // Class management
        Route::get('/classes', [TeacherController::class, 'classes'])->name('classes');
        Route::post('/classes', [TeacherController::class, 'createClass'])->name('classes.store');
    });

// ==========================================
// STUDENT ROUTES
// ==========================================
Route::prefix('student')->name('student.')
    ->middleware(['auth', 'verified'])

    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');

        // Student results
        Route::get('/results', [StudentController::class, 'results'])->name('results');
        Route::get('/progress', [StudentController::class, 'progress'])->name('progress');

        // Available tests
        Route::controller(TestController::class)->group(function () {
            Route::get('/tests', 'index')->name('tests.index');
            Route::get('/tests/{test:slug}', 'show')->name('tests.show');
            Route::post('/tests/{test:slug}/start', 'start')->name('tests.start');
            Route::get('/tests/{test:slug}/attempt/{attempt}', 'take')->name('tests.take');
            Route::get('/tests/{test:slug}/attempt/{attempt}/result', 'result')->name('tests.result');
            Route::get('/test-history', 'history')->name('tests.history');
        });

        // AJAX routes for test taking
        Route::post('/tests/{test:slug}/attempt/{attempt}/save-answer', [TestController::class, 'saveAnswer'])->name('tests.save-answer');
        Route::post('/tests/{test:slug}/attempt/{attempt}/submit', [TestController::class, 'submit'])->name('tests.submit');
        Route::post('/tests/{test:slug}/attempt/{attempt}/auto-save', [TestController::class, 'autoSave'])->name('tests.auto-save');
    });

// ==========================================
// TEST MANAGEMENT (Admin & Teacher)
// ==========================================
Route::prefix('test-management')->name('test-management.')
    ->middleware(['auth', 'verified'])
    ->group(function () {

        // Main test CRUD
        Route::controller(TestManagementController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{test}/edit', 'edit')->name('edit');
            Route::put('/{test}', 'update')->name('update');
            Route::delete('/{test}', 'destroy')->name('destroy');

            // Test operations
            Route::get('/{test}/preview', 'preview')->name('preview');
            Route::post('/{test}/duplicate', 'duplicate')->name('duplicate');
            Route::post('/{test}/publish', 'publish')->name('publish');
            Route::post('/{test}/unpublish', 'unpublish')->name('unpublish');

            // Question management
            Route::get('/{test}/questions/create', 'createQuestions')->name('questions.create');
            Route::post('/{test}/questions', 'storeQuestions')->name('questions.store');
            Route::get('/{test}/questions/add', 'addQuestion')->name('questions.add');
            Route::get('/{test}/questions/{question}/edit', 'editQuestion')->name('questions.edit');
            Route::put('/{test}/questions/{question}', 'updateQuestion')->name('questions.update');
            Route::delete('/{test}/questions/{question}', 'deleteQuestion')->name('questions.delete');

            // Bulk operations
            Route::post('/{test}/questions/import', 'importQuestions')->name('questions.import');
            Route::get('/{test}/questions/export', 'exportQuestions')->name('questions.export');

            // Test results
            Route::get('/{test}/results', 'results')->name('results');
            Route::get('/{test}/analytics', 'analytics')->name('analytics');

            // Settings
            Route::get('/enums', 'showEnums')->name('enums');
        });
    });

// ==========================================
// SPECIFIC TEST TYPE ROUTES
// ==========================================

// Listening Tests
Route::prefix('listening')->name('listening.')
    ->middleware(['auth', 'verified'])
    ->controller(ListeningTestController::class)
    ->group(function () {
        Route::get('/{test:slug}/start', 'start')->name('start');
        Route::get('/{test:slug}/instructions/{attempt}', 'instructions')->name('instructions');
        Route::get('/{test:slug}/part1/{attempt}', 'part1')->name('part1');
        Route::get('/{test:slug}/part2/{attempt}', 'part2')->name('part2');
        Route::get('/{test:slug}/part3/{attempt}', 'part3')->name('part3');
        Route::get('/{test:slug}/part4/{attempt}', 'part4')->name('part4');
        Route::get('/{test:slug}/unified/{attempt}', 'unifiedTest')->name('unified');
        Route::get('/{test:slug}/attempt/{attempt}/complete', 'complete')->name('complete');

        // AJAX endpoints
        Route::post('/{test:slug}/attempt/{attempt}/submit', 'submitAnswers')->name('submit-answers');
        Route::post('/{test:slug}/attempt/{attempt}/save', 'saveAnswer')->name('save-answer');
        Route::get('/{test:slug}/attempt/{attempt}/time-remaining', 'getTimeRemaining')->name('time-remaining');
    });

// Reading Tests
Route::prefix('reading')->name('reading.')
    ->middleware(['auth', 'verified'])
    ->controller(ReadingTestController::class)
    ->group(function () {
        Route::get('/{test:slug}/start', 'start')->name('start');
        Route::get('/{test:slug}/part1/{attemptCode}', 'part1')->name('part1');
        Route::get('/{test:slug}/part2/{attemptCode}', 'part2')->name('part2');
        Route::get('/{test:slug}/part3/{attemptCode}', 'part3')->name('part3');
        Route::get('/{test:slug}/unified/{attemptCode}', 'unifiedTest')->name('unified');
        Route::get('/{test:slug}/attempt/{attemptCode}/complete', 'complete')->name('complete');

        // AJAX endpoints
        Route::match(['get', 'post'], '/{test:slug}/attempt/{attemptCode}/answers', 'submitAnswers')->name('submit-answers');
        Route::post('/{test:slug}/attempt/{attemptCode}/save', 'saveAnswer')->name('save-answer');
        Route::get('/{test:slug}/attempt/{attemptCode}/time', 'getTimeRemaining')->name('time-remaining');
    });

// Writing Tests
Route::prefix('writing')->name('writing.')
    ->middleware(['auth', 'verified'])
    ->controller(WritingTestController::class)
    ->group(function () {
        Route::get('/{test:slug}/start', 'start')->name('start');
        Route::get('/{test:slug}/instructions/{attempt}', 'instructions')->name('instructions');
        Route::get('/{test:slug}/task1/{attempt}', 'task1')->name('task1');
        Route::get('/{test:slug}/task2/{attempt}', 'task2')->name('task2');
        Route::get('/{test:slug}/attempt/{attempt}/complete', 'complete')->name('complete');

        // AJAX endpoints
        Route::post('/{test:slug}/attempt/{attempt}/answers', 'submitAnswers')->name('submit-answers');
        Route::post('/{test:slug}/attempt/{attempt}/save', 'saveAnswer')->name('save-answer');
        Route::post('/{test:slug}/attempt/{attempt}/auto-save', 'autoSave')->name('auto-save');
    });

// ==========================================
// QUICK ACCESS ROUTES
// ==========================================

// Interactive test shortcuts
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/listening-test', function () {
        $category = Category::where('name', 'Listening')->first();
        $test = $category?->activeTests()->first();

        return $test
            ? redirect()->route('listening.start', ['test' => $test->slug])
            : redirect()->route('student.tests.index')->with('error', 'No listening tests available');
    })->name('tests.listening');

    Route::get('/reading-test', function () {
        $category = Category::where('name', 'Academic Reading')->first();
        $test = $category?->activeTests()->first();

        return $test
            ? redirect()->route('reading.start', ['test' => $test->slug])
            : redirect()->route('student.tests.index')->with('error', 'No reading tests available');
    })->name('tests.reading');

    Route::get('/writing-test', function () {
        $category = Category::where('name', 'Academic Writing')->first();
        $test = $category?->activeTests()->first();

        return $test
            ? redirect()->route('writing.start', ['test' => $test->slug])
            : redirect()->route('student.tests.index')->with('error', 'No writing tests available');
    })->name('tests.writing');
});

// ==========================================
// AUDIO & FILE HANDLING
// ==========================================

// Audio upload routes - Admin & Teacher only
Route::middleware(['auth', 'verified'])->group(function () {

    // Main audio upload (supports both normal and chunked)
    Route::post('/audio/upload', [AudioController::class, 'upload'])
        ->name('audio.upload');

    // Chunked upload specific endpoints
    Route::post('/audio/upload/chunk', [AudioController::class, 'uploadChunk'])
        ->name('audio.upload.chunk');

    Route::post('/audio/upload/finalize', [AudioController::class, 'finalizeUpload'])
        ->name('audio.upload.finalize');

    // Audio file management
    Route::get('/audio/list', [AudioController::class, 'list'])
        ->name('audio.list');

    Route::delete('/audio/delete', [AudioController::class, 'delete'])
        ->name('audio.delete');

    // Audio streaming - for authenticated users
    Route::get('/audio/stream/{filename}', [AudioController::class, 'stream'])
        ->where('filename', '[a-zA-Z0-9._-]+')
        ->name('audio.stream');

    // Alternative streaming route (if you need path support)
    Route::get('/audio/{path}', [AudioController::class, 'stream'])
        ->where('path', '.*')
        ->name('audio.stream.path');
});



// File downloads (keeping your existing route)
Route::get('/download/{type}/{id}', [HomeController::class, 'download'])
    ->middleware(['auth', 'verified'])
    ->name('download.file');


// ==========================================
// API ROUTES
// ==========================================
Route::prefix('api')->name('api.')
    ->middleware(['auth', 'verified'])
    ->group(function () {

        // Public API endpoints (all authenticated users)
        Route::get('/test-categories', [TestCategoryController::class, 'getCategories'])->name('test-categories');
        Route::get('/user/profile', [StudentController::class, 'getProfile'])->name('user.profile');

        // Test taking API
        Route::prefix('tests')->name('tests.')->group(function () {
            Route::get('/{test:slug}', [TestController::class, 'show'])->name('show');
            Route::get('/{test:slug}/questions', [TestController::class, 'getQuestions'])->name('questions');
            Route::post('/{test:slug}/attempt/{attempt}/progress', [TestController::class, 'saveProgress'])->name('save-progress');
            Route::get('/{test:slug}/attempt/{attempt}/status', [TestController::class, 'getStatus'])->name('status');
        });

        // Admin & Teacher only API endpoints
        Route::middleware(['auth', 'verified'])->group(function () {
            // Test management API
            Route::prefix('test-management')->name('test-management.')->group(function () {
                Route::get('/tests/{test}/questions', [TestManagementController::class, 'getQuestions'])->name('questions');
                Route::post('/tests/{test}/questions/reorder', [TestManagementController::class, 'reorderQuestions'])->name('questions.reorder');
                Route::get('/tests/{test}/statistics', [TestManagementController::class, 'getStatistics'])->name('statistics');
            });

            // Audio API
            Route::prefix('audio')->name('audio.')->group(function () {
                Route::get('/upload-status/{id}', [AudioController::class, 'uploadStatus'])->name('upload-status');
                Route::post('/process', [AudioController::class, 'processAudio'])->name('process');
            });

            // Analytics API
            Route::prefix('analytics')->name('analytics.')->group(function () {
                Route::get('/dashboard', [AdminController::class, 'getDashboardData'])->name('dashboard');
                Route::get('/test/{test}/performance', [TestResultController::class, 'getPerformanceData'])->name('test.performance');
            });
        });
    });

// ==========================================
// FALLBACK & ERROR ROUTES
// ==========================================

// Handle 404 for test routes specifically
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

