# Architecture

<!-- refreshed: 2026-04-27 -->
**Analysis Date:** 2026-04-27

## System Overview

```text
┌─────────────────────────────────────────────────────────────────────────┐
│                          Web Browser / Mobile App                        │
└─────────────┬───────────────────────┬──────────────────────────────────┘
              │ HTTP request           │ API / JSON request
              ▼                        ▼
┌─────────────────────┐   ┌───────────────────────┐
│  Public Frontend     │   │   REST-ish API         │
│  `modules/frontend` │   │  `modules/api`         │
│  `modules/site`     │   │  echoes json_encode()  │
└────────┬────────────┘   └──────────┬────────────┘
         │ Authenticated              │
         ▼                            │
┌──────────────────────────────────────────────────────────────────────────┐
│                         Admin Portal (HMVC modules)                       │
│  `modules/dashboard`  `modules/appointment`  `modules/patient`           │
│  `modules/doctor`     `modules/finance`       `modules/lab`              │
│  `modules/pharmacy`   `modules/prescription`  `modules/treatment_plan`   │
│  `modules/report`     `modules/nurse`         `modules/receptionist`     │
│  … 60+ more modules                                                       │
└──────────────────────────────┬───────────────────────────────────────────┘
                               │
                               ▼
┌──────────────────────────────────────────────────────────────────────────┐
│                        Shared Infrastructure                              │
│  ion_auth library (`application/libraries/Ion_auth.php`)                 │
│  Settings model (`modules/settings/models/Settings_model.php`)           │
│  CI Query Builder → MySQLi (`application/config/database.php`)           │
└──────────────────────────────────────────────────────────────────────────┘
```

## Pattern

**Style:** Hierarchical MVC (HMVC) monolith — CodeIgniter 3 + Wiredesignz MX extension

**Entry point:** `index.php` at web root

**Request flow:**
1. Web server routes all traffic to `index.php`
2. `index.php` loads `.env` via `application/config/dotenv_loader.php`, then boots CodeIgniter
3. `MY_Router` (extends `MX_Router`) resolves URI segments to `module/controller/method`
4. Hooks fire: `pre_system` → `pre_controller` → `post_controller_constructor`
5. Resolved `MX_Controller` subclass handles the request
6. Controller queries one or more models, assembles `$data[]` array
7. Controller stacks views: `home/dashboard` → content view → `home/footer`
8. CI Output class sends rendered HTML (or `json_encode()` for AJAX/API endpoints)

## Layers

| Layer | Responsibility | Key files |
|-------|---------------|-----------|
| Bootstrap | CI init, env loading, constants | `index.php`, `application/config/dotenv_loader.php`, `application/config/constants.php` |
| Routing | URI → module/controller/method mapping | `application/config/routes.php`, `application/core/MY_Router.php`, `application/third_party/MX/Router.php` |
| Hooks | Cross-cutting: DB listen check, auto-migrate, security headers, required/settings injection | `application/hooks/` (4 files) |
| Controllers | Handle HTTP, enforce auth, orchestrate models → views | `application/modules/*/controllers/*.php` |
| Models | All DB access via CI Query Builder (`$this->db->`) | `application/modules/*/models/*.php` |
| Views | PHP HTML templates; layout assembled by stacking multiple view files | `application/modules/*/views/*.php` |
| Libraries | Auth (`Ion_auth`), Email, PDF, Excel, SMS (Twilio), Payment (Stripe/Omnipay) | `application/libraries/` |
| Helpers | Autoloaded utilities: url, file, form, asset, audit, safe_redirect, chamber_practice | `application/helpers/` |
| Config | Per-deployment settings (DB, ion_auth, platform flags, openai) | `application/config/` |

## Data Flow

### Standard Admin Page Request

1. Browser sends `GET /appointment` — routed to `Appointment::index()` (`modules/appointment/controllers/Appointment.php`)
2. Constructor checks `ion_auth->in_group()` → redirects to `home/permission` if unauthorized
3. Constructor loads models: `appointment_model`, `doctor_model`, `patient_model`, `finance_model`, etc.
4. `index()` calls model methods e.g. `$this->appointment_model->getAppointments()` → CI Query Builder → MySQLi → result set
5. Data array assembled: `$data['settings']`, `$data['patients']`, `$data['doctors']`, etc.
6. Views stacked: `$this->load->view('home/dashboard', $data)` → `$this->load->view('appointment', $data)` → `$this->load->view('home/footer')`
7. Combined HTML flushed to browser

### AJAX / DataTables JSON Request

1. Browser sends `POST /appointment/getAppointmentByJason` (typical pattern)
2. Controller method reads `$this->input->post()`, queries model
3. Returns `echo json_encode($result)` — no view loading

### Authentication Flow

1. `GET /auth/login` → `Auth::login()` (`modules/auth/controllers/Auth.php`)
2. Credentials validated by `ion_auth->login()` (library: `application/libraries/Ion_auth.php`)
3. On success: `hospital_id` set in session via `$this->session->set_userdata('hospital_id', ...)`
4. Redirect to `home` → `Home::index()` → `dashboard`

### Cron / Background Jobs

1. CLI or web request to `/cronjobs/appointmentRemainder`
2. `Cronjobs::appointmentRemainder()` (`modules/cronjobs/controllers/Cronjobs.php`) queries DB, sends email/SMS
3. No session or view; pure model + library calls

**State Management:** Session-based. `hospital_id` stored in CI session (`$this->session->userdata('hospital_id')`) and read by every settings/model query to scope data per hospital.

## Key Design Decisions

- **HMVC via Wiredesignz MX:** `application/third_party/MX/` extends CI3 to allow self-contained modules, each with own `controllers/`, `models/`, `views/`. `MY_Router` and `MY_Loader` in `application/core/` delegate to MX classes.
- **IonAuth for all authentication:** Single library handles login, groups (superadmin, admin, doctor, nurse, receptionist, patient, accountant), and session. Every protected controller calls `$this->ion_auth->in_group()` or `logged_in()` in `__construct()`.
- **Shared layout via stacked views:** All admin pages load `home/dashboard` (header + sidebar) and `home/footer` as wrappers. Content view is sandwiched between them. Layout files live in `modules/home/views/`.
- **Settings model as global config:** `Settings_model::getSettings()` (`modules/settings/models/Settings_model.php`) is called by almost every controller. It scopes all hospital configuration by `hospital_id` from session.
- **Multi-hospital SaaS scoping:** Every DB query in models uses `hospital_id` (session value) to partition data. Superadmin has `hospital_id = 'superadmin'`.
- **Hook-driven cross-cutting concerns:** `pre_system` db_listen_check, `pre_controller` required (settings injection, user context), `post_controller_constructor` auto_migrate + security_headers.
- **Platform feature flags:** `application/config/platform.php` provides `audit_log_enabled`, `audit_ui_enabled`, and `platform_release` — toggled per deployment without code changes.
- **Auto-migrations on boot:** `auto_migrate` hook runs pending migrations on every request (development convenience; `application/hooks/auto_migrate.php`).
- **Composer + vendor for heavy libs:** PDF (`mpdf`), HTTP (`guzzlehttp`), money (`moneyphp`), Omnipay (payment abstraction) loaded via `vendor/autoload.php`. Stripe SDK bundled in `application/third_party/stripe/`.

## Architectural Constraints

- **Threading:** Single-threaded PHP-per-request. No worker threads, no message queue.
- **Global state:** CI super-object (`get_instance()`) is a singleton. `hospital_id` lives in CI session which is request-scoped.
- **Circular imports:** Not observed; HMVC module isolation prevents most circular dependencies.
- **Database:** Single MySQLi connection per request, configured in `application/config/database.php`. No connection pooling.
- **No namespace separation:** All module controllers are in global PHP namespace (class `Appointment`, `Doctor`, etc.); naming conflicts are avoided only by convention.

## Anti-Patterns

### Direct `$this->db` Calls Inside Controllers

**What happens:** Some controllers (e.g. `Auth.php`, `Api.php`) call `$this->db->get_where(...)` directly instead of delegating to a model method.
**Why it's wrong:** Bypasses the model layer, scatters query logic, and makes the controller harder to test.
**Do this instead:** Add a method to the relevant model (e.g. `modules/auth/models/` or `modules/api/models/`) and call it from the controller.

### Model Class Extends `CI_model` (Lowercase)

**What happens:** `Settings_model extends CI_model` — CI3's model base class is actually `CI_Model` (capital M). This works due to PHP case-insensitivity on most platforms but is inconsistent.
**Why it's wrong:** Breaks on case-sensitive filesystems; inconsistent with CI3 convention.
**Do this instead:** Extend `CI_Model` (capital M) uniformly across all model files.

## Error Handling

**Strategy:** Mixed — CI3 native `show_error()`, exception try/catch in newer modules, and flash data messages for user-facing errors.

**Patterns:**
- Auth errors: `$this->session->set_flashdata('message', ...)` → redirect → display in view
- Model errors: DB errors surface via CI3's built-in error display (when `db_debug = TRUE`)
- AI/new modules (`ai_image_analysis`, `ai_patient_overview`): wrap `index()` in `try/catch(Exception $e)` → `show_error()` + `log_message('error', ...)`
- Missing required DB rows: `show_error(...)` with HTTP 503 (e.g. `Frontend::index()`)

## Cross-Cutting Concerns

**Logging:** `log_message('debug'|'error'|'info', ...)` — CI3 native file logger writing to `application/logs/`. Audit log table via `audit_helper.php` (when `audit_log_enabled` is true in `platform.php`).

**Validation:** `form_validation` library loaded per-controller. Rules set via `set_rules()`. `xss_clean` rule applied on text inputs.

**Authentication:** IonAuth library (`application/libraries/Ion_auth.php`). Group membership checked in every module constructor. Session key `hospital_id` used for multi-tenant data scoping.

---

*Architecture analysis: 2026-04-27*
