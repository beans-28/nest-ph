{{-- resources/views/pdfs/lease-contract.blade.php --}}
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 11.5px; color: #221b16; line-height: 1.55; }

  .header { text-align: center; padding-bottom: 14px; border-bottom: 1.5px solid #194e19; margin-bottom: 20px; }
  .header h1 { font-size: 21px; margin: 0; color: #194e19; letter-spacing: 1px; }
  .header p { font-size: 11px; margin: 6px 0 0 0; color: #221b16; text-transform: uppercase; letter-spacing: 0.5px; }

  .intro { font-size: 11px; margin-bottom: 16px; }

  h2.section { font-size: 13.5px; color: #194e19; margin: 18px 0 8px 0; }

  table.fields { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
  table.fields td { padding: 4px 0; font-size: 11px; vertical-align: bottom; }
  table.fields td.label { font-weight: bold; width: 150px; white-space: nowrap; }
  table.fields td.value { border-bottom: 1px solid #999; padding-left: 4px; }

  p.clause { font-size: 11px; margin: 0 0 8px 0; }

  .sig-block { width: 260px; margin-top: 46px; text-align: center; }
  .sig-line { border-bottom: 1px solid #333; height: 46px; }
  .sig-line img { max-height: 44px; max-width: 100%; }
  .sig-caption { font-size: 10px; margin-top: 5px; }
  .sig-date { font-size: 10.5px; margin-top: 10px; }
</style>
</head>
<body>

  <div class="header">
    <h1>NEST.PH</h1>
    <p>Pureza Station Dormitory Lease Contract</p>
  </div>

  <p class="intro">
    This Lease Contract ("Contract") is executed by and between NEST.PH Pureza Station Dormitory,
    represented by its duly authorized administrator (hereinafter referred to as "Management"), and
    the tenant identified below (hereinafter referred to as "Tenant"). The parties agree to be bound
    by the terms and conditions set forth in this Contract, which shall govern the Tenant's occupancy
    of the bedspace assigned herein.
  </p>

  <h2 class="section">1. Parties and Room Assignment</h2>
  <table class="fields">
    <tr><td class="label">Tenant Full Name:</td><td class="value">{{ $fullName }}</td></tr>
    <tr><td class="label">Contact Number:</td><td class="value">{{ $contactNumber ?: 'Not provided' }}</td></tr>
    <tr><td class="label">Email Address:</td><td class="value">{{ $email ?: 'Not provided' }}</td></tr>
    <tr>
      <td class="label">Room / Bed Assignment:</td>
      <td class="value">
        @if($roomNo)
          Room {{ $roomNo }}, {{ $bedLabel ?? '—' }}
        @else
          To be assigned
        @endif
      </td>
    </tr>
    <tr><td class="label">Monthly Rate:</td><td class="value">{{ $monthlyRate ? 'PHP ' . number_format($monthlyRate, 2) : 'To be confirmed upon approval' }}</td></tr>
    <tr><td class="label">Preferred Move-in Date:</td><td class="value">{{ $moveInDate ?? 'To be confirmed' }}</td></tr>
    <tr><td class="label">Preferred End Date:</td><td class="value">{{ $moveOutDate ?? 'To be confirmed' }}</td></tr>
  </table>

  <h2 class="section">2. Emergency Contact Information</h2>
  <table class="fields">
    <tr><td class="label">Emergency Contact Name:</td><td class="value">{{ $emergencyContactName ?: 'Not provided' }}</td></tr>
    <tr><td class="label">Relationship to Tenant:</td><td class="value">{{ $emergencyContactRelation ?: 'Not provided' }}</td></tr>
    <tr><td class="label">Emergency Contact Number:</td><td class="value">{{ $emergencyContactNumber ?: 'Not provided' }}</td></tr>
  </table>

  <h2 class="section">3. Term of Lease</h2>
  <p class="clause">This Contract takes effect on the move-in date stated above and continues on a month to month basis unless earlier terminated under Section 7. Either party may terminate this Contract by giving the other at least thirty (30) days' written notice.</p>

  <h2 class="section">4. Rent and Payment Terms</h2>
  <p class="clause">4.1. The Tenant agrees to pay the Monthly Rate stated above on or before the 5th day of each month. Payments may be made in cash at the Management office or through the accepted online payment channels shown in the Tenant Portal.</p>
  <p class="clause">4.2. A late payment penalty applies to any balance left unpaid after the due date, following the dormitory's official Rate and Penalty Schedule, available from Management upon request.</p>
  <p class="clause">4.3. A security deposit equal to one (1) month's rent is required before move in and will be refunded within thirty (30) days of move out, less any deductions for unpaid balances, damages, or penalties recorded during the Tenant's stay.</p>

  <h2 class="section">5. House Rules and Conduct</h2>
  <p class="clause">The Tenant agrees to follow the dormitory's House Rules, including but not limited to quiet hours, visitor policies, cleanliness standards, and prohibited items. The full House Rules are provided separately and form part of this Contract by reference.</p>

  <h2 class="section">6. Damages and Penalties</h2>
  <p class="clause">Any damage to dormitory property caused by the Tenant, whether by negligence or intent, will be recorded by Management and billed to the Tenant as a penalty added to the Tenant's next billing statement. The Tenant may contest a recorded penalty by filing a written dispute with Management within seven (7) days of notice.</p>

  <h2 class="section">7. Termination and Delinquency Escalation</h2>
  <p class="clause">7.1. Management reserves the right to terminate this Contract for repeated or serious violations of the House Rules, non payment of rent, or other just cause.</p>
  <p class="clause">7.2. If rent remains unpaid, the Tenant's account will be subject to the dormitory's escalation protocol, consisting of successive notices that may end in a formal Eviction Notice if the balance remains unresolved. Notices under this protocol may be sent by SMS to the mobile number provided in this Contract.</p>

  <h2 class="section">8. Emergency Contact and Data Privacy Consent</h2>
  <p class="clause">8.1. The Tenant authorizes Management to contact the Emergency Contact named in Section 2 in the event of a medical or safety emergency, or when the Tenant cannot be reached regarding an unpaid balance.</p>
  <p class="clause">8.2. In accordance with the Data Privacy Act of 2012 (RA 10173), the Tenant consents to the collection, use, and storage of the personal information provided in this Contract for the purposes of dormitory administration, billing, safety, and communication. This information will not be shared with third parties except as required by law or with the Tenant's explicit consent.</p>

  <h2 class="section">9. Acknowledgement</h2>
  <p class="clause">By signing below, the Tenant confirms having read, understood, and agreed to all terms in this Contract and the House Rules referenced herein.</p>

  <div class="sig-block">
    <div class="sig-line">
      @if($signatureDataUrl)
        <img src="{{ $signatureDataUrl }}">
      @endif
    </div>
    <div class="sig-caption">Tenant Signature over Printed Name</div>
    <div class="sig-caption">{{ $fullName }}</div>
    <div class="sig-date">Date: {{ $todayDate }}</div>
  </div>

</body>
</html>