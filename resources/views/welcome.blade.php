<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEST.PH — Study hard, make friends, and live your NEST life.</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-light: #a2d9a4;
            --green-dark: #567357;
            --green-darker: #197335;
            --ink: #292420;
            --ink-alt: #21272a;
            --cream: #dcd8d7;
            --cream-light: #f2f4f8;
            --gray-border: #c1c7cd;
        }
        html { overflow-x: hidden; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Roboto', system-ui, -apple-system, sans-serif;
            color: var(--ink);
            background: #fff;
            overflow-x: hidden;
        }
        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; display: block; }

        /* Decorative leaf textures live INSIDE the green sections, blended
           onto their background — not floating between sections. */
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
            background: linear-gradient(90deg, var(--green-dark), var(--green-light));
            padding: 20px 80px;
            display: flex;
            align-items: center;
            gap: 48px;
        }
        .topnav .menu { flex: 1; display: flex; align-items: center; gap: 16px; }
        .topnav .menu a, .topnav .menu span {
            color: #fff; font-weight: 500; font-size: 16px;
            padding: 12px 8px; display: inline-flex; align-items: center; gap: 4px;
        }
        .topnav .menu a.pill {
            border: 1px solid rgba(255,255,255,0.5);
            border-radius: 999px;
            padding: 8px 18px;
        }
        .topnav .menu a.pill:hover {
            background: rgba(255,255,255,0.12);
        }
        .topnav .logo {
            display: flex; align-items: center; gap: 6px;
            color: #fff; font-weight: 700; font-size: 24px;
            letter-spacing: 0.02em; white-space: nowrap;
        }
        .topnav .logo .mark { width: 22px; height: 22px; border: 2px solid #fff; border-radius: 4px; flex-shrink: 0; }
        .topnav .buttons { flex: 1; display: flex; justify-content: flex-end; gap: 16px; }

        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            height: 48px; padding: 0 24px; border: 2px solid #fff;
            font-weight: 500; font-size: 16px; letter-spacing: 0.02em; cursor: pointer; white-space: nowrap;
        }
        .btn-white { background: #fff; color: var(--ink); }
        .btn-outline-white { background: transparent; color: #fff; }
        .btn-green { background: var(--green-dark); border-color: var(--green-dark); color: #fff; }
        .btn-outline-green { background: transparent; border-color: var(--green-dark); color: var(--green-dark); }
        .btn-lg { height: 56px; padding: 0 28px; font-size: 20px; }

        .hero {
            background: linear-gradient(90deg, #dcd8d7, #f2f2f2);
            padding: 64px 80px;
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 40px;
            align-items: center;
            box-shadow: 0 4px 2px rgba(0,0,0,0.15);
        }
        .hero-text h1 { font-size: 48px; font-weight: 700; line-height: 1.1; margin-bottom: 20px; }
        .hero-text p { font-size: 18px; line-height: 1.5; margin-bottom: 32px; max-width: 640px; }
        .hero-buttons { display: flex; gap: 16px; flex-wrap: wrap; }
        .hero-image {
            width: 100%; aspect-ratio: 4 / 3; border-radius: 4px; overflow: hidden;
            box-shadow: inset 0 4px 4px rgba(0,0,0,0.25);
            background: linear-gradient(135deg, #e7e5e5, #818080);
        }
        .hero-image img { width: 100%; height: 100%; object-fit: cover; }

        .stats-bar {
            background: linear-gradient(90deg, var(--green-dark), var(--green-light));
            padding: 48px 80px; text-align: center;
        }
        .stats-bar h2 { font-size: 36px; font-weight: 700; margin-bottom: 8px; }
        .stats-bar .sub { font-size: 18px; margin-bottom: 32px; }
        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; max-width: 1100px; margin: 0 auto; }
        .stat-card { display: flex; flex-direction: column; align-items: center; gap: 12px; }
        .stat-icon { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; color: var(--ink-alt); }
        .stat-icon svg { width: 100%; height: 100%; }
        .stat-value { font-size: 22px; font-weight: 700; color: var(--ink-alt); }
        .stat-label { font-size: 15px; color: var(--ink-alt); }

        .why-section {
            background: linear-gradient(90deg, #dcd8d7, #f2f2f2);
            padding: 56px 80px; text-align: center;
            box-shadow: 0 4px 2px rgba(0,0,0,0.15);
        }
        .eyebrow {
            font-size: 18px; font-weight: 700; letter-spacing: 0.06em;
            text-transform: uppercase; color: var(--green-darker); margin-bottom: 8px;
        }
        .why-section h2 { font-size: 38px; font-weight: 700; margin-bottom: 16px; }
        .why-section p { font-size: 18px; max-width: 980px; margin: 0 auto; line-height: 1.5; }

        .about-section { background: var(--green-dark); padding: 56px 80px; }
        .about-header { text-align: center; max-width: 900px; margin: 0 auto 48px; }
        .about-header .eyebrow { color: rgba(255,255,255,0.85); }
        .about-header h2 { color: #fff; font-size: 36px; font-weight: 700; }

        .feature-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 48px 64px; max-width: 1100px; margin: 0 auto; }
        .feature { display: flex; flex-direction: column; align-items: center; gap: 16px; text-align: center; color: #fff; }
        .feature-icon { width: 72px; height: 72px; color: #fff; }
        .feature-icon svg { width: 100%; height: 100%; }
        .feature p { font-size: 17px; line-height: 1.5; }
        .feature strong { font-weight: 700; }

        .about-actions { display: flex; justify-content: center; gap: 16px; margin-top: 40px; flex-wrap: wrap; }
        .about-footnote {
            text-align: center; margin-top: 20px; font-size: 11px;
            letter-spacing: 0.05em; color: #292420; line-height: 1.8;
        }

        footer {
            background: linear-gradient(2deg, #59473f 20%, rgba(35,27,23,0.71) 85%);
            color: var(--cream);
            padding: 48px 80px 24px;
        }
        .footer-top {
            display: flex; align-items: center; gap: 48px; padding-bottom: 24px;
            border-bottom: 1px solid var(--gray-border); margin-bottom: 32px;
        }
        .footer-logo { display: flex; align-items: center; gap: 6px; font-weight: 700; font-size: 24px; }
        .footer-logo .mark { width: 20px; height: 20px; border: 2px solid var(--cream); border-radius: 4px; }
        .newsletter { flex: 1; display: flex; justify-content: flex-end; gap: 16px; }
        .newsletter input {
            background: var(--cream-light); border: none; border-bottom: 1px solid var(--gray-border);
            padding: 14px 16px; font-size: 16px; color: #555; min-width: 280px;
        }
        .newsletter button {
            background: var(--green-dark); border: 2px solid var(--green-dark); color: #fff;
            font-weight: 500; padding: 0 24px; cursor: pointer;
        }
        .footer-columns { display: grid; grid-template-columns: 1fr 1fr 1fr 1.4fr; gap: 48px; margin-bottom: 32px; }
        .footer-columns h4 { font-size: 18px; font-weight: 700; margin-bottom: 16px; }
        .footer-columns p, .footer-columns a { display: block; font-size: 16px; margin-bottom: 12px; color: var(--cream); }
        .social-icons { display: flex; gap: 16px; margin-top: 8px; }
        .social-icons svg { width: 22px; height: 22px; }
        .footer-badge {
            background: #fff; border-radius: 4px; padding: 12px; display: inline-flex;
            align-items: center; gap: 8px; color: #292420; font-size: 11px; max-width: 260px;
        }
        .footer-bottom {
            display: flex; justify-content: space-between; align-items: center;
            padding-top: 16px; border-top: 1px solid var(--gray-border); font-size: 14px;
            flex-wrap: wrap; gap: 12px;
        }
        .footer-bottom .links { display: flex; gap: 16px; }

        @media (max-width: 1024px) {
            .topnav, .hero, .stats-bar, .why-section, .about-section, footer { padding-left: 32px; padding-right: 32px; }
            .hero { grid-template-columns: 1fr; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
            .feature-grid { grid-template-columns: 1fr; }
            .footer-columns { grid-template-columns: 1fr 1fr; }
            .topnav { flex-wrap: wrap; }
        }
        @media (max-width: 640px) {
            .topnav, .hero, .stats-bar, .why-section, .about-section, footer { padding-left: 20px; padding-right: 20px; }
            .hero-text h1 { font-size: 34px; }
            .stats-row { grid-template-columns: 1fr 1fr; }
            .footer-top { flex-direction: column; align-items: flex-start; }
            .newsletter { justify-content: flex-start; flex-wrap: wrap; }
            .newsletter input { min-width: 0; flex: 1; }
            .footer-columns { grid-template-columns: 1fr; }
            .footer-bottom { flex-direction: column; align-items: flex-start; }
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

    <section class="hero">
        <div class="hero-text">
            <h1>Study hard, make friends, and live your NEST life.</h1>
            <p>Every great mind needs a secure, comfortable place to hatch their biggest ideas. Welcome to a student living experience that prioritizes your well-being, safety, and academic focus. Whether you are retreating to your room after a long day of classes, collaborating with peers in our shared lounges, or simply taking a moment to breathe, you will find that everything you need is right here. Build your foundation, connect with your fellow residents, and make this space your ultimate sanctuary.</p>
            <div class="hero-buttons">
                <a href="{{ route('public.rooms') }}" class="btn btn-green btn-lg">Browse Rooms</a>
                <a href="{{ route('public.vr') }}" class="btn btn-outline-green btn-lg">VR Tour</a>
            </div>
        </div>
        <div class="hero-image">
            {{-- Placeholder image slot — replace with the dorm's real photo asset --}}
        </div>
    </section>

    <section class="stats-bar textured">
        <img src="{{ asset('images/leaf-texture-1.png') }}" class="bg-texture" alt="">
        <h2>Find your room at Pureza Station</h2>
        <p class="sub">Browse available beds, take a 360&deg; virtual tour, and reserve online</p>
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><path d="M9 9h.01M15 9h.01"/></svg></div>
                <div class="stat-value">250+</div>
                <div class="stat-label">Happy Tenants</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2l3 7h7l-5.5 4.5L18.5 21 12 16.5 5.5 21 7.5 13.5 2 9h7z"/></svg></div>
                <div class="stat-value">5</div>
                <div class="stat-label">Star Ratings</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3H8a2 2 0 00-2 2v2h12V5a2 2 0 00-2-2z"/></svg></div>
                <div class="stat-value">AC, WIFI, CR</div>
                <div class="stat-label">Available Resources</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 20v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 20v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg></div>
                <div class="stat-value">{{ $availableBeds }}</div>
                <div class="stat-label">Beds Available</div>
            </div>
        </div>
    </section>

    <section class="why-section">
        <div class="eyebrow">You Are in Good Company</div>
        <h2>Why Pick Us?</h2>
        <p>Located right at the doorstep of Manila's academic hubs, Pureza Station Dormitory is dedicated to providing students with a secure, comfortable, and highly accessible living environment. Through our dedicated management platform, NEST.PH, we ensure that your stay is seamless&mdash;from your first virtual room viewing to your daily tenant needs. Every great mind needs a secure place to hatch their biggest ideas, and we are here to provide the foundation.</p>
    </section>

    <section class="about-section textured">
        <img src="{{ asset('images/leaf-texture-1.png') }}" class="bg-texture" alt="">
        <div class="about-header">
            <div class="eyebrow">We don't just offer a room; we offer a smart living experience powered by our custom Dormitory Management System.</div>
            <h2>About Us: Your Home in the Heart of Santa Mesa</h2>
        </div>

        <div class="feature-grid">
            <div class="feature">
                <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div>
                <p><strong>Real-Time Announcements:</strong> Stay updated with instant notifications about dorm events, maintenance schedules, or guest policies directly on your dashboard.</p>
            </div>
            <div class="feature">
                <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 7v10l9 4 9-4V7"/></svg></div>
                <p><strong>Seamless Tenant Portal:</strong> Manage your stay entirely online. Check your billing statements, upload proof of payment, and track your payment history without lining up at the admin office.</p>
            </div>
            <div class="feature">
                <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></div>
                <p><strong>Digital Maintenance Requests:</strong> Got a leaking faucet or a busted lightbulb? Submit a maintenance ticket directly through the NEST.PH portal and track its resolution status in real-time.</p>
            </div>
            <div class="feature">
                <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 11l7-7 7 7M5 10v9a1 1 0 001 1h3v-6h4v6h3a1 1 0 001-1v-9"/></svg></div>
                <p><strong>VR Room Viewing Module:</strong> Don't have time to visit in person? Put on a headset or use your screen to walk through our rooms and facilities in full 360-degree Virtual Reality. Get a realistic feel for room dimensions and setups before making a decision.</p>
            </div>
        </div>

        <div class="about-actions">
            <a href="{{ route('public.dorminfo') }}" class="btn btn-green btn-lg">More Information</a>
            <a href="#" class="btn btn-green btn-lg">Rate Us</a>
        </div>
        <div class="about-footnote">
            RENTAL RATES<br>
            PAYMENT SCHEDULES<br>
            RULES AND REGULATIONS
        </div>
    </section>

    <footer>
        <div class="footer-top">
            <div class="footer-logo"><span class="mark"></span> NEST.PH</div>
            <div class="newsletter">
                <input type="email" placeholder="Enter your email to inquire.....">
                <button type="button">Send</button>
            </div>
        </div>

        <div class="footer-columns">
            <div>
                <h4>CONTACTS</h4>
                <p>{{ $contactNumber ?? '(02) 8123-4567 / +63 917 123 4567' }}</p>
                <p>{{ $contactEmail ?? 'admin@nestph-pureza.com' }}</p>
                <p>{{ $address ?? 'Pureza Street, Santa Mesa, Manila, 1016 Metro Manila, Philippines' }}</p>
            </div>
            <div>
                <h4>STUFF</h4>
                <a href="{{ route('login.tenant') }}">[ Tenant Portal Login ]</a>
                <a href="#">[ Terms &amp; Conditions ]</a>
                <a href="#">[ Privacy Policy ]</a>
            </div>
            <div>
                <h4>SOCIALS</h4>
                <p style="margin-bottom:16px;">Join Us</p>
                <div class="social-icons">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M23.5 6.2a3 3 0 00-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6A3 3 0 00.5 6.2 31 31 0 000 12a31 31 0 00.5 5.8 3 3 0 002.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 002.1-2.1A31 31 0 0024 12a31 31 0 00-.5-5.8zM9.6 15.5V8.5l6.3 3.5-6.3 3.5z"/></svg>
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 10-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.4h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.4v7A10 10 0 0022 12z"/></svg>
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M23 4.9a9 9 0 01-2.6.7 4.5 4.5 0 002-2.5 9 9 0 01-2.9 1.1 4.5 4.5 0 00-7.7 4.1A12.8 12.8 0 012 3.9a4.5 4.5 0 001.4 6 4.5 4.5 0 01-2-.6v.1a4.5 4.5 0 003.6 4.4 4.5 4.5 0 01-2 .1 4.5 4.5 0 004.2 3.1A9 9 0 011 19a12.8 12.8 0 006.9 2c8.3 0 12.8-6.9 12.8-12.8v-.6A9.2 9.2 0 0023 4.9z"/></svg>
                    <svg viewBox="0 0 24 24" fill="currentColor"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4" fill="#59473f"/><circle cx="17.5" cy="6.5" r="1.2" fill="#59473f"/></svg>
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M4.98 3.5a2.5 2.5 0 11-.02 5 2.5 2.5 0 01.02-5zM3 9h4v12H3zM9 9h3.8v1.7h.1c.5-1 1.8-2 3.7-2 4 0 4.7 2.6 4.7 6V21h-4v-5.3c0-1.3 0-3-1.8-3s-2.1 1.4-2.1 2.9V21H9z"/></svg>
                </div>
            </div>
            <div>
                <div class="footer-badge">
                    Registered with the Bureau of Internal Revenue &middot; BIR 2026
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <span>Powered by {{ $dormName ?? 'NEST.PH' }} Dormitory Management System &copy; 2026. All rights reserved.</span>
            <div class="links">
                <a href="#">Eleven</a>
                <a href="#">Twelve</a>
                <a href="#">Thirteen</a>
            </div>
        </div>
    </footer>

</body>
</html>