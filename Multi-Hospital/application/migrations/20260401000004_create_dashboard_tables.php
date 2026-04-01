<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_dashboard_tables extends CI_Migration
{
    public function up()
    {
        if (!$this->db->table_exists('dashboard_settings')) {
            $this->db->query("
                CREATE TABLE `dashboard_settings` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `dashboard_type` enum('executive','clinical','financial','operational') NOT NULL,
                  `widgets` text DEFAULT NULL,
                  `layout` text DEFAULT NULL,
                  `refresh_interval` int(11) DEFAULT 300,
                  `hospital_id` int(11) NOT NULL,
                  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `idx_user_id` (`user_id`),
                  KEY `idx_hospital_id` (`hospital_id`),
                  KEY `idx_dashboard_type` (`dashboard_type`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->db->table_exists('dashboard_metrics_cache')) {
            $this->db->query("
                CREATE TABLE `dashboard_metrics_cache` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `metric_key` varchar(255) NOT NULL,
                  `metric_value` text NOT NULL,
                  `hospital_id` int(11) NOT NULL,
                  `expires_at` timestamp NOT NULL,
                  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `unique_metric_hospital` (`metric_key`, `hospital_id`),
                  KEY `idx_hospital_id` (`hospital_id`),
                  KEY `idx_expires_at` (`expires_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->db->table_exists('dashboard_alerts')) {
            $this->db->query("
                CREATE TABLE `dashboard_alerts` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `alert_type` enum('info','warning','danger','success') NOT NULL,
                  `title` varchar(255) NOT NULL,
                  `message` text NOT NULL,
                  `dashboard_type` enum('executive','clinical','financial','operational') NOT NULL,
                  `priority` enum('low','medium','high','critical') DEFAULT 'medium',
                  `is_active` tinyint(1) DEFAULT 1,
                  `hospital_id` int(11) NOT NULL,
                  `created_by` int(11) DEFAULT NULL,
                  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `idx_hospital_id` (`hospital_id`),
                  KEY `idx_dashboard_type` (`dashboard_type`),
                  KEY `idx_priority` (`priority`),
                  KEY `idx_is_active` (`is_active`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->db->table_exists('dashboard_widgets')) {
            $this->db->query("
                CREATE TABLE `dashboard_widgets` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `widget_name` varchar(255) NOT NULL,
                  `widget_type` varchar(100) NOT NULL,
                  `widget_config` text DEFAULT NULL,
                  `dashboard_type` enum('executive','clinical','financial','operational') NOT NULL,
                  `is_active` tinyint(1) DEFAULT 1,
                  `sort_order` int(11) DEFAULT 0,
                  `hospital_id` int(11) NOT NULL,
                  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `idx_hospital_id` (`hospital_id`),
                  KEY `idx_dashboard_type` (`dashboard_type`),
                  KEY `idx_is_active` (`is_active`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->db->table_exists('dashboard_analytics')) {
            $this->db->query("
                CREATE TABLE `dashboard_analytics` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `dashboard_type` enum('executive','clinical','financial','operational') NOT NULL,
                  `action` varchar(100) NOT NULL,
                  `details` text DEFAULT NULL,
                  `ip_address` varchar(45) DEFAULT NULL,
                  `user_agent` text DEFAULT NULL,
                  `hospital_id` int(11) NOT NULL,
                  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `idx_user_id` (`user_id`),
                  KEY `idx_hospital_id` (`hospital_id`),
                  KEY `idx_dashboard_type` (`dashboard_type`),
                  KEY `idx_action` (`action`),
                  KEY `idx_created_at` (`created_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }

    public function down()
    {
        $this->dbforge->drop_table('dashboard_analytics', TRUE);
        $this->dbforge->drop_table('dashboard_widgets', TRUE);
        $this->dbforge->drop_table('dashboard_alerts', TRUE);
        $this->dbforge->drop_table('dashboard_metrics_cache', TRUE);
        $this->dbforge->drop_table('dashboard_settings', TRUE);
    }
}
