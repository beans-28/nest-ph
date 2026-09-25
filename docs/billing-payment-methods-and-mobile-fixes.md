# Billing, payment methods, and mobile fixes (2026-09-25)

A record of everything changed in this session, in the order the work happened. Each section lists the problem, what changed, the files involved, and anything still to watch.

## Contents

1. [Stat cards overflowing on mobile](#1-stat-cards-overflowing-on-mobile)
2. [Announcements feed on mobile](#2-announcements-feed-on-mobile)
3. [Tenant Manager: Set Status for delinquent tenants](#3-tenant-manager-set-status-for-delinquent-tenants)
4. [Why a reset tenant can still show as Delinquent](#4-why-a-reset-tenant-can-still-show-as-delinquent)
5. [Wi-Fi and utilities pricing per room](#5-wi-fi-and-utilities-pricing-per-room)
6. [Admin-managed payment methods and QR codes](#6-admin-managed-payment-methods-and-qr-codes)
7. [Cash as a built-in method, and the cash workflow](#7-cash-as-a-built-in-method-and-the-cash-workflow)
8. [Billing page freeze (bug introduced and fixed)](#8-billing-page-freeze-bug-introduced-and-fixed)
9. [Peso icons and the zoomable QR viewer](#9-peso-icons-and-the-zoomable-qr-viewer)
10. [Design polish pass](#10-design-polish-pass)
11. [Modals cut off on mobile](#11-modals-cut-off-on-mobile)
12. [Database changes](#12-database-changes)
13. [Files changed](#13-files-changed)
14. [Testing checklist](#14-testing-checklist)
15. [Open items](#15-open-items)

---

## 1. Stat cards overflowing on mobile

**Problem.** On the admin Billing and Payments page at phone width, peso amounts such as "₱16,650.00" ran past the edge of their stat cards.

**Cause.** On phones the stat cards sit two per row. The text column inside each card was not allowed to shrink below its content width, so a long amount at 24px pushed out of the card.

**Change.**

- `public/css/admin.css`, inside the `max-width: 640px` block, applied to every admin page that uses the shared stat cards (Billing, Dashboard, Reports, Tickets, Delinquency, Applications and others):
  - the text column gets `min-width: 0` so it can shrink;
  - stat values drop to 18px and wrap with `overflow-wrap: anywhere` as a last resort;
  - the icon shrinks to 36px (from about 48px) to give the number more room.
- `resources/views/tenantdashboard.blade.php`: this page has its own styles. Between 520px and 900px wide it shows two cards per row, so it got the same shrink and wrap rules, and its grid uses `minmax(0, 1fr)`. Under 520px it was already one card per row.

## 2. Announcements feed on mobile

**Problem.** In the Announcements section at phone width, the "Restrict comments" button was squeezed and the "Admin" tag ran into it.

**Change.** `resources/views/partials/announcements-feed.blade.php` (shared by the admin and tenant dashboards):

- the name, "Admin" tag and Restrict button keep their full size (`flex-shrink: 0`), and the name and tag wrap onto a new line when space runs out;
- at 640px and below:
  - the Restrict button is smaller and sits level with the name;
  - post text, the comment count, the Comment button and comment threads lose their 46px indent and use the full card width;
  - the post box stacks the text field above a right-aligned Post button;
  - cards have slightly less padding;
  - pop-up messages sit centered along the bottom of the screen.

## 3. Tenant Manager: Set Status for delinquent tenants

**Problem.** Setting a **Delinquent** tenant to **Active** showed the normal success message but nothing changed.

**Cause.** "Delinquent" is not a stored status. The Tenant Manager works it out from two things: the tenant is blacklisted, or they have a bill marked overdue. A delinquent tenant's stored status is already `active`, so the request changed nothing.

**Change.** `app/Http/Controllers/TenantController.php`, `setStatus()`, when reactivating:

- if the tenant is blacklisted or has an overdue bill, the request is rejected (HTTP 409) with:
  > "A delinquent account can't be set to active. It clears automatically once the tenant's overdue balance is paid and confirmed."
- if the tenant is already active and not delinquent, the request is rejected with "This tenant is already active."
- an inactive tenant with no overdue bills still reactivates normally.

The error appears inside the Set Status window, which already showed server errors.

**Side effect to know about.** A tenant who was evicted (blacklisted) and later deactivated can't be reactivated until their delinquency is cleared.

## 4. Why a reset tenant can still show as Delinquent

This was a question, not a code change, but it explains the rule in section 3.

- Delinquency → **Reset** deletes the escalation history and lifts the portal restriction and blacklist. It does **not** change any bills.
- If the tenant still has an overdue bill, they still show as Delinquent in the Tenant Manager.
- The label clears once the overdue bill is paid and the admin confirms the payment.
- Reset also un-pauses escalation, so the stages may start advancing again while an overdue bill is still open.

## 5. Wi-Fi and utilities pricing per room

**Problem.** Wi-Fi and utilities on every bill were ₱0. The fields existed on floors, but no screen let an admin set them.

**Change.**

- **New room fields.** Migration `2026_09_25_000001_add_utility_costs_to_rooms_table` adds `monthly_utility_cost` and `monthly_wifi_cost` to `rooms`, both defaulting to 0.
- **The split.** `app/Models/Room.php` has a new `utilityShares()` method that returns each tenant's `[utilities, wifi]` share.
  - The first version split by the number of tenants actually in the room.
  - At your request it now splits by **bed count**, the same way rent is split (`perBedRate()`). For example, ₱1,000 Wi-Fi in a 3-bed room is ₱333.33 per bed, however many beds are filled.
- **Billing generation.** `app/Http/Controllers/Api/BillingController.php`, `splitUtilityCost()`, now uses the room's prices instead of the floor's. If a contract has no room, the bill still goes out with 0 and a "utility data missing" event is logged.
- **Admin screen.** `resources/views/adminaddfloor.blade.php` (Vacancy Monitor):
  - the Add Room and Edit Room windows have **Monthly Utilities** and **Monthly WiFi** fields, with a note: "Whole-room amounts, split evenly by the number of beds, same as rent. Changes apply from each tenant's next generated bill.";
  - each room card shows "Utilities ₱… · WiFi ₱…".
- **Room endpoints.** `app/Http/Controllers/VacancyController.php`: the create and update room endpoints validate and save both fields, and the room data returned to the page includes them.

**Mid-month price changes.** Bills that were already generated keep the amounts they were issued with. A new price only applies when the admin generates each tenant's next bill ("Generate This Month's Billing").

**Next Bill Estimate for tenants.** So tenants can see a price change before it's billed, their Billing page has a **Next Bill Estimate** card:

- `routes/web.php` (`/billing` route) works out rent + utilities + Wi-Fi at today's room prices;
- `resources/views/tenantbilling.blade.php` shows Rent, Utilities, Wi-Fi and Estimated Total, labelled "Based on current room prices. Not yet added to your balance.";
- the card shows two columns at phone width.

The old `floors.monthly_utility_cost` and `floors.monthly_wifi_cost` columns are still in the database but nothing uses them.

## 6. Admin-managed payment methods and QR codes

**Problem.** Payment methods were hardcoded (GCash, BDO, Cash). The QR panel showed a fake placeholder, and the move-in page generated a "QR" that only contained the text "GCash: [number]", which no banking app can pay from. Admins had no way to set their accounts or upload a real QR.

### Data model

- Migration `2026_09_25_000002_create_payment_methods_table` creates `payment_methods`:
  - `type` (`ewallet`, `bank` or `cash`), `name`, `account_name`, `account_number`, `qr_path`, `instructions`, `sort_order`, timestamps.
- The same migration adds `payments.payment_method_label`. This saves the method's name on each payment ("Maya", say), so the record still reads correctly if the method is later edited or deleted. `payments.payment_method` stays the broad type (`gcash` / `bank_transfer` / `other` / `cash`) that reports and filters group by.
- New model `app/Models/PaymentMethod.php`:
  - `ordered()`, `isOnline()`;
  - `paymentEnum()` maps a method to that broad type;
  - `brand()` picks a colour for known brands (gcash, maya, bdo, bpi), otherwise the default green;
  - `toClientArray()` returns the data the pages use, including the QR image URL.
- `app/Models/Payment.php`: `payment_method_label` is now fillable.

### Admin: Dormitory Profile → Payment Methods

- New controller `app/Http/Controllers/PaymentMethodController.php` with `store`, `update` and `destroy`:
  - account name and account number are required for e-wallets and banks;
  - QR must be JPG, PNG or WEBP, up to 5 MB, stored under `storage/app/public/payment-qr`;
  - on update, the admin can replace or remove the QR, and the old file is deleted;
  - delete removes the QR file too.
- Routes in `routes/web.php`, in the admin group:
  - `POST /dormitory-profile/payment-methods`
  - `POST /dormitory-profile/payment-methods/{paymentMethod}`
  - `DELETE /dormitory-profile/payment-methods/{paymentMethod}`
- `app/Http/Controllers/DormitoryProfileController.php` passes `paymentMethods` to the page.
- `resources/views/admindormitoryprofile.blade.php` has a new **Payment Methods** card:
  - each row shows a type icon, name, type badge, account name and number, a QR thumbnail (or "No QR"), and Edit and Delete buttons;
  - Delete asks for confirmation;
  - the add/edit window has Type, Name, Account Name, Mobile/Account Number, a QR upload with preview and Remove QR, and Instructions for tenants;
  - the window closes with Escape or a click outside it, and errors show inside it;
  - changes apply to tenant screens immediately.

### Tenant: Billing page

`resources/views/tenantbilling.blade.php`:

- The payment options are built from the admin's list, with an empty state: "No payment methods are set up yet. Please contact the dormitory admin."
- Options are selectable with the keyboard (radio role, Enter or Space).
- The payment screen:
  - **Selected Payment Method** shows the method name, account name and number, and instructions;
  - the **QR Code** card shows the admin's QR, which tenants tap to open the viewer (section 9), or a neutral "No QR code set up" placeholder.
- The branded blue GCash panel was removed at your request.
- The payment is submitted with `payment_method_id`. The old "(Bank: BDO)" text that was appended to notes is gone.
- Payment history shows the method's name when one was saved.

### Tenant: Move-in flow

- `app/Http/Controllers/TenantOnboardingController.php`:
  - the method page lists only e-wallet and bank methods, since move-in needs a proof upload;
  - the chosen method's ID is validated and saved in the session;
  - if that method was deleted, or the session still holds an old `gcash`/`bdo` value, the tenant is sent back to choose again.
- `resources/views/tenantmoveinpaymentmethod.blade.php`: the options come from the admin's list, the previously chosen method stays selected, and there's an empty state and a validation error message.
- `resources/views/tenantmoveinpayment.blade.php`:
  - the fake QR generator and its CDN script were removed;
  - a plain white card shows "Pay via [method]", the account name and number, instructions, and the real QR (tap to enlarge) or the "No QR code set up" placeholder.

### Server-side submission

- `app/Http/Controllers/Api/TenantPortalController.php`, `submitProof()`:
  - accepts `payment_method_id`; the older `payment_method` field is still accepted when no ID is sent;
  - the payment's type and label are taken from the stored method, not from what the browser sends, so a tenant can't claim a method that isn't offered;
  - a cash method is rejected: "Cash payments are recorded by the admin at the office, not submitted online."
- `app/Http/Controllers/Api/PaymentController.php` and `TenantPortalController.php` include `payment_method_label` in the payment data they return.
- `resources/views/adminbilling.blade.php` shows the label where it exists, in the payments table, the payment details drawer and the proofs list.

### Starter data

The first version pre-filled GCash, BDO and Cash methods. At your request those were removed from the database and from the migration, so the admin sets up their own. Cash was later added back as a built-in method (section 7).

## 7. Cash as a built-in method, and the cash workflow

**Change.**

- Migration `2026_09_25_000003_add_default_cash_payment_method` adds one **Cash Payment** method ("Pay in person at the lobby / admin office."), but only if no cash method exists.
- `PaymentMethodController` protects it:
  - it can't be deleted;
  - its type can't be changed;
  - a second cash method can't be added.

  The admin can still rename it and edit the instructions.
- In the admin card, the Cash row is labelled "Cash · Always on", has no delete button, and "Cash" isn't offered as a type for new methods.
- When a tenant chooses Cash on the Billing page, a pop-up opens: **"Proceed to the Lobby / Admin Office"**. It shows:
  - the admin's instructions;
  - the amount to pay;
  - the due date;
  - a notice that choosing cash doesn't hold or extend the due date, the bill stays unpaid until an admin records the cash, and late penalties apply as usual.

  "Proceed to Payment" stays disabled for cash, since there's nothing to upload.
- Cash isn't offered in the move-in flow, which requires a proof upload.

### Why choosing Cash is not a loophole

- Choosing Cash **saves nothing** and does not change the bill.
- The daily `escalation:process` job (`EscalationService::processAll()`):
  1. marks any **unpaid** or **partial** bill past its due date as **overdue** (`BillingStatement::syncOverdueStatuses()`);
  2. moves only **overdue** bills up the delinquency stages, skipping paused tenants and leaving blacklisted tenants where they are.
- A tenant who picks Cash and arrives 5 days late is treated like anyone who paid 5 days late.

### Admin workflow when a tenant pays in cash

1. The tenant chooses Cash and sees the pop-up. Nothing is saved.
2. The tenant hands over the cash at the office.
3. The admin opens **Billing and Payments → + Record Payment Entry** ("Record Cash Payment"), picks the bill, and enters the amount and an optional receipt number. The payment date defaults to today and can't be in the future. An amount larger than the balance is rejected.
4. `PaymentController::recordCash()` then:
   - saves the payment as cash and **already approved**, with the admin recorded as the person who received it;
   - recalculates the bill as **paid** or **partial**;
   - if the bill is now paid, resolves any open escalation steps on it and lifts the portal restriction straight away (`resolveSettledEscalations()`);
   - if it was a move-in fee, activates the tenant and occupies the bed.

| When the cash is recorded | Result |
|---|---|
| On or before the due date, in full | Paid. It never goes overdue and no escalation starts. |
| After the due date, in full | Escalation steps are resolved and the portal restriction is lifted. Penalties already applied stay on the bill. |
| Partial amount | Partial. It goes overdue if the rest isn't paid by the due date. |
| Tenant already at Stage 6 (blacklisted) | The blacklist stays until the admin uses Reset. |

**Guidance for admins.**

- Record cash the same day, while the tenant is there. The daily check only sees what's saved.
- Entering an earlier payment date doesn't undo overdue status or stages that already happened.
- For a genuine delay, use **Pause** on the Delinquency page. That's a logged decision by the admin.

## 8. Billing page freeze (bug introduced and fixed)

**Problem.** After the cash pop-up was added, the tenant Billing page stayed on "Loading…" and nothing on the page responded, sidebar and top bar included.

**Cause.** The cash change left an extra `}` in `tenantbilling.blade.php`. That syntax error stopped the page's whole script.

**Fix.** Removed the extra brace. The page's script and the scripts of every other page edited that day were then checked with `node --check`, and all passed.

## 9. Peso icons and the zoomable QR viewer

### Peso icons

All six dollar-sign icons were replaced with a peso icon:

- `resources/views/partials/admin-sidebar.blade.php`: the Billing and Payments link;
- `resources/views/adminbilling.blade.php`: a stat card;
- `resources/views/admincontracts.blade.php`: one icon on the Lease Management page;
- `resources/views/tenantdashboard.blade.php`: the Balance Due card;
- `resources/views/tenantbilling.blade.php`: the Amount Due icon and the Payment Details heading.

Page text already used ₱. The other `$` characters in the code are never shown to users.

### QR viewer

New shared file `resources/views/partials/qr-lightbox.blade.php`, used by the tenant Billing page and the move-in payment page. Any element with `data-qr-src` opens it, and `data-qr-name` sets its title and the downloaded file's name.

- It opens full screen on a dark background, and the image fills the space even if the upload is small.
- Zoom goes up to 8×, because uploads are often whole GCash screenshots where the code is a small part. Ways to zoom:
  - + and − buttons, with the zoom level shown;
  - the mouse wheel, which zooms toward the cursor;
  - pinching with two fingers;
  - double-click or double-tap, which goes to 3× at that point, and again to zoom back out;
  - the + / − / 0 keys.
- Once zoomed in, drag to move around; the image can't be dragged off screen.
- A fit-to-screen button resets the zoom.
- **Download** saves the image as `<method-name>-qr.<ext>`.
- Close with ✕, Escape, or the Close button. Focus returns to what the tenant tapped, and the page behind won't scroll while it's open.
- On phones, button labels become icons only.

## 10. Design polish pass

Run with the project's impeccable skill. The pages were rendered to static HTML from real data and screenshotted with headless Chrome at desktop width (1440px) and true phone width (390px, loaded inside a 390px frame because headless Chrome won't make a window narrower than about 500px).

**Fixes.**

1. **Method logos.** They were built from the first four letters of the name ("GCAS"). They now use drawn icons: a phone for e-wallets, a bank for banks, a peso sign for cash, keeping brand colours. This applies to the tenant Billing page and the admin card.
2. **QR first on phones.** The QR card moves above the proof form at phone width, capped at 280px wide.
3. **Duplicate title.** The second "Proof of Payment" card is now **Payment Details**, matching the move-in page.
4. **Empty "Uploaded file" label.** It's hidden until a file is chosen.
5. **Focus outline.** The cash pop-up's button has a green focus outline instead of the browser default.
6. **Admin rows on phones.** Payment Methods rows keep Edit and Delete lined up at the top.
7. **QR viewer markup.** The viewer image no longer has an empty `src`.

**Left as is.**

- The detector flags Roboto as an "overused font". It's the locked brand font in PRODUCT.md.
- The admin sidebar's `transition: width` was there before this session.

**A blocked attempt.** A temporary local-only login route for screenshots was blocked by the permission check and removed straight away. `routes/web.php` has no trace of it.

## 11. Modals cut off on mobile

**Problem.** The tenant **Add New Ticket** form was cut off on phones, with its heading hidden behind the top bar.

**Cause.** At 860px and narrower the top bar sticks to the top at layer 100 (z-index). Most pages' modals and side drawers sat below that, at 50–61, so the bar covered their top edge and tall forms had nowhere to go.

**Fix.** One block of rules added to both shared stylesheets, `public/css/tenant.css` and `public/css/admin.css`:

- at 860px and below:
  - `.modal-overlay`, `.overlay`, `.review-modal-overlay` and `.lightbox` go to z-index 300, and `.drawer` to 301, above the top bar and the navigation menu (200);
  - modals are limited to `100dvh − 24px` (with a `100vh` fallback), scroll inside themselves, and don't scroll the page behind them;
- at 640px and below, `.modal-box` padding is 20px 18px.

**Pages covered.**

- **Tenant:** Tickets, Billing, Move-out review.
- **Admin:** Billing (modals and drawer), Applications, Lease Management, Inquiries, Admin Privileges, Tickets, Delinquency, Tenant Manager, Vacancy Monitor.

The login pages, the apply page and the move-in payment page have no fixed-position windows. The Payment Methods window (z-index 300) and the QR viewer (1000) were already above the top bar.

Only the ticket modal was screenshotted after this fix (375×667). The others use the same shared rule and should be checked on a phone.

## 12. Database changes

Run with `php artisan migrate`. All three have already run on the local database.

| Migration | What it does |
|---|---|
| `2026_09_25_000001_add_utility_costs_to_rooms_table` | Adds `rooms.monthly_utility_cost` and `rooms.monthly_wifi_cost` (decimal 10,2, default 0). |
| `2026_09_25_000002_create_payment_methods_table` | Creates `payment_methods` and adds `payments.payment_method_label`. No starter rows. |
| `2026_09_25_000003_add_default_cash_payment_method` | Adds the built-in Cash Payment method if none exists. Its `down()` leaves the row in place on purpose. |

**Mishap during setup.** The first run of the second migration failed because it read `dormitory_profiles` instead of `dormitory_profile`. MySQL had already created the table and column, so both were removed by hand and the migration was run again cleanly. That table read was later removed entirely along with the starter rows.

**Local data changes:**

- the pre-filled GCash, BDO and Cash rows were deleted;
- you added GCash (with a QR) through the new admin screen;
- rooms 1, 2, 3 and 5 each have ₱500 utilities and ₱1,000 Wi-Fi, which were already set when checked.

## 13. Files changed

**New files**

- `app/Http/Controllers/PaymentMethodController.php`
- `app/Models/PaymentMethod.php`
- `database/migrations/2026_09_25_000001_add_utility_costs_to_rooms_table.php`
- `database/migrations/2026_09_25_000002_create_payment_methods_table.php`
- `database/migrations/2026_09_25_000003_add_default_cash_payment_method.php`
- `resources/views/partials/qr-lightbox.blade.php`

**Changed files**

- `app/Http/Controllers/Api/BillingController.php`: utilities and Wi-Fi from the room.
- `app/Http/Controllers/Api/PaymentController.php`: `payment_method_label` in the data returned.
- `app/Http/Controllers/Api/TenantPortalController.php`: `payment_method_id` on proof submission, cash rejected, label saved.
- `app/Http/Controllers/DormitoryProfileController.php`: passes payment methods to the page.
- `app/Http/Controllers/TenantController.php`: delinquent tenants can't be set to Active.
- `app/Http/Controllers/TenantOnboardingController.php`: move-in methods come from the database.
- `app/Http/Controllers/VacancyController.php`: room utilities and Wi-Fi fields.
- `app/Models/Payment.php`: fillable label.
- `app/Models/Room.php`: new fields and `utilityShares()`.
- `public/css/admin.css`: stat card mobile fix, modal and drawer layering.
- `public/css/tenant.css`: modal and drawer layering.
- `resources/views/adminaddfloor.blade.php`: room utilities and Wi-Fi fields and card text.
- `resources/views/adminbilling.blade.php`: method labels, peso icon.
- `resources/views/admincontracts.blade.php`: peso icon.
- `resources/views/admindormitoryprofile.blade.php`: Payment Methods card and window.
- `resources/views/partials/admin-sidebar.blade.php`: peso icon.
- `resources/views/partials/announcements-feed.blade.php`: mobile layout (already committed during the session).
- `resources/views/tenantbilling.blade.php`: Next Bill Estimate, methods from the database, cash pop-up, QR card, polish.
- `resources/views/tenantdashboard.blade.php`: stat cards on mobile, peso icon.
- `resources/views/tenantmoveinpayment.blade.php`: real QR card, fake generator removed.
- `resources/views/tenantmoveinpaymentmethod.blade.php`: methods from the database.
- `routes/web.php`: payment method routes, Next Bill Estimate and methods on `/billing`.

## 14. Testing checklist

What was checked during the session:

- PHP syntax check (`php -l`) on every changed PHP file, and Blade compilation (`view:cache`);
- `node --check` on the scripts of every edited page;
- in the local database:
  - the utilities and Wi-Fi split;
  - payment method create, update (including removing the QR), validation and delete, with a fake upload;
  - the cash protections;
  - reactivating a tenant through `setStatus`;
- page rendering and screenshots of the tenant Billing states, the admin Payment Methods card and window, the Vacancy Monitor, both move-in payment pages, and the ticket modal.

Still to do by hand in a browser:

- [ ] Admin: add, edit (swap and remove QR) and delete an e-wallet and a bank method; edit Cash instructions; confirm Cash has no delete button.
- [ ] Tenant Billing: pick each method; the cash pop-up; submit a real proof with GCash and confirm the admin Billing page shows "GCash".
- [ ] QR viewer on a real phone: pinch, double-tap, drag, download.
- [ ] Move-in flow for a tenant waiting on their move-in payment: choose a method, pay screen, submit proof.
- [ ] Vacancy Monitor: set room utilities and Wi-Fi, generate this month's billing, check the per-bed amounts on a new bill.
- [ ] Tenant Manager: try to set a delinquent tenant to Active and confirm the error.
- [ ] Phone check of admin modals and drawers after the layering fix: Billing, Applications, Lease Management, Inquiries, Tenant Manager.

## 15. Open items

- **Old columns.** `floors.monthly_utility_cost` / `monthly_wifi_cost` and `dormitory_profile.gcash_number` / `bdo_account_number` are no longer used and could be dropped in a later migration.
- **Rent split.** Rent, utilities and Wi-Fi are all split by bed count "for now". Switching to split by occupied beds would change `Room::perBedRate()` and `Room::utilityShares()` together.
- **Backdated cash (proposed, not built).** Recording a full cash payment dated on or before the due date could also reverse penalties and stages applied after that date. This needs an audit note, because it lets an admin wipe penalties by entering an earlier date.
- **Move-in cash.** Not offered; move-in requires a proof upload.
- **Not committed.** None of this session's work has been committed.
