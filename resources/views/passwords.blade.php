<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Help | NEST.PH</title>
    <style>
        :root {
            --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji',
                'Segoe UI Symbol', 'Noto Color Emoji';
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #06310c;
            background: linear-gradient(180deg, #293c2d 0%, #273826 100%);
            min-height: 100vh;
        }

        .topbar {
            width: 100%;
            padding: 18px 48px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(99, 133, 104, 0.96);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .topbar .left,
        .topbar .right {
            display: flex;
            align-items: center;
            gap: 28px;
        }

        .topbar a,
        .topbar button {
            color: #f4f7f1;
            text-decoration: none;
            font-size: 13px;
            letter-spacing: .02em;
        }

        .topbar .logo {
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
        }

        .topbar .label {
            padding: 8px 16px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(255,255,255,0.07);
            font-size: 12px;
        }

        .topbar button {
            border: 1px solid rgba(255,255,255,0.18);
            background: transparent;
            border-radius: 6px;
            padding: 10px 20px;
            cursor: pointer;
        }

        .topbar button.primary {
            background: #fff;
            color: #274d2f;
            border-color: transparent;
        }

        .page-wrapper {
            display: grid;
            grid-template-columns: 1fr 1.05fr;
            max-width: 1180px;
            margin: 36px auto 64px;
            min-height: calc(100vh - 120px);
            gap: 40px;
            padding: 0 48px;
        }

        .panel-left {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 30px;
            color: #f7f9f5;
            position: relative;
        }

        .back-button {
            position: absolute;
            top: 0;
            left: 0;
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: #f7f9f5;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        .back-button:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .panel-left h1 {
            font-size: clamp(2.2rem, 3vw, 3.3rem);
            line-height: 1.05;
            max-width: 500px;
        }

        .panel-left .brand-mark {
            width: 240px;
            height: 240px;
            border-radius: 45px;
            background: linear-gradient(180deg, #7ac07d 0%, #39593a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 28px 60px rgba(0, 0, 0, 0.22);
        }

        .panel-left .brand-mark span {
            font-size: 6rem;
            font-weight: 700;
            color: #f5f7f1;
            letter-spacing: -0.05em;
        }

        .panel-right {
            background: #eef0ed;
            border-radius: 36px;
            padding: 46px 42px 38px;
            display: flex;
            flex-direction: column;
            justify-content: start;
            box-shadow: 0 24px 80px rgba(13, 25, 14, 0.12);
        }

        .tab-header {
            display: inline-flex;
            border-radius: 999px;
            background: rgba(79, 99, 77, 0.16);
            border: 1px solid rgba(71, 91, 70, 0.18);
            padding: 6px;
            margin-bottom: 30px;
        }

        .tab-header button {
            border: none;
            background: transparent;
            padding: 16px 34px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #5c725d;
            cursor: pointer;
        }

        .tab-header button.active {
            background: #f7f8f4;
            box-shadow: inset 0 0 0 1px rgba(71, 91, 70, 0.14);
            color: #2f4f34;
        }

        .panel-right h2 {
            margin-bottom: 12px;
            font-size: 2rem;
            letter-spacing: -0.04em;
        }

        .panel-right .intro {
            margin-bottom: 24px;
            color: #556a56;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .form-card {
            display: none;
            border: 1px solid rgba(71, 91, 70, 0.14);
            border-radius: 20px;
            padding: 22px;
            background: rgba(255,255,255,0.7);
            margin-bottom: 18px;
        }

        .form-card.active {
            display: block;
        }

        .form-card h3 {
            font-size: 1.05rem;
            margin-bottom: 14px;
            color: #2f4f34;
        }

        .form-group {
            display: grid;
            gap: 10px;
            margin-bottom: 16px;
        }

        .form-group label {
            font-size: 12px;
            font-weight: 600;
            color: #556a56;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .form-group input {
            border: none;
            border-bottom: 1px solid #a6b69f;
            padding: 10px 6px 8px;
            font-size: 1rem;
            background: transparent;
            color: #1f3322;
            outline: none;
        }

        .btn-login {
            width: 100%;
            margin-top: 8px;
            padding: 14px 0;
            border: none;
            border-radius: 12px;
            background: #4f6f52;
            color: #f7f9f2;
            font-weight: 700;
            letter-spacing: .04em;
            cursor: pointer;
            position: relative;
            transition: all 0.2s ease;
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid #f7f9f2;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-right: 8px;
        }

        .btn-login.loading .spinner {
            display: inline-block;
        }

        .btn-login.loading .btn-text {
            opacity: 0.8;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .help-text {
            text-align: center;
            font-size: 13px;
            color: #4b5f4c;
            margin-top: 12px;
        }

        .help-text a {
            color: #3f6e45;
            font-weight: 700;
            text-decoration: none;
        }

        @media (max-width: 1024px) {
            .page-wrapper {
                grid-template-columns: 1fr;
                padding: 0 28px;
            }
            .topbar {
                flex-wrap: wrap;
                gap: 14px;
            }
            .topbar .left,
            .topbar .right {
                flex-wrap: wrap;
                gap: 16px;
            }
            .panel-left {
                order: 2;
                text-align: center;
            }
            .panel-left .brand-mark {
                margin: 0 auto;
            }
        }

        @media (max-width: 640px) {
            .topbar {
                padding: 14px 20px;
            }
            .page-wrapper {
                margin: 16px auto 32px;
                gap: 24px;
            }
            .panel-right {
                padding: 32px 24px;
            }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="left">
            <a href="#">VR TOUR</a>
            <a href="#">ROOMS</a>
            <a href="#">HOME</a>
            <span class="label">Dorm Info</span>
        </div>
        <div class="right">
            <span class="logo">NEST.PH</span>
            <button type="button">Admin</button>
            <button type="button">Apply</button>
            <button type="button" class="primary">Log In</button>
        </div>
    </header>

    <main class="page-wrapper">
        <section class="panel-left">
            <button class="back-button" type="button" aria-label="Go back">←</button>
            <h1>Reset access quickly and safely for your NEST account.</h1>
            <div class="brand-mark"><span>P</span></div>
        </section>

        <section class="panel-right">
            <div class="tab-header">
                <button type="button" class="active" data-tab="forgot">FORGOT PASSWORD</button>
                <button type="button" data-tab="update">UPDATE PASSWORD</button>
            </div>

            <div class="form-card active" id="forgot-tab">
                <h3>Forgot Password</h3>
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="form-group">
                        <label for="forgot_email">Email</label>
                        <input id="forgot_email" name="email" type="email" placeholder="you@example.com" required />
                    </div>
                    <button type="submit" class="btn-login">
                        <span class="spinner"></span>
                        <span class="btn-text">Send Reset Link</span>
                    </button>
                </form>
                <p class="help-text">We'll send you a link to reset your password via email.</p>
            </div>

            <div class="form-card" id="update-tab">
                <h3>Update Password</h3>
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input id="current_password" name="current_password" type="password" placeholder="Enter current password" required />
                    </div>
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input id="password" name="password" type="password" placeholder="Enter new password" required />
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Confirm new password" required />
                    </div>
                    <button type="submit" class="btn-login">
                        <span class="spinner"></span>
                        <span class="btn-text">Update Password</span>
                    </button>
                </form>
                <p class="help-text">Must be logged in to update your password.</p>
            </div>

            <p class="help-text">
                <a href="{{ request('from') === 'admin' ? route('login.admin') : route('login.tenant') }}">Back to Login</a>
            </p>
        </section>
    </main>

    <script>
        document.querySelectorAll('.tab-header button').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const tab = this.getAttribute('data-tab');
                
                // Remove active class from all buttons and cards
                document.querySelectorAll('.tab-header button').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.form-card').forEach(card => card.classList.remove('active'));
                
                // Add active class to clicked button and corresponding card
                this.classList.add('active');
                document.getElementById(tab + '-tab').classList.add('active');
            });
        });

        // Handle form submission with loading state
        document.querySelectorAll('.form-card form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const button = this.querySelector('.btn-login');
                if (button.disabled) {
                    e.preventDefault();
                    return;
                }
                
                button.disabled = true;
                button.classList.add('loading');
            });
        });
    </script>
</body>
</html>
