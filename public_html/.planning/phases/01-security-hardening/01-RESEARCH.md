# Phase 1: Security Hardening — Research

**Researched:** 2026-04-27
**Domain:** PHP 8.1 / CodeIgniter 3 security hardening — CRITICAL and HIGH vulnerability remediation
**Confidence:** HIGH

---

## Summary

Phase 1 patches ten discrete security vulnerabilities across six modules before any portal UI ships. The system holds patient medical data and is multi-tenant (hospital_id scoping). Every vulnerability has a confirmed location in the codebase; nothing here is speculative. The fixes are surgical: no architectural changes are required, and each can be executed as an isolated commit.

The two highest-risk issues are the 4-digit API token (SEC-01, trivially brute-forceable in ~4,500 guesses on average) and the unauthenticated `hospital_id` override in six API endpoints (SEC-02, enables cross-tenant patient data leakage). These must be fixed before any other work.

SEC-05 (165 LIKE injections across 27 files) is the highest-volume task but is mechanically uniform: every instance follows the same pattern and the CI3 Query Builder provides a clean, tested replacement. The bulk strategy is file-by-file with a per-file smoke test.

**Primary recommendation:** Fix SEC-01 and SEC-02 first (single-file, high-impact). Then SEC-04 (remove one method). Then SEC-06/SEC-07/SEC-10 (remove bad code). Then SEC-03 (two-line auth guard). Then SEC-09 (env migration). Then SEC-08 (add CSP header). Then SEC-05 last (bulk, same pattern, many files).

---

## Project Constraints (from CLAUDE.md)

- Controllers extend `MX_Controller` (HMVC) or `CI_Controller`
- Models extend `CI_Model`; use `$this->db->...` Query Builder only — no raw queries
- Use `$this->db->like()` for search — NEVER string interpolation in LIKE clauses (SQL injection)
- CSRF token must be included in all AJAX POST requests
- All new code follows existing module structure: `application/modules/{name}/controllers/`, `models/`, `views/`
- No debug code in production: `echo print_r()`, `die()`, `error_reporting(0)` are banned
- Environment config: credentials go in `.env` or environment variables, never hardcoded
- PHPUnit tests in `tests/Unit/` — run with `composer test`
- Security phases 1-2 must complete before any portal UI ships to production
- Complete CI3 system; no framework switch

---

<phase_requirements>
## Phase Requirements

| ID | Description | Research Support |
|----|-------------|------------------|
| SEC-01 | Replace `rand(1111,9999)` API token with `bin2hex(random_bytes(32))` | Two occurrences in `Api.php` lines 44 and 81 — straightforward one-line change each |
| SEC-02 | Validate `hospital_id` server-side in 6 API endpoints — no GET override | `getHospitalID($ion_id)` already exists; apply it to the 6 endpoints instead of `$this->input->get()` |
| SEC-03 | Enforce Ion Auth login lockout via `is_max_login_attempts_exceeded()` / `is_time_locked_out()` | Both methods exist in `Ion_auth_model.php` and are callable via `$this->ion_auth->` `__call` proxy — add guard in `Auth::login()` before `ion_auth->login()` call |
| SEC-04 | Remove or auth-gate `debugSession()` endpoint in `Ai_image_analysis.php` | Method at line 615; only ENVIRONMENT guard exists — simplest fix is delete the method entirely |
| SEC-05 | Replace 165+ LIKE string interpolation with `$this->db->like()` across 27 files | CI3 `like()`/`or_like()` + `group_start()`/`group_end()` provides exact equivalent; all 27 files follow same pattern |
| SEC-06 | Remove hardcoded `'12345'` fallback passwords from 4 controllers | Located at `Frontend.php:1704`, `Meeting.php:968`, `Payu.php:293`, `Request.php:182` — replace with generated secrets or remove fallback |
| SEC-07 | Remove 18 `echo print_r()` / `echo "Database error"` debug outputs | Located in `Ai_image_analysis.php`, `Ambulance.php`, `Inventory.php` — delete or replace with `log_message()` |
| SEC-08 | Add `Content-Security-Policy` header to `security_headers.php` | File at `application/hooks/security_headers.php` — add one `set_header()` call; policy must accommodate inline scripts in dashboard views and CDN sources |
| SEC-09 | Move OpenAI API key from `settings` DB row to `OPENAI_API_KEY` env var | `.env` loading infrastructure exists (`dotenv_loader.php`); add key to `.env`, read via `getenv()`, null the DB row |
| SEC-10 | Add admin-only auth guard to `Inventory::init_categories()` before DROP TABLE | `Inventory::__construct()` already calls `$this->ion_auth->logged_in()` — add `in_group(['superadmin','admin'])` guard at method entry |
</phase_requirements>

---

## Architectural Responsibility Map

| Capability | Primary Tier | Secondary Tier | Rationale |
|------------|-------------|----------------|-----------|
| API token generation (SEC-01) | API / Backend | — | Token is issued by the API controller during auth; fix is in the controller |
| Tenant isolation enforcement (SEC-02) | API / Backend | — | hospital_id must come from the authenticated session, never from the HTTP request |
| Login lockout enforcement (SEC-03) | API / Backend | — | Auth controller owns login flow; Ion Auth model already tracks attempts |
| Debug endpoint removal (SEC-04) | API / Backend | — | Controller method with no view; remove entirely |
| LIKE SQL injection (SEC-05) | Database / Storage | — | Injections are in model query methods; fix at model layer |
| Hardcoded passwords (SEC-06) | API / Backend | — | Controller-level logic that sets registration credentials |
| Debug output removal (SEC-07) | API / Backend | — | `echo` statements in controller methods |
| CSP header (SEC-08) | Frontend Server (SSR) | CDN / Static | Header emitted by the CI3 hook post-controller; policy must allow local + CDN origins |
| API key env migration (SEC-09) | API / Backend | — | Secret retrieval at controller boot time; move source from DB to env |
| DROP TABLE auth guard (SEC-10) | API / Backend | — | Controller method with destructive DB operation; add group check |

---

## Standard Stack

### Core (already present — no new installs required)

| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| CodeIgniter 3 Query Builder | CI3 bundled | `$this->db->like()`, `group_start()`, `group_end()`, `or_like()` | Native CI3; already available everywhere |
| Ion Auth | 2.5.2 (bundled) | `is_max_login_attempts_exceeded()`, `is_time_locked_out()` via `__call` proxy | Lockout logic already implemented in `Ion_auth_model.php`; just not called |
| PHP `random_bytes()` | PHP 7.0+ native | CSPRNG for token generation | Built-in; no library needed |
| `ci_load_dotenv` | Custom (bundled) | Reads `.env` from project root and `public_html/.env` | Already loading `CI_DB_HOST` etc. — `OPENAI_API_KEY` follows same pattern |

### No new packages needed

All fixes use existing CI3 APIs, existing PHP built-ins, or configuration changes. `composer install` state does not change.

---

## Architecture Patterns

### System Architecture Diagram

```
HTTP Request
     |
     v
CI3 Hooks (post_controller_constructor)
     |--- security_headers.php  ← SEC-08: add CSP header here
     |
     v
Controller (__construct)
     |--- ion_auth->logged_in() check
     |--- ion_auth->in_group() check  ← SEC-10: add to init_categories()
     |--- SEC-03: add is_time_locked_out() before ion_auth->login()
     |
     v
Controller (action method)
     |--- $this->input->get/post()
     |--- SEC-01: replace rand() with bin2hex(random_bytes(32))
     |--- SEC-02: replace $this->input->get('hospital_id') with session-derived value
     |--- SEC-04: delete debugSession() method entirely
     |--- SEC-06: remove '12345' password literals
     |--- SEC-07: remove echo print_r() / echo "Database error" lines
     |--- SEC-09: replace $settings->chatgpt_api_key with getenv('OPENAI_API_KEY')
     |
     v
Model (query methods)
     |--- SEC-05: replace ->where("col LIKE '%$var%'", NULL, FALSE)
     |            with ->group_start()->like('col', $var)->or_like(...)->group_end()
     |
     v
MySQL via CI3 Query Builder
```

### Recommended Project Structure

No structural changes needed. All fixes are in-place edits to existing files.

```
application/
├── hooks/
│   └── security_headers.php          ← SEC-08: add CSP header
├── modules/
│   ├── api/
│   │   └── controllers/Api.php       ← SEC-01, SEC-02
│   ├── auth/
│   │   └── controllers/Auth.php      ← SEC-03
│   ├── ai_image_analysis/
│   │   └── controllers/Ai_image_analysis.php  ← SEC-04, SEC-07
│   ├── ambulance/
│   │   └── controllers/Ambulance.php ← SEC-05 (controller), SEC-07
│   ├── appointment/
│   │   └── models/Appointment_model.php  ← SEC-05 (24 occurrences)
│   ├── inventory/
│   │   └── controllers/Inventory.php ← SEC-07, SEC-10
│   ├── frontend/
│   │   └── controllers/Frontend.php  ← SEC-06
│   ├── meeting/
│   │   └── controllers/Meeting.php   ← SEC-06
│   ├── payu/
│   │   └── controllers/Payu.php      ← SEC-06
│   └── request/
│       └── controllers/Request.php   ← SEC-06
├── config/
│   └── openai.php                    ← SEC-09: already has env pattern
└── (project root)/.env               ← SEC-09: add OPENAI_API_KEY here
```

---

## SEC-01: API Token — Replace `rand(1111,9999)`

### What Goes Wrong

`rand(1111, 9999)` produces only 8,889 possible values. An attacker who intercepts one valid token can brute-force the next one in milliseconds with no account lockout.

### Fix Pattern

```php
// BEFORE (Api.php lines 44 and 81):
$data['idToken'] = rand(1111, 9999);

// AFTER:
$data['idToken'] = bin2hex(random_bytes(32));
// Produces 64-character hex string; 2^256 search space
```

**Applies to:** `Api::authenticateNew()` (line 44) and `Api::authenticate()` (line 81). Both are identical one-line changes.

**Note:** If the mobile app that consumes `idToken` has a length assumption (e.g., `int` type), it must accept a `string`. Verify API consumer before deploying. [ASSUMED — mobile app token type unknown]

---

## SEC-02: hospital_id from Unvalidated GET

### What Goes Wrong

Six API endpoints (`getPatientInfoList`, `getDiagnosisInfo`, `getTreatmentInfo`, `getSymptomInfo`, `getAdviceInfo`, `getTestInfo`) each do:

```php
$this->hospitalID = $this->input->get('hospital_id');
```

Any authenticated API caller can supply any `hospital_id` in the query string and read another tenant's patient list, diagnoses, symptoms, etc.

### Fix Pattern

The authenticated hospital_id derivation already exists as `getHospitalID($ion_id)`. The session already holds the ion_user_id after login.

```php
// BEFORE (applied to all 6 methods, e.g. Api::getPatientInfoList()):
$this->hospitalID = $this->input->get('hospital_id');

// AFTER:
$ion_id = $this->ion_auth->get_user_id();
if (!$ion_id) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthenticated']);
    return;
}
$this->hospitalID = $this->getHospitalID($ion_id);
```

**Applies to lines:** 3214, 3225, 3233, 3241, 3249, 3257 in `Api.php`.

**Behavioral note:** These endpoints previously served any caller who knew a `hospital_id`. After fix, callers must be logged in — the authenticated `hospital_id` is always used. If these endpoints are called by the mobile app with a bearer token but no CI3 session, `get_user_id()` will return null. [ASSUMED — mobile app session strategy unknown; verify before deploy]

---

## SEC-03: Ion Auth Login Lockout Never Enforced

### What Goes Wrong

`ion_auth.php` config sets `maximum_login_attempts = 10` and `lockout_time = 50s`. `Ion_auth_model::login()` tracks attempts internally and enforces `is_time_locked_out()` — but the `Auth::login()` controller proceeds to call `$this->ion_auth->login()` without first checking. Since `ion_auth_model::login()` does check internally (line 936), failed attempts ARE counted. The lockout DOES fire during `ion_auth->login()` calls.

**The real gap:** The `Auth::login()` controller does not show the lockout message to the user before calling the model — but the underlying Ion Auth model enforces it. The practical risk is that the error message is not shown before the attempt is processed.

**However:** The API endpoints `Api::authenticateNew()` and `Api::authenticate()` call `$this->ion_auth->login()` without any lockout pre-check, and since Ion Auth model enforces on the call, those are covered.

The meaningful fix is: **display the lockout state to the user before submitting** in `Auth::login()`, so brute-force tools get a fast rejection without creating a DB attempt record:

```php
// In Auth::login(), BEFORE calling $this->ion_auth->login():
$identity_value = $this->input->post('identity', true);

// Check if already locked out (fast path — no DB write)
if ($this->ion_auth->is_time_locked_out($identity_value)) {
    $this->session->set_flashdata('message', $this->ion_auth->errors());
    redirect('auth/login', 'refresh');
    return;
}
```

**Ion Auth method availability:** `Ion_auth` library uses `__call` to proxy to `Ion_auth_model`. Both `is_max_login_attempts_exceeded($identity)` and `is_time_locked_out($identity)` exist in `Ion_auth_model` (verified lines 990 and 1028). They are callable as `$this->ion_auth->is_time_locked_out($identity)`.

**Also note:** `lockout_time = 50` seconds is below OWASP minimum of 60 seconds. Fix config to 300 (5 min) while here. [ASSUMED — no external compliance requirement defined, but OWASP recommends >= 60s]

---

## SEC-04: Unauthenticated `debugSession()` Endpoint

### What Goes Wrong

`Ai_image_analysis::debugSession()` (lines 615–642) outputs:
- Full CI3 session dump (`all_userdata()`)
- `hospital_id`
- Ion Auth login status
- DB record counts

The only guard is `if (defined('ENVIRONMENT') && ENVIRONMENT === 'production') { show_404(); }`. If `ENVIRONMENT` is not defined or is anything other than `'production'`, this is reachable by anyone. Since `ENVIRONMENT` defaults to `'production'` in `index.php` this is partially mitigated but not safe — any misconfiguration exposes it.

### Fix Pattern

**Recommended: Delete the method.** It has no production purpose.

```php
// DELETE the entire debugSession() method (lines 615-642)
// No replacement needed.
```

If debugging is needed in future: add `$this->ion_auth->logged_in()` + `$this->ion_auth->in_group(['superadmin'])` guard and use `log_message()` to a file rather than HTTP output.

**Also note:** The unnamed method at lines ~496–612 (the one that outputs `"Database connected<br>"`, session dump, and raw query results) follows the same pattern and should also be deleted.

---

## SEC-05: LIKE SQL Injection — Bulk Fix (165 occurrences, 27 files)

### What Goes Wrong

The pattern:
```php
->where("(col1 LIKE '%" . $search . "%' OR col2 LIKE '%" . $search . "%')", NULL, FALSE)
```

The `NULL, FALSE` third argument to `where()` disables CI3's escaping. This makes `$search` a direct SQL injection vector. DataTables search inputs flow directly into this.

### CI3 `like()` API

[VERIFIED: CI3 system/database/DB_query_builder.php lines 866–907]

```php
// Single column:
$this->db->like('colname', $value);         // col LIKE '%value%'
$this->db->like('colname', $value, 'both'); // both = default
$this->db->like('colname', $value, 'before'); // LIKE '%value'
$this->db->like('colname', $value, 'after');  // LIKE 'value%'

// Multiple columns with OR (group wrapping preserves parentheses):
$this->db->group_start();
$this->db->like('col1', $value);
$this->db->or_like('col2', $value);
$this->db->or_like('col3', $value);
$this->db->group_end();
// Produces: AND (col1 LIKE '%value%' OR col2 LIKE '%value%' OR col3 LIKE '%value%')

// Array form (AND between each, NOT suitable for multi-column OR):
$this->db->like(['col1' => $value, 'col2' => $value]); // AND between — wrong for this use case
```

**The correct replacement pattern for multi-column OR LIKE:**

```php
// BEFORE:
->where("(id LIKE '%" . $search . "%' OR patientname LIKE '%" . $search . "%' OR doctorname LIKE '%" . $search . "%')", NULL, FALSE)

// AFTER:
->group_start()
->like('id', $search)
->or_like('patientname', $search)
->or_like('doctorname', $search)
->group_end()
```

CI3's `like()` calls `escape_like_str()` internally (verified in `_like()` method), so special regex characters (`%`, `_`, `\`) in `$search` are properly escaped.

### Files and Occurrences

[VERIFIED: grep audit 2026-04-27]

| File | Approx Count | Search Columns |
|------|-------------|----------------|
| `appointment/models/Appointment_model.php` | 24 | id, patientname, doctorname |
| `patient/models/Patient_model.php` | high | id, name, phone, etc. |
| `finance/models/Finance_model.php` | high | various |
| `lab/models/Lab_model.php` | medium | various |
| `api/models/Api_model.php` | 2 | id+name; id+category+name+etc. |
| `ambulance/controllers/Ambulance.php` | 1 | booking_number, patient_name |
| `bed/models/Bed_model.php` | 3+ | id, bed_id, description |
| `doctor/models/Doctor_model.php` | medium | various |
| `finance/models/Pharmacy_model.php` | medium | various |
| `frontend/models/Site_model.php` | medium | various |
| `insurance/models/Insurance_model.php` | medium | various |
| `inventory/models/Inventory_model.php` | medium | various |
| `inventory/models/Purchase_model.php` | medium | various |
| `inventory/models/Supplier_model.php` | medium | various |
| `lab/models/Lab_model_bk.php` | medium | various |
| `leave/models/Leave_model.php` | medium | various |
| `logs/models/Logs_model.php` | medium | various |
| `macro/models/Macro_model.php` | medium | various |
| `medicine/models/Medicine_model.php` | medium | various |
| `meeting/models/Meeting_model.php` | medium | various |
| `nurse/models/Nurse_model.php` | medium | various |
| `prescription/models/Prescription_model.php` | medium | various |
| `pservice/models/Pservice_model.php` | medium | various |
| `sms/models/Sms_model.php` | medium | various |
| `systems/models/Systems_model.php` | medium | various |
| `email/models/Email_model.php` | medium | various |
| `testpkz/models/Testpkz_model.php` | low | (dead code — delete in Phase 2) |

**Execution strategy:** Process file-by-file. For each file: read, replace each `->where("... LIKE '%.$var.'%' ...", NULL, FALSE)` block with the `group_start()/like()/or_like()/group_end()` pattern. Run `php -l <file>` after each file to catch syntax errors.

**Edge case — `Ambulance.php` is a controller, not a model.** The same fix applies; location doesn't matter.

**Edge case — multi-column expressions with different column sets.** Each occurrence may search different columns. Read each location individually rather than doing a global search-replace. The column names must be preserved.

---

## SEC-06: Hardcoded `'12345'` Fallback Passwords

### Locations and Context

[VERIFIED: source inspection]

| File | Line | Context |
|------|------|---------|
| `Frontend.php` | 1704 | `$password = '12345';` — used as default password when creating a new hospital account via `ion_auth->register()` |
| `Request.php` | 182 | `$password = '12345';` — same: `ion_auth->register($username, $password, $email, ...)` |
| `Meeting.php` | 968 | `$meeting_password = '12345';` — default meeting password when creating a meeting with no password provided |
| `Payu.php` | 293 | `$password = '12345';` — used in `base64_encode($password)` when constructing PayU product_info string for non-backend flow |

### Fix Strategies by Context

**Frontend.php and Request.php (hospital registration passwords):**

These set the initial login password for a new hospital. `'12345'` is weak but is the first-use credential. Replace with a generated temporary password:

```php
// Replace:
$password = '12345';

// With:
$password = bin2hex(random_bytes(8)); // 16-char random hex; send to admin email
```

The generated password should be emailed to the new hospital admin. Check whether `ion_auth->register()` flow sends a registration email — if yes, pass the generated password into the email template. [ASSUMED — email flow not fully traced; verify that password reaches the admin]

**Meeting.php (meeting default password):**

`'12345'` is a meeting room default password — not an auth credential. Replace with a generated value:

```php
$meeting_password = bin2hex(random_bytes(4)); // 8-char random hex, easy to share
```

**Payu.php (product_info string):**

`$password` here is `base64_encode`d into a PayU product info string — it is not an auth credential; it is metadata passed to the payment gateway. The value `'12345'` is arbitrary placeholder. Replace with an empty string or a deterministic value that doesn't look like a credential:

```php
// In Payu::check4() when $from != 'backend':
$password = ''; // PayU product_info metadata — no credential meaning
```

---

## SEC-07: Debug Output Removal (18 occurrences)

### Locations

[VERIFIED: source inspection]

| File | Lines | Pattern |
|------|-------|---------|
| `ai_image_analysis/controllers/Ai_image_analysis.php` | ~496–612 | Full debug method: `echo "Database connected"`, `echo "Session data: " . print_r(...)`, etc. |
| `ai_image_analysis/controllers/Ai_image_analysis.php` | 625 | Inside `debugSession()`: `echo "Session data:<br>"; ... print_r($this->session->all_userdata())` |
| `ambulance/controllers/Ambulance.php` | 740–766 | `debugRates()` method: `echo print_r($tables)`, `echo print_r($fields)`, `echo print_r($rates)`, `echo print_r($this->session->userdata())` |
| `inventory/controllers/Inventory.php` | 1070–1185 | `testCategory()`, `debug_categories()`, `init_categories()` methods: `echo print_r(...)`, `echo "Database error: ..."` |

### Fix Pattern

Delete the entire debug method bodies. Replace any information that was genuinely needed (e.g., "table created successfully") with `log_message('debug', '...')`.

```php
// REMOVE: echo print_r($anything, true);
// REMOVE: echo "Database error: ...";
// REMOVE: echo "Session data: ...";

// If a status message is needed for admin, use:
log_message('info', 'inventory_categories table initialized');
// And return a proper JSON response or redirect.
```

The `debugRates()`, `debug_categories()`, `testCategory()` methods in `Ambulance.php` and `Inventory.php` serve no production purpose and should be deleted in their entirety.

---

## SEC-08: Content-Security-Policy Header

### Current State

`application/hooks/security_headers.php` already sets `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`, and conditional `HSTS`. No CSP is set.

### CSP Complexity for This Application

[ASSUMED — full CSP audit not performed; policy below is based on observed script/style origins in dashboard.php and footer.php]

The admin dashboard loads from these external origins:
- `https://fonts.googleapis.com` — CSS
- `https://fonts.gstatic.com` — font files
- `https://code.ionicframework.com` — CSS (ionicons)
- `https://cdnjs.cloudflare.com` — axios.min.js

The application also uses **inline scripts** extensively:
- `<script>window.CI_BASE_URL = ...</script>` (dashboard.php line 21)
- `<script>var langdate = ...</script>` (footer.php line 70)
- `<script>var time_format = ...</script>` (footer.php line 81)
- `<script>$.widget.bridge(...);</script>` (footer.php line 109)
- Multiple other inline `<script>` blocks throughout views

A strict `script-src 'self'` without `'unsafe-inline'` would break all these. The options are:

**Option A — `'unsafe-inline'` (weakest but workable for Phase 1):**
Allows all inline scripts. Provides XSS protection only for external script injection, not inline. Still blocks many XSS vectors (data: URIs, etc.). Recommended for Phase 1 given volume of inline scripts.

**Option B — nonce-based CSP (safest but requires architectural change):**
CI3 hook generates a nonce, all inline `<script>` tags must receive `nonce="$nonce"`. Requires modifying dashboard.php, footer.php, and all module views with inline scripts. High volume — not Phase 1 scope.

**Phase 1 recommended CSP (Option A — immediate improvement, no view changes):**

```php
// In security_headers.php, add after existing headers:
$CI->output->set_header(
    "Content-Security-Policy: " .
    "default-src 'self'; " .
    "script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; " .
    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://code.ionicframework.com; " .
    "font-src 'self' https://fonts.gstatic.com https://code.ionicframework.com data:; " .
    "img-src 'self' data: blob:; " .
    "connect-src 'self'; " .
    "frame-ancestors 'self'; " .
    "object-src 'none'; " .
    "base-uri 'self';"
);
```

**Important:** Test in staging before production deploy. A wrong CSP silently breaks pages. The `frame-ancestors 'self'` directive supersedes `X-Frame-Options: SAMEORIGIN` for modern browsers.

**What this does NOT cover:** Inline event handlers (`onclick=`, etc.) are covered by `'unsafe-inline'`. If a future phase removes all inline scripts, upgrade to nonce-based CSP.

---

## SEC-09: OpenAI API Key — Env Migration

### Current State

```php
// Ai_image_analysis.php line 99:
$api_key = $this->settings_model->getSettings()->chatgpt_api_key;
```

Key is stored as a column value in the `settings` DB table, accessible to any code that loads `settings_model`.

### Migration Pattern

**Step 1:** Add key to `.env` (project root, not in git):

```dotenv
OPENAI_API_KEY=sk-...your-key-here...
```

The `ci_load_dotenv()` in `dotenv_loader.php` already loads this file at boot. `getenv('OPENAI_API_KEY')` will work immediately.

**Step 2:** Create/update `application/config/openai.php` (file already exists):

```php
// openai.php already has:
$config['openai_chat_model'] = getenv('OPENAI_CHAT_MODEL') ?: 'gpt-4o';
$config['openai_vision_model'] = getenv('OPENAI_VISION_MODEL') ?: 'gpt-4o';

// Add:
$config['openai_api_key'] = getenv('OPENAI_API_KEY') ?: '';
```

**Step 3:** Update all consumers to read from config:

```php
// BEFORE (Ai_image_analysis.php):
$api_key = $this->settings_model->getSettings()->chatgpt_api_key;

// AFTER:
$this->config->load('openai');
$api_key = $this->config->item('openai_api_key');
if (empty($api_key)) {
    echo json_encode(['success' => false, 'message' => 'AI API key not configured.']);
    return;
}
```

**Step 4:** Null the DB row (do NOT delete the column — schema change is Phase 2 scope):

```sql
UPDATE settings SET chatgpt_api_key = NULL WHERE chatgpt_api_key IS NOT NULL;
```

Run as a one-shot migration command. Document that `chatgpt_api_key` column is deprecated.

**Step 5:** Check for other consumers:

```
ai_patient_condition.php line 122 also reads chatgpt_api_key — apply same fix there.
```

**WARNING for production:** The `.env` file at project root (parent of `public_html`) must exist and contain `OPENAI_API_KEY` before deploying. If missing, AI features silently fail. Add to deployment checklist.

---

## SEC-10: `init_categories()` DROP TABLE Without Auth Guard

### What Goes Wrong

`Inventory::init_categories()` executes `DROP TABLE IF EXISTS inventory_categories` unconditionally. The constructor only checks `$this->ion_auth->logged_in()` — any logged-in user (nurse, receptionist, patient role) can hit this URL and wipe the table.

### Fix Pattern

Add a role check at the start of the method:

```php
public function init_categories()
{
    // Require superadmin or admin — no other role may truncate production tables
    if (!$this->ion_auth->in_group(['superadmin', 'admin'])) {
        show_error('Forbidden', 403);
        return;
    }

    // Existing DROP TABLE / CREATE TABLE code follows...
}
```

**Additional recommendation:** This method is infrastructure setup code — it should not exist in a production controller at all. After guarding it for Phase 1, flag it for removal or migration to a CI3 migration file in Phase 2. The `debug_categories()` and `testCategory()` methods in the same file should also be deleted (covered by SEC-07).

---

## Don't Hand-Roll

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| CSPRNG token | Custom entropy mixing | `bin2hex(random_bytes(32))` | PHP CSPRNG uses OS entropy pool; hand-rolled RNG has known statistical biases |
| SQL LIKE escaping | Manual `str_replace('%', '\\%', $v)` | `$this->db->like()` | CI3 handles `%`, `_`, `\` escaping and driver-specific escape sequences via `escape_like_str()` |
| Login lockout | Custom attempt counting table | `$this->ion_auth->is_time_locked_out()` | Already implemented in `Ion_auth_model`; re-implementing risks off-by-one or time comparison bugs |
| CSP nonces | Custom PHP nonce injection | Standard `Content-Security-Policy` header approach | Nonce injection is correct but high-scope for Phase 1; `'unsafe-inline'` is an acceptable Phase 1 step |
| Env var loading | Custom `file_get_contents('.env')` | `getenv()` after `ci_load_dotenv()` | Already loaded at boot; use the existing mechanism |

---

## Common Pitfalls

### Pitfall 1: Multi-column OR LIKE — Wrong Array Form

**What goes wrong:** Using `$this->db->like(['col1' => $v, 'col2' => $v])` — the array form joins conditions with AND, not OR. This changes query semantics silently (search only returns rows matching all columns simultaneously).

**How to avoid:** Always use `group_start()` + `like()` + `or_like()` + `group_end()` chain for OR conditions. Never use array form for multi-column search.

**Warning sign:** Fewer results returned after fix for multi-word searches.

### Pitfall 2: Ion Auth `__call` Proxy — Method Must Exist in Model

**What goes wrong:** Calling `$this->ion_auth->is_time_locked_out()` when `Ion_auth_model` does not define it throws `Undefined method Ion_auth::is_time_locked_out() called`. This project's `Ion_auth_model.php` DOES define both methods (verified lines 990, 1028) — but if the library is ever updated, verify they still exist.

**How to avoid:** Grep `Ion_auth_model.php` to confirm method name before calling. The `__call` proxy throws a catchable Exception on miss.

### Pitfall 3: CSP Breaks Ajax / DataTables

**What goes wrong:** `connect-src 'self'` blocks AJAX to any non-same-origin endpoint. If any AJAX call goes to an external URL or a subdomain, it will be blocked silently.

**How to avoid:** Check for any external AJAX calls in `common/js/common-scripts.js` and module-specific JS before deploying CSP. The current `connect-src 'self'` may need `https://cdnjs.cloudflare.com` added if Axios calls go through there.

**Warning sign:** Browser console shows `Content Security Policy: The page's settings blocked the loading of a resource at ...`.

### Pitfall 4: SEC-09 Env Key Not Set in Production

**What goes wrong:** `getenv('OPENAI_API_KEY')` returns `false` (falsy) when the `.env` file is not deployed or the key is missing. `config/openai.php` would set `$config['openai_api_key'] = ''`. AI features silently fail — no crash, just empty API key errors.

**How to avoid:** Add a deploy checklist item. Add a startup validation: after the migration, the existing check `if (empty($api_key)) { return error; }` still catches this correctly.

### Pitfall 5: SEC-06 Password Replacement — Hospital Registration Flow Breaks

**What goes wrong:** Replacing `'12345'` in `Frontend.php:addNewhospital()` and `Request.php` with `bin2hex(random_bytes(8))` but not passing the generated password to the admin notification email means the new hospital admin cannot log in.

**How to avoid:** Trace the email flow for new hospital registration before replacing. If no email is sent, either (a) implement an email-based first-login flow, or (b) have the superadmin copy the generated password from the response UI. [ASSUMED — email flow for new hospital registration not fully traced]

### Pitfall 6: `debugSession()` ENVIRONMENT Guard is Insufficient

**What goes wrong:** The existing guard `if (defined('ENVIRONMENT') && ENVIRONMENT === 'production')` only hides the output when both conditions are met. If `ENVIRONMENT` is `'development'` on a staging server that faces the internet, the endpoint is fully exposed.

**How to avoid:** Delete the method. Do not rely on environment guards for security-sensitive endpoints.

### Pitfall 7: SEC-05 `testpkz` Model

**What goes wrong:** `testpkz/models/Testpkz_model.php` contains the same LIKE pattern. Fixing it is wasted effort because the whole `testpkz` module is dead code scheduled for deletion in Phase 2.

**How to avoid:** Skip `testpkz` files in SEC-05. They will be deleted in Phase 2 (BUG-02).

---

## Code Examples

### SEC-01: Token Generation

```php
// Source: PHP manual — random_bytes() [VERIFIED: PHP 7.0+ built-in]
$data['idToken'] = bin2hex(random_bytes(32));
// Produces 64-char hex string, e.g. "a3f2b1c9..."
```

### SEC-02: Session-Derived Hospital ID

```php
// Source: existing pattern used at Api.php lines 321, 332, 344 [VERIFIED: codebase]
$ion_id = $this->ion_auth->get_user_id();
if (!$ion_id) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthenticated']);
    return;
}
$this->hospitalID = $this->getHospitalID($ion_id);
```

### SEC-03: Lockout Pre-Check

```php
// Source: Ion_auth_model.php line 1028 [VERIFIED: codebase]
// Place immediately before the existing $this->ion_auth->login() call in Auth::login():
$identity_value = $this->input->post('identity', true);
if ($this->ion_auth->is_time_locked_out($identity_value)) {
    $this->session->set_flashdata('message', $this->ion_auth->errors());
    redirect('auth/login', 'refresh');
    return;
}
```

### SEC-05: Multi-Column OR LIKE

```php
// Source: CI3 DB_query_builder.php lines 866-1075 [VERIFIED: codebase]

// BEFORE — SQL injection vector:
->where("(id LIKE '%" . $search . "%' OR patientname LIKE '%" . $search . "%' OR doctorname LIKE '%" . $search . "%')", NULL, FALSE)

// AFTER — properly escaped, semantically equivalent:
->group_start()
->like('id', $search)
->or_like('patientname', $search)
->or_like('doctorname', $search)
->group_end()

// For Appointment_model: drop-in replacement, chain position unchanged.
// Full method example:
$query = $this->db->select('*')
    ->from('appointment')
    ->where('hospital_id', $this->session->userdata('hospital_id'))
    ->group_start()
    ->like('id', $search)
    ->or_like('patientname', $search)
    ->or_like('doctorname', $search)
    ->group_end()
    ->get();
```

### SEC-08: CSP Header in Hook

```php
// Source: CI3 Output class set_header() [VERIFIED: CI3 docs pattern]
// application/hooks/security_headers.php — append after existing headers:

$CI->output->set_header(
    "Content-Security-Policy: " .
    "default-src 'self'; " .
    "script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; " .
    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://code.ionicframework.com; " .
    "font-src 'self' https://fonts.gstatic.com https://code.ionicframework.com data:; " .
    "img-src 'self' data: blob:; " .
    "connect-src 'self'; " .
    "frame-ancestors 'self'; " .
    "object-src 'none'; " .
    "base-uri 'self';"
);
```

### SEC-09: Config-Based Key Read

```php
// application/config/openai.php — add:
$config['openai_api_key'] = getenv('OPENAI_API_KEY') ?: '';

// In Ai_image_analysis.php — replace DB read:
$this->config->load('openai');
$api_key = $this->config->item('openai_api_key');
```

### SEC-10: Auth Guard for Destructive Method

```php
// application/modules/inventory/controllers/Inventory.php
public function init_categories()
{
    if (!$this->ion_auth->in_group(['superadmin', 'admin'])) {
        show_error('You do not have permission to perform this action.', 403);
        return;
    }
    // ... existing code
}
```

---

## Validation Architecture

### Test Framework

| Property | Value |
|----------|-------|
| Framework | PHPUnit ^10.5 |
| Config file | `phpunit.xml.dist` |
| Quick run command | `composer test` |
| Full suite command | `composer test` |

### Phase Requirements → Test Map

| Req ID | Behavior | Test Type | Automated Command | File Exists? |
|--------|----------|-----------|-------------------|-------------|
| SEC-01 | `bin2hex(random_bytes(32))` produces 64-char hex, not integer | unit | `composer test -- --filter SecurityTokenTest` | ❌ Wave 0 |
| SEC-02 | hospital_id from session matches authenticated user; GET param ignored | unit | `composer test -- --filter ApiHospitalIsolationTest` | ❌ Wave 0 |
| SEC-03 | `is_time_locked_out()` called before `ion_auth->login()` | unit | `composer test -- --filter LoginLockoutTest` | ❌ Wave 0 |
| SEC-04 | `debugSession()` method no longer exists in class | unit | `composer test -- --filter DebugEndpointRemovedTest` | ❌ Wave 0 |
| SEC-05 | No `NULL, FALSE` LIKE pattern remains in any module file | static/smoke | `composer test -- --filter LikeInjectionRegressionTest` | ❌ Wave 0 |
| SEC-06 | String `'12345'` does not appear as password literal in any controller | static/smoke | `composer test -- --filter HardcodedPasswordTest` | ❌ Wave 0 |
| SEC-07 | No `echo print_r` or `echo "Database error"` in production controllers | static/smoke | `composer test -- --filter DebugOutputRemovedTest` | ❌ Wave 0 |
| SEC-08 | CSP header present in `security_headers.php` | unit | `composer test -- --filter CspHeaderTest` | ❌ Wave 0 |
| SEC-09 | `chatgpt_api_key` not read from settings; `getenv('OPENAI_API_KEY')` used | unit | `composer test -- --filter OpenAiKeySourceTest` | ❌ Wave 0 |
| SEC-10 | `init_categories()` has group check before DROP TABLE | unit | `composer test -- --filter InitCategoriesAuthTest` | ❌ Wave 0 |

**Note:** Most practical tests for this phase are static analysis / grep-based: confirm dangerous patterns no longer exist in source. These can be implemented as PHP file-read assertions in PHPUnit (same pattern as `PhpSyntaxSmokeTest.php`). Full functional tests require CI3 bootstrap (integration tests) — add only syntax/static checks in Wave 0.

### Sampling Rate

- **Per task commit:** `composer test` (syntax smoke tests pass)
- **Per wave merge:** `composer test` (full suite green)
- **Phase gate:** Full suite green before `/gsd-verify-work`

### Wave 0 Gaps

- [ ] `tests/Unit/SecurityPatchTest.php` — static grep assertions for SEC-01 through SEC-10 (verifies dangerous patterns removed from source files)
- [ ] `tests/Unit/CspHeaderTest.php` — verifies CSP string present in `security_headers.php`

---

## Security Domain

### Applicable ASVS Categories

| ASVS Category | Applies | Standard Control |
|---------------|---------|-----------------|
| V2 Authentication | yes | Ion Auth login lockout (SEC-03) |
| V3 Session Management | yes | hospital_id from session, not GET (SEC-02) |
| V4 Access Control | yes | init_categories admin guard (SEC-10); debugSession removal (SEC-04) |
| V5 Input Validation | yes | `$this->db->like()` for all search inputs (SEC-05) |
| V6 Cryptography | yes | `bin2hex(random_bytes(32))` for tokens (SEC-01) |

### Known Threat Patterns for CI3 + MySQL Stack

| Pattern | STRIDE | Standard Mitigation |
|---------|--------|---------------------|
| SQL injection via LIKE string interpolation | Tampering | `$this->db->like()` with default escaping |
| API token brute force | Spoofing | CSPRNG token (`random_bytes(32)`) |
| Cross-tenant data access via GET param | Information Disclosure | Session-derived `hospital_id` only |
| Unauthenticated debug endpoint | Information Disclosure | Delete endpoint |
| Hardcoded default credentials | Elevation of Privilege | Generated passwords; force-change on first login |
| Debug output in production responses | Information Disclosure | Remove all `echo print_r()` calls |
| Missing CSP | Tampering (XSS amplification) | CSP header with `'unsafe-inline'` baseline |
| Secrets in database | Information Disclosure | Move to `.env` / `getenv()` |
| Unauthenticated destructive endpoint | Tampering | Role-based auth guard |

---

## Environment Availability

No new external dependencies. All fixes use:
- PHP 8.1 built-ins (`random_bytes`, `bin2hex`, `getenv`) — available
- CI3 Query Builder (already loaded) — available
- Ion Auth library (already loaded) — available
- `.env` file read by `ci_load_dotenv` at boot — available

| Dependency | Required By | Available | Version | Fallback |
|------------|------------|-----------|---------|----------|
| PHP `random_bytes()` | SEC-01 | ✓ | PHP 8.1 (built-in since 7.0) | — |
| CI3 `$this->db->like()` | SEC-05 | ✓ | CI3 bundled | — |
| Ion Auth `is_time_locked_out()` | SEC-03 | ✓ | 2.5.2 bundled | — |
| `.env` + `getenv()` | SEC-09 | ✓ | Custom loader in place | — |

---

## Runtime State Inventory

> SEC-09 involves migrating a live configuration value from the database.

| Category | Items Found | Action Required |
|----------|-------------|------------------|
| Stored data | `settings.chatgpt_api_key` column in MySQL — at least one row per hospital contains the OpenAI key | Code edit (read from env) + data migration (NULL the DB column value after adding to .env) |
| Live service config | No external services have hardcoded credentials registered outside of code | None |
| OS-registered state | No OS-level registration involved | None — verified by architecture review |
| Secrets/env vars | `OPENAI_API_KEY` — must be added to `.env` before deploying SEC-09 fix | Add to `.env`; document in deployment checklist |
| Build artifacts | No compiled artifacts or installed packages reference the chatgpt_api_key | None |

**Nothing found in categories: Live service config, OS-registered state, Build artifacts.**

---

## Assumptions Log

| # | Claim | Section | Risk if Wrong |
|---|-------|---------|---------------|
| A1 | Mobile app API consumer accepts `idToken` as string (64-char hex), not integer | SEC-01 | Mobile app crashes or token validation fails if it expects an int |
| A2 | The 6 API endpoints (SEC-02) are consumed only by logged-in CI3 sessions or mobile apps with valid Ion Auth sessions | SEC-02 | If consumed by third-party API clients without CI3 sessions, `get_user_id()` returns null and all calls fail with 401 |
| A3 | `lockout_time = 50s` is a typo/oversight and should be raised to at least 300s | SEC-03 | If 50s is intentional (very short lockout by design), changing it may lock out legitimate users in high-traffic scenarios |
| A4 | New hospital registration email (Frontend.php / Request.php) sends the generated password to the admin | SEC-06 | If no email is sent, new hospital admins have no way to receive their login credentials |
| A5 | No external AJAX calls exist that would be blocked by `connect-src 'self'` | SEC-08 | CSP breaks AJAX functionality if any call targets a different origin |
| A6 | `ai_patient_condition.php` also reads `chatgpt_api_key` — it was identified in CONCERNS.md but the fix scope should include it | SEC-09 | If not fixed, one consumer still reads from DB after the other is fixed — inconsistent state |

---

## Open Questions

1. **Mobile app token type assumption (SEC-01)**
   - What we know: `idToken` is consumed by `Api::authenticate()` and `Api::authenticateNew()` callers
   - What's unclear: Whether the consuming mobile app stores/validates `idToken` as a 4-digit integer or accepts a string
   - Recommendation: Search for mobile app code or API documentation before deploying; if no app exists yet, proceed with the fix and document the new format

2. **SEC-02 API endpoint callers — session vs stateless**
   - What we know: The 6 endpoints do not have explicit auth middleware; they rely on CI3 session
   - What's unclear: Whether any caller is a stateless API client (mobile app, curl script) that would not have a CI3 session
   - Recommendation: Check if these endpoints are called with an `idToken` header that could be validated; if so, add token validation before `get_user_id()`

3. **SEC-06 Meeting password purpose**
   - What we know: `meeting_password = '12345'` is stored as a meeting room credential
   - What's unclear: Whether the meeting platform validates this password externally (e.g., Zoom/Jitsi integration) or if it is only stored in DB
   - Recommendation: Read `Meeting_model::insertMeeting()` to confirm where `meeting_password` is stored and if it is ever sent to an external service

---

## Sources

### Primary (HIGH confidence)
- `application/libraries/Ion_auth.php` + `application/models/Ion_auth_model.php` — lockout methods (is_time_locked_out, is_max_login_attempts_exceeded), __call proxy behavior
- `system/database/DB_query_builder.php` — like(), or_like(), group_start(), group_end() method signatures and escaping behavior
- `application/hooks/security_headers.php` — confirmed: CSP absent, other headers present
- `application/modules/api/controllers/Api.php` — confirmed: rand(1111,9999) at lines 44, 81; GET hospital_id at lines 3214,3225,3233,3241,3249,3257
- `application/modules/auth/controllers/Auth.php` — confirmed: no lockout pre-check before ion_auth->login()
- `application/modules/ai_image_analysis/controllers/Ai_image_analysis.php` — confirmed: debugSession() at 615, settings->chatgpt_api_key at 99
- `application/modules/inventory/controllers/Inventory.php` — confirmed: init_categories() DROP TABLE at 1107, debug output at 1070-1185
- `application/modules/ambulance/controllers/Ambulance.php` — confirmed: debug output at 740-766
- `application/config/dotenv_loader.php` — confirmed: existing env loading infrastructure
- `application/config/openai.php` — confirmed: existing pattern for env-driven config
- grep audit of 27 files — confirmed: 165 occurrences of `NULL, FALSE` LIKE pattern

### Secondary (MEDIUM confidence)
- PHP documentation — `random_bytes()`, `bin2hex()` behavior [ASSUMED from training, consistent with PHP 8.1 behavior]
- OWASP ASVS v4.0 — lockout time recommendations (>= 60s for SEC-03 config fix)

---

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH — all fixes use verified, existing CI3 and PHP built-ins
- Architecture: HIGH — fix locations confirmed by source inspection
- Pitfalls: HIGH — verified against actual code patterns found in source
- SEC-08 CSP policy: MEDIUM — external origins derived from dashboard.php/footer.php audit; may miss CDN origins in module-specific views

**Research date:** 2026-04-27
**Valid until:** 2026-07-27 (stable CI3 framework; no external library changes expected)
