# Tech Stack

**Analysis Date:** 2026-04-27

## Runtime & Language

- **Language:** PHP >= 8.1 (production default; `index.php` sets timezone `Asia/Dhaka`)
- **Runtime:** Apache (`.htaccess` mod_rewrite) or Nginx (LiteSpeed-compatible; `nginx-laragon-snippet.conf` provided)
- **Default environment:** `production` via `ENVIRONMENT` constant; overridden by `CI_ENV` env var or `.env`
- **PHP extensions required:** `mysqli` or `pdo_mysql` (auto-detected in `application/config/database_bootstrap.php`)

## Framework

- **Backend:** CodeIgniter 3 (CI3) — `codeigniter/framework` (MIT)
  - Entry point: `index.php`
  - Framework core: `system/`
  - Application: `application/`
- **HMVC extension:** WireDesignz MX (`application/third_party/MX/`) — all feature modules extend `MX_Controller` / `MX_Model`
- **Modules:** 70+ feature modules under `application/modules/` (e.g. `appointment`, `doctor`, `patient`, `finance`, `ai_image_analysis`, `ai_patient_overview`, `saas_platform`, etc.)

## Frontend

- **Admin UI framework:** AdminLTE 3 (`adminlte/`) — Bootstrap 4-based
- **CSS:** Bootstrap 4 (via AdminLTE `adminlte/plugins/bootstrap/`); custom per-module CSS in views
- **JS libraries (bundled in `adminlte/plugins/`):**
  - jQuery
  - DataTables (+ BS4 skin, buttons, responsive, autofill, fixed headers/columns, row group, row reorder, scroller, search builder, search panes, select)
  - Chart.js 3.9.1 (CDN reference in `application/modules/dashboard/views/clinical.php`)
  - FullCalendar
  - Select2 + Select2-Bootstrap4 theme
  - SweetAlert2
  - Toastr
  - Summernote (rich text editor)
  - Moment.js
  - Tempusdominus Bootstrap 4 (date/time picker)
  - Daterangepicker
  - Dropzone
  - Flatpickr / InputMask
  - pdfmake
  - JSZip
  - Bootstrap Switch / Colorpicker / Slider
  - iCheck Bootstrap
  - Flag Icon CSS
  - FontAwesome Free
  - jQuery Validation
  - Pace Progress
  - Overlay Scrollbars
  - Ekko Lightbox
- **Frontend portal assets:** `front/assets/`, `front-end/assets/`, `common/assets/` (shared CSS/JS for public-facing pages)

## Backend

- **Server routing:** CodeIgniter MVC via `index.php` front controller; URL rewrite removes `index.php` from URLs
- **ORM / DB layer:** CodeIgniter Query Builder (Active Record) — `$this->db->...` pattern throughout all models; no external ORM
- **Session storage:** CI native sessions (database, files, redis, or memcached — driver configurable via `config.php`); auto-loaded via `$autoload['libraries'] = ['database', 'email', 'session']` in `application/config/autoload.php`
- **Auth:** Ion Auth library (`application/libraries/Ion_auth.php`, config: `application/config/ion_auth.php`)
  - Hash method: bcrypt (cost 10 normal / 12 admin)
  - Identity field: `email`
  - Login attempt tracking: enabled (max 10 attempts, 50s lockout)
  - Tables: `users`, `groups`, `users_groups`, `login_attempts`

## Database

- **Primary DB:** MySQL / MariaDB via `mysqli` driver (fallback PDO MySQL)
  - Default dev DB name: `democa_hmz_v2` (localhost, root, no password)
  - Production credentials: env vars `CI_DB_HOST`, `CI_DB_USER`, `CI_DB_PASSWORD`, `CI_DB_NAME`, `CI_DB_PORT` (default 3306)
  - Config: `application/config/database.php` + `application/config/database_bootstrap.php`
- **Migrations:** CI3 timestamp-based migrations (`application/migrations/`); enabled in `application/config/migration.php`
  - 11 migrations present (20260401000001 through 20260421000010)
  - Migration table: default CI migrations table
- **Caching:** No dedicated cache layer detected; CI session driver supports files/database/redis/memcached but no explicit cache config enabled

## Build & Tooling

- **Package manager:** Composer (`composer.json` / `composer.lock`)
  - `composer serve` → PHP built-in server on `localhost:8080` via `router.php`
  - `composer test` → PHPUnit
  - `composer lint-php` → custom PHP lint script (`scripts/lint-php.php`)
  - `composer ci` → validate + test + lint
- **Test runner:** PHPUnit ^10.5 (`phpunit.xml.dist`); test suites under `tests/Unit/`
- **Dev tools:** Laragon (local dev; nginx config snippet provided); `.user.ini` for PHP ini overrides (max_input_vars)
- **No frontend build pipeline detected** (no webpack, vite, npm — AdminLTE assets served as static files from `adminlte/`)

## Deployment

- **Hosting:** Apache/LiteSpeed shared hosting or Nginx (Laragon for local dev)
  - `.htaccess` handles mod_rewrite for CI front controller
  - LiteSpeed compatible — `php_value` directives removed, limits via `.user.ini`
- **Environment config:** `.env` file(s) loaded by custom `ci_load_dotenv()` in `application/config/dotenv_loader.php`
  - Loads from project root (parent of `public_html`) first, then `public_html/.env` (overrides)
  - Key env vars: `CI_ENV`, `CI_BASE_URL`, `CI_BASE_URL_STRICT`, `CI_DB_HOST`, `CI_DB_USER`, `CI_DB_PASSWORD`, `CI_DB_NAME`, `CI_DB_PORT`
- **Platform release tracking:** `application/config/platform.php` — current release `0.2.0`; feature flags `audit_log_enabled`, `audit_ui_enabled`
- **Timezone:** `Asia/Dhaka` (set in `index.php`)
- **Multi-language:** 30+ language packs under `application/language/`
