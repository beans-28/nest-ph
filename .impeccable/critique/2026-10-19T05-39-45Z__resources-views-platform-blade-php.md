---
target: NEST.PH platform landing
total_score: 20
max_score: 32
na_heuristics: 7,10
p0_count: 0
p1_count: 3
target_identity: "file:C:\\Users\\Vince\\nest-ph\\resources\\views\\platform.blade.php"
target_fingerprint: "sha256:128d81a232ecfb41ae4062a70dcbbb15d7652fa218c62cfb8c0871ea82121244"
target_path: "C:\\Users\\Vince\\nest-ph\\resources\\views\\platform.blade.php"
timestamp: 2026-10-19T05-39-45Z
slug: resources-views-platform-blade-php
---
# Critique: NEST.PH platform landing (resources/views/platform.blade.php)
Method: dual-agent. Score 20/32 (heuristics 7 and 10 scored n/a).
Detector: overused-font (Roboto) x2. False positive, because PRODUCT.md locks Roboto as the brand font.
Priority issues:
- P1: The empty dashed placeholder card stretches to the same height as the Pureza card.
- P1: The hero image is a logo slab and shows nothing of the product.
- P1: A dorm with no photo falls back to a blank grey box, and a broken image shows a broken-image icon.
- P2: The nav has 7 targets, the labels repeat, and Team Login is too prominent.
- P2: Ink-on-green subtitle contrast is about 3:1, and outline-green buttons look washed out on cream.
- P3: Form states: no "Sending…" label, no consent asterisk, only the first error is shown.
