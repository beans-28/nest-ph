<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEST.PH - Study hard, make friends, and live your NEST life.</title>
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
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Roboto', system-ui, -apple-system, sans-serif;
            color: var(--ink);
            background: #fff;
        }
        .page-wrap { overflow-x: hidden; }
        .site-shell { width: 100%; }
        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; display: block; }

        a:focus-visible, button:focus-visible, input:focus-visible {
            outline: 2px solid var(--green-darker);
            outline-offset: 2px;
        }
        .topnav a:focus-visible, .topnav .buttons a:focus-visible, footer a:focus-visible {
            outline: 2px solid #fff;
            outline-offset: 2px;
        }
        .btn-white:focus-visible, .btn-green:focus-visible {
            outline-color: var(--ink);
        }
        .visually-hidden {
            position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
            overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0;
        }

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
            background: linear-gradient(90deg, var(--green-darker), var(--green-dark));
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
        }
        .topnav .menu a.pill {
            border: 1px solid rgba(255,255,255,0.5);
            border-radius: 999px;
            padding: 7px 16px;
        }
        .topnav .menu a.pill:hover {
            background: rgba(255,255,255,0.12);
        }
        .topnav .logo {
            display: flex; align-items: center; gap: 6px;
            color: #fff; font-weight: 700; font-size: 19px;
            letter-spacing: 0.02em; white-space: nowrap;
        }
        .topnav .logo .logo-img { height: 32px; width: auto; }
        .topnav .buttons { flex: 1; display: flex; justify-content: flex-end; gap: 12px; }

        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            height: 44px; padding: 0 18px; border: 2px solid #fff;
            font-weight: 500; font-size: 13.5px; letter-spacing: 0.02em; cursor: pointer; white-space: nowrap;
        }
        .btn-white { background: #fff; color: var(--ink); }
        .btn-outline-white { background: transparent; color: #fff; }
        .btn-green { background: var(--green-dark); border-color: var(--green-dark); color: #fff; }
        .btn-outline-green { background: transparent; border-color: var(--green-dark); color: var(--green-dark); }
        .btn-lg { height: 46px; padding: 0 22px; font-size: 15px; }

        .hero {
            background: linear-gradient(90deg, var(--cream), var(--cream-light));
            padding: clamp(32px, 6vw, 56px) clamp(20px, 6vw, 64px);
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 32px;
            align-items: center;
            box-shadow: 0 4px 2px rgba(0,0,0,0.15);
        }
        .hero-text h1 { font-size: clamp(24px, 3.2vw, 36px); font-weight: 700; line-height: 1.2; margin-bottom: 16px; }
        .hero-text p { font-size: 15px; line-height: 1.6; margin-bottom: 24px; max-width: 540px; }
        .hero-buttons { display: flex; gap: 14px; flex-wrap: wrap; }
        .hero-image {
            width: 100%; max-width: 460px; margin-left: auto; aspect-ratio: 4 / 3; border-radius: 4px; overflow: hidden;
            box-shadow: inset 0 4px 4px rgba(0,0,0,0.25);
            background: linear-gradient(135deg, #e7e5e5, #818080);
        }
        .hero-image img { width: 100%; height: 100%; object-fit: cover; }

        .stats-bar {
            background: linear-gradient(90deg, var(--green-dark), var(--green-light));
            padding: clamp(28px, 5vw, 44px) clamp(20px, 6vw, 64px); text-align: center;
        }
        .stats-bar h2 { font-size: clamp(20px, 2.4vw, 26px); font-weight: 700; margin-bottom: 8px; }
        .stats-bar .sub { font-size: 14px; margin-bottom: 24px; }
        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; max-width: 900px; margin: 0 auto; }
        .stat-card { display: flex; flex-direction: column; align-items: center; gap: 10px; }
        .stat-icon { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; color: var(--ink-alt); }
        .stat-icon svg { width: 100%; height: 100%; }
                .stat-value { font-size: 17px; font-weight: 700; color: var(--ink-alt); min-height: 42px; display: flex; align-items: center; justify-content: center; line-height: 1.2; text-align: center; }
        .stat-label { font-size: 12.5px; color: var(--ink-alt); }

        .why-section {
            background: linear-gradient(90deg, var(--cream), var(--cream-light));
            padding: clamp(28px, 5vw, 44px) clamp(20px, 6vw, 64px); text-align: center;
            box-shadow: 0 4px 2px rgba(0,0,0,0.15);
        }
        .eyebrow {
            font-size: 12.5px; font-weight: 700; letter-spacing: 0.06em;
            text-transform: uppercase; color: var(--green-darker); margin-bottom: 8px;
        }
        .why-section h2 { font-size: clamp(20px, 2.6vw, 26px); font-weight: 700; margin-bottom: 14px; }
        .why-section p { font-size: 14.5px; max-width: 740px; margin: 0 auto; line-height: 1.6; }

        .about-section { background: var(--green-dark); padding: clamp(28px, 5vw, 44px) clamp(20px, 6vw, 64px); }
        .about-header { text-align: center; max-width: 720px; margin: 0 auto 32px; }
        .about-header .eyebrow { color: rgba(255,255,255,0.85); }
        .about-header h2 { color: #fff; font-size: clamp(19px, 2.4vw, 24px); font-weight: 700; }

        .feature-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 28px 44px; max-width: 860px; margin: 0 auto; }
        .feature { display: flex; flex-direction: column; align-items: center; gap: 12px; text-align: center; color: #fff; }
        .feature-icon { width: 48px; height: 48px; color: #fff; }
        .feature-icon svg { width: 100%; height: 100%; }
        .feature p { font-size: 13.5px; line-height: 1.55; }
        .feature strong { font-weight: 700; }

        .about-actions { display: flex; justify-content: center; gap: 14px; margin-top: 28px; flex-wrap: wrap; }
        .about-footnote {
            text-align: center; margin-top: 16px; font-size: 10px;
            letter-spacing: 0.05em; color: #292420; line-height: 1.8;
        }

        footer {
            background: linear-gradient(2deg, #59473f 20%, rgba(35,27,23,0.71) 85%);
            color: var(--cream);
            padding: clamp(28px, 5vw, 40px) clamp(20px, 6vw, 64px) 20px;
        }
        .footer-top {
            display: flex; align-items: center; gap: 32px; padding-bottom: 20px;
            border-bottom: 1px solid var(--gray-border); margin-bottom: 24px;
        }
        .footer-logo { display: flex; align-items: center; gap: 6px; font-weight: 700; font-size: 19px; }
        .footer-logo .logo-img { height: 28px; width: auto; }
        .newsletter { flex: 1; display: flex; justify-content: flex-end; gap: 12px; }
        .newsletter input {
            background: var(--cream-light); border: none; border-bottom: 1px solid var(--gray-border);
            padding: 11px 14px; font-size: 13px; color: #555; min-width: 240px;
        }
        .newsletter button {
            background: var(--green-dark); border: 2px solid var(--green-dark); color: #fff;
            font-weight: 500; padding: 0 20px; cursor: pointer; font-size: 13px;
        }
        .footer-columns { display: grid; grid-template-columns: 1fr 1fr 1fr 1.4fr; gap: 32px; margin-bottom: 24px; }
        .footer-columns h4 { font-size: 13.5px; font-weight: 700; margin-bottom: 12px; }
        .footer-columns p, .footer-columns a { display: block; font-size: 13px; margin-bottom: 10px; color: var(--cream); }
        .social-icons { display: flex; gap: 14px; margin-top: 6px; }
        .social-icons svg { width: 18px; height: 18px; }
        .footer-badge {
            background: #fff; border-radius: 4px; padding: 10px; display: inline-flex;
            align-items: center; gap: 8px; color: #292420; font-size: 10px; max-width: 230px;
        }
        .footer-bottom {
            display: flex; justify-content: space-between; align-items: center;
            padding-top: 14px; border-top: 1px solid var(--gray-border); font-size: 12px;
            flex-wrap: wrap; gap: 10px;
        }
        .footer-bottom .links { display: flex; gap: 14px; }

        @media (max-width: 1024px) {
            .topnav, .hero, .stats-bar, .why-section, .about-section, footer { padding-left: 28px; padding-right: 28px; }
            .hero { grid-template-columns: 1fr; }
            .hero-image { max-width: 100%; margin-left: 0; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
            .feature-grid { grid-template-columns: 1fr; }
            .footer-columns { grid-template-columns: 1fr 1fr; }
            .topnav { flex-wrap: wrap; }
        }
        @media (max-width: 640px) {
            .topnav, .hero, .stats-bar, .why-section, .about-section, footer { padding-left: 18px; padding-right: 18px; }
            .hero-text h1 { font-size: 24px; }
            .stats-row { grid-template-columns: 1fr 1fr; }
            .footer-top { flex-direction: column; align-items: flex-start; }
            .newsletter { justify-content: flex-start; flex-wrap: wrap; }
            .newsletter input { min-width: 0; flex: 1; }
            .footer-columns { grid-template-columns: 1fr; }
            .footer-bottom { flex-direction: column; align-items: flex-start; }

            /* Topnav: the flex-wrap layout at wider breakpoints packs the
               menu, logo and button groups unpredictably at phone widths,
               so stack them into clear rows instead. */
            .topnav { flex-direction: column; align-items: stretch; gap: 12px; padding-top: 14px; padding-bottom: 14px; }
            .topnav .logo { order: -1; justify-content: center; }
            .topnav .menu { flex: none; justify-content: center; flex-wrap: wrap; row-gap: 8px; }
            .topnav .menu a, .topnav .menu span { padding: 10px 8px; }
            .topnav .buttons { flex: none; justify-content: center; flex-wrap: wrap; row-gap: 10px; }

            .newsletter input { padding-top: 13px; padding-bottom: 13px; font-size: 16px; }
            .newsletter button { min-height: 44px; }
        }
        .footer-badge img{ width:20px; height:20px; border-radius:4px; object-fit:cover; flex-shrink:0; }
        .about-contact-note{ text-align: center; margin-top: 32px; font-size: 13px; color: rgba(255,255,255,0.85); }
        .about-contact-note a{ color: #fff; font-weight: 600; text-decoration: underline; }
    </style>
</head>
<body>

    <div class="site-shell">
    @include('partials.public-nav')

    <div class="page-wrap">
    <section class="hero">
        <div class="hero-text">
                        <h1>Welcome to {{ $dormName ?? 'NEST.PH' }}!</h1>
            <p>{{ $description ?? 'Every great mind needs a secure, comfortable place to hatch their biggest ideas. Welcome to a student living experience that prioritizes your well-being, safety, and academic focus.' }}</p>
            <div class="hero-buttons">
                <a href="{{ route('public.rooms') }}" class="btn btn-green btn-lg">Browse Rooms</a>
                <a href="{{ route('public.vr') }}" class="btn btn-outline-green btn-lg">VR Tour</a>
            </div>
        </div>
        <div class="hero-image">
            @if($coverPhotoUrl)
                <img src="{{ $coverPhotoUrl }}" alt="{{ $dormName }}">
            @endif
        </div>
    </section>

    <section class="stats-bar textured">
        <img src="{{ asset('images/leaf-texture-1.png') }}" class="bg-texture" alt="" loading="lazy" decoding="async">
        <h2>Find your room at {{ $dormName ?? 'NEST.PH' }}</h2>
        <p class="sub">Browse available beds, take a 360&deg; virtual tour, and apply online!</p>
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><path d="M9 9h.01M15 9h.01"/></svg></div>
                <div class="stat-value">{{ $happyTenantsCount }}+</div>
                <div class="stat-label">Happy Tenants</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><svg viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg></div>
                <div class="stat-value">{{ $reviewCount > 0 ? number_format($averageRating, 1) : 'New' }}</div>
                <div class="stat-label">{{ $reviewCount > 0 ? $reviewCount . ' ' . \Illuminate\Support\Str::plural('Review', $reviewCount) : 'No Reviews Yet' }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 20h.01"/><path d="M2 8.82a15 15 0 0 1 20 0"/><path d="M5 12.859a10 10 0 0 1 14 0"/><path d="M8.5 16.429a5 5 0 0 1 7 0"/></svg></div>
                <div class="stat-value">{{ $availableResources ?: 'AC, WIFI, CR' }}</div>
                <div class="stat-label">Available Resources</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/></svg></div>
                <div class="stat-value">{{ $availableBeds }}</div>
                <div class="stat-label">Beds Available</div>
            </div>
        </div>
    </section>

    <section class="why-section">
        <div class="eyebrow">You Are in Good Company</div>
        <h2>Why Pick Us?</h2>
        <p>Located right at the doorstep of Manila's academic hubs, Pureza Station Dormitory gives students a secure, comfortable, and accessible place to stay. Our management platform, NEST.PH, covers everything from your first virtual room viewing to your everyday tenant needs, so you can focus on school instead of paperwork.</p>
    </section>

    <section class="about-section textured">
        <img src="{{ asset('images/leaf-texture-1.png') }}" class="bg-texture" alt="" loading="lazy" decoding="async">
        <div class="about-header">
            <div class="eyebrow">Powered by NEST.PH, a smarter web app for dormitory living and management</div>
            <h2>About NEST.PH</h2>
        </div>

        <div class="feature-grid">
            <div class="feature">
                <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="8" width="20" height="9" rx="4"/><circle cx="8" cy="12.5" r="2"/><circle cx="16" cy="12.5" r="2"/><path d="M12 8V5a3 3 0 00-3-3H7"/></svg></div>
                <p><strong>360&deg; VR Room Viewing:</strong> Walk through real rooms and common areas in immersive 360&deg; from any device. See the actual space, layout, and lighting before you commit, with no guesswork and no wasted trips.</p>
            </div>
            <div class="feature">
                <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 3l7 3v6c0 5-3.5 8-7 9-3.5-1-7-4-7-9V6l7-3z"/><path d="M12 8v4M12 15h.01"/></svg></div>
                <p><strong>Smart Delinquency Escalation:</strong> Automated reminders and a clear, staged notice process keep accounts on track, giving tenants fair warning and dorm owners a system that runs itself instead of chasing payments by hand.</p>
            </div>
            <div class="feature">
                <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 7v10l9 4 9-4V7"/></svg></div>
                <p><strong>Online Tenant Portal:</strong> Manage your stay entirely online. Check your billing statements, upload proof of payment, and track your payment history without lining up at the admin office.</p>
            </div>
            <div class="feature">
                <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></div>
                <p><strong>Digital Maintenance Requests:</strong> Got a leaking faucet or a busted lightbulb? Submit a maintenance ticket directly through the NEST.PH portal and track its resolution status in real-time.</p>
            </div>
        </div>

        <p class="about-contact-note">Want NEST.PH for your own dormitory? Contact us at thenestphils@gmail.com.</p>
    </section>

    <footer>
        <div class="footer-top">
            <div class="footer-logo"><img src="{{ asset('images/nestph.png') }}" alt="NEST.PH" class="logo-img"> NEST.PH</div>
            <form class="newsletter" onsubmit="return false;">
                <label for="newsletter-email" class="visually-hidden">Email address</label>
                <input id="newsletter-email" type="email" name="email" placeholder="Enter your email to inquire" autocomplete="email" required>
                <button type="submit">Send</button>
            </form>
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
                @if($isBirVerified)
                <div class="footer-badge">
                    @if($birRegistrationImageUrl)
                        <img src="{{ $birRegistrationImageUrl }}" alt="BIR registration certificate" loading="lazy">
                    @else
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16"><path d="M20 6L9 17l-5-5"/></svg>
                    @endif
                    <span>Registered with the Bureau of Internal Revenue</span>
                </div>
                @endif
            </div>
        </div>

        <div class="footer-bottom">
            <span>Powered by {{ $dormName ?? 'NEST.PH' }} Dormitory Management System &copy; 2026. All rights reserved.</span>
        </div>
    </footer>
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