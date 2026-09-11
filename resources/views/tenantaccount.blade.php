<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH — My Profile</title>
<style>
  :root{
    --green-dark:#3f6b4a; --green-darker:#345a3e; --green-mid:#4f7c57;
    --green-sidebar-top:#33513c; --green-sidebar-bottom:#223a29;
    --green-accent:#3f6b4a; --green-btn:#3f6b4a; --green-btn-hover:#2f5439;
    --logout-bg:#16241b; --logout-bg-hover:#0f1b13;
    --bg-page:#f4f6f4; --card-bg:#ffffff;
    --text-dark:#1f2a22; --text-mid:#5b6b60; --text-light:#8a9690; --border:#e5e9e4;
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

  .nav-list{
    list-style:none; margin:0; padding:0 0 8px 0; flex:1; min-height:0;
    overflow-y:auto; overflow-x:hidden;
    scrollbar-width:thin; scrollbar-color:rgba(255,255,255,0.25) transparent;
  }
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

  .nav-item:focus-visible, .hamburger-icon:focus-visible, .topbar-icon:focus-visible, .back-arrow:focus-visible{
    outline:2px solid #ffffff; outline-offset:-2px; border-radius:4px;
  }
  .topbar-icon:focus-visible{ outline-color:var(--green-darker); }

  .main{ flex:1; display:flex; flex-direction:column; min-width:0; }
  .topbar{
    display:flex; align-items:center; gap:16px;
    background:linear-gradient(90deg, rgba(51,81,60,0.45), rgba(63,107,74,0.45));
    backdrop-filter:blur(16px) saturate(140%); -webkit-backdrop-filter:blur(16px) saturate(140%);
    padding:16px 28px;
    box-shadow:0 1px 0 rgba(0,0,0,0.08);
    position:sticky; top:0; z-index:20;
  }
  .hamburger-icon{ width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; cursor:pointer; transition:background 0.12s ease; }
  .hamburger-icon:hover{ background:rgba(255,255,255,0.14); }
  .topbar-right{ margin-left:auto; display:flex; align-items:center; gap:14px; }
  .topbar-username{ color:#fff; font-size:13.5px; font-weight:600; white-space:nowrap; text-shadow:0 1px 2px rgba(0,0,0,0.15); }
  .topbar-icon{ width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.92); display:flex; align-items:center; justify-content:center; color:var(--green-dark); cursor:pointer; }
  .topbar-icon svg{ width:16px; height:16px; }

  .content{ padding:26px 34px 48px 34px; flex:1; max-width:1180px; }
  .page-head{ display:flex; align-items:flex-start; gap:12px; margin-bottom:22px; }
  .back-arrow{ width:30px; height:30px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-mid); flex-shrink:0; margin-top:2px; }
  .page-head-text h1{ font-size:19px; font-weight:700; margin:0; color:var(--green-accent); }
  .page-head-text p{ font-size:12.5px; color:var(--text-mid); margin:2px 0 0 0; }

  .tabs-row{ display:flex; gap:8px; margin-bottom:20px; }
  .tab-pill{ padding:9px 18px; border-radius:20px; font-size:12.5px; font-weight:600; cursor:pointer; border:1px solid var(--border); background:#fff; color:var(--text-mid); }
  .tab-pill.active{ background:var(--green-dark); color:#fff; border-color:var(--green-dark); }

  .tab-panel{ display:none; }
  .tab-panel.active{ display:block; }

  .settings-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:14px; padding:28px 30px; max-width:640px; }
  .settings-card h2{ font-size:16px; font-weight:700; margin:0 0 6px 0; color:var(--text-dark); }
  .settings-card .intro{ font-size:12.5px; color:var(--text-mid); line-height:1.6; margin-bottom:24px; }

  /* ===== Profile tab ===== */
  .request-edit-banner{
    display:flex; align-items:center; gap:14px; justify-content:space-between;
    background:#eaf3ec; border:1px solid #cfe3d2; border-radius:12px;
    padding:14px 18px; margin-bottom:22px; max-width:640px;
  }
  .request-edit-banner p{ margin:0; font-size:12.5px; color:var(--text-dark); line-height:1.5; }
  .request-edit-banner p strong{ color:var(--green-accent); }
  .request-edit-btn{
    flex-shrink:0; background:var(--green-btn); color:#fff; border:none; border-radius:8px;
    padding:9px 16px; font-size:12.5px; font-weight:700; cursor:pointer; white-space:nowrap;
    text-decoration:none; display:inline-block; transition:background 0.15s ease;
  }
  .request-edit-btn:hover{ background:var(--green-btn-hover); }

  .profile-section{ margin-bottom:26px; }
  .profile-section:last-child{ margin-bottom:0; }
  .profile-section-title{
    font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.04em;
    color:var(--green-accent); margin:0 0 14px 0; padding-bottom:8px; border-bottom:1px solid var(--border);
  }
  .info-grid{ display:grid; grid-template-columns:repeat(2, 1fr); gap:16px 28px; }
  .info-item label{ display:block; font-size:11px; text-transform:uppercase; letter-spacing:0.03em; color:var(--text-light); font-weight:600; margin-bottom:4px; }
  .info-item .value{ font-size:14px; color:var(--text-dark); font-weight:500; }
  .info-item .value.muted{ color:var(--text-light); font-weight:400; font-style:italic; }

  .lease-actions{ margin-top:16px; display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
  .btn-outline-sm{
    display:inline-flex; align-items:center; gap:6px; border:1px solid var(--green-dark); color:var(--green-dark);
    background:#fff; border-radius:8px; padding:8px 14px; font-size:12.5px; font-weight:700;
    text-decoration:none; cursor:pointer; transition:background 0.15s ease, color 0.15s ease;
  }
  .btn-outline-sm:hover{ background:var(--green-dark); color:#fff; }
  .muted-note{ font-size:12px; color:var(--text-light); margin:0; line-height:1.5; }

  /* ===== Change Password tab ===== */
  .pw-form-group{ margin-bottom:20px; }
  .pw-form-group label{ display:block; font-size:12.5px; font-weight:600; color:var(--green-accent); margin-bottom:7px; }
  .pw-input-wrap{ position:relative; }
  .pw-form-group input{ width:100%; border:none; border-bottom:1px solid var(--border); padding:8px 32px 8px 2px; font-size:14px; background:transparent; color:var(--text-dark); outline:none; font-family:inherit; }
  .pw-form-group input:focus{ border-bottom-color:var(--green-dark); }
  .pw-toggle{ position:absolute; right:2px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--text-light); padding:4px; display:flex; }
  .pw-toggle:hover{ color:var(--green-dark); }
  .pw-toggle svg{ width:16px; height:16px; }

  .field-error{ color:#b3261e; font-size:12px; margin-top:6px; display:none; }
  .field-error.visible{ display:block; }
  .pw-hint{ font-size:11px; color:var(--text-light); margin-top:-10px; margin-bottom:20px; }

  .form-banner{ display:none; align-items:flex-start; gap:8px; padding:12px 14px; border-radius:8px; font-size:12.5px; margin-bottom:20px; line-height:1.5; }
  .form-banner.visible{ display:flex; }
  .form-banner.success{ background:#d9f2dd; color:#2f6f3c; }
  .form-banner.error{ background:#f7d9d7; color:#a3372e; }

  .pw-submit-btn{ background:var(--green-btn); color:#fff; border:none; border-radius:8px; padding:13px 0; width:100%; font-weight:700; font-size:13.5px; letter-spacing:0.03em; cursor:pointer; margin-top:4px; transition:background 0.15s ease; }
  .pw-submit-btn:hover:not(:disabled){ background:var(--green-btn-hover); }
  .pw-submit-btn:disabled{ opacity:0.7; cursor:not-allowed; }
  .pw-submit-btn .spinner{ display:none; width:13px; height:13px; border:2px solid rgba(255,255,255,0.35); border-top:2px solid #fff; border-radius:50%; animation:pwspin 0.8s linear infinite; margin-right:8px; vertical-align:-2px; }
  .pw-submit-btn.loading .spinner{ display:inline-block; }
  @keyframes pwspin{ to{ transform:rotate(360deg); } }

  @media (max-width: 640px){
    .info-grid{ grid-template-columns:1fr; }
    .request-edit-banner{ flex-direction:column; align-items:flex-start; }
  }
</style>
</head>
<body>
<div class="app">

  <aside class="sidebar" id="sidebar">
    <div class="sidebar-logo"><span class="logo-mark"></span><span class="logo-text">NEST.PH</span></div>
    <div class="sidebar-section-label">Tenant View</div>
    <ul class="nav-list">
      <li class="nav-item" data-href="{{ route('dashboard') }}" tabindex="0"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></span><span class="label">Tenant Dashboard</span></li>
      <li class="nav-item" data-href="{{ route('tenant.tickets') }}" tabindex="0"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h18v10H3z"/><path d="M3 12h18"/></svg></span><span class="label">Tickets</span></li>
      <li class="nav-item" data-href="{{ route('tenant.billing') }}" tabindex="0"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></span><span class="label">Billing and Payments</span></li>
      <li class="nav-item active" data-href="{{ route('tenant.account') }}" tabindex="0"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></span><span class="label">Profile</span></li>
      <li class="nav-item" data-href="{{ route('tenant.delinquency') }}" tabindex="0"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg></span><span class="label">Delinquency</span></li>
    </ul>
    <div class="sidebar-footer"><div class="nav-item" id="logoutBtn" tabindex="0"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.4 5.6a9 9 0 11-12.8 0M12 3v8"/></svg></span><span class="label">Log Out</span></div></div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div class="hamburger-icon" id="hamburgerBtn" tabindex="0" aria-label="Toggle sidebar"><svg width="20" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg></div>
      <div class="topbar-right">
        <span class="topbar-username">{{ $tenant->full_name ?? 'Tenant' }}</span>
        <div class="topbar-icon" data-href="{{ route('tenant.account') }}" tabindex="0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
      </div>
    </div>

    <div class="content">
      <div class="page-head">
        <div class="back-arrow" data-href="{{ route('dashboard') }}" tabindex="0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
        <div class="page-head-text">
          <h1>My Profile</h1>
          <p>{{ $tenant->full_name ?? 'Tenant' }}@if($contract?->bed?->room?->room_no) &middot; Room {{ $contract->bed->room->room_no }}@endif</p>
        </div>
      </div>

      <div class="tabs-row">
        <div class="tab-pill active" data-panel="profilePanel">Profile</div>
        <div class="tab-pill" data-panel="passwordPanel">Change Password</div>
      </div>

      {{-- ===== PROFILE TAB ===== --}}
      <div class="tab-panel active" id="profilePanel">

        <div class="request-edit-banner">
          <p>This information is view-only. <strong>Spotted something wrong, or need it updated?</strong> Submit a ticket and an admin will make the change for you.</p>
          <a href="{{ route('tenant.tickets') }}" class="request-edit-btn">Submit a Ticket</a>
        </div>

        <div class="settings-card">

          <div class="profile-section">
            <h3 class="profile-section-title">Personal Information</h3>
            <div class="info-grid">
              <div class="info-item">
                <label>Full Name</label>
                <div class="value">{{ $tenant->full_name ?? '—' }}</div>
              </div>
              <div class="info-item">
                <label>Email</label>
                <div class="value">{{ $tenant->email ?? '—' }}</div>
              </div>
              <div class="info-item">
                <label>Contact Number</label>
                <div class="value">{{ $tenant->contact_number ?? '—' }}</div>
              </div>
              <div class="info-item">
                <label>Date of Birth</label>
                <div class="value">{{ $tenant->date_of_birth ? \Carbon\Carbon::parse($tenant->date_of_birth)->format('M j, Y') : '—' }}</div>
              </div>
              <div class="info-item">
                <label>Home Address</label>
                <div class="value">{{ $tenant->home_address ?? '—' }}</div>
              </div>
              <div class="info-item">
                <label>Tenant Type</label>
                <div class="value">{{ $tenant->tenant_type ? ucwords(str_replace('_', ' ', $tenant->tenant_type)) : '—' }}</div>
              </div>
            </div>
          </div>

          <div class="profile-section">
            <h3 class="profile-section-title">Emergency Contact</h3>
            <div class="info-grid">
              <div class="info-item">
                <label>Name</label>
                <div class="value">{{ $tenant->emergency_contact_name ?? '—' }}</div>
              </div>
              <div class="info-item">
                <label>Contact Number</label>
                <div class="value">{{ $tenant->emergency_contact_number ?? '—' }}</div>
              </div>
            </div>
          </div>

          <div class="profile-section">
            <h3 class="profile-section-title">Room &amp; Lease</h3>
            <div class="info-grid">
              <div class="info-item">
                <label>Room</label>
                <div class="value">{{ $contract?->bed?->room?->room_no ?? '—' }}</div>
              </div>
              <div class="info-item">
                <label>Bed</label>
                <div class="value">{{ $contract?->bed?->bed_label ?? '—' }}</div>
              </div>
              <div class="info-item">
                <label>Lease Start</label>
                <div class="value">{{ $contract?->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('M j, Y') : '—' }}</div>
              </div>
              <div class="info-item">
                <label>Lease End</label>
                <div class="value">{{ $contract?->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('M j, Y') : '—' }}</div>
              </div>
              <div class="info-item">
                <label>Monthly Rate</label>
                <div class="value">{{ $contract ? '₱' . number_format($contract->monthly_rate, 2) : '—' }}</div>
              </div>
            </div>

            <div class="lease-actions">
              @if($contractDocumentUrl)
                <a href="{{ $contractDocumentUrl }}" target="_blank" rel="noopener" class="btn-outline-sm">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
                  View Lease Contract
                </a>
              @else
                <p class="muted-note">Your signed lease contract hasn't been uploaded to your account yet. If you believe this is a mistake, submit a ticket to ask the admin about it.</p>
              @endif
            </div>
          </div>

        </div>
      </div>

      {{-- ===== CHANGE PASSWORD TAB ===== --}}
      <div class="tab-panel" id="passwordPanel">
        <div class="settings-card">
          <h2>Change Password</h2>
          <p class="intro">Update the password you use to log in. Choose something you don't use anywhere else.</p>

          <div class="form-banner" id="formBanner"></div>

          <form id="changePasswordForm" autocomplete="off">
            <div class="pw-form-group">
              <label for="currentPassword">Current Password</label>
              <div class="pw-input-wrap">
                <input id="currentPassword" type="password" placeholder="Enter current password" autocomplete="current-password" required>
                <button type="button" class="pw-toggle" data-target="currentPassword" aria-label="Show password"></button>
              </div>
              <div class="field-error" id="currentPasswordError"></div>
            </div>

            <div class="pw-form-group">
              <label for="newPassword">New Password</label>
              <div class="pw-input-wrap">
                <input id="newPassword" type="password" placeholder="Enter new password" autocomplete="new-password" minlength="8" required>
                <button type="button" class="pw-toggle" data-target="newPassword" aria-label="Show password"></button>
              </div>
              <div class="field-error" id="newPasswordError"></div>
            </div>
            <div class="pw-hint">At least 8 characters.</div>

            <div class="pw-form-group">
              <label for="confirmPassword">Confirm New Password</label>
              <div class="pw-input-wrap">
                <input id="confirmPassword" type="password" placeholder="Re-enter new password" autocomplete="new-password" minlength="8" required>
                <button type="button" class="pw-toggle" data-target="confirmPassword" aria-label="Show password"></button>
              </div>
              <div class="field-error" id="confirmPasswordError"></div>
            </div>

            <button type="submit" class="pw-submit-btn" id="submitBtn">
              <span class="spinner"></span>
              <span class="btn-text">Update Password</span>
            </button>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
(function(){
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

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

  document.querySelectorAll('[tabindex="0"]').forEach(el => {
    el.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        el.click();
      }
    });
  });

  // ===== Tabs (Profile / Change Password) =====
  document.querySelectorAll('.tab-pill').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.tab-pill').forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
      const panel = document.getElementById(tab.dataset.panel);
      if (panel) panel.classList.add('active');
    });
  });

  // ===== Change Password form =====
  const EYE_OPEN = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>';
  const EYE_CLOSED = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.94 10.94 0 0112 19c-7 0-11-7-11-7a20.3 20.3 0 015.06-5.94M9.9 4.24A10.94 10.94 0 0112 4c7 0 11 7 11 7a20.3 20.3 0 01-3.22 4.39M14.12 14.12a3 3 0 11-4.24-4.24"/><path d="M1 1l22 22"/></svg>';

  document.querySelectorAll('.pw-toggle').forEach(btn => {
    btn.innerHTML = EYE_OPEN;
    btn.addEventListener('click', () => {
      const input = document.getElementById(btn.dataset.target);
      const showing = input.type === 'text';
      input.type = showing ? 'password' : 'text';
      btn.innerHTML = showing ? EYE_OPEN : EYE_CLOSED;
    });
  });

  const form = document.getElementById('changePasswordForm');
  const currentInput = document.getElementById('currentPassword');
  const newInput = document.getElementById('newPassword');
  const confirmInput = document.getElementById('confirmPassword');
  const currentError = document.getElementById('currentPasswordError');
  const newError = document.getElementById('newPasswordError');
  const confirmError = document.getElementById('confirmPasswordError');
  const banner = document.getElementById('formBanner');
  const submitBtn = document.getElementById('submitBtn');

  function clearErrors(){
    [currentError, newError, confirmError].forEach(el => { el.textContent = ''; el.classList.remove('visible'); });
    banner.classList.remove('visible', 'success', 'error');
    banner.textContent = '';
  }

  function showFieldError(el, msg){
    el.textContent = msg;
    el.classList.add('visible');
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearErrors();

    if (newInput.value !== confirmInput.value) {
      showFieldError(confirmError, 'New password and confirmation do not match.');
      return;
    }

    submitBtn.disabled = true;
    submitBtn.classList.add('loading');

    try {
      const res = await fetch("{{ route('password.update') }}", {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({
          current_password: currentInput.value,
          password: newInput.value,
          password_confirmation: confirmInput.value,
        }),
      });

      if (res.ok) {
        banner.textContent = 'Your password has been updated.';
        banner.classList.add('visible', 'success');
        form.reset();
        document.querySelectorAll('.pw-toggle').forEach(btn => { btn.innerHTML = EYE_OPEN; });
      } else if (res.status === 422) {
        const data = await res.json();
        const errors = data.errors || {};
        let handled = false;
        if (errors.current_password) { showFieldError(currentError, errors.current_password[0]); handled = true; }
        if (errors.password) { showFieldError(newError, errors.password[0]); handled = true; }
        if (!handled) {
          banner.textContent = data.message || 'Something went wrong. Please try again.';
          banner.classList.add('visible', 'error');
        }
      } else {
        let data = {};
        try { data = await res.json(); } catch (e) { /* not JSON, ignore */ }
        banner.textContent = data.message || `Something went wrong (error ${res.status}). Please try again.`;
        banner.classList.add('visible', 'error');
      }
    } catch (err) {
      banner.textContent = 'Network error. Please check your connection and try again.';
      banner.classList.add('visible', 'error');
    }

    submitBtn.disabled = false;
    submitBtn.classList.remove('loading');
  });
})();
</script>

</body>
</html>