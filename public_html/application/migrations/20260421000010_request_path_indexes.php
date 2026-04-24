<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Adds indexes for the hot request-path lookups used by the global bootstrap hook.
 */
class Migration_Request_path_indexes extends CI_Migration
{
    public function up()
    {
        $this->_safe_index('settings', 'idx_settings_hospital_id', '(`hospital_id`)');
        $this->_safe_index('hospital', 'idx_hospital_ion_user_id', '(`ion_user_id`)');
        $this->_safe_index('hospital', 'idx_hospital_username', '(`username`)');
        $this->_safe_index('users_groups', 'idx_users_groups_user_id', '(`user_id`)');
        $this->_safe_index('patient', 'idx_patient_ion_user_id', '(`ion_user_id`)');
        $this->_safe_index('doctor', 'idx_doctor_ion_user_id', '(`ion_user_id`)');
        $this->_safe_index('superadmin', 'idx_superadmin_ion_user_id', '(`ion_user_id`)');
        $this->_safe_index('email_settings', 'idx_email_settings_type_hospital', '(`type`, `hospital_id`)');
        $this->_safe_index('site_settings', 'idx_site_settings_hospital_id', '(`hospital_id`)');
    }

    public function down()
    {
        $this->_drop_index('site_settings', 'idx_site_settings_hospital_id');
        $this->_drop_index('email_settings', 'idx_email_settings_type_hospital');
        $this->_drop_index('superadmin', 'idx_superadmin_ion_user_id');
        $this->_drop_index('doctor', 'idx_doctor_ion_user_id');
        $this->_drop_index('patient', 'idx_patient_ion_user_id');
        $this->_drop_index('users_groups', 'idx_users_groups_user_id');
        $this->_drop_index('hospital', 'idx_hospital_username');
        $this->_drop_index('hospital', 'idx_hospital_ion_user_id');
        $this->_drop_index('settings', 'idx_settings_hospital_id');
    }

    protected function _safe_index($table, $indexName, $columnsSql)
    {
        if (!$this->db->table_exists($table)) {
            return;
        }

        $q = $this->db->query("SHOW INDEX FROM `{$table}` WHERE Key_name = " . $this->db->escape($indexName));
        if ($q && $q->num_rows() > 0) {
            return;
        }

        $this->db->query("ALTER TABLE `{$table}` ADD INDEX `{$indexName}` {$columnsSql}");
    }

    protected function _drop_index($table, $indexName)
    {
        if (!$this->db->table_exists($table)) {
            return;
        }

        $q = $this->db->query("SHOW INDEX FROM `{$table}` WHERE Key_name = " . $this->db->escape($indexName));
        if ($q && $q->num_rows() > 0) {
            $this->db->query("ALTER TABLE `{$table}` DROP INDEX `{$indexName}`");
        }
    }
}
