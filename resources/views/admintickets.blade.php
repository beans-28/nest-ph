<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH — Tickets</title>
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<style>
  /* Shared color variables, page reset, sidebar, topbar, and content
     header now live in public/css/admin.css (linked above). This page
     adds two extra status colors (purple/blue) that admin.css doesn't
     define, plus its own ticket-grid/modal/lightbox styling below. */
  :root{
    --purple:#7a4fc9; --purple-bg:#e9defa;
    --blue:#33629e; --blue-bg:#e3ecf7;
  }

  .stats-row{ display:grid; grid-template-columns:repeat(3, 1fr); gap:16px; margin-bottom:20px; }
  .stat-card{ background:var(--card-bg); border-radius:12px; border:1px solid var(--border); padding:18px 20px; display:flex; align-items:center; gap:14px; box-shadow:0 1px 2px rgba(20,30,20,0.03); }
  .stat-icon{ width:46px; height:46px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .stat-icon svg{ width:20px; height:20px; }
  .stat-icon.open{ background:var(--blue-bg); color:var(--blue); }
  .stat-icon.progress{ background:var(--status-maintenance-bg); color:var(--status-maintenance); }
  .stat-icon.overdue{ background:var(--status-occupied-bg); color:var(--status-occupied); }
  .stat-label{ font-size:12.5px; color:var(--text-mid); margin-bottom:3px; }
  .stat-value{ font-size:24px; font-weight:700; color:var(--text-dark); }

  .filters-row{ display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap; }
  .search-input{ flex:1; min-width:200px; border:1px solid var(--border); border-radius:9px; padding:10px 14px; font-size:13px; font-family:var(--font-body); }
  .filters-row select{ border:1px solid var(--border); border-radius:9px; padding:10px 14px; font-size:13px; font-family:var(--font-body); background:#fff; color:var(--text-dark); }

  .empty{ text-align:center; color:var(--text-light); font-style:italic; padding:50px 20px; background:var(--card-bg); border:1px solid var(--border); border-radius:12px; }

  .app-grid{ display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:16px; }
  .app-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:18px; cursor:pointer; display:flex; flex-direction:column; gap:10px; transition:box-shadow .15s ease, border-color .15s ease; }
  .app-card:hover{ box-shadow:0 4px 14px rgba(20,30,20,0.08); border-color:#cfd8d0; }
  .ac-top{ display:flex; justify-content:space-between; align-items:flex-start; gap:10px; }
  .ac-num{ font-size:11.5px; color:var(--text-light); font-weight:600; }
  .ac-title{ font-size:14.5px; font-weight:700; color:var(--text-dark); margin:2px 0 0 0; }
  .ac-meta{ font-size:12px; color:var(--text-mid); }
  .ac-cat{ font-size:11.5px; color:var(--text-mid); background:#f4f7f4; border-radius:6px; padding:4px 9px; display:inline-block; width:fit-content; }

  .badge{ font-size:11px; font-weight:700; padding:4px 10px; border-radius:20px; display:inline-block; white-space:nowrap; }
  .badge.open{ background:var(--blue-bg); color:var(--blue); }
  .badge.seen{ background:var(--purple-bg); color:var(--purple); }
  .badge.in_progress{ background:var(--status-maintenance-bg); color:var(--status-maintenance); }
  .badge.resolved{ background:var(--status-vacant-bg); color:var(--green-accent); }
  .badge.rejected{ background:var(--status-occupied-bg); color:var(--status-occupied); }

  .overdue-flag{ font-size:11px; font-weight:700; color:var(--status-occupied); background:var(--status-occupied-bg); border-radius:20px; padding:4px 10px; display:inline-flex; align-items:center; gap:5px; width:fit-content; }
  .unresolved-note{ font-size:11.5px; color:var(--text-light); }

  .ac-bottom{ display:flex; justify-content:space-between; align-items:center; gap:8px; margin-top:auto; padding-top:10px; border-top:1px solid #f0f2f0; }
  .priority-select{ font-size:11.5px; border:1px solid var(--border); border-radius:6px; padding:5px 8px; font-family:var(--font-body); background:#fff; color:var(--text-mid); }
  .priority-select.urgent{ color:var(--status-occupied); border-color:#f2cfcc; background:var(--status-occupied-bg); font-weight:700; }
  .ac-assigned{ font-size:11.5px; color:var(--text-light); }

  .lightbox{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.8); z-index:90; align-items:center; justify-content:center; padding:30px; }
  .lightbox.open{ display:flex; }
  .lightbox img{ max-width:100%; max-height:100%; border-radius:8px; }
  .modal-overlay{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:60; align-items:center; justify-content:center; padding:20px; }
  .modal-overlay.open{ display:flex; }
  .modal-box{ background:#fff; border-radius:14px; width:100%; max-width:560px; max-height:90vh; overflow-y:auto; }
  .modal-head{ padding:20px 24px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:flex-start; }
  .modal-head .mh-tenant{ font-size:12.5px; color:var(--text-mid); margin-bottom:4px; }
  .modal-head h2{ font-size:16px; font-weight:700; margin:0; }
  .modal-head .mh-num{ font-size:12px; color:var(--text-light); }
  .modal-body{ padding:22px 24px; }
  .modal-body .mb-desc{ font-size:13px; color:var(--text-mid); line-height:1.6; margin-bottom:16px; }
  .modal-body .mb-attachment{ margin-bottom:16px; }
  .modal-body .mb-attachment img{ width:88px; height:88px; object-fit:cover; border-radius:8px; border:1px solid var(--border); cursor:pointer; }
  .modal-body .fld{ display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
  .modal-body label{ font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-mid); }
  .modal-body select, .modal-body textarea{ border:1px solid var(--border); border-radius:8px; padding:10px 13px; font-size:13px; font-family:var(--font-body); width:100%; }
  .modal-body textarea{ min-height:80px; resize:vertical; }
  .reply-thread{ display:flex; flex-direction:column; gap:8px; margin-bottom:12px; max-height:160px; overflow-y:auto; }
  .reply-item{ background:#f7f9f7; border-radius:8px; padding:10px 12px; font-size:12.5px; }
  .reply-item .r-meta{ color:var(--text-light); font-size:11px; margin-top:4px; }
  .modal-actions{ display:flex; gap:10px; padding:0 24px 24px 24px; }
  .btn{ border-radius:8px; padding:10px 16px; font-size:13px; font-weight:600; cursor:pointer; border:1px solid var(--border); background:#fff; color:var(--text-dark); font-family:var(--font-body); }
  .btn.primary{ background:var(--green-btn); color:#fff; border-color:var(--green-btn); }
  .btn.primary:hover{ background:var(--green-btn-hover); }

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
      <div class="hamburger" id="hamburgerBtn"><span></span><span></span><span></span></div>
      <div class="topbar-right">
        <div class="topbar-icon avatar-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
      </div>
    </div>

    <div class="content">
      <div class="page-head">
        <div class="back-arrow" data-href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
        <h1>Tickets</h1>
      </div>

      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-icon open"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h18v10H3z"/><path d="M3 12h18"/></svg></div>
          <div><div class="stat-label">Open</div><div class="stat-value" id="statOpen">{{ $openCount }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon progress"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg></div>
          <div><div class="stat-label">In Progress</div><div class="stat-value" id="statProgress">{{ $inProgressCount }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon overdue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg></div>
          <div><div class="stat-label">Overdue</div><div class="stat-value" id="statOverdue">{{ $overdueCount }}</div></div>
        </div>
      </div>

      <div class="filters-row">
        <input type="text" class="search-input" id="searchInput" placeholder="Search tenant or title">
        <select id="categoryFilter">
          <option value="">All Categories</option>
          @foreach($categories as $key => $label)
            <option value="{{ $key }}">{{ $label }}</option>
          @endforeach
        </select>
        <select id="statusFilter">
          <option value="">All Statuses</option>
          @foreach($statuses as $key => $label)
            <option value="{{ $key }}">{{ $label }}</option>
          @endforeach
        </select>
        <select id="priorityFilter">
          <option value="">All Priorities</option>
          @foreach($priorities as $key => $label)
            <option value="{{ $key }}">{{ $label }}</option>
          @endforeach
        </select>
      </div>

      <div class="app-grid" id="ticketGrid"></div>
    </div>
  </div>
</div>

<div class="modal-overlay" id="ticketModal">
  <div class="modal-box">
    <div class="modal-head">
      <div>
        <div class="mh-tenant" id="mTenant"></div>
        <h2 id="mTitle"></h2>
      </div>
      <div class="mh-num" id="mNum"></div>
    </div>
    <div class="modal-body">
      <div class="mb-desc" id="mDesc"></div>
            <div class="mb-attachment" id="mAttachment" style="display:none;">
        <div style="display:flex;gap:8px;flex-wrap:wrap;" id="mAttachmentGrid"></div>
      </div>

      <div class="fld">
        <label>Assign to</label>
        <select id="mAssignSelect"><option value="">Unassigned</option></select>
      </div>

      <div class="fld">
        <label>Update Status</label>
        <select id="mStatusSelect">
          @foreach($statuses as $key => $label)
            <option value="{{ $key }}">{{ $label }}</option>
          @endforeach
        </select>
      </div>

      <div class="fld">
        <label>Reply to tenant</label>
        <div class="reply-thread" id="mReplyThread"></div>
        <textarea id="mReplyInput" placeholder="Write a reply..."></textarea>
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn primary" id="mSaveBtn" style="flex:1;">Save</button>
      <button class="btn" id="mCancelBtn">Cancel</button>
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

  let search = '';
  let categoryFilter = '';
  let statusFilter = '';
  let priorityFilter = '';
  let openTicketId = null;

  const $ = id => document.getElementById(id);

  document.querySelectorAll('[data-href]').forEach(el => {
    el.addEventListener('click', () => { window.location.href = el.dataset.href; });
  });

  $('logoutBtn').addEventListener('click', async () => {
    await fetch('/logout', { method:'POST', headers:{ 'X-CSRF-TOKEN': csrf } });
    window.location.href = '/';
  });

  $('lightbox').addEventListener('click', () => $('lightbox').classList.remove('open'));

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
    return tickets.filter(t => {
      if(categoryFilter && t.category !== categoryFilter) return false;
      if(statusFilter && t.status !== statusFilter) return false;
      if(priorityFilter && t.priority !== priorityFilter) return false;
      if(!search) return true;
      const hay = `${t.tenant_name ?? ''} ${t.title ?? ''}`.toLowerCase();
      return hay.includes(search);
    });
  }

  function priorityOptionsHtml(current){
    const opts = [['', 'Unclassified'], ['urgent', 'Urgent'], ['non_urgent', 'Non-Urgent']];
    return opts.map(([val, label]) =>
      `<option value="${val}" ${current === val || (!current && val==='') ? 'selected' : ''}>${label}</option>`
    ).join('');
  }

  function renderGrid(){
    const list = visible();

    if(list.length === 0){
      $('ticketGrid').innerHTML = '<div class="empty">No tickets match this view.</div>';
      return;
    }

    $('ticketGrid').innerHTML = list.map(t => `
      <div class="app-card" data-open="${t.id}">
        <div class="ac-top">
          <div>
            <div class="ac-num">Ticket#${t.id}</div>
            <div class="ac-title">${esc(t.title)}</div>
            <div class="ac-meta">${esc(t.tenant_name ?? 'Unknown tenant')}${t.room_no ? ' — Room ' + esc(t.room_no) : ''}</div>
          </div>
          <span class="badge ${t.status}">${esc(t.status_label)}</span>
        </div>
        <span class="ac-cat">${esc(t.category_label)}</span>
        ${t.is_overdue ? `<span class="overdue-flag">⚠ Overdue</span>` : (t.unresolved_for ? `<span class="unresolved-note">${esc(t.unresolved_for)}</span>` : '')}
        <div class="ac-bottom">
          <select class="priority-select ${t.priority ?? ''}" data-priority-for="${t.id}">${priorityOptionsHtml(t.priority)}</select>
          <span class="ac-assigned">${t.assigned_to_name ? 'Assigned: ' + esc(t.assigned_to_name) : 'Unassigned'}</span>
        </div>
      </div>
    `).join('');

    document.querySelectorAll('[data-open]').forEach(el => {
      el.addEventListener('click', (e) => {
        if(e.target.closest('[data-priority-for]')) return;
        openModal(Number(el.dataset.open));
      });
    });

    document.querySelectorAll('[data-priority-for]').forEach(el => {
      el.addEventListener('click', e => e.stopPropagation());
      el.addEventListener('change', async () => {
        const id = Number(el.dataset.priorityFor);
        const ticket = tickets.find(t => t.id === id);
        try {
          const res = await api(`/tickets/${id}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: ticket.status, priority: el.value || null }),
          });
          Object.assign(ticket, res.ticket);
          toast('Priority updated.');
          renderGrid();
        } catch(e){ toast(e.message, true); renderGrid(); }
      });
    });
  }

  async function openModal(id){
    openTicketId = id;
    try {
      const detail = await api(`/tickets/${id}`);

      $('mTenant').textContent = detail.tenant_name ?? 'Unknown tenant';
      $('mNum').textContent = `Ticket#${detail.id}`;
      $('mTitle').textContent = detail.title;
      $('mDesc').textContent = detail.description;

      if(detail.attachment_urls && detail.attachment_urls.length){
        $('mAttachment').style.display = 'block';
        $('mAttachmentGrid').innerHTML = detail.attachment_urls.map(u => `<img src="${u}" alt="Attachment" data-lightbox="${u}">`).join('');
        document.querySelectorAll('#mAttachmentGrid [data-lightbox]').forEach(img => {
          img.addEventListener('click', () => {
            $('lightboxImg').src = img.dataset.lightbox;
            $('lightbox').classList.add('open');
          });
        });
      } else {
        $('mAttachment').style.display = 'none';
      }
      $('mAssignSelect').innerHTML = '<option value="">Unassigned</option>' +
        detail.assignable_admins.map(a => `<option value="${a.id}" ${a.id === detail.assigned_to ? 'selected' : ''}>${esc(a.name)}</option>`).join('');

      $('mStatusSelect').value = detail.status;

      $('mReplyThread').innerHTML = detail.replies.length
        ? detail.replies.map(r => `<div class="reply-item">${esc(r.message)}<div class="r-meta">${esc(r.author)} — ${esc(r.created_at)}</div></div>`).join('')
        : '<div class="reply-item" style="color:var(--text-light);font-style:italic;">No replies yet.</div>';

      $('mReplyInput').value = '';

      $('ticketModal').classList.add('open');
    } catch(e){ toast(e.message, true); }
  }

  $('mCancelBtn').addEventListener('click', () => $('ticketModal').classList.remove('open'));
  $('ticketModal').addEventListener('click', (e) => { if(e.target.id === 'ticketModal') $('ticketModal').classList.remove('open'); });

  $('mSaveBtn').addEventListener('click', async function(){
    this.disabled = true;
    try {
      const res = await api(`/tickets/${openTicketId}`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          status: $('mStatusSelect').value,
          assigned_to: $('mAssignSelect').value || null,
          reply_message: $('mReplyInput').value.trim() || null,
        }),
      });

      const idx = tickets.findIndex(t => t.id === openTicketId);
      if(idx !== -1) tickets[idx] = res.ticket;

      $('ticketModal').classList.remove('open');
      toast('Ticket updated successfully.');
      renderGrid();
    } catch(e){ toast(e.message, true); }
    this.disabled = false;
  });

  $('searchInput').addEventListener('input', e => { search = e.target.value.toLowerCase(); renderGrid(); });
  $('categoryFilter').addEventListener('change', e => { categoryFilter = e.target.value; renderGrid(); });
  $('statusFilter').addEventListener('change', e => { statusFilter = e.target.value; renderGrid(); });
  $('priorityFilter').addEventListener('change', e => { priorityFilter = e.target.value; renderGrid(); });

  renderGrid();
})();
</script>

<script>
(function(){
  const SIDEBAR_COLLAPSE_KEY = 'nestph_sidebar_collapsed';
  const hamburgerBtn = document.getElementById('hamburgerBtn');
  const sidebar = document.getElementById('sidebar');
  if (hamburgerBtn && sidebar) {
    if (localStorage.getItem(SIDEBAR_COLLAPSE_KEY) === '1') sidebar.classList.add('collapsed');
    hamburgerBtn.addEventListener('click', () => {
      const collapsed = sidebar.classList.toggle('collapsed');
      localStorage.setItem(SIDEBAR_COLLAPSE_KEY, collapsed ? '1' : '0');
    });
  }
})();
</script>
</body>
</html>