# Ship to production

| Document | Purpose |
|----------|---------|
| [PRODUCTION_ENV.md](PRODUCTION_ENV.md) | Assemble `.env` above `public_html`, encryption key, gateway vars |
| [STAGING_DEPLOY.md](STAGING_DEPLOY.md) | `deploy.py --dry-run`, FTP staging, migrations `20260427000012`, verify SQL |
| [UAT_MATRIX.md](UAT_MATRIX.md) | Manual UAT checklist (staging + production) |
| [DOCTOR_PANEL_UAT.md](DOCTOR_PANEL_UAT.md) | Doctor portal only (chamber dashboard, consultation, embed Rx) |
| [PRODUCTION_DEPLOY.md](PRODUCTION_DEPLOY.md) | Backup, live deploy, rollback |

## One-command preflight

From `public_html/`:

```bash
php scripts/ship_preflight.php
```

The command prints only presence/status, never secret values. It must end with `ship_preflight=pass` before staging or production deploy.
