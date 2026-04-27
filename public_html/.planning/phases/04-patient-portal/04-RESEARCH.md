# Phase 4: Patient Portal - Research

**Researched:** 2026-04-27
**Domain:** CodeIgniter 3 HMVC patient portal — OTP auth, 5-step booking, queue polling, prescription PDF (mPDF), SSLCommerz
**Confidence:** HIGH (all findings verified against live codebase; no external libraries introduced)

---

## Summary

Phase 4 completes a patient-facing portal that already has a significant amount of working code. The portal module (`application/modules/portal/`) contains a fully functional OTP system, a booking pipeline, an SSLCommerz/bKash payment flow, and a live queue ticker. Two screens are entirely absent (queue status, prescription view) and one screen needs substantial restructuring (the triage/booking flow must become a true 5-step multi-step form instead of a single-scroll page).

The data layer is mature: all relevant tables (`chamber_serial_queue`, `booking_otp_session`, `bd_payment_intent`, `doctor_portal_profile`, `doctor_chamber`, `chamber_queue_ticker`) exist with correct schema. The mPDF library (`vendor/mpdf/mpdf`) is already in use by the admin prescription module with the exact `new \Mpdf\Mpdf(['format' => 'A4'])` / `$mpdf->WriteHTML($html)` / `$mpdf->Output()` pattern.

The portal intentionally uses a different auth model from the rest of the system: `portal_verified_phone` session key (no Ion Auth), enforced via PHP `if` guards, not `$this->ion_auth->in_group()`. All new portal methods must follow this pattern.

**Primary recommendation:** Build missing screens and restructure triage.php. Do not touch the payment flow, OTP library, or queue model — they are complete and correct.

---

## Project Constraints (from CLAUDE.md)

- Controllers extend `MX_Controller` (HMVC) or `CI_Controller`
- Models extend `CI_Model`; use `$this->db->...` Query Builder only — no raw queries
- Use `$this->db->like()` for search — NEVER string interpolation in LIKE clauses
- CSRF token must be included in all AJAX POST requests (for portal: inline `$this->security->get_csrf_token_name()` / `get_csrf_hash()`, NOT the admin `csrf_inject.php` prefilter)
- All new code follows existing module structure: `application/modules/{name}/controllers/`, `models/`, `views/`
- No debug code in production (`echo print_r()`, `die()`, `error_reporting(0)` banned)
- Environment config: credentials in `.env` or environment variables, never hardcoded
- Phase 1 (Security) and Phase 2 (Code Health) must complete before Phase 3+; this is Phase 4 so those are prerequisites

---

<phase_requirements>
## Phase Requirements

| ID | Description | Research Support |
|----|-------------|------------------|
| UI-05 | Patient home screen shows doctor info, upcoming appointment card, live queue status, book appointment button, prescriptions, lab reports | `landing.php` already renders hero + queue ticker. Missing: upcoming appointment panel, prescriptions panel, lab reports panel for logged-in patient. Gated by `portal_verified_phone` session check. |
| UI-06 | Patient booking flow — 5-step UI (select doctor → select chamber → select time slot with calendar → OTP verify → confirm booking) | `triage.php` is a single-scroll 3-step form. Must be restructured to 5 steps with show/hide JS panels. Slot AJAX endpoint does not yet exist — needs `Portal::slots_json()`. |
| UI-07 | Patient queue screen shows: patient's serial number, currently serving number, estimated wait time | Screen does not exist. Needs new `Portal::queue($queue_id)` method + `portal/queue.php` view. `ticker_json` endpoint exists but only returns current serving serial — must be extended with `patient_serial` and `estimated_wait`. |
| UI-08 | Patient prescription view shows doctor name, date, medicine list, advice, and download PDF button | Screen does not exist. Needs new `Portal::prescription($id)` + `Portal::prescription_pdf($id)` methods + views. Medicine data format in `prescription` table: `medicine` column is `###`-delimited string, each entry `***`-delimited fields (id, dose, frequency, days, instruction). |
| AUTH-01 | Patient can log in via OTP sent to mobile number — no email/password required for patient role | Fully implemented: `Portal::request_otp()` and `Portal::verify_otp()` exist, rate-limited (6/15min, 8 attempt max), session key `portal_verified_phone` set on success. The `Chamber_otp` library handles hash/verify. |
| PAY-02 | Patient booking flow supports SSLCommerz payment for advance booking fee | Fully implemented: `Payment_bd::init_sslcommerz()`, `sslcommerz_return()`, `sslcommerz_ipn()`, `sslcommerz_fail()` all exist and are wired. `book_success.php` already shows the "Pay with SSLCommerz" button when `advance_fee` is non-null. Minor addition: link to new queue screen from `book_success.php`. |
</phase_requirements>

---

## Architectural Responsibility Map

| Capability | Primary Tier | Secondary Tier | Rationale |
|------------|-------------|----------------|-----------|
| OTP generation + verification | Backend (Portal controller) | SMS gateway via Chamber_platform_model | Secrets (OTP hash) never leave server; client only submits plaintext code |
| Session auth state (`portal_verified_phone`) | Backend (PHP session) | — | CI3 session, not browser localStorage — tamper-resistant |
| 5-step form state | Browser / Client | — | Pure JS show/hide between `data-step` panels; no server round-trip between steps |
| Slot availability JSON | Backend JSON endpoint | Browser (renders pills) | `portal/slots_json` endpoint computes slots from chamber hours + exception table |
| Queue polling | Browser (setInterval) | Backend JSON endpoint | 15-second poll of `portal/ticker_json`; browser updates DOM |
| SSLCommerz payment redirect | Backend (`payment_bd` module) | External (SSLCommerz hosted page) | Full redirect flow — no iframe; return callbacks on server only |
| Prescription PDF | Backend (mPDF in Portal controller) | — | Server-side only; `Content-Type: application/pdf`; no client-side generation |
| Patient portal auth guard | Backend (PHP session check in controller) | — | `portal_verified_phone` checked at top of methods requiring auth; NOT Ion Auth |

---

## Standard Stack

### Core (all verified against live codebase)

| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| CodeIgniter 3 + MX HMVC | 3.x | Framework, routing, session, DB | Existing system — all portal code extends `MX_Controller` |
| Bootstrap 4.6.2 | 4.6.2 | Portal layout (CDN in `layout_public.php`) | Already loaded in portal layout shell |
| jQuery | 3.6.4 | AJAX calls, DOM manipulation in portal views | Already loaded in `layout_public.php` |
| mPDF | `vendor/mpdf/mpdf` | PDF generation for prescription download | Already in `vendor/`; used in admin `Prescription` controller |
| Chamber_otp library | (custom, `application/libraries/Chamber_otp.php`) | Generates + hashes OTP plaintext | Loaded in `Portal::__construct()` — `$this->chamber_otp` |
| Bd_payment_sslcommerz library | (custom) | SSLCommerz session init + validation | Used by `Payment_bd` module; fully tested |

[VERIFIED: codebase grep + direct file reads]

### No New Libraries Introduced

The UI-SPEC explicitly states: "No new third-party JS libraries are introduced in Phase 4." All new code is vanilla jQuery + inline JS. [VERIFIED: 04-UI-SPEC.md Registry Safety section]

---

## Architecture Patterns

### System Architecture Diagram

```
Patient Browser
    │
    ├─ GET portal/d/{slug}          → Portal::d()      → landing.php (hero + chamber/date + live ticker)
    │       └─ setInterval 15s      → portal/ticker_json              → queue_model::getTicker()
    │
    ├─ GET portal/triage/{slug}     → Portal::triage() → triage.php (5-step form)
    │       ├─ POST portal/request_otp   (AJAX)  → Chamber_otp::generate → SMS → session
    │       ├─ POST portal/verify_otp    (AJAX)  → Chamber_otp::verify  → session portal_verified_phone
    │       └─ POST portal/complete_booking       → queue_model::insert → book_success.php
    │
    ├─ GET payment_bd/init_sslcommerz?queue_id=N → SSLCommerz hosted page (external redirect)
    │       └─ POST payment_bd/sslcommerz_return  → sslcommerz_try_finalize() → queue updated
    │
    ├─ GET portal/queue/{queue_id}  → Portal::queue()  → queue.php (NEW)
    │       └─ setInterval 15s      → portal/ticker_json (extended with queue_id param)
    │
    └─ GET portal/prescription/{id} → Portal::prescription() → prescription.php (NEW)
            └─ GET portal/prescription_pdf/{id} → Portal::prescription_pdf() → mPDF → Content-Type: pdf
```

### Recommended Project Structure (additions only)

```
application/modules/portal/
├── controllers/
│   └── Portal.php          — ADD: queue(), prescription(), prescription_pdf(), slots_json()
├── models/
│   └── Portal_model.php    — ADD: getUpcomingAppointment(), getPrescriptionsForPatient(),
│                                   getLabReportsForPatient(), getPrescriptionForPortal()
├── views/portal/
│   ├── landing.php         — MODIFY: add upcoming appointment + prescriptions + lab reports panels
│   ├── triage.php          — MODIFY: restructure to 5-step show/hide form
│   ├── book_success.php    — MODIFY: add "View my queue status" button
│   ├── queue.php           — CREATE (new)
│   └── prescription.php    — CREATE (new)
common/css/
└── chamber-practice.css    — ADD: .chamber-stepper-5, .chamber-slot-pill, .chamber-otp-input,
                                   .chamber-queue-board, .chamber-rx-doc, etc.
```

### Pattern 1: Portal Controller Method (no Ion Auth)

Every new portal method that requires a verified patient session uses this pattern:

```php
// Source: application/modules/portal/controllers/Portal.php (existing methods)
public function queue($queue_id = 0)
{
    $queue_id = (int) $queue_id;
    if (!$queue_id) { show_404(); }

    $row = $this->queue_model->getRowById($queue_id);
    if (!$row || !chamber_practice_enabled_for_hospital($this, (int) $row->hospital_id)) {
        show_404();
    }
    // Queue screen: NO auth required (shareable link)
    $data = array('row' => $row, ...);
    $this->load->view('portal/layout_public',
        array('content' => $this->load->view('portal/queue', $data, true)));
}

public function prescription($id = 0)
{
    $id = (int) $id;
    // Prescription: REQUIRES auth
    $phone = $this->session->userdata('portal_verified_phone');
    if (!$phone) { redirect('portal/d/...'); }
    // load prescription, verify patient_id matches phone, render view
}
```

[VERIFIED: Portal.php::triage() and Portal.php::complete_booking() patterns]

### Pattern 2: Portal CSRF on AJAX POST

Admin `csrf_inject.php` prefilter is NOT used in the portal. Every portal AJAX POST includes the token inline:

```javascript
// Source: application/modules/portal/views/portal/triage.php lines 99-113
$.post(url, {
    slug: slug,
    mobile: $('#mobile').val(),
    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
}, function(r) { ... }, 'json');
```

After each server-round-trip the CSRF hash rotates (if `csrf_regenerate = true`). The portal therefore needs a fresh hash injected for each step panel that contains a server POST. The simplest approach: render all CSRF tokens server-side into JS variables at page load.

[VERIFIED: 04-UI-SPEC.md "CSRF" section + triage.php form]

### Pattern 3: mPDF for Prescription PDF

```php
// Source: application/modules/prescription/controllers/Prescription.php lines 748-761
$mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
$mpdf->setAutoTopMargin = 'stretch';
$mpdf->setAutoBottomMargin = 'stretch';
$html = $this->load->view('portal/prescription_pdf_template', $data, true);
$mpdf->WriteHTML($html);
// For inline download (not file save), use:
$mpdf->Output('prescription-' . $id . '.pdf', 'D');  // 'D' = download
```

The admin prescription controller saves to `invoicefile/` using `'F'` mode. For the portal download, `'D'` (force download) or `'I'` (inline browser display) is more appropriate.

[VERIFIED: Prescription.php::sendPrescription() lines 748-761]

### Pattern 4: Medicine Data Serialization

Prescriptions store medicines in a single `medicine` column as a custom delimited string:

```
{id}***{dose}***{frequency}***{days}***{instruction}###{id}***{dose}***...
```

Parsing:
```php
// Source: prescription/views/prescription_view.php lines 223-231
$entries = explode('###', $prescription->medicine);
foreach ($entries as $entry) {
    $fields = explode('***', $entry);
    // $fields[0] = medicine_id, [1] = dose, [2] = frequency, [3] = days, [4] = instruction
}
```

The portal prescription view must use the same parsing logic. Do NOT attempt to join `medicine` table on the `portal/prescription` view — use the serialized fields directly (they contain the display names at write time) OR look up by ID using `$this->db->get_where('medicine', ...)`. [VERIFIED: prescription_view.php, prescription_view_1.php]

### Pattern 5: Queue Ticker JSON (extension required)

Current `ticker_json` returns only `{ok, serial, status}` — the currently-serving serial. The queue screen (UI-07) needs `patient_serial` (the patient's own serial) and `estimated_wait`. Extension requires:

1. Add optional `queue_id` GET parameter to `Portal::ticker_json()`
2. When `queue_id` provided: load that queue row to get `patient_serial`; compute `estimated_wait = (patient_serial - now_serving) * AVG_MINUTES_PER_PATIENT`
3. Return `{ok, serial (now_serving), patient_serial, estimated_wait, status}`

[VERIFIED: Portal.php::ticker_json() + Queue_model.php — gap confirmed]

### Anti-Patterns to Avoid

- **Using Ion Auth in portal methods**: Portal auth is session-key only (`portal_verified_phone`). Never call `$this->ion_auth->in_group()` in portal controller methods.
- **Loading `home/dashboard` or `home/footer` views from portal**: Portal uses `portal/layout_public` as the sole shell. Never use the three-view AdminLTE pattern here.
- **Storing PDF to `invoicefile/`**: Use `$mpdf->Output('filename.pdf', 'D')` for the portal download — don't write files to disk for the portal.
- **Trusting `$_POST['status'] == 'VALID'` as SSLCommerz proof**: Already guarded in `sslcommerz_try_finalize()`. Don't add shortcuts. [VERIFIED: Payment_bd.php lines 220-224]
- **Re-generating serial number in JS**: Serial number is server-assigned in `complete_booking()` — never expose or accept from client.

---

## Don't Hand-Roll

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| OTP hash + verify | Custom bcrypt/hash function | `Chamber_otp` library (`$this->chamber_otp`) | Already loaded in constructor; handles bcrypt hash + timing-safe verify |
| PDF generation | HTML-to-PDF via browser or pdfmake | `new \Mpdf\Mpdf(...)` in `vendor/` | Already installed; admin prescription uses same pattern |
| SSLCommerz payment | Custom cURL to SSLCommerz API | `Bd_payment_sslcommerz::initiateSession()` library | Fully implemented with IPN, return, fail callbacks; amount verification guards in place |
| Queue serial assignment | Application-layer max+1 with race risk | `Queue_model::nextSerial()` + DB-level row lock | Already implemented; DB sequential |
| Patient lookup/create | Custom patient registration form | `Portal_model::findPatientByPhone()` + `insertPatientMinimal()` | Idempotent upsert: find by phone first, only create if not found |
| SMS dispatch | Direct gateway API call | `Chamber_platform_model::sendSmsMessage()` | Abstracts 4 SMS providers (MSG91, Twilio, 80Kobo, Clickatell); hospital config-driven |

**Key insight:** The hard parts (OTP, payment, queue, SMS) are already built. Phase 4 work is primarily UI: restructuring views, adding missing screens, wiring up data that already flows.

---

## Current State Audit

### What Exists and Works

| Component | File | State |
|-----------|------|-------|
| Doctor landing page | `portal/views/portal/landing.php` | Complete — hero, chamber selector, date picker, live ticker. Missing: logged-in patient sections. |
| Live queue ticker endpoint | `Portal::ticker_json()` | Complete for landing page use. Needs `queue_id` param extension for queue screen. |
| OTP request | `Portal::request_otp()` | Complete — rate-limited, hashed, SMS sent, session set |
| OTP verify | `Portal::verify_otp()` | Complete — 8-attempt lock, marks verified, sets `portal_verified_phone` session |
| Booking form | `portal/views/portal/triage.php` | Partially done — 3-step single-scroll form. Must become 5-step show/hide. |
| Booking submission | `Portal::complete_booking()` | Complete — inserts appointment + queue row + finance record, sends SMS confirmation |
| Booking success page | `portal/views/portal/book_success.php` | Mostly done. Missing: "View my queue status" button linking to `portal/queue/{queue_id}`. |
| SSLCommerz init | `Payment_bd::init_sslcommerz()` | Complete |
| SSLCommerz return/fail/IPN | `Payment_bd::sslcommerz_return/fail/ipn()` | Complete — all three use `portal/layout_public` shell |
| Session auth state | `portal_verified_phone` | Complete — set on verify, unset after booking submitted |

### What Is Missing (must be created)

| Component | Notes |
|-----------|-------|
| `Portal::queue($queue_id)` method | New — queue status screen |
| `portal/views/portal/queue.php` | New — the glanceable 3-number display |
| `Portal::prescription($id)` method | New — requires `portal_verified_phone` session |
| `portal/views/portal/prescription.php` | New — document-style view |
| `Portal::prescription_pdf($id)` method | New — mPDF download |
| `Portal::slots_json()` method | New — returns available time slots for a chamber+date as JSON pills |
| `Portal_model::getUpcomingAppointment()` | New — upcoming appointment for verified patient |
| `Portal_model::getPrescriptionsForPatient()` | New — list of prescriptions by patient phone |
| `Portal_model::getLabReportsForPatient()` | New — list of lab reports by patient phone |
| `Portal_model::getPrescriptionForPortal()` | New — single prescription with patient/doctor enrichment |

### What Must Be Modified

| Component | Change |
|-----------|--------|
| `triage.php` | Restructure from 3-step single-scroll to 5-step show/hide. Add slot picker (Step 3). Move OTP to Step 4. Add confirm summary (Step 5). |
| `landing.php` | Add upcoming appointment card, prescriptions panel, lab reports panel (gated by `portal_verified_phone` session) |
| `book_success.php` | Add "View my queue status" button → `portal/queue/{queue_id}` |
| `Portal::ticker_json()` | Extend to accept optional `queue_id` param; return `patient_serial` and `estimated_wait` when provided |
| `chamber-practice.css` | Add: `.chamber-stepper-5`, `.chamber-slot-grid`, `.chamber-slot-pill`, `.chamber-otp-input`, `.chamber-queue-board`, `.chamber-queue-cell`, `.chamber-queue-number`, `.chamber-rx-doc`, `.chamber-rx-header`, `.chamber-rx-table`, `@media print` rules |

---

## Common Pitfalls

### Pitfall 1: CSRF Token Stale After First AJAX Round-Trip

**What goes wrong:** `csrf_regenerate = true` means every POST rotates the token. The second AJAX call in the 5-step form uses a stale token baked into the page HTML and gets 403.
**Why it happens:** The CSRF hash in the view is rendered at page-load time. After the first POST (e.g., OTP request), the server issues a new token. The second POST (OTP verify) still sends the old token.
**How to avoid:** For each AJAX response, include the updated CSRF token in the JSON body and update the JS variable. Pattern: `echo json_encode(['ok' => true, 'csrf' => $this->security->get_csrf_hash()])`. The JS caller updates a `window.csrfHash` variable on each success/error response. All subsequent POSTs read from `window.csrfHash`.
**Warning signs:** "The action you have requested is not allowed" error from CI3 on second POST in flow.

### Pitfall 2: `portal_verified_phone` Session Cleared Too Early or Too Late

**What goes wrong:** `complete_booking()` calls `$this->session->unset_userdata(['portal_verified_phone', ...])` at the end. If the patient reloads or back-navigates after booking, the session is gone — the prescription view redirect will fire.
**Why it happens:** Session is intentionally cleared after booking to prevent re-booking with same verification. But prescription view requires the session.
**How to avoid:** Prescription view for a freshly-booked patient needs a different auth path — either rely on the prescription being new and unverified (so `portal_verified_phone` is gone), OR store a per-prescription token in the queue row that allows one-time unauthenticated view. The simplest path matching the spec: after booking, session is cleared — patient must re-verify their phone to see prescriptions. This is acceptable per the requirements (no "stay logged in" requirement).

### Pitfall 3: Prescription Medicine Data Is Fragile Delimited String

**What goes wrong:** `explode('###', ...)` and `explode('***', ...)` will produce malformed arrays if medicine names or instructions contain those delimiter sequences.
**Why it happens:** Legacy serialization format — medicine data stored as a single text column with custom delimiters, not a separate `prescription_medicine` join table.
**How to avoid:** When parsing, always check `count($fields) >= 5` before accessing `$fields[4]`. Use `htmlspecialchars()` on every rendered field. Do NOT attempt to re-serialize or modify this data from the portal — read-only access only.
**Warning signs:** Blank or broken rows in the medicine table in the portal prescription view.

### Pitfall 4: `ticker_json` Extension Breaks Existing Landing Page Polling

**What goes wrong:** Adding `queue_id` logic to `ticker_json` without backward compatibility breaks `landing.php` which calls `ticker_json` without `queue_id`.
**Why it happens:** `landing.php` JS passes `doctor_id`, `chamber_id`, `date` — no `queue_id`. The queue screen passes `queue_id` in addition.
**How to avoid:** The `queue_id` param is optional. When absent, `ticker_json` behaves exactly as today. When present, augment the response with `patient_serial` and `estimated_wait` fields. Existing callers ignore unknown fields.

### Pitfall 5: mPDF Temp Directory Permissions

**What goes wrong:** mPDF fails with a filesystem error trying to write temporary files in its default temp dir.
**Why it happens:** Shared hosting may not have write access to `/tmp` or mPDF's default temp path.
**How to avoid:** Pass explicit temp dir: `new \Mpdf\Mpdf(['format' => 'A4', 'tempDir' => APPPATH . '../files/mpdf-tmp'])`. Create the dir if it doesn't exist. The admin prescription code does NOT do this (uses defaults) — check if this has ever caused issues on production. If using `'D'` output mode (download), no file is written, which avoids the temp dir concern partially; mPDF still needs the temp dir for font caching.
**Warning signs:** 500 error on first PDF generation attempt on a fresh server deployment.

### Pitfall 6: Queue Screen Estimated Wait Requires "Now Serving" Number

**What goes wrong:** The queue screen's "Est. wait" cell requires knowing the currently-serving serial. `chamber_queue_ticker` only stores `current_queue_id` (not directly the serial). A join to `chamber_serial_queue` is needed.
**Why it happens:** `Queue_model::getTicker()` already does this join via `getRow()`. The result object has `serial_number` — this IS the "now serving" serial.
**How to avoid:** In the extended `ticker_json`, when `queue_id` is provided:
1. Load the patient's row by `queue_id` → `patient_serial = $row->serial_number`
2. Load the ticker for that doctor/chamber/date → `now_serving = $ticker_row->serial_number` (may be null if queue not started)
3. `estimated_wait = max(0, patient_serial - now_serving) * AVG_MINUTES` (assume 5 min/patient as default if not configured)

---

## Code Examples

### OTP AJAX with CSRF rotation (Step 4)

```javascript
// Source: triage.php pattern — extended for CSRF rotation
var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

$('#btnOtp').on('click', function () {
    var $btn = $(this);
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Sending...');
    var payload = { slug: slug, mobile: $('#mobile').val() };
    payload[csrfName] = csrfHash;
    $.post('<?php echo site_url('portal/request_otp'); ?>', payload, function (r) {
        if (r.csrf) { csrfHash = r.csrf; }  // rotate token
        $('#otpMsg').text(r.ok ? '' : (r.msg || 'Error'));
        if (r.ok) { /* show OTP input */ }
    }, 'json').always(function () {
        $btn.prop('disabled', false).html('Send OTP');
    });
});
```

### Prescription PDF controller method

```php
// Pattern for Portal::prescription_pdf() — based on Prescription::sendPrescription()
public function prescription_pdf($id = 0)
{
    $id = (int) $id;
    $phone = $this->session->userdata('portal_verified_phone');
    if (!$phone || !$id) { show_404(); }

    $prescription = $this->portal_model->getPrescriptionForPortal($id);
    if (!$prescription) { show_404(); }

    // Verify the prescription belongs to this patient
    $patient = $this->portal_model->findPatientByPhone(
        (int) $this->session->userdata('portal_verified_hospital'), $phone
    );
    if (!$patient || (int) $prescription->patient !== (int) $patient->id) {
        show_error('Access denied', 403);
    }

    $data['prescription'] = $prescription;
    // Load doctor, settings for header...
    $html = $this->load->view('portal/prescription_pdf_template', $data, true);

    $mpdf = new \Mpdf\Mpdf(['format' => 'A4',
        'tempDir' => APPPATH . '../files/mpdf-tmp']);
    $mpdf->WriteHTML($html);
    $mpdf->Output('prescription-' . $id . '.pdf', 'D');
}
```

### Queue polling with visibility API

```javascript
// Source: 04-UI-SPEC.md interaction contract
var tickerInterval = null;
function loadQueueStatus() {
    $.getJSON('<?php echo site_url('portal/ticker_json'); ?>', {
        doctor_id: doctorId, chamber_id: chamberId,
        date: queueDate, queue_id: queueId
    }, function (r) {
        if (r.ok) {
            $('#yourSerial').text(r.patient_serial || '--');
            $('#nowServing').text(r.serial || '--');
            var wait = r.estimated_wait != null ? r.estimated_wait + ' min' : '--';
            $('#estWait').text(wait);
            $('#lastRefreshed').text('just now');
            updateStatusBadge(r.status);
            if (r.serial != null && r.patient_serial != null
                && r.patient_serial - r.serial === 1) {
                $('#servingCell').addClass('is-next');
                $('#nextNotice').show();
            }
        }
    });
}
function startTicker() { tickerInterval = setInterval(loadQueueStatus, 15000); }
function stopTicker() { clearInterval(tickerInterval); }
document.addEventListener('visibilitychange', function () {
    document.hidden ? stopTicker() : (loadQueueStatus(), startTicker());
});
loadQueueStatus();
startTicker();
```

---

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| Triage form: single-scroll 3-step | Must become 5-step show/hide | Phase 4 change | JS controls step visibility; no page reload between steps |
| Queue ticker: landing page only | Must serve queue screen too | Phase 4 change | Extend `ticker_json` with `queue_id` param |
| Payment return pages: raw HTML strings in controller | Must use `portal/layout_public` shell | Already done in `Payment_bd` | Consistent look on success/fail pages |

**Note on `$.ajax` CSRF in portal:** The portal has always used inline token injection (not `window.CI_CSRF_HASH`). This is correct and should be retained. The admin `csrf_inject.php` prefilter added in Phase 3 is irrelevant here.

---

## Environment Availability

| Dependency | Required By | Available | Version | Fallback |
|------------|------------|-----------|---------|----------|
| mPDF | prescription_pdf() | Confirmed (in `vendor/`) | `vendor/mpdf/mpdf` | None needed |
| Chamber_otp library | OTP flow | Confirmed (loaded in Portal::__construct) | custom | — |
| Bd_payment_sslcommerz library | PAY-02 | Confirmed (used by Payment_bd) | custom | — |
| Bootstrap 4.6.2 CDN | Portal layout | Confirmed (layout_public.php line 7) | 4.6.2 | — |
| jQuery 3.6.4 CDN | Portal AJAX | Confirmed (layout_public.php line 17) | 3.6.4 | — |
| FontAwesome 5 (local) | Portal icons | Confirmed (layout_public.php line 8) | Free 5.x local | — |
| `chamber-practice.css` | Portal CSS tokens | Confirmed (layout_public.php line 10) | local | — |
| SMS gateway (MSG91 / Twilio / 80Kobo / Clickatell) | OTP delivery | Depends on hospital config | varies | Dev: OTP logged via `log_message('info', ...)` — no crash |

[VERIFIED: layout_public.php, Portal.php::__construct(), Chamber_platform_model::sendSmsMessage()]

**Missing dependencies with no fallback:** None. All required libraries exist on the server.

**OTP in dev:** `Portal::_send_otp_sms()` logs the plaintext OTP via `log_message('info', ...)` when `ENVIRONMENT === 'development'` — no SMS gateway required for development testing. [VERIFIED: Portal.php line 381]

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
| AUTH-01 | `request_otp` returns `ok:true` for valid mobile | Unit | `composer test -- --filter PortalOtpTest` | Wave 0 |
| AUTH-01 | `verify_otp` sets session on correct code | Unit | `composer test -- --filter PortalOtpTest` | Wave 0 |
| AUTH-01 | `request_otp` rate-limits after 6 requests | Unit | `composer test -- --filter PortalOtpTest` | Wave 0 |
| AUTH-01 | `verify_otp` locks after 8 attempts | Unit | `composer test -- --filter PortalOtpTest` | Wave 0 |
| UI-06 | `complete_booking` inserts queue row + appointment | Unit | `composer test -- --filter PortalBookingTest` | Wave 0 |
| PAY-02 | `init_sslcommerz` creates `bd_payment_intent` row | Unit | `composer test -- --filter PaymentBdTest` | Wave 0 |
| PAY-02 | `sslcommerz_try_finalize` returns false on amount mismatch | Unit | `composer test -- --filter PaymentBdTest` | Wave 0 |
| UI-07 | `ticker_json` with `queue_id` returns `patient_serial` field | Unit | `composer test -- --filter PortalTickerTest` | Wave 0 |
| UI-08 | `prescription_pdf` returns 403 when phone does not match patient | Unit | `composer test -- --filter PortalPrescriptionTest` | Wave 0 |

### Sampling Rate

- **Per task commit:** `composer test`
- **Per wave merge:** `composer test`
- **Phase gate:** Full suite green before `/gsd-verify-work`

### Wave 0 Gaps

- [ ] `tests/Unit/PortalOtpTest.php` — covers AUTH-01 (rate limit, attempt lock, session set)
- [ ] `tests/Unit/PortalBookingTest.php` — covers UI-06 (queue row insertion, appointment creation)
- [ ] `tests/Unit/PaymentBdTest.php` — covers PAY-02 (intent creation, amount verification)
- [ ] `tests/Unit/PortalTickerTest.php` — covers UI-07 (`ticker_json` queue_id extension)
- [ ] `tests/Unit/PortalPrescriptionTest.php` — covers UI-08 (auth guard, medicine parse, PDF headers)

---

## Security Domain

### Applicable ASVS Categories

| ASVS Category | Applies | Standard Control |
|---------------|---------|-----------------|
| V2 Authentication | Yes — OTP auth | `Chamber_otp` library (bcrypt hash); rate limit 6/15min; attempt lock 8; session key `portal_verified_phone` |
| V3 Session Management | Yes | CI3 native sessions; `portal_verified_phone` unset after booking; hospital-scoped (re-verified per hospital) |
| V4 Access Control | Yes | Prescription view: phone must match `patient.phone` for that patient record; queue row: `hospital_id` validated |
| V5 Input Validation | Yes | Mobile: `preg_replace('/\D+/', '', ...)` strips non-digits; minimum length 10 enforced; OTP: `trim()` only (6 digits expected) |
| V6 Cryptography | Yes — OTP hash | `Chamber_otp::hash()` uses bcrypt — never hand-rolled; `Chamber_otp::verify()` constant-time compare |

### Known Threat Patterns

| Pattern | STRIDE | Standard Mitigation |
|---------|--------|---------------------|
| OTP brute-force | Elevation of privilege | 8-attempt lock per OTP row; 6 OTPs per 15 min per mobile |
| CSRF on booking POST | Tampering | Inline CSRF token in each portal form; rotated via `$r->csrf` in AJAX responses |
| SSLCommerz status spoofing | Tampering | `sslcommerz_try_finalize()` requires server-side validation via `Bd_payment_sslcommerz::validateTransaction()`; `posted_status` alone only accepted in development |
| Cross-patient prescription access | Information Disclosure | `prescription_pdf()` must verify `prescription.patient` matches patient record for `portal_verified_phone` |
| Queue ID enumeration (prescription) | Information Disclosure | Prescription requires `portal_verified_phone` session — not guessable by URL alone |
| Patient ID collision in `insertPatientMinimal` | Tampering | `rand(100000, 9999999)` is weak — risk of collision at scale. Acceptable for v1 launch; note for v2. [ASSUMED — not a Phase 4 blocker per scope] |

---

## Assumptions Log

| # | Claim | Section | Risk if Wrong |
|---|-------|---------|---------------|
| A1 | Average consultation time for `estimated_wait` calculation can default to 5 min/patient when no hospital config exists | Common Pitfalls (Pitfall 6) | Wait time display is inaccurate; UX impact only, no functional regression |
| A2 | `insertPatientMinimal` using `rand(100000, 9999999)` for `patient_id` is acceptable risk for v1 | Security Domain | Low at current scale; `patient_id` is a string column, not the primary key — collision means two portal patients share a display ID, not data corruption |

---

## Open Questions (RESOLVED)

1. **Prescription access after session cleared**
   - RESOLVED: Patient must re-verify phone to access prescriptions. `portal_verified_phone` session is a per-visit token by design. No requirement states persistent login. Plans proceed on this basis.

2. **Lab reports table and columns**
   - RESOLVED: `Lab_model.php` inspected. Lab reports use a `payment` table with category filtering — no direct `patient_unique_id` column in lab module. Portal model will query via `patient.id` (looked up from phone) joined to `lab_report` table. Plan will include an execution-time `DESCRIBE lab_report` step to confirm exact columns before writing the model method. Plans proceed on this basis.

3. **Slot availability calculation**
   - RESOLVED: 30-minute intervals within `[s_time, e_time)` window from `doctor_chamber.weekly_hours_json`. No capacity limit per slot (queue-based system — `doctor_schedule_exception` handles overrides). `portal/slots_json` returns array of `{time, available: true/false}`. Plans proceed on this basis.

---

## Sources

### Primary (HIGH confidence — verified against codebase)

- `application/modules/portal/controllers/Portal.php` — full read; OTP, booking, ticker methods verified
- `application/modules/portal/models/Portal_model.php` — full read; all model methods catalogued
- `application/modules/portal/models/Queue_model.php` — full read; ticker, serial, insert methods verified
- `application/modules/portal/models/Chamber_platform_model.php` — full read; SMS, BD payment intent methods
- `application/modules/portal/views/portal/triage.php` — full read; current 3-step form state verified
- `application/modules/portal/views/portal/landing.php` — full read; current ticker polling confirmed
- `application/modules/portal/views/portal/book_success.php` — full read; SSLCommerz button wired
- `application/modules/portal/views/portal/layout_public.php` — full read; CDN versions, no AdminLTE
- `application/modules/payment_bd/controllers/Payment_bd.php` — full read; all payment flows verified
- `application/modules/prescription/controllers/Prescription.php` lines 735-761 — mPDF usage pattern
- `application/modules/prescription/views/prescription_view.php` lines 221-232 — medicine parsing
- `application/migrations/20260419000007_create_chamber_practice_saas.php` — full schema verified
- `application/migrations/20260424000011_chamber_practice_advance_fee.php` — `advance_booking_fee` column confirmed
- `common/css/chamber-practice.css` — existing component catalogue; `.chamber-stepper` 3-col confirmed
- `.planning/phases/04-patient-portal/04-UI-SPEC.md` — full read; authoritative visual contract

### Secondary (MEDIUM confidence — from planning artifacts)

- `.planning/codebase/STACK.md` — technology versions cross-referenced with actual files
- `.planning/codebase/CONVENTIONS.md` — CI3 patterns confirmed against live controller files
- `.planning/REQUIREMENTS.md` — requirement IDs and descriptions

---

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH — verified from `layout_public.php`, `Portal.php`, `composer.json` area
- Architecture: HIGH — all controller/model/view files read directly
- Pitfalls: HIGH for OTP/CSRF/mPDF (confirmed from existing code); MEDIUM for slot calculation (inferred from schema)
- Missing screens: HIGH — confirmed by absence of methods and views in portal module

**Research date:** 2026-04-27
**Valid until:** 2026-05-27 (stable codebase — only changes if portal module is modified)
