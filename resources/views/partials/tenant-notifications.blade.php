{{--
    Tenant notification bell + panel + login bill-reminder pop-up (v39).
    Included by partials/tenant-sidebar and partials/tenant-sidebar-restricted,
    so it's on every tenant portal page. The script moves the bell into the
    page's .topbar-right, next to the profile icon.

    Pop-up: AuthController::login() sets session('show_bill_popup') for
    tenants; the first portal page after login pulls it and, if any bill is
    due within 10 days or overdue, shows the centred reminder once.
--}}
@php
    $notifTenant = auth()->user()?->tenant;
    $popupBills = collect();
    if ($notifTenant && session()->pull('show_bill_popup')) {
        $notifService = app(\App\Services\TenantNotificationService::class);
        $notifService->syncAutomatic($notifTenant);
        $popupBills = $notifService->billsNeedingAttention($notifTenant);
    }
@endphp
@if($notifTenant)
<style>
  .notif-bell{ position:relative; border:none; padding:0; }
  .notif-bell .notif-badge{ position:absolute; top:-6px; right:-8px; min-width:12px; height:16px; padding:0 4px; border-radius:8px; background:#c0392b; color:#fff; font-size:10px; font-weight:700; line-height:16px; text-align:center; border:2px solid var(--sage-800); font-variant-numeric:tabular-nums; pointer-events:none; }
  .notif-bell .notif-badge[hidden]{ display:none; }
  .notif-panel{ position:fixed; top:66px; right:16px; width:min(380px, calc(100vw - 32px)); max-height:min(560px, calc(100vh - 90px)); background:#fff; border:1px solid var(--border); border-radius:14px; box-shadow:0 14px 40px rgba(20,40,28,.18); z-index:200; display:none; flex-direction:column; overflow:hidden; }
  .notif-panel.open{ display:flex; }
  .notif-head{ display:flex; align-items:center; gap:10px; padding:14px 16px; border-bottom:1px solid var(--border); }
  .notif-head h2{ margin:0; font-size:15px; font-weight:700; color:var(--text-dark); }
  .notif-readall{ margin-left:auto; background:none; border:none; color:var(--sage-700); font-size:12.5px; font-weight:600; cursor:pointer; padding:6px 4px; font-family:var(--font-body); }
  .notif-readall:disabled{ color:var(--text-light); cursor:default; }
  .notif-list{ overflow-y:auto; margin:0; padding:0; list-style:none; }
  .notif-item{ display:flex; gap:12px; padding:12px 16px; border-bottom:1px solid #f1eee7; cursor:pointer; text-align:left; width:100%; background:#fff; border-left:none; border-right:none; border-top:none; font-family:var(--font-body); }
  .notif-item:hover{ background:var(--sage-50); }
  .notif-item:focus-visible{ outline:2px solid var(--sage-600); outline-offset:-2px; }
  .notif-item.unread{ background:#f7fbf5; }
  .notif-dot{ width:8px; height:8px; border-radius:50%; margin-top:6px; flex:none; background:transparent; }
  .notif-item.unread .notif-dot{ background:var(--sage-600); }
  .notif-item.urgent.unread .notif-dot{ background:#c0392b; }
  .notif-text{ flex:1; min-width:0; }
  .notif-title{ font-size:13px; font-weight:600; color:var(--text-dark); margin:0 0 2px; }
  .notif-body{ font-size:12.5px; color:var(--text-mid); margin:0 0 4px; line-height:1.45; }
  .notif-time{ font-size:11px; color:var(--text-light); }
  .notif-empty{ padding:36px 16px; text-align:center; font-size:13px; color:var(--text-light); }

  .bill-popup{ position:fixed; inset:0; background:rgba(20,30,24,.55); z-index:300; display:flex; align-items:center; justify-content:center; padding:16px; }
  .bill-popup[hidden]{ display:none; }
  .bill-popup-box{ background:#fff; border-radius:16px; width:100%; max-width:440px; max-height:calc(100vh - 32px); overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.3); }
  .bill-popup-head{ padding:22px 24px 8px; text-align:center; }
  .bill-popup-head svg{ width:44px; height:44px; color:#b7791f; }
  .bill-popup.has-overdue .bill-popup-head svg{ color:#c0392b; }
  .bill-popup-head h2{ margin:10px 0 4px; font-size:18px; color:var(--text-dark); }
  .bill-popup-head p{ margin:0; font-size:13px; color:var(--text-mid); }
  .bill-popup-list{ list-style:none; margin:14px 24px 0; padding:0; border:1px solid var(--border); border-radius:12px; overflow:hidden; }
  .bill-popup-list li{ display:flex; justify-content:space-between; gap:12px; padding:12px 14px; border-bottom:1px solid var(--border); font-size:13px; }
  .bill-popup-list li:last-child{ border-bottom:none; }
  .bill-popup-list .when{ display:block; font-size:12px; color:var(--text-mid); margin-top:2px; }
  .bill-popup-list .when.overdue{ color:#c0392b; font-weight:600; }
  .bill-popup-list .amt{ font-weight:700; white-space:nowrap; font-variant-numeric:tabular-nums; }
  .bill-popup-actions{ display:flex; gap:10px; padding:18px 24px 22px; }
  .bill-popup-actions a, .bill-popup-actions button{ flex:1; min-height:44px; border-radius:10px; font-size:14px; font-weight:600; font-family:var(--font-body); cursor:pointer; display:flex; align-items:center; justify-content:center; text-decoration:none; }
  .bill-popup-actions a{ background:var(--sage-600); color:#fff; border:none; }
  .bill-popup-actions a:hover{ background:var(--sage-700); }
  .bill-popup-actions button{ background:#fff; color:var(--text-mid); border:1px solid var(--border); }
  .bill-popup-actions a:focus-visible, .bill-popup-actions button:focus-visible{ outline:2px solid var(--sage-600); outline-offset:2px; }
</style>

<button type="button" class="topbar-icon notif-bell" id="notifBell" aria-label="Notifications" aria-haspopup="true" aria-expanded="false" hidden>
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
  <span class="notif-badge" id="notifBadge" hidden>0</span>
</button>

<section class="notif-panel" id="notifPanel" aria-label="Notifications">
  <div class="notif-head">
    <h2>Notifications</h2>
    <button type="button" class="notif-readall" id="notifReadAll" disabled>Mark all as read</button>
  </div>
  <ul class="notif-list" id="notifList"><li class="notif-empty">Loading…</li></ul>
</section>

@if($popupBills->isNotEmpty())
  @php($anyOverdue = $popupBills->contains('is_overdue', true))
  <div class="bill-popup {{ $anyOverdue ? 'has-overdue' : '' }}" id="billPopup" role="dialog" aria-modal="true" aria-labelledby="billPopupTitle">
    <div class="bill-popup-box">
      <div class="bill-popup-head">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
        <h2 id="billPopupTitle">{{ $anyOverdue ? 'You have an overdue bill' : 'Your bill is due soon' }}</h2>
        <p>{{ $anyOverdue ? 'Please settle it as soon as possible to avoid penalties and account restrictions.' : 'Pay on or before the due date to avoid late penalties.' }}</p>
      </div>
      <ul class="bill-popup-list">
        @foreach($popupBills as $b)
          <li>
            <span>
              {{ $b['label'] }}
              @if($b['is_overdue'])
                <span class="when overdue">Overdue since {{ $b['due_date'] }}</span>
              @elseif($b['days_left'] === 0)
                <span class="when">Due today</span>
              @else
                <span class="when">Due {{ $b['due_date'] }} ({{ $b['days_left'] }} day{{ $b['days_left'] === 1 ? '' : 's' }} left)</span>
              @endif
            </span>
            <span class="amt">₱{{ number_format($b['balance'], 2) }}</span>
          </li>
        @endforeach
      </ul>
      <div class="bill-popup-actions">
        <button type="button" id="billPopupLater">Remind me later</button>
        <a href="{{ route('tenant.billing') }}" id="billPopupPay">Go to Billing</a>
      </div>
    </div>
  </div>
@endif

<script>
(function(){
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const bell = document.getElementById('notifBell');
  const panel = document.getElementById('notifPanel');
  const list = document.getElementById('notifList');
  const badge = document.getElementById('notifBadge');
  const readAll = document.getElementById('notifReadAll');
  const URGENT = ['bill_overdue', 'payment_rejected', 'account_restricted', 'demand_letter'];
  let items = [];
  let loaded = false;

  const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

  // Put the bell in the top bar, just before the profile icon.
  document.addEventListener('DOMContentLoaded', () => {
    const right = document.querySelector('.topbar-right');
    if(!right) return; // e.g. the blacklist takeover page has no top bar
    right.insertBefore(bell, right.querySelector('.topbar-icon'));
    bell.hidden = false;
    load();
  });

  function setUnread(n){
    badge.textContent = n > 9 ? '9+' : String(n);
    badge.hidden = n === 0;
    bell.setAttribute('aria-label', n ? `Notifications, ${n} unread` : 'Notifications');
    readAll.disabled = n === 0;
  }

  function render(){
    if(!items.length){
      list.innerHTML = '<li class="notif-empty">You have no notifications yet.</li>';
      return;
    }
    list.innerHTML = items.map(n => `
      <li><button type="button" class="notif-item ${n.read ? '' : 'unread'} ${URGENT.includes(n.type) ? 'urgent' : ''}" data-id="${n.id}">
        <span class="notif-dot" aria-hidden="true"></span>
        <span class="notif-text">
          <p class="notif-title">${esc(n.title)}</p>
          ${n.body ? `<p class="notif-body">${esc(n.body)}</p>` : ''}
          <span class="notif-time">${esc(n.time_ago)}</span>
        </span>
      </button></li>`).join('');
  }

  async function load(){
    try {
      const res = await fetch('/my/notifications', { headers: { 'Accept': 'application/json' } });
      if(!res.ok) throw new Error();
      const data = await res.json();
      items = data.notifications;
      loaded = true;
      setUnread(data.unread);
      render();
    } catch(e){
      list.innerHTML = '<li class="notif-empty">Notifications could not be loaded. Please refresh the page.</li>';
    }
  }

  function openPanel(){
    panel.classList.add('open');
    bell.setAttribute('aria-expanded', 'true');
    if(!loaded) load();
  }
  function closePanel(){
    panel.classList.remove('open');
    bell.setAttribute('aria-expanded', 'false');
  }

  bell.addEventListener('click', (e) => {
    e.stopPropagation();
    panel.classList.contains('open') ? closePanel() : openPanel();
  });
  document.addEventListener('click', (e) => {
    if(panel.classList.contains('open') && !panel.contains(e.target)) closePanel();
  });
  document.addEventListener('keydown', (e) => {
    if(e.key === 'Escape' && panel.classList.contains('open')){ closePanel(); bell.focus(); }
  });

  list.addEventListener('click', async (e) => {
    const btn = e.target.closest('.notif-item');
    if(!btn) return;
    const n = items.find(x => x.id === Number(btn.dataset.id));
    if(!n) return;
    if(!n.read){
      n.read = true;
      setUnread(items.filter(x => !x.read).length);
      render();
      await fetch(`/my/notifications/${n.id}/read`, { method:'PATCH', headers:{ 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' } }).catch(() => {});
    }
    if(n.link && n.link !== location.pathname) location.href = n.link;
  });

  readAll.addEventListener('click', async () => {
    items.forEach(n => n.read = true);
    setUnread(0);
    render();
    await fetch('/my/notifications/read-all', { method:'POST', headers:{ 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' } }).catch(() => {});
  });

  // ---- Login bill reminder pop-up ----
  const popup = document.getElementById('billPopup');
  if(popup){
    const later = document.getElementById('billPopupLater');
    const focusables = () => [...popup.querySelectorAll('a, button')];
    later.addEventListener('click', () => popup.hidden = true);
    popup.addEventListener('keydown', (e) => {
      if(e.key === 'Escape'){ popup.hidden = true; return; }
      if(e.key !== 'Tab') return;
      const f = focusables(), i = f.indexOf(document.activeElement);
      e.preventDefault();
      f[e.shiftKey ? (i <= 0 ? f.length - 1 : i - 1) : (i === f.length - 1 ? 0 : i + 1)].focus();
    });
    document.getElementById('billPopupPay').focus();
  }
})();
</script>
@endif
