<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_audit_log extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('audit_log')) {
            return;
        }

        $this->db->query("
            CREATE TABLE `audit_log` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `hospital_id` varchar(64) DEFAULT NULL,
              `user_id` int(11) unsigned DEFAULT NULL,
              `action` varchar(128) NOT NULL,
              `entity_type` varchar(64) DEFAULT NULL,
              `entity_id` int(11) DEFAULT NULL,
              `ip_address` varchar(45) DEFAULT NULL,
              `user_agent` varchar(255) DEFAULT NULL,
              `metadata` longtext,
              `created_at` datetime NOT NULL,
              PRIMARY KEY (`id`),
              KEY `hospital_id` (`hospital_id`),
              KEY `user_id` (`user_id`),
              KEY `action` (`action`),
              KEY `entity` (`entity_type`,`entity_id`),
              KEY `created_at` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down()
    {
        $this->dbforge->drop_table('audit_log', true);
    }
}
