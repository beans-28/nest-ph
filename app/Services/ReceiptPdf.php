<?php

namespace App\Services;

use App\Models\DormitoryProfile;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Official payment receipt as a PDF (pdfs/receipt.blade.php), shared by the
 * tenant download (TenantPortalController::receiptPdf) and the admin one
 * (PaymentController::receiptPdf). Only for approved payments; callers check
 * ownership/permission first.
 */
class ReceiptPdf
{
    public static function number(Payment $payment): string
    {
        return 'RCPT-' . str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT);
    }

    public static function download(Payment $payment)
    {
        $payment->loadMissing(['tenant.activeContract.bed.room', 'billingStatement']);
        $bill = $payment->billingStatement;

        $paidOnBill = $bill
            ? (float) $bill->payments()->where('status', 'approved')->sum('amount_paid')
            : (float) $payment->amount_paid;

        $number = self::number($payment);

        return Pdf::loadView('pdfs.receipt', [
            'payment' => $payment,
            'bill' => $bill,
            'receiptNo' => $number,
            'methodLabel' => $payment->payment_method_label
                ?: ['cash' => 'Cash', 'gcash' => 'GCash', 'bank_transfer' => 'Bank Transfer', 'other' => 'Other'][$payment->payment_method] ?? ucfirst((string) $payment->payment_method),
            'roomLabel' => $payment->tenant?->activeContract?->bed?->room?->room_no,
            'balanceAfter' => $bill ? max(0, round((float) $bill->total_amount - $paidOnBill, 2)) : 0,
            'dormName' => DormitoryProfile::current()->dorm_name ?? 'NEST PH',
        ])->download("{$number}.pdf");
    }
}
