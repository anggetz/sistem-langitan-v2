<?php

use App\Http\Controllers\Examples\ComponentsController;
use App\Http\Controllers\Examples\CrudController;
use App\Http\Controllers\Examples\ExamplesController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect('/examples/dashboard');
});

Route::get('/welcome', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::prefix('/examples')->group(function() {
    Route::get('/get-profile', [ExamplesController::class, 'getProfile']);
    Route::get('/dashboard', [ExamplesController::class, 'dashboard']);
    Route::resource('/crud', CrudController::class);
    Route::get('/components/accordion', [ComponentsController::class, 'accordion']);
});
