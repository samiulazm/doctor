<?php

declare(strict_types=1);

require_once __DIR__ . '/../application/config/dotenv_loader.php';

/**
 * Config sanity checks meant for CI and deploy preflight.
 * Keep this script dependency-free (no CI bootstrap) so it can run before vendor install if needed.
 */

function env_present(string $key): bool
{
    return getenv($key) !== false || array_key_exists($key, $_ENV);
}

function env_value(string $key): string
{
    $v = getenv($key);
    if ($v === false && isset($_ENV[$key])) {
        $v = (string) $_ENV[$key];
    }
    return $v === false ? '' : (string) $v;
}

function fail(array $errors): int
{
    foreach ($errors as $e) {
        fwrite(STDERR, "[config-sanity] ERROR: {$e}\n");
    }
    return 1;
}

$env = env_value('CI_ENV');
if ($env === '') {
    $env = 'production';
}

$errors = array();

// Encryption key is required in all non-dev environments.
if ($env !== 'development') {
    $key = env_value('CI_ENCRYPTION_KEY');
    if ($key === '') {
        $errors[] = 'CI_ENCRYPTION_KEY is required (set it in .env or server env).';
    } elseif (strlen($key) < 32) {
        $errors[] = 'CI_ENCRYPTION_KEY must be at least 32 characters.';
    }
}

// DB settings are required in testing/production (development can use local defaults).
if ($env === 'testing' || $env === 'production') {
    foreach (array('CI_DB_HOST', 'CI_DB_USER', 'CI_DB_NAME') as $k) {
        if (env_value($k) === '') {
            $errors[] = "{$k} is required for {$env}.";
        }
    }

    // Password may be empty, but should be explicitly present.
    if (!env_present('CI_DB_PASSWORD')) {
        $errors[] = 'CI_DB_PASSWORD must be present for testing/production (can be empty).';
    }
}

// Writable directories that commonly break runtime when missing perms on deploy.
$paths = array(
    __DIR__ . '/../application/cache',
    __DIR__ . '/../application/cache/sessions',
);

foreach ($paths as $p) {
    if (!is_dir($p)) {
        $errors[] = 'Missing directory: ' . $p;
        continue;
    }
    if (!is_writable($p)) {
        $errors[] = 'Directory is not writable: ' . $p;
    }
}

if (!empty($errors)) {
    exit(fail($errors));
}

fwrite(STDOUT, "[config-sanity] OK ({$env})\n");
exit(0);

