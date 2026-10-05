{{--
  Photo lightbox. Any link with [data-lightbox="group"] opens its image in an
  on-page viewer instead of a new tab. Links sharing a group value become one
  gallery (arrow keys / swipe to move between them). Without JS, the plain
  link still works.
--}}
<dialog class="plb" id="photoLightbox" aria-label="Photo viewer">
  <div class="plb-stage">
    <img class="plb-img" alt="">
  </div>
  <div class="plb-bar">
    <span class="plb-count" aria-live="polite"></span>
    <button type="button" class="plb-btn plb-close" aria-label="Close photo">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
    </button>
  </div>
  <button type="button" class="plb-btn plb-nav plb-prev" aria-label="Previous photo">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
  </button>
  <button type="button" class="plb-btn plb-nav plb-next" aria-label="Next photo">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
  </button>
</dialog>

<style>
  .plb { position: fixed; inset: 0; width: 100vw; height: 100dvh; max-width: none; max-height: none; margin: 0; padding: 0; border: 0; background: transparent; color: #fff; overflow: hidden; }
  .plb::backdrop { background: rgba(12, 10, 9, .9); backdrop-filter: blur(4px); }
  .plb[open] { animation: plb-in .18s ease-out; }
  .plb-stage { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; padding: 64px 72px; }
  .plb-img { max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 6px; box-shadow: 0 20px 60px rgba(0,0,0,.5); user-select: none; transition: opacity .15s ease; }
  .plb-img.loading { opacity: 0; }
  .plb-bar { position: absolute; top: 0; left: 0; right: 0; display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; }
  .plb-count { font-size: 14px; font-variant-numeric: tabular-nums; opacity: .85; }
  .plb-btn { display: inline-flex; align-items: center; justify-content: center; width: 44px; height: 44px; border: 0; border-radius: 999px; background: rgba(255,255,255,.12); color: #fff; cursor: pointer; transition: background .15s; }
  .plb-btn:hover { background: rgba(255,255,255,.24); }
  .plb-btn:focus-visible { outline: 2px solid #fff; outline-offset: 2px; }
  .plb-btn svg { width: 22px; height: 22px; }
  .plb-nav { position: absolute; top: 50%; transform: translateY(-50%); }
  .plb-prev { left: 16px; }
  .plb-next { right: 16px; }
  .plb.single .plb-nav, .plb.single .plb-count { visibility: hidden; }
  [data-lightbox] { cursor: zoom-in; }
  @media (max-width: 640px) {
    .plb-stage { padding: 64px 8px 80px; }
    .plb-nav { top: auto; bottom: 16px; transform: none; }
  }
  @keyframes plb-in { from { opacity: 0; } to { opacity: 1; } }
  @media (prefers-reduced-motion: reduce) { .plb[open] { animation: none; } .plb-img { transition: none; } }
</style>

<script>
(function () {
  const dlg = document.getElementById('photoLightbox');
  if (!dlg || typeof dlg.showModal !== 'function') return; // old browser: links open normally
  const img = dlg.querySelector('.plb-img');
  const count = dlg.querySelector('.plb-count');
  let items = [], index = 0, opener = null;

  function show(i) {
    index = (i + items.length) % items.length;
    const link = items[index];
    img.classList.add('loading');
    img.onload = () => img.classList.remove('loading');
    img.src = link.href;
    img.alt = link.querySelector('img')?.alt || 'Photo';
    count.textContent = (index + 1) + ' of ' + items.length;
  }

  document.addEventListener('click', (e) => {
    const link = e.target.closest('a[data-lightbox]');
    if (!link || e.ctrlKey || e.metaKey || e.shiftKey) return; // allow "open in new tab" on purpose
    e.preventDefault();
    const group = link.dataset.lightbox;
    items = Array.from(document.querySelectorAll('a[data-lightbox]')).filter(a => a.dataset.lightbox === group);
    opener = link;
    dlg.classList.toggle('single', items.length < 2);
    show(items.indexOf(link));
    dlg.showModal();
    dlg.querySelector('.plb-close').focus();
  });

  dlg.querySelector('.plb-close').addEventListener('click', () => dlg.close());
  dlg.querySelector('.plb-prev').addEventListener('click', () => show(index - 1));
  dlg.querySelector('.plb-next').addEventListener('click', () => show(index + 1));
  // Clicking the dark area around the photo closes it.
  dlg.addEventListener('click', (e) => { if (e.target === dlg || e.target.classList.contains('plb-stage')) dlg.close(); });
  dlg.addEventListener('keydown', (e) => {
    if (items.length < 2) return;
    if (e.key === 'ArrowLeft') show(index - 1);
    if (e.key === 'ArrowRight') show(index + 1);
  });
  dlg.addEventListener('close', () => { img.removeAttribute('src'); opener?.focus(); });

  // Swipe left/right on phones.
  let startX = null;
  dlg.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; }, { passive: true });
  dlg.addEventListener('touchend', (e) => {
    if (startX === null || items.length < 2) return;
    const dx = e.changedTouches[0].clientX - startX;
    if (Math.abs(dx) > 50) show(index + (dx < 0 ? 1 : -1));
    startX = null;
  });
})();
</script>
