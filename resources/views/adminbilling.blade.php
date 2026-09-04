<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH — Billing and Payments</title>
<style>
  :root{
    --green-dark:#3f6b4a; --green-mid:#4f7c57;
    --green-sidebar-top:#5b8a63; --green-sidebar-bottom:#2c4a35;
    --green-accent:#2f6f3c; --green-btn:#2f6b3a; --green-btn-hover:#255a2f;
    --status-occupied:#d9564f; --status-vacant:#7fc98a; --status-vacant-bg:#d9f2dd;
    --status-maintenance:#c9962f; --status-maintenance-bg:#f6ecd6;
    --purple:#7a4fc9; --purple-bg:#e9defa;
    --blue:#33629e; --blue-bg:#e3ecf7;
    --orange:#c9962f; --orange-bg:#f6ecd6;
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

  .topbar-right{ margin-left:auto; display:flex; align-items:center; gap:18px; }
  .topbar-icon{ width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.9); color:var(--green-dark); display:flex; align-items:center; justify-content:center; cursor:pointer; }
  .topbar-icon svg{ width:16px; height:16px; }

  .content{ padding:24px 28px 40px 28px; flex:1; }
  .page-head{ display:flex; align-items:center; gap:12px; margin-bottom:20px; }
  .back-arrow{ width:34px; height:34px; border-radius:8px; background:var(--card-bg); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-mid); }
  .page-head h1{ font-size:20px; font-weight:700; margin:0; color:var(--green-accent); }

  .stats-row{ display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:14px; margin-bottom:22px; }
  .stat-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:16px 18px; display:flex; align-items:center; gap:12px; }
  .stat-icon{ width:40px; height:40px; border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .stat-icon svg{ width:19px; height:19px; }
  .stat-icon.green{ background:var(--status-vacant-bg); color:var(--green-accent); }
  .stat-icon.blue{ background:var(--blue-bg); color:var(--blue); }
  .stat-icon.purple{ background:var(--purple-bg); color:var(--purple); }
  .stat-icon.orange{ background:var(--orange-bg); color:var(--orange); }
  .stat-label{ font-size:12.5px; color:var(--text-mid); }
  .stat-value{ font-size:22px; font-weight:700; color:var(--text-dark); line-height:1.2; }
  .stat-sub{ font-size:11px; color:var(--status-occupied); font-weight:700; margin-top:2px; }
  .stat-sub.neutral{ color:var(--text-light); }

  .tabs-row{ display:flex; align-items:center; justify-content:space-between; gap:16px; border-bottom:1px solid var(--border); margin-bottom:18px; }
  .tabs-left{ display:flex; gap:28px; }
  .tab-item{ padding:10px 2px 14px; font-size:14px; font-weight:600; color:var(--text-light); cursor:pointer; border-bottom:3px solid transparent; margin-bottom:-1px; }
  .tab-item.active{ color:var(--green-accent); border-bottom-color:var(--green-accent); }

  .filters-row{ display:flex; gap:12px; margin-bottom:18px; flex-wrap:wrap; align-items:center; }
  .filters-row .search-input{ flex:1; min-width:220px; border:1px solid var(--border); border-radius:8px; padding:11px 14px; font-size:13px; font-family:var(--font-body); }
  .filters-row select{ border:1px solid var(--border); border-radius:8px; padding:11px 14px; font-size:13px; font-family:var(--font-body); background:#fff; color:var(--text-dark); }

  .btn{ font-size:12.5px; font-weight:600; padding:10px 18px; border-radius:7px; border:1px solid var(--border); background:#fff; color:var(--text-mid); cursor:pointer; font-family:var(--font-body); }
  .btn:hover:not(:disabled){ background:#f7f9f7; }
  .btn:disabled{ opacity:.5; cursor:not-allowed; }
  .btn.primary{ background:var(--green-btn); border-color:var(--green-btn); color:#fff; }
  .btn.primary:hover:not(:disabled){ background:var(--green-btn-hover); }
  .btn.warn{ background:#fbeceb; border-color:#f2cfcc; color:var(--status-occupied); }
  .btn.warn:hover:not(:disabled){ background:#f6d9d7; }
  .btn.sm{ padding:7px 13px; font-size:11.5px; }

  .table-panel{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; overflow-x:auto; }
  table{ width:100%; border-collapse:collapse; min-width:980px; }
  thead th{ text-align:left; font-size:11px; font-weight:700; color:var(--text-mid); padding:12px 10px; border-bottom:1px solid var(--border); background:#fbfcfb; white-space:nowrap; }
  tbody td{ padding:11px 10px; font-size:12px; border-bottom:1px solid #f0f2f0; vertical-align:middle; white-space:nowrap; }
  tbody tr:last-child td{ border-bottom:none; }
  tbody tr:hover{ background:#fbfcfb; }
  .tenant-cell{ display:flex; align-items:center; gap:8px; white-space:normal; }
  .avatar{ width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:11px; color:#fff; flex-shrink:0; }
  .tenant-name{ font-weight:600; font-size:12.5px; }
  .move-in-badge{ display:inline-block; font-size:8.5px; font-weight:700; text-transform:uppercase; letter-spacing:.3px; background:var(--purple-bg); color:var(--purple); padding:2px 7px; border-radius:20px; margin-top:2px; white-space:nowrap; }
  .method-text{ font-weight:600; }
  .method-text.gcash{ color:var(--blue); }
  .method-text.bank_transfer{ color:var(--green-accent); }
  .method-text.other{ color:var(--text-mid); }
  .proof-thumb{ width:36px; height:36px; border-radius:6px; object-fit:cover; border:1px solid var(--border); cursor:pointer; }
  .proof-thumb.pdf{ display:flex; align-items:center; justify-content:center; background:#fbfcfb; font-size:9px; font-weight:700; color:var(--text-light); }
  .action-cell{ display:flex; gap:5px; flex-wrap:nowrap; }
  .action-cell .btn{ padding:6px 10px; font-size:11px; white-space:nowrap; }
  .eye-btn{ width:32px; height:32px; border-radius:7px; border:1px solid var(--border); background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-mid); flex-shrink:0; }
  .eye-btn:hover{ background:#f7f9f7; border-color:var(--green-accent); color:var(--green-accent); }
  .eye-btn svg{ width:15px; height:15px; flex-shrink:0; }
  .empty-row td{ text-align:center; color:var(--text-light); font-style:italic; padding:34px; white-space:normal; }

  .coming-soon{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:60px 20px; text-align:center; color:var(--text-light); }
  .coming-soon svg{ width:40px; height:40px; margin-bottom:14px; color:var(--text-light); }
  .coming-soon p{ font-size:13px; max-width:420px; margin:0 auto; line-height:1.6; }

  .status-pill{ font-weight:700; font-size:11.5px; }
  .status-pill.paid{ color:var(--green-accent); }
  .status-pill.overdue{ color:var(--status-occupied); }
  .status-pill.unpaid{ color:var(--orange); }
  .status-pill.partial{ color:var(--blue); }
  .status-pill.pending{ color:var(--orange); }
  .due-overdue-note{ display:block; font-size:10px; color:var(--status-occupied); font-weight:700; margin-top:2px; }

  .pagination-row{ display:flex; align-items:center; gap:8px; padding:16px 16px; }
  .pagination-row .info{ font-size:12px; color:var(--text-light); margin-right:auto; }
  .page-btn{ width:32px; height:32px; border-radius:8px; border:1px solid var(--border); background:#fff; color:var(--text-mid); font-size:12px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; }
  .page-btn.active{ background:var(--green-btn); border-color:var(--green-btn); color:#fff; }
  .page-btn:disabled{ opacity:.4; cursor:not-allowed; }

  .stmt-breakdown{ background:#fbfcfb; border:1px solid var(--border); border-radius:8px; padding:12px 14px; font-size:12px; }
  .stmt-breakdown-row{ display:flex; justify-content:space-between; padding:5px 0; }
  .stmt-breakdown-row.total{ border-top:1px solid var(--border); margin-top:5px; padding-top:9px; font-weight:700; }
  .stmt-history-row{ display:flex; align-items:center; gap:10px; padding:9px 0; border-bottom:1px solid #f0f2f0; font-size:12px; }
  .stmt-history-row:last-child{ border-bottom:none; }
  .stmt-history-row .amt{ font-weight:700; margin-left:auto; }
  .stmt-history-empty{ color:var(--text-light); font-style:italic; font-size:12px; padding:10px 0; }

  /* Record Cash Payment / Record Damage / Add Penalty / Waive modals */
  .modal-overlay{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:60; align-items:center; justify-content:center; padding:20px; }
  .modal-overlay.open{ display:flex; }
  .modal-box{ background:#fff; border-radius:14px; width:100%; max-width:520px; max-height:88vh; overflow-y:auto; }
  .modal-head{ padding:20px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; }
  .modal-head h2{ font-size:16px; font-weight:700; margin:0; }
  .modal-body{ padding:22px 24px; }
  .modal-body .fld{ display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
  .modal-body label{ font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-mid); }
  .modal-body input, .modal-body select, .modal-body textarea{ border:1px solid var(--border); border-radius:8px; padding:10px 13px; font-size:13px; font-family:var(--font-body); width:100%; }
  .modal-body textarea{ min-height:80px; resize:vertical; }
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
  .rp-balance-note{ background:var(--status-vacant-bg); border:1px solid var(--status-vacant); border-radius:8px; padding:10px 13px; font-size:12.5px; color:var(--green-accent); font-weight:600; }

  .overlay{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:50; }
  .overlay.open{ display:block; }
  .drawer{ position:fixed; top:0; right:0; height:100%; width:min(520px,100%); background:#fff; z-index:51; transform:translateX(100%); transition:transform .25s ease; overflow-y:auto; display:none; }
  .drawer.open{ transform:translateX(0); display:block; }
  .drawer-head{ position:sticky; top:0; background:#fff; border-bottom:1px solid var(--border); padding:18px 24px; display:flex; align-items:center; gap:12px; z-index:2; }
  .drawer-head h2{ font-size:16px; font-weight:700; margin:0; }
  .drawer-close{ margin-left:auto; background:none; border:none; font-size:22px; color:var(--text-light); cursor:pointer; line-height:1; }
  .drawer-body{ padding:22px 24px 40px 24px; }

  .sec{ margin-bottom:24px; }
  .sec h3{ font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--green-accent); margin:0 0 12px 0; padding-bottom:7px; border-bottom:2px solid #e2ede3; }
  .kv{ display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:12px 20px; }
  .kv .k{ font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-light); }
  .kv .v{ font-size:13px; font-weight:500; margin-top:3px; word-break:break-word; }
  .kv .v.empty-v{ color:#c2c9c5; font-style:italic; font-weight:400; }

  .proof-preview{ width:100%; border-radius:10px; border:1px solid var(--border); display:block; }
  .proof-preview-link{ display:inline-block; font-size:12px; color:var(--green-accent); font-weight:700; margin-top:8px; }

  .action-box{ display:none; border:1px solid var(--border); border-radius:10px; padding:16px; background:#fbfcfb; margin-top:12px; }
  .action-box.open{ display:block; }
  .action-box textarea{ width:100%; border:1px solid var(--border); border-radius:8px; padding:10px 12px; font-size:13px; font-family:var(--font-body); min-height:80px; resize:vertical; margin-bottom:12px; }
  .action-actions{ display:flex; gap:8px; }

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
      <li class="nav-item active" data-href="{{ route('payments.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></span><span class="label">Billing and Payments</span></li>
      <li class="nav-item" data-href="{{ route('delinquency.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg></span><span class="label">Delinquency</span></li>
      <li class="nav-item" data-href="{{ route('admin.addfloor') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="4" width="7" height="7"/><rect x="3" y="15" width="7" height="7"/><rect x="14" y="15" width="7" height="7"/></svg></span><span class="label">Vacancy Monitor</span></li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h18v10H3z"/><path d="M3 12h18"/></svg></span><span class="label">Tickets</span></li>
      <li class="nav-item" data-href="{{ route('applications.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/></svg></span><span class="label">Applications</span></li>
      <li class="nav-item" data-href="{{ route('inquiries.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></span><span class="label">Inquiries</span></li>
      <li class="nav-item" data-href="{{ route('vr.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 3v18M16 3v18"/></svg></span><span class="label">VR Management</span></li>
      <li class="nav-item" data-href="{{ route('contracts.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg></span><span class="label">Lease Management</span></li>
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
        <h1>Billing and Payments</h1>
      </div>

      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
          <div><div class="stat-label">Total Outstanding</div><div class="stat-value">₱{{ number_format($stats['total_outstanding'], 2) }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></div>
          <div><div class="stat-label">Paid This Month</div><div class="stat-value">₱{{ number_format($stats['paid_this_month'], 2) }}</div><div class="stat-sub neutral">{{ now()->format('F Y') }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
          <div><div class="stat-label">Overdue Accounts</div><div class="stat-value">{{ $stats['overdue_accounts'] }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon orange"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 11-3-6.7"/><path d="M21 3v6h-6"/></svg></div>
          <div><div class="stat-label">Pending Payments</div><div class="stat-value">{{ $stats['pending_count'] }}</div></div>
        </div>
      </div>

      <div class="tabs-row" id="tabsRow">
        <div class="tabs-left">
          <div class="tab-item active" data-tab="overview">Billing Overview</div>
          <div class="tab-item" data-tab="pending">Pending Payment</div>
          <div class="tab-item" data-tab="penalties">Penalties</div>
        </div>
        <div style="display:flex;gap:10px;">
          <button class="btn primary" id="openRecordPaymentBtn">+ Record Payment Entry</button>
          <button class="btn primary" id="openRecordDamageBtn">+ Record Damage</button>
          <button class="btn primary" id="openAddPenaltyBtn">+ Add Penalty</button>
        </div>
      </div>

      <div id="overviewTab" data-tab-content="overview">
        <div class="filters-row">
          <input type="text" class="search-input" id="overviewSearchInput" placeholder="Search Tenants or Rooms...">
          <select id="overviewRoomTypeFilter">
            <option value="">All Room Types</option>
          </select>
          <select id="overviewStatusFilter">
            <option value="">All Status</option>
            <option value="unpaid">Unpaid</option>
            <option value="partial">Partial</option>
            <option value="paid">Paid</option>
            <option value="overdue">Overdue</option>
          </select>
          <button class="btn primary" id="generateBillingBtn">+ Generate This Month's Billing</button>
        </div>

        <div class="table-panel">
          <table>
            <thead>
              <tr>
                <th>Tenant</th>
                <th>Room</th>
                <th>Billing Month</th>
                <th>Due Date</th>
                <th>Total Amount</th>
                <th>Paid Amount</th>
                <th>Balance</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="overviewTableBody"></tbody>
          </table>
          <div class="pagination-row" id="overviewPaginationRow"></div>
        </div>
      </div>

      <div id="pendingTab" data-tab-content="pending" style="display:none;">
        <div class="filters-row">
          <input type="text" class="search-input" id="searchInput" placeholder="Search Tenants or Rooms...">
          <select id="methodFilter">
            <option value="">All Payment Methods</option>
            <option value="gcash">GCash</option>
            <option value="bank_transfer">Bank Transfer</option>
            <option value="other">Other</option>
          </select>
        </div>

        <div class="table-panel">
          <table>
            <thead>
              <tr>
                <th>Tenant</th>
                <th>Room</th>
                <th>Billing Month</th>
                <th>Date Paid</th>
                <th>Payment Method</th>
                <th>Paid Amount</th>
                <th>OR#/Reference</th>
                <th>Proof</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="tableBody"></tbody>
          </table>
        </div>
      </div>

      <div id="penaltiesTab" data-tab-content="penalties" style="display:none;">
        <div class="filters-row">
          <input type="text" class="search-input" id="penaltySearchInput" placeholder="Search Tenants...">
          <select id="penaltyTypeFilter">
            <option value="">All Types</option>
            <option value="damage">Damage</option>
            <option value="manual">Manual</option>
          </select>
          <select id="penaltyStatusFilter">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="waived">Waived</option>
          </select>
        </div>

        <div class="table-panel">
          <table>
            <thead>
              <tr>
                <th>Tenant</th>
                <th>Room</th>
                <th>Type</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="penaltiesTableBody"></tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="overlay" id="overlay"></div>

<div class="drawer" id="drawer">
  <div class="drawer-head">
    <h2 id="drawerTitle">Payment</h2>
    <button class="drawer-close" id="drawerClose">&times;</button>
  </div>
  <div class="drawer-body" id="drawerBody"></div>
</div>

<div class="modal-overlay" id="recordPaymentModal">
  <div class="modal-box">
    <div class="modal-head"><h2>Record Cash Payment</h2></div>
    <div class="modal-body">
      <div class="fld">
        <label for="rpTenantSearchInput">Tenant</label>
        <input type="text" id="rpTenantSearchInput" placeholder="Search registered tenants by name...">
        <div class="tenant-results" id="rpTenantResults"></div>
        <div class="selected-tenant" id="rpSelectedTenant">
          <span id="rpSelectedTenantName"></span>
          <button type="button" id="rpClearTenantBtn">Change</button>
        </div>
      </div>

      <div class="fld">
        <label for="rpStatementSelect">Billing Statement</label>
        <select id="rpStatementSelect"><option value="">Select a tenant first</option></select>
      </div>

      <div class="fld" id="rpBalanceNote" style="display:none;"></div>

      <div class="modal-row2">
        <div class="fld">
          <label for="rpAmountInput">Amount Paid</label>
          <input type="number" id="rpAmountInput" step="0.01" min="0.01" placeholder="0.00">
        </div>
        <div class="fld">
          <label for="rpDateInput">Date Received</label>
          <input type="date" id="rpDateInput">
        </div>
      </div>

      <div class="fld">
        <label for="rpReferenceInput">Reference Number (optional)</label>
        <input type="text" id="rpReferenceInput" placeholder="OR#, receipt number, etc.">
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn primary" id="rpSubmitBtn" style="flex:1;">Record Payment</button>
      <button class="btn" id="rpCancelBtn">Cancel</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="recordDamageModal">
  <div class="modal-box">
    <div class="modal-head"><h2>Record Damage</h2></div>
    <div class="modal-body">
      <div class="fld">
        <label for="rdTenantSearchInput">Tenant</label>
        <input type="text" id="rdTenantSearchInput" placeholder="Search registered tenants by name...">
        <div class="tenant-results" id="rdTenantResults"></div>
        <div class="selected-tenant" id="rdSelectedTenant">
          <span id="rdSelectedTenantName"></span>
          <button type="button" id="rdClearTenantBtn">Change</button>
        </div>
      </div>

      <div class="fld">
        <label>Room / Bed</label>
        <div id="rdRoomBedDisplay" style="padding:9px 11px;border:1px solid var(--border);border-radius:8px;font-size:12.5px;color:var(--text-mid);background:#f7f9f7;">Select a tenant first</div>
      </div>

      <div class="fld">
        <label for="rdDescriptionInput">Description</label>
        <textarea id="rdDescriptionInput" placeholder="Describe the damage..."></textarea>
      </div>

      <div class="modal-row2">
        <div class="fld">
          <label for="rdCostInput">Cost</label>
          <input type="number" id="rdCostInput" step="0.01" min="0.01" placeholder="0.00">
        </div>
        <div class="fld">
          <label for="rdDateInput">Date Incurred</label>
          <input type="date" id="rdDateInput">
        </div>
      </div>

      <div class="fld">
        <label for="rdPhotoInput">Photo (optional)</label>
        <input type="file" id="rdPhotoInput" accept=".jpg,.jpeg,.png">
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn primary" id="rdSubmitBtn" style="flex:1;">Record Damage</button>
      <button class="btn" id="rdCancelBtn">Cancel</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="addPenaltyModal">
  <div class="modal-box">
    <div class="modal-head"><h2>Add Penalty</h2></div>
    <div class="modal-body">
      <div class="fld">
        <label for="apTenantSearchInput">Tenant</label>
        <input type="text" id="apTenantSearchInput" placeholder="Search registered tenants by name...">
        <div class="tenant-results" id="apTenantResults"></div>
        <div class="selected-tenant" id="apSelectedTenant">
          <span id="apSelectedTenantName"></span>
          <button type="button" id="apClearTenantBtn">Change</button>
        </div>
      </div>

      <div class="fld">
        <label for="apTypeSelect">Type</label>
        <select id="apTypeSelect">
          <option value="manual">Manual</option>
          <option value="other">Other</option>
        </select>
      </div>

      <div class="fld">
        <label for="apDescriptionInput">Reason</label>
        <input type="text" id="apDescriptionInput" placeholder="e.g. Late curfew violation">
      </div>

      <div class="modal-row2">
        <div class="fld">
          <label for="apAmountInput">Amount</label>
          <input type="number" id="apAmountInput" step="0.01" min="0.01" placeholder="0.00">
        </div>
        <div class="fld">
          <label for="apDateInput">Date</label>
          <input type="date" id="apDateInput">
        </div>
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn primary" id="apSubmitBtn" style="flex:1;">Add Penalty</button>
      <button class="btn" id="apCancelBtn">Cancel</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="waivePenaltyModal">
  <div class="modal-box">
    <div class="modal-head"><h2>Waive Penalty</h2></div>
    <div class="modal-body">
      <p style="font-size:12.5px;color:var(--text-mid);margin:0 0 14px 0;">This penalty will be marked as waived. It stays on record and can be reinstated later if needed.</p>
      <div class="fld">
        <label for="waiveReasonInput">Reason</label>
        <textarea id="waiveReasonInput" placeholder="Explain why this penalty is being waived..."></textarea>
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn warn" id="waiveSubmitBtn" style="flex:1;">Confirm Waive</button>
      <button class="btn" id="waiveCancelBtn">Cancel</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script type="application/json" id="pending-data">{!! json_encode($pending) !!}</script>
<script type="application/json" id="overview-data">{!! json_encode($overview) !!}</script>

<script>
(function(){
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  let pending = JSON.parse(document.getElementById('pending-data').textContent);

  let search = '';
  let methodFilter = '';

  const $ = id => document.getElementById(id);
  const AVATAR_COLORS = ['#f0a877','#f091b2','#a78bda','#f0c96a','#8bb8e8','#8fd19e','#f0a3a3'];
  const METHOD_LABEL = { gcash:'GCash', bank_transfer:'BDO', other:'Other' };

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

  function isImage(url){
    return url && /\.(jpe?g|png|gif|webp)$/i.test(url);
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

  // ===== Tabs =====
  // Single source of truth: every tab-content div carries data-tab-content
  // matching its tab-item's data-tab. Looping over all of them and hiding
  // everything except the match is more defensive than separate hardcoded
  // getElementById() calls per tab -- a single missing/misnamed wrapper div
  // can no longer leave a tab's content stuck visible underneath another.
  const tabContents = document.querySelectorAll('[data-tab-content]');
  $('tabsRow').querySelectorAll('[data-tab]').forEach(t => {
    t.addEventListener('click', () => {
      $('tabsRow').querySelectorAll('[data-tab]').forEach(x => x.classList.toggle('active', x === t));
      tabContents.forEach(el => {
        el.style.display = el.dataset.tabContent === t.dataset.tab ? 'block' : 'none';
      });
      if(t.dataset.tab === 'overview') renderOverviewTable();
      if(t.dataset.tab === 'penalties') loadPenalties();
    });
  });

  // ===== List =====
  function visible(){
    return pending.filter(p => {
      if(methodFilter && p.payment_method !== methodFilter) return false;
      if(!search) return true;
      const hay = `${p.tenant_name ?? ''} ${p.room_no ?? ''}`.toLowerCase();
      return hay.includes(search);
    });
  }

  function renderTable(){
    const list = visible();

    if(list.length === 0){
      $('tableBody').innerHTML = '<tr class="empty-row"><td colspan="9">No pending payments right now.</td></tr>';
      return;
    }

    $('tableBody').innerHTML = list.map(p => `
      <tr>
        <td>
          <div class="tenant-cell">
            <div class="avatar" style="background:${avatarColor(p.id)}">${esc(initials(p.tenant_name))}</div>
            <div>
              <div class="tenant-name">${esc(p.tenant_name)}</div>
              ${p.billing_type === 'move_in' ? '<div class="move-in-badge">Move-In Fee</div>' : ''}
            </div>
          </div>
        </td>
        <td>${esc(p.room_no ?? '—')}</td>
        <td>${esc(p.billing_month ?? '—')}</td>
        <td>${esc(p.date_paid ?? '—')}</td>
        <td><span class="method-text ${p.payment_method}">${METHOD_LABEL[p.payment_method] ?? p.payment_method}</span></td>
        <td>${peso(p.amount_paid)}</td>
        <td>${esc(p.reference_number ?? 'N/A')}</td>
        <td>
          ${p.proof_url
            ? (isImage(p.proof_url)
                ? `<img class="proof-thumb" src="${p.proof_url}" data-open="${p.id}" alt="">`
                : `<div class="proof-thumb pdf" data-open="${p.id}">PDF</div>`)
            : '—'}
        </td>
        <td>
          <div class="action-cell">
            <button class="btn sm" data-open="${p.id}">View</button>
            <button class="btn sm primary" data-quick-approve="${p.id}">Approve</button>
            <button class="btn sm warn" data-quick-reject="${p.id}">Reject</button>
          </div>
        </td>
      </tr>`).join('');

    $('tableBody').querySelectorAll('[data-open]').forEach(el => {
      el.addEventListener('click', () => openDrawer(Number(el.dataset.open)));
    });
    $('tableBody').querySelectorAll('[data-quick-approve]').forEach(btn => {
      btn.addEventListener('click', () => approvePayment(Number(btn.dataset.quickApprove)));
    });
    $('tableBody').querySelectorAll('[data-quick-reject]').forEach(btn => {
      btn.addEventListener('click', () => openDrawer(Number(btn.dataset.quickReject), true));
    });
  }

  $('searchInput').addEventListener('input', function(){
    search = this.value.trim().toLowerCase();
    renderTable();
  });
  $('methodFilter').addEventListener('change', function(){
    methodFilter = this.value;
    renderTable();
  });

  // ===== Drawer =====
  function openDrawer(id, openRejectBox){
    const p = pending.find(x => x.id === id);
    if(!p) return;

    $('drawerTitle').textContent = `Payment from ${p.tenant_name}`;

    const proofHtml = p.proof_url
      ? (isImage(p.proof_url)
          ? `<img class="proof-preview" src="${p.proof_url}" alt="">`
          : `<a class="proof-preview-link" href="${p.proof_url}" target="_blank" rel="noopener">Open PDF proof of payment →</a>`)
      : '<span class="v empty-v">No proof attached</span>';

    $('drawerBody').innerHTML = `
      <div class="sec">
        <h3>Payment Details</h3>
        <div class="kv">
          <div><div class="k">Tenant</div>${val(p.tenant_name)}</div>
          <div><div class="k">Room</div>${val(p.room_no)}</div>
          <div><div class="k">Billing Month</div>${val(p.billing_month)}</div>
          <div><div class="k">Type</div>${val(p.billing_type === 'move_in' ? 'Move-In Fee' : 'Monthly Rent')}</div>
          <div><div class="k">Date Paid</div>${val(p.date_paid)}</div>
          <div><div class="k">Payment Method</div>${val(METHOD_LABEL[p.payment_method] ?? p.payment_method)}</div>
          <div><div class="k">Amount Paid</div><span class="v">${peso(p.amount_paid)}</span></div>
          <div><div class="k">Reference</div>${val(p.reference_number)}</div>
        </div>
      </div>

      <div class="sec">
        <h3>Notes</h3>
        <div class="kv"><div>${val(p.notes)}</div></div>
      </div>

      <div class="sec">
        <h3>Proof of Payment</h3>
        ${proofHtml}
      </div>

      <div class="sec">
        <h3>Decision</h3>
        <div style="display:flex;gap:8px;">
          <button class="btn primary" id="drawerApproveBtn" style="flex:1;">Approve</button>
          <button class="btn warn" id="drawerShowRejectBtn" style="flex:1;">Reject</button>
        </div>

        <div class="action-box" id="rejectBox">
          <textarea id="rejectReason" placeholder="Explain why this payment proof is being rejected..."></textarea>
          <div class="action-actions">
            <button class="btn warn" id="confirmRejectBtn">Confirm Rejection</button>
            <button class="btn" id="cancelRejectBtn">Cancel</button>
          </div>
        </div>
      </div>
    `;

    $('drawerApproveBtn').addEventListener('click', () => approvePayment(p.id));
    $('drawerShowRejectBtn').addEventListener('click', () => $('rejectBox').classList.add('open'));
    $('cancelRejectBtn').addEventListener('click', () => $('rejectBox').classList.remove('open'));
    $('confirmRejectBtn').addEventListener('click', () => rejectPayment(p.id));

    $('overlay').classList.add('open');
    $('drawer').classList.add('open');

    if(openRejectBox) $('rejectBox').classList.add('open');
  }

  function closeDrawer(){
    $('overlay').classList.remove('open');
    $('drawer').classList.remove('open');
  }
  $('drawerClose').addEventListener('click', closeDrawer);
  $('overlay').addEventListener('click', closeDrawer);

  // ===== Actions =====
  async function approvePayment(id){
    if(!confirm('Approve this payment? If it fully settles a move-in fee, the tenant will be activated and the bed marked occupied.')) return;

    try {
      const result = await api(`/payments/${id}/approve`, { method:'POST' });
      pending = pending.filter(p => p.id !== id);
      renderTable();
      closeDrawer();
      toast(result.message || 'Payment approved.');
    } catch(e){ toast(e.message, true); }
  }

  async function rejectPayment(id){
    const reason = $('rejectReason').value.trim();
    if(!reason) return toast('A rejection reason is required.', true);

    try {
      const result = await api(`/payments/${id}/reject`, {
        method:'POST',
        headers:{ 'Content-Type':'application/json' },
        body: JSON.stringify({ review_notes: reason }),
      });
      pending = pending.filter(p => p.id !== id);
      renderTable();
      closeDrawer();
      toast(result.message || 'Payment rejected.');
    } catch(e){ toast(e.message, true); }
  }

  renderTable();

  // ===== Billing Overview tab =====
  let overview = JSON.parse(document.getElementById('overview-data').textContent);
  let overviewSearch = '';
  let overviewRoomType = '';
  let overviewStatus = '';
  let overviewPage = 1;
  const OVERVIEW_PAGE_SIZE = 7;
  const STATUS_LABEL = { unpaid:'Unpaid', partial:'Partial', paid:'Paid', overdue:'Overdue' };

  // Populate room type filter from actual data present
  const roomTypes = [...new Set(overview.map(o => o.room_type).filter(Boolean))];
  const roomTypeSelect = $('overviewRoomTypeFilter');
  roomTypes.forEach(rt => {
    const opt = document.createElement('option');
    opt.value = rt;
    opt.textContent = rt.charAt(0).toUpperCase() + rt.slice(1);
    roomTypeSelect.appendChild(opt);
  });

  function overviewVisible(){
    return overview.filter(o => {
      if(overviewStatus && o.status !== overviewStatus) return false;
      if(overviewRoomType && o.room_type !== overviewRoomType) return false;
      if(!overviewSearch) return true;
      const hay = `${o.tenant_name ?? ''} ${o.room_no ?? ''}`.toLowerCase();
      return hay.includes(overviewSearch);
    });
  }

  function renderOverviewTable(){
    const all = overviewVisible();
    const totalPages = Math.max(1, Math.ceil(all.length / OVERVIEW_PAGE_SIZE));
    if(overviewPage > totalPages) overviewPage = totalPages;
    const start = (overviewPage - 1) * OVERVIEW_PAGE_SIZE;
    const pageItems = all.slice(start, start + OVERVIEW_PAGE_SIZE);

    if(pageItems.length === 0){
      $('overviewTableBody').innerHTML = '<tr class="empty-row"><td colspan="9">No billing statements found.</td></tr>';
    } else {
      $('overviewTableBody').innerHTML = pageItems.map(o => `
        <tr>
          <td>
            <div class="tenant-cell">
              <div class="avatar" style="background:${avatarColor(o.id)}">${esc(initials(o.tenant_name))}</div>
              <span class="tenant-name">${esc(o.tenant_name)}</span>
            </div>
          </td>
          <td>${esc(o.room_no ?? '—')}</td>
          <td>${esc(o.billing_month ?? '—')}</td>
          <td>${esc(o.due_date ?? '—')}${o.days_overdue ? `<span class="due-overdue-note">Overdue (${o.days_overdue} days)</span>` : ''}</td>
          <td>${peso(o.total_amount)}</td>
          <td>${peso(o.amount_paid)}</td>
          <td>${peso(o.balance)}</td>
          <td><span class="status-pill ${o.status}">${STATUS_LABEL[o.status] ?? o.status}</span></td>
          <td><button class="eye-btn" data-view-stmt="${o.id}" title="View details"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg></button></td>
        </tr>`).join('');
    }

    $('overviewTableBody').querySelectorAll('[data-view-stmt]').forEach(btn => {
      btn.addEventListener('click', () => openStatementDrawer(Number(btn.dataset.viewStmt)));
    });

    renderOverviewPagination(all.length, totalPages);
  }

  function renderOverviewPagination(total, totalPages){
    const shownStart = total === 0 ? 0 : (overviewPage - 1) * OVERVIEW_PAGE_SIZE + 1;
    const shownEnd = Math.min(overviewPage * OVERVIEW_PAGE_SIZE, total);

    let pageBtns = '';
    for(let p = 1; p <= totalPages; p++){
      pageBtns += `<button class="page-btn ${p === overviewPage ? 'active' : ''}" data-ov-page="${p}">${p}</button>`;
    }

    $('overviewPaginationRow').innerHTML = `
      <span class="info">Showing ${shownStart} to ${shownEnd} of ${total} results</span>
      <button class="page-btn" id="ovPrevBtn" ${overviewPage <= 1 ? 'disabled' : ''}>&lsaquo;</button>
      ${pageBtns}
      <button class="page-btn" id="ovNextBtn" ${overviewPage >= totalPages ? 'disabled' : ''}>&rsaquo;</button>
    `;

    $('overviewPaginationRow').querySelectorAll('[data-ov-page]').forEach(btn => {
      btn.addEventListener('click', () => { overviewPage = Number(btn.dataset.ovPage); renderOverviewTable(); });
    });
    const prevBtn = $('ovPrevBtn');
    const nextBtn = $('ovNextBtn');
    if(prevBtn) prevBtn.addEventListener('click', () => { overviewPage--; renderOverviewTable(); });
    if(nextBtn) nextBtn.addEventListener('click', () => { overviewPage++; renderOverviewTable(); });
  }

  $('overviewSearchInput').addEventListener('input', function(){
    overviewSearch = this.value.trim().toLowerCase();
    overviewPage = 1;
    renderOverviewTable();
  });
  $('overviewRoomTypeFilter').addEventListener('change', function(){
    overviewRoomType = this.value;
    overviewPage = 1;
    renderOverviewTable();
  });
  $('overviewStatusFilter').addEventListener('change', function(){
    overviewStatus = this.value;
    overviewPage = 1;
    renderOverviewTable();
  });

  const PAYMENT_METHOD_LABEL = { gcash:'GCash', bank_transfer:'BDO', other:'Other' };

  /**
   * Use Case Report Table 22 (View Payment History): "select a specific
   * transaction to view details" -- scoped here to one statement's own
   * breakdown and payment history, rather than a separate cross-statement
   * transaction ledger.
   */
  function openStatementDrawer(id){
    const o = overview.find(x => x.id === id);
    if(!o) return;

    $('drawerTitle').textContent = `Statement — ${o.tenant_name}`;

    const historyHtml = o.payments.length === 0
      ? '<div class="stmt-history-empty">No payments recorded against this statement yet.</div>'
      : o.payments.map(p => `
          <div class="stmt-history-row">
            <span>${esc(p.date ?? '—')}</span>
            <span>${esc(PAYMENT_METHOD_LABEL[p.payment_method] ?? p.payment_method)}</span>
            <span class="status-pill ${p.status === 'approved' ? 'paid' : (p.status === 'rejected' ? 'overdue' : 'unpaid')}">${esc(p.status)}</span>
            <span class="amt">${peso(p.amount_paid)}</span>
          </div>`).join('');

    $('drawerBody').innerHTML = `
      <div class="sec">
        <h3>Statement Details</h3>
        <div class="kv">
          <div><div class="k">Tenant</div>${val(o.tenant_name)}</div>
          <div><div class="k">Room</div>${val(o.room_no)}</div>
          <div><div class="k">Billing Month</div>${val(o.billing_month)}</div>
          <div><div class="k">Due Date</div>${val(o.due_date)}</div>
          <div><div class="k">Status</div><span class="v status-pill ${o.status}">${STATUS_LABEL[o.status] ?? o.status}</span></div>
        </div>
      </div>

      <div class="sec">
        <h3>Amount Breakdown</h3>
        <div class="stmt-breakdown">
          <div class="stmt-breakdown-row"><span>Base Rent</span><span>${peso(o.base_rent)}</span></div>
          <div class="stmt-breakdown-row"><span>Utilities</span><span>${peso(o.utilities_amount)}</span></div>
          <div class="stmt-breakdown-row"><span>WiFi</span><span>${peso(o.wifi_amount)}</span></div>
          <div class="stmt-breakdown-row"><span>Penalties</span><span>${peso(o.penalty_amount)}</span></div>
          <div class="stmt-breakdown-row total"><span>Total</span><span>${peso(o.total_amount)}</span></div>
        </div>
      </div>

      <div class="sec">
        <h3>Payment History</h3>
        ${historyHtml}
      </div>

      <div class="sec">
        <h3>Balance</h3>
        <div class="kv">
          <div><div class="k">Paid so far</div><span class="v">${peso(o.amount_paid)}</span></div>
          <div><div class="k">Remaining balance</div><span class="v">${peso(o.balance)}</span></div>
        </div>
      </div>
    `;

    $('overlay').classList.add('open');
    $('drawer').classList.add('open');
  }

  renderOverviewTable();

  // ===== Generate Billing =====
  // Matches Use Case Report Table 19 ("Generate Billing Statement"). Runs
  // BillingController::generate() for every active contract that's due for
  // its next monthly statement -- safe to click repeatedly, contracts that
  // aren't due yet (or already have a current-period statement) are skipped
  // automatically rather than double-billed.
  $('generateBillingBtn').addEventListener('click', async function(){
    if(!confirm("Generate this month's billing statements for every active tenant who is due for one? Tenants who already have a current statement will be skipped automatically.")) return;

    this.disabled = true;
    try {
      const result = await api('/billing/generate', { method:'POST' });
      toast(result.message || 'Billing statements generated.');
      setTimeout(() => window.location.reload(), 900);
    } catch(e){
      toast(e.message, true);
      this.disabled = false;
    }
  });

  // ===== Record Cash Payment modal =====
  let rpSelectedTenantId = null;
  let rpTenantSearchTimer = null;

  $('openRecordPaymentBtn').addEventListener('click', () => {
    resetRecordPaymentModal();
    $('recordPaymentModal').classList.add('open');
  });
  $('rpCancelBtn').addEventListener('click', () => $('recordPaymentModal').classList.remove('open'));

  function resetRecordPaymentModal(){
    rpSelectedTenantId = null;
    $('rpTenantSearchInput').value = '';
    $('rpTenantResults').classList.remove('open');
    $('rpSelectedTenant').classList.remove('open');
    $('rpStatementSelect').innerHTML = '<option value="">Select a tenant first</option>';
    $('rpBalanceNote').style.display = 'none';
    $('rpAmountInput').value = '';
    $('rpDateInput').value = '';
    $('rpReferenceInput').value = '';
  }

  $('rpTenantSearchInput').addEventListener('input', function(){
    clearTimeout(rpTenantSearchTimer);
    const q = this.value.trim();
    rpTenantSearchTimer = setTimeout(async () => {
      try {
        const tenants = await api(`/lease-contracts/tenants/search?q=${encodeURIComponent(q)}`);
        if(tenants.length === 0){
          $('rpTenantResults').innerHTML = '<div class="tenant-result-item" style="color:var(--text-light);font-style:italic;">No matching tenants found.</div>';
        } else {
          $('rpTenantResults').innerHTML = tenants.map(t => `<div class="tenant-result-item" data-id="${t.id}" data-name="${esc(t.full_name)}">${esc(t.full_name)} <span style="color:var(--text-light);">\u00b7 ${esc(t.email || t.contact_number || '')}</span></div>`).join('');
          $('rpTenantResults').querySelectorAll('[data-id]').forEach(item => {
            item.addEventListener('click', () => {
              rpSelectedTenantId = Number(item.dataset.id);
              $('rpSelectedTenantName').textContent = item.dataset.name;
              $('rpSelectedTenant').classList.add('open');
              $('rpTenantResults').classList.remove('open');
              $('rpTenantSearchInput').value = '';
              loadOutstandingStatements(rpSelectedTenantId);
            });
          });
        }
        $('rpTenantResults').classList.add('open');
      } catch(e){ /* silent */ }
    }, 250);
  });

  $('rpClearTenantBtn').addEventListener('click', () => {
    rpSelectedTenantId = null;
    $('rpSelectedTenant').classList.remove('open');
    $('rpStatementSelect').innerHTML = '<option value="">Select a tenant first</option>';
    $('rpBalanceNote').style.display = 'none';
  });

  async function loadOutstandingStatements(tenantId){
    $('rpStatementSelect').innerHTML = '<option value="">Loading\u2026</option>';
    try {
      const statements = await api(`/billing/tenants/${tenantId}/statements`);
      if(statements.length === 0){
        $('rpStatementSelect').innerHTML = '<option value="">No outstanding statements for this tenant</option>';
        return;
      }
      $('rpStatementSelect').innerHTML = '<option value="">Select a statement</option>' +
        statements.map(s => `<option value="${s.id}" data-balance="${s.balance}">${esc(s.billing_month)} \u2014 Balance ${peso(s.balance)}</option>`).join('');
    } catch(e){
      $('rpStatementSelect').innerHTML = '<option value="">Could not load statements</option>';
    }
  }

  $('rpStatementSelect').addEventListener('change', function(){
    const opt = this.selectedOptions[0];
    const balance = opt ? opt.dataset.balance : null;
    if(balance){
      $('rpBalanceNote').style.display = 'block';
      $('rpBalanceNote').innerHTML = `<div class="rp-balance-note">Outstanding balance: ${peso(balance)}</div>`;
      $('rpAmountInput').value = balance;
    } else {
      $('rpBalanceNote').style.display = 'none';
    }
  });

  $('rpSubmitBtn').addEventListener('click', async function(){
    const statementId = $('rpStatementSelect').value;
    const amount = $('rpAmountInput').value;
    const date = $('rpDateInput').value;

    if(!rpSelectedTenantId) return toast('Select a tenant first.', true);
    if(!statementId) return toast('Select a billing statement.', true);
    if(!amount || Number(amount) <= 0) return toast('Enter a valid amount.', true);

    this.disabled = true;
    try {
      const result = await api(`/billing/${statementId}/payments/cash`, {
        method:'POST',
        headers:{ 'Content-Type':'application/json' },
        body: JSON.stringify({
          amount_paid: amount,
          payment_date: date || null,
          reference_number: $('rpReferenceInput').value || null,
        }),
      });

      $('recordPaymentModal').classList.remove('open');
      toast(result.message || 'Payment recorded successfully.');

      // Simplest correct way to reflect the change everywhere (stats,
      // overview balance, pending count) without duplicating the page's
      // computation logic client-side.
      setTimeout(() => window.location.reload(), 700);
    } catch(e){ toast(e.message, true); }
    this.disabled = false;
  });

  // ===== Penalties tab =====
  let penalties = [];
  let penaltySearch = '';
  let penaltyTypeFilter = '';
  let penaltyStatusFilter = '';
  const TYPE_LABEL = { damage:'Damage', manual:'Manual', other:'Other' };

  async function loadPenalties(){
    $('penaltiesTableBody').innerHTML = '<tr class="empty-row"><td colspan="8">Loading…</td></tr>';
    try {
      penalties = await api('/penalties');
      renderPenaltiesTable();
    } catch(e){
      $('penaltiesTableBody').innerHTML = `<tr class="empty-row"><td colspan="8">${esc(e.message)}</td></tr>`;
    }
  }

  function penaltiesVisible(){
    return penalties.filter(p => {
      if(penaltyTypeFilter && p.type !== penaltyTypeFilter) return false;
      if(penaltyStatusFilter && p.status !== penaltyStatusFilter) return false;
      if(!penaltySearch) return true;
      const hay = `${p.tenant?.full_name ?? ''}`.toLowerCase();
      return hay.includes(penaltySearch);
    });
  }

  function renderPenaltiesTable(){
    const list = penaltiesVisible();

    if(list.length === 0){
      $('penaltiesTableBody').innerHTML = '<tr class="empty-row"><td colspan="8">No penalties found.</td></tr>';
      return;
    }

    $('penaltiesTableBody').innerHTML = list.map(p => `
      <tr>
        <td>
          <div class="tenant-cell">
            <div class="avatar" style="background:${avatarColor(p.id)}">${esc(initials(p.tenant?.full_name))}</div>
            <span class="tenant-name">${esc(p.tenant?.full_name ?? '—')}</span>
          </div>
        </td>
        <td>${esc(p.room_no ?? '—')}</td>
        <td>${esc(TYPE_LABEL[p.type] ?? p.type)}</td>
        <td>${esc(p.description)}</td>
        <td>${peso(p.amount)}</td>
        <td>${p.date ? new Date(p.date).toLocaleDateString('en-PH', { year:'numeric', month:'short', day:'numeric' }) : '—'}</td>
        <td><span class="status-pill ${p.status === 'waived' ? 'paid' : 'pending'}">${p.status === 'waived' ? 'Waived' : 'Active'}</span></td>
        <td>
          <div class="action-cell">
            ${p.damage_photo_url ? `<a class="btn sm" href="${p.damage_photo_url}" target="_blank" rel="noopener">Photo</a>` : ''}
            ${p.status === 'active' ? `<button class="btn sm warn" data-waive="${p.id}">Waive</button>` : ''}
          </div>
        </td>
      </tr>
    `).join('');

    $('penaltiesTableBody').querySelectorAll('[data-waive]').forEach(btn => {
      btn.addEventListener('click', () => openWaiveModal(Number(btn.dataset.waive)));
    });
  }

  $('penaltySearchInput').addEventListener('input', function(){
    penaltySearch = this.value.trim().toLowerCase();
    renderPenaltiesTable();
  });
  $('penaltyTypeFilter').addEventListener('change', function(){
    penaltyTypeFilter = this.value;
    renderPenaltiesTable();
  });
  $('penaltyStatusFilter').addEventListener('change', function(){
    penaltyStatusFilter = this.value;
    renderPenaltiesTable();
  });

  // ===== Waive modal =====
  let waivingPenaltyId = null;

  function openWaiveModal(id){
    waivingPenaltyId = id;
    $('waiveReasonInput').value = '';
    $('waivePenaltyModal').classList.add('open');
  }
  $('waiveCancelBtn').addEventListener('click', () => $('waivePenaltyModal').classList.remove('open'));

  $('waiveSubmitBtn').addEventListener('click', async function(){
    const reason = $('waiveReasonInput').value.trim();
    if(!reason) return toast('A reason is required to waive a penalty.', true);

    this.disabled = true;
    try {
      await api(`/penalties/${waivingPenaltyId}/waive`, {
        method:'PATCH',
        headers:{ 'Content-Type':'application/json' },
        body: JSON.stringify({ reason }),
      });
      $('waivePenaltyModal').classList.remove('open');
      toast('Penalty waived.');
      loadPenalties();
    } catch(e){ toast(e.message, true); }
    this.disabled = false;
  });

  // ===== Record Damage modal =====
  let rdSelectedTenantId = null;
  let rdSelectedRoomId = null;
  let rdSelectedBedId = null;
  let rdTenantSearchTimer = null;

  $('openRecordDamageBtn').addEventListener('click', () => {
    resetRecordDamageModal();
    $('recordDamageModal').classList.add('open');
  });
  $('rdCancelBtn').addEventListener('click', () => $('recordDamageModal').classList.remove('open'));

  function resetRecordDamageModal(){
    rdSelectedTenantId = null;
    rdSelectedRoomId = null;
    rdSelectedBedId = null;
    $('rdTenantSearchInput').value = '';
    $('rdTenantResults').classList.remove('open');
    $('rdSelectedTenant').classList.remove('open');
    $('rdRoomBedDisplay').textContent = 'Select a tenant first';
    $('rdDescriptionInput').value = '';
    $('rdCostInput').value = '';
    $('rdDateInput').value = '';
    $('rdPhotoInput').value = '';
  }

  $('rdTenantSearchInput').addEventListener('input', function(){
    clearTimeout(rdTenantSearchTimer);
    const q = this.value.trim();
    rdTenantSearchTimer = setTimeout(async () => {
      try {
        const tenants = await api(`/lease-contracts/tenants/search?q=${encodeURIComponent(q)}`);
        if(tenants.length === 0){
          $('rdTenantResults').innerHTML = '<div class="tenant-result-item" style="color:var(--text-light);font-style:italic;">No matching tenants found.</div>';
        } else {
          $('rdTenantResults').innerHTML = tenants.map(t => `<div class="tenant-result-item" data-id="${t.id}" data-name="${esc(t.full_name)}">${esc(t.full_name)} <span style="color:var(--text-light);">\u00b7 ${esc(t.email || t.contact_number || '')}</span></div>`).join('');
          $('rdTenantResults').querySelectorAll('[data-id]').forEach(item => {
            item.addEventListener('click', async () => {
              rdSelectedTenantId = Number(item.dataset.id);
              $('rdSelectedTenantName').textContent = item.dataset.name;
              $('rdSelectedTenant').classList.add('open');
              $('rdTenantResults').classList.remove('open');
              $('rdTenantSearchInput').value = '';
              await loadTenantRoomBed(rdSelectedTenantId);
            });
          });
        }
        $('rdTenantResults').classList.add('open');
      } catch(e){ /* silent */ }
    }, 250);
  });

  $('rdClearTenantBtn').addEventListener('click', () => {
    rdSelectedTenantId = null;
    rdSelectedRoomId = null;
    rdSelectedBedId = null;
    $('rdSelectedTenant').classList.remove('open');
    $('rdRoomBedDisplay').textContent = 'Select a tenant first';
  });

  async function loadTenantRoomBed(tenantId){
    $('rdRoomBedDisplay').textContent = 'Loading…';
    try {
      const result = await api(`/tenants/${tenantId}/active-lease`);
      if(!result.room || !result.bed){
        rdSelectedRoomId = null;
        rdSelectedBedId = null;
        $('rdRoomBedDisplay').textContent = 'No active room/bed found for this tenant.';
        return;
      }
      rdSelectedRoomId = result.room.id;
      rdSelectedBedId = result.bed.id;
      $('rdRoomBedDisplay').textContent = `Room ${result.room.room_no} — ${result.bed.bed_label}`;
    } catch(e){
      rdSelectedRoomId = null;
      rdSelectedBedId = null;
      $('rdRoomBedDisplay').textContent = 'Could not load room/bed for this tenant.';
    }
  }

  $('rdSubmitBtn').addEventListener('click', async function(){
    if(!rdSelectedTenantId) return toast('Select a tenant first.', true);
    if(!$('rdDescriptionInput').value.trim()) return toast('Enter a description.', true);
    if(!$('rdCostInput').value || Number($('rdCostInput').value) <= 0) return toast('Enter a valid cost.', true);
    if(!$('rdDateInput').value) return toast('Pick the date the damage occurred.', true);

    const form = new FormData();
    form.append('tenant_id', rdSelectedTenantId);
    if(rdSelectedRoomId) form.append('room_id', rdSelectedRoomId);
    if(rdSelectedBedId) form.append('bed_id', rdSelectedBedId);
    form.append('description', $('rdDescriptionInput').value.trim());
    form.append('cost', $('rdCostInput').value);
    form.append('date_incurred', $('rdDateInput').value);
    const photo = $('rdPhotoInput').files[0];
    if(photo) form.append('photo', photo);

    this.disabled = true;
    try {
      await api('/damages', { method:'POST', body: form });
      $('recordDamageModal').classList.remove('open');
      toast('Damage recorded and penalty added.');
      loadPenalties();
    } catch(e){ toast(e.message, true); }
    this.disabled = false;
  });

  // ===== Add Penalty modal =====
  let apSelectedTenantId = null;
  let apTenantSearchTimer = null;

  $('openAddPenaltyBtn').addEventListener('click', () => {
    resetAddPenaltyModal();
    $('addPenaltyModal').classList.add('open');
  });
  $('apCancelBtn').addEventListener('click', () => $('addPenaltyModal').classList.remove('open'));

  function resetAddPenaltyModal(){
    apSelectedTenantId = null;
    $('apTenantSearchInput').value = '';
    $('apTenantResults').classList.remove('open');
    $('apSelectedTenant').classList.remove('open');
    $('apTypeSelect').value = 'manual';
    $('apDescriptionInput').value = '';
    $('apAmountInput').value = '';
    const today = new Date().toISOString().slice(0, 10);
    $('apDateInput').value = today;
    $('apDateInput').max = today;
  }

  $('apTenantSearchInput').addEventListener('input', function(){
    clearTimeout(apTenantSearchTimer);
    const q = this.value.trim();
    apTenantSearchTimer = setTimeout(async () => {
      try {
        const tenants = await api(`/lease-contracts/tenants/search?q=${encodeURIComponent(q)}`);
        if(tenants.length === 0){
          $('apTenantResults').innerHTML = '<div class="tenant-result-item" style="color:var(--text-light);font-style:italic;">No matching tenants found.</div>';
        } else {
          $('apTenantResults').innerHTML = tenants.map(t => `<div class="tenant-result-item" data-id="${t.id}" data-name="${esc(t.full_name)}">${esc(t.full_name)} <span style="color:var(--text-light);">\u00b7 ${esc(t.email || t.contact_number || '')}</span></div>`).join('');
          $('apTenantResults').querySelectorAll('[data-id]').forEach(item => {
            item.addEventListener('click', () => {
              apSelectedTenantId = Number(item.dataset.id);
              $('apSelectedTenantName').textContent = item.dataset.name;
              $('apSelectedTenant').classList.add('open');
              $('apTenantResults').classList.remove('open');
              $('apTenantSearchInput').value = '';
            });
          });
        }
        $('apTenantResults').classList.add('open');
      } catch(e){ /* silent */ }
    }, 250);
  });

  $('apClearTenantBtn').addEventListener('click', () => {
    apSelectedTenantId = null;
    $('apSelectedTenant').classList.remove('open');
  });

  $('apSubmitBtn').addEventListener('click', async function(){
    if(!apSelectedTenantId) return toast('Select a tenant first.', true);
    if(!$('apDescriptionInput').value.trim()) return toast('Enter a reason.', true);
    if(!$('apAmountInput').value || Number($('apAmountInput').value) <= 0) return toast('Enter a valid amount.', true);

    this.disabled = true;
    try {
      await api('/penalties', {
        method:'POST',
        headers:{ 'Content-Type':'application/json' },
        body: JSON.stringify({
          tenant_id: apSelectedTenantId,
          type: $('apTypeSelect').value,
          description: $('apDescriptionInput').value.trim(),
          amount: $('apAmountInput').value,
          date_incurred: $('apDateInput').value || null,
        }),
      });
      $('addPenaltyModal').classList.remove('open');
      toast('Penalty added.');
      loadPenalties();
    } catch(e){ toast(e.message, true); }
    this.disabled = false;
  });
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