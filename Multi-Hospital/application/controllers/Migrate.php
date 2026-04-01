<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CLI migration controller.
 *
 * Usage:
 *   php index.php migrate          — run all pending migrations
 *   php index.php migrate status   — show current migration version
 *   php index.php migrate rollback — roll back the last migration batch
 */
class Migrate extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // CLI only — block browser access.
        if (!$this->input->is_cli_request()) {
            show_error('This controller is accessible via the command line only.', 403);
        }
    }

    /**
     * Run all pending migrations (default action).
     */
    public function index()
    {
        $this->load->library('migration');

        echo "Running migrations...\n";

        if ($this->migration->latest() === FALSE) {
            echo "ERROR: " . $this->migration->error_string() . "\n";
            exit(1);
        }

        $version = $this->migration->latest();
        echo "Migrations complete. Current version: {$version}\n";
    }

    /**
     * Show current migration version.
     */
    public function status()
    {
        $this->load->library('migration');

        // Read the migrations table directly.
        if ($this->db->table_exists('migrations')) {
            $row = $this->db->get('migrations')->row();
            $version = $row ? $row->version : 0;
        } else {
            $version = 0;
        }

        echo "Current migration version: {$version}\n";

        // List available migration files.
        $files = glob(APPPATH . 'migrations/*.php');
        echo "Available migrations: " . count($files) . "\n";
        foreach ($files as $f) {
            echo "  - " . basename($f) . "\n";
        }
    }

    /**
     * Roll back to version 0 (use with caution).
     */
    public function rollback()
    {
        $this->load->library('migration');

        echo "Rolling back all migrations...\n";

        if ($this->migration->version(0) === FALSE) {
            echo "ERROR: " . $this->migration->error_string() . "\n";
            exit(1);
        }

        echo "Rollback complete.\n";
    }
}
