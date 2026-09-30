<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{-- Same look as pdfs/demand-letter.blade.php and pdfs/report.blade.php. --}}
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #292420; }

  .letter-header { text-align: center; padding-bottom: 14px; border-bottom: 1px solid #c4c4c4; margin-bottom: 20px; }
  .letter-header h1 { font-size: 22px; margin: 0; color: #194e19; letter-spacing: 0.5px; }
  .letter-header p { font-size: 11px; margin: 4px 0 0 0; color: #4b5f4c; }

  .meta td { padding: 2px 6px; font-size: 11px; }

  h2.section { font-size: 15px; color: #194e19; margin: 20px 0 8px 0; text-transform: uppercase; letter-spacing: 0.4px; }

  .paid-box { background: #dcebdc; border-radius: 14px; padding: 16px 20px; margin-top: 6px; text-align: center; color: #194e19; }
  .paid-box .label { font-size: 10.5px; font-weight: bold; text-transform: uppercase; }
  .paid-box .value { font-size: 22px; font-weight: bold; margin-top: 2px; }
  .paid-box .note { font-size: 9px; margin-top: 3px; }

  table.summary { width: 100%; border-collapse: collapse; background: #e9e8e7; margin-top: 8px; }
  table.summary td { padding: 8px 10px; font-size: 11px; border-bottom: 1px solid #d5d5d5; }
  table.summary td.k { color: #292420; width: 45%; }
  table.summary td.v { color: #194e19; font-weight: bold; text-align: right; }
  table.summary tr:last-child td { border-bottom: none; }

  .thanks { margin-top: 30px; font-size: 11.5px; line-height: 1.6; }
  .pdf-footer { position: fixed; bottom: -4px; left: 0; right: 0; text-align: center; font-size: 8.5px; color: #8a8a8a; }
  .brand-logo { height: 42px; width: auto; margin-bottom: 6px; }
</style>
</head>
<body>
  {{-- Dual branding: the dorm (PIC) issues this document; NEST.PH (PIP) generated it. --}}
  <div class="pdf-footer">Issued by {{ $brandDormName }} &middot; Generated via NEST.PH Dormitory Management System</div>
  <div class="letter-header">
    @if($brandLogoFile)<img src="{{ $brandLogoFile }}" class="brand-logo"><br>@endif
    <h1>{{ $dormName }}</h1>
    <p>Official Payment Receipt</p>
  </div>

  <table class="meta">
    <tr><td><strong>Receipt No.:</strong></td><td>{{ $receiptNo }}</td></tr>
    <tr><td><strong>Date Issued:</strong></td><td>{{ now()->format('F j, Y') }}</td></tr>
    <tr><td><strong>Received From:</strong></td><td>{{ $payment->tenant?->full_name ?? '—' }}</td></tr>
    @if($roomLabel)<tr><td><strong>Room:</strong></td><td>{{ $roomLabel }}</td></tr>@endif
  </table>

  <h2 class="section">Amount Received</h2>
  <div class="paid-box">
    <div class="label">Amount Paid</div>
    <div class="value">PHP {{ number_format((float) $payment->amount_paid, 2) }}</div>
    <div class="note">Paid on {{ optional($payment->payment_date)->format('F j, Y') ?? '—' }}</div>
  </div>

  <h2 class="section">Payment Details</h2>
  <table class="summary">
    <tr><td class="k">Payment For</td><td class="v">
      @if($bill)
        {{ $bill->type === 'move_in' ? 'Move-In Fee' : 'Monthly Rent & Charges' }},
        {{ \Carbon\Carbon::parse($bill->billing_period_start)->format('F Y') }}
      @else
        —
      @endif
    </td></tr>
    <tr><td class="k">Payment Method</td><td class="v">{{ $methodLabel }}</td></tr>
    <tr><td class="k">Reference No.</td><td class="v">{{ $payment->reference_number ?: '—' }}</td></tr>
    @if($bill)
      <tr><td class="k">Statement Total</td><td class="v">PHP {{ number_format((float) $bill->total_amount, 2) }}</td></tr>
      <tr><td class="k">Balance Remaining (as of Date Issued)</td><td class="v">PHP {{ number_format($balanceAfter, 2) }}</td></tr>
    @endif
  </table>

  <div class="thanks">
    <p>Thank you for your payment. Please keep this receipt for your records.</p>
    <p><strong>{{ $dormName }} Management</strong></p>
  </div>
</body>
</html>
