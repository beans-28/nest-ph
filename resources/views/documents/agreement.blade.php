{{--
  Dormitory Tenant Agreement -- word for word from PSD_Contract.docx. Only
  the blanks are filled in (see App\Services\TenancyDocuments::data()).
--}}
@php
  $fill = fn ($value, $width) => filled($value) ? $value : str_repeat('_', $width);
  $box = fn ($checked) => '<span class="box">' . ($checked ? '☑' : '☐') . '</span>';
@endphp
<div class="agreement">
  <div class="doc-title spaced">DORMITORY TENANT AGREEMENT</div>

  <p class="loose left">
    This Dormitory Tenant Agreement (“<strong>Agreement</strong>”) is entered into on the
    @if($agreementDate)<span class="fill">{{ $agreementDate->format('jS') }}</span> day of <span class="fill">{{ $agreementDate->format('F') }}</span>, 20<span class="fill">{{ $agreementDate->format('y') }}</span>,@else ____ day of ______________, 20____,@endif
    between:
  </p>

  <p class="loose left indent-1">
    <strong>Pureza Station Dormitory</strong>, with office address at
    <span class="{{ $officeAddress ? 'fill' : '' }}">{{ $fill($officeAddress, 38) }}</span>, represented by
    <span class="{{ $representative ? 'fill' : '' }}">{{ $fill($representative, 30) }}</span> (name and position), referred to as the <strong>“Lessor”</strong>; and
  </p>

  <p class="loose left indent-1">
    <strong class="{{ $tenantName ? 'fill' : '' }}">{{ $fill($tenantName, 36) }}</strong> (Tenant’s full name), of legal age, with home
    address at <span class="{{ $homeAddress ? 'fill' : '' }}">{{ $fill($homeAddress, 38) }}</span>, mobile number
    <span class="{{ $mobile ? 'fill' : '' }}">{{ $fill($mobile, 16) }}</span>, and email address
    <span class="{{ $email ? 'fill' : '' }}">{{ $fill($email, 22) }}</span>, referred to as the <strong>“Tenant.”</strong>
  </p>

  <p class="loose">
    The Lessor and the Tenant are together called the “<strong>Parties</strong>.” In this Agreement, “Lessor” includes its
    authorized owners, managers, and staff acting on its behalf.
  </p>

  <h2>1. DORMITORY DETAILS</h2>
  <p class="loose">The Tenant shall occupy:</p>
  <p class="loose left indent-2"><strong>Bed No.:</strong> <span class="{{ $bedNo ? 'fill' : '' }}">{{ $fill($bedNo, 20) }}</span></p>
  <p class="loose left indent-2"><strong>Room No.:</strong> <span class="{{ $roomNo ? 'fill' : '' }}">{{ $fill($roomNo, 20) }}</span></p>
  <p class="loose left indent-2">
    <strong>Room Type:</strong>&nbsp;
    {!! $box($roomTypeBox === 'solo') !!} Solo fan &nbsp;&nbsp;
    {!! $box($roomTypeBox === '4') !!} 4-person AC &nbsp;&nbsp;
    {!! $box($roomTypeBox === '6') !!} 6-person AC &nbsp;&nbsp;
    {!! $box($roomTypeBox === '10-16') !!} 10–16-person AC
  </p>
  <p class="loose">
    The Tenant understands that this is a dormitory (bedspace) arrangement and that, where applicable,
    the room and common facilities are shared with other tenants.
  </p>

  <h2>2. TERM OF OCCUPANCY</h2>
  <h3>2.1 Term</h3>
  <p class="loose left">
    The occupancy starts on <strong>Start Date: <span class="{{ $startDate ? 'fill' : '' }}">{{ $fill($startDate, 20) }}</span></strong>
    and ends on <strong>End Date: <span class="{{ $endDate ? 'fill' : '' }}">{{ $fill($endDate, 20) }}</span></strong>.
  </p>
  <h3>2.2 Minimum Occupancy</h3>
  <p class="loose">The minimum term is three (3) months from the Start Date. If no End Date is written above, the term ends on the last day of the third month counted from the Start Date.</p>
  <p class="loose">The Tenant remains responsible for rent and other charges properly due for the minimum occupancy period, subject to this Agreement and applicable Philippine law.</p>
  <h3>2.3 Move-Out</h3>
  <p class="loose">Unless the Parties agree otherwise in writing, move-out takes place on the last day of the applicable month. The Tenant must give notice under Section 13.1 and follow the Check-out Procedures.</p>
  <h3>2.4 Extension or Renewal</h3>
  <p class="loose">A Tenant who wishes to extend or renew should notify the Lessor in writing at least one (1) month before the End Date. Any extension or renewal is subject to: (a) the Lessor’s written approval; (b) bed or room availability; and (c) the rental rate then in effect.</p>
  <p class="loose">If the Tenant stays after the End Date without a written extension, the stay is month-to-month at the rental rate then in effect, and either Party may end it by notice under Section 13. It does not start a new fixed three-month term unless the Parties expressly agree in writing.</p>

  <h2>3. RENT AND PAYMENTS</h2>
  <h3>3.1 Monthly Rent</h3>
  <p class="loose left">
    The monthly rent is <strong>₱<span class="{{ $monthlyRent !== null ? 'fill' : '' }}">{{ $monthlyRent !== null ? number_format($monthlyRent, 2) : str_repeat('_', 20) }}</span></strong>,
    due on the <strong>first (1st) day of each month</strong>.
  </p>
  <h3>3.2 Payment Methods and Proof of Payment</h3>
  <p class="loose">Rent may be paid in cash, by GCash, or by bank deposit. Payments must be made only to the official accounts and channels listed in the Payments and Fees Schedule. The Lessor is not responsible for payments sent to any other account.</p>
  <p class="loose">Proof of payment, where required, must be submitted through an official channel: (a) the official Pureza Station Dormitory Facebook Page; (b) the official dormitory email; or (c) the NEST.PH Dormitory Management System.</p>
  <p class="loose">The Lessor shall issue or make available an official receipt or other proof of payment for each payment received, as required by law.</p>
  <h3>3.3 Late Payment</h3>
  <p class="loose">The Tenant has a grace period of three (3) calendar days after the rent due date. If rent is still unpaid after the grace period, the Lessor may charge a one-time late-payment penalty of ten percent (10%) of the overdue monthly rent, for each overdue month, subject to applicable law. The penalty shall not be compounded or charged again on the same overdue amount.</p>
  <h3>3.4 Non-Payment</h3>
  <p class="loose">If the Tenant’s unpaid rent reaches three (3) months, the Lessor may terminate the tenancy, demand that the Tenant pay and vacate, and pursue remedies allowed by Philippine law, including ejectment proceedings.</p>
  <p class="loose">Nothing in this Agreement allows the Lessor to forcibly remove the Tenant, change or block locks, withhold or dispose of the Tenant’s belongings, or cut off basic services in order to force the Tenant out. Any eviction must follow the procedures allowed by law.</p>

  <h2>4. RESERVATION FEE, ADVANCE RENT, AND SECURITY DEPOSIT</h2>
  <h3>4.1 Reservation Fee</h3>
  <p class="loose">Upon registration, the Tenant shall pay a reservation fee made up of: (a) one (1) month advance rent; and (b) one (1) month security deposit. The Lessor shall issue a receipt for these payments.</p>
  <h3>4.2 Validity of Reservation</h3>
  <p class="loose">The reservation is valid for one (1) month from the date of payment. If the Tenant does not move in by the Start Date or within that period, the Lessor may release the bed, and the amounts paid are handled under Section 4.6.</p>
  <h3>4.3 Advance Rent</h3>
  <p class="loose">The advance rent is applied to the Tenant’s rent for the first month. After that, rent is due on the first (1st) day of each month.</p>
  <h3>4.4 Security Deposit</h3>
  <p class="loose">The security deposit secures the Tenant’s obligations. It is refundable after the Tenant completes the minimum three (3)-month tenancy, less deductions for unpaid rent, penalties, damage beyond normal wear and tear, and other outstanding charges. The Tenant may not use the security deposit as payment of rent unless the Lessor agrees in writing.</p>
  <h3>4.5 Refund</h3>
  <p class="loose">The Lessor shall return the remaining security deposit within twenty-one (21) days after check-out, once the room inspection and clearance are complete. If any amount is deducted, the Lessor shall give the Tenant a written list of the deductions and the reasons.</p>
  <h3>4.6 Early Cancellation or Check-Out</h3>
  <p class="loose">If the Tenant cancels the reservation or checks out before completing the minimum three (3)-month tenancy, the amounts paid may be applied to the Tenant’s outstanding obligations, including rent due for the remainder of the minimum term, subject to this Agreement and applicable law. Any balance shall be refunded under Section 4.5.</p>

  <h2>5. DORMITORY RULES AND REGULATIONS</h2>
  <h3>5.1 Compliance</h3>
  <p class="loose">The Tenant agrees to follow the Dormitory Rules and Regulations, which are provided with this Agreement and form part of it.</p>
  <h3>5.2 Violations</h3>
  <p class="loose">A material or repeated violation of the Dormitory Rules and Regulations may lead to disciplinary action or termination of this Agreement, in line with the Rules, this Agreement, and Philippine law.</p>
  <h3>5.3 Changes to the Rules</h3>
  <p class="loose">The Lessor may adopt or update reasonable administrative, operational, safety, or security rules. The Lessor shall give the Tenant notice of any change before it takes effect, except where immediate action is needed for safety or security. A change shall not increase the rent or reduce the Tenant’s rights under this Agreement without the Tenant’s written agreement.</p>

  <h2>6. MAINTENANCE, REPAIRS, AND DAMAGES</h2>
  <h3>6.1 Lessor’s Responsibilities</h3>
  <p class="loose">The Lessor shall keep the premises and common facilities in reasonably safe and working condition, subject to normal wear and tear and circumstances beyond its reasonable control.</p>
  <h3>6.2 Reporting</h3>
  <p class="loose">The Tenant shall promptly report damage, defects, maintenance concerns, or unsafe conditions to dormitory staff or through NEST.PH.</p>
  <h3>6.3 Tenant-Caused Damage</h3>
  <p class="loose">The Tenant is responsible for the reasonable cost of repairing or replacing loss or damage caused by the Tenant or the Tenant’s guests, excluding normal wear and tear. Charges shall be based on actual reasonable cost or on a published schedule of charges given to the Tenant.</p>
  <p class="loose">If the person responsible for damage cannot reasonably be determined, the Tenant shall not be charged just because the damage happened in a shared area.</p>

  <h2>7. ROOM ACCESS AND INSPECTION</h2>
  <p class="loose">The Lessor or its authorized personnel may enter dormitory rooms at reasonable times for legitimate purposes, including: (a) inspection; (b) maintenance and repairs; (c) pest control and sanitation; (d) safety and security checks; (e) enforcing the dormitory rules; and (f) other legitimate dormitory operations.</p>
  <p class="loose">Where reasonably practicable, the Lessor shall give reasonable prior notice. Prior notice is not required in an emergency, where immediate entry is reasonably necessary to protect people or property, where there is reasonable cause to believe an urgent safety or security concern exists, or where the law otherwise permits.</p>
  <p class="loose">The Lessor shall exercise these rights reasonably and with due regard for the privacy of tenants.</p>

  <h2>8. USE OF THE NEST.PH DORMITORY MANAGEMENT SYSTEM</h2>
  <h3>8.1 System Functions</h3>
  <p class="loose">The Lessor uses the NEST.PH Dormitory Management System for legitimate dormitory-management purposes, including: (a) tenant registration and records; (b) room and bed assignments; (c) rent billing, payment records, and reminders; (d) maintenance requests; (e) notices and announcements; and (f) administrative reports and analytics.</p>
  <h3>8.2 Notifications</h3>
  <p class="loose">The Tenant may receive notifications by SMS, email, NEST.PH, or other registered channels. These may include billing statements, due-date reminders, overdue and penalty notices, maintenance updates, safety or emergency announcements, and other notices about the tenancy.</p>
  <h3>8.3 Records</h3>
  <p class="loose">NEST.PH is a tool for the Lessor’s administration. It does not replace the Lessor’s decisions, and official receipts and written notices remain the controlling records. If the system shows information that materially differs from an official receipt, written agreement, or other controlling record, the Lessor shall reasonably look into the difference and correct the record where appropriate.</p>
  <h3>8.4 Contact Information</h3>
  <p class="loose">The Tenant must give accurate contact details and keep them current. The Lessor is not responsible for a missed notice caused by inaccurate or outdated contact details supplied by the Tenant, without affecting any notice required by law.</p>

  <h2>9. EMERGENCY CONTACT</h2>
  <h3>9.1 Emergency Contact Information</h3>
  <p class="loose">The Tenant shall provide the name and current contact details of an emergency contact in the Personal Information Form. The Tenant confirms that the emergency contact has agreed to be listed and to be contacted by the Lessor for the purposes in this Section.</p>
  <h3>9.2 Permitted Contact</h3>
  <p class="loose">Subject to applicable privacy laws, the Lessor may contact the emergency contact when reasonably necessary for:</p>
  @foreach([
    'a' => 'a medical emergency, accident, or injury involving the Tenant;',
    'b' => 'fire, natural disaster, or another threat to the Tenant’s health or safety;',
    'c' => 'situations where the Tenant cannot be reached for an extended period and the Lessor has a genuine concern for the Tenant’s welfare;',
    'd' => 'abandonment, or circumstances reasonably indicating abandonment, of the Tenant’s accommodation, or another serious matter about the tenancy, after reasonable attempts to reach the Tenant first; and',
    'e' => 'billing reminders, only as allowed under Section 9.3.',
  ] as $letter => $purpose)
    <table class="num alpha"><tr><td class="n">({{ $letter }})</td><td>{{ $purpose }}</td></tr></table>
  @endforeach
  <h3>9.3 Billing Reminders</h3>
  <p class="loose">An emergency contact does not receive information about the Tenant’s rent, debts, or payment history just by being listed. Rent billing reminders and overdue notices may be sent to the emergency contact through NEST.PH only if the emergency contact has separately agreed, which the emergency contact may do by signing the consent line in this Agreement. These messages state only the amount due, the due date, and any penalty, and contain no other personal details of the Tenant.</p>
  <h3>9.4 Opting Out</h3>
  <p class="loose">The emergency contact may stop receiving billing reminders at any time by writing to dormitorypurezastation@gmail.com. Emergency contact under Section 9.2 (a) to (d) continues.</p>
  <h3>9.5 Limits on Use</h3>
  <p class="loose">The emergency contact’s details shall not be used for marketing or promotions. They shall not be shared with third parties, except with emergency responders or authorities when necessary or as required by law. Only authorized personnel who need the information for legitimate dormitory purposes may view it in NEST.PH.</p>
  <h3>9.6 Wrong or Outdated Details</h3>
  <p class="loose">The Lessor is not liable for failing to reach an emergency contact because the details given were wrong or outdated.</p>

  <h2>10. DATA PRIVACY</h2>
  <h3>10.1 Compliance</h3>
  <p class="loose">The Lessor shall process personal information under the Data Privacy Act of 2012 (Republic Act No. 10173), its implementing rules, and other applicable privacy requirements.</p>
  <h3>10.2 Personal Information Collected</h3>
  <p class="loose">Where reasonably necessary, this includes: (a) the Tenant’s identification and contact details; (b) date of birth and age verification; (c) school or employment information; (d) emergency-contact details; (e) room and occupancy information; (f) billing and payment records; (g) maintenance and incident records; and (h) other information needed for dormitory operations or legal compliance.</p>
  <h3>10.3 Purposes</h3>
  <p class="loose">Personal information is used only for: (a) processing tenancy applications; (b) administering the tenancy; (c) room and bed management; (d) billing and payments; (e) safety and security; (f) maintenance; (g) communicating with the Tenant; (h) complying with legal obligations; and (i) establishing, exercising, or defending legal claims.</p>
  <h3>10.4 Privacy Notice</h3>
  <p class="loose">The Tenant acknowledges receiving the Pureza Station Dormitory Privacy Notice, which gives further details on the information processed, the purposes and legal bases, who receives the information, retention periods, the Tenant’s rights, and how to raise privacy concerns.</p>
  <h3>10.5 Retention</h3>
  <p class="loose">Records are kept for the duration of the tenancy plus one (1) year, or longer where the law requires or where needed for a pending claim or dispute. After that, they are securely deleted or anonymized.</p>
  <h3>10.6 Tenant’s Rights</h3>
  <p class="loose">The Tenant may exercise the rights available under Philippine data privacy law, including access, correction, objection, and erasure or blocking where warranted. Requests may be sent to <strong>dormitorypurezastation@gmail.com</strong>. Erasure does not apply to records the Lessor is legally required to keep. The Tenant may also file a complaint with the National Privacy Commission.</p>
  <h3>10.7 Aggregated and Anonymized Information</h3>
  <p class="loose">The Lessor may use aggregated or anonymized information for statistical, operational, and analytical purposes, including NEST.PH reports, as long as no individual tenant can reasonably be identified.</p>

  <h2>11. LOSS OF PROPERTY AND LIABILITY</h2>
  <h3>11.1 Personal Belongings</h3>
  <p class="loose">The Tenant must take reasonable steps to secure personal belongings. The Lessor is not liable for loss of or damage to belongings caused by circumstances beyond its reasonable control or by the acts or negligence of the Tenant, other tenants, guests, or third persons, except to the extent the law makes the Lessor liable.</p>
  <h3>11.2 Injuries</h3>
  <p class="loose">The Lessor is not liable for injuries caused by circumstances beyond its reasonable control or by the Tenant’s own acts or negligence, without affecting any liability the law imposes on the Lessor. Nothing in this Agreement excludes or limits liability that cannot lawfully be excluded or limited.</p>
  <h3>11.3 Reporting</h3>
  <p class="loose">The Tenant should promptly report theft, accidents, injuries, unsafe conditions, or security incidents to the Lessor.</p>

  <h2>12. CASUALTY, EMERGENCY, OR UNINHABITABLE ACCOMMODATION</h2>
  <p class="loose">If the assigned bedspace or room becomes temporarily unsafe or unfit for occupancy because of fire, flooding, structural damage, necessary repairs, natural disaster, or another serious event, the Lessor may temporarily move the Tenant to reasonably comparable accommodation where available.</p>
  <p class="loose">If continued occupancy becomes impossible or unreasonable for a substantial period, the Parties may terminate this Agreement or make another suitable arrangement, subject to applicable law. Any rent adjustment or refund shall be decided fairly according to the circumstances and applicable law.</p>

  <h2>13. TERMINATION AND CHECK-OUT</h2>
  <h3>13.1 Tenant Notice</h3>
  <p class="loose">The Tenant must give the Lessor written notice at least two (2) weeks before the intended move-out date. Giving notice does not release the Tenant from the three (3)-month minimum occupancy or other obligations properly due under this Agreement.</p>
  <h3>13.2 Termination by the Lessor</h3>
  <p class="loose">Subject to applicable law, the Lessor may terminate this Agreement for:</p>
  @foreach([
    'a' => 'non-payment of rent, as provided in Section 3.4;',
    'b' => 'a material or repeated violation of this Agreement;',
    'c' => 'a material or repeated violation of the Dormitory Rules and Regulations;',
    'd' => 'a material misrepresentation or false information given in connection with the tenancy;',
    'e' => 'illegal use of the premises;',
    'f' => 'conduct that seriously threatens the safety or security of people or property; or',
    'g' => 'any other lawful ground for termination or ejectment.',
  ] as $letter => $ground)
    <table class="num alpha"><tr><td class="n">({{ $letter }})</td><td>{{ $ground }}</td></tr></table>
  @endforeach
  <p class="loose">The Lessor shall follow any notice, demand, or legal procedure required by Philippine law.</p>
  <h3>13.3 Check-Out</h3>
  <p class="loose">At check-out, the Tenant shall follow the Check-out Procedures, including: (a) inspection of the assigned accommodation; (b) settlement of outstanding obligations; (c) return of keys and dormitory property; and (d) vacating the premises by 2:00 PM on the agreed move-out date, unless the Lessor approves otherwise.</p>
  <h3>13.4 Belongings Left Behind</h3>
  <p class="loose">If the Tenant leaves belongings after check-out, the Lessor shall notify the Tenant and keep them safely for a reasonable period of at least thirty (30) days. The Lessor shall not sell, discard, or withhold them except as the law allows.</p>

  <h2>14. APPLICANT DECLARATION AND VERIFICATION</h2>
  <h3>14.1 Declaration</h3>
  <p class="loose">The Tenant declares that: (a) they are at least eighteen (18) years old; (b) the information in this Agreement and the Personal Information Form is true, accurate, and complete to the best of their knowledge; and (c) the documents submitted with the application are authentic to the best of their knowledge.</p>
  <h3>14.2 Material Misrepresentation</h3>
  <p class="loose">A material false statement, misrepresentation, or intentional omission about information relevant to the tenancy may result in: (a) rejection of the application; (b) cancellation of the application or reservation; or (c) termination of this Agreement, subject to applicable law. Any security deposit shall be applied and refunded under Section 4 and shall not be automatically forfeited because of termination.</p>
  <h3>14.3 Verification</h3>
  <p class="loose">Where reasonably necessary to evaluate the application or check information material to the tenancy, the Tenant authorizes the Lessor to verify specific information the Tenant provided, including by contacting the Tenant’s school or employer and emergency contact, subject to data privacy laws and the Privacy Notice. Verification shall be limited to information reasonably relevant to the tenancy.</p>
  <h3>14.4 Documents Received</h3>
  <p class="loose">By signing, the Tenant confirms receiving or being given access to the following, and having had the chance to read them and ask questions before signing:</p>
  {{-- Ticked only for what the applicant actually received and confirmed online. --}}
  <div class="checks">
    <div>{!! $box($tenantSignature) !!}&nbsp; Payments and Fees Schedule</div>
    <div>{!! $box($tenantSignature) !!}&nbsp; Dormitory Rules and Regulations</div>
    <div>{!! $box(false) !!}&nbsp; Check-out Procedures</div>
    <div>{!! $box(false) !!}&nbsp; Pureza Station Dormitory Privacy Notice</div>
    <div>{!! $box($tenantSignature) !!}&nbsp; Personal Information and Emergency Contact Form</div>
  </div>

  <h2>15. NOTICES AND COMMUNICATIONS</h2>
  <p class="loose">Unless the law requires a particular form of notice, notices under this Agreement may be given: (a) personally; (b) to the Tenant’s registered email address; (c) to the Tenant’s registered mobile number; (d) through NEST.PH; or (e) through another channel the Parties agree on. Nothing in this Section replaces any formal notice, demand, or service required by Philippine law.</p>

  <h2>16. GOVERNING LAW AND DISPUTE RESOLUTION</h2>
  <p class="loose">This Agreement is governed by the laws of the Republic of the Philippines. The Parties shall first try in good faith to settle any dispute amicably. Where barangay conciliation or another pre-litigation step is required by law, the Parties shall complete it before going to court.</p>
  <p class="loose">Subject to the rules on jurisdiction and venue, any court case arising from this Agreement shall be filed before the proper courts of Manila City.</p>

  <h2>17. ENTIRE AGREEMENT AND ATTACHMENTS</h2>
  <p class="loose">This Agreement, together with (a) the Payments and Fees Schedule; (b) the Dormitory Rules and Regulations; (c) the Check-out Procedures; (d) the Pureza Station Dormitory Privacy Notice; and (e) the Personal Information and Emergency Contact Form, is the whole agreement between the Parties about the Tenant’s occupancy.</p>
  <p class="loose">If this Agreement and an attached document conflict, this Agreement prevails unless the law requires otherwise. The Tenant shall receive a signed copy of this Agreement, and a scanned or electronic copy has the same effect as the original.</p>

  <h2>18. AMENDMENTS</h2>
  <p class="loose">A material amendment to this Agreement must be in writing and acknowledged by both Parties, except for rule changes the Lessor makes under Section 5.3.</p>

  <h2>19. SEVERABILITY</h2>
  <p class="loose">If a provision of this Agreement is declared invalid, unlawful, or unenforceable by a competent authority, the remaining provisions stay valid and enforceable to the fullest extent the law allows.</p>

  <h2>20. NO WAIVER</h2>
  <p class="loose">A delay or failure by either Party to exercise a right under this Agreement is not a waiver of that right. A waiver of one violation or obligation is not a waiver of any later one.</p>

  {{-- The original starts SIGNATURES on its own page. --}}
  <div class="page-break"></div>
  <h2>SIGNATURES</h2>
  <p class="loose">By signing below, the Parties confirm that they have read and understood this Agreement and voluntarily agree to its terms.</p>

  @include('documents._signature', ['image' => $tenantSignature, 'name' => $tenantName, 'date' => $signedAt, 'caption' => 'Tenant’s Signature over Printed Name'])

  @include('documents._signature', ['image' => $emergencySignature, 'name' => $emergencyName, 'date' => $signedAt, 'caption' => 'Emergency Contact’s Signature over Printed Name'])
  <p class="consent">{!! $box($emergencyConsent && $emergencySignature) !!} I agree to receive billing reminders and overdue notices for the<br>Tenant’s account as described in Section 9.3.</p>

  @include('documents._signature', ['image' => null, 'name' => null, 'date' => null, 'caption' => 'Lessor / Authorized Representative (Signature over Printed Name and Position)'])

  <div class="keep">
    <h2 class="plain">FOR ADMIN USE ONLY</h2>
    <table class="admin">
      <tr><td>Security Deposit Paid: ____________</td><td>Advance Rent Paid: ____________</td></tr>
      <tr><td>Date of Payment: ____________</td><td>Recorded in NEST.PH by: ____________</td></tr>
      <tr>
        <td>Emergency Contact Consent:<br>{!! $box($emergencySignature && $emergencyConsent) !!} Yes &nbsp;{!! $box($emergencySignature && ! $emergencyConsent) !!} No</td>
        <td>Billing Reminders to Emergency Contact:<br>{!! $box($emergencySignature && $emergencyConsent) !!} On &nbsp;{!! $box($emergencySignature && ! $emergencyConsent) !!} Off</td>
      </tr>
      <tr><td>Verified by: ____________</td><td>Date: ____________</td></tr>
    </table>
  </div>
</div>
