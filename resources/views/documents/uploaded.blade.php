{{--
  Signature and acknowledgment page for the documents the owner uploaded
  as their own PDFs (see DormitoryProfile::uploadedDocumentPath()). Those
  files can't be filled in, so the tenant's details and signatures go here
  instead. In a signed set, each title links to the exact copy signed.
--}}
<div class="uploaded">
  <div class="doc-title">SIGNATURE AND ACKNOWLEDGMENT</div>

  <p class="loose">By signing below, the Tenant confirms that he or she has read, understood, and voluntarily agrees to the following documents provided by the dormitory:</p>

  @foreach($uploadedDocuments as $i => $doc)
    <table class="num"><tr><td class="n">{{ $i + 1 }}.</td><td>
      @if($doc['url'])<a href="{{ $doc['url'] }}">{{ $doc['title'] }}</a>@else{{ $doc['title'] }}@endif
    </td></tr></table>
  @endforeach

  <h2>TENANT DETAILS</h2>
  <table class="num"><tr><td>Name: {{ $tenantName ?: '____________________' }}</td></tr></table>
  <table class="num"><tr><td>Home address: {{ $homeAddress ?: '____________________' }}</td></tr></table>
  <table class="num"><tr><td>Mobile: {{ $mobile ?: '__________' }} &nbsp;|&nbsp; Email: {{ $email ?: '__________' }}</td></tr></table>
  <table class="num"><tr><td>Room: {{ $roomNo ?: '____' }} &nbsp;|&nbsp; Bed: {{ $bedNo ?: '____' }} &nbsp;|&nbsp; Monthly rent: {{ $monthlyRent ? '₱' . number_format($monthlyRent, 2) : '__________' }}</td></tr></table>
  <table class="num"><tr><td>Start date: {{ $startDate ?: '__________' }} &nbsp;|&nbsp; End date: {{ $endDate ?: '__________' }}</td></tr></table>

  <div class="keep">
    <h2>SIGNATURES</h2>
    @include('documents._signature', ['image' => $tenantSignature, 'name' => $tenantName, 'date' => $signedAt, 'caption' => 'Tenant’s Signature over Printed Name'])

    @include('documents._signature', ['image' => $emergencySignature, 'name' => $emergencyName, 'date' => $signedAt, 'caption' => 'Emergency Contact’s Signature over Printed Name'])
    <p class="consent">[{{ $emergencyConsent && $emergencySignature ? 'X' : ' ' }}] I agree to receive billing reminders and overdue notices for the Tenant’s account.</p>

    @include('documents._signature', ['image' => null, 'name' => null, 'date' => null, 'caption' => 'Lessor / Authorized Representative (Signature over Printed Name and Position)'])
  </div>
</div>
