<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Reports · {{ $brandDormName }}</title>
<link rel="icon" href="{{ $brandFaviconUrl }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
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
  .field[hidden]{ display:none; }
  .field label{ font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-light); }
  .field input{ border:1px solid var(--border); border-radius:8px; padding:8px 10px; font-size:13px; font-family:var(--font-body); }
  .btn{ border:none; border-radius:8px; padding:10px 18px; font-size:13px; font-weight:600; cursor:pointer; }
  .btn.primary{ background:var(--green-btn); color:#fff; }
  .btn.primary:hover{ background:var(--green-btn-hover); }
  .btn.secondary{ background:#eef1ee; color:var(--text-dark); border:1px solid var(--border); }
  .btn.secondary:hover{ background:#e2e6e2; }
  .btn:disabled{ opacity:0.6; cursor:not-allowed; }
  .report-actions{ display:flex; gap:10px; }
  @media (max-width:560px){ .report-actions{ display:grid; grid-template-columns:1fr 1fr; width:100%; } .report-actions #generateBtn{ grid-column:1 / -1; } .report-actions .btn{ min-height:44px; } }
  .btn:focus-visible, .report-tab:focus-visible{ outline:2px solid var(--green-btn); outline-offset:2px; }
  .export-error{ font-size:12px; color:#ba2828; margin:-6px 0 16px; display:none; }
  .export-error.visible{ display:block; }
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

  [hidden]{ display:none !important; }
  .content{ min-width:0; }
  .report-tabs{ flex-wrap:wrap; }
  @media (max-width:560px){
    .content{ padding:18px 16px; }
    .stats-row.money .stat-card:last-child{ grid-column:1 / -1; }
    .controls-row{ padding:14px; }
    .controls-row .field{ flex:1 1 130px; }
    .controls-row .field input{ width:100%; box-sizing:border-box; min-height:44px; }
    .stat-value{ font-size:19px; }
    .chart-card{ padding:14px; }
    .chart-box{ height:220px; }
    .expense-grid{ grid-template-columns:1fr 1fr; padding:14px; }
    .expense-grid .field input{ min-height:44px; }
    .expense-footer .btn{ width:100%; min-height:44px; }
    .profit-sum .part strong{ font-size:17px; }
  }
  .stat-value, .num, .report-table td{ font-variant-numeric:tabular-nums; }
  .report-table tr[data-month]:hover td{ background:#f4f7f4; }
  .report-table tr[data-month]:focus-visible{ outline:2px solid var(--green-btn); outline-offset:-2px; }
  .link-btn{ background:none; border:none; padding:0; font:inherit; color:inherit; font-weight:600; text-decoration:underline; cursor:pointer; }
  .link-btn:focus-visible{ outline:2px solid var(--green-btn); outline-offset:2px; border-radius:2px; }

  /* Charts (same Chart.js build as the Admin Dashboard) */
  .chart-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:16px 18px; margin-bottom:20px; }
  .chart-card h3{ font-size:13.5px; margin:0 0 2px; }
  .chart-card .chart-sub{ font-size:11.5px; color:var(--text-light); margin:0 0 12px; }
  .chart-box{ position:relative; height:260px; }
  .chart-fallback{ position:absolute; inset:0; margin:0; display:flex; align-items:center; justify-content:center; text-align:center; padding:12px; font-size:12.5px; color:var(--text-mid); background:#f7f9f7; border-radius:8px; }
  .chart-legend{ display:flex; gap:16px; flex-wrap:wrap; font-size:11.5px; color:var(--text-mid); margin-bottom:8px; }
  .chart-legend span::before{ content:""; display:inline-block; width:10px; height:10px; border-radius:2px; margin-right:6px; vertical-align:-1px; background:var(--swatch); }
  .stat-card.profit{ background:#dcebdc; border-color:#b9d6b9; }
  .stat-card.profit .stat-value{ color:#194e19; }
  .stat-card.loss{ background:#fbe9e9; border-color:#efc5c5; }
  .stat-card.loss .stat-value{ color:#ba2828; }
  .missing-note{ font-size:12px; color:#8a5a00; background:#fff6e0; border:1px solid #f0dca8; border-radius:8px; padding:9px 12px; margin-bottom:16px; }
  .table-scroll{ overflow-x:auto; margin-bottom:20px; }
  .num{ text-align:right !important; }
  .neg{ color:#ba2828 !important; }

  /* Monthly Expenses form */
  .expense-grid{ display:grid; grid-template-columns:repeat(auto-fit, minmax(190px, 1fr)); gap:14px; background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:18px; margin-bottom:14px; }
  .expense-grid .field input{ width:100%; box-sizing:border-box; }
  .expense-grid .field.wide{ grid-column:1 / -1; }
  .field .hint{ font-size:11px; color:var(--text-light); }
  .expense-footer{ display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:24px; }
  .profit-sum{ display:grid; grid-template-columns:1fr auto 1fr auto 1.2fr; align-items:center; gap:10px; background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:16px 18px; margin-bottom:14px; }
  .profit-sum .part{ min-width:0; }
  .profit-sum .part span{ display:block; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-light); margin-bottom:4px; }
  .profit-sum .part strong{ font-size:20px; font-variant-numeric:tabular-nums; }
  .profit-sum .op{ font-size:20px; color:var(--text-light); }
  .profit-sum .result{ background:#dcebdc; border-radius:10px; padding:10px 14px; }
  .profit-sum .result strong{ color:#194e19; }
  .profit-sum .result.loss{ background:#fbe9e9; }
  .profit-sum .result.loss strong{ color:#ba2828; }
  .save-state{ font-size:12.5px; color:var(--text-light); }
  .save-state.unsaved{ color:#8a5a00; font-weight:600; }
  .profit-note{ font-size:11.5px; color:var(--text-light); margin:-6px 0 14px; }
  @media (max-width:720px){
    .profit-sum{ grid-template-columns:1fr 1fr; }
    .profit-sum .op{ display:none; }
    .profit-sum .result{ grid-column:1 / -1; }
  }
  .form-msg{ font-size:12.5px; margin:0 0 14px; display:none; }
  .form-msg.ok{ display:block; color:#194e19; }
  .form-msg.err{ display:block; color:#ba2828; }
</style>
</head>
<body>
<div class="app">

@include('partials.admin-sidebar')

  <div class="main">
    <div class="topbar">
      <div class="hamburger" id="hamburgerBtn" tabindex="0" aria-label="Toggle sidebar"><span></span><span></span><span></span></div>
      <div class="topbar-right">
        <div class="topbar-icon avatar-icon" tabindex="0" aria-label="Account"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
      </div>
    </div>

    <div class="content">
      <div class="page-head">
        <div class="back-arrow" data-href="{{ route('dashboard') }}" tabindex="0" aria-label="Back to dashboard"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
        <h1>Reports</h1>
      </div>

      <div class="report-tabs">
        <button type="button" class="report-tab active" data-type="occupancy">Occupancy Report</button>
        <button type="button" class="report-tab" data-type="financial">Financial / Billing Report</button>
        <button type="button" class="report-tab" data-type="forecast">Forecast</button>
        <button type="button" class="report-tab" data-type="expenses">Expenses &amp; Profit</button>
      </div>

      <div class="controls-row" id="reportControls">
        <div class="field" id="startField">
          <label for="startDate">From</label>
          <input type="date" id="startDate">
        </div>
        <div class="field" id="endField">
          <label for="endDate">To</label>
          <input type="date" id="endDate">
        </div>
        <div class="report-actions">
          <button type="button" class="btn primary" id="generateBtn">Generate</button>
          <button type="button" class="btn secondary" id="exportBtn" disabled>Export Excel</button>
          <button type="button" class="btn secondary" id="exportPdfBtn" disabled>Export PDF</button>
        </div>
      </div>
      <p class="export-error" id="exportError" role="alert"></p>
      <p class="occupancy-note" id="occupancyNote">Occupancy always reflects current room/bed status in real time. The date range only applies to the Financial report.</p>
      <div id="resultsArea">
        <div class="empty-note">Pick a report type and click Generate to see the numbers.</div>
      </div>

      {{-- Monthly Expenses: the dorm's own bills for a month. The Financial
           report subtracts these from payments collected to get Net Profit. --}}
      <div id="expensesArea" hidden>
        <div class="controls-row">
          <div class="field">
            <label for="expenseMonth">Month</label>
            <input type="month" id="expenseMonth">
          </div>
          <p class="occupancy-note" style="margin:0 0 4px;">Enter the whole building's bills for the month. Saving a month again replaces what was saved before.</p>
        </div>
        <form id="expenseForm" novalidate>
          <div class="expense-grid">
            <div class="field"><label for="exElectricity">Electricity (Meralco)</label><input type="number" id="exElectricity" min="0" step="0.01" inputmode="decimal" placeholder="0.00"></div>
            <div class="field"><label for="exWater">Water</label><input type="number" id="exWater" min="0" step="0.01" inputmode="decimal" placeholder="0.00"></div>
            <div class="field"><label for="exInternet">Internet / WiFi</label><input type="number" id="exInternet" min="0" step="0.01" inputmode="decimal" placeholder="0.00"></div>
            <div class="field"><label for="exSalaries">Staff Salaries</label><input type="number" id="exSalaries" min="0" step="0.01" inputmode="decimal" placeholder="0.00"><span class="hint">Caretaker, cleaners, guards, etc.</span></div>
            <div class="field"><label for="exOther">Others</label><input type="number" id="exOther" min="0" step="0.01" inputmode="decimal" placeholder="0.00"></div>
            <div class="field wide"><label for="exOtherNotes">What were the "Others"? (optional)</label><input type="text" id="exOtherNotes" maxlength="255" placeholder="e.g. Plumbing repair, cleaning supplies"></div>
          </div>
          {{-- Money in minus money out for the picked month. Updates as the
               expense amounts are typed, before saving. --}}
          <div class="profit-sum" aria-live="polite">
            <div class="part"><span id="collectedLabel">Collected</span><strong id="sumCollected">&#8369;0.00</strong></div>
            <div class="op" aria-hidden="true">&minus;</div>
            <div class="part"><span>Expenses</span><strong id="expenseTotal">&#8369;0.00</strong></div>
            <div class="op" aria-hidden="true">=</div>
            <div class="part result" id="sumResult"><span>Net Profit</span><strong id="sumNet">&#8369;0.00</strong></div>
          </div>
          <p class="profit-note">Collected = approved tenant payments dated within the month (rent, utilities, move-in fees, penalties).</p>
          <p class="form-msg" id="expenseMsg" role="status"></p>
          <div class="expense-footer">
            <span class="save-state" id="saveState"></span>
            <button type="submit" class="btn primary" id="saveExpenseBtn">Save Expenses</button>
          </div>
        </form>
        <div class="results-head">
          <h2>Profit by Month</h2>
          <div class="report-actions">
            <button type="button" class="btn secondary" id="expExportBtn">Export Excel</button>
            <button type="button" class="btn secondary" id="expExportPdfBtn">Export PDF</button>
          </div>
        </div>
        <p class="export-error" id="expExportError" role="alert"></p>
        <div class="table-scroll" id="expenseHistory"></div>
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
<script>
(function(){
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  const $ = id => document.getElementById(id);

  let currentType = 'occupancy';
  let hasResults = false;
  // The type + dates the on-screen report was generated with. Export uses
  // these (not the live date inputs), so the file always matches what
  // the admin is looking at even if they edit the dates afterwards.
  let generatedParams = null;

  document.querySelectorAll('[data-href]').forEach(el => {
    el.addEventListener('click', () => window.location.href = el.dataset.href);
  });

  // Remember collapsed/expanded across page loads (each admin page is a
  // full reload, not a single-page app, so this has to be localStorage
  // rather than in-memory state) so the sidebar doesn't reset every time
  // you navigate. Same key as every other admin page.
  const SIDEBAR_COLLAPSE_KEY = 'nestph_sidebar_collapsed';
  const sidebar = $('sidebar');
  if (localStorage.getItem(SIDEBAR_COLLAPSE_KEY) === '1') {
    sidebar.classList.add('collapsed');
  }
  $('hamburgerBtn').addEventListener('click', () => {
    const collapsed = sidebar.classList.toggle('collapsed');
    localStorage.setItem(SIDEBAR_COLLAPSE_KEY, collapsed ? '1' : '0');
  });

  document.querySelectorAll('.sidebar [tabindex="0"], .hamburger, .topbar-icon, .back-arrow').forEach(el => {
    el.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        el.click();
      }
    });
  });

  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', async () => {
      await fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf } });
      window.location.href = '/';
    });
  }

  function toggleDateFields(){
    const isExpenses = currentType === 'expenses';
    $('reportControls').hidden = isExpenses;
    $('resultsArea').hidden = isExpenses;
    $('expensesArea').hidden = !isExpenses;
    $('exportError').classList.remove('visible');
    $('occupancyNote').style.display = currentType === 'occupancy' ? 'block' : 'none';
    // Forecast always looks at the last 6 months and the next 3, so the dates don't apply.
    $('startField').hidden = $('endField').hidden = currentType === 'forecast';
    if (isExpenses) loadExpenses();
  }

  document.querySelectorAll('.report-tab').forEach(tab => {
    tab.addEventListener('click', function(){
      document.querySelectorAll('.report-tab').forEach(t => t.classList.remove('active'));
      this.classList.add('active');
      currentType = this.dataset.type;
      hasResults = false;
      destroyCharts();
      $('exportBtn').disabled = true;
      $('exportPdfBtn').disabled = true;
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

  // ---- Charts ----
  const C = { green:'#194e19', greenSoft:'rgba(25,78,25,0.12)', olive:'#8fb48f', red:'#c0504d', grid:'rgba(0,0,0,0.06)' };
  let charts = [];
  function destroyCharts(){ charts.forEach(c => c.destroy()); charts = []; }
  function chartOrFallback(id, config){
    const canvas = $(id);
    if (typeof Chart === 'undefined') {
      canvas.parentElement.innerHTML = '<p class="chart-fallback">Chart couldn&#39;t load. The table below still has the same numbers.</p>';
      return;
    }
    charts.push(new Chart(canvas, config));
  }
  const shortPeso = v => '₱' + (Math.abs(v) >= 1000 ? (v / 1000).toLocaleString('en-PH', {maximumFractionDigits:1}) + 'k' : v);
  const tooltip = { backgroundColor:'#1f2a1f', padding:10, cornerRadius:6 };
  // "Jan 2026" -> "Jan '26" so 12 months fit on a phone without tilted labels
  const shortMonth = l => l.replace(/ \d{2}(\d{2})$/, " '$1");
  const xAxis = { grid:{ display:false }, border:{ display:false }, ticks:{ maxRotation:0, autoSkip:true, autoSkipPadding:8 } };

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
      <div class="table-scroll"><table class="report-table">
        <thead><tr><th>Floor</th><th>Total Beds</th><th>Occupied</th><th>Vacant</th><th>Reserved</th><th>Maintenance</th><th>Occupancy %</th></tr></thead>
        <tbody>`;
    if(data.by_floor.length === 0){
      html += `<tr><td colspan="7" style="text-align:center;color:var(--text-light);">No floors added yet.</td></tr>`;
    } else {
      data.by_floor.forEach(f => {
        html += `<tr><td>${f.label}</td><td>${f.total_beds}</td><td>${f.occupied}</td><td>${f.vacant}</td><td>${f.reserved}</td><td>${f.maintenance}</td><td>${f.occupancy_rate}%</td></tr>`;
      });
    }
    html += `</tbody></table></div>`;

    html += `
      <div class="results-head" style="margin-top:26px;"><h2>Occupancy Trend</h2></div>
      <div class="chart-card">
        <h3>Occupancy rate, last 12 months</h3>
        <p class="chart-sub">Rate at the end of each month, based on when tenants moved in and moved out. Bars show move-ins and move-outs.</p>
        <div class="chart-legend"><span style="--swatch:${C.green}">Occupancy rate</span><span style="--swatch:${C.olive}">Moved in</span><span style="--swatch:${C.red}">Moved out</span></div>
        <div class="chart-box"><canvas id="trendChart" role="img" aria-label="Line chart of monthly occupancy rate over the last 12 months"></canvas></div>
      </div>
      <div class="table-scroll"><table class="report-table">
        <thead><tr><th>Month</th><th>Total Beds</th><th>Occupied</th><th>Moved In</th><th>Moved Out</th><th>Occupancy %</th></tr></thead>
        <tbody>${data.trend.map(t => `<tr><td>${t.label}</td><td>${t.total_beds}</td><td>${t.occupied}</td><td>${t.moved_in}</td><td>${t.moved_out}</td><td>${t.occupancy_rate}%</td></tr>`).join('')}</tbody>
      </table></div>`;
    $('resultsArea').innerHTML = html;

    const t = data.trend;
    chartOrFallback('trendChart', {
      data:{ labels:t.map(x => shortMonth(x.label)), datasets:[
        { type:'line', label:'Occupancy rate', data:t.map(x => x.occupancy_rate), yAxisID:'y', borderColor:C.green, backgroundColor:C.greenSoft,
          fill:true, tension:0, pointRadius:3, pointBackgroundColor:C.green, order:0 },
        { type:'bar', label:'Moved in', data:t.map(x => x.moved_in), yAxisID:'y2', backgroundColor:C.olive, borderRadius:3, maxBarThickness:14, order:1 },
        { type:'bar', label:'Moved out', data:t.map(x => x.moved_out), yAxisID:'y2', backgroundColor:C.red, borderRadius:3, maxBarThickness:14, order:1 },
      ] },
      options:{ responsive:true, maintainAspectRatio:false, interaction:{ mode:'index', intersect:false },
        plugins:{ legend:{ display:false }, tooltip:{ ...tooltip, callbacks:{ label: c =>
          c.dataset.yAxisID === 'y' ? ` Occupancy: ${c.parsed.y}% (${t[c.dataIndex].occupied}/${t[c.dataIndex].total_beds} beds)` : ` ${c.dataset.label}: ${c.parsed.y}` } } },
        scales:{
          x:xAxis,
          y:{ min:0, max:100, border:{ display:false }, grid:{ color:C.grid }, ticks:{ stepSize:25, callback:v => v + '%' } },
          y2:{ position:'right', beginAtZero:true, suggestedMax:6, grid:{ display:false }, border:{ display:false }, ticks:{ precision:0 },
            title:{ display:true, text:'Tenants', font:{ size:10 } } }
        } }
    });
  }

  function renderFinancial(data){
    const profitClass = data.net_profit < 0 ? 'loss' : 'profit';
    const missing = data.months_missing_expenses || [];
    let html = `
      <div class="results-head"><h2>Financial / Billing Report</h2></div>
      <div class="generated-note">Period: ${data.range.start} to ${data.range.end}</div>
      ${missing.length ? `<div class="missing-note">No expenses recorded yet for ${missing.join(', ')}, so Net Profit may look higher than it really is. <button type="button" class="link-btn" id="goExpenses">Record expenses</button></div>` : ''}
      <div class="stats-row money">
        <div class="stat-card"><div class="stat-label">Revenue (Collected)</div><div class="stat-value">${money(data.total_collected)}</div></div>
        <div class="stat-card"><div class="stat-label">Total Expenses</div><div class="stat-value">${money(data.total_expenses)}</div></div>
        <div class="stat-card ${profitClass}"><div class="stat-label">Net Profit</div><div class="stat-value">${money(data.net_profit)}</div></div>
      </div>
      <div class="chart-card">
        <h3>Collected vs. expenses per month</h3>
        <p class="chart-sub">The green line is net profit (collected minus expenses). Months with no expenses recorded have no profit point.</p>
        <div class="chart-legend"><span style="--swatch:${C.olive}">Collected</span><span style="--swatch:${C.red}">Expenses</span><span style="--swatch:${C.green}">Net profit</span></div>
        <div class="chart-box"><canvas id="profitChart" role="img" aria-label="Bar chart of money collected versus expenses for each month"></canvas></div>
      </div>
      <div class="table-scroll"><table class="report-table">
        <thead><tr><th>Month</th><th class="num">Collected</th><th class="num">Expenses</th><th class="num">Net Profit</th></tr></thead>
        <tbody>${data.monthly.map(m => `<tr><td>${m.label}</td><td class="num">${money(m.collected)}</td><td class="num">${m.has_expenses ? money(m.expenses) : '<span style="color:var(--text-light)">Not recorded</span>'}</td><td class="num ${m.net < 0 ? 'neg' : ''}">${m.has_expenses ? money(m.net) : '<span style="color:var(--text-light)">&mdash;</span>'}</td></tr>`).join('')}</tbody>
      </table></div>
      <div class="results-head"><h2>Collections &amp; Receivables</h2></div>
      <div class="stats-row">
        <div class="stat-card"><div class="stat-label">Cash</div><div class="stat-value">${money(data.cash_collected)}</div></div>
        <div class="stat-card"><div class="stat-label">Online</div><div class="stat-value">${money(data.online_collected)}</div></div>
        <div class="stat-card"><div class="stat-label">Total Outstanding</div><div class="stat-value">${money(data.total_outstanding)}</div></div>
        <div class="stat-card"><div class="stat-label">Penalties Applied</div><div class="stat-value">${money(data.total_penalties)}</div></div>
        <div class="stat-card"><div class="stat-label">Delinquent Accounts</div><div class="stat-value">${data.delinquent_accounts}</div></div>
        <div class="stat-card"><div class="stat-label">Payments Recorded</div><div class="stat-value">${data.payment_count}</div></div>
      </div>`;
    $('resultsArea').innerHTML = html;
    const go = $('goExpenses');
    if (go) go.addEventListener('click', () => document.querySelector('.report-tab[data-type="expenses"]').click());

    const m = data.monthly;
    chartOrFallback('profitChart', {
      data:{ labels:m.map(x => shortMonth(x.label)), datasets:[
        { type:'line', label:'Net profit', data:m.map(x => x.has_expenses ? x.net : null), spanGaps:false, borderColor:C.green, backgroundColor:C.green, tension:0, pointRadius:3, order:0 },
        { type:'bar', label:'Collected', data:m.map(x => x.collected), backgroundColor:C.olive, borderRadius:4, maxBarThickness:30, order:1 },
        { type:'bar', label:'Expenses', data:m.map(x => x.has_expenses ? x.expenses : null), backgroundColor:C.red, borderRadius:4, maxBarThickness:30, order:1 },
      ] },
      options:{ responsive:true, maintainAspectRatio:false, interaction:{ mode:'index', intersect:false },
        plugins:{ legend:{ display:false }, tooltip:{ ...tooltip, callbacks:{ label: c => ` ${c.dataset.label}: ${c.parsed.y === null ? 'not recorded' : money(c.parsed.y)}` } } },
        scales:{ x:xAxis,
          y:{ border:{ display:false }, grid:{ color:C.grid }, ticks:{ maxTicksLimit:5, callback:shortPeso } } } }
    });
  }

  // Forecast: last 6 real months, then 3 estimated ones. Estimates are drawn
  // dashed / faded so nobody mistakes them for recorded numbers.
  function renderForecast(data){
    const a = data.assumptions, t = data.totals;
    const rows = data.history.map(r => ({ ...r, est:false })).concat(data.forecast.map(r => ({ ...r, est:true })));
    const netClass = t.net === null ? '' : (t.net < 0 ? 'loss' : 'profit');
    const kind = r => r.est ? 'Estimate' : (r.partial ? 'So far' : 'Actual');
    const monthLabel = r => r.partial ? `${r.label} (so far)` : r.label;
    const cell = v => v === null ? '<span style="color:var(--text-light)">Not recorded</span>' : money(v);
    let html = `
      <div class="results-head"><h2>Forecast: ${data.range}</h2></div>
      <div class="generated-note">Generated ${data.generated_at}. These are estimates, not records.</div>
      ${a.expense_months === 0 ? `<div class="missing-note">No expenses recorded in the last 6 months, so expected expenses and net can't be estimated. <button type="button" class="link-btn" id="goExpenses">Record expenses</button></div>` : ''}
      <div class="stats-row money">
        <div class="stat-card"><div class="stat-label">Expected Income (3 mo.)</div><div class="stat-value">≈ ${money(t.income)}</div></div>
        <div class="stat-card"><div class="stat-label">Expected Expenses (3 mo.)</div><div class="stat-value">${t.expenses === null ? '&mdash;' : '≈ ' + money(t.expenses)}</div></div>
        <div class="stat-card ${netClass}"><div class="stat-label">Expected Net</div><div class="stat-value">${t.net === null ? '&mdash;' : '≈ ' + money(t.net)}</div></div>
        <div class="stat-card"><div class="stat-label">Leases Ending</div><div class="stat-value">${t.ending_leases}</div></div>
      </div>
      <p class="occupancy-note" style="margin-top:0;">How it's estimated: about <strong>${a.avg_move_ins}</strong> new move-ins a month and <strong>${money(a.income_per_bed)}</strong> collected per occupied bed (averages of the last 6 months), ${a.expense_months ? `expenses of <strong>${money(a.avg_expenses)}</strong> a month (average of ${a.expense_months} recorded month${a.expense_months === 1 ? '' : 's'})` : 'no expense estimate (none recorded)'}. ${rows.some(r => r.partial) ? "The current month isn't over, so it shows income so far and is left out of the averages. " : ''}Estimates are rounded to the nearest ₱100. The date filters above don't apply here: the forecast always uses the last 6 months. Tenants whose lease ends are counted as moving out, so if some renew, the real numbers will be higher.</p>
      <div class="chart-card">
        <h3>Occupancy rate</h3>
        <p class="chart-sub">Solid line is actual. Dashed line is the estimate.</p>
        <div class="chart-box"><canvas id="fcOccChart" role="img" aria-label="Line chart of actual and estimated occupancy rate"></canvas></div>
      </div>
      <div class="chart-card">
        <h3>Income vs. expenses</h3>
        <p class="chart-sub">Faded bars are estimates. The current month only shows income so far.</p>
        <div class="chart-legend"><span style="--swatch:${C.olive}">Income</span><span style="--swatch:${C.red}">Expenses</span><span style="--swatch:rgba(143,180,143,0.4)">Estimate</span></div>
        <div class="chart-box"><canvas id="fcMoneyChart" role="img" aria-label="Bar chart of actual and estimated income and expenses per month"></canvas></div>
      </div>
      <div class="table-scroll"><table class="report-table">
        <thead><tr><th>Month</th><th>Kind</th><th class="num">Leases Ending</th><th class="num">Occupied</th><th class="num">Occupancy</th><th class="num">Income</th><th class="num">Expenses</th><th class="num">Net</th></tr></thead>
        <tbody>${rows.map(r => `<tr${r.est || r.partial ? ' style="font-style:italic"' : ''}><td>${monthLabel(r)}</td><td>${kind(r)}</td><td class="num">${r.est ? r.ending_leases : '&mdash;'}</td><td class="num">${r.occupied}</td><td class="num">${r.occupancy_rate}%</td><td class="num">${money(r.income)}</td><td class="num">${r.est && r.expenses === null ? '&mdash;' : cell(r.expenses)}</td><td class="num ${r.net !== null && r.net < 0 ? 'neg' : ''}">${r.net === null ? '&mdash;' : money(r.net)}</td></tr>`).join('')}</tbody>
      </table></div>`;
    $('resultsArea').innerHTML = html;
    const go = $('goExpenses');
    if (go) go.addEventListener('click', () => document.querySelector('.report-tab[data-type="expenses"]').click());

    const labels = rows.map(r => shortMonth(r.label));
    const firstEst = data.history.length; // index of the first estimated month
    chartOrFallback('fcOccChart', {
      type:'line',
      data:{ labels, datasets:[{ label:'Occupancy rate', data:rows.map(r => r.occupancy_rate), borderColor:C.green, backgroundColor:C.greenSoft,
        fill:true, tension:0, pointRadius:3, pointBackgroundColor:C.green,
        // Dash the line from the last actual month onwards
        segment:{ borderDash: ctx => ctx.p1DataIndex >= firstEst ? [6, 4] : undefined } }] },
      options:{ responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ display:false }, tooltip:{ ...tooltip, callbacks:{ label: c => ` ${kind(rows[c.dataIndex])}: ${c.parsed.y}% (${rows[c.dataIndex].occupied}/${data.total_beds} beds)` } } },
        scales:{ x:xAxis, y:{ min:0, max:100, border:{ display:false }, grid:{ color:C.grid }, ticks:{ stepSize:25, callback:v => v + '%' } } } }
    });
    const fade = (color, soft) => rows.map(r => r.est || r.partial ? soft : color);
    chartOrFallback('fcMoneyChart', {
      type:'bar',
      data:{ labels, datasets:[
        { label:'Income', data:rows.map(r => r.income), backgroundColor:fade(C.olive, 'rgba(143,180,143,0.4)'), borderRadius:4, maxBarThickness:30 },
        { label:'Expenses', data:rows.map(r => r.expenses), backgroundColor:fade(C.red, 'rgba(192,80,77,0.35)'), borderRadius:4, maxBarThickness:30 },
      ] },
      options:{ responsive:true, maintainAspectRatio:false, interaction:{ mode:'index', intersect:false },
        plugins:{ legend:{ display:false }, tooltip:{ ...tooltip, callbacks:{
          title: items => items[0].label + (rows[items[0].dataIndex].est ? ' (estimate)' : rows[items[0].dataIndex].partial ? ' (so far)' : ''),
          label: c => ` ${c.dataset.label}: ${c.parsed.y === null ? 'not recorded' : money(c.parsed.y)}` } } },
        scales:{ x:xAxis, y:{ beginAtZero:true, border:{ display:false }, grid:{ color:C.grid }, ticks:{ maxTicksLimit:5, callback:shortPeso } } } }
    });
  }

  $('generateBtn').addEventListener('click', async function(){
    destroyCharts();
    this.disabled = true;
    this.textContent = 'Generating...';
    try {
      const url = `/reports/${currentType}?` + rangeParams().toString();
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
      if(!res.ok) throw new Error('Could not generate the report.');
      const data = await res.json();
      if(currentType === 'occupancy'){ renderOccupancy(data); }
      else if(currentType === 'forecast'){ renderForecast(data); }
      else { renderFinancial(data); }
      hasResults = true;
      generatedParams = rangeParams();
      generatedParams.set('type', currentType);
      $('exportError').classList.remove('visible');
      $('exportBtn').disabled = false;
      $('exportPdfBtn').disabled = false;
    } catch(e){
      $('resultsArea').innerHTML = `<div class="empty-note">${e.message}</div>`;
      hasResults = false;
      $('exportBtn').disabled = true;
      $('exportPdfBtn').disabled = true;
    }
    this.disabled = false;
    this.textContent = 'Generate';
  });

  // ---- Monthly Expenses tab ----
  const EXPENSE_FIELDS = { electricity:'exElectricity', water:'exWater', internet:'exInternet', salaries:'exSalaries', other:'exOther' };
  const now = new Date();
  const thisMonth = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0');
  $('expenseMonth').value = thisMonth;
  $('expenseMonth').max = thisMonth;

  let monthCollected = 0;
  function updateExpenseTotal(){
    const total = Object.values(EXPENSE_FIELDS).reduce((sum, id) => sum + (parseFloat($(id).value) || 0), 0);
    const net = monthCollected - total;
    $('expenseTotal').textContent = money(total);
    $('sumCollected').textContent = money(monthCollected);
    $('sumNet').textContent = (net < 0 ? '-' : '') + money(Math.abs(net));
    $('sumResult').classList.toggle('loss', net < 0);
  }
  Object.values(EXPENSE_FIELDS).forEach(id => $(id).addEventListener('input', updateExpenseTotal));

  function showExpenseMsg(text, ok){
    const el = $('expenseMsg');
    el.textContent = text;
    el.className = 'form-msg ' + (ok ? 'ok' : 'err');
  }

  function renderExpenseHistory(rows){
    if (!rows.length) {
      $('expenseHistory').innerHTML = '<div class="empty-note">No months recorded yet.</div>';
      return;
    }
    $('expenseHistory').innerHTML = `<table class="report-table">
      <thead><tr><th>Month</th><th class="num">Collected</th><th class="num">Meralco</th><th class="num">Water</th><th class="num">WiFi</th><th class="num">Salaries</th><th class="num">Others</th><th class="num">Total Expenses</th><th class="num">Net Profit</th></tr></thead>
      <tbody>${rows.map(r => `<tr data-month="${r.month}" tabindex="0" style="cursor:pointer" title="Click to edit ${r.label}"><td>${r.label}</td><td class="num">${money(r.collected)}</td><td class="num">${money(r.electricity)}</td><td class="num">${money(r.water)}</td><td class="num">${money(r.internet)}</td><td class="num">${money(r.salaries)}</td><td class="num">${money(r.other)}</td><td class="num">${money(r.total)}</td><td class="num ${r.net < 0 ? 'neg' : ''}"><strong>${r.net < 0 ? '-' : ''}${money(Math.abs(r.net))}</strong></td></tr>`).join('')}</tbody>
    </table>`;
    $('expenseHistory').querySelectorAll('tr[data-month]').forEach(tr => {
      const edit = () => { $('expenseMonth').value = tr.dataset.month; loadExpenses(); window.scrollTo({ top:0, behavior:'smooth' }); };
      tr.addEventListener('click', edit);
      tr.addEventListener('keydown', e => { if (e.key === 'Enter') edit(); });
    });
  }

  async function loadExpenses(){
    try {
      const res = await fetch('/reports/expenses?month=' + encodeURIComponent($('expenseMonth').value || thisMonth), { headers:{ 'Accept':'application/json' } });
      if (!res.ok) throw new Error();
      const data = await res.json();
      Object.entries(EXPENSE_FIELDS).forEach(([key, id]) => { $(id).value = data.expense ? data.expense[key] : ''; });
      $('exOtherNotes').value = data.expense ? (data.expense.other_notes || '') : '';
      monthCollected = Number(data.collected) || 0;
      const state = $('saveState');
      state.textContent = data.expense ? `Saved for ${data.label}. Edit the amounts and save again to update.` : `Nothing saved for ${data.label} yet.`;
      state.className = 'save-state' + (data.expense ? '' : ' unsaved');
      $('collectedLabel').textContent = 'Collected in ' + data.label;
      updateExpenseTotal();
      renderExpenseHistory(data.history);
    } catch(e){
      showExpenseMsg('Could not load expenses. Please refresh the page.', false);
    }
  }
  $('expenseMonth').addEventListener('change', () => { $('expenseMsg').className = 'form-msg'; loadExpenses(); });

  $('expenseForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const body = { month:$('expenseMonth').value, other_notes:$('exOtherNotes').value.trim() || null };
    if (!body.month) { showExpenseMsg('Pick a month first.', false); return; }
    for (const [key, id] of Object.entries(EXPENSE_FIELDS)) {
      const v = $(id).value.trim();
      const n = v === '' ? 0 : Number(v);
      if (!Number.isFinite(n) || n < 0) { showExpenseMsg('Amounts must be zero or more.', false); $(id).focus(); return; }
      body[key] = n;
    }

    const btn = $('saveExpenseBtn');
    btn.disabled = true; btn.textContent = 'Saving...';
    try {
      const res = await fetch('/reports/expenses', {
        method:'POST',
        headers:{ 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN':csrf },
        body:JSON.stringify(body),
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok) throw new Error(data.message || 'Could not save. Please check the amounts and try again.');
      await loadExpenses();
      showExpenseMsg(data.message, true);
    } catch(err){
      showExpenseMsg(err.message, false);
    }
    btn.disabled = false; btn.textContent = 'Save Expenses';
  });

  // Downloads via fetch (not a page redirect) so the button can show
  // "Exporting..." while the file builds, and a failure shows a message
  // here instead of sending the admin to an error page.
  async function exportReport(btn, format, fixedParams, errorId){
    if(!fixedParams && (!hasResults || !generatedParams)) return;
    const params = new URLSearchParams(fixedParams || generatedParams);
    params.set('format', format);
    const errBox = $(errorId || 'exportError');
    const label = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Exporting...';
    errBox.classList.remove('visible');
    try {
      const res = await fetch('/reports/export?' + params.toString());
      if(!res.ok) throw new Error();
      const match = /filename="?([^";]+)"?/.exec(res.headers.get('Content-Disposition') || '');
      const url = URL.createObjectURL(await res.blob());
      const a = document.createElement('a');
      a.href = url;
      a.download = match ? match[1] : `${params.get('type')}-report.${format}`;
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(url);
    } catch(e){
      errBox.textContent = 'The report could not be exported. Please try again, or refresh the page if it keeps failing.';
      errBox.classList.add('visible');
    }
    btn.disabled = false;
    btn.textContent = label;
  }
  $('exportBtn').addEventListener('click', function(){ exportReport(this, 'xlsx'); });
  $('exportPdfBtn').addEventListener('click', function(){ exportReport(this, 'pdf'); });
  // Expenses & Profit tab: exports the saved months shown under "Profit by Month".
  $('expExportBtn').addEventListener('click', function(){ exportReport(this, 'xlsx', { type:'expenses' }, 'expExportError'); });
  $('expExportPdfBtn').addEventListener('click', function(){ exportReport(this, 'pdf', { type:'expenses' }, 'expExportError'); });
})();
</script>
</body>
</html>