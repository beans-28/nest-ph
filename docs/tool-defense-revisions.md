# Tool Defense Revisions

Changes made after the tool defense, following the panel's comments (Sir Leo). Each item lists what the system does now, where to see it, and where the code lives.

**Before deploying:** run `php artisan migrate`, then the seeders (`PurezaStationSeeder`, then `DemoDataSeeder` for demo data). The server must use `SESSION_DRIVER=database`, or online status and emergency auto-assign won't work. Set `TURNSTILE_SITE_KEY` and `TURNSTILE_SECRET_KEY` in the server's environment, and add the live domain to the Turnstile widget's hostnames in Cloudflare (see section 4). On the live site, create the NEST.PH team account with `php artisan nestph:create-platform-admin` (see section 5).

---

## 1. Room Listings and VR

### ☑ Add room area in square meters

- Every room has an optional **Room area (m²)** field.
- **Admin:** Vacancy Monitoring → Add/Edit Room. The field sits next to "Number of Beds". Room cards show the area, e.g. "₱4,500/bed · 18 m²".
- **Public:**
  - **Rooms page:** each card shows the area, e.g. "Priced per bed · 18 m²".
  - **VR tour:** the room info card shows it, e.g. "… ₱4,500 / bed / month · 18 m²".
- **Demo data:** solo rooms 9.5 m², 4-person rooms 18 m², 6-person rooms 24 m², big shared rooms about 3.5 m² per bed.
- **Code:** migration `2026_10_16_000001_add_area_sqm_to_rooms_table.php`, `Room::listingDetails()`, `VacancyController`, `Api/PublicController`.

### ☑ Clarify per-bed versus whole-room pricing

Room types were already set as **per bed** or **per room**, but the public pages only showed "₱X/mo". Now every listing says which one it is:

| Pricing | What the visitor sees |
|---|---|
| Per bed | **₱4,500**/bed/mo, then "Priced per bed" |
| Whole room (shared) | **₱4,500**/bed/mo, then "Room ₱9,000 split 2 ways" |
| Whole room (solo) | **₱4,500**/bed/mo, then "Whole room" |

- Rooms without a room type count as "whole room", because their price was already split across the beds.
- **Code:** `Room::pricingMode()`, `publicrooms.blade.php`, `publicvr.blade.php`.

---

## 2. Forecasting and Analytics

Reports → **Forecast** tab. The forecast estimates **income, occupancy and expenses for the next 3 months** from the last 6 months of real data.

### ☑ Add actual versus forecasted comparison lines

- For each past month, the system works out what the forecast **would have predicted at the time**, using only the 6 months before it. It then shows this next to what actually happened.
- **Charts:**
  - **Occupancy chart:** a dotted amber line, "What we forecast".
  - **Income vs. expenses chart:** dotted amber (forecast income) and purple (forecast expenses) lines over the actual bars.
- **Table:** "Forecast" columns next to Income, Expenses and Net.
- **Accuracy:** the Expected Income card says how far off past estimates were, e.g. "Past estimates were 16.2% off".
- **Code:** `ReportController::computeForecast()`, `adminreports.blade.php`.

### ☑ Consider utility expense fluctuations

- Expenses are now split into **utilities** (electricity, water, internet), which swing from month to month, and **steady costs** (salaries, other).
- **Expense range:** expected expenses come with a range based on the cheapest and most expensive recent months, e.g. "Range ₱535.8k–₱717.9k".
- **Seasonal months:** if the same month last year was recorded, the estimate leans toward it, so summer and El Niño electricity spikes show up. These months are marked **\***.
- **Demo data:** 18 months of expenses, with a hotter March–May 2026 (electricity about ₱150k vs ₱100k in normal months).

### ☑ Account for contractual restrictions on rent changes

- **The rule:** per the Tenant Agreement (§2.2) and the Payments and Fees Schedule, a tenant's rent **can't change during their current term**. A new price only applies to new tenants and renewals.
- **What the forecast shows:** for each future month, the number of beds whose rent is **locked by signed contracts**. Example: "78 of 79 beds locked at signed rent in Nov '26".
- **Why it matters:** this shows the owner that a price increase would only reach the remaining beds in that period.
- **NHSB rent-control caps are not built in.** The owner sets prices in the Dormitory Profile, and the contract rule decides when they take effect.

### Answers to the panel's questions

| Question | Answer |
|---|---|
| What is the forecast? | An estimate of income, occupancy and expenses for the next 3 months. |
| Is income what the dorm receives? | Yes: money actually collected (approved payments). |
| Parameters besides the 6-month history? | Lease end dates from signed contracts (expected move-outs), current occupied and total beds, average move-ins per month, money collected per occupied bed, expenses split into utilities vs. steady costs, and the same month last year for seasonal utilities. |
| Where is the actual? | The forecast lines for past months sit next to the actual numbers in both charts and the table. |

**Exports:** the Excel and PDF Forecast reports include all of the above: forecast columns, expense range, locked beds, and the accuracy figure.

---

## 3. Emergency Maintenance

An **emergency** is a ticket the system marks **Critical**, for example sparks, flooding or a threat to a tenant.

### ☑ Add immediate safety guidance for emergency reports (pop-up reminders)

- **Pop-up on picking a dangerous type:** in the tenant's **Add New Ticket** form, picking one of these shows a red pop-up with what to do right now:

| Concern type | Pop-up title |
|---|---|
| Fire / Safety Hazard | Fire or smoke? Get out first. |
| Electrical Issue | Stay away from the wires. |
| Plumbing / Water Emergency | Water and electricity do not mix. |
| Structural Damage | Move away from the damage. |
| Security Concern / Safety & Security | Your safety comes first. |

- **Buttons:** **Call 911** (dials on phones) and **I'm safe, continue my report**.
- **After submitting:** if the ticket is marked Critical, the success screen says **"Emergency Reported"**, shows how many staff were alerted, and repeats the safety steps.
- **Code:** the wording is in `MaintenanceTicket::SAFETY_GUIDANCE` (editable there); the screen is `tenanttickets.blade.php`.

### ☑ Support multiple simultaneous emergency assignments

- **Several staff per ticket:** the single "Assign to" dropdown is now an **Assigned staff** checklist.
- **Each row shows:** whether the admin is Online or Offline, and how many open emergencies they're already on.
- **"auto" tag:** marks staff the system picked automatically.
- **Ticket cards:** show the team, e.g. "Jerome + Mark". "Unassigned" shows in red.
- **Several emergencies at once:** they're spread across staff, because auto-assign picks whoever has the fewest open emergencies.
- **Code:** new table `maintenance_ticket_assignees` (migration `2026_10_16_000002_…`), `MaintenanceTicket::assignees()`, `TicketController`, `admintickets.blade.php`.

### ☑ Improve emergency status and response notifications (auto-assign online admins; online tag)

- **Online:** an admin counts as online if they've used any admin page in the **last 5 minutes**.
- **Auto-assign:** a new Critical ticket goes straight to **2 online admins**, choosing the ones with the fewest open emergencies.
- **If nobody is online:** the ticket stays unassigned and **every** admin is alerted.
- **Bell:**
  - It now checks every **30 seconds** instead of 60, because Critical tickets must be answered within 5 minutes.
  - It has new urgent items: "emergency assigned to you" and "emergency with no one assigned".
- **Emergency pop-up for admins:** a red alert shows the ticket title, room, and who is assigned. It has **Open ticket** (jumps straight to it) and **Later**. It disappears once someone responds.
- **Online tag:** Admin Privileges shows a green dot on the avatar plus an **Online** tag next to online admins.
- **Settings:** team size (2) and the online window (5 minutes) are at the top of `app/Services/EmergencyDispatcher.php`.

---

## 4. Security and Accounts

### ☑ Add CAPTCHA or equivalent bot protection

- **Tool:** Cloudflare **Turnstile**, a free CAPTCHA alternative. Most visitors don't have to solve a puzzle: Cloudflare checks the browser in the background and shows a checkbox only when something looks suspicious.
- **Where it's used:** on the public forms that anyone can submit without logging in:

| Form | Page | Why it's protected |
|---|---|---|
| Tenant log in | `/tenant/login` | Stops bots from guessing passwords |
| Admin log in | `/admin/login` | Stops bots from guessing passwords |
| Forgot Password (Send Code) | `/passwords` | Every request sends an email |
| Inquiry form | `/inquiry` | Stops spam from reaching the admin's inbox |
| Apply: "Send code" (cellphone OTP) | `/apply` | Every request sends an SMS, which costs money |
| Apply: "Approve & Register" | `/apply` | Stops fake applications |

- **Not protected:** pages behind a login, plus the OTP and reset-code verify steps (these come after a Turnstile check and are already rate-limited).
- **How it works:** the widget gives the browser a one-time token. The form sends it along, and the server asks Cloudflare whether it's real before the form is processed. If the check fails, the visitor sees "Please complete the security check and try again."
- **Setup:**
  - Keys come from the Cloudflare dashboard → Turnstile and go in `.env` as `TURNSTILE_SITE_KEY` and `TURNSTILE_SECRET_KEY`.
  - The secret key must never be committed.
  - If the keys are empty, the check is turned off (this is how automated tests run).
- **Code:** `app/Http/Middleware/VerifyTurnstile.php` (the server check, attached to the routes above as `turnstile`), `resources/views/partials/turnstile.blade.php` (the widget), `config/services.php`.

---

## 5. Generic NEST.PH Platform

The panel noted that the system opened straight to Pureza Station Dormitory, even though NEST.PH is meant to serve other dormitories too.

| Comment | Revision |
|---|---|
| The system immediately presents Pureza Station instead of a generic NEST.PH application. | A new NEST.PH landing page at `/` introduces the platform first. Pureza's homepage moved to `/pureza-station`. |
| The generic application process for other dorm owners isn't visible. | The landing page has a **For Dorm Owners** section with a 4-step "how it works" and a **Request NEST.PH** form. |
| No navigation separates the platform from the specific dormitory. | The landing page has a **Choose a Dormitory** section that links to Pureza Station. Every Pureza page has a **← NEST.PH** link back to the platform. |

### ☑ Create a generic NEST.PH landing page

- **Address:** `/` (the main address). Pureza Station's own homepage is now at `/pureza-station`. All of Pureza's "Back to home" buttons still go to Pureza's homepage.
- **Look:** the same design as Pureza's homepage: the same green nav bar with leaf texture, cream hero, green bands, buttons and dark footer.
- **Sections:**
  - **Hero:** "Welcome to NEST.PH!", a short explanation, a large green NEST.PH logo, and **Find a Dormitory** / **I Own a Dormitory** buttons.
  - **Choose a Dormitory:** a card for each dormitory on NEST.PH (currently Pureza Station) with its photo, address, description and a **Visit Dormitory** button. A dorm with no photos shows a green "Photos coming soon" placeholder.
  - **About NEST.PH:** the four main features (VR room viewing, automatic payment reminders, online applications and tenant portal, maintenance requests).
  - **For Dorm Owners:** the request form (see below).
- **Navigation:** the top bar has Home, Dormitories, About NEST.PH, For Dorm Owners and a **Request NEST.PH** button. On phones it collapses into the same Menu button as Pureza's pages.
- **Code:** `PlatformController::landing()`, `resources/views/platform.blade.php`. The top bar is the shared `partials/public-nav.blade.php`, called with `['platformNav' => true]`.

### ☑ GUI for other dormitory owners to request NEST.PH

- **How it works** (shown on the page): 1. Send a request → 2. We contact you → 3. Setup (rooms, rates, house rules, admin accounts) → 4. Go live.
- **Form fields:** owner's name, dormitory name, email and contact number (required); city, approximate number of beds and a message (optional); Data Privacy Act consent (required).
- **Protection:** Cloudflare Turnstile (see section 4), and at most 5 requests per person every 10 minutes.
- **Feedback:** the button says "Sending…" while sending. Errors appear under the field they belong to. On success: "Thank you! The NEST.PH team will contact you within 2–3 business days."
- **Code:** `PlatformController::storePartnerRequest()`, table `partner_requests` (migration `2026_10_19_000001_…`), model `PartnerRequest`.

### ☑ NEST.PH team (platform admin) account and dashboard

The existing admins are the **dormitory's** admins, so there was no one to receive the owner requests. A third role now covers the NEST.PH team itself.

- **New role:** `platform_admin` (migration `2026_10_19_000002_…`).
- **Login:** `/login/platform`, linked as **NEST.PH Team Login** in the landing page footer. It looks like the existing Admin login, with a single **NEST.PH TEAM** tab.
- **Dashboard:** `/platform` → **Owner Requests**, built the same way as the dorm admin pages (sidebar, top bar, filter chips):
  - Filter by status, with counts: New, Contacted, Demo scheduled, Onboarded, Declined.
  - Click a request to see the owner's details and message, change its status, and write **team notes** (e.g. "Demo set for Saturday 10 AM").
- **Kept separate:**
  - Each login page only accepts its own kind of account. A platform account can't log in at the tenant or admin login, and a dorm admin or tenant can't log in at the platform login.
  - Dorm admins and tenants get "Forbidden" at `/platform`.
  - A platform account can't open the dorm's admin pages.
- **Creating the account on the live site** (the demo account below is never created in production):
  ```
  php artisan nestph:create-platform-admin --name="NEST.PH Team" --email=... --password="..."
  ```
  Running it without the options asks each question instead.
- **Demo data:** 6 fictional owner requests covering every status. They're part of `DemoDataSeeder`. To add only them on the live site (this never deletes anything): `php artisan db:seed --class=PartnerRequestDemoSeeder --force`.
- **Demo reset:** resetting the demo data keeps platform team accounts. Before this fix it would have deleted them.
- **Code:** `PlatformController::dashboard()` / `updatePartnerRequest()`, `app/Http/Middleware/EnsureUserIsPlatformAdmin.php` (registered as `platform`), `app/Http/Controllers/Api/AuthController.php` (login rule for each page), `resources/views/loginplatform.blade.php`, `resources/views/platformadmin.blade.php`, `app/Console/Commands/CreatePlatformAdmin.php`.

---

## 6. Billing and Delinquency

The panel raised four billing points:

1. **Utilities:** some dorms include utilities in the rent and some don't. Check that turning this on or off really changes the monthly total, and show the result clearly.
2. **Advance rent and security deposit:** where do they appear in the application and payment process? Show the upfront payment breakdown on the tenant dashboard, following the dorm's own policy.
3. **Mid-month move-ins:** how much does a tenant pay when they move in partway through a month?
4. **Paying in advance:** what happens when a tenant wants to pay rent ahead, after they have already moved in?

All four are now handled. The sections below explain each one. Every peso example uses the system's real calculations, and each one is covered by an automated test.

### The three kinds of upfront money

These are easy to mix up, so the system keeps them strictly separate:

| | **Advance rent** | **Security deposit** | **Rent paid ahead** |
|---|---|---|---|
| **What it is** | Rent paid at move-in for the first month(s) | Money the dorm holds in case of unpaid bills or damage | Rent a current tenant chooses to pay early |
| **When it's paid** | With the move-in fee | With the move-in fee | Any time after moving in, if nothing is due |
| **Who decides the amount** | The dorm (Rental Policy) | The dorm (Rental Policy) | The tenant (1 to 3 months) |
| **What it pays for** | Rent for the first month(s) only | Nothing. It is held, not spent. | Rent for the months the tenant chose |
| **Returned to the tenant?** | No, it's used as rent | Yes, after move-out, minus approved deductions | Only if some of it is never used (e.g. moving out early) |
| **Covers utilities?** | No | No | No |

**Utilities are never paid in advance.** Water, electricity and Wi-Fi change from month to month, so they always appear on that month's bill, at that month's price. This keeps the dorm from losing money when prices go up and avoids "adjustment" charges later.

---

### ☑ Verify configurable utility charge calculations

**How utilities are billed.** Each room has a monthly **utilities cost** (water and electricity together) and a **Wi-Fi cost**, set by the admin in Vacancy Monitor. These are split evenly across the room's beds and added to each tenant's monthly bill. The dorm can mark either one as **included in the rent**, and then it isn't added.

**What was wrong, and what we changed.**
- **One checkbox for water and electricity.** The settings page had separate "Water" and "Electricity" checkboxes, but the room stores both as one combined cost. Checking only one of them did nothing, so the total didn't change. There is now one **"Water & electricity"** checkbox and one **"Wi-Fi"** checkbox, and each one changes the total.
- **The tenant's estimate follows the setting.** The **Next bill estimate** on the tenant's Billing page used to show utility charges even when the dorm had marked them as included. It now follows the setting and says **"Included in rent"** instead of a peso amount.

**Example:** ₱4,000 rent, in a 4-bed room with ₱2,000 utilities and ₱400 Wi-Fi. That's ₱500 utilities and ₱100 Wi-Fi per bed.

| Included in the rent | Monthly bill |
|---|---|
| Nothing | ₱4,000 + ₱500 + ₱100 = **₱4,600** |
| Water & electricity | ₱4,000 + ₱100 = **₱4,100** |
| Water & electricity, and Wi-Fi | **₱4,000** |

**How we verified it:** an automated test creates a bill under each of the three settings and checks these exact totals (`AdvanceRentAndDepositTest`).

**Note:** a change applies to bills created from then on. Bills that were already issued keep their amounts, so a tenant's existing bill never changes behind their back.

**Where to see it:**
- **Admin:** Dormitory Profile → Rental Policy → "Included in the rent".
- **Tenant:** Billing and Payments → "Next bill estimate".

---

### ☑ Display advance rent and security deposit breakdowns (and add advance rent to the system)

#### 1. The dorm sets its own advance and deposit

Before, every dorm was forced to collect exactly 1 month advance and 1 month deposit, because the numbers were written into the code. Now each dorm chooses:

- **Advance rent at move-in:** None, 1, 2 or 3 months.
- **Security deposit at move-in:** None, 1, 2 or 3 months.

The default is **1 + 1**, which matches Pureza Station's signed documents. A dorm can't set both to None, because a new tenant only becomes active after paying a move-in fee. Below the two dropdowns, a sentence explains the result in plain words, for example: "New tenants pay 3 months of rent at move-in. The advance pays their first 2 months, so their first bill comes after that."

**Move-in fee** = monthly rent × (advance months + deposit months). Example: ₱4,000 rent with 2 months advance and 1 month deposit = ₱8,000 advance + ₱4,000 deposit = **₱12,000**.

**Changing the setting never affects current tenants.** Each tenant's move-in bill stores its own advance and deposit amounts, and billing reads those, not the current setting.

#### 2. How the advance is used

Each month of advance pays one of the tenant's first rental months, so the first monthly bill comes **after** those months:

| Move-in | Advance | Covered by the advance | First monthly bill |
|---|---|---|---|
| Oct 1 | 1 month | October | November |
| Oct 1 | 2 months | October and November | December |
| Jul 15 | 3 months | July 15 to September 30 | October |
| Oct 17 | None | Nothing | Starts Oct 17 (see mid-month move-ins below) |

**The advance is never treated as a deposit, and the deposit is never treated as rent.** They are stored as separate amounts on the move-in bill.

#### 3. Where the tenant sees the breakdown

**Before paying: the move-in payment page.** Before choosing full or half payment, the new tenant sees:

```
Advance rent (1 month)          ₱4,000.00
  Pays for Oct 10 – Oct 31, 2026
Security deposit (1 month)      ₱4,000.00
  Returned when you move out
Total move-in fee               ₱8,000.00
```

**After moving in: Tenant Dashboard → "Deposit & Advance" card.** It has two halves.

**Advance rent:**
- The amount, the number of months, and the exact dates it paid for.
- The payment date and reference number.
- A note if they moved in mid-month and part of it is credited on the first bill.
- A note if they have also paid rent ahead (see the last section).

**Security deposit:** the amount, plus one of four statuses.

| Status | Meaning |
|---|---|
| **Not yet paid** | The move-in fee hasn't been paid yet |
| **Held** | The dorm is holding it until move-out. It is not used as rent. |
| **Refunded** | After move-out, the remaining deposit was returned. The card shows the deposit, the deductions with the reason, and the amount refunded (with method and date). |
| **Fully deducted** | After move-out, the deductions used up the whole deposit |

**When the deposit is used up by unpaid rent or penalties:** the card shows the deposit, the deductions and the reason (for example "Unpaid October rent"), and **₱0.00 refunded**, with the sentence "Your whole deposit was used for the deductions above, so there is nothing to refund." If the tenant owed more than the deposit could cover, the card also shows **"Still owed: ₱X"**. The leftover stays on their unpaid bills, so the same charge is never collected twice.

**Also clarified:** the dashboard's "Recent Billing" list used to label the move-in fee with its month (e.g. "Jul 2026 ₱12,000"), which looked like a very large rent bill. It now says **"Move-in fee"**.

**Where to see it:**
- **Admin:** Dormitory Profile → Rental Policy → "Advance rent at move-in" and "Security deposit at move-in".
- **Tenant (before paying):** the move-in payment page (Payment type step).
- **Tenant (after moving in):** Tenant Dashboard → "Deposit & Advance".

---

### ☑ Initial payment for mid-month move-ins

The dorm chooses how a mid-month move-in is charged (Rental Policy → "Mid-month move-in: first month is"):

- **Charged in full** (Pureza's setting): the advance pays the move-in month as a whole month, even if the tenant arrives on the 20th.
- **Prorated:** the tenant only pays for the days they actually stay. The unused days of the advance are taken off the **first monthly bill**.

**Example (prorated):** moving in October 20 at ₱3,100 a month. October has 31 days, and the tenant doesn't stay October 1–19, which is 19 days. ₱3,100 × 19 ÷ 31 = ₱1,900 credit, so November's bill is ₱3,100 − ₱1,900 = **₱1,200**.

**With no advance:** the first bill starts on the move-in date. If prorated, it covers only the days from that date (e.g. moving in Oct 17 at ₱3,100: 15 of 31 days = **₱1,500**). If charged in full, it's the whole month.

**Where the tenant sees it:** the move-in payment page and the dashboard card both say in words how much comes off the first bill and why.

---

### ☑ Tenant paying rent ahead (beyond the move-in)

The panel described a tenant who wants to pay in advance after already moving in. For example, a parent who wants to pay two months at once.

#### How it works for the tenant

1. Go to **Billing and Payments → Pay rent ahead**.
2. Pick **1, 2 or 3 months**. The card shows the amount and the months covered, for example "₱8,000.00, Covers your rent for Nov 2026 – Dec 2026".
3. Click **Continue** and pay the usual way: upload a GCash/bank proof for the admin to approve, or pay cash at the office.
4. Until the admin approves, the card says "Your proof of payment is being reviewed."
5. After approval, each covered month still gets a bill, but its rent shows as **paid ahead**, so only that month's utilities and Wi-Fi are due.

#### Example

₱4,000 rent and ₱600 utilities + Wi-Fi a month. The tenant pays ahead for November and December (₱8,000). In December, electricity goes up.

| Month | Rent | Utilities + Wi-Fi | Tenant pays |
|---|---|---|---|
| November | ₱0 (₱4,000 paid ahead) | ₱600 | **₱600** |
| December (electricity went up) | ₱0 (₱4,000 paid ahead) | ₱800 | **₱800** |
| January | ₱4,000 | ₱600 | **₱4,600** |

If the dorm includes utilities in the rent and a month is fully paid ahead, that month's bill is ₱0 and is **marked paid automatically**, so it never sits as "unpaid".

#### The rules

- **Only when nothing is due.** A tenant with an unpaid bill sees "Please settle your current bill first. You can pay ahead once nothing is due."
- **1 to 3 months, never past the end of the contract.** Only full months are offered.
- **The months are fixed and come after what's already covered:** after the last bill, the move-in advance, and any earlier pay-ahead.
- **One at a time.** A tenant can't start a second pay-ahead while one is waiting.
- **Paid in full.** The amount on the payment form is locked, so it's either paid ahead or not.
- **It can be cancelled** until a proof is submitted. After that, it waits for the admin.
- **A bill that was already issued is still covered.** Bills come out 10 days before they're due. If November's bill was already issued when the admin approves the pay-ahead, November's rent is covered at the moment of approval.

#### Safety: an unpaid pay-ahead is never treated as money owed

Paying ahead is optional. If a tenant starts one and doesn't finish, nothing bad happens to them. An unpaid pay-ahead:

- does **not** count in the tenant's **Balance Due**, or in the admin's **Total Outstanding** and reports
- never becomes **overdue** and never gets a **late penalty**
- never triggers **payment reminders**
- never starts the **delinquency process** (SMS, portal restriction, demand letters)
- can't have penalties attached to it, because penalties go on the next monthly bill instead

#### What the admin sees

- **Pending Payment:** the proof appears with a **Rent Paid Ahead** badge and "Rent ahead: Nov 2026 – Dec 2026". The admin approves or rejects it like any other proof.
- **Cash:** if the tenant pays cash at the office, the admin records it against the pay-ahead in "Record Payment Entry".
- **Receipts and Statement of Account** label it "Rent paid ahead", not "Monthly".
- **Moving out early:** if a tenant leaves before their paid-ahead months are used, Tenant Manager → Security Deposit shows **Unused Rent Paid Ahead: ₱X, return it to the tenant at move-out**.

**One limit:** the tenant starts a pay-ahead from their portal. The admin can't start one from the admin side.

---

### Answers to the panel's questions

| Question | Answer |
|---|---|
| Can utilities be included in or excluded from the rent? | Yes, per dorm: "Water & electricity" and "Wi-Fi" in Rental Policy. Each one changes the monthly total (₱4,600 → ₱4,100 → ₱4,000 in the example above). |
| How do you know the calculation is right? | Automated tests check the exact totals for each setting, and the tenant's estimate uses the same rule as the real bill. |
| Where do advance rent and security deposit appear? | On the move-in payment page before paying (with the total), and afterwards on the Tenant Dashboard's "Deposit & Advance" card. |
| Can a dorm require a different advance or deposit? | Yes: 0 to 3 months each, in Rental Policy. Default 1 + 1. |
| What if the whole deposit goes to unpaid rent or penalties? | The card shows each deduction with its reason, ₱0.00 refunded, "Fully deducted", and any amount still owed beyond the deposit. |
| What does a mid-month move-in pay? | It depends on the dorm's setting: the whole month, or only the days stayed, credited on the first bill (e.g. ₱1,200 for an Oct 20 move-in at ₱3,100). |
| Can a tenant pay in advance after moving in? | Yes, 1 to 3 months of rent, from Billing and Payments → Pay rent ahead. |
| What if utilities go up after a tenant paid ahead? | Only rent is prepaid. Utilities stay on each month's bill at that month's price, so the tenant pays the new amount and the dorm loses nothing. |
| What if a tenant moves out before using what they paid ahead? | Tenant Manager shows the unused amount so the admin returns it at move-out. |

**Code (for the team):**
- **Migrations:**
  - `2026_10_19_000001_add_advance_and_deposit_months_to_dormitory_profile.php`
  - `2026_10_20_000001_add_pay_ahead_to_billing_statements.php`
- **Main logic:**
  - `app/Services/PayAhead.php` (pay-ahead rules)
  - `BillingController::generateForContract()` (monthly bills: advance months, proration, pay-ahead, utilities)
  - `ApplicationController` (move-in fee)
  - `BillingStatement::advanceMonths()` / `scopeOwed()`
  - `DepositRefund::summaryFor()` (dashboard card data)
  - `TenantPortalController::startPayAhead()` / `cancelPayAhead()`
- **Views:**
  - `partials/deposit-advance-card.blade.php`
  - `partials/movein-breakdown.blade.php`
  - `tenantbilling.blade.php`
  - `admindormitoryprofile.blade.php`
- **Tests:**
  - `tests/Feature/AdvanceRentAndDepositTest.php`
  - `tests/Feature/PayAheadTest.php`

---

## Demo walkthrough

| To show | Log in as | Steps |
|---|---|---|
| Room area and pricing | (public) | Rooms page, or VR Tour → room card |
| Forecast vs actual, utilities, locked rent | Admin with View Reports | Reports → Forecast → Generate; open "How this is estimated" |
| Safety pop-up | Tenant (e.g. maria.santos@gmail.com) | Tickets → Add New Ticket → pick "Fire / Safety Hazard" |
| Emergency alert | Admin (e.g. kristine.bautista@nestph.test) | Any admin page: the "Sparking outlet near Bed 2" emergency pops up (no one assigned) |
| Several staff on one emergency | Admin | Tickets → "Burst pipe flooding the 3F hallway" → Assigned staff (Jerome + Mark) |
| Online tag | Owner | Admin Privileges |
| Bot protection | (public) | Tenant/Admin log in, Inquiry, Forgot Password or Apply: the Cloudflare box shows above the submit button |
| Generic NEST.PH landing and dorm selection | (public) | Open `/` → Choose a Dormitory → Visit Dormitory; on Pureza's pages, "← NEST.PH" goes back |
| Owner request form | (public) | `/` → For Dorm Owners → fill in and Send Request |
| Advance rent and deposit settings | Owner | Dormitory Profile → Rental Policy → "Advance rent at move-in" / "Security deposit at move-in" and "Included in the rent" |
| Deposit & Advance card | Tenant (e.g. maria.santos@gmail.com) | Dashboard → "Deposit & Advance" (1 month advance) |
| 2 or 3 months advance | hannah.soriano@gmail.com (2 months) or jerome.pineda@gmail.com (3 months) | Dashboard → "Deposit & Advance" shows the months covered; Billing shows the first bill only after them |
| Pay rent ahead | maria.santos@gmail.com (nothing due) | Billing and Payments → Pay rent ahead → pick months → Continue → pay. As admin: Billing and Payments → Pending Payment → "Rent Paid Ahead" → Approve |
| Owner requests dashboard | NEST.PH team (platform@nestph.test locally; your team account on the live site) | `/login/platform` → Owner Requests → filter by status, open a request, change status, add notes |

Demo password for every seeded account: **Password123!**

## Tests

The new automated tests are in `tests/Feature/EmergencyTicketsTest.php`, `tests/Feature/RoomListingAndForecastExtrasTest.php`, `tests/Feature/PlatformLandingTest.php`, `tests/Feature/PlatformAdminTest.php` and `tests/Feature/AdvanceRentAndDepositTest.php` and `tests/Feature/PayAheadTest.php`. Run `php artisan test`: all 101 tests pass.
