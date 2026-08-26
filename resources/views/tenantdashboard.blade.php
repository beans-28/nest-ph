<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH — Tenant Dashboard</title>
<style>
  :root{
    --green-dark:#3f6b4a; --green-darker:#345a3e;
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
  .page-head{ display:flex; align-items:center; gap:12px; margin-bottom:22px; }
  .back-arrow{ width:30px; height:30px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-mid); flex-shrink:0; }
  .page-head h1{ font-size:19px; font-weight:700; margin:0; color:var(--green-accent); }

  .stats-row{ display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-bottom:20px; }
  .stat-card{ background:var(--card-bg); border-radius:14px; border:1px solid var(--border); padding:18px 20px; display:flex; align-items:center; gap:16px; }
  .stat-icon{ width:52px; height:52px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .stat-icon svg{ width:22px; height:22px; }
  .stat-icon.green{ background:#d9f2dd; color:var(--green-dark); }
  .stat-icon.red{ background:#f7d9d7; color:#c0463d; }
  .stat-icon.purple{ background:#e9defa; color:#7a4fc9; }
  .stat-label{ font-size:10.5px; font-weight:700; letter-spacing:0.4px; text-transform:uppercase; color:var(--text-light); margin-bottom:3px; }
  .stat-value{ font-size:20px; font-weight:800; color:var(--text-dark); }
  .stat-sub{ font-size:11px; color:var(--text-light); margin-top:2px; }

  .dash-grid{ display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:20px; align-items:start; }
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

  .banner{ background:#eef4ee; border-radius:14px; padding:22px 28px; display:flex; align-items:center; gap:16px; overflow:hidden; position:relative; }
  .banner-icon{ width:44px; height:44px; border-radius:50%; background:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .banner-icon svg{ width:20px; height:20px; color:var(--green-dark); }
  .banner strong{ display:block; font-size:14px; color:var(--text-dark); }
  .banner span{ font-size:12px; color:var(--text-mid); }
  .banner-illustration{ margin-left:auto; opacity:0.9; flex-shrink:0; }
</style>
</head>
<body>
<div class="app">

  <aside class="sidebar">
    <div class="sidebar-logo"><span class="logo-mark"></span> NEST.PH</div>
    <div class="sidebar-section-label">Tenant View</div>
    <ul class="nav-list">
      <li class="nav-item active" data-href="{{ route('dashboard') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></span>Tenant Dashboard</li>
      <li class="nav-item"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h18v10H3z"/><path d="M3 12h18"/></svg></span>Ticket Module</li>
      <li class="nav-item" data-href="{{ route('tenant.billing') }}"><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></span>Billing and Payments</li>
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
      <div class="page-head">
        <div class="back-arrow" data-href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
        <h1>Tenant Dashboard</h1>
      </div>

      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
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

        <div class="stat-card">
          <div class="stat-icon red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 21V9l8-6 8 6v12"/><path d="M9 21v-6h6v6"/></svg></div>
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

        <div class="stat-card">
          <div class="stat-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div>
          <div>
            <div class="stat-label">Lease Ends</div>
            <div class="stat-value">
              @if($contract?->end_date) {{ $contract->end_date->format('M jS') }} @else No end date @endif
            </div>
            <div class="stat-sub">
              @if($contract?->end_date)
                @php $monthsLeft = now()->startOfDay()->diffInMonths($contract->end_date, false); @endphp
                @if($monthsLeft > 0) {{ $monthsLeft }} {{ Str::plural('month', $monthsLeft) }} left
                @else Lease ended @endif
              @else — @endif
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 7v10l9 4 9-4V7"/></svg></div>
          <div>
            <div class="stat-label">Open Tickets</div>
            <div class="stat-value">{{ $openTicketsCount }}</div>
            <div class="stat-sub">{{ $inProgressCount }} in progress</div>
          </div>
        </div>
      </div>

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
          <button class="panel-btn outline">View All</button>
        </div>
      </div>

      <div class="banner">
        <div class="banner-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8a6 6 0 00-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 01-3.4 0"/></svg></div>
        <div><strong>Stay updated!</strong><span>Make sure your payments are on time to avoid penalties</span></div>
        <div class="banner-illustration">
          <svg width="100" height="60" viewBox="0 0 100 60" fill="none">
            <circle cx="20" cy="45" r="12" fill="#bfe0c2"/>
            <circle cx="85" cy="48" r="10" fill="#bfe0c2"/>
            <rect x="40" y="30" width="30" height="20" fill="#fff" stroke="#8fc394" stroke-width="1.5"/>
            <path d="M37 30L55 15L73 30Z" fill="#4f7c57"/>
            <rect x="51" y="38" width="8" height="12" fill="#8fc394"/>
          </svg>
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
})();
</script>

</body>
</html>
