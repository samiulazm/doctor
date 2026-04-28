# Doctor Chamber Management System

## What This Is

A multi-tenant SaaS platform for managing independent doctor chambers and small practices in Bangladesh. Doctors, assistants, and patients interact through role-based portals covering appointment booking, live queue management, e-prescriptions, billing, and patient records. Built on CodeIgniter 3 + PHP 8.1, serving the Asia/Dhaka market with local payment integrations (bKash, SSLCommerz). Legacy broader modules remain available where they support records, billing, lab, and pharmacy workflows, but the launch product is chamber/practice management.

## Core Value

Real doctors actively using the system to manage their daily patient queue — every other feature is secondary to that live usage loop.

## Requirements

### Validated

- ✓ Multi-tenant practice isolation with internal `hospital_id` scoping — existing
- ✓ Role-based authentication (Ion Auth) — Doctor, Assistant, Admin, Patient, Super Admin — existing
- ✓ Appointment booking with time slots and chamber-wise scheduling — existing
- ✓ Serial-based queue management with check-in flow — existing
- ✓ Doctor dashboard (patient counts, revenue, live queue) — existing
- ✓ E-prescription creation with PDF export — existing
- ✓ Patient records management with timeline — existing
- ✓ Finance/billing module — existing
- ✓ Lab module — existing
- ✓ Pharmacy/inventory module — existing
- ✓ Admin/SaaS platform panel (subscription, SMS credits, user management) — existing
- ✓ 30+ language packs (multi-language support) — existing
- ✓ Email and SMS notification infrastructure — existing

### Active

- [ ] **SEC-01**: Fix CRITICAL API token — replace `rand(1111,9999)` with `bin2hex(random_bytes(32))`
- [ ] **SEC-02**: Fix cross-tenant data leak — validate `hospital_id` in all 6 vulnerable API endpoints
- [ ] **SEC-03**: Enforce Ion Auth login lockout — call `is_max_login_attempts_exceeded()` in auth controller
- [ ] **SEC-04**: Remove debug endpoint — delete or auth-guard `debugSession()` in `Ai_image_analysis`
- [ ] **SEC-05**: Parameterize 165+ LIKE SQL injection vectors — use `$this->db->like()`
- [ ] **SEC-06**: Remove 4 hardcoded `'12345'` passwords from controllers
- [ ] **SEC-07**: Remove 18 `echo print_r()` / `echo "Database error"` debug outputs from production code
- [ ] **SEC-08**: Add Content-Security-Policy header in `security_headers.php`
- [ ] **SEC-09**: Move OpenAI API key from DB settings to env var `OPENAI_API_KEY`
- [ ] **SEC-10**: Remove `init_categories()` auth guard gap — prevent unauthenticated table DROP
- [ ] **UI-01**: Consistent admin UI layout across all modules — match AdminLTE 3 patterns
- [ ] **UI-02**: DataTables integration standardized (server-side, consistent column config)
- [ ] **UI-03**: CSRF token handling consistent across all AJAX POST requests
- [ ] **UI-04**: Design system applied — buttons, inputs, cards, modals, status tags (Green/Yellow/Red/Blue)
- [ ] **UI-05**: Patient-facing web portal — home, booking flow (5-step), queue screen, prescription view
- [ ] **UI-06**: Doctor consultation room — left panel (patient history), right panel (e-pad + medicine search + templates), save + print
- [ ] **UI-07**: Assistant queue screen — drag & drop serial, color tags, check-in / emergency / print
- [ ] **UI-08**: Assistant billing screen — fee entry, paid/due toggle, cash/bKash payment method
- [ ] **BUG-01**: Dashboard N+1 query — `get('patient')->result()` full-table scan inside appointment loop
- [ ] **BUG-02**: Remove `testpkz` dead module and `home_backup.php` view from production
- [ ] **BUG-03**: Replace deprecated PHPExcel with `phpoffice/phpspreadsheet`
- [ ] **BUG-04**: Remove `die()` inside `Api::getAppointmentById()` — returns no JSON response
- [ ] **BUG-05**: Fix `updateTimezone()` — stop writing to `index.php` directly; use DB + `date_default_timezone_set()`
- [ ] **BUG-06**: Remove `error_reporting(0)` suppression from Bed, Finance, Patient, Payroll controllers
- [ ] **PAY-01**: bKash payment integration on assistant billing screen
- [ ] **PAY-02**: SSLCommerz payment integration
- [ ] **QUEUE-01**: Real-time queue updates (WebSocket or long-poll) — live serial/serving number sync
- [ ] **AUTH-01**: OTP-based patient login flow

### Out of Scope

- Flutter mobile app — Phase 2 (patient retention phase)
- React/Next.js rebuild — no; completing existing CI3 system
- AI prescription suggestion — Phase 3
- Voice-to-text — Phase 3
- WhatsApp bot — Phase 3
- Lab integration (external) — Phase 3
- Centralized audit log (HIPAA-grade) — post-launch hardening
- Read replicas / connection pooling — post-launch scaling

## Context

- **Market:** Bangladesh doctor chambers and small practices; primary language Bangla, UI in English
- **Payment methods:** bKash (mobile), SSLCommerz (card/bank), cash on-spot
- **Timezone:** Asia/Dhaka
- **Platform release:** 0.2.0 (tracked in `application/config/platform.php`)
- **Codebase state:** CodeIgniter 3 (HMVC via WireDesignz MX), PHP 8.1+, MySQL, AdminLTE 3, Bootstrap 4, jQuery
- **AI modules in progress:** `ai_image_analysis`, `ai_patient_overview`, `openai.php` config untracked — secondary to launch path
- **Security posture:** Significant pre-launch hardening required — patient medical data demands high bar

## Constraints

- **Timeline:** ASAP — production deployment is the goal
- **Tech stack:** CI3/PHP — no framework switch; complete within existing architecture
- **Security:** Fix all CRITICAL + HIGH issues before production deploy — non-negotiable for patient data
- **Deployment target:** Apache/LiteSpeed shared hosting, Bangladesh

## Key Decisions

| Decision | Rationale | Outcome |
|----------|-----------|---------|
| Complete CI3 system, not rebuild | 90% done; rebuild would delay months | — Pending |
| Fix all CRITICAL security first | Patient medical data — cannot ship vulnerable | — Pending |
| Use existing AdminLTE 3 + Bootstrap 4 | No frontend build pipeline; design system layered on top | — Pending |
| bKash + SSLCommerz (not Stripe) | Bangladesh local payment methods; Stripe manually vendored at v7 | — Pending |
| Product framing is doctor/chamber practice management | `hospital_id` remains an internal tenant key, not launch product copy | Active portal copy says practice/chamber |

## Evolution

This document evolves at phase transitions and milestone boundaries.

**After each phase transition** (via `/gsd-transition`):
1. Requirements invalidated? → Move to Out of Scope with reason
2. Requirements validated? → Move to Validated with phase reference
3. New requirements emerged? → Add to Active
4. Decisions to log? → Add to Key Decisions
5. "What This Is" still accurate? → Update if drifted

**After each milestone** (via `/gsd-complete-milestone`):
1. Full review of all sections
2. Core Value check — still the right priority?
3. Audit Out of Scope — reasons still valid?
4. Update Context with current state

---
*Last updated: 2026-04-27 after initialization*
