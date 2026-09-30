{{--
    Admin notification bell (v39). Included by partials/admin-sidebar, so
    it's on every admin page; the script moves the bell into .topbar-right.
    Shows live counts of waiting work (AdminNotificationController) and
    refreshes every 60 seconds while the tab is open.
--}}
<style>
  /* Matches the white profile circle beside it on every admin page. */
  .topbar-right .admin-bell{ position:relative; border:none; padding:0; background:#fff; color:var(--green-dark); }
  .admin-bell .notif-badge{ position:absolute; top:-6px; right:-8px; min-width:12px; height:16px; padding:0 4px; border-radius:8px; background:#c0392b; color:#fff; font-size:10px; font-weight:700; line-height:16px; text-align:center; border:2px solid var(--green-dark); font-variant-numeric:tabular-nums; pointer-events:none; }
  .admin-bell .notif-badge[hidden]{ display:none; }
  .admin-notif-panel{ position:fixed; top:62px; right:16px; width:min(340px, calc(100vw - 32px)); background:#fff; border:1px solid var(--border, #e3e7e3); border-radius:12px; box-shadow:0 14px 40px rgba(0,0,0,.18); z-index:200; display:none; flex-direction:column; overflow:hidden; font-family:var(--font-body); }
  .admin-notif-panel.open{ display:flex; }
  .admin-notif-panel h2{ margin:0; padding:14px 16px; font-size:14px; font-weight:700; color:var(--text-dark, #26302a); border-bottom:1px solid var(--border, #e3e7e3); }
  .admin-notif-list{ list-style:none; margin:0; padding:0; max-height:min(420px, calc(100vh - 140px)); overflow-y:auto; }
  .admin-notif-list a{ display:flex; align-items:center; gap:10px; padding:12px 16px; border-bottom:1px solid #f0f2f0; color:var(--text-dark, #26302a); text-decoration:none; font-size:13px; }
  .admin-notif-list a:hover{ background:#f5f8f5; }
  .admin-notif-list a:focus-visible{ outline:2px solid var(--green-accent, #2f6f3c); outline-offset:-2px; }
  .admin-notif-list .dot{ width:8px; height:8px; border-radius:50%; background:var(--green-accent, #2f6f3c); flex:none; }
  .admin-notif-list .urgent .dot{ background:#d64541; }
  .admin-notif-list .go{ margin-left:auto; width:16px; height:16px; color:var(--text-light, #8b938a); flex:none; }
  .admin-notif-empty{ padding:28px 16px; text-align:center; font-size:13px; color:var(--text-light, #8b938a); }
</style>

<button type="button" class="topbar-icon admin-bell" id="adminBell" aria-label="Notifications" aria-haspopup="true" aria-expanded="false" hidden>
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
  <span class="notif-badge" id="adminBellBadge" hidden>0</span>
</button>
<section class="admin-notif-panel" id="adminNotifPanel" aria-label="Notifications">
  <h2>Needs your attention</h2>
  <ul class="admin-notif-list" id="adminNotifList"><li class="admin-notif-empty">Loading…</li></ul>
</section>

<script>
(function(){
  const bell = document.getElementById('adminBell');
  const panel = document.getElementById('adminNotifPanel');
  const list = document.getElementById('adminNotifList');
  const badge = document.getElementById('adminBellBadge');
  const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

  document.addEventListener('DOMContentLoaded', () => {
    const right = document.querySelector('.topbar-right');
    if(!right) return;
    right.insertBefore(bell, right.querySelector('.topbar-icon'));
    bell.hidden = false;
    load();
    setInterval(() => { if(!document.hidden) load(); }, 60000);
  });

  async function load(){
    try {
      const res = await fetch('/admin/notifications', { headers: { 'Accept': 'application/json' } });
      if(!res.ok) throw new Error();
      const data = await res.json();
      badge.textContent = data.total > 99 ? '99+' : String(data.total);
      badge.hidden = data.total === 0;
      bell.setAttribute('aria-label', data.total ? `Notifications, ${data.total} items need attention` : 'Notifications');
      list.innerHTML = data.items.length
        ? data.items.map(i => `<li class="${i.urgent ? 'urgent' : ''}"><a href="${i.link}"><span class="dot" aria-hidden="true"></span>${esc(i.label)}<svg class="go" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg></a></li>`).join('')
        : '<li class="admin-notif-empty">All caught up. Nothing is waiting for you.</li>';
    } catch(e){
      list.innerHTML = '<li class="admin-notif-empty">Could not load notifications.</li>';
    }
  }

  const close = () => { panel.classList.remove('open'); bell.setAttribute('aria-expanded', 'false'); };
  bell.addEventListener('click', (e) => {
    e.stopPropagation();
    const open = !panel.classList.contains('open');
    panel.classList.toggle('open', open);
    bell.setAttribute('aria-expanded', String(open));
    if(open) load();
  });
  document.addEventListener('click', (e) => { if(panel.classList.contains('open') && !panel.contains(e.target)) close(); });
  document.addEventListener('keydown', (e) => { if(e.key === 'Escape' && panel.classList.contains('open')){ close(); bell.focus(); } });
})();
</script>
