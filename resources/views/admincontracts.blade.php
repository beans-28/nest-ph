<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH — Contract Management</title>
<style>
  :root{
    --green-dark:#3f6b4a; --green-mid:#4f7c57;
    --green-sidebar-top:#5b8a63; --green-sidebar-bottom:#2c4a35;
    --green-accent:#2f6f3c; --green-btn:#2f6b3a; --green-btn-hover:#255a2f;
    --status-occupied:#d9564f; --status-vacant:#7fc98a; --status-vacant-bg:#d9f2dd;
    --status-maintenance:#c9962f; --status-maintenance-bg:#f6ecd6;
    --bg-page:#eef1ee; --card-bg:#ffffff;
    --text-dark:#243026; --text-mid:#5b6b60; --text-light:#8a9690; --border:#e2e6e2;
    --font-body:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;
  }
  *{box-sizing:border-box;}
  html,body{ margin:0; padding:0; font-family:var(--font-body); background:var(--bg-page); color:var(--text-dark); }
  .app{ display:flex; min-height:100vh; }

  .sidebar{ width:220px; flex-shrink:0; background:linear-gradient(180deg,var(--green-sidebar-top),var(--green-sidebar-bottom)); color:#eaf0ea; display:flex; flex-direction:column; padding:20px 0; }
  .sidebar-logo{ display:flex; align-items:center; gap:8px; padding:0 20px 22px 20px; font-weight:700; font-size:17px; border-bottom:1px solid rgba(255,255,255,0.12); margin-bottom:14px; }
  .sidebar-logo .logo-mark{ width:18px; height:18px; border:2px solid #eaf0ea; display:inline-block; position:relative; }
  .sidebar-logo .logo-mark::before,.sidebar-logo .logo-mark::after{ content:''; position:absolute; background:#eaf0ea; width:2px; height:14px; top:0; left:6px; }
  .sidebar-section-label{ font-size:11px; text-transform:uppercase; letter-spacing:1px; color:rgba(234,240,234,0.55); padding:4px 20px 10px 20px; font-weight:600; }
  .nav-list{ list-style:none; margin:0; padding:0; flex:1; }
  .nav-item{ display:flex; align-items:center; gap:12px; padding:11px 20px; font-size:13.5px; color:rgba(234,240,234,0.78); cursor:pointer; border-left:3px solid transparent; }
  .nav-item:hover{ background:rgba(255,255,255,0.06); color:#fff; }
  .nav-item.active{ background:rgba(255,255,255,0.14); color:#fff; font-weight:600; border-left:3px solid #fff; }
  .nav-item .icon svg{ width:16px; height:16px; }
  .sidebar-footer{ padding:14px 20px 0 20px; border-top:1px solid rgba(255,255,255,0.12); margin-top:10px; }
  .sidebar-footer .nav-item{ padding-left:0; }

  .main{ flex:1; display:flex; flex-direction:column; min-width:0; }
  .topbar{ display:flex; align-items:center; gap:16px; background:linear-gradient(90deg,var(--green-mid),var(--green-dark)); padding:14px 28px; }
  .topbar .hamburger{ width:20px; height:16px; display:flex; flex-direction:column; justify-content:space-between; cursor:pointer; }
  .topbar .hamburger span{ display:block; height:2px; background:#eaf0ea; border-radius:2px; }
  .search-box{ flex:1; max-width:420px; display:flex; align-items:center; gap:8px; background:rgba(255,255,255,0.92); border-radius:8px; padding:9px 14px; }
  .search-box svg{ width:15px; height:15px; color:#8a9690; }
  .search-box input{ border:none; outline:none; background:transparent; font-size:13.5px; width:100%; }
  .topbar-right{ margin-left:auto; }
  .topbar-icon{ width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.9); color:var(--green-dark); display:flex; align-items:center; justify-content:center; cursor:pointer; }
  .topbar-icon svg{ width:16px; height:16px; }

  .content{ padding:28px 32px 48px 32px; flex:1; }
  .page-head{ display:flex; align-items:center; gap:12px; margin-bottom:20px; }
  .back-arrow{ width:34px; height:34px; border-radius:8px; background:var(--card-bg); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-mid); }
  .page-head h1{ font-size:20px; font-weight:700; margin:0; }

  .btn{ font-size:12px; font-weight:600; padding:9px 16px; border-radius:7px; border:1px solid var(--border); background:#fff; color:var(--text-mid); cursor:pointer; font-family:var(--font-body); }
  .btn:hover:not(:disabled){ background:#f7f9f7; }
  .btn:disabled{ opacity:.45; cursor:not-allowed; }
  .btn.primary{ background:var(--green-btn); border-color:var(--green-btn); color:#fff; }
  .btn.primary:hover:not(:disabled){ background:var(--green-btn-hover); }
  .btn.warn{ background:#fbeceb; border-color:#f2cfcc; color:var(--status-occupied); }
  .btn.sm{ padding:6px 12px; font-size:11px; }

  .filters{ display:flex; gap:8px; margin-bottom:18px; flex-wrap:wrap; }
  .filter-chip{ border:1px solid var(--border); background:#fff; border-radius:20px; padding:7px 16px; font-size:12px; font-weight:600; color:var(--text-mid); cursor:pointer; }
  .filter-chip.active{ background:var(--status-vacant-bg); border-color:var(--status-vacant); color:var(--green-accent); }

  .contract-grid{ display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:16px; }
  .contract-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:18px 20px; }
  .cc-top{ display:flex; align-items:flex-start; gap:10px; margin-bottom:10px; }
  .cc-name{ font-size:14px; font-weight:700; }
  .cc-room{ font-size:11.5px; color:var(--text-light); margin-top:2px; }
  .badge{ font-size:9.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; padding:4px 9px; border-radius:20px; margin-left:auto; white-space:nowrap; }
  .badge.pending{ background:var(--status-maintenance-bg); color:var(--status-maintenance); }
  .badge.active{ background:var(--status-vacant-bg); color:var(--green-accent); }
  .badge.terminated,.badge.expired{ background:#f0f1f0; color:var(--text-light); }
  .cc-meta{ font-size:11.5px; color:var(--text-mid); line-height:1.7; margin-bottom:14px; }
  .cc-meta b{ color:var(--text-dark); }
  .empty{ color:var(--text-light); font-size:13px; font-style:italic; padding:30px; text-align:center; }

  /* Detail drawer */
  .overlay{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:50; }
  .overlay.open{ display:block; }
  .drawer{ position:fixed; top:0; right:0; height:100%; width:min(640px,100%); background:#fff; z-index:51; transform:translateX(100%); transition:transform .25s ease; overflow-y:auto; display:none; }
  .drawer.open{ transform:translateX(0); display:block; }
  .drawer-head{ position:sticky; top:0; background:#fff; border-bottom:1px solid var(--border); padding:18px 24px; display:flex; align-items:center; gap:12px; z-index:2; }
  .drawer-head h2{ font-size:16px; font-weight:700; margin:0; }
  .drawer-close{ margin-left:auto; background:none; border:none; font-size:22px; color:var(--text-light); cursor:pointer; line-height:1; }
  .drawer-body{ padding:22px 24px 40px 24px; }

  .sec{ margin-bottom:26px; }
  .sec h3{ font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--green-accent); margin:0 0 12px 0; padding-bottom:7px; border-bottom:2px solid #e2ede3; }
  .kv{ display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:12px 20px; }
  .kv .k{ font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-light); }
  .kv .v{ font-size:13px; font-weight:500; margin-top:3px; word-break:break-word; }
  .kv .v.empty-v{ color:#c2c9c5; font-style:italic; font-weight:400; }

  .clause{ background:#fbfcfb; border:1px solid var(--border); border-radius:10px; padding:16px 18px; font-size:12.5px; line-height:1.75; color:var(--text-dark); }
  .clause p{ margin:0 0 11px 0; }
  .clause p:last-child{ margin-bottom:0; }
  .clause ol{ margin:0 0 11px 0; padding-left:20px; }
  .clause li{ margin-bottom:8px; }
  .clause .dpa{ font-size:11.5px; color:var(--text-mid); border-top:1px solid var(--border); padding-top:11px; margin-top:13px; }

  .sign-box{ border:1px solid var(--border); border-radius:10px; padding:16px 18px; background:#fbfcfb; }
  .sign-box .fld{ display:flex; flex-direction:column; gap:6px; margin-bottom:14px; }
  .sign-box label{ font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-mid); }
  .sign-box input[type=file],.sign-box input[type=date]{ border:1px solid var(--border); border-radius:8px; padding:9px 12px; font-size:13px; font-family:var(--font-body); background:#fff; }
  .confirm-row{ display:flex; gap:9px; align-items:flex-start; font-size:12px; color:var(--text-mid); line-height:1.6; margin-bottom:14px; }
  .confirm-row input{ margin-top:2px; accent-color:var(--green-accent); width:15px; height:15px; flex-shrink:0; }
  .signed-note{ background:var(--status-vacant-bg); border:1px solid var(--status-vacant); border-radius:10px; padding:14px 16px; font-size:12.5px; color:var(--green-accent); line-height:1.6; }
  .signed-note a{ color:var(--green-accent); font-weight:700; }

  .drawer-actions{ display:flex; gap:9px; flex-wrap:wrap; padding-top:18px; border-top:1px solid var(--border); }

  .toast{ position:fixed; bottom:22px; right:22px; background:var(--green-accent); color:#fff; padding:12px 20px; border-radius:8px; font-size:13px; display:none; z-index:99; box-shadow:0 6px 18px rgba(0,0,0,.2); }
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
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/></svg></span>Applications</li>
      <li class="nav-item" data-href="{{ route('inquiries.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></span>Inquiries</li>
      <li class="nav-item" data-href="{{ route('vr.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 3v18M16 3v18"/></svg></span>VR Management</li>
      <li class="nav-item active" data-href="{{ route('contracts.index') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg></span>Lease Management</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/></svg></span>Admin Privileges</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 21V9l8-6 8 6v12"/><path d="M9 21v-6h6v6"/></svg></span>Business Information</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M6 10h4M6 14h2"/></svg></span>Dormitory Profile</li>
    </ul>
    <div class="sidebar-footer"><div class="nav-item" id="logoutBtn"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.4 5.6a9 9 0 11-12.8 0M12 3v8"/></svg></span>Log Out</div></div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div class="hamburger"><span></span><span></span><span></span></div>
      <div class="search-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg><input type="text" id="searchInput" placeholder="Search tenant or room"></div>
      <div class="topbar-right">
        <div class="topbar-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
      </div>
    </div>

    <div class="content">
      <div class="page-head">
        <div class="back-arrow" data-href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
        <h1>Contract Management</h1>
      </div>

      <div class="filters" id="filters">
        <div class="filter-chip active" data-filter="all">All</div>
        <div class="filter-chip" data-filter="pending">Awaiting Signature</div>
        <div class="filter-chip" data-filter="active">Active</div>
        <div class="filter-chip" data-filter="terminated">Terminated</div>
      </div>

      <div class="contract-grid" id="contractGrid"></div>
    </div>
  </div>
</div>

<div class="overlay" id="overlay"></div>

<div class="drawer" id="drawer">
  <div class="drawer-head">
    <h2 id="drawerTitle">Contract</h2>
    <button class="drawer-close" id="drawerClose">&times;</button>
  </div>
  <div class="drawer-body" id="drawerBody"></div>
</div>

<div class="toast" id="toast"></div>

<script type="application/json" id="contracts-data">{!! json_encode($contracts) !!}</script>
<script type="application/json" id="dorm-data">{!! json_encode(['name' => $dormName, 'address' => $dormAddress]) !!}</script>

<script>
(function(){
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  let contracts = JSON.parse(document.getElementById('contracts-data').textContent);
  const dorm = JSON.parse(document.getElementById('dorm-data').textContent);

  let filter = 'all';
  let search = '';
  let openId = null;

  const $ = id => document.getElementById(id);

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

  async function api(url, options = {}){
    const headers = Object.assign({ 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' }, options.headers || {});
    const res = await fetch(url, Object.assign({}, options, { headers }));
    const body = await res.json().catch(() => ({}));
    if(!res.ok){
      throw new Error(body.message || (body.errors ? Object.values(body.errors)[0][0] : `Request failed (${res.status})`));
    }
    return body;
  }

  function visible(){
    return contracts.filter(c => {
      if(filter !== 'all' && c.status !== filter) return false;
      if(!search) return true;
      const hay = `${c.tenant.full_name ?? ''} ${c.room_no ?? ''} ${c.bed_label ?? ''}`.toLowerCase();
      return hay.includes(search);
    });
  }

  function renderGrid(){
    const list = visible();

    if(list.length === 0){
      $('contractGrid').innerHTML = '<div class="empty">No contracts match this view.</div>';
      return;
    }

    $('contractGrid').innerHTML = list.map(c => `
      <div class="contract-card" data-open="${c.id}" style="cursor:pointer;">
        <div class="cc-top">
          <div>
            <div class="cc-name">${esc(c.tenant.full_name ?? 'Unnamed tenant')}</div>
            <div class="cc-room">Room ${esc(c.room_no ?? '—')} · ${esc(c.bed_label ?? '—')}</div>
          </div>
          <span class="badge ${c.status}">${esc(c.status)}</span>
        </div>
        <div class="cc-meta">
          <div><b>Rate:</b> ${peso(c.monthly_rate)}/mo</div>
          <div><b>Starts:</b> ${esc(c.start_date ?? '—')}</div>
          <div><b>Signature:</b> ${c.esign_status === 'signed' ? 'Recorded ' + esc(c.signed_at ?? '') : (c.esign_status === 'not_applicable' ? 'Not required' : 'Awaiting signed copy')}</div>
        </div>
        <button class="btn ${c.esign_status === 'pending' ? 'primary' : ''}" style="width:100%;">
          ${c.esign_status === 'pending' ? 'Record Signed Contract' : 'View Contract'}
        </button>
      </div>`).join('');

    $('contractGrid').querySelectorAll('[data-open]').forEach(card => {
      card.addEventListener('click', () => openDrawer(Number(card.dataset.open)));
    });
  }

  /**
   * The emergency-contact clause, matching the printed contract the tenant
   * signs. Shown here so the admin can read back the exact terms while
   * recording the signed copy. Source: contract-clause-draft.md (BAGUI).
   */
  function clauseHtml(c){
    return `
      <div class="clause">
        <p><strong>Section — Consent to Contact and Emergency Contact Use</strong></p>
        <p>The Tenant acknowledges and agrees that the contact number, email address, and
        emergency contact details provided in this contract and in the Tenant's application
        may be used by ${esc(dorm.name)} (the "Dormitory") for the following purposes:</p>
        <ol>
          <li><strong>Routine dormitory communication</strong> — billing statements, payment
          reminders, maintenance schedules, dormitory announcements, and other matters
          relating to the Tenant's stay.</li>
          <li><strong>Emergencies</strong> — in the event of a medical emergency, fire, natural
          disaster, security incident, or any circumstance where the Dormitory reasonably
          believes the Tenant's health or safety is at risk, the Dormitory may contact the
          Tenant's designated emergency contact and, where necessary, disclose the Tenant's
          name, unit assignment, and the nature of the emergency.</li>
          <li><strong>Unpaid rent where the Tenant becomes unreachable</strong> — where the
          Tenant has an outstanding balance and has not responded to at least three (3)
          documented attempts to reach them using their own contact details across a period
          of not less than fourteen (14) days, the Dormitory may contact the Tenant's
          designated emergency contact for the sole purpose of re-establishing contact with
          the Tenant. The Dormitory shall disclose only that the Tenant has an outstanding
          dormitory account and that the Dormitory has been unable to reach them, and shall
          <strong>not</strong> disclose the amount owed, the payment history, or any other
          financial detail.</li>
          <li>The Tenant confirms that they have informed their designated emergency contact
          that their details have been provided for these purposes.</li>
        </ol>
        <div class="dpa">
          <strong>Data Privacy.</strong> All personal information collected is processed in
          accordance with Republic Act No. 10173 (Data Privacy Act of 2012). The Tenant may
          request access to, correction of, or erasure of their personal information by
          written request to dormitory management, subject to the Dormitory's legal and
          contractual retention obligations.
        </div>
      </div>`;
  }

  function signSectionHtml(c){
    if(c.esign_status === 'signed'){
      return `
        <div class="signed-note">
          <strong>Signed copy recorded${c.signed_at ? ' on ' + esc(c.signed_at) : ''}.</strong><br>
          ${c.signed_document_url ? `<a href="${c.signed_document_url}" target="_blank" rel="noopener">View the signed document</a>` : 'No file was attached — a URL was recorded instead.'}
        </div>`;
    }

    if(c.esign_status === 'not_applicable'){
      return `<div class="signed-note"><strong>Marked as not requiring a signature.</strong><br>Used for tenants onboarded before the contract process existed.</div>`;
    }

    return `
      <div class="sign-box">
        <div class="fld">
          <label for="signedFile">Scan or photo of the signed contract</label>
          <input type="file" id="signedFile" accept=".pdf,.jpg,.jpeg,.png">
        </div>
        <div class="fld">
          <label for="signedDate">Date signed</label>
          <input type="date" id="signedDate">
        </div>
        <label class="confirm-row">
          <input type="checkbox" id="confirmSign">
          <span>I confirm the tenant has physically signed this contract, that the signed copy
          uploaded above matches the terms shown on this page, and that the emergency-contact
          clause was explained to them.</span>
        </label>
        <button class="btn primary" id="submitSignBtn" style="width:100%;">Record Signed Contract</button>
      </div>`;
  }

  function openDrawer(id){
    const c = contracts.find(x => x.id === id);
    if(!c) return;
    openId = id;

    $('drawerTitle').textContent = `Contract #${c.id} — ${c.tenant.full_name ?? 'Tenant'}`;

    $('drawerBody').innerHTML = `
      <div class="sec">
        <h3>Tenant</h3>
        <div class="kv">
          <div><div class="k">Full name</div>${val(c.tenant.full_name)}</div>
          <div><div class="k">Contact number</div>${val(c.tenant.contact_number)}</div>
          <div><div class="k">Email</div>${val(c.tenant.email)}</div>
          <div><div class="k">Home address</div>${val(c.home_address)}</div>
        </div>
      </div>

      <div class="sec">
        <h3>Emergency Contact</h3>
        <div class="kv">
          <div><div class="k">Name</div>${val(c.tenant.emergency_contact_name)}</div>
          <div><div class="k">Contact number</div>${val(c.tenant.emergency_contact_number)}</div>
        </div>
      </div>

      <div class="sec">
        <h3>Lease Terms</h3>
        <div class="kv">
          <div><div class="k">Room / Bed</div><span class="v">Room ${esc(c.room_no ?? '—')} · ${esc(c.bed_label ?? '—')}</span></div>
          <div><div class="k">Monthly rate</div><span class="v">${peso(c.monthly_rate)}</span></div>
          <div><div class="k">Start date</div>${val(c.start_date)}</div>
          <div><div class="k">End date</div>${val(c.end_date)}</div>
        </div>
      </div>

      <div class="sec">
        <h3>Contract Clause</h3>
        ${clauseHtml(c)}
      </div>

      <div class="sec">
        <h3>Signature</h3>
        ${signSectionHtml(c)}
      </div>

      <div class="sec">
        <h3>Audit Trail</h3>
        <div class="kv">
          <div><div class="k">Created</div>${val(c.created_at)}</div>
          <div><div class="k">Created by</div>${val(c.created_by)}</div>
          <div><div class="k">Approved by</div>${val(c.approved_by)}</div>
          <div><div class="k">Signed at</div>${val(c.signed_at)}</div>
        </div>
      </div>

      <div class="drawer-actions">
        ${c.esign_status === 'pending' ? `<button class="btn" id="markNaBtn">Mark as not required</button>` : ''}
        ${(c.status === 'pending' || c.status === 'active') ? `<button class="btn warn" id="terminateBtn">Terminate Contract</button>` : ''}
      </div>`;

    wireDrawer(c);

    $('overlay').classList.add('open');
    $('drawer').classList.add('open');
  }

  function wireDrawer(c){
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
        try {
          const res = await api(`/lease-contracts/${c.id}/sign`, { method:'POST', body: form });
          applyUpdate(c.id, {
            esign_status:'signed',
            status:'active',
            signed_at: 'just now',
            signed_document_url: res.signed_document_url ?? null,
          });
          toast('Signed contract recorded. Lease is now active.');
          closeDrawer();
        } catch(e){ toast(e.message, true); this.disabled = false; }
      });
    }

    const naBtn = $('markNaBtn');
    if(naBtn){
      naBtn.addEventListener('click', async function(){
        if(!confirm('Mark this contract as not requiring a signature? The lease becomes active immediately.')) return;
        try {
          await api(`/lease-contracts/${c.id}/not-applicable`, { method:'PATCH' });
          applyUpdate(c.id, { esign_status:'not_applicable', status:'active' });
          toast('Contract marked as not requiring a signature.');
          closeDrawer();
        } catch(e){ toast(e.message, true); }
      });
    }

    const termBtn = $('terminateBtn');
    if(termBtn){
      termBtn.addEventListener('click', async function(){
        if(!confirm('Terminate this contract? The bedspace will be released back to vacant.')) return;
        try {
          await api(`/lease-contracts/${c.id}/terminate`, { method:'PATCH' });
          applyUpdate(c.id, { status:'terminated' });
          toast('Contract terminated and bedspace released.');
          closeDrawer();
        } catch(e){ toast(e.message, true); }
      });
    }
  }

  function applyUpdate(id, changes){
    const idx = contracts.findIndex(c => c.id === id);
    if(idx !== -1) contracts[idx] = Object.assign({}, contracts[idx], changes);
    renderGrid();
  }

  function closeDrawer(){
    openId = null;
    $('overlay').classList.remove('open');
    $('drawer').classList.remove('open');
  }

  $('drawerClose').addEventListener('click', closeDrawer);
  $('overlay').addEventListener('click', closeDrawer);

  $('filters').querySelectorAll('[data-filter]').forEach(chip => {
    chip.addEventListener('click', () => {
      filter = chip.dataset.filter;
      $('filters').querySelectorAll('[data-filter]').forEach(c => c.classList.toggle('active', c === chip));
      renderGrid();
    });
  });

  $('searchInput').addEventListener('input', function(){
    search = this.value.trim().toLowerCase();
    renderGrid();
  });

  renderGrid();
})();
</script>

</body>
</html>