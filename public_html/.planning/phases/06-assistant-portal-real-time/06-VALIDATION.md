---
phase: 6
slug: assistant-portal-real-time
status: draft
nyquist_compliant: true
wave_0_complete: false
created: 2026-04-28
---

# Phase 6 — Validation Strategy

> Per-phase validation contract for feedback sampling during execution.

---

## Test Infrastructure

| Property | Value |
|----------|-------|
| **Framework** | PHPUnit 10.x + grep/static analysis |
| **Config file** | `phpunit.xml.dist` |
| **Quick run command** | `composer test -- --filter AssistantPortal` |
| **Full suite command** | `composer test` |
| **Estimated runtime** | ~30 seconds |

---

## Sampling Rate

- **After every task commit:** Run `composer test -- --filter AssistantPortal`
- **After every plan wave:** Run `composer test`
- **Before `/gsd-verify-work`:** Full suite must be green
- **Max feedback latency:** 30 seconds

---

## Per-Task Verification Map

| Task ID | Plan | Wave | Requirement | Expected Behavior | Test Type | Automated Command | Status |
|---------|------|------|-------------|-------------------|-----------|-------------------|--------|
| 6-00-01 | 00 | 0 | All | AssistantPortalTest.php scaffold exists | unit | `composer test -- --filter AssistantPortal` | ⬜ pending |
| 6-01-01 | 01 | 1 | SCHEMA | `is_emergency` column exists in chamber_serial_queue | grep | `grep -c "is_emergency" application/migrations/` → ≥1 | ⬜ pending |
| 6-01-02 | 01 | 1 | UI-12 | 3 new CSS classes in chamber-practice.css | grep | `grep -c "chamber-status.emergency\|chamber-source-tag\|btn-bkash" application/assets/css/chamber-practice.css` → ≥3 | ⬜ pending |
| 6-02-01 | 02 | 2 | UI-12 | desk.php has drag-drop table + check-in/emergency/print buttons | grep | `grep -c "Sortable\|queue_reorder\|emergency_bump\|print_token" application/modules/assistant_chamber/views/assistant/desk.php` → ≥4 | ⬜ pending |
| 6-02-02 | 02 | 2 | QUEUE-01 | queue_ticker_json() endpoint exists in Assistant_chamber | grep | `grep -c "function queue_ticker_json" application/modules/assistant_chamber/controllers/Assistant_chamber.php` → 1 | ⬜ pending |
| 6-03-01 | 03 | 2 | UI-13 | billing.php has fee input, paid/due toggle, cash/bKash selector | grep | `grep -c "record_fee\|payment_method\|btn-bkash\|paid_status" application/modules/assistant_chamber/views/assistant/_billing_panel.php` → ≥4 | ⬜ pending |
| 6-03-02 | 03 | 2 | PAY-01 | bkash_initiate_desk() endpoint exists in Payment_bd | grep | `grep -c "function bkash_initiate_desk\|bkash_desk" application/modules/payment_bd/controllers/Payment_bd.php` → ≥1 | ⬜ pending |
| 6-04-01 | 04 | 3 | QUEUE-01 | print_token() endpoint exists; mark_queue_fee_paid_ajax() exists | grep | `grep -c "function print_token\|function mark_queue_fee_paid_ajax" application/modules/assistant_chamber/controllers/Assistant_chamber.php` → 2 | ⬜ pending |

*Status: ⬜ pending · ✅ green · ❌ red · ⚠️ flaky*

---

## Wave 0 Requirements

- [ ] `tests/Unit/AssistantPortal/AssistantPortalTest.php` — consolidated class with `test_ui12_*` through `test_queue01_*` methods (created by plan 06-00)

---

## Manual-Only Verifications

| Behavior | Requirement | Why Manual | Test Instructions |
|----------|-------------|------------|-------------------|
| Drag-drop reorder updates serial numbers + syncs to doctor/patient | UI-12 + QUEUE-01 | Requires live queue + multiple browser tabs | Reorder 2 patients in assistant view; verify serials update; verify doctor dashboard queue updates within 12s; verify patient queue screen shows updated "now serving" |
| Emergency bump moves patient to serial #1 | UI-12 | Requires live queue | Click Emergency on row 3; verify they move to position 1 with red tag |
| Print token generates PDF/print page | UI-12 | Requires live server + print preview | Click Token button; verify print dialog opens with correct serial |
| bKash initiate shows payment_id inline | PAY-01 | Requires bKash sandbox credentials | Select bKash, click Initiate bKash; verify payment_id appears inline in billing panel |
| End-to-end queue sync across all 3 portals | QUEUE-01 | Requires doctor + assistant + patient views open simultaneously | Open all 3 portals; check in patient; verify all 3 update within 15s |

---

## Validation Sign-Off

- [x] All tasks have `<automated>` verify or Wave 0 dependencies
- [x] Sampling continuity: no 3 consecutive tasks without automated verify
- [x] Wave 0 covers all MISSING references
- [x] No watch-mode flags
- [x] Feedback latency < 30s
- [x] `nyquist_compliant: true` set in frontmatter

**Approval:** pending
