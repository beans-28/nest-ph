<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    {{-- Page-switch fade opt-in: must be inline and first in <head> or Chrome decides before it sees it (see typography.css). --}}
    <style>@view-transition{navigation:auto}</style><link rel="stylesheet" href="/css/motion.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Admin Dashboard · {{ $brandDormName }}</title>
<link rel="icon" href="{{ $brandFaviconUrl }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/typography.css') }}">
    <script src="{{ asset('js/smooth-nav.js') }}" defer></script>
    <script src="{{ asset('js/a11y-dialogs.js') }}" defer></script>
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
  /* Fewer cards (limited admin): let them flow instead of fixed slots. */
  .overview.overview-partial{ grid-template-areas:none; grid-template-columns:repeat(auto-fit, minmax(min(280px, 100%), 1fr)); }
  .overview.overview-partial > .panel{ grid-area:auto; }
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
  .delta.up{ background:var(--brand-tint); color:var(--green-accent); }
  .delta.down{ background:var(--status-occupied-bg); color:var(--danger-text); }
  .chart-box{ position:relative; height:190px; }
  .chart-fallback{ position:absolute; inset:0; margin:0; display:flex; align-items:center; justify-content:center; text-align:center; padding:12px; font-size:12.5px; color:var(--text-mid); background:#f8f8f8; border-radius:8px; }
  .chart-box.chart-sm{ height:84px; }

  .bed-map{ display:flex; flex-direction:column; gap:10px; }
  .bed-floor{ display:flex; align-items:center; gap:10px; }
  .bed-floor-label{ flex:0 0 56px; font-size:12px; color:var(--text-mid); }
  .bed-cells{ display:flex; flex-wrap:wrap; gap:5px; }
  .bed{ width:18px; height:18px; border-radius:4px; display:inline-block; flex-shrink:0; }
  .bed-occupied{ background:var(--status-good, #2f6f3c); }
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
  .seg-paid{ background:var(--status-good, #2f6f3c); }
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

  /* ---- Greeting + Quick Actions + Needs Your Attention ---- */
  .welcome{ margin-bottom:18px; }
  .welcome h1{ font-size:22px; font-weight:700; color:var(--text-dark); margin:0 0 4px; }
  .welcome p{ font-size:13px; color:var(--text-mid); margin:0; }
  .today{ display:flex; flex-direction:column; gap:16px; margin-bottom:16px; }
  .actions{ display:grid; grid-template-columns:repeat(3, minmax(0,1fr)); gap:10px; }
  /* Wide screens: all six in one row, icon above the text so labels never wrap. */
  @media (min-width: 1280px){ .actions{ grid-template-columns:repeat(6, minmax(0,1fr)); } .action{ flex-direction:column; align-items:center; text-align:center; gap:10px; } .action-text{ align-items:center; } }
  .action{ display:flex; align-items:center; gap:12px; padding:14px; min-height:64px; border:1px solid var(--border); border-radius:10px; text-decoration:none; color:var(--text-dark); background:var(--card-bg); transition:border-color .15s, background .15s; }
  .action:hover{ border-color:var(--green-accent); background:var(--brand-tint); }
  .action:focus-visible{ outline:2px solid var(--green-accent); outline-offset:2px; }
  .action-icon{ flex:0 0 38px; width:38px; height:38px; border-radius:9px; display:flex; align-items:center; justify-content:center; background:var(--brand-tint); color:var(--green-accent); }
  .action-icon svg{ width:20px; height:20px; }
  .action-text{ display:flex; flex-direction:column; min-width:0; }
  .action-text strong{ font-size:13.5px; font-weight:600; }
  .action-text span{ font-size:11.5px; color:var(--text-mid); }
  .attn{ list-style:none; margin:0; padding:0; display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:6px 10px; }
  .attn > li{ min-width:0; } .attn-row{ height:100%; }
  .attn-row{ display:flex; align-items:center; gap:12px; padding:10px 12px; min-height:48px; border-radius:9px; text-decoration:none; color:var(--text-dark); background:#f8f8f8; }
  .attn-row:hover{ background:var(--brand-tint); }
  .attn-row:focus-visible{ outline:2px solid var(--green-accent); outline-offset:2px; }
  .attn-count{ flex:0 0 auto; min-width:32px; height:28px; padding:0 8px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; font-variant-numeric:tabular-nums; background:var(--card-bg); border:1px solid var(--border); }
  .attn-row.urgent{ background:#fbeceb; }
  .attn-row.urgent .attn-count{ background:#c0463d; border-color:#c0463d; color:#fff; }
  .attn-text{ flex:1; font-size:13px; display:flex; flex-direction:column; }
  .attn-text small{ font-size:11.5px; color:var(--text-mid); margin-top:2px; }
  .attn-go{ font-size:20px; color:var(--text-light); }
  .all-clear{ font-size:13px; color:var(--green-accent); background:var(--brand-tint); border-radius:9px; padding:14px; margin:0; }
  @media (max-width: 900px){ .attn{ grid-template-columns:1fr; } }
  @media (max-width: 560px){ .actions{ grid-template-columns:repeat(2, minmax(0,1fr)); } .action-text span{ display:none; } }

  .alert-banner{ background:#fbeceb; border:1px solid #f2c3bf; border-radius:12px; padding:14px 20px; display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:20px; }
  .alert-banner-text strong{ display:block; font-size:13.5px; color:#a8382f; }
  .alert-banner-text span{ font-size:12px; color:#8a4a44; }
  .alert-review-btn{ background:#c0463d; color:#fff; border:none; border-radius:8px; padding:9px 16px; min-height:44px; display:inline-flex; align-items:center; justify-content:center; font-size:12px; font-weight:600; cursor:pointer; white-space:nowrap; }
  .alert-review-btn:hover{ background:#a8382f; }

  .dash-grid{ display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:start; }
  .dash-grid.dash-grid-single{ grid-template-columns:1fr; }
  .no-access-note{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; margin:0 0 20px; }
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
  .ticket-status-pill.status-pending{ background:var(--tk-pending-bg); color:var(--tk-pending); }
  .ticket-status-pill.status-closed{ background:var(--tk-closed-bg); color:var(--tk-closed); }
  .ticket-status-pill.status-in-progress{ background:#f6ecd6; color:#8a6414; }

  .ticket-overdue-pill{ font-size:11.5px; font-weight:600; padding:3px 9px; border-radius:20px; background:#f7d9d7; color:#9a2f27; }
  .ticket-summary-row{ font-size:12px; color:var(--text-mid); margin-top:12px; text-align:right; }

  .activity-tabs{ font-size:11.5px; color:var(--green-accent); font-weight:600; margin-bottom:10px; }
  table.activity-table{ width:100%; border-collapse:collapse; }
  table.activity-table th{ text-align:left; font-size:12px; font-weight:600; color:var(--text-mid); padding:6px 8px; border-bottom:1px solid var(--border); }
  table.activity-table td{ font-size:12px; color:var(--text-dark); padding:8px 8px; border-bottom:1px solid #f1f1f1; }
  table.activity-table td.type{ text-align:right; color:var(--text-mid); }
</style>
@include('partials.ticket-style')
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
      @php
        $hour = (int) now()->format('G');
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
        $firstName = \Illuminate\Support\Str::of(auth()->user()->name ?? 'Admin')->before(' ');
      @endphp
      <div class="welcome">
        <h1>{{ $greeting }}, {{ $firstName }}</h1>
        <p>{{ now()->format('l, F j') }} · Here is what needs your attention today.</p>
      </div>

      @php
        // Only show cards for areas this admin has a privilege for
        // (same rules as the sidebar / EnsureAdminHasPrivilege).
        $me = auth()->user()->loadMissing('privileges');
        $seeBilling = $me->hasPrivilege('manage_billing');
        $seeRooms = $me->hasPrivilege('manage_rooms');
        $seeTenants = $me->hasPrivilege('manage_tenants');
        $seeReports = $me->hasPrivilege('view_reports');
        $overviewCards = (int) $seeBilling * 2 + (int) $seeRooms + (int) $seeTenants;
        $rev = $cardCharts['revenue'];
        $revPrev = $rev[count($rev) - 2] ?? 0;
        $revDelta = $revPrev > 0 ? round((($revenueThisMonth - $revPrev) / $revPrev) * 100) : null;
        $bills = $cardCharts['bills'];
        $billsTotal = array_sum($bills->all());
        $occupiedBeds = $cardCharts['beds']['occupied'];
        $maintBeds = $cardCharts['beds']['maintenance'];
        $reservedBeds = $cardCharts['beds']['reserved'];
      @endphp
      @if(! $seeBilling && ! $seeRooms && ! $seeTenants && ! $seeReports && ! $me->hasPrivilege('manage_contracts'))
        <p class="empty-note no-access-note">Your account has no sections assigned yet. Ask the Dormitory Owner to grant you access on the Admin Privileges page.</p>
      @endif

      @php
        // Shortcuts to the jobs admins do most. Each one only shows if this
        // admin has the privilege for it (same rules as the sidebar).
        $actions = collect([
          ['show' => $seeBilling, 'href' => route('payments.index') . '?tab=pending', 'title' => 'Verify Payments', 'hint' => 'Check uploaded proofs',
           'icon' => '<path d="M9 12l2 2 4-4"/><rect x="3" y="4" width="18" height="16" rx="2"/>'],
          ['show' => $seeBilling, 'href' => route('payments.index'), 'title' => 'Billing', 'hint' => 'Bills and cash payments',
           'icon' => '<rect x="2" y="6" width="20" height="13" rx="2"/><path d="M2 10h20M6 15h4"/>'],
          ['show' => $seeTenants, 'href' => route('applications.index'), 'title' => 'Applications', 'hint' => 'Approve new tenants',
           'icon' => '<path d="M14 3H6a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2V9z"/><path d="M14 3v6h6M8 13h8M8 17h5"/>'],
          ['show' => $seeTenants, 'href' => route('tenant-manager.index'), 'title' => 'Tenants', 'hint' => 'Find a tenant\'s profile',
           'icon' => '<path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>'],
          ['show' => $seeRooms, 'href' => route('vacancy.index'), 'title' => 'Vacant Beds', 'hint' => 'See open rooms',
           'icon' => '<path d="M2 18V6M2 14h20v4M22 14v-3a3 3 0 00-3-3h-7v6"/><circle cx="7" cy="10.5" r="2"/>'],
          ['show' => $seeTenants, 'href' => route('tickets.index'), 'title' => 'Maintenance', 'hint' => 'Repair requests',
           'icon' => '<path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94z"/>'],
        ])->where('show', true)->take(6);

        // Each row: [show, count, label, link, urgent]
        $attn = collect([
          [$seeBilling, $attention['overdue'], $attention['overdue'] === 1 ? 'tenant with an overdue bill' : 'tenants with overdue bills', route('delinquency.index'), true,
            $topDelinquent ? 'Longest: ' . $topDelinquent['name'] . ($topDelinquent['room_no'] ? ' (Room ' . $topDelinquent['room_no'] . ')' : '') . ', ' . $topDelinquent['days_overdue'] . ' days' : null],
          [$seeTenants, $overdueTicketsCount, $overdueTicketsCount === 1 ? 'maintenance ticket is overdue' : 'maintenance tickets are overdue', route('tickets.index'), true,
            $ticketOverdueSummary['critical'] > 0 ? $ticketOverdueSummary['critical'] . ' critical or high priority' : null],
          [$seeBilling, $attention['proofs'], $attention['proofs'] === 1 ? 'payment proof to verify' : 'payment proofs to verify', route('payments.index') . '?tab=pending', false, null],
          [$seeTenants, $attention['applications'], $attention['applications'] === 1 ? 'application to review' : 'applications to review', route('applications.index'), false, null],
          [$seeBilling, $attention['unpaid'], $attention['unpaid'] === 1 ? 'tenant with an unpaid bill (not yet due)' : 'tenants with unpaid bills (not yet due)', route('payments.index'), false, null],
          [$seeTenants, $attention['tickets'], $attention['tickets'] === 1 ? 'new maintenance request' : 'new maintenance requests', route('tickets.index'), false, null],
          [$seeTenants, $attention['inquiries'], $attention['inquiries'] === 1 ? 'inquiry waiting for a reply' : 'inquiries waiting for a reply', route('inquiries.index'), false, null],
        ])->filter(fn ($r) => $r[0]);
        $attnOpen = $attn->filter(fn ($r) => $r[1] > 0);
      @endphp

      @if($actions->isNotEmpty() || $attn->isNotEmpty())
      <div class="today">
        @if($actions->isNotEmpty())
        <section class="panel" aria-labelledby="qaHead">
          <header class="panel-head"><h2 id="qaHead">Quick Actions</h2></header>
          <div class="actions">
            @foreach($actions as $a)
              <a href="{{ $a['href'] }}" class="action">
                <span class="action-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $a['icon'] !!}</svg></span>
                <span class="action-text"><strong>{{ $a['title'] }}</strong><span>{{ $a['hint'] }}</span></span>
              </a>
            @endforeach
          </div>
        </section>
        @endif

        @if($attn->isNotEmpty())
        <section class="panel" aria-labelledby="attnHead">
          <header class="panel-head"><h2 id="attnHead">Needs Your Attention</h2></header>
          @if($attnOpen->isEmpty())
            <p class="all-clear">All caught up. Nothing is waiting on you right now.</p>
          @else
            <ul class="attn">
              @foreach($attnOpen as [$show, $count, $label, $href, $urgent, $sub])
                <li>
                  <a href="{{ $href }}" class="attn-row{{ $urgent ? ' urgent' : '' }}">
                    <span class="attn-count">{{ $count }}</span>
                    <span class="attn-text">{{ $label }}@if($sub)<small>{{ $sub }}</small>@endif</span>
                    <span class="attn-go" aria-hidden="true">›</span>
                  </a>
                </li>
              @endforeach
            </ul>
          @endif
        </section>
        @endif
      </div>
      @endif

      @if($overviewCards > 0)
      <div class="overview{{ $overviewCards < 4 ? ' overview-partial' : '' }}">
        @if($seeBilling)
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

        @endif

        @if($seeRooms)
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

        @endif

        @if($seeBilling)
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

        @endif

        @if($seeTenants)
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
        @endif
      </div>
      @endif

      @include('partials.announcements-feed')
      @if($seeTenants || $seeReports)
      <div class="dash-grid{{ $seeTenants && $seeReports ? '' : ' dash-grid-single' }}">
        @if($seeTenants)
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
                      <span class="prio {{ $ticket['priority'] }}">{{ $ticket['priority_label'] }}</span>
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
        @endif

        @if($seeReports)
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
        @endif
      </div>
      @endif

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
      const logoutRes = await fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } });
      window.location.href = (await logoutRes.json().catch(() => ({}))).redirect || '/';
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
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
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
  const d = {!! json_encode([
    'labels' => $cardCharts['labels'],
    'revenue' => $seeBilling ? $cardCharts['revenue'] : [],
    'tenants' => $seeTenants ? $cardCharts['tenants'] : [],
  ]) !!};
  // Colours come from admin.css so the charts follow any palette change.
  const css = name => getComputedStyle(document.documentElement).getPropertyValue(name).trim();
  const C = { muted:css('--chart-muted'), mutedHover:css('--chart-muted-hover'), accent:css('--green-accent'),
    accentHover:css('--green-btn-hover'), grid:css('--chart-grid'), tooltip:css('--chart-tooltip'), text:css('--text-mid') };
  const last = d.labels.length - 1;
  Chart.defaults.font.family = '"Plus Jakarta Sans", sans-serif';
  Chart.defaults.font.size = 11;
  Chart.defaults.color = C.text;
  const peso = v => '₱' + Number(v).toLocaleString('en-PH');
  const shortPeso = v => v >= 1000 ? '₱' + (v / 1000).toLocaleString('en-PH', { maximumFractionDigits: 1 }) + 'k' : '₱' + v;
  const tooltip = { backgroundColor:C.tooltip, padding:10, cornerRadius:6, displayColors:false, titleFont:{ weight:'600' } };
  // Past months muted, current month in full green so it ties to the headline figure.
  const shade = (past, now) => d.labels.map((_, i) => i === last ? now : past);

  if (document.getElementById('revenueChart')) new Chart(document.getElementById('revenueChart'), {
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

  if (document.getElementById('tenantsChart')) new Chart(document.getElementById('tenantsChart'), {
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