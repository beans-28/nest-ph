<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH — Reports</title>
<style>
  :root{
    --green-dark:#3f6b4a; --green-mid:#4f7c57;
    --green-sidebar-top:#5b8a63; --green-sidebar-bottom:#2c4a35;
    --green-accent:#2f6f3c; --green-btn:#2f6b3a; --green-btn-hover:#255a2f;
    --bg-page:#eef1ee; --card-bg:#ffffff;
    --text-dark:#243026; --text-mid:#5b6b60; --text-light:#8a9690; --border:#e2e6e2;
    --font-body: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
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
  .sidebar-logo .logo-mark::before, .sidebar-logo .logo-mark::after{ content:''; position:absolute; background:#eaf0ea; width:2px; height:12px; top:0; left:5px; }
  .sidebar-section-label{ font-size:10.5px; text-transform:uppercase; letter-spacing:1px; color:rgba(234,240,234,0.55); padding:4px 20px 8px 20px; font-weight:600; white-space:nowrap; flex-shrink:0; }

  .nav-list{ list-style:none; margin:0; padding:0 0 8px 0; flex:1; min-height:0; overflow-y:auto; overflow-x:hidden; scrollbar-width:thin; scrollbar-color:rgba(255,255,255,0.28) transparent; }
  .nav-list::-webkit-scrollbar{ width:5px; }
  .nav-list::-webkit-scrollbar-track{ background:transparent; }
  .nav-list::-webkit-scrollbar-thumb{ background:rgba(255,255,255,0.28); border-radius:10px; }

  .nav-item{ display:flex; align-items:center; gap:11px; padding:9px 20px; font-size:13px; color:rgba(234,240,234,0.78); cursor:pointer; border-left:3px solid transparent; white-space:nowrap; flex-shrink:0; text-decoration:none; }
  .nav-item:hover{ background:rgba(255,255,255,0.06); color:#fff; }
  .nav-item.active{ background:rgba(255,255,255,0.14); color:#fff; font-weight:600; border-left:3px solid #ffffff; }
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
  .topbar{ display:flex; align-items:center; gap:16px; background:linear-gradient(90deg, var(--green-mid), var(--green-dark)); padding:14px 28px; position:sticky; top:0; z-index:20; }
  .topbar .hamburger{ width:20px; height:16px; display:flex; flex-direction:column; justify-content:space-between; cursor:pointer; }
  .topbar .hamburger span{ display:block; height:2px; background:#eaf0ea; border-radius:2px; }
  .topbar-right{ margin-left:auto; display:flex; align-items:center; gap:18px; }
  .topbar-icon{ width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; color:#eaf0ea; }

  .content{ padding:28px; max-width:1100px; }
  .page-head{ display:flex; align-items:center; gap:12px; margin-bottom:20px; }
  .page-head h1{ font-size:20px; margin:0; }
  .back-arrow{ cursor:pointer; color:var(--text-mid); display:flex; }

  .report-tabs{ display:flex; gap:8px; margin-bottom:18px; }
  .report-tab{ padding:9px 18px; border-radius:8px; border:1px solid var(--border); background:var(--card-bg); font-size:13px; font-weight:600; color:var(--text-mid); cursor:pointer; }
  .report-tab.active{ background:var(--green-btn); border-color:var(--green-btn); color:#fff; }

  .controls-row{ display:flex; align-items:flex-end; gap:14px; flex-wrap:wrap; background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:16px 18px; margin-bottom:20px; }
  .field{ display:flex; flex-direction:column; gap:5px; }
  .field label{ font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-light); }
  .field input{ border:1px solid var(--border); border-radius:8px; padding:8px 10px; font-size:13px; font-family:var(--font-body); }
  .btn{ border:none; border-radius:8px; padding:10px 18px; font-size:13px; font-weight:600; cursor:pointer; }
  .btn.primary{ background:var(--green-btn); color:#fff; }
  .btn.primary:hover{ background:var(--green-btn-hover); }
  .btn.secondary{ background:#eef1ee; color:var(--text-dark); border:1px solid var(--border); }
  .btn.secondary:hover{ background:#e2e6e2; }
  .btn:disabled{ opacity:0.6; cursor:not-allowed; }
  .occupancy-note{ font-size:11.5px; color:var(--text-light); margin-top:-6px; margin-bottom:16px; }

  .stats-row{ display:grid; grid-template-columns:repeat(auto-fit, minmax(160px, 1fr)); gap:14px; margin-bottom:20px; }
  .stat-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:16px 18px; }
  .stat-label{ font-size:11px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-light); font-weight:600; margin-bottom:6px; }
  .stat-value{ font-size:22px; font-weight:700; }

  table.report-table{ width:100%; border-collapse:collapse; background:var(--card-bg); border:1px solid var(--border); border-radius:12px; overflow:hidden; }
  table.report-table th{ text-align:left; font-size:10.5px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-light); padding:10px 14px; border-bottom:1px solid var(--border); background:#f7f9f7; }
  table.report-table td{ font-size:13px; color:var(--text-dark); padding:10px 14px; border-bottom:1px solid #f0f2f0; }

  .empty-note{ font-size:13px; color:var(--text-light); text-align:center; padding:40px 10px; background:var(--card-bg); border:1px solid var(--border); border-radius:12px; }
  .results-head{ display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
  .results-head h2{ font-size:15px; margin:0; }
  .generated-note{ font-size:11.5px; color:var(--text-light); margin-bottom:14px; }
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
      <li class="nav-item" data-href="{{ route('tickets.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h18v10H3z"/><path d="M3 12h18"/></svg></span><span class="label">Tickets</span></li>
      <li class="nav-item" data-href="{{ route('applications.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/></svg></span><span class="label">Applications</span></li>
      <li class="nav-item" data-href="{{ route('inquiries.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></span><span class="label">Inquiries</span></li>
      <li class="nav-item" data-href="{{ route('vr.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 3v18M16 3v18"/></svg></span><span class="label">VR Management</span></li>
      <li class="nav-item" data-href="{{ route('contracts.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg></span><span class="label">Lease Management</span></li>
      <li class="nav-item active" data-href="{{ route('reports.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M18 17V9M13 17V5M8 17v-3"/></svg></span><span class="label">Reports</span></li>
      <li class="nav-item" data-href="{{ route('admin-privileges.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l8 4v6c0 5-3.5 9-8 10-4.5-1-8-5-8-10V6l8-4z"/><path d="M9 12l2 2 4-4"/></svg></span><span class="label">Admin Privileges</span></li>
      <li class="nav-item" data-href="{{ route('dormitory-profile.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M6 10h4M6 14h2"/></svg></span><span class="label">Dormitory Profile</span></li>
    </ul>
    <div class="sidebar-footer">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <div class="nav-item" onclick="this.closest('form').submit()">
          <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.4 5.6a9 9 0 11-12.8 0M12 3v8"/></svg></span>
          <span class="label">Log Out</span>
        </div>
      </form>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div class="hamburger" id="hamburgerBtn"><span></span><span></span><span></span></div>
      <div class="topbar-right">
        <div class="topbar-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
      </div>
    </div>

    <div class="content">
      <div class="page-head">
        <div class="back-arrow" data-href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
        <h1>Reports</h1>
      </div>

      <div class="report-tabs">
        <button type="button" class="report-tab active" data-type="occupancy">Occupancy Report</button>
        <button type="button" class="report-tab" data-type="financial">Financial / Billing Report</button>
      </div>

      <div class="controls-row">
        <div class="field" id="startField">
          <label for="startDate">From</label>
          <input type="date" id="startDate">
        </div>
        <div class="field" id="endField">
          <label for="endDate">To</label>
          <input type="date" id="endDate">
        </div>
        <button type="button" class="btn primary" id="generateBtn">Generate</button>
        <button type="button" class="btn secondary" id="exportBtn" disabled>Export CSV</button>
      </div>
      <p class="occupancy-note" id="occupancyNote">Occupancy always reflects current room/bed status in real time — the date range only applies to the Financial report.</p>
      <div id="resultsArea">
        <div class="empty-note">Pick a report type and click Generate to see the numbers.</div>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  const $ = id => document.getElementById(id);

  let currentType = 'occupancy';
  let hasResults = false;

  document.querySelectorAll('[data-href]').forEach(el => {
    el.addEventListener('click', () => window.location.href = el.dataset.href);
  });

  $('hamburgerBtn').addEventListener('click', () => {
    $('sidebar').classList.toggle('collapsed');
  });

  function toggleDateFields(){
    const show = currentType === 'financial';
    $('startField').style.display = 'flex';
    $('endField').style.display = 'flex';
    $('occupancyNote').style.display = currentType === 'occupancy' ? 'block' : 'none';
  }

  document.querySelectorAll('.report-tab').forEach(tab => {
    tab.addEventListener('click', function(){
      document.querySelectorAll('.report-tab').forEach(t => t.classList.remove('active'));
      this.classList.add('active');
      currentType = this.dataset.type;
      hasResults = false;
      $('exportBtn').disabled = true;
      $('resultsArea').innerHTML = '<div class="empty-note">Pick a report type and click Generate to see the numbers.</div>';
      toggleDateFields();
    });
  });
  toggleDateFields();

  function money(n){
    return '₱' + Number(n || 0).toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});
  }

  function rangeParams(){
    const start = $('startDate').value;
    const end = $('endDate').value;
    const params = new URLSearchParams();
    if(start) params.set('start', start);
    if(end) params.set('end', end);
    return params;
  }

  function renderOccupancy(data){
    let html = `
      <div class="results-head"><h2>Occupancy Report</h2></div>
      <div class="generated-note">Generated ${data.generated_at}</div>
      <div class="stats-row">
        <div class="stat-card"><div class="stat-label">Total Rooms</div><div class="stat-value">${data.total_rooms}</div></div>
        <div class="stat-card"><div class="stat-label">Total Bedspaces</div><div class="stat-value">${data.total_beds}</div></div>
        <div class="stat-card"><div class="stat-label">Occupied</div><div class="stat-value">${data.occupied}</div></div>
        <div class="stat-card"><div class="stat-label">Vacant</div><div class="stat-value">${data.vacant}</div></div>
        <div class="stat-card"><div class="stat-label">Reserved</div><div class="stat-value">${data.reserved}</div></div>
        <div class="stat-card"><div class="stat-label">Maintenance</div><div class="stat-value">${data.maintenance}</div></div>
        <div class="stat-card"><div class="stat-label">Occupancy Rate</div><div class="stat-value">${data.occupancy_rate}%</div></div>
      </div>
      <table class="report-table">
        <thead><tr><th>Floor</th><th>Total Beds</th><th>Occupied</th><th>Vacant</th><th>Reserved</th><th>Maintenance</th><th>Occupancy %</th></tr></thead>
        <tbody>`;
    if(data.by_floor.length === 0){
      html += `<tr><td colspan="7" style="text-align:center;color:var(--text-light);">No floors added yet.</td></tr>`;
    } else {
      data.by_floor.forEach(f => {
        html += `<tr><td>${f.label}</td><td>${f.total_beds}</td><td>${f.occupied}</td><td>${f.vacant}</td><td>${f.reserved}</td><td>${f.maintenance}</td><td>${f.occupancy_rate}%</td></tr>`;
      });
    }
    html += `</tbody></table>`;
    $('resultsArea').innerHTML = html;
  }

  function renderFinancial(data){
    let html = `
      <div class="results-head"><h2>Financial / Billing Report</h2></div>
      <div class="generated-note">Period: ${data.range.start} to ${data.range.end}</div>
      <div class="stats-row">
        <div class="stat-card"><div class="stat-label">Total Collected</div><div class="stat-value">${money(data.total_collected)}</div></div>
        <div class="stat-card"><div class="stat-label">Cash</div><div class="stat-value">${money(data.cash_collected)}</div></div>
        <div class="stat-card"><div class="stat-label">Online</div><div class="stat-value">${money(data.online_collected)}</div></div>
        <div class="stat-card"><div class="stat-label">Total Outstanding</div><div class="stat-value">${money(data.total_outstanding)}</div></div>
        <div class="stat-card"><div class="stat-label">Penalties Applied</div><div class="stat-value">${money(data.total_penalties)}</div></div>
        <div class="stat-card"><div class="stat-label">Delinquent Accounts</div><div class="stat-value">${data.delinquent_accounts}</div></div>
        <div class="stat-card"><div class="stat-label">Payments Recorded</div><div class="stat-value">${data.payment_count}</div></div>
      </div>`;
    $('resultsArea').innerHTML = html;
  }

  $('generateBtn').addEventListener('click', async function(){
    this.disabled = true;
    this.textContent = 'Generating...';
    try {
      const url = `/reports/${currentType}?` + rangeParams().toString();
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
      if(!res.ok) throw new Error('Could not generate the report.');
      const data = await res.json();
      if(currentType === 'occupancy'){ renderOccupancy(data); } else { renderFinancial(data); }
      hasResults = true;
      $('exportBtn').disabled = false;
    } catch(e){
      $('resultsArea').innerHTML = `<div class="empty-note">${e.message}</div>`;
      hasResults = false;
      $('exportBtn').disabled = true;
    }
    this.disabled = false;
    this.textContent = 'Generate';
  });

  $('exportBtn').addEventListener('click', function(){
    if(!hasResults) return;
    const params = rangeParams();
    params.set('type', currentType);
    window.location.href = '/reports/export?' + params.toString();
  });
})();
</script>
</body>
</html>
