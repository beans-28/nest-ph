<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Settings · {{ $brandDormName }}</title>
<link rel="icon" href="{{ $brandFaviconUrl }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/typography.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<style>
  .settings-grid{ display:grid; gap:20px; max-width:640px; }
  .card{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:24px; }
  .card h2{ font-size:15px; font-weight:700; color:var(--green-accent); margin:0 0 18px; }
  .field{ margin-bottom:16px; }
  .field label{ display:block; font-size:12.5px; font-weight:600; color:var(--text-dark); margin-bottom:6px; }
  .field input{ width:100%; box-sizing:border-box; border:1px solid var(--border); border-radius:8px; padding:10px 12px; font:inherit; font-size:14px; background:#fff; color:var(--text-dark); }
  .field input:focus{ outline:2px solid var(--green-accent); outline-offset:0; border-color:transparent; }
  .field-error{ display:block; min-height:0; margin-top:5px; font-size:12px; color:#b42318; }
  .field-error:empty{ display:none; }
  .confirm-field[hidden]{ display:none; }
  .actions{ display:flex; align-items:center; gap:12px; margin-top:6px; }
  .btn-save{ height:38px; padding:0 20px; border:none; border-radius:8px; background:var(--green-btn); color:#fff; font:inherit; font-size:13px; font-weight:600; cursor:pointer; }
  .btn-save:hover{ background:var(--green-btn-hover); }
  .btn-save:disabled{ opacity:.6; cursor:default; }
  .btn-save:focus-visible{ outline:2px solid var(--text-dark); outline-offset:2px; }
  .saved-msg{ font-size:12.5px; color:var(--green-accent); font-weight:600; }
  .saved-msg.error{ color:#b42318; }
  .avatar-row{ display:flex; align-items:center; gap:18px; margin-bottom:22px; padding-bottom:20px; border-bottom:1px solid var(--border); }
  .avatar-big{ width:84px; height:84px; border-radius:50%; flex:none; overflow:hidden; display:flex; align-items:center; justify-content:center; background:var(--brand-tint, #e6f0e8); color:var(--green-accent); font-size:28px; font-weight:700; }
  .avatar-big img{ width:100%; height:100%; object-fit:cover; }
  .avatar-big [hidden]{ display:none; }
  .avatar-label{ font-size:12.5px; font-weight:600; color:var(--text-dark); margin-bottom:2px; }
  .avatar-hint{ font-size:12px; color:var(--text-mid); margin-bottom:10px; }
  @media (max-width:420px){ .avatar-row{ align-items:flex-start; gap:14px; } .avatar-big{ width:64px; height:64px; font-size:22px; } }
  .avatar-actions{ display:flex; gap:8px; margin-bottom:4px; }
  .btn-ghost{ min-height:36px; padding:0 14px; border:1px solid var(--border); border-radius:8px; background:#fff; font:inherit; font-size:13px; font-weight:600; color:var(--text-dark); cursor:pointer; }
  .btn-ghost:hover{ border-color:var(--text-light); }
  .btn-ghost.danger{ color:#b42318; }
  .btn-ghost[hidden]{ display:none; }
  .btn-ghost:focus-visible{ outline:2px solid var(--green-accent); outline-offset:2px; }
</style>
</head>
<body>
<div class="app">

@include('partials.admin-sidebar')

  <div class="main">
    <div class="topbar">
      <div class="hamburger" id="hamburgerBtn" tabindex="0" aria-label="Toggle sidebar"><span></span><span></span><span></span></div>
      <div class="topbar-right"></div>
    </div>

    <div class="content">
      <div class="page-head">
        <div class="back-arrow" data-href="{{ route('dashboard') }}" tabindex="0" aria-label="Back to dashboard"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
        <h1>Settings</h1>
      </div>

      <div class="settings-grid">
        <section class="card" aria-labelledby="profileHead">
          <h2 id="profileHead">Profile</h2>
          @php
            $avatarUrl = $user->avatarUrl();
            $initials = collect(preg_split('/\s+/', trim($user->name)))->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
          @endphp
          <div class="avatar-row">
            <div class="avatar-big" id="avatarBig" aria-hidden="true">
              <img id="avatarImg" src="{{ $avatarUrl }}" alt="" @if(! $avatarUrl) hidden @endif>
              <span id="avatarInitials" @if($avatarUrl) hidden @endif>{{ $initials }}</span>
            </div>
            <div>
              <div class="avatar-label">Profile picture</div>
              <div class="avatar-hint">Square JPG, PNG or WEBP · max 2 MB</div>
              <div class="avatar-actions">
                <button type="button" class="btn-ghost" id="avatarPick">{{ $avatarUrl ? 'Change' : 'Upload' }}</button>
                <button type="button" class="btn-ghost danger" id="avatarRemove" @if(! $avatarUrl) hidden @endif>Remove</button>
              </div>
              <span class="saved-msg" id="avatarMsg" role="status" aria-live="polite"></span>
              <input type="file" id="avatarInput" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" hidden>
            </div>
          </div>
          <form id="profileForm" novalidate>
            <div class="field">
              <label for="name">Full name</label>
              <input id="name" name="name" type="text" value="{{ $user->name }}" autocomplete="name" required maxlength="255">
              <span class="field-error" id="nameError"></span>
            </div>
            <div class="field">
              <label for="email">Email <x-info-tip text="You log in with this email." /></label>
              <input id="email" name="email" type="email" value="{{ $user->email }}" autocomplete="email" required maxlength="255">
              <span class="field-error" id="emailError"></span>
            </div>
            <div class="field confirm-field" id="emailPasswordField" hidden>
              <label for="emailPassword">Current password</label>
              <input id="emailPassword" type="password" autocomplete="current-password">
              <span class="field-error" id="emailPasswordError"></span>
            </div>
            <div class="actions">
              <button type="submit" class="btn-save" id="profileSave">Save changes</button>
              <span class="saved-msg" id="profileMsg" role="status" aria-live="polite"></span>
            </div>
          </form>
        </section>

        <section class="card" aria-labelledby="passwordHead">
          <h2 id="passwordHead">Change password</h2>
          <form id="passwordForm" novalidate>
            <div class="field">
              <label for="currentPassword">Current password</label>
              <input id="currentPassword" type="password" autocomplete="current-password" required>
              <span class="field-error" id="currentPasswordError"></span>
            </div>
            <div class="field">
              <label for="newPassword">New password</label>
              <input id="newPassword" type="password" autocomplete="new-password" required minlength="8" aria-describedby="passwordRules">
              @include('partials.password-rules', ['for' => 'newPassword'])
              <span class="field-error" id="newPasswordError"></span>
            </div>
            <div class="field">
              <label for="confirmPassword">Confirm new password</label>
              <input id="confirmPassword" type="password" autocomplete="new-password" required>
              <span class="field-error" id="confirmPasswordError"></span>
            </div>
            <div class="actions">
              <button type="submit" class="btn-save" id="passwordSave">Update password</button>
              <span class="saved-msg" id="passwordMsg" role="status" aria-live="polite"></span>
            </div>
          </form>
        </section>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const $ = id => document.getElementById(id);
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  const originalEmail = @json($user->email);

  document.querySelectorAll('[data-href]').forEach(el => {
    el.addEventListener('click', () => { window.location.href = el.dataset.href; });
  });

  $('logoutBtn').addEventListener('click', async () => {
    const logoutRes = await fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
    window.location.href = (await logoutRes.json().catch(() => ({}))).redirect || '/';
  });

  async function send(url, method, body){
    const res = await fetch(url, {
      method,
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
      body: JSON.stringify(body),
    });
    const data = await res.json().catch(() => ({}));
    return { ok: res.ok, status: res.status, data };
  }
  function clearErrors(form){ form.querySelectorAll('.field-error').forEach(e => e.textContent = ''); }

  // ----- Profile picture -----
  // Updates this page and the top-bar menu right away, no reload.
  function showAvatar(url){
    $('avatarImg').hidden = !url;
    $('avatarInitials').hidden = !!url;
    if (url) $('avatarImg').src = url;
    $('avatarRemove').hidden = !url;
    $('avatarPick').textContent = url ? 'Change' : 'Upload';
    const initials = document.querySelector('.pm-avatar')?.dataset.initials;
    document.querySelectorAll('.pm-trigger, .pm-avatar').forEach(el => {
      el.innerHTML = url ? `<img src="${url}" alt="">`
        : el.classList.contains('pm-avatar') ? (initials || '')
        : '<svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>';
    });
  }
  function avatarMsg(text, isError){
    $('avatarMsg').textContent = text;
    $('avatarMsg').classList.toggle('error', !!isError);
  }
  $('avatarPick').addEventListener('click', () => $('avatarInput').click());
  $('avatarInput').addEventListener('change', async (e) => {
    const file = e.target.files[0];
    e.target.value = '';
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) { avatarMsg('The picture must be 2 MB or smaller.', true); return; }
    avatarMsg('Uploading…');
    const body = new FormData();
    body.append('avatar', file);
    try {
      const res = await fetch(@json(route('admin.settings.avatar')), { method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body });
      const data = await res.json().catch(() => ({}));
      if (!res.ok) { avatarMsg(data.errors?.avatar?.[0] || 'Could not upload. Please try again.', true); return; }
      showAvatar(data.avatar_url);
      avatarMsg('Saved.');
    } catch { avatarMsg('Could not upload. Please try again.', true); }
  });
  $('avatarRemove').addEventListener('click', async () => {
    avatarMsg('Removing…');
    const r = await send(@json(route('admin.settings.avatar')), 'DELETE', {}).catch(() => ({ ok: false }));
    if (!r.ok) { avatarMsg('Could not remove. Please try again.', true); return; }
    showAvatar(null);
    avatarMsg('Removed.');
  });

  // ----- Profile -----
  // Changing the email (the login) asks for the current password.
  $('email').addEventListener('input', () => {
    $('emailPasswordField').hidden = $('email').value.trim().toLowerCase() === originalEmail.toLowerCase();
  });

  $('profileForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    clearErrors(e.target);
    $('profileMsg').textContent = '';
    $('profileMsg').classList.remove('error');
    $('profileSave').disabled = true;
    try {
      const r = await send(@json(route('admin.settings.profile')), 'PATCH', {
        name: $('name').value.trim(),
        email: $('email').value.trim().toLowerCase(),
        current_password: $('emailPassword').value || null,
      });
      if (r.ok) {
        $('profileMsg').textContent = 'Saved.';
        $('emailPassword').value = '';
        $('emailPasswordField').hidden = true;
        // Show the new name in the profile menu without a reload.
        document.querySelectorAll('.pm-name').forEach(n => n.textContent = r.data.name);
        document.querySelectorAll('.pm-email').forEach(n => n.textContent = r.data.email);
      } else if (r.status === 422) {
        const errs = r.data.errors || {};
        if (errs.name) $('nameError').textContent = errs.name[0];
        if (errs.email) $('emailError').textContent = errs.email[0];
        if (errs.current_password) $('emailPasswordError').textContent = errs.current_password[0];
      } else {
        throw new Error();
      }
    } catch {
      $('profileMsg').textContent = 'Could not save. Please try again.';
      $('profileMsg').classList.add('error');
    } finally {
      $('profileSave').disabled = false;
    }
  });

  // ----- Password -----
  $('passwordForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    clearErrors(e.target);
    $('passwordMsg').textContent = '';
    $('passwordMsg').classList.remove('error');
    if ($('newPassword').value !== $('confirmPassword').value) {
      $('confirmPasswordError').textContent = "The passwords don't match.";
      return;
    }
    $('passwordSave').disabled = true;
    try {
      const r = await send(@json(route('password.update')), 'PUT', {
        current_password: $('currentPassword').value,
        password: $('newPassword').value,
        password_confirmation: $('confirmPassword').value,
      });
      if (r.ok) {
        $('passwordMsg').textContent = 'Password updated.';
        e.target.reset();
        $('newPassword').dispatchEvent(new Event('input'));
      } else if (r.status === 422) {
        const errs = r.data.errors || {};
        if (errs.current_password) $('currentPasswordError').textContent = errs.current_password[0];
        if (errs.password) $('newPasswordError').textContent = errs.password[0];
      } else {
        throw new Error();
      }
    } catch {
      $('passwordMsg').textContent = 'Could not update the password. Please try again.';
      $('passwordMsg').classList.add('error');
    } finally {
      $('passwordSave').disabled = false;
    }
  });

  // Sidebar collapse, same as the other admin pages.
  const KEY = 'nestph_sidebar_collapsed';
  const hamburgerBtn = $('hamburgerBtn');
  const sidebar = $('sidebar');
  if (hamburgerBtn && sidebar) {
    if (localStorage.getItem(KEY) === '1') sidebar.classList.add('collapsed');
    hamburgerBtn.addEventListener('click', () => {
      localStorage.setItem(KEY, sidebar.classList.toggle('collapsed') ? '1' : '0');
    });
  }
  document.querySelectorAll('.sidebar [tabindex="0"], .hamburger, .back-arrow').forEach(el => {
    el.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); el.click(); }
    });
  });
})();
</script>
</body>
</html>
