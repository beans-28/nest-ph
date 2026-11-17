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
  .bill-popup{ position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:300; display:flex; align-items:center; justify-content:center; padding:16px; }
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

<button type="button" class="topbar-icon notif-bell" id="notifBell" aria-label="Notifications" aria-haspopup="true" aria-expanded="false" aria-controls="tenantNotifPanel" hidden>
  <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
  <span class="notif-badge" id="notifBadge" hidden>0</span>
</button>

@include('partials.notification-panel', ['prefix' => 'tenant'])

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
              @elseif(!empty($b['in_grace']))
                <span class="when overdue">Was due {{ $b['due_date'] }}. Pay by {{ $b['grace_until'] }} to avoid a late penalty</span>
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
  const URGENT = ['bill_overdue', 'bill_grace', 'payment_rejected', 'account_restricted', 'demand_letter', 'blacklisted'];
  // Notification type -> icon in the shared panel.
  const kindOf = t => t.startsWith('bill_') || t.startsWith('payment') ? 'bill'
    : t.includes('penalty') ? 'penalty'
    : t.startsWith('lease_') ? 'lease'
    : t.startsWith('ticket_') ? 'ticket'
    : t.startsWith('account_') || t === 'blacklisted' || t === 'demand_letter' ? 'account'
    : t.startsWith('deposit') ? 'deposit'
    : t === 'announcement' ? 'announcement' : 'other';
  let loaded = false;

  const panel = NestNotifPanel({
    prefix: 'tenant',
    bell,
    badge: document.getElementById('notifBadge'),
    badgeMax: 9,
    onOpen: (retry) => { if(!loaded || retry) load(); },
    onMarkRead: (keys, all) => {
      const headers = { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' };
      if(all){
        fetch('/my/notifications/read-all', { method: 'POST', headers, keepalive: true }).catch(() => {});
      } else {
        keys.forEach(id => fetch(`/my/notifications/${id}/read`, { method: 'PATCH', headers, keepalive: true }).catch(() => {}));
      }
    },
  });

  // Put the bell in the top bar, just before the profile icon.
  document.addEventListener('DOMContentLoaded', () => {
    const right = document.querySelector('.topbar-right');
    if(!right) return; // e.g. the blacklist takeover page has no top bar
    right.insertBefore(bell, right.querySelector('.topbar-icon'));
    bell.hidden = false;
    load();
  });

  async function load(){
    try {
      const res = await fetch('/my/notifications', { headers: { 'Accept': 'application/json' } });
      if(!res.ok) throw new Error();
      const data = await res.json();
      loaded = true;
      panel.setItems(data.notifications.map(n => ({
        key: n.id, kind: kindOf(n.type || ''), urgent: URGENT.includes(n.type), read: n.read,
        title: n.title, body: n.body, ago: n.time_ago, time: n.created_at, href: n.link || null,
      })));
    } catch(e){
      panel.setError('Notifications could not be loaded.');
    }
  }

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
