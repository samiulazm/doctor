# Project State

## Project Reference

See: .planning/PROJECT.md (updated 2026-04-27)

**Core value:** Real doctors actively using the system to manage their daily patient queue
**Current focus:** All planned phases executed; production readiness verification remains

## Current Position

Phase: 6 of 6 (Assistant Portal + Real-Time)
Plan: 6 of 6 in current phase
Status: All planned phases complete
Last activity: 2026-04-28 - Phase 6 Assistant Portal + Real-Time implemented and validated; AssistantPortal tests green

Progress: [##########] 100%

## Performance Metrics

**Velocity:**
- Total plans completed: 41
- Average duration: not tracked for imported execution
- Total execution time: not tracked

**By Phase:**

| Phase | Plans | Total | Avg/Plan |
|-------|-------|-------|----------|
| 1. Security Hardening | 10 | 10 | - |
| 2. Code Health | 5 | 5 | - |
| 3. Admin Design System | 7 | 7 | - |
| 4. Patient Portal | 6 | 6 | - |
| 5. Doctor Portal | 7 | 7 | - |
| 6. Assistant Portal + Real-Time | 6 | 6 | - |

**Recent Trend:**
- Last 5 plans: 06-01, 06-02, 06-03, 06-04, 06-05 complete
- Trend: green static validation; manual live checks still required where noted

*Updated after each plan completion*

## Accumulated Context

### Decisions

Decisions are logged in PROJECT.md Key Decisions table.
Recent decisions affecting current work:

- [Pre-Phase 1]: Security phases 1-2 must complete before any portal UI ships to production - patient medical data requires it
- [Pre-Phase 1]: Complete CI3 system; no framework switch - 90% already built
- [Phase 4]: Patient portal payment uses existing `payment_bd` SSLCommerz/bKash queue-intent flow after booking confirmation.
- [Phase 5]: Doctor dashboard and consultation room use long-poll JSON endpoints instead of WebSocket.
- [Phase 5]: Consultation room embeds the existing prescription composer and uses a postMessage callback for save-and-print.
- [Phase 6]: Assistant queue sync uses long-poll plus immediate SortableJS reorder POST for shared-hosting compatibility.
- [Phase 6]: Desk bKash flow initiates inline and reports JSON status without redirecting the assistant away from the queue.
- [Post-Phase 6]: Doctor dashboard charts use the bundled AdminLTE Chart.js asset; assistant queue drag-drop uses local native browser drag/drop with optional SortableJS support if bundled later.

### Pending Todos

- Run live/manual checks for OTP SMS, SSLCommerz sandbox redirect, doctor dashboard queue polling, consultation save-and-print, assistant drag-drop reorder, emergency bump, bKash sandbox initiation, assistant billing, token printing, and prescription PDF download before production.
- Apply database migrations in the target environment, including `20260427000012_chamber_queue_is_emergency.php`.

### Blockers/Concerns

- [Phase 4]: OTP SMS delivery depends on existing Twilio/SMS infrastructure being configured and tested.
- [Phase 4]: SSLCommerz/bKash payment requires sandbox/live credentials in `paymentGateway` or environment variables.
- [Phase 6]: Real bKash success requires sandbox/live credentials and gateway callback configuration.

## Deferred Items

| Category | Item | Status | Deferred At |
|----------|------|--------|-------------|
| Mobile | Flutter app | v2 | Project init |
| AI | Prescription suggestion | v3 | Project init |
| Infra | Centralized audit log | Post-launch | Project init |
| Infra | Read replicas / connection pooling | Post-launch | Project init |

## Session Continuity

Last session: 2026-04-28
Stopped at: All six phases validated; production/live manual verification remains
Resume file: None
