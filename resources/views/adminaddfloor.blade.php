<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH — Vacancy Monitoring</title>
<style>
  :root{
    --green-dark:#3f6b4a; --green-mid:#4f7c57;
    --green-sidebar-top:#5b8a63; --green-sidebar-bottom:#2c4a35;
    --green-accent:#2f6f3c; --green-btn:#2f6b3a; --green-btn-hover:#255a2f;
    --status-occupied:#d9564f; --status-occupied-bg:#f7d9d7;
    --status-vacant:#7fc98a; --status-vacant-bg:#d9f2dd;
    --status-maintenance:#c9962f; --status-maintenance-bg:#f6ecd6;
    --bg-page:#eef1ee; --card-bg:#ffffff;
    --text-dark:#243026; --text-mid:#5b6b60; --text-light:#8a9690; --border:#e2e6e2;
    --font-body: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  }
  *{box-sizing:border-box;}
  html,body{ margin:0; padding:0; font-family:var(--font-body); background:var(--bg-page); color:var(--text-dark); }
  .app{ display:flex; min-height:100vh; }

  .sidebar{ width:220px; flex-shrink:0; background:linear-gradient(180deg, var(--green-sidebar-top) 0%, var(--green-sidebar-bottom) 100%); color:#eaf0ea; display:flex; flex-direction:column; padding:20px 0; }
  .sidebar-logo{ display:flex; align-items:center; gap:8px; padding:0 20px 22px 20px; font-weight:700; font-size:17px; border-bottom:1px solid rgba(255,255,255,0.12); margin-bottom:14px; }
  .sidebar-logo .logo-mark{ width:18px; height:18px; border:2px solid #eaf0ea; display:inline-block; position:relative; }
  .sidebar-logo .logo-mark::before, .sidebar-logo .logo-mark::after{ content:''; position:absolute; background:#eaf0ea; width:2px; height:14px; top:0; left:6px; }
  .sidebar-section-label{ font-size:11px; text-transform:uppercase; letter-spacing:1px; color:rgba(234,240,234,0.55); padding:4px 20px 10px 20px; font-weight:600; }
  .nav-list{ list-style:none; margin:0; padding:0; flex:1; }
  .nav-item{ display:flex; align-items:center; gap:12px; padding:11px 20px; font-size:13.5px; color:rgba(234,240,234,0.78); cursor:pointer; border-left:3px solid transparent; }
  .nav-item:hover{ background:rgba(255,255,255,0.06); color:#fff; }
  .nav-item.active{ background:rgba(255,255,255,0.14); color:#fff; font-weight:600; border-left:3px solid #ffffff; }
  .nav-item .icon svg{ width:16px; height:16px; }
  .sidebar-footer{ padding:14px 20px 0 20px; border-top:1px solid rgba(255,255,255,0.12); margin-top:10px; }
  .sidebar-footer .nav-item{ padding-left:0; }

  .main{ flex:1; display:flex; flex-direction:column; min-width:0; }
  .topbar{ display:flex; align-items:center; gap:16px; background:linear-gradient(90deg, var(--green-mid), var(--green-dark)); padding:14px 28px; }
  .topbar .hamburger{ width:20px; height:16px; display:flex; flex-direction:column; justify-content:space-between; cursor:pointer; }
  .topbar .hamburger span{ display:block; height:2px; background:#eaf0ea; border-radius:2px; }
  .search-box{ flex:1; max-width:420px; display:flex; align-items:center; gap:8px; background:rgba(255,255,255,0.92); border-radius:8px; padding:9px 14px; }
  .search-box svg{ width:15px; height:15px; color:#8a9690; flex-shrink:0; }
  .search-box input{ border:none; outline:none; background:transparent; font-size:13.5px; width:100%; color:var(--text-dark); }
  .topbar-right{ margin-left:auto; display:flex; align-items:center; gap:18px; }
  .topbar-icon{ width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; color:#eaf0ea; cursor:pointer; position:relative; }
  .topbar-icon svg{ width:16px; height:16px; }
  .badge{ position:absolute; top:-3px; right:-3px; background:#e0554f; color:#fff; font-size:9px; font-weight:700; width:15px; height:15px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid var(--green-dark); }
  .avatar-icon{ background:rgba(255,255,255,0.9); color:var(--green-dark); }

  .content{ padding:28px 32px 48px 32px; flex:1; }
  .page-head{ display:flex; align-items:center; gap:12px; margin-bottom:22px; }
  .back-arrow{ width:34px; height:34px; border-radius:8px; background:var(--card-bg); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-mid); flex-shrink:0; }
  .page-head h1{ font-size:20px; font-weight:700; margin:0; color:var(--text-dark); }
  .page-head-tools{ margin-left:auto; display:flex; align-items:center; gap:10px; }
  .head-tool-btn{ width:32px; height:32px; border-radius:7px; background:var(--card-bg); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; color:var(--text-mid); cursor:pointer; }
  .head-tool-btn svg{ width:14px; height:14px; }

  .stats-row{ display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-bottom:20px; }
  .stat-card{ background:var(--card-bg); border-radius:12px; border:1px solid var(--border); padding:18px 20px; display:flex; align-items:center; gap:14px; box-shadow:0 1px 2px rgba(20,30,20,0.03); }
  .stat-icon{ width:46px; height:46px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .stat-icon svg{ width:20px; height:20px; }
  .stat-icon.bedspaces{ background:var(--status-vacant-bg); color:var(--green-accent); }
  .stat-icon.occupied{ background:var(--status-occupied-bg); color:var(--status-occupied); }
  .stat-icon.vacant{ background:var(--status-vacant-bg); color:#4a9a58; }
  .stat-icon.maintenance{ background:var(--status-maintenance-bg); color:var(--status-maintenance); }
  .stat-label{ font-size:12.5px; color:var(--text-mid); margin-bottom:3px; }
  .stat-value{ font-size:24px; font-weight:700; color:var(--text-dark); }

  .filter-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:18px 20px; display:flex; align-items:flex-end; gap:18px; margin-bottom:20px; flex-wrap:wrap; }
  .filter-group{ display:flex; flex-direction:column; gap:6px; min-width:170px; }
  .filter-group label{ font-size:12px; color:var(--text-mid); }
  .filter-select{ display:flex; align-items:center; justify-content:space-between; border:1px solid var(--border); border-radius:8px; padding:9px 12px; font-size:13px; color:var(--text-dark); background:#fff; cursor:pointer; }
  .filter-select svg{ width:12px; height:12px; color:var(--text-light); }
  .add-room-btn{ margin-left:auto; display:flex; align-items:center; gap:8px; background:var(--green-btn); color:#fff; border:none; border-radius:8px; padding:11px 18px; font-size:13px; font-weight:600; cursor:pointer; }
  .add-room-btn:hover{ background:var(--green-btn-hover); }
  .add-room-btn svg{ width:13px; height:13px; }

  .map-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:22px 24px 26px 24px; }
  .map-head{ display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:18px; flex-wrap:wrap; gap:10px; }
  .map-head h2{ font-size:15px; font-weight:700; margin:0 0 3px 0; }
  .map-head p{ font-size:12.5px; color:var(--text-light); margin:0; }
  .legend{ display:flex; align-items:center; gap:18px; }
  .legend-item{ display:flex; align-items:center; gap:6px; font-size:12.5px; color:var(--text-mid); }
  .legend-dot{ width:9px; height:9px; border-radius:50%; display:inline-block; }
  .legend-dot.occupied{ background:var(--status-occupied); }
  .legend-dot.vacant{ background:var(--status-vacant); }
  .legend-dot.maintenance{ background:var(--status-maintenance); }

  .floor-block{ margin-bottom:10px; }
  .floor-header{ display:flex; align-items:center; justify-content:space-between; border:1px solid var(--border); border-radius:8px; padding:13px 16px; font-size:13.5px; font-weight:600; color:var(--text-dark); cursor:pointer; background:#fff; }
  .floor-header:hover{ background:#f7f9f7; }
  .floor-header .chev{ width:14px; height:14px; color:var(--text-light); transition:transform 0.2s ease; flex-shrink:0; }
  .floor-block.open .floor-header .chev{ transform:rotate(90deg); }
  .floor-block.open .floor-header{ border-bottom-left-radius:0; border-bottom-right-radius:0; border-bottom:none; }

  .floor-rooms{ display:none; grid-template-columns:repeat(auto-fill, minmax(170px, 1fr)); gap:14px; border:1px solid var(--border); border-top:none; border-bottom-left-radius:8px; border-bottom-right-radius:8px; padding:18px 16px 20px 16px; }
  .floor-block.open .floor-rooms{ display:grid; }
  .floor-empty{ display:none; border:1px solid var(--border); border-top:none; border-bottom-left-radius:8px; border-bottom-right-radius:8px; padding:22px 16px; text-align:center; color:var(--text-light); font-size:12.5px; }
  .floor-block.open .floor-empty{ display:block; }

  .room-card{ border:1px solid var(--border); border-radius:10px; padding:14px 14px 16px 14px; background:#fff; }
  .room-card-head{ display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:4px; gap:8px; }
  .room-title{ font-size:13px; font-weight:700; color:var(--text-dark); }
  .room-meta{ font-size:11px; color:var(--text-light); margin-bottom:8px; }
  .room-status-badge{ font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.3px; padding:2px 7px; border-radius:20px; display:inline-block; margin-bottom:10px; }
  .room-status-badge.available{ background:var(--status-vacant-bg); color:#3f7a4a; }
  .room-status-badge.full{ background:var(--status-occupied-bg); color:var(--status-occupied); }
  .room-status-badge.maintenance{ background:var(--status-maintenance-bg); color:var(--status-maintenance); }

  .bed-row{ display:flex; align-items:center; gap:9px; margin-bottom:7px; cursor:pointer; }
  .bed-row:last-child{ margin-bottom:0; }
  .bed-row:hover .bed-label{ color:var(--text-dark); }
  .bed-swatch{ width:16px; height:16px; border-radius:4px; flex-shrink:0; cursor:pointer; transition:transform 0.1s ease; }
  .bed-swatch:hover{ transform:scale(1.15); }
  .bed-swatch.occupied{ background:var(--status-occupied); }
  .bed-swatch.vacant{ background:var(--status-vacant); }
  .bed-swatch.maintenance{ background:var(--status-maintenance); }
  .bed-label{ font-size:12.5px; color:var(--text-mid); }

  .floor-header-left{ display:flex; align-items:center; gap:10px; }
  .floor-header-right{ display:flex; align-items:center; gap:10px; }
  .floor-add-room-btn{ display:flex; align-items:center; gap:6px; background:#fff; border:1px solid var(--border); color:var(--green-btn); font-size:11.5px; font-weight:700; padding:6px 11px; border-radius:6px; cursor:pointer; }
  .floor-add-room-btn:hover{ background:var(--status-vacant-bg); border-color:#bfe0c6; }
  .floor-add-room-btn svg{ width:11px; height:11px; }

  .delete-floor-btn{ width:26px; height:26px; border-radius:6px; border:1px solid var(--border); background:#fff; color:var(--text-light); display:flex; align-items:center; justify-content:center; cursor:pointer; }
  .delete-floor-btn:hover{ color:var(--status-occupied); border-color:#f2c6c3; background:var(--status-occupied-bg); }
  .delete-floor-btn svg{ width:12px; height:12px; }

  .room-actions{ display:flex; align-items:center; gap:4px; flex-shrink:0; }
  .room-action-btn{ width:22px; height:22px; border-radius:5px; border:1px solid transparent; background:transparent; color:var(--text-light); display:flex; align-items:center; justify-content:center; cursor:pointer; }
  .room-action-btn svg{ width:11px; height:11px; }
  .room-action-btn.edit:hover{ color:var(--green-accent); background:var(--status-vacant-bg); }
  .room-action-btn.delete:hover{ color:var(--status-occupied); background:var(--status-occupied-bg); }

  .modal-overlay{ display:none; position:fixed; inset:0; background:rgba(20,30,20,0.45); align-items:center; justify-content:center; z-index:100; padding:20px; }
  .modal-overlay.open{ display:flex; }
  .modal-box{ background:#fff; border-radius:14px; width:100%; max-width:420px; padding:24px 24px 20px 24px; box-shadow:0 20px 50px rgba(20,30,20,0.25); max-height:90vh; overflow-y:auto; }
  .modal-title{ font-size:16px; font-weight:700; color:var(--text-dark); margin:0 0 4px 0; }
  .modal-sub{ font-size:12.5px; color:var(--text-light); margin:0 0 18px 0; }
  .modal-row{ display:flex; gap:12px; }
  .modal-row .modal-field{ flex:1; }
  .modal-field{ margin-bottom:16px; }
  .modal-field label{ display:block; font-size:12px; color:var(--text-mid); margin-bottom:6px; font-weight:600; }
  .modal-field input, .modal-field select{ width:100%; border:1px solid var(--border); border-radius:8px; padding:10px 12px; font-size:13px; color:var(--text-dark); font-family:var(--font-body); outline:none; }
  .modal-field input:focus, .modal-field select:focus{ border-color:var(--green-accent); }
  .bed-status-rows{ display:flex; flex-direction:column; gap:10px; }
  .bed-status-row{ display:flex; align-items:center; gap:10px; }
  .bed-status-row span.bed-index{ font-size:12.5px; color:var(--text-mid); width:44px; flex-shrink:0; }
  .bed-status-row select{ flex:1; border:1px solid var(--border); border-radius:8px; padding:8px 10px; font-size:12.5px; font-family:var(--font-body); color:var(--text-dark); }
  .modal-actions{ display:flex; justify-content:flex-end; gap:10px; margin-top:6px; }
  .modal-btn{ padding:9px 16px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; border:1px solid var(--border); }
  .modal-btn.cancel{ background:#fff; color:var(--text-mid); }
  .modal-btn.cancel:hover{ background:#f5f6f5; }
  .modal-btn.confirm{ background:var(--green-btn); color:#fff; border-color:var(--green-btn); }
  .modal-btn.confirm:hover{ background:var(--green-btn-hover); }
  .modal-error{ font-size:12px; color:var(--status-occupied); margin:-10px 0 14px 0; display:none; }

  @media (max-width: 900px){ .stats-row{ grid-template-columns:repeat(2, 1fr); } .sidebar{ display:none; } }
</style>
</head>
<body>

<div class="app">
  <aside class="sidebar">
    <div class="sidebar-logo"><span class="logo-mark"></span> NEST.PH</div>
    <div class="sidebar-section-label">Quick Access</div>
    <ul class="nav-list">
      <li class="nav-item" data-href="{{ route('dashboard') }}" onclick="window.location.href=this.dataset.href"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg></span>Dashboard</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h5l2-2h4l2 2h5v12H3z"/></svg></span>Tenant Manager</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></span>Billing and Payments</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="5" r="2"/><path d="M12 7v6M8 21l4-8 4 8M6 12l6 1 6-1"/></svg></span>Delinquency</li>
      <li class="nav-item active"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="7" height="7"/><rect x="14" y="4" width="7" height="7"/><rect x="3" y="15" width="7" height="7"/><rect x="14" y="15" width="7" height="7"/></svg></span>Vacancy Monitor</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h18v10H3z"/><path d="M3 12h18"/></svg></span>Tickets</li>
      <li class="nav-item" data-href="{{ route('vr.index') }}" onclick="window.location.href=this.dataset.href"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 3v18M16 3v18"/></svg></span>VR Management</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg></span>Lease Management</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/></svg></span>Admin Privileges</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 21V9l8-6 8 6v12"/><path d="M9 21v-6h6v6"/></svg></span>Business Information</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M6 10h4M6 14h2"/></svg></span>Dormitory Profile</li>
    </ul>
    <div class="sidebar-footer"><div class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.4 5.6a9 9 0 11-12.8 0M12 3v8"/></svg></span>Log Out</div></div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div class="hamburger"><span></span><span></span><span></span></div>
      <div class="search-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg><input type="text" placeholder="Search"></div>
      <div class="topbar-right">
        <div class="topbar-icon avatar-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
        <div class="topbar-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 00.3 1.9l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.7 1.7 0 00-1.9-.3 1.7 1.7 0 00-1 1.6V21a2 2 0 11-4 0v-.2a1.7 1.7 0 00-1-1.5 1.7 1.7 0 00-1.9.3l-.1.1a2 2 0 11-2.8-2.8l.1-.1a1.7 1.7 0 00.3-1.9 1.7 1.7 0 00-1.6-1H3a2 2 0 110-4h.2a1.7 1.7 0 001.5-1 1.7 1.7 0 00-.3-1.9l-.1-.1a2 2 0 112.8-2.8l.1.1a1.7 1.7 0 001.9.3H9a1.7 1.7 0 001-1.6V3a2 2 0 114 0v.2a1.7 1.7 0 001 1.6 1.7 1.7 0 001.9-.3l.1-.1a2 2 0 112.8 2.8l-.1.1a1.7 1.7 0 00-.3 1.9V9a1.7 1.7 0 001.6 1H21a2 2 0 110 4h-.2a1.7 1.7 0 00-1.6 1z"/></svg></div>
        <div class="topbar-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 00-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 01-3.4 0"/></svg><span class="badge">1</span></div>
      </div>
    </div>

    <div class="content">
      <div class="page-head">
        <div class="back-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
        <h1>Vacancy Monitoring</h1>
        <div class="page-head-tools">
          <div class="head-tool-btn"><svg viewBox="0 0 24 24" fill="currentColor"><circle cx="5" cy="5" r="2"/><circle cx="12" cy="5" r="2"/><circle cx="19" cy="5" r="2"/><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/><circle cx="5" cy="19" r="2"/><circle cx="12" cy="19" r="2"/><circle cx="19" cy="19" r="2"/></svg></div>
          <div class="head-tool-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg></div>
          <div class="head-tool-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg></div>
        </div>
      </div>

      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-icon bedspaces"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 18v-6a2 2 0 012-2h16a2 2 0 012 2v6"/><path d="M2 18h20M4 10V6a2 2 0 012-2h4a2 2 0 012 2v4"/></svg></div>
          <div><div class="stat-label">Total Bedspaces</div><div class="stat-value" id="statTotal">{{ $stats['total'] }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon occupied"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg></div>
          <div><div class="stat-label">Occupied</div><div class="stat-value" id="statOccupied">{{ $stats['occupied'] }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon vacant"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 18v-6a2 2 0 012-2h16a2 2 0 012 2v6"/><path d="M2 18h20M4 10V6a2 2 0 012-2h4a2 2 0 012 2v4"/></svg></div>
          <div><div class="stat-label">Vacant</div><div class="stat-value" id="statVacant">{{ $stats['vacant'] }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon maintenance"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a4 4 0 01-5.4 5.4L4 17v3h3l5.3-5.3a4 4 0 015.4-5.4l-2.5 2.5-2-2z"/></svg></div>
          <div><div class="stat-label">Maintenance</div><div class="stat-value" id="statMaintenance">{{ $stats['maintenance'] }}</div></div>
        </div>
      </div>

      <div class="filter-card">
        <div class="filter-group"><label>Floor</label><div class="filter-select"><span>All Floors</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg></div></div>
        <div class="filter-group"><label>Room Type</label><div class="filter-select"><span>All Types</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg></div></div>
        <div class="filter-group"><label>Status</label><div class="filter-select"><span>All Status</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg></div></div>
        <button class="add-room-btn" id="addFloorBtn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
          ADD FLOOR
        </button>
      </div>

      <div class="map-card">
        <div class="map-head">
          <div><h2>Room / Bed Map</h2><p>Click a room to view bedspace details.</p></div>
          <div class="legend">
            <div class="legend-item"><span class="legend-dot occupied"></span>Occupied</div>
            <div class="legend-item"><span class="legend-dot vacant"></span>Vacant</div>
            <div class="legend-item"><span class="legend-dot maintenance"></span>Maintenance</div>
          </div>
        </div>
        <div id="floors"></div>
      </div>
    </div>
  </div>
</div>

<!-- ADD FLOOR MODAL (client-side grouping label only — becomes real once a room is saved under it) -->
<div class="modal-overlay" id="addFloorModal">
  <div class="modal-box">
    <h3 class="modal-title">Add Floor</h3>
    <p class="modal-sub">Name a new floor group. It's saved to the database as soon as you add the first room to it.</p>
    <div class="modal-field"><label>Floor Label</label><input type="text" id="newFloorName" placeholder="e.g. 5"></div>
    <p class="modal-error" id="floorModalError"></p>
    <div class="modal-actions">
      <div class="modal-btn cancel" id="cancelAddFloor">Cancel</div>
      <div class="modal-btn confirm" id="confirmAddFloor">Add Floor</div>
    </div>
  </div>
</div>

<!-- ADD / EDIT ROOM MODAL -->
<div class="modal-overlay" id="addRoomModal">
  <div class="modal-box">
    <h3 class="modal-title" id="roomModalTitle">Add Room</h3>
    <p class="modal-sub" id="addRoomFloorLabel">Adding a room to Floor 1</p>

    <div class="modal-row">
      <div class="modal-field"><label>Room No.</label><input type="text" id="newRoomNo" placeholder="e.g. A105"></div>
      <div class="modal-field"><label>Floor</label><input type="text" id="newRoomFloor" placeholder="e.g. 1"></div>
    </div>
    <div class="modal-row">
      <div class="modal-field"><label>Room Type</label><input type="text" id="newRoomType" placeholder="e.g. Standard"></div>
      <div class="modal-field"><label>Monthly Rate</label><input type="number" id="newRoomRate" min="0" step="0.01" placeholder="0.00"></div>
    </div>
    <div class="modal-field"><label>Number of Beds</label><input type="number" id="newRoomBedCount" min="1" max="8" value="2"></div>
    <div class="modal-field"><label>Bed Status</label><div class="bed-status-rows" id="bedStatusRows"></div></div>

    <p class="modal-error" id="roomModalError"></p>
    <div class="modal-actions">
      <div class="modal-btn cancel" id="cancelAddRoom">Cancel</div>
      <div class="modal-btn confirm" id="confirmAddRoom">Add Room</div>
    </div>
  </div>
</div>

<script id="floor-groups-data" type="application/json">{!! json_encode($floorGroups) !!}</script>
<script>
  const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

  const routes = {
    rooms: {
      store: "{{ route('vacancy.rooms.store') }}",
      update: (id) => `{{ url('/vacancy/rooms') }}/${id}`,
      destroy: (id) => `{{ url('/vacancy/rooms') }}/${id}`,
    },
    beds: {
      update: (id) => `{{ url('/vacancy/beds') }}/${id}`,
    },
    floors: {
      destroy: (label) => `{{ url('/vacancy/floors') }}/${label}`,
    },
  };

  async function apiFetch(url, method, body){
    const res = await fetch(url, {
      method,
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
      body: body ? JSON.stringify(body) : undefined,
    });
    if(!res.ok){
      const errBody = await res.json().catch(() => ({}));
      const message = errBody.message || (errBody.errors ? Object.values(errBody.errors)[0][0] : `Request failed (${res.status})`);
      throw new Error(message);
    }
    return res.status === 204 ? null : res.json();
  }

  // Server-rendered initial data: rooms grouped by their `floor` column.
  // Read from a JSON script tag instead of inlining the Blade json helper
  // directly, so the JS/TS language service doesn't misread the leading "@" as a decorator.
  let floorGroups = JSON.parse(document.getElementById('floor-groups-data').textContent);

  const container = document.getElementById('floors');
  let openFloorLabel = floorGroups.length ? String(floorGroups[0].label).trim() : null;

  let roomModalMode = 'create';
  let roomModalFloorLabel = null;
  let roomModalRoomId = null;

  const statusCycle = ['vacant', 'occupied', 'maintenance'];

  function normalizeFloorLabel(label){
    return String(label ?? '').trim();
  }

  function findFloorGroup(label){
    return floorGroups.find(g => normalizeFloorLabel(g.label) === normalizeFloorLabel(label));
  }

  function computeStats(){
    let total = 0, occupied = 0, vacant = 0, maintenance = 0;
    floorGroups.forEach(group => {
      group.rooms.forEach(room => {
        room.beds.forEach(bed => {
          total++;
          if(bed.status === 'occupied') occupied++;
          else if(bed.status === 'vacant') vacant++;
          else if(bed.status === 'maintenance') maintenance++;
        });
      });
    });
    document.getElementById('statTotal').textContent = total;
    document.getElementById('statOccupied').textContent = occupied;
    document.getElementById('statVacant').textContent = vacant;
    document.getElementById('statMaintenance').textContent = maintenance;
  }

  function findRoom(floorLabel, roomId){
    const group = findFloorGroup(floorLabel);
    return group ? group.rooms.find(r => r.id === roomId) : null;
  }

  function findBed(bedId){
    for(const group of floorGroups){
      for(const room of group.rooms){
        const bed = room.beds.find(b => b.id === bedId);
        if(bed) return bed;
      }
    }
    return null;
  }

  function renderFloors(){
    container.innerHTML = '';

    floorGroups.forEach(group => {
      const block = document.createElement('div');
      block.className = 'floor-block';
      if(normalizeFloorLabel(group.label) === normalizeFloorLabel(openFloorLabel)) block.classList.add('open');

      const header = document.createElement('div');
      header.className = 'floor-header';
      header.innerHTML = `
        <div class="floor-header-left"><span>Floor ${group.label}</span></div>
        <div class="floor-header-right">
          <button class="floor-add-room-btn" data-floor="${group.label}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            ADD ROOM
          </button>
          <button class="delete-floor-btn" data-floor="${group.label}" title="Remove floor group">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 6l12 12M18 6L6 18"/></svg>
          </button>
          <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
        </div>
      `;

      header.addEventListener('click', (e) => {
        if(e.target.closest('.floor-add-room-btn') || e.target.closest('.delete-floor-btn')) return;
        openFloorLabel = (normalizeFloorLabel(openFloorLabel) === normalizeFloorLabel(group.label)) ? null : normalizeFloorLabel(group.label);
        renderFloors();
      });

      block.appendChild(header);

      if(group.rooms.length === 0){
        const empty = document.createElement('div');
        empty.className = 'floor-empty';
        empty.textContent = 'No rooms added yet for this floor.';
        block.appendChild(empty);
      } else {
        const roomsGrid = document.createElement('div');
        roomsGrid.className = 'floor-rooms';

        group.rooms.forEach(room => {
          const card = document.createElement('div');
          card.className = 'room-card';

          let bedsHtml = '';
          room.beds.forEach(bed => {
            bedsHtml += `
              <div class="bed-row" data-bed-id="${bed.id}">
                <span class="bed-swatch ${bed.status}"></span>
                <span class="bed-label">${bed.bed_label}</span>
              </div>
            `;
          });

          card.innerHTML = `
            <div class="room-card-head">
              <div>
                <div class="room-title">Room ${room.room_no}</div>
                <div class="room-meta">${room.room_type ? room.room_type + ' · ' : ''}₱${Number(room.monthly_rate).toLocaleString(undefined, {minimumFractionDigits:2})}</div>
              </div>
              <div class="room-actions">
                <button class="room-action-btn edit" data-floor="${group.label}" data-room-id="${room.id}" title="Edit room">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4z"/></svg>
                </button>
                <button class="room-action-btn delete" data-floor="${group.label}" data-room-id="${room.id}" title="Delete room">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M6 6l1 14h10l1-14"/></svg>
                </button>
              </div>
            </div>
            <span class="room-status-badge ${room.status}">${room.status}</span>
            ${bedsHtml}
          `;
          roomsGrid.appendChild(card);
        });

        block.appendChild(roomsGrid);
      }

      container.appendChild(block);
    });

    container.querySelectorAll('.floor-add-room-btn').forEach(btn => {
      btn.addEventListener('click', () => openAddRoomModal(btn.dataset.floor));
    });

    container.querySelectorAll('.delete-floor-btn').forEach(btn => {
      btn.addEventListener('click', async () => {
        const label = btn.dataset.floor;
        const group = findFloorGroup(label);

        const confirmMsg = group.rooms.length > 0
          ? `This deletes Floor ${label} and all ${group.rooms.length} room(s) on it permanently. Continue?`
          : `Delete Floor ${label}?`;
        if(!confirm(confirmMsg)) return;

        try {
          await apiFetch(routes.floors.destroy(label), 'DELETE');
        } catch(err){
          alert(err.message);
          return;
        }

        floorGroups = floorGroups.filter(g => g.label !== label);
        if(openFloorLabel === label) openFloorLabel = null;
        renderFloors();
        computeStats();
      });
    });

    container.querySelectorAll('.room-action-btn.edit').forEach(btn => {
      btn.addEventListener('click', () => openEditRoomModal(btn.dataset.floor, parseInt(btn.dataset.roomId)));
    });

    container.querySelectorAll('.room-action-btn.delete').forEach(btn => {
      btn.addEventListener('click', async () => {
        const label = btn.dataset.floor;
        const roomId = parseInt(btn.dataset.roomId);
        const room = findRoom(label, roomId);
        if(!confirm(`Delete Room ${room.room_no}?`)) return;

        try {
          await apiFetch(routes.rooms.destroy(roomId), 'DELETE');
          const group = findFloorGroup(label);
          group.rooms = group.rooms.filter(r => r.id !== roomId);
          renderFloors();
          computeStats();
        } catch(err){
          alert(err.message);
        }
      });
    });

    container.querySelectorAll('.bed-row').forEach(row => {
      row.addEventListener('click', async () => {
        const bedId = parseInt(row.dataset.bedId);
        const bed = findBed(bedId);
        if(!bed) return;

        const nextStatus = statusCycle[(statusCycle.indexOf(bed.status) + 1) % statusCycle.length];

        try {
          const updated = await apiFetch(routes.beds.update(bedId), 'PATCH', { status: nextStatus });
          bed.status = updated.status;
          // Re-sync the room's own status locally to match server behavior
          for(const group of floorGroups){
            for(const room of group.rooms){
              if(room.beds.some(b => b.id === bedId)){
                const hasVacant = room.beds.some(b => b.status === 'vacant');
                if(room.status !== 'maintenance') room.status = hasVacant ? 'available' : 'full';
              }
            }
          }
          renderFloors();
          computeStats();
        } catch(err){
          alert(err.message);
        }
      });
    });
  }

  /* ---------- ADD FLOOR MODAL ---------- */
  const addFloorModal = document.getElementById('addFloorModal');
  const floorModalError = document.getElementById('floorModalError');
  const newFloorNameInput = document.getElementById('newFloorName');

  document.getElementById('addFloorBtn').addEventListener('click', () => {
    floorModalError.style.display = 'none';
    newFloorNameInput.value = '';
    addFloorModal.classList.add('open');
    newFloorNameInput.focus();
  });

  document.getElementById('cancelAddFloor').addEventListener('click', () => addFloorModal.classList.remove('open'));

  document.getElementById('confirmAddFloor').addEventListener('click', () => {
    const label = newFloorNameInput.value.trim();
    if(!label){
      floorModalError.textContent = 'Floor label is required.';
      floorModalError.style.display = 'block';
      return;
    }
    const normalizedLabel = String(label).trim();
    if(floorGroups.some(g => String(g.label).trim() === normalizedLabel)){
      floorModalError.textContent = 'That floor already exists.';
      floorModalError.style.display = 'block';
      return;
    }
    floorGroups.push({ label: normalizedLabel, rooms: [] });
    openFloorLabel = normalizedLabel;
    addFloorModal.classList.remove('open');
    renderFloors();
  });

  /* ---------- ADD / EDIT ROOM MODAL ---------- */
  const addRoomModal = document.getElementById('addRoomModal');
  const roomModalTitle = document.getElementById('roomModalTitle');
  const addRoomFloorLabel = document.getElementById('addRoomFloorLabel');
  const roomModalError = document.getElementById('roomModalError');
  const newRoomNoInput = document.getElementById('newRoomNo');
  const newRoomFloorInput = document.getElementById('newRoomFloor');
  const newRoomTypeInput = document.getElementById('newRoomType');
  const newRoomRateInput = document.getElementById('newRoomRate');
  const newRoomBedCountInput = document.getElementById('newRoomBedCount');
  const bedStatusRows = document.getElementById('bedStatusRows');
  const confirmAddRoomBtn = document.getElementById('confirmAddRoom');

  function buildBedStatusRows(existingBeds){
    const count = Math.max(1, Math.min(8, parseInt(newRoomBedCountInput.value) || 1));
    bedStatusRows.innerHTML = '';
    for(let i = 1; i <= count; i++){
      const preset = (existingBeds && existingBeds[i-1]) ? existingBeds[i-1].status : 'vacant';
      const row = document.createElement('div');
      row.className = 'bed-status-row';
      row.innerHTML = `
        <span class="bed-index">Bed ${i}</span>
        <select data-bed-index="${i}">
          <option value="vacant" ${preset === 'vacant' ? 'selected' : ''}>Vacant</option>
          <option value="occupied" ${preset === 'occupied' ? 'selected' : ''}>Occupied</option>
          <option value="maintenance" ${preset === 'maintenance' ? 'selected' : ''}>Maintenance</option>
        </select>
      `;
      bedStatusRows.appendChild(row);
    }
  }

  newRoomBedCountInput.addEventListener('input', () => buildBedStatusRows());

  function openAddRoomModal(floorLabel){
    const normalizedFloorLabel = String(floorLabel).trim();
    roomModalMode = 'create';
    roomModalFloorLabel = normalizedFloorLabel;
    roomModalRoomId = null;
    roomModalTitle.textContent = 'Add Room';
    confirmAddRoomBtn.textContent = 'Add Room';
    roomModalError.style.display = 'none';
    addRoomFloorLabel.textContent = `Adding a room to Floor ${normalizedFloorLabel}`;
    newRoomNoInput.value = '';
    newRoomFloorInput.value = normalizedFloorLabel;
    newRoomTypeInput.value = '';
    newRoomRateInput.value = '';
    newRoomBedCountInput.value = 2;
    buildBedStatusRows();
    addRoomModal.classList.add('open');
    newRoomNoInput.focus();
  }

  function openEditRoomModal(floorLabel, roomId){
    const room = findRoom(floorLabel, roomId);
    roomModalMode = 'edit';
    roomModalFloorLabel = normalizeFloorLabel(floorLabel);
    roomModalRoomId = roomId;
    roomModalTitle.textContent = 'Edit Room';
    confirmAddRoomBtn.textContent = 'Save Changes';
    roomModalError.style.display = 'none';
    addRoomFloorLabel.textContent = `Editing Room ${room.room_no}`;
    newRoomNoInput.value = room.room_no;
    newRoomFloorInput.value = room.floor;
    newRoomTypeInput.value = room.room_type || '';
    newRoomRateInput.value = room.monthly_rate;
    newRoomBedCountInput.value = room.beds.length;
    buildBedStatusRows(room.beds);
    addRoomModal.classList.add('open');
    newRoomNoInput.focus();
  }

  document.getElementById('cancelAddRoom').addEventListener('click', () => addRoomModal.classList.remove('open'));

  confirmAddRoomBtn.addEventListener('click', async () => {
    const room_no = newRoomNoInput.value.trim();
    const floor = String(newRoomFloorInput.value).trim();
    const room_type = newRoomTypeInput.value.trim();
    const monthly_rate = parseFloat(newRoomRateInput.value) || 0;
    const bed_statuses = Array.from(bedStatusRows.querySelectorAll('select')).map(sel => sel.value);

    if(!room_no || !floor){
      roomModalError.textContent = 'Room No. and Floor are required.';
      roomModalError.style.display = 'block';
      return;
    }

    const payload = { room_no, floor, room_type, monthly_rate, bed_count: bed_statuses.length, bed_statuses };

    try {
      if(roomModalMode === 'create'){
        const normalizedFloor = String(floor).trim();
        const created = await apiFetch(routes.rooms.store, 'POST', { ...payload, floor: normalizedFloor });
        let group = findFloorGroup(normalizedFloor);
        if(!group){
          group = { label: normalizedFloor, rooms: [] };
          floorGroups.push(group);
        }
        group.rooms.push({
          id: created.id, room_no: created.room_no, floor: created.floor,
          room_type: created.room_type, monthly_rate: created.monthly_rate, status: created.status,
          beds: created.beds.map(b => ({ id: b.id, bed_label: b.bed_label, status: b.status })),
        });
        openFloorLabel = normalizedFloor;
      } else {
        const updated = await apiFetch(routes.rooms.update(roomModalRoomId), 'PUT', payload);

        // remove from old floor group
        const oldGroup = findFloorGroup(roomModalFloorLabel);
        oldGroup.rooms = oldGroup.rooms.filter(r => r.id !== roomModalRoomId);

        // add to (possibly new) floor group
        let newGroup = findFloorGroup(updated.floor);
        if(!newGroup){
          newGroup = { label: updated.floor, rooms: [] };
          floorGroups.push(newGroup);
        }
        newGroup.rooms.push({
          id: updated.id, room_no: updated.room_no, floor: updated.floor,
          room_type: updated.room_type, monthly_rate: updated.monthly_rate, status: updated.status,
          beds: updated.beds.map(b => ({ id: b.id, bed_label: b.bed_label, status: b.status })),
        });
        openFloorLabel = updated.floor;
      }
      addRoomModal.classList.remove('open');
      renderFloors();
      computeStats();
    } catch(err){
      roomModalError.textContent = err.message;
      roomModalError.style.display = 'block';
    }
  });

  [addFloorModal, addRoomModal].forEach(modal => {
    modal.addEventListener('click', (e) => { if(e.target === modal) modal.classList.remove('open'); });
  });

  renderFloors();
  computeStats();
</script>

</body>
</html>