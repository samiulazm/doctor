# Testing

**Analysis Date:** 2026-04-27

## Test Infrastructure

**Framework:** PHPUnit 10.5 (declared in `composer.json` `require-dev`)

**Test Runner:** PHPUnit CLI via Composer script

**Config:** `phpunit.xml.dist` at project root
- Bootstrap: `tests/bootstrap.php` — loads `vendor/autoload.php` only; no CI framework boot
- Cache directory: `.phpunit.cache/`
- Flags: `failOnRisky="true"`, `failOnWarning="true"`, `colors="true"`
- Only one suite defined: `Unit` — maps to `tests/Unit/` with `*Test.php` suffix

**Run Commands:**
```bash
composer test                   # Run all tests (PHPUnit with phpunit.xml.dist)
composer ci                     # Full CI: composer validate + test + lint-php
composer lint-php               # PHP syntax check via scripts/lint-php.php
```

**Windows convenience runner:** `run-tests.ps1`
- Detects Laragon PHP, Docker, or global PHP/composer in that order
- Falls back to Docker image `php:8.1-cli` via `scripts/docker-test.sh` if Laragon PHP < 8.1

**PHP requirement:** >=8.1 (per `composer.json`)

**Autoload (test namespace):**
```
Tests\       → tests/
Tests\Unit\  → tests/Unit/
Tests\Support\ → tests/Support/
```

## Coverage

**Unit tests:** Yes — `tests/Unit/` (5 test files)

**Integration tests:** No — none present. No database connection is made in any test.

**E2E tests:** No — no browser automation framework (no Selenium, Cypress, Behat, etc.)

**CI pipeline:** No `.github/workflows/` directory exists. No automated test execution on push/PR is configured.

## Test Files

| File | What It Tests |
|------|---------------|
| `tests/Unit/BootstrapTest.php` | Verifies the `Tests\Support` namespace autoloads correctly |
| `tests/Unit/HtmlEscapeHelperTest.php` | `Tests\Support\HtmlEscapeHelper::escape()` — HTML special chars, ampersand, quotes |
| `tests/Unit/PhpSyntaxSmokeTest.php` | PHP syntax (`php -l`) of key files: `index.php`, `asset_helper.php`, `Health.php`, `Audit_log_model.php`, `Logs` controller |
| `tests/Unit/DatabaseBootstrapTest.php` | `ci_database_missing_env_keys()` and `ci_database_connection_settings()` from `application/config/database_bootstrap.php`; also tests `ci_load_dotenv()` |
| `tests/Unit/HealthHelperTest.php` | `ci_health_ready_payload()` from `application/helpers/health_helper.php` — connected/disconnected DB status payloads |

## Test Patterns

**All test files use `declare(strict_types=1)`.**

**Class declarations:** All test classes are `final`.

**Method naming:** snake_case with `test_` prefix — `test_escapes_html_special_characters()`, `test_production_requires_all_database_env_vars()`.

**Assertions used:**
- `assertTrue()` / `assertFalse()`
- `assertSame()` — strict equality, preferred over `assertEquals()`
- `assertStringContainsString()` / `assertStringNotContainsString()`
- `assertArrayNotHasKey()` / `assertArrayHasKey()`
- `assertFileExists()`
- Custom assertion `assertFilePassesPhpLint()` (private helper in `PhpSyntaxSmokeTest`)

**setUp / tearDown pattern** (see `DatabaseBootstrapTest`):
```php
private array $originalEnv = array();

protected function setUp(): void {
    parent::setUp();
    // Snapshot and unset env vars
}

protected function tearDown(): void {
    // Restore env vars
    parent::tearDown();
}
```

**Requiring application files in tests** (no CI framework boot):
```php
require_once dirname(__DIR__, 2) . '/application/config/database_bootstrap.php';
require_once dirname(__DIR__, 2) . '/application/helpers/health_helper.php';
```
Pure PHP functions and helpers are directly `require_once`'d into tests. CI-coupled code (models, controllers that use `$this->db`) is not tested.

**Temporary file pattern** (`DatabaseBootstrapTest`):
```php
$tmp = tempnam(sys_get_temp_dir(), 'dotenv');
try {
    file_put_contents($tmp, "...content...");
    ci_load_dotenv($tmp);
    // assertions
} finally {
    @unlink($tmp);
}
```

**Support class** — `tests/Support/HtmlEscapeHelper.php`:
- Small pure-PHP utility class used as both a test fixture and a stand-in for view escaping logic
- Wraps `htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8')`

## Gaps

**No controller tests.** All 70+ module controllers extend `MX_Controller` and are tightly coupled to the CI framework, database, and session — none are tested.

**No model tests.** Models extend `CI_model` and call `$this->db` directly. Without a test DB or mock, they cannot be unit-tested. No in-memory SQLite fixtures exist.

**No view tests.** Views mix PHP and HTML with direct `get_instance()` calls; no template rendering tests exist.

**No integration/smoke tests for routes.** No HTTP-level test verifies that a URL returns expected status codes or output.

**SQL injection in search methods is untested.** The raw LIKE concatenation pattern in `Appointment_model`, `Patient_model`, and similar models has no test for injection vectors.

**No CI pipeline.** There is no `.github/workflows/` config or equivalent — tests are only run manually by a developer.

**Coverage is narrow.** Existing tests cover: bootstrap, HTML escaping utility, database config resolution, dotenv loading, and PHP syntax of ~6 files. The vast majority of application logic has zero test coverage.

**No fixtures or factories.** No factory pattern or seeder exists for generating test data; each test that needs DB state must build it from scratch (currently avoided by skipping DB-connected tests entirely).
