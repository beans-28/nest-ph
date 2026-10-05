<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $brandDormName }} · Malayo sa bahay, pero at home.</title>
    <link rel="icon" href="{{ $brandFaviconUrl }}">
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
        .hero-image { position: relative; }
        .hero-image img { width: 100%; height: 100%; object-fit: cover; }
        .hero-slide { position: absolute; inset: 0; opacity: 0; transition: opacity .8s ease; }
        .hero-slide.active { opacity: 1; }
        .hero-arrow {
            position: absolute; top: 50%; transform: translateY(-50%); width: 36px; height: 36px; border-radius: 50%;
            border: none; background: rgba(255,255,255,0.85); color: #292420; cursor: pointer; z-index: 2;
            display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }
        .hero-arrow svg { width: 18px; height: 18px; }
        .hero-arrow.prev { left: 10px; } .hero-arrow.next { right: 10px; }
        .hero-arrow:focus-visible, .hero-dots button:focus-visible, .hero-pause:focus-visible { outline: 3px solid #fff; outline-offset: 2px; }
        .hero-dots { position: absolute; bottom: 6px; left: 0; right: 0; display: flex; justify-content: center; gap: 2px; z-index: 2; }
        /* 28px tap area around a 10px dot */
        .hero-dots button { width: 28px; height: 28px; border: none; padding: 0; background: none; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .hero-dots button::before { content: ''; width: 10px; height: 10px; border-radius: 50%; background: rgba(255,255,255,0.55); box-shadow: 0 0 0 1px rgba(0,0,0,0.25); }
        .hero-dots button[aria-current="true"]::before { background: #fff; }
        .hero-pause {
            position: absolute; top: 10px; right: 10px; width: 32px; height: 32px; border-radius: 50%; border: none; z-index: 2;
            background: rgba(255,255,255,0.85); color: #292420; cursor: pointer; display: flex; align-items: center; justify-content: center;
        }
        .hero-pause svg { width: 14px; height: 14px; }
        @media (prefers-reduced-motion: reduce) { .hero-slide { transition: none; } }

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

        .site-footer {
            background: #2f2723; color: rgba(255,255,255,0.78);
            padding: clamp(36px, 5vw, 56px) clamp(20px, 6vw, 64px) 22px; font-size: 13.5px; line-height: 1.6;
        }
        .footer-grid { display: grid; grid-template-columns: 1.5fr 1.4fr 0.9fr 1.3fr; gap: 40px; padding-bottom: 32px; }
        .footer-name { color: #fff; font-weight: 700; font-size: 18px; margin-bottom: 10px; }
        .footer-tagline { max-width: 320px; }
        .footer-note { margin-top: 14px; font-size: 12px; color: rgba(255,255,255,0.55); }
        .site-footer h4 { color: #fff; font-size: 12px; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 14px; }
        .site-footer ul { list-style: none; padding: 0; margin: 0; }
        .site-footer li { margin-bottom: 8px; overflow-wrap: anywhere; }
        .site-footer a { color: rgba(255,255,255,0.78); text-decoration: none; }
        .site-footer a:hover { color: #fff; text-decoration: underline; }
        .footer-bir { display: block; max-width: 100%; max-height: 140px; width: auto; border-radius: 6px; background: #fff; }
        .footer-muted { color: rgba(255,255,255,0.5); }
        .footer-bottom {
            display: flex; justify-content: space-between; flex-wrap: wrap; gap: 8px;
            padding-top: 18px; border-top: 1px solid rgba(255,255,255,0.12); font-size: 12px;
        }

        @media (max-width: 1024px) {
            .topnav, .hero, .stats-bar, .why-section, .about-section, footer { padding-left: 28px; padding-right: 28px; }
            .hero { grid-template-columns: 1fr; }
            .hero-image { max-width: 100%; margin-left: 0; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
            .feature-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .topnav { flex-wrap: wrap; }
        }
        @media (max-width: 640px) {
            .topnav, .hero, .stats-bar, .why-section, .about-section, footer { padding-left: 18px; padding-right: 18px; }
            .hero-text h1 { font-size: 24px; }
            .stats-row { grid-template-columns: 1fr 1fr; }
            .footer-grid { grid-template-columns: 1fr; gap: 28px; }
            .footer-bottom { flex-direction: column; align-items: flex-start; }

            /* Topnav: the flex-wrap layout at wider breakpoints packs the
               menu, logo and button groups unpredictably at phone widths,
               so stack them into clear rows instead. */
            .topnav { flex-direction: column; align-items: stretch; gap: 12px; padding-top: 14px; padding-bottom: 14px; }
            .topnav .logo { order: -1; justify-content: center; }
            .topnav .menu { flex: none; justify-content: center; flex-wrap: wrap; row-gap: 8px; }
            .topnav .menu a, .topnav .menu span { padding: 10px 8px; }
            .topnav .buttons { flex: none; justify-content: center; flex-wrap: wrap; row-gap: 10px; }

        }
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
        <div class="hero-image" id="heroCarousel" @if(count($heroPhotoUrls) > 1) role="region" aria-roledescription="carousel" aria-label="Photos of {{ $dormName }}" @endif>
            @foreach($heroPhotoUrls as $i => $url)
                <img src="{{ $url }}" alt="{{ $i === 0 ? $dormName : '' }}" class="hero-slide {{ $i === 0 ? 'active' : '' }}" @if($i > 0) loading="lazy" aria-hidden="true" @endif>
            @endforeach
            @if(count($heroPhotoUrls) > 1)
                <button type="button" class="hero-pause" aria-label="Pause slideshow"><svg viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="5" width="4" height="14"/><rect x="14" y="5" width="4" height="14"/></svg></button>
                <button type="button" class="hero-arrow prev" aria-label="Previous photo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg></button>
                <button type="button" class="hero-arrow next" aria-label="Next photo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg></button>
                <div class="hero-dots">
                    @foreach($heroPhotoUrls as $i => $url)
                        <button type="button" aria-label="Show photo {{ $i + 1 }} of {{ count($heroPhotoUrls) }}" @if($i === 0) aria-current="true" @endif></button>
                    @endforeach
                </div>
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
                <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h.01"/><path d="M2 8.82a15 15 0 0 1 20 0"/><path d="M5 12.859a10 10 0 0 1 14 0"/><path d="M8.5 16.429a5 5 0 0 1 7 0"/></svg></div>
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
                <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v6c0 5-3.5 8-7 9-3.5-1-7-4-7-9V6l7-3z"/><path d="M12 8v4M12 15h.01"/></svg></div>
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

    <footer class="site-footer">
        <div class="footer-grid">
            <div class="footer-brand">
                <div class="footer-name">{{ $brandDormName }}</div>
                <p class="footer-tagline">Safe, affordable rooms for students and young professionals, a short walk from PUP and LRT-2 Pureza Station.</p>
                @if($isBirVerified && ! $birRegistrationImageUrl)
                <p class="footer-note">BIR-registered business</p>
                @endif
            </div>
            <div>
                <h4>Contact</h4>
                <ul>
                    @if($address)<li>{{ $address }}</li>@endif
                    @if($contactNumber)<li><a href="tel:{{ preg_replace('/[^0-9+]/', '', $contactNumber) }}">{{ $contactNumber }}</a></li>@endif
                    @if($contactEmail)<li><a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></li>@endif
                    @if($facebookUrl)<li><a href="{{ $facebookUrl }}" target="_blank" rel="noopener">Facebook: {{ $facebookPageName ?: $brandDormName }}</a></li>@endif
                </ul>
            </div>
            <div>
                <h4>Explore</h4>
                <ul>
                    <li><a href="{{ route('public.rooms') }}">Rooms &amp; Rates</a></li>
                    <li><a href="{{ route('public.vr') }}">Virtual Tour</a></li>
                    <li><a href="{{ route('login.tenant') }}">Tenant Portal</a></li>
                </ul>
            </div>
            <div>
                @if($isBirVerified && $birRegistrationImageUrl)
                <img src="{{ $birRegistrationImageUrl }}" alt="Certificate of Registration with the Bureau of Internal Revenue" class="footer-bir" loading="lazy">
                @endif
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} {{ $brandDormName }}. All rights reserved.</span>
            <span class="footer-muted">Powered by NEST.PH Dormitory Management System</span>
        </div>
    </footer>
    </div>
    </div>

    <script>
        // Hero slideshow: only runs with 2+ photos. Auto-advances every 5s
        // unless the visitor prefers reduced motion; pauses on hover, on
        // keyboard focus, and with the pause button. Swipe works on phones.
        (function () {
            const root = document.getElementById('heroCarousel');
            const slides = root ? root.querySelectorAll('.hero-slide') : [];
            if (slides.length < 2) return;
            const dots = root.querySelectorAll('.hero-dots button');
            const pauseBtn = root.querySelector('.hero-pause');
            const PLAY = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M7 5l12 7-12 7z"/></svg>';
            const PAUSE = pauseBtn.innerHTML;
            let current = 0, timer = null, paused = false, hovering = false;
            function show(n) {
                slides[current].classList.remove('active'); slides[current].setAttribute('aria-hidden', 'true');
                dots[current].removeAttribute('aria-current');
                current = (n + slides.length) % slides.length;
                slides[current].classList.add('active'); slides[current].removeAttribute('aria-hidden');
                dots[current].setAttribute('aria-current', 'true');
            }
            function sync() {
                clearInterval(timer);
                if (!paused && !hovering) timer = setInterval(() => show(current + 1), 5000);
                pauseBtn.innerHTML = paused ? PLAY : PAUSE;
                pauseBtn.setAttribute('aria-label', paused ? 'Play slideshow' : 'Pause slideshow');
            }
            pauseBtn.addEventListener('click', () => { paused = !paused; sync(); });
            root.querySelector('.prev').addEventListener('click', () => { show(current - 1); sync(); });
            root.querySelector('.next').addEventListener('click', () => { show(current + 1); sync(); });
            dots.forEach((d, i) => d.addEventListener('click', () => { show(i); sync(); }));
            root.addEventListener('mouseenter', () => { hovering = true; sync(); });
            root.addEventListener('mouseleave', () => { hovering = false; sync(); });
            root.addEventListener('focusin', () => { hovering = true; sync(); });
            root.addEventListener('focusout', e => { if (!root.contains(e.relatedTarget)) { hovering = false; sync(); } });
            let startX = null;
            root.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
            root.addEventListener('touchend', e => {
                if (startX === null) return;
                const dx = e.changedTouches[0].clientX - startX;
                if (Math.abs(dx) > 40) { show(current + (dx < 0 ? 1 : -1)); sync(); }
                startX = null;
            });
            sync();
        })();

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