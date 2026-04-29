---
phase: 3
slug: admin-design-system
artifact: qa-signoff-checklist
status: in_progress
created: 2026-04-28
---

# Phase 3 — Comprehensive QA Sign-Off Checklist

Use this file as the execution sheet for final QA sign-off. Mark each item as pass/fail and capture evidence links (screenshot path, request URL, console log, or note).

---

## Execution Log

| Timestamp | Activity | Result |
|-----------|----------|--------|
| 2026-04-28 | Runtime verification with required PHP executable | ✅ PHP 8.4.20 confirmed via `php -v` |
| 2026-04-28 | DesignSystem regression suite (`--filter DesignSystem`) | ✅ 8 tests passed, 15 assertions |
| 2026-04-29 | Full regression suite (`phpunit.xml.dist`) | ✅ 108 tests passed, 403 assertions |
| 2026-04-29 | Project lint pass (`scripts/lint-php.php`) | ✅ 1266 PHP files linted |
| 2026-04-28 | Static preflight checks for UI-02/UI-03/UI-04 | ⚠️ Legacy DataTable ID and badge classes not found; browser verification pending |
| 2026-04-29 | Import/PDF/CSV static implementation checks | ⚠️ PhpSpreadsheet namespace present; runtime flow validation still pending |
| 2026-04-29 | App log preflight (`application/logs/log-2026-04-28.php`) | ⚠️ Found DB connection-refused errors; clean-session runtime verification required |

---

## 0) Environment and Setup

| Check | Status | Evidence/Notes |
|-------|--------|----------------|
| PHP runtime is `C:\laragon\bin\php\php-8.4.20-Win32-vs17-x64\php.exe` | ✅ | `php -v` confirms PHP 8.4.20 (cli), Zend Engine 4.4.20 |
| App is running with latest DB snapshot + representative data | ⚠️ | Not yet verified in this run; requires runtime confirmation on target environment |
| Browser cache is disabled (DevTools open) | ⬜ | |
| Test users ready: Admin, Doctor, Assistant, Patient | ⬜ | |

---

## 1) Security / Session / CSRF

| Scenario | Admin | Doctor | Assistant | Patient | Evidence/Notes |
|----------|-------|--------|-----------|---------|----------------|
| Login with valid credentials works | ⬜ | ⬜ | ⬜ | ⬜ | |
| Invalid login shows error and no crash | ⬜ | ⬜ | ⬜ | ⬜ | |
| Idle 10+ min, then submit form; request handled correctly | ⬜ | ⬜ | ⬜ | ⬜ | |
| Repeated AJAX save/update/delete works without reload | ⬜ | ⬜ | ⬜ | ⬜ | |
| No 403 CSRF errors in DevTools Network | ⬜ | ⬜ | ⬜ | ⬜ | |
| Logout clears session and back button does not restore auth state | ⬜ | ⬜ | ⬜ | ⬜ | |

Pass criteria: all role columns green for all scenarios.

---

## 2) Core Portal Flows

| Flow | Role(s) | Status | Evidence/Notes |
|------|---------|--------|----------------|
| Create appointment | Admin/Assistant | ⬜ | |
| Update appointment status | Admin/Doctor/Assistant | ⬜ | |
| Cancel appointment | Admin/Assistant | ⬜ | |
| Register new patient | Admin/Assistant | ⬜ | |
| Create payment/billing entry | Admin/Accountant | ⬜ | |
| Generate/view prescription | Doctor | ⬜ | |
| Create lab order + update result | Doctor/Lab staff | ⬜ | |
| Pharmacy payment/sale complete flow | Pharmacy/Admin | ⬜ | |

---

## 3) DataTables Verification (Priority)

### Common checks per page

- [ ] Table loads
- [ ] Search works
- [ ] Sort works
- [ ] Pagination works
- [ ] No JS console errors

| Page | Role | Table Loads | Search | Sort | Pagination | No Console Errors | Evidence/Notes |
|------|------|-------------|--------|------|------------|-------------------|----------------|
| Appointment list | Admin/Assistant | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | |
| Doctor list | Admin | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | |
| Patient list | Admin/Assistant | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | |
| Lab list | Admin/Lab staff | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | |
| Pharmacy/payment list | Admin/Pharmacy | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | |

---

## 4) UI / Design System Verification

| Check | Status | Evidence/Notes |
|-------|--------|----------------|
| Status badges render with `.ap-status-*` classes | ⬜ | |
| Legacy semantic badge visuals do not appear (`badge-success/warning/danger/info`) | ⚠️ | Static scan found zero legacy class matches under `application/modules`; visual browser pass pending |
| Cards/layout are consistent in major admin pages | ⬜ | |
| Responsive usability verified at 1366px and 768px widths | ⬜ | |
| No critical inline-style regression on major pages | ⬜ | |

---

## 5) Import (PhpSpreadsheet Migration)

| Scenario | Status | Evidence/Notes |
|----------|--------|----------------|
| Patient import via `.xlsx` succeeds | ⚠️ | Controller-level static migration check done; runtime upload flow pending |
| Doctor import via `.xlsx` succeeds | ⚠️ | Controller-level static migration check done; runtime upload flow pending |
| Medicine import via `.xlsx` succeeds | ⚠️ | Controller-level static migration check done; runtime upload flow pending |
| Empty/malformed sheet fails gracefully with clear message | ⬜ | |
| Imported fields map correctly (no shifted columns) | ⬜ | |

---

## 6) Report / PDF / Export Checks

| Scenario | Status | Evidence/Notes |
|----------|--------|----------------|
| Finance invoice PDF opens and downloads | ⚠️ | Invoice/PDF-related finance views present; browser/runtime validation pending |
| Lab report PDF/print works | ⚠️ | Lab report/export views present; browser/runtime validation pending |
| CSV/export buttons produce valid output | ⚠️ | CSV/export-related module paths detected; output validity pending runtime test |
| No blank PDF or corrupted output | ⬜ | |

---

## 7) Performance and Stability Smoke

| Scenario | Status | Evidence/Notes |
|----------|--------|----------------|
| Dashboard loads without obvious slowdown | ⬜ | |
| Large list pages (100+ rows) remain usable | ⬜ | |
| No repeated network failures in DevTools | ⬜ | |
| No fatal errors in PHP/app logs during session | ⚠️ | Existing log shows DB connection-refused errors on 2026-04-28; rerun on clean session required |

---

## Phase 3 Plan Alignment Matrix (03-00 to 03-06)

Use this matrix to keep audit traceability from QA outcomes to implementation plans.

| Plan | Area | QA Focus | Checklist Sections | Status | Evidence/Notes |
|------|------|----------|--------------------|--------|----------------|
| 03-00 | DesignSystem test baseline | Confirm green baseline remains valid on runtime + representative data | 0, 7 | ✅ | `phpunit --filter DesignSystem` passed (8/8) on PHP 8.4.20 |
| 03-01 | CSRF contract hardening | Session + token refresh + repeated AJAX + no CSRF 403 | 1 | ⚠️ | Static checks found `window.CI_CSRF_HASH` in `csrf_inject.php` and `csrf_regenerate` set true; role/session browser flow pending |
| 03-02 | Status class migration | `.ap-status-*` in UI; no legacy semantic badge behavior | 4 | ⚠️ | Static scan found zero `badge-success/warning/danger/info`; browser render confirmation pending |
| 03-03 | Layout cleanup | Card consistency and no critical style regressions | 4 | ⚠️ | Lint + unit suite green; visual/card consistency browser pass pending |
| 03-04 | DataTable ID migration | Priority DataTables functional checks pass | 3 | ⚠️ | Static scan found zero exact `id="editable-sample"`; live table behavior checks pending |
| 03-05 | DataTables cleanup breadth | No regressions across list pages using new IDs/patterns | 3, 7 | ⚠️ | Static scan clean for legacy exact ID; list-page runtime checks pending |
| 03-06 | Remaining UI sweep | Final visual stability and no hidden regressions | 4, 7 | ⚠️ | Full suite + lint green; runtime visual and log-clean checks pending |

---

## Defect Log (Quick Template)

Copy/paste per issue:

```text
ID: QA-001
Module/Page:
Role:
Steps to Reproduce: 1) ... 2) ... 3) ...
Expected:
Actual:
Severity: Critical / High / Medium / Low
Screenshot/Request URL:
```

---

## Execution Notes

- Status legend: ⬜ not run, ✅ pass, ❌ fail, ⚠️ partial/flaky, N/A not applicable.
- Capture at least one artifact per failed check.
- A module cannot be marked signed-off if any Critical/High defect remains open.

---

## Sign-Off

| Scope | Owner | Result | Date | Notes |
|-------|-------|--------|------|-------|
| Phase 3 QA execution |  | ⬜ |  | |
| Product acceptance |  | ⬜ |  | |
