<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH - Tenant Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/tenant.css') }}">
<style>
  /* Shared color variables, page reset, sidebar, topbar, and focus styles
     now live in public/css/tenant.css (linked above). This page adds two
     extra variable pairs (pending/paid) that tenant.css doesn't define,
     plus its own dashboard content styling below. */
  :root{
    --pending-bg:#fbe9c8; --pending-text:#a4761a;
  }

  .content{ padding:26px 34px 48px 34px; flex:1; max-width:1180px; }
  .page-head{ display:flex; align-items:center; gap:12px; margin-bottom:22px; }
  .back-arrow{ width:30px; height:30px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-mid); flex-shrink:0; }
  .page-head h1{ font-size:19px; font-weight:700; margin:0; color:var(--green-accent); }

  .stats-row{ display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-bottom:16px; }
  .stat-card{ background:var(--card-bg); border-radius:14px; border:1px solid var(--border); padding:18px 20px; display:flex; align-items:center; gap:16px; }
  .stat-icon{ width:52px; height:52px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .stat-icon svg{ width:22px; height:22px; }
  .stat-icon.green{ background:var(--sage-100); color:var(--green-dark); }
  .stat-icon.attention{ background:#f7d9d7; color:#c0463d; }
  .stat-icon.neutral{ background:var(--cream); color:var(--text-mid); }
  .stat-label{ font-size:10.5px; font-weight:700; letter-spacing:0.4px; text-transform:uppercase; color:var(--text-light); margin-bottom:3px; }
  .stat-value{ font-size:20px; font-weight:800; color:var(--text-dark); }
  .stat-card.emphasis .stat-value{ font-size:23px; color:var(--green-darker); }
  .stat-sub{ font-size:11px; color:var(--text-light); margin-top:2px; }

  @media (max-width: 900px){
    .content{ padding:20px 18px 40px 18px; }
    .stats-row{ grid-template-columns:repeat(2, minmax(0, 1fr)); }
    .stat-card > div:not(.stat-icon){ min-width:0; }
    .stat-value{ overflow-wrap:anywhere; }
    .dash-grid{ grid-template-columns:1fr; }
  }
  @media (max-width: 520px){
    .stats-row{ grid-template-columns:1fr; }
  }

  /* Compact heads-up strip -- replaces the old wide illustrated banner.
     Same message, a fraction of the vertical space, and sits right under
     the stats where a quick reminder belongs instead of trailing the
     whole page as a disconnected afterthought. */
  .tip-strip{ background:var(--sage-50); border:1px solid var(--sage-200); border-radius:10px; padding:12px 18px; display:flex; align-items:center; gap:12px; margin-bottom:20px; }
  .tip-strip-icon{ width:30px; height:30px; border-radius:50%; background:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0; color:var(--green-dark); }
  .tip-strip-icon svg{ width:15px; height:15px; }
  .tip-strip-text{ font-size:12.5px; color:var(--text-mid); }
  .tip-strip-text strong{ color:var(--text-dark); font-weight:700; }

  .dash-grid{ display:grid; grid-template-columns:1fr 1fr; gap:18px; align-items:start; }
  /* Must come after the base rule above, or the 2-column layout wins on phones */
  @media (max-width: 900px){ .dash-grid{ grid-template-columns:1fr; } }
  .panel{ background:var(--card-bg); border:1px solid var(--border); border-radius:14px; padding:20px 22px; }
  .panel-head{ display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
  .panel-head h2{ font-size:15px; font-weight:700; margin:0; }

  table.mini-table{ width:100%; border-collapse:collapse; }
  table.mini-table th{ text-align:left; font-size:10.5px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-light); padding:6px 4px; border-bottom:1px solid var(--border); }
  table.mini-table td{ font-size:13px; color:var(--text-dark); padding:12px 4px; border-bottom:1px solid #f0f2f0; }
  .status-pill{ font-size:11px; font-weight:600; padding:4px 12px; border-radius:20px; display:inline-block; }
  .status-pill.pending{ background:var(--pending-bg); color:var(--pending-text); }
  .status-pill.paid{ background:var(--paid-bg); color:var(--paid-text); }
  .status-pill.overdue{ background:#f7d9d7; color:#c0463d; }
  .status-pill.open, .status-pill.in_progress{ background:var(--pending-bg); color:var(--pending-text); }
  .status-pill.resolved, .status-pill.closed{ background:var(--paid-bg); color:var(--paid-text); }

  .panel-btn{ background:var(--green-btn); color:#fff; border:none; border-radius:8px; padding:10px 18px; font-size:12.5px; font-weight:700; cursor:pointer; margin-top:14px; }
  .panel-btn:hover{ background:var(--green-btn-hover); }
  .panel-btn.outline{ background:#fff; border:1px solid var(--border); color:var(--text-dark); }
  .empty-note{ font-size:12px; color:var(--text-light); text-align:center; padding:20px 0; }
</style>
</head>
<body>
<div class="app">

  @include('partials.tenant-sidebar')

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
        <h1>Tenant Dashboard</h1>
      </div>

      <div class="stats-row">
        <div class="stat-card emphasis" role="group" aria-label="Balance due">
          <div class="stat-icon {{ ($daysUntilDue !== null && $daysUntilDue < 0) ? 'attention' : 'green' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 11H4M20 7H4"/><path d="M7 21V4a1 1 0 011-1h4a1 1 0 010 12H7"/></svg></div>
          <div>
            <div class="stat-label">Balance Due</div>
            <div class="stat-value">₱{{ number_format($balanceDue, 0) }}</div>
            <div class="stat-sub">
              @if($nextDueDate && $daysUntilDue !== null)
                @if($daysUntilDue > 0) Due in {{ $daysUntilDue }} {{ Str::plural('day', $daysUntilDue) }}
                @elseif($daysUntilDue === 0) Due today
                @else Overdue by {{ abs($daysUntilDue) }} {{ Str::plural('day', abs($daysUntilDue)) }}
                @endif
              @else No balance due @endif
            </div>
          </div>
        </div>

        <div class="stat-card" role="group" aria-label="Room">
          <div class="stat-icon neutral"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 21V9l8-6 8 6v12"/><path d="M9 21v-6h6v6"/></svg></div>
          <div>
            <div class="stat-label">Room</div>
            <div class="stat-value">{{ $contract?->bed?->room?->room_no ?? '—' }}</div>
            <div class="stat-sub">
              @if($contract?->bed?->room)
                Floor {{ $contract->bed->room->floor?->floor_number ?? '—' }} - {{ $contract->bed->room->room_type ?? 'room' }}
              @else No active lease @endif
            </div>
          </div>
        </div>

        <div class="stat-card" role="group" aria-label="Lease ends">
          <div class="stat-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div>
          <div>
            <div class="stat-label">Lease Ends</div>
            <div class="stat-value">
              @if($contract?->end_date) {{ $contract->end_date->format('M jS') }} @else No end date @endif
            </div>
            <div class="stat-sub">
              @if($contract?->end_date)
                @php
                  $today = now()->startOfDay();
                  $endDate = $contract->end_date;
                  $isPast = $today->greaterThan($endDate);
                  $diff = $today->diff($endDate);
                  $monthsLeft = $diff->y * 12 + $diff->m;
                  $daysLeft = $diff->d;
                @endphp
                @if($isPast)
                  Lease ended
                @elseif($monthsLeft > 0 && $daysLeft > 0)
                  {{ $monthsLeft }} {{ Str::plural('month', $monthsLeft) }}, {{ $daysLeft }} {{ Str::plural('day', $daysLeft) }} left
                @elseif($monthsLeft > 0)
                  {{ $monthsLeft }} {{ Str::plural('month', $monthsLeft) }} left
                @elseif($daysLeft > 0)
                  {{ $daysLeft }} {{ Str::plural('day', $daysLeft) }} left
                @else
                  Ends today
                @endif
              @else — @endif
            </div>
          </div>
        </div>

        <div class="stat-card" role="group" aria-label="Open tickets">
          <div class="stat-icon neutral"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 7v10l9 4 9-4V7"/></svg></div>
          <div>
            <div class="stat-label">Open Tickets</div>
            <div class="stat-value">{{ $openTicketsCount }}</div>
            <div class="stat-sub">{{ $inProgressCount }} in progress</div>
          </div>
        </div>
      </div>

      <div class="tip-strip">
        <div class="tip-strip-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8a6 6 0 00-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 01-3.4 0"/></svg></div>
        <div class="tip-strip-text"><strong>Stay updated:</strong> make sure your payments are on time to avoid penalties.</div>
      </div>

      @include('partials.announcements-feed')

      <div class="dash-grid">
        <div class="panel">
          <div class="panel-head"><h2>Recent Billing</h2></div>
          @if($recentBills->isEmpty())
            <div class="empty-note">No billing statements yet.</div>
          @else
            <table class="mini-table">
              <thead><tr><th>Month</th><th>Amount</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($recentBills as $bill)
                  <tr>
                    <td>{{ $bill['month_label'] }}</td>
                    <td>₱{{ number_format($bill['total_amount'], 0) }}</td>
                    <td>
                      @php $pillClass = $bill['status'] === 'paid' ? 'paid' : ($bill['status'] === 'overdue' ? 'overdue' : 'pending'); @endphp
                      <span class="status-pill {{ $pillClass }}">{{ $bill['status'] === 'unpaid' ? 'Pending' : ucfirst($bill['status']) }}</span>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
          <button class="panel-btn" data-href="{{ route('tenant.billing') }}">Pay Now</button>
        </div>

        <div class="panel">
          <div class="panel-head"><h2>My Tickets</h2></div>
          @if($recentTickets->isEmpty())
            <div class="empty-note">You have not submitted any tickets yet.</div>
          @else
            <table class="mini-table">
              <thead><tr><th>Issue</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($recentTickets as $ticket)
                  <tr>
                    <td>{{ $ticket->title }}</td>
                    <td><span class="status-pill {{ $ticket->status }}">{{ ucwords(str_replace('_',' ', $ticket->status)) }}</span></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
          <button class="panel-btn outline" data-href="{{ route('tenant.tickets') }}">View All</button>
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

  // Remember collapsed/expanded across page loads (each page is a full
  // reload, not a single-page app, so this has to be localStorage rather
  // than in-memory state) so the sidebar doesn't reset every time you
  // navigate. Same key as the admin side, so the preference is shared.
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

  // Sidebar links, the hamburger, Log Out, and the back-arrow are styled
  // divs rather than native <button>/<a> elements, so pressing Enter or
  // Space while one is focused wouldn't normally do anything. This makes
  // them behave like real interactive controls for keyboard users.
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