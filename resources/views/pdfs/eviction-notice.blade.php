<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
  .header { text-align: center; margin-bottom: 24px; }
  .header h1 { font-size: 18px; margin: 0; color: #cd0000; }
  .meta td { padding: 3px 8px; }
  .box { border: 2px solid #cd0000; padding: 14px; margin: 18px 0; background: #fff5f5; }
  .signature { margin-top: 50px; }
  .pdf-footer { position: fixed; bottom: -18px; left: 0; right: 0; text-align: center; font-size: 8.5px; color: #8a8a8a; }
  .brand-logo { height: 42px; width: auto; margin-bottom: 6px; }
</style>
</head>
<body>
  {{-- Dual branding: the dorm (PIC) issues this document; NEST.PH (PIP) generated it. --}}
  <div class="pdf-footer">Issued by {{ $brandDormName }} &middot; Generated via NEST.PH Dormitory Management System</div>
  <div class="header">
    <h1>NOTICE OF EVICTION</h1>
    <p>{{ $brandDormName }} Management</p>
  </div>

  <table class="meta">
    <tr><td><strong>Date:</strong></td><td>{{ $noticeDate }}</td></tr>
    <tr><td><strong>Tenant Name:</strong></td><td>{{ $tenant->full_name }}</td></tr>
    <tr><td><strong>Unit/Room:</strong></td><td>{{ $room }}</td></tr>
  </table>

  <p>Dear {{ $tenant->full_name }},</p>

  <p>
    This letter serves as formal notice that, due to persistent non-compliance
    with your financial obligations under your lease agreement with {{ $brandDormName }},
    your account has been flagged as <strong>Delinquent</strong> and you have
    been placed on the dormitory's internal blacklist.
  </p>

  <div class="box">
    <strong>Reason for Eviction:</strong><br>
    {{ $reason }}
  </div>

  <p>
    You are hereby required to vacate your assigned unit/bedspace
    (<strong>{{ $room }}</strong>) on or before the date stated above, and to
    settle any outstanding balance owed to {{ $brandDormName }} prior to your departure.
  </p>

  <p>
    Failure to comply with this notice may result in further action as
    provided under your lease agreement and applicable law.
  </p>

  <div class="signature">
    <p>Sincerely,</p>
    <p><strong>{{ $brandDormName }} Management</strong></p>
  </div>
</body>
</html>