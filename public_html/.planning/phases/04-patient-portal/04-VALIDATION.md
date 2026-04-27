---
phase: 4
slug: patient-portal
status: complete
nyquist_compliant: true
wave_0_complete: true
created: 2026-04-28
---

# Phase 4 - Validation Strategy

> Per-phase validation contract for feedback sampling during execution.

---

## Test Infrastructure

| Property | Value |
|----------|-------|
| **Framework** | PHPUnit 10.x + grep/static analysis |
| **Config file** | `phpunit.xml.dist` |
| **Quick run command** | `composer test -- --filter PatientPortal` |
| **Full suite command** | `composer test` |
| **Estimated runtime** | ~30 seconds |

---

## Sampling Rate

- **After every task commit:** Run `composer test -- --filter PatientPortal`
- **After every plan wave:** Run `composer test`
- **Before `/gsd-verify-work`:** Full suite must be green
- **Max feedback latency:** 30 seconds

---

## Per-Task Verification Map

| Task ID | Plan | Wave | Requirement | Expected Behavior | Test Type | Automated Command | Status |
|---------|------|------|-------------|-------------------|-----------|-------------------|--------|
| 4-00-01 | 00 | 0 | All | PatientPortalTest.php has 9 green assertions | unit | `composer test -- --filter PatientPortal` | green |
| 4-01-01 | 01 | 1 | UI-05 | Landing page shows doctor info, upcoming appointment, queue status | grep | `grep -c "chamber-kicker\|upcoming-card\|queue-ticker" application/modules/portal/views/portal/landing.php` -> >=3 | green |
| 4-02-01 | 02 | 1 | AUTH-01 | OTP panel HTML and JS exist in landing view | grep | `grep -c "otpLoginPanel\|sendOtp\|verifyOtp" application/modules/portal/views/portal/landing.php` -> >=3 | green |
| 4-03-01 | 03 | 2 | UI-06 | 5-step booking flow in triage.php has step panels | grep | `grep -c "chamber-step\|step-panel\|data-step" application/modules/portal/views/portal/triage.php` -> >=5 | green |
| 4-03-02 | 03 | 2 | PAY-02 | SSLCommerz init flow exists and booking success links to it | grep | `grep -c "function init_sslcommerz\|insertBdIntent\|initiateSession\|payment_bd/init_sslcommerz" application/modules/payment_bd/controllers/Payment_bd.php application/modules/portal/views/portal/book_success.php` -> >=4 | green |
| 4-04-01 | 04 | 3 | UI-07 | Queue controller method and view exist | grep | `grep -c "function queue" application/modules/portal/controllers/Portal.php` -> >=1 && `test -f application/modules/portal/views/portal/queue.php` | green |
| 4-04-02 | 04 | 3 | UI-07 | ticker_json returns patient_serial and estimated_wait | grep | `grep -c "patient_serial\|estimated_wait" application/modules/portal/controllers/Portal.php` -> >=1 | green |
| 4-05-01 | 05 | 3 | UI-08 | Prescription controller method and view exist | grep | `grep -c "function prescription" application/modules/portal/controllers/Portal.php` -> >=1 && `test -f application/modules/portal/views/portal/rx_detail.php` | green |
| 4-05-02 | 05 | 3 | UI-08 | Prescription PDF download endpoint exists | grep | `grep -c "function prescription_pdf\|Mpdf\|mpdf" application/modules/portal/controllers/Portal.php` -> >=1 | green |

*Status: pending / green / red / flaky*

---

## Wave 0 Requirements

- [x] `tests/Unit/PatientPortal/PatientPortalTest.php` - consolidated class with `test_auth01_*`, `test_ui06_*`, `test_pay02_*`, `test_ui07_*`, and `test_ui08_*` methods (created by plan 04-00)

---

## Manual-Only Verifications

| Behavior | Requirement | Why Manual | Test Instructions |
|----------|-------------|------------|-------------------|
| OTP SMS received on real phone | AUTH-01 | Requires live SMS gateway | Enter real phone number; verify OTP arrives within 30s |
| 5-step booking completes end-to-end | UI-06 | Requires live DB + doctor schedule | Complete full booking; verify confirmation screen shows serial |
| SSLCommerz payment redirect works | PAY-02 | Requires live SSLCommerz sandbox | Select advance payment; verify redirect to SSLCommerz; complete; verify return to book_success |
| Queue screen updates without reload | UI-07 | Requires live queue with serial changes | Change serving serial in assistant panel; verify patient queue screen updates within 15s |
| Prescription PDF downloads | UI-08 | Requires mPDF + existing prescription | Click download on prescription; verify PDF opens with correct content |

---

## Validation Sign-Off

- [x] All tasks have `<automated>` verify or Wave 0 dependencies
- [x] Sampling continuity: no 3 consecutive tasks without automated verify
- [x] Wave 0 covers all MISSING references
- [x] No watch-mode flags
- [x] Feedback latency < 30s
- [x] `nyquist_compliant: true` set in frontmatter

**Approval:** complete
