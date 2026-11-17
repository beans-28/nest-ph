<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Settings page for the logged-in admin's own account (top-bar profile
 * menu > Settings): name and email here; password goes through the
 * existing PUT /password route (Auth\PasswordController).
 */
class AdminSettingsController extends Controller
{
    public function show(Request $request): View
    {
        return view('adminsettings', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            // The email is the login, so changing it needs the current password.
            'current_password' => [Rule::requiredIf(fn () => strtolower((string) $request->input('email')) !== strtolower($user->email)), 'nullable', 'current_password'],
        ], [
            'current_password.required' => 'Enter your current password to change your email.',
            'current_password.current_password' => 'That password is incorrect.',
        ]);

        $user->name = $data['name'];
        if ($data['email'] !== $user->email) {
            $user->email = $data['email'];
            $user->email_verified_at = null;
        }
        $user->save();

        return response()->json(['message' => 'Profile saved.', 'name' => $user->name, 'email' => $user->email]);
    }

    /** Upload or replace the profile picture (shown in the top-bar menu). */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'avatar.mimes' => 'Use a JPG, PNG or WEBP image.',
            'avatar.max' => 'The picture must be 2 MB or smaller.',
        ]);

        $user = $request->user();
        $this->removeAvatarFile($user);
        $user->avatar_path = $request->file('avatar')->store('avatars', 'public');
        $user->save();

        return response()->json(['message' => 'Profile picture updated.', 'avatar_url' => $user->avatarUrl()]);
    }

    public function deleteAvatar(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->removeAvatarFile($user);
        $user->avatar_path = null;
        $user->save();

        return response()->json(['message' => 'Profile picture removed.']);
    }

    private function removeAvatarFile(User $user): void
    {
        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }
    }
}
