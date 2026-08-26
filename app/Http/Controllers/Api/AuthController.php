<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Invalid credentials.',
                ], 401);
            }

            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Decide a post-login redirect target. The shared `/dashboard`
        // route already renders admin or tenant views depending on the
        // authenticated user's relations/role, so redirect there.
        $redirect = url('/dashboard');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Logged in successfully.',
                'redirect' => $redirect,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ], 200);
        }

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logged out successfully.',
        ], 200);
    }
}