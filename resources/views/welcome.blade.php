<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    {{-- Page-switch fade opt-in: must be inline and first in <head> or Chrome decides before it sees it (see typography.css). --}}
    <style>@view-transition{navigation:auto}</style><link rel="stylesheet" href="/css/motion.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $brandDormName }} · Malayo sa bahay, pero at home.</title>
    <link rel="icon" href="{{ $brandFaviconUrl }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/typography.css') }}">
    <script src="{{ asset('js/smooth-nav.js') }}" defer></script>
    <script src="{{ asset('js/a11y-dialogs.js') }}" defer></script>
    @if($heroPanoramaUrl)
        <link rel="stylesheet" href="{{ asset('vendor/pannellum/pannellum.css') }}">
        <script src="{{ asset('vendor/pannellum/pannellum.js') }}" defer></script>
    @endif
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
            /* The whole palette: the page background, ONE light tint of the dorm's
               color for alternating bands, and the dorm's color as accent. No
               gradients, no extra greys, so any color the dorm picks stays calm. */
            --surface: var(--bg-page, #fff);
            --surface-alt: color-mix(in srgb, var(--green-dark) 7%, var(--surface));
            --hairline: color-mix(in srgb, var(--green-dark) 14%, transparent);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
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
            /* The leaf photo is green; greyscale keeps only its pattern so
               it never tints the dorm's chosen color green. */
            filter: grayscale(1);
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
        .btn-lg { height: 48px; padding: 0 24px; font-size: 15px; }
        /* Page buttons match the rounded photo, cards and map (top bar keeps its own). */
        .page-wrap .btn { border-radius: 10px; font-weight: 600; transition: background-color .2s ease, color .2s ease, transform .2s cubic-bezier(.32,.72,0,1); }
        .page-wrap .btn:active { transform: translateY(1px); }
        .page-wrap .btn-green:hover { background: var(--green-darker); border-color: var(--green-darker); }
        .page-wrap .btn-outline-green:hover { background: color-mix(in srgb, var(--green-dark) 9%, transparent); }
        ::selection { background: color-mix(in srgb, var(--green-dark) 25%, #fff); color: var(--ink); }

        /* One heading size for every section below the hero. */
        .section-title { font-size: clamp(24px, 2.8vw, 32px); font-weight: 700; line-height: 1.2; letter-spacing: -0.02em; text-wrap: balance; }

        .hero {
            background: var(--surface-alt);
            /* Content stays within ~1240px on very wide screens */
            padding: clamp(40px, 7vw, 88px) max(clamp(20px, 6vw, 64px), calc((100% - 1240px) / 2));
            display: grid;
            grid-template-columns: 1fr 1.1fr;
            gap: clamp(32px, 5vw, 72px);
            align-items: center;
        }
        .hero-text h1 { font-size: clamp(30px, 4.2vw, 52px); font-weight: 800; line-height: 1.08; letter-spacing: -0.03em; margin-bottom: 20px; text-wrap: balance; }
        .hero-text p { font-size: 16px; line-height: 1.65; margin-bottom: 32px; max-width: 34em; color: #4a423d; }
        .hero-buttons { display: flex; gap: 14px; flex-wrap: wrap; }
        .hero-image {
            width: 100%; max-width: 620px; margin-left: auto; aspect-ratio: 4 / 3; border-radius: 14px; overflow: hidden;
            /* Gallery-style frame: a thin outline in the site color floating
               just outside the photo (follows the dorm's color), plus a soft shadow. */
            outline: 2px solid var(--green-dark); outline-offset: 10px;
            box-shadow: 0 18px 40px -12px rgba(41,36,32,0.45);
            background: var(--surface-alt);
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
        /* Homepage 360° photo (replaces the slideshow when the dorm uploads one) */
        .hero-pano { position: absolute; inset: 0; cursor: grab; }
        .hero-pano:active { cursor: grabbing; }
        .hero-pano-badge {
            position: absolute; left: 10px; bottom: 10px; z-index: 2; pointer-events: none;
            display: flex; align-items: center; gap: 6px; padding: 6px 10px; border-radius: 999px;
            background: rgba(0,0,0,0.6); color: #fff; font-size: 12px; font-weight: 500;
        }
        .hero-pano-badge svg { width: 14px; height: 14px; }
        @media (max-width: 480px) { .pano-badge-long { display: none; } }
        .hero-pano-controls { position: absolute; right: 10px; bottom: 10px; z-index: 2; display: flex; gap: 8px; }
        .hero-pano-controls button {
            width: 38px; height: 38px; border-radius: 50%; border: none; cursor: pointer;
            background: rgba(255,255,255,0.9); color: #292420; box-shadow: 0 2px 6px rgba(0,0,0,0.25);
            display: flex; align-items: center; justify-content: center;
        }
        .hero-pano-controls button:hover { background: #fff; }
        .hero-pano-controls button:focus-visible { outline: 3px solid #fff; outline-offset: 2px; }
        .hero-pano-controls svg { width: 18px; height: 18px; }
        /* Full screen: the real Fullscreen API where the browser has it, or this
           fixed overlay as a fallback (iPhone Safari can't full-screen a box). */
        .hero-image:fullscreen { max-width: none; border-radius: 0; outline: none; aspect-ratio: auto; }
        .hero-image.is-fullscreen {
            position: fixed; inset: 0; z-index: 9999; max-width: none; margin: 0;
            border-radius: 0; outline: none; aspect-ratio: auto;
        }
        .hero-image:fullscreen .hero-pano-controls, .hero-image.is-fullscreen .hero-pano-controls { right: 18px; bottom: 18px; }

        /* Brand color is used as an ACCENT (icons, headings, buttons) on clean
           light surfaces, never as a big dark wash: dark shades of gold,
           orange or yellow turn brown, so whole sections in them look muddy. */
        .stats-bar {
            background: var(--surface);
            padding: clamp(48px, 7vw, 80px) clamp(20px, 6vw, 64px); text-align: center;
        }
        .stats-bar h2 { margin-bottom: 10px; }
        .stats-bar .sub { font-size: 16px; margin-bottom: 36px; color: #5b524c; }
        /* One panel split by hairlines, not four separate cards. */
        .stats-row {
            display: grid; grid-template-columns: repeat(4, 1fr); max-width: 1000px; margin: 0 auto;
            background: #fff; border-radius: 18px; overflow: hidden;
            box-shadow: 0 1px 2px rgba(41,36,32,0.06), 0 18px 40px -22px rgba(41,36,32,0.35);
        }
        .stat-card { display: flex; flex-direction: column; align-items: center; gap: 6px; padding: 28px 16px; }
        .stat-card + .stat-card { border-left: 1px solid color-mix(in srgb, var(--green-dark) 12%, #ece7e3); }
        .stat-icon {
            width: 48px; height: 48px; border-radius: 50%; padding: 12px; margin-bottom: 6px;
            display: flex; align-items: center; justify-content: center;
            color: var(--green-dark); background: color-mix(in srgb, var(--green-dark) 12%, #fff);
        }
        .stat-icon svg { width: 100%; height: 100%; }
        .stat-value { font-size: 22px; font-weight: 800; letter-spacing: -0.02em; color: var(--ink); min-height: 32px; display: flex; align-items: center; justify-content: center; line-height: 1.2; text-align: center; font-variant-numeric: tabular-nums; }
        .stat-label { font-size: 13px; color: #6b625c; }

        .why-section {
            background: var(--surface-alt);
            padding: clamp(48px, 7vw, 88px) clamp(20px, 6vw, 64px); text-align: center;
        }
        .why-section h2 { margin-bottom: 16px; }
        .why-section p { font-size: 16px; max-width: 62ch; margin: 0 auto; line-height: 1.7; color: #4a423d; text-wrap: pretty; }

        .spot-section { background: var(--surface); border-top: 1px solid var(--hairline); padding: clamp(48px, 7vw, 88px) clamp(20px, 6vw, 64px); }
        .spot-inner { max-width: 1080px; margin: 0 auto; display: grid; grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr); gap: clamp(32px, 5vw, 72px); align-items: start; }
        .spot-intro { position: sticky; top: 112px; }
        .spot-intro h2 { margin-bottom: 14px; }
        .spot-intro p { font-size: 16px; line-height: 1.65; color: #4a423d; margin-bottom: 26px; max-width: 34em; }
        @media (max-width: 860px) {
            .spot-inner { grid-template-columns: minmax(0, 1fr); }
            .spot-intro { position: static; }
        }

        .location-section { background: var(--surface); padding: clamp(48px, 7vw, 88px) clamp(20px, 6vw, 64px); }
        .location-inner { max-width: 1080px; margin: 0 auto; display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1.8fr); gap: clamp(28px, 4vw, 56px); align-items: center; }
        .location-text h2 { margin-bottom: 14px; }
        .location-text p { font-size: 15px; line-height: 1.65; margin-bottom: 10px; color: #4a423d; }
        .location-text .location-address { font-weight: 600; color: var(--ink); }
        .location-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 22px; }
        .location-map { position: relative; width: 100%; aspect-ratio: 16 / 10; border-radius: 16px; overflow: hidden; border: 1px solid color-mix(in srgb, var(--green-dark) 15%, #e5e0dc); background: var(--surface-alt); box-shadow: 0 1px 2px rgba(41,36,32,0.06), 0 14px 32px -16px rgba(41,36,32,0.3); }
        .location-map iframe { position: absolute; inset: 0; width: 100%; height: 100%; border: 0; }
        /* On phones the map grabs your finger while you scroll past it. This see-through
           cover sits on top of it on touch screens; tap once to use the map. */
        .map-shield { display: none; position: absolute; inset: 0; z-index: 1; border: 0; padding: 0; background: transparent; cursor: pointer; font: inherit; -webkit-tap-highlight-color: transparent; }
        .map-shield span { position: absolute; left: 50%; bottom: 14px; transform: translateX(-50%); white-space: nowrap; padding: 8px 14px; border-radius: 999px; background: rgba(41,36,32,0.85); color: #fff; font-size: 13px; font-weight: 600; }
        @media (hover: none) and (pointer: coarse) { .map-shield { display: block; } }
        .location-map.map-active .map-shield { display: none; }
        @media (max-width: 760px) {
            .location-inner { grid-template-columns: 1fr; }
            .location-map { aspect-ratio: 4 / 3; }
        }
        .about-section {
            background: var(--surface-alt);
            padding: clamp(48px, 7vw, 88px) clamp(20px, 6vw, 64px);
        }
        .about-header { text-align: center; max-width: 720px; margin: 0 auto 44px; }
        .about-header .sub { font-size: 16px; color: #5b524c; margin-top: 10px; }

        /* A 2x2 list divided by hairlines, not a grid of boxed cards. */
        .feature-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 56px; max-width: 1000px; margin: 0 auto; }
        .feature {
            display: flex; align-items: flex-start; gap: 18px; text-align: left; padding: 26px 0;
            border-top: 1px solid color-mix(in srgb, var(--green-dark) 18%, #e5e0dc);
        }
        .feature-icon {
            flex-shrink: 0; width: 48px; height: 48px; border-radius: 12px; padding: 11px;
            color: var(--green-dark); background: #fff;
            box-shadow: 0 1px 2px rgba(41,36,32,0.08), 0 6px 14px -8px rgba(41,36,32,0.3);
        }
        .feature-icon svg { width: 100%; height: 100%; }
        .feature p { font-size: 14.5px; line-height: 1.65; color: #4a423d; }
        .feature strong { font-weight: 700; color: var(--ink); display: block; margin-bottom: 6px; font-size: 16px; letter-spacing: -0.01em; }

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
            .stat-card:nth-child(3) { border-left: none; }
            .stat-card:nth-child(n+3) { border-top: 1px solid color-mix(in srgb, var(--green-dark) 12%, #ece7e3); }
            .feature-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .topnav { flex-wrap: wrap; }
        }
        @media (max-width: 640px) {
            .topnav, .hero, .stats-bar, .why-section, .about-section, footer { padding-left: 18px; padding-right: 18px; }
            .hero-text h1 { font-size: 30px; }
            .hero-image { outline-offset: 6px; }
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
        .about-contact-note{ text-align: center; margin-top: 28px; font-size: 13px; color: #5b524c; }
        .about-contact-note a{ color: var(--green-darker); font-weight: 600; text-decoration: underline; }
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
        @if($heroPanoramaUrl)
        <div class="hero-image" id="heroPanoFrame">
            <div class="hero-pano-controls">
                <button type="button" id="panoZoomOut" aria-label="Zoom out" title="Zoom out"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/></svg></button>
                <button type="button" id="panoZoomIn" aria-label="Zoom in" title="Zoom in"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg></button>
                <button type="button" id="panoFullscreen" aria-label="View full screen" title="Full screen"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"/></svg></button>
            </div>
            <div class="hero-pano" id="heroPano" role="img" aria-label="360° view of {{ $dormName }}. Drag to look around."></div>
            <div class="hero-pano-badge" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="12" rx="10" ry="4"/><path d="M12 2v20"/></svg>
                360°<span class="pano-badge-long"> · Drag to look around</span>
            </div>
        </div>
        @else
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
        @endif
    </section>

    <section class="stats-bar">
        <h2 class="section-title">Find your room at {{ $dormName ?? 'NEST.PH' }}</h2>
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

    <section class="spot-section" id="how-to-apply" aria-labelledby="spotTitle">
        <div class="spot-inner">
            <div class="spot-intro">
                <h2 id="spotTitle" class="section-title">How to Secure Your Spot</h2>
                <p>From your application to move-in day, here is everything that happens and what you need to do at each step.</p>
                <a href="{{ route('public.apply') }}" class="btn btn-green btn-lg">Apply Now</a>
            </div>
            <div class="spot-list">
                @include('partials.secure-spot-steps', ['current' => 0])
            </div>
        </div>
    </section>

    <section class="why-section">
                <h2 class="section-title">Why Pick Us?</h2>
        <p>Located right at the doorstep of Manila's academic hubs, Pureza Station Dormitory gives students a secure, comfortable, and accessible place to stay. Our management platform, NEST.PH, covers everything from your first virtual room viewing to your everyday tenant needs, so you can focus on school instead of paperwork.</p>
    </section>

    @if($showLocationMap && $mapQuery)
    <section class="location-section" id="location" aria-labelledby="locationTitle">
        <div class="location-inner">
            <div class="location-text">
                <h2 id="locationTitle" class="section-title">Where We Are</h2>
                @if($address)<p class="location-address">{{ $address }}</p>@endif
                @if($locationNote)<p>{{ $locationNote }}</p>@endif
                <div class="location-actions">
                    <a class="btn btn-green" href="https://www.google.com/maps/dir/?api=1&amp;destination={{ urlencode($mapQuery) }}" target="_blank" rel="noopener">Get Directions</a>
                    <a class="btn btn-outline-green" href="https://www.google.com/maps/search/?api=1&amp;query={{ urlencode($mapQuery) }}" target="_blank" rel="noopener">Open in Google Maps</a>
                </div>
            </div>
            <div class="location-map">
                <iframe src="https://www.google.com/maps?q={{ urlencode($mapQuery) }}&amp;z=17&amp;output=embed" title="Map showing the location of {{ $brandDormName }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                <button type="button" class="map-shield" aria-label="Tap to use the map"><span>Tap to use the map</span></button>
                <script>
                (function () {
                    var box = document.currentScript.parentNode;
                    box.querySelector('.map-shield').addEventListener('click', function () { box.classList.add('map-active'); });
                    // Put the cover back once the map scrolls off screen, so it can't grab the next scroll.
                    if ('IntersectionObserver' in window) new IntersectionObserver(function (e) {
                        if (!e[0].isIntersecting) box.classList.remove('map-active');
                    }).observe(box);
                })();
                </script>
            </div>
        </div>
    </section>
    @endif

    <section class="about-section">
        <div class="about-header">
            <h2 class="section-title">About NEST.PH</h2>
            <p class="sub">Powered by NEST.PH, a smarter web app for dormitory living and management.</p>
        </div>

        <div class="feature-grid">
            <div class="feature">
                <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="8" width="20" height="9" rx="4"/><circle cx="8" cy="12.5" r="2"/><circle cx="16" cy="12.5" r="2"/><path d="M12 8V5a3 3 0 00-3-3H7"/></svg></div>
                <p><strong>360&deg; VR Room Viewing</strong>Walk through real rooms and common areas in immersive 360&deg; from any device. See the actual space, layout, and lighting before you commit, with no guesswork and no wasted trips.</p>
            </div>
            <div class="feature">
                <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v6c0 5-3.5 8-7 9-3.5-1-7-4-7-9V6l7-3z"/><path d="M12 8v4M12 15h.01"/></svg></div>
                <p><strong>Smart Delinquency Escalation</strong>Automated reminders and a clear, staged notice process keep accounts on track, giving tenants fair warning and dorm owners a system that runs itself instead of chasing payments by hand.</p>
            </div>
            <div class="feature">
                <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 7v10l9 4 9-4V7"/></svg></div>
                <p><strong>Online Tenant Portal</strong>Manage your stay entirely online. Check your billing statements, upload proof of payment, and track your payment history without lining up at the admin office.</p>
            </div>
            <div class="feature">
                <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></div>
                <p><strong>Digital Maintenance Requests</strong>Got a leaking faucet or a busted lightbulb? Submit a maintenance ticket directly through the NEST.PH portal and track its resolution status in real-time.</p>
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

    @if($heroPanoramaUrl)
    <script>
        // Homepage 360° photo. Turns slowly on its own (unless the visitor
        // prefers reduced motion) and stops once they drag it. Scroll-wheel
        // zoom is off so scrolling over the photo still scrolls the page.
        // Zoom with the + / - buttons (or pinch on phones). Full screen covers
        // the whole box, buttons included; there the scroll wheel zooms too.
        window.addEventListener('DOMContentLoaded', function () {
            const viewer = pannellum.viewer('heroPano', {
                type: 'equirectangular',
                panorama: @json($heroPanoramaUrl),
                autoLoad: true,
                showControls: false,
                mouseZoom: false,
                hfov: 100, minHfov: 40, maxHfov: 120,
                autoRotate: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : -2,
            });
            const frame = document.getElementById('heroPanoFrame');
            const fsBtn = document.getElementById('panoFullscreen');
            const ENTER = fsBtn.innerHTML;
            const EXIT = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 4v5H4M15 4v5h5M9 20v-5H4M15 20v-5h5"/></svg>';

            const zoom = step => { viewer.stopAutoRotate(); viewer.setHfov(viewer.getHfov() + step); };
            document.getElementById('panoZoomIn').addEventListener('click', () => zoom(-15));
            document.getElementById('panoZoomOut').addEventListener('click', () => zoom(15));

            const isFull = () => document.fullscreenElement === frame || frame.classList.contains('is-fullscreen');
            function onFullChange() {
                const full = isFull();
                fsBtn.innerHTML = full ? EXIT : ENTER;
                fsBtn.setAttribute('aria-label', full ? 'Exit full screen' : 'View full screen');
                fsBtn.title = full ? 'Exit full screen' : 'Full screen';
                document.body.style.overflow = frame.classList.contains('is-fullscreen') ? 'hidden' : '';
                setTimeout(() => viewer.resize(), 50);
            }
            fsBtn.addEventListener('click', () => {
                if (isFull()) {
                    if (document.fullscreenElement) document.exitFullscreen(); else frame.classList.remove('is-fullscreen');
                } else if (frame.requestFullscreen) {
                    frame.requestFullscreen().catch(() => frame.classList.add('is-fullscreen')).finally(onFullChange);
                    return;
                } else {
                    frame.classList.add('is-fullscreen');
                }
                onFullChange();
            });
            document.addEventListener('fullscreenchange', onFullChange);
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape' && frame.classList.contains('is-fullscreen')) { frame.classList.remove('is-fullscreen'); onFullChange(); }
            });
            // Scroll-wheel zoom only in full screen, so on the page it still scrolls.
            frame.addEventListener('wheel', e => {
                if (!isFull()) return;
                e.preventDefault();
                zoom(e.deltaY > 0 ? 5 : -5);
            }, { passive: false });
        });
    </script>
    @endif
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

@include('partials.contact-fab')
</body>
</html>