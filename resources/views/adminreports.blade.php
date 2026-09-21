<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH — Reports</title>
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<style>
  /* Shared sidebar/topbar/content-header/reset styles now live in
     public/css/admin.css (linked above). Only page-specific overrides
     and this page's own content styling stay here. */

  /* Customized vs admin.css: symmetric padding + max-width, no flex:1 */
  .content{ padding:28px; max-width:1100px; }
  /* Customized vs admin.css: slightly different page-head margin */
  .page-head{ display:flex; align-items:center; gap:12px; margin-bottom:20px; }
  /* Customized vs admin.css: no font-weight/color (missing font-weight:700
     and color:var(--text-dark) that admin.css's .page-head h1 sets) */
  .page-head h1{ font-size:20px; margin:0; }
  /* Customized vs admin.css: plain icon, no circular button background/border */
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

@include('partials.admin-sidebar')

  <div class="main">
    <div class="topbar">
      <div class="hamburger" id="hamburgerBtn"><span></span><span></span><span></span></div>
      <div class="topbar-right">
        <div class="topbar-icon avatar-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
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

  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', async () => {
      await fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf } });
      window.location.href = '/';
    });
  }

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