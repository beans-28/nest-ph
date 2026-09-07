<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH — Admin Privileges</title>
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
  .search-box{ position:relative; flex:1; max-width:420px; display:flex; align-items:center; gap:8px; background:rgba(255,255,255,0.92); border-radius:8px; padding:9px 14px; }
  .search-box svg{ width:15px; height:15px; color:#8a9690; flex-shrink:0; }
  .search-box input{ border:none; outline:none; background:transparent; font-size:13.5px; width:100%; color:var(--text-dark); }

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

  .content{ padding:28px 32px 48px 32px; flex:1; }
  .page-head{ display:flex; align-items:center; gap:12px; margin-bottom:22px; }
  .back-arrow{ width:34px; height:34px; border-radius:8px; background:var(--card-bg); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-mid); }
  .page-head h1{ font-size:22px; font-weight:700; margin:0; color:var(--green-accent); }
  .page-head .spacer{ flex:1; }

  .btn{ font-size:12.5px; font-weight:600; padding:10px 18px; border-radius:7px; border:1px solid var(--border); background:#fff; color:var(--text-mid); cursor:pointer; font-family:var(--font-body); }
  .btn:hover:not(:disabled){ background:#f7f9f7; }
  .btn:disabled{ opacity:.5; cursor:not-allowed; }
  .btn{ border-radius:5px; }
  .btn.primary{ background:var(--green-btn); border-color:var(--green-btn); color:#fff; }
  .btn.primary:hover:not(:disabled){ background:var(--green-btn-hover); }
  .btn.warn{ background:#fbeceb; border-color:#f2cfcc; color:var(--status-occupied); }
  .btn.warn:hover:not(:disabled){ background:#f6d9d7; }
  .btn.sm{ padding:7px 13px; font-size:11.5px; }

  /* One flat strip, not two competing icon-in-a-box cards. A single
     hairline divider does the separating instead of two shadowed boxes. */
  .stats-row{ display:flex; background:var(--card-bg); border:1px solid var(--border); border-radius:6px; margin-bottom:24px; }
  .stat-block{ flex:1; padding:16px 24px; }
  .stat-block + .stat-block{ border-left:1px solid var(--border); }
  .stat-label{ font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.5px; color:var(--text-light); margin-bottom:4px; }
  .stat-value{ font-size:26px; font-weight:700; color:var(--text-dark); line-height:1.2; font-variant-numeric:tabular-nums; }

  .filters-row{ display:flex; gap:12px; margin-bottom:18px; flex-wrap:wrap; align-items:center; }
  .filters-row .search-input{ flex:1; min-width:220px; border:1px solid var(--border); border-radius:8px; padding:11px 14px; font-size:13px; font-family:var(--font-body); }
  .filters-row select{ border:1px solid var(--border); border-radius:8px; padding:11px 14px; font-size:13px; font-family:var(--font-body); background:#fff; color:var(--text-dark); }

  .table-panel{ background:var(--card-bg); border:1px solid var(--border); border-radius:6px; overflow-x:auto; }
  table{ width:100%; border-collapse:collapse; min-width:820px; }
  thead th{ text-align:left; font-size:12px; font-weight:700; color:var(--text-mid); padding:14px 20px; border-bottom:1px solid var(--border); background:#fbfcfb; white-space:nowrap; }
  tbody td{ padding:14px 20px; font-size:13px; border-bottom:1px solid #f0f2f0; vertical-align:middle; }
  tbody tr:last-child td{ border-bottom:none; }
  tbody tr:hover{ background:#fbfcfb; }
  .admin-cell{ display:flex; align-items:center; gap:10px; }
  /* No random per-row rainbow — every avatar is the same tone family as
     the brand, just varied one step in lightness so rows stay tellable
     apart without introducing new hues. */
  .avatar{ width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:11.5px; color:var(--green-dark); background:var(--status-vacant-bg); border:1px solid #c3e3c6; flex-shrink:0; }
  .admin-name{ font-weight:600; }
  .you-tag{ font-size:10px; font-weight:600; color:var(--text-light); margin-left:6px; text-transform:uppercase; letter-spacing:.3px; }
  .status-text{ font-weight:600; font-size:12.5px; }
  .status-text.active{ color:var(--green-accent); }
  .status-text.inactive{ color:var(--text-light); }

  /* Privileges read as a short line of text, not a row of colored pills.
     manage_users (the "Owner" gate) is the one thing worth calling out,
     done with weight + a dot, not a second/third hue. */
  .privilege-line{ font-size:12.5px; color:var(--text-mid); max-width:300px; line-height:1.6; }
  .privilege-line .owner-flag{ display:inline-flex; align-items:center; gap:5px; font-weight:700; color:var(--text-dark); }
  .privilege-line .owner-flag::before{ content:''; width:6px; height:6px; border-radius:50%; background:var(--green-accent); }
  .privilege-line .none{ color:var(--text-light); font-style:italic; }

  .row-actions{ display:flex; align-items:center; gap:14px; }
  .row-actions .btn{ padding:7px 14px; }
  /* Revoke is destructive but secondary to Manage — a text action, not a
     second competing button, so there's a clear primary per row. */
  .revoke-link{ background:none; border:none; padding:0; font-size:12.5px; font-weight:600; color:var(--status-occupied); cursor:pointer; font-family:var(--font-body); }
  .revoke-link:hover:not(:disabled){ text-decoration:underline; }
  .revoke-link:disabled{ color:var(--text-light); cursor:not-allowed; }

  .empty-row td{ text-align:center; color:var(--text-light); font-style:italic; padding:34px; }

  .modal-overlay{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:60; align-items:center; justify-content:center; padding:20px; }
  .modal-overlay.open{ display:flex; }
  .modal-box{ background:#fff; border-radius:14px; width:100%; max-width:520px; max-height:90vh; overflow-y:auto; }
  .modal-head{ padding:20px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; }
  .modal-head h2{ font-size:16px; font-weight:700; margin:0; }
  .modal-body{ padding:22px 24px; }
  .modal-body .fld{ display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
  .modal-body label{ font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-mid); }
  .modal-body input{ border:1px solid var(--border); border-radius:8px; padding:10px 13px; font-size:13px; font-family:var(--font-body); width:100%; }
  .modal-error{ display:none; background:#fbeceb; border:1px solid #f2cfcc; color:var(--status-occupied); border-radius:8px; padding:10px 13px; font-size:12.5px; margin-bottom:14px; }
  .modal-error.visible{ display:block; }
  .modal-actions{ display:flex; gap:10px; padding:0 24px 24px 24px; }
  .modal-section-title{ font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--green-accent); margin:4px 0 12px 0; }
  .checkbox-grid{ display:grid; grid-template-columns:1fr 1fr; gap:10px; }
  .checkbox-fld{ display:flex; align-items:center; gap:8px; border:1px solid var(--border); border-radius:8px; padding:10px 12px; font-size:12.5px; }
  .checkbox-fld input{ width:auto; flex-shrink:0; }
  .checkbox-fld.disabled{ opacity:.55; }
  .password-box{ background:#fbfcfb; border:1px dashed var(--border); border-radius:8px; padding:16px; text-align:center; margin:0 24px 24px 24px; }
  .password-box .pw{ font-size:20px; font-weight:700; letter-spacing:1px; color:var(--green-accent); margin:8px 0; font-family:monospace; }
  .password-box .note{ font-size:11.5px; color:var(--text-light); }

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
      <li class="nav-item" data-href="{{ route('contracts.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg></span><span class="label">Lease Management</span></li>
      <li class="nav-item active" data-href="{{ route('admin-privileges.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l8 4v6c0 5-3.5 9-8 10-4.5-1-8-5-8-10V6l8-4z"/><path d="M9 12l2 2 4-4"/></svg></span><span class="label">Admin Privileges</span></li>
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
        <h1>Admin Privileges</h1>
      </div>

      <div class="stats-row">
        <div class="stat-block">
          <div class="stat-label">Active Admins</div>
          <div class="stat-value" id="statActiveAdmins">{{ $activeAdminsCount }}</div>
        </div>
        <div class="stat-block">
          <div class="stat-label">Privileges Assigned</div>
          <div class="stat-value" id="statTotalGrants">{{ $totalGrants }}</div>
        </div>
      </div>

      <div class="filters-row">
        <input type="text" class="search-input" id="adminSearchInput" placeholder="Search name or email">
        <select id="statusFilter">
          <option value="">All Statuses</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
        <button class="btn primary" id="openAddModalBtn" style="margin-left:auto;">+ Add Admin Account</button>
      </div>

      <div class="table-panel">
        <table>
          <thead>
            <tr>
              <th>Admin</th><th>Email</th><th>Privileges</th><th>Status</th><th>Granted</th><th>Actions</th>
            </tr>
          </thead>
          <tbody id="tableBody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- ===== Add Admin Account modal ===== -->
<div class="modal-overlay" id="addModal">
  <div class="modal-box">
    <div class="modal-head"><h2>Add Admin Account</h2></div>
    <div class="modal-body">
      <div class="modal-error" id="addModalError"></div>
      <div class="fld"><label for="addName">Full Name</label><input type="text" id="addName"></div>
      <div class="fld"><label for="addEmail">Email</label><input type="email" id="addEmail"></div>
      <div class="modal-section-title">Starting Privileges</div>
      <div class="checkbox-grid" id="addPrivilegeGrid"></div>
    </div>
    <div class="modal-actions">
      <button class="btn primary" id="submitAddBtn" style="flex:1;">Create Account</button>
      <button class="btn" id="cancelAddBtn">Cancel</button>
    </div>
  </div>
</div>

<!-- ===== Manage Privileges modal ===== -->
<div class="modal-overlay" id="privModal">
  <div class="modal-box">
    <div class="modal-head"><h2 id="privModalTitle">Manage Privileges</h2></div>
    <div class="modal-body">
      <div class="modal-error" id="privModalError"></div>
      <div class="checkbox-grid" id="privPrivilegeGrid"></div>
    </div>
    <div class="modal-actions">
      <button class="btn primary" id="submitPrivBtn" style="flex:1;">Save Changes</button>
      <button class="btn" id="cancelPrivBtn">Cancel</button>
    </div>
  </div>
</div>

<!-- ===== Temporary password reveal ===== -->
<div class="modal-overlay" id="pwModal">
  <div class="modal-box">
    <div class="modal-head"><h2>Admin Account Created</h2></div>
    <div class="password-box">
      <div id="pwEmail"></div>
      <div class="pw" id="pwValue"></div>
      <div class="note">Share this password with the new admin. They should change it after logging in, from Account Settings.</div>
      <button class="btn sm" id="copyPwBtn" style="margin-top:12px;">Copy Password</button>
    </div>
    <div class="modal-actions">
      <button class="btn primary" id="closePwBtn" style="flex:1;">Done</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script type="application/json" id="admins-data">{!! json_encode($admins) !!}</script>
<script type="application/json" id="privilege-options-data">{!! json_encode($privilegeOptions) !!}</script>

<script>
(function(){
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  const CURRENT_USER_ID = {{ (int) $currentUserId }};
  let admins = JSON.parse(document.getElementById('admins-data').textContent);
  const PRIVILEGE_OPTIONS = JSON.parse(document.getElementById('privilege-options-data').textContent);

  let search = '';
  let statusFilter = '';
  let privModalUserId = null;

  const $ = id => document.getElementById(id);
  // One tone family (the brand green), not a rainbow — see CSS .avatar.

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
    setTimeout(() => el.classList.remove('visible'), 3000);
  }

  function esc(s){
    const d = document.createElement('div');
    d.textContent = s ?? '';
    return d.innerHTML;
  }

  function initials(name){
    return (name || '?').trim().split(/\s+/).map(w => w[0]).slice(0,2).join('').toUpperCase();
  }

  function formatDate(iso){
    if(!iso) return '—';
    const d = new Date(iso);
    return d.toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' });
  }

  async function api(url, options = {}){
    const headers = Object.assign({ 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' }, options.headers || {});
    const res = await fetch(url, Object.assign({}, options, { headers }));
    const body = await res.json().catch(() => ({}));
    if(!res.ok){
      throw new Error(body.message || `Request failed (${res.status})`);
    }
    return body;
  }

  function privilegeCheckboxGrid(containerId, selected, opts = {}){
    const disableManageUsers = !!opts.disableManageUsers;
    const el = $(containerId);
    el.innerHTML = Object.keys(PRIVILEGE_OPTIONS).map(key => {
      const checked = selected.includes(key);
      const locked = disableManageUsers && key === 'manage_users';
      return `
        <label class="checkbox-fld ${locked ? 'disabled' : ''}">
          <input type="checkbox" value="${key}" ${checked ? 'checked' : ''} ${locked ? 'disabled' : ''}>
          ${esc(PRIVILEGE_OPTIONS[key])}
        </label>`;
    }).join('');
  }

  function selectedPrivileges(containerId){
    return Array.from($(containerId).querySelectorAll('input[type="checkbox"]'))
      .filter(cb => cb.checked)
      .map(cb => cb.value);
  }

  function refreshStats(){
    $('statActiveAdmins').textContent = admins.filter(a => a.is_active).length;
    $('statTotalGrants').textContent = admins.reduce((sum, a) => sum + a.privileges.length, 0);
  }

  function visible(){
    return admins.filter(a => {
      if(statusFilter === 'active' && !a.is_active) return false;
      if(statusFilter === 'inactive' && a.is_active) return false;
      if(!search) return true;
      const hay = `${a.name ?? ''} ${a.email ?? ''}`.toLowerCase();
      return hay.includes(search);
    });
  }

  function renderTable(){
    const list = visible();

    if(list.length === 0){
      $('tableBody').innerHTML = '<tr class="empty-row"><td colspan="6">No admins match this view.</td></tr>';
      return;
    }

    $('tableBody').innerHTML = list.map(a => {
      const isSelf = a.id === CURRENT_USER_ID;
      const privilegeText = a.privileges.length === 0
        ? '<span class="none">No privileges assigned</span>'
        : a.privileges
            .map(p => p === 'manage_users'
              ? `<span class="owner-flag">${esc(PRIVILEGE_OPTIONS[p] || p)}</span>`
              : esc(PRIVILEGE_OPTIONS[p] || p))
            .join(', ');

      return `
        <tr>
          <td>
            <div class="admin-cell">
              <div class="avatar">${esc(initials(a.name))}</div>
              <span class="admin-name">${esc(a.name)}${isSelf ? '<span class="you-tag">You</span>' : ''}</span>
            </div>
          </td>
          <td>${esc(a.email)}</td>
          <td><div class="privilege-line">${privilegeText}</div></td>
          <td><span class="status-text ${a.is_active ? 'active' : 'inactive'}">${a.is_active ? 'Active' : 'Inactive'}</span></td>
          <td>${formatDate(a.granted_at)}</td>
          <td>
            <div class="row-actions">
              <button class="btn sm" data-manage="${a.id}">Manage Privileges</button>
              <button class="revoke-link" data-revoke="${a.id}" ${isSelf ? 'disabled title="You cannot revoke your own access"' : ''}>Revoke</button>
            </div>
          </td>
        </tr>`;
    }).join('');

    $('tableBody').querySelectorAll('[data-manage]').forEach(btn => {
      btn.addEventListener('click', () => openPrivModal(Number(btn.dataset.manage)));
    });
    $('tableBody').querySelectorAll('[data-revoke]:not(:disabled)').forEach(btn => {
      btn.addEventListener('click', () => revokeAdmin(Number(btn.dataset.revoke)));
    });
  }

  $('adminSearchInput').addEventListener('input', function(){
    search = this.value.trim().toLowerCase();
    renderTable();
  });
  $('statusFilter').addEventListener('change', function(){
    statusFilter = this.value;
    renderTable();
  });

  // ===== Add Admin Account =====
  function openAddModal(){
    $('addName').value = '';
    $('addEmail').value = '';
    $('addModalError').classList.remove('visible');
    privilegeCheckboxGrid('addPrivilegeGrid', []);
    $('addModal').classList.add('open');
  }
  $('openAddModalBtn').addEventListener('click', openAddModal);
  $('cancelAddBtn').addEventListener('click', () => $('addModal').classList.remove('open'));

  $('submitAddBtn').addEventListener('click', async function(){
    const name = $('addName').value.trim();
    const email = $('addEmail').value.trim();
    const errEl = $('addModalError');
    errEl.classList.remove('visible');

    if(!name || !email){
      errEl.textContent = 'Please fill in the name and email.';
      errEl.classList.add('visible');
      return;
    }

    this.disabled = true;
    try {
      const result = await api('{{ route('admin-privileges.store') }}', {
        method:'POST',
        headers:{ 'Content-Type':'application/json' },
        body: JSON.stringify({
          name,
          email,
          privileges: selectedPrivileges('addPrivilegeGrid'),
        }),
      });

      admins.push({
        id: result.user.id,
        name: result.user.name,
        email: result.user.email,
        is_active: true,
        privileges: selectedPrivileges('addPrivilegeGrid'),
        granted_at: new Date().toISOString(),
      });

      $('addModal').classList.remove('open');
      refreshStats();
      renderTable();

      $('pwEmail').textContent = result.user.email;
      $('pwValue').textContent = result.temporary_password;
      $('pwModal').classList.add('open');
    } catch(e){
      errEl.textContent = e.message;
      errEl.classList.add('visible');
    }
    this.disabled = false;
  });

  $('closePwBtn').addEventListener('click', () => $('pwModal').classList.remove('open'));
  $('copyPwBtn').addEventListener('click', () => {
    navigator.clipboard?.writeText($('pwValue').textContent);
    toast('Password copied.');
  });

  // ===== Manage Privileges =====
  function openPrivModal(id){
    const admin = admins.find(a => a.id === id);
    if(!admin) return;
    privModalUserId = id;
    $('privModalTitle').textContent = `Manage Privileges — ${admin.name}`;
    $('privModalError').classList.remove('visible');
    privilegeCheckboxGrid('privPrivilegeGrid', admin.privileges, {
      disableManageUsers: id === CURRENT_USER_ID,
    });
    $('privModal').classList.add('open');
  }
  $('cancelPrivBtn').addEventListener('click', () => $('privModal').classList.remove('open'));

  $('submitPrivBtn').addEventListener('click', async function(){
    if(!privModalUserId) return;
    const errEl = $('privModalError');
    errEl.classList.remove('visible');
    this.disabled = true;

    try {
      const selected = selectedPrivileges('privPrivilegeGrid');
      await api(`/admin-privileges/${privModalUserId}/privileges`, {
        method:'PATCH',
        headers:{ 'Content-Type':'application/json' },
        body: JSON.stringify({ privileges: selected }),
      });

      const admin = admins.find(a => a.id === privModalUserId);
      if(admin) admin.privileges = selected;

      $('privModal').classList.remove('open');
      refreshStats();
      renderTable();
      toast('Privileges updated.');
    } catch(e){
      errEl.textContent = e.message;
      errEl.classList.add('visible');
    }
    this.disabled = false;
  });

  // ===== Revoke Access =====
  async function revokeAdmin(id){
    const admin = admins.find(a => a.id === id);
    if(!admin) return;
    if(!confirm(`Revoke admin access for ${admin.name}? They will be signed out of the admin panel.`)) return;

    try {
      await api(`/admin-privileges/${id}/revoke`, { method:'PATCH' });
      admins = admins.filter(a => a.id !== id);
      refreshStats();
      renderTable();
      toast('Admin access revoked.');
    } catch(e){
      toast(e.message, true);
    }
  }

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

  const NEST_PAGES = [
    { label: 'Dashboard', href: '{{ route('dashboard') }}' },
    { label: 'Tenant Manager', href: '{{ route('tenant-manager.index') }}' },
    { label: 'Billing and Payments', href: '{{ route('payments.index') }}' },
    { label: 'Delinquency', href: '{{ route('delinquency.index') }}' },
    { label: 'Vacancy Monitor', href: '{{ route('admin.addfloor') }}' },
    { label: 'Tickets', href: null },
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
    searchResults.querySelectorAll('[data-href]').forEach(el => {
      el.addEventListener('click', () => { window.location.href = el.dataset.href; });
    });
  }

  if (searchInput) {
    searchInput.addEventListener('input', () => renderNestSearchResults(searchInput.value));
    searchInput.addEventListener('focus', () => renderNestSearchResults(searchInput.value));
    document.addEventListener('click', (e) => {
      if (searchBox && !searchBox.contains(e.target)) {
        searchResults.classList.remove('visible');
      }
    });
  }
})();
</script>
</body>
</html>