/*
  Mobile sidebar drawer. Loaded by the admin and tenant sidebar partials,
  so every page that includes a sidebar gets it without touching the
  page's own <script> block.

  Desktop (wider than 860px): does nothing. Each page's own script keeps
  handling the hamburger (collapse to icons, saved in localStorage).

  Mobile (860px and below): the sidebar is hidden off-screen (see the
  @media block in admin.css / tenant.css). Tapping the hamburger slides
  it in over the page with a dimmed backdrop. Tap the backdrop, press
  Escape, or pick a link to close it.

  How it avoids the page's own hamburger handler: this listener runs on
  `document` in the capture phase, which fires BEFORE the click reaches
  the hamburger button. On mobile it calls stopPropagation(), so the
  page's "collapse" toggle never runs and localStorage is left alone.
*/
(function () {
  var MOBILE = window.matchMedia('(max-width: 860px)');
  var sidebar = document.getElementById('sidebar');
  if (!sidebar) return;

  var backdrop = document.createElement('div');
  backdrop.className = 'sidebar-backdrop';
  document.body.appendChild(backdrop);

  function hamburger() { return document.getElementById('hamburgerBtn'); }

  function setOpen(open) {
    sidebar.classList.toggle('open', open);
    backdrop.classList.toggle('show', open);
    document.body.classList.toggle('drawer-open', open);
    var btn = hamburger();
    if (btn) btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (open) {
      var first = sidebar.querySelector('.nav-item.active, .nav-item');
      if (first) first.focus();
    } else if (btn && sidebar.contains(document.activeElement)) {
      btn.focus();
    }
  }

  document.addEventListener('click', function (e) {
    if (!MOBILE.matches) return;
    if (e.target.closest && e.target.closest('#hamburgerBtn')) {
      e.stopPropagation();
      setOpen(!sidebar.classList.contains('open'));
    }
  }, true);

  backdrop.addEventListener('click', function () { setOpen(false); });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && sidebar.classList.contains('open')) setOpen(false);
  });

  // Rotating a tablet or resizing past 860px: drop back to the desktop layout.
  function onChange() { if (!MOBILE.matches) setOpen(false); }
  if (MOBILE.addEventListener) MOBILE.addEventListener('change', onChange);
  else if (MOBILE.addListener) MOBILE.addListener(onChange);
})();
