{{--
    Shared shell for the tenant move-in flow (welcome, payment type,
    payment method, payment, pending). Mirrors the public page skeleton
    (publicinquiry, logintenant, passwords) so the flow reads as one
    continuous site. Include inside <head>, before page-specific styles:
        @include('partials.movein-styles')
--}}
<style>
    :root {
        --green-light: #a2d9a4;
        --green-dark: #567357;
        --green-darker: #197335;
        --green-deep: #194e19;
        --ink: #292420;
        --muted: #5a6b5e;
        --line: #d5ddd6;
        --panel: #eeeded;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    ::selection { background: var(--green-light); color: var(--ink); }

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
    .topnav a:focus-visible, .topnav button:focus-visible, .back-button:focus-visible {
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

    /* ===== Top nav — desktop (phone layout comes from partials.public-nav) ===== */
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
        white-space: nowrap; text-decoration: none; font-family: inherit;
    }
    .btn-white { background: #fff; color: var(--ink); }
    .btn-outline-white { background: transparent; color: #fff; }

    /* ===== Page skeleton — same as the login / inquiry pages ===== */
    .login-grid {
        display: grid; grid-template-columns: 37% 63%; align-items: stretch;
        min-height: calc(100vh - 72px);
        padding: clamp(16px, 3vw, 20px) clamp(24px, 5vw, 60px) clamp(24px, 5vw, 60px) 0;
    }
    .login-left {
        position: relative;
        padding: 16px clamp(20px, 4vw, 40px) clamp(24px, 4vw, 40px) clamp(28px, 6vw, 80px);
        display: flex; flex-direction: column;
    }
    .back-button {
        background: none; border: none; color: #fff; font-size: 22px; cursor: pointer;
        line-height: 1; padding: 4px; margin: -4px 0 28px -4px; align-self: flex-start;
        text-decoration: none; border-radius: 6px;
    }
    .login-left-content { flex: 1; display: flex; flex-direction: column; justify-content: center; }
    .login-left h1 { color: #fff; font-weight: 700; font-size: clamp(24px, 3vw, 34px); line-height: 1.2; max-width: 360px; }
    .login-left h1 .accent { display: block; color: #44ad65; font-weight: 900; font-size: clamp(28px, 3.6vw, 40px); margin-top: 4px; }
    .brand-mark {
        margin-top: 36px; width: 150px; height: 150px;
        background: linear-gradient(180deg, #567357 0%, #a2d9a4 100%);
        border-radius: 0 80px 80px 0; display: flex; align-items: center; justify-content: center;
    }
    .brand-mark span { font-family: 'Agbalumo', cursive; font-size: 96px; color: #fff; line-height: 1; }

    .login-right-wrap { position: relative; padding-top: 16px; display: flex; flex-direction: column; min-width: 0; }
    .login-right {
        background: var(--panel); border-radius: 32px;
        padding: clamp(32px, 5vw, 52px) clamp(24px, 5vw, 56px);
        flex: 1; display: flex; flex-direction: column;
    }
    /* Centered confirmation-style panels (welcome, type, method, pending) */
    .login-right.centered { align-items: center; text-align: center; }
    .login-right.centered .panel-body { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; }

    .login-right h2 { color: var(--green-deep); font-weight: 900; font-size: clamp(22px, 2.8vw, 30px); line-height: 1.2; margin-bottom: 14px; text-wrap: balance; }
    .login-right .lead { color: var(--ink); font-size: 15px; line-height: 1.7; max-width: 52ch; margin: 0 auto 28px; }

    .state-badge { width: 76px; height: 76px; margin: 0 auto 24px; display: block; flex-shrink: 0; }

    /* ===== Move-in progress ===== */
    .movein-steps {
        list-style: none; display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px;
        width: 100%; max-width: 560px; margin: 0 auto 36px; counter-reset: step; text-align: left;
    }
    .movein-steps li { font-size: 12px; font-weight: 500; color: #7c887e; }
    .movein-steps li::before {
        content: ""; display: block; height: 4px; border-radius: 4px; background: var(--line); margin-bottom: 8px;
    }
    .movein-steps li.done { color: var(--green-dark); }
    .movein-steps li.done::before { background: var(--green-dark); }
    .movein-steps li[aria-current="step"] { color: var(--green-deep); font-weight: 700; }
    .movein-steps li[aria-current="step"]::before { background: var(--green-darker); }

    /* ===== Buttons ===== */
    .btn-login {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        min-height: 52px; padding: 0 40px; border: none; border-radius: 8px;
        background: var(--green-dark); color: #fff; font-family: inherit; font-weight: 700; font-size: 14px;
        letter-spacing: 0.05em; text-transform: uppercase; cursor: pointer; text-decoration: none;
        transition: background 0.2s;
    }
    .btn-login:hover:not(:disabled) { background: var(--green-darker); }
    .btn-login:disabled { opacity: 0.5; cursor: not-allowed; }
    .btn-login svg { width: 16px; height: 16px; }

    .btn-secondary {
        display: inline-flex; align-items: center; justify-content: center;
        min-height: 44px; padding: 0 22px; border: 1.5px solid var(--green-dark); border-radius: 8px;
        background: transparent; color: #345234; font-family: inherit; font-weight: 700; font-size: 13px;
        letter-spacing: 0.03em; cursor: pointer; text-decoration: none; transition: background 0.2s;
    }
    .btn-secondary:hover { background: rgba(86,115,87,0.08); }

    .spinner {
        display: none; width: 14px; height: 14px; border: 2px solid rgba(255,255,255,0.3);
        border-top: 2px solid #fff; border-radius: 50%; animation: spin 0.8s linear infinite;
    }
    .loading .spinner { display: inline-block; }
    @keyframes spin { to { transform: rotate(360deg); } }
    @media (prefers-reduced-motion: reduce) { .spinner { animation-duration: 2s; } }

    .form-error {
        display: none; background: #fdf0f0; border: 1px solid #f3cccc; color: #b3261e;
        border-radius: 8px; padding: 12px 14px; font-size: 13px; line-height: 1.5; margin-bottom: 18px;
    }
    .form-error.visible { display: block; }

    /* Rejected move-in proof reason (welcome + payment pages). */
    .rejection-notice {
        width: 100%; text-align: left; background: #fdf0f0; border: 1px solid #f3cccc; color: #b3261e;
        border-radius: 8px; padding: 12px 14px; font-size: 13px; line-height: 1.5; margin-bottom: 18px;
        overflow-wrap: anywhere;
    }
    .rejection-notice strong, .rejection-notice span { display: block; }

    .visually-hidden {
        position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
        overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0;
    }

    @media (max-width: 1024px) {
        .login-grid { grid-template-columns: 1fr; padding: 20px 28px 32px; }
        .login-left { padding: 0 0 24px; }
        .login-right { padding: 40px 28px 32px; }
    }
    /* Phones: get tenants to the task fast — tagline shrinks, the decorative
       N mark goes, the panel fills the width. Matches publicinquiry/passwords. */
    @media (max-width: 640px) {
        .login-grid { min-height: 0; padding: 12px 16px 24px; }
        .login-left { padding-bottom: 16px; }
        .login-left-content { justify-content: flex-start; }
        .login-left h1 { font-size: 22px; }
        .login-left h1 .accent { font-size: 26px; }
        .brand-mark { display: none; }
        .back-button { margin-bottom: 12px; }
        .login-right { border-radius: 24px; padding: 28px 20px 28px; }
        .login-right .lead { font-size: 14.5px; }
        .movein-steps { margin-bottom: 28px; gap: 6px; }
        .movein-steps li { font-size: 11px; }
        .btn-login { width: 100%; padding: 0 20px; }
        input, select, textarea { font-size: 16px !important; } /* avoid iOS zoom-on-focus */
    }
</style>
