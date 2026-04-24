<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Chamber / practice SaaS extension — domain tables for serial queue, OTP,
 * triage, vitals, subscriptions, BD payments, referrals, and analytics.
 */
class Migration_Create_chamber_practice_saas extends CI_Migration
{
    public function up()
    {
        if (!$this->db->table_exists('doctor_portal_profile')) {
            $this->db->query("
                CREATE TABLE `doctor_portal_profile` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `hospital_id` int(11) NOT NULL,
                  `doctor_id` int(11) NOT NULL,
                  `public_slug` varchar(191) NOT NULL,
                  `specialty_label` varchar(255) DEFAULT NULL,
                  `hero_image` varchar(500) DEFAULT NULL,
                  `booking_enabled` tinyint(1) NOT NULL DEFAULT 1,
                  `signature_image` varchar(500) DEFAULT NULL,
                  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `uniq_doctor` (`doctor_id`),
                  UNIQUE KEY `uniq_hospital_slug` (`hospital_id`, `public_slug`),
                  KEY `idx_hospital` (`hospital_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->db->table_exists('doctor_chamber')) {
            $this->db->query("
                CREATE TABLE `doctor_chamber` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `hospital_id` int(11) NOT NULL,
                  `doctor_id` int(11) NOT NULL,
                  `name` varchar(255) NOT NULL,
                  `address` text DEFAULT NULL,
                  `phone` varchar(50) DEFAULT NULL,
                  `sort_order` int(11) NOT NULL DEFAULT 0,
                  `is_active` tinyint(1) NOT NULL DEFAULT 1,
                  `weekly_hours_json` text DEFAULT NULL,
                  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `idx_doctor` (`doctor_id`, `hospital_id`, `is_active`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->db->table_exists('chamber_serial_queue')) {
            $this->db->query("
                CREATE TABLE `chamber_serial_queue` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `hospital_id` int(11) NOT NULL,
                  `doctor_id` int(11) NOT NULL,
                  `chamber_id` int(11) NOT NULL,
                  `queue_date` date NOT NULL,
                  `serial_number` int(11) NOT NULL,
                  `sort_position` decimal(12,4) NOT NULL DEFAULT 0.0000,
                  `status` enum('pending','arrived','serving','done','cancelled') NOT NULL DEFAULT 'pending',
                  `patient_id` int(11) DEFAULT NULL,
                  `guest_name` varchar(255) DEFAULT NULL,
                  `guest_phone` varchar(50) DEFAULT NULL,
                  `appointment_id` int(11) DEFAULT NULL,
                  `triage_json` longtext DEFAULT NULL,
                  `advance_fee_amount` decimal(12,2) DEFAULT NULL,
                  `advance_payment_status` enum('none','pending','paid') NOT NULL DEFAULT 'none',
                  `remarks` varchar(1000) DEFAULT NULL,
                  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `idx_queue_day` (`doctor_id`, `chamber_id`, `queue_date`, `status`),
                  KEY `idx_hospital` (`hospital_id`),
                  KEY `idx_sort` (`doctor_id`, `chamber_id`, `queue_date`, `sort_position`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->db->table_exists('chamber_queue_ticker')) {
            $this->db->query("
                CREATE TABLE `chamber_queue_ticker` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `hospital_id` int(11) NOT NULL,
                  `doctor_id` int(11) NOT NULL,
                  `chamber_id` int(11) NOT NULL,
                  `queue_date` date NOT NULL,
                  `current_queue_id` int(11) DEFAULT NULL,
                  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `uniq_chamber_day` (`doctor_id`, `chamber_id`, `queue_date`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->db->table_exists('booking_otp_session')) {
            $this->db->query("
                CREATE TABLE `booking_otp_session` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `hospital_id` int(11) NOT NULL,
                  `mobile` varchar(32) NOT NULL,
                  `otp_hash` varchar(128) NOT NULL,
                  `expires_at` datetime NOT NULL,
                  `attempts` int(11) NOT NULL DEFAULT 0,
                  `verified_at` datetime DEFAULT NULL,
                  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `idx_mobile_hospital` (`hospital_id`, `mobile`, `expires_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->db->table_exists('visit_vital')) {
            $this->db->query("
                CREATE TABLE `visit_vital` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `hospital_id` int(11) NOT NULL,
                  `queue_id` int(11) NOT NULL,
                  `bp_systolic` varchar(20) DEFAULT NULL,
                  `bp_diastolic` varchar(20) DEFAULT NULL,
                  `pulse` varchar(20) DEFAULT NULL,
                  `weight_kg` varchar(20) DEFAULT NULL,
                  `recorded_by` int(11) DEFAULT NULL,
                  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `idx_queue` (`queue_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->db->table_exists('patient_practice_tag')) {
            $this->db->query("
                CREATE TABLE `patient_practice_tag` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `hospital_id` int(11) NOT NULL,
                  `doctor_id` int(11) NOT NULL,
                  `patient_id` int(11) NOT NULL,
                  `tag` enum('high_risk','follow_up','vip') NOT NULL,
                  `notes` varchar(500) DEFAULT NULL,
                  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `uniq_tag` (`doctor_id`, `patient_id`, `tag`),
                  KEY `idx_patient` (`patient_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->db->table_exists('doctor_schedule_exception')) {
            $this->db->query("
                CREATE TABLE `doctor_schedule_exception` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `hospital_id` int(11) NOT NULL,
                  `doctor_id` int(11) NOT NULL,
                  `chamber_id` int(11) DEFAULT NULL,
                  `exception_date` date NOT NULL,
                  `is_closed` tinyint(1) NOT NULL DEFAULT 1,
                  `open_time` varchar(20) DEFAULT NULL,
                  `close_time` varchar(20) DEFAULT NULL,
                  `reason` varchar(500) DEFAULT NULL,
                  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `idx_doctor_date` (`doctor_id`, `exception_date`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->db->table_exists('prescription_print_template')) {
            $this->db->query("
                CREATE TABLE `prescription_print_template` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `hospital_id` int(11) NOT NULL,
                  `doctor_id` int(11) NOT NULL,
                  `header_html` mediumtext DEFAULT NULL,
                  `footer_html` mediumtext DEFAULT NULL,
                  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `uniq_doctor_tpl` (`doctor_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->db->table_exists('prescription_favorite')) {
            $this->db->query("
                CREATE TABLE `prescription_favorite` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `hospital_id` int(11) NOT NULL,
                  `doctor_id` int(11) NOT NULL,
                  `label` varchar(255) NOT NULL,
                  `medicine_lines_json` mediumtext NOT NULL,
                  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `idx_doctor` (`doctor_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->db->table_exists('subscription_plan')) {
            $this->db->query("
                CREATE TABLE `subscription_plan` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `code` varchar(64) NOT NULL,
                  `name` varchar(255) NOT NULL,
                  `interval_unit` enum('monthly','yearly') NOT NULL DEFAULT 'monthly',
                  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
                  `features_json` text DEFAULT NULL,
                  `is_active` tinyint(1) NOT NULL DEFAULT 1,
                  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `uniq_code` (`code`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->db->table_exists('doctor_subscription')) {
            $this->db->query("
                CREATE TABLE `doctor_subscription` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `hospital_id` int(11) NOT NULL,
                  `doctor_id` int(11) NOT NULL,
                  `plan_id` int(11) NOT NULL,
                  `status` enum('active','past_due','cancelled') NOT NULL DEFAULT 'active',
                  `starts_at` date NOT NULL,
                  `ends_at` date DEFAULT NULL,
                  `last_paid_at` date DEFAULT NULL,
                  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `idx_doctor` (`doctor_id`),
                  KEY `idx_status` (`status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->db->table_exists('sms_credit_ledger')) {
            $this->db->query("
                CREATE TABLE `sms_credit_ledger` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `hospital_id` int(11) NOT NULL,
                  `doctor_id` int(11) DEFAULT NULL,
                  `delta` int(11) NOT NULL,
                  `balance_after` int(11) NOT NULL DEFAULT 0,
                  `reason` varchar(255) DEFAULT NULL,
                  `ref_type` varchar(64) DEFAULT NULL,
                  `ref_id` int(11) DEFAULT NULL,
                  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `idx_hospital_doctor` (`hospital_id`, `doctor_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->db->table_exists('usage_analytics_event')) {
            $this->db->query("
                CREATE TABLE `usage_analytics_event` (
                  `id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `hospital_id` int(11) NOT NULL,
                  `doctor_id` int(11) DEFAULT NULL,
                  `user_id` int(11) DEFAULT NULL,
                  `event_type` varchar(64) NOT NULL,
                  `meta_json` text DEFAULT NULL,
                  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `idx_hospital_type` (`hospital_id`, `event_type`, `created_at`),
                  KEY `idx_doctor` (`doctor_id`, `created_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->db->table_exists('referral_lab_event')) {
            $this->db->query("
                CREATE TABLE `referral_lab_event` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `hospital_id` int(11) NOT NULL,
                  `doctor_id` int(11) NOT NULL,
                  `patient_id` int(11) NOT NULL,
                  `discount_code` varchar(64) NOT NULL,
                  `lab_name` varchar(255) DEFAULT NULL,
                  `status` enum('sent','report_ready') NOT NULL DEFAULT 'sent',
                  `patient_phone` varchar(50) DEFAULT NULL,
                  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `report_ready_at` datetime DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `idx_doctor` (`doctor_id`),
                  KEY `idx_code` (`discount_code`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->db->table_exists('medicine_reminder')) {
            $this->db->query("
                CREATE TABLE `medicine_reminder` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `hospital_id` int(11) NOT NULL,
                  `patient_id` int(11) NOT NULL,
                  `medicine_label` varchar(255) NOT NULL,
                  `dose_times_json` text NOT NULL,
                  `is_active` tinyint(1) NOT NULL DEFAULT 1,
                  `last_sent_at` datetime DEFAULT NULL,
                  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `idx_patient` (`patient_id`, `is_active`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->db->table_exists('bd_payment_intent')) {
            $this->db->query("
                CREATE TABLE `bd_payment_intent` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `hospital_id` int(11) NOT NULL,
                  `queue_id` int(11) NOT NULL,
                  `gateway` enum('sslcommerz','bkash') NOT NULL,
                  `amount` decimal(12,2) NOT NULL,
                  `currency` varchar(8) NOT NULL DEFAULT 'BDT',
                  `status` enum('created','success','failed') NOT NULL DEFAULT 'created',
                  `gateway_session_id` varchar(255) DEFAULT NULL,
                  `meta_json` text DEFAULT NULL,
                  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `idx_queue` (`queue_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        // Seed default SaaS plan row if empty
        if ($this->db->table_exists('subscription_plan')) {
            $n = $this->db->count_all('subscription_plan');
            if ((int) $n === 0) {
                $this->db->insert('subscription_plan', array(
                    'code' => 'pro',
                    'name' => 'Professional',
                    'interval_unit' => 'monthly',
                    'price' => 0.00,
                    'features_json' => json_encode(array('queue', 'otp', 'portal')),
                    'is_active' => 1,
                ));
            }
        }
    }

    public function down()
    {
        $tables = array(
            'bd_payment_intent',
            'medicine_reminder',
            'referral_lab_event',
            'usage_analytics_event',
            'sms_credit_ledger',
            'doctor_subscription',
            'subscription_plan',
            'prescription_favorite',
            'prescription_print_template',
            'doctor_schedule_exception',
            'patient_practice_tag',
            'visit_vital',
            'booking_otp_session',
            'chamber_queue_ticker',
            'chamber_serial_queue',
            'doctor_chamber',
            'doctor_portal_profile',
        );
        foreach ($tables as $t) {
            if ($this->db->table_exists($t)) {
                $this->dbforge->drop_table($t, true);
            }
        }
    }
}
