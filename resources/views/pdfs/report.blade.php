<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{-- Same look as pdfs/demand-letter.blade.php (header, colours, tables, footer). --}}
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #292420; }

  .letter-header { text-align: center; padding-bottom: 14px; border-bottom: 1px solid #c4c4c4; margin-bottom: 20px; }
  .letter-header h1 { font-size: 22px; margin: 0; color: #194e19; letter-spacing: 0.5px; }
  .letter-header p { font-size: 11px; margin: 4px 0 0 0; color: #4b5f4c; }

  .meta td { padding: 2px 6px; font-size: 11px; }

  h2.section { font-size: 15px; color: #194e19; margin: 20px 0 8px 0; text-transform: uppercase; letter-spacing: 0.4px; }

  p.body-text { font-size: 11.5px; line-height: 1.6; margin: 0 0 6px 0; }

  table.summary { width: 100%; border-collapse: collapse; background: #e9e8e7; margin-top: 8px; }
  table.summary th, table.summary td { padding: 8px 10px; font-size: 11px; text-align: left; }
  table.summary th { text-transform: uppercase; font-size: 10px; color: #292420; border-bottom: 1px solid #c9c9c9; }
  table.summary td { border-bottom: 1px solid #d5d5d5; color: #194e19; font-weight: bold; }
  table.summary td.amount { text-align: right; }
  table.summary tr.sub td { font-weight: normal; color: #292420; padding-left: 26px; }
  table.summary tr.total td { border-bottom: none; color: #292420; font-size: 12px; padding-top: 10px; }
  table.summary td.warn { color: #ba2828; }

  .highlight-box { background: #dcebdc; border-radius: 14px; padding: 14px 20px; margin-top: 16px; }
  .highlight-box table { width: 100%; }
  .highlight-box td { text-align: center; color: #194e19; }
  .highlight-box .label { font-size: 10.5px; font-weight: bold; text-transform: uppercase; }
  .highlight-box .value { font-size: 16px; font-weight: bold; margin-top: 2px; }
  .highlight-box .note { font-size: 8.5px; margin-top: 3px; }

  table.detail-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
  table.detail-table th, table.detail-table td { border: 1px solid #ccc; padding: 6px 8px; font-size: 10.5px; text-align: center; }
  table.detail-table th { background: #f2f2f2; }
  table.detail-table td.first, table.detail-table th.first { text-align: left; }
  table.detail-table td.rate { color: #194e19; font-weight: bold; }

  .chart { margin: 6px 0 10px 0; page-break-inside: avoid; }
  .chart-title { font-size: 10.5px; font-weight: bold; color: #4b5f4c; margin-top: 10px; }
  .signature { margin-top: 40px; font-size: 11.5px; }
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
    <p>{{ ['occupancy' => 'Occupancy Report', 'financial' => 'Financial / Billing Report', 'forecast' => 'Forecast Report'][$type] }}</p>
  </div>

  <table class="meta">
    <tr><td><strong>Date Generated:</strong></td><td>{{ now()->format('F j, Y g:i A') }}</td></tr>
    @if($type === 'occupancy')
      <tr><td><strong>Coverage:</strong></td><td>Current room and bed status (live snapshot), plus a 12-month trend</td></tr>
    @elseif($type === 'forecast')
      <tr><td><strong>Coverage:</strong></td><td>Estimates for {{ $report['range'] }}, based on the last 6 months</td></tr>
    @else
      <tr><td><strong>Period Covered:</strong></td><td>{{ $report['range']['start'] }} to {{ $report['range']['end'] }}</td></tr>
    @endif
  </table>

  @if($type === 'occupancy')
    <h2 class="section">Overview</h2>
    <p class="body-text">
      This report summarizes the current occupancy of {{ $dormName }}, covering
      {{ $report['total_rooms'] }} rooms and {{ $report['total_beds'] }} bedspaces
      as of {{ now()->format('F j, Y') }}.
    </p>

    <div class="highlight-box">
      <table>
        <tr>
          <td>
            <div class="label">Occupancy Rate</div>
            <div class="value">{{ $report['occupancy_rate'] }}%</div>
            <div class="note">Occupied bedspaces out of total</div>
          </td>
          <td>
            <div class="label">Available Bedspaces</div>
            <div class="value">{{ $report['vacant'] }}</div>
            <div class="note">Vacant and ready for new tenants</div>
          </td>
        </tr>
      </table>
    </div>

    <h2 class="section">Summary</h2>
    <table class="summary">
      <thead><tr><th>Metric</th><th style="text-align:right;">Count</th></tr></thead>
      <tbody>
        <tr><td>Total Rooms</td><td class="amount">{{ $report['total_rooms'] }}</td></tr>
        <tr><td>Total Bedspaces</td><td class="amount">{{ $report['total_beds'] }}</td></tr>
        <tr><td>Occupied</td><td class="amount">{{ $report['occupied'] }}</td></tr>
        <tr><td>Vacant / Available</td><td class="amount">{{ $report['vacant'] }}</td></tr>
        <tr><td>Reserved</td><td class="amount">{{ $report['reserved'] }}</td></tr>
        <tr><td>Under Maintenance</td><td class="amount">{{ $report['maintenance'] }}</td></tr>
        <tr class="total"><td>Occupancy Rate:</td><td class="amount">{{ $report['occupancy_rate'] }}%</td></tr>
      </tbody>
    </table>

    <h2 class="section" style="margin-top:26px;">Breakdown by Floor</h2>
    <table class="detail-table">
      <thead>
        <tr><th class="first">Floor</th><th>Total Beds</th><th>Occupied</th><th>Vacant</th><th>Reserved</th><th>Maintenance</th><th>Occupancy</th></tr>
      </thead>
      <tbody>
        @forelse($report['by_floor'] as $floor)
          <tr>
            <td class="first">{{ $floor['label'] }}</td>
            <td>{{ $floor['total_beds'] }}</td>
            <td>{{ $floor['occupied'] }}</td>
            <td>{{ $floor['vacant'] }}</td>
            <td>{{ $floor['reserved'] }}</td>
            <td>{{ $floor['maintenance'] }}</td>
            <td class="rate">{{ $floor['occupancy_rate'] }}%</td>
          </tr>
        @empty
          <tr><td colspan="7">No floors added yet.</td></tr>
        @endforelse
      </tbody>
    </table>

    <h2 class="section" style="margin-top:26px;">Occupancy Trend (Last 12 Months)</h2>
    <p class="body-text">Occupancy rate at the end of each month, rebuilt from tenants' move-in and move-out dates.</p>
    <div class="chart"><img src="{{ $charts['trend'] }}" width="660"></div>
    <div class="chart-title">Tenants moved in / moved out per month</div>
    <div class="chart"><img src="{{ $charts['moves'] }}" width="660"></div>
    <table class="detail-table">
      <thead>
        <tr><th class="first">Month</th><th>Total Beds</th><th>Occupied</th><th>Moved In</th><th>Moved Out</th><th>Occupancy</th></tr>
      </thead>
      <tbody>
        @foreach($report['trend'] as $t)
          <tr>
            <td class="first">{{ $t['label'] }}</td>
            <td>{{ $t['total_beds'] }}</td>
            <td>{{ $t['occupied'] }}</td>
            <td>{{ $t['moved_in'] }}</td>
            <td>{{ $t['moved_out'] }}</td>
            <td class="rate">{{ $t['occupancy_rate'] }}%</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @elseif($type === 'forecast')
    <h2 class="section">Overview</h2>
    <p class="body-text">
      This report estimates occupancy, income and expenses for {{ $dormName }} from
      {{ $report['range'] }}. The estimates use simple averages of the last 6 months
      and the lease end dates on file, so treat them as a guide, not a guarantee.
    </p>

    <div class="highlight-box">
      <table>
        <tr>
          <td>
            <div class="label">Expected Income (3 months)</div>
            <div class="value">About PHP {{ number_format($report['totals']['income']) }}</div>
            <div class="note">Expected expenses: {{ $report['totals']['expenses'] === null ? 'none recorded' : 'about PHP ' . number_format($report['totals']['expenses']) }}</div>
          </td>
          <td>
            <div class="label">Expected Net</div>
            @if($report['totals']['net'] === null)
              <div class="value">-</div>
              <div class="note">Can't be estimated: no expenses recorded</div>
            @else
              <div class="value" @if($report['totals']['net'] < 0) style="color:#ba2828;" @endif>About PHP {{ number_format($report['totals']['net']) }}</div>
            @endif
            <div class="note">{{ $report['totals']['ending_leases'] }} lease(s) ending in this period</div>
          </td>
        </tr>
      </table>
    </div>

    <h2 class="section">How the Estimates Are Made</h2>
    <table class="summary">
      <thead><tr><th>Assumption</th><th style="text-align:right;">Value</th></tr></thead>
      <tbody>
        <tr><td>Average new move-ins per month</td><td class="amount">{{ $report['assumptions']['avg_move_ins'] }}</td></tr>
        <tr><td>Average income per occupied bed</td><td class="amount">PHP {{ number_format($report['assumptions']['income_per_bed'], 2) }}</td></tr>
        <tr><td>Average monthly expenses</td><td class="amount">{{ $report['assumptions']['expense_months'] ? 'PHP ' . number_format($report['assumptions']['avg_expenses'], 2) : 'None recorded' }}</td></tr>
        <tr><td>Months of expenses recorded (of last 6)</td><td class="amount">{{ $report['assumptions']['expense_months'] }}</td></tr>
      </tbody>
    </table>
    <p class="body-text">Tenants whose lease ends in a month are counted as moving out, so if some renew, actual occupancy will be higher. The current month is not over, so it shows income so far and is left out of the averages. Estimates are rounded to the nearest PHP 100.</p>

    <h2 class="section" style="margin-top:26px;">Actual and Estimated Months</h2>
    <div class="chart-title">Occupancy rate (* = estimate)</div>
    <div class="chart"><img src="{{ $charts['occupancy'] }}" width="660"></div>
    <div class="chart-title">Income vs. expenses (* = estimate)</div>
    <div class="chart"><img src="{{ $charts['money'] }}" width="660"></div>
    <table class="detail-table">
      <thead><tr><th class="first">Month</th><th>Kind</th><th>Leases Ending</th><th>Occupied</th><th>Occupancy</th><th>Income</th><th>Expenses</th><th>Net</th></tr></thead>
      <tbody>
        @foreach(array_merge($report['history'], $report['forecast']) as $r)
          @php $est = isset($r['ending_leases']); $partial = ! empty($r['partial']); @endphp
          <tr @if($est || $partial) style="font-style:italic;" @endif>
            <td class="first">{{ $r['label'] }}{{ $partial ? ' (so far)' : '' }}</td>
            <td>{{ $est ? 'Estimate' : ($partial ? 'So far' : 'Actual') }}</td>
            <td>{{ $est ? $r['ending_leases'] : '-' }}</td>
            <td>{{ $r['occupied'] }}</td>
            <td class="rate">{{ $r['occupancy_rate'] }}%</td>
            <td>PHP {{ number_format($r['income'], 2) }}</td>
            <td>{{ $r['expenses'] === null ? ($est ? '-' : 'Not recorded') : 'PHP ' . number_format($r['expenses'], 2) }}</td>
            <td @if(($r['net'] ?? 0) < 0) style="color:#ba2828;" @endif>{{ $r['net'] === null ? '-' : 'PHP ' . number_format($r['net'], 2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <h2 class="section">Overview</h2>
    <p class="body-text">
      This report summarizes payments collected, outstanding balances, and
      penalties, expenses and net profit for {{ $dormName }} from {{ $report['range']['start'] }} to
      {{ $report['range']['end'] }}.
    </p>

    <div class="highlight-box">
      <table>
        <tr>
          <td>
            <div class="label">Total Collected</div>
            <div class="value">PHP {{ number_format($report['total_collected'], 2) }}</div>
            <div class="note">{{ $report['payment_count'] }} approved payment(s)</div>
          </td>
          <td style="color:#ba2828;">
            <div class="label">Total Outstanding</div>
            <div class="value">PHP {{ number_format($report['total_outstanding'], 2) }}</div>
            <div class="note">Unpaid balances due within the period</div>
          </td>
        </tr>
      </table>
    </div>

    <h2 class="section">Revenue and Profit</h2>
    <table class="summary">
      <thead><tr><th>Description</th><th style="text-align:right;">Amount</th></tr></thead>
      <tbody>
        <tr><td>Total Collected</td><td class="amount">PHP {{ number_format($report['total_collected'], 2) }}</td></tr>
        <tr><td>Less: Total Expenses</td><td class="amount warn">PHP {{ number_format($report['total_expenses'], 2) }}</td></tr>
        <tr class="sub"><td>Electricity (Meralco)</td><td class="amount">PHP {{ number_format($report['expense_breakdown']['electricity'], 2) }}</td></tr>
        <tr class="sub"><td>Water</td><td class="amount">PHP {{ number_format($report['expense_breakdown']['water'], 2) }}</td></tr>
        <tr class="sub"><td>Internet / WiFi</td><td class="amount">PHP {{ number_format($report['expense_breakdown']['internet'], 2) }}</td></tr>
        <tr class="sub"><td>Staff Salaries</td><td class="amount">PHP {{ number_format($report['expense_breakdown']['salaries'], 2) }}</td></tr>
        <tr class="sub"><td>Others</td><td class="amount">PHP {{ number_format($report['expense_breakdown']['other'], 2) }}</td></tr>
        <tr class="total"><td>Net Profit:</td><td class="amount {{ $report['net_profit'] < 0 ? 'warn' : '' }}">PHP {{ number_format($report['net_profit'], 2) }}</td></tr>
      </tbody>
    </table>
    @if(count($report['months_missing_expenses']))
      <p class="body-text" style="font-size:9.5px;color:#8a8a8a;margin-top:6px;">No expenses recorded yet for: {{ implode(', ', $report['months_missing_expenses']->all()) }}.</p>
    @endif

    <h2 class="section" style="margin-top:26px;">Collections Breakdown</h2>
    <table class="summary">
      <thead><tr><th>Description</th><th style="text-align:right;">Amount</th></tr></thead>
      <tbody>
        <tr class="sub"><td>Cash</td><td class="amount">PHP {{ number_format($report['cash_collected'], 2) }}</td></tr>
        <tr class="sub"><td>Online (GCash / Bank / Other)</td><td class="amount">PHP {{ number_format($report['online_collected'], 2) }}</td></tr>
        <tr class="total"><td>Total Collected:</td><td class="amount">PHP {{ number_format($report['total_collected'], 2) }}</td></tr>
      </tbody>
    </table>

    <h2 class="section" style="margin-top:26px;">Receivables and Delinquency</h2>
    <table class="summary">
      <thead><tr><th>Metric</th><th style="text-align:right;">Value</th></tr></thead>
      <tbody>
        <tr><td>Total Outstanding</td><td class="amount warn">PHP {{ number_format($report['total_outstanding'], 2) }}</td></tr>
        <tr><td>Total Penalties Applied</td><td class="amount warn">PHP {{ number_format($report['total_penalties'], 2) }}</td></tr>
        <tr><td>Delinquent Accounts</td><td class="amount">{{ $report['delinquent_accounts'] }}</td></tr>
        <tr><td>Payments Recorded</td><td class="amount">{{ $report['payment_count'] }}</td></tr>
      </tbody>
    </table>

    <h2 class="section" style="margin-top:26px;">Monthly Breakdown</h2>
    <div class="chart-title">Collected vs. expenses per month (line = net profit)</div>
    <div class="chart"><img src="{{ $charts['profit'] }}" width="660"></div>
    <table class="detail-table">
      <thead><tr><th class="first">Month</th><th>Collected</th><th>Expenses</th><th>Net Profit</th></tr></thead>
      <tbody>
        @foreach($report['monthly'] as $m)
          <tr>
            <td class="first">{{ $m['label'] }}</td>
            <td>PHP {{ number_format($m['collected'], 2) }}</td>
            <td>{{ $m['has_expenses'] ? 'PHP ' . number_format($m['expenses'], 2) : 'Not recorded' }}</td>
            <td class="rate" @if($m['net'] < 0) style="color:#ba2828;" @endif>PHP {{ number_format($m['net'], 2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif

  <div class="signature">
    <p>Prepared by,</p>
    <p><strong>{{ $dormName }} Management</strong></p>
  </div>
</body>
</html>
