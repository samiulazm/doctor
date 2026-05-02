<?php

if (!function_exists('required_resolve_hospital_id')) {
    /**
     * Session / superadmin fallback when $CI->hospital_id was not set (e.g. frontend excluded from assignment block).
     */
    function required_resolve_hospital_id($CI)
    {
        if (isset($CI->hospital_id) && $CI->hospital_id !== null && $CI->hospital_id !== '') {
            return $CI->hospital_id;
        }
        $sid = $CI->session->userdata('hospital_id');
        if ($sid !== null && $sid !== '') {
            return $sid;
        }
        return 'superadmin';
    }
}

if (!function_exists('required_settings_row')) {
    /**
     * One settings row for a hospital_id, or null if DB is empty / not imported yet.
     */
    function required_settings_row($CI, $hospital_id)
    {
        $CI->db->where('hospital_id', $hospital_id);
        return $CI->db->get('settings')->row();
    }
}

if (!function_exists('required_cached_settings_row')) {
    /**
     * Per-request memoized settings lookup.
     */
    function required_cached_settings_row($CI, $hospital_id)
    {
        if (!isset($CI->__required_settings_cache) || !is_array($CI->__required_settings_cache)) {
            $CI->__required_settings_cache = array();
        }

        if (!array_key_exists($hospital_id, $CI->__required_settings_cache)) {
            $CI->__required_settings_cache[$hospital_id] = required_settings_row($CI, $hospital_id);
        }

        return $CI->__required_settings_cache[$hospital_id];
    }
}

if (!function_exists('required_user_context')) {
    /**
     * Resolve logged-in user, group, and hospital once per request.
     *
     * @return array<string, mixed>
     */
    function required_user_context($CI)
    {
        if (isset($CI->__required_user_context) && is_array($CI->__required_user_context)) {
            return $CI->__required_user_context;
        }

        $context = array(
            'user_id' => null,
            'group_id' => null,
            'group_name' => null,
            'hospital_id' => null,
        );

        if (!$CI->ion_auth->logged_in()) {
            $CI->__required_user_context = $context;
            return $context;
        }

        $user_id = $CI->ion_auth->get_user_id();
        $context['user_id'] = $user_id;

        if ($CI->ion_auth->in_group(array('superadmin'))) {
            $context['hospital_id'] = 'superadmin';
            $CI->__required_user_context = $context;
            return $context;
        }

        if ($CI->ion_auth->in_group(array('admin'))) {
            $hospital = $CI->db->select('id')->get_where('hospital', array('ion_user_id' => $user_id))->row();
            $context['hospital_id'] = $hospital ? $hospital->id : null;
            $CI->__required_user_context = $context;
            return $context;
        }

        $user_group = $CI->db->select('group_id')->get_where('users_groups', array('user_id' => $user_id))->row();
        $context['group_id'] = $user_group ? $user_group->group_id : null;

        if ($context['group_id'] !== null) {
            $group = $CI->db->select('name')->get_where('groups', array('id' => $context['group_id']))->row();
            $context['group_name'] = $group ? strtolower($group->name) : null;
        }

        if (!empty($context['group_name'])) {
            $member = $CI->db->select('hospital_id')->get_where($context['group_name'], array('ion_user_id' => $user_id))->row();
            $context['hospital_id'] = $member ? $member->hospital_id : null;
        }

        $CI->__required_user_context = $context;
        return $context;
    }
}

if (!function_exists('required_get_modules_for_hospital')) {
    /**
     * Per-request memoized hospital modules.
     *
     * @return array<int, string>
     */
    function required_get_modules_for_hospital($CI, $hospital_id)
    {
        if (!isset($CI->__required_modules_cache) || !is_array($CI->__required_modules_cache)) {
            $CI->__required_modules_cache = array();
        }

        if (!array_key_exists($hospital_id, $CI->__required_modules_cache)) {
            $row = $CI->db->select('module')->get_where('hospital', array('id' => $hospital_id))->row();
            $CI->__required_modules_cache[$hospital_id] = ($row && !empty($row->module)) ? explode(',', $row->module) : array();
        }

        return $CI->__required_modules_cache[$hospital_id];
    }
}

if (!function_exists('required_get_super_modules')) {
    /**
     * Per-request memoized superadmin modules.
     *
     * @return array<int, string>
     */
    function required_get_super_modules($CI, $user_id)
    {
        if (isset($CI->__required_super_modules) && is_array($CI->__required_super_modules)) {
            return $CI->__required_super_modules;
        }

        $row = $CI->db->select('module')->get_where('superadmin', array('ion_user_id' => $user_id))->row();
        $CI->__required_super_modules = ($row && !empty($row->module)) ? explode(',', $row->module) : array();
        return $CI->__required_super_modules;
    }
}

if (!function_exists('required_cached_email_settings')) {
    /**
     * Per-request memoized email settings lookup.
     */
    function required_cached_email_settings($CI, $type, $hospital_id)
    {
        if (!isset($CI->__required_email_settings_cache) || !is_array($CI->__required_email_settings_cache)) {
            $CI->__required_email_settings_cache = array();
        }

        $cache_key = $type . '|' . $hospital_id;
        if (!array_key_exists($cache_key, $CI->__required_email_settings_cache)) {
            $CI->__required_email_settings_cache[$cache_key] = $CI->db
                ->get_where('email_settings', array('type' => $type, 'hospital_id' => $hospital_id))
                ->row();
        }

        return $CI->__required_email_settings_cache[$cache_key];
    }
}

if (!function_exists('required_cached_user_language')) {
    /**
     * Per-request memoized user language lookup for role tables.
     */
    function required_cached_user_language($CI, $table, $user_id)
    {
        if (!isset($CI->__required_user_language_cache) || !is_array($CI->__required_user_language_cache)) {
            $CI->__required_user_language_cache = array();
        }

        $cache_key = $table . '|' . $user_id;
        if (!array_key_exists($cache_key, $CI->__required_user_language_cache)) {
            $row = $CI->db->select('language')->get_where($table, array('ion_user_id' => $user_id))->row();
            $CI->__required_user_language_cache[$cache_key] = $row ? $row->language : null;
        }

        return $CI->__required_user_language_cache[$cache_key];
    }
}

function required()
{
    $CI = &get_instance();
    $CI->load->library('Ion_auth');
    $CI->load->library('session');
    $CI->load->helper('cookie');
    $CI->load->library('form_validation');
    $CI->load->library('upload');
    $CI->load->library('parser');
    $CI->load->helper('security');
    $CI->load->helper('toastr_helper');


    $RTR = &load_class('Router');
    if ($RTR->class != "frontend" && $RTR->class != "payu" && $RTR->class != "status" && $RTR->class != "cronjobs" && $RTR->class != "request" && $RTR->class != "auth" && $RTR->class != "site" && $RTR->class != "api" && $RTR->class != "health" && $RTR->class != "portal" && $RTR->class != "payment_bd") {
        if (!$CI->ion_auth->logged_in()) {
            redirect('auth/login');
        }
    }

    $user_context = required_user_context($CI);
    $current_user_id = $user_context['user_id'];

    $CI->load->model('settings/settings_model');
    $CI->load->model('logs/logs_model');
    $CI->load->model('ion_auth_model');
    $CI->load->model('hospital/hospital_model');



    // if ($CI->ion_auth->logged_in()) {
    //     if (!$CI->ion_auth->in_group(array('superadmin'))) {
    //         if ($CI->router->fetch_class() != 'settings' && $CI->router->fetch_class() != 'auth') {
    //             try {
    //                 $verify = $CI->settings_model->verify();
    //                 if ($verify['verified'] == 1) {
    //                 } else {
    //                     redirect('settings/verifyYourPruchase776cbvcfytfytfvvn');
    //                 }
    //             } catch (Exception $e) {
    //                 redirect('settings/verifyYourPruchase776cbvcfytfytfvvn');
    //             }
    //         }
    //     }
    // }



    if ($CI->router->fetch_class() == 'site' && $CI->router->fetch_method() == 'index') {
        $hospital_username = $CI->uri->segment(2);
        $hospital = $CI->db->select('id,module')->get_where('hospital', array('username' => $hospital_username))->row();
        $CI->hospital_id = $hospital ? $hospital->id : null;
        $CI->modules = ($hospital && !empty($hospital->module)) ? explode(',', $hospital->module) : array();
        if (!empty($CI->hospital_id)) {
            $newdata = array(
                'site_id' => $CI->hospital_id,
                'site_name' => $hospital_username,
                'hospital_id' => $CI->hospital_id
            );
            $CI->session->set_userdata($newdata);
        } else {
            redirect('home/permission');
        }
        $CI->db->where('hospital_id', $CI->session->userdata('site_id'));
        $site_row = $CI->db->get('site_settings')->row();
        $language = ($site_row && !empty($site_row->language)) ? $site_row->language : 'english';


        if (!empty($CI->session->userdata('language_site'))) {
            $language = $CI->session->userdata('language_site');
        }

        if (empty($language)) {
            $language = 'english';
        }


        $CI->language = $language;
        $CI->lang->load('system_syntax', $language);
    } elseif ($CI->router->fetch_class() == 'site' && ($CI->router->fetch_method() == 'getAvailableSlotByDoctorByDateByJason' || $CI->router->fetch_method() == 'getDoctorVisit' || $CI->router->fetch_method() == 'getDoctorVisitCharges' || $CI->router->fetch_method() == 'addNew')) {
        $CI->hospital_id = $CI->session->userdata('site_id');
    } else {
        if ($RTR->class != "cronjobs" && $RTR->class != "frontend" && $RTR->class != "payu" && $RTR->class != "status" && $RTR->class != "request" && $RTR->class != "auth" && $RTR->class != "api" && $RTR->class != "health" && $RTR->class != "portal" && $RTR->class != "payment_bd") {
            $CI->hospital_id = $user_context['hospital_id'];
            if (!empty($CI->hospital_id)) {
                $CI->session->set_userdata(array(
                    'hospital_id' => $CI->hospital_id,
                ));
            }
        }

    if (!$CI->ion_auth->logged_in()) {
        $row_tz = required_cached_settings_row($CI, 'superadmin');
        $CI->timezone = $row_tz ? $row_tz->timezone : null;
    } elseif ($CI->ion_auth->in_group(array('superadmin'))) {
        $row_tz = required_cached_settings_row($CI, 'superadmin');
        $CI->timezone = $row_tz ? $row_tz->timezone : null;
    } else {
        $row_tz = required_cached_settings_row($CI, required_resolve_hospital_id($CI));
        $CI->timezone = $row_tz ? $row_tz->timezone : null;
    }
    $timezone = $CI->timezone;
    if (!empty($timezone)) {
        date_default_timezone_set($timezone);
    } else {
        date_default_timezone_set('UTC');
    }


        // Language
        if ($RTR->class != "cronjobs" && $RTR->class != "frontend" && $RTR->class != "payu" && $RTR->class != "status"  && $RTR->class != "request" && $RTR->class != "api") {
            if (!$CI->ion_auth->in_group(array('superadmin'))) {
                $row_lang = required_cached_settings_row($CI, required_resolve_hospital_id($CI));
                $CI->language = ($row_lang && !empty($row_lang->language)) ? $row_lang->language : 'english';
                $CI->hospital_language = $CI->language;
                $CI->lang->load('system_syntax', $CI->language);
                if ($CI->ion_auth->in_group(array('Patient'))) {
                    $CI->language = required_cached_user_language($CI, 'patient', $current_user_id);
                    if (empty($CI->language)) {
                        $CI->language = $CI->hospital_language;
                    }
                    if (!empty($CI->language)) {
                        $CI->lang->load('system_syntax', $CI->language);
                    }
                }
                if ($CI->ion_auth->in_group(array('Doctor'))) {
                    $CI->language = required_cached_user_language($CI, 'doctor', $current_user_id);
                    if (empty($CI->language)) {
                        $CI->language = $CI->hospital_language;
                    }
                    if (!empty($CI->language)) {
                        $CI->lang->load('system_syntax', $CI->language);
                    }
                }
            } else {
                $row_lang = required_cached_settings_row($CI, 'superadmin');
                $CI->language = ($row_lang && !empty($row_lang->language)) ? $row_lang->language : 'english';
                $CI->lang->load('system_syntax', $CI->language);
            }
        }
        if ($RTR->class == "frontend" || $RTR->class == "request" || $RTR->class == "portal" || $RTR->class == "payment_bd") {
            $row_lang = required_cached_settings_row($CI, 'superadmin');
            $CI->language = ($row_lang && !empty($row_lang->language)) ? $row_lang->language : 'english';
            $CI->lang->load('system_syntax', $CI->language);
        }


        if ($CI->router->fetch_class() == 'frontend' && $CI->router->fetch_method() == 'index') {
            $language = $CI->session->userdata('language');
            if (empty($language)) {
                $language = 'english';
            }
            $CI->language = $language;
            $CI->lang->load('system_syntax', $language);
        }




        if ($RTR->class == "auth" && $CI->router->fetch_method() == 'login') {
            $session_lang = $CI->session->userdata('language_site');
            if (!empty($session_lang)) {
                $CI->language = $session_lang;
            } else {
                $row_lang = required_cached_settings_row($CI, 'superadmin');
                $CI->language = ($row_lang && !empty($row_lang->language)) ? $row_lang->language : 'english';
            }
            if (empty($CI->language)) {
                $CI->language = 'english';
            }
            $CI->lang->load('system_syntax', $CI->language);
        }
        // Language



        // Currency (display: Bangladesh Taka everywhere)
        if ($RTR->class != "cronjobs" && $RTR->class != "payu" && $RTR->class != "status" &&   $RTR->class != "auth" && $RTR->class != "frontend" && $RTR->class != "site" && $RTR->class != "portal" && $RTR->class != "payment_bd") {
            $CI->currency = defined('HOSPITAL_CURRENCY_SYMBOL') ? HOSPITAL_CURRENCY_SYMBOL : '৳';
        }
        // Currency

        if ($RTR->class != "cronjobs" && $RTR->class != "payu" && $RTR->class != "status"  && $CI->ion_auth->in_group(array('admin', 'superadmin', 'Doctor', 'Receptionist', 'Patient', 'Nurse')) && $RTR->class != "auth" && $RTR->class != "site") {
            if (!$CI->ion_auth->in_group(array('superadmin')) && $RTR->class != "frontend") {
                $CI->settings = required_cached_settings_row($CI, required_resolve_hospital_id($CI));
            } else {
                $CI->settings = required_cached_settings_row($CI, 'superadmin');
            }
            if (!empty($CI->settings)) {
                $CI->settings->currency = defined('HOSPITAL_CURRENCY_SYMBOL') ? HOSPITAL_CURRENCY_SYMBOL : '৳';
            }

            if (!empty($CI->settings) && $CI->settings->emailtype == 'Domain Email') {

                $CI->load->library('email');
            }
            if (!empty($CI->settings) && $CI->settings->emailtype == 'Smtp') {


                $email_Settings = required_cached_email_settings($CI, $CI->settings->emailtype, required_resolve_hospital_id($CI));

                if (!empty($email_Settings)) {
                    $config['protocol'] = 'smtp';
                    $config['mailpath'] = '/usr/sbin/sendmail';
                    $config['smtp_host'] = $email_Settings->smtp_host;
                    $config['smtp_port'] = $email_Settings->smtp_port;
                    $config['smtp_user'] = $email_Settings->user;
                    $config['smtp_pass'] = base64_decode($email_Settings->password);
                    $config['smtp_crypto'] = 'tls';
                    $config['mailtype'] = 'html';
                    $config['charset'] = 'utf-8';
                    $config['wordwrap'] = TRUE;
                    $config['send_multipart'] = TRUE;
                    $config['newline'] = "\r\n";

                    $CI->load->library('email');
                    $CI->email->initialize($config);
                }
            }
        }
        if ($RTR->class != "cronjobs" && $RTR->class != "payu" && $RTR->class != "status"  && $RTR->class != "frontend" && $RTR->class != "request" && $RTR->class != "auth" && $RTR->class != "api" && $RTR->class != "health" && $RTR->class != "portal" && $RTR->class != "payment_bd") {
            if ($CI->ion_auth->logged_in() && !$CI->ion_auth->in_group(array('superadmin'))) {
                $CI->modules = required_get_modules_for_hospital($CI, required_resolve_hospital_id($CI));
            }
        }
        if ($RTR->class != "cronjobs" && $RTR->class != "payu" && $RTR->class != "status" &&  $RTR->class != "" && $RTR->class != "" && $RTR->class != "auth") {
            if ($CI->ion_auth->in_group(array('superadmin'))) {
                $CI->super_modules = required_get_super_modules($CI, $current_user_id);
            }
        }

        $common = array('payu', 'status', 'macro', 'auth', 'pservice', 'frontend', 'settings', 'import', 'home', 'profile', 'request', 'api', 'cronjobs', 'logs', 'doctorvisit', 'site', 'testpkz', 'facilitie', 'faq', 'diagnosis', 'treatment', 'symptom', 'advice', 'inventory', 'treatment_plan', 'ai_image_analysis', 'ai_patient_overview', 'emergency', 'ambulance', 'dashboard', 'radiology', 'health', 'portal', 'payment_bd', 'doctor_chamber', 'assistant_chamber', 'patient_chamber', 'saas_platform'); 

        if (!in_array($RTR->class, $common)) {
            if (!$CI->ion_auth->in_group(array('superadmin'))) {
                if ($RTR->class != "schedule" && $RTR->class != "meeting" && $RTR->class != "featured" && $RTR->class != "gallery" && $RTR->class != "review" && $RTR->class != "gridsection" && $RTR->class != "service" && $RTR->class != "slide" && $RTR->class != "facilitie" && $RTR->class != "faq") {
                    if ($RTR->class != "pgateway") {
                        if (!in_array($RTR->class, $CI->modules)) {
                            redirect('home');
                        }
                    } elseif (!in_array('finance', $CI->modules)) {
                        redirect('home');
                    }
                } elseif (!in_array('appointment', $CI->modules)) {
                    redirect('home');
                }
            } else {
                if (!in_array($RTR->class, $CI->super_modules)) {
                    redirect('home');
                }
            }
        }
    }



    if (!empty($CI->input->cookie('language_site'))) {
        $CI->language = $CI->input->cookie('language_site');
        $CI->lang->load('system_syntax', $CI->language);
    }


    // if ($RTR->class == "site") {
    //     if ($CI->router->fetch_method() == 'index') {

    //     }
    //     // $settings = $CI->db->get_where('settings', array('hospital_id' => $CI->session->userdata('hospital_id')))->row();
    //     // $CI->lang->load('system_syntax', $settings->language);
    // }
}
