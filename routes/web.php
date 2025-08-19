<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LeaveController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route untuk karyawan request cuti
Route::get('/leave', [LeaveController::class, 'index'])->name('leave.index');
Route::post('/leave', [LeaveController::class, 'store'])->name('leave.store');
Route::put('/leave/{id}', [LeaveController::class, 'update'])->name('leave.update');
Route::delete('/leave/{id}', [LeaveController::class, 'destroy'])->name('leave.destroy');

// Route untuk approval cuti bawahan
Route::get('/leave/subordinate', [LeaveController::class, 'subordinate'])->name('leave.subordinate');
Route::post('/leave/approve/{id}', [LeaveController::class, 'approve'])->name('leave.approve');
Route::post('/leave/reject/{id}', [LeaveController::class, 'reject'])->name('leave.reject');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
