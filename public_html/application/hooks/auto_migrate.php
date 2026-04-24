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

    $stateFile = APPPATH . 'cache/migration_ran';
    $lockFile = APPPATH . 'cache/migration.lock';
    $migrDir = APPPATH . 'migrations/';

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

    // Fast-path skip before attempting to lock.
    if (is_file($stateFile) && filemtime($stateFile) >= $latestMtime) {
        return;
    }

    $lockHandle = @fopen($lockFile, 'c');
    if ($lockHandle === false) {
        log_message('error', 'Auto-migrate failed: unable to open migration lock file.');
        return;
    }

    if (!flock($lockHandle, LOCK_EX)) {
        fclose($lockHandle);
        log_message('error', 'Auto-migrate failed: unable to acquire migration lock.');
        return;
    }

    try {
        clearstatcache(true, $stateFile);
        if (is_file($stateFile) && filemtime($stateFile) >= $latestMtime) {
            return;
        }

        // Run migrations only while holding the lock.
        $CI->load->library('migration');

        if ($CI->migration->latest() === FALSE) {
            log_message('error', 'Auto-migrate failed: ' . $CI->migration->error_string());
            return;
        }

        // Touch the state file so later requests can skip migration work.
        @file_put_contents($stateFile, date('Y-m-d H:i:s'));
        log_message('info', 'Auto-migrate completed successfully.');
    } finally {
        flock($lockHandle, LOCK_UN);
        fclose($lockHandle);
    }
}
