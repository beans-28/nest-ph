<style>
    .app-topbar {
        width: 100%;
        padding: 18px 48px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(99, 133, 104, 0.96);
        position: sticky;
        top: 0;
        z-index: 10;
        font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }
    .app-topbar .left,
    .app-topbar .right {
        display: flex;
        align-items: center;
        gap: 28px;
    }
    .app-topbar a,
    .app-topbar button {
        color: #f4f7f1;
        text-decoration: none;
        font-size: 13px;
        letter-spacing: .02em;
        background: none;
        border: none;
        cursor: pointer;
    }
    .app-topbar a:hover,
    .app-topbar button:hover {
        color: #d8e4d3;
    }
    .app-topbar .logo {
        font-size: 18px;
        font-weight: 600;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: #f4f7f1;
    }
    .app-topbar .logout-btn {
        border: 1px solid rgba(255,255,255,0.18);
        border-radius: 6px;
        padding: 10px 20px;
    }
</style>

<header class="app-topbar">
    <div class="left">
        <span class="logo">NEST.PH</span>

        @auth
            @php
                $roleName = auth()->user()->role?->role_name;
            @endphp

            @if($roleName === 'admin')
                <a href="{{ route('dashboard') }}">Manage Rooms</a>
                <a href="{{ route('dashboard') }}">Manage Users</a>
                <a href="{{ route('dashboard') }}">Reports</a>
            @else
                <a href="{{ route('dashboard') }}">My Bills</a>
                <a href="{{ route('dashboard') }}">My Applications</a>
            @endif
        @endauth
    </div>

    <div class="right">
        @auth
            <span>{{ auth()->user()->name }}</span>
            <button type="button" class="logout-btn" id="logout-btn">Log Out</button>
        @endauth
    </div>
</header>

<script>
document.getElementById('logout-btn')?.addEventListener('click', async function() {
    try {
        const response = await fetch('/logout', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (response.ok) {
            window.location.href = '/login/tenant';
        } else {
            alert('Something went wrong logging out. Please try again.');
        }
    } catch (error) {
        console.error('Logout error:', error);
        alert('Something went wrong logging out. Please try again.');
    }
});
</script>