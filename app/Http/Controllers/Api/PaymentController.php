<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\MoveInPermitMail;
use App\Models\Bed;
use App\Models\BillingStatement;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    /**
     * Admin: list payments. Optional ?tenant_id=, ?billing_id=, ?status= filters.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'tenant_id' => ['nullable', 'integer', 'exists:tenants,id'],
            'billing_id' => ['nullable', 'integer', 'exists:billing_statements,id'],
            'status' => ['nullable', Rule::in(['pending', 'approved', 'rejected'])],
        ]);

        $query = Payment::with([
            'tenant:id,full_name',
            'billingStatement:id,billing_period_start,billing_period_end,due_date,total_amount,status',
            'recordedBy:id,name',
            'reviewedBy:id,name',
        ])->latest('payment_date');

        foreach (['tenant_id', 'billing_id', 'status'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        return response()->json(
            $query->get()->map(fn ($p) => $this->withProofUrl($p))
        );
    }

    /**
     * Admin: view a single payment.
     */
    public function show(Payment $payment): JsonResponse
    {
        $payment->load([
            'tenant',
            'billingStatement.contract:id,bed_id',
            'recordedBy:id,name',
            'reviewedBy:id,name',
        ]);

        return response()->json($this->withProofUrl($payment));
    }

    /**
     * Admin: record a cash payment against a billing statement.
     *
     * Recorded by an admin in person, so it counts immediately — status is
     * 'approved' on creation, no review step.
     */
    public function recordCash(Request $request, BillingStatement $billingStatement): JsonResponse
    {
        $data = $request->validate([
            'amount_paid' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['nullable', 'date', 'before_or_equal:today'],
            'reference_number' => ['nullable', 'string', 'max:100'],
        ], [
            'payment_date.before_or_equal' => 'Payment date cannot be in the future.',
        ]);

        $balance = $this->balanceOf($billingStatement);

        if ($balance <= 0) {
            return response()->json([
                'message' => 'This statement is already fully paid.',
            ], 409);
        }

        // Guard against overpayment — matches the use case's validation rule
        // that the amount must not exceed the outstanding balance.
        if ($data['amount_paid'] > $balance) {
            return response()->json([
                'message' => 'Payment exceeds the outstanding balance of ' . number_format($balance, 2) . '.',
                'errors' => ['amount_paid' => ['Amount cannot exceed the outstanding balance.']],
            ], 422);
        }

        $payment = DB::transaction(function () use ($billingStatement, $data, $request) {
            $payment = Payment::create([
                'billing_id' => $billingStatement->id,
                'tenant_id' => $billingStatement->tenant_id,
                'amount_paid' => $data['amount_paid'],
                'payment_method' => 'cash',
                'reference_number' => $data['reference_number'] ?? null,
                'payment_date' => $data['payment_date'] ?? now()->toDateString(),
                'status' => 'approved',
                'recorded_by' => $request->user()?->id,
                'reviewed_by' => $request->user()?->id,
                'reviewed_at' => now(),
                'created_at' => now(),
            ]);

            $this->resyncStatementStatus($billingStatement);

            return $payment;
        });

        $billingStatement->refresh();

        // Use Case Report — Pay Move-In Fees, step 6.1: if this payment fully
        // settled a move-in fee, occupy the bed and activate the tenant.
        // Cash payments go through this same activation path as approved
        // online proof — either route to "fully paid" should trigger it.
        $this->activateTenantIfMoveInSettled($billingStatement);

        $this->notify('payment.recorded', [
            'payment_id' => $payment->id,
            'billing_id' => $billingStatement->id,
            'tenant_id' => $billingStatement->tenant_id,
            'amount' => $payment->amount_paid,
            'fully_settled' => $billingStatement->status === 'paid',
        ]);

        return response()->json([
            'message' => 'Payment recorded successfully.',
            'payment' => $payment,
            'billing_statement' => $billingStatement,
            'balance' => $this->balanceOf($billingStatement),
            'receipt' => $this->buildReceipt($payment->fresh(['tenant', 'billingStatement'])),
        ], 201);
    }

    /**
     * Tenant: submit proof of an online payment (GCash screenshot, bank
     * transfer confirmation). Creates a PENDING payment — it does not affect
     * the balance until an admin approves it.
     */
    public function submitProof(Request $request, BillingStatement $billingStatement): JsonResponse
    {
        $data = $request->validate([
            'amount_paid' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', Rule::in(['gcash', 'bank_transfer', 'other'])],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'], // 10MB
        ], [
            'proof.required' => 'Please attach a screenshot or file showing proof of payment.',
            'payment_date.before_or_equal' => 'Payment date cannot be in the future.',
        ]);

        if ($billingStatement->status === 'paid') {
            return response()->json([
                'message' => 'This statement is already fully paid.',
            ], 409);
        }

        // Don't let a tenant stack multiple pending submissions on one bill.
        $hasPending = Payment::where('billing_id', $billingStatement->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return response()->json([
                'message' => 'You already have a payment proof awaiting review for this statement.',
            ], 409);
        }

        $proofPath = $request->file('proof')->store('payment-proofs', 'public');

        $payment = Payment::create([
            'billing_id' => $billingStatement->id,
            'tenant_id' => $billingStatement->tenant_id,
            'amount_paid' => $data['amount_paid'],
            'payment_method' => $data['payment_method'],
            'reference_number' => $data['reference_number'] ?? null,
            'payment_date' => $data['payment_date'],
            'status' => 'pending',
            'proof_path' => $proofPath,
            'created_at' => now(),
        ]);

        $this->notify('payment.proof_submitted', [
            'payment_id' => $payment->id,
            'billing_id' => $billingStatement->id,
            'tenant_id' => $billingStatement->tenant_id,
            'amount' => $payment->amount_paid,
        ]);

        return response()->json([
            'message' => 'Payment proof submitted. It will be reviewed by an administrator.',
            'payment' => $this->withProofUrl($payment),
        ], 201);
    }

    /**
     * Admin: approve a pending payment proof. Only now does it count toward
     * the balance and potentially settle the statement.
     */
    public function approveProof(Request $request, Payment $payment): JsonResponse
    {
        $data = $request->validate([
            'review_notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ($payment->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending payment proofs can be approved.',
            ], 409);
        }

        $statement = $payment->billingStatement;
        $balance = $this->balanceOf($statement);

        if ($payment->amount_paid > $balance) {
            return response()->json([
                'message' => 'This payment (' . number_format($payment->amount_paid, 2) . ') exceeds the outstanding balance of ' . number_format($balance, 2) . '. Reject it, or ask the tenant to resubmit with the correct amount.',
            ], 422);
        }

        DB::transaction(function () use ($payment, $statement, $data, $request) {
            $payment->update([
                'status' => 'approved',
                'review_notes' => $data['review_notes'] ?? null,
                'reviewed_by' => $request->user()?->id,
                'reviewed_at' => now(),
            ]);

            $this->resyncStatementStatus($statement);
        });

        $statement->refresh();

        // Use Case Report — Pay Move-In Fees, step 6.1: verifying payment on
        // a move-in fee is what actually occupies the bed and activates the
        // tenant — not application approval, which only reserves it.
        $this->activateTenantIfMoveInSettled($statement);

        $this->notify('payment.proof_approved', [
            'payment_id' => $payment->id,
            'billing_id' => $statement->id,
            'tenant_id' => $payment->tenant_id,
            'approved_by' => $request->user()?->id,
            'fully_settled' => $statement->status === 'paid',
        ]);

        return response()->json([
            'message' => 'Payment approved.',
            'payment' => $this->withProofUrl($payment->fresh()),
            'billing_statement' => $statement,
            'balance' => $this->balanceOf($statement),
            'receipt' => $this->buildReceipt($payment->fresh(['tenant', 'billingStatement'])),
        ]);
    }

    /**
     * Admin: reject a pending payment proof (unreadable screenshot, wrong
     * amount, unverifiable reference). Requires a reason so the tenant knows
     * what to fix.
     */
    public function rejectProof(Request $request, Payment $payment): JsonResponse
    {
        $data = $request->validate([
            'review_notes' => ['required', 'string', 'max:500'],
        ], [
            'review_notes.required' => 'Please give a reason so the tenant knows why it was rejected.',
        ]);

        if ($payment->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending payment proofs can be rejected.',
            ], 409);
        }

        $payment->update([
            'status' => 'rejected',
            'review_notes' => $data['review_notes'],
            'reviewed_by' => $request->user()?->id,
            'reviewed_at' => now(),
        ]);

        $this->notify('payment.proof_rejected', [
            'payment_id' => $payment->id,
            'billing_id' => $payment->billing_id,
            'tenant_id' => $payment->tenant_id,
            'reason' => $data['review_notes'],
            'rejected_by' => $request->user()?->id,
        ]);

        return response()->json([
            'message' => 'Payment proof rejected.',
            'payment' => $this->withProofUrl($payment->fresh()),
        ]);
    }

    /**
     * Payment history for a statement — the "view full payment history"
     * use case, chronological with running balance after each payment.
     */
    public function historyForStatement(BillingStatement $billingStatement): JsonResponse
    {
        $payments = Payment::where('billing_id', $billingStatement->id)
            ->orderBy('payment_date')
            ->orderBy('id')
            ->with('recordedBy:id,name', 'reviewedBy:id,name')
            ->get();

        $running = 0;
        $history = $payments->map(function ($p) use (&$running, $billingStatement) {
            if ($p->status === 'approved') {
                $running += (float) $p->amount_paid;
            }

            return [
                'id' => $p->id,
                'payment_date' => $p->payment_date,
                'amount_paid' => $p->amount_paid,
                'payment_method' => $p->payment_method,
                'reference_number' => $p->reference_number,
                'status' => $p->status,
                'review_notes' => $p->review_notes,
                'proof_url' => $p->proof_path ? Storage::disk('public')->url($p->proof_path) : null,
                // Balance only moves for approved payments; pending/rejected
                // rows appear in the history but don't reduce what's owed.
                'balance_after' => round($billingStatement->total_amount - $running, 2),
            ];
        });

        return response()->json([
            'billing_statement' => $billingStatement,
            'total_amount' => $billingStatement->total_amount,
            'amount_paid' => round($running, 2),
            'balance' => round($billingStatement->total_amount - $running, 2),
            'payments' => $history,
        ]);
    }

    /**
     * Sum of approved payments only — pending proofs don't count yet.
     */
    private function balanceOf(BillingStatement $statement): float
    {
        $paid = Payment::where('billing_id', $statement->id)
            ->where('status', 'approved')
            ->sum('amount_paid');

        return round((float) $statement->total_amount - (float) $paid, 2);
    }

    /**
     * Moves a statement between unpaid / partial / paid based on what's
     * actually been approved against it. Preserves 'overdue' handling:
     * an unsettled statement past its due date stays flagged overdue.
     */
    private function resyncStatementStatus(BillingStatement $statement): void
    {
        $paid = Payment::where('billing_id', $statement->id)
            ->where('status', 'approved')
            ->sum('amount_paid');

        $total = (float) $statement->total_amount;

        if ($paid <= 0) {
            $status = $statement->due_date->isPast() ? 'overdue' : 'unpaid';
        } elseif ($paid < $total) {
            $status = 'partial';
        } else {
            $status = 'paid';
        }

        $statement->update(['status' => $status]);
    }

    /**
     * Use Case Report — Pay Move-In Fees, step 6.1: once a move-in fee
     * statement is fully settled, occupy the bed and — for a brand-new
     * onboarding tenant — activate the account and send the move-in permit.
     *
     * Deliberately separates "occupy the bed" from "activate the tenant":
     * a returning tenant (findReturningTenant() match during approval) is
     * already Active from a prior stay, so only the bed needs to flip here.
     * Both cash payments and approved online proof funnel through this same
     * path, since either one can be what finally settles the statement.
     *
     * Safe to call even when the statement isn't fully paid yet, or isn't a
     * move-in fee at all — it's a no-op in both cases. Also safe to call more
     * than once on an already-settled statement (idempotent): the bed/tenant
     * status checks inside mean a repeat call changes nothing.
     */
    private function activateTenantIfMoveInSettled(BillingStatement $statement): void
    {
        if ($statement->type !== 'move_in' || $statement->status !== 'paid') {
            return;
        }

        $statement->loadMissing('contract', 'tenant');
        $contract = $statement->contract;
        $tenant = $statement->tenant;

        DB::transaction(function () use ($contract, $tenant) {
            if ($contract && $contract->bed_id) {
                $bed = Bed::with('room')->find($contract->bed_id);

                if ($bed && $bed->status === 'reserved') {
                    $bed->update(['status' => 'occupied']);
                    $bed->room?->syncStatusFromBeds();
                }
            }

            if ($tenant && $tenant->status === 'pending_move_in_payment') {
                $tenant->update(['status' => 'active']);
            }
        });

        if ($tenant && $tenant->email) {
            try {
                Mail::to($tenant->email)->send(new MoveInPermitMail($tenant, $contract));
            } catch (\Throwable $e) {
                Log::warning('Move-in permit email failed to send.', [
                    'tenant_id' => $tenant->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->notify('tenant.movein_settled', [
            'tenant_id' => $tenant?->id,
            'contract_id' => $contract?->id,
            'billing_id' => $statement->id,
        ]);
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

    private function withProofUrl(Payment $payment): Payment
    {
        $payment->proof_url = $payment->proof_path
            ? Storage::disk('public')->url($payment->proof_path)
            : null;

        return $payment;
    }

    private function notify(string $event, array $payload): void
    {
        Log::info("[notification stub] {$event}", $payload);
    }
}