<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->role || $user->role->role_name !== 'tenant') {
            return response()->json([
                'message' => 'Forbidden. Tenants only.',
            ], 403);
        }

        return $next($request);
    }
}
