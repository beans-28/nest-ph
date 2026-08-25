<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BillingStatement;
use App\Models\Payment;
use App\Models\Penalty;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TenantPortalController extends Controller
{
    /**
     * Every method here resolves the tenant from the authenticated user, never
     * from a route parameter or request body. A tenant cannot address another
     * tenant's records because there is no input that could name one.
     */
    private function tenant(Request $request): Tenant
    {
        return $request->attributes->get('tenant') ?? $request->user()->tenant;
    }

    /**
     * Confirms a statement belongs to this tenant before doing anything with
     * it. Returns 404 rather than 403 on mismatch, so probing for other
     * tenants' statement IDs reveals nothing about what exists.
     */
    private function ownedStatement(Request $request, BillingStatement $statement): BillingStatement
    {
        if ($statement->tenant_id !== $this->tenant($request)->id) {
            throw new NotFoundHttpException('Billing statement not found.');
        }

        return $statement;
    }

    private function ownedPayment(Request $request, Payment $payment): Payment
    {
        if ($payment->tenant_id !== $this->tenant($request)->id) {
            throw new NotFoundHttpException('Payment not found.');
        }

        return $payment;
    }

    /**
     * Tenant: my profile and current account summary.
     */
    public function me(Request $request): JsonResponse
    {
        $tenant = $this->tenant($request);

        return response()->json([
            'tenant' => $tenant->only(['id', 'full_name', 'email', 'contact_number']),
            'contract' => $tenant->contracts()
                ->where('status', 'active')
                ->with('bed:id,room_id,bed_label', 'bed.room:id,room_no,room_type')
                ->first(),
            'summary' => $this->accountSummary($tenant),
        ]);
    }

    /**
     * Tenant: my billing statements.
     */
    public function myBills(Request $request): JsonResponse
    {
        $request->validate([
            'status' => ['nullable', Rule::in(['unpaid', 'partial', 'paid', 'overdue'])],
        ]);

        $tenant = $this->tenant($request);

        $query = BillingStatement::where('tenant_id', $tenant->id)
            ->with('payments')
            ->latest('due_date');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return response()->json([
            'summary' => $this->accountSummary($tenant),
            'bills' => $query->get()->map(fn ($bill) => $this->withBalance($bill)),
        ]);
    }

    /**
     * Tenant: one of my statements, with its penalty line items and payments.
     */
    public function showBill(Request $request, BillingStatement $billingStatement): JsonResponse
    {
        $bill = $this->ownedStatement($request, $billingStatement);

        $bill->load('payments.reviewedBy:id,name');
        $bill->penalties = Penalty::where('billing_id', $bill->id)
            ->with('damage:id,description,date_incurred')
            ->get();

        return response()->json($this->withBalance($bill));
    }

    /**
     * Tenant: my penalties (damage charges, fees), including waived ones so
     * the record is transparent.
     */
    public function myPenalties(Request $request): JsonResponse
    {
        $tenant = $this->tenant($request);

        return response()->json(
            Penalty::where('tenant_id', $tenant->id)
                ->with('damage:id,description,date_incurred')
                ->latest()
                ->get()
        );
    }

    /**
     * Tenant: my payment history, newest first.
     */
    public function myPayments(Request $request): JsonResponse
    {
        $tenant = $this->tenant($request);

        $payments = Payment::where('tenant_id', $tenant->id)
            ->with('billingStatement:id,billing_period_start,billing_period_end,total_amount')
            ->latest('payment_date')
            ->get()
            ->map(fn ($p) => $this->withProofUrl($p));

        return response()->json($payments);
    }

    /**
     * Tenant: submit proof of an online payment against one of MY statements.
     * Creates a pending record — it does not reduce the balance until an
     * admin approves it.
     */
    public function submitProof(Request $request, BillingStatement $billingStatement): JsonResponse
    {
        $bill = $this->ownedStatement($request, $billingStatement);

        $data = $request->validate([
            'amount_paid' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', Rule::in(['gcash', 'bank_transfer', 'other'])],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
        ], [
            'proof.required' => 'Please attach a screenshot or file showing proof of payment.',
            'payment_date.before_or_equal' => 'Payment date cannot be in the future.',
        ]);

        if ($bill->status === 'paid') {
            return response()->json(['message' => 'This statement is already fully paid.'], 409);
        }

        $hasPending = Payment::where('billing_id', $bill->id)->where('status', 'pending')->exists();

        if ($hasPending) {
            return response()->json([
                'message' => 'You already have a payment proof awaiting review for this statement.',
            ], 409);
        }

        $payment = Payment::create([
            'billing_id' => $bill->id,
            'tenant_id' => $bill->tenant_id,
            'amount_paid' => $data['amount_paid'],
            'payment_method' => $data['payment_method'],
            'reference_number' => $data['reference_number'] ?? null,
            'payment_date' => $data['payment_date'],
            'status' => 'pending',
            'proof_path' => $request->file('proof')->store('payment-proofs', 'public'),
            'created_at' => now(),
        ]);

        Log::info('[notification stub] payment.proof_submitted', [
            'payment_id' => $payment->id,
            'billing_id' => $bill->id,
            'tenant_id' => $bill->tenant_id,
        ]);

        return response()->json([
            'message' => 'Payment proof submitted. It will be reviewed by an administrator.',
            'payment' => $this->withProofUrl($payment),
        ], 201);
    }

    /**
     * Tenant: receipt data for one of MY approved payments.
     * Only approved payments produce a receipt — a pending or rejected
     * submission isn't proof of anything yet.
     */
    public function receipt(Request $request, Payment $payment): JsonResponse
    {
        $payment = $this->ownedPayment($request, $payment);

        if ($payment->status !== 'approved') {
            return response()->json([
                'message' => 'A receipt is only available once the payment has been approved.',
            ], 409);
        }

        return response()->json($this->buildReceipt($payment->load('tenant', 'billingStatement')));
    }

    private function accountSummary(Tenant $tenant): array
    {
        $bills = BillingStatement::where('tenant_id', $tenant->id)
            ->whereIn('status', ['unpaid', 'partial', 'overdue'])
            ->with('payments')
            ->get();

        $outstanding = $bills->sum(function ($bill) {
            $approved = $bill->payments->where('status', 'approved')->sum('amount_paid');

            return $bill->total_amount - $approved;
        });

        $unbilledPenalties = Penalty::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->whereNull('billing_id')
            ->sum('amount');

        return [
            'outstanding_balance' => round($outstanding, 2),
            'unbilled_penalties' => round($unbilledPenalties, 2),
            'total_owed' => round($outstanding + $unbilledPenalties, 2),
            'overdue_count' => $bills->where('status', 'overdue')->count(),
            'pending_review_count' => Payment::where('tenant_id', $tenant->id)
                ->where('status', 'pending')->count(),
        ];
    }

    private function withBalance(BillingStatement $bill): BillingStatement
    {
        $approved = $bill->payments->where('status', 'approved')->sum('amount_paid');
        $bill->amount_paid = round($approved, 2);
        $bill->balance = round($bill->total_amount - $approved, 2);

        return $bill;
    }

    private function withProofUrl(Payment $payment): Payment
    {
        $payment->proof_url = $payment->proof_path
            ? Storage::disk('public')->url($payment->proof_path)
            : null;

        return $payment;
    }

    private function buildReceipt(Payment $payment): array
    {
        return [
            'receipt_no' => 'RCPT-' . str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT),
            'tenant' => $payment->tenant?->full_name,
            'amount_paid' => $payment->amount_paid,
            'payment_method' => $payment->payment_method,
            'reference_number' => $payment->reference_number,
            'payment_date' => $payment->payment_date,
            'billing_period' => $payment->billingStatement
                ? $payment->billingStatement->billing_period_start . ' to ' . $payment->billingStatement->billing_period_end
                : null,
            'issued_at' => now()->toDateTimeString(),
        ];
    }
}
