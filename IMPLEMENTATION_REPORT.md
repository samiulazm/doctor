# Chamber Practice Module Implementation Report

**Generated:** 2026-04-24  
**Workspace:** `C:\Users\Samiul\Pictures\doctor`  
**Framework:** CodeIgniter 3 style HMVC (`MX_Controller`), not CodeIgniter 4  
**Main module key:** `chamber_practice`

---

## 1. Executive Summary

This report covers the continuation of the first and second prompts:

1. Build only the requested Patient Portal, Doctor Portal, and Assistant Portal feature set.
2. Keep the feature set hidden unless a module is turned on.
3. Continue the previously missed items and document the actual codebase status.

The implementation is now organized around a switchable `chamber_practice` module. When the module is enabled for a hospital/package, the chamber practice features become available. When the module is disabled, the public landing page, patient chamber pages, doctor chamber pages, assistant chamber pages, Bangladesh payment routes, chamber medicine reminders, and chamber prescription template behavior are blocked or hidden.

The previous generated report claimed some inaccurate details. The corrected status is:

- The app is CodeIgniter 3/HMVC style, not CodeIgniter 4.
- Live queue is implemented with AJAX polling, not WebSocket.
- Medicine reminders are implemented with SMS cron support, not mobile push notifications.
- AI call is represented as a queued/logged telephony integration request, not an actual outbound calling engine.
- Payment integration is wired through SSLCommerz and bKash routes, but production behavior still depends on valid gateway credentials and callback configuration.

---

## 2. Module Switch / Feature Gate

### Purpose

The user asked for only this feature set, not the rest of the system, and wanted a module that can be turned on/off. The module gate is implemented through `chamber_practice`.

### Main helper

`public_html/application/helpers/chamber_practice_helper.php`

Verified helper methods:

- `chamber_practice_module_key()`
- `chamber_practice_module_aliases()`
- `chamber_practice_normalize_modules($modules)`
- `chamber_practice_resolve_hospital_id($CI)`
- `chamber_practice_modules_have_key($modules)`
- `chamber_practice_enabled_for_hospital($CI, $hospital_id = null)`
- `chamber_practice_enabled($CI, $hospital_id = null)`
- `chamber_practice_require_enabled($CI, $hospital_id = null, $public_request = false)`

### Alias support

The primary key is:

- `chamber_practice`

Legacy/related aliases are accepted for compatibility:

- `doctor_chamber`
- `assistant_chamber`
- `patient_chamber`
- `portal`
- `payment_bd`

### Autoload

`public_html/application/config/autoload.php`

The `chamber_practice` helper is autoloaded so every gated controller can call the feature check.

### Gated controllers and flows

The module gate now protects:

- `public_html/application/modules/portal/controllers/Portal.php`
- `public_html/application/modules/doctor_chamber/controllers/Doctor_chamber.php`
- `public_html/application/modules/assistant_chamber/controllers/Assistant_chamber.php`
- `public_html/application/modules/patient_chamber/controllers/Patient_chamber.php`
- `public_html/application/modules/payment_bd/controllers/Payment_bd.php`
- `public_html/application/modules/cronjobs/controllers/Cronjobs.php`
- `public_html/application/modules/prescription/controllers/Prescription.php` for chamber print header/footer/signature behavior

### Menu and package visibility

The chamber menu is hidden unless the feature is enabled:

- `public_html/application/modules/home/views/menu_partials/menu_chamber_practice.php`

The module can be selected or shown in package/hospital/frontend plan screens:

- `public_html/application/modules/hospital/views/add_new.php`
- `public_html/application/modules/hospital/views/add_new_package.php`
- `public_html/application/modules/hospital/views/package.php`
- `public_html/application/modules/hospital/controllers/Hospital.php`
- `public_html/application/modules/settings/views/change_plan.php`
- `public_html/application/modules/frontend/views/front_end.php`
- `public_html/application/modules/frontend/views/index.php`

---

## 3. Feature Matrix

### Patient Portal

| Requirement | Status | Actual implementation |
|---|---:|---|
| Public Landing Page | Complete | Doctor photo, specialty, chambers, schedule, live ticker, and `Book Serial Now` entry are in `portal/views/portal/landing.php`. |
| Chamber Toggle | Complete | Chamber dropdown switches selected chamber details and availability in the public landing view. |
| Live Queue Tracker | Complete with limitation | `Portal::ticker_json()` returns current serving serial and the landing page polls it. This is not WebSocket real-time. |
| OTP Verification | Complete | `Portal::request_otp()` and `Portal::verify_otp()` manage SMS OTP rows and verification. |
| Triage Module | Complete | `portal/views/portal/triage.php` has identity, symptoms, and attachment upload steps. |
| Payment Integration | Complete with gateway dependency | `Payment_bd` supports SSLCommerz and bKash intent/init/callback routes. Requires production credentials. |
| My Prescriptions | Complete | `Patient_chamber::my_prescriptions()` loads past prescriptions for the logged-in patient. |
| Lab Report Repository | Complete | `Patient_chamber::lab_vault()` loads normal lab, OT lab, and referral records. |
| Medicine Reminder | Complete with limitation | Patient reminder CRUD exists and `Cronjobs::chamberMedicineReminders()` sends SMS reminders. No mobile push notification engine was added. |

### Doctor Portal

| Requirement | Status | Actual implementation |
|---|---:|---|
| Dashboard | Complete | `Doctor_chamber::dashboard()` shows appointment summary and today's live queue. |
| Revenue Summary | Complete | `Doctor_chamber::revenue()` summarizes consultation/procedure collection. |
| Consultation Room | Complete | `Doctor_chamber::consultation_room()` loads patient history, triage, attachments, vitals, tags, and prescription workspace links. |
| One-Click Prescription | Complete | Favorites are managed by `favorites()` and `favorite_save()` and shown through doctor chamber views. |
| Drug Database | Complete with limitation | `drug_search_json()` searches the existing medicine database. This is search, not AI drug interaction checking. |
| Electronic Signature | Complete | Doctor profile signature is applied to prescription PDF output when module is enabled. |
| Patient CRM | Complete | `crm_search()` searches by phone/name and opens patient history quickly. |
| Tagging | Complete | `tag_patient()` supports tags such as High Risk, Follow-up Required, and VIP. |
| Schedules | Complete | Chamber hours and date exceptions are managed by chamber and schedule screens. |
| Template Builder | Complete | `template_builder()` and `template_save()` manage prescription header/footer template content. |

### Assistant Portal

| Requirement | Status | Actual implementation |
|---|---:|---|
| Check-in Desk | Complete | `desk()` lists queue and `checkin()` marks arrival. |
| Manual Entry | Complete | `manual_booking()` and `manual_booking_save()` support over-the-call booking. |
| Vitals Entry | Complete | `vitals_save()` stores BP, pulse, weight, and other vitals for doctor view. |
| Queue Management | Complete | `queue_reorder()` and `emergency_bump()` support drag/drop and emergency priority. |
| SMS Broadcaster / AI call | Partial | Bulk SMS sends through existing SMS logic. AI call requests are queued/logged for telephony integration but do not place calls yet. |
| Billing & Collection | Complete | `mark_queue_fee_paid()` records cash/mobile payment status. |
| Print Manager | Complete | `print_latest_rx()` and `desk_rx_poll()` support one-click/latest prescription printing workflow. |

---

## 4. Patient Portal Implementation Details

### Public landing page

Files:

- `public_html/application/modules/portal/controllers/Portal.php`
- `public_html/application/modules/portal/models/Portal_model.php`
- `public_html/application/modules/portal/views/portal/landing.php`

Controller methods:

- `d($slug = '')`
- `ticker_json()`

What it does:

- Loads doctor profile by public slug.
- Shows doctor photo, name, specialty, degree, experience, chamber selector, and booking button.
- Shows chamber address, phone, and weekly hours.
- Shows current queue serial through AJAX polling.
- Blocks the page if `chamber_practice` is disabled for the hospital.

### Chamber toggle

Files:

- `portal/views/portal/landing.php`
- `portal/models/Portal_model.php`

Methods:

- `Portal_model::getChambersForDoctor($doctor_id, $hospital_id)`
- `Portal_model::getChamberIfOwned($chamber_id, $doctor_id, $hospital_id)`

What it does:

- Loads multiple chamber locations for one doctor.
- Decodes `weekly_hours_json` into a view-ready structure.
- Lets patient switch the selected chamber before booking.
- Carries chamber metadata with `data-address`, `data-phone`, and `data-hours`.

### Live queue tracker

Files:

- `portal/controllers/Portal.php`
- `portal/views/portal/landing.php`
- Database table: `chamber_queue_ticker`

Method:

- `Portal::ticker_json()`

What it does:

- Returns current serial being served for the selected doctor/chamber.
- Assistant updates serving status from the desk.
- Public page polls the endpoint periodically.

Limitation:

- This is polling-based live status. It is not WebSocket push.

### OTP verification

Files:

- `portal/controllers/Portal.php`
- `portal/models/Portal_model.php`
- Database table: `booking_otp_session`

Methods:

- `Portal::request_otp()`
- `Portal::verify_otp()`
- `Portal::_send_otp_sms($hospital_id, $mobile, $plain)`
- `Portal_model::countRecentOtpRequests($hospital_id, $mobile, $since_ts)`
- `Portal_model::insertOtpRow($hospital_id, $mobile, $hash, $expires_at)`
- `Portal_model::getLatestOtp($hospital_id, $mobile)`
- `Portal_model::markOtpVerified($id)`
- `Portal_model::incrementOtpAttempts($id, $attempts)`

What it does:

- Accepts mobile number.
- Stores hashed OTP.
- Limits repeated OTP requests.
- Sends SMS through existing SMS integration.
- Requires verified OTP before booking can complete.

### Triage module

Files:

- `portal/controllers/Portal.php`
- `portal/views/portal/triage.php`
- Database table: `chamber_serial_queue`

Methods:

- `Portal::triage($slug = '')`
- `Portal::complete_booking()`

What it does:

- Step 1: identity fields such as name, age, and gender.
- Step 2: symptoms and duration.
- Step 3: attachments for previous prescription/lab/report photos.
- Stores triage data as JSON in the queue record.
- Creates minimal patient profile when needed.

### Payment integration

Files:

- `public_html/application/modules/payment_bd/controllers/Payment_bd.php`
- Database table: `bd_payment_intent`

Methods:

- `Payment_bd::init_sslcommerz()`
- `Payment_bd::sslcommerz_return()`
- `Payment_bd::sslcommerz_ipn()`
- `Payment_bd::sslcommerz_fail()`
- `Payment_bd::init_bkash()`
- `Payment_bd::bkash_callback()`

What it does:

- Creates payment intent for booking fee.
- Supports SSLCommerz init/return/IPN.
- Supports bKash init/callback finalization.
- Verifies module enablement before processing payment.

Important correction:

- Patient-posted fee is not trusted. Booking fee is loaded from doctor profile server-side.

### My prescriptions

Files:

- `public_html/application/modules/patient_chamber/controllers/Patient_chamber.php`
- `public_html/application/modules/patient_chamber/views/patient/my_prescriptions.php`

Method:

- `Patient_chamber::my_prescriptions()`

What it does:

- Resolves logged-in patient by Ion Auth user.
- Loads prescription records by patient ID.
- Presents past prescription PDF/list access inside the patient chamber area.

### Lab report repository

Files:

- `public_html/application/modules/patient_chamber/controllers/Patient_chamber.php`
- `public_html/application/modules/patient_chamber/views/patient/lab_vault.php`

Method:

- `Patient_chamber::lab_vault()`

What it does:

- Loads referral events for the patient.
- Loads lab records from `lab_model`.
- Loads OT lab records from `lab_model`.
- Shows patient-facing report repository.

### Medicine reminders

Files:

- `public_html/application/modules/patient_chamber/controllers/Patient_chamber.php`
- `public_html/application/modules/patient_chamber/views/patient/reminders.php`
- `public_html/application/modules/cronjobs/controllers/Cronjobs.php`
- Database table: `medicine_reminder`

Methods:

- `Patient_chamber::reminders()`
- `Patient_chamber::reminder_save()`
- `Cronjobs::chamberMedicineReminders()`

What it does:

- Lets patient save medicine label and dose times.
- Cron reads active reminders.
- Sends reminder SMS when due.

Limitation:

- No FCM/mobile push notification implementation was added.

---

## 5. Doctor Portal Implementation Details

### Dashboard

Files:

- `public_html/application/modules/doctor_chamber/controllers/Doctor_chamber.php`
- `public_html/application/modules/doctor_chamber/views/doctor/dashboard.php`

Method:

- `Doctor_chamber::dashboard()`

What it does:

- Shows appointment totals for today.
- Shows checked-in vs pending style summary.
- Shows today's queue with serial, patient, chamber, status, triage, and consultation room link.

### Revenue summary

Files:

- `doctor_chamber/controllers/Doctor_chamber.php`
- `doctor_chamber/views/doctor/revenue.php`

Method:

- `Doctor_chamber::revenue()`

What it does:

- Reads collection data from queue/payment records.
- Separates consultation/procedure style collection where available.

### Consultation room

Files:

- `doctor_chamber/controllers/Doctor_chamber.php`
- `doctor_chamber/views/doctor/consultation_room.php`

Methods:

- `Doctor_chamber::consultation_room()`
- `Doctor_chamber::vitals_json()`

What it does:

- Left side: patient history, past prescriptions, triage, attachments, tags, and vitals.
- Right side: active prescription workflow/E-Pad through existing prescription module links.
- Latest assistant-entered vitals appear for doctor review.

### One-click prescription favorites

Files:

- `doctor_chamber/controllers/Doctor_chamber.php`
- `doctor_chamber/views/doctor/favorites.php`
- Database table: `prescription_favorite`

Methods:

- `Doctor_chamber::favorites()`
- `Doctor_chamber::favorite_save()`

What it does:

- Lets doctor save common medicine/prescription snippets.
- Provides reusable favorites for faster prescription entry.

### Drug database

Files:

- `doctor_chamber/controllers/Doctor_chamber.php`

Method:

- `Doctor_chamber::drug_search_json()`

What it does:

- Searches the existing medicine/drug database.
- Returns JSON results for intelligent lookup style UI.

Limitation:

- This is database search. It does not include drug interaction AI or clinical decision support.

### Electronic signature

Files:

- `doctor_chamber/controllers/Doctor_chamber.php`
- `doctor_chamber/views/doctor/portal_profile.php`
- `prescription/controllers/Prescription.php`

Methods:

- `Doctor_chamber::portal_profile()`
- `Doctor_chamber::portal_profile_save()`

What it does:

- Saves doctor public profile and signature URL/path.
- Prescription PDF output can stamp chamber header, footer, and signature when `chamber_practice` is enabled.

### Patient CRM

Files:

- `doctor_chamber/controllers/Doctor_chamber.php`
- `doctor_chamber/views/doctor/crm_search.php`

Method:

- `Doctor_chamber::crm_search()`

What it does:

- Searches patient by phone or name.
- Pulls patient history quickly for doctor workflow.

### Patient tagging

Files:

- `doctor_chamber/controllers/Doctor_chamber.php`
- Database table: `patient_practice_tag`

Method:

- `Doctor_chamber::tag_patient()`

What it does:

- Stores doctor/hospital scoped tags for patients.
- Supports operational tags such as `High Risk`, `Follow-up Required`, and `VIP`.

### Schedules and chamber timings

Files:

- `doctor_chamber/controllers/Doctor_chamber.php`
- `doctor_chamber/views/doctor/my_chambers.php`
- `doctor_chamber/views/doctor/schedule_exceptions.php`
- Database tables: `doctor_chamber`, `doctor_schedule_exception`

Methods:

- `Doctor_chamber::my_chambers()`
- `Doctor_chamber::chamber_create()`
- `Doctor_chamber::chamber_update()`
- `Doctor_chamber::schedule_exceptions()`
- `Doctor_chamber::schedule_save()`

What it does:

- Creates multiple chamber locations.
- Stores weekly chamber hours.
- Stores vacation dates and one-day open/close overrides.
- Booking flow checks schedule exception and regular weekly hours before confirming serial.

### Template builder

Files:

- `doctor_chamber/controllers/Doctor_chamber.php`
- `doctor_chamber/views/doctor/template_builder.php`
- `prescription/controllers/Prescription.php`
- Database table: `prescription_print_template`

Methods:

- `Doctor_chamber::template_builder()`
- `Doctor_chamber::template_save()`

What it does:

- Lets doctor configure prescription header and footer.
- Prescription printing/PDF output uses the template when the module is enabled.

---

## 6. Assistant Portal Implementation Details

### Check-in desk

Files:

- `assistant_chamber/controllers/Assistant_chamber.php`
- `assistant_chamber/views/assistant/desk.php`

Methods:

- `Assistant_chamber::desk()`
- `Assistant_chamber::checkin()`

What it does:

- Lists today's queue.
- Lets assistant mark patient as arrived.
- Updates status so doctor dashboard/consultation room can see the change.

### Manual booking

Files:

- `assistant_chamber/controllers/Assistant_chamber.php`
- `assistant_chamber/views/assistant/manual_booking.php`

Methods:

- `Assistant_chamber::manual_booking()`
- `Assistant_chamber::manual_booking_save()`

What it does:

- Allows over-the-call booking.
- Supports existing patient ID.
- Supports creating/finding patient by phone/name/age/gender.
- Supports doctor chamber selection.
- Stores symptom, duration, and remarks in triage JSON.

### Vitals entry

Files:

- `assistant_chamber/controllers/Assistant_chamber.php`
- Database table: `visit_vital`

Method:

- `Assistant_chamber::vitals_save()`

What it does:

- Stores BP, pulse, weight, and other vitals.
- Doctor consultation view can load latest vitals.

### Queue management

Files:

- `assistant_chamber/controllers/Assistant_chamber.php`
- `assistant_chamber/views/assistant/desk.php`

Methods:

- `Assistant_chamber::queue_reorder()`
- `Assistant_chamber::emergency_bump()`
- `Assistant_chamber::mark_serving()`
- `Assistant_chamber::mark_status()`

What it does:

- Supports drag/drop reorder from the desk UI.
- Supports emergency bump to top.
- Marks current patient as serving.
- Marks queue as done or cancelled.
- Clears current queue ticker when the serving item is completed/cancelled.

### SMS broadcaster and AI call request

Files:

- `assistant_chamber/controllers/Assistant_chamber.php`
- `assistant_chamber/views/assistant/bulk_sms.php`
- Database table: `usage_analytics_event`

Methods:

- `Assistant_chamber::bulk_sms()`
- `Assistant_chamber::bulk_sms_send()`
- `Assistant_chamber::bulk_call_request()`

What it does:

- Sends bulk SMS to selected queue contacts.
- Provides quick message templates such as doctor on the way/chamber closed.
- Logs AI call request payload for a future telephony worker.

Important limitation:

- No actual outbound AI calling provider is integrated in this pass.

### Billing and collection

Files:

- `assistant_chamber/controllers/Assistant_chamber.php`
- `assistant_chamber/views/assistant/desk.php`

Method:

- `Assistant_chamber::mark_queue_fee_paid()`

What it does:

- Marks consultation/advance fee as paid.
- Supports cash/mobile banking style collection marking.

### Print manager

Files:

- `assistant_chamber/controllers/Assistant_chamber.php`
- `assistant_chamber/views/assistant/desk.php`

Methods:

- `Assistant_chamber::print_latest_rx()`
- `Assistant_chamber::desk_rx_poll()`
- `Assistant_chamber::print_prescription()`

What it does:

- Lets assistant print the latest prescription after doctor saves it.
- Desk can poll for recent prescription output and open print flow.

---

## 7. Database and Migration Layer

### Main migrations

- `public_html/application/migrations/20260419000007_create_chamber_practice_saas.php`
- `public_html/application/migrations/20260420000008_chamber_indexes_and_bd_gateways.php`
- `public_html/application/migrations/20260421000009_doctor_chamber_weekly_hours.php`
- `public_html/application/migrations/20260421000010_request_path_indexes.php`
- `public_html/application/migrations/20260424000011_chamber_practice_advance_fee.php`

### Migration config

- `public_html/application/config/migration.php`

The migration version is updated through `20260424000011`.

### SQL patch

- `database_chamber_practice_saas_patch.sql`
- `database_chamber_practice_saas_verify.sql`

### Main chamber practice tables

| Table | Purpose |
|---|---|
| `doctor_portal_profile` | Public doctor profile, slug, profile media, signature, and advance booking fee. |
| `doctor_chamber` | Doctor chamber/location records and weekly hours. |
| `chamber_serial_queue` | Booking queue, serials, triage JSON, status, payment info. |
| `chamber_queue_ticker` | Current serial being served. |
| `booking_otp_session` | OTP hash, attempts, expiry, verification status. |
| `visit_vital` | Assistant-entered patient vitals. |
| `patient_practice_tag` | Doctor/hospital scoped patient tags. |
| `doctor_schedule_exception` | Vacation dates and one-day chamber timing overrides. |
| `prescription_print_template` | Doctor prescription header/footer templates. |
| `prescription_favorite` | Saved common medicine/prescription snippets. |
| `subscription_plan` | SaaS package/module plan support. |
| `doctor_subscription` | Doctor subscription/plan assignment support. |
| `sms_credit_ledger` | SMS credit accounting. |
| `usage_analytics_event` | Usage events and queued AI call requests. |
| `referral_lab_event` | Doctor to lab referral tracking. |
| `medicine_reminder` | Patient medicine reminder schedule. |
| `bd_payment_intent` | SSLCommerz/bKash payment intents and status. |

### New advance booking fee field

Migration:

- `20260424000011_chamber_practice_advance_fee.php`

Table/field:

- `doctor_portal_profile.advance_booking_fee decimal(12,2) default 0.00`

Usage:

- Doctor sets booking fee from profile.
- Public booking reads fee server-side.
- Patient cannot override amount from form POST.

---

## 8. OOP and Architecture Concepts Used

### HMVC modular MVC

The implementation follows the existing HMVC structure:

- Controllers under `application/modules/{module}/controllers`
- Models under `application/modules/{module}/models`
- Views under `application/modules/{module}/views`

This keeps Patient Portal, Doctor Portal, Assistant Portal, Payment, Cron, and Prescription responsibilities separated.

### Controller responsibility separation

The implementation uses separate controllers by role:

- `Portal` for unauthenticated/public booking flow.
- `Patient_chamber` for logged-in patient self-service.
- `Doctor_chamber` for doctor operations.
- `Assistant_chamber` for assistant desk operations.
- `Payment_bd` for Bangladesh payment gateways.
- `Cronjobs` for scheduled reminders.
- `Prescription` for existing prescription/PDF generation with chamber template extension.

### Model gateway methods

`Portal_model` wraps public booking queries:

- Profile lookup
- Chamber lookup
- OTP persistence
- Patient find/create
- Recent booking throttling

This avoids placing every SQL detail directly inside the public controller.

### Feature gate helper

The `chamber_practice_helper` acts like a module-level policy helper:

- Resolves hospital context.
- Reads enabled module/package data.
- Normalizes module keys.
- Blocks access when not enabled.

This is the core of the on/off module behavior.

### Role-based access

The existing Ion Auth role model is used:

- Patient routes require `Patient`.
- Doctor chamber routes require doctor context through existing user/session relationships.
- Assistant routes require assistant/hospital context.
- Public portal routes are public but still gated by hospital module status.

### Payment gateway separation

`Payment_bd` separates SSLCommerz and bKash flows into independent methods. This behaves like a strategy-style separation, but it is implemented using controller methods and helper routines, not a formal Strategy class.

### Scheduled job integration

Medicine reminders are implemented through the existing cron controller pattern:

- `Cronjobs::chamberMedicineReminders()`

This keeps reminder sending out of page-load requests.

---

## 9. How To Enable This Feature Set

### 1. Run database migration or SQL patch

Use the app's existing migration process or apply:

- `database_chamber_practice_saas_patch.sql`

Verify with:

- `database_chamber_practice_saas_verify.sql`

### 2. Enable the module

Enable the module key:

- `chamber_practice`

This can be done from package/hospital/module settings screens where the checkbox/list entry was added.

### 3. Configure doctor public profile

Doctor should set:

- Public slug
- Doctor photo
- Specialty/degree/experience
- Signature
- Advance booking fee
- Public booking availability

Controller/view:

- `Doctor_chamber::portal_profile()`
- `Doctor_chamber::portal_profile_save()`
- `doctor_chamber/views/doctor/portal_profile.php`

### 4. Configure chambers and schedules

Doctor should create/update:

- Chamber display name
- Address
- Phone
- Weekly hours
- Vacation/exception dates

Screens:

- `doctor_chamber/my_chambers`
- `doctor_chamber/schedule_exceptions`

### 5. Configure SMS and payment gateways

For real OTP/payment operation, the hospital must have:

- SMS gateway/credit configuration.
- SSLCommerz credentials if using SSLCommerz.
- bKash credentials if using bKash.
- Correct return/callback/IPN URLs.

---

## 10. Method Index

### Public patient booking

- `Portal::__construct()`
- `Portal::d($slug = '')`
- `Portal::ticker_json()`
- `Portal::request_otp()`
- `Portal::verify_otp()`
- `Portal::triage($slug = '')`
- `Portal::complete_booking()`
- `Portal::_send_otp_sms($hospital_id, $mobile, $plain)`

### Patient logged-in portal

- `Patient_chamber::__construct()`
- `Patient_chamber::myPatientId()`
- `Patient_chamber::my_prescriptions()`
- `Patient_chamber::lab_vault()`
- `Patient_chamber::reminders()`
- `Patient_chamber::reminder_save()`

### Doctor portal

- `Doctor_chamber::__construct()`
- `Doctor_chamber::currentDoctor()`
- `Doctor_chamber::portal_profile()`
- `Doctor_chamber::portal_profile_save()`
- `Doctor_chamber::dashboard()`
- `Doctor_chamber::revenue()`
- `Doctor_chamber::consultation_room()`
- `Doctor_chamber::vitals_json()`
- `Doctor_chamber::favorites()`
- `Doctor_chamber::favorite_save()`
- `Doctor_chamber::crm_search()`
- `Doctor_chamber::tag_patient()`
- `Doctor_chamber::schedule_exceptions()`
- `Doctor_chamber::schedule_save()`
- `Doctor_chamber::template_builder()`
- `Doctor_chamber::template_save()`
- `Doctor_chamber::refer_lab()`
- `Doctor_chamber::drug_search_json()`
- `Doctor_chamber::my_chambers()`
- `Doctor_chamber::chamber_create()`
- `Doctor_chamber::chamber_update()`

### Assistant portal

- `Assistant_chamber::__construct()`
- `Assistant_chamber::desk()`
- `Assistant_chamber::checkin()`
- `Assistant_chamber::vitals_save()`
- `Assistant_chamber::queue_reorder()`
- `Assistant_chamber::emergency_bump()`
- `Assistant_chamber::bulk_sms()`
- `Assistant_chamber::bulk_sms_send()`
- `Assistant_chamber::bulk_call_request()`
- `Assistant_chamber::mark_queue_fee_paid()`
- `Assistant_chamber::mark_serving()`
- `Assistant_chamber::mark_status()`
- `Assistant_chamber::manual_booking()`
- `Assistant_chamber::manual_booking_save()`
- `Assistant_chamber::print_prescription()`
- `Assistant_chamber::desk_rx_poll()`
- `Assistant_chamber::print_latest_rx()`

### Payment

- `Payment_bd::__construct()`
- `Payment_bd::init_sslcommerz()`
- `Payment_bd::sslcommerz_return()`
- `Payment_bd::sslcommerz_ipn()`
- `Payment_bd::sslcommerz_fail()`
- `Payment_bd::init_bkash()`
- `Payment_bd::bkash_callback()`

### Cron

- `Cronjobs::chamberMedicineReminders()`

---

## 11. Verification Performed

### Static PHP lint

A PHP lint sweep was performed with:

`C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe -l`

Files linted included the chamber helper, controllers, views, migration, config changes, package screens, payment controller, cron controller, and prescription controller.

Result:

- PHP syntax check passed for the touched files.

### Not performed in this pass

- Browser smoke test.
- Database migration execution against a live database.
- Real SSLCommerz/bKash sandbox transaction.
- Real SMS gateway delivery test.
- Real telephony/AI call provider test.

---

## 12. Remaining Limitations / Honest Gap List

1. Live queue is AJAX polling, not WebSocket/SSE.
2. Medicine reminders are SMS cron based, not push notification based.
3. AI call button queues/logs a request; no outbound call provider is connected yet.
4. Drug database search does not include interaction checking or AI clinical decision support.
5. Payment routes are implemented, but production payment success depends on real configured gateway credentials and callback URLs.
6. Full end-to-end QA needs a database-backed browser test with module enabled, doctor profile configured, chamber created, OTP sent, booking completed, and assistant/doctor workflows exercised.

---

## 13. Final Status

The requested feature set is implemented as a switchable `chamber_practice` module inside the existing CodeIgniter/HMVC codebase.

The original Patient Portal, Doctor Portal, and Assistant Portal items are covered. The only partial items are the ones that require external real-time or third-party infrastructure:

- WebSocket-level live queue.
- Mobile push notifications.
- Actual AI/outbound call provider.
- Real payment/SMS gateway production validation.

Everything else is wired into the codebase with module gating, role-specific controllers, database migrations, package/module visibility, and patient/doctor/assistant views.
