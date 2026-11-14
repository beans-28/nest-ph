<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
{{-- Delinquent Tenant Report (Pureza revision 2026-11-14). Same look as pdfs/report.blade.php. --}}
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #292420; }
  .letter-header { text-align: center; padding-bottom: 14px; border-bottom: 1px solid #c4c4c4; margin-bottom: 20px; }
  .letter-header h1 { font-size: 22px; margin: 0; color: #194e19; letter-spacing: 0.5px; }
  .letter-header p { font-size: 11px; margin: 4px 0 0 0; color: #4b5f4c; }
  .brand-logo { height: 42px; width: auto; margin-bottom: 6px; }
  .meta td { padding: 2px 6px; font-size: 11px; }
  h2.section { font-size: 15px; color: #194e19; margin: 20px 0 8px 0; text-transform: uppercase; letter-spacing: 0.4px; }
  p.body-text { font-size: 11.5px; line-height: 1.6; margin: 0 0 6px 0; }
  .highlight-box { background: #fbe9e9; border-radius: 14px; padding: 14px 20px; margin-top: 16px; }
  .highlight-box table { width: 100%; }
  .highlight-box td { text-align: center; color: #ba2828; }
  .highlight-box .label { font-size: 10.5px; font-weight: bold; text-transform: uppercase; }
  .highlight-box .value { font-size: 16px; font-weight: bold; margin-top: 2px; }
  table.detail-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
  table.detail-table th, table.detail-table td { border: 1px solid #ccc; padding: 6px 8px; font-size: 10.5px; }
  table.detail-table th { background: #f2f2f2; text-align: left; }
  table.detail-table .num { text-align: right; }
  table.detail-table .center { text-align: center; }
  table.detail-table tr.total td { background: #dcebdc; font-weight: bold; color: #194e19; }
  .balance { color: #ba2828; font-weight: bold; }
  thead { display: table-header-group; }
  tr { page-break-inside: avoid; }
  .signature { margin-top: 40px; font-size: 11.5px; }
  .pdf-footer { position: fixed; bottom: -4px; left: 0; right: 0; text-align: center; font-size: 8.5px; color: #8a8a8a; }
</style>
</head>
<body>
  <div class="pdf-footer">Issued by {{ $brandDormName }} &middot; Generated via NEST.PH Dormitory Management System</div>
  <div class="letter-header">
    @if($brandLogoFile)<img src="{{ $brandLogoFile }}" class="brand-logo"><br>@endif
    <h1>{{ $brandDormName }}</h1>
    <p>Delinquent Tenant Report</p>
  </div>

  <table class="meta">
    <tr><td><strong>Date Generated:</strong></td><td>{{ now()->format('F j, Y g:i A') }}</td></tr>
    <tr><td><strong>Coverage:</strong></td><td>Tenants with overdue bills, or blacklisted, as of {{ now()->format('F j, Y') }}</td></tr>
  </table>

  <div class="highlight-box">
    <table>
      <tr>
        <td>
          <div class="label">Delinquent Tenants</div>
          <div class="value">{{ $accounts->count() }}</div>
        </td>
        <td>
          <div class="label">Total Outstanding Balance</div>
          <div class="value">PHP {{ number_format($total, 2) }}</div>
        </td>
      </tr>
    </table>
  </div>

  <h2 class="section">Delinquent Tenants</h2>
  <p class="body-text">Sorted by outstanding balance, highest first. Outstanding balance is the unpaid amount of overdue bills, after any partial payments.</p>
  <table class="detail-table">
    <thead>
      <tr>
        <th style="width:24px;" class="center">#</th>
        <th>Tenant Name</th>
        <th class="center">Room No.</th>
        <th class="num">Outstanding Balance</th>
        <th class="center">Days Overdue</th>
        <th>Stage</th>
      </tr>
    </thead>
    <tbody>
      @forelse($accounts as $i => $a)
        <tr>
          <td class="center">{{ $i + 1 }}</td>
          <td>{{ $a['name'] }}{{ $a['is_blacklisted'] ? ' (blacklisted)' : '' }}</td>
          <td class="center">{{ $a['room'] }}</td>
          <td class="num balance">PHP {{ number_format($a['balance'], 2) }}</td>
          <td class="center">{{ $a['days_overdue'] }}</td>
          <td>{{ $a['stage_name'] }}</td>
        </tr>
      @empty
        <tr><td colspan="6" class="center">No delinquent tenants. Everyone is up to date.</td></tr>
      @endforelse
      @if($accounts->isNotEmpty())
        <tr class="total">
          <td></td>
          <td colspan="2">Total</td>
          <td class="num">PHP {{ number_format($total, 2) }}</td>
          <td colspan="2"></td>
        </tr>
      @endif
    </tbody>
  </table>

  <div class="signature">
    <p>Prepared by,</p>
    <p><strong>{{ $brandDormName }} Management</strong></p>
  </div>
</body>
</html>
