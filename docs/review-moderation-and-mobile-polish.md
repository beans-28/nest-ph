# Review Moderation and Mobile Polish: What Changed

This document explains the work done in this round, in the order it happened:

1. [Review Moderation (backend)](#1-review-moderation-backend)
2. [Review Moderation card (admin UI)](#2-review-moderation-card-admin-ui)
3. [Mobile fixes for every page](#3-mobile-fixes-for-every-page)
4. [Public site polish](#4-public-site-polish)
5. [Tenant portal redesign](#5-tenant-portal-redesign)
6. [Move-out review page](#6-move-out-review-page)
7. [How to test everything](#7-how-to-test-everything)
8. [Rules we followed (and why)](#8-rules-we-followed-and-why)

After pulling these changes, run:

```bash
php artisan migrate
php artisan view:clear
```

Then hard-refresh the browser (Ctrl+F5), because the CSS and JS files are cached.

---

## 1. Review Moderation (backend)

**The problem:** tenants who move out can leave a review, and reviews show publicly on the Dorm Info page. Nothing stopped a review with swearing, a phone number, or a spam link from going public.

**The solution:** every new review is scanned automatically. Anything suspicious is **hidden** and waits for an admin to decide.

### Review statuses

| Status | Meaning | Shown publicly? |
|---|---|---|
| `published` | Normal, visible review | Yes |
| `hidden` | Auto-flagged by the filter, or hidden by an admin | No |
| `removed` | An admin removed it permanently | No |

### Files

| File | What it does |
|---|---|
| `database/migrations/2026_09_24_130732_add_moderation_columns_to_reviews_table.php` | Adds `status`, `flag_reasons`, `moderated_by`, `moderated_at` and `moderation_note` to the `reviews` table. Existing unapproved reviews become `hidden`. |
| `config/review_moderation.php` | The list of blocked words (English, Filipino/Tagalog, Bisaya). Add or remove words here. |
| `app/Services/ReviewModerationService.php` | The filter itself (details below). |
| `app/Http/Controllers/ReviewModerationController.php` | The admin actions: Publish, Hide, Remove and Re-scan. |
| `app/Models/Review.php` | Status labels (`Public`, `Hidden`, `Removed`), `moderationSummary()` (the one-line history under each review), and a hook that keeps the old `is_approved` column in sync. |
| `app/Http/Controllers/ReviewController.php` | Runs the filter right after a tenant submits a review. |
| `app/Http/Controllers/DormitoryProfileController.php` | Sends the reviews and their counts to the Dormitory Profile page. |
| `routes/web.php` | New routes (below). |

### What the filter catches

- **Blocked words** from the config file. Matching is whole-word, so "class" does not match "ass". It also sees through tricks like `g4g0`, `sh!t` and `gaaaago`. "tang ina" also catches "tangina" and "tang-ina".
- **Links**, such as `http://`, `www.` or `something.com`. ".ph" is ignored on purpose, so "NEST.PH" is not flagged.
- **Email addresses**
- **Phone numbers** (10 or more digits)
- **Spam patterns:** the same character 6+ times in a row (`!!!!!!`), or one word repeated through most of the comment.

### Important behavior

- **An admin's decision always wins.** Once an admin has published, hidden or removed a review, the filter never changes it again, not even on Re-scan.
- **`is_approved` still works.** The homepage rating, the rating breakdown and the Dorm Info list all read `is_approved`. The Review model now sets it from `status` automatically on every save, so none of those queries had to change. **Never set `is_approved` by hand.**
- **The tenant is told.** If a new review gets hidden, the tenant sees: "It will appear publicly once an administrator has checked it."

### Routes

| Method | URL | Action |
|---|---|---|
| PATCH | `/dormitory-profile/reviews/{review}/publish` | Make public (also used by Restore) |
| PATCH | `/dormitory-profile/reviews/{review}/hide` | Hide from the public page |
| PATCH | `/dormitory-profile/reviews/{review}/remove` | Remove permanently, with an optional reason (`note`) |
| POST | `/dormitory-profile/reviews/rescan` | Run the filter again on every review an admin hasn't decided on yet |

---

## 2. Review Moderation card (admin UI)

**Where:** Admin, then **Dormitory Profile**, in the card above "Legitimacy Documents".
**File:** `resources/views/admindormitoryprofile.blade.php`, in three separate parts:
- the `.rv-*` CSS rules at the end of the `<style>` block
- the card's HTML (`id="reviewModerationCard"`)
- its **own separate `<script>` block** at the end of the file

### What the admin can do

- **See a summary:** the public rating and how many public reviews it comes from.
- **Filter by status** with tabs: All, Public, Hidden, Removed. Each tab shows a count.
- **Search** by tenant name or comment text, and **filter by star rating**.
- **Act on each review.** Only the buttons that make sense for its status are shown:

| Review status | Buttons shown |
|---|---|
| Public | Hide, Remove |
| Hidden | Publish, Remove |
| Removed | Restore |

Remove asks for an optional reason. The line under each review explains what happened, for example "Auto-flagged: Contains a phone number." or "Removed by Admin on Sep 24, 2026. Reason: spam".

**Re-scan Reviews** runs the filter again. Use it after editing the blocked-word list.

### Polish applied (after an audit)

- **Keyboard focus:** a green outline shows on tabs and buttons when you move with the Tab key.
- **Screen readers:**
  - Tabs announce whether they are selected (`aria-pressed`).
  - Stars read as "Rated 4 out of 5" (`role="img"`).
- **Readable colors:** "Hidden" is dark bold text, and "Removed" is grey. The old amber and red were too faint to read.
- **Hidden reviews stand out:** the Hidden tab turns bold when reviews are waiting for a decision.
- **Clear filters:** the search and rating fields have visible labels, and sit on their own row under the tabs.
- **Empty states:** "No reviews yet." when there are no reviews, and "No reviews match this view." when filters hide everything.
- **Grammar:** "1 public review" (singular) and "1 star" / "2 stars".
- **Focus after an action:** keyboard focus moves to the next review instead of jumping to the top of the page.
- **Scrolling:** removed the list's own scrollbar, so it scrolls with the page like the other cards.
- **Phones:** the header and filters stack cleanly at 640px wide.

Review text is only ever inserted with Blade `{{ }}` or JavaScript `textContent`, never `innerHTML`, so a review cannot inject HTML or scripts.

---

## 3. Mobile fixes for every page

### The sidebar is now a slide-in drawer on phones (admin and tenant)

**Before:** on phones, the sidebar stayed on screen and took up half the width.
**Now (860px wide and below):** the sidebar is hidden. Tapping the hamburger slides it in over the page with a dimmed background. It closes when you tap outside it, press Escape, or pick a link.

| File | What it does |
|---|---|
| `public/js/sidebar-drawer.js` (new) | Opens and closes the drawer. It catches the hamburger tap **before** the page's own script, so no page script had to be edited. Desktop collapse still works and is still remembered. |
| `resources/views/partials/admin-sidebar.blade.php`, `tenant-sidebar.blade.php`, `tenant-sidebar-restricted.blade.php` | Each loads the script above with one line at the end. |
| `public/css/admin.css`, `public/css/tenant.css` | A "Mobile (860px and below)" section at the end of each file. |

The top bar stays pinned at the top while scrolling on every screen size.

### Shared admin phone layouts (in `admin.css`, 640px and below)

- **Stat cards:** two per row instead of one tall stack.
- **Tab rows:**
  - The tabs sit in one row you can swipe sideways.
  - The action buttons (such as Billing's "+ Record Payment Entry") move above the tabs and fill the width.
- **Filters:** the search box is full width, the dropdowns share one row, and buttons are full width.
- **Tables:** they scroll sideways inside their white panel instead of making the whole page scroll sideways.
- **Modals:** two-column form rows stack into one column.

### Page-specific fixes

| Page | Fix |
|---|---|
| Inquiries | Each inquiry row wraps onto two lines instead of cramming five items into one. |
| VR Management | Form fields fill the width, and settings stack. |
| Activity Log | Tighter padding. The table scrolls sideways instead of crushing the Details column. |
| Dormitory Profile | A long file name or email no longer makes the whole page scroll sideways (details below). |
| Tenant Dashboard | "Recent Billing" and "My Tickets" now stack on phones (details below). |

### Bugs found along the way

1. **Tickets, Inquiries and Applications scrolled sideways, and the sidebar moved with them.**
   - `admin.css` had a rule for `.badge` (meant for a small notification dot) that included `position:absolute`.
   - Those three pages also use `.badge` for their status labels, so the labels escaped their cards and pushed the page wider than the screen.
   - **Fix:** the rule is now `.topbar-icon .badge`, so it only applies inside a top-bar icon.

2. **Dormitory Profile scrolled sideways on phones.**
   - Grid columns grow to fit their longest unbreakable word by default, so one long file name made the column 850px wide.
   - **Fix:** `min-width:0` on the grid columns, and `overflow-wrap:anywhere` so long text wraps.

3. **Phone rules that never worked.**
   - On the Tenant Dashboard and in the Apply page's contract preview, a phone rule was written *before* the normal rule it was meant to override.
   - In CSS, when two rules conflict, the later one wins, so the phone rule was always cancelled.
   - **Fix:** the phone rules were moved after the rules they override.

---

## 4. Public site polish

### One shared top navigation

The public nav was copied into 9 pages: Home, Rooms, Dorm Info, VR Tour, Inquire, Apply, Tenant Log In, Admin Log In and Password Reset. It now lives in **one** file:

`resources/views/partials/public-nav.blade.php`, included with `@include('partials.public-nav')`

To change the nav, edit only this file.

- **Phones (860px and below):**
  - The nav is one slim row (about 58px, down from about 180px): logo, an **Apply** button and a **Menu** button.
  - Menu opens the page links, plus Log In and Admin.
  - It closes when you tap Menu again, tap outside it, or press Escape.
- **All sizes:**
  - The current page is marked: underlined links, or a white-filled "About the Dorm" pill on desktop.
  - The NEST.PH logo links to Home.
- **Desktop:** looks the same as before.

### Forms appear sooner on phones

On Inquire, Apply, Tenant Log In, Admin Log In and Password Reset, the form used to start about 700px down the page. There was a full-height layout gap, plus the decorative "N" logo shape.

On phones, the gap is removed and the decorative shape is hidden, so the form starts on the first screen. The tagline stays.

---

## 5. Tenant portal redesign

**Goal:** a cozy, homey, modern look with one consistent green.

### One color set for all tenant pages

All tenant greens now come from one set of color variables at the top of `public/css/tenant.css`:

| Variable | Color | Used for |
|---|---|---|
| `--sage-900`, `--sage-800` | `#1f4630`, `#27573a` | Sidebar |
| `--sage-700` | `#2f6a46` | Top bar, hover states |
| `--sage-600` | `#3a7a52` | Main green: buttons, the Balance Due card, icons |
| `--sage-100`, `--sage-50` | `#e5f1e2`, `#f2f7ef` | Soft panels, "Paid" labels, highlights |
| `--cream` | `#fbf8f0` | Page background |

Old variable names like `--green-dark` and `--green-btn` still work. They now point to these colors.

**Rule for the future:** in tenant pages, use these variables. Don't write new hex greens like `#2f6f3c`. Each tenant page's own greens were already replaced with them.

Status colors (blue Open, purple Seen, amber Pending, red Overdue) and the GCash/BDO brand colors are unchanged, because each of those colors means something.

### What changed visually

- **Top bar:** a flat forest green with a faint leaf texture, matching the public site. Before, it was a washed-out translucent strip.
- **Your name:** shown in a rounded pill next to the avatar.
- **Sidebar:** the current page is a rounded highlighted block with a leaf-green icon, instead of a thick white stripe.
- **Page headers:** bigger titles, and the back arrow is now a proper button.
- **Background:** warm cream, which gives the cozy feel.
- **Announcements feed:** this file is shared with the admin dashboard, so it's re-tinted from `tenant.css` only. The admin dashboard looks the same as before.

---

## 6. Move-out review page

**URL:** `/moved-out`. This is the only page a moved-out tenant can reach.
**File:** `resources/views/tenantmoveout.blade.php`

**Tip for testing:** any logged-in tenant can open `/moved-out` to see the page. Submitting a review only works for a real moved-out tenant, because the backend refuses the others.

### What changed

- **Matching look:** the page uses `tenant.css`, so it has the same colors and top bar as the rest of the tenant portal.
- **The review is the focus:** a "How was your stay?" section with five large stars. Tapping a star opens the review form with that rating already picked.
- **Review form:**
  - A word appears next to the stars (Poor, Fair, Good, Very good, Excellent).
  - The comment box has a visible label and a 0 / 1000 character counter.
  - The submit button shows "Submitting..." while saving.
  - It closes when you tap outside it or press Escape.
  - The page behind doesn't scroll while it's open.
  - Keyboard focus stays inside the form.
- **Phones:**
  - The form slides up from the bottom of the screen.
  - Stars are large enough to tap easily.
  - The comment box uses 16px text, so iPhones don't zoom in.
  - Buttons are full width.
  - Your name and Log Out fit in the top bar, and long names get "…".
- **After submitting:** filled stars and "Thanks for your review!". It looks the same after reloading.
- **Contact card:** the dorm's email and phone number are tappable links.

The backend is unchanged: the same `reviews.store` route receives the same data.

---

## 7. How to test everything

Use Chrome DevTools (F12, then the phone icon) to test phone sizes, or use a real phone.

**Review moderation (admin, Dormitory Profile)**
- [ ] As a moved-out tenant, submit a review containing a blocked word or a phone number. It shows as **Hidden**, with the reason.
- [ ] As an admin, try Publish, Hide, Remove (with a reason) and Restore. Check that the counts and average update.
- [ ] Tabs, search and the rating filter all work.
- [ ] Re-scan Reviews shows a message, then the page reloads.

**Admin pages on a phone**
- [ ] The hamburger slides the menu in, and tapping outside closes it.
- [ ] The top bar stays at the top while scrolling.
- [ ] No page scrolls sideways. Check Tickets, Inquiries, Applications and Dormitory Profile especially.
- [ ] Wide tables swipe sideways inside their panel.

**Public pages on a phone**
- [ ] The nav is one slim row. Menu opens and closes.
- [ ] The forms on Inquire, Apply and Log In start on the first screen.

**Tenant pages**
- [ ] The greens match on every page, including the Balance Due card.
- [ ] `/moved-out` on a phone: tap a star, and the form opens with that rating picked.

**Every page**
- [ ] F12 Console has no red errors.
- [ ] The house rules, amenities and upload buttons still work.

---

## 8. Rules we followed (and why)

- **Keep new JavaScript in its own separate `<script>` block (an IIFE).** Past "every button stopped working" bugs came from editing shared script blocks. The review card, the sidebar drawer, the public nav and the move-out page each have their own.
- **Never reuse a `const` name that another script block on the same page already uses.**
- **Insert user text only with `{{ }}` or `textContent`, never `innerHTML`.** This stops HTML or scripts hidden inside a review or name from running.
- **Mind CSS rule order.** When two rules conflict, the later one wins. A phone rule written before the normal rule it overrides will never work. Put `@media` rules after the base rules.
- **Reuse shared files.** Admin shell styles go in `admin.css`, tenant styles and colors in `tenant.css`, the public nav in `partials/public-nav.blade.php`. Fix something once, and it's fixed everywhere.
- **Copy rules:** no em dashes, visible labels instead of grey placeholder text, and short, plain wording.
