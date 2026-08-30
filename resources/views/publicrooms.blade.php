<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Rooms | NEST.PH</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        .page-wrap { overflow-x: hidden; }

        body {
            font-family: 'Roboto', system-ui, -apple-system, sans-serif;
            color: #292420;
            background:
                linear-gradient(160deg, #4e7454 15%, #3b4a3e 85%),
                linear-gradient(100deg, #4e7454 0%, #92db9f 100%);
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

        /* ===== Header banner ===== */
        .rooms-header {
            background: linear-gradient(90deg, #ddd8d7, #f2f2f2);
            padding: clamp(28px, 5vw, 40px) clamp(20px, 6vw, 64px) clamp(32px, 5vw, 44px);
            box-shadow: 0 4px 4px rgba(0,0,0,0.15), inset 0 4px 4px rgba(0,0,0,0.1);
            position: relative;
        }
        .back-button {
            background: none; border: none; color: #20bd72; font-size: 26px;
            cursor: pointer; line-height: 1; padding: 0; margin-bottom: 16px;
        }
        .rooms-header-text { text-align: center; }
        .rooms-header .eyebrow {
            font-weight: 700; font-size: 14px; letter-spacing: 0.08em; text-transform: uppercase;
            color: #004f0f; margin-bottom: 6px;
        }
        .rooms-header h1 {
            font-weight: 700; font-size: clamp(22px, 3vw, 30px); color: #1f272a;
            letter-spacing: 0.01em; margin-bottom: 18px;
        }
        .legend { display: flex; justify-content: center; gap: 28px; flex-wrap: wrap; }
        .legend-item { display: flex; align-items: center; gap: 8px; font-size: 12.5px; font-weight: 700; color: #1f272a; letter-spacing: 0.03em; }
        .legend-dot { width: 13px; height: 13px; border-radius: 50%; }
        .legend-dot.available { background: radial-gradient(circle, #ffeeef 0%, #00d444 100%); }
        .legend-dot.reserved { background: radial-gradient(circle, #ffeeef 0%, #a165b3 100%); }
        .legend-dot.occupied { background: radial-gradient(circle, #ffeeef 0%, #e24149 100%); }

        /* ===== Room/bed status grid ===== */
        .status-section {
            background: linear-gradient(8deg, #605a58 -20%, rgba(37,26,22,0.85) 85%);
            padding: clamp(24px, 4vw, 40px) clamp(16px, 4vw, 40px);
        }
        .status-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;
            max-width: 1400px; margin: 0 auto;
        }
        .status-card {
            background: #ddd8d7; border: 1px solid #496342; border-radius: 8px;
            padding: 16px 20px;
        }
        .status-card-head {
            display: flex; justify-content: space-between; align-items: baseline;
            font-weight: 700; font-size: 14px; color: #0b151d; margin-bottom: 10px;
        }
        .status-card-head .floor { font-weight: 500; font-size: 12px; color: #4c5c6b; }
        .bed-row { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #0b151d; padding: 3px 0; }
        .bed-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
        .bed-dot.vacant { background: radial-gradient(circle, #ffeeef 0%, #00d444 100%); }
        .bed-dot.occupied { background: radial-gradient(circle, #ffeeef 0%, #e24149 100%); }
        .bed-dot.maintenance { background: radial-gradient(circle, #ffeeef 0%, #a165b3 100%); }
        .bed-label { font-weight: 700; min-width: 22px; }
        .bed-status { color: #4c5c6b; text-transform: capitalize; }
        .empty-note { color: #eeeded; font-size: 13px; text-align: center; padding: 20px; grid-column: 1 / -1; }

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
        .listing-favorite {
            position: absolute; top: 12px; right: 12px; width: 30px; height: 30px;
            border-radius: 50%; background: rgba(0,0,0,0.25); display: flex; align-items: center;
            justify-content: center; cursor: pointer; transition: background 0.15s;
        }
        .listing-favorite:hover { background: rgba(0,0,0,0.4); }
        .listing-favorite svg { width: 16px; height: 16px; stroke: #fff; fill: none; stroke-width: 2; }
        .listing-favorite.active svg { fill: #e24149; stroke: #e24149; }
        .listing-body { padding: 14px 16px 16px; }
        .listing-price { color: #355e3d; font-weight: 700; font-size: 16px; margin-bottom: 8px; }
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

        @media (max-width: 1024px) {
            .status-grid { grid-template-columns: repeat(2, 1fr); }
            .listing-grid { grid-template-columns: 1fr; }
            .topnav { padding: 14px 24px; flex-wrap: wrap; }
        }
        @media (max-width: 640px) {
            .status-grid { grid-template-columns: 1fr; }
            .legend { gap: 16px; }
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

        <div class="rooms-header">
            <button class="back-button" type="button" aria-label="Go back" onclick="window.location.href='{{ route('home') }}'">←</button>
            <div class="rooms-header-text">
                <div class="eyebrow">Pureza Station Dormitory</div>
                <h1>Available Rooms</h1>
                <div class="legend">
                    <div class="legend-item"><span class="legend-dot available"></span> Available</div>
                    <div class="legend-item"><span class="legend-dot reserved"></span> Reserved</div>
                    <div class="legend-item"><span class="legend-dot occupied"></span> Occupied</div>
                </div>
            </div>
        </div>

        <div class="status-section">
            <div class="status-grid" id="statusGrid">
                <div class="empty-note">Loading rooms…</div>
            </div>
        </div>

        <div class="vr-cta-wrap">
            <a href="{{ route('public.vr') }}" class="vr-cta">VR Room Tour</a>
        </div>

        <div class="listing-section">
            <div class="listing-grid" id="listingGrid">
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

    <script>
        // Bed status → legend dot color mapping. "maintenance" is treated as
        // the closest visual match to the design's "Reserved" category —
        // there's no literal reserved concept in the current bed schema.
        const STATUS_LABELS = { vacant: 'Open', occupied: 'Occupied', maintenance: 'Reserved' };

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

            grid.innerHTML = rooms.map(room => `
                <div class="status-card">
                    <div class="status-card-head">
                        <span>Room ${escapeHtml(room.room_no)}</span>
                        <span class="floor">${escapeHtml(room.floor_label || '')}</span>
                    </div>
                    ${room.beds.map(bed => `
                        <div class="bed-row">
                            <span class="bed-dot ${bed.status}"></span>
                            <span class="bed-label">${escapeHtml(bed.label)}</span>
                            <span class="bed-status">${STATUS_LABELS[bed.status] || bed.status}</span>
                        </div>
                    `).join('')}
                </div>
            `).join('');
        }

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
                    .map(a => `<span>• ${escapeHtml(a)}</span>`)
                    .join('');

                const photoHtml = room.photo_url
                    ? `<img src="${room.photo_url}" class="listing-photo" alt="Room ${escapeHtml(room.room_no)}">`
                    : `<div class="listing-photo"></div>`;

                const tourButton = room.has_vr_tour
                    ? `<a href="{{ route('public.vr') }}">Start Tour</a>`
                    : `<a href="{{ route('public.vr') }}" class="disabled">Start Tour</a>`;

                return `
                    <div class="listing-card">
                        ${photoHtml}
                        <div class="listing-favorite" onclick="this.classList.toggle('active')">
                            <svg viewBox="0 0 24 24"><path d="M12 21s-7-4.5-9.5-9C1 8.5 2.5 5 6 5c2 0 3.5 1.2 4 2.2C10.5 6.2 12 5 14 5c3.5 0 5 3.5 3.5 7-2.5 4.5-9.5 9-9.5 9z"/></svg>
                        </div>
                        <div class="listing-body">
                            <div class="listing-price">${typeLabel} - ${formatPrice(room.monthly_rate)}/mo</div>
                            <div class="listing-amenities">${amenitiesHtml}</div>
                            <div class="listing-buttons">
                                ${tourButton}
                                <a href="{{ route('public.apply') }}">Apply</a>
                                <a href="{{ route('public.inquiry') }}?room_id=${room.id}&room_type=${encodeURIComponent(room.room_type || '')}">Inquiry</a>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function setTourBanner(rooms) {
            const withPhoto = rooms.find(r => r.photo_url);
            if (withPhoto) {
                document.getElementById('tourBanner').style.backgroundImage = `url('${withPhoto.photo_url}')`;
            }
        }

        fetch('/public-api/rooms')
            .then(r => r.json())
            .then(rooms => {
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

</body>
</html>