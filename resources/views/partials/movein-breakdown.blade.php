{{-- Unpaid move-in fee, split into what it's for. Expects $billing (a move_in BillingStatement). --}}
@php
    $rate = (float) ($billing->contract?->monthly_rate ?? 0);
    $advanceMonths = $billing->advanceMonths();
    $depositMonths = $rate > 0 ? (int) round((float) $billing->deposit_amount / $rate) : 0;
    $covers = \App\Models\DepositRefund::advanceCoverage($billing);
    $profile = \App\Models\DormitoryProfile::current();
    $start = $billing->contract?->start_date;
    $prorated = $advanceMonths > 0 && $start && $start->day > 1
        && $profile->rent_due_basis !== 'start_date' && $profile->mid_month_move_in === 'prorated';
@endphp
<dl class="movein-ledger">
    @if($advanceMonths > 0)
        <div><dt>Advance rent ({{ $advanceMonths }} {{ Str::plural('month', $advanceMonths) }})<span class="ledger-sub">Pays for {{ $covers }}</span></dt><dd>₱{{ number_format($billing->advance_amount, 2) }}</dd></div>
    @endif
    @if((float) $billing->deposit_amount > 0)
        <div><dt>Security deposit ({{ $depositMonths }} {{ Str::plural('month', $depositMonths) }})<span class="ledger-sub">Returned when you move out</span></dt><dd>₱{{ number_format($billing->deposit_amount, 2) }}</dd></div>
    @endif
    <div class="is-due"><dt>Total move-in fee</dt><dd>₱{{ number_format($billing->total_amount, 2) }}</dd></div>
</dl>
@if($prorated)
    <p class="movein-deadline">You move in on {{ $start->format('F j') }}, so the {{ $start->day - 1 }} {{ Str::plural('day', $start->day - 1) }} before that (₱{{ number_format($rate * ($start->day - 1) / $start->daysInMonth, 2) }}) come off your first monthly bill.</p>
@endif
