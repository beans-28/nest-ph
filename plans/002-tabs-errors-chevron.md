# 002 — Tabs, form errors, chevron
Commit: 2f1569e · Severity: LOW · Status: DONE (2026-11-26)

- motion.css: .tab-panel.active / .view.active fade 150ms; .review-modal-overlay.open and .img-viewer.open fade 200ms.
- motion.css: *-error.visible fade + 3px drop, 200ms ease-out (fade only under reduced motion).
- platformadmin.blade.php: .chev rotate uses cubic-bezier(.23,1,.32,1).
