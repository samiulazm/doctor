# UAT matrix (staging and production)

Run on **staging** first; repeat on **production** with minimal real-money tests.

| # | Check | Staging | Prod | Notes |
|---|--------|---------|------|-------|
| 1 | **OTP login (AUTH-01)** — mobile SMS + verify grants session | ☐ | ☐ | Requires Twilio/SMS configured |
| 2 | **SSLCommerz (PAY-02)** — booking advance fee sandbox/live redirect → success | ☐ | ☐ | Prod: one low-value txn; refund if needed |
| 3 | **Doctor dashboard poll (UI-09/10)** — second tab updates counts without reload | ☐ | ☐ | |
| 4 | **Consultation save+print (UI-11)** — Rx save, PDF/postMessage | ☐ | ☐ | |
| 5 | **Assistant drag-drop (UI-12)** — reorder reflects on doctor + patient queue | ☐ | ☐ | |
| 6 | **Emergency bump (UI-12)** — row to top, `is_emergency` set | ☐ | ☐ | |
| 7 | **bKash (PAY-01)** — desk flow returns JSON success | ☐ | ☐ | Sandbox then live credentials |
| 8 | **Billing paid/due (UI-13)** — `mark_queue_fee_paid_ajax`, ledger | ☐ | ☐ | |
| 9 | **Token print (UI-12)** — `token_print.php` correct serial/doctor/chamber | ☐ | ☐ | |
| 10 | **Rx PDF (UI-08)** — portal download opens with meds + advice | ☐ | ☐ | |

**Tester:** _______________ **Date:** _______________

**Failures:** log in issue tracker or `/gsd-insert-phase 6.1` before prod promotion.
