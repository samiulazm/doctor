# Requirements: Doctor Chamber Management System

**Defined:** 2026-04-27
**Core Value:** Real doctors actively using the system to manage their daily patient queue

## v1 Requirements

Requirements for production launch. All CRITICAL security issues must ship before any other category.

### Security Hardening

- [ ] **SEC-01**: System replaces trivially brute-forceable `rand(1111,9999)` API token with `bin2hex(random_bytes(32))` in `Api::authenticateNew()`
- [ ] **SEC-02**: System validates `hospital_id` server-side in all 6 API endpoints (`getPatientInfoList`, `getDiagnosisInfo`, etc.) — no caller-supplied tenant override
- [ ] **SEC-03**: Auth controller enforces Ion Auth login lockout by calling `is_max_login_attempts_exceeded()` and `is_time_locked_out()` before granting access
- [ ] **SEC-04**: `debugSession()` endpoint in `Ai_image_analysis` is removed or requires authentication — no unauthenticated session dump
- [ ] **SEC-05**: All 165+ LIKE clauses in models/controllers use `$this->db->like()` instead of string interpolation inside `where(..., NULL, FALSE)`
- [ ] **SEC-06**: All 4 hardcoded `'12345'` fallback passwords removed from `Frontend`, `Meeting`, `Payu`, and `Request` controllers
- [ ] **SEC-07**: All 18 `echo print_r()` and `echo "Database error"` debug outputs removed from production controllers
- [ ] **SEC-08**: `Content-Security-Policy` header added to `application/hooks/security_headers.php`
- [ ] **SEC-09**: OpenAI API key moved from `settings` DB row to `OPENAI_API_KEY` environment variable; existing DB key nulled
- [ ] **SEC-10**: `Inventory::init_categories()` requires explicit admin-only auth guard before executing `DROP TABLE` logic

### UI Consistency & Admin Polish

- [ ] **UI-01**: All module views follow consistent AdminLTE 3 layout — unified card/section structure, no legacy inline styles
- [ ] **UI-02**: DataTables initialized with standardized server-side config across all list views (consistent column layout, search, pagination)
- [ ] **UI-03**: All AJAX POST requests include CSRF token header; `csrf_regenerate = true` enabled in `config.php`
- [ ] **UI-04**: Design system tokens applied — primary/secondary/danger buttons, input fields, card components, modal templates, status tags (Green=success, Yellow=pending, Red=emergency, Blue=primary action)

### Patient Portal

- [ ] **UI-05**: Patient home screen shows doctor info, upcoming appointment card, live queue status, book appointment button, prescriptions, lab reports
- [ ] **UI-06**: Patient booking flow — 5-step UI (select doctor → select chamber → select time slot with calendar → OTP verify → confirm booking)
- [ ] **UI-07**: Patient queue screen shows: patient's serial number, currently serving number, estimated wait time
- [ ] **UI-08**: Patient prescription view shows doctor name, date, medicine list, advice, and download PDF button

### Doctor Portal

- [ ] **UI-09**: Doctor dashboard top cards: total patients today, checked-in count, pending count, today's revenue
- [ ] **UI-10**: Doctor dashboard main panel shows live queue; right panel shows follow-up count and high-risk patients; bottom shows monthly revenue and patient count graphs
- [ ] **UI-11**: Doctor consultation room — left panel (40%): patient history search (by date or unique ID) + previous prescriptions; right panel (60%): e-pad with medicine search and templates; bottom: save + print action

### Assistant Portal

- [ ] **UI-12**: Assistant queue screen shows patient list (manual entry, phone booking, website booking); drag-and-drop serial reordering; color-coded tags; check-in, emergency override, and print buttons
- [ ] **UI-13**: Assistant billing screen: patient name, fee input, paid/due toggle, payment method selector (cash or bKash)

### Authentication

- [ ] **AUTH-01**: Patient can log in via OTP sent to mobile number — no email/password required for patient role

### Queue & Real-time

- [ ] **QUEUE-01**: Queue screen updates in real-time (WebSocket or long-poll) — serial number and "now serving" sync across doctor, assistant, and patient views without page reload

### Payments

- [ ] **PAY-01**: Assistant billing screen processes bKash payment — initiates bKash API request, shows payment status
- [ ] **PAY-02**: Patient booking flow supports SSLCommerz payment for advance booking fee

### Bug Fixes

- [ ] **BUG-01**: Dashboard removes full-table patient scan inside appointment loop — replace with keyed lookup or `where_in`
- [ ] **BUG-02**: `testpkz` module directory and `home_backup.php` view file deleted from production codebase
- [ ] **BUG-03**: PHPExcel library replaced with `phpoffice/phpspreadsheet` in import module
- [ ] **BUG-04**: `die()` removed from `Api::getAppointmentById()` — returns proper JSON error instead
- [ ] **BUG-05**: `updateTimezone()` stops writing to `index.php` directly — stores timezone in DB only, applies via `date_default_timezone_set()` at boot
- [ ] **BUG-06**: `error_reporting(0)` removed from Bed, Finance, Patient, and Payroll controllers — error visibility controlled by `ENVIRONMENT` constant

## v2 Requirements

Deferred to post-launch growth phase.

### Patient Mobile App

- Flutter mobile app for iOS/Android with full booking, queue, and prescription flows

### Engagement

- SMS automation for appointment reminders and queue notifications
- CRM tagging system for patient segmentation (high-risk, follow-up, VIP)
- Revenue analytics dashboard for admin

### Infrastructure

- Background job queue for async SMS/email sends with retry
- Centralized audit log for patient data changes (HIPAA-aligned)
- Read replica / connection pooling for scale

## v3 Requirements

Future scale features.

- AI prescription suggestion based on patient history
- Voice-to-text for consultation notes
- External lab integration
- WhatsApp bot for patient interactions
- Multi-doctor clinic / franchise management

## Out of Scope

| Feature | Reason |
|---------|--------|
| React/Next.js rebuild | 90% of CI3 system already built — rewrite would delay launch by months |
| Flutter app (Phase 1) | Desktop-first for launch; mobile deferred to Phase 2 |
| Stripe payments | Bangladesh market uses bKash + SSLCommerz; Stripe vendored manually at v7 |
| External lab integration | Phase 3 complexity; basic lab module already exists |
| HIPAA full audit trail | Post-launch hardening; not required for Bangladesh regulatory context at launch |

## Traceability

| Requirement | Phase | Status |
|-------------|-------|--------|
| SEC-01 | Phase 1 — Security Hardening | Pending |
| SEC-02 | Phase 1 — Security Hardening | Pending |
| SEC-03 | Phase 1 — Security Hardening | Pending |
| SEC-04 | Phase 1 — Security Hardening | Pending |
| SEC-05 | Phase 1 — Security Hardening | Pending |
| SEC-06 | Phase 1 — Security Hardening | Pending |
| SEC-07 | Phase 1 — Security Hardening | Pending |
| SEC-08 | Phase 1 — Security Hardening | Pending |
| SEC-09 | Phase 1 — Security Hardening | Pending |
| SEC-10 | Phase 1 — Security Hardening | Pending |
| BUG-01 | Phase 2 — Code Health | Pending |
| BUG-02 | Phase 2 — Code Health | Pending |
| BUG-03 | Phase 2 — Code Health | Pending |
| BUG-04 | Phase 2 — Code Health | Pending |
| BUG-05 | Phase 2 — Code Health | Pending |
| BUG-06 | Phase 2 — Code Health | Pending |
| UI-01 | Phase 3 — Admin Design System | Pending |
| UI-02 | Phase 3 — Admin Design System | Pending |
| UI-03 | Phase 3 — Admin Design System | Pending |
| UI-04 | Phase 3 — Admin Design System | Pending |
| UI-05 | Phase 4 — Patient Portal | Pending |
| UI-06 | Phase 4 — Patient Portal | Pending |
| UI-07 | Phase 4 — Patient Portal | Pending |
| UI-08 | Phase 4 — Patient Portal | Pending |
| AUTH-01 | Phase 4 — Patient Portal | Pending |
| PAY-02 | Phase 4 — Patient Portal | Pending |
| UI-09 | Phase 5 — Doctor Portal | Pending |
| UI-10 | Phase 5 — Doctor Portal | Pending |
| UI-11 | Phase 5 — Doctor Portal | Pending |
| UI-12 | Phase 6 — Assistant Portal + Real-Time | Pending |
| UI-13 | Phase 6 — Assistant Portal + Real-Time | Pending |
| PAY-01 | Phase 6 — Assistant Portal + Real-Time | Pending |
| QUEUE-01 | Phase 6 — Assistant Portal + Real-Time | Pending |

**Coverage:**
- v1 requirements: 33 total (SEC x10, BUG x6, UI x13, AUTH x1, QUEUE x1, PAY x2)
- Mapped to phases: 33
- Unmapped: 0

**Note on count:** Requirements count is 33, not 35 as initially estimated during intake. All named requirements (SEC-01 to SEC-10, UI-01 to UI-13, AUTH-01, QUEUE-01, PAY-01, PAY-02, BUG-01 to BUG-06) are accounted for.

---
*Requirements defined: 2026-04-27*
*Last updated: 2026-04-27 — traceability table completed after roadmap creation*
