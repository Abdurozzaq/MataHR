<?php

use Illuminate\Foundation\Application;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\WorkScheduleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SupervisorDashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaveRequestController;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/reverse-geocode', [LocationController::class, 'reverseGeocode']);

Route::middleware(['auth'])->group(function () {
    // Route untuk karyawan request cuti
    Route::get('/leave', [LeaveController::class, 'index'])->name('leave.index');
    Route::post('/leave', [LeaveController::class, 'store'])->name('leave.store');
    Route::put('/leave/{id}', [LeaveController::class, 'update'])->name('leave.update');
    Route::delete('/leave/{id}', [LeaveController::class, 'destroy'])->name('leave.destroy');

    // Route untuk approval cuti bawahan
    Route::get('/leave/subordinate', [LeaveController::class, 'subordinate'])->name('leave.subordinate');
    Route::post('/leave/approve/{id}', [LeaveController::class, 'approve'])->name('leave.approve');
    Route::post('/leave/reject/{id}', [LeaveController::class, 'reject'])->name('leave.reject');

    Route::get('/absent', [AttendanceController::class, 'today'])->name('absent');
    Route::post('/attendances/clock-in', [AttendanceController::class, 'clockIn']);
    Route::post('/attendances/clock-out', [AttendanceController::class, 'clockOut']);

    Route::resource('work-schedule', WorkScheduleController::class);

    // Route dashboard absensi supervisor
    Route::get('/supervisor/dashboard', [SupervisorDashboardController::class, 'index'])
        ->name('supervisor.attendance.dashboard');

        // Department CRUD
    Route::get('/department', [DepartmentController::class, 'index'])->name('department.index');
    Route::post('/department', [DepartmentController::class, 'store'])->name('department.store');
    Route::put('/department/{id}', [DepartmentController::class, 'update'])->name('department.update');
    Route::delete('/department/{id}', [DepartmentController::class, 'destroy'])->name('department.destroy');

    // Position CRUD
    Route::get('/position', [PositionController::class, 'index'])->name('position.index');
    Route::post('/position', [PositionController::class, 'store'])->name('position.store');
    Route::put('/position/{id}', [PositionController::class, 'update'])->name('position.update');
    Route::delete('/position/{id}', [PositionController::class, 'destroy'])->name('position.destroy');

    // User CRUD
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');

    Route::get('/global-setting', function () {
        return inertia('globalSetting/index');
    })->name('global-setting.index');

    Route::get('/dashboard-data', [DashboardController::class, 'index']);
    Route::get('/leave-requests/me', [LeaveRequestController::class, 'me']);
});



require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
