<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH — VR Management</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css">
<script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
<style>
  :root{
    --green-dark:#3f6b4a; --green-mid:#4f7c57;
    --green-sidebar-top:#5b8a63; --green-sidebar-bottom:#2c4a35;
    --green-accent:#2f6f3c; --green-btn:#2f6b3a; --green-btn-hover:#255a2f;
    --status-occupied:#d9564f;
    --status-vacant:#7fc98a; --status-vacant-bg:#d9f2dd;
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
  .topbar-icon{ width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; color:#eaf0ea; cursor:pointer; }
  .topbar-icon svg{ width:16px; height:16px; }
  .avatar-icon{ background:rgba(255,255,255,0.9); color:var(--green-dark); }

  .content{ padding:28px 32px 48px 32px; flex:1; }
  .page-head{ display:flex; align-items:center; gap:12px; margin-bottom:22px; }
  .back-arrow{ width:34px; height:34px; border-radius:8px; background:var(--card-bg); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-mid); flex-shrink:0; }
  .page-head h1{ font-size:20px; font-weight:700; margin:0; color:var(--text-dark); }
  .page-head .room-pill{ background:var(--status-vacant-bg); color:var(--green-accent); font-size:11.5px; font-weight:700; padding:5px 13px; border-radius:20px; }

  .vr-btn{ font-size:12px; font-weight:600; padding:9px 16px; border-radius:7px; border:1px solid var(--border); background:#fff; color:var(--text-mid); cursor:pointer; text-align:center; font-family:var(--font-body); }
  .vr-btn:hover:not(:disabled){ background:#f7f9f7; }
  .vr-btn:disabled{ opacity:0.45; cursor:not-allowed; }
  .vr-btn.primary{ background:var(--green-btn); border-color:var(--green-btn); color:#fff; }
  .vr-btn.primary:hover:not(:disabled){ background:var(--green-btn-hover); }
  .vr-btn.warn{ background:#fbeceb; border-color:#f2cfcc; color:var(--status-occupied); }
  .vr-btn.sm{ padding:6px 11px; font-size:11px; }

  /* ===== LIST VIEW ===== */
  .vr-card-panel{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:22px 24px 26px 24px; }
  .vr-card-panel h2{ font-size:15px; font-weight:700; margin:0 0 18px 0; }
  .vr-grid{ display:grid; grid-template-columns:repeat(auto-fill, minmax(210px, 1fr)); gap:18px; }
  .vr-card{ border:1px solid var(--border); border-radius:10px; overflow:hidden; background:#fff; }
  .vr-thumb{ position:relative; height:130px; background:#dfe6e0 center/cover no-repeat; display:flex; align-items:flex-start; justify-content:space-between; padding:8px; }
  .vr-thumb.empty{ align-items:center; justify-content:center; color:var(--text-light); font-size:11px; }
  .vr-badge-360{ background:rgba(0,0,0,0.55); color:#fff; font-size:10px; font-weight:700; padding:3px 7px; border-radius:5px; }
  .vr-lock-icon{ margin-left:auto; width:26px; height:26px; border-radius:50%; background:rgba(0,0,0,0.55); display:flex; align-items:center; justify-content:center; }
  .vr-lock-icon svg{ width:13px; height:13px; color:#fff; }
  .vr-card-body{ padding:12px 14px 14px 14px; }
  .vr-card-title{ font-size:13px; font-weight:700; display:flex; align-items:center; gap:6px; }
  .vr-floor-chip{ font-size:10px; font-weight:600; color:var(--text-mid); background:#eef1ee; padding:2px 7px; border-radius:20px; }
  .vr-card-sub{ font-size:11px; color:var(--text-light); margin:5px 0 10px 0; }

  /* ===== EDIT VIEW ===== */
  #vrEditView{ display:none; }
  .vr-tabs-bar{ display:flex; align-items:center; gap:10px; margin-bottom:22px; }
  .vr-tabs-scroll{ display:flex; gap:8px; overflow-x:auto; flex:1; padding-bottom:2px; }
  .vr-tab{ flex-shrink:0; border:1px solid var(--border); background:#fff; border-radius:8px; padding:8px 16px; font-size:12.5px; font-weight:600; color:var(--text-mid); cursor:pointer; white-space:nowrap; }
  .vr-tab .tab-floor{ display:block; font-size:10px; font-weight:500; color:var(--text-light); }
  .vr-tab.active{ background:var(--status-vacant-bg); border-color:var(--status-vacant); color:var(--green-accent); }
  .vr-tab-scroll-btn{ flex-shrink:0; width:30px; height:30px; border-radius:8px; border:1px solid var(--border); background:#fff; color:var(--text-mid); display:flex; align-items:center; justify-content:center; cursor:pointer; }
  .vr-tab-scroll-btn svg{ width:14px; height:14px; }

  /* Step cards */
  .step-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; margin-bottom:18px; overflow:hidden; }
  .step-head{ display:flex; align-items:center; gap:12px; padding:16px 22px; border-bottom:1px solid var(--border); background:#fbfcfb; }
  .step-num{ width:26px; height:26px; border-radius:50%; background:var(--green-accent); color:#fff; font-size:12.5px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .step-title{ font-size:14px; font-weight:700; }
  .step-sub{ font-size:11.5px; color:var(--text-light); margin-top:2px; }
  .step-status{ margin-left:auto; font-size:11.5px; font-weight:600; color:var(--text-light); }
  .step-status.done{ color:var(--green-accent); }
  .step-body{ padding:20px 22px 22px 22px; }

  /* Step 1 — photos */
  .photo-grid{ display:grid; grid-template-columns:repeat(auto-fill, minmax(150px,1fr)); gap:14px; margin-bottom:18px; }
  .photo-tile{ border:2px solid var(--border); border-radius:10px; overflow:hidden; cursor:pointer; background:#fafbfa; transition:border-color .18s, transform .18s; }
  .photo-tile:hover{ transform:translateY(-2px); }
  .photo-tile.active{ border-color:var(--green-accent); }
  .photo-tile img{ width:100%; height:88px; object-fit:cover; display:block; background:#dfe6e0; }
  .photo-tile .pt-meta{ padding:9px 11px; }
  .photo-tile .pt-title{ font-size:12px; font-weight:700; word-break:break-word; }
  .photo-tile .pt-flags{ font-size:9.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--green-accent); margin-top:3px; }
  .photo-tile .pt-links{ font-size:10.5px; color:var(--text-light); margin-top:3px; }

  .add-tile{ border:2px dashed var(--status-vacant); border-radius:10px; background:var(--status-vacant-bg); display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px; min-height:139px; cursor:pointer; color:var(--green-accent); font-size:12px; font-weight:700; text-align:center; padding:12px; }
  .add-tile:hover{ background:#cfeed5; }
  .add-tile .plus{ font-size:24px; line-height:1; }

  .add-form{ display:none; border-top:1px solid var(--border); padding-top:18px; }
  .add-form.open{ display:block; }
  .add-form .af-row{ display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end; }
  .fld{ display:flex; flex-direction:column; gap:6px; }
  .fld label{ font-size:11px; font-weight:700; letter-spacing:.4px; color:var(--text-mid); text-transform:uppercase; }
  .fld input[type=text], .fld select{ border:1px solid var(--border); border-radius:8px; padding:10px 13px; font-size:13px; font-family:var(--font-body); min-width:210px; background:#fff; }
  .tip{ background:#eef5ef; border:1px solid #cfe0d1; border-radius:8px; padding:10px 14px; font-size:12px; color:#33513a; line-height:1.6; margin-bottom:16px; }

  /* Step 2 — two column: photo left, arrows right */
  .link-layout{ display:grid; grid-template-columns:1fr 320px; gap:20px; }
  .pano-col{ min-width:0; }
  #editorPanorama{ width:100%; height:400px; border-radius:10px; overflow:hidden; background:#222; }
  .pano-caption{ display:flex; align-items:center; gap:10px; margin-bottom:10px; }
  .pano-caption .pc-name{ font-size:13px; font-weight:700; }
  .pano-caption .pc-hint{ font-size:11.5px; color:var(--text-light); margin-left:auto; }

  .placing-banner{ display:none; background:#fff6e0; border:1px solid #f0dfa8; color:#6b5a1f; border-radius:8px; padding:11px 14px; font-size:12.5px; font-weight:600; margin-bottom:10px; align-items:center; gap:10px; }
  .placing-banner.on{ display:flex; }
  .placing-banner .pulse{ width:9px; height:9px; border-radius:50%; background:#d9a520; animation:pulse 1.1s ease-in-out infinite; flex-shrink:0; }
  @keyframes pulse{ 0%,100%{ transform:scale(1); opacity:1; } 50%{ transform:scale(1.5); opacity:.5; } }

  .arrow-panel{ border:1px solid var(--border); border-radius:10px; background:#fbfcfb; padding:16px; display:flex; flex-direction:column; }
  .arrow-panel h3{ font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-mid); margin:0 0 12px 0; }
  .arrow-list{ list-style:none; margin:0 0 14px 0; padding:0; display:flex; flex-direction:column; gap:8px; }
  .arrow-item{ background:#fff; border:1px solid var(--border); border-radius:8px; padding:10px 12px; font-size:12px; }
  .arrow-item .ai-top{ display:flex; align-items:center; gap:8px; }
  .arrow-item .ai-label{ font-weight:700; word-break:break-word; }
  .arrow-item .ai-remove{ margin-left:auto; background:none; border:none; color:var(--status-occupied); font-size:11px; font-weight:600; cursor:pointer; padding:2px 4px; flex-shrink:0; }
  .arrow-item .ai-dest{ color:var(--text-light); font-size:11px; margin-top:3px; }
  .arrow-empty{ font-size:12px; color:var(--text-light); font-style:italic; margin-bottom:14px; line-height:1.55; }

  .arrow-form{ display:none; border-top:1px solid var(--border); padding-top:14px; flex-direction:column; gap:12px; }
  .arrow-form.open{ display:flex; }
  .arrow-form .fld input, .arrow-form .fld select{ min-width:0; width:100%; }

  .adv-toggle{ background:none; border:none; color:var(--text-light); font-size:11.5px; cursor:pointer; padding:6px 0 0 0; text-align:left; text-decoration:underline; }
  .adv-box{ display:none; margin-top:12px; border-top:1px solid var(--border); padding-top:14px; }
  .adv-box.open{ display:block; }
  .adv-box .sweep-note{ font-size:11.5px; color:var(--text-light); margin:0 0 10px 0; line-height:1.55; }
  .adv-box .sweep-val{ font-size:11.5px; font-weight:700; color:var(--green-accent); }
  .adv-box input[type=range]{ width:100%; accent-color:var(--green-accent); margin-bottom:10px; }

  .scene-tools{ display:flex; gap:8px; flex-wrap:wrap; margin-top:14px; padding-top:14px; border-top:1px solid var(--border); }

  /* Step 3 — settings */
  .settings-row{ display:grid; grid-template-columns:1fr 1fr; gap:20px; }
  .settings-row .fld input, .settings-row .fld select{ width:100%; }
  .fld input[readonly]{ background:#f4f6f4; color:var(--text-mid); }
  .save-msg{ font-size:12px; color:var(--green-accent); font-weight:600; display:none; align-self:center; }

  .toast{ position:fixed; bottom:22px; right:22px; background:var(--green-accent); color:#fff; padding:12px 20px; border-radius:8px; font-size:13px; display:none; z-index:99; box-shadow:0 6px 18px rgba(0,0,0,0.2); }
  .toast.error{ background:var(--status-occupied); }
  .toast.visible{ display:block; }

  @media (max-width:1100px){ .link-layout{ grid-template-columns:1fr; } }
</style>
</head>
<body>
<div class="app">

  <aside class="sidebar">
    <div class="sidebar-logo"><span class="logo-mark"></span> NEST.PH</div>
    <div class="sidebar-section-label">Quick Access</div>
    <ul class="nav-list">
      <li class="nav-item" data-href="{{ route('dashboard') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></span>Dashboard</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></span>Tenant Manager</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></span>Billing and Payments</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg></span>Delinquency</li>
      <li class="nav-item" data-href="{{ route('admin.addfloor') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="4" width="7" height="7"/><rect x="3" y="15" width="7" height="7"/><rect x="14" y="15" width="7" height="7"/></svg></span>Vacancy Monitor</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h18v10H3z"/><path d="M3 12h18"/></svg></span>Tickets</li>
      <li class="nav-item active" data-href="{{ route('vr.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 3v18M16 3v18"/></svg></span>VR Management</li>
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

      <!-- ===== LIST VIEW ===== -->
      <div id="vrListView">
        <div class="page-head">
          <div class="back-arrow" data-href="{{ route('vacancy.index') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
          <h1>VR Management</h1>
        </div>

        <div class="vr-card-panel">
          <h2>VR Room Tours</h2>
          <div class="vr-grid" id="vrGrid"></div>
        </div>
      </div>

      <!-- ===== EDIT VIEW ===== -->
      <div id="vrEditView">
        <div class="page-head">
          <div class="back-arrow" id="backToListBtn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
          <h1>Build VR Tour</h1>
          <span class="room-pill" id="roomPill"></span>
        </div>

        <div class="vr-tabs-bar">
          <div class="vr-tabs-scroll" id="vrTabs"></div>
          <div class="vr-tab-scroll-btn" id="tabsRight"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg></div>
        </div>

        <!-- STEP 1 -->
        <div class="step-card">
          <div class="step-head">
            <div class="step-num">1</div>
            <div>
              <div class="step-title">Add photos of the room</div>
              <div class="step-sub">Each photo is one spot a visitor can stand in.</div>
            </div>
            <div class="step-status" id="step1Status"></div>
          </div>
          <div class="step-body">
            <div class="tip">
              <strong>Taking the photo:</strong> stand in the middle of the spot and use your phone's
              <strong>Panorama</strong> mode, turning slowly in one direction. A full spin is best.
            </div>

            <div class="photo-grid" id="photoGrid"></div>

            <div class="add-form" id="addForm">
              <div class="af-row">
                <div class="fld">
                  <label for="newSceneTitle">Name this spot</label>
                  <input type="text" id="newSceneTitle" placeholder="e.g. Entrance, Bedside, Study Corner">
                </div>
                <div class="fld">
                  <label for="newScenePanorama">Choose the photo</label>
                  <input type="file" id="newScenePanorama" accept="image/jpeg,image/png">
                </div>
                <button class="vr-btn primary" id="addSceneBtn" type="button">Upload Photo</button>
                <button class="vr-btn" id="cancelAddBtn" type="button">Cancel</button>
              </div>
            </div>
          </div>
        </div>

        <!-- STEP 2 -->
        <div class="step-card" id="linkStep">
          <div class="step-head">
            <div class="step-num">2</div>
            <div>
              <div class="step-title">Link the photos together</div>
              <div class="step-sub">Add arrows so visitors can walk from one spot to the next.</div>
            </div>
            <div class="step-status" id="step2Status"></div>
          </div>
          <div class="step-body">

            <div class="link-layout">
              <div class="pano-col">
                <div class="pano-caption">
                  <span class="pc-name" id="panoName"></span>
                  <span class="pc-hint">Drag to look around</span>
                </div>

                <div class="placing-banner" id="placingBanner">
                  <span class="pulse"></span>
                  <span>Click the spot in the photo where the arrow should go — usually a doorway.</span>
                  <button class="vr-btn sm" id="cancelPlaceBtn" type="button" style="margin-left:auto;">Cancel</button>
                </div>

                <div id="editorPanorama"></div>

                <button class="adv-toggle" id="advToggle">Photo looks stretched? Adjust coverage</button>
                <div class="adv-box" id="advBox">
                  <p class="sweep-note" id="sweepNote"></p>
                  <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                    <label style="font-size:11.5px;font-weight:600;color:var(--text-mid);">How far did you turn?</label>
                    <span class="sweep-val" id="sweepValue" style="margin-left:auto;"></span>
                  </div>
                  <input type="range" id="sweepSlider" min="30" max="360" step="5">
                  <button class="vr-btn primary sm" id="saveSweepBtn" type="button">Save Coverage</button>
                </div>
              </div>

              <div class="arrow-panel">
                <h3>Arrows from this spot</h3>
                <ul class="arrow-list" id="arrowList"></ul>
                <div class="arrow-empty" id="arrowEmpty"></div>

                <button class="vr-btn primary" id="addArrowBtn" type="button">+ Add Arrow</button>

                <div class="arrow-form" id="arrowForm">
                  <div class="fld">
                    <label for="targetScene">Where does it lead?</label>
                    <select id="targetScene"></select>
                  </div>
                  <div class="fld">
                    <label for="hotspotLabel">Label (optional)</label>
                    <input type="text" id="hotspotLabel" placeholder="Auto: “Go to [spot]”">
                  </div>
                  <div style="display:flex;gap:8px;">
                    <button class="vr-btn primary" id="startPlaceBtn" type="button" style="flex:1;">Next: pick the spot</button>
                    <button class="vr-btn" id="cancelArrowBtn" type="button">Cancel</button>
                  </div>
                </div>

                <div class="scene-tools">
                  <button class="vr-btn sm" id="setDefaultBtn" type="button">Set as start</button>
                  <button class="vr-btn warn sm" id="deleteSceneBtn" type="button">Delete photo</button>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- STEP 3 -->
        <div class="step-card">
          <div class="step-head">
            <div class="step-num">3</div>
            <div>
              <div class="step-title">Publish</div>
              <div class="step-sub">Control whether visitors can see this tour.</div>
            </div>
            <div class="step-status" id="step3Status"></div>
          </div>
          <div class="step-body">
            <div class="fld" style="margin-bottom:18px;">
              <label for="captionInput">Caption shown to visitors</label>
              <input type="text" id="captionInput" placeholder="e.g. Spacious room with study area" style="width:100%;">
            </div>

            <div class="settings-row">
              <div class="fld">
                <label for="visibilitySelect">Visibility</label>
                <select id="visibilitySelect">
                  <option value="public">Public — visitors can view</option>
                  <option value="locked">Locked — hidden from visitors</option>
                  <option value="draft">Draft — still working on it</option>
                </select>
              </div>
              <div class="fld">
                <label for="lastUpdatedInput">Last updated</label>
                <input type="text" id="lastUpdatedInput" readonly>
              </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:22px;align-items:center;">
              <button class="vr-btn primary" id="saveInfoBtn" type="button">Save</button>
              <button class="vr-btn" id="cancelEditBtn" type="button">Cancel</button>
              <span class="save-msg" id="saveMsg">Saved!</span>
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script type="application/json" id="vr-rooms-data">{!! json_encode($rooms) !!}</script>

<script>
(function(){
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  let rooms = JSON.parse(document.getElementById('vr-rooms-data').textContent);

  let activeRoomId = null;
  let activeSceneId = null;
  let viewer = null;
  let placingMode = false;

  const $ = id => document.getElementById(id);
  const listView = $('vrListView');
  const editView = $('vrEditView');

  document.querySelectorAll('[data-href]').forEach(el => {
    el.addEventListener('click', () => { window.location.href = el.dataset.href; });
  });

  $('logoutBtn').addEventListener('click', async () => {
    await fetch('/logout', { method:'POST', headers:{ 'X-CSRF-TOKEN': csrfToken } });
    window.location.href = '/';
  });

  function toast(msg, isError){
    const el = $('toast');
    el.textContent = msg;
    el.classList.toggle('error', !!isError);
    el.classList.add('visible');
    setTimeout(() => el.classList.remove('visible'), 2600);
  }

  function esc(str){
    const d = document.createElement('div');
    d.textContent = str ?? '';
    return d.innerHTML;
  }

  async function api(url, options = {}){
    const headers = Object.assign({ 'X-CSRF-TOKEN': csrfToken, 'Accept':'application/json' }, options.headers || {});
    const res = await fetch(url, Object.assign({}, options, { headers }));
    if(!res.ok){
      const body = await res.json().catch(() => ({}));
      throw new Error(body.message || (body.errors ? Object.values(body.errors)[0][0] : `Request failed (${res.status})`));
    }
    return res.status === 204 ? null : res.json();
  }

  const findRoom = id => rooms.find(r => r.id === id);
  const activeRoom = () => findRoom(activeRoomId);
  const activeScene = () => activeRoom()?.scenes.find(s => s.id === activeSceneId);
  const defaultSceneOf = room => room.scenes.find(s => s.is_default) || room.scenes[0] || null;

  // ===== LIST VIEW =====
  function renderGrid(){
    if(rooms.length === 0){
      $('vrGrid').innerHTML = '<p style="color:var(--text-light);font-size:12.5px;">No rooms yet. Add rooms from Vacancy Monitoring first.</p>';
      return;
    }

    $('vrGrid').innerHTML = rooms.map(room => {
      const cover = defaultSceneOf(room);
      const locked = room.vr_visibility === 'locked';
      const n = room.scenes.length;
      const arrows = room.scenes.reduce((sum, s) => sum + s.hotspots.length, 0);

      return `
        <div class="vr-card">
          <div class="vr-thumb ${cover ? '' : 'empty'}" ${cover ? `style="background-image:url('${cover.panorama_url}')"` : ''}>
            ${cover ? '<span class="vr-badge-360">360°</span>' : '<span>No photos yet</span>'}
            ${cover ? `<span class="vr-lock-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">${locked ? '<rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 118 0v3"/>' : '<rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 017.5-2"/>'}</svg></span>` : ''}
          </div>
          <div class="vr-card-body">
            <div class="vr-card-title">Room ${esc(room.room_no)} <span class="vr-floor-chip">Floor ${esc(room.floor ?? '—')}</span></div>
            <div class="vr-card-sub">${n} photo${n===1?'':'s'} · ${arrows} arrow${arrows===1?'':'s'} · ${esc(room.vr_visibility)}</div>
            <button class="vr-btn primary" data-edit="${room.id}" style="width:100%;">${n === 0 ? 'Start Tour' : 'Edit Tour'}</button>
          </div>
        </div>`;
    }).join('');

    $('vrGrid').querySelectorAll('[data-edit]').forEach(btn => {
      btn.addEventListener('click', () => openEditor(Number(btn.dataset.edit)));
    });
  }

  // ===== EDIT VIEW =====
  function openEditor(roomId){
    activeRoomId = roomId;
    const room = activeRoom();
    activeSceneId = room.scenes.length ? defaultSceneOf(room).id : null;

    listView.style.display = 'none';
    editView.style.display = 'block';

    $('roomPill').textContent = `Room ${room.room_no} · Floor ${room.floor ?? '—'}`;
    $('captionInput').value = room.vr_caption || '';
    $('visibilitySelect').value = room.vr_visibility || 'draft';
    $('lastUpdatedInput').value = room.updated_at || '';

    closeArrowForm();
    $('addForm').classList.remove('open');
    $('advBox').classList.remove('open');

    renderTabs();
    renderAll();
  }

  function renderTabs(){
    $('vrTabs').innerHTML = rooms.map(room => `
      <div class="vr-tab ${room.id === activeRoomId ? 'active' : ''}" data-tab="${room.id}">
        Room ${esc(room.room_no)}
        <span class="tab-floor">${room.scenes.length} photo${room.scenes.length===1?'':'s'}</span>
      </div>`).join('');

    $('vrTabs').querySelectorAll('[data-tab]').forEach(tab => {
      tab.addEventListener('click', () => openEditor(Number(tab.dataset.tab)));
    });
  }

  function renderAll(){
    renderPhotoGrid();
    renderStepStatus();
    renderLinkStep();
  }

  function renderStepStatus(){
    const room = activeRoom();
    const n = room.scenes.length;
    const arrows = room.scenes.reduce((sum, s) => sum + s.hotspots.length, 0);

    const s1 = $('step1Status');
    s1.textContent = n === 0 ? 'No photos yet' : `${n} photo${n===1?'':'s'} added`;
    s1.classList.toggle('done', n > 0);

    const s2 = $('step2Status');
    if(n < 2){
      s2.textContent = 'Add 2+ photos to link them';
      s2.classList.remove('done');
    } else {
      s2.textContent = arrows === 0 ? 'No arrows yet' : `${arrows} arrow${arrows===1?'':'s'} placed`;
      s2.classList.toggle('done', arrows > 0);
    }

    const s3 = $('step3Status');
    const vis = room.vr_visibility;
    s3.textContent = vis === 'public' ? 'Live to visitors' : (vis === 'locked' ? 'Hidden' : 'Draft');
    s3.classList.toggle('done', vis === 'public');
  }

  function renderPhotoGrid(){
    const room = activeRoom();

    const tiles = room.scenes.map(scene => {
      const arrows = scene.hotspots.length;
      return `
        <div class="photo-tile ${scene.id === activeSceneId ? 'active' : ''}" data-scene="${scene.id}">
          <img src="${scene.panorama_url}" alt="">
          <div class="pt-meta">
            <div class="pt-title">${esc(scene.title)}</div>
            ${scene.is_default ? '<div class="pt-flags">Starting spot</div>' : ''}
            <div class="pt-links">${arrows} arrow${arrows===1?'':'s'} out</div>
          </div>
        </div>`;
    }).join('');

    $('photoGrid').innerHTML = tiles + `
      <div class="add-tile" id="openAddBtn">
        <span class="plus">+</span>
        <span>Add a photo</span>
      </div>`;

    $('photoGrid').querySelectorAll('[data-scene]').forEach(tile => {
      tile.addEventListener('click', () => {
        activeSceneId = Number(tile.dataset.scene);
        closeArrowForm();
        renderAll();
      });
    });

    $('openAddBtn').addEventListener('click', () => {
      $('addForm').classList.add('open');
      $('newSceneTitle').focus();
    });
  }

  function renderLinkStep(){
    const room = activeRoom();
    const scene = activeScene();

    if(!scene){
      $('linkStep').style.opacity = '0.55';
      $('panoName').textContent = 'Add a photo first';
      if(viewer){ viewer.destroy(); viewer = null; }
      $('editorPanorama').innerHTML = '';
      $('arrowList').innerHTML = '';
      $('arrowEmpty').textContent = 'Nothing to link yet.';
      $('addArrowBtn').disabled = true;
      return;
    }

    $('linkStep').style.opacity = '1';
    $('panoName').textContent = 'Viewing: ' + scene.title;

    const others = room.scenes.filter(s => s.id !== activeSceneId);
    $('addArrowBtn').disabled = others.length === 0;
    $('targetScene').innerHTML = others.map(s => `<option value="${s.id}">${esc(s.title)}</option>`).join('');

    renderArrowList();
    renderSweep();
    renderViewer();
  }

  function renderArrowList(){
    const scene = activeScene();
    const room = activeRoom();
    const others = room.scenes.filter(s => s.id !== activeSceneId);

    if(scene.hotspots.length === 0){
      $('arrowList').innerHTML = '';
      $('arrowEmpty').textContent = others.length === 0
        ? 'Add another photo first, then you can link them with arrows.'
        : 'No arrows yet. Add one so visitors can move to another spot.';
      $('arrowEmpty').style.display = 'block';
      return;
    }

    $('arrowEmpty').style.display = 'none';
    $('arrowList').innerHTML = scene.hotspots.map(h => `
      <li class="arrow-item">
        <div class="ai-top">
          <span class="ai-label">${esc(h.label)}</span>
          <button class="ai-remove" data-remove="${h.id}">Remove</button>
        </div>
        <div class="ai-dest">→ ${esc(h.target_title || '—')}</div>
      </li>`).join('');

    $('arrowList').querySelectorAll('[data-remove]').forEach(btn => {
      btn.addEventListener('click', () => removeHotspot(Number(btn.dataset.remove)));
    });
  }

  function renderSweep(){
    const scene = activeScene();
    $('sweepSlider').value = Math.round(scene.haov);
    $('sweepValue').textContent = Math.round(scene.haov) + '° of 360°';
    $('sweepNote').textContent = scene.is_partial
      ? 'This looks like a partial phone panorama. Drag until the room looks natural, then save.'
      : 'This looks like a full 360° photo, so no adjustment is usually needed.';
  }

  function viewerConfig(scene, extra){
    return Object.assign({
      type:'equirectangular',
      panorama: scene.panorama_url,
      autoLoad: true,
      showControls: true,
      haov: Number(scene.haov),
      vaov: Number(scene.vaov),
      vOffset: Number(scene.v_offset),
      hotSpots: scene.hotspots.map(h => ({
        pitch:Number(h.pitch), yaw:Number(h.yaw), type:'info', text:h.label,
      })),
    }, extra || {});
  }

  function renderViewer(){
    const scene = activeScene();
    if(viewer){ viewer.destroy(); viewer = null; }

    viewer = pannellum.viewer('editorPanorama', viewerConfig(scene));

    // Pannellum converts a raw click into sphere coordinates, which is what
    // makes click-to-place work instead of typing pitch/yaw by hand.
    viewer.on('mousedown', function(event){
      if(!placingMode) return;
      const coords = viewer.mouseEventToCoords(event);
      stopPlacing();
      addHotspot(coords[0], coords[1]);
    });
  }

  // ===== Arrow flow =====
  function openArrowForm(){
    $('arrowForm').classList.add('open');
    $('addArrowBtn').style.display = 'none';
  }
  function closeArrowForm(){
    $('arrowForm').classList.remove('open');
    $('addArrowBtn').style.display = 'block';
    stopPlacing();
  }
  function startPlacing(){
    placingMode = true;
    $('placingBanner').classList.add('on');
    $('arrowForm').classList.remove('open');
    $('editorPanorama').scrollIntoView({ behavior:'smooth', block:'center' });
  }
  function stopPlacing(){
    placingMode = false;
    $('placingBanner').classList.remove('on');
    $('addArrowBtn').style.display = 'block';
  }

  $('addArrowBtn').addEventListener('click', openArrowForm);
  $('cancelArrowBtn').addEventListener('click', closeArrowForm);
  $('startPlaceBtn').addEventListener('click', () => {
    if(!$('targetScene').value) return toast('Add another photo to link to first.', true);
    startPlacing();
  });
  $('cancelPlaceBtn').addEventListener('click', stopPlacing);

  async function addHotspot(pitch, yaw){
    try {
      const hotspot = await api(`/vr-tours/scenes/${activeSceneId}/hotspots`, {
        method:'POST',
        headers:{ 'Content-Type':'application/json' },
        body: JSON.stringify({
          target_scene_id: Number($('targetScene').value),
          pitch, yaw,
          label: $('hotspotLabel').value.trim() || null,
        }),
      });

      activeScene().hotspots.push(hotspot);
      $('hotspotLabel').value = '';
      renderPhotoGrid();
      renderStepStatus();
      renderArrowList();
      renderViewer();
      toast('Arrow added.');
    } catch(e){ toast(e.message, true); }
  }

  async function removeHotspot(id){
    try {
      await api(`/vr-tours/hotspots/${id}`, { method:'DELETE' });
      const scene = activeScene();
      scene.hotspots = scene.hotspots.filter(h => h.id !== id);
      renderPhotoGrid();
      renderStepStatus();
      renderArrowList();
      renderViewer();
      toast('Arrow removed.');
    } catch(e){ toast(e.message, true); }
  }

  // ===== Photos =====
  $('cancelAddBtn').addEventListener('click', () => {
    $('addForm').classList.remove('open');
    $('newSceneTitle').value = '';
    $('newScenePanorama').value = '';
  });

  $('addSceneBtn').addEventListener('click', async function(){
    const title = $('newSceneTitle').value.trim();
    const fileInput = $('newScenePanorama');

    if(!title) return toast('Name this spot first.', true);
    if(!fileInput.files[0]) return toast('Choose a photo.', true);

    const form = new FormData();
    form.append('title', title);
    form.append('panorama', fileInput.files[0]);

    this.disabled = true;
    try {
      const scene = await api(`/vr-tours/rooms/${activeRoomId}/scenes`, { method:'POST', body: form });
      activeRoom().scenes.push(scene);
      activeSceneId = scene.id;
      $('newSceneTitle').value = '';
      fileInput.value = '';
      $('addForm').classList.remove('open');
      renderTabs();
      renderAll();
      toast('Photo added.');
    } catch(e){ toast(e.message, true); }
    this.disabled = false;
  });

  $('setDefaultBtn').addEventListener('click', async function(){
    try {
      await api(`/vr-tours/scenes/${activeSceneId}/default`, { method:'POST' });
      activeRoom().scenes.forEach(s => { s.is_default = (s.id === activeSceneId); });
      renderPhotoGrid();
      toast('Starting spot updated.');
    } catch(e){ toast(e.message, true); }
  });

  $('deleteSceneBtn').addEventListener('click', async function(){
    const scene = activeScene();
    if(!confirm(`Delete "${scene.title}"? Arrows pointing to it will be removed too.`)) return;

    try {
      await api(`/vr-tours/scenes/${activeSceneId}`, { method:'DELETE' });
      const room = activeRoom();
      const deletedId = activeSceneId;
      room.scenes = room.scenes.filter(s => s.id !== deletedId);
      // Server already removed arrows leading here; mirror that locally.
      room.scenes.forEach(s => {
        s.hotspots = s.hotspots.filter(h => h.target_scene_id !== deletedId);
      });
      if(room.scenes.length && !room.scenes.some(s => s.is_default)){
        room.scenes[0].is_default = true;
      }
      activeSceneId = room.scenes.length ? room.scenes[0].id : null;
      closeArrowForm();
      renderTabs();
      renderAll();
      toast('Photo deleted.');
    } catch(e){ toast(e.message, true); }
  });

  // ===== Coverage =====
  $('advToggle').addEventListener('click', () => {
    $('advBox').classList.toggle('open');
  });

  let sweepTimer = null;
  $('sweepSlider').addEventListener('input', function(){
    const haov = Number(this.value);
    $('sweepValue').textContent = haov + '° of 360°';

    clearTimeout(sweepTimer);
    sweepTimer = setTimeout(() => {
      const scene = activeScene();
      if(!scene) return;
      const img = new Image();
      img.onload = function(){
        const ratio = img.width / img.height;
        const preview = Object.assign({}, scene, { haov, vaov: Math.min(180, haov / ratio) });
        if(viewer){ viewer.destroy(); viewer = null; }
        viewer = pannellum.viewer('editorPanorama', viewerConfig(preview));
      };
      img.src = scene.panorama_url;
    }, 220);
  });

  $('saveSweepBtn').addEventListener('click', async function(){
    try {
      const updated = await api(`/vr-tours/scenes/${activeSceneId}/view`, {
        method:'PATCH',
        headers:{ 'Content-Type':'application/json' },
        body: JSON.stringify({ haov: Number($('sweepSlider').value) }),
      });
      const room = activeRoom();
      const idx = room.scenes.findIndex(s => s.id === updated.id);
      if(idx !== -1) room.scenes[idx] = updated;
      renderLinkStep();
      toast('Coverage saved.');
    } catch(e){ toast(e.message, true); }
  });

  // ===== Publish =====
  $('saveInfoBtn').addEventListener('click', async function(){
    try {
      const updated = await api(`/vacancy/rooms/${activeRoomId}/vr-info`, {
        method:'PATCH',
        headers:{ 'Content-Type':'application/json' },
        body: JSON.stringify({
          vr_caption: $('captionInput').value,
          vr_visibility: $('visibilitySelect').value,
        }),
      });

      const room = activeRoom();
      room.vr_caption = updated.vr_caption;
      room.vr_visibility = updated.vr_visibility;
      room.updated_at = updated.updated_at;
      $('lastUpdatedInput').value = updated.updated_at || '';

      renderStepStatus();
      const msg = $('saveMsg');
      msg.style.display = 'block';
      setTimeout(() => { msg.style.display = 'none'; }, 2000);
    } catch(e){ toast(e.message, true); }
  });

  $('cancelEditBtn').addEventListener('click', function(){
    const room = activeRoom();
    $('captionInput').value = room.vr_caption || '';
    $('visibilitySelect').value = room.vr_visibility || 'draft';
  });

  $('backToListBtn').addEventListener('click', () => {
    if(viewer){ viewer.destroy(); viewer = null; }
    editView.style.display = 'none';
    listView.style.display = 'block';
    renderGrid();
  });

  $('tabsRight').addEventListener('click', () => {
    $('vrTabs').scrollBy({ left: 200, behavior:'smooth' });
  });

  renderGrid();
})();
</script>

</body>
</html>