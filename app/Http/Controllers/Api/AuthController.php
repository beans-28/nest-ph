<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;

    private const ATTEMPT_WINDOW_SECONDS = 300; // failed attempts must happen within 5 minutes of each other to count toward a lock

    private const LOCKOUT_SECONDS = 900; // 15 minutes, once locked

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $base = $this->throttleKeyBase($request);
        $lockKey = "login-lock:{$base}";
        $attemptsKey = "login-attempts:{$base}";

        if (RateLimiter::tooManyAttempts($lockKey, 1)) {
            return response()->json([
                'message' => 'Too many login attempts.',
                'locked' => true,
                'retry_after' => RateLimiter::availableIn($lockKey),
            ], 429);
        }

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($attemptsKey, self::ATTEMPT_WINDOW_SECONDS);

            if (RateLimiter::attempts($attemptsKey) >= self::MAX_ATTEMPTS) {
                RateLimiter::clear($attemptsKey);
                RateLimiter::hit($lockKey, self::LOCKOUT_SECONDS);

                return response()->json([
                    'message' => 'Too many login attempts.',
                    'locked' => true,
                    'retry_after' => self::LOCKOUT_SECONDS,
                ], 429);
            }

            return response()->json([
                'message' => 'Incorrect password.',
                'attempts' => RateLimiter::attempts($attemptsKey),
                'max_attempts' => self::MAX_ATTEMPTS,
            ], 401);
        }

        RateLimiter::clear($attemptsKey);
        RateLimiter::clear($lockKey);

        $request->session()->regenerate();

        $user = Auth::user();

        return response()->json([
            'message' => 'Logged in successfully.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 200);
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

    /**
     * Keyed by email + IP, so one user mistyping their password doesn't
     * lock out anyone else, and repeatedly trying different emails from
     * the same IP doesn't dodge the limiter either.
     */
    private function throttleKeyBase(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email', ''))).'|'.$request->ip();
    }
}