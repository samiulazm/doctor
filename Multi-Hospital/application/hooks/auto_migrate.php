<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auto-migrate hook.
 *
 * Runs pending CI migrations once per deployment.  A small file-based lock
 * (application/cache/migration_lock) prevents the check from firing on every
 * single request — it only re-checks when the lock file is missing or older
 * than the newest migration file.
 */
function auto_migrate()
{
    $CI =& get_instance();

    $lockFile  = APPPATH . 'cache/migration_ran';
    $migrDir   = APPPATH . 'migrations/';

    // If the migrations directory doesn't exist or is empty, nothing to do.
    if (!is_dir($migrDir)) {
        return;
    }

    $migrations = glob($migrDir . '*.php');
    if (empty($migrations)) {
        return;
    }

    // Find the newest migration file's mtime.
    $latestMtime = 0;
    foreach ($migrations as $f) {
        $mt = filemtime($f);
        if ($mt > $latestMtime) {
            $latestMtime = $mt;
        }
    }

    // If the lock file exists and is newer than the latest migration, skip.
    if (is_file($lockFile) && filemtime($lockFile) >= $latestMtime) {
        return;
    }

    // Run migrations.
    $CI->load->library('migration');

    if ($CI->migration->latest() === FALSE) {
        log_message('error', 'Auto-migrate failed: ' . $CI->migration->error_string());
    } else {
        // Touch the lock file so we don't re-run until a new migration lands.
        @file_put_contents($lockFile, date('Y-m-d H:i:s'));
        log_message('info', 'Auto-migrate completed successfully.');
    }
}
