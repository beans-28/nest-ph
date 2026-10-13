---
title: "NEST PH – Use Case and Activity Diagram Updates"
---

This guide lists the diagram changes needed for the updated use cases in the Use Case Reports (Tables 36, 38, and 54–57).

# Updated and New Use Case Reports

Tables 36 and 38 were rewritten. Tables 54 to 57 are new.

Table 36  
**Use Case Report – Generate Reports**

| Use Case Name | Generate Reports |
| :---- | :---- |
| **Scenario** | A Dormitory Administrator/Owner generates a summary report (Occupancy, Financial / Billing, Expenses and Profit, or Forecast) and optionally exports it as an Excel or PDF file. |
| **Triggering Event** | The administrator navigates to the Reports page and selects a report type. |

Continuation of **Table 36**

| Brief Description | This use case combines the generation of all report types into a single flow. All report types share the same navigation, generate action, and export options. The Occupancy report shows the current status of all rooms and bedspaces, a breakdown by floor, and a 12-month occupancy trend (move-ins, move-outs, and occupancy rate per month) rebuilt from lease contract dates. The Financial / Billing report covers a selected date range (default: this month so far) and computes rent collected, cash vs. online collections, outstanding balances, penalties, delinquent accounts, recorded expenses, and net profit, with a month-by-month collected vs. expenses chart. The Expenses and Profit report lists the last 12 months of recorded expenses against collections. The Forecast report is described in View Forecast Analysis. Exported Excel and PDF files include the dormitory name, the report period, summary tables, and charts. |
| ----- | :---- |
| **Actors** | Dormitory Administrator/Owner |
| **Include Use Case** | Retrieve Report Data Compute Report Summary |
| **Extend Use Case** | Export Report as Excel Export Report as PDF View Forecast Analysis Record Monthly Expenses |
| **Preconditions** | Administrator is logged in with the View Reports privilege. Room, tenant, and/or billing records exist in the system. |
| **Postconditions** | Selected report is generated and displayed. Report is optionally exported as an Excel or PDF file. |
| **Exceptions** | No records exist for the selected date range; system displays an empty report with a notice. Some months in the selected range have no recorded expenses; system lists the missing months and notes that net profit for those months counts collections only. Selected date range is invalid; system displays a validation error. System cannot retrieve data due to a database error; system displays an error message. Report export fails due to a system error; system displays an error and allows the administrator to retry. |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to Reports. | 1.1. Display the reports page with the report type tabs. |
| 2\. Select report type: Occupancy, Financial / Billing, Expenses and Profit, or Forecast. | 2.1. Display the date range selector (Financial / Billing report only). |
| 3\. Set the desired date range (if applicable) and click Generate. | \[Occupancy Report\] 3.1. Retrieve the current room and bedspace occupancy data. 3.2. Compute: total rooms, total bedspaces, occupied, vacant, reserved, and maintenance counts, and occupancy percentage. 3.3. Display the summary with a breakdown by floor and a 12-month occupancy trend chart. |

Continuation of **Table 36**

|  | \[Financial / Billing Report\] 3.1. Retrieve approved payments, billing statements, penalties, and recorded expenses for the selected period. 3.2. Compute: total collected (cash vs. online), total outstanding, total penalties, number of delinquent accounts, total expenses with a breakdown (electricity, water, internet, salaries, other), and net profit. 3.3. Display the summary with a monthly collected vs. expenses chart, and list any months with no recorded expenses. \[Expenses and Profit Report\] 3.1. Retrieve the last 12 months of recorded expenses and collections. 3.2. Display each month's expenses, collections, and net profit with column totals. \[Forecast Report\] 3.1. Proceed to View Forecast Analysis. |
| :---- | :---- |
| 4\. Click Export and choose Excel or PDF (optional). | 4.1. Generate the file with the dormitory name, report title, report period, summary tables, and charts. 4.2. Download the file to the administrator's device. 4.2.1. If export fails, display an error and allow the administrator to retry. |

Table 38  
**Use Case Report \- Manage Dormitory Profile** 

| Use Case Name | Manage Dormitory Profile  |
| ----- | :---- |
| **Scenario** | The Dormitory Administrator/Owner sets up or updates the dormitory's public profile (details, photos, logo, amenities, room types and rates, other charges, house rules) and optional legitimacy documents from the admin panel.  |
| **Triggering Event** | The administrator navigates to the Manage Dorm Profile tab in the admin panel.  |
| **Brief Description** | This use case allows the administrator to edit the dormitory's basic information, upload a cover photo, hero photos, and brand logo, turn amenities on or off, manage room types and their monthly rates, manage other charges listed in the fee schedule, and manage house rules grouped under section headings. The administrator may also upload legitimacy documents such as the Business Permit and BIR Registration credentials in accordance with BIR RMC No. 038-2026. Rental policy settings and the tenancy documents applicants sign are covered in Configure Rental Policy and Tenancy Documents, and payment accounts in Manage Payment Methods. All changes are reflected on the public Dormitory Profile page.  |
| **Actors** | Dormitory Administrator Dormitory Owner |
| **Include Use Case** | Validate Inputs   Display BIR Registration Seal/Badge  |
| **Extend Use Case** | Upload Business Permit  Upload BIR Registration Credentials  Remove Uploaded Document  Configure Rental Policy and Tenancy Documents  Manage Payment Methods  |
| **Preconditions** | The administrator is logged in.  |
| **Postconditions** | Changes are saved and reflected on the public Dormitory Profile page.  Uploaded BIR credentials trigger the display of the BIR Registration Seal/Badge on the public profile per RMC No. 038-2026.  |
| **Exceptions** | Required fields are left empty or invalid; system displays a validation error.  Uploaded file is an unsupported format or exceeds size limit; system rejects the upload and displays an error.  Room type maximum capacity is less than its minimum; system displays 'The maximum capacity cannot be less than the minimum.'  |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to Manage Dorm Profile.  | 1.1. Display current dormitory information, photos, amenities, room types, charges, house rules, and any uploaded legitimacy documents.  |
| 2\. Edit dormitory details and click Save.  | 2.1. Validate inputs.  2.1.1. Display error if required fields are empty or invalid.  2.2. Save the details and display confirmation "Dormitory profile updated successfully."  |
| 3\. Upload or replace the cover photo, hero photos, or brand logo (optional).  | 3.1. Validate image format and size.  3.1.1. Display error if unsupported or too large.  3.2. Save the image and show it on the public profile.  |
| 4\. Turn amenities on or off (optional).  | 4.1. Save the change and update the amenities list on the public profile.  |
| 5\. Add, edit, or delete a room type with its name, pricing mode, monthly rate, and capacity (optional).  | 5.1. Validate the inputs.  5.2. Save the room type and update the rates shown on the public profile.  |
| 6\. Add, edit, or delete an other charge (e.g., key replacement) with its amount and when it applies (optional).  | 6.1. Validate and save the charge.  6.2. Include the charge in the Payments and Fees Schedule.  |
| 7\. Add, edit, or delete a house rule, optionally under a section heading (optional).  | 7.1. Validate and save the rule.  7.2. Include the rule in the Rules and Regulations document.  |
| 8\. Upload Business Permit or BIR Registration credentials (optional).  | 8.1. Validate and save the uploaded file.  8.2. If BIR credentials were uploaded, display the BIR Registration Seal/Badge on the public Dormitory Profile page per RMC No. 038-2026.  |
| 9\. Click Remove on a document (optional).  | 9.1. Prompt confirmation, then delete the document and remove it from the public profile.  |

Table 54  
**Use Case Report \- View Forecast Analysis**

| Use Case Name | View Forecast Analysis |
| ----- | :---- |
| **Scenario** | A Dormitory Administrator/Owner views an estimate of the dormitory's occupancy, income, expenses, and net profit for the next three months, based on the last six months of actual data.  |
| **Triggering Event** | The administrator opens the Forecast tab on the Reports page.  |
| **Brief Description** | This use case lets the administrator plan ahead using simple, checkable averages. For each of the next three months, the system estimates occupied beds (current occupied beds, minus leases ending that month, plus the average number of move-ins per month), income (estimated occupied beds multiplied by the average income per occupied bed), expenses (average of the months with recorded expenses), and net profit (income minus expenses). The current month is excluded from the averages because it is not yet over. Estimates are rounded to the nearest ₱100 and are displayed separately from actual months, together with the assumptions used, so the administrator can see how each figure was computed.  |
| **Actors** | Dormitory Administrator/Owner  |
| **Include Use Case** | Retrieve Occupancy Trend  Retrieve Collected Payments  Compute Forecast  |
| **Extend Use Case** | Export Report as Excel  Export Report as PDF  Record Monthly Expenses  |
| **Preconditions** | The administrator is logged in with the View Reports privilege.  Bed and lease contract records exist in the system.  |
| **Postconditions** | The forecast for the next three months is displayed with the last six actual months, the totals, and the assumptions used. The forecast is optionally exported.  |
| **Exceptions** | No payment history exists yet; system estimates income from the average monthly rate of active leases.  No expenses have been recorded in the last six months; system shows expenses and net profit as unavailable and prompts the administrator to record monthly expenses.  System cannot retrieve data due to a database error; system displays an error message.  |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to Reports and open the Forecast tab.  | 1.1. Retrieve the occupancy trend, collected payments, recorded expenses, and lease end dates for the last six months and the next three months.  |
| 2\. Review the forecast.  | 2.1. Compute the averages: move-ins per month, income per occupied bed, and monthly expenses.  2.2. Compute each future month's leases ending, occupied beds, occupancy rate, income, expenses, and net profit.  2.3. Display actual months and estimated months in one table and chart, with estimated months clearly marked.  2.4. Display the three-month totals and the assumptions used.  |
| 3\. Click Export and choose Excel or PDF (optional).  | 3.1. Generate the forecast file with the assumptions table, the actual and estimated months table, and an income vs. expenses chart.  3.2. Download the file.  |

Table 55  
**Use Case Report \- Record Monthly Expenses**

| Use Case Name | Record Monthly Expenses |
| ----- | :---- |
| **Scenario** | A Dormitory Administrator/Owner records the dormitory's operating expenses for a month so the system can compute net profit and forecast future expenses.  |
| **Triggering Event** | The administrator opens the Monthly Expenses form on the Reports page.  |
| **Brief Description** | This use case allows the administrator to enter a month's expenses under fixed categories: electricity, water, internet, salaries, and other (with optional notes). While the administrator types, the system shows the month's collected payments and the resulting net profit. Saving a month that already has expenses updates that month instead of creating a duplicate. Recorded expenses are used by the Financial / Billing report, the Expenses and Profit report, and the Forecast.  |
| **Actors** | Dormitory Administrator/Owner  |
| **Include Use Case** | Validate Inputs  Compute Net Profit  Save Monthly Expenses  |
| **Extend Use Case** | View Expense History  |
| **Preconditions** | The administrator is logged in with the View Reports privilege.  |
| **Postconditions** | The month's expenses are saved with the recording administrator. Reports and the forecast use the saved figures.  |
| **Exceptions** | An amount is empty, negative, or not a number; system displays a validation error.  Selected month is in the future; system displays 'You can only record expenses for this month or earlier.'  System encounters a database error when saving; system displays an error message.  |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Open the Monthly Expenses form.  | 1.1. Display the form for the current month (all zero if nothing has been saved yet), the month's collected payments, and the last 12 saved months with their net profit.  |
| 2\. Select the month to record.  | 2.1. Load that month's saved expenses, if any, and its collected payments.  |
| 3\. Enter the amounts for electricity, water, internet, salaries, and other, with optional notes.  | 3.1. Display the total expenses and the month's net profit as the administrator types.  |
| 4\. Click Save.  | 4.1. Validate the inputs and the month.  4.1.1. Display an error if an amount is invalid or the month is in the future.  4.2. Create or update the month's expense record.  4.3. Display confirmation 'Expenses for \[month\] saved.' and refresh the history table.  |

Table 56  
**Use Case Report \- Configure Rental Policy and Tenancy Documents**

| Use Case Name | Configure Rental Policy and Tenancy Documents |
| ----- | :---- |
| **Scenario** | A Dormitory Administrator/Owner sets the dormitory's rental policy and manages the three tenancy documents that applicants review and sign: the Dormitory Tenant Agreement, the Dormitory Rules and Regulations, and the Payments and Fees Schedule.  |
| **Triggering Event** | The administrator opens the Rental Policy section in Manage Dorm Profile.  |
| **Brief Description** | This use case allows the administrator to set the rules the system uses for billing and contracts: rent due day and basis, grace period, late penalty percentage, minimum stay, move-out and extension notice periods, deposit refund period, reservation validity, mid-month move-in and move-out handling (full or prorated), which utilities are included in rent, short-term and transient rates, the representative who signs for the dormitory, and document version numbers and effective date. The system generates the three tenancy documents from these settings, the house rules, and the other charges. The administrator may preview each generated document or upload their own PDF to replace it. An uploaded PDF is used everywhere: the Apply page, the public Dorm Info page, and the documents applicants sign. Removing the upload goes back to the generated version.  |
| **Actors** | Dormitory Administrator/Owner  |
| **Include Use Case** | Validate Inputs  Save Rental Policy  Generate Tenancy Documents  |
| **Extend Use Case** | Preview Tenancy Document  Upload Tenancy Document  Remove Uploaded Tenancy Document  |
| **Preconditions** | The administrator is logged in.  |
| **Postconditions** | The rental policy is saved. New applicants sign documents with the saved terms, and billing changes apply to future bills only. Uploaded documents replace the generated versions. Documents already signed by tenants keep their own frozen copy.  |
| **Exceptions** | A policy field is empty or outside its allowed range (e.g., rent due day not between 1 and 28); system displays a validation error.  Uploaded document is not a PDF or exceeds 10 MB; system displays 'Please upload the document as a PDF.' or a size error.  |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Open the Rental Policy section.  | 1.1. Display the current policy settings and the status of each tenancy document (generated or uploaded).  |
| 2\. Edit the policy settings and click Save.  | 2.1. Validate the inputs.  2.1.1. Display an error if a value is missing or out of range.  2.2. Save the policy and display 'Rental policy saved. New applicants will sign documents with these terms, and billing changes apply to future bills only.'  |
| 3\. Click Preview on a tenancy document (optional).  | 3.1. Generate the document as a PDF from the current settings and display it.  |
| 4\. Upload a PDF to replace a tenancy document (optional).  | 4.1. Validate the file type and size.  4.2. Save the file and use it in place of the generated document.  4.3. Display '\[Document\] uploaded. Applicants will now see and sign your file.'  |
| 5\. Click Remove on an uploaded document (optional).  | 5.1. Remove the upload and go back to the generated version.  5.2. Display a confirmation message.  |

Table 57  
**Use Case Report \- Manage Payment Methods**

| Use Case Name | Manage Payment Methods |
| ----- | :---- |
| **Scenario** | A Dormitory Administrator/Owner sets up the payment accounts (e-wallet, bank, or cash) that tenants use to pay their move-in fees and monthly bills.  |
| **Triggering Event** | The administrator opens the Payment Methods section in Manage Dorm Profile.  |
| **Brief Description** | This use case allows the administrator to add, edit, and delete payment methods. An e-wallet or bank method requires the name and number of the account that tenants send money to, and may include payment instructions and a QR code image. A cash method needs no account details. The saved methods are shown to tenants when they pay move-in fees or submit payment proof.  |
| **Actors** | Dormitory Administrator/Owner  |
| **Include Use Case** | Validate Inputs  Save Payment Method  |
| **Extend Use Case** | Upload QR Code  Delete Payment Method  |
| **Preconditions** | The administrator is logged in.  |
| **Postconditions** | The payment method is saved and shown to tenants on the payment pages, with its QR code if one was uploaded.  |
| **Exceptions** | Account name or number is missing for an e-wallet or bank method; system displays 'Enter the name on the account tenants will send money to.' or 'Enter the mobile or account number tenants will send money to.'  QR code image is an unsupported format or exceeds 5 MB; system rejects the upload and displays an error.  |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Open the Payment Methods section.  | 1.1. Display the list of saved payment methods.  |
| 2\. Click Add Payment Method and select the type (e-wallet, bank, or cash).  | 2.1. Display the fields for the selected type.  |
| 3\. Enter the method name, account name, account number, and instructions, and upload a QR code (optional), then click Save.  | 3.1. Validate the inputs.  3.1.1. Display an error if a required field is missing or the QR image is invalid.  3.2. Save the payment method and add it to the list.  |
| 4\. Edit or delete an existing payment method (optional).  | 4.1. Save the changes, or remove the method and its QR code image.  4.2. Update the payment options shown to tenants.  |


# Part 1: Use Case Diagram Changes

All of these use the **Dormitory Administrator/Owner** actor. No new actors are needed.

## New use cases (ovals) to add

- View Forecast Analysis
- Record Monthly Expenses
- Configure Rental Policy and Tenancy Documents
- Manage Payment Methods

## Changes to existing use cases

**Generate Reports**

- Replace the "Export Report as CSV" extend with two extends: **Export Report as Excel** and **Export Report as PDF**.
- Add «extend» arrows from **View Forecast Analysis** and **Record Monthly Expenses** to Generate Reports.

**Manage Dormitory Profile**

- Remove "Upload Dorm Contract/Policies File". It no longer exists in the app.
- Add «extend» arrows from **Configure Rental Policy and Tenancy Documents** and **Manage Payment Methods** to Manage Dormitory Profile.

## Include and extend relationships for the new use cases

| Use case | «include» | «extend» |
|---|---|---|
| View Forecast Analysis | Retrieve Occupancy Trend, Retrieve Collected Payments, Compute Forecast | Export Report as Excel, Export Report as PDF, Record Monthly Expenses |
| Record Monthly Expenses | Validate Inputs, Compute Net Profit, Save Monthly Expenses | View Expense History |
| Configure Rental Policy and Tenancy Documents | Validate Inputs, Save Rental Policy, Generate Tenancy Documents | Preview Tenancy Document, Upload Tenancy Document, Remove Uploaded Tenancy Document |
| Manage Payment Methods | Validate Inputs, Save Payment Method | Upload QR Code, Delete Payment Method |

## Arrow directions

- An «include» arrow points **from the main use case to the included one**. The step always happens.
- An «extend» arrow points **from the optional use case back to the main one**. The step happens only sometimes.

**Optional cross-link:** draw a dashed line or note from **Manage Payment Methods** to **Pay Move-In Fees** and **Submit Payment Proof**, since tenants see those payment accounts there.

# Part 2: Activity Diagrams

Use two swimlanes (**Administrator** and **System**) for each diagram. Make four new diagrams and redo two existing ones.

## 1. Generate Reports (redo)

1. Admin opens Reports.
2. Admin picks a report type. Use a **decision diamond with four branches**:
    - **Occupancy:** get bed data → compute counts and rate → show floor breakdown and 12-month trend.
    - **Financial / Billing:** admin sets a date range → system checks the range (invalid → error, back to the range step) → compute collected, outstanding, penalties, expenses, and net profit → **decision: any months missing expenses?** If yes, show a notice → show the chart.
    - **Expenses and Profit:** get the last 12 months → show the table with totals.
    - **Forecast:** go to View Forecast Analysis.
3. **Decision: export?** If yes, **decision: Excel or PDF?** → generate the file → **decision: did it fail?** If yes, show an error and let the admin retry.

## 2. View Forecast Analysis (new)

1. Admin opens the Forecast tab.
2. System gets the last 6 months of data.
3. **Decision: is there payment history?** If no, use the average rent of active leases instead.
4. **Decision: are any expenses recorded?** If no, mark expenses and net profit as unavailable and prompt the admin to record expenses.
5. Compute the averages.
6. **Loop three times (once per future month):** estimated beds = current − ending leases + average move-ins; income = beds × income per bed; expenses; net profit.
7. Show the actual and estimated months, with estimates marked.
8. Optional export (same as the end of diagram 1).

## 3. Record Monthly Expenses (new)

1. Admin opens the form.
2. System shows the current month, the amount collected, and the history.
3. Admin picks a month → system loads any saved values.
4. Admin enters the amounts → system updates net profit as the admin types.
5. Admin clicks Save.
6. **Decision: are the amounts valid?** If not, show an error and go back to entering amounts.
7. **Decision: is the month in the future?** If yes, show an error.
8. **Decision: is the month already saved?** If yes, update it. If no, create it.
9. Show the confirmation and refresh the history.

## 4. Configure Rental Policy and Tenancy Documents (new)

1. Admin opens Rental Policy.
2. Admin picks an action. Use a **decision with three branches**:
    - **Edit policy:** save → **decision: valid?** If not, show an error → if valid, save and confirm.
    - **Preview a document:** generate the PDF and display it.
    - **Upload a PDF:** **decision: is it a PDF under 10 MB?** If not, show an error → if it is, save it to replace the generated version and confirm. **Remove the upload:** go back to the generated version.

## 5. Manage Payment Methods (new)

1. Admin opens Payment Methods → clicks Add.
2. **Decision: what type?**
    - **E-wallet or bank:** account name and number are required.
    - **Cash:** no account details needed.
3. Admin enters details and an optional QR code → clicks Save.
4. **Decision: are the fields valid and is the QR image OK?** If not, show an error and go back.
5. Save the method → it appears in the tenant payment options.
6. Add an edit/delete branch from the list.

## 6. Manage Dormitory Profile (redo)

1. Admin opens the profile.
2. Admin picks what to change. Use a **decision with branches**: details, photos/logo, amenities, room types, charges, house rules, Business Permit/BIR, remove a document.
3. Each branch follows the same pattern: validate → (invalid → error) → save → update the public page.
4. **On the BIR branch only:** after saving, show the BIR seal.
5. **On the remove branch:** ask for confirmation first.
6. Delete the old "upload policies file" path.

# Tips for Splitting the Work

- **Draw the use case diagram first,** so the oval names are fixed and every activity diagram can reuse them exactly.
- **Assign diagrams in pairs:** 1 and 2 to one person, 3 and 4 to another, 5 and 6 to a third.
- **Check the step numbers in each table's Flow of Events.** Each activity should match a step there, so a reviewer can compare the diagram with the table.
