<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH — Admin Dashboard</title>
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<style>
  /* Shared sidebar/topbar/content-header/reset styles now live in
     public/css/admin.css (linked above). Only this page's own dashboard
     content styling stays here. */

  .stats-row{ display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:20px; }
  .stat-card{ background:var(--card-bg); border-radius:12px; border:1px solid var(--border); padding:16px 18px; box-shadow:0 1px 2px rgba(20,30,20,0.03); position:relative; }
  .stat-card:has(.placeholder-tag){ padding-right:76px; }
  .stat-card-head{ display:flex; align-items:center; gap:8px; font-size:12px; color:var(--text-mid); margin-bottom:8px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .stat-icon-dot{ width:20px; height:20px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .stat-icon-dot svg{ width:11px; height:11px; }
  .stat-icon-dot.red{ background:#f7d9d7; color:var(--status-occupied); }
  .stat-icon-dot.blue{ background:#dbe6f7; color:#3f66c9; }
  .stat-icon-dot.teal{ background:#d7f0ec; color:#2f9c85; }
  .stat-icon-dot.green{ background:var(--status-vacant-bg); color:var(--green-accent); }
  .stat-value{ font-size:26px; font-weight:700; color:var(--text-dark); }
  .stat-value .unit{ font-size:15px; font-weight:600; color:var(--text-mid); }
  .stat-sub{ font-size:11px; color:var(--text-light); margin-top:2px; }
  .placeholder-tag{ position:absolute; top:14px; right:16px; font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; color:var(--status-maintenance); background:var(--status-maintenance-bg); padding:3px 7px; border-radius:20px; }

  .alert-banner{ background:linear-gradient(90deg, #f6d6d3, #fbeceb); border:1px solid #f2c3bf; border-radius:12px; padding:14px 20px; display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:20px; }
  .alert-banner-text strong{ display:block; font-size:13.5px; color:#a3372e; }
  .alert-banner-text span{ font-size:12px; color:#8a4a44; }
  .alert-review-btn{ background:#a3372e; color:#fff; border:none; border-radius:8px; padding:9px 16px; font-size:12px; font-weight:600; cursor:pointer; white-space:nowrap; }

  .dash-grid{ display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:start; }
  .dash-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:20px 22px; margin-bottom:20px; }
  .dash-card-head{ display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
  .dash-card-head h2{ font-size:14.5px; font-weight:700; margin:0; }
  .dash-card-head .view-all{ font-size:11.5px; color:var(--green-accent); font-weight:600; cursor:pointer; }

  .occ-row{ display:grid; grid-template-columns:70px 1fr 40px; align-items:center; gap:12px; margin-bottom:14px; }
  .occ-row:last-child{ margin-bottom:0; }
  .occ-label{ font-size:12.5px; color:var(--text-mid); }
  .occ-bar-track{ height:9px; border-radius:20px; background:#e6ebe6; overflow:hidden; }
  .occ-bar-fill{ height:100%; border-radius:20px; background:linear-gradient(90deg, var(--green-accent), var(--status-vacant)); }
  .occ-count{ font-size:11.5px; color:var(--text-light); text-align:right; }

  .ticket-item{ border:1px solid var(--border); border-radius:10px; padding:14px 16px; margin-bottom:12px; }
  .ticket-item:last-child{ margin-bottom:0; }
  .ticket-title{ font-size:13px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; justify-content:between; gap:8px; }
  .ticket-title .time{ margin-left:auto; font-size:11px; font-weight:500; color:var(--text-light); }
  .ticket-desc{ font-size:12px; color:var(--text-mid); margin-top:4px; }
  .ticket-meta{ font-size:11px; color:var(--text-light); margin-top:6px; }
  .ticket-status-pill{ font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.3px; padding:3px 8px; border-radius:20px; }
  .ticket-status-pill.status-open{ background:#dbe6f7; color:#3f66c9; }
  .ticket-status-pill.status-seen{ background:#e9defa; color:#6a5bcf; }
  .ticket-status-pill.status-in-progress{ background:#f6ecd6; color:#c9962f; }
  .ticket-priority-pill{ font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.3px; padding:3px 8px; border-radius:20px; margin-left:6px; }
  .ticket-priority-pill.priority-urgent{ background:#f7d9d7; color:#c0463d; }
  .ticket-priority-pill.priority-non-urgent{ background:#d9f2dd; color:#3f7a4a; }
  .ticket-overdue-pill{ font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.3px; padding:3px 8px; border-radius:20px; margin-left:6px; background:#f7d9d7; color:#a3372e; }
  .ticket-summary-row{ font-size:11px; color:var(--text-light); margin-top:10px; text-align:right; }

  .activity-tabs{ font-size:11.5px; color:var(--green-accent); font-weight:600; margin-bottom:10px; }
  table.activity-table{ width:100%; border-collapse:collapse; }
  table.activity-table th{ text-align:left; font-size:10.5px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-light); padding:6px 8px; border-bottom:1px solid var(--border); }
  table.activity-table td{ font-size:12px; color:var(--text-dark); padding:8px 8px; border-bottom:1px solid #f0f2f0; }
  table.activity-table td.type{ text-align:right; color:var(--text-mid); }
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
        <div class="back-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
        <h1>Admin Dashboard</h1>
      </div>

      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-card-head"><span class="stat-icon-dot red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></span>Total Tenants</div>
          <div class="stat-value">{{ $totalTenants }}</div>
          <div class="stat-sub">Active tenants</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-head"><span class="stat-icon-dot blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></span>Revenue ({{ now()->format('F') }})</div>
          <div class="stat-value">₱{{ number_format($revenueThisMonth, 0) }}</div>
          <div class="stat-sub">Approved payments this month</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-head"><span class="stat-icon-dot teal"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg></span>Delinquent</div>
          <div class="stat-value">{{ $delinquentCount }}</div>
          <div class="stat-sub">{{ $delinquentCount === 1 ? 'account overdue' : 'accounts overdue' }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-head"><span class="stat-icon-dot green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg></span>Vacancy Rate</div>
          <div class="stat-value">{{ $vacancyRate }}<span class="unit">%</span></div>
          <div class="stat-sub">{{ $vacantBeds }} of {{ $totalBeds }} beds open</div>
        </div>
      </div>

      @if($topDelinquent)
        <div class="alert-banner">
          <div class="alert-banner-text">
            <strong>{{ $delinquentCount }} {{ $delinquentCount === 1 ? 'delinquent account needs' : 'delinquent accounts need' }} attention</strong>
            <span>{{ $topDelinquent['name'] }}{{ $topDelinquent['room_no'] ? ' (Room ' . $topDelinquent['room_no'] . ')' : '' }} is {{ $topDelinquent['days_overdue'] }} days overdue. Immediate escalation recommended.</span>
          </div>
          <a href="{{ route('delinquency.index') }}" class="alert-review-btn" style="text-decoration:none;">Review</a>
        </div>
      @endif

      @include('partials.announcements-feed')
      <div class="dash-grid">
        <div>
          <div class="dash-card">
            <div class="dash-card-head"><h2>Occupancy</h2></div>
            @if($occupancy->isEmpty())
              <div class="empty-note">No floors added yet. Add rooms in Vacancy Monitoring to see occupancy here.</div>
            @else
              @foreach($occupancy as $floor)
                @php $pct = $floor['total'] > 0 ? round(($floor['occupied'] / $floor['total']) * 100) : 0; @endphp
                <div class="occ-row">
                  <div class="occ-label">{{ $floor['label'] }}</div>
                  <div class="occ-bar-track"><div class="occ-bar-fill" style="width:{{ $pct }}%"></div></div>
                  <div class="occ-count">{{ $floor['occupied'] }}/{{ $floor['total'] }}</div>
                </div>
              @endforeach
            @endif
          </div>

          <div class="dash-card">
            <div class="dash-card-head"><h2>Tickets</h2><a href="{{ route('tickets.index') }}" class="view-all" style="text-decoration:none;">View All</a></div>
            @if($recentTickets->isEmpty())
              <div class="empty-note">No open tickets right now.</div>
            @else
              @foreach($recentTickets as $ticket)
                <div class="ticket-item">
                  <div class="ticket-title">
                    {{ $ticket['title'] }}
                    <span class="time">{{ $ticket['submitted_at'] }}</span>
                  </div>
                  <div class="ticket-desc">{{ $ticket['tenant_name'] ?? 'Unknown tenant' }}{{ $ticket['room_no'] ? ' — Room ' . $ticket['room_no'] : '' }}</div>
                  <div class="ticket-meta">
                    <span class="ticket-status-pill status-{{ str_replace(' ', '-', strtolower($ticket['status_label'])) }}">{{ $ticket['status_label'] }}</span>
                    @if($ticket['priority_label'])
                      <span class="ticket-priority-pill priority-{{ str_replace(' ', '-', strtolower($ticket['priority_label'])) }}">{{ $ticket['priority_label'] }}</span>
                    @endif
                    @if($ticket['is_overdue'])
                      <span class="ticket-overdue-pill">Overdue</span>
                    @endif
                  </div>
                </div>
              @endforeach
              <div class="ticket-summary-row">{{ $openTicketsCount }} open · {{ $inProgressTicketsCount }} in progress @if($overdueTicketsCount > 0)· {{ $overdueTicketsCount }} overdue @endif</div>
            @endif
          </div>
        </div>

        <div>
          <div class="dash-card">
            <div class="dash-card-head"><h2>Recent Activities</h2><a href="{{ route('activity-log.index') }}" class="view-all" style="text-decoration:none;">View All</a></div>
            <div class="activity-tabs">ALL</div>
            @if($recentActivities->isEmpty())
              <div class="empty-note">No activity yet.</div>
            @else
              <table class="activity-table">
                <thead>
                  <tr><th>Date</th><th>Detail</th><th>Type</th></tr>
                </thead>
                <tbody>
                  @foreach($recentActivities as $activity)
                    <tr>
                      <td>{{ \Carbon\Carbon::parse($activity['date'])->format('Y/m/d h:i A') }}</td>
                      <td>{{ $activity['detail'] }}</td>
                      <td class="type">{{ $activity['type'] }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            @endif
          </div>
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

})();
</script>

</body>
</html>