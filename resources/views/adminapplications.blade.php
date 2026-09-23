<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH - Review Applications</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<style>
  :root{
    --green-dark:#3f6b4a; --green-mid:#4f7c57;
    --green-sidebar-top:#5b8a63; --green-sidebar-bottom:#2c4a35;
    --green-accent:#2f6f3c; --green-btn:#2f6b3a; --green-btn-hover:#255a2f;
    --status-occupied:#d9564f; --status-vacant:#7fc98a; --status-vacant-bg:#d9f2dd;
    --status-maintenance:#c9962f; --status-maintenance-bg:#f6ecd6;
    --purple:#7a4fc9; --purple-bg:#e9defa;
    --bg-page:#eef1ee; --card-bg:#ffffff;
    --text-dark:#243026; --text-mid:#5b6b60; --text-light:#8a9690; --border:#e2e6e2;
    --font-body:'Roboto',-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;
  }
  /* Shared sidebar/topbar/content-header/reset styles now live in
     public/css/admin.css (linked above). Kept here: :root (this page adds
     --purple/--purple-bg not in the shared file), .topbar-icon (this page
     bakes the avatar coloring directly into .topbar-icon instead of using
     the .avatar-icon override class), and .page-head (this page uses a
     20px margin-bottom vs admin.css's 22px). */
  .topbar-icon{ width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.9); color:var(--green-dark); display:flex; align-items:center; justify-content:center; cursor:pointer; }
  .page-head{ display:flex; align-items:center; gap:12px; margin-bottom:20px; }

  .filters{ display:flex; gap:8px; margin-bottom:18px; flex-wrap:wrap; align-items:center; }
  .filters .search-input{ border:1px solid var(--border); border-radius:20px; padding:7px 16px; font-size:12px; font-family:var(--font-body); color:var(--text-dark); min-width:220px; }
  .filter-chip{ border:1px solid var(--border); background:#fff; border-radius:20px; padding:7px 16px; font-size:12px; font-family:inherit; font-weight:600; color:var(--text-mid); cursor:pointer; }
  .filter-chip.active{ background:var(--status-vacant-bg); border-color:var(--status-vacant); color:var(--green-accent); }

  .app-grid{ display:grid; grid-template-columns:repeat(auto-fill,minmax(290px,1fr)); gap:16px; }
  .app-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:18px 20px; cursor:pointer; }
  .ac-top{ display:flex; align-items:flex-start; gap:10px; margin-bottom:10px; }
  .ac-name{ font-size:14px; font-weight:700; }
  .ac-room{ font-size:11.5px; color:var(--text-light); margin-top:2px; }
  .badge{ font-size:9.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; padding:4px 9px; border-radius:20px; margin-left:auto; white-space:nowrap; }
  .badge.pending{ background:var(--status-maintenance-bg); color:var(--status-maintenance); }
  .badge.approved{ background:var(--status-vacant-bg); color:var(--green-accent); }
  .badge.rejected{ background:#fbeceb; color:var(--status-occupied); }
  .badge.re_application_requested{ background:#e3ecf7; color:#33629e; }
  .badge.cancelled{ background:#f0f1f0; color:var(--text-light); }
  .returning-flag{ display:inline-flex; align-items:center; gap:5px; background:var(--purple-bg); color:var(--purple); font-size:10.5px; font-weight:700; padding:4px 10px; border-radius:20px; margin-bottom:10px; }
  .returning-flag svg{ width:11px; height:11px; }
  .ac-meta{ font-size:11.5px; color:var(--text-mid); line-height:1.7; margin-bottom:4px; }
  .ac-meta b{ color:var(--text-dark); }
  .empty{ color:var(--text-light); font-size:13px; font-style:italic; padding:30px; text-align:center; grid-column:1/-1; }

  .btn{ font-size:12px; font-weight:600; padding:10px 18px; border-radius:7px; border:1px solid var(--border); background:#fff; color:var(--text-mid); cursor:pointer; font-family:var(--font-body); }
  .btn:hover:not(:disabled){ background:#f7f9f7; }
  .btn:disabled{ opacity:.5; cursor:not-allowed; }
  .btn.primary{ background:var(--green-btn); border-color:var(--green-btn); color:#fff; }
  .btn.primary:hover:not(:disabled){ background:var(--green-btn-hover); }
  .btn.warn{ background:#fbeceb; border-color:#f2cfcc; color:var(--status-occupied); }
  .btn.warn:hover:not(:disabled){ background:#f6d9d7; }
  .btn.info{ background:#e3ecf7; border-color:#c7d9f0; color:#33629e; }
  .btn.info:hover:not(:disabled){ background:#d3e2f5; }

  /* Drawer */
  .overlay{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:50; }
  .overlay.open{ display:block; }
  .drawer{ position:fixed; top:0; right:0; height:100%; width:min(640px,100%); background:#fff; z-index:51; transform:translateX(100%); transition:transform .25s ease; overflow-y:auto; display:none; }
  .drawer.open{ transform:translateX(0); display:block; }
  .drawer-head{ position:sticky; top:0; background:#fff; border-bottom:1px solid var(--border); padding:18px 24px; display:flex; align-items:center; gap:12px; z-index:2; }
  .drawer-head h2{ font-size:16px; font-weight:700; margin:0; }
  .drawer-close{ margin-left:auto; background:none; border:none; font-size:22px; color:var(--text-light); cursor:pointer; line-height:1; }
  .drawer-body{ padding:22px 24px 40px 24px; }

  .sec{ margin-bottom:24px; }
  .sec h3{ font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--green-accent); margin:0 0 12px 0; padding-bottom:7px; border-bottom:2px solid #e2ede3; }
  .kv{ display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:12px 20px; }
  .kv .k{ font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-light); }
  .kv .v{ font-size:13px; font-weight:500; margin-top:3px; word-break:break-word; }
  .kv .v.empty-v{ color:#c2c9c5; font-style:italic; font-weight:400; }

  .doc-links{ display:flex; gap:10px; flex-wrap:wrap; }
  .doc-link{ display:inline-flex; align-items:center; gap:7px; background:#fbfcfb; border:1px solid var(--border); border-radius:8px; padding:9px 14px; font-size:12.5px; font-weight:600; color:var(--green-accent); text-decoration:none; }
  .doc-link svg{ width:14px; height:14px; }
  .doc-missing{ color:#c2c9c5; font-style:italic; font-size:12.5px; }

  .returning-note{ background:var(--purple-bg); border:1px solid #d9c5f2; border-radius:10px; padding:14px 16px; font-size:12.5px; color:var(--purple); line-height:1.6; margin-bottom:14px; }
  .discount-fld{ display:flex; flex-direction:column; gap:6px; margin-bottom:14px; max-width:220px; }
  .discount-fld label{ font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-mid); }
  .discount-fld input{ border:1px solid var(--border); border-radius:8px; padding:9px 12px; font-size:13px; font-family:var(--font-body); }

  .decision-box{ display:none; border:1px solid var(--border); border-radius:10px; padding:16px; background:#fbfcfb; margin-top:12px; }
  .decision-box.open{ display:block; }
  .decision-box textarea{ width:100%; border:1px solid var(--border); border-radius:8px; padding:11px 13px; font-size:13px; font-family:var(--font-body); min-height:90px; resize:vertical; margin-bottom:12px; }
  .decision-actions{ display:flex; gap:8px; }

  .action-row{ display:flex; gap:10px; flex-wrap:wrap; }
  .final-note{ background:#fbfcfb; border:1px solid var(--border); border-radius:10px; padding:14px 16px; font-size:12.5px; line-height:1.6; color:var(--text-mid); }
  .final-note.rejected{ color:var(--status-occupied); }
  .final-note.re_application_requested{ color:#33629e; }
  .final-note.approved{ color:var(--green-accent); }

  .toast{ position:fixed; bottom:22px; right:22px; background:var(--green-accent); color:#fff; padding:12px 20px; border-radius:8px; font-size:13px; display:none; z-index:99; box-shadow:0 6px 18px rgba(0,0,0,.2); }
  .toast.error{ background:var(--status-occupied); }
  .toast.visible{ display:block; }
</style>
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
        <h1>Review Applications</h1>
      </div>

      <div class="filters" id="filters" role="tablist" aria-label="Filter applications">
        <input type="text" class="search-input" id="searchInput" placeholder="Search applicant or room">
        <button type="button" class="filter-chip active" role="tab" aria-selected="true" data-filter="pending">Pending Review</button>
        <button type="button" class="filter-chip" role="tab" aria-selected="false" data-filter="approved">Approved</button>
        <button type="button" class="filter-chip" role="tab" aria-selected="false" data-filter="rejected">Rejected</button>
        <button type="button" class="filter-chip" role="tab" aria-selected="false" data-filter="re_application_requested">Re-application Requested</button>
        <button type="button" class="filter-chip" role="tab" aria-selected="false" data-filter="all">All</button>
      </div>

      <div class="app-grid" id="appGrid"></div>
    </div>
  </div>
</div>

<div class="overlay" id="overlay"></div>

<div class="drawer" id="drawer" role="dialog" aria-modal="true" aria-labelledby="drawerTitle">
  <div class="drawer-head">
    <h2 id="drawerTitle">Application</h2>
    <button class="drawer-close" id="drawerClose">&times;</button>
  </div>
  <div class="drawer-body" id="drawerBody"></div>
</div>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script type="application/json" id="applications-data">{!! json_encode($applications) !!}</script>

<script>
(function(){
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  let applications = JSON.parse(document.getElementById('applications-data').textContent);

  let filter = 'pending';
  let search = '';

  const $ = id => document.getElementById(id);
  const STATUS_LABEL = { pending:'Pending Review', approved:'Approved', rejected:'Rejected', re_application_requested:'Re-application Requested', cancelled:'Cancelled' };

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
    return applications.filter(a => {
      if(filter !== 'all' && a.status !== filter) return false;
      if(!search) return true;
      const hay = `${a.full_name ?? ''} ${a.room_no ?? ''}`.toLowerCase();
      return hay.includes(search);
    });
  }

  function renderGrid(){
    const list = visible();

    if(list.length === 0){
      $('appGrid').innerHTML = '<div class="empty">No applications match this view.</div>';
      return;
    }

    $('appGrid').innerHTML = list.map(a => `
      <div class="app-card" data-open="${a.id}" tabindex="0" role="button" aria-label="Open application from ${esc(a.full_name)}">
        ${a.returning_tenant ? '<div class="returning-flag"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.5l2.9 6.06 6.6.7-4.9 4.55 1.28 6.55L12 16.9l-5.88 3.46 1.28-6.55L2.5 9.26l6.6-.7z"/></svg>Returning tenant</div>' : ''}
        <div class="ac-top">
          <div>
            <div class="ac-name">${esc(a.full_name)}</div>
            <div class="ac-room">Room ${esc(a.room_no ?? '—')} · ${esc(a.bed_label ?? '—')}</div>
          </div>
          <span class="badge ${a.status}">${STATUS_LABEL[a.status] ?? a.status}</span>
        </div>
        <div class="ac-meta"><b>Move-in:</b> ${esc(a.preferred_start_date ?? '—')}</div>
        <div class="ac-meta"><b>Submitted:</b> ${esc(a.created_at ?? '—')}</div>
      </div>`).join('');

    $('appGrid').querySelectorAll('[data-open]').forEach(card => {
      card.addEventListener('click', () => openDrawer(Number(card.dataset.open)));
      card.addEventListener('keydown', (e) => {
        if(e.key === 'Enter' || e.key === ' '){ e.preventDefault(); openDrawer(Number(card.dataset.open)); }
      });
    });
  }

  function docLinksHtml(a){
    const links = [];
    if(a.id_document_url){
      links.push(`<a class="doc-link" href="${a.id_document_url}" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="M21 15l-5-5L5 21"/></svg>View ID</a>`);
    }
    if(a.signed_contract_url){
      links.push(`<a class="doc-link" href="${a.signed_contract_url}" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>Signed Contract</a>`);
    }
    if(links.length === 0) return '<span class="doc-missing">No documents were attached to this application.</span>';
    return `<div class="doc-links">${links.join('')}</div>`;
  }

  function decisionActionsHtml(a){
    if(a.status !== 'pending') return '';

    return `
      <div class="sec">
        <h3>Decision</h3>

        ${a.returning_tenant ? `
          <div class="returning-note">
            <strong>This applicant matches an existing tenant record</strong> (${esc(a.returning_tenant.full_name)}).
            They may qualify for a returning-tenant discount. Enter an amount below to reduce their monthly rate by that much if approving.
          </div>
          <div class="discount-fld">
            <label for="discountInput">Discount Amount (₱)</label>
            <input type="number" id="discountInput" min="0" step="0.01" placeholder="0.00">
          </div>
        ` : ''}

        <div class="action-row">
          <button class="btn primary" id="approveBtn">Approve</button>
          <button class="btn warn" id="showRejectBtn">Reject</button>
          <button class="btn info" id="showReapplyBtn">Request Re-application</button>
        </div>

        <div class="decision-box" id="rejectBox">
          <label for="rejectReason" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--text-mid);display:block;margin-bottom:8px;">Rejection Reason (sent to applicant)</label>
          <textarea id="rejectReason" placeholder="Explain why this application is being rejected..."></textarea>
          <div class="decision-actions">
            <button class="btn warn" id="confirmRejectBtn">Confirm Rejection</button>
            <button class="btn" id="cancelRejectBtn">Cancel</button>
          </div>
        </div>

        <div class="decision-box" id="reapplyBox">
          <label for="reapplyNote" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--text-mid);display:block;margin-bottom:8px;">Instructions for Re-application (sent to applicant)</label>
          <textarea id="reapplyNote" placeholder="Explain what needs to change before they reapply..."></textarea>
          <div class="decision-actions">
            <button class="btn info" id="confirmReapplyBtn">Confirm Request</button>
            <button class="btn" id="cancelReapplyBtn">Cancel</button>
          </div>
        </div>
      </div>`;
  }

  function outcomeNoteHtml(a){
    if(a.status === 'rejected' && a.rejection_reason){
      return `<div class="sec"><h3>Rejection Reason</h3><div class="final-note rejected">${esc(a.rejection_reason)}</div></div>`;
    }
    if(a.status === 're_application_requested' && a.re_application_note){
      return `<div class="sec"><h3>Re-application Instructions</h3><div class="final-note re_application_requested">${esc(a.re_application_note)}</div></div>`;
    }
    if(a.status === 'approved'){
      return `<div class="sec"><div class="final-note approved">Approved. Tenant account created and login credentials emailed to the applicant.</div></div>`;
    }
    return '';
  }

  function openDrawer(id){
    const a = applications.find(x => x.id === id);
    if(!a) return;

    const tenantTypeLabel = { student:'Student', working_student:'Working Student', full_time_employee:'Full-time Employee', part_time_employee:'Part-time Employee' }[a.type_of_tenant] || a.type_of_tenant;

    $('drawerTitle').textContent = `Application from ${a.full_name}`;

    $('drawerBody').innerHTML = `
      <div class="sec">
        <h3>Personal Information</h3>
        <div class="kv">
          <div><div class="k">Full name</div>${val(a.full_name)}</div>
          <div><div class="k">Birthdate</div>${val(a.birthdate)}</div>
          <div><div class="k">Gender</div>${val(a.gender)}</div>
          <div><div class="k">Nationality</div>${val(a.nationality)}</div>
          <div><div class="k">Medical condition</div>${val(a.medical_condition)}</div>
          <div><div class="k">Occupation</div>${val(a.occupation)}</div>
          <div><div class="k">School/Company</div>${val(a.school_company)}</div>
          <div><div class="k">School/Company address</div>${val(a.school_company_address)}</div>
        </div>
      </div>

      <div class="sec">
        <h3>Contact Information</h3>
        <div class="kv">
          <div><div class="k">Cellphone</div>${val(a.contact_number)}</div>
          <div><div class="k">Email</div>${val(a.email)}</div>
          <div><div class="k">Landline</div>${val(a.landline)}</div>
          <div><div class="k">Home address</div>${val(a.home_address)}</div>
        </div>
      </div>

      <div class="sec">
        <h3>Emergency Contact</h3>
        <div class="kv">
          <div><div class="k">Name</div>${val(a.emergency_contact_name)}</div>
          <div><div class="k">Cellphone</div>${val(a.emergency_contact_number)}</div>
          <div><div class="k">Relation to tenant</div>${val(a.emergency_contact_relation)}</div>
        </div>
      </div>

      <div class="sec">
        <h3>Room Information</h3>
        <div class="kv">
          <div><div class="k">Room / Bed</div><span class="v">Room ${esc(a.room_no ?? '—')} · ${esc(a.bed_label ?? '—')}</span></div>
          <div><div class="k">Monthly rate</div><span class="v">${peso(a.monthly_rate)}</span></div>
          <div><div class="k">Preferred start</div>${val(a.preferred_start_date)}</div>
          <div><div class="k">Tenant end date</div>${val(a.tenant_end_date)}</div>
          <div><div class="k">Type of tenant</div>${val(tenantTypeLabel)}</div>
        </div>
      </div>

      <div class="sec">
        <h3>Uploaded Documents</h3>
        ${docLinksHtml(a)}
      </div>

      ${outcomeNoteHtml(a)}
      ${decisionActionsHtml(a)}
    `;

    if(a.status === 'pending') wireDecisionActions(a);

    $('overlay').classList.add('open');
    $('drawer').classList.add('open');
  }

  function wireDecisionActions(a){
    $('approveBtn').addEventListener('click', async function(){
      this.disabled = true;
      try {
        const discountInput = $('discountInput');
        const payload = {};
        if(discountInput && discountInput.value){
          payload.discount_amount = discountInput.value;
        }

        const result = await api(`/applications/${a.id}/approve`, {
          method:'POST',
          headers:{ 'Content-Type':'application/json' },
          body: JSON.stringify(payload),
        });

        a.status = 'approved';
        renderGrid();
        openDrawer(a.id);
        toast(result.message || 'Application approved.');
      } catch(e){ toast(e.message, true); this.disabled = false; }
    });

    $('showRejectBtn').addEventListener('click', () => {
      $('rejectBox').classList.add('open');
      $('reapplyBox').classList.remove('open');
    });
    $('cancelRejectBtn').addEventListener('click', () => $('rejectBox').classList.remove('open'));

    $('showReapplyBtn').addEventListener('click', () => {
      $('reapplyBox').classList.add('open');
      $('rejectBox').classList.remove('open');
    });
    $('cancelReapplyBtn').addEventListener('click', () => $('reapplyBox').classList.remove('open'));

    $('confirmRejectBtn').addEventListener('click', async function(){
      const reason = $('rejectReason').value.trim();
      if(!reason) return toast('A rejection reason is required.', true);

      this.disabled = true;
      try {
        const result = await api(`/applications/${a.id}/reject`, {
          method:'POST',
          headers:{ 'Content-Type':'application/json' },
          body: JSON.stringify({ reason }),
        });
        a.status = 'rejected';
        a.rejection_reason = reason;
        renderGrid();
        openDrawer(a.id);
        toast(result.message || 'Application rejected.');
      } catch(e){ toast(e.message, true); this.disabled = false; }
    });

    $('confirmReapplyBtn').addEventListener('click', async function(){
      const note = $('reapplyNote').value.trim();
      if(!note) return toast('Instructions for the applicant are required.', true);

      this.disabled = true;
      try {
        const result = await api(`/applications/${a.id}/request-reapplication`, {
          method:'POST',
          headers:{ 'Content-Type':'application/json' },
          body: JSON.stringify({ note }),
        });
        a.status = 're_application_requested';
        a.re_application_note = note;
        renderGrid();
        openDrawer(a.id);
        toast(result.message || 'Re-application request sent.');
      } catch(e){ toast(e.message, true); this.disabled = false; }
    });
  }

  function closeDrawer(){
    $('overlay').classList.remove('open');
    $('drawer').classList.remove('open');
  }

  $('drawerClose').addEventListener('click', closeDrawer);
  $('overlay').addEventListener('click', closeDrawer);

  $('filters').querySelectorAll('[data-filter]').forEach(chip => {
    chip.addEventListener('click', () => {
      filter = chip.dataset.filter;
      $('filters').querySelectorAll('[data-filter]').forEach(c => {
        const active = c === chip;
        c.classList.toggle('active', active);
        c.setAttribute('aria-selected', active ? 'true' : 'false');
      });
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

<script>
(function(){
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
    const drawer = document.getElementById('drawer');
    if (drawer && drawer.classList.contains('open')) document.getElementById('drawerClose').click();
  });
})();
</script>

</body>
</html>