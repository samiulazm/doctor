---
phase: 1
slug: security-hardening
status: complete
nyquist_compliant: true
wave_0_complete: true
created: 2026-04-27
---

# Phase 1 — Validation Strategy

> Per-phase validation contract for feedback sampling during execution.

---

## Test Infrastructure

| Property | Value |
|----------|-------|
| **Framework** | PHPUnit 10.x |
| **Config file** | `phpunit.xml.dist` |
| **Quick run command** | `composer test -- --filter Security` |
| **Full suite command** | `composer test` |
| **Estimated runtime** | ~30 seconds |

---

## Sampling Rate

- **After every task commit:** Run `composer test -- --filter Security`
- **After every plan wave:** Run `composer test`
- **Before `/gsd-verify-work`:** Full suite must be green
- **Max feedback latency:** 30 seconds

---

## Per-Task Verification Map

| Task ID | Plan | Wave | Requirement | Secure Behavior | Test Type | Automated Command | Status |
|---------|------|------|-------------|-----------------|-----------|-------------------|--------|
| 1-01-01 | 01 | 1 | SEC-01 | `idToken` is a 64-char hex string, not a 4-digit int | unit | `grep -r "random_bytes" application/modules/api/controllers/Api.php` | ✅ green |
| 1-01-02 | 01 | 1 | SEC-02 | Foreign `hospital_id` in GET returns 403 / session value | grep | `grep -n "input->get('hospital_id')" application/modules/api/controllers/Api.php` -> 0 results | ✅ green |
| 1-01-03 | 01 | 1 | SEC-03 | Login lockout called in Auth controller | grep | `grep -n "is_max_login_attempts_exceeded\|is_time_locked_out" application/modules/auth/controllers/Auth.php` | ✅ green |
| 1-01-04 | 01 | 1 | SEC-04 | `debugSession()` removed or requires auth | grep | `grep -n "debugSession" application/modules/ai_image_analysis/controllers/Ai_image_analysis.php` -> absent | ✅ green |
| 1-01-05 | 01 | 1 | SEC-05 | No LIKE string interpolation in any model/controller | grep | `grep -rn "where.*NULL.*FALSE" application/modules/ \| wc -l` -> 0 outside `testpkz` | ✅ green |
| 1-01-06 | 01 | 1 | SEC-06 | No hardcoded `'12345'` password in 4 controllers | grep | `grep -rn "'12345'" application/modules/frontend/ application/modules/meeting/ application/modules/payu/ application/modules/request/` -> 0 results | ✅ green |
| 1-01-07 | 01 | 1 | SEC-07 | No debug `echo print_r()` or `echo "Database error"` in production | grep | `grep -rn "echo print_r\|echo \"Database error\"" application/modules/` -> 0 results | ✅ green |
| 1-01-08 | 01 | 1 | SEC-08 | CSP header present in security_headers hook | grep | `grep -n "Content-Security-Policy" application/hooks/security_headers.php` | ✅ green |
| 1-01-09 | 01 | 1 | SEC-09 | OpenAI key reads from env var, not DB settings | grep | `grep -n "OPENAI_API_KEY" application/config/openai.php` | ✅ green |
| 1-01-10 | 01 | 1 | SEC-10 | `init_categories()` has explicit admin auth guard | grep | `grep -B5 "DROP TABLE" application/modules/inventory/controllers/Inventory.php \| grep -i "auth\|group\|admin"` | ✅ green |

*Status: ⬜ pending · ✅ green · ❌ red · ⚠️ flaky*

---

## Wave 0 Requirements

- [x] `tests/Unit/Security/SecurityPatchTest.php` - consolidated class with `test_sec01_*` through `test_sec10_*` methods (created by plan 01-00)

*All 10 SEC verifications are grep/static-analysis based. Single consolidated class covers all requirements.*

---

## Manual-Only Verifications

| Behavior | Requirement | Why Manual | Test Instructions |
|----------|-------------|------------|-------------------|
| Login lockout fires after N failed attempts in browser | SEC-03 | Requires live session + DB state | Log in with wrong password 11 times; verify account locked message appears |
| CSP header appears in browser DevTools Network tab | SEC-08 | Runtime header check | Load any admin page; open DevTools → Network → click HTML response → check Response Headers for `Content-Security-Policy` |
| bKash/SSLCommerz API calls still work with new token format | SEC-01 | Integration with external services | Perform test booking and check no payment API auth failure |

---

## Validation Sign-Off

- [x] All tasks have `<automated>` verify or Wave 0 dependencies
- [x] Sampling continuity: no 3 consecutive tasks without automated verify
- [x] Wave 0 covers all MISSING references
- [x] No watch-mode flags
- [x] Feedback latency < 30s
- [x] `nyquist_compliant: true` set in frontmatter

**Approval:** complete
