<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH - Tenant Manager</title>
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<style>
  /* Shared sidebar/topbar/content-header/reset styles now live in
     public/css/admin.css (linked above). Only page-specific overrides
     and this page's own content styling stay here. */
  :root{
    --purple:#7a4fc9; --purple-bg:#e9defa;
    --blue:#33629e; --blue-bg:#e3ecf7;
  }

  /* Customized vs admin.css: adds background/min-height */
  .main{ flex:1; display:flex; flex-direction:column; min-width:0; background:var(--bg-page); min-height:100vh; }

  /* Customized vs admin.css: light-background icon (not using .avatar-icon) */
  .topbar-icon{ width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.9); color:var(--green-dark); display:flex; align-items:center; justify-content:center; cursor:pointer; }

  /* Customized vs admin.css: tighter/wider content, different page-head spacing, smaller plain back-arrow, different h1 size/color */
  .content{ padding:26px 34px 48px 34px; flex:1; width:100%; max-width:1400px; margin:0 auto; }
  .page-head{ display:flex; align-items:center; gap:12px; margin-bottom:6px; }
  .back-arrow{ width:30px; height:30px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-mid); flex-shrink:0; }
  .page-head h1{ font-size:19px; font-weight:700; margin:0; color:var(--green-accent); }
  .page-sub{ font-size:12.5px; color:var(--text-mid); margin:0 0 22px 42px; }

  .filters-row{ display:flex; gap:12px; align-items:center; margin-bottom:16px; flex-wrap:wrap; }
  .search-input{ flex:1; min-width:220px; border:1px solid var(--border); border-radius:10px; padding:10px 14px; font-size:13px; font-family:var(--font-body); background:#fff; }
  .filters-row select{ border:1px solid var(--border); border-radius:10px; padding:10px 14px; font-size:13px; font-family:var(--font-body); background:#fff; min-width:170px; }
  .btn{ border:1px solid var(--border); background:#fff; border-radius:10px; padding:10px 16px; font-size:13px; font-weight:600; cursor:pointer; color:var(--text-dark); white-space:nowrap; }
  .btn:hover{ background:#f5f6f5; }
  .btn.primary{ background:var(--green-btn); border-color:var(--green-btn); color:#fff; }
  .btn.primary:hover{ background:var(--green-btn-hover); }
  .btn.warn{ background:var(--status-occupied); border-color:var(--status-occupied); color:#fff; }
  .btn.warn:hover{ opacity:0.9; }
  .btn.sm{ padding:8px 12px; font-size:12px; }

  .table-panel{ background:var(--card-bg); border-radius:14px; box-shadow:0 2px 10px rgba(0,0,0,0.04); overflow:hidden; }
  table{ width:100%; border-collapse:collapse; }
  thead th{ text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:.4px; color:var(--text-light); font-weight:700; padding:14px 18px; border-bottom:1px solid var(--border); }
  tbody td{ padding:14px 18px; font-size:13px; border-bottom:1px solid #f1f3f1; vertical-align:middle; }
  tbody tr:last-child td{ border-bottom:none; }
  tbody tr:hover{ background:#fafcfa; }
  .empty-row td{ text-align:center; color:var(--text-light); padding:32px; font-style:italic; }

  .tenant-cell .name{ font-weight:600; font-size:13.5px; }
  .tenant-cell .email{ font-size:12px; color:var(--text-light); margin-top:2px; }
  .not-assigned{ color:var(--text-light); font-style:italic; }

  .status-text{ font-weight:700; font-size:12.5px; }
  .status-text.active{ color:var(--status-vacant); }
  .status-text.delinquent{ color:var(--status-occupied); }
  .status-text.pending_move_in_payment{ color:var(--status-maintenance); }
  .status-text.inactive{ color:var(--text-light); }

  .row-actions{ display:flex; gap:8px; flex-wrap:wrap; }
  .row-actions .btn{ padding:8px 14px; }

  .pagination-row{ display:flex; justify-content:space-between; align-items:center; padding:14px 18px; font-size:12.5px; color:var(--text-mid); }
  .pagination{ display:flex; gap:6px; }
  .pagination button{ border:1px solid var(--border); background:#fff; border-radius:6px; width:28px; height:28px; cursor:pointer; font-size:12px; }
  .pagination button.active{ background:var(--green-btn); border-color:var(--green-btn); color:#fff; }
  .pagination button:disabled{ opacity:0.4; cursor:default; }

  /* ===== Modals ===== */
  .modal-overlay{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:60; align-items:center; justify-content:center; padding:20px; }
  .modal-overlay.open{ display:flex; }
  .modal-box{ background:#fff; border-radius:14px; width:100%; max-width:640px; max-height:90vh; overflow-y:auto; }
  .modal-head{ padding:20px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; }
  .modal-head h2{ font-size:16px; font-weight:700; margin:0; }
  .modal-body{ padding:22px 24px; }
  .modal-body .fld{ display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
  .modal-body label{ font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-mid); }
  .modal-body input,.modal-body select,.modal-body textarea{ border:1px solid var(--border); border-radius:8px; padding:10px 13px; font-size:13px; font-family:var(--font-body); width:100%; }
  .modal-body textarea{ min-height:80px; resize:vertical; }
  .modal-row2{ display:grid; grid-template-columns:1fr 1fr; gap:14px; }
  .modal-section-title{ font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--green-accent); margin:20px 0 12px 0; padding-bottom:6px; border-bottom:1px solid #e2ede3; }
  .modal-section-title:first-child{ margin-top:0; }
  .modal-actions{ display:flex; gap:10px; padding:18px 24px; border-top:1px solid var(--border); }
  .modal-error{ font-size:12px; color:var(--status-occupied); margin:-8px 0 14px 0; display:none; }
  .modal-error.visible{ display:block; }

  /* ===== View drawer ===== */
  .overlay{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:60; }
  .overlay.open{ display:block; }
  .drawer{ position:fixed; top:0; right:-460px; width:440px; max-width:92vw; height:100vh; background:#fff; z-index:61; transition:right .25s ease; overflow-y:auto; box-shadow:-8px 0 24px rgba(0,0,0,.12); }
  .drawer.open{ right:0; }
  .drawer-head{ padding:22px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
  .drawer-head h2{ font-size:17px; margin:0; }
  .drawer-close{ background:none; border:none; font-size:22px; cursor:pointer; color:var(--text-mid); line-height:1; }
  .drawer-body{ padding:22px 24px; }
  .sec{ margin-bottom:22px; }
  .sec h3{ font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--green-accent); margin:0 0 12px 0; padding-bottom:7px; border-bottom:2px solid #e2ede3; }
  .kv{ display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:12px 20px; }
  .kv .k{ font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-light); }
  .kv .v{ font-size:13px; font-weight:500; margin-top:3px; word-break:break-word; }
  .kv .v.empty-v{ color:#c2c9c5; font-style:italic; font-weight:400; }
  .doc-link{ display:inline-block; font-size:12.5px; color:var(--green-accent); font-weight:600; margin-right:14px; }

  /* ===== Tenant search (Add modal reuses none — bed picker only) ===== */
  .tenant-results{ border:1px solid var(--border); border-radius:8px; margin-top:6px; max-height:160px; overflow-y:auto; display:none; }
  .tenant-results.open{ display:block; }

  .toast{ position:fixed; bottom:22px; right:22px; background:var(--green-accent); color:#fff; padding:12px 20px; border-radius:8px; font-size:13px; display:none; z-index:99; box-shadow:0 6px 18px rgba(0,0,0,.2); }
  .toast.error{ background:var(--status-occupied); }
  .toast.visible{ display:block; }

    @media (max-width: 900px){
    .content{ padding:18px 16px 32px 16px; }
    .filters-row{ flex-direction:column; align-items:stretch; }
    .filters-row select, .filters-row .btn{ width:100%; margin-left:0 !important; }
    .table-panel{ overflow-x:auto; }
    table{ min-width:720px; }
  }
</style>
</head>
<body>
<div class="app">

@include('partials.admin-sidebar')

  <div class="main">
    <div class="topbar">
      <div class="hamburger" id="hamburgerBtn"><span></span><span></span><span></span></div>
      <div class="topbar-right">
        <div class="topbar-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
      </div>
    </div>

    <div class="content">
      <div class="page-head">
        <div class="back-arrow" data-href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
        <h1>Tenant Management</h1>
      </div>
      <p class="page-sub" id="pageSub">{{ $totalCount }} Tenants</p>

      <div class="filters-row">
        <input type="text" class="search-input" id="searchInput" placeholder="Search Name or Room No...">
        <select id="statusFilter">
          <option value="">All Statuses</option>
          <option value="active">Active</option>
          <option value="delinquent">Delinquent</option>
          <option value="pending_move_in_payment">Pending Move-In</option>
          <option value="inactive">Inactive</option>
        </select>
        <select id="tenantTypeFilter">
          <option value="">All Tenant Types</option>
          <option value="student">Student</option>
          <option value="working_student">Working Student</option>
          <option value="full_time_employee">Full-time Employee</option>
          <option value="part_time_employee">Part-time Employee</option>
          <option value="transient_worker">Transient Worker</option>
        </select>
        <select id="sortFilter">
          <option value="date_started_desc">Sort: Latest Date Started</option>
          <option value="date_started_asc">Sort: Oldest Date Started</option>
          <option value="name_asc">Sort: Name (A to Z)</option>
          <option value="name_desc">Sort: Name (Z to A)</option>
          <option value="rent_desc">Sort: Rent (High to Low)</option>
          <option value="rent_asc">Sort: Rent (Low to High)</option>
        </select>
        <button class="btn primary" id="openAddModalBtn" style="margin-left:auto;">+ Add New Tenant</button>
      </div>

      <div class="table-panel">
        <table>
          <thead>
            <tr>
              <th>Tenant</th><th>Room &amp; Bed</th><th>Date Started</th><th>Rent /Month</th><th>Status</th><th>Actions</th>
            </tr>
          </thead>
          <tbody id="tableBody"></tbody>
        </table>
        <div class="pagination-row">
          <div id="tableSummary"></div>
          <div class="pagination" id="pagination"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ===== View drawer ===== -->
<div class="overlay" id="overlay"></div>
<div class="drawer" id="drawer">
  <div class="drawer-head">
    <h2 id="drawerTitle">Tenant Profile</h2>
    <button class="drawer-close" id="drawerClose">&times;</button>
  </div>
  <div class="drawer-body" id="drawerBody">Loading…</div>
</div>

<!-- ===== Add New Tenant modal ===== -->
<div class="modal-overlay" id="addModal">
  <div class="modal-box">
    <div class="modal-head"><h2>Add New Tenant</h2></div>
    <div class="modal-body">
      <div class="modal-error" id="addModalError"></div>

      <div class="modal-section-title">Personal Information</div>
      <div class="modal-row2">
        <div class="fld"><label for="addFirstName">First Name</label><input type="text" id="addFirstName"></div>
        <div class="fld"><label for="addLastName">Last Name</label><input type="text" id="addLastName"></div>
      </div>
      <div class="modal-row2">
        <div class="fld"><label for="addDob">Date of Birth</label><input type="date" id="addDob"></div>
        <div class="fld"><label for="addTenantType">Tenant Type</label>
          <select id="addTenantType">
            <option value="">Select type</option>
            <option value="student">Student</option>
            <option value="working_student">Working Student</option>
            <option value="full_time_employee">Full-time Employee</option>
            <option value="part_time_employee">Part-time Employee</option>
            <option value="transient_worker">Transient Worker</option>
          </select>
        </div>
      </div>
      <div class="fld"><label for="addHomeAddress">Home Address</label><input type="text" id="addHomeAddress"></div>
      <div class="modal-row2">
        <div class="fld"><label for="addContactNumber">Contact Number</label><input type="text" id="addContactNumber"></div>
        <div class="fld"><label for="addEmail">Email Address</label><input type="email" id="addEmail"></div>
      </div>
      <div class="modal-row2">
        <div class="fld"><label for="addEmergencyName">Emergency Contact Name</label><input type="text" id="addEmergencyName"></div>
        <div class="fld"><label for="addEmergencyNumber">Emergency Contact Number</label><input type="text" id="addEmergencyNumber"></div>
      </div>

      <div class="modal-section-title">Documents</div>
      <div class="fld"><label for="addIdDocument">Valid ID</label><input type="file" id="addIdDocument" accept=".pdf,.jpg,.jpeg,.png"></div>
      <div class="fld"><label for="addSignedContract">Signed Contract (optional)</label><input type="file" id="addSignedContract" accept=".pdf,.jpg,.jpeg,.png"></div>

      <div class="modal-section-title">Room &amp; Lease</div>
      <div class="fld"><label for="addRoomSelect">Room</label><select id="addRoomSelect"><option value="">Loading rooms…</option></select></div>
      <div class="fld"><label for="addBedSelect">Bedspace</label><select id="addBedSelect"><option value="">Select a room first</option></select></div>
      <div class="modal-row2">
        <div class="fld"><label for="addStartDate">Lease Start Date</label><input type="date" id="addStartDate"></div>
        <div class="fld"><label for="addEndDate">Lease End Date</label><input type="date" id="addEndDate"></div>
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn primary" id="submitAddBtn" style="flex:1;">Register Tenant</button>
      <button class="btn" id="cancelAddBtn">Cancel</button>
    </div>
  </div>
</div>

<!-- ===== Edit Tenant modal ===== -->
<div class="modal-overlay" id="editModal">
  <div class="modal-box">
    <div class="modal-head"><h2>Edit Tenant</h2></div>
    <div class="modal-body">
      <div class="modal-error" id="editModalError"></div>
      <input type="hidden" id="editTenantId">
      <div class="modal-row2">
        <div class="fld"><label for="editFirstName">First Name</label><input type="text" id="editFirstName"></div>
        <div class="fld"><label for="editLastName">Last Name</label><input type="text" id="editLastName"></div>
      </div>
      <div class="modal-row2">
        <div class="fld"><label for="editDob">Date of Birth</label><input type="date" id="editDob"></div>
        <div class="fld"><label for="editTenantType">Tenant Type</label>
          <select id="editTenantType">
            <option value="">Select type</option>
            <option value="student">Student</option>
            <option value="working_student">Working Student</option>
            <option value="full_time_employee">Full-time Employee</option>
            <option value="part_time_employee">Part-time Employee</option>
            <option value="transient_worker">Transient Worker</option>
          </select>
        </div>
      </div>
      <div class="fld"><label for="editHomeAddress">Home Address</label><input type="text" id="editHomeAddress"></div>
      <div class="modal-row2">
        <div class="fld"><label for="editContactNumber">Contact Number</label><input type="text" id="editContactNumber"></div>
        <div class="fld"><label for="editEmail">Email Address</label><input type="email" id="editEmail"></div>
      </div>
      <div class="modal-row2">
        <div class="fld"><label for="editEmergencyName">Emergency Contact Name</label><input type="text" id="editEmergencyName"></div>
        <div class="fld"><label for="editEmergencyNumber">Emergency Contact Number</label><input type="text" id="editEmergencyNumber"></div>
      </div>
      <div class="modal-section-title">Documents (leave blank to keep existing)</div>
      <div class="fld"><label for="editIdDocument">Replace Valid ID</label><input type="file" id="editIdDocument" accept=".pdf,.jpg,.jpeg,.png"></div>
      <div class="fld"><label for="editSignedContract">Replace Signed Contract</label><input type="file" id="editSignedContract" accept=".pdf,.jpg,.jpeg,.png"></div>
    </div>
    <div class="modal-actions">
      <button class="btn primary" id="submitEditBtn" style="flex:1;">Save Changes</button>
      <button class="btn" id="cancelEditBtn">Cancel</button>
    </div>
  </div>
</div>

<!-- ===== Set Status modal ===== -->
<div class="modal-overlay" id="statusModal">
  <div class="modal-box">
    <div class="modal-head"><h2>Set Status</h2></div>
    <div class="modal-body">
      <div class="modal-error" id="statusModalError"></div>
      <input type="hidden" id="statusTenantId">
      <div class="fld">
        <label for="statusSelect">New Status</label>
        <select id="statusSelect">
          <option value="active">Active</option>
          <option value="inactive">Inactive (Deactivate)</option>
        </select>
      </div>
      <div class="fld" id="statusReasonFld" style="display:none;">
        <label for="statusReason">Reason for Deactivation</label>
        <textarea id="statusReason" placeholder="e.g. Tenant moved out, lease ended..."></textarea>
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn warn" id="submitStatusBtn" style="flex:1;">Confirm</button>
      <button class="btn" id="cancelStatusBtn">Cancel</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script type="application/json" id="tenants-data">{!! json_encode($tenants) !!}</script>

<script>
(function(){
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  let tenants = JSON.parse(document.getElementById('tenants-data').textContent);

  let search = '';
  let statusFilter = '';
  let tenantTypeFilter = '';
  let sortBy = 'date_started_desc';
  let page = 1;
  const PAGE_SIZE = 10;

  // Combines the lease start date with the contract's created_at time of
  // day, so two tenants who started on the same calendar date still sort
  // by which record was actually created first/most recently, not just
  // left in whatever order the database happened to return them.
  function dateStartedSortValue(t){
    if(!t.date_started_raw) return null;
    const datePart = new Date(t.date_started_raw + 'T00:00:00').getTime();
    const timeOfDay = t.contract_created_raw ? (new Date(t.contract_created_raw).getTime() % 86400000) : 0;
    return datePart + timeOfDay;
  }

  function compareTenants(a, b){
    if(sortBy === 'name_asc') return (a.full_name || '').localeCompare(b.full_name || '');
    if(sortBy === 'name_desc') return (b.full_name || '').localeCompare(a.full_name || '');
    if(sortBy === 'rent_desc') return (parseFloat(b.monthly_rate) || 0) - (parseFloat(a.monthly_rate) || 0);
    if(sortBy === 'rent_asc') return (parseFloat(a.monthly_rate) || 0) - (parseFloat(b.monthly_rate) || 0);

    // date_started_desc / date_started_asc -- tenants with no active
    // contract (no start date at all) always sort to the very end,
    // regardless of direction, rather than clumping unpredictably at
    // whichever end -Infinity/Infinity math would put them.
    const av = dateStartedSortValue(a);
    const bv = dateStartedSortValue(b);
    if(av === null && bv === null) return 0;
    if(av === null) return 1;
    if(bv === null) return -1;
    return sortBy === 'date_started_asc' ? av - bv : bv - av;
  }

  const $ = id => document.getElementById(id);
  const STATUS_LABEL = { active:'Active', delinquent:'Delinquent', pending_move_in_payment:'Pending Move-In', inactive:'Inactive' };

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
    setTimeout(() => el.classList.remove('visible'), 3000);
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
    return (n === null || n === undefined || isNaN(num)) ? '—' : '₱' + num.toLocaleString('en-PH', { minimumFractionDigits:2 });
  }

  async function api(url, options = {}){
    const headers = Object.assign({ 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' }, options.headers || {});
    const res = await fetch(url, Object.assign({}, options, { headers }));
    const body = await res.json().catch(() => ({}));
    if(!res.ok){
      const err = new Error(body.message || (body.errors ? Object.values(body.errors)[0][0] : `Request failed (${res.status})`));
      err.status = res.status;
      throw err;
    }
    return body;
  }

  function visible(){
    return tenants.filter(t => {
      if(statusFilter && t.status !== statusFilter) return false;
      if(tenantTypeFilter && t.tenant_type !== tenantTypeFilter) return false;
      if(!search) return true;
      const hay = `${t.full_name ?? ''} ${t.room_bed ?? ''}`.toLowerCase();
      return hay.includes(search);
    }).sort(compareTenants);
  }

  function countActiveTenants(){
    return tenants.filter(t => t.raw_status === 'active' && !t.is_blacklisted).length;
  }

  function renderTable(){
    const all = visible();
    const totalPages = Math.max(1, Math.ceil(all.length / PAGE_SIZE));
    if(page > totalPages) page = totalPages;
    const start = (page - 1) * PAGE_SIZE;
    const pageItems = all.slice(start, start + PAGE_SIZE);

    $('pageSub').textContent = `${countActiveTenants()} Tenants across all floors`;

    if(pageItems.length === 0){
      $('tableBody').innerHTML = '<tr class="empty-row"><td colspan="6">No records found.</td></tr>';
    } else {
      $('tableBody').innerHTML = pageItems.map(t => `
        <tr>
          <td class="tenant-cell">
            <div class="name">${esc(t.full_name)}</div>
            <div class="email">${esc(t.email ?? '')}</div>
          </td>
          <td>${t.room_bed ? esc(t.room_bed) : '<span class="not-assigned">Not Assigned</span>'}</td>
          <td>${t.date_started ? esc(t.date_started) : '—'}</td>
          <td>${peso(t.monthly_rate)}</td>
          <td><span class="status-text ${t.status}">${STATUS_LABEL[t.status] ?? t.status}</span></td>
          <td>
            <div class="row-actions">
              <button class="btn sm" data-view="${t.id}">View</button>
              <button class="btn sm" data-edit="${t.id}">Edit</button>
              <button class="btn sm" data-status="${t.id}">Set Status</button>
            </div>
          </td>
        </tr>`).join('');
    }

    $('tableBody').querySelectorAll('[data-view]').forEach(b => b.addEventListener('click', () => openDrawer(Number(b.dataset.view))));
    $('tableBody').querySelectorAll('[data-edit]').forEach(b => b.addEventListener('click', () => openEditModal(Number(b.dataset.edit))));
    $('tableBody').querySelectorAll('[data-status]').forEach(b => b.addEventListener('click', () => openStatusModal(Number(b.dataset.status))));
    
    renderPagination(all.length, totalPages);
  }

  function renderPagination(total, totalPages){
    const shownStart = total === 0 ? 0 : (page - 1) * PAGE_SIZE + 1;
    const shownEnd = Math.min(page * PAGE_SIZE, total);
    $('tableSummary').textContent = total === 0 ? 'No results' : `Showing ${shownStart}-${shownEnd} of ${total}`;

    let html = `<button ${page === 1 ? 'disabled' : ''} data-page="${page - 1}">‹</button>`;
    for(let p = 1; p <= totalPages; p++){
      html += `<button class="${p === page ? 'active' : ''}" data-page="${p}">${p}</button>`;
    }
    html += `<button ${page === totalPages ? 'disabled' : ''} data-page="${page + 1}">›</button>`;
    $('pagination').innerHTML = html;
    $('pagination').querySelectorAll('[data-page]').forEach(b => {
      b.addEventListener('click', () => { page = Number(b.dataset.page); renderTable(); });
    });
  }

  $('searchInput').addEventListener('input', function(){
    search = this.value.trim().toLowerCase();
    page = 1;
    renderTable();
  });
  $('statusFilter').addEventListener('change', function(){
    statusFilter = this.value;
    page = 1;
    renderTable();
  });
  $('tenantTypeFilter').addEventListener('change', function(){
    tenantTypeFilter = this.value;
    page = 1;
    renderTable();
  });
  $('sortFilter').addEventListener('change', function(){
    sortBy = this.value;
    page = 1;
    renderTable();
  });

  // ===== View drawer =====
  function closeDrawer(){
    $('overlay').classList.remove('open');
    $('drawer').classList.remove('open');
  }
  $('drawerClose').addEventListener('click', closeDrawer);
  $('overlay').addEventListener('click', closeDrawer);

  async function openDrawer(id){
    $('drawerBody').innerHTML = 'Loading…';
    $('overlay').classList.add('open');
    $('drawer').classList.add('open');
    try {
      const t = await api(`/tenant-manager/${id}`);
      $('drawerTitle').textContent = t.full_name;
      $('drawerBody').innerHTML = `
        <div class="sec">
          <h3>Personal Information</h3>
          <div class="kv">
            <div><div class="k">Date of Birth</div>${val(t.date_of_birth)}</div>
            <div><div class="k">Tenant Type</div>${val(t.tenant_type)}</div>
            <div><div class="k">Contact Number</div>${val(t.contact_number)}</div>
            <div><div class="k">Email</div>${val(t.email)}</div>
            <div><div class="k">Home Address</div>${val(t.home_address)}</div>
          </div>
        </div>
        <div class="sec">
          <h3>Emergency Contact</h3>
          <div class="kv">
            <div><div class="k">Name</div>${val(t.emergency_contact_name)}</div>
            <div><div class="k">Number</div>${val(t.emergency_contact_number)}</div>
          </div>
        </div>
        <div class="sec">
          <h3>Room &amp; Lease</h3>
          ${t.contract ? `
          <div class="kv">
            <div><div class="k">Room</div>${val(t.contract.room_no)}</div>
            <div><div class="k">Bed</div>${val(t.contract.bed_label)}</div>
            <div><div class="k">Start Date</div>${val(t.contract.start_date)}</div>
            <div><div class="k">End Date</div>${val(t.contract.end_date)}</div>
            <div><div class="k">Monthly Rate</div>${val(peso(t.contract.monthly_rate))}</div>
          </div>` : '<p style="font-size:12.5px;color:var(--text-light);">No active lease on file.</p>'}
        </div>
        <div class="sec">
          <h3>Billing Summary</h3>
          <div class="kv">
            <div><div class="k">Outstanding Balance</div>${val(peso(t.outstanding_balance))}</div>
            <div><div class="k">Payments Recorded</div>${val(t.payments_count)}</div>
          </div>
        </div>
        <div class="sec">
          <h3>Documents</h3>
          ${t.id_document_url ? `<a class="doc-link" href="${t.id_document_url}" target="_blank">View Valid ID</a>` : '<span class="v empty-v">No valid ID on file</span>'}
          ${t.signed_contract_url ? `<a class="doc-link" href="${t.signed_contract_url}" target="_blank">View Signed Contract</a>` : ''}
        </div>
        ${t.status === 'inactive' ? `
        <div class="sec">
          <h3>Deactivation Record</h3>
          <div class="kv">
            <div><div class="k">Reason</div>${val(t.deactivation_reason)}</div>
            <div><div class="k">Date</div>${val(t.deactivated_at)}</div>
            <div><div class="k">Deactivated By</div>${val(t.deactivated_by_name)}</div>
          </div>
        </div>` : ''}
      `;
    } catch(e){
      $('drawerBody').innerHTML = `<p style="color:var(--status-occupied);">${esc(e.message)}</p>`;
    }
  }

  // ===== Add New Tenant modal =====
  $('openAddModalBtn').addEventListener('click', () => {
    resetAddModal();
    loadRooms($('addRoomSelect'), $('addBedSelect'));
    $('addModal').classList.add('open');
  });
  $('cancelAddBtn').addEventListener('click', () => $('addModal').classList.remove('open'));

  function resetAddModal(){
    $('addModalError').classList.remove('visible');
    ['addFirstName','addLastName','addDob','addHomeAddress','addContactNumber','addEmail','addEmergencyName','addEmergencyNumber','addStartDate','addEndDate'].forEach(id => $(id).value = '');
    $('addTenantType').value = '';
    $('addIdDocument').value = '';
    $('addSignedContract').value = '';
    $('addRoomSelect').innerHTML = '<option value="">Loading rooms…</option>';
    $('addRoomSelect').disabled = false;
    $('addBedSelect').innerHTML = '<option value="">Select a room first</option>';
  }

  function loadRooms(roomSelect, bedSelect){
    fetch('/public-api/rooms')
      .then(r => r.json())
      .then(rooms => {
        // Table 15, step 6: only rooms with at least one vacant bed should
        // be selectable, and the dropdown should show room type. If
        // nothing at all is available, show "No Available Rooms" rather
        // than an empty-looking dropdown.
        const available = rooms.filter(r => (r.beds || []).some(b => b.status === 'vacant'));
        if(available.length === 0){
          roomSelect.innerHTML = '<option value="">No Available Rooms</option>';
          roomSelect.disabled = true;
          return;
        }
        roomSelect.disabled = false;
        roomSelect.innerHTML = '<option value="">Select a room</option>' +
          available.map(r => {
            const vacantCount = (r.beds || []).filter(b => b.status === 'vacant').length;
            const type = r.room_type ? ` (${esc(r.room_type)})` : '';
            return `<option value="${r.id}">Room ${esc(r.room_no)}${type}: ${vacantCount} bed${vacantCount === 1 ? '' : 's'} available</option>`;
          }).join('');
      })
      .catch(() => { roomSelect.innerHTML = '<option value="">Could not load rooms</option>'; });

    roomSelect.onchange = function(){
      bedSelect.innerHTML = '<option value="">Loading beds…</option>';
      if(!this.value){ bedSelect.innerHTML = '<option value="">Select a room first</option>'; return; }
      fetch(`/public-api/rooms/${this.value}/beds`)
        .then(r => r.json())
        .then(beds => {
          bedSelect.innerHTML = beds.length
            ? '<option value="">Select a bed</option>' + beds.map(b => `<option value="${b.id}">${esc(b.bed_label)}</option>`).join('')
            : '<option value="">No Available Rooms</option>';
        })
        .catch(() => { bedSelect.innerHTML = '<option value="">Could not load beds</option>'; });
    };
  }

  $('submitAddBtn').addEventListener('click', async function(){
    const errEl = $('addModalError');
    errEl.classList.remove('visible');

    if(!$('addFirstName').value.trim()) return showAddError('First name is required.');
    if(!$('addLastName').value.trim()) return showAddError('Last name is required.');
    if(!$('addEmail').value.trim()) return showAddError('Email address is required.');
    if(!$('addIdDocument').files[0]) return showAddError('A valid ID upload is required.');
    if(!$('addBedSelect').value) return showAddError('Select a bedspace.');
    if(!$('addStartDate').value || !$('addEndDate').value) return showAddError('Set both the lease start and end dates.');

    const form = new FormData();
    form.append('first_name', $('addFirstName').value.trim());
    form.append('last_name', $('addLastName').value.trim());
    form.append('date_of_birth', $('addDob').value);
    form.append('tenant_type', $('addTenantType').value);
    form.append('home_address', $('addHomeAddress').value.trim());
    form.append('contact_number', $('addContactNumber').value.trim());
    form.append('email', $('addEmail').value.trim());
    form.append('emergency_contact_name', $('addEmergencyName').value.trim());
    form.append('emergency_contact_number', $('addEmergencyNumber').value.trim());
    form.append('bed_id', $('addBedSelect').value);
    form.append('start_date', $('addStartDate').value);
    form.append('end_date', $('addEndDate').value);
    form.append('id_document', $('addIdDocument').files[0]);
    if($('addSignedContract').files[0]) form.append('signed_contract', $('addSignedContract').files[0]);

    this.disabled = true;
    try {
      const result = await api('/tenant-manager', { method:'POST', body: form });
      tenants.push(result.tenant);
      renderTable();
      $('addModal').classList.remove('open');
      toast(result.message);
    } catch(e){ showAddError(e.message); }
    this.disabled = false;
  });

  function showAddError(msg){
    const el = $('addModalError');
    el.textContent = msg;
    el.classList.add('visible');
  }

  // ===== Edit Tenant modal =====
  async function openEditModal(id){
    try {
      const t = await api(`/tenant-manager/${id}`);
      $('editModalError').classList.remove('visible');
      $('editTenantId').value = t.id;
      $('editFirstName').value = t.first_name ?? '';
      $('editLastName').value = t.last_name ?? '';
      $('editDob').value = t.date_of_birth ?? '';
      $('editTenantType').value = t.tenant_type ?? '';
      $('editHomeAddress').value = t.home_address ?? '';
      $('editContactNumber').value = t.contact_number ?? '';
      $('editEmail').value = t.email ?? '';
      $('editEmergencyName').value = t.emergency_contact_name ?? '';
      $('editEmergencyNumber').value = t.emergency_contact_number ?? '';
      $('editIdDocument').value = '';
      $('editSignedContract').value = '';
      $('editModal').classList.add('open');
    } catch(e){ toast(e.message, true); }
  }
  $('cancelEditBtn').addEventListener('click', () => $('editModal').classList.remove('open'));

  $('submitEditBtn').addEventListener('click', async function(){
    const errEl = $('editModalError');
    errEl.classList.remove('visible');
    if(!$('editFirstName').value.trim()) return showEditError('First name is required.');
    if(!$('editLastName').value.trim()) return showEditError('Last name is required.');
    if(!$('editEmail').value.trim()) return showEditError('Email address is required.');

    const form = new FormData();
    form.append('first_name', $('editFirstName').value.trim());
    form.append('last_name', $('editLastName').value.trim());
    form.append('date_of_birth', $('editDob').value);
    form.append('tenant_type', $('editTenantType').value);
    form.append('home_address', $('editHomeAddress').value.trim());
    form.append('contact_number', $('editContactNumber').value.trim());
    form.append('email', $('editEmail').value.trim());
    form.append('emergency_contact_name', $('editEmergencyName').value.trim());
    form.append('emergency_contact_number', $('editEmergencyNumber').value.trim());
    if($('editIdDocument').files[0]) form.append('id_document', $('editIdDocument').files[0]);
    if($('editSignedContract').files[0]) form.append('signed_contract', $('editSignedContract').files[0]);

    this.disabled = true;
    try {
      const id = $('editTenantId').value;
      const result = await api(`/tenant-manager/${id}`, { method:'POST', body: form });
      const idx = tenants.findIndex(t => t.id === Number(id));
      if(idx !== -1) tenants[idx] = Object.assign({}, tenants[idx], result.tenant);
      renderTable();
      $('editModal').classList.remove('open');
      toast(result.message);
    } catch(e){ showEditError(e.message); }
    this.disabled = false;
  });

  function showEditError(msg){
    const el = $('editModalError');
    el.textContent = msg;
    el.classList.add('visible');
  }

  // ===== Set Status modal =====
  function openStatusModal(id){
    const t = tenants.find(x => x.id === id);
    $('statusModalError').classList.remove('visible');
    $('statusTenantId').value = id;
    $('statusSelect').value = t && t.status === 'inactive' ? 'active' : 'inactive';
    $('statusReasonFld').style.display = $('statusSelect').value === 'inactive' ? 'block' : 'none';
    $('statusReason').value = '';
    $('statusModal').classList.add('open');
  }
  $('statusSelect').addEventListener('change', function(){
    $('statusReasonFld').style.display = this.value === 'inactive' ? 'block' : 'none';
  });
  $('cancelStatusBtn').addEventListener('click', () => $('statusModal').classList.remove('open'));

  $('submitStatusBtn').addEventListener('click', async function(){
    const errEl = $('statusModalError');
    errEl.classList.remove('visible');
    const status = $('statusSelect').value;
    const reason = $('statusReason').value.trim();
    if(status === 'inactive' && !reason){
      errEl.textContent = 'A reason is required to deactivate this account.';
      errEl.classList.add('visible');
      return;
    }

    this.disabled = true;
    try {
      const id = $('statusTenantId').value;
      const result = await api(`/tenant-manager/${id}/status`, {
        method:'POST', headers:{ 'Content-Type':'application/json' }, body: JSON.stringify({ status, reason }),
      });
      const idx = tenants.findIndex(t => t.id === Number(id));
      if(idx !== -1) tenants[idx] = Object.assign({}, tenants[idx], result.tenant);
      renderTable();
      $('statusModal').classList.remove('open');
      toast(result.message);
    } catch(e){
      errEl.textContent = e.message;
      errEl.classList.add('visible');
    }
    this.disabled = false;
  });

  renderTable();
})();
</script>

<script>
(function(){
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
})();
</script>

</body>
</html>