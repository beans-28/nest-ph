<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NEST.PH · Dormitory Management System</title>
    <link rel="icon" href="{{ asset('images/nestphgreen.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    {{-- Same tokens, nav, hero, section and footer styles as welcome.blade.php
         (the dormitory homepage) so the platform and dorm pages look alike. --}}
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
        html { scroll-behavior: smooth; }
        @media (prefers-reduced-motion: reduce) { html { scroll-behavior: auto; } }
        body { font-family: 'Roboto', system-ui, -apple-system, sans-serif; color: var(--ink); background: #fff; }
        section[id] { scroll-margin-top: 80px; }
        .page-wrap { overflow-x: hidden; }
        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; display: block; }

        a:focus-visible, button:focus-visible, input:focus-visible, textarea:focus-visible {
            outline: 2px solid var(--green-darker); outline-offset: 2px;
        }
        .topnav a:focus-visible, .topnav .buttons a:focus-visible, footer a:focus-visible { outline: 2px solid #fff; outline-offset: 2px; }

        .textured { position: relative; overflow: hidden; }
        .textured .bg-texture {
            position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover;
            pointer-events: none; z-index: 0; mix-blend-mode: multiply; opacity: 0.5;
        }
        .textured > *:not(.bg-texture) { position: relative; z-index: 1; }

        .topnav {
            background: linear-gradient(90deg, var(--green-darker), var(--green-dark));
            padding: 14px clamp(20px, 5vw, 64px);
            display: flex; align-items: center; gap: clamp(16px, 3vw, 40px);
            position: sticky; top: 0; z-index: 1000; transition: box-shadow 0.25s ease;
        }
        .topnav.scrolled { box-shadow: 0 4px 14px rgba(0,0,0,0.2); }
        .topnav .menu { flex: 1; display: flex; align-items: center; gap: 10px; }
        .topnav .menu a { color: #fff; font-weight: 500; font-size: 14px; padding: 10px 6px; display: inline-flex; align-items: center; gap: 4px; }
        .topnav .menu a.pill { border: 1px solid rgba(255,255,255,0.5); border-radius: 999px; padding: 7px 16px; }
        .topnav .menu a.pill:hover { background: rgba(255,255,255,0.12); }
        .topnav .logo { display: flex; align-items: center; gap: 6px; color: #fff; font-weight: 700; font-size: 19px; letter-spacing: 0.02em; white-space: nowrap; }
        .topnav .logo .logo-img { height: 32px; width: auto; }
        .topnav .buttons { flex: 1; display: flex; justify-content: flex-end; gap: 12px; }

        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            height: 44px; padding: 0 18px; border: 2px solid #fff;
            font-weight: 500; font-size: 13.5px; letter-spacing: 0.02em; cursor: pointer; white-space: nowrap; font-family: inherit;
        }
        .btn-white { background: #fff; color: var(--ink); }
        .btn-outline-white { background: transparent; color: #fff; }
        .btn-green { background: var(--green-dark); border-color: var(--green-dark); color: #fff; }
        .btn-outline-green { background: transparent; border-color: var(--green-darker); color: var(--green-darker); }
        .btn-green:hover { background: #48614a; border-color: #48614a; }
        .btn-outline-green:hover { background: rgba(25,115,53,0.08); }
        .btn-lg { height: 46px; padding: 0 22px; font-size: 15px; }
        .btn[disabled] { opacity: .6; cursor: wait; }

        .hero {
            background: linear-gradient(90deg, var(--cream), var(--cream-light));
            padding: clamp(32px, 6vw, 56px) clamp(20px, 6vw, 64px);
            display: grid; grid-template-columns: 1.1fr 1fr; gap: 32px; align-items: center;
            box-shadow: 0 4px 2px rgba(0,0,0,0.15);
        }
        .hero-text h1 { font-size: clamp(24px, 3.2vw, 36px); font-weight: 700; line-height: 1.2; margin-bottom: 16px; }
        .hero-text p { font-size: 15px; line-height: 1.6; margin-bottom: 24px; max-width: 540px; }
        .hero-buttons { display: flex; gap: 14px; flex-wrap: wrap; }
        .hero-image {
            width: 100%; max-width: 460px; margin-left: auto; aspect-ratio: 4 / 3; border-radius: 4px; overflow: hidden;
            box-shadow: inset 0 4px 4px rgba(0,0,0,0.25);
            background: linear-gradient(135deg, var(--green-darker), var(--green-dark));
            display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 14px; color: #fff; text-align: center; padding: 24px;
        }
        .hero-image img { width: 38%; height: auto; }
        .hero-image span { font-size: 15px; font-weight: 500; max-width: 280px; }

        .eyebrow { font-size: 12.5px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: var(--green-darker); margin-bottom: 8px; }

        /* "Choose a dormitory": green leaf band, like the homepage stats bar */
        .stats-bar {
            background: linear-gradient(90deg, var(--green-dark), var(--green-light));
            padding: clamp(28px, 5vw, 44px) clamp(20px, 6vw, 64px); text-align: center;
        }
        .stats-bar .eyebrow { color: #fff; }
        /* White text here: ink on the dark end of this gradient was only ~3:1 */
        .stats-bar h2 { font-size: clamp(20px, 2.4vw, 26px); font-weight: 700; margin-bottom: 8px; color: #fff; text-shadow: 0 1px 2px rgba(0,0,0,0.25); }
        .stats-bar .sub { font-size: 14px; margin-bottom: 24px; color: #fff; text-shadow: 0 1px 2px rgba(0,0,0,0.25); }
        .dorm-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 440px)); justify-content: center; gap: 24px; max-width: 900px; margin: 0 auto; text-align: left; }
        .dorm-card { background: #fff; border-radius: 4px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
        /* No photo yet (or it failed to load): the branded leaf placeholder shows through */
        .dorm-photo { aspect-ratio: 16 / 9; position: relative; overflow: hidden; background: linear-gradient(135deg, var(--green-darker), var(--green-dark)); }
        .dorm-photo .bg-texture { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; mix-blend-mode: multiply; opacity: 0.5; }
        .dorm-photo-empty { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; color: #fff; font-size: 13px; font-weight: 500; }
        .dorm-photo-empty img { width: 56px; height: auto; opacity: 0.9; }
        .dorm-photo > .dorm-photo-img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 1; }
        .dorm-body { padding: 18px 20px 20px; display: flex; flex-direction: column; gap: 8px; flex: 1; }
        .dorm-body h3 { font-size: 18px; font-weight: 700; }
        .dorm-body .addr { font-size: 13.5px; color: var(--ink-alt); }
        .dorm-body p { font-size: 13.5px; line-height: 1.55; }
        .dorm-badge { align-self: flex-start; font-size: 11.5px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; color: var(--green-darker); background: #e8f4e9; padding: 4px 10px; border-radius: 999px; }
        .dorm-body .btn { margin-top: auto; align-self: flex-start; }
        .owner-strip {
            max-width: 900px; margin: 24px auto 0; padding: 16px 20px; border-radius: 4px;
            background: rgba(255,255,255,0.92); box-shadow: 0 4px 10px rgba(0,0,0,0.12);
            display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; text-align: left;
        }
        .owner-strip p { font-size: 14px; line-height: 1.5; color: var(--ink); }
        .owner-strip strong { display: block; font-size: 15px; }

        /* "About NEST.PH": same as the homepage's dark green feature section */
        .about-section { background: var(--green-dark); padding: clamp(28px, 5vw, 44px) clamp(20px, 6vw, 64px); }
        .about-header { text-align: center; max-width: 720px; margin: 0 auto 32px; }
        .about-header .eyebrow { color: rgba(255,255,255,0.85); }
        .about-header h2 { color: #fff; font-size: clamp(19px, 2.4vw, 24px); font-weight: 700; }
        .feature-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 28px 44px; max-width: 860px; margin: 0 auto; }
        .feature { display: flex; flex-direction: column; align-items: center; gap: 12px; text-align: center; color: #fff; }
        .feature-icon { width: 48px; height: 48px; color: #fff; }
        .feature-icon svg { width: 100%; height: 100%; }
        .feature p { font-size: 13.5px; line-height: 1.55; }

        /* "For dormitory owners": cream band, like the homepage's "Why Pick Us" */
        .why-section {
            background: linear-gradient(90deg, var(--cream), var(--cream-light));
            padding: clamp(28px, 5vw, 44px) clamp(20px, 6vw, 64px);
            box-shadow: 0 4px 2px rgba(0,0,0,0.15);
        }
        .why-head { text-align: center; max-width: 740px; margin: 0 auto 28px; }
        .why-head h2 { font-size: clamp(20px, 2.6vw, 26px); font-weight: 700; margin-bottom: 14px; }
        .why-head p { font-size: 14.5px; line-height: 1.6; }
        .steps { list-style: none; counter-reset: step; display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; max-width: 900px; margin: 0 auto 32px; }
        .steps li { counter-increment: step; text-align: center; font-size: 13.5px; line-height: 1.5; color: var(--ink-alt); }
        .steps li::before {
            content: counter(step); display: flex; align-items: center; justify-content: center;
            width: 36px; height: 36px; margin: 0 auto 10px; border-radius: 50%; background: var(--green-dark); color: #fff; font-weight: 700;
        }
        .steps strong { display: block; color: var(--ink); font-size: 14.5px; margin-bottom: 2px; }

        form.request {
            max-width: 860px; margin: 0 auto; background: #fff; border-radius: 4px; padding: clamp(20px, 4vw, 32px);
            display: grid; grid-template-columns: 1fr 1fr; gap: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .field { display: flex; flex-direction: column; gap: 6px; }
        .field.full { grid-column: 1 / -1; }
        .field label { font-weight: 500; font-size: 13.5px; }
        .req-star { color: #b42318; }
        .field input, .field textarea { font: inherit; font-size: 14px; padding: 10px 12px; border: 1px solid var(--gray-border); border-radius: 4px; width: 100%; background: #fff; }
        .field textarea { min-height: 110px; resize: vertical; }
        .consent { grid-column: 1 / -1; display: flex; gap: 10px; align-items: flex-start; font-size: 13px; line-height: 1.5; }
        .consent input { margin-top: 3px; accent-color: var(--green-darker); }
        .field-error { font-size: 12.5px; color: #b42318; }
        .field input[aria-invalid="true"], .field textarea[aria-invalid="true"] { border-color: #b42318; }
        ::selection { background: var(--green-light); color: var(--ink); }
        .form-msg { grid-column: 1 / -1; padding: 12px 14px; border-radius: 4px; font-size: 14px; display: none; }
        .form-msg.ok { display: block; background: #e8f4e9; color: var(--green-darker); }
        .form-msg.err { display: block; background: #fdecea; color: #b42318; }

        .site-footer {
            background: #2f2723; color: rgba(255,255,255,0.78);
            padding: clamp(36px, 5vw, 56px) clamp(20px, 6vw, 64px) 22px; font-size: 13.5px; line-height: 1.6;
        }
        .footer-grid { display: grid; grid-template-columns: 1.5fr 1.4fr 0.9fr 1.3fr; gap: 40px; padding-bottom: 32px; }
        .footer-name { color: #fff; font-weight: 700; font-size: 18px; margin-bottom: 10px; }
        .footer-tagline { max-width: 320px; }
        .site-footer h4 { color: #fff; font-size: 12px; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 14px; }
        .site-footer ul { list-style: none; }
        .site-footer li { margin-bottom: 8px; overflow-wrap: anywhere; }
        .site-footer a { color: rgba(255,255,255,0.78); }
        .site-footer a:hover { color: #fff; text-decoration: underline; }
        .footer-muted { color: rgba(255,255,255,0.5); }
        .footer-bottom { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 8px; padding-top: 18px; border-top: 1px solid rgba(255,255,255,0.12); font-size: 12px; }

        @media (max-width: 1024px) {
            .topnav, .hero, .stats-bar, .why-section, .about-section, footer { padding-left: 28px; padding-right: 28px; }
            .hero { grid-template-columns: 1fr; }
            /* Short banner when stacked, so the dorm list isn't pushed far down */
            .hero-image { max-width: 100%; margin-left: 0; aspect-ratio: auto; flex-direction: row; padding: 16px 20px; }
            .hero-image img { width: 56px; }
            .feature-grid { grid-template-columns: 1fr; }
            .steps { grid-template-columns: repeat(2, 1fr); }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .topnav { flex-wrap: wrap; }
        }
        @media (max-width: 640px) {
            .topnav, .hero, .stats-bar, .why-section, .about-section, footer { padding-left: 18px; padding-right: 18px; }
            .hero-text h1 { font-size: 24px; }
            .dorm-grid, form.request { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; gap: 28px; }
            .footer-bottom { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
    @include('partials.public-nav', ['platformNav' => true])

    <div class="page-wrap">
    <section class="hero">
        <div class="hero-text">
            <div class="eyebrow">Dormitory Management System</div>
            <h1>Welcome to NEST.PH!</h1>
            <p>NEST.PH is a web app for dormitory living and management. Students find rooms, take virtual tours and apply online, while dormitory owners handle tenants, contracts, billing and maintenance in one place.</p>
            <div class="hero-buttons">
                <a href="#dormitories" class="btn btn-green btn-lg">Find a Dormitory</a>
                <a href="#for-owners" class="btn btn-outline-green btn-lg">I Own a Dormitory</a>
            </div>
        </div>
        <div class="hero-image" aria-hidden="true">
            <img src="{{ asset('images/nestph.png') }}" alt="">
            <span>Malayo sa bahay, pero at home.</span>
        </div>
    </section>

    <section class="stats-bar textured" id="dormitories">
        <img src="{{ asset('images/leaf-texture-1.png') }}" class="bg-texture" alt="" loading="lazy" decoding="async">
        <div class="eyebrow">Choose a Dormitory</div>
        <h2>Dormitories on NEST.PH</h2>
        <p class="sub">Pick a dormitory to browse its rooms, take a 360&deg; virtual tour, and apply online!</p>
        <div class="dorm-grid">
            <article class="dorm-card">
                <div class="dorm-photo">
                    <img src="{{ asset('images/leaf-texture-1.png') }}" class="bg-texture" alt="" loading="lazy">
                    <div class="dorm-photo-empty" aria-hidden="true">
                        <img src="{{ asset('images/nestph.png') }}" alt="">
                        Photos coming soon
                    </div>
                    @if ($dormPhotoUrl)
                        {{-- If the photo fails to load, remove it so the placeholder shows instead. --}}
                        <img src="{{ $dormPhotoUrl }}" alt="{{ $dormName }}" class="dorm-photo-img" loading="lazy" onerror="this.remove()">
                    @endif
                </div>
                <div class="dorm-body">
                    <span class="dorm-badge">Now accepting tenants</span>
                    <h3>{{ $dormName }}</h3>
                    @if ($dormAddress)<p class="addr">{{ $dormAddress }}</p>@endif
                    @if ($dormDescription)<p>{{ \Illuminate\Support\Str::limit($dormDescription, 140) }}</p>@endif
                    <a href="{{ route('home') }}" class="btn btn-green">Visit Dormitory</a>
                </div>
            </article>
        </div>
        <div class="owner-strip">
            <p><strong>Own a dormitory?</strong>Bring it onto NEST.PH and manage tenants, billing and maintenance the same way.</p>
            <a href="#for-owners" class="btn btn-green">Request NEST.PH</a>
        </div>
    </section>

    <section class="about-section textured" id="about">
        <img src="{{ asset('images/leaf-texture-1.png') }}" class="bg-texture" alt="" loading="lazy" decoding="async">
        <div class="about-header">
            <div class="eyebrow">A smarter web app for dormitory living and management</div>
            <h2>About NEST.PH</h2>
        </div>
        <div class="feature-grid">
            <div class="feature">
                <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="8" width="20" height="9" rx="4"/><circle cx="8" cy="12.5" r="2"/><circle cx="16" cy="12.5" r="2"/><path d="M12 8V5a3 3 0 00-3-3H7"/></svg></div>
                <p><strong>360&deg; VR Room Viewing:</strong> Each dormitory can show its real rooms and common areas in immersive 360&deg;, so students see the space before they commit.</p>
            </div>
            <div class="feature">
                <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v6c0 5-3.5 8-7 9-3.5-1-7-4-7-9V6l7-3z"/><path d="M12 8v4M12 15h.01"/></svg></div>
                <p><strong>Automatic Payment Reminders:</strong> Tenants get reminders before and after their due date, and follow-up notices go out in clear steps, following each dormitory's own policies.</p>
            </div>
            <div class="feature">
                <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 7v10l9 4 9-4V7"/></svg></div>
                <p><strong>Online Applications &amp; Tenant Portal:</strong> Students apply online, and tenants check bills, upload proof of payment and track their history without going to the admin office.</p>
            </div>
            <div class="feature">
                <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></div>
                <p><strong>Digital Maintenance Requests:</strong> Tenants submit maintenance tickets online and track them, while admins assign, prioritize and resolve them on time.</p>
            </div>
        </div>
    </section>

    <section class="why-section" id="for-owners">
        <div class="why-head">
            <div class="eyebrow">For Dormitory Owners</div>
            <h2>Request NEST.PH for Your Dormitory</h2>
            <p>Tell us about your dormitory and the NEST.PH team will get in touch. Here's how it works:</p>
        </div>
        <ol class="steps">
            <li><strong>Send a request</strong>Fill out the form below with your dormitory's details.</li>
            <li><strong>We contact you</strong>Our team reaches out to discuss your needs and schedule a demo.</li>
            <li><strong>Setup</strong>We set up your rooms, rates, house rules and admin accounts.</li>
            <li><strong>Go live</strong>Your dormitory gets its own page here and starts accepting tenants.</li>
        </ol>

        <form class="request" id="partnerForm" novalidate>
            <div class="field"><label for="owner_name">Your name <span class="req-star">*</span></label><input id="owner_name" name="owner_name" required maxlength="255" autocomplete="name"></div>
            <div class="field"><label for="dorm_name">Dormitory name <span class="req-star">*</span></label><input id="dorm_name" name="dorm_name" required maxlength="255"></div>
            <div class="field"><label for="email">Email <span class="req-star">*</span></label><input id="email" name="email" type="email" required maxlength="255" autocomplete="email"></div>
            <div class="field"><label for="contact_number">Contact number <span class="req-star">*</span></label><input id="contact_number" name="contact_number" type="tel" required maxlength="30" autocomplete="tel" placeholder="09XX XXX XXXX"></div>
            <div class="field"><label for="city">City / municipality</label><input id="city" name="city" maxlength="255"></div>
            <div class="field"><label for="bed_count">Approx. number of beds</label><input id="bed_count" name="bed_count" type="number" min="1" max="10000"></div>
            <div class="field full"><label for="message">Anything else we should know?</label><textarea id="message" name="message" maxlength="2000" placeholder="Current setup, questions, preferred schedule for a demo..."></textarea></div>
            <label class="consent"><input type="checkbox" name="dpa_consent" id="dpa_consent" required> <span>I agree that NEST.PH may use these details to contact me about this request, in line with the Data Privacy Act of 2012. <span class="req-star" aria-hidden="true">*</span></span></label>
            <div class="field full">@include('partials.turnstile', ['id' => 'partnerTurnstile'])</div>
            <div class="form-msg" id="partnerMsg" role="status" aria-live="polite"></div>
            <div class="field full"><button type="submit" class="btn btn-green btn-lg" id="partnerSubmit">Send Request</button></div>
        </form>
    </section>

    <footer class="site-footer">
        <div class="footer-grid">
            <div>
                <div class="footer-name">NEST.PH</div>
                <p class="footer-tagline">A smarter web app for dormitory living and management in the Philippines.</p>
            </div>
            <div>
                <h4>Contact</h4>
                <ul>
                    <li><a href="mailto:thenestphils@gmail.com">thenestphils@gmail.com</a></li>
                </ul>
            </div>
            <div>
                <h4>Explore</h4>
                <ul>
                    <li><a href="#dormitories">Dormitories</a></li>
                    <li><a href="#for-owners">For Dorm Owners</a></li>
                    <li><a href="{{ route('login.platform') }}">NEST.PH Team Login</a></li>
                </ul>
            </div>
            <div>
                <h4>Dormitories</h4>
                <ul>
                    <li><a href="{{ route('home') }}">{{ $dormName }}</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} NEST.PH. All rights reserved.</span>
            <span class="footer-muted">NEST.PH Dormitory Management System</span>
        </div>
    </footer>
    </div>

    <script>
        window.addEventListener('scroll', function () {
            document.querySelector('.topnav').classList.toggle('scrolled', window.scrollY > 10);
        }, { passive: true });

        const form = document.getElementById('partnerForm');
        const msg = document.getElementById('partnerMsg');
        const submitBtn = document.getElementById('partnerSubmit');
        const requestUrl = @json(route('platform.partner-request'));

        function showMsg(text, ok) {
            msg.textContent = text;
            msg.className = 'form-msg ' + (ok ? 'ok' : 'err');
        }

        // Show each server error under its own field.
        function clearFieldErrors() {
            form.querySelectorAll('.field-error').forEach(el => el.remove());
            form.querySelectorAll('[aria-invalid]').forEach(el => {
                el.removeAttribute('aria-invalid');
                el.removeAttribute('aria-describedby');
            });
        }
        function showFieldErrors(errors) {
            Object.entries(errors).forEach(([name, messages]) => {
                const input = form.elements[name];
                const holder = input && input.closest ? (input.closest('.field') || input.closest('.consent')) : null;
                if (!holder) return;
                const note = document.createElement('span');
                note.className = 'field-error';
                note.id = 'err-' + name;
                note.textContent = messages[0];
                input.setAttribute('aria-invalid', 'true');
                input.setAttribute('aria-describedby', note.id);
                holder.appendChild(note);
            });
            const first = form.querySelector('[aria-invalid="true"]');
            if (first) first.focus();
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const data = Object.fromEntries(new FormData(form));
            data.dpa_consent = form.dpa_consent.checked ? 1 : 0;
            data.cf_turnstile_response = turnstileToken('partnerTurnstile');
            turnstileReset('partnerTurnstile');

            clearFieldErrors();
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending…';
            form.setAttribute('aria-busy', 'true');
            try {
                const response = await fetch(requestUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify(data),
                });
                const result = await response.json().catch(() => ({}));
                if (response.ok) {
                    form.reset();
                    showMsg(result.message || 'Thank you! The NEST.PH team will contact you within 2–3 business days.', true);
                } else if (response.status === 429) {
                    showMsg('Too many requests. Please try again in a few minutes.', false);
                } else if (result.errors) {
                    showFieldErrors(result.errors);
                    showMsg(result.errors.cf_turnstile_response
                        ? 'Please complete the security check above, then send again.'
                        : 'Please fix the highlighted fields, then send again.', false);
                } else {
                    showMsg(result.message || 'Something went wrong. Please try again.', false);
                }
            } catch (err) {
                showMsg('Could not send your request. Please check your connection.', false);
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Send Request';
                form.removeAttribute('aria-busy');
            }
        });
    </script>
</body>
</html>
