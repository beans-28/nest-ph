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

  /* ===== TOP STAT CARDS ===== */
  .stats-row{ display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-bottom:20px; }
  .stat-card{ background:var(--card-bg); border-radius:12px; border:1px solid var(--border); padding:16px 18px; box-shadow:0 1px 2px rgba(20,30,20,0.03); position:relative; }
  .stat-card-head{ display:flex; align-items:center; gap:8px; font-size:12px; color:var(--text-mid); margin-bottom:8px; }
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

  /* ===== ALERT BANNER ===== */
  .alert-banner{ background:linear-gradient(90deg, #f6d6d3, #fbeceb); border:1px solid #f2c3bf; border-radius:12px; padding:14px 20px; display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:20px; }
  .alert-banner-text strong{ display:block; font-size:13.5px; color:#a3372e; }
  .alert-banner-text span{ font-size:12px; color:#8a4a44; }
  .alert-review-btn{ background:#a3372e; color:#fff; border:none; border-radius:8px; padding:9px 16px; font-size:12px; font-weight:600; cursor:pointer; white-space:nowrap; }

  /* ===== TWO-COLUMN LAYOUT ===== */
  .dash-grid{ display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:start; }
  .dash-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:20px 22px; margin-bottom:20px; }
  .dash-card-head{ display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
  .dash-card-head h2{ font-size:14.5px; font-weight:700; margin:0; }
  .dash-card-head .view-all{ font-size:11.5px; color:var(--green-accent); font-weight:600; cursor:pointer; }

  /* Occupancy bars */
  .occ-row{ display:grid; grid-template-columns:70px 1fr 40px; align-items:center; gap:12px; margin-bottom:14px; }
  .occ-row:last-child{ margin-bottom:0; }
  .occ-label{ font-size:12.5px; color:var(--text-mid); }
  .occ-bar-track{ height:9px; border-radius:20px; background:#e6ebe6; overflow:hidden; }
  .occ-bar-fill{ height:100%; border-radius:20px; background:linear-gradient(90deg, var(--green-accent), var(--status-vacant)); }
  .occ-count{ font-size:11.5px; color:var(--text-light); text-align:right; }

  /* Tickets */
  .ticket-item{ border:1px solid var(--border); border-radius:10px; padding:14px 16px; margin-bottom:12px; }
  .ticket-item:last-child{ margin-bottom:0; }
  .ticket-title{ font-size:13px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; justify-content:between; gap:8px; }
  .ticket-title .time{ margin-left:auto; font-size:11px; font-weight:500; color:var(--text-light); }
  .ticket-desc{ font-size:12px; color:var(--text-mid); margin-top:4px; }
  .ticket-meta{ font-size:11px; color:var(--text-light); margin-top:6px; }

  /* Recent activities table */
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

  <aside class="sidebar">
    <div class="sidebar-logo"><span class="logo-mark"></span> NEST.PH</div>
    <div class="sidebar-section-label">Quick Access</div>
    <ul class="nav-list">
      <li class="nav-item active" data-href="{{ route('dashboard') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></span>Dashboard</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></span>Tenant Manager</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></span>Billing and Payments</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg></span>Delinquency</li>
      <li class="nav-item" data-href="{{ route('admin.addfloor') }}" onclick="window.location.href=this.dataset.href"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="4" width="7" height="7"/><rect x="3" y="15" width="7" height="7"/><rect x="14" y="15" width="7" height="7"/></svg></span>Vacancy Monitor</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h18v10H3z"/><path d="M3 12h18"/></svg></span>Tickets</li>
      <li class="nav-item" data-href="{{ route('vr.index') }}" onclick="window.location.href=this.dataset.href"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 3v18M16 3v18"/></svg></span>VR Management</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg></span>Lease Management</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/></svg></span>Admin Privileges</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 21V9l8-6 8 6v12"/><path d="M9 21v-6h6v6"/></svg></span>Business Information</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M6 10h4M6 14h2"/></svg></span>Dormitory Profile</li>
    </ul>
    <div class="sidebar-footer"><div class="nav-item" id="logoutBtn"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.4 5.6a9 9 0 11-12.8 0M12 3v8"/></svg></span>Log Out</div></div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div class="hamburger"><span></span><span></span><span></span></div>
      <div class="search-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg><input type="text" placeholder="Search"></div>
      <div class="topbar-right">
        <div class="topbar-icon avatar-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
      </div>
    </div>

    <div class="content">
      <div class="page-head">
        <div class="back-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
        <h1>Admin Dashboard</h1>
      </div>

      <!-- ===== TOP STAT CARDS ===== -->
      <div class="stats-row">
        <div class="stat-card">
          <span class="placeholder-tag">Coming Soon</span>
          <div class="stat-card-head"><span class="stat-icon-dot red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></span>Total Tenants</div>
          <div class="stat-value">—</div>
          <div class="stat-sub">Needs Tenant Manager module</div>
        </div>
        <div class="stat-card">
          <span class="placeholder-tag">Coming Soon</span>
          <div class="stat-card-head"><span class="stat-icon-dot blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></span>Revenue (This Month)</div>
          <div class="stat-value">—</div>
          <div class="stat-sub">Needs Billing &amp; Payments module</div>
        </div>
        <div class="stat-card">
          <span class="placeholder-tag">Coming Soon</span>
          <div class="stat-card-head"><span class="stat-icon-dot teal"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg></span>Delinquent</div>
          <div class="stat-value">—</div>
          <div class="stat-sub">Needs Delinquency module</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-head"><span class="stat-icon-dot green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg></span>Vacancy Rate</div>
          <div class="stat-value">{{ $vacancyRate }}<span class="unit">%</span></div>
          <div class="stat-sub">{{ $vacantBeds }} of {{ $totalBeds }} beds open</div>
        </div>
      </div>

      <!-- ===== DELINQUENCY ALERT (placeholder) ===== -->
      <div class="alert-banner">
        <div class="alert-banner-text">
          <strong>Delinquency alerts will appear here</strong>
          <span>This banner will surface overdue accounts once the Delinquency module is built.</span>
        </div>
        <button class="alert-review-btn" disabled style="opacity:0.6;cursor:not-allowed;">Review</button>
      </div>

      <div class="dash-grid">
        <div>
          <!-- ===== OCCUPANCY (real data) ===== -->
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

          <!-- ===== TICKETS (placeholder) ===== -->
          <div class="dash-card">
            <div class="dash-card-head"><h2>Tickets</h2><span class="view-all">View All</span></div>
            <div class="empty-note">
              No tickets yet — the Ticketing module hasn't been built.
              <div class="tag">Coming Soon</div>
            </div>
          </div>
        </div>

        <div>
          <!-- ===== RECENT ACTIVITIES (placeholder) ===== -->
          <div class="dash-card">
            <div class="dash-card-head"><h2>Recent Activities</h2></div>
            <div class="activity-tabs">ALL</div>
            <div class="empty-note">
              Activity log will appear here once Tenants, Billing, Delinquency, and Tickets are built.
              <div class="tag">Coming Soon</div>
            </div>
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
})();
</script>

</body>
</html>