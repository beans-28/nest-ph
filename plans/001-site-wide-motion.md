# 001 — Site-wide motion pass (findings 1–7)
Commit: 2f1569e · Severity: HIGH · Status: DONE (applied 2026-11-26)

## Goal
Smoother, faster, consistent motion everywhere. Reduced-motion users keep opacity-only fades (no movement), never a hard "nothing".

## Tokens (public/css/motion.css)
- `--ease-out: cubic-bezier(.23,1,.32,1)` — every enter/hover/press
- `--dur-fast: 150ms` (menus, press) · `--dur: 200ms` (modals, toasts) · `--dur-slow: 280ms`

## Steps
1. Create `public/css/motion.css` (tokens, view transitions, modal/menu entrances, press feedback, reduced-motion fallbacks).
2. In every view, replace the inline `<style>@view-transition…</style>` line with `<link rel="stylesheet" href="/css/motion.css">` (reduced motion keeps the crossfade, which is opacity only).
3. Modals (`.modal-overlay.open`, `.modal-box`): backdrop fade 200ms; box opacity 0→1 + scale .96→1, 200ms ease-out.
4. `.notif-panel.open`, `.pm-panel.open`: opacity + scale .97→1, 150ms, `transform-origin: top right`.
5. Toasts (delinquency, tenanttickets): `transition:.25s` → opacity/transform 200ms ease-out.
6. Replace `cubic-bezier(.2,.8,.2,1)` and `cubic-bezier(0.16,1,0.3,1)` with `var(--ease-out)` fallback-safe value.
7. Breeze components: `ease-in-out` → `ease-out`.
8. Press feedback: `button:active`, `.btn:active` → `scale: .98` (uses `scale` property so existing transforms are untouched).
9. Reduced motion: swap scale/translate entrances for pure fades; let backdrop/lightbox/fab fades still run.

## Verify
- DevTools → Animations panel at 10% speed: open each modal, bell, profile menu, toast.
- DevTools → Rendering → "Emulate prefers-reduced-motion: reduce": everything should fade, nothing should move or scale.
- Run `php artisan test`.
