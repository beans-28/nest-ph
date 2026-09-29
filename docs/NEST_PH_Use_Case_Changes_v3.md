# NEST.PH Use Case Reports: Changes from the Original to v3

This document lists every change made to the use case reports (Tables 8 to 52 in the original, Tables 8 to 53 in v3) so the manuscript matches the system as built. For each changed table it shows the original text and the new text. Tables not listed under "Changed tables" are unchanged apart from their table number.

## Summary

- Table 16 (Record Occupancy Transaction) was removed. Move-in is covered by Pay Move-In Fees and move-out by Deactivate Tenant Account.
- Tables were renumbered after removing Table 16. This also fixed the duplicate "Table 51".
- Table 53 (Manage Admin Accounts and Privileges) was added.
- Tenant types are now the same four in every form: Student, Working Student, Full-time Employee, and Part-time Employee.
- Open item, not changed: the ticket overdue thresholds (24 hours, 3 days) and the 5-day auto-escalation are placeholders the team still has to confirm. The delinquency Stage 3 to 6 day thresholds were intentionally not added.

## Table numbering: original to v3

| Original | v3 | Use Case |
| :---- | :---- | :---- |
| Table 8 | Table 8 | Login |
| Table 9 | Table 9 | Forgot Password |
| Table 10 | Table 10 | Manage Account Settings |
| Table 11 | Table 11 | Browse Rooms |
| Table 12 | Table 12 | Inquiry Form |
| Table 13 | Table 13 | Apply for Occupancy |
| Table 14 | Table 14 | Manage Tenant Records |
| Table 15 | Table 15 | Add New Tenant |
| Table 16 | Removed | Record Occupancy Transaction |
| Table 17 | Table 16 | Pay Move-In Fees |
| Table 18 | Table 17 | Manage Lease Contracts |
| Table 19 | Table 18 | Generate Billing Statement |
| Table 20 | Table 19 | Record Cash Payment |
| Table 21 | Table 20 | Process Online Payment Proof |
| Table 22 | Table 21 | View Payment History |
| Table 23 | Table 22 | Trigger Delinquency Escalation – Stage 1: Automated Account Flagging |
| Table 24 | Table 23 | Send Automated SMS Reminders – Stage 2: Graduated SMS Notifications |
| Table 25 | Table 24 | Restrict Tenant Portal Access – Stage 3: Automatic Portal Restriction |
| Table 26 | Table 25 | Notify Emergency Contact – Stage 4: Emergency Contact Notification |
| Table 27 | Table 26 | Generate Formal Demand Letter – Stage 5: Auto-Generated Demand Letter |
| Table 28 | Table 27 | Flag Tenant as Delinquent and Blacklist – Stage 6: Internal Delinquency Record |
| Table 29 | Table 28 | Override Delinquency Escalation Stage |
| Table 30 | Table 29 | View Vacancy Monitoring Dashboard |
| Table 31 | Table 30 | View VR Room Visualization |
| Table 32 | Table 31 | Manage VR Room Content |
| Table 33 | Table 32 | Submit Ticket (Concern / Maintenance Request) |
| Table 34 | Table 33 | View and Manage Tickets |
| Table 35 | Table 34 | Track Ticket Status |
| Table 36 | Table 35 | View Activity Log |
| Table 37 | Table 36 | Generate Reports |
| Table 38 | Table 37 | Deactivate Tenant Account |
| Table 39 | Table 38 | Manage Dormitory Profile |
| Table 40 | Table 39 | Ticket Priority Classification |
| Table 41 | Table 40 | Ticket Escalation Reminder |
| Table 42 | Table 41 | Reviews & Ratings |
| Table 43 | Table 42 | Submit Review after Move-out |
| Table 44 | Table 43 | View Announcements |
| Table 45 | Table 44 | Comment on Announcement |
| Table 46 | Table 45 | Manage Announcements |
| Table 47 | Table 46 | Manage Comments |
| Table 48 | Table 47 | View Penalties |
| Table 49 | Table 48 | Record Tenant Damage |
| Table 50 | Table 49 | Add Tenant Penalty |
| Table 51 | Table 50 | Issue Eviction Notice |
| Table 51 | Table 51 | Oversee Delinquency Escalation |
| Table 52 | Table 52 | Facility Management |
| (new) | Table 53 | Manage Admin Accounts and Privileges |

## Removed table

### Original Table 16: Record Occupancy Transaction

Removed because it was never built as a separate feature. Move-in is handled by Pay Move-In Fees (v3 Table 16) and move-out by Deactivate Tenant Account (v3 Table 37).

## New table

### v3 Table 53: Manage Admin Accounts and Privileges

- **Use Case Name:** Manage Admin Accounts and Privileges
- **Scenario:** The Dormitory Owner creates a new admin account, sets which privileges each administrator has, and revokes admin access when it is no longer needed.
- **Triggering Event:** The Dormitory Owner navigates to the Admin Privileges page.
- **Brief Description:** This use case allows the Dormitory Owner to manage who can use the admin panel and what each administrator can do. The owner adds a new admin account by entering a full name and email address and choosing the privileges to grant. The system creates the login and displays a temporary password for the owner to give to the new administrator. The owner can also change the privileges of an existing administrator or revoke their admin access. The owner cannot remove their own Manage Admin Users privilege or revoke their own admin access.
- **Actors:** Dormitory Owner
- **Include Use Case:** Validate Inputs Save Admin Privileges Log Privilege Changes
- **Extend Use Case:** Revoke Admin Access
- **Preconditions:** The Dormitory Owner is logged in and has the Manage Admin Users privilege.
- **Postconditions:** A new admin account is created with the selected privileges, the privileges of an existing administrator are updated, or admin access is revoked. Each action is logged with the administrator affected, the person who performed it, and the date.
- **Exceptions:** Full name or email is left empty or is invalid; system displays a validation error. Email address is already used by another account; system displays a validation error. Owner attempts to remove their own Manage Admin Users privilege; system displays 'You cannot remove your own Manage Admin Users privilege.' and keeps the current privileges. Owner attempts to revoke their own admin access; system disables the Revoke action for the owner's own account.
- **Flow of Events**
- Actor: 1. Navigate to the Admin Privileges page.  
System: 1.1. Display the list of administrators with their name, email, privileges, status, and date granted.
- Actor: [To add] 2. Click Add Admin Account.  
System: 2.1. Display the Add Admin Account form with fields for full name and email, and a checklist of privileges (Manage Tenants, Manage Rooms, Manage Contracts, Manage Billing, Manage Admin Users, and View Reports).
- Actor: 3. Enter the full name and email, select the privileges to grant, and click Create Account.  
System: 3.1. Validate the inputs. 3.1.1. Display an error if a field is missing or the email is already in use. 3.2. Create the admin login with the selected privileges and log the action. 3.3. Display the Admin Account Created window with the temporary password and a Copy Password button.
- Actor: [To change privileges] 4. Click Manage Privileges on an administrator.  
System: 4.1. Display the administrator's current privileges as a checklist.
- Actor: 5. Check or uncheck privileges and click Save Changes.  
System: 5.1. If the owner is removing their own Manage Admin Users privilege, display an error and keep the current privileges. 5.2. Save the updated privileges and log the change. 5.3. Display confirmation that the privileges have been updated.
- Actor: [To revoke] 6. Click Revoke on an administrator.  
System: 6.1. Display a confirmation prompt stating that the administrator will be signed out of the admin panel.
- Actor: 7. Confirm the revocation.  
System: 7.1. Remove all privileges from the account, deactivate its admin login, and log the action. 7.2. Display confirmation that admin access has been revoked.

## Changed tables

### Manage Account Settings (Table 10)

**Old:**

> **Scenario:** A logged-in current tenant requests updates to their personal profile information from their account settings page. Profile information changes made by tenants are subject to Dormitory Administrator/Owner approval before taking effect.

**New:**

> **Scenario:** A logged-in current tenant views their personal profile information and changes their password from their account settings page. Profile information is view-only for tenants, and corrections are requested by submitting a ticket to the Dormitory Administrator/Owner.

**Old:**

> **Brief Description:** This use case covers profile management within the account settings flow. Tenants can request updates to their personal details (name, contact number, emergency contact), which are submitted for administrator review and approval before being applied. Administrators may update their own profile details directly.
>
> **Actors:** Current Tenant Dormitory Administrator/Owner
>
> **Include Use Case:** Validate Inputs Submit Profile Update Request (Tenant) Save Changes
>
> **Extend Use Case:** None

**New:**

> **Brief Description:** This use case covers profile management within the account settings flow. Tenants can view their personal details (name, contact number, emergency contact) but cannot edit them directly. To request a correction, the tenant submits a ticket and the administrator updates the record. Tenants may also change their password.
>
> **Actors:** Current Tenant
>
> **Include Use Case:** Validate Inputs Change Password (Tenant) Save Changes
>
> **Extend Use Case:** Submit Ticket (Concern / Maintenance Request)

**Old:**

> **Postconditions:** For tenants: a profile update request is submitted and pending administrator approval. Profile information is updated only upon administrator approval.
>
> **Exceptions:** Required fields are left empty or contain invalid input; system displays a validation error. Administrator rejects the profile update request; system notifies the tenant with the rejection reason. System encounters a database error when saving changes; system displays an error message.

**New:**

> **Postconditions:** For tenants: profile information is displayed as view-only. If the tenant changes their password, the new password is saved. Corrections to profile information are requested through a submitted ticket and applied by the administrator.
>
> **Exceptions:** Required fields are left empty or contain invalid input; system displays a validation error. Current password entered is incorrect, or the new password and its confirmation do not match; system displays a validation error. System encounters a database error when saving changes; system displays an error message.

**Old:**

> Actor: 1. Navigate to Account Settings.  
> System: 1.1. Display current account details in editable form (name, contact number, emergency contact, etc.).
>
> Actor: 2. Modify personal details (name, contact number, emergency contact).  
> System: 2.1. Validate input fields in real time. 2.1.1. Display error if required fields are empty or invalid.
>
> Actor: 3. Click Submit for Approval.  
> System: 3.1. Save the update request as Pending in the database. 3.2. Notify the Dormitory Administrator/Owner of the pending profile update request. 3.3. Display confirmation 'Your profile update request has been submitted and is awaiting administrator approval.'
>
> Actor: 4. Administrator reviews the submitted profile update request.  
> System: 4.1. Display the pending request with old and new values for comparison.
>
> Actor: 5. Administrator clicks Approve or Reject.  
> System: 5.1. If Approved: apply the updated profile details to the tenant's account and notify the tenant that their profile has been updated. 5.2. If Rejected: retain the original profile details, prompt administrator to enter a rejection reason, and notify the tenant with the rejection reason.

**New:**

> Actor: 1. Navigate to Account Settings.  
> System: 1.1. Display current account details in view-only form (name, contact number, emergency contact, etc.). 1.2. Display a Submit a Ticket button for requesting corrections.
>
> Actor: 2. Click Submit a Ticket to request a correction to personal details (optional).  
> System: 2.1. Redirect the tenant to the Tickets page, where the request is submitted as a ticket.
>
> Actor: 3. Open the Change Password tab and enter the current password, new password, and confirmation.  
> System: 3.1. Validate input fields. 3.1.1. Display error if the current password is incorrect or the new password and confirmation do not match.
>
> Actor: 4. Click Update Password.  
> System: 4.1. Save the new password. 4.2. Display confirmation 'Your password has been updated.'

---

### Apply for Occupancy (Table 13)

**Old:**

> **Scenario:** A prospective tenant submits a formal occupancy application through the public-facing NEST PH website, reviews and signs the dormitory contract, uploads the signed document, and submits their application. The Dormitory Administrator/Owner then screens the application and either approves, rejects, or requests re-application. Upon approval, the system automatically generates a tenant account and sends the login credentials to the applicant via email.

**New:**

> **Scenario:** A prospective tenant submits a formal occupancy application through the public-facing NEST PH website, reviews and signs the dormitory contract electronically within the website, and submits their application. The Dormitory Administrator/Owner then screens the application and either approves, rejects, or requests re-application. Upon approval, the system automatically generates a tenant account and sends the login credentials to the applicant via email.

**Old:**

> **Brief Description:** This use case covers the occupancy application flow from the prospective tenant's perspective and the admin screening process. The tenant fills out the application form, accepts the Privacy Notice, provides personal and contact information with preferred room details, reviews and accepts the dormitory contract, downloads the contract file, uploads the signed e-signature document, and submits the application. The Dormitory Administrator/Owner reviews the submission and may approve, reject, or request re-application. The applicant is notified via email of the outcome. If approved, the system automatically generates a tenant account and sends the login credentials via email.

**New:**

> **Brief Description:** This use case covers the occupancy application flow from the prospective tenant's perspective and the admin screening process. The tenant fills out the application form, provides personal and contact information with preferred room details, reviews the dormitory contract pre-filled with their details, draws their signature to sign it electronically, checks the consent checkbox, and submits the application. No printing, scanning, or uploading of a signed copy is required, and only the applicant signs the contract. The Dormitory Administrator/Owner reviews the submission and may approve, reject, or request re-application. The applicant is notified via email of the outcome. If approved, the system automatically generates a tenant account and sends the login credentials via email.

**Old:**

> **Include Use Case:** Validate Inputs Accept Privacy Notice Display Dormitory Contract Upload Signed Document Notify Applicant via Email Generate Tenant Account

**New:**

> **Include Use Case:** Validate Inputs Accept Data Privacy Consent Display Dormitory Contract Sign Contract Electronically Notify Applicant via Email Generate Tenant Account

**Old:**

> **Exceptions:** Required fields (personal information, contact details, preferred room) are missing or contain invalid input; system displays a validation error and prevents progression. Applicant does not accept the Privacy Notice; system prevents progression to the application form. Applicant does not accept the dormitory contract or skips the signed document upload; system prevents form submission. Uploaded signed document has an unsupported file type or exceeds size limit; system rejects the upload and displays an error. Selected room/bedspace becomes unavailable due to a concurrent submission; system notifies the applicant to select another. Tenant account generation fails due to a system error after admin approval; system displays an error and retries. Email notification fails to send; system logs the failure and retries. Selected room/bedspace is already tagged as Reserved; system notifies the applicant to select another available room/bedspace.

**New:**

> **Exceptions:** Required fields (personal information, contact details, preferred room) are missing or contain invalid input; system displays a validation error and prevents progression. Applicant does not check the consent checkbox; system prevents submission and displays 'You must consent to the data privacy notice before submitting.' Applicant does not sign the dormitory contract; system prevents form submission. Drawn signature cannot be read when signing the contract; system displays an error and prompts the applicant to sign again. Selected room/bedspace becomes unavailable due to a concurrent submission; system notifies the applicant to select another. Tenant account generation fails due to a system error after admin approval; system displays an error and retries. Email notification fails to send; system logs the failure and retries. Selected room/bedspace is already tagged as Reserved; system notifies the applicant to select another available room/bedspace.

**Old:**

> Actor: 1. Click Apply for Occupancy.  
> System: 1.1. Redirect prospective tenant to the application form page. 1.2. Display the Privacy Notice before the form is accessible.
>
> Actor: 2. Read and accept the Privacy Notice.  
> System: 2.1. Validate that the Privacy Notice has been accepted. 2.1.1. If not accepted, prevent access to the application form.
>
> Actor: 3. Fill out the application form: personal information (full name, date of birth, address), contact details (mobile number, email), and preferred room information (room type, move-in date).  
> System: 3.1. Validate all required fields in real time. 3.1.1. Display error if any required field is missing or invalid.
>
> Actor: 4. Review the dormitory contract.  
> System: 4.1. Display the full dormitory contract for review. 4.2. Provide a Download Contract button for the applicant to obtain the contract file.
>
> Actor: 5. Download the contract file.  
> System: 5.1. Generate and serve the downloadable contract file.
>
> Actor: 6. Sign the contract and upload the signed e-signature document.  
> System: 6.1. Validate the uploaded file type and size. 6.1.1. Display error if the file is unsupported or exceeds the size limit.
>
> Actor: 7. Check the contract acceptance checkbox and click Submit Application.  
> System: 7.1. Validate contract acceptance and signed document upload. 7.1.1. Prevent submission if either is missing. 7.1.2. Check if selected room/bedspace has an existing pending application; if so, notify applicant to select another. 7.2. Save application with Pending Review status and timestamp. 7.3. Tag selected room/bedspace as Reserved. 7.4. Notify administrator of new pending application. 7.5. Send acknowledgment email to applicant.

**New:**

> Actor: 1. Click Apply for Occupancy.  
> System: 1.1. Redirect prospective tenant to the application form page. 1.2. Display the form in steps: Personal Information, Contact Information, and Room Information.
>
> Actor: 2. Proceed through the form steps using the Next and Back buttons.  
> System: 2.1. Validate the fields of each step before moving to the next step. 2.1.1. If a required field is missing, prevent progression to the next step.
>
> Actor: 3. Fill out the application form: personal information (first name, last name, date of birth, address), contact details (mobile number, email), preferred room information (room type, move-in date), and type of tenant (Student, Working Student, Full-time Employee, or Part-time Employee).  
> System: 3.1. Validate all required fields in real time. 3.1.1. Display error if any required field is missing or invalid.
>
> Actor: 4. Click Review & Sign Contract.  
> System: 4.1. Display the dormitory contract pre-filled with the applicant's details for review. 4.2. Display a signature pad for the applicant to draw their signature.
>
> Actor: 5. Draw signature, check the contract acceptance checkbox, and click Sign Contract.  
> System: 5.1. Generate the signed contract with the applicant's signature and attach it to the application. 5.1.1. If the signature cannot be read, display an error and prompt the applicant to sign again. 5.2. Display the signing date with a link to view the signed copy.
>
> Actor: 6. Upload a valid ID.  
> System: 6.1. Validate the uploaded file type and size. 6.1.1. Display error if the file is unsupported or exceeds the size limit.
>
> Actor: 7. Review the summary on the Verify Your Information step, check the consent checkbox confirming the accuracy of the information and agreement to the dormitory rules, and click Approve & Register.  
> System: 7.1. Validate contract acceptance, signed contract, and consent. 7.1.1. Prevent submission if any is missing. 7.1.2. Check if selected room/bedspace has an existing pending application; if so, notify applicant to select another. 7.2. Save application with Pending Review status and timestamp. 7.3. Tag selected room/bedspace as Reserved. 7.4. Notify administrator of new pending application. 7.5. Send acknowledgment email to applicant.

**Old:**

> Actor: 9. Select a pending application to review.  
> System: 9.1. Display full application details: personal information, contact details, preferred room, uploaded signed contract, and submission timestamp.
>
> **10. Review the application details and uploaded signed contract.**

**New:**

> Actor: 9. Select a pending application to review.  
> System: 9.1. Display full application details: personal information, contact details, preferred room, signed contract, and submission timestamp.
>
> **10. Review the application details and signed contract.**

---

### Manage Tenant Records (Table 14)

**Old:**

> **Brief Description:** This use case combines viewing and editing tenant records into a single management flow. The administrator retrieves the complete tenant list, applies search or filter options including by tenant type (e.g., student, employee, transient worker), and accesses individual profiles with full lease and billing details. From a profile, the administrator can edit and update any tenant information as needed.

**New:**

> **Brief Description:** This use case combines viewing and editing tenant records into a single management flow. The administrator retrieves the complete tenant list, applies search or filter options including by tenant type (Student, Working Student, Full-time Employee, or Part-time Employee), and accesses individual profiles with full lease and billing details. From a profile, the administrator can edit and update any tenant information as needed.

**Old:**

> Actor: 5. Modify specific fields (name, contact number, emergency contact, tenant type, or documents).  
> System: 5.1. Validate modified fields in real time. 5.1.1. Display error if required fields are cleared or invalid.

**New:**

> Actor: 5. Modify specific fields (first name, last name, contact number, emergency contact, tenant type, or documents).  
> System: 5.1. Validate modified fields in real time. 5.1.1. Display error if required fields are cleared or invalid.

---

### Add New Tenant (Table 15)

**Old:**

> **Postconditions:** Tenant record is created and saved in the database. Room/bedspace status is updated to Occupied. Tenant login credentials are generated and sent to the tenant's email address. Billing cycle is initiated upon confirmation of move-in.
>
> **Exceptions:** Required personal detail fields (name, contact number, email, emergency contact) are missing or invalid; system displays a specific validation error. Uploaded document has an unsupported file type or exceeds the size limit; system rejects the file and displays an error. No available rooms or bedspaces exist in the system; system displays 'No Available Rooms' message and prevents assignment. Lease end date is set before the lease start date; system displays a validation error. A tenant record with the same email address already exists in the system; system displays a duplicate record warning. Email service is unavailable when sending login credentials; system logs the failure and retries.

**New:**

> **Postconditions:** Tenant record is created and saved in the database. Room/bedspace status is updated to Occupied. Tenant login credentials are generated and sent to the tenant's email address. Tenant account status is set to Active immediately upon registration, and the tenant is included in the next billing cycle.
>
> **Exceptions:** Required personal detail fields (first name, last name, email) are missing or invalid; system displays a specific validation error. Uploaded document has an unsupported file type or exceeds the size limit; system rejects the file and displays an error. No available rooms or bedspaces exist in the system; system displays 'No Available Rooms' message and prevents assignment. Lease end date is set before the lease start date; system displays a validation error. A tenant record with the same email address already exists in the system; system displays a duplicate record warning. Email service is unavailable when sending login credentials; system logs the failure and retries.

**Old:**

> Actor: 3. Enter tenant personal details: full name, date of birth, home address, contact number, email address, and emergency contact details.  
> System: 3.1. Validate required fields in real time. 3.1.1. Display error if required fields are missing or invalid. 3.1.2. Check for duplicate email address. If a record with the same email already exists, display a duplicate record warning.
>
> Actor: 4. Select tenant type (e.g., student, employee, transient worker).  
> System: 4.1. Update form fields or required documents based on tenant type if applicable.

**New:**

> Actor: 3. Enter tenant personal details: first name, last name, date of birth, home address, contact number, email address, and emergency contact details.  
> System: 3.1. Validate required fields in real time. 3.1.1. Display error if required fields are missing or invalid. 3.1.2. Check for duplicate email address. If a record with the same email already exists, display a duplicate record warning.
>
> Actor: 4. Select tenant type (Student, Working Student, Full-time Employee, or Part-time Employee).  
> System: 4.1. Save the selected tenant type to the tenant record.

**Old:**

> Actor: 8. Click Submit.  
> System: 8.1. Validate all inputs. 8.1.1. Display errors for any remaining invalid or missing fields. 8.2. Generate tenant account credentials (Tenant ID and temporary password). 8.3. Save tenant record to the database. 8.4. Update room/bedspace status to Occupied. 8.5. Send login credentials to the tenant's registered email address. 8.6. Display confirmation 'Tenant registered successfully. Login credentials sent to [email address].'

**New:**

> Actor: 8. Click Submit.  
> System: 8.1. Validate all inputs. 8.1.1. Display errors for any remaining invalid or missing fields. 8.2. Generate tenant account credentials (Tenant ID and temporary password). 8.3. Save tenant record to the database with Active status. 8.4. Update room/bedspace status to Occupied. 8.5. Send login credentials to the tenant's registered email address. 8.6. Display confirmation 'Tenant registered successfully. Login credentials sent to [email address].'

---

### Pay Move-In Fees (Original Table 17, now Table 16)

**Old:**

> **Brief Description:** This use case describes how an approved applicant completes their move-in payment using the same tenant account credentials sent to them upon application approval. The account is already created at that point — however, access is restricted to the billing module only until the move-in fee is settled. The system displays the computed total due (security deposit + 1 month advance rent). The applicant uploads proof payment (e.g., GCash screenshot, bank transfer confirmation) for administrator verification. The admin manually reviews the proof and confirms the payment. Upon approval, the tenant's account status is updated to Active and full system access — including payment history, monthly billing, maintenance ticketing, visitor requests, utilities, downloadable contract, and room assignment details — is unlocked. The system also generates and sends a move-in permit to the tenant via email.

**New:**

> **Brief Description:** This use case describes how an approved applicant completes their move-in payment using the same tenant account credentials sent to them upon application approval. The account is already created at that point — however, access is restricted to the billing module only until the move-in fee is settled. The system displays the computed total due (security deposit + 1 month advance rent). The applicant chooses Full Payment or Partial Payment, then selects one of the payment methods set up by the administrator (e-wallet or bank account), which displays the account details and a QR code if one was provided. Cash payments are recorded by the administrator instead. The applicant uploads proof payment (e.g., GCash screenshot, bank transfer confirmation) for administrator verification. The admin manually reviews the proof and confirms the payment. Once the move-in fee is fully paid, the tenant's account status is updated to Active and full system access — including payment history, monthly billing, maintenance ticketing, downloadable contract, and room assignment details — is unlocked. The system also generates and sends a move-in permit to the tenant via email.

**Old:**

> **Include Use Case:** Compute Move-In Total Display Billing Module Verify Payment Activate Tenant Account Generate Move-In Permit

**New:**

> **Include Use Case:** Compute Move-In Total Display Billing Module Select Payment Type Select Payment Method Verify Payment Activate Tenant Account Generate Move-In Permit

**Old:**

> **Preconditions:** Applicant has been approved and has received their login credentials via email. Applicant is logged in — account status is Pending Move-In Payment and access is restricted to the billing module only. Billing record with the move-in fee breakdown exists in the system.
>
> **Postconditions:** Move-in payment proof is submitted and recorded. Admin manually verifies and confirms the payment. Tenant account status is updated to Active. Full system access is unlocked using the same login credentials. Move-in permit is generated and sent to the tenant via email.
>
> **Exceptions:** Proof of payment file has an unsupported type or exceeds size limit; system rejects the upload and displays an error. Admin cannot verify payment due to invalid or unclear proof; admin requests resubmission from the tenant. System encounters an error when updating tenant status to Active; system displays an error message and notifies the administrator. Move-in permit generation fails due to a system error; system logs the failure and notifies the administrator.

**New:**

> **Preconditions:** Applicant has been approved and has received their login credentials via email. Applicant is logged in — account status is Pending Move-In Payment and access is restricted to the billing module only. Billing record with the move-in fee breakdown exists in the system. At least one payment method has been set up by the administrator.
>
> **Postconditions:** Move-in payment proof is submitted and recorded. Admin manually verifies and confirms the payment. Once the move-in fee is fully paid, tenant account status is updated to Active. Full system access is unlocked using the same login credentials. Move-in permit is generated and sent to the tenant via email.
>
> **Exceptions:** Proof of payment file has an unsupported type or exceeds size limit; system rejects the upload and displays an error. Admin cannot verify payment due to invalid or unclear proof; admin rejects the proof with a reason, which is shown to the tenant, and the tenant uploads a new proof. System encounters an error when updating tenant status to Active; system displays an error message and notifies the administrator. Move-in permit generation fails due to a system error; system logs the failure and notifies the administrator.

**Old:**

> Actor: 2. View the billing module.  
> System: 2.1. Display the move-in fee breakdown: security deposit + 1 month advance rent = total amount due.
>
> Actor: 3. Upload proof of payment (e.g., GCash screenshot, bank transfer confirmation).  
> System: 3.1. Validate file type and size. 3.1.1. Display error if the file is unsupported or too large.

**New:**

> Actor: 2. View the billing module and choose Full Payment or Partial Payment.  
> System: 2.1. Display the move-in fee breakdown: security deposit + 1 month advance rent = total amount due. 2.2. Display the payment methods set up by the administrator (e-wallets and bank accounts).
>
> Actor: 3. Select a payment method, enter the amount sent if paying partially, and upload proof of payment (e.g., GCash screenshot, bank transfer confirmation).  
> System: 3.1. Display the account name, account number, and QR code (if provided) of the selected payment method. 3.2. Validate file type and size. 3.2.1. Display error if the file is unsupported or too large.

**Old:**

> Actor: 6. Click Approve or Request Resubmission.  
> System: 6.1. If Approved: update tenant account status from Pending Move-In Payment to Active, unlock full system access (payment history, monthly billing, maintenance ticketing, visitor requests, utilities, downloadable contract, room assignment details) using the same login credentials, generate and send a move-in permit to the tenant via email, and display confirmation 'Payment verified. Tenant account activated and move-in permit issued.' 6.2. If Request Resubmission: notify the tenant that their proof of payment was unclear or invalid and prompt them to re-upload.

**New:**

> Actor: 6. Click Approve or Reject.  
> System: 6.1. If Approved and the move-in fee is fully paid: update tenant account status from Pending Move-In Payment to Active, unlock full system access (payment history, monthly billing, maintenance ticketing, downloadable contract, room assignment details) using the same login credentials, generate and send a move-in permit to the tenant via email, and display confirmation 'Payment verified. Tenant account activated and move-in permit issued.' 6.2. If Rejected: require the administrator to enter a reason for the rejection and mark the proof as rejected. The next time the tenant opens the move-in payment pages, display 'Your last proof of payment was not accepted.' with the reason, and prompt the tenant to submit a new proof of payment. 6.3. If Approved and the payment is partial: record the payment and keep the account status as Pending Move-In Payment until the remaining balance is paid.

---

### Process Online Payment Proof (Original Table 21, now Table 20)

**Old:**

> **Postconditions:** If Approved: Tenant's outstanding balance is updated, electronic receipt is issued, and portal restriction (if active) is lifted. If Rejected: Tenant is notified and prompted to resubmit.

**New:**

> **Postconditions:** If Approved: Tenant's outstanding balance is updated, electronic receipt is issued, and portal restriction (if active) is lifted. If Rejected: Tenant sees the rejection reason on their billing page and is prompted to resubmit.

**Old:**

> Actor: 7. Click Approve or Reject.  
> System: 7.1. If Approved: Update tenant's outstanding balance. Record payment with timestamp. Generate and send electronic receipt to tenant. If Stage 3 restriction is active, lift restriction and restore full portal access. 7.2. If Rejected: Notify tenant that proof is invalid or unclear. Prompt tenant to resubmit.

**New:**

> Actor: 7. Click Approve or Reject.  
> System: 7.1. If Approved: Update tenant's outstanding balance. Record payment with timestamp. Generate and send electronic receipt to tenant. If Stage 3 restriction is active, lift restriction and restore full portal access. 7.2. If Rejected: Require the administrator to enter a reason for the rejection. On the tenant's billing page, mark the bill with 'Proof rejected' and, when the tenant opens the payment screen, display 'Your last proof of payment was not accepted.' with the reason. Prompt tenant to resubmit.

---

### Generate Formal Demand Letter – Stage 5: Auto-Generated Demand Letter (Original Table 27, now Table 26)

**Old:**

> **4. System generates the formal demand letter document including: tenant name and contact details, overdue amount with breakdown, escalation timeline, and a final payment deadline.**

**New:**

> **4. System generates the formal demand letter document including: tenant name and contact details, overdue amount with breakdown, escalation timeline, a final payment deadline, and a notice that the matter may be brought before the Barangay for conciliation under the Katarungang Pambarangay Law if the balance remains unpaid after the deadline.**

---

### Submit Ticket (Concern / Maintenance Request) (Original Table 33, now Table 32)

**Old:**

> **Extend Use Case:** Attach Photo/File to Ticket

**New:**

> **Extend Use Case:** Attach Photos to Ticket

**Old:**

> **Postconditions:** Ticket is created with a unique tracking number. Administrator is notified. Tenant can view ticket status.
>
> **Exceptions:** Tenant's portal is restricted due to Stage 3 delinquency; system blocks ticket submission and redirects to the payment link. Description field is left empty; system displays a validation error and prevents submission. Attached file has an unsupported type or exceeds the allowed size; system displays a specific error. Administrator notification fails after ticket is submitted; system logs the failure and retries.

**New:**

> **Postconditions:** Ticket is created with a unique tracking number. The ticket appears on the administrator's Tickets page. Tenant can view ticket status.
>
> **Exceptions:** Tenant's portal is restricted due to Stage 3 delinquency; system blocks ticket submission and redirects to the payment link. Subject or details field is left empty; system displays a validation error and prevents submission. Attached photo has an unsupported type or exceeds the allowed size, or more than 5 photos are attached; system displays a specific error.

**Old:**

> **3. Select ticket category (Maintenance Request, Concern, Feedback).**
>
> Actor: 4. Enter description of the concern or request.  
> System: 4.1. Validate that description is not empty. 4.1.1. Display error if the description field is empty.
>
> Actor: 5. Optionally attach a photo or file.  
> System: 5.1. Validate file type and size. 5.1.1. Display error if attachment is invalid.
>
> Actor: 6. Click Submit.  
> System: 6.1. Create ticket record with unique tracking number and timestamp. 6.2. Send notification to the administrator of the new ticket. 6.3. Display confirmation 'Ticket #[number] submitted successfully. You can track its status in My Tickets.'

**New:**

> **3. Select the concern type (Billing & Payment Concern, Electrical Issue, Plumbing / Water Emergency, Security Concern, Structural Damage, Safety & Security, Fire / Safety Hazard, Maintenance & Repairs, Facilities & Amenities, Administrative / Leasing Concern, Account & Access Issue, Noise / Roommate Concern, or Suggestion / Feedback).**
>
> Actor: 4. Enter a subject and the details of the concern or request.  
> System: 4.1. Validate that the subject and details are not empty. 4.1.1. Display error if either field is empty.
>
> Actor: 5. Optionally attach up to 5 photos.  
> System: 5.1. Validate file type, size, and number of photos. 5.1.1. Display error if attachment is invalid.
>
> Actor: 6. Click Submit.  
> System: 6.1. Create ticket record with unique tracking number and timestamp. 6.2. Display the new ticket on the administrator's Tickets page. 6.3. Display confirmation 'Ticket #[number] submitted successfully. You can track its status in My Tickets.'

---

### View and Manage Tickets (Original Table 34, now Table 33)

**Old:**

> **Scenario:** A Dormitory Administrator/Owner views all submitted tickets, updates their status, responds to tenants, and marks resolved tickets as closed.

**New:**

> **Scenario:** A Dormitory Administrator/Owner views all submitted tickets, updates their status, responds to tenants, and marks handled tickets as Resolved or Rejected.

**Old:**

> **Brief Description:** This use case describes how the Dormitory Administrator/Owner views, filters, and manages all tenant-submitted tickets. The administrator can update ticket statuses, respond to tenants, and close resolved tickets to maintain an organized and accountable support system.

**New:**

> **Brief Description:** This use case describes how the Dormitory Administrator/Owner views, filters, and manages all tenant-submitted tickets. The administrator can update ticket statuses, respond to tenants, and mark tickets as Resolved or Rejected to maintain an organized and accountable support system.

**Old:**

> **Extend Use Case:** Respond to Tenant Close Ticket

**New:**

> **Extend Use Case:** Respond to Tenant Resolve or Reject Ticket

**Old:**

> **Postconditions:** Ticket status is updated. Tenant is notified of any response or status change.
>
> **Exceptions:** No tickets match the applied filter criteria; system displays 'No tickets found.' Tenant notification fails after a status update or response is sent; system logs the failure and retries. System encounters a database error when updating the ticket status; system displays an error message.

**New:**

> **Postconditions:** Ticket status is updated. Tenant sees any response or status change the next time they open their Tickets page. No SMS or email notification is sent.
>
> **Exceptions:** No tickets match the applied filter criteria; system displays 'No tickets found.' System encounters a database error when updating the ticket status; system displays an error message.

**Old:**

> Actor: 2. Apply filters (by category, status, or date range).  
> System: 2.1. Display filtered ticket list. 2.1.1. If no tickets match, display 'No tickets found.'

**New:**

> Actor: 2. Apply filters (by category, status, or priority).  
> System: 2.1. Display filtered ticket list. 2.1.1. If no tickets match, display 'No tickets found.'

**Old:**

> Actor: [To update status] 4. Change ticket status (Open / In Progress / Resolved).  
> System: 4.1. Update ticket status in the database. 4.2. Notify the tenant of the status change.
>
> Actor: [To respond] 5. Enter a response message and click Send.  
> System: 5.1. Save the response and attach it to the ticket record. 5.2. Notify the tenant of the new response.
>
> Actor: [To close] 6. Click Close Ticket after resolution.  
> System: 6.1. Update ticket status to Closed with timestamp. 6.2. Notify the tenant that their ticket has been resolved and closed.

**New:**

> Actor: [To update status] 4. Change ticket status (Open / Seen / In Progress / Resolved / Rejected).  
> System: 4.1. Update ticket status in the database. 4.2. Show the new status to the tenant the next time they open their Tickets page.
>
> Actor: [To respond] 5. Enter a response message and click Save.  
> System: 5.1. Save the response and attach it to the ticket record. 5.2. Show the new response to the tenant the next time they open their Tickets page.
>
> Actor: [To close] 6. Set the status to Resolved or Rejected after the concern is handled and click Save.  
> System: 6.1. Update ticket status to Resolved or Rejected with timestamp. 6.2. Show the final status to the tenant the next time they open their Tickets page.

---

### Track Ticket Status (Original Table 35, now Table 34)

**Old:**

> **Brief Description:** This use case describes how a current tenant monitors the status of their previously submitted tickets. The system displays all submitted tickets with their current status and any administrator responses, allowing the tenant to stay informed about the progress of their concerns.

**New:**

> **Brief Description:** This use case describes how a current tenant monitors the status of their previously submitted tickets. The system displays all submitted tickets with their current status and any administrator responses, allowing the tenant to stay informed about the progress of their concerns. The tenant may also add replies to their own ticket.

**Old:**

> **Extend Use Case:** None

**New:**

> **Extend Use Case:** Reply to Ticket

**Old:**

> **Postconditions:** Tenant views the current status and any administrator responses for their submitted tickets.

**New:**

> **Postconditions:** Tenant views the current status and any administrator responses for their submitted tickets. Any reply added by the tenant is saved to the ticket thread.

**Old:** (none, this row was added)

**New:**

> Actor: 4. Enter a message in the Add a reply field and click Submit (optional).  
> System: 4.1. Validate that the reply is not empty. 4.2. Save the reply to the ticket thread with timestamp. 4.3. Display confirmation 'Reply sent.'

---

### Generate Reports (Original Table 37, now Table 36)

**Old:**

> **Brief Description:** This use case combines the generation of occupancy and financial/billing reports into a single flow. Both report types share the same navigation, date range selection, generate action, and export option. The occupancy report computes room statuses, availability, and occupancy rates. The financial/billing report computes rent collected, outstanding balances, penalties, and payment method breakdowns.

**New:**

> **Brief Description:** This use case combines the generation of occupancy and financial/billing reports into a single flow. Both report types share the same navigation, generate action, and export option. The date range applies only to the financial/billing report, while the occupancy report always shows the current status of all rooms and bedspaces. The occupancy report computes room statuses, availability, and occupancy rates. The financial/billing report computes rent collected, outstanding balances, penalties, and payment method breakdowns.

**Old:**

> **Extend Use Case:** Export Report as CSV/PDF

**New:**

> **Extend Use Case:** Export Report as CSV

**Old:**

> Actor: 3. Set the desired date range and click Generate.  
> System: [Occupancy Report] 3.1. Retrieve room and bedspace occupancy data for the selected period.
>
> 3.2. Compute: total rooms, occupied count, available count, reserved visitor count, reserved applicant count, approved/pending count, and occupancy percentage. 3.3. Display summary report with breakdown and historical trend if applicable. [Financial / Billing Report] 3.1. Retrieve billing and payment records for the selected period. 3.2. Compute totals: total collected, total outstanding, total penalties applied, number of delinquent accounts. 3.3. Display summary with breakdown by payment method (cash vs. online).
>
> Actor: 4. Click Export (optional).  
> System: 4.1. Generate and download the report as a PDF or CSV.

**New:**

> Actor: 3. Set the desired date range and click Generate.  
> System: [Occupancy Report] 3.1. Retrieve the current room and bedspace occupancy data, regardless of the selected date range.
>
> 3.2. Compute: total rooms, total bedspaces, occupied count, vacant count, reserved count, maintenance count, and occupancy percentage. 3.3. Display summary report with breakdown by floor. [Financial / Billing Report] 3.1. Retrieve billing and payment records for the selected period. 3.2. Compute totals: total collected, total outstanding, total penalties applied, number of delinquent accounts. 3.3. Display summary with breakdown by payment method (cash vs. online).
>
> Actor: 4. Click Export CSV (optional).  
> System: 4.1. Generate and download the report as a CSV file.

---

### Deactivate Tenant Account (Original Table 38, now Table 37)

**Old:**

> **Brief Description:** This use case describes how the Dormitory Administrator/Owner deactivates a former or inactive tenant's account following move-out or lease termination. The system checks for outstanding balances, requires confirmation, revokes portal login access, and retains all records for historical reference.

**New:**

> **Brief Description:** This use case describes how the Dormitory Administrator/Owner deactivates a former or inactive tenant's account following move-out or lease termination. The system checks for outstanding balances, requires confirmation, revokes portal login access, ends the tenant's active lease, frees their bedspace, and retains all records for historical reference.

**Old:**

> **Postconditions:** Tenant account is deactivated. Portal login access is revoked. All tenant records are retained in the system.

**New:**

> **Postconditions:** Tenant account status is set to Inactive. Portal login access is revoked, except that a tenant who moved out normally may log in only to leave a review. The tenant's active lease is ended and their bedspace is set to Vacant. All tenant records are retained in the system.

**Old:**

> Actor: 4. Click Confirm Deactivation.  
> System: 4.1. Update tenant account status to Inactive. 4.2. Revoke portal login access. 4.3. Retain all tenant records in the database for historical reference. 4.4. Log the deactivation action with administrator ID, reason, and timestamp. 4.5. Display confirmation 'Tenant account deactivated successfully.'

**New:**

> Actor: 4. Click Confirm Deactivation.  
> System: 4.1. Update tenant account status to Inactive. 4.2. Revoke portal login access, except for logging in to leave a review after a normal move-out. 4.3. End the tenant's active lease and set their bedspace to Vacant. 4.4. Retain all tenant records in the database for historical reference. 4.5. Log the deactivation action with administrator ID, reason, and timestamp. 4.6. Display confirmation 'Tenant account deactivated successfully.'

---

### Ticket Priority Classification (Original Table 40, now Table 39)

**Old:**

> **Triggering Event:** The administrator/owner opens a submitted ticket to review and manage it.
>
> **Brief Description:** This use case describes how the Dormitory Administrator/Owner classifies submitted tenant tickets by priority level. Upon reviewing a ticket, the administrator assigns it as either Urgent or Non-Urgent, which guides resolution order and prioritization.

**New:**

> **Triggering Event:** The administrator/owner views a submitted ticket on the Tickets page.
>
> **Brief Description:** This use case describes how the Dormitory Administrator/Owner classifies submitted tenant tickets by priority level. Upon reviewing a ticket, the administrator assigns it as either Urgent or Non-Urgent using the priority dropdown on the ticket card, which guides resolution order and prioritization. Assigning a priority is optional. A ticket without a priority is shown as Unclassified and is treated as Non-Urgent for overdue purposes.

**Old:**

> **Exceptions:** Administrator saves the ticket without assigning a priority, system displays a validation error.

**New:**

> **Exceptions:** Priority update fails due to a server error; system displays an error message and keeps the previous priority.

**Old:**

> Actor: 1. Navigate to the Tickets page and open a submitted ticket.  
> System: 1.1. Display the ticket details including the tenant's name, concern description, and attached photo if any.
>
> Actor: 2. Review the ticket and assign a priority level (Urgent or Non-Urgent).  
> System: 2.1. Accept the selected priority classification.
>
> Actor: 3. Optionally assign the ticket to a staff member, update status, and enter a reply to the tenant.  
> System: 3.1. Accept and validate the provided inputs.
>
> Actor: 4. Click Save.  
> System: 4.1. Save the ticket with the assigned priority level and any other updates. 4.2. Display confirmation that the ticket has been updated successfully.

**New:**

> Actor: 1. Navigate to the Tickets page.  
> System: 1.1. Display the ticket cards, each with a priority dropdown (Unclassified, Urgent, or Non-Urgent).
>
> Actor: 2. Review the ticket and select a priority level (Urgent or Non-Urgent) from the dropdown on the ticket card.  
> System: 2.1. Save the selected priority classification immediately. 2.2. Display confirmation 'Priority updated.'
>
> Actor: 3. Optionally open the ticket to assign it to a staff member, update status, and enter a reply to the tenant.  
> System: 3.1. Display the ticket details including the tenant's name, concern description, and attached photos if any. 3.2. Accept and validate the provided inputs.
>
> Actor: 4. Click Save.  
> System: 4.1. Save the ticket updates. 4.2. Display confirmation that the ticket has been updated successfully.

---

### Ticket Escalation Reminder (Original Table 41, now Table 40)

**Old:**

> **Scenario:** The system tracks how long each ticket has remained unresolved and displays a running day count on the ticket card. Once the overdue threshold is reached (24 to 48 hours for Urgent tickets and 3 to 5 days for Non-Urgent tickets)

**New:**

> **Scenario:** The system tracks how long each ticket has remained unresolved and displays a running day count on the ticket card. Once the overdue threshold is reached (24 hours for Urgent tickets and 3 days for Non-Urgent tickets), the ticket is flagged as overdue, moved to the top of the Tickets page, and counted in an alert on the administrator's dashboard.

**Old:**

> **Brief Description:** This use case describes how the system provides passive but continuous escalation awareness on the administrator's Tickets page. A day count is displayed below each unresolved ticket card. Once the ticket surpasses its overdue threshold (Urgent: 24–48 hours; Non-Urgent: 3–5 days), a red "Overdue" badge is shown on the ticket card to prompt the administrator to act. All escalation indicators are visible on the admin side only.

**New:**

> **Brief Description:** This use case describes how the system provides passive but continuous escalation awareness on the administrator's Tickets page. A day count is displayed below each unresolved ticket card. Once the ticket surpasses its overdue threshold (Urgent: 24 hours; Non-Urgent: 3 days), a red "Overdue" badge is shown on the ticket card to prompt the administrator to act. Tickets without an assigned priority are treated as Non-Urgent. A Non-Urgent ticket that remains unresolved for 5 days or more is automatically treated as Urgent and labeled "Auto-escalated to Urgent," while the priority set by the administrator stays unchanged. Overdue tickets are sorted to the top of the Tickets page, and a red alert on the administrator's dashboard shows the number of overdue tickets. All escalation indicators are visible on the admin side only.

**Old:**

> **Include Use Case:** Monitor Ticket Resolution Status
>
> **Extend Use Case:** None

**New:**

> **Include Use Case:** Monitor Ticket Resolution Status Display Dashboard Overdue Alert
>
> **Extend Use Case:** Auto-escalate Non-Urgent Ticket

**Old:**

> **Postconditions:** The unresolved day count and Overdue badge are displayed on the ticket card as applicable. Both indicators are removed once the ticket is marked as Resolved.

**New:**

> **Postconditions:** The unresolved day count, Overdue badge, and Auto-escalated to Urgent label are displayed on the ticket card as applicable. All indicators are removed once the ticket is marked as Resolved or Rejected. The dashboard alert shows the current number of overdue tickets and is hidden when no tickets are overdue.

**Old:**

> Actor: 1. Navigate to the Tickets page.  
> System: 1.1. Retrieve all open tickets and calculate the elapsed time since each ticket was submitted. 1.2. Display a running day count below each unresolved ticket card (e.g., "Unresolved for 2 days"). 1.3. If the ticket has exceeded its overdue threshold (Urgent: 24–48 hours; Non-Urgent: 3–5 days), display a red "Overdue" badge on the ticket card.

**New:**

> Actor: 1. Navigate to the Tickets page.  
> System: 1.1. Retrieve all open tickets and calculate the elapsed time since each ticket was submitted. 1.2. Display a running day count below each unresolved ticket card (e.g., "Unresolved for 2 days"). 1.3. If the ticket has exceeded its overdue threshold (Urgent: 24 hours; Non-Urgent: 3 days), display a red "Overdue" badge on the ticket card in place of the day count. 1.4. If a Non-Urgent or unclassified ticket has been unresolved for 5 days or more, treat it as Urgent and display an "Auto-escalated to Urgent" label on the ticket card. 1.5. Sort overdue tickets to the top of the list, with Urgent tickets first and the oldest first, followed by the other open tickets and then Resolved or Rejected tickets.

**Old:** (none, this row was added)

**New:**

> Actor: 4. Navigate to the admin dashboard.  
> System: 4.1. If at least one ticket is overdue, display a red alert showing the number of overdue tickets and how many of them are Urgent, with a View Tickets button. 4.2. Hide the alert when no tickets are overdue.

---

### Reviews & Ratings (Original Table 42, now Table 41)

**Old:**

> **Scenario:** A prospective or current tenant visits the homepage and views the dormitory's aggregate star rating, total review count, and rating breakdown bar to assess the dormitory's overall reputation.

**New:**

> **Scenario:** A prospective or current tenant visits the homepage and views the dormitory's aggregate star rating and total review count, and may view the rating breakdown bar and individual reviews on the About the Dorm page, to assess the dormitory's overall reputation.

**Old:**

> **Brief Description:** This use case describes how the system displays aggregated tenant review data on the homepage. The display includes the overall star rating, total number of reviews submitted, and a visual rating breakdown bar showing the distribution of ratings. This allows visitors to quickly gauge the dormitory's reputation without navigating to a separate page.

**New:**

> **Brief Description:** This use case describes how the system displays aggregated tenant review data on the homepage. The homepage displays the overall star rating and the total number of reviews submitted. This allows visitors to quickly gauge the dormitory's reputation. The About the Dorm page displays the same summary together with a visual rating breakdown bar showing the distribution of ratings and the list of published reviews.

**Old:**

> **Postconditions:** The user successfully views the aggregate star rating, review count, and rating breakdown bar on the homepage.

**New:**

> **Postconditions:** The user successfully views the aggregate star rating and review count on the homepage, and the rating breakdown bar and reviews on the About the Dorm page.

**Old:**

> Actor: 1. Navigate to the homepage.  
> System: 1.1. Retrieve all approved reviews from the database. 1.2. Calculate the average star ratings. 1.3. Compute the rating distribution for the breakdown bar (count per star level: 1–5).

**New:**

> Actor: 1. Navigate to the homepage.  
> System: 1.1. Retrieve all approved reviews from the database. 1.2. Calculate the average star ratings.

**Old:** (none, this row was added)

**New:**

> Actor: 3. Navigate to the About the Dorm page (optional).  
> System: 3.1. Compute the rating distribution for the breakdown bar (count per star level from 1 to 5). 3.2. Display the rating breakdown bar and the list of published reviews.

---

### Submit Review after Move-out (Original Table 43, now Table 42)

**Old:**

> **Scenario:** A tenant who has been marked as Moved Out receives a prompt via SMS/system notification and submits a star rating and written review of the dormitory.
>
> **Triggering Event:** The system marks a tenant's status as Moved Out, which triggers an SMS/system notification prompting the tenant to leave a review.
>
> **Brief Description:** This use case describes how a former tenant submits a review after their move-out has been recorded by the administrator. Upon being marked as Moved Out, the tenant receives a notification with a link or prompt to submit a review. The tenant provides a star rating (1–5) and an optional written comment. Once submitted, the review is saved and reflected in the aggregate ratings displayed on the homepage.

**New:**

> **Scenario:** A tenant whose account has been set to Inactive after move-out receives a prompt via SMS and submits a star rating and written review of the dormitory.
>
> **Triggering Event:** The administrator deactivates a tenant's account after move-out, setting their status to Inactive, which triggers an SMS prompting the tenant to leave a review.
>
> **Brief Description:** This use case describes how a former tenant submits a review after their move-out has been recorded by the administrator. Upon being set to Inactive, the tenant receives an SMS asking them to log in and submit a review. A former tenant may log in only to leave a review. The tenant provides a star rating (1–5) and an optional written comment. Once submitted, the review is saved and reflected in the aggregate ratings displayed on the homepage. New reviews are automatically checked, and reviews with offensive language, links, contact details, or spam patterns are hidden until an administrator reviews them.

**Old:**

> **Include Use Case:** Validate Review Inputs Save Review to Database Update Aggregate Rating

**New:**

> **Include Use Case:** Validate Review Inputs Save Review to Database Automatically Check Review Update Aggregate Rating

**Old:**

> **Preconditions:** The administrator has marked the tenant's status as Moved Out. The tenant has a valid contact number or system account to receive the notification.
>
> **Postconditions:** The tenant's review is saved and included in the aggregate star rating and breakdown bar displayed on the homepage. The tenant can only submit one review per stay.
>
> **Exceptions:** Tenant does not submit a review after receiving the prompt; no action is taken and the prompt expires. Tenant attempts to submit a review more than once; system disallows duplicate submissions and displays a notice.

**New:**

> **Preconditions:** The administrator has set the tenant's status to Inactive after a normal move-out. The tenant has a valid contact number or system account to receive the notification.
>
> **Postconditions:** The tenant's review is saved and included in the aggregate star rating and breakdown bar displayed on the homepage. A review flagged by the automatic check is hidden and is not included until an administrator publishes it. The tenant can only submit one review per stay.
>
> **Exceptions:** Tenant does not submit a review after receiving the prompt; no action is taken and the prompt expires. Tenant attempts to submit a review more than once; system disallows duplicate submissions and displays a notice. Tenant who has already submitted a review attempts to log in again; system displays that the account has been deactivated.

**Old:**

> Actor: 1. Administrator marks tenant status as Moved Out.  
> System: 1.1. Update tenant status to Moved Out in the database. 1.2. Trigger an SMS/system notification to the tenant with a prompt to submit a review.
>
> Actor: 2. Tenant receives the notification and opens the review form.  
> System: 2.1. Display the review submission form with a star rating selector (1–5) and an optional written comment field. 2.2. Check if the tenant has already submitted a review for this stay; if so, display a notice and block resubmission.

**New:**

> Actor: 1. Administrator deactivates the tenant account after move-out.  
> System: 1.1. Update tenant status to Inactive in the database. 1.2. Send an SMS to the tenant with a prompt to log in and submit a review.
>
> Actor: 2. Tenant receives the SMS, logs in, and clicks Leave a Review.  
> System: 2.1. Display the review submission form with a star rating selector (1–5) and an optional written comment field. 2.2. Check if the tenant has already submitted a review for this stay; if so, display a notice and block resubmission.

**Old:**

> Actor: 5. Tenant clicks Submit Review.  
> System: 5.1. Save the review with the star rating, written comment (if any), and timestamp. 5.2. Update the aggregate star rating and review count on the homepage. 5.3. Display a confirmation message: "Thank you for your review!"

**New:**

> Actor: 5. Tenant clicks Submit Review.  
> System: 5.1. Save the review with the star rating, written comment (if any), and timestamp. 5.2. Automatically check the comment for offensive language, links, contact details, or spam patterns. 5.2.1. If the review is flagged, hide it from public view and display the message: "Thank you for your review! It will appear publicly once an administrator has checked it." 5.3. If the review is not flagged, update the aggregate star rating and review count on the homepage. 5.4. Display a confirmation message: "Thank you for your review!"
>
> Actor: [Admin] 6. Open the Review Moderation section of the Dormitory Profile page and click Publish, Hide, or Remove on a review.  
> System: 6.1. Update the review status. 6.2. Include the review in the aggregate star rating only if it is published.

---

### View Announcements (Original Table 44, now Table 43)

**Old:**

> **Scenario:** A logged-in tenant views announcements posted by the administrator on the homepage newsfeed.
>
> **Triggering Event:** The tenant navigates to the homepage after logging in.
>
> **Brief Description:** This use case allows current tenants to view all posted announcements on the newsfeed, including the poster's avatar, name, date, body, and comment count.

**New:**

> **Scenario:** A logged-in tenant views announcements posted by the administrator on the dashboard newsfeed.
>
> **Triggering Event:** The tenant navigates to the dashboard after logging in.
>
> **Brief Description:** This use case allows current tenants to view all posted announcements on the newsfeed, including the poster's avatar showing their initials, name, date, body, and comment count.

**Old:**

> **Postconditions:** The tenant successfully views the announcements on the homepage newsfeed.

**New:**

> **Postconditions:** The tenant successfully views the announcements on the dashboard newsfeed.

**Old:**

> Actor: 1. Navigate to the homepage.  
> System: 1.1. Retrieve and display all announcements in the newsfeed (avatar, name, date, body, comment count).

**New:**

> Actor: 1. Navigate to the dashboard.  
> System: 1.1. Retrieve and display all announcements in the newsfeed (avatar with the poster's initials, name, date, body, comment count).

---

### Comment on Announcement (Original Table 45, now Table 44)

**Old:**

> **Scenario:** A logged-in tenant submits a comment on an announcement from the homepage newsfeed.
>
> **Triggering Event:** The tenant types and submits a comment on an expanded announcement thread.
>
> **Brief Description:** This use case allows tenants to submit comments on announcements, provided commenting has not been restricted by the administrator.
>
> **Actors:** Current Tenant

**New:**

> **Scenario:** A logged-in tenant or Dormitory Administrator/Owner submits a comment on an announcement from the dashboard newsfeed.
>
> **Triggering Event:** The tenant or administrator types and submits a comment on an expanded announcement thread.
>
> **Brief Description:** This use case allows tenants to submit comments on announcements, provided commenting has not been restricted by the administrator. Administrators may also comment, including on announcements where comments are restricted.
>
> **Actors:** Current Tenant Dormitory Administrator/Owner

**Old:**

> **Preconditions:** The tenant is logged in. The announcement's comment thread is expanded. Comments are not restricted on the post.

**New:**

> **Preconditions:** The tenant or administrator is logged in. The announcement's comment thread is expanded. For tenants, comments are not restricted on the post.

**Old:**

> **Exceptions:** Comment field is empty; system prevents submission. Comments are restricted by the administrator; comment input is disabled.

**New:**

> **Exceptions:** Comment field is empty; system prevents submission. Comments are restricted by the administrator; comment input is disabled for tenants.

---

### Manage Announcements (Original Table 46, now Table 45)

**Old:**

> **Scenario:** A Dormitory Administrator/Owner composes and publishes an announcement to all tenants via the homepage newsfeed.
>
> **Triggering Event:** The administrator clicks the "Post Announcement" button on the homepage.
>
> **Brief Description:** This use case allows administrators to post announcements visible to all tenants on the homepage newsfeed. The "Post Announcement" button is exclusive to Admin and Dormitory Owner roles.

**New:**

> **Scenario:** A Dormitory Administrator/Owner composes and publishes an announcement to all tenants via the newsfeed on the admin dashboard.
>
> **Triggering Event:** The administrator types an announcement in the composition field on the admin dashboard and clicks the "Post" button.
>
> **Brief Description:** This use case allows administrators to post announcements visible to all tenants on the dashboard newsfeed. Announcements are posted and managed directly from the admin dashboard, and there is no separate Manage Announcements page. The "Post" button is exclusive to Admin and Dormitory Owner roles.

**Old:**

> Actor: 1. Click "Post Announcement."  
> System: 1.1. Display the announcement composition form.
>
> Actor: 2. Enter the announcement body and submit.  
> System: 2.1. Validate that the body is not empty. 2.1.1. If empty, display a validation error. 2.2. Save and publish the announcement to the newsfeed for all tenants.

**New:**

> Actor: 1. Navigate to the admin dashboard.  
> System: 1.1. Display the announcement composition field with the placeholder "Post an announcement to all tenants..." and a "Post" button.
>
> Actor: 2. Enter the announcement body and click "Post."  
> System: 2.1. Validate that the body is not empty. 2.1.1. If empty, display a validation error. 2.2. Save and publish the announcement to the newsfeed for all tenants.

---

### Manage Comments (Original Table 47, now Table 46)

**Old:**

> **Triggering Event:** The administrator clicks the Delete or Restrict Comments control on an announcement.

**New:**

> **Triggering Event:** The administrator clicks the Delete or Restrict comments control on an announcement in the admin dashboard newsfeed.

**Old:**

> Actor: 3. Click Restrict Comments on an announcement.  
> System: 3.1. Update the restriction status and disable the comment input for tenants on that post. 3.2. Display confirmation that comments have been restricted.
>
> Actor: 4. Click Restrict Comments again to re-enable (optional).  
> System: 4.1. Lift the restriction and re-enable the comment input for tenants on that post.

**New:**

> Actor: 3. Click Restrict comments on an announcement.  
> System: 3.1. Update the restriction status and disable the comment input for tenants on that post. 3.2. Display confirmation that comments have been restricted.
>
> Actor: 4. Click Unrestrict to re-enable comments (optional).  
> System: 4.1. Lift the restriction and re-enable the comment input for tenants on that post.

---

### Issue Eviction Notice (Original Table 51, now Table 50)

**Old:**

> **Scenario:** A Dormitory Administrator triggers Stage 6 of the delinquency escalation protocol, generating a formal Eviction Notice PDF and sending it to the tenant via SMS through textbee.dev.

**New:**

> **Scenario:** A Dormitory Administrator triggers Stage 6 of the delinquency escalation protocol, generating a formal Eviction Notice PDF and sending the tenant an SMS notice through textbee.dev.

**Old:**

> **Brief Description:** This use case covers the final escalation step where the administrator formally issues an eviction notice. The system generates a PDF containing the tenant's name, unit, reason, and date, then dispatches it via SMS using the textbee.dev SMS gateway.

**New:**

> **Brief Description:** This use case covers the final escalation step where the administrator formally issues an eviction notice. The system generates a PDF containing the tenant's name, unit, reason, and date, and stores it in the system. It then sends the tenant a short SMS through the textbee.dev SMS gateway stating that a formal eviction notice has been issued on the notice date and instructing the tenant to contact the dormitory administrator. The PDF itself is not sent by SMS, since an SMS cannot carry a file, and it remains available for the administrator to view and download.

**Old:**

> **Postconditions:** The eviction notice PDF is generated and stored. An SMS containing the notice or a reference to it is sent to the tenant. An Eviction Notice Issued event is recorded against the tenant's escalation history.
>
> **Exceptions:** textbee.dev SMS gateway is unavailable; system displays an error and logs the failed send attempt. PDF generation fails due to missing tenant data; system displays a validation error and halts the process. Tenant contact number is missing or invalid; system prompts the administrator to update the tenant's contact information before proceeding.

**New:**

> **Postconditions:** The eviction notice PDF is generated and stored. An SMS informing the tenant that a formal eviction notice has been issued is sent to the tenant. An Eviction Notice Issued event is recorded against the tenant's escalation history.
>
> **Exceptions:** textbee.dev SMS gateway is unavailable; system displays an error and logs the failed send attempt. PDF generation fails due to missing tenant data; system displays a validation error and halts the process. Tenant contact number is missing or invalid; the notice is still generated, and the system displays that the SMS could not be sent and logs it for retry.

**Old:**

> Actor: 2. Review and confirm the eviction notice details.  
> System: 2.1. Generate the formal Eviction Notice PDF using the retrieved tenant data. 2.2. Store the generated PDF and link it to the tenant's record.
>
> Actor: 3. Confirm sending the notice.  
> System: 3.1. Dispatch the eviction notice via SMS through the textbee.dev gateway. 3.1.1. If SMS send fails, log the error and display a failure message to the administrator. 3.2. Log an Eviction Notice Issued entry against the tenant's escalation history. 3.3. Display a confirmation message.

**New:**

> Actor: 2. Enter the Reason for Eviction and the Notice Date, then review the details.  
> System: 2.1. Generate the formal Eviction Notice PDF using the retrieved tenant data. 2.2. Store the generated PDF and link it to the tenant's record.
>
> Actor: 3. Click Confirm & Send Notice.  
> System: 3.1. Send an SMS through the textbee.dev gateway informing the tenant that a formal eviction notice has been issued and instructing them to contact the dormitory administrator. The PDF is not attached to the SMS. 3.1.1. If SMS send fails, log the error and display a failure message to the administrator. 3.2. Log an Eviction Notice Issued entry against the tenant's escalation history. 3.3. Display a confirmation message.

---
