# Comprehensive QC and QC mastery (A → Z)

This guide is written for **hospital / multi-tenant PHP (CodeIgniter) apps** like this codebase. Use it for **release gates**, **exploratory testing**, and **ongoing hardening**.

---

## 1) What “good QC” actually proves

| Layer | Question |
|--------|----------|
| **Correctness** | Does each role see the right data and actions? |
| **Safety** | Can users cross tenants (`hospital_id`), roles, or patient boundaries? |
| **Resilience** | What happens when DB, SMS, payment, or mail is slow, wrong, or down? |
| **Operability** | Migrations, cron, logs, backups — can you recover and explain incidents? |
| **Perception** | Does the product feel **trustworthy** (clear errors, no dead ends, consistent UI)? |

**QC mastering** means designing tests so bugs **cannot hide**: stateful flows, boundaries, concurrency, and schema drift are all first-class.

---

## 2) From A to Z — dimensions to cover

### A — Authentication and session

- Login/logout; remember-me if used; session timeout mid-form.
- Password reset; lockout / rate limits if any.
- **Role matrix**: every menu URL for `admin`, `Doctor`, `Patient`, `Receptionist`, `Nurse`, `Pharmacist` — direct URL access must **403** or redirect, not leak data.

### B — Boundary / tenant (`hospital_id`)

- Two hospitals in staging: user A must **never** see hospital B patients, prescriptions, medicines, finance.
- Copy-paste an ID from another tenant into the URL — must fail safely.

### C — CRUD on high-risk entities

- Patient, appointment, prescription, payment, inventory: create → read → update → delete (or soft-delete) with validation messages.

### D — Data validation and UX

- Required fields, max lengths, invalid dates, XSS strings in text fields (stored and re-displayed escaped).
- File uploads: size, type, path traversal in names.

### E — Email and SMS

- Gateway not configured: user sees **actionable** message, not a white screen.
- Twilio/sandbox: single patient + optional voice — **cost and consent** (only test numbers).

### F — Finance and payments

- SSLCommerz / bKash: success, cancel, failure, **duplicate callback**, amount mismatch.
- Receipts and ledger entries match UI totals.

### G — Cron and background jobs

- Job installed, runs at expected frequency, **idempotent** (re-run does not double-charge or double-notify).
- Timezone: “today” in app vs server.

### H — HTTP / API contracts

- JSON endpoints: auth required, correct `Content-Type`, stable error shape for clients.
- CSRF on POST forms (if enabled): AJAX must still succeed (prefilter / tokens).

### I — Internationalization

- Switch language: no missing `lang()` keys, no broken layouts for longer strings.

### J — JavaScript and UI state

- Double-submit on save buttons; Select2 + dynamic rows (prescription medicines) after add/remove.
- Browser back/forward after POST.

### K — Keys and IDs

- Integer IDs in URLs; **zero**, **negative**, **huge**, **non-numeric** — graceful 404, not SQL errors.

### L — Logs and observability

- Trigger a controlled error; confirm **log_message** / PHP error path does not expose secrets to the browser.

### M — Migrations and schema

- Fresh DB: migrate from zero → latest **without manual SQL**.
- Upgrade from **N-1** → **N** on a copy of production-like data.
- Code references **new columns** (e.g. `rx_content_version`): verify migration applied or code degrades safely (`field_exists` pattern).

### N — Null and empty states

- `->row()->property` when row missing → fatal in PHP; QC every path where user can deep-link to deleted records.
- Empty lists: helpful empty state, not blank tables.

### O — Ownership and workflow

- Doctor can only edit **their** prescriptions if that is the rule; receptionist overrides if allowed.

### P — Performance (lightweight)

- Search on large `medicine` / `patient` tables: acceptable delay, no full table scan in production (EXPLAIN in staging).
- N+1 queries on list pages (many small queries per row).

### Q — Queries and SQL injection

- Any `where("... LIKE '%".$search."%'", NULL, FALSE)` or string-built SQL: test with **`'`**, **`%`**, **`_`**, **unicode**, **very long** input.
- Prefer query builder bindings or escaped fragments; QC should **file bugs** when raw concatenation touches user input.

### R — Reports and exports

- PDF/print: pagination, signatures, RTL if needed, correct hospital header.

### S — Security headers and cookies

- HTTPS-only cookies if production is HTTPS; `httponly`, `secure`, session fixation on privilege change.

### T — Third-party outages

- SMS gateway timeout; payment gateway 500 — user message + no partial inconsistent DB (transactions).

### U — Upgrade / rollback

- Deploy new code with **old** DB briefly (should not white-screen); deploy new DB with **old** code (document incompatibility).

### V — Volume and soak (optional)

- Many rows in queue, many medicines in one Rx — memory and POST size limits.

### W — Web server and PHP

- `max_post_size`, `upload_max_filesize`, `max_execution_time` for heavy imports.

### X — XSS / CSRF / open redirects

- `redirect($this->input->get(...))` patterns; reflected parameters in HTML.

### Y — “Yesterday’s bug” regression

- Every production bug gets a **minimal automated or manual checklist item** so it cannot return silently.

### Z — Zero-downtime mindset

- Feature flags or `field_exists` / `table_exists` guards for gradual rollout.

---

## 3) What makes the app feel *relevant* and *working* (not just “no errors”)

1. **Clear primary actions** on each screen (one obvious next step for the role).
2. **Consistent vocabulary** (same term for “serial”, “appointment”, “visit” everywhere).
3. **Feedback in &lt; 1 s perceived**: loading indicator on slow AJAX; disable double submit.
4. **Errors that teach**: “SMS gateway not selected in Settings” beats “Error”.
5. **Empty and success states** that confirm what happened (“Prescription saved. Print from assistant desk.”).
6. **Mobile-friendly** critical flows (reception desk on tablet).
7. **Trust cues** in health apps: dates with timezone, doctor name on print, audit when sensitive data is viewed.

---

## 4) Where bugs love to hide (this stack)

| Location | Risk |
|----------|------|
| **`->row()->id` without check** | Fatal error if no doctor/patient row — deep links, stale data, wrong `hospital_id`. |
| **Views doing queries** | Hard to test, easy to break when context missing — QC: hit every prescription/patient view as each role. |
| **String SQL with `$search`** | Injection and syntax break with quotes; performance cliff on large tables. |
| **Session `hospital_id` vs URL id** | IDOR if one path forgets to filter. |
| **POST arrays** (`medicine[]`) | Empty array, duplicate ids, tampered ids — server must re-validate. |
| **Migrations not run** | Silent feature half-working or SQL errors when column assumed present without guard. |
| **Payment callbacks** | Duplicate delivery, wrong amount — QC must replay webhooks in sandbox. |
| **Cron + timezone** | “Today’s appointments” wrong for the hospital. |
| **jQuery Select2 + dynamic DOM** | Events not bound after AJAX; stale hidden inputs. |
| **`urlencode` then gateway** | Double encoding or wrong charset for non-ASCII names. |

---

## 5) Database QC checklist (app stops working)

- [ ] **Migration order** and **single source of truth** (`database_tables.sql` vs live DB vs migrations).
- [ ] **Charset**: `utf8mb4` for patient names and addresses; no `????` in PDFs.
- [ ] **Indexes** on foreign keys and high-traffic filters: `hospital_id`, `patient`, `doctor`, `date`.
- [ ] **Constraints**: NOT NULL vs app sending null; enum/string mismatches.
- [ ] **Orphan rows**: prescription pointing to deleted patient — UI and queries.
- [ ] **Large TEXT / JSON** columns: `interaction_rules_json` invalid JSON — app should not fatal when decoding.
- [ ] **Transactions**: money movement + stock + invoice in one transaction where required.
- [ ] **Locks**: long reports vs concurrent writes — timeouts?
- [ ] **Backup/restore drill** on staging quarterly.

---

## 6) “QC mastering” process (how teams level up)

1. **Definition of Done** per story: automated test *or* documented manual steps + owner sign-off.
2. **Risk-based depth**: high-risk stories get exploratory charters (time-boxed, mission-based).
3. **Pair QC**: developer + QA walk boundary cases for 30 minutes before merge.
4. **Staging mirror**: anonymized production volume weekly.
5. **Bug taxonomy**: tag each bug (tenant, SQL, JS, UX, ops) — quarterly review drives training.
6. **Smoke → full regression pyramid**: keep `docs/chamber-saas-smoke-test.md` green every release; expand suite when bugs escape.

---

## 7) Artifacts to maintain

| Artifact | Purpose |
|----------|---------|
| `docs/chamber-saas-smoke-test.md` | Fast release gate |
| This file | Depth, methodology, hidden bug classes |
| Test data script | Repeatable multi-hospital dataset |
| Incident postmortems | New automated checks |

---

## 8) Quick “red team” hour (high yield)

1. Second browser, second hospital user, cross-tenant URL tampering.  
2. SQL metacharacters in every search box.  
3. Kill SMS credentials mid-send.  
4. Submit prescription with network throttled to 2G.  
5. Run migrations on empty DB; then run app.  
6. Open every report as **Patient** via URL guessing.

Anything that **500s**, **leaks data**, or **silently no-ops** is a **release blocker** for a clinical system.
