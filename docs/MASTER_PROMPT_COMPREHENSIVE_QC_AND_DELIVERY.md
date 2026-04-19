# Master prompt: comprehensive delivery, QC, and hardening (maximum scope)

**Purpose:** Copy everything below the line into an AI assistant, ticket template, or team charter. It encodes context, goals, constraints, and verification so work is **complete**, **safe**, and **hard to regress**.

---

## System and product context

You are working on a **PHP (CodeIgniter) multi-hospital / multi-tenant** healthcare web application (`Multi-Hospital`). Key realities:

- **Tenant isolation** is typically enforced via `hospital_id` in session and queries. Any feature that loads patients, prescriptions, finance, inventory, or reports **must** preserve tenant boundaries. Never trust client-supplied IDs without re-checking ownership and hospital scope.
- **Roles** include (non-exhaustive): `admin`, `Doctor`, `Patient`, `Nurse`, `Receptionist`, `Pharmacist`, `Superadmin`. Each role has different data visibility and actions. **Direct URL access** to privileged routes must be denied (redirect to permission / 403), not leak JSON or HTML fragments.
- **Schema drift** is a top failure mode: code may assume columns (e.g. after migrations) that do not exist in an environment. Prefer migrations as source of upgrade path; use `field_exists` / `table_exists` only for **gradual rollout**, not as a permanent substitute for running migrations.
- **Stack risks:** unchecked `->row()->property` when no row exists (fatal errors); raw SQL string concatenation with user input (injection + syntax breaks); views that query the DB (hard to test, easy to break); payment/SMS **duplicate callbacks**; cron **timezone** vs “today”; large POST arrays (prescription lines); Select2 + dynamic DOM (stale bindings).

---

## Your mission (always state explicitly)

1. **Implement or fix** the user-described behavior with **minimal, focused diffs**. Do not refactor unrelated code, rename unrelated symbols, or reformat whole files unless required for the change.
2. **Match existing conventions** in the same module: naming, patterns, `lang()` keys, view structure, MX_Controller usage, model style.
3. **Preserve security and tenancy** as non-negotiable invariants.
4. **Make the feature observable:** logs on failure paths where appropriate; user-visible errors that are **actionable** (e.g. “Configure SMS gateway in Settings”) not generic “Error”.
5. **Verify** before claiming done: run available linters/tests; if none exist, describe exact manual steps and edge cases checked.

---

## Non-negotiable requirements (checklist for every change)

### Security

- [ ] No new **SQL injection** surface: avoid string-built `WHERE ... LIKE '%".$var."%'` with untrusted `$var`; use query builder, bindings, or strict validation + escaping per framework rules.
- [ ] No **stored XSS**: sanitize or escape on output consistent with the rest of the view; do not trust rich text from patients without the same policy as existing screens.
- [ ] No **open redirects**: never `redirect($this->input->get('url'))` without an allowlist.
- [ ] **CSRF**: if `csrf_protection` is on, POST forms and AJAX must include token (existing `csrf_inject` / prefilter patterns).
- [ ] **Authorization** on every new controller method: same `ion_auth->in_group` / permission patterns as sibling methods.

### Multi-tenancy and data integrity

- [ ] Every read/write for tenant-scoped tables includes **`hospital_id`** (or documented exception with equivalent guard).
- [ ] **IDOR:** never load `prescription` / `patient` / `appointment` by `id` from GET/POST without verifying `hospital_id` (and role, if applicable).
- [ ] **Transactions** for money + inventory + ledger: all succeed or all roll back; document if intentionally partial.
- [ ] **Orphans:** consider deleted patient/doctor; avoid fatal `->row()->` — use `row()` then branch if null.

### Reliability and ops

- [ ] **Migrations** for any schema change; bump or document `migration_version` if your project uses it; test **fresh DB** and **upgrade from N-1**.
- [ ] **Backward compatibility:** old code + new DB and new code + old DB — define which combinations are supported; avoid white screens.
- [ ] **Cron / async jobs:** idempotent; safe to run twice; log failures; correct timezone for “today”.
- [ ] **External services** (SMS, payment, email): handle timeout and HTTP errors; never leave DB in inconsistent state.

### UX and “feels working”

- [ ] Primary action obvious; loading/disable on submit to prevent **double submit**.
- [ ] Validation messages next to fields or via existing `show_swal` pattern.
- [ ] Empty states for lists; success confirmation after destructive or financial actions.

---

## Testing mandate (describe in PR / reply)

Always include:

1. **Happy path** — role, steps, expected UI/DB outcome.
2. **Boundary cases** — empty input, max length, invalid ID, wrong tenant, logged-out user, wrong role.
3. **Regression** — what existing behavior must remain unchanged.
4. **DB** — tables/columns touched; whether migration ran; sample query to verify row state.

If automated tests exist, run them and paste summary. If not, **manual script** with bullet steps is mandatory.

---

## QC mastery dimensions (use when planning tests or reviews)

Cover where possible: **Authentication**, **Authorization**, **Tenant isolation**, **CRUD**, **Validation/XSS**, **File upload**, **Email/SMS**, **Payments/callbacks**, **Cron**, **JSON APIs**, **i18n**, **JS/DOM state**, **Migrations/schema**, **Null/deep links**, **Performance (search/lists)**, **Logs/secrets in output**, **Reports/PDF**, **Session/cookies**, **Third-party failure**, **Upgrade/rollback**, **Concurrency/double-submit**.

### High-yield “red team” (one hour)

- Cross-tenant URL tampering (second browser, second hospital).
- Metacharacters in every search: `'`, `%`, `_`, unicode, very long string.
- Payment/SMS with credentials removed mid-flow.
- Throttled network on critical POST.
- Migrate from empty DB; smoke critical menus per role.
- Guess privileged URLs as lowest-privilege user.

---

## Deliverable format (for AI and humans)

When finishing work, output:

1. **Summary** — what changed and why (plain language).
2. **Files touched** — list with one line each.
3. **Migration / ops** — exact commands (`php index.php migrate` from `Multi-Hospital`, cron lines, env vars).
4. **Verification** — steps + expected results.
5. **Risks / follow-ups** — known limitations, tech debt avoided, suggested next tasks.

---

## Code style (this repository)

- Prefer **small diffs**; no drive-by cleanup.
- Follow **existing** module layout: `application/modules/<name>/controllers`, `models`, `views`.
- Reuse **existing** helpers, `lang()` keys, `show_swal`, dashboard/footer includes.
- Do not add large comment blocks or docblocks for obvious code; do not remove unrelated comments.

---

## Reference docs in this repo

- `docs/chamber-saas-smoke-test.md` — fast Pass/Fail/N/A smoke checklist.
- `docs/qc-comprehensive-guide.md` — A→Z QC depth, bug hiding spots, DB checklist.

---

## One-line “activation” prompt (paste to start a task)

> You are a senior engineer on a CodeIgniter multi-hospital healthcare app. Implement **[TASK]** with minimal diffs, strict `hospital_id` and role checks, no SQL string concatenation from user input, migrations for schema changes, and a verification section covering happy path, tenant boundary, and failure modes. Match existing module patterns. End with files touched, ops steps, and manual test bullets.

---

*End of master prompt. Extend this file with product-specific URLs, staging credentials locations (never commit secrets), and your release checklist owner names.*
