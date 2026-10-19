{{-- Tenant dashboard: what the move-in fee paid for. Expects $fee from DepositRefund::summaryFor(). --}}
@php
  $peso = fn ($n) => '₱' . number_format($n, 2);
  $depositPill = [
    'awaiting' => ['pending', 'Not yet paid'],
    'held' => ['neutral', 'Held'],
    'refunded' => ['paid', 'Refunded'],
    'fully_deducted' => ['neutral', 'Fully deducted'],
  ][$fee['deposit_status']];
@endphp
<div class="panel fee-panel">
  <div class="panel-head"><h2>Deposit &amp; Advance</h2></div>
  <div class="fee-grid">

    <div>
      <div class="fee-title">
        Advance rent
        @if($fee['advance_months'] > 0)
          <span class="status-pill {{ $fee['paid'] ? 'paid' : 'pending' }}">{{ $fee['paid'] ? 'Paid' : 'Not yet paid' }}</span>
        @endif
      </div>
      @if($fee['advance_months'] > 0)
        <div class="fee-amount">{{ $peso($fee['advance_amount']) }}</div>
        <div class="fee-note">
          {{ $fee['advance_months'] }} {{ Str::plural('month', $fee['advance_months']) }} of rent, {{ $fee['paid'] ? 'used for' : 'pays for' }} {{ $fee['advance_covers'] }}.
          @if($fee['prorated_credit'] > 0)
            You moved in mid-month, so {{ $peso($fee['prorated_credit']) }} for the days before your move-in comes off your first monthly bill.
          @endif
          @if($fee['pay_ahead_credit'] > 0)
            <br>You also paid {{ $peso($fee['pay_ahead_credit']) }} of rent ahead, used on your coming bills.
          @endif
          @if($fee['paid'] && $fee['paid_on'])
            <br>Paid {{ $fee['paid_on'] }}@if($fee['reference']) · Ref {{ $fee['reference'] }}@endif
          @endif
        </div>
      @else
        <div class="fee-amount">None</div>
        <div class="fee-note">No advance rent was collected. Your rent is billed monthly from your move-in date.</div>
      @endif
    </div>

    <div>
      <div class="fee-title">
        Security deposit
        @if($fee['deposit_amount'] > 0)
          <span class="status-pill {{ $depositPill[0] }}">{{ $depositPill[1] }}</span>
        @endif
      </div>
      @if($fee['deposit_amount'] <= 0)
        <div class="fee-amount">None</div>
        <div class="fee-note">No security deposit was collected.</div>
      @elseif($fee['refund'])
        @php $r = $fee['refund']; @endphp
        <div class="fee-lines">
          <div><span>Deposit</span><span>{{ $peso($fee['deposit_amount']) }}</span></div>
          <div><span>Deductions{{ $r['deductions_note'] ? ' (' . $r['deductions_note'] . ')' : '' }}</span><span>− {{ $peso($r['deductions_amount']) }}</span></div>
          <div class="total"><span>Refunded to you</span><span>{{ $peso($r['refund_amount']) }}</span></div>
        </div>
        <div class="fee-note" style="margin-top:6px;">
          @if($r['refund_amount'] > 0)
            Sent via {{ $r['refund_method'] }} on {{ $r['refunded_at'] }}.
          @else
            Your whole deposit was used for the deductions above, so there is nothing to refund.
          @endif
          @if($fee['still_owed'] > 0)
            <br><strong>Still owed: {{ $peso($fee['still_owed']) }}</strong>. The deposit did not cover your full balance.
          @endif
        </div>
      @else
        <div class="fee-amount">{{ $peso($fee['deposit_amount']) }}</div>
        <div class="fee-note">
          @if($fee['paid'])
            Held by the dorm and returned after you move out, less any unpaid rent, penalties or damages. It is not used as rent.
          @else
            Part of your move-in fee. It is returned after you move out, less any unpaid rent, penalties or damages.
          @endif
        </div>
      @endif
    </div>

  </div>
</div>
