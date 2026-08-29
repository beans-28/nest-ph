<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VR Room Viewing | NEST.PH</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css">
    <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        /* The whole page is locked to the viewport — no page scrolling, so the
           panorama fills the space between the nav and the room strip and the
           strip is always visible without needing sticky positioning. */
        html, body { height: 100%; overflow: hidden; }

        body {
            font-family: 'Roboto', system-ui, -apple-system, sans-serif;
            color: #292420;
            background: linear-gradient(90deg, #4e7454 0%, #92db9f 100%);
            display: flex;
            flex-direction: column;
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
        .topnav .logo .mark { width: 18px; height: 18px; border: 2px solid #fff; border-radius: 4px; flex-shrink: 0; }
        .topnav .buttons { flex: 1; display: flex; justify-content: flex-end; gap: 12px; }
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            height: 40px; padding: 0 18px; border: 2px solid #fff;
            font-weight: 500; font-size: 13.5px; cursor: pointer;
            white-space: nowrap; text-decoration: none;
        }
        .btn-white { background: #fff; color: #292420; }
        .btn-outline-white { background: transparent; color: #fff; }

        /* Title bar */
        .vr-titlebar {
            background: linear-gradient(90deg, #ddd8d7, #f2f2f2);
            padding: 10px clamp(20px, 5vw, 64px);
            box-shadow: 0 4px 4px rgba(0,0,0,0.2), inset 0 4px 4px rgba(0,0,0,0.12);
            flex-shrink: 0;
        }
        .vr-titlebar h1 { font-size: clamp(15px, 1.8vw, 20px); font-weight: 700; color: #2a241f; }

        /* Viewer — takes whatever height is left between nav and strip */
        .viewer-shell {
            position: relative; background: #1a1a18;
            flex: 1; min-height: 0; display: flex; flex-direction: column;
        }
        #panorama { width: 100%; flex: 1; min-height: 0; }
        .viewer-message {
            display: flex; align-items: center; justify-content: center;
            flex: 1; color: #e6e6e2; font-size: 14px; text-align: center;
            padding: 20px;
        }
        /* Room picker strip — pinned to the bottom of the viewport so it stays
           reachable while looking around the panorama. */
        .rooms-strip {
            flex-shrink: 0;
            background: linear-gradient(8deg, rgba(93,71,62,0.96) -20%, rgba(28,20,17,0.96) 85%);
            padding: 8px clamp(14px, 3vw, 28px) 10px;
            box-shadow: 0 -6px 20px rgba(0,0,0,0.28);
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .rooms-strip h2 {
            color: rgba(255,255,255,0.75); font-size: 10.5px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.09em;
            text-align: center; margin-bottom: 9px;
        }
        .rooms-scroll {
            display: flex; gap: 10px; overflow-x: auto; padding-bottom: 4px;
            scroll-snap-type: x mandatory; justify-content: flex-start;
        }
        .rooms-scroll::-webkit-scrollbar { height: 5px; }
        .rooms-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.22); border-radius: 4px; }

        .room-thumb {
            position: relative; flex: 0 0 132px; height: 76px; border-radius: 8px;
            overflow: hidden; cursor: pointer; scroll-snap-align: start;
            background: #3f4a3f; box-shadow: 0 3px 8px rgba(0,0,0,0.35);
            border: 2px solid transparent;
            transition: transform 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease;
            animation: thumbIn 0.4s ease backwards;
        }
        .room-thumb:hover { transform: translateY(-3px); box-shadow: 0 7px 16px rgba(0,0,0,0.42); }
        .room-thumb.active { border-color: #92db9f; transform: translateY(-2px); }
        .room-thumb img {
            width: 100%; height: 100%; object-fit: cover; display: block;
            transition: transform 0.35s ease;
        }
        .room-thumb:hover img { transform: scale(1.07); }
        .room-thumb .label {
            position: absolute; top: 5px; left: 7px; color: #fff; font-weight: 700;
            font-size: 13px; text-shadow: 0 2px 6px rgba(0,0,0,0.8);
        }
        .room-thumb .count {
            position: absolute; bottom: 5px; right: 6px; color: #fff; font-size: 9px;
            background: rgba(0,0,0,0.6); padding: 2px 7px; border-radius: 999px;
        }
        .strip-empty { color: #ddd8d7; text-align: center; font-size: 12px; padding: 12px; }

        @keyframes thumbIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @media (prefers-reduced-motion: reduce) {
            .room-thumb, .room-thumb img, .rooms-strip { animation: none; transition: none; }
        }

        @media (max-width: 1024px) { .topnav { padding: 14px 24px; flex-wrap: wrap; } }
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

    <div class="vr-titlebar">
        <h1>VR Room Viewing &mdash; 360&deg; Virtual Tour</h1>
    </div>

    <div class="viewer-shell">
        <div id="panorama"></div>
        <div class="viewer-message" id="viewerMessage">Loading virtual tours…</div>
    </div>

    <div class="rooms-strip">
        <h2>Rooms</h2>
        <div class="rooms-scroll" id="roomsScroll">
            <div class="strip-empty">Loading…</div>
        </div>
    </div>

<script>
(function () {
    let viewer = null;
    let tours = [];
    let activeRoomId = null;

    const panoramaEl = document.getElementById('panorama');
    const messageEl = document.getElementById('viewerMessage');
    const roomsScroll = document.getElementById('roomsScroll');

    function showMessage(text) {
        panoramaEl.style.display = 'none';
        messageEl.style.display = 'flex';
        messageEl.textContent = text;
    }

    function escapeHtml(str) {
        const d = document.createElement('div');
        d.textContent = str ?? '';
        return d.innerHTML;
    }

    /**
     * Boots Pannellum with a room's full multi-scene tour config. The config
     * comes straight from the API already shaped the way Pannellum expects,
     * so scene switching and hotspot arrows work without extra wiring.
     */
    function loadTour(room) {
        activeRoomId = room.id;

        messageEl.style.display = 'none';
        panoramaEl.style.display = 'block';

        if (viewer) {
            viewer.destroy();
            viewer = null;
        }

        // Prefix each scene's title with the room number so Pannellum's own
        // bottom-left title carries both, instead of duplicating it in a
        // separate overlay chip.
        const tour = {
            default: room.tour.default,
            scenes: Object.fromEntries(
                Object.entries(room.tour.scenes).map(([id, scene]) => [
                    id,
                    Object.assign({}, scene, {
                        title: 'Room ' + room.room_no + ' — ' + scene.title,
                    }),
                ])
            ),
        };

        viewer = pannellum.viewer('panorama', Object.assign({}, tour, {
            autoLoad: true,
            showControls: true,
            hotSpotDebug: false,
            // Pannellum's default field of view (100°) reads as quite zoomed in
            // on a room-sized space. Starting wider shows more of the room at
            // once; visitors can still pinch/scroll in and out from here.
            hfov: 125,
            minHfov: 50,
            maxHfov: 140,
        }));

        markActiveThumb();
    }

    function markActiveThumb() {
        roomsScroll.querySelectorAll('.room-thumb').forEach(thumb => {
            thumb.classList.toggle('active', Number(thumb.dataset.room) === activeRoomId);
        });
    }

    function renderRoomStrip() {
        if (tours.length === 0) {
            roomsScroll.innerHTML = '<div class="strip-empty">No virtual tours have been published yet.</div>';
            return;
        }

        roomsScroll.innerHTML = tours.map((room, i) => `
            <div class="room-thumb" data-room="${room.id}" style="animation-delay:${i * 55}ms">
                ${room.thumbnail_url ? `<img src="${room.thumbnail_url}" alt="Room ${escapeHtml(room.room_no)}">` : ''}
                <span class="label">${escapeHtml(room.room_no)}</span>
                <span class="count">${room.scene_count} view${room.scene_count === 1 ? '' : 's'}</span>
            </div>
        `).join('');

        roomsScroll.querySelectorAll('.room-thumb').forEach(thumb => {
            thumb.addEventListener('click', () => {
                const room = tours.find(r => r.id === Number(thumb.dataset.room));
                if (room) loadTour(room);
            });
        });
    }

    fetch('/public-api/vr-tours')
        .then(r => r.json())
        .then(data => {
            tours = data;
            renderRoomStrip();

            if (tours.length === 0) {
                showMessage('No virtual tours have been published yet. Please check back soon.');
                return;
            }

            loadTour(tours[0]);
        })
        .catch(() => {
            showMessage('Could not load the virtual tours right now.');
            roomsScroll.innerHTML = '<div class="strip-empty">Could not load rooms.</div>';
        });
})();
</script>

</body>
</html>