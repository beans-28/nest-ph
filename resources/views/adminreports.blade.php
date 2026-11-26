<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    {{-- Page-switch fade opt-in: must be inline and first in <head> or Chrome decides before it sees it (see typography.css). --}}
    <style>@view-transition{navigation:auto}</style><link rel="stylesheet" href="/css/motion.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Reports · {{ $brandDormName }}</title>
<link rel="icon" href="{{ $brandFaviconUrl }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/typography.css') }}">
    <script src="{{ asset('js/smooth-nav.js') }}" defer></script>
    <script src="{{ asset('js/details-anim.js') }}" defer></script>
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<style>
  /* Shared sidebar/topbar/content-header/reset styles now live in
     public/css/admin.css (linked above). Only page-specific overrides
     and this page's own content styling stay here. */

  /* Customized vs admin.css: symmetric padding + max-width, no flex:1 */
  .content{ max-width:1100px; }
  /* Customized vs admin.css: no font-weight/color (missing font-weight:700
     and color:var(--text-dark) that admin.css's .page-head h1 sets) */
  /* Customized vs admin.css: plain icon, no circular button background/border */

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
  .btn.secondary{ background:#f0f0f0; color:var(--text-dark); border:1px solid var(--border); }
  .btn.secondary:hover{ background:#e5e5e5; }
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
  table.report-table th{ text-align:left; font-size:10.5px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-light); padding:10px 14px; border-bottom:1px solid var(--border); background:#f8f8f8; }
  table.report-table td{ font-size:13px; color:var(--text-dark); padding:10px 14px; border-bottom:1px solid #f1f1f1; }
  table.report-table tr.total-row td{ font-weight:700; background:#f6f6f6; color:#194e19; }

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
    .expense-grid .field input, .expense-grid .field select{ min-height:44px; }
    .expense-footer .btn{ flex:1; min-height:44px; }
    .profit-sum .part strong{ font-size:17px; }
  }
  .stat-value, .num, .report-table td{ font-variant-numeric:tabular-nums; }
  .link-btn{ background:none; border:none; padding:0; font:inherit; color:inherit; font-weight:600; text-decoration:underline; cursor:pointer; }
  .link-btn:focus-visible{ outline:2px solid var(--green-btn); outline-offset:2px; border-radius:2px; }

  /* Charts (same Chart.js build as the Admin Dashboard) */
  .chart-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:16px 18px; margin-bottom:20px; }
  .chart-card h3{ font-size:13.5px; margin:0 0 2px; }
  .chart-card .chart-sub{ font-size:11.5px; color:var(--text-light); margin:0 0 12px; }
  .chart-box{ position:relative; height:260px; }
  .chart-fallback{ position:absolute; inset:0; margin:0; display:flex; align-items:center; justify-content:center; text-align:center; padding:12px; font-size:12.5px; color:var(--text-mid); background:#f8f8f8; border-radius:8px; }
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

  /* Expenses form and lists */
  .card-form{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:18px; margin-bottom:24px; }
  .card-form-head h2{ font-size:15px; margin:0; }
  .card-form-head p{ font-size:12.5px; color:var(--text-mid); margin:3px 0 14px; }
  .expense-grid{ display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:14px; margin-bottom:14px; }
  .expense-grid .field input, .expense-grid .field select{ width:100%; box-sizing:border-box; }
  .expense-grid .span-2{ grid-column:span 2; }
  .expense-grid .span-all{ grid-column:1 / -1; }
  .field select{ border:1px solid var(--border); border-radius:8px; padding:8px 10px; font-size:13px; font-family:var(--font-body); background:#fff; color:var(--text-dark); }
  .field input[type=file]{ padding:6px; background:#f8f8f8; border-style:dashed; }
  .field input:focus-visible, .field select:focus-visible{ outline:2px solid var(--green-btn); outline-offset:1px; }
  .field .hint{ font-size:12px; color:var(--text-mid); line-height:1.4; }
  .inline-check{ display:inline-flex !important; align-items:center; gap:7px; font-size:12.5px !important; text-transform:none !important; letter-spacing:0 !important; color:var(--text-dark) !important; font-weight:500 !important; cursor:pointer; }
  .inline-check.strong{ font-weight:600 !important; }
  .inline-check input{ width:16px; height:16px; accent-color:var(--green-btn); margin:0; }
  .current-receipt{ display:flex; align-items:center; gap:14px; font-size:12.5px; margin-top:2px; }
  .current-receipt a{ color:var(--green-btn); font-weight:600; }
  .repeat-box{ background:#f7f7f7; border:1px solid #e6e6e6; border-radius:10px; padding:12px 14px; gap:6px; }
  .repeat-box #frequencyField{ max-width:320px; margin-top:6px; }
  .expense-footer{ display:flex; align-items:center; justify-content:flex-end; gap:10px; flex-wrap:wrap; }
  .report-table a{ color:var(--green-btn); font-weight:600; }
  .row-actions{ white-space:nowrap; text-align:right; }
  .row-actions .mini-btn.danger{ margin-left:14px; }
  /* Long descriptions wrap instead of stretching the table */
  .desc-cell{ min-width:180px; max-width:320px; overflow-wrap:anywhere; }
  /* Loading: old month's numbers are dimmed until the new ones arrive */
  #expensesArea[aria-busy="true"] .profit-sum, #expensesArea[aria-busy="true"] #expenseList,
  #expensesArea[aria-busy="true"] #recurringList, #expensesArea[aria-busy="true"] #expenseHistory{ opacity:.45; pointer-events:none; transition:opacity .15s ease-out; }
  .list-msg{ margin:-4px 0 12px; }
  .edit-note{ grid-column:1 / -1; font-size:12.5px; color:#1f4f87; background:#e3edf9; border-radius:8px; padding:9px 12px; margin:0; }
  .field input[aria-invalid="true"], .field select[aria-invalid="true"]{ border-color:#ba2828; box-shadow:0 0 0 1px #ba2828; }
  .month-link{ background:none; border:none; padding:6px 0; font:inherit; color:var(--green-btn); font-weight:600; text-decoration:underline; text-underline-offset:3px; cursor:pointer; text-align:left; }
  .month-link:focus-visible{ outline:2px solid var(--green-btn); outline-offset:2px; border-radius:2px; }
  .mini-btn{ border:1px solid var(--border); background:#fff; border-radius:6px; padding:5px 10px; font-size:12px; font-weight:600; color:var(--text-mid); cursor:pointer; margin-left:6px; min-height:30px; }
  .mini-btn:hover{ background:#f4f4f4; }
  .mini-btn.danger{ color:#ba2828; }
  .mini-btn.danger:hover{ background:#fbe9e9; }
  .mini-btn:focus-visible{ outline:2px solid var(--green-btn); outline-offset:1px; }
  .link-btn.muted{ color:var(--text-mid); font-weight:500; padding:4px 0; }
  .tag{ display:inline-block; font-size:11.5px; font-weight:600; padding:1px 8px; border-radius:999px; background:#f0f0f0; color:var(--text-mid); vertical-align:1px; }
  .cat{ display:inline-block; font-size:11.5px; font-weight:600; padding:2px 9px; border-radius:999px; background:#f0f0f0; color:#3d4a3e; white-space:nowrap; }
  .cat-electricity, .cat-water, .cat-internet{ background:#fff3d6; color:#7a5300; }
  .cat-salaries{ background:#e3edf9; color:#1f4f87; }
  .cat-repairs{ background:#fde8e0; color:#8f3412; }
  .cat-equipment{ background:#ece6f6; color:#563a85; }
  .cat-supplies{ background:#dcebdc; color:#194e19; }
  tr.paused td{ color:var(--text-light); }
  .sr-only{ position:absolute; width:1px; height:1px; overflow:hidden; clip:rect(0 0 0 0); white-space:nowrap; }
  @media (max-width:820px){ .expense-grid{ grid-template-columns:1fr 1fr; } }
  @media (max-width:560px){
    .expense-grid{ grid-template-columns:1fr; } .expense-grid .span-2{ grid-column:auto; } .card-form{ padding:14px; }
    .mini-btn{ min-height:44px; padding:8px 16px; }
    /* Expense and recurring lists become stacked cards, so Edit/Delete stay on screen */
    .stack-table thead{ display:none; }
    .stack-table, .stack-table tbody, .stack-table tr, .stack-table td{ display:block; width:100%; box-sizing:border-box; }
    .stack-table{ border:none; background:none; }
    .stack-table tr{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:10px 14px; margin-bottom:10px; }
    .stack-table td{ border:none; padding:5px 0; display:flex; justify-content:space-between; gap:12px; text-align:right !important; max-width:none; }
    .stack-table td::before{ content:attr(data-label); font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:var(--text-mid); text-align:left; flex-shrink:0; }
    .stack-table td.row-actions{ justify-content:flex-end; padding-top:10px; margin-top:4px; border-top:1px solid #f1f1f1; }
    .stack-table td.row-actions::before{ content:none; }
    .stack-table td.row-actions .mini-btn{ flex:1; margin-left:0; }
    .stack-table td.row-actions .mini-btn.danger{ margin-left:14px; }
  }
  /* Daily Collection */
  .cash-flow{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:6px 18px; margin-bottom:20px; max-width:560px; }
  .cash-flow .line{ display:flex; justify-content:space-between; gap:12px; padding:9px 0; font-size:13px; border-bottom:1px solid #f1f1f1; font-variant-numeric:tabular-nums; }
  .cash-flow .line.sub{ padding-left:16px; color:var(--text-mid); font-size:12.5px; }
  .cash-flow .line.minus span:last-child{ color:#ba2828; }
  .cash-flow .line.result{ font-weight:700; }
  .cash-flow .line.deposit{ border-bottom:none; margin:6px -10px 6px; padding:11px 10px; background:#dcebdc; border-radius:8px; color:#194e19; font-size:14.5px; font-weight:700; }
  .cash-flow .line.deposit.loss{ background:#fbe9e9; color:#ba2828; }
  .cash-flow .gap{ height:8px; }
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
  .stat-sub{ font-size:12px; color:var(--text-light); margin-top:6px; line-height:1.35; }
  .cell-sub{ font-size:11px; color:var(--text-light); font-style:normal; white-space:nowrap; }
  td.fc{ color:#8a5a12; }
  .fc-table td{ white-space:nowrap; }
  @media (max-width:560px){ .stats-row.fc-stats{ grid-template-columns:1fr 1fr; gap:10px; } .stats-row.fc-stats .stat-card:last-child{ grid-column:auto; } .stats-row.fc-stats .stat-card:nth-child(odd):last-child{ grid-column:1 / -1; } .fc-stats .stat-card{ padding:14px; } .fc-stats .stat-value{ font-size:18px; } }
  .fc-method{ margin:-6px 0 18px; font-size:12.5px; color:var(--text-mid); }
  .fc-method summary{ cursor:pointer; width:max-content; max-width:100%; color:var(--green-btn); font-weight:600; padding:6px 0; min-height:32px; display:flex; align-items:center; gap:6px; list-style:none; }
  .fc-method summary::-webkit-details-marker{ display:none; }
  .fc-method summary::before{ content:""; width:6px; height:6px; border:solid currentColor; border-width:0 1.5px 1.5px 0; transform:rotate(-45deg); transition:transform .15s ease-out; }
  .fc-method[open] summary::before{ transform:rotate(45deg); }
  .fc-method summary:focus-visible{ outline:2px solid var(--green-btn); outline-offset:2px; border-radius:4px; }
  .fc-method ul{ margin:4px 0 0; padding-left:18px; max-width:72ch; line-height:1.55; }
  .fc-method li{ margin-bottom:3px; }
  .chart-legend span.dash::before{ height:0; border-top:2px dashed var(--swatch); background:none; width:14px; vertical-align:3px; }
  .chart-legend span.dot::before{ height:0; border-top:2px dotted var(--swatch); background:none; width:14px; vertical-align:3px; }
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
        <button type="button" class="report-tab" data-type="daily">Daily Collection</button>
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
      <p class="occupancy-note" id="dailyNote" hidden>Payments received and expenses paid each day, and the cash left for the bank deposit. Starts on today; pick a From/To range to cover several days.</p>
      <div id="resultsArea">
        <div class="empty-note">Pick a report type and click Generate to see the numbers.</div>
      </div>

      {{-- Expenses & Profit (Pureza revision 2026-11-14): expenses are recorded
           one by one, each with an optional receipt. Recurring ones (e.g.
           salaries every payday) are recorded automatically. Every report
           (Financial, Forecast, Daily Collection) adds these up. --}}
      <div id="expensesArea" hidden>
        <div class="controls-row">
          <div class="field">
            <label for="expenseMonth">Month</label>
            <input type="month" id="expenseMonth">
          </div>
          <p class="occupancy-note" style="margin:0 0 4px;">Shows the expenses paid in this month and its net profit.</p>
        </div>

        <div class="profit-sum" aria-live="polite">
          <div class="part"><span id="collectedLabel">Collected</span><strong id="sumCollected">&#8369;0.00</strong></div>
          <div class="op" aria-hidden="true">&minus;</div>
          <div class="part"><span>Expenses</span><strong id="expenseTotal">&#8369;0.00</strong></div>
          <div class="op" aria-hidden="true">=</div>
          <div class="part result" id="sumResult"><span>Net Profit</span><strong id="sumNet">&#8369;0.00</strong></div>
        </div>
        <p class="profit-note">Collected = approved tenant payments dated within the month (rent, utilities, move-in fees, penalties).</p>

        <form id="expenseForm" class="card-form" novalidate>
          <div class="card-form-head">
            <h2 id="expenseFormTitle">Record an Expense</h2>
            <p>For bills, salaries, repairs, and purchases like furniture or appliances.</p>
          </div>
          <div class="expense-grid">
            <p class="edit-note" id="autoEditNote" hidden>This entry was recorded by a recurring schedule. Saving changes only this one payment. To change future ones, remove the schedule under Recurring Expenses and record a new one.</p>
            <div class="field">
              <label for="exCategory">Category</label>
              <select id="exCategory" required>
                @foreach(\App\Models\Expense::CATEGORIES as $key => $label)
                  <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div class="field span-2">
              <label for="exDescription">Description</label>
              <input type="text" id="exDescription" maxlength="255" required placeholder="e.g. Electric fan for Room 204">
            </div>
            <div class="field">
              <label for="exAmount">Amount (&#8369;)</label>
              <input type="number" id="exAmount" min="0.01" step="0.01" inputmode="decimal" required placeholder="0.00">
            </div>
            <div class="field">
              <label for="exDate">Date Paid</label>
              <input type="date" id="exDate" required>
            </div>
            <div class="field">
              <label for="exMethod">Paid By <x-info-tip text="Cash expenses are subtracted from the cash for bank deposit." /></label>
              <select id="exMethod">
                @foreach(\App\Models\Expense::PAYMENT_METHODS as $key => $label)
                  <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div class="field span-2">
              <label for="exReceipt">Receipt / Proof of Purchase (optional)</label>
              <input type="file" id="exReceipt" accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf">
              <span class="hint">JPG, PNG or PDF, up to 5 MB.</span>
              <div class="current-receipt" id="currentReceipt" hidden>
                <a id="currentReceiptLink" href="#" target="_blank" rel="noopener">View current receipt</a>
                <label class="inline-check"><input type="checkbox" id="exRemoveReceipt"> Remove it</label>
              </div>
            </div>
            <div class="field span-all repeat-box" id="repeatBox">
              <label class="inline-check strong"><input type="checkbox" id="exRepeat"> Repeat this expense automatically</label> <x-info-tip text="For regular costs like salaries. Each one is recorded on its due date." />
              <div class="field" id="frequencyField" hidden>
                <label for="exFrequency">How often</label>
                <select id="exFrequency">
                  @foreach(\App\Models\RecurringExpense::FREQUENCIES as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <p class="form-msg" id="expenseMsg" role="status"></p>
          <div class="expense-footer">
            <button type="button" class="btn secondary" id="cancelEditBtn" hidden>Cancel Edit</button>
            <button type="submit" class="btn primary" id="saveExpenseBtn">Save Expense</button>
          </div>
        </form>

        <div class="results-head"><h2 id="monthListTitle">Expenses</h2></div>
        <p class="form-msg list-msg" id="listMsg" role="status"></p>
        <div class="table-scroll" id="expenseList"><div class="empty-note">Loading expenses…</div></div>

        <div class="results-head"><h2>Recurring Expenses</h2></div>
        <div class="table-scroll" id="recurringList"></div>

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
  // Escapes text typed by admins (descriptions, names) before it goes into HTML.
  const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c]));

  let currentType = 'occupancy';
  let hasResults = false;
  // The type + dates the on-screen report was generated with. Export uses
  // these (not the live date inputs), so the file always matches what
  // the admin is looking at even if they edit the dates afterwards.
  let generatedParams = null;
  // Each tab keeps its own From/To dates. Daily Collection starts on today.
  const today0 = new Date();
  const todayIso = today0.getFullYear() + '-' + String(today0.getMonth() + 1).padStart(2, '0') + '-' + String(today0.getDate()).padStart(2, '0');
  const savedDates = { daily:[todayIso, todayIso] };

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
      const logoutRes = await fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
      window.location.href = (await logoutRes.json().catch(() => ({}))).redirect || '/';
    });
  }

  function toggleDateFields(){
    const isExpenses = currentType === 'expenses';
    $('reportControls').hidden = isExpenses;
    $('resultsArea').hidden = isExpenses;
    $('expensesArea').hidden = !isExpenses;
    $('exportError').classList.remove('visible');
    $('occupancyNote').style.display = currentType === 'occupancy' ? 'block' : 'none';
    $('dailyNote').hidden = currentType !== 'daily';
    // Forecast always looks at the last 6 months and the next 3, so the dates don't apply.
    $('startField').hidden = $('endField').hidden = currentType === 'forecast';
    if (isExpenses) loadExpenses();
  }

  document.querySelectorAll('.report-tab').forEach(tab => {
    tab.addEventListener('click', function(){
      document.querySelectorAll('.report-tab').forEach(t => t.classList.remove('active'));
      this.classList.add('active');
      savedDates[currentType] = [$('startDate').value, $('endDate').value];
      currentType = this.dataset.type;
      [$('startDate').value, $('endDate').value] = savedDates[currentType] || ['', ''];
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
  const C = { amber:'#b7791f', purple:'#7a4fa0', green:'#194e19', greenSoft:'rgba(25,78,25,0.12)', olive:'#8fb48f', red:'#c0504d', grid:'rgba(0,0,0,0.06)' };
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
    // Estimates are rounded to P100, so centavos would fake precision.
    const whole = v => '₱' + Math.round(v).toLocaleString('en-PH');
    const lockedNext = data.forecast[0];
    let html = `
      <div class="results-head"><h2>Forecast: ${data.range}</h2></div>
      <div class="generated-note">Generated ${data.generated_at}</div>
      ${a.expense_months === 0 ? `<div class="missing-note">No expenses recorded in the last 6 months, so expected expenses and net can't be estimated. <button type="button" class="link-btn" id="goExpenses">Record expenses</button></div>` : ''}
      <div class="stats-row money fc-stats">
        <div class="stat-card"><div class="stat-label">Expected Income (3 mo.)</div><div class="stat-value">≈ ${whole(t.income)}</div>${a.income_error_pct === null ? '' : `<div class="stat-sub">Past estimates were ${a.income_error_pct}% off</div>`}</div>
        <div class="stat-card"><div class="stat-label">Expected Expenses (3 mo.)</div><div class="stat-value">${t.expenses === null ? '&mdash;' : '≈ ' + whole(t.expenses)}</div>${t.expenses === null || t.expenses_low === t.expenses_high ? '' : `<div class="stat-sub">Range ${shortPeso(t.expenses_low)}–${shortPeso(t.expenses_high)}</div>`}</div>
        ${a.expense_months ? `<div class="stat-card"><div class="stat-label">Monthly Utilities</div><div class="stat-value">${a.min_utilities === a.max_utilities ? shortPeso(a.min_utilities) : `${shortPeso(a.min_utilities)}–${shortPeso(a.max_utilities)}`}</div><div class="stat-sub">Lowest to highest bill, last 6 months</div></div>` : ''}
        <div class="stat-card ${netClass}"><div class="stat-label">Expected Net</div><div class="stat-value">${t.net === null ? '&mdash;' : '≈ ' + whole(t.net)}</div></div>
        <div class="stat-card"><div class="stat-label">Leases Ending</div><div class="stat-value">${t.ending_leases}</div><div class="stat-sub">${lockedNext.locked_beds} of ${lockedNext.occupied} beds locked at signed rent in ${shortMonth(lockedNext.label)}</div></div>
      </div>
      <details class="fc-method">
        <summary>How this is estimated</summary>
        <ul>
          <li>Averages of the last 6 months: ${a.avg_move_ins} move-ins a month, ${whole(a.income_per_bed)} collected per occupied bed${a.expense_months ? `, ${whole(a.avg_expenses)} in expenses` : ''}.</li>
          <li>Leases ending count as move-outs. Renewals would push the numbers up.</li>
          ${a.expense_months ? `<li>Expected expenses use the lowest and highest utility bills above, so they are a range.${data.forecast.some(r => r.seasonal) ? ' Months marked * also lean on the same month last year.' : ''}</li>` : ''}
          <li>Rent is fixed for a tenant's current term (Tenant Agreement §2.2). New prices only reach new tenants and renewals.</li>
          <li>Rounded to the nearest ₱100. The date filters don't apply here.</li>
        </ul>
      </details>
      <div class="chart-card">
        <h3>Occupancy rate</h3>
        <div class="chart-legend"><span style="--swatch:${C.green}">Actual</span><span class="dash" style="--swatch:${C.green}">Estimate</span><span class="dot" style="--swatch:${C.amber}">What we forecast</span></div>
        <div class="chart-box"><canvas id="fcOccChart" role="img" aria-label="Line chart of actual and estimated occupancy rate"></canvas></div>
      </div>
      <div class="chart-card">
        <h3>Income vs. expenses</h3>
        <div class="chart-legend"><span style="--swatch:${C.olive}">Income</span><span style="--swatch:${C.red}">Expenses</span><span style="--swatch:rgba(143,180,143,0.4)">Estimated income</span><span style="--swatch:rgba(192,80,77,0.35)">Estimated expenses</span><span class="dot" style="--swatch:${C.amber}">Forecast income</span><span class="dot" style="--swatch:${C.purple}">Forecast expenses</span></div>
        <div class="chart-box"><canvas id="fcMoneyChart" role="img" aria-label="Bar chart of actual and estimated income and expenses per month"></canvas></div>
      </div>
      <div class="table-scroll"><table class="report-table fc-table">
        <thead><tr><th>Month</th><th>Kind</th><th class="num">Leases Ending</th><th class="num">Occupied</th><th class="num">Occupancy</th><th class="num">Income</th><th class="num">Forecast</th><th class="num">Expenses</th><th class="num">Forecast</th><th class="num">Net</th><th class="num">Forecast</th><th class="num">Locked Beds</th></tr></thead>
        <tbody>${rows.map(r => `<tr${r.est || r.partial ? ' style="font-style:italic"' : ''}><td>${monthLabel(r)}</td><td>${kind(r)}</td><td class="num">${r.est ? r.ending_leases : '&mdash;'}</td><td class="num">${r.occupied}</td><td class="num">${r.occupancy_rate}%</td><td class="num">${money(r.income)}</td><td class="num fc">${r.forecast_income == null ? '&mdash;' : whole(r.forecast_income)}</td><td class="num">${r.est && r.expenses === null ? '&mdash;' : cell(r.expenses)}${r.est && r.expenses !== null && r.expenses_low !== r.expenses_high ? `<div class="cell-sub">${shortPeso(r.expenses_low)}–${shortPeso(r.expenses_high)}${r.seasonal ? ' *' : ''}</div>` : ''}</td><td class="num fc">${r.forecast_expenses == null ? '&mdash;' : whole(r.forecast_expenses)}</td><td class="num ${r.net !== null && r.net < 0 ? 'neg' : ''}">${r.net === null ? '&mdash;' : money(r.net)}</td><td class="num fc">${r.forecast_income == null || r.forecast_expenses == null ? '&mdash;' : whole(r.forecast_income - r.forecast_expenses)}</td><td class="num">${r.est ? `${r.locked_beds} of ${r.occupied}` : '&mdash;'}</td></tr>`).join('')}</tbody>
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
        segment:{ borderDash: ctx => ctx.p1DataIndex >= firstEst ? [6, 4] : undefined } },
        { label:'Forecast at the time', data:rows.map(r => r.forecast_occupied == null ? null : Math.round(r.forecast_occupied / data.total_beds * 1000) / 10),
          borderColor:C.amber, backgroundColor:C.amber, borderDash:[2, 3], fill:false, tension:0, pointRadius:3, spanGaps:false }] },
      options:{ responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ display:false }, tooltip:{ ...tooltip, callbacks:{ label: c => c.datasetIndex === 1 ? ` Forecast at the time: ${c.parsed.y}% (${rows[c.dataIndex].forecast_occupied}/${data.total_beds} beds)` : ` ${kind(rows[c.dataIndex])}: ${c.parsed.y}% (${rows[c.dataIndex].occupied}/${data.total_beds} beds)` } } },
        scales:{ x:xAxis, y:{ min:0, max:100, border:{ display:false }, grid:{ color:C.grid }, ticks:{ stepSize:25, callback:v => v + '%' } } } }
    });
    const fade = (color, soft) => rows.map(r => r.est || r.partial ? soft : color);
    chartOrFallback('fcMoneyChart', {
      type:'bar',
      data:{ labels, datasets:[
        { label:'Income', data:rows.map(r => r.income), backgroundColor:fade(C.olive, 'rgba(143,180,143,0.4)'), borderRadius:4, maxBarThickness:30 },
        { label:'Expenses', data:rows.map(r => r.expenses), backgroundColor:fade(C.red, 'rgba(192,80,77,0.35)'), borderRadius:4, maxBarThickness:30 },
        { type:'line', label:'Forecast income', data:rows.map(r => r.forecast_income ?? null), borderColor:C.amber, backgroundColor:C.amber, borderDash:[2, 3], pointRadius:3, tension:0, spanGaps:false, order:-1 },
        { type:'line', label:'Forecast expenses', data:rows.map(r => r.forecast_expenses ?? null), borderColor:C.purple, backgroundColor:C.purple, borderDash:[2, 3], pointRadius:3, tension:0, spanGaps:false, order:-1 },
      ] },
      options:{ responsive:true, maintainAspectRatio:false, interaction:{ mode:'index', intersect:false },
        plugins:{ legend:{ display:false }, tooltip:{ ...tooltip, callbacks:{
          title: items => items[0].label + (rows[items[0].dataIndex].est ? ' (estimate)' : rows[items[0].dataIndex].partial ? ' (so far)' : ''),
          label: c => ` ${c.dataset.label}: ${c.parsed.y === null ? 'not recorded' : money(c.parsed.y)}` } } },
        scales:{ x:xAxis, y:{ beginAtZero:true, border:{ display:false }, grid:{ color:C.grid }, ticks:{ maxTicksLimit:5, callback:shortPeso } } } }
    });
  }

  // Daily Collection: who paid, the expenses paid the same days, and the cash
  // left for the bank. Only cash is deposited (online payments are already in
  // an account), so expenses paid by GCash/bank don't reduce it.
  function renderDaily(data){
    const r = data.range;
    const depositClass = data.cash_for_deposit < 0 ? ' loss' : '';
    let html = `
      <div class="results-head"><h2>Daily Collection: ${esc(r.label)}</h2></div>
      <div class="stats-row money">
        <div class="stat-card"><div class="stat-label">Total Collections</div><div class="stat-value">${money(data.total_collected)}</div><div class="stat-sub">${data.payments.length} payment(s)</div></div>
        <div class="stat-card"><div class="stat-label">Expenses Paid</div><div class="stat-value">${money(data.total_expenses)}</div><div class="stat-sub">${data.expenses.length} expense(s)</div></div>
        <div class="stat-card ${data.cash_for_deposit < 0 ? 'loss' : 'profit'}"><div class="stat-label">Cash for Bank Deposit</div><div class="stat-value">${signedMoney(data.cash_for_deposit)}</div>${data.cash_for_deposit < 0 ? '<div class="stat-sub">Short: more cash spent than collected</div>' : ''}</div>
      </div>
      <div class="results-head"><h2>Cash on Hand</h2></div>
      <div class="cash-flow">
        <div class="line"><span>Total collections</span><span>${money(data.total_collected)}</span></div>
        <div class="line sub"><span>Cash</span><span>${money(data.cash_collected)}</span></div>
        <div class="line sub"><span>Online (GCash / bank / other)</span><span>${money(data.online_collected)}</span></div>
        <div class="line minus"><span>Less: expenses paid</span><span>&minus;${money(data.total_expenses)}</span></div>
        <div class="line result"><span>Remaining after expenses</span><span class="${data.remaining < 0 ? 'neg' : ''}">${signedMoney(data.remaining)}</span></div>
        <div class="gap"></div>
        <div class="line"><span>Cash collected</span><span>${money(data.cash_collected)}</span></div>
        <div class="line minus"><span>Less: expenses paid in cash</span><span>&minus;${money(data.cash_expenses)}</span></div>
        <div class="line deposit${depositClass}"><span>Net cash for bank deposit</span><span>${signedMoney(data.cash_for_deposit)}</span></div>
      </div>
      ${data.cash_for_deposit < 0 ? `<div class="missing-note">More cash was spent than collected, so the cash on hand is short by ${money(Math.abs(data.cash_for_deposit))}.</div>` : ''}
      <div class="results-head"><h2>Payments Received</h2></div>
      <div class="table-scroll">${data.payments.length ? `<table class="report-table">
        <thead><tr><th>Date</th><th>Tenant</th><th>Room</th><th>Method</th><th>Reference No.</th><th class="num">Amount</th></tr></thead>
        <tbody>${data.payments.map(p => `<tr><td style="white-space:nowrap">${esc(p.date_label)}</td><td>${esc(p.tenant)}</td><td>${esc(p.room)}</td><td>${esc(p.method_label)}</td><td>${esc(p.reference || '—')}</td><td class="num">${money(p.amount)}</td></tr>`).join('')}
          <tr class="total-row"><td colspan="5">Total</td><td class="num">${money(data.total_collected)}</td></tr></tbody>
      </table>` : '<div class="empty-note">No payments received in this period.</div>'}</div>
      <div class="results-head"><h2>Expenses Paid</h2></div>
      <div class="table-scroll">${data.expenses.length ? `<table class="report-table">
        <thead><tr><th>Date</th><th>Description</th><th>Category</th><th>Paid By</th><th class="num">Amount</th></tr></thead>
        <tbody>${data.expenses.map(e => `<tr><td style="white-space:nowrap">${esc(e.date_label)}</td><td>${esc(e.description)}</td><td><span class="cat cat-${esc(e.category)}">${esc(e.category_label)}</span></td><td>${esc(e.payment_method_label)}</td><td class="num">${money(e.amount)}</td></tr>`).join('')}
          <tr class="total-row"><td colspan="4">Total</td><td class="num">${money(data.total_expenses)}</td></tr></tbody>
      </table>` : '<div class="empty-note">No expenses paid in this period.</div>'}</div>`;
    if (data.days.length > 1) {
      html += `
      <div class="results-head"><h2>Day by Day</h2></div>
      <div class="table-scroll"><table class="report-table">
        <thead><tr><th>Date</th><th class="num">Cash Collected</th><th class="num">Online Collected</th><th class="num">Expenses Paid</th><th class="num">Cash Expenses</th><th class="num">Cash for Deposit</th></tr></thead>
        <tbody>${data.days.map(d => `<tr><td style="white-space:nowrap">${esc(d.date_label)}</td><td class="num">${money(d.cash_collected)}</td><td class="num">${money(d.online_collected)}</td><td class="num">${money(d.total_expenses)}</td><td class="num">${money(d.cash_expenses)}</td><td class="num ${d.cash_for_deposit < 0 ? 'neg' : ''}"><strong>${signedMoney(d.cash_for_deposit)}</strong></td></tr>`).join('')}</tbody>
      </table></div>`;
    }
    $('resultsArea').innerHTML = html;
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
      else if(currentType === 'daily'){ renderDaily(data); }
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

  // ---- Expenses & Profit tab ----
  const now = new Date();
  const pad = n => String(n).padStart(2, '0');
  const thisMonth = now.getFullYear() + '-' + pad(now.getMonth() + 1);
  const todayStr = thisMonth + '-' + pad(now.getDate());
  $('expenseMonth').value = thisMonth;
  $('expenseMonth').max = thisMonth;
  $('exDate').max = todayStr;
  $('exDate').value = todayStr;

  let editingId = null;   // id of the expense being edited, or null when recording a new one
  let monthExpenses = [];

  function showExpenseMsg(text, ok){
    const el = $('expenseMsg');
    el.textContent = text;
    el.className = 'form-msg ' + (ok ? 'ok' : 'err');
  }
  // Messages for actions taken in the lists (delete, pause, remove) show
  // next to the lists, where the admin clicked, not up in the form.
  function showListMsg(text, ok){
    const el = $('listMsg');
    el.textContent = text;
    el.className = 'form-msg list-msg ' + (ok ? 'ok' : 'err');
    if (el.getBoundingClientRect().top < 0) el.scrollIntoView({ behavior:'smooth', block:'center' });
  }
  // Server validation errors are shown on the field they belong to.
  const FIELD_IDS = { category:'exCategory', description:'exDescription', amount:'exAmount', date_paid:'exDate', payment_method:'exMethod', receipt:'exReceipt', frequency:'exFrequency' };
  function clearFieldErrors(){
    Object.values(FIELD_IDS).forEach(id => { $(id).removeAttribute('aria-invalid'); $(id).removeAttribute('aria-describedby'); });
  }
  function markField(id){
    $(id).setAttribute('aria-invalid', 'true');
    $(id).setAttribute('aria-describedby', 'expenseMsg');
    $(id).focus();
  }
  // Client-side check failed: show the message on that field.
  function fieldError(id, text){ clearFieldErrors(); showExpenseMsg(text, false); markField(id); }
  const signedMoney = n => (n < 0 ? '-' : '') + money(Math.abs(n));

  $('exRepeat').addEventListener('change', () => { $('frequencyField').hidden = !$('exRepeat').checked; });

  function resetExpenseForm(){
    editingId = null;
    $('expenseForm').reset();
    $('exDate').value = todayStr;
    $('frequencyField').hidden = true;
    $('repeatBox').hidden = false;
    $('currentReceipt').hidden = true;
    $('autoEditNote').hidden = true;
    $('cancelEditBtn').hidden = true;
    clearFieldErrors();
    $('expenseFormTitle').textContent = 'Record an Expense';
    $('saveExpenseBtn').textContent = 'Save Expense';
  }
  $('cancelEditBtn').addEventListener('click', () => { resetExpenseForm(); $('expenseMsg').className = 'form-msg'; });

  function startEdit(id){
    const e = monthExpenses.find(x => x.id === id);
    if (!e) return;
    resetExpenseForm();
    editingId = id;
    $('exCategory').value = e.category;
    $('exDescription').value = e.description;
    $('exAmount').value = e.amount;
    $('exDate').value = e.date_paid;
    $('exMethod').value = e.payment_method;
    // Repeating is set up when recording; an edit changes only this one expense.
    $('repeatBox').hidden = true;
    $('autoEditNote').hidden = !e.is_recurring;
    $('currentReceipt').hidden = !e.receipt_url;
    if (e.receipt_url) $('currentReceiptLink').href = e.receipt_url;
    $('cancelEditBtn').hidden = false;
    $('expenseFormTitle').textContent = 'Edit Expense';
    $('saveExpenseBtn').textContent = 'Save Changes';
    $('expenseMsg').className = 'form-msg';
    $('expenseForm').scrollIntoView({ behavior:'smooth', block:'start' });
    $('exDescription').focus({ preventScroll:true });
  }

  function renderExpenseList(rows, label){
    $('monthListTitle').textContent = 'Expenses in ' + label;
    if (!rows.length) {
      $('expenseList').innerHTML = `<div class="empty-note">No expenses recorded for ${esc(label)} yet.</div>`;
      return;
    }
    $('expenseList').innerHTML = `<table class="report-table stack-table">
      <thead><tr><th>Date Paid</th><th>Description</th><th>Category</th><th>Paid By</th><th class="num">Amount</th><th>Receipt</th><th><span class="sr-only">Actions</span></th></tr></thead>
      <tbody>${rows.map(e => `<tr>
        <td data-label="Date Paid" style="white-space:nowrap">${esc(e.date_label)}</td>
        <td data-label="Description" class="desc-cell">${esc(e.description)}${e.is_recurring ? ' <span class="tag" title="Recorded by a recurring schedule">Auto</span>' : ''}</td>
        <td data-label="Category"><span class="cat cat-${esc(e.category)}">${esc(e.category_label)}</span></td>
        <td data-label="Paid By">${esc(e.payment_method_label)}</td>
        <td data-label="Amount" class="num">${money(e.amount)}</td>
        <td data-label="Receipt">${e.receipt_url ? `<a href="${esc(e.receipt_url)}" target="_blank" rel="noopener" aria-label="View receipt for ${esc(e.description)}">View</a>` : `<button type="button" class="link-btn muted" data-edit="${e.id}" aria-label="Attach a receipt to ${esc(e.description)}">None · Attach</button>`}</td>
        <td class="row-actions"><button type="button" class="mini-btn" data-edit="${e.id}" aria-label="Edit ${esc(e.description)}">Edit</button><button type="button" class="mini-btn danger" data-delete="${e.id}" aria-label="Delete ${esc(e.description)}">Delete</button></td>
      </tr>`).join('')}</tbody>
    </table>`;
    $('expenseList').querySelectorAll('[data-edit]').forEach(b => b.addEventListener('click', () => startEdit(Number(b.dataset.edit))));
    $('expenseList').querySelectorAll('[data-delete]').forEach(b => b.addEventListener('click', () => deleteExpense(Number(b.dataset.delete))));
  }

  function renderRecurring(rows){
    if (!rows.length) {
      $('recurringList').innerHTML = '<div class="empty-note">No recurring expenses yet. Turn on "Repeat this expense automatically" when recording one, e.g. a salary.</div>';
      return;
    }
    $('recurringList').innerHTML = `<table class="report-table stack-table">
      <thead><tr><th>Description</th><th>Category</th><th class="num">Amount</th><th>How Often</th><th>Next Date</th><th><span class="sr-only">Actions</span></th></tr></thead>
      <tbody>${rows.map(r => `<tr${r.active ? '' : ' class="paused"'}>
        <td data-label="Description" class="desc-cell">${esc(r.description)}</td>
        <td data-label="Category"><span class="cat cat-${esc(r.category)}">${esc(r.category_label)}</span></td>
        <td data-label="Amount" class="num">${money(r.amount)}</td>
        <td data-label="How Often">${esc(r.frequency_label)}</td>
        <td data-label="Next Date" style="white-space:nowrap">${r.active ? esc(r.next_date) : '<span class="tag">Paused</span>'}</td>
        <td class="row-actions"><button type="button" class="mini-btn" data-toggle="${r.id}" data-active="${r.active ? 0 : 1}" aria-label="${r.active ? 'Pause' : 'Resume'} ${esc(r.description)}">${r.active ? 'Pause' : 'Resume'}</button><button type="button" class="mini-btn danger" data-remove="${r.id}" aria-label="Remove schedule for ${esc(r.description)}">Remove</button></td>
      </tr>`).join('')}</tbody>
    </table>`;
    $('recurringList').querySelectorAll('[data-toggle]').forEach(b => b.addEventListener('click', () => sendJson('PATCH', '/reports/recurring-expenses/' + b.dataset.toggle, { active: b.dataset.active === '1' })));
    $('recurringList').querySelectorAll('[data-remove]').forEach(b => b.addEventListener('click', () => {
      if (confirm('Stop this recurring expense? Expenses it already recorded will be kept.')) sendJson('DELETE', '/reports/recurring-expenses/' + b.dataset.remove);
    }));
  }

  function renderExpenseHistory(rows){
    if (!rows.length) {
      $('expenseHistory').innerHTML = '<div class="empty-note">No months recorded yet.</div>';
      return;
    }
    $('expenseHistory').innerHTML = `<table class="report-table">
      <thead><tr><th>Month</th><th class="num">Collected</th><th class="num">Utilities</th><th class="num">Salaries</th><th class="num">Other Expenses</th><th class="num">Total Expenses</th><th class="num">Net Profit</th></tr></thead>
      <tbody>${rows.map(r => `<tr><td><button type="button" class="month-link" data-month="${r.month}" aria-label="Show expenses for ${r.label}">${r.label}</button></td><td class="num">${money(r.collected)}</td><td class="num">${money(r.utilities)}</td><td class="num">${money(r.salaries)}</td><td class="num">${money(r.total - r.utilities - r.salaries)}</td><td class="num">${money(r.total)}</td><td class="num ${r.net < 0 ? 'neg' : ''}"><strong>${signedMoney(r.net)}</strong></td></tr>`).join('')}</tbody>
    </table>
    <p class="profit-note" style="margin-top:8px;">Utilities = electricity, water and internet. Other expenses = repairs, furniture/appliances/equipment, supplies and others. Pick a month to see its expenses.</p>`;
    $('expenseHistory').querySelectorAll('[data-month]').forEach(b => b.addEventListener('click', () => {
      $('expenseMonth').value = b.dataset.month;
      loadExpenses();
      $('monthListTitle').scrollIntoView({ behavior:'smooth', block:'start' });
    }));
  }

  async function loadExpenses(){
    const area = $('expensesArea');
    area.setAttribute('aria-busy', 'true');
    $('expenseMonth').disabled = true;
    try {
      const res = await fetch('/reports/expenses?month=' + encodeURIComponent($('expenseMonth').value || thisMonth), { headers:{ 'Accept':'application/json' } });
      if (!res.ok) throw new Error();
      const data = await res.json();
      monthExpenses = data.expenses;
      $('collectedLabel').textContent = 'Collected in ' + data.label;
      $('sumCollected').textContent = money(data.collected);
      $('expenseTotal').textContent = money(data.total);
      $('sumNet').textContent = signedMoney(data.net);
      $('sumResult').classList.toggle('loss', data.net < 0);
      renderExpenseList(data.expenses, data.label);
      renderRecurring(data.recurring);
      renderExpenseHistory(data.history);
    } catch(e){
      showListMsg('Could not load expenses. Check your connection, then pick the month again or refresh the page.', false);
    } finally {
      area.removeAttribute('aria-busy');
      $('expenseMonth').disabled = false;
    }
  }
  $('expenseMonth').addEventListener('change', () => { $('expenseMsg').className = 'form-msg'; $('listMsg').className = 'form-msg list-msg'; loadExpenses(); });

  // Sends a JSON request (delete, pause/resume), then reloads the tab.
  async function sendJson(method, url, body){
    try {
      const res = await fetch(url, {
        method,
        headers:{ 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN':csrf },
        body: body ? JSON.stringify(body) : undefined,
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok) throw new Error(data.message || 'Something went wrong. Please try again.');
      await loadExpenses();
      showListMsg(data.message, true);
    } catch(err){
      showListMsg(err.message, false);
    }
  }

  function deleteExpense(id){
    const e = monthExpenses.find(x => x.id === id);
    if (!e || !confirm(`Delete "${e.description}" (${money(e.amount)})?${e.receipt_url ? ' Its receipt will be deleted too.' : ''}`)) return;
    if (editingId === id) resetExpenseForm();
    sendJson('DELETE', '/reports/expenses/' + id);
  }

  $('expenseForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const amount = Number($('exAmount').value);
    clearFieldErrors();
    if (!$('exDescription').value.trim()) return fieldError('exDescription', 'Please describe the expense.');
    if (!Number.isFinite(amount) || amount <= 0) return fieldError('exAmount', 'Enter an amount greater than zero.');
    if (amount > 9999999) return fieldError('exAmount', 'That amount is too large. Please check it.');
    if (!$('exDate').value) return fieldError('exDate', 'Pick the date it was paid.');
    if ($('exDate').value > todayStr) return fieldError('exDate', 'The date paid can\'t be in the future.');
    const file = $('exReceipt').files[0];
    if (file && file.size > 5 * 1024 * 1024) return fieldError('exReceipt', 'The receipt must be 5 MB or smaller. Try a smaller photo or a PDF.');

    // FormData (not JSON) so the receipt file can go along with the fields.
    const form = new FormData();
    form.append('category', $('exCategory').value);
    form.append('description', $('exDescription').value.trim());
    form.append('amount', amount);
    form.append('date_paid', $('exDate').value);
    form.append('payment_method', $('exMethod').value);
    if (file) form.append('receipt', file);
    if (editingId) {
      if ($('exRemoveReceipt').checked) form.append('remove_receipt', '1');
    } else if ($('exRepeat').checked) {
      form.append('repeat', '1');
      form.append('frequency', $('exFrequency').value);
    }

    const btn = $('saveExpenseBtn');
    btn.disabled = true; btn.textContent = 'Saving...';
    try {
      const res = await fetch(editingId ? '/reports/expenses/' + editingId : '/reports/expenses', {
        method:'POST',
        headers:{ 'Accept':'application/json', 'X-CSRF-TOKEN':csrf },
        body:form,
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok) {
        if (data.errors) {
          const keys = Object.keys(data.errors);
          showExpenseMsg(keys.map(k => data.errors[k][0]).join(' '), false);
          keys.slice().reverse().forEach(k => { if (FIELD_IDS[k]) markField(FIELD_IDS[k]); });
          throw null;
        }
        throw new Error(data.message || 'Could not save. Please check the details and try again.');
      }
      // Jump to the month the expense belongs to, so it shows in the list.
      $('expenseMonth').value = $('exDate').value.slice(0, 7);
      resetExpenseForm();
      await loadExpenses();
      showExpenseMsg(data.message, true);
    } catch(err){
      if (err) showExpenseMsg(err.message, false);
    }
    btn.disabled = false;
    btn.textContent = editingId ? 'Save Changes' : 'Save Expense';
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