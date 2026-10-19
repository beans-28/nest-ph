<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NEST.PH Platform Login</title>
    <link rel="icon" href="{{ asset('images/nestphgreen.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Roboto', system-ui, sans-serif; min-height: 100vh; display: grid; place-items: center; padding: 16px;
               background: linear-gradient(135deg, #197335, #567357); color: #292420; }
        .card { background: #fff; width: min(400px, 100%); border-radius: 14px; padding: 32px; box-shadow: 0 10px 30px rgba(0,0,0,.2); }
        .brand { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 20px; color: #197335; margin-bottom: 4px; }
        .brand img { height: 32px; }
        .sub { color: #5b6168; font-size: 14px; margin-bottom: 24px; }
        label { display: block; font-weight: 500; font-size: 14px; margin: 14px 0 6px; }
        input { width: 100%; font: inherit; padding: 10px 12px; border: 1px solid #c1c7cd; border-radius: 8px; }
        input:focus-visible, button:focus-visible, a:focus-visible { outline: 2px solid #197335; outline-offset: 2px; }
        button { width: 100%; margin-top: 18px; padding: 12px; border: 0; border-radius: 8px; background: #197335; color: #fff; font: inherit; font-weight: 700; cursor: pointer; }
        button[disabled] { opacity: .6; cursor: wait; }
        .msg { margin-top: 14px; font-size: 14px; color: #b42318; min-height: 1em; }
        .links { margin-top: 18px; font-size: 13px; color: #5b6168; display: flex; justify-content: space-between; gap: 8px; flex-wrap: wrap; }
        .links a { color: #197335; }
    </style>
</head>
<body>
    <main class="card">
        <div class="brand"><img src="{{ asset('images/nestphgreen.png') }}" alt=""> NEST.PH</div>
        <p class="sub">Platform team login. Dormitory staff should use their dormitory's admin login.</p>
        <form id="loginForm" novalidate>
            <label for="email">Email</label>
            <input id="email" type="email" autocomplete="username" required>
            <label for="password">Password</label>
            <input id="password" type="password" autocomplete="current-password" required>
            @include('partials.turnstile', ['id' => 'platformTurnstile'])
            <button type="submit" id="loginBtn">Log in</button>
            <p class="msg" id="msg" role="alert"></p>
        </form>
        <div class="links">
            <a href="{{ route('platform') }}">&larr; Back to NEST.PH</a>
            <a href="{{ route('passwords', ['from' => 'admin']) }}">Forgot password?</a>
        </div>
    </main>
    <script>
        const form = document.getElementById('loginForm');
        const msg = document.getElementById('msg');
        const btn = document.getElementById('loginBtn');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            msg.textContent = '';
            btn.disabled = true;
            try {
                const response = await fetch('/platform/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        email: document.getElementById('email').value,
                        password: document.getElementById('password').value,
                        cf_turnstile_response: turnstileToken('platformTurnstile'),
                    }),
                });
                turnstileReset('platformTurnstile');
                const data = await response.json().catch(() => ({}));

                if (response.ok) {
                    window.location.href = '{{ route('platform.dashboard') }}';
                    return;
                }
                if (data.locked) {
                    msg.textContent = `Too many attempts. Try again in ${Math.ceil(data.retry_after / 60)} minutes.`;
                } else if (data.attempts) {
                    msg.textContent = `Incorrect email or password. ${data.attempts} of ${data.max_attempts} attempts used.`;
                } else {
                    msg.textContent = data.message || 'Login failed. Please try again.';
                }
            } catch (err) {
                msg.textContent = 'Could not reach the server. Please check your connection.';
            } finally {
                btn.disabled = false;
            }
        });
    </script>
</body>
</html>
