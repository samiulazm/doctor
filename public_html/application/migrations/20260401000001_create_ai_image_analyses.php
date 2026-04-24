<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_ai_image_analyses extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('ai_image_analyses')) {
            return;
        }

        $this->db->query("
            CREATE TABLE `ai_image_analyses` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `patient_id` int(11) NOT NULL,
              `doctor_id` int(11) NOT NULL,
              `hospital_id` int(11) NOT NULL,
              `image_type` enum('xray','ct_scan','mri','ultrasound','endoscopy','dermatology','ophthalmology','pathology','auto_detect','other') NOT NULL,
              `description` text,
              `image_path` varchar(255) NOT NULL,
              `analysis_result` longtext,
              `created_by` int(11) NOT NULL,
              `updated_by` int(11) DEFAULT NULL,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `patient_id` (`patient_id`),
              KEY `doctor_id` (`doctor_id`),
              KEY `hospital_id` (`hospital_id`),
              KEY `created_by` (`created_by`),
              KEY `updated_by` (`updated_by`),
              KEY `image_type` (`image_type`),
              KEY `created_at` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down()
    {
        $this->dbforge->drop_table('ai_image_analyses', TRUE);
    }
}
