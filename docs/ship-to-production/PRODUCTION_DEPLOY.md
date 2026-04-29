# Production deploy and rollback

## Pre-deploy

1. Confirm staging [UAT_MATRIX.md](UAT_MATRIX.md) all pass.
2. Set live credentials in host `.env` (`SSLCOMMERZ_SANDBOX=0`, `BKASH_SANDBOX=0`) per [PRODUCTION_ENV.md](PRODUCTION_ENV.md).
3. From `public_html/`, run `php scripts/ship_preflight.php`; continue only if it ends with `ship_preflight=pass`.

## Backup (mandatory)

1. **Database:** full mysqldump of production DB to dated file, e.g. `backup_YYYYMMDD_HHMM.sql.gz`.
2. **Files:** tarball of remote `public_html` (cPanel File Manager → Compress, or `tar czf` over SSH if available).

Store backups outside the web root.

## Deploy

1. From local `public_html`: `python deploy.py --dry-run`, then `python deploy.py` (or `--only-changed` if appropriate).
2. Migrations: same target as staging — `migration_version` = `20260427000012` in `application/config/migration.php` (already set in repo). First request with DB write runs pending migrations if `migration_auto_latest` is true.
3. Run `database_chamber_practice_saas_verify.sql` on production DB (from repo root; import via phpMyAdmin or `mysql` CLI).
4. **OPcache / LiteSpeed:** touch `index.php` or restart PHP if the host allows.

## Post-deploy smoke

1. Re-run [UAT_MATRIX.md](UAT_MATRIX.md) **Prod** column with one small live payment each gateway where applicable.
2. Tail server error log ~30 minutes for fatals and autoload errors.

## Rollback

Trigger if any critical UAT fails or site is down:

1. Restore `public_html` from tarball.
2. Restore DB from mysqldump.
3. Optional: maintenance page / DNS hold until verified.

Document outcome in `.planning/STATE.md` and `PROJECT.md` Key Decisions.
