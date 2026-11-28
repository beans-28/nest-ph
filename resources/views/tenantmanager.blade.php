<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    {{-- Page-switch fade opt-in: must be inline and first in <head> or Chrome decides before it sees it (see typography.css). --}}
    <style>@view-transition{navigation:auto}</style><link rel="stylesheet" href="/css/motion.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Tenant Manager · {{ $brandDormName }}</title>
<link rel="icon" href="{{ $brandFaviconUrl }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/typography.css') }}">
    <script src="{{ asset('js/smooth-nav.js') }}" defer></script>
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<style>
  /* Shared sidebar/topbar/content-header/reset styles now live in
     public/css/admin.css (linked above). Only page-specific overrides
     and this page's own content styling stay here. */

  /* Customized vs admin.css: adds background/min-height */
  .main{ flex:1; display:flex; flex-direction:column; min-width:0; background:var(--bg-page); min-height:100vh; }

  /* Customized vs admin.css: light-background icon (not using .avatar-icon) */
  .topbar-icon{ width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.9); color:var(--green-dark); display:flex; align-items:center; justify-content:center; cursor:pointer; }

  /* Customized vs admin.css: tighter/wider content, different page-head spacing, smaller plain back-arrow, different h1 size/color */
  .content{ width:100%; max-width:1400px; margin:0 auto; }
  .page-head{ display:flex; align-items:center; gap:12px; margin-bottom:6px; }
  .page-sub{ font-size:12.5px; color:var(--text-mid); margin:0 0 22px 42px; }

  .filters-row{ display:flex; gap:12px; align-items:center; margin-bottom:16px; flex-wrap:wrap; }
  .search-input{ flex:1; min-width:220px; border:1px solid var(--border); border-radius:10px; padding:10px 14px; font-size:13px; font-family:var(--font-body); background:#fff; }
  .filters-row select{ border:1px solid var(--border); border-radius:10px; padding:10px 14px; font-size:13px; font-family:var(--font-body); background:#fff; min-width:170px; }
  .btn{ border:1px solid var(--border); background:#fff; border-radius:10px; padding:10px 16px; font-size:13px; font-weight:600; cursor:pointer; color:var(--text-dark); white-space:nowrap; }
  .btn:hover{ background:#f6f6f6; }
  .btn.primary{ background:var(--green-btn); border-color:var(--green-btn); color:#fff; }
  .btn.primary:hover{ background:var(--green-btn-hover); }
  .btn.warn{ background:var(--status-occupied); border-color:var(--status-occupied); color:#fff; }
  .btn.warn:hover{ opacity:0.9; }
  .btn.sm{ padding:8px 12px; font-size:12px; }
  a.btn{ text-decoration:none; display:inline-block; }

  .table-panel{ background:var(--card-bg); border-radius:14px; box-shadow:0 2px 10px rgba(0,0,0,0.04); overflow:hidden; }
  table{ width:100%; border-collapse:collapse; }
  thead th{ text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:.4px; color:var(--text-light); font-weight:700; padding:14px 18px; border-bottom:1px solid var(--border); }
  tbody td{ padding:14px 18px; font-size:13px; border-bottom:1px solid #f2f2f2; vertical-align:middle; }
  tbody tr:last-child td{ border-bottom:none; }
  tbody tr:hover{ background:#fbfbfb; }
  .empty-row td{ text-align:center; color:var(--text-light); padding:32px; font-style:italic; }

  .tenant-cell{ display:flex; align-items:center; gap:10px; }
  .tenant-avatar{ width:36px; height:36px; border-radius:6px; flex-shrink:0; object-fit:cover; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:12px; color:var(--green-darker, #1f5a2c); background:color-mix(in srgb, var(--green-accent, #2f6f3c) 12%, #fff); border:1px solid color-mix(in srgb, var(--green-accent, #2f6f3c) 22%, #fff); }
  .drawer-photo{ display:flex; align-items:center; gap:14px; margin-bottom:6px; }
  .drawer-photo .tenant-avatar{ width:96px; height:96px; border-radius:8px; font-size:26px; }
  .drawer-photo .hint{ font-size:12px; color:var(--text-light); }
  .tenant-cell .name{ font-weight:600; font-size:13.5px; }
  .name-link{ background:none; border:none; padding:0; font:inherit; color:inherit; cursor:pointer; text-align:left; }
  .name-link:hover .name{ color:var(--green-accent); text-decoration:underline; }
  tbody tr.clickable{ cursor:pointer; }
  .tenant-cell .email{ font-size:12px; color:var(--text-light); margin-top:2px; }
  .not-assigned{ color:var(--text-light); font-style:italic; }

  .status-text{ font-weight:700; font-size:12.5px; }
  .status-text.active{ color:var(--status-vacant); }
  .status-text.delinquent{ color:var(--status-occupied); }
  .status-text.pending_move_in_payment{ color:var(--status-maintenance); }
  .status-text.inactive{ color:var(--text-light); }

  .row-actions{ display:flex; gap:8px; flex-wrap:wrap; }
  .row-actions .btn{ padding:8px 14px; }

  .pagination-row{ display:flex; justify-content:space-between; align-items:center; padding:14px 18px; font-size:12.5px; color:var(--text-mid); }
  .pagination{ display:flex; gap:6px; }
  .pagination button{ display:inline-flex; align-items:center; justify-content:center; border:1px solid var(--border); background:#fff; border-radius:6px; width:28px; height:28px; cursor:pointer; font-size:12px; }
  .pagination button.active{ background:var(--green-btn); border-color:var(--green-btn); color:#fff; }
  .pagination button:disabled{ opacity:0.4; cursor:default; }

  /* ===== Modals ===== */
  .modal-overlay{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:60; align-items:center; justify-content:center; padding:20px; }
  .modal-overlay.open{ display:flex; }
  .modal-box{ background:#fff; border-radius:14px; width:100%; max-width:640px; max-height:90vh; overflow-y:auto; }
  .modal-head{ padding:20px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; }
  .modal-head h2{ font-size:16px; font-weight:700; margin:0; }
  .modal-body{ padding:22px 24px; }
  .modal-body .fld{ display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
  .modal-body label{ font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-mid); }
  .modal-body input,.modal-body select,.modal-body textarea{ border:1px solid var(--border); border-radius:8px; padding:10px 13px; font-size:13px; font-family:var(--font-body); width:100%; }
  .modal-body textarea{ min-height:80px; resize:vertical; }
  .modal-row2{ display:grid; grid-template-columns:1fr 1fr; gap:14px; }
  .modal-section-title{ font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--green-accent); margin:20px 0 12px 0; padding-bottom:6px; border-bottom:1px solid #e2ede3; }
  .modal-section-title:first-child{ margin-top:0; }
  .modal-actions{ display:flex; gap:10px; padding:18px 24px; border-top:1px solid var(--border); }
  .modal-error{ font-size:12px; color:var(--status-occupied); margin:-8px 0 14px 0; display:none; }
  .modal-error.visible{ display:block; }

  .sec{ margin-bottom:22px; }
  .sec h3{ font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--green-accent); margin:0 0 12px 0; padding-bottom:7px; border-bottom:2px solid #e2ede3; }
  .kv{ display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:12px 20px; }
  .kv .k{ font-size:11.5px; font-weight:600; color:var(--text-mid); }
  .kv .v{ font-size:13px; font-weight:500; margin-top:3px; word-break:break-word; }
  .kv .v.empty-v{ color:#6b746e; font-weight:400; }
  .doc-link{ display:inline-block; font-size:12.5px; color:var(--green-accent); font-weight:600; margin-right:14px; }

  /* ===== Tenant search (Add modal reuses none — bed picker only) ===== */
  .tenant-results{ border:1px solid var(--border); border-radius:8px; margin-top:6px; max-height:160px; overflow-y:auto; display:none; }
  .tenant-results.open{ display:block; }

  .toast{ position:fixed; bottom:22px; right:22px; background:var(--green-accent); color:#fff; padding:12px 20px; border-radius:8px; font-size:13px; display:none; z-index:99; box-shadow:0 6px 18px rgba(0,0,0,.2); }
  .toast.error{ background:var(--status-occupied); }
  .toast.visible{ display:block; }

  /* ===== Tenant Profile window (Pureza revision 2026-11-14) =====
     Replaces the old side drawer: a big, centered window with tabs, so all
     of a tenant's info (personal, lease, billing, activity) is in one place.
     Lease Management used to be its own page; it now lives in the Lease tab. */
  .profile-overlay{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:60; align-items:center; justify-content:center; padding:24px; }
  .profile-overlay.open{ display:flex; }
  .profile-box{ background:var(--bg-page); border-radius:18px; width:100%; max-width:1080px; height:min(92vh, 900px); display:flex; flex-direction:column; overflow:hidden; box-shadow:0 24px 60px rgba(0,0,0,.25); }
  .profile-top{ background:#fff; padding:20px 28px 0 28px; border-bottom:1px solid var(--border); position:relative; }
  .profile-close{ position:absolute; top:14px; right:16px; width:36px; height:36px; border-radius:50%; border:none; background:rgba(255,255,255,.8); font-size:22px; line-height:1; cursor:pointer; color:var(--text-mid); }
  .profile-close:hover{ background:#fff; }
  .profile-id{ display:flex; align-items:center; gap:20px; padding-right:44px; }
  .profile-id .tenant-avatar{ width:64px; height:64px; border-radius:8px; font-size:20px; }
  .profile-id h2{ font-size:21px; margin:0 0 6px 0; color:var(--text-dark); }
  .profile-meta{ display:flex; flex-wrap:wrap; gap:6px 18px; font-size:13px; color:var(--text-mid); margin-top:8px; }
  .profile-meta span{ display:inline-flex; align-items:center; gap:6px; }
  .profile-meta svg{ width:15px; height:15px; }
  .pill{ display:inline-block; vertical-align:middle; font-size:12px; font-weight:700; padding:4px 12px; border-radius:999px; }
  .pill.active{ background:#d9f2dd; color:#2f6f3c; }
  .pill.delinquent{ background:#fbeceb; color:#b13f39; }
  .pill.pending_move_in_payment{ background:#f6ecd6; color:#8a6516; }
  .pill.inactive{ background:#ededed; color:#6b746e; }
  .pill.sm{ font-size:11px; padding:3px 9px; }
  .pill.ok{ background:#d9f2dd; color:#2f6f3c; }
  .pill.bad{ background:#fbeceb; color:#b13f39; }
  .pill.wait{ background:#f6ecd6; color:#8a6516; }
  .pill.muted{ background:#ededed; color:#6b746e; }
  .profile-tabs{ display:flex; gap:24px; margin-top:16px; overflow-x:auto; scrollbar-width:none; }
  .profile-tab{ padding:10px 2px 12px; border:none; border-bottom:2px solid transparent; background:none; font:600 13.5px var(--font-body); color:var(--text-mid); cursor:pointer; white-space:nowrap; margin-bottom:-1px; }
  .profile-tab:hover{ color:var(--text-dark); }
  .profile-tab[aria-selected="true"]{ color:#197335; border-bottom-color:#197335; }
  .profile-tab:focus-visible, .chip:focus-visible, .profile-close:focus-visible{ outline:2px solid #197335; outline-offset:2px; }
  .profile-tab .short{ display:none; }
  .profile-body{ flex:1; overflow-y:auto; padding:22px 28px; }
  .profile-foot{ border-top:1px solid var(--border); background:#fff; padding:14px 28px; display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
  .profile-foot .label{ font-size:12.5px; font-weight:600; color:var(--text-mid); margin-right:4px; }
  .card{ background:#fff; border:1px solid var(--border); border-radius:10px; padding:16px 20px; margin-bottom:14px; }
  .card h3{ display:flex; align-items:center; gap:8px; font-size:14.5px; font-weight:700; color:var(--text-dark); margin:0 0 14px 0; text-transform:none; letter-spacing:0; padding:0; border:none; }
    .card h3 .right{ margin-left:auto; font-weight:500; font-size:12px; }
  .grid2{ display:grid; grid-template-columns:1fr 1fr; gap:16px; }
  .kv.wide{ grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); }
  .money-strip{ display:grid; grid-template-columns:repeat(4,1fr); background:#fff; border:1px solid var(--border); border-radius:10px; margin-bottom:14px; }
  .money-strip > div{ padding:12px 16px; border-left:1px solid var(--border); }
  .money-strip > div:first-child{ border-left:none; }
  .money-strip .k{ font-size:12px; color:var(--text-mid); }
  .money-strip .n{ font-size:17px; font-weight:700; margin-top:2px; font-variant-numeric:tabular-nums; color:var(--text-dark); }
  .money-strip .n.red{ color:#b13f39; }
  .mini-table td.num, .pay-item .amt{ font-variant-numeric:tabular-nums; }
  .mini-table{ width:100%; border-collapse:collapse; }
  .mini-table th{ font-size:10.5px; padding:8px 10px; background:#fbfbfb; }
  .mini-table td{ font-size:12.5px; padding:9px 10px; }
  .mini-table .num{ text-align:right; white-space:nowrap; }
  .table-scroll{ overflow-x:auto; }
  .pay-list{ display:flex; flex-direction:column; gap:10px; }
  .pay-item{ display:flex; gap:14px; border:1px solid var(--border); border-radius:12px; padding:12px; align-items:flex-start; }
  .pay-item .proof{ width:84px; height:84px; flex-shrink:0; border-radius:8px; border:1px solid var(--border); background:#f7f7f7; display:flex; align-items:center; justify-content:center; overflow:hidden; font-size:11px; color:var(--text-light); text-align:center; }
  .pay-item .proof img{ width:100%; height:100%; object-fit:cover; }
  .pay-item .info{ flex:1; min-width:0; }
  .pay-item .top{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
  .pay-item .amt{ font-size:15px; font-weight:700; }
  .pay-item .sub{ font-size:12px; color:var(--text-mid); margin-top:4px; line-height:1.55; }
  .timeline{ list-style:none; margin:0; padding:0; }
  .timeline li{ position:relative; padding:0 0 16px 22px; border-left:2px solid #e2ede3; margin-left:6px; }
  .timeline li:last-child{ border-left-color:transparent; }
  .timeline li::before{ content:''; position:absolute; left:-7px; top:2px; width:12px; height:12px; border-radius:50%; background:#fff; border:2px solid var(--green-accent); }
  .timeline li.delinquency::before{ border-color:var(--status-occupied); }
  .timeline li.guardian::before{ border-color:#292420; }
  .timeline li.email::before{ border-color:#567357; background:#567357; }
  .timeline .tag{ font-size:11px; font-weight:600; color:var(--text-mid); }
  .timeline .t{ font-size:13px; font-weight:600; }
  .timeline .d{ font-size:12px; color:var(--text-mid); margin-top:3px; line-height:1.5; word-break:break-word; }
  .timeline .w{ font-size:11.5px; color:var(--text-mid); margin-top:3px; }
  .chip-row{ display:flex; gap:8px; flex-wrap:wrap; margin-bottom:14px; }
  .chip{ border:1px solid var(--border); background:#fff; border-radius:999px; padding:8px 14px; min-height:36px; font:600 12.5px var(--font-body); color:var(--text-mid); cursor:pointer; }
  .chip[aria-pressed="true"]{ background:var(--green-btn); border-color:var(--green-btn); color:#fff; }
  .profile-msg{ padding:28px 0; font-size:13px; color:var(--text-mid); }
  .profile-msg.err{ color:#b13f39; }
  .profile-msg .btn{ margin-left:10px; }
  .lease-older{ margin-top:6px; }
  .lease-older summary{ cursor:pointer; font-size:12.5px; font-weight:600; color:var(--green-accent); }
  .signed-note{ background:color-mix(in srgb, var(--green-accent, #2f6f3c) 12%, #fff); border:1px solid #9fd6a8; border-radius:10px; padding:12px 14px; font-size:12.5px; color:var(--green-darker, #1f5a2c); line-height:1.6; }
  .signed-note a{ color:inherit; font-weight:700; }
  .signed-note.review{ background:#f6ecd6; border-color:#ead7a8; color:#7a5a12; }
  .signed-note.missing{ background:#fbeceb; border-color:#f2cfcc; color:#a8403a; }
  .sign-box{ border:1px solid var(--border); border-radius:10px; padding:14px 16px; background:#fcfcfc; margin-top:10px; }
  .sign-box .fld, .action-box .fld{ display:flex; flex-direction:column; gap:6px; margin-bottom:12px; }
  .sign-box label, .action-box label{ font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-mid); }
  .sign-box input, .sign-box select, .action-box input, .action-box textarea{ border:1px solid var(--border); border-radius:8px; padding:9px 12px; font-size:13px; font-family:var(--font-body); background:#fff; width:100%; }
  .confirm-row{ display:flex; gap:9px; align-items:flex-start; font-size:12px; color:var(--text-mid); line-height:1.6; margin-bottom:12px; }
  .confirm-row input{ width:15px !important; height:15px; margin-top:2px; flex-shrink:0; }
  .action-box{ display:none; border:1px solid var(--border); border-radius:10px; padding:14px; background:#fcfcfc; margin-top:12px; }
  .action-box.open{ display:block; }
  .action-box textarea{ min-height:80px; resize:vertical; }
  .action-hint{ font-size:11.5px; color:#a8403a; margin:8px 0 0; line-height:1.5; }
  .note-muted{ font-size:12.5px; color:var(--text-mid); margin:0; }
  .lease-flag{ display:block; margin-top:3px; font-size:11px; font-weight:700; color:#a8403a; }
  .lease-flag.review{ color:#7a5a12; }
  /* Dialogs opened from the profile window must sit above it. */
  #depositRefundModal, #editModal, #statusModal{ z-index:320; }
  @media (max-width: 760px){
    .profile-overlay{ padding:0; }
    .profile-box{ height:100vh; height:100dvh; max-width:none; border-radius:0; }
    .profile-tab .long{ display:none; }
    .profile-tab .short{ display:inline; }
    .profile-tabs{ gap:18px; }
    .profile-foot{ flex-wrap:nowrap; overflow-x:auto; }
    .profile-foot .label{ display:none; }
    .profile-foot .btn{ min-height:44px; display:inline-flex; align-items:center; flex-shrink:0; }
    .profile-close{ width:44px; height:44px; top:10px; right:10px; }
    .money-strip{ grid-template-columns:1fr 1fr; }
    .money-strip > div:nth-child(3){ border-left:none; }
    .money-strip > div:nth-child(n+3){ border-top:1px solid var(--border); }
    .profile-top{ padding:16px 16px 0 16px; }
    .profile-id{ gap:14px; }
    .profile-id .tenant-avatar{ width:52px; height:52px; font-size:17px; }
    .profile-id h2{ font-size:19px; }
    .profile-body{ padding:16px; }
    .profile-foot{ padding:12px 16px; }
    .grid2{ grid-template-columns:1fr; }
    .pay-item{ flex-direction:column; }
  }

    @media (max-width: 760px){
    .table-panel table{ min-width:0 !important; }
    .table-panel thead{ display:none; }
    .table-panel tbody tr{ display:grid; grid-template-columns:1fr auto; gap:4px 12px; padding:14px 16px; border-bottom:1px solid #f2f2f2; }
    .table-panel tbody td{ display:block; padding:0; border:none; }
    .table-panel tbody td:nth-child(1){ grid-column:1 / -1; }
    .table-panel tbody td:nth-child(3){ display:none; }
    .table-panel tbody td:nth-child(2){ font-size:12.5px; color:var(--text-mid); }
    .table-panel tbody td:nth-child(4){ text-align:right; font-variant-numeric:tabular-nums; }
    .table-panel tbody td:nth-child(5){ grid-column:1 / -1; }
    .table-panel tbody td:nth-child(6){ grid-column:1 / -1; margin-top:6px; }
    .table-panel tbody td:nth-child(6) .btn{ min-height:40px; }
    .table-panel .empty-row td{ grid-column:1 / -1; }
  }
    @media (max-width: 900px){
    .content{ padding:18px 16px 32px 16px; }
    .filters-row{ flex-direction:column; align-items:stretch; }
    .filters-row select, .filters-row .btn{ width:100%; margin-left:0 !important; }
    .table-panel{ overflow-x:auto; }
    table{ min-width:720px; }
  }
</style>
    <script src="{{ asset('js/heic-convert.js') }}" defer></script>
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
        <h1>Tenant Management</h1>
      </div>
      <p class="page-sub" id="pageSub">{{ $totalCount }} Tenants</p>

      <div class="filters-row">
        <input type="text" class="search-input" id="searchInput" placeholder="Search Name or Room No...">
        <select id="statusFilter">
          <option value="">All Statuses</option>
          <option value="active">Active</option>
          <option value="delinquent">Delinquent</option>
          <option value="pending_move_in_payment">Pending Move-In</option>
          <option value="inactive">Inactive</option>
        </select>
        <select id="tenantTypeFilter">
          <option value="">All Tenant Types</option>
          <option value="student">Student</option>
          <option value="working_student">Working Student</option>
          <option value="full_time_employee">Full-time Employee</option>
          <option value="part_time_employee">Part-time Employee</option>
        </select>
        <select id="leaseFilter" aria-label="Filter by lease">
          <option value="">All Leases</option>
          <option value="expiring_soon" data-label="Lease expiring soon">Lease expiring soon ({{ $leaseCounts['expiring_soon'] ?? 0 }})</option>
          <option value="expired" data-label="Lease expired">Lease expired ({{ $leaseCounts['expired'] ?? 0 }})</option>
          <option value="notarized_review" data-label="Notarized copy to review">Notarized copy to review ({{ $leaseCounts['notarized_review'] ?? 0 }})</option>
        </select>
        <select id="sortFilter">
          <option value="date_started_desc">Sort: Latest Date Started</option>
          <option value="date_started_asc">Sort: Oldest Date Started</option>
          <option value="name_asc">Sort: Name (A to Z)</option>
          <option value="name_desc">Sort: Name (Z to A)</option>
          <option value="rent_desc">Sort: Rent (High to Low)</option>
          <option value="rent_asc">Sort: Rent (Low to High)</option>
        </select>
        <button class="btn primary" id="openAddModalBtn" style="margin-left:auto;">+ Add New Tenant</button>
      </div>

      <div class="table-panel">
        <table>
          <thead>
            <tr>
              <th>Tenant</th><th>Room &amp; Bed</th><th>Date Started</th><th>Rent /Month</th><th>Status</th><th>Actions</th>
            </tr>
          </thead>
          <tbody id="tableBody"></tbody>
        </table>
        <div class="pagination-row">
          <div id="tableSummary"></div>
          <div class="pagination" id="pagination"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ===== Tenant Profile window (replaces the old side drawer and the Lease Management page) ===== -->
<div class="profile-overlay" id="profileModal">
  <div class="profile-box" role="dialog" aria-modal="true" aria-labelledby="profileName">
    <div class="profile-top">
      <button type="button" class="profile-close" id="profileClose" aria-label="Close tenant profile">&times;</button>
      <div class="profile-id">
        <div id="profileAvatar"></div>
        <div style="min-width:0;">
          <h2 id="profileName">Tenant Profile</h2>
          <span class="pill" id="profileStatus"></span>
          <div class="profile-meta" id="profileMeta"></div>
        </div>
      </div>
      <div class="profile-tabs" role="tablist" aria-label="Tenant profile sections">
        <button type="button" class="profile-tab" role="tab" id="tab-personal" aria-controls="profileBody" aria-selected="true" data-ptab="personal"><span class="long">Personal Information</span><span class="short">Personal</span></button>
        <button type="button" class="profile-tab" role="tab" id="tab-lease" aria-controls="profileBody" aria-selected="false" tabindex="-1" data-ptab="lease"><span class="long">Lease &amp; Contract</span><span class="short">Lease</span></button>
        <button type="button" class="profile-tab" role="tab" id="tab-billing" aria-controls="profileBody" aria-selected="false" tabindex="-1" data-ptab="billing"><span class="long">Billing &amp; Payment History</span><span class="short">Billing</span></button>
        <button type="button" class="profile-tab" role="tab" id="tab-activity" aria-controls="profileBody" aria-selected="false" tabindex="-1" data-ptab="activity"><span class="long">Activity Log</span><span class="short">Activity</span></button>
      </div>
    </div>
    <div class="profile-body" id="profileBody" role="tabpanel" tabindex="0" aria-live="polite"></div>
    <div class="profile-foot" id="profileFoot"></div>
  </div>
</div>

<!-- ===== Add New Tenant modal ===== -->
<div class="modal-overlay" id="addModal">
  <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="addModalTitle">
    <div class="modal-head"><h2 id="addModalTitle">Add New Tenant</h2></div>
    <div class="modal-body">
      <div class="modal-error" id="addModalError"></div>

      <div class="modal-section-title">Personal Information</div>
      <div class="modal-row2">
        <div class="fld"><label for="addFirstName">First Name</label><input type="text" id="addFirstName"></div>
        <div class="fld"><label for="addLastName">Last Name</label><input type="text" id="addLastName"></div>
      </div>
      <div class="modal-row2">
        <div class="fld"><label for="addDob">Date of Birth</label><input type="date" id="addDob"></div>
        <div class="fld"><label for="addTenantType">Tenant Type</label>
          <select id="addTenantType">
            <option value="">Select type</option>
            <option value="student">Student</option>
            <option value="working_student">Working Student</option>
            <option value="full_time_employee">Full-time Employee</option>
            <option value="part_time_employee">Part-time Employee</option>
          </select>
        </div>
      </div>
      <div class="fld"><label for="addHomeAddress">Home Address</label><input type="text" id="addHomeAddress"></div>
      <div class="modal-row2">
        <div class="fld"><label for="addContactNumber">Contact Number</label><input type="text" id="addContactNumber"></div>
        <div class="fld"><label for="addEmail">Email Address</label><input type="email" id="addEmail"></div>
      </div>
      <div class="modal-row2">
        <div class="fld"><label for="addEmergencyName">Emergency Contact Name</label><input type="text" id="addEmergencyName"></div>
        <div class="fld"><label for="addEmergencyNumber">Emergency Contact Number</label><input type="text" id="addEmergencyNumber"></div>
      </div>

      <div class="modal-section-title">Documents</div>
      <div class="fld"><label for="addIdPhoto">2x2 ID Picture (optional, JPG or PNG, up to 5 MB)</label><input type="file" id="addIdPhoto" accept=".jpg,.jpeg,.png"></div>
      <div class="fld"><label for="addIdDocument">Valid ID</label><input type="file" id="addIdDocument" accept=".pdf,.jpg,.jpeg,.png"></div>
      <div class="fld"><label for="addSignedContract">Signed Contract (optional)</label><input type="file" id="addSignedContract" accept=".pdf,.jpg,.jpeg,.png"></div>

      <div class="modal-section-title">Room &amp; Lease</div>
      <div class="fld"><label for="addRoomSelect">Room</label><select id="addRoomSelect"><option value="">Loading rooms…</option></select></div>
      <div class="fld"><label for="addBedSelect">Bedspace</label><select id="addBedSelect"><option value="">Select a room first</option></select></div>
      <div class="modal-row2">
        <div class="fld"><label for="addStartDate">Lease Start Date</label><input type="date" id="addStartDate"></div>
        <div class="fld"><label for="addEndDate">Lease End Date</label><input type="date" id="addEndDate"></div>
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn primary" id="submitAddBtn" style="flex:1;">Register Tenant</button>
      <button class="btn" id="cancelAddBtn">Cancel</button>
    </div>
  </div>
</div>

<!-- ===== Record Deposit Refund modal (v39) ===== -->
<div class="modal-overlay" id="depositRefundModal">
  <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="depositRefundTitle">
    <div class="modal-head"><h2 id="depositRefundTitle">Record Deposit Refund</h2></div>
    <div class="modal-body">
      <div class="modal-error" id="drError"></div>
      <p id="drForfeitNote" role="status" style="display:none;font-size:13px;line-height:1.5;color:var(--status-occupied);background:#fdecec;border:1px solid #f3c4c4;border-radius:8px;padding:10px 12px;margin:0 0 14px;"></p>
      <p style="font-size:12.5px;color:var(--text-mid);margin:0 0 14px;">The deposit is half of the paid move-in fee. Suggested deductions are the tenant's unpaid balance plus penalties not yet billed; change them if needed.</p>
      <div class="modal-row2">
        <div class="fld"><label for="drDeposit">Deposit Amount (₱)</label><input type="number" id="drDeposit" min="0.01" step="0.01"></div>
        <div class="fld"><label for="drDeductions">Deductions (₱)</label><input type="number" id="drDeductions" min="0" step="0.01"></div>
      </div>
      <div class="fld"><label for="drNote">What the deductions are for</label><input type="text" id="drNote" maxlength="500" placeholder="e.g. Unpaid October rent, broken window"></div>
      <div class="modal-row2">
        <div class="fld"><label for="drMethod">Refund Method</label><input type="text" id="drMethod" maxlength="60" list="drMethodList"><datalist id="drMethodList"><option value="Cash"><option value="GCash"><option value="Bank Transfer"></datalist></div>
        <div class="fld"><label for="drDate">Refund Date</label><input type="date" id="drDate"></div>
      </div>
      <div class="fld"><label for="drReference">Reference No. (optional)</label><input type="text" id="drReference" maxlength="100"></div>
      <p style="font-size:14px;margin:6px 0 0;">Amount to refund: <strong id="drRefundPreview">₱0.00</strong></p>
    </div>
    <div class="modal-actions">
      <button class="btn primary" id="drSubmitBtn" style="flex:1;">Save Refund</button>
      <button class="btn" id="drCancelBtn">Cancel</button>
    </div>
  </div>
</div>

<!-- ===== Edit Tenant modal ===== -->
<div class="modal-overlay" id="editModal">
  <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="editModalTitle">
    <div class="modal-head"><h2 id="editModalTitle">Edit Tenant</h2></div>
    <div class="modal-body">
      <div class="modal-error" id="editModalError"></div>
      <input type="hidden" id="editTenantId">
      <div class="modal-row2">
        <div class="fld"><label for="editFirstName">First Name</label><input type="text" id="editFirstName"></div>
        <div class="fld"><label for="editLastName">Last Name</label><input type="text" id="editLastName"></div>
      </div>
      <div class="modal-row2">
        <div class="fld"><label for="editDob">Date of Birth</label><input type="date" id="editDob"></div>
        <div class="fld"><label for="editTenantType">Tenant Type</label>
          <select id="editTenantType">
            <option value="">Select type</option>
            <option value="student">Student</option>
            <option value="working_student">Working Student</option>
            <option value="full_time_employee">Full-time Employee</option>
            <option value="part_time_employee">Part-time Employee</option>
          </select>
        </div>
      </div>
      <div class="fld"><label for="editHomeAddress">Home Address</label><input type="text" id="editHomeAddress"></div>
      <div class="modal-row2">
        <div class="fld"><label for="editContactNumber">Contact Number</label><input type="text" id="editContactNumber"></div>
        <div class="fld"><label for="editEmail">Email Address</label><input type="email" id="editEmail"></div>
      </div>
      <div class="modal-row2">
        <div class="fld"><label for="editEmergencyName">Emergency Contact Name</label><input type="text" id="editEmergencyName"></div>
        <div class="fld"><label for="editEmergencyNumber">Emergency Contact Number</label><input type="text" id="editEmergencyNumber"></div>
      </div>
      <div class="fld"><label for="editEmergencyEmail">Emergency Contact Email (for billing notices)</label><input type="email" id="editEmergencyEmail"></div>
      <div class="fld">
        <label style="display:flex;gap:8px;align-items:flex-start;font-weight:500;text-transform:none;letter-spacing:0;">
          <input type="checkbox" id="editEmergencyReminders" style="width:16px;height:16px;margin-top:2px;">
          <span>Send billing and overdue notices to the emergency contact too (the same SMS and emails the tenant gets after the grace period). Turn on only if they agreed in writing (the consent line in the Tenant Agreement). Turn off if they ask to stop.</span>
        </label>
      </div>
      <div class="modal-section-title">Documents (leave blank to keep existing)</div>
      <div class="fld"><label for="editIdPhoto">Replace 2x2 ID Picture (JPG or PNG, up to 5 MB)</label><input type="file" id="editIdPhoto" accept=".jpg,.jpeg,.png"></div>
      <div class="fld" id="editRemovePhotoRow" style="display:none;">
        <label style="display:flex;gap:8px;align-items:center;font-weight:500;text-transform:none;letter-spacing:0;">
          <input type="checkbox" id="editRemovePhoto" style="width:16px;height:16px;">
          <span>Remove the current 2x2 ID picture</span>
        </label>
      </div>
      <div class="fld"><label for="editIdDocument">Replace Valid ID</label><input type="file" id="editIdDocument" accept=".pdf,.jpg,.jpeg,.png"></div>
      <div class="fld"><label for="editSignedContract">Replace Signed Contract</label><input type="file" id="editSignedContract" accept=".pdf,.jpg,.jpeg,.png"></div>
    </div>
    <div class="modal-actions">
      <button class="btn primary" id="submitEditBtn" style="flex:1;">Save Changes</button>
      <button class="btn" id="cancelEditBtn">Cancel</button>
    </div>
  </div>
</div>

<!-- ===== Set Status modal ===== -->
<div class="modal-overlay" id="statusModal">
  <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="statusModalTitle">
    <div class="modal-head"><h2 id="statusModalTitle">Set Status</h2></div>
    <div class="modal-body">
      <div class="modal-error" id="statusModalError"></div>
      <input type="hidden" id="statusTenantId">
      <div class="fld">
        <label for="statusSelect">New Status</label>
        <select id="statusSelect">
          <option value="active">Active</option>
          <option value="inactive">Inactive (Deactivate)</option>
        </select>
      </div>
      <div class="fld" id="statusReasonFld" style="display:none;">
        <label for="statusReason">Reason for Deactivation</label>
        <textarea id="statusReason" placeholder="e.g. Tenant moved out, lease ended..."></textarea>
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn warn" id="submitStatusBtn" style="flex:1;">Confirm</button>
      <button class="btn" id="cancelStatusBtn">Cancel</button>
    </div>
  </div>
</div>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script type="application/json" id="tenants-data">{!! json_encode($tenants) !!}</script>

<script>
(function(){
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  let tenants = JSON.parse(document.getElementById('tenants-data').textContent);

  let search = '';
  let statusFilter = '';
  let tenantTypeFilter = '';
  // Lease filter: replaces the old Lease Management page's tabs.
  let leaseFilter = new URLSearchParams(location.search).get('lease') || '';
  if(document.getElementById('leaseFilter')) document.getElementById('leaseFilter').value = leaseFilter;
  let sortBy = 'date_started_desc';
  let page = 1;
  const PAGE_SIZE = 10;

  // Combines the lease start date with the contract's created_at time of
  // day, so two tenants who started on the same calendar date still sort
  // by which record was actually created first/most recently, not just
  // left in whatever order the database happened to return them.
  function dateStartedSortValue(t){
    if(!t.date_started_raw) return null;
    const datePart = new Date(t.date_started_raw + 'T00:00:00').getTime();
    const timeOfDay = t.contract_created_raw ? (new Date(t.contract_created_raw).getTime() % 86400000) : 0;
    return datePart + timeOfDay;
  }

  function compareTenants(a, b){
    if(sortBy === 'name_asc') return (a.full_name || '').localeCompare(b.full_name || '');
    if(sortBy === 'name_desc') return (b.full_name || '').localeCompare(a.full_name || '');
    if(sortBy === 'rent_desc') return (parseFloat(b.monthly_rate) || 0) - (parseFloat(a.monthly_rate) || 0);
    if(sortBy === 'rent_asc') return (parseFloat(a.monthly_rate) || 0) - (parseFloat(b.monthly_rate) || 0);

    // date_started_desc / date_started_asc -- tenants with no active
    // contract (no start date at all) always sort to the very end,
    // regardless of direction, rather than clumping unpredictably at
    // whichever end -Infinity/Infinity math would put them.
    const av = dateStartedSortValue(a);
    const bv = dateStartedSortValue(b);
    if(av === null && bv === null) return 0;
    if(av === null) return 1;
    if(bv === null) return -1;
    return sortBy === 'date_started_asc' ? av - bv : bv - av;
  }

  const $ = id => document.getElementById(id);
  const STATUS_LABEL = { active:'Active', delinquent:'Delinquent', pending_move_in_payment:'Pending Move-In', inactive:'Inactive' };

  document.querySelectorAll('[data-href]').forEach(el => {
    el.addEventListener('click', () => { window.location.href = el.dataset.href; });
  });

  $('logoutBtn').addEventListener('click', async () => {
    const logoutRes = await fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
    window.location.href = (await logoutRes.json().catch(() => ({}))).redirect || '/';
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

  function val(v){
    return (v === null || v === undefined || v === '')
      ? '<span class="v empty-v">Not provided</span>'
      : `<span class="v">${esc(v)}</span>`;
  }

  function peso(n){
    const num = parseFloat(n);
    return (n === null || n === undefined || isNaN(num)) ? '—' : '₱' + num.toLocaleString('en-PH', { minimumFractionDigits:2 });
  }

  async function api(url, options = {}){
    const headers = Object.assign({ 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' }, options.headers || {});
    const res = await fetch(url, Object.assign({}, options, { headers }));
    const body = await res.json().catch(() => ({}));
    if(!res.ok){
      const err = new Error(body.message || (body.errors ? Object.values(body.errors)[0][0] : `Request failed (${res.status})`));
      err.status = res.status;
      throw err;
    }
    return body;
  }

  function visible(){
    return tenants.filter(t => {
      if(statusFilter && t.status !== statusFilter) return false;
      if(tenantTypeFilter && t.tenant_type !== tenantTypeFilter) return false;
      if(leaseFilter === 'notarized_review' && t.notarized_status !== 'submitted') return false;
      if(leaseFilter && leaseFilter !== 'notarized_review' && t.lease_status !== leaseFilter) return false;
      if(!search) return true;
      const hay = `${t.full_name ?? ''} ${t.room_bed ?? ''}`.toLowerCase();
      return hay.includes(search);
    }).sort(compareTenants);
  }

  function countActiveTenants(){
    return tenants.filter(t => t.raw_status === 'active' && !t.is_blacklisted).length;
  }

  // Recount the lease filter options from the current list, so the numbers
  // stay right after a lease is renewed, ended or a copy is reviewed.
  function updateLeaseCounts(){
    const counts = {
      expiring_soon: tenants.filter(t => t.lease_status === 'expiring_soon').length,
      expired: tenants.filter(t => t.lease_status === 'expired').length,
      notarized_review: tenants.filter(t => t.notarized_status === 'submitted').length,
    };
    document.querySelectorAll('#leaseFilter option[data-label]').forEach(o => {
      o.textContent = `${o.dataset.label} (${counts[o.value] ?? 0})`;
    });
  }

  function renderTable(){
    updateLeaseCounts();
    const all = visible();
    const totalPages = Math.max(1, Math.ceil(all.length / PAGE_SIZE));
    if(page > totalPages) page = totalPages;
    const start = (page - 1) * PAGE_SIZE;
    const pageItems = all.slice(start, start + PAGE_SIZE);

    $('pageSub').textContent = `${countActiveTenants()} active tenants across all floors`;

    if(pageItems.length === 0){
      $('tableBody').innerHTML = '<tr class="empty-row"><td colspan="6">No records found.</td></tr>';
    } else {
      $('tableBody').innerHTML = pageItems.map(t => `
        <tr>
          <td><button type="button" class="name-link tenant-cell" data-view="${t.id}" aria-label="Open ${esc(t.full_name)}'s profile">
            ${avatarHtml(t)}
            <div>
              <div class="name">${esc(t.full_name)}</div>
              <div class="email">${esc(t.email ?? '')}</div>
            </div>
          </button></td>
          <td>${t.room_bed ? esc(t.room_bed) : '<span class="not-assigned">Not Assigned</span>'}</td>
          <td>${t.date_started ? esc(t.date_started) : '—'}</td>
          <td>${peso(t.monthly_rate)}</td>
          <td><span class="status-text ${t.status}">${STATUS_LABEL[t.status] ?? t.status}</span>${leaseFlag(t)}</td>
          <td>
            <div class="row-actions">
              <button class="btn sm primary" data-view="${t.id}">View Profile</button>
              <button class="btn sm" data-edit="${t.id}">Edit</button>
              <button class="btn sm" data-status="${t.id}">Set Status</button>
            </div>
          </td>
        </tr>`).join('');
    }

    $('tableBody').querySelectorAll('[data-view]').forEach(b => b.addEventListener('click', () => openProfile(Number(b.dataset.view))));
    $('tableBody').querySelectorAll('[data-edit]').forEach(b => b.addEventListener('click', () => openEditModal(Number(b.dataset.edit))));
    $('tableBody').querySelectorAll('[data-status]').forEach(b => b.addEventListener('click', () => openStatusModal(Number(b.dataset.status))));
    
    renderPagination(all.length, totalPages);
  }

  function renderPagination(total, totalPages){
    const shownStart = total === 0 ? 0 : (page - 1) * PAGE_SIZE + 1;
    const shownEnd = Math.min(page * PAGE_SIZE, total);
    $('tableSummary').textContent = total === 0 ? 'No results' : `Showing ${shownStart}-${shownEnd} of ${total}`;

    let html = `<button ${page === 1 ? 'disabled' : ''} data-page="${page - 1}" aria-label="Previous page"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg></button>`;
    for(let p = 1; p <= totalPages; p++){
      html += `<button class="${p === page ? 'active' : ''}" data-page="${p}">${p}</button>`;
    }
    html += `<button ${page === totalPages ? 'disabled' : ''} data-page="${page + 1}" aria-label="Next page"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg></button>`;
    $('pagination').innerHTML = html;
    $('pagination').querySelectorAll('[data-page]').forEach(b => {
      b.addEventListener('click', () => { page = Number(b.dataset.page); renderTable(); });
    });
  }

  $('searchInput').addEventListener('input', function(){
    search = this.value.trim().toLowerCase();
    page = 1;
    renderTable();
  });
  $('statusFilter').addEventListener('change', function(){
    statusFilter = this.value;
    page = 1;
    renderTable();
  });
  $('tenantTypeFilter').addEventListener('change', function(){
    tenantTypeFilter = this.value;
    page = 1;
    renderTable();
  });
  $('leaseFilter').addEventListener('change', function(){
    leaseFilter = this.value;
    page = 1;
    renderTable();
  });
  function leaseFlag(t){
    if(t.notarized_status === 'submitted') return '<span class="lease-flag review">Notarized copy to review</span>';
    if(t.lease_status === 'expiring_soon') return `<span class="lease-flag review">Lease ends ${esc(t.lease_end || 'soon')}</span>`;
    if(t.lease_status === 'expired' && t.status !== 'inactive') return '<span class="lease-flag">Lease expired</span>';
    return '';
  }
  $('sortFilter').addEventListener('change', function(){
    sortBy = this.value;
    page = 1;
    renderTable();
  });

  // ===== Tenant Profile window (Pureza revision 2026-11-14) =====
  // Clicking a tenant opens one big window with everything about them:
  //   Personal Information | Lease & Contract | Billing & Payment History | Activity Log
  // The Lease tab holds what used to be the separate Lease Management page.
  const CAN_MANAGE_LEASES = @json($canManageLeases ?? false);
  const CAN_MANAGE_BILLING = @json($canManageBilling ?? false);
  const LEASE_LABEL = { pending:'Pending', active:'Active', expiring_soon:'Expiring soon', expired:'Expired', terminated:'Terminated' };
  const TENANT_TYPE_LABEL = { student:'Student', working_student:'Working Student', full_time_employee:'Full-time Employee', part_time_employee:'Part-time Employee' };
  const ICON = {
    user:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>',
    shield:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
    doc:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>',
    bed:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 20V8M2 14h20v6M22 14v-3a3 3 0 00-3-3h-8v6"/><circle cx="6.5" cy="11" r="1.5"/></svg>',
    cal:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>',
    card:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>',
    alert:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg>',
    wallet:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 12V8H6a2 2 0 010-4h12v4"/><path d="M4 6v12a2 2 0 002 2h14v-4"/><path d="M18 12a2 2 0 000 4h4v-4z"/></svg>',
    mail:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 6l-10 7L2 6"/></svg>',
  };

  let profileTenant = null;   // GET /tenant-manager/{id}
  let profileTab = 'personal';
  let leaseData = null;       // GET /lease-contracts/tenants/{id}
  let billingData = null;     // GET /tenant-manager/{id}/payments
  let activityData = null;    // GET /tenant-manager/{id}/activity
  let activityFilter = '';
  let lastFocus = null;

  function closeProfile(){
    $('profileModal').classList.remove('open');
    document.body.style.overflow = '';
    document.querySelector('.app').inert = false;
    const u = new URL(location.href); u.searchParams.delete('view'); u.searchParams.delete('tab');
    history.replaceState(null, '', u);
    if(lastFocus) lastFocus.focus();
  }
  $('profileClose').addEventListener('click', closeProfile);
  $('profileModal').addEventListener('click', (e) => { if(e.target.id === 'profileModal') closeProfile(); });

  document.querySelectorAll('[data-ptab]').forEach(btn => btn.addEventListener('click', () => switchTab(btn.dataset.ptab)));

  // Arrow keys / Home / End move between tabs (standard tab pattern).
  const TAB_ORDER = ['personal', 'lease', 'billing', 'activity'];
  document.querySelector('.profile-tabs').addEventListener('keydown', (e) => {
    const i = TAB_ORDER.indexOf(profileTab);
    const next = { ArrowRight: i + 1, ArrowLeft: i - 1, Home: 0, End: TAB_ORDER.length - 1 }[e.key];
    if(next === undefined) return;
    e.preventDefault();
    const name = TAB_ORDER[(next + TAB_ORDER.length) % TAB_ORDER.length];
    switchTab(name);
    document.querySelector(`[data-ptab="${name}"]`).focus();
  });

  // Keep Tab-key focus inside the window while it's open.
  $('profileModal').addEventListener('keydown', (e) => {
    if(e.key !== 'Tab') return;
    const f = [...$('profileModal').querySelectorAll('button:not([disabled]), a[href], input:not([disabled]), select, textarea, [tabindex="0"]')].filter(el => el.offsetParent !== null);
    if(!f.length) return;
    if(e.shiftKey && document.activeElement === f[0]){ e.preventDefault(); f[f.length - 1].focus(); }
    else if(!e.shiftKey && document.activeElement === f[f.length - 1]){ e.preventDefault(); f[0].focus(); }
  });

  function busy(msg){ $('profileBody').setAttribute('aria-busy', 'true'); $('profileBody').innerHTML = `<p class="profile-msg">${msg}</p>`; }
  function failed(e, retry){
    $('profileBody').removeAttribute('aria-busy');
    $('profileBody').innerHTML = `<p class="profile-msg err" role="alert">Couldn't load this: ${esc(e.message)}<button type="button" class="btn sm" id="profileRetry">Try again</button></p>`;
    $('profileRetry').addEventListener('click', retry);
  }
  function setUrl(){
    if(!profileTenant) return;
    const u = new URL(location.href);
    u.searchParams.set('view', profileTenant.id);
    u.searchParams.set('tab', profileTab);
    history.replaceState(null, '', u);
  }

  function switchTab(name){
    profileTab = name;
    document.querySelectorAll('[data-ptab]').forEach(b => {
      const on = b.dataset.ptab === name;
      b.setAttribute('aria-selected', on ? 'true' : 'false');
      b.tabIndex = on ? 0 : -1;
    });
    setUrl();
    $('profileBody').setAttribute('aria-labelledby', 'tab-' + name);
    $('profileBody').scrollTop = 0;
    renderTab();
  }

  async function openProfile(id, tab){
    lastFocus = document.activeElement;
    profileTenant = null; leaseData = null; billingData = null; activityData = null; activityFilter = '';
    $('profileName').textContent = 'Loading…';
    $('profileStatus').textContent = '';
    $('profileStatus').className = 'pill';
    $('profileMeta').innerHTML = '';
    $('profileAvatar').innerHTML = '';
    $('profileFoot').innerHTML = '';
    busy('Loading tenant…');
    $('profileModal').classList.add('open');
    document.body.style.overflow = 'hidden';
    document.querySelector('.app').inert = true;
    $('profileClose').focus();
    try {
      const t = await api(`/tenant-manager/${id}`);
      t.id = t.id ?? id;
      profileTenant = t;
      renderHeader();
      switchTab(tab || 'personal');
    } catch(e){
      $('profileName').textContent = 'Tenant Profile';
      failed(e, () => openProfile(id, tab));
    }
  }

  async function reloadProfile(){
    if(!profileTenant) return;
    const id = profileTenant.id;
    try {
      profileTenant = Object.assign(await api(`/tenant-manager/${id}`), { id });
      billingData = null; leaseData = null; activityData = null;
      renderHeader();
      renderTab();
    } catch(e){ toast(e.message, true); }
  }

  function renderHeader(){
    const t = profileTenant;
    $('profileName').textContent = t.full_name;
    const st = t.display_status || t.status;
    $('profileStatus').className = 'pill ' + st;
    $('profileStatus').textContent = STATUS_LABEL[st] ?? st;
    $('profileAvatar').innerHTML = t.id_photo_url
      ? `<a href="${esc(t.id_photo_url)}" data-lightbox="tenant-photo-${t.id}" target="_blank" rel="noopener" aria-label="View ${esc(t.full_name)}'s 2x2 ID picture">${avatarHtml(t)}</a>`
      : avatarHtml(t);
    const c = t.contract;
    $('profileMeta').innerHTML = [
      c ? `<span>${ICON.bed}Room ${esc(c.room_no)} · ${esc(c.bed_label)}</span>` : `<span>${ICON.bed}No room assigned</span>`,
      t.tenant_since ? `<span>${ICON.cal}Since ${esc(t.tenant_since)}</span>` : '',
      t.is_blacklisted ? `<span style="color:var(--status-occupied);font-weight:700;">${ICON.alert}Blacklisted</span>` : '',
      t.outstanding_balance > 0 ? `<span style="color:var(--status-occupied);font-weight:700;">${ICON.card}${peso(t.outstanding_balance)} unpaid</span>` : '',
    ].join('');

    $('profileFoot').innerHTML = `
      <span class="label">Actions:</span>
      <button type="button" class="btn sm" id="pfEdit">Edit Details</button>
      <button type="button" class="btn sm" id="pfStatus">Set Status</button>
      ${t.signed_contract_url ? `<a class="btn sm" href="${esc(t.signed_contract_url)}" target="_blank" rel="noopener">View Contract</a>` : ''}
      <a class="btn sm" href="/tenant-manager/${t.id}/statement-of-account">Statement of Account (PDF)</a>
      ${t.email ? `<a class="btn sm" href="mailto:${esc(t.email)}">Send Email</a>` : ''}`;
    $('pfEdit').addEventListener('click', () => openEditModal(t.id));
    $('pfStatus').addEventListener('click', () => openStatusModal(t.id));
  }

  function renderTab(){
    if(!profileTenant) return;
    $('profileBody').removeAttribute('aria-busy');
    if(profileTab === 'personal') return renderPersonal();
    if(profileTab === 'lease') return renderLease();
    if(profileTab === 'billing') return renderBilling();
    return renderActivity();
  }

  // ----- Personal Information -----
  function ageFrom(dob){
    if(!dob) return null;
    const d = new Date(dob + 'T00:00:00'), now = new Date();
    let a = now.getFullYear() - d.getFullYear();
    if(now < new Date(now.getFullYear(), d.getMonth(), d.getDate())) a--;
    return isNaN(a) ? null : a;
  }
  function fmtDate(iso){
    if(!iso) return null;
    const d = new Date(iso + 'T00:00:00');
    return isNaN(d) ? iso : d.toLocaleDateString('en-PH', { month:'short', day:'numeric', year:'numeric' });
  }
  function kv(label, value){ return `<div><div class="k">${label}</div>${val(value)}</div>`; }

  function renderPersonal(){
    const t = profileTenant;
    const a = t.application || {};
    const age = ageFrom(t.date_of_birth);
    $('profileBody').innerHTML = `
      <div class="card">
        <h3>Personal Details</h3>
        <div class="kv wide">
          ${kv('Full Name', t.full_name)}
          ${kv('Email Address', t.email)}
          ${kv('Date of Birth', t.date_of_birth ? fmtDate(t.date_of_birth) + (age !== null ? ` (${age} years old)` : '') : null)}
          ${kv('Contact Number', t.contact_number)}
          ${kv('Gender', a.gender)}
          ${kv('Landline', a.landline)}
          ${kv('Nationality', a.nationality)}
          ${kv('Home Address', t.home_address)}
          ${kv('Tenant Type', TENANT_TYPE_LABEL[t.tenant_type] ?? t.tenant_type)}
          ${kv('Occupation', a.occupation)}
          ${kv('School / Company', a.school_company)}
          ${kv('School / Company Address', a.school_company_address)}
          ${kv('Medical Condition', a.medical_condition)}
          ${kv('Application Submitted', a.submitted_at)}
        </div>
      </div>
      <div class="card">
        <h3>Emergency Contact / Guardian</h3>
        <div class="kv wide">
          ${kv('Name', t.emergency_contact_name)}
          ${kv('Relationship', a.emergency_contact_relation)}
          ${kv('Contact Number', t.emergency_contact_number)}
          ${kv('Email', t.emergency_contact_email)}
          <div><div class="k">Billing &amp; Overdue Notices</div><span class="v">${t.emergency_billing_reminders
            ? '<span class="pill sm ok">On</span> Gets the same overdue notices as the tenant (SMS and email).'
            : '<span class="pill sm muted">Off</span> No consent on file, so only the tenant is notified.'}</span></div>
        </div>
      </div>
      <div class="card">
        <h3>Documents</h3>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
          ${t.id_photo_url ? `<a class="btn sm" href="${esc(t.id_photo_url)}" data-lightbox="tenant-docs-${t.id}" target="_blank" rel="noopener">2x2 ID Picture</a>` : ''}
          ${t.id_document_url ? `<a class="btn sm" href="${esc(t.id_document_url)}" data-lightbox="tenant-docs-${t.id}" target="_blank" rel="noopener">Valid ID</a>` : '<span class="note-muted">No valid ID on file.</span>'}
          ${t.emergency_contact_id_url ? `<a class="btn sm" href="${esc(t.emergency_contact_id_url)}" data-lightbox="tenant-docs-${t.id}" target="_blank" rel="noopener">Emergency Contact's ID</a>` : ''}
          ${t.signed_contract_url ? `<a class="btn sm" href="${esc(t.signed_contract_url)}" data-lightbox="tenant-docs-${t.id}" target="_blank" rel="noopener">Signed Contract</a>` : ''}
        </div>
      </div>
      ${t.status === 'inactive' ? `
      <div class="card">
        <h3>Deactivation Record</h3>
        <div class="kv wide">
          ${kv('Reason', t.deactivation_reason)}
          ${kv('Date', t.deactivated_at)}
          ${kv('Deactivated By', t.deactivated_by_name)}
        </div>
      </div>` : ''}`;
  }

  // ----- Lease & Contract (was the Lease Management page) -----
  async function renderLease(){
    const t = profileTenant;
    if(!CAN_MANAGE_LEASES){
      const c = t.contract;
      $('profileBody').innerHTML = `
        <div class="card"><h3>Current Lease</h3>
          ${c ? `<div class="kv wide">${kv('Room', c.room_no)}${kv('Bed', c.bed_label)}${kv('Start Date', c.start_date)}${kv('End Date', c.end_date)}${kv('Monthly Rate', peso(c.monthly_rate))}</div>`
              : '<p class="note-muted">No active lease on file.</p>'}
          <p class="note-muted" style="margin-top:12px;">You need the "Manage Contracts" privilege to renew, terminate or review contracts.</p>
        </div>`;
      return;
    }
    if(!leaseData){
      busy('Loading leases…');
      try { leaseData = await api(`/lease-contracts/tenants/${t.id}`); }
      catch(e){ failed(e, renderTab); return; }
      if(profileTab !== 'lease') return;
    }
    const list = leaseData.contracts || [];
    const current = list.find(c => ['pending','active','expiring_soon'].includes(c.status)) || list[0];
    const older = list.filter(c => c !== current);
    const hasOpenLease = list.some(c => ['pending','active','expiring_soon'].includes(c.status));

    $('profileBody').innerHTML = `
      ${current ? leaseCardHtml(current, true) : `<div class="card"><h3>Lease</h3><p class="note-muted">This tenant has no lease yet.</p></div>`}
      ${older.length ? `
        <div class="card"><h3>Lease History <span class="right note-muted">${older.length} older lease${older.length === 1 ? '' : 's'}</span></h3>
          <div class="table-scroll"><table class="mini-table">
            <thead><tr><th>Contract</th><th>Room / Bed</th><th>Start</th><th>End</th><th>Status</th><th class="num">Rate</th></tr></thead>
            <tbody>${older.map(c => `<tr><td>#${c.id}</td><td>Room ${esc(c.room_no ?? '—')} · ${esc(c.bed_label ?? '—')}</td><td>${esc(c.start_date ?? '—')}</td><td>${esc(c.end_date ?? '—')}</td>
              <td><span class="status-text">${LEASE_LABEL[c.status] ?? esc(c.status)}</span>${c.termination_reason ? `<div class="note-muted">${esc(c.termination_reason)}</div>` : ''}</td><td class="num">${peso(c.monthly_rate)}</td></tr>`).join('')}</tbody>
          </table></div>
        </div>` : ''}
      ${!hasOpenLease && t.status !== 'inactive' ? newLeaseHtml() : ''}`;
    wireLease(current);
    wireNewLease();
  }

  function leaseCardHtml(c){
    const canAct = ['pending','active','expiring_soon'].includes(c.status);
    const canRenew = ['active','expiring_soon','expired'].includes(c.status);
    const remaining = (c.remaining_days === null || c.remaining_days === undefined || c.status === 'terminated') ? null
      : (c.remaining_days < 0 ? `Ended ${Math.abs(c.remaining_days)} days ago` : `${c.remaining_days} days left`);
    return `
      <div class="card">
        <h3>Contract #${c.id} <span class="right"><span class="pill sm ${c.status === 'active' ? 'ok' : (c.status === 'expiring_soon' || c.status === 'pending' ? 'wait' : 'muted')}">${LEASE_LABEL[c.status] ?? esc(c.status)}</span></span></h3>
        <div class="kv wide">
          ${kv('Room / Bed', `Room ${c.room_no ?? '—'} · ${c.bed_label ?? '—'}`)}
          ${kv('Monthly Rate', peso(c.monthly_rate))}
          ${kv('Discount', c.discount_amount ? peso(c.discount_amount) : null)}
          ${kv('Start Date', c.start_date)}
          ${kv('End Date', c.end_date)}
          ${kv('Remaining', remaining)}
        </div>
      </div>
      <div class="grid2">
        <div class="card"><h3>Signature</h3>${signSectionHtml(c)}</div>
        ${notarizedSectionHtml(c) || `<div class="card"><h3>Notarized Contract</h3><p class="note-muted">Not required by the dormitory.</p></div>`}
      </div>
      ${outcomeHtml(c)}
      ${canAct ? `
      <div class="card">
        <h3>Lease Actions</h3>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
          ${c.esign_status === 'pending' ? `<button class="btn sm" id="markNaBtn">Mark signature not required</button>` : ''}
          ${canRenew ? `<button class="btn sm primary" id="showRenewBtn" ${c.notarization_missing ? 'disabled aria-describedby="renewBlockedHint"' : ''}>Renew Lease</button>` : ''}
          <button class="btn sm warn" id="showTerminateBtn">Terminate Lease</button>
        </div>
        ${canRenew && c.notarization_missing ? `<p class="action-hint" id="renewBlockedHint">Renewal is locked until a notarized copy is approved.</p>` : ''}
        <div class="action-box" id="renewBox">
          <div class="fld"><label for="renewEndDate">New End Date</label><input type="date" id="renewEndDate"></div>
          <div style="display:flex;gap:8px;"><button class="btn primary" id="confirmRenewBtn">Confirm Renewal</button><button class="btn" id="cancelRenewBtn">Cancel</button></div>
        </div>
        <div class="action-box" id="terminateBox">
          <div class="fld"><label for="terminateReason">Termination Reason</label><textarea id="terminateReason" placeholder="Explain why this lease is being terminated..."></textarea></div>
          <div style="display:flex;gap:8px;"><button class="btn warn" id="confirmTerminateBtn">Confirm Termination</button><button class="btn" id="cancelTerminateBtn">Cancel</button></div>
        </div>
      </div>` : (canRenew ? `
      <div class="card">
        <h3>Lease Actions</h3>
        <button class="btn sm primary" id="showRenewBtn" ${c.notarization_missing ? 'disabled' : ''}>Renew Lease</button>
        ${c.notarization_missing ? `<p class="action-hint">Renewal is locked until a notarized copy is approved.</p>` : ''}
        <div class="action-box" id="renewBox">
          <div class="fld"><label for="renewEndDate">New End Date</label><input type="date" id="renewEndDate"></div>
          <div style="display:flex;gap:8px;"><button class="btn primary" id="confirmRenewBtn">Confirm Renewal</button><button class="btn" id="cancelRenewBtn">Cancel</button></div>
        </div>
      </div>` : '')}`;
  }

  function signSectionHtml(c){
    if(c.esign_status === 'signed'){
      return `<div class="signed-note"><strong>Signed${c.signed_at ? ' on ' + esc(c.signed_at) : ''}.</strong><br>${c.signed_document_url ? `<a href="${esc(c.signed_document_url)}" data-lightbox="contract-${c.id}" target="_blank" rel="noopener">View the signed document</a>` : 'No file attached.'}</div>`;
    }
    if(c.esign_status === 'not_applicable'){
      return `<div class="signed-note"><strong>Marked as not requiring a signature.</strong></div>`;
    }
    return `
      <div class="sign-box">
        <div class="fld"><label for="signedFile">Scan of the signed contract</label><input type="file" id="signedFile" accept=".pdf,.jpg,.jpeg,.png"></div>
        <div class="fld"><label for="signedDate">Date signed</label><input type="date" id="signedDate"></div>
        <label class="confirm-row"><input type="checkbox" id="confirmSign"><span>I confirm the tenant has physically signed this contract.</span></label>
        <button class="btn primary" id="submitSignBtn" style="width:100%;">Record Signed Contract</button>
      </div>`;
  }

  function notarizedSectionHtml(c){
    const required = !!(leaseData && leaseData.notarization_required);
    const st = c.notarized_status || 'none';
    if(!required && st === 'none') return '';
    const ended = ['terminated','expired'].includes(c.status);
    const viewLink = c.notarized_url ? `<br><a href="${esc(c.notarized_url)}" target="_blank" rel="noopener">Open the uploaded copy</a>` : '';
    const when = c.notarized_at ? ` Uploaded ${esc(c.notarized_at)}.` : '';
    let note = '', review = '';
    if(st === 'approved'){
      note = `<div class="signed-note"><strong>Notarized copy approved.</strong>${when}${viewLink}</div>`;
    } else if(st === 'submitted'){
      note = `<div class="signed-note review" role="status"><strong>Submitted by the tenant. Needs your review.</strong>${when} Check that the notary's seal, signature and document number are visible and that it is this contract.${viewLink}</div>`;
      review = `
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;">
          <button class="btn sm primary" id="approveNotarizedBtn">Approve Copy</button>
          <button class="btn sm warn" id="showRejectNotarizedBtn">Send Back</button>
        </div>
        <div class="action-box" id="rejectNotarizedBox">
          <div class="fld"><label for="rejectNotarizedReason">Reason the tenant will see</label><textarea id="rejectNotarizedReason" maxlength="255" placeholder="e.g. The notary seal is cut off. Please upload a full, clear scan."></textarea></div>
          <div style="display:flex;gap:8px;"><button class="btn warn" id="confirmRejectNotarizedBtn">Send Back to Tenant</button><button class="btn" id="cancelRejectNotarizedBtn">Cancel</button></div>
        </div>`;
    } else if(st === 'rejected'){
      note = `<div class="signed-note missing" role="status"><strong>Sent back to the tenant.</strong> Reason: ${esc(c.notarized_rejection_reason ?? '')}<br>Waiting for the tenant to upload a new copy.${viewLink}</div>`;
    } else {
      note = `<div class="signed-note missing" role="status"><strong>Notarized copy required.</strong><br>The tenant can upload it from My Account, or you can upload it below. This lease can't be renewed until a copy is approved.</div>`;
    }
    const label = st === 'approved' ? 'Replace with a new scan' : 'Upload the copy yourself (approved right away)';
    const form = required && !ended && st !== 'submitted' ? `
      <div class="sign-box">
        <div class="fld"><label for="notarizedFile">${label}</label><input type="file" id="notarizedFile" accept=".pdf,.jpg,.jpeg,.png" aria-describedby="notarizedFileHint"><span id="notarizedFileHint" style="font-size:11px;color:var(--text-light);">PDF, JPG or PNG, up to 10 MB.</span></div>
        <button class="btn ${st === 'approved' ? '' : 'primary'}" id="uploadNotarizedBtn" style="width:100%;">${st === 'approved' ? 'Replace Notarized Copy' : 'Upload Notarized Contract'}</button>
      </div>` : '';
    return `<div class="card"><h3>Notarized Contract</h3>${note}${review}${form}</div>`;
  }

  function outcomeHtml(c){
    if(c.status === 'terminated' && c.termination_reason){
      return `<div class="card"><h3>Termination Reason</h3><p style="margin:0;font-size:13px;color:var(--status-occupied);">${esc(c.termination_reason)}${c.terminated_at ? '<br><span class="note-muted">' + esc(c.terminated_at) + '</span>' : ''}</p></div>`;
    }
    if(c.last_renewed_at){
      return `<div class="card"><h3>Renewal History</h3><p style="margin:0;font-size:13px;color:var(--green-accent);">Last renewed ${esc(c.last_renewed_at)}.</p></div>`;
    }
    return '';
  }

  function newLeaseHtml(){
    return `
      <div class="card">
        <h3>Add a New Lease</h3>
        <p class="note-muted" style="margin-bottom:12px;">This tenant has no active lease. Pick a vacant bed to create one.</p>
        <div class="sign-box" style="margin-top:0;">
          <div class="grid2">
            <div class="fld"><label for="nlRoom">Room</label><select id="nlRoom"><option value="">Loading rooms…</option></select></div>
            <div class="fld"><label for="nlBed">Bedspace</label><select id="nlBed"><option value="">Select a room first</option></select></div>
            <div class="fld"><label for="nlStart">Lease Start Date</label><input type="date" id="nlStart"></div>
            <div class="fld"><label for="nlEnd">Lease End Date</label><input type="date" id="nlEnd"></div>
          </div>
          <div class="fld"><label for="nlDoc">Signed Contract Document (optional)</label><input type="file" id="nlDoc" accept=".pdf,.jpg,.jpeg,.png"></div>
          <button class="btn primary" id="nlSubmit" style="width:100%;">Create Lease</button>
        </div>
      </div>`;
  }

  function wireNewLease(){
    if(!$('nlSubmit')) return;
    loadRooms($('nlRoom'), $('nlBed'));
    $('nlSubmit').addEventListener('click', async function(){
      if(!$('nlBed').value) return toast('Select a bedspace.', true);
      if(!$('nlStart').value || !$('nlEnd').value) return toast('Set both the start and end dates.', true);
      const form = new FormData();
      form.append('tenant_id', profileTenant.id);
      form.append('bed_id', $('nlBed').value);
      form.append('start_date', $('nlStart').value);
      form.append('end_date', $('nlEnd').value);
      if($('nlDoc').files[0]) form.append('signed_document', $('nlDoc').files[0]);
      this.disabled = true;
      try {
        await api('/lease-contracts', { method:'POST', body: form });
        toast('Lease contract added.');
        await afterLeaseChange();
      } catch(e){ toast(e.message, true); this.disabled = false; }
    });
  }

  // After any lease change: refresh the profile and the tenant's table row.
  async function afterLeaseChange(){
    await reloadProfile();
    refreshRow(profileTenant.id);
  }
  async function refreshRow(id){
    // The list rows come from the page load; reload them quietly in the background.
    try {
      const html = await fetch(location.pathname, { headers:{ 'Accept':'text/html' } }).then(r => r.text());
      const m = html.match(/<script type="application\/json" id="tenants-data">([\s\S]*?)<\/script>/);
      if(m){ tenants = JSON.parse(m[1]); renderTable(); }
    } catch(e){ /* the table refreshes on the next page load */ }
  }

  function wireLease(c){
    if(!c) return;
    const ok = async (res, msg) => { toast(msg || res.message || 'Saved.'); await afterLeaseChange(); };

    const notarizedBtn = $('uploadNotarizedBtn');
    if(notarizedBtn){
      notarizedBtn.addEventListener('click', async function(){
        const file = $('notarizedFile').files[0];
        if(!file) return toast('Attach the scan of the notarized contract.', true);
        if(file.size > 10 * 1024 * 1024) return toast('That file is over 10 MB. Upload a smaller scan.', true);
        const form = new FormData();
        form.append('notarized_document', file);
        this.disabled = true; this.textContent = 'Uploading…';
        try { await ok(await api(`/lease-contracts/${c.id}/notarized`, { method:'POST', body: form })); }
        catch(e){ toast(e.message, true); this.disabled = false; this.textContent = 'Upload Notarized Contract'; }
      });
    }

    const approveNBtn = $('approveNotarizedBtn');
    if(approveNBtn){
      approveNBtn.addEventListener('click', async function(){
        this.disabled = true;
        try { await ok(await api(`/lease-contracts/${c.id}/notarized/approve`, { method:'PATCH' })); }
        catch(e){ toast(e.message, true); this.disabled = false; }
      });
      $('showRejectNotarizedBtn').addEventListener('click', () => { $('rejectNotarizedBox').classList.add('open'); $('rejectNotarizedReason').focus(); });
      $('cancelRejectNotarizedBtn').addEventListener('click', () => { $('rejectNotarizedBox').classList.remove('open'); $('showRejectNotarizedBtn').focus(); });
      $('confirmRejectNotarizedBtn').addEventListener('click', async function(){
        const reason = $('rejectNotarizedReason').value.trim();
        if(reason.length < 5) return toast('Tell the tenant why the copy was not accepted.', true);
        this.disabled = true;
        try { await ok(await api(`/lease-contracts/${c.id}/notarized/reject`, { method:'PATCH', headers:{ 'Content-Type':'application/json' }, body: JSON.stringify({ reason }) })); }
        catch(e){ toast(e.message, true); this.disabled = false; }
      });
    }

    const signBtn = $('submitSignBtn');
    if(signBtn){
      signBtn.addEventListener('click', async function(){
        if(!$('confirmSign').checked) return toast('Tick the confirmation box first.', true);
        const file = $('signedFile').files[0];
        if(!file) return toast('Attach the scan of the signed contract.', true);
        const form = new FormData();
        form.append('signed_document', file);
        if($('signedDate').value) form.append('signed_at', $('signedDate').value);
        this.disabled = true;
        try { await ok(await api(`/lease-contracts/${c.id}/sign`, { method:'POST', body: form }), 'Signed contract recorded. Lease is now active.'); }
        catch(e){ toast(e.message, true); this.disabled = false; }
      });
    }

    const naBtn = $('markNaBtn');
    if(naBtn){
      naBtn.addEventListener('click', async function(){
        if(!confirm('Mark this contract as not requiring a signature?')) return;
        try { await ok(await api(`/lease-contracts/${c.id}/not-applicable`, { method:'PATCH' }), 'Contract marked as not requiring a signature.'); }
        catch(e){ toast(e.message, true); }
      });
    }

    const showRenewBtn = $('showRenewBtn');
    if(showRenewBtn){
      showRenewBtn.addEventListener('click', () => { $('renewBox').classList.add('open'); const tb = $('terminateBox'); if(tb) tb.classList.remove('open'); $('renewEndDate').focus(); });
      $('cancelRenewBtn').addEventListener('click', () => $('renewBox').classList.remove('open'));
      $('confirmRenewBtn').addEventListener('click', async function(){
        const newEnd = $('renewEndDate').value;
        if(!newEnd) return toast('Pick a new end date.', true);
        this.disabled = true;
        try { await ok(await api(`/lease-contracts/${c.id}/renew`, { method:'PATCH', headers:{ 'Content-Type':'application/json' }, body: JSON.stringify({ end_date:newEnd }) }), 'Lease renewed successfully.'); }
        catch(e){ toast(e.message, true); this.disabled = false; }
      });
    }

    const showTermBtn = $('showTerminateBtn');
    if(showTermBtn){
      showTermBtn.addEventListener('click', () => { $('terminateBox').classList.add('open'); const rb = $('renewBox'); if(rb) rb.classList.remove('open'); $('terminateReason').focus(); });
      $('cancelTerminateBtn').addEventListener('click', () => $('terminateBox').classList.remove('open'));
      $('confirmTerminateBtn').addEventListener('click', async function(){
        const reason = $('terminateReason').value.trim();
        if(!reason) return toast('A termination reason is required.', true);
        if(!confirm('Terminate this lease? The bedspace will be released back to vacant.')) return;
        this.disabled = true;
        try { await ok(await api(`/lease-contracts/${c.id}/terminate`, { method:'PATCH', headers:{ 'Content-Type':'application/json' }, body: JSON.stringify({ reason }) }), 'Lease terminated successfully.'); }
        catch(e){ toast(e.message, true); this.disabled = false; }
      });
    }
  }

  // ----- Billing & Payment History -----
  const BILL_STATUS = { paid:['ok','Paid'], unpaid:['wait','Unpaid'], partial:['wait','Partly paid'], overdue:['bad','Overdue'] };
  const PAY_STATUS = { approved:['ok','Approved'], pending:['wait','Waiting for review'], rejected:['bad','Rejected'], replaced:['muted','Replaced'] };

  async function renderBilling(){
    const t = profileTenant;
    if(!billingData){
      busy('Loading payment history…');
      try { billingData = await api(`/tenant-manager/${t.id}/payments`); }
      catch(e){ failed(e, renderTab); return; }
      if(profileTab !== 'billing') return;
    }
    const b = billingData;
    const d = t.deposit || {};
    const isImg = url => /\.(jpe?g|png|gif|webp)(\?|$)/i.test(url || '');

    $('profileBody').innerHTML = `
      <div class="money-strip">
        <div><div class="k">Outstanding</div><div class="n ${b.totals.outstanding > 0 ? 'red' : ''}">${peso(b.totals.outstanding)}</div></div>
        <div><div class="k">Total paid</div><div class="n">${peso(b.totals.paid)}</div></div>
        <div><div class="k">Deposit left</div><div class="n">${d.refund ? '—' : peso(d.held || 0)}</div></div>
        <div><div class="k">Proofs to review</div><div class="n">${b.totals.pending_review}</div></div>
      </div>

      <div class="card">
        <h3>Payment History <span class="right note-muted">Same proofs the tenant uploaded on their Billing page</span></h3>
        ${b.payments.length ? `<div class="pay-list">${b.payments.map(p => {
          const st = PAY_STATUS[p.status] || ['muted', p.status];
          const proof = p.proof_url
            ? (isImg(p.proof_url)
                ? `<a class="proof" href="${esc(p.proof_url)}" data-lightbox="proofs-${t.id}" target="_blank" rel="noopener" aria-label="Open proof of payment"><img src="${esc(p.proof_url)}" alt="Proof of payment" loading="lazy"></a>`
                : `<a class="proof" href="${esc(p.proof_url)}" target="_blank" rel="noopener">Open PDF proof</a>`)
            : `<div class="proof">${p.recorded_by ? 'Recorded at the office' : 'No proof file'}</div>`;
          return `<div class="pay-item">${proof}
            <div class="info">
              <div class="top"><span class="amt">${peso(p.amount)}</span><span class="pill sm ${st[0]}">${st[1]}</span></div>
              <div class="sub">
                <strong>${esc(p.bill)}</strong> · ${esc(p.method || '—')}${p.reference ? ' · Ref ' + esc(p.reference) : ''}<br>
                Paid on ${esc(p.payment_date || '—')} · submitted ${esc(p.submitted_at || '—')}${p.recorded_by ? ' · recorded by ' + esc(p.recorded_by) : ''}
                ${p.reviewed_at ? `<br>Reviewed ${esc(p.reviewed_at)}` : ''}
                ${p.review_notes ? `<br>Note: ${esc(p.review_notes)}` : ''}
              </div>
              ${p.receipt_url && CAN_MANAGE_BILLING ? `<a class="doc-link" href="${esc(p.receipt_url)}" style="margin-top:6px;">Download receipt</a>` : ''}
            </div>
          </div>`;
        }).join('')}</div>` : '<p class="note-muted">No payments yet.</p>'}
      </div>

      <div class="card">
        <h3>Bills</h3>
        ${b.bills.length ? `<div class="table-scroll"><table class="mini-table">
          <thead><tr><th>Bill</th><th>Due</th><th>Status</th><th class="num">Total</th><th class="num">Penalty</th><th class="num">Balance</th></tr></thead>
          <tbody>${b.bills.map(x => { const st = BILL_STATUS[x.status] || ['muted', x.status];
            return `<tr><td>${esc(x.label)}</td><td>${esc(x.due_date || '—')}</td><td><span class="pill sm ${st[0]}">${st[1]}</span></td>
              <td class="num">${peso(x.total)}</td><td class="num">${x.penalty > 0 ? peso(x.penalty) : '—'}</td><td class="num" style="${x.balance > 0 ? 'color:var(--status-occupied);font-weight:700;' : ''}">${peso(x.balance)}</td></tr>`; }).join('')}</tbody>
        </table></div>` : '<p class="note-muted">No bills yet.</p>'}
      </div>

      <div class="card">
        <h3>Penalties <span class="right note-muted">Paid from the security deposit first while some is left</span></h3>
        ${b.penalties.length ? `<div class="table-scroll"><table class="mini-table">
          <thead><tr><th>Date</th><th>Description</th><th>Status</th><th class="num">Amount</th><th class="num">From Deposit</th><th class="num">Billed</th></tr></thead>
          <tbody>${b.penalties.map(p => `<tr>
            <td>${esc(p.date || '—')}</td><td>${esc(p.description)}</td>
            <td><span class="pill sm ${p.status === 'active' ? 'wait' : 'muted'}">${p.status === 'active' ? 'Active' : 'Waived'}</span></td>
            <td class="num">${peso(p.amount)}</td>
            <td class="num">${p.from_deposit > 0 ? peso(p.from_deposit) : '—'}</td>
            <td class="num">${p.charged > 0 ? peso(p.charged) + (p.billed ? '' : ' (next bill)') : '—'}</td></tr>`).join('')}</tbody>
        </table></div>` : '<p class="note-muted">No penalties.</p>'}
      </div>

      <div class="card" id="depositSec">${depositHtml(t)}</div>`;
  }

  // ----- Activity Log -----
  const ACT_KINDS = { '':'All', notification:'Portal notifications', delinquency:'Delinquency steps', guardian:'Guardian notices', email:'Emails' };
  async function renderActivity(){
    const t = profileTenant;
    if(!activityData){
      busy('Loading activity…');
      try { activityData = await api(`/tenant-manager/${t.id}/activity`); }
      catch(e){ failed(e, renderTab); return; }
      if(profileTab !== 'activity') return;
    }
    const items = activityData.filter(i => !activityFilter || i.kind === activityFilter);
    const STATUS_PILL = { sent:'ok', resolved:'ok', pending:'wait', failed:'bad' };
    $('profileBody').innerHTML = `
      <div class="card">
        <h3>Activity Log</h3>
        <div class="chip-row" role="group" aria-label="Filter activity">
          ${Object.entries(ACT_KINDS).map(([k, label]) => `<button type="button" class="chip" data-act="${k}" aria-pressed="${activityFilter === k}">${label}</button>`).join('')}
        </div>
        ${items.length ? `<ul class="timeline">${items.map(i => `
          <li class="${esc(i.kind)}">
            <div class="t">${esc(i.title)} ${i.status ? `<span class="pill sm ${STATUS_PILL[i.status] || 'muted'}">${esc(i.status)}</span>` : ''}</div>
            ${i.detail ? `<div class="d">${esc(i.detail)}</div>` : ''}
            <div class="w"><span class="tag">${esc(ACT_KINDS[i.kind] || i.kind)}</span> · ${esc(i.at)}${i.by ? ' · by ' + esc(i.by) : ''}</div>
          </li>`).join('')}</ul>` : '<p class="note-muted">Nothing recorded yet.</p>'}
      </div>`;
    $('profileBody').querySelectorAll('[data-act]').forEach(b => b.addEventListener('click', () => { activityFilter = b.dataset.act; renderActivity(); }));
  }

  // ===== Security deposit (v39) =====
  function depositHtml(t){
    const d = t.deposit || {};
    const e = d.eligibility || {};
    const title = `<h3>Security Deposit</h3>`;
    if(d.refund){
      const r = d.refund;
      return `${title}
        <div class="kv wide">
          ${d.used_for_penalties > 0 ? `<div><div class="k">Used for Penalties</div>${val(peso(d.used_for_penalties))}</div>` : ''}
          <div><div class="k">Deposit at Move-out</div>${val(peso(r.deposit_amount))}</div>
          <div><div class="k">Deductions</div>${val(peso(r.deductions_amount))}${r.deductions_note ? `<div class="k" style="margin-top:4px;text-transform:none;letter-spacing:0;">${esc(r.deductions_note)}</div>` : ''}</div>
          <div><div class="k">Refunded</div>${r.forfeited ? '<span class="v">Forfeited (stay under the minimum)</span>' : val(peso(r.refund_amount))}</div>
          <div><div class="k">Date &amp; Method</div>${val(r.refunded_at + ' · ' + r.refund_method)}</div>
          <div><div class="k">Reference</div>${val(r.reference_number)}</div>
          <div><div class="k">Recorded By</div>${val(r.recorded_by)}</div>
        </div>`;
    }
    return `${title}
      <div class="kv wide">
        <div><div class="k">Deposit Paid</div><span class="v">${d.paid ? peso(d.paid) : 'None on record'}</span></div>
        <div><div class="k">Used for Penalties</div><span class="v">${d.used_for_penalties > 0 ? peso(d.used_for_penalties) : '—'}</span></div>
        <div><div class="k">Deposit Left</div><span class="v" style="font-weight:700;">${d.paid ? peso(d.held || 0) : '—'}</span></div>
        <div><div class="k">Status</div><span class="v">${!d.paid ? 'Move-in fee not yet paid' : (d.held > 0 ? 'Held until move-out. New penalties are taken from it first.' : 'Used up. New penalties go on the tenant\'s bill.')}</span></div>
        ${e.required_months ? `<div><div class="k">Refundable?</div><span class="v">${e.refundable ? `Yes, stayed ${e.months_stayed} months (minus deductions)` : `Not yet, stayed ${e.months_stayed} of ${e.required_months} months. Moving out now forfeits it.`}</span></div>` : ''}
        ${d.unused_pay_ahead > 0 ? `<div><div class="k">Unused Rent Paid Ahead</div><span class="v">${peso(d.unused_pay_ahead)}, return it to the tenant at move-out</span></div>` : ''}
      </div>
      <button type="button" class="btn" id="openDepositRefundBtn" style="margin-top:12px;">Record Deposit Refund</button>`;
  }

  function openDepositModal(){
    const d = profileTenant.deposit || {};
    $('drDeposit').value = d.held ? Number(d.held).toFixed(2) : '';
    // Only suggest deductions against a deposit that exists; for a walk-in
    // the admin types both amounts.
    const forfeit = d.eligibility && !d.eligibility.refundable;
    const suggest = forfeit ? (d.held || 0) : (d.held ? Math.min(d.suggested_deductions || 0, d.held) : 0);
    $('drDeductions').value = suggest.toFixed(2);
    $('drNote').value = forfeit ? `Forfeited: stayed ${d.eligibility.months_stayed} of ${d.eligibility.required_months} months` : (suggest > 0 ? 'Unpaid balance and penalties' : '');
    $('drDeductions').readOnly = $('drNote').readOnly = !!forfeit;
    $('drForfeitNote').style.display = forfeit ? 'block' : 'none';
    $('drForfeitNote').innerHTML = forfeit
      ? `<strong>Deposit forfeited.</strong> This tenant stayed ${d.eligibility.months_stayed} of the ${d.eligibility.required_months} months needed, so the deposit is non-refundable. The refund will be ₱0.00.`
      : '';
    $('drSubmitBtn').textContent = forfeit ? 'Record Forfeit' : 'Save Refund';
    $('drMethod').value = 'Cash';
    $('drReference').value = '';
    $('drDate').value = new Date().toISOString().slice(0, 10);
    $('drError').classList.remove('visible');
    updateRefundPreview();
    $('depositRefundModal').classList.add('open');
  }
  function updateRefundPreview(){
    const refund = (Number($('drDeposit').value) || 0) - (Number($('drDeductions').value) || 0);
    $('drRefundPreview').textContent = peso(Math.max(0, refund));
  }
  ['drDeposit', 'drDeductions'].forEach(id => $(id).addEventListener('input', updateRefundPreview));
  $('drCancelBtn').addEventListener('click', () => $('depositRefundModal').classList.remove('open'));
  $('drSubmitBtn').addEventListener('click', async function(){
    this.disabled = true;
    $('drError').classList.remove('visible');
    try {
      const res = await api(`/tenant-manager/${profileTenant.id}/deposit-refund`, {
        method:'POST',
        headers:{ 'Content-Type':'application/json' },
        body: JSON.stringify({
          deposit_amount: $('drDeposit').value,
          deductions_amount: $('drDeductions').value || 0,
          deductions_note: $('drNote').value.trim() || null,
          refund_method: $('drMethod').value.trim(),
          reference_number: $('drReference').value.trim() || null,
          refunded_at: $('drDate').value,
        }),
      });
      profileTenant.deposit = res.deposit;
      if($('depositSec')) $('depositSec').innerHTML = depositHtml(profileTenant);
      $('depositRefundModal').classList.remove('open');
      toast('Deposit refund recorded.');
    } catch(e){
      $('drError').textContent = e.message;
      $('drError').classList.add('visible');
    }
    this.disabled = false;
  });
  $('profileBody').addEventListener('click', (e) => {
    if(e.target.id === 'openDepositRefundBtn') openDepositModal();
  });

  // 2x2 ID picture as the tenant's profile picture; initials when there's none.
  function avatarHtml(t){
    return t.id_photo_url
      ? `<img class="tenant-avatar" src="${esc(t.id_photo_url)}" alt="" loading="lazy">`
      : `<span class="tenant-avatar" aria-hidden="true">${esc(t.initials || '?')}</span>`;
  }

  // ===== Add New Tenant modal =====
  $('openAddModalBtn').addEventListener('click', () => {
    resetAddModal();
    loadRooms($('addRoomSelect'), $('addBedSelect'));
    $('addModal').classList.add('open');
  });
  $('cancelAddBtn').addEventListener('click', () => $('addModal').classList.remove('open'));

  function resetAddModal(){
    $('addModalError').classList.remove('visible');
    ['addFirstName','addLastName','addDob','addHomeAddress','addContactNumber','addEmail','addEmergencyName','addEmergencyNumber','addStartDate','addEndDate'].forEach(id => $(id).value = '');
    $('addTenantType').value = '';
    $('addIdDocument').value = '';
    $('addIdPhoto').value = '';
    $('addSignedContract').value = '';
    $('addRoomSelect').innerHTML = '<option value="">Loading rooms…</option>';
    $('addRoomSelect').disabled = false;
    $('addBedSelect').innerHTML = '<option value="">Select a room first</option>';
  }

  function loadRooms(roomSelect, bedSelect){
    fetch('/public-api/rooms')
      .then(r => r.json())
      .then(rooms => {
        // Table 15, step 6: only rooms with at least one vacant bed should
        // be selectable, and the dropdown should show room type. If
        // nothing at all is available, show "No Available Rooms" rather
        // than an empty-looking dropdown.
        const available = rooms.filter(r => (r.beds || []).some(b => b.status === 'vacant'));
        if(available.length === 0){
          roomSelect.innerHTML = '<option value="">No Available Rooms</option>';
          roomSelect.disabled = true;
          return;
        }
        roomSelect.disabled = false;
        roomSelect.innerHTML = '<option value="">Select a room</option>' +
          available.map(r => {
            const vacantCount = (r.beds || []).filter(b => b.status === 'vacant').length;
            const type = r.room_type ? ` (${esc(r.room_type)})` : '';
            return `<option value="${r.id}">Room ${esc(r.room_no)}${type}: ${vacantCount} bed${vacantCount === 1 ? '' : 's'} available</option>`;
          }).join('');
      })
      .catch(() => { roomSelect.innerHTML = '<option value="">Could not load rooms</option>'; });

    roomSelect.onchange = function(){
      bedSelect.innerHTML = '<option value="">Loading beds…</option>';
      if(!this.value){ bedSelect.innerHTML = '<option value="">Select a room first</option>'; return; }
      fetch(`/public-api/rooms/${this.value}/beds`)
        .then(r => r.json())
        .then(beds => {
          bedSelect.innerHTML = beds.length
            ? '<option value="">Select a bed</option>' + beds.map(b => `<option value="${b.id}">${esc(b.bed_label)}</option>`).join('')
            : '<option value="">No Available Rooms</option>';
        })
        .catch(() => { bedSelect.innerHTML = '<option value="">Could not load beds</option>'; });
    };
  }

  $('submitAddBtn').addEventListener('click', async function(){
    const errEl = $('addModalError');
    errEl.classList.remove('visible');

    if(!$('addFirstName').value.trim()) return showAddError('First name is required.');
    if(!$('addLastName').value.trim()) return showAddError('Last name is required.');
    if(!$('addEmail').value.trim()) return showAddError('Email address is required.');
    if(!$('addIdDocument').files[0]) return showAddError('A valid ID upload is required.');
    if(!$('addBedSelect').value) return showAddError('Select a bedspace.');
    if(!$('addStartDate').value || !$('addEndDate').value) return showAddError('Set both the lease start and end dates.');

    const form = new FormData();
    form.append('first_name', $('addFirstName').value.trim());
    form.append('last_name', $('addLastName').value.trim());
    form.append('date_of_birth', $('addDob').value);
    form.append('tenant_type', $('addTenantType').value);
    form.append('home_address', $('addHomeAddress').value.trim());
    form.append('contact_number', $('addContactNumber').value.trim());
    form.append('email', $('addEmail').value.trim());
    form.append('emergency_contact_name', $('addEmergencyName').value.trim());
    form.append('emergency_contact_number', $('addEmergencyNumber').value.trim());
    form.append('bed_id', $('addBedSelect').value);
    form.append('start_date', $('addStartDate').value);
    form.append('end_date', $('addEndDate').value);
    form.append('id_document', $('addIdDocument').files[0]);
    if($('addIdPhoto').files[0]) form.append('id_photo', $('addIdPhoto').files[0]);
    if($('addSignedContract').files[0]) form.append('signed_contract', $('addSignedContract').files[0]);

    this.disabled = true;
    try {
      const result = await api('/tenant-manager', { method:'POST', body: form });
      tenants.push(result.tenant);
      renderTable();
      $('addModal').classList.remove('open');
      toast(result.message);
    } catch(e){ showAddError(e.message); }
    this.disabled = false;
  });

  function showAddError(msg){
    const el = $('addModalError');
    el.textContent = msg;
    el.classList.add('visible');
  }

  // ===== Edit Tenant modal =====
  async function openEditModal(id){
    try {
      const t = await api(`/tenant-manager/${id}`);
      $('editModalError').classList.remove('visible');
      $('editTenantId').value = t.id;
      $('editFirstName').value = t.first_name ?? '';
      $('editLastName').value = t.last_name ?? '';
      $('editDob').value = t.date_of_birth ?? '';
      $('editTenantType').value = t.tenant_type ?? '';
      $('editHomeAddress').value = t.home_address ?? '';
      $('editContactNumber').value = t.contact_number ?? '';
      $('editEmail').value = t.email ?? '';
      $('editEmergencyName').value = t.emergency_contact_name ?? '';
      $('editEmergencyNumber').value = t.emergency_contact_number ?? '';
      $('editEmergencyEmail').value = t.emergency_contact_email ?? '';
      $('editEmergencyReminders').checked = !!t.emergency_billing_reminders;
      $('editIdDocument').value = '';
      $('editIdPhoto').value = '';
      $('editRemovePhoto').checked = false;
      $('editRemovePhotoRow').style.display = t.id_photo_url ? '' : 'none';
      $('editSignedContract').value = '';
      $('editModal').classList.add('open');
    } catch(e){ toast(e.message, true); }
  }
  $('cancelEditBtn').addEventListener('click', () => $('editModal').classList.remove('open'));

  $('submitEditBtn').addEventListener('click', async function(){
    const errEl = $('editModalError');
    errEl.classList.remove('visible');
    if(!$('editFirstName').value.trim()) return showEditError('First name is required.');
    if(!$('editLastName').value.trim()) return showEditError('Last name is required.');
    if(!$('editEmail').value.trim()) return showEditError('Email address is required.');

    const form = new FormData();
    form.append('first_name', $('editFirstName').value.trim());
    form.append('last_name', $('editLastName').value.trim());
    form.append('date_of_birth', $('editDob').value);
    form.append('tenant_type', $('editTenantType').value);
    form.append('home_address', $('editHomeAddress').value.trim());
    form.append('contact_number', $('editContactNumber').value.trim());
    form.append('email', $('editEmail').value.trim());
    form.append('emergency_contact_name', $('editEmergencyName').value.trim());
    form.append('emergency_contact_number', $('editEmergencyNumber').value.trim());
    form.append('emergency_contact_email', $('editEmergencyEmail').value.trim());
    form.append('emergency_billing_reminders', $('editEmergencyReminders').checked ? '1' : '0');
    if($('editIdDocument').files[0]) form.append('id_document', $('editIdDocument').files[0]);
    if($('editIdPhoto').files[0]) form.append('id_photo', $('editIdPhoto').files[0]);
    else if($('editRemovePhoto').checked) form.append('remove_id_photo', '1');
    if($('editSignedContract').files[0]) form.append('signed_contract', $('editSignedContract').files[0]);

    this.disabled = true;
    try {
      const id = $('editTenantId').value;
      const result = await api(`/tenant-manager/${id}`, { method:'POST', body: form });
      const idx = tenants.findIndex(t => t.id === Number(id));
      if(idx !== -1) tenants[idx] = Object.assign({}, tenants[idx], result.tenant);
      renderTable();
      $('editModal').classList.remove('open');
      toast(result.message);
      if(profileTenant && profileTenant.id === Number(id) && $('profileModal').classList.contains('open')) reloadProfile();
    } catch(e){ showEditError(e.message); }
    this.disabled = false;
  });

  function showEditError(msg){
    const el = $('editModalError');
    el.textContent = msg;
    el.classList.add('visible');
  }

  // ===== Set Status modal =====
  function openStatusModal(id){
    const t = tenants.find(x => x.id === id);
    $('statusModalError').classList.remove('visible');
    $('statusTenantId').value = id;
    $('statusSelect').value = t && t.status === 'inactive' ? 'active' : 'inactive';
    $('statusReasonFld').style.display = $('statusSelect').value === 'inactive' ? 'block' : 'none';
    $('statusReason').value = '';
    $('statusModal').classList.add('open');
  }
  $('statusSelect').addEventListener('change', function(){
    $('statusReasonFld').style.display = this.value === 'inactive' ? 'block' : 'none';
  });
  $('cancelStatusBtn').addEventListener('click', () => $('statusModal').classList.remove('open'));

  $('submitStatusBtn').addEventListener('click', async function(){
    const errEl = $('statusModalError');
    errEl.classList.remove('visible');
    const status = $('statusSelect').value;
    const reason = $('statusReason').value.trim();
    if(status === 'inactive' && !reason){
      errEl.textContent = 'A reason is required to deactivate this account.';
      errEl.classList.add('visible');
      return;
    }

    this.disabled = true;
    try {
      const id = $('statusTenantId').value;
      const result = await api(`/tenant-manager/${id}/status`, {
        method:'POST', headers:{ 'Content-Type':'application/json' }, body: JSON.stringify({ status, reason }),
      });
      const idx = tenants.findIndex(t => t.id === Number(id));
      if(idx !== -1) tenants[idx] = Object.assign({}, tenants[idx], result.tenant);
      renderTable();
      $('statusModal').classList.remove('open');
      toast(result.message);
      if(profileTenant && profileTenant.id === Number(id) && $('profileModal').classList.contains('open')) reloadProfile();
    } catch(e){
      errEl.textContent = e.message;
      errEl.classList.add('visible');
    }
    this.disabled = false;
  });

  renderTable();

  // /tenant-manager?view=ID opens that tenant's profile (linked from Tickets);
  // &tab=lease|billing|activity opens a specific tab.
  const params = new URLSearchParams(location.search);
  const viewId = Number(params.get('view'));
  if(viewId) openProfile(viewId, params.get('tab') || undefined);
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

  // Sidebar links, the hamburger, and topbar icons are styled divs rather
  // than native <button>/<a> elements, so pressing Enter or Space while one
  // is focused wouldn't normally do anything. This makes them behave like
  // real interactive controls for keyboard users.
  // Only the sidebar/topbar's div "buttons" -- not #profileBody, whose
  // tabindex is for scrolling; catching its keys broke typing spaces.
  document.querySelectorAll('.sidebar [tabindex="0"], .hamburger, .topbar-icon, .back-arrow').forEach(el => {
    el.addEventListener('keydown', (e) => {
      if (e.target !== el) return;
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        el.click();
      }
    });
  });

  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    // Close the top-most window first: a dialog opened from the profile,
    // then the profile itself.
    for (const id of ['depositRefundModal', 'editModal', 'statusModal', 'addModal']) {
      const modal = document.getElementById(id);
      if (modal && modal.classList.contains('open')) { modal.classList.remove('open'); return; }
    }
    const profile = document.getElementById('profileModal');
    if (profile && profile.classList.contains('open')) document.getElementById('profileClose').click();
  });
})();
</script>

@include('partials.photo-lightbox')
</body>
</html>