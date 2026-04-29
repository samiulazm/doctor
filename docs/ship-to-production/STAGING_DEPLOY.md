# Staging deploy (FTP + schema)

## Prerequisites

- Python 3 with `ftplib` (stdlib).
- `FTP_HOST`, `FTP_USER`, `FTP_PASS`, `FTP_REMOTE_DIR` in local `.env` at `public_html/.env` (not committed; copy from `public_html/.env.example` and add FTP block).

## 1. Dry run (required before any upload)

From the **application root** (the folder that contains `index.php` and `deploy.py` — this repo’s `public_html/`):

```bash
cd public_html   # or: already in public_html
php scripts/ship_preflight.php
python deploy.py --dry-run
```

`ship_preflight.php` must end with `ship_preflight=pass`. Review the dry-run file list. The dry run should **not** include internal files such as `.env`, `.env.example`, `.planning/`, `.claude/`, `.vscode/`, `.github/`, `.DS_Store`, `CLAUDE.md`, test suites, or deploy progress state. Use `python deploy.py --only-changed` after the first full deploy to save time.

## 2. Real deploy to staging

```bash
python deploy.py
```

`deploy.py` blocks real uploads when required `FTP_*` values are missing; `--dry-run` still works for file-list validation.

Options: `--skip-assets` (first-time only if AdminLTE already on server), `--resume` after a failed run, `--config-only` for config hotfix.

**Note:** `deploy.py` intentionally skips `.env` — set secrets on the server under `/home/USER/.env` per [PRODUCTION_ENV.md](PRODUCTION_ENV.md).

## 3. Migrations (target version)

Ensure `public_html/application/config/migration.php` has:

```php
$config['migration_version'] = '20260427000012';
```

With `migration_enabled` and `migration_auto_latest` as already configured, loading the app with DB credentials set will apply pending migrations to the `migrations` table.

**Alternative:** import `database_chamber_practice_saas_patch.sql` (repository root, next to `public_html/`) if the migration runner is disabled on the host (not recommended; prefer CI migrations).

## 4. Verify schema on staging

Run on the **staging** MySQL database:

```text
source database_chamber_practice_saas_verify.sql
```

Files live at repository root (sibling to `public_html/`):

- `database_chamber_practice_saas_verify.sql`
- `database_chamber_practice_saas_patch.sql`

Fix any reported mismatches before UAT.
