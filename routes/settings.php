<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SettingController;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance');

    // Resource routes for profile data
    Route::resource('emergency-contacts', App\Http\Controllers\EmergencyContactController::class)->except(['create', 'edit']);
    Route::resource('job-histories', App\Http\Controllers\JobHistoryController::class)->except(['create', 'edit']);
    Route::resource('trainings', App\Http\Controllers\TrainingController::class)->except(['create', 'edit']);
    Route::resource('certifications', App\Http\Controllers\CertificationController::class)->except(['create', 'edit']);
    Route::resource('work-goals', App\Http\Controllers\WorkGoalController::class)->except(['create', 'edit']);
    Route::resource('career-plans', App\Http\Controllers\CareerPlanController::class)->except(['create', 'edit']);
});

// Settings API routes
Route::get('/settings', [SettingController::class, 'index']);
Route::put('/settings', [SettingController::class, 'update']);
