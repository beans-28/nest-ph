<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Owner Requests · NEST.PH</title>
<link rel="icon" href="{{ asset('images/nestphgreen.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<style>
  /* Same look as the dorm admin pages (admin.css + the Inquiries page's
     chips, list and badges). Only this page's own content styling is here. */
  .topbar-icon{ width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.9); color:var(--green-dark); display:flex; align-items:center; justify-content:center; }
  .topbar-name{ color:#eaf0ea; font-size:13px; font-weight:500; }
  .page-head h1{ font-size:22px; margin:0; }
  .page-sub{ font-size:13px; color:var(--text-mid); margin:-12px 0 20px; }
  .flash{ background:var(--status-vacant-bg); color:var(--green-accent); border-radius:8px; padding:10px 14px; font-size:13px; font-weight:600; margin-bottom:16px; }

  .filters{ display:flex; gap:8px; margin-bottom:18px; flex-wrap:wrap; }
  .filter-chip{ border:1px solid var(--border); background:#fff; border-radius:20px; padding:7px 16px; font-size:12px; font-weight:600; color:var(--text-mid); text-decoration:none; }
  .filter-chip.active{ background:var(--status-vacant-bg); border-color:var(--status-vacant); color:var(--green-accent); }
  .filter-chip .n{ margin-left:4px; opacity:.8; }

  .list-panel{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; overflow:hidden; }
  .list-panel details{ border-bottom:1px solid #f0f2f0; }
  .list-panel details:last-child{ border-bottom:none; }
  .list-row{ display:flex; align-items:center; gap:16px; padding:16px 20px; cursor:pointer; list-style:none; }
  .list-row::-webkit-details-marker{ display:none; }
  .list-row:hover{ background:#fbfcfb; }
  .list-row:focus-visible{ outline:2px solid var(--green-accent); outline-offset:-2px; }
  .lr-name{ font-size:13.5px; font-weight:700; min-width:170px; }
  .lr-owner{ font-size:12.5px; color:var(--text-mid); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .lr-date{ font-size:11.5px; color:var(--text-light); white-space:nowrap; }
  .chev{ width:14px; height:14px; color:var(--text-light); transition:transform .2s; flex-shrink:0; }
  details[open] .chev{ transform:rotate(180deg); }

  .badge{ font-size:9.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; padding:4px 10px; border-radius:20px; white-space:nowrap; }
  .badge.new{ background:var(--status-maintenance-bg); color:var(--status-maintenance); }
  .badge.contacted, .badge.demo_scheduled{ background:#e3ecf7; color:#33629e; }
  .badge.onboarded{ background:var(--status-vacant-bg); color:var(--green-accent); }
  .badge.declined{ background:#f0f1f0; color:var(--text-light); }

  .detail{ display:grid; grid-template-columns:1.2fr 1fr; gap:24px; padding:4px 20px 20px; background:#fbfcfb; border-top:1px solid #f0f2f0; }
  .detail h3{ font-size:11px; text-transform:uppercase; letter-spacing:.6px; color:var(--text-light); margin:16px 0 8px; }
  .info{ list-style:none; margin:0; padding:0; font-size:13px; display:grid; gap:4px; }
  .info span{ display:inline-block; min-width:100px; color:var(--text-mid); }
  .info a{ color:var(--green-accent); }
  .msg{ font-size:13px; line-height:1.55; background:#fff; border:1px solid var(--border); border-radius:8px; padding:10px 12px; white-space:pre-line; margin:0; }
  .update label{ display:block; font-size:12px; font-weight:600; color:var(--text-mid); margin:10px 0 6px; }
  .update select, .update textarea{ width:100%; border:1px solid var(--border); border-radius:8px; padding:9px 12px; font-size:13px; font-family:var(--font-body); background:#fff; }
  .update textarea{ min-height:84px; resize:vertical; }
  .btn{ font-size:12px; font-weight:600; padding:10px 18px; border-radius:7px; border:1px solid var(--border); background:#fff; color:var(--text-mid); cursor:pointer; font-family:var(--font-body); margin-top:12px; }
  .btn.primary{ background:var(--green-btn); border-color:var(--green-btn); color:#fff; }
  .btn.primary:hover{ background:var(--green-btn-hover); }
  .empty{ color:var(--text-light); font-size:13px; font-style:italic; padding:30px; text-align:center; }
  .pager{ display:flex; gap:14px; justify-content:center; align-items:center; margin-top:18px; font-size:12.5px; color:var(--text-mid); }
  .pager a{ color:var(--green-accent); font-weight:600; text-decoration:none; }
  .logout-form{ margin:0; }
  .logout-form button{ width:100%; border:0; font:inherit; text-align:left; }

  @media (max-width: 860px){
    .detail{ grid-template-columns:1fr; }
    .lr-owner{ display:none; }
    .lr-name{ min-width:0; flex:1; }
  }
</style>
</head>
<body>
<div class="app">

  <aside class="sidebar" id="sidebar">
    <div class="sidebar-logo" title="NEST.PH"><img src="{{ asset('images/nestph.png') }}" alt="" class="logo-img"><span class="logo-text">NEST.PH</span></div>
    <div class="sidebar-section-label">Platform</div>
    <ul class="nav-list">
      <li class="nav-item active" data-href="{{ route('platform.dashboard') }}" tabindex="0">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></span>
        <span class="label">Owner Requests</span>
      </li>
      <li class="nav-item" data-href="{{ route('platform') }}" tabindex="0">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></span>
        <span class="label">View Landing Page</span>
      </li>
    </ul>
    <div class="sidebar-footer">
      <form method="POST" action="{{ route('logout') }}" class="logout-form">
        @csrf
        <button type="submit" class="nav-item">
          <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.4 5.6a9 9 0 11-12.8 0M12 3v8"/></svg></span>
          <span class="label">Log Out</span>
        </button>
      </form>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div class="hamburger" id="hamburgerBtn" tabindex="0" aria-label="Toggle sidebar"><span></span><span></span><span></span></div>
      <div class="topbar-right">
        <span class="topbar-name">{{ auth()->user()->name }}</span>
        <div class="topbar-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
      </div>
    </div>

    <div class="content">
      <div class="page-head"><h1>Owner Requests</h1></div>
      <p class="page-sub">Dormitory owners who asked to use NEST.PH, from the "Request NEST.PH" form on the landing page.</p>

      @if (session('status'))
        <div class="flash" role="status">{{ session('status') }}</div>
      @endif

      <nav class="filters" aria-label="Filter by status">
        <a href="{{ route('platform.dashboard') }}" class="filter-chip {{ $activeStatus ? '' : 'active' }}" @if(! $activeStatus) aria-current="page" @endif>All<span class="n">{{ $total }}</span></a>
        @foreach ($statuses as $key => $label)
          <a href="{{ route('platform.dashboard', ['status' => $key]) }}" class="filter-chip {{ $activeStatus === $key ? 'active' : '' }}" @if($activeStatus === $key) aria-current="page" @endif>{{ $label }}<span class="n">{{ $counts[$key] ?? 0 }}</span></a>
        @endforeach
      </nav>

      <div class="list-panel">
        @forelse ($requests as $req)
          <details>
            <summary class="list-row">
              <span class="lr-name">{{ $req->dorm_name }}</span>
              <span class="lr-owner">{{ $req->owner_name }}@if($req->city) · {{ $req->city }}@endif</span>
              <span class="badge {{ $req->status }}">{{ $statuses[$req->status] ?? $req->status }}</span>
              <span class="lr-date">{{ $req->created_at->format('M j, Y') }}</span>
              <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
            </summary>
            <div class="detail">
              <div>
                <h3>Owner details</h3>
                <ul class="info">
                  <li><span>Owner</span>{{ $req->owner_name }}</li>
                  <li><span>Email</span><a href="mailto:{{ $req->email }}">{{ $req->email }}</a></li>
                  <li><span>Contact no.</span><a href="tel:{{ $req->contact_number }}">{{ $req->contact_number }}</a></li>
                  @if ($req->city)<li><span>City</span>{{ $req->city }}</li>@endif
                  @if ($req->bed_count)<li><span>Beds</span>~{{ $req->bed_count }}</li>@endif
                  <li><span>Sent</span>{{ $req->created_at->format('M j, Y g:i A') }}</li>
                </ul>
                @if ($req->message)
                  <h3>Message</h3>
                  <p class="msg">{{ $req->message }}</p>
                @endif
              </div>
              <form class="update" method="POST" action="{{ route('platform.requests.update', $req) }}">
                @csrf
                @method('PATCH')
                <h3>Follow-up</h3>
                <label for="status-{{ $req->id }}">Status</label>
                <select id="status-{{ $req->id }}" name="status">
                  @foreach ($statuses as $key => $label)
                    <option value="{{ $key }}" @selected($req->status === $key)>{{ $label }}</option>
                  @endforeach
                </select>
                <label for="notes-{{ $req->id }}">Team notes</label>
                <textarea id="notes-{{ $req->id }}" name="notes" maxlength="2000" placeholder="e.g. Called on Oct 20, demo set for Oct 25">{{ $req->notes }}</textarea>
                <button type="submit" class="btn primary">Save</button>
              </form>
            </div>
          </details>
        @empty
          <div class="empty">No requests {{ $activeStatus ? 'with this status' : 'yet' }}.</div>
        @endforelse
      </div>

      @if ($requests->hasPages())
        <div class="pager">
          @if ($requests->previousPageUrl())<a href="{{ $requests->previousPageUrl() }}">&larr; Newer</a>@endif
          <span>Page {{ $requests->currentPage() }} of {{ $requests->lastPage() }}</span>
          @if ($requests->nextPageUrl())<a href="{{ $requests->nextPageUrl() }}">Older &rarr;</a>@endif
        </div>
      @endif
    </div>
  </div>
</div>

<script src="{{ asset('js/sidebar-drawer.js') }}"></script>
<script>
(function(){
  // Same sidebar behaviour as the dorm admin pages.
  const SIDEBAR_COLLAPSE_KEY = 'nestph_sidebar_collapsed';
  const hamburgerBtn = document.getElementById('hamburgerBtn');
  const sidebar = document.getElementById('sidebar');
  try { if (localStorage.getItem(SIDEBAR_COLLAPSE_KEY) === '1') sidebar.classList.add('collapsed'); } catch (e) {}
  hamburgerBtn.addEventListener('click', () => {
    const collapsed = sidebar.classList.toggle('collapsed');
    try { localStorage.setItem(SIDEBAR_COLLAPSE_KEY, collapsed ? '1' : '0'); } catch (e) {}
  });
  document.querySelectorAll('.nav-item[data-href]').forEach(el => {
    el.addEventListener('click', () => { window.location.href = el.dataset.href; });
  });
  document.querySelectorAll('.nav-item[data-href], .hamburger').forEach(el => {
    el.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); el.click(); }
    });
  });
})();
</script>
</body>
</html>
