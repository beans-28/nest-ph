<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #292420; }

  .letter-header { text-align: center; padding-bottom: 14px; border-bottom: 1px solid #c4c4c4; margin-bottom: 20px; }
  .letter-header h1 { font-size: 22px; margin: 0; color: #194e19; letter-spacing: 0.5px; }
  .letter-header p { font-size: 11px; margin: 4px 0 0 0; color: #4b5f4c; }

  .meta td { padding: 2px 6px; font-size: 11px; }

  h2.section { font-size: 15px; color: #194e19; margin: 20px 0 8px 0; text-transform: uppercase; letter-spacing: 0.4px; }

  p.body-text { font-size: 11.5px; line-height: 1.6; margin: 0 0 6px 0; }

  table.balance { width: 100%; border-collapse: collapse; background: #e9e8e7; border-radius: 10px; margin-top: 8px; }
  table.balance th, table.balance td { padding: 8px 10px; font-size: 11px; text-align: left; }
  table.balance th { text-transform: uppercase; font-size: 10px; color: #292420; border-bottom: 1px solid #c9c9c9; }
  table.balance td { border-bottom: 1px solid #d5d5d5; color: #194e19; font-weight: bold; }
  table.balance td.amount { text-align: right; color: #ba2828; }
  table.balance tr.total td { border-bottom: none; color: #292420; font-size: 12px; padding-top: 10px; }
  table.balance tr.total td.amount { color: #ba2828; font-size: 13px; }

  .demand-text { font-size: 11.5px; line-height: 1.7; margin-top: 6px; }
  .demand-text .highlight { color: #ba2828; font-weight: bold; }

  .deadline-box { background: #f8c2c2; border-radius: 14px; padding: 14px 20px; margin-top: 16px; }
  .deadline-box table { width: 100%; }
  .deadline-box td { text-align: center; color: #ba2828; width: 50%; }
  .deadline-box .label { font-size: 10.5px; font-weight: bold; text-transform: uppercase; }
  .deadline-box .value { font-size: 16px; font-weight: bold; margin-top: 2px; }
  .deadline-box .note { font-size: 8.5px; margin-top: 3px; }

  table.history-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
  table.history-table th, table.history-table td { border: 1px solid #ccc; padding: 6px 8px; font-size: 10.5px; text-align: left; }
  table.history-table th { background: #f2f2f2; }
  table.history-table td.status { color: #194e19; font-weight: bold; }

  .signature { margin-top: 40px; font-size: 11.5px; }
</style>
</head>
<body>
  <div class="letter-header">
    <h1>{{ $dormName }}</h1>
    <p>Formal Demand for Payment</p>
  </div>

  <table class="meta">
    <tr><td><strong>Date Issued:</strong></td><td>{{ now()->format('F j, Y') }}</td></tr>
    <tr><td><strong>Tenant:</strong></td><td>{{ $tenant->full_name }}</td></tr>
    <tr><td><strong>Contact:</strong></td><td>{{ $tenant->contact_number ?? '—' }}</td></tr>
    <tr><td><strong>Email:</strong></td><td>{{ $tenant->email ?? '—' }}</td></tr>
  </table>

  <h2 class="section">Salutation</h2>
  <p class="body-text">
    This formal demand letter is issued to inform you of your outstanding
    financial obligation to {{ $dormName }}. Despite repeated notices, SMS
    reminders, portal restriction, and emergency contact notification, your
    account balance remains unsettled as of {{ now()->format('F j, Y') }}.
    We urge you to take immediate action to resolve this matter before the
    final deadline stated below.
  </p>

  <h2 class="section">Outstanding Balance Breakdown</h2>
  <table class="balance">
    <thead>
      <tr><th>Description</th><th>Period</th><th style="text-align:right;">Amount</th></tr>
    </thead>
    <tbody>
      @foreach($bills as $b)
        <tr>
          <td>Rent &amp; Charges</td>
          <td>{{ \Carbon\Carbon::parse($b->billing_period_start)->format('F Y') }}</td>
          <td class="amount">PHP {{ number_format((float) ($b->total_amount - $b->penalty_amount), 2) }}</td>
        </tr>
      @endforeach
      @if($totalPenalties > 0)
        <tr>
          <td>Late Penalties</td>
          <td>
            @if($bills->isNotEmpty())
              {{ \Carbon\Carbon::parse($bills->min('billing_period_start'))->format('M Y') }} –
              {{ \Carbon\Carbon::parse($bills->max('billing_period_start'))->format('M Y') }}
            @endif
          </td>
          <td class="amount">PHP {{ number_format($totalPenalties, 2) }}</td>
        </tr>
      @endif
      <tr class="total">
        <td colspan="2">Total Amount Owed:</td>
        <td class="amount">PHP {{ number_format($totalOwed, 2) }}</td>
      </tr>
    </tbody>
  </table>

  <h2 class="section">Formal Demand and Final Notice</h2>
  <p class="demand-text">
    We hereby formally demand full payment of
    <span class="highlight">PHP {{ number_format($totalOwed, 2) }}</span>
    and all applicable late payment penalties no later than
    <span class="highlight">{{ $deadline }}</span>. Failure to settle the
    outstanding balance by this date will result in the permanent flagging
    of your account as delinquent and addition to our dormitory blacklist
    effective <span class="highlight">{{ $blacklistDate }}</span>, with all
    future room reservations at {{ $dormName }} permanently blocked.
  </p>

  <p style="margin-top:10px; font-size:11.5px; line-height:1.7;">
    Please be advised that if this balance remains unpaid after the deadline
    stated above, {{ $dormName }} Management reserves the right to bring
    this matter before the office of the Barangay for formal conciliation
    proceedings, in accordance with the Katarungang Pambarangay Law, prior
    to pursuing any further legal action.
  </p>

  <div class="deadline-box">
    <table>
      <tr>
        <td>
          <div class="label">Payment Deadline</div>
          <div class="value">{{ $deadline }}</div>
          <div class="note">Full balance must be settled</div>
        </td>
        <td>
          <div class="label">Blacklisting Date</div>
          <div class="value">{{ $blacklistDate }}</div>
          <div class="note">If balance remains unpaid</div>
        </td>
      </tr>
    </table>
  </div>

  <h2 class="section" style="margin-top:26px;">Escalation History</h2>
  <table class="history-table">
    <thead>
      <tr><th>Date</th><th>Stage</th><th>Action</th><th>Status</th></tr>
    </thead>
    <tbody>
      @foreach($history as $log)
        <tr>
          <td>{{ optional($log->created_at)->format('M j, Y g:i A') }}</td>
          <td>Stage {{ $log->stage }}</td>
          <td>{{ str_replace('_', ' ', ucfirst($log->action_type)) }}</td>
          <td class="status">{{ ucfirst($log->status) }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <div class="signature">
    <p>Sincerely,</p>
    <p><strong>{{ $dormName }} Management</strong></p>
  </div>
</body>
</html>