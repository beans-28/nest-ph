/*
 * Keyboard-friendly pop-ups, for every page at once.
 *
 * Any pop-up marked role="dialog" + aria-modal="true" (it blocks the page
 * behind it) gets, while it is open:
 *  1. Focus moves into it, so keyboard and screen-reader users start there.
 *  2. Tab / Shift+Tab stay inside it instead of wandering behind it.
 *  3. Esc closes it (if the page didn't already handle Esc itself).
 *  4. When it closes, focus goes back to the button that opened it.
 *
 * Pages don't need to change anything: we notice pop-ups opening/closing by
 * watching for them to become visible or hidden.
 */
(function () {
  const DIALOG = '[role="dialog"][aria-modal="true"], [role="alertdialog"][aria-modal="true"]';
  const FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"]), [contenteditable="true"]';
  const CLOSE = '[data-close], [data-dismiss], .modal-close, .close-btn, .drawer-close, .lightbox-close, button[aria-label^="Close" i], button[aria-label^="Cancel" i], [data-role="close"]';

  const open = new Map();   // dialog -> element focused before it opened
  let lastPointerTarget = null;
  let usingTouch = false;

  // Remember whether the person is using a keyboard or a mouse/finger, so the
  // green focus ring (typography.css) only shows for keyboard users and
  // doesn't flash around every field someone taps on a phone.
  const root = document.documentElement;
  document.addEventListener('keydown', e => {
    if (e.key === 'Tab' || e.key.startsWith('Arrow')) root.classList.add('kbd-nav');
  }, true);
  document.addEventListener('pointerdown', e => {
    root.classList.remove('kbd-nav');
    usingTouch = e.pointerType === 'touch' || e.pointerType === 'pen';
  }, true);

  const visible = el => {
    if (!el.isConnected || el.closest('[hidden], [aria-hidden="true"]')) return false;
    if (!el.getClientRects().length) return false;
    const cs = getComputedStyle(el);
    return cs.visibility !== 'hidden' && cs.display !== 'none';
  };
  const focusables = d => Array.from(d.querySelectorAll(FOCUSABLE)).filter(visible);
  const topDialog = () => Array.from(open.keys()).pop();

  function onOpen(d) {
    const from = document.activeElement && document.activeElement !== document.body
      ? document.activeElement : lastPointerTarget;
    open.set(d, from);
    // Let the page's own code place focus first (e.g. on a specific field).
    setTimeout(() => {
      if (!open.has(d) || d.contains(document.activeElement)) return;
      const first = d.querySelector('[autofocus]') || focusables(d)[0];
      // On touch screens, focusing a text field would pop the on-screen
      // keyboard over the pop-up before it's even read, so we focus the
      // pop-up itself instead. Keyboard users still land on the first control.
      const typing = first && first.matches('input:not([type="checkbox"], [type="radio"], [type="button"], [type="submit"], [type="file"], [type="range"]), textarea, select, [contenteditable="true"]');
      if (first && !(usingTouch && typing)) first.focus({ preventScroll: true });
      else {
        if (!d.hasAttribute('tabindex')) { d.setAttribute('tabindex', '-1'); d.style.outline = 'none'; }
        d.focus({ preventScroll: true });
      }
    }, 60);
  }

  function onClose(d) {
    const back = open.get(d);
    open.delete(d);
    // Only give focus back if it was lost (still in the closed pop-up or on <body>).
    setTimeout(() => {
      const a = document.activeElement;
      if (back && back.isConnected && visible(back) && (!a || a === document.body || d.contains(a))) {
        back.focus({ preventScroll: true });
      }
    }, 60);
  }

  let queued = false;
  function scan() {
    queued = false;
    document.querySelectorAll(DIALOG).forEach(d => {
      const isOpen = visible(d);
      if (isOpen && !open.has(d)) onOpen(d);
      else if (!isOpen && open.has(d)) onClose(d);
    });
    open.forEach((_, d) => { if (!d.isConnected) onClose(d); });
  }
  const queue = () => { if (!queued) { queued = true; requestAnimationFrame(scan); } };

  document.addEventListener('pointerdown', e => {
    const t = e.target.closest && e.target.closest(FOCUSABLE);
    if (t) lastPointerTarget = t;
  }, true);

  document.addEventListener('keydown', e => {
    const d = topDialog();
    if (!d) return;

    if (e.key === 'Tab') {
      const items = focusables(d);
      if (!items.length) { e.preventDefault(); return; }
      const first = items[0], last = items[items.length - 1];
      if (!d.contains(document.activeElement)) { e.preventDefault(); first.focus(); }
      else if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }

    if (e.key === 'Escape') {
      // Give the page's own Esc handling a moment; if the pop-up is still
      // open afterwards, press its close button for the user.
      setTimeout(() => {
        if (!open.has(d) || !visible(d)) return;
        const btn = Array.from(d.querySelectorAll(CLOSE)).find(visible)
          || Array.from((d.parentElement || d).querySelectorAll(CLOSE)).find(visible);
        if (btn) btn.click();
      }, 80);
    }
  });

  function start() {
    new MutationObserver(queue).observe(document.body, {
      subtree: true, childList: true, attributes: true,
      attributeFilter: ['class', 'style', 'hidden', 'aria-hidden', 'open'],
    });
    scan();
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
  else start();
})();
