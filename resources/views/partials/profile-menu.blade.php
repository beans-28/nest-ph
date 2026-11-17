{{--
    Profile menu in the top bar (admin and tenant pages). Included by the
    admin and tenant sidebar partials, so every page gets it; the script
    moves it into .topbar-right, replacing the old profile icon.

    Log Out: each page's own script listens on the sidebar's #logoutBtn,
    so that button stays in the page but is hidden once this menu is in
    place, and "Log out" here simply presses it. If a page has no top bar,
    the menu isn't shown and the sidebar's Log Out stays visible.

    Pass ['restricted' => true] for portal-restricted tenants, who can't
    open their Account page.
--}}
@php
    $pmUser = auth()->user();
    $pmTenant = $pmUser?->tenant;
    $pmName = $pmTenant?->full_name ?: ($pmUser?->name ?? 'Account');
    $pmEmail = $pmUser?->email;
    $pmPhoto = $pmTenant ? $pmTenant->id_photo_url : $pmUser?->avatarUrl();
    $pmRole = $pmTenant ? 'Tenant' : ($pmUser?->hasPrivilege('manage_users') ? 'Owner' : 'Admin');
    $pmSettings = ($restricted ?? false) ? null : ($pmTenant ? route('tenant.account') : route('admin.settings'));
    $pmInitials = collect(preg_split('/\s+/', trim($pmName)))->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
@endphp
<style>
  .pm-wrap{ position:relative; }
  .pm-trigger{ width:34px; height:34px; padding:0; border:none; border-radius:50%; background:rgba(255,255,255,.92); color:var(--green-dark, #2f6f3c); display:flex; align-items:center; justify-content:center; cursor:pointer; overflow:hidden; }
  .pm-trigger svg{ width:18px; height:18px; }
  .pm-trigger img{ width:100%; height:100%; object-fit:cover; display:block; }
  .pm-trigger:focus-visible{ outline:2px solid #fff; outline-offset:2px; }
  .pm-panel{ position:fixed; top:62px; right:16px; width:min(280px, calc(100vw - 32px)); background:#fff; border:1px solid var(--border, #e3e7e3); border-radius:12px; box-shadow:0 14px 40px rgba(0,0,0,.18); z-index:200; padding:6px; font-family:var(--font-body); display:none; }
  .pm-panel.open{ display:block; }
  .pm-head{ display:flex; align-items:center; gap:12px; padding:10px 10px 12px; border-bottom:1px solid var(--border, #e3e7e3); margin-bottom:6px; }
  .pm-avatar{ width:44px; height:44px; border-radius:50%; flex:none; overflow:hidden; display:flex; align-items:center; justify-content:center; background:var(--brand-tint, #e6f0e8); color:var(--green-accent, #2f6f3c); font-weight:700; font-size:15px; }
  .pm-avatar img{ width:100%; height:100%; object-fit:cover; }
  .pm-who{ min-width:0; }
  .pm-name{ font-size:14px; font-weight:700; color:var(--text-dark, #26302a); overflow-wrap:anywhere; }
  @media (pointer:coarse){ .pm-trigger{ width:40px; height:40px; } }
  .pm-email{ font-size:12px; color:var(--text-mid, #5b6b60); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .pm-role{ display:inline-block; margin-top:3px; font-size:11px; font-weight:600; color:var(--green-accent, #2f6f3c); }
  .pm-item{ display:flex; align-items:center; gap:10px; width:100%; padding:10px; border:none; border-radius:8px; background:none; font:inherit; font-size:13.5px; color:var(--text-dark, #26302a); text-decoration:none; cursor:pointer; text-align:left; }
  .pm-item:hover{ background:#f3f4f3; }
  .pm-item:focus-visible{ outline:2px solid var(--green-accent, #2f6f3c); outline-offset:-2px; }
  .pm-item svg{ width:18px; height:18px; flex:none; color:var(--text-mid, #5b655d); }
  .pm-item.danger, .pm-item.danger svg{ color:#b42318; }
  /* Once the menu is in the top bar, the sidebar's Log Out is redundant. */
  body.has-profile-menu .sidebar-footer #logoutBtn{ display:none; }
  body.has-profile-menu .topbar-right .topbar-username, body.has-profile-menu .topbar-right .topbar-name{ display:none; }
</style>

<div class="pm-wrap" id="profileMenu" hidden>
  <button type="button" class="pm-trigger" id="profileMenuBtn" aria-haspopup="true" aria-expanded="false" aria-controls="profileMenuPanel" aria-label="Account menu for {{ $pmName }}">
    @if($pmPhoto)
      <img src="{{ $pmPhoto }}" alt="">
    @else
      <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
    @endif
  </button>
  <div class="pm-panel" id="profileMenuPanel">
    <div class="pm-head">
      <span class="pm-avatar" aria-hidden="true" data-initials="{{ $pmInitials }}">@if($pmPhoto)<img src="{{ $pmPhoto }}" alt="">@else{{ $pmInitials }}@endif</span>
      <div class="pm-who">
        <div class="pm-name">{{ $pmName }}</div>
        @if($pmEmail)<div class="pm-email" title="{{ $pmEmail }}">{{ $pmEmail }}</div>@endif
        <span class="pm-role">{{ $pmRole }}</span>
      </div>
    </div>
    @if($pmSettings)
      <a class="pm-item" href="{{ $pmSettings }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        {{ $pmTenant ? 'My Account' : 'Settings' }}
      </a>
    @endif
    <button type="button" class="pm-item danger" id="profileMenuLogout">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
      Log out
    </button>
  </div>
</div>

<script>
(function(){
  const wrap = document.getElementById('profileMenu');
  const btn = document.getElementById('profileMenuBtn');
  const panel = document.getElementById('profileMenuPanel');

  document.addEventListener('DOMContentLoaded', () => {
    const right = document.querySelector('.topbar-right');
    if(!right) return; // no top bar: keep the sidebar's Log Out
    // Replace the old profile icon / avatar link (not the notification bell).
    right.querySelectorAll('.topbar-icon:not(.admin-bell):not(.notif-bell)').forEach(el => el.remove());
    right.appendChild(wrap);
    wrap.hidden = false;
    document.body.classList.add('has-profile-menu');
  });

  const close = () => { panel.classList.remove('open'); btn.setAttribute('aria-expanded', 'false'); };
  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    const open = !panel.classList.contains('open');
    // Only one top-bar panel open at a time.
    document.querySelectorAll('.admin-notif-panel.open, .notif-panel.open').forEach(p => p.classList.remove('open'));
    // Sit just under the top bar (admin and tenant bars differ in height).
    const bar = btn.closest('.topbar');
    if(bar) panel.style.top = Math.round(bar.getBoundingClientRect().bottom + 6) + 'px';
    panel.classList.toggle('open', open);
    btn.setAttribute('aria-expanded', String(open));
    if(open) panel.querySelector('.pm-item')?.focus();
  });
  document.addEventListener('click', (e) => { if(panel.classList.contains('open') && !wrap.contains(e.target)) close(); });
  document.addEventListener('keydown', (e) => { if(e.key === 'Escape' && panel.classList.contains('open')){ close(); btn.focus(); } });

  // The page's own script handles logging out from the sidebar button.
  document.getElementById('profileMenuLogout').addEventListener('click', () => {
    const sidebarLogout = document.getElementById('logoutBtn');
    if(sidebarLogout){ sidebarLogout.click(); return; }
    const f = document.createElement('form');
    f.method = 'POST'; f.action = @json(route('logout'));
    f.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
    document.body.appendChild(f); f.submit();
  });
})();
</script>
