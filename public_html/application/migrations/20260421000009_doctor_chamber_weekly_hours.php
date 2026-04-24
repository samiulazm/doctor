<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Adds per-chamber weekly hours (Mon–Sun) JSON for scheduling UI.
 */
class Migration_Doctor_chamber_weekly_hours extends CI_Migration
{
    public function up()
    {
        if (!$this->db->table_exists('doctor_chamber')) {
            return;
        }
        if ($this->db->field_exists('weekly_hours_json', 'doctor_chamber')) {
            return;
        }
        $this->db->query("
            ALTER TABLE `doctor_chamber`
            ADD COLUMN `weekly_hours_json` text DEFAULT NULL AFTER `is_active`
        ");
    }

    public function down()
    {
        if (!$this->db->table_exists('doctor_chamber')) {
            return;
        }
        if (!$this->db->field_exists('weekly_hours_json', 'doctor_chamber')) {
            return;
        }
        $this->db->query('ALTER TABLE `doctor_chamber` DROP COLUMN `weekly_hours_json`');
    }
}
