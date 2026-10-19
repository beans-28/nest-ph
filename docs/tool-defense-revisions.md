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

### ☑ Verify configurable utility charge calculations

- **The rule:** each room has a monthly utilities cost (water and electricity together) and a Wi-Fi cost, split evenly across its beds. Items the dorm marks as **included in the rent** are not added to the bill.
- **What changed:** the setting used to have separate Water and Electricity checkboxes, but the room stores them as one cost, so checking only one did nothing. It is now one **"Water & electricity"** checkbox plus **"Wi-Fi"**.
- **Example** (₱4,000 rent, 4-bed room with ₱2,000 utilities and ₱400 Wi-Fi):

| Included in rent | Monthly bill |
|---|---|
| Nothing | ₱4,000 + ₱500 + ₱100 = **₱4,600** |
| Water & electricity | ₱4,000 + ₱100 = **₱4,100** |
| Everything | **₱4,000** |

- **Tenant view:** the "Next bill estimate" on the Billing page now follows the same setting and says **"Included in rent"** instead of showing a charge. Before, it showed utilities even when the dorm had marked them as included.
- **Where:** Dormitory Profile → Rental Policy → "Included in the rent".

### ☑ Display advance rent and security deposit breakdowns (and add advance rent to the system)

- **Advance rent setting:** Dormitory Profile → Rental Policy now has **Advance rent at move-in** and **Security deposit at move-in**, each None or 1–3 months. The default is 1 + 1, which matches the signed documents. At least one month must be collected.
- **How the advance is used:** each month of advance pays one of the tenant's first months, so the first monthly bill comes after them. Example: moving in Oct 1 with 2 months advance covers October and November, and the first bill is for December. With no advance, the first month is billed from the move-in date.
- **Existing tenants are not affected** by a later change: each tenant's move-in fee keeps the advance and deposit it was created with.
- **Before paying:** the move-in payment page lists Advance rent (with the dates it pays for), Security deposit, and the Total move-in fee.
- **Tenant Dashboard → "Deposit & Advance" card:**
  - **Advance rent:** amount, number of months, dates covered, payment date and reference.
  - **Security deposit:** amount and status: **Not yet paid**, **Held**, **Refunded**, or **Fully deducted**.
  - **After move-out:** deposit, deductions (with the reason), and the amount refunded. If deductions use the whole deposit, it shows ₱0.00 refunded and "Your whole deposit was used for the deductions above". If the tenant still owes more than the deposit covered, it shows **"Still owed: ₱X"**.
- **Code:** migration `2026_10_19_000001_add_advance_and_deposit_months_to_dormitory_profile.php`, `ApplicationController::approve()` (move-in fee), `BillingController::generateForContract()`, `BillingStatement::advanceMonths()`, `DepositRefund::summaryFor()`, `partials/deposit-advance-card.blade.php`, `partials/movein-breakdown.blade.php`.

### ☑ Initial payment for mid-month move-ins

- **Charged in full** (default): the advance pays the move-in month as a whole month.
- **Prorated:** the days before the move-in date come off the first monthly bill. Example: moving in Oct 20 at ₱3,100 credits 19 of 31 days, so November's bill is ₱1,200. The move-in payment page and the dashboard card both explain this credit.
- **With no advance:** the first bill covers only the days from the move-in date if prorated, or the whole month if not.

### ☑ Tenant paying rent ahead (beyond the move-in)

The panel asked about a tenant who wants to pay in advance after moving in.

- **Where:** Billing and Payments → **Pay rent ahead**. The tenant picks 1, 2 or 3 months, sees the amount and the months it covers, then pays like any other bill: GCash/bank proof for the admin to approve, or cash at the office.
- **Rent only.** Utilities and Wi-Fi are **not** prepaid, because they change month to month. Each paid-ahead month still gets a bill for that month's utilities, at that month's price. Example (₱4,000 rent, ₱600 utilities and Wi-Fi):

| Month | Rent | Utilities + Wi-Fi | Due |
|---|---|---|---|
| Nov (paid ahead) | ₱0 (₱4,000 paid ahead) | ₱600 | **₱600** |
| Dec (paid ahead, electricity went up) | ₱0 (₱4,000 paid ahead) | ₱800 | **₱800** |
| Jan | ₱4,000 | ₱600 | ₱4,600 |

- **Rules:**
  - Allowed only when nothing is currently due ("Please settle your current bill first").
  - Up to 3 months, and never past the end of the contract.
  - Only one waiting at a time. It can be cancelled until a proof is submitted.
  - It must be paid in full.
  - It covers the exact months shown. If a month's bill was already issued when the payment is approved, that bill's rent is covered at that point.
- **What an unpaid pay-ahead never does:** count as "Balance due" or "Total Outstanding", go overdue, get a late penalty, send reminders, or start delinquency. Paying ahead is optional.
- **Admin:** it appears in Pending Payment with a **Rent Paid Ahead** badge and the months it covers ("Rent ahead: Nov 2026 – Dec 2026"). Receipts and the Statement of Account label it "Rent paid ahead". If a tenant moves out before using it, Tenant Manager shows **Unused Rent Paid Ahead** so it can be returned.
- **Code:** migration `2026_10_20_000001_add_pay_ahead_to_billing_statements.php`, `app/Services/PayAhead.php`, `TenantPortalController::startPayAhead()` / `cancelPayAhead()`, `BillingStatement::scopeOwed()`, `BillingController::generateForContract()`, `tenantbilling.blade.php`.

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
