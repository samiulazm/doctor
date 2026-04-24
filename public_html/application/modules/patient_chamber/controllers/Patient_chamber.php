<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Patient_chamber extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!function_exists('chamber_practice_require_enabled')) {
            $this->load->helper('chamber_practice');
        }
        if (!$this->ion_auth->in_group(array('Patient'))) {
            redirect('home/permission');
        }
        chamber_practice_require_enabled($this);
        $this->load->model('patient/patient_model');
        $this->load->model('prescription/prescription_model');
        $this->load->model('portal/chamber_platform_model');
    }

    protected function myPatientId()
    {
        $uid = $this->ion_auth->get_user_id();
        $row = $this->patient_model->getPatientByIonUserId($uid);
        if (!$row) {
            show_error('Patient profile not linked', 403);
        }
        return (int) $row->id;
    }

    public function my_prescriptions()
    {
        $pid = $this->myPatientId();
        $data['settings'] = $this->settings_model->getSettings();
        $data['prescriptions'] = $this->prescription_model->getPrescriptionByPatientId($pid);
        $this->load->view('home/dashboard', $data);
        $this->load->view('patient/my_prescriptions', $data);
        $this->load->view('home/footer');
    }

    public function lab_vault()
    {
        $pid = $this->myPatientId();
        $data['settings'] = $this->settings_model->getSettings();
        $data['referrals'] = $this->chamber_platform_model->getReferralsForPatient($pid);
        $this->load->model('lab/lab_model');
        $data['lab_reports'] = $this->lab_model->getLabByPatientId($pid);
        $data['ot_lab_reports'] = $this->lab_model->getOtLabByPatientId($pid);
        $this->load->view('home/dashboard', $data);
        $this->load->view('patient/lab_vault', $data);
        $this->load->view('home/footer');
    }

    public function reminders()
    {
        $pid = $this->myPatientId();
        $this->db->where('patient_id', $pid);
        $this->db->where('is_active', 1);
        $rows = $this->db->get('medicine_reminder')->result();
        $data['settings'] = $this->settings_model->getSettings();
        $data['rows'] = $rows;
        $this->load->view('home/dashboard', $data);
        $this->load->view('patient/reminders', $data);
        $this->load->view('home/footer');
    }

    public function reminder_save()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $pid = $this->myPatientId();
        $hid = $this->session->userdata('hospital_id');
        $times = $this->input->post('dose_times');
        $this->db->insert('medicine_reminder', array(
            'hospital_id' => $hid,
            'patient_id' => $pid,
            'medicine_label' => $this->input->post('medicine_label'),
            'dose_times_json' => json_encode(array_filter(array_map('trim', explode(',', (string) $times)))),
            'is_active' => 1,
        ));
        redirect('patient_chamber/reminders');
    }
}
