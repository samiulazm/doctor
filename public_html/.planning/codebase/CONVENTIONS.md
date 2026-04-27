# Coding Conventions

**Analysis Date:** 2026-04-27

## Naming

**Files:**
- Controllers: PascalCase matching class name — `Appointment.php`, `Patient.php`, `Ai_image_analysis.php`
- Models: PascalCase with `_model` suffix — `Appointment_model.php`, `Patient_model.php`, `Settings_model.php`
- Views: snake_case descriptive noun — `appointment.php`, `add_new.php`, `all_prescription.php`
- Helpers: snake_case with `_helper` suffix — `asset_helper.php`, `toastr_helper.php`, `health_helper.php`
- Language files: snake_case with `_lang.php` suffix — `auth_lang.php`, `calendar_lang.php`
- Config files: snake_case noun — `openai.php`, `database.php`, `platform.php`

**Classes:**
- Controllers: PascalCase, no suffix — `class Appointment extends MX_Controller`
- Models: PascalCase with `_model` suffix — `class Appointment_model extends CI_model`
- Multi-word module controllers use underscore separator — `class Ai_image_analysis extends MX_Controller`
- Tests (PHPUnit): PascalCase with `Test` suffix, marked `final` — `final class HtmlEscapeHelperTest extends TestCase`

**Functions/Methods:**
- Controller action methods: camelCase — `addNewView()`, `uploadImage()`, `addPrescriptionView()`
- Model CRUD methods: camelCase with a noun prefix — `insertAppointment()`, `getAppointment()`, `updateDoctor()`, `delete()`
- Model search/pagination variants: camelCase descriptive — `getAppointmentBySearch()`, `getDoctorByLimitBySearch()`
- Helpers: snake_case prefixed with `ci_` for globally-shareable pure functions — `ci_health_ready_payload()`, `ci_database_missing_env_keys()`
- Test methods: snake_case with `test_` prefix — `test_escapes_html_special_characters()`
- Constructors: always `__construct()` (not legacy `function __construct`)

**Variables:**
- Local variables: snake_case — `$patient_id`, `$doctor_id`, `$search_term`
- View data arrays: `$data['key']` pattern, keys are snake_case — `$data['patients']`, `$data['settings']`
- Parameters: snake_case — `$limit`, `$start`, `$order`, `$dir`

**DB Tables/Columns:**
- Table names: lowercase singular noun — `appointment`, `patient`, `doctor`, `settings`
- Column names: lowercase snake_case — `hospital_id`, `ion_user_id`, `patientname` (legacy flat names also present)
- All queries scope by `hospital_id` tenant key from session — `$this->session->userdata('hospital_id')`

## Code Style

**Indentation:**
- 4-space indentation in controllers and models
- Brace style varies: some files use Allman (brace on new line), others K&R (brace on same line)
- Test files use 4-space, strictly consistent (PSR-12 style)

**Comment Style:**
- Inline guards at file top: `if (!defined('BASEPATH')) exit('No direct script access allowed');` or `defined('BASEPATH') OR exit(...)`
- DocBlocks on helpers and test support classes: `/** @return array<string, string> */`
- Inline `// comments` for short explanations within methods
- Dead code left commented out with `//` rather than removed (common in controllers — see `appointment/controllers/Appointment.php` lines 41-49, `settings/controllers/Settings.php` lines 29-33)
- `log_message('debug', ...)` and `log_message('error', ...)` used in newer modules (e.g., `ai_image_analysis/controllers/Ai_image_analysis.php`)

**Error Handling Pattern:**
- Controller access guard: `if (!$this->ion_auth->in_group([...])) { redirect('home/permission'); }` — placed in `__construct()` or at the start of each action
- Form validation: CI's `form_validation` library with `set_rules()` / `run()` pattern
- AJAX endpoints: return `echo json_encode(['success' => false, 'message' => '...'])` then `return`
- Flash notifications: `show_swal($msg, 'success'|'error'|'warning', $title)` helper (sets CI session flashdata), then `redirect()`
- Exception handling: `try/catch(Exception $e)` with `show_error()` or `log_message('error', ...)` in newer modules; older modules lack try/catch entirely
- Health controller uses `Throwable` catch and never leaks DB error details to response

## Architectural Conventions

**How Controllers Are Structured:**

Every module controller follows this template:

```php
class ModuleName extends MX_Controller {
    public $settings; // optional cached row

    function __construct() {
        parent::__construct();
        // 1. Load all models this controller needs
        $this->load->model('module_name_model');
        $this->load->model('other_module/other_model');
        // 2. Auth guard — redirect immediately if not in allowed group
        if (!$this->ion_auth->in_group(['admin', 'Role1', 'Role2'])) {
            redirect('home/permission');
        }
    }

    public function index() {
        $data['items'] = $this->module_model->getItems();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard', $data);
        $this->load->view('index_view', $data);
        $this->load->view('home/footer');
    }
}
```

Key patterns:
- All modules extend `MX_Controller` (HMVC via Wiredesignz MX)
- `settings_model` is accessed globally without being explicitly loaded — it is loaded in a base or autoloaded path
- Three-view pattern for every page: `home/dashboard`, the module view, `home/footer`
- AJAX handler methods check `$this->input->is_ajax_request()` at the top, return JSON, no view loading
- Role-based permission check repeated per action when roles differ from constructor-level default

**How Models Are Structured:**

```php
class Module_model extends CI_model {
    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function insertRecord($data) {
        $data['hospital_id'] = $this->session->userdata('hospital_id');
        $this->db->insert('table_name', $data);
    }

    function getRecord() {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        return $this->db->get('table_name')->result();
    }

    // DataTables server-side variant pattern (4 methods):
    function getWithoutSearch($order, $dir) { ... }
    function getBySearch($search, $order, $dir) { ... }
    function getByLimit($limit, $start, $order, $dir) { ... }
    function getByLimitBySearch($limit, $start, $search, $order, $dir) { ... }
}
```

Key patterns:
- All queries inject `hospital_id` from session — multi-tenancy isolation
- DataTables support requires four paired methods per entity (without/with search, without/with pagination)
- Raw LIKE queries built by concatenating `$search` without parameterization (SQL injection risk — see `appointment/models/Appointment_model.php:46`, `patient/models/Patient_model.php:65`)
- `result()` (returns array of objects) used universally; `row()` for single-row lookups
- Models call `$this->db` directly — no repository abstraction layer

**How Views Are Organized:**

- Each module has a `views/` subdirectory, e.g., `application/modules/appointment/views/`
- Views are pure PHP/HTML fragments, not full pages
- Pages rendered by three consecutive `$this->load->view()` calls in the controller
- Shared chrome lives in `application/modules/home/views/dashboard.php` (full `<html>`, nav, scripts) and `home/footer`
- Views obtain the CI instance via `$CI = get_instance();` to call models or helpers directly from view code — this is an established pattern in this codebase
- Inline `<style>` blocks are common in views for page-specific CSS
- Flash messages checked as: `$this->session->flashdata('success')` / `$this->session->flashdata('error')`
- Language strings used via `lang('key')` helper throughout views

**How Config Is Handled:**

- Environment-specific values read from env vars with `getenv()` + fallback defaults (see `application/config/openai.php`, `application/config/database_bootstrap.php`)
- Module-specific config files loaded in controller constructor: `$this->config->load('openai', true)` (namespaced to avoid collision)
- Global config in `application/config/config.php`; platform metadata in `application/config/platform.php`
- Autoloaded helpers: `url`, `file`, `form`, `asset`, `audit`, `safe_redirect`, `chamber_practice` (see `application/config/autoload.php`)
- Autoloaded libraries: `database`, `email`, `session`
- Constants defined in `application/config/constants.php` using `defined('X') OR define('X', value)` guard pattern

## Anti-Patterns (Seen in Codebase)

- **Raw SQL injection via LIKE**: Search strings concatenated directly into query strings without binding — `"(id LIKE '%" . $search . "%' ...)"` in `appointment/models/Appointment_model.php:46` and `patient/models/Patient_model.php:65`.
- **Business logic in views**: `application/modules/home/views/dashboard.php` runs DB queries directly (`$this->db->get('settings')`) to determine RTL/LTR direction. Logic belongs in the controller.
- **Dead commented-out code**: Verification/subscription redirect blocks left commented in `appointment/controllers/Appointment.php` and `settings/controllers/Settings.php` rather than removed.
- **Mixed `function` vs `public function` declarations**: Older modules use bare `function __construct()` and `function methodName()` (implicitly public); newer modules use explicit `public function`. Inconsistent within the codebase.
- **Inconsistent BASEPATH guard syntax**: Some files use `if (!defined('BASEPATH')) exit(...)`, others `defined('BASEPATH') OR exit(...)`. Both are correct but should be standardized.
- **Obfuscated method name**: `verifyYourPruchase776cbvcfytfytfvvn()` in `settings/controllers/Settings.php` — security through obscurity, not real access control.
- **Variable potentially undefined**: `$doctor_id` used in `prescription/controllers/Prescription.php:35` without a fallback if the user is not a Doctor role, risking a PHP notice.
