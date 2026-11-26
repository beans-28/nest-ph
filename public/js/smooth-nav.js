/*
  Faster-feeling page switches (all pages; linked next to typography.css).

  When the pointer rests on a link or a sidebar item (or it gets keyboard
  focus), the browser is told to fetch that page in the background, so the
  click usually lands on a page that is already downloaded. Only the HTML is
  fetched; no script on that page runs and nothing on the server changes.

  Uses the browser's Speculation Rules (Chrome/Edge). Other browsers ignore
  this file and load pages normally. Pair: the cross-fade between pages is
  the @view-transition rule in typography.css.

  Never prefetched: downloads/PDFs/exports, logout, API calls, other sites,
  links that open a new tab, and the page you're already on.
*/
// Back/Forward can restore a page exactly as it was left (the browser's
// back-forward cache). Close the bell and profile menus so a link picked
// from an open menu doesn't bring the page back with that menu still open.
window.addEventListener('pageshow', function (e) {
  if (!e.persisted) return;
  document.querySelectorAll('.notif-panel.open, .admin-notif-panel.open, .pm-panel.open').forEach(function (p) {
    p.classList.remove('open');
  });
  document.querySelectorAll('[aria-expanded="true"]:not(#hamburgerBtn)').forEach(function (b) {
    if (b.closest('.notif-panel, .pm-panel, .sidebar')) return;
    if (/notif|bell|profile|pm-/i.test(b.id + ' ' + b.className)) b.setAttribute('aria-expanded', 'false');
  });
});

(function () {
  if (!(window.HTMLScriptElement && HTMLScriptElement.supports && HTMLScriptElement.supports('speculationrules'))) return;
  if (navigator.connection && (navigator.connection.saveData || /2g/.test(navigator.connection.effectiveType || ''))) return;

  var SKIP = /\/(logout|api|public-api)(\/|$)|download|\.pdf|\/export|\/receipt|statement|demand-letter|eviction-notice|\/documents\/|policies-file/i;
  var done = new Set();

  function urlFor(el) {
    var href = el.getAttribute('data-href') || (el.tagName === 'A' ? el.getAttribute('href') : null);
    if (!href || href.charAt(0) === '#' || el.hasAttribute('download') || el.getAttribute('target') === '_blank') return null;
    var u;
    try { u = new URL(href, location.href); } catch (e) { return null; }
    if (u.origin !== location.origin || SKIP.test(u.pathname)) return null;
    u.hash = '';
    if (u.href === location.href.split('#')[0]) return null;
    return u.href;
  }

  function prefetch(e) {
    var el = e.target.closest && e.target.closest('a[href], [data-href]');
    if (!el) return;
    var url = urlFor(el);
    if (!url || done.has(url) || done.size >= 20) return;
    done.add(url);
    var s = document.createElement('script');
    s.type = 'speculationrules';
    s.textContent = JSON.stringify({ prefetch: [{ source: 'list', urls: [url] }] });
    document.head.appendChild(s);
  }

  // Short hover delay so sweeping the mouse across the sidebar doesn't fetch everything.
  var timer = null;
  document.addEventListener('pointerover', function (e) {
    clearTimeout(timer);
    timer = setTimeout(function () { prefetch(e); }, 65);
  }, { passive: true });
  document.addEventListener('pointerout', function () { clearTimeout(timer); }, { passive: true });
  document.addEventListener('focusin', prefetch);
  document.addEventListener('touchstart', prefetch, { passive: true });
})();
