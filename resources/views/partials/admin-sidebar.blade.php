<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo"><span class="logo-mark"></span><span class="logo-text">NEST.PH</span></div>
    <div class="sidebar-section-label">Quick Access</div>
    <ul class="nav-list">
        <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" data-href="{{ route('dashboard') }}">
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></span>
            <span class="label">Dashboard</span>
        </li>
        <li class="nav-item {{ request()->routeIs('tenant-manager.*') ? 'active' : '' }}" data-href="{{ route('tenant-manager.index') }}">
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></span>
            <span class="label">Tenant Manager</span>
        </li>
        <li class="nav-item {{ request()->routeIs('payments.*') ? 'active' : '' }}" data-href="{{ route('payments.index') }}">
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></span>
            <span class="label">Billing and Payments</span>
        </li>
        <li class="nav-item {{ request()->routeIs('delinquency.*') ? 'active' : '' }}" data-href="{{ route('delinquency.index') }}">
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg></span>
            <span class="label">Delinquency</span>
        </li>
        <li class="nav-item {{ request()->routeIs('admin.addfloor') ? 'active' : '' }}" data-href="{{ route('admin.addfloor') }}">
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="4" width="7" height="7"/><rect x="3" y="15" width="7" height="7"/><rect x="14" y="15" width="7" height="7"/></svg></span>
            <span class="label">Vacancy Monitor</span>
        </li>
        <li class="nav-item {{ request()->routeIs('tickets.*') ? 'active' : '' }}" data-href="{{ route('tickets.index') }}">
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/></svg></span>
            <span class="label">Tickets</span>
        </li>
        <li class="nav-item {{ request()->routeIs('applications.*') ? 'active' : '' }}" data-href="{{ route('applications.index') }}">
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/></svg></span>
            <span class="label">Applications</span>
        </li>
        <li class="nav-item {{ request()->routeIs('inquiries.*') ? 'active' : '' }}" data-href="{{ route('inquiries.index') }}">
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></span>
            <span class="label">Inquiries</span>
        </li>
        <li class="nav-item {{ request()->routeIs('vr.*') ? 'active' : '' }}" data-href="{{ route('vr.index') }}">
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m14.31 8 5.74 9.94"/><path d="M9.69 8h11.48"/><path d="m7.38 12 5.74-9.94"/><path d="M9.69 16 3.95 6.06"/><path d="M14.31 16H2.83"/><path d="m16.62 12-5.74 9.94"/></svg></span>
            <span class="label">VR Management</span>
        </li>
        <li class="nav-item {{ request()->routeIs('contracts.*') ? 'active' : '' }}" data-href="{{ route('contracts.index') }}">
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21 17-2.156-1.868A.5.5 0 0 0 18 15.5v.5a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1c0-2.545-3.991-3.97-8.5-4a1 1 0 0 0 0 5c4.153 0 4.745-11.295 5.708-13.5a2.5 2.5 0 1 1 3.31 3.284"/><path d="M3 21h18"/></svg></span>
            <span class="label">Lease Management</span>
        </li>
        <li class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}" data-href="{{ route('reports.index') }}">
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M18 17V9M13 17V5M8 17v-3"/></svg></span>
            <span class="label">Reports</span>
        </li>
        <li class="nav-item {{ request()->routeIs('admin-privileges.*') ? 'active' : '' }}" data-href="{{ route('admin-privileges.index') }}">
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l8 4v6c0 5-3.5 9-8 10-4.5-1-8-5-8-10V6l8-4z"/><path d="M9 12l2 2 4-4"/></svg></span>
            <span class="label">Admin Privileges</span>
        </li>
        <li class="nav-item {{ request()->routeIs('dormitory-profile.*') ? 'active' : '' }}" data-href="{{ route('dormitory-profile.index') }}">
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 12h4"/><path d="M10 8h4"/><path d="M14 21v-3a2 2 0 0 0-4 0v3"/><path d="M6 10H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2"/><path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16"/></svg></span>
            <span class="label">Dormitory Profile</span>
        </li>
    </ul>
    <div class="sidebar-footer">
        <div class="nav-item" id="logoutBtn">
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.4 5.6a9 9 0 11-12.8 0M12 3v8"/></svg></span>
            <span class="label">Log Out</span>
        </div>
    </div>
</aside>
