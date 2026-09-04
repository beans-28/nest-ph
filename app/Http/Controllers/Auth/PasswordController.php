<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     *
     * Bug fix: this originally always redirected on success (Breeze's
     * default, meant for a traditional Blade form submit). But
     * tenantaccount.blade.php talks to this route via fetch() expecting
     * JSON back -- the mismatch meant a *successful* password change was
     * silently misreported to the tenant as a failure, because the JS
     * never got the JSON response shape it was looking for.
     */
    public function update(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Password updated successfully.']);
        }

        return back()->with('status', 'password-updated');
    }
}