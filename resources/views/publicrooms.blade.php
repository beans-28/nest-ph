<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    {{-- Page-switch fade opt-in: must be inline and first in <head> or Chrome decides before it sees it (see typography.css). --}}
    <style>@view-transition{navigation:auto}</style><link rel="stylesheet" href="/css/motion.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Rooms · {{ $brandDormName }}</title>
    <link rel="icon" href="{{ $brandFaviconUrl }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/typography.css') }}">
    <script src="{{ asset('js/smooth-nav.js') }}" defer></script>
    <style>
        :root {
            --green-light: #a2d9a4;
            --green-dark: #567357;
            --green-darker: #197335;
            --ink: #292420;
            --cream: #dcd8d7;
            --cream-light: #f2f4f8;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }

        .page-wrap { overflow-x: hidden; }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: var(--ink);
            /* Light page: the brand color is an accent, not the whole page. */
            background: var(--bg-page, #f7f5f2);
            min-height: 100vh;
        }

        a:focus-visible, button:focus-visible, input:focus-visible {
            outline: 2px solid var(--green-darker);
            outline-offset: 2px;
        }
        .topnav a:focus-visible, .topnav .buttons a:focus-visible {
            outline: 2px solid #fff;
            outline-offset: 2px;
        }
        .btn-white:focus-visible { outline-color: var(--ink); }

        .textured { position: relative; overflow: hidden; }
        .textured .bg-texture {
            position: absolute; inset: 0; width: 100%; height: 100%;
            object-fit: cover; pointer-events: none; z-index: 0;
            mix-blend-mode: multiply; opacity: 0.5;
        }
        .textured > *:not(.bg-texture) { position: relative; z-index: 1; }

        .topnav {
            background: linear-gradient(90deg, var(--green-darker), var(--green-dark));
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
        .topnav .logo .logo-img { height: 32px; width: auto; }
        .topnav .buttons { flex: 1; display: flex; justify-content: flex-end; gap: 12px; }
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            height: 44px; padding: 0 18px; border: 2px solid #fff;
            font-weight: 500; font-size: 13.5px; letter-spacing: 0.02em; cursor: pointer;
            white-space: nowrap; text-decoration: none;
        }
        .btn-white { background: #fff; color: var(--ink); }
        .btn-outline-white { background: transparent; color: #fff; }

        /* ===== Header banner ===== */
        /* Bed status colors are fixed on purpose: they carry meaning, so they
           don't follow the dorm's brand color (hex values kept out of
           BrandTheme::LITERALS). Open beds are the only strong color. */
        .page-wrap {
            --st-open: #146c35; --st-open-ink: #fff;
            --st-res-bg: #dcc2ee; --st-res-ink: #5a2577; --st-res-line: #a678c6;
            --st-occ-bg: #ebe8e5; --st-occ-ink: #6f6863;
            --st-mnt-a: #fbf1d2; --st-mnt-b: #f2e1a1; --st-mnt-ink: #7d6200;
            --surface: var(--bg-page, #f7f5f2);
        }
        .rooms-header {
            background: var(--surface);
            padding: clamp(28px, 5vw, 44px) clamp(20px, 6vw, 64px) 8px;
            position: relative;
        }
        .back-button {
            background: none; border: none; color: var(--green-darker); font-size: 26px;
            cursor: pointer; line-height: 1; padding: 0; margin-bottom: 16px;
        }
        .rooms-header-text { text-align: center; max-width: 1200px; margin: 0 auto; }
        .rooms-header h1 {
            font-weight: 800; font-size: clamp(26px, 3.4vw, 38px); color: #292420;
            letter-spacing: -0.02em; margin-bottom: 8px;
        }
        .rooms-summary { font-size: 16px; color: #5b524c; margin-bottom: 22px; font-variant-numeric: tabular-nums; }
        .rooms-summary strong { color: var(--st-open); font-weight: 700; }
        .rooms-toolbar { display: flex; justify-content: center; align-items: center; gap: 14px 28px; flex-wrap: wrap; }
        /* Legend: same tiles as the beds, so it reads as a key, not decoration */
        .legend { display: flex; justify-content: center; gap: 8px 20px; flex-wrap: wrap; }
        .legend-item { display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 600; color: #3a332f; }
        .legend-swatch { width: 18px; height: 18px; border-radius: 5px; flex-shrink: 0; box-shadow: inset 0 0 0 1px rgba(41,36,32,0.14); }
        /* Filter: All rooms / Rooms with open beds */
        .room-filter { display: inline-flex; padding: 4px; gap: 4px; border-radius: 999px; background: #fff; border: 1px solid #e6e1dc; }
        .room-filter button {
            border: 0; background: transparent; font: inherit; font-size: 13.5px; font-weight: 600; color: #6b625c;
            padding: 7px 16px; border-radius: 999px; cursor: pointer; transition: background-color .2s ease, color .2s ease;
        }
        .room-filter button[aria-pressed="true"] { background: #292420; color: #fff; }
        .room-filter button:focus-visible { outline: 2px solid #292420; outline-offset: 2px; }

        /* ===== Room/bed status grid ===== */
        .status-section { background: var(--surface); padding: 24px clamp(16px, 4vw, 40px) clamp(32px, 5vw, 56px); }
        .status-grid { max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 32px; }
        .floor-group-title {
            display: flex; align-items: center; gap: 14px; margin-bottom: 14px;
            color: #292420; font-weight: 700; font-size: 18px; letter-spacing: -0.01em;
        }
        .floor-group-title .floor-open { font-size: 13.5px; font-weight: 600; color: #6b625c; }
        .floor-group-title::after { content: ''; flex: 1; height: 1px; background: #e6e1dc; }
        .floor-rooms { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; align-items: start; }
        .status-card {
            background: #fff; border-radius: 16px; padding: 16px 18px 18px;
            border: 1px solid #ebe6e1;
            box-shadow: 0 1px 2px rgba(41,36,32,0.05), 0 8px 20px -14px rgba(41,36,32,0.3);
        }
        .status-card.has-open { border-color: color-mix(in srgb, var(--st-open) 35%, #ebe6e1); }
        .status-card-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; margin-bottom: 14px; }
        .status-card-name { font-weight: 700; font-size: 17px; color: #292420; line-height: 1.25; }
        .status-card-type { display: block; font-weight: 500; font-size: 13px; color: #7a716b; margin-top: 2px; }
        .open-count {
            flex-shrink: 0; font-weight: 700; font-size: 12.5px; padding: 5px 11px; border-radius: 999px;
            background: var(--st-open); color: var(--st-open-ink); font-variant-numeric: tabular-nums;
        }
        .open-count.full { background: var(--st-occ-bg); color: #7a716b; }
        /* Each bed is a numbered tile colored by status */
        .bed-tiles { display: grid; grid-template-columns: repeat(auto-fill, minmax(38px, 1fr)); gap: 6px; list-style: none; padding: 0; margin: 0; }
        .bed-tile {
            height: 38px; border-radius: 9px; display: flex; align-items: center; justify-content: center;
            font-size: 13.5px; font-weight: 700; font-variant-numeric: tabular-nums;
        }
        .st-vacant { background: var(--st-open); color: var(--st-open-ink); }
        .st-reserved { background: var(--st-res-bg); color: var(--st-res-ink); box-shadow: inset 0 0 0 1px var(--st-res-line); }
        .st-occupied { background: var(--st-occ-bg); color: var(--st-occ-ink); font-weight: 600; }
        .st-maintenance { background: repeating-linear-gradient(135deg, var(--st-mnt-a) 0 6px, var(--st-mnt-b) 6px 12px); color: var(--st-mnt-ink); }
        .status-grid.only-open .status-card:not(.has-open) { display: none; }
        .status-grid.only-open section:not(:has(.has-open)) { display: none; }
        ::selection { background: #a2d9a4; color: #292420; }
        .empty-note { color: #6b625c; font-size: 14px; text-align: center; padding: 24px; grid-column: 1 / -1; }

        /* ===== VR Tour CTA ===== */
        .vr-cta-wrap { display: flex; justify-content: center; padding: clamp(22px, 3vw, 32px) 20px; }
        .vr-cta {
            background: #faffff; color: #63856a; font-weight: 500; font-size: clamp(15px, 1.8vw, 19px);
            letter-spacing: 0.02em; text-transform: uppercase; padding: 12px 32px;
            border-radius: 16px; text-decoration: none; text-align: center;
        }
        .banner-wrap { padding: 0 clamp(16px, 4vw, 40px); }

        /* ===== Room listing cards ===== */
        .listing-section { padding: 0 clamp(16px, 4vw, 40px) clamp(32px, 5vw, 48px); }
        .listing-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 380px));
            gap: 22px; max-width: 1100px; margin: 0 auto; justify-content: center;
        }
        .listing-card {
            background: #d9d9d9; border-radius: 18px; overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.25); position: relative;
        }
        .listing-photo {
            width: 100%; aspect-ratio: 16/10; object-fit: cover; display: block;
            background: linear-gradient(135deg, #e7e5e5, #818080);
        }
        button.listing-photo-btn { display: block; width: 100%; padding: 0; border: none; background: none; cursor: zoom-in; position: relative; }
        .listing-photo-btn:hover .listing-photo { filter: brightness(1.05); }
        .photo-count {
            position: absolute; left: 12px; bottom: 12px; background: rgba(20,20,15,0.65); color: #fff;
            font-size: 11px; font-weight: 700; padding: 4px 9px; border-radius: 999px; font-variant-numeric: tabular-nums;
        }

        /* ===== Photo viewer (click a listing photo to enlarge) ===== */
        .lightbox[hidden] { display: none; }
        .lightbox {
            position: fixed; inset: 0; z-index: 2000; background: rgba(15,18,14,0.92);
            display: flex; align-items: center; justify-content: center; padding: 56px 72px;
        }
        .lightbox img { max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 6px; box-shadow: 0 10px 40px rgba(0,0,0,0.5); }
        .lightbox button {
            position: absolute; width: 44px; height: 44px; border-radius: 50%; border: none; cursor: pointer;
            background: rgba(255,255,255,0.14); color: #fff; display: flex; align-items: center; justify-content: center;
        }
        .lightbox button[hidden] { display: none; }
        .lightbox button:hover { background: rgba(255,255,255,0.26); }
        .lightbox button:focus-visible { outline: 2px solid #fff; outline-offset: 2px; }
        .lightbox button svg { width: 20px; height: 20px; stroke: currentColor; fill: none; stroke-width: 2.2; }
        .lb-close { top: 14px; right: 14px; }
        .lb-prev { left: 14px; top: 50%; transform: translateY(-50%); }
        .lb-next { right: 14px; top: 50%; transform: translateY(-50%); }
        .lb-caption { position: absolute; bottom: 16px; left: 0; right: 0; text-align: center; color: #e8ebe6; font-size: 13px; font-variant-numeric: tabular-nums; }
        @media (max-width: 640px) {
            .lightbox { padding: 64px 12px; }
            .lb-prev, .lb-next { top: auto; bottom: 8px; transform: none; }
        }

        .listing-body { padding: 14px 16px 16px; }
        .listing-type { color: #292420; font-weight: 500; font-size: 14px; margin-bottom: 2px; }
        .listing-price { color: #355e3d; font-weight: 700; font-size: 18px; margin-bottom: 2px; font-variant-numeric: tabular-nums; }
        .listing-unit { font-size: 12px; font-weight: 500; color: #567357; }
        .listing-pricing-note { font-size: 12px; color: #5c5c5c; margin-bottom: 4px; }
        .listing-price-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 2px; }
        .listing-price-row .listing-price { margin-bottom: 0; }
        .pricing-tag { font-size: 11.5px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; padding: 3px 8px; border-radius: 999px; line-height: 1.3; }
        .pricing-tag.per-bed { background: #e3efe3; color: #2d5a35; }
        .pricing-tag.per-room { background: #355e3d; color: #fff; }
        .listing-size { font-size: 13px; color: #292420; margin-bottom: 8px; }
        .listing-size strong { font-weight: 600; }
        .listing-room-label { font-size: 11px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; color: #6b6b6b; margin-bottom: 2px; }
        .listing-amenities { display: flex; flex-wrap: wrap; gap: 3px 14px; margin-bottom: 13px; }
        .listing-amenities span { font-size: 10px; font-weight: 700; letter-spacing: 0.03em; color: #292420; text-transform: uppercase; }
        .listing-buttons { display: flex; gap: 8px; flex-wrap: wrap; }
        .listing-buttons a, .listing-buttons button {
            flex: 1; min-width: 72px; background: #1d4f27; color: #fff; border: none;
            border-radius: 8px; padding: 9px 6px; font-weight: 500; font-size: 10.5px;
            letter-spacing: 0.03em; text-transform: uppercase; text-align: center;
            text-decoration: none; cursor: pointer; font-family: inherit;
        }
        .listing-buttons a.disabled { opacity: 0.45; pointer-events: none; }
        .no-rooms { color: #fff; text-align: center; padding: 40px 20px; font-size: 14px; grid-column: 1 / -1; }

        /* ===== Bottom VR tour banner ===== */
        .tour-banner {
            position: relative; width: 100%; max-width: 1100px; margin: 0 auto;
            border-radius: 20px; overflow: hidden;
            aspect-ratio: 21 / 9; display: flex; align-items: flex-end;
            justify-content: center; padding-bottom: 28px;
            background: linear-gradient(135deg, #4f483c, #3f533f);
            background-size: cover; background-position: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.25);
        }
        .tour-banner::before {
            content: ''; position: absolute; inset: 0; background: rgba(20,20,15,0.35);
        }
        .tour-banner .btn-nav {
            position: relative; z-index: 1; background: #fff; color: #292420; font-weight: 700;
            font-size: 14px; letter-spacing: 0.05em; padding: 12px 38px; border-radius: 8px;
            text-decoration: none;
        }
        .banner-footer-spacer { height: clamp(24px, 4vw, 40px); }

        /* ===== Room details (listing cards), light version ===== */
        .listing-head { max-width: 1200px; margin: 0 auto; padding: clamp(40px, 6vw, 64px) 0 22px; border-top: 1px solid #e6e1dc; display: flex; justify-content: space-between; align-items: flex-end; gap: 16px; flex-wrap: wrap; }
        /* Default: only rooms with open beds; "All rooms" shows the rest. */
        .listing-grid.only-open .listing-card.is-full { display: none; }
        .listing-head h2 { font-size: clamp(24px, 2.8vw, 32px); font-weight: 800; letter-spacing: -0.02em; color: #292420; margin-bottom: 6px; }
        .listing-head p { font-size: 16px; color: #5b524c; }
        .listing-grid { grid-template-columns: repeat(auto-fill, minmax(min(100%, 320px), 1fr)); max-width: 1200px; gap: 20px; }
        .listing-card {
            background: #fff; border-radius: 18px; border: 1px solid #ebe6e1; display: flex; flex-direction: column;
            box-shadow: 0 1px 2px rgba(41,36,32,0.05), 0 10px 24px -16px rgba(41,36,32,0.35);
        }
        .listing-media { position: relative; }
        .listing-photo { background: color-mix(in srgb, var(--green-dark) 8%, #f1eeea); }
        .listing-photo.empty { display: flex; align-items: center; justify-content: center; color: #6b625c; font-size: 13.5px; font-weight: 600; }
        .listing-open {
            position: absolute; top: 12px; left: 12px; z-index: 2; padding: 5px 11px; border-radius: 999px;
            font-size: 12.5px; font-weight: 700; font-variant-numeric: tabular-nums;
            background: var(--st-open); color: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .listing-open.full { background: rgba(255,255,255,0.92); color: #6b625c; }
        .listing-tour {
            position: absolute; right: 12px; bottom: 12px; z-index: 2; display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 12px; border-radius: 999px; background: rgba(22,21,20,0.72); color: #fff;
            font-size: 12.5px; font-weight: 600; text-decoration: none; backdrop-filter: blur(6px);
            transition: background-color .2s ease;
        }
        .listing-tour:hover { background: rgba(22,21,20,0.88); }
        .listing-tour svg { width: 15px; height: 15px; }
        .listing-body { padding: 16px 18px 18px; display: flex; flex-direction: column; flex: 1; }
        .listing-room-label { font-size: 12.5px; font-weight: 600; letter-spacing: 0; text-transform: none; color: #7a716b; margin-bottom: 2px; }
        .listing-type { font-size: 17px; font-weight: 700; color: #292420; margin-bottom: 8px; letter-spacing: -0.01em; }
        .listing-price { color: #292420; font-size: 22px; font-weight: 800; letter-spacing: -0.02em; }
        .listing-unit { color: #7a716b; font-size: 13px; font-weight: 500; letter-spacing: 0; margin-left: 2px; }
        .pricing-tag.per-bed { background: #f1eeea; color: #5b524c; }
        .pricing-tag.per-room { background: #292420; color: #fff; }
        .listing-size { color: #5b524c; font-size: 14px; margin: 4px 0 12px; }
        .listing-amenities { gap: 6px; margin-bottom: 16px; }
        .listing-amenities span {
            font-size: 12.5px; font-weight: 500; letter-spacing: 0; text-transform: none; color: #4a423d;
            padding: 4px 10px; border-radius: 999px; background: #f4f1ee;
        }
        .listing-buttons { margin-top: auto; display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .listing-buttons a {
            display: inline-flex; align-items: center; justify-content: center; height: 42px; padding: 0 12px;
            border-radius: 10px; font-size: 14px; font-weight: 600; letter-spacing: 0; text-transform: none;
            transition: background-color .2s ease, border-color .2s ease;
        }
        .listing-buttons a.btn-green { background: var(--green-dark); color: #fff; border: 1px solid var(--green-dark); }
        .listing-buttons a.btn-green:hover { background: var(--green-darker); border-color: var(--green-darker); }
        .listing-buttons a.lc-ghost { background: #fff; color: #292420; border: 1px solid #ddd6cf; }
        .listing-buttons a.lc-ghost:hover { border-color: #292420; }
        .no-rooms { color: #6b625c; }
        .tour-banner { border-radius: 22px; box-shadow: 0 1px 2px rgba(41,36,32,0.05), 0 18px 40px -22px rgba(41,36,32,0.45); }
        .tour-banner .btn-nav { border-radius: 10px; letter-spacing: 0; font-size: 15px; padding: 13px 30px; }

        @media (max-width: 1024px) {
            .floor-rooms { grid-template-columns: repeat(2, 1fr); }
            .listing-grid { grid-template-columns: 1fr; }
            .topnav { padding: 14px 24px; flex-wrap: wrap; }
        }
        @media (max-width: 640px) {
            .floor-rooms { grid-template-columns: 1fr; }
            .legend { gap: 16px; }

            /* Topnav: the flex-wrap layout at wider breakpoints packs the
               menu, logo and button groups unpredictably at phone widths,
               so stack them into clear rows instead. Matches welcome.blade.php. */
            .topnav { flex-direction: column; align-items: stretch; gap: 12px; padding-top: 14px; padding-bottom: 14px; }
            .topnav .logo { order: -1; justify-content: center; }
            .topnav .menu { flex: none; justify-content: center; flex-wrap: wrap; row-gap: 8px; }
            .topnav .menu a, .topnav .menu span { padding: 10px 8px; }
            .topnav .buttons { flex: none; justify-content: center; flex-wrap: wrap; row-gap: 10px; }
        }

        /* Tap targets on room listing cards were under the ~44px touch
           minimum on small phones. */
        @media (max-width: 768px) {
            .rooms-header { padding: 24px 20px 28px; }

            .listing-buttons { gap: 10px; }
            .listing-buttons a, .listing-buttons button {
                min-height: 44px;
                padding: 9px 6px;
            }

            .vr-cta, .tour-banner .btn-nav {
                min-height: 44px;
                display: inline-flex;
                align-items: center;
            }
        }

    </style>
</head>
<body>

    @include('partials.public-nav')

    <div class="page-wrap">

        <div class="rooms-header">
            <button class="back-button" type="button" aria-label="Go back" onclick="window.location.href='{{ route('home') }}'">←</button>
            <div class="rooms-header-text">
                <h1>Available Rooms</h1>
                <p class="rooms-summary" id="roomsSummary">&nbsp;</p>
                <div class="rooms-toolbar">
                    <div class="legend" aria-label="Bed status key">
                        <div class="legend-item"><span class="legend-swatch st-vacant"></span> Available</div>
                        <div class="legend-item"><span class="legend-swatch st-reserved"></span> Reserved</div>
                        <div class="legend-item"><span class="legend-swatch st-occupied"></span> Occupied</div>
                        <div class="legend-item"><span class="legend-swatch st-maintenance"></span> Maintenance</div>
                    </div>
                    <div class="room-filter" id="statusFilter" role="group" aria-label="Show rooms">
                        <button type="button" data-filter="open" aria-pressed="true">With open beds</button>
                        <button type="button" data-filter="all" aria-pressed="false">All rooms</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="status-section">
            <div class="status-grid" id="statusGrid" aria-live="polite">
                <div class="empty-note">Loading rooms…</div>
            </div>
        </div>

        <div class="listing-section">
            <div class="listing-head">
                <div>
                    <h2>Room details</h2>
                    <p>Prices, room sizes and what's included. Tap a photo to see more, or take the 360° tour.</p>
                </div>
                <div class="room-filter" id="listingFilter" role="group" aria-label="Show room details for">
                    <button type="button" data-filter="open" aria-pressed="true">With open beds</button>
                    <button type="button" data-filter="all" aria-pressed="false">All rooms</button>
                </div>
            </div>
            <div class="listing-grid" id="listingGrid" aria-live="polite">
                <div class="no-rooms">Loading listings…</div>
            </div>
        </div>

        <div class="banner-wrap">
            <div class="tour-banner" id="tourBanner">
                <a href="{{ route('public.vr') }}" class="btn-nav">START TOUR</a>
            </div>
        </div>
        <div class="banner-footer-spacer"></div>

    </div>

    <div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Room photos" hidden>
        <img alt="">
        <button type="button" class="lb-close" aria-label="Close"><svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></svg></button>
        <button type="button" class="lb-prev" aria-label="Previous photo"><svg viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg></button>
        <button type="button" class="lb-next" aria-label="Next photo"><svg viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg></button>
        <div class="lb-caption"></div>
    </div>

    <script>
        // Bed status → legend dot color mapping. "reserved" is now a genuine
        // status (an applicant has claimed the bed pending admin review), not
        // an approximation of anything else.
        const STATUS_LABELS = { vacant: 'Available', occupied: 'Occupied', reserved: 'Reserved', maintenance: 'Maintenance' };

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str ?? '';
            return div.innerHTML;
        }

        function renderStatusGrid(rooms) {
            const grid = document.getElementById('statusGrid');

            if (rooms.length === 0) {
                grid.innerHTML = '<div class="empty-note">No rooms have been added yet.</div>';
                return;
            }

            // Rooms arrive already sorted Floor 1 → 5 (see the fetch below).
            const sorted = rooms;
            const openOf = room => room.beds.filter(b => b.status === 'vacant').length;

            // One-line summary above the key
            const openBeds = sorted.reduce((n, r) => n + openOf(r), 0);
            const openRooms = sorted.filter(r => openOf(r) > 0).length;
            const plural = (n, w) => n + ' ' + w + (n === 1 ? '' : 's');
            document.getElementById('roomsSummary').innerHTML = openBeds > 0
                ? `<strong>${plural(openBeds, 'bed')} open</strong> in ${plural(openRooms, 'room')}`
                : 'No beds are open right now. Check back soon, or send an inquiry.';
            // Default to rooms with open beds; if none are open, show every room.
            setStatusFilter(openBeds > 0 ? 'open' : 'all');
            document.getElementById('statusFilter').hidden = openBeds === 0;

            const floors = [];
            sorted.forEach(room => {
                let group = floors[floors.length - 1];
                if (!group || group.floor !== room.floor) {
                    group = { floor: room.floor, label: room.floor_label, rooms: [] };
                    floors.push(group);
                }
                group.rooms.push(room);
            });

            const cardHtml = room => {
                const open = openOf(room);
                const tiles = room.beds.map(bed => {
                    const status = STATUS_LABELS[bed.status] || bed.status;
                    // "Bed 3" -> "3" on the tile; the full name is in the label.
                    const num = String(bed.label).replace(/^bed\s*/i, '');
                    return `<li class="bed-tile st-${escapeHtml(bed.status)}" title="${escapeHtml(bed.label)}: ${status}" aria-label="${escapeHtml(bed.label)}: ${status}">${escapeHtml(num)}</li>`;
                }).join('');
                return `
                    <article class="status-card ${open > 0 ? 'has-open' : ''}">
                        <div class="status-card-head">
                            <h3 class="status-card-name">Room ${escapeHtml(room.room_no)}${room.room_type ? `<span class="status-card-type">${escapeHtml(room.room_type)}</span>` : ''}</h3>
                            <span class="open-count ${open === 0 ? 'full' : ''}">${open === 0 ? 'Full' : `${open} of ${room.beds.length} open`}</span>
                        </div>
                        <ul class="bed-tiles" aria-label="Beds in Room ${escapeHtml(room.room_no)}">${tiles}</ul>
                    </article>
                `;
            };

            grid.innerHTML = floors.map(group => {
                const floorOpen = group.rooms.reduce((n, r) => n + openOf(r), 0);
                return `
                <section>
                    <h2 class="floor-group-title">${escapeHtml(group.label || 'Other')} <span class="floor-open">${floorOpen > 0 ? plural(floorOpen, 'bed') + ' open' : 'Full'}</span></h2>
                    <div class="floor-rooms">${group.rooms.map(cardHtml).join('')}</div>
                </section>`;
            }).join('');
        }

        // Bed-status filter (top): starts on "With open beds"
        function setStatusFilter(mode) {
            document.querySelectorAll('#statusFilter button').forEach(b => b.setAttribute('aria-pressed', String(b.dataset.filter === mode)));
            document.getElementById('statusGrid').classList.toggle('only-open', mode === 'open');
        }
        document.querySelectorAll('#statusFilter button').forEach(btn => btn.addEventListener('click', () => setStatusFilter(btn.dataset.filter)));

        // Room details filter: starts on "With open beds"
        function setListingFilter(mode) {
            document.querySelectorAll('#listingFilter button').forEach(b => b.setAttribute('aria-pressed', String(b.dataset.filter === mode)));
            document.getElementById('listingGrid').classList.toggle('only-open', mode === 'open');
        }
        document.querySelectorAll('#listingFilter button').forEach(btn => btn.addEventListener('click', () => setListingFilter(btn.dataset.filter)));

        function formatPrice(rate) {
            const number = parseFloat(rate);
            if (isNaN(number)) return '';
            return '₱' + number.toLocaleString('en-PH', { minimumFractionDigits: 0 });
        }

        function renderListingGrid(rooms) {
            const grid = document.getElementById('listingGrid');

            if (rooms.length === 0) {
                grid.innerHTML = '<div class="no-rooms">No room listings available right now.</div>';
                return;
            }

            grid.innerHTML = rooms.map(room => {
                const typeLabel = room.room_type
                    ? room.room_type.charAt(0).toUpperCase() + room.room_type.slice(1)
                    : 'Room';

                const amenitiesHtml = (room.amenities || [])
                    .map(a => `<span>${escapeHtml(a)}</span>`)
                    .join('');
                const open = Number(room.available_beds) || 0;

                // Regular listing photos open in the viewer; a room with only a
                // 360 panorama keeps it as a plain (non-clickable) thumbnail.
                const gallery = room.photo_urls || [];
                const photoHtml = gallery.length
                    ? `<button type="button" class="listing-photo-btn" data-gallery="${room.id}" aria-label="View photos of Room ${escapeHtml(room.room_no)}">
                           <img src="${gallery[0]}" class="listing-photo" alt="" loading="lazy" decoding="async">
                           ${gallery.length > 1 ? `<span class="photo-count">${gallery.length} photos</span>` : ''}
                       </button>`
                    : room.photo_url
                        ? `<img src="${room.photo_url}" class="listing-photo" alt="Room ${escapeHtml(room.room_no)}" loading="lazy" decoding="async">`
                        : `<div class="listing-photo empty">No photos yet</div>`;

                // Tour button only when the room actually has a public tour.
                const tourButton = room.has_vr_tour
                    ? `<a href="{{ route('public.vr') }}" class="listing-tour"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><ellipse cx="12" cy="12" rx="10" ry="4"/><path d="M12 2v20"/></svg>360° tour</a>`
                    : '';

                return `
                    <div class="listing-card ${open === 0 ? 'is-full' : ''}">
                        <div class="listing-media">
                        ${photoHtml}
                        <span class="listing-open ${open === 0 ? 'full' : ''}">${open === 0 ? 'Full' : (open === 1 ? '1 bed open' : open + ' beds open')}</span>
                        ${tourButton}
                        </div>
                        <div class="listing-body">
                            <div class="listing-room-label">Room ${escapeHtml(room.room_no)}</div>
                            <div class="listing-type">${typeLabel}</div>
                            <div class="listing-price-row">
                                ${room.pricing_mode === 'per_room'
                                    ? `<div class="listing-price">${formatPrice(room.whole_room_rate)}<span class="listing-unit">/room/mo</span></div><span class="pricing-tag per-room">Whole room</span>`
                                    : `<div class="listing-price">${formatPrice(room.price_per_bed)}<span class="listing-unit">/bed/mo</span></div><span class="pricing-tag per-bed">Per bed</span>`}
                            </div>
                            ${room.pricing_mode === 'per_room' && room.bed_count > 1 ? `<div class="listing-pricing-note">${formatPrice(room.price_per_bed)} each when shared by ${room.bed_count}</div>` : ''}
                            ${room.area_sqm ? `<div class="listing-size">Room size: <strong>${Number(room.area_sqm)} m²</strong></div>` : ''}
                            <div class="listing-amenities">${amenitiesHtml}</div>
                            <div class="listing-buttons">
                                <a href="{{ route('public.apply') }}" class="btn-green">Apply</a>
                                <a href="{{ route('public.inquiry') }}?room_id=${room.id}&room_type=${encodeURIComponent(room.room_type || '')}" class="lc-ghost">Inquire</a>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            // Default to rooms with open beds; if none are open, show everything.
            const anyOpen = rooms.some(r => Number(r.available_beds) > 0);
            setListingFilter(anyOpen ? 'open' : 'all');
            document.getElementById('listingFilter').hidden = !anyOpen;

            grid.querySelectorAll('[data-gallery]').forEach(btn => {
                const room = rooms.find(r => r.id === Number(btn.dataset.gallery));
                btn.addEventListener('click', () => openLightbox(room, btn));
            });
        }

        // ===== Photo viewer =====
        const lightbox = document.getElementById('lightbox');
        const lb = { photos: [], index: 0, roomNo: '', returnFocus: null };

        function openLightbox(room, trigger) {
            lb.photos = room.photo_urls;
            lb.roomNo = room.room_no;
            lb.index = 0;
            lb.returnFocus = trigger;
            const many = lb.photos.length > 1;
            lightbox.querySelector('.lb-prev').hidden = !many;
            lightbox.querySelector('.lb-next').hidden = !many;
            showLightboxPhoto();
            lightbox.hidden = false;
            document.body.style.overflow = 'hidden';
            lightbox.querySelector('.lb-close').focus();
        }

        function showLightboxPhoto() {
            const img = lightbox.querySelector('img');
            img.src = lb.photos[lb.index];
            img.alt = `Room ${lb.roomNo}, photo ${lb.index + 1} of ${lb.photos.length}`;
            lightbox.querySelector('.lb-caption').textContent =
                `Room ${lb.roomNo}` + (lb.photos.length > 1 ? ` · ${lb.index + 1} / ${lb.photos.length}` : '');
        }

        function stepLightbox(delta) {
            lb.index = (lb.index + delta + lb.photos.length) % lb.photos.length;
            showLightboxPhoto();
        }

        function closeLightbox() {
            lightbox.hidden = true;
            document.body.style.overflow = '';
            lb.returnFocus?.focus();
        }

        // Phones: swipe left/right to move between photos.
        let touchStartX = null;
        lightbox.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
        lightbox.addEventListener('touchend', e => {
            if (touchStartX === null || lb.photos.length < 2) return;
            const dx = e.changedTouches[0].clientX - touchStartX;
            touchStartX = null;
            if (Math.abs(dx) > 50) stepLightbox(dx < 0 ? 1 : -1);
        });

        lightbox.querySelector('.lb-close').addEventListener('click', closeLightbox);
        lightbox.querySelector('.lb-prev').addEventListener('click', () => stepLightbox(-1));
        lightbox.querySelector('.lb-next').addEventListener('click', () => stepLightbox(1));
        // Clicking the dark backdrop (not the photo or buttons) closes it.
        lightbox.addEventListener('click', e => { if (e.target === lightbox) closeLightbox(); });
        document.addEventListener('keydown', e => {
            if (lightbox.hidden) return;
            if (e.key === 'Escape') closeLightbox();
            else if (e.key === 'ArrowLeft' && lb.photos.length > 1) stepLightbox(-1);
            else if (e.key === 'ArrowRight' && lb.photos.length > 1) stepLightbox(1);
            else if (e.key === 'Tab') {
                // Keep keyboard focus inside the viewer while it's open.
                const buttons = [...lightbox.querySelectorAll('button:not([hidden])')];
                const i = buttons.indexOf(document.activeElement);
                e.preventDefault();
                buttons[(i + (e.shiftKey ? -1 : 1) + buttons.length) % buttons.length].focus();
            }
        });

        function setTourBanner(rooms) {
            const withPhoto = rooms.find(r => r.photo_url);
            if (withPhoto) {
                document.getElementById('tourBanner').style.backgroundImage = `url('${withPhoto.photo_url}')`;
            }
        }

        fetch('/public-api/rooms')
            .then(r => r.json())
            .then(rooms => {
                rooms.sort((a, b) =>
                    (a.floor ?? 99) - (b.floor ?? 99) ||
                    String(a.room_no).localeCompare(String(b.room_no), undefined, { numeric: true })
                );
                renderStatusGrid(rooms);
                renderListingGrid(rooms);
                setTourBanner(rooms);
            })
            .catch(() => {
                document.getElementById('statusGrid').innerHTML = '<div class="empty-note">Could not load rooms right now.</div>';
                document.getElementById('listingGrid').innerHTML = '<div class="no-rooms">Could not load listings right now.</div>';
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

@include('partials.contact-fab')
</body>
</html>