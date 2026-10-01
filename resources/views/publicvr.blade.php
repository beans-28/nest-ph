<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VR Room Viewing · {{ $brandDormName }}</title>
    <link rel="icon" href="{{ $brandFaviconUrl }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css">
    <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
    <style>
        :root {
            --green-light: #a2d9a4;
            --green-dark: #567357;
            --green-darker: #197335;
            --ink: #292420;
            --mint: #92db9f;
            --glass: rgba(20, 24, 21, 0.62);
            --glass-text: #f3f1ec;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }

        /* The whole page is locked to the viewport — no page scrolling. The
           panorama fills everything under the nav. */
        html, body { height: 100%; overflow: hidden; }

        body {
            font-family: 'Roboto', system-ui, -apple-system, sans-serif;
            color: var(--ink);
            background: #141714;
            display: flex;
            flex-direction: column;
        }

        a:focus-visible, button:focus-visible {
            outline: 2px solid var(--mint);
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
            padding: 10px clamp(20px, 5vw, 64px);
            display: flex; align-items: center; gap: clamp(16px, 3vw, 40px);
            flex-shrink: 0;
        }
        .topnav .menu { flex: 1; display: flex; align-items: center; gap: 10px; }
        .topnav .menu a {
            color: #fff; font-weight: 500; font-size: 14px;
            padding: 10px 6px; display: inline-flex; align-items: center; gap: 4px;
            text-decoration: none;
        }
        .topnav .menu a.pill { border: 1px solid rgba(255,255,255,0.5); border-radius: 999px; padding: 7px 16px; }
        .topnav .logo {
            display: flex; align-items: center; gap: 6px; color: #fff; font-weight: 700;
            font-size: 19px; letter-spacing: 0.02em; white-space: nowrap;
        }
        .topnav .logo .logo-img { height: 32px; width: auto; }
        .topnav .buttons { flex: 1; display: flex; justify-content: flex-end; gap: 12px; }
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            height: 40px; padding: 0 18px; border: 2px solid #fff;
            font-weight: 500; font-size: 13.5px; cursor: pointer;
            white-space: nowrap; text-decoration: none;
        }
        .btn-white { background: #fff; color: var(--ink); }
        .btn-outline-white { background: transparent; color: #fff; }

        /* ===== Immersive stage =====
           The panorama fills everything below the nav. All other UI floats
           on top of it as small see-through panels, so the room itself is
           the page instead of a box squeezed between bars. */
        .stage { position: relative; flex: 1; min-height: 0; background: #141714; overflow: hidden; }
        .stage:fullscreen { width: 100vw; height: 100vh; }
        #panorama { position: absolute; inset: 0; }

        /* Soft dark fades at the top and bottom so the floating text stays
           readable on bright walls and ceilings. */
        .stage::before, .stage::after {
            content: ''; position: absolute; left: 0; right: 0; z-index: 2; pointer-events: none;
        }
        .stage::before { top: 0; height: 140px; background: linear-gradient(rgba(10,12,10,0.5), transparent); }
        .stage::after { bottom: 0; height: 190px; background: linear-gradient(transparent, rgba(10,12,10,0.55)); }

        .glass {
            background: var(--glass);
            -webkit-backdrop-filter: blur(14px) saturate(1.2);
            backdrop-filter: blur(14px) saturate(1.2);
            border: 1px solid rgba(255,255,255,0.12);
            color: var(--glass-text);
            box-shadow: 0 10px 30px rgba(0,0,0,0.28);
        }
        .overlay { position: absolute; z-index: 5; }

        /* Room info card (top-left) */
        .info-card {
            top: 18px; left: 18px; width: min(340px, calc(100% - 100px));
            border-radius: 16px; padding: 16px 18px;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .info-card.is-hidden { opacity: 0; transform: translateY(-8px); pointer-events: none; }
        .info-eyebrow {
            font-size: 11px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;
            color: var(--mint); margin-bottom: 4px;
        }
        .info-title { font-size: 26px; font-weight: 900; line-height: 1.1; letter-spacing: -0.01em; }
        .info-meta { margin-top: 6px; font-size: 14px; color: rgba(243,241,236,0.82); }
        .info-meta strong { color: #fff; font-weight: 700; }
        .info-caption {
            margin-top: 10px; font-size: 13px; line-height: 1.5; color: rgba(243,241,236,0.75);
            display: -webkit-box; -webkit-line-clamp: 3; line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
        }
        .info-updated { margin-top: 6px; font-size: 12px; color: rgba(243,241,236,0.65); }
        .info-updated:empty { display: none; }
        .info-actions { display: flex; gap: 8px; margin-top: 14px; }
        .info-actions a {
            flex: 1; display: inline-flex; align-items: center; justify-content: center;
            height: 40px; border-radius: 10px; font-size: 13.5px; font-weight: 700; text-decoration: none;
            transition: background 0.2s ease;
        }
        .info-actions .primary { background: var(--mint); color: #10301a; }
        .info-actions .primary:hover { background: #a9e6b4; }
        .info-actions .ghost { border: 1px solid rgba(255,255,255,0.3); color: #fff; }
        .info-actions .ghost:hover { background: rgba(255,255,255,0.1); }

        /* Look-around controls (right edge) */
        .controls {
            top: 18px; right: 18px; display: flex; flex-direction: column;
            border-radius: 14px; padding: 5px; gap: 2px;
        }
        .ctrl {
            width: 42px; height: 42px; border: 0; border-radius: 10px; background: transparent;
            color: var(--glass-text); cursor: pointer; display: grid; place-items: center;
            transition: background 0.2s ease;
        }
        .ctrl:hover { background: rgba(255,255,255,0.12); }
        .ctrl[aria-pressed="true"] { background: rgba(146,219,159,0.22); color: var(--mint); }
        .ctrl svg { width: 20px; height: 20px; }
        .ctrl-sep { height: 1px; margin: 3px 6px; background: rgba(255,255,255,0.14); }

        /* "Drag to look around" hint (centre, fades after first touch) */
        .hint {
            top: 50%; left: 50%; transform: translate(-50%, -50%);
            display: flex; align-items: center; gap: 10px; padding: 12px 18px; border-radius: 999px;
            font-size: 14px; font-weight: 500; pointer-events: none; white-space: nowrap;
            transition: opacity 0.6s ease;
        }
        .hint svg { width: 22px; height: 22px; animation: sway 2.4s ease-in-out infinite; }
        .hint.gone { opacity: 0; }
        @keyframes sway { 0%,100% { transform: translateX(-5px); } 50% { transform: translateX(5px); } }

        /* Bottom area: spots in this room + room dock */
        .bottom {
            left: 0; right: 0; bottom: 0; padding: 0 18px 16px;
            display: flex; flex-direction: column; align-items: center; gap: 10px;
            pointer-events: none;
        }
        .bottom > * { pointer-events: auto; }

        .spots { display: flex; gap: 6px; padding: 5px; border-radius: 999px; max-width: 100%; overflow-x: auto; scrollbar-width: none; }
        .spots::-webkit-scrollbar { display: none; }
        .spots[hidden] { display: none; }
        .spot {
            border: 0; background: transparent; color: rgba(243,241,236,0.8); font: inherit;
            font-size: 13px; font-weight: 500; padding: 8px 14px; border-radius: 999px;
            cursor: pointer; white-space: nowrap; transition: background 0.2s ease, color 0.2s ease;
        }
        .spot:hover { background: rgba(255,255,255,0.1); color: #fff; }
        .spot.active { background: #fff; color: #1b221c; font-weight: 700; }

        .dock { border-radius: 18px; padding: 8px; max-width: 100%; display: flex; align-items: center; gap: 8px; }
        .dock-toggle {
            flex-shrink: 0; border: 0; background: transparent; color: var(--glass-text); font: inherit;
            font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;
            padding: 0 10px; height: 64px; cursor: pointer; border-radius: 12px;
            display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px;
        }
        .dock-toggle:hover { background: rgba(255,255,255,0.08); }
        .dock-toggle { line-height: 1.2; text-align: center; }
        .dock.collapsed .dock-toggle br { display: none; }
        .dock-toggle svg { width: 16px; height: 16px; transition: transform 0.25s ease; }
        .dock.collapsed .dock-toggle { height: 36px; flex-direction: row; gap: 6px; }
        .dock.collapsed .dock-toggle svg { transform: rotate(180deg); }
        .dock.collapsed .rooms-scroll { display: none; }

        .rooms-scroll {
            display: flex; gap: 8px; overflow-x: auto; scroll-snap-type: x mandatory;
            scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.25) transparent;
        }
        .room-thumb {
            position: relative; flex: 0 0 112px; height: 64px; border-radius: 11px;
            overflow: hidden; cursor: pointer; scroll-snap-align: start; padding: 0;
            background: #3f4a3f; border: 2px solid transparent; font-family: inherit;
            transition: border-color 0.2s ease, transform 0.2s ease;
            animation: thumbIn 0.4s ease backwards;
        }
        .room-thumb:hover { transform: translateY(-2px); }
        .room-thumb.active { border-color: var(--mint); }
        .room-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; filter: brightness(0.85); }
        .room-thumb::after { content: ''; position: absolute; inset: 0; background: linear-gradient(transparent 35%, rgba(0,0,0,0.7)); }
        .room-thumb .label {
            position: absolute; left: 8px; bottom: 6px; z-index: 1; color: #fff;
            font-weight: 700; font-size: 13px; text-align: left; line-height: 1.1;
        }
        .room-thumb .label small { display: block; font-size: 10px; font-weight: 500; opacity: 0.85; }
        .strip-empty { color: #ddd8d7; font-size: 12px; padding: 12px; }

        @keyframes thumbIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }

        .viewer-message {
            position: absolute; inset: 0; z-index: 4; display: flex; align-items: center; justify-content: center;
            color: #e6e6e2; font-size: 15px; text-align: center; padding: 24px;
        }

        /* Pannellum's own pieces, restyled to match */
        .pnlm-container { background: #141714 !important; font-family: inherit !important; }
        .pnlm-load-box { border-radius: 14px !important; background: rgba(20,24,21,0.8) !important; }
        .pnlm-hotspot-base.pnlm-scene {
            width: 46px; height: 46px; border-radius: 50%; background: rgba(25,115,53,0.88);
            border: 2px solid #fff; box-shadow: 0 4px 14px rgba(0,0,0,0.4); cursor: pointer;
            margin: -23px 0 0 -23px;
        }
        .pnlm-hotspot-base.pnlm-scene::before {
            content: ''; position: absolute; inset: -2px; border-radius: 50%;
            border: 2px solid rgba(146,219,159,0.9); animation: pulse 2s ease-out infinite;
        }
        .pnlm-hotspot-base.pnlm-scene::after {
            content: ''; position: absolute; left: 50%; top: 50%; width: 12px; height: 12px;
            border-top: 3px solid #fff; border-right: 3px solid #fff;
            transform: translate(-50%, -30%) rotate(-45deg);
        }
        .pnlm-hotspot-base.pnlm-scene:hover { background: var(--green-darker); }
        .pnlm-tooltip span {
            background: rgba(20,24,21,0.88) !important; border-radius: 8px !important;
            font-size: 13px !important; padding: 6px 10px !important;
        }
        .pnlm-tooltip span::after { border-color: rgba(20,24,21,0.88) transparent transparent transparent !important; }
        @keyframes pulse { from { transform: scale(1); opacity: 0.9; } to { transform: scale(1.6); opacity: 0; } }

        @media (prefers-reduced-motion: reduce) {
            .room-thumb, .hint svg, .pnlm-hotspot-base.pnlm-scene::before { animation: none; }
            .info-card, .hint { transition: none; }
        }

        @media (max-width: 1024px) { .topnav { padding: 10px 24px; flex-wrap: wrap; } }

        /* Phones: a compact info card, smaller controls and thumbnails. */
        @media (max-width: 640px) {
            .info-card { top: 12px; left: 12px; width: calc(100% - 82px); padding: 12px 14px; border-radius: 14px; }
            .info-title { font-size: 20px; }
            .info-meta { font-size: 13px; }
            .info-caption { display: none; }
            .info-actions { margin-top: 10px; }
            .info-actions a { height: 36px; font-size: 13px; }
            .controls { top: 12px; right: 12px; }
            .ctrl { width: 40px; height: 40px; }
            .bottom { padding: 0 12px 12px; }
            .dock { width: 100%; }
            .dock.collapsed { width: auto; }
            .dock-toggle { height: 54px; padding: 0 6px; font-size: 10px; }
            .room-thumb { flex-basis: 92px; height: 54px; }
            .hint { font-size: 13px; }
        }
        /* Short screens (e.g. phone turned sideways): keep the card small */
        @media (max-height: 520px) {
            .info-caption, .info-actions { display: none; }
        }
    </style>
</head>
<body>

    @include('partials.public-nav')

    <main class="stage" id="stage" aria-label="360 degree virtual tour">
        <div id="panorama"></div>
        <div class="viewer-message" id="viewerMessage" aria-live="polite">Loading virtual tours…</div>

        <section class="overlay glass info-card is-hidden" id="infoCard" aria-live="polite">
            <div class="info-eyebrow" id="infoEyebrow"></div>
            <h1 class="info-title" id="infoTitle">Virtual Tour</h1>
            <p class="info-meta" id="infoMeta"></p>
            <p class="info-caption" id="infoCaption"></p>
            <p class="info-updated" id="infoUpdated"></p>
            <div class="info-actions">
                <a href="{{ route('public.apply') }}" class="primary">Apply now</a>
                <a href="{{ route('public.inquiry') }}" class="ghost">Inquire</a>
            </div>
        </section>

        <div class="overlay glass controls" role="toolbar" aria-label="View controls">
            <button type="button" class="ctrl" id="zoomIn" aria-label="Zoom in" title="Zoom in">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            </button>
            <button type="button" class="ctrl" id="zoomOut" aria-label="Zoom out" title="Zoom out">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14"/></svg>
            </button>
            <div class="ctrl-sep"></div>
            <button type="button" class="ctrl" id="rotateBtn" aria-pressed="true" aria-label="Auto-rotate" title="Auto-rotate">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 1 1-3-6.7"/><path d="M21 4v5h-5"/></svg>
            </button>
            <button type="button" class="ctrl" id="fullBtn" aria-label="Full screen" title="Full screen">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"/></svg>
            </button>
        </div>

        <div class="overlay glass hint" id="hint" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 13V5.5a1.5 1.5 0 0 1 3 0V12"/><path d="M11 11.5v-1a1.5 1.5 0 0 1 3 0V12"/><path d="M14 11a1.5 1.5 0 0 1 3 0v1.5"/><path d="M17 12a1.5 1.5 0 0 1 3 0v3a6 6 0 0 1-6 6h-2a6 6 0 0 1-5-2.7L4.3 14.6a1.5 1.5 0 0 1 2.4-1.8L8 14.5"/></svg>
            Drag to look around
        </div>

        <div class="overlay bottom">
            <nav class="glass spots" id="spots" aria-label="Spots in this room" hidden></nav>
            <div class="glass dock" id="dock">
                <button type="button" class="dock-toggle" id="dockToggle" aria-expanded="true" aria-controls="roomsScroll">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                    Available<br>rooms
                </button>
                <div class="rooms-scroll" id="roomsScroll" aria-live="polite">
                    <div class="strip-empty">Loading…</div>
                </div>
            </div>
        </div>
    </main>

<script>
(function () {
    let viewer = null;
    let tours = [];
    let activeRoom = null;
    let rotating = true;
    const ROTATE_SPEED = -2; // degrees per second; matches the API default

    const $ = id => document.getElementById(id);
    const stage = $('stage');
    const panoramaEl = $('panorama');
    const messageEl = $('viewerMessage');
    const roomsScroll = $('roomsScroll');

    function showMessage(text) {
        panoramaEl.style.display = 'none';
        $('infoCard').classList.add('is-hidden');
        messageEl.style.display = 'flex';
        messageEl.textContent = text;
    }

    function escapeHtml(str) {
        const d = document.createElement('div');
        d.textContent = str ?? '';
        return d.innerHTML;
    }

    function peso(amount) {
        const n = Number(amount);
        return isNaN(n) ? '' : '₱' + n.toLocaleString('en-PH', { maximumFractionDigits: 0 });
    }

    /** Fills the floating card with the current room's facts. */
    function renderInfo(room) {
        const bits = [];
        if (room.floor) bits.push('Floor ' + room.floor);
        if (room.vacant_beds) bits.push(room.vacant_beds + ' bed' + (room.vacant_beds === 1 ? '' : 's') + ' open');
        $('infoEyebrow').textContent = bits.join(' · ');
        $('infoTitle').textContent = 'Room ' + room.room_no;

        const meta = [];
        if (room.room_type) meta.push(escapeHtml(room.room_type));
        if (room.capacity) meta.push('Good for ' + room.capacity + ' pax');
        if (room.price_per_bed) meta.push('<strong>' + peso(room.price_per_bed) + '</strong> / bed / month');
        $('infoMeta').innerHTML = meta.join(' · ');

        $('infoCaption').textContent = room.vr_caption || '';
        $('infoCaption').style.display = room.vr_caption ? '' : 'none';
        $('infoCard').classList.remove('is-hidden');
    }

    /** Shows when the photo currently on screen was last changed. */
    function renderUpdated(scene) {
        const date = scene && scene.updatedAt ? new Date(scene.updatedAt) : null;
        $('infoUpdated').textContent = date && !isNaN(date)
            ? 'Last updated on ' + date.toLocaleDateString('en-PH', { month: 'long', day: 'numeric', year: 'numeric' })
            : '';
    }

    /**
     * The "spots" bar lets visitors jump between the photos inside a room
     * (e.g. Entrance → Bedside) without hunting for the arrows. Hidden when
     * the room only has one photo.
     */
    function renderSpots(room, currentId) {
        renderUpdated(room.tour.scenes[currentId]);
        const scenes = Object.entries(room.tour.scenes);
        const bar = $('spots');
        bar.hidden = scenes.length < 2;
        bar.innerHTML = scenes.map(([id, scene]) => `
            <button type="button" class="spot${id === currentId ? ' active' : ''}" data-scene="${id}"${id === currentId ? ' aria-current="true"' : ''}>${escapeHtml(scene.title)}</button>
        `).join('');
        bar.querySelectorAll('.spot').forEach(btn => {
            btn.addEventListener('click', () => {
                if (viewer && btn.dataset.scene !== String(viewer.getScene())) viewer.loadScene(btn.dataset.scene);
            });
        });
    }

    /**
     * Pannellum's zoom number is the view's WIDTH. On an upright phone the
     * screen is much taller than wide, so the same width shows a huge amount
     * of ceiling and floor and the room looks tiny. For upright screens,
     * start zoomed in so the view is about 100° tall instead.
     * Returns null on landscape screens (keep the API's value).
     */
    function portraitStartHfov() {
        const aspect = stage.clientWidth / Math.max(1, stage.clientHeight);
        if (aspect >= 1) return null;
        const targetVfov = 100 * Math.PI / 180;
        return 2 * Math.atan(Math.tan(targetVfov / 2) * aspect) * 180 / Math.PI;
    }

    /**
     * Boots Pannellum with a room's full multi-scene tour config. The config
     * comes straight from the API already shaped the way Pannellum expects,
     * so scene switching and hotspot arrows work without extra wiring.
     * Pannellum's own title and buttons are switched off because this page
     * draws its own floating card and controls instead.
     */
    function loadTour(room) {
        activeRoom = room;
        messageEl.style.display = 'none';
        panoramaEl.style.display = 'block';

        if (viewer) {
            viewer.destroy();
            viewer = null;
        }

        const startHfov = portraitStartHfov();
        const scenes = Object.fromEntries(
            Object.entries(room.tour.scenes).map(([id, scene]) => {
                const copy = Object.assign({}, scene);
                delete copy.title; // the floating card shows the name instead
                if (startHfov) copy.hfov = Math.max(copy.minHfov || 50, Math.min(copy.hfov, startHfov));
                return [id, copy];
            })
        );
        const defaults = Object.assign({}, room.tour.default, {
            autoRotate: rotating ? ROTATE_SPEED : false,
        });

        viewer = pannellum.viewer('panorama', {
            default: defaults,
            scenes: scenes,
            autoLoad: true,
            showControls: false,
            hotSpotDebug: false,
            // Zoom limits (hfov/minHfov/maxHfov) come per scene from the API,
            // sized so the view never runs past the photo's edge.
        });

        viewer.on('scenechange', id => renderSpots(room, String(id)));
        viewer.on('mousedown', hideHint);
        viewer.on('touchstart', hideHint);

        renderInfo(room);
        renderSpots(room, String(room.tour.default.firstScene));
        markActiveThumb();
    }

    function markActiveThumb() {
        roomsScroll.querySelectorAll('.room-thumb').forEach(thumb => {
            const on = !!activeRoom && Number(thumb.dataset.room) === activeRoom.id;
            thumb.classList.toggle('active', on);
            thumb.setAttribute('aria-pressed', on ? 'true' : 'false');
        });
    }

    function renderRoomStrip() {
        if (tours.length === 0) {
            roomsScroll.innerHTML = '<div class="strip-empty">No available rooms right now.</div>';
            return;
        }

        roomsScroll.innerHTML = tours.map((room, i) => `
            <button type="button" class="room-thumb" data-room="${room.id}" style="animation-delay:${i * 55}ms" aria-label="View Room ${escapeHtml(room.room_no)} tour">
                ${room.thumbnail_url ? `<img src="${room.thumbnail_url}" alt="" loading="lazy" decoding="async">` : ''}
                <span class="label">Room ${escapeHtml(room.room_no)}<small>${[room.capacity ? room.capacity + ' pax' : '', room.price_per_bed ? peso(room.price_per_bed) + '/bed/mo' : ''].filter(Boolean).join(' · ')}</small></span>
            </button>
        `).join('');

        roomsScroll.querySelectorAll('.room-thumb').forEach(thumb => {
            thumb.addEventListener('click', () => {
                const room = tours.find(r => r.id === Number(thumb.dataset.room));
                if (room && room !== activeRoom) loadTour(room);
            });
        });
    }

    // ===== Floating controls =====
    function zoomBy(step) {
        if (viewer) viewer.setHfov(viewer.getHfov() + step);
    }
    $('zoomIn').addEventListener('click', () => zoomBy(-15));
    $('zoomOut').addEventListener('click', () => zoomBy(15));

    $('rotateBtn').addEventListener('click', function () {
        rotating = !rotating;
        this.setAttribute('aria-pressed', rotating ? 'true' : 'false');
        if (!viewer) return;
        if (rotating) viewer.startAutoRotate(ROTATE_SPEED);
        else viewer.stopAutoRotate();
    });

    // Full screen covers the whole stage (not just Pannellum's box) so the
    // floating card and controls come along.
    $('fullBtn').addEventListener('click', () => {
        if (document.fullscreenElement) document.exitFullscreen();
        else stage.requestFullscreen();
    });
    if (!document.fullscreenEnabled) $('fullBtn').style.display = 'none';

    $('dockToggle').addEventListener('click', function () {
        const collapsed = $('dock').classList.toggle('collapsed');
        this.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
    });

    // ===== First-visit hint =====
    const hintTimer = setTimeout(hideHint, 6000);
    function hideHint() {
        clearTimeout(hintTimer);
        $('hint').classList.add('gone');
    }

    fetch('/public-api/vr-tours')
        .then(r => r.json())
        .then(data => {
            tours = data;
            renderRoomStrip();

            if (tours.length === 0) {
                showMessage('No rooms are available for viewing right now. Please check back soon.');
                hideHint();
                return;
            }

            loadTour(tours[0]);
        })
        .catch(() => {
            showMessage('Could not load the virtual tours right now.');
            hideHint();
            roomsScroll.innerHTML = '<div class="strip-empty">Could not load rooms.</div>';
        });
})();
</script>

</body>
</html>
