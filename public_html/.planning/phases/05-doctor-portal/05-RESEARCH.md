# Phase 5: Doctor Portal — Research

**Researched:** 2026-04-27
**Domain:** CodeIgniter 3 HMVC, AdminLTE 3, Chart.js 3.9.1, MySQL
**Confidence:** HIGH

---

<phase_requirements>
## Phase Requirements

| ID | Description | Research Support |
|----|-------------|------------------|
| UI-09 | Doctor dashboard top cards: total patients today, checked-in count, pending count, today's revenue | 3 of 4 stat vars already in controller; `today_revenue` query pattern proven in `revenue()` method |
| UI-10 | Doctor dashboard: live queue panel (long-poll), right panel (follow-up count + high-risk list), bottom charts | `queue_json` endpoint is MISSING — must be added; `patient_practice_tag` table exists for tag queries; Chart.js 3.9.1 CDN already in codebase |
| UI-11 | Doctor consultation room: left panel (search by date or ID), right panel (e-pad iframe), save+print action bar | iframe pattern already exists; radio-pill toggle and action bar are the two net-new UI pieces |
</phase_requirements>

---

## Summary

Phase 5 is a brownfield completion phase. The `doctor_chamber` module has a fully functional controller and both target views already exist as working skeletons. The task is not to build from scratch but to close the gap between the current skeleton and the UI-SPEC contract defined in `05-UI-SPEC.md`.

The dashboard (`doctor/dashboard.php`) has all four stat card slots but is missing: (a) the `$today_revenue` stat card and supporting controller query, (b) the two-column layout split (queue left, right panel), (c) long-poll JS for live queue updates, (d) the follow-up/high-risk right panel, and (e) the two bottom Chart.js graphs. The controller `dashboard()` method is also missing variables for `$today_revenue`, `$followup_count`, and `$high_risk_patients`, and the three JSON endpoints `queue_json`, `chart_data_json`, and `search_json` do not yet exist in the controller.

The consultation room (`doctor/consultation_room.php`) has a working iframe e-pad, drug search autocomplete, vitals long-poll, favorites, and triage display. What is missing is the radio-pill search mode toggle (currently a plain GET form), the `col-md-5/col-md-7` layout split (currently 50/50), the `#searchResultsList` AJAX results area, the template selector dropdown (`$rx_templates`), the structured action bar with "Save draft" and "Save and print" buttons, and the prescription date format fix (view renders `date('d-m-Y', $rx->date)` while the DB column is an integer Unix timestamp, which is correct — this just needs verification).

**Primary recommendation:** Implement in three waves: (1) controller additions (stat vars + three new JSON endpoints), (2) dashboard view redesign to match UI-SPEC layout, (3) consultation room view upgrades (radio toolbar, layout split, action bar, template dropdown).

---

## Architectural Responsibility Map

| Capability | Primary Tier | Secondary Tier | Rationale |
|------------|-------------|----------------|-----------|
| Stat card data (`$total_today`, `$checked_in`, `$pending`, `$today_revenue`) | API/Backend (controller) | — | PHP queries at page-load; no async needed |
| Live queue table | API/Backend (controller adds `queue_json`) | Browser (long-poll JS) | 12-second poll — shared hosting constraint |
| Follow-up count + high-risk list | API/Backend (controller adds vars) | — | Server-rendered at page load; not live-updated |
| Monthly revenue/patients charts | Browser (Chart.js) | API/Backend (`chart_data_json` endpoint) | Chart renders client-side; data fetched via AJAX |
| E-pad prescription form | API/Backend (`prescription` module, iframe) | Browser | Existing module — Phase 5 only wraps the iframe |
| Patient history search | Browser (AJAX toggle) | API/Backend (`search_json` endpoint) | Mode toggle is pure JS; results fetched from server |
| Vitals long-poll | API/Backend (`vitals_json` — already exists) | Browser | Already implemented — no change needed |
| Drug search autocomplete | API/Backend (`drug_search_json` — already exists) | Browser | Already implemented — no change needed |

---

## Standard Stack

### Core

| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| CodeIgniter 3 + MX | (vendored) | HMVC routing, Query Builder | Entire codebase; all controllers extend `MX_Controller` |
| AdminLTE 3 + Bootstrap 4 | (vendored) | Grid, components, layout | Project-wide admin UI framework |
| jQuery | (bundled in adminlte/plugins) | AJAX, DOM, event handling | Required by AdminLTE; used throughout all views |
| Chart.js | 3.9.1 (CDN-pinned) | Bar charts for revenue/patient trends | Already in codebase (`dashboard/views/clinical.php`) |
| FontAwesome Free 5 | (bundled in adminlte/plugins) | Icons for stat cards and panels | Project-wide icon library |

### Supporting

| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| `chamber-practice.css` | (project file) | Chamber-specific CSS tokens and components | All `doctor_chamber` views — already loaded |
| `app-design-tokens.css` | (project file) | `--ap-*` base tokens (Phase 3) | Referenced by `--chamber-*` variables in chamber CSS |
| mPDF | (Composer) | PDF generation for prescriptions | Used by existing `prescription` module only — Phase 5 does not add new PDF generation |

**No new packages required.** All libraries for Phase 5 are already present in the codebase.

---

## Architecture Patterns

### System Architecture Diagram

```
Doctor browser
    │
    ├─ Page load ──► Doctor_chamber::dashboard()
    │                    │
    │                    ├─ appointment COUNT (today, this doctor)
    │                    ├─ chamber_serial_queue COUNT (checked_in, pending)
    │                    ├─ payment SUM (today_revenue)         ← NET NEW
    │                    ├─ patient_practice_tag COUNT (followup_count)  ← NET NEW
    │                    └─ patient_practice_tag JOIN queue (high_risk_patients)  ← NET NEW
    │                    ↓
    │                dashboard.php view (stat grid + 2-col layout + chart canvases)
    │
    ├─ JS setInterval 12s ──► Doctor_chamber::queue_json()  ← NET NEW
    │                              └─ chamber_serial_queue JOIN doctor_chamber
    │                              └─ returns { rows: [...] }
    │                              └─ JS injects into #liveQueueBody
    │
    ├─ JS init (one-shot) ──► Doctor_chamber::chart_data_json()  ← NET NEW
    │                              └─ payment SUM GROUP BY month (last 6 months)
    │                              └─ appointment COUNT GROUP BY month (last 6 months)
    │                              └─ returns { revenue: [...], patients: [...], labels: [...] }
    │                              └─ Chart.js renders two bar charts
    │
    └─ Page load ──► Doctor_chamber::consultation_room()
                         │
                         ├─ GET ?patient=N ──► patient lookup, triage, vitals, tags, past_rx
                         ├─ prescription_favorite (favorites)
                         └─ NO rx_templates fetch yet (net new: query prescription_print_template)
                         ↓
                     consultation_room.php view (toolbar + 5/7 split + e-pad iframe + action bar)
                         │
                         ├─ #btnSearchPatient click ──► Doctor_chamber::search_json()  ← NET NEW
                         │       └─ mode=id: patient WHERE id=N
                         │       └─ mode=date: patient JOIN prescription WHERE date range
                         │
                         ├─ JS setInterval 8s ──► Doctor_chamber::vitals_json()  (ALREADY EXISTS)
                         │
                         └─ #drugq keyup ──► Doctor_chamber::drug_search_json()  (ALREADY EXISTS)
```

### Recommended Project Structure

No new directories needed. All work goes into the existing module:

```
application/modules/doctor_chamber/
├── controllers/
│   └── Doctor_chamber.php       — add queue_json(), chart_data_json(), search_json();
│                                    add revenue/followup/high-risk vars to dashboard()
├── views/doctor/
│   ├── dashboard.php            — full redesign to match UI-SPEC layout
│   └── consultation_room.php   — radio toolbar + 5/7 split + action bar + template dropdown
```

### Pattern 1: AJAX JSON Endpoint (CI3 controller method)

**What:** A public controller method that reads GET params, queries DB, returns `json_encode`.
**When to use:** All three net-new endpoints (`queue_json`, `chart_data_json`, `search_json`).

```php
// Source: [VERIFIED: existing Doctor_chamber.php::drug_search_json() at line 434]
public function queue_json()
{
    $doc = $this->currentDoctor();
    $today = date('Y-m-d');
    $this->db->select('q.serial_number, q.patient_id, q.guest_name, q.status, q.triage_json, c.name AS chamber_name');
    $this->db->from('chamber_serial_queue q');
    $this->db->join('doctor_chamber c', 'c.id = q.chamber_id', 'left');
    $this->db->where('q.doctor_id', $doc->id);
    $this->db->where('q.hospital_id', $doc->hospital_id);
    $this->db->where('q.queue_date', $today);
    $this->db->where_not_in('q.status', array('done', 'cancelled'));
    $this->db->order_by('q.sort_position', 'asc');
    $rows = $this->db->get()->result();
    $out = array();
    foreach ($rows as $r) {
        $triage = !empty($r->triage_json) ? @json_decode($r->triage_json, true) : array();
        $out[] = array(
            'serial_number' => (int) $r->serial_number,
            'patient_id'    => $r->patient_id ? (int) $r->patient_id : null,
            'guest_name'    => (string) $r->guest_name,
            'chamber_name'  => (string) $r->chamber_name,
            'status'        => (string) $r->status,
            'triage_summary' => is_array($triage)
                ? trim(($triage['symptom'] ?? '') . ' ' . (isset($triage['duration']) ? '(' . $triage['duration'] . ')' : ''))
                : '',
        );
    }
    $this->output->set_content_type('application/json')
                 ->set_output(json_encode(array('rows' => $out)));
}
```

### Pattern 2: Today's Revenue Query

**What:** SUM of `doctor_amount` from `payment` for today using `date_string` column.
**Critical detail:** The `payment.date_string` column uses `'d-m-Y'` format in `Doctor_chamber::revenue()` (verified at line 133: `date('d-m-Y', strtotime($from))`). The Finance controller also writes `date('d-m-y', $date)` (2-digit year) at older sites, but the `revenue()` method already works with 4-digit format for comparison. Use `date('d-m-Y')` (4-digit year) and also try `date('d-m-y')` with an OR if needed, OR use the `date` (unix timestamp) column instead, which avoids the string format entirely.

```php
// Source: [VERIFIED: Doctor_chamber::revenue() lines 133-142]
// Safest approach — use unix timestamp column like getPaymentByDoctorDate() does
$this->db->select_sum('doctor_amount');
$this->db->where('hospital_id', $doc->hospital_id);
$this->db->where('doctor', $doc->id);
$this->db->where('date >=', strtotime('today'));
$this->db->where('date <', strtotime('tomorrow'));
$q = $this->db->get('payment')->row();
$today_revenue = ($q && $q->doctor_amount !== null) ? (float) $q->doctor_amount : 0.0;
```

### Pattern 3: Chart Data — Last 6 Months GROUP BY

**What:** Monthly aggregation for revenue and patient count charts.
**When to use:** `chart_data_json` endpoint.

```php
// Source: [ASSUMED — based on CI3 Query Builder pattern; verified Query Builder exists]
// Revenue: SUM per month
$rows = array();
for ($i = 5; $i >= 0; $i--) {
    $start = mktime(0, 0, 0, date('n') - $i, 1);
    $end   = mktime(0, 0, 0, date('n') - $i + 1, 1);
    $this->db->select_sum('doctor_amount');
    $this->db->where('hospital_id', $doc->hospital_id);
    $this->db->where('doctor', $doc->id);
    $this->db->where('date >=', $start);
    $this->db->where('date <', $end);
    $r = $this->db->get('payment')->row();
    $rows[] = array(
        'label'   => date('M', $start),
        'revenue' => ($r && $r->doctor_amount !== null) ? (float) $r->doctor_amount : 0.0,
    );
}
```

### Pattern 4: Follow-up and High-Risk Queries

**What:** Count and list of today's queue patients with `patient_practice_tag` flags.
**Tables involved:** `patient_practice_tag` (columns: `doctor_id`, `patient_id`, `tag` ENUM('high_risk','follow_up','vip')) and `chamber_serial_queue`.

```php
// Source: [VERIFIED: migration 20260419000007, patient_practice_tag schema, lines 132-147]
// Follow-up count — patients tagged follow_up who are in today's queue
$this->db->from('chamber_serial_queue q');
$this->db->join('patient_practice_tag t', 't.patient_id = q.patient_id AND t.doctor_id = q.doctor_id', 'inner');
$this->db->where('q.doctor_id', $doc->id);
$this->db->where('q.hospital_id', $doc->hospital_id);
$this->db->where('q.queue_date', date('Y-m-d'));
$this->db->where('t.tag', 'follow_up');
$this->db->where_not_in('q.status', array('done', 'cancelled'));
$followup_count = $this->db->count_all_results();

// High-risk list — patients tagged high_risk in today's queue, limit 10
$this->db->select('q.patient_id, q.guest_name');
$this->db->from('chamber_serial_queue q');
$this->db->join('patient_practice_tag t', 't.patient_id = q.patient_id AND t.doctor_id = q.doctor_id', 'inner');
$this->db->where('q.doctor_id', $doc->id);
$this->db->where('q.hospital_id', $doc->hospital_id);
$this->db->where('q.queue_date', date('Y-m-d'));
$this->db->where('t.tag', 'high_risk');
$this->db->where_not_in('q.status', array('done', 'cancelled'));
$this->db->limit(10);
$high_risk_patients = $this->db->get()->result();
```

### Pattern 5: Prescription Template Selector (`$rx_templates`)

**What:** The UI-SPEC shows a Templates dropdown in the e-pad panel header populated from `$rx_templates`. The current `consultation_room()` method does NOT fetch this — it must be added.
**Table:** `prescription_print_template` (columns: `doctor_id`, `header_html`, `footer_html`) — this stores print layout templates, not content templates. There is no separate "content template" table in the schema.

**Resolution:** The dropdown in the UI-SPEC uses `$rx_templates` (items with `id` and `label`). Since only `prescription_print_template` exists and it has no `label`, this either (a) maps to the single doctor template row as "My template", or (b) requires a new `prescription_content_template` table. Given the UI-SPEC references `tpl->label` and `tpl->id`, and the existing `prescription_favorite` table has `label` + medicine JSON, the most likely intended source is `prescription_favorite` rows rendered as "Templates". Mark as `[ASSUMED]` — see Assumptions Log.

### Anti-Patterns to Avoid

- **PHP rendering the queue table rows:** The UI-SPEC contract explicitly states queue rows are populated by long-poll JS. Do not render `$queue_today` into `<tbody>` — this was the old pattern and breaks the 12-second live update.
- **Using `date_string` string comparison for today's revenue:** The `date_string` column contains `'d-m-y'` (2-digit year) in some rows and `'d-m-Y'` (4-digit) in others due to different Finance.php write sites. Use `date >=` / `date <` with Unix timestamps instead (the `payment.date` column is INT Unix timestamp — verified via Finance.php line 200 `$date = time()`).
- **50/50 split in consultation room:** Current view uses `col-md-6` / `col-md-6`. UI-SPEC requires `col-md-5` (left, 40%) / `col-md-7` (right, 60%).
- **Stale button sizes on header actions:** Current dashboard buttons use `btn-primary` without `btn-sm`. UI-SPEC requires `btn btn-sm btn-primary`.
- **`$rx_count++ >= 5` limit:** Current consultation room shows only 5 past prescriptions. UI-SPEC caps at 8. Update the loop limit.
- **Drug search missing debounce:** Current `consultation_room.php` script fires on every keyup with no `clearTimeout` debounce. UI-SPEC requires 280ms debounce. The JS must be replaced entirely.

---

## Don't Hand-Roll

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Chart rendering | Manual SVG/canvas drawing | Chart.js 3.9.1 (already on CDN) | Already loaded in codebase; bar chart is 15 lines of config |
| HTML escaping in JS | Custom regex replace | `escHtml()` inline function (4 lines — already in UI-SPEC JS) | Prevents XSS in queue table injection |
| Debounced keyup | `setTimeout` reinvented | `clearTimeout` + `setTimeout` pattern (standard, in UI-SPEC) | Already proven in existing drug search; just needs timer var |
| Revenue aggregation | Custom date math | CI3 Query Builder with Unix timestamps | Already proven in `Doctor_chamber::revenue()` method |

---

## Current State Gap Analysis

### dashboard.php — Gaps vs UI-SPEC

| Item | Current State | Required (UI-SPEC) | Change Type |
|------|--------------|-------------------|-------------|
| Stat card 1 | `$appt_today` — "Appointments today" | `$total_today` — "Total patients today" | Rename var + label |
| Stat card 2 | `$queue_open` — "Open queue" | `$checked_in` — "Checked in" | Replace: `$queue_open` is wrong count (pending+arrived+serving); need `$queue_checked_in` |
| Stat card 3 | `$queue_pending` — "Pending arrivals" | `$pending` — "Pending" | Rename var + label |
| Stat card 4 | MISSING | `$today_revenue` — "Today's revenue" | Net new: query + card |
| Stat card icon order | calendar-day, users, hourglass-half, user-check | calendar-day, user-check, hourglass-half, coins | Reorder cards 2 and 4; change icon on card 4 |
| Header button sizes | `btn-primary` (full size) | `btn btn-sm btn-primary` | Add `btn-sm` to all 3 header buttons |
| Subtitle | "Today queue, checked-in patients, and chamber actions in one view." | "Today's queue, checked-in patients, and revenue at a glance." | Copy update |
| AI strip | Present (`.chamber-ai-strip`) | NOT in UI-SPEC | Remove |
| Quick-action links row | Present (Revenue, CRM, Chambers, Schedules) | NOT in UI-SPEC (belong in nav, not dashboard) | Remove |
| Queue table | Server-rendered from `$queue_today` | Long-poll JS via `queue_json` | Replace PHP loop with JS + `#liveQueueBody` |
| Queue panel column | Full width (no right panel) | `col-md-8` | Add Bootstrap grid wrapper |
| Right panel | MISSING | `col-md-4` — follow-up count + high-risk list | Net new |
| Bottom charts | MISSING | Two Chart.js bar charts in `col-md-6` each | Net new |
| `queueLastUpdated` span | MISSING | Present in queue panel header | Net new |

### consultation_room.php — Gaps vs UI-SPEC

| Item | Current State | Required (UI-SPEC) | Change Type |
|------|--------------|-------------------|-------------|
| Search toolbar | Single `<form method="get">` with number input | Radio pill toggle (ID / Date) + AJAX search (no page reload) | Full replacement |
| Panel columns | `col-md-6` / `col-md-6` | `col-md-5` / `col-md-7` | Grid class change |
| Left panel title | "History & context" | "Patient history" | Copy update |
| Previous RX limit | 5 items | 8 items | Change `>= 5` to `>= 8` |
| RX list format | Unordered list with PDF link | `d-flex` row with date + "View" button | Layout update |
| Patient tags | Works (`.chamber-status` classes) | Same — no change needed | None |
| Template dropdown | MISSING | Dropdown populated from `$rx_templates` | Net new (requires resolving what `$rx_templates` source is) |
| Action bar | MISSING | Save draft + Save and print buttons | Net new |
| Drug search debounce | None — fires on every keyup | 280ms debounce | JS update |
| Drug search `#drugq` placeholder | "Name, generic, or company" | "Name, generic, or company..." (3 dots) | Copy nit |
| iframe `id` attribute | None | `id="rxFrame"` | Add attribute |
| Favorites layout | `<ul>` with `<li>` per button | `.chamber-btn-row` with inline buttons | Layout update |

### controller — Missing Variables and Methods

| Item | Current | Required | Location |
|------|---------|---------|---------|
| `$today_revenue` | Missing | SUM from payment (unix timestamp today) | `dashboard()` |
| `$followup_count` | Missing | COUNT from tag join | `dashboard()` |
| `$high_risk_patients` | Missing | rows from tag join | `dashboard()` |
| Variable rename: `$appt_today` | Present | Rename to `$total_today` in `$data[]` | `dashboard()` |
| Variable rename: `$queue_checked_in` | Present | Rename to `$checked_in` in `$data[]` | `dashboard()` |
| Variable rename: `$queue_pending` | Present | Rename to `$pending` in `$data[]` | `dashboard()` |
| `queue_json()` | MISSING | New public method | `Doctor_chamber.php` |
| `chart_data_json()` | MISSING | New public method | `Doctor_chamber.php` |
| `search_json()` | MISSING | New public method | `Doctor_chamber.php` |
| `$rx_templates` in consultation | Missing | Requires decision on source table | `consultation_room()` |

---

## Common Pitfalls

### Pitfall 1: date_string Format Inconsistency
**What goes wrong:** The `payment.date_string` column has rows written with `'d-m-y'` (2-digit year, e.g. "27-04-26") from older Finance.php code, AND rows written with `'d-m-Y'` (4-digit year, e.g. "27-04-2026") from the `revenue()` method. String comparison `date_string = '27-04-2026'` will miss older rows.
**Why it happens:** Two different write sites in Finance.php (lines 201 and 363 use `'d-m-y'`; the revenue method uses `'d-m-Y'`).
**How to avoid:** Query using the `date` column (Unix timestamp INT) for `today_revenue` and chart data. Pattern: `WHERE date >= strtotime('today') AND date < strtotime('tomorrow')`. This is how `getPaymentByDoctorDate()` works.
**Warning signs:** Revenue shows 0 even when payments exist for today.

### Pitfall 2: Column Name Mismatch in visit_vital
**What goes wrong:** The `visit_vital` DB table (migration 20260419000007, line 125) has columns `bp_systolic` and `bp_diastolic`. The consultation room view at lines 51-52 reads `bp_sys` and `bp_dia`. The vitals poll JS at lines 148-149 also uses `bp_sys` / `bp_dia`.
**Why it happens:** View was written before or separately from the migration.
**How to avoid:** When Phase 5 touches the vitals display, use `bp_systolic` / `bp_diastolic` (the actual DB column names) OR add a model alias. Do not change the DB column — it would require a migration. The simplest fix is to alias in the `vitals_json` query: `SELECT bp_systolic AS bp_sys, bp_diastolic AS bp_dia, ...`
**Warning signs:** Vitals show "-" for systolic/diastolic even after assistant records them.

### Pitfall 3: Queue Table Still Rendered Server-Side After Refactor
**What goes wrong:** After switching to long-poll, the old `<?php foreach ($queue_today) ?>` block is left in the view, causing double rows on first render.
**Why it happens:** Partial view rewrite.
**How to avoid:** Remove the PHP foreach loop entirely. The `<tbody id="liveQueueBody">` must be empty on initial render. `pollQueue()` fires immediately on page load to populate it.
**Warning signs:** Queue shows rows twice, or shows stale data briefly then updates.

### Pitfall 4: Missing `$rx_templates` Crashes Consultation Room
**What goes wrong:** UI-SPEC template dropdown uses `<?php if (!empty($rx_templates))` guard, so if `$rx_templates` is not set, PHP throws an undefined variable notice.
**Why it happens:** `consultation_room()` doesn't set `rx_templates` in `$data`.
**How to avoid:** Always add `'rx_templates' => array()` as a fallback in `$data` even before the real source is decided. If using `prescription_favorite` as the source, just pass the existing `$favorites` as `$rx_templates`.
**Warning signs:** PHP notices in error log; dropdown not rendered.

### Pitfall 5: `document.getElementById('rxFrame')` Null Reference
**What goes wrong:** "Save draft" and "Save and print" buttons trigger iframe submit, but if `#rxFrame` doesn't exist (no patient loaded), `getElementById` returns null and `.contentWindow` throws.
**Why it happens:** Action bar is only rendered when `$patient` is not null, but JS can still be triggered.
**How to avoid:** The action bar is wrapped in `<?php if ($patient) : ?>` — which is correct. Just ensure JS button handlers also do a null check: `var frame = document.getElementById('rxFrame'); if (!frame) return;`.
**Warning signs:** Console error "Cannot read properties of null" when clicking Save without a patient loaded.

### Pitfall 6: Chart.js CDN Not Loaded on Dashboard
**What goes wrong:** Chart.js is referenced in `dashboard/views/clinical.php` via CDN, not on the `doctor_chamber` dashboard. The two Chart.js `<canvas>` elements will silently fail.
**Why it happens:** Chart.js CDN tag lives in a specific dashboard view, not in a global footer.
**How to avoid:** Add the Chart.js CDN `<script>` tag at the bottom of `doctor/dashboard.php` (before the chart init JS). Pinned version: `https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js`.
**Warning signs:** `ReferenceError: Chart is not defined` in browser console.

---

## Code Examples

### Long-Poll Queue JS (from UI-SPEC — verified contract)

```javascript
// Source: [CITED: .planning/phases/05-doctor-portal/05-UI-SPEC.md — Interaction Contracts]
(function () {
    var POLL_INTERVAL = 12000;
    function pollQueue() {
        $.getJSON(BASE_URL + 'doctor_chamber/queue_json', function (r) {
            if (!r || !r.rows) return;
            var tbody = document.getElementById('liveQueueBody');
            var html = '';
            if (r.rows.length === 0) {
                html = '<tr><td colspan="6" class="text-muted text-center py-4">No active queue yet today.</td></tr>';
            } else {
                r.rows.forEach(function (q) {
                    var statusClass = q.status || 'pending';
                    html += '<tr>' +
                        '<td>' + parseInt(q.serial_number, 10) + '</td>' +
                        '<td>' + escHtml(q.guest_name || ('#' + q.patient_id)) + '</td>' +
                        '<td>' + escHtml(q.chamber_name || '') + '</td>' +
                        '<td><span class="chamber-status ' + escHtml(statusClass) + '">' + escHtml(statusClass) + '</span></td>' +
                        '<td>' + escHtml(q.triage_summary || '') + '</td>' +
                        '<td>' + (q.patient_id
                            ? '<a class="btn btn-xs btn-primary" href="' + BASE_URL + 'doctor_chamber/consultation_room?patient=' + parseInt(q.patient_id, 10) + '"><i class="fas fa-door-open"></i> Open</a>'
                            : '') + '</td>' +
                        '</tr>';
                });
            }
            tbody.innerHTML = html;
            var el = document.getElementById('queueLastUpdated');
            if (el) el.textContent = 'Updated ' + new Date().toLocaleTimeString('en-BD', { hour: '2-digit', minute: '2-digit' });
        });
    }
    pollQueue();
    setInterval(pollQueue, POLL_INTERVAL);
    function escHtml(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
}());
```

### Chart.js Bar Chart Initialization

```javascript
// Source: [CITED: .planning/phases/05-doctor-portal/05-UI-SPEC.md — Chart configuration contract]
$.getJSON(BASE_URL + 'doctor_chamber/chart_data_json', function (d) {
    var opts = {
        type: 'bar',
        options: {
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: '#e2e8f0' }, ticks: { font: { family: 'DM Sans', size: 12 } } },
                y: { grid: { color: '#e2e8f0' }, ticks: { font: { family: 'DM Sans', size: 12 } } }
            }
        }
    };
    new Chart(document.getElementById('chartMonthlyRevenue'), Object.assign({}, opts, {
        data: { labels: d.labels, datasets: [{ data: d.revenue, backgroundColor: 'rgba(15,118,110,0.75)', borderColor: '#0f766e', borderWidth: 1 }] }
    }));
    new Chart(document.getElementById('chartMonthlyPatients'), Object.assign({}, opts, {
        data: { labels: d.labels, datasets: [{ data: d.patients, backgroundColor: 'rgba(15,118,110,0.75)', borderColor: '#0f766e', borderWidth: 1 }] }
    }));
});
```

### vitals_json Column Alias Fix

```php
// Source: [VERIFIED: visit_vital schema (migration line 125) vs view lines 51-52]
// Add to vitals_json() and consultation_room() vitals queries:
$this->db->select('id, queue_id, bp_systolic AS bp_sys, bp_diastolic AS bp_dia, pulse, weight_kg, created_at');
```

---

## CSS Token Gap Analysis

### Classes Already Present in chamber-practice.css

| Class | Status | Notes |
|-------|--------|-------|
| `.chamber-ui` | EXISTS | Page wrapper — in use |
| `.chamber-head` | EXISTS | Page header flex container |
| `.chamber-kicker` | EXISTS | "Doctor portal" eyebrow label |
| `.chamber-subtitle` | EXISTS | Muted subtitle under h1 |
| `.chamber-actions` | EXISTS | Button row flex wrapper |
| `.chamber-stat-grid` | EXISTS | 4-column stat card grid |
| `.chamber-stat` | EXISTS | Individual stat card |
| `.chamber-stat-icon` | EXISTS | Teal icon container 36x36 |
| `.chamber-stat-value` | EXISTS | 28px Outfit bold number |
| `.chamber-stat-label` | EXISTS | 13px muted label |
| `.chamber-panel` | EXISTS | White bordered panel card |
| `.chamber-panel-header` | EXISTS | Panel header with border-bottom |
| `.chamber-panel-title` | EXISTS | 16px bold panel heading |
| `.chamber-panel-body` | EXISTS | 16px padding panel body |
| `.chamber-toolbar` | EXISTS | Flex toolbar container |
| `.chamber-radio-group` | EXISTS | Radio pill flex row |
| `.chamber-radio-pill` | EXISTS | Individual radio pill label |
| `.chamber-table` | EXISTS | Table with chamber styling |
| `.chamber-status.*` | EXISTS | All 7 status tag variants (pending, arrived, serving, done, paid, cancelled, failed, high_risk, follow_up, vip) |
| `.chamber-empty` | EXISTS | Empty state dashed box |
| `.chamber-vitals-grid` | EXISTS | Vitals 4-column grid |
| `.chamber-vital` | EXISTS | Individual vital card |
| `.chamber-vital-label` | EXISTS | Vital label |
| `.chamber-vital-value` | EXISTS | Vital value |
| `.chamber-muted` | EXISTS | Muted text color |
| `.chamber-btn-row` | EXISTS | Flex wrap button row |

### Classes MISSING (Net New CSS Additions Required)

| Class | Purpose | Where Used |
|-------|---------|-----------|
| `.chamber-stat-grid` gap update | Change from 14px to 12px per UI-SPEC spacing section | Spacing fix to `.chamber-stat-grid` |

**The gap analysis confirms: zero new CSS classes are required.** All component classes needed by the UI-SPEC already exist in `chamber-practice.css`. The only CSS change is a minor gap tweak to `.chamber-stat-grid` (14px → 12px) per the spacing section. The `.chamber-panel-header` padding update (14px → 12px) and `.chamber-panel` margin-bottom (18px → 16px) are also called out in the UI-SPEC spacing section.

---

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| Server-rendered queue (PHP foreach) | Long-poll JS populating `#liveQueueBody` | Phase 5 (this phase) | Queue updates without page reload |
| Single GET form for patient search | Radio pill toggle + AJAX `search_json` | Phase 5 (this phase) | No page reload on search; mode toggle without navigation |
| No revenue stat on dashboard | `$today_revenue` from payment SUM | Phase 5 (this phase) | Doctor sees earnings at a glance |

---

## Assumptions Log

| # | Claim | Section | Risk if Wrong |
|---|-------|---------|---------------|
| A1 | `$rx_templates` in the consultation room is sourced from `prescription_favorite` (label + medicine JSON) rather than a separate content template table that doesn't yet exist | Current State Gap Analysis, Pattern 5 | If wrong: a new `prescription_content_template` table + migration is required before the template dropdown can render real data. Low impact — the dropdown is guarded by `<?php if (!empty($rx_templates))` so the feature degrades gracefully to "no dropdown" if the array is empty |
| A2 | The 6-month chart loop (PHP iterating 6 months, 6 separate queries per chart) is acceptable performance for a shared hosting target | Pattern 3 | If wrong: a single GROUP BY query is needed instead. Group-by on `date` (unix timestamp) requires MONTH/YEAR extraction which is vendor-specific SQL. The loop approach is safer for portability but 12 queries per page load |

---

## Open Questions

1. **What is the intended source for `$rx_templates` in the consultation room template dropdown?**
   - What we know: `prescription_print_template` table exists but stores print layout HTML (header/footer), not content templates. `prescription_favorite` has `label` and medicine JSON. The UI-SPEC uses `$tpl->id` and `$tpl->label`.
   - What's unclear: Is the dropdown supposed to show prescription favorites (quick-fill templates) or a new class of structured content templates?
   - Recommendation: Use `prescription_favorite` as the source for Phase 5 (rename `$favorites` as `$rx_templates` in `$data` array), and relabel the dropdown "Favorites" rather than "Templates". This avoids a schema change and uses already-loaded data.

2. **Should `followup_count` and `high_risk_patients` use `patient_practice_tag` (doctor-assigned tags) or `triage_json` in `chamber_serial_queue`?**
   - What we know: `patient_practice_tag` table stores durable tags assigned by the doctor (`high_risk`, `follow_up`, `vip`). `triage_json` in queue rows stores per-visit symptom data and may also contain a `tag` key.
   - What's unclear: UI-SPEC says "follow_up tag from today's queue" and "`triage_json LIKE '%tag':'follow_up'%' OR join to patient_tag`". The OR condition means either source counts.
   - Recommendation: Use `patient_practice_tag` JOIN `chamber_serial_queue` (verified schema). This is cleaner than LIKE on JSON and uses indexed columns.

---

## Environment Availability

Step 2.6: SKIPPED — Phase 5 is a pure code/view change within the existing `doctor_chamber` module. No new external services, CLIs, or runtimes are required beyond PHP 8.1 + MySQL which are already confirmed in `STACK.md`.

---

## Validation Architecture

### Test Framework

| Property | Value |
|----------|-------|
| Framework | PHPUnit ^10.5 |
| Config file | `phpunit.xml.dist` |
| Quick run command | `composer test -- --group DoctorPortal` |
| Full suite command | `composer test` |

### Phase Requirements → Test Map

| Req ID | Behavior | Test Type | Automated Command | File Exists? |
|--------|----------|-----------|-------------------|-------------|
| UI-09 | `dashboard()` returns `$today_revenue` as numeric SUM from payment for today's doctor | unit | `composer test -- --group DoctorPortal --filter test_ui09` | ❌ Wave 0 |
| UI-09 | `dashboard()` returns `$total_today` count scoped to correct doctor | unit | `composer test -- --group DoctorPortal --filter test_ui09` | ❌ Wave 0 |
| UI-10 | `queue_json()` returns `rows` array with correct fields | unit | `composer test -- --group DoctorPortal --filter test_ui10` | ❌ Wave 0 |
| UI-10 | `chart_data_json()` returns `revenue`, `patients`, `labels` arrays of length 6 | unit | `composer test -- --group DoctorPortal --filter test_ui10` | ❌ Wave 0 |
| UI-10 | Dashboard view contains `#liveQueueBody` (empty tbody — no PHP loop) | static analysis | `composer test -- --group DoctorPortal --filter test_ui10_view` | ❌ Wave 0 |
| UI-11 | `search_json()` with mode=id returns patient array with `id`, `name`, `phone` | unit | `composer test -- --group DoctorPortal --filter test_ui11` | ❌ Wave 0 |
| UI-11 | Consultation room view contains `#rxFrame` iframe with `embed=1` when patient loaded | static analysis | `composer test -- --group DoctorPortal --filter test_ui11_view` | ❌ Wave 0 |

### Sampling Rate

- **Per task commit:** `composer test -- --group DoctorPortal`
- **Per wave merge:** `composer test`
- **Phase gate:** Full suite green before `/gsd-verify-work`

### Wave 0 Gaps

- [ ] `tests/Unit/DoctorPortal/DoctorPortalTest.php` — covers UI-09, UI-10, UI-11 (7 test methods above)
- [ ] `tests/Unit/DoctorPortal/` directory — does not exist

---

## Security Domain

### Applicable ASVS Categories

| ASVS Category | Applies | Standard Control |
|---------------|---------|-----------------|
| V2 Authentication | No — all endpoints behind Ion Auth Doctor group check in `__construct()` | Ion Auth (`in_group(['Doctor'])`) |
| V3 Session Management | No — session managed by existing CI3 session config | CI3 native sessions |
| V4 Access Control | Yes — `currentDoctor()` must scope all queries to `$doc->id` + `$doc->hospital_id` | Verified: all existing queries do this; new endpoints must follow same pattern |
| V5 Input Validation | Yes — `search_json` takes `mode`, `id`, `date` GET params | `(int)` cast for IDs; `$this->input->get()` for strings; date validated with `strtotime()` |
| V6 Cryptography | No — no new crypto in this phase | N/A |

### Known Threat Patterns

| Pattern | STRIDE | Standard Mitigation |
|---------|--------|---------------------|
| Insecure direct object reference: `search_json?mode=id&id=999` returning another hospital's patient | Tampering | `$this->db->where('hospital_id', $doc->hospital_id)` on all patient queries |
| XSS in queue table injection via `innerHTML` | Tampering | `escHtml()` wrapper on all JS string interpolation (in UI-SPEC JS contract) |
| Missing CSRF on GET-only JSON endpoints | N/A | GET-only endpoints do not require CSRF tokens (no state change) |
| CSRF on `save draft` / `save and print` | Tampering | iframe form submit goes to `prescription/addPrescriptionView` — existing CSRF behavior inherited from that endpoint |

---

## Sources

### Primary (HIGH confidence)

- `[VERIFIED: Doctor_chamber.php]` — Full controller read; all existing methods, variable names, query patterns confirmed
- `[VERIFIED: doctor/dashboard.php]` — Full view read; current stat card layout, queue table, missing features confirmed
- `[VERIFIED: doctor/consultation_room.php]` — Full view read; iframe pattern, drug search JS, vitals poll, favorites confirmed
- `[VERIFIED: chamber-practice.css]` — Full CSS read; all existing `.chamber-*` classes inventoried
- `[VERIFIED: migration 20260419000007]` — DB schema for `chamber_serial_queue`, `visit_vital`, `patient_practice_tag`, `prescription_favorite` confirmed
- `[VERIFIED: 05-UI-SPEC.md]` — UI contract read in full; all component patterns, JS contracts, conformance checklist confirmed
- `[VERIFIED: app-design-tokens.css]` — Token layer confirmed; `--ap-*` variables all present

### Secondary (MEDIUM confidence)

- `[VERIFIED: Finance.php lines 200-201, 363]` — `date_string` uses `'d-m-y'` format in two Finance write sites; `Doctor_chamber::revenue()` line 133 uses `'d-m-Y'`; inconsistency confirmed

---

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH — all libraries verified present in codebase
- Architecture: HIGH — controller, views, CSS, DB schema all read directly
- Pitfalls: HIGH — most found by direct code inspection (column mismatch, date format, stale server render)
- `$rx_templates` source: LOW — no matching table found; resolution deferred as open question

**Research date:** 2026-04-27
**Valid until:** 2026-05-27 (stable codebase; no fast-moving dependencies)
