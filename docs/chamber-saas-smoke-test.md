# Chamber / hospital production readiness smoke test

Use this checklist in each environment (about **5 minutes** per run). Mark each item as **Pass**, **Fail**, or **N/A** (only when the feature is intentionally not shipped in that build).

---

## 0) Ops (60–90s)

- [ ] **Migrations**: `php index.php migrate` completes with no errors; current version matches the latest migration file in this repo (e.g. `20260419120000` on branch `cursor/continue-rx-sms-voice-cds-efe1`). **N/A** for a fixed “target `20260421000009`” unless that migration exists in your fork/branch.
- [ ] **Cron**: Expected jobs for this product are installed and last-run time advances (or logs show execution). **N/A** for `cronjobs/chamberMedicineReminders` if chamber SaaS is not deployed in this codebase.
- [ ] **Secrets**: SMS + payment credentials are set per hospital; one test SMS and one sandbox payment (if available).

---

## 1) Patient portal (~90s)

*(Full chamber patient portal: use **N/A** if not shipped in this build.)*

- [ ] **Public landing**: Doctor photo, specialty, and book CTA (e.g. Book Serial).
- [ ] **Chamber toggle**: With multiple chambers, switching changes availability/context.
- [ ] **Live queue**: Ticker shows current serial for the selected chamber.
- [ ] **OTP**: Wrong OTP rejected; valid OTP allows the next step.
- [ ] **Triage**: Three steps complete (identity → symptoms → attachments); attachments optional but work when used.
- [ ] **Advance fee**: Gateway redirect/callback marks booking paid (or clear error path).
- [ ] **My prescriptions**: List loads; open one PDF.
- [ ] **Lab vault**: Uploaded or integrated reports visible.
- [ ] **Medicine reminder**: Patient sees reminders or cron-driven notification path verified once.

---

## 2) Doctor portal (~90s)

- [ ] **Dashboard**: Today’s appointments; checked-in vs pending matches desk state.
- [ ] **Revenue**: Totals reflect at least one paid consultation/procedure in test data.
- [ ] **Consultation room**: Split view (history left, Rx pad right); embedded prescription iframe + preselected patient if using that flow (**N/A** if chamber room not in build).
- [ ] **Favorites / one-click**: Add favorite and insert into Rx quickly.
- [ ] **Drug search**: Ranked results (**Pass** if Select2 search feels well ordered; optional **Pass** for `medicineInteractions` alerts when `interaction_rules_json` is populated after migration).
- [ ] **Signature**: PDF export includes stamped signature.
- [ ] **CRM**: Search by phone or name opens the correct patient.
- [ ] **Tags**: High risk / Follow-up / VIP visible on the patient record.
- [ ] **Schedules / vacation**: Weekly chamber hours save; vacation/closure reflects on portal and booking (**N/A** if not in build).
- [ ] **Template builder**: Header/footer changes appear in print/PDF.

---

## 3) Assistant portal (~60s)

- [ ] **Check-in**: One action marks arrived; doctor queue updates without manual refresh or within the expected polling interval.
- [ ] **Manual booking**: Over-the-phone booking creates a serial in the queue.
- [ ] **Vitals**: BP / pulse / weight appear on the doctor side quickly (same refresh/poll contract as production).
- [ ] **Queue control**: Emergency bump moves patient to top (drag-and-drop or equivalent).
- [ ] **SMS broadcaster**: Bulk template sends to a small test list (subset of queue).
- [ ] **Billing**: Mark paid (cash / mobile banking); reflected in doctor/revenue views if wired.
- [ ] **Print after save**: **Pass** if assistant polls `prescription/assistantPoll?id=&since_version=` and opens `print_url` after the doctor saves (requires migration `rx_content_version`). **N/A** for strict server-push print until implemented.

---

## 4) Super admin (~30s)

- [ ] **Subscriptions / SaaS fees**: Visible per doctor (**N/A** if not in build).
- [ ] **SMS credits**: Balance or usage visible (**N/A** if not in build).
- [ ] **Usage analytics**: “Most active” or equivalent updates with test activity (**N/A** if not in build).

---

## 5) Value-add (~30s)

- [ ] **Referral loop**: “Refer to lab” triggers discount path (SMS) and doctor notification when the report is ready, or the shipped subset aligned with patient chamber / lab vault (**N/A** if not in build).

---

## Scope notes (this repo / branch)

| Area | Note |
|------|------|
| **Voice** | Optional Twilio voice for **single-patient** SMS send exists on some builds; bulk/AI voice may still be **N/A**. |
| **Drug intelligence** | Ranked search + optional JSON interaction hints after migration; licensed formulary CDS may still be **N/A**. |
| **Auto-print** | Polling + `assistantPoll` + `print_url` is the supported path; true push print **N/A** until implemented. |

---

## Run metadata (optional)

| Field | Value |
|--------|--------|
| Environment | |
| Date/time | |
| Tester | |
| Build/commit | |
| Notes | |
