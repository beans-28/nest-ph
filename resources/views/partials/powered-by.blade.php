{{--
    Small "Powered by NEST.PH" mark at the bottom of every sidebar -- the
    platform's trademark, always visible but never competing with the dorm's.
    Links to NEST.PH support, since there is no Settings > About page.
--}}
<a class="powered-by" href="mailto:{{ config('mail.from.address') }}" title="NEST.PH support: {{ config('mail.from.address') }}">
  <img src="{{ asset('images/nestph.png') }}" alt="">
  <span class="label">Powered by <strong>NEST.PH</strong></span>
</a>
