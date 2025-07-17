<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TestCategoryController;
use Illuminate\Support\Facades\Route;

// Asosiy sahifa
Route::get('/', [HomeController::class, 'index'])->name('home');

// Test kategoriyalari
Route::get('/categories', [TestCategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [TestCategoryController::class, 'show'])->name('categories.show');

// Testlar
Route::prefix('tests')->name('tests.')->group(function () {
    Route::get('/{test:slug}', [TestController::class, 'show'])->name('show');
    Route::post('/{test:slug}/start', [TestController::class, 'start'])->name('start');
    Route::get('/{test:slug}/take/{attempt}', [TestController::class, 'take'])->name('take');
    Route::post('/{test:slug}/attempt/{attempt}/answer', [TestController::class, 'submitAnswer'])->name('submit-answer');
    Route::post('/{test:slug}/attempt/{attempt}/complete', [TestController::class, 'complete'])->name('complete');
    Route::get('/{test:slug}/attempt/{attempt}/results', [TestController::class, 'results'])->name('results');
});

// Autentifikatsiya kerak bo'lgan route'lar
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
