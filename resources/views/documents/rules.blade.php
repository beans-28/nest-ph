{{--
  Dormitory Rules and Regulations -- word for word from
  PSD_Rules & Regulations.docx (Version 2). Only the tenant's signature,
  printed name and date are filled in.
--}}
@php
  $sections = [
    'A. CONDUCT AND RESPECT' => [
      1 => 'Treat all tenants, staff, and visitors with respect and consideration. Harassment, discrimination, and bullying are not tolerated.',
      2 => '<strong>Quiet hours</strong> are 10:00 PM to 6:00 AM. At all other times, tenants must still avoid unreasonable noise that disturbs others.',
      3 => '<strong>Curfew</strong> is from 11:00 PM to 4:00 AM. Tenants should be inside the dormitory during curfew hours. A tenant who must be out late (for example, because of night work or a class schedule) or expects to return during curfew must inform the management in advance. Emergencies are excepted, and the tenant should inform the management as soon as possible.',
      4 => 'Complaints and suggestions to improve the dormitory are welcome and may be sent to the management or through NEST.PH.',
    ],
    'B. VISITORS AND ROOM ACCESS' => [
      5 => 'Only registered tenants may enter the rooms. Visitors are received at the receiving area only. Overnight guests are not allowed.',
      6 => 'Tenants are responsible for their visitors and for any loss or damage they cause.',
      7 => 'Management may enter rooms for inspection, maintenance, and safety checks as stated in Section 7 of the Agreement.',
    ],
    'C. SAFETY AND PROHIBITED ITEMS' => [
      8 => 'Alcoholic beverages, smoking, and vaping are not allowed on the premises.',
      9 => 'Hazardous items such as gas, cooking stoves, flammable fuels, and firearms are strictly prohibited. A tenant found using or keeping them may be fined the amount listed in the Payments and Fees Schedule (currently ₱500), and the matter may be reported to the proper authorities where required by law.',
      10 => 'Drugs and other illegal substances are strictly prohibited. Possession may be reported to the proper authorities.',
      11 => 'Pets are not allowed on the premises.',
      12 => 'Do not tamper with fire alarms, extinguishers, or CCTV equipment, and do not block hallways, stairs, or exits. Follow the evacuation plan and staff instructions during a fire, earthquake, or other emergency.',
      13 => 'Do not bring or use appliances that draw high power (for example heaters, irons, or cooking appliances) unless the management allows them. Approved appliances may carry an electricity charge listed in the Payments and Fees Schedule.',
      14 => 'Cameras (CCTV) may be installed in common areas for safety and security. They are not placed in bedrooms or bathrooms. How footage is used and kept is explained in the Privacy Notice.',
    ],
    'D. CARE OF THE PREMISES' => [
      15 => 'Keep common areas clean and tidy after use and dispose of garbage in the proper place. Washing clothes in the dormitory is not allowed. A laundry service is available outside the dormitory.',
      16 => 'Use the air-conditioner only from 10:00 PM to 5:00 AM (aircon schedule), and keep doors and windows closed while it is running.',
      17 => 'When leaving the room, turn off all faucets, showers, lights, air-conditioners, and other devices. Close and lock the door.',
      18 => 'Do not move, remove, or swap beds, furniture, or rooms without the management’s approval. Do not transfer or sublet your bed to another person.',
      19 => 'Report any damage, defect, or unsafe condition promptly to the maintenance staff or through NEST.PH.',
      20 => 'The tenant pays the reasonable cost of repair or replacement for loss or damage caused by the tenant, the tenant’s visitors, or anyone the tenant is responsible for, as stated in Section 6.3 of the Agreement. Charges follow the Payments and Fees Schedule.',
      21 => 'If a key is lost or damaged, the tenant pays the key-duplication fee listed in the Payments and Fees Schedule (currently ₱50).',
    ],
    'E. LIABILITY' => [
      22 => 'Tenants must take reasonable care of their belongings and their own safety. The management’s responsibility for loss, damage, or injury is stated in Section 11 of the Agreement. Nothing in these Rules limits any liability that the law does not allow to be limited.',
    ],
    'F. BREACH AND DISCIPLINARY STEPS' => [
      23 => 'For a breach of these Rules, the management will usually follow these steps: (a) verbal reminder; (b) written warning; (c) fine, where one is listed in the Payments and Fees Schedule; and (d) termination under Section 13.2 of the Agreement.',
      24 => 'The management may skip steps for serious violations, such as illegal drugs, weapons, violence, or conduct that threatens the safety of others. Termination is always subject to the Agreement and Philippine law. The tenant may explain his or her side before a fine or termination is finalized.',
      25 => 'The management may update these Rules for reasonable administrative, operational, safety, or security reasons, with advance notice to tenants as stated in Section 5.3 of the Agreement.',
    ],
  ];
@endphp
<div class="rules">
  <div class="doc-title">DORMITORY RULES AND REGULATIONS</div>
  <div class="doc-version">Version 2 &nbsp;|&nbsp; Effective date: September 30, 2026</div>

  <p>These Rules form part of the Dormitory Tenant Agreement. They are meant to keep the dormitory safe, clean, and comfortable for everyone. Charges mentioned here are listed in the Payments and Fees Schedule.</p>

  @foreach($sections as $heading => $rules)
    <h2>{{ $heading }}</h2>
    @foreach($rules as $number => $rule)
      {{-- The rule text is fixed text from this file (with <strong> for the
           original's bold words), never user input. --}}
      <table class="num"><tr><td class="n">{{ $number }}.</td><td>{!! $rule !!}</td></tr></table>
    @endforeach
  @endforeach

  <div class="keep">
    <p class="loose" style="margin-top:14pt;">I have read and understood these Rules and agree to follow them.</p>
    @include('documents._signature', ['image' => $tenantSignature, 'name' => $tenantName, 'date' => $signedAt, 'caption' => 'Tenant’s Signature over Printed Name'])
  </div>
</div>
