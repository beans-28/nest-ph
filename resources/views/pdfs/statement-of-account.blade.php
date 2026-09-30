<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{-- Same look as pdfs/demand-letter, pdfs/receipt and pdfs/report. --}}
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #292420; }

  .letter-header { text-align: center; padding-bottom: 14px; border-bottom: 1px solid #c4c4c4; margin-bottom: 18px; }
  .letter-header h1 { font-size: 22px; margin: 0; color: #194e19; letter-spacing: 0.5px; }
  .letter-header p { font-size: 11px; margin: 4px 0 0 0; color: #4b5f4c; }

  .meta td { padding: 2px 6px; font-size: 11px; }

  h2.section { font-size: 14px; color: #194e19; margin: 20px 0 8px 0; text-transform: uppercase; letter-spacing: 0.4px; }

  .summary-box { background: #dcebdc; border-radius: 14px; padding: 12px 16px; margin-top: 4px; }
  .summary-box table { width: 100%; }
  .summary-box td { text-align: center; color: #194e19; width: 25%; }
  .summary-box td.owed { color: #ba2828; }
  .summary-box .label { font-size: 9.5px; font-weight: bold; text-transform: uppercase; }
  .summary-box .value { font-size: 14px; font-weight: bold; margin-top: 2px; }

  table.detail { width: 100%; border-collapse: collapse; margin-top: 6px; }
  table.detail th, table.detail td { border: 1px solid #ccc; padding: 5px 7px; font-size: 10px; text-align: left; }
  table.detail th { background: #f2f2f2; }
  table.detail td.num, table.detail th.num { text-align: right; white-space: nowrap; }
  table.detail td.owed { color: #ba2828; font-weight: bold; }
  table.detail tr.total td { font-weight: bold; background: #f7f7f7; }
  .empty { font-size: 10.5px; color: #6b6b6b; margin: 4px 0; }

  .note { font-size: 10px; color: #4b5f4c; line-height: 1.5; margin-top: 18px; }
  .pdf-footer { position: fixed; bottom: -4px; left: 0; right: 0; text-align: center; font-size: 8.5px; color: #8a8a8a; }
  .brand-logo { height: 42px; width: auto; margin-bottom: 6px; }
</style>
</head>
<body>
  <div class="pdf-footer">Issued by {{ $brandDormName }} &middot; Generated via NEST.PH Dormitory Management System</div>
  <div class="letter-header">
    @if($brandLogoFile)<img src="{{ $brandLogoFile }}" class="brand-logo"><br>@endif
    <h1>{{ $dormName }}</h1>
    <p>Statement of Account</p>
  </div>

  <table class="meta">
    <tr><td><strong>Tenant:</strong></td><td>{{ $tenant->full_name }}</td></tr>
    @if($room)<tr><td><strong>Room:</strong></td><td>{{ $room }}</td></tr>@endif
    <tr><td><strong>As of:</strong></td><td>{{ now()->format('F j, Y g:i A') }}</td></tr>
  </table>

  <h2 class="section">Summary</h2>
  <div class="summary-box">
    <table>
      <tr>
        <td><div class="label">Total Billed</div><div class="value">PHP {{ number_format($billed, 2) }}</div></td>
        <td><div class="label">Total Paid</div><div class="value">PHP {{ number_format($paid, 2) }}</div></td>
        <td class="{{ $outstanding > 0 ? 'owed' : '' }}"><div class="label">Balance Due</div><div class="value">PHP {{ number_format($outstanding, 2) }}</div></td>
        <td><div class="label">Deposit Held</div><div class="value">PHP {{ number_format($depositRefund ? 0 : $depositHeld, 2) }}</div></td>
      </tr>
    </table>
  </div>

  <h2 class="section">Bills</h2>
  @if($bills->isEmpty())
    <p class="empty">No bills yet.</p>
  @else
    <table class="detail">
      <thead><tr><th>Period</th><th>Type</th><th>Due Date</th><th class="num">Amount</th><th class="num">Paid</th><th class="num">Balance</th><th>Status</th></tr></thead>
      <tbody>
        @foreach($bills as $b)
          @php($bal = $b->remainingBalance())
          <tr>
            <td>{{ \Carbon\Carbon::parse($b->billing_period_start)->format('M Y') }}</td>
            <td>{{ $b->type === 'move_in' ? 'Move-in fee' : 'Monthly' }}</td>
            <td>{{ optional($b->due_date)->format('M j, Y') ?? '—' }}</td>
            <td class="num">{{ number_format((float) $b->total_amount, 2) }}</td>
            <td class="num">{{ number_format((float) ($b->approved_paid ?? 0), 2) }}</td>
            <td class="num {{ $bal > 0 ? 'owed' : '' }}">{{ number_format($bal, 2) }}</td>
            <td>{{ ucfirst($b->status) }}</td>
          </tr>
        @endforeach
        <tr class="total"><td colspan="3">Total</td><td class="num">{{ number_format($billed, 2) }}</td><td class="num">{{ number_format((float) $bills->sum('approved_paid'), 2) }}</td><td class="num">{{ number_format($outstanding, 2) }}</td><td></td></tr>
      </tbody>
    </table>
  @endif

  <h2 class="section">Payments Received</h2>
  @if($payments->isEmpty())
    <p class="empty">No approved payments yet.</p>
  @else
    <table class="detail">
      <thead><tr><th>Date</th><th>Receipt No.</th><th>For</th><th>Method</th><th>Reference</th><th class="num">Amount</th></tr></thead>
      <tbody>
        @foreach($payments as $p)
          <tr>
            <td>{{ optional($p->payment_date)->format('M j, Y') }}</td>
            <td>{{ \App\Services\ReceiptPdf::number($p) }}</td>
            <td>{{ $p->billingStatement ? ($p->billingStatement->type === 'move_in' ? 'Move-in fee' : \Carbon\Carbon::parse($p->billingStatement->billing_period_start)->format('M Y')) : '—' }}</td>
            <td>{{ $p->payment_method_label ?: ucwords(str_replace('_', ' ', $p->payment_method)) }}</td>
            <td>{{ $p->reference_number ?: '—' }}</td>
            <td class="num">{{ number_format((float) $p->amount_paid, 2) }}</td>
          </tr>
        @endforeach
        <tr class="total"><td colspan="5">Total</td><td class="num">{{ number_format($paid, 2) }}</td></tr>
      </tbody>
    </table>
  @endif

  @if($unbilledPenalties->isNotEmpty())
    <h2 class="section">Penalties Not Yet Billed</h2>
    <table class="detail">
      <thead><tr><th>Description</th><th>Date</th><th class="num">Amount</th></tr></thead>
      <tbody>
        @foreach($unbilledPenalties as $pen)
          <tr>
            <td>{{ $pen->description }}</td>
            <td>{{ $pen->date_incurred ? \Carbon\Carbon::parse($pen->date_incurred)->format('M j, Y') : '—' }}</td>
            <td class="num">{{ number_format((float) $pen->amount, 2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <p class="empty">These will be added to the next bill (PHP {{ number_format($pendingPenalties, 2) }} in total).</p>
  @endif

  <h2 class="section">Security Deposit</h2>
  @if($depositRefund)
    <table class="detail">
      <tbody>
        <tr><td>Deposit paid</td><td class="num">PHP {{ number_format((float) $depositRefund->deposit_amount, 2) }}</td></tr>
        <tr><td>Less: deductions{{ $depositRefund->deductions_note ? ' (' . $depositRefund->deductions_note . ')' : '' }}</td><td class="num">- PHP {{ number_format((float) $depositRefund->deductions_amount, 2) }}</td></tr>
        <tr class="total"><td>Refunded on {{ $depositRefund->refunded_at->format('F j, Y') }} via {{ $depositRefund->refund_method }}</td><td class="num">PHP {{ number_format((float) $depositRefund->refund_amount, 2) }}</td></tr>
      </tbody>
    </table>
  @elseif($depositHeld > 0)
    <p class="empty">PHP {{ number_format($depositHeld, 2) }} is held as your security deposit. It is settled when you move out, less any unpaid balance or damages.</p>
  @else
    <p class="empty">No security deposit on record.</p>
  @endif

  <p class="note">This statement lists approved payments only. Payments still under review are not included. For questions, please contact the {{ $dormName }} office.</p>
</body>
</html>
