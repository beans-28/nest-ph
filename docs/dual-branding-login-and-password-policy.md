# Dual branding, login pages, and password policy (2026-09-29)

A record of everything changed in this session, in the order the work happened. Each section lists the problem, what changed, the files involved, and anything still to watch. The technical reference for all of this is **API Contract v37** (`docs/API Contract v37.md`).

After pulling: run `php artisan migrate` (one new migration) and `php artisan view:clear`, then hard-refresh the browser (Ctrl+F5).

## Contents

1. [Branding audit: who owns the brand?](#1-branding-audit-who-owns-the-brand)
2. [Dual branding built](#2-dual-branding-built)
3. [Dorm Logo upload](#3-dorm-logo-upload)
4. [Wording that treated NEST.PH as the dorm](#4-wording-that-treated-nestph-as-the-dorm)
5. [Impeccable polish pass on the branding](#5-impeccable-polish-pass-on-the-branding)
6. [New tagline and the real NEST.PH logo](#6-new-tagline-and-the-real-nestph-logo)
7. [Login pages that don't scroll](#7-login-pages-that-dont-scroll)
8. [Stronger password rule](#8-stronger-password-rule)
9. [Database changes](#9-database-changes)
10. [Files changed](#10-files-changed)
11. [Testing checklist](#11-testing-checklist)
12. [Open items](#12-open-items)

---

## 1. Branding audit: who owns the brand?

**Request.** Under the Data Privacy Act (RA 10173), the **dormitory is the PIC** (Personal Information Controller, responsible for tenants' data) and **NEST.PH is the PIP** (Personal Information Processor, the software handling data on the dorm's behalf). So both trademarks must be visible. The team shared a table of where each logo should go and asked for a check of what already existed.

**Findings.** Almost none of it was in place. The app showed NEST.PH as the main brand nearly everywhere:

- Sidebars, login pages and all 38 browser tab titles said "NEST.PH".
- All 9 emails had "NEST.PH" as their header, with wording like "Thank you for your interest in NEST.PH".
- The **lease contract** named "NEST.PH Pureza Station Dormitory" (typed in by hand), and the **eviction notice** said the tenant's lease was "with NEST PH". Legally, the agreement is with the dorm.
- `public/favicon.ico` was an empty file (0 bytes), so browser tabs had a blank icon.
- The dorm had no logo slot at all: the `logo_path` column was already being used for the **cover photo**.

The app manages **one** dorm (not many dorms on separate web addresses), so "tenant login" and "login page" in the team's table became the same page.

## 2. Dual branding built

**Change.** The dorm is now the main identity everywhere, and NEST.PH appears as a small "Powered by" mark.

| Area | Dorm | NEST.PH |
|---|---|---|
| Sidebars (admin, tenant, restricted tenant) | Logo and full name at the top | "Powered by NEST.PH" under Log Out, linking to the support email |
| Public top bar | Logo and name | — |
| Login pages | Logo and name above the form | "Powered by NEST.PH" below the form |
| Browser tab | "Admin Dashboard · Pureza Station Dormitory", dorm logo as icon | Green NEST.PH icon if no dorm logo |
| Emails | Name (and logo) in the header, name in the subject line | Footer: "Sent by [Dorm] via NEST.PH" |
| PDFs | Name (and logo) in the header | Footer: "Issued by [Dorm] · Generated via NEST.PH" |
| Landing page footer | "© 2026 [Dorm]. All rights reserved." | "Powered by NEST.PH Dormitory Management System" |

**How it works.** One piece of code in `AppServiceProvider` (a "view composer") hands the dorm's name and logo to **every** page, email and PDF automatically. If the dorm renames itself on the Dormitory Profile page, the whole system updates. If the database can't be reached, pages fall back to "NEST.PH" instead of breaking.

The team's table also had a "Settings > About" row. The app has no settings page, so the "Powered by NEST.PH" link in the sidebar does that job instead.

## 3. Dorm Logo upload

**Change.** A new **Dorm Logo** card on `/dormitory-profile`, just under Cover Photo. It shows:

- a preview of the current logo,
- a status line ("No dorm logo yet. The NEST.PH logo is shown in its place." or "Your logo is live across the system."),
- file guidance (square PNG, JPG or WEBP, up to 2 MB),
- a **Sidebar preview** showing exactly how the logo and name look in the green sidebar.

While uploading, the button says "Uploading..." and can't be clicked twice. After upload, the real sidebar updates straight away without a page reload.

**Behind it:** a new database column `brand_logo_path` and a new route `POST /dormitory-profile/brand-logo`. The cover photo is untouched.

## 4. Wording that treated NEST.PH as the dorm

| Where | Before | After |
|---|---|---|
| Lease contract | "NEST.PH Pureza Station Dormitory" (hand-typed) | The saved dorm name |
| Eviction notice | "your lease agreement with NEST PH", "owed to NEST PH", signed "NEST PH Management" | The dorm name in all three |
| Email subjects | "Your NEST.PH Password Reset Code" | "Your Pureza Station Dormitory Password Reset Code" |
| Email text | "Thank you for your interest in NEST.PH" | The dorm name |
| **Inquiry form privacy consent** | "I consent to NEST.PH collecting…" | "I consent to [Dorm] collecting … (processed through the NEST.PH platform)" |
| Tenant blacklist message | "your NEST.PH account" | "your [Dorm] tenant account" |
| Report CSV titles | "NEST.PH - Occupancy Report" | "[Dorm] - Occupancy Report (generated via NEST.PH)" |
| Announcements with no author | "NEST PH Admin" | "[Dorm] Admin" |

The inquiry consent was the most important fix: it asked people to consent to the wrong party under the Data Privacy Act.

## 5. Impeccable polish pass on the branding

**Request.** Run Impeccable on the new branding, make sure it passes the anti-AI-slop check and works on phones, and never cut off a long dorm name ("Pureza Station Do…").

**Changes.**

- **Long names wrap instead of being cut**, in the sidebar and the public top bar. Tested with a made-up 51-character name, "Pureza Station Student Residences and Dormitory Hall".
- **Bug found: the fallback logo was invisible.** The NEST.PH logo has two files: `nestph.png` is **white** (for green bars) and `nestphgreen.png` is **green** (for light backgrounds). The white one had been used everywhere, so it vanished on the Dorm Logo card, the login card, the browser tab and the 404 page. Light backgrounds now use the green file.
- The "Powered by" line in the sidebar was faint; it is now brighter, easier to tap on phones, and shows an outline when reached with the keyboard.
- The login "Powered by" text now meets the standard contrast guideline (WCAG 4.5:1).

**Result.** The design detector found no problems in the new work. The only flags were older, deliberate choices: the Roboto font (locked in PRODUCT.md) and the sidebar's collapse animation.

## 6. New tagline and the real NEST.PH logo

**Request.** Replace the drawn "N" block on the login page with the real NEST.PH logo, and replace "Study hard, make friends, and live your NEST life." with something short, catchy and dorm-related, local if possible.

**Change.**

- New tagline: **"Malayo sa bahay, / pero at home."** ("Far from home, but at home."). Casual Taglish, about why people live in a dorm.
- The drawn "N" is replaced by the real white NEST.PH logo. It first sat below the tagline and looked disconnected, so it now sits **above** the tagline at a smaller size, so the two read as one unit.
- **The old tagline was then removed completely:** from the two login pages, Forgot Password, Apply, Inquiry, the 5 move-in steps, the homepage tab title and PRODUCT.md. All of those pages now use the same logo-above-tagline layout.
- On phones the logo stays hidden so the form comes first.

Other tagline options, if the team wants to change it later (one-line change per page): "Your home near campus." / "Uwian mo, malapit lang."

## 7. Login pages that don't scroll

**Problem.** At 100% zoom the login page scrolled down a little. The page assumed the top green bar is always 70px tall; on many screens it is taller.

**Change.** On **both login pages and Forgot Password**:

- The page now fills exactly the space left under the top bar, however tall the bar is.
- Spacing shrinks on shorter screens (small laptops, zoomed-in browsers).
- The form is centred in its card instead of hugging the top.
- On phones and tablets, the card is only as tall as its content (no big empty card).
- On landscape tablets, the small logo above the tagline is hidden to save room.

**Measured** (the extra height a user would have to scroll):

| Screen | Login (both) | Forgot Password (all 3 steps) |
|---|---|---|
| 1920×870, 1536×730, 1440×900, 1366×650, 1280×600 | 0px | 0px |
| 1180×820, 1024×700, 768×1000 | 0px | 0px |
| Phones 390×844, 360×700 | 0px | 0px |
| 1024×600 (old netbook) | 37px | not measured |

On phones, scrolling while the on-screen keyboard is open is normal and expected.

## 8. Stronger password rule

**Request.** Passwords must have **at least 8 characters, 1 number, 1 symbol and 1 uppercase letter.**

**Change.**

- The rule is set **once** in `AppServiceProvider` (Laravel's `Password::defaults()`), so every place that uses the default picks it up. Laravel only has a built-in "upper and lowercase" option, so a small new rule, `App\Rules\HasUppercase`, checks for just an uppercase letter.
- **Where it applies:** Forgot Password (the reset step used to check only for 8 characters), the tenant Change Password tab, the `nestph:create-owner` setup command, and Laravel's built-in register/reset pages (unused, but covered).
- **Temporary passwords** (new admin, new tenant, approved application) used to be random letters and numbers, which the new rule would reject. A new generator, `App\Support\TemporaryPassword`, always includes an uppercase letter, a number and a symbol, e.g. `VC*9tU&pae*Q`. It skips look-alike characters (0/O, 1/l/I) because people type these from an email.
- **Live checklist:** under the "New Password" box on Forgot Password and the tenant Change Password tab, each requirement ticks green as the user types.

**Checked:** weak samples ("password", "Password1", "password1!", "Password!") are each rejected with a clear message; "Password1!" is accepted; 2,000 generated temporary passwords all passed.

**Not changed:** existing accounts keep their current passwords. The rule applies only when a password is set or changed, so the `password123` test accounts still log in.

## 9. Database changes

| Migration | What it does |
|---|---|
| `2026_09_29_000001_add_brand_logo_path_to_dormitory_profile_table` | Adds `dormitory_profile.brand_logo_path` (text, can be empty), the Dorm Logo file |

One new route: `POST /dormitory-profile/brand-logo`.

## 10. Files changed

**New files**

- `app/Rules/HasUppercase.php`: the "must have an uppercase letter" check
- `app/Support/TemporaryPassword.php`: temporary passwords that meet the rule
- `database/migrations/2026_09_29_000001_add_brand_logo_path_to_dormitory_profile_table.php`
- `resources/views/partials/sidebar-brand.blade.php`: dorm logo and name at the top of every sidebar
- `resources/views/partials/powered-by.blade.php`: "Powered by NEST.PH" at the bottom of every sidebar
- `resources/views/partials/password-rules.blade.php`: the live password checklist
- `resources/views/emails/partials/header.blade.php` and `footer.blade.php`: shared email header and footer
- `tests/Unit/PasswordPolicyTest.php`: 6 tests for the password rule

**Backend**

- `app/Providers/AppServiceProvider.php`: shares the dorm's name and logo with every page; sets the password rule
- `app/Models/DormitoryProfile.php`: `brand_logo_path` and `brandLogoUrl()`
- `app/Http/Controllers/DormitoryProfileController.php`: `uploadBrandLogo()`
- `routes/web.php`: the Dorm Logo route
- `app/Http/Controllers/Auth/PasswordResetCodeController.php`: uses the new rule
- `app/Http/Controllers/AdminPrivilegeController.php`, `TenantController.php`, `Api/ApplicationController.php`: use the new temporary-password generator
- `app/Console/Commands/CreateOwner.php`: checks the new rule
- `app/Http/Controllers/ReportController.php`: CSV titles use the dorm name
- `app/Models/Announcement.php`, `AnnouncementComment.php`: "[Dorm] Admin" fallback
- `app/Mail/*.php` (all 9): subject lines use the dorm name

**Pages, emails and PDFs**

- Sidebars: `partials/admin-sidebar`, `partials/tenant-sidebar`, `partials/tenant-sidebar-restricted`; styles in `public/css/admin.css` and `public/css/tenant.css`
- Public top bar: `partials/public-nav`
- Login and password pages: `logintenant`, `loginadmin`, `passwords`
- Tagline pages: `publicapply`, `publicinquiry`, the 5 `tenantmovein*` pages, `partials/movein-styles`, `welcome`
- Dorm Logo card: `admindormitoryprofile`
- Tab titles and icons: every page with a `<title>` (38 files)
- Other text: `tenantdelinquency`, `tenantaccount`, `activitylog`, `dashboard`, `tenantmoveout`, `partials/nav`, `errors/404`
- All 9 email templates in `resources/views/emails/`
- PDFs: `pdfs/lease-contract`, `pdfs/eviction-notice`, `pdfs/demand-letter`
- `public/favicon.ico`: now the NEST.PH logo (was an empty file)
- `PRODUCT.md`: new official tagline

**Tests updated for the new rule:** `PasswordResetTest`, `PasswordUpdateTest`, `CreateOwnerCommandTest` (they used example passwords the rule now rejects; they still test the same things).

## 11. Testing checklist

Already verified in this session:

- [x] `php artisan test`: 31 passed, 0 failed (6 new)
- [x] Every page template compiles (`php artisan view:cache`)
- [x] Screenshots at desktop and phone width: sidebar with a long name, Dorm Logo card, both login pages, Forgot Password (all steps)
- [x] No-scroll layout measured at 10–13 window sizes (see section 7)
- [x] The password rule rejects weak samples; 2,000 temporary passwords all pass
- [x] The eviction notice PDF generates without errors

Still to check by hand:

- [ ] Upload a real dorm logo and look at: sidebar, login page, browser tab, and a test email
- [ ] Open a generated **lease contract** and **demand letter**: the new footer should sit neatly at the bottom of each page
- [ ] Open a test email in a real mail inbox (Mailtrap) to see the new header and footer
- [ ] The 5 move-in pages with a tenant who is partway through moving in
- [ ] Apply and Inquiry pages: logo above the tagline
- [ ] Change a password on the tenant Change Password tab and watch the checklist tick

## 12. Open items

**What you need to decide (for BAGUI):**

1. **Lease contract clause 8.2 (data privacy consent).** It still doesn't say that NEST.PH processes data on the dorm's behalf. Someone should review this legal wording before the defense; the developer deliberately didn't write legal text.
2. **Email "From" name.** It comes from `MAIL_FROM_NAME` in the `.env` file. If it says NEST.PH, consider changing it to the dorm's name.
3. **Existing weak passwords.** Accounts with old passwords aren't forced to change them. Forcing a change at next login is possible but a bigger change. (Less important if the old test accounts are deleted, as planned.)
4. **Temporary passwords are never forced to change.** New admins, tenants and approved applicants keep their emailed temporary password until they change it themselves. Should they be required to change it at first login?

**Left as NEST.PH on purpose:** the "About NEST.PH" section on the homepage (platform marketing) and the downloaded PDF file names (e.g. `NEST-PH-Lease-Contract-Preview.pdf`).

**Next up (planned):** a demo-data seeder with about 20 tenants with realistic Filipino details, 3 admins, the kept Owner account, and matching floors, rooms and beds. Rooms that have photos or VR tours should be kept, because deleting a room also deletes its photos and VR scenes.
