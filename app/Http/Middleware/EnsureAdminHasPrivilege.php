<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enforces the privileges ticked on the Admin Privileges screen.
 *
 * Every admin URL is matched by its first path segment (the "/api" prefix
 * is ignored) against AREAS below. The admin needs that privilege, or
 * manage_users (the Owner, who can do everything). A URL that isn't listed
 * is denied, so a newly added admin page stays locked until someone puts
 * it in this list on purpose.
 */
class EnsureAdminHasPrivilege
{
    /** First URL segment => privilege needed. null = any admin may use it. */
    public const AREAS = [
        // Rooms
        'admin' => null, // /admin/notifications (add-floor is handled below)
        'vacancy-monitoring' => 'manage_rooms',
        'vacancy' => 'manage_rooms',
        'vr-management' => 'manage_rooms',
        'vr-tours' => 'manage_rooms',

        // Tenants
        'tenant-manager' => 'manage_tenants',
        'applications' => 'manage_tenants',
        'inquiries' => 'manage_tenants',
        'tickets' => 'manage_tenants',
        'announcements' => 'manage_tenants',

        // Contracts
        'lease-contracts' => 'manage_contracts',

        // Billing
        'payments' => 'manage_billing',
        'billing' => 'manage_billing',
        'penalties' => 'manage_billing',
        'damages' => 'manage_billing',
        'delinquency' => 'manage_billing',
        'delinquency-testing' => 'manage_billing',

        // Reports
        'reports' => 'view_reports',
        'activity-log' => 'view_reports',

        // Owner only
        'dormitory-profile' => 'manage_users',

        // Dev/demo testing tools on several pages
        'demo-tools' => null,
        'settings' => null, // every admin manages their own account
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $segments = $request->segments();
        if (($segments[0] ?? null) === 'api') {
            array_shift($segments);
        }

        if (! self::canOpen($user, $segments)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "Forbidden. You don't have the privilege for this section.",
                ], 403);
            }

            abort(403, "You don't have the privilege to access this page.");
        }

        return $next($request);
    }

    /**
     * Can $user open this admin path? Accepts '/payments' or ['payments'].
     * Also used to drop notification-bell items the admin can't open.
     */
    public static function canOpen($user, string|array $path): bool
    {
        $segments = is_array($path)
            ? $path
            : array_values(array_filter(explode('/', parse_url($path, PHP_URL_PATH) ?? '')));
        $first = $segments[0] ?? '';
        $privilege = self::privilegeFor($first, $segments);

        return $user && (
            $privilege === null
                ? array_key_exists($first, self::AREAS)
                : $user->hasPrivilege($privilege)
        );
    }

    private static function privilegeFor(string $first, array $segments): ?string
    {
        // /admin/add-floor is the Vacancy Monitor page.
        if ($first === 'admin' && ($segments[1] ?? null) === 'add-floor') {
            return 'manage_rooms';
        }

        // /tenants/{id}/active-lease vs /tenants/{id}/running-total
        if ($first === 'tenants') {
            return ($segments[2] ?? null) === 'active-lease' ? 'manage_contracts' : 'manage_billing';
        }

        return self::AREAS[$first] ?? null;
    }

    /** Which privilege a sidebar route name needs (null = always shown). */
    public static function forRoute(string $routeName): ?string
    {
        return [
            'tenant-manager.index' => 'manage_tenants',
            'applications.index' => 'manage_tenants',
            'inquiries.index' => 'manage_tenants',
            'tickets.index' => 'manage_tenants',
            'payments.index' => 'manage_billing',
            'delinquency.index' => 'manage_billing',
            'admin.addfloor' => 'manage_rooms',
            'vacancy.index' => 'manage_rooms',
            'vr.index' => 'manage_rooms',
            'contracts.index' => 'manage_contracts',
            'reports.index' => 'view_reports',
            'activity-log.index' => 'view_reports',
            'admin-privileges.index' => 'manage_users',
            'dormitory-profile.index' => 'manage_users',
        ][$routeName] ?? null;
    }
}
