/*
 * NestPager — one shared page-by-page control for long lists.
 *
 * Why it works in the browser (not the server): our list pages load the
 * data once, then search / filter / count it in JavaScript. Splitting the
 * data on the server would break those searches and tab counts, so we
 * only split what is *shown*.
 *
 * Two ways to use it:
 *
 * 1) Lists drawn by JavaScript:
 *      const pager = NestPager.create({ after: $('tableBody').closest('table'), onChange: render });
 *      function render(){
 *        const list = visibleRows();               // after search/filter
 *        const shown = pager.slice(list, filter + '|' + search); // this page's rows
 *        ...draw `shown`...
 *      }
 *      // The 2nd argument is the current search/filter; when it changes the
 *      // pager jumps back to page 1 by itself.
 *
 * 2) Lists already printed by Blade (@foreach):
 *      NestPager.dom(document.getElementById('reviewList'), { item: '.review-card' });
 */
(function () {
  const STYLE_ID = 'nest-pager-style';

  function injectStyle() {
    if (document.getElementById(STYLE_ID)) return;
    const s = document.createElement('style');
    s.id = STYLE_ID;
    s.textContent = `
      .np-pager{ display:flex; align-items:center; flex-wrap:wrap; gap:6px; padding:14px 16px; }
      .np-pager[hidden]{ display:none; }
      .np-pager .np-info{ font-size:12px; color:var(--text-light,#6b7280); margin-right:auto; }
      .np-pager button{ min-width:32px; height:32px; padding:0 8px; border-radius:8px; border:1px solid var(--border,#e5e7eb);
        background:#fff; color:var(--text-mid,#374151); font-size:12px; font-weight:600; cursor:pointer; font-family:inherit; display:inline-flex; align-items:center; justify-content:center;
        font-variant-numeric:tabular-nums; transition:border-color .15s ease, background-color .15s ease; }
      .np-pager button:not(:disabled):not(.active):hover{ border-color:var(--green-btn,#2f6b3a); color:var(--green-btn,#2f6b3a); }
      .np-pager button:focus-visible{ outline:2px solid var(--green-btn,#2f6b3a); outline-offset:2px; }
      .np-pager button.active{ background:var(--green-btn,#2f6b3a); border-color:var(--green-btn,#2f6b3a); color:#fff; }
      .np-pager button:disabled{ opacity:.4; cursor:not-allowed; }
      .np-pager .np-gap{ color:var(--text-light,#6b7280); padding:0 2px; }
      @media (max-width:600px){ .np-pager .np-info{ width:100%; margin-bottom:4px; } }
    `;
    document.head.appendChild(s);
  }

  // Page numbers to show: 1 … 4 5 6 … 12 (keeps the bar short on phones).
  function pageList(current, total) {
    if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);
    const out = [1];
    const from = Math.max(2, current - 1);
    const to = Math.min(total - 1, current + 1);
    if (from > 2) out.push('…');
    for (let p = from; p <= to; p++) out.push(p);
    if (to < total - 1) out.push('…');
    out.push(total);
    return out;
  }

  function create(opts) {
    injectStyle();
    const size = opts.pageSize || 10;
    const label = opts.label || 'results';
    let page = 1;
    let lastKey;

    const bar = document.createElement('nav');
    bar.className = 'np-pager';
    bar.setAttribute('aria-label', 'Pages of ' + label);
    bar.hidden = true;
    if (opts.after) opts.after.insertAdjacentElement('afterend', bar);

    function go(p, fromBtn) {
      page = p;
      if (opts.onChange) opts.onChange();
      // The buttons were redrawn, so put keyboard focus back on the same one
      // (or the current page if that one is now disabled).
      const again = bar.querySelector(`button[data-p="${fromBtn}"]:not(:disabled)`) || bar.querySelector('button.active');
      if (again) again.focus({ preventScroll: true });
      const top = opts.scrollTo || opts.after;
      if (top && top.getBoundingClientRect().top < 0) top.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function draw(total) {
      const pages = Math.max(1, Math.ceil(total / size));
      // Hide the bar entirely when everything fits on one page.
      if (total <= size) { bar.hidden = true; bar.innerHTML = ''; return; }
      bar.hidden = false;
      const start = (page - 1) * size + 1;
      const end = Math.min(page * size, total);
      bar.innerHTML =
        `<span class="np-info" aria-live="polite">Showing ${start} to ${end} of ${total} ${label}</span>` +
        `<button type="button" data-p="${page - 1}" ${page <= 1 ? 'disabled' : ''} aria-label="Previous page"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg></button>` +
        pageList(page, pages).map(p => p === '…'
          ? '<span class="np-gap" aria-hidden="true">…</span>'
          : `<button type="button" data-p="${p}" class="${p === page ? 'active' : ''}" aria-label="Page ${p}"${p === page ? ' aria-current="page"' : ''}>${p}</button>`).join('') +
        `<button type="button" data-p="${page + 1}" ${page >= pages ? 'disabled' : ''} aria-label="Next page"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg></button>`;
      bar.querySelectorAll('button[data-p]').forEach(b => b.addEventListener('click', () => go(Number(b.dataset.p), b.dataset.p)));
    }

    return {
      el: bar,
      /** Returns only the rows for the current page, and redraws the buttons. */
      slice(list, key) {
        // `key` = the current search/filter. When it changes, start at page 1.
        if (key !== undefined && key !== lastKey) { page = 1; lastKey = key; }
        const pages = Math.max(1, Math.ceil(list.length / size));
        if (page > pages) page = pages;
        draw(list.length);
        return list.slice((page - 1) * size, page * size);
      },
      /** Go back to page 1 (call when search/filter changes). */
      reset() { page = 1; },
    };
  }

  // For lists that Blade already printed: hide the items not on this page.
  function dom(container, opts) {
    if (!container) return null;
    const pager = create(Object.assign({ after: container, pageSize: 10 }, opts, {
      onChange: () => apply(),
    }));
    function apply() {
      const items = Array.from(container.querySelectorAll(opts.item));
      const shown = new Set(pager.slice(items));
      items.forEach(el => { el.style.display = shown.has(el) ? '' : 'none'; });
    }
    apply();
    return pager;
  }

  window.NestPager = { create, dom };
})();
