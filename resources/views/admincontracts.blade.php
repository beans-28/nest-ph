<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH — Lease Management</title>
<style>
  :root{
    --green-dark:#3f6b4a; --green-mid:#4f7c57;
    --green-sidebar-top:#5b8a63; --green-sidebar-bottom:#2c4a35;
    --green-accent:#2f6f3c; --green-btn:#2f6b3a; --green-btn-hover:#255a2f;
    --status-occupied:#d9564f; --status-vacant:#7fc98a; --status-vacant-bg:#d9f2dd;
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

  /* ===== Sidebar: logo pinned at top, Log Out pinned at bottom, and only
     the middle nav list scrolls if it doesn't fit (like textbee.dev's
     sidebar). Collapsible via the hamburger button. ===== */
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

  .nav-list{
    list-style:none; margin:0; padding:0 0 8px 0; flex:1; min-height:0;
    overflow-y:auto; overflow-x:hidden;
    scrollbar-width:thin; scrollbar-color:rgba(255,255,255,0.28) transparent;
  }
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
  .sidebar.collapsed .sidebar-footer{ padding-left:10px; padding-right:10px; }
  .sidebar.collapsed .sidebar-footer .nav-item{ padding:9px 0; }

  .main{ flex:1; display:flex; flex-direction:column; min-width:0; }
  .topbar{ display:flex; align-items:center; gap:16px; background:linear-gradient(90deg,var(--green-mid),var(--green-dark)); padding:14px 28px; position:sticky; top:0; z-index:20; }
  .topbar .hamburger{ width:20px; height:16px; display:flex; flex-direction:column; justify-content:space-between; cursor:pointer; }
  .topbar .hamburger span{ display:block; height:2px; background:#eaf0ea; border-radius:2px; }
  .search-box{ position:relative; flex:1; max-width:420px; display:flex; align-items:center; gap:8px; background:rgba(255,255,255,0.92); border-radius:8px; padding:9px 14px; }
  .search-box svg{ width:15px; height:15px; color:#8a9690; }
  .search-box input{ border:none; outline:none; background:transparent; font-size:13.5px; width:100%; }

  .search-results{ position:absolute; top:calc(100% + 8px); left:0; right:0; background:var(--card-bg); border:1px solid var(--border); border-radius:10px; box-shadow:0 10px 26px rgba(20,30,20,0.14); max-height:280px; overflow-y:auto; z-index:50; display:none; }
  .search-results.visible{ display:block; }
  .search-result-item{ display:flex; align-items:center; justify-content:space-between; gap:10px; padding:10px 14px; font-size:13px; color:var(--text-dark); cursor:pointer; }
  .search-result-item:hover{ background:#f3f6f3; }
  .search-result-item.disabled{ color:var(--text-light); cursor:not-allowed; }
  .search-result-item.disabled:hover{ background:transparent; }
  .search-result-item .tag{ font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; color:var(--status-maintenance); background:var(--status-maintenance-bg); padding:2px 7px; border-radius:20px; flex-shrink:0; }
  .search-empty{ padding:14px; font-size:12.5px; color:var(--text-light); text-align:center; }

  .topbar-right{ margin-left:auto; }
  .topbar-icon{ width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.9); color:var(--green-dark); display:flex; align-items:center; justify-content:center; cursor:pointer; }
  .topbar-icon svg{ width:16px; height:16px; }

  .content{ padding:28px 32px 48px 32px; flex:1; }
  .page-head{ display:flex; align-items:center; gap:12px; margin-bottom:22px; }
  .back-arrow{ width:34px; height:34px; border-radius:8px; background:var(--card-bg); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-mid); }
  .page-head h1{ font-size:22px; font-weight:700; margin:0; color:var(--green-accent); }
  .page-head .spacer{ flex:1; }

  .btn{ font-size:12.5px; font-weight:600; padding:10px 18px; border-radius:7px; border:1px solid var(--border); background:#fff; color:var(--text-mid); cursor:pointer; font-family:var(--font-body); }
  .btn:hover:not(:disabled){ background:#f7f9f7; }
  .btn:disabled{ opacity:.5; cursor:not-allowed; }
  .btn.primary{ background:var(--green-btn); border-color:var(--green-btn); color:#fff; }
  .btn.primary:hover:not(:disabled){ background:var(--green-btn-hover); }
  .btn.warn{ background:#fbeceb; border-color:#f2cfcc; color:var(--status-occupied); }
  .btn.warn:hover:not(:disabled){ background:#f6d9d7; }
  .btn.sm{ padding:7px 13px; font-size:11.5px; }

  .stats-row{ display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:16px; margin-bottom:24px; }
  .stat-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:18px 20px; display:flex; align-items:center; gap:14px; }
  .stat-icon{ width:46px; height:46px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .stat-icon svg{ width:22px; height:22px; }
  .stat-icon.green{ background:var(--status-vacant-bg); color:var(--green-accent); }
  .stat-icon.blue{ background:var(--blue-bg); color:var(--blue); }
  .stat-icon.purple{ background:var(--purple-bg); color:var(--purple); }
  .stat-icon.orange{ background:var(--status-maintenance-bg); color:var(--status-maintenance); }
  .stat-label{ font-size:12.5px; color:var(--text-mid); }
  .stat-value{ font-size:24px; font-weight:700; color:var(--text-dark); line-height:1.2; }
  .stat-sub{ font-size:11px; color:var(--status-occupied); font-weight:700; margin-top:2px; }

  .tabs-row{ display:flex; gap:28px; border-bottom:1px solid var(--border); margin-bottom:18px; }
  .tab-item{ padding:10px 2px 14px; font-size:14px; font-weight:600; color:var(--text-light); cursor:pointer; border-bottom:3px solid transparent; margin-bottom:-1px; }
  .tab-item.active{ color:var(--green-accent); border-bottom-color:var(--green-accent); }

  .filters-row{ display:flex; gap:12px; margin-bottom:18px; flex-wrap:wrap; }
  .filters-row .search-input{ flex:1; min-width:220px; border:1px solid var(--border); border-radius:8px; padding:11px 14px; font-size:13px; font-family:var(--font-body); }

  .table-panel{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; overflow:hidden; }
  table{ width:100%; border-collapse:collapse; }
  thead th{ text-align:left; font-size:12px; font-weight:700; color:var(--text-mid); padding:14px 20px; border-bottom:1px solid var(--border); background:#fbfcfb; }
  tbody td{ padding:14px 20px; font-size:13px; border-bottom:1px solid #f0f2f0; vertical-align:middle; }
  tbody tr:last-child td{ border-bottom:none; }
  tbody tr:hover{ background:#fbfcfb; }
  .tenant-cell{ display:flex; align-items:center; gap:10px; }
  .avatar{ width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; color:#fff; flex-shrink:0; }
  .tenant-name{ font-weight:600; }
  .status-text{ font-weight:700; font-size:12.5px; }
  .status-text.active{ color:var(--green-accent); }
  .status-text.expiring_soon{ color:var(--status-occupied); }
  .status-text.expired{ color:var(--text-light); }
  .status-text.terminated{ color:var(--text-light); }
  .status-text.pending{ color:var(--status-maintenance); }
  .eye-btn{ width:36px; height:36px; border-radius:8px; border:1px solid var(--border); background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-mid); }
  .eye-btn:hover{ background:#f7f9f7; }
  .eye-btn svg{ width:16px; height:16px; }
  .empty-row td{ text-align:center; color:var(--text-light); font-style:italic; padding:34px; }

  .pagination-row{ display:flex; align-items:center; gap:8px; padding:16px 20px; }
  .pagination-row .info{ font-size:12.5px; color:var(--text-light); margin-right:auto; }
  .page-btn{ width:34px; height:34px; border-radius:8px; border:1px solid var(--border); background:#fff; color:var(--text-mid); font-size:12.5px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; }
  .page-btn.active{ background:var(--green-btn); border-color:var(--green-btn); color:#fff; }
  .page-btn:disabled{ opacity:.4; cursor:not-allowed; }

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
  .kv{ display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:12px 20px; }
  .kv .k{ font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-light); }
  .kv .v{ font-size:13px; font-weight:500; margin-top:3px; word-break:break-word; }
  .kv .v.empty-v{ color:#c2c9c5; font-style:italic; font-weight:400; }

  .signed-note{ background:var(--status-vacant-bg); border:1px solid var(--status-vacant); border-radius:10px; padding:14px 16px; font-size:12.5px; color:var(--green-accent); line-height:1.6; }
  .signed-note a{ color:var(--green-accent); font-weight:700; }
  .sign-box{ border:1px solid var(--border); border-radius:10px; padding:16px 18px; background:#fbfcfb; }
  .sign-box .fld{ display:flex; flex-direction:column; gap:6px; margin-bottom:14px; }
  .sign-box label{ font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-mid); }
  .sign-box input[type=file],.sign-box input[type=date]{ border:1px solid var(--border); border-radius:8px; padding:9px 12px; font-size:13px; font-family:var(--font-body); background:#fff; }
  .confirm-row{ display:flex; gap:9px; align-items:flex-start; font-size:12px; color:var(--text-mid); line-height:1.6; margin-bottom:14px; }
  .confirm-row input{ margin-top:2px; accent-color:var(--green-accent); width:15px; height:15px; flex-shrink:0; }

  .action-box{ display:none; border:1px solid var(--border); border-radius:10px; padding:16px; background:#fbfcfb; margin-top:12px; }
  .action-box.open{ display:block; }
  .action-box .fld{ display:flex; flex-direction:column; gap:6px; margin-bottom:12px; }
  .action-box label{ font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-mid); }
  .action-box input,.action-box textarea{ border:1px solid var(--border); border-radius:8px; padding:10px 12px; font-size:13px; font-family:var(--font-body); width:100%; }
  .action-box textarea{ min-height:80px; resize:vertical; }
  .action-actions{ display:flex; gap:8px; }

  .outcome-note{ font-size:12.5px; line-height:1.6; padding:12px 14px; border-radius:8px; background:#fbfcfb; border:1px solid var(--border); }

  .modal-overlay{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:60; align-items:center; justify-content:center; padding:20px; }
  .modal-overlay.open{ display:flex; }
  .modal-box{ background:#fff; border-radius:14px; width:100%; max-width:560px; max-height:88vh; overflow-y:auto; }
  .modal-head{ padding:20px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; }
  .modal-head h2{ font-size:16px; font-weight:700; margin:0; }
  .modal-body{ padding:22px 24px; }
  .modal-body .fld{ display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
  .modal-body label{ font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-mid); }
  .modal-body input,.modal-body select{ border:1px solid var(--border); border-radius:8px; padding:10px 13px; font-size:13px; font-family:var(--font-body); width:100%; }
  .modal-row2{ display:grid; grid-template-columns:1fr 1fr; gap:14px; }
  .tenant-results{ border:1px solid var(--border); border-radius:8px; margin-top:6px; max-height:160px; overflow-y:auto; display:none; }
  .tenant-results.open{ display:block; }
  .tenant-result-item{ padding:10px 13px; font-size:13px; cursor:pointer; border-bottom:1px solid #f0f2f0; }
  .tenant-result-item:hover{ background:#f7f9f7; }
  .tenant-result-item:last-child{ border-bottom:none; }
  .selected-tenant{ background:var(--status-vacant-bg); border:1px solid var(--status-vacant); border-radius:8px; padding:10px 13px; font-size:13px; color:var(--green-accent); font-weight:600; display:none; align-items:center; gap:10px; margin-top:6px; }
  .selected-tenant.open{ display:flex; }
  .selected-tenant button{ margin-left:auto; background:none; border:none; color:var(--status-occupied); font-size:11.5px; font-weight:600; cursor:pointer; }
  .modal-actions{ display:flex; gap:10px; padding:18px 24px; border-top:1px solid var(--border); }

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
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h18v10H3z"/><path d="M3 12h18"/></svg></span><span class="label">Tickets</span></li>
      <li class="nav-item" data-href="{{ route('applications.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/></svg></span><span class="label">Applications</span></li>
      <li class="nav-item" data-href="{{ route('inquiries.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></span><span class="label">Inquiries</span></li>
      <li class="nav-item" data-href="{{ route('vr.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 3v18M16 3v18"/></svg></span><span class="label">VR Management</span></li>
      <li class="nav-item active" data-href="{{ route('contracts.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg></span><span class="label">Lease Management</span></li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/></svg></span><span class="label">Admin Privileges</span></li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M6 10h4M6 14h2"/></svg></span><span class="label">Dormitory Profile</span></li>
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
        <h1>Lease Management</h1>
        <div class="spacer"></div>
        <button class="btn primary" id="openAddModalBtn">+ Add Lease Contract</button>
      </div>

      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
          <div><div class="stat-label">Active Contracts</div><div class="stat-value">{{ $stats['active'] }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></div>
          <div><div class="stat-label">Total Tenants</div><div class="stat-value">{{ $stats['total_tenants'] }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
          <div><div class="stat-label">Expired Contracts</div><div class="stat-value">{{ $stats['expired'] }}</div>@if($stats['expired'] > 0)<div class="stat-sub">Requires Action</div>@endif</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon orange"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 11-3-6.7"/><path d="M21 3v6h-6"/></svg></div>
          <div><div class="stat-label">Expiring Soon</div><div class="stat-value">{{ $stats['expiring_soon'] }}</div></div>
        </div>
      </div>

      <div class="tabs-row" id="tabsRow">
        <div class="tab-item active" data-tab="all">All Contracts</div>
        <div class="tab-item" data-tab="active">Active</div>
        <div class="tab-item" data-tab="expiring_soon">Expiring Soon</div>
        <div class="tab-item" data-tab="expired">Expired</div>
        <div class="tab-item" data-tab="terminated">Terminated</div>
      </div>

      <div class="filters-row">
        <input type="text" class="search-input" id="searchInput" placeholder="Search Tenants or Rooms...">
      </div>

      <div class="table-panel">
        <table>
          <thead>
            <tr>
              <th>Tenant</th>
              <th>Room</th>
              <th>Start Date</th>
              <th>End Date</th>
              <th>Remaining Days</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="tableBody"></tbody>
        </table>
        <div class="pagination-row" id="paginationRow"></div>
      </div>

    </div>
  </div>
</div>

<div class="modal-overlay" id="addModal">
  <div class="modal-box">
    <div class="modal-head"><h2>Add Lease Contract</h2></div>
    <div class="modal-body">
      <div class="fld">
        <label for="tenantSearchInput">Tenant</label>
        <input type="text" id="tenantSearchInput" placeholder="Search registered tenants by name...">
        <div class="tenant-results" id="tenantResults"></div>
        <div class="selected-tenant" id="selectedTenant">
          <span id="selectedTenantName"></span>
          <button type="button" id="clearTenantBtn">Change</button>
        </div>
      </div>

      <div class="fld">
        <label for="roomSelect">Room</label>
        <select id="roomSelect"><option value="">Loading rooms…</option></select>
      </div>
      <div class="fld">
        <label for="bedSelect">Bedspace</label>
        <select id="bedSelect"><option value="">Select a room first</option></select>
      </div>

      <div class="modal-row2">
        <div class="fld">
          <label for="startDateInput">Lease Start Date</label>
          <input type="date" id="startDateInput">
        </div>
        <div class="fld">
          <label for="endDateInput">Lease End Date</label>
          <input type="date" id="endDateInput">
        </div>
      </div>

      <div class="fld">
        <label for="signedDocInput">Signed Contract Document (optional)</label>
        <input type="file" id="signedDocInput" accept=".pdf,.jpg,.jpeg,.png">
      </div>

      <label class="confirm-row" id="conflictConfirmRow" style="display:none;">
        <input type="checkbox" id="conflictConfirmCheckbox">
        <span>This tenant already has an active lease. Confirm you want to create another one anyway.</span>
      </label>
    </div>
    <div class="modal-actions">
      <button class="btn primary" id="submitAddBtn" style="flex:1;">Submit</button>
      <button class="btn" id="cancelAddBtn">Cancel</button>
    </div>
  </div>
</div>

<div class="overlay" id="overlay"></div>

<div class="drawer" id="drawer">
  <div class="drawer-head">
    <h2 id="drawerTitle">Contract</h2>
    <button class="drawer-close" id="drawerClose">&times;</button>
  </div>
  <div class="drawer-body" id="drawerBody"></div>
</div>

<div class="toast" id="toast"></div>

<script type="application/json" id="contracts-data">{!! json_encode($contracts) !!}</script>

<script>
(function(){
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  let contracts = JSON.parse(document.getElementById('contracts-data').textContent);

  let tab = 'all';
  let search = '';
  let page = 1;
  const PAGE_SIZE = 7;

  const $ = id => document.getElementById(id);
  const STATUS_LABEL = { pending:'Pending', active:'Active', expiring_soon:'Expiring soon', expired:'Expired', terminated:'Terminated' };
  const AVATAR_COLORS = ['#a78bda','#f0a877','#8fd19e','#f091b2','#8bb8e8','#f0c96a','#f0a3a3'];

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

  function peso(n){
    const num = parseFloat(n);
    return isNaN(num) ? '—' : '₱' + num.toLocaleString('en-PH', { minimumFractionDigits:2 });
  }

  function initials(name){
    return (name || '?').trim().split(/\s+/).map(w => w[0]).slice(0,2).join('').toUpperCase();
  }

  function avatarColor(id){
    return AVATAR_COLORS[id % AVATAR_COLORS.length];
  }

  async function api(url, options = {}){
    const headers = Object.assign({ 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' }, options.headers || {});
    const res = await fetch(url, Object.assign({}, options, { headers }));
    const body = await res.json().catch(() => ({}));
    if(!res.ok){
      const err = new Error(body.message || (body.errors ? Object.values(body.errors)[0][0] : `Request failed (${res.status})`));
      err.status = res.status;
      err.body = body;
      throw err;
    }
    return body;
  }

  function visible(){
    return contracts.filter(c => {
      if(tab !== 'all' && c.status !== tab) return false;
      if(!search) return true;
      const hay = `${c.tenant_name ?? ''} ${c.room_no ?? ''}`.toLowerCase();
      return hay.includes(search);
    });
  }

  function remainingDaysLabel(c){
    if(c.remaining_days === null || c.remaining_days === undefined) return '—';
    if(c.status === 'terminated') return '—';
    if(c.remaining_days < 0) return Math.abs(c.remaining_days) + ' days ago';
    return c.remaining_days;
  }

  function renderTable(){
    const all = visible();
    const totalPages = Math.max(1, Math.ceil(all.length / PAGE_SIZE));
    if(page > totalPages) page = totalPages;
    const start = (page - 1) * PAGE_SIZE;
    const pageItems = all.slice(start, start + PAGE_SIZE);

    if(pageItems.length === 0){
      $('tableBody').innerHTML = '<tr class="empty-row"><td colspan="7">No records found.</td></tr>';
    } else {
      $('tableBody').innerHTML = pageItems.map(c => `
        <tr>
          <td>
            <div class="tenant-cell">
              <div class="avatar" style="background:${avatarColor(c.id)}">${esc(initials(c.tenant_name))}</div>
              <span class="tenant-name">${esc(c.tenant_name)}</span>
            </div>
          </td>
          <td>${esc(c.room_no ?? '—')}</td>
          <td>${esc(c.start_date ?? '—')}</td>
          <td>${esc(c.end_date ?? '—')}</td>
          <td>${remainingDaysLabel(c)}</td>
          <td><span class="status-text ${c.status}">${STATUS_LABEL[c.status] ?? c.status}</span></td>
          <td>
            <button class="eye-btn" data-open="${c.id}" title="View details">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </td>
        </tr>`).join('');
    }

    $('tableBody').querySelectorAll('[data-open]').forEach(btn => {
      btn.addEventListener('click', () => openDrawer(Number(btn.dataset.open)));
    });

    renderPagination(all.length, totalPages);
  }

  function renderPagination(total, totalPages){
    const shownStart = total === 0 ? 0 : (page - 1) * PAGE_SIZE + 1;
    const shownEnd = Math.min(page * PAGE_SIZE, total);

    let pageBtns = '';
    for(let p = 1; p <= totalPages; p++){
      pageBtns += `<button class="page-btn ${p === page ? 'active' : ''}" data-page="${p}">${p}</button>`;
    }

    $('paginationRow').innerHTML = `
      <span class="info">Showing ${shownStart} to ${shownEnd} of ${total} results</span>
      <button class="page-btn" id="prevPageBtn" ${page <= 1 ? 'disabled' : ''}>&lsaquo;</button>
      ${pageBtns}
      <button class="page-btn" id="nextPageBtn" ${page >= totalPages ? 'disabled' : ''}>&rsaquo;</button>
    `;

    $('paginationRow').querySelectorAll('[data-page]').forEach(btn => {
      btn.addEventListener('click', () => { page = Number(btn.dataset.page); renderTable(); });
    });
    const prevBtn = $('prevPageBtn');
    const nextBtn = $('nextPageBtn');
    if(prevBtn) prevBtn.addEventListener('click', () => { page--; renderTable(); });
    if(nextBtn) nextBtn.addEventListener('click', () => { page++; renderTable(); });
  }

  $('tabsRow').querySelectorAll('[data-tab]').forEach(t => {
    t.addEventListener('click', () => {
      tab = t.dataset.tab;
      page = 1;
      $('tabsRow').querySelectorAll('[data-tab]').forEach(x => x.classList.toggle('active', x === t));
      renderTable();
    });
  });

  $('searchInput').addEventListener('input', function(){
    search = this.value.trim().toLowerCase();
    page = 1;
    renderTable();
  });

  function signSectionHtml(c){
    if(c.esign_status === 'signed'){
      return `<div class="signed-note"><strong>Signed${c.signed_at ? ' on ' + esc(c.signed_at) : ''}.</strong><br>${c.signed_document_url ? `<a href="${c.signed_document_url}" target="_blank" rel="noopener">View the signed document</a>` : 'No file attached.'}</div>`;
    }
    if(c.esign_status === 'not_applicable'){
      return `<div class="signed-note"><strong>Marked as not requiring a signature.</strong></div>`;
    }
    return `
      <div class="sign-box">
        <div class="fld"><label for="signedFile">Scan of the signed contract</label><input type="file" id="signedFile" accept=".pdf,.jpg,.jpeg,.png"></div>
        <div class="fld"><label for="signedDate">Date signed</label><input type="date" id="signedDate"></div>
        <label class="confirm-row"><input type="checkbox" id="confirmSign"><span>I confirm the tenant has physically signed this contract.</span></label>
        <button class="btn primary" id="submitSignBtn" style="width:100%;">Record Signed Contract</button>
      </div>`;
  }

  function outcomeHtml(c){
    if(c.status === 'terminated' && c.termination_reason){
      return `<div class="sec"><h3>Termination Reason</h3><div class="outcome-note" style="color:var(--status-occupied);">${esc(c.termination_reason)}${c.terminated_at ? '<br><span style="color:var(--text-light);">' + esc(c.terminated_at) + '</span>' : ''}</div></div>`;
    }
    if(c.last_renewed_at){
      return `<div class="sec"><h3>Renewal History</h3><div class="outcome-note" style="color:var(--green-accent);">Last renewed ${esc(c.last_renewed_at)}.</div></div>`;
    }
    return '';
  }

  function openDrawer(id){
    const c = contracts.find(x => x.id === id);
    if(!c) return;

    $('drawerTitle').textContent = `Contract #${c.id} — ${c.tenant_name}`;

    const canAct = ['pending','active','expiring_soon'].includes(c.status);
    const canRenew = ['active','expiring_soon','expired'].includes(c.status);

    $('drawerBody').innerHTML = `
      <div class="sec">
        <h3>Lease Terms</h3>
        <div class="kv">
          <div><div class="k">Tenant</div>${val(c.tenant_name)}</div>
          <div><div class="k">Room / Bed</div><span class="v">Room ${esc(c.room_no ?? '—')} · ${esc(c.bed_label ?? '—')}</span></div>
          <div><div class="k">Monthly rate</div><span class="v">${peso(c.monthly_rate)}</span></div>
          <div><div class="k">Discount</div>${c.discount_amount ? val(peso(c.discount_amount)) : val(null)}</div>
          <div><div class="k">Start date</div>${val(c.start_date)}</div>
          <div><div class="k">End date</div>${val(c.end_date)}</div>
          <div><div class="k">Status</div><span class="v status-text ${c.status}">${STATUS_LABEL[c.status] ?? c.status}</span></div>
        </div>
      </div>

      <div class="sec">
        <h3>Signature</h3>
        ${signSectionHtml(c)}
      </div>

      ${outcomeHtml(c)}

      ${canAct ? `
      <div class="sec">
        <h3>Actions</h3>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
          ${c.esign_status === 'pending' ? `<button class="btn sm" id="markNaBtn">Mark not required</button>` : ''}
          ${canRenew ? `<button class="btn sm primary" id="showRenewBtn">Renew Lease</button>` : ''}
          <button class="btn sm warn" id="showTerminateBtn">Terminate Lease</button>
        </div>

        <div class="action-box" id="renewBox">
          <div class="fld"><label for="renewEndDate">New End Date</label><input type="date" id="renewEndDate"></div>
          <div class="action-actions">
            <button class="btn primary" id="confirmRenewBtn">Confirm Renewal</button>
            <button class="btn" id="cancelRenewBtn">Cancel</button>
          </div>
        </div>

        <div class="action-box" id="terminateBox">
          <div class="fld"><label for="terminateReason">Termination Reason</label><textarea id="terminateReason" placeholder="Explain why this lease is being terminated..."></textarea></div>
          <div class="action-actions">
            <button class="btn warn" id="confirmTerminateBtn">Confirm Termination</button>
            <button class="btn" id="cancelTerminateBtn">Cancel</button>
          </div>
        </div>
      </div>` : ''}
    `;

    wireDrawer(c);
    $('overlay').classList.add('open');
    $('drawer').classList.add('open');
  }

  function wireDrawer(c){
    const signBtn = $('submitSignBtn');
    if(signBtn){
      signBtn.addEventListener('click', async function(){
        if(!$('confirmSign').checked) return toast('Tick the confirmation box first.', true);
        const file = $('signedFile').files[0];
        if(!file) return toast('Attach the scan of the signed contract.', true);

        const form = new FormData();
        form.append('signed_document', file);
        if($('signedDate').value) form.append('signed_at', $('signedDate').value);

        this.disabled = true;
        try {
          const res = await api(`/lease-contracts/${c.id}/sign`, { method:'POST', body: form });
          applyUpdate(c.id, { esign_status:'signed', status:'active', signed_at: res.signed_at || 'just now', signed_document_url: res.signed_document_url ?? null });
          toast('Signed contract recorded. Lease is now active.');
          closeDrawer();
        } catch(e){ toast(e.message, true); this.disabled = false; }
      });
    }

    const naBtn = $('markNaBtn');
    if(naBtn){
      naBtn.addEventListener('click', async function(){
        if(!confirm('Mark this contract as not requiring a signature?')) return;
        try {
          await api(`/lease-contracts/${c.id}/not-applicable`, { method:'PATCH' });
          applyUpdate(c.id, { esign_status:'not_applicable', status:'active' });
          toast('Contract marked as not requiring a signature.');
          closeDrawer();
        } catch(e){ toast(e.message, true); }
      });
    }

    const showRenewBtn = $('showRenewBtn');
    if(showRenewBtn){
      showRenewBtn.addEventListener('click', () => { $('renewBox').classList.add('open'); $('terminateBox').classList.remove('open'); });
      $('cancelRenewBtn').addEventListener('click', () => $('renewBox').classList.remove('open'));
      $('confirmRenewBtn').addEventListener('click', async function(){
        const newEnd = $('renewEndDate').value;
        if(!newEnd) return toast('Pick a new end date.', true);
        this.disabled = true;
        try {
          const result = await api(`/lease-contracts/${c.id}/renew`, {
            method:'PATCH', headers:{ 'Content-Type':'application/json' }, body: JSON.stringify({ end_date:newEnd }),
          });
          applyUpdate(c.id, result.contract);
          toast('Lease renewed successfully.');
          closeDrawer();
        } catch(e){ toast(e.message, true); this.disabled = false; }
      });
    }

    $('showTerminateBtn').addEventListener('click', () => { $('terminateBox').classList.add('open'); const rb = $('renewBox'); if(rb) rb.classList.remove('open'); });
    $('cancelTerminateBtn').addEventListener('click', () => $('terminateBox').classList.remove('open'));
    $('confirmTerminateBtn').addEventListener('click', async function(){
      const reason = $('terminateReason').value.trim();
      if(!reason) return toast('A termination reason is required.', true);
      if(!confirm('Terminate this lease? The bedspace will be released back to vacant.')) return;
      this.disabled = true;
      try {
        const result = await api(`/lease-contracts/${c.id}/terminate`, {
          method:'PATCH', headers:{ 'Content-Type':'application/json' }, body: JSON.stringify({ reason }),
        });
        applyUpdate(c.id, result.contract);
        toast('Lease terminated successfully.');
        closeDrawer();
      } catch(e){ toast(e.message, true); this.disabled = false; }
    });
  }

  function applyUpdate(id, changes){
    const idx = contracts.findIndex(c => c.id === id);
    if(idx !== -1) contracts[idx] = Object.assign({}, contracts[idx], changes);
    renderTable();
  }

  function closeDrawer(){
    $('overlay').classList.remove('open');
    $('drawer').classList.remove('open');
  }
  $('drawerClose').addEventListener('click', closeDrawer);
  $('overlay').addEventListener('click', closeDrawer);

  let selectedTenantId = null;
  let tenantSearchTimer = null;

  $('openAddModalBtn').addEventListener('click', () => {
    resetAddModal();
    loadRooms();
    $('addModal').classList.add('open');
  });
  $('cancelAddBtn').addEventListener('click', () => $('addModal').classList.remove('open'));

  function resetAddModal(){
    selectedTenantId = null;
    $('tenantSearchInput').value = '';
    $('tenantResults').classList.remove('open');
    $('selectedTenant').classList.remove('open');
    $('roomSelect').innerHTML = '<option value="">Loading rooms…</option>';
    $('bedSelect').innerHTML = '<option value="">Select a room first</option>';
    $('startDateInput').value = '';
    $('endDateInput').value = '';
    $('signedDocInput').value = '';
    $('conflictConfirmRow').style.display = 'none';
    $('conflictConfirmCheckbox').checked = false;
  }

  $('tenantSearchInput').addEventListener('input', function(){
    clearTimeout(tenantSearchTimer);
    const q = this.value.trim();
    tenantSearchTimer = setTimeout(async () => {
      try {
        const tenants = await api(`/lease-contracts/tenants/search?q=${encodeURIComponent(q)}`);
        if(tenants.length === 0){
          $('tenantResults').innerHTML = '<div class="tenant-result-item" style="color:var(--text-light);font-style:italic;">No matching tenants found.</div>';
        } else {
          $('tenantResults').innerHTML = tenants.map(t => `<div class="tenant-result-item" data-id="${t.id}" data-name="${esc(t.full_name)}">${esc(t.full_name)} <span style="color:var(--text-light);">· ${esc(t.email || t.contact_number || '')}</span></div>`).join('');
          $('tenantResults').querySelectorAll('[data-id]').forEach(item => {
            item.addEventListener('click', () => {
              selectedTenantId = Number(item.dataset.id);
              $('selectedTenantName').textContent = item.dataset.name;
              $('selectedTenant').classList.add('open');
              $('tenantResults').classList.remove('open');
              $('tenantSearchInput').value = '';
            });
          });
        }
        $('tenantResults').classList.add('open');
      } catch(e){ /* silent */ }
    }, 250);
  });

  $('clearTenantBtn').addEventListener('click', () => {
    selectedTenantId = null;
    $('selectedTenant').classList.remove('open');
  });

  function loadRooms(){
    fetch('/public-api/rooms')
      .then(r => r.json())
      .then(rooms => {
        $('roomSelect').innerHTML = '<option value="">Select a room</option>' +
          rooms.map(r => `<option value="${r.id}">${esc(r.room_no)}</option>`).join('');
      })
      .catch(() => { $('roomSelect').innerHTML = '<option value="">Could not load rooms</option>'; });
  }

  $('roomSelect').addEventListener('change', function(){
    $('bedSelect').innerHTML = '<option value="">Loading beds…</option>';
    if(!this.value){ $('bedSelect').innerHTML = '<option value="">Select a room first</option>'; return; }
    fetch(`/public-api/rooms/${this.value}/beds`)
      .then(r => r.json())
      .then(beds => {
        $('bedSelect').innerHTML = beds.length
          ? '<option value="">Select a bed</option>' + beds.map(b => `<option value="${b.id}">${esc(b.bed_label)}</option>`).join('')
          : '<option value="">No vacant beds in this room</option>';
      })
      .catch(() => { $('bedSelect').innerHTML = '<option value="">Could not load beds</option>'; });
  });

  $('submitAddBtn').addEventListener('click', async function(){
    if(!selectedTenantId) return toast('Select a tenant first.', true);
    if(!$('bedSelect').value) return toast('Select a bedspace.', true);
    if(!$('startDateInput').value || !$('endDateInput').value) return toast('Set both the start and end dates.', true);

    const form = new FormData();
    form.append('tenant_id', selectedTenantId);
    form.append('bed_id', $('bedSelect').value);
    form.append('start_date', $('startDateInput').value);
    form.append('end_date', $('endDateInput').value);
    if($('conflictConfirmCheckbox').checked) form.append('confirm_existing_active_lease', '1');
    const file = $('signedDocInput').files[0];
    if(file) form.append('signed_document', file);

    this.disabled = true;
    try {
      const result = await api('/lease-contracts', { method:'POST', body: form });
      contracts.unshift(result.contract);
      renderTable();
      $('addModal').classList.remove('open');
      toast('Lease contract added successfully.');
    } catch(e){
      if(e.status === 409 && e.body && e.body.requires_confirmation){
        $('conflictConfirmRow').style.display = 'flex';
        toast(e.message, true);
      } else {
        toast(e.message, true);
      }
    }
    this.disabled = false;
  });

  renderTable();
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

  // Simple client-side search over the site's pages. Pages without a route
  // yet show up but are marked "Coming Soon" and aren't clickable, matching
  // how they behave in the sidebar itself.
  const NEST_PAGES = [
    { label: 'Dashboard', href: '{{ route('dashboard') }}' },
    { label: 'Tenant Manager', href: '{{ route('tenant-manager.index') }}' },
    { label: 'Billing and Payments', href: '{{ route('payments.index') }}' },
    { label: 'Delinquency', href: null },
    { label: 'Vacancy Monitor', href: '{{ route('admin.addfloor') }}' },
    { label: 'Tickets', href: null },
    { label: 'Applications', href: '{{ route('applications.index') }}' },
    { label: 'Inquiries', href: '{{ route('inquiries.index') }}' },
    { label: 'VR Management', href: '{{ route('vr.index') }}' },
    { label: 'Lease Management', href: '{{ route('contracts.index') }}' },
    { label: 'Admin Privileges', href: null },
    { label: 'Dormitory Profile', href: null },
  ];

  const searchBox = document.getElementById('searchBox');
  const searchInput = document.getElementById('pageSearchInput');
  const searchResults = document.getElementById('searchResults');

  function nestEscapeHtml(str){
    const d = document.createElement('div');
    d.textContent = str ?? '';
    return d.innerHTML;
  }

  function renderNestSearchResults(query){
    const q = query.trim().toLowerCase();
    if (!q) {
      searchResults.classList.remove('visible');
      searchResults.innerHTML = '';
      return;
    }
    const matches = NEST_PAGES.filter(p => p.label.toLowerCase().includes(q));
    searchResults.innerHTML = matches.length === 0
      ? `<div class="search-empty">No pages match "${nestEscapeHtml(query)}"</div>`
      : matches.map(p => p.href
          ? `<div class="search-result-item" data-href="${p.href}">${nestEscapeHtml(p.label)}</div>`
          : `<div class="search-result-item disabled">${nestEscapeHtml(p.label)}<span class="tag">Coming Soon</span></div>`
        ).join('');
    searchResults.classList.add('visible');
  }

  if (searchInput && searchResults && searchBox) {
    searchInput.addEventListener('input', () => renderNestSearchResults(searchInput.value));
    searchInput.addEventListener('focus', () => {
      if (searchInput.value.trim()) renderNestSearchResults(searchInput.value);
    });
    searchInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        const first = searchResults.querySelector('.search-result-item[data-href]');
        if (first) window.location.href = first.dataset.href;
      } else if (e.key === 'Escape') {
        searchResults.classList.remove('visible');
        searchInput.blur();
      }
    });
    searchResults.addEventListener('click', (e) => {
      const item = e.target.closest('.search-result-item[data-href]');
      if (item) window.location.href = item.dataset.href;
    });
    document.addEventListener('click', (e) => {
      if (!searchBox.contains(e.target)) {
        searchResults.classList.remove('visible');
      }
    });
  }
})();
</script>

</body>
</html>