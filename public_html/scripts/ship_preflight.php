<?php
/**
 * Secret-safe production ship preflight.
 *
 * Usage:
 *   php scripts/ship_preflight.php
 *
 * Prints only presence/status, never secret values.
 */
declare(strict_types=1);

$root = dirname(__DIR__);

function load_env_file(string $path, array &$env): void
{
    if (!is_file($path)) {
        return;
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
        if (!preg_match('/^\s*([^#][^=]+?)\s*=\s*(.*)$/', $line, $matches)) {
            continue;
        }

        $env[trim($matches[1])] = trim($matches[2]);
    }
}

function report(string $key, string $status): void
{
    echo $key . '=' . $status . PHP_EOL;
}

$env = [];
load_env_file(dirname($root) . DIRECTORY_SEPARATOR . '.env', $env);
load_env_file($root . DIRECTORY_SEPARATOR . '.env', $env);

$requiredGroups = [
    'db' => ['CI_DB_HOST', 'CI_DB_USER', 'CI_DB_PASSWORD', 'CI_DB_NAME'],
    'ftp' => ['FTP_HOST', 'FTP_USER', 'FTP_PASS', 'FTP_REMOTE_DIR'],
    'sslcommerz' => ['SSLCOMMERZ_STORE_ID', 'SSLCOMMERZ_STORE_PASSWORD'],
    'bkash' => ['BKASH_APP_KEY', 'BKASH_APP_SECRET', 'BKASH_USERNAME', 'BKASH_PASSWORD'],
];

$ok = true;
foreach ($requiredGroups as $group => $keys) {
    foreach ($keys as $key) {
        $present = array_key_exists($key, $env) && trim((string) $env[$key]) !== '';
        report($group . '.' . $key, $present ? 'present' : 'missing');
        $ok = $ok && $present;
    }
}

if (!extension_loaded('mysqli') && !extension_loaded('pdo_mysql')) {
    report('db.driver', 'missing_mysqli_or_pdo_mysql');
    exit(1);
}

$dbKeysPresent = true;
foreach ($requiredGroups['db'] as $key) {
    $dbKeysPresent = $dbKeysPresent && array_key_exists($key, $env) && trim((string) $env[$key]) !== '';
}

if (!$dbKeysPresent) {
    report('db.connect', 'skipped_missing_env');
    exit(1);
}

$usingPdo = !extension_loaded('mysqli') && extension_loaded('pdo_mysql');
$pdo = null;
$mysqli = null;

if ($usingPdo) {
    try {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            (string) $env['CI_DB_HOST'],
            (int) ($env['CI_DB_PORT'] ?? 3306),
            (string) $env['CI_DB_NAME']
        );
        $pdo = new PDO($dsn, (string) $env['CI_DB_USER'], (string) $env['CI_DB_PASSWORD']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Throwable $e) {
        report('db.connect', 'failed');
        exit(1);
    }
} else {
    mysqli_report(MYSQLI_REPORT_OFF);
    $mysqli = @new mysqli(
        (string) $env['CI_DB_HOST'],
        (string) $env['CI_DB_USER'],
        (string) $env['CI_DB_PASSWORD'],
        (string) $env['CI_DB_NAME'],
        (int) ($env['CI_DB_PORT'] ?? 3306)
    );

    if ($mysqli->connect_errno) {
        report('db.connect', 'failed');
        exit(1);
    }
}

report('db.connect', 'ok');

$migrationVersion = 'missing';
if ($usingPdo && $pdo instanceof PDO) {
    $migrations = $pdo->query("SHOW TABLES LIKE 'migrations'");
    if ($migrations && $migrations->fetchColumn() !== false) {
        $versionResult = $pdo->query("SELECT version FROM migrations ORDER BY version DESC LIMIT 1");
        $migrationVersion = (string) ($versionResult ? $versionResult->fetchColumn() : 'missing');
    }
} elseif ($mysqli instanceof mysqli) {
    $migrations = $mysqli->query("SHOW TABLES LIKE 'migrations'");
    if ($migrations && $migrations->num_rows > 0) {
        $versionResult = $mysqli->query("SELECT version FROM migrations ORDER BY version DESC LIMIT 1");
        if ($versionResult && ($row = $versionResult->fetch_assoc())) {
            $migrationVersion = (string) $row['version'];
        }
    }
}
report('db.migration_version', $migrationVersion);
$ok = $ok && $migrationVersion === '20260427000012';

$requiredTables = [
    'doctor_portal_profile',
    'doctor_chamber',
    'chamber_serial_queue',
    'chamber_queue_ticker',
    'booking_otp_session',
    'visit_vital',
    'patient_practice_tag',
    'doctor_schedule_exception',
    'prescription_print_template',
    'prescription_favorite',
    'subscription_plan',
    'doctor_subscription',
    'sms_credit_ledger',
    'usage_analytics_event',
    'referral_lab_event',
    'medicine_reminder',
    'bd_payment_intent',
];

$foundTables = 0;
foreach ($requiredTables as $table) {
    if ($usingPdo && $pdo instanceof PDO) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $present = $stmt->fetchColumn() !== false;
    } else {
        $escaped = $mysqli->real_escape_string($table);
        $result = $mysqli->query("SHOW TABLES LIKE '{$escaped}'");
        $present = $result && $result->num_rows > 0;
    }

    if ($present) {
        $foundTables++;
    } else {
        report('db.table.' . $table, 'missing');
    }
}

report('db.required_tables', $foundTables . '/' . count($requiredTables));
$ok = $ok && $foundTables === count($requiredTables);

if ($usingPdo && $pdo instanceof PDO) {
    $stmt = $pdo->prepare("SHOW COLUMNS FROM `chamber_serial_queue` LIKE ?");
    $stmt->execute(['is_emergency']);
    $hasEmergencyColumn = $stmt->fetchColumn() !== false;
} else {
    $emergencyColumn = $mysqli->query("SHOW COLUMNS FROM `chamber_serial_queue` LIKE 'is_emergency'");
    $hasEmergencyColumn = $emergencyColumn && $emergencyColumn->num_rows > 0;
}
report('db.chamber_serial_queue.is_emergency', $hasEmergencyColumn ? 'present' : 'missing');
$ok = $ok && $hasEmergencyColumn;

report('ship_preflight', $ok ? 'pass' : 'fail');
exit($ok ? 0 : 1);
