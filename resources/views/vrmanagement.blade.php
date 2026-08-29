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
  .avatar-icon{ background:rgba(255,255,255,0.9); color:var(--green-dark); }

  .content{ padding:28px 32px 48px 32px; flex:1; }
  .page-head{ display:flex; align-items:center; gap:12px; margin-bottom:22px; }
  .back-arrow{ width:34px; height:34px; border-radius:8px; background:var(--card-bg); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-mid); flex-shrink:0; }
  .page-head h1{ font-size:20px; font-weight:700; margin:0; color:var(--text-dark); }

  /* ===== LIST VIEW ===== */
  .vr-card-panel{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:22px 24px 26px 24px; }
  .vr-card-panel h2{ font-size:15px; font-weight:700; margin:0 0 18px 0; }
  .vr-grid{ display:grid; grid-template-columns:repeat(auto-fill, minmax(210px, 1fr)); gap:18px; }
  .vr-card{ border:1px solid var(--border); border-radius:10px; overflow:hidden; background:#fff; }
  .vr-thumb{ position:relative; height:130px; background:#dfe6e0 center/cover no-repeat; display:flex; align-items:flex-start; justify-content:space-between; padding:8px; }
  .vr-thumb.empty{ display:flex; align-items:center; justify-content:center; color:var(--text-light); font-size:11px; }
  .vr-badge-360{ background:rgba(0,0,0,0.55); color:#fff; font-size:10px; font-weight:700; padding:3px 7px; border-radius:5px; }
  .vr-lock-icon{ margin-left:auto; width:26px; height:26px; border-radius:50%; background:rgba(0,0,0,0.55); display:flex; align-items:center; justify-content:center; }
  .vr-lock-icon svg{ width:13px; height:13px; color:#fff; }
  .vr-card-body{ padding:12px 14px 14px 14px; }
  .vr-card-title{ font-size:13px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:6px; }
  .vr-floor-chip{ font-size:10px; font-weight:600; color:var(--text-mid); background:#eef1ee; padding:2px 7px; border-radius:20px; }
  .vr-card-sub{ font-size:11px; color:var(--text-light); margin:4px 0 2px 0; }
  .vr-card-updated{ font-size:11px; color:var(--text-light); margin-bottom:10px; }
  .vr-card-actions{ display:flex; gap:6px; flex-wrap:wrap; }
  .vr-btn{ flex:1; min-width:70px; font-size:11px; font-weight:600; padding:7px 6px; border-radius:6px; border:1px solid var(--border); background:#fff; color:var(--text-mid); cursor:pointer; text-align:center; font-family:var(--font-body); }
  .vr-btn:hover:not(:disabled){ background:#f7f9f7; }
  .vr-btn:disabled{ opacity:0.5; cursor:not-allowed; }
  .vr-btn.primary{ background:var(--green-btn); border-color:var(--green-btn); color:#fff; }
  .vr-btn.primary:hover:not(:disabled){ background:var(--green-btn-hover); }
  .vr-btn.warn{ background:#fbeceb; border-color:#f2cfcc; color:var(--status-occupied); }

  /* ===== EDIT VIEW ===== */
  #vrEditView{ display:none; }
  .vr-tabs-bar{ display:flex; align-items:center; gap:10px; margin-bottom:20px; }
  .vr-tabs-scroll{ display:flex; gap:8px; overflow-x:auto; flex:1; padding-bottom:2px; }
  .vr-tab{ flex-shrink:0; border:1px solid var(--border); background:#fff; border-radius:8px; padding:8px 16px; font-size:12.5px; font-weight:600; color:var(--text-mid); cursor:pointer; white-space:nowrap; }
  .vr-tab .tab-floor{ display:block; font-size:10px; font-weight:500; color:var(--text-light); }
  .vr-tab.active{ background:var(--status-vacant-bg); border-color:var(--status-vacant); color:var(--green-accent); }
  .vr-tab-scroll-btn{ flex-shrink:0; width:30px; height:30px; border-radius:8px; border:1px solid var(--border); background:#fff; color:var(--text-mid); display:flex; align-items:center; justify-content:center; cursor:pointer; }
  .vr-tab-scroll-btn svg{ width:14px; height:14px; }

  .vr-edit-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:24px; margin-bottom:18px; }
  .vr-edit-label{ font-size:11px; font-weight:700; letter-spacing:0.5px; color:var(--text-mid); text-transform:uppercase; margin-bottom:8px; }
  .vr-field{ margin-top:20px; }
  .vr-field input[type=text], .vr-field select{ width:100%; border:1px solid var(--border); border-radius:8px; padding:11px 14px; font-size:13px; color:var(--text-dark); background:#fff; font-family:var(--font-body); }
  .vr-field-row{ display:grid; grid-template-columns:1fr 1fr; gap:20px; }
  .vr-field input[readonly]{ background:#f4f6f4; color:var(--text-mid); }
  .vr-edit-actions{ display:flex; gap:10px; margin-top:26px; align-items:center; }
  .vr-edit-actions .vr-btn{ flex:none; padding:11px 26px; font-size:12.5px; }
  .vr-save-msg{ font-size:12px; color:var(--green-accent); font-weight:600; margin-left:auto; align-self:center; display:none; }
  input[type=file].hidden-file{ display:none; }

  /* Scenes */
  .scene-strip{ display:flex; gap:12px; flex-wrap:wrap; margin-bottom:16px; }
  .scene-item{ width:160px; border:2px solid var(--border); border-radius:10px; overflow:hidden; cursor:pointer; background:#fafbfa; }
  .scene-item.active{ border-color:var(--green-accent); }
  .scene-item img{ width:100%; height:84px; object-fit:cover; display:block; background:#dfe6e0; }
  .scene-item .meta{ padding:8px 10px; }
  .scene-item .title{ font-size:12px; font-weight:700; word-break:break-word; }
  .scene-item .badge-default{ font-size:9.5px; font-weight:700; color:var(--green-accent); text-transform:uppercase; letter-spacing:0.4px; }
  .scene-empty{ font-size:12.5px; color:var(--text-light); font-style:italic; padding:8px 0 16px; }

  .add-scene-row{ display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end; border-top:1px solid var(--border); padding-top:18px; }
  .add-scene-row .field{ display:flex; flex-direction:column; gap:6px; }
  .add-scene-row input[type=text]{ border:1px solid var(--border); border-radius:8px; padding:10px 13px; font-size:13px; font-family:var(--font-body); min-width:220px; }
  .add-scene-row input[type=file]{ font-size:12px; }

  #editorPanorama{ width:100%; height:400px; border-radius:10px; overflow:hidden; background:#222; }
  .editor-hint{ background:#eef5ef; border:1px solid #cfe0d1; border-radius:8px; padding:10px 14px; font-size:12.5px; color:#33513a; margin-bottom:12px; line-height:1.6; }
  .editor-hint.placing{ background:#fff6e0; border-color:#f0dfa8; color:#6b5a1f; }
  .hotspot-row{ display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end; margin-bottom:12px; }
  .hotspot-row .field{ display:flex; flex-direction:column; gap:6px; }
  .hotspot-row select, .hotspot-row input[type=text]{ border:1px solid var(--border); border-radius:8px; padding:10px 13px; font-size:13px; font-family:var(--font-body); min-width:190px; background:#fff; }

  .hotspot-table{ width:100%; border-collapse:collapse; margin-top:14px; font-size:12.5px; }
  .hotspot-table th{ text-align:left; font-size:10.5px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-light); padding:8px 10px; border-bottom:1px solid var(--border); }
  .hotspot-table td{ padding:9px 10px; border-bottom:1px solid #f0f2f0; }
  .hotspot-table .coords{ color:var(--text-light); font-size:11.5px; }
  .hotspot-table .empty{ color:var(--text-light); font-style:italic; }

  .toast{ position:fixed; bottom:22px; right:22px; background:var(--green-accent); color:#fff; padding:12px 20px; border-radius:8px; font-size:13px; display:none; z-index:99; box-shadow:0 6px 18px rgba(0,0,0,0.2); }
  .toast.error{ background:var(--status-occupied); }
  .toast.visible{ display:block; }
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
          <h1>VR Management</h1>
        </div>

        <div class="vr-tabs-bar">
          <div class="vr-tabs-scroll" id="vrTabs"></div>
          <div class="vr-tab-scroll-btn" id="tabsRight"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg></div>
        </div>

        <!-- Scenes -->
        <div class="vr-edit-card">
          <div class="vr-edit-label">Panorama Scenes</div>
          <div class="scene-strip" id="sceneStrip"></div>

          <div class="add-scene-row">
            <div class="field">
              <div class="vr-edit-label">New Scene Title</div>
              <input type="text" id="newSceneTitle" placeholder="e.g. Entrance, Bedside, Study Corner">
            </div>
            <div class="field">
              <div class="vr-edit-label">360&deg; Panorama (equirectangular)</div>
              <input type="file" id="newScenePanorama" accept="image/jpeg,image/png">
            </div>
            <button class="vr-btn primary" id="addSceneBtn" type="button" style="flex:none;padding:10px 20px;">Add Scene</button>
          </div>
        </div>

        <!-- Hotspot editor -->
        <div class="vr-edit-card" id="hotspotCard">
          <div class="vr-edit-label">Place Hotspots — <span id="editorSceneTitle"></span></div>

          <div class="editor-hint" id="editorHint"></div>

          <div class="hotspot-row">
            <div class="field">
              <div class="vr-edit-label">Destination Scene</div>
              <select id="targetScene"></select>
            </div>
            <div class="field">
              <div class="vr-edit-label">Arrow Label (optional)</div>
              <input type="text" id="hotspotLabel" placeholder="Auto: “Go to [scene]”">
            </div>
            <button class="vr-btn primary" id="placeHotspotBtn" type="button" style="flex:none;padding:10px 20px;">Place Hotspot</button>
            <button class="vr-btn" id="cancelPlaceBtn" type="button" style="flex:none;padding:10px 20px;display:none;">Cancel</button>
          </div>

          <div id="editorPanorama"></div>

          <table class="hotspot-table">
            <thead><tr><th>Label</th><th>Goes To</th><th>Position</th><th></th></tr></thead>
            <tbody id="hotspotBody"></tbody>
          </table>

          <div class="vr-edit-actions">
            <button class="vr-btn" id="setDefaultBtn" type="button">Make Starting Scene</button>
            <button class="vr-btn warn" id="deleteSceneBtn" type="button">Delete This Scene</button>
          </div>
        </div>

        <!-- Room-level settings -->
        <div class="vr-edit-card">
          <div class="vr-edit-label">Tour Settings</div>

          <div class="vr-field">
            <div class="vr-edit-label">Caption to Show on View</div>
            <input type="text" id="captionInput" placeholder="Enter caption...">
          </div>

          <div class="vr-field vr-field-row">
            <div>
              <div class="vr-edit-label">Visibility</div>
              <select id="visibilitySelect">
                <option value="public">Public View</option>
                <option value="locked">Lock</option>
                <option value="draft">Draft</option>
              </select>
            </div>
            <div>
              <div class="vr-edit-label">Last Updated</div>
              <input type="text" id="lastUpdatedInput" readonly>
            </div>
          </div>

          <div class="vr-edit-actions">
            <button class="vr-btn primary" id="saveInfoBtn" type="button">Save</button>
            <button class="vr-btn" id="cancelEditBtn" type="button">Cancel</button>
            <span class="vr-save-msg" id="saveMsg">Saved!</span>
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

  const listView = document.getElementById('vrListView');
  const editView = document.getElementById('vrEditView');
  const vrGrid = document.getElementById('vrGrid');
  const vrTabs = document.getElementById('vrTabs');
  const sceneStrip = document.getElementById('sceneStrip');
  const hotspotCard = document.getElementById('hotspotCard');
  const targetSelect = document.getElementById('targetScene');
  const hotspotBody = document.getElementById('hotspotBody');
  const editorHint = document.getElementById('editorHint');

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

  function toast(msg, isError){
    const el = document.getElementById('toast');
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
    const headers = Object.assign({ 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, options.headers || {});
    const res = await fetch(url, Object.assign({}, options, { headers }));
    if(!res.ok){
      const errBody = await res.json().catch(() => ({}));
      throw new Error(errBody.message || (errBody.errors ? Object.values(errBody.errors)[0][0] : `Request failed (${res.status})`));
    }
    return res.status === 204 ? null : res.json();
  }

  function findRoom(id){ return rooms.find(r => r.id === id); }
  function activeRoom(){ return findRoom(activeRoomId); }
  function activeScene(){ return activeRoom()?.scenes.find(s => s.id === activeSceneId); }

  function defaultSceneOf(room){
    return room.scenes.find(s => s.is_default) || room.scenes[0] || null;
  }

  // ===== LIST VIEW =====
  function renderGrid(){
    vrGrid.innerHTML = '';

    if(rooms.length === 0){
      vrGrid.innerHTML = '<p style="color:var(--text-light);font-size:12.5px;">No rooms yet. Add rooms from Vacancy Monitoring first.</p>';
      return;
    }

    rooms.forEach(room => {
      const card = document.createElement('div');
      card.className = 'vr-card';

      const locked = room.vr_visibility === 'locked';
      const cover = defaultSceneOf(room);
      const thumbStyle = cover ? `style="background-image:url('${cover.panorama_url}')"` : '';
      const sceneCount = room.scenes.length;

      card.innerHTML = `
        <div class="vr-thumb ${cover ? '' : 'empty'}" ${thumbStyle}>
          ${cover ? '<span class="vr-badge-360">360°</span>' : '<span>No scenes yet</span>'}
          ${cover ? `<span class="vr-lock-icon" title="${locked ? 'Locked' : 'Unlocked'}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">${locked ? '<rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 118 0v3"/>' : '<rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 017.5-2"/>'}</svg></span>` : ''}
        </div>
        <div class="vr-card-body">
          <div class="vr-card-title">Room ${esc(room.room_no)} <span class="vr-floor-chip">Floor ${esc(room.floor ?? '—')}</span></div>
          <div class="vr-card-sub">${sceneCount} scene${sceneCount === 1 ? '' : 's'} · ${esc(room.vr_visibility)}</div>
          <div class="vr-card-updated">${esc(room.updated_at ?? '')}</div>
          <div class="vr-card-actions">
            <button class="vr-btn primary" data-edit="${room.id}">Edit Tour</button>
          </div>
        </div>
      `;

      vrGrid.appendChild(card);
    });

    vrGrid.querySelectorAll('[data-edit]').forEach(btn => {
      btn.addEventListener('click', () => openEditor(Number(btn.dataset.edit)));
    });
  }

  // ===== EDIT VIEW =====
  function openEditor(roomId){
    activeRoomId = roomId;
    const room = activeRoom();
    activeSceneId = room.scenes.length ? (defaultSceneOf(room).id) : null;

    listView.style.display = 'none';
    editView.style.display = 'block';

    document.getElementById('captionInput').value = room.vr_caption || '';
    document.getElementById('visibilitySelect').value = room.vr_visibility || 'draft';
    document.getElementById('lastUpdatedInput').value = room.updated_at || '';

    renderTabs();
    renderSceneStrip();
    renderEditor();
  }

  function renderTabs(){
    vrTabs.innerHTML = rooms.map(room => `
      <div class="vr-tab ${room.id === activeRoomId ? 'active' : ''}" data-tab="${room.id}">
        Room ${esc(room.room_no)}
        <span class="tab-floor">Floor ${esc(room.floor ?? '—')}</span>
      </div>
    `).join('');

    vrTabs.querySelectorAll('[data-tab]').forEach(tab => {
      tab.addEventListener('click', () => openEditor(Number(tab.dataset.tab)));
    });
  }

  function renderSceneStrip(){
    const room = activeRoom();

    if(room.scenes.length === 0){
      sceneStrip.innerHTML = '<div class="scene-empty">No scenes yet. Upload a 360° panorama below to start this room\'s tour.</div>';
      return;
    }

    sceneStrip.innerHTML = room.scenes.map(scene => `
      <div class="scene-item ${scene.id === activeSceneId ? 'active' : ''}" data-scene="${scene.id}">
        <img src="${scene.panorama_url}" alt="">
        <div class="meta">
          <div class="title">${esc(scene.title)}</div>
          ${scene.is_default ? '<div class="badge-default">Starting scene</div>' : ''}
        </div>
      </div>
    `).join('');

    sceneStrip.querySelectorAll('[data-scene]').forEach(item => {
      item.addEventListener('click', () => {
        activeSceneId = Number(item.dataset.scene);
        renderSceneStrip();
        renderEditor();
      });
    });
  }

  function updateHint(){
    document.getElementById('cancelPlaceBtn').style.display = placingMode ? 'inline-block' : 'none';
    editorHint.classList.toggle('placing', placingMode);
    editorHint.innerHTML = placingMode
      ? 'Now <strong>click the spot in the panorama</strong> where the arrow should appear — usually a doorway, or the direction someone would walk.'
      : 'Drag to look around. To add an arrow, pick a destination, click <strong>Place Hotspot</strong>, then click where it should sit in the panorama.';
  }

  function renderTargetOptions(){
    const others = activeRoom().scenes.filter(s => s.id !== activeSceneId);
    targetSelect.innerHTML = others.length
      ? others.map(s => `<option value="${s.id}">${esc(s.title)}</option>`).join('')
      : '<option value="">Add another scene first</option>';
    document.getElementById('placeHotspotBtn').disabled = others.length === 0;
  }

  function renderHotspotTable(){
    const scene = activeScene();

    if(!scene || scene.hotspots.length === 0){
      hotspotBody.innerHTML = '<tr><td colspan="4" class="empty">No hotspots in this scene yet.</td></tr>';
      return;
    }

    hotspotBody.innerHTML = scene.hotspots.map(h => `
      <tr>
        <td>${esc(h.label)}</td>
        <td>${esc(h.target_title || '—')}</td>
        <td class="coords">pitch ${Number(h.pitch).toFixed(1)}°, yaw ${Number(h.yaw).toFixed(1)}°</td>
        <td><button class="vr-btn warn" data-remove="${h.id}" style="flex:none;">Remove</button></td>
      </tr>
    `).join('');

    hotspotBody.querySelectorAll('[data-remove]').forEach(btn => {
      btn.addEventListener('click', () => removeHotspot(Number(btn.dataset.remove)));
    });
  }

  function renderEditor(){
    const scene = activeScene();

    if(!scene){
      hotspotCard.style.display = 'none';
      return;
    }

    hotspotCard.style.display = 'block';
    document.getElementById('editorSceneTitle').textContent = scene.title;

    renderTargetOptions();
    renderHotspotTable();
    updateHint();

    if(viewer){ viewer.destroy(); viewer = null; }

    viewer = pannellum.viewer('editorPanorama', {
      type: 'equirectangular',
      panorama: scene.panorama_url,
      autoLoad: true,
      showControls: true,
      hotSpots: scene.hotspots.map(h => ({
        pitch: Number(h.pitch),
        yaw: Number(h.yaw),
        type: 'info',
        text: h.label,
      })),
    });

    // Pannellum turns a raw click into sphere coordinates, which is what makes
    // click-to-place possible instead of hand-typing pitch/yaw angles.
    viewer.on('mousedown', function(event){
      if(!placingMode) return;
      const coords = viewer.mouseEventToCoords(event);
      placingMode = false;
      updateHint();
      addHotspot(coords[0], coords[1]);
    });
  }

  // ===== ACTIONS =====
  document.getElementById('backToListBtn').addEventListener('click', () => {
    if(viewer){ viewer.destroy(); viewer = null; }
    editView.style.display = 'none';
    listView.style.display = 'block';
    renderGrid();
  });

  document.getElementById('addSceneBtn').addEventListener('click', async function(){
    const title = document.getElementById('newSceneTitle').value.trim();
    const fileInput = document.getElementById('newScenePanorama');

    if(!title) return toast('Give the scene a title first.', true);
    if(!fileInput.files[0]) return toast('Choose a panorama image.', true);

    const form = new FormData();
    form.append('title', title);
    form.append('panorama', fileInput.files[0]);

    this.disabled = true;
    try {
      const scene = await api(`/vr-tours/rooms/${activeRoomId}/scenes`, { method:'POST', body: form });
      activeRoom().scenes.push(scene);
      activeSceneId = scene.id;
      document.getElementById('newSceneTitle').value = '';
      fileInput.value = '';
      renderSceneStrip();
      renderEditor();
      toast('Scene added.');
    } catch(e){ toast(e.message, true); }
    this.disabled = false;
  });

  document.getElementById('placeHotspotBtn').addEventListener('click', function(){
    if(!targetSelect.value) return toast('Add a second scene to link to first.', true);
    placingMode = true;
    updateHint();
  });

  document.getElementById('cancelPlaceBtn').addEventListener('click', function(){
    placingMode = false;
    updateHint();
  });

  async function addHotspot(pitch, yaw){
    try {
      const hotspot = await api(`/vr-tours/scenes/${activeSceneId}/hotspots`, {
        method:'POST',
        headers:{ 'Content-Type':'application/json' },
        body: JSON.stringify({
          target_scene_id: Number(targetSelect.value),
          pitch: pitch,
          yaw: yaw,
          label: document.getElementById('hotspotLabel').value.trim() || null,
        }),
      });

      activeScene().hotspots.push(hotspot);
      document.getElementById('hotspotLabel').value = '';
      renderHotspotTable();
      renderEditor();
      toast('Hotspot placed.');
    } catch(e){ toast(e.message, true); }
  }

  async function removeHotspot(id){
    try {
      await api(`/vr-tours/hotspots/${id}`, { method:'DELETE' });
      const scene = activeScene();
      scene.hotspots = scene.hotspots.filter(h => h.id !== id);
      renderHotspotTable();
      renderEditor();
      toast('Hotspot removed.');
    } catch(e){ toast(e.message, true); }
  }

  document.getElementById('setDefaultBtn').addEventListener('click', async function(){
    try {
      await api(`/vr-tours/scenes/${activeSceneId}/default`, { method:'POST' });
      activeRoom().scenes.forEach(s => { s.is_default = (s.id === activeSceneId); });
      renderSceneStrip();
      toast('Starting scene updated.');
    } catch(e){ toast(e.message, true); }
  });

  document.getElementById('deleteSceneBtn').addEventListener('click', async function(){
    const scene = activeScene();
    if(!confirm(`Delete "${scene.title}"? Any arrows pointing to it will be removed too.`)) return;

    try {
      await api(`/vr-tours/scenes/${activeSceneId}`, { method:'DELETE' });
      const room = activeRoom();
      const deletedId = activeSceneId;
      room.scenes = room.scenes.filter(s => s.id !== deletedId);
      // Arrows leading to the deleted scene are gone server-side; clear them
      // locally too so the table doesn't show stale rows.
      room.scenes.forEach(s => {
        s.hotspots = s.hotspots.filter(h => h.target_scene_id !== deletedId);
      });
      if(room.scenes.length && !room.scenes.some(s => s.is_default)){
        room.scenes[0].is_default = true;
      }
      activeSceneId = room.scenes.length ? room.scenes[0].id : null;
      renderSceneStrip();
      renderEditor();
      toast('Scene deleted.');
    } catch(e){ toast(e.message, true); }
  });

  document.getElementById('saveInfoBtn').addEventListener('click', async function(){
    try {
      const updated = await api(`/vacancy/rooms/${activeRoomId}/vr-info`, {
        method:'PATCH',
        headers:{ 'Content-Type':'application/json' },
        body: JSON.stringify({
          vr_caption: document.getElementById('captionInput').value,
          vr_visibility: document.getElementById('visibilitySelect').value,
        }),
      });

      const room = activeRoom();
      room.vr_caption = updated.vr_caption;
      room.vr_visibility = updated.vr_visibility;
      room.updated_at = updated.updated_at;
      document.getElementById('lastUpdatedInput').value = updated.updated_at || '';

      const msg = document.getElementById('saveMsg');
      msg.style.display = 'block';
      setTimeout(() => { msg.style.display = 'none'; }, 2000);
    } catch(e){ toast(e.message, true); }
  });

  document.getElementById('cancelEditBtn').addEventListener('click', function(){
    const room = activeRoom();
    document.getElementById('captionInput').value = room.vr_caption || '';
    document.getElementById('visibilitySelect').value = room.vr_visibility || 'draft';
  });

  document.getElementById('tabsRight').addEventListener('click', () => {
    vrTabs.scrollBy({ left: 200, behavior: 'smooth' });
  });

  renderGrid();
})();
</script>

</body>
</html>