{{--
    Shared tenant sidebar (normal nav). Included via:
        @include('partials.tenant-sidebar')

    Used directly by tenantdashboard.blade.php, tenanttickets.blade.php,
    and tenantaccount.blade.php (which never show a restricted variant --
    RestrictDelinquentTenant redirects a portal-restricted tenant away
    from all three before they'd ever render). Also used inside the
    @else branch of tenantbilling.blade.php and tenantdelinquency.blade.php,
    alongside partials.tenant-sidebar-restricted for their portal_restricted
    branch and each page's own blacklist takeover for $isBlacklisted.

    "active" is derived from the current route via request()->routeIs(),
    same pattern as partials/admin-sidebar.blade.php.
--}}
<aside class="sidebar" id="sidebar">
  <div class="sidebar-logo"><span class="logo-mark"></span><span class="logo-text">NEST.PH</span></div>
  <div class="sidebar-section-label">Tenant View</div>
  <ul class="nav-list">
    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" data-href="{{ route('dashboard') }}" tabindex="0">
      <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></span>
      <span class="label">Tenant Dashboard</span>
    </li>
    <li class="nav-item {{ request()->routeIs('tenant.tickets') ? 'active' : '' }}" data-href="{{ route('tenant.tickets') }}" tabindex="0">
      <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/></svg></span>
      <span class="label">Tickets</span>
    </li>
    <li class="nav-item {{ request()->routeIs('tenant.billing') ? 'active' : '' }}" data-href="{{ route('tenant.billing') }}" tabindex="0">
      <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></span>
      <span class="label">Billing and Payments</span>
    </li>
    <li class="nav-item {{ request()->routeIs('tenant.account') ? 'active' : '' }}" data-href="{{ route('tenant.account') }}" tabindex="0">
      <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></span>
      <span class="label">Profile</span>
    </li>
    <li class="nav-item {{ request()->routeIs('tenant.delinquency') ? 'active' : '' }}" data-href="{{ route('tenant.delinquency') }}" tabindex="0">
      <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg></span>
      <span class="label">Delinquency</span>
    </li>
  </ul>
  <div class="sidebar-footer">
    <div class="nav-item" id="logoutBtn" tabindex="0">
      <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.4 5.6a9 9 0 11-12.8 0M12 3v8"/></svg></span>
      <span class="label">Log Out</span>
    </div>
  </div>
</aside>