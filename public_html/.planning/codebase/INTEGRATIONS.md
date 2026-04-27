# Integrations

**Analysis Date:** 2026-04-27

## External APIs

| Service | Purpose | Auth method | Files |
|---------|---------|-------------|-------|
| OpenAI Chat Completions (`gpt-4o`) | AI patient overview text generation | `OPENAI_API_KEY` env var (retrieved from `settings_model->getSettings()->chatgpt_api_key` in DB) | `application/modules/ai_patient_overview/controllers/Ai_patient_overview.php`, `application/config/openai.php` |
| OpenAI Vision API (`gpt-4o`) | Medical image analysis (X-rays, scans) | Same `chatgpt_api_key` from DB settings | `application/modules/ai_image_analysis/controllers/Ai_image_analysis.php`, `application/modules/ai_image_analysis/controllers/Ai_patient_condition.php` |
| Twilio | SMS notifications | `account_sid` + `auth_token` from `application/config/twilio.php` | `application/libraries/Twilio.php`; Composer: `twilio/sdk` v5.42.2 |
| Zoom | Telemedicine video meetings | `api_key` + `secret_key` stored in DB settings table | `application/modules/api/controllers/Api.php` (lines ~3122, ~3398) |
| PayPal | Online payment (checkout) | Client ID / Secret via Omnipay adapter | `application/modules/paypal/controllers/`, Composer: `omnipay/paypal` v3.0.2 |
| Stripe | Online payment (card) | Secret key from DB payment gateway settings | `application/third_party/stripe/stripe-php/`; used in `application/modules/pgateway/` |
| Paystack | Online payment | Public/Secret key from DB payment gateway settings | `application/modules/paystack/controllers/` |
| PayU Money | Online payment | Merchant key/salt from DB payment gateway settings | `application/modules/payu/controllers/` |
| bKash (Bangladesh) | Mobile banking payment | `BKASH_APP_KEY`, `BKASH_APP_SECRET`, `BKASH_USERNAME`, `BKASH_PASSWORD` env vars | `application/libraries/Bd_payment_bkash.php`; sandbox toggled via `BKASH_SANDBOX` |
| SSLCommerz (Bangladesh) | Online payment gateway | `SSLCOMMERZ_STORE_ID`, `SSLCOMMERZ_STORE_PASSWORD` env vars | `application/libraries/Bd_payment_sslcommerz.php`; sandbox toggled via `SSLCOMMERZ_SANDBOX` |

## Third-party Libraries (non-trivial)

| Library | Purpose | Version |
|---------|---------|---------|
| `twilio/sdk` | SMS sending via Twilio REST API | 5.42.2 |
| `omnipay/paypal` | PayPal payment processing via Omnipay abstraction | 3.0.2 |
| `league/omnipay` | Payment gateway abstraction layer | 3.2.1 |
| `omnipay/common` | Omnipay shared interfaces | ^3.2 |
| `mpdf/mpdf` | PDF generation (invoices, reports) | v8.3.1 |
| `phpoffice/phpword` | Word document generation | 0.18.3 |
| `guzzlehttp/guzzle` | HTTP client (used internally by Omnipay and Twilio SDK) | in lock |
| `php-http/guzzle7-adapter` | PSR-18 adapter for Guzzle 7 | ^1 |
| `setasign/fpdi` | PDF import/manipulation (pulled by mpdf) | in lock |
| `moneyphp/money` | Money/currency handling (Omnipay dependency) | in lock |
| `laminas/laminas-escaper` | HTML escaping (framework dependency) | in lock |
| `mikey179/vfsstream` | Virtual filesystem for PHPUnit tests | ^1.6.11 (dev) |
| `phpunit/phpunit` | Unit testing framework | ^10.5 (dev) |
| WireDesignz MX (HMVC) | Modular Extensions for CodeIgniter 3 | bundled in `application/third_party/MX/` |
| Ion Auth | Authentication library (users, groups, login attempts) | bundled in `application/libraries/Ion_auth.php` |
| PHPExcel (legacy) | Excel export (legacy bundled library) | bundled in `application/libraries/PHPExcel/` |
| Dompdf | Alternative PDF generation (CI PDF library wrapper) | via `vendor/autoload.php`; `application/libraries/pdf.php` |
| AdminLTE 3 | Admin UI template (Bootstrap 4) | bundled in `adminlte/` |
| DataTables | Server-side / client-side table rendering | bundled in `adminlte/plugins/datatables*` |
| Chart.js | Dashboard charts | 3.9.1 (CDN in some views) |
| Select2 | Enhanced select dropdowns | bundled in `adminlte/plugins/select2/` |
| SweetAlert2 | Modal dialogs and confirmations | bundled in `adminlte/plugins/sweetalert2/` |
| Summernote | WYSIWYG rich text editor | bundled in `adminlte/plugins/summernote/` |
| FullCalendar | Appointment/schedule calendar UI | bundled in `adminlte/plugins/fullcalendar/` |
| Dropzone | Drag-and-drop file uploads | bundled in `adminlte/plugins/dropzone/` |

## Internal Services

| Service | Protocol | Purpose |
|---------|----------|---------|
| CodeIgniter REST API module | HTTP (internal CI controller) | Mobile app / external client API; `application/modules/api/controllers/Api.php` |
| Cronjobs module | PHP CLI / cron scheduler | Scheduled tasks (reminders, batch jobs); `application/modules/cronjobs/` |
| SaaS Platform module | HTTP (CI controller) | Multi-hospital/tenant management; `application/modules/saas_platform/` |
| Email (SMTP) | SMTP via CI Email library | Transactional email (appointments, notifications); config stored in DB `email_settings` table, applied in `application/hooks/required.php` |
| SMS module | HTTP (Twilio REST) | SMS notifications; `application/modules/sms/controllers/Sms.php`, model: `application/modules/sms/models/Sms_model.php` |
| Audit Log | DB write (internal) | User action audit trail; `application/helpers/audit_helper.php`; feature-flagged via `platform.php` (`audit_log_enabled`) |
| Payment Gateway manager | HTTP (external APIs) | Unified payment gateway CRUD; `application/modules/pgateway/` selects Stripe / PayPal / Paystack / PayU by gateway name from DB |
| BD Payment gateways | HTTP (cURL) | bKash and SSLCommerz mobile payment callbacks; `application/libraries/Bd_payment_bkash.php`, `application/libraries/Bd_payment_sslcommerz.php` |

## Environment Configuration

**Required env vars (production):**
- `CI_ENV` — environment name (`development` / `production`)
- `CI_BASE_URL` — base URL with trailing slash
- `CI_DB_HOST`, `CI_DB_USER`, `CI_DB_PASSWORD`, `CI_DB_NAME` — MySQL credentials (required outside development)
- `CI_DB_PORT` — MySQL port (default 3306)

**Service-specific env vars:**
- `OPENAI_CHAT_MODEL`, `OPENAI_VISION_MODEL` — OpenAI model overrides (default `gpt-4o`)
- `BKASH_APP_KEY`, `BKASH_APP_SECRET`, `BKASH_USERNAME`, `BKASH_PASSWORD`, `BKASH_SANDBOX`
- `SSLCOMMERZ_STORE_ID`, `SSLCOMMERZ_STORE_PASSWORD`, `SSLCOMMERZ_SANDBOX`

**Secrets location:**
- `.env` file at project root (parent directory of `public_html`) — production secrets
- `public_html/.env` — local development overrides
- Payment gateway credentials (Stripe, PayPal, Paystack, PayU, Twilio, Zoom, OpenAI key) also stored in the database `payment_gateways` / `settings` tables and editable via admin UI
