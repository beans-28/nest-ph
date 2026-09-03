<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH — Delinquency Status</title>
<style>
  :root{
    --green-dark:#3f6b4a; --green-darker:#345a3e; --green-mid:#4f7c57;
    --green-sidebar-top:#33513c; --green-sidebar-bottom:#223a29;
    --green-accent:#3f6b4a; --green-btn:#3f6b4a; --green-btn-hover:#2f5439;
    --logout-bg:#16241b; --logout-bg-hover:#0f1b13;
    --bg-page:#f4f6f4; --card-bg:#ffffff;
    --text-dark:#1f2a22; --text-mid:#5b6b60; --text-light:#8a9690; --border:#e5e9e4;
    --pending-bg:#fbe9c8; --pending-text:#a4761a;
    --paid-bg:#d9f2dd; --paid-text:#3f7a4a;
    --font-body: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  }
  *{box-sizing:border-box;}
  html,body{ margin:0; padding:0; font-family:var(--font-body); background:var(--bg-page); color:var(--text-dark); }
  .app{ display:flex; min-height:100vh; }

  /* ===== Sidebar: same shell as tenantdashboard/tenantbilling/tenantaccount ===== */
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
  .topbar{ display:flex; align-items:center; gap:16px; background:linear-gradient(90deg, rgba(51,81,60,0.45), rgba(63,107,74,0.45)); backdrop-filter:blur(16px) saturate(140%); -webkit-backdrop-filter:blur(16px) saturate(140%); padding:16px 28px; box-shadow:0 1px 0 rgba(0,0,0,0.08); position:sticky; top:0; z-index:20; }
  .hamburger-icon{ width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; cursor:pointer; transition:background 0.12s ease; }
  .hamburger-icon:hover{ background:rgba(255,255,255,0.14); }
  .topbar-right{ margin-left:auto; display:flex; align-items:center; gap:14px; }
  .topbar-username{ color:#fff; font-size:13.5px; font-weight:600; white-space:nowrap; text-shadow:0 1px 2px rgba(0,0,0,0.15); }
  .topbar-icon{ width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.92); display:flex; align-items:center; justify-content:center; color:var(--green-dark); cursor:pointer; }
  .topbar-icon svg{ width:16px; height:16px; }

  .content{ padding:26px 34px 48px 34px; flex:1; max-width:1180px; }
  .page-head{ display:flex; align-items:center; gap:12px; margin-bottom:22px; }
  .back-arrow{ width:30px; height:30px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-mid); flex-shrink:0; }
  .page-head h1{ font-size:19px; font-weight:700; margin:0; color:var(--green-accent); }
  .page-head p{ font-size:12.5px; color:var(--text-mid); margin:2px 0 0 0; }

  .stage-banner{ font-size:22px; font-weight:800; color:#c9962f; letter-spacing:0.3px; margin:2px 0 10px 0; }
  .stage-divider{ height:1px; background:linear-gradient(90deg, var(--green-dark), transparent); margin-bottom:22px; }

  /* ===== Portal-restricted takeover: replaces the normal sidebar nav
     with a non-navigable lock panel while portal_restricted is true, so
     a restricted tenant isn't shown links that would just bounce them
     back anyway. Log Out stays available -- restriction shouldn't trap
     someone in a session they want to end. ===== */
  .sidebar.restricted-lock{ align-items:center; }
  .lock-panel-body{ flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:24px 22px; text-align:center; }
  .lock-icon-circle{ width:80px; height:80px; border-radius:50%; background:#c0463d; display:flex; align-items:center; justify-content:center; margin-bottom:24px; box-shadow:0 4px 10px rgba(0,0,0,0.25); flex-shrink:0; }
  .lock-icon-circle svg{ width:36px; height:36px; color:#fff; }
  .lock-panel-title{ color:#fff; font-size:18px; font-weight:800; margin:0 0 10px 0; line-height:1.3; letter-spacing:0.2px; }
  .lock-panel-text{ color:rgba(255,255,255,0.85); font-size:12px; line-height:1.65; max-width:200px; }

  /* ===== Warning banners ===== */
  .warning-banner{ border-radius:14px; padding:16px 22px; margin-bottom:20px; }
  .warning-banner strong{ display:block; font-size:14px; margin-bottom:3px; }
  .warning-banner span{ font-size:12.5px; }
  .warning-banner.restricted{ background:#fdeee9; border:1px solid #f3c3b3; }
  .warning-banner.restricted strong{ color:#c0463d; }
  .warning-banner.restricted span{ color:#8a5347; }
  .warning-banner.blacklist{ background:#eceaea; border:1px solid #c7c3c3; }
  .warning-banner.blacklist strong{ color:#1f2a22; }
  .warning-banner.blacklist span{ color:#5b6b60; }

  /* ===== Good-standing placeholder ===== */
  .good-standing-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:16px; padding:56px 32px; text-align:center; }
  .good-standing-icon{ width:64px; height:64px; border-radius:50%; background:#eaf5eb; display:flex; align-items:center; justify-content:center; margin:0 auto 18px auto; }
  .good-standing-icon svg{ width:30px; height:30px; color:var(--green-dark); }
  .good-standing-card h2{ font-size:17px; margin:0 0 8px 0; color:var(--text-dark); }
  .good-standing-card p{ font-size:13px; color:var(--text-mid); max-width:340px; margin:0 auto; line-height:1.6; }

  /* ===== Escalation cards ===== */
  .top-grid{ display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:20px; }
  .balance-card{ background:linear-gradient(135deg, var(--green-dark), var(--green-darker)); border-radius:14px; padding:22px 24px; color:#fff; }
  .balance-label{ font-size:11.5px; font-weight:700; letter-spacing:0.6px; opacity:0.85; margin-bottom:8px; }
  .balance-amount{ font-size:32px; font-weight:800; margin-bottom:6px; }
  .balance-due{ font-size:12px; opacity:0.85; }

  /* Balance card grows a right-side action column once portal_restricted
     is true, staying visible for the whole restricted period -- not just
     while Stage 3 itself is the current stage. */
  .balance-card.with-actions{ display:flex; align-items:center; justify-content:space-between; gap:20px; flex-wrap:wrap; }
  .balance-card-actions{ display:flex; flex-direction:column; gap:10px; flex-shrink:0; }
  .balance-card-actions .pay-now-pill, .balance-card-actions .contact-admin-pill{ padding:12px 22px; }
  /* On the green balance card specifically, the plain dark-green pill
     blends into the card's own background -- swap it to a white/outline
     style there so it stays legible against green. */
  .balance-card-actions .contact-admin-pill{ background:rgba(255,255,255,0.95); color:var(--green-darker); }
  .balance-card-actions .contact-admin-pill:hover{ background:#fff; }

  .breakdown-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:14px; padding:20px 22px; }
  .breakdown-card h3{ font-size:13px; font-weight:700; margin:0 0 14px 0; letter-spacing:0.3px; color:var(--text-dark); }
  .breakdown-line{ display:flex; justify-content:space-between; font-size:13px; color:var(--text-dark); padding:9px 0; border-bottom:1px solid #f0f2f0; }
  .breakdown-line:last-child{ border-bottom:none; }
  .breakdown-line.penalty-line{ color:#c0463d; font-weight:700; }

  .billing-panel{ background:var(--card-bg); border:1px solid var(--border); border-radius:14px; padding:22px 24px 20px 24px; }
  .billing-panel h3{ font-size:14px; font-weight:700; margin:0 0 16px 0; letter-spacing:0.3px; color:var(--text-dark); }

  .timeline-row{ border:1px solid var(--border); border-radius:10px; padding:12px 16px; margin-bottom:10px; }
  .timeline-row:last-child{ margin-bottom:0; }
  .timeline-row-head{ display:flex; align-items:center; gap:14px; }
  .timeline-number{ width:30px; height:30px; border-radius:50%; color:#fff; font-size:13px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .timeline-info{ flex:1; min-width:0; }
  .timeline-stage-name{ font-size:13px; font-weight:700; color:var(--text-dark); }
  .timeline-date{ font-size:11.5px; color:var(--text-mid); margin-top:2px; }
  .status-pill{ font-size:11px; font-weight:700; padding:5px 14px; border-radius:20px; white-space:nowrap; }
  .status-pill.overdue{ background:#f7d9d7; color:#c0463d; }
  .status-pill.paid{ background:var(--paid-bg); color:var(--paid-text); }

  /* Expanded variant: the tenant's CURRENT stage, when that stage has an
     action attached (e.g. Stage 3's portal restriction) gets a highlighted
     panel with an explanation + relevant buttons, instead of just a plain
     row like earlier/inactive stages. */
  .timeline-row.expanded.restricted{ background:#fdeee9; border-color:#f3c3b3; padding:16px 20px; }
  .timeline-expanded-body{ margin-top:14px; padding-top:14px; border-top:1px solid rgba(0,0,0,0.08); text-align:center; }
  .timeline-expanded-body p{ font-size:13px; color:var(--text-dark); margin:0 0 16px 0; line-height:1.6; max-width:520px; margin-left:auto; margin-right:auto; }
  .timeline-expanded-actions{ display:flex; gap:12px; justify-content:center; flex-wrap:wrap; }
  .pay-now-pill{ background:#c0463d; color:#fff; border:none; border-radius:8px; padding:12px 28px; font-size:12.5px; font-weight:700; cursor:pointer; letter-spacing:0.3px; }
  .pay-now-pill:hover{ background:#a8382f; }
  .contact-admin-pill{ background:var(--green-darker); color:#fff; border-radius:8px; padding:12px 28px; font-size:12.5px; font-weight:700; cursor:pointer; letter-spacing:0.3px; text-decoration:none; display:inline-flex; align-items:center; }
  .contact-admin-pill:hover{ background:#233f29; }

  /* Stage 4's expanded row: the emergency contact's name/number + the
     actual SMS message sent to them (real data from message_content,
     never re-typed here). */
  .emergency-contact-body{ text-align:left; }
  .emergency-contact-header{ display:flex; align-items:center; gap:12px; margin-bottom:14px; flex-wrap:wrap; }
  .emergency-contact-avatar{ width:40px; height:40px; border-radius:50%; background:#f0efef; display:flex; align-items:center; justify-content:center; font-size:15px; font-weight:700; color:var(--text-dark); flex-shrink:0; box-shadow:0 2px 4px rgba(0,0,0,0.15); }
  .emergency-contact-name{ font-size:15px; font-weight:700; color:var(--text-dark); }
  .emergency-contact-number{ font-size:13px; font-weight:700; color:#c0463d; }
  .emergency-message-label{ font-size:11px; font-weight:700; letter-spacing:0.4px; color:var(--text-dark); opacity:0.7; margin-bottom:6px; }
  .emergency-message-box{ background:#fff; border:1px solid rgba(0,0,0,0.15); border-radius:10px; padding:14px 16px; font-size:12.5px; color:var(--text-dark); line-height:1.6; }

  /* Stage 5's demand letter row: grey rather than red/pink -- it's a
     document notice, not a restriction warning. */
  .timeline-row.expanded.demand-letter{ background:#e5e3e3; border-color:#c7c3c3; padding:16px 20px; }
  .demand-letter-body{ text-align:center; }
  .demand-letter-download-btn{ background:#c0463d; color:#fff; border:none; border-radius:8px; padding:11px 26px; font-size:12.5px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-block; margin-bottom:12px; letter-spacing:0.3px; }
  .demand-letter-download-btn:hover{ background:#a8382f; }
  .demand-letter-note{ font-size:11.5px; color:var(--text-dark); opacity:0.85; line-height:1.6; max-width:480px; margin:0 auto; }
  .demand-letter-footnote{ font-size:11px; font-weight:700; color:var(--green-dark); text-align:center; margin-top:14px; }

  .empty-note{ font-size:12px; color:var(--text-light); text-align:center; padding:16px 0; }

  /* ===== Stage 6 full takeover: blacklisted tenants get a completely
     different page -- no sidebar, no nav links, nothing to navigate to.
     Table 28 blacklisting is permanent, even once the balance is paid, so
     this isn't "restricted but reachable" like Stage 3+'s lock panel --
     it's final. ===== */
  .blacklist-page{ min-height:100vh; display:flex; flex-direction:column; background:linear-gradient(160deg, var(--green-sidebar-top) 0%, #2a221d 60%, var(--green-sidebar-bottom) 100%); }
  .blacklist-topbar{ display:flex; align-items:center; padding:16px 28px; background:linear-gradient(90deg, rgba(51,81,60,0.45), rgba(63,107,74,0.45)); backdrop-filter:blur(16px) saturate(140%); -webkit-backdrop-filter:blur(16px) saturate(140%); box-shadow:0 1px 0 rgba(0,0,0,0.08); }
  .blacklist-topbar .topbar-username{ margin-left:auto; }
  .blacklist-body{ flex:1; display:flex; align-items:center; justify-content:center; padding:60px 24px; }
  .blacklist-content{ max-width:680px; text-align:center; }
  .blacklist-lock-circle{ width:110px; height:110px; border-radius:50%; background:#c0463d; display:flex; align-items:center; justify-content:center; margin:0 auto 30px auto; box-shadow:0 6px 16px rgba(0,0,0,0.3); }
  .blacklist-lock-circle svg{ width:52px; height:52px; color:#fff; }
  .blacklist-headline{ color:#fff; font-size:26px; font-weight:900; line-height:1.3; margin:0 0 4px 0; }
  .blacklist-headline .flagged-word{ color:#e8615a; }
  .blacklist-subtext{ color:rgba(255,255,255,0.9); font-size:13.5px; line-height:1.7; margin:18px auto 26px auto; max-width:540px; }
  .blacklist-stage-dots{ display:flex; align-items:center; justify-content:center; gap:14px; margin-bottom:10px; flex-wrap:wrap; }
  .blacklist-stage-dot{ width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-size:13px; font-weight:700; flex-shrink:0; }
  .blacklist-stage-label{ color:#fff; font-size:12.5px; font-weight:700; letter-spacing:0.4px; margin-bottom:30px; }
  .blacklist-info-box{ background:var(--card-bg); border-radius:12px; padding:20px 26px; text-align:left; margin-bottom:26px; }
  .blacklist-info-box h3{ font-size:14px; font-weight:700; color:var(--green-accent); margin:0 0 8px 0; }
  .blacklist-info-box p{ font-size:12.5px; color:var(--text-mid); line-height:1.7; margin:0; }
  .blacklist-actions{ display:flex; gap:14px; justify-content:center; flex-wrap:wrap; }
  .blacklist-contact-btn{ background:var(--green-mid); color:#fff; border:none; border-radius:8px; padding:13px 30px; font-size:13px; font-weight:700; letter-spacing:0.3px; cursor:pointer; text-decoration:none; display:inline-block; }
  .blacklist-contact-btn:hover{ background:var(--green-dark); }
  .blacklist-logout-btn{ background:#c0463d; color:#fff; border:none; border-radius:8px; padding:13px 30px; font-size:13px; font-weight:700; letter-spacing:0.3px; cursor:pointer; }
  .blacklist-logout-btn:hover{ background:#a8382f; }
</style>
</head>
<body>

@if($isBlacklisted)
  <div class="blacklist-page">
    <div class="blacklist-topbar">
      <span class="topbar-username" style="color:#fff;">{{ $tenant->full_name }}</span>
    </div>
    <div class="blacklist-body">
      <div class="blacklist-content">
        <div class="blacklist-lock-circle">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/></svg>
        </div>
        <h1 class="blacklist-headline">WE'RE SORRY, {{ strtoupper(explode(' ', trim($tenant->full_name))[0] ?? 'TENANT') }}.</h1>
        <h1 class="blacklist-headline">YOUR ACCOUNT HAS BEEN FLAGGED AS <span class="flagged-word">DELINQUENT</span></h1>
        <p class="blacklist-subtext">Despite multiple notices, reminders, and an emergency contact notification, your outstanding balance remains unsettled. As a result, your NEST.PH account has been permanently deactivated and added to our blacklist.</p>

        <div class="blacklist-stage-dots">
          @foreach($stages as $s)
            <div class="blacklist-stage-dot" style="background:{{ $s['stage_accent'] }}; color:{{ $s['stage_text'] }};">{{ $s['stage'] }}</div>
          @endforeach
        </div>
        <div class="blacklist-stage-label">ALL ESCALATION STAGES COMPLETED</div>

        <div class="blacklist-info-box">
          <h3>WHAT HAPPENS NEXT?</h3>
          <p>Your account has been permanently deactivated. If you believe this is an error or wish to settle your balance, please contact the dormitory administrator directly. All records are retained for audit purposes.</p>
        </div>

        <div class="blacklist-actions">
          @if($dormContactEmail || $dormContactNumber)
            <a class="blacklist-contact-btn" href="{{ $dormContactEmail ? 'mailto:'.$dormContactEmail : 'tel:'.$dormContactNumber }}">CONTACT ADMIN</a>
          @endif
          <button class="blacklist-logout-btn" id="logoutBtn" tabindex="0">LOG OUT</button>
        </div>
      </div>
    </div>
  </div>
@else
<div class="app">

  @if($portalRestricted)
    <aside class="sidebar restricted-lock" id="sidebar">
      <div class="sidebar-logo"><span class="logo-mark"></span><span class="logo-text">NEST.PH</span></div>
      <div class="lock-panel-body">
        <div class="lock-icon-circle">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/></svg>
        </div>
        <h2 class="lock-panel-title">PORTAL ACCESS<br>RESTRICTED</h2>
        <p class="lock-panel-text">Your account access has been restricted due to unpaid balance. Please settle your balance to restore full access.</p>
      </div>
      <div class="sidebar-footer"><div class="nav-item" id="logoutBtn" tabindex="0"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.4 5.6a9 9 0 11-12.8 0M12 3v8"/></svg></span><span class="label">Log Out</span></div></div>
    </aside>
  @else
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-logo"><span class="logo-mark"></span><span class="logo-text">NEST.PH</span></div>
      <div class="sidebar-section-label">Tenant View</div>
      <ul class="nav-list">
        <li class="nav-item" data-href="{{ route('dashboard') }}" tabindex="0"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></span><span class="label">Tenant Dashboard</span></li>
        <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h18v10H3z"/><path d="M3 12h18"/></svg></span><span class="label">Ticket Module</span></li>
        <li class="nav-item" data-href="{{ route('tenant.billing') }}" tabindex="0"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></span><span class="label">Billing and Payments</span></li>
        <li class="nav-item" data-href="{{ route('tenant.account') }}" tabindex="0"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></span><span class="label">Profile</span></li>
        <li class="nav-item active" data-href="{{ route('tenant.delinquency') }}" tabindex="0"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg></span><span class="label">Delinquency</span></li>
      </ul>
      <div class="sidebar-footer"><div class="nav-item" id="logoutBtn" tabindex="0"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.4 5.6a9 9 0 11-12.8 0M12 3v8"/></svg></span><span class="label">Log Out</span></div></div>
    </aside>
  @endif

  <div class="main">
    <div class="topbar">
      @unless($portalRestricted)
        <div class="hamburger-icon" id="hamburgerBtn" tabindex="0" aria-label="Toggle sidebar"><svg width="20" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg></div>
      @endunless
      <div class="topbar-right">
        <span class="topbar-username">{{ $tenant->full_name }}</span>
        <div class="topbar-icon" data-href="{{ route('tenant.account') }}" tabindex="0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
      </div>
    </div>

    <div class="content">
      <div class="page-head">
        <div class="back-arrow" data-href="{{ route('dashboard') }}" tabindex="0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
        <div>
          <h1>Delinquency Status</h1>
          <p>{{ $tenant->full_name }} @if($tenant->activeContract?->bed?->room?->room_no) · Room {{ $tenant->activeContract->bed->room->room_no }} @endif</p>
        </div>
      </div>

      @if($inEscalation && $currentStageName)
        <div class="stage-banner">DELINQUENCY - STAGE {{ $currentStage }}: {{ strtoupper($currentStageName) }}</div>
        <div class="stage-divider"></div>
      @endif

      @if($isBlacklisted)
        <div class="warning-banner blacklist">
          <strong>Account Blacklisted</strong>
          <span>Your account has reached the final stage of delinquency escalation. Please contact the dormitory administrator immediately to resolve this.</span>
        </div>
      @elseif($portalRestricted)
        <div class="warning-banner restricted">
          <strong>Portal Access Restricted</strong>
          <span>Your account is significantly overdue, so portal access is currently limited to Billing and Payments until your balance is settled.</span>
        </div>
      @endif

      @if(!$inEscalation)
        <div class="good-standing-card">
          <div class="good-standing-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
          </div>
          <h2>You're in Good Standing</h2>
          <p>No escalation activity on your account. Keep those on-time payments coming!</p>
        </div>
      @else
        <div class="top-grid">
          <div class="balance-card {{ $portalRestricted ? 'with-actions' : '' }}">
            <div class="balance-card-main">
              <div class="balance-label">OUTSTANDING BALANCE</div>
              <div class="balance-amount">₱{{ number_format($balance, 2) }}</div>
              @if($monthsOverdue >= 1)
                <div class="balance-due">{{ $monthsOverdue }} Month{{ $monthsOverdue > 1 ? 's' : '' }} Overdue</div>
              @elseif($oldestDueDate)
                <div class="balance-due">Overdue since {{ \Carbon\Carbon::parse($oldestDueDate)->format('M j, Y') }}</div>
              @endif
            </div>
            @if($portalRestricted)
              <div class="balance-card-actions">
                <button class="pay-now-pill" data-href="{{ route('tenant.billing') }}">PAY NOW</button>
                @if($dormContactEmail || $dormContactNumber)
                  <a class="contact-admin-pill" href="{{ $dormContactEmail ? 'mailto:'.$dormContactEmail : 'tel:'.$dormContactNumber }}">CONTACT ADMIN</a>
                @endif
              </div>
            @endif
          </div>

          <div class="breakdown-card">
            <h3>BILLING BREAKDOWN</h3>
            @forelse($overdueBills as $bill)
              <div class="breakdown-line">
                <span>{{ \Carbon\Carbon::parse($bill->billing_period_start)->format('F Y') }}</span>
                <span>₱{{ number_format($bill->total_amount, 2) }}</span>
              </div>
            @empty
              <div class="empty-note">No overdue statements right now.</div>
            @endforelse
            @if($totalPenalties > 0)
              <div class="breakdown-line penalty-line">
                <span>Includes Late Penalties</span>
                <span>+ ₱{{ number_format($totalPenalties, 2) }}</span>
              </div>
            @endif
          </div>
        </div>

        <div class="billing-panel">
          <h3>ESCALATION TIMELINE</h3>
          @forelse($stages as $stageRow)
            @php
              $isCurrent = $stageRow['stage'] === $currentStage;
              // Single timestamp if this stage only ever logged once
              // (e.g. Stage 1), or a range if it spans several attempts
              // (e.g. Stage 2's Day 1/3/7 reminders).
              $sameMoment = $stageRow['earliest']->equalTo($stageRow['latest']);
              $dateLabel = $sameMoment
                  ? $stageRow['earliest']->format('M j, Y · g:i A')
                  : $stageRow['earliest']->format('M j') . ' – ' . $stageRow['latest']->format('M j, Y');
              // Stages 3+ get an expanded, highlighted row while they're
              // the tenant's current stage. Stage 3's Pay Now/Contact Admin
              // buttons moved up into the balance card (visible the whole
              // portal-restricted period, not just while Stage 3 is
              // current), so its expanded body is just the explanation now.
              $isExpandedCurrent = $isCurrent && in_array($stageRow['stage'], [3, 4, 5, 6], true);
              $expandedClass = $isExpandedCurrent
                  ? ($stageRow['stage'] === 5 ? 'expanded demand-letter' : 'expanded restricted')
                  : '';
            @endphp
            <div class="timeline-row {{ $expandedClass }}">
              <div class="timeline-row-head">
                <div class="timeline-number" style="background:{{ $stageRow['stage_accent'] }}; color:{{ $stageRow['stage_text'] }};">{{ $stageRow['stage'] }}</div>
                <div class="timeline-info">
                  <div class="timeline-stage-name">{{ strtoupper($stageRow['stage_name']) }}</div>
                  <div class="timeline-date">{{ $dateLabel }}</div>
                </div>
                <span class="status-pill {{ $isCurrent ? 'overdue' : 'paid' }}">
                  {{ $isCurrent ? 'ACTIVE' : 'COMPLETE' }}
                </span>
              </div>

              @if($isExpandedCurrent && $stageRow['stage'] === 3)
                <div class="timeline-expanded-body">
                  <p>Your account access has been restricted due to unpaid balance. Please settle your balance to restore full access.</p>
                </div>
              @elseif($isExpandedCurrent && $stageRow['stage'] === 4)
                <div class="timeline-expanded-body emergency-contact-body">
                  <div class="emergency-contact-header">
                    @if($emergencyContactInitials)
                      <div class="emergency-contact-avatar">{{ $emergencyContactInitials }}</div>
                    @endif
                    <div class="emergency-contact-name">{{ $tenant->emergency_contact_name ?? 'No emergency contact on file' }}</div>
                    @if($tenant->emergency_contact_number)
                      <div class="emergency-contact-number">{{ $tenant->emergency_contact_number }}</div>
                    @endif
                  </div>
                  @if($emergencyContactMessage)
                    <div class="emergency-message-label">MESSAGE PREVIEW</div>
                    <div class="emergency-message-box">{{ $emergencyContactMessage }}</div>
                  @endif
                </div>
              @elseif($isExpandedCurrent && $stageRow['stage'] === 5)
                <div class="timeline-expanded-body demand-letter-body">
                  @if($demandLetterReady)
                    <a class="demand-letter-download-btn" href="{{ route('tenant.delinquency.demand-letter') }}">Download PDF</a>
                  @endif
                  <div class="demand-letter-note">This document contains confidential legal information. Contents are not displayed here for privacy. Download the file to view the full demand letter.</div>
                  @if($demandLetterReady)
                    <div class="demand-letter-footnote">Accessible to tenant only · Single download</div>
                  @else
                    <div class="demand-letter-footnote">Your demand letter is still being prepared.</div>
                  @endif
                </div>
              @endif
            </div>
          @empty
            <div class="empty-note">No escalation activity yet.</div>
          @endforelse
        </div>
      @endif
    </div>
  </div>
</div>
@endif

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
})();
</script>

</body>
</html>