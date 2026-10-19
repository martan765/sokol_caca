<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AttendanceStatusController as AdminAttendanceStatusController;
use App\Http\Controllers\Admin\GameController as AdminGameController;
use App\Http\Controllers\Admin\GameTypeController as AdminGameTypeController;
use App\Http\Controllers\Admin\TrainingController as AdminTrainingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WeatherController;
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

    Route::get('/weather', [WeatherController::class, 'index'])->name('weather.index');
});

// Area "Admin" – pouze trenér (admin), oprávnění řeší AdminMiddleware.
// resource() vytvoří index, create, store, edit, update a destroy.
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('trainings', AdminTrainingController::class)->except('show');
    Route::get('trainings/{training}/attendance', [AdminTrainingController::class, 'attendance'])->name('trainings.attendance');
    Route::put('trainings/{training}/attendance', [AdminTrainingController::class, 'updateAttendance'])->name('trainings.attendance.update');

    Route::resource('games', AdminGameController::class)->except('show');
    Route::get('games/{game}/attendance', [AdminGameController::class, 'attendance'])->name('games.attendance');
    Route::put('games/{game}/attendance', [AdminGameController::class, 'updateAttendance'])->name('games.attendance.update');

    Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update', 'destroy']);
    Route::resource('game-types', AdminGameTypeController::class)->except('show');
    Route::resource('attendance-statuses', AdminAttendanceStatusController::class)->only(['index', 'edit', 'update']);
});

require __DIR__.'/auth.php';
