<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Under Review | NEST.PH</title>
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
        .textured { position: relative; overflow: hidden; }
        .textured .bg-texture {
            position: absolute; inset: 0; width: 100%; height: 100%;
            object-fit: cover; pointer-events: none; z-index: 0;
            mix-blend-mode: multiply; opacity: 0.5;
        }
        .textured > *:not(.bg-texture) { position: relative; z-index: 1; }

        .topnav {
            background: linear-gradient(90deg, #567357, #a2d9a4);
            padding: 14px clamp(20px, 5vw, 64px);
            display: flex; align-items: center; gap: clamp(16px, 3vw, 40px);
            position: sticky; top: 0; z-index: 1000;
            transition: box-shadow 0.25s ease;
        }
        .topnav.scrolled { box-shadow: 0 4px 14px rgba(0,0,0,0.2); }
        .topnav .menu { flex: 1; display: flex; align-items: center; gap: 10px; }
        .topnav .menu a, .topnav .menu span {
            color: #fff; font-weight: 500; font-size: 14px;
            padding: 10px 6px; display: inline-flex; align-items: center; gap: 4px;
            text-decoration: none;
        }
        .topnav .menu a.pill { border: 1px solid rgba(255,255,255,0.5); border-radius: 999px; padding: 7px 16px; }
        .topnav .logo {
            display: flex; align-items: center; gap: 6px; color: #fff; font-weight: 700;
            font-size: 19px; letter-spacing: 0.02em; white-space: nowrap; text-decoration: none;
        }
        .topnav .logo .mark { width: 18px; height: 18px; border: 2px solid #fff; border-radius: 4px; flex-shrink: 0; }
        .topnav .buttons { flex: 1; display: flex; justify-content: flex-end; gap: 12px; }
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            height: 40px; padding: 0 18px; border: 2px solid #fff;
            font-weight: 500; font-size: 13.5px; cursor: pointer;
            white-space: nowrap; text-decoration: none;
        }
        .btn-white { background: #fff; color: #292420; }
        .btn-outline-white { background: transparent; color: #fff; }

        .login-grid {
            display: grid; grid-template-columns: 37% 63%; align-items: stretch;
            min-height: calc(100vh - 70px);
            padding: clamp(16px, 3vw, 20px) clamp(24px, 5vw, 60px) clamp(24px, 5vw, 60px) 0;
        }
        .login-left {
            position: relative; padding: 16px clamp(20px, 4vw, 40px) clamp(24px, 4vw, 40px) clamp(28px, 6vw, 80px);
            display: flex; flex-direction: column;
        }
        .back-button {
            background: none; border: none; color: #fff; font-size: 22px; cursor: pointer;
            line-height: 1; padding: 0; margin-bottom: 32px; align-self: flex-start;
        }
        .login-left-content { flex: 1; display: flex; flex-direction: column; justify-content: center; }
        .login-left h1 { color: #fff; font-weight: 700; font-size: clamp(24px, 3vw, 34px); line-height: 1.2; max-width: 360px; }
        .login-left h1 .accent { display: block; color: #44ad65; font-weight: 900; font-size: clamp(28px, 3.6vw, 40px); margin-top: 4px; }
        .brand-mark {
            margin-top: 36px; width: 150px; height: 150px;
            background: linear-gradient(180deg, #567357 0%, #a2d9a4 100%);
            border-radius: 0 80px 80px 0; display: flex; align-items: center; justify-content: center;
        }
        .brand-mark span { font-family: 'Agbalumo', cursive; font-size: 96px; color: #fff; line-height: 1; }

        .login-right-wrap { position: relative; padding-top: 16px; display: flex; flex-direction: column; }
        .login-right {
            background: #eeeded; border-radius: 32px;
            padding: clamp(40px, 6vw, 60px) clamp(24px, 5vw, 56px);
            flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;
            text-align: center;
        }

        .eyebrow { color: #44ad65; font-weight: 700; font-size: 15px; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 32px; align-self: flex-start; }

        .pending-badge { width: 84px; height: 84px; margin: 0 auto 26px; display: block; }
        .login-right h2 { color: #194e19; font-weight: 900; font-size: clamp(22px, 2.8vw, 30px); margin-bottom: 18px; }
        .login-right p { color: #292420; font-size: 15px; line-height: 1.7; max-width: 520px; margin: 0 auto 30px; }

        .btn-login {
            display: inline-block; padding: 17px 40px; border: none; border-radius: 8px;
            background: #567357; color: #fff; font-weight: 700; font-size: 14px;
            letter-spacing: 0.05em; text-transform: uppercase; cursor: pointer; text-decoration: none;
            transition: background 0.2s;
        }
        .btn-login:hover { background: #197335; }

        .resubmit-note { color: #6b6b6b; font-size: 12.5px; margin: 4px 0 12px; }
        .btn-resubmit {
            display: inline-block; padding: 10px 22px; border: 1.5px solid #567357; border-radius: 8px;
            background: transparent; color: #345234; font-weight: 600; font-size: 12.5px;
            letter-spacing: 0.03em; cursor: pointer; text-decoration: none; transition: background 0.2s;
        }
        .btn-resubmit:hover { background: rgba(86,115,87,0.08); }

        @media (max-width: 1024px) {
            .login-grid { grid-template-columns: 1fr; padding: 20px 28px 32px; }
            .login-left { padding: 0 0 24px; }
            .login-right { padding: 40px 28px; }
            .topnav { padding: 14px 28px; flex-wrap: wrap; }
        }
        @media (max-width: 640px) {
            .login-left h1 { font-size: 22px; }
            .brand-mark { width: 110px; height: 110px; }
            .brand-mark span { font-size: 70px; }
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
            <button class="back-button" type="button" aria-label="Go back" onclick="window.location.href='{{ route('home') }}'">&larr;</button>
            <div class="login-left-content">
                <h1>Study hard, make friends, and live your<span class="accent">NEST life.</span></h1>
                <div class="brand-mark"><span>N</span></div>
            </div>
        </div>

        <div class="login-right-wrap">
            <div class="login-right">
                <div class="eyebrow">Payment Submitted</div>

                <svg class="pending-badge" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="32" cy="32" r="30" fill="#e8b13c"/>
                    <path d="M32 16v16l11 6" stroke="#fff" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </svg>

                <h2>Your payment is being verified</h2>
                <p>We've received your proof of payment and it's currently being reviewed by our admin team. You'll receive your official Move-In Permit by email once it's verified — this usually takes 1&ndash;2 business days.</p>

                <p class="resubmit-note">Made a mistake, or need to submit a different proof?</p>
                <a href="{{ route('tenant.movein.payment-type') }}" class="btn-resubmit">Submit Again</a>
            </div>
        </div>
    </div>
    </div>

<script>
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
