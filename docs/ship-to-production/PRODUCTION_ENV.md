# Production environment assembly

Place production secrets in **`/home/USER/.env`** (one directory **above** `public_html`) on cPanel. The app loads that file first, then `public_html/.env` for overrides. Never commit filled `.env` files.

## 1. Generate `CI_ENCRYPTION_KEY`

On any machine with OpenSSL:

```bash
openssl rand -hex 32
```

Or PHP one-liner (from `public_html`):

```bash
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```

Paste the result as `CI_ENCRYPTION_KEY=...` (no quotes).

## 2. Required variables (copy into your host `.env`)

```ini
# Core
CI_ENV=production
CI_ENCRYPTION_KEY=<paste openssl output>
CI_BASE_URL=https://your-domain.com/
# CI_BASE_URL_STRICT=1

# Database
CI_DB_HOST=127.0.0.1
CI_DB_USER=your_db_user
CI_DB_PASSWORD=your_db_password
CI_DB_NAME=your_db_name
CI_DB_PORT=3306

# Chamber SaaS — SSLCommerz (live: SSLCOMMERZ_SANDBOX=0)
SSLCOMMERZ_STORE_ID=
SSLCOMMERZ_STORE_PASSWORD=
SSLCOMMERZ_SANDBOX=0

# Chamber SaaS — bKash (live: BKASH_SANDBOX=0)
BKASH_APP_KEY=
BKASH_APP_SECRET=
BKASH_USERNAME=
BKASH_PASSWORD=
BKASH_SANDBOX=0

# OTP / SMS — use Twilio or values configured in Admin SMS settings (verify panel matches env if applicable)

# Optional — AI modules only
# OPENAI_API_KEY=
```

## 3. Firewall / callbacks

- Whitelist SSLCommerz IPN: route under `payment_bd` for `sslcommerz_ipn` (CSRF excluded in `application/config/config.php`).
- Configure bKash callback URLs per gateway docs for your live app URL.

## 4. Verification

After upload, confirm PHP sees variables (temporary `phpinfo()` in a protected admin-only page, or check application logs). Ensure `.env` file permissions are **600** on the server.

From `public_html/`, run the secret-safe launch preflight:

```bash
php scripts/ship_preflight.php
```

It checks required env keys, PHP MySQL driver availability (`mysqli` or `pdo_mysql`), database connectivity, migration version `20260427000012`, required chamber tables, and `chamber_serial_queue.is_emergency`.
