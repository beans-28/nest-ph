<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Use Case Report Table 25 (Stage 3: Portal Restriction), postcondition:
 * "Tenant portal access is restricted to the payment link only."
 *
 * Applied to the tenant route group, right after movein.check. If the
 * logged-in tenant's portal_restricted flag is true, every request gets
 * redirected to the billing page except:
 *   - the billing page itself (and its my/billing/* data + payment-proof
 *     routes, which aren't individually named, so we also allow by path)
 *   - the tenant's own delinquency status page, so they can see WHY
 *     they're restricted
 *   - logout, which must always work
 *
 * Deliberately does NOT touch the move-in flow -- movein.check already
 * runs before this one and takes priority for a pending_move_in_payment
 * tenant.
 */
class RestrictDelinquentTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $tenant = Tenant::where('user_id', $user->id)->first();

        if (! $tenant || ! $tenant->portal_restricted) {
            return $next($request);
        }

        $allowedRouteNames = [
            'tenant.billing',
            'tenant.delinquency',
            'tenant.billing.payment-proof',
            'logout',
        ];

        $routeName = $request->route()?->getName();

        $isAllowedByName = in_array($routeName, $allowedRouteNames, true);
        $isAllowedByPath = $request->is('my/billing*') || $request->is('my/delinquency*');

        if (! $isAllowedByName && ! $isAllowedByPath) {
            // A blacklisted tenant's default landing spot should be their
            // status page, not Billing -- /billing itself now shows a
            // dead-end takeover for them (Table 28: blacklisting is
            // permanent, there's nothing left to pay toward), so bouncing
            // them there first would just be a page they immediately have
            // to click away from.
            return redirect()->route($tenant->is_blacklisted ? 'tenant.delinquency' : 'tenant.billing');
        }

        return $next($request);
    }
}