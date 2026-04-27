# Codebase Concerns

**Analysis Date:** 2026-04-27

---

## Security

| Issue | Severity | Location | Notes |
|-------|----------|----------|-------|
| API token is `rand(1111, 9999)` — only 8889 possible values | CRITICAL | `application/modules/api/controllers/Api.php:44,81` | `authenticateNew()` issues a 4-digit random int as `idToken`. Trivially brute-forceable. Replace with `bin2hex(random_bytes(32))`. |
| `hospital_id` taken from unvalidated `$_GET` in API endpoints | CRITICAL | `application/modules/api/controllers/Api.php:3214,3225,3233,3241,3249,3257` | Six public API actions (`getPatientInfoList`, `getDiagnosisInfo`, etc.) set `$this->hospitalID` directly from `input->get('hospital_id')` with no auth check, enabling cross-tenant data access. |
| Hardcoded password `'12345'` in four controllers | HIGH | `application/modules/frontend/controllers/Frontend.php:1704`, `application/modules/meeting/controllers/Meeting.php:968`, `application/modules/payu/controllers/Payu.php:293`, `application/modules/request/controllers/Request.php:182` | Used as default/fallback credential. Must be removed and replaced with env-driven or generated secrets. |
| Unparameterized `LIKE '%.$var.'%'` across 165 occurrences | HIGH | `application/modules/appointment/models/Appointment_model.php:46,73…`, `application/modules/ambulance/controllers/Ambulance.php:388`, `application/modules/api/models/Api_model.php:899,943` | String interpolation inside `where(…, NULL, FALSE)` bypasses CodeIgniter query-builder escaping. SQL injection vector for all DataTables search inputs. Use `$this->db->like()` instead. |
| Login lockout configured but never enforced | HIGH | `application/config/ion_auth.php:156-157`, `application/modules/auth/controllers/Auth.php` | `maximum_login_attempts=10`, `lockout_time=50s`, but `is_max_login_attempts_exceeded()` / `is_time_locked_out()` are never called in any module controller. Brute-force on login is unrestricted. |
| `debugSession()` public endpoint exposes full session dump | HIGH | `application/modules/ai_image_analysis/controllers/Ai_image_analysis.php:615-640` | Outputs all session keys, hospital_id, and DB record counts to any unauthenticated HTTP request. |
| 18 `echo print_r(…)` / `echo "Database error"` debug outputs in production code | HIGH | `application/modules/ai_image_analysis/controllers/Ai_image_analysis.php:507,625`, `application/modules/ambulance/controllers/Ambulance.php:747-766`, `application/modules/inventory/controllers/Inventory.php:1070-1185` | Raw PHP structure dumps and DB errors rendered to browser. Information disclosure for attackers. |
| `csrf_regenerate = false` — token not rotated per request | MEDIUM | `application/config/config.php:481` | Token is valid for 7200 s and never regenerated. A stolen token is reusable for 2 hours. Set to `true` for state-changing actions. |
| CSRF excluded for entire `api.*` wildcard | MEDIUM | `application/config/config.php:483` | All routes matching `api.*` skip CSRF. Any POST action reachable under the `api` controller is unprotected. Scope exclusions to specific webhook/callback URIs only. |
| Minimum password length is 5 characters | MEDIUM | `application/config/ion_auth.php:147` | `min_password_length = 5` is far below OWASP minimum (8+). Not currently enforced by CI form validation rules either. |
| `init_categories()` endpoint drops and recreates a production table | HIGH | `application/modules/inventory/controllers/Inventory.php:1101-1128` | `DROP TABLE IF EXISTS inventory_categories` executed inline in a public controller action with no auth guard visible at the action level. Data-destruction endpoint reachable by any authenticated user. |
| Timezone value from `POST` written verbatim to `index.php` | HIGH | `application/modules/home/controllers/Home.php:560-574` | `$timezone = $this->input->post('timezone')` is not validated against the allowed timezone list before being injected into `ini_set("date.timezone","<value>")` in `index.php`. An admin could inject arbitrary PHP. |
| Stripe SDK is an unmanaged third-party copy, not via Composer | MEDIUM | `application/third_party/stripe/stripe-php/` (v7.12.0) | Vendored manually, not tracked by `composer.json`. Security patches will be missed. Latest is v16.x. |
| No Content-Security-Policy header | MEDIUM | `application/hooks/security_headers.php` | `X-Frame-Options`, `X-Content-Type-Options`, and HSTS are set, but no CSP header. XSS attack surface is wider than necessary given inline scripts in views. |

---

## Technical Debt

| Issue | Impact | Location | Effort to fix |
|-------|--------|----------|---------------|
| "God controllers" — Finance (4285 lines), Patient (3827), Lab (3450), Api (3545), Inventory (3280), Appointment (3245) | Untestable, high change risk, merge conflicts | `application/modules/finance/controllers/Finance.php`, `patient/`, `lab/`, `api/`, `inventory/`, `appointment/` | High — extract service/repository layer |
| `testpkz` module shipped in production | Dead code, maintenance confusion | `application/modules/testpkz/` | Low — delete directory |
| `home_backup.php` view (1750 lines) left in production | Dead code | `application/modules/home/views/home_backup.php` | Low — delete file |
| PHPExcel library (deprecated 2017) still used for imports | No security patches; PhpSpreadsheet is the maintained fork | `application/modules/import/controllers/Import.php:61,148,230`, `application/libraries/PHPExcel/` | Medium — replace with `phpoffice/phpspreadsheet` |
| `phpoffice/phpword` pinned at `0.18.3` (2021) | Latest is 1.3.x; missing security fixes | `composer.json:21` | Low — `composer update phpoffice/phpword` |
| `die()` left inside `Api::getAppointmentById()` | Terminates response without JSON output | `application/modules/api/controllers/Api.php:904` | Low — remove |
| Commented-out code blocks scattered throughout large controllers | Code clarity, stale logic confusion | `application/modules/auth/controllers/Auth.php:54-67`, `application/modules/api/controllers/Api.php:1215-1293`, `application/modules/meeting/controllers/Meeting.php:247-319,539` | Low — delete dead blocks |
| `error_reporting(0)` silencing errors in multiple controllers | Hides runtime failures; masks bugs | `application/modules/bed/controllers/Bed.php:1804`, `finance/Finance.php:2942,3182`, `patient/Patient.php:3495`, `payroll/Payroll.php:203` | Low — remove; configure env-level error reporting |
| Schema changes done via raw `$this->db->query("CREATE TABLE…")` in controllers instead of migrations | Unrepeatable, order-dependent, not version controlled | `application/modules/inventory/controllers/Inventory.php:1110-1126` | Medium — move to migration files |
| Inline `include("./vendor/autoload.php")` with relative path in API controller | Path-sensitive; breaks if controller invoked from different CWD | `application/modules/api/controllers/Api.php:5` | Low — use `FCPATH . 'vendor/autoload.php'` |
| `updateTimezone()` directly modifies `index.php` via `fopen/rename` | Fragile; race conditions; file-system dependency | `application/modules/home/controllers/Home.php:560-608` | Medium — store timezone in DB only; apply via `date_default_timezone_set()` at boot |
| Audit logging is absent from most modules | No forensic trail for HIPAA/patient-data changes | Only `application/modules/logs/models/Logs_model.php:143` found; not used broadly | High — implement audit middleware or hook |
| OpenAI API key stored in `settings` DB row as `chatgpt_api_key` | Key exposed to any code with settings access; no key rotation mechanism | `application/modules/ai_image_analysis/controllers/Ai_image_analysis.php:99`, `ai_patient_condition.php:122` | Medium — move to env var `OPENAI_API_KEY` |

---

## Performance

| Issue | Severity | Location | Notes |
|-------|----------|----------|-------|
| `$this->db->get('patient')->result()` fetches entire patient table in dashboard loop | HIGH | `application/modules/home/controllers/Home.php:98` | Inside a `foreach` loop over appointments. Full table scan per dashboard load. Add `where_in` or cache patient map. |
| Nested `foreach` loops over payment categories and payments in dashboard | MEDIUM | `application/modules/home/controllers/Home.php:353-356` | Triple-nested loop for finance summary; no pagination or limit. Will degrade at scale. |
| SAAS platform analytics uses two complex raw queries with no caching | MEDIUM | `application/modules/saas_platform/controllers/Saas_platform.php:30,64` | Full-table aggregation on `sms_credit_ledger` and doctor ranking run on every page load. |
| `save_queries = true` in production (DB) | MEDIUM | `application/config/database.php:106` | Query log buffered in memory for every request in non-production. Confirm `ENVIRONMENT=production` is set; the guard is `!== 'production'`. |
| No HTTP-level caching or output caching configured | LOW | `application/config/config.php:332` | `cache_query_string = false`, CI cache disabled. Static assets served without far-future cache headers. |

---

## Outdated Dependencies

| Package | Current | Notes | Risk |
|---------|---------|-------|------|
| `application/third_party/stripe/stripe-php` | 7.12.0 | Not in `composer.json`; manually vendored. Current is 16.x. | HIGH — unpatched CVEs likely |
| `phpoffice/phpword` | 0.18.3 | Latest is 1.3.x (breaking changes exist). | MEDIUM — missing security fixes |
| `application/libraries/PHPExcel/` | Unknown (deprecated 2017) | Replaced by `phpoffice/phpspreadsheet`. No patches ever. | HIGH — abandoned library |
| `twilio/sdk` | 5.42.2 | Latest is 8.x. | LOW — functional but old API surface |
| `omnipay/paypal` | v3.0.2 | Latest is v3.7.x. | LOW |

---

## Architecture Concerns

- **No API authentication middleware.** Most API endpoints set `$this->hospitalID` per-call from session or POST data. There is no token-based auth middleware; the API relies on session state, which is not suitable for mobile/third-party consumers.
- **Multi-tenancy enforced ad-hoc.** `hospital_id` scoping is applied manually inside individual model queries. There is no central tenant-isolation layer. Several API endpoints allow the caller to supply `hospital_id` directly via GET parameter (`Api.php:3214`), bypassing isolation.
- **Mixed concerns in controllers.** Business logic (payment calculations, PDF generation, SMS dispatch) lives directly in controller methods rather than in service or model classes. Finance.php alone is 4285 lines.
- **No service layer.** All cross-module operations (e.g., create patient + create appointment + send SMS) are orchestrated inline in controller actions with no transactional guarantee.
- **Migrations exist but incomplete.** Only 10 migration files cover new modules added in 2026. The bulk of the legacy schema (70+ modules) has no migration history; `init_categories()` in controllers fills the gap destructively.
- **`testpkz` module reachable in production.** The module has auth guards but represents dead/test code that should not be deployed.

---

## Missing Infrastructure

- **No CSP header.** `security_headers.php` omits `Content-Security-Policy`. Inline scripts and external CDN resources in views create XSS risk.
- **No rate limiting at the application layer.** Login attempts are tracked but lockout is not applied in the auth controller. API endpoints have no per-IP or per-user throttle.
- **No centralized audit log.** Patient data changes, prescription writes, and financial transactions are not systematically logged for compliance (HIPAA/local health regulations).
- **No background job queue.** SMS/email sends in cronjobs run synchronously via cURL in `Cronjobs.php`. Failures are silent; no retry mechanism.
- **No automated security scanning in CI.** `composer.json` scripts (`ci`) run unit tests and lint but no `composer audit` or static analysis (Psalm/PHPStan).
- **No database connection pooling or read replicas.** Single `mysqli` connection; no failover configured (`failover = []` in `database.php:105`).
