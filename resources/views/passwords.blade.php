<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Password Help | NEST.PH</title>
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

        /* Decorative leaf textures — same technique as homepage/login pages */
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

        /* ===== PASSWORD RESET AREA — same skeleton as the login pages ===== */
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
            max-width: 360px;
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
            padding-top: 16px;
            display: flex;
            flex-direction: column;
        }

        .login-right {
            background: #eeeded;
            border-radius: 32px;
            padding: clamp(36px, 5vw, 52px) clamp(24px, 5vw, 56px) clamp(36px, 5vw, 52px);
            flex: 1;
        }

        .login-right h2 {
            color: #567357;
            font-weight: 700;
            font-size: clamp(20px, 2.4vw, 26px);
            margin-bottom: 10px;
        }
        .login-right .intro {
            color: #7a7a7a;
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 26px;
            max-width: 560px;
        }

        /* Step cards — only one visible at a time */
        .step-card { display: none; }
        .step-card.active { display: block; }

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

        /* OTP code boxes */
        .otp-row { display: flex; gap: 12px; margin-bottom: 26px; }
        .otp-box {
            width: 52px;
            height: 60px;
            text-align: center;
            font-size: 20px;
            font-weight: 700;
            border: 1px solid #a6b69f;
            border-radius: 10px;
            background: #fff;
            color: #292420;
            font-family: inherit;
            outline: none;
        }
        .otp-box:focus { border-color: #567357; box-shadow: 0 0 0 3px rgba(86,115,87,0.15); }

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
            text-align: center;
            text-decoration: none;
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
        .help-text a { color: #567357; text-decoration: none; cursor: pointer; }

        .field-error {
            color: #b3261e;
            font-size: 12px;
            margin-top: 6px;
            display: none;
        }
        .field-error.visible { display: block; }

        /* Step 4 — success */
        .success-badge {
            width: 64px;
            height: 64px;
            margin: 0 auto 22px;
            display: block;
        }
        #step-success h2, #step-success .intro { text-align: center; margin-left: auto; margin-right: auto; }

        @media (max-width: 1024px) {
            .login-grid { grid-template-columns: 1fr; padding: 20px 28px 32px; }
            .login-left { padding: 0 0 24px; }
            .login-right { padding: 40px 28px 32px; }
            .topnav { padding: 14px 28px; flex-wrap: wrap; }
        }
        @media (max-width: 640px) {
            .login-left h1 { font-size: 22px; }
            .brand-mark { width: 110px; height: 110px; }
            .brand-mark span { font-size: 70px; }
            .otp-box { width: 44px; height: 52px; font-size: 17px; }
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
            <button class="back-button" type="button" aria-label="Go back" onclick="window.location.href='{{ request('from') === 'admin' ? route('login.admin') : route('login.tenant') }}'">←</button>
            <div class="login-left-content">
                <h1>Study hard, make friends, and live your<span class="accent">NEST life.</span></h1>
                <div class="brand-mark"><span>N</span></div>
            </div>
        </div>

        <div class="login-right-wrap">
            <div class="login-right">

                {{-- STEP 1 — request the code --}}
                <div class="step-card active" id="step-email" data-step="1">
                    <h2>Forgot Password</h2>
                    <p class="intro">Enter the email address linked to your account and we'll send you a 5-digit code to reset your password.</p>
                    <form id="emailForm">
                        <div class="form-group">
                            <label for="forgot_email">Email</label>
                            <input id="forgot_email" name="email" type="email" placeholder="you@example.com" required />
                            <div class="field-error" id="emailError"></div>
                        </div>
                        <button type="submit" class="btn-login" id="sendCodeBtn">
                            <span class="spinner"></span>
                            <span class="btn-text">Send Code</span>
                        </button>
                    </form>
                </div>

                {{-- STEP 2 — enter the code --}}
                <div class="step-card" id="step-code" data-step="2">
                    <h2>Verify Code</h2>
                    <p class="intro">We sent a code to <strong id="codeEmailDisplay">your email</strong>. Enter the 5-digit code below.</p>
                    <div class="otp-row">
                        <input type="text" inputmode="numeric" maxlength="1" class="otp-box" autocomplete="one-time-code">
                        <input type="text" inputmode="numeric" maxlength="1" class="otp-box">
                        <input type="text" inputmode="numeric" maxlength="1" class="otp-box">
                        <input type="text" inputmode="numeric" maxlength="1" class="otp-box">
                        <input type="text" inputmode="numeric" maxlength="1" class="otp-box">
                    </div>
                    <div class="field-error" id="codeError" style="margin-bottom: 16px;"></div>
                    <button type="button" class="btn-login" id="verifyCodeBtn">
                        <span class="spinner"></span>
                        <span class="btn-text">Verify Code</span>
                    </button>
                    <p class="help-text">Haven't got the email yet? <a id="resendLink">Resend email</a></p>
                </div>

                {{-- STEP 3 — set the new password --}}
                <div class="step-card" id="step-password" data-step="3">
                    <h2>Password Reset</h2>
                    <p class="intro">Create a new password. Ensure it differs from previous ones for security.</p>
                    <form id="passwordForm">
                        <input type="hidden" name="email" id="resetEmailField" value="">
                        <input type="hidden" name="code" id="resetCodeField" value="">
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input id="password" name="password" type="password" placeholder="Enter new password" required minlength="8" />
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Confirm new password" required minlength="8" />
                        </div>
                        <div class="field-error" id="passwordError" style="margin-bottom: 16px;"></div>
                        <button type="submit" class="btn-login">
                            <span class="spinner"></span>
                            <span class="btn-text">Update Password</span>
                        </button>
                    </form>
                </div>

                {{-- STEP 4 — confirmation --}}
                <div class="step-card" id="step-success" data-step="4">
                    <svg class="success-badge" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M32 2 L37.5 7.5 L45 5 L47.5 12.5 L55 15 L52.5 22.5 L58 28 L52.5 33.5 L55 41 L47.5 43.5 L45 51 L37.5 48.5 L32 54 L26.5 48.5 L19 51 L16.5 43.5 L9 41 L11.5 33.5 L6 28 L11.5 22.5 L9 15 L16.5 12.5 L19 5 L26.5 7.5 Z" fill="#5ea86a"/>
                        <path d="M21 32 L28 39 L43 24" stroke="#fff" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </svg>
                    <h2>Password Reset Successful</h2>
                    <p class="intro">Congratulations! Your password has been changed. Click continue to log in.</p>
                    <a href="{{ request('from') === 'admin' ? route('login.admin') : route('login.tenant') }}" class="btn-login">Log In</a>
                </div>

            </div>
        </div>
    </div>
    </div>

    <script>
        function goToStep(step) {
            document.querySelectorAll('.step-card').forEach(function (card) {
                card.classList.remove('active');
            });
            document.querySelector('.step-card[data-step="' + step + '"]').classList.add('active');
        }

        function postJson(url, payload) {
            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            }).then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, data: data };
                });
            });
        }

        // ===== Step 1: request code =====
        document.getElementById('emailForm').addEventListener('submit', function (e) {
            e.preventDefault();
            var button = document.getElementById('sendCodeBtn');
            var email = document.getElementById('forgot_email').value;
            var errorBox = document.getElementById('emailError');
            errorBox.classList.remove('visible');

            button.disabled = true;
            button.classList.add('loading');

            postJson("{{ route('password.code.send') }}", { email: email })
                .then(function (result) {
                    button.disabled = false;
                    button.classList.remove('loading');

                    if (!result.ok) {
                        errorBox.textContent = result.data.message || 'Something went wrong. Please try again.';
                        errorBox.classList.add('visible');
                        return;
                    }

                    document.getElementById('codeEmailDisplay').textContent = email;
                    document.getElementById('resetEmailField').value = email;
                    goToStep(2);
                })
                .catch(function () {
                    button.disabled = false;
                    button.classList.remove('loading');
                    errorBox.textContent = 'Something went wrong. Please try again.';
                    errorBox.classList.add('visible');
                });
        });

        // ===== Step 2: OTP box behavior =====
        var otpBoxes = document.querySelectorAll('.otp-box');
        otpBoxes.forEach(function (box, index) {
            box.addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length === 1 && otpBoxes[index + 1]) {
                    otpBoxes[index + 1].focus();
                }
            });
            box.addEventListener('keydown', function (e) {
                if (e.key === 'Backspace' && !this.value && otpBoxes[index - 1]) {
                    otpBoxes[index - 1].focus();
                }
            });
        });

        document.getElementById('verifyCodeBtn').addEventListener('click', function () {
            var button = this;
            var code = Array.from(otpBoxes).map(function (box) { return box.value; }).join('');
            var errorBox = document.getElementById('codeError');
            var email = document.getElementById('resetEmailField').value;

            if (code.length < 5) {
                errorBox.textContent = 'Please enter all 5 digits.';
                errorBox.classList.add('visible');
                return;
            }

            errorBox.classList.remove('visible');
            button.disabled = true;
            button.classList.add('loading');

            postJson("{{ route('password.code.verify') }}", { email: email, code: code })
                .then(function (result) {
                    button.disabled = false;
                    button.classList.remove('loading');

                    if (!result.ok) {
                        errorBox.textContent = result.data.message || 'That code is invalid or has expired.';
                        errorBox.classList.add('visible');
                        return;
                    }

                    document.getElementById('resetCodeField').value = code;
                    goToStep(3);
                })
                .catch(function () {
                    button.disabled = false;
                    button.classList.remove('loading');
                    errorBox.textContent = 'Something went wrong. Please try again.';
                    errorBox.classList.add('visible');
                });
        });

        document.getElementById('resendLink').addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('emailForm').requestSubmit();
        });

        // ===== Step 3: submit new password =====
        document.getElementById('passwordForm').addEventListener('submit', function (e) {
            e.preventDefault();
            var button = this.querySelector('.btn-login');
            var errorBox = document.getElementById('passwordError');
            if (button.disabled) return;

            var payload = {
                email: document.getElementById('resetEmailField').value,
                code: document.getElementById('resetCodeField').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('password_confirmation').value
            };

            errorBox.classList.remove('visible');
            button.disabled = true;
            button.classList.add('loading');

            postJson("{{ route('password.code.reset') }}", payload)
                .then(function (result) {
                    button.disabled = false;
                    button.classList.remove('loading');

                    if (!result.ok) {
                        var message = result.data.message || 'Something went wrong. Please try again.';
                        if (result.data.errors) {
                            message = Object.values(result.data.errors)[0][0];
                        }
                        errorBox.textContent = message;
                        errorBox.classList.add('visible');
                        return;
                    }

                    goToStep(4);
                })
                .catch(function () {
                    button.disabled = false;
                    button.classList.remove('loading');
                    errorBox.textContent = 'Something went wrong. Please try again.';
                    errorBox.classList.add('visible');
                });
        });

        // ===== Sticky nav shadow =====
        window.addEventListener('scroll', function () {
            var nav = document.querySelector('.topnav');
            if (window.scrollY > 10) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });
    </script>

</body>
</html>