# Project State

## Project Reference

See: .planning/PROJECT.md (updated 2026-04-27)

**Core value:** Real doctors actively using the system to manage their daily patient queue
**Current focus:** Phase 6 - Assistant Portal + Real-Time

## Current Position

Phase: 6 of 6 (Assistant Portal + Real-Time)
Plan: 0 of 6 in current phase
Status: Ready to execute Phase 6
Last activity: 2026-04-28 - Phase 5 Doctor Portal implemented and validated; DoctorPortal tests green

Progress: [#########-] 85%

## Performance Metrics

**Velocity:**
- Total plans completed: 35
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
| 6. Assistant Portal + Real-Time | 0 | 6 | - |

**Recent Trend:**
- Last 5 plans: 05-02, 05-03, 05-04, 05-05, 05-06 complete
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

### Pending Todos

- Execute Phase 6 Assistant Portal + Real-Time plans.
- Run live/manual checks for OTP SMS, SSLCommerz sandbox redirect, doctor dashboard queue polling, consultation save-and-print, assistant billing, and prescription PDF download before production.

### Blockers/Concerns

- [Phase 4]: OTP SMS delivery depends on existing Twilio/SMS infrastructure being configured and tested.
- [Phase 4]: SSLCommerz/bKash payment requires sandbox/live credentials in `paymentGateway` or environment variables.
- [Phase 5]: Chart.js loads from a pinned CDN and should be checked in the deployment network.
- [Phase 6]: Real-time queue (QUEUE-01) requires choosing between WebSocket and long-poll; shared hosting (Apache/LiteSpeed) may not support persistent WebSocket connections - long-poll may be the safer fallback.

## Deferred Items

| Category | Item | Status | Deferred At |
|----------|------|--------|-------------|
| Mobile | Flutter app | v2 | Project init |
| AI | Prescription suggestion | v3 | Project init |
| Infra | Centralized audit log | Post-launch | Project init |
| Infra | Read replicas / connection pooling | Post-launch | Project init |

## Session Continuity

Last session: 2026-04-28
Stopped at: Phase 5 validated; Phase 6 Assistant Portal + Real-Time ready to execute
Resume file: None
