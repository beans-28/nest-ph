<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Use Case — Admin Privileges (DT&TM Week 2, Wed Aug 12): "This screen is
 * for the Dormitory Owner only — Admins can't grant themselves privileges."
 * There is no separate "owner" role in the schema (see
 * RolesAndTestUsersSeeder) — the Dormitory Owner is simply the admin who
 * holds the manage_users privilege, so that's the gate checked here.
 */
class EnsureCanManagePrivileges
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $canManage = $user
            && $user->role?->role_name === 'admin'
            && $user->privileges()->where('privilege_name', 'manage_users')->exists();

        if (! $canManage) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Forbidden. Only an admin with Manage Admin Users privilege can do this.',
                ], 403);
            }

            abort(403, 'Only an admin with Manage Admin Users privilege can access this page.');
        }

        return $next($request);
    }
}