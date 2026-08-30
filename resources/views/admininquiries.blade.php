<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH — Inquiry Management</title>
<style>
  :root{
    --green-dark:#3f6b4a; --green-mid:#4f7c57;
    --green-sidebar-top:#5b8a63; --green-sidebar-bottom:#2c4a35;
    --green-accent:#2f6f3c; --green-btn:#2f6b3a; --green-btn-hover:#255a2f;
    --status-occupied:#d9564f; --status-vacant:#7fc98a; --status-vacant-bg:#d9f2dd;
    --status-maintenance:#c9962f; --status-maintenance-bg:#f6ecd6;
    --bg-page:#eef1ee; --card-bg:#ffffff;
    --text-dark:#243026; --text-mid:#5b6b60; --text-light:#8a9690; --border:#e2e6e2;
    --font-body:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;
  }
  *{box-sizing:border-box;}
  html,body{ margin:0; padding:0; font-family:var(--font-body); background:var(--bg-page); color:var(--text-dark); }
  .app{ display:flex; min-height:100vh; }

  .sidebar{ width:220px; flex-shrink:0; background:linear-gradient(180deg,var(--green-sidebar-top),var(--green-sidebar-bottom)); color:#eaf0ea; display:flex; flex-direction:column; padding:20px 0; }
  .sidebar-logo{ display:flex; align-items:center; gap:8px; padding:0 20px 22px 20px; font-weight:700; font-size:17px; border-bottom:1px solid rgba(255,255,255,0.12); margin-bottom:14px; }
  .sidebar-logo .logo-mark{ width:18px; height:18px; border:2px solid #eaf0ea; display:inline-block; position:relative; }
  .sidebar-logo .logo-mark::before,.sidebar-logo .logo-mark::after{ content:''; position:absolute; background:#eaf0ea; width:2px; height:14px; top:0; left:6px; }
  .sidebar-section-label{ font-size:11px; text-transform:uppercase; letter-spacing:1px; color:rgba(234,240,234,0.55); padding:4px 20px 10px 20px; font-weight:600; }
  .nav-list{ list-style:none; margin:0; padding:0; flex:1; }
  .nav-item{ display:flex; align-items:center; gap:12px; padding:11px 20px; font-size:13.5px; color:rgba(234,240,234,0.78); cursor:pointer; border-left:3px solid transparent; }
  .nav-item:hover{ background:rgba(255,255,255,0.06); color:#fff; }
  .nav-item.active{ background:rgba(255,255,255,0.14); color:#fff; font-weight:600; border-left:3px solid #fff; }
  .nav-item .icon svg{ width:16px; height:16px; }
  .sidebar-footer{ padding:14px 20px 0 20px; border-top:1px solid rgba(255,255,255,0.12); margin-top:10px; }
  .sidebar-footer .nav-item{ padding-left:0; }

  .main{ flex:1; display:flex; flex-direction:column; min-width:0; }
  .topbar{ display:flex; align-items:center; gap:16px; background:linear-gradient(90deg,var(--green-mid),var(--green-dark)); padding:14px 28px; }
  .topbar .hamburger{ width:20px; height:16px; display:flex; flex-direction:column; justify-content:space-between; cursor:pointer; }
  .topbar .hamburger span{ display:block; height:2px; background:#eaf0ea; border-radius:2px; }
  .search-box{ flex:1; max-width:420px; display:flex; align-items:center; gap:8px; background:rgba(255,255,255,0.92); border-radius:8px; padding:9px 14px; }
  .search-box svg{ width:15px; height:15px; color:#8a9690; }
  .search-box input{ border:none; outline:none; background:transparent; font-size:13.5px; width:100%; }
  .topbar-right{ margin-left:auto; }
  .topbar-icon{ width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.9); color:var(--green-dark); display:flex; align-items:center; justify-content:center; cursor:pointer; }
  .topbar-icon svg{ width:16px; height:16px; }

  .content{ padding:28px 32px 48px 32px; flex:1; }
  .page-head{ display:flex; align-items:center; gap:12px; margin-bottom:20px; }
  .back-arrow{ width:34px; height:34px; border-radius:8px; background:var(--card-bg); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-mid); }
  .page-head h1{ font-size:20px; font-weight:700; margin:0; }

  .filters{ display:flex; gap:8px; margin-bottom:18px; flex-wrap:wrap; }
  .filter-chip{ border:1px solid var(--border); background:#fff; border-radius:20px; padding:7px 16px; font-size:12px; font-weight:600; color:var(--text-mid); cursor:pointer; }
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
  .already-replied{ background:var(--status-vacant-bg); border:1px solid var(--status-vacant); border-radius:10px; padding:14px 16px; font-size:12.5px; color:var(--green-accent); line-height:1.6; }

  .status-row{ display:flex; gap:8px; flex-wrap:wrap; }

  .toast{ position:fixed; bottom:22px; right:22px; background:var(--green-accent); color:#fff; padding:12px 20px; border-radius:8px; font-size:13px; display:none; z-index:99; box-shadow:0 6px 18px rgba(0,0,0,.2); }
  .toast.error{ background:var(--status-occupied); }
  .toast.visible{ display:block; }
</style>
</head>
<body>
<div class="app">

  <aside class="sidebar">
    <div class="sidebar-logo"><span class="logo-mark"></span> NEST.PH</div>
    <div class="sidebar-section-label">Quick Access</div>
    <ul class="nav-list">
      <li class="nav-item" data-href="{{ route('dashboard') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></span>Dashboard</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></span>Tenant Manager</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></span>Billing and Payments</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg></span>Delinquency</li>
      <li class="nav-item" data-href="{{ route('admin.addfloor') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="4" width="7" height="7"/><rect x="3" y="15" width="7" height="7"/><rect x="14" y="15" width="7" height="7"/></svg></span>Vacancy Monitor</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/></svg></span>Applications</li>
      <li class="nav-item active" data-href="{{ route('inquiries.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></span>Inquiries</li>
      <li class="nav-item" data-href="{{ route('vr.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 3v18M16 3v18"/></svg></span>VR Management</li>
      <li class="nav-item" data-href="{{ route('contracts.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg></span>Lease Management</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/></svg></span>Admin Privileges</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 21V9l8-6 8 6v12"/><path d="M9 21v-6h6v6"/></svg></span>Business Information</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M6 10h4M6 14h2"/></svg></span>Dormitory Profile</li>
    </ul>
    <div class="sidebar-footer"><div class="nav-item" id="logoutBtn"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.4 5.6a9 9 0 11-12.8 0M12 3v8"/></svg></span>Log Out</div></div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div class="hamburger"><span></span><span></span><span></span></div>
      <div class="search-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg><input type="text" id="searchInput" placeholder="Search name or message"></div>
      <div class="topbar-right">
        <div class="topbar-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
      </div>
    </div>

    <div class="content">
      <div class="page-head">
        <div class="back-arrow" data-href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
        <h1>Inquiry Management</h1>
      </div>

      <div class="filters" id="filters">
        <div class="filter-chip active" data-filter="all">All</div>
        <div class="filter-chip" data-filter="new">New</div>
        <div class="filter-chip" data-filter="contacted">Replied</div>
        <div class="filter-chip" data-filter="converted">Converted</div>
        <div class="filter-chip" data-filter="closed">Closed</div>
        <div class="filter-chip" data-filter="_has_room">About a specific room</div>
      </div>

      <div class="list-panel" id="listPanel"></div>
    </div>
  </div>
</div>

<div class="overlay" id="overlay"></div>

<div class="drawer" id="drawer">
  <div class="drawer-head">
    <h2 id="drawerTitle">Inquiry</h2>
    <button class="drawer-close" id="drawerClose">&times;</button>
  </div>
  <div class="drawer-body" id="drawerBody"></div>
</div>

<div class="toast" id="toast"></div>

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
      <div class="list-row" data-open="${i.id}">
        <span class="lr-name">${esc(i.full_name)}</span>
        ${i.room_no ? `<span class="lr-room">Room ${esc(i.room_no)}</span>` : ''}
        <span class="lr-msg">${esc(i.message)}</span>
        <span class="lr-date">${esc(i.created_at)}</span>
        <span class="badge ${i.status}">${STATUS_LABEL[i.status] ?? i.status}</span>
      </div>`).join('');

    $('listPanel').querySelectorAll('[data-open]').forEach(row => {
      row.addEventListener('click', () => openDrawer(Number(row.dataset.open)));
    });
  }

  function replySectionHtml(i){
    if(i.reply_message){
      return `
        <div class="already-replied">
          <strong>Replied${i.replied_at ? ' on ' + esc(i.replied_at) : ''}${i.replied_by ? ' by ' + esc(i.replied_by) : ''}.</strong>
          <div class="msg-box" style="background:#fff;margin-top:10px;">${esc(i.reply_message)}</div>
        </div>`;
    }

    if(!i.email){
      return `<div class="already-replied" style="background:#fdf0f0;border-color:#f3cccc;color:#b3261e;">No email address on file — a reply cannot be sent for this inquiry.</div>`;
    }

    return `
      <div class="reply-box">
        <textarea id="replyText" placeholder="Type your reply..."></textarea>
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
      $('filters').querySelectorAll('[data-filter]').forEach(c => c.classList.toggle('active', c === chip));
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

</body>
</html>