# Doctor Chamber Management System — Project Guide

## Project Overview

Multi-tenant SaaS for doctor chamber and hospital management in Bangladesh. Built on CodeIgniter 3 (HMVC), PHP 8.1+, MySQL, AdminLTE 3, Bootstrap 4, jQuery. ~90% complete — in active production hardening phase.

**Core Value:** Real doctors actively using the system to manage their daily patient queue.

## GSD Workflow

This project uses Get Shit Done (GSD) for structured execution.

**Planning artifacts:** `.planning/`
**Current status:** See `.planning/STATE.md`
**Roadmap:** `.planning/ROADMAP.md`
**Requirements:** `.planning/REQUIREMENTS.md`

### Workflow Commands

```
/gsd-discuss-phase N   — gather context before planning
/gsd-plan-phase N      — create PLAN.md for a phase
/gsd-execute-phase N   — execute all plans in a phase
/gsd-progress          — check current state and next step
```

**Next step:** Production — follow `../docs/ship-to-production/README.md` (UAT, staging/prod deploy). GSD phases 1–6 are complete; see `.planning/STATE.md`.

## Architecture

- **Framework:** CodeIgniter 3 with WireDesignz MX HMVC extension
- **Modules:** `application/modules/` — 70+ feature modules, each with controllers/models/views
- **Auth:** Ion Auth library (`application/libraries/Ion_auth.php`)
- **DB:** MySQL via CI Query Builder (`$this->db->...`)
- **Frontend:** AdminLTE 3, Bootstrap 4, jQuery, DataTables, Select2, SweetAlert2

## Key Constraints

### Security — Non-Negotiable for Production
- Phase 1 MUST complete before any portal UI ships to production
- Patient medical data — high security bar required
- See `.planning/codebase/CONCERNS.md` for full list of findings

### Code Conventions
- Controllers extend `MX_Controller` (HMVC) or `CI_Controller`
- Models extend `CI_Model`; use `$this->db->...` Query Builder only — no raw queries
- Use `$this->db->like()` for search — NEVER string interpolation in LIKE clauses (SQL injection)
- CSRF token must be included in all AJAX POST requests
- All new code follows existing module structure: `application/modules/{name}/controllers/`, `models/`, `views/`

### Testing
- PHPUnit tests in `tests/Unit/` — run with `composer test`
- No automated tests exist for most modules — add unit tests for security-critical logic

## Phase Execution Rules

1. **Read PLAN.md files** in `.planning/phases/NN-name/` before executing any phase
2. **Security first** — Phase 1 and Phase 2 must complete before Phase 3+
3. **Commit atomically** — one commit per completed plan task
4. **No debug code in production** — `echo print_r()`, `die()`, `error_reporting(0)` are banned
5. **Environment config** — credentials go in `.env` or environment variables, never hardcoded

## Environment

- **Local dev:** Laragon (nginx config in `nginx-laragon-snippet.conf`)
- **Production:** Apache/LiteSpeed shared hosting, Bangladesh
- **Timezone:** `Asia/Dhaka`
- **PHP:** 8.1+ (LiteSpeed compatible — no `php_value` in `.htaccess`)
- **DB credentials:** `CI_DB_HOST`, `CI_DB_USER`, `CI_DB_PASSWORD`, `CI_DB_NAME` env vars
