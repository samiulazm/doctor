<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Logs extends MX_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->load->model('logs_model');
        if (!$this->ion_auth->in_group(array('admin', 'superadmin'))) {
            redirect('home/permission');
        }
    }
    function index()
    {
        $this->load->view('home/dashboard');
        $this->load->view('logs');
        $this->load->view('home/footer');
    }
    function getLogs()
    {

        $requestData = $_REQUEST;
        $start = $requestData['start'];
        $limit = $requestData['length'];
        $search = $this->input->post('search')['value'];

        $order = $this->input->post("order");
        $columns_valid = array(
            "0" => "id",
            "1" => "name",
            "2" => "email",
        );
        $values = $this->settings_model->getColumnOrder($order, $columns_valid);
        $dir = $values[0];
        $order = $values[1];
        if ($this->ion_auth->in_group(array('admin'))) {
            if ($limit == -1) {
                if (!empty($search)) {
                    $data['logs'] = $this->logs_model->getLogsBysearch($search, $order, $dir);
                } else {
                    $data['logs'] = $this->logs_model->getLogsWithoutSearch($order, $dir);
                }
            } else {
                if (!empty($search)) {
                    $data['logs'] = $this->logs_model->getLogsByLimitBySearch($limit, $start, $search, $order, $dir);
                } else {
                    $data['logs'] = $this->logs_model->getLogsByLimit($limit, $start, $order, $dir);
                }
            }
        } else {
            if ($limit == -1) {
                if (!empty($search)) {
                    $data['logs'] = $this->logs_model->getLogsBysearchForSuperadmin($search, $order, $dir);
                } else {
                    $data['logs'] = $this->logs_model->getLogsWithoutSearchForSuperadmin($order, $dir);
                }
            } else {
                if (!empty($search)) {
                    $data['logs'] = $this->logs_model->getLogsByLimitBySearchForSuperadmin($limit, $start, $search, $order, $dir);
                } else {
                    $data['logs'] = $this->logs_model->getLogsByLimitForSuperadmin($limit, $start, $order, $dir);
                }
            }
        }
        $count = count($data['logs']);
        $i = 0;
        foreach ($data['logs'] as $log) {
            $i = $i + 1;

            $info[] = array(

                $log->name,
                $log->email,
                $log->role,
                $log->ip_address,
                $log->date_time
            );
        }

        if (!empty($data['logs'])) {
            $output = array(
                "draw" => intval($requestData['draw']),
                "recordsTotal" => $count,
                "recordsFiltered" => $i,
                "data" => $info
            );
        } else {
            $output = array(
                // "draw" => 1,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            );
        }

        echo json_encode($output);
    }

    function transactionLogs()
    {
        $this->load->view('home/dashboard');
        $this->load->view('transaction_logs');
        $this->load->view('home/footer');
    }

    function getTransaction()
    {
        $requestData = $_REQUEST;
        $start = $requestData['start'];
        $limit = $requestData['length'];
        $search = $this->input->post('search')['value'];

        $order = $this->input->post("order");
        $columns_valid = array(
            "0" => "id",
            "1" => "name",
            "2" => "email",
        );
        $values = $this->settings_model->getColumnOrder($order, $columns_valid);
        $dir = $values[0];
        $order = $values[1];

        if ($limit == -1) {
            if (!empty($search)) {
                $data['logs'] = $this->logs_model->getTransactionLogsBysearch($search, $order, $dir);
            } else {
                $data['logs'] = $this->logs_model->getTransactionLogsWithoutSearch($order, $dir);
            }
        } else {
            if (!empty($search)) {
                $data['logs'] = $this->logs_model->getTransactionLogsByLimitBySearch($limit, $start, $search, $order, $dir);
            } else {
                $data['logs'] = $this->logs_model->getTransactionLogsByLimit($limit, $start, $order, $dir);
            }
        }

        $count = count($data['logs']);
        $i = 0;
        foreach ($data['logs'] as $log) {
            $i = $i + 1;
            if ($log->action == 'Added') {
                $action = '<span class="badge badge-success">' . lang('added') . '</span>';
            } elseif ($log->action == 'Added/Deposited') {
                $action = '<span class="badge badge-success">' . lang('added') . ' ' . lang('deposited') . '</span>';
            } elseif ($log->action == 'Updated') {
                $action = '<span class="badge badge-success">' . lang('updated') . '</span>';
            } elseif ($log->action == 'deleted_deposit') {
                $action = '<span class="badge badge-danger">' . lang('deleted') . ' ' . 'Deposit' . '</span>';
            } elseif ($log->action == 'deleted') {
                $action = '<span class="badge badge-danger">' . lang('deleted') . '</span>';
            } else {
                $action = '<span class="badge badge-info">' . lang('updated') . ' ' . lang('deposited') . '</span>';
            }
            $user_name = $this->db->get_where('users', array('id' => $log->user))->row()->username;
            $info[] = array(

                $log->date_time,
                $log->invoice_id,
                $log->patientname,
                $log->deposit_type,
                $log->amount,
                $user_name,
                $action
            );
        }

        if (!empty($data['logs'])) {
            $output = array(
                "draw" => intval($requestData['draw']),
                "recordsTotal" => $count,
                "recordsFiltered" => count($this->logs_model->getTransactionLogs()),
                "data" => $info
            );
        } else {
            $output = array(
                // "draw" => 1,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            );
        }

        echo json_encode($output);
    }

    /**
     * Read-only audit trail (platform config audit_ui_enabled).
     */
    function audit()
    {
        if (!$this->config->item('audit_ui_enabled')) {
            redirect('home/permission');
        }
        $data = array();
        $data['audit_table_ready'] = $this->db->table_exists('audit_log');
        $data['audit_scope'] = $this->ion_auth->in_group(array('superadmin')) ? 'all' : 'hospital';
        $data['platform_release'] = $this->config->item('platform_release');
        $data['audit_log_enabled'] = (bool) $this->config->item('audit_log_enabled');
        $data['audit_total_count'] = null;
        if ($data['audit_table_ready']) {
            $this->load->model('audit_log_model');
            $data['audit_total_count'] = $this->audit_log_model->audit_count_total(
                $this->ion_auth->in_group(array('superadmin'))
            );
        }
        $this->load->view('home/dashboard');
        $this->load->view('audit', $data);
        $this->load->view('home/footer');
    }

    function getAuditJson()
    {
        if (!$this->config->item('audit_ui_enabled')) {
            show_error('Forbidden', 403);
            return;
        }
        $this->load->model('audit_log_model');

        $requestData = $_REQUEST;
        $start = isset($requestData['start']) ? (int) $requestData['start'] : 0;
        $limit = isset($requestData['length']) ? (int) $requestData['length'] : 10;
        $searchRaw = $this->input->post('search');
        $search = (is_array($searchRaw) && isset($searchRaw['value'])) ? $searchRaw['value'] : '';

        $order = $this->input->post('order');
        $columns_valid = array(
            '0' => 'created_at',
            '1' => 'action',
            '2' => 'entity_type',
            '3' => 'entity_id',
            '4' => 'user_id',
            '5' => 'ip_address',
            '6' => 'metadata',
        );
        $values = $this->settings_model->getColumnOrder($order, $columns_valid);
        $dir = $values[0];
        $orderCol = !empty($values[1]) ? $values[1] : 'created_at';

        $superadmin = $this->ion_auth->in_group(array('superadmin'));

        if (!$this->db->table_exists('audit_log')) {
            $output = array(
                'draw' => intval($requestData['draw']),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => array(),
            );
            echo json_encode($output);
            return;
        }

        $total = $this->audit_log_model->audit_count_total($superadmin);
        $filtered = $this->audit_log_model->audit_count_filtered($superadmin, $search);
        $rows = $this->audit_log_model->audit_datatable_rows($start, $limit, $search, $orderCol, $dir, $superadmin);

        $info = array();
        foreach ($rows as $r) {
            $meta = isset($r->metadata) ? (string) $r->metadata : '';
            if (strlen($meta) > 120) {
                $meta = substr($meta, 0, 117) . '...';
            }
            $info[] = array(
                $r->created_at,
                $r->action,
                $r->entity_type !== null && $r->entity_type !== '' ? $r->entity_type : '—',
                $r->entity_id !== null ? $r->entity_id : '—',
                $r->user_id !== null ? $r->user_id : '—',
                $r->ip_address !== null ? $r->ip_address : '—',
                $meta !== '' ? $meta : '—',
            );
        }

        $output = array(
            'draw' => intval($requestData['draw']),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $info,
        );
        echo json_encode($output);
    }
}
