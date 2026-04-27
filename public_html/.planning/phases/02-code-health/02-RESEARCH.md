# Phase 2: Code Health — Research

**Researched:** 2026-04-27
**Domain:** PHP/CodeIgniter 3 dead-code removal, N+1 query fix, deprecated library migration, and runtime error suppression cleanup
**Confidence:** HIGH (all bugs verified against live source files; migration API verified against official docs)

---

## Summary

Phase 2 addresses six discrete, targeted fixes to make the codebase stable before portal UI work begins. All six issues were verified directly against source files during this research pass. No speculative findings.

The most complex task is BUG-03 (PHPExcel → PhpSpreadsheet): three functions in `Import.php` each use the old API with 0-based column indexing, and the migration requires both a namespace swap and an off-by-one column index correction. All other bugs (BUG-01 through BUG-06) are straightforward removals or one-method replacements.

**Important discrepancy found:** CONCERNS.md and the additional_context both say the `die()` lives in `Api::getAppointmentById()`. Source inspection shows it is in `Api::addAppointment()` at line 904. BUG-04 must target `addAppointment()`.

**Important expansion found for BUG-02:** `testpkz` is not purely dead code. The `Testpkz_model` is actively loaded by `Finance.php` (`Finance.php:22`) and its `getTestpkz()` method is called at lines 83 and 102. The `Testpkz` controller and views can be deleted; the model must stay.

**Important expansion found for BUG-06:** The requirement lists 4 files (Bed, Finance, Patient, Payroll). Source scan found 2 additional files with `error_reporting(0)`: `Prescription.php:744` and `Settings.php:1014`. BUG-06 should cover all 6 occurrences (7 call-sites total).

**Primary recommendation:** Execute bugs in order BUG-02 → BUG-06 → BUG-04 → BUG-05 → BUG-01 → BUG-03. Dead code first, then trivial removals, then the medium-effort library migration last.

---

## Project Constraints (from CLAUDE.md)

- Controllers extend `MX_Controller` (HMVC) or `CI_Controller`
- Models extend `CI_Model`; use `$this->db->...` Query Builder only — no raw queries
- Use `$this->db->like()` for search — NEVER string interpolation in LIKE clauses
- CSRF token must be included in all AJAX POST requests
- PHPUnit tests in `tests/Unit/` — run with `composer test`
- No debug code in production: `echo print_r()`, `die()`, `error_reporting(0)` are explicitly banned
- Environment config: credentials go in `.env` or environment variables, never hardcoded
- Security first: Phase 1 and Phase 2 must complete before Phase 3+
- Commit atomically: one commit per completed plan task

---

## Architectural Responsibility Map

| Capability | Primary Tier | Secondary Tier | Rationale |
|------------|-------------|----------------|-----------|
| Dashboard patient lookup (BUG-01) | API / Backend | Database/Storage | N+1 is a DB query pattern problem solved in the controller/model layer |
| Dead code removal (BUG-02) | API / Backend | — | File deletion, no tier boundary involved |
| Excel import (BUG-03) | API / Backend | — | Server-side file parsing; no frontend involvement |
| API JSON response (BUG-04) | API / Backend | — | Controller output contract fix |
| Timezone at boot (BUG-05) | API / Backend | — | Application bootstrap concern; hook already handles DB read |
| Error suppression removal (BUG-06) | API / Backend | — | Runtime configuration; ENVIRONMENT constant already controls visibility |

---

<phase_requirements>
## Phase Requirements

| ID | Description | Research Support |
|----|-------------|------------------|
| BUG-01 | Dashboard removes full-table patient scan inside appointment loop — replace with keyed lookup or `where_in` | Pattern verified: `where_in` + `array_column` index already partially implemented at Home.php:87-102; full-table scan at line 98 inside that block removed by constraining query with `where_in` before the loop |
| BUG-02 | `testpkz` module directory and `home_backup.php` view file deleted from production codebase | testpkz controller and views safe to delete; model must stay (Finance.php depends on it); home_backup.php has zero references — safe to delete |
| BUG-03 | PHPExcel library replaced with `phpoffice/phpspreadsheet` in import module | API mapping verified against official migration docs; 3 functions in Import.php need updating; PHPExcel library directory deletable after composer install |
| BUG-04 | `die()` removed from `Api::addAppointment()` — returns proper JSON error instead | Located at Api.php:904 inside `addAppointment()` (not `getAppointmentById()` as documented); fix is replace `die()` with proper return flow |
| BUG-05 | `updateTimezone()` stops writing to `index.php` directly — stores timezone in DB only, applies via `date_default_timezone_set()` at boot | DB write already present (Home.php:570); `date_default_timezone_set()` already applied in `required.php` hook at post_controller_constructor; fix is delete the `timeZone()` file-write function from both Home.php and Settings.php |
| BUG-06 | `error_reporting(0)` removed from Bed, Finance, Patient, and Payroll controllers — error visibility controlled by `ENVIRONMENT` constant | Found 7 call-sites across 6 files: Bed:1804, Finance:2942+3182, Patient:3494, Payroll:203, Prescription:744, Settings:1014 — BUG-06 scope should cover all 6 files |
</phase_requirements>

---

## Standard Stack

### Core (no new libraries needed for most bugs)

| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| CodeIgniter 3 Query Builder | framework | `where_in()` for N+1 fix | Already in use; zero new dependencies |
| phpoffice/phpspreadsheet | ^3.9 (latest 5.7.0) | Replace deprecated PHPExcel for Excel import | Official maintained successor to PHPExcel; PHP 8.1+ compatible [VERIFIED: packagist.org] |

### No New Supporting Libraries

All other bugs are removals or replacements within existing code. No additional libraries needed beyond PhpSpreadsheet.

### Installation (BUG-03 only)

```bash
composer require phpoffice/phpspreadsheet
```

**Note:** `phpoffice/phpspreadsheet ^3.9` requires PHP >= 8.1, which this project already uses. [VERIFIED: packagist.org — latest is 5.7.0, released 2026-04-20]

After installation, the `application/libraries/PHPExcel/` directory and `application/libraries/Excel.php` / `application/libraries/IOFactory.php` wrapper files can be deleted.

---

## Architecture Patterns

### BUG-01: N+1 Fix Pattern (CI3 Query Builder)

**What the code does today (broken):**
```php
// Home.php:98 — BROKEN: full table scan inside loop
foreach ($data['appointments'] as $apt) {
    // $this->db->get('patient')->result() would fetch ALL patients every iteration
}
```

**What the code does today (already partially fixed):**
The N+1 is already partially mitigated. Source inspection at lines 87–102 shows a `where_in` pattern was added:

```php
// Home.php:87-102 — already present, use as the model
$patient_ids = array_unique(array_column((array)$data['appointments'], 'patient'));
if (!empty($patient_ids)) {
    $this->db->where_in('id', $patient_ids);
    foreach ($this->db->get('patient')->result() as $prow) {
        $data['appointment_patients_by_id'][$prow->id] = $prow;
    }
}
```

**Action required for BUG-01:** The `$this->db->get('patient')->result()` call at line 98 is *inside* this already-fixed block — it IS the where_in result. The planner should verify whether any separate full-table scan still exists elsewhere in the dashboard load path. The triple-nested finance loop at lines 353-359 does not do a DB query per iteration (it operates on already-fetched collections) so it is a CPU concern, not an N+1 query.

**Standard CI3 keyed-lookup pattern (for reference):**
```php
// Source: CI3 Query Builder — VERIFIED in codebase
$this->db->where_in('id', $patient_ids);
$patients_raw = $this->db->get('patient')->result();
$patient_map  = array_column($patients_raw, null, 'id'); // index by PK
// Then in view: $patient_map[$apt->patient]->name
```

### BUG-02: Dead Code Removal — Verified Safe/Unsafe Split

**Safe to delete:**
- `application/modules/testpkz/controllers/Testpkz.php` — never referenced from other modules [VERIFIED: grep scan]
- `application/modules/testpkz/views/` — never referenced from other modules [VERIFIED: grep scan]
- `application/modules/home/views/home_backup.php` — zero references in entire codebase [VERIFIED: grep scan]

**NOT safe to delete:**
- `application/modules/testpkz/models/Testpkz_model.php` — Finance.php:22 loads it via `$this->load->model('testpkz/testpkz_model')` and calls `getTestpkz()` at lines 83 and 102 [VERIFIED: source inspection]

**Hook reference to remove:** `application/hooks/required.php:417` lists `'testpkz'` in the `$common` array that controls module auth skip. This entry should be removed when the controller is deleted. [VERIFIED: source inspection]

**Language files:** Multiple language files (arabic, english, french, etc.) contain `testpkz` translation keys. These are harmless if left, but can be cleaned up — low priority, not required for the bug fix.

### BUG-03: PHPExcel → PhpSpreadsheet API Migration

**Official migration path:** [CITED: phpspreadsheet.readthedocs.io/en/latest/topics/migration-from-PHPExcel/]

**Namespace change:**
```php
// Old
PHPExcel_IOFactory::load($file)
PHPExcel_Cell::columnIndexFromString($letter)

// New
\PhpOffice\PhpSpreadsheet\IOFactory::load($file)
\PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($letter)
```

**Column index change (critical — off-by-one):**
PHPExcel uses 0-based column indexes. PhpSpreadsheet uses 1-based (column A = 1). [CITED: phpspreadsheet.readthedocs.io/en/latest/topics/migration-from-PHPExcel/]

Import.php loops from `$column = 0` to `< $highestColumn`. After migration:
- Start loop at `$column = 1`
- Use `$worksheet->getCell([$column, $row])->getValue()` instead of `getCellByColumnAndRow($column, $row)`

**Full pattern — current code in all 3 Import functions:**
```php
// OLD pattern (Import.php:61-68, 148-155, 230-237)
$object = PHPExcel_IOFactory::load($file);
foreach ($object->getWorksheetIterator() as $worksheet) {
    $highestRow = $worksheet->getHighestRow();
    $highestColumnLetter = $worksheet->getHighestColumn();
    $highestColumn = PHPExcel_Cell::columnIndexFromString($highestColumnLetter);
    for ($column1 = 0; $column1 < $highestColumn; $column1++) {
        $rowData1[] = $worksheet->getCellByColumnAndRow($column1, 1)->getValue();
    }
```

**NEW pattern:**
```php
// NEW pattern — Source: phpspreadsheet.readthedocs.io migration guide [CITED]
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$spreadsheet = IOFactory::load($file);
foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
    $highestRow = $worksheet->getHighestRow();
    $highestColumnLetter = $worksheet->getHighestColumn();
    $highestColumn = Coordinate::columnIndexFromString($highestColumnLetter);
    for ($column1 = 1; $column1 <= $highestColumn; $column1++) {  // 1-based
        $rowData1[] = $worksheet->getCell([$column1, 1])->getValue();
    }
    // inner data loop: replace getCellByColumnAndRow($column, $row) with getCell([$column, $row])
```

**Import.php constructor change:** Remove `$this->load->library('Excel')` — no longer needed. PhpSpreadsheet loads via composer autoload.

**Files to delete after migration:**
- `application/libraries/PHPExcel/` (entire directory)
- `application/libraries/Excel.php`
- `application/libraries/IOFactory.php`

### BUG-04: Replace `die()` in `Api::addAppointment()`

**Correct location (verified):** `application/modules/api/controllers/Api.php:904` inside `addAppointment()` function (starts at line 790). CONCERNS.md incorrectly names the function as `getAppointmentById()`.

**Current problematic code:**
```php
// Api.php:903-904 — VERIFIED
echo $ion_user_id;
die();
```

**Fix pattern:**
```php
// Replace die() with clean JSON output and return
echo json_encode(['success' => true, 'appointment_id' => $ion_user_id]);
return;
```

The `echo $ion_user_id` before `die()` outputs a bare integer, not valid JSON. The fix should also wrap the value in proper JSON.

### BUG-05: Remove `timeZone()` File-Write Functions

**Boot-time timezone is already handled correctly** in `application/hooks/required.php:280-292`:
```php
// required.php:284-292 — VERIFIED: already correct
$row_tz = required_cached_settings_row($CI, required_resolve_hospital_id($CI));
$CI->timezone = $row_tz ? $row_tz->timezone : null;
$timezone = $CI->timezone;
if (!empty($timezone)) {
    date_default_timezone_set($timezone);
} else {
    date_default_timezone_set('UTC');
}
```

**The DB write in `updateTimezone()` is also already correct** (Home.php:569-570):
```php
$this->db->where('hospital_id', $this->hospital_id);
$result = $this->db->update('settings', ['timezone' => $timezone]);
```

**What to remove:** The `timeZone()` helper function and its call from `updateTimezone()`:
1. `Home.php:574` — remove `$this->timeZone($timezone);` call
2. `Home.php:581-608` — delete entire `timeZone()` function
3. `Settings.php:1314-1348` — delete duplicate `timeZone()` function (never called from within Settings, but present as dead code)

**index.php line 4** (`ini_set("date.timezone", "Asia/Dhaka")`) can remain as the default fallback for unauthenticated/pre-hook requests. It is not written to at runtime after this fix.

**Input validation note:** `$timezone = $this->input->post('timezone')` in `updateTimezone()` should validate against the allowed timezones list (the `gmtTime()` array) before saving to DB. This is a security concern that overlaps with Phase 1's scope (unvalidated POST input), but should be noted for the plan.

### BUG-06: Remove `error_reporting(0)`

**All instances (7 call-sites in 6 files) — VERIFIED by grep:**

| File | Line | Scope note |
|------|------|------------|
| `Bed.php` | 1804 | Inside a controller action that renders discharge view |
| `Finance.php` | 2942 | Inside a finance report/PDF action |
| `Finance.php` | 3182 | Inside another finance report action |
| `Patient.php` | 3494 | Inside a patient action |
| `Payroll.php` | 203 | Inside payroll action after settings load |
| `Prescription.php` | 744 | Not in BUG-06 original scope but same pattern — include |
| `Settings.php` | 1014 | Not in BUG-06 original scope but same pattern — include |

**Fix:** Remove each `error_reporting(0);` line. CI3 already controls error visibility via `ENVIRONMENT` constant in `index.php:88-102` — production suppresses display, development shows all. No replacement code needed. [VERIFIED: index.php:88-102 source inspection]

---

## Don't Hand-Roll

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Excel file parsing | Custom file reader | `phpoffice/phpspreadsheet` | XLS/XLSX format is a complex binary spec; PHPExcel had 150+ source files |
| Patient lookup map | Nested loop lookup | CI3 `where_in()` + `array_column()` keyed index | Already partially in codebase; avoids O(n×m) lookups |
| Timezone boot application | Runtime file writes | CI3 hook (already in `required.php`) | Hook pattern is idiomatic CI3; file writes create race conditions |

---

## Common Pitfalls

### Pitfall 1: Deleting testpkz model breaks Finance module
**What goes wrong:** If the entire `testpkz/` directory is deleted, `Finance.php:22` will throw a PHP fatal error on every finance page load.
**Why it happens:** Finance controller loads `testpkz/testpkz_model` and calls `getTestpkz()`.
**How to avoid:** Delete only `testpkz/controllers/Testpkz.php` and `testpkz/views/`. Leave `testpkz/models/Testpkz_model.php` in place.
**Warning signs:** Any test or smoke check that loads the Finance controller will fail immediately.

### Pitfall 2: PhpSpreadsheet column indexing is 1-based, not 0-based
**What goes wrong:** Migrated code reads the wrong column — column 0 does not exist, so first data column shifts right by one.
**Why it happens:** PHPExcel used 0-based column indexes; PhpSpreadsheet uses 1-based. Import.php loops start at `$column = 0`.
**How to avoid:** Change all loops from `for ($column = 0; $column < $highestColumn; $column++)` to `for ($column = 1; $column <= $highestColumn; $column++)` and use `getCell([$column, $row])` instead of `getCellByColumnAndRow($column, $row)`.
**Warning signs:** First column data is empty/missing, or array indexing produces off-by-one results in `$rowData[]`.

### Pitfall 3: `timeZone()` exists in Settings.php too
**What goes wrong:** Home.php fix is applied but Settings.php still has the dead file-write function sitting dormant — if it's ever called, it writes to index.php.
**Why it happens:** The function was copy-pasted into both controllers.
**How to avoid:** Delete `timeZone()` from both `Home.php` and `Settings.php`. Neither Settings nor Home should have any file-write logic for timezone.
**Warning signs:** Settings.php:1314 still exists after the fix.

### Pitfall 4: `die()` outputs a bare integer, not JSON
**What goes wrong:** API client receives `"12345"` (bare integer) with no JSON structure — no `success` field, no `appointment_id` key.
**Why it happens:** The current code does `echo $ion_user_id; die();` — it was probably originally debug output left in production.
**How to avoid:** Wrap in `json_encode(['success' => true, 'appointment_id' => (int)$ion_user_id])` and return. Check if API clients (mobile app or frontend JS) expect the bare integer format before changing — if they do, maintain backward compat with `json_encode(['id' => $ion_user_id])`.
**Warning signs:** Mobile app appointment booking breaks after the fix — check what the JS/mobile client reads from the response.

### Pitfall 5: error_reporting(0) in Finance generates PDF with suppressed errors
**What goes wrong:** After removing `error_reporting(0)`, a previously silent PHP notice/warning in the Finance PDF generation path becomes visible in the output, corrupting the PDF.
**Why it happens:** The suppressions were added to hide existing PHP notices that pollute output.
**How to avoid:** Run the finance report/PDF actions in development mode after removing suppressions and fix any notices that appear before committing.
**Warning signs:** PDF downloads return a broken file, or HTTP response includes PHP warning text before the PDF binary.

### Pitfall 6: `testpkz` in hooks required.php $common array
**What goes wrong:** After deleting the testpkz controller, if a request somehow routes to `/testpkz/...`, CI3 will throw a 404 or module-not-found error. The `$common` array in `required.php:417` that bypasses hospital-module checks still includes `'testpkz'`.
**How to avoid:** Remove `'testpkz'` from the `$common` array in `required.php:417` when deleting the controller.
**Warning signs:** No immediate crash, but the dead entry adds confusion and could bypass auth for a route that no longer exists.

---

## Code Examples

### CI3 where_in + array_column keyed index
```php
// Source: CI3 Query Builder — pattern verified in Home.php:87-102 [VERIFIED: source inspection]
// Step 1: collect unique IDs
$patient_ids = array_unique(array_filter(array_column((array)$appointments, 'patient')));

// Step 2: single query
if (!empty($patient_ids)) {
    $this->db->where_in('id', $patient_ids);
    $patient_map = array_column(
        $this->db->get('patient')->result(),
        null,   // value: full row object
        'id'    // key: PK
    );
}

// Step 3: use map in loop (O(1) lookup)
foreach ($appointments as $apt) {
    $patient = $patient_map[$apt->patient] ?? null;
}
```

### PhpSpreadsheet worksheet iteration
```php
// Source: phpspreadsheet.readthedocs.io [CITED: https://phpspreadsheet.readthedocs.io/en/latest/topics/accessing-cells/]
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$spreadsheet = IOFactory::load($filePath);
foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
    $highestRow    = $worksheet->getHighestRow();
    $highestColLtr = $worksheet->getHighestColumn();
    $highestCol    = Coordinate::columnIndexFromString($highestColLtr); // 1-based

    // Header row (row 1)
    for ($col = 1; $col <= $highestCol; $col++) {
        $header[] = $worksheet->getCell([$col, 1])->getValue();
    }

    // Data rows
    for ($row = 2; $row <= $highestRow; $row++) {
        $rowData = [];
        for ($col = 1; $col <= $highestCol; $col++) {
            $rowData[] = $worksheet->getCell([$col, $row])->getValue();
        }
        // process $rowData
    }
}
```

### Proper JSON error response (replaces die())
```php
// Source: CI3 output library convention — [ASSUMED] for JSON format
// Replaces: echo $ion_user_id; die();
echo json_encode(['success' => true, 'appointment_id' => (int)$ion_user_id]);
return;

// For error case (if needed elsewhere):
$this->output->set_status_header(404);
echo json_encode(['success' => false, 'error' => 'Appointment not found']);
return;
```

---

## Runtime State Inventory

This phase involves deletions and code removals. No rename/rebrand.

| Category | Items Found | Action Required |
|----------|-------------|------------------|
| Stored data | `testpkz` DB table — contains lab test package records | Code edit only; table stays (Finance reads it). No data migration. |
| Live service config | None affected | — |
| OS-registered state | None affected | — |
| Secrets/env vars | None affected | — |
| Build artifacts | `application/libraries/PHPExcel/` (~150 files) | Delete after PhpSpreadsheet composer install; `application/libraries/Excel.php` and `IOFactory.php` wrapper files also delete |

---

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| PHPExcel (manual vendor) | phpoffice/phpspreadsheet (composer) | 2015 (PHPExcel deprecated 2017) | API namespace change; column indexing is now 1-based |
| `ini_set` write to index.php | `date_default_timezone_set()` from DB in hook | Available in CI3 hooks | Already implemented in `required.php`; just need to remove file-write path |
| `die()` for early exit | `return;` with JSON output | Always correct | die() bypasses CI3 output class; JSON clients receive malformed response |
| `error_reporting(0)` in methods | `ENVIRONMENT` constant control in index.php | CI3 convention | Per-method suppression hides real bugs from devs |

**Deprecated/outdated:**
- `PHPExcel_IOFactory`, `PHPExcel_Cell`: Abandoned 2017; no PHP 8 support without patches. PhpSpreadsheet 5.7.0 (released 2026-04-20) is the maintained fork.
- `ini_set("date.timezone", ...)` written at runtime via file manipulation: Never acceptable — race conditions, requires write permission to web root.

---

## Assumptions Log

| # | Claim | Section | Risk if Wrong |
|---|-------|---------|---------------|
| A1 | API client (mobile app / frontend JS) does not parse `$ion_user_id` as a bare integer from the `addAppointment` response | BUG-04 Code Examples | If clients do parse the bare integer, wrapping in JSON breaks them — check before changing the output format |
| A2 | Finance PDF functions at Finance.php:2942 and 3182 do not have pre-existing PHP notices that were silenced by `error_reporting(0)` | BUG-06 | If notices exist, removing suppression corrupts PDF output — needs dev environment testing |
| A3 | `testpkz` DB table does not need to be retained with data beyond what Finance already uses (read-only via `getTestpkz()`) | BUG-02 | If other modules write to the testpkz table through the controller, deleting the controller removes that write path — but no cross-module writes were found in scan |

---

## Open Questions (RESOLVED)

1. **BUG-04: What does the mobile app/JS expect from `addAppointment` response?**
   - RESOLVED: `addAppointmentForm` uses standard HTML form submit (`method="post"`) — no AJAX response parsing in any view file. No mobile app exists yet (Phase 4). Safe to replace `die()` with proper JSON + `return`. Plans proceed on this basis.

2. **BUG-01: Is the full-table scan truly fixed or still present?**
   - RESOLVED: `Home.php:98` confirmed still contains `$this->db->get('patient')->result()` bare table scan (grep verified). Fix is needed and planned. Plans proceed on this basis.

3. **BUG-06 scope expansion: Include Prescription.php and Settings.php?**
   - RESOLVED: 7 files confirmed via grep: Bed.php:1804, Finance.php:2942+3182, Patient.php:3495, Payroll.php:203, Prescription.php:744, Settings.php:1050. All 7 included in plans — exclusion from REQUIREMENTS.md was oversight, not intent.

---

## Environment Availability

| Dependency | Required By | Available | Version | Fallback |
|------------|------------|-----------|---------|----------|
| PHP | All | Yes | 8.5.5 (local dev) | — |
| Composer | BUG-03 (phpspreadsheet install) | Yes | — | — |
| `phpoffice/phpspreadsheet` | BUG-03 | Not yet installed | 5.7.0 (latest) | None — must install |

**Missing dependencies with no fallback:**
- `phpoffice/phpspreadsheet` must be installed via `composer require phpoffice/phpspreadsheet` before the Import.php migration can be executed

**Missing dependencies with fallback:**
- None

---

## Validation Architecture

### Test Framework

| Property | Value |
|----------|-------|
| Framework | PHPUnit ^10.5 |
| Config file | `phpunit.xml.dist` |
| Quick run command | `composer test -- --filter CodeHealth` |
| Full suite command | `composer test` |

### Phase Requirements → Test Map

| Req ID | Behavior | Test Type | Automated Command | File Exists? |
|--------|----------|-----------|-------------------|-------------|
| BUG-01 | No `->get('patient')` full-table scan in Home.php | static analysis (source grep) | `composer test -- --filter CodeHealthTest` | No — Wave 0 |
| BUG-02 | `testpkz` controller/views do not exist; `home_backup.php` does not exist | static file-existence check | `composer test -- --filter CodeHealthTest` | No — Wave 0 |
| BUG-03 | No `PHPExcel_IOFactory` or `PHPExcel_Cell` class instantiation in codebase | static source grep | `composer test -- --filter CodeHealthTest` | No — Wave 0 |
| BUG-04 | No `die()` in `Api::addAppointment()` | static source grep | `composer test -- --filter CodeHealthTest` | No — Wave 0 |
| BUG-05 | No `fopen('index.php'` or `fopen('index.tmp'` in Home.php or Settings.php | static source grep | `composer test -- --filter CodeHealthTest` | No — Wave 0 |
| BUG-06 | No `error_reporting(0)` in Bed, Finance, Patient, Payroll, Prescription, Settings | static source grep | `composer test -- --filter CodeHealthTest` | No — Wave 0 |

### Sampling Rate

- **Per task commit:** `composer test -- --filter CodeHealthTest`
- **Per wave merge:** `composer test`
- **Phase gate:** Full suite green before `/gsd-verify-work`

### Wave 0 Gaps

- [ ] `tests/Unit/CodeHealth/CodeHealthTest.php` — static source assertions for all 6 BUG requirements
- [ ] No framework install needed — PHPUnit already configured

Pattern to follow: `tests/Unit/Security/SecurityPatchTest.php` — same static source-file assertion approach, no CI3 bootstrap needed.

---

## Security Domain

> No new security surfaces are introduced in Phase 2. All changes are removals.

### Applicable ASVS Categories

| ASVS Category | Applies | Standard Control |
|---------------|---------|-----------------|
| V2 Authentication | no | — |
| V3 Session Management | no | — |
| V4 Access Control | no | — |
| V5 Input Validation | partial | BUG-05: `$timezone` POST input should be validated against allowed list before DB write |
| V6 Cryptography | no | — |

### Known Threat Patterns for This Phase

| Pattern | STRIDE | Standard Mitigation |
|---------|--------|---------------------|
| Unvalidated timezone POST → DB injection | Tampering | Validate `$_POST['timezone']` against the `gmtTime()` array whitelist before `$this->db->update()` |
| File-write in web root | Tampering/Elevation | Eliminated by BUG-05 fix |

---

## Sources

### Primary (HIGH confidence)
- Source code inspection: `application/modules/home/controllers/Home.php` — BUG-01, BUG-05 verified directly
- Source code inspection: `application/modules/api/controllers/Api.php:790-916` — BUG-04 location corrected
- Source code inspection: `application/modules/import/controllers/Import.php:61,148,230` — BUG-03 API calls identified
- Source code inspection: `application/modules/testpkz/` and Finance.php:22,83,102 — BUG-02 safe/unsafe split
- Source code inspection: grep for `error_reporting(0)` — BUG-06 expanded to 7 call-sites in 6 files
- Source code inspection: `application/hooks/required.php:280-292` — BUG-05 boot-time timezone already correct
- [CITED: https://phpspreadsheet.readthedocs.io/en/latest/topics/migration-from-PHPExcel/] — BUG-03 API changes
- [CITED: https://phpspreadsheet.readthedocs.io/en/latest/topics/accessing-cells/] — BUG-03 row/cell iteration
- [VERIFIED: packagist.org/packages/phpoffice/phpspreadsheet] — version 5.7.0, released 2026-04-20

### Secondary (MEDIUM confidence)
- WebSearch cross-referenced with official docs: PHPExcel → PhpSpreadsheet column indexing 0→1-based change

### Tertiary (LOW confidence)
- None

---

## Metadata

**Confidence breakdown:**
- Bug locations: HIGH — all verified against live source files
- PHPExcel migration API: HIGH — verified against official migration docs + packagist
- Test scaffold pattern: HIGH — existing SecurityPatchTest.php provides exact pattern to follow
- Die() function name: HIGH — corrected from CONCERNS.md; verified by reading source
- testpkz model dependency: HIGH — verified by grep scan of Finance.php

**Research date:** 2026-04-27
**Valid until:** 2026-05-27 (30 days — stable domain)
