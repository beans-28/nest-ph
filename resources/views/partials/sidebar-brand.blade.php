{{--
    Top of every sidebar: the DORM's logo and name (dual branding -- under
    the Data Privacy Act the dorm is the PIC, NEST.PH is the PIP). Falls back
    to the NEST.PH logo if the dorm hasn't uploaded one. $brandDormName and
    $brandLogoUrl come from AppServiceProvider.
--}}
<div class="sidebar-logo" title="{{ $brandDormName }}"><img src="{{ $brandLogoUrl ?? asset('images/nestph.png') }}" alt="" class="logo-img{{ $brandLogoUrl ? ' logo-img-dorm' : '' }}"><span class="logo-text">{{ $brandDormName }}</span></div>
