<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Vacancy Monitoring · {{ $brandDormName }}</title>
<link rel="icon" href="{{ $brandFaviconUrl }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/typography.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<style>
  /* Shared color variables, page reset, sidebar, topbar/search, and
     content header now live in public/css/admin.css (linked above). */
  .page-head-tools{ margin-left:auto; display:flex; align-items:center; gap:10px; }
  .head-tool-btn{ width:32px; height:32px; border-radius:7px; background:var(--card-bg); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; color:var(--text-mid); cursor:pointer; }
  .head-tool-btn svg{ width:14px; height:14px; }

  .stats-row{ display:grid; grid-template-columns:repeat(5, 1fr); gap:16px; margin-bottom:20px; }
  .stat-card{ background:var(--card-bg); border-radius:12px; border:1px solid var(--border); padding:18px 20px; display:flex; align-items:center; gap:14px; box-shadow:0 1px 2px rgba(20,30,20,0.03); }
  .stat-icon{ width:46px; height:46px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .stat-icon svg{ width:20px; height:20px; }
  .stat-icon.bedspaces{ background:var(--status-vacant-bg); color:var(--green-accent); }
  .stat-icon.occupied{ background:var(--status-occupied-bg); color:var(--status-occupied); }
  .stat-icon.vacant{ background:var(--status-vacant-bg); color:#4a9a58; }
  .stat-icon.reserved{ background:var(--status-reserved-bg); color:var(--status-reserved); }
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
  .legend-dot.reserved{ background:var(--status-reserved); }
  .legend-dot.maintenance{ background:var(--status-maintenance); }

  /* Bed colour key (client request 2026-11-14): big, at the top of the page,
     and pinned under the top bar while scrolling, so staff never have to
     hunt for what a colour means. Swatches are real mini bed tiles. */
  .bed-key{ position:sticky; top:var(--key-top, 56px); z-index:15; background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:12px 16px; margin-bottom:20px; display:flex; align-items:center; gap:14px; flex-wrap:wrap; box-shadow:0 4px 12px rgba(20,30,20,0.06); }
  .bed-key h2{ font-size:13px; font-weight:700; margin:0; color:var(--text-dark); white-space:nowrap; }
  .bed-key ul{ list-style:none; margin:0; padding:0; display:grid; grid-template-columns:repeat(4, minmax(0,1fr)); gap:10px; flex:1; min-width:0; }
  .key-item{ display:flex; align-items:center; gap:10px; border-radius:9px; padding:8px 12px; border:1px solid transparent; min-width:0; }
  .key-item svg{ width:22px; height:22px; flex-shrink:0; }
  .key-item .kname{ font-size:14px; font-weight:700; display:flex; align-items:baseline; gap:8px; }
  .key-item .kcount{ font-size:13px; font-weight:700; font-variant-numeric:tabular-nums; opacity:.85; }
  .key-item .kmean{ font-size:11.5px; line-height:1.35; opacity:.9; }
  .key-item.occupied{ background:var(--status-occupied-bg); color:#9b2f29; border-color:#efc4c0; }
  .key-item.vacant{ background:var(--status-vacant-bg); color:#256b33; border-color:#8fd09a; }
  .key-item.reserved{ background:var(--status-reserved-bg); color:#553a9c; border-color:#d6caf0; }
  .key-item.maintenance{ background:var(--status-maintenance-bg); color:#7a5712; border-color:#ead6a8; }
  @media (max-width: 1100px){ .bed-key ul{ grid-template-columns:repeat(2, minmax(0,1fr)); } }
  @media (max-width: 560px){
    .bed-key{ padding:10px; gap:8px; }
    .bed-key h2{ width:100%; }
    .key-item{ padding:7px 9px; gap:7px; }
    .key-item .kmean{ display:none; }
    .key-item svg{ width:18px; height:18px; }
  }

  .floor-block{ margin-bottom:10px; }
  .floor-header{ display:flex; align-items:center; justify-content:space-between; border:1px solid var(--border); border-radius:8px; padding:13px 16px; font-size:13.5px; font-weight:600; color:var(--text-dark); cursor:pointer; background:#fff; }
  .floor-header:hover{ background:#f7f9f7; }
  .floor-header .chev{ width:14px; height:14px; color:var(--text-light); transition:transform 0.2s ease; flex-shrink:0; }
  .floor-block.open .floor-header .chev{ transform:rotate(90deg); }
  .floor-block.open .floor-header{ border-bottom-left-radius:0; border-bottom-right-radius:0; border-bottom:none; }

  .floor-rooms{ display:none; grid-template-columns:repeat(auto-fill, minmax(260px, 1fr)); gap:14px; border:1px solid var(--border); border-top:none; border-bottom-left-radius:8px; border-bottom-right-radius:8px; padding:18px 16px 20px 16px; }
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
  .modal-hint{ font-size:11.5px; color:var(--text-light); margin:-6px 0 16px; line-height:1.5; }
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

  @media (max-width: 1280px){ .stats-row{ grid-template-columns:repeat(3, 1fr); } }
  @media (max-width: 900px){ .stats-row{ grid-template-columns:repeat(2, 1fr); } }
  /* Nov 2026 client revision: vacancy overview + bed tiles.
     Vacant is the state the admin is hunting for, so it gets the strongest
     treatment; occupied/reserved/maintenance stay readable but quieter. */
  .overview-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:20px 24px 14px; margin-bottom:20px; }
  .overview-top{ display:flex; align-items:baseline; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:12px; }
  .overview-top h2{ font-size:15px; font-weight:700; margin:0; }
  .overview-headline{ font-size:13px; color:var(--text-mid); font-variant-numeric:tabular-nums; }
  .overview-headline strong{ font-size:22px; color:#2f7a3d; margin-right:3px; }
  .occ-bar{ display:flex; gap:2px; height:12px; border-radius:6px; overflow:hidden; background:#eef1ee; }
  .occ-bar span{ display:block; height:100%; min-width:3px; }
  .occ-bar .occupied{ background:var(--status-occupied); }
  .occ-bar .reserved{ background:var(--status-reserved); }
  .occ-bar .vacant{ background:var(--status-vacant); }
  .occ-bar .maintenance{ background:var(--status-maintenance); }
  .floor-summary{ list-style:none; margin:14px 0 0; padding:0; }
  .floor-sum-item{ display:grid; grid-template-columns:72px minmax(120px, 1fr) 130px 150px; align-items:center; gap:16px; width:100%; padding:10px 8px; border:0; border-top:1px solid var(--border); background:none; text-align:left; font:inherit; font-size:12.5px; color:var(--text-mid); cursor:pointer; border-radius:0; font-variant-numeric:tabular-nums; }
  .floor-sum-item:hover{ background:#f7f9f7; }
  .floor-sum-item:focus-visible{ outline:2px solid var(--green-accent); outline-offset:-2px; }
  .floor-sum-item .fs-name{ font-weight:700; color:var(--text-dark); font-size:13px; }
  .floor-sum-item .occ-bar{ height:8px; }
  .floor-sum-item .fs-vac{ font-weight:600; color:#2f7a3d; }
  .floor-sum-item .fs-vac.none{ color:#b23b34; }
  .floor-sum-item .fs-rooms{ color:var(--text-light); text-align:right; }
  @media (max-width: 760px){
    .floor-sum-item{ grid-template-columns:1fr auto; row-gap:6px; }
    .floor-sum-item .occ-bar{ grid-column:1 / -1; grid-row:2; }
    .floor-sum-item .fs-rooms{ grid-column:1 / -1; text-align:left; }
  }

  .bed-avail-head{ display:flex; justify-content:space-between; align-items:baseline; border-top:1px solid var(--border); padding-top:11px; margin:2px 0 9px; font-variant-numeric:tabular-nums; }
  .bed-avail-head .ba-title{ font-size:12px; font-weight:700; color:var(--text-dark); }
  .bed-avail-head .ratio{ font-size:12px; color:var(--text-light); }
  .bed-avail-head .ratio b{ font-size:16px; color:#2f7a3d; }
  .bed-avail-head .ratio.none b{ color:#b23b34; }
  .bed-tiles{ display:grid; grid-template-columns:repeat(auto-fill, minmax(70px, 1fr)); gap:6px; }
  .bed-tile{ display:flex; flex-direction:column; align-items:center; gap:2px; border-radius:7px; padding:8px 4px 7px; cursor:pointer; border:1px solid transparent; line-height:1.2; transition:transform 0.12s cubic-bezier(0.16,1,0.3,1), box-shadow 0.12s ease; }
  .bed-tile:hover{ transform:translateY(-1px); box-shadow:0 2px 6px rgba(20,30,20,0.10); }
  .bed-tile:focus-visible{ outline:2px solid var(--text-dark); outline-offset:2px; }
  .bed-tile svg{ width:15px; height:15px; margin-bottom:2px; }
  .bed-tile .bt-name{ font-size:11.5px; font-weight:600; white-space:nowrap; }
  .bed-tile .bt-status{ font-size:10.5px; text-transform:capitalize; white-space:nowrap; }
  .bed-tile.occupied{ background:var(--status-occupied-bg); color:#9b2f29; border-color:#efc4c0; }
  .bed-tile.vacant{ background:var(--status-vacant-bg); color:#256b33; border-color:#8fd09a; }
  .bed-tile.reserved{ background:var(--status-reserved-bg); color:#553a9c; border-color:#d6caf0; }
  .bed-tile.maintenance{ background:var(--status-maintenance-bg); color:#7a5712; border-color:#ead6a8; }
  .vacant-note{ font-size:12px; font-weight:600; color:#2f7a3d; margin-top:10px; }
  .vacant-note.none{ color:#b23b34; }
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
        <h1>Vacancy Monitoring</h1>
        <div class="page-head-tools">
          <div class="head-tool-btn"><svg viewBox="0 0 24 24" fill="currentColor"><circle cx="5" cy="5" r="2"/><circle cx="12" cy="5" r="2"/><circle cx="19" cy="5" r="2"/><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/><circle cx="5" cy="19" r="2"/><circle cx="12" cy="19" r="2"/><circle cx="19" cy="19" r="2"/></svg></div>
          <div class="head-tool-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg></div>
          <div class="head-tool-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg></div>
        </div>
      </div>

      <section class="bed-key" id="bedKey" aria-labelledby="bedKeyTitle">
        <h2 id="bedKeyTitle">Bed colours</h2>
        <ul>
          <li class="key-item vacant"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 18v-6a2 2 0 012-2h16a2 2 0 012 2v6"/><path d="M2 18h20M4 10V6a2 2 0 012-2h4a2 2 0 012 2v4"/></svg><div><div class="kname">Vacant <span class="kcount" data-key-count="statVacant">{{ $stats['vacant'] }}</span></div><div class="kmean">Open for new tenants</div></div></li>
          <li class="key-item occupied"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 18v-6a2 2 0 012-2h16a2 2 0 012 2v6"/><path d="M2 18h20M4 10V6a2 2 0 012-2h4a2 2 0 012 2v4"/></svg><div><div class="kname">Occupied <span class="kcount" data-key-count="statOccupied">{{ $stats['occupied'] }}</span></div><div class="kmean">A tenant lives here</div></div></li>
          <li class="key-item reserved"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 18v-6a2 2 0 012-2h16a2 2 0 012 2v6"/><path d="M2 18h20M4 10V6a2 2 0 012-2h4a2 2 0 012 2v4"/></svg><div><div class="kname">Reserved <span class="kcount" data-key-count="statReserved">{{ $stats['reserved'] }}</span></div><div class="kmean">Held for an incoming tenant</div></div></li>
          <li class="key-item maintenance"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 18v-6a2 2 0 012-2h16a2 2 0 012 2v6"/><path d="M2 18h20M4 10V6a2 2 0 012-2h4a2 2 0 012 2v4"/></svg><div><div class="kname">Maintenance <span class="kcount" data-key-count="statMaintenance">{{ $stats['maintenance'] }}</span></div><div class="kmean">Not available right now</div></div></li>
        </ul>
      </section>

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
          <div class="stat-icon reserved"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div>
          <div><div class="stat-label">Reserved</div><div class="stat-value" id="statReserved">{{ $stats['reserved'] }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon maintenance"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a4 4 0 01-5.4 5.4L4 17v3h3l5.3-5.3a4 4 0 015.4-5.4l-2.5 2.5-2-2z"/></svg></div>
          <div><div class="stat-label">Maintenance</div><div class="stat-value" id="statMaintenance">{{ $stats['maintenance'] }}</div></div>
        </div>
      </div>

      <div class="overview-card" aria-labelledby="overviewTitle">
        <div class="overview-top">
          <h2 id="overviewTitle">Vacancy Overview</h2>
          <div class="overview-headline" id="overviewHeadline"></div>
        </div>
        <div class="occ-bar" id="overviewBar" role="img"></div>
        <ul class="floor-summary" id="floorSummary" aria-label="Vacancy by floor"></ul>
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
          <div><h2>Room / Bed Map</h2><p>Click a bed to change its status. Colours follow the key at the top.</p></div>
        </div>
        <div id="floors"></div>
      </div>
    </div>
  </div>
</div>

<!-- ADD FLOOR MODAL (client-side grouping label only — becomes real once a room is saved under it) -->
<div class="modal-overlay" id="addFloorModal">
  <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="addFloorModalTitle">
    <h3 class="modal-title" id="addFloorModalTitle">Add Floor <x-info-tip text="The floor is saved once its first room is added." /></h3>
    <div class="modal-field"><label for="newFloorName">Floor Label</label><input type="text" id="newFloorName" placeholder="e.g. 5"></div>
    <p class="modal-error" id="floorModalError" role="alert"></p>
    <div class="modal-actions">
      <button type="button" class="modal-btn cancel" id="cancelAddFloor">Cancel</button>
      <button type="button" class="modal-btn confirm" id="confirmAddFloor">Add Floor</button>
    </div>
  </div>
</div>

<!-- ADD / EDIT ROOM MODAL -->
<div class="modal-overlay" id="addRoomModal">
  <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="roomModalTitle">
    <h3 class="modal-title" id="roomModalTitle">Add Room</h3>
    <p class="modal-sub" id="addRoomFloorLabel">Adding a room to Floor 1</p>

    <div class="modal-row">
      <div class="modal-field"><label for="newRoomNo">Room No.</label><input type="text" id="newRoomNo" placeholder="e.g. A105"></div>
      <div class="modal-field"><label for="newRoomFloor">Floor</label><input type="text" id="newRoomFloor" placeholder="e.g. 1"></div>
    </div>
    <div class="modal-row">
      <div class="modal-field">
        <label for="newRoomTypeSelect">Room Type</label>
        <select id="newRoomTypeSelect"></select>
      </div>
      <div class="modal-field" id="newRoomRateField"><label for="newRoomRate">Monthly Rate (whole room)</label><input type="number" id="newRoomRate" min="0" step="0.01" placeholder="0.00"></div>
    </div>
    <p class="modal-hint" id="roomTypePriceHint"></p>
    <div class="modal-field" id="customRoomTypeField" style="display:none;"><label for="newRoomType">Custom type name</label><input type="text" id="newRoomType" placeholder="e.g. Standard"></div>
    <div class="modal-row">
      <div class="modal-field"><label for="newRoomUtility">Monthly Utilities <x-info-tip text="Whole-room amounts, split evenly across beds. Applies from each tenant's next bill." /></label><input type="number" id="newRoomUtility" min="0" step="0.01" placeholder="0.00"></div>
      <div class="modal-field"><label for="newRoomWifi">Monthly WiFi</label><input type="number" id="newRoomWifi" min="0" step="0.01" placeholder="0.00"></div>
    </div>
    <div class="modal-row">
      <div class="modal-field"><label for="newRoomBedCount">Number of Beds</label><input type="number" id="newRoomBedCount" min="1" max="20" value="2"></div>
      <div class="modal-field"><label for="newRoomArea">Room area (m²)</label><input type="number" id="newRoomArea" min="0" step="0.01" inputmode="decimal" placeholder="e.g. 18"></div>
    </div>
    <div class="modal-field"><label id="bedStatusLabel">Bed Status</label><div class="bed-status-rows" id="bedStatusRows" role="group" aria-labelledby="bedStatusLabel"></div></div>

    <p class="modal-error" id="roomModalError" role="alert"></p>
    <div class="modal-actions">
      <button type="button" class="modal-btn cancel" id="cancelAddRoom">Cancel</button>
      <button type="button" class="modal-btn confirm" id="confirmAddRoom">Add Room</button>
    </div>
  </div>
</div>

<script id="floor-groups-data" type="application/json">{!! json_encode($floorGroups) !!}</script>
<script id="room-types-data" type="application/json">{!! json_encode($roomTypes) !!}</script>
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
    let total = 0, occupied = 0, vacant = 0, reserved = 0, maintenance = 0;
    floorGroups.forEach(group => {
      group.rooms.forEach(room => {
        room.beds.forEach(bed => {
          total++;
          if(bed.status === 'occupied') occupied++;
          else if(bed.status === 'vacant') vacant++;
          else if(bed.status === 'reserved') reserved++;
          else if(bed.status === 'maintenance') maintenance++;
        });
      });
    });
    document.getElementById('statTotal').textContent = total;
    document.getElementById('statOccupied').textContent = occupied;
    document.getElementById('statVacant').textContent = vacant;
    document.getElementById('statReserved').textContent = reserved;
    document.getElementById('statMaintenance').textContent = maintenance;
    renderOverview({ total, occupied, reserved, vacant, maintenance });
  }

  function countBeds(beds){
    const c = { total: beds.length, occupied: 0, reserved: 0, vacant: 0, maintenance: 0 };
    beds.forEach(b => { if(c[b.status] !== undefined) c[b.status]++; });
    return c;
  }

  function barHtml(c){
    if(!c.total) return '';
    return ['occupied', 'reserved', 'vacant', 'maintenance']
      .filter(k => c[k] > 0)
      .map(k => `<span class="${k}" style="width:${(c[k] / c.total * 100).toFixed(2)}%" title="${c[k]} ${k}"></span>`)
      .join('');
  }

  // Overall bar + one tile per floor, so the admin sees at a glance where
  // the vacant beds are. Clicking a floor tile opens that floor below.
  function renderOverview(c){
    const headline = document.getElementById('overviewHeadline');
    headline.innerHTML = c.total
      ? `<strong>${c.vacant}</strong> of ${c.total} beds vacant (${Math.round(c.vacant / c.total * 100)}%)`
      : 'No beds added yet';
    const bar = document.getElementById('overviewBar');
    bar.innerHTML = barHtml(c);
    bar.style.display = c.total ? '' : 'none';
    bar.setAttribute('aria-label', `${c.occupied} occupied, ${c.reserved} reserved, ${c.vacant} vacant, ${c.maintenance} under maintenance`);

    const wrap = document.getElementById('floorSummary');
    wrap.innerHTML = '';
    floorGroups.forEach(group => {
      const beds = group.rooms.flatMap(r => r.beds);
      const fc = countBeds(beds);
      const roomsWithSpace = group.rooms.filter(r => r.beds.some(b => b.status === 'vacant')).length;
      const li = document.createElement('li');
      const item = document.createElement('button');
      item.type = 'button';
      item.className = 'floor-sum-item';
      item.title = `Show Floor ${group.label} rooms`;
      item.innerHTML = `
        <span class="fs-name">Floor ${group.label}</span>
        <span class="occ-bar" role="img" aria-label="${fc.occupied} occupied, ${fc.reserved} reserved, ${fc.vacant} vacant, ${fc.maintenance} maintenance">${barHtml(fc)}</span>
        <span class="fs-vac ${fc.vacant === 0 ? 'none' : ''}">${fc.vacant === 0 ? 'Full' : `${fc.vacant} of ${fc.total} beds vacant`}</span>
        <span class="fs-rooms">${roomsWithSpace} of ${group.rooms.length} room${group.rooms.length === 1 ? '' : 's'} with space</span>
      `;
      li.appendChild(item);
      item.addEventListener('click', () => {
        openFloorLabel = normalizeFloorLabel(group.label);
        renderFloors();
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
      wrap.appendChild(li);
    });
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
      header.tabIndex = 0;
      header.setAttribute('role', 'button');
      header.setAttribute('aria-expanded', block.classList.contains('open') ? 'true' : 'false');
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
      header.addEventListener('keydown', (e) => {
        if(e.target !== header) return;
        if(e.key === 'Enter' || e.key === ' '){ e.preventDefault(); header.click(); }
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

          const vacantCount = room.beds.filter(b => b.status === 'vacant').length;
          let bedsHtml = '';
          room.beds.forEach(bed => {
            bedsHtml += `
              <div class="bed-tile ${bed.status}" data-bed-id="${bed.id}" tabindex="0" role="button" aria-label="${bed.bed_label}, ${bed.status}. Activate to cycle status." title="Click to change status">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 18v-6a2 2 0 012-2h16a2 2 0 012 2v6"/><path d="M2 18h20M4 10V6a2 2 0 012-2h4a2 2 0 012 2v4"/></svg>
                <span class="bt-name">${bed.bed_label}</span>
                <span class="bt-status">${bed.status}</span>
              </div>
            `;
          });
          const vacantNote = vacantCount === 0
            ? '<div class="vacant-note none">No vacant beds</div>'
            : `<div class="vacant-note">${vacantCount} vacant bed${vacantCount === 1 ? '' : 's'} remaining</div>`;

          card.innerHTML = `
            <div class="room-card-head">
              <div>
                <div class="room-title">Room ${room.room_no}</div>
                <div class="room-meta">${room.room_type ? room.room_type + ' · ' : ''}₱${Number(room.price_per_bed ?? room.monthly_rate).toLocaleString(undefined, {minimumFractionDigits:2})} /bed${room.pricing_mode === 'per_room' ? ' (room split)' : ''}${room.area_sqm ? ' · ' + Number(room.area_sqm) + ' m²' : ''}</div>
                <div class="room-meta">Utilities ₱${Number(room.monthly_utility_cost || 0).toLocaleString(undefined, {minimumFractionDigits:2})} · WiFi ₱${Number(room.monthly_wifi_cost || 0).toLocaleString(undefined, {minimumFractionDigits:2})}</div>
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
            <div class="bed-avail-head"><span class="ba-title">Bed Availability</span><span class="ratio ${vacantCount === 0 ? 'none' : ''}" aria-label="${vacantCount} of ${room.beds.length} beds vacant"><b>${vacantCount}</b> / ${room.beds.length} vacant</span></div>
            <div class="bed-tiles">${bedsHtml}</div>
            ${vacantNote}
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

    container.querySelectorAll('.bed-tile').forEach(row => {
      row.addEventListener('keydown', (e) => {
        if(e.key === 'Enter' || e.key === ' '){ e.preventDefault(); row.click(); }
      });
      row.addEventListener('click', async () => {
        const bedId = parseInt(row.dataset.bedId);
        const bed = findBed(bedId);
        if(!bed) return;

        // A reserved bed is held for an applicant. One misclick shouldn't
        // cancel that, so cycling skips it; changing it on purpose is
        // still possible from the room's Edit screen.
        if(bed.status === 'reserved'){
          alert('This bed is reserved by a tenant who has already paid, so clicking won\'t change it. To change it on purpose, use Edit on this room.');
          return;
        }

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
  const newRoomTypeSelect = document.getElementById('newRoomTypeSelect');
  const newRoomRateField = document.getElementById('newRoomRateField');
  const customRoomTypeField = document.getElementById('customRoomTypeField');
  const roomTypePriceHint = document.getElementById('roomTypePriceHint');
  const ROOM_TYPES = JSON.parse(document.getElementById('room-types-data').textContent || '[]');

  // Room types are set up in Dormitory Profile > Room Types & Rates. Picking
  // one fills in the name and price; "Custom" keeps the old free-text name
  // and whole-room price for rooms that don't fit any type.
  function buildRoomTypeOptions(selectedId){
    const options = ROOM_TYPES.map(t => {
      const where = t.location ? ` (${t.location})` : '';
      return `<option value="${t.id}">${escapeHtml(t.name + where)} · ${escapeHtml(t.price_label)}</option>`;
    });
    options.unshift(ROOM_TYPES.length
      ? '<option value="">Custom (no room type)</option>'
      : '<option value="">Custom (add room types in Dormitory Profile)</option>');
    newRoomTypeSelect.innerHTML = options.join('');
    // null = a new room (default to the first type); '' = an existing room with no type.
    newRoomTypeSelect.value = selectedId === null ? (ROOM_TYPES[0] ? String(ROOM_TYPES[0].id) : '') : String(selectedId);
    syncRoomTypeFields();
  }

  function escapeHtml(v){
    return String(v ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  }

  function syncRoomTypeFields(){
    const type = ROOM_TYPES.find(t => String(t.id) === newRoomTypeSelect.value);
    newRoomRateField.style.display = type ? 'none' : '';
    customRoomTypeField.style.display = type ? 'none' : '';
    if(type){
      const beds = Math.max(1, parseInt(newRoomBedCountInput.value) || 1);
      const perBed = type.pricing_mode === 'per_room' ? type.monthly_rate / beds : type.monthly_rate;
      const cap = type.capacity_label ? ` Usually ${type.capacity_label}.` : '';
      roomTypePriceHint.textContent = `Each tenant pays ₱${perBed.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})} per month.${cap}`;
    } else {
      roomTypePriceHint.textContent = 'Custom rooms use the whole-room rate below, split evenly by the number of beds.';
    }
  }
  newRoomTypeSelect.addEventListener('change', syncRoomTypeFields);
  const newRoomRateInput = document.getElementById('newRoomRate');
  const newRoomUtilityInput = document.getElementById('newRoomUtility');
  const newRoomWifiInput = document.getElementById('newRoomWifi');
  const newRoomAreaInput = document.getElementById('newRoomArea');
  const newRoomBedCountInput = document.getElementById('newRoomBedCount');
  const bedStatusRows = document.getElementById('bedStatusRows');
  const confirmAddRoomBtn = document.getElementById('confirmAddRoom');

  function buildBedStatusRows(existingBeds){
    const count = Math.max(1, Math.min(20, parseInt(newRoomBedCountInput.value) || 1));
    bedStatusRows.innerHTML = '';
    for(let i = 1; i <= count; i++){
      const preset = (existingBeds && existingBeds[i-1]) ? existingBeds[i-1].status : 'vacant';
      const row = document.createElement('div');
      row.className = 'bed-status-row';
      row.innerHTML = `
        <span class="bed-index">Bed ${i}</span>
        <select data-bed-index="${i}">
          <option value="vacant" ${preset === 'vacant' ? 'selected' : ''}>Vacant</option>
          <option value="reserved" ${preset === 'reserved' ? 'selected' : ''}>Reserved</option>
          <option value="occupied" ${preset === 'occupied' ? 'selected' : ''}>Occupied</option>
          <option value="maintenance" ${preset === 'maintenance' ? 'selected' : ''}>Maintenance</option>
        </select>
      `;
      bedStatusRows.appendChild(row);
    }
  }

  newRoomBedCountInput.addEventListener('input', () => { buildBedStatusRows(); syncRoomTypeFields(); });

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
    buildRoomTypeOptions(null);
    newRoomUtilityInput.value = '';
    newRoomWifiInput.value = '';
    newRoomAreaInput.value = '';
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
    buildRoomTypeOptions(room.room_type_id ?? '');
    newRoomUtilityInput.value = Number(room.monthly_utility_cost) || '';
    newRoomWifiInput.value = Number(room.monthly_wifi_cost) || '';
    newRoomAreaInput.value = room.area_sqm ?? '';
    buildBedStatusRows(room.beds);
    addRoomModal.classList.add('open');
    newRoomNoInput.focus();
  }

  document.getElementById('cancelAddRoom').addEventListener('click', () => addRoomModal.classList.remove('open'));

  confirmAddRoomBtn.addEventListener('click', async () => {
    const room_no = newRoomNoInput.value.trim();
    const floor = String(newRoomFloorInput.value).trim();
    const room_type_id = newRoomTypeSelect.value ? Number(newRoomTypeSelect.value) : null;
    const room_type = newRoomTypeInput.value.trim();
    const monthly_rate = parseFloat(newRoomRateInput.value) || 0;
    const monthly_utility_cost = parseFloat(newRoomUtilityInput.value) || 0;
    const monthly_wifi_cost = parseFloat(newRoomWifiInput.value) || 0;
    const area_sqm = newRoomAreaInput.value === '' ? null : parseFloat(newRoomAreaInput.value);
    const bed_statuses = Array.from(bedStatusRows.querySelectorAll('select')).map(sel => sel.value);

    if(!room_no || !floor){
      roomModalError.textContent = 'Room No. and Floor are required.';
      roomModalError.style.display = 'block';
      return;
    }

    const payload = { room_no, floor, room_type_id, room_type, monthly_rate, monthly_utility_cost, monthly_wifi_cost, area_sqm, bed_count: bed_statuses.length, bed_statuses };

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
          room_type_id: created.room_type_id, room_type: created.room_type, monthly_rate: created.monthly_rate, price_per_bed: created.price_per_bed, pricing_mode: created.pricing_mode, area_sqm: created.area_sqm,
          monthly_utility_cost: created.monthly_utility_cost, monthly_wifi_cost: created.monthly_wifi_cost, status: created.status,
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
          room_type_id: updated.room_type_id, room_type: updated.room_type, monthly_rate: updated.monthly_rate, price_per_bed: updated.price_per_bed, pricing_mode: updated.pricing_mode, area_sqm: updated.area_sqm,
          monthly_utility_cost: updated.monthly_utility_cost, monthly_wifi_cost: updated.monthly_wifi_cost, status: updated.status,
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

<script>
(function(){
  // This page never had the generic [data-href] click handler that other
  // pages have -- it relied entirely on inline onclick attributes for nav
  // links, which got removed when the sidebar markup was updated. Restoring
  // it here so every sidebar link (and the back-arrow, if it ever gets one)
  // works again.
  document.querySelectorAll('[data-href]').forEach(el => {
    el.addEventListener('click', () => { window.location.href = el.dataset.href; });
  });

  // This page's Log Out button existed in the markup but was never wired to
  // anything -- every other admin page POSTs to /logout on click, so doing
  // the same here. Reuses the CSRF_TOKEN constant already declared earlier
  // in this file's main script.
  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', async () => {
      const logoutRes = await fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' } });
      window.location.href = (await logoutRes.json().catch(() => ({}))).redirect || '/';
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

  document.querySelectorAll('.sidebar [tabindex="0"], .hamburger, .topbar-icon, .back-arrow').forEach(el => {
    el.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        el.click();
      }
    });
  });

  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    document.querySelectorAll('.modal-overlay.open').forEach(m => m.classList.remove('open'));
  });
})();
</script>

<script>
(function(){
  // Pin the colour key just below the sticky top bar.
  const bar = document.querySelector('.main > .topbar, .topbar');
  const setTop = () => document.documentElement.style.setProperty('--key-top', ((bar ? bar.offsetHeight : 0) + 8) + 'px');
  setTop(); window.addEventListener('resize', setTop);
  // Mirror the live stat numbers (they update when a bed's status changes).
  document.querySelectorAll('[data-key-count]').forEach(el => {
    const src = document.getElementById(el.dataset.keyCount);
    if(!src) return;
    new MutationObserver(() => { el.textContent = src.textContent; }).observe(src, { childList:true, characterData:true, subtree:true });
  });
})();
</script>
</body>
</html>