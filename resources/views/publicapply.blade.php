<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Apply for Occupancy · {{ $brandDormName }}</title>
    <link rel="icon" href="{{ $brandFaviconUrl }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&family=Agbalumo&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-light: #a2d9a4;
            --green-dark: #567357;
            --green-darker: #197335;
            --ink: #292420;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }

        .page-wrap { overflow-x: hidden; }

        body {
            font-family: 'Roboto', system-ui, -apple-system, sans-serif;
            color: var(--ink);
            background: linear-gradient(180deg, #567357 0%, #59473f 100%);
            min-height: 100vh;
        }

        a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible {
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

        /* ===== APPLY WIZARD — same skeleton as the login pages ===== */
        .apply-grid {
            display: grid;
            grid-template-columns: 33% 67%;
            align-items: stretch;
            min-height: calc(100vh - 70px);
            padding: clamp(16px, 3vw, 20px) clamp(24px, 5vw, 48px) clamp(24px, 5vw, 48px) 0;
        }

        .apply-left {
            position: relative;
            padding: 16px clamp(20px, 4vw, 40px) clamp(24px, 4vw, 40px) clamp(28px, 6vw, 64px);
            display: flex; flex-direction: column;
        }
        .back-button {
            background: none; border: none; color: #fff; font-size: 22px; cursor: pointer;
            line-height: 1; padding: 0; margin-bottom: 32px; align-self: flex-start;
        }
        .apply-left-content { flex: 1; display: flex; flex-direction: column; justify-content: center; }
        .apply-left h1 {
            color: #fff; font-weight: 700; font-size: clamp(22px, 2.6vw, 30px);
            line-height: 1.2; max-width: 320px;
        }
        .apply-left h1 .accent {
            display: block; color: #44ad65; font-weight: 900;
            font-size: clamp(26px, 3.2vw, 36px); margin-top: 4px;
        }
        .brand-mark {
            margin-top: 32px; width: 130px; height: 130px;
            background: linear-gradient(180deg, #567357 0%, #a2d9a4 100%);
            border-radius: 0 70px 70px 0; display: flex; align-items: center; justify-content: center;
        }
        .brand-mark span { font-family: 'Agbalumo', cursive; font-size: 84px; color: #fff; line-height: 1; }

        .apply-right-wrap { position: relative; padding-top: 16px; display: flex; flex-direction: column; }
        .apply-right {
            background: #eeeded; border-radius: 28px;
            padding: clamp(28px, 4vw, 44px) clamp(24px, 4vw, 48px) clamp(32px, 4vw, 44px);
            flex: 1;
        }

        .apply-title {
            color: #44ad65; font-weight: 900; font-size: clamp(17px, 1.8vw, 21px);
            letter-spacing: 0.02em; text-transform: uppercase; margin-bottom: 20px;
        }

        /* Step indicator */
        .step-track {
            display: flex; align-items: flex-start; justify-content: space-between;
            max-width: 460px; margin: 0 auto 22px; position: relative;
        }
        .step-track::before {
            content: ''; position: absolute; top: 8px; left: 30px; right: 30px;
            height: 2px; background: #44ad65; z-index: 0;
        }
        .step-dot-wrap { position: relative; z-index: 1; display: flex; flex-direction: column; align-items: center; gap: 7px; flex: 1; }
        .step-dot { width: 17px; height: 17px; border-radius: 50%; background: #44ad65; border: 2px solid #44ad65; }
        .step-dot.current { background: #fff; }
        .step-label { font-size: 9.5px; font-weight: 700; color: #194e19; text-transform: uppercase; letter-spacing: 0.02em; text-align: center; }
        .step-divider { height: 1px; background: #d8dde3; margin: 0 0 22px; }

        /* Steps */
        .step-card { display: none; }
        .step-card.active { display: block; }
        .step-card h3 {
            color: #194e19; font-weight: 700; font-size: 16px;
            text-transform: uppercase; letter-spacing: 0.02em; margin-bottom: 18px;
        }
        .step-card h3.spaced { margin-top: 28px; }

        .field-row { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 18px; }
        .field { flex: 1; min-width: 180px; display: flex; flex-direction: column; gap: 8px; }
        .field.full { flex-basis: 100%; }
        .field label {
            font-size: 12px; font-weight: 500; color: #262e36; letter-spacing: -0.01em;
            display: flex; align-items: center; gap: 3px;
        }
        .field label .req { color: #d95117; }
        .field-fieldset { border: none; padding: 0; margin: 0; }
        .field-fieldset legend {
            font-size: 12px; font-weight: 500; color: #262e36; letter-spacing: -0.01em;
            display: flex; align-items: center; gap: 3px; margin-bottom: 8px; padding: 0;
        }
        .field-fieldset legend .req { color: #d95117; }
        .field input[type="text"],
        .field input[type="email"],
        .field input[type="tel"],
        .field input[type="date"],
        .field select,
        .field textarea {
            background: #fff; border: 1px solid #d8dde3; border-radius: 8px;
            padding: 12px 14px; font-size: 13.5px; color: #4c5c6b;
            font-family: inherit; width: 100%; box-shadow: 0 0 2px rgba(23,25,28,0.05);
        }
        .field textarea { resize: vertical; min-height: 70px; }
        .field-hint { font-size: 11.5px; color: #5b6b60; line-height: 1.4; }
        .contract-resign-note { margin-top: 10px; font-size: 12.5px; color: #8a4b0f; background: #fdf3e6; border: 1px solid #f1d6b3; border-radius: 8px; padding: 10px 12px; }
        .field input:focus, .field select:focus, .field textarea:focus {
            outline: none; border-color: #567357;
        }

        .radio-group {
            background: #fff; border: 1px solid #d8dde3; border-radius: 8px;
            padding: 16px; display: flex; flex-direction: column; gap: 12px;
        }
        .radio-option { display: flex; align-items: center; gap: 8px; font-size: 13.5px; color: #4c5c6b; }
        .radio-option input { accent-color: #567357; width: 16px; height: 16px; }

        .file-drop {
            background: #fff; border: 1px solid #d8dde3; border-radius: 8px;
            padding: 16px; min-height: 90px; display: flex; flex-direction: column;
            justify-content: center; gap: 8px; cursor: pointer; position: relative;
        }
        .file-drop input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
        .file-drop .placeholder { font-size: 13px; color: #9aa5ac; }
        .file-drop .filename { font-size: 13px; color: #194e19; font-weight: 500; word-break: break-all; }
        .file-drop-icons { display: flex; gap: 10px; color: #9aa5ac; }
        .file-drop-icons svg { width: 16px; height: 16px; }

        .contract-review-card {
            background: #fff; border: 1px solid #d8dde3; border-radius: 10px; padding: 18px 20px;
        }
        .contract-review-head { display: flex; gap: 12px; align-items: flex-start; margin-bottom: 14px; }
        .contract-review-head svg { width: 26px; height: 26px; color: #567357; flex-shrink: 0; margin-top: 2px; }
        .crc-title { font-size: 14px; font-weight: 700; color: #194e19; }
        .crc-sub { font-size: 12px; color: #5b6b60; margin-top: 3px; line-height: 1.5; }
        .contract-review-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 16px; }
        .crc-btn {
            display: inline-flex; align-items: center; background: #567357; color: #fff; font-weight: 700;
            font-size: 12.5px; padding: 10px 20px; border-radius: 7px; text-decoration: none;
            border: none; cursor: pointer;
        }
        .crc-btn.secondary { background: #fff; color: #567357; border: 1px solid #a6b69f; }

        .step-actions { display: flex; justify-content: space-between; gap: 16px; margin-top: 28px; }
        .btn-nav {
            display: inline-flex; align-items: center; justify-content: center;
            border: none; border-radius: 6px; padding: 14px 32px; font-weight: 700;
            font-size: 14px; letter-spacing: 0.05em; cursor: pointer; transition: background 0.2s;
            text-decoration: none;
        }
        .btn-nav.primary { background: #345234; color: #fff; }
        .btn-nav.primary:hover { background: #26401f; }
        .btn-nav.secondary { background: transparent; color: #567357; border: 1px solid #a6b69f; }
        .btn-nav.secondary:hover { background: #e4e9e3; }
        .btn-nav:disabled { opacity: 0.6; cursor: not-allowed; }

        .spinner {
            display: none; width: 14px; height: 14px; border: 2px solid rgba(255,255,255,0.3);
            border-top: 2px solid #fff; border-radius: 50%; animation: spin 0.8s linear infinite; margin-right: 8px;
        }
        .btn-nav.loading .spinner { display: inline-block; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .form-error {
            display: none; background: #fdf0f0; border: 1px solid #f3cccc; color: #b3261e;
            border-radius: 8px; padding: 12px 14px; font-size: 13px; margin-bottom: 18px;
        }
        .form-error.visible { display: block; }

        /* Step 4 — verify summary */
        .verify-card {
            background: #fff; border: 1px solid #e2e6e3; border-radius: 12px;
            padding: 24px 28px; margin-bottom: 18px;
        }
        .summary-section { margin-bottom: 26px; }
        .summary-section:last-child { margin-bottom: 0; }
        .summary-section h4 {
            color: #194e19; font-weight: 700; font-size: 12.5px; text-transform: uppercase;
            letter-spacing: 0.04em; margin-bottom: 14px; padding-bottom: 8px;
            border-bottom: 2px solid #e2ede3;
        }
        .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 16px 24px; }
        .summary-item { display: flex; flex-direction: column; gap: 4px; }
        .summary-item .label {
            font-size: 10px; color: #5b6b60; text-transform: uppercase;
            letter-spacing: 0.04em; font-weight: 700;
        }
        .summary-item .value { font-size: 13.5px; color: #292420; font-weight: 500; word-break: break-word; }
        .summary-item .value.empty { color: #c2c9c5; font-style: italic; font-weight: 400; }
        .consent-row {
            display: flex; gap: 10px; align-items: flex-start; font-size: 12.5px;
            color: #4b5f4c; line-height: 1.6; margin-bottom: 20px;
        }
        .consent-row input { margin-top: 3px; accent-color: #567357; width: 16px; height: 16px; flex-shrink: 0; }

        /* Step 5 — success */
        #step-5 { text-align: center; padding: 40px 0; }
        .success-badge { width: 72px; height: 72px; margin: 0 auto 20px; display: block; }
        #step-5 h2 { color: #567357; font-weight: 900; font-size: clamp(22px, 2.6vw, 30px); margin-bottom: 14px; }
        #step-5 p { color: #5c6660; font-size: 14px; line-height: 1.6; max-width: 480px; margin: 0 auto 26px; }

        @media (max-width: 1024px) {
            .apply-grid { grid-template-columns: 1fr; padding: 20px 24px 32px; }
            .apply-left { padding: 0 0 24px; }
            .apply-right { padding: 28px 24px 32px; }
            .topnav { padding: 14px 24px; flex-wrap: wrap; }
        }
        @media (max-width: 640px) {
            .apply-left h1 { font-size: 22px; }
            .apply-left h1 .accent { font-size: 26px; }
            .brand-mark { width: 100px; height: 100px; }
            .brand-mark span { font-size: 64px; }
            .field-row { flex-direction: column; }
            .step-label { display: none; }
            .step-actions { flex-direction: column-reverse; }

            /* Topnav: the flex-wrap layout at wider breakpoints packs the
               menu, logo and button groups unpredictably at phone widths,
               so stack them into clear rows instead. Matches welcome.blade.php. */
            .topnav { flex-direction: column; align-items: stretch; gap: 12px; padding-top: 14px; padding-bottom: 14px; }
            .topnav .logo { order: -1; justify-content: center; }
            .topnav .menu { flex: none; justify-content: center; flex-wrap: wrap; row-gap: 8px; }
            .topnav .menu a, .topnav .menu span { padding: 10px 8px; }
            .topnav .buttons { flex: none; justify-content: center; flex-wrap: wrap; row-gap: 10px; }

            /* Form fields: 16px avoids the iOS Safari auto-zoom-on-focus. */
            .field input[type="text"],
            .field input[type="email"],
            .field input[type="tel"],
            .field input[type="date"],
            .field select,
            .field textarea { font-size: 16px; }

            /* Tap targets on the contract-review and modal-footer buttons. */
            .crc-btn { min-height: 44px; }
            .contract-modal-foot { flex-wrap: wrap; }
            .contract-modal-foot button { min-height: 44px; }
            .contract-preview-frame, .contract-preview-loading { height: 260px; }
        }

        /* ===== Contract e-sign modal ===== */
        .contract-signed-status {
            display: flex; align-items: center; gap: 8px; margin-top: 12px;
            font-size: 12.5px; color: #194e19; font-weight: 600;
        }
        .contract-signed-status svg { width: 15px; height: 15px; flex-shrink: 0; }
        .contract-signed-status a { color: #194e19; text-decoration: underline; font-weight: 600; }

        .contract-modal-overlay {
            display: none; position: fixed; inset: 0; background: rgba(20, 26, 22, 0.55);
            z-index: 2000; align-items: center; justify-content: center; padding: 20px; /* above the sticky site nav (z-index 1000) */
        }
        .contract-modal-overlay.open { display: flex; }
        .contract-modal {
            background: #fff; border-radius: 14px; width: min(760px, 100%);
            max-height: 92vh; display: flex; flex-direction: column; overflow: hidden;
        }
        .contract-modal-head {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 22px; border-bottom: 1px solid #e5e9e4;
        }
        .contract-modal-head h3 { margin: 0; font-size: 15.5px; color: #194e19; }
        .contract-modal-close { background: none; border: none; font-size: 22px; color: #8a9690; cursor: pointer; line-height: 1; }
        .contract-modal-body { padding: 18px 22px; overflow-y: auto; flex: 1; }

        .contract-preview-frame {
            width: 100%; height: 360px; border: 1px solid #e5e9e4; border-radius: 8px; background: #f4f6f4;
        }
        .contract-preview-loading {
            display: flex; align-items: center; justify-content: center; height: 360px;
            font-size: 12.5px; color: #5b6b60;
        }

        .signature-pad-label { font-size: 12.5px; font-weight: 600; color: #194e19; margin: 18px 0 8px; }
        .signature-pad-wrap {
            border: 1px solid #d8dde3; border-radius: 8px; background: #fff; position: relative;
        }
        #signatureCanvas, #emergencySignatureCanvas { width: 100%; height: 150px; display: block; touch-action: none; cursor: crosshair; }

        /* ===== Documents viewer (Tenant Agreement / Rules / Fees) ===== */
        .contract-modal { width: min(900px, 100%); }
        .docs-intro { font-size: 12.5px; color: #4b5f4c; line-height: 1.55; margin-bottom: 12px; }
        .doc-tabs { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 10px; }
        .doc-tab {
            border: 1px solid #c9d4c8; background: #fff; color: #345234; border-radius: 999px;
            padding: 8px 14px; font-size: 12px; font-weight: 700; cursor: pointer; min-height: 36px;
        }
        .doc-tab.active { background: #194e19; border-color: #194e19; color: #fff; }
        .doc-viewer {
            border: 1px solid #e0e5df; border-radius: 10px; background: #fcfcfa;
            height: min(62vh, 600px); overflow-y: auto; padding: 18px 20px;
        }
        .doc-viewer { background: #e9ece8; padding: 12px; }
        .doc-page { display: block; margin: 0 auto 12px; background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.18); }
        .doc-viewer { cursor: zoom-in; }
        .doc-viewer-actions { display: flex; justify-content: flex-end; gap: 18px; margin: 6px 0 4px; }
        .doc-link-btn { background: none; border: none; color: #194e19; font-size: 12px; font-weight: 700; text-decoration: underline; cursor: pointer; padding: 8px 0; display: inline-flex; align-items: center; gap: 6px; }
        .doc-link-btn svg { width: 15px; height: 15px; }

        /* Full screen: the same pages, as large as the screen allows. */
        .doc-max-bar { display: none; }
        .doc-viewer-wrap.maximized {
            position: fixed; inset: 0; z-index: 2100; background: #3a403b;
            display: flex; flex-direction: column;
        }
        .doc-viewer-wrap.maximized .doc-max-bar {
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            padding: 10px 16px; background: #194e19; color: #fff;
        }
        .doc-max-title { font-size: 14px; font-weight: 700; }
        .doc-max-exit {
            background: #fff; color: #194e19; border: none; border-radius: 6px;
            padding: 10px 16px; font-size: 13px; font-weight: 700; cursor: pointer; min-height: 40px;
        }
        .doc-viewer-wrap.maximized .doc-viewer {
            flex: 1; height: auto; border: none; border-radius: 0; background: #3a403b; cursor: default; padding: 16px;
        }
        .doc-acks { border: none; padding: 0; margin: 8px 0 4px; }
        .doc-acks legend { font-size: 12.5px; font-weight: 700; color: #194e19; margin-bottom: 2px; padding: 0; }
        .doc-acks .contract-agree-row { margin-top: 8px; }
        .sig-section { border-top: 1px solid #e5e9e4; margin-top: 16px; padding-top: 2px; }
        .sig-section.emergency { background: #f6f8f4; border: 1px solid #e3e9e1; border-radius: 10px; padding: 4px 14px 14px; }
        .sig-help { font-size: 11.5px; color: #5b6b60; margin: -4px 0 8px; line-height: 1.45; }
        .sign-status { flex: 1; font-size: 11.5px; color: #5b6b60; align-self: center; }

        @media (max-width: 640px) {
            .contract-modal-overlay { padding: 0; }
            .contract-modal { max-height: 100vh; height: 100%; border-radius: 0; }
            .contract-modal-body { padding: 14px 16px; }
            .doc-viewer { height: 52vh; padding: 14px; }
            .doc-tab { flex: 1 1 auto; }
            .sign-status { flex-basis: 100%; }
        }
        .signature-pad-hint {
            position: absolute; bottom: 8px; left: 12px; right: 12px; border-top: 1px solid #eceff0;
            font-size: 10.5px; color: #b6bcb9; pointer-events: none;
        }
        .signature-pad-actions { display: flex; justify-content: flex-end; margin-top: 8px; }
        .signature-clear-btn {
            background: none; border: 1px solid #d8dde3; border-radius: 6px; padding: 6px 12px;
            font-size: 11.5px; color: #5b6b60; cursor: pointer;
        }

        .contract-agree-row {
            display: flex; gap: 9px; align-items: flex-start; font-size: 12.5px; color: #262e36;
            line-height: 1.55; margin-top: 16px;
        }
        .contract-agree-row input { margin-top: 3px; accent-color: #194e19; width: 15px; height: 15px; flex-shrink: 0; }

        .contract-modal-foot {
            display: flex; justify-content: flex-end; gap: 10px; padding: 14px 22px;
            border-top: 1px solid #e5e9e4;
        }
        .contract-modal-foot button {
            padding: 10px 18px; border-radius: 8px; font-size: 12.5px; font-weight: 700; cursor: pointer;
        }
        .contract-modal-cancel { background: #fff; border: 1px solid #d8dde3; color: #5b6b60; }
        .contract-modal-sign { background: #194e19; border: none; color: #fff; }
        .contract-modal-sign:disabled { opacity: 0.5; cursor: not-allowed; }
        /* Repeated here because the base rule above would otherwise override the earlier 640px one */
        @media (max-width: 640px) { .contract-preview-loading { height: 260px; } }
        /* Phones: get visitors to the form fast. The full-height grid split its
           leftover space between the tagline and the form, leaving a big gap,
           and the decorative N mark pushed the form down further. Placed last
           so it wins over the base rules above. */
        @media (max-width: 640px) {
            .apply-grid { min-height: 0; padding-top: 12px; }
            .apply-left { padding-bottom: 16px; }
            .apply-left-content { justify-content: flex-start; }
            .brand-mark { display: none; }
            .back-button { margin-bottom: 14px; }
        }
            /* Real NEST.PH logo above the tagline (replaces the old drawn "N"). Phones keep hiding it via the existing .brand-mark rule. */
        img.brand-mark { width: 72px; height: auto; margin: 0 0 28px; background: none; border-radius: 0; }
        @media (min-width: 641px) { img.brand-mark { display: block; } }
</style>
</head>
<body>

    @include('partials.public-nav')

    <div class="page-wrap">
    <div class="apply-grid">
        <div class="apply-left">
            <button class="back-button" type="button" aria-label="Go back" onclick="window.location.href='{{ route('home') }}'">←</button>
            <div class="apply-left-content">
                <img src="{{ asset('images/nestph.png') }}" alt="NEST.PH" class="brand-mark" width="72" height="68">
                <h1>Malayo sa bahay,<span class="accent">pero at home.</span></h1>
            </div>
        </div>

        <div class="apply-right-wrap">
            <div class="apply-right">
                <div class="apply-title">Apply for Occupancy</div>

                <div class="step-track" id="stepTrack">
                    <div class="step-dot-wrap"><div class="step-dot current" data-dot="1"></div><span class="step-label">Personal<br>Information</span></div>
                    <div class="step-dot-wrap"><div class="step-dot" data-dot="2"></div><span class="step-label">Contact<br>Information</span></div>
                    <div class="step-dot-wrap"><div class="step-dot" data-dot="3"></div><span class="step-label">Room<br>Information</span></div>
                </div>
                <div class="step-divider"></div>

                <div class="form-error" id="formError" role="alert"></div>

                {{-- STEP 1 — Personal Information --}}
                <div class="step-card active" id="step-1" data-step="1">
                    <h3>Personal Information</h3>
                    <div class="field-row">
                        <div class="field">
                            <label for="first_name">First Name <span class="req">*</span></label>
                            <input type="text" id="first_name" required>
                        </div>
                        <div class="field">
                            <label for="last_name">Last Name <span class="req">*</span></label>
                            <input type="text" id="last_name" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label for="birthdate">Birthdate <span class="req">*</span></label>
                            <input type="date" id="birthdate" required max="{{ now()->subYears(18)->toDateString() }}">
                            <span class="field-hint">Tenants must be at least 18 years old.</span>
                        </div>
                        <div class="field">
                            <label for="gender">Gender</label>
                            <select id="gender">
                                <option value="">Select</option>
                                <option value="female">Female</option>
                                <option value="male">Male</option>
                                <option value="prefer_not_to_say">Prefer not to say</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="nationality">Nationality</label>
                            <input type="text" id="nationality" placeholder="Filipino">
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field full">
                            <label for="medical_condition">Medical Condition <span class="req">*</span></label>
                            <input type="text" id="medical_condition" placeholder="None, if not applicable" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field full">
                            <label for="occupation">Occupation <span class="req">*</span></label>
                            <input type="text" id="occupation" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field full">
                            <label for="school_company">School/Company <span class="req">*</span></label>
                            <input type="text" id="school_company" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field full">
                            <label for="school_company_address">School/Company Address <span class="req">*</span></label>
                            <input type="text" id="school_company_address" required>
                        </div>
                    </div>

                    <div class="step-actions">
                        <span></span>
                        <button type="button" class="btn-nav primary" onclick="goNext(1)">NEXT</button>
                    </div>
                </div>

                {{-- STEP 2 — Contact + Emergency Contact Information --}}
                <div class="step-card" id="step-2" data-step="2">
                    <h3>Contact Information</h3>
                    <div class="field-row">
                        <div class="field">
                            <label for="contact_number">Cellphone No. <span class="req">*</span></label>
                            <input type="tel" id="contact_number" placeholder="09-" required>
                        </div>
                        <div class="field">
                            <label for="email">Email</label>
                            <input type="email" id="email">
                        </div>
                        <div class="field">
                            <label for="landline">Landline</label>
                            <input type="text" id="landline">
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field full">
                            <label for="home_address">Home Address <span class="req">*</span></label>
                            <input type="text" id="home_address" required>
                        </div>
                    </div>

                    <h3 class="spaced">Emergency Contact Information</h3>
                    <div class="field-row">
                        <div class="field full">
                            <label for="emergency_contact_name">Fullname <span class="req">*</span></label>
                            <input type="text" id="emergency_contact_name" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label for="emergency_contact_number">Cellphone No. <span class="req">*</span></label>
                            <input type="tel" id="emergency_contact_number" placeholder="09-" required>
                        </div>
                        <div class="field">
                            <label for="emergency_contact_email">Email</label>
                            <input type="email" id="emergency_contact_email">
                        </div>
                        <div class="field">
                            <label for="emergency_contact_landline">Landline</label>
                            <input type="text" id="emergency_contact_landline">
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field full">
                            <label for="emergency_contact_relation">Relation to Tenant <span class="req">*</span></label>
                            <select id="emergency_contact_relation" required>
                                <option value="">Select</option>
                                <option value="parent">Parent</option>
                                <option value="grandparent">Grandparent</option>
                                <option value="sibling">Sibling</option>
                                <option value="child">Child</option>
                                <option value="spouse">Spouse</option>
                                <option value="relative">Relative</option>
                                <option value="guardian">Guardian</option>
                                <option value="friend">Friend</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="step-actions">
                        <button type="button" class="btn-nav secondary" onclick="goBack(2)">BACK</button>
                        <button type="button" class="btn-nav primary" onclick="goNext(2)">NEXT</button>
                    </div>
                </div>

                {{-- STEP 3 — Room Information --}}
                <div class="step-card" id="step-3" data-step="3">
                    <h3>Room Information</h3>
                    <div class="field-row">
                        <div class="field">
                            <label for="preferred_start_date">Preferred Start Date <span class="req">*</span></label>
                            <input type="date" id="preferred_start_date" required>
                        </div>
                        <div class="field">
                            <label for="room_select">Room No <span class="req">*</span></label>
                            <select id="room_select" required>
                                <option value="">Loading rooms…</option>
                            </select>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label for="tenant_end_date">Tenant End Date (optional)</label>
                            <input type="date" id="tenant_end_date">
                            <span class="field-hint" id="endDateHint">Minimum stay is {{ $minimumStayMonths }} months. Leave blank to end on the last day of month {{ $minimumStayMonths }}.</span>
                        </div>
                        <div class="field">
                            <label for="bed_select">Bed No <span class="req">*</span></label>
                            <select id="bed_select" required>
                                <option value="">Select a room first</option>
                            </select>
                        </div>
                    </div>

                    <div class="field-row">
                        <fieldset class="field full field-fieldset">
                            <legend>Type of Tenant <span class="req">*</span></legend>
                            <div class="radio-group">
                                <label class="radio-option"><input type="radio" name="type_of_tenant" value="student" required> Student</label>
                                <label class="radio-option"><input type="radio" name="type_of_tenant" value="working_student" required> Working Student</label>
                                <label class="radio-option"><input type="radio" name="type_of_tenant" value="full_time_employee" required> Full-time Employee</label>
                                <label class="radio-option"><input type="radio" name="type_of_tenant" value="part_time_employee" required> Part-time Employee</label>
                            </div>
                        </fieldset>
                    </div>

                    <div class="field-row">
                        <div class="field full">
                            <div class="contract-review-card">
                                <div class="contract-review-head">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
                                    <div>
                                        <div class="crc-title">Read and Sign the Dormitory Documents</div>
                                        <div class="crc-sub">The Tenant Agreement, the Rules and Regulations, and the Payments and Fees Schedule, filled in with your details. You sign, and your emergency contact signs too. No printing or scanning needed.</div>
                                    </div>
                                </div>

                                <div class="contract-review-actions">
                                    <button type="button" class="crc-btn" id="openContractModalBtn">Read &amp; Sign Documents</button>
                                </div>

                                <div class="contract-signed-status" id="contractSignedStatus" style="display:none;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                                    <span>Documents signed on <span id="contractSignedDate"></span>.</span>
                                    <a href="#" id="viewSignedContractLink" target="_blank" rel="noopener">View signed copy</a>
                                </div>
                            </div>

                            <p class="contract-resign-note" id="contractResignNote" role="status" style="display:none;"></p>
                            <input type="hidden" id="contract_acceptance" value="0">
                            <input type="hidden" id="signed_contract_path" value="">
                        </div>
                    </div>

                    <div class="field-row">
                        <div class="field">
                            <label for="id_document">ID <span class="req">*</span></label>
                            <div class="file-drop" id="idDrop">
                                <input type="file" id="id_document" accept=".jpg,.jpeg,.png,.pdf" required>
                                <span class="placeholder" id="idPlaceholder">Add file (JPG, PNG, or PDF)</span>
                            </div>
                        </div>
                    </div>

                    <div class="step-actions">
                        <button type="button" class="btn-nav secondary" onclick="goBack(3)">BACK</button>
                        <button type="button" class="btn-nav primary" onclick="goNextFromStep3()">NEXT</button>
                    </div>
                </div>

                {{-- STEP 4 — Verify Your Information --}}
                <div class="step-card" id="step-4" data-step="4">
                    <h3>Verify Your Information</h3>

                    <div class="verify-card">
                        <div class="summary-section">
                            <h4>Personal Information</h4>
                            <div class="summary-grid">
                                <div class="summary-item"><span class="label">Full Name</span><span class="value" id="sum_full_name"></span></div>
                                <div class="summary-item"><span class="label">Birthdate</span><span class="value" id="sum_birthdate"></span></div>
                                <div class="summary-item"><span class="label">Gender</span><span class="value" id="sum_gender"></span></div>
                                <div class="summary-item"><span class="label">Nationality</span><span class="value" id="sum_nationality"></span></div>
                                <div class="summary-item"><span class="label">Medical Condition</span><span class="value" id="sum_medical_condition"></span></div>
                                <div class="summary-item"><span class="label">Occupation</span><span class="value" id="sum_occupation"></span></div>
                                <div class="summary-item"><span class="label">School/Company</span><span class="value" id="sum_school_company"></span></div>
                                <div class="summary-item"><span class="label">School/Company Address</span><span class="value" id="sum_school_company_address"></span></div>
                            </div>
                        </div>

                        <div class="summary-section">
                            <h4>Contact Information</h4>
                            <div class="summary-grid">
                                <div class="summary-item"><span class="label">Cellphone No.</span><span class="value" id="sum_contact_number"></span></div>
                                <div class="summary-item"><span class="label">Email</span><span class="value" id="sum_email"></span></div>
                                <div class="summary-item"><span class="label">Landline</span><span class="value" id="sum_landline"></span></div>
                                <div class="summary-item"><span class="label">Home Address</span><span class="value" id="sum_home_address"></span></div>
                            </div>
                        </div>

                        <div class="summary-section">
                            <h4>Emergency Contact Information</h4>
                            <div class="summary-grid">
                                <div class="summary-item"><span class="label">Full Name</span><span class="value" id="sum_emergency_contact_name"></span></div>
                                <div class="summary-item"><span class="label">Cellphone No.</span><span class="value" id="sum_emergency_contact_number"></span></div>
                                <div class="summary-item"><span class="label">Email</span><span class="value" id="sum_emergency_contact_email"></span></div>
                                <div class="summary-item"><span class="label">Landline</span><span class="value" id="sum_emergency_contact_landline"></span></div>
                                <div class="summary-item"><span class="label">Relation to Tenant</span><span class="value" id="sum_emergency_contact_relation"></span></div>
                            </div>
                        </div>

                        <div class="summary-section">
                            <h4>Room Information</h4>
                            <div class="summary-grid">
                                <div class="summary-item"><span class="label">Preferred Start Date</span><span class="value" id="sum_start_date"></span></div>
                                <div class="summary-item"><span class="label">Tenant End Date</span><span class="value" id="sum_end_date"></span></div>
                                <div class="summary-item"><span class="label">Room</span><span class="value" id="sum_room"></span></div>
                                <div class="summary-item"><span class="label">Bed</span><span class="value" id="sum_bed"></span></div>
                                <div class="summary-item"><span class="label">Type of Tenant</span><span class="value" id="sum_tenant_type"></span></div>
                                <div class="summary-item"><span class="label">ID File</span><span class="value" id="sum_id_file"></span></div>
                                <div class="summary-item"><span class="label">Signed Documents</span><span class="value" id="sum_contract_file"></span></div>
                            </div>
                        </div>
                    </div>

                    <label class="consent-row">
                        <input type="checkbox" id="dpa_consent" required>
                        <span>By approving, I verify the accuracy of the provided information. I am aware of the rental rates, payment schedule, and associated penalties for late payment. I also understand and agree to abide by all dormitory rules and regulations.</span>
                    </label>

                    <div class="step-actions">
                        <button type="button" class="btn-nav secondary" onclick="goBack(4)">BACK</button>
                        <button type="button" class="btn-nav primary" id="registerBtn" onclick="submitApplication()">
                            <span class="spinner"></span>
                            APPROVE &amp; REGISTER
                        </button>
                    </div>
                </div>

                {{-- STEP 5 — Success --}}
                <div class="step-card" id="step-5" data-step="5">
                    <svg class="success-badge" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M32 2 L37.5 7.5 L45 5 L47.5 12.5 L55 15 L52.5 22.5 L58 28 L52.5 33.5 L55 41 L47.5 43.5 L45 51 L37.5 48.5 L32 54 L26.5 48.5 L19 51 L16.5 43.5 L9 41 L11.5 33.5 L6 28 L11.5 22.5 L9 15 L16.5 12.5 L19 5 L26.5 7.5 Z" fill="#5ea86a"/>
                        <path d="M21 32 L28 39 L43 24" stroke="#fff" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </svg>
                    <h2>Application Successful</h2>
                    <p id="successAppNumber" style="display:none; font-weight:700; color:#194e19; margin-bottom:6px;"></p>
                    <p>Please wait for the administrator's review and approval. Kindly check your email inbox regularly for updates regarding your application status.</p>
                    <a href="{{ route('home') }}" class="btn-nav primary" style="display:inline-flex;">BACK TO HOME</a>
                </div>

            </div>
        </div>
    </div>
    </div>

    <div class="contract-modal-overlay" id="contractModalOverlay">
        <div class="contract-modal" role="dialog" aria-modal="true" aria-labelledby="contractModalTitle">
            <div class="contract-modal-head">
                <h3 id="contractModalTitle">Review &amp; Sign Documents</h3>
                <button type="button" class="contract-modal-close" id="closeContractModalBtn" aria-label="Close">&times;</button>
            </div>
            <div class="contract-modal-body">
                <p class="docs-intro">These are the dormitory's documents, filled in with your details. Read all three, then you and your emergency contact sign below.</p>

                <div class="doc-tabs" role="tablist" aria-label="Documents to sign">
                    <button type="button" role="tab" class="doc-tab active" data-doc="agreement" aria-selected="true">1. Tenant Agreement</button>
                    <button type="button" role="tab" class="doc-tab" data-doc="rules" aria-selected="false">2. Rules &amp; Regulations</button>
                    <button type="button" role="tab" class="doc-tab" data-doc="fees" aria-selected="false">3. Payments &amp; Fees</button>
                </div>
                <div class="doc-viewer-wrap" id="docViewerWrap">
                    <div class="doc-max-bar">
                        <span class="doc-max-title" id="docMaxTitle">Tenant Agreement</span>
                        <button type="button" class="doc-max-exit" id="exitMaximizeBtn">Exit full screen</button>
                    </div>
                    <div class="doc-viewer" id="docViewer" tabindex="0" aria-live="polite" title="Click to view full screen">
                        <div class="contract-preview-loading" id="contractPreviewLoading">Preparing your documents...</div>
                    </div>
                </div>
                <div class="doc-viewer-actions">
                    <button type="button" class="doc-link-btn" id="maximizeDocBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"/></svg>
                        View full screen
                    </button>
                    <button type="button" class="doc-link-btn" id="downloadDocsBtn">Download as PDF</button>
                </div>

                <fieldset class="doc-acks">
                    <legend>Confirm you have read each document</legend>
                    <label class="contract-agree-row"><input type="checkbox" data-ack="agreement"> <span>I have read and agree to the <strong>Dormitory Tenant Agreement</strong>.</span></label>
                    <label class="contract-agree-row"><input type="checkbox" data-ack="rules"> <span>I have read and agree to follow the <strong>Dormitory Rules and Regulations</strong>.</span></label>
                    <label class="contract-agree-row"><input type="checkbox" data-ack="fees"> <span>I have read and agree to the <strong>Payments and Fees Schedule</strong>.</span></label>
                </fieldset>

                <div class="sig-section">
                    <div class="signature-pad-label">Your signature (tenant)</div>
                    <p class="sig-help">Goes on all three documents.</p>
                    <div class="signature-pad-wrap">
                        <canvas id="signatureCanvas" aria-label="Tenant signature pad"></canvas>
                        <div class="signature-pad-hint">Sign above this line</div>
                    </div>
                    <div class="signature-pad-actions">
                        <button type="button" class="signature-clear-btn" id="clearSignatureBtn">Clear</button>
                    </div>
                </div>

                <div class="sig-section emergency">
                    <div class="signature-pad-label">Emergency contact's signature</div>
                    <p class="sig-help"><span id="emergencySignerName">Your emergency contact</span> must sign this themselves. If they are not with you, hand them your phone or finish the application when they are.</p>
                    <div class="signature-pad-wrap">
                        <canvas id="emergencySignatureCanvas" aria-label="Emergency contact signature pad"></canvas>
                        <div class="signature-pad-hint">Emergency contact signs above this line</div>
                    </div>
                    <div class="signature-pad-actions">
                        <button type="button" class="signature-clear-btn" id="clearEmergencySignatureBtn">Clear</button>
                    </div>
                    <label class="contract-agree-row">
                        <input type="checkbox" id="emergencyConsentCheckbox">
                        <span><strong>Optional, for the emergency contact:</strong> I agree to receive billing reminders and overdue notices for the tenant's account. These only state the amount due, the due date and any penalty (Agreement Section 9.3). I can stop them anytime by emailing the dormitory.</span>
                    </label>
                </div>
            </div>
            <div class="contract-modal-foot">
                <span class="sign-status" id="signStatus" aria-live="polite"></span>
                <button type="button" class="contract-modal-cancel" id="cancelContractModalBtn">Cancel</button>
                <button type="button" class="contract-modal-sign" id="confirmSignBtn" disabled>Sign Documents</button>
            </div>
        </div>
    </div>

<script>
    const STEP_LABELS = {
        1: 'PERSONAL INFORMATION',
        2: 'CONTACT INFORMATION',
        3: 'ROOM INFORMATION',
    };

    function showFormError(message) {
        const box = document.getElementById('formError');
        box.textContent = message;
        box.classList.add('visible');
    }

    function clearFormError() {
        document.getElementById('formError').classList.remove('visible');
    }

    function validateStep(stepEl) {
        const fields = stepEl.querySelectorAll('[required]');
        for (const field of fields) {
            if (field.type === 'radio') {
                const group = stepEl.querySelectorAll(`[name="${field.name}"]`);
                const checked = Array.from(group).some(r => r.checked);
                if (!checked) {
                    showFormError('Please select a type of tenant before continuing.');
                    return false;
                }
                continue;
            }
            if (!field.reportValidity()) {
                return false;
            }
        }
        return true;
    }

    function setActiveStep(step) {
        document.querySelectorAll('.step-card').forEach(c => c.classList.remove('active'));
        document.getElementById('step-' + step).classList.add('active');

        const trackStep = Math.min(step, 3);
        document.querySelectorAll('.step-dot').forEach(dot => {
            const n = parseInt(dot.dataset.dot, 10);
            dot.classList.toggle('current', n === trackStep && step <= 3);
        });

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function goNext(currentStep) {
        clearFormError();
        const stepEl = document.getElementById('step-' + currentStep);
        if (!validateStep(stepEl)) return;

        if (currentStep === 3) {
            buildVerifySummary();
        }

        setActiveStep(currentStep + 1);
    }

    function goBack(currentStep) {
        clearFormError();
        setActiveStep(currentStep - 1);
    }

    function val(id) {
        const el = document.getElementById(id);
        return el ? el.value : '';
    }

    function checkedRadioValue(name) {
        const el = document.querySelector(`input[name="${name}"]:checked`);
        return el ? el.value : '';
    }

    function formatDate(value) {
        if (!value) return '';
        const d = new Date(value + 'T00:00:00');
        if (isNaN(d.getTime())) return value;
        return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    // Payments and Fees Schedule 4.1: minimum stay; with no end date the
    // stay ends on the last day of the last minimum month.
    const MIN_STAY_MONTHS = {{ (int) $minimumStayMonths }};

    function toIsoDate(d) {
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    function defaultEndDate() {
        const start = val('preferred_start_date');
        if (!start) return '';
        const d = new Date(start + 'T00:00:00');
        const day = d.getDate();
        d.setDate(1);
        d.setMonth(d.getMonth() + MIN_STAY_MONTHS);
        // start + N months - 1 day, then the end of that month
        const lastDay = new Date(d.getFullYear(), d.getMonth() + 1, 0).getDate();
        d.setDate(Math.min(day, lastDay));
        d.setDate(d.getDate() - 1);
        return toIsoDate(new Date(d.getFullYear(), d.getMonth() + 1, 0));
    }

    document.getElementById('preferred_start_date').addEventListener('change', function () {
        const end = document.getElementById('tenant_end_date');
        const min = defaultEndDate();
        end.min = min;
        document.getElementById('endDateHint').textContent = min
            ? `Minimum stay is ${MIN_STAY_MONTHS} months. Leave blank to end on ${formatDate(min)}, or pick a later date.`
            : `Minimum stay is ${MIN_STAY_MONTHS} months.`;
    });

    function capitalize(value) {
        if (!value) return '';
        return value.charAt(0).toUpperCase() + value.slice(1).replace(/_/g, ' ');
    }

    function setSummary(id, value) {
        const el = document.getElementById(id);
        if (!el) return;
        if (value && String(value).trim()) {
            el.textContent = value;
            el.classList.remove('empty');
        } else {
            el.textContent = '—';
            el.classList.add('empty');
        }
    }

    function buildVerifySummary() {
        const roomText = document.getElementById('room_select').selectedOptions[0]?.textContent.replace(/\s*\(\d+ vacant\)$/, '') || '';
        const bedText = document.getElementById('bed_select').selectedOptions[0]?.textContent || '';
        const tenantTypeLabel = {
            student: 'Student',
            working_student: 'Working Student',
            full_time_employee: 'Full-time Employee',
            part_time_employee: 'Part-time Employee',
        }[checkedRadioValue('type_of_tenant')] || '';

        setSummary('sum_full_name', (val('first_name') + ' ' + val('last_name')).trim());
        setSummary('sum_birthdate', formatDate(val('birthdate')));
        setSummary('sum_gender', capitalize(val('gender')));
        setSummary('sum_nationality', val('nationality'));
        setSummary('sum_medical_condition', val('medical_condition'));
        setSummary('sum_occupation', val('occupation'));
        setSummary('sum_school_company', val('school_company'));
        setSummary('sum_school_company_address', val('school_company_address'));

        setSummary('sum_contact_number', val('contact_number'));
        setSummary('sum_email', val('email'));
        setSummary('sum_landline', val('landline'));
        setSummary('sum_home_address', val('home_address'));

        setSummary('sum_emergency_contact_name', val('emergency_contact_name'));
        setSummary('sum_emergency_contact_number', val('emergency_contact_number'));
        setSummary('sum_emergency_contact_email', val('emergency_contact_email'));
        setSummary('sum_emergency_contact_landline', val('emergency_contact_landline'));
        setSummary('sum_emergency_contact_relation', capitalize(val('emergency_contact_relation')));

        setSummary('sum_start_date', formatDate(val('preferred_start_date')));
        setSummary('sum_end_date', val('tenant_end_date') ? formatDate(val('tenant_end_date')) : (defaultEndDate() ? formatDate(defaultEndDate()) + ' (minimum stay)' : ''));
        setSummary('sum_room', roomText);
        setSummary('sum_bed', bedText);
        setSummary('sum_tenant_type', tenantTypeLabel);
        setSummary('sum_id_file', document.getElementById('id_document').files[0]?.name);
        setSummary('sum_contract_file', document.getElementById('signed_contract_path').value ? 'Signed by you and your emergency contact' : 'Not signed yet');
    }

    // ===== File drop labels =====
    document.getElementById('id_document').addEventListener('change', function () {
        document.getElementById('idPlaceholder').textContent = this.files[0]?.name || 'Add file (JPG, PNG, or PDF)';
    });

    // ===== Room -> Bed cascading dropdowns =====
    const roomSelect = document.getElementById('room_select');
    const bedSelect = document.getElementById('bed_select');

    fetch('/public-api/rooms')
        .then(r => r.json())
        .then(rooms => {
            const vacant = rooms.filter(r => r.available_beds > 0);
            roomSelect.innerHTML = '<option value="">Select a room</option>';
            vacant.forEach(room => {
                const opt = document.createElement('option');
                opt.value = room.id;
                const price = room.price_per_bed ? ` · ₱${Number(room.price_per_bed).toLocaleString('en-PH')}/bed` : '';
                opt.textContent = `${room.room_no}${room.room_type ? ' · ' + room.room_type : ''}${price} (${room.available_beds} vacant)`;
                roomSelect.appendChild(opt);
            });
            if (vacant.length === 0) {
                roomSelect.innerHTML = '<option value="">No vacant rooms right now</option>';
            }
        })
        .catch(() => {
            roomSelect.innerHTML = '<option value="">Could not load rooms</option>';
        });

    roomSelect.addEventListener('change', function () {
        bedSelect.innerHTML = '<option value="">Loading beds…</option>';
        if (!this.value) {
            bedSelect.innerHTML = '<option value="">Select a room first</option>';
            return;
        }
        fetch(`/public-api/rooms/${this.value}/beds`)
            .then(r => r.json())
            .then(beds => {
                bedSelect.innerHTML = '<option value="">Select a bed</option>';
                beds.forEach(bed => {
                    const opt = document.createElement('option');
                    opt.value = bed.id;
                    opt.textContent = bed.bed_label;
                    bedSelect.appendChild(opt);
                });
                if (beds.length === 0) {
                    bedSelect.innerHTML = '<option value="">No vacant beds in this room</option>';
                }
            })
            .catch(() => {
                bedSelect.innerHTML = '<option value="">Could not load beds</option>';
            });
    });

    // ===== Final submission =====
    function submitApplication() {
        clearFormError();
        const step4 = document.getElementById('step-4');
        if (!validateStep(step4)) return;

        const button = document.getElementById('registerBtn');
        button.disabled = true;
        button.classList.add('loading');

        const formData = new FormData();
        formData.append('first_name', val('first_name'));
        formData.append('last_name', val('last_name'));
        formData.append('birthdate', val('birthdate'));
        formData.append('gender', val('gender'));
        formData.append('nationality', val('nationality'));
        formData.append('medical_condition', val('medical_condition'));
        formData.append('occupation', val('occupation'));
        formData.append('school_company', val('school_company'));
        formData.append('school_company_address', val('school_company_address'));

        formData.append('contact_number', val('contact_number'));
        formData.append('email', val('email'));
        formData.append('landline', val('landline'));
        formData.append('home_address', val('home_address'));

        formData.append('emergency_contact_name', val('emergency_contact_name'));
        formData.append('emergency_contact_number', val('emergency_contact_number'));
        formData.append('emergency_contact_email', val('emergency_contact_email'));
        formData.append('emergency_contact_landline', val('emergency_contact_landline'));
        formData.append('emergency_contact_relation', val('emergency_contact_relation'));

        formData.append('bed_id', bedSelect.value);
        formData.append('preferred_start_date', val('preferred_start_date'));
        formData.append('tenant_end_date', val('tenant_end_date'));
        formData.append('type_of_tenant', checkedRadioValue('type_of_tenant'));

        const idFile = document.getElementById('id_document').files[0];
        if (idFile) formData.append('id_document', idFile);
        formData.append('signed_contract_path', document.getElementById('signed_contract_path').value);

        formData.append('dpa_consent', document.getElementById('dpa_consent').checked ? '1' : '0');
        formData.append('contract_acceptance', document.getElementById('contract_acceptance').value === '1' ? '1' : '0');

        fetch('/api/applications', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
            .then(async response => {
                const data = await response.json();
                button.disabled = false;
                button.classList.remove('loading');

                if (!response.ok) {
                    const firstError = data.errors ? Object.values(data.errors)[0][0] : null;
                    showFormError(firstError || data.message || 'Something went wrong. Please review your answers and try again.');
                    return;
                }

                if (data.application && data.application.id) {
                    const numEl = document.getElementById('successAppNumber');
                    numEl.textContent = 'Application #' + data.application.id;
                    numEl.style.display = 'block';
                }

                setActiveStep(5);
            })
            .catch(() => {
                button.disabled = false;
                button.classList.remove('loading');
                showFormError('Something went wrong. Please check your connection and try again.');
            });
    }

    // ===== Sticky nav shadow =====
    window.addEventListener('scroll', function () {
        const nav = document.querySelector('.topnav');
        if (window.scrollY > 10) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    });
</script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js" defer></script>
<script>
(function(){
    function val(id){ return document.getElementById(id)?.value || ''; }
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function goNextFromStep3(){
        if (!document.getElementById('signed_contract_path').value) {
            showFormError('Please read and sign the dormitory documents before continuing.');
            return;
        }
        goNext(3);
    }
    window.goNextFromStep3 = goNextFromStep3;

    // The details printed on the documents. Changing any of them after
    // signing means the signed copy no longer matches, so it's cleared.
    const SIGNED_FIELDS = ['first_name', 'last_name', 'home_address', 'contact_number', 'email',
        'emergency_contact_name', 'emergency_contact_number', 'emergency_contact_relation',
        'preferred_start_date', 'tenant_end_date', 'bed_select'];

    function collectContractFields(){
        return {
            first_name: val('first_name'),
            last_name: val('last_name'),
            home_address: val('home_address'),
            contact_number: val('contact_number'),
            email: val('email'),
            emergency_contact_name: val('emergency_contact_name'),
            emergency_contact_number: val('emergency_contact_number'),
            emergency_contact_relation: val('emergency_contact_relation'),
            bed_id: val('bed_select'),
            preferred_start_date: val('preferred_start_date'),
            tenant_end_date: val('tenant_end_date'),
        };
    }

    function clearSignedState(reason){
        if (!document.getElementById('signed_contract_path').value) return;
        document.getElementById('signed_contract_path').value = '';
        document.getElementById('contract_acceptance').value = '0';
        document.getElementById('contractSignedStatus').style.display = 'none';
        const note = document.getElementById('contractResignNote');
        note.textContent = reason;
        note.style.display = 'block';
    }
    SIGNED_FIELDS.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', () => clearSignedState('You changed details that appear on the documents, so please review and sign them again.'));
    });

    // ===== Signature pads (one per signer) =====
    function createSignaturePad(canvas, onChange){
        const ctx = canvas.getContext('2d');
        let drawing = false, hasInk = false;
        let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;

        function resize(){
            const ratio = window.devicePixelRatio || 1;
            const rect = canvas.getBoundingClientRect();
            canvas.width = rect.width * ratio;
            canvas.height = rect.height * ratio;
            ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
            ctx.strokeStyle = '#1f2a22';
            ctx.lineWidth = 2;
            ctx.lineJoin = 'round';
            ctx.lineCap = 'round';
        }
        function reset(){
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            hasInk = false;
            minX = minY = Infinity; maxX = maxY = -Infinity;
            onChange();
        }
        function pos(e){
            const rect = canvas.getBoundingClientRect();
            const p = e.touches ? e.touches[0] : e;
            return { x: p.clientX - rect.left, y: p.clientY - rect.top };
        }
        function bound(p){
            minX = Math.min(minX, p.x); minY = Math.min(minY, p.y);
            maxX = Math.max(maxX, p.x); maxY = Math.max(maxY, p.y);
        }
        function start(e){
            drawing = true;
            const p = pos(e);
            bound(p);
            ctx.beginPath();
            ctx.moveTo(p.x, p.y);
            e.preventDefault();
        }
        function move(e){
            if (!drawing) return;
            const p = pos(e);
            bound(p);
            ctx.lineTo(p.x, p.y);
            ctx.stroke();
            if (!hasInk) { hasInk = true; onChange(); }
            e.preventDefault();
        }
        function end(){ drawing = false; }

        canvas.addEventListener('mousedown', start);
        canvas.addEventListener('mousemove', move);
        canvas.addEventListener('mouseup', end);
        canvas.addEventListener('mouseleave', end);
        canvas.addEventListener('touchstart', start, { passive: false });
        canvas.addEventListener('touchmove', move, { passive: false });
        canvas.addEventListener('touchend', end);

        // Crops to the ink, so the signature sits neatly on the PDF line.
        function toDataUrl(){
            const ratio = window.devicePixelRatio || 1;
            const pad = 10;
            const sx = Math.max(0, (minX - pad) * ratio);
            const sy = Math.max(0, (minY - pad) * ratio);
            const sw = Math.min(canvas.width - sx, (maxX - minX + pad * 2) * ratio);
            const sh = Math.min(canvas.height - sy, (maxY - minY + pad * 2) * ratio);
            const out = document.createElement('canvas');
            out.width = sw; out.height = sh;
            out.getContext('2d').drawImage(canvas, sx, sy, sw, sh, 0, 0, sw, sh);
            return out.toDataURL('image/png');
        }

        return { resize, reset, hasInk: () => hasInk, toDataUrl };
    }

    const overlay = document.getElementById('contractModalOverlay');
    const viewer = document.getElementById('docViewer');
    const wrap = document.getElementById('docViewerWrap');
    const DOC_TITLES = { agreement: 'Dormitory Tenant Agreement', rules: 'Dormitory Rules and Regulations', fees: 'Payments and Fees Schedule' };

    // "View full screen": the viewer fills the screen; pages are redrawn at
    // the bigger size (the PDF is already loaded, so it's quick).
    function setMaximized(on){
        if (wrap.classList.contains('maximized') === on) return;
        wrap.classList.toggle('maximized', on);
        document.getElementById('docMaxTitle').textContent = DOC_TITLES[currentDoc] || '';
        showDoc(currentDoc);
        (on ? document.getElementById('exitMaximizeBtn') : document.getElementById('maximizeDocBtn')).focus();
    }
    const signBtn = document.getElementById('confirmSignBtn');
    const signStatus = document.getElementById('signStatus');
    const ackBoxes = Array.from(document.querySelectorAll('[data-ack]'));
    const tabs = Array.from(document.querySelectorAll('.doc-tab'));
    // PDF.js (from cdnjs) draws the real filled-in PDF pages, so applicants
    // see exactly the dorm's documents (letterhead and all) that they sign.
    const PDFJS_WORKER = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    let pdfCache = {};      // document key -> loaded PDF, for the current form details
    let currentDoc = 'agreement';
    let renderToken = 0;    // ignores a slow render if the user switched tabs

    const tenantPad = createSignaturePad(document.getElementById('signatureCanvas'), updateSignButtonState);
    const emergencyPad = createSignaturePad(document.getElementById('emergencySignatureCanvas'), updateSignButtonState);

    document.getElementById('clearSignatureBtn').addEventListener('click', () => tenantPad.reset());
    document.getElementById('clearEmergencySignatureBtn').addEventListener('click', () => emergencyPad.reset());
    ackBoxes.forEach(b => b.addEventListener('change', updateSignButtonState));

    function updateSignButtonState(){
        const missing = [];
        if (!ackBoxes.every(b => b.checked)) missing.push('confirm all three documents');
        if (!tenantPad.hasInk()) missing.push('your signature');
        if (!emergencyPad.hasInk()) missing.push("your emergency contact's signature");
        signBtn.disabled = missing.length > 0;
        signStatus.textContent = missing.length ? 'Still needed: ' + missing.join(', ') + '.' : 'Ready to sign.';
    }

    function loadingMessage(text){
        viewer.innerHTML = '';
        const div = document.createElement('div');
        div.className = 'contract-preview-loading';
        div.textContent = text;
        viewer.appendChild(div);
    }

    async function loadPdf(key){
        if (pdfCache[key]) return pdfCache[key];
        const res = await fetch('/api/applications/contract-preview', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify(Object.assign(collectContractFields(), { document: key })),
        });
        if (!res.ok) throw new Error('Could not load the document.');
        const data = new Uint8Array(await res.arrayBuffer());
        pdfCache[key] = await window.pdfjsLib.getDocument({ data }).promise;
        return pdfCache[key];
    }

    async function showDoc(key){
        currentDoc = key;
        document.getElementById('docMaxTitle').textContent = DOC_TITLES[key] || '';
        const token = ++renderToken;
        tabs.forEach(t => {
            const on = t.dataset.doc === key;
            t.classList.toggle('active', on);
            t.setAttribute('aria-selected', on ? 'true' : 'false');
        });
        loadingMessage('Preparing the document...');

        try {
            if (!window.pdfjsLib) throw new Error('viewer');
            window.pdfjsLib.GlobalWorkerOptions.workerSrc = PDFJS_WORKER;
            const pdf = await loadPdf(key);
            if (token !== renderToken) return;

            const pages = document.createDocumentFragment();
            // Full screen caps the page width so text stays a comfortable size.
            const width = Math.min(viewer.clientWidth - 32, wrap.classList.contains('maximized') ? 1000 : 2000);
            const ratio = window.devicePixelRatio || 1;
            for (let n = 1; n <= pdf.numPages; n++) {
                const page = await pdf.getPage(n);
                const base = page.getViewport({ scale: 1 });
                const viewport = page.getViewport({ scale: (width / base.width) * ratio });
                const canvas = document.createElement('canvas');
                canvas.className = 'doc-page';
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                canvas.style.width = width + 'px';
                canvas.setAttribute('role', 'img');
                canvas.setAttribute('aria-label', `Page ${n} of ${pdf.numPages}`);
                await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;
                if (token !== renderToken) return;
                pages.appendChild(canvas);
            }
            viewer.innerHTML = '';
            viewer.appendChild(pages);
            viewer.scrollTop = 0;
        } catch (err) {
            if (token !== renderToken) return;
            loadingMessage(err.message === 'viewer'
                ? 'The document viewer could not load. Use "Download as PDF" below to read it.'
                : 'Could not load the document. Please close this and try again.');
        }
    }

    tabs.forEach(t => t.addEventListener('click', () => showDoc(t.dataset.doc)));

    document.getElementById('openContractModalBtn').addEventListener('click', async () => {
        clearFormError();
        if (!val('first_name') || !val('last_name') || !val('bed_select')) {
            showFormError('Please fill in your name and choose a room and bed before reviewing the documents.');
            return;
        }
        if (!val('emergency_contact_name')) {
            showFormError("Please enter your emergency contact's details (step 2) before signing. They sign the agreement too.");
            return;
        }

        document.getElementById('emergencySignerName').textContent = val('emergency_contact_name');
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
        pdfCache = {};
        ackBoxes.forEach(b => b.checked = false);
        document.getElementById('emergencyConsentCheckbox').checked = false;
        tenantPad.resize(); tenantPad.reset();
        emergencyPad.resize(); emergencyPad.reset();
        updateSignButtonState();

        showDoc('agreement');
    });

    document.getElementById('downloadDocsBtn').addEventListener('click', async function(){
        this.disabled = true;
        try {
            const res = await fetch('/api/applications/contract-preview', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify(collectContractFields()),
            });
            if (!res.ok) throw new Error();
            const url = URL.createObjectURL(await res.blob());
            const a = document.createElement('a');
            a.href = url;
            a.download = 'Tenancy-Documents.pdf';
            document.body.appendChild(a);
            a.click();
            a.remove();
        } catch (e) {
            alert('Could not prepare the PDF. Please try again.');
        }
        this.disabled = false;
    });

    function closeModal(){
        wrap.classList.remove('maximized');
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    }
    document.getElementById('closeContractModalBtn').addEventListener('click', closeModal);
    document.getElementById('cancelContractModalBtn').addEventListener('click', closeModal);
    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape' || !overlay.classList.contains('open')) return;
        // Esc leaves full screen first, then closes the window.
        if (wrap.classList.contains('maximized')) setMaximized(false);
        else closeModal();
    });
    document.getElementById('maximizeDocBtn').addEventListener('click', () => setMaximized(true));
    document.getElementById('exitMaximizeBtn').addEventListener('click', () => setMaximized(false));
    viewer.addEventListener('click', () => { if (!wrap.classList.contains('maximized')) setMaximized(true); });

    signBtn.addEventListener('click', async () => {
        signBtn.disabled = true;
        signBtn.textContent = 'Signing...';

        try {
            const payload = collectContractFields();
            payload.signature_image = tenantPad.toDataUrl();
            payload.emergency_signature_image = emergencyPad.toDataUrl();
            payload.emergency_billing_consent = document.getElementById('emergencyConsentCheckbox').checked;
            payload.acknowledged = ackBoxes.filter(b => b.checked).map(b => b.dataset.ack);

            const res = await fetch('/api/applications/contract-sign', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify(payload),
            });
            const data = await res.json();
            if (!res.ok) {
                const first = data.errors ? Object.values(data.errors)[0][0] : null;
                throw new Error(first || data.message || 'Something went wrong while signing.');
            }

            document.getElementById('signed_contract_path').value = data.signed_contract_path;
            document.getElementById('contract_acceptance').value = '1';
            document.getElementById('contractSignedDate').textContent = data.signed_at;
            document.getElementById('viewSignedContractLink').href = data.preview_url;
            document.getElementById('contractSignedStatus').style.display = 'flex';
            document.getElementById('contractResignNote').style.display = 'none';

            closeModal();
        } catch (err) {
            signStatus.textContent = err.message;
        }

        signBtn.textContent = 'Sign Documents';
        updateSignButtonState();
    });
})();
</script>
</body>
</html>
