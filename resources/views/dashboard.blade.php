<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEST.PH - Account Setup Needed</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
  :root{
    --green-dark:#3f6b4a; --green-darker:#345a3e;
    --green-sidebar-top:#33513c; --green-sidebar-bottom:#223a29;
    --bg-page:#f4f6f4; --card-bg:#ffffff;
    --text-dark:#1f2a22; --text-mid:#5b6b60; --text-light:#8a9690; --border:#e5e9e4;
    --font-body: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif;
  }
  *{box-sizing:border-box;}
  html,body{ margin:0; padding:0; font-family:var(--font-body); background:var(--bg-page); color:var(--text-dark); }

  .topbar{
    display:flex; align-items:center; gap:10px;
    background:linear-gradient(90deg, var(--green-sidebar-top), var(--green-dark));
    padding:16px 32px; color:#eaf0ea;
  }
  .topbar .logo-img{ height:24px; width:auto; }
  .topbar .logo-text{ font-weight:700; font-size:15px; }

  .content{ min-height:calc(100vh - 57px); display:flex; align-items:center; justify-content:center; padding:24px; }
  .notice-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:16px; padding:40px 36px; max-width:440px; text-align:center; }
  .notice-icon{ width:56px; height:56px; border-radius:50%; background:#eaf3ec; color:var(--green-dark); display:flex; align-items:center; justify-content:center; margin:0 auto 20px auto; }
  .notice-icon svg{ width:26px; height:26px; }
  .notice-card h1{ font-size:18px; font-weight:700; margin:0 0 10px 0; color:var(--text-dark); }
  .notice-card p{ font-size:13px; color:var(--text-mid); line-height:1.65; margin:0 0 24px 0; }
  .logout-btn{ background:var(--green-dark); color:#fff; border:none; border-radius:8px; padding:12px 26px; font-size:13px; font-weight:700; letter-spacing:0.02em; cursor:pointer; }
  .logout-btn:hover{ background:var(--green-darker); }
</style>
</head>
<body>

  <div class="topbar">
    <img src="{{ asset('images/nestph.png') }}" alt="NEST.PH" class="logo-img"><span class="logo-text">NEST.PH</span>
  </div>

  <div class="content">
    <div class="notice-card">
      <div class="notice-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
      </div>
      <h1>Account Setup Needed</h1>
      <p>Your login isn't linked to a tenant record yet, so there's nothing to show here. Please contact the dormitory administrator so they can finish setting up your account.</p>
      <button type="button" class="logout-btn" id="logoutBtn">Log Out</button>
    </div>
  </div>

<script>
(function(){
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  document.getElementById('logoutBtn').addEventListener('click', async () => {
    await fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken } });
    window.location.href = '/';
  });
})();
</script>

</body>
</html>
