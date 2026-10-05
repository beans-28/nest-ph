<?php

namespace App\Console\Commands;

use App\Mail\ReservationExpiredMail;
use App\Models\Bed;
use App\Models\BillingStatement;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Payments and Fees Schedule 3.2: "The reservation is valid for one (1)
 * month from the date of payment." A tenant who paid only the first half of
 * the move-in fee and hasn't paid the rest a month later loses the
 * reservation: the bed goes back to Vacant, the contract is terminated and
 * the tenant account is closed. The tenant is emailed; the admin bell shows
 * it for a week (AdminNotificationController).
 *
 * An approved tenant who pays nothing at all gets the same treatment
 * UNPAID_DAYS after approval, unless a proof of payment is still waiting
 * for admin review (that delay isn't the tenant's fault).
 */
class ExpireMoveInReservations extends Command
{
    public const MONTHS = 1;

    public const UNPAID_DAYS = 7;

    protected $signature = 'reservations:expire';

    protected $description = 'Release beds for move-in fees that are unpaid 7 days after approval, or half-paid past the one-month reservation.';

    public function handle(): int
    {
        $bills = BillingStatement::with('contract', 'tenant.user')
            ->where('type', 'move_in')
            ->whereIn('status', ['partial', 'unpaid'])
            ->whereNull('reservation_expired_at')
            ->get()
            ->filter(fn (BillingStatement $bill) => $bill->reservationDeadline()?->isPast())
            ->reject(fn (BillingStatement $bill) => $bill->status === 'unpaid'
                && $bill->payments()->where('status', 'pending')->exists());

        foreach ($bills as $bill) {
            DB::transaction(function () use ($bill) {
                $contract = $bill->contract;
                $tenant = $bill->tenant;

                if ($contract?->bed_id) {
                    $bed = Bed::with('room')->find($contract->bed_id);
                    if ($bed && $bed->status === 'reserved') {
                        $bed->update(['status' => 'vacant']);
                        $bed->room?->syncStatusFromBeds();
                    }
                }

                $contract?->update(['status' => 'terminated']);

                // A proof still waiting for review can't hold a bed that's gone.
                Payment::where('billing_id', $bill->id)->where('status', 'pending')->update([
                    'status' => 'rejected',
                    'review_notes' => 'Reservation expired before the remaining balance was paid.',
                    'reviewed_at' => now(),
                ]);

                if ($tenant && $tenant->status === 'pending_move_in_payment') {
                    $tenant->update(['status' => 'inactive']);
                    $tenant->user?->update(['is_active' => false]);
                }

                $bill->update(['reservation_expired_at' => now()]);
            });

            $tenant = $bill->tenant;
            if ($tenant?->email) {
                try {
                    Mail::to($tenant->email)->send(new ReservationExpiredMail($tenant, $bill));
                } catch (\Throwable $e) {
                    Log::warning('Reservation expired email failed to send.', [
                        'tenant_id' => $tenant->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $this->info($bills->count() . ' reservation(s) expired.');

        return self::SUCCESS;
    }
}
