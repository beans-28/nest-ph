<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsTenant
{
    /**
     * Allows through only authenticated users who have a linked tenant record.
     *
     * Having a 'tenant' role isn't enough on its own — the request needs an
     * actual tenants row to scope data against, otherwise every downstream
     * "only your own records" check has nothing to compare to.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $tenant = $user->tenant;

        if (! $tenant) {
            return response()->json([
                'message' => 'This account is not linked to a tenant record.',
            ], 403);
        }

        // Make the resolved tenant available to controllers without a re-query.
        $request->attributes->set('tenant', $tenant);

        return $next($request);
    }
}
