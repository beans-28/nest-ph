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
  /* Emergency pop-up: stays until dismissed or opened, one per ticket. */
  .emergency-pop{ position:fixed; top:74px; right:16px; width:min(360px, calc(100vw - 32px)); z-index:210; background:#fff; border:1px solid #f0c4c1; border-radius:12px; box-shadow:0 18px 44px rgba(120,20,20,.22); overflow:hidden; font-family:var(--font-body); animation:emPop .28s cubic-bezier(.16,1,.3,1); }
  @keyframes emPop{ from{ opacity:0; transform:translateY(-8px); } to{ opacity:1; transform:none; } }
  @media (prefers-reduced-motion:reduce){ .emergency-pop{ animation:none; } }
  .emergency-pop header{ display:flex; align-items:center; gap:8px; background:#b42318; color:#fff; padding:10px 14px; font-size:12px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
  .emergency-pop header .pulse{ width:8px; height:8px; border-radius:50%; background:#fff; box-shadow:0 0 0 0 rgba(255,255,255,.7); animation:emPulse 1.6s infinite; }
  @keyframes emPulse{ 70%{ box-shadow:0 0 0 8px rgba(255,255,255,0); } 100%{ box-shadow:0 0 0 0 rgba(255,255,255,0); } }
  /* Out of the way while a ticket or other dialog is open. */
  body:has(.modal-overlay.open) .emergency-pop{ display:none; }
  .emergency-pop .body{ padding:12px 14px 14px; }
  .emergency-pop h3{ margin:0 0 4px; font-size:15px; color:#26302a; }
  .emergency-pop p{ margin:0 0 12px; font-size:12.5px; color:#5b655d; line-height:1.45; }
  .emergency-pop .actions{ display:flex; gap:8px; }
  .emergency-pop .actions a, .emergency-pop .actions button{ flex:1; min-height:38px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; text-decoration:none; font-family:inherit; }
  .emergency-pop .actions a{ background:#b42318; color:#fff; border:none; }
  .emergency-pop .actions button{ background:#fff; color:#5b655d; border:1px solid #dfe3df; }
  .emergency-pop .actions :focus-visible{ outline:2px solid #26302a; outline-offset:2px; }
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

<div id="emergencyPops" aria-live="assertive"></div>

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
    // 30s: a Critical ticket must be answered within 5 minutes.
    setInterval(() => { if(!document.hidden) load(); }, 30000);
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
      showEmergencies(data.emergencies || []);
    } catch(e){
      list.innerHTML = '<li class="admin-notif-empty">Could not load notifications.</li>';
    }
  }

  // One pop-up per emergency; "Later" hides it for this browser tab only,
  // so it comes back after a reload if still nobody has responded.
  const dismissed = new Set(JSON.parse(sessionStorage.getItem('dismissedEmergencies') || '[]'));
  function showEmergencies(list){
    const host = document.getElementById('emergencyPops');
    const e = list.find(x => !dismissed.has(x.id));
    if(!e){ host.innerHTML = ''; return; }
    if(host.dataset.id === String(e.id)) return;
    host.dataset.id = e.id;
    const who = e.mine
      ? (e.team.length > 1 ? `You and ${e.team.length - 1} other${e.team.length > 2 ? 's' : ''} are assigned.` : 'You are assigned.')
      : 'No one is assigned yet. Can you take it?';
    host.innerHTML = `<section class="emergency-pop" role="alertdialog" aria-labelledby="emTitle">
        <header><span class="pulse" aria-hidden="true"></span>Emergency ticket</header>
        <div class="body">
          <h3 id="emTitle">${esc(e.title)}</h3>
          <p>${esc(e.category)}${e.room ? ' · Room ' + esc(e.room) : ''} · ${esc(e.filed)}<br>${who} Respond within 5 minutes.</p>
          <div class="actions"><a href="/tickets?open=${e.id}">Open ticket</a><button type="button" data-later>Later</button></div>
        </div>
      </section>`;
    host.querySelector('[data-later]').addEventListener('click', () => {
      dismissed.add(e.id);
      sessionStorage.setItem('dismissedEmergencies', JSON.stringify([...dismissed]));
      host.dataset.id = '';
      showEmergencies(list);
    });
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
