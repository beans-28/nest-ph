<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inquiry | NEST.PH</title>
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
        .topnav .menu a.pill:hover { background: rgba(255,255,255,0.12); }
        .topnav .logo {
            display: flex; align-items: center; gap: 6px; color: #fff; font-weight: 700;
            font-size: 19px; letter-spacing: 0.02em; white-space: nowrap; text-decoration: none;
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

        /* ===== INQUIRY AREA — same skeleton as the login pages ===== */
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
            display: flex; flex-direction: column;
        }
        .back-button {
            background: none; border: none; color: #fff; font-size: 22px; cursor: pointer;
            line-height: 1; padding: 0; margin-bottom: 32px; align-self: flex-start;
        }
        .login-left-content { flex: 1; display: flex; flex-direction: column; justify-content: center; }
        .login-left h1 {
            color: #fff; font-weight: 700; font-size: clamp(24px, 3vw, 34px);
            line-height: 1.2; max-width: 360px;
        }
        .login-left h1 .accent {
            display: block; color: #44ad65; font-weight: 900;
            font-size: clamp(28px, 3.6vw, 40px); margin-top: 4px;
        }
        .brand-mark {
            margin-top: 36px; width: 150px; height: 150px;
            background: linear-gradient(180deg, #567357 0%, #a2d9a4 100%);
            border-radius: 0 80px 80px 0; display: flex; align-items: center; justify-content: center;
        }
        .brand-mark span { font-family: 'Agbalumo', cursive; font-size: 96px; color: #fff; line-height: 1; }

        .login-right-wrap { position: relative; padding-top: 16px; display: flex; flex-direction: column; }
        .login-right {
            background: #eeeded; border-radius: 32px;
            padding: clamp(36px, 5vw, 52px) clamp(24px, 5vw, 56px) clamp(36px, 5vw, 52px);
            flex: 1;
        }

        .login-right h2 { color: #567357; font-weight: 700; font-size: clamp(22px, 2.6vw, 28px); margin-bottom: 8px; }
        .login-right .intro { color: #7a7a7a; font-size: 13.5px; line-height: 1.6; margin-bottom: 26px; max-width: 560px; }

        .form-group { margin-bottom: 20px; max-width: 560px; }
        .form-group label {
            display: block; color: #567357; font-weight: 500; font-size: 13px; margin-bottom: 8px;
        }
        .form-group label .req { color: #d95117; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; border: none; border-bottom: 1px solid #a6b69f; padding: 8px 4px;
            font-size: 14px; background: transparent; color: #292420; outline: none;
            font-family: inherit;
        }
        .form-group textarea { resize: vertical; min-height: 90px; border: 1px solid #a6b69f; border-radius: 6px; padding: 10px; }
        .form-hint { font-size: 11.5px; color: #9aa5ac; margin-top: 6px; }

        .consent-row {
            display: flex; gap: 10px; align-items: flex-start; font-size: 12.5px;
            color: #4b5f4c; line-height: 1.6; margin: 22px 0 20px; max-width: 560px;
        }
        .consent-row input { margin-top: 3px; accent-color: #567357; width: 16px; height: 16px; flex-shrink: 0; }
        .consent-row a { color: #567357; font-weight: 700; }

        .btn-login {
            display: block; width: 100%; max-width: 560px; margin: 0 auto; padding: 17px 0;
            border: none; border-radius: 8px; background: #567357; color: #fff;
            font-weight: 700; font-size: 14px; letter-spacing: 0.05em; cursor: pointer;
            position: relative; transition: background 0.2s; text-align: center;
        }
        .btn-login:hover:not(:disabled) { background: #197335; }
        .btn-login:disabled { opacity: 0.5; cursor: not-allowed; }

        .spinner {
            display: none; width: 14px; height: 14px; border: 2px solid rgba(255,255,255,0.3);
            border-top: 2px solid #fff; border-radius: 50%; animation: spin 0.8s linear infinite; margin-right: 8px;
        }
        .btn-login.loading .spinner { display: inline-block; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .form-error {
            display: none; background: #fdf0f0; border: 1px solid #f3cccc; color: #b3261e;
            border-radius: 8px; padding: 12px 14px; font-size: 13px; margin-bottom: 18px; max-width: 560px;
        }
        .form-error.visible { display: block; }

        .room-context {
            display: flex; align-items: center; gap: 12px; background: #e2ede3;
            border: 1px solid #c5dbc7; border-radius: 10px; padding: 10px 14px;
            margin-bottom: 22px; max-width: 560px;
        }
        .room-context-thumb {
            width: 44px; height: 44px; border-radius: 8px; flex-shrink: 0;
            background: linear-gradient(135deg, #b9c7ba, #8a9a8b) center/cover;
        }
        .room-context-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #567357; }
        .room-context-name { font-size: 13.5px; font-weight: 700; color: #292420; }
        .room-context-clear {
            margin-left: auto; background: none; border: none; color: #567357; font-size: 11.5px;
            font-weight: 700; text-decoration: underline; cursor: pointer; font-family: inherit; flex-shrink: 0;
        }

        /* Success state */
        #successState { display: none; text-align: center; padding: 30px 0; }
        .success-badge { width: 68px; height: 68px; margin: 0 auto 20px; display: block; }
        #successState h2 { color: #567357; font-weight: 900; margin-bottom: 12px; }
        #successState p { color: #8a9690; font-size: 14px; line-height: 1.6; max-width: 460px; margin: 0 auto 24px; }
        #successState .btn-login { display: inline-block; width: auto; padding: 14px 36px; text-decoration: none; }

        @media (max-width: 1024px) {
            .login-grid { grid-template-columns: 1fr; padding: 20px 28px 32px; }
            .login-left { padding: 0 0 24px; }
            .login-right { padding: 40px 28px 32px; }
            .topnav { padding: 14px 28px; flex-wrap: wrap; }
        }
        @media (max-width: 640px) {
            .login-left h1 { font-size: 22px; }
            .login-left h1 .accent { font-size: 26px; }
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
            <button class="back-button" type="button" aria-label="Go back" onclick="window.location.href='{{ route('home') }}'">←</button>
            <div class="login-left-content">
                <h1>Study hard, make friends, and live your<span class="accent">NEST life.</span></h1>
                <div class="brand-mark"><span>N</span></div>
            </div>
        </div>

        <div class="login-right-wrap">
            <div class="login-right">

                <div id="formState">
                    <h2>Send an Inquiry</h2>
                    <p class="intro">Have a question about room availability, rates, or dormitory policies? Send us a message and we'll get back to you shortly.</p>

                    <div class="room-context" id="roomContext" style="display:none;">
                        <div class="room-context-thumb" id="roomContextThumb"></div>
                        <div>
                            <div class="room-context-label">Asking about</div>
                            <div class="room-context-name" id="roomContextName"></div>
                        </div>
                        <button type="button" class="room-context-clear" id="roomContextClear">Not this room</button>
                    </div>

                    <div class="form-error" id="formError"></div>

                    <form id="inquiryForm">
                        <div class="form-group">
                            <label for="full_name">Full Name <span class="req">*</span></label>
                            <input type="text" id="full_name" required>
                        </div>

                        <div class="form-group">
                            <label for="contact_number">Contact Number</label>
                            <input type="tel" id="contact_number" placeholder="09-">
                            <div class="form-hint">Provide at least a contact number or an email so we can reach you back.</div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email">
                        </div>

                        <div class="form-group" id="roomTypeGroup" style="display:none;">
                            <label for="preferred_room_type">Room Type You're Interested In</label>
                            <select id="preferred_room_type">
                                <option value="">No preference</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="message">Your Inquiry <span class="req">*</span></label>
                            <textarea id="message" placeholder="Ask about availability, rates, policies, or anything else..." required></textarea>
                        </div>

                        <label class="consent-row">
                            <input type="checkbox" id="dpa_consent" required>
                            <span>I consent to NEST.PH collecting and using the information above to respond to my inquiry, in accordance with the <a href="#">Data Privacy Notice</a> (RA 10173).</span>
                        </label>

                        <button type="submit" class="btn-login" id="submitBtn" disabled>
                            <span class="spinner"></span>
                            <span class="btn-text">Submit Inquiry</span>
                        </button>
                    </form>
                </div>

                <div id="successState">
                    <svg class="success-badge" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M32 2 L37.5 7.5 L45 5 L47.5 12.5 L55 15 L52.5 22.5 L58 28 L52.5 33.5 L55 41 L47.5 43.5 L45 51 L37.5 48.5 L32 54 L26.5 48.5 L19 51 L16.5 43.5 L9 41 L11.5 33.5 L6 28 L11.5 22.5 L9 15 L16.5 12.5 L19 5 L26.5 7.5 Z" fill="#5ea86a"/>
                        <path d="M21 32 L28 39 L43 24" stroke="#fff" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </svg>
                    <h2>Inquiry Sent</h2>
                    <p id="successMessage">Your inquiry has been submitted. We will get back to you shortly.</p>
                    <a href="{{ route('home') }}" class="btn-login">Back to Home</a>
                </div>

            </div>
        </div>
    </div>
    </div>

<script>
    // Pre-fill the room reference if the visitor arrived via a room's
    // "Inquiry" button (?room_id=..&room_type=..), per the Browse Rooms use
    // case's "Click Inquire/Apply to proceed" step.
    const params = new URLSearchParams(window.location.search);
    let presetRoomId = params.get('room_id');
    let presetRoomType = params.get('room_type') || null;

    if (presetRoomId) {
        fetch(`/public-api/rooms/${presetRoomId}`)
            .then(r => { if (!r.ok) throw new Error('not found'); return r.json(); })
            .then(room => {
                presetRoomType = room.room_type || presetRoomType;
                document.getElementById('roomContext').style.display = 'flex';
                document.getElementById('roomContextName').textContent =
                    `Room ${room.room_no}${room.room_type ? ' — ' + room.room_type.charAt(0).toUpperCase() + room.room_type.slice(1) : ''}`;
                if (room.photo_url) {
                    document.getElementById('roomContextThumb').style.backgroundImage = `url('${room.photo_url}')`;
                }
            })
            .catch(() => {
                // Room no longer exists or the id was invalid — fall back to
                // a general inquiry rather than referencing a room that isn't
                // really there.
                presetRoomId = null;
                presetRoomType = null;
            });
    }

    document.getElementById('roomContextClear').addEventListener('click', () => {
        presetRoomId = null;
        presetRoomType = null;
        document.getElementById('roomContext').style.display = 'none';
    });

    function updateSubmitState() {
        document.getElementById('submitBtn').disabled = !document.getElementById('dpa_consent').checked;
    }
    document.getElementById('dpa_consent').addEventListener('change', updateSubmitState);
    updateSubmitState();

    // Populate the optional room-type dropdown from real data instead of a
    // hardcoded list. Skipped when a specific room is already selected —
    // choosing a general type doesn't make sense once a room is picked.
    if (!presetRoomId) {
        fetch('/public-api/filter-options')
            .then(r => r.json())
            .then(data => {
                const types = data.room_types || [];
                if (types.length === 0) return;

                const select = document.getElementById('preferred_room_type');
                types.forEach(type => {
                    const opt = document.createElement('option');
                    opt.value = type;
                    opt.textContent = type.charAt(0).toUpperCase() + type.slice(1);
                    if (params.get('room_type') === type) opt.selected = true;
                    select.appendChild(opt);
                });
                document.getElementById('roomTypeGroup').style.display = 'flex';
                document.getElementById('roomTypeGroup').style.flexDirection = 'column';
            })
            .catch(() => { /* dropdown just stays hidden if this fails */ });
    }

    document.getElementById('inquiryForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const button = document.getElementById('submitBtn');
        if (button.disabled) return;

        const errorBox = document.getElementById('formError');
        errorBox.classList.remove('visible');
        button.disabled = true;
        button.classList.add('loading');

        const payload = {
            full_name: document.getElementById('full_name').value,
            contact_number: document.getElementById('contact_number').value,
            email: document.getElementById('email').value,
            message: document.getElementById('message').value,
            preferred_room_type: presetRoomType || document.getElementById('preferred_room_type').value || null,
            room_id: presetRoomId || null,
            dpa_consent: document.getElementById('dpa_consent').checked,
        };

        try {
            const response = await fetch('/api/inquiries', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (!response.ok) {
                const firstError = data.errors ? Object.values(data.errors)[0][0] : null;
                errorBox.textContent = firstError || data.message || 'Something went wrong. Please try again.';
                errorBox.classList.add('visible');
                button.disabled = false;
                button.classList.remove('loading');
                return;
            }

            document.getElementById('successMessage').textContent = data.message;
            document.getElementById('formState').style.display = 'none';
            document.getElementById('successState').style.display = 'block';
        } catch (err) {
            errorBox.textContent = 'Something went wrong. Please check your connection and try again.';
            errorBox.classList.add('visible');
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