<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('audit_log')) {
    /**
     * Write one audit row if platform config and DB table exist.
     *
     * @param string $action e.g. invoice.create
     * @param string|null $hospitalId Optional tenant (session may be unset during auth/login).
     * @param int|null $userId Optional actor (e.g. null for failed login before user is known).
     */
    function audit_log($action, $entityType = null, $entityId = null, $metadata = null, $hospitalId = null, $userId = null)
    {
        $CI = &get_instance();
        if (!$CI->config->item('audit_log_enabled')) {
            return false;
        }
        $CI->load->model('audit_log_model');
        return $CI->audit_log_model->insert_entry($action, $entityType, $entityId, $metadata, $hospitalId, $userId);
    }
}
