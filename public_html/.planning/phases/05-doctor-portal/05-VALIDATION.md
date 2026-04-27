---
phase: 5
slug: doctor-portal
status: draft
nyquist_compliant: true
wave_0_complete: false
created: 2026-04-28
---

# Phase 5 — Validation Strategy

> Per-phase validation contract for feedback sampling during execution.

---

## Test Infrastructure

| Property | Value |
|----------|-------|
| **Framework** | PHPUnit 10.x + grep/static analysis |
| **Config file** | `phpunit.xml.dist` |
| **Quick run command** | `composer test -- --filter DoctorPortal` |
| **Full suite command** | `composer test` |
| **Estimated runtime** | ~30 seconds |

---

## Sampling Rate

- **After every task commit:** Run `composer test -- --filter DoctorPortal`
- **After every plan wave:** Run `composer test`
- **Before `/gsd-verify-work`:** Full suite must be green
- **Max feedback latency:** 30 seconds

---

## Per-Task Verification Map

| Task ID | Plan | Wave | Requirement | Expected Behavior | Test Type | Automated Command | Status |
|---------|------|------|-------------|-------------------|-----------|-------------------|--------|
| 5-00-01 | 00 | 0 | All UI-0x | DoctorPortalTest.php scaffold exists | unit | `composer test -- --filter DoctorPortal` | ⬜ pending |
| 5-01-01 | 01 | 1 | UI-09 | dashboard.php has 4 stat cards (today/checked-in/pending/revenue) | grep | `grep -c "chamber-stat-value" application/modules/doctor_chamber/views/doctor/dashboard.php` → ≥4 | ⬜ pending |
| 5-01-02 | 01 | 1 | UI-10 | `today_revenue` query exists in Doctor_chamber controller | grep | `grep -c "today_revenue\|SUM.*doctor_amount" application/modules/doctor_chamber/controllers/Doctor_chamber.php` → ≥1 | ⬜ pending |
| 5-02-01 | 02 | 1 | UI-10 | `queue_json()` endpoint exists and returns serial/patient/status | grep | `grep -c "function queue_json" application/modules/doctor_chamber/controllers/Doctor_chamber.php` → 1 | ⬜ pending |
| 5-02-02 | 02 | 1 | UI-10 | Dashboard has live queue panel with 12s long-poll JS | grep | `grep -c "queue_json\|setInterval\|12000" application/modules/doctor_chamber/views/doctor/dashboard.php` → ≥2 | ⬜ pending |
| 5-03-01 | 03 | 2 | UI-10 | `chart_data_json()` endpoint returns 6-month revenue+patient arrays | grep | `grep -c "function chart_data_json" application/modules/doctor_chamber/controllers/Doctor_chamber.php` → 1 | ⬜ pending |
| 5-03-02 | 03 | 2 | UI-10 | Dashboard bottom charts use Chart.js with correct data binding | grep | `grep -c "new Chart\|chart_data_json" application/modules/doctor_chamber/views/doctor/dashboard.php` → ≥2 | ⬜ pending |
| 5-04-01 | 04 | 2 | UI-10 | Right panel shows follow-up count and high-risk patients from patient_practice_tag | grep | `grep -c "patient_practice_tag\|followup_count\|high_risk_patients" application/modules/doctor_chamber/controllers/Doctor_chamber.php` → ≥2 | ⬜ pending |
| 5-05-01 | 05 | 3 | UI-11 | `search_json()` endpoint handles by-ID and by-date search | grep | `grep -c "function search_json" application/modules/doctor_chamber/controllers/Doctor_chamber.php` → 1 | ⬜ pending |
| 5-05-02 | 05 | 3 | UI-11 | Consultation room left panel has radio-pill search toggle + favorites dropdown | grep | `grep -c "chamber-radio-pill\|rx_templates\|search_json" application/modules/doctor_chamber/views/doctor/consultation_room.php` → ≥3 | ⬜ pending |

*Status: ⬜ pending · ✅ green · ❌ red · ⚠️ flaky*

---

## Wave 0 Requirements

- [ ] `tests/Unit/DoctorPortal/DoctorPortalTest.php` — consolidated class with `test_ui09_*` through `test_ui11_*` methods (created by plan 05-00)

---

## Manual-Only Verifications

| Behavior | Requirement | Why Manual | Test Instructions |
|----------|-------------|------------|-------------------|
| Stat cards show accurate live counts | UI-09 | Requires live DB + running queue | Load dashboard; verify 4 cards match actual appointment/payment data for today |
| Queue long-poll updates within 12s | UI-10 | Requires queue state change | Check in a patient in assistant portal; verify queue panel updates within 12s without page reload |
| Charts render with 6 months of data | UI-10 | Requires Chart.js render + real data | Load dashboard; verify revenue and patient count bar charts render correctly |
| Consultation room: full workflow | UI-11 | Requires live patient + iframe | Load patient, search history, open e-pad, write prescription, save and print |

---

## Validation Sign-Off

- [x] All tasks have `<automated>` verify or Wave 0 dependencies
- [x] Sampling continuity: no 3 consecutive tasks without automated verify
- [x] Wave 0 covers all MISSING references
- [x] No watch-mode flags
- [x] Feedback latency < 30s
- [x] `nyquist_compliant: true` set in frontmatter

**Approval:** pending
