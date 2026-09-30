<?php

namespace App\Services;

use App\Models\BillingStatement;
use App\Models\DepositRefund;
use App\Models\DormitoryProfile;
use App\Models\Payment;
use App\Models\Penalty;
use App\Models\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Statement of Account (v39): every bill, every approved payment, unbilled
 * penalties, the security deposit and the balance, as one PDF. Used by the
 * tenant (/my/billing/statement-of-account) and the admin
 * (/tenant-manager/{tenant}/statement-of-account).
 */
class StatementOfAccountPdf
{
    public static function download(Tenant $tenant)
    {
        $tenant->loadMissing('activeContract.bed.room');

        $bills = BillingStatement::where('tenant_id', $tenant->id)
            ->withApprovedPaid()
            ->orderBy('billing_period_start')
            ->orderBy('id')
            ->get();

        $payments = Payment::where('tenant_id', $tenant->id)
            ->where('status', 'approved')
            ->with('billingStatement:id,type,billing_period_start')
            ->orderBy('payment_date')
            ->orderBy('id')
            ->get();

        $unbilledPenalties = Penalty::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->whereNull('billing_id')
            ->get();

        $billed = round((float) $bills->sum('total_amount'), 2);
        $paid = round((float) $payments->sum('amount_paid'), 2);
        $outstanding = round($bills->sum(fn ($b) => $b->remainingBalance()), 2);
        $pendingPenalties = round((float) $unbilledPenalties->sum('amount'), 2);

        $fileName = 'Statement-of-Account-' . preg_replace('/[^A-Za-z0-9]+/', '-', $tenant->full_name) . '-' . now()->format('Y-m-d') . '.pdf';

        return Pdf::loadView('pdfs.statement-of-account', [
            'tenant' => $tenant,
            'room' => $tenant->activeContract?->bed?->room?->room_no,
            'bills' => $bills,
            'payments' => $payments,
            'unbilledPenalties' => $unbilledPenalties,
            'billed' => $billed,
            'paid' => $paid,
            'outstanding' => $outstanding,
            'pendingPenalties' => $pendingPenalties,
            'depositHeld' => DepositRefund::depositHeldFor($tenant),
            'depositRefund' => DepositRefund::where('tenant_id', $tenant->id)->latest('id')->first(),
            'dormName' => DormitoryProfile::current()->dorm_name ?? 'NEST PH',
        ])->download($fileName);
    }
}
