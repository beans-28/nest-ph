<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH — VR Management</title>
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
  .vr-btn{ flex:1; min-width:70px; font-size:11px; font-weight:600; padding:7px 6px; border-radius:6px; border:1px solid var(--border); background:#fff; color:var(--text-mid); cursor:pointer; text-align:center; }
  .vr-btn:hover{ background:#f7f9f7; }
  .vr-btn.primary{ background:var(--green-btn); border-color:var(--green-btn); color:#fff; }
  .vr-btn.primary:hover{ background:var(--green-btn-hover); }
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

  .vr-edit-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:24px; }
  .vr-edit-label{ font-size:11px; font-weight:700; letter-spacing:0.5px; color:var(--text-mid); text-transform:uppercase; margin-bottom:8px; }
  .vr-panorama-preview{ border:2px dashed var(--status-vacant); background:var(--status-vacant-bg); border-radius:10px; min-height:260px; display:flex; align-items:center; justify-content:center; color:var(--green-accent); font-size:12.5px; font-weight:600; letter-spacing:0.5px; position:relative; overflow:hidden; background-size:cover; background-position:center; cursor:pointer; }
  .vr-panorama-preview img{ position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
  .vr-panorama-preview .vr-preview-label{ position:relative; z-index:1; background:rgba(255,255,255,0.85); padding:6px 14px; border-radius:20px; }
  .vr-preview-actions{ display:flex; gap:10px; margin-top:10px; }
  .vr-preview-actions .vr-btn{ flex:none; padding:8px 16px; }
  .vr-field{ margin-top:20px; }
  .vr-field input[type=text], .vr-field select{ width:100%; border:1px solid var(--border); border-radius:8px; padding:11px 14px; font-size:13px; color:var(--text-dark); background:#fff; font-family:var(--font-body); }
  .vr-field-row{ display:grid; grid-template-columns:1fr 1fr; gap:20px; }
  .vr-field input[readonly]{ background:#f4f6f4; color:var(--text-mid); }
  .vr-edit-actions{ display:flex; gap:10px; margin-top:26px; }
  .vr-edit-actions .vr-btn{ flex:none; padding:11px 26px; font-size:12.5px; }
  .vr-save-msg{ font-size:12px; color:var(--green-accent); font-weight:600; margin-left:auto; align-self:center; display:none; }
  input[type=file]{ display:none; }
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
      <li class="nav-item" data-href="{{ route('admin.addfloor') }}" onclick="window.location.href=this.dataset.href"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="4" width="7" height="7"/><rect x="3" y="15" width="7" height="7"/><rect x="14" y="15" width="7" height="7"/></svg></span>Vacancy Monitor</li>
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

        <div class="vr-edit-card">
          <div class="vr-edit-label">Panorama Image</div>
          <div class="vr-panorama-preview" id="panoramaPreview">
            <span class="vr-preview-label">PANORAMA PREVIEW — click to upload</span>
          </div>
          <input type="file" id="panoramaFileInput" accept="image/png,image/jpeg">
          <div class="vr-preview-actions">
            <button class="vr-btn primary" id="uploadBtn" type="button">Upload / Replace Image</button>
            <button class="vr-btn warn" id="deleteImageBtn" type="button">Delete Image</button>
          </div>

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

<script type="application/json" id="vr-rooms-data">{!! json_encode($rooms) !!}</script>

<script>
(function(){
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  // Server-rendered room list, read from a JSON script tag (avoids using @@json in comments)
  // parsing issues with the VS Code JS/TS language service — same pattern
  // used on the Vacancy Monitoring page).
  let rooms = JSON.parse(document.getElementById('vr-rooms-data').textContent);
  let activeRoomId = null;

  const listView = document.getElementById('vrListView');
  const editView = document.getElementById('vrEditView');
  const vrGrid = document.getElementById('vrGrid');
  const vrTabs = document.getElementById('vrTabs');

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

  async function api(url, options = {}){
    const headers = Object.assign({ 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, options.headers || {});
    const res = await fetch(url, Object.assign({}, options, { headers }));
    if(!res.ok){
      const errBody = await res.json().catch(() => ({}));
      const message = errBody.message || (errBody.errors ? Object.values(errBody.errors)[0][0] : `Request failed (${res.status})`);
      throw new Error(message);
    }
    return res.status === 204 ? null : res.json();
  }

  function findRoom(id){
    return rooms.find(r => r.id === id);
  }

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
      const thumbStyle = room.vr_url ? `style="background-image:url('${room.vr_url}')"` : '';

      card.innerHTML = `
        <div class="vr-thumb ${room.vr_url ? '' : 'empty'}" ${thumbStyle}>
          ${room.vr_url ? '<span class="vr-badge-360">360°</span>' : '<span>No image uploaded</span>'}
          ${room.vr_url ? `<span class="vr-lock-icon" title="${locked ? 'Locked' : 'Unlocked'}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">${locked ? '<rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 118 0v3"/>' : '<rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 017.5-2"/>'}</svg></span>` : ''}
        </div>
        <div class="vr-card-body">
          <div class="vr-card-title">Room ${room.room_no} <span class="vr-floor-chip">Floor ${room.floor ?? '—'}</span></div>
          <div class="vr-card-sub">${room.vr_caption ? room.vr_caption : 'No caption set'}</div>
          <div class="vr-card-updated">Updated ${room.updated_at ?? '—'}</div>
          <div class="vr-card-actions">
            <button class="vr-btn primary" data-action="update" data-id="${room.id}">Update VR</button>
            <button class="vr-btn" data-action="edit" data-id="${room.id}">Edit Info</button>
            <button class="vr-btn" data-action="lock" data-id="${room.id}">${locked ? 'Unlock View' : 'Lock View'}</button>
          </div>
        </div>
      `;
      vrGrid.appendChild(card);
    });

    vrGrid.querySelectorAll('[data-action]').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = Number(btn.dataset.id);
        const action = btn.dataset.action;
        if(action === 'lock'){
          quickToggleLock(id);
        } else {
          openEditView(id);
        }
      });
    });
  }

  async function quickToggleLock(id){
    const room = findRoom(id);
    const newVisibility = room.vr_visibility === 'locked' ? 'public' : 'locked';
    try {
      const updated = await api(`/vacancy/rooms/${id}/vr-info`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ vr_caption: room.vr_caption, vr_visibility: newVisibility }),
      });
      Object.assign(room, updated);
      renderGrid();
    } catch(e){
      alert(e.message);
    }
  }

  function renderTabs(){
    vrTabs.innerHTML = '';
    rooms.forEach(room => {
      const tab = document.createElement('div');
      tab.className = 'vr-tab' + (room.id === activeRoomId ? ' active' : '');
      tab.innerHTML = `Room ${room.room_no}<span class="tab-floor">Floor ${room.floor ?? '—'}</span>`;
      tab.addEventListener('click', () => selectRoom(room.id));
      vrTabs.appendChild(tab);
    });
  }

  function selectRoom(id){
    activeRoomId = id;
    const room = findRoom(id);
    renderTabs();

    const preview = document.getElementById('panoramaPreview');
    if(room.vr_url){
      preview.style.backgroundImage = `url('${room.vr_url}')`;
      preview.innerHTML = '';
    } else {
      preview.style.backgroundImage = 'none';
      preview.innerHTML = '<span class="vr-preview-label">PANORAMA PREVIEW — click to upload</span>';
    }

    document.getElementById('captionInput').value = room.vr_caption ?? '';
    document.getElementById('visibilitySelect').value = room.vr_visibility ?? 'draft';
    document.getElementById('lastUpdatedInput').value = room.updated_at ?? '—';
    document.getElementById('saveMsg').style.display = 'none';
  }

  function openEditView(id){
    selectRoom(id);
    listView.style.display = 'none';
    editView.style.display = 'block';
  }

  function backToList(){
    editView.style.display = 'none';
    listView.style.display = 'block';
    renderGrid();
  }

  document.getElementById('backToListBtn').addEventListener('click', backToList);
  document.getElementById('cancelEditBtn').addEventListener('click', backToList);

  // Upload / replace panorama image
  const fileInput = document.getElementById('panoramaFileInput');
  document.getElementById('uploadBtn').addEventListener('click', () => fileInput.click());
  document.getElementById('panoramaPreview').addEventListener('click', () => fileInput.click());

  fileInput.addEventListener('change', async () => {
    if(!fileInput.files.length || activeRoomId === null) return;
    const formData = new FormData();
    formData.append('vr_image', fileInput.files[0]);

    try {
      const result = await api(`/vacancy/rooms/${activeRoomId}/vr-image`, {
        method: 'POST',
        body: formData,
      });
      const room = findRoom(activeRoomId);
      room.vr_asset_path = result.vr_asset_path;
      room.vr_url = result.url;
      selectRoom(activeRoomId);
    } catch(e){
      alert(e.message);
    } finally {
      fileInput.value = '';
    }
  });

  // Delete panorama image
  document.getElementById('deleteImageBtn').addEventListener('click', async () => {
    if(activeRoomId === null) return;
    const room = findRoom(activeRoomId);
    if(!room.vr_url){ alert('This room has no VR image to delete.'); return; }
    if(!confirm(`Delete the panorama image for Room ${room.room_no}? This cannot be undone.`)) return;

    try {
      const result = await api(`/vacancy/rooms/${activeRoomId}/vr-image`, { method: 'DELETE' });
      Object.assign(room, result.room);
      selectRoom(activeRoomId);
    } catch(e){
      alert(e.message);
    }
  });

  // Save caption + visibility
  document.getElementById('saveInfoBtn').addEventListener('click', async () => {
    if(activeRoomId === null) return;
    const caption = document.getElementById('captionInput').value.trim();
    const visibility = document.getElementById('visibilitySelect').value;

    try {
      const updated = await api(`/vacancy/rooms/${activeRoomId}/vr-info`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ vr_caption: caption, vr_visibility: visibility }),
      });
      Object.assign(findRoom(activeRoomId), updated);
      document.getElementById('lastUpdatedInput').value = updated.updated_at;
      const msg = document.getElementById('saveMsg');
      msg.style.display = 'inline';
      setTimeout(() => { msg.style.display = 'none'; }, 2000);
    } catch(e){
      alert(e.message);
    }
  });

  renderGrid();
})();
</script>

</body>
</html>