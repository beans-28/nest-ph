# Tool Defense Revisions

Changes made after the tool defense, following the panel's comments (Sir Leo). Each item lists what the system does now, where to see it, and where the code lives.

**Before deploying:** run `php artisan migrate`, then the seeders (`PurezaStationSeeder`, then `DemoDataSeeder` for demo data). The server must use `SESSION_DRIVER=database`, or online status and emergency auto-assign won't work. Set `TURNSTILE_SITE_KEY` and `TURNSTILE_SECRET_KEY` in the server's environment, and add the live domain to the Turnstile widget's hostnames in Cloudflare (see section 4).

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

Demo password for every seeded account: **Password123!**

## Tests

The new automated tests are in `tests/Feature/EmergencyTicketsTest.php` and `tests/Feature/RoomListingAndForecastExtrasTest.php`. Run `php artisan test`: all 74 tests pass.
