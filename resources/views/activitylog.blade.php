<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH — Activity Log</title>
<style>
  :root{
    --green-dark:#3f6b4a; --green-mid:#4f7c57; --green-accent:#2f6f3c;
    --bg-page:#eef1ee; --card-bg:#ffffff;
    --text-dark:#243026; --text-mid:#5b6b60; --text-light:#8a9690; --border:#e2e6e2;
    --font-body: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  }
  *{box-sizing:border-box;}
  html,body{ margin:0; padding:0; font-family:var(--font-body); background:var(--bg-page); color:var(--text-dark); }

  .topbar{ display:flex; align-items:center; gap:10px; background:linear-gradient(90deg, var(--green-mid), var(--green-dark)); padding:16px 32px; color:#eaf0ea; }
  .topbar .logo-mark{ width:16px; height:16px; border:2px solid #eaf0ea; display:inline-block; position:relative; flex-shrink:0; }
  .topbar .logo-mark::before, .topbar .logo-mark::after{ content:''; position:absolute; background:#eaf0ea; width:2px; height:12px; top:0; left:5px; }
  .topbar .logo-text{ font-weight:700; font-size:15px; }

  .content{ max-width:1200px; margin:0 auto; padding:28px 32px 48px 32px; }
  .page-head{ display:flex; align-items:center; gap:12px; margin-bottom:22px; }
  .back-arrow{ width:34px; height:34px; border-radius:8px; background:var(--card-bg); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-mid); flex-shrink:0; }
  .page-head h1{ font-size:20px; font-weight:700; margin:0; color:var(--text-dark); }

  .table-panel{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:8px 22px; }
  table.activity-table{ width:100%; border-collapse:collapse; table-layout:fixed; }
  table.activity-table col.col-date{ width:160px; }
  table.activity-table col.col-type{ width:130px; }
  table.activity-table col.col-admin{ width:150px; }
  table.activity-table th, table.activity-table td{ text-align:left; }
  table.activity-table th.right, table.activity-table td.right{ text-align:right; }
  table.activity-table th{ font-size:10.5px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-light); padding:12px 8px; border-bottom:1px solid var(--border); }
  table.activity-table td{ font-size:13px; color:var(--text-dark); padding:12px 8px; border-bottom:1px solid #f0f2f0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  table.activity-table td.detail-cell{ white-space:normal; }
  table.activity-table td.type-cell{ color:var(--text-mid); }
  table.activity-table td.admin-cell{ color:var(--text-mid); }
  .empty-note{ font-size:13px; color:var(--text-light); text-align:center; padding:40px 10px; }

  .pagination{ display:flex; align-items:center; justify-content:center; gap:6px; padding:18px 0; }
  .page-link, .page-current{ min-width:32px; height:32px; display:flex; align-items:center; justify-content:center; border-radius:8px; font-size:12.5px; text-decoration:none; }
  .page-link{ background:var(--card-bg); border:1px solid var(--border); color:var(--text-mid); }
  .page-link:hover{ background:#f3f6f3; }
  .page-current{ background:var(--green-accent); color:#fff; font-weight:700; }
  .page-disabled{ min-width:32px; height:32px; display:flex; align-items:center; justify-content:center; color:var(--text-light); opacity:0.4; }
</style>
</head>
<body>

  <div class="topbar">
    <span class="logo-mark"></span><span class="logo-text">NEST.PH</span>
  </div>

  <div class="content">
    <div class="page-head">
      <div class="back-arrow" data-href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
      <h1>Activity Log</h1>
    </div>

    <div class="table-panel">
      @if($activities->isEmpty())
        <div class="empty-note">No activity yet.</div>
      @else
        <table class="activity-table">
          <colgroup>
            <col class="col-date">
            <col>
            <col class="col-type">
            <col class="col-admin">
          </colgroup>
          <thead>
            <tr>
              <th>Date</th>
              <th>Detail</th>
              <th class="right">Type</th>
              <th class="right">Admin</th>
            </tr>
          </thead>
          <tbody>
            @foreach($activities as $activity)
              <tr>
                <td>{{ \Carbon\Carbon::parse($activity['date'])->format('Y/m/d h:i A') }}</td>
                <td class="detail-cell">{{ $activity['detail'] }}</td>
                <td class="right type-cell">{{ $activity['type'] }}</td>
                <td class="right admin-cell">{{ $activity['admin'] ?? '—' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>

        <div class="pagination">
          @if($activities->onFirstPage())
            <span class="page-disabled">&laquo;</span>
          @else
            <a class="page-link" href="{{ $activities->previousPageUrl() }}">&laquo;</a>
          @endif

          @for($p = 1; $p <= $activities->lastPage(); $p++)
            @if($p === $activities->currentPage())
              <span class="page-current">{{ $p }}</span>
            @else
              <a class="page-link" href="{{ $activities->url($p) }}">{{ $p }}</a>
            @endif
          @endfor

          @if($activities->hasMorePages())
            <a class="page-link" href="{{ $activities->nextPageUrl() }}">&raquo;</a>
          @else
            <span class="page-disabled">&raquo;</span>
          @endif
        </div>
      @endif
    </div>
  </div>

<script>
  document.querySelectorAll('[data-href]').forEach(el => {
    el.addEventListener('click', () => { window.location.href = el.dataset.href; });
  });
</script>
</body>
</html>