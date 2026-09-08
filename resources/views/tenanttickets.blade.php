<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH — Tickets</title>
<style>
  :root{
    --green-dark:#3f6b4a; --green-darker:#345a3e; --green-mid:#4f7c57;
    --green-sidebar-top:#33513c; --green-sidebar-bottom:#223a29;
    --green-accent:#3f6b4a; --green-btn:#3f6b4a; --green-btn-hover:#2f5439;
    --logout-bg:#16241b; --logout-bg-hover:#0f1b13;
    --bg-page:#f4f6f4; --card-bg:#ffffff;
    --text-dark:#1f2a22; --text-mid:#5b6b60; --text-light:#8a9690; --border:#e5e9e4;
    --blue:#33629e; --blue-bg:#e3ecf7;
    --purple:#7a4fc9; --purple-bg:#e9defa;
    --orange:#a4761a; --orange-bg:#fbe9c8;
    --red:#c0463d; --red-bg:#f7d9d7;
    --font-body: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  }
  *{box-sizing:border-box;}
  html,body{ margin:0; padding:0; font-family:var(--font-body); background:var(--bg-page); color:var(--text-dark); }
  .app{ display:flex; min-height:100vh; }

  .sidebar{
    width:220px; flex-shrink:0;
    background:linear-gradient(180deg, var(--green-sidebar-top) 0%, var(--green-sidebar-bottom) 100%);
    color:#eaf0ea; display:flex; flex-direction:column; padding:18px 0;
    position:sticky; top:0; height:100vh; overflow:hidden;
    box-shadow:2px 0 14px rgba(0,0,0,0.12);
    transition:width 0.2s ease;
  }
  .sidebar-logo{ display:flex; align-items:center; gap:8px; padding:0 20px 18px 20px; font-weight:700; font-size:16px; border-bottom:1px solid rgba(255,255,255,0.12); margin-bottom:12px; white-space:nowrap; flex-shrink:0; }
  .sidebar-logo .logo-mark{ width:16px; height:16px; border:2px solid #eaf0ea; display:inline-block; position:relative; flex-shrink:0; }
  .sidebar-logo .logo-mark::before, .sidebar-logo .logo-mark::after{ content:''; position:absolute; background:#eaf0ea; width:2px; height:12px; top:0; left:5px; }
  .sidebar-section-label{ font-size:10.5px; text-transform:uppercase; letter-spacing:1px; color:rgba(234,240,234,0.55); padding:4px 20px 8px 20px; font-weight:600; white-space:nowrap; flex-shrink:0; }
  .nav-list{ list-style:none; margin:0; padding:0 0 8px 0; flex:1; min-height:0; overflow-y:auto; overflow-x:hidden; scrollbar-width:thin; scrollbar-color:rgba(255,255,255,0.25) transparent; }
  .nav-list::-webkit-scrollbar{ width:5px; }
  .nav-list::-webkit-scrollbar-track{ background:transparent; }
  .nav-list::-webkit-scrollbar-thumb{ background:rgba(255,255,255,0.22); border-radius:10px; }
  .nav-item{ display:flex; align-items:center; gap:11px; padding:9px 20px; font-size:13px; color:rgba(234,240,234,0.82); cursor:pointer; border-left:3px solid transparent; white-space:nowrap; flex-shrink:0; transition:background 0.12s ease, color 0.12s ease; }
  .nav-item:hover{ background:rgba(255,255,255,0.08); color:#fff; }
  .nav-item.active{ background:rgba(255,255,255,0.16); color:#fff; font-weight:600; border-left:3px solid #ffffff; }
  .nav-item .icon svg{ width:15px; height:15px; flex-shrink:0; }
  .sidebar-footer{ padding:14px 20px 2px 20px; border-top:1px solid rgba(255,255,255,0.12); margin-top:8px; flex-shrink:0; }
  .sidebar-footer .nav-item{ padding:10px 14px; border-radius:9px; background:var(--logout-bg); border-left:none; color:#fff; box-shadow:inset 0 0 0 1px rgba(255,255,255,0.06); }
  .sidebar-footer .nav-item:hover{ background:var(--logout-bg-hover); }
  .sidebar-footer .nav-item .icon svg{ color:#fff; }
  .sidebar.collapsed{ width:64px; }
  .sidebar.collapsed .sidebar-logo{ justify-content:center; padding-left:0; padding-right:0; }
  .sidebar.collapsed .sidebar-logo .logo-text{ display:none; }
  .sidebar.collapsed .sidebar-section-label{ display:none; }
  .sidebar.collapsed .nav-item{ justify-content:center; padding-left:0; padding-right:0; gap:0; }
  .sidebar.collapsed .nav-item .label{ display:none; }
  .sidebar.collapsed .sidebar-footer{ padding-left:10px; padding-right:10px; }
  .sidebar.collapsed .sidebar-footer .nav-item{ padding:10px 0; }
  .nav-item:focus-visible, .hamburger-icon:focus-visible, .topbar-icon:focus-visible, .back-arrow:focus-visible{ outline:2px solid #ffffff; outline-offset:-2px; border-radius:4px; }
  .topbar-icon:focus-visible{ outline-color:var(--green-darker); }

  .main{ flex:1; display:flex; flex-direction:column; min-width:0; }
  .topbar{
    display:flex; align-items:center; gap:16px;
    background:linear-gradient(90deg, rgba(51,81,60,0.45), rgba(63,107,74,0.45));
    backdrop-filter:blur(16px) saturate(140%); -webkit-backdrop-filter:blur(16px) saturate(140%);
    padding:16px 28px; box-shadow:0 1px 0 rgba(0,0,0,0.08);
    position:sticky; top:0; z-index:20;
  }
  .hamburger-icon{ width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; cursor:pointer; transition:background 0.12s ease; }
  .hamburger-icon:hover{ background:rgba(255,255,255,0.14); }
  .topbar-right{ margin-left:auto; display:flex; align-items:center; gap:14px; }
  .topbar-username{ color:#fff; font-size:13.5px; font-weight:600; white-space:nowrap; text-shadow:0 1px 2px rgba(0,0,0,0.15); }
  .topbar-icon{ width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.92); display:flex; align-items:center; justify-content:center; color:var(--green-dark); cursor:pointer; }
  .topbar-icon svg{ width:16px; height:16px; }

  .content{ padding:26px 34px 48px 34px; flex:1; max-width:1180px; }
  .page-head{ display:flex; align-items:flex-start; gap:12px; margin-bottom:8px; }
  .back-arrow{ width:30px; height:30px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-mid); flex-shrink:0; margin-top:2px; }
  .page-head-text{ flex:1; }
  .page-head-text h1{ font-size:20px; font-weight:700; margin:0; color:var(--green-accent); }
  .page-head-text p{ font-size:12.5px; color:var(--text-mid); margin:2px 0 0 0; }
  .new-ticket-btn{ background:var(--green-btn); color:#fff; border:none; border-radius:8px; padding:12px 20px; font-size:14px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:8px; flex-shrink:0; }
  .new-ticket-btn:hover{ background:var(--green-btn-hover); }
  .new-ticket-btn svg{ width:16px; height:16px; }

  .restricted-banner{ display:none; background:var(--red-bg); border:1px solid #f2cfcc; color:var(--red); border-radius:10px; padding:12px 16px; font-size:12.5px; margin:18px 0; }
  .restricted-banner.visible{ display:block; }
  .restricted-banner a{ color:var(--red); font-weight:700; }

  .ticket-list{ display:flex; flex-direction:column; gap:18px; margin-top:22px; }
  .empty{ text-align:center; color:var(--text-light); font-style:italic; padding:50px 20px; background:var(--card-bg); border:1px solid var(--border); border-radius:14px; margin-top:22px; }

  .ticket-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:14px; padding:20px 22px; }
  .tc-head{ display:flex; justify-content:space-between; align-items:flex-start; gap:12px; margin-bottom:4px; }
  .tc-title{ font-size:15px; font-weight:700; color:var(--green-accent); margin:0; }
  .tc-sub{ font-size:11.5px; color:var(--text-light); margin-top:4px; }
  .tc-cat{ font-size:11px; color:var(--text-mid); background:#f4f7f4; border-radius:6px; padding:3px 8px; display:inline-block; margin-top:6px; }

  .badge{ font-size:11px; font-weight:700; padding:4px 12px; border-radius:20px; white-space:nowrap; flex-shrink:0; }
  .badge.open{ background:var(--blue-bg); color:var(--blue); }
  .badge.seen{ background:var(--purple-bg); color:var(--purple); }
  .badge.in_progress{ background:var(--orange-bg); color:var(--orange); }
  .badge.resolved{ background:#d9f2dd; color:#3f7a4a; }
  .badge.rejected{ background:var(--red-bg); color:var(--red); }

  .tc-gallery{ display:flex; gap:8px; flex-wrap:wrap; margin:14px 0; }
  .tc-gallery img{ width:76px; height:76px; object-fit:cover; border-radius:8px; border:1px solid var(--border); cursor:pointer; }

  .thread{ display:flex; flex-direction:column; gap:10px; margin-top:16px; }
  .msg{ display:flex; gap:10px; align-items:flex-start; }
  .msg-avatar{ width:32px; height:32px; border-radius:50%; background:var(--green-dark); color:#fff; font-size:11.5px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .msg-body{ flex:1; border-radius:10px; padding:10px 14px; background:#f5f7f5; }
  .msg.admin .msg-body{ background:var(--blue-bg); }
  .msg-name{ font-size:12.5px; font-weight:700; color:var(--text-dark); }
  .msg.admin .msg-name{ color:var(--green-accent); }
  .msg-time{ font-size:10.5px; color:var(--text-light); font-weight:400; margin-left:6px; }
  .msg-text{ font-size:13px; color:var(--text-dark); margin-top:3px; line-height:1.5; }

  .reply-row{ display:flex; gap:10px; margin-top:14px; }
  .reply-row input{ flex:1; border:1px solid var(--border); border-radius:8px; padding:10px 14px; font-size:13px; font-family:var(--font-body); }
  .reply-row button{ background:var(--green-btn); color:#fff; border:none; border-radius:8px; padding:10px 20px; font-size:13px; font-weight:700; cursor:pointer; }
  .reply-row button:hover{ background:var(--green-btn-hover); }
  .reply-row button:disabled{ opacity:.6; cursor:not-allowed; }

  /* ===== New Ticket modal ===== */
  .modal-overlay{ display:none; position:fixed; inset:0; background:rgba(20,30,20,.5); z-index:60; align-items:center; justify-content:center; padding:20px; }
  .modal-overlay.open{ display:flex; }
  .modal-box{ background:#fff; border-radius:16px; width:100%; max-width:480px; max-height:90vh; overflow-y:auto; padding:26px 28px; }
  .modal-box h2{ font-size:19px; font-weight:700; color:var(--green-accent); margin:0 0 20px 0; }
  .fld{ margin-bottom:16px; }
  .fld label{ display:block; font-size:13px; font-weight:600; color:var(--text-dark); margin-bottom:7px; }
  .fld input, .fld select, .fld textarea{ width:100%; border:1px solid var(--border); border-radius:9px; padding:11px 14px; font-size:13.5px; font-family:var(--font-body); color:var(--text-dark); }
  .fld input[readonly]{ background:#f5f7f5; color:var(--text-mid); }
  .fld textarea{ min-height:90px; resize:vertical; }

  /* Photo grid: small square tiles, native picker feel instead of one
     big centered dropzone. Up to MAX_ATTACHMENTS, plus an "add" tile. */
  .photo-grid{ display:grid; grid-template-columns:repeat(auto-fill, minmax(76px, 1fr)); gap:10px; }
  .photo-tile{ position:relative; aspect-ratio:1; border-radius:10px; overflow:hidden; border:1px solid var(--border); background:#f5f7f5; }
  .photo-tile img{ width:100%; height:100%; object-fit:cover; display:block; }
  .photo-tile .remove-tile{ position:absolute; top:4px; right:4px; width:20px; height:20px; border-radius:50%; background:rgba(20,30,20,.65); color:#fff; border:none; font-size:13px; line-height:1; cursor:pointer; display:flex; align-items:center; justify-content:center; padding:0; }
  .photo-tile .remove-tile:hover{ background:var(--red); }
  .add-tile{ aspect-ratio:1; border:1.5px dashed var(--border); border-radius:10px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:3px; cursor:pointer; color:var(--text-light); background:none; }
  .add-tile:hover{ border-color:var(--green-mid); color:var(--green-accent); }
  .add-tile svg{ width:18px; height:18px; }
  .add-tile span{ font-size:10.5px; font-weight:600; }
  .photo-hint{ font-size:11px; color:var(--text-light); margin-top:8px; }

  .modal-error{ display:none; background:var(--red-bg); border:1px solid #f2cfcc; color:var(--red); border-radius:8px; padding:10px 13px; font-size:12.5px; margin-bottom:14px; }
  .modal-error.visible{ display:block; }
  .modal-actions{ display:flex; gap:10px; margin-top:20px; }
  .modal-actions button{ flex:1; border-radius:9px; padding:12px; font-size:14px; font-weight:700; cursor:pointer; border:1px solid var(--border); }
  .modal-actions .btn-cancel{ background:#fff; color:var(--text-mid); }
  .modal-actions .btn-submit{ background:var(--green-btn); color:#fff; border-color:var(--green-btn); }
  .modal-actions .btn-submit:hover{ background:var(--green-btn-hover); }
  .modal-actions .btn-submit:disabled{ opacity:.6; cursor:not-allowed; }

  .success-state{ display:none; text-align:center; padding:10px 0; }
  .success-state.active{ display:block; }
  .success-badge{ width:64px; height:64px; margin-bottom:14px; }
  .success-state h2{ margin-bottom:8px; }
  .success-state p{ font-size:13px; color:var(--text-mid); line-height:1.6; margin:0 0 20px 0; }
  .success-state button{ background:var(--green-btn); color:#fff; border:none; border-radius:9px; padding:12px 26px; font-size:14px; font-weight:700; cursor:pointer; }
  .success-state button:hover{ background:var(--green-btn-hover); }

  .toast{ position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(20px); background:var(--text-dark); color:#fff; padding:12px 22px; border-radius:10px; font-size:13px; opacity:0; pointer-events:none; transition:.25s; z-index:80; }
  .toast.visible{ opacity:1; transform:translateX(-50%) translateY(0); }
  .toast.error{ background:var(--red); }

  /* Lightbox for viewing a ticket-card photo full size */
  .lightbox{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.8); z-index:90; align-items:center; justify-content:center; padding:30px; }
  .lightbox.open{ display:flex; }
  .lightbox img{ max-width:100%; max-height:100%; border-radius:8px; }
</style>
</head>
<body>
<div class="app">

  <aside class="sidebar" id="sidebar">
    <div class="sidebar-logo"><span class="logo-mark"></span><span class="logo-text">NEST.PH</span></div>
    <div class="sidebar-section-label">Tenant View</div>
    <ul class="nav-list">
      <li class="nav-item" data-href="{{ route('dashboard') }}" tabindex="0"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></span><span class="label">Tenant Dashboard</span></li>
      <li class="nav-item active" data-href="{{ route('tenant.tickets') }}" tabindex="0"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h18v10H3z"/><path d="M3 12h18"/></svg></span><span class="label">Tickets</span></li>
      <li class="nav-item" data-href="{{ route('tenant.billing') }}" tabindex="0"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></span><span class="label">Billing and Payments</span></li>
      <li class="nav-item" data-href="{{ route('tenant.account') }}" tabindex="0"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></span><span class="label">Profile</span></li>
      <li class="nav-item" data-href="{{ route('tenant.delinquency') }}" tabindex="0"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg></span><span class="label">Delinquency</span></li>
    </ul>
    <div class="sidebar-footer"><div class="nav-item" id="logoutBtn" tabindex="0"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.4 5.6a9 9 0 11-12.8 0M12 3v8"/></svg></span><span class="label">Log Out</span></div></div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div class="hamburger-icon" id="hamburgerBtn" tabindex="0" aria-label="Toggle sidebar"><svg width="20" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg></div>
      <div class="topbar-right">
        <span class="topbar-username">{{ $tenant->full_name ?? '' }}</span>
        <div class="topbar-icon" data-href="{{ route('tenant.account') }}" tabindex="0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
      </div>
    </div>

    <div class="content">
      <div class="page-head">
        <div class="back-arrow" data-href="{{ route('dashboard') }}" tabindex="0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
        <div class="page-head-text">
          <h1>Tickets</h1>
          <p>Submit and manage concerns. We are here to help!</p>
        </div>
        <button class="new-ticket-btn" id="openNewTicketBtn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>New Ticket</button>
      </div>

      <div class="restricted-banner {{ $portalRestricted ? 'visible' : '' }}">
        Ticket submission is unavailable while your account has an outstanding balance. <a href="{{ route('tenant.billing') }}">Settle your balance</a> to continue.
      </div>

      <div class="ticket-list" id="ticketList"></div>
    </div>
  </div>
</div>

<div class="modal-overlay" id="newTicketModal">
  <div class="modal-box">
    <div id="formState">
      <h2>Add New Ticket</h2>
      <div class="modal-error" id="formError"></div>

      <div class="fld">
        <label for="concernType">Concern Type</label>
        <select id="concernType">
          <option value="">Select concern type...</option>
          @foreach($categories as $key => $label)
            <option value="{{ $key }}">{{ $label }}</option>
          @endforeach
        </select>
      </div>

      <div class="fld">
        <label for="roomDisplay">Room</label>
        <input type="text" id="roomDisplay" value="{{ $roomNo ?? '—' }}" readonly>
      </div>

      <div class="fld">
        <label for="subjectInput">Subject</label>
        <input type="text" id="subjectInput" placeholder="Enter concern....">
      </div>

      <div class="fld">
        <label for="detailsInput">Details</label>
        <textarea id="detailsInput" placeholder="Enter details....."></textarea>
      </div>

      <div class="fld">
        <label>Photos (optional)</label>
        <div class="photo-grid" id="photoGrid"></div>
        <div class="photo-hint" id="photoHint">Up to {{ $maxAttachments }} photos, {{ $maxAttachments }} max, 5MB each.</div>
        <input type="file" id="photoInput" accept=".jpg,.jpeg,.png,.webp" multiple style="display:none;">
      </div>

      <div class="modal-actions">
        <button class="btn-cancel" id="cancelTicketBtn">Cancel</button>
        <button class="btn-submit" id="submitTicketBtn">Submit</button>
      </div>
    </div>

    <div class="success-state" id="successState">
      <svg class="success-badge" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M32 2 L37.5 7.5 L45 5 L47.5 12.5 L55 15 L52.5 22.5 L58 28 L52.5 33.5 L55 41 L47.5 43.5 L45 51 L37.5 48.5 L32 54 L26.5 48.5 L19 51 L16.5 43.5 L9 41 L11.5 33.5 L6 28 L11.5 22.5 L9 15 L16.5 12.5 L19 5 L26.5 7.5 Z" fill="#5ea86a"/>
        <path d="M21 32 L28 39 L43 24" stroke="#fff" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
      </svg>
      <h2 id="successHeading">Ticket Submitted!</h2>
      <p id="successMessage">An administrator will review your ticket shortly.</p>
      <button id="successDoneBtn">Done</button>
    </div>
  </div>
</div>

<div class="lightbox" id="lightbox"><img id="lightboxImg" alt=""></div>

<div class="toast" id="toast"></div>

<script type="application/json" id="tickets-data">{!! json_encode($tickets) !!}</script>

<script>
(function(){
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  let tickets = JSON.parse(document.getElementById('tickets-data').textContent);
  const portalRestricted = {{ $portalRestricted ? 'true' : 'false' }};
  const MAX_ATTACHMENTS = {{ (int) $maxAttachments }};

  const $ = id => document.getElementById(id);

  document.querySelectorAll('[data-href]').forEach(el => {
    el.addEventListener('click', () => { window.location.href = el.dataset.href; });
    el.addEventListener('keydown', (e) => { if(e.key === 'Enter' || e.key === ' '){ e.preventDefault(); window.location.href = el.dataset.href; } });
  });

  const logoutBtn = $('logoutBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', async () => {
      await fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf } });
      window.location.href = '/';
    });
  }

  const SIDEBAR_COLLAPSE_KEY = 'nestph_sidebar_collapsed';
  const hamburgerBtn = $('hamburgerBtn');
  const sidebar = $('sidebar');
  if (hamburgerBtn && sidebar) {
    if (localStorage.getItem(SIDEBAR_COLLAPSE_KEY) === '1') sidebar.classList.add('collapsed');
    hamburgerBtn.addEventListener('click', () => {
      const collapsed = sidebar.classList.toggle('collapsed');
      localStorage.setItem(SIDEBAR_COLLAPSE_KEY, collapsed ? '1' : '0');
    });
  }

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

  async function api(url, options = {}){
    const headers = Object.assign({ 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' }, options.headers || {});
    const res = await fetch(url, Object.assign({}, options, { headers }));
    const body = await res.json().catch(() => ({}));
    if(!res.ok){
      throw new Error(body.message || (body.errors ? Object.values(body.errors)[0][0] : `Request failed (${res.status})`));
    }
    return body;
  }

  function initials(name){
    return (name || '?').trim().split(/\s+/).map(w => w[0]).slice(0,2).join('').toUpperCase();
  }

  $('lightbox').addEventListener('click', () => $('lightbox').classList.remove('open'));

  function renderList(){
    if(tickets.length === 0){
      $('ticketList').innerHTML = '<div class="empty">You have not submitted any tickets yet.</div>';
      return;
    }

    $('ticketList').innerHTML = tickets.map(t => `
      <div class="ticket-card" data-ticket="${t.id}">
        <div class="tc-head">
          <div>
            <h3 class="tc-title">#${String(t.id).padStart(3,'0')} — ${esc(t.title)}</h3>
            <div class="tc-sub">Submitted: ${esc(t.submitted_at)}</div>
            <span class="tc-cat">${esc(t.category_label)}</span>
          </div>
          <span class="badge ${t.status}">${esc(t.status_label)}</span>
        </div>
        ${t.attachment_urls && t.attachment_urls.length ? `
          <div class="tc-gallery">
            ${t.attachment_urls.map(u => `<img src="${u}" alt="Attachment" data-lightbox="${u}">`).join('')}
          </div>
        ` : ''}
        <div class="thread">
          ${t.messages.map(m => `
            <div class="msg ${m.is_admin ? 'admin' : ''}">
              <div class="msg-avatar">${initials(m.author)}</div>
              <div class="msg-body">
                <span class="msg-name">${esc(m.author)}<span class="msg-time">${esc(m.created_at)}</span></span>
                <div class="msg-text">${esc(m.message)}</div>
              </div>
            </div>
          `).join('')}
        </div>
        <div class="reply-row">
          <input type="text" placeholder="Add a reply..." data-reply-input="${t.id}">
          <button data-reply-submit="${t.id}">Submit</button>
        </div>
      </div>
    `).join('');

    document.querySelectorAll('[data-lightbox]').forEach(img => {
      img.addEventListener('click', () => {
        $('lightboxImg').src = img.dataset.lightbox;
        $('lightbox').classList.add('open');
      });
    });

    document.querySelectorAll('[data-reply-submit]').forEach(btn => {
      btn.addEventListener('click', async () => {
        const id = btn.dataset.replySubmit;
        const input = document.querySelector(`[data-reply-input="${id}"]`);
        const message = input.value.trim();
        if(!message) return;

        btn.disabled = true;
        try {
          const res = await api(`/my/tickets/${id}/reply`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message }),
          });
          const idx = tickets.findIndex(t => t.id === Number(id));
          if(idx !== -1) tickets[idx] = res.ticket;
          renderList();
          toast('Reply sent.');
        } catch(e){ toast(e.message, true); }
        btn.disabled = false;
      });
    });
  }

  // ===== New Ticket modal: photo grid =====
  let selectedFiles = [];

  function renderPhotoGrid(){
    const grid = $('photoGrid');
    const tiles = selectedFiles.map((entry, i) => `
      <div class="photo-tile" data-index="${i}">
        <img src="${entry.preview}" alt="">
        <button type="button" class="remove-tile" data-remove-photo="${i}">&times;</button>
      </div>
    `).join('');

    const addTile = selectedFiles.length < MAX_ATTACHMENTS ? `
      <button type="button" class="add-tile" id="addPhotoTile">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 5v14M5 12h14"/></svg>
        <span>Add photo</span>
      </button>
    ` : '';

    grid.innerHTML = tiles + addTile;

    const addBtn = $('addPhotoTile');
    if(addBtn) addBtn.addEventListener('click', () => $('photoInput').click());

    document.querySelectorAll('[data-remove-photo]').forEach(btn => {
      btn.addEventListener('click', () => {
        selectedFiles.splice(Number(btn.dataset.removePhoto), 1);
        renderPhotoGrid();
      });
    });

    $('photoHint').textContent = selectedFiles.length >= MAX_ATTACHMENTS
      ? `Maximum of ${MAX_ATTACHMENTS} photos reached.`
      : `Up to ${MAX_ATTACHMENTS} photos, ${MAX_ATTACHMENTS}MB max, 5MB each.`.replace(`${MAX_ATTACHMENTS}MB max`, '5MB max each');
  }

  $('photoInput').addEventListener('change', () => {
    const incoming = Array.from($('photoInput').files);
    const room = MAX_ATTACHMENTS - selectedFiles.length;

    if(incoming.length > room){
      toast(`Only ${room} more photo(s) can be added (max ${MAX_ATTACHMENTS}).`, true);
    }

    incoming.slice(0, room).forEach(file => {
      const reader = new FileReader();
      reader.onload = (e) => {
        selectedFiles.push({ file, preview: e.target.result });
        renderPhotoGrid();
      };
      reader.readAsDataURL(file);
    });

    $('photoInput').value = '';
  });

  function resetForm(){
    $('concernType').value = '';
    $('subjectInput').value = '';
    $('detailsInput').value = '';
    $('photoInput').value = '';
    selectedFiles = [];
    renderPhotoGrid();
    $('formError').classList.remove('visible');
    $('formState').style.display = 'block';
    $('successState').classList.remove('active');
  }

  $('openNewTicketBtn').addEventListener('click', () => {
    if(portalRestricted){
      toast('Settle your outstanding balance to submit a new ticket.', true);
      return;
    }
    resetForm();
    $('newTicketModal').classList.add('open');
  });

  $('cancelTicketBtn').addEventListener('click', () => $('newTicketModal').classList.remove('open'));
  $('newTicketModal').addEventListener('click', (e) => { if(e.target.id === 'newTicketModal') $('newTicketModal').classList.remove('open'); });

  $('submitTicketBtn').addEventListener('click', async function(){
    const category = $('concernType').value;
    const title = $('subjectInput').value.trim();
    const description = $('detailsInput').value.trim();

    function showFormError(msg){
      const el = $('formError');
      el.textContent = msg;
      el.classList.add('visible');
    }

    if(!category){ return showFormError('Please select a concern type.'); }
    if(!title){ return showFormError('Please enter a subject.'); }
    if(!description){ return showFormError('Please enter details about your concern.'); }

    $('formError').classList.remove('visible');
    this.disabled = true;

    const fd = new FormData();
    fd.append('category', category);
    fd.append('title', title);
    fd.append('description', description);
    selectedFiles.forEach(entry => fd.append('attachment[]', entry.file));

    try {
      const res = await api('/my/tickets', { method: 'POST', body: fd });
      tickets.unshift(res.ticket);
      renderList();

      $('successMessage').textContent = res.message;
      $('formState').style.display = 'none';
      $('successState').classList.add('active');
    } catch(e){ showFormError(e.message); }
    this.disabled = false;
  });

  $('successDoneBtn').addEventListener('click', () => $('newTicketModal').classList.remove('open'));

  renderList();
})();
</script>
</body>
</html>