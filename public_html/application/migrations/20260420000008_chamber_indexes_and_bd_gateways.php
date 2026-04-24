<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Performance indexes for chamber SaaS + seed SSLCommerz / bKash rows in paymentGateway per hospital.
 */
class Migration_Chamber_indexes_and_bd_gateways extends CI_Migration
{
    public function up()
    {
        $this->_safe_index('patient', 'idx_chamber_patient_phone', '(`hospital_id`, `phone`(32))');
        $this->_safe_index('appointment', 'idx_chamber_appt_doctor_date', '(`hospital_id`, `doctor`, `date`)');
        if ($this->db->table_exists('chamber_serial_queue')) {
            $this->_safe_index('chamber_serial_queue', 'idx_chamber_queue_day', '(`doctor_id`, `chamber_id`, `queue_date`, `status`)');
        }

        if (!$this->db->table_exists('paymentGateway')) {
            return;
        }
        $template = $this->db->order_by('id', 'asc')->limit(1)->get('paymentGateway')->row_array();
        if (empty($template)) {
            return;
        }
        unset($template['id']);
        $hospitals = $this->db->select('id')->get('hospital')->result();
        foreach ($hospitals as $h) {
            foreach (array('SSLCommerz', 'bKash') as $gwName) {
                if ($this->db->get_where('paymentGateway', array('hospital_id' => $h->id, 'name' => $gwName))->num_rows() > 0) {
                    continue;
                }
                $row = $template;
                $row['hospital_id'] = $h->id;
                $row['name'] = $gwName;
                if (isset($row['status'])) {
                    $row['status'] = 'Deactive';
                }
                foreach (array('merchant_key', 'salt', 'secret', 'public_key', 'APIUsername', 'APIPassword', 'APISignature', 'publish') as $k) {
                    if (array_key_exists($k, $row)) {
                        $row[$k] = '';
                    }
                }
                $this->db->insert('paymentGateway', $row);
            }
        }
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

    public function down()
    {
        if ($this->db->table_exists('chamber_serial_queue')) {
            $this->_drop_index('chamber_serial_queue', 'idx_chamber_queue_day');
        }
        $this->_drop_index('appointment', 'idx_chamber_appt_doctor_date');
        $this->_drop_index('patient', 'idx_chamber_patient_phone');
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
