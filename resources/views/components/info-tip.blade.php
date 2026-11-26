{{--
  Small "i" icon that shows extra details on hover, keyboard focus or tap.
  Usage: <x-info-tip text="Shows on your public profile." />
  Use it for background details people only sometimes need, so pages stay clean.

  - Hover / keyboard focus shows it; tap toggles it (phones); Escape or a
    tap elsewhere closes it.
  - The bubble is display:none while closed, so it never widens the page
    on phones, and it is nudged left when it would run off the screen.
  - Screen readers hear "More info" plus the text (aria-describedby).
--}}
@props(['text'])
@php($tipId = 'tip-'.\Illuminate\Support\Str::random(8))
@once
<style>
  .info-tip{ position:relative; display:inline-flex; align-items:center; justify-content:center; width:18px; height:18px; margin-left:6px; padding:0; vertical-align:middle; border:1.5px solid currentColor; border-radius:50%; background:transparent; color:var(--text-mid, #5b6b60); font-family:var(--font-body, 'Plus Jakarta Sans', system-ui, sans-serif); font-size:11px; font-weight:700; font-style:normal; line-height:1; letter-spacing:0; text-transform:none; cursor:help; flex-shrink:0; }
  /* Bigger invisible tap area (about 34px) around the small icon. */
  .info-tip::after{ content:''; position:absolute; inset:-8px; }
  .info-tip:hover, .info-tip:focus-visible, .info-tip.open{ color:var(--text-dark, #243026); }
  .info-tip:focus-visible{ outline:2px solid var(--green-accent, #2f6f3c); outline-offset:2px; }
  .info-tip-bubble{ position:absolute; top:calc(100% + 8px); left:-8px; z-index:60; display:none; width:max-content; max-width:min(280px, calc(100vw - 32px)); padding:9px 12px; border-radius:8px; background:#2b2a28; color:#fff; font-family:var(--font-body, 'Plus Jakarta Sans', system-ui, sans-serif); font-size:12.5px; font-weight:400; font-style:normal; line-height:1.5; letter-spacing:normal; text-transform:none; text-align:left; white-space:normal; cursor:auto; box-shadow:0 6px 18px rgba(0,0,0,.18); }
  .info-tip-bubble::before{ content:''; position:absolute; bottom:100%; left:var(--arrow-x, 14px); border:6px solid transparent; border-bottom-color:#2b2a28; }
  .info-tip:hover .info-tip-bubble, .info-tip:focus-visible .info-tip-bubble, .info-tip.open .info-tip-bubble{ display:block; }
  .info-tip.is-dismissed .info-tip-bubble{ display:none; }
</style>
<script>
(function(){
  // Keep an open bubble inside the screen.
  function place(tip){
    const b = tip.querySelector('.info-tip-bubble');
    if(!b) return;
    b.style.left = ''; b.style.setProperty('--arrow-x', '14px');
    requestAnimationFrame(() => {
      const r = b.getBoundingClientRect();
      if(!r.width) return;
      const over = r.right - (document.documentElement.clientWidth - 12);
      if(over > 0){
        b.style.left = (-8 - over) + 'px';
        b.style.setProperty('--arrow-x', (14 + over) + 'px');
      }
    });
  }
  const closeAll = except => document.querySelectorAll('.info-tip.open').forEach(t => { if(t !== except){ t.classList.remove('open'); t.setAttribute('aria-expanded', 'false'); } });
  document.addEventListener('click', e => {
    const el = e.target instanceof Element ? e.target : null;
    const tip = el?.closest('.info-tip');
    if(tip && !el.closest('.info-tip-bubble')){
      e.preventDefault(); e.stopPropagation(); // never toggle a checkbox/label it sits in
      const open = !tip.classList.contains('open');
      closeAll(tip);
      tip.classList.toggle('open', open);
      tip.classList.remove('is-dismissed');
      tip.setAttribute('aria-expanded', String(open));
      if(open) place(tip);
      return;
    }
    if(!tip) closeAll();
  }, true);
  document.addEventListener('mouseover', e => { const t = e.target.closest?.('.info-tip'); if(t) place(t); });
  document.addEventListener('focusin', e => { const t = e.target.closest?.('.info-tip'); if(t){ t.classList.remove('is-dismissed'); place(t); } });
  document.addEventListener('keydown', e => {
    if(e.key !== 'Escape') return;
    const t = document.activeElement?.closest?.('.info-tip');
    if(t){ t.classList.add('is-dismissed'); t.classList.remove('open'); t.setAttribute('aria-expanded', 'false'); }
    closeAll();
  });
})();
</script>
@endonce
<button type="button" {{ $attributes->merge(['class' => 'info-tip']) }} aria-label="More info" aria-describedby="{{ $tipId }}" aria-expanded="false">i<span class="info-tip-bubble" id="{{ $tipId }}" role="tooltip">{{ $text }}</span></button>
