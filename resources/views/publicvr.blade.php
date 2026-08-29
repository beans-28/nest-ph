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
        .page-wrap { overflow-x: hidden; }
        body {
            font-family: 'Roboto', system-ui, -apple-system, sans-serif;
            color: #292420;
            background: linear-gradient(90deg, #4e7454 0%, #92db9f 100%);
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
            padding: 18px clamp(20px, 5vw, 64px);
            box-shadow: 0 4px 4px rgba(0,0,0,0.2), inset 0 4px 4px rgba(0,0,0,0.12);
        }
        .vr-titlebar h1 { font-size: clamp(20px, 2.6vw, 30px); font-weight: 700; color: #2a241f; }

        /* Viewer */
        .viewer-shell { position: relative; background: #1a1a18; }
        #panorama { width: 100%; height: min(66vh, 620px); }
        .viewer-overlay {
            position: absolute; top: 16px; left: 16px; z-index: 5;
            display: flex; align-items: center; gap: 10px; pointer-events: none;
        }
        .scene-chip {
            background: rgba(0,0,0,0.55); color: #fff; font-size: 13px; font-weight: 500;
            padding: 8px 16px; border-radius: 999px; backdrop-filter: blur(4px);
        }
        .viewer-message {
            display: flex; align-items: center; justify-content: center;
            height: min(66vh, 620px); color: #e6e6e2; font-size: 14px; text-align: center;
            padding: 20px;
        }
        .scene-nav {
            display: flex; gap: 8px; flex-wrap: wrap; justify-content: center;
            padding: 14px clamp(16px, 4vw, 40px);
            background: rgba(0,0,0,0.3);
        }
        .scene-nav button {
            background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.3);
            border-radius: 999px; padding: 7px 16px; font-size: 12.5px; font-weight: 500;
            cursor: pointer; font-family: inherit; transition: background 0.15s;
        }
        .scene-nav button:hover { background: rgba(255,255,255,0.28); }
        .scene-nav button.active { background: #fff; color: #292420; border-color: #fff; font-weight: 700; }

        /* Room picker strip */
        .rooms-strip {
            background: linear-gradient(8deg, #5d473e -20%, rgba(37,26,22,0.85) 85%);
            padding: clamp(20px, 3vw, 30px) clamp(16px, 4vw, 40px) clamp(28px, 4vw, 40px);
        }
        .rooms-strip h2 {
            color: #fff; font-size: 18px; font-weight: 700; text-align: center; margin-bottom: 18px;
        }
        .rooms-scroll {
            display: flex; gap: 16px; overflow-x: auto; padding-bottom: 8px;
            scroll-snap-type: x mandatory;
        }
        .room-thumb {
            position: relative; flex: 0 0 220px; height: 150px; border-radius: 10px;
            overflow: hidden; cursor: pointer; scroll-snap-align: start;
            background: #3f4a3f; box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            border: 2px solid transparent; transition: border-color 0.15s;
        }
        .room-thumb.active { border-color: #92db9f; }
        .room-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .room-thumb .label {
            position: absolute; top: 8px; left: 10px; color: #fff; font-weight: 700;
            font-size: 17px; text-shadow: 0 2px 6px rgba(0,0,0,0.7);
        }
        .room-thumb .count {
            position: absolute; bottom: 8px; right: 10px; color: #fff; font-size: 10.5px;
            background: rgba(0,0,0,0.55); padding: 3px 8px; border-radius: 999px;
        }
        .strip-empty { color: #ddd8d7; text-align: center; font-size: 13px; padding: 20px; }

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

    <div class="page-wrap">

        <div class="vr-titlebar">
            <h1>VR Room Viewing &mdash; 360&deg; Virtual Tour</h1>
        </div>

        <div class="viewer-shell">
            <div class="viewer-overlay">
                <span class="scene-chip" id="sceneChip" style="display:none;"></span>
            </div>
            <div id="panorama"></div>
            <div class="viewer-message" id="viewerMessage">Loading virtual tours…</div>
            <div class="scene-nav" id="sceneNav" style="display:none;"></div>
        </div>

        <div class="rooms-strip">
            <h2>Rooms</h2>
            <div class="rooms-scroll" id="roomsScroll">
                <div class="strip-empty">Loading…</div>
            </div>
        </div>

    </div>

<script>
(function () {
    let viewer = null;
    let tours = [];
    let activeRoomId = null;

    const panoramaEl = document.getElementById('panorama');
    const messageEl = document.getElementById('viewerMessage');
    const sceneChip = document.getElementById('sceneChip');
    const sceneNav = document.getElementById('sceneNav');
    const roomsScroll = document.getElementById('roomsScroll');

    function showMessage(text) {
        panoramaEl.style.display = 'none';
        sceneNav.style.display = 'none';
        sceneChip.style.display = 'none';
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

        viewer = pannellum.viewer('panorama', Object.assign({}, room.tour, {
            autoLoad: true,
            showControls: true,
            hotSpotDebug: false,
        }));

        viewer.on('scenechange', function (sceneId) {
            updateSceneUi(room, sceneId);
        });

        updateSceneUi(room, room.tour.default.firstScene);
        renderSceneNav(room);
        markActiveThumb();
    }

    function updateSceneUi(room, sceneId) {
        const scene = room.tour.scenes[sceneId];
        if (scene) {
            sceneChip.style.display = 'inline-block';
            sceneChip.textContent = 'Room ' + room.room_no + ' — ' + scene.title;
        }
        sceneNav.querySelectorAll('button').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.scene === String(sceneId));
        });
    }

    /**
     * Buttons to jump directly between scenes. The hotspot arrows inside the
     * panorama are the primary way to move around; this is a fallback for
     * anyone who can't find an arrow.
     */
    function renderSceneNav(room) {
        const ids = Object.keys(room.tour.scenes);

        if (ids.length < 2) {
            sceneNav.style.display = 'none';
            return;
        }

        sceneNav.style.display = 'flex';
        sceneNav.innerHTML = ids.map(id => `
            <button type="button" data-scene="${id}">${escapeHtml(room.tour.scenes[id].title)}</button>
        `).join('');

        sceneNav.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', () => {
                if (viewer) viewer.loadScene(btn.dataset.scene);
            });
        });
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

        roomsScroll.innerHTML = tours.map(room => `
            <div class="room-thumb" data-room="${room.id}">
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

    window.addEventListener('scroll', function () {
        const nav = document.querySelector('.topnav');
        nav.classList.toggle('scrolled', window.scrollY > 10);
    });
})();
</script>

</body>
</html>