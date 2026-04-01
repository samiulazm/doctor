<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_treatment_plans extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('treatment_plans')) {
            // Table exists — ensure doctor_id column is present
            if (!$this->db->field_exists('doctor_id', 'treatment_plans')) {
                $this->db->query("
                    ALTER TABLE `treatment_plans`
                    ADD COLUMN `doctor_id` int(11) NOT NULL AFTER `patient_id`,
                    ADD KEY `doctor_id` (`doctor_id`)
                ");
            }
            return;
        }

        $this->db->query("
            CREATE TABLE `treatment_plans` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `patient_id` int(11) NOT NULL,
              `doctor_id` int(11) NOT NULL,
              `symptoms` text NOT NULL,
              `symptom_analysis` longtext,
              `doctor_input` text,
              `test_results` text,
              `treatment_plan` longtext,
              `prescription` longtext,
              `created_by` int(11) NOT NULL,
              `updated_by` int(11) DEFAULT NULL,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
              `hospital_id` int(11) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `patient_id` (`patient_id`),
              KEY `doctor_id` (`doctor_id`),
              KEY `created_by` (`created_by`),
              KEY `updated_by` (`updated_by`),
              KEY `hospital_id` (`hospital_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down()
    {
        $this->dbforge->drop_table('treatment_plans', TRUE);
    }
}
