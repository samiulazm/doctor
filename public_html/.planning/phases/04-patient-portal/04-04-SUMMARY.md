# 04-04 Summary — Queue Status Screen

## Completed

- Added `Portal::queue($queue_id)` and `portal/queue.php`.
- Extended `ticker_json` to return `patient_serial` and `estimated_wait` when `queue_id` is provided.
- Preserved the existing landing ticker response shape for calls without `queue_id`.
- Added 15-second polling with visibility pause and the "You are next" notice.

## Verification

- Static checks confirm queue view IDs, polling, visibility handling, and controller fields are present.
- Full `composer test` passes.
