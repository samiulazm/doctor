<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Doctor_chamber extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!function_exists('chamber_practice_require_enabled')) {
            $this->load->helper('chamber_practice');
        }
        if (!$this->ion_auth->in_group(array('Doctor'))) {
            redirect('home/permission');
        }
        chamber_practice_require_enabled($this);
        $this->load->model('portal/queue_model');
        $this->load->model('portal/portal_model');
        $this->load->model('portal/chamber_platform_model');
        $this->load->model('patient/patient_model');
        $this->load->model('prescription/prescription_model');
        $this->load->model('finance/finance_model');
    }

    protected function currentDoctor()
    {
        $uid = $this->ion_auth->get_user_id();
        return $this->db->get_where('doctor', array('ion_user_id' => $uid))->row();
    }

    protected function normalizeVitals($v)
    {
        if (!$v) {
            return null;
        }
        $row = is_object($v) ? get_object_vars($v) : (array) $v;
        $row['bp_sys'] = isset($row['bp_sys']) ? $row['bp_sys'] : (isset($row['bp_systolic']) ? $row['bp_systolic'] : null);
        $row['bp_dia'] = isset($row['bp_dia']) ? $row['bp_dia'] : (isset($row['bp_diastolic']) ? $row['bp_diastolic'] : null);
        $row['pulse'] = isset($row['pulse']) ? $row['pulse'] : null;
        $row['weight_kg'] = isset($row['weight_kg']) ? $row['weight_kg'] : null;
        return (object) $row;
    }

    protected function triageSummary($triage_json)
    {
        if (empty($triage_json)) {
            return '';
        }
        $triage = @json_decode($triage_json, true);
        if (!is_array($triage)) {
            return '';
        }
        $symptom = '';
        foreach (array('symptom', 'complaint', 'chief_complaint', 'problem') as $key) {
            if (!empty($triage[$key])) {
                $symptom = (string) $triage[$key];
                break;
            }
        }
        $duration = !empty($triage['duration']) ? ' (' . $triage['duration'] . ')' : '';
        return trim($symptom . $duration);
    }

    protected function jsonResponse($payload)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    public function portal_profile()
    {
        $doc = $this->currentDoctor();
        $this->db->where('doctor_id', $doc->id);
        $profile = $this->db->get('doctor_portal_profile')->row();
        $data = array('settings' => $this->settings_model->getSettings(), 'doctor' => $doc, 'profile' => $profile);
        $this->load->view('home/dashboard', $data);
        $this->load->view('doctor/portal_profile', $data);
        $this->load->view('home/footer');
    }

    public function portal_profile_save()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $doc = $this->currentDoctor();
        $slug = preg_replace('/[^a-z0-9\-]/i', '-', strtolower(trim($this->input->post('public_slug'))));
        $row = array(
            'hospital_id' => $doc->hospital_id,
            'doctor_id' => $doc->id,
            'public_slug' => $slug,
            'specialty_label' => $this->input->post('specialty_label'),
            'hero_image' => $this->input->post('hero_image'),
            'signature_image' => $this->input->post('signature_image'),
            'booking_enabled' => $this->input->post('booking_enabled') ? 1 : 0,
        );
        if ($this->db->field_exists('advance_booking_fee', 'doctor_portal_profile')) {
            $fee = (float) str_replace(array(',', ' '), array('', ''), (string) $this->input->post('advance_booking_fee'));
            $row['advance_booking_fee'] = max(0, $fee);
        }
        $this->db->where('doctor_id', $doc->id);
        $exist = $this->db->get('doctor_portal_profile')->row();
        if ($exist) {
            $upd = $row;
            unset($upd['doctor_id'], $upd['hospital_id']);
            $this->db->where('doctor_id', $doc->id);
            $this->db->update('doctor_portal_profile', $upd);
        } else {
            $this->db->insert('doctor_portal_profile', $row);
        }
        $this->portal_model->ensureDefaultChamber($doc->id, $doc->hospital_id);
        redirect('doctor_chamber/portal_profile');
    }

    public function dashboard()
    {
        $doc = $this->currentDoctor();
        if (!$doc) {
            redirect('home/permission');
        }
        $start = strtotime('today');
        $end = strtotime('tomorrow');
        $today_qd = date('Y-m-d');

        $this->db->where('doctor', $doc->id);
        $this->db->where('hospital_id', $doc->hospital_id);
        $this->db->where('date >=', $start);
        $this->db->where('date <', $end);
        $total_today = $this->db->count_all_results('appointment');

        $this->db->where('doctor_id', $doc->id);
        $this->db->where('hospital_id', $doc->hospital_id);
        $this->db->where('queue_date', $today_qd);
        $this->db->where('status', 'pending');
        $pending = $this->db->count_all_results('chamber_serial_queue');

        $this->db->where('doctor_id', $doc->id);
        $this->db->where('hospital_id', $doc->hospital_id);
        $this->db->where('queue_date', $today_qd);
        $this->db->where_in('status', array('arrived', 'serving'));
        $checked_in = $this->db->count_all_results('chamber_serial_queue');

        $this->db->select_sum('doctor_amount');
        $this->db->where('hospital_id', $doc->hospital_id);
        $this->db->where('doctor', $doc->id);
        $this->db->where('date >=', $start);
        $this->db->where('date <', $end);
        $rev = $this->db->get('payment')->row();
        $today_revenue = $rev && $rev->doctor_amount !== null ? (float) $rev->doctor_amount : 0.0;

        $followJoin = 'ppt.patient_id = q.patient_id AND ppt.doctor_id = q.doctor_id AND ppt.hospital_id = q.hospital_id AND ppt.tag = ' . $this->db->escape('follow_up');
        $this->db->select('COUNT(DISTINCT q.patient_id) AS cnt', false);
        $this->db->from('chamber_serial_queue q');
        $this->db->join('patient_practice_tag ppt', $followJoin, 'left');
        $this->db->where('q.doctor_id', $doc->id);
        $this->db->where('q.hospital_id', $doc->hospital_id);
        $this->db->where('q.queue_date', $today_qd);
        $this->db->where_not_in('q.status', array('done', 'cancelled'));
        $this->db->where('q.patient_id IS NOT NULL', null, false);
        $this->db->group_start();
        $this->db->where('ppt.id IS NOT NULL', null, false);
        $this->db->or_like('q.triage_json', 'follow_up');
        $this->db->group_end();
        $follow = $this->db->get()->row();
        $followup_count = $follow ? (int) $follow->cnt : 0;

        $riskJoin = 'ppt.patient_id = q.patient_id AND ppt.doctor_id = q.doctor_id AND ppt.hospital_id = q.hospital_id AND ppt.tag = ' . $this->db->escape('high_risk');
        $this->db->select('q.patient_id, q.guest_name, p.name AS patient_name');
        $this->db->from('chamber_serial_queue q');
        $this->db->join('patient p', 'p.id = q.patient_id AND p.hospital_id = q.hospital_id', 'left');
        $this->db->join('patient_practice_tag ppt', $riskJoin, 'left');
        $this->db->where('q.doctor_id', $doc->id);
        $this->db->where('q.hospital_id', $doc->hospital_id);
        $this->db->where('q.queue_date', $today_qd);
        $this->db->where_not_in('q.status', array('done', 'cancelled'));
        $this->db->group_start();
        $this->db->where('ppt.id IS NOT NULL', null, false);
        $this->db->or_like('q.triage_json', 'high_risk');
        $this->db->group_end();
        $this->db->group_by('q.patient_id, q.guest_name, p.name', false);
        $this->db->order_by('q.sort_position', 'asc');
        $this->db->limit(10);
        $high_risk_patients = $this->db->get()->result();

        $data = array(
            'settings' => $this->settings_model->getSettings(),
            'doctor' => $doc,
            'total_today' => $total_today,
            'checked_in' => $checked_in,
            'pending' => $pending,
            'today_revenue' => $today_revenue,
            'followup_count' => $followup_count,
            'high_risk_patients' => $high_risk_patients,
        );
        $this->chamber_platform_model->logUsage($doc->hospital_id, 'doctor_chamber_dashboard', $doc->id, $this->ion_auth->get_user_id(), null);
        $this->load->view('home/dashboard', $data);
        $this->load->view('doctor/dashboard', $data);
        $this->load->view('home/footer');
    }

    public function revenue()
    {
        $doc = $this->currentDoctor();
        $from = $this->input->get('from') ?: date('Y-m-01');
        $to = $this->input->get('to') ?: date('Y-m-d');
        $from_ds = date('d-m-Y', strtotime($from));
        $to_ds = date('d-m-Y', strtotime($to));

        $this->db->select_sum('doctor_amount');
        $this->db->where('hospital_id', $doc->hospital_id);
        $this->db->where('doctor', $doc->id);
        $this->db->where('date_string >=', $from_ds);
        $this->db->where('date_string <=', $to_ds);
        $q = $this->db->get('payment')->row();
        $total = $q && $q->doctor_amount !== null ? $q->doctor_amount : 0;

        $this->db->select_sum('doctor_amount');
        $this->db->where('hospital_id', $doc->hospital_id);
        $this->db->where('doctor', $doc->id);
        $this->db->where('date_string >=', $from_ds);
        $this->db->where('date_string <=', $to_ds);
        $this->db->group_start();
        $this->db->where('category_name', 'Consultant Fee');
        $this->db->or_where_in('payment_from', array('appointment', 'chamber_desk'));
        $this->db->group_end();
        $this->db->where('(category_name IS NULL OR category_name <> ' . $this->db->escape('Procedure') . ')', null, false);
        $qc = $this->db->get('payment')->row();
        $consultation = $qc && $qc->doctor_amount !== null ? $qc->doctor_amount : 0;

        $this->db->select_sum('doctor_amount');
        $this->db->where('hospital_id', $doc->hospital_id);
        $this->db->where('doctor', $doc->id);
        $this->db->where('date_string >=', $from_ds);
        $this->db->where('date_string <=', $to_ds);
        $this->db->where('category_name', 'Procedure');
        $qp = $this->db->get('payment')->row();
        $procedure = $qp && $qp->doctor_amount !== null ? $qp->doctor_amount : 0;

        $this->db->select_sum('doctor_amount');
        $this->db->where('hospital_id', $doc->hospital_id);
        $this->db->where('doctor_name', $doc->name);
        $this->db->where('date_string >=', $from_ds);
        $this->db->where('date_string <=', $to_ds);
        $this->db->where('(doctor IS NULL OR doctor = 0 OR doctor = ' . $this->db->escape('') . ')', null, false);
        $ql = $this->db->get('payment')->row();
        $legacy_total = $ql && $ql->doctor_amount !== null ? $ql->doctor_amount : 0;
        $total_combined = (float) $total + (float) $legacy_total;

        $data = array(
            'settings' => $this->settings_model->getSettings(),
            'doctor' => $doc,
            'total' => $total,
            'legacy_total' => $legacy_total,
            'total_combined' => $total_combined,
            'consultation' => $consultation,
            'procedure' => $procedure,
            'from' => $from,
            'to' => $to,
        );
        $this->load->view('home/dashboard', $data);
        $this->load->view('doctor/revenue', $data);
        $this->load->view('home/footer');
    }

    public function queue_json()
    {
        $doc = $this->currentDoctor();
        if (!$doc) {
            $this->jsonResponse(array('rows' => array()));
            return;
        }

        $this->db->select('q.id, q.serial_number, q.patient_id, q.guest_name, q.status, q.triage_json, c.name AS chamber_name, p.name AS patient_name');
        $this->db->from('chamber_serial_queue q');
        $this->db->join('doctor_chamber c', 'c.id = q.chamber_id', 'left');
        $this->db->join('patient p', 'p.id = q.patient_id AND p.hospital_id = q.hospital_id', 'left');
        $this->db->where('q.doctor_id', $doc->id);
        $this->db->where('q.hospital_id', $doc->hospital_id);
        $this->db->where('q.queue_date', date('Y-m-d'));
        $this->db->where_not_in('q.status', array('done', 'cancelled'));
        $this->db->order_by('q.sort_position', 'asc');
        $this->db->order_by('q.serial_number', 'asc');
        $rows = $this->db->get()->result();

        $out = array();
        foreach ($rows as $row) {
            $out[] = array(
                'serial_number' => (int) $row->serial_number,
                'patient_id' => (int) $row->patient_id,
                'guest_name' => !empty($row->guest_name) ? $row->guest_name : $row->patient_name,
                'chamber_name' => $row->chamber_name,
                'status' => $row->status,
                'triage_summary' => $this->triageSummary($row->triage_json),
            );
        }

        $this->jsonResponse(array('rows' => $out));
    }

    public function chart_data_json()
    {
        $doc = $this->currentDoctor();
        if (!$doc) {
            $this->jsonResponse(array('labels' => array(), 'revenue' => array(), 'patients' => array()));
            return;
        }

        $months = array();
        for ($i = 5; $i >= 0; $i--) {
            $ts = strtotime('first day of -' . $i . ' months');
            $months[date('Y-m', $ts)] = array(
                'label' => date('M', $ts),
                'revenue' => 0.0,
                'patients' => 0,
            );
        }

        $keys = array_keys($months);
        $range_start = strtotime($keys[0] . '-01');
        $range_end = strtotime('first day of +1 month', strtotime($keys[count($keys) - 1] . '-01'));

        $this->db->select("DATE_FORMAT(FROM_UNIXTIME(`date`), '%Y-%m') AS month_key, SUM(doctor_amount) AS total", false);
        $this->db->where('hospital_id', $doc->hospital_id);
        $this->db->where('doctor', $doc->id);
        $this->db->where('date >=', $range_start);
        $this->db->where('date <', $range_end);
        $this->db->group_by("DATE_FORMAT(FROM_UNIXTIME(`date`), '%Y-%m')", false);
        $revenue_rows = $this->db->get('payment')->result();
        foreach ($revenue_rows as $row) {
            if (isset($months[$row->month_key])) {
                $months[$row->month_key]['revenue'] = (float) $row->total;
            }
        }

        $this->db->select("DATE_FORMAT(FROM_UNIXTIME(`date`), '%Y-%m') AS month_key, COUNT(id) AS total", false);
        $this->db->where('hospital_id', $doc->hospital_id);
        $this->db->where('doctor', $doc->id);
        $this->db->where('date >=', $range_start);
        $this->db->where('date <', $range_end);
        $this->db->group_by("DATE_FORMAT(FROM_UNIXTIME(`date`), '%Y-%m')", false);
        $patient_rows = $this->db->get('appointment')->result();
        foreach ($patient_rows as $row) {
            if (isset($months[$row->month_key])) {
                $months[$row->month_key]['patients'] = (int) $row->total;
            }
        }

        $this->jsonResponse(array(
            'labels' => array_values(array_map(function ($row) {
                return $row['label'];
            }, $months)),
            'revenue' => array_values(array_map(function ($row) {
                return $row['revenue'];
            }, $months)),
            'patients' => array_values(array_map(function ($row) {
                return $row['patients'];
            }, $months)),
        ));
    }

    public function consultation_room()
    {
        $doc = $this->currentDoctor();
        $patient_id = (int) $this->input->get('patient');
        $patient = $patient_id ? $this->patient_model->getPatientById($patient_id) : null;
        if ($patient && (string) $patient->hospital_id !== (string) $doc->hospital_id) {
            $patient = null;
            $patient_id = 0;
        }
        $vitals = null;
        $triage = null;
        $past_prescriptions = array();
        $patient_tags = array();
        if ($patient_id && $patient) {
            $this->db->where('hospital_id', $doc->hospital_id);
            $this->db->where('doctor_id', $doc->id);
            $this->db->where('patient_id', $patient_id);
            $this->db->order_by('id', 'desc');
            $this->db->limit(1);
            $qrow = $this->db->get('chamber_serial_queue')->row();
            if ($qrow) {
                $decoded_triage = !empty($qrow->triage_json) ? @json_decode($qrow->triage_json, true) : array();
                $triage = is_array($decoded_triage) ? $decoded_triage : null;
                $this->db->where('queue_id', $qrow->id);
                $this->db->order_by('id', 'desc');
                $vitals = $this->normalizeVitals($this->db->get('visit_vital')->row());
            }
            $past_prescriptions = $this->prescription_model->getPrescriptionByPatientId($patient_id);
            $this->db->where('doctor_id', $doc->id);
            $this->db->where('hospital_id', $doc->hospital_id);
            $this->db->where('patient_id', $patient_id);
            $this->db->order_by('id', 'desc');
            $patient_tags = $this->db->get('patient_practice_tag')->result();
        }
        $this->db->where('doctor_id', $doc->id);
        $this->db->where('hospital_id', $doc->hospital_id);
        $this->db->order_by('label', 'asc');
        $favorites = $this->db->get('prescription_favorite')->result();
        $data = array(
            'settings' => $this->settings_model->getSettings(),
            'doctor' => $doc,
            'patient' => $patient,
            'vitals' => $vitals,
            'triage' => $triage,
            'past_prescriptions' => $past_prescriptions,
            'patient_tags' => $patient_tags,
            'favorites' => $favorites,
            'rx_templates' => $favorites,
            'rx_url' => $patient ? site_url('prescription/addPrescriptionView?embed=1&patient=' . (int) $patient->id) : '',
        );
        $this->load->view('home/dashboard', $data);
        $this->load->view('doctor/consultation_room', $data);
        $this->load->view('home/footer');
    }

    public function vitals_json()
    {
        $doc = $this->currentDoctor();
        $patient_id = (int) $this->input->get('patient');
        $this->db->where('hospital_id', $doc->hospital_id);
        $this->db->where('doctor_id', $doc->id);
        $this->db->where('patient_id', $patient_id);
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $qrow = $this->db->get('chamber_serial_queue')->row();
        $v = null;
        if ($qrow) {
            $this->db->where('queue_id', $qrow->id);
            $this->db->order_by('id', 'desc');
            $v = $this->normalizeVitals($this->db->get('visit_vital')->row());
        }
        $this->jsonResponse(array('ok' => true, 'vitals' => $v));
    }

    public function search_json()
    {
        $doc = $this->currentDoctor();
        $mode = $this->input->get('mode') === 'date' ? 'date' : 'id';
        $patients = array();

        if ($mode === 'id') {
            $patient_id = (int) $this->input->get('id');
            if ($patient_id > 0) {
                $this->db->select('id, name, phone, age');
                $this->db->where('hospital_id', $doc->hospital_id);
                $this->db->where('id', $patient_id);
                $row = $this->db->get('patient')->row();
                if ($row) {
                    $patients[] = array(
                        'id' => (int) $row->id,
                        'name' => $row->name,
                        'phone' => $row->phone,
                        'age' => isset($row->age) ? $row->age : '',
                    );
                }
            }
        } else {
            $date = trim((string) $this->input->get('date'));
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $date = date('Y-m-d');
            }
            $this->db->select('p.id, p.name, p.phone, p.age');
            $this->db->from('chamber_serial_queue q');
            $this->db->join('patient p', 'p.id = q.patient_id AND p.hospital_id = q.hospital_id', 'inner');
            $this->db->where('q.hospital_id', $doc->hospital_id);
            $this->db->where('q.doctor_id', $doc->id);
            $this->db->where('q.queue_date', $date);
            $this->db->group_by('p.id, p.name, p.phone, p.age', false);
            $this->db->order_by('p.name', 'asc');
            $this->db->limit(50);
            $rows = $this->db->get()->result();
            foreach ($rows as $row) {
                $patients[] = array(
                    'id' => (int) $row->id,
                    'name' => $row->name,
                    'phone' => $row->phone,
                    'age' => isset($row->age) ? $row->age : '',
                );
            }
        }

        $this->jsonResponse(array('ok' => true, 'patients' => $patients));
    }

    public function favorites()
    {
        $doc = $this->currentDoctor();
        $this->db->where('doctor_id', $doc->id);
        $rows = $this->db->get('prescription_favorite')->result();
        $data = array('settings' => $this->settings_model->getSettings(), 'rows' => $rows, 'doctor' => $doc);
        $this->load->view('home/dashboard', $data);
        $this->load->view('doctor/favorites', $data);
        $this->load->view('home/footer');
    }

    public function favorite_save()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $doc = $this->currentDoctor();
        $label = $this->input->post('label');
        $json = $this->input->post('medicine_lines_json');
        $this->db->insert('prescription_favorite', array(
            'hospital_id' => $doc->hospital_id,
            'doctor_id' => $doc->id,
            'label' => $label,
            'medicine_lines_json' => $json,
        ));
        redirect('doctor_chamber/favorites');
    }

    public function crm_search()
    {
        $doc = $this->currentDoctor();
        $q = trim($this->input->get('q'));
        $patients = array();
        if ($q !== '') {
            $this->db->where('hospital_id', $doc->hospital_id);
            $this->db->group_start();
            $this->db->like('phone', $q);
            $this->db->or_like('name', $q);
            $this->db->group_end();
            $this->db->limit(50);
            $patients = $this->db->get('patient')->result();
        }
        $data = array('settings' => $this->settings_model->getSettings(), 'patients' => $patients, 'q' => $q, 'doctor' => $doc);
        $this->load->view('home/dashboard', $data);
        $this->load->view('doctor/crm_search', $data);
        $this->load->view('home/footer');
    }

    public function tag_patient()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $doc = $this->currentDoctor();
        $pid = (int) $this->input->post('patient_id');
        $tag = $this->input->post('tag');
        if (!in_array($tag, array('high_risk', 'follow_up', 'vip'), true)) {
            show_error('Invalid tag', 400);
        }
        $this->db->delete('patient_practice_tag', array(
            'doctor_id' => $doc->id,
            'patient_id' => $pid,
            'tag' => $tag,
        ));
        $this->db->insert('patient_practice_tag', array(
            'hospital_id' => $doc->hospital_id,
            'doctor_id' => $doc->id,
            'patient_id' => $pid,
            'tag' => $tag,
            'notes' => $this->input->post('notes'),
        ));
        redirect('doctor_chamber/crm_search?q=' . urlencode((string) $this->input->post('redirect_q')));
    }

    public function schedule_exceptions()
    {
        $doc = $this->currentDoctor();
        $this->portal_model->ensureDefaultChamber($doc->id, $doc->hospital_id);
        $this->db->where('doctor_id', $doc->id);
        $this->db->order_by('exception_date', 'desc');
        $rows = $this->db->get('doctor_schedule_exception')->result();
        $this->db->where('doctor_id', $doc->id);
        $this->db->where('hospital_id', $doc->hospital_id);
        $this->db->order_by('sort_order', 'asc');
        $chambers = $this->db->get('doctor_chamber')->result();
        $data = array('settings' => $this->settings_model->getSettings(), 'rows' => $rows, 'doctor' => $doc, 'chambers' => $chambers);
        $this->load->view('home/dashboard', $data);
        $this->load->view('doctor/schedule_exceptions', $data);
        $this->load->view('home/footer');
    }

    public function schedule_save()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $doc = $this->currentDoctor();
        $chamber_id = (int) $this->input->post('chamber_id');
        if ($chamber_id > 0 && !$this->portal_model->getChamberIfOwned($chamber_id, $doc->id, $doc->hospital_id)) {
            show_error('Invalid chamber', 400);
        }
        $this->db->insert('doctor_schedule_exception', array(
            'hospital_id' => $doc->hospital_id,
            'doctor_id' => $doc->id,
            'chamber_id' => $chamber_id > 0 ? $chamber_id : null,
            'exception_date' => $this->input->post('exception_date'),
            'is_closed' => $this->input->post('is_closed') ? 1 : 0,
            'open_time' => $this->input->post('open_time') ?: null,
            'close_time' => $this->input->post('close_time') ?: null,
            'reason' => $this->input->post('reason'),
        ));
        redirect('doctor_chamber/schedule_exceptions');
    }

    public function template_builder()
    {
        $doc = $this->currentDoctor();
        $this->db->where('doctor_id', $doc->id);
        $tpl = $this->db->get('prescription_print_template')->row();
        $data = array('settings' => $this->settings_model->getSettings(), 'tpl' => $tpl, 'doctor' => $doc);
        $this->load->view('home/dashboard', $data);
        $this->load->view('doctor/template_builder', $data);
        $this->load->view('home/footer');
    }

    public function template_save()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $doc = $this->currentDoctor();
        $row = array(
            'hospital_id' => $doc->hospital_id,
            'doctor_id' => $doc->id,
            'header_html' => $this->input->post('header_html'),
            'footer_html' => $this->input->post('footer_html'),
        );
        $existing = $this->db->get_where('prescription_print_template', array('doctor_id' => $doc->id), 1)->row();
        if ($existing) {
            $this->db->where('doctor_id', $doc->id);
            $this->db->update('prescription_print_template', $row);
        } else {
            $this->db->insert('prescription_print_template', $row);
        }
        redirect('doctor_chamber/template_builder');
    }

    public function refer_lab()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $doc = $this->currentDoctor();
        $pid = (int) $this->input->post('patient_id');
        $pat = $this->patient_model->getPatientById($pid);
        if (!$pat) {
            show_404();
        }
        $code = 'LAB' . strtoupper(bin2hex(random_bytes(3)));
        $lab_name = trim((string) $this->input->post('lab_name'));
        $rid = $this->chamber_platform_model->insertReferral(array(
            'hospital_id' => $doc->hospital_id,
            'doctor_id' => $doc->id,
            'patient_id' => $pid,
            'discount_code' => $code,
            'lab_name' => $lab_name,
            'status' => 'sent',
            'patient_phone' => $pat->phone,
        ));
        $msg = 'Lab referral' . ($lab_name ? (' (' . $lab_name . ')') : '') . ': use discount code ' . $code . ' when booking tests.';
        $sent = !empty($pat->phone) && $this->chamber_platform_model->sendSmsMessage($doc->hospital_id, $pat->phone, $msg);
        if ($sent) {
            $this->chamber_platform_model->adjustSmsCredits($doc->hospital_id, -1, 'referral_sms', $doc->id, 'referral', $rid);
        }
        redirect('doctor_chamber/crm_search?q=' . urlencode($pat->phone));
    }

    public function drug_search_json()
    {
        $doc = $this->currentDoctor();
        $term = trim((string) $this->input->get('term'));
        if (strlen($term) < 2) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array()));
            return;
        }
        $this->db->where('hospital_id', $doc->hospital_id);
        $this->db->group_start();
        $this->db->like('name', $term);
        $this->db->or_like('generic', $term);
        $this->db->or_like('company', $term);
        $this->db->group_end();
        $pfx = $this->db->escape($term . '%');
        $any = $this->db->escape('%' . $term . '%');
        $this->db->order_by(
            '(CASE WHEN `name` LIKE ' . $pfx . ' OR `generic` LIKE ' . $pfx . ' OR `company` LIKE ' . $pfx
            . ' THEN 0 WHEN `name` LIKE ' . $any . ' OR `generic` LIKE ' . $any . ' OR `company` LIKE ' . $any
            . ' THEN 1 ELSE 2 END)',
            'ASC',
            false
        );
        $this->db->order_by('name', 'asc');
        $this->db->limit(25);
        $rows = $this->db->get('medicine')->result();
        $out = array();
        foreach ($rows as $r) {
            $label = $r->name;
            if (!empty($r->generic)) {
                $label .= ' - ' . $r->generic;
            }
            if (!empty($r->company)) {
                $label .= ' (' . $r->company . ')';
            }
            $out[] = array('id' => $r->id, 'label' => $label, 'name' => $r->name);
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($out));
    }

    public function my_chambers()
    {
        $doc = $this->currentDoctor();
        $this->portal_model->ensureDefaultChamber($doc->id, $doc->hospital_id);
        $this->db->where('doctor_id', $doc->id);
        $this->db->where('hospital_id', $doc->hospital_id);
        $this->db->order_by('sort_order', 'asc');
        $rows = $this->db->get('doctor_chamber')->result();
        foreach ($rows as $r) {
            $raw = isset($r->weekly_hours_json) ? $r->weekly_hours_json : '';
            $decoded = @json_decode($raw, true);
            $r->weekly_hours = is_array($decoded) ? $decoded : array();
        }
        $data = array('settings' => $this->settings_model->getSettings(), 'doctor' => $doc, 'rows' => $rows);
        $this->load->view('home/dashboard', $data);
        $this->load->view('doctor/my_chambers', $data);
        $this->load->view('home/footer');
    }

    public function chamber_create()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $doc = $this->currentDoctor();
        $name = trim((string) $this->input->post('name'));
        if ($name === '') {
            show_error('Chamber name is required', 400);
        }
        $this->db->insert('doctor_chamber', array(
            'hospital_id' => $doc->hospital_id,
            'doctor_id' => $doc->id,
            'name' => $name,
            'address' => $this->input->post('address'),
            'phone' => $this->input->post('phone'),
            'sort_order' => (int) $this->input->post('sort_order'),
            'is_active' => $this->input->post('is_active') ? 1 : 0,
        ));
        redirect('doctor_chamber/my_chambers');
    }

    public function chamber_update()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $doc = $this->currentDoctor();
        $cid = (int) $this->input->post('chamber_id');
        $row = $this->db->get_where('doctor_chamber', array(
            'id' => $cid,
            'doctor_id' => $doc->id,
            'hospital_id' => $doc->hospital_id,
        ), 1)->row();
        if (!$row) {
            show_404();
        }
        $update = array(
            'name' => $this->input->post('name'),
            'address' => $this->input->post('address'),
            'phone' => $this->input->post('phone'),
            'sort_order' => (int) $this->input->post('sort_order'),
            'is_active' => $this->input->post('is_active') ? 1 : 0,
        );
        if ($this->db->field_exists('weekly_hours_json', 'doctor_chamber')) {
            $days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
            $weekly = array();
            foreach ($days as $d) {
                $o = trim((string) $this->input->post('wh_' . $d . '_open'));
                $cl = trim((string) $this->input->post('wh_' . $d . '_close'));
                if ($o !== '' || $cl !== '') {
                    $weekly[$d] = array('open' => $o, 'close' => $cl);
                }
            }
            $update['weekly_hours_json'] = empty($weekly) ? null : json_encode($weekly);
        }
        $this->db->where('id', $cid);
        $this->db->update('doctor_chamber', $update);
        redirect('doctor_chamber/my_chambers');
    }
}
