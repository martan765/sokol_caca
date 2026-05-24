<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\TrainingController as AdminTrainingController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/trainings', [TrainingController::class, 'index'])->name('trainings.index');
    Route::get('/games', [GameController::class, 'index'])->name('games.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/treninky/vytvorit', [AdminTrainingController::class, 'create'])->name('admin.trainings.create');
    Route::post('/admin/treninky', [AdminTrainingController::class, 'store'])->name('admin.trainings.store');
    Route::delete('/trainings/{training}', [App\Http\Controllers\TrainingController::class, 'destroy'])->name('trainings.destroy');

    Route::get('/admin/zapasy/vytvorit', [GameController::class, 'create'])->name('admin.games.create');
    Route::post('/admin/zapasy', [GameController::class, 'store'])->name('admin.games.store');
    Route::delete('/games/{game}', [GameController::class, 'destroy'])->name('games.destroy');
});

require __DIR__.'/auth.php';
