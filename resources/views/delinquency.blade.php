<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH — Delinquency</title>
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
  .sidebar.collapsed .sidebar-footer{ padding-left:10px; padding-right:10px; }
  .sidebar.collapsed .sidebar-footer .nav-item{ padding:9px 0; }

  .main{ flex:1; display:flex; flex-direction:column; min-width:0; }
  .topbar{ display:flex; align-items:center; gap:16px; background:linear-gradient(90deg,var(--green-mid),var(--green-dark)); padding:14px 28px; position:sticky; top:0; z-index:20; }
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
  .page-head h1{ font-size:20px; font-weight:700; margin:0; color:var(--green-accent); }

  .stats-row{ display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:14px; margin-bottom:22px; }
  .stat-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:16px 18px; display:flex; align-items:center; gap:14px; }
  .stat-icon{ width:48px; height:48px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .stat-icon svg{ width:22px; height:22px; }
  .stat-icon.red{ background:#f7d9d7; color:#c0463d; }
  .stat-icon.orange{ background:#fdf5e9; color:#de6f13; }
  .stat-icon.green{ background:var(--status-vacant-bg); color:var(--green-accent); }
  .stat-icon.purple{ background:var(--purple-bg); color:var(--purple); }
  .stat-label{ font-size:12.5px; color:var(--text-mid); }
  .stat-value{ font-size:24px; font-weight:700; color:var(--text-dark); line-height:1.3; }

  .section-title{ font-size:17px; font-weight:700; color:var(--green-accent); margin:0 0 14px 0; }
  .stage-row{ display:grid; grid-template-columns:repeat(6,1fr); gap:14px; margin-bottom:26px; }
  .stage-card{ background:var(--card-bg); border:1px solid #d7d7d7; border-bottom:4px solid; border-radius:10px; box-shadow:0 0 4px rgba(0,0,0,.1); padding:16px 14px; text-align:center; }
  .stage-card .stage-badge{ width:44px; height:44px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 8px auto; font-size:12px; font-weight:700; }
  .stage-card .stage-name{ font-size:13px; font-weight:700; margin-bottom:2px; }
  .stage-card .stage-no{ font-size:10.5px; color:var(--text-light); margin-bottom:10px; text-transform:uppercase; letter-spacing:.4px; }
  .stage-card .stage-nums{ display:flex; justify-content:space-around; font-size:11px; color:var(--text-mid); }
  .stage-card .stage-nums strong{ display:block; font-size:16px; color:var(--text-dark); }

  .filters-row{ display:flex; gap:12px; margin-bottom:16px; flex-wrap:wrap; align-items:center; }
  .filters-row .search-input{ flex:1; min-width:220px; border:1px solid var(--border); border-radius:8px; padding:11px 14px; font-size:13px; font-family:var(--font-body); }
  .filters-row select{ border:1px solid var(--border); border-radius:8px; padding:11px 14px; font-size:13px; font-family:var(--font-body); background:#fff; color:var(--text-dark); }
  .table-panel{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; overflow-x:auto; }
  table{ width:100%; border-collapse:collapse; min-width:900px; table-layout:fixed; }
  thead th{ text-align:left; font-size:11px; font-weight:700; color:var(--text-mid); padding:14px 12px; border-bottom:1px solid var(--border); background:#fbfcfb; white-space:nowrap; }
  thead th:not(:first-child){ text-align:center; }
  tbody td{ padding:12px; font-size:12.5px; border-bottom:1px solid #f0f2f0; vertical-align:middle; }
  tbody td:not(:first-child){ text-align:center; }
  tbody tr:last-child td{ border-bottom:none; }
  tbody tr:hover{ background:#fbfcfb; }
  .tenant-cell{ display:flex; align-items:center; gap:10px; }
  .avatar{ width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; color:#567357; background:#d7efd7; flex-shrink:0; }
  .tenant-name{ font-weight:700; font-size:13px; }
  .tenant-email{ font-size:11px; color:var(--text-light); }
  .tenant-email a{ color:var(--text-light); }
  .stage-pill{ font-weight:700; font-size:12px; padding:4px 10px; border-radius:20px; display:inline-block; white-space:nowrap; }
  .balance-cell{ font-weight:700; }
  .days-cell{ font-weight:700; }
  .edit-btn{ border:1px solid #635f5f; background:#fff; border-radius:5px; width:36px; height:36px; display:flex; align-items:center; justify-content:center; cursor:pointer; }
  .edit-btn:hover{ background:#f5f6f5; }
  .empty-row td{ text-align:center; color:var(--text-light); font-style:italic; padding:34px; }

  .table-footer{ display:flex; align-items:center; justify-content:space-between; padding:14px 16px; font-size:13px; color:var(--text-mid); }
  .pagination{ display:flex; gap:6px; }
  .page-btn{ width:32px; height:32px; border-radius:8px; border:1px solid var(--border); background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:13px; font-weight:600; color:var(--text-dark); }
  .page-btn.active{ background:var(--green-btn); color:#fff; border-color:var(--green-btn); }
  .page-btn:disabled{ opacity:.4; cursor:not-allowed; }

  .overlay{ display:none; position:fixed; inset:0; background:rgba(20,30,20,.4); z-index:60; }
  .overlay.open{ display:block; }
  .modal{ display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; border-radius:14px; width:420px; max-width:92vw; padding:26px; z-index:61; box-shadow:0 20px 50px rgba(0,0,0,.2); }
  .modal.open{ display:block; }
  .modal h2{ font-size:16px; margin:0 0 4px 0; }
  .modal .modal-sub{ font-size:12.5px; color:var(--text-mid); margin-bottom:18px; }
  .modal label{ display:block; font-size:12px; font-weight:700; margin:14px 0 6px 0; color:var(--text-dark); }
  .modal select, .modal textarea{ width:100%; border:1px solid var(--border); border-radius:8px; padding:10px 12px; font-size:13px; font-family:var(--font-body); resize:vertical; }
  .modal textarea{ min-height:70px; }
  .modal input[type="date"]{ width:100%; border:1px solid var(--border); border-radius:8px; padding:10px 12px; font-size:13px; font-family:var(--font-body); }
  .modal-actions{ display:flex; justify-content:flex-end; gap:10px; margin-top:20px; }
  .modal-btn{ padding:9px 18px; border-radius:8px; font-size:12.5px; font-weight:700; cursor:pointer; border:1px solid var(--border); }
  .modal-btn.cancel{ background:#fff; color:var(--text-mid); }
  .modal-btn.confirm{ background:var(--green-btn); color:#fff; border-color:var(--green-btn); }
  .modal-btn.confirm:hover{ background:var(--green-btn-hover); }
  .modal-error{ font-size:12px; color:var(--status-occupied); margin-top:8px; display:none; }

  .action-explain{ font-size:12px; line-height:1.5; color:var(--text-mid); background:#f4f7f4; border-radius:8px; padding:10px 12px; margin-top:8px; }
  .action-explain.warning{ background:#fdf1e9; color:#8a4a12; }
  .reset-confirm-box{ background:#fbeceb; border:1px solid #f2cfcc; border-radius:8px; padding:12px; margin-top:10px; }
  .reset-confirm-check{ display:flex; align-items:flex-start; gap:9px; font-size:12px; line-height:1.5; color:#8a352c; cursor:pointer; }
  .reset-confirm-check input{ margin-top:2px; flex-shrink:0; }
  .modal-btn.confirm:disabled{ opacity:.5; cursor:not-allowed; }

  .toast{ position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(20px); background:#243026; color:#fff; padding:12px 22px; border-radius:10px; font-size:13px; opacity:0; pointer-events:none; transition:.25s; z-index:80; }
  .toast.visible{ opacity:1; transform:translateX(-50%) translateY(0); }
  .toast.error{ background:#a8352c; }

  .action-btns{ display:flex; gap:6px; justify-content:center; }
  .history-modal{ width:520px; max-height:82vh; overflow-y:auto; }
  .history-modal .modal-sub{ margin-bottom:14px; }
  .history-tenant-flags{ display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px; }
  .history-flag{ font-size:11px; font-weight:700; padding:3px 9px; border-radius:20px; }
  .history-flag.blacklisted{ background:#ecebeb; color:#020202; }
  .history-flag.paused{ background:#eee; color:#888; }
  .timeline{ list-style:none; margin:0; padding:0; }
  .timeline-item{ display:flex; gap:12px; padding:0 0 18px 0; }
  .timeline-item:last-child{ padding-bottom:0; }
  .timeline-dot-col{ display:flex; flex-direction:column; align-items:center; flex-shrink:0; }
  .timeline-dot{ width:12px; height:12px; border-radius:50%; margin-top:3px; flex-shrink:0; }
  .timeline-line{ width:2px; flex:1; background:var(--border); margin-top:4px; }
  .timeline-body{ flex:1; padding-bottom:2px; }
  .timeline-head{ display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:3px; }
  .timeline-stage{ font-size:12.5px; font-weight:700; }
  .timeline-status{ font-size:10.5px; font-weight:700; padding:2px 8px; border-radius:20px; text-transform:uppercase; letter-spacing:.3px; }
  .timeline-status.resolved,.timeline-status.sent{ background:var(--status-vacant-bg); color:var(--green-accent); }
  .timeline-status.pending{ background:#fdf5e9; color:#de6f13; }
  .timeline-time{ font-size:11px; color:var(--text-light); margin-bottom:4px; }
  .timeline-msg{ font-size:12px; color:var(--text-mid); line-height:1.5; background:#fbfcfb; border:1px solid var(--border); border-radius:8px; padding:8px 10px; }
  .timeline-override-tag{ font-size:10px; font-weight:700; color:#5408da; background:#efe3ff; padding:2px 7px; border-radius:20px; }
  .timeline-empty{ text-align:center; color:var(--text-light); font-style:italic; padding:30px 10px; }
  .stub-actions{ display:flex; gap:10px; margin-top:18px; padding-top:16px; border-top:1px solid var(--border); }
  .stub-btn{ flex:1; padding:10px 12px; border-radius:8px; font-size:12px; font-weight:700; border:1px dashed var(--border); background:#fbfcfb; color:var(--text-light); cursor:not-allowed; text-align:center; }
  .action-btn-ready{ flex:1; padding:10px 12px; border-radius:8px; font-size:12px; font-weight:700; border:1px solid var(--green-btn); background:var(--green-btn); color:#fff; cursor:pointer; text-align:center; text-decoration:none; display:block; }
  .action-btn-ready:hover{ background:var(--green-btn-hover); }
  .action-btn-issue{ flex:1; padding:10px 12px; border-radius:8px; font-size:12px; font-weight:700; border:1px solid var(--status-occupied); background:var(--status-occupied); color:#fff; cursor:pointer; text-align:center; }
  .eviction-form{ display:none; margin-top:14px; padding-top:14px; border-top:1px dashed var(--border); }
  .eviction-form.open{ display:block; }
  .history-actions{ display:flex; justify-content:flex-end; margin-top:18px; }

  @media (max-width: 1100px){ .stage-row{ grid-template-columns:repeat(3,1fr); } }
  @media (max-width: 640px){ .stage-row{ grid-template-columns:repeat(2,1fr); } }
</style>
</head>
<body>

<div class="app">
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-logo"><span class="logo-mark"></span><span class="logo-text">NEST.PH</span></div>
    <div class="sidebar-section-label">Quick Access</div>
    <ul class="nav-list">
      <li class="nav-item" data-href="{{ route('dashboard') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></span><span class="label">Dashboard</span></li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></span><span class="label">Tenant Manager</span></li>
      <li class="nav-item" data-href="{{ route('payments.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></span><span class="label">Billing and Payments</span></li>
      <li class="nav-item active" data-href="{{ route('delinquency.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg></span><span class="label">Delinquency</span></li>
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
      <div class="search-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg><input type="text" placeholder="Search pages"></div>
      <div class="topbar-right">
        <div class="topbar-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
      </div>
    </div>

    <div class="content">
      <div class="page-head">
        <div class="back-arrow" data-href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
        <h1>Delinquency</h1>
      </div>

      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-icon red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg></div>
          <div><div class="stat-label">Overdue Accounts</div><div class="stat-value">{{ $stats['overdue_accounts'] }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon orange"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/><path d="M12 9v4M12 17h.01"/></svg></div>
          <div><div class="stat-label">Tenants In Escalation</div><div class="stat-value">{{ $stats['tenants_in_escalation'] }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 11-6.2-8.6M22 4L12 14l-3-3"/></svg></div>
          <div><div class="stat-label">Total Overdue Balance</div><div class="stat-value">₱{{ number_format($stats['total_overdue_balance'], 0) }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div>
          <div><div class="stat-label">Avg. Days Overdue</div><div class="stat-value">{{ $stats['avg_days_overdue'] }}</div></div>
        </div>
      </div>

      <div class="section-title">Delinquency Overview by Escalation Stage</div>
      <div class="stage-row">
        @foreach($stageBreakdown as $s)
          <div class="stage-card" style="border-bottom-color:{{ $s['accent'] }};">
            <div class="stage-badge" style="background:{{ $s['accent'] }}; color:{{ $s['text'] }};">{{ $s['stage'] }}</div>
            <div class="stage-name">{{ $s['name'] }}</div>
            <div class="stage-no">Stage {{ $s['stage'] }}</div>
            <div class="stage-nums">
              <div><strong>{{ $s['accounts'] }}</strong>Accounts</div>
              <div><strong>₱{{ number_format($s['total_balance'], 0) }}</strong>Balance</div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="section-title">Delinquent Accounts</div>
      <div class="filters-row">
        <input type="text" class="search-input" id="searchInput" placeholder="Search Tenant or Room...">
        <select id="stageFilter">
          <option value="">All Stages</option>
          @foreach($stageBreakdown as $s)
            <option value="{{ $s['stage'] }}">Stage {{ $s['stage'] }} — {{ $s['name'] }}</option>
          @endforeach
        </select>
      </div>

      <div class="table-panel">
        <table>
          <colgroup>
            <col style="width:26%">
            <col style="width:8%">
            <col style="width:12%">
            <col style="width:12%">
            <col style="width:20%">
            <col style="width:12%">
            <col style="width:10%">
          </colgroup>
          <thead>
            <tr>
              <th>Tenant</th><th>Room</th><th>Days Overdue</th><th>Total Balance</th><th>Escalation Stage</th><th>Last Payment</th><th>Actions</th>
            </tr>
          </thead>
          <tbody id="tableBody"></tbody>
        </table>
        <div class="table-footer">
          <div id="tableSummary"></div>
          <div class="pagination" id="pagination"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="overlay" id="overlay"></div>
<div class="modal" id="overrideModal">
  <h2 id="modalTenantName">Override Escalation</h2>
  <div class="modal-sub" id="modalTenantStage"></div>

  <label for="overrideAction">Action</label>
  <select id="overrideAction">
    <option value="pause">Pause — give the tenant more time</option>
    <option value="unpause">Unpause — resume normal escalation</option>
    <option value="clear">Clear — resolve current items, lift restrictions</option>
    <option value="reset">Reset — erase escalation history</option>
  </select>

  <div class="action-explain" id="actionExplain"></div>

  <div class="reset-confirm-box" id="resetConfirmBox" style="display:none;">
    <label class="reset-confirm-check">
      <input type="checkbox" id="resetUnderstandCheck">
      <span>I understand Reset does <u>not</u> stop the overdue clock, and confirm I want to erase this tenant's escalation history anyway.</span>
    </label>
  </div>

  <label for="overrideReason">Reason (required)</label>
  <textarea id="overrideReason" placeholder="e.g. Tenant messaged admin, asked for 2 more weeks to pay..."></textarea>
  <div class="modal-error" id="modalError"></div>

  <div class="modal-actions">
    <button class="modal-btn cancel" id="modalCancel">Cancel</button>
    <button class="modal-btn confirm" id="modalConfirm">Apply Override</button>
  </div>
</div>

<div class="modal history-modal" id="historyModal">
  <h2 id="historyTenantName">Escalation History</h2>
  <div class="modal-sub" id="historyTenantStage"></div>
  <div class="history-tenant-flags" id="historyFlags"></div>

  <ul class="timeline" id="historyTimeline"></ul>

  <div class="stub-actions" id="historyActionsArea"></div>

  <div class="eviction-form" id="evictionForm">
    <label for="evictionReason">Reason for Eviction (required)</label>
    <textarea id="evictionReason" placeholder="e.g. Non-payment of rent for over 30 days despite full escalation"></textarea>
    <label for="evictionDate">Notice Date</label>
    <input type="date" id="evictionDate">
    <div class="modal-error" id="evictionError"></div>
    <div class="modal-actions">
      <button class="modal-btn cancel" id="evictionCancelBtn">Cancel</button>
      <button class="modal-btn confirm" id="evictionConfirmBtn" style="background:var(--status-occupied);border-color:var(--status-occupied);">Confirm & Send Notice</button>
    </div>
  </div>

  <div class="history-actions">
    <button class="modal-btn cancel" id="historyCloseBtn">Close</button>
  </div>
</div>

<div class="toast" id="toast"></div>

<script type="application/json" id="accounts-data">{!! json_encode($accounts) !!}</script>

<script>
(function(){
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  const accounts = JSON.parse(document.getElementById('accounts-data').textContent);

  const $ = id => document.getElementById(id);
  const PAGE_SIZE = 5;
  let page = 1;
  let search = '';
  let stageFilter = '';
  let overrideTenantId = null;

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

  function peso(n){
    const num = parseFloat(n);
    return isNaN(num) ? '—' : '₱' + num.toLocaleString('en-PH', { minimumFractionDigits:0, maximumFractionDigits:0 });
  }

  function initials(name){
    return (name || '?').split(' ').map(p => p[0]).join('').slice(0,2).toUpperCase();
  }

  function visible(){
    return accounts.filter(a => {
      if(stageFilter !== '' && String(a.stage) !== stageFilter) return false;
      if(!search) return true;
      const hay = `${a.name ?? ''} ${a.room ?? ''}`.toLowerCase();
      return hay.includes(search);
    });
  }

  function renderTable(){
    const all = visible();
    const totalPages = Math.max(1, Math.ceil(all.length / PAGE_SIZE));
    page = Math.min(page, totalPages);
    const shown = all.slice((page - 1) * PAGE_SIZE, page * PAGE_SIZE);

    if(shown.length === 0){
      $('tableBody').innerHTML = '<tr class="empty-row"><td colspan="7">No delinquent accounts match this view.</td></tr>';
    } else {
      $('tableBody').innerHTML = shown.map(a => `
        <tr>
          <td>
            <div class="tenant-cell">
              <div class="avatar">${esc(initials(a.name))}</div>
              <div>
                <div class="tenant-name">${esc(a.name)}</div>
                <div class="tenant-email"><a href="mailto:${esc(a.email)}">${esc(a.email)}</a></div>
              </div>
            </div>
          </td>
          <td>${esc(a.room)}</td>
          <td class="days-cell">${a.days_overdue} day${a.days_overdue === 1 ? '' : 's'}</td>
          <td class="balance-cell">${peso(a.balance)}</td>
          <td><span class="stage-pill" style="background:${a.stage_accent};color:${a.stage_text};">Stage ${a.stage} — ${esc(a.stage_name)}</span>${a.escalation_paused ? ' <span class="stage-pill" style="background:#eee;color:#888;">Paused</span>' : ''}</td>
          <td>${a.last_payment ? esc(a.last_payment) : '—'}</td>
          <td>
            <div class="action-btns">
              <button class="edit-btn" data-history="${a.id}" title="View escalation history">
                <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/></svg>
              </button>
              <button class="edit-btn" data-override="${a.id}" title="Override escalation (Table 29)">
                <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4z"/></svg>
              </button>
            </div>
          </td>
        </tr>
      `).join('');
    }

    $('tableBody').querySelectorAll('[data-override]').forEach(btn => {
      btn.addEventListener('click', () => openOverrideModal(Number(btn.dataset.override)));
    });

    $('tableBody').querySelectorAll('[data-history]').forEach(btn => {
      btn.addEventListener('click', () => openHistoryModal(Number(btn.dataset.history)));
    });

    $('tableSummary').textContent = all.length === 0
      ? 'No accounts'
      : `Showing ${(page-1)*PAGE_SIZE + 1} to ${Math.min(page*PAGE_SIZE, all.length)} of ${all.length} accounts`;

    renderPagination(totalPages);
  }

  function renderPagination(totalPages){
    let html = `<button class="page-btn" id="prevPage" ${page === 1 ? 'disabled' : ''}>&lsaquo;</button>`;
    for(let i = 1; i <= totalPages; i++){
      html += `<button class="page-btn ${i === page ? 'active' : ''}" data-page="${i}">${i}</button>`;
    }
    html += `<button class="page-btn" id="nextPage" ${page === totalPages ? 'disabled' : ''}>&rsaquo;</button>`;
    $('pagination').innerHTML = html;

    $('pagination').querySelectorAll('[data-page]').forEach(btn => {
      btn.addEventListener('click', () => { page = Number(btn.dataset.page); renderTable(); });
    });
    const prev = $('prevPage'), next = $('nextPage');
    if(prev) prev.addEventListener('click', () => { page = Math.max(1, page - 1); renderTable(); });
    if(next) next.addEventListener('click', () => { page = Math.min(totalPages, page + 1); renderTable(); });
  }

  $('searchInput').addEventListener('input', function(){
    search = this.value.trim().toLowerCase();
    page = 1;
    renderTable();
  });

  $('stageFilter').addEventListener('change', function(){
    stageFilter = this.value;
    page = 1;
    renderTable();
  });

  const ACTION_EXPLANATIONS = {
    pause: {
      text: 'Freezes this tenant exactly where they are. No more reminders, no restrictions, no blacklist — nothing happens until you Unpause them. Their current stage and full history stay untouched. Use this when a tenant has honestly communicated and you\'re giving them more time.',
      warning: false,
    },
    unpause: {
      text: 'Lets the system resume normal escalation for this tenant on the next run, picking up right where they were paused.',
      warning: false,
    },
    clear: {
      text: 'Marks everything currently open as resolved and lifts any portal restriction, but keeps the history — it stays visible for the record. Does not touch a Stage 6 blacklist.',
      warning: false,
    },
    reset: {
      text: 'Erases this tenant\'s entire escalation history — like it never started. Important: this does NOT stop the overdue clock. If they\'re still unpaid, the system will immediately restart from Stage 1 (including re-sending SMS reminders) the very next time it runs. This is for fixing a mistake or clearing test data — NOT for giving a tenant more time. For that, use Pause instead.',
      warning: true,
    },
  };

  function updateActionExplain(){
    const action = $('overrideAction').value;
    const info = ACTION_EXPLANATIONS[action];
    const box = $('actionExplain');
    box.textContent = info.text;
    box.classList.toggle('warning', info.warning);

    const resetBox = $('resetConfirmBox');
    if(action === 'reset'){
      resetBox.style.display = 'block';
      $('resetUnderstandCheck').checked = false;
    } else {
      resetBox.style.display = 'none';
    }
    updateConfirmButtonState();
  }

  function updateConfirmButtonState(){
    const action = $('overrideAction').value;
    const needsCheck = action === 'reset' && !$('resetUnderstandCheck').checked;
    $('modalConfirm').disabled = needsCheck;
  }

  $('overrideAction').addEventListener('change', updateActionExplain);
  $('resetUnderstandCheck').addEventListener('change', updateConfirmButtonState);

  function openOverrideModal(tenantId){
    const account = accounts.find(a => a.id === tenantId);
    if(!account) return;
    overrideTenantId = tenantId;
    $('modalTenantName').textContent = account.name;
    $('modalTenantStage').textContent = `Currently: Stage ${account.stage} — ${account.stage_name}${account.escalation_paused ? ' (Paused)' : ''}`;
    $('overrideAction').value = account.escalation_paused ? 'unpause' : 'pause';
    $('overrideReason').value = '';
    $('modalError').style.display = 'none';
    updateActionExplain();
    $('overlay').classList.add('open');
    $('overrideModal').classList.add('open');
  }

  function closeOverrideModal(){
    $('overlay').classList.remove('open');
    $('overrideModal').classList.remove('open');
    overrideTenantId = null;
  }

  $('modalCancel').addEventListener('click', closeOverrideModal);
  $('overlay').addEventListener('click', () => { closeOverrideModal(); closeHistoryModal(); });

  $('modalConfirm').addEventListener('click', async function(){
    if(this.disabled) return;

    const reason = $('overrideReason').value.trim();
    if(!reason){
      $('modalError').textContent = 'A reason is required.';
      $('modalError').style.display = 'block';
      return;
    }

    this.disabled = true;
    try {
      const res = await fetch(`/delinquency/${overrideTenantId}/override`, {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' },
        body: JSON.stringify({ action: $('overrideAction').value, reason }),
      });
      const body = await res.json().catch(() => ({}));
      if(!res.ok) throw new Error(body.message || 'Override failed.');
      toast(body.message || 'Escalation override applied.');
      closeOverrideModal();
      setTimeout(() => window.location.reload(), 700);
    } catch(e){
      $('modalError').textContent = e.message;
      $('modalError').style.display = 'block';
    }
    this.disabled = false;
  });

  let historyTenant = null;

  async function openHistoryModal(tenantId){
    $('historyTenantName').textContent = 'Loading...';
    $('historyTenantStage').textContent = '';
    $('historyFlags').innerHTML = '';
    $('historyTimeline').innerHTML = '<li class="timeline-empty">Loading escalation history...</li>';
    $('historyActionsArea').innerHTML = '';
    $('evictionForm').classList.remove('open');
    $('overlay').classList.add('open');
    $('historyModal').classList.add('open');

    try {
      const res = await fetch(`/delinquency/${tenantId}/history`, { headers: { 'Accept':'application/json' } });
      if(!res.ok) throw new Error('Could not load escalation history.');
      const data = await res.json();
      renderHistoryModal(data);
    } catch(e){
      $('historyTenantName').textContent = 'Escalation History';
      $('historyTimeline').innerHTML = `<li class="timeline-empty">${esc(e.message)}</li>`;
    }
  }

  function renderHistoryModal(data){
    const t = data.tenant;
    historyTenant = t;
    $('historyTenantName').textContent = t.name;
    $('historyTenantStage').textContent = `${t.room !== '—' ? 'Room ' + t.room + ' — ' : ''}Currently: Stage ${t.current_stage} — ${t.current_stage_name}`;

    const flags = [];
    if(t.is_blacklisted) flags.push('<span class="history-flag blacklisted">Blacklisted</span>');
    if(t.escalation_paused) flags.push('<span class="history-flag paused">Escalation Paused</span>');
    $('historyFlags').innerHTML = flags.join('');

    renderHistoryActions(t);
    $('evictionForm').classList.remove('open');

    if(data.logs.length === 0){
      $('historyTimeline').innerHTML = '<li class="timeline-empty">No escalation activity recorded for this tenant yet.</li>';
      return;
    }

    $('historyTimeline').innerHTML = data.logs.map((log, i) => `
      <li class="timeline-item">
        <div class="timeline-dot-col">
          <div class="timeline-dot" style="background:${log.stage_accent};"></div>
          ${i < data.logs.length - 1 ? '<div class="timeline-line"></div>' : ''}
        </div>
        <div class="timeline-body">
          <div class="timeline-head">
            <span class="timeline-stage">${log.is_override ? 'Admin Override' : 'Stage ' + log.stage + ' — ' + esc(log.stage_name)}</span>
            <span class="timeline-status ${log.status}">${esc(log.status)}</span>
            ${log.is_override ? '<span class="timeline-override-tag">Table 29</span>' : ''}
          </div>
          <div class="timeline-time">${esc(log.created_at || '')}${log.performed_by ? ' · by ' + esc(log.performed_by) : ''}</div>
          ${timelineMessageLine(log)}
        </div>
      </li>
    `).join('');
  }

  function timelineMessageLine(log){
    // PDF-backed entries store a storage path in message_content -- never
    // show that raw path to the admin, show a friendly line instead.
    if(log.action_type === 'demand_letter_generated'){
      return '<div class="timeline-msg">Formal demand letter generated and saved to this tenant\'s record.</div>';
    }
    if(log.action_type === 'eviction_notice_issued'){
      return '<div class="timeline-msg">Eviction notice generated and dispatched via SMS.</div>';
    }
    return log.message_content ? `<div class="timeline-msg">${esc(log.message_content)}</div>` : '';
  }

  function renderHistoryActions(t){
    let demandLetterHtml, evictionHtml;

    if(t.demand_letter_ready){
      demandLetterHtml = `<a class="action-btn-ready" href="/delinquency/${t.id}/demand-letter" target="_blank">View Demand Letter</a>`;
    } else {
      demandLetterHtml = `<div class="stub-btn" title="The system generates this automatically once Stage 5 is reached.">View Demand Letter (not yet generated)</div>`;
    }

    if(!t.is_blacklisted){
      evictionHtml = `<div class="stub-btn" title="Only available once a tenant reaches Stage 6 (Blacklisted).">Issue Eviction Notice (available at Stage 6)</div>`;
    } else if(t.eviction_notice_issued){
      evictionHtml = `<a class="action-btn-ready" href="/delinquency/${t.id}/eviction-notice" target="_blank">View Eviction Notice</a>`;
    } else {
      evictionHtml = `<button type="button" class="action-btn-issue" id="openEvictionFormBtn">Issue Eviction Notice</button>`;
    }

    $('historyActionsArea').innerHTML = `<div class="stub-actions" style="border-top:none;padding-top:0;margin-top:0;">${demandLetterHtml}${evictionHtml}</div>`;

    const openBtn = document.getElementById('openEvictionFormBtn');
    if(openBtn){
      openBtn.addEventListener('click', () => {
        $('evictionReason').value = '';
        $('evictionDate').value = new Date().toISOString().slice(0, 10);
        $('evictionError').style.display = 'none';
        $('evictionForm').classList.add('open');
      });
    }
  }

  function closeHistoryModal(){
    $('overlay').classList.remove('open');
    $('historyModal').classList.remove('open');
    historyTenant = null;
  }

  $('historyCloseBtn').addEventListener('click', closeHistoryModal);

  $('evictionCancelBtn').addEventListener('click', () => {
    $('evictionForm').classList.remove('open');
  });

  $('evictionConfirmBtn').addEventListener('click', async function(){
    if(this.disabled || !historyTenant) return;

    const reason = $('evictionReason').value.trim();
    const noticeDate = $('evictionDate').value;

    if(!reason || !noticeDate){
      $('evictionError').textContent = 'Both a reason and a notice date are required.';
      $('evictionError').style.display = 'block';
      return;
    }

    this.disabled = true;
    try {
      const res = await fetch(`/delinquency/${historyTenant.id}/eviction-notice`, {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' },
        body: JSON.stringify({ reason, notice_date: noticeDate }),
      });
      const body = await res.json().catch(() => ({}));
      if(!res.ok) throw new Error(body.message || 'Could not issue eviction notice.');
      toast(body.message || 'Eviction notice issued.');
      $('evictionForm').classList.remove('open');
      openHistoryModal(historyTenant.id); // refresh the modal in place
    } catch(e){
      $('evictionError').textContent = e.message;
      $('evictionError').style.display = 'block';
    }
    this.disabled = false;
  });

  renderTable();
})();
</script>

<script>
(function(){
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
})();
</script>

</body>
</html>