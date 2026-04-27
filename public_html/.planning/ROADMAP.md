# Roadmap: Doctor Chamber Management System

## Overview

This is a brownfield completion project. The system is ~90% built on CodeIgniter 3 / PHP 8.1 with 70+ modules. The remaining work is a non-negotiable security hardening pass first, then code health cleanup, then completing the portal UIs that form the live usage loop for doctors, assistants, and patients. Each phase is a coherent delivery boundary that does not require undoing anything in the next phase.

## Phases

**Phase Numbering:**
- Integer phases (1, 2, 3): Planned milestone work
- Decimal phases (2.1, 2.2): Urgent insertions (marked with INSERTED)

Decimal phases appear between their surrounding integers in numeric order.

- [ ] **Phase 1: Security Hardening** - Fix all CRITICAL + HIGH vulnerabilities before any portal UI ships
- [ ] **Phase 2: Code Health** - Remove dead code, fix bugs, and eliminate tech debt that masks runtime failures
- [ ] **Phase 3: Admin Design System** - Unify AdminLTE 3 layout, DataTables, CSRF, and design tokens across all modules
- [ ] **Phase 4: Patient Portal** - Complete patient-facing portal: home, booking (with OTP + SSLCommerz), queue, and prescriptions
- [ ] **Phase 5: Doctor Portal** - Complete doctor dashboard and consultation room
- [ ] **Phase 6: Assistant Portal + Real-Time** - Complete assistant queue and billing screens, bKash integration, and live queue sync

## Phase Details

### Phase 1: Security Hardening
**Goal**: All CRITICAL and HIGH security vulnerabilities are patched — the system can hold patient medical data without active attack surface
**Depends on**: Nothing (first phase)
**Requirements**: SEC-01, SEC-02, SEC-03, SEC-04, SEC-05, SEC-06, SEC-07, SEC-08, SEC-09, SEC-10
**Success Criteria** (what must be TRUE):
  1. API token generation uses `bin2hex(random_bytes(32))` — a brute-force against the token space is computationally infeasible
  2. Every API endpoint validates `hospital_id` from session — a GET request with a foreign `hospital_id` returns 403, not another tenant's patient data
  3. Failed login attempts are tracked and the account is locked after the configured threshold — automated credential stuffing is blocked
  4. No unauthenticated HTTP request to `debugSession()` returns session data — the endpoint is removed or returns 401
  5. No `LIKE '%$var%'` string interpolation exists in any model or controller — all search inputs go through `$this->db->like()`
**Plans**: 10 plans
Plans:
- [ ] 01-00-PLAN.md — Wave 0: Create SecurityPatchTest.php scaffold (all 10 SEC assertions)
- [ ] 01-01-PLAN.md — Wave 1: SEC-01 + SEC-02 — Api.php token and hospital_id fixes
- [ ] 01-02-PLAN.md — Wave 1: SEC-04 + SEC-07 — Remove debug methods from Ai_image_analysis + Ambulance
- [ ] 01-03-PLAN.md — Wave 1: SEC-07 + SEC-10 — Remove debug methods + add auth guard in Inventory
- [ ] 01-04-PLAN.md — Wave 1: SEC-06 — Remove hardcoded '12345' from 4 controllers
- [ ] 01-05-PLAN.md — Wave 2: SEC-03 — Login lockout pre-check in Auth.php + ion_auth.php config
- [ ] 01-06-PLAN.md — Wave 2: SEC-09 — Move OpenAI key to env var across 5 files
- [ ] 01-07-PLAN.md — Wave 2: SEC-08 — Add CSP header to security_headers.php
- [ ] 01-08-PLAN.md — Wave 3: SEC-05 batch A — LIKE fix in 13 files (Appointment, Patient, Finance, Lab, Api, Ambulance, Bed, Doctor, Pharmacy, Site, Insurance, Inventory, Purchase)
- [ ] 01-09-PLAN.md — Wave 3: SEC-05 batch B — LIKE fix in 13 remaining files (Supplier, Lab_bk, Leave, Logs, Macro, Medicine, Meeting, Nurse, Prescription, Pservice, Sms, Systems, Email)
**UI hint**: no

### Phase 2: Code Health
**Goal**: Dead code is gone, critical bugs are fixed, and runtime errors are visible — the codebase is stable enough to build portal UIs on top of
**Depends on**: Phase 1
**Requirements**: BUG-01, BUG-02, BUG-03, BUG-04, BUG-05, BUG-06
**Success Criteria** (what must be TRUE):
  1. Doctor dashboard loads without executing a full patient table scan — page load time is not proportional to total patient count
  2. `testpkz` module directory and `home_backup.php` view file do not exist in the codebase
  3. Import module uses `phpoffice/phpspreadsheet` — no `PHPExcel` class is instantiated anywhere
  4. `Api::addAppointment()` returns a valid JSON response — no `die()` terminates the response body
  5. Timezone changes are stored in the DB and applied via `date_default_timezone_set()` at boot — `index.php` is never written to at runtime
  6. `error_reporting(0)` does not appear in any of the 7 known locations across Bed, Finance, Patient, Payroll, Prescription, Settings controllers
**Plans**: 5 plans
Plans:
- [ ] 02-00-PLAN.md — Wave 0: Create CodeHealthTest.php scaffold (all 6 BUG assertions, 15 test methods)
- [ ] 02-01-PLAN.md — Wave 1: BUG-01 + BUG-02 — N+1 patient query fix + testpkz/home_backup.php removal
- [ ] 02-02-PLAN.md — Wave 1: BUG-03 — PHPExcel → PhpSpreadsheet migration in Import.php
- [ ] 02-03-PLAN.md — Wave 1: BUG-04 + BUG-05 — die() fix in Api.php + timezone file-write removal from Home.php + Settings.php
- [ ] 02-04-PLAN.md — Wave 2: BUG-06 — Remove error_reporting(0) from all 7 sites across 6 controllers
**UI hint**: no

### Phase 3: Admin Design System
**Goal**: Every admin module view follows a consistent AdminLTE 3 layout with standardized DataTables, CSRF-safe AJAX, and a unified design token set — any admin page looks and behaves like it belongs to the same product
**Depends on**: Phase 2
**Requirements**: UI-01, UI-02, UI-03, UI-04
**Success Criteria** (what must be TRUE):
  1. Every module list view renders a DataTable with server-side pagination, search, and consistent column config — no list view uses a plain `<table>` without DataTables
  2. Every AJAX POST request sends the CSRF token header and `csrf_regenerate = true` is set — submitting any admin form in DevTools shows the token in request headers
  3. Buttons, inputs, modals, and status tags use the defined design token classes — a new developer can add a page using only the design system components without introducing custom inline styles
  4. All module views use the standard AdminLTE 3 card/section structure — no legacy inline-styled layout sections exist
**Plans**: TBD
**UI hint**: yes

### Phase 4: Patient Portal
**Goal**: Patients can log in via OTP, book appointments with advance payment, check their queue position, and view prescriptions — the full self-service loop works end-to-end
**Depends on**: Phase 3
**Requirements**: UI-05, UI-06, UI-07, UI-08, AUTH-01, PAY-02
**Success Criteria** (what must be TRUE):
  1. A patient can log in using only a mobile phone number — an OTP is sent via SMS and a valid OTP grants access without requiring an email or password
  2. A patient can complete a 5-step booking flow (select doctor → select chamber → select time slot → OTP verify → confirm) and pay an advance booking fee via SSLCommerz — a confirmed booking appears in the appointment list
  3. A patient on the queue screen sees their serial number, the currently serving number, and an estimated wait time without reloading the page
  4. A patient can view a prescription showing doctor name, date, medicine list, and advice, and download it as a PDF
**Plans**: TBD
**UI hint**: yes

### Phase 5: Doctor Portal
**Goal**: Doctors can see their day at a glance on the dashboard and conduct consultations from a single screen — the live usage loop that is the system's core value is fully operational
**Depends on**: Phase 3
**Requirements**: UI-09, UI-10, UI-11
**Success Criteria** (what must be TRUE):
  1. Doctor dashboard top cards show correct live counts: total patients today, checked-in count, pending count, and today's revenue — all four cards are accurate when the page loads
  2. Doctor dashboard main panel shows the live queue list; right panel shows follow-up count and high-risk patients; bottom section shows monthly revenue and patient count graphs
  3. Doctor can open the consultation room, search a patient's history by date or unique ID, write a prescription using the e-pad with medicine search and templates, and save and print — the complete consultation workflow completes without leaving the screen
**Plans**: TBD
**UI hint**: yes

### Phase 6: Assistant Portal + Real-Time
**Goal**: Assistants can manage the queue (drag-and-drop, color tags, check-in, emergency override), process billing with bKash, and both doctor and patient queue screens update live without page reload — the operational hub of the daily chamber is complete
**Depends on**: Phase 4, Phase 5
**Requirements**: UI-12, UI-13, PAY-01, QUEUE-01
**Success Criteria** (what must be TRUE):
  1. Assistant can reorder patients in the queue by drag-and-drop — serial numbers update and are reflected immediately on doctor and patient queue screens
  2. Assistant can check in a patient, mark one as emergency (moves to top), and print the serial token from the queue screen
  3. Assistant billing screen records fee, paid/due status, and payment method; selecting bKash initiates the bKash API payment flow and shows payment status inline
  4. Queue serial and "now serving" number update on doctor, assistant, and patient views in real-time (WebSocket or long-poll) without any manual page reload
**Plans**: TBD
**UI hint**: yes

## Progress

**Execution Order:**
Phases execute in numeric order: 1 → 2 → 3 → 4 → 5 → 6

| Phase | Plans Complete | Status | Completed |
|-------|----------------|--------|-----------|
| 1. Security Hardening | 0/10 | Planned | - |
| 2. Code Health | 0/5 | Planned | - |
| 3. Admin Design System | 0/TBD | Not started | - |
| 4. Patient Portal | 0/TBD | Not started | - |
| 5. Doctor Portal | 0/TBD | Not started | - |
| 6. Assistant Portal + Real-Time | 0/TBD | Not started | - |

---
*Roadmap created: 2026-04-27*
*Coverage: 33/33 v1 requirements mapped*
*Phase 1 planned: 2026-04-27 — 10 plans across 3 waves*
*Phase 2 planned: 2026-04-27 — 5 plans across 2 waves*
