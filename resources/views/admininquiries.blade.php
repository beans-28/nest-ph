<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Inquiry Management · {{ $brandDormName }}</title>
<link rel="icon" href="{{ $brandFaviconUrl }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<style>
  /* Shared sidebar/topbar/content-header/reset styles now live in
     public/css/admin.css (linked above). Only page-specific overrides
     and this page's own content styling stay here. */

  /* Customized vs admin.css: light-background icon (not using .avatar-icon) */
  .topbar-icon{ width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.9); color:var(--green-dark); display:flex; align-items:center; justify-content:center; cursor:pointer; }

  /* Customized vs admin.css: slightly different page-head margin */
  .page-head{ display:flex; align-items:center; gap:12px; margin-bottom:20px; }

  /* This page's own inquiry search (filters the list below) — this is
     NOT the shared topbar page-navigation search that was removed, so
     it moved into the content area instead of being deleted. */
  .search-input{ display:block; width:100%; max-width:320px; border:1px solid var(--border); border-radius:8px; padding:9px 14px; font-size:13px; font-family:var(--font-body); background:#fff; margin-bottom:14px; }

  .filters{ display:flex; gap:8px; margin-bottom:18px; flex-wrap:wrap; }
  .filter-chip{ border:1px solid var(--border); background:#fff; border-radius:20px; padding:7px 16px; font-size:12px; font-family:inherit; font-weight:600; color:var(--text-mid); cursor:pointer; }
  .filter-chip.active{ background:var(--status-vacant-bg); border-color:var(--status-vacant); color:var(--green-accent); }

  .list-panel{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; overflow:hidden; }
  .list-row{ display:flex; align-items:center; gap:16px; padding:16px 20px; border-bottom:1px solid #f0f2f0; cursor:pointer; }
  .list-row:last-child{ border-bottom:none; }
  .list-row:hover{ background:#fbfcfb; }
  .lr-name{ font-size:13.5px; font-weight:700; min-width:150px; }
  .lr-room{ font-size:11px; font-weight:700; color:var(--green-accent); background:var(--status-vacant-bg); padding:3px 10px; border-radius:20px; white-space:nowrap; }
  .lr-msg{ font-size:12.5px; color:var(--text-mid); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .lr-date{ font-size:11.5px; color:var(--text-light); white-space:nowrap; }
  .badge{ font-size:9.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; padding:4px 10px; border-radius:20px; white-space:nowrap; }
  .badge.new{ background:var(--status-maintenance-bg); color:var(--status-maintenance); }
  .badge.contacted{ background:var(--status-vacant-bg); color:var(--green-accent); }
  .badge.converted{ background:#e3ecf7; color:#33629e; }
  .badge.closed{ background:#f0f1f0; color:var(--text-light); }
  .empty{ color:var(--text-light); font-size:13px; font-style:italic; padding:30px; text-align:center; }

  .btn{ font-size:12px; font-weight:600; padding:10px 18px; border-radius:7px; border:1px solid var(--border); background:#fff; color:var(--text-mid); cursor:pointer; font-family:var(--font-body); }
  .btn:hover:not(:disabled){ background:#f7f9f7; }
  .btn:disabled{ opacity:.5; cursor:not-allowed; }
  .btn.primary{ background:var(--green-btn); border-color:var(--green-btn); color:#fff; }
  .btn.primary:hover:not(:disabled){ background:var(--green-btn-hover); }

  /* Drawer */
  .overlay{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:50; }
  .overlay.open{ display:block; }
  .drawer{ position:fixed; top:0; right:0; height:100%; width:min(560px,100%); background:#fff; z-index:51; transform:translateX(100%); transition:transform .25s ease; overflow-y:auto; display:none; }
  .drawer.open{ transform:translateX(0); display:block; }
  .drawer-head{ position:sticky; top:0; background:#fff; border-bottom:1px solid var(--border); padding:18px 24px; display:flex; align-items:center; gap:12px; z-index:2; }
  .drawer-head h2{ font-size:16px; font-weight:700; margin:0; }
  .drawer-close{ margin-left:auto; background:none; border:none; font-size:22px; color:var(--text-light); cursor:pointer; line-height:1; }
  .drawer-body{ padding:22px 24px 40px 24px; }

  .sec{ margin-bottom:24px; }
  .sec h3{ font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--green-accent); margin:0 0 12px 0; padding-bottom:7px; border-bottom:2px solid #e2ede3; }
  .kv{ display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:12px 20px; }
  .kv .k{ font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-light); }
  .kv .v{ font-size:13px; font-weight:500; margin-top:3px; word-break:break-word; }
  .kv .v.empty-v{ color:#c2c9c5; font-style:italic; font-weight:400; }

  .msg-box{ background:#fbfcfb; border:1px solid var(--border); border-radius:10px; padding:14px 16px; font-size:13px; line-height:1.7; white-space:pre-line; }

  .reply-box textarea{ width:100%; border:1px solid var(--border); border-radius:8px; padding:12px 14px; font-size:13px; font-family:var(--font-body); min-height:110px; resize:vertical; margin-bottom:12px; }
  .tpl-label{ font-size:11px; font-weight:600; color:var(--text-light); margin-bottom:8px; }
  .tpl-row{ display:flex; flex-wrap:wrap; gap:6px; margin-bottom:10px; }
  .tpl-chip{ border:1px solid var(--border); background:#fff; border-radius:999px; padding:5px 12px; font-size:12px; font-family:var(--font-body); cursor:pointer; color:var(--text); }
  .tpl-chip:hover{ border-color:var(--green-accent); }
  .tpl-chip.active{ background:var(--green-accent); border-color:var(--green-accent); color:#fff; }
  .reply-box textarea{ min-height:220px; }
  .already-replied{ background:var(--status-vacant-bg); border:1px solid var(--status-vacant); border-radius:10px; padding:14px 16px; font-size:12.5px; color:var(--green-accent); line-height:1.6; }

  .status-row{ display:flex; gap:8px; flex-wrap:wrap; }

  .toast{ position:fixed; bottom:22px; right:22px; background:var(--green-accent); color:#fff; padding:12px 20px; border-radius:8px; font-size:13px; display:none; z-index:99; box-shadow:0 6px 18px rgba(0,0,0,.2); }
  .toast.error{ background:var(--status-occupied); }
  .toast.visible{ display:block; }
  /* Mobile: each inquiry wraps onto two lines instead of squeezing five items into one */
  @media (max-width:640px){
    .list-row{ flex-wrap:wrap; gap:6px 10px; padding:14px 16px; }
    .lr-name{ min-width:0; flex:1 1 auto; }
    .lr-msg{ flex:1 1 100%; order:5; white-space:normal; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; }
  }
</style>
@include('partials.role-tag-style')
</head>
<body>
<div class="app">

@include('partials.admin-sidebar')

  <div class="main">
    <div class="topbar">
      <div class="hamburger" id="hamburgerBtn" tabindex="0" aria-label="Toggle sidebar"><span></span><span></span><span></span></div>
      <div class="topbar-right">
        <div class="topbar-icon" tabindex="0" aria-label="Account"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
      </div>
    </div>

    <div class="content">
      <div class="page-head">
        <div class="back-arrow" data-href="{{ route('dashboard') }}" tabindex="0" aria-label="Back to dashboard"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
        <h1>Inquiry Management</h1>
      </div>

      <input type="text" class="search-input" id="searchInput" placeholder="Search name or message" aria-label="Search name or message">

      <div class="filters" id="filters" role="tablist" aria-label="Filter inquiries">
        <button type="button" class="filter-chip active" role="tab" aria-selected="true" data-filter="all">All</button>
        <button type="button" class="filter-chip" role="tab" aria-selected="false" data-filter="new">New</button>
        <button type="button" class="filter-chip" role="tab" aria-selected="false" data-filter="contacted">Replied</button>
        <button type="button" class="filter-chip" role="tab" aria-selected="false" data-filter="converted">Converted</button>
        <button type="button" class="filter-chip" role="tab" aria-selected="false" data-filter="closed">Closed</button>
        <button type="button" class="filter-chip" role="tab" aria-selected="false" data-filter="_has_room">About a specific room</button>
      </div>

      <div class="list-panel" id="listPanel"></div>
    </div>
  </div>
</div>

<div class="overlay" id="overlay"></div>

<div class="drawer" id="drawer" role="dialog" aria-modal="true" aria-labelledby="drawerTitle">
  <div class="drawer-head">
    <h2 id="drawerTitle">Inquiry</h2>
    <button class="drawer-close" id="drawerClose">&times;</button>
  </div>
  <div class="drawer-body" id="drawerBody"></div>
</div>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script type="application/json" id="inquiries-data">{!! json_encode($inquiries) !!}</script>

<script>
(function(){
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  let inquiries = JSON.parse(document.getElementById('inquiries-data').textContent);

  let filter = 'all';
  let search = '';

  const $ = id => document.getElementById(id);

  document.querySelectorAll('[data-href]').forEach(el => {
    el.addEventListener('click', () => { window.location.href = el.dataset.href; });
  });

  $('logoutBtn').addEventListener('click', async () => {
    await fetch('/logout', { method:'POST', headers:{ 'X-CSRF-TOKEN': csrf } });
    window.location.href = '/';
  });

  function toast(msg, isError){
    const el = $('toast');
    el.textContent = msg;
    el.classList.toggle('error', !!isError);
    el.classList.add('visible');
    setTimeout(() => el.classList.remove('visible'), 2800);
  }

  // Owner / Admin tag next to staff names (styles: partials/role-tag-style).
  const roleTag = t => t ? `<span class="role-tag role-tag-${t}">${t === 'owner' ? 'Owner' : 'Admin'}</span>` : '';

  function esc(s){
    const d = document.createElement('div');
    d.textContent = s ?? '';
    return d.innerHTML;
  }

  function val(v){
    return (v === null || v === undefined || v === '')
      ? '<span class="v empty-v">Not provided</span>'
      : `<span class="v">${esc(v)}</span>`;
  }

  async function api(url, options = {}){
    const headers = Object.assign({ 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' }, options.headers || {});
    const res = await fetch(url, Object.assign({}, options, { headers }));
    const body = await res.json().catch(() => ({}));
    if(!res.ok && res.status !== 207){
      throw new Error(body.message || (body.errors ? Object.values(body.errors)[0][0] : `Request failed (${res.status})`));
    }
    return { ok: res.ok, status: res.status, body };
  }

  const STATUS_LABEL = { new:'New', contacted:'Replied', converted:'Converted', closed:'Closed' };

  function visible(){
    return inquiries.filter(i => {
      if(filter === '_has_room' && !i.room_no) return false;
      if(filter !== 'all' && filter !== '_has_room' && i.status !== filter) return false;
      if(!search) return true;
      const hay = `${i.full_name ?? ''} ${i.message ?? ''}`.toLowerCase();
      return hay.includes(search);
    });
  }

  function renderList(){
    const list = visible();

    if(list.length === 0){
      $('listPanel').innerHTML = '<div class="empty">No inquiries match this view.</div>';
      return;
    }

    $('listPanel').innerHTML = list.map(i => `
      <div class="list-row" data-open="${i.id}" tabindex="0" role="button" aria-label="Open inquiry from ${esc(i.full_name)}">
        <span class="lr-name">${esc(i.full_name)}</span>
        ${i.room_no ? `<span class="lr-room">Room ${esc(i.room_no)}</span>` : ''}
        <span class="lr-msg">${esc(i.message)}</span>
        <span class="lr-date">${esc(i.created_at)}</span>
        <span class="badge ${i.status}">${STATUS_LABEL[i.status] ?? i.status}</span>
      </div>`).join('');

    $('listPanel').querySelectorAll('[data-open]').forEach(row => {
      row.addEventListener('click', () => openDrawer(Number(row.dataset.open)));
      row.addEventListener('keydown', (e) => {
        if(e.key === 'Enter' || e.key === ' '){ e.preventDefault(); openDrawer(Number(row.dataset.open)); }
      });
    });
  }

  // Pre-filled answers to common questions. Details come from Pureza Station's signed documents (Sept 30, 2026); update here if rates or rules change.
  const TEMPLATES = {
    availability: {
      label: 'Room availability',
      keywords: ['available', 'availability', 'vacant', 'vacancy', 'slot', 'bakante', 'may room'],
      body: `Thank you for your interest in Pureza Station Dormitory!

{room_line}You can see which rooms and beds are open right now on the Rooms page of our website (thenestphils.purezastationdormitory.com/rooms). Bed availability updates as soon as a slot is reserved, so we recommend applying early.

We offer the following:
- Solo fan room (ground floor): ₱4,500/month
- Room with AC, 4 persons (ground floor): ₱4,500/month per bed
- Room with AC, 4 persons (2nd–5th floor): ₱4,200/month per bed
- Room with AC, 6 persons (2nd–5th floor): ₱4,000/month per bed
- Room with AC, 10–16 persons (2nd–5th floor): ₱3,800/month per bed

To reserve a bed, just submit an application on our website.`,
    },
    rates: {
      label: 'Rates & payment',
      keywords: ['rate', 'price', 'how much', 'magkano', 'cost', 'rent', 'monthly', 'fee', 'payment', 'deposit', 'advance'],
      body: `Thank you for your inquiry!

{room_line}Here are our current monthly rates:
- Solo fan room (ground floor): ₱4,500/month
- Room with AC, 4 persons (ground floor): ₱4,500/month per bed
- Room with AC, 4 persons (2nd–5th floor): ₱4,200/month per bed
- Room with AC, 6 persons (2nd–5th floor): ₱4,000/month per bed
- Room with AC, 10–16 persons (2nd–5th floor): ₱3,800/month per bed

Short-term stay (less than 3 months): ₱4,500/month. Transient: ₱300/night.

Upon registration, we collect a reservation fee made up of one (1) month advance rent and one (1) month security deposit. The reservation is valid for one month. The advance covers your first month, and rent is then due every 1st of the month (3-day grace period; a 10% late penalty applies after that).

The minimum stay is 3 months. The security deposit is refundable after the minimum stay, less any unpaid charges or damages, within 21 days after check-out.

We accept cash, GCash and bank deposit (BDO).`,
    },
    viewing: {
      label: 'Visit / viewing',
      keywords: ['visit', 'view', 'viewing', 'tour', 'see the room', 'tingnan', 'schedule', 'ocular'],
      body: `Thank you for reaching out!

You are welcome to visit Pureza Station Dormitory at 329 C De Dios, Brgy. 632, Sta. Mesa, Manila. Kindly reply with your preferred date and time so we can confirm and prepare for your visit. Visitors are received at the receiving area.

In the meantime, you can take a virtual tour of our rooms on our website: thenestphils.purezastationdormitory.com/vr-tour`,
    },
    requirements: {
      label: 'Requirements',
      keywords: ['requirement', 'document', 'needed', 'kailangan', 'apply', 'application', 'reserve', 'reservation'],
      body: `Thank you for your interest in staying with us!

You can apply online at thenestphils.purezastationdormitory.com/apply. Please prepare the following:
- A valid ID (photo or PDF)
- Your school or company name and address
- Your home address and cellphone number
- An emergency contact person (name, cellphone number and relationship)
- Your preferred room, bed and move-in date

During the application you will read and sign our Tenant Agreement, Rules and Regulations, and Payments and Fees Schedule. Once approved, a reservation fee (one month advance rent plus one month security deposit) secures your bed.`,
    },
    rules: {
      label: 'House rules',
      keywords: ['rule', 'curfew', 'visitor', 'pet', 'cook', 'policy', 'allowed', 'bawal', 'pwede'],
      body: `Thank you for your question!

Here are the main house rules of Pureza Station Dormitory:
- Quiet hours: 10:00 PM to 6:00 AM
- Curfew: 11:00 PM to 4:00 AM (please inform management in advance if you will be out late)
- Visitors are received at the receiving area only; no overnight guests
- No alcohol, smoking or vaping on the premises
- No pets
- No cooking stoves, gas or other hazardous items (₱500 fine)
- High-power appliances need management approval
- Aircon runs from 10:00 PM to 5:00 AM
- Washing clothes inside the dorm is not allowed; a laundry service is available nearby

You can read the full Rules and Regulations on our website: thenestphils.purezastationdormitory.com/dorm-info`,
    },
    general: {
      label: 'General',
      keywords: [],
      body: `Thank you for your inquiry about Pureza Station Dormitory!

[Your answer]

You can find our rooms, rates, virtual tour and house rules on our website: thenestphils.purezastationdormitory.com. You may also reach us at dormitorypurezastation@gmail.com or on our Facebook page, Pureza Station Dormitory.`,
    },
  };

  // Picks the template whose keywords appear most in the inquiry message.
  function suggestTemplate(i){
    const msg = (i.message ?? '').toLowerCase();
    let best = null, bestScore = 0;
    for (const [key, t] of Object.entries(TEMPLATES)){
      const score = t.keywords.filter(k => msg.includes(k)).length;
      if(score > bestScore){ best = key; bestScore = score; }
    }
    return best;
  }

  function fillTemplate(key, i){
    const firstName = (i.full_name ?? '').trim().split(/\s+/)[0] || 'there';
    const roomLine = i.room_no
      ? `Regarding Room ${i.room_no}: you can check its open beds on the Rooms page of our website.\n\n`
      : (i.preferred_room_type ? `Regarding your interest in a ${i.preferred_room_type} room:\n\n` : '');
    return `Hi ${firstName},\n\n` + TEMPLATES[key].body.replace('{room_line}', roomLine) + `\n\nThank you!`;
  }

  function replySectionHtml(i){
    if(i.reply_message){
      return `
        <div class="already-replied">
          <strong>Replied${i.replied_at ? ' on ' + esc(i.replied_at) : ''}${i.replied_by ? ' by ' + esc(i.replied_by) : ''}.</strong>${i.replied_by ? roleTag(i.replied_by_tag) : ''}
          <div class="msg-box" style="background:#fff;margin-top:10px;">${esc(i.reply_message)}</div>
        </div>`;
    }

    if(!i.email){
      return `<div class="already-replied" style="background:#fdf0f0;border-color:#f3cccc;color:#c0463d;">No email address on file, so a reply cannot be sent for this inquiry.</div>`;
    }

    const suggested = suggestTemplate(i);
    return `
      <div class="reply-box">
        <div class="tpl-label">Quick replies${suggested ? ' (suggested answer filled in, edit before sending)' : ''}</div>
        <div class="tpl-row">
          ${Object.entries(TEMPLATES).map(([key, t]) => `
            <button type="button" class="tpl-chip ${key === suggested ? 'active' : ''}" data-tpl="${key}">${t.label}</button>
          `).join('')}
        </div>
        <textarea id="replyText" placeholder="Type your reply...">${suggested ? esc(fillTemplate(suggested, i)) : ''}</textarea>
        <button class="btn primary" id="sendReplyBtn" style="width:100%;">Send Reply</button>
      </div>`;
  }

  function openDrawer(id){
    const i = inquiries.find(x => x.id === id);
    if(!i) return;

    $('drawerTitle').textContent = `Inquiry from ${i.full_name}`;

    $('drawerBody').innerHTML = `
      <div class="sec">
        <h3>Contact Details</h3>
        <div class="kv">
          <div><div class="k">Full name</div>${val(i.full_name)}</div>
          <div><div class="k">Contact number</div>${val(i.contact_number)}</div>
          <div><div class="k">Email</div>${val(i.email)}</div>
          <div><div class="k">Room type interest</div>${val(i.preferred_room_type)}</div>
          <div><div class="k">Room reference</div>${val(i.room_no)}</div>
          <div><div class="k">Submitted</div>${val(i.created_at)}</div>
        </div>
      </div>

      <div class="sec">
        <h3>Message</h3>
        <div class="msg-box">${esc(i.message) || '(No message provided)'}</div>
      </div>

      <div class="sec">
        <h3>Reply</h3>
        ${replySectionHtml(i)}
      </div>

      <div class="sec">
        <h3>Status</h3>
        <div class="status-row">
          ${Object.entries(STATUS_LABEL).map(([value, label]) => `
            <button class="btn ${i.status === value ? 'primary' : ''}" data-status="${value}">${label}</button>
          `).join('')}
        </div>
      </div>`;

    $('drawerBody').querySelectorAll('[data-tpl]').forEach(chip => {
      chip.addEventListener('click', () => {
        const box = $('replyText');
        const edited = box.value.trim() && box.dataset.filled !== box.value;
        if(edited && !confirm('Replace your current reply with this template?')) return;
        box.value = fillTemplate(chip.dataset.tpl, i);
        box.dataset.filled = box.value;
        $('drawerBody').querySelectorAll('[data-tpl]').forEach(c => c.classList.toggle('active', c === chip));
        box.focus();
      });
    });
    if($('replyText')) $('replyText').dataset.filled = $('replyText').value;

    const sendBtn = $('sendReplyBtn');
    if(sendBtn){
      sendBtn.addEventListener('click', async function(){
        const text = $('replyText').value.trim();
        if(!text) return toast('Write a reply before sending.', true);

        this.disabled = true;
        try {
          const { status, body } = await api(`/inquiries/${i.id}/reply`, {
            method:'POST',
            headers:{ 'Content-Type':'application/json' },
            body: JSON.stringify({ reply_message: text }),
          });

          Object.assign(i, body.inquiry ? {
            reply_message: text,
            status: body.inquiry.status,
            replied_at: 'just now',
          } : {});

          renderList();
          openDrawer(i.id);

          if(status === 207){
            toast('Reply saved, but the email failed to send.', true);
          } else {
            toast('Reply sent successfully.');
          }
        } catch(e){ toast(e.message, true); this.disabled = false; }
      });
    }

    $('drawerBody').querySelectorAll('[data-status]').forEach(btn => {
      btn.addEventListener('click', async () => {
        try {
          await api(`/inquiries/${i.id}/status`, {
            method:'PATCH',
            headers:{ 'Content-Type':'application/json' },
            body: JSON.stringify({ status: btn.dataset.status }),
          });
          i.status = btn.dataset.status;
          renderList();
          openDrawer(i.id);
          toast('Status updated.');
        } catch(e){ toast(e.message, true); }
      });
    });

    $('overlay').classList.add('open');
    $('drawer').classList.add('open');
  }

  function closeDrawer(){
    $('overlay').classList.remove('open');
    $('drawer').classList.remove('open');
  }

  $('drawerClose').addEventListener('click', closeDrawer);
  $('overlay').addEventListener('click', closeDrawer);

  $('filters').querySelectorAll('[data-filter]').forEach(chip => {
    chip.addEventListener('click', () => {
      filter = chip.dataset.filter;
      $('filters').querySelectorAll('[data-filter]').forEach(c => {
        const active = c === chip;
        c.classList.toggle('active', active);
        c.setAttribute('aria-selected', active ? 'true' : 'false');
      });
      renderList();
    });
  });

  $('searchInput').addEventListener('input', function(){
    search = this.value.trim().toLowerCase();
    renderList();
  });

  renderList();
})();
</script>

<script>
(function(){
  // Remember collapsed/expanded across page loads (each admin page is a
  // full reload, not a single-page app, so this has to be localStorage
  // rather than in-memory state) so the sidebar doesn't reset every time
  // you navigate.
  const SIDEBAR_COLLAPSE_KEY = 'nestph_sidebar_collapsed';
  const hamburgerBtn = document.getElementById('hamburgerBtn');
  const sidebar = document.getElementById('sidebar');
  if (hamburgerBtn && sidebar) {
    if (localStorage.getItem(SIDEBAR_COLLAPSE_KEY) === '1') {
      sidebar.classList.add('collapsed');
    }
    hamburgerBtn.addEventListener('click', () => {
      const collapsed = sidebar.classList.toggle('collapsed');
      localStorage.setItem(SIDEBAR_COLLAPSE_KEY, collapsed ? '1' : '0');
    });
  }

  document.querySelectorAll('.sidebar [tabindex="0"], .hamburger, .topbar-icon, .back-arrow').forEach(el => {
    el.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        el.click();
      }
    });
  });

  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    const drawer = document.getElementById('drawer');
    if (drawer && drawer.classList.contains('open')) document.getElementById('drawerClose').click();
  });
})();
</script>

</body>
</html>