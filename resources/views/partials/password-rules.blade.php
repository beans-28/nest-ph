{{--
    Live password checklist. Include right under a "new password" input:
        @include('partials.password-rules', ['for' => 'newPassword'])

    Each requirement turns green with a check as the user types. It only
    guides the user; the real check happens on the server
    (Password::defaults() in AppServiceProvider). Keep the two in sync.
--}}
@once
<style>
  .pw-rules{ list-style:none; margin:8px 0 16px; padding:0; display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:4px 14px; font-size:12px; color:#5f5a57; }
  .pw-rules li{ display:flex; align-items:center; gap:6px; transition:color .15s ease; }
  .pw-rules li::before{ content:""; width:14px; height:14px; flex-shrink:0; border-radius:50%; border:1.5px solid #a6b69f; box-sizing:border-box; transition:background-color .15s ease, border-color .15s ease; }
  .pw-rules li.met{ color:#197335; }
  .pw-rules li.met::before{ border-color:#197335; background:#197335 url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23fff' stroke-width='4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 6L9 17l-5-5'/%3E%3C/svg%3E") center / 9px no-repeat; }
  @media (max-width:380px){ .pw-rules{ grid-template-columns:1fr; } }
  @media (prefers-reduced-motion: reduce){ .pw-rules li, .pw-rules li::before{ transition:none; } }
</style>
<script>
  // Wires every checklist to its input. Runs after the page is parsed.
  document.addEventListener('DOMContentLoaded', function () {
    var tests = {
      length: function (v) { return v.length >= 8; },
      upper: function (v) { return /\p{Lu}/u.test(v); },
      number: function (v) { return /\d/.test(v); },
      symbol: function (v) { return /[^\p{L}\p{N}\s]/u.test(v); }
    };
    document.querySelectorAll('.pw-rules[data-for]').forEach(function (list) {
      var input = document.getElementById(list.dataset.for);
      if (!input) return;
      var update = function () {
        list.querySelectorAll('li[data-rule]').forEach(function (li) {
          var met = tests[li.dataset.rule](input.value);
          li.classList.toggle('met', met);
          li.querySelector('.sr-state').textContent = met ? ' (done)' : '';
        });
      };
      input.addEventListener('input', update);
      update();
    });
  });
</script>
@endonce
<ul class="pw-rules" data-for="{{ $for }}" id="{{ $for }}Rules" aria-label="Password requirements">
  <li data-rule="length">At least 8 characters<span class="sr-state" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0)"></span></li>
  <li data-rule="upper">1 uppercase letter<span class="sr-state" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0)"></span></li>
  <li data-rule="number">1 number<span class="sr-state" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0)"></span></li>
  <li data-rule="symbol">1 symbol (e.g. ! @ # ?)<span class="sr-state" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0)"></span></li>
</ul>
