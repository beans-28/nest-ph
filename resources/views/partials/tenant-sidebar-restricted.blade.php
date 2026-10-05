{{--
    Shared portal-restricted lock-panel sidebar. Included via:
        @include('partials.tenant-sidebar-restricted')

    Used only inside the @if($portalRestricted) branch of
    tenantbilling.blade.php and tenantdelinquency.blade.php -- the only
    two pages a portal-restricted tenant can reach. Replaces the normal
    nav entirely since those links would just bounce the tenant back
    here anyway. Log Out stays available.
--}}
<aside class="sidebar restricted-lock" id="sidebar">
  @include('partials.sidebar-brand')
  <div class="lock-panel-body">
    <div class="lock-icon-circle">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/></svg>
    </div>
    <h2 class="lock-panel-title">PORTAL ACCESS<br>RESTRICTED</h2>
    <p class="lock-panel-text">Your rent is overdue. Pay your outstanding balance and upload your proof on the Billing page. Full portal access comes back once it's settled.</p>
  </div>
  <div class="sidebar-footer">
    <div class="nav-item" id="logoutBtn" tabindex="0">
      <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.4 5.6a9 9 0 11-12.8 0M12 3v8"/></svg></span>
      <span class="label">Log Out</span>
    </div>
    @include('partials.powered-by')
  </div>
</aside>
@include('partials.tenant-notifications')

<script src="{{ asset('js/sidebar-drawer.js') }}"></script>
