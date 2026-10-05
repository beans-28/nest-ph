<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Billing and Payments · {{ $brandDormName }}</title>
<link rel="icon" href="{{ $brandFaviconUrl }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
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
    --font-body:'Roboto',-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;
  }
  /* Page-specific overrides of shared topbar/content-header rules from admin.css:
     this page uses a lighter (non-translucent) topbar icon and tighter content/page-head spacing. */
  .topbar-icon{ width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.9); color:var(--green-dark); display:flex; align-items:center; justify-content:center; cursor:pointer; }
  .topbar-icon svg{ width:16px; height:16px; }

  .content{ padding:24px 28px 40px 28px; flex:1; }
  .page-head{ display:flex; align-items:center; gap:12px; margin-bottom:20px; }
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
  .tab-item{ padding:10px 2px 14px; font-size:14px; font-family:inherit; font-weight:600; color:var(--text-light); background:none; border:none; border-bottom:3px solid transparent; cursor:pointer; margin-bottom:-1px; }
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

  .proof-open{ display:block; width:100%; padding:0; border:none; background:none; border-radius:10px; cursor:zoom-in; }
  .proof-open:focus-visible{ outline:2px solid var(--green-accent); outline-offset:3px; }
  .proof-preview{ width:100%; border-radius:10px; border:1px solid var(--border); display:block; }

  /* Full-screen proof of payment viewer */
  .img-viewer{ display:none; position:fixed; inset:0; background:rgba(12,16,13,.92); z-index:100; overflow:hidden; touch-action:none; }
  .img-viewer.open{ display:block; }
  .img-viewer img{ position:absolute; top:50%; left:50%; max-width:92vw; max-height:calc(100vh - 150px); transform:translate(-50%,-50%); transform-origin:center center; user-select:none; -webkit-user-drag:none; border-radius:4px; box-shadow:0 12px 40px rgba(0,0,0,.45); }
  .img-viewer.zoomed img{ cursor:grab; }
  .img-viewer img.dragging{ cursor:grabbing; }
  .img-viewer.loading img, .img-viewer.failed img{ visibility:hidden; }
  .iv-status{ position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); color:#e8efe9; font-size:13px; text-align:center; display:none; }
  .img-viewer.loading .iv-status, .img-viewer.failed .iv-status{ display:block; }
  .iv-toolbar{ position:absolute; bottom:max(20px, env(safe-area-inset-bottom)); left:50%; transform:translateX(-50%); display:flex; gap:4px; align-items:center; background:rgba(0,0,0,.7); padding:6px; border-radius:12px; z-index:2; }
  .iv-btn{ display:inline-flex; align-items:center; justify-content:center; gap:6px; background:transparent; color:#fff; border:none; border-radius:8px; min-width:44px; height:44px; padding:0 12px; font-size:13px; font-weight:600; font-family:var(--font-body); cursor:pointer; }
  .iv-btn svg{ width:18px; height:18px; flex:none; }
  .iv-btn:hover:not(:disabled){ background:rgba(255,255,255,.14); }
  .iv-btn:disabled{ opacity:.45; cursor:default; }
  .iv-btn:focus-visible, .iv-close:focus-visible{ outline:2px solid #a2d9a4; outline-offset:2px; }
  .iv-divider{ width:1px; height:24px; background:rgba(255,255,255,.2); margin:0 4px; }
  .iv-zoom{ color:#fff; font-size:12px; min-width:46px; text-align:center; font-variant-numeric:tabular-nums; }
  .iv-close{ position:absolute; top:max(16px, env(safe-area-inset-top)); right:16px; z-index:2; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,.7); color:#fff; border:none; border-radius:50%; width:44px; height:44px; cursor:pointer; }
  .iv-close:hover{ background:rgba(0,0,0,.9); }
  .iv-close svg{ width:20px; height:20px; }
  @media (max-width:520px){ .iv-btn .iv-label{ display:none; } }
  .stmt-receipt{ font-size:11.5px; font-weight:700; color:var(--green-accent); text-decoration:underline; text-underline-offset:2px; white-space:nowrap; }
  .btn-text-danger{ background:none; border:none; padding:6px 4px; font-size:12px; font-weight:600; color:#b3261e; text-decoration:underline; text-underline-offset:2px; cursor:pointer; font-family:var(--font-body); }
  .btn-text-danger:hover{ color:#8c1d13; }
  .btn-text-danger:focus-visible{ outline:2px solid #b3261e; outline-offset:2px; border-radius:4px; }
  .dupe-warning{ background:#fdecea; border:1px solid #f3b7b1; color:#8c1d13; border-radius:10px; padding:12px 14px; margin-bottom:18px; font-size:12.5px; display:flex; flex-direction:column; gap:4px; }
  .dupe-warning ul{ margin:2px 0; padding-left:18px; }
  .dupe-flag{ display:inline-block; margin-left:6px; padding:1px 7px; border-radius:999px; background:#fdecea; color:#8c1d13; font-size:10.5px; font-weight:700; vertical-align:middle; }
  .doc-links{ display:flex; flex-direction:column; gap:4px; align-items:flex-start; }
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

@include('partials.admin-sidebar')

  <div class="main">
    <div class="topbar">
      <div class="hamburger" id="hamburgerBtn" tabindex="0" aria-label="Toggle sidebar"><span></span><span></span><span></span></div>
      <div class="topbar-right">
        <div class="topbar-icon" tabindex="0" aria-label="Account"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
      </div>
    </div>

    <div class="content">
      <div class="page-head">
        <div class="back-arrow" data-href="{{ route('dashboard') }}" tabindex="0" aria-label="Back to dashboard"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
        <h1>Billing and Payments</h1>
      </div>

      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 11H4M20 7H4"/><path d="M7 21V4a1 1 0 011-1h4a1 1 0 010 12H7"/></svg></div>
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

      <div class="tabs-row" id="tabsRow" role="tablist" aria-label="Billing sections">
        <div class="tabs-left">
          <button type="button" class="tab-item active" role="tab" aria-selected="true" data-tab="overview">Billing Overview</button>
          <button type="button" class="tab-item" role="tab" aria-selected="false" data-tab="pending">Pending Payment</button>
          <button type="button" class="tab-item" role="tab" aria-selected="false" data-tab="penalties">Penalties</button>
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
            <option value="late_payment">Late payment</option>
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

      @include('partials.demo-tools', [
        'tool' => 'reservations',
        'hint' => 'Half-paid move-in fees. "Skip past deadline" moves the first payment back a month and runs the daily reservation check, so the bed is released and the tenant is emailed.',
        'actions' => [
          ['key' => 'expire', 'label' => 'Skip past deadline', 'needsItem' => true],
          ['key' => 'sweep', 'label' => 'Run daily reservation check', 'needsItem' => false],
        ],
      ])

    </div>
  </div>
</div>

<div class="overlay" id="overlay"></div>

<div class="drawer" id="drawer" role="dialog" aria-modal="true" aria-labelledby="drawerTitle">
  <div class="drawer-head">
    <h2 id="drawerTitle">Payment</h2>
    <button class="drawer-close" id="drawerClose">&times;</button>
  </div>
  <div class="drawer-body" id="drawerBody"></div>
</div>

<div class="img-viewer" id="imgViewer" role="dialog" aria-modal="true" aria-label="Proof of payment viewer" aria-hidden="true">
  <button type="button" class="iv-close" id="ivClose" aria-label="Close viewer"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
  <img id="ivImg" src="" alt="Proof of payment, full size">
  <div class="iv-status" id="ivStatus" role="status" aria-live="polite"></div>
  <div class="iv-toolbar" role="toolbar" aria-label="Image controls">
    <button type="button" class="iv-btn" id="ivZoomOut" aria-label="Zoom out"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3M8 11h6"/></svg></button>
    <span class="iv-zoom" id="ivZoomLabel" aria-live="polite">100%</span>
    <button type="button" class="iv-btn" id="ivZoomIn" aria-label="Zoom in"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3M8 11h6M11 8v6"/></svg></button>
    <button type="button" class="iv-btn" id="ivReset" aria-label="Reset zoom"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg><span class="iv-label">Reset</span></button>
    <span class="iv-divider" aria-hidden="true"></span>
    <button type="button" class="iv-btn" id="ivDownload" aria-label="Download image"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12M7 10l5 5 5-5M5 21h14"/></svg><span class="iv-label" id="ivDownloadLabel">Download</span></button>
  </div>
</div>

<div class="modal-overlay" id="recordPaymentModal">
  <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="recordPaymentModalTitle">
    <div class="modal-head"><h2 id="recordPaymentModalTitle">Record Cash Payment</h2></div>
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
  <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="recordDamageModalTitle">
    <div class="modal-head"><h2 id="recordDamageModalTitle">Record Damage</h2></div>
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
  <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="addPenaltyModalTitle">
    <div class="modal-head"><h2 id="addPenaltyModalTitle">Add Penalty</h2></div>
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
        <label for="apChargeSelect">Charge from your fee schedule</label>
        <select id="apChargeSelect"></select>
        <p style="font-size:11.5px;color:var(--text-light);margin:4px 0 0;">Your Payments and Fees Schedule says no charge applies unless it is listed. Manage the list in Dormitory Profile &gt; Other Charges.</p>
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
  <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="waivePenaltyModalTitle">
    <div class="modal-head"><h2 id="waivePenaltyModalTitle">Waive Penalty</h2></div>
    <div class="modal-body">
      <p id="waiveModalNote" style="font-size:12.5px;color:var(--text-mid);margin:0 0 14px 0;">This penalty will be marked as waived. It stays on record and can be reinstated later if needed.</p>
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

<div class="modal-overlay" id="editPenaltyModal">
  <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="editPenaltyModalTitle">
    <div class="modal-head"><h2 id="editPenaltyModalTitle">Edit Penalty</h2></div>
    <div class="modal-body">
      <p id="editPenaltyNote" style="font-size:12.5px;color:var(--text-mid);margin:0 0 14px 0;"></p>
      <div class="fld">
        <label for="editPenaltyDescription">Description</label>
        <input type="text" id="editPenaltyDescription" maxlength="255">
      </div>
      <div class="fld">
        <label for="editPenaltyAmount">Amount (₱)</label>
        <input type="number" id="editPenaltyAmount" min="0.01" step="0.01">
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn primary" id="editPenaltySubmitBtn" style="flex:1;">Save Changes</button>
      <button class="btn" id="editPenaltyCancelBtn">Cancel</button>
    </div>
  </div>
</div>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script type="application/json" id="pending-data">{!! json_encode($pending) !!}</script>
<script type="application/json" id="overview-data">{!! json_encode($overview) !!}</script>
<script type="application/json" id="charges-data">{!! json_encode($charges) !!}</script>

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
      $('tabsRow').querySelectorAll('[data-tab]').forEach(x => {
        const active = x === t;
        x.classList.toggle('active', active);
        x.setAttribute('aria-selected', active ? 'true' : 'false');
      });
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
        <td><span class="method-text ${p.payment_method}">${esc(p.payment_method_label || (METHOD_LABEL[p.payment_method] ?? p.payment_method))}</span></td>
        <td>${peso(p.amount_paid)}</td>
        <td>${esc(p.reference_number ?? 'N/A')}${p.duplicates && p.duplicates.length ? '<span class="dupe-flag" title="This reference number is used on another payment">Duplicate</span>' : ''}</td>
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
          ? `<button type="button" class="proof-open" id="proofPreviewImg" aria-label="View proof of payment full size"><img class="proof-preview" src="${p.proof_url}" alt=""></button>`
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
          <div><div class="k">Payment Method</div>${val(p.payment_method_label || (METHOD_LABEL[p.payment_method] ?? p.payment_method))}</div>
          <div><div class="k">Amount Paid</div><span class="v">${peso(p.amount_paid)}</span></div>
          <div><div class="k">Reference</div>${val(p.reference_number)}</div>
        </div>
      </div>

      <div class="sec">
        <h3>Notes</h3>
        <div class="kv"><div>${val(p.notes)}</div></div>
      </div>

      ${p.duplicates && p.duplicates.length ? `
      <div class="dupe-warning" role="alert">
        <strong>Reference number already used</strong>
        <span>This reference number also appears on:</span>
        <ul>${p.duplicates.map(d => `<li>${esc(d.tenant_name)} · ${peso(d.amount_paid)} · ${esc(d.date ?? '—')} · ${esc(d.status)}</li>`).join('')}</ul>
        <span>Check the proof carefully before approving. It may be a reused screenshot.</span>
      </div>` : ''}

      <div class="sec">
        <h3>Proof of Payment</h3>
        ${proofHtml}
      </div>

      ${p.billing_type === 'move_in' ? `
      <div class="sec">
        <h3>Tenant Documents</h3>
        <div class="doc-links">
          ${p.id_document_url ? `<a class="proof-preview-link" href="${p.id_document_url}" target="_blank" rel="noopener">View valid ID →</a>` : '<span class="v empty-v">No ID on file</span>'}
          ${p.signed_contract_url ? `<a class="proof-preview-link" href="${p.signed_contract_url}" target="_blank" rel="noopener">View signed contract →</a>` : '<span class="v empty-v">No signed contract on file</span>'}
        </div>
      </div>` : ''}

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

    if($('proofPreviewImg')) $('proofPreviewImg').addEventListener('click', () => window.openImageViewer(p.proof_url,
      'proof-of-payment-' + String(p.tenant_name || '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')));
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
   * Use Case Report Table 21 (View Payment History): "select a specific
   * transaction to view details" -- scoped here to one statement's own
   * breakdown and payment history, rather than a separate cross-statement
   * transaction ledger.
   */
  function openStatementDrawer(id){
    const o = overview.find(x => x.id === id);
    if(!o) return;

    $('drawerTitle').textContent = `Statement: ${o.tenant_name}`;

    const historyHtml = o.payments.length === 0
      ? '<div class="stmt-history-empty">No payments recorded against this statement yet.</div>'
      : o.payments.map(p => `
          <div class="stmt-history-row">
            <span>${esc(p.date ?? '—')}</span>
            <span>${esc(p.payment_method_label || (PAYMENT_METHOD_LABEL[p.payment_method] ?? p.payment_method))}</span>
            <span class="status-pill ${p.status === 'approved' ? 'paid' : (p.status === 'rejected' ? 'overdue' : 'unpaid')}">${esc(p.status)}</span>
            <span class="amt">${peso(p.amount_paid)}</span>
            ${p.status === 'approved' ? `<a class="stmt-receipt" href="/payments/${p.id}/receipt" aria-label="Download receipt PDF">Receipt</a>` : ''}
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
      ${o.status !== 'paid' && o.unbilled_penalties > 0 ? `
      <div class="sec">
        <h3>Penalties Not Yet Billed</h3>
        <p style="font-size:12.5px;color:var(--text-mid);margin:0 0 10px;">${esc(o.tenant_name)} has ${peso(o.unbilled_penalties)} in penalties that will be charged on the next bill. You can add them to this one instead.</p>
        <button class="btn" id="attachPenaltiesBtn">Add ${peso(o.unbilled_penalties)} to this statement</button>
      </div>` : ''}

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

    if($('attachPenaltiesBtn')) $('attachPenaltiesBtn').addEventListener('click', async function(){
      this.disabled = true;
      try {
        const result = await api(`/billing/${o.id}/attach-penalties`, { method:'POST' });
        toast(result.message);
        setTimeout(() => window.location.reload(), 900);
      } catch(e){
        toast(e.message, true);
        this.disabled = false;
      }
    });

    $('overlay').classList.add('open');
    $('drawer').classList.add('open');
  }

  renderOverviewTable();

  // ===== Generate Billing =====
  // Matches Use Case Report Table 18 ("Generate Billing Statement"). Runs
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
  const TYPE_LABEL = { damage:'Damage', manual:'Manual', other:'Other', late_payment:'Late payment' };

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
            ${p.status === 'active' ? `<button class="btn sm" data-edit-penalty="${p.id}">Edit</button>` : ''}
            ${p.status === 'active' ? `<button class="btn sm warn" data-waive="${p.id}">Waive</button>` : ''}
            ${p.status === 'waived' ? `<button class="btn sm" data-reinstate="${p.id}">Reinstate</button>` : ''}
            ${!p.billing_id ? `<button class="btn-text-danger" data-delete-penalty="${p.id}" title="Only for mistakes, e.g. added to the wrong tenant">Delete</button>` : ''}
          </div>
        </td>
      </tr>
    `).join('');

    $('penaltiesTableBody').querySelectorAll('[data-waive]').forEach(btn => {
      btn.addEventListener('click', () => openWaiveModal(Number(btn.dataset.waive), 'waive'));
    });
    $('penaltiesTableBody').querySelectorAll('[data-reinstate]').forEach(btn => {
      btn.addEventListener('click', () => openWaiveModal(Number(btn.dataset.reinstate), 'reinstate'));
    });
    $('penaltiesTableBody').querySelectorAll('[data-edit-penalty]').forEach(btn => {
      btn.addEventListener('click', () => openEditPenaltyModal(Number(btn.dataset.editPenalty)));
    });
    $('penaltiesTableBody').querySelectorAll('[data-delete-penalty]').forEach(btn => {
      btn.addEventListener('click', () => deletePenalty(Number(btn.dataset.deletePenalty)));
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

  // ===== Waive / Reinstate modal (same form, both need a reason for the audit log) =====
  let waivingPenaltyId = null;
  let waiveMode = 'waive';
  const WAIVE_MODES = {
    waive: {
      title: 'Waive Penalty', button: 'Confirm Waive', btnClass: 'btn warn', done: 'Penalty waived.',
      note: 'This penalty will be marked as waived. It stays on record and can be reinstated later if needed.',
      placeholder: 'Explain why this penalty is being waived...',
    },
    reinstate: {
      title: 'Reinstate Penalty', button: 'Confirm Reinstate', btnClass: 'btn primary', done: 'Penalty reinstated.',
      note: 'This penalty will be charged again. If it is on a bill, that bill\'s total goes back up.',
      placeholder: 'Explain why this penalty is being reinstated...',
    },
  };

  function openWaiveModal(id, mode){
    waivingPenaltyId = id;
    waiveMode = mode;
    const m = WAIVE_MODES[mode];
    $('waivePenaltyModalTitle').textContent = m.title;
    $('waiveModalNote').textContent = m.note;
    $('waiveReasonInput').placeholder = m.placeholder;
    $('waiveSubmitBtn').textContent = m.button;
    $('waiveSubmitBtn').className = m.btnClass;
    $('waiveReasonInput').value = '';
    $('waivePenaltyModal').classList.add('open');
  }
  $('waiveCancelBtn').addEventListener('click', () => $('waivePenaltyModal').classList.remove('open'));

  $('waiveSubmitBtn').addEventListener('click', async function(){
    const reason = $('waiveReasonInput').value.trim();
    if(!reason) return toast(`A reason is required to ${waiveMode} a penalty.`, true);

    this.disabled = true;
    try {
      await api(`/penalties/${waivingPenaltyId}/${waiveMode}`, {
        method:'PATCH',
        headers:{ 'Content-Type':'application/json' },
        body: JSON.stringify({ reason }),
      });
      $('waivePenaltyModal').classList.remove('open');
      toast(WAIVE_MODES[waiveMode].done);
      loadPenalties();
    } catch(e){ toast(e.message, true); }
    this.disabled = false;
  });

  // ===== Edit penalty modal =====
  let editingPenaltyId = null;

  function openEditPenaltyModal(id){
    const p = penalties.find(x => x.id === id);
    if(!p) return;
    editingPenaltyId = id;
    $('editPenaltyDescription').value = p.description ?? '';
    $('editPenaltyAmount').value = Number(p.amount).toFixed(2);
    $('editPenaltyNote').textContent = p.billing_id
      ? 'This penalty is already on a billing statement. Changing the amount updates that bill\'s total too.'
      : 'This penalty is not on a bill yet.';
    $('editPenaltyModal').classList.add('open');
  }
  $('editPenaltyCancelBtn').addEventListener('click', () => $('editPenaltyModal').classList.remove('open'));

  $('editPenaltySubmitBtn').addEventListener('click', async function(){
    const description = $('editPenaltyDescription').value.trim();
    const amount = Number($('editPenaltyAmount').value);
    if(!description) return toast('Please enter a description.', true);
    if(!(amount > 0)) return toast('Please enter an amount greater than zero.', true);

    this.disabled = true;
    try {
      await api(`/penalties/${editingPenaltyId}`, {
        method:'PATCH',
        headers:{ 'Content-Type':'application/json' },
        body: JSON.stringify({ description, amount }),
      });
      $('editPenaltyModal').classList.remove('open');
      toast('Penalty updated.');
      loadPenalties();
    } catch(e){ toast(e.message, true); }
    this.disabled = false;
  });

  // Delete is for genuine mistakes only (e.g. wrong tenant). The server
  // refuses once the penalty is on a bill; those must be waived instead.
  async function deletePenalty(id){
    const p = penalties.find(x => x.id === id);
    if(!p) return;
    if(!confirm(`Delete this ${peso(p.amount)} penalty for ${p.tenant?.full_name ?? 'this tenant'}?\n\nUse this only for mistakes. It removes the penalty and its history completely. To cancel a real penalty, use Waive instead.`)) return;
    try {
      await api(`/penalties/${id}`, { method:'DELETE' });
      toast('Penalty deleted.');
      loadPenalties();
    } catch(e){ toast(e.message, true); }
  }

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
      $('rdRoomBedDisplay').textContent = `Room ${result.room.room_no}, ${result.bed.bed_label}`;
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

  // Presets from Dormitory Profile > Other Charges. Picking one fills in
  // the reason and (when it has a fixed amount) the amount.
  const CHARGES = JSON.parse(document.getElementById('charges-data').textContent || '[]');
  $('apChargeSelect').innerHTML = '<option value="">Other (type a reason)</option>' +
    CHARGES.map(c => `<option value="${c.id}">${esc(c.name)} — ${esc(c.amount_label)}</option>`).join('');
  $('apChargeSelect').addEventListener('change', function(){
    const c = CHARGES.find(x => String(x.id) === this.value);
    if(!c) return;
    $('apDescriptionInput').value = c.name;
    $('apAmountInput').value = c.amount !== null ? c.amount : '';
    if(c.amount === null) $('apAmountInput').focus();
  });

  function resetAddPenaltyModal(){
    $('apChargeSelect').value = '';
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

  document.querySelectorAll('.sidebar [tabindex="0"], .hamburger, .topbar-icon, .back-arrow').forEach(el => {
    el.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        el.click();
      }
    });
  });

  // ===== Proof of payment image viewer (zoom, drag, pinch, download) =====
  const $ = id => document.getElementById(id);
  const viewer = $('imgViewer'), viewerImg = $('ivImg');
  let ivScale = 1, ivX = 0, ivY = 0, ivDrag = null, ivPinch = null;
  let ivName = 'proof-of-payment', ivReturnFocus = null;
  const ivPointers = new Map();
  const IV_MIN = 0.5, IV_MAX = 5;

  function ivApply(){
    viewerImg.style.transform = `translate(calc(-50% + ${ivX}px), calc(-50% + ${ivY}px)) scale(${ivScale})`;
    $('ivZoomLabel').textContent = Math.round(ivScale * 100) + '%';
    viewer.classList.toggle('zoomed', ivScale > 1);
    $('ivZoomOut').disabled = ivScale <= IV_MIN;
    $('ivZoomIn').disabled = ivScale >= IV_MAX;
  }
  function ivSetScale(next){
    ivScale = Math.min(IV_MAX, Math.max(IV_MIN, next));
    // Not zoomed in = nothing to pan, so keep the image centred
    if(ivScale <= 1){ ivX = 0; ivY = 0; }
    ivApply();
  }
  function ivZoom(factor){ ivSetScale(ivScale * factor); }
  function ivReset(){ ivScale = 1; ivX = 0; ivY = 0; ivApply(); }

  // name: used for the downloaded file, e.g. "proof-of-payment-patricia-mae-gonzales"
  function openImageViewer(src, name){
    ivReturnFocus = document.activeElement;
    ivName = name || 'proof-of-payment';
    viewer.classList.remove('failed');
    viewer.classList.add('loading');
    $('ivStatus').textContent = 'Loading image…';
    $('ivDownload').disabled = true;
    viewerImg.src = src;
    ivReset();
    viewer.classList.add('open');
    viewer.setAttribute('aria-hidden', 'false');
    $('ivClose').focus();
  }
  // Exposed on window so the drawer script (a separate <script> block) can call it
  window.openImageViewer = openImageViewer;
  function closeImageViewer(){
    viewer.classList.remove('open');
    viewer.setAttribute('aria-hidden', 'true');
    // Put keyboard focus back on the thumbnail the admin opened it from
    if(ivReturnFocus && document.contains(ivReturnFocus)) ivReturnFocus.focus();
  }

  viewerImg.addEventListener('load', () => {
    viewer.classList.remove('loading');
    $('ivDownload').disabled = false;
  });
  viewerImg.addEventListener('error', () => {
    if(!viewer.classList.contains('open')) return;
    viewer.classList.remove('loading');
    viewer.classList.add('failed');
    $('ivStatus').textContent = 'This image could not be loaded. It may have been moved or deleted.';
  });

  $('ivZoomIn').addEventListener('click', () => ivZoom(1.25));
  $('ivZoomOut').addEventListener('click', () => ivZoom(0.8));
  $('ivReset').addEventListener('click', ivReset);
  $('ivClose').addEventListener('click', closeImageViewer);

  // Download: fetch the image as a file blob so the browser saves it
  // instead of just opening it; fall back to a new tab if that fails.
  $('ivDownload').addEventListener('click', async () => {
    const btn = $('ivDownload'), label = $('ivDownloadLabel');
    const src = viewerImg.src;
    const ext = (src.split('?')[0].match(/\.(jpe?g|png|gif|webp)$/i) || ['.png'])[0].toLowerCase();
    btn.disabled = true;
    label.textContent = 'Downloading…';
    try {
      const res = await fetch(src, { credentials: 'same-origin' });
      if (!res.ok) throw new Error('Download failed');
      const url = URL.createObjectURL(await res.blob());
      const a = document.createElement('a');
      a.href = url;
      a.download = ivName + ext;
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(url);
    } catch (err) {
      window.open(src, '_blank', 'noopener');
    }
    btn.disabled = false;
    label.textContent = 'Download';
  });

  // Clicking the dark background (not the image or controls) closes the viewer
  viewer.addEventListener('click', (e) => { if(e.target === viewer) closeImageViewer(); });
  // Mouse wheel / trackpad zoom
  viewer.addEventListener('wheel', (e) => { e.preventDefault(); ivZoom(e.deltaY < 0 ? 1.1 : 0.9); }, { passive:false });
  // Double-click toggles between 100% and 200%
  viewerImg.addEventListener('dblclick', () => { if(ivScale > 1) ivReset(); else ivSetScale(2); });

  // Pointer handling: one finger / mouse drags (only when zoomed in),
  // two fingers pinch to zoom.
  const pinchDistance = () => {
    const [a, b] = [...ivPointers.values()];
    return Math.hypot(a.x - b.x, a.y - b.y);
  };
  viewerImg.addEventListener('pointerdown', (e) => {
    e.preventDefault();
    viewerImg.setPointerCapture(e.pointerId);
    ivPointers.set(e.pointerId, { x: e.clientX, y: e.clientY });
    if(ivPointers.size === 2){
      ivDrag = null;
      ivPinch = { dist: pinchDistance(), scale: ivScale };
    } else if(ivScale > 1){
      ivDrag = { x: e.clientX - ivX, y: e.clientY - ivY };
      viewerImg.classList.add('dragging');
    }
  });
  viewerImg.addEventListener('pointermove', (e) => {
    if(!ivPointers.has(e.pointerId)) return;
    ivPointers.set(e.pointerId, { x: e.clientX, y: e.clientY });
    if(ivPinch && ivPointers.size === 2){
      ivSetScale(ivPinch.scale * (pinchDistance() / ivPinch.dist));
    } else if(ivDrag){
      ivX = e.clientX - ivDrag.x;
      ivY = e.clientY - ivDrag.y;
      ivApply();
    }
  });
  ['pointerup','pointercancel'].forEach(ev => viewerImg.addEventListener(ev, (e) => {
    ivPointers.delete(e.pointerId);
    if(ivPointers.size < 2) ivPinch = null;
    ivDrag = null;
    viewerImg.classList.remove('dragging');
  }));

  document.addEventListener('keydown', (e) => {
    if (viewer.classList.contains('open')) {
      if (e.key === 'Escape') closeImageViewer();
      else if (e.key === '+' || e.key === '=') ivZoom(1.25);
      else if (e.key === '-') ivZoom(0.8);
      else if (e.key === '0') ivReset();
      else if (e.key === 'Tab') {
        // Keep Tab inside the viewer while it is open
        const items = [...viewer.querySelectorAll('button:not(:disabled)')];
        const i = items.indexOf(document.activeElement);
        const next = e.shiftKey ? (i <= 0 ? items.length - 1 : i - 1) : (i === items.length - 1 ? 0 : i + 1);
        e.preventDefault();
        items[next].focus();
      }
      return;
    }
    if (e.key !== 'Escape') return;
    const drawer = document.getElementById('drawer');
    if (drawer && drawer.classList.contains('open')) { document.getElementById('drawerClose').click(); return; }
    document.querySelectorAll('.modal-overlay.open').forEach(m => m.classList.remove('open'));
  });
})();
</script>

</body>
</html>