<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH — Billing and Payments</title>
<style>
  :root{
    --green-dark:#3f6b4a; --green-darker:#345a3e; --green-mid:#4f7c57;
    --green-sidebar-top:#46694e; --green-sidebar-bottom:#a8d4ab;
    --green-accent:#3f6b4a; --green-btn:#3f6b4a; --green-btn-hover:#2f5439;
    --bg-page:#f4f6f4; --card-bg:#ffffff;
    --text-dark:#1f2a22; --text-mid:#5b6b60; --text-light:#8a9690; --border:#e5e9e4;
    --pending-bg:#fbe9c8; --pending-text:#a4761a;
    --paid-bg:#d9f2dd; --paid-text:#3f7a4a;
    --font-body: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
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

  .main{ flex:1; display:flex; flex-direction:column; min-width:0; }
  .topbar{ display:flex; align-items:center; gap:16px; background:linear-gradient(90deg, #cfcdc4, var(--green-sidebar-top)); padding:16px 28px; }
  .hamburger-icon{ color:#fff; cursor:pointer; }
  .topbar-right{ margin-left:auto; display:flex; align-items:center; gap:14px; }
  .topbar-icon{ width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.9); display:flex; align-items:center; justify-content:center; color:var(--green-dark); cursor:pointer; }
  .topbar-icon svg{ width:16px; height:16px; }

  .content{ padding:26px 34px 48px 34px; flex:1; max-width:1180px; }
  .page-head{ display:flex; align-items:flex-start; gap:12px; margin-bottom:20px; }
  .back-arrow{ width:30px; height:30px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-mid); flex-shrink:0; margin-top:2px; }
  .page-head-text h1{ font-size:19px; font-weight:700; margin:0; color:var(--green-accent); }
  .page-head-text p{ font-size:12.5px; color:var(--text-mid); margin:2px 0 0 0; }

  .view{ display:none; }
  .view.active{ display:block; }

  /* ===== MAIN VIEW ===== */
  .top-grid{ display:grid; grid-template-columns:1.1fr 1fr 0.85fr; gap:18px; margin-bottom:20px; }
  .balance-card{ background:linear-gradient(135deg, var(--green-dark), var(--green-darker)); border-radius:14px; padding:22px 24px; color:#fff; }
  .balance-label{ font-size:11.5px; font-weight:700; letter-spacing:0.6px; opacity:0.85; margin-bottom:8px; }
  .balance-amount{ font-size:32px; font-weight:800; margin-bottom:6px; }
  .balance-due{ font-size:12px; opacity:0.85; margin-bottom:16px; }
  .pay-now-btn{ background:#fff; color:var(--green-dark); border:none; border-radius:8px; padding:10px 18px; font-size:13px; font-weight:700; cursor:pointer; }
  .pay-now-btn:hover{ background:#f0f0f0; }

  .breakdown-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:14px; padding:20px 22px; }
  .breakdown-card h3{ font-size:14px; font-weight:700; margin:0 0 16px 0; }
  .breakdown-row{ display:grid; grid-template-columns:70px 1fr 60px; align-items:center; gap:12px; margin-bottom:14px; }
  .breakdown-row:last-child{ margin-bottom:0; }
  .breakdown-label{ font-size:12.5px; color:var(--text-mid); }
  .breakdown-track{ height:8px; border-radius:20px; background:#eef0ee; overflow:hidden; }
  .breakdown-fill{ height:100%; border-radius:20px; }
  .breakdown-fill.rent{ background:#e8b13c; }
  .breakdown-fill.utilities{ background:#6f8fae; }
  .breakdown-fill.wifi{ background:#6fbf73; }
  .breakdown-amount{ font-size:12.5px; color:var(--text-dark); text-align:right; }

  .reminder-card{ background:#eef4ee; border-radius:14px; padding:22px 20px; text-align:center; display:flex; flex-direction:column; align-items:center; justify-content:center; }
  .reminder-card svg{ width:30px; height:30px; color:var(--green-dark); margin-bottom:10px; }
  .reminder-card strong{ font-size:13.5px; color:var(--text-dark); display:block; margin-bottom:4px; }
  .reminder-card span{ font-size:11.5px; color:var(--text-mid); }

  .billing-panel{ background:var(--card-bg); border:1px solid var(--border); border-radius:14px; padding:22px 24px 20px 24px; }
  .billing-panel h3{ font-size:15px; font-weight:700; margin:0 0 16px 0; }
  table.billing-table{ width:100%; border-collapse:collapse; }
  table.billing-table th{ text-align:left; font-size:10.5px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-light); padding:8px 10px; border-bottom:1px solid var(--border); }
  table.billing-table td{ font-size:13px; color:var(--text-dark); padding:14px 10px; border-bottom:1px solid #f0f2f0; }
  .status-pill{ font-size:11.5px; font-weight:600; padding:4px 14px; border-radius:20px; display:inline-block; }
  .status-pill.pending{ background:var(--pending-bg); color:var(--pending-text); }
  .status-pill.paid{ background:var(--paid-bg); color:var(--paid-text); }
  .status-pill.overdue{ background:#f7d9d7; color:#c0463d; }
  .row-pay-btn{ background:var(--green-btn); color:#fff; border:none; border-radius:7px; padding:8px 18px; font-size:12px; font-weight:700; cursor:pointer; }
  .row-pay-btn:hover{ background:var(--green-btn-hover); }
  .row-view-btn{ background:#fff; border:1px solid var(--border); border-radius:7px; padding:8px 16px; font-size:12px; font-weight:600; color:var(--text-dark); cursor:pointer; }
  .view-more-wrap{ text-align:center; margin-top:20px; }
  .view-more-btn{ background:#fff; border:1px solid var(--border); border-radius:20px; padding:9px 24px; font-size:12.5px; font-weight:600; color:var(--text-dark); cursor:pointer; }

  /* ===== METHOD VIEW ===== */
  .method-top-grid{ display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:20px; }
  .amount-card{ background:linear-gradient(135deg, var(--green-dark), var(--green-darker)); border-radius:14px; padding:22px 24px; color:#fff; }
  .amount-card-label{ font-size:13px; opacity:0.9; margin-bottom:8px; }
  .amount-card-value{ font-size:30px; font-weight:800; margin-bottom:6px; }
  .amount-card-due{ font-size:12px; opacity:0.85; margin-bottom:16px; }
  .amount-card-note{ border-top:1px dashed rgba(255,255,255,0.35); padding-top:12px; font-size:11.5px; opacity:0.9; display:flex; align-items:center; gap:8px; }

  .summary-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:14px; padding:20px 22px; }
  .summary-card h3{ font-size:13.5px; font-weight:700; margin:0 0 14px 0; letter-spacing:0.3px; }
  .summary-row{ display:flex; justify-content:space-between; font-size:12.5px; color:var(--text-mid); padding:8px 0; border-bottom:1px solid #f0f2f0; }
  .summary-row span:last-child{ color:var(--text-dark); font-weight:600; }
  .summary-row.total{ border-bottom:none; padding-top:12px; font-weight:700; }
  .summary-row.total span{ color:var(--text-dark); font-size:13.5px; }

  .method-panel{ background:var(--card-bg); border:1px solid var(--border); border-radius:14px; padding:22px 24px; margin-bottom:18px; }
  .method-panel h3{ font-size:15px; font-weight:700; margin:0 0 16px 0; }
  .method-option{ display:flex; align-items:center; gap:16px; border:1px solid var(--border); border-radius:10px; padding:16px 18px; margin-bottom:12px; cursor:pointer; }
  .method-option:last-child{ margin-bottom:0; }
  .method-option.selected{ border-color:var(--green-dark); background:#f4faf5; }
  .method-radio{ width:18px; height:18px; border-radius:50%; border:2px solid #b7c2b8; flex-shrink:0; position:relative; }
  .method-option.selected .method-radio{ border-color:var(--green-dark); }
  .method-option.selected .method-radio::after{ content:''; position:absolute; inset:3px; border-radius:50%; background:var(--green-dark); }
  .method-logo{ width:40px; height:40px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-weight:800; font-size:11px; color:#fff; }
  .method-logo.gcash{ background:#0070e0; }
  .method-logo.bdo{ background:#00287a; }
  .method-logo.cash{ background:#eef4ee; color:var(--green-dark); font-size:18px; }
  .method-text strong{ display:block; font-size:13.5px; color:var(--text-dark); }
  .method-text span{ font-size:11.5px; color:var(--text-mid); }
  .method-note{ margin-left:auto; font-size:11px; color:#c0463d; text-align:right; max-width:220px; }

  .secure-bar{ background:#eef4ee; border-radius:14px; padding:16px 22px; display:flex; align-items:center; gap:14px; margin-bottom:20px; }
  .secure-bar svg{ width:26px; height:26px; color:var(--green-dark); flex-shrink:0; }
  .secure-bar strong{ display:block; font-size:13px; color:var(--text-dark); }
  .secure-bar span{ font-size:11px; color:var(--text-mid); }
  .secure-bar .help{ margin-left:auto; text-align:right; margin-right:16px; }
  .secure-bar .help strong{ font-size:12.5px; }
  .secure-bar .help span{ font-size:11px; }
  .contact-btn{ background:#fff; border:1px solid var(--border); border-radius:8px; padding:9px 16px; font-size:12px; font-weight:600; color:var(--text-dark); cursor:pointer; }

  .proceed-wrap{ text-align:center; }
  .proceed-btn{ background:var(--green-btn); color:#fff; border:none; border-radius:9px; padding:13px 38px; font-size:13.5px; font-weight:700; cursor:pointer; }
  .proceed-btn:hover{ background:var(--green-btn-hover); }
  .proceed-btn:disabled{ background:#c7d0c8; cursor:not-allowed; }

  .cash-notice{ display:none; background:#fff; border:1px solid var(--border); border-radius:14px; padding:26px; text-align:center; margin-bottom:18px; }
  .cash-notice svg{ width:34px; height:34px; color:var(--green-dark); margin-bottom:10px; }
  .cash-notice h4{ margin:0 0 6px 0; font-size:15px; }
  .cash-notice p{ margin:0; font-size:12.5px; color:var(--text-mid); }

  /* ===== PROOF VIEW ===== */
  .info-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:14px; padding:20px 24px; margin-bottom:18px; }
  .info-card-head{ display:flex; align-items:center; gap:8px; font-size:14px; font-weight:700; margin-bottom:16px; }
  .info-card-head svg{ width:16px; height:16px; }
  .info-grid{ display:grid; grid-template-columns:repeat(5, 1fr); gap:14px; margin-bottom:16px; }
  .info-item{ display:flex; align-items:flex-start; gap:8px; }
  .info-item svg{ width:14px; height:14px; color:var(--text-mid); flex-shrink:0; margin-top:2px; }
  .info-item-label{ font-size:10.5px; color:var(--text-light); }
  .info-item-value{ font-size:12.5px; font-weight:700; color:var(--text-dark); }
  .selected-method-row{ border-top:1px solid var(--border); padding-top:14px; }
  .selected-method-row .lbl{ font-size:11.5px; font-weight:700; color:var(--text-dark); margin-bottom:4px; }
  .selected-method-row .val{ font-size:12.5px; color:var(--text-mid); }

  .proof-grid{ display:grid; grid-template-columns:1fr 1fr 0.9fr; gap:18px; align-items:start; margin-bottom:18px; }
  .proof-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:14px; padding:20px 22px; }
  .proof-card-head{ display:flex; align-items:center; gap:8px; font-size:14px; font-weight:700; margin-bottom:16px; }
  .proof-card-head svg{ width:16px; height:16px; }
  .dropzone{ border:2px dashed #cdd6cd; border-radius:10px; padding:28px 16px; text-align:center; }
  .dropzone svg{ width:34px; height:34px; color:var(--green-dark); margin-bottom:10px; }
  .dropzone strong{ display:block; font-size:13px; margin-bottom:4px; }
  .dropzone .or{ font-size:11.5px; color:var(--text-light); margin:6px 0; }
  .choose-file-btn{ background:#fff; border:1px solid var(--border); border-radius:8px; padding:8px 18px; font-size:12.5px; font-weight:600; cursor:pointer; }
  .dropzone-hint{ font-size:10.5px; color:var(--text-light); margin-top:10px; }
  .uploaded-file-label{ font-size:11.5px; font-weight:700; color:var(--text-mid); margin:16px 0 8px 0; }
  .uploaded-file-row{ display:none; border:1px solid var(--border); border-radius:8px; padding:10px 12px; align-items:center; gap:10px; }
  .uploaded-file-row.show{ display:flex; }
  .uploaded-file-icon{ width:34px; height:34px; border-radius:6px; background:#0070e0; display:flex; align-items:center; justify-content:center; color:#fff; font-size:9px; font-weight:800; flex-shrink:0; }
  .uploaded-file-name{ font-size:12px; font-weight:600; color:var(--text-dark); }
  .uploaded-file-size{ font-size:10.5px; color:var(--text-light); }
  .remove-file-link{ margin-left:auto; font-size:11px; color:#c0463d; cursor:pointer; }

  .proof-field{ margin-bottom:12px; }
  .proof-field label{ display:block; font-size:11px; font-weight:600; color:var(--text-dark); margin-bottom:5px; }
  .proof-field label .req{ color:#c0463d; }
  .proof-field input, .proof-field textarea{ width:100%; border:1px solid var(--border); border-radius:8px; padding:9px 11px; font-size:12.5px; color:var(--text-dark); font-family:var(--font-body); }

  .qr-panel{ border-radius:14px; padding:22px; text-align:center; color:#fff; }
  .qr-panel.gcash{ background:#0070e0; }
  .qr-panel.bdo{ background:#00287a; border:2px solid #f2b90c; }
  .qr-panel-title{ font-size:20px; font-weight:800; margin-bottom:2px; }
  .qr-panel-sub{ font-size:11.5px; font-weight:700; letter-spacing:0.5px; margin-bottom:16px; }
  .qr-box{ background:#fff; border-radius:10px; padding:14px; margin-bottom:12px; }
  .qr-panel.bdo .qr-box{ border:2px solid #f2b90c; }
  .qr-merchant{ font-size:10px; font-weight:700; color:var(--text-dark); margin-bottom:8px; }
  .qr-placeholder{ width:100%; aspect-ratio:1; background:
      repeating-conic-gradient(#1c1c1c 0% 25%, #fff 0% 50%) 0 0/22% 22%,
      #fff; border-radius:4px; }
  .qr-caption{ font-size:10.5px; opacity:0.9; }

  .proof-actions{ display:flex; gap:12px; align-items:center; }
  .submit-proof-btn{ background:var(--green-btn); color:#fff; border:none; border-radius:9px; padding:12px 26px; font-size:13px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:8px; }
  .submit-proof-btn:hover{ background:var(--green-btn-hover); }
  .cancel-proof-btn{ background:#fff; border:1px solid var(--border); border-radius:9px; padding:12px 22px; font-size:13px; font-weight:600; color:var(--text-dark); cursor:pointer; display:flex; align-items:center; gap:8px; }
  .form-error{ font-size:12px; color:#c0463d; margin-top:8px; display:none; }

  .empty-note{ font-size:12px; color:var(--text-light); text-align:center; padding:20px; }

  .modal-overlay{ display:none; position:fixed; inset:0; background:rgba(20,30,20,0.45); align-items:center; justify-content:center; z-index:50; }
  .modal-overlay.open{ display:flex; }
  .modal-box{ background:#fff; border-radius:14px; padding:24px; width:420px; max-width:92vw; max-height:85vh; overflow-y:auto; }
  .modal-box h3{ margin:0 0 14px 0; font-size:15px; }
  .modal-payment-row{ border:1px solid var(--border); border-radius:8px; padding:12px 14px; margin-bottom:10px; font-size:12.5px; }
  .modal-payment-row div{ display:flex; justify-content:space-between; margin-bottom:4px; }
  .modal-payment-row div span:first-child{ color:var(--text-light); }
  .modal-close-btn{ margin-top:10px; width:100%; background:#fff; border:1px solid var(--border); border-radius:8px; padding:10px; font-size:13px; font-weight:600; cursor:pointer; }
</style>
</head>
<body>
<div class="app">

  <aside class="sidebar">
    <div class="sidebar-logo"><span class="logo-mark"></span> NEST.PH</div>
    <div class="sidebar-section-label">Tenant View</div>
    <ul class="nav-list">
      <li class="nav-item" data-href="{{ route('dashboard') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></span>Tenant Dashboard</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h18v10H3z"/><path d="M3 12h18"/></svg></span>Ticket Module</li>
      <li class="nav-item active" data-href="{{ route('tenant.billing') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></span>Billing and Payments</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></span>Profile</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg></span>Delinquency</li>
    </ul>
    <div class="sidebar-footer"><div class="nav-item" id="logoutBtn"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.4 5.6a9 9 0 11-12.8 0M12 3v8"/></svg></span>Log Out</div></div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div class="hamburger-icon"><svg width="20" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg></div>
      <div class="topbar-right">
        <div class="topbar-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
        <div class="topbar-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 00.3 1.9l.1.1a2 2 0 11-2.8 2.8"/></svg></div>
      </div>
    </div>

    <div class="content">

      <div class="view active" id="viewMain">
        <div class="page-head">
          <div class="back-arrow" data-href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
          <div class="page-head-text">
            <h1>Billing and Payments</h1>
            <p id="tenantSubtitle">Loading…</p>
          </div>
        </div>

        <div class="top-grid">
          <div class="balance-card">
            <div class="balance-label">BALANCE DUE</div>
            <div class="balance-amount" id="mainBalanceAmount">₱0.00</div>
            <div class="balance-due" id="mainBalanceDue">—</div>
            <button class="pay-now-btn" id="mainPayNowBtn">Pay Now &nbsp;›</button>
          </div>

          <div class="breakdown-card">
            <h3>BREAKDOWN</h3>
            <div id="breakdownRows">
              <div class="empty-note">No active bill.</div>
            </div>
          </div>

          <div class="reminder-card">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            <strong>Stay on track!</strong>
            <span>Make sure your payments are on time to avoid penalties</span>
          </div>
        </div>

        <div class="billing-panel">
          <h3>Recent Billing</h3>
          <table class="billing-table">
            <thead>
              <tr><th>Month</th><th>Rent</th><th>Utilities</th><th>Wi-Fi</th><th>Total</th><th>Status</th><th>Due Date</th><th>Actions</th></tr>
            </thead>
            <tbody id="billingRows">
              <tr><td colspan="8" class="empty-note">Loading…</td></tr>
            </tbody>
          </table>
          <div class="view-more-wrap">
            <button class="view-more-btn" id="viewMoreBtn">View More History  ⌄</button>
          </div>
        </div>
      </div>

      <div class="view" id="viewMethod">
        <div class="page-head">
          <div class="back-arrow" id="methodBackBtn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
          <div class="page-head-text">
            <h1>Billing and Payments</h1>
            <p id="methodSubtitle">—</p>
          </div>
        </div>

        <div class="method-top-grid">
          <div class="amount-card">
            <div class="amount-card-label">Amount to Pay</div>
            <div class="amount-card-value" id="methodAmount">₱0.00</div>
            <div class="amount-card-due" id="methodDue">Due —</div>
            <div class="amount-card-note">ⓘ&nbsp; Make sure to settle your payments on time to avoid penalties</div>
          </div>

          <div class="summary-card">
            <h3>BILLING SUMMARY</h3>
            <div class="summary-row"><span>Rent</span><span id="sumRent">₱0.00</span></div>
            <div class="summary-row"><span>Utilities</span><span id="sumUtilities">₱0.00</span></div>
            <div class="summary-row"><span>Wi-Fi</span><span id="sumWifi">₱0.00</span></div>
            <div class="summary-row total"><span>Total Amount</span><span id="sumTotal">₱0.00</span></div>
          </div>
        </div>

        <div class="method-panel">
          <h3>Select Payment Method</h3>
          <div class="method-option" data-method="gcash">
            <div class="method-radio"></div>
            <div class="method-logo gcash">G</div>
            <div class="method-text"><strong>Gcash</strong><span>Pay using your gcash account or mobile number</span></div>
          </div>
          <div class="method-option" data-method="bdo">
            <div class="method-radio"></div>
            <div class="method-logo bdo">BDO</div>
            <div class="method-text"><strong>BDO</strong><span>Pay using your BDO account</span></div>
          </div>
          <div class="method-option" data-method="cash">
            <div class="method-radio"></div>
            <div class="method-logo cash">P</div>
            <div class="method-text"><strong>Cash Payment</strong><span>Please proceed to the lobby reception to settle your payment in person.</span></div>
            <div class="method-note">*In-person payments accepted from 8 AM to 5 PM only</div>
          </div>
        </div>

        <div class="cash-notice" id="cashNotice">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 2"/></svg>
          <h4>Pay at the Lobby Reception</h4>
          <p>Please bring your exact balance to the reception desk between 8:00 AM and 5:00 PM. An admin will record your payment once received.</p>
        </div>

        <div class="secure-bar">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 018 0v3"/></svg>
          <div><strong>Your payment is secure</strong><span>Your personal and payment information is encrypted and protected</span></div>
          <div class="help"><strong>Need help?</strong><span>Contact us</span></div>
          <button class="contact-btn">FB Team</button>
        </div>

        <div class="proceed-wrap">
          <button class="proceed-btn" id="proceedBtn" disabled>Proceed to Payment</button>
        </div>
      </div>

      <div class="view" id="viewProof">
        <div class="page-head">
          <div class="back-arrow" id="proofBackBtn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
          <div class="page-head-text">
            <h1>Payment</h1>
            <p>Pay here and submit proof of payment</p>
          </div>
        </div>

        <div class="info-card">
          <div class="info-card-head"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 7v10l9 4 9-4V7"/></svg>Payment Information</div>
          <div class="info-grid">
            <div class="info-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg><div><div class="info-item-label">Tenant Name</div><div class="info-item-value" id="infoTenantName">—</div></div></div>
            <div class="info-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 21V9l8-6 8 6v12"/></svg><div><div class="info-item-label">Room/Bed Space</div><div class="info-item-value" id="infoRoom">—</div></div></div>
            <div class="info-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 3v4a1 1 0 001 1h4"/><path d="M17 21H7a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z"/></svg><div><div class="info-item-label">Billing Reference No.</div><div class="info-item-value" id="infoRef">—</div></div></div>
            <div class="info-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg><div><div class="info-item-label">Amount Due</div><div class="info-item-value" id="infoAmount">—</div></div></div>
            <div class="info-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg><div><div class="info-item-label">Due Date</div><div class="info-item-value" id="infoDue">—</div></div></div>
          </div>
          <div class="selected-method-row">
            <div class="lbl">Selected Payment Method</div>
            <div class="val" id="infoMethodName">—</div>
          </div>
        </div>

        <div class="proof-grid">
          <div class="proof-card">
            <div class="proof-card-head"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><path d="M17 8l-5-5-5 5M12 3v12"/></svg>Proof of Payment</div>
            <div class="dropzone" id="dropzone">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><path d="M17 8l-5-5-5 5M12 3v12"/></svg>
              <strong>Drag and drop your file here</strong>
              <div class="or">or</div>
              <button class="choose-file-btn" type="button" id="chooseFileBtn">Choose File</button>
              <div class="dropzone-hint">JPG, PNG, PDF up to 10 mb</div>
            </div>
            <input type="file" id="proofFileInput" accept="image/jpeg,image/png,application/pdf" style="display:none">
            <div class="uploaded-file-label">Uploaded file</div>
            <div class="uploaded-file-row" id="uploadedFileRow">
              <div class="uploaded-file-icon">FILE</div>
              <div>
                <div class="uploaded-file-name" id="uploadedFileName">—</div>
                <div class="uploaded-file-size" id="uploadedFileSize">—</div>
              </div>
              <div class="remove-file-link" id="removeFileLink">Remove file</div>
            </div>
          </div>

          <div class="proof-card">
            <div class="proof-card-head"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>Proof of Payment</div>
            <div class="proof-field"><label>Reference / Transaction ID <span class="req">*</span></label><input type="text" id="pfReference" placeholder="1234 5678 9012 3456"></div>
            <div class="proof-field"><label>Date of Payment <span class="req">*</span></label><input type="date" id="pfDate"></div>
            <div class="proof-field"><label>Time of Payment <span class="req">*</span></label><input type="time" id="pfTime"></div>
            <div class="proof-field"><label>Amount Paid <span class="req">*</span></label><input type="number" id="pfAmount" step="0.01" min="0.01"></div>
            <div class="proof-field"><label>Notes (optional)</label><input type="text" id="pfNotes" placeholder="Add any additional information…"></div>
          </div>

          <div class="qr-panel" id="qrPanel">
            <div class="qr-panel-title" id="qrTitle">GCash</div>
            <div class="qr-panel-sub">SCAN TO PAY HERE</div>
            <div class="qr-box">
              <div class="qr-merchant" id="qrMerchant">Merchant Name Here<br>0999-XXX-1234</div>
              <div class="qr-placeholder"></div>
            </div>
            <div class="qr-caption" id="qrCaption">Pay using your gcash account or mobile number</div>
          </div>
        </div>

        <div class="form-error" id="proofError"></div>

        <div class="proof-actions">
          <button class="submit-proof-btn" id="submitProofBtn">Submit Proof of Payment</button>
          <button class="cancel-proof-btn" id="cancelProofBtn">Cancel</button>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="modal-overlay" id="proofsModal">
  <div class="modal-box">
    <h3>Payment Proofs</h3>
    <div id="proofsModalBody"><div class="empty-note">Loading…</div></div>
    <button class="modal-close-btn" id="proofsModalClose">Close</button>
  </div>
</div>

<script>
(function(){
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  let tenantInfo = null;
  let allBills = [];
  let activeBill = null;
  let selectedMethod = null;
  let billingRowsShown = 5;

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
    const body = await res.json().catch(() => ({}));
    if(!res.ok){
      const message = body.message || (body.errors ? Object.values(body.errors)[0][0] : `Request failed (${res.status})`);
      throw new Error(message);
    }
    return body;
  }

  function peso(n){
    return '₱' + Number(n ?? 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function monthLabel(d){
    if(!d) return '—';
    return new Date(d).toLocaleDateString('en-PH', { year: 'numeric', month: 'short' });
  }

  function fullDate(d){
    if(!d) return '—';
    return new Date(d).toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' });
  }

  function showView(id){
    document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
    document.getElementById(id).classList.add('active');
  }

  async function loadEverything(){
    try {
      const me = await api('/my/billing/summary');
      tenantInfo = me;
      document.getElementById('tenantSubtitle').textContent = `${me.tenant.full_name} - Room ${me.contract?.bed?.room?.room_no ?? '—'}`;
    } catch(e){ /* non-fatal */ }

    try {
      const data = await api('/my/billing/bills');
      allBills = data.bills;
      renderMainView();
    } catch(e){
      document.getElementById('billingRows').innerHTML = `<tr><td colspan="8" class="empty-note">${e.message}</td></tr>`;
    }
  }

  function renderMainView(){
    const payable = allBills.filter(b => ['unpaid', 'partial', 'overdue'].includes(b.status));
    const current = payable[0] || null;

    if(current){
      document.getElementById('mainBalanceAmount').textContent = peso(current.balance);
      document.getElementById('mainBalanceDue').textContent = `Due: ${fullDate(current.due_date)}`;
      document.getElementById('mainPayNowBtn').disabled = false;

      const total = Number(current.total_amount) || 1;
      const rows = [
        ['Rent', 'rent', current.base_rent],
        ['Utilities', 'utilities', current.utilities_amount],
        ['Wi-Fi', 'wifi', current.wifi_amount],
      ];
      document.getElementById('breakdownRows').innerHTML = rows.map(([label, cls, amt]) => `
        <div class="breakdown-row">
          <div class="breakdown-label">${label}</div>
          <div class="breakdown-track"><div class="breakdown-fill ${cls}" style="width:${Math.min(100, (amt / total) * 100)}%"></div></div>
          <div class="breakdown-amount">${peso(amt)}</div>
        </div>
      `).join('');
    } else {
      document.getElementById('mainBalanceAmount').textContent = peso(0);
      document.getElementById('mainBalanceDue').textContent = 'No outstanding balance';
      document.getElementById('mainPayNowBtn').disabled = true;
      document.getElementById('breakdownRows').innerHTML = '<div class="empty-note">Nothing due right now.</div>';
    }

    renderBillingTable();
  }

  function renderBillingTable(){
    const tbody = document.getElementById('billingRows');
    const shown = allBills.slice(0, billingRowsShown);

    if(!shown.length){
      tbody.innerHTML = '<tr><td colspan="8" class="empty-note">No billing statements yet.</td></tr>';
      return;
    }

    tbody.innerHTML = shown.map(bill => {
      const payable = ['unpaid', 'partial', 'overdue'].includes(bill.status);
      const pillClass = bill.status === 'paid' ? 'paid' : (bill.status === 'overdue' ? 'overdue' : 'pending');
      const pillLabel = bill.status === 'unpaid' ? 'Pending' : bill.status.charAt(0).toUpperCase() + bill.status.slice(1);
      return `
        <tr>
          <td>${monthLabel(bill.billing_period_start)}</td>
          <td>${peso(bill.base_rent)}</td>
          <td>${peso(bill.utilities_amount)}</td>
          <td>${peso(bill.wifi_amount)}</td>
          <td>${peso(bill.total_amount)}</td>
          <td><span class="status-pill ${pillClass}">${pillLabel}</span></td>
          <td>${fullDate(bill.due_date)}</td>
          <td>${payable
            ? `<button class="row-pay-btn" data-bill-id="${bill.id}">Pay</button>`
            : `<button class="row-view-btn" data-bill-id="${bill.id}">View Proofs</button>`}</td>
        </tr>
      `;
    }).join('');

    tbody.querySelectorAll('.row-pay-btn').forEach(btn => {
      btn.addEventListener('click', () => startPaymentFlow(Number(btn.dataset.billId)));
    });
    tbody.querySelectorAll('.row-view-btn').forEach(btn => {
      btn.addEventListener('click', () => openProofsModal(Number(btn.dataset.billId)));
    });

    document.getElementById('viewMoreBtn').style.display = billingRowsShown >= allBills.length ? 'none' : 'inline-block';
  }

  document.getElementById('viewMoreBtn').addEventListener('click', () => {
    billingRowsShown += 5;
    renderBillingTable();
  });

  document.getElementById('mainPayNowBtn').addEventListener('click', () => {
    const payable = allBills.filter(b => ['unpaid', 'partial', 'overdue'].includes(b.status));
    if(payable[0]) startPaymentFlow(payable[0].id);
  });

  function startPaymentFlow(billId){
    activeBill = allBills.find(b => b.id === billId);
    if(!activeBill) return;
    selectedMethod = null;

    document.getElementById('methodSubtitle').textContent = tenantInfo
      ? `${tenantInfo.tenant.full_name} - Room ${tenantInfo.contract?.bed?.room?.room_no ?? '—'}`
      : '';
    document.getElementById('methodAmount').textContent = peso(activeBill.balance);
    document.getElementById('methodDue').textContent = `Due ${fullDate(activeBill.due_date)}`;
    document.getElementById('sumRent').textContent = peso(activeBill.base_rent);
    document.getElementById('sumUtilities').textContent = peso(activeBill.utilities_amount);
    document.getElementById('sumWifi').textContent = peso(activeBill.wifi_amount);
    document.getElementById('sumTotal').textContent = peso(activeBill.total_amount);

    document.querySelectorAll('.method-option').forEach(o => o.classList.remove('selected'));
    document.getElementById('cashNotice').style.display = 'none';
    document.getElementById('proceedBtn').disabled = true;

    showView('viewMethod');
  }

  document.getElementById('methodBackBtn').addEventListener('click', () => showView('viewMain'));

  document.querySelectorAll('.method-option').forEach(opt => {
    opt.addEventListener('click', () => {
      document.querySelectorAll('.method-option').forEach(o => o.classList.remove('selected'));
      opt.classList.add('selected');
      selectedMethod = opt.dataset.method;
      document.getElementById('proceedBtn').disabled = false;
      document.getElementById('cashNotice').style.display = selectedMethod === 'cash' ? 'block' : 'none';
    });
  });

  document.getElementById('proceedBtn').addEventListener('click', () => {
    if(!selectedMethod) return;
    if(selectedMethod === 'cash'){
      return;
    }
    openProofView(selectedMethod);
  });

  let selectedFile = null;

  function openProofView(method){
    const roomLabel = tenantInfo?.contract?.bed?.room?.room_no
      ? `${tenantInfo.contract.bed.room.room_no}${tenantInfo.contract.bed.bed_label ? ' - ' + tenantInfo.contract.bed.bed_label : ''}`
      : '—';
    const dueDate = new Date(activeBill.due_date);
    const invoiceRef = `INV-${dueDate.getFullYear()}-${String(dueDate.getMonth()+1).padStart(2,'0')}${String(dueDate.getDate()).padStart(2,'0')}-${String(activeBill.id).padStart(3,'0')}`;

    document.getElementById('infoTenantName').textContent = tenantInfo?.tenant?.full_name ?? '—';
    document.getElementById('infoRoom').textContent = roomLabel;
    document.getElementById('infoRef').textContent = invoiceRef;
    document.getElementById('infoAmount').textContent = peso(activeBill.balance);
    document.getElementById('infoDue').textContent = fullDate(activeBill.due_date);
    document.getElementById('infoMethodName').textContent = method === 'gcash' ? 'Gcash' : 'BDO';

    const qrPanel = document.getElementById('qrPanel');
    qrPanel.className = 'qr-panel ' + method;
    document.getElementById('qrTitle').textContent = method === 'gcash' ? 'GCash' : 'BDO';
    document.getElementById('qrCaption').textContent = method === 'gcash'
      ? 'Pay using your gcash account or mobile number'
      : 'Pay using your BDO account';
    document.getElementById('qrMerchant').innerHTML = method === 'gcash'
      ? 'Merchant Name Here<br>0999-XXX-1234'
      : 'Merchant Name Here<br>[XXX-123-12345]';

    document.getElementById('pfReference').value = '';
    document.getElementById('pfDate').value = new Date().toISOString().slice(0, 10);
    document.getElementById('pfTime').value = new Date().toTimeString().slice(0, 5);
    document.getElementById('pfAmount').value = activeBill.balance > 0 ? Number(activeBill.balance).toFixed(2) : '';
    document.getElementById('pfNotes').value = '';
    selectedFile = null;
    document.getElementById('uploadedFileRow').classList.remove('show');
    document.getElementById('proofError').style.display = 'none';

    showView('viewProof');
  }

  document.getElementById('proofBackBtn').addEventListener('click', () => showView('viewMethod'));
  document.getElementById('cancelProofBtn').addEventListener('click', () => showView('viewMethod'));

  const fileInput = document.getElementById('proofFileInput');
  document.getElementById('chooseFileBtn').addEventListener('click', () => fileInput.click());
  document.getElementById('dropzone').addEventListener('click', (e) => {
    if(e.target.id !== 'chooseFileBtn') fileInput.click();
  });
  document.getElementById('dropzone').addEventListener('dragover', (e) => e.preventDefault());
  document.getElementById('dropzone').addEventListener('drop', (e) => {
    e.preventDefault();
    if(e.dataTransfer.files.length){ setSelectedFile(e.dataTransfer.files[0]); }
  });
  fileInput.addEventListener('change', () => {
    if(fileInput.files.length) setSelectedFile(fileInput.files[0]);
  });

  function setSelectedFile(file){
    selectedFile = file;
    document.getElementById('uploadedFileName').textContent = file.name;
    document.getElementById('uploadedFileSize').textContent = (file.size / (1024*1024)).toFixed(1) + ' MB';
    document.getElementById('uploadedFileRow').classList.add('show');
  }

  document.getElementById('removeFileLink').addEventListener('click', () => {
    selectedFile = null;
    fileInput.value = '';
    document.getElementById('uploadedFileRow').classList.remove('show');
  });

  document.getElementById('submitProofBtn').addEventListener('click', async () => {
    const errorEl = document.getElementById('proofError');
    errorEl.style.display = 'none';

    if(!selectedFile){
      errorEl.textContent = 'Please attach a screenshot or file showing proof of payment.';
      errorEl.style.display = 'block';
      return;
    }

    const time = document.getElementById('pfTime').value;
    const notes = document.getElementById('pfNotes').value.trim();
    const combinedNotes = `Time of payment: ${time || '—'}${notes ? ' — ' + notes : ''}${selectedMethod === 'bdo' ? ' (Bank: BDO)' : ''}`;

    const formData = new FormData();
    formData.append('amount_paid', document.getElementById('pfAmount').value);
    formData.append('payment_method', selectedMethod === 'gcash' ? 'gcash' : 'bank_transfer');
    formData.append('reference_number', document.getElementById('pfReference').value);
    formData.append('payment_date', document.getElementById('pfDate').value);
    formData.append('notes', combinedNotes);
    formData.append('proof', selectedFile);

    try {
      await api(`/my/billing/bills/${activeBill.id}/payment-proof`, { method: 'POST', body: formData });
      const data = await api('/my/billing/bills');
      allBills = data.bills;
      renderMainView();
      showView('viewMain');
    } catch(e){
      errorEl.textContent = e.message;
      errorEl.style.display = 'block';
    }
  });

  async function openProofsModal(billId){
    const modal = document.getElementById('proofsModal');
    const body = document.getElementById('proofsModalBody');
    body.innerHTML = '<div class="empty-note">Loading…</div>';
    modal.classList.add('open');

    try {
      const bill = await api(`/my/billing/bills/${billId}`);
      if(!bill.payments || !bill.payments.length){
        body.innerHTML = '<div class="empty-note">No payment proofs submitted for this bill.</div>';
        return;
      }
      body.innerHTML = bill.payments.map(p => `
        <div class="modal-payment-row">
          <div><span>Date</span><span>${fullDate(p.payment_date)}</span></div>
          <div><span>Amount</span><span>${peso(p.amount_paid)}</span></div>
          <div><span>Method</span><span>${p.payment_method.replace('_',' ')}</span></div>
          <div><span>Status</span><span>${p.status}</span></div>
        </div>
      `).join('');
    } catch(e){
      body.innerHTML = `<div class="empty-note">${e.message}</div>`;
    }
  }

  document.getElementById('proofsModalClose').addEventListener('click', () => {
    document.getElementById('proofsModal').classList.remove('open');
  });

  loadEverything();

  // Demo helper: open a mock GCash payment flow when ?demo=gcash is present
  try{
    const params = new URLSearchParams(location.search);
    if(params.get('demo') === 'gcash'){
      tenantInfo = { tenant: { full_name: 'Demo Tenant' }, contract: { bed: { room: { room_no: 'A101' }, bed_label: 'B' } } };
      allBills = [{ id: 999999, balance: 1234.56, total_amount: 1234.56, base_rent: 1000, utilities_amount: 200, wifi_amount: 34.56, status:'unpaid', due_date: new Date().toISOString() }];
      renderMainView();
      // start flow and preselect GCash then proceed to proof (QR) view
      startPaymentFlow(999999);
      const gcashOpt = document.querySelector('.method-option[data-method="gcash"]');
      if(gcashOpt){ gcashOpt.classList.add('selected'); selectedMethod = 'gcash'; document.getElementById('proceedBtn').disabled = false; }
      document.getElementById('proceedBtn').click();
    }
  }catch(e){ /* ignore demo errors */ }
})();
</script>

</body>
</html>