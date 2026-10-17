{{--
    Cloudflare Turnstile widget (bot check). Usage:
        @include('partials.turnstile', ['id' => 'loginTurnstile'])
    Then in JS, send turnstileToken('loginTurnstile') as "cf_turnstile_response",
    and call turnstileReset('loginTurnstile') after each request (tokens are single-use).
    Renders nothing when TURNSTILE_SITE_KEY is empty.
--}}
@php($turnstileSiteKey = config('services.turnstile.site_key'))
@if ($turnstileSiteKey)
    <div id="{{ $id }}" class="cf-turnstile" data-sitekey="{{ $turnstileSiteKey }}" style="margin: 12px 0;"></div>
@endif
@once
    @if ($turnstileSiteKey)
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
    <script>
        function turnstileToken(id) {
            const input = document.querySelector('#' + id + ' [name="cf-turnstile-response"]');
            return input ? input.value : '';
        }
        function turnstileReset(id) {
            if (window.turnstile && document.getElementById(id)) window.turnstile.reset('#' + id);
        }
    </script>
@endonce
