<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH — Tickets</title>
<style>
  :root{
    --green-dark:#3f6b4a; --green-mid:#4f7c57;
    --green-sidebar-top:#5b8a63; --green-sidebar-bottom:#2c4a35;
    --green-accent:#2f6f3c; --green-btn:#2f6b3a; --green-btn-hover:#255a2f;
    --status-occupied:#d9564f; --status-occupied-bg:#f7d9d7;
    --status-vacant:#7fc98a; --status-vacant-bg:#d9f2dd;
    --status-maintenance:#c9962f; --status-maintenance-bg:#f6ecd6;
    --purple:#7a4fc9; --purple-bg:#e9defa;
    --blue:#33629e; --blue-bg:#e3ecf7;
    --bg-page:#eef1ee; --card-bg:#ffffff;
    --text-dark:#243026; --text-mid:#5b6b60; --text-light:#8a9690; --border:#e2e6e2;
    --font-body:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;
  }
  *{box-sizing:border-box;}
  html,body{ margin:0; padding:0; font-family:var(--font-body); background:var(--bg-page); color:var(--text-dark); }
  .app{ display:flex; min-height:100vh; }

  /* ===== Sidebar ===== */
  .sidebar{
    width:220px; flex-shrink:0;
    background:linear-gradient(180deg, var(--green-sidebar-top) 0%, var(--green-sidebar-bottom) 100%);
    color:#eaf0ea; display:flex; flex-direction:column; padding:18px 0;
    position:sticky; top:0; height:100vh; overflow:hidden;
    transition:width 0.2s ease;
  }
  .sidebar-logo{ display:flex; align-items:center; gap:8px; padding:0 20px 18px 20px; font-weight:700; font-size:16px; border-bottom:1px solid rgba(255,255,255,0.12); margin-bottom:12px; white-space:nowrap; flex-shrink:0; }
  .sidebar-logo .logo-mark{ width:16px; height:16px; border:2px solid #eaf0ea; display:inline-block; position:relative; flex-shrink:0; }
  .sidebar-logo .logo-mark::before,.sidebar-logo .logo-mark::after{ content:''; position:absolute; background:#eaf0ea; width:2px; height:12px; top:0; left:5px; }
  .sidebar-section-label{ font-size:10.5px; text-transform:uppercase; letter-spacing:1px; color:rgba(234,240,234,0.55); padding:4px 20px 8px 20px; font-weight:600; white-space:nowrap; flex-shrink:0; }
  .nav-list{ list-style:none; margin:0; padding:0 0 8px 0; flex:1; min-height:0; overflow-y:auto; overflow-x:hidden; scrollbar-width:thin; scrollbar-color:rgba(255,255,255,0.28) transparent; }
  .nav-list::-webkit-scrollbar{ width:5px; }
  .nav-list::-webkit-scrollbar-track{ background:transparent; }
  .nav-list::-webkit-scrollbar-thumb{ background:rgba(255,255,255,0.28); border-radius:10px; }
  .nav-item{ display:flex; align-items:center; gap:11px; padding:9px 20px; font-size:13px; color:rgba(234,240,234,0.78); cursor:pointer; border-left:3px solid transparent; white-space:nowrap; flex-shrink:0; }
  .nav-item:hover{ background:rgba(255,255,255,0.06); color:#fff; }
  .nav-item.active{ background:rgba(255,255,255,0.14); color:#fff; font-weight:600; border-left:3px solid #fff; }
  .nav-item .icon svg{ width:15px; height:15px; flex-shrink:0; }
  .sidebar-footer{ padding:12px 20px 0 20px; border-top:1px solid rgba(255,255,255,0.12); margin-top:8px; flex-shrink:0; }
  .sidebar-footer .nav-item{ padding:9px 12px; border-radius:8px; background:rgba(0,0,0,0.28); border-left:none; }
  .sidebar-footer .nav-item:hover{ background:rgba(0,0,0,0.42); color:#fff; }
  .sidebar.collapsed{ width:64px; }
  .sidebar.collapsed .sidebar-logo{ justify-content:center; padding-left:0; padding-right:0; }
  .sidebar.collapsed .sidebar-logo .logo-text{ display:none; }
  .sidebar.collapsed .sidebar-section-label{ display:none; }
  .sidebar.collapsed .nav-item{ justify-content:center; padding-left:0; padding-right:0; gap:0; }
  .sidebar.collapsed .nav-item .label{ display:none; }

  /* ===== Main / Topbar ===== */
  .main{ flex:1; min-width:0; }
  .topbar{ display:flex; align-items:center; gap:16px; background:linear-gradient(90deg, var(--green-mid), var(--green-dark)); padding:14px 28px; position:sticky; top:0; z-index:20; }
  .topbar .hamburger{ width:20px; height:16px; display:flex; flex-direction:column; justify-content:space-between; cursor:pointer; }
  .topbar .hamburger span{ display:block; height:2px; background:#eaf0ea; border-radius:2px; }
  .search-box{ position:relative; flex:1; max-width:420px; display:flex; align-items:center; gap:8px; background:rgba(255,255,255,0.92); border-radius:8px; padding:9px 14px; }
  .search-box svg{ width:15px; height:15px; color:#8a9690; flex-shrink:0; }
  .search-box input{ border:none; outline:none; background:transparent; font-size:13.5px; width:100%; color:var(--text-dark); }
  .search-results{ position:absolute; top:calc(100% + 8px); left:0; right:0; background:var(--card-bg); border:1px solid var(--border); border-radius:10px; box-shadow:0 10px 26px rgba(20,30,20,0.14); max-height:280px; overflow-y:auto; z-index:50; display:none; }
  .search-results.visible{ display:block; }
  .search-results a, .search-results .search-empty, .search-results .search-disabled{ display:block; padding:10px 14px; font-size:13px; color:var(--text-dark); text-decoration:none; cursor:pointer; }
  .search-results a:hover{ background:#f3f6f3; }
  .search-results .search-empty, .search-results .search-disabled{ color:var(--text-light); font-style:italic; }
  .topbar-right{ margin-left:auto; display:flex; align-items:center; gap:18px; }
  .topbar-icon{ width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.9); color:var(--green-dark); display:flex; align-items:center; justify-content:center; cursor:pointer; }
  .topbar-icon svg{ width:16px; height:16px; }

  .content{ padding:28px 32px 48px 32px; flex:1; }
  .page-head{ display:flex; align-items:center; gap:12px; margin-bottom:22px; }
  .back-arrow{ width:34px; height:34px; border-radius:8px; background:var(--card-bg); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-mid); flex-shrink:0; }
  .page-head h1{ font-size:20px; font-weight:700; margin:0; color:var(--text-dark); }

  .stats-row{ display:grid; grid-template-columns:repeat(3, 1fr); gap:16px; margin-bottom:20px; }
  .stat-card{ background:var(--card-bg); border-radius:12px; border:1px solid var(--border); padding:18px 20px; display:flex; align-items:center; gap:14px; box-shadow:0 1px 2px rgba(20,30,20,0.03); }
  .stat-icon{ width:46px; height:46px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .stat-icon svg{ width:20px; height:20px; }
  .stat-icon.open{ background:var(--blue-bg); color:var(--blue); }
  .stat-icon.progress{ background:var(--status-maintenance-bg); color:var(--status-maintenance); }
  .stat-icon.overdue{ background:var(--status-occupied-bg); color:var(--status-occupied); }
  .stat-label{ font-size:12.5px; color:var(--text-mid); margin-bottom:3px; }
  .stat-value{ font-size:24px; font-weight:700; color:var(--text-dark); }

  .filters-row{ display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap; }
  .search-input{ flex:1; min-width:200px; border:1px solid var(--border); border-radius:9px; padding:10px 14px; font-size:13px; font-family:var(--font-body); }
  .filters-row select{ border:1px solid var(--border); border-radius:9px; padding:10px 14px; font-size:13px; font-family:var(--font-body); background:#fff; color:var(--text-dark); }

  .empty{ text-align:center; color:var(--text-light); font-style:italic; padding:50px 20px; background:var(--card-bg); border:1px solid var(--border); border-radius:12px; }

  .app-grid{ display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:16px; }
  .app-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:18px; cursor:pointer; display:flex; flex-direction:column; gap:10px; transition:box-shadow .15s ease, border-color .15s ease; }
  .app-card:hover{ box-shadow:0 4px 14px rgba(20,30,20,0.08); border-color:#cfd8d0; }
  .ac-top{ display:flex; justify-content:space-between; align-items:flex-start; gap:10px; }
  .ac-num{ font-size:11.5px; color:var(--text-light); font-weight:600; }
  .ac-title{ font-size:14.5px; font-weight:700; color:var(--text-dark); margin:2px 0 0 0; }
  .ac-meta{ font-size:12px; color:var(--text-mid); }
  .ac-cat{ font-size:11.5px; color:var(--text-mid); background:#f4f7f4; border-radius:6px; padding:4px 9px; display:inline-block; width:fit-content; }

  .badge{ font-size:11px; font-weight:700; padding:4px 10px; border-radius:20px; display:inline-block; white-space:nowrap; }
  .badge.open{ background:var(--blue-bg); color:var(--blue); }
  .badge.seen{ background:var(--purple-bg); color:var(--purple); }
  .badge.in_progress{ background:var(--status-maintenance-bg); color:var(--status-maintenance); }
  .badge.resolved{ background:var(--status-vacant-bg); color:var(--green-accent); }
  .badge.rejected{ background:var(--status-occupied-bg); color:var(--status-occupied); }

  .overdue-flag{ font-size:11px; font-weight:700; color:var(--status-occupied); background:var(--status-occupied-bg); border-radius:20px; padding:4px 10px; display:inline-flex; align-items:center; gap:5px; width:fit-content; }
  .unresolved-note{ font-size:11.5px; color:var(--text-light); }

  .ac-bottom{ display:flex; justify-content:space-between; align-items:center; gap:8px; margin-top:auto; padding-top:10px; border-top:1px solid #f0f2f0; }
  .priority-select{ font-size:11.5px; border:1px solid var(--border); border-radius:6px; padding:5px 8px; font-family:var(--font-body); background:#fff; color:var(--text-mid); }
  .priority-select.urgent{ color:var(--status-occupied); border-color:#f2cfcc; background:var(--status-occupied-bg); font-weight:700; }
  .ac-assigned{ font-size:11.5px; color:var(--text-light); }

  .lightbox{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.8); z-index:90; align-items:center; justify-content:center; padding:30px; }
  .lightbox.open{ display:flex; }
  .lightbox img{ max-width:100%; max-height:100%; border-radius:8px; }
  .modal-overlay{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:60; align-items:center; justify-content:center; padding:20px; }
  .modal-overlay.open{ display:flex; }
  .modal-box{ background:#fff; border-radius:14px; width:100%; max-width:560px; max-height:90vh; overflow-y:auto; }
  .modal-head{ padding:20px 24px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:flex-start; }
  .modal-head .mh-tenant{ font-size:12.5px; color:var(--text-mid); margin-bottom:4px; }
  .modal-head h2{ font-size:16px; font-weight:700; margin:0; }
  .modal-head .mh-num{ font-size:12px; color:var(--text-light); }
  .modal-body{ padding:22px 24px; }
  .modal-body .mb-desc{ font-size:13px; color:var(--text-mid); line-height:1.6; margin-bottom:16px; }
  .modal-body .mb-attachment{ margin-bottom:16px; }
  .modal-body .mb-attachment img{ width:88px; height:88px; object-fit:cover; border-radius:8px; border:1px solid var(--border); cursor:pointer; }
  .modal-body .fld{ display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
  .modal-body label{ font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-mid); }
  .modal-body select, .modal-body textarea{ border:1px solid var(--border); border-radius:8px; padding:10px 13px; font-size:13px; font-family:var(--font-body); width:100%; }
  .modal-body textarea{ min-height:80px; resize:vertical; }
  .reply-thread{ display:flex; flex-direction:column; gap:8px; margin-bottom:12px; max-height:160px; overflow-y:auto; }
  .reply-item{ background:#f7f9f7; border-radius:8px; padding:10px 12px; font-size:12.5px; }
  .reply-item .r-meta{ color:var(--text-light); font-size:11px; margin-top:4px; }
  .modal-actions{ display:flex; gap:10px; padding:0 24px 24px 24px; }
  .btn{ border-radius:8px; padding:10px 16px; font-size:13px; font-weight:600; cursor:pointer; border:1px solid var(--border); background:#fff; color:var(--text-dark); font-family:var(--font-body); }
  .btn.primary{ background:var(--green-btn); color:#fff; border-color:var(--green-btn); }
  .btn.primary:hover{ background:var(--green-btn-hover); }

  .toast{ position:fixed; bottom:22px; right:22px; background:var(--green-accent); color:#fff; padding:12px 20px; border-radius:8px; font-size:13px; display:none; z-index:99; box-shadow:0 6px 18px rgba(0,0,0,.2); }
  .toast.error{ background:var(--status-occupied); }
  .toast.visible{ display:block; }
</style>
</head>
<body>
<div class="app">

  <aside class="sidebar" id="sidebar">
    <div class="sidebar-logo"><span class="logo-mark"></span><span class="logo-text">NEST.PH</span></div>
    <div class="sidebar-section-label">Quick Access</div>
    <ul class="nav-list">
      <li class="nav-item" data-href="{{ route('dashboard') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></span><span class="label">Dashboard</span></li>
      <li class="nav-item" data-href="{{ route('tenant-manager.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></span><span class="label">Tenant Manager</span></li>
      <li class="nav-item" data-href="{{ route('payments.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></span><span class="label">Billing and Payments</span></li>
      <li class="nav-item" data-href="{{ route('delinquency.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg></span><span class="label">Delinquency</span></li>
      <li class="nav-item" data-href="{{ route('admin.addfloor') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="4" width="7" height="7"/><rect x="3" y="15" width="7" height="7"/><rect x="14" y="15" width="7" height="7"/></svg></span><span class="label">Vacancy Monitor</span></li>
      <li class="nav-item active" data-href="{{ route('tickets.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h18v10H3z"/><path d="M3 12h18"/></svg></span><span class="label">Tickets</span></li>
      <li class="nav-item" data-href="{{ route('applications.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg></span><span class="label">Applications</span></li>
      <li class="nav-item" data-href="{{ route('inquiries.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></span><span class="label">Inquiries</span></li>
      <li class="nav-item" data-href="{{ route('vr.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 3v18M16 3v18"/></svg></span><span class="label">VR Management</span></li>
      <li class="nav-item" data-href="{{ route('contracts.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg></span><span class="label">Lease Management</span></li>
      <li class="nav-item" data-href="{{ route('admin-privileges.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l8 4v6c0 5-3.5 9-8 10-4.5-1-8-5-8-10V6l8-4z"/><path d="M9 12l2 2 4-4"/></svg></span><span class="label">Admin Privileges</span></li>
      <li class="nav-item" data-href="{{ route('dormitory-profile.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M6 10h4M6 14h2"/></svg></span><span class="label">Dormitory Profile</span></li>
    </ul>
    <div class="sidebar-footer"><div class="nav-item" id="logoutBtn"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.4 5.6a9 9 0 11-12.8 0M12 3v8"/></svg></span><span class="label">Log Out</span></div></div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div class="hamburger" id="hamburgerBtn"><span></span><span></span><span></span></div>
      <div class="search-box" id="searchBox">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
        <input type="text" id="pageSearchInput" placeholder="Search pages" autocomplete="off">
        <div class="search-results" id="searchResults"></div>
      </div>
      <div class="topbar-right">
        <div class="topbar-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
      </div>
    </div>

    <div class="content">
      <div class="page-head">
        <div class="back-arrow" data-href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
        <h1>Tickets</h1>
      </div>

      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-icon open"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h18v10H3z"/><path d="M3 12h18"/></svg></div>
          <div><div class="stat-label">Open</div><div class="stat-value" id="statOpen">{{ $openCount }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon progress"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg></div>
          <div><div class="stat-label">In Progress</div><div class="stat-value" id="statProgress">{{ $inProgressCount }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon overdue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg></div>
          <div><div class="stat-label">Overdue</div><div class="stat-value" id="statOverdue">{{ $overdueCount }}</div></div>
        </div>
      </div>

      <div class="filters-row">
        <input type="text" class="search-input" id="searchInput" placeholder="Search tenant or title">
        <select id="categoryFilter">
          <option value="">All Categories</option>
          @foreach($categories as $key => $label)
            <option value="{{ $key }}">{{ $label }}</option>
          @endforeach
        </select>
        <select id="statusFilter">
          <option value="">All Statuses</option>
          @foreach($statuses as $key => $label)
            <option value="{{ $key }}">{{ $label }}</option>
          @endforeach
        </select>
        <select id="priorityFilter">
          <option value="">All Priorities</option>
          @foreach($priorities as $key => $label)
            <option value="{{ $key }}">{{ $label }}</option>
          @endforeach
        </select>
      </div>

      <div class="app-grid" id="ticketGrid"></div>
    </div>
  </div>
</div>

<div class="modal-overlay" id="ticketModal">
  <div class="modal-box">
    <div class="modal-head">
      <div>
        <div class="mh-tenant" id="mTenant"></div>
        <h2 id="mTitle"></h2>
      </div>
      <div class="mh-num" id="mNum"></div>
    </div>
    <div class="modal-body">
      <div class="mb-desc" id="mDesc"></div>
            <div class="mb-attachment" id="mAttachment" style="display:none;">
        <div style="display:flex;gap:8px;flex-wrap:wrap;" id="mAttachmentGrid"></div>
      </div>

      <div class="fld">
        <label>Assign to</label>
        <select id="mAssignSelect"><option value="">Unassigned</option></select>
      </div>

      <div class="fld">
        <label>Update Status</label>
        <select id="mStatusSelect">
          @foreach($statuses as $key => $label)
            <option value="{{ $key }}">{{ $label }}</option>
          @endforeach
        </select>
      </div>

      <div class="fld">
        <label>Reply to tenant</label>
        <div class="reply-thread" id="mReplyThread"></div>
        <textarea id="mReplyInput" placeholder="Write a reply..."></textarea>
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn primary" id="mSaveBtn" style="flex:1;">Save</button>
      <button class="btn" id="mCancelBtn">Cancel</button>
    </div>
  </div>
</div>

<div class="lightbox" id="lightbox"><img id="lightboxImg" alt=""></div>

<div class="toast" id="toast"></div>

<script type="application/json" id="tickets-data">{!! json_encode($tickets) !!}</script>

<script>
(function(){
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  let tickets = JSON.parse(document.getElementById('tickets-data').textContent);

  let search = '';
  let categoryFilter = '';
  let statusFilter = '';
  let priorityFilter = '';
  let openTicketId = null;

  const $ = id => document.getElementById(id);

  document.querySelectorAll('[data-href]').forEach(el => {
    el.addEventListener('click', () => { window.location.href = el.dataset.href; });
  });

  $('logoutBtn').addEventListener('click', async () => {
    await fetch('/logout', { method:'POST', headers:{ 'X-CSRF-TOKEN': csrf } });
    window.location.href = '/';
  });

  $('lightbox').addEventListener('click', () => $('lightbox').classList.remove('open'));

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

  async function api(url, options = {}){
    const headers = Object.assign({ 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' }, options.headers || {});
    const res = await fetch(url, Object.assign({}, options, { headers }));
    const body = await res.json().catch(() => ({}));
    if(!res.ok){
      throw new Error(body.message || (body.errors ? Object.values(body.errors)[0][0] : `Request failed (${res.status})`));
    }
    return body;
  }

  function visible(){
    return tickets.filter(t => {
      if(categoryFilter && t.category !== categoryFilter) return false;
      if(statusFilter && t.status !== statusFilter) return false;
      if(priorityFilter && t.priority !== priorityFilter) return false;
      if(!search) return true;
      const hay = `${t.tenant_name ?? ''} ${t.title ?? ''}`.toLowerCase();
      return hay.includes(search);
    });
  }

  function priorityOptionsHtml(current){
    const opts = [['', 'Unclassified'], ['urgent', 'Urgent'], ['non_urgent', 'Non-Urgent']];
    return opts.map(([val, label]) =>
      `<option value="${val}" ${current === val || (!current && val==='') ? 'selected' : ''}>${label}</option>`
    ).join('');
  }

  function renderGrid(){
    const list = visible();

    if(list.length === 0){
      $('ticketGrid').innerHTML = '<div class="empty">No tickets match this view.</div>';
      return;
    }

    $('ticketGrid').innerHTML = list.map(t => `
      <div class="app-card" data-open="${t.id}">
        <div class="ac-top">
          <div>
            <div class="ac-num">Ticket#${t.id}</div>
            <div class="ac-title">${esc(t.title)}</div>
            <div class="ac-meta">${esc(t.tenant_name ?? 'Unknown tenant')}${t.room_no ? ' — Room ' + esc(t.room_no) : ''}</div>
          </div>
          <span class="badge ${t.status}">${esc(t.status_label)}</span>
        </div>
        <span class="ac-cat">${esc(t.category_label)}</span>
        ${t.is_overdue ? `<span class="overdue-flag">⚠ Overdue</span>` : (t.unresolved_for ? `<span class="unresolved-note">${esc(t.unresolved_for)}</span>` : '')}
        <div class="ac-bottom">
          <select class="priority-select ${t.priority ?? ''}" data-priority-for="${t.id}">${priorityOptionsHtml(t.priority)}</select>
          <span class="ac-assigned">${t.assigned_to_name ? 'Assigned: ' + esc(t.assigned_to_name) : 'Unassigned'}</span>
        </div>
      </div>
    `).join('');

    document.querySelectorAll('[data-open]').forEach(el => {
      el.addEventListener('click', (e) => {
        if(e.target.closest('[data-priority-for]')) return;
        openModal(Number(el.dataset.open));
      });
    });

    document.querySelectorAll('[data-priority-for]').forEach(el => {
      el.addEventListener('click', e => e.stopPropagation());
      el.addEventListener('change', async () => {
        const id = Number(el.dataset.priorityFor);
        const ticket = tickets.find(t => t.id === id);
        try {
          const res = await api(`/tickets/${id}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: ticket.status, priority: el.value || null }),
          });
          Object.assign(ticket, res.ticket);
          toast('Priority updated.');
          renderGrid();
        } catch(e){ toast(e.message, true); renderGrid(); }
      });
    });
  }

  async function openModal(id){
    openTicketId = id;
    try {
      const detail = await api(`/tickets/${id}`);

      $('mTenant').textContent = detail.tenant_name ?? 'Unknown tenant';
      $('mNum').textContent = `Ticket#${detail.id}`;
      $('mTitle').textContent = detail.title;
      $('mDesc').textContent = detail.description;

      if(detail.attachment_urls && detail.attachment_urls.length){
        $('mAttachment').style.display = 'block';
        $('mAttachmentGrid').innerHTML = detail.attachment_urls.map(u => `<img src="${u}" alt="Attachment" data-lightbox="${u}">`).join('');
        document.querySelectorAll('#mAttachmentGrid [data-lightbox]').forEach(img => {
          img.addEventListener('click', () => {
            $('lightboxImg').src = img.dataset.lightbox;
            $('lightbox').classList.add('open');
          });
        });
      } else {
        $('mAttachment').style.display = 'none';
      }
      $('mAssignSelect').innerHTML = '<option value="">Unassigned</option>' +
        detail.assignable_admins.map(a => `<option value="${a.id}" ${a.id === detail.assigned_to ? 'selected' : ''}>${esc(a.name)}</option>`).join('');

      $('mStatusSelect').value = detail.status;

      $('mReplyThread').innerHTML = detail.replies.length
        ? detail.replies.map(r => `<div class="reply-item">${esc(r.message)}<div class="r-meta">${esc(r.author)} — ${esc(r.created_at)}</div></div>`).join('')
        : '<div class="reply-item" style="color:var(--text-light);font-style:italic;">No replies yet.</div>';

      $('mReplyInput').value = '';

      $('ticketModal').classList.add('open');
    } catch(e){ toast(e.message, true); }
  }

  $('mCancelBtn').addEventListener('click', () => $('ticketModal').classList.remove('open'));
  $('ticketModal').addEventListener('click', (e) => { if(e.target.id === 'ticketModal') $('ticketModal').classList.remove('open'); });

  $('mSaveBtn').addEventListener('click', async function(){
    this.disabled = true;
    try {
      const res = await api(`/tickets/${openTicketId}`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          status: $('mStatusSelect').value,
          assigned_to: $('mAssignSelect').value || null,
          reply_message: $('mReplyInput').value.trim() || null,
        }),
      });

      const idx = tickets.findIndex(t => t.id === openTicketId);
      if(idx !== -1) tickets[idx] = res.ticket;

      $('ticketModal').classList.remove('open');
      toast('Ticket updated successfully.');
      renderGrid();
    } catch(e){ toast(e.message, true); }
    this.disabled = false;
  });

  $('searchInput').addEventListener('input', e => { search = e.target.value.toLowerCase(); renderGrid(); });
  $('categoryFilter').addEventListener('change', e => { categoryFilter = e.target.value; renderGrid(); });
  $('statusFilter').addEventListener('change', e => { statusFilter = e.target.value; renderGrid(); });
  $('priorityFilter').addEventListener('change', e => { priorityFilter = e.target.value; renderGrid(); });

  renderGrid();
})();
</script>

<script>
(function(){
  const SIDEBAR_COLLAPSE_KEY = 'nestph_sidebar_collapsed';
  const hamburgerBtn = document.getElementById('hamburgerBtn');
  const sidebar = document.getElementById('sidebar');
  if (hamburgerBtn && sidebar) {
    if (localStorage.getItem(SIDEBAR_COLLAPSE_KEY) === '1') sidebar.classList.add('collapsed');
    hamburgerBtn.addEventListener('click', () => {
      const collapsed = sidebar.classList.toggle('collapsed');
      localStorage.setItem(SIDEBAR_COLLAPSE_KEY, collapsed ? '1' : '0');
    });
  }

  const NEST_PAGES = [
    { label: 'Dashboard', href: '{{ route('dashboard') }}' },
    { label: 'Tenant Manager', href: '{{ route('tenant-manager.index') }}' },
    { label: 'Billing and Payments', href: '{{ route('payments.index') }}' },
    { label: 'Delinquency', href: '{{ route('delinquency.index') }}' },
    { label: 'Vacancy Monitor', href: '{{ route('admin.addfloor') }}' },
    { label: 'Tickets', href: '{{ route('tickets.index') }}' },
    { label: 'Applications', href: '{{ route('applications.index') }}' },
    { label: 'Inquiries', href: '{{ route('inquiries.index') }}' },
    { label: 'VR Management', href: '{{ route('vr.index') }}' },
    { label: 'Lease Management', href: '{{ route('contracts.index') }}' },
    { label: 'Admin Privileges', href: '{{ route('admin-privileges.index') }}' },
    { label: 'Dormitory Profile', href: '{{ route('dormitory-profile.index') }}' },
  ];

  const searchBox = document.getElementById('searchBox');
  const searchInput = document.getElementById('pageSearchInput');
  const searchResults = document.getElementById('searchResults');

  function nestEscapeHtml(str){
    const d = document.createElement('div');
    d.textContent = str ?? '';
    return d.innerHTML;
  }

  if (searchInput) {
    searchInput.addEventListener('input', () => {
      const q = searchInput.value.trim().toLowerCase();
      if (!q) { searchResults.classList.remove('visible'); return; }
      const matches = NEST_PAGES.filter(p => p.label.toLowerCase().includes(q));
      searchResults.innerHTML = matches.length
        ? matches.map(p => p.href
            ? `<a href="${p.href}">${nestEscapeHtml(p.label)}</a>`
            : `<div class="search-disabled">${nestEscapeHtml(p.label)} (Coming Soon)</div>`).join('')
        : `<div class="search-empty">No pages found</div>`;
      searchResults.classList.add('visible');
    });
    document.addEventListener('click', (e) => {
      if (!searchBox.contains(e.target)) searchResults.classList.remove('visible');
    });
  }
})();
</script>
</body>
</html>