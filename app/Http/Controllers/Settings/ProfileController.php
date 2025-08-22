<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Department;
use App\Models\Position;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        $user = \App\Models\User::with([
            'emergencyContacts',
            'jobHistories',
            'trainings',
            'certifications',
            'workGoals',
            'careerPlans',
            'supervisedEmployees.employee',
        ])->findOrFail($request->user()->id);
        return Inertia::render('settings/Profile', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => $user,
            'old' => session('_old_input', []),
            'departments' => Department::all(), // atau Department::paginate(10)
            'positions' => Position::all(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Fill all fields except id, created_at, updated_at
    $user?->fill(collect($data)->except(['id', 'created_at', 'updated_at'])->toArray());

        if ($user && array_key_exists('email', $data) && $user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user?->save();

        return redirect()->route('profile.edit');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user?->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome');
    }
}
