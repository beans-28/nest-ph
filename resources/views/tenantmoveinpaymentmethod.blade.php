<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment | NEST.PH</title>
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
            padding: clamp(40px, 6vw, 56px) clamp(24px, 5vw, 56px);
            flex: 1; display: flex; flex-direction: column; align-items: center;
            text-align: center;
        }

        .eyebrow { color: #44ad65; font-weight: 700; font-size: 15px; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 30px; align-self: flex-start; }

        .payment-badge {
            width: 78px; height: 78px; border-radius: 50%; margin: 0 auto 22px;
            background: #5ea86a; display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 34px; font-weight: 700;
        }

        .login-right h2 { color: #567357; font-weight: 900; font-size: clamp(20px, 2.6vw, 28px); text-transform: uppercase; letter-spacing: 0.02em; margin-bottom: 6px; }
        .helper-text { font-weight: 600; font-size: 13.5px; color: #292420; margin-bottom: 30px; }

        .method-card {
            background: #fff; border-radius: 12px; padding: 24px 28px; text-align: left;
            width: 100%; max-width: 560px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 26px;
        }
        .method-card h3 { font-size: 15px; font-weight: 700; margin-bottom: 18px; }
        .method-option {
            display: flex; align-items: flex-start; gap: 14px; padding: 12px 0;
            cursor: pointer; border-radius: 8px;
        }
        .method-option input { margin-top: 4px; accent-color: #567357; width: 16px; height: 16px; flex-shrink: 0; }
        .method-icon {
            width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center;
            justify-content: center; font-weight: 900; font-size: 14px; color: #fff; flex-shrink: 0;
        }
        .method-icon.gcash { background: #007dfe; }
        .method-icon.bdo { background: #003da5; }
        .method-name { font-weight: 700; font-size: 14px; color: #292420; }
        .method-desc { font-size: 12.5px; color: #8a9690; margin-top: 2px; }

        .btn-login {
            display: inline-block; padding: 16px 40px; border: none; border-radius: 8px;
            background: #345234; color: #fff; font-weight: 700; font-size: 13.5px;
            letter-spacing: 0.05em; text-transform: uppercase; cursor: pointer;
            transition: background 0.2s;
        }
        .btn-login:hover:not(:disabled) { background: #26401f; }
        .btn-login:disabled { opacity: 0.5; cursor: not-allowed; }

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
            <button class="back-button" type="button" aria-label="Go back" onclick="window.location.href='{{ route('tenant.movein.payment-type') }}'">←</button>
            <div class="login-left-content">
                <h1>Study hard, make friends, and live your<span class="accent">NEST life.</span></h1>
                <div class="brand-mark"><span>N</span></div>
            </div>
        </div>

        <div class="login-right-wrap">
            <div class="login-right">
                <div class="eyebrow">Payment</div>

                <div class="payment-badge">₱</div>

                <h2>Payment Options</h2>
                <p class="helper-text">Your room will be automatically reserved when payment is received!</p>

                <form method="POST" action="{{ route('tenant.movein.payment-method.store') }}" style="width:100%; display:flex; flex-direction:column; align-items:center;">
                    @csrf
                    <div class="method-card">
                        <h3>Select Payment Method</h3>

                        <label class="method-option">
                            <input type="radio" name="payment_method" value="gcash" required>
                            <div class="method-icon gcash">G</div>
                            <div>
                                <div class="method-name">GCash</div>
                                <div class="method-desc">Pay using your GCash account or mobile number</div>
                            </div>
                        </label>

                        <label class="method-option">
                            <input type="radio" name="payment_method" value="bdo" required>
                            <div class="method-icon bdo">B</div>
                            <div>
                                <div class="method-name">BDO</div>
                                <div class="method-desc">Pay using your BDO account</div>
                            </div>
                        </label>
                    </div>

                    <button type="submit" class="btn-login">Proceed with Payment</button>
                </form>
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
