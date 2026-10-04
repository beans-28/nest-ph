# NEST.PH — API Contract v41
**Status:** Full consolidated contract (supersedes v40). Checked against the code and `php artisan route:list` on 2026-10-02.

**What's new in v41:**
1. **The system now follows Pureza Station Dormitory's signed documents** (Tenant Agreement, Rules and Regulations, Payments and Fees Schedule). This work landed in the code on 2026-10-01 but was never written into v40. It covers a Rental Policy card, Room Types & Rates, Other Charges, three tenancy documents signed by the applicant **and** their emergency contact, calendar-month billing with a grace period, an automatic one-time late penalty, and SMS consent for emergency contacts. One migration: `2026_10_01_000001_align_with_dormitory_documents`. To load Pureza's values, run `php artisan db:seed --class=PurezaStationSeeder`. See **"Dorm Documents & Rental Policy (v41)"**.
2. **The app is deployed to Laravel Cloud** for testing and the thesis defense demo. Uploads now go through the `public` storage disk, which is the Cloud bucket in production. New Artisan command: `storage:push-to-cloud`. See **"Deployment"**.
3. **New admin route:** `POST /vr-tours/scenes/{scene}/photo` replaces a VR scene's photo but keeps its name and arrows.
4. **Cleanup:** the version-by-version summaries at the top of the document were merged into one short Document history. The "Confirmed resolved" list and the notes on deleted scripts were removed (git history keeps them). The Route Index was regenerated: 9 routes added, 1 removed.
5. **Tests:** 48 passing (17 new, in `tests/Feature/DormitoryDocumentsAlignmentTest.php`).

**Auth model, mixed by design.** The first routes were built under `/api/...` with Sanctum (`auth:sanctum` + `admin`/`tenant`). Most later work uses plain session auth (`['auth','admin']` / `['auth','tenant']`) on routes in `web.php`, called from server-rendered Blade pages. Both log in the same way from the same browser session. As a result, some features have two routes (an older `/api/...` one and a newer plain one) pointing at the same controller. This is intentional, so don't remove either one.

**Guards and account-state gates:**
- `admin`: dormitory staff and owner routes.
- `tenant`: a tenant's own data only. The tenant is found from the logged-in user, never from a route parameter. A mismatch returns `404`, not `403`.
- `privileges`: stacked on `admin` for Admin Privileges only. It requires the `manage_users` privilege (the Owner). Everyone else gets `403`.
- `movein.check`: sends tenants with `pending_move_in_payment` to the move-in pages. See "Tenant Account Status".
- `moveout.check`: limits a moved-out tenant to the review page. See "Reviews & Ratings".
- `delinquency.check`: enforces `portal_restricted`. See "Delinquency Escalation — Tenant Side".

**SMS goes through textbee.dev, not Semaphore.** Semaphore needs a Sender ID approval that a thesis project can't get. textbee.dev uses a registered Android phone as the gateway. See "SMS Integration".

**Table numbers** follow use case manuscript v3 (`docs/NEST_PH_Use_Case_Changes_v3.md`). Document history entries keep the numbers used at the time.

---

## Document history (condensed)
*One line per version. The full write-up of each version is in git history (`git log -p -- "docs/API Contract v*.md"`).*

- **v9** — live-database cross-check found a genuinely missing composite index and one unapplied migration.
- **v10** — Week 6 schema groundwork done *before* building anything, catching a manuscript inconsistency (Table 51 vs. Table 28's "Stage 6") ahead of time.
- **v11** — Week 6 Mon–Tue built: textbee.dev SMS integration, the full Stage 1–6 escalation state machine. Exception-path testing caught two real correctness bugs (SMS retry safety, Stage 6's completeness gate).
- **v12** — Admin Delinquency dashboard built from a Figma prototype that needed four real corrections against the manuscript before being implemented as-is.
- **v13** — Stage 5 demand letter made real (PDF generation, redesigned per a separate approved Figma frame), Table 51 Eviction Notice built, a Reset-override display bug found and fixed, Table 29's wording confirmed against the manuscript, stage-detail history modal finished, table column-alignment UI fix.
- **v14** — consolidation of v9–v13 into one document + a live-repo verification pass. **Found Stage 5's PDF generation was actually broken** despite v13 describing it as done — a variable mismatch between the controller and its redesigned template, introduced when the template was updated but the controller wasn't updated to match.
- **v15** — verified the v14 fix was correctly applied; Stage 5 is genuinely reliable again. **Found one new, unconfirmed issue**: possible stray invisible characters in the demand letter template's highlighted `<span>` tags, which may or may not actually affect rendering — flagged for a direct visual check rather than assumed broken.
- **v16** — the v15 span-tag concern was checked directly against the actual source file (not via search-tool snippets) and confirmed clean. False alarm, resolved, no code changes needed. Stage 5 has no known open issues as of that document.
- **v17** — **Delinquency Escalation, Tenant Side, built end-to-end:** `RestrictDelinquentTenant` middleware (actually enforces `portal_restricted`, closing a gap that had persisted since v11), Table 28 step 4 wired into `InquiryController::store()`, and a full tenant-facing `/my/delinquency` page covering Stages 1–6, each built and verified against its own approved Figma frame. Admin-side stage colors (`DelinquencyController::STAGES`) were also updated to match the same Figma palette the tenant side now uses. Deliberately left one open question: should `/billing` get the Stage 6 full-takeover treatment too, or keep the lighter Stage 3+ lock panel?
- **v18** — **Team decision: `/billing` also gets the full Stage 6 takeover (Option A).** Built and verified. Also found and fixed a real bug surfaced while building it: `RestrictDelinquentTenant`'s default redirect target was hardcoded to `tenant.billing` regardless of blacklist status, so a Stage 6 tenant logging in (or hitting any disallowed route) was being sent to `/billing` instead of `/my/delinquency`. Fixed. This closed out the last open item from Week 6's tenant-side Delinquency work.
- **v19** — **Tenant Manager built** (Tables 14, 15, 38). Five bugs found and fixed in the onboarding pipeline while testing it: application approval silently dropping DOB/address/tenant-type/documents onto the new tenant record; a tenant-type vocabulary mismatch between Table 13's and Table 15's own wording; a redundant second signature-verification step for online-approved tenants who'd already e-signed during application; a tenant password-change flow that silently misreported success as failure (Breeze scaffolding returning a redirect to a fetch-based page expecting JSON); and Table 38's admin-ID audit logging, caught via a deliberate post-build check against the manuscript. `escalation:process` was also wired to the scheduler for the first time, closing a gap flagged since v11.
- **v20** — Three more rounds of real bugs, all found by the team actually clicking through the finished Tenant Manager and Billing pages rather than just reading the code:
- **v21** — **Manage Dormitory Profile (Table 39) built end-to-end**, the single longest-open item in this contract's history at the time. New tables `dormitory_amenities` (8 fixed, seeded, toggle-only rows) and `dormitory_house_rules` (freely addable/editable/deletable); two new columns on `dormitory_profile` for Business Permit / BIR Registration. New `DormitoryProfileController` with independent endpoints per field/file so no single slow upload blocks the rest of the form. `PublicController::home()` and `dormInfoPage()` updated to actually render all of it — see the full "Dormitory Profile" section below for the complete public-reflection mapping.
- **v22** — **Admin Privileges built** (DT&TM Week 2, Wed Aug 12 / Thu Aug 13 — "This screen is for the Dormitory Owner only; Admins can't grant themselves privileges"). New `admin_access_logs` audit table, new `AdminAccessLog` model, new `EnsureCanManagePrivileges` middleware (alias `privileges`), new `AdminPrivilegeController` with four endpoints (list, create, update privileges, revoke), new `adminprivileges.blade.php`, and a new self-lockout safety check (an Owner can't strip their own `manage_users` privilege or revoke their own account) that goes slightly beyond the manuscript's literal text. Fed into the existing unified Activity Log via a new `ActivityFeedService::adminAccessEvents()` source. Full detail in "Admin Privileges" below.
- **v23** — **Ticketing built end-to-end** (Tables 33, 34, 35, 40, 41), admin and tenant sides both, closing the single longest-unlinked sidebar placeholder in the app. New `MaintenanceTicket`/`TicketReply` models (the table itself is a rebuild of an unused Feb-2024 placeholder, not new), new `TicketController` (admin) and `TenantTicketController` (tenant), new `admintickets.blade.php` and `tenanttickets.blade.php`, both built without a list-page Figma to work from. Full detail in "Ticketing" below.
- **v24** — **Admin Delinquency Testing Tools built**: a new `DelinquencyTestingController` (`GET /delinquency-testing/tenants`, `POST /delinquency-testing/{tenant}/escalate`) plus a Blade panel on `/delinquency` itself, both gated to local environments only. Lists every active tenant tagged with their current stage (0 = not delinquent), with buttons to jump any tenant straight to Stage 0–6 — reuses the real `EscalationService` via a new scoped `processBillingStatement()` method, so genuine SMS still goes out; not a faked shortcut. Two real bugs found and fixed along the way, unrelated to each other:
- **v25** — **Payment-settlement → escalation-resolution gap closed.** Prompted by a direct question about the new Testing Tools panel's Reset action ("does the tenant actually leave the list, does their overdue clock reset") that led to checking the *real* payment flow's equivalent question honestly rather than assuming it already worked correctly. Confirmed via code trace, not guesswork: `resolveSettledEscalations()` (lifts `portal_restricted`, resolves open `escalation_logs`) was only ever invoked from inside `EscalationService::processAll()` — never from either place that actually settles a bill (`PaymentController::approveProof()` / `recordCash()`). Consequence: a tenant who paid in full dropped off the admin's live-computed Delinquent Accounts list immediately, but their own portal stayed locked behind the Stage 3 restriction until the next `escalation:process` run — indefinitely delayed on a local dev box with no `schedule:work` running, and no faster than the scheduler's own interval even in production. Fixed by calling `resolveSettledEscalations()` directly in both methods, immediately after the bill's status resync, guarded to only fire when that specific payment actually finished settling the bill (`status === 'paid'`). No schema change; two methods touched.
- **v26** — **Applicant/tenant name format split into `first_name`/`last_name`**, requested by the team's research adviser as a data-quality correction to Apply for Occupancy and Tenant Manager. `applications.full_name` and `tenants.full_name` dropped; `first_name`/`last_name` (each `varchar(100)`) added to both tables, existing test rows backfilled by a one-off tinker script (naive last-word split, spot-checked manually rather than trusted blindly for multi-word Filipino names). `Application` and `Tenant` both gained a computed `full_name` accessor (`$appends`) so every existing reader of `->full_name` — PDFs, emails, JSON responses, admin JS — kept working unchanged; only the actual input forms and their validation/creation code needed touching. Deliberately **not** applied to `users.name` (stays one column; login only ever needs a single display name) or `inquiries.full_name` (Contact/Inquiry form was out of scope for this pass). Two real bugs caught and fixed while wiring this through: `TenantController::page()`'s `orderBy('full_name')` would have thrown a SQL error against the now-nonexistent column (changed to `orderBy('last_name')->orderBy('first_name')`), and `ApplicationController::index()`'s eager-load `'tenant:id,full_name'` would have done the same (changed to select `first_name`/`last_name`, letting the accessor rebuild `full_name` after loading). Full detail in "Applicant & Tenant Name Format" below. **Flagged for BAGUI:** Tables 13, 14, and 15 still describe this as a single "full name" field in the manuscript — a deliberate, adviser-driven deviation, not a silent fix.
- **v27** — Three independent pieces of work, not one connected feature:
- **v28** — **Generate Reports (Table 37) built** — previously the largest fully-missing use case in the manuscript; no `ReportController`, no `/reports` route, and no view existed anywhere in the codebase prior to this version. New standalone page, not folded into Billing or Vacancy Monitoring, matching Table 37's own Flow of Events ("1. Navigate to Reports") rather than an embedded-tab approach. Occupancy Report (live bed/room status by floor) and Financial/Billing Report (collected/outstanding/penalties/delinquent count, date-range filterable) both built, both CSV-exportable. Full detail in the new "Generate Reports" section below.
- **v29** — **Table 16 (Record Occupancy Transaction) evaluated and deliberately excluded from scope**, resolving the "still unresolved" open architectural question this document had carried since the Pay Move-In Fees section was first written. A full working implementation was actually built first, not just discussed: a new `occupancy_transactions` audit table, a new `OccupancyController` with `recordMoveIn()`/`recordMoveOut()` actions, two conditional buttons in Tenant Manager (shown only for tenants in the matching status), and a confirm-date modal — deliberately additive, leaving `activateTenantIfMoveInSettled()` and the existing Deactivate Account action completely untouched rather than replacing them. On review before shipping it for real, the team judged it unnecessary and rolled it back in full:
- **v30** — **Announcements built end-to-end** (Tables 44–47), the newsfeed module — closing the last remaining fully-unbuilt use case in the manuscript. New `Announcement`/`AnnouncementComment` models (the latter's `user_id`/`tenant_id` both nullable, exactly one set per row, same pattern as `ticket_replies`), new `AnnouncementController`, and one shared Blade partial included on both `admindashboard.blade.php` and `tenantdashboard.blade.php` rather than a standalone page. Full detail in the "Announcements" section below.
- **v31** — **Frontend-only consolidation pass across the admin and tenant portals, plus a public-nav consistency fix.** No routes, controllers, migrations, or business logic touched — this was UI/markup/CSS deduplication and cosmetic-bug fixing, prompted by the team noticing generic-looking icons and layout inconsistencies on review.
- **v32** — **Review Moderation built, plus a site-wide mobile and visual polish pass.** Run `php artisan migrate` and `php artisan view:clear` after pulling, then hard-refresh (Ctrl+F5) since CSS/JS files are cached.
- **v33** — **Re-application list cleanup, plus move-in and Forgot Password polish.** No migrations. After pulling, run `php artisan view:clear`, then hard-refresh (Ctrl+F5).
- **v34** — **Payment methods, room Wi-Fi/utilities pricing, and a contract audit.** Run `php artisan migrate` (three new migrations) and `php artisan view:clear` after pulling, then hard-refresh (Ctrl+F5). `php artisan storage:link` must already exist for QR images to show (it's also needed for photos and documents).
- **v35** — use case reports updated to manuscript v3, plus the code changes that came out of it. Table numbers in this entry are v3 numbers.
- **v36** — **Dashboard charts, Login Tracker, Owner/Admin tags, first-Owner setup, and a passing test suite.** Run `php artisan migrate` (one new migration) and `php artisan view:clear` after pulling, then hard-refresh (Ctrl+F5). Plain-language session write-up: `docs/dashboard-charts-login-tracker-and-role-tags.md`.
- **v37** — **Dual branding, dorm logo, new login tagline and layout, and a stronger password policy.** Run `php artisan migrate` (one new migration) and `php artisan view:clear` after pulling, then hard-refresh (Ctrl+F5). Plain-language session write-up: `docs/dual-branding-login-and-password-policy.md`.
- **v38** — **Proof viewer, PDF and Excel report exports.** Run `composer install` (new package `phpoffice/phpspreadsheet`). No migrations, no new routes.
- **v39** — **Notifications, statement of account, deposit refunds, duplicate reference check.** Run `php artisan migrate`.
- **v40** — **Revenue, expenses and net profit; 12-month occupancy trend; charts in every export; a year of demo history.** Requested by the team leader. Run `php artisan migrate`.
- **v41** — Pureza Station documents, rental policy, room types, other charges, three-document e-sign with emergency-contact signature, calendar-month billing with grace period and automatic late penalty, Laravel Cloud deployment, VR scene photo replace. Document cleaned up and Route Index regenerated.

---

## Auth
*(Unchanged since v6 — no v6 document was available during the v14 consolidation to re-verify its exact content. `/tenant/login`, `/admin/login`, `/register`, `/logout`, `/api/user`, password reset. The `AuthController::login()` role-name bug reported in v6 has not been re-verified since and should be treated as still open until someone confirms otherwise.)*

### Tenant password change — `PUT /password` (`password.update`), `PasswordController`
Used by both the desktop Breeze password form (`partials/update-password-form.blade.php`, unaffected) and the tenant portal's own Change Password tab (`tenantaccount.blade.php`, fetch-based — see "Tenant Profile (Read-Only) + Change Password" below for the rest of that page). The controller now returns `response()->json(['message' => '...'])` when `$request->wantsJson()` is true, instead of always redirecting — previously a successful password change was indistinguishable from a failure on the tenant-facing page. Validation unchanged: `current_password` (Laravel's built-in check against the authenticated user), `password` (`Password::defaults()`, confirmed).

**Deactivated accounts (updated v35):** `AuthController::login()` refuses an account with `users.is_active = false`, returning `403` "This account has been deactivated. Please contact the dormitory administrator." **One exception (v35, Table 42):** a tenant with `status = 'inactive'`, not blacklisted, and no review yet may still log in; `moveout.check` then keeps them on `/moved-out`, so the only thing they can do is leave a review. After they review, login is refused again. *The paragraph below is the original v22 note, kept for history; the gap it describes was fixed before v34.*

**Admin session recording (v36).** After a successful login, both `Api\AuthController::login()` and `Auth\AuthenticatedSessionController::store()` call `AdminLoginSession::recordLogin($user, $request)` (after `session()->regenerate()`, so the stored session id is the live one). Both `Api\AuthController::logout()` and `AuthenticatedSessionController::destroy()` call `AdminLoginSession::recordLogout()` before `Auth::logout()`. Both methods return immediately for non-admin users, so tenant logins are never recorded. A deactivated account that is refused at login is not recorded. See "Admin Privileges → Login Tracker."

*Original v22 note:* `AuthController::login()` calls `Auth::attempt($credentials, $remember)` with only `email`/`password` — it never actually checks `users.is_active`. Tenant deactivation (Table 37) and the new Admin revoke action both set `is_active = false` on the assumption that this blocks login, but nothing currently enforces that at the authentication layer itself; only downstream role-based route middleware (`admin`/`tenant`) keeps a deactivated/revoked account from reaching anything useful once logged in, and a revoked admin demoted to the `tenant` role with no linked `tenants` row would hit `/dashboard`'s existing "no tenant record" fallback rather than anything broken, but this is relying on a side effect, not a real gate. Worth a real fix (e.g. a custom `Authenticatable` check or an explicit `is_active` condition in the credentials array) before deployment.

### Password policy (v37)
Every password a person chooses must have **at least 8 characters, 1 number, 1 symbol and 1 uppercase letter.**
- **Where it's defined:** once, in `AppServiceProvider::boot()`: `Password::defaults(fn () => Password::min(8)->numbers()->symbols()->rules([new HasUppercase]))`. `App\Rules\HasUppercase` checks for any uppercase letter (`\p{Lu}`, so accented capitals count), because Laravel's built-in rule only offers "mixed case" (upper and lower together).
- **Where it applies:** `PUT /password` (tenant Change Password), the reset-by-code step (`PasswordResetCodeController::reset()`, previously `min:8`), Breeze's `NewPasswordController` and `RegisteredUserController` (unused by NEST.PH, covered anyway), and `php artisan nestph:create-owner`.
- **Error responses:** `422` with one message per missing requirement under `errors.password`, e.g. "The password must contain at least one uppercase letter." / "The password field must contain at least one symbol." The existing fetch-based forms already show `errors.password[0]`.
- **Temporary passwords:** `App\Support\TemporaryPassword::generate()` (12 characters, always at least one of each required type, no look-alike characters 0/O/1/l/I, shuffled with `random_int`). Used by `AdminPrivilegeController::store()`, `TenantController::store()` and `Api\ApplicationController::approve()`. Previously `Str::random()`, which is letters and numbers only.
- **Live checklist (frontend only):** `partials/password-rules.blade.php`, included with `['for' => '<input id>']` under the new-password input on `tenantaccount.blade.php` and `passwords.blade.php` step 3. It ticks each requirement as the user types. It only guides; the server is the real check. **Keep the two in sync if the policy changes.**
- **Not enforced on existing passwords.** The rule applies only when a password is set or changed; accounts with older, weaker passwords (including the seeded `password123` test accounts) still log in. There is no "must change on first login" for temporary passwords.

---

## Admin Dashboard — overview panels (v36)
*`GET /dashboard` (`dashboard`) for an admin user, `DashboardController::adminDashboard()`, view `admindashboard.blade.php`. Page render only; no JSON endpoint.*

Requested by the team's adviser (stat cards → graphs). The four stat cards were replaced by four panels:

| Panel | Data | Notes |
|---|---|---|
| **Collections** | `revenueThisMonth`, `cardCharts.revenue` | Headline ₱ amount, "±N% vs {last month}" badge (hidden when last month was ₱0), 6-month bar chart. Same rule as before: `payments.status = approved`, by `payment_date`. |
| **Beds** | `bedMap`, `cardCharts.beds`, `totalBeds`, `vacantBeds`, `vacancyRate` | "occupied/total" plus one square per bed by floor: occupied (solid green), reserved (purple with a centre dot), open (outline), maintenance (striped amber). Each floor has a visually hidden text summary for screen readers. |
| **Tenants** | `totalTenants`, `newTenantsThisMonth`, `cardCharts.tenants` | New tenants per month by `tenants.created_at` — **not** total tenants over time (no move-out history to reconstruct that from). |
| **Bills** | `cardCharts.bills`, `delinquentCount` | One bar split by `billing_statements.status` (paid/partial/unpaid/overdue) with counts; link to `/delinquency`. |

- **`cardCharts`**: `labels` (6 month abbreviations, oldest first, ending with the current month), `tenants` and `revenue` (6 numbers each), `bills` (`paid`/`partial`/`unpaid`/`overdue` counts), `beds` (`occupied`/`vacant`/`reserved`/`maintenance` counts). About 16 small queries; fine at this scale.
- **`bedMap`**: `[{label: "Floor N", beds: [{name: "Room X · Bed Y", status}]}]`, rooms and beds sorted by number/label.
- The separate **Occupancy** card was removed; the Beds panel replaces it. The overdue-tickets banner, delinquency banner, announcements feed, Tickets card and Recent Activities card are unchanged in behaviour.
- **Chart.js 4.4.1** from `cdn.jsdelivr.net`. Chart colours are read at runtime from `admin.css` custom properties. If the script fails to load, each chart area shows "Chart couldn't load. The numbers above are still up to date."
- Panels stack to one column at ≤1080px; panel links get a 44px tap area at ≤720px.

---

## Admin Privileges — Table 53 (added in manuscript v3), DT&TM Week 2 (built v22)
*Routes require `['auth','admin','privileges']` session middleware. `App\Http\Controllers\AdminPrivilegeController`, view `resources/views/adminprivileges.blade.php`.*

Manuscript scenario: "This screen is for the Dormitory Owner only — Admins can't grant themselves privileges." There's no separate `Owner` role in the schema (see `RolesAndTestUsersSeeder`) — the Dormitory Owner is simply the admin who holds the `manage_users` privilege among the six defined on `admin_privileges.privilege_name` (`manage_tenants`, `manage_rooms`, `manage_contracts`, `manage_billing`, `manage_users`, `view_reports`). That's the literal gate the new `privileges` middleware checks.

**Scope decision, flagged rather than assumed from the manuscript:** "Add Admin Account" creates a **brand-new login** (name + email, admin picks starting privileges), not a search-and-promote flow against existing tenants. The manuscript doesn't specify which; a fresh account was chosen as the simpler, fully-self-contained path for a capstone demo. Revisit if BAGUI's use case actually calls for promoting an existing user instead.

### Schema (v22 addition)
New table **`admin_access_logs`**: `user_id` (FK `users`, cascade delete — the admin account the event happened to), `performed_by` (nullable FK `users`, null-on-delete — who did it), `action` (enum `granted`/`privileges_updated`/`revoked`), `note` (nullable text), `created_at` only (no `updated_at`). Exists because `admin_privileges` rows get deleted/replaced on every privilege edit or revoke, so without a separate log there'd be no history left for the page's own "Granted" column or the unified Activity Log once something's been revoked or changed.

`App\Models\AdminAccessLog` — `$timestamps = false`, `created_at` cast to `datetime`, `belongsTo(User::class)` as `user`, `belongsTo(User::class, 'performed_by')` as `performedBy`.

`App\Models\User` gained a `privileges(): HasMany` relation to `AdminPrivilege` — used by the new middleware's `$user->privileges()->where('privilege_name', 'manage_users')->exists()` check and by the controller's index query.

### `App\Http\Middleware\EnsureCanManagePrivileges` (alias `privileges`)
Passes only if the authenticated user's role is `admin` **and** they hold the `manage_users` privilege. Returns `403` JSON for `expectsJson()` requests, otherwise `abort(403)` with a plain message. Stacked as `['auth', 'admin', 'privileges']` — a regular admin without `manage_users` gets a `403` even hitting `GET /admin-privileges` directly, not just a hidden nav link.

### `GET /admin-privileges` (`admin-privileges.index`)
Lists every `admin`-role user with their current privileges, active/inactive status, and a `granted_at` timestamp (the most recent `admin_access_logs` row for that user, falling back to the earliest `admin_privileges.granted_at` if no log exists yet — e.g. the two seeded test accounts). Also passes `activeAdminsCount` and `totalGrants` (sum of all `admin_privileges` rows across every admin) for the page's stat strip, and `currentUserId` so the page can replace an admin's own Revoke button with "Can't revoke yourself" (v36; previously a disabled button) and lock their own `manage_users` checkbox client-side. **v36:** each admin row also carries `role_tag` (`owner`/`admin`, see "Owner/Admin role tags" below), and the page receives `loginSessions` and `loginSessionsHasMore` for the Login Tracker. Optional query `?tracker=N` (default 30, clamped to 30–1000) sets how many tracker rows to show; the "Show older sign-ins" link adds 30.

### `POST /admin-privileges` (`admin-privileges.store`) — "Add Admin Account"
`name`, `email` (unique against `users`), `privileges[]` (each validated against the six known keys). Creates the `User` (role `admin`, `is_active: true`, password `Str::random(10)`), the selected `AdminPrivilege` rows, and an `admin_access_logs` row (`action: 'granted'`), all in one transaction. Returns the plaintext temporary password in the JSON response — there's no guaranteed working email delivery to hand it off any other way (Mailtrap sandbox only, see "Deployment"), so the page shows it on screen once, with a copy button and a reminder to change it from Account Settings.

### `PATCH /admin-privileges/{user}/privileges` (`admin-privileges.privileges.update`) — "Manage Privileges"
`privileges[]`, synced against the target admin's current rows (deletes anything unchecked, `firstOrCreate`s anything newly checked, each new row stamped with `granted_by`). Logs `privileges_updated` with the resulting privilege list in the note. `404`s if `{user}` isn't actually an `admin`-role account.

**Self-lockout safety check, beyond the manuscript's literal text:** if the target user is the currently-authenticated admin and `manage_users` isn't in the submitted list, the request is rejected with `422` — stops the Owner from accidentally locking themselves out of this page with no one else able to get back in.

### `PATCH /admin-privileges/{user}/revoke` (`admin-privileges.revoke`) — "Revoke Access"
Blocks revoking your own account (`422`). Otherwise, in one transaction: deletes every `admin_privileges` row for the user, sets `role_id` back to the `tenant` role, sets `is_active: false` (same convention Table 37's Deactivate Tenant Account already uses — see the Auth section above for the caveat that `is_active` isn't actually checked at login yet), and logs `revoked`.

### Login Tracker (v36)
A section at the bottom of `/admin-privileges` showing when **other** admin accounts signed in and out. Owner-only by virtue of the page's existing `privileges` middleware; no new route.

**Schema:** new table **`admin_login_sessions`** (migration `2026_09_28_000001`): `id`, `user_id` (FK `users`, cascade delete), `session_id` (nullable string), `ip_address` (nullable, 45), `user_agent` (nullable, 512), `logged_in_at` (`dateTime`), `logged_out_at` (nullable `dateTime`), index on (`user_id`, `logged_in_at`). No `created_at`/`updated_at`. The two time columns are deliberately `dateTime`, not `timestamp`: as `timestamp`, MySQL silently rewrote `logged_in_at` to "now" whenever the row was updated at logout.

**Model:** `App\Models\AdminLoginSession` — `$timestamps = false`; `recordLogin()` (admins only; stores session id, IP, user agent); `recordLogout()` (admins only; closes the open row for the current session id, falling back to the user's newest open row); `deviceLabel()` ("Chrome on Windows" style label from the user agent).

**Controller:** `AdminPrivilegeController::loginTracker($viewerId, $limit)` returns the newest rows for every admin except the viewer, each with `name`, `email`, `tag`, `logged_in_at`, `logged_out_at`, `last_active`, `state`, `device`, `ip`. `state` is computed, never stored:
- `logged_out` — `logged_out_at` is set.
- `online` — no logout, and the row's `session_id` still exists in the `sessions` table (database session driver) with `last_activity` within `session.lifetime` (30 minutes).
- `ended` — no logout and no live session: timed out or the browser was closed. Shown as "Didn't log out."

**UI:** "Signed in now: …" summary, then a table (Admin, Logged in, Logged out, Duration, Device). Empty state: "No sign-ins from other admins yet…".

### Owner/Admin role tags (v36)
Staff names show an **Owner** tag (solid dark green) or **Admin** tag (light green) wherever they appear. Tenants get no tag.
- **Rule:** `User::isOwner()` is true for an `admin`-role user holding `manage_users` — the same rule as the `privileges` middleware. `User::roleTag()` returns `'owner'`, `'admin'` or `null`. Uses the loaded `privileges` relation when present, so callers eager-load `role` and `privileges` to avoid one query per row.
- **Markup:** `partials/role-tag-style.blade.php` (the only place the tag's CSS lives; included once per page) and `partials/role-tag.blade.php` (`@include('partials.role-tag', ['tag' => $x])` for server-rendered rows). JS-rendered pages use a one-line `roleTag(t)` helper producing the same `<span class="role-tag role-tag-owner|admin">`.
- **New response fields (all additive):** `role_tag` on `/admin-privileges` admin rows; `tag` on tracker rows; `poster_tag` on `GET /announcements` and the `POST /announcements` response (falls back to `admin` if the poster was deleted); `author_tag` on announcement comments, admin ticket-detail replies (`GET /tickets/{ticket}`) and tenant ticket messages (`GET /my/tickets`, `null` on the tenant's own messages); `replied_by_tag` on the `/inquiries` page data; `performed_by_tag` on `GET /delinquency/{tenant}/history` entries.
- The page updates a row's tag immediately after Manage Privileges or Add Admin Account, without a reload.
- Not tagged: the Activity Log (entries are sentences).

### Sidebar wiring
"Admin Privileges" existed as an unlinked nav item on every admin page already (the same placeholder pattern "Tickets" used until v23) — now points at `route('admin-privileges.index')` across all admin pages, and added to each page's `NEST_PAGES`/`PAGES` page-search array. Icon changed from the reused-Applications rectangle icon to a dedicated shield icon, so the two no longer look identical in the sidebar.

### Activity Log integration
`ActivityFeedService::adminAccessEvents()` *(new)* reads `admin_access_logs` and renders `Granted admin access to {name}` / `Updated admin privileges for {name}` / `Revoked admin access from {name}`, concatenated into the same unified feed `all()` already builds from payments, escalation, tenants, applications, leases, penalties, and damages. Shows up on both the Dashboard's Recent Activities card and the full `/activity-log` page with no further wiring needed.

### Superseded, not removed
`App\Http\Controllers\Api\UserManagementController::grantAdmin()`/`revokeAdmin()` (the original v6-era stub — a plain role-toggle with no privilege granularity, no logging, no Owner-only gate) is now fully superseded by this module. Left in place since nothing else calls it and removing it isn't required for correctness, but it shouldn't be extended going forward — new work belongs in `AdminPrivilegeController`.

---

## Ticketing — Tables 32, 33, 34, 39, 40 (built v23)
*Admin routes require `['auth','admin']` session middleware, `App\Http\Controllers\TicketController`, view `resources/views/admintickets.blade.php`. Tenant routes require `['auth','tenant','movein.check','delinquency.check']` under a `/my/tickets` prefix (chosen over a bare `/tickets` to avoid colliding with the admin URI — same reasoning as `/my/delinquency` vs. admin's `/delinquency`), `App\Http\Controllers\TenantTicketController`, view `resources/views/tenanttickets.blade.php`.*

Covers Submit Ticket (33), View and Manage Tickets (34), Track Ticket Status (35), Ticket Priority Classification (40), and Ticket Escalation Reminder (41) as one connected flow, the same way Tenant Manager treats Tables 14/15/37 as one flow. Figma coverage: the admin "ticket view" modal (node 441-527), the type dropdown (node 994-5004), the status dropdown (node 882-3246), the tenant "Add New Ticket" form (node 564-3561), and the tenant tracking/My Tickets page (node 524-6001). **Neither list page (admin or tenant) had a Figma frame** — both were designed from scratch, matching the app's existing card-grid conventions (Applications/Inquiries style) rather than a table, since Table 40 itself describes the unit as a "ticket card." Since v27, the tenant Profile page's read-only banner also links here as the sole path for requesting a change to one's own profile record — see "Tenant Profile (Read-Only) + Change Password" below.

### Schema
Rebuilds the original `maintenance_tickets` table (a Feb-2024 placeholder with no model/controller/route ever built against it, so no real data existed to migrate) rather than altering it column-by-column:
- `tenant_id` (FK `tenants`, cascade delete), `bed_id` (nullable FK `beds`, null-on-delete — a snapshot of the tenant's room *at submission time*, so a ticket still shows its original room even if the tenant later moves).
- `title` (string 150), `description` (text, required at the DB level too — no `nullable()`).
- `category` — enum, the 13 values from the type-dropdown Figma (`billing_payment_concern`, `electrical_issue`, `plumbing_water_emergency`, `security_concern`, `structural_damage`, `safety_security`, `fire_safety_hazard`, `maintenance_repairs`, `facilities_amenities`, `administrative_leasing_concern`, `account_access_issue`, `noise_roommate_concern`, `suggestion_feedback`) — see "Known deviations" below.
- `attachment_paths` — JSON array of Storage paths, up to `MaintenanceTicket::MAX_ATTACHMENTS` (5). Replaced a single `attachment_path` string mid-module once the tenant form's photo picker was redesigned to support multiple photos (see Document History v23 #3).
- `priority` — nullable enum (`urgent`, `non_urgent`), Table 39. Null until an admin classifies it.
- `status` — enum, the 5 values from the status-dropdown Figma (`open`, `seen`, `in_progress`, `resolved`, `rejected`), default `open` — see "Known deviations" below.
- `assigned_to` — nullable FK `users`, null-on-delete.
- `resolved_at` — nullable timestamp, set/cleared automatically whenever `status` crosses into/out of `resolved`/`rejected`.
- Standard `created_at`/`updated_at` (the placeholder's separate `submitted_at` column and `$timestamps = false` were dropped — `created_at` now doubles as "submitted at," matching every other model in the app).

New table **`ticket_replies`**: `ticket_id` (FK, cascade delete), `user_id` (nullable FK `users`, null-on-delete — set when an admin replies), `tenant_id` (nullable FK `tenants`, null-on-delete — set when the tenant replies), `message` (text), `created_at` only (`$timestamps = false`, same pattern as `AdminAccessLog`/`EscalationLog`). Exactly one of `user_id`/`tenant_id` is set per row; which one determines both the displayed author name and whether a message bubble renders as "admin" or "tenant" styled on either page.

`App\Models\MaintenanceTicket` — constants `CATEGORIES`, `STATUSES`, `PRIORITIES` (label maps, reused by both controllers' validation and both blade views' dropdowns), `MAX_ATTACHMENTS = 5`, and the Table 40 thresholds `URGENT_OVERDUE_HOURS = 24` / `NON_URGENT_OVERDUE_DAYS = 3`, plus (v35) `NON_URGENT_ESCALATE_DAYS = 5`. **All three are placeholders the team still has to confirm.** The original manuscript gave ranges (Urgent 24 to 48 hours, Non-Urgent 3 to 5 days); the overdue thresholds use the lower bound and the escalate value uses the upper bound. Everything below is computed live and never stored (v35): `effectivePriority()` returns `urgent` if the stored priority is urgent, or if the ticket is unresolved and at least `NON_URGENT_ESCALATE_DAYS` old; otherwise the stored priority, or `non_urgent` when none is set. `isAutoEscalated()` is true when the effective priority is urgent but the stored one isn't. `isOverdue()` uses the effective priority, so a ticket with no priority is now treated as Non-Urgent instead of ignored. `unresolvedForHumans()` gives the day count. `isOverdue()`/`unresolvedForHumans()` are `false`/`null` once a ticket is `resolved`/`rejected`. **`MaintenanceTicket::overdueSummary()`** (static) returns `['total' => int, 'urgent' => int]` and is the only place overdue tickets are counted.

### Admin side — `TicketController`
- **`GET /tickets`** (`tickets.index`) — whole ticket list rendered once server-side, filtered/searched client-side in JS (same pattern as Tenant Manager/Lease Management/Applications), plus `openCount`/`inProgressCount`/`overdueCount` for the stat strip. **v35:** `overdueCount` comes from `overdueSummary()['total']`. Each row also carries `effective_priority`, `is_auto_escalated`, and `created_ts`. **Default order, applied client-side after filtering and search:** overdue first (effective-urgent before non-urgent, then oldest first), then the other open tickets (oldest first), then resolved/rejected (newest first). An auto-escalated card shows an "Auto-escalated to Urgent · open 5+ days" label; its priority dropdown keeps showing the stored value. The **Urgent** priority filter matches the effective priority, so it includes auto-escalated tickets.
- **`GET /tickets/{ticket}`** (unnamed, JSON) — full detail for the "ticket view" modal: description, `attachment_urls`, the reply thread (each with resolved author name), and `assignable_admins` (every active `admin`-role user) — fetched on demand per card click rather than embedded in the list payload, same on-demand-detail pattern Tenant Manager uses for its View drawer.
- **`PATCH /tickets/{ticket}`** (unnamed) — the modal's single Save action, matching its single Save button: `status` (required), `priority` (nullable — see "Known deviations"), `assigned_to` (nullable, server-verified to actually be an `admin`-role user before assignment, `422` if not), `reply_message` (nullable — creates a `ticket_replies` row with `user_id` set if present). `resolved_at` is set/cleared automatically based on the status transition, not accepted as direct input.

### Admin ticket card layout (v36)
Frontend only. Each `/tickets` card shows the title, then one line "Tenant, Room · Category · #id", then a status line with a coloured dot and the status label, followed by bold red "Overdue" when `is_overdue` (no icon), or the `unresolved_for` note. The auto-escalated label is plain red text. The priority `<select>` and "Assigned: …" line are unchanged in behaviour. The page's `.badge` and `.overdue-flag` styles were removed. The Overdue stat card's "!" icon now renders its dot (`stroke-linecap="round"`). The ticket detail modal was not changed. Admin replies in the modal show the replier's Owner/Admin tag.

### Admin dashboard overdue banner (v35)
`DashboardController::adminDashboard()` passes `ticketOverdueSummary` (from `overdueSummary()`) to `admindashboard.blade.php`. When `total > 0`, a red `.alert-banner` reads "N tickets are overdue (M urgent)." (the urgent part is left out when M is 0) with a **View Tickets** link to `tickets.index`. It is hidden when nothing is overdue. The dashboard Tickets card's overdue count uses the same value. Admin side only; no SMS, email, or scheduled job.

### Tenant side — `TenantTicketController`
- **`GET /my/tickets`** (`tenant.tickets`) — the tenant's own tickets only (`where('tenant_id', $tenant->id)`), each with its full message thread already embedded (the original submission synthesized as the thread's first message, followed by real `ticket_replies` rows) — no separate detail fetch needed, since a tenant's own ticket list is small enough to embed in full up front, unlike the admin's cross-tenant list.
- **`POST /my/tickets`** — Table 32. `category`/`title`/`description` required, `attachment[]` optional array (up to 5, each `jpg/jpeg/png/webp`, max 5MB, validated individually). `bed_id` is snapshotted from the tenant's current `activeContract` at submission time, never accepted from the client. **Stage 3+ restriction (Table 32's precondition/exception) is enforced entirely by the pre-existing `delinquency.check` middleware already on this route group** — a `portal_restricted` tenant never reaches this method at all; the "Ticket submission is unavailable..." banner on the page itself is client-side UX on top of that server-side gate, not the gate itself.
- **`POST /my/tickets/{ticket}/reply`** — the tracking page's own reply box (Figma node 524-6001), beyond Table 34's literal text. Ownership check (`$ticket->tenant_id === $tenant->id`) returns `404`, not `403`, matching the rest of the tenant portal's convention for a cross-tenant mismatch.

### Tenant dashboard integration
`DashboardController::tenantDashboard()`'s "My Tickets" panel — previously hardcoded to an empty collection with a comment noting the module didn't exist yet — now reads the tenant's 5 most recent tickets plus real `openTicketsCount`/`inProgressCount`. Its "View All" button now links to `route('tenant.tickets')`.

### Sidebar wiring
"Tickets" existed as an unlinked nav item on every admin *and* tenant page already (the single longest-standing example of this placeholder pattern in the app, referenced by name in this document's own text for every prior module that used to point at it as the comparison case) — now points at `route('tickets.index')` (admin) / `route('tenant.tickets')` (tenant) across every page, added to each admin page's `NEST_PAGES` search array, and relabeled from "Ticket Module" to "Tickets" on the tenant side per an explicit team request (read as less AI-generated-sounding).

### Known deviations from the manuscript, flagged for BAGUI
*v35: all five are now written into the manuscript (use case reports v3), so none of them is open any more. Kept for history.*

1. **Category list** — Table 32 step 3 says "Maintenance Request / Concern / Feedback"; the actual built list is the 13 categories from the approved type-dropdown Figma frame. Figma was treated as authoritative since it's the real approved UI, but the manuscript itself was never updated to match.
2. **Status list** — Table 33's literal text is "Open / In Progress / Resolved"; the actual built list is the 5 values from the approved status-dropdown Figma frame (adds Seen and Rejected, drops Closed entirely — Table 33's separate "Close Ticket" extend-use-case has no distinct status in what's built; Resolved/Rejected are being treated as covering it). Needs explicit team sign-off that this is intentional, not a gap.
3. **No priority control anywhere in the given modal Figma** — Table 39 requires classification and treats a missing value as a validation error; the modal as designed has no field for it. A standalone card-level selector was added as a stopgap (see Document History v23 #2) — needs a real decision on where this belongs before backend validation can be tightened to actually require it.
4. **"Notified" (Tables 32/33) is passive, not an active push** — a tenant or admin sees a new ticket/reply/status change only the next time they load the relevant page; no email/SMS fires automatically. Deliberate scope decision to keep this build backend-and-frontend-only for now, flagged in case the team wants real notifications later.
5. **No date-range filter** — Table 33 step 2 lists category, status, *or date range* as filter options; only category/status/priority were built on the admin list page.

---

## Tenant Manager — Tables 14, 15, 37
*Routes require `['auth','admin']` session middleware. `App\Http\Controllers\TenantController`, view `resources/views/tenantmanager.blade.php`.*

Admin-facing tenant list/search/filter, individual profile view, edit, walk-in registration, and deactivate/reactivate — the three use cases the manuscript treats as one connected management flow.

### Schema additions
- `tenants.date_of_birth` (date, nullable)
- `tenants.home_address` (string, nullable)
- `tenants.tenant_type` (string(30), nullable — plain string rather than an enum, since Table 13's and Table 15's tenant-type vocabularies disagreed in the original manuscript; v35 aligned both forms to the same four types)
- `tenants.id_document_path` / `tenants.signed_contract_path` (string, nullable)
- `tenants.deactivation_reason` (string(500), nullable), `tenants.deactivated_at` (timestamp, nullable)
- `tenants.deactivated_by` (nullable FK → `users.id`) — added after a post-build check against Table 37's postcondition ("Log the deactivation action with administrator ID, reason, and timestamp"), which the initial build had missed.
- **`tenants.status` enum value renamed `archived` → `inactive` (v20)**, to match Table 37's literal wording. Enum is now `pending_move_in_payment`, `active`, `inactive`. Migrated via a three-step `ALTER TABLE` (widen the enum to allow both values → move existing rows → narrow to the final set) so no row was ever left in an invalid state mid-migration.
- **`tenants.first_name` / `tenants.last_name` (v26)** — replace the old `tenants.full_name` column, which was dropped. See "Applicant & Tenant Name Format," further below, for the full detail, including the computed `full_name` accessor that replaced the real column.

### `GET /tenant-manager` (`tenant-manager.index`) — Table 14, step 1
Renders the full tenant list once server-side (name, room/bed, lease start, monthly rate, derived status), filtered/searched/sorted/paginated client-side in JS — same pattern as Lease Management and Delinquency.

**Derived `status` (not a raw DB column read directly):**
- `inactive` if `tenants.status = 'inactive'`
- `delinquent` if `is_blacklisted` or the tenant has any `overdue` billing statement (same definition `DelinquencyController` uses for "belongs on the Delinquency page")
- `pending_move_in_payment` if `tenants.status = 'pending_move_in_payment'`
- `active` otherwise

**Filters, both client-side:** status (Active/Delinquent/Pending Move-In/Inactive) and tenant type (Student/Working Student/Full-time Employee/Part-time Employee) — the latter added after a manuscript check found Table 14 explicitly calls for filtering "by tenant type," which the initial build omitted. **v35:** Transient Worker was removed from this filter and from the Add/Edit forms (`TenantController::TENANT_TYPES`), so Tenant Manager and the public Apply form offer the same four types. No existing tenant had that value.

**Sort control (v27), also client-side:** Latest Date Started (default) / Oldest Date Started / Name A–Z / Name Z–A / Rent High-to-Low / Rent Low-to-High. Date-started sorting combines the active lease's `start_date` (a date only, no time) with that same lease record's own `created_at` timestamp as a tiebreaker, so two tenants who started on the same calendar date still sort by which lease was actually recorded first (or most recently) rather than an arbitrary database order. Powered by two new raw fields on each row, `date_started_raw` (`Y-m-d`) and `contract_created_raw` (ISO 8601) — the existing `date_started` field (`M Y`, e.g. "Sep 2026") is unchanged and still used for display.

**`raw_status` field added (v27), alongside the existing derived `status` above.** The derived label can call a tenant "delinquent" purely from an overdue *move-in fee* bill, before they've ever actually occupied a room — which meant this page's own headline count, if built naively on top of the derived label, could include tenants who haven't moved in at all. `raw_status` carries the real, unmodified `tenants.status` column value specifically so anything that needs to ask "does this tenant genuinely occupy a room right now" (this page's own headline count; see below) has a reliable field to check instead of the display label.

**The headline tenant count, corrected (v27).** Now computed as `raw_status === 'active' && !is_blacklisted`, both server-side (`page()`, for the initial page load) and client-side (`countActiveTenants()`, so it stays correct after searching/filtering). This deliberately matches `DashboardController`'s own "Total Tenants" definition exactly — the two numbers should never disagree. Two real bugs were found getting here, in order: first, the count included blacklisted tenants (blacklisting never touches `tenants.status`, only `is_blacklisted`, so a Stage 6 tenant's raw status stays `'active'` unless separately excluded); after fixing that, a second pass mistakenly counted on the *derived* `status` label instead of `raw_status`, which let `pending_move_in_payment` tenants with an overdue move-in-fee bill through as "delinquent." Caught by directly comparing this page's count against Dashboard's Total Tenants card for the same data and asking why they disagreed, rather than trusting either number in isolation.

### GET `/tenant-manager/{tenant}` — Table 14, step 3 (View) and the Edit modal's pre-fill
Single endpoint serving both: full personal info (including `first_name`/`last_name` separately, plus the computed `full_name`, since v26), tenant type, emergency contact, active contract (room/bed/dates/rate), outstanding balance, payment count, document URLs, and — when the tenant is inactive — the deactivation audit trail (reason, timestamp, admin name).

### POST `/tenant-manager` — Table 15 (Add New Tenant)
Walk-in registration in one transaction: creates the `User` login (tenant role, random temp password), the `Tenant` record, and an active `LeaseContract` on the selected vacant bed; occupies the bed; emails credentials (`TenantAccountCreatedMail`). `first_name`/`last_name` required separately (v26 — previously one `full_name` field); `id_document` required; `signed_contract` optional (admin can complete the physical-signature step later via Lease Management if not attached here). Duplicate-email check (`409`); bed-availability check (`409`).

**Room/bed picker (v20 fix):** only rooms with at least one vacant bed are listed, each showing room type and remaining vacant-bed count; if nothing qualifies at all, the dropdown shows "No Available Rooms" per Table 15's own specified exception message instead of an empty-looking list.

**Design decision, flagged for BAGUI (same shape as the resolved old Table 16 decision — see Document History v29):** a walk-in tenant is set `status: 'active'` immediately, skipping `pending_move_in_payment` — the admin is completing the whole registration in person, so there's no separate online payment step to gate on. Online applicants (via Application approval) still start `pending_move_in_payment` as before; this only affects tenants added directly through this page.

**Flagged, not built:** Table 15 step 4.1 says selecting a tenant type should conditionally change required fields/documents, but the manuscript never specifies what actually differs per type. Building something here would mean inventing requirements that don't exist in the use case — worth a direct question to BAGUI before this is attempted.

### POST `/tenant-manager/{tenant}` — Table 14, steps 4–7 (Edit)
Standard field validation (`first_name`/`last_name` separately, since v26) + optional document replacement. Keeps the linked `User`'s name/email in sync so the portal login and this record never drift apart — `first_name`+`last_name` are combined into `User.name` on save, since `users` has no separate name columns of its own.

### POST `/tenant-manager/{tenant}/status` — Table 37 (Deactivate) + its reverse (Reactivate)
- **Deactivate:** requires a reason (`422` if missing); blocks with `409` if the tenant has any `unpaid`/`partial`/`overdue` billing statement (Table 37's exception path, exact wording); on success, sets `status: 'inactive'` + `deactivation_reason` + `deactivated_at` + `deactivated_by` (admin ID), sets the linked `User.is_active = false`, and — since move-out itself has no separate use case implemented (see old Table 16 in Document History v29; it was removed from the manuscript in v3) — also terminates the tenant's active lease contract and releases the bed back to `vacant`. **This synthesis of old Table 16 and Table 37 into one action is now the team's settled design, not a placeholder** — a separate Record Move-Out action was actually built and then deliberately rolled back after the team judged it duplicated this action almost entirely. See v29 in Document History for the full reasoning.
- **Reactivate:** sets `status: 'active'`, clears `deactivation_reason`/`deactivated_at`/`deactivated_by`, restores `User.is_active = true`. Does not restore a lease or bed — a reactivated tenant needs a fresh lease added via Lease Management if they're moving back in.
- **Delinquent or already-active tenants can't be "reactivated" (v34).** Before reactivating, `setStatus()` checks the same two conditions the derived `delinquent` label uses:
  - `is_blacklisted`, or any `billing_statements.status = 'overdue'` → `409` "A delinquent account can't be set to active. It clears automatically once the tenant's overdue balance is paid and confirmed."
  - `tenants.status` already `active` → `409` "This tenant is already active."

  Previously both cases returned the normal success message while changing nothing, since a delinquent tenant's stored status is already `active`. The Set Status modal already displays server errors, so no frontend change was needed. **Side effect:** an evicted (blacklisted) tenant who was later deactivated can't be reactivated until an admin clears the blacklist with Delinquency → Reset. Note that Reset alone does not remove the Delinquent label if an overdue bill remains; only paying that bill does.

### Sidebar wiring
"Tenant Manager" existed as an unlinked nav item on every admin page already (same placeholder pattern "Tickets" used until v23) — now points at `route('tenant-manager.index')` across all admin pages, and added to each page's page-search `NEST_PAGES` array.

### A note on how this module's bugs were caught
Every bug fixed in this module (v19, v20, and now v27's count-accuracy fix combined) was found either by a deliberate line-by-line check against the actual manuscript text after building, by the team physically clicking through the finished page rather than trusting a read-through of the code, or — new to v27 — by cross-checking one page's own number against a different page's supposedly-equivalent number and asking why they disagreed rather than assuming either was already correct. None of these classes of bug would have been visible from code review alone. Worth treating "click through it for real, and check it against anything else in the app claiming to measure the same thing" as a standard step before calling any future module done, not an optional extra.

---

## Tenant Profile (Read-Only) + Change Password — built v27
*Route unchanged: `GET /account` (`tenant.account`), `['auth','tenant','movein.check','moveout.check','delinquency.check']` session middleware. View `resources/views/tenantaccount.blade.php`, previously Change Password only.*

Adds a **Profile** tab (now the default tab) to the page that already held Change Password, rather than a separate page — matches the sidebar's existing single "Profile" nav item, which already pointed at this route.

- **Profile tab is entirely read-only.** Shows the tenant's own personal info (name, email, contact number, date of birth, home address, tenant type), emergency contact, and room & lease details (room/bed, lease start/end, monthly rate) — every field sourced directly from the same `Tenant`/`LeaseContract` data Tenant Manager's own View drawer reads, no separate endpoint. **There is no edit form anywhere on this page.** A persistent banner at the top of the tab instead directs the tenant to submit a Ticket for any correction, linking to `route('tenant.tickets')`.
- **Flagged for BAGUI:** Table 10 ("Manage Account Settings") describes tenants submitting an editable profile-update form that goes into a Pending state for administrator Approve/Reject, with old/new values shown for comparison. What's built instead skips the editable-form/approval-queue concept entirely in favor of routing every change request through the existing Tickets module as a plain support request. This is a real scope simplification against the manuscript's literal description, not an equivalent implementation of the same use case — worth a direct decision on whether Table 10's own approval-workflow shape is still wanted, or whether Tickets-as-the-change-request-path is the team's actual final answer.
- **"View Lease Contract" is conditional, not always shown.** The `/account` route resolves the tenant's `activeContract` and computes a signed-document URL, preferring the lease's own `signed_document_url` (if `esign_status = 'signed'`) and falling back to the tenant's onboarding-time `signed_contract_path` if the lease itself has no signed document attached yet. If neither exists, the section shows a plain "not available yet" note instead of a broken or absent-looking button — same pattern already used elsewhere in the app (e.g. Stage 5's demand letter button hiding behind a "still being prepared" note when not ready).
- **Change Password tab is unchanged** from its pre-v27 behavior — same endpoint (`PUT /password`), same validation, same JSON-response fix documented in the Auth section above.

---

## Facility Management (Floors / Rooms / Beds)
*Routes require `['auth','admin']` session middleware. Handled by `VacancyController`.*

Bed status enum: `vacant`, `reserved`, `occupied`, `maintenance`.

### POST `/vacancy/rooms` / PUT `/vacancy/rooms/{room}` — room create/edit
`room_no`, `floor`, `room_type`, **`room_type_id` (v41, nullable, `exists:room_types,id`)**, `amenities[]`, `monthly_rate`, `bed_count` (1–20 since v41, was 1–8), `bed_statuses[]`, `photos[]` (up to 8, 5MB each), plus **`monthly_utility_cost` and `monthly_wifi_cost` (v34, nullable numeric ≥ 0, default 0)** — whole-room amounts, split per bed at billing time (see "Room Wi-Fi & Utilities Pricing"). The room payload returned (and embedded in the page) includes both fields alongside `monthly_rate` and `price_per_bed`. **v41:** when a `room_type_id` is set, `Room::syncFromRoomType()` copies the type's name into `room_type` and its whole-room price into `monthly_rate` after every save, so the typed values are overwritten. The page also receives `roomTypes` for the dropdown.

### DELETE `/vacancy/rooms/{room}` / DELETE `/vacancy/floors/{floorNumber}`
Blocked with a `409` and a clear message if any bed involved has `applications` or `lease_contracts` history — prevents orphaning records instead of surfacing a raw SQL error.

> **Dev utility, not a route:** `php artisan test:purge-room {room_no} [--with-tenant]` safely cascade-deletes a test room's entire history in dependency order. Confirms before deleting. Not for production.

### GET `/vacancy-monitoring` (`vacancy.index`) *(admin, page render)*
Also served at `GET /admin/add-floor` (`admin.addfloor`), same `VacancyController::index()`. **v36:** `stats` gains `reserved` (count of beds with status `reserved`), shown in a fifth stat card; reserved beds are purple in the bed map and legend; the room editor's per-bed dropdown includes **Reserved** (previously a reserved bed opened as "Vacant", and saving would have un-reserved it); and clicking a reserved bed no longer cycles its status — it shows "This bed is reserved for an applicant, so clicking won't change it. To change it on purpose, use Edit on this room." The click cycle for other beds is unchanged (vacant → occupied → maintenance). Each room includes `price_per_bed`, `monthly_utility_cost` and `monthly_wifi_cost`; each room card shows "Utilities ₱… · WiFi ₱…" under the rent. *(v34: this heading previously said `GET /vacancy/rooms`, which isn't a registered route; `/vacancy/rooms` only accepts POST.)*

---

## Per-Bed Rate Calculation
**v41: rooms with a room type are priced by the type.** `Room::perBedRate()` returns `RoomType::perBedRate($bedCount)`. A `per_bed` type charges every tenant the listed rate. A `per_room` type (for example a solo room) splits the listed price across the beds. The rule below applies only to rooms without a type.

`rooms.monthly_rate` is the rent for the **whole room**, not one tenant's share. `Room::perBedRate() = monthly_rate / count(beds in this room)`, computed live (not stored, so it stays correct if beds are added/removed later). Applied at application approval, manual lease creation, walk-in tenant registration, and the public Rooms listing price.

**Wi-Fi and utilities use the same rule (v34).** `Room::utilityShares()` returns `[monthly_utility_cost / beds, monthly_wifi_cost / beds]`, rounded to 2 decimals (divisor floored at 1). Split by **bed count, not occupants**, as a deliberate team decision "for now" so every tenant's share stays fixed however full the room is. If the team later switches to occupied beds, change `perBedRate()` and `utilityShares()` together so rent and charges stay consistent.

---

## Room Wi-Fi & Utilities Pricing (v34)
*Admin input on the Vacancy Monitor; applied by billing generation; previewed on the tenant Billing page.*

**Why it existed as a gap:** `floors.monthly_utility_cost`/`monthly_wifi_cost` (added 2026-08-26) were what `BillingController` split across every active tenant on a floor, but no screen ever let an admin set them, so every generated statement carried ₱0 for both.

### Schema
Migration `2026_09_25_000001_add_utility_costs_to_rooms_table`: `rooms.monthly_utility_cost`, `rooms.monthly_wifi_cost` — `decimal(10,2)`, default `0`, after `monthly_rate`. The floor columns are left in place but **nothing reads them anymore**.

### Where it's set
Vacancy Monitor → Add Room / Edit Room window: **Monthly Utilities** and **Monthly WiFi** fields, with the hint "Whole-room amounts, split evenly by the number of beds, same as rent. Changes apply from each tenant's next generated bill." Saved via the existing `POST /vacancy/rooms` / `PUT /vacancy/rooms/{room}`.

### How it's billed
`BillingController::generateForContract()` → `splitUtilityCost($contract)` → `$contract->bed->room->utilityShares()`. The result is written to `billing_statements.utilities_amount` / `wifi_amount` and included in `total_amount`. If a contract has no room, the statement still goes out with `0`/`0` and a `billing.utility_data_missing` stub event is logged (Table 18's exception path, unchanged).

**Mid-month price changes never touch issued bills.** Amounts are copied onto each statement when it's generated, so a changed price takes effect from each tenant's next generated statement ("+ Generate This Month's Billing" or the scheduler).

### Next Bill Estimate (tenant `/billing`)
The `/billing` closure route computes `nextBill` = `{ rent: contract.monthly_rate, utilities, wifi, total }` from the tenant's active contract and the room's current prices, and the page shows it in a **Next Bill Estimate** card labelled "Based on current room prices. Not yet added to your balance." Absent (card hidden) when the tenant has no active contract with a room. No endpoint; server-rendered.

---

## Public Rooms
*No authentication required. Registered under `/public-api/...`.*

### GET `/public-api/rooms`
Query params: `floor_id`, `status`, `sort` (`availability`/`price_low`/`price_high`).

```json
[
  {
    "id": 5, "room_no": "35", "room_type": "standard",
    "monthly_rate": "4555.00", "price_per_bed": 2277.50,
    "status": "available", "amenities": ["wifi", "electricity", "water"],
    "photo_url": "http://127.0.0.1:8000/storage/room-photos/test.jpg",
    "photo_urls": ["..."], "has_vr_tour": true,
    "beds": [{ "label": "Bed 1", "status": "occupied" }, { "label": "Bed 2", "status": "reserved" }]
  }
]
```

### GET `/public-api/rooms/{room}` / GET `/public-api/rooms/{room}/beds`
Single-room detail; and **vacant-only** beds for that room — the actual mechanism preventing two applicants (or two admin flows, including Tenant Manager's Add New Tenant) from selecting the same bedspace.

### GET `/public-api/filter-options` / GET `/public-api/dorm-info`
Floors/room types for filter dropdowns; dorm name, description, address, policy text, `cover_photo_url`, `brand_logo_url`, `is_bir_verified`, `amenities`, `house_rules_list` and `policies_file_url`. Since v41 `policies_file_url` is always set, because the policies document is now the generated Rules and Regulations PDF.

### GET `/dorm-info/policies-file` / GET `/dorm-info/policies-file/download` *(public)*
**v41:** stream or download the dorm's **Rules and Regulations** (unsigned), generated by `TenancyDocuments`. This replaced the uploaded policies PDF. The Tenant Agreement and the Payments and Fees Schedule are **not** public, because the schedule lists the owner's bank and GCash accounts.

---

## VR Tours — multi-scene system
*Admin routes require `['auth','admin']` session middleware.*

A room can have multiple linked panorama "scenes" with clickable navigation between them.

- **POST `/vr-tours/rooms/{room}/scenes`** — uploads a panorama (`multipart/form-data`, field `panorama`). Auto-detects field-of-view from aspect ratio (2:1 = full 360°; otherwise a partial "phone panorama" sweep, `PHONE_VERTICAL_FOV = 60.0`).
- **PATCH `/vr-tours/scenes/{scene}`** — rename. **PATCH `.../view`** — adjust `haov`/`vaov`/`v_offset`. **POST `.../default`** — set landing scene. **DELETE `/vr-tours/scenes/{scene}`** — deletes a scene; **any hotspot on another scene pointing to it is left dangling, not cleaned up automatically** (still open).
- **POST `/vr-tours/scenes/{scene}/photo`** *(v41)*: replaces the scene's picture (`panorama`, `jpg/jpeg/png`, max 20MB) and keeps its name, place in the tour and arrows. The field of view is re-estimated, the old file and its painted ceiling/floor copy are deleted, and `photo_updated_at` is set, so the public tour shows a new "Last updated on ..." date. Returns the updated scene.
- **POST `/vr-tours/scenes/{scene}/hotspots`** / **DELETE `/vr-tours/hotspots/{hotspot}`** — navigation arrows, placed via click-to-place `pitch`/`yaw`.
- **PATCH `/vacancy/rooms/{room}/vr-info`** — caption + public visibility toggle.
- **GET `/public-api/rooms/{room}/vr-tour`** *(public)* — full Pannellum config, with `minPitch`/`maxPitch` constrained to the photo's real coverage (a workaround that hides the black void a partial panorama doesn't cover — doesn't add missing image data).
- **GET `/public-api/vr-tours`** *(public)* — every room with at least one public VR scene. Only rooms with status `available` are listed, so a full room's tour doesn't show publicly.

---

## Dormitory Profile — Table 38, admin UI built (v21)
Singleton `dormitory_profile` row. Fields: `policies_file_path` *(no longer read since v41)*, `contract_template_path` *(unused)*, `business_permit_path`, `bir_registration_path` *(new)*, `gcash_number`, `bdo_account_number` *(both unused since v34 — superseded by the `payment_methods` table; see "Payment Methods & QR Codes")*, `dorm_name`, `description`, `address`, `contact_number`, `contact_email`, `logo_path` (reused as the cover photo — was never actually used anywhere before v21), `brand_logo_path` *(v37: the dorm's own square logo; see "Dual Branding")*.

- **`DormitoryProfile::current()`** — unchanged accessor, still used by the Stage 5 demand letter PDF and both tenant Delinquency takeover pages' Contact Admin buttons.
- **`DormitoryProfile::isBirVerified()`** *(new)* — `(bool) $this->bir_registration_path`. Deliberately no separate boolean column: presence of the file **is** the flag, so admin and public views can never disagree about whether the badge should show.

### Schema (v21 additions)
- `dormitory_profile.business_permit_path`, `dormitory_profile.bir_registration_path` — nullable strings.
- New table **`dormitory_amenities`**: `key` (unique string), `label`, `is_enabled` (bool), `sort_order`. **Seeded with 8 fixed rows by the migration itself** (`wifi`, `kitchen`, `study_area`, `parking_area`, `cctv`, `hot_cold_shower`, `laundry_area`, `24_7_security`), all `is_enabled = true` by default. This is a **toggle list, not a free-add list** — matches the Figma checklist design exactly; the admin turns items on/off, doesn't create new ones (flagged as a possible future addition below).
- New table **`dormitory_house_rules`**: `rule_text` (string 500), `sort_order`, and since v41 `section` (nullable string 80, for example "A. Conduct and Respect"). `/dorm-info` groups the rules by section and numbers them. Freely addable/editable/deletable by the admin, unlike amenities.
  - **Deliberately separate from the pre-existing `dormitory_profile.house_rules` long-text column.** That column is untouched and still serves its original purpose only — plain-text fallback content shown on `/dorm-info` when no policies PDF has been uploaded (same for `payments_and_fees`/`checkout_procedures`). The new table backs a different, additive UI element: the individually-editable rules list on the admin preview panel and the public listing card. Two things named similarly, doing genuinely different jobs — worth remembering if this ever looks like duplicate data.

### `App\Http\Controllers\DormitoryProfileController` — new, `['auth','admin']` session routes
- **GET `/dormitory-profile`** (`dormitory-profile.index`) — renders `admindormitoryprofile.blade.php`: profile fields, cover photo, all three document upload cards (file name, extension, and an inline preview image if the uploaded file is itself an image rather than a PDF), amenities checklist, house rules list, and a live "Public Listing Preview" panel that mirrors what the public page will show.
- **POST `/dormitory-profile`** (`dormitory-profile.update`) — `dorm_name`/`contact_number`/`address`/`description` required, `contact_email` optional. Text fields only — every file upload below is independently endpointed so a slow image upload can't block saving a one-word description fix, and vice versa.
- **POST `/dormitory-profile/cover-photo`** (`dormitory-profile.cover-photo`) — `multipart/form-data`, field `cover_photo`, `jpg/jpeg/png/webp`, max 5MB.
- **POST `/dormitory-profile/brand-logo`** (`dormitory-profile.brand-logo`) *(v37)* — `multipart/form-data`, field `brand_logo`, `jpg/jpeg/png/webp`, max 2MB. Replaces (and deletes from storage) any previous logo. Response `200`: `{"message": "Dorm logo updated.", "brand_logo_url": "<public storage URL>"}`. The admin card updates its preview, its "Sidebar preview" and the real sidebar in place, without a reload. No DELETE route: the logo can be replaced but not removed.
- **Removed in v41:** `POST /dormitory-profile/policies-file`. The public page now always shows the generated Rules and Regulations. The Rental Policy, Room Types, Other Charges and document preview routes are documented in "Dorm Documents & Rental Policy (v41)".
- **POST `/dormitory-profile/business-permit`** (`dormitory-profile.business-permit`) / **DELETE (same URI, unnamed)** — field `business_permit`, `pdf/jpg/jpeg/png`, max 10MB. **Deliberately admin-only** — no public surface at all (see the mapping table below).
- **POST `/dormitory-profile/bir-registration`** (`dormitory-profile.bir-registration`) / **DELETE (same URI, unnamed)** — same file rules. This is the upload that turns the public verification badge on/off, per BIR RMC No. 038-2026.
- **POST `/dormitory-profile/amenities/{amenity}/toggle`** — `{"is_enabled": bool}`. Flips one of the 8 fixed rows; never creates or deletes.
- **POST `/dormitory-profile/house-rules`** (`dormitory-profile.house-rules.store`) / **PATCH `/dormitory-profile/house-rules/{houseRule}`** / **DELETE `/dormitory-profile/house-rules/{houseRule}`** — full CRUD, each action saved independently (no batch/draft state).
- **Review Moderation routes (v32)** also live under `/dormitory-profile/reviews/...` and render as a card on this page, but belong to `ReviewModerationController`. See "Reviews & Ratings" above.
- **Payment Methods routes (v34)** live under `/dormitory-profile/payment-methods...` and render as a card on this page (placed before Amenities), but belong to `PaymentMethodController`. `page()` passes `paymentMethods` (each via `PaymentMethod::toClientArray()`). See "Payment Methods & QR Codes" below.

**File-type-aware previews:** each of the three document upload responses includes `file_ext`, and — if the uploaded file is itself an image (`png/jpg/jpeg/webp`, checked by extension) — an `image_url`. The admin card, public badge, and public listing card all render that real image inline when applicable (e.g. a BIR seal graphic uploaded as a PNG), falling back to a generic document icon for actual PDFs.

### Public reflection — where each admin field actually shows up
| Admin field | Homepage (`/`) | `/dorm-info` listing card | Admin preview panel |
|---|---|---|---|
| Dormitory Name | Hero heading ("Welcome to [name]!"), "Find your room at [name]" stats heading, footer | Page heading | Preview name |
| Description | Hero paragraph | *(not shown yet — open item below)* | Preview description |
| Contact Number / Email / Address | Footer contacts | Address only | Preview address |
| Cover Photo (`logo_path`) | Hero image | Cover image on listing card | Top of preview card |
| Dorm Logo (`brand_logo_path`, v37) | Public top bar and footer logo | Public top bar | Dorm Logo card and its "Sidebar preview". Also every sidebar, login page, browser tab, email and PDF (see "Dual Branding") |
| Rules and Regulations PDF (v41, generated, replaced the uploaded policies PDF) | — | Embedded PDF viewer with Download button | Preview via `/dormitory-profile/documents/rules` |
| Business Permit | — | — | — *(intentional, admin-only)* |
| BIR Registration | Footer badge ("Registered with the Bureau of Internal Revenue" — real uploaded image if it's a picture, generic checkmark if a PDF) | Same badge, top of listing card | Same badge, top of preview card |
| Amenities (enabled) | "Available Resources" stat (first 2 enabled labels, comma-joined; falls back to "AC, WIFI, CR" if none enabled) | Icon row on listing card | Icon row in preview card |
| House Rules (structured list) | — | Numbered list, grouped by section (v41) | Bulleted list in preview card |

**"Happy Tenants" homepage stat is also now real** — `Tenant::count()`, no longer a hardcoded `250+`.

### `PublicController` changes
`home()` and `dormInfoPage()` were both updated to pass `coverPhotoUrl`, `isBirVerified`, `birRegistrationImageUrl` (and `dormInfoPage()` additionally `amenitiesList`, `houseRulesList`, computed from the two new models) to their views, plus `home()` now also passes `happyTenantsCount` and `availableResources`. `/public-api/dorm-info` was brought up to date in v38.

### Payment Methods & QR Codes (v34)
*Admin routes `['auth','admin']`; tenant consumption on `/billing` and `/move-in/*`.*

**Replaces:** hardcoded GCash/BDO/Cash options in `tenantbilling.blade.php` and `tenantmoveinpaymentmethod.blade.php`; the placeholder "Merchant Name Here / 0999-XXX-1234" QR panel; the move-in page's client-side `qrcode@1.5.3` canvas that encoded only the text `GCash: <number>` (not scannable by any payment app); and `dormitory_profile.gcash_number`/`bdo_account_number`, which no screen could edit.

#### Schema
Migration `2026_09_25_000002_create_payment_methods_table`:
- **`payment_methods`**: `id`, `type` (`ewallet` | `bank` | `cash`, string(20)), `name` (string 60), `account_name` (string 120, nullable), `account_number` (string 60, nullable), `qr_path` (nullable, `public` disk under `payment-qr/`), `instructions` (string 500, nullable), `sort_order` (unsigned int), timestamps.
- **`payments.payment_method_label`** (string 60, nullable, after `payment_method`) — a snapshot of the method's name at submission time (e.g. "Maya"), so the record still reads correctly after the method is renamed or deleted. **`payments.payment_method` stays the coarse enum** (`cash`/`gcash`/`bank_transfer`/`other`) that reports and admin filters group by; it's derived, never typed by the tenant.

Migration `2026_09_25_000003_add_default_cash_payment_method`: inserts one `cash` row ("Cash Payment", instructions "Pay in person at the lobby / admin office.") **only if no cash method exists**. `down()` deliberately leaves it (removing it would strand payments recorded as cash). No other starter rows: the admin adds e-wallets and banks themselves.

#### Model — `App\Models\PaymentMethod`
- `TYPES = ['ewallet','bank','cash']`; `ordered()` (by `sort_order`, then `id`); `isOnline()` (`type !== 'cash'`).
- `paymentEnum()` → `cash` → `cash`; `bank` → `bank_transfer`; e-wallet whose name contains "gcash" → `gcash`; any other e-wallet → `other`.
- `brand()` → `gcash`/`maya`/`bdo`/`bpi` when the name contains one (used for logo colors), else `cash` or `default` (app green).
- `toClientArray()` → `{ id, type, name, account_name, account_number, instructions, qr_url, brand }`, with `qr_url` from `Storage::disk('public')->url()` or `null`. This is the shape every page embeds.

#### Admin endpoints — `PaymentMethodController`
| Method | Path | Body | Result |
|---|---|---|---|
| POST | `/dormitory-profile/payment-methods` | multipart: `type`, `name`, `account_name`, `account_number`, `instructions?`, `qr?` | `201 { message, method }`; appended at the end of `sort_order`. |
| POST | `/dormitory-profile/payment-methods/{paymentMethod}` | same, plus `remove_qr=1` to clear the image | `200 { message, method }`; a new `qr` replaces (and deletes) the old file. |
| DELETE | `/dormitory-profile/payment-methods/{paymentMethod}` | — | `200 { message }`; deletes the QR file too. |

Validation: `type` in `TYPES`; `name` required ≤60; for `ewallet`/`bank`, `account_name` (≤120) and `account_number` (≤60) **required** with messages "Enter the name on the account tenants will send money to." / "Enter the mobile or account number tenants will send money to."; `instructions` ≤500; `qr` image `jpg/jpeg/png/webp` ≤5MB. For `cash`, account fields are forced to `null` and any QR is dropped.

**Cash protections (`422`):** deleting a cash method ("Cash is always available to tenants and can't be deleted."); changing a cash method's type; changing another method's type to cash, or creating a second cash method ("Cash is already set up. Edit the existing Cash method instead."). The admin can still rename Cash and edit its instructions.

#### Admin UI — Payment Methods card on `/dormitory-profile`
One row per method: type icon (phone for e-wallet, bank for bank, peso sign for cash) in the brand color, name, type badge ("Cash · Always on" for cash), account name · number (or cash instructions), QR thumbnail or "No QR", Edit, and Delete (not shown for cash; confirms first). **+ Add Method** and Edit open one window: Type (cash not offered for new methods; locked when editing cash), Name, Account Name, Mobile / Account Number (label reads "Account Number" for banks), QR upload with preview and Remove QR, Instructions for tenants. Closes on Escape or outside click; errors show inside the window. Separate `<script>` IIFE with its own `api()`/`esc()`, same pattern as Review Moderation.

#### Tenant `/billing`
- The route passes `paymentMethods` (all methods, `toClientArray()`). The method list is rendered from it, keyboard-selectable (`role="radio"`, Enter/Space). Empty state: "No payment methods are set up yet. Please contact the dormitory admin."
- **Online method → Proceed →** payment screen: **Selected Payment Method** shows name, account name · number, and instructions; a **QR Code** card shows the uploaded image (tap to open the QR viewer) or a neutral "No QR code set up" placeholder. On phones the QR card is placed first, above the upload and **Payment Details** cards.
- **Cash →** the "Proceed to the Lobby / Admin Office" dialog (amount, due date, instructions, no-extension warning, "Got it"); **Proceed to Payment stays disabled**.
- Submission sends `payment_method_id` (see "Tenant Portal — Billing & Penalties"). Payment history shows `payment_method_label` when present.

#### Move-in (`/move-in/payment-method`, `/move-in/payment`)
- `TenantOnboardingController::paymentMethod()` lists **online methods only** (move-in requires a proof upload) and pre-selects the tenant's earlier choice. Empty state: "No online payment methods are set up yet. Please contact the dormitory admin."
- `storePaymentMethod()` validates `payment_method` as an existing, non-cash `payment_methods.id` ("That payment method is no longer available. Please choose another.") and stores the **ID** in the `move_in_payment_method` session key (previously the string `gcash`/`bdo`).
- `payment()` loads that method; if it's missing, deleted, cash, or an old pre-v34 string value, the tenant is redirected back to choose again. The page shows a plain card: "Pay via", method name, account name · number, instructions, and the QR (tap to enlarge) or the "No QR code set up" placeholder. The submit sends `payment_method_id`.

#### QR viewer — `resources/views/partials/qr-lightbox.blade.php`
Included once by `tenantbilling` and `tenantmoveinpayment`. Any element with `data-qr-src` (and optional `data-qr-name`) opens it. Full screen; image fills the stage; zoom 1–8× via buttons (level shown), mouse wheel toward the cursor, two-finger pinch, double-click/double-tap (3×, again to reset), and `+`/`-`/`0` keys; drag to pan with bounds; fit-to-screen; **Download** as `<name>-qr.<ext>` (same-origin `/storage` URL, so the `download` attribute works); closes on ✕ or Escape and returns focus; page scroll locked while open.

#### Cash payments and delinquency
Choosing Cash **records nothing and changes nothing on the bill**; there is no "intent to pay" state, so it cannot be used to avoid delinquency. The only thing that changes a bill is an admin-recorded payment (`POST /billing/{billingStatement}/payments/cash`, "+ Record Payment Entry"), which saves the payment already `approved`, resyncs the statement to `paid`/`partial`, calls `resolveSettledEscalations()` immediately when it becomes `paid` (v25), and activates a move-in tenant when applicable. Escalation (`escalation:process`, daily) still flips unpaid/partial statements past `due_date` to `overdue` and advances only overdue ones, regardless of the tenant's chosen method.

| When the cash is recorded | Outcome |
|---|---|
| On/before the due date, in full | `paid`; never becomes overdue; no escalation. |
| After the due date, in full | Open escalation logs resolved and `portal_restricted` lifted immediately; penalties already applied remain. |
| Partial amount | `partial`; becomes `overdue` if the remainder isn't paid by the due date. |
| Tenant already blacklisted (Stage 6) | Blacklist remains until Delinquency → Reset. |

Operational rule for admins: record cash the same day it's received; a backdated `payment_date` does not undo overdue status or stages already applied. A genuine delay is handled with Delinquency → **Pause** (logged), never automatically.

### Shared partial: `partials-amenity-icon.blade.php`
Deliberately breaks this project's usual "one self-contained Blade file per page" convention — the same 8 hand-authored line-icon SVGs (switched on the amenity's `key`) are needed identically in three places (admin checklist, admin live preview, public listing card), so a shared `@include` was used once instead of copy-pasting the same `@switch` three times.

### Sidebar wiring
"Dormitory Profile" existed as an unlinked nav item on every admin page already (same placeholder pattern "Tickets" used until v23) — now points at `route('dormitory-profile.index')` across all admin pages, and added to each page's `NEST_PAGES` page-search array.

---

## Dual Branding — dorm first, "Powered by NEST.PH" (v37)
*No routes of its own (the only new route is the Dorm Logo upload under "Dormitory Profile"). Frontend, emails and PDFs.*

**Why:** under the Data Privacy Act (RA 10173) the dormitory is the **Personal Information Controller (PIC)** and NEST.PH is the **Personal Information Processor (PIP)**. Both marks need to be visible, and legal documents must name the dorm as the party the tenant deals with.

### Shared view data — `AppServiceProvider::boot()`
`View::composer('*', …)` gives **every** Blade view (pages, emails and dompdf PDFs) these variables. It is loaded once per request; if the database is unreachable it falls back to NEST.PH so no page breaks.

| Variable | Value |
|---|---|
| `brandDormName` | `dormitory_profile.dorm_name`, or `NEST.PH` if empty |
| `brandLogoUrl` | Public URL of the dorm logo, or `null` if none uploaded |
| `brandLogoOnLightUrl` | `brandLogoUrl`, or `images/nestphgreen.png`. Use on light backgrounds. |
| `brandLogoFile` | Absolute file path of the dorm logo for dompdf (which can't load URLs), or `null` |
| `brandFaviconUrl` | Same as `brandLogoOnLightUrl`; used by every page's `<link rel="icon">` |

**Which NEST.PH logo file to use:** `images/nestph.png` is **white** (for green bars and dark backgrounds); `images/nestphgreen.png` is **green** (for light backgrounds). The white one on a light surface is invisible.

### Where each brand appears
| Area | Dorm (main identity) | NEST.PH |
|---|---|---|
| Admin and tenant sidebars (`partials/sidebar-brand`, `partials/powered-by`) | Top: dorm logo and full name. Long names wrap; never cut with "…". | Bottom, under Log Out: "Powered by NEST.PH", a `mailto:` link to `config('mail.from.address')` (stands in for the Settings > About page the app doesn't have). |
| Public top bar (`partials/public-nav`) | Dorm logo and name (wraps on long names) | — |
| Tenant and admin login | Dorm logo and name above the form | "Powered by NEST.PH" under the form |
| Browser tab | Title `"<Page> · <Dorm>"` on all 38 pages; icon is the dorm logo | Icon falls back to the green NEST.PH logo. `public/favicon.ico` is now a copy of `nestph.png` (was 0 bytes). |
| Landing page footer | Dorm logo and name, "© 2026 <Dorm>. All rights reserved." | "· Powered by NEST.PH Dormitory Management System". The "About NEST.PH" section stays as platform marketing. |
| Emails (all 9; `emails/partials/header`, `emails/partials/footer`) | Header: dorm name (and logo if uploaded). Subject lines use the dorm name, e.g. "Your <Dorm> Password Reset Code". | Footer: "Sent by <Dorm> via NEST.PH Dormitory Management System" |
| PDFs (lease contract, eviction notice, demand letter) | Header: dorm name (and logo if uploaded). Lease parties: "executed by and between <Dorm>" (was the hand-typed "NEST.PH Pureza Station Dormitory"). The eviction notice names the dorm as the lease party and creditor. | Fixed footer: "Issued by <Dorm> · Generated via NEST.PH Dormitory Management System" |
| Report exports (v38: Excel and PDF) | Dorm name as the title; footer "Issued by <Dorm> · Generated via NEST.PH Dormitory Management System" | In the footer |
| Other text | Inquiry DPA consent: "I consent to <Dorm> collecting … (processed through the NEST.PH platform)". Tenant blacklist message: "your <Dorm> tenant account". Announcement/comment fallback author: "<Dorm> Admin" (was "NEST PH Admin"). Top bars on the Activity Log, Account Setup and Moved Out pages. | — |

**Left as NEST.PH on purpose:** the landing page's "About NEST.PH" section, downloaded PDF file names (`NEST-PH-Lease-Contract-Preview.pdf`, `NEST-PH-Dormitory-Contract.pdf`), the `nestph:create-owner` command text, and the "NEST PH" SMS handling in `TextbeeService`.

### Open items, flagged for BAGUI
- **Lease contract clause 8.2 (data privacy consent)** still doesn't say that NEST.PH processes data on the dorm's behalf. The legal wording needs someone's review; it was deliberately not written by the developer.
- **`MAIL_FROM_NAME` in `.env`** sets the email "From" name. If it says NEST.PH, consider changing it to the dorm name.

---

## Reviews & Ratings — Tables 41, 42 (built pre-v32, moderation added v32)
*Not recorded in this contract before v32, despite being built. Documented here in full for the first time.*

### Schema
**`reviews`** (`2026_09_10_150536_create_reviews_table.php`): `tenant_id` (FK `tenants`, **unique**, cascade delete: one review per stay), `rating` (tinyint 1 to 5), `comment` (text, nullable), `is_approved` (bool, default `true`), timestamps.

**v32 additions** (`2026_09_24_130732_add_moderation_columns_to_reviews_table.php`):
- `status` enum `published` / `hidden` / `removed`, default `published`, indexed. Existing rows with `is_approved = false` were migrated to `hidden`.
- `flag_reasons` (json, nullable): human-readable reasons from the filter, kept as history even after an admin publishes.
- `moderated_by` (nullable FK `users`, null-on-delete), `moderated_at` (timestamp), `moderation_note` (string 500, the optional Remove reason).

| Status | Label shown to admin | Public? | How it gets there |
|---|---|---|---|
| `published` | Public | Yes | Default on submit; admin Publish/Restore |
| `hidden` | Hidden | No | Filter auto-flag, or admin Hide |
| `removed` | Removed | No | Admin Remove (the admin's "delete") |

**`is_approved` is now derived, never set by hand.** A `saving` hook on the `Review` model sets `is_approved = (status === 'published')` on every save. Every public query (`Review::aggregate()`, `Review::breakdown()`, the `/dorm-info` list) still filters on `is_approved = true`, so none of them had to change. Do not edit `is_approved` directly in phpMyAdmin; change `status` instead. The model also declares `$attributes = ['status' => 'published']` so a new review is public before the hook runs.

### Model — `App\Models\Review`
- `tenant()`, `moderator()` (belongsTo `User` via `moderated_by`).
- `status_label` accessor: Public / Hidden / Removed.
- `moderationSummary()`: the one-line history under each review on the admin card, e.g. "Auto-flagged: Contains a phone number." / "Hidden by Admin on ..." / "Removed by Admin on Sep 24, 2026. Reason: spam" / "Published by Admin on ...".
- `aggregate()` (average + count of public reviews) and `breakdown()` (per-star count and percent), both unchanged.

### Tenant side — submit a review (Table 42)
- **POST `/reviews`** (`reviews.store`), `ReviewController::store()`, in the tenant route group. Body `{rating: 1-5 required, comment: string max 1000 nullable}`. Returns `404` if no tenant record, `403` unless `tenants.status = 'inactive'` and not blacklisted (evictions can't review), `409` if a review already exists.
- **v32:** right after saving, runs `ReviewModerationService::autoModerate()`. If the review is hidden, the response message becomes "Thank you for your review! It will appear publicly once an administrator has checked it." instead of "Thank you for your review!".
- **Eligibility:** there is no separate Record Move-Out flow (old Table 16, excluded in v29 and removed from the manuscript in v3), so "Moved Out" means `status = 'inactive'` from Deactivate Account (Table 37), excluding blacklisted tenants.
- **Scope simplification, flagged for BAGUI:** the Figma "Review Overlay" shows 5 category ratings plus a recommend field; Tables 41/42 only call for one overall 1 to 5 rating and an optional comment, so that's all that's stored.

### Middleware — `RestrictMovedOutTenant` (alias `moveout.check`)
Applied to the tenant route group alongside `movein.check`/`delinquency.check`. An inactive, non-blacklisted tenant is redirected to `tenant.moveout` from everywhere except `tenant.moveout` and `reviews.store`. Blacklisted tenants are left to `delinquency.check`. **v35:** such a tenant can now actually log in to reach this page (see "Auth"); before, deactivation blocked their login entirely.

### GET `/moved-out` (`tenant.moveout`), `TenantMoveOutController::show()`
The only page a moved-out tenant can reach. Passes the tenant, their latest terminated contract, computed length of stay, and dorm contact email/number. An automatic SMS prompt fires on move-out, sent outside the DB transaction.

**v32 rebuild** (`tenantmoveout.blade.php`, now on `tenant.css`): a "How was your stay?" section with five large stars; tapping one opens the form with that rating preselected; rating word (Poor to Excellent); labelled comment box with a 0 / 1000 counter; "Submitting..." state; closes on outside tap or Escape; background scroll locked and focus trapped while open; bottom-sheet form on phones with 16px text (no iOS zoom) and full-width buttons; filled stars and "Thanks for your review!" after submitting, also after reload; tappable email/phone links. Testing tip: any logged-in tenant can open `/moved-out` to view it, but only a real moved-out tenant can submit.

### Public side (Table 41)
- Homepage (`PublicController::home()`): `averageRating` + `reviewCount` from `Review::aggregate()`.
- `/dorm-info` (`dormInfoPage()`): aggregate, per-star breakdown bar, and the 20 latest public reviews (first name + last initial, month/year, stars, comment), with an empty state when there are none.

### Automatic filter — `App\Services\ReviewModerationService` (v32)
- `scan(?string $text): array` returns reasons (empty = clean). Rating-only reviews are never flagged.
- **Blocked words** from `config/review_moderation.php` (English, Filipino/Tagalog, Bisaya). Whole-word matching ("class" does not match "ass"); normalizes leetspeak and stretched letters (`g4g0`, `sh!t`, `gaaaago`); multi-word entries like "tang ina" also catch "tangina" and "tang-ina". No automatic suffix matching; list every variant wanted.
- **Links** (`http://`, `www.`, `something.com`; `.ph` deliberately ignored so "NEST.PH" isn't flagged), **email addresses**, **phone numbers** (10+ digits), **spam patterns** (same character 6+ times in a row; one word repeated through most of the comment).
- `autoModerate(Review)`: hides a flagged review and stores `flag_reasons`; republishes a previously auto-hidden review that now passes. **An admin's decision always wins:** any review with `moderated_by` set is skipped, including on Re-scan.
- After editing the word list: `php artisan config:clear`, then Re-scan Reviews.

### Admin moderation — `ReviewModerationController` (v32), `['auth','admin']` session routes
| Method | URL | Action |
|---|---|---|
| PATCH | `/dormitory-profile/reviews/{review}/publish` | Make public. Also used by the Restore button. |
| PATCH | `/dormitory-profile/reviews/{review}/hide` | Hide from the public page. |
| PATCH | `/dormitory-profile/reviews/{review}/remove` | Remove. Optional `note` (string max 500) saved as `moderation_note`. |
| POST | `/dormitory-profile/reviews/rescan` (`dormitory-profile.reviews.rescan`) | Re-run the filter on every review no admin has decided on. Returns `{message, checked, hidden}`. |

Publish/Hide/Remove set `status`, `moderated_by` (current admin), `moderated_at`, `moderation_note`, and return `{message, review: {id, status, summary}, aggregate: {average, count}}` so the card updates its row, counts and public average without a reload.

### Admin UI — Review Moderation card on `/dormitory-profile`
Card `#reviewModerationCard`, above Legitimacy Documents, fed by `reviews`, `reviewCounts` (`all`/`published`/`hidden`/`removed`) and `reviewAverage` from `DormitoryProfileController::page()`. Summary line (public rating from N public reviews), tabs All / Public / Hidden / Removed with counts, search by tenant name or comment, star filter. Buttons shown per status: Public → Hide, Remove; Hidden → Publish, Remove; Removed → Restore (visibility driven by `.rv-row[data-status]` CSS). Re-scan shows a message then reloads. Review text is only inserted via Blade `{{ }}` or JS `textContent`, never `innerHTML`.

---

## Applicant & Tenant Name Format — First Name / Last Name (built v26)
*Touches the public Apply for Occupancy form, Tenant Manager's Add/Edit modals, `ApplicationController`, `TenantController`, and the `Application`/`Tenant` models.*

Requested directly by the team's research adviser: applicant and tenant names must be captured as separate First Name / Last Name fields, not one free-text "Full Name" input.

### Schema
- `applications.first_name` / `applications.last_name` (each `varchar(100)`, nullable) — replace the old `applications.full_name` (`varchar(150)`), which was dropped.
- `tenants.first_name` / `tenants.last_name` (each `varchar(100)`, nullable) — replace the old `tenants.full_name` (`varchar(150)`), which was dropped.
- Migrated in three steps rather than one destructive change: add the new columns → backfill existing rows via a one-off tinker script (splits on the last space; not reliable for multi-word Filipino given/middle names, so backfilled rows were spot-checked manually rather than trusted blindly) → drop the old column only once the backfill was confirmed.
- **Deliberately not changed:** `users.name` stays a single column — the `users` table only ever needs one display name for login, and adding `first_name`/`last_name` there would have meant touching `RegisteredUserController`, Breeze's own auth scaffolding, and every place a `User`'s name is displayed, for no functional benefit. `inquiries.full_name` also stays a single real column — the Contact/Inquiry form was out of scope for this pass.

### Models
`Application` and `Tenant` both gained:
```php
protected $appends = ['full_name'];

public function getFullNameAttribute(): string
{
    return trim("{$this->first_name} {$this->last_name}");
}
```
This is a computed attribute, not a database column — `$appends` makes it show up in JSON responses the same way the old real column did. This was the deliberate design choice that kept the blast radius small: every existing consumer of `->full_name` (demand letter / eviction notice PDFs, all outbound emails, admin JS reading `t.full_name` / `application.full_name`, the `inquiry`/`tenant` eager-load labels elsewhere in the app) needed **zero changes**, because the attribute still resolves the same way it always did. Only the input forms and their validation/creation code needed to change.

### Public Apply form (`publicapply.blade.php`)
Step 1's single "Full Name" input replaced with separate "First Name" / "Last Name" inputs. Submission JS (`submitApplication()`) now appends `first_name`/`last_name` instead of `full_name`; the Step 4 verify-summary (`buildVerifySummary()`) combines both into the existing `sum_full_name` display span, so the on-screen summary still reads as one name even though it's collected as two fields.

### `ApplicationController`
- **`store()`** — validation swapped `full_name` (`required|string|max:150`) for `first_name`/`last_name` (each `required|string|max:100`); the `Application::create()` call writes both new keys.
- **`createTenantWithLogin()`** — the `Tenant::create()` call now copies `first_name`/`last_name` from the application instead of `full_name`. The `User::create()` call just above it is unchanged — it still writes a single combined `name`.
- **`index()`** — the `tenant` eager-load changed from `'tenant:id,full_name'` to `'tenant:id,first_name,last_name'`, since `full_name` is no longer a real column to `SELECT`. The accessor rebuilds `full_name` once the tenant relation is loaded, so the response shape is unchanged.

### `TenantController`
- **`page()`** — the tenant list's `orderBy('full_name')` changed to `orderBy('last_name')->orderBy('first_name')`, for the same reason as `index()` above: sorting directly on a computed accessor isn't possible at the SQL level.
- **`show()`** — now returns `first_name`/`last_name` directly, in addition to the existing computed `full_name`, so the Edit Tenant modal can pre-fill two separate inputs.
- **`store()`** (Table 15, Add New Tenant) / **`update()`** (Table 14, Edit) — validation swapped the same way as `ApplicationController::store()`. Both methods' `User::create()`/`update()` calls combine `first_name`+`last_name` into a single `name` string (`trim($data['first_name'].' '.$data['last_name'])`), since `users` has no `first_name`/`last_name` columns of its own.

### `tenantmanager.blade.php`
Add New Tenant and Edit Tenant modals both split their single "Full Name" field into "First Name"/"Last Name" inputs, with matching updates to each modal's submission `FormData` and (for Edit) its pre-fill logic reading the new `first_name`/`last_name` keys off `GET /tenant-manager/{tenant}`.

### Real bugs caught while wiring this through
Both found by tracing every existing reference to the old column rather than assuming the rename was complete once the obvious spots were done:
1. **`TenantController::page()`'s `orderBy('full_name')`** would have thrown a SQL "Unknown column" error the first time anyone loaded the Tenant Manager list after the migration, since `full_name` no longer exists as a real column to sort on.
2. **`ApplicationController::index()`'s eager-load `'tenant:id,full_name'`** would have thrown the same class of error the first time the admin Applications list loaded an application with a linked tenant.

### Flagged for BAGUI
Tables 13, 14, and 15 in the manuscript still describe this as a single "full name" field. This is a deliberate deviation driven directly by the research adviser's feedback, not a silent fix — worth reflecting in the manuscript's next revision.

---

## Dorm Documents & Rental Policy (v41)
*All admin routes are `['auth','admin']` session routes on `DormitoryProfileController` and render as cards on `/dormitory-profile`. Migration `2026_10_01_000001_align_with_dormitory_documents`. Values for Pureza Station: `php artisan db:seed --class=PurezaStationSeeder`.*

The client's signed documents are the source of truth for business rules. Where they disagree with earlier manuscript or Figma choices, the documents win. Billing, late penalties, minimum stay and the documents applicants sign all read the settings below. Changing a setting changes both what the system does and what **new** applicants sign. Existing contracts keep the monthly rate their tenant signed for.

### Schema
- **`room_types`** (new): `name`, `location`, `description`, `pricing_mode` (`per_bed` | `per_room`), `monthly_rate`, `min_capacity`, `max_capacity`, `has_aircon`, `sort_order`.
- **`rooms.room_type_id`** (new, nullable FK, set null on delete).
- **`dormitory_charges`** (new): the fee schedule's "Other Charges": `name`, `amount` (null = no fixed amount), `amount_note` (for example "Reasonable repair or replacement cost"), `when_applies`, `sort_order`.
- **`dormitory_house_rules.section`** (new, nullable).
- **`dormitory_profile`** gains:
  - Lessor details: `representative_name`, `representative_position`, `facebook_page_name`, `facebook_url`, `website_url`.
  - Rental policy: `rent_due_day` (1), `grace_period_days` (3), `late_penalty_percent` (10), `minimum_stay_months` (3), `move_out_notice_days` (14), `extension_notice_days` (30), `deposit_refund_days` (21), `reservation_validity_days` (30), `mid_month_move_in` (`full` | `prorated`), `water_included`, `electricity_included`, `wifi_included`, `short_term_rate`, `transient_rate`.
  - Document versions: `rules_version`, `fees_version`, `documents_effective_date`.
- **`applications`**: `emergency_contact_signed`, `emergency_billing_consent`. **`tenants`**: `emergency_billing_reminders`. (All boolean, default false.)
- **`penalties.type`** changed from an enum to a string(30) so new types such as `late_payment` need no migration.

### POST `/dormitory-profile/policy` — Rental Policy card
Saves every field listed above under Lessor details, Rental policy and Document versions. Required: `rent_due_day` (1–28), `grace_period_days` (0–31), `late_penalty_percent` (0–100), `minimum_stay_months` (1–24), the three notice/refund day counts (0–180), `reservation_validity_days` (1–180), `mid_month_move_in`, and the three `*_included` booleans. URLs must be valid URLs. Response: `{"message": "Rental policy saved. New applicants will sign documents with these terms."}`.

### Room Types & Rates card
- **POST `/dormitory-profile/room-types`** → `201` `{ message, room_type }`. Fields: `name` (required), `location`, `description`, `pricing_mode` (required), `monthly_rate` (required, ≥ 0), `min_capacity`/`max_capacity` (1–50, max ≥ min), `has_aircon`.
- **PATCH `/dormitory-profile/room-types/{roomType}`**: same fields. Every room of this type is re-priced right away (`syncFromRoomType()`).
- **DELETE `/dormitory-profile/room-types/{roomType}`**: `409` while any room still uses the type.

### Other Charges card
- **POST `/dormitory-profile/charges`** → `201` `{ message, charge }`; **PATCH / DELETE `/dormitory-profile/charges/{charge}`**. Fields: `name` (required), `amount` (nullable), `amount_note`, `when_applies`.
- The `/payments` page receives these as `charges` and offers them as presets in **Add Penalty**, because the fee schedule allows no unlisted charges.

### How billing follows the policy
- **Calendar months.** A monthly bill covers the 1st to the last day of the month and is due on `rent_due_day`. A period cut short by the contract's end date is charged by the day.
- **The first monthly bill is for the month after move-in**, because the move-in fee's advance rent covers the move-in month (Agreement 4.3). If `mid_month_move_in` is `prorated`, the unused days of the move-in month are credited on that first bill. Walk-in contracts with no move-in bill are billed from their start date.
- **Utilities and Wi-Fi** are left off when the policy marks them as included.
- **Overdue only after the grace period.** `syncOverdueStatuses()` marks a bill overdue once `due_date + grace_period_days` has passed (due Oct 1, grace until Oct 4, overdue from Oct 5). The escalation ladder counts days from the end of the grace period too (`EscalationService::daysOverdue()`), so no SMS goes out during the grace period.
- **Automatic late penalty** (`BillingStatement::applyLatePenalties()`, run with the overdue sync): once per monthly bill, a `late_payment` penalty of `late_penalty_percent` × the rent still unpaid at the grace deadline. Utilities and Wi-Fi are never included. It is never compounded and never re-added after a waive. The tenant gets a `late_penalty` notification. It waits while a pending proof dated within the grace period is under review.
- **On time = the payment date on the proof**, not the approval date. When an admin approves or records a payment that was made on time, `waiveLatePenaltyIfPaidOnTime()` waives a penalty that was only added because the review came late.
- New helpers on `BillingStatement`: `graceDeadline()`, `isPastGrace()`, `paidOnTime()`, `overdueRent()`, `recalculateTotals()`.

### Emergency contact SMS (Agreement 9.3–9.4)
Stage 4 of the escalation ladder texts the emergency contact **only if** `tenants.emergency_billing_reminders` is true. Without consent, the stage is logged as done-but-skipped so the ladder can still move on. The message states only the amount due, the due date and any penalty, with no tenant name, and tells the contact how to opt out.

### Still open, for BAGUI
- Stage 6 blacklisting comes about 11 days after the grace period, while the Agreement only allows termination after 3 months unpaid.
- The returning-tenant discount was kept, although the documents say nothing about discounts.
- "Utilities included" and "prorated mid-month move-in" are unticked in the client's Schedule (the demo data assumes included and charged in full).
- The authorized representative's name is missing from the documents.
- The Privacy Notice and Check-out Procedures documents haven't been provided.

---

## Onboarding: Applications
*Public submission, no auth. Admin management requires `['auth','admin']` session middleware.*

### POST `/api/applications`
Full personal/contact/emergency-contact/school/room-preference fields. **v41:** `birthdate` is required and the applicant must be at least 18 (Agreement 14.1). If `tenant_end_date` is sent, it can't be earlier than the minimum stay allows (`422` with the earliest allowed date). Also `id_document`/`signed_contract` file uploads, `contract_acceptance` (server-enforced `required`+`accepted`), `dpa_consent`. **`first_name`/`last_name` required separately since v26** (previously one `full_name` field — see "Applicant & Tenant Name Format" above).

**`signed_contract_path` accepted since v27**, as an alternative to the `signed_contract` file upload — see "Tenancy Documents E-Sign" below. **v41:** the path is accepted only if `contract-sign` wrote it and its signing record exists. If the applicant changed any printed detail after signing, the submission is refused with `422` "You changed some details after signing...". `emergency_contact_signed` and `emergency_billing_consent` are copied from the signing record, not from the form. Submission is rejected with a `422` if neither a valid stored path nor an uploaded file is present; the applicant-facing UI no longer exposes the raw file-upload field at all, though the server still honors it if sent directly.

- **Step 7.3 — bed reservation on submission:** a successful submission tags the bed `reserved`, the real mechanism preventing double-selection.
- **Step 7.5 — acknowledgment email** on submission, non-blocking on failure.

### PATCH `/api/applications/{application}/approve`
- Bed stays `reserved` through approval, only becomes `occupied` once the move-in fee is actually paid.
- `discount_amount` is genuinely subtracted from the per-bed rate before storage — `LeaseContract.monthly_rate` is the post-discount figure.
- A move-in fee `BillingStatement` is created (`type: 'move_in'`, 2× per-bed rate: one month's advance plus the deposit).
- **v41:** with no end date, the lease ends on the last day of the minimum stay (`DormitoryProfile::minimumEndDate()`). The emergency contact's signature and billing consent are copied to the lease and to `tenants.emergency_billing_reminders`. A returning tenant's emergency contact and consent are replaced by the new ones.
- A brand-new tenant's account starts at `status: 'pending_move_in_payment'`; a returning tenant keeps their existing status.
- The resulting `Tenant` record receives `date_of_birth`/`home_address`/`tenant_type`/`id_document_path`/`signed_contract_path` from the application — previously silently dropped.
- If the applicant already uploaded a signed e-signature document, the resulting `LeaseContract` is created `esign_status: 'signed'`, `status: 'active'` immediately, instead of always `pending` regardless of what was already submitted.

### PATCH `/api/applications/{application}/reject`
`rejection_reason` required, persisted, emailed. Bed released back to `vacant`.

### POST `/applications/{application}/request-reapplication` *(session, admin)*
Third outcome distinct from rejection (`{"note": "..."}`). Same bed-release. `re_application_requested` status, `re_application_note` column. *(v34 correction: previously documented as `PATCH /api/applications/{application}/request-reapplication`; no `/api` version exists. Approve and reject are registered both ways — `PATCH /api/applications/{application}/approve|reject` (sanctum) and `POST /applications/{application}/approve|reject` (session, used by the admin page).)*

### GET `/applications` (`applications.index`) *(page render)*
Review Applications page: filters, detail drawer, the three decision actions. *(v34 correction: previously documented as `/admin/applications`.)*

### Application list — resolved re-application requests hidden (v33)
`ApplicationController::index()` excludes any application that has `status = 're_application_requested'` **and** has a newer application (later `created_at`) with the same email, matched case-insensitively:

```php
$query->where(function ($q) {
    $q->where('status', '!=', 're_application_requested')
        ->orWhereNotExists(function ($sub) {
            $sub->selectRaw('1')
                ->from('applications as newer')
                ->whereRaw('LOWER(newer.email) = LOWER(applications.email)')
                ->whereColumn('newer.created_at', '>', 'applications.created_at');
        });
});
```

- **Nothing is deleted or modified**; the old row keeps `re_application_requested` and `re_application_note`.
- Hidden from **All** as well as the Re-application Requested tab (open point below).
- Only a *newer* same-email application counts as "resolved."

---

## Tenancy Documents E-Sign — built v27, rebuilt v41
*Public, no auth. Part of the Apply for Occupancy flow, before an `Application` row exists. `Api\ApplicationController` + `App\Services\TenancyDocuments`.*

Since v41 the applicant reads and signs the dorm's **three real documents**, instead of the single generated lease contract from v27 (`pdfs/lease-contract.blade.php` was deleted). The templates in `resources/views/documents/` (`agreement`, `rules`, `fees`) are word-for-word copies of `PSD_Contract.docx`, `PSD_Rules & Regulations.docx` and `PSD_Payments and Fees.docx` in the repo root. They use the original letterhead (`resources/documents/letterhead`) and the Arimo font. Only the blanks are filled in, so **changing the wording means editing those Blade files**. `TenancyDocuments::DOCUMENTS` = `agreement` (Dormitory Tenant Agreement), `rules` (Dormitory Rules and Regulations), `fees` (Payments and Fees Schedule).

### POST `/api/applications/contract-preview`
Takes the applicant's details so far (`first_name`, `last_name`, `home_address`, `contact_number`, `email`, the three emergency contact fields, `bed_id`, `preferred_start_date`, `tenant_end_date`; all nullable except the name) and an optional `document` (`agreement`/`rules`/`fees`). Streams that one document unsigned, or all three when `document` is left out (the "Download as PDF" button). Nothing is saved. The Apply page shows the PDF pages with PDF.js.

### POST `/api/applications/contract-sign`
Same fields, plus:
- `signature_image`: the applicant's signature, a `data:image/png;base64,...` string (max about 600KB).
- `emergency_signature_image`: the emergency contact's signature, from a second pad. Required.
- `emergency_billing_consent`: boolean. Required. This is the emergency contact's separate yes/no to billing reminders (Agreement 9.3).
- `acknowledged[]`: must list all three document keys ("I have read" checkboxes).

The applicant's signature goes on all three documents and the emergency contact's on the Agreement. The PDF is saved to `application-documents/signed-contracts/{uuid}.pdf`, next to a `{uuid}.json` **signing record** (`signed_at`, a SHA-256 fingerprint of the printed details, and the consent). Returns `{ signed_contract_path, preview_url, signed_at }`. `422` if a signature is missing or unreadable, the emergency contact's name is blank, or not all three documents are acknowledged.

### Signature capture
Plain `<canvas>` pads with mouse and touch handlers, no library. The drawn area is cropped to its bounding box before sending, so a small signature in a corner still fills the signature line. Pads are cleared each time the step is reopened.

### Runtime requirement: PHP GD extension
dompdf needs GD to embed the signature images. Without it signing fails with "The PHP GD extension is required". Enable `extension=gd` in `php.ini` and restart the server (check with `php -m`).

### Step gating on the Apply form
The applicant can't move past the Review & Sign step until `contract-sign` has returned a `signed_contract_path`.

### Admin preview — GET `/dormitory-profile/documents/{document}` (`dormitory-profile.documents`) *(admin)*
`document` is `agreement`, `rules` or `fees`. Streams a blank copy filled with the dorm's current settings, so the admin can check it.

---

## Onboarding: Inquiries
*Public submission, no auth. Admin management requires `['auth','admin']` session middleware.*

### POST `/inquiries` — blocks blacklisted contacts (Table 27 step 4)
`InquiryController::store()` checks the submitted `contact_number`/`email` against every `tenants.is_blacklisted = true` row before creating the inquiry. Since this form is public and unauthenticated, there's no session-based tenant to check — it's a soft match against contact info instead (matches on phone **or** email, since a blacklisted tenant could otherwise dodge the check by leaving one field blank). Returns `403` with a plain refusal message on a match; no `errors` object, since this isn't a validation failure. Doesn't touch the existing DPA-consent / contact-info-required validation, which still runs first.

### POST `/inquiries/{inquiry}/reply` *(admin)*
`{"reply_message": "..."}` — saves, moves status to `contacted`, emails the visitor. New columns `reply_message`/`replied_at`/`replied_by`.

### GET `/inquiries` (`inquiries.index`) *(page render)*
*(v34 correction: previously documented as `/admin/inquiries`.)*
List + reply drawer; filterable by `room_id` when a visitor clicks "Inquiry" from a room's public listing. **v36:** each inquiry also carries `replied_by_tag` (Owner/Admin), shown after "Replied … by {name}."

---

## Lease Contracts
*Routes require `['auth','admin']` session middleware.*

Status enum: `pending`, `active`, `expiring_soon`, `expired`, `terminated`.

- **POST `/lease-contracts`** — manually create a lease independent of the application flow. `409` + `requires_confirmation: true` if the tenant already has an active lease elsewhere.
- **GET `/lease-contracts/tenants/search?q=`** — shared tenant-search endpoint, reused across Tenant Manager's Add modal and three other admin forms.
- **GET `/tenants/{tenant}/active-lease`** — a tenant's active room/bed, powers auto-fill on the Record Damage form.
- **PATCH `.../renew`** — new end date must be a real future date later than the current one. **PATCH `.../terminate`** — `reason` required, persisted, shown in the detail view.
- **GET `/lease-contracts`** *(page render)* — status auto-sync on every load (`active` → `expiring_soon` within 30 days of `end_date`; → `expired` past it). **Not a real scheduled job** — only runs when someone views the page.

---

## Billing
*Routes require `['auth','admin']` session middleware (older sanctum-guarded equivalents also exist under `/api/billing/...`).*

`billing_statements.type`: `'move_in'` or `'monthly'`.

- **`billing_statements(tenant_id, status)` composite index** — confirmed missing via a live DB export in v9, since added.
- **Bug fix:** `withBalance()` now only sums `status: 'approved'` payments.
- `BillingStatement::syncOverdueStatuses()` flips unpaid statements to `overdue` on page load once the grace period has passed (v41), and then adds any automatic late penalties. See "Dorm Documents & Rental Policy".
- **GET `/payments`** (`payments.index`) *(page render, "Billing Overview" tab; v34 correction, previously documented as `/admin/payments`)* — payments table and drawer show `payment_method_label` when present (e.g. "Maya"), falling back to the enum label. — every statement, computed balance, detail drawer. **Known gap, not yet fixed:** this drawer does not show the tenant's ID/signed-contract documents, even though the admin is reviewing a move-in fee payment for the same tenant — flagged as a possible future addition (surface Tenant Manager's document links here too) but not built this round.
- **Proof of payment viewer (v38, frontend only)** — in the Pending Payment drawer, an image `proof_url` renders as a button that opens a full-screen viewer (`#imgViewer`, bottom of `adminbilling.blade.php`, exposed as `window.openImageViewer(src, name)`). Zoom 50% to 500% by buttons, wheel, pinch or `+`/`-`/`0`; drag to pan when zoomed; Download fetches the image as a blob and saves it as `proof-of-payment-<tenant-name>.<ext>`. PDF proofs still open in a new tab. No backend change.
- **POST `/billing/generate`** — the "+ Generate This Month's Billing" button. Safe to click repeatedly. Also logs `billing.statement_generated` and `billing.utility_data_missing` stub events for traceability. **v34:** utilities and Wi-Fi now come from the tenant's **room** (`Room::utilityShares()`, per bed) instead of the floor; see "Room Wi-Fi & Utilities Pricing."
- **`routes/console.php` scheduled task**: does nothing unless a scheduler runs. Locally there's no cron, and on Laravel Cloud the Scheduler is deliberately **off** (see "Deployment").
- **POST `/api/billing/{billingStatement}/attach-penalties`** *(sanctum only; v34 correction, previously written without the `/api` prefix)* — **no admin UI button calls it.**
- **POST `/billing/{billingStatement}/payments/cash`** / **GET `/billing/tenants/{tenant}/statements`** — Record Cash Payment modal flow ("+ Record Payment Entry"). Saves `payment_method: 'cash'`, `status: 'approved'`, `recorded_by`/`reviewed_by`; rejects amounts above the balance (`422`) and future dates. See "Cash payments and delinquency" under Payment Methods for the full admin workflow.

---

## Penalties & Damages
*Newer session-auth routes require `['auth','admin']`, registered in `web.php`. Older sanctum-guarded equivalents (`update`/`destroy`/`reinstate` for both) exist under `/api/...`. **The session-auth admin UI only wires up `index`/`store`/`waive` for penalties and `store` for damages — `update`, `destroy`, and `reinstate` exist and work but have no button anywhere in `adminbilling.blade.php`.***

Fulfills Tables 47, 48, 49.

### Schema
- **`penalties`**: `tenant_id`, `damage_id` (nullable), `billing_id` (nullable), `type` (`damage`/`manual`/`other`/`late_payment`; a plain string since v41), `description`, `amount`, `date_incurred` (nullable), `status` (`active`/`waived`), `created_by`.
- **`damages`**: `tenant_id`, `room_id`/`bed_id` (nullable), `description`, `cost`, `date_incurred`, `photo_path` (nullable), `created_by`.
- **`penalty_audit_logs`**: `penalty_id`, `action`, `performed_by`, `reason` (nullable), `created_at`.

`date_incurred` on `penalties` — code-complete since v8; a live DB export in v9 caught the migration hadn't actually been run. **Confirm current status with `php artisan migrate:status` before relying on it** — this document cannot verify a live database's actual migration state.

Every penalty response computes a unified, non-stored `date` field:
```php
$penalty->date = $penalty->type === 'damage'
    ? $penalty->damage?->date_incurred
    : ($penalty->date_incurred ?? $penalty->created_at?->toDateString());
```

### Key endpoints
- **GET `/penalties`** — `?tenant_id=` / `?status=` filters, computed `room_no` + `damage_photo_url`.
- **POST `/penalties`** — `type` (`manual`/`other`) and `date_incurred` optional (default `manual`/today), can't be future. `late_payment` penalties are only created automatically.
- **PATCH `/penalties/{penalty}/waive`** — `reason` required, `active → waived` (not a delete), triggers statement resync if already billed.
- **`reinstate`/`update`/`destroy`** — exist, `/api/...` only, no session mirror, no admin UI.
- **POST `/damages`** — creates a `Damage` + linked `Penalty` (`type: 'damage'`) atomically. `date_incurred` required.
- **`Penalty::computeRunningTotal($tenantId)`** = `$unpaidBillingBalance + $unbilledActivePenaltyTotal`.

### Admin UI — `adminbilling.blade.php`, "Penalties" tab
Third tab. "+ Record Damage" / "+ Add Penalty" modals; every active row gets a Waive button (confirm modal, required reason).

---

## Tenant Portal — Billing & Penalties
*Session routes (`['auth','tenant','movein.check','delinquency.check']`, prefix `my/billing`) mirror sanctum routes under `/api/my/...`.*

- **GET `/my/billing/summary`** — tenant profile, active contract, `accountSummary()` (`outstanding_balance`, `unbilled_penalties`, `total_owed`, `overdue_count`, `pending_review_count`).
- **GET `/my/billing/bills`** — same summary + statement list.
- **Bug fix:** tenant's own Balance Due card previously never read `data.summary`, only `data.bills` — an unbilled penalty was invisible until the tenant's next generated statement folded it in. **Fixed**, including the edge case of an active penalty with no currently-payable bill at all.
- **GET `.../bills/{billingStatement}`** / **POST `.../payment-proof`** — statement detail with penalty line items; `reference_number` is **optional** since v41 (the fee schedule doesn't require one). **v34:** proof submission takes **`payment_method_id`** (nullable, `exists:payment_methods,id`); `payment_method` is now `required_without:payment_method_id`. When an ID is sent, the stored `payment_method` comes from `PaymentMethod::paymentEnum()` and `payment_method_label` from its name; a cash method is rejected with `422` "Cash payments are recorded by the admin at the office, not submitted online." Responses include `payment_method_label`.
- **GET `/my/billing/penalties`** — every penalty on the tenant's own account (active + waived).
- **Rejected proof reason (v35, Table 20).** `payments.review_notes` was already in the bill payload but never shown. `tenantbilling.blade.php` now marks an unpaid bill whose newest proof was rejected with "Proof rejected", shows "Your last proof of payment was not accepted." with the reason on the proof screen, and adds a Reason line to rejected rows in the Payment Proofs modal. All of it disappears once a newer proof exists. Frontend only.
- **GET `/api/my/payments`** / **`/api/my/payments/{payment}/receipt`** *(sanctum only; v34 correction, previously written without the `/api` prefix)* — no session mirror, not called from any Blade page.

### `GET /billing` page render — three distinct states, resolved by `portal_restricted`/`is_blacklisted`
The closure route (`routes/web.php`) resolves the tenant and the dormitory profile, then passes `tenant`, `portalRestricted`, `isBlacklisted`, `dormContactEmail`, `dormContactNumber`, and (v34) `nextBill` (Next Bill Estimate, or `null`) and `paymentMethods` (every method, `toClientArray()`) into `tenantbilling.blade.php`. Rendering branches, checked in this order:

1. **`isBlacklisted` → full-page takeover.** No sidebar, no billing content at all — a dark full-screen screen matching `/my/delinquency`'s own Stage 6 takeover, but with copy specific to Billing ("Billing is no longer accessible here") and three actions: **View Delinquency Status** (links to `tenant.delinquency`), **Contact Admin** (`mailto:`/`tel:`, hidden if neither is configured), and **Log Out**.
2. **`portalRestricted` (and not blacklisted) → lock-panel sidebar swap:** normal billing content still renders, but the sidebar nav becomes a non-navigable lock panel, hamburger hidden, back-arrow points at `tenant.delinquency` instead of `dashboard`.
3. **Neither → normal page.**

### "Pay Now" button — fixed, two rounds (v20)
Previously failed completely silently when there was no payable statement: the button was outright `disabled` via JS with no visual disabled treatment, so it looked normal but never fired a click event at all. Fixed properly across two passes:
- The button is no longer force-disabled — clicking it with nothing due now shows an actual toast message explaining why (either "no outstanding balance" or, if there's an unbilled penalty not yet attached to a statement, a message explaining it'll appear on the next generated bill).
- The first attempt at this fix introduced two further silent bugs of its own: the page never actually defined a `toast()` function (despite calling it), and the replacement logic referenced a variable scoped inside a different function. Both errors threw silently in the browser console — invisible without actually opening dev tools. Fully fixed: a real `#toast` element, CSS, and `toast()` function were added, and the click handler now recomputes its needed value from `billingSummary` (a properly outer-scoped variable) instead of reaching for an inner-scoped one it couldn't see.

---

## Tenant Account Status
`tenants.status`: `pending_move_in_payment`, `active`, `inactive`.

### Middleware: `movein.check`
Redirects a `pending_move_in_payment` tenant to `/move-in` from anywhere else in the tenant portal. **Known gap in its own design:** route-name whitelist is manually maintained, not a wildcard match — a new step added to the move-in flow without updating this list will redirect back to `/move-in` instead of loading.

---

## Pay Move-In Fees Flow — Table 16, plus a "payment under review" page

- **GET `/move-in`** — `welcome()` checks `hasPendingProof()`, redirects to the pending-verification page instead of the normal screen if a proof is already submitted.
- **GET `/move-in/pending`** — "being verified" screen, with a **"Submit Again"** link. **Known limitation:** resubmitting creates a second `pending` `Payment` row rather than voiding the first.
- **GET/POST `/move-in/payment-type`** — Full/Partial choice. Not part of Table 16's literal flow — a deliberate team addition.
- **GET/POST `/move-in/payment-method`** — choice among the admin's **online** payment methods (v34; previously hardcoded GCash/BDO). POST `payment_method` is a `payment_methods.id`.
- **GET `/move-in/payment`** — real balance, the chosen method's account details and uploaded QR (or a "No QR code set up" placeholder), proof-upload form. *(v34: the old client-side QR that encoded the number as plain text is gone.)* Still **not a payment gateway** — the tenant pays in their own app and uploads proof for admin review.
- **Rejected proof reason (v35).** `TenantOnboardingController::latestRejectedProof()` returns the newest proof on the pending move-in bill if it was rejected. `/move-in` and `/move-in/payment` then show "Your last proof of payment was not accepted." with the admin's `review_notes` and a prompt to submit a new proof, using `.rejection-notice` in `partials/movein-styles.blade.php`. Hidden once a newer proof is submitted. `rejectProof()` already required a reason; before v35 it only reached the log.

**Frontend (v33) — no route, controller, or payload changes.**

| Page | View | Notes |
|---|---|---|
| Welcome | `tenantmoveinwelcome.blade.php` | Step 1; "Application Approved" label replaced by the step bar; back arrow is a real link. |
| Payment Type | `tenantmoveinpaymenttype.blade.php` | Full/Partial radio cards in a normal `<form>` posting to `tenant.movein.payment-type.store` (JS-built hidden form removed). Continue disabled until picked, spinner on submit. |
| Payment Method | `tenantmoveinpaymentmethod.blade.php` | Radio cards built from the admin's online methods (v34), drawn phone/bank icon in the brand color, "account name · number" underneath. Proceed disabled until picked; earlier choice pre-selected. |
| Payment | `tenantmoveinpayment.blade.php` | Panels "Proof of payment" / "Payment details"; balance `₱9,500.00` style with full/partial label; file row only after a file is picked; field-level validation messages with scroll + focus; first field-specific server error shown; success message takes focus and links to the pending page. Upload endpoint, fields and notes-folding unchanged; v34 replaced the QR panel with the admin's QR/placeholder and sends `payment_method_id`. |
| Pending | `tenantmoveinpending.blade.php` | All four steps shown done; "Submit Again" uses the shared secondary button. |

All five use `partials/movein-styles.blade.php`, `partials/movein-steps.blade.php`, and `@include('partials.public-nav', ['tenantSession' => true])`.
- **`activateTenantIfMoveInSettled()`** — occupies the bed + activates the tenant once a move-in statement reaches `paid`, idempotently, from either an approved proof or a recorded cash payment. **This is currently the only real trigger for "move-in confirmed" anywhere in the app** — see the open architectural question immediately below.

> **Resolved architectural question (v29):** Old Table 16 (Record Occupancy Transaction, removed from the manuscript in v3) described confirming move-in as its own deliberate admin action, distinct from payment verification. What's built does both automatically the instant a payment is approved, and Tenant Manager's own walk-in registration sidesteps the question entirely by starting those tenants at `status: 'active'` directly. A separate Record Move-In/Move-Out action matching old Table 16's literal description was actually built and tested, then deliberately rolled back — the team's conclusion was that a third activation path added no real capability, since every fact it would track already exists on `lease_contracts`/`tenants`, and it would rarely even be reachable given the two paths already below. **This is now the settled design, not an open question.** Full reasoning in Document History, v29.

---

## SMS Integration — textbee.dev

### `app/Services/TextbeeService.php`
`send(string $recipient, string $message): bool` — normalizes PH local number formats to E.164, logs every attempt, never throws.

**Config:** `config/services.php` → `services.textbee.{api_key,device_id,base_url}`, from `.env`.

**Known carrier-filter bug, found and fixed:** any SMS body containing the literal string `NEST.PH` (with the period) was silently rejected by the PH telco's anti-smishing filter. **Fix:** `TextbeeService::BRAND_NAME` constant (`'NEST PH'`, no period). `sanitizeMessage()` runs automatically inside `send()` as a defense-in-depth safety net too.

**Testing note:** `EscalationService::processAll()` / `php artisan escalation:process` call `TextbeeService::send()` directly for Stages 2–4, with no dry-run flag — even a failed send still hits the textbee API and counts against the monthly limit, and it processes **every** currently-overdue tenant in the database, not just a test account. **Never run that command directly for testing.** Two safer alternatives now exist, both covered in "Testing Tools" below: the new admin-page panel (`DelinquencyTestingController`), which scopes each click to a single tenant's single bill via `processBillingStatement()` — real SMS still goes out, but only for that one tenant, and only for the stage(s) the click actually crosses; or seeding `escalation_logs`/`tenants` flags directly via Tinker, which guarantees zero API calls since `TextbeeService::send()` is never invoked at all. Stage 5's `stage5DemandLetter()` is the one exception worth knowing: it contains no SMS call at all, so it's safe to invoke for real (via reflection, since it's `private`) when a genuine PDF is needed for testing.

**Operational note for deployment:** textbee.dev routes through a real, physical Android phone, not a cloud API — it must stay powered on, connected, and running the Textbee app continuously for production SMS to go out. This is an ongoing operational responsibility, not a one-time setup step — worth the team deciding explicitly whose phone it is and what the fallback plan is if it goes offline.

---

## Delinquency Escalation — Admin Side — full ladder built and confirmed working end-to-end, Stages 1–6

### The stages, per the actual use cases
| Stage | Table | Trigger | Status |
|---|---|---|---|
| 1 — Automated Account Flagging | 23 | Due date passes | ✅ Built |
| 2 — Graduated SMS Notifications | 24 | Day 2/4/7 overdue *(shifted from Table 23's literal Day 1/3/7 — see below)* | ✅ Built, real SMS |
| 3 — Portal Restriction | 25 | Day 8 (see below) | ✅ Built, real SMS, enforced |
| 4 — Emergency Contact Notification | 26 | Day 9 | ✅ Built, real SMS |
| 5 — Auto-Generated Demand Letter | 27 | Day 10 | ✅ Confirmed working (v16) |
| 6 — Flag Delinquent and Blacklist | 28 | Day 11, AND Stages 1–5 verified complete | ✅ Built, confirmed firing correctly once Stage 5 completes |

### `app/Services/EscalationService.php` — the state machine
Entry point `processAll()` queries every `status = 'overdue'` billing statement, computes days overdue, advances each tenant through every stage they now qualify for. Safe to run repeatedly without double-sending/double-logging.

**`processBillingStatement(BillingStatement $bill)`** *(new, v24)* — a scoped sibling to `processAll()`, added for the new admin Testing Tools panel. Runs the exact same sync-then-`advance()` logic as `processAll()`, but against one specific billing statement instead of every overdue bill in the system, so a demo click never touches any tenant besides the one selected. Not called anywhere in production code paths — only from `DelinquencyTestingController`.

**`STAGE_2_DAYS` shifted from `[1, 3, 7]` to `[2, 4, 7]` (v24) — a deliberate deviation from Table 23's literal "Day 1, Day 3, Day 7," not an unspecified gap being filled in.** Root cause: Stage 1 (Table 22) activates the instant a bill is first detected overdue, and "Day 1 overdue" is that exact same calendar day — there's no "Day 0" of being overdue in this design. That meant Stage 1's log entry and Stage 2's first SMS reminder always fired together, in the same `advance()` call, for every real tenant in production, not just in testing. Table 23's own precondition ("Stage 1 active") is technically satisfied either way, but Table 22 step 8 ("if payment is received before Stage 2 triggers...") implies the manuscript's authors were picturing some gap between the two. Rather than silently pick an interpretation, this was raised as a team decision: shift the first reminder to Day 2, keep the spacing reasonably even (2→4→7) while deliberately keeping Day 7 as the anchor for the final "URGENT, full week overdue" reminder. **The manuscript (Table 23) has been updated to say "Day 2, Day 4, Day 7" to match** — this is not a code-vs-manuscript mismatch to flag for BAGUI, it's a joint decision reflected in both documents. `max(STAGE_2_DAYS)` is still `7`, so Stage 3 onward (thresholds below) are completely unaffected by this change.

**Day thresholds for Stages 3–6 are not specified in the manuscript.** Team placeholder: `DAYS_PER_STAGE = 1` (fast for testing), cumulative from Stage 2's day 7 — Stage 3 = day 8, Stage 4 = day 9, Stage 5 = day 10, Stage 6 = day 11.

**`escalation:process` is wired to the scheduler** (`routes/console.php`, `Schedule::command('escalation:process')->daily()`), closing a gap flagged in every version of this document since v11. **This is inert on the current local Windows/XAMPP dev setup** (no real cron running `schedule:run`), so nothing changes locally without either `php artisan schedule:work` or a real deployment cron. **But the moment a real cron does exist (i.e., at actual deployment), `DAYS_PER_STAGE = 1` stops being a safe testing placeholder and becomes real production behavior** — a tenant who misses one payment could be fully SMS'd, portal-restricted, and blacklisted within about a week. **This is the single highest-priority open item before any real deployment** — a genuine policy decision is needed here before the production cron is ever turned on.

**Correctness bugs found and fixed across versions:**
1. **SMS retry safety** (v11) — a failed send updates the existing `pending` row on the next run rather than being silently treated as done.
2. **Stage 6 completeness gate** (v11) — `canProceedToStage6()` checks every prior stage genuinely completed, not just that enough days passed.
3. **Reset-override false "Stage 1" display** (v13/v14) — `override()`'s Reset deleted a tenant's history then mislabeled its own audit entry via a bad fallback. Fixed by capturing the tenant's real previous stage before the wipe, and excluding `admin_override_*` entries from "current stage" calculations.
4. **Stage 5 variable mismatch crash** (v14, confirmed fixed v15) — the controller was passing an outdated variable set into a template that had been redesigned to expect a different one. Now correctly aligned.
5. **Blacklisted-tenant redirect target** (v18) — `RestrictDelinquentTenant`'s default redirect for anything not on its allow-list was hardcoded to `tenant.billing`. Fixed — redirects to `tenant.delinquency` instead.
6. **`Payment::created_at` not casting to Carbon** (v24) — `Payment` has `$timestamps = false` (no `updated_at` column exists), which also silently disables Laravel's automatic `created_at`-to-Carbon casting. `DelinquencyController::transformTenant()`'s "Last Payment" column called `->format()` on what turned out to be a plain string, throwing a `500` the moment any tenant on the Delinquency page had a real payment on file. Fixed by adding an explicit `'created_at' => 'datetime'` to `Payment::$casts` — the same fix already applied to `TicketReply`, `PenaltyAuditLog`, and `AdminAccessLog` for the identical reason.

### Stage 5 demand letter — built, redesigned per Figma, confirmed genuinely working
Real PDF generation, rebuilt from the team's approved Figma frame (`node-id=238:1945`) — a multi-month balance breakdown table, highlighted deadline/blacklist-date box, the tenant's full escalation history as an appendix, and (a deliberate addition beyond Table 26's literal text, flagged for BAGUI) a barangay/Katarungang Pambarangay Law paragraph. Pulls the real dorm name from `DormitoryProfile::current()->dorm_name`. No known open issues.

**Also worth knowing:** the letter's stated "Blacklisting Date" is illustrative (7 days + 1 from issuance), not literally tied to the engine's real `DAYS_PER_STAGE` timing — the two are independent calculations and can drift apart if `DAYS_PER_STAGE` changes.

### Payment-received exception handling
`resolveSettledEscalations()` — any bill reaching `status: paid` gets its open logs marked resolved and `portal_restricted` lifted. Deliberately does **not** touch Stage 6 (Table 27 treats blacklisting as permanent) — and does not clear `portal_restricted` on a blacklisted tenant either, since it's never reached for a Stage 6 account. A blacklisted tenant is therefore always both `is_blacklisted = true` AND `portal_restricted = true` simultaneously; the middleware checks blacklist status specifically rather than assuming it's the only flag in play.

**Now called immediately on settlement, not just on the next scheduler run (v25).** Previously this method only ran inside `EscalationService::processAll()` — meaning it only ever executed when `escalation:process` itself ran (manual or scheduled). Nothing that actually settles a bill (`Api\PaymentController::approveProof()`, `Api\PaymentController::recordCash()`) ever called it directly. Practical effect: a tenant who paid off their overdue balance in full would vanish from the admin's Delinquent Accounts list on the very next page load (that list is always computed live from `escalation_logs`/`billing_statements`, nothing cached) — but if they'd been Stage 3+ before paying, `portal_restricted` stayed `true` and they'd still hit the tenant-side lock panel until the next `escalation:process` execution, which on the team's local XAMPP setup (no `schedule:work` running) could be never. Fixed: both `approveProof()` and `recordCash()` now call `app(EscalationService::class)->resolveSettledEscalations()` immediately after `resyncStatementStatus()`, guarded behind `if ($statement->status === 'paid')` so it's a no-op for a partial payment that didn't fully settle the bill. Safe to call unconditionally either way since the method itself only ever touches bills already at `status: 'paid'` with unresolved logs — the guard is purely to skip a redundant query on partial payments, not a correctness requirement.

### Table 50 — Issue Eviction Notice: built and confirmed working
Not Stage 6 itself — a discretionary admin action gated on `tenant.is_blacklisted`, matching the corrected use case framing (confirmed via Figure 26: Table 27 is the automatic blacklist; Table 50 is a separate human judgment call).

- **POST `/delinquency/{tenant}/eviction-notice`** — `{"reason": "required", "notice_date": "required, date"}`. Returns `409` if not yet blacklisted (server-enforced). Generates a PDF, sends an SMS via textbee.dev. Per Table 50's own exception path, the PDF and log are created regardless of SMS success — only `status` differs.
- **GET `/delinquency/{tenant}/demand-letter`** / **GET `/delinquency/{tenant}/eviction-notice`** — *(admin-only, gated `['auth','admin']`)* stream the most recent respective PDF via `Storage::disk('public')->download()`. `404` with a friendly message if none exists yet. A separate, tenant-scoped equivalent for the demand letter also exists — see Tenant Side below.

`escalation_logs.action_type` distinguishes the two Stage-6-numbered events without new schema: `delinquent_blacklisted` (automatic) vs. `eviction_notice_issued` (admin-triggered).

### Delinquency Admin UI — `GET /delinquency`, `DelinquencyController`
Built from a Figma prototype (`node-id=454-3564`) that needed four corrections against Tables 22–28 before being implemented as-is: real stage names, removal of a legally-confused page-level demand-letter banner, "Delinquent Tenants" renamed to "Tenants In Escalation," and dropped month-over-month trend deltas.

- **`page()`** — live `stats`, `stageBreakdown`, `accounts` list. Nothing cached.
- **`history()`** — the stage-detail modal's data: full timeline plus `demand_letter_ready`/`eviction_notice_issued` booleans. **v36:** each timeline entry also carries `performed_by_tag` (Owner/Admin), shown after "by {name}."
- **`override()`** — Table 28. Pause/Unpause, Reset, Clear — each writes its own audit `escalation_logs` row, captured with the tenant's genuine prior stage.

**Stage colors match the approved Figma palette**, shared with the tenant side, each with a paired `text` key (dark green on the lighter early stages, white on the darker later ones) so stage badges/pills stay legible against their solid-color backgrounds:

| Stage | Accent | Text |
|---|---|---|
| 1 — Account Flagged | `#ffec60` (yellow) | `#004f0f` |
| 2 — SMS Reminders | `#f87542` (orange) | `#004f0f` |
| 3 — Portal Restricted | `#fe424b` (red) | `#004f0f` |
| 4 — Emergency Contact | `#a24346` (maroon) | `#ffffff` |
| 5 — Demand Letter | `#645d5d` (grey) | `#ffffff` |
| 6 — Blacklisted | `#000000` (black) | `#ffffff` |

**These two `STAGES` consts (admin `DelinquencyController` and tenant `TenantDelinquencyController`) are separate arrays kept in sync by hand, not a shared source.** If a stage name or color ever changes, both need updating.

**Known simplification, still open:** `transformTenant()`'s balance sums `total_amount` on overdue bills directly, without subtracting partial payments already made against an `overdue`-status bill. `TenantDelinquencyController`'s own balance calculation mirrors this same simplification deliberately, for consistency between what the admin and the tenant each see.

### Schema (Week 6, cumulative)
- `tenants.portal_restricted`, `tenants.is_blacklisted`, `tenants.escalation_paused` (booleans).
- `escalation_logs` composite index `(billing_id, action_type)`.
- `billing_statements` composite index `(status, due_date)`.
- `Tenant::escalationLogs()` / `BillingStatement::escalationLogs()` relations.
- `app/Console/Commands/ProcessEscalations.php` — `php artisan escalation:process`. Wired to the scheduler — see above.

---

## Delinquency Escalation — Tenant Side

### Middleware: `RestrictDelinquentTenant` (alias `delinquency.check`)
`app/Http/Middleware/RestrictDelinquentTenant.php`. Fulfills Table 24's postcondition literally: *"Tenant portal access is restricted to the payment link only."*

- Applied to the full tenant route group (`['auth','tenant','movein.check','delinquency.check']`) **and**, separately, to the shared `/dashboard` and `/profile` routes that live outside that group in `routes/web.php`.
- If `tenant.portal_restricted` is true, every request is redirected **except**: `tenant.billing`, `tenant.delinquency`, `tenant.billing.payment-proof`, anything under `my/billing*` or `my/delinquency*` (matched by path), and `logout`.
- Checks `tenant.is_blacklisted` first and redirects there to `tenant.delinquency` instead of `tenant.billing`, since `/billing` itself now dead-ends in a takeover for a blacklisted tenant.
- No-ops entirely for anyone who isn't a portal-restricted tenant (admins included), so it's safe to have applied it to shared routes.
- **Known gap, same shape as `movein.check`'s:** the allowed-route list is manually maintained, not a wildcard match. A new tenant-facing route added later needs an explicit add here or it'll bounce a restricted tenant back to Billing/Delinquency.

### `TenantDelinquencyController` — `GET /my/delinquency` (`tenant.delinquency`)
`app/Http/Controllers/TenantDelinquencyController.php`, view `resources/views/tenantdelinquency.blade.php`. Shows the logged-in tenant their **own** escalation timeline and balance only — resolved from the session, never a route parameter, same pattern as the rest of the tenant portal.

- **No escalation activity at all** (no logs, not blacklisted) → an aesthetic "You're in Good Standing" placeholder card instead of an empty timeline.
- **In escalation** → outstanding-balance card (overdue bills' `total_amount` summed, matching the admin side's own known simplification), a billing breakdown listing each overdue period + late penalties, and an Escalation Timeline.
- **Timeline is grouped by STAGE, not by individual log row** — Stage 2 alone can produce up to three log rows (Day 2/4/7 SMS reminders, v24), these collapse into one "SMS Reminders Sent" row with a date range, matching the approved Figma design rather than three near-identical rows.
- **Status pills read ACTIVE/COMPLETE based on the tenant's current (highest-reached) stage, not each row's own internal `resolved`/`sent` DB status.**
- Admin override entries (`admin_override_*` action types, Table 28) are excluded from the tenant's timeline entirely, same as they're excluded from the admin's own "current stage" calculation.

### Per-stage tenant UI, each built against its own approved Figma frame
- **Stage 1 (Account Flagged)** — plain timeline row.
- **Stage 2 (SMS Reminders)** — plain timeline row with a date range.
- **Stage 3 (Portal Restricted)** — the tenant's **entire sidebar swaps to a non-navigable lock panel** (large lock icon, "PORTAL ACCESS RESTRICTED" headline, explanation text, Log Out only) on both `/my/delinquency` and `/billing` — the only two pages a restricted-but-not-blacklisted tenant can actually reach. Hamburger toggle hidden. Pay Now / Contact Admin buttons live in the balance card itself, visible for the whole restricted period.
- **Stage 4 (Emergency Contact Notified)** — expanded timeline row shows the tenant's real registered emergency contact (name, initials avatar, number) and the **actual SMS text** pulled from that stage's `escalation_logs.message_content`.
- **Stage 5 (Demand Letter)** — expanded row with a real **Download PDF** button once `demand_letter_generated` status is `sent`; hides the button (shows a "still being prepared" note instead) if not yet ready.
- **Stage 6 (Blacklisted)** — **full-page takeover on `/my/delinquency`**, replacing the entire tenant shell (no sidebar at all, not even the lock panel). Shows the tenant's first name, the "DELINQUENT" headline, all six stage dots in their real accent colors, a "What Happens Next?" explanation box, and Contact Admin / Log Out only.

### `/billing`'s own Stage 6 takeover
`resources/views/tenantbilling.blade.php` mirrors the same three-branch structure — see "Tenant Portal — Billing & Penalties" above.

### GET `/my/delinquency/demand-letter` (`tenant.delinquency.demand-letter`)
Tenant's own copy of Table 26 step 8 — same underlying PDF the admin can already download, but scoped strictly to the authenticated tenant's own most recent `sent` `demand_letter_generated` log. Never accepts a tenant ID from the request. `404` with a friendly message if none exists yet.

### Sidebar wiring
The "Delinquency" nav item points at `route('tenant.delinquency')` across `tenantdashboard.blade.php`, `tenantbilling.blade.php`, and `tenantaccount.blade.php`.

---

## Generate Reports — Table 36 (built v28)
*Routes require `['auth','admin']` session middleware, registered directly in `web.php` alongside Tenant Manager/Tickets/Delinquency — no `/api/...` mirror exists for this module.*

Was the single largest fully-missing use case in the manuscript prior to this version — confirmed by directly searching the live repo for a `ReportController`, a `/reports` route, and an `adminreports.blade.php` view, all of which turned up nothing, rather than assumed from the Development Timeline's own "Not Started" markers alone. Built as its own standalone page at `/reports`, not embedded as a tab inside Billing or Vacancy Monitoring — matches Table 36's own Flow of Events, which opens with "1. Navigate to Reports" as a distinct destination, not a sub-view of another page.

### Schema
**v40 adds one table, `monthly_expenses`** (`App\Models\MonthlyExpense`), the dorm's own costs for one month:
- `month` (date, **unique**, always the 1st of the month), `electricity`, `water`, `internet`, `salaries`, `other` (all `decimal(10,2)`, default 0), `other_notes` (string 255, nullable), `recorded_by` (nullable FK `users`, null-on-delete), timestamps.
- `MonthlyExpense::total()` adds the five amounts.

Everything else is still computed live on each Generate click from existing data (`beds`, `floors`, `lease_contracts`, `tenants`, `payments`, `penalties`, `billing_statements`); nothing is cached.

### Endpoints
- **GET `/reports`** (`reports.index`) — page shell only. Both report types load their actual numbers via the two JSON endpoints below once the admin picks a type and clicks Generate — same on-demand-data-on-click pattern as every other admin page in this app (Tenant Manager, Tickets, Delinquency all work the same way).
- **GET `/reports/occupancy`** *(JSON)* — Table 36's [Occupancy Report] branch. Bed counts by the app's real 4 statuses (`vacant`/`reserved`/`occupied`/`maintenance` — see `VacancyController::BED_STATUSES`), overall occupancy rate, and the same breakdown per floor. The counts are the CURRENT live state; the date-range control has no effect on this tab.
  - **v40: `trend`** — 12 rows, oldest first, one per month: `label` ("Nov 2025"), `total_beds`, `occupied`, `occupancy_rate` (%), `moved_in`, `moved_out`. The current month is measured at today; earlier months at their last day. Built by `occupancyTrend()` from lease contracts, since beds keep no status history: a contract fills its bed from `start_date` until `terminated_at`, or the tenant's `deactivated_at` if the contract has none. Tenants still `pending_move_in_payment` are left out. `total_beds` counts beds whose `created_at` is on or before that date.
- **GET `/reports/financial`** *(JSON)* — Table 36's [Financial/Billing Report] branch. `?start=&end=` (`YYYY-MM-DD`), defaults to "this month so far" when omitted — same default window as the Admin Dashboard's own "Revenue (this month)" card. Returns total collected (split into cash vs. online — GCash/bank transfer/other), total outstanding (computed per-statement as `total_amount` minus approved payments, summed across every unpaid/partial/overdue statement due within the range), total penalties applied (by `date_incurred`), a distinct delinquent-tenant count (overdue statements due within the range), and a payment count.
  - **v40 additions:** `total_expenses`, `net_profit` (`total_collected − total_expenses`), `expense_breakdown` (`electricity`, `water`, `internet`, `salaries`, `other`), `monthly` (one row per month in the range, at most 36: `label`, `collected`, `expenses`, `net`, `has_expenses`), and `months_missing_expenses` (labels of months in the range with nothing recorded).
  - **Expenses count by whole month.** Any month the range touches counts in full, e.g. Sep 15–Oct 10 includes all of September's and October's expenses. "Collected" means approved payments by `payment_date`, so it includes rent, utilities, move-in fees and penalties.
- **GET `/reports/export`** — `?type=occupancy|financial&start=&end=&format=xlsx|pdf`. **v38:** `format` defaults to `xlsx`; `csv` is no longer accepted (`422`). Returns a download named `<type>-report-<Y-m-d_His>.xlsx` or `.pdf`. The server does not know what is on screen; the Reports page sends the same `type`/`start`/`end` it used for Generate.
- **GET `/reports/expenses`** *(JSON, v40)* — `?month=YYYY-MM` (defaults to the current month; any other format is `422`). Returns `month`, `label` ("September 2026"), `collected` (approved payments dated in that month), `expense` (that month's saved row, or `null` if nothing is saved), and `history` (the 12 most recent saved months, newest first, each with the five amounts, `other_notes`, `total`, `collected` and `net`).
- **POST `/reports/expenses`** *(JSON, v40)* — body `month` (`YYYY-MM`, required), `electricity`, `water`, `internet`, `salaries`, `other` (all required, numeric, 0 to 9,999,999), `other_notes` (optional, max 255). Creates or **overwrites** that month's row and sets `recorded_by` to the current admin. A future month returns `422` with "You can only record expenses for this month or earlier." Returns `message` ("Expenses for September 2026 saved.") and the saved `expense`.

### Known deviation, flagged for BAGUI
Table 36's Occupancy Report flow describes bed-level sub-statuses the schema doesn't have ("reserved visitor" / "reserved applicant" / "approved / pending move-in payment" are all just `reserved` here). The bed counts are therefore a **live, current-moment snapshot**, and the date-range control has no effect on the Occupancy tab; only the Financial tab filters by date. This is the same simplification already made on the Vacancy Monitoring Dashboard itself (Table 29).

**v40:** the "historical trend if applicable" part is now built (the `trend` array above), rebuilt from move-in and move-out dates rather than logged bed statuses. It does not know about beds that were under maintenance in the past, or beds deleted since; it always covers the last 12 months, regardless of the date range.

**Revenue and profit (v40) are not in the manuscript.** They were requested by the team leader: the admin enters the building's monthly bills and staff salaries, and the report subtracts them from payments collected. Expenses are entered by hand once a month; nothing reads them from Meralco or any other source.

### Excel output (v38, replaces the CSV output)
**v40 tables:** Occupancy adds "Occupancy Trend (last 12 months)". Financial adds "Revenue and Profit" (collected, less expenses, net profit in red when negative), "Expenses Breakdown", and "Monthly Breakdown" (collected, expenses, net per month).

**v40 charts:** native Excel charts, built by `xlsxChart()` and written with `setIncludeCharts(true)`. They read from the tables' cells, so they update if the numbers are edited in Excel. Occupancy has a line chart of the occupancy rate and a bar chart of move-ins and move-outs; Financial has a bar chart of collected vs. expenses with a net-profit line, placed after a page break so it prints whole. Each series name must reference a real cell (the column header above the values): an empty reference makes Excel "repair" the file and delete every chart. `setLineWidth()` takes points, not EMU. Checked by opening the files in Microsoft Excel.

Built by `ReportController::exportXlsx()` / `xlsxTable()` with `phpoffice/phpspreadsheet`. One sheet: the dorm name as a green title, the report name, "Date Generated" and "Period Covered" (or "Coverage" for Occupancy), then titled tables with a dark-green header row, striped rows and a light-green total row. Money cells use the `"₱"#,##0.00` format and percentages `0.0%`, so they stay real numbers. Outstanding and penalties are red. Gridlines hidden, fit to one page wide, printed footer "<Dorm> - <Report>" and "Page X of Y", document title "<Dorm> - <Report>". Colours match the PDF (`194E19` green, `BA2828` red). The old CSV notes (UTF-8 BOM, `number_format()` strings) no longer apply.

### PDF output (v38)
**v40:** Occupancy adds an "Occupancy Trend (Last 12 Months)" section: a rate line chart, a moved-in/moved-out bar chart and the table. Financial adds a "Revenue and Profit" table with the expense breakdown and net profit, a note listing months with no expenses recorded, and a "Monthly Breakdown" with a collected-vs-expenses chart and table. dompdf can't run Chart.js, so `ReportController::svgChart()` draws each chart as an SVG `data:` image (`pdfCharts()` passes them to the view as `$charts`).

`resources/views/pdfs/report.blade.php`, rendered by dompdf. Same look as `pdfs/demand-letter.blade.php`: dorm logo (`brandLogoFile`) and name header, green section headings, a shaded box with the two key figures (Occupancy: rate and available bedspaces; Financial: total collected and total outstanding), grey summary tables, a floor table for Occupancy, "Prepared by, <Dorm> Management", and the dual-branding footer. Amounts are written "PHP 1,234.00" because DejaVu Sans does not reliably render ₱. "Page X of Y" is stamped after rendering with `Canvas::page_text()`, since dompdf does not support CSS `counter(pages)`.

### Admin UI — `adminreports.blade.php`
**v40:** three tabs. **Occupancy** adds the 12-month trend chart (Chart.js 4.4.1 from jsDelivr, the same build as the Admin Dashboard) and table. **Financial** opens with Revenue (Collected), Total Expenses and Net Profit (red when negative), a missing-expenses warning with a "Record expenses" button, a collected-vs-expenses chart and a monthly table. Months with nothing recorded show "—" for profit and no chart point. The Collections section no longer repeats Total Collected. **Expenses & Profit** (new) has a month picker, the five amounts and a notes field, a live Collected − Expenses = Net Profit summary, a saved / "Nothing saved for <month> yet" state, and a clickable Profit by Month table. If Chart.js fails to load, each chart is replaced by a note and the tables still show the numbers. On phones the tables scroll inside their own boxes, and inputs and buttons are at least 44px tall.

New standalone page. Two-tab toggle (Occupancy / Financial), a From/To date-range control, Generate, Export Excel and Export PDF (v38; was Export CSV). Both export buttons stay disabled until a report is generated, export the parameters stored at Generate time, show "Exporting..." while the file builds, and show an inline error (`#exportError`) on failure. Has its own sidebar entry ("Reports," positioned between Lease Management and Admin Privileges) — **not yet added to any of the OTHER admin pages' sidebars**, so the page is reachable by direct navigation but the nav link itself currently only appears on the Reports page.

---

## Announcements — Tables 43–46 (built v30)
*Viewing/commenting routes require `['auth','movein.check','moveout.check','delinquency.check']` session middleware — the same stack as `/dashboard` itself, so a delinquent-restricted or pending-move-in tenant is blocked from the feed exactly like every other portal feature. Posting/moderation routes require `['auth','admin']`. Both groups registered directly in `web.php`; no `/api/...` mirror exists for this module.*

The newsfeed module — the only use case in the manuscript with no code behind it at all prior to this version (no migration, model, controller, or route touched it). Deliberately built as a feed embedded on the existing `/dashboard` route rather than a new standalone page, matching Table 45's own trigger wording ("administrator clicks Post Announcement **on the homepage**") — see Document History v30 item 4 for the full list of deliberate deviations from the manuscript's literal text.

### Schema
Two new tables, following the same nullable-`user_id`/`tenant_id` pattern `ticket_replies` already established — exactly one of the two is set per row, which resolves both the author and the comment-bubble styling without a second lookup:
- **`announcements`** — `user_id` (nullable FK `users`, null-on-delete — the poster; kept even if that admin account is later removed), `body` (text, required), `comments_restricted` (boolean, default `false`), timestamps.
- **`announcement_comments`** — `announcement_id` (FK `announcements`, cascade delete), `user_id` (nullable FK `users`, null-on-delete — set when an admin comments), `tenant_id` (nullable FK `tenants`, null-on-delete — set when a tenant comments), `body` (text), `created_at` only (`$timestamps = false`, same pattern as `ticket_replies`/`admin_access_logs`/`AdminAccessLog`).

### Models
`Announcement::poster()` / `comments()` (the latter ordered oldest-first for thread display); computed `poster_name`/`poster_initials` accessors (`Announcement::initialsFromName()` is a static helper shared with the comment model, capped at two initials from the first two words of a name). `AnnouncementComment::user()`/`tenant()`; computed `author_name`/`author_initials`/`is_admin` accessors resolve directly off whichever of `user_id`/`tenant_id` is set, with a `'(no name on record)'` fallback matching the existing `Application`/`Tenant` `full_name` accessor convention.

### Endpoints
- **GET `/announcements`** (`announcements.index`) — the 20 most recent announcements, each with a comment **count** only (`withCount('comments')`) — not the comments themselves, keeping the initial dashboard payload light. Powers both dashboards' feed on page load. **v36:** each item also carries `poster_tag` (`owner`/`admin`); comments (from the comments endpoints below) carry `author_tag` (`owner`/`admin`, `null` for tenants). The feed renders these with the shared role tag, replacing the old hard-coded "Admin" tag on every post and "(Admin)" on admin comments.
- **POST `/announcements`** (`announcements.store`) — admin/owner only. `body` required. Table 45's validation exception (empty body → `422`) is the standard Laravel validation response; no custom message needed beyond the default.
- **GET `/announcements/{announcement}/comments`** (`announcements.comments.index`) — Table 43 step 2, the expand-thread action. Full comment list for one announcement plus its current `comments_restricted` state, fetched on demand rather than bundled into the feed response above.
- **POST `/announcements/{announcement}/comments`** (`announcements.comments.store`) — Table 44. `body` required (`422` if empty). Open to **both** tenants and admins (see Document History v30 item 4(b)); when `comments_restricted` is `true`, only a non-admin caller gets blocked (`403`) — an admin can always reply on their own post regardless of the flag.
- **DELETE `/announcements/comments/{comment}`** (`announcements.comments.destroy`) — Table 46, delete branch. Admin/owner only. Returns the announcement's updated comment count so the caller can refresh the count without a second request.
- **PATCH `/announcements/{announcement}/restrict`** (`announcements.restrict`) — Table 46, restrict/re-enable toggle. Admin/owner only. Flips `comments_restricted` and returns the new state plus a confirmation message matching Table 46 step 3.2/4.1's wording.

### Frontend — `partials/announcements-feed.blade.php`
One self-contained Blade partial (its own scoped `.announce-*` CSS and its own IIFE-wrapped `<script>`, per the team's "new JS always in its own IIFE" convention) included once each in `admindashboard.blade.php` and `tenantdashboard.blade.php`. Admin/tenant branching is read directly from `auth()->user()->role` inside the partial (`$isAnnounceAdmin`) rather than passed in from either dashboard's own controller method, so the same one file works unmodified on both pages — deliberately not built against either page's own `.dash-card`/`.panel` classes (an early version was, and rendered unstyled on whichever page didn't define that exact class — see Document History v30 item 2's sibling frontend bugs, all found and fixed the same session). Composer + moderation controls (Restrict/Unrestrict, Delete) render only when `$isAnnounceAdmin` is true; the comment composer itself renders for everyone unless `comments_restricted` is on for a non-admin viewer, in which case it's replaced with a plain "Comments are restricted on this announcement" note (Table 44's exception path).

### Known deviations from the manuscript, flagged for BAGUI
See Document History v30 item 4 for the full list (no separate Manage Announcements page; commenting open to admins too; avatar rendered as initials, not a photo). None of these are silent — all three are genuine scope decisions made to keep the feature "smooth and efficient" per the team's own framing, not simplifications discovered after the fact.

---

## Mobile & Shared Frontend Layer (v32)
Frontend only; no routes or business logic. Where to edit what:

| Concern | File | Notes |
|---|---|---|
| Admin shell + shared phone layouts | `public/css/admin.css` | "Mobile (860px and below)" section at the end, plus 640px rules: stat cards two per row, swipeable tab rows with action buttons above them, full-width search/filters/buttons, tables scroll inside their panel, modal form rows stack. `.badge` positioning rule is now scoped to `.topbar-icon .badge`. |
| Tenant shell, colors, phone layout | `public/css/tenant.css` | `--sage-900` `#1f4630`, `--sage-800` `#27573a`, `--sage-700` `#2f6a46`, `--sage-600` `#3a7a52` (main green), `--sage-100` `#e5f1e2`, `--sage-50` `#f2f7ef`, `--cream` `#fbf8f0`. Old `--green-dark`/`--green-btn` alias to these. **Rule: no new hex greens in tenant pages.** Status colors unchanged on purpose. Payment-method brand colors (GCash, Maya, BDO, BPI) come from `PaymentMethod::brand()` (v34), with the app green for anything else. |
| Sidebar drawer (admin + tenant) | `public/js/sidebar-drawer.js` | At 860px and below the sidebar slides in over a dimmed backdrop; closes on outside tap, Escape, or link pick. Captures the hamburger click before page scripts. Desktop collapse and its `localStorage` memory still work. Loaded by one line at the end of `partials/admin-sidebar`, `tenant-sidebar`, `tenant-sidebar-restricted`. |
| Public nav | `resources/views/partials/public-nav.blade.php` | Included on 9 public pages via `@include('partials.public-nav')`, plus the 5 move-in pages (v33). Phones: one slim row (logo, Apply, Menu); Menu closes on re-tap, outside tap, Escape. Current page marked on all sizes. **v33:** optional `['tenantSession' => true]` replaces Apply/Log In/Admin with one Log Out button (posts to `logout`). Edit the nav only here. |
| Move-in flow styles (v33) | `resources/views/partials/movein-styles.blade.php` | Shared CSS for all 5 move-in pages: colors, nav, two-column layout, buttons, errors, spinner, step bar, tablet/phone layouts. Same pattern as `publicinquiry`/`logintenant`/`passwords`. Phones: decorative "N" hidden, full-width panel and buttons, 16px inputs. |
| Move-in step bar (v33) | `resources/views/partials/movein-steps.blade.php` | `@include('partials.movein-steps', ['step' => N])`, steps 1–4; pass `5` to mark all complete. |
| Forgot Password (v33) | `resources/views/passwords.blade.php` | Focus underline restored, code boxes shrink on narrow phones, 16px inputs on phones, 24px corners / 20px padding to match other public pages. |
| Overlay layering on phones (v34) | `public/css/admin.css`, `public/css/tenant.css` (identical block at the end of each) | At ≤860px the sticky top bar is `z-index:100`, which had covered page modals (50–61). Now `.modal-overlay`, `.overlay`, `.review-modal-overlay`, `.lightbox` → `300`, `.drawer` → `301` (sidebar drawer is 200); `.modal-box`/`.review-modal` capped at `100dvh − 24px` (100vh fallback) and scroll inside with `overscroll-behavior: contain`; ≤640px `.modal-box` padding 20px 18px. Uses `body .x` selectors so it overrides page-local rules regardless of order. Fixes Add New Ticket and every other page modal/drawer. |
| Stat cards on phones (v34) | `public/css/admin.css` (640px block), `tenantdashboard.blade.php` | Text column `min-width:0`, value 18px with `overflow-wrap:anywhere`, icon 36px; tenant dashboard's 2-column range uses `minmax(0,1fr)`. Long peso amounts no longer spill out of the card. |
| Announcements feed on phones (v34) | `partials/announcements-feed.blade.php` | Tag and Restrict button don't shrink; name/tag wrap; at ≤640px the 46px indent is dropped, composer stacks, toast centers. |
| QR viewer (v34) | `partials/qr-lightbox.blade.php` | See "Payment Methods & QR Codes". z-index 1000. |
| Peso icon (v34) | inline SVG | `<path d="M20 11H4M20 7H4"/><path d="M7 21V4a1 1 0 011-1h4a1 1 0 010 12H7"/>` replaces the dollar path everywhere (admin sidebar Billing link, admin Billing stat, Lease Management, tenant dashboard, tenant Billing). Don't reintroduce `M12 1v22M17 5H9.5…`. |

**Rules kept this round:** new JS in its own IIFE; no reused `const` names across script blocks on one page; user text only via `{{ }}`/`textContent`; `@media` overrides placed after the base rules; shared styles go in the shared files.

---

## Testing Tools

Two ways exist to test the Stage 0–6 ladder without ever calling `TextbeeService::send()` against every overdue tenant in the database — see the SMS Integration section above for why `escalation:process`/`processAll()` are unsafe to use directly for this.

### In-app panel — `DelinquencyTestingController` *(new, v24)*
A "Testing Tools" section on `/delinquency` itself, rendered only inside a Blade `@env('local')` block and additionally hard-blocked server-side (`abort(404)` if `app()->environment('local')` is false) — it cannot be reached at all outside a local environment, by design.

- **`GET /delinquency-testing/tenants`** — every `status: 'active'` tenant, each tagged with their current stage (`0` if never flagged). Powers the panel's dropdown.
- **`POST /delinquency-testing/{tenant}/escalate`** — `{"stage": 0-6}`. `0` wipes that tenant's `escalation_logs` and resets `portal_restricted`/`is_blacklisted`/`escalation_paused` to `false` (equivalent to Table 28's Reset, but callable on any tenant, not just one already in escalation). `1`–`6` sets the tenant's most relevant unpaid/overdue/partial billing statement's `due_date` to the matching days-overdue value, then calls `EscalationService::processBillingStatement()` — real SMS included. `409` if the tenant is already blacklisted (Stage 6 is permanent; reset first). If the tenant has no qualifying bill at all, a fresh one is auto-created from their most recent lease contract (`422` if they have no lease to base it on).
- **Known trade-off, not a bug:** jumping straight to a later stage (e.g. clicking "Stage 2") sets `due_date` far enough back that *every* Stage 2 reminder threshold it passes fires in that one click (Day 2, 4, **and** 7 all at once) — three real SMS in a single button press, not one. For a genuine one-SMS-per-click walkthrough, escalate one day-threshold at a time instead (set `due_date` to each of Day 2, then 4, then 7 in separate clicks/requests) rather than jumping directly to a stage's final threshold.

### Automated test suite (v36)
`php artisan test` runs on in-memory SQLite (`phpunit.xml`) and **now passes: 25 tests, 0 failures** — the first time the whole suite has passed.
- **Why it was failing:** four migrations used MySQL-only `ALTER TABLE … MODIFY` (`2026_08_30_000001`, `2026_08_30_000002`, `2026_09_04_140701`, `2026_09_04_185202`). Each now has a private `modifyColumn($mysqlSql, $table, $change)` helper: on `mysql`/`mariadb` it runs the original SQL unchanged; on anything else it applies the same column change with Laravel's built-in `->change()` (Laravel 11+, no `doctrine/dbal`). Existing MySQL databases are unaffected because those migrations never re-run.
- **Removed:** two Breeze starter tests for features NEST.PH doesn't have — "new users can register" (tenants come in through applications) and "profile page is displayed" (its view no longer exists). The rest of those files still run.
- **Added:** `tests/Feature/CreateOwnerCommandTest.php` — creates an Owner on an empty database (and logs in as them), and declines to create a second Owner.
- **Rollback fixed:** `migrate:reset` failed on three migrations whose composite index began with a foreign-key column; MySQL had dropped its automatic FK index in favour of the composite one, so dropping the composite was refused. The `down()` of `2026_09_08_000002` (escalation_logs `billing_id`), `2026_08_25_000020` (payments `tenant_id`) and `2026_09_02_000001` (billing_statements `tenant_id`) now adds a plain index on that column first. Verified: 75 migrations run, reset and re-run cleanly on a fresh MySQL database.
- **To test against MySQL instead** (Git Bash): `DB_CONNECTION=mysql DB_DATABASE=<empty test db> php artisan test`. Never point this at `nestph_local` — `RefreshDatabase` wipes it.

### Automated test suite (v37)
**31 tests, 0 failures.**
- **Added:** `tests/Unit/PasswordPolicyTest.php` (6 tests): a valid password passes; "too short", "no uppercase", "no number" and "no symbol" are each rejected; 200 generated temporary passwords all pass.
- **Updated for the new policy:** `PasswordResetTest` and `PasswordUpdateTest` now use `New-password1` instead of `password` / `new-password`. `CreateOwnerCommandTest` uses the new prompt text ("Password (8+ characters with an uppercase letter, a number and a symbol; typing is hidden)"), the new error message and `Secret-pass-1`.

### Automated test suite (v41)
**48 tests, 0 failures.** `tests/Feature/DormitoryDocumentsAlignmentTest.php` (17 tests) covers room type pricing, the minimum stay, calendar-month billing and the first bill, prorated move-in, the grace period, the one-time late penalty (only on unpaid rent, waiting for on-time proofs, waived after a late review), emergency contact SMS consent and message, the three-document signing flow and re-sign check, the 18+ and minimum-stay rules, Rules-only public access, admin room type and policy management, and that edited pages still render.

### Debugging tip
To check "did this actually save?", run a direct query in `php artisan tinker` (for example `Hash::check()` against a stored password hash). In the browser, read a page's embedded JSON from the console (`JSON.parse(document.getElementById('tenants-data').textContent)`) to see exactly what data the page received.

---

## Deployment — Laravel Cloud (testing and defense demo)

**Live at `https://nestph-production-un1rbg.laravel.cloud`** (Singapore), deployed 2026-10-02. It's for testing and the thesis defense demo, not daily use by a real dorm.

- **Resources:** Dev MySQL database `nestphdatabase`; public bucket `nestph_files`, attached as the `public` disk (also the default disk).
- **Uploads go through the storage disk (v41).** Code that read files from local paths now reads through `Storage` (VR panorama fill, scene sizing, the PDF logo as a data URI), so it works with the bucket. Package `league/flysystem-aws-s3-v3` was added.
- **`php artisan storage:push-to-cloud [--overwrite]`** (v41) uploads `storage/app/public` to the bucket. Files already in the bucket are skipped unless `--overwrite` is given.
- **Data:** `database.sql` (the Pureza-aligned demo data from `DemoDataSeeder`) was imported. Every dummy account's password is `Password123!`. Dummies with unpaid bills use the team demo SMS number `09212408565`.
- **Scheduler is OFF on purpose.** `DAYS_PER_STAGE = 1` would blacklist the dummies within days and text the demo phone. No queue worker either: nothing in the app is queued.
- **Removed stray files (v41):** `EER.mwb`, `fr.html`, `generate_demand_letter.php`, `seed_test_tenant.php`.
- **First Owner on a fresh install:** `php artisan migrate`, then `php artisan nestph:create-owner`. **Never run `php artisan db:seed` on a real dormitory**, because `RolesAndTestUsersSeeder` creates well-known test logins.

**Before a real dormitory goes live:**
- Real email provider (mail still goes to the Mailtrap sandbox).
- A private bucket for sensitive uploads (IDs, signed documents, payment proofs). Today they're in the public bucket.
- A real `DAYS_PER_STAGE` policy, then turn the Scheduler on.
- Move to a production database and wipe the dummies.
- Rotate `APP_KEY` and the Textbee and Mailtrap secrets (they appeared in screenshots). Turn off the database's public endpoint.
- Upload the dorm's logo, fill in its contact email and number, and set `MAIL_FROM_NAME` to the dorm's name.
- Decide who keeps the textbee.dev gateway phone online.
- The PHP `gd` extension (signatures) and `zip`/`xml` (Excel export) must be enabled.
- Run `composer audit` and `composer update`, then the tests.

---

## Notifications (v39)

### Tenant panel — `TenantNotificationController`, session routes in the tenant group
- **GET `/my/notifications`** — creates any due automatic reminders first (`TenantNotificationService::syncAutomatic()`), then returns `{ unread, notifications: [{ id, type, title, body, link, read, created_at, time_ago }] }`, newest 50.
- **PATCH `/my/notifications/{notification}/read`** — own notifications only (`404` otherwise).
- **POST `/my/notifications/read-all`**.
- Types: `bill_due`, `bill_overdue`, `bill_new`, `payment_approved`, `payment_rejected`, `penalty_added`, `penalty_waived`, `ticket_update`, `ticket_resolved`, `ticket_reply`, `announcement`, `account_restricted`, `account_restored`, `demand_letter`, `lease_ending`, `lease_renewed`, `deposit_refunded`. The panel marks `bill_overdue`, `payment_rejected`, `account_restricted` and `demand_letter` with a red dot.
- **Login pop-up:** `session('show_bill_popup')`, set by `AuthController::login()` for tenants and pulled by `partials/tenant-notifications`. Lists bills due within `TenantNotificationService::REMINDER_DAYS` (10) or overdue.

### Admin bell — `AdminNotificationController`
- **GET `/admin/notifications`** — `{ total, items: [{ key, urgent, link, count, label }] }`, only items with a count above 0. Keys: `proofs`, `applications`, `inquiries`, `tickets_new`, `tickets_overdue` (urgent), `delinquent` (urgent), `reviews`, `leases`.

---

## Security Deposit & Statement of Account (v39)

- **`deposit_refunds`**: `tenant_id`, `deposit_amount`, `deductions_amount`, `deductions_note`, `refund_amount`, `refund_method`, `reference_number`, `refunded_at`, `recorded_by`.
- **Deposit held** = `DepositRefund::depositHeldFor()`: half of the tenant's latest **paid** move-in bill's `base_rent`; 0 when there is none (e.g. walk-ins, whose deposit the admin types in).
- **GET `/tenant-manager/{tenant}`** now also returns `deposit: { held, suggested_deductions, refund }`.
- **POST `/tenant-manager/{tenant}/deposit-refund`** — `deposit_amount`, `deductions_amount`, `deductions_note` (required when deductions > 0), `refund_method`, `reference_number?`, `refunded_at` (not in the future). `409` if one exists, `422` if deductions exceed the deposit.
- **GET `/my/billing/statement-of-account`** (tenant) and **GET `/tenant-manager/{tenant}/statement-of-account`** (admin) — the PDF.
- **Duplicate reference check:** each row from `PaymentController::page()`'s pending list has `duplicates: [{ tenant_name, amount_paid, date, status }]`.

---

## Known Gaps / Not Yet Built

### Open, needing attention
*Re-checked on 2026-09-30 (v38) and updated for v41. Resolved items are removed; see git history for them. Grouped by the kind of work each one needs.*

#### A. Real bugs or missing features to build
- **Penalty edits aren't in the penalty history (v38).** `penalty_audit_logs.action` only allows `created`/`waived`/`reinstated`, so an Edit (new in v38) is written to the Laravel log (old and new description/amount) instead. Adding an `edited` value needs a migration with a SQLite path for the tests.
- **Payment Type page wording** and the other small copy items are under "Team decisions" below.

#### B. Must be done before going live
- **`DAYS_PER_STAGE = 1` is a testing value** (`EscalationService`). Highest priority: escalation runs for real as soon as the live server has a cron job.
- **The "Before a real dormitory goes live" list** in "Deployment" above (real email, private bucket, production DB, secrets).
- **Pureza document decisions** still open: see "Dorm Documents & Rental Policy → Still open, for BAGUI".
- **17 `composer audit` advisories (v38)** in packages the project already had (`guzzlehttp/guzzle`, `league/commonmark`, `laravel/framework`, `league/flysystem`). Run `composer update` and the tests before deploying.
- **Fill in the dorm's contact email and number on `/dormitory-profile`.** If they're blank, the Contact Admin buttons on the delinquency, blacklist and moved-out pages are hidden (by design, but easy to miss before a demo).
- **PHP `gd` (and for Excel, `zip` and `xml`) must be enabled on the live server.**

#### C. Team decisions (the code works; someone needs to choose)
- Bill reminders start 10 days before the due date (`TenantNotificationService::REMINDER_DAYS`) and the lease notice 30 days before the end (`LEASE_NOTICE_DAYS`); change there if needed. Reminders are in-app only, so a tenant who never logs in never sees them.
- Old notifications are kept forever. Consider deleting read ones after e.g. 6 months.
- Ticket time limits are placeholders: `URGENT_OVERDUE_HOURS = 24`, `NON_URGENT_OVERDUE_DAYS = 3`, `NON_URGENT_ESCALATE_DAYS = 5` (`MaintenanceTicket`).
- Login Tracker hides the Owner's own sign-ins and never deletes old rows. Show own sessions? Delete rows after e.g. 90 days?
- Reserved beds are only protected from click-cycling; they can still be changed in the room editor. Lock them completely?
- Dashboard Tenants chart shows new tenants per month, not the running total.
- The "N delinquent accounts need attention" banner partly repeats the dashboard Bills panel. Keep both?
- Move-out review SMS links to `/account` (works via redirect); link straight to `/moved-out` instead?
- Rent, Wi-Fi and utilities are split by total beds "for now". If it should be occupied beds, change `Room::perBedRate()` and `Room::utilityShares()` together.
- Cash isn't offered in the online move-in flow (the admin can still record it at the office).
- Backdated cash payments don't undo later penalties/stages. Current rule: record cash the same day.
- Payment methods can't be reordered (new ones go last).
- Partial Payment is described as "Pay part now and the remaining balance later." Confirm that matches how it really works.
- Resolved re-application requests are hidden from the "All" tab too. Should they stay visible for history?
- Payment Type page says "Please wait for the administrator's review…" before the tenant has even paid (`tenantmoveinpaymenttype.blade.php`). Reword?
- The dorm description only shows on the homepage, not on `/dorm-info`.
- Business Permit is admin-only (never shown publicly). Amenities are a fixed list of 8. No "uploaded on / verified by" dates for permits.
- Management doesn't countersign the e-signed Tenant Agreement; confirm that happens elsewhere.
- Manuscript Table 20's Exceptions row is a copy of its Postconditions row.
- Contract review steps 4 and 5 are minimal.
- Blocked-word review filter will have false positives and gaps; tune `config/review_moderation.php` as they come up.

#### D. Clean-up (safe to leave, tidy later)
- **`dormitory_profile.contract_template_path`** (routes removed in v38) and **`policies_file_path`** (upload removed in v41) are no longer read. Safe to drop in a later migration, along with the old files in `storage/app/public/dormitory-profile`.
- **Unused columns:** `floors.monthly_utility_cost` / `monthly_wifi_cost` (rooms now hold these) and `dormitory_profile.gcash_number` / `bdo_account_number` (payment methods replaced them).
- `admin_access_logs` records who/what/when, not exactly which privileges changed.
- Leftover styling: the sidebar's small ALL-CAPS "QUICK ACCESS" label, and admin heading sizes that are too close together (needs a site-wide pass).

#### E. Still to click through by hand in a browser
These were built and checked in code, but nobody has tested them by clicking through as a real user.
- **v39:** log in as a tenant with a bill due within 10 days and check the pop-up appears once, then not on the next page; open the bell, click an item, "Mark all as read"; the bell on a portal-restricted tenant; the admin bell counts and links; Record Deposit Refund in Tenant Manager; both Statement of Account downloads; a pending payment with a reused reference number. Check the bell and pop-up at phone width.
- **v38 (fixes round):** Submit Again on the move-in pending page; Edit/Reinstate/Delete on the Penalties tab; "Add unbilled penalties" in a statement drawer; receipt downloads (tenant and admin); Tenant Documents links on a move-in payment; publishing/hiding a review (check the email in Mailtrap and the Activity Log).
- **v38:** the proof viewer on `/payments` → Pending Payment (zoom, pinch on a phone, drag, Download, Esc); on `/reports`, generate each report, change the dates, export Excel and PDF, and confirm the files match the screen.
- **v36:** clicking a reserved bed on Vacancy Monitor; role tags on `/tickets`, `/my/tickets`, `/inquiries`, the delinquency history modal and both announcement feeds; a ticket's detail modal; "Show older sign-ins" with 30+ rows; the dashboard on a real phone.
- **v34:** add/edit/delete payment methods with QR swap and removal; each method on tenant Billing including cash; a real GCash proof (admin should see "GCash"); the QR viewer on a real phone; generating a bill after setting room Wi-Fi/utilities; Set Status on a delinquent tenant; admin modals on a phone.
- **v33:** the move-in flow with a real logged-in tenant (Welcome → Type → Method → Payment → Pending, and Log Out) at desktop and 375px.
- **Phone layouts** of the Penalties tab and modals, both tenant delinquency pages (every stage), Tenant Manager and Admin Privileges.
- **v41:** apply as a new tenant (read all three documents, sign on both pads, change a detail after signing and check that you must sign again); edit the Rental Policy and a room type and check the room prices and the admin document previews; add an Other Charge and use it in Add Penalty; let a bill pass its grace period and check the late penalty and notification; replace a VR scene photo.
- **Testing Tools:** the stage-jump buttons send every SMS they pass in one click. Fine for a quick demo; step one day at a time for a one-text-per-click walkthrough.

---

## Test Accounts
**Demo data (`DemoDataSeeder`, also in `database.sql` and on Laravel Cloud):** every account's password is **`Password123!`**. The Owner is `owner@nestph.test`; the other staff are `kristine.bautista@`, `mark.villanueva@`, `jerome.castillo@` and `lea.fernandez@nestph.test`; tenants are listed in the seeder and the walkthrough script.

**Basic seeder (`RolesAndTestUsersSeeder`; in production it creates the roles but skips these accounts):**
| Role | Email | Password |
|---|---|---|
| Tenant | tenant@nestph.test | password123 |
| Admin (limited privileges) | admin@nestph.test | password123 |
| Admin (all privileges, "owner") | owner@nestph.test | password123 |

These `password123` logins predate the v37 password policy. They still work because the policy only applies when a password is set or changed.

**Only the Owner account can reach `/admin-privileges`** (it holds `manage_users`). Other admins get a `403`.

**All of these accounts are dummies and must be wiped before a real dormitory goes live.**

---

## Route Index (generated, v41)

*232 routes, generated from `php artisan route:list --json` on 2026-10-02 (framework routes such as `storage/*`, `sanctum/*` and `up` left out). Regenerate this table rather than editing it by hand. Since v40: added the eight Rental Policy / Room Types / Other Charges / document preview routes and `POST /vr-tours/scenes/{scene}/photo`; removed `POST /dormitory-profile/policies-file`.*

### Public, auth and account pages (38)

| Method | Path | Handler | Name | Middleware |
|---|---|---|---|---|
| GET | `/` | `Api\PublicController@home` | `home` | public |
| POST | `/admin/login` | `Api\AuthController@login` |  | public |
| GET | `/apply` | `Api\PublicController@applyPage` | `public.apply` | public |
| GET | `/confirm-password` | `Auth\ConfirmablePasswordController@show` | `password.confirm` | auth |
| POST | `/confirm-password` | `Auth\ConfirmablePasswordController@store` |  | auth |
| GET | `/dorm-info` | `Api\PublicController@dormInfoPage` | `public.dorminfo` | public |
| GET | `/dorm-info/policies-file` | `Api\PublicController@policiesFileView` | `public.dorminfo.file` | public |
| GET | `/dorm-info/policies-file/download` | `Api\PublicController@policiesFileDownload` | `public.dorminfo.download` | public |
| POST | `/email/verification-notification` | `Auth\EmailVerificationNotificationController@store` | `verification.send` | auth, throttle:6, 1 |
| GET | `/forgot-password` | `Auth\PasswordResetLinkController@create` | `password.request` | guest |
| POST | `/forgot-password` | `Auth\PasswordResetLinkController@store` | `password.email` | guest |
| GET | `/inquire` | `closure` | `public.inquiry` | public |
| GET | `/login` | `Auth\AuthenticatedSessionController@create` | `login` | guest |
| POST | `/login` | `Auth\AuthenticatedSessionController@store` | `login` | guest |
| GET | `/login/admin` | `closure` | `login.admin` | public |
| GET | `/login/tenant` | `closure` | `login.tenant` | public |
| POST | `/logout` | `Auth\AuthenticatedSessionController@destroy` | `logout` | auth |
| PUT | `/password` | `Auth\PasswordController@update` | `password.update` | auth |
| POST | `/password/reset-code` | `Auth\PasswordResetCodeController@reset` | `password.code.reset` | public |
| POST | `/password/send-code` | `Auth\PasswordResetCodeController@send` | `password.code.send` | public |
| POST | `/password/verify-code` | `Auth\PasswordResetCodeController@verify` | `password.code.verify` | public |
| GET | `/passwords` | `closure` | `passwords` | public |
| GET | `/public-api/dorm-info` | `Api\PublicController@dormInfo` |  | public |
| GET | `/public-api/filter-options` | `Api\PublicController@filterOptions` |  | public |
| GET | `/public-api/rooms` | `Api\PublicController@rooms` |  | public |
| GET | `/public-api/rooms/{room}` | `Api\PublicController@room` |  | public |
| GET | `/public-api/rooms/{room}/beds` | `Api\PublicController@roomBeds` |  | public |
| GET | `/public-api/rooms/{room}/vr-tour` | `Api\PublicController@roomVrTour` |  | public |
| GET | `/public-api/vr-tours` | `Api\PublicController@vrTours` |  | public |
| GET | `/register` | `Auth\RegisteredUserController@create` | `register` | guest |
| POST | `/register` | `Auth\RegisteredUserController@store` |  | guest |
| POST | `/reset-password` | `Auth\NewPasswordController@store` | `password.store` | guest |
| GET | `/reset-password/{token}` | `Auth\NewPasswordController@create` | `password.reset` | guest |
| GET | `/rooms` | `closure` | `public.rooms` | public |
| POST | `/tenant/login` | `Api\AuthController@login` |  | public |
| GET | `/verify-email` | `Auth\EmailVerificationPromptController` | `verification.notice` | auth |
| GET | `/verify-email/{id}/{hash}` | `Auth\VerifyEmailController` | `verification.verify` | auth, signed, throttle:6, 1 |
| GET | `/vr-tour` | `closure` | `public.vr` | public |

### Tenant portal (33)

| Method | Path | Handler | Name | Middleware |
|---|---|---|---|---|
| GET | `/account` | `closure` | `tenant.account` | auth, tenant, movein.check, moveout.check, delinquency.check |
| GET | `/announcements` | `AnnouncementController@index` | `announcements.index` | auth, movein.check, moveout.check, delinquency.check |
| GET | `/announcements/{announcement}/comments` | `AnnouncementController@comments` | `announcements.comments.index` | auth, movein.check, moveout.check, delinquency.check |
| POST | `/announcements/{announcement}/comments` | `AnnouncementController@storeComment` | `announcements.comments.store` | auth, movein.check, moveout.check, delinquency.check |
| GET | `/billing` | `closure` | `tenant.billing` | auth, tenant, movein.check, moveout.check, delinquency.check |
| GET | `/dashboard` | `DashboardController@index` | `dashboard` | auth, movein.check, moveout.check, delinquency.check |
| GET | `/move-in` | `TenantOnboardingController@welcome` | `tenant.movein.welcome` | auth, tenant, movein.check, moveout.check, delinquency.check |
| GET | `/move-in/payment` | `TenantOnboardingController@payment` | `tenant.movein.payment` | auth, tenant, movein.check, moveout.check, delinquency.check |
| GET | `/move-in/payment-method` | `TenantOnboardingController@paymentMethod` | `tenant.movein.payment-method` | auth, tenant, movein.check, moveout.check, delinquency.check |
| POST | `/move-in/payment-method` | `TenantOnboardingController@storePaymentMethod` | `tenant.movein.payment-method.store` | auth, tenant, movein.check, moveout.check, delinquency.check |
| GET | `/move-in/payment-type` | `TenantOnboardingController@paymentType` | `tenant.movein.payment-type` | auth, tenant, movein.check, moveout.check, delinquency.check |
| POST | `/move-in/payment-type` | `TenantOnboardingController@storePaymentType` | `tenant.movein.payment-type.store` | auth, tenant, movein.check, moveout.check, delinquency.check |
| GET | `/move-in/pending` | `TenantOnboardingController@pendingVerification` | `tenant.movein.pending` | auth, tenant, movein.check, moveout.check, delinquency.check |
| GET | `/moved-out` | `TenantMoveOutController@show` | `tenant.moveout` | auth, tenant, movein.check, moveout.check, delinquency.check |
| GET | `/my/billing/bills` | `Api\TenantPortalController@myBills` |  | auth, tenant, movein.check, moveout.check, delinquency.check |
| GET | `/my/billing/bills/{billingStatement}` | `Api\TenantPortalController@showBill` |  | auth, tenant, movein.check, moveout.check, delinquency.check |
| POST | `/my/billing/bills/{billingStatement}/payment-proof` | `Api\TenantPortalController@submitProof` | `tenant.billing.payment-proof` | auth, tenant, movein.check, moveout.check, delinquency.check |
| GET | `/my/billing/payments/{payment}/receipt` | `Api\TenantPortalController@receiptPdf` | `tenant.billing.receipt` | auth, tenant, movein.check, moveout.check, delinquency.check |
| GET | `/my/billing/penalties` | `Api\TenantPortalController@myPenalties` |  | auth, tenant, movein.check, moveout.check, delinquency.check |
| GET | `/my/billing/statement-of-account` | `Api\TenantPortalController@statementOfAccountPdf` | `tenant.statement-of-account` | auth, tenant, movein.check, moveout.check, delinquency.check |
| GET | `/my/billing/summary` | `Api\TenantPortalController@me` |  | auth, tenant, movein.check, moveout.check, delinquency.check |
| GET | `/my/delinquency` | `TenantDelinquencyController@page` | `tenant.delinquency` | auth, tenant, movein.check, moveout.check, delinquency.check |
| GET | `/my/delinquency/demand-letter` | `TenantDelinquencyController@downloadDemandLetter` | `tenant.delinquency.demand-letter` | auth, tenant, movein.check, moveout.check, delinquency.check |
| GET | `/my/notifications` | `TenantNotificationController@index` | `tenant.notifications` | auth, tenant, movein.check, moveout.check, delinquency.check |
| POST | `/my/notifications/read-all` | `TenantNotificationController@markAllRead` |  | auth, tenant, movein.check, moveout.check, delinquency.check |
| PATCH | `/my/notifications/{notification}/read` | `TenantNotificationController@markRead` |  | auth, tenant, movein.check, moveout.check, delinquency.check |
| GET | `/my/tickets` | `TenantTicketController@page` | `tenant.tickets` | auth, tenant, movein.check, moveout.check, delinquency.check |
| POST | `/my/tickets` | `TenantTicketController@store` |  | auth, tenant, movein.check, moveout.check, delinquency.check |
| POST | `/my/tickets/{ticket}/reply` | `TenantTicketController@reply` |  | auth, tenant, movein.check, moveout.check, delinquency.check |
| DELETE | `/profile` | `ProfileController@destroy` | `profile.destroy` | auth, moveout.check, delinquency.check |
| GET | `/profile` | `ProfileController@edit` | `profile.edit` | auth, moveout.check, delinquency.check |
| PATCH | `/profile` | `ProfileController@update` | `profile.update` | auth, moveout.check, delinquency.check |
| POST | `/reviews` | `ReviewController@store` | `reviews.store` | auth, tenant, movein.check, moveout.check, delinquency.check |

### Admin (session) (109)

| Method | Path | Handler | Name | Middleware |
|---|---|---|---|---|
| GET | `/activity-log` | `DashboardController@activityLog` | `activity-log.index` | auth, admin |
| GET | `/admin-privileges` | `AdminPrivilegeController@index` | `admin-privileges.index` | auth, admin, privileges |
| POST | `/admin-privileges` | `AdminPrivilegeController@store` | `admin-privileges.store` | auth, admin, privileges |
| PATCH | `/admin-privileges/{user}/privileges` | `AdminPrivilegeController@updatePrivileges` | `admin-privileges.privileges.update` | auth, admin, privileges |
| PATCH | `/admin-privileges/{user}/revoke` | `AdminPrivilegeController@revoke` | `admin-privileges.revoke` | auth, admin, privileges |
| GET | `/admin/add-floor` | `VacancyController@index` | `admin.addfloor` | auth, admin |
| GET | `/admin/notifications` | `AdminNotificationController@index` | `admin.notifications` | auth, admin |
| POST | `/announcements` | `AnnouncementController@store` | `announcements.store` | auth, admin |
| DELETE | `/announcements/comments/{comment}` | `AnnouncementController@destroyComment` | `announcements.comments.destroy` | auth, admin |
| PATCH | `/announcements/{announcement}/restrict` | `AnnouncementController@toggleRestrict` | `announcements.restrict` | auth, admin |
| GET | `/applications` | `Api\ApplicationController@page` | `applications.index` | auth, admin |
| POST | `/applications/{application}/approve` | `Api\ApplicationController@approve` |  | auth, admin |
| POST | `/applications/{application}/reject` | `Api\ApplicationController@reject` |  | auth, admin |
| POST | `/applications/{application}/request-reapplication` | `Api\ApplicationController@requestReapplication` |  | auth, admin |
| POST | `/billing/generate` | `Api\BillingController@generate` |  | auth, admin |
| GET | `/billing/tenants/{tenant}/statements` | `Api\PaymentController@outstandingStatementsForTenant` |  | auth, admin |
| POST | `/billing/{billingStatement}/attach-penalties` | `Api\BillingController@attachPenalties` |  | auth, admin |
| POST | `/billing/{billingStatement}/payments/cash` | `Api\PaymentController@recordCash` |  | auth, admin |
| POST | `/damages` | `Api\DamageController@store` |  | auth, admin |
| GET | `/delinquency` | `DelinquencyController@page` | `delinquency.index` | auth, admin |
| GET | `/delinquency-testing/tenants` | `DelinquencyTestingController@index` |  | auth, admin |
| POST | `/delinquency-testing/{tenant}/escalate` | `DelinquencyTestingController@escalate` |  | auth, admin |
| GET | `/delinquency/{tenant}/demand-letter` | `DelinquencyController@downloadDemandLetter` |  | auth, admin |
| GET | `/delinquency/{tenant}/eviction-notice` | `DelinquencyController@downloadEvictionNotice` |  | auth, admin |
| POST | `/delinquency/{tenant}/eviction-notice` | `DelinquencyController@issueEvictionNotice` |  | auth, admin |
| GET | `/delinquency/{tenant}/history` | `DelinquencyController@history` |  | auth, admin |
| POST | `/delinquency/{tenant}/override` | `DelinquencyController@override` |  | auth, admin |
| GET | `/dormitory-profile` | `DormitoryProfileController@page` | `dormitory-profile.index` | auth, admin |
| POST | `/dormitory-profile` | `DormitoryProfileController@updateProfile` | `dormitory-profile.update` | auth, admin |
| POST | `/dormitory-profile/amenities/{amenity}/toggle` | `DormitoryProfileController@toggleAmenity` |  | auth, admin |
| DELETE | `/dormitory-profile/bir-registration` | `DormitoryProfileController@deleteBirRegistration` |  | auth, admin |
| POST | `/dormitory-profile/bir-registration` | `DormitoryProfileController@uploadBirRegistration` | `dormitory-profile.bir-registration` | auth, admin |
| POST | `/dormitory-profile/brand-logo` | `DormitoryProfileController@uploadBrandLogo` | `dormitory-profile.brand-logo` | auth, admin |
| DELETE | `/dormitory-profile/business-permit` | `DormitoryProfileController@deleteBusinessPermit` |  | auth, admin |
| POST | `/dormitory-profile/business-permit` | `DormitoryProfileController@uploadBusinessPermit` | `dormitory-profile.business-permit` | auth, admin |
| POST | `/dormitory-profile/charges` | `DormitoryProfileController@storeCharge` |  | auth, admin |
| DELETE | `/dormitory-profile/charges/{charge}` | `DormitoryProfileController@destroyCharge` |  | auth, admin |
| PATCH | `/dormitory-profile/charges/{charge}` | `DormitoryProfileController@updateCharge` |  | auth, admin |
| POST | `/dormitory-profile/cover-photo` | `DormitoryProfileController@uploadCoverPhoto` | `dormitory-profile.cover-photo` | auth, admin |
| GET | `/dormitory-profile/documents/{document}` | `DormitoryProfileController@previewDocument` | `dormitory-profile.documents` | auth, admin |
| POST | `/dormitory-profile/house-rules` | `DormitoryProfileController@storeHouseRule` | `dormitory-profile.house-rules.store` | auth, admin |
| DELETE | `/dormitory-profile/house-rules/{houseRule}` | `DormitoryProfileController@destroyHouseRule` |  | auth, admin |
| PATCH | `/dormitory-profile/house-rules/{houseRule}` | `DormitoryProfileController@updateHouseRule` |  | auth, admin |
| POST | `/dormitory-profile/payment-methods` | `PaymentMethodController@store` |  | auth, admin |
| DELETE | `/dormitory-profile/payment-methods/{paymentMethod}` | `PaymentMethodController@destroy` |  | auth, admin |
| POST | `/dormitory-profile/payment-methods/{paymentMethod}` | `PaymentMethodController@update` |  | auth, admin |
| POST | `/dormitory-profile/policy` | `DormitoryProfileController@updatePolicy` |  | auth, admin |
| POST | `/dormitory-profile/reviews/rescan` | `ReviewModerationController@rescan` | `dormitory-profile.reviews.rescan` | auth, admin |
| PATCH | `/dormitory-profile/reviews/{review}/hide` | `ReviewModerationController@hide` |  | auth, admin |
| PATCH | `/dormitory-profile/reviews/{review}/publish` | `ReviewModerationController@publish` |  | auth, admin |
| PATCH | `/dormitory-profile/reviews/{review}/remove` | `ReviewModerationController@remove` |  | auth, admin |
| POST | `/dormitory-profile/room-types` | `DormitoryProfileController@storeRoomType` |  | auth, admin |
| DELETE | `/dormitory-profile/room-types/{roomType}` | `DormitoryProfileController@destroyRoomType` |  | auth, admin |
| PATCH | `/dormitory-profile/room-types/{roomType}` | `DormitoryProfileController@updateRoomType` |  | auth, admin |
| GET | `/inquiries` | `Api\InquiryController@page` | `inquiries.index` | auth, admin |
| POST | `/inquiries/{inquiry}/reply` | `Api\InquiryController@reply` |  | auth, admin |
| PATCH | `/inquiries/{inquiry}/status` | `Api\InquiryController@updateStatus` |  | auth, admin |
| GET | `/lease-contracts` | `Api\LeaseContractController@page` | `contracts.index` | auth, admin |
| POST | `/lease-contracts` | `Api\LeaseContractController@store` |  | auth, admin |
| GET | `/lease-contracts/tenants/search` | `Api\LeaseContractController@searchTenants` |  | auth, admin |
| PATCH | `/lease-contracts/{leaseContract}/not-applicable` | `Api\LeaseContractController@markNotApplicable` |  | auth, admin |
| PATCH | `/lease-contracts/{leaseContract}/renew` | `Api\LeaseContractController@renew` |  | auth, admin |
| POST | `/lease-contracts/{leaseContract}/sign` | `Api\LeaseContractController@submitSigned` |  | auth, admin |
| PATCH | `/lease-contracts/{leaseContract}/terminate` | `Api\LeaseContractController@terminate` |  | auth, admin |
| GET | `/payments` | `Api\PaymentController@page` | `payments.index` | auth, admin |
| POST | `/payments/{payment}/approve` | `Api\PaymentController@approveProof` |  | auth, admin |
| GET | `/payments/{payment}/receipt` | `Api\PaymentController@receiptPdf` |  | auth, admin |
| POST | `/payments/{payment}/reject` | `Api\PaymentController@rejectProof` |  | auth, admin |
| GET | `/penalties` | `Api\PenaltyController@index` |  | auth, admin |
| POST | `/penalties` | `Api\PenaltyController@store` |  | auth, admin |
| DELETE | `/penalties/{penalty}` | `Api\PenaltyController@destroy` |  | auth, admin |
| PATCH | `/penalties/{penalty}` | `Api\PenaltyController@update` |  | auth, admin |
| PATCH | `/penalties/{penalty}/reinstate` | `Api\PenaltyController@reinstate` |  | auth, admin |
| PATCH | `/penalties/{penalty}/waive` | `Api\PenaltyController@waive` |  | auth, admin |
| GET | `/reports` | `ReportController@page` | `reports.index` | auth, admin |
| GET | `/reports/expenses` | `ReportController@showExpenses` |  | auth, admin |
| POST | `/reports/expenses` | `ReportController@saveExpenses` |  | auth, admin |
| GET | `/reports/export` | `ReportController@export` |  | auth, admin |
| GET | `/reports/financial` | `ReportController@financial` |  | auth, admin |
| GET | `/reports/occupancy` | `ReportController@occupancy` |  | auth, admin |
| GET | `/tenant-manager` | `TenantController@page` | `tenant-manager.index` | auth, admin |
| POST | `/tenant-manager` | `TenantController@store` |  | auth, admin |
| GET | `/tenant-manager/{tenant}` | `TenantController@show` |  | auth, admin |
| POST | `/tenant-manager/{tenant}` | `TenantController@update` |  | auth, admin |
| POST | `/tenant-manager/{tenant}/deposit-refund` | `TenantController@recordDepositRefund` |  | auth, admin |
| GET | `/tenant-manager/{tenant}/statement-of-account` | `TenantController@statementOfAccountPdf` |  | auth, admin |
| POST | `/tenant-manager/{tenant}/status` | `TenantController@setStatus` |  | auth, admin |
| GET | `/tenants/{tenant}/active-lease` | `Api\LeaseContractController@activeLeaseForTenant` |  | auth, admin |
| GET | `/tickets` | `TicketController@page` | `tickets.index` | auth, admin |
| GET | `/tickets/{ticket}` | `TicketController@show` |  | auth, admin |
| PATCH | `/tickets/{ticket}` | `TicketController@update` |  | auth, admin |
| GET | `/vacancy-monitoring` | `VacancyController@index` | `vacancy.index` | auth, admin |
| PATCH | `/vacancy/beds/{bed}` | `VacancyController@updateBedStatus` | `vacancy.beds.update` | auth, admin |
| DELETE | `/vacancy/floors/{floorNumber}` | `VacancyController@destroyFloor` | `vacancy.floors.destroy` | auth, admin |
| POST | `/vacancy/rooms` | `VacancyController@storeRoom` | `vacancy.rooms.store` | auth, admin |
| DELETE | `/vacancy/rooms/{room}` | `VacancyController@destroyRoom` | `vacancy.rooms.destroy` | auth, admin |
| PUT | `/vacancy/rooms/{room}` | `VacancyController@updateRoom` | `vacancy.rooms.update` | auth, admin |
| POST | `/vacancy/rooms/{room}/photos/reorder` | `VacancyController@reorderRoomPhotos` |  | auth, admin |
| DELETE | `/vacancy/rooms/{room}/photos/{photo}` | `VacancyController@deleteRoomPhoto` |  | auth, admin |
| PATCH | `/vacancy/rooms/{room}/vr-info` | `VacancyController@updateVrInfo` |  | auth, admin |
| GET | `/vr-management` | `VacancyController@vrIndex` | `vr.index` | auth, admin |
| DELETE | `/vr-tours/hotspots/{hotspot}` | `VrTourController@destroyHotspot` |  | auth, admin |
| POST | `/vr-tours/rooms/{room}/scenes` | `VrTourController@storeScene` |  | auth, admin |
| DELETE | `/vr-tours/scenes/{scene}` | `VrTourController@destroyScene` |  | auth, admin |
| PATCH | `/vr-tours/scenes/{scene}` | `VrTourController@updateScene` |  | auth, admin |
| POST | `/vr-tours/scenes/{scene}/default` | `VrTourController@setDefaultScene` |  | auth, admin |
| POST | `/vr-tours/scenes/{scene}/hotspots` | `VrTourController@storeHotspot` |  | auth, admin |
| POST | `/vr-tours/scenes/{scene}/photo` | `VrTourController@replaceScenePhoto` |  | auth, admin |
| PATCH | `/vr-tours/scenes/{scene}/view` | `VrTourController@updateSceneView` |  | auth, admin |

### Sanctum / JSON API (`/api/...`) (52)

| Method | Path | Handler | Name | Middleware |
|---|---|---|---|---|
| GET | `/api/applications` | `Api\ApplicationController@index` |  | auth:sanctum, admin |
| POST | `/api/applications` | `Api\ApplicationController@store` |  | public |
| POST | `/api/applications/contract-preview` | `Api\ApplicationController@previewContract` |  | public |
| POST | `/api/applications/contract-sign` | `Api\ApplicationController@signContract` |  | public |
| GET | `/api/applications/{application}` | `Api\ApplicationController@show` |  | auth:sanctum, admin |
| PATCH | `/api/applications/{application}/approve` | `Api\ApplicationController@approve` |  | auth:sanctum, admin |
| PATCH | `/api/applications/{application}/cancel` | `Api\ApplicationController@cancel` |  | auth:sanctum, admin |
| PATCH | `/api/applications/{application}/reject` | `Api\ApplicationController@reject` |  | auth:sanctum, admin |
| GET | `/api/billing` | `Api\BillingController@index` |  | auth:sanctum, admin |
| POST | `/api/billing/contracts/{contract}/generate` | `Api\BillingController@generateForContractEndpoint` |  | auth:sanctum, admin |
| POST | `/api/billing/generate` | `Api\BillingController@generate` |  | auth:sanctum, admin |
| GET | `/api/billing/{billingStatement}` | `Api\BillingController@show` |  | auth:sanctum, admin |
| POST | `/api/billing/{billingStatement}/attach-penalties` | `Api\BillingController@attachPenalties` |  | auth:sanctum, admin |
| GET | `/api/billing/{billingStatement}/payments` | `Api\PaymentController@historyForStatement` |  | auth:sanctum, admin |
| POST | `/api/billing/{billingStatement}/payments/cash` | `Api\PaymentController@recordCash` |  | auth:sanctum, admin |
| GET | `/api/damages` | `Api\DamageController@index` |  | auth:sanctum, admin |
| POST | `/api/damages` | `Api\DamageController@store` |  | auth:sanctum, admin |
| DELETE | `/api/damages/{damage}` | `Api\DamageController@destroy` |  | auth:sanctum, admin |
| GET | `/api/damages/{damage}` | `Api\DamageController@show` |  | auth:sanctum, admin |
| POST | `/api/damages/{damage}` | `Api\DamageController@update` |  | auth:sanctum, admin |
| GET | `/api/inquiries` | `Api\InquiryController@index` |  | auth:sanctum, admin |
| POST | `/api/inquiries` | `Api\InquiryController@store` |  | public |
| GET | `/api/inquiries/{inquiry}` | `Api\InquiryController@show` |  | auth:sanctum, admin |
| PATCH | `/api/inquiries/{inquiry}/status` | `Api\InquiryController@updateStatus` |  | auth:sanctum, admin |
| GET | `/api/lease-contracts` | `Api\LeaseContractController@index` |  | auth:sanctum, admin |
| GET | `/api/lease-contracts/{leaseContract}` | `Api\LeaseContractController@show` |  | auth:sanctum, admin |
| PATCH | `/api/lease-contracts/{leaseContract}/not-applicable` | `Api\LeaseContractController@markNotApplicable` |  | auth:sanctum, admin |
| POST | `/api/lease-contracts/{leaseContract}/sign` | `Api\LeaseContractController@submitSigned` |  | auth:sanctum, admin |
| PATCH | `/api/lease-contracts/{leaseContract}/terminate` | `Api\LeaseContractController@terminate` |  | auth:sanctum, admin |
| POST | `/api/login` | `Api\AuthController@login` |  | public |
| POST | `/api/logout` | `Api\AuthController@logout` |  | public |
| GET | `/api/my/account` | `Api\TenantPortalController@me` |  | auth:sanctum, tenant |
| GET | `/api/my/bills` | `Api\TenantPortalController@myBills` |  | auth:sanctum, tenant |
| GET | `/api/my/bills/{billingStatement}` | `Api\TenantPortalController@showBill` |  | auth:sanctum, tenant |
| POST | `/api/my/bills/{billingStatement}/payment-proof` | `Api\TenantPortalController@submitProof` |  | auth:sanctum, tenant |
| GET | `/api/my/payments` | `Api\TenantPortalController@myPayments` |  | auth:sanctum, tenant |
| GET | `/api/my/payments/{payment}/receipt` | `Api\TenantPortalController@receipt` |  | auth:sanctum, tenant |
| GET | `/api/my/penalties` | `Api\TenantPortalController@myPenalties` |  | auth:sanctum, tenant |
| GET | `/api/payments` | `Api\PaymentController@index` |  | auth:sanctum, admin |
| GET | `/api/payments/{payment}` | `Api\PaymentController@show` |  | auth:sanctum, admin |
| PATCH | `/api/payments/{payment}/approve` | `Api\PaymentController@approveProof` |  | auth:sanctum, admin |
| PATCH | `/api/payments/{payment}/reject` | `Api\PaymentController@rejectProof` |  | auth:sanctum, admin |
| GET | `/api/penalties` | `Api\PenaltyController@index` |  | auth:sanctum, admin |
| POST | `/api/penalties` | `Api\PenaltyController@store` |  | auth:sanctum, admin |
| DELETE | `/api/penalties/{penalty}` | `Api\PenaltyController@destroy` |  | auth:sanctum, admin |
| GET | `/api/penalties/{penalty}` | `Api\PenaltyController@show` |  | auth:sanctum, admin |
| PATCH | `/api/penalties/{penalty}` | `Api\PenaltyController@update` |  | auth:sanctum, admin |
| PATCH | `/api/penalties/{penalty}/reinstate` | `Api\PenaltyController@reinstate` |  | auth:sanctum, admin |
| PATCH | `/api/penalties/{penalty}/waive` | `Api\PenaltyController@waive` |  | auth:sanctum, admin |
| GET | `/api/public/rooms` | `Api\PublicRoomController@index` |  | public |
| GET | `/api/tenants/{tenant}/running-total` | `Api\PenaltyController@runningTotal` |  | auth:sanctum, admin |
| GET | `/api/user` | `closure` |  | auth:sanctum |
