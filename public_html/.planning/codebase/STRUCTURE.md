# Codebase Structure

**Analysis Date:** 2026-04-27

## Directory Layout

```
public_html/                        — web root (document root)
├── index.php                       — CI3 bootstrap / front controller
├── router.php                      — PHP built-in dev server router
├── server.php                      — alternate dev server entry
├── composer.json / composer.lock   — PHP dependency manifest
├── phpunit.xml.dist                — PHPUnit configuration
│
├── application/                    — all custom application code
│   ├── config/                     — CI3 config files (db, routes, hooks, platform flags)
│   ├── controllers/                — top-level controllers (Health, Migrate, Welcome)
│   ├── core/                       — CI3 core overrides (MY_Router, MY_Loader → HMVC MX)
│   ├── helpers/                    — custom helpers (asset, audit, safe_redirect, etc.)
│   ├── hooks/                      — hook callbacks (required, auto_migrate, security_headers)
│   ├── language/                   — i18n strings per locale (en/, bd/, etc.)
│   ├── libraries/                  — custom + extended libraries (Ion_auth, Email, pdf, Excel, Twilio)
│   ├── migrations/                 — numbered migration files (YYYYMMDDNNNNNN_*.php)
│   ├── models/                     — top-level models (Ion_auth_model, Audit_log_model)
│   ├── modules/                    — HMVC modules (70+ self-contained feature modules)
│   ├── stubs/                      — code generation stubs
│   ├── third_party/                — vendored non-Composer libs (MX HMVC, Stripe SDK)
│   └── views/                      — (empty — all views live inside modules)
│
├── adminlte/                       — AdminLTE 3 theme assets
│   ├── dist/                       — compiled CSS/JS
│   └── plugins/                    — AdminLTE bundled plugins (DataTables, Select2, etc.)
│
├── common/                         — shared frontend assets (CSS, JS, images, toastr)
├── front/                          — public-facing website theme assets
├── front-end/                      — legacy frontend assets (ajax, older theme)
├── new-fnt/                        — newer frontend theme assets
│
├── uploads/                        — user-uploaded files
│   ├── ai_image_analysis/          — medical images uploaded for AI analysis
│   ├── documents/                  — patient/staff documents
│   └── image/                      — profile images, logos
│
├── files/                          — server-side file storage
│   ├── backups/                    — DB/export backups
│   └── downloads/                  — generated files (reports, exports)
│
├── invoicefile/                    — generated invoice PDFs
├── scripts/                        — utility/maintenance scripts
├── tools/                          — developer tooling
│
├── system/                         — CodeIgniter 3 system (framework core — do not edit)
│   ├── core/                       — CI3 core classes
│   ├── database/                   — CI3 database drivers
│   ├── helpers/                    — CI3 built-in helpers
│   └── libraries/                  — CI3 built-in libraries
│
├── vendor/                         — Composer packages (mpdf, guzzlehttp, moneyphp, etc.)
└── tests/                          — PHPUnit test suite
    ├── Unit/                       — unit tests
    └── Support/                    — test support helpers
```

## Key Modules/Packages

| Module | Purpose | Key files |
|--------|---------|-----------|
| `auth` | Login, logout, password reset via IonAuth | `modules/auth/controllers/Auth.php` |
| `home` | Post-login landing, attendance clock-in, layout partials | `modules/home/controllers/Home.php`, `modules/home/views/dashboard.php`, `modules/home/views/footer.php`, `modules/home/views/menu.php` |
| `dashboard` | Role-based dashboards (executive, clinical, financial, operational) | `modules/dashboard/controllers/Dashboard.php`, `modules/dashboard/models/` |
| `patient` | Patient CRUD, records, history | `modules/patient/controllers/Patient.php`, `modules/patient/models/Patient_model.php` |
| `doctor` | Doctor CRUD, profiles, schedules | `modules/doctor/controllers/Doctor.php`, `modules/doctor/models/Doctor_model.php` |
| `appointment` | Booking, calendar, AJAX slot queries | `modules/appointment/controllers/Appointment.php`, `modules/appointment/models/Appointment_model.php` |
| `prescription` | Doctor prescriptions, medicine selection | `modules/prescription/controllers/Prescription.php` |
| `treatment_plan` | Treatment plans (newer module, has SQL migrations) | `modules/treatment_plan/controllers/Treatment_plan.php` |
| `lab` | Laboratory tests, results | `modules/lab/controllers/` |
| `finance` | Billing, invoices, payment records | `modules/finance/controllers/Finance.php` |
| `pharmacy` | Medicine inventory, dispensing | `modules/finance/controllers/Pharmacy.php` |
| `report` | Exportable reports (PDF/Excel) | `modules/report/controllers/Report.php` |
| `settings` | Per-hospital config; `getSettings()` called globally | `modules/settings/controllers/Settings.php`, `modules/settings/models/Settings_model.php` |
| `hospital` | Hospital profile, subscription packages | `modules/hospital/controllers/Hospital.php`, `modules/hospital/controllers/Package.php` |
| `superadmin` | Platform-level admin (manages hospitals) | `modules/superadmin/controllers/Superadmin.php` |
| `saas_platform` | SaaS subscription/tenant management | `modules/saas_platform/controllers/Saas_platform.php` |
| `frontend` | Public marketing site (no auth required) | `modules/frontend/controllers/Frontend.php` |
| `site` | Public CMS-style pages (gallery, slide, services) | `modules/site/controllers/Site.php` |
| `api` | JSON API for mobile/external consumers | `modules/api/controllers/Api.php` |
| `portal` | Patient self-service portal | `modules/portal/controllers/Portal.php` |
| `pgateway` | Payment gateway abstraction layer | `modules/pgateway/controllers/Pgateway.php`, `modules/pgateway/models/Pgateway_model.php` |
| `paypal` | PayPal integration (loaded as sub-module) | `modules/paypal/controllers/Paypal.php` |
| `payment_bd` | Bangladesh payment gateways (bKash, SSLCommerz) | `modules/payment_bd/controllers/Payment_bd.php` |
| `paystack` | Paystack gateway | `modules/paystack/` |
| `payu` | PayU gateway | `modules/payu/` |
| `doctor_chamber` | Private chamber / clinic sessions | `modules/doctor_chamber/controllers/Doctor_chamber.php` |
| `assistant_chamber` | Chamber assistant workflows | `modules/assistant_chamber/` |
| `emergency` | Emergency case management | `modules/emergency/controllers/Emergency.php` |
| `bed` | Bed/ward management | `modules/bed/` |
| `nurse` | Nurse-specific workflows | `modules/nurse/` |
| `receptionist` | Receptionist workflows | `modules/receptionist/` |
| `inventory` | Hospital inventory management | `modules/inventory/` |
| `insurance` | Insurance claims | `modules/insurance/` |
| `leave` | Staff leave management | `modules/leave/` |
| `payroll` | Payroll processing | `modules/payroll/` |
| `attendance` | Staff attendance tracking | `modules/attendance/` |
| `schedule` | Doctor scheduling | `modules/schedule/` |
| `chat` | Internal messaging | `modules/chat/` |
| `notice` | Bulletin/notice board | `modules/notice/` |
| `meeting` | Meeting scheduling | `modules/meeting/` |
| `sms` | SMS dispatch (Twilio + local providers) | `modules/sms/` |
| `email` | Email dispatch wrapper | `modules/email/` |
| `cronjobs` | Scheduled tasks (appointment reminders) | `modules/cronjobs/controllers/Cronjobs.php` |
| `logs` | Audit trail UI, transaction logs | `modules/logs/` |
| `ai_image_analysis` | AI medical image analysis (OpenAI) | `modules/ai_image_analysis/controllers/Ai_image_analysis.php`, `modules/ai_image_analysis/models/Ai_image_analysis_model.php` |
| `ai_patient_overview` | AI-generated patient overview summaries | `modules/ai_patient_overview/controllers/Ai_patient_overview.php` |
| `diagnosis` | Diagnosis records | `modules/diagnosis/` |
| `symptom` | Symptom management | `modules/symptom/` |
| `donor` | Blood/organ donor registry | `modules/donor/` |
| `ambulance` | Ambulance service management | `modules/ambulance/` |
| `department` | Hospital departments | `modules/department/` |
| `facilitie` | Hospital facilities listing | `modules/facilitie/` |
| `service` | Hospital services listing | `modules/service/` |
| `faq` | FAQ management | `modules/faq/` |
| `advice` | Medical advice module | `modules/advice/` |
| `macro` | Text expansion macros | `modules/macro/` |
| `import` | Bulk data import | `modules/import/` |
| `file` | File management | `modules/file/` |
| `profile` | User profile editing | `modules/profile/` |
| `pservice` | Premium services | `modules/pservice/` |
| `featured` | Featured doctors/services (public site) | `modules/featured/` |
| `slide` | Homepage slider (public site) | `modules/slide/` |
| `request` | Service/appointment requests | `modules/request/` |
| `accountant` | Accountant-role views | `modules/accountant/` |
| `laboratorist` | Lab technician views | `modules/laboratorist/` |
| `pharmacist` | Pharmacist views | `modules/pharmacist/` |
| `doctorvisit` | Doctor visit charge records | `modules/doctorvisit/` |
| `patient_chamber` | Patient's chamber-visit history | `modules/patient_chamber/` |
| `systems` | System utilities | `modules/systems/` |

## Entry Points

**Web (default):** `index.php` — CI3 front controller, loads all PHP. Default route resolves to `modules/frontend/controllers/Frontend.php::index()`.

**Web (admin):** `modules/auth/controllers/Auth.php::login()` at `/auth/login`. Post-login: `modules/home/controllers/Home.php::index()` → `modules/dashboard/controllers/Dashboard.php`.

**Dev server:** `router.php` — used with `php -S localhost:8080 router.php`

**CLI health check:** `application/controllers/Health.php` — JSON health endpoint at `/health`

**CLI migrations:** `application/controllers/Migrate.php` — runs pending migrations via URL or CLI

**Cron jobs:** `modules/cronjobs/controllers/Cronjobs.php` — hit via HTTP cron trigger

**API (mobile/external):** `modules/api/controllers/Api.php` — `/api/authenticate`, `/api/...` endpoints returning JSON

## Naming Conventions

**Files:**
- Controllers: `PascalCase.php` matching the class name — e.g. `Appointment.php` → `class Appointment`
- Models: `Snake_case_model.php` — e.g. `Appointment_model.php` → `class Appointment_model`
- Views: `snake_case.php` — e.g. `add_new.php`, `appointment.php`
- Migrations: `YYYYMMDDNNNNNN_description.php` — e.g. `20260401000001_create_ai_image_analyses.php`

**Directories:**
- Module names: `snake_case` — e.g. `ai_image_analysis`, `doctor_chamber`, `treatment_plan`
- Asset directories: lowercase — e.g. `common/css/`, `front/js/`

## Where to Add New Code

**New feature module:**
- Create `application/modules/<module_name>/controllers/<Module_name>.php` extending `MX_Controller`
- Create `application/modules/<module_name>/models/<Module_name>_model.php` extending `CI_Model`
- Create `application/modules/<module_name>/views/<view>.php`
- Add SQL migration to `application/migrations/` if schema changes needed

**New admin page:**
- Controller: `application/modules/<module>/controllers/<Name>.php`
- Standard view pattern: stack `home/dashboard` → content view → `home/footer`
- Add menu link in `application/modules/home/views/menu.php`

**New AJAX/JSON endpoint:**
- Add public method to existing module controller
- Method reads `$this->input->post()` / `$this->input->get()`, calls model, returns `echo json_encode($data)`

**Utilities / shared helpers:**
- Add to `application/helpers/<name>_helper.php`
- Register in `application/config/autoload.php` `$autoload['helper']` if needed globally

**New config flag:**
- Feature flags → `application/config/platform.php`
- Service credentials → `application/config/openai.php` (pattern), loaded per-module with `$this->config->load('openai', true)`

**Database schema:**
- Create `application/migrations/YYYYMMDDNNNNNN_description.php`
- Extend `CI_Migration`, implement `up()` and `down()` methods

## Special Directories

**`application/third_party/MX/`:**
- Purpose: Wiredesignz HMVC extension — `Router.php`, `Loader.php`, `Controller.php`, `Modules.php`
- Generated: No (vendored manually)
- Committed: Yes

**`application/third_party/stripe/`:**
- Purpose: Stripe PHP SDK (bundled, not via Composer)
- Generated: No
- Committed: Yes

**`vendor/`:**
- Purpose: Composer-managed dependencies (mpdf, guzzlehttp, moneyphp, omnipay, PHPUnit)
- Generated: Yes (via `composer install`)
- Committed: No (in .gitignore pattern)

**`uploads/`:**
- Purpose: Runtime user-uploaded files (images, documents, AI analysis files)
- Generated: Yes (runtime)
- Committed: No

**`application/cache/`:**
- Purpose: CI3 view/DB cache
- Generated: Yes (runtime)
- Committed: No

**`application/logs/`:**
- Purpose: CI3 application log files
- Generated: Yes (runtime)
- Committed: No

**`.planning/codebase/`:**
- Purpose: GSD codebase map documents consumed by planning/execution agents
- Generated: Yes (by codebase mapper)
- Committed: Yes

---

*Structure analysis: 2026-04-27*
