<?php

namespace App\Http\Controllers;

use App\Models\DormitoryProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Use Case Report Table 43 — Submit Review after Move-out.
 *
 * Dedicated, sidebar-less takeover page (Figma: "Move-out Review
 * Prompt", node 1009:4715). This is now the ONLY page a moved-out
 * (status: inactive, not blacklisted) tenant can reach — enforced by
 * RestrictMovedOutTenant everywhere else in the tenant portal.
 */
class TenantMoveOutController extends Controller
{
    public function show(Request $request)
    {
        $tenant = $request->attributes->get('tenant') ?? $request->user()->tenant;

        $moveOutContract = $tenant->contracts()
            ->where('status', 'terminated')
            ->latest('terminated_at')
            ->first();

        $lengthOfStay = null;

        if ($moveOutContract && $moveOutContract->start_date) {
            $start = Carbon::parse($moveOutContract->start_date);
            $end = $moveOutContract->terminated_at
                ? Carbon::parse($moveOutContract->terminated_at)
                : now();

            $diff = $start->diff($end);
            $months = ($diff->y * 12) + $diff->m;

            $parts = [];
            if ($months > 0) {
                $parts[] = $months . ' month' . ($months === 1 ? '' : 's');
            }
            if ($diff->d > 0 || empty($parts)) {
                $parts[] = $diff->d . ' day' . ($diff->d === 1 ? '' : 's');
            }
            $lengthOfStay = implode(', ', $parts);
        }

        $dormProfile = DormitoryProfile::current();

        return view('tenantmoveout', [
            'tenant' => $tenant,
            'moveOutContract' => $moveOutContract,
            'lengthOfStay' => $lengthOfStay,
            'dormContactEmail' => $dormProfile->contact_email,
            'dormContactNumber' => $dormProfile->contact_number,
        ]);
    }
}