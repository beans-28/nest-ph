<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Use Case Report — Pay Move-In Fees, step 1.3: "Restrict access to the
 * billing module only — all other system features remain inaccessible
 * until the move-in fee is verified."
 *
 * Applied to any tenant-facing route. If the logged-in user is a tenant
 * whose account is still Pending Move-In Payment, every request gets
 * redirected to the move-in flow except the move-in pages themselves
 * (and logout, which must always work).
 */
class RedirectPendingMoveInTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $tenant = Tenant::where('user_id', $user->id)->first();

        // Not a tenant (e.g. an admin hitting a shared route) — nothing to
        // restrict, let it through untouched.
        if (! $tenant) {
            return $next($request);
        }

        $allowedRouteNames = [
            'tenant.movein.welcome',
            'tenant.movein.payment-type',
            'tenant.movein.payment-type.store',
            'tenant.movein.payment-method',
            'tenant.movein.payment-method.store',
            'tenant.movein.payment',
            'tenant.billing.payment-proof',
            'logout',
        ];

        if (
            $tenant->status === 'pending_move_in_payment'
            && ! in_array($request->route()?->getName(), $allowedRouteNames, true)
        ) {
            return redirect()->route('tenant.movein.welcome');
        }

        return $next($request);
    }
}