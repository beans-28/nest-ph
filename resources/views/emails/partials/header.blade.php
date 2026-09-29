{{-- Email header: the DORM's name/logo (it is the PIC under the Data Privacy Act). --}}
<td style="background:linear-gradient(90deg,#567357,#a2d9a4);padding:22px 32px;">
    @if($brandLogoUrl)
        <img src="{{ $brandLogoUrl }}" alt="" width="32" height="32" style="vertical-align:middle;border-radius:6px;background:#ffffff;margin-right:10px;object-fit:contain;">
    @endif
    <span style="color:#ffffff;font-size:20px;font-weight:700;letter-spacing:0.02em;vertical-align:middle;">{{ $brandDormName }}</span>
</td>
