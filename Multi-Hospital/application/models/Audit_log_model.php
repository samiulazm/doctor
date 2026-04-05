<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Optional audit trail (enable after running migration 20260401000006).
 */
class Audit_log_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $action       Short action key, e.g. patient.update
     * @param string|null $entityType
     * @param int|null $entityId
     * @param array|string|null $metadata JSON-serializable
     */
    public function insert_entry(
        $action,
        $entityType = null,
        $entityId = null,
        $metadata = null,
        $hospitalId = null,
        $userId = null
    ) {
        if (!$this->db->table_exists('audit_log')) {
            return false;
        }

        $this->load->library('session');
        if ($userId === null && function_exists('get_instance')) {
            $CI = &get_instance();
            if (isset($CI->ion_auth) && $CI->ion_auth->logged_in()) {
                $userId = $CI->ion_auth->get_user_id();
            }
        }
        if ($hospitalId === null && $this->session->userdata('hospital_id')) {
            $hospitalId = $this->session->userdata('hospital_id');
        }

        $row = array(
            'hospital_id' => $hospitalId,
            'user_id' => $userId,
            'action' => (string) $action,
            'entity_type' => $entityType !== null ? (string) $entityType : null,
            'entity_id' => $entityId !== null ? (int) $entityId : null,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => substr((string) $this->input->user_agent(), 0, 255),
            'metadata' => $metadata !== null ? json_encode($metadata) : null,
            'created_at' => date('Y-m-d H:i:s'),
        );

        return $this->db->insert('audit_log', $row);
    }

    /**
     * Total rows visible to this admin (scoped), no search filter.
     */
    public function audit_count_total($superadmin)
    {
        if (!$this->db->table_exists('audit_log')) {
            return 0;
        }
        $this->db->from('audit_log');
        if (!$superadmin) {
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        }
        return (int) $this->db->count_all_results();
    }

    /**
     * Row count with scope + optional search (DataTables recordsFiltered).
     */
    public function audit_count_filtered($superadmin, $search)
    {
        if (!$this->db->table_exists('audit_log')) {
            return 0;
        }
        $this->db->from('audit_log');
        if (!$superadmin) {
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        }
        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('action', $search);
            $this->db->or_like('entity_type', $search);
            $this->db->or_like('ip_address', $search);
            $this->db->or_like('metadata', $search);
            $this->db->group_end();
        }
        return (int) $this->db->count_all_results();
    }

    /**
     * Paged rows for audit viewer (DataTables).
     *
     * @param string|null $orderColumn one of audit_log columns
     * @return object[]
     */
    public function audit_datatable_rows($start, $length, $search, $orderColumn, $dir, $superadmin)
    {
        if (!$this->db->table_exists('audit_log')) {
            return array();
        }
        $allowed = array('id', 'created_at', 'action', 'entity_type', 'entity_id', 'user_id', 'ip_address', 'metadata');
        $col = in_array($orderColumn, $allowed, true) ? $orderColumn : 'created_at';
        $dir = ($dir === 'asc') ? 'asc' : 'desc';

        $this->db->from('audit_log');
        if (!$superadmin) {
            $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        }
        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('action', $search);
            $this->db->or_like('entity_type', $search);
            $this->db->or_like('ip_address', $search);
            $this->db->or_like('metadata', $search);
            $this->db->group_end();
        }
        $this->db->order_by($col, $dir);
        if ($length > 0) {
            $this->db->limit((int) $length, (int) $start);
        }
        $q = $this->db->get();
        return $q ? $q->result() : array();
    }
}
