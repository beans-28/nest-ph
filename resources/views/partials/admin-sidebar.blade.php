<aside class="sidebar" id="sidebar">
    @include('partials.sidebar-brand')
    @php
        // Hide links this admin has no privilege for (rules live in
        // App\Http\Middleware\EnsureAdminHasPrivilege, which also blocks the URLs).
        $navUser = auth()->user()?->loadMissing('privileges');
        $canSee = function (string $route) use ($navUser) {
            $needed = \App\Http\Middleware\EnsureAdminHasPrivilege::forRoute($route);
            return $needed === null || ($navUser && $navUser->hasPrivilege($needed));
        };

        // Grouped so the everyday pages sit at the top. Each entry:
        // [route name, active pattern, label, svg icon paths].
        // Lease Management now lives in each tenant's profile (Tenant Manager).
        $navGroups = [
            'Daily Work' => [
                ['dashboard', 'dashboard', 'Dashboard', '<path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>'],
                ['payments.index', 'payments.*', 'Billing and Payments', '<path d="M20 11H4M20 7H4"/><path d="M7 21V4a1 1 0 011-1h4a1 1 0 010 12H7"/>'],
                ['tenant-manager.index', 'tenant-manager.*', 'Tenant Manager', '<path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>'],
                ['applications.index', 'applications.*', 'Applications', '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/>'],
                ['tickets.index', 'tickets.*', 'Tickets', '<path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/>'],
                ['inquiries.index', 'inquiries.*', 'Inquiries', '<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>'],
                ['delinquency.index', 'delinquency.*', 'Delinquency', '<path d="M12 9v4M12 17h.01" stroke-linecap="round"/><circle cx="12" cy="12" r="9"/>'],
            ],
            'Rooms' => [
                ['admin.addfloor', 'admin.addfloor', 'Vacancy Monitor', '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="4" width="7" height="7"/><rect x="3" y="15" width="7" height="7"/><rect x="14" y="15" width="7" height="7"/>'],
                ['vr.index', 'vr.*', 'VR Management', '<circle cx="12" cy="12" r="10"/><path d="m14.31 8 5.74 9.94"/><path d="M9.69 8h11.48"/><path d="m7.38 12 5.74-9.94"/><path d="M9.69 16 3.95 6.06"/><path d="M14.31 16H2.83"/><path d="m16.62 12-5.74 9.94"/>'],
            ],
            'Management' => [
                ['reports.index', 'reports.*', 'Reports', '<path d="M3 3v18h18"/><path d="M18 17V9M13 17V5M8 17v-3"/>'],
                ['admin-privileges.index', 'admin-privileges.*', 'Admin Privileges', '<path d="M12 2l8 4v6c0 5-3.5 9-8 10-4.5-1-8-5-8-10V6l8-4z"/><path d="M9 12l2 2 4-4"/>'],
                ['dormitory-profile.index', 'dormitory-profile.*', 'Dormitory Profile', '<path d="M10 12h4"/><path d="M10 8h4"/><path d="M14 21v-3a2 2 0 0 0-4 0v3"/><path d="M6 10H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2"/><path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16"/>'],
            ],
        ];
    @endphp
    <ul class="nav-list">
        @foreach ($navGroups as $groupName => $items)
            @php $visibleItems = array_filter($items, fn ($i) => $i[0] === 'dashboard' || $canSee($i[0])); @endphp
            @if (count($visibleItems))
                <li class="sidebar-section-label nav-group-label" aria-hidden="true">{{ $groupName }}</li>
                @foreach ($visibleItems as [$routeName, $activePattern, $label, $icon])
                    <li class="nav-item {{ request()->routeIs($activePattern) ? 'active' : '' }}" data-href="{{ route($routeName) }}" tabindex="0">
                        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">{!! $icon !!}</svg></span>
                        <span class="label">{{ $label }}</span>
                    </li>
                @endforeach
            @endif
        @endforeach
    </ul>
    <div class="sidebar-footer">
        <div class="nav-item" id="logoutBtn" tabindex="0">
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.4 5.6a9 9 0 11-12.8 0M12 3v8"/></svg></span>
            <span class="label">Log Out</span>
        </div>
        @include('partials.powered-by')
    </div>
</aside>
@include('partials.admin-notifications')
@include('partials.profile-menu')
<script src="{{ asset('js/sidebar-drawer.js') }}"></script>
