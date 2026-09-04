<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Damage;
use App\Models\EscalationLog;
use App\Models\LeaseContract;
use App\Models\Payment;
use App\Models\PenaltyAuditLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Collection;

class ActivityFeedService
{
    private const PER_SOURCE_LIMIT = 300;

    private ?Collection $userNames = null;

    public function all(): Collection
    {
        $this->userNames = User::pluck('name', 'id');

        return $this->paymentEvents()
            ->concat($this->escalationEvents())
            ->concat($this->tenantEvents())
            ->concat($this->applicationEvents())
            ->concat($this->leaseContractEvents())
            ->concat($this->penaltyEvents())
            ->concat($this->damageEvents())
            ->sortByDesc('date')
            ->values();
    }

    private function actorName(?int $userId, bool $systemIfMissing = false): ?string
    {
        if ($userId && $this->userNames->has($userId)) {
            return $this->userNames->get($userId);
        }

        return $systemIfMissing ? 'System' : null;
    }

    private function paymentEvents(): Collection
    {
        return Payment::where('status', 'approved')
            ->with('tenant:id,full_name')
            ->latest('created_at')
            ->take(self::PER_SOURCE_LIMIT)
            ->get()
            ->map(fn ($p) => [
                'date' => $p->created_at,
                'detail' => ($p->tenant?->full_name ?? 'A tenant') . ' — payment received (₱' . number_format($p->amount_paid, 0) . ')',
                'type' => 'Payment',
                'admin' => $this->actorName($p->reviewed_by),
            ]);
    }

    private function escalationEvents(): Collection
    {
        return EscalationLog::with('tenant:id,full_name')
            ->latest('created_at')
            ->take(self::PER_SOURCE_LIMIT)
            ->get()
            ->map(fn ($log) => [
                'date' => $log->created_at,
                'detail' => ($log->tenant?->full_name ?? 'A tenant') . ' — ' . ucwords(str_replace('_', ' ', $log->action_type ?? 'escalation update')),
                'type' => 'Delinquency',
                'admin' => $this->actorName($log->performed_by, systemIfMissing: true),
            ]);
    }

    /**
     * Tenant itself has no created-by column, but every tenant -- walk-in
     * (TenantController::store) or approved application
     * (ApplicationController::approve) -- always gets an initial
     * LeaseContract created in the same transaction, with created_by set
     * to the admin who did it. Uses the tenant's FIRST (oldest) contract,
     * not activeContract, since activeContract could later point at a
     * renewal contract instead of the original onboarding one.
     */
    private function tenantEvents(): Collection
    {
        return Tenant::with(['contracts' => fn ($q) => $q->oldest('created_at'), 'contracts.bed.room'])
            ->latest('created_at')
            ->take(self::PER_SOURCE_LIMIT)
            ->get()
            ->map(function ($t) {
                $firstContract = $t->contracts->first();
                $room = $firstContract?->bed?->room;
                $roomText = $room ? " (Room {$room->room_no})" : '';

                return [
                    'date' => $t->created_at,
                    'detail' => "New tenant onboarded: {$t->full_name}{$roomText}",
                    'type' => 'Occupancy',
                    'admin' => $this->actorName($firstContract?->created_by),
                ];
            });
    }

    private function applicationEvents(): Collection
    {
        return Application::latest('created_at')
            ->take(self::PER_SOURCE_LIMIT)
            ->get()
            ->map(fn ($a) => [
                'date' => $a->created_at,
                'detail' => "New application submitted: {$a->full_name}",
                'type' => 'Application',
                'admin' => $this->actorName($a->created_by),
            ]);
    }

    private function leaseContractEvents(): Collection
    {
        $contracts = LeaseContract::with('tenant:id,full_name')
            ->latest('created_at')
            ->take(self::PER_SOURCE_LIMIT)
            ->get();

        $events = collect();

        foreach ($contracts as $c) {
            $name = $c->tenant?->full_name ?? 'A tenant';

            $events->push([
                'date' => $c->created_at,
                'detail' => "Lease contract created for {$name}",
                'type' => 'Lease',
                'admin' => $this->actorName($c->created_by),
            ]);

            if ($c->signed_at) {
                $events->push([
                    'date' => $c->signed_at,
                    'detail' => "Lease contract signed by {$name}",
                    'type' => 'Lease',
                    'admin' => null,
                ]);
            }

            if ($c->last_renewed_at) {
                $events->push([
                    'date' => $c->last_renewed_at,
                    'detail' => "Lease contract renewed for {$name}",
                    'type' => 'Lease',
                    'admin' => $this->actorName($c->last_renewed_by),
                ]);
            }

            if ($c->terminated_at) {
                $events->push([
                    'date' => $c->terminated_at,
                    'detail' => "Lease contract terminated for {$name}" . ($c->termination_reason ? " — {$c->termination_reason}" : ''),
                    'type' => 'Lease',
                    'admin' => null,
                ]);
            }
        }

        return $events;
    }

    private function penaltyEvents(): Collection
    {
        return PenaltyAuditLog::with('penalty.tenant:id,full_name')
            ->latest('created_at')
            ->take(self::PER_SOURCE_LIMIT)
            ->get()
            ->map(function ($log) {
                $name = $log->penalty?->tenant?->full_name ?? 'A tenant';
                $action = match ($log->action) {
                    'created' => 'penalty added',
                    'waived' => 'penalty waived',
                    'reinstated' => 'penalty reinstated',
                    default => $log->action,
                };

                return [
                    'date' => $log->created_at,
                    'detail' => "{$name} — {$action}",
                    'type' => 'Penalty',
                    'admin' => $this->actorName($log->performed_by),
                ];
            });
    }

    private function damageEvents(): Collection
    {
        return Damage::with('tenant:id,full_name')
            ->latest('created_at')
            ->take(self::PER_SOURCE_LIMIT)
            ->get()
            ->map(fn ($d) => [
                'date' => $d->created_at,
                'detail' => ($d->tenant?->full_name ?? 'A tenant') . ' — damage recorded (₱' . number_format($d->cost, 0) . ')',
                'type' => 'Damage',
                'admin' => $this->actorName($d->created_by),
            ]);
    }
}