# Chamber SaaS Smoke Test

Use this as a short production-readiness checklist. Treat each line as `Pass`, `Fail`, or `N/A`.

`N/A` is only valid when the item is consciously not shipped yet.

## 0. Ops (60-90 s)

- Migrations: `php index.php migrate` completes with target `20260421000009` or newer if additional migrations have been added.
- Cron: Job for `cronjobs/chamberMedicineReminders` is installed and the last run time advances, or logs show execution.
- Secrets: SMS plus payment (`SSLCommerz` / `bKash`) credentials are set per hospital; run one test SMS and one test payment in sandbox if available.

## 1. Patient Portal (~90 s)

- Public landing: Doctor photo, specialty, and `Book Serial` or equivalent call to action are visible.
- Chamber toggle: With multiple chambers, switching chambers changes availability and context.
- Live queue: Ticker shows current serial for the selected chamber.
- OTP: Wrong OTP is rejected; valid OTP allows the next step.
- Triage: Three-step flow works: identity -> symptoms -> attachments. Attachment upload is optional but functional if used.
- Advance fee: Gateway redirect and callback mark the booking as paid, or the error path is clear.
- My prescriptions: List loads and one PDF opens successfully.
- Lab vault: Uploaded or integrated reports are visible.
- Medicine reminder: Patient sees configured reminders, or the cron-driven notification path is verified once.

## 2. Doctor Portal (~90 s)

- Dashboard: Today’s appointments load; checked-in versus pending matches the assistant or desk state.
- Revenue: Totals reflect at least one paid consultation or procedure in test data.
- Consultation room: Split view works with history on the left and prescription pad on the right; embedded prescription iframe and preselected patient work if that flow is enabled.
- Favorites / one-click: Add a favorite and insert it into the prescription quickly.
- Drug search: Search returns ranked results. Full CDS stays `N/A` until interactions or formulary support exists.
- Signature: PDF export shows the stamped signature.
- CRM: Search by phone or name opens the correct patient.
- Tags: Apply `High risk`, `Follow-up`, or `VIP`; tags are visible on the patient record.
- Schedules / vacation: Weekly chamber hours save; vacation or closure is reflected on the portal and booking flow.
- Template builder: Header and footer changes appear on print and PDF output.

## 3. Assistant Portal (~60 s)

- Check-in: One action marks the patient as arrived; the doctor queue updates without manual refresh, or within the expected poll interval.
- Manual booking: Over-the-phone booking creates a serial in the queue.
- Vitals: BP, pulse, and weight appear on the doctor side quickly, following the same refresh or poll contract used in production.
- Queue: Emergency bump moves the patient to the top, whether by drag-and-drop or the shipped equivalent.
- SMS broadcaster: Bulk template sends to a small test list, ideally a subset of the queue.
- Billing: Mark paid by cash or mobile banking; it reflects in doctor and revenue views if those paths are wired.
- Print manager: After the doctor saves the prescription, the assistant has one-click print. Polling plus tab-open behavior is acceptable for now; strict server-push remains `N/A` until implemented.

## 4. Super Admin (~30 s)

- Subscriptions: SaaS fee status is visible per doctor.
- SMS credits: Balance or usage is visible per doctor or chamber.
- Usage analytics: `Most active` or a similar metric updates after test activity.

## 5. Value-Add (~30 s)

- Referral loop: `Refer to lab` issues the shipped discount path, such as SMS, and sends the doctor notification when the report is ready. If only part of the flow is shipped, verify that subset and align it with `Patient_chamber` and lab vault aggregation behavior.

## Explicit `N/A` Items

- AI / voice broadcaster: Smoke-test only the SMS path; voice remains `N/A`.
- Drug intelligence: Smoke-test search ranking only; full CDS remains `N/A`.
- Auto-print: Smoke-test polling plus tab behavior; server-triggered print on save remains `N/A`.
