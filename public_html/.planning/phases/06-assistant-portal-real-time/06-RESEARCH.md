# Phase 6: Assistant Portal + Real-Time — Research

**Researched:** 2026-04-27
**Domain:** CI3 HMVC — assistant queue management, bKash inline payment, long-poll real-time sync
**Confidence:** HIGH (all claims verified from live codebase)

---

## Summary

Phase 6 is a brownfield completion phase. The `assistant_chamber` module already has a working
skeleton — controller with 13 methods, `desk.php` view, SortableJS wired, drag-reorder endpoint,
check-in, vitals, emergency bump, billing redirect, and a 12-second prescription auto-print
poller. The gap between the skeleton and the UI-SPEC contract is well-defined and surgically
scoped.

The four requirements decompose into distinct implementation streams:

- **UI-12 (Queue Desk):** Refine `desk.php` — add Source tag column, swap inline CSRF tokens to
  `csrf_field()` helper, add "Token" print button, move billing out of the vitals collapse into a
  dedicated `_billing_panel.php` partial, add `#nowServingBadge` long-poll JS, fire reorder POST
  immediately on `onEnd` instead of only on button click.
- **UI-13 (Billing Panel):** Build the `_billing_panel.php` partial (fee input + paid/due toggle +
  cash/bKash toggle + bKash reveal section), add `mark_queue_fee_paid_ajax()` AJAX variant to
  `Assistant_chamber.php`.
- **PAY-01 (bKash Inline):** Add `bkash_initiate_desk()` to `Payment_bd.php` — a new server-side
  method that performs Grant Token + Create Payment server-to-server and returns a JSON payment_id
  without redirecting. The existing `Bd_payment_bkash` library handles all cURL transport; no new
  HTTP layer is needed.
- **QUEUE-01 (Real-Time Sync):** Add `queue_ticker_json()` to `Assistant_chamber.php` — modelled
  on the existing `Portal::ticker_json()`. No WebSocket; long-poll at 12-second intervals matches
  the doctor portal and patient portal patterns. The `chamber_queue_ticker` table and
  `Queue_model::setTicker()` are already in production; the new endpoint just reads them.

One schema migration is required: add `is_emergency TINYINT(1) DEFAULT 0` column to
`chamber_serial_queue`. The column is referenced in the UI-SPEC view logic but is absent from the
current migration.

**Primary recommendation:** Implement in three waves — Wave 0 scaffolding test, Wave 1 schema
migration + view/CSS/partial, Wave 2 new controller methods + wiring, Wave 3 full integration
verification.

---

## Architectural Responsibility Map

| Capability | Primary Tier | Secondary Tier | Rationale |
|------------|-------------|----------------|-----------|
| Queue display + drag-drop | Frontend (AdminLTE view) | API/Backend (reorder POST) | DOM manipulation owns ordering; backend persists and broadcasts |
| Serial renumbering after reorder | API/Backend (`queue_reorder`) | — | `sort_position` update in `chamber_serial_queue`; sequential float assignment |
| Emergency bump | API/Backend (`emergency_bump`) | Frontend (form POST) | `sort_position` must update server-side before any poller reads it |
| Check-in status transition | API/Backend (`checkin`) | — | Owns `chamber_serial_queue.status` write |
| Print token view | Frontend (printable view) | API/Backend (data fetch) | Minimal HTML view; no business logic beyond data retrieval |
| Billing record | API/Backend (`mark_queue_fee_paid_ajax`) | Frontend (AJAX panel) | Writes to `payment` table; finance_model owns persistence |
| bKash payment initiation | API/Backend (`payment_bd/bkash_initiate_desk`) | Frontend (AJAX + status) | Credentials + cURL to bKash API are server-side only; browser never sees credentials |
| Real-time queue ticker | API/Backend (`queue_ticker_json`) | Frontend (long-poll JS) | Reads `chamber_queue_ticker` + `chamber_serial_queue`; frontend polls |
| CSS tokens (.emergency, .source-tag, .btn-bkash) | Frontend (chamber-practice.css) | — | Token file is the single source of truth for all chamber UI styling |

---

## Standard Stack

### Core (all existing — no new installs)

| Library / Component | Version | Purpose | Status |
|---------------------|---------|---------|--------|
| CodeIgniter 3 + MX HMVC | 3.x | Controller/model/routing | Existing |
| AdminLTE 3 + Bootstrap 4 | 3.x | Layout, btn-group, collapse | Existing — `adminlte/` |
| SortableJS | 1.15.0 | Drag-drop queue reorder | CDN already in `desk.php` line 135 |
| jQuery | 3.x (via AdminLTE) | AJAX, DOM, event delegation | Existing |
| Toastr | bundled | Toast notifications | Existing — `adminlte/plugins/toastr/` |
| `Bd_payment_bkash` library | — | bKash cURL transport | `application/libraries/Bd_payment_bkash.php` |
| `Queue_model` | — | `chamber_serial_queue` + `chamber_queue_ticker` | `portal/models/Queue_model.php` |
| `Finance_model` | — | Insert payment record | `finance/models/Finance_model.php` |
| `Chamber_platform_model` | — | `bd_payment_intent`, usage log | `portal/models/Chamber_platform_model.php` |

**No new packages to install.** [VERIFIED: codebase grep]

### CSS Additions (new classes in existing file)

Three CSS additions go into `common/css/chamber-practice.css` — all confirmed absent:

| Class | File | Status |
|-------|------|--------|
| `.chamber-status.emergency` | `chamber-practice.css` | MISSING — must add |
| `.chamber-source-tag` + `.manual/.phone/.website` modifiers | `chamber-practice.css` | MISSING — must add |
| `.btn-bkash` + hover/disabled states | `chamber-practice.css` | MISSING — must add |

[VERIFIED: grep of `chamber-practice.css` lines 222–268 — emergency, source-tag, btn-bkash all absent]

---

## Architecture Patterns

### System Architecture Diagram

```
[Assistant browser]
        |
        | GET assistant_chamber/desk
        v
[Assistant_chamber::desk()]
        |-- getQueueForDay() --> chamber_serial_queue (ORDER BY sort_position)
        |-- getDoctor() / getChambers()
        v
[desk.php view]
        |-- SortableJS drag → onEnd → $.post queue_reorder
        |                                   |
        |                              [queue_reorder()]
        |                              updateRow(sort_position) per id
        |                              returns {ok:true}
        |
        |-- Check-in btn → form POST → checkin() → updateRow(status='arrived')
        |-- Serving btn  → form POST → mark_serving() → updateRow(status='serving')
        |                                                 setTicker(queue_id)
        |-- Done btn     → form POST → mark_status() → updateRow(status='done')
        |                                               clear ticker if this was serving
        |-- Emergency btn → form POST → emergency_bump() → sort_position = min - 1
        |-- Bill btn     → collapse → _billing_panel.php partial
        |       |
        |       |-- "Record fee" → $.post mark_queue_fee_paid_ajax()
        |       |       |-- finance_model->insertPayment()
        |       |       |-- returns {success, message, csrf_hash}
        |       |
        |       |-- "bKash" toggle → reveals bKash section
        |               |-- "Initiate bKash" → $.post payment_bd/bkash_initiate_desk
        |                       |-- bkash_credentials_for_hospital()
        |                       |-- Bd_payment_bkash::grantToken()
        |                       |-- Bd_payment_bkash::createUrlCheckout()  [mode 0011]
        |                       |-- insertBdIntent(status='created')
        |                       |-- returns {success, payment_id, message}
        |
        |-- "Token" btn → GET assistant_chamber/print_token?queue_id={id} (new tab)
        |       |-- renders minimal 80mm-compatible HTML
        |
        |-- Long-poll (12s) → GET assistant_chamber/queue_ticker_json
                |-- getTicker(doctor_id, chamber_id, date)
                |-- returns {serial, serving_name, updated_at}
                |-- updates #nowServingBadge in panel header

[Doctor browser / Patient browser] — same chamber_queue_ticker table
        |-- GET portal/ticker_json (patient, 15s poll — Phase 4, already built)
        |-- Doctor dashboard reads queue_today from DB (static page load, no long-poll yet)
        |   NOTE: Doctor dashboard (Phase 5) uses static page load — no queue_json endpoint
        |         exists. QUEUE-01 is satisfied for the doctor side if the doctor reloads
        |         or if a future Phase 5 enhancement adds a poller. Phase 6 QUEUE-01
        |         scope is: assistant + patient views update in real-time.
```

### Recommended Project Structure (changes only)

```
application/modules/assistant_chamber/
├── controllers/
│   └── Assistant_chamber.php          — ADD: print_token(), mark_queue_fee_paid_ajax(),
│                                              queue_ticker_json()
├── views/assistant/
│   ├── desk.php                       — MODIFY: source tag col, csrf_field(), Bill btn,
│   │                                            Token btn, nowServingBadge, long-poll JS,
│   │                                            onEnd auto-reorder, load billing partial
│   └── _billing_panel.php             — NEW: fee input + toggles + bKash section
│                                              (partial loaded by desk.php per queue row)
application/modules/payment_bd/
└── controllers/
    └── Payment_bd.php                 — ADD: bkash_initiate_desk() method
common/css/
└── chamber-practice.css               — ADD: .chamber-status.emergency,
                                               .chamber-source-tag.*, .btn-bkash
application/migrations/
└── 20260427000012_chamber_queue_is_emergency.php  — ADD: is_emergency column
```

---

## Research Findings by Topic

### 1. Current State of `desk.php` — What Exists vs What Needs Building

**What exists (verified):** [VERIFIED: read of `desk.php` lines 1–205]

- `.chamber-ui` wrapper, header with kicker, page title, actions
- Context toolbar (doctor/chamber/date selectors) with GET form
- `$this->session->flashdata('chamber_desk_msg')` flash alert
- Queue table `#queueBody` with columns: `#`, Serial, Patient, Phone, Status, Advance, Triage, Actions
- SortableJS loaded from CDN (line 135), bound with `handle: '.handle'` (no animation option set)
- `#saveOrder` button below table (not in panel header) — fires `$.post` with `alert('Order saved')`
- Check-in, Serving, Done, Cancel, Emergency buttons — all standard form POSTs with manual CSRF token injection (`$this->security->get_csrf_token_name()` / `get_csrf_hash()`)
- Vitals collapse and fee form inside the same `#v{id}` collapse region
- `desk_rx_poll` auto-print script (12s poller)
- Empty state check

**What is MISSING from `desk.php` (Phase 6 delta):**

| Gap | Action |
|-----|--------|
| No Source tag column | Add `<th>Source</th>` + `<td><span class="chamber-source-tag ...">` per row |
| CSRF via manual injection | Replace all `$this->security->get_csrf_token_name()/get_csrf_hash()` with `csrf_field()` helper |
| No "Token" print button | Add `<a href="assistant_chamber/print_token?queue_id=...">` per row |
| Billing in vitals collapse, not dedicated panel | Move billing to `_billing_panel.php` partial loaded in its own collapse `#billing{id}` |
| "Bill" collapse toggle button absent | Add per-row |
| Emergency button is `btn-warning` not `btn-danger` | Change to `btn-danger` per UI-SPEC |
| `onEnd` does not auto-POST | SortableJS `onEnd` callback fires `$.post queue_reorder` immediately |
| `#saveOrder` below table, uses `alert()` | Move to panel header; use `toastr.success` |
| No `#nowServingBadge` element | Add to panel header |
| No queue ticker long-poll JS | Add 12s `setInterval` calling `queue_ticker_json` |
| `animation: 150` not set on Sortable | Add option |
| `btn-primary` on Manual booking link is full-size, not `btn-sm` | Adjust to `btn btn-sm btn-primary` |

### 2. Current State of Billing — What Exists vs What Phase 6 Needs

**What exists:** [VERIFIED: `desk.php` lines 112–125]

The billing form is embedded inside the vitals collapse (`#v{id}`). It is a plain inline-form
`POST` to `assistant_chamber/mark_queue_fee_paid` with: queue_id, amount, fee_type, pay_method
(free-text input), remarks. It does a page redirect on success.

**`mark_queue_fee_paid()` method exists:** [VERIFIED: `Assistant_chamber.php` lines 218–267]
Reads post data, calls `finance_model->insertPayment()`, logs usage, redirects. The full payment
record insertion is already implemented and correct.

**What Phase 6 adds:**

1. `_billing_panel.php` partial — replaces the inline billing form; adds Paid/Due `btn-group`,
   Cash/bKash `btn-group`, currency prefix from `$settings->currency`, bKash reveal section.
2. `mark_queue_fee_paid_ajax()` — thin wrapper around the same logic as `mark_queue_fee_paid()`
   but returns `json_encode(['success'=>true, 'message'=>'Fee recorded.', 'csrf_hash'=>...])`.
   The existing redirect method is retained for fallback.
3. `pay_status` field (paid/due) — the existing method always sets `status = 'paid'`. The AJAX
   variant must pass `pay_status` through to the payment record. The `payment` table has a `status`
   column (confirmed via `mark_queue_fee_paid` line 247: `'status' => 'paid'`).

### 3. SortableJS Drag-Drop — Current Skeleton vs Full Implementation

**Current state:** [VERIFIED: `desk.php` lines 136–148]

```javascript
Sortable.create(el, { handle: '.handle' });
$('#saveOrder').click → collect ids → $.post queue_reorder → alert('Order saved')
```

Missing: `animation: 150`, `onEnd` auto-fire, toastr instead of alert, button in panel header.

**`queue_reorder()` already fully implemented:** [VERIFIED: `Assistant_chamber.php` lines 92–112]
Iterates `order[]` array, validates each row against `hospital_id`, sets `sort_position` as
sequential float. Returns `{ok: true}`. No changes needed to this method.

**Serial renumbering after drag-drop:** The existing `queue_reorder()` updates only `sort_position`
(the display ordering float). `serial_number` is the immutable intake sequence number (assigned at
booking time). Serial numbers are NOT renumbered on drag-drop — the queue is re-sorted by
`sort_position`, not by `serial_number`. This is correct: serial number is the patient's token
number; sort order is the serving sequence.

**Broadcasting to pollers:** Because `queue_ticker_json` reads `chamber_queue_ticker` (not
`sort_position`), a drag-drop reorder does NOT need to update the ticker. The doctor's dashboard
shows `queue_today` ordered by `sort_position` — it reflects the new order on next page load. For
QUEUE-01 strict compliance, the queue order visible to the doctor updates within their next page
load or poll cycle; the patient poller only tracks "now serving serial", not the full order.

### 4. bKash API Flow for Inline Desk Payment (PAY-01)

**Existing bKash flow (patient portal):** [VERIFIED: `Payment_bd.php` `init_bkash()`]

The existing flow is a redirect flow (Checkout URL mode 0011):
1. `init_bkash()` — grants token, creates payment, gets `bkashURL`, redirects patient browser to bKash checkout.
2. `bkash_callback()` — patient returns, execute payment server-side, mark paid.

**Phase 6 bKash flow (desk, inline):**

The desk flow must NOT redirect the browser. The assistant stays on `desk.php`. The new
`bkash_initiate_desk()` endpoint in `Payment_bd.php`:

1. Auth check — must verify the caller is a logged-in Receptionist/Nurse/admin with valid session.
2. Load queue row by `queue_id` from POST; validate `hospital_id` matches session.
3. Call `bkash_credentials_for_hospital($hid)` — same protected method already in `Payment_bd`.
4. `Bd_payment_bkash::grantToken()` — server-to-server.
5. `Bd_payment_bkash::createUrlCheckout()` — server-to-server; gets `paymentID` + `bkashURL`.
6. `insertBdIntent()` — save intent with `status='created'`, `gateway_session_id=paymentID`.
7. Return JSON `{success: true, payment_id: paymentID, message: 'bKash payment initiated'}`.
8. The `bkashURL` is NOT used — the desk flow is "initiate only". The assistant physically takes
   the patient's bKash confirmation code or uses a physical terminal; the system records the
   initiation. Execute/callback happens out-of-band or is confirmed separately.

**Key constraint:** `Bd_payment_bkash` library works via static methods + cURL. No new HTTP client
needed. The `45s` cURL timeout is already set. [VERIFIED: `Bd_payment_bkash.php` line 188]

**Auth guard for `bkash_initiate_desk()`:** `Payment_bd::__construct()` does NOT check auth — it
is designed for public payment flows. `bkash_initiate_desk()` must add its own auth guard:
```php
if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group(['Receptionist', 'Nurse', 'admin'])) {
    $this->output->set_status_header(403)
         ->set_content_type('application/json')
         ->set_output(json_encode(['success'=>false,'message'=>'Unauthorized']));
    return;
}
```
[VERIFIED: `Payment_bd::__construct()` lines 9–17 — no auth check present]

**CSRF:** The AJAX POST from the browser includes the CSRF token via Phase 3's `csrf_inject.js`
(global `ajaxSuccess` hook). No per-request token injection needed in the billing JS.

### 5. `ticker_json` — Extended in Phase 4; How to Build `queue_ticker_json` for Phase 6

**Existing `portal/ticker_json`:** [VERIFIED: `Portal.php` lines 66–109]

- Auth: none (public endpoint for patient portal)
- Returns: `{ok, serial, status, csrf, patient_serial?, estimated_wait?}`
- Uses: `Queue_model::getTicker()` → returns current `chamber_serial_queue` row for ticker

**Phase 6 `assistant_chamber/queue_ticker_json`:** Same data source, different auth:

- Auth: must be logged-in staff (session check in `Assistant_chamber` constructor already handles this)
- The constructor `ion_auth->in_group(['Receptionist','Nurse','admin'])` guard is inherited — no extra work
- Returns: `{serial, serving_name, updated_at}` per UI-SPEC
  - `serial` → `$row->serial_number` (int or null)
  - `serving_name` → `$row->guest_name` (the patient being served, or null)
  - `updated_at` → `chamber_queue_ticker.updated_at` timestamp in ISO 8601 (`+06:00`)

**Gap: `getTicker()` returns a `chamber_serial_queue` row, not a `chamber_queue_ticker` row.**
The ticker `updated_at` timestamp lives in `chamber_queue_ticker`, but `getTicker()` only returns
the queue row. Two options:

- **Option A (recommended):** Inline query — load ticker row separately to get `updated_at`:
  ```php
  $ticker_row = $this->db->get_where('chamber_queue_ticker', [...doctor/chamber/date...])->row();
  $updated_at = $ticker_row ? $ticker_row->updated_at : date('Y-m-d H:i:s');
  ```
- **Option B:** Add `updated_at` to `getTicker()` return — requires modifying `Queue_model`.

Option A avoids touching the shared model and keeps the change isolated. [ASSUMED: Option A is
the lower-risk approach]

### 6. Serial Renumbering After Drag-Drop

Serial numbers (`serial_number`) are immutable intake identifiers. They are NOT renumbered when
the queue is reordered. [VERIFIED: `Queue_model::nextSerial()` — serial is set once at
`insertQueueRow` time; `queue_reorder()` only updates `sort_position`]

The `getQueueForDay()` query orders by `sort_position ASC` — so after a drag-drop and reorder
POST, the next page load or queue poll shows the new serving sequence with unchanged serial
numbers. This is the correct behavior for the Bangladesh chamber context: a patient's paper token
shows serial #7 even if they get served before serial #5.

**Emergency bump does not renumber either:** [VERIFIED: `emergency_bump()` lines 123–132] Sets
`sort_position = min - 1.0` so the row sorts first. The `serial_number` is unchanged.

### 7. Emergency Override — Current Implementation + Phase 6 Delta

**What exists:** [VERIFIED: `Assistant_chamber.php` lines 114–133]

`emergency_bump()` — sets the row's `sort_position` to `(current_min - 1.0)`. Redirects back
to desk. No `is_emergency` flag is set anywhere in the current code.

**Phase 6 delta:**

1. `is_emergency` column does NOT exist in `chamber_serial_queue`. [VERIFIED: migration
   `20260419000007` schema — only columns confirmed: `id, hospital_id, doctor_id, chamber_id,
   queue_date, serial_number, sort_position, status, patient_id, guest_name, guest_phone,
   appointment_id, triage_json, advance_fee_amount, advance_payment_status, remarks,
   created_at, updated_at`]

2. A new migration must add: `ALTER TABLE chamber_serial_queue ADD is_emergency TINYINT(1) NOT NULL DEFAULT 0`

3. `emergency_bump()` must be updated to also set `is_emergency = 1` via `updateRow()`.

4. `desk.php` view logic checks `$q->is_emergency` to switch status display to
   `.chamber-status.emergency`.

### 8. Patient Check-In Flow — Current State

**Fully implemented:** [VERIFIED: `Assistant_chamber.php` lines 53–67]

`checkin()` — validates POST, `getRow()` with hospital_id guard, `updateRow(status='arrived')`,
logs usage, redirects to desk. The button in `desk.php` is conditionally shown only when
`$q->status === 'pending'`. No changes needed to the check-in logic itself; Phase 6 only changes
the CSRF token injection pattern (from manual to `csrf_field()`).

### 9. Print Serial Token — Current State + What's Needed

**`print_token()` method does NOT exist** in `Assistant_chamber.php`. [VERIFIED: grep of all
method signatures in the controller — method absent]

**What needs building:**

```php
public function print_token() {
    $queue_id = (int) $this->input->get('queue_id');
    $hid = $this->session->userdata('hospital_id');
    $row = $this->queue_model->getRow($queue_id, $hid);
    if (!$row) { show_404(); }
    $doc = $this->db->get_where('doctor', ['id' => $row->doctor_id], 1)->row();
    $settings = $this->settings_model->getSettings();
    $data = ['queue_row' => $row, 'doctor' => $doc, 'settings' => $settings];
    // Load minimal print view — NO home/dashboard wrapper, NO home/footer
    $this->load->view('assistant/print_token', $data);
}
```

**New view:** `assistant/print_token.php` — minimal HTML:
- `<head>` with `@media print { body { margin: 0; } }` and `onload="window.print()"`
- Max-width 300px (80mm thermal paper)
- Hospital name, doctor name, patient name, serial number (large), date
- System monospace font — no external CDN dependencies (must print cleanly offline)

---

## Don't Hand-Roll

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| bKash HTTP transport | Custom cURL | `Bd_payment_bkash` library | Already handles JSON, headers, token, execute, query — tested in production (patient portal uses it) |
| Queue persistence | Custom queue logic | `Queue_model` methods | `getRow()`, `updateRow()`, `setTicker()`, `getTicker()`, `getQueueForDay()` already implement all needed operations |
| Payment record insertion | Direct DB insert | `finance_model->insertPayment()` | Used by existing `mark_queue_fee_paid()` — handles all payment table fields |
| Drag-drop | Custom JS drag | SortableJS (already loaded) | CDN-pinned, proven, handle-based, 150ms animation |
| Toast notifications | `alert()` or custom | Toastr (already in AdminLTE) | Global `toastr.success/error/warning` — already used elsewhere |
| Intent tracking for bKash | Ad-hoc status field | `chamber_platform_model->insertBdIntent()` / `updateBdIntent()` | `bd_payment_intent` table purpose-built for this |
| Serial token print layout | PDF library | Minimal HTML + `window.print()` | 80mm thermal printer compatibility requires plain HTML; mPDF overkill for a 4-field slip |

---

## Common Pitfalls

### Pitfall 1: CSRF Token on Form POSTs
**What goes wrong:** Using `$this->security->get_csrf_token_name()` + `get_csrf_hash()` inline in
forms — works but Phase 3 standardised all views to use `csrf_field()` helper output.
**Why it happens:** Existing `desk.php` uses the manual pattern throughout.
**How to avoid:** Replace all manual CSRF injections with `<?php echo csrf_field(); ?>` in every
form. The Phase 3 `csrf_inject.js` hook also auto-injects into AJAX POSTs globally — no per-AJAX
token handling needed in Phase 6 JS.
**Warning signs:** If CSRF errors appear on form submits after Phase 3 runs, the manual tokens
are stale.

### Pitfall 2: `is_emergency` Column Missing From Schema
**What goes wrong:** `desk.php` view references `$q->is_emergency` — if the column doesn't
exist, PHP throws `undefined property` notices (or silently returns null, depending on error level).
**Why it happens:** The UI-SPEC assumes the column exists; the migration does not add it.
**How to avoid:** Migration `20260427000012` must be the first task in Wave 1 (before any view
changes land).
**Warning signs:** Desk page throwing PHP notices or emergency rows not displaying the red tag.

### Pitfall 3: bKash `bkash_initiate_desk()` — Auth Gap in `Payment_bd`
**What goes wrong:** `Payment_bd::__construct()` has no auth check. Adding a new method without
an explicit auth guard exposes the bKash credential call to unauthenticated requests.
**Why it happens:** `Payment_bd` was designed for public payment callbacks; the constructor
pattern does not protect new methods.
**How to avoid:** Add explicit `ion_auth->logged_in()` + group check at the top of
`bkash_initiate_desk()`, before any credential or cURL work.
**Warning signs:** `/payment_bd/bkash_initiate_desk` returning non-403 to a curl request without
a session cookie.

### Pitfall 4: SortableJS `animation` Option Not Set
**What goes wrong:** Without `animation: 150`, rows snap instantly on drop — poor UX and makes
it hard to confirm a drop happened.
**Why it happens:** Existing skeleton uses minimal `Sortable.create(el, { handle: '.handle' })`.
**How to avoid:** Update to `Sortable.create(el, { handle: '.handle', animation: 150, onEnd: ... })`.

### Pitfall 5: Billing Panel Opened via Collapse — `csrf_field()` Token Stale After `csrf_regenerate`
**What goes wrong:** If Phase 3's `csrf_regenerate = true` is active, the CSRF token in the
billing panel HTML changes after the first POST — any subsequent AJAX from the already-rendered
collapse region uses the original stale token.
**Why it happens:** `csrf_regenerate = true` rotates the token on every validated request. The
Phase 3 `csrf_inject.js` `ajaxSetup` hook reads `window.CI_CSRF_HASH` which is refreshed on
each response. AJAX requests using `$.post` with Phase 3's global hook are automatically
protected. Plain form POSTs inside collapses are not.
**How to avoid:** All billing writes from `_billing_panel.php` use AJAX (`$.post`) — not form
POSTs. The `csrf_field()` in the vitals form POSTs is fine because the page reloads after
vitals save.

### Pitfall 6: `print_token.php` Must NOT Load `home/dashboard` or `home/footer`
**What goes wrong:** Loading the standard layout wraps the token in the full AdminLTE sidebar/nav
and breaks `window.print()` targeting.
**Why it happens:** Convention in this module is `load->view('home/dashboard', $data)` before
content views.
**How to avoid:** `print_token()` controller method loads only the minimal view — no dashboard
wrapper. Set `Content-Type: text/html`.

### Pitfall 7: `queue_ticker_json` Must Not Return `serving_name` From Ticker Row
**What goes wrong:** `chamber_queue_ticker` only stores `current_queue_id`. `serving_name` comes
from the `chamber_serial_queue` row that `getTicker()` fetches. If nobody is serving,
`getTicker()` returns null — `serving_name` must be null, not an empty string.
**Why it happens:** The UI-SPEC response contract distinguishes `null` from `""` — the JS checks
`if (r.serial)` which would be truthy for `""`.
**How to avoid:** Return `'serving_name' => $row ? $row->guest_name : null` explicitly.

---

## Code Examples

### queue_ticker_json() — New Method Pattern
```php
// Source: modelled on Portal::ticker_json() lines 66–109 (verified)
public function queue_ticker_json()
{
    $doctor_id  = (int) $this->input->get('doctor_id');
    $chamber_id = (int) $this->input->get('chamber_id');
    $date       = $this->input->get('date');
    if (!$doctor_id || !$chamber_id || !$date) {
        $this->output->set_content_type('application/json')
             ->set_output(json_encode(array('serial' => null, 'serving_name' => null, 'updated_at' => date('c'))));
        return;
    }
    $row = $this->queue_model->getTicker($doctor_id, $chamber_id, $date);
    // Get updated_at from ticker table directly (getTicker returns queue row, not ticker row)
    $ticker = $this->db->select('updated_at')
        ->get_where('chamber_queue_ticker', array(
            'doctor_id'  => $doctor_id,
            'chamber_id' => $chamber_id,
            'queue_date' => $date,
        ), 1)->row();
    $updated_at = $ticker ? date('c', strtotime($ticker->updated_at)) : date('c');
    $this->output->set_content_type('application/json')->set_output(json_encode(array(
        'serial'       => $row ? (int) $row->serial_number : null,
        'serving_name' => $row ? ($row->guest_name ?: null) : null,
        'updated_at'   => $updated_at,
    )));
}
```

### mark_queue_fee_paid_ajax() — AJAX Variant Pattern
```php
// Source: based on existing mark_queue_fee_paid() lines 218–267 (verified)
public function mark_queue_fee_paid_ajax()
{
    if ($this->input->method() !== 'post') {
        $this->output->set_status_header(405)->set_content_type('application/json')
             ->set_output(json_encode(array('success' => false, 'message' => 'Method not allowed')));
        return;
    }
    $id  = (int) $this->input->post('queue_id');
    $hid = $this->session->userdata('hospital_id');
    $row = $this->queue_model->getRow($id, $hid);
    if (!$row || empty($row->patient_id)) {
        $this->output->set_content_type('application/json')
             ->set_output(json_encode(array('success' => false, 'message' => 'Patient not found or walk-in only')));
        return;
    }
    $amount = (float) $this->input->post('amount');
    if ($amount <= 0) {
        $this->output->set_content_type('application/json')
             ->set_output(json_encode(array('success' => false, 'message' => 'Invalid amount')));
        return;
    }
    // ... (same logic as mark_queue_fee_paid, omit redirect) ...
    $this->output->set_content_type('application/json')->set_output(json_encode(array(
        'success'   => true,
        'message'   => 'Fee recorded.',
        'csrf_hash' => $this->security->get_csrf_hash(),
    )));
}
```

### bkash_initiate_desk() — New Method in Payment_bd
```php
// Source: based on init_bkash() pattern lines 399–460 (verified), desk variant returns JSON
public function bkash_initiate_desk()
{
    if ($this->input->method() !== 'post') {
        $this->output->set_status_header(405)->set_content_type('application/json')
             ->set_output(json_encode(array('success' => false, 'message' => 'Method not allowed')));
        return;
    }
    if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group(array('Receptionist', 'Nurse', 'admin'))) {
        $this->output->set_status_header(403)->set_content_type('application/json')
             ->set_output(json_encode(array('success' => false, 'message' => 'Unauthorized')));
        return;
    }
    $queue_id = (int) $this->input->post('queue_id');
    $amount   = (float) $this->input->post('amount');
    $hid      = $this->session->userdata('hospital_id');
    if ($queue_id < 1 || $amount <= 0) {
        $this->output->set_content_type('application/json')
             ->set_output(json_encode(array('success' => false, 'message' => 'queue_id and amount required')));
        return;
    }
    $row = $this->queue_model->getRowById($queue_id);
    if (!$row || (int) $row->hospital_id !== (int) $hid) {
        $this->output->set_content_type('application/json')
             ->set_output(json_encode(array('success' => false, 'message' => 'Queue row not found')));
        return;
    }
    $this->load->library('Bd_payment_bkash');
    list($app_key, $app_secret, $user, $pass) = $this->bkash_credentials_for_hospital($hid);
    if ($app_key === '' || $app_secret === '' || $user === '' || $pass === '') {
        $this->output->set_content_type('application/json')
             ->set_output(json_encode(array('success' => false, 'message' => 'bKash not configured for this hospital')));
        return;
    }
    $amount_str      = number_format($amount, 2, '.', '');
    $intent_id       = $this->chamber_platform_model->insertBdIntent(array(
        'hospital_id' => $hid,
        'queue_id'    => $queue_id,
        'gateway'     => 'bkash',
        'amount'      => $amount,
        'currency'    => 'BDT',
        'status'      => 'created',
    ));
    $merchant_invoice = 'DESK' . $intent_id . 'T' . time();
    $payer_ref        = preg_replace('/\D/', '', (string) $row->guest_phone);
    $payer_ref        = $payer_ref !== '' ? substr($payer_ref, 0, 11) : ('Q' . $queue_id);
    $base             = Bd_payment_bkash::baseUrl();
    $callback_base    = rtrim(site_url('payment_bd/bkash_callback'), '/');
    $grant = Bd_payment_bkash::grantToken($base, $user, $pass, $app_key, $app_secret);
    if (empty($grant['ok']) || empty($grant['id_token'])) {
        $msg = isset($grant['error']) ? $grant['error'] : 'bKash token grant failed';
        $this->chamber_platform_model->updateBdIntent($intent_id, array('status' => 'failed'));
        $this->output->set_content_type('application/json')
             ->set_output(json_encode(array('success' => false, 'message' => $msg)));
        return;
    }
    $create = Bd_payment_bkash::createUrlCheckout(
        $base, $grant['id_token'], $app_key,
        $payer_ref, $callback_base, $amount_str, $merchant_invoice
    );
    if (empty($create['ok']) || empty($create['paymentID'])) {
        $msg = isset($create['error']) ? $create['error'] : 'bKash payment create failed';
        $this->chamber_platform_model->updateBdIntent($intent_id, array('status' => 'failed',
            'meta_json' => json_encode(array('error' => $msg))));
        $this->output->set_content_type('application/json')
             ->set_output(json_encode(array('success' => false, 'message' => $msg)));
        return;
    }
    $this->chamber_platform_model->updateBdIntent($intent_id, array(
        'gateway_session_id' => $create['paymentID'],
        'meta_json'          => json_encode(array('merchant_invoice' => $merchant_invoice)),
    ));
    $this->output->set_content_type('application/json')->set_output(json_encode(array(
        'success'    => true,
        'payment_id' => $create['paymentID'],
        'message'    => 'bKash payment initiated.',
    )));
}
```

### Migration — `is_emergency` Column
```php
// File: application/migrations/20260427000012_chamber_queue_is_emergency.php
class Migration_Chamber_queue_is_emergency extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('chamber_serial_queue')
            && !$this->db->field_exists('is_emergency', 'chamber_serial_queue')) {
            $this->db->query(
                "ALTER TABLE `chamber_serial_queue` ADD `is_emergency` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`"
            );
        }
    }
    public function down()
    {
        if ($this->db->table_exists('chamber_serial_queue')
            && $this->db->field_exists('is_emergency', 'chamber_serial_queue')) {
            $this->db->query("ALTER TABLE `chamber_serial_queue` DROP COLUMN `is_emergency`");
        }
    }
}
```

---

## State of the Art

| Old Approach (skeleton) | Phase 6 Approach | Impact |
|------------------------|------------------|--------|
| Manual CSRF injection in forms | `csrf_field()` helper (Phase 3 standard) | Consistent, Phase 3 compliant |
| Billing inside vitals collapse | Dedicated `_billing_panel.php` partial | UI-SPEC compliant, maintainable |
| Reorder only on "Save order" click | `onEnd` auto-reorder + button as fallback | Real-time queue broadcast |
| `alert('Order saved')` | Toastr toast | Consistent UX |
| Emergency button `btn-warning` | `btn-danger` per UI-SPEC | Color token compliance |
| bKash redirect flow only | bKash inline initiate (server-to-server, returns JSON) | No page redirect for desk operator |
| No token print endpoint | `print_token()` method + minimal view | Serial token workflow complete |

---

## Assumptions Log

| # | Claim | Section | Risk if Wrong |
|---|-------|---------|---------------|
| A1 | `bkash_initiate_desk()` is "initiate only" — bKash Checkout URL flow creates a payment_id that the assistant uses as a reference; full execution happens separately (physical confirmation or out-of-band). The bKashURL is not used in the desk flow. | PAY-01 bKash flow | Low — if the requirement is actually for a full browser-redirect execute flow, the endpoint design changes significantly. Discuss with user if "initiates bKash API request" means full execution or just initiation. |
| A2 | `getTicker()` returning `null` when nobody is serving is the correct "idle" state — `queue_ticker_json` should return `{serial: null, serving_name: null}` | QUEUE-01 ticker endpoint | Low — the patient portal already handles the null case identically |
| A3 | Option A (inline query for `updated_at` from `chamber_queue_ticker`) is preferred over modifying `Queue_model::getTicker()` | queue_ticker_json implementation | Low — both approaches produce identical output |

---

## Open Questions (RESOLVED)

1. **bKash desk flow — initiate only or full execute?**
   - RESOLVED: Initiate only. PAY-01 requirement says "initiates bKash API request, shows payment status" — "initiates" is the operative word. Returns `payment_id` for assistant reference. Full execute + callback is covered by existing `bkash_callback()`/`bkash_try_finalize()` flow if needed in future. Plans proceed on initiate-only basis.

2. **Doctor queue screen — QUEUE-01 scope**
   - RESOLVED: Phase 5 plans (05-01 + 05-02) add `queue_json()` endpoint + 12s long-poll JS to doctor dashboard — doctor queue IS covered. Phase 5 is not yet executed but the plan exists. Phase 6 adds `queue_ticker_json()` for the assistant desk view and confirms patient `ticker_json` (extended in Phase 4) completes the 3-portal sync. All 3 portals covered across Phases 4, 5, and 6. Plans proceed on this basis.

---

## Environment Availability

Step 2.6: SKIPPED — Phase 6 is purely PHP code + CSS changes. All dependencies (MySQL, PHP, bKash cURL) are confirmed in the existing codebase. No new external tools required.

---

## Validation Architecture

### Test Framework
| Property | Value |
|----------|-------|
| Framework | PHPUnit ^10.5 |
| Config file | `phpunit.xml.dist` |
| Quick run command | `composer test -- --filter AssistantPortalTest` |
| Full suite command | `composer test` |

### Phase Requirements → Test Map

| Req ID | Behavior | Test Type | Automated Command | File Exists? |
|--------|----------|-----------|-------------------|-------------|
| UI-12 | `desk()` loads with queue data, source tag logic, emergency status | Unit (static analysis + method existence) | `composer test -- --filter AssistantPortalTest` | ❌ Wave 0 |
| UI-12 | `queue_reorder()` validates hospital_id per row | Unit | `composer test -- --filter AssistantPortalTest` | ❌ Wave 0 |
| UI-12 | `emergency_bump()` sets `is_emergency=1` after migration | Unit | `composer test -- --filter AssistantPortalTest` | ❌ Wave 0 |
| UI-12 | `print_token()` method exists and returns non-empty output | Unit | `composer test -- --filter AssistantPortalTest` | ❌ Wave 0 |
| UI-13 | `mark_queue_fee_paid_ajax()` returns JSON envelope | Unit | `composer test -- --filter AssistantPortalTest` | ❌ Wave 0 |
| PAY-01 | `bkash_initiate_desk()` returns `{success, payment_id}` | Unit | `composer test -- --filter AssistantPortalTest` | ❌ Wave 0 |
| QUEUE-01 | `queue_ticker_json()` returns `{serial, serving_name, updated_at}` | Unit | `composer test -- --filter AssistantPortalTest` | ❌ Wave 0 |
| QUEUE-01 | `portal/ticker_json` still returns correct data after Phase 6 changes | Regression (existing test) | `composer test -- --filter PatientPortalTest` | ✅ exists |

### Sampling Rate
- **Per task commit:** `composer test -- --filter AssistantPortalTest`
- **Per wave merge:** `composer test`
- **Phase gate:** Full suite green before `/gsd-verify-work`

### Wave 0 Gaps
- [ ] `tests/Unit/AssistantPortal/AssistantPortalTest.php` — covers UI-12, UI-13, PAY-01, QUEUE-01
- [ ] No additional fixtures needed — existing `BootstrapTest` + `PhpSyntaxSmokeTest` covers file syntax

---

## Security Domain

### Applicable ASVS Categories

| ASVS Category | Applies | Standard Control |
|---------------|---------|-----------------|
| V2 Authentication | Yes (bkash_initiate_desk new public-facing method in non-authed controller) | Explicit `ion_auth->logged_in()` + group check at method entry |
| V3 Session Management | Yes (hospital_id from session used for tenant isolation in all new methods) | `$this->session->userdata('hospital_id')` — existing pattern |
| V4 Access Control | Yes (queue row ownership — must verify `hospital_id` matches session) | `queue_model->getRow($id, $hid)` tenant-scoped fetch — existing pattern |
| V5 Input Validation | Yes (queue_id, amount, fee_type, pay_method from POST) | `(int)` cast for IDs, `(float)` for amounts, whitelist for fee_type/status |
| V6 Cryptography | No (bKash credentials stored in paymentGateway table; transit is HTTPS to bKash) | Not applicable — no key derivation in Phase 6 |

### Known Threat Patterns for This Stack

| Pattern | STRIDE | Standard Mitigation |
|---------|--------|---------------------|
| Unauthenticated call to `bkash_initiate_desk` | Elevation of Privilege | Auth guard in method — `ion_auth->logged_in()` + group check |
| Cross-tenant queue_id access | Information Disclosure | `queue_model->getRow($id, $hid)` — hospital_id scoped |
| Amount tampering (POST `amount` field) | Tampering | `(float)` cast + `<= 0` rejection; server-side amount from POST is trusted only after validation |
| CSRF on billing AJAX | Spoofing | Phase 3 `csrf_inject.js` global `ajaxSetup` header — no per-call token needed |
| Status parameter injection in `mark_status` | Tampering | Whitelist check `in_array($status, ['done', 'cancelled'])` — already present |

---

## Sources

### Primary (HIGH confidence)
- `Assistant_chamber.php` — read in full; all 13 methods documented
- `desk.php` — read in full; all gaps identified against UI-SPEC
- `Payment_bd.php` — read in full; bKash flow verified
- `Bd_payment_bkash.php` — read in full; all static methods verified
- `Queue_model.php` — read in full; all methods documented
- `Portal.php::ticker_json()` — read lines 66–109; ticker response pattern verified
- `chamber-practice.css` — lines 215–270; confirmed missing classes
- Migration `20260419000007` — schema verified; `is_emergency` column absent confirmed
- UI-SPEC `06-UI-SPEC.md` — read in full; all contracts extracted

### Secondary (MEDIUM confidence)
- `Doctor_chamber::dashboard()` — confirmed no queue_json endpoint exists (grep returned no matches)
- Migration `20260420000008` — paymentGateway table bKash row seed pattern verified

---

## Project Constraints (from CLAUDE.md)

- Controllers extend `MX_Controller` (HMVC) — Assistant_chamber already does; Payment_bd already does
- Models extend `CI_Model`; `$this->db->...` Query Builder only — no raw queries
- `$this->db->like()` for search — no new LIKE clauses in Phase 6
- CSRF token in all AJAX POST requests — satisfied by Phase 3 `csrf_inject.js` global hook
- No debug code in production: no `echo print_r()`, no `die()`, no `error_reporting(0)`
- Credentials in `.env` or environment variables — bKash credentials already in paymentGateway table / env vars (existing pattern)
- Commit atomically — one commit per completed plan task

---

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH — all libraries verified present in codebase
- Architecture patterns: HIGH — existing controller/model/view patterns read directly
- bKash API flow: HIGH — `Bd_payment_bkash` library read in full; existing `init_bkash()` and `bkash_callback()` methods provide exact pattern
- Schema gaps: HIGH — migration file read, `is_emergency` absence confirmed by grep
- Pitfalls: HIGH — derived from direct code inspection, not assumptions

**Research date:** 2026-04-27
**Valid until:** 2026-05-27 (stable codebase, no upstream dependencies to track)

<phase_requirements>
## Phase Requirements

| ID | Description | Research Support |
|----|-------------|------------------|
| UI-12 | Assistant queue screen: drag-and-drop serial reordering, color-coded tags, check-in, emergency override, print buttons | `desk.php` skeleton gaps catalogued; SortableJS and all action methods verified; source tag CSS and is_emergency migration identified |
| UI-13 | Assistant billing screen: patient name, fee input, paid/due toggle, payment method selector | `mark_queue_fee_paid()` implementation verified; `_billing_panel.php` partial spec extracted from UI-SPEC; AJAX variant pattern documented |
| PAY-01 | bKash payment initiation from billing screen — initiates bKash API request, shows status inline | `Bd_payment_bkash` library fully verified; `bkash_initiate_desk()` pattern derived from existing `init_bkash()` + auth gap documented |
| QUEUE-01 | Real-time queue sync — serial and "now serving" update on doctor, assistant, and patient views without page reload | `portal/ticker_json` and `chamber_queue_ticker` table verified; `queue_ticker_json()` pattern documented; doctor-side scope clarified as open question |
</phase_requirements>
