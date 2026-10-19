<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Login Tracker (Admin Privileges page) -- admins only.
        \App\Models\AdminLoginSession::recordLogin($request->user(), $request);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        // Decide where to land before the user is logged out.
        $redirect = $request->user()?->logoutRedirectUrl() ?? route('home');

        // Login Tracker (Admin Privileges page) -- admins only.
        \App\Models\AdminLoginSession::recordLogout($request->user(), $request);

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Pages that log out via fetch() ask for JSON and navigate themselves.
        if ($request->expectsJson()) {
            return response()->json(['redirect' => $redirect]);
        }

        return redirect($redirect);
    }
}
