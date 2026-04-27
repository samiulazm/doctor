---
phase: 3
slug: admin-design-system
status: draft
nyquist_compliant: true
wave_0_complete: false
created: 2026-04-28
---

# Phase 3 — Validation Strategy

> Per-phase validation contract for feedback sampling during execution.

---

## Test Infrastructure

| Property | Value |
|----------|-------|
| **Framework** | PHPUnit 10.x + grep/static analysis |
| **Config file** | `phpunit.xml.dist` |
| **Quick run command** | `composer test -- --filter DesignSystem` |
| **Full suite command** | `composer test` |
| **Estimated runtime** | ~30 seconds |

---

## Sampling Rate

- **After every task commit:** Run `composer test -- --filter DesignSystem`
- **After every plan wave:** Run `composer test`
- **Before `/gsd-verify-work`:** Full suite must be green
- **Max feedback latency:** 30 seconds

---

## Per-Task Verification Map

| Task ID | Plan | Wave | Requirement | Expected Behavior | Test Type | Automated Command | Status |
|---------|------|------|-------------|-------------------|-----------|-------------------|--------|
| 3-00-01 | 00 | 0 | All UI-0x | DesignSystemTest.php scaffold exists | unit | `composer test -- --filter DesignSystem` | ⬜ pending |
| 3-01-01 | 01 | 1 | UI-03 | `csrf_regenerate = true` in config.php | grep | `grep -c "csrf_regenerate.*=.*true" application/config/config.php` → 1 | ⬜ pending |
| 3-01-02 | 01 | 1 | UI-03 | `window.CI_CSRF_HASH` exposed in csrf_inject.php | grep | `grep -c "CI_CSRF_HASH" application/views/csrf_inject.php` → ≥1 | ⬜ pending |
| 3-01-03 | 01 | 1 | UI-03 | Manual CSRF tokens removed from AJAX calls | grep | `grep -rn "security->get_csrf_hash\|security->get_csrf_token_name" application/modules/ --include="*.php" \| grep -v "csrf_inject" \| wc -l` → 0 | ⬜ pending |
| 3-02-01 | 02 | 2 | UI-04 | `app-design-tokens.css` contains all status tag classes | grep | `grep -c "ap-status-success\|ap-status-warning\|ap-status-danger\|ap-status-info" application/assets/css/app-design-tokens.css` → ≥4 | ⬜ pending |
| 3-02-02 | 02 | 2 | UI-04 | Legacy `badge-success/warning/danger/info` replaced with `.ap-status-*` | grep | `grep -rn "badge-success\|badge-warning\|badge-danger\|badge-info" application/modules/ \| wc -l` → 0 | ⬜ pending |
| 3-03-01 | 03 | 2 | UI-01 | High-traffic modules use standard AdminLTE 3 card layout | grep | `grep -c "class=\"card\"" application/modules/appointment/views/appointment.php` → ≥1 | ⬜ pending |
| 3-04-01 | 04 | 3 | UI-02 | All list views use `id="dt-{module}"` DataTable init | grep | `grep -rn "id=\"editable-sample\"" application/modules/ \| wc -l` → 0 | ⬜ pending |
| 3-04-02 | 04 | 3 | UI-02 | DataTables init uses server-side standard pattern | grep | `grep -rn "serverSide.*true\|processing.*true" application/modules/ --include="*.php" \| wc -l` → ≥25 | ⬜ pending |

*Status: ⬜ pending · ✅ green · ❌ red · ⚠️ flaky*

---

## Wave 0 Requirements

- [ ] `tests/Unit/DesignSystem/DesignSystemTest.php` — consolidated class with `test_ui01_*` through `test_ui04_*` methods (created by plan 03-00)

---

## Manual-Only Verifications

| Behavior | Requirement | Why Manual | Test Instructions |
|----------|-------------|------------|-------------------|
| CSRF token refreshes after form submit without page reload | UI-03 | Requires browser + live session | Submit a form via AJAX; re-submit immediately; confirm second request succeeds (token was refreshed in response) |
| DataTables search works live on high-traffic module | UI-02 | Requires live DB with real data | Load appointment list; type in search box; verify results filter within 1s |
| Status tags render correct colors in browser | UI-04 | Visual — requires browser render | Load any list view; verify waiting=yellow, done=green, emergency=red, primary action=teal |
| Page header partial renders in lab module | UI-01 | HMVC smoke test | Load `/lab` admin page; verify page title and breadcrumb render correctly |

---

## Validation Sign-Off

- [x] All tasks have `<automated>` verify or Wave 0 dependencies
- [x] Sampling continuity: no 3 consecutive tasks without automated verify
- [x] Wave 0 covers all MISSING references
- [x] No watch-mode flags
- [x] Feedback latency < 30s
- [x] `nyquist_compliant: true` set in frontmatter

**Approval:** pending
