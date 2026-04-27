# Project State

## Project Reference

See: .planning/PROJECT.md (updated 2026-04-27)

**Core value:** Real doctors actively using the system to manage their daily patient queue
**Current focus:** Phase 5 - Doctor Portal

## Current Position

Phase: 5 of 6 (Doctor Portal)
Plan: 0 of 7 in current phase
Status: Ready to execute Phase 5
Last activity: 2026-04-28 - Phases 1-4 implemented and validated; Phase 4 placeholder tests converted to green static assertions

Progress: [#######---] 68%

## Performance Metrics

**Velocity:**
- Total plans completed: 28
- Average duration: not tracked for imported execution
- Total execution time: not tracked

**By Phase:**

| Phase | Plans | Total | Avg/Plan |
|-------|-------|-------|----------|
| 1. Security Hardening | 10 | 10 | - |
| 2. Code Health | 5 | 5 | - |
| 3. Admin Design System | 7 | 7 | - |
| 4. Patient Portal | 6 | 6 | - |
| 5. Doctor Portal | 0 | 7 | - |
| 6. Assistant Portal + Real-Time | 0 | 6 | - |

**Recent Trend:**
- Last 5 plans: 04-01, 04-02, 04-03, 04-04, 04-05 complete
- Trend: green static validation; manual live checks still required where noted

*Updated after each plan completion*

## Accumulated Context

### Decisions

Decisions are logged in PROJECT.md Key Decisions table.
Recent decisions affecting current work:

- [Pre-Phase 1]: Security phases 1-2 must complete before any portal UI ships to production - patient medical data requires it
- [Pre-Phase 1]: Complete CI3 system; no framework switch - 90% already built
- [Phase 4]: Patient portal payment uses existing `payment_bd` SSLCommerz/bKash queue-intent flow after booking confirmation.

### Pending Todos

- Execute Phase 5 Doctor Portal plans.
- Run live/manual checks for OTP SMS, SSLCommerz sandbox redirect, queue polling, and prescription PDF download before production.

### Blockers/Concerns

- [Phase 4]: OTP SMS delivery depends on existing Twilio/SMS infrastructure being configured and tested.
- [Phase 4]: SSLCommerz/bKash payment requires sandbox/live credentials in `paymentGateway` or environment variables.
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
Stopped at: Phases 1-4 validated; Phase 5 Doctor Portal ready to execute
Resume file: None
