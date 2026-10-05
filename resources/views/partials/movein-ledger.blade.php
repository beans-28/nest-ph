{{-- Half-paid move-in fee summary. Expects $billing (a move_in BillingStatement with its first half approved). --}}
@php
    $remaining = $billing->remainingBalance();
    $deadline = $billing->reservationDeadline();
@endphp
<dl class="movein-ledger">
    <div><dt>Move-in fee</dt><dd>₱{{ number_format($billing->total_amount, 2) }}</dd></div>
    <div><dt>Paid (first half)</dt><dd>₱{{ number_format($billing->total_amount - $remaining, 2) }}</dd></div>
    <div class="is-due"><dt>Remaining</dt><dd>₱{{ number_format($remaining, 2) }}</dd></div>
</dl>
@if($deadline)
    <p class="movein-deadline">Pay by <strong>{{ $deadline->format('F j, Y') }}</strong> to keep your bedspace. After that date the reservation expires and the bed is released.</p>
@endif
