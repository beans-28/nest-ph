### **Use Case Reports.**

Table 8  
**Use Case Report – Login**

| Use Case Name | Login |
| :---- | :---- |
| **Scenario** | Current tenants and Dormitory Administrator/Owners authenticate themselves using their registered email and password to access the NEST PH system and their respective dashboards. |
| **Triggering Event** | The user navigates to the login page to access the system's features. |

Continuation of **Table 8**

| Brief Description | This use case describes how users enter the system using their email and password. The system verifies the credentials and grants access if correct. Upon successful authentication, the user is redirected to their respective dashboard based on their role. Prospective tenants do not have system accounts and are not actors in this use case. |
| ----- | ----- |
| **Actors** | Current Tenant Dormitory Administrator/Owner |
| **Include Use Case** | Validate Credentials Retrieve User Role |
| **Extend Use Case** | Display Login Error |
| **Preconditions** | The user has a registered account in the system. The user knows their correct login credentials. The system is online and functioning correctly. |
| **Postconditions** | The user is logged in and directed to their respective dashboard. If unsuccessful, an error message is displayed and the user is prompted to retry. |
| **Exceptions** | Incorrect email or password entered; system displays a login error message. Account is locked after five consecutive failed login attempts; system displays a lockout message. System is offline or unavailable; login page cannot be accessed. |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Click Login In button. | 1.1. Display the login form. |
| 2\. Enter email and password. | 2.1. After five failed attempts, the system locks out the account. |

Continuation of **Table 8**

| 3\. Validate user credentials. | 3.1. If credentials are correct, retrieve the user's role and permissions. 3.2. If credentials are incorrect, display an error message. 3.3. If account is locked, display lockout message. |
| :---- | :---- |
| 4\. Redirect user to their respective dashboard based on role. |  |

Table 9  
**Use Case Report – Forgot Password**

| Use Case Name | Forgot Password |
| :---- | :---- |
| **Scenario** | A user who has forgotten their password initiates a password reset process by providing their registered email address to receive a verification code. |
| **Triggering Event** | The user clicks on the Forgot Password link from the login page. |
| **Brief Description** | This use case allows users to reset their forgotten passwords. The system sends a 6-digit reset code to their registered email, which they must enter before setting a new password. |
| **Actors** | Current Tenant Dormitory Administrator/Owner |
| **Include Use Case** | Enter Reset Password Code Enter New Password and Confirm It |
| **Extend Use Case** | Display Error if Email Not Found Resend Verification Code |
| **Preconditions** | The user has a registered account in the system. The user is on the login page. |

Continuation of **Table 9**

| Postconditions | The user successfully resets their password and regains access. If unsuccessful, an error message is displayed for incorrect or expired code. |
| ----- | ----- |
| **Exceptions** | Email address entered is not found in the system; system displays an error message. Email service is unavailable; system displays 'Try again later' message. Verification code entered is incorrect; system displays an error and prompts retry or resend. Verification code expires after 10 minutes; user must request a new code. New password does not meet security requirements; system displays a specific error. |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Click Forgot Password. | 1.1. Display the password reset form. |
| 2\. Enter registered email address and click Reset Password. | 2.1. Send verification code and display code entry form with 6 input fields. 2.1.1. Email address not found in system; display error message. 2.1.2. Email service unavailable; display 'Try again later' message. |
| 3\. Check email and retrieve verification code. | 3.1. After 10 minutes, the system invalidates the code. 3.1.1. User must request a new verification code. |
| 4\. Click the Resend option (if needed). | 4.1. Generate and send new verification code. |
| 5\. Enter 6-digit verification code. | 5.1. Validate verification code. 5.1.1. Invalid code entered; display error message. 5.1.2. User must request a new code or try again. |
| 6\. Click Continue. | 6.1. Display Set New Password form. |

Continuation of **Table 9**

| 7\. Enter new password and confirm it. | 7.1. Validate password requirements and update the password. 7.1.1. New password does not meet requirements; display error. |
| :---- | :---- |

Table 10  
**Use Case Report – Manage Account Settings**

| Use Case Name | Manage Account Settings |
| :---- | :---- |
| **Scenario** | A logged-in current tenant views their personal profile information and changes their password from their account settings page. Profile information is view-only for tenants, and corrections are requested by submitting a ticket to the Dormitory Administrator/Owner.  |
| **Triggering Event** | The user navigates to Account Settings from their profile menu. |
| **Brief Description** | This use case covers profile management within the account settings flow. Tenants can view their personal details (name, contact number, emergency contact) but cannot edit them directly. To request a correction, the tenant submits a ticket and the administrator updates the record. Tenants may also change their password.  |
| **Actors** | Current Tenant |
| **Include Use Case** | Validate Inputs Change Password (Tenant)  Save Changes  |
| **Extend Use Case** | Submit Ticket (Concern / Maintenance Request) |
| **Preconditions** | The user is logged into the system. |

Continuation of **Table 10**

| Postconditions | For tenants: profile information is displayed as view-only. If the tenant changes their password, the new password is saved. Corrections to profile information are requested through a submitted ticket and applied by the administrator.  |
| ----- | ----- |
| **Exceptions** | Required fields are left empty or contain invalid input; system displays a validation error.  Current password entered is incorrect, or the new password and its confirmation do not match; system displays a validation error.  System encounters a database error when saving changes; system displays an error message.  |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to Account Settings. | 1.1. Display current account details in view-only form (name, contact number, emergency contact, etc.). 1.2. Display a Submit a Ticket button for requesting corrections. |
| 2\. Click Submit a Ticket to request a correction to personal details (optional).  | 2.1. Redirect the tenant to the Tickets page, where the request is submitted as a ticket.  |
| 3\. Open the Change Password tab and enter the current password, new password, and confirmation.  | 3.1. Validate input fields. 3.1.1. Display error if the current password is incorrect or the new password and confirmation do not match.  |
| 4\. Click Update Password.  | 4.1. Save the new password.  4.2. Display confirmation 'Your password has been updated.'  |

Table 11  
**Use Case Report – Browse Rooms**

| Use Case Name | Browse Rooms |
| ----- | ----- |
| **Scenario** | A prospective tenant browses available rooms, views rates, amenities, policies, and availability, and optionally explores a VR/360 preview before deciding to proceed with inquiring. |
| **Triggering Event** | The prospective tenant navigates to the Rooms page. |
| **Brief Description** | This use case describes how a prospective tenant explores available dormitory rooms without needing a system account. The system displays room details including rates, amenities, policies, and real-time availability status. The prospective tenant may optionally view a VR/360 preview before choosing to proceed with inquiring. |
| **Actors** | Prospective Tenant |
| **Include Use Case** | Retrieve Room Listings Display Room Details |
| **Extend Use Case** | View VR Room Visualization |
| **Preconditions** | Room records exist in the system. The inquiry form is accessible. |
| **Postconditions** | Prospective tenant views room details and may proceed to the Inquiry Form. |
| **Exceptions** | No rooms are currently available; system displays a notice. System cannot retrieve room data due to a database error; system displays an error message. VR content is unavailable for the selected room; system displays standard photos as fallback. |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to the Rooms page. | 1.1. Retrieve and display all rooms with availability status, rates, amenities, and policies. |

Continuation of **Table 11**

| 2\. Select a specific room to view details. | 2.1. Display full room details including photos, rates, amenities, and availability. |
| :---- | :---- |
| 3\. Optionally click View VR/360 Preview. | 3.1. Load VR Room Visualization module. 3.1.1. If VR content is unavailable, display standard room photos as fallback. |
| 4\. Click Inquire/Apply to proceed. | 4.1. Display Inquiry form. |

Table 12  
**Use Case Report – Inquiry Form**

| Use Case Name | Inquiry Form |
| :---- | :---- |
| **Scenario** | A prospective tenant or any visitor submits an inquiry through the NEST PH public-facing website to ask questions about room availability, rates, policies, or any dormitory-related concerns. |
| **Triggering Event** | The visitor navigates to the Contact / Inquiry section of the NEST PH website and submits the inquiry form. |
| **Brief Description** | This use case describes how a visitor or prospective tenant sends an inquiry to the dormitory management through the public inquiry form. The visitor provides their name, contact number, email address, and inquiry message. The system saves the submission and notifies the Dormitory Administrator/Owner, who reviews and responds to the inquiry through their admin panel. |
| **Actors** | Prospective Tenant  Dormitory Administrator/Owner |
| **Include Use Case** | Validate Inputs   Notify Administrator |
| **Extend Use Case** | Send Email Acknowledgment to Visitor |
| **Preconditions** | The inquiry form page is accessible to the public. No system account is required. |

Continuation of **Table 12**

| Postconditions | Inquiry is saved in the system. The Dormitory Administrator/Owner is notified of the new inquiry. An optional acknowledgment email is sent to the visitor. Administrator may respond to the inquiry through the admin panel. |
| ----- | :---- |
| **Exceptions** | Required fields (name, email, or inquiry message) are left empty or contain invalid input; system displays a validation error and prevents submission.  Email address entered is in an invalid format; system displays a format error. Email service is unavailable when sending the acknowledgment; system logs the failure and continues without blocking the submission.  System encounters a database error when saving the inquiry; system displays an error message and prompts the visitor to retry. |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to the Contact / Inquiry section. | 1.1. Display the inquiry form with fields for name, contact number, email address, and inquiry message. |
| 2\. Fill out the inquiry form (name, contact number, email, and inquiry message). | 2.1. Validate required fields and email format in real time.  2.1.1. Display error if required fields are missing or the email format is invalid. |
| 3\. Click Submit Inquiry. | 3.1. Save the inquiry submission to the database with timestamp.  3.2. Notify the Dormitory Administrator/Owner of the new inquiry.  3.3. Send an email acknowledgment to the visitor confirming receipt of their inquiry.  3.4. Display confirmation message: 'Your inquiry has been submitted. We will get back to you shortly.' |
| \[Admin\] 4\. Navigate to the Inquiry Management section of the admin panel. | 4.1. Display a list of all submitted inquiries with visitor name, subject, date submitted, and status (New / Replied). |
| 5\. Select an inquiry to view the full message. | 5.1. Display full inquiry details including visitor name, contact number, email, and message. |

Continuation of **Table 12**

| 6\. Enter a reply message and click Send Reply. | 6.1. Save the reply and associate it with the inquiry record.  6.2. Update inquiry status to Replied.  6.3. Send the reply to the visitor's registered email address.  6.4. Display confirmation 'Reply sent successfully.' |
| :---- | :---- |

Table 13  
**Use Case Report – Apply for Occupancy**

| Use Case Name | Apply for Occupancy |
| :---- | :---- |
| **Scenario** | A prospective tenant submits a formal occupancy application through the public-facing NEST PH website, reviews and signs the dormitory contract electronically within the website, and submits their application. The Dormitory Administrator/Owner then screens the application and either approves, rejects, or requests re-application. Upon approval, the system automatically generates a tenant account and sends the login credentials to the applicant via email. |
| **Triggering Event** | The prospective tenant clicks the Apply for Occupancy button on the NEST PH public website. |
| **Brief Description** | This use case covers the occupancy application flow from the prospective tenant's perspective and the admin screening process. The tenant fills out the application form, provides personal and contact information with preferred room details, reviews the dormitory contract pre-filled with their details, draws their signature to sign it electronically, checks the consent checkbox, and submits the application. No printing, scanning, or uploading of a signed copy is required, and only the applicant signs the contract. The Dormitory Administrator/Owner reviews the submission and may approve, reject, or request re-application. The applicant is notified via email of the outcome. If approved, the system automatically generates a tenant account and sends the login credentials via email. |

Continuation of **Table 13**

| Actors | Prospective Tenant Dormitory Administrator/Owner |
| :---- | :---- |
| **Include Use Case** | Validate Inputs   Accept Data Privacy Consent   Display Dormitory Contract   Sign Contract Electronically   Notify Applicant via Email   Generate Tenant Account |
| **Extend Use Case** | Request Re-application |
| **Preconditions** | The Apply for Occupancy page is publicly accessible.  No system account is required for the prospective tenant.  At least one room/bedspace is available and not tagged as Reserved .  The dormitory contract document is available for download in the system. |
| **Postconditions** | Application is saved in the system with Pending Review status, and room/bedspace is automatically tagged as reserved. Administrator is notified of the new application.  Applicant receives an email notification of the screening outcome.  If approved: a tenant account is automatically generated and login credentials are sent to the applicant via email.  If rejected: applicant is notified with the rejection reason. If re-application is requested: applicant is notified and prompted to submit a new application. |

Continuation of **Table 13**

| Exceptions | Required fields (personal information, contact details, preferred room) are missing or contain invalid input; system displays a validation error and prevents progression.  Applicant does not check the consent checkbox; system prevents submission and displays 'You must consent to the data privacy notice before submitting.'  Applicant does not sign the dormitory contract; system prevents form submission.  Drawn signature cannot be read when signing the contract; system displays an error and prompts the applicant to sign again.  Selected room/bedspace becomes unavailable due to a concurrent submission; system notifies the applicant to select another.  Tenant account generation fails due to a system error after admin approval; system displays an error and retries.  Email notification fails to send; system logs the failure and retries. Selected room/bedspace is already tagged as Reserved; system notifies the applicant to select another available room/bedspace.  |
| ----- | :---- |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Click Apply for Occupancy. | 1.1. Redirect prospective tenant to the application form page.  1.2. Display the form in steps: Personal Information, Contact Information, and Room Information. |
| 2\. Proceed through the form steps using the Next and Back buttons. | 2.1. Validate the fields of each step before moving to the next step.  2.1.1. If a required field is missing, prevent progression to the next step. |

Continuation of **Table 13**

| 3\. Fill out the application form: personal information (first name, last name, date of birth, address), contact details (mobile number, email), preferred room information (room type, move-in date), and type of tenant (Student, Working Student, Full-time Employee, or Part-time Employee). | 3.1. Validate all required fields in real time. 3.1.1. Display error if any required field is missing or invalid. |
| :---- | :---- |
| 4\. Click Review & Sign Contract. | 4.1. Display the dormitory contract pre-filled with the applicant's details for review. 4.2. Display a signature pad for the applicant to draw their signature. |
| 5\. Draw signature, check the contract acceptance checkbox, and click Sign Contract. | 5.1. Generate the signed contract with the applicant's signature and attach it to the application.  5.1.1. If the signature cannot be read, display an error and prompt the applicant to sign again.  5.2. Display the signing date with a link to view the signed copy. |
| 6\. Upload a valid ID. | 6.1. Validate the uploaded file type and size.  6.1.1. Display error if the file is unsupported or exceeds the size limit. |
| 7\. Review the summary on the Verify Your Information step, check the consent checkbox confirming the accuracy of the information and agreement to the dormitory rules, and click Approve & Register. | 7.1. Validate contract acceptance, signed contract, and consent.  7.1.1. Prevent submission if any is missing.  7.1.2. Check if selected room/bedspace has an existing pending application; if so, notify applicant to select another.  7.2. Save application with Pending Review status and timestamp.  7.3. Tag selected room/bedspace as **Reserved**.  7.4. Notify administrator of new pending application. 7.5. Send acknowledgment email to applicant.  |
| \[Admin\] 8\. Navigate to the Application Management page. | 8.1. Display the list of pending applications with applicant name, preferred room, submission date, and status. |
| 9\. Select a pending application to review. | 9.1. Display full application details: personal information, contact details, preferred room, signed contract, and submission timestamp. |
| 10\. Review the application details and signed contract. |  |

Continuation of **Table 13**

| \[Admin approves\] 11\. Click Approve. | 11.1. Update application status to Approved.  11.2. Automatically generate a tenant account: assign Tenant ID, set temporary password, create billing record.  11.3. Send email to applicant with login credentials and instructions to proceed to the Pay Move-In Fees step.  11.4. Display confirmation 'Application approved. Tenant account created and credentials sent.' |
| :---- | :---- |
| \[Admin rejects\] 12\. Click Reject. | 12.1. Display rejection reason input field. |
| 13\. Enter rejection reason and click Confirm Rejection. | 13.1. Update status to Rejected.  13.2. Lift the Reserved tag, restoring room/bedspace availability.  13.3. Send rejection email to applicant.  13.4. Display confirmation “Application rejected. Applicant notified.”  |
| \[Admin requests re-application\] 14\. Click Request Re-application. | 14.1. Display re-application note input field. |
| 15\. Enter instructions or notes for re-application and click Confirm. | 15.1. Update status to Re-application Requested. 15.2. Lift the Reserved tag, restoring room/bedspace availability.  15.3. Send re-application instructions email to applicant.  15.4. Display confirmation “Re-application request sent to applicant.” |

Table 14  
**Use Case Report – Manage Tenant Records**

| Use Case Name | Manage Tenant Records |
| :---- | :---- |
| **Scenario** | A Dormitory Administrator/Owner views the list of all registered tenants, accesses individual tenant profiles, and updates tenant information such as personal details, contact number, emergency contact, tenant type, or uploaded documents.  |
| **Triggering Event** | The administrator navigates to the Tenant Management page. |
| **Brief Description** | This use case combines viewing and editing tenant records into a single management flow. The administrator retrieves the complete tenant list, applies search or filter options including by tenant type (Student, Working Student, Full-time Employee, or Part-time Employee), and accesses individual profiles with full lease and billing details. From a profile, the administrator can edit and update any tenant information as needed. |
| **Actors** | Dormitory Administrator/Owner |
| **Include Use Case** | Retrieve Tenant List Filter/Search Tenants Validate Inputs Save Updated Record |
| **Extend Use Case** | None |
| **Preconditions** | Administrator is logged in. Tenant records exist in the system. |
| **Postconditions** | Tenant list is displayed. Individual tenant profile is viewable with all relevant details. If edited, updated information is saved in the database. |
| **Exceptions** | No tenants match the applied search or filter criteria; system displays 'No records found.'  System cannot connect to the database to retrieve tenant records; system displays a connection error message.  Required fields are cleared or left empty during editing; system displays a validation error.  |

Continuation of **Table 14**

|  | Replacement document has an unsupported file type or exceeds the size limit; system rejects the upload.  System encounters a database error when saving changes; system displays an error message.  |
| ----- | :---- |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to Tenant Management. | 1.1. Retrieve complete list of tenants from the database. 1.2. Display tenants in list format with name, room, and status. |
| 2\. Apply search or filter (by name, room, tenant type, or status). | 2.1. Retrieve tenants matching the search/filter criteria. 2.2. Display filtered list. 2.2.1. If no tenants match, display 'No records found' message. 2.2.2. If the system cannot connect to the database, display an error. |
| 3\. Select a specific tenant from the list. | 3.1. Display the individual tenant profile including personal details, tenant type, assigned room, lease dates, and payment history. |
| 4\. Click Edit. | 4.1. Display the editable form pre-filled with current data. |
| 5\. Modify specific fields (first name, last name, contact number, emergency contact, tenant type,  or documents). | 5.1. Validate modified fields in real time. 5.1.1. Display error if required fields are cleared or invalid. |
| 6\. Replace documents if needed. | 6.1. Validate file type and size of new uploads. |
| 7\. Click Save Changes. | 7.1. Validate all inputs. 7.2. Save updated record to the database. 7.3. Display confirmation 'Record updated successfully.' |

Table 15  
**Use Case Report – Add New Tenant**

| Use Case Name | Add New Tenant |
| :---- | :---- |
| **Scenario** | A Dormitory Administrator/Owner manually creates a new tenant record in the system by entering the tenant's personal information, uploading required documents, assigning a room and bedspace, and setting lease details. This use case covers walk-in tenants or cases where the admin directly registers a tenant without going through the online application flow. |
| **Triggering Event** | The administrator clicks the Add New Tenant button from the Tenant Management page. |
| **Brief Description** | This use case describes how the Dormitory Administrator/Owner manually registers a new tenant into the NEST PH system. The admin encodes the tenant's personal details, uploads required documents (valid ID, contract), assigns a specific room and bedspace, sets the lease start and end dates, and submits the registration. The system generates tenant login credentials and sends them to the tenant's registered email address. The room/bedspace status is updated to Occupied upon successful registration. |
| **Actors** | Dormitory Administrator/Owner |
| **Include Use Case** | Validate Inputs   Assign Room/Bedspace   Save Tenant Record   Send Account Credentials via Email |
| **Extend Use Case** | None |
| **Preconditions** | Administrator is logged in.  An available room/bedspace exists in the system.  The tenant is not yet registered in the system. |

Continuation of **Table 15**

| Postconditions | Tenant record is created and saved in the database.  Room/bedspace status is updated to Occupied.  Tenant login credentials are generated and sent to the tenant's email address.  Tenant account status is set to Active immediately upon registration, and the tenant is included in the next billing cycle. |
| ----- | ----- |
| **Exceptions** | Required personal detail fields (first name, last name, email) are missing or invalid; system displays a specific validation error.  Uploaded document has an unsupported file type or exceeds the size limit; system rejects the file and displays an error.  No available rooms or bedspaces exist in the system; system displays 'No Available Rooms' message and prevents assignment.  Lease end date is set before the lease start date; system displays a validation error.  A tenant record with the same email address already exists in the system; system displays a duplicate record warning. Email service is unavailable when sending login credentials; system logs the failure and retries. |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to Tenant Management. | 1.1. Display the Tenant Management page with the existing tenant list. |
| 2\. Click Add New Tenant. | 2.1. Display the tenant registration form. |
| 3\. Enter tenant personal details: first name, last name, date of birth, home address, contact number, email address, and emergency contact details. | 3.1. Validate required fields in real time.  3.1.1. Display error if required fields are missing or invalid.  3.1.2. Check for duplicate email address. If a record with the same email already exists, display a duplicate record warning. |

Continuation of **Table 15**

| 4\. Select tenant type (Student, Working Student, Full-time Employee, or Part-time Employee). | 4.1. Save the selected tenant type to the tenant record. |
| :---- | :---- |
| 5\. Upload required documents (valid ID, signed contract, etc.). | 5.1. Validate file type and size for each uploaded document.  5.1.1. Display error if the file type is unsupported or exceeds the size limit. |
| 6\. Select available room and bedspace from the vacancy list. | 6.1. Display list of available rooms and bedspaces with room type and floor.  6.1.1. If no rooms are available, display 'No Available Rooms' message and prevent progression. |
| 7\. Set lease start date and lease end date. | 7.1. Validate lease dates.  7.1.1. Display error if the end date is before the start date or if either date is invalid. |
| 8\. Click Submit. | 8.1. Validate all inputs.  8.1.1. Display errors for any remaining invalid or missing fields.  8.2. Generate tenant account credentials (Tenant ID and temporary password).  8.3. Save tenant record to the database with Active status.  8.4. Update room/bedspace status to Occupied.  8.5. Send login credentials to the tenant's registered email address.  8.6. Display confirmation 'Tenant registered successfully. Login credentials sent to \[email address\].' |

Table 16  
**Use Case Report – Pay Move-In Fees**

| Use Case Name | Pay Move-In Fees |
| :---- | :---- |
| **Scenario** | An approved applicant logs into the system using the credentials sent upon application approval, accesses the billing module, and submits proof of their move-in fee payment for administrator verification. Upon successful verification, the tenant's account status is updated to Active and full system access is granted. |
| **Triggering Event** | The approved applicant logs in and is directed to the billing module. |
| **Brief Description** | This use case describes how an approved applicant completes their move-in payment using the same tenant account credentials sent to them upon application approval. The account is already created at that point — however, access is restricted to the billing module only until the move-in fee is settled. The system displays the computed total due (security deposit \+ 1 month advance rent). The applicant chooses Full Payment or Partial Payment, then selects one of the payment methods set up by the administrator (e-wallet or bank account), which displays the account details and a QR code if one was provided. Cash payments are recorded by the administrator instead. The applicant uploads proof payment (e.g., GCash screenshot, bank transfer confirmation) for administrator verification. The admin manually reviews the proof and confirms the payment. Once the move-in fee is fully paid, the tenant's account status is updated to Active and full system access — including payment history, monthly billing, maintenance ticketing, downloadable contract, and room assignment details — is unlocked. The system also generates and sends a move-in permit to the tenant via email. |
| **Actors** | Approved Applicant (Tenant)  Dormitory Administrator/Owner |
| **Include Use Case** | Compute Move-In Total   Display Billing Module   Select Payment Type   Select Payment Method   Verify Payment   Activate Tenant Account   Generate Move-In Permit |
| **Extend Use Case** | None |

Continuation of **Table 16**

| Preconditions | Applicant has been approved and has received their login credentials via email.  Applicant is logged in — account status is Pending Move-In Payment and access is restricted to the billing module only.  Billing record with the move-in fee breakdown exists in the system.  At least one payment method has been set up by the administrator. |
| ----- | ----- |
| **Postconditions** | Move-in payment proof is submitted and recorded. Admin manually verifies and confirms the payment.  Once the move-in fee is fully paid, tenant account status is updated to Active. Full system access is unlocked using the same login credentials.  Move-in permit is generated and sent to the tenant via email. |
| **Exceptions** | Proof of payment file has an unsupported type or exceeds size limit; system rejects the upload and displays an error.  Admin cannot verify payment due to invalid or unclear proof; admin rejects the proof with a reason, which is shown to the tenant, and the tenant uploads a new proof.  System encounters an error when updating tenant status to Active; system displays an error message and notifies the administrator.  Move-in permit generation fails due to a system error; system logs the failure and notifies the administrator. |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Log in using the credentials received via email upon application approval. | 1.1. Authenticate the user. 1.2. Detect that account status is Pending Move-In Payment. 1.3. Restrict access to the billing module only — all other system features remain inaccessible until the move-in fee is verified. |
| 2\. View the billing module and choose Full Payment or Partial Payment. | 2.1. Display the move-in fee breakdown: security deposit \+ 1 month advance rent \= total amount due.  2.2. Display the payment methods set up by the administrator (e-wallets and bank accounts). |
| 3\. Select a payment method, enter the amount sent if paying partially, and upload proof of payment (e.g., GCash screenshot, bank transfer confirmation). | 3.1. Display the account name, account number, and QR code (if provided) of the selected payment method.  3.2. Validate file type and size.  3.2.1. Display error if the file is unsupported or too large. |

Continuation of **Table 16**

| 4\. Submit payment proof. | 4.1. Save the payment submission and notify the administrator for verification.  4.2. Display message: 'Payment proof submitted. Awaiting admin verification.' |
| :---- | :---- |
| \[Admin\] 5\. Navigate to the pending payment submissions and review the submitted proof of payment. | 5.1. Display the submitted proof of payment alongside the move-in fee breakdown for the applicant. |
| 6\. Click Approve or Reject. | 6.1. If Approved and the move-in fee is fully paid: update tenant account status from Pending Move-In Payment to Active, unlock full system access (payment history, monthly billing, maintenance ticketing, downloadable contract, room assignment details) using the same login credentials, generate and send a move-in permit to the tenant via email, and display confirmation 'Payment verified. Tenant account activated and move-in permit issued.'  6.2. If Rejected: require the administrator to enter a reason for the rejection and mark the proof as rejected. The next time the tenant opens the move-in payment pages, display 'Your last proof of payment was not accepted.' with the reason, and prompt the tenant to submit a new proof of payment.  6.3. If Approved and the payment is partial: record the payment and keep the account status as Pending Move-In Payment until the remaining balance is paid. |

Table 17  
**Use Case Report – Manage Lease Contracts**

| Use Case Name | Manage Lease Contracts |
| :---- | :---- |
| **Scenario** | A Dormitory Administrator/Owner adds new lease contracts, monitors existing ones, receives automated expiry alerts, and renews or terminates contracts as needed. |
| **Triggering Event** | The administrator navigates to the Lease Management page. |
| **Brief Description** | This use case describes how the Dormitory Administrator/Owner manages the full lease contract lifecycle within NEST PH. The admin can add a new lease contract for a registered tenant, view and filter all existing contracts by status, and access individual contract details.  |

Continuation of **Table 17**

|  | Contracts nearing expiration are automatically flagged and the administrator is alerted. The admin may renew an active or expiring contract by setting a new end date, or terminate a contract by providing a reason. Renewals and terminations are logged with a timestamp, and the associated room/bedspace status is updated accordingly. |
| :---- | :---- |
| **Actors** | Dormitory Administrator/Owner |
| **Include Use Case** | Retrieve Contract List   Send Expiry Notification   Update Lease Status   Validate Inputs |
| **Extend Use Case** | Renew Lease Contract   Terminate Lease Contract   Upload Signed Contract Document |
| **Preconditions** | Administrator is logged in.  For viewing and managing: lease contract records exist in the system.  For adding: the tenant to be assigned already has an existing record in the system, and an available room/bedspace exists. |
| **Postconditions** | New lease contracts are saved and linked to the tenant's profile.  Existing contracts are updated (renewed or terminated).  Room/bedspace statuses reflect the latest lease state. All actions are logged with administrator ID and timestamp. |
| **Exceptions** | No contracts match the applied filter criteria; system displays 'No records found.' Required fields for a new lease (tenant, room/bedspace, start date, end date) are missing or invalid; system displays a validation error.  Lease end date is set before or on the start date; system displays a validation error.  Selected tenant already has an active lease; system displays a conflict warning and requires administrator confirmation to proceed.  |

Continuation of **Table 17**

|  | Selected room/bedspace already has an active lease for the overlapping period; system displays a conflict error and prevents submission.  Uploaded contract document has an unsupported file type or exceeds the size limit; system rejects the upload.  New end date during renewal is invalid or earlier than the current end date; system displays a validation error. No termination reason is provided; system blocks confirmation and prompts the administrator.  Automated expiry alert fails to send; system logs the failure and retries.  System encounters a database error during any save or update; system displays an error message. |
| ----- | ----- |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to Lease Management. | 1.1. Retrieve all lease contracts from the database. 1.2. Display contracts in a list with columns: Tenant Name, Room, Start Date, End Date, and Status (Active / Expiring Soon / Expired / Terminated). |
| 2\. Apply filter by status (Active / Expiring Soon / Expired / Terminated) or search by tenant name or room. | 2.1. Retrieve and display contracts matching the filter or search input.  2.1.1. If no contracts match, display 'No records found.' |
| \[To add\] 3\. Click Add Lease Contract. | 3.1. Display the Add Lease Contract form. |
| 4\. Search for and select the tenant. | 4.1. Display a searchable list of registered tenants. 4.2. Populate tenant details (name, contact number, email) upon selection. |
| 5\. Select the room and bedspace to be assigned. | 5.1. Display available rooms and bedspaces, excluding those with conflicting active leases for the intended period. |
| 6\. Set the lease start date and lease end date. | 6.1. Validate dates in real time.  6.1.1. Display error if the end date is before or on the start date, or if either date is invalid. |

Continuation of **Table 17**

| 7\. Upload the signed contract document (optional). | 7.1. Validate file type and size.  7.1.1. Display error if the file is unsupported or exceeds the size limit. |
| :---- | :---- |
| 8\. Click Submit. | 8.1. Validate all required fields.  8.1.1. Display errors for any missing or invalid fields. 8.2. Check for conflicting active lease for the selected tenant.  8.2.1. If a conflict is found, display warning: 'This tenant already has an active lease. Do you want to proceed?' and require administrator confirmation. 8.3. Check for room/bedspace conflicts during the specified period.  8.3.1. If the room/bedspace is already occupied for the overlapping period, display a conflict error and prevent submission.  8.4. Save the new lease contract record.  8.5. Update room/bedspace status to Occupied.  8.6. Link the contract to the tenant's profile.  8.7. Log the action with administrator ID and timestamp.  8.8. Display confirmation 'Lease contract added successfully.' |
| \[To view\] 9\. Select a lease contract from the list. | 9.1. Display full contract details: tenant name, room/bedspace, start date, end date, status, and attached contract document (if any). |
| \[To renew\] 10\. Click Renew Lease. | 10.1. Display the renewal form with the current end date pre-filled. |
| 11\. Enter the new end date and click Confirm Renewal. | 11.1. Validate the new end date — must be a valid future date and later than the current end date. 11.1.1. Display validation error if the date is invalid or earlier than the current end date.  11.2. Update the lease contract with the new end date.  11.3. Update lease status to Active if previously Expiring Soon or Expired.  11.4. Log the renewal with administrator ID and timestamp.  11.5. Display confirmation 'Lease renewed successfully.' |
| \[To terminate\] 12\. Click Terminate Lease. | 12.1. Prompt administrator to enter a termination reason. |

Continuation of **Table 17**

| 13\. Enter termination reason and click Confirm Termination. | 13.1. Validate that a reason has been entered. 13.1.1. Block confirmation and display error if reason field is empty.  13.2. Update lease status to Terminated.  13.3. Update the associated room/bedspace status to Available.  13.4. Log the termination with administrator ID, reason, and timestamp.  13.5. Display confirmation 'Lease terminated successfully.' |
| :---- | :---- |
| 14\. (Automated) System checks for contracts within 30 days of expiry on a scheduled basis. | 14.1. Flag affected contracts with Expiring Soon status.  14.2. Send expiry alert notification to the administrator.  14.2.1. If the alert fails, log the failure and retry at the next scheduled interval. |

Table 18  
**Use Case Report – Generate Billing Statement**

| Use Case Name | Generate Billing Statement |
| :---- | :---- |
| **Scenario** | The system automatically generates a monthly billing statement for each active tenant, computing rent, utility charges, and any applicable penalties. |
| **Triggering Event** | The billing cycle date is reached for an active tenant. |
| **Brief Description** | This use case describes how the system automatically generates a monthly billing statement on the billing cycle date. It computes base rent, distributes utility charges, applies any applicable late payment penalties, and notifies the tenant. |
| **Actors** | System (Automated) Dormitory Administrator/Owner |
| **Include Use Case** | Compute Rental Fee Add Fixed Utility Share Apply Penalties |

Continuation of **Table 18**

| Extend Use Case | Send Billing Notification to Tenant |
| ----- | ----- |
| **Preconditions** | Tenant is actively occupying a room with Active status. Billing cycle date has been reached. |
| **Postconditions** | Billing statement is generated and stored. Tenant is notified. Outstanding balance is updated. |
| **Exceptions** | Utility charge data is incomplete or missing for the billing period; system flags the discrepancy and notifies the administrator. SMS notification service is unavailable when sending the billing alert; system logs the failure and retries. System encounters an error when saving the billing statement; system displays an error and retries the generation. |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. System detects that the billing cycle date has been reached for an active tenant. |  |
| 2\. Compute base rent for the tenant's assigned room. |  |
| 3\. Add the fixed utility charges among tenants sharing the same floor/facility. |  |
| 4\. Check for existing unpaid balance and apply late payment penalty if applicable. |  |
| 5\. Finalize and generate the billing statement. |  |
| 6\. Save the billing statement to the tenant's payment history. |  |

Continuation of **Table 18**

| 7\. Update tenant's outstanding balance. |  |
| :---- | :---- |
| 8\. (Automated) Send billing notification to tenant via system portal and SMS. | 8.1. If SMS service is unavailable, log the failure and retry. |
| 9\. Administrator can view generated statements from the billing management page. |  |

Table 19  
**Use Case Report – Record Cash Payment**

| Use Case Name | Record Cash Payment |
| :---- | :---- |
| **Scenario** | A Dormitory Administrator/Owner manually records a cash payment received from a tenant, updates the tenant's outstanding balance, and issues a payment receipt. |
| **Triggering Event** | The administrator clicks the Record Cash Payment button on the tenant's billing profile. |
| **Brief Description** | This use case describes how the Dormitory Administrator/Owner manually encodes a cash payment received from a tenant, validates the amount, updates the outstanding balance in real time, generates a payment receipt, and reverses any active delinquency escalation stage if the payment fully settles the balance. |
| **Actors** | Dormitory Administrator/Owner |
| **Include Use Case** | Validate Payment Amount Update Tenant Balance |
| **Extend Use Case** | Generate Payment Receipt Reverse Delinquency Escalation Stage |
| **Preconditions** | Administrator is logged in. Tenant has an outstanding balance in the system. |

Continuation of **Table 19**

| Postconditions | Payment is recorded with timestamp. Tenant balance is updated. Receipt is generated. If balance is fully settled and tenant was under escalation, escalation stage is reversed accordingly. |
| ----- | ----- |
| **Exceptions** | Payment amount entered is zero, negative, or exceeds the outstanding balance beyond policy limits; system displays a validation error. System encounters a database error when updating the tenant balance; system displays an error message. Receipt generation fails due to a system error; system logs the failure and notifies the administrator. |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to the tenant's billing profile. | 1.1. Display tenant's outstanding balance and billing history. |
| 2\. Click Record Cash Payment. | 2.1. Display payment entry form. |
| 3\. Enter payment amount and date received. | 3.1. Validate amount (must be positive and not exceed outstanding balance by policy). 3.1.1. Display error if amount is invalid. |
| 4\. Click Submit. | 4.1. Update tenant's outstanding balance. 4.2. Record payment with timestamp. 4.3. Check if balance is fully settled. 4.3.1. If tenant was under active delinquency escalation, reverse the escalation stage accordingly. 4.4. Generate payment receipt. 4.5. Display confirmation 'Payment recorded successfully.' |

Table 20  
**Use Case Report – Process Online Payment Proof**

| Use Case Name | Process Online Payment Proof |
| ----- | :---- |
| **Scenario** | A tenant uploads proof of payment through the portal. The Dormitory Administrator/Owner manually reviews the submission and approves or rejects it to update the tenant's account accordingly. |
| **Triggering Event** | The tenant clicks the Submit Payment Proof button from the billing dashboard or the restricted payment page (Stage 3 escalation). |
| **Brief Description** | This use case describes how an active tenant settles their outstanding monthly balance by uploading proof of payment (e.g., GCash screenshot, bank transfer confirmation) through the tenant portal. The system automatically validates the file and notifies the administrator. The administrator then independently reviews the submission and either approves or rejects it. Upon approval, the outstanding balance is updated and an electronic receipt is issued. If applicable, any active Stage 3 portal restriction is lifted. |
| **Actors** | Current Tenant Dormitory Administrator/Owner |
| **Submit Payment Proof  (Tenant)** |  |
| **Use Case Name** | Submit Payment Proof |
| **Include Use Case** | Validate Upload  Notify Administrator  |
| **Extend Use Case** | None |
| **Preconditions** | Tenant is logged in or accessing via restricted payment link.  Tenant has an outstanding balance.  |
| **Postconditions** | Payment proof file is uploaded, validated, and recorded. Administrator is automatically notified for manual review. |

Continuation of **Table 20**

| Exceptions | File has an unsupported type or exceeds size limit; system rejects the upload and displays an error. System fails to send notification to administrator; system logs the error and retries automatically. |
| ----- | :---- |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to billing dashboard or access payment link (even if portal is restricted).  | 1.1. Display outstanding balance and the Submit Payment Proof option.  |
| 2\. Click Submit Payment Proof.  | 2.1. Display proof of payment upload form.  |
| 3\. Upload proof of payment (e.g., GCash screenshot, bank transfer confirmation).  | 3.1. Validate file type and size. 3.1.1. Display error if file is unsupported or too large.   |
| 4\. Confirm and submit the uploaded proof.  | 4.1. Save the submission and record it with a timestamp. 4.2. Automatically notify the administrator for manual review. 4.3. Display 'Payment proof submitted. Awaiting admin verification.''  |
| **Review Payment Submission  (Admin)** |  |
| **Use Case Name** | Review Payment Submission |
| **Include Use Case** | None |
| **Extend Use Case** | Generate Electronic Receipt Reverse Portal Restriction (Stage 3 Escalation) |
| **Preconditions** | Tenant has completed Submit Payment Proof. Administrator is logged in and has access to the payment review panel. |
| **Postconditions** | If Approved: Tenant's outstanding balance is updated, electronic receipt is issued, and portal restriction (if active) is lifted. If Rejected: Tenant sees the rejection reason on their billing page and is prompted to resubmit. |

Continuation of **Table 20**

| Exceptions | If Approved: Tenant's outstanding balance is updated, electronic receipt is issued, and portal restriction (if active) is lifted. If Rejected: Tenant is notified and prompted to resubmit. |
| ----- | :---- |
| **Flow of Events** |  |
| **Actor** | **System** |
| 5\. Receive system notification of a new payment submission. | 5.1. Display the submitted proof of payment alongside the tenant's outstanding balance.  |
| 6\. Open and review the submitted proof of payment.  | 6.1. Display the proof of payment file and submission details.  |
| 3\. Upload proof of payment (e.g., GCash screenshot, bank transfer confirmation).  | 3.1. Validate file type and size. 3.1.1. Display error if file is unsupported or too large.   |
| 7\. Click Approve or Reject.  | 7.1. If Approved: Update tenant's outstanding balance. Record payment with timestamp. Generate and send electronic receipt to tenant. If Stage 3 restriction is active, lift restriction and restore full portal access. 7.2. If Rejected: Require the administrator to enter a reason for the rejection. On the tenant's billing page, mark the bill with 'Proof rejected' and, when the tenant opens the payment screen, display 'Your last proof of payment was not accepted.' with the reason. Prompt tenant to resubmit. |

Table 21  
**Use Case Report – View Payment History**

| Use Case Name | View Payment History |
| :---- | :---- |
| **Scenario** | A tenant or Dormitory Administrator/Owner views the complete payment transaction history for a specific tenant account. |
| **Triggering Event** | The user navigates to the Payment History section of the billing page. |

Continuation of **Table 21**

| Brief Description | This use case describes how a tenant views their own payment history, or how a Dormitory Administrator/Owner views any tenant's complete transaction history, including payment dates, amounts, methods, and outstanding balance records. |
| ----- | :---- |
| **Actors** | Current Tenant Dormitory Administrator/Owner |
| **Include Use Case** | Retrieve Payment Records Filter Transactions |
| **Extend Use Case** | None |
| **Preconditions** | User is logged in. Payment records exist for the tenant. |
| **Postconditions** | Payment history is displayed in chronological order. |
| **Exceptions** | No payment records exist for the selected tenant; system displays an empty state or appropriate message. No transactions match the applied date or status filter; system displays 'No transactions found.' System cannot connect to the database to retrieve payment records; system displays a connection error. |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to Billing / Payment section. | 1.1. Display payment history page. |
| 2a. (For admin) Search and select the specific tenant. | 2.1. Retrieve all payment records for the selected tenant. |
| 2b. (For tenant) Access own billing page. | 2.1. Retrieve own payment records. |
| 3\. Display full payment history in chronological order (date, amount, method, balance after payment). |  |

Continuation of **Table 21**

| 4\. Apply date or status filters (optional). | 4.1. Retrieve and display filtered results. 4.1.1. If no records match the filter, display 'No transactions found.' |
| :---- | :---- |
| 5\. Select a specific transaction to view details. | 5.1. Display full transaction details including reference number. |

Table 22  
**Use Case Report – Trigger Delinquency Escalation –**   
**Stage 1: Automated Account Flagging**

| Use Case Name | Trigger Delinquency Escalation – Stage 1: Automated Account Flagging |
| :---- | :---- |
| **Scenario** | The system automatically detects a missed payment deadline, marks the tenant's account as Payment Overdue, timestamps the event, and initiates the formal escalation record. |
| **Triggering Event** | The payment due date passes without a recorded payment from the tenant. |
| **Brief Description** | This use case describes the automatic initiation of the delinquency escalation protocol. Once a tenant misses their payment deadline, the system flags the account as Payment Overdue, timestamps the missed payment, starts the overdue countdown, and creates the initial escalation audit log entry. |
| **Actors** | System (Automated) |
| **Include Use Case** | Detect Missed Payment Create Escalation Log Entry |
| **Extend Use Case** | Notify Administrator of Flagged Account |
| **Preconditions** | Tenant has an active billing cycle. Payment due date has passed without a recorded payment. |
| **Postconditions** | Account is flagged as Payment Overdue. Overdue countdown begins. Escalation log entry is created with timestamp. Administrator is notified. |

Continuation of **Table 22**

| Exceptions | Automated notification to the administrator fails; system logs the failure and retries. Billing record cannot be accessed due to a database connection error; system logs the issue and retries the check at the next scheduled interval. Payment is recorded concurrently as the system checks, causing a race condition; system re-validates before applying the overdue flag. |
| ----- | :---- |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. System checks billing records at the end of each payment due date. |  |
| 2\. System detects that no payment has been recorded for the tenant by the due date. |  |
| 3\. System flags the tenant account status as 'Payment Overdue.' |  |
| 4\. System timestamps the missed payment event. |  |
| 5\. System begins the overdue countdown. |  |
| 6\. System creates an escalation log entry (Stage 1\) with tenant ID, overdue amount, due date, and timestamp. |  |
| 7\. System sends an automated notification to the administrator of the flagged account. | 7.1. If notification fails, log the failure and retry. |
| 8\. If payment is received before Stage 2 triggers, system removes the overdue flag and closes the escalation record. |  |

Table 23  
**Use Case Report – Send Automated SMS Reminders –**   
**Stage 2: Graduated SMS Notifications**

| Use Case Name | Send Automated SMS Reminders – Stage 2: Graduated SMS Notifications |
| :---- | :---- |
| **Scenario** | The system sends a sequence of automated SMS reminders to the delinquent tenant on scheduled days with increasing urgency, logging each message with delivery status. |
| **Triggering Event** | Stage 1 (Account Flagging) is active and the scheduled reminder day is reached (Day 2, Day 4, Day 7). |
| **Brief Description** | This use case describes how the system sends a sequence of automated SMS reminders to a tenant whose account remains overdue after Stage 1 flagging. Messages are sent on scheduled days with escalating urgency about penalties, and each message is timestamped and logged with delivery status. |
| **Actors** | System (Automated) |
| **Include Use Case** | Check Overdue Countdown Send SMS via Gateway |
| **Extend Use Case** | Log Delivery Status |
| **Preconditions** | Account is flagged as Payment Overdue (Stage 1 active). Tenant has a registered mobile number in the system. Payment has not been received. |
| **Postconditions** | SMS reminders are sent on scheduled days. Each message is logged with timestamp and delivery status. Escalation log is updated. |
| **Exceptions** | SMS gateway is unavailable when sending a reminder; system logs the failure and retries after 30 minutes. Tenant's registered mobile number is invalid or inactive; system logs the error and notifies the administrator. |

Continuation of **Table 23**

|  | Payment is received between reminders; system cancels all remaining scheduled reminders and closes the escalation at Stage 2\. |
| ----- | :---- |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. System checks overdue countdown on scheduled days (Day 2, Day 4, Day 7). |  |
| 2\. System verifies that the account is still flagged as Payment Overdue and no payment has been received. |  |
| 3\. System composes the SMS reminder message including overdue amount, accrued penalties, and payment instructions. |  |
| 4\. System sends the SMS via the SMS gateway. | 4.1. If SMS gateway is unavailable, log failure and retry after 30 minutes. 4.2. If tenant mobile number is invalid, log the error and notify the administrator. |
| 5\. System logs the sent message with timestamp and delivery status (Sent / Delivered / Failed). |  |
| 6\. System updates the escalation log entry (Stage 2\) with the reminder details. |  |
| 7\. If payment is received between reminders, system cancels remaining reminders and closes the escalation at Stage 2\. |  |

Table 24  
**Use Case Report – Restrict Tenant Portal Access –**   
**Stage 3: Automatic Portal Restriction**

| Use Case Name | Restrict Tenant Portal Access – Stage 3: Automatic Portal Restriction |
| :---- | :---- |
| **Scenario** | The system automatically restricts the delinquent tenant's portal access except for the direct payment link, enforcing a system-level consequence to prompt payment settlement. |
| **Triggering Event** | Stage 2 SMS reminders have been sent and the account remains unpaid after Day 7\. |
| **Brief Description** | This use case describes how the system automatically restricts a delinquent tenant's portal access when SMS reminders have been ignored. The tenant is blocked from accessing billing records, documents, and ticket submissions, while only the direct payment link remains accessible. |
| **Actors** | System (Automated) |
| **Include Use Case** | Apply Portal Restriction Flag Update Escalation Log |
| **Extend Use Case** | Notify Tenant of Restriction |
| **Preconditions** | Stage 2 reminders have been sent and ignored. Payment remains outstanding after Day 7\. |
| **Postconditions** | Tenant portal access is restricted to the payment link only. Restriction is logged with timestamp. Tenant is notified of the restriction. |
| **Exceptions** | Notification to the tenant regarding the portal restriction fails; system logs the failure and notifies the administrator. Portal restriction flag cannot be applied due to a system error; system logs the issue and retries. Payment is received while the restriction is being applied; system re-validates and cancels the restriction if the balance is cleared. |

Continuation of **Table 24**

| Flow of Events |  |
| ----- | :---- |
| **Actor** | **System** |
| 1\. System verifies that Stage 2 conditions are complete and payment is still outstanding. |  |
| 2\. System applies a portal restriction flag to the tenant's account. |  |
| 3\. System restricts access to all portal features except the direct payment link. |  |
| 4\. System logs the restriction action with timestamp (Stage 3 escalation log entry). |  |
| 5\. System sends a notification to the tenant informing them of the restriction and providing instructions to resolve it via the payment link. | 5.1. If notification fails, log the failure and notify the administrator. |
| 6\. Administrator is notified of the Stage 3 restriction for the tenant. |  |
| 7\. If tenant accesses the portal, they see only the payment portal with a message: 'Your account access has been restricted due to unpaid balance. Please settle your balance to restore full access.' |  |
| 8\. Upon successful payment, system automatically lifts the portal restriction and restores full access. |  |

Table 25  
**Use Case Report – Notify Emergency Contact –**   
**Stage 4: Emergency Contact Notification**

| Use Case Name | Notify Emergency Contact – Stage 4: Emergency Contact Notification |
| :---- | :---- |
| **Scenario** | The system automatically notifies the tenant's registered emergency contact about the prolonged non-payment, introducing an additional accountability layer. |
| **Triggering Event** | Portal restriction (Stage 3\) has been active and the account remains unresolved. |
| **Brief Description** | This use case describes how the system automatically contacts the tenant's registered emergency contact when the account remains unresolved after Stage 3\. The notification includes the debt status and a summary of the communication gap to introduce an additional layer of accountability. |
| **Actors** | System (Automated) |
| **Include Use Case** | Retrieve Emergency Contact Details Send Notification |
| **Extend Use Case** | Log Notification |
| **Preconditions** | Portal restriction (Stage 3\) is active. Payment has not been settled. Emergency contact information is registered in the system. |
| **Postconditions** | Emergency contact is notified via SMS and/or email. Notification is logged with timestamp. Escalation record is updated to Stage 4\. |
| **Exceptions** | Emergency contact details are missing or invalid in the system; system notifies the administrator to update the record before proceeding. SMS or email delivery to the emergency contact fails; system logs the failure and retries. |

Continuation of **Table 25**

|  | System encounters an error when updating the escalation log to Stage 4; system logs the error and alerts the administrator. |
| ----- | :---- |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. System verifies that Stage 3 conditions are active and payment remains outstanding. |  |
| 2\. System retrieves the tenant's registered emergency contact details. | 2.1. If emergency contact details are missing or invalid, notify the administrator to update the record before proceeding. |
| 3\. System composes the notification message including tenant name, overdue amount, duration overdue, and dormitory contact information. |  |
| 4\. System sends the notification via SMS and/or email to the emergency contact. | 4.1. If delivery fails, log failure and retry. 4.2. Log delivery status. |
| 5\. System updates the escalation log to Stage 4 with timestamp. |  |
| 6\. Administrator is notified that Stage 4 notification has been sent. |  |
| 7\. If payment is received after Stage 4, system closes the escalation and lifts all restrictions. |  |

Table 26  
**Use Case Report – Generate Formal Demand Letter –**   
**Stage 5: Auto-Generated Demand Letter**

| Use Case Name | Generate Formal Demand Letter – Stage 5: Auto-Generated Demand Letter |
| :---- | :---- |
| **Scenario** | The system automatically produces a formal demand letter documenting the tenant's financial obligation and full escalation history for use in formal dispute resolution. |
| **Triggering Event** | Stage 4 emergency contact notification has been sent and the account remains unresolved. |
| **Brief Description** | This use case describes how the system automatically generates a formal demand letter when all prior escalation stages have failed to resolve the overdue balance. The letter documents the tenant's full financial obligation, escalation history, and final payment deadline, and is made available for the administrator to download. |
| **Actors** | System (Automated) Dormitory Administrator/Owner |
| **Include Use Case** | Compile Escalation History Generate Demand Letter Document |
| **Extend Use Case** | None |
| **Preconditions** | Stages 1 through 4 have been triggered. Payment remains outstanding. |
| **Postconditions** | Formal demand letter is generated and stored in the system. Administrator is notified and can download or print the letter. Escalation record is updated to Stage 5\. |
| **Exceptions** | Escalation history data is incomplete or corrupted; system alerts the administrator and pauses letter generation until records are verified. Tenant or lease information required for the letter is missing; system notifies the administrator to complete the records. Demand letter PDF generation fails due to a system error; system logs the failure and notifies the administrator. |

Continuation of **Table 26**

|  | Administrator notification fails; system logs the failure and retries. |
| ----- | :---- |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. System verifies that Stages 1–4 are complete and payment is still outstanding. |  |
| 2\. System compiles the full escalation history including all timestamps, messages sent, and actions taken. |  |
| 3\. System retrieves tenant details, outstanding balance, and lease information. |  |
| 4\. System generates the formal demand letter document including: tenant name and contact details, overdue amount with breakdown, escalation timeline, a final payment deadline, and a notice that the matter may be brought before the Barangay for conciliation under the Katarungang Pambarangay Law if the balance remains unpaid after the deadline. |  |
| 5\. System saves the generated letter to the tenant's record. |  |
| 6\. System updates the escalation log to Stage 5 with timestamp. |  |
| 7\. System notifies the administrator that the demand letter has been generated and is available for download. |  |
| 8\. Administrator navigates to the tenant's escalation record and clicks Download Demand Letter. | 8.1. Generate and serve the demand letter PDF for download. |

Table 27  
**Use Case Report – Flag Tenant as Delinquent and Blacklist –**   
**Stage 6: Internal Delinquency Record**

| Use Case Name | Flag Tenant as Delinquent and Blacklist – Stage 6: Internal Delinquency Record |
| :---- | :---- |
| **Scenario** | The system permanently flags a persistently non-compliant tenant as Delinquent and adds them to the blacklist for future reference by Dormitory Administrator/Owners. |
| **Triggering Event** | All prior escalation stages (1–5) have been exhausted and payment remains unresolved. |
| **Brief Description** | This use case describes the final stage of the delinquency escalation protocol. The system permanently flags the tenant as Delinquent, creates a blacklist entry with the full escalation history, and restricts future inquiry form submissions from the tenant. The administrator is notified and the full audit trail is finalized. |
| **Actors** | System (Automated) Dormitory Administrator/Owner |
| **Include Use Case** | Apply Delinquent Flag Create Blacklist Entry |
| **Extend Use Case** | None |
| **Preconditions** | Stages 1 through 5 have all been triggered. Payment remains unresolved. |
| **Postconditions** | Tenant account is permanently flagged as Delinquent. Blacklist record is created with full escalation audit trail. Administrator is notified. Future inquiry form submissions from this tenant are blocked. |
| **Exceptions** | One or more prior escalation stages are incomplete or unverified; system blocks Stage 6 from triggering and notifies the administrator. Blacklist entry creation fails due to a database error; system logs the error and alerts the administrator. Administrator notification fails; system logs the failure and retries. |

Continuation of **Table 27**

| Flow of Events |  |
| ----- | ----- |
| **Actor** | **System** |
| 1\. System verifies all prior escalation stages are complete and payment is still unresolved. |  |
| 2\. System applies the permanent 'Delinquent' flag to the tenant's account. |  |
| 3\. System creates a blacklist entry containing: tenant name and contact details, total amount owed, full escalation history with timestamps, and all supporting documents. |  |
| 4\. System restricts the tenant from making future inquiry form submissions. |  |
| 5\. System finalizes the full escalation audit trail. |  |
| 6\. System updates the escalation log to Stage 6 with timestamp. |  |
| 7\. System notifies the administrator that the tenant has been flagged as Delinquent and added to the blacklist. |  |
| 8\. Administrator can view the blacklist from the Tenant Management page. | 8.1. Display blacklist with delinquent records filtered. |

Table 28  
**Use Case Report – Override Delinquency Escalation Stage**

| Use Case Name | Override Delinquency Escalation Stage |
| :---- | :---- |
| **Scenario** | A Dormitory Administrator/Owner manually overrides or resets a delinquency escalation stage for a tenant when special circumstances arise, such as a confirmed payment arrangement. |
| **Triggering Event** | The administrator navigates to a tenant's escalation record and clicks Override Escalation Stage. |
| **Brief Description** | This use case describes how the Dormitory Administrator/Owner can manually override, pause, or reset the current delinquency escalation stage of a tenant. This action is logged for full audit accountability, and the tenant is notified of any resulting changes. |
| **Actors** | Dormitory Administrator/Owner |
| **Include Use Case** | Log Override Action |
| **Extend Use Case** | Notify Tenant of Override |
| **Preconditions** | Administrator is logged in with proper access rights. Tenant account is under active delinquency escalation. |
| **Postconditions** | Escalation stage is overridden or reset as specified. Override action is logged with administrator ID, reason, and timestamp. Tenant is notified if applicable. |
| **Exceptions** | No reason is provided for the override; system disables the confirm button and displays a validation error. System encounters an error when applying the override to the escalation record; system displays an error and retains the previous escalation state. Tenant notification fails after the override is applied; system logs the failure and notifies the administrator. |

Continuation of **Table 28**

| Flow of Events |  |
| ----- | :---- |
| **Actor** | **System** |
| 1\. Navigate to the tenant's escalation record. | 1.1. Display current escalation stage and history. |
| 2\. Click Override Escalation Stage. | 2.1. Display override form with options: Pause, Reset to Stage 1, or Clear Escalation. |
| 3\. Select override action and enter reason. | 3.1. Validate that a reason has been provided. 3.1.1. If reason is empty, disable confirm button and display error. |
| 4\. Click Confirm Override. | 4.1. Apply the selected override to the escalation record. 4.2. Log the override action with administrator ID, action type, reason, and timestamp. 4.3. Notify the tenant of the change if applicable. 4.4. Display confirmation 'Escalation stage overridden successfully.' |

Table 29  
**Use Case Report – View Vacancy Monitoring Dashboard**

| Use Case Name | View Vacancy Monitoring Dashboard |
| :---- | :---- |
| **Scenario** | A Dormitory Administrator/Owner or prospective tenant views the real-time vacancy dashboard displaying the current status of all rooms and bedspaces (Available, Reserved Visitor, Reserved Applicant / Pending Review, Approved / Pending Move-In Payment, Occupied). |
| **Triggering Event** | The user navigates to the Vacancy Dashboard page. |
| **Brief Description** | This use case describes how the Dormitory Administrator/Owner or prospective tenant accesses the real-time vacancy monitoring dashboard. The system retrieves current occupancy statuses for all rooms and individual bedspaces and displays them in a visual, color-coded interface. Bedspaces that are Reserved Visitor or Reserved Applicant are locked and not selectable for new inquiries. |

Continuation of **Table 29**

| Actors | Dormitory Administrator/Owner, Prospective Tenant |
| ----- | :---- |
| **Include Use Case** | Retrieve Room Status Display Room Manifest |
| **Extend Use Case** | None |
| **Preconditions** | User is logged in (administrator) or browsing as a prospective tenant (no account required). Room records exist in the system. |
| **Postconditions** | Real-time vacancy dashboard is displayed with accurate and up-to-date room and bedspace statuses. |
| **Exceptions** | System cannot connect to the database to retrieve room statuses; system displays a connection error message. VR room content is unavailable for the selected room; system displays standard room photos as a fallback. Room data is outdated due to a synchronization delay; system indicates the display may not reflect the latest status. |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to the Vacancy Dashboard. | 1.1. Retrieve real-time room and bedspace statuses from the database. 1.1.1. If the system cannot connect to the database, display an error message. |
| 2\. View the vacancy dashboard with color-coded status indicators: Green: Available Yellow: Reserved Applicant (Pending Review) Orange: Approved / Pending Move-In Payment Red: Occupied |  |

Continuation of **Table 29**

| 3\. Click on a specific room to view bedspace-level details. | 3.1. Display individual bedspace statuses within the selected room. 3.1.1. Bedspaces with Reserved or Approved status are not selectable for new inquiries. |
| :---- | :---- |
| 4\. (Prospective Tenant) View VR visualization of the room (optional). | 4.1. Load VR room viewing module for the selected room. |
| 5\. (Prospective Tenant) Click Inquire/Apply on an Available bedspace/room. | 5.1. Redirect to the Inquiry Form use case. |

Table 30  
**Use Case Report – View VR Room Visualization**

| Use Case Name | View VR Room Visualization |
| :---- | :---- |
| **Scenario** | A prospective tenant launches the VR Room Viewing module to interactively explore the 360-degree virtual representation of an available dormitory room before deciding to submit an inquiry form. |
| **Triggering Event** | The prospective tenant clicks the View Room (VR) button from the vacancy dashboard or room listing page. |
| **Brief Description** | This use case describes how a prospective tenant accesses and navigates the VR Room Viewing module within their browser. The system loads a 360-degree interactive virtual environment of the selected room, allowing the tenant to assess the room before proceeding to the inquiry form. No system account is required. |
| **Actors** | Prospective Tenant |
| **Include Use Case** | Load VR Room Environment |
| **Extend Use Case** | Proceed to Inquiry Form |

Continuation of **Table 30**

| Preconditions | VR room content has been uploaded by the administrator. The prospective tenant is viewing a room or bedspace listing. |
| ----- | :---- |
| **Postconditions** | The user successfully explores the virtual room environment. The user may proceed to the inquiry form or exit the VR view. |
| **Exceptions** | No VR content has been uploaded for the selected room; system displays standard room photos as a fallback. Device or browser does not support VR rendering; system displays a compatibility warning and offers standard photos instead. VR environment fails to load due to a network or file error; system displays an error and offers the fallback photo view. |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Click View Room (VR) from the room listing or vacancy dashboard. | 1.1. Check if VR content is available for the selected room. 1.1.1. If no VR content is available, display standard room photos as fallback. |
| 2\. Load the 360-degree VR room environment in the browser. | 2.1. Display a loading indicator while the VR environment loads. 2.2. If the device or browser does not support VR, display a compatibility warning and offer standard photos instead. |
| 3\. Navigate the virtual room using on-screen controls (pan, zoom, look around). | 3.1. Render the VR environment based on user interaction. |
| 4\. Exit VR view when done. | 4.1. Return the user to the room listing or vacancy dashboard. |
| 5\. Click Inquire/Apply from within or after the VR view (optional). | 5.1. Redirect to the Inquiry Form use case. |

Table 31  
**Use Case Report – Manage VR Room Content**

| Use Case Name | Manage VR Room Content |
| :---- | :---- |
| **Scenario** | A Dormitory Administrator/Owner uploads, updates, or deletes the 360-degree VR room content for specific rooms to keep the virtual viewing experience accurate and current. |
| **Triggering Event** | The administrator navigates to the VR Content Management page. |
| **Brief Description** | This use case describes how the Dormitory Administrator/Owner manages the VR room content by uploading new 360-degree room media, updating existing visualizations, previewing content, and deleting outdated materials to ensure prospective tenants always see accurate room conditions. |
| **Actors** | Dormitory Administrator/Owner |
| **Include Use Case** | Upload VR Media Link to Room Record |
| **Extend Use Case** | Delete VR Content Preview VR Room |
| **Preconditions** | Administrator is logged in. Room records exist in the system. |
| **Postconditions** | VR room content is updated. Prospective tenants will see the latest VR visualization for the room. |
| **Exceptions** | Uploaded file has an unsupported format or exceeds the allowed size limit; system displays a specific error and rejects the upload. VR preview fails to load during the preview step; system displays an error and allows the administrator to retry. System encounters an error when saving or linking the VR content to the room record; system displays an error message. Deletion fails due to a system or database error; system displays an error and retains the existing content. |

Continuation of **Table 31**

| Flow of Events |  |
| ----- | ----- |
| **Actor** | **System** |
| 1\. Navigate to VR Content Management. | 1.1. Display list of rooms with their current VR content status (Uploaded / Not Uploaded). |
| 2\. Select a room to manage. | 2.1. Display current VR content for the selected room. |
| \[To upload new content\] 3\. Click Upload VR Media. | 3.1. Display file upload interface. |
| 4\. Select and upload the 360-degree media file. | 4.1. Validate file type (supported 360° formats) and size. 4.1.1. If file type is unsupported, display error. 4.1.2. If file exceeds size limit, display error. |
| 5\. Click Preview to review before saving. | 5.1. Load the uploaded content in the VR preview window. |
| 6\. Click Save. | 6.1. Link the VR content to the room record. 6.2. Display confirmation 'VR content uploaded successfully.' |
| \[To delete content\] 7\. Select existing VR content and click Delete. | 7.1. Display confirmation prompt. |
| 8\. Confirm deletion. | 8.1. Remove the VR content from the room record. 8.2. Display confirmation 'VR content deleted.' |

Table 32  
**Use Case Report – Submit Ticket (Concern / Maintenance Request)**

| Use Case Name | Submit Ticket (Concern / Maintenance Request) |
| :---- | :---- |
| **Scenario** | A current tenant digitally submits a concern, maintenance request, or feedback through the ticketing module, which creates a tracked record for administrator resolution. |
| **Triggering Event** | The tenant navigates to the Ticketing page and clicks Submit a Ticket. |

Continuation of **Table 32**

| Brief Description | This use case describes how a current tenant submits a maintenance request, concern, or feedback through the NEST PH ticketing module. The submission is validated, assigned a unique ticket number, and logged for tracking and resolution by the Dormitory Administrator/Owner. |
| ----- | :---- |
| **Actors** | Current Tenant (Active) |
| **Include Use Case** | Validate Inputs Create Ticket Record |
| **Extend Use Case** | Attach Photos to Ticket |
| **Preconditions** | Tenant is logged in with Active status. Tenant's portal is not under Stage 3 delinquency restriction. |
| **Postconditions** | Ticket is created with a unique tracking number. The ticket appears on the administrator's Tickets page. Tenant can view ticket status. |
| **Exceptions** | Tenant's portal is restricted due to Stage 3 delinquency; system blocks ticket submission and redirects to the payment link. Subject or details field is left empty; system displays a validation error and prevents submission. Attached photo has an unsupported type or exceeds the allowed size, or more than 5 photos are attached; system displays a specific error. |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to the Ticketing page. | 1.1. Display list of previously submitted tickets. 1.1.1. If portal is restricted (Stage 3 escalation), display 'Feature unavailable. Please settle outstanding balance.' and redirect to payment link. |
| 2\. Click Submit a Ticket. | 2.1. Display the ticket submission form. |
| 3\. Select the concern type (Billing & Payment Concern, Electrical Issue, Plumbing / Water Emergency, Security Concern, Structural Damage, Safety & Security, Fire / Safety Hazard, Maintenance & Repairs, Facilities & Amenities, Administrative / Leasing Concern, Account & Access Issue, Noise / Roommate Concern, or Suggestion / Feedback). |  |

Continuation of **Table 32**

| 4\. Enter a subject and the details of the concern or request. | 4.1. Validate that the subject and details are not empty. 4.1.1. Display error if either field is empty. |
| :---- | :---- |
| 5\. Optionally attach up to 5 photos. | 5.1. Validate file type, size, and number of photos. 5.1.1. Display error if attachment is invalid. |
| 6\. Click Submit. | 6.1. Create ticket record with unique tracking number and timestamp. 6.2. Display the new ticket on the administrator's Tickets page. 6.3. Display confirmation 'Ticket \#\[number\] submitted successfully. You can track its status in My Tickets.' |

Table 33  
**Use Case Report – View and Manage Tickets**

| Use Case Name | View and Manage Tickets |
| :---- | :---- |
| **Scenario** | A Dormitory Administrator/Owner views all submitted tickets, updates their status, responds to tenants, and marks handled tickets as Resolved or Rejected. |
| **Triggering Event** | The administrator navigates to the Ticket Management page. |
| **Brief Description** | This use case describes how the Dormitory Administrator/Owner views, filters, and manages all tenant-submitted tickets. The administrator can update ticket statuses, respond to tenants, and mark tickets as Resolved or Rejected to maintain an organized and accountable support system. |
| **Actors** | Dormitory Administrator/Owner |
| **Include Use Case** | Retrieve Ticket List Update Ticket Status |
| **Extend Use Case** | Respond to Tenant Resolve or Reject Ticket |
| **Preconditions** | Administrator is logged in. Ticket records exist in the system. |

Continuation of **Table 33**

| Postconditions | Ticket status is updated. Tenant sees any response or status change the next time they open their Tickets page. No SMS or email notification is sent. |
| ----- | :---- |
| **Exceptions** | No tickets match the applied filter criteria; system displays 'No tickets found.' System encounters a database error when updating the ticket status; system displays an error message. |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to Ticket Management. | 1.1. Retrieve all tickets from the database. 1.2. Display ticket list with columns: Ticket \#, Category, Status, Submitted By, and Date. |
| 2\. Apply filters (by category, status, or priority). | 2.1. Display filtered ticket list. 2.1.1. If no tickets match, display 'No tickets found.' |
| 3\. Select a ticket to view full details. | 3.1. Display full ticket details including description, attachments, and history. |
| \[To update status\] 4\. Change ticket status (Open / Seen / In Progress / Resolved / Rejected). | 4.1. Update ticket status in the database. 4.2. Show the new status to the tenant the next time they open their Tickets page. |
| \[To respond\] 5\. Enter a response message and click Save. | 5.1. Save the response and attach it to the ticket record. 5.2. Show the new response to the tenant the next time they open their Tickets page. |
| \[To close\] 6\. Set the status to Resolved or Rejected after the concern is handled and click Save. | 6.1. Update ticket status to Resolved or Rejected with timestamp. 6.2. Show the final status to the tenant the next time they open their Tickets page. |

Table 34  
**Use Case Report – Track Ticket Status**

| Use Case Name | Track Ticket Status |
| ----- | :---- |
| **Scenario** | A current tenant tracks the progress and status updates of their submitted tickets through the tenant portal. |
| **Triggering Event** | The tenant navigates to the My Tickets section of the portal. |
| **Brief Description** | This use case describes how a current tenant monitors the status of their previously submitted tickets. The system displays all submitted tickets with their current status and any administrator responses, allowing the tenant to stay informed about the progress of their concerns. The tenant may also add replies to their own ticket. |
| **Actors** | Current Tenant (Active) |
| **Include Use Case** | Retrieve Own Ticket Records |
| **Extend Use Case** | Reply to Ticket |
| **Preconditions** | Tenant is logged in. The tenant has previously submitted at least one ticket. |
| **Postconditions** | Tenant views the current status and any administrator responses for their submitted tickets. Any reply added by the tenant is saved to the ticket thread. |
| **Exceptions** | Tenant has not submitted any tickets; system displays 'You have not submitted any tickets yet.' System cannot retrieve ticket records due to a database connection error; system displays a connection error message. |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to My Tickets. | 1.1. Retrieve all tickets submitted by the logged-in tenant. 1.2. Display ticket list with Ticket \#, Category, Status, and Date Submitted. 1.2.1. If no tickets exist, display 'You have not submitted any tickets yet.' |

Continuation of **Table 34**

| 2\. Select a ticket to view full details. | 2.1. Display full ticket details including the original description, any attached files, current status, and administrator responses. |
| :---- | :---- |
| 3\. Review administrator response (if any). | 3.1. Display the response with timestamp. |
| 4\. Enter a message in the Add a reply field and click Submit (optional). | 4.1. Validate that the reply is not empty. 4.2. Save the reply to the ticket thread with timestamp. 4.3. Display confirmation 'Reply sent.' |

Table 35  
**Use Case Report – View Activity Log**

| Use Case Name | View Activity Log |
| :---- | :---- |
| **Scenario** | A Dormitory Administrator/Owner monitors a chronological record of all user and system activities within NEST PH, with options to filter, search, and export logs for audit purposes. |
| **Triggering Event** | The administrator clicks the Activity Log button from the admin dashboard. |
| **Brief Description** | This use case describes how the Dormitory Administrator/Owner accesses and reviews the chronological activity log of all user actions and system events in NEST PH. Logs can be filtered by date, user, or action type, searched, and exported as a CSV file for audit and compliance purposes. |
| **Actors** | Dormitory Administrator/Owner |
| **Include Use Case** | Authenticate User Display Activity Records Apply Search/Filter |
| **Extend Use Case** | Export Activity Log as CSV |
| **Preconditions** | Administrator is logged in with permission to view activity logs. |
| **Postconditions** | Activity log is displayed. Filtered results are shown if filters were applied. CSV is exported if requested. |

Continuation of **Table 35**

| Exceptions | Administrator does not have the required permission to view activity logs; system displays a permission error. Filter inputs are invalid (e.g., end date before start date); system displays a validation error. No log entries match the applied filters; system displays an empty results state. CSV export fails due to a system error; system displays an error message. |
| ----- | :---- |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to Activity Log. | 1.1. Display the Activity Log interface with a table and filter controls. 1.1.1. Display error if the administrator lacks permission to view logs. |
| 2\. Adjust filters (date range, user, action type). | 2.1. Validate filter inputs. 2.2. Retrieve and display matching log entries. |
| 3\. Select a log entry to view details (optional). | 3.1. Display full details of the selected activity event. |
| 4\. Click Export CSV (optional). | 4.1. Generate and download the activity log as a CSV file based on current filter settings. |

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

Table 37  
**Use Case Report – Deactivate Tenant Account**

| Use Case Name | Deactivate Tenant Account |
| :---- | :---- |
| **Scenario** | A Dormitory Administrator/Owner deactivates a former tenant's account after move-out, revoking system access while preserving records for historical reference. |
| **Triggering Event** | The administrator selects a tenant record and clicks Deactivate Account. |
| **Brief Description** | This use case describes how the Dormitory Administrator/Owner deactivates a former or inactive tenant's account following move-out or lease termination. The system checks for outstanding balances, requires confirmation, revokes portal login access, ends the tenant's active lease, frees their bedspace, and retains all records for historical reference. |
| **Actors** | Dormitory Administrator/Owner |

Continuation of **Table 37**

| Include Use Case | Verify Outstanding Balance Update Account Status |
| ----- | :---- |
| **Extend Use Case** | Confirm Deactivation |
| **Preconditions** | Administrator is logged in. The tenant has an active account and has completed move-out or lease termination. |
| **Postconditions** | Tenant account status is set to Inactive. Portal login access is revoked, except that a tenant who moved out normally may log in only to leave a review. The tenant's active lease is ended and their bedspace is set to Vacant. All tenant records are retained in the system. |
| **Exceptions** | Tenant has an outstanding balance; system displays a warning and blocks the deactivation until the balance is settled. No deactivation reason is provided; system displays a validation error and prevents confirmation. System encounters a database error when updating the account status; system displays an error message. |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Search for and select the tenant record. | 1.1. Display tenant profile and current account status. |
| 2\. Click Deactivate Account. | 2.1. Check for outstanding balance. 2.1.1. If outstanding balance exists, display warning: 'This tenant has an unpaid balance of \[amount\]. Please settle before deactivating.' and block the action. |
| 3\. Enter reason for deactivation. | 3.1. Validate that a reason has been provided. 3.1.1. Display error if reason field is empty. |
| 4\. Click Confirm Deactivation. | 4.1. Update tenant account status to Inactive. 4.2. Revoke portal login access, except for logging in to leave a review after a normal move-out. 4.3. End the tenant's active lease and set their bedspace to Vacant. 4.4. Retain all tenant records in the database for historical reference. 4.5. Log the deactivation action with administrator ID, reason, and timestamp. 4.6. Display confirmation 'Tenant account deactivated successfully.' |

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

Table 39  
**Use Case Report \- Ticket Priority Classification** 

| Use Case Name | Ticket Priority Classification  |
| ----- | :---- |
| **Scenario** | A Dormitory Administrator/Owner reviews a submitted ticket and assigns it a priority classification of Urgent or Non-Urgent to support proper sorting and timeely resolution.  |
| **Triggering Event** | The administrator/owner views a submitted ticket on the Tickets page.  |
| **Brief Description** | This use case describes how the Dormitory Administrator/Owner classifies submitted tenant tickets by priority level. Upon reviewing a ticket, the administrator assigns it as either Urgent or Non-Urgent using the priority dropdown on the ticket card, which guides resolution order and prioritization. Assigning a priority is optional. A ticket without a priority is shown as Unclassified and is treated as Non-Urgent for overdue purposes. |
| **Actors** | Dormitory Administrator Dormitory Owner  |
| **Include Use Case** | View Ticket  Save Ticket Updates  |
| **Extend Use Case** | None  |
| **Preconditions** | Administrator is logged in.  At least one ticket has been submitted by a tenant.  |
| **Postconditions** | The ticket is saved with the assigned priority classification, which is reflected in the ticket's display and used to determine escalation thresholds.  |
| **Exceptions** | Priority update fails due to a server error; system displays an error message and keeps the previous priority. |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to the Tickets page.  | 1.1. Display the ticket cards, each with a priority dropdown (Unclassified, Urgent, or Non-Urgent).  |
| 2\. Review the ticket and select a priority level (Urgent or Non-Urgent) from the dropdown on the ticket card.  | 2.1. Save the selected priority classification immediately.  2.2. Display confirmation 'Priority updated.'  |
| 3\. Optionally open the ticket to assign it to a staff member, update status, and enter a reply to the tenant.  | 3.1. Display the ticket details including the tenant's name, concern description, and attached photos if any.  3.2. Accept and validate the provided inputs.  |
| 4\. Click Save.  | 4.1. Save the ticket updates.  4.2. Display confirmation that the ticket has been updated successfully.  |

Table 40  
**Use Case Report \- Ticket Escalation Reminder**

| Use Case Name | Ticket Escalation Reminder |
| ----- | :---- |
| **Scenario** | The system tracks how long each ticket has remained unresolved and displays a running day count on the ticket card. Once the overdue threshold is reached (24 hours for Urgent tickets and 3 days for Non-Urgent tickets), the ticket is flagged as overdue, moved to the top of the Tickets page, and counted in an alert on the administrator's dashboard.  |
| **Triggering Event** | A submitted ticket remains unresolved past its overdue threshold based on its priority level.  |
| **Brief Description** | This use case describes how the system provides passive but continuous escalation awareness on the administrator's Tickets page. A day count is displayed below each unresolved ticket card. Once the ticket surpasses its overdue threshold (Urgent: 24 hours; Non-Urgent: 3 days), a red "Overdue" badge is shown on the ticket card to prompt the administrator to act. Tickets without an assigned priority are treated as Non-Urgent. A Non-Urgent ticket that remains unresolved for 5 days or more is automatically treated as Urgent and labeled "Auto-escalated to Urgent," while the priority set by the administrator stays unchanged. Overdue tickets are sorted to the top of the Tickets page, and a red alert on the administrator's dashboard shows the number of overdue tickets. All escalation indicators are visible on the admin side only.  |
| **Actors** | Dormitory Administrator Dormitory Owner |
| **Include Use Case** | Monitor Ticket Resolution Status  Display Dashboard Overdue Alert  |
| **Extend Use Case** | Auto-escalate Non-Urgent Ticket  |
| **Preconditions** | At least one ticket has been submitted and remains unresolved.  |
| **Postconditions** | The unresolved day count, Overdue badge, and Auto-escalated to Urgent label are displayed on the ticket card as applicable.  All indicators are removed once the ticket is marked as Resolved or Rejected.  The dashboard alert shows the current number of overdue tickets and is hidden when no tickets are overdue.  |
| **Exceptions** | System fails to calculate or display the day count or badge due to a server error |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to the Tickets page.  | 1.1. Retrieve all open tickets and calculate the elapsed time since each ticket was submitted.  1.2. Display a running day count below each unresolved ticket card (e.g., "Unresolved for 2 days").  1.3. If the ticket has exceeded its overdue threshold (Urgent: 24 hours; Non-Urgent: 3 days), display a red "Overdue" badge on the ticket card in place of the day count.  1.4. If a Non-Urgent or unclassified ticket has been unresolved for 5 days or more, treat it as Urgent and display an "Auto-escalated to Urgent" label on the ticket card.  1.5. Sort overdue tickets to the top of the list, with Urgent tickets first and the oldest first, followed by the other open tickets and then Resolved or Rejected tickets.  |
| 2\. Administrator/Owner reviews the tickets and takes note of overdue tickets.  | 2.1. Continue updating the day count and badge status as time passes.  |
| 3\. Administrator opens and resolves a ticket.  | 3.1. Mark the ticket as Resolved.  3.2. Remove the day count indicator and Overdue badge from the ticket card.  |
| 4\. Navigate to the admin dashboard.  | 4.1. If at least one ticket is overdue, display a red alert showing the number of overdue tickets and how many of them are Urgent, with a View Tickets button.  4.2. Hide the alert when no tickets are overdue.  |

   
Table 41  
**Use Case Report \- Reviews & Ratings** 

| Use Case Name | Reviews & Ratings  |
| ----- | :---- |
| **Scenario** | A prospective or current tenant visits the homepage and views the dormitory's aggregate star rating and total review count, and may view the rating breakdown bar and individual reviews on the About the Dorm page, to assess the dormitory's overall reputation.  |
| **Triggering Event** | The user navigates to the homepage of the NEST PH system.  |
| **Brief Description** | This use case describes how the system displays aggregated tenant review data on the homepage. The homepage displays the overall star rating and the total number of reviews submitted. This allows visitors to quickly gauge the dormitory's reputation. The About the Dorm page displays the same summary together with a visual rating breakdown bar showing the distribution of ratings and the list of published reviews.  |
| **Actors** | Prospective Tenant  Current Tenant  |
| **Include Use Case** | Calculate Aggregate Rating  Retrieve Review Data  |
| **Extend Use Case** | Submit Review after Move-out  |
| **Preconditions** | At least one approved review has been submitted by a former tenant.  The homepage is accessible and the system is online.  |
| **Postconditions** | The user successfully views the aggregate star rating and review count on the homepage, and the rating breakdown bar and reviews on the About the Dorm page.  |
| **Exceptions** | No reviews have been submitted yet; system displays a placeholder or empty state  |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to the homepage.  | 1.1. Retrieve all approved reviews from the database.  1.2. Calculate the average star ratings.  |
| 2\. View the ratings section on the homepage.  | 2.1. Display the aggregate star rating.  2.2. Display the total review count (e.g., "128 reviews").  |
| 3\. Navigate to the About the Dorm page (optional).  | 3.1. Compute the rating distribution for the breakdown bar (count per star level from 1 to 5).  3.2. Display the rating breakdown bar and the list of published reviews.  |

Table 42  
**Use Case Report \- Submit Review after Move-out** 

| Use Case Name | Submit Review after Move-out  |
| ----- | :---- |
| **Scenario** | A tenant whose account has been set to Inactive after move-out receives a prompt via SMS and submits a star rating and written review of the dormitory.  |
| **Triggering Event** | The administrator deactivates a tenant's account after move-out, setting their status to Inactive, which triggers an SMS prompting the tenant to leave a review.  |
| **Brief Description** | This use case describes how a former tenant submits a review after their move-out has been recorded by the administrator. Upon being set to Inactive, the tenant receives an SMS asking them to log in and submit a review. A former tenant may log in only to leave a review. The tenant provides a star rating (1–5) and an optional written comment. Once submitted, the review is saved and reflected in the aggregate ratings displayed on the homepage. New reviews are automatically checked, and reviews with offensive language, links, contact details, or spam patterns are hidden until an administrator reviews them.  |
| **Actors** | Current Tenant  Dormitory Administrator/Owner  |
| **Include Use Case** | Validate Review Inputs  Save Review to Database  Automatically Check Review  Update Aggregate Rating  |
| **Extend Use Case** | View Aggregate Reviews & Ratings  |
| **Preconditions** | The administrator has set the tenant's status to Inactive after a normal move-out.  The tenant has a valid contact number or system account to receive the notification.  |
| **Postconditions** | The tenant's review is saved and included in the aggregate star rating and breakdown bar displayed on the homepage.  A review flagged by the automatic check is hidden and is not included until an administrator publishes it.  The tenant can only submit one review per stay.  |
| **Exceptions** | Tenant does not submit a review after receiving the prompt; no action is taken and the prompt expires.  Tenant attempts to submit a review more than once; system disallows duplicate submissions and displays a notice.  Tenant who has already submitted a review attempts to log in again; system displays that the account has been deactivated.  |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Administrator deactivates the tenant account after move-out.  | 1.1. Update tenant status to Inactive in the database.  1.2. Send an SMS to the tenant with a prompt to log in and submit a review.  |
| 2\. Tenant receives the SMS, logs in, and clicks Leave a Review.  | 2.1. Display the review submission form with a star rating selector (1–5) and an optional written comment field.  2.2. Check if the tenant has already submitted a review for this stay; if so, display a notice and block resubmission.  |
| 3\. Tenant selects a star rating (1–5).  | 3.1. Validate that a star rating has been selected. 3.1.1. If no rating is selected and the tenant attempts to submit, display a validation error.  |
| 4\. Tenant enters an optional written comment.  | 4.1. Accept the text input.  |
| 5\. Tenant clicks Submit Review.  | 5.1. Save the review with the star rating, written comment (if any), and timestamp.  5.2. Automatically check the comment for offensive language, links, contact details, or spam patterns.  5.2.1. If the review is flagged, hide it from public view and display the message: "Thank you for your review\! It will appear publicly once an administrator has checked it."  5.3. If the review is not flagged, update the aggregate star rating and review count on the homepage.  5.4. Display a confirmation message: "Thank you for your review\!"  |
| \[Admin\] 6\. Open the Review Moderation section of the Dormitory Profile page and click Publish, Hide, or Remove on a review.  | 6.1. Update the review status.  6.2. Include the review in the aggregate star rating only if it is published.  |

   
Table 43  
**Use Case Report \- View Announcements**

| Use Case Name | View Announcements |
| ----- | :---- |
| **Scenario** | A logged-in tenant views announcements posted by the administrator on the dashboard newsfeed.  |
| **Triggering Event** | The tenant navigates to the dashboard after logging in.  |
| **Brief Description** | This use case allows current tenants to view all posted announcements on the newsfeed, including the poster's avatar showing their initials, name, date, body, and comment count.  |
| **Actors** | Current Tenant  |
| **Include Use Case** | Retrieve and Display Newsfeed  |
| **Extend Use Case** | Comment on Announcement  |
| **Preconditions** | The tenant is logged in.  At least one announcement has been posted.  |
| **Postconditions** | The tenant successfully views the announcements on the dashboard newsfeed.  |
| **Exceptions** | No announcements have been posted yet; system displays an empty state.  |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to the dashboard.  | 1.1. Retrieve and display all announcements in the newsfeed (avatar with the poster's initials, name, date, body, comment count).  |
| 2\. Click on an announcement to expand the comment thread.  | 2.1. Display the full comment thread for the selected announcement.  |

Table 44  
**Use Case Report \- Comment on Announcement** 

| Use Case Name | Comment on Announcement  |
| ----- | :---- |
| **Scenario** | A logged-in tenant or Dormitory Administrator/Owner submits a comment on an announcement from the dashboard newsfeed.  |
| **Triggering Event** | The tenant or administrator types and submits a comment on an expanded announcement thread.  |
| **Brief Description** | This use case allows tenants to submit comments on announcements, provided commenting has not been restricted by the administrator. Administrators may also comment, including on announcements where comments are restricted.  |
| **Actors** | Current Tenant  Dormitory Administrator/Owner  |
| **Include Use Case** | Validate Comment Field  |
| **Extend Use Case** | None  |
| **Preconditions** | The tenant or administrator is logged in.  The announcement's comment thread is expanded.  For tenants, comments are not restricted on the post.  |
| **Postconditions** | The comment is saved and displayed in the thread; comment count is updated.  |
| **Exceptions** | Comment field is empty; system prevents submission.  Comments are restricted by the administrator; comment input is disabled for tenants.  |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Enter a comment in the comment field and submit.  | 1.1. Validate that the field is not empty.  1.1.1. If empty, display a validation error.  1.2. Save and display the comment in the thread. 1.3. Update the comment count on the announcement card.  |

Table 45  
**Use Case Report \- Manage Announcements** 

| Use Case Name | Manage Announcements  |
| ----- | :---- |
| **Scenario** | A Dormitory Administrator/Owner composes and publishes an announcement to all tenants via the newsfeed on the admin dashboard.  |
| **Triggering Event** | The administrator types an announcement in the composition field on the admin dashboard and clicks the "Post" button.  |
| **Brief Description** | This use case allows administrators to post announcements visible to all tenants on the dashboard newsfeed. Announcements are posted and managed directly from the admin dashboard, and there is no separate Manage Announcements page. The "Post" button is exclusive to Admin and Dormitory Owner roles.  |
| **Actors** | Dormitory Administrator/Owner  |
| **Include Use Case** | Validate Announcement Body  |
| **Extend Use Case** | Manage Comments  |
| **Preconditions** | The administrator is logged in.  |
| **Postconditions** | The announcement is published and visible to all tenants on the newsfeed.  |
| **Exceptions** | Announcement body is empty; system displays a validation error and prevents posting.  |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to the admin dashboard.  | 1.1. Display the announcement composition field with the placeholder "Post an announcement to all tenants..." and a "Post" button.  |
| 2\. Enter the announcement body and click "Post."  | 2.1. Validate that the body is not empty.  2.1.1. If empty, display a validation error.  2.2. Save and publish the announcement to the newsfeed for all tenants.  |

Table 46  
**Use Case Report \- Manage Comments** 

| Use Case Name | Manage Comments  |
| ----- | :---- |
| **Scenario** | A Dormitory Administrator/Owner deletes a comment or restricts/re-enables commenting on a specific announcement.  |
| **Triggering Event** | The administrator clicks the Delete or Restrict comments control on an announcement in the admin dashboard newsfeed.  |
| **Brief Description** | This use case covers admin comment controls per announcement. The administrator may delete any tenant comment from a thread, or toggle comment restriction on an announcement to prevent or re-allow tenant comments.  |
| **Actors** | Dormitory Administrator/Owner  |
| **Include Use Case** | Retrieve Announcement Comment Thread  |
| **Extend Use Case** | None |
| **Preconditions** | The administrator is logged in.  At least one announcement exists.  |
| **Postconditions** | The selected comment is deleted and removed from the thread, or the announcement's comment restriction status is updated accordingly.  |
| **Exceptions** | Deletion or restriction action fails due to a server error; system displays an error message.  |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Open an announcement's comment thread and click Delete on a comment.  | 1.1. Display a confirmation prompt.  |
| 2\. Confirm deletion.  | 2.1. Delete the comment and remove it from the thread.  2.2. Update the comment count on the announcement card.  |
| 3\. Click Restrict comments on an announcement.  | 3.1. Update the restriction status and disable the comment input for tenants on that post.  3.2. Display confirmation that comments have been restricted.  |
| 4\. Click Unrestrict to re-enable comments (optional).  | 4.1. Lift the restriction and re-enable the comment input for tenants on that post.  |

Table 47  
**Use Case Report \- View Penalties** 

| Use Case Name | View Penalties  |
| ----- | :---- |
| **Scenario** | A current tenant navigates to their billing page to view any late payment penalties associated with their account.  |
| **Triggering Event** | The tenant navigates to the Billing section of their dashboard.  |
| **Brief Description** | This use case allows tenants to view a penalties subsection within their billing view, displaying each penalty's date, type, amount, status, and a running total.  |
| **Actors** | Current Tenant |
| **Include Use Case** | Retrieve Penalty Records  |
| **Extend Use Case** | None |
| **Preconditions** | The tenant is logged in.  At least one penalty has been recorded on the tenant's account.  |
| **Postconditions** | The tenant successfully views their penalty history and running total.  |
| **Exceptions** | No penalties exist on the account; system displays an empty state or a message indicating no penalties.  |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to the Billing section.  | 1.1. Retrieve and display the billing page including the Penalties subsection.  |
| 2\. View the Penalties subsection.  | 2.1. Display a list of penalties with date, type, amount, and status. 2.2. Display the running total of all penalties.  |

   
Table 48  
**Use Case Report \- Record Tenant Damage**  

| Use Case Name | Record Tenant Damage  |
| ----- | :---- |
| **Scenario** | A Dormitory Administrator/Owner records a damage incident caused by a tenant, including the affected room/bed, description, cost, date, and an optional photo. The system automatically adds the damage cost as a penalty to the tenant's billing.  |
| **Triggering Event** | The administrator opens the Record Damage feature from the admin panel.  |
| **Brief Description** | This use case allows the administrator to document tenant-caused damage through a form. Upon submission, the system logs the damage and automatically generates a corresponding penalty entry in the tenant's billing record.  |
| **Actors** | Dormitory Administrator Dormitory Owner  |
| **Include Use Case** | Validate Inputs  Auto-Add Penalty to Billing  |
| **Extend Use Case** | None |
| **Preconditions** | The administrator is logged in.  At least one active tenant exists in the system.  |
| **Postconditions** | The damage record is saved and a penalty is automatically added to the tenant's billing account.   |
| **Exceptions** | Required fields are left empty or contain invalid input; system displays a validation error.  Uploaded photo exceeds the allowed file size or format; system displays an error and prompts re-upload.  |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to the Record Damage feature.  | 1.1. Display the damage recording form with fields for tenant, room/bed, description, cost, date, and photo.  |
| 2\. Select the tenant from the dropdown.  | 2.1. Load the tenant's associated room/bed information.  |
| 3\. Fill in the description, cost, date, and optionally upload a photo.  | 3.1. Validate all required fields and file format/size if a photo is uploaded.  |
| 4\. Click Submit.  | 4.1. Save the damage record.  4.2. Automatically create a penalty entry in the tenant's billing record based on the damage cost. 4.3. Display a confirmation message.  |

   
Table 49  
**Use Case Report \- Add Tenant Penalty** 

| Use Case Name | Add Tenant Penalty  |
| ----- | :---- |
| **Scenario** | A Dormitory Administrator/Owner manually adds a penalty to a tenant's billing record by specifying the penalty type, amount, reason, and date.  |
| **Triggering Event** | The administrator opens the Manual Add Tenant Penalty form from the admin panel.  |
| **Brief Description** | This use case allows the administrator to manually create a penalty entry on a tenant's account for cases not covered by automatic penalty generation (e.g., rule violations, miscellaneous charges).  |
| **Actors** | Dormitory Administrator Dormitory Owner  |
| **Include Use Case** | Validate Inputs  Save Penalty to Billing  |
| **Extend Use Case** | None |
| **Preconditions** | The administrator is logged in.  The target tenant has an active account in the system.  |
| **Postconditions** | The penalty is saved and reflected in the tenant's billing and penalties subsection.  |
| **Exceptions** | Required fields are empty or invalid; system displays a validation error and prevents submission.  |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to the Add Tenant Penalty form.  | 1.1. Display the form with fields for penalty type, amount, reason, and date.  |
| 2\. Fill in all required fields.  | 2.1. Validate inputs upon submission.  2.1.1. If any required field is missing or invalid, display a validation error.  |
| 3\. Click Submit.  | 3.1. Save the penalty entry to the tenant's billing record.  3.2. Display a confirmation message.  |

   
Table 50  
**Use Case Report \- Issue Eviction Notice** 

| Use Case Name | Issue Eviction Notice  |
| ----- | :---- |
| **Scenario** | A Dormitory Administrator triggers Stage 6 of the delinquency escalation protocol, generating a formal Eviction Notice PDF and sending the tenant an SMS notice through textbee.dev.  |
| **Triggering Event** | The tenant's account has reached Delinquent/Blacklisted status (Stage 6 complete), and the administrator determines eviction is warranted.  |
| **Brief Description** | This use case covers the final escalation step where the administrator formally issues an eviction notice. The system generates a PDF containing the tenant's name, unit, reason, and date, and stores it in the system. It then sends the tenant a short SMS through the textbee.dev SMS gateway stating that a formal eviction notice has been issued on the notice date and instructing the tenant to contact the dormitory administrator. The PDF itself is not sent by SMS, since an SMS cannot carry a file, and it remains available for the administrator to view and download.   |
| **Actors** | Dormitory Administrator Dormitory Owner  |
| **Include Use Case** | Generate Eviction Notice PDF Send SMS Notification via textbee.dev  |
| **Extend Use Case** | None |
| **Preconditions** | The administrator is logged in.  The tenant's account status is Delinquent (Stage 6 has already been applied).  The tenant has a valid contact number on record.  textbee.dev SMS gateway is online and configured.  |
| **Postconditions** | The eviction notice PDF is generated and stored.  An SMS informing the tenant that a formal eviction notice has been issued is sent to the tenant.  An Eviction Notice Issued event is recorded against the tenant's escalation history.  |
| **Exceptions** | textbee.dev SMS gateway is unavailable; system displays an error and logs the failed send attempt.  PDF generation fails due to missing tenant data; system displays a validation error and halts the process.  Tenant contact number is missing or invalid; the notice is still generated, and the system displays that the SMS could not be sent and logs it for retry.  |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to a delinquent tenant's record and select "Issue Eviction Notice."  | 1.1. Retrieve tenant details (name, unit, reason, date) for the notice.  |
| 2\. Enter the Reason for Eviction and the Notice Date, then review the details.  | 2.1. Generate the formal Eviction Notice PDF using the retrieved tenant data.  2.2. Store the generated PDF and link it to the tenant's record.  |
| 3\. Click Confirm & Send Notice.  | 3.1. Send an SMS through the textbee.dev gateway informing the tenant that a formal eviction notice has been issued and instructing them to contact the dormitory administrator. The PDF is not attached to the SMS.  3.1.1. If SMS send fails, log the error and display a failure message to the administrator.  3.2. Log an Eviction Notice Issued entry against the tenant's escalation history.  3.3. Display a confirmation message.  |

Table 51  
**Use Case Report \- Oversee Delinquency Escalation** 

| Use Case Name | Oversee Delinquency Escalation  |
| ----- | :---- |
| **Scenario** | The Dormitory Owner monitors the overall delinquency escalation status of tenants across all escalation stages and reviews actions taken by the administrator.  |
| **Triggering Event** | The Dormitory Owner navigates to the Delinquency Escalation section of the system.  |
| **Brief Description** | This use case allows the Dormitory Owner to oversee the full delinquency escalation protocol. The owner can view each tenant's current escalation stage, review the history of escalation actions taken, and monitor whether eviction notices have been issued. The owner does not directly trigger escalation steps but has full visibility over the process.  |
| **Actors** | Dormitory Owner  |
| **Include Use Case** | View Tenant Escalation Stage View Escalation Action History  |
| **Extend Use Case** | Issue Eviction Notice  |
| **Preconditions** | The Dormitory Owner is logged in.  At least one tenant has an active delinquency escalation record.  |
| **Postconditions** | The Dormitory Owner has reviewed the escalation statuses and action history of delinquent tenants.  |
| **Exceptions** | No delinquency records exist; system displays an empty state.  |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to the Delinquency Escalation section.  | 1.1. Retrieve all active delinquency escalation records.  1.2. Display each tenant's name, unit, current escalation stage, and latest action taken.  |
| 2\. Select a specific tenant's escalation record to review.  | 2.1. Display the full escalation history for the selected tenant, including stage progression, dates, and actions taken at each stage.  |
| 3\. Review whether an eviction notice has been issued at Stage 6\.  | 3.1. If Stage 6 has been triggered, display the eviction notice details and SMS send status.  |

   
Table 52  
**Use Case Report \- Facility Management**

| Use Case Name | Facility Management |
| ----- | :---- |
| **Scenario** | A Dormitory Administrator/Owner manages the dormitory's physical structure by adding and configuring floors, rooms, and beds in a hierarchical manner, each with their respective label, capacity, and status.  |
| **Triggering Event** | The administrator navigates to the Facility Management section of the admin panel.  |
| **Brief Description** | This use case allows the administrator to build and maintain the dormitory's floor-room-bed hierarchy. Each level can be individually labeled, assigned a capacity, and given a status. Changes made at any level are reflected throughout the system where room and bed data is referenced |
| **Actors** | Dormitory Owner  Dormitory Administrator |
| **Include Use Case** | Validate Inputs  Save Facility Structure  |
| **Extend Use Case** | Record Tenant Damage Assign Tenant to Bed  |
| **Preconditions** | The Dormitory Owner/Administrator is logged in.  The dormitory profile has been set up in the system.  |
| **Postconditions** | The floor, room, or bed entry is saved and available for use across the system (tenant assignment, billing, damage recording, etc.).  |
| **Exceptions** | Required fields (label, capacity, status) are missing or invalid; system displays a validation error.  Duplicate floor/room/bed label detected; system prompts the administrator to use a unique label.  |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to the Facility Management section.  | 1.1. Display the current floor-room-bed hierarchy with existing entries and their statuses.  |
| 2\. Click Add Floor and enter the floor label, capacity, and status.  | 2.1. Validate the inputs.  2.1.1. If invalid or duplicate, display an error.  2.2. Save the floor entry and display it in the hierarchy.  |
| 3\. Select a floor and click Add Room, then enter the room label, capacity, and status.  | 3.1. Save the room under the selected floor and update the hierarchy display.  |
| 4\. Select a room and click Add Bed, then enter the bed label and status.  | 4.1. Save the bed under the selected room and update the hierarchy display.  |
| 5\. Edit or update an existing floor, room, or bed entry.  | 5.1. Load the existing entry details into the form. 5.2. Validate and save the updated information.  5.3. Reflect changes across all system references.  |

   
Table 53  
**Use Case Report \- Manage Admin Accounts and Privileges**

| Use Case Name | Manage Admin Accounts and Privileges |
| ----- | :---- |
| **Scenario** | The Dormitory Owner creates a new admin account, sets which privileges each administrator has, and revokes admin access when it is no longer needed.  |
| **Triggering Event** | The Dormitory Owner navigates to the Admin Privileges page.  |
| **Brief Description** | This use case allows the Dormitory Owner to manage who can use the admin panel and what each administrator can do. The owner adds a new admin account by entering a full name and email address and choosing the privileges to grant. The system creates the login and displays a temporary password for the owner to give to the new administrator. The owner can also change the privileges of an existing administrator or revoke their admin access. The owner cannot remove their own Manage Admin Users privilege or revoke their own admin access.  |
| **Actors** | Dormitory Owner  |
| **Include Use Case** | Validate Inputs  Save Admin Privileges  Log Privilege Changes  |
| **Extend Use Case** | Revoke Admin Access  |
| **Preconditions** | The Dormitory Owner is logged in and has the Manage Admin Users privilege.  |
| **Postconditions** | A new admin account is created with the selected privileges, the privileges of an existing administrator are updated, or admin access is revoked. Each action is logged with the administrator affected, the person who performed it, and the date.  |
| **Exceptions** | Full name or email is left empty or is invalid; system displays a validation error.  Email address is already used by another account; system displays a validation error.  Owner attempts to remove their own Manage Admin Users privilege; system displays 'You cannot remove your own Manage Admin Users privilege.' and keeps the current privileges.  Owner attempts to revoke their own admin access; system disables the Revoke action for the owner's own account.  |
| **Flow of Events** |  |
| **Actor** | **System** |
| 1\. Navigate to the Admin Privileges page.  | 1.1. Display the list of administrators with their name, email, privileges, status, and date granted.  |
| \[To add\] 2\. Click Add Admin Account.  | 2.1. Display the Add Admin Account form with fields for full name and email, and a checklist of privileges (Manage Tenants, Manage Rooms, Manage Contracts, Manage Billing, Manage Admin Users, and View Reports).  |
| 3\. Enter the full name and email, select the privileges to grant, and click Create Account.  | 3.1. Validate the inputs.  3.1.1. Display an error if a field is missing or the email is already in use.  3.2. Create the admin login with the selected privileges and log the action.  3.3. Display the Admin Account Created window with the temporary password and a Copy Password button.  |
| \[To change privileges\] 4\. Click Manage Privileges on an administrator.  | 4.1. Display the administrator's current privileges as a checklist.  |
| 5\. Check or uncheck privileges and click Save Changes.  | 5.1. If the owner is removing their own Manage Admin Users privilege, display an error and keep the current privileges.  5.2. Save the updated privileges and log the change.  5.3. Display confirmation that the privileges have been updated.  |
| \[To revoke\] 6\. Click Revoke on an administrator.  | 6.1. Display a confirmation prompt stating that the administrator will be signed out of the admin panel.  |
| 7\. Confirm the revocation.  | 7.1. Remove all privileges from the account, deactivate its admin login, and log the action.  7.2. Display confirmation that admin access has been revoked.  |

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
