<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Use Case Report Table 43, precondition: once Move-Out has been
 * recorded, a former tenant's only remaining reason to log in is to see
 * the move-out confirmation and optionally leave a review -- not to keep
 * browsing Billing, Tickets, or Delinquency with stale data from a
 * closed stay.
 *
 * Applied to the tenant route group, alongside movein.check and
 * delinquency.check. If the logged-in tenant's status is 'inactive' and
 * they are NOT blacklisted (an evicted tenant is delinquency.check's
 * problem, not this one -- Table 28's blacklist is permanent and takes
 * priority), every request gets redirected to the Profile/Account page
 * except that page itself and the review-submission endpoint.
 */
class RestrictMovedOutTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $tenant = Tenant::where('user_id', $user->id)->first();

        if (! $tenant || $tenant->status !== 'inactive' || $tenant->is_blacklisted) {
            return $next($request);
        }

        $allowedRouteNames = [
            'tenant.moveout',
            'reviews.store',
        ];

        if (! in_array($request->route()?->getName(), $allowedRouteNames, true)) {
            return redirect()->route('tenant.moveout');
        }

        return $next($request);
    }
}