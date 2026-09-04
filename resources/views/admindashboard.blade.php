<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH — Admin Dashboard</title>
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

  /* ===== Sidebar: logo pinned at top, Log Out pinned at bottom, and only
     the middle nav list scrolls if it doesn't fit (like textbee.dev's
     sidebar). Trying to shrink everything to force it to fit (our previous
     approach) breaks down once there's simply too little vertical room --
     items stop shrinking at some point and start overlapping the footer.
     Letting the nav list scroll is the version that never overlaps or
     clips: it just gains a scrollbar exactly when there isn't room. ===== */
  .sidebar{
    width:220px; flex-shrink:0;
    background:linear-gradient(180deg, var(--green-sidebar-top) 0%, var(--green-sidebar-bottom) 100%);
    color:#eaf0ea; display:flex; flex-direction:column; padding:18px 0;
    position:sticky; top:0; height:100vh; overflow:hidden;
    transition:width 0.2s ease;
  }
  .sidebar-logo{ display:flex; align-items:center; gap:8px; padding:0 20px 18px 20px; font-weight:700; font-size:16px; border-bottom:1px solid rgba(255,255,255,0.12); margin-bottom:12px; white-space:nowrap; flex-shrink:0; }
  .sidebar-logo .logo-mark{ width:16px; height:16px; border:2px solid #eaf0ea; display:inline-block; position:relative; flex-shrink:0; }
  .sidebar-logo .logo-mark::before, .sidebar-logo .logo-mark::after{ content:''; position:absolute; background:#eaf0ea; width:2px; height:12px; top:0; left:5px; }
  .sidebar-section-label{ font-size:10.5px; text-transform:uppercase; letter-spacing:1px; color:rgba(234,240,234,0.55); padding:4px 20px 8px 20px; font-weight:600; white-space:nowrap; flex-shrink:0; }

  /* The scrollable middle section */
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
  .nav-item.active{ background:rgba(255,255,255,0.14); color:#fff; font-weight:600; border-left:3px solid #ffffff; }
  .nav-item .icon svg{ width:15px; height:15px; flex-shrink:0; }
  .sidebar-footer{ padding:12px 20px 0 20px; border-top:1px solid rgba(255,255,255,0.12); margin-top:8px; flex-shrink:0; }

  /* Darker, clearly-a-button Log Out */
  .sidebar-footer .nav-item{ padding:9px 12px; border-radius:8px; background:rgba(0,0,0,0.28); border-left:none; }
  .sidebar-footer .nav-item:hover{ background:rgba(0,0,0,0.42); color:#fff; }

  /* Collapsed state (toggled by the hamburger button) */
  .sidebar.collapsed{ width:64px; }
  .sidebar.collapsed .sidebar-logo{ justify-content:center; padding-left:0; padding-right:0; }
  .sidebar.collapsed .sidebar-logo .logo-text{ display:none; }
  .sidebar.collapsed .sidebar-section-label{ display:none; }
  .sidebar.collapsed .nav-item{ justify-content:center; padding-left:0; padding-right:0; gap:0; }
  .sidebar.collapsed .nav-item .label{ display:none; }
  .sidebar.collapsed .sidebar-footer{ padding-left:10px; padding-right:10px; }
  .sidebar.collapsed .sidebar-footer .nav-item{ padding:9px 0; }

  .main{ flex:1; display:flex; flex-direction:column; min-width:0; }
  .topbar{ display:flex; align-items:center; gap:16px; background:linear-gradient(90deg, var(--green-mid), var(--green-dark)); padding:14px 28px; position:sticky; top:0; z-index:20; }
  .topbar .hamburger{ width:20px; height:16px; display:flex; flex-direction:column; justify-content:space-between; cursor:pointer; }
  .topbar .hamburger span{ display:block; height:2px; background:#eaf0ea; border-radius:2px; }
  .search-box{ position:relative; flex:1; max-width:420px; display:flex; align-items:center; gap:8px; background:rgba(255,255,255,0.92); border-radius:8px; padding:9px 14px; }
  .search-box svg{ width:15px; height:15px; color:#8a9690; flex-shrink:0; }
  .search-box input{ border:none; outline:none; background:transparent; font-size:13.5px; width:100%; color:var(--text-dark); }

  /* Search results dropdown */
  .search-results{ position:absolute; top:calc(100% + 8px); left:0; right:0; background:var(--card-bg); border:1px solid var(--border); border-radius:10px; box-shadow:0 10px 26px rgba(20,30,20,0.14); max-height:280px; overflow-y:auto; z-index:50; display:none; }
  .search-results.visible{ display:block; }
  .search-result-item{ display:flex; align-items:center; justify-content:space-between; gap:10px; padding:10px 14px; font-size:13px; color:var(--text-dark); cursor:pointer; }
  .search-result-item:hover{ background:#f3f6f3; }
  .search-result-item.disabled{ color:var(--text-light); cursor:not-allowed; }
  .search-result-item.disabled:hover{ background:transparent; }
  .search-result-item .tag{ font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; color:var(--status-maintenance); background:var(--status-maintenance-bg); padding:2px 7px; border-radius:20px; flex-shrink:0; }
  .search-empty{ padding:14px; font-size:12.5px; color:var(--text-light); text-align:center; }

  .topbar-right{ margin-left:auto; display:flex; align-items:center; gap:18px; }
  .topbar-icon{ width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; color:#eaf0ea; cursor:pointer; position:relative; }
  .topbar-icon svg{ width:16px; height:16px; }
  .badge{ position:absolute; top:-3px; right:-3px; background:#e0554f; color:#fff; font-size:9px; font-weight:700; width:15px; height:15px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid var(--green-dark); }
  .avatar-icon{ background:rgba(255,255,255,0.9); color:var(--green-dark); }

  .content{ padding:28px 32px 48px 32px; flex:1; }
  .page-head{ display:flex; align-items:center; gap:12px; margin-bottom:22px; }
  .back-arrow{ width:34px; height:34px; border-radius:8px; background:var(--card-bg); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-mid); flex-shrink:0; }
  .page-head h1{ font-size:20px; font-weight:700; margin:0; color:var(--text-dark); }

  .stats-row{ display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:20px; }
  .stat-card{ background:var(--card-bg); border-radius:12px; border:1px solid var(--border); padding:16px 18px; box-shadow:0 1px 2px rgba(20,30,20,0.03); position:relative; }
  .stat-card:has(.placeholder-tag){ padding-right:76px; }
  .stat-card-head{ display:flex; align-items:center; gap:8px; font-size:12px; color:var(--text-mid); margin-bottom:8px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .stat-icon-dot{ width:20px; height:20px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .stat-icon-dot svg{ width:11px; height:11px; }
  .stat-icon-dot.red{ background:#f7d9d7; color:var(--status-occupied); }
  .stat-icon-dot.blue{ background:#dbe6f7; color:#3f66c9; }
  .stat-icon-dot.teal{ background:#d7f0ec; color:#2f9c85; }
  .stat-icon-dot.green{ background:var(--status-vacant-bg); color:var(--green-accent); }
  .stat-value{ font-size:26px; font-weight:700; color:var(--text-dark); }
  .stat-value .unit{ font-size:15px; font-weight:600; color:var(--text-mid); }
  .stat-sub{ font-size:11px; color:var(--text-light); margin-top:2px; }
  .placeholder-tag{ position:absolute; top:14px; right:16px; font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; color:var(--status-maintenance); background:var(--status-maintenance-bg); padding:3px 7px; border-radius:20px; }

  .alert-banner{ background:linear-gradient(90deg, #f6d6d3, #fbeceb); border:1px solid #f2c3bf; border-radius:12px; padding:14px 20px; display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:20px; }
  .alert-banner-text strong{ display:block; font-size:13.5px; color:#a3372e; }
  .alert-banner-text span{ font-size:12px; color:#8a4a44; }
  .alert-review-btn{ background:#a3372e; color:#fff; border:none; border-radius:8px; padding:9px 16px; font-size:12px; font-weight:600; cursor:pointer; white-space:nowrap; }

  .dash-grid{ display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:start; }
  .dash-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:20px 22px; margin-bottom:20px; }
  .dash-card-head{ display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
  .dash-card-head h2{ font-size:14.5px; font-weight:700; margin:0; }
  .dash-card-head .view-all{ font-size:11.5px; color:var(--green-accent); font-weight:600; cursor:pointer; }

  .occ-row{ display:grid; grid-template-columns:70px 1fr 40px; align-items:center; gap:12px; margin-bottom:14px; }
  .occ-row:last-child{ margin-bottom:0; }
  .occ-label{ font-size:12.5px; color:var(--text-mid); }
  .occ-bar-track{ height:9px; border-radius:20px; background:#e6ebe6; overflow:hidden; }
  .occ-bar-fill{ height:100%; border-radius:20px; background:linear-gradient(90deg, var(--green-accent), var(--status-vacant)); }
  .occ-count{ font-size:11.5px; color:var(--text-light); text-align:right; }

  .ticket-item{ border:1px solid var(--border); border-radius:10px; padding:14px 16px; margin-bottom:12px; }
  .ticket-item:last-child{ margin-bottom:0; }
  .ticket-title{ font-size:13px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; justify-content:between; gap:8px; }
  .ticket-title .time{ margin-left:auto; font-size:11px; font-weight:500; color:var(--text-light); }
  .ticket-desc{ font-size:12px; color:var(--text-mid); margin-top:4px; }
  .ticket-meta{ font-size:11px; color:var(--text-light); margin-top:6px; }

  .activity-tabs{ font-size:11.5px; color:var(--green-accent); font-weight:600; margin-bottom:10px; }
  table.activity-table{ width:100%; border-collapse:collapse; }
  table.activity-table th{ text-align:left; font-size:10.5px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-light); padding:6px 8px; border-bottom:1px solid var(--border); }
  table.activity-table td{ font-size:12px; color:var(--text-dark); padding:8px 8px; border-bottom:1px solid #f0f2f0; }
  table.activity-table td.type{ text-align:right; color:var(--text-mid); }

  .empty-note{ font-size:12px; color:var(--text-light); text-align:center; padding:22px 10px; }
  .empty-note .tag{ display:inline-block; margin-top:8px; font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; color:var(--status-maintenance); background:var(--status-maintenance-bg); padding:3px 8px; border-radius:20px; }
</style>
</head>
<body>
<div class="app">

  <aside class="sidebar" id="sidebar">
    <div class="sidebar-logo"><span class="logo-mark"></span><span class="logo-text">NEST.PH</span></div>
    <div class="sidebar-section-label">Quick Access</div>
    <ul class="nav-list">
      <li class="nav-item active" data-href="{{ route('dashboard') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></span><span class="label">Dashboard</span></li>
      <li class="nav-item" data-href="{{ route('tenant-manager.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></span><span class="label">Tenant Manager</span></li>
      <li class="nav-item" data-href="{{ route('payments.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></span><span class="label">Billing and Payments</span></li>
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
        <div class="topbar-icon avatar-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
      </div>
    </div>

    <div class="content">
      <div class="page-head">
        <div class="back-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
        <h1>Admin Dashboard</h1>
      </div>

      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-card-head"><span class="stat-icon-dot red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></span>Total Tenants</div>
          <div class="stat-value">{{ $totalTenants }}</div>
          <div class="stat-sub">Active tenants</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-head"><span class="stat-icon-dot blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></span>Revenue ({{ now()->format('F') }})</div>
          <div class="stat-value">₱{{ number_format($revenueThisMonth, 0) }}</div>
          <div class="stat-sub">Approved payments this month</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-head"><span class="stat-icon-dot teal"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg></span>Delinquent</div>
          <div class="stat-value">{{ $delinquentCount }}</div>
          <div class="stat-sub">{{ $delinquentCount === 1 ? 'account overdue' : 'accounts overdue' }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-head"><span class="stat-icon-dot green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg></span>Vacancy Rate</div>
          <div class="stat-value">{{ $vacancyRate }}<span class="unit">%</span></div>
          <div class="stat-sub">{{ $vacantBeds }} of {{ $totalBeds }} beds open</div>
        </div>
      </div>

      @if($topDelinquent)
        <div class="alert-banner">
          <div class="alert-banner-text">
            <strong>{{ $delinquentCount }} {{ $delinquentCount === 1 ? 'delinquent account needs' : 'delinquent accounts need' }} attention</strong>
            <span>{{ $topDelinquent['name'] }}{{ $topDelinquent['room_no'] ? ' (Room ' . $topDelinquent['room_no'] . ')' : '' }} is {{ $topDelinquent['days_overdue'] }} days overdue. Immediate escalation recommended.</span>
          </div>
          <a href="{{ route('delinquency.index') }}" class="alert-review-btn" style="text-decoration:none;">Review</a>
        </div>
      @endif

      <div class="dash-grid">
        <div>
          <div class="dash-card">
            <div class="dash-card-head"><h2>Occupancy</h2></div>
            @if($occupancy->isEmpty())
              <div class="empty-note">No floors added yet. Add rooms in Vacancy Monitoring to see occupancy here.</div>
            @else
              @foreach($occupancy as $floor)
                @php $pct = $floor['total'] > 0 ? round(($floor['occupied'] / $floor['total']) * 100) : 0; @endphp
                <div class="occ-row">
                  <div class="occ-label">{{ $floor['label'] }}</div>
                  <div class="occ-bar-track"><div class="occ-bar-fill" style="width:{{ $pct }}%"></div></div>
                  <div class="occ-count">{{ $floor['occupied'] }}/{{ $floor['total'] }}</div>
                </div>
              @endforeach
            @endif
          </div>

          <div class="dash-card">
            <div class="dash-card-head"><h2>Tickets</h2><span class="view-all">View All</span></div>
            <div class="empty-note">
              No tickets yet — the Ticketing module hasn't been built.
              <div class="tag">Coming Soon</div>
            </div>
          </div>
        </div>

        <div>
          <div class="dash-card">
            <div class="dash-card-head"><h2>Recent Activities</h2><a href="{{ route('activity-log.index') }}" class="view-all" style="text-decoration:none;">View All</a></div>
            <div class="activity-tabs">ALL</div>
            @if($recentActivities->isEmpty())
              <div class="empty-note">No activity yet.</div>
            @else
              <table class="activity-table">
                <thead>
                  <tr><th>Date</th><th>Detail</th><th>Type</th></tr>
                </thead>
                <tbody>
                  @foreach($recentActivities as $activity)
                    <tr>
                      <td>{{ \Carbon\Carbon::parse($activity['date'])->format('Y/m/d h:i A') }}</td>
                      <td>{{ $activity['detail'] }}</td>
                      <td class="type">{{ $activity['type'] }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            @endif
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
(function(){
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  document.querySelectorAll('[data-href]').forEach(el => {
    el.addEventListener('click', () => { window.location.href = el.dataset.href; });
  });

  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', async () => {
      await fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken } });
      window.location.href = '/';
    });
  }

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

  // ===== Topbar page search =====
  // Simple client-side search over the site's pages. Pages without a route
  // yet (Tenant Manager, Delinquency, Tickets, Admin Privileges, Dormitory
  // Profile) show up but are marked "Coming Soon" and aren't clickable,
  // matching how they behave in the sidebar itself.
  const PAGES = [
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

  function escapeHtml(str){
    const d = document.createElement('div');
    d.textContent = str ?? '';
    return d.innerHTML;
  }

  function renderSearchResults(query){
    const q = query.trim().toLowerCase();
    if (!q) {
      searchResults.classList.remove('visible');
      searchResults.innerHTML = '';
      return;
    }

    const matches = PAGES.filter(p => p.label.toLowerCase().includes(q));

    searchResults.innerHTML = matches.length === 0
      ? `<div class="search-empty">No pages match "${escapeHtml(query)}"</div>`
      : matches.map(p => p.href
          ? `<div class="search-result-item" data-href="${p.href}">${escapeHtml(p.label)}</div>`
          : `<div class="search-result-item disabled">${escapeHtml(p.label)}<span class="tag">Coming Soon</span></div>`
        ).join('');

    searchResults.classList.add('visible');
  }

  if (searchInput && searchResults && searchBox) {
    searchInput.addEventListener('input', () => renderSearchResults(searchInput.value));

    searchInput.addEventListener('focus', () => {
      if (searchInput.value.trim()) renderSearchResults(searchInput.value);
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