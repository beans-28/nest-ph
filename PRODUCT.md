# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

NEST.PH serves three distinct audiences on one system:

- **Prospective tenants (public site visitors):** people discovering the dorm online, browsing room listings, VR tours, and dorm info/policies, deciding whether to inquire or apply. Not yet authenticated.
- **Students/tenants:** renters of a bed or room, using the authenticated tenant portal to apply, sign leases, pay rent (including move-in payments), submit/track maintenance tickets, read announcements, and manage their stay. Frequently on mobile.
- **Dorm owner/admin staff:** back-office operators running the property day to day — vacancy/room/floor management, application review, payment approval and billing generation, lease contracts, delinquency and eviction workflows, tickets, activity log, and reports. Desk-based, task-heavy usage.

## Product Purpose

NEST.PH is a full-cycle dormitory management platform: it takes a prospective renter from public discovery of available rooms through application, lease signing, move-in payment, ongoing rent/billing, maintenance requests, and eventual move-out — while giving dorm ownership/staff the back-office tools to run occupancy, billing, delinquency, and reporting for the property. Success means prospective tenants can self-serve discovery and application without staff intervention, tenants can manage their stay entirely online, and staff can run the property's operations (vacancy, money, compliance) from one system instead of spreadsheets or manual paperwork.

## Positioning

NEST.PH's differentiator is that it spans the full dorm lifecycle in one system — public room discovery and VR tours, tenant application and lease signing, rent billing and payment proof review, delinquency escalation and eviction notices, maintenance ticketing, and occupancy/financial reporting — rather than being just a room-listing site (no ops/back-office) or a generic property-management tool (no purpose-built student-dorm public front end, VR tours, or bed-level vacancy granularity).

## Operating Context

- **Public front end:** home, room listings (with per-room bed availability), VR tours, dorm info/policies (with downloadable policy documents), inquiry form, and the application flow with a downloadable contract template.
- **Tenant portal:** dashboard, profile, announcements (with comments), move-in flow (welcome, payment type/method, payment, pending states), payments, tickets, and authentication (including password reset via emailed code).
- **Admin back office:** vacancy monitoring and floor/room setup, VR tour management (scenes, hotspots, room photos), applications (approve/reject/request reapplication), payments (approve/reject proof, cash recording, billing generation), inquiries (reply), lease contracts (create, search tenants, capture signatures), penalties and damages, delinquency management (override, history, demand letters, eviction notices), tenant manager (CRUD, status), tickets, activity log, reports (occupancy, financial, export), and dormitory profile settings.
- Authentication is split by role: separate tenant login and admin login pages/flows.

## Capabilities and Constraints

- Built on Laravel 12 (PHP 8.2+) with Blade views, Tailwind CSS, and Vite. PDF generation via barryvdh/laravel-dompdf (contracts, demand letters, eviction notices, policy files).
- Billing/payments distinguish proof-of-payment uploads (requiring admin approval/rejection) from admin-recorded cash payments.
- Delinquency has an escalation path with an explicit testing/simulation surface (`delinquency-testing`) distinct from live delinquency, and can produce demand letters and eviction notices as downloadable documents.
- Vacancy is tracked at bed level within rooms, not just room level; rooms support photo galleries (reorderable) and VR scenes with hotspots.
- Undecided: no formal accessibility standard has been set; no analytics/monitoring stack has been confirmed.

## Brand Commitments

- Name/brand: **NEST.PH** — tagline "Study hard, make friends, and live your NEST life."
- Palette (from current public front end): green-light `#a2d9a4`, green-dark `#567357`, green-darker `#197335`, ink `#292420`, ink-alt `#21272a`, cream `#dcd8d7`, cream-light `#f2f4f8`, gray-border `#c1c7cd`.
- Typeface: Roboto (400/500/700) via Google Fonts, falling back to system UI sans-serif.
- Logo assets: `public/images/nestph.png`, `public/images/nestphgreen.png`.
- These are confirmed and authoritative — treat them as locked, not open for reinvention, in future design work.

## Evidence on Hand

- Live Blade views for every surface listed under Operating Context (public, tenant, admin) exist in `resources/views/`.
- No case studies, testimonials, press, or external evidence on hand — future work must not fabricate these.

## Product Principles

1. One system, full lifecycle: never design a surface as if it stands alone from the public → tenant → admin continuum it belongs to.
2. Bed-level, not just room-level: occupancy and vacancy language and UI must reflect that a room can be partially filled.
3. Money and compliance are trust-critical: payment, delinquency, and contract surfaces must read as precise and accountable, not merely tidy.
4. Self-service first: prospective and current tenants should be able to complete discovery, application, payment, and support without staff intervention.
5. Staff efficiency: admin surfaces prioritize scanability and fast task completion over expressive brand moments.

## Accessibility & Inclusion

No product-specific accessibility standard has been established yet.
