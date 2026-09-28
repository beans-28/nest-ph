# Dashboard charts, login tracker, and role tags (2026-09-28)

A record of everything changed in this session, in the order the work happened. Each section lists the problem, what changed, the files involved, and anything still to watch.

## Contents

1. [Dashboard stat cards turned into charts](#1-dashboard-stat-cards-turned-into-charts)
2. [Dashboard redesign with Impeccable](#2-dashboard-redesign-with-impeccable)
3. [Reserved beds were invisible](#3-reserved-beds-were-invisible)
4. [Login Tracker for the Owner](#4-login-tracker-for-the-owner)
5. [Creating the first Owner on a new install](#5-creating-the-first-owner-on-a-new-install)
6. [Migrations that broke the test suite](#6-migrations-that-broke-the-test-suite)
7. [Old starter tests and rollback failures](#7-old-starter-tests-and-rollback-failures)
8. [Owner and Admin tags](#8-owner-and-admin-tags)
9. [Impeccable audit of the new work](#9-impeccable-audit-of-the-new-work)
10. [Audit fixes](#10-audit-fixes)
11. [Readability fixes to older labels](#11-readability-fixes-to-older-labels)
12. [Tickets page cleanup and the broken "!" icon](#12-tickets-page-cleanup-and-the-broken--icon)
13. [Database changes](#13-database-changes)
14. [Files changed](#14-files-changed)
15. [Testing checklist](#15-testing-checklist)
16. [Open items](#16-open-items)

---

## 1. Dashboard stat cards turned into charts

**Request.** The adviser asked for the four dashboard cards (Total Tenants, Revenue, Delinquent, Vacancy Rate) to become graphs.

**Change.** Each card kept its big number and got a small chart under it, drawn with **Chart.js**, a free charting library loaded from a CDN (nothing to install).

- `DashboardController::adminDashboard()` now builds a `$cardCharts` array: the last 6 months of new tenants and approved revenue, plus bill counts by status and bed counts by status.
- Revenue uses the same "approved payments only" rule as the headline number, so the two always agree.

This version was replaced in section 2.

## 2. Dashboard redesign with Impeccable

**Problem.** Four identical "number plus mini chart" cards is a generic, machine-made dashboard pattern.

**Change.** The top of the dashboard is now four panels, each shaped by its own data:

| Panel | What it shows |
|---|---|
| **Collections** (large, left) | This month's approved revenue, a "+81% vs August" style change badge, and a 6-month bar chart with a ₱ scale. The current month is dark green; past months are faded. |
| **Beds** | "11/15 occupied", plus **one square per real bed**, grouped by floor. Hovering a square shows the room and bed. This follows the product principle that vacancy is tracked per bed, not just per room. |
| **Tenants** | Active tenant count and a small chart of new tenants per month. |
| **Bills** (full-width strip) | One bar split into Paid / Partial / Unpaid / Overdue, with counts. It links to Delinquency. |

- Each panel has a link in its top-right corner to the matching page (for example "Vacancy Monitor →").
- The old **Occupancy** card lower on the page was removed, because the bed map shows the same thing in more detail.
- On screens narrower than 1080px, the panels stack into one column.
- `DashboardController` also sends a `$bedMap` (every bed, grouped by floor) and `$newTenantsThisMonth`.

## 3. Reserved beds were invisible

**Problem.** A later migration added a fourth bed status, **reserved** (a bed held for an applicant). The dashboard bed map had no colour for it, so reserved beds were drawn blank. That also shifted the Floor 1 row. The Vacancy Monitor page didn't know about "reserved" either.

**Change.**

- `public/css/admin.css`: a new shared purple colour, `--status-reserved` / `--status-reserved-bg`.
- **Dashboard:** reserved beds are purple, "Reserved" is in the legend, and the summary line mentions how many are reserved.
- **Vacancy Monitor** (`adminaddfloor.blade.php` + `VacancyController`):
  - Reserved beds are purple, and "Reserved" is in the legend.
  - There's a new **Reserved** count card.
  - The stat cards wrap to 3 per row on medium screens.
  - The room editor's bed dropdown gained a "Reserved" option. Before, a reserved bed opened in the editor showed as "Vacant", and saving would have quietly un-reserved it.

## 4. Login Tracker for the Owner

**Request.** On the Admin Privileges page, the Owner should see when other admin accounts log in and out.

**Change.**

- **A new table, `admin_login_sessions`.** Each row is one sign-in: the user, their session id, IP address, browser, login time and logout time.
- **Model `AdminLoginSession`.**
  - `recordLogin()` and `recordLogout()` only record admin accounts; tenants are ignored.
  - `deviceLabel()` turns the browser's technical ID string into "Chrome on Windows".
- **Login is recorded in both login routes:** `Api\AuthController::login` and `Auth\AuthenticatedSessionController::store`.
- **Logout is recorded in both logout routes:** `Api\AuthController::logout` and `AuthenticatedSessionController::destroy`.
- **The Admin Privileges page now has a "Login Tracker" section.** It lists other admins only (not the Owner viewing it). Each row shows the admin, login time, logout time, how long they stayed, and their device and IP. A "Signed in now: …" line sits at the top.
- **The Logged out column shows one of three states:**
  - **A date and time:** they pressed Log Out.
  - **"Still signed in":** their session still exists in the `sessions` table and was active within the 30-minute session lifetime.
  - **"Didn't log out":** the session timed out or the browser was closed.
- **Access is already Owner-only.** The page sits behind the existing `privileges` middleware, and the Owner is the admin with the `manage_users` privilege.

**Bug found while testing.** The login and logout columns were first created as `timestamp`. MySQL silently updated `logged_in_at` to "now" whenever the row changed, so saving a logout overwrote the login time. The columns are now `dateTime`, which MySQL never auto-updates.

## 5. Creating the first Owner on a new install

**Problem.** Only an Owner can create admin accounts, so a fresh install for a new dormitory has no way to create the first one. The test seeder creates `owner@nestph.test` with the password `password123`, which must never be used on a real dormitory.

**Change.** A new terminal command:

```
php artisan nestph:create-owner
```

- It asks for the Owner's name, email (checked to be valid and not already used) and password (hidden, at least 8 characters, typed twice).
- It creates an active admin account with all six privileges, including `manage_users`.
- If an Owner already exists, it names them and asks before creating another.
- It creates the `admin` and `tenant` roles itself if they're missing, so it works on an empty database.
- `README.md` now starts with a "Setting up NEST.PH for a new dormitory" section, including a warning never to run `db:seed` on a real installation.
- `tests/Feature/CreateOwnerCommandTest.php` covers the normal path (including a real login with the new Owner) and the "Owner already exists" path.

## 6. Migrations that broke the test suite

**Problem.** Four migrations used `ALTER TABLE … MODIFY`, which only MySQL understands. `php artisan test` uses an in-memory SQLite database, so every test that needed a database crashed during setup.

**Change.** Each of those four migrations has a small `modifyColumn()` helper:

- **On MySQL or MariaDB** it runs the original line, **unchanged**.
- **On any other database** it makes the same change with Laravel's portable `->change()`, which is built into Laravel 11+ and needs no extra package.

Migrations that have already run never run again, so existing databases are not affected.

| Migration | What it changes |
|---|---|
| `2026_08_30_000001_add_reserved_status_and_application_workflow_fields` | Bed "reserved" status; application "re-application requested" status |
| `2026_08_30_000002_add_lease_lifecycle_fields` | Lease "expiring soon" / "expired" statuses |
| `2026_09_04_140701_change_tenant_type_to_string_on_tenants_table` | Tenant type becomes plain text |
| `2026_09_04_185202_rename_archived_to_inactive_on_tenants_table` | Tenant status "archived" renamed to "inactive" |

## 7. Old starter tests and rollback failures

**Old starter tests.** Two leftover Laravel Breeze tests were deleted, because they tested features NEST.PH doesn't have:

- "new users can register" assumed anyone can sign up and be logged in straight away. Tenants come in through applications instead.
- "profile page is displayed" looked for a page file that no longer exists.

The other tests in those files still pass and were kept. **The full test suite now passes: 25 tests, 0 failures.**

**Rollback failures.** Undoing migrations (`migrate:reset`) failed on three migrations:

- **Why:** MySQL requires every foreign key (a link to another table, like `tenant_id`) to have an index. When each migration added a combined index starting with that column, MySQL removed its own automatic index. Undoing the migration then tried to remove the only index left, and MySQL refused.
- **Fix:** each migration's undo step now puts a plain index back first, then removes the combined one.
- **Files:** escalation logs (`billing_id`), payments (`tenant_id`) and billing statements (`tenant_id`).
- **Result:** on a fresh test MySQL database, all 75 migrations run, undo and re-run with no failures.

## 8. Owner and Admin tags

**Request.** Show an "Owner" or "Admin" tag next to staff names everywhere.

**Change.**

- **`User::isOwner()` and `User::roleTag()`** return `owner` (an admin with `manage_users`), `admin`, or nothing for tenants. This is the same rule the Owner-only middleware uses.
- **`partials/role-tag-style.blade.php`** defines the tag's look once. Owner is a solid dark-green pill with white text; Admin is a light-green pill with green text. `partials/role-tag.blade.php` draws the tag in server-rendered pages.
- **Pages that build their lists in JavaScript** use a matching one-line `roleTag()` helper.
- **Each controller** now sends a tag alongside the name, and loads roles and privileges up front, so there's no extra database query per row.

| Page | Where the tag appears |
|---|---|
| Admin Privileges | The admin list and the Login Tracker. The tag updates straight away when privileges change or an admin is added. |
| Announcements (admin and tenant) | The poster, and admin comments. Before, every post was labelled "Admin", even the Owner's. |
| Tickets (admin and tenant) | Admin replies |
| Inquiries | "Replied by …" |
| Delinquency | The history timeline's "by …" |

**Not tagged:** the Activity Log. Its entries are full sentences, and a tag in the middle reads awkwardly.

## 9. Impeccable audit of the new work

Impeccable's detector (an automatic checker) and a manual code read covered everything above. The score was **13/20 ("Acceptable")**.

| Priority | Finding |
|---|---|
| P1 | The Login Tracker's small grey text was too faint (3.1:1; the standard needs 4.5:1). |
| P1 | Clicking a reserved bed on Vacancy Monitor turned it into Vacant with no warning. |
| P2 | The bed map relied on colour alone, and its details were only visible on mouse hover. |
| P2 | The role tag text was 9.5px. |
| P2 | The dashboard's panel links were small tap targets on phones. |
| P2 | Colour codes were typed directly into the new dashboard code instead of using shared names. |
| P3 | Charts left a blank box if Chart.js failed to load. |
| P3 | The Login Tracker only ever showed the latest 30 sign-ins. |

**False alarms:** "Roboto is overused" (Roboto is the locked brand font) and "table has no padding" (the cells have their own padding).

## 10. Audit fixes

- **Login Tracker text:** uses the darker grey (`--text-mid`, about 5.6:1) at 12px.
- **Reserved beds:** clicking one no longer changes it. A message points to the room's **Edit** screen for deliberate changes.
- **The bed map no longer relies on colour alone:**
  - Maintenance beds are striped, reserved beds have a white centre dot, and open beds are an outline.
  - Screen readers hear a summary per floor, like "Floor 1: 8 occupied, 1 reserved, 2 open, 0 in maintenance". The squares themselves are hidden from screen readers.
- **Role tags** are 11px.
- **Panel links** have a 44px tall tap area on screens 720px wide or less, without moving the layout.
- **Colours** moved into `admin.css` as shared names: `--danger`, `--danger-text`, `--chart-muted`, `--chart-muted-hover`, `--chart-grid`, `--chart-tooltip`, `--bar-neutral` and `--bed-open-ring`. The charts read these values at runtime. A `.sr-only` helper was added for text only screen readers hear.
- **Chart fallback:** if Chart.js doesn't load, the chart area says "Chart couldn't load. The numbers above are still up to date."
- **The Login Tracker** has a "Show older sign-ins" button that loads 30 more each time (`?tracker=60`, `90`, and so on, capped at 1000).

## 11. Readability fixes to older labels

**Problem.** Older parts of the dashboard and Admin Privileges pages used tiny (10–11px), ALL-CAPS, letter-spaced, pale labels. They failed the contrast check and looked machine-made.

**Dashboard:**

- **Ticket pills** now use normal capitalisation at 11.5px, with darker text colours:

  | Pill | Before | After |
  |---|---|---|
  | In Progress | 2.3:1 | 4.6:1 |
  | Urgent / Overdue | 3.8:1 | 5.6:1 |
  | Open | 4.2:1 | 5.5:1 |
  | Seen | 4.1:1 | 5.5:1 |
  | Non-urgent | 4.3:1 | above 4.5:1 |

- **Activity table headers** are 12px in the darker grey. The ticket time, meta line and summary line are 12px as well.

**Admin Privileges:**

- **Labels:** "Active admins" and "Privileges assigned" are in normal text. "YOU" became "(you)", and the inactive status uses the readable grey.
- **Revoke:** the red is darker (5.4:1) and the button has a 44px tap area. On your own row, the faded disabled button is replaced by the words "Can't revoke yourself".
- **Add Admin popup:** the labels, the "Starting privileges" heading and the temporary-password note use normal text at a readable size.
- **Wrapping:** buttons, dates and the "(you)" label no longer wrap onto a second line.

## 12. Tickets page cleanup and the broken "!" icon

**Ticket cards.** Up to five coloured pills per card were replaced with plain text:

- **Layout:** the title comes first. One grey line follows: "Tenant, Room · Category · #id".
- **Status and Overdue:** the status is a coloured dot plus the word ("● In Progress"). "Overdue" is bold red text on the same line, with no icon. "Auto-escalated" is a plain red text line.
- **Kept as a control:** the priority dropdown, because it's actually clicked. It's slightly larger now, and "Assigned:" uses the readable grey.
- **Cleanup:** the unused `.badge` and `.overdue-flag` styles were deleted.

**Broken "!" icon.** The "!" in several icons is a line plus a dot. The dot is drawn as a tiny line (`h.01`), which only shows up when line ends are rounded, so only the line appeared. Adding `stroke-linecap="round"` fixed it in:

- `admintickets.blade.php` (the Overdue stat card)
- `loginadmin.blade.php` and `logintenant.blade.php`
- `publicdorminfo.blade.php` and `welcome.blade.php`

The sidebar and Delinquency page icons already had rounded ends.

## 13. Database changes

- **New table `admin_login_sessions`** (migration `2026_09_28_000001`). **Each teammate must run `php artisan migrate` after pulling.**
- **Migrations edited** (sections 6 and 7). These only change behaviour on SQLite and during rollback, so existing MySQL databases don't need anything.

## 14. Files changed

**New**

- `app/Console/Commands/CreateOwner.php`
- `app/Models/AdminLoginSession.php`
- `database/migrations/2026_09_28_000001_create_admin_login_sessions_table.php`
- `resources/views/partials/role-tag-style.blade.php`
- `resources/views/partials/role-tag.blade.php`
- `tests/Feature/CreateOwnerCommandTest.php`
- `docs/dashboard-charts-login-tracker-and-role-tags.md` (this file)

**Changed: back end**

- `app/Models/User.php`: `isOwner()` and `roleTag()`
- `app/Http/Controllers/DashboardController.php`: chart data and bed map
- `app/Http/Controllers/VacancyController.php`: reserved count
- `app/Http/Controllers/AdminPrivilegeController.php`: login tracker, role tags, "show older"
- `app/Http/Controllers/Api/AuthController.php` and `Auth/AuthenticatedSessionController.php`: recording logins and logouts
- `app/Http/Controllers/AnnouncementController.php`, `TicketController.php`, `TenantTicketController.php`, `Api/InquiryController.php`, `DelinquencyController.php`: role tags

**Changed: pages and styles**

- `public/css/admin.css`: reserved colour, shared chart and danger colours, `.sr-only`
- `resources/views/admindashboard.blade.php`
- `resources/views/adminaddfloor.blade.php`
- `resources/views/adminprivileges.blade.php`
- `resources/views/admintickets.blade.php`
- `resources/views/tenanttickets.blade.php`
- `resources/views/admininquiries.blade.php`
- `resources/views/delinquency.blade.php`
- `resources/views/partials/announcements-feed.blade.php`
- `resources/views/loginadmin.blade.php`, `logintenant.blade.php`, `publicdorminfo.blade.php`, `welcome.blade.php`: icon fix only

**Changed: database and tests**

- Seven migrations (sections 6 and 7)
- `tests/Feature/ProfileTest.php` and `tests/Feature/Auth/RegistrationTest.php`: old tests removed
- `README.md`: setup section

## 15. Testing checklist

Already verified in this session:

- [x] `php artisan test`: 25 passed, 0 failed
- [x] All 75 migrations run, undo and re-run cleanly on a fresh MySQL database
- [x] Admin login and logout are recorded; the login time stays unchanged after logout
- [x] `nestph:create-owner` creates an Owner who can log in
- [x] Screenshots of the dashboard, Admin Privileges and Tickets pages, with the detector clean apart from known false alarms

Still to check in a real browser:

- [ ] Vacancy Monitor: a reserved bed shows purple, and clicking it shows the message instead of changing it
- [ ] Owner/Admin tags on Tickets (admin and tenant), Inquiries, Delinquency and the announcements feed
- [ ] The ticket details popup, which may still use pill-style labels
- [ ] The Login Tracker's "Show older sign-ins" button (needs more than 30 sign-ins)
- [ ] The dashboard on a real phone (screenshots could only go down to about 500px wide)

## 16. Open items

For the project manager (BAGUI) to decide:

1. **Tenants chart:** it counts new tenants per month, not total tenants over time. A true total could be built from contract start and end dates.
2. **Duplicate information on the dashboard:** the "9 delinquent accounts" alert banner partly repeats the Bills strip.
3. **Reserved beds:** they're currently protected from click-cycling. Should they be fully locked, including from the Edit screen?
4. **Login Tracker:** should the Owner's own sign-ins be listed too? Should very old records be cleared automatically (for example, after 90 days)?
5. **Test seeder:** make `db:seed` refuse to run when the app is in production mode.
6. **Leftover styling:** the sidebar's "QUICK ACCESS" label is still small ALL-CAPS text, and heading sizes are close together across all admin pages. Both need a site-wide pass.
7. **Impeccable update:** `npx impeccable update` failed with an HTTP 404 on Impeccable's side. Retry later with `npx impeccable@latest update`.
