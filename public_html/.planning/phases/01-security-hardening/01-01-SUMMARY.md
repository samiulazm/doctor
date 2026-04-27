# Plan 01-01 Summary: API Token and Tenant Isolation

## Completed
- Replaced 4-digit API tokens with `bin2hex(random_bytes(32))`.
- Updated six API lookup endpoints to derive `hospital_id` from the authenticated Ion Auth session.
- Fixed `authenticateNew()` hospital lookup to use the resolved Ion user id.

## Verification
- `SecurityPatch` tests pass.
- `php -l application/modules/api/controllers/Api.php` passes.
