<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Rooms · {{ $brandDormName }}</title>
    <link rel="icon" href="{{ $brandFaviconUrl }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
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
            font-family: 'Roboto', system-ui, -apple-system, sans-serif;
            color: var(--ink);
            background:
                linear-gradient(160deg, #4e7454 15%, #3b4a3e 85%),
                linear-gradient(100deg, #4e7454 0%, #92db9f 100%);
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
        .rooms-header {
            background: linear-gradient(90deg, var(--cream), var(--cream-light));
            padding: clamp(28px, 5vw, 40px) clamp(20px, 6vw, 64px) clamp(32px, 5vw, 44px);
            box-shadow: 0 4px 4px rgba(0,0,0,0.15), inset 0 4px 4px rgba(0,0,0,0.1);
            position: relative;
        }
        .back-button {
            background: none; border: none; color: var(--green-darker); font-size: 26px;
            cursor: pointer; line-height: 1; padding: 0; margin-bottom: 16px;
        }
        .rooms-header-text { text-align: center; }
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
        .legend-dot.maintenance { background: radial-gradient(circle, #ffeeef 0%, #d4c130 100%); }

        /* ===== Room/bed status grid ===== */
        .status-section {
            background: linear-gradient(8deg, #605a58 -20%, rgba(37,26,22,0.85) 85%);
            padding: clamp(24px, 4vw, 40px) clamp(16px, 4vw, 40px);
        }
        .status-grid { max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 28px; }
        .floor-group-title {
            display: flex; align-items: center; gap: 14px; margin-bottom: 14px;
            color: #f2f4f8; font-weight: 700; font-size: 15px; letter-spacing: 0.08em; text-transform: uppercase;
        }
        .floor-group-title::after { content: ''; flex: 1; height: 1px; background: rgba(255,255,255,0.2); }
        .floor-rooms { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .status-card {
            background: #ddd8d7; border-radius: 12px; padding: 16px 18px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
            display: flex; flex-direction: column;
        }
        .status-card-head {
            display: flex; justify-content: space-between; align-items: center;
            font-weight: 700; font-size: 16px; color: #0b151d;
            padding-bottom: 10px; margin-bottom: 10px; border-bottom: 1px solid rgba(0,0,0,0.12);
        }
        .status-card-head .open-count {
            font-weight: 700; font-size: 11px; letter-spacing: 0.03em; padding: 4px 10px; border-radius: 999px;
            background: #cfe9d2; color: #197335;
        }
        .status-card-head .open-count.full { background: #f1d0d2; color: #a3262d; }
        /* Beds sit in two columns, and every card reserves room for the
           biggest room's bed count (--bed-rows, set in JS) so all cards
           are the same size. */
        .bed-list {
            display: grid; grid-template-columns: 1fr 1fr; column-gap: 12px;
            grid-auto-rows: 24px; align-content: start;
            min-height: calc(var(--bed-rows, 1) * 24px);
        }
        .bed-row { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #0b151d; }
        .bed-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
        .bed-dot.vacant { background: radial-gradient(circle, #ffeeef 0%, #00d444 100%); }
        .bed-dot.occupied { background: radial-gradient(circle, #ffeeef 0%, #e24149 100%); }
        .bed-dot.reserved { background: radial-gradient(circle, #ffeeef 0%, #a165b3 100%); }
        .bed-dot.maintenance { background: radial-gradient(circle, #ffeeef 0%, #d4c130 100%); }
        .bed-label { font-weight: 700; min-width: 22px; }
        .bed-status { color: #4c5c6b; text-transform: capitalize; }
        /* Taken beds recede so the available ones are what the eye finds first. */
        .bed-row.occupied { color: #5d6a75; }
        .bed-row.occupied .bed-label { font-weight: 500; }
        .bed-row.vacant .bed-status { color: #197335; font-weight: 700; }
        .status-card-head .open-count, .bed-label { font-variant-numeric: tabular-nums; }
        ::selection { background: #a2d9a4; color: #292420; }
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

        .listing-favorite {
            position: absolute; top: 12px; right: 12px; width: 30px; height: 30px;
            border-radius: 50%; background: rgba(0,0,0,0.25); display: flex; align-items: center;
            justify-content: center; cursor: pointer; transition: background 0.15s;
            border: none; padding: 0;
        }
        .listing-favorite:hover { background: rgba(0,0,0,0.4); }
        .listing-favorite svg { width: 16px; height: 16px; stroke: #fff; fill: none; stroke-width: 2; }
        .listing-favorite.active svg { fill: #e24149; stroke: #e24149; }
        .listing-body { padding: 14px 16px 16px; }
        .listing-price { color: #355e3d; font-weight: 700; font-size: 16px; margin-bottom: 8px; }
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

        /* Scales the whole page down uniformly on larger screens, where the
           fixed px sizes above otherwise read a bit large relative to
           available screen space (e.g. a 14" laptop at 100% zoom). Left
           untouched on tablet/mobile widths, where the breakpoints above
           already handle sizing appropriately. `zoom` (not
           `transform: scale`) is used deliberately -- it recalculates
           layout at the smaller size instead of just visually shrinking a
           layout still sized for full scale, which is what avoids leftover
           blank space or overflow. */
        @media (min-width: 1025px) {
            .page-wrap { zoom: 0.85; }
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
                <div class="legend">
                    <div class="legend-item"><span class="legend-dot available"></span> Available</div>
                    <div class="legend-item"><span class="legend-dot reserved"></span> Reserved</div>
                    <div class="legend-item"><span class="legend-dot occupied"></span> Occupied</div>
                    <div class="legend-item"><span class="legend-dot maintenance"></span> Maintenance</div>
                </div>
            </div>
        </div>

        <div class="status-section">
            <div class="status-grid" id="statusGrid" aria-live="polite">
                <div class="empty-note">Loading rooms…</div>
            </div>
        </div>

        <div class="vr-cta-wrap">
            <a href="{{ route('public.vr') }}" class="vr-cta">VR Room Tour</a>
        </div>

        <div class="listing-section">
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

            const maxBeds = Math.max(1, ...sorted.map(r => r.beds.length));
            grid.style.setProperty('--bed-rows', Math.ceil(maxBeds / 2));

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
                const open = room.beds.filter(b => b.status === 'vacant').length;
                return `
                    <div class="status-card">
                        <div class="status-card-head">
                            <span>Room ${escapeHtml(room.room_no)}</span>
                            <span class="open-count ${open === 0 ? 'full' : ''}">${open === 0 ? 'Full' : `${open} of ${room.beds.length} available`}</span>
                        </div>
                        <div class="bed-list">
                            ${room.beds.map(bed => `
                                <div class="bed-row ${bed.status}">
                                    <span class="bed-dot ${bed.status}"></span>
                                    <span class="bed-label">${escapeHtml(bed.label)}</span>
                                    <span class="bed-status">${STATUS_LABELS[bed.status] || bed.status}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            };

            grid.innerHTML = floors.map(group => `
                <section>
                    <h2 class="floor-group-title">${escapeHtml(group.label || 'Other')}</h2>
                    <div class="floor-rooms">${group.rooms.map(cardHtml).join('')}</div>
                </section>
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
                        : `<div class="listing-photo"></div>`;

                const tourButton = room.has_vr_tour
                    ? `<a href="{{ route('public.vr') }}">Start Tour</a>`
                    : `<a href="{{ route('public.vr') }}" class="disabled">Start Tour</a>`;

                return `
                    <div class="listing-card">
                        ${photoHtml}
                        <button type="button" class="listing-favorite" aria-pressed="false" aria-label="Save Room ${escapeHtml(room.room_no)} to favorites" onclick="this.classList.toggle('active'); this.setAttribute('aria-pressed', this.classList.contains('active'))">
                            <svg viewBox="0 0 24 24"><path d="M12 21s-7-4.5-9.5-9C1 8.5 2.5 5 6 5c2 0 3.5 1.2 4 2.2C10.5 6.2 12 5 14 5c3.5 0 5 3.5 3.5 7-2.5 4.5-9.5 9-9.5 9z"/></svg>
                        </button>
                        <div class="listing-body">
                            <div class="listing-room-label">Room ${escapeHtml(room.room_no)}</div>
                            <div class="listing-price">${typeLabel} - ${formatPrice(room.price_per_bed)}/mo</div>
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

</body>
</html>