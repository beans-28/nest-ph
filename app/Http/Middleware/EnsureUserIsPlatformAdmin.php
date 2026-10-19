<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Only the NEST.PH team (platform_admin role) may pass. Dormitory admins
 * and tenants are refused, so the platform and each dorm stay separate.
 */
class EnsureUserIsPlatformAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role?->role_name !== 'platform_admin') {
            abort(403, 'NEST.PH platform team only.');
        }

        return $next($request);
    }
}
