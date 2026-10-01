{{--
  Payments and Fees Schedule -- word for word from
  PSD_Payments and Fees.docx (Version 1). Only the tenant's signature,
  printed name and date are filled in.
--}}
@php $box = '<span class="box">☐</span>'; @endphp
<div class="fees">
  <div class="doc-title">PAYMENTS AND FEES SCHEDULE</div>
  <div class="doc-version">Version 1 &nbsp;|&nbsp; Effective date: September 30, 2026</div>
  <p>This Schedule forms part of the Dormitory Tenant Agreement. Rates and charges may change only with advance notice to tenants, and a change does not apply to a term already paid for.</p>

  <h2>1. MONTHLY RENT</h2>
  <table class="grid">
    <tr><th style="width:30%">Location</th><th style="width:43%">Room type</th><th>Monthly rent</th></tr>
    <tr><td>Ground floor</td><td>Solo fan room</td><td>₱4,500 per room</td></tr>
    <tr><td>Ground floor</td><td>Room with AC, 4 persons</td><td>₱4,500 per bed</td></tr>
    <tr><td>2nd to 5th floor</td><td>Room with AC, 4 persons</td><td>₱4,200 per bed</td></tr>
    <tr><td>2nd to 5th floor</td><td>Room with AC, 6 persons</td><td>₱4,000 per bed</td></tr>
    <tr><td>2nd to 5th floor</td><td>Room with AC, 10–16 persons</td><td>₱3,800 per bed</td></tr>
  </table>
  <table class="grid">
    <tr><th style="width:30%">Other stays</th><th style="width:43%">Details</th><th>Rate</th></tr>
    <tr><td>Short-term stay</td><td>1 to 2 months. Set out in a separate written agreement, because the three (3)-month minimum does not apply.</td><td>₱4,500 per month</td></tr>
    <tr><td>Transient stay</td><td>Per night, 2:00 PM to 2:00 PM. Includes a pillow and bed sheets.</td><td>₱300 per night</td></tr>
  </table>

  <p>Rent is due on the <strong>first (1st) day of each month</strong>. Please confirm with the management whether these are included in the rent: water {!! $box !!} Yes {!! $box !!} No; electricity {!! $box !!} Yes {!! $box !!} No; Wi-Fi {!! $box !!} Yes {!! $box !!} No. If a tenant moves in mid-month, the first month is {!! $box !!} prorated {!! $box !!} charged in full.</p>

  <h2>2. HOW TO PAY</h2>
  <p>Pay only to the official accounts below. The dormitory is not responsible for payments sent to any other account. The same account details and QR codes are shown in your NEST.PH account.</p>
  <table class="grid">
    <tr><th style="width:24%">Method</th><th>Official account</th></tr>
    <tr><td>Cash</td><td>Paid to authorized dormitory staff, who record the payment in NEST.PH and issue a receipt.</td></tr>
    <tr><td>GCash</td><td>Number: 09178932970 &nbsp;|&nbsp; Account name: Patricia Joy N.</td></tr>
    <tr><td>Bank deposit</td><td>Bank: BDO &nbsp;|&nbsp; Account name: Patricia Joy Han &nbsp;|&nbsp; Account number: 005548032974</td></tr>
  </table>

  <p><strong>Paying and submitting proof through NEST.PH (GCash or bank).</strong> NEST.PH is not a payment gateway. You pay from your own GCash or bank app, then upload proof in the NEST.PH tenant portal:</p>
  @foreach([
    '(1)' => 'Log in to NEST.PH and open your billing page. Your outstanding balance and the official payment accounts are shown there.',
    '(2)' => 'Pay the exact amount due from your GCash or bank app.',
    '(3)' => 'Click <strong>Submit Payment Proof</strong> and upload a screenshot or confirmation (JPG, PNG, or PDF, up to 10 MB). Enter the amount, payment date, and reference number if you have one.',
    '(4)' => 'The management reviews your proof. NEST.PH shows “Awaiting admin verification” until then. You can only have one proof under review per billing statement at a time.',
    '(5)' => 'If the proof is approved, your balance is updated and an electronic receipt is sent to you. If it is rejected, the reason is shown on your billing page and you can submit a new proof.',
  ] as $n => $step)
    <table class="num"><tr><td class="n">{{ $n }}</td><td>{!! $step !!}</td></tr></table>
  @endforeach
  <p>Your payment counts as paid only after the management approves it, but the date you actually paid, as shown on your proof, is used to decide whether the payment was on time. If NEST.PH is unavailable, send your proof through the official Facebook Page (Pureza Station Dormitory) or the official email (dormitorypurezastation@gmail.com) before the grace period ends.</p>
  <p><strong>Receipts and records.</strong> Every payment receives an official receipt, whether paper or electronic. All payments are recorded in NEST.PH, where you can view your billing statement and payment history. If the system record and the official receipt differ, the official receipt controls and the management will correct the record.</p>

  @foreach([
    '3. RESERVATION FEE, ADVANCE RENT, AND SECURITY DEPOSIT' => [
      '3.1' => 'Upon registration, the new tenant pays a reservation fee made up of (a) one (1) month advance rent and (b) one (1) month security deposit. The advance rent pays for the first month. After that, rent is due every first (1st) day of the month.',
      '3.2' => 'The reservation is valid for one (1) month from the date of payment.',
      '3.3' => 'The security deposit is refundable after the tenant completes the three (3)-month minimum stay, less deductions for unpaid rent, penalties, damage beyond normal wear and tear, and other outstanding charges. Deductions are given in writing with reasons.',
      '3.4' => 'If the tenant cancels the reservation or checks out before completing the three (3)-month minimum, the amounts paid may be applied to the tenant’s outstanding obligations, as stated in Section 4.6 of the Agreement.',
      '3.5' => 'The remaining deposit is returned within twenty-one (21) days after check-out, inspection, and clearance, by the refund method the tenant chose (GCash or bank transfer).',
    ],
    '4. TERM, NOTICE, AND MOVE-OUT' => [
      '4.1' => 'The minimum stay is three (3) months. At registration, the tenant gives a start date and an end date. If the tenant cannot give an end date, the stay ends on the last day of the third month.',
      '4.2' => 'Move-out is at the end of the month. The tenant gives written notice at least two (2) weeks before the intended move-out date, through the NEST.PH portal or the official email. Notice does not cancel the three (3)-month minimum.',
      '4.3' => 'To extend a stay past the End Date, the tenant sends written notice at least one (1) month in advance through NEST.PH, the official Facebook Page, or the official email. Extensions need the management’s approval and depend on availability.',
    ],
    '5. LATE PAYMENT AND NON-PAYMENT' => [
      '5.1' => 'The tenant has a <strong>three (3)-day grace period</strong> after the due date.',
      '5.2' => 'If rent is unpaid after the grace period, a one-time penalty of <strong>ten percent (10%)</strong> of the overdue monthly rent is added. It is not compounded.',
      '5.3' => 'NEST.PH sends reminders and overdue notices by SMS, email, or in-app. While a balance is unpaid, the tenant’s NEST.PH access may be limited to the payment page until it is settled. This does not affect the tenant’s right to stay in the room or the tenant’s right to be heard and to receive any notice required by law.',
      '5.4' => 'With the tenant’s and emergency contact’s consent under Section 9.3 of the Agreement, overdue notices limited to the amount due, due date, and penalty may be sent to the emergency contact.',
      '5.5' => 'If unpaid rent reaches three (3) months, the management may terminate the tenancy and pursue remedies allowed by law, as stated in Section 3.4 of the Agreement. The management will follow the legal process and will not lock out the tenant or withhold the tenant’s belongings.',
    ],
  ] as $heading => $items)
    <h2>{{ $heading }}</h2>
    @foreach($items as $n => $item)
      <table class="num"><tr><td class="n">{{ $n }}</td><td>{!! $item !!}</td></tr></table>
    @endforeach
  @endforeach

  <h2>6. OTHER CHARGES</h2>
  <table class="grid">
    <tr><th style="width:56%">Charge</th><th style="width:24%">Amount</th><th>When</th></tr>
    <tr><td>Lost or unreturned key (key duplication)</td><td>₱50</td><td>Upon loss or check-out</td></tr>
    <tr><td>Possession or use of hazardous items (Rules, item 9)</td><td>₱500</td><td>Per violation</td></tr>
    <tr><td>Damage to dormitory property</td><td>Reasonable repair or replacement cost</td><td>Upon assessment</td></tr>
    <tr><td>Approved high-power appliance</td><td>₱__________ per month</td><td>If approved</td></tr>
    <tr><td>Late check-out after 2:00 PM</td><td>₱__________</td><td>If applicable</td></tr>
  </table>
  <p>The management shall give the tenant a written breakdown of any charge. No charge applies unless it is stated in this Schedule or the Agreement.</p>

  <div class="keep">
    <h2>7. TENANT’S ACKNOWLEDGMENT</h2>
    <p>I acknowledge that I received a copy of this Payments and Fees Schedule (Version 1), that I had the chance to read it and ask questions, and that I understand and agree to it.</p>
    @include('documents._signature', ['image' => $tenantSignature, 'name' => $tenantName, 'date' => $signedAt, 'caption' => 'Tenant’s Signature over Printed Name'])
  </div>
</div>
