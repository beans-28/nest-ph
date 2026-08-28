<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dorm Info | NEST.PH</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        .page-wrap { overflow-x: hidden; }

        body {
            font-family: 'Roboto', system-ui, -apple-system, sans-serif;
            color: #292420;
            background: linear-gradient(180deg, #567357 0%, #59473f 100%);
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

        /* ===== DORM INFO CONTENT ===== */
        .info-wrap {
            max-width: 980px; margin: 0 auto;
            padding: clamp(28px, 5vw, 48px) clamp(20px, 5vw, 24px) clamp(40px, 6vw, 64px);
        }

        .info-header {
            display: flex; align-items: center; justify-content: space-between;
            gap: 16px; flex-wrap: wrap; margin-bottom: 24px;
        }
        .info-header-text h1 {
            color: #fff; font-weight: 900; font-size: clamp(24px, 3vw, 32px);
            letter-spacing: 0.01em;
        }
        .info-header-text p {
            color: rgba(255,255,255,0.85); font-size: 14px; margin-top: 6px; max-width: 520px;
        }
        .btn-download {
            display: inline-flex; align-items: center; gap: 8px;
            background: #fff; color: #292420; font-weight: 700; font-size: 14px;
            padding: 12px 22px; border-radius: 999px; text-decoration: none;
            box-shadow: 0 6px 16px rgba(0,0,0,0.2); white-space: nowrap;
            transition: transform 0.15s ease;
        }
        .btn-download:hover { transform: translateY(-1px); }
        .btn-download svg { width: 17px; height: 17px; }

        .doc-card {
            background: #fff; border-radius: 16px; overflow: hidden;
            box-shadow: 0 14px 34px rgba(0,0,0,0.25);
        }
        .doc-frame {
            width: 100%; height: 82vh; min-height: 480px; border: none; display: block;
        }

        /* Fallback content when no PDF has been uploaded yet */
        .fallback {
            background: #eeeded; border-radius: 16px; padding: clamp(28px, 5vw, 48px);
        }
        .fallback-notice {
            display: flex; align-items: flex-start; gap: 12px; background: #fff8e6;
            border: 1px solid #f0dfa8; border-radius: 10px; padding: 14px 16px;
            font-size: 13px; color: #6b5a1f; margin-bottom: 32px; line-height: 1.6;
        }
        .fallback-notice svg { width: 18px; height: 18px; flex-shrink: 0; margin-top: 1px; color: #b3941f; }

        .doc-section { margin-bottom: 34px; }
        .doc-section:last-child { margin-bottom: 0; }
        .doc-section h2 {
            color: #194e19; font-weight: 700; font-size: 18px; text-transform: uppercase;
            letter-spacing: 0.02em; margin-bottom: 14px; padding-bottom: 10px;
            border-bottom: 2px solid #d7e8d8;
        }
        .doc-section .body-text {
            font-size: 14px; line-height: 1.8; color: #33393c; white-space: pre-line;
        }

        .empty-state { text-align: center; padding: 40px 20px; color: #4b5f4c; }
        .empty-state svg { width: 48px; height: 48px; margin-bottom: 14px; color: #8a9690; }
        .empty-state p { font-size: 14px; max-width: 420px; margin: 0 auto; line-height: 1.6; }

        @media (max-width: 640px) {
            .info-header { flex-direction: column; align-items: flex-start; }
            .doc-frame { height: 70vh; }
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
    <div class="info-wrap">
        <div class="info-header">
            <div class="info-header-text">
                <h1>{{ $dormName ?? 'NEST.PH' }} — Dorm Info</h1>
                <p>Rental rates, payment schedule, house rules, and check-out procedures.</p>
            </div>
            @if($policiesFileUrl)
                <a href="{{ $policiesFileUrl }}" download class="btn-download">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12m0 0l-4-4m4 4l4-4M4 19h16"/></svg>
                    Download PDF
                </a>
            @endif
        </div>

        @if($policiesFileUrl)
            {{-- Real uploaded document — the browser's native PDF viewer gives
                 zoom, scroll, search, and print for free. --}}
            <div class="doc-card">
                <iframe src="{{ $policiesFileUrl }}" class="doc-frame" title="Dormitory policies document"></iframe>
            </div>
        @else
            {{-- No PDF uploaded yet — fall back to the seeded text content so
                 the page still has real information instead of being empty.
                 NOTE for whoever builds the admin "Manage Dormitory Profile"
                 page: uploading a file here (dormitory_profile.policies_file_path)
                 replaces this fallback with the real document automatically. --}}
            <div class="fallback">
                <div class="fallback-notice">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4m0 4h.01M10.29 3.86l-8.4 14.55A1.5 1.5 0 003.19 21h17.62a1.5 1.5 0 001.3-2.59l-8.4-14.55a1.5 1.5 0 00-2.62 0z"/></svg>
                    <span>The admin hasn't uploaded a policies document yet. Showing the information on file below instead.</span>
                </div>

                @if($paymentsAndFees || $houseRules || $checkoutProcedures)
                    @if($paymentsAndFees)
                        <div class="doc-section">
                            <h2>Payments and Fees</h2>
                            <div class="body-text">{{ $paymentsAndFees }}</div>
                        </div>
                    @endif
                    @if($houseRules)
                        <div class="doc-section">
                            <h2>Dormitory Rules and Regulation</h2>
                            <div class="body-text">{{ $houseRules }}</div>
                        </div>
                    @endif
                    @if($checkoutProcedures)
                        <div class="doc-section">
                            <h2>Check-out Procedures</h2>
                            <div class="body-text">{{ $checkoutProcedures }}</div>
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
                        <p>Dorm information isn't available yet. Please check back soon, or contact the dormitory directly.</p>
                    </div>
                @endif
            </div>
        @endif
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
