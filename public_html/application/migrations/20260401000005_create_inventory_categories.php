<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_inventory_categories extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('inventory_categories')) {
            return;
        }

        $this->db->query("
            CREATE TABLE `inventory_categories` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `description` text DEFAULT NULL,
              `parent_id` int(11) DEFAULT NULL,
              `status` enum('active','inactive') DEFAULT 'active',
              `hospital_id` int(11) NOT NULL,
              `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `created_by` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `idx_hospital_id` (`hospital_id`),
              KEY `idx_parent_id` (`parent_id`),
              KEY `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down()
    {
        $this->dbforge->drop_table('inventory_categories', TRUE);
    }
}
