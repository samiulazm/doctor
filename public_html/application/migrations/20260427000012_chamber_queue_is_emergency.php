<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Chamber_queue_is_emergency extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('chamber_serial_queue')
            && !$this->db->field_exists('is_emergency', 'chamber_serial_queue')) {
            $this->db->query(
                "ALTER TABLE `chamber_serial_queue` ADD `is_emergency` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`"
            );
        }
    }

    public function down()
    {
        if ($this->db->table_exists('chamber_serial_queue')
            && $this->db->field_exists('is_emergency', 'chamber_serial_queue')) {
            $this->db->query("ALTER TABLE `chamber_serial_queue` DROP COLUMN `is_emergency`");
        }
    }
}
