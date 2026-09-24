# Re-application Cleanup and Move-In / Password Polish: What Changed

This document explains the work done in this round, in the order it happened:

1. [Re-application Requested list cleanup](#1-re-application-requested-list-cleanup)
2. [Move-in payment pages brought in line with the public site](#2-move-in-payment-pages-brought-in-line-with-the-public-site)
3. [Forgot password fixes](#3-forgot-password-fixes)
4. [How we checked it](#4-how-we-checked-it)
5. [How to test everything](#5-how-to-test-everything)
6. [Open points](#6-open-points)

No migrations are needed. After pulling these changes, run:

```bash
php artisan view:clear
```

Then hard-refresh the browser (Ctrl+F5).

---

## 1. Re-application Requested list cleanup

**The problem:** when an admin clicks "Request Re-application", the application moves to the **Re-application Requested** tab. When the applicant then sends a new application with the same email, the new one shows under **Pending Review**, but the old one stayed in **Re-application Requested**. The same person was listed twice, and the old entry looked like it still needed follow-up.

**The solution:** a re-application request counts as resolved once a **newer** application exists with the **same email**, and it no longer appears in the list.

### How it works

In `ApplicationController::index()`, the list query now leaves out any application that:

- has the status `re_application_requested`, **and**
- has a newer application (later `created_at`) with the same email. The email match ignores capital letters.

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

### Things to know

- **Nothing is deleted or changed.** The old record keeps its `re_application_requested` status in the database. It is only hidden from the admin list.
- It is hidden from **All** as well, not only from the Re-application Requested tab.
- Only a newer application counts. An older application with the same email does not hide anything.

### Files

| File | Change |
|---|---|
| `app/Http/Controllers/Api/ApplicationController.php` | Added the filter in `index()` |

---

## 2. Move-in payment pages brought in line with the public site

**The problem:** the move-in flow is what a tenant sees right after being approved: Welcome → Payment Type → Payment Method → Payment → Pending. Those pages were built before the public site was polished and had fallen behind it:

- The nav used the old light-green gradient, and on phones it had no menu.
- The nav showed **Admin / Apply / Log In**, although the tenant is already logged in.
- There was no phone layout. The decorative "N" mark and a full-height layout pushed the form far down the screen.
- There were no keyboard focus styles.
- Letters stood in for icons ("₱", "G" for GCash, "B" for BDO).
- On the payment page, both panels were titled "Proof of Payment", the "Uploaded file" label showed before any file was added, and the balance showed no decimals while the other pages show two.
- Payment Type used two buttons plus JavaScript that built and submitted a hidden form. A tenant could not tell which option was selected unless they noticed a color change.
- Nothing showed where the tenant was in the flow.

**The solution:** all five pages now share one set of styles and the same nav as the public pages. The design was done with the `impeccable` skill (polish pass).

### New shared pieces

| File | What it is |
|---|---|
| `resources/views/partials/movein-styles.blade.php` | Shared CSS for the whole move-in flow: colors, nav, the two-column layout, buttons, errors, spinner, the step bar, and the tablet/phone layouts. It follows the same pattern as `publicinquiry`, `logintenant` and `passwords`. |
| `resources/views/partials/movein-steps.blade.php` | The step bar: **Approved → Payment type → Method → Proof of payment**. Usage: `@include('partials.movein-steps', ['step' => 2])`. Pass `5` when everything is done, which marks all four steps complete. |

### Nav: new tenant mode

`resources/views/partials/public-nav.blade.php` now accepts an optional `tenantSession` flag:

```blade
@include('partials.public-nav', ['tenantSession' => true])
```

When it is set, **Apply / Log In / Admin** are replaced by a single **Log Out** button, which posts to the existing `logout` route. Pages that don't pass the flag look exactly as before.

### Page by page

| Page | View file | What changed |
|---|---|---|
| Welcome | `tenantmoveinwelcome.blade.php` | Shared shell and nav, step 1 of the bar, the "Application Approved" label above the heading removed (the step bar shows that now), back arrow is a real link. Wording unchanged. |
| Payment Type | `tenantmoveinpaymenttype.blade.php` | Full and Partial are now **radio option cards** in a normal `<form>` that posts to `tenant.movein.payment-type.store` (the JS-built form is gone). Each option has a one-line description. The fee is shown larger. **Continue** stays disabled until an option is picked and shows a spinner while submitting. The options stack on phones. |
| Payment Method | `tenantmoveinpaymentmethod.blade.php` | GCash and BDO are radio option cards with drawn icons (phone and bank) instead of "G" and "B". **Proceed with Payment** stays disabled until one is picked. |
| Payment (upload proof) | `tenantmoveinpayment.blade.php` | See below. |
| Pending | `tenantmoveinpending.blade.php` | Shared shell, all four steps shown as done, "Submit Again" uses the shared secondary button style. |

#### Payment page details

- The panels are now titled **Proof of payment** and **Payment details**; before, both said "Proof of Payment".
- The balance shows two decimals (`₱9,500.00`) and says whether it is full or partial. For partial it adds "enter the amount you sent below."
- The account name and number now sit under the "Scan to pay here" label.
- The upload box can be reached with the keyboard and shows a focus ring. On touch devices it says "Add a screenshot or file" instead of "Drag and drop".
- The uploaded-file row appears only after a file is picked. Long file names are cut off with "…". "Remove" puts the cursor back on the file picker.
- Date and time sit side by side on desktop and stack on phones.
- For full payment, the read-only amount has a note: "Set to the full move-in fee."
- **Validation:** a missing file or empty field shows a clear message (for example "Please fill in date of payment."), scrolls to it, and puts the cursor in the field. Error messages from the server now show the first field-specific error.
- **Success:** the confirmation takes focus, so screen readers announce it, and has a **View Payment Status** button that links to the pending page.
- **Kept as before:** the upload endpoint, the form fields sent to the server, folding the payment time into the notes, and the QR-code fallback when the library can't load.

### Phone layout (all move-in pages)

- One slim nav row: logo, **Log Out**, **Menu**.
- The tagline is smaller, the decorative "N" mark is hidden, and the panel fills the screen width.
- Main buttons are full width.
- Inputs use 16px text so iOS Safari doesn't zoom in when you tap a field.

---

## 3. Forgot password fixes

`resources/views/passwords.blade.php` already used the shared public nav and phone layout, so it only needed fixes:

| Issue | Fix |
|---|---|
| The underline inputs set `outline: none`, which also removed the focus ring. Keyboard users couldn't see which field was selected. | The underline turns dark green and thicker when a field is selected. |
| The 5 code boxes overflowed on narrow phones (about 320px). | The boxes shrink to fit and the gap is smaller on phones. |
| Inputs were below 16px, so iOS zoomed in when you tapped them. | 16px inputs on phones. |
| The panel's corners and padding differed from the other public pages on phones. | Now matches the other public pages (24px corners, 20px padding). |

The steps and wording are unchanged.

---

## 4. How we checked it

The move-in pages need a logged-in tenant with a bill, so each view was rendered with sample data (a ₱9,500 bill, GCash, partial payment) and screenshotted in headless Chrome:

- **Desktop** at 1440px wide.
- **Phone** at 375px wide, inside an iframe. Headless Chrome won't go narrower than about 500px, which falsely made the pages look cut off.

In those renders the QR box shows "QR code unavailable" because the QR library couldn't load from a local file. On the real site it loads normally.

The flow has **not** been clicked through end to end with a real logged-in tenant yet. See the next section.

---

## 5. How to test everything

**Re-application cleanup**
1. As admin, open a pending application and click **Request Re-application**. It appears under **Re-application Requested**.
2. Apply again from `/apply` using the same email, in different capitalization if you like.
3. Back in admin: the new application is under **Pending Review**, and the old one is gone from **Re-application Requested** and **All**.

**Move-in flow** (log in as a newly approved tenant)
1. `/move-in`: the step bar shows "Approved"; the nav shows only **Log Out** (plus **Menu** on phones).
2. **Proceed with Payment** → Payment Type: Continue is disabled until you pick an option.
3. Payment Method: same check, then **Proceed with Payment**.
4. Payment page: try submitting with nothing filled in (you should get an error and the cursor should move to the field), then upload a file, fill in the fields, and submit. The success message should appear, and **View Payment Status** should open the pending page.
5. Click **Log Out** in the nav; it should end the session.
6. Repeat on a phone, or in DevTools device mode at 375px.

**Forgot password** (`/passwords`)
1. Use Tab to move through the fields; the selected field's underline should highlight.
2. On a narrow phone, the 5 code boxes should fit on one line.
3. On iOS, tapping a field should not zoom the page.

---

## 6. Open points

- **Partial payment wording:** the Payment Type page now says Partial Payment means "Pay part now and the remaining balance later." Confirm this matches how partial payments are actually handled.
- **Hidden from "All":** resolved re-application requests are also hidden from the **All** tab. If you want them kept there for history (for example with a "Resubmitted" label), that's a small follow-up.
- **Old wording kept on Payment Type:** "Please wait for the administrator's review…" appears before the tenant has paid. The wording was kept as it was, but it may be worth rewording.
