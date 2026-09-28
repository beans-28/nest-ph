<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH - Admin Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<style>
  /* Shared sidebar/topbar/content-header/reset styles now live in
     public/css/admin.css (linked above). Only this page's own dashboard
     content styling stays here. */

  /* ---- Overview: four panels, each shaped by its own data ---- */
  .overview{ display:grid; grid-template-columns:minmax(0,1.55fr) minmax(0,1fr); grid-template-areas:"rev beds" "rev ten" "bills bills"; gap:16px; margin-bottom:20px; }
  .panel-revenue{ grid-area:rev; display:flex; flex-direction:column; }
  .panel-revenue .chart-box{ flex:1; min-height:220px; }
  .panel-beds{ grid-area:beds; } .panel-tenants{ grid-area:ten; } .panel-bills{ grid-area:bills; }
  .panel-bills .bills-body{ display:grid; grid-template-columns:minmax(0,1fr) auto; align-items:center; gap:28px; }
  .panel-bills .stack{ margin:0; }
  .panel-bills .stack-keys{ display:flex; gap:28px; }
  .panel{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:18px 20px 20px; min-width:0; }
  .panel-head{ display:flex; align-items:baseline; justify-content:space-between; gap:12px; margin-bottom:10px; }
  .panel-head h2{ font-size:13px; font-weight:700; color:var(--text-mid); margin:0; }
  .panel-link{ font-size:12px; font-weight:500; color:var(--green-accent); text-decoration:none; white-space:nowrap; border-radius:4px; }
  .panel-link::after{ content:" \2192"; }
  .panel-link:hover{ text-decoration:underline; text-underline-offset:3px; }
  .panel-link:focus-visible{ outline:2px solid var(--green-accent); outline-offset:2px; }
  .figure{ display:flex; flex-wrap:wrap; align-items:baseline; column-gap:10px; row-gap:4px; margin-bottom:14px; }
  .figure-value{ font-size:30px; font-weight:700; color:var(--text-dark); letter-spacing:-0.02em; font-variant-numeric:tabular-nums; line-height:1.1; }
  .figure-of{ font-size:18px; font-weight:500; color:var(--text-light); }
  .figure-label{ font-size:12.5px; color:var(--text-mid); }
  .delta{ font-size:11.5px; font-weight:600; padding:2px 8px; border-radius:20px; font-variant-numeric:tabular-nums; }
  .delta.up{ background:var(--status-vacant-bg); color:var(--green-accent); }
  .delta.down{ background:var(--status-occupied-bg); color:var(--danger-text); }
  .chart-box{ position:relative; height:190px; }
  .chart-fallback{ position:absolute; inset:0; margin:0; display:flex; align-items:center; justify-content:center; text-align:center; padding:12px; font-size:12.5px; color:var(--text-mid); background:#f7f9f7; border-radius:8px; }
  .chart-box.chart-sm{ height:84px; }

  .bed-map{ display:flex; flex-direction:column; gap:10px; }
  .bed-floor{ display:flex; align-items:center; gap:10px; }
  .bed-floor-label{ flex:0 0 56px; font-size:12px; color:var(--text-mid); }
  .bed-cells{ display:flex; flex-wrap:wrap; gap:5px; }
  .bed{ width:18px; height:18px; border-radius:4px; display:inline-block; flex-shrink:0; }
  .bed-occupied{ background:var(--green-accent); }
  .bed-vacant{ background:var(--card-bg); box-shadow:inset 0 0 0 1.5px var(--bed-open-ring); }
  /* Not colour alone: maintenance is striped, reserved has a centre dot,
     open is an outline, so colour-blind admins can still tell them apart. */
  .bed-maintenance{ background:repeating-linear-gradient(135deg, var(--status-maintenance) 0 3px, #e3bd6c 3px 6px); }
  .bed-reserved{ background:var(--status-reserved); position:relative; }
  .bed-reserved::after{ content:''; position:absolute; inset:33%; border-radius:50%; background:#fff; }
  .bed-none{ font-size:11.5px; color:var(--text-light); }
  .legend{ list-style:none; display:flex; flex-wrap:wrap; gap:14px; margin:14px 0 0; padding:12px 0 0; border-top:1px solid var(--border); }
  .legend li{ display:flex; align-items:center; gap:6px; font-size:11.5px; color:var(--text-mid); }
  .legend .bed{ width:11px; height:11px; border-radius:3px; }

  .stack{ display:flex; gap:3px; height:14px; border-radius:7px; overflow:hidden; margin:6px 0 16px; }
  .stack-seg{ flex-basis:0; min-width:6px; }
  .seg-paid{ background:var(--green-accent); }
  .seg-partial{ background:var(--status-maintenance); }
  .seg-unpaid{ background:var(--bar-neutral); }
  .seg-overdue{ background:var(--danger); }
  .stack-keys{ display:grid; grid-template-columns:repeat(4, minmax(0,1fr)); gap:8px; margin:0; }
  .stack-key dt{ display:flex; align-items:center; gap:6px; font-size:11.5px; color:var(--text-mid); }
  .stack-key dd{ margin:2px 0 0 14px; font-size:20px; font-weight:700; color:var(--text-dark); font-variant-numeric:tabular-nums; }
  .stack-key.key-overdue dd{ color:var(--danger-text); }
  .dot{ width:8px; height:8px; border-radius:50%; flex-shrink:0; }

  @media (max-width: 1080px){
    .overview{ grid-template-columns:1fr; grid-template-areas:"rev" "beds" "bills" "ten"; }
    .panel-bills .bills-body{ grid-template-columns:1fr; gap:16px; }
  }
  @media (max-width: 720px){
    .panel-link{ display:inline-block; padding:13px 0; margin:-13px 0; }
  }
  @media (max-width: 480px){
    .panel-bills .stack-keys{ display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:12px; }
    .figure-value{ font-size:26px; }
  }

  .alert-banner{ background:#fbeceb; border:1px solid #f2c3bf; border-radius:12px; padding:14px 20px; display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:20px; }
  .alert-banner-text strong{ display:block; font-size:13.5px; color:#a8382f; }
  .alert-banner-text span{ font-size:12px; color:#8a4a44; }
  .alert-review-btn{ background:#c0463d; color:#fff; border:none; border-radius:8px; padding:9px 16px; min-height:44px; display:inline-flex; align-items:center; justify-content:center; font-size:12px; font-weight:600; cursor:pointer; white-space:nowrap; }
  .alert-review-btn:hover{ background:#a8382f; }

  .dash-grid{ display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:start; }
  @media (max-width: 1080px){
    .dash-grid{ grid-template-columns:1fr; }
  }
  @media (max-width: 720px){
    .content{ padding:20px 18px 40px 18px; }
    .alert-banner{ flex-wrap:wrap; }
  }
  .dash-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:20px 22px; margin-bottom:20px; }
  .dash-card-head{ display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
  .dash-card-head h2{ font-size:14.5px; font-weight:700; margin:0; }
  .dash-card-head .view-all{ font-size:11.5px; color:var(--green-accent); font-weight:600; cursor:pointer; }


  .ticket-item{ border:1px solid var(--border); border-radius:10px; padding:14px 16px; margin-bottom:12px; }
  .ticket-item:last-child{ margin-bottom:0; }
  .ticket-title{ font-size:13px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px; }
  .ticket-title .time{ margin-left:auto; font-size:12px; font-weight:500; color:var(--text-mid); }
  .ticket-desc{ font-size:12px; color:var(--text-mid); margin-top:4px; }
  .ticket-meta{ display:flex; flex-wrap:wrap; gap:6px; font-size:12px; color:var(--text-mid); margin-top:8px; }
  .ticket-status-pill{ font-size:11.5px; font-weight:600; padding:3px 9px; border-radius:20px; }
  .ticket-status-pill.status-open{ background:#dbe6f7; color:#2f55b0; }
  .ticket-status-pill.status-seen{ background:#e9defa; color:#5646b8; }
  .ticket-status-pill.status-in-progress{ background:#f6ecd6; color:#8a6414; }
  .ticket-priority-pill{ font-size:11.5px; font-weight:600; padding:3px 9px; border-radius:20px; }
  .ticket-priority-pill.priority-urgent{ background:#f7d9d7; color:#9a2f27; }
  .ticket-priority-pill.priority-non-urgent{ background:#d9f2dd; color:var(--green-accent); }
  .ticket-overdue-pill{ font-size:11.5px; font-weight:600; padding:3px 9px; border-radius:20px; background:#f7d9d7; color:#9a2f27; }
  .ticket-summary-row{ font-size:12px; color:var(--text-mid); margin-top:12px; text-align:right; }

  .activity-tabs{ font-size:11.5px; color:var(--green-accent); font-weight:600; margin-bottom:10px; }
  table.activity-table{ width:100%; border-collapse:collapse; }
  table.activity-table th{ text-align:left; font-size:12px; font-weight:600; color:var(--text-mid); padding:6px 8px; border-bottom:1px solid var(--border); }
  table.activity-table td{ font-size:12px; color:var(--text-dark); padding:8px 8px; border-bottom:1px solid #f0f2f0; }
  table.activity-table td.type{ text-align:right; color:var(--text-mid); }
</style>
</head>
<body>
<div class="app">

  @include('partials.admin-sidebar')

  <div class="main">
    <div class="topbar">
      <div class="hamburger" id="hamburgerBtn" tabindex="0" aria-label="Toggle sidebar"><span></span><span></span><span></span></div>
      <div class="topbar-right">
        <div class="topbar-icon avatar-icon" tabindex="0" aria-label="Account"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div>
      </div>
    </div>

    <div class="content">
      <div class="page-head">
        <h1>Admin Dashboard</h1>
      </div>

      @php
        $rev = $cardCharts['revenue'];
        $revPrev = $rev[count($rev) - 2] ?? 0;
        $revDelta = $revPrev > 0 ? round((($revenueThisMonth - $revPrev) / $revPrev) * 100) : null;
        $bills = $cardCharts['bills'];
        $billsTotal = array_sum($bills->all());
        $occupiedBeds = $cardCharts['beds']['occupied'];
        $maintBeds = $cardCharts['beds']['maintenance'];
        $reservedBeds = $cardCharts['beds']['reserved'];
      @endphp
      <div class="overview">
        <section class="panel panel-revenue" aria-labelledby="revHead">
          <header class="panel-head">
            <h2 id="revHead">Collections</h2>
            <a href="{{ route('payments.index') }}" class="panel-link">Billing and Payments</a>
          </header>
          <div class="figure">
            <span class="figure-value">₱{{ number_format($revenueThisMonth, 0) }}</span>
            <span class="figure-label">approved in {{ now()->format('F') }}</span>
            @if(!is_null($revDelta))
              <span class="delta {{ $revDelta >= 0 ? 'up' : 'down' }}">{{ $revDelta >= 0 ? '+' : '−' }}{{ abs($revDelta) }}% vs {{ now()->subMonthNoOverflow()->format('F') }}</span>
            @endif
          </div>
          <div class="chart-box"><canvas id="revenueChart" role="img" aria-label="Approved payments per month, last six months"></canvas></div>
        </section>

        <section class="panel panel-beds" aria-labelledby="bedsHead">
          <header class="panel-head">
            <h2 id="bedsHead">Beds</h2>
            <a href="{{ route('vacancy.index') }}" class="panel-link">Vacancy Monitor</a>
          </header>
          <div class="figure">
            <span class="figure-value">{{ $occupiedBeds }}<span class="figure-of">/{{ $totalBeds }}</span></span>
            <span class="figure-label">occupied · {{ $vacantBeds }} open ({{ $vacancyRate }}% vacancy){{ $reservedBeds ? ' · ' . $reservedBeds . ' reserved' : '' }}{{ $maintBeds ? ' · ' . $maintBeds . ' in maintenance' : '' }}</span>
          </div>
          @if($bedMap->isEmpty())
            <div class="empty-note">No floors yet. Add rooms in Vacancy Monitor and every bed will appear here.</div>
          @else
            <div class="bed-map">
              @foreach($bedMap as $floor)
                <div class="bed-floor">
                  @php $fc = collect($floor['beds'])->countBy('status'); @endphp
                  <span class="bed-floor-label">{{ $floor['label'] }}</span>
                  <span class="sr-only">: {{ $fc->get('occupied', 0) }} occupied, {{ $fc->get('reserved', 0) }} reserved, {{ $fc->get('vacant', 0) }} open, {{ $fc->get('maintenance', 0) }} in maintenance.</span>
                  <div class="bed-cells" aria-hidden="true">
                    @forelse($floor['beds'] as $bed)
                      <span class="bed bed-{{ $bed['status'] }}" title="{{ $bed['name'] }}: {{ $bed['status'] === 'vacant' ? 'Open' : ucfirst($bed['status']) }}"></span>
                    @empty
                      <span class="bed-none">No beds</span>
                    @endforelse
                  </div>
                </div>
              @endforeach
            </div>
            <ul class="legend">
              <li><span class="bed bed-occupied"></span>Occupied</li>
              <li><span class="bed bed-reserved"></span>Reserved</li>
              <li><span class="bed bed-vacant"></span>Open</li>
              <li><span class="bed bed-maintenance"></span>Maintenance</li>
            </ul>
          @endif
        </section>

        <section class="panel panel-bills" aria-labelledby="billsHead">
          <header class="panel-head">
            <h2 id="billsHead">Bills</h2>
            <a href="{{ route('delinquency.index') }}" class="panel-link">{{ $delinquentCount }} delinquent {{ $delinquentCount === 1 ? 'account' : 'accounts' }}</a>
          </header>
          @if($billsTotal === 0)
            <div class="empty-note">No bills generated yet. They will show here by status once billing runs.</div>
          @else
            <div class="bills-body">
            <div class="stack" role="img" aria-label="Bills by status: {{ $bills['paid'] }} paid, {{ $bills['partial'] }} partial, {{ $bills['unpaid'] }} unpaid, {{ $bills['overdue'] }} overdue">
              @foreach(['paid','partial','unpaid','overdue'] as $st)
                @if($bills[$st] > 0)<span class="stack-seg seg-{{ $st }}" style="flex-grow:{{ $bills[$st] }}"></span>@endif
              @endforeach
            </div>
            <dl class="stack-keys">
              @foreach(['paid' => 'Paid','partial' => 'Partial','unpaid' => 'Unpaid','overdue' => 'Overdue'] as $st => $lbl)
                <div class="stack-key key-{{ $st }}"><dt><span class="dot seg-{{ $st }}"></span>{{ $lbl }}</dt><dd>{{ $bills[$st] }}</dd></div>
              @endforeach
            </dl>
            </div>
          @endif
        </section>

        <section class="panel panel-tenants" aria-labelledby="tenHead">
          <header class="panel-head">
            <h2 id="tenHead">Tenants</h2>
            <a href="{{ route('tenant-manager.index') }}" class="panel-link">Tenant Manager</a>
          </header>
          <div class="figure">
            <span class="figure-value">{{ $totalTenants }}</span>
            <span class="figure-label">active · {{ $newTenantsThisMonth }} new this month</span>
          </div>
          <div class="chart-box chart-sm"><canvas id="tenantsChart" role="img" aria-label="New tenants per month, last six months"></canvas></div>
        </section>
      </div>

      @if($ticketOverdueSummary['total'] > 0)
        <div class="alert-banner">
          <div class="alert-banner-text">
            <strong>{{ $ticketOverdueSummary['total'] }} {{ $ticketOverdueSummary['total'] === 1 ? 'ticket is' : 'tickets are' }} overdue{{ $ticketOverdueSummary['urgent'] > 0 ? ' (' . $ticketOverdueSummary['urgent'] . ' urgent)' : '' }}.</strong>
            <span>Overdue tickets are listed first on the Tickets page.</span>
          </div>
          <a href="{{ route('tickets.index') }}" class="alert-review-btn" style="text-decoration:none;">View Tickets</a>
        </div>
      @endif

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
                  <div class="ticket-desc">{{ $ticket['tenant_name'] ?? 'Unknown tenant' }}{{ $ticket['room_no'] ? ', Room ' . $ticket['room_no'] : '' }}</div>
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

  // Sidebar links, the hamburger, and topbar icons are styled divs rather
  // than native <button>/<a> elements, so pressing Enter or Space while one
  // is focused wouldn't normally do anything. This makes them behave like
  // real interactive controls for keyboard users.
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
// Revenue + new-tenant charts. Data: DashboardController::adminDashboard() -> $cardCharts.
(function(){
  if (typeof Chart === 'undefined') {
    // CDN didn't load (e.g. offline): say so instead of leaving a blank box.
    document.querySelectorAll('.chart-box').forEach(box => {
      box.innerHTML = '<p class="chart-fallback">Chart couldn\'t load. The numbers above are still up to date.</p>';
    });
    return;
  }
  const d = @json($cardCharts);
  // Colours come from admin.css so the charts follow any palette change.
  const css = name => getComputedStyle(document.documentElement).getPropertyValue(name).trim();
  const C = { muted:css('--chart-muted'), mutedHover:css('--chart-muted-hover'), accent:css('--green-accent'),
    accentHover:css('--green-btn-hover'), grid:css('--chart-grid'), tooltip:css('--chart-tooltip'), text:css('--text-mid') };
  const last = d.labels.length - 1;
  Chart.defaults.font.family = 'Roboto, sans-serif';
  Chart.defaults.font.size = 11;
  Chart.defaults.color = C.text;
  const peso = v => '₱' + Number(v).toLocaleString('en-PH');
  const shortPeso = v => v >= 1000 ? '₱' + (v / 1000).toLocaleString('en-PH', { maximumFractionDigits: 1 }) + 'k' : '₱' + v;
  const tooltip = { backgroundColor:C.tooltip, padding:10, cornerRadius:6, displayColors:false, titleFont:{ weight:'600' } };
  // Past months muted, current month in full green so it ties to the headline figure.
  const shade = (past, now) => d.labels.map((_, i) => i === last ? now : past);

  new Chart(document.getElementById('revenueChart'), {
    type:'bar',
    data:{ labels:d.labels, datasets:[{ data:d.revenue, backgroundColor:shade(C.muted, C.accent),
      hoverBackgroundColor:shade(C.mutedHover, C.accentHover), borderRadius:5, maxBarThickness:44 }] },
    options:{ responsive:true, maintainAspectRatio:false,
      plugins:{ legend:{ display:false }, tooltip:{ ...tooltip, callbacks:{ label: c => peso(c.parsed.y) + ' approved' } } },
      scales:{
        x:{ grid:{ display:false }, border:{ display:false } },
        y:{ beginAtZero:true, border:{ display:false }, grid:{ color:C.grid }, ticks:{ maxTicksLimit:4, callback:shortPeso } }
      } }
  });

  new Chart(document.getElementById('tenantsChart'), {
    type:'bar',
    data:{ labels:d.labels, datasets:[{ data:d.tenants, backgroundColor:shade(C.muted, C.accent), borderRadius:4, maxBarThickness:26 }] },
    options:{ responsive:true, maintainAspectRatio:false,
      plugins:{ legend:{ display:false }, tooltip:{ ...tooltip, callbacks:{ label: c => c.parsed.y + ' new ' + (c.parsed.y === 1 ? 'tenant' : 'tenants') } } },
      scales:{ x:{ grid:{ display:false }, border:{ display:false } }, y:{ display:false, beginAtZero:true, suggestedMax:2 } } }
  });
})();
</script>

</body>
</html>