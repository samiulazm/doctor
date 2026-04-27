---
phase: 2
slug: code-health
status: draft
nyquist_compliant: true
wave_0_complete: false
created: 2026-04-27
---

# Phase 2 — Validation Strategy

> Per-phase validation contract for feedback sampling during execution.

---

## Test Infrastructure

| Property | Value |
|----------|-------|
| **Framework** | PHPUnit 10.x |
| **Config file** | `phpunit.xml.dist` |
| **Quick run command** | `composer test -- --filter CodeHealth` |
| **Full suite command** | `composer test` |
| **Estimated runtime** | ~30 seconds |

---

## Sampling Rate

- **After every task commit:** Run `composer test -- --filter CodeHealth`
- **After every plan wave:** Run `composer test`
- **Before `/gsd-verify-work`:** Full suite must be green
- **Max feedback latency:** 30 seconds

---

## Per-Task Verification Map

| Task ID | Plan | Wave | Requirement | Secure Behavior | Test Type | Automated Command | Status |
|---------|------|------|-------------|-----------------|-----------|-------------------|--------|
| 2-00-01 | 00 | 0 | All BUG-0x | CodeHealthTest.php scaffold exists with stub assertions | unit | `composer test -- --filter CodeHealth` | ⬜ pending |
| 2-01-01 | 01 | 1 | BUG-01 | No bare `get('patient')` table scan in Home.php | grep | `grep -c "->get('patient')" application/modules/home/controllers/Home.php` → 0 | ⬜ pending |
| 2-01-02 | 01 | 1 | BUG-02 | testpkz directory and home_backup.php do not exist | grep | `test ! -f application/modules/home/views/home_backup.php && echo OK` | ⬜ pending |
| 2-02-01 | 02 | 1 | BUG-03 | PhpSpreadsheet installed; no PHPExcel class instantiated | grep | `composer show phpoffice/phpspreadsheet 2>/dev/null && grep -rn "new PHPExcel\|PHPExcel_IOFactory" application/ \| wc -l` → 0 | ⬜ pending |
| 2-02-02 | 02 | 1 | BUG-03 | Import module uses PhpSpreadsheet namespace | grep | `grep -c "PhpOffice\\\\PhpSpreadsheet" application/modules/import/controllers/Import.php` → 3 | ⬜ pending |
| 2-03-01 | 03 | 1 | BUG-04 | No `die()` in Api.php addAppointment | grep | `grep -n "die()" application/modules/api/controllers/Api.php` → 0 results | ⬜ pending |
| 2-03-02 | 03 | 1 | BUG-05 | updateTimezone() no longer writes to index.php | grep | `grep -c "fopen\|fwrite\|rename" application/modules/home/controllers/Home.php` → 0 | ⬜ pending |
| 2-04-01 | 04 | 2 | BUG-06 | No error_reporting(0) in any of 7 controller files | grep | `grep -rn "error_reporting(0)" application/modules/bed/ application/modules/finance/ application/modules/patient/ application/modules/payroll/ application/modules/prescription/ application/modules/settings/ \| wc -l` → 0 | ⬜ pending |

*Status: ⬜ pending · ✅ green · ❌ red · ⚠️ flaky*

---

## Wave 0 Requirements

- [ ] `tests/Unit/CodeHealth/CodeHealthTest.php` — consolidated class with `test_bug01_*` through `test_bug06_*` methods (created by plan 02-00)

*All Phase 2 verifications are grep/static-analysis based. Single consolidated test class covers all requirements.*

---

## Manual-Only Verifications

| Behavior | Requirement | Why Manual | Test Instructions |
|----------|-------------|------------|-------------------|
| Dashboard load time not proportional to patient count | BUG-01 | Runtime performance — requires DB with real data | Load dashboard with 1000+ patient rows; verify page loads in < 2s |
| Excel import works with PhpSpreadsheet | BUG-03 | Requires real .xlsx file upload | Upload a sample .xlsx to Import module; verify data imports correctly |
| Finance PDF generation still works after error_reporting fix | BUG-06 | PHP notices may corrupt output | Generate a Finance PDF report; verify no blank/broken output |

---

## Validation Sign-Off

- [x] All tasks have `<automated>` verify or Wave 0 dependencies
- [x] Sampling continuity: no 3 consecutive tasks without automated verify
- [x] Wave 0 covers all MISSING references
- [x] No watch-mode flags
- [x] Feedback latency < 30s
- [x] `nyquist_compliant: true` set in frontmatter

**Approval:** pending
