<?php

namespace App\Http\Controllers;

use App\Models\BillingStatement;
use App\Models\DormitoryProfile;
use App\Models\Payment;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Use Case Report — Pay Move-In Fees. Covers the screens before the actual
 * proof-of-payment upload lands on the admin's desk (steps 1–4 of the flow):
 * welcome, payment type, payment method, and the proof upload itself. Step
 * 5–6 (admin review queue, approve/reject) still needs an admin-facing page —
 * the backend for that already exists in PaymentController.
 */
class TenantOnboardingController extends Controller
{
    /**
     * Step 1.2–1.3: shown right after a Pending Move-In Payment tenant logs
     * in. NOTE: this page currently isn't gated by tenant status on its own —
     * the actual restriction comes from the `movein.check` middleware
     * applied to the tenant route group, which redirects here.
     *
     * FIX: previously this always showed the "Application Approved / Proceed
     * with Payment" screen even if the tenant had already submitted a proof
     * of payment that was just sitting in the admin's review queue — meaning
     * a tenant who already paid and was waiting for verification kept
     * getting bounced through the whole move-in flow every time they logged
     * back in. Now checks for a pending proof first and redirects to a
     * dedicated waiting screen instead.
     */
    public function welcome()
    {
        $tenant = Tenant::where('user_id', Auth::id())->firstOrFail();

        if ($this->hasPendingProof($tenant)) {
            return redirect()->route('tenant.movein.pending');
        }

        return view('tenantmoveinwelcome', [
            'tenant' => $tenant,
        ]);
    }

    /**
     * Shown instead of the welcome/payment flow once the tenant has already
     * submitted a proof of payment that's still awaiting admin review.
     * Includes a "Submit Again" option in case they need to correct or
     * resubmit their proof before it's reviewed.
     */
    public function pendingVerification()
    {
        $tenant = Tenant::where('user_id', Auth::id())->firstOrFail();

        return view('tenantmoveinpending', [
            'tenant' => $tenant,
        ]);
    }

    /**
     * Step 2: displays the move-in fee breakdown and lets the tenant choose
     * how they intend to pay. The Full/Partial choice itself is not part of
     * Table 17's documented flow — that use case only ever describes paying
     * the full computed total in one submission. This screen exists because
     * the team's own design calls for it.
     */
    public function paymentType()
    {
        $tenant = Tenant::where('user_id', Auth::id())->firstOrFail();
        $billing = $this->pendingMoveInBill($tenant);

        return view('tenantmoveinpaymenttype', [
            'tenant' => $tenant,
            'billing' => $billing,
        ]);
    }

    /**
     * Records the Full/Partial choice, then sends the tenant on to pick a
     * payment method.
     */
    public function storePaymentType(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'payment_type' => ['required', Rule::in(['full', 'partial'])],
        ]);

        session(['move_in_payment_type' => $data['payment_type']]);

        return redirect()->route('tenant.movein.payment-method');
    }

    /**
     * Payment method choice (GCash / BDO). Requires the payment type to
     * already be chosen — a tenant landing here directly (e.g. a bookmarked
     * URL) gets sent back to pick that first.
     */
    public function paymentMethod()
    {
        if (! session('move_in_payment_type')) {
            return redirect()->route('tenant.movein.payment-type');
        }

        $tenant = Tenant::where('user_id', Auth::id())->firstOrFail();

        return view('tenantmoveinpaymentmethod', [
            'tenant' => $tenant,
            'paymentType' => session('move_in_payment_type'),
        ]);
    }

    /**
     * Records the payment method, then sends the tenant to the actual
     * payment + proof-of-payment upload screen.
     */
    public function storePaymentMethod(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'payment_method' => ['required', Rule::in(['gcash', 'bdo'])],
        ]);

        session(['move_in_payment_method' => $data['payment_method']]);

        return redirect()->route('tenant.movein.payment');
    }

    /**
     * Steps 3–4: the actual payment screen — QR code for the chosen method,
     * balance due, and the proof-of-payment upload + details form. Submits
     * to the existing tenant billing endpoint
     * (/my/billing/bills/{id}/payment-proof), not a new one — this is the
     * page that endpoint was always missing.
     */
    public function payment()
    {
        $paymentType = session('move_in_payment_type');
        $paymentMethod = session('move_in_payment_method');

        if (! $paymentType) {
            return redirect()->route('tenant.movein.payment-type');
        }
        if (! $paymentMethod) {
            return redirect()->route('tenant.movein.payment-method');
        }

        $tenant = Tenant::where('user_id', Auth::id())->firstOrFail();
        $billing = $this->pendingMoveInBill($tenant);
        $profile = DormitoryProfile::current();

        return view('tenantmoveinpayment', [
            'tenant' => $tenant,
            'billing' => $billing,
            'paymentType' => $paymentType,
            'paymentMethod' => $paymentMethod,
            'gcashNumber' => $profile->gcash_number,
            'bdoAccountNumber' => $profile->bdo_account_number,
            'dormName' => $profile->dorm_name ?: 'NEST.PH',
        ]);
    }

    private function pendingMoveInBill(Tenant $tenant): ?BillingStatement
    {
        return BillingStatement::where('tenant_id', $tenant->id)
            ->where('type', 'move_in')
            ->where('status', '!=', 'paid')
            ->latest()
            ->first();
    }

    /**
     * True when the tenant's move-in billing statement already has a
     * proof-of-payment submission sitting in the admin's review queue.
     * Note: if the tenant uses "Submit Again", a second pending Payment row
     * will exist alongside the first — the admin can approve whichever one
     * is correct. Not yet handled: auto-voiding the earlier submission.
     */
    private function hasPendingProof(Tenant $tenant): bool
    {
        $billing = $this->pendingMoveInBill($tenant);

        if (! $billing) {
            return false;
        }

        return Payment::where('billing_id', $billing->id)
            ->where('status', 'pending')
            ->exists();
    }
}