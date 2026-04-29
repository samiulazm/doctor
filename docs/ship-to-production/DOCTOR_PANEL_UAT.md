# Doctor panel production UAT

Use this checklist for the doctor-only release scope after staging deploy and before production promotion.

| # | Check | Staging | Prod | Notes |
|---|-------|---------|------|-------|
| 1 | Doctor login opens `doctor_chamber/dashboard` for a valid doctor profile | [ ] | [ ] | Confirm no permission redirect for assigned doctor |
| 2 | Missing/invalid doctor profile cannot crash JSON endpoints | [ ] | [ ] | Verify `queue_json`, `chart_data_json`, `vitals_json`, `search_json`, `drug_search_json` return empty/error JSON |
| 3 | Dashboard queue refreshes every 12 seconds after a successful load | [ ] | [ ] | Confirm `Live` indicator and updated timestamp |
| 4 | Dashboard queue outage shows `Stale` and recovers without duplicate requests | [ ] | [ ] | Temporarily block request or simulate network failure |
| 5 | Dashboard chart data failure shows retry status and renders after recovery | [ ] | [ ] | Confirm bundled Chart.js is used |
| 6 | Consultation patient search shows success, no-result, and failure status messages | [ ] | [ ] | Test by patient ID and queue date |
| 7 | Live vitals update every 8 seconds and show `Stale` on request failure | [ ] | [ ] | Confirm recovery does not require page reload |
| 8 | Save draft from embedded prescription re-enables buttons after success | [ ] | [ ] | Confirm visible saved message |
| 9 | Save and print posts `rx:saved` with `print_url` and opens print view | [ ] | [ ] | Confirm no legacy non-embed behavior changed |
| 10 | Missing or inaccessible prescription iframe shows visible error and buttons recover | [ ] | [ ] | Reload iframe, block frame, or test before frame ready |

**Tester:** _______________ **Date:** _______________

**Failures:** capture browser console/network evidence and stop production promotion until resolved.
