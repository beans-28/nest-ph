<?php

namespace App\Http\Controllers;

use App\Models\BillingStatement;
use App\Models\EscalationLog;
use App\Models\Tenant;
use App\Services\EscalationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Testing/demo-only tool for the Delinquency Escalation ladder (Tables
 * 23-28). NOT one of the manuscript's use cases -- lets the team push any
 * tenant to any stage on demand for consultations/walkthroughs, instead
 * of hand-editing due dates in tinker every time.
 *
 * Reuses EscalationService::processBillingStatement() under the hood, so
 * what gets demoed (including real SMS sends) is the genuine stage
 * behavior, not a fake shortcut. Every route here is also hard-blocked
 * outside local environments as a safety net (see escalate() below), on
 * top of the Blade panel already hiding itself in production.
 */
class DelinquencyTestingController extends Controller
{
    /** Days-overdue value that lands a bill exactly on each stage. */
    private const STAGE_DAYS = [
        1 => 1,
        2 => 7,
        3 => 8,
        4 => 9,
        5 => 10,
        6 => 11,
    ];

    /** All active tenants, tagged with their current stage (0 = not delinquent). */
    public function index(): JsonResponse
    {
        $tenants = Tenant::where('status', 'active')
            ->with('escalationLogs')
            ->orderBy('full_name')
            ->get()
            ->map(function (Tenant $tenant) {
                $stage = (int) ($tenant->escalationLogs
                    ->reject(fn ($log) => str_starts_with((string) $log->action_type, 'admin_override_'))
                    ->max('stage') ?? 0);

                return [
                    'id' => $tenant->id,
                    'name' => $tenant->full_name,
                    'stage' => $stage,
                    'is_blacklisted' => (bool) $tenant->is_blacklisted,
                ];
            })
            ->values();

        return response()->json(['tenants' => $tenants]);
    }

    /** Push (stage 1-6) or reset (stage 0) one tenant, real SMS included. */
    public function escalate(Request $request, Tenant $tenant, EscalationService $escalation): JsonResponse
    {
        if (! app()->environment('local')) {
            abort(404);
        }

        $data = $request->validate([
            'stage' => ['required', 'integer', 'min:0', 'max:6'],
        ]);

        if ($data['stage'] === 0) {
            EscalationLog::where('tenant_id', $tenant->id)->delete();
            $tenant->update([
                'portal_restricted' => false,
                'is_blacklisted' => false,
                'escalation_paused' => false,
            ]);

            return response()->json(['message' => "{$tenant->full_name} reset to Stage 0 — Not Delinquent."]);
        }

        if ($tenant->is_blacklisted) {
            return response()->json([
                'message' => 'This tenant is already blacklisted (Stage 6 is permanent). Reset to Stage 0 first.',
            ], 409);
        }

        $bill = $this->findOrCreateTestBill($tenant);
        $bill->update(['due_date' => now()->subDays(self::STAGE_DAYS[$data['stage']])]);

        $escalation->processBillingStatement($bill->fresh());

        return response()->json(['message' => "{$tenant->full_name} advanced toward Stage {$data['stage']}."]);
    }

    /**
     * Reuses the tenant's newest unpaid/overdue bill if one exists, so
     * real billing data isn't duplicated. Only creates a fresh test bill
     * if the tenant has nothing outstanding yet.
     */
    private function findOrCreateTestBill(Tenant $tenant): BillingStatement
    {
        $bill = BillingStatement::where('tenant_id', $tenant->id)
            ->whereIn('status', ['unpaid', 'partial', 'overdue'])
            ->orderByDesc('due_date')
            ->first();

        if ($bill) {
            return $bill;
        }

        $contract = $tenant->contracts()->latest('start_date')->first();

        abort_if(! $contract, 422, 'This tenant has no lease contract yet — assign a bed/lease before testing escalation.');

        return BillingStatement::create([
            'contract_id' => $contract->id,
            'tenant_id' => $tenant->id,
            'type' => 'monthly',
            'billing_period_start' => now()->subMonth(),
            'billing_period_end' => now(),
            'due_date' => now(),
            'base_rent' => $contract->monthly_rate,
            'total_amount' => $contract->monthly_rate,
            'status' => 'unpaid',
        ]);
    }
}