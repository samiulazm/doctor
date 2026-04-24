<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('chamber_practice_module_key')) {
    function chamber_practice_module_key()
    {
        return 'chamber_practice';
    }
}

if (!function_exists('chamber_practice_module_aliases')) {
    function chamber_practice_module_aliases()
    {
        return array(
            chamber_practice_module_key(),
            'doctor_chamber',
            'assistant_chamber',
            'patient_chamber',
            'portal',
            'payment_bd',
        );
    }
}

if (!function_exists('chamber_practice_normalize_modules')) {
    function chamber_practice_normalize_modules($modules)
    {
        if (is_string($modules)) {
            $modules = explode(',', $modules);
        }

        if (!is_array($modules)) {
            return array();
        }

        $clean = array();
        foreach ($modules as $module) {
            $module = trim((string) $module);
            if ($module !== '') {
                $clean[] = $module;
            }
        }

        return $clean;
    }
}

if (!function_exists('chamber_practice_resolve_hospital_id')) {
    function chamber_practice_resolve_hospital_id($CI)
    {
        if (isset($CI->hospital_id) && $CI->hospital_id !== null && $CI->hospital_id !== '' && $CI->hospital_id !== 'superadmin') {
            return $CI->hospital_id;
        }

        if (isset($CI->session)) {
            $sid = $CI->session->userdata('hospital_id');
            if ($sid !== null && $sid !== '' && $sid !== 'superadmin') {
                return $sid;
            }

            $site_id = $CI->session->userdata('site_id');
            if ($site_id !== null && $site_id !== '' && $site_id !== 'superadmin') {
                return $site_id;
            }
        }

        return null;
    }
}

if (!function_exists('chamber_practice_modules_have_key')) {
    function chamber_practice_modules_have_key($modules)
    {
        $modules = chamber_practice_normalize_modules($modules);
        foreach (chamber_practice_module_aliases() as $alias) {
            if (in_array($alias, $modules, true)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('chamber_practice_enabled_for_hospital')) {
    function chamber_practice_enabled_for_hospital($CI, $hospital_id = null)
    {
        $hospital_id = ($hospital_id === null || $hospital_id === '') ? chamber_practice_resolve_hospital_id($CI) : $hospital_id;
        if ($hospital_id === null || $hospital_id === '' || $hospital_id === 'superadmin') {
            return false;
        }

        if (!isset($CI->__chamber_practice_module_cache) || !is_array($CI->__chamber_practice_module_cache)) {
            $CI->__chamber_practice_module_cache = array();
        }

        $cache_key = (string) $hospital_id;
        if (!array_key_exists($cache_key, $CI->__chamber_practice_module_cache)) {
            $modules = null;
            if (isset($CI->modules) && is_array($CI->modules) && (string) chamber_practice_resolve_hospital_id($CI) === $cache_key) {
                $modules = $CI->modules;
            } else {
                $row = $CI->db->select('module')->get_where('hospital', array('id' => $hospital_id))->row();
                $modules = ($row && isset($row->module)) ? $row->module : array();
            }

            $CI->__chamber_practice_module_cache[$cache_key] = chamber_practice_modules_have_key($modules);
        }

        return (bool) $CI->__chamber_practice_module_cache[$cache_key];
    }
}

if (!function_exists('chamber_practice_enabled')) {
    function chamber_practice_enabled($CI, $hospital_id = null)
    {
        if ($hospital_id === null && isset($CI->ion_auth) && $CI->ion_auth->logged_in() && $CI->ion_auth->in_group(array('superadmin'))) {
            return true;
        }

        return chamber_practice_enabled_for_hospital($CI, $hospital_id);
    }
}

if (!function_exists('chamber_practice_require_enabled')) {
    function chamber_practice_require_enabled($CI, $hospital_id = null, $public_request = false)
    {
        if (chamber_practice_enabled($CI, $hospital_id)) {
            return;
        }

        if ($public_request) {
            show_404();
        }

        redirect('home/permission');
    }
}
