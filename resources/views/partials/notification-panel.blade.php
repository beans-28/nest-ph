{{--
    Shared notification panel (admin and tenant bells). One look and one
    behaviour for both, so the two can't drift apart.

    Include once per bell:
        @include('partials.notification-panel', ['prefix' => 'admin'])
    That outputs the panel (#{prefix}NotifPanel) and, once per page, the
    styles plus window.NestNotifPanel. Each bell partial then wires its own
    data with:
        const ctl = NestNotifPanel({ prefix, bell, badge, onOpen, onMarkRead });
        ctl.setItems([{ key, kind, urgent, read, actor, text, title, body, ago, time, href }]);
        ctl.setError('Could not load notifications.');

    Item text: either actor + text ("<b>Juan</b> sent an application.") or
    title + body (tenant messages). kind picks the icon.
--}}
@once
<style>
  .notif-panel{ position:fixed; top:64px; right:16px; width:min(380px, calc(100vw - 32px)); background:#fff; border:1px solid var(--border, #e3e7e3); border-radius:14px; box-shadow:0 14px 40px rgba(20,30,24,.18); z-index:200; display:none; flex-direction:column; overflow:hidden; font-family:var(--font-body); }
  .notif-panel.open{ display:flex; }
  .notif-panel .np-head{ display:flex; align-items:center; justify-content:space-between; gap:8px; padding:14px 16px 8px; }
  .notif-panel .np-head h2{ margin:0; font-size:18px; font-weight:700; color:var(--text-dark, #26302a); }
  .notif-panel .np-mark-all{ border:none; background:none; padding:6px 4px; font:inherit; font-size:12.5px; font-weight:600; color:var(--green-accent, #2f6f3c); cursor:pointer; border-radius:6px; }
  .notif-panel .np-mark-all:hover{ text-decoration:underline; }
  .notif-panel .np-mark-all:disabled{ color:var(--text-mid, #5b6b60); opacity:.6; cursor:default; text-decoration:none; }
  .notif-panel .np-mark-all:focus-visible{ outline:2px solid var(--green-accent, #2f6f3c); outline-offset:1px; }
  .notif-panel .np-filters{ display:flex; gap:6px; padding:0 16px 10px; border-bottom:1px solid var(--border, #e3e7e3); }
  .notif-panel .np-filters button{ min-height:32px; border:none; background:none; padding:6px 12px; border-radius:999px; font:inherit; font-size:13px; font-weight:600; color:var(--text-mid, #5b6b60); cursor:pointer; }
  .notif-panel .np-filters button:hover{ background:rgba(0,0,0,.05); }
  .notif-panel .np-filters button[aria-pressed="true"]{ background:var(--brand-tint, var(--sage-100, #e6f0e8)); color:var(--green-accent, #2f6f3c); }
  .notif-panel .np-filters button:focus-visible{ outline:2px solid var(--green-accent, #2f6f3c); outline-offset:1px; }
  .notif-panel .np-list{ list-style:none; margin:0; padding:4px 0 6px; max-height:min(460px, calc(100vh - 180px)); overflow-y:auto; overscroll-behavior:contain; }
  .notif-panel .np-group{ padding:10px 16px 4px; font-size:12px; font-weight:700; color:var(--text-mid, #5b6b60); }
  .notif-panel .np-item{ position:relative; display:flex; align-items:flex-start; gap:12px; width:calc(100% - 12px); margin:0 6px; padding:10px 30px 10px 10px; border:none; border-radius:10px; background:none; color:var(--text-dark, #26302a); text-decoration:none; text-align:left; font:inherit; font-size:13px; line-height:1.4; cursor:pointer; }
  .notif-panel .np-item:hover{ background:rgba(0,0,0,.04); }
  .notif-panel .np-item:focus-visible{ outline:2px solid var(--green-accent, #2f6f3c); outline-offset:-2px; }
  .notif-panel .unread > .np-item{ background:var(--brand-tint, var(--sage-50, #eef5ef)); }
  .notif-panel .unread > .np-item:hover{ filter:brightness(.98); }
  .notif-panel .np-icon{ width:40px; height:40px; border-radius:50%; flex:none; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,.05); color:var(--green-accent, #2f6f3c); }
  .notif-panel .np-icon svg{ width:20px; height:20px; }
  .notif-panel .urgent .np-icon{ background:#fde8e6; color:#c0392b; }
  .notif-panel .np-text{ min-width:0; flex:1; overflow-wrap:anywhere; }
  .notif-panel .np-text strong{ font-weight:700; }
  .notif-panel .unread .np-text{ font-weight:500; }
  .notif-panel .np-title{ display:block; font-weight:600; }
  .notif-panel .unread .np-title{ font-weight:700; }
  .notif-panel .np-body{ display:block; margin-top:2px; font-size:12.5px; font-weight:400; color:var(--text-mid, #5b6b60); line-height:1.45; }
  .notif-panel .np-time{ display:block; margin-top:3px; font-size:12px; font-weight:400; color:var(--text-mid, #5b6b60); }
  .notif-panel .unread .np-time{ color:var(--green-accent, #2f6f3c); font-weight:600; }
  .notif-panel .urgent.unread .np-time{ color:#c0392b; }
  .notif-panel .np-dot{ position:absolute; right:12px; top:50%; width:10px; height:10px; margin-top:-5px; border-radius:50%; background:var(--green-accent, #2f6f3c); }
  .notif-panel .urgent .np-dot{ background:#c0392b; }
  .notif-panel .np-sr{ position:absolute; width:1px; height:1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; }
  .notif-panel .np-empty{ padding:32px 16px; text-align:center; font-size:13px; color:var(--text-mid, #5b6b60); }
  .notif-panel .np-empty button{ margin-top:10px; min-height:34px; padding:0 14px; border:1px solid var(--border, #e3e7e3); border-radius:8px; background:#fff; font:inherit; font-size:12.5px; font-weight:600; color:var(--text-dark, #26302a); cursor:pointer; }
  /* The emergency pop-up and other top-bar panels step aside while this is open. */
  body:has(.notif-panel.open) .emergency-pop{ display:none; }
</style>
<script>
window.NestNotifPanel = (function(){
  const ICONS = {
    emergency: '<path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/>',
    application: '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 15h6"/><path d="M9 11h2"/>',
    payment: '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>',
    inquiry: '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
    ticket: '<path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2.6 2.6-2.4-.6-.6-2.4z"/>',
    bill: '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
    penalty: '<circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/>',
    reservation: '<path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/>',
    review: '<path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z"/>',
    lease: '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/>',
    account: '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
    deposit: '<path d="M20 12V8H6a2 2 0 0 1 0-4h12v4"/><path d="M4 6v12a2 2 0 0 0 2 2h14v-4"/><path d="M18 12a2 2 0 0 0 0 4h4v-4z"/>',
    announcement: '<path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>',
    other: '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>',
  };
  const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const icon = k => `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${ICONS[k] || ICONS.other}</svg>`;

  return function(opts){
    const panel = document.getElementById(opts.prefix + 'NotifPanel');
    const list = panel.querySelector('.np-list');
    const markAll = panel.querySelector('.np-mark-all');
    const { bell, badge } = opts;
    let items = [], filter = 'all', error = null;

    function updateBadge(){
      const unread = items.filter(i => !i.read).length;
      const max = opts.badgeMax || 99;
      badge.textContent = unread > max ? max + '+' : String(unread);
      badge.hidden = unread === 0;
      bell.setAttribute('aria-label', unread ? `Notifications, ${unread} unread` : 'Notifications');
      markAll.disabled = unread === 0;
    }

    function itemHtml(i){
      const text = i.actor
        ? `<strong>${esc(i.actor)}</strong>${String(i.text).startsWith("'") ? '' : ' '}${esc(i.text)}`
        : `<span class="np-title">${esc(i.title ?? i.text)}</span>${i.body ? `<span class="np-body">${esc(i.body)}</span>` : ''}`;
      const inner = `<span class="np-icon">${icon(i.kind)}</span>
        <span class="np-text">${text}<span class="np-time">${esc(i.ago)}${i.read ? '' : '<span class="np-sr"> · unread</span>'}</span></span>
        ${i.read ? '' : '<span class="np-dot" aria-hidden="true"></span>'}`;
      return i.href
        ? `<a class="np-item" href="${esc(i.href)}" data-key="${esc(i.key)}">${inner}</a>`
        : `<button type="button" class="np-item" data-key="${esc(i.key)}">${inner}</button>`;
    }

    function render(){
      updateBadge();
      if(error){
        list.innerHTML = `<li class="np-empty">${esc(error)}<br><button type="button" data-retry>Try again</button></li>`;
        return;
      }
      const shown = filter === 'unread' ? items.filter(i => !i.read) : items;
      if(!shown.length){
        list.innerHTML = `<li class="np-empty">${filter === 'unread' ? 'No unread notifications.' : 'No notifications yet.'}</li>`;
        return;
      }
      const startOfToday = new Date(); startOfToday.setHours(0, 0, 0, 0);
      const isToday = i => i.time && new Date(i.time) >= startOfToday;
      const groups = [['Today', shown.filter(isToday)], ['Earlier', shown.filter(i => !isToday(i))]];
      list.innerHTML = groups.filter(g => g[1].length).map(([label, group]) =>
        `<li class="np-group" role="presentation">${label}</li>` +
        group.map(i => `<li class="${i.urgent ? 'urgent' : ''} ${i.read ? '' : 'unread'}">${itemHtml(i)}</li>`).join('')
      ).join('');
    }

    function markRead(keys, all){
      if(!keys.length) return;
      items.forEach(i => { if(keys.includes(i.key)) i.read = true; });
      opts.onMarkRead(keys, all);
    }

    list.addEventListener('click', e => {
      if(e.target.closest('[data-retry]')){ e.stopPropagation(); error = null; list.innerHTML = '<li class="np-empty">Loading…</li>'; opts.onOpen?.(true); return; }
      const el = e.target.closest('.np-item');
      if(!el) return;
      const item = items.find(i => String(i.key) === el.dataset.key);
      if(!item) return;
      const wasUnread = !item.read;
      if(wasUnread) markRead([item.key], false);
      // A link to the page you're already on: just mark it read and stay.
      if(!item.href || item.href === location.pathname + location.search){
        e.preventDefault();
        render();
      }
      // Otherwise the browser follows the link; the list isn't redrawn mid-click.
    });
    markAll.addEventListener('click', e => {
      e.stopPropagation();
      markRead(items.filter(i => !i.read).map(i => i.key), true);
      render();
    });
    panel.querySelectorAll('.np-filters button').forEach(btn => btn.addEventListener('click', e => {
      e.stopPropagation();
      filter = btn.dataset.filter;
      panel.querySelectorAll('.np-filters button').forEach(b => b.setAttribute('aria-pressed', String(b === btn)));
      render();
    }));

    // Open / close (one top-bar panel at a time).
    const close = () => { panel.classList.remove('open'); bell.setAttribute('aria-expanded', 'false'); };
    bell.addEventListener('click', e => {
      e.stopPropagation();
      const open = !panel.classList.contains('open');
      document.querySelectorAll('.pm-panel.open').forEach(p => p.classList.remove('open'));
      const bar = bell.closest('.topbar');
      if(bar) panel.style.top = Math.round(bar.getBoundingClientRect().bottom + 6) + 'px';
      panel.classList.toggle('open', open);
      bell.setAttribute('aria-expanded', String(open));
      if(open) opts.onOpen?.(false);
    });
    document.addEventListener('click', e => { if(panel.classList.contains('open') && !panel.contains(e.target)) close(); });
    document.addEventListener('keydown', e => { if(e.key === 'Escape' && panel.classList.contains('open')){ close(); bell.focus(); } });

    return {
      setItems(next){ items = next; error = null; render(); },
      setError(msg){ error = msg; render(); },
      unreadCount(){ return items.filter(i => !i.read).length; },
    };
  };
})();
</script>
@endonce
<section class="notif-panel" id="{{ $prefix }}NotifPanel" aria-label="Notifications">
  <div class="np-head">
    <h2>Notifications</h2>
    <button type="button" class="np-mark-all" disabled>Mark all as read</button>
  </div>
  <div class="np-filters" role="group" aria-label="Show">
    <button type="button" aria-pressed="true" data-filter="all">All</button>
    <button type="button" aria-pressed="false" data-filter="unread">Unread</button>
  </div>
  <ul class="np-list"><li class="np-empty">Loading…</li></ul>
</section>
