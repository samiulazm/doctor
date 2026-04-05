<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Public health checks for load balancers, deploy scripts, and monitoring.
 * Excluded from auth in hooks/required.php (class name: health).
 */
class Health extends CI_Controller
{
    /** @return array<string, string> */
    private function _release_meta()
    {
        $rel = $this->config->item('platform_release');
        if (is_string($rel) && $rel !== '') {
            return array('release' => $rel);
        }
        return array();
    }

    public function index()
    {
        $this->_json(array_merge(array(
            'status' => 'ok',
            'service' => 'multi-hospital',
            'php' => PHP_VERSION,
            'time' => gmdate('c'),
        ), $this->_release_meta()));
    }

    /** Alias for index (same payload). */
    public function ping()
    {
        $this->index();
    }

    /** DB connectivity check (optional query; no secrets in response). */
    public function ready()
    {
        $this->load->database();
        $ok = false;
        $error = null;
        try {
            $this->db->query('SELECT 1 AS ok');
            $ok = $this->db->conn_id !== false;
        } catch (Throwable $e) {
            $error = 'db_error';
        }

        $payload = array_merge(array(
            'status' => $ok ? 'ready' : 'not_ready',
            'database' => $ok ? 'connected' : 'disconnected',
            'time' => gmdate('c'),
        ), $this->_release_meta());
        if ($error !== null) {
            $payload['error'] = $error;
        }

        $this->output->set_status_header($ok ? 200 : 503);
        $this->_json($payload);
    }

    private function _json(array $data)
    {
        $this->output
            ->set_content_type('application/json; charset=UTF-8')
            ->set_output(json_encode($data, JSON_UNESCAPED_SLASHES));
    }
}
