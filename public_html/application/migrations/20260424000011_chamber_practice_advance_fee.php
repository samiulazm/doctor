<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Chamber_practice_advance_fee extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('doctor_portal_profile') && !$this->db->field_exists('advance_booking_fee', 'doctor_portal_profile')) {
            $this->db->query("ALTER TABLE `doctor_portal_profile` ADD `advance_booking_fee` decimal(12,2) NOT NULL DEFAULT 0.00 AFTER `booking_enabled`");
        }
    }

    public function down()
    {
        if ($this->db->table_exists('doctor_portal_profile') && $this->db->field_exists('advance_booking_fee', 'doctor_portal_profile')) {
            $this->db->query("ALTER TABLE `doctor_portal_profile` DROP COLUMN `advance_booking_fee`");
        }
    }
}
