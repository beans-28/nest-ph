<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | NEST.PH</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&family=Agbalumo&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        .page-wrap { overflow-x: hidden; }

        body {
            font-family: 'Roboto', system-ui, -apple-system, sans-serif;
            color: #292420;
            background: linear-gradient(180deg, #567357 0%, #59473f 100%);
            min-height: 100vh;
        }

        /* Decorative leaf textures — same technique as homepage */
        .textured { position: relative; overflow: hidden; }
        .textured .bg-texture {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            pointer-events: none;
            z-index: 0;
            mix-blend-mode: multiply;
            opacity: 0.5;
        }
        .textured > *:not(.bg-texture) { position: relative; z-index: 1; }

        .topnav {
            background: linear-gradient(90deg, #567357, #a2d9a4);
            padding: 14px clamp(20px, 5vw, 64px);
            display: flex;
            align-items: center;
            gap: clamp(16px, 3vw, 40px);
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: box-shadow 0.25s ease;
        }
        .topnav.scrolled {
            box-shadow: 0 4px 14px rgba(0,0,0,0.2);
        }
        .topnav .menu { flex: 1; display: flex; align-items: center; gap: 10px; }
        .topnav .menu a, .topnav .menu span {
            color: #fff; font-weight: 500; font-size: 14px;
            padding: 10px 6px; display: inline-flex; align-items: center; gap: 4px;
            text-decoration: none;
        }
        .topnav .menu a.pill {
            border: 1px solid rgba(255,255,255,0.5);
            border-radius: 999px;
            padding: 7px 16px;
        }
        .topnav .menu a.pill:hover { background: rgba(255,255,255,0.12); }
        .topnav .logo {
            display: flex; align-items: center; gap: 6px;
            color: #fff; font-weight: 700; font-size: 19px;
            letter-spacing: 0.02em; white-space: nowrap; text-decoration: none;
        }
        .topnav .logo .mark { width: 18px; height: 18px; border: 2px solid #fff; border-radius: 4px; flex-shrink: 0; }
        .topnav .buttons { flex: 1; display: flex; justify-content: flex-end; gap: 12px; }
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            height: 40px; padding: 0 18px; border: 2px solid #fff;
            font-weight: 500; font-size: 13.5px; letter-spacing: 0.02em; cursor: pointer;
            white-space: nowrap; text-decoration: none;
        }
        .btn-white { background: #fff; color: #292420; }
        .btn-outline-white { background: transparent; color: #fff; }

        /* ===== LOGIN AREA ===== */
        .login-grid {
            display: grid;
            grid-template-columns: 37% 63%;
            align-items: stretch;
            min-height: calc(100vh - 70px);
            padding: clamp(16px, 3vw, 20px) clamp(24px, 5vw, 60px) clamp(24px, 5vw, 60px) 0;
        }

        .login-left {
            position: relative;
            padding: 16px clamp(20px, 4vw, 40px) clamp(24px, 4vw, 40px) clamp(28px, 6vw, 80px);
            display: flex;
            flex-direction: column;
        }
        .back-button {
            background: none;
            border: none;
            color: #fff;
            font-size: 22px;
            cursor: pointer;
            line-height: 1;
            padding: 0;
            margin-bottom: 32px;
            align-self: flex-start;
        }
        .login-left-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-left h1 {
            color: #fff;
            font-weight: 700;
            font-size: clamp(24px, 3vw, 34px);
            line-height: 1.2;
            max-width: 340px;
        }
        .login-left h1 .accent {
            display: block;
            color: #44ad65;
            font-weight: 900;
            font-size: clamp(28px, 3.6vw, 40px);
            margin-top: 4px;
        }
        .brand-mark {
            margin-top: 36px;
            width: 150px;
            height: 150px;
            background: linear-gradient(180deg, #567357 0%, #a2d9a4 100%);
            border-radius: 0 80px 80px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .brand-mark span {
            font-family: 'Agbalumo', cursive;
            font-size: 96px;
            color: #fff;
            line-height: 1;
        }

        .login-right-wrap {
            position: relative;
            padding-top: 32px;
            display: flex;
            flex-direction: column;
        }

        .login-right {
            background: #eeeded;
            border-radius: 32px;
            padding: clamp(36px, 5vw, 52px) clamp(24px, 5vw, 56px) clamp(36px, 5vw, 52px);
            flex: 1;
        }

        .tab-header {
            position: absolute;
            top: 0;
            left: 32px;
            display: inline-flex;
            border-radius: 999px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }
        .tab-header a {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 13px 26px;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 0.02em;
            text-decoration: none;
            color: #526652;
            background: #d8dcd8;
        }
        .tab-header a.active { background: #eeeded; color: #194e19; }
        .tab-header a svg { width: 16px; height: 16px; }

        .login-right h2 {
            color: #567357;
            font-weight: 700;
            font-size: clamp(20px, 2.4vw, 26px);
            margin-bottom: 24px;
            margin-top: 20px;
        }

        .form-group { margin-bottom: 22px; max-width: 560px; }
        .form-group label {
            display: block;
            color: #567357;
            font-weight: 500;
            font-size: 13px;
            margin-bottom: 8px;
        }
        .form-group input {
            width: 100%;
            border: none;
            border-bottom: 1px solid #a6b69f;
            padding: 7px 4px;
            font-size: 14px;
            background: transparent;
            color: #292420;
            outline: none;
            font-family: inherit;
        }

        .btn-login {
            display: block;
            width: 100%;
            max-width: 560px;
            margin: 10px auto 0;
            padding: 17px 0;
            border: none;
            border-radius: 8px;
            background: #567357;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 0.05em;
            cursor: pointer;
            position: relative;
            transition: background 0.2s;
        }
        .btn-login:hover:not(:disabled) { background: #197335; }
        .btn-login:disabled { opacity: 0.7; cursor: not-allowed; }

        .spinner {
            display: none;
            width: 14px; height: 14px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top: 2px solid #fff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-right: 8px;
        }
        .btn-login.loading .spinner { display: inline-block; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .help-text {
            text-align: center;
            margin: 20px auto 0;
            max-width: 560px;
            font-weight: 700;
            font-size: 13px;
            color: #292420;
        }
        .help-text a { color: #567357; text-decoration: none; }
        .help-text.secondary { font-weight: 400; color: #4b5f4c; margin-top: 8px; }

        @media (max-width: 1024px) {
            .login-grid { grid-template-columns: 1fr; padding: 20px 28px 32px; }
            .login-left { padding: 0 0 24px; }
            .login-right { padding: 40px 28px 32px; }
            .tab-header { left: 16px; }
            .topnav { padding: 14px 28px; flex-wrap: wrap; }
        }
        @media (max-width: 640px) {
            .login-left h1 { font-size: 22px; }
            .login-left h1 .accent { font-size: 26px; }
            .brand-mark { width: 110px; height: 110px; }
            .brand-mark span { font-size: 70px; }
            .tab-header a { padding: 11px 16px; font-size: 12.5px; }
        }
    </style>
</head>
<body>

    <nav class="topnav textured">
        <img src="{{ asset('images/leaf-texture-2.png') }}" class="bg-texture" alt="">
        <div class="menu">
            <a href="{{ route('public.vr') }}">VR TOUR</a>
            <a href="{{ route('public.rooms') }}">ROOMS</a>
            <a href="{{ route('home') }}">HOME</a>
            <a href="{{ route('public.dorminfo') }}" class="pill">Dorm Info</a>
        </div>
        <div class="logo"><span class="mark"></span> NEST.PH</div>
        <div class="buttons">
            <a href="{{ route('login.admin') }}" class="btn btn-white">Admin</a>
            <a href="{{ route('public.apply') }}" class="btn btn-outline-white">Apply</a>
            <a href="{{ route('login.tenant') }}" class="btn btn-white">Log In</a>
        </div>
    </nav>

    <div class="page-wrap">
    <div class="login-grid">
        <div class="login-left">
            <button class="back-button" type="button" aria-label="Go back" onclick="window.location.href='{{ route('home') }}'">←</button>
            <div class="login-left-content">
                <h1>Study hard, make friends, and live your<span class="accent">NEST life.</span></h1>
                <div class="brand-mark"><span>N</span></div>
            </div>
        </div>

        <div class="login-right-wrap">
            <div class="tab-header">
                <a href="{{ route('login.tenant') }}" class="active">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                    TENANT
                </a>
                <a href="{{ route('login.admin') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M2 20c0-3.5 3-5.5 7-5.5s7 2 7 5.5M16 14.5c3 0 5.5 1.8 5.5 5"/></svg>
                    ADMIN
                </a>
            </div>

            <div class="login-right">
                <h2>Tenant Log In</h2>

                <form id="login-form" action="/tenant/login" method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" type="email" placeholder="you@example.com" />
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input id="password" type="password" placeholder="Enter password" />
                    </div>
                    <button type="submit" class="btn-login">
                        <span class="spinner"></span>
                        <span class="btn-text">Login</span>
                    </button>
                </form>

                <p class="help-text">Did you forget your password? <a href="{{ route('passwords', ['from' => 'tenant']) }}">Forgot Password</a></p>
            </div>
        </div>
    </div>
    </div>

<script>
document.getElementById('login-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const button = this.querySelector('.btn-login');
    if (button.disabled) return;

    button.disabled = true;
    button.classList.add('loading');

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    try {
        const response = await fetch("/tenant/login", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ email: email, password: password })
        });

        if (response.ok) {
            alert("Login successful!");
            window.location.href = "/dashboard";
        } else {
            const data = await response.json();
            alert(data.message || "Invalid credentials.");
            button.disabled = false;
            button.classList.remove('loading');
        }
    } catch (error) {
        console.error("Error:", error);
        button.disabled = false;
        button.classList.remove('loading');
    }
});

window.addEventListener('scroll', function () {
    const nav = document.querySelector('.topnav');
    if (window.scrollY > 10) {
        nav.classList.add('scrolled');
    } else {
        nav.classList.remove('scrolled');
    }
});
</script>

</body>
</html>