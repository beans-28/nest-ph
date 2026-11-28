{{--
    Cloudflare Turnstile widget (bot check). Usage:
        @include('partials.turnstile', ['id' => 'loginTurnstile'])
    Then in JS, send turnstileToken('loginTurnstile') as "cf_turnstile_response",
    and call turnstileReset('loginTurnstile') after each request (tokens are single-use).
    Renders nothing when TURNSTILE_SITE_KEY is empty.

    If the check can't load or gets stuck (common with Safari privacy
    settings, iCloud Private Relay, ad/content blockers, or a slow network),
    a short message with a "Try again" button appears under it, so people
    are never left stuck with no way forward.
--}}
@php($turnstileSiteKey = config('services.turnstile.site_key'))
@if ($turnstileSiteKey)
    <div class="ts-wrap">
        <div id="{{ $id }}" class="cf-turnstile" data-sitekey="{{ $turnstileSiteKey }}" style="margin: 12px 0 6px;"></div>
        <div class="ts-help" id="{{ $id }}Help" role="alert" hidden>
            <p class="ts-help-msg"></p>
            <button type="button" class="ts-help-btn" data-ts-retry="{{ $id }}">Try again</button>
        </div>
    </div>
@endif
@once
    @if ($turnstileSiteKey)
        <style>
            .ts-help{ margin:4px 0 12px; padding:10px 12px; border:1px solid #f0d9a8; background:#fff8e8; border-radius:8px; font-size:12.5px; line-height:1.5; color:#5c4813; text-align:left; }
            .ts-help[hidden]{ display:none; }
            .ts-help-msg{ margin:0 0 8px; }
            .ts-help-btn{ font:inherit; font-weight:600; font-size:12.5px; padding:6px 14px; border-radius:6px; border:1px solid #c9a44f; background:#fff; color:#5c4813; cursor:pointer; }
            .ts-help-btn:focus-visible{ outline:2px solid #197335; outline-offset:2px; }
        </style>
    @endif
    <script>
        (function () {
            var SITE_KEY = @json($turnstileSiteKey);
            var MSG = {
                blocked: 'The security check couldn’t load. If you use Safari, an ad/content blocker, or iCloud Private Relay, try turning it off for this site, or open the site in another browser. Then press Try again.',
                failed: 'The security check didn’t go through. Please press Try again. If it keeps failing, check your internet connection or try another browser.',
                expired: 'The security check timed out. Please press Try again.',
                pending: 'Please wait for the security check above to finish (a green ✓ appears), then try again.'
            };
            var widgets = {};      // container id -> Turnstile widget id
            var tokens = {};       // container id -> latest token
            var scriptState = SITE_KEY ? 'loading' : 'off';

            function help(id, kind) {
                var box = document.getElementById(id + 'Help');
                if (!box) return;
                if (!kind) { box.hidden = true; return; }
                box.querySelector('.ts-help-msg').textContent = MSG[kind];
                box.hidden = false;
            }

            function renderAll() {
                document.querySelectorAll('.cf-turnstile[id]').forEach(function (el) {
                    if (widgets[el.id] !== undefined) return;
                    var id = el.id;
                    widgets[id] = window.turnstile.render(el, {
                        sitekey: SITE_KEY,
                        size: 'normal',     // compact 300px box; fits every phone
                        theme: 'light',     // match our light pages even if the device is in dark mode
                        retry: 'auto',
                        'refresh-expired': 'auto',
                        callback: function (t) { tokens[id] = t; help(id, null); },
                        'expired-callback': function () { tokens[id] = ''; },
                        'timeout-callback': function () { tokens[id] = ''; help(id, 'expired'); },
                        'error-callback': function () { tokens[id] = ''; help(id, 'failed'); return true; }
                    });
                });
            }

            function loadScript() {
                scriptState = 'loading';
                var s = document.createElement('script');
                s.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit&onload=nestTurnstileReady';
                s.async = true;
                s.onerror = function () {
                    scriptState = 'blocked';
                    s.remove();
                    document.querySelectorAll('.cf-turnstile[id]').forEach(function (el) { help(el.id, 'blocked'); });
                };
                document.head.appendChild(s);
                // Still nothing after 15 seconds? Say so instead of leaving people waiting.
                setTimeout(function () {
                    if (scriptState === 'loading') {
                        document.querySelectorAll('.cf-turnstile[id]').forEach(function (el) { help(el.id, 'blocked'); });
                    }
                }, 15000);
            }

            window.nestTurnstileReady = function () { scriptState = 'ready'; renderAll(); };

            window.turnstileToken = function (id) {
                var el = document.getElementById(id);
                if (!el) return '';
                var t = tokens[id] || (window.turnstile && widgets[id] !== undefined ? window.turnstile.getResponse(widgets[id]) : '') || '';
                if (!t) help(id, scriptState === 'blocked' ? 'blocked' : 'pending');
                return t;
            };

            window.turnstileReset = function (id) {
                tokens[id] = '';
                if (window.turnstile && widgets[id] !== undefined) window.turnstile.reset(widgets[id]);
            };

            document.addEventListener('click', function (e) {
                var btn = e.target.closest && e.target.closest('[data-ts-retry]');
                if (!btn) return;
                var id = btn.getAttribute('data-ts-retry');
                help(id, null);
                if (window.turnstile) {
                    if (widgets[id] === undefined) renderAll(); else window.turnstileReset(id);
                } else {
                    loadScript();
                }
            });

            if (SITE_KEY) {
                if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', loadScript);
                else loadScript();
            }
        })();
    </script>
@endonce
