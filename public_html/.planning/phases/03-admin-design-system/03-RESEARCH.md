# Phase 3: Admin Design System — Research

**Researched:** 2026-04-27
**Domain:** AdminLTE 3 / Bootstrap 4 / jQuery DataTables / CodeIgniter 3 CSRF
**Confidence:** HIGH

---

<phase_requirements>
## Phase Requirements

| ID | Description | Research Support |
|----|-------------|------------------|
| UI-01 | All module views follow consistent AdminLTE 3 card/section layout — no legacy inline styles | Conforming pattern confirmed in `appointment.php`, `doctor.php`, `patient.php`. Non-conforming pattern confirmed in `lab.php` and ~43 additional views using `bg-gradient-light` + inline header sections. `partials/page_header` exists at `application/views/partials/page_header.php`. |
| UI-02 | DataTables initialized with standardized server-side config across all list views | 134 view files still use the legacy `id="editable-sample"` table ID (managed by `editable-table.js`). 25 controllers already return `recordsTotal` JSON. Zero views currently use the `id="dt-{module}"` naming convention. Server-side DataTable pattern is fully established in lab, inventory, finance, emergency, ai modules. |
| UI-03 | All AJAX POST requests include CSRF token header; `csrf_regenerate = true` in config.php | `csrf_regenerate = false` confirmed at `config.php` line 481. `csrf_inject.php` prefilter exists and loads on every page after line 134 of `footer.php`. 89 view files have manual CSRF token injection (`get_csrf_token_name`/`get_csrf_hash`) — these must be audited for double-token conflicts after enabling regenerate. Sequential-POST risk requires `window.CI_CSRF_HASH` global and `$(document).ajaxSuccess` hash-refresh handler. |
| UI-04 | Design tokens applied — buttons, inputs, cards, modals, status tags | `app-design-tokens.css` is fully specified and loaded on all pages. Zero module views currently use `.ap-status-*` classes. 52 files use raw Bootstrap `badge-success/warning/danger/info` (348 total occurrences). Inline color styles on buttons/elements found in 13 files. |
</phase_requirements>

---

## Summary

Phase 3 is a brownfield standardization sweep across 70+ existing admin modules. No new design system is introduced — the token layer (`app-design-tokens.css`), the `partials/page_header` partial, and the DataTables plugin bundle are all already present and globally loaded. The work is exclusively applying existing infrastructure to non-conforming views, filling two explicit CSRF gaps, and replacing legacy status badge classes.

The conformance gap is large but uniform: 134 view files still use the legacy `id="editable-sample"` DataTable identity managed by `editable-table.js` (client-side, non-server-side). These need to be migrated to server-side `DataTable({ serverSide: true })` with the `id="dt-{module}"` naming convention. 25 of the 91 module controllers already implement the server-side JSON response contract (`recordsTotal`, `recordsFiltered`, `data`, `draw`) — the remaining modules will need a `datatables()` controller method and model query methods added.

The CSRF change (`csrf_regenerate = true`) is the highest-risk atomic change in the phase. 89 view files manually inject the CSRF token inline; after regeneration is enabled, any sequential AJAX POST that does not read the new hash from the JSON envelope will fail on the second request. The mitigation is already specified in the UI-SPEC: add `window.CI_CSRF_HASH` and an `ajaxSuccess` handler in `csrf_inject.php`, then remove duplicate manual tokens from views.

**Primary recommendation:** Prioritize the CSRF config change and `csrf_inject.php` hash-refresh patch as Wave 0 (it is a single-file change that unblocks all other AJAX work). Execute DataTables migration module-by-module, highest-traffic first. Design token status badge swap is CSS-only in many cases and can follow.

---

## Architectural Responsibility Map

| Capability | Primary Tier | Secondary Tier | Rationale |
|------------|-------------|----------------|-----------|
| Card/section layout structure | Frontend (view) | — | Pure HTML/CSS; no server logic involved |
| DataTables initialization config | Frontend (view/JS) | — | Client-side DataTables init lives in view JS |
| DataTables server-side data endpoint | Backend (controller) | Model (query) | Controller accepts DT params, model queries with pagination/search |
| CSRF token injection (form) | View (csrf_inject.php) | — | Prefilter runs globally after jQuery loads |
| CSRF config (`csrf_regenerate`) | Server config (config.php) | — | Single-file PHP config change |
| CSRF hash refresh on sequential POSTs | Frontend (csrf_inject.php JS) | Backend (AJAX response envelope) | Frontend reads hash from response; backend must include it |
| Design token CSS (`:root` vars) | Shared CSS (app-design-tokens.css) | — | Already globally loaded; no per-module change needed |
| Status badge class replacement | Frontend (view) | — | Replace `badge-*` with `.ap-status .ap-status-*` in view HTML |
| page_header partial | View (partials/page_header.php) | — | Already exists at `application/views/partials/page_header.php` |

---

## Standard Stack

### Core (Already in Codebase — No New Installs)

| Library | Version | Purpose | Status |
|---------|---------|---------|--------|
| AdminLTE 3 | vendored | Page chrome, card components, layout | Loaded globally |
| Bootstrap 4/5 compat | vendored | Grid, modals, forms, badges | Loaded globally |
| jQuery | vendored | DOM, AJAX, plugin host | Loaded in `footer.php` line 67 |
| DataTables | vendored | Sortable/filterable/paginated tables | Loaded in `footer.php` lines 139–150 |
| DataTables Buttons | vendored | Copy/CSV/Excel/PDF/Print/ColVis buttons | Loaded in `footer.php` lines 143–150 |
| DataTables Responsive | vendored | Responsive column collapse | Loaded in `footer.php` lines 141–142 |
| SweetAlert2 | vendored | Delete confirmation dialogs | Loaded in `footer.php` line 198 |
| Select2 | vendored | Searchable dropdowns | Loaded in `footer.php` line 151 |
| FontAwesome Free 5 | vendored | Icons | Loaded in dashboard.php |

[VERIFIED: grep of footer.php lines 67–200, adminlte/plugins/ directory listing]

### What Does NOT Need Installation

Nothing new to install. All libraries are vendored in `adminlte/plugins/` or `common/`. The design token CSS is already at `application/assets/css/app-design-tokens.css`. The `partials/page_header.php` partial is already at `application/views/partials/page_header.php`.

---

## Architecture Patterns

### System Architecture Diagram

```
Browser GET /module/index
        |
        v
[CI3 Controller: module/index()]
  loads: home/dashboard + module_view + home/footer
        |
        +--> home/dashboard.php  [<html>, nav, CSS links, app-design-tokens.css loaded here]
        |
        +--> module/views/index.php
        |     Uses: partials/page_header
        |           card shell: .card.shadow-sm.border-0
        |           table: #dt-{module}, serverSide:true
        |           AJAX target: module/datatables
        |           modals: #addModal, #editModal
        |           status cells: .ap-status .ap-status-{variant}
        |
        +--> home/footer.php  [jQuery, DataTables, csrf_inject, SweetAlert2, Select2]
                              [csrf_inject.php: $.ajaxPrefilter injects CSRF on all POST]

Browser POST /module/datatables  (DataTables server-side AJAX)
        |
        v
[CI3 Controller: datatables()]
  reads: $_POST['draw'], 'start', 'length', 'search[value]', 'order[0][column]', 'order[0][dir]'
  calls: model->getByLimitBySearch() or model->getByLimit()
  returns: JSON { draw, recordsTotal, recordsFiltered, data[], csrf_hash }
```

### Recommended Project Structure for New DataTables Endpoints

```
application/modules/{module}/
├── controllers/{Module}.php        — add public function datatables()
├── models/{Module}_model.php       — add getByLimit(), getByLimitBySearch(),
│                                     getWithoutSearch(), getBySearch()
└── views/index.php                 — table id="dt-{module}", serverSide:true AJAX call
```

### Pattern 1: Server-Side DataTable Controller Method

**What:** Controller method that reads DataTables POST params, delegates to model, returns JSON.
**When to use:** Every module list view.

```php
// Source: established pattern in application/modules/doctor/controllers/Doctor.php:510
// and application/modules/emergency/controllers/Emergency.php
public function datatables()
{
    $draw   = $this->input->post('draw');
    $start  = $this->input->post('start');
    $length = $this->input->post('length');
    $search = $this->input->post('search')['value'];
    $order_col = $this->input->post('order')[0]['column'];
    $order_dir = $this->input->post('order')[0]['dir'];

    // Column map — index matches columns[] order in JS init
    $columns = ['id', 'name', /* ... */];
    $order = isset($columns[$order_col]) ? $columns[$order_col] : 'id';

    if ($search) {
        $total    = count($this->module_model->getBySearch($search, $order, $order_dir));
        $filtered = count($this->module_model->getBySearch($search, $order, $order_dir));
        $records  = $this->module_model->getByLimitBySearch($length, $start, $search, $order, $order_dir);
    } else {
        $total    = count($this->module_model->getWithoutSearch($order, $order_dir));
        $filtered = $total;
        $records  = $this->module_model->getByLimit($length, $start, $order, $order_dir);
    }

    $data = [];
    foreach ($records as $row) {
        $data[] = [
            'id'      => $row->id,
            'name'    => htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'),
            // ... other columns ...
            'actions' => '<div class="ap-row-actions">'
                       . '<a href="#" class="ap-icon-btn" title="Edit" onclick="editRecord(' . $row->id . ')"><i class="fas fa-edit"></i></a>'
                       . '<a href="#" class="ap-icon-btn text-danger" title="Delete" onclick="confirmDelete(' . $row->id . ')"><i class="fas fa-trash"></i></a>'
                       . '</div>',
        ];
    }

    echo json_encode([
        'draw'            => (int) $draw,
        'recordsTotal'    => $total,
        'recordsFiltered' => $filtered,
        'data'            => $data,
        'csrf_hash'       => $this->security->get_csrf_hash(), // UI-03: return new hash
    ]);
}
```

### Pattern 2: Four-Method Model Query Pair

**What:** Model methods that DataTables controller uses for pagination + search.
**When to use:** Any module that needs server-side DataTables.

```php
// Source: established pattern in application/modules/doctor/models/Doctor_model.php
// and CONVENTIONS.md model pattern section
public function getWithoutSearch($order, $dir)
{
    $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
    $this->db->order_by($order, $dir);
    return $this->db->get('module_table')->result();
}

public function getBySearch($search, $order, $dir)
{
    $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
    $this->db->group_start();
    $this->db->like('id', $search);
    $this->db->or_like('name', $search);
    $this->db->group_end();
    $this->db->order_by($order, $dir);
    return $this->db->get('module_table')->result();
}

public function getByLimit($limit, $start, $order, $dir)
{
    $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
    $this->db->order_by($order, $dir);
    $this->db->limit($limit, $start);
    return $this->db->get('module_table')->result();
}

public function getByLimitBySearch($limit, $start, $search, $order, $dir)
{
    $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
    $this->db->group_start();
    $this->db->like('id', $search);
    $this->db->or_like('name', $search);
    $this->db->group_end();
    $this->db->order_by($order, $dir);
    $this->db->limit($limit, $start);
    return $this->db->get('module_table')->result();
}
```

**Key rule:** Use `$this->db->like()` — never string interpolation. SEC-05 mandates this. [VERIFIED: CLAUDE.md, CONVENTIONS.md anti-patterns section]

### Pattern 3: Standard View Shell (UI-01 Conforming)

**What:** The exact HTML structure every list view must use.
**When to use:** Every admin module list view page.

```php
// Source: application/views/partials/page_header.php (verified exists)
// Source: appointment.php, doctor.php, patient.php (verified conforming examples)
<div class="content-wrapper bg-light">
    <?php $CI->load->view('partials/page_header', [
        'title'       => lang('module_name'),
        'icon'        => 'fas fa-icon-name text-primary mr-2',
        'breadcrumbs' => [
            ['label' => lang('home'), 'url' => 'home'],
            ['label' => lang('module_name'), 'url' => null],
        ],
    ]); ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom d-flex flex-wrap
                                    align-items-center justify-content-between gap-2 py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase">
                                <?php echo lang('list_label'); ?>
                            </h3>
                            <button type="button" class="btn btn-sm btn-primary"
                                    data-toggle="modal" data-target="#addModal">
                                <i class="fas fa-plus mr-1"></i>
                                <?php echo lang('add_new'); ?>
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle mb-0"
                                       id="dt-MODULE_NAME" style="width:100%">
                                    <thead><tr><!-- th cols --></tr></thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
```

### Pattern 4: CSRF Hash Refresh for Sequential POSTs (UI-03)

**What:** Patch to `csrf_inject.php` so `window.CI_CSRF_HASH` tracks the latest token after each POST response.
**When to use:** Required once `csrf_regenerate = true` is enabled.

```javascript
// Source: UI-SPEC §CSRF Contract — additions to csrf_inject.php
// After the existing $.ajaxPrefilter block, add:
window.CI_CSRF_HASH = h; // expose page-load hash globally

// Replace the inner `h` reference in the prefilter to use window.CI_CSRF_HASH:
// options.data[n] = window.CI_CSRF_HASH;  (and other branches likewise)

// Add a global success handler AFTER the prefilter:
$(document).ajaxSuccess(function(event, xhr, settings, data) {
    if (data && data.csrf_hash) {
        window.CI_CSRF_HASH = data.csrf_hash;
    }
});
```

### Pattern 5: Status Badge Replacement (UI-04)

**What:** Replace raw Bootstrap badge classes with `.ap-status` design token classes.
**When to use:** Every status column in every DataTable.

```html
<!-- Before (non-conforming — 348 occurrences across 52 files): -->
<span class="badge badge-success">Confirmed</span>
<span class="badge badge-warning">Pending</span>
<span class="badge badge-danger">Cancelled</span>
<span class="badge badge-info">Requested</span>

<!-- After (conforming — source: app-design-tokens.css .ap-status-* classes): -->
<span class="ap-status ap-status-success">Confirmed</span>
<span class="ap-status ap-status-warning">Pending</span>
<span class="ap-status ap-status-danger">Cancelled</span>
<span class="ap-status ap-status-info">Requested</span>
```

[VERIFIED: app-design-tokens.css lines 433–478 — `.ap-status`, `.ap-status-success`, `.ap-status-warning`, `.ap-status-danger`, `.ap-status-info` all defined]

### Anti-Patterns to Avoid

- **`EditableTable.init()` pattern:** `editable-table.js` initializes `#editable-sample` as a client-side inline-editable DataTable with no server pagination. It is incompatible with server-side mode. **Never call `EditableTable.init()`** for list views — use `$('#dt-MODULE').DataTable({ serverSide: true })` instead.
- **Manual CSRF in inline data:** Views that set `data: { csrf_token_name: csrf_hash }` in a `$.ajax()` call will send a double token after `csrf_regenerate = true`. CodeIgniter will accept the first token it finds and ignore the second, but the stale baked-in value from render time will fail on second requests. Remove all manual injections and let the prefilter handle it.
- **Hardcoded English strings in view:** Use `lang('key')` — the system has 30+ language packs and hardcoded English breaks i18n compatibility.
- **Raw SQL LIKE via string concat:** The four-method model pattern must use `$this->db->like()` not `WHERE id LIKE '%$var%'` (SEC-05 is still open — Phase 3 model methods must not introduce new violations).

---

## Conformance Audit Results

### UI-01: Layout Conformance

| Status | Evidence | Count |
|--------|----------|-------|
| Conforming: uses `partials/page_header` | grep across modules | 94 view files |
| Non-conforming: uses `bg-gradient-light` + inline section header | grep: `content-wrapper bg-gradient-light` | 43 view files |
| Conforming: uses `content-wrapper bg-light` (correct class) | grep | 179 occurrences (includes conforming + partially conforming) |

Confirmed non-conforming layout pattern (from `lab/views/lab.php`):
- `<div class="content-wrapper bg-gradient-light">` — wrong wrapper class
- `<section class="content-header py-4 bg-white shadow-sm">` — inline header instead of partial
- `<h1 class="display-4 font-weight-black mb-0">` — wrong heading style

[VERIFIED: direct file read of lab.php lines 1–30]

### UI-02: DataTables Conformance

| Status | Evidence | Count |
|--------|----------|-------|
| Legacy `id="editable-sample"` table IDs | grep: `id="editable-sample"` | **134 view files** |
| `id="dt-*"` naming (new standard) — NOT YET USED | grep: `id="dt-"` | **0 occurrences** |
| Views with `serverSide: true` DataTables init | grep: `serverSide.*true` | 20 view files (lab, inventory, finance, emergency, ai modules) |
| Controllers returning `recordsTotal` JSON | grep: `recordsTotal` | 25 controllers |
| Views using standard `DataTable({` init | grep: `DataTable\(\{` | 38 view files |

**Key finding:** The 134 `editable-sample` views are managed by `common/js/editable-table.js`, which initializes a client-side non-server-side DataTable on the `#editable-sample` selector globally. Any view that does not have its own `DataTable()` call falls through to this global init. Replacing requires: (1) rename table ID to `dt-{module}`, (2) add `serverSide: true` init in the view, (3) add `datatables()` controller method, (4) add four model query methods.

Modules that **already have `recordsTotal` and are partially compliant** (need only view-side update and ID rename):
`appointment`, `doctor`, `patient`, `lab`, `inventory`, `finance`, `emergency`, `ai_image_analysis`, `ai_patient_overview`, `medicine`, `meeting`, `treatment`, `sms`, `prescription`, `advice`, `macro`, `diagnosis`, `symptom`, `doctorvisit`, `insurance`, `logs`, `systems`, `leave` (25 modules)

Modules that **need both controller and model work** plus view update: all remaining ~45 modules.

### UI-03: CSRF Conformance

| Item | Status |
|------|--------|
| `csrf_protection = true` | Already set — `config.php` line 477 |
| `csrf_regenerate = false` | **Gap confirmed** — `config.php` line 481 |
| `csrf_inject.php` prefilter exists | Confirmed — `application/views/csrf_inject.php` loaded in `footer.php` line 134 |
| jQuery available before prefilter | Confirmed — jQuery loaded at `footer.php` line 67, prefilter at line 134 |
| `window.CI_CSRF_HASH` global | **Missing** — prefilter uses closed-over `h` variable, not updatable from outside |
| `ajaxSuccess` hash refresh | **Missing** — required after `csrf_regenerate = true` |
| Manual CSRF in views (`get_csrf_token_name`) | **89 view files** with manual injection — audit required |
| AJAX endpoints returning `csrf_hash` | **Not standard yet** — must be added to all `datatables()` and AJAX methods |

[VERIFIED: direct file reads of config.php line 481, csrf_inject.php full content, footer.php lines 67/134]

### UI-04: Design Token Conformance

| Item | Status | Evidence |
|------|--------|----------|
| `app-design-tokens.css` defines `--ap-*` vars | Confirmed — 44 custom properties defined | File read lines 7–43 |
| `.ap-status-*` classes defined | Confirmed — 4 variants (success/warning/danger/info) | File read lines 454–478 |
| `.ap-icon-btn` class defined | Confirmed | File read lines 487–505 |
| `.ap-row-actions` class defined | Confirmed | File read lines 480–485 |
| `.ap-status-*` actually used in module views | **Zero usage** | grep across all modules: 0 results |
| Raw `badge-success/warning/danger/info` usage | **348 occurrences in 52 files** | grep result |
| Inline `style="background-color:..."` on elements | 13 files | grep result |
| Buttons using `btn-primary/secondary/danger` | Widespread — consistent with spec | Pattern visible in conforming views |

[VERIFIED: grep of application/ directory, direct CSS file read]

---

## Don't Hand-Roll

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Sortable/paginated/searchable tables | Custom PHP pagination + table render | DataTables serverSide | Handles sorting, search, export, responsive, accessibility |
| Delete confirmation dialogs | Native `confirm()` dialog | SweetAlert2 `Swal.fire()` | Already loaded globally; `confirm()` is blocked by some browsers |
| CSRF token injection | Per-view manual token embed | `csrf_inject.php` prefilter | Global coverage; avoids stale tokens in sequential POST flows |
| Status color badges | Inline `style="background:#..."` | `.ap-status .ap-status-{variant}` | Token layer ensures consistency; dark mode support; print mode |
| Page header HTML | Duplicated `content-header` HTML in each view | `partials/page_header` partial | Exists at `application/views/partials/page_header.php` |
| Select dropdowns | `<select>` without Select2 | Select2 (already loaded globally) | Search, clear, remote data — already globally available |

**Key insight:** Every piece of infrastructure this phase needs already exists. The work is applying it consistently, not building anything new.

---

## Common Pitfalls

### Pitfall 1: Double CSRF Token After Enabling Regeneration
**What goes wrong:** View renders with token `A` baked into a hidden input. User submits form once — token `A` consumed, server returns token `B`. Second submit (or second AJAX call on same page load) sends token `A` again — rejected with 403.
**Why it happens:** `csrf_regenerate = true` invalidates the token after each POST. Views that manually embed the token at render time can't update it.
**How to avoid:** (1) Patch `csrf_inject.php` to expose `window.CI_CSRF_HASH` and update it from `ajaxSuccess`. (2) Remove manual token embeds from the 89 files that have them. (3) Enable `csrf_regenerate = true` AFTER patching the prefilter.
**Warning signs:** In DevTools, a second AJAX POST to the same endpoint returns 403 or redirects to the login page.

### Pitfall 2: `editable-table.js` Interfering with New DataTables
**What goes wrong:** `editable-table.js` is loaded globally in `footer.php` line 95 and calls `$('#editable-sample').dataTable(...)`. If a view has both `id="editable-sample"` and a new `DataTable({ serverSide: true })` call targeting the same element, two DataTables instances will fight.
**Why it happens:** The global `editable-table.js` initializes unconditionally on any element matching `#editable-sample`.
**How to avoid:** When migrating a view, rename the table ID from `editable-sample` to `dt-{module}` simultaneously with adding the new DataTable init. Never mix IDs.
**Warning signs:** DataTables "Cannot reinitialize" console error.

### Pitfall 3: `partials/page_header` Path Resolution
**What goes wrong:** `$this->load->view('partials/page_header', ...)` fails with "Unable to load the requested file" because CodeIgniter HMVC looks in the module's own `views/` first.
**Why it happens:** CI3 HMVC MX resolves view paths relative to the calling module's views directory. The partial lives in `application/views/partials/`, not in the module.
**How to avoid:** The `$CI->load->view(...)` call (using `get_instance()`) uses the top-level loader which finds `application/views/partials/page_header.php`. This is already the established pattern — confirmed in `appointment.php`, `doctor.php`, `patient.php` which all work correctly. Always use `$CI->load->view(...)` (via `$CI = get_instance()`) for cross-module partials, never `$this->load->view(...)`.
**Warning signs:** "Unable to load the requested file: partials/page_header.php" error on page load.

### Pitfall 4: Status Tag Semantic Mismatch
**What goes wrong:** Developer maps "Requested" status to `.ap-status-success` (green) instead of `.ap-status-info` (blue) because it feels "positive".
**Why it happens:** Semantic meaning of the four variants is not obvious from class names.
**How to avoid:** Follow the UI-SPEC semantic map exactly:
- `.ap-status-success` = Treated, Confirmed, Completed, Active (green)
- `.ap-status-warning` = Pending Confirmation, Waiting (yellow/orange)
- `.ap-status-danger` = Cancelled, Emergency, Overdue (red)
- `.ap-status-info` = Draft, Requested, Info states (blue)
**Warning signs:** Green "Cancelled" or red "Confirmed" status pills.

### Pitfall 5: DataTables `align-middle` Class Missing
**What goes wrong:** Action buttons in the actions column appear vertically misaligned because the default `<td>` vertical-align is `baseline`.
**Why it happens:** `table-bordered table-hover` does not apply vertical centering.
**How to avoid:** Always add `align-middle` to the `<table>` class list: `class="table table-hover table-bordered align-middle mb-0"`.

### Pitfall 6: `hospital_id` Missing from New Model Methods
**What goes wrong:** New `getByLimit()` / `getBySearch()` model methods return records from ALL hospitals, not just the current tenant.
**Why it happens:** Forgetting to add `$this->db->where('hospital_id', $this->session->userdata('hospital_id'))` to every query method.
**How to avoid:** Multi-tenancy isolation is mandatory. Every model query in this codebase must scope by `hospital_id` from session. [VERIFIED: CONVENTIONS.md architectural conventions]

---

## CSRF Risk Analysis (UI-03 Deep Dive)

**Current state:** `csrf_regenerate = false`. Every page load generates one token that remains valid until it expires (`csrf_expire = 7200` seconds, i.e. 2 hours). The prefilter reads this token at load time and appends it to every POST — this works correctly today.

**After change to `csrf_regenerate = true`:** Each POST request invalidates the previous token and the server sets a new CSRF cookie. The response does NOT automatically include the new hash in the response body — the client must extract it from the cookie or the app must return it in JSON.

**The three-file fix required:**
1. `application/config/config.php` line 481: `false` → `true`
2. `application/views/csrf_inject.php`: expose `window.CI_CSRF_HASH = h;` and replace closed `h` with `window.CI_CSRF_HASH` in all prefilter branches; add `$(document).ajaxSuccess` handler
3. All controller AJAX endpoints: add `'csrf_hash' => $this->security->get_csrf_hash()` to every JSON response

**Views to audit for manual CSRF token removal:** 89 files contain `get_csrf_token_name` or `get_csrf_hash` inline. These fall into two categories:
- **Form hidden inputs:** `<input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="...">` — safe to keep for traditional form POSTs (CI handles this natively), but redundant once the prefilter is patched
- **AJAX `data:` objects:** `data: { csrf_token: '<?php echo $this->security->get_csrf_hash(); ?>' }` — these MUST be removed to prevent double-token conflicts

The `csrf_inject.php` prefilter already handles all cases: plain objects, FormData, query strings, JSON. Manual injection in AJAX data objects is redundant and conflicting.

---

## Module Priority for Wave Execution

Based on traffic and visibility (highest-impact modules to standardize first):

**Wave 1 — Core clinical modules (most used daily):**
- `appointment` — primary workflow module; already has controller DataTables methods
- `patient` — core patient list; already has controller DataTables methods
- `doctor` — frequently visible; already has controller DataTables methods
- `prescription` — critical clinical; already has controller DataTables methods

**Wave 2 — Finance and lab (billing-critical):**
- `finance/payment` — primary billing view
- `lab/lab` — lab reports (known non-conforming layout)
- `medicine` — pharmacy stock

**Wave 3 — Support modules:**
- `department`, `diagnosis`, `symptom`, `advice`, `doctorvisit`
- `schedule`, `leave`, `attendance`, `payroll`

**Wave 4 — Admin/config modules (lower traffic):**
- `settings`, `hospital`, `superadmin`, `systems`
- `sms`, `email`, `notice`, `faq`, `site`, `slide`

**Exclude from Phase 3 (portal views, not admin):**
- `portal/`, `patient_chamber/` patient-facing views — these belong to Phase 4 (Patient Portal)
- `frontend/` — public marketing site, not admin

---

## Validation Architecture

### Test Framework

| Property | Value |
|----------|-------|
| Framework | PHPUnit 10.5 |
| Config file | `phpunit.xml.dist` (project root) |
| Quick run command | `composer test -- --filter AdminDesignSystem` |
| Full suite command | `composer test` |

### Phase Requirements → Test Map

| Req ID | Behavior | Test Type | Automated Command | File Exists? |
|--------|----------|-----------|-------------------|-------------|
| UI-01 | No view uses `content-wrapper bg-gradient-light` wrapper class | Static source grep | `composer test -- --filter AdminDesignSystemTest::test_ui01_no_bg_gradient_light_in_list_views` | Wave 0 |
| UI-01 | No view has inline `<div style="...">` layout sections | Static source grep | `composer test -- --filter AdminDesignSystemTest::test_ui01_no_inline_style_layout_divs` | Wave 0 |
| UI-02 | No primary list view uses `id="editable-sample"` table ID | Static source grep | `composer test -- --filter AdminDesignSystemTest::test_ui02_no_editable_sample_in_list_views` | Wave 0 |
| UI-02 | All DataTables endpoint controllers return `recordsTotal` key | Static source grep | `composer test -- --filter AdminDesignSystemTest::test_ui02_datatables_controllers_return_records_total` | Wave 0 |
| UI-03 | `csrf_regenerate = true` in config.php | Static source grep | `composer test -- --filter AdminDesignSystemTest::test_ui03_csrf_regenerate_is_true` | Wave 0 |
| UI-03 | `window.CI_CSRF_HASH` exposed in csrf_inject.php | Static source grep | `composer test -- --filter AdminDesignSystemTest::test_ui03_csrf_inject_exposes_global_hash` | Wave 0 |
| UI-04 | Zero `badge-success/warning/danger/info` occurrences in module views | Static source grep | `composer test -- --filter AdminDesignSystemTest::test_ui04_no_raw_badge_classes` | Wave 0 |
| UI-04 | `.ap-status-*` classes present in app-design-tokens.css | Static source assert | `composer test -- --filter AdminDesignSystemTest::test_ui04_ap_status_classes_defined` | Wave 0 |

### Wave 0 Gaps

- [ ] `tests/Unit/AdminDesignSystem/AdminDesignSystemTest.php` — covers UI-01 through UI-04 (8 assertions above)
- [ ] Wave 0 plan must create this test file before any implementation plans run

*(No new framework install needed — PHPUnit is already configured)*

---

## Environment Availability

> Step 2.6: All dependencies are vendored PHP libraries loaded from disk. No external services, CLI tools beyond PHP/Composer, or runtime services are required by this phase.

| Dependency | Required By | Available | Version | Fallback |
|------------|------------|-----------|---------|----------|
| PHP 8.1+ | CodeIgniter 3 runtime | Confirmed (production system running) | 8.1+ | — |
| Composer | `composer test` | Confirmed | Present (`composer.json` exists) | — |
| PHPUnit 10.5 | Test runner | Confirmed | `phpunit.xml.dist` present | — |
| DataTables plugin | UI-02 | Confirmed (vendored) | `adminlte/plugins/datatables/` | — |
| SweetAlert2 | UI-04 confirmations | Confirmed (vendored) | `adminlte/plugins/sweetalert2/` | — |
| app-design-tokens.css | UI-04 | Confirmed | `application/assets/css/app-design-tokens.css` | — |
| partials/page_header.php | UI-01 | Confirmed | `application/views/partials/page_header.php` | — |

**No missing dependencies. No fallbacks needed.**

---

## Security Domain

> `security_enforcement` not explicitly set to false — treated as enabled.

### Applicable ASVS Categories

| ASVS Category | Applies | Standard Control |
|---------------|---------|-----------------|
| V2 Authentication | No | Not modified in this phase |
| V3 Session Management | No | Not modified in this phase |
| V4 Access Control | No | Existing auth guards unchanged |
| V5 Input Validation | Yes | `$this->db->like()` in all new model search methods — mandatory per CLAUDE.md |
| V6 Cryptography | No | CSRF uses CI3 native token (secure random) |
| V10 Malicious Code | Partial | `csrf_regenerate = true` closes CSRF replay window |

### Known Threat Patterns for This Stack

| Pattern | STRIDE | Standard Mitigation |
|---------|--------|---------------------|
| CSRF token replay (fixed token per session) | Spoofing | `csrf_regenerate = true` + hash refresh in prefilter |
| SQL injection via search input in new model methods | Tampering | `$this->db->like()` — never string concatenation |
| XSS in DataTables `data[]` action column HTML | Tampering | `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` on all user-controlled strings rendered in action buttons |
| Double-token conflict on sequential AJAX | Denial | Patch `csrf_inject.php` before enabling regeneration |

---

## Assumptions Log

| # | Claim | Section | Risk if Wrong |
|---|-------|---------|---------------|
| A1 | Modules not in the `recordsTotal` grep list (66 modules) do not have server-side DataTables controller methods | UI-02 Conformance Audit | Some modules may have the method under a different name — would reduce Wave 3/4 scope |
| A2 | The 43 views using `bg-gradient-light` represent the full set of non-conforming layout views | UI-01 Conformance Audit | Additional non-conforming patterns may exist using other legacy wrapper classes |
| A3 | Removing manual CSRF injection from AJAX `data:` objects and relying solely on the prefilter is safe for all modules | CSRF Risk Analysis | A module that constructs AJAX data in a way the prefilter doesn't cover (e.g., raw XHR, fetch API) would break — needs manual audit |

**If A3 is wrong:** The mitigation is to audit each of the 89 files individually before removing manual tokens. The prefilter covers jQuery `$.ajax`, `$.post`, FormData, JSON, and query-string formats — this covers ~100% of this codebase's AJAX usage pattern.

---

## Open Questions (RESOLVED)

1. **Which of the 66 non-`recordsTotal` modules have existing non-DataTables list functionality that should be preserved vs. replaced?**
   - RESOLVED: Complex-filtered modules (attendance, payroll, schedule) will be flagged for per-module execution-time audit in the plan. The plan includes an audit task before each wave of DataTables migration. Standard pattern applies where possible; custom `datatables()` methods added where filter params are needed. Plans proceed on this basis.

2. **Does enabling `csrf_regenerate = true` affect the API endpoints (mobile app)?**
   - RESOLVED: `csrf_exclude_uris` (config.php lines 482–496) already excludes all external-facing non-browser endpoints: `api.*`, `api/v1/.*`, `payu.*`, `paystack.*`, `paypal.*`, `pgateway.*`, `payment_bd/sslcommerz_*`, `payment_bd/bkash_callback`, `cronjobs.*`, `request.*`, `health.*`. All webhook and payment callbacks are covered. Safe to set `csrf_regenerate = true`. Plans proceed on this basis.

3. **`partials/page_header` path: does `$CI->load->view('partials/page_header')` resolve across all HMVC modules?**
   - RESOLVED: Pattern confirmed working in 94 views using `appointment`, `doctor`, `patient`. CI3 HMVC `get_instance()` loader resolves global view paths. Plan includes a smoke-test step in Wave 1 (lab module) before bulk migration. Plans proceed on this basis.

---

## Sources

### Primary (HIGH confidence)

- CODEBASE — `application/assets/css/app-design-tokens.css` — full file read; all `.ap-status-*`, `.ap-icon-btn`, `.ap-row-actions`, `--ap-*` custom properties verified
- CODEBASE — `application/views/csrf_inject.php` — full file read; prefilter implementation verified
- CODEBASE — `application/config/config.php` lines 475–490 — `csrf_regenerate = false` confirmed at line 481
- CODEBASE — `application/modules/home/views/footer.php` — jQuery load order (line 67), csrf_inject load (line 134), DataTables bundle (lines 139–150) verified
- CODEBASE — `application/views/partials/page_header.php` — full file read; partial exists and is functional
- CODEBASE — grep across `application/modules/` — `id="editable-sample"`: 134 files; `recordsTotal`: 25 controllers; `badge-success/warning/danger/info`: 52 files/348 occurrences; `ap-status`: 0 occurrences; `partials/page_header`: 94 files; `content-wrapper bg-gradient-light`: 43 files
- CODEBASE — `application/modules/appointment/views/appointment.php` — conforming layout reference (confirmed)
- CODEBASE — `application/modules/lab/views/lab.php` — non-conforming layout reference (confirmed)
- CODEBASE — `application/modules/doctor/views/doctor.php` — partially conforming (card layout, wrong table ID)
- CODEBASE — `common/js/editable-table.js` — legacy DataTables init on `#editable-sample` confirmed
- PLANNING — `.planning/phases/03-admin-design-system/03-UI-SPEC.md` — authoritative design contract
- PLANNING — `.planning/codebase/CONVENTIONS.md` — architectural patterns, model query conventions
- PLANNING — `.planning/codebase/STACK.md` — library versions and load paths

### Secondary (MEDIUM confidence)

- PLANNING — `.planning/REQUIREMENTS.md` — UI-01 through UI-04 requirement definitions
- PLANNING — `.planning/ROADMAP.md` — Phase 3 success criteria

---

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH — all libraries verified as vendored in repo, loaded in footer.php
- Architecture patterns: HIGH — extracted from working conforming views in codebase
- Conformance audit counts: HIGH — direct grep across full module tree
- Pitfalls: HIGH — extracted from actual code issues found during audit
- CSRF risk analysis: HIGH — based on CI3 CSRF documentation behavior and direct config file read

**Research date:** 2026-04-27
**Valid until:** 2026-05-27 (30 days — stable brownfield project, no expected library changes)
