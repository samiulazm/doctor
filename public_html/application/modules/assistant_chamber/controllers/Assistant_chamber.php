<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Assistant_chamber extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!function_exists('chamber_practice_require_enabled')) {
            $this->load->helper('chamber_practice');
        }
        if (!$this->ion_auth->in_group(array('Receptionist', 'Nurse', 'admin'))) {
            redirect('home/permission');
        }
        chamber_practice_require_enabled($this);
        $this->load->model('portal/queue_model');
        $this->load->model('portal/portal_model');
        $this->load->model('portal/chamber_platform_model');
        $this->load->model('doctor/doctor_model');
        $this->load->model('patient/patient_model');
        $this->load->model('finance/finance_model');
    }

    public function desk()
    {
        $data['settings'] = $this->settings_model->getSettings();
        $data['doctors'] = $this->doctor_model->getDoctor();
        $doctor_id = (int) $this->input->get('doctor_id');
        $chamber_id = (int) $this->input->get('chamber_id');
        $queue_date = $this->input->get('date') ?: date('Y-m-d');
        $chambers = array();
        if ($doctor_id) {
            $doc = $this->db->get_where('doctor', array('id' => $doctor_id), 1)->row();
            if ($doc) {
                $this->portal_model->ensureDefaultChamber($doc->id, $doc->hospital_id);
                $chambers = $this->portal_model->getChambersForDoctor($doc->id, $doc->hospital_id);
            }
        }
        $queue = array();
        if ($doctor_id && $chamber_id) {
            $queue = $this->queue_model->getQueueForDay($doctor_id, $chamber_id, $queue_date);
        }
        $data['queue'] = $queue;
        $data['chambers'] = $chambers;
        $data['doctor_id'] = $doctor_id;
        $data['chamber_id'] = $chamber_id;
        $data['queue_date'] = $queue_date;
        $this->load->view('home/dashboard', $data);
        $this->load->view('assistant/desk', $data);
        $this->load->view('home/footer');
    }

    public function checkin()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $id = (int) $this->input->post('queue_id');
        $hid = $this->session->userdata('hospital_id');
        $row = $this->queue_model->getRow($id, $hid);
        if (!$row) {
            show_404();
        }
        $this->queue_model->updateRow($id, array('status' => 'arrived'));
        $this->chamber_platform_model->logUsage($hid, 'assistant_checkin', $row->doctor_id, $this->ion_auth->get_user_id(), array('queue_id' => $id));
        redirect('assistant_chamber/desk?doctor_id=' . $row->doctor_id . '&chamber_id=' . $row->chamber_id . '&date=' . $row->queue_date);
    }

    public function vitals_save()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $id = (int) $this->input->post('queue_id');
        $hid = $this->session->userdata('hospital_id');
        $row = $this->queue_model->getRow($id, $hid);
        if (!$row) {
            show_404();
        }
        $this->db->insert('visit_vital', array(
            'hospital_id' => $hid,
            'queue_id' => $id,
            'bp_systolic' => $this->input->post('bp_sys'),
            'bp_diastolic' => $this->input->post('bp_dia'),
            'pulse' => $this->input->post('pulse'),
            'weight_kg' => $this->input->post('weight_kg'),
            'recorded_by' => $this->ion_auth->get_user_id(),
        ));
        redirect('assistant_chamber/desk?doctor_id=' . $row->doctor_id . '&chamber_id=' . $row->chamber_id . '&date=' . $row->queue_date);
    }

    public function queue_reorder()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $hid = $this->session->userdata('hospital_id');
        $ids = $this->input->post('order');
        if (!is_array($ids)) {
            show_error('Invalid', 400);
        }
        $pos = 1.0;
        foreach ($ids as $qid) {
            $qid = (int) $qid;
            $row = $this->queue_model->getRow($qid, $hid);
            if ($row) {
                $this->queue_model->updateRow($qid, array('sort_position' => $pos));
                $pos += 1.0;
            }
        }
        $this->output->set_content_type('application/json')->set_output(json_encode(array('ok' => true)));
    }

    public function emergency_bump()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $id = (int) $this->input->post('queue_id');
        $hid = $this->session->userdata('hospital_id');
        $row = $this->queue_model->getRow($id, $hid);
        if (!$row) {
            show_404();
        }
        $this->db->select_min('sort_position');
        $this->db->where('doctor_id', $row->doctor_id);
        $this->db->where('chamber_id', $row->chamber_id);
        $this->db->where('queue_date', $row->queue_date);
        $min = $this->db->get('chamber_serial_queue')->row();
        $newp = ($min && $min->sort_position !== null) ? ((float) $min->sort_position) - 1.0 : 0.0;
        $this->queue_model->updateRow($id, array('sort_position' => $newp));
        redirect('assistant_chamber/desk?doctor_id=' . $row->doctor_id . '&chamber_id=' . $row->chamber_id . '&date=' . $row->queue_date);
    }

    public function bulk_sms()
    {
        $data['settings'] = $this->settings_model->getSettings();
        $phones_prefill = '';
        if ($this->input->get('from_desk')) {
            $doctor_id = (int) $this->input->get('doctor_id');
            $chamber_id = (int) $this->input->get('chamber_id');
            $queue_date = $this->input->get('date') ?: date('Y-m-d');
            if ($doctor_id && $chamber_id) {
                $queue = $this->queue_model->getQueueForDay($doctor_id, $chamber_id, $queue_date);
                $nums = array();
                foreach ($queue as $q) {
                    $p = preg_replace('/\D+/', '', (string) $q->guest_phone);
                    if (strlen($p) >= 10) {
                        $nums[$p] = $p;
                    }
                }
                $phones_prefill = implode(', ', $nums);
            }
        }
        $data['phones_prefill'] = $phones_prefill;
        $this->load->view('home/dashboard', $data);
        $this->load->view('assistant/bulk_sms', $data);
        $this->load->view('home/footer');
    }

    public function bulk_sms_send()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $msg = trim((string) $this->input->post('message'));
        $phones = preg_split('/\s*,\s*|\s+/', trim((string) $this->input->post('phones')));
        $hid = $this->session->userdata('hospital_id');
        $sent = 0;
        $skipped = 0;
        foreach ($phones as $p) {
            $p = preg_replace('/\D+/', '', $p);
            if (strlen($p) < 10) {
                continue;
            }
            if ($this->chamber_platform_model->getSmsBalance($hid, null) < 1) {
                $skipped++;
                break;
            }
            if ($this->chamber_platform_model->sendSmsMessage($hid, $p, $msg)) {
                $this->chamber_platform_model->adjustSmsCredits($hid, -1, 'bulk_assistant', null, 'bulk', null);
                $sent++;
            } else {
                $skipped++;
            }
        }
        $this->session->set_flashdata('chamber_bulk_msg', 'Sent ' . $sent . ' SMS. Skipped or failed: ' . $skipped . '. Configure SMS under hospital settings (MSG91, Twilio, 80Kobo, or Clickatell).');
        redirect('assistant_chamber/bulk_sms');
    }

    public function mark_queue_fee_paid()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $id = (int) $this->input->post('queue_id');
        $hid = $this->session->userdata('hospital_id');
        $row = $this->queue_model->getRow($id, $hid);
        if (!$row || empty($row->patient_id)) {
            show_404();
        }
        $amount = (float) $this->input->post('amount');
        if ($amount <= 0) {
            show_error('Invalid amount', 400);
        }
        $fee_type = $this->input->post('fee_type') === 'procedure' ? 'procedure' : 'consultation';
        $category = $fee_type === 'procedure' ? 'Procedure' : 'Consultant Fee';
        $pat = $this->patient_model->getPatientById($row->patient_id);
        if (!$pat) {
            show_404();
        }
        $doc = $this->db->get_where('doctor', array('id' => $row->doctor_id))->row();
        $docname = $doc ? $doc->name : '';
        $remarks = trim('Desk ' . $fee_type . ': ' . (string) $this->input->post('pay_method') . ' ' . (string) $this->input->post('remarks'));
        $data_payment = array(
            'category_name' => $category,
            'patient' => $row->patient_id,
            'amount' => (string) $amount,
            'doctor' => $row->doctor_id,
            'discount' => '0',
            'flat_discount' => '0',
            'gross_total' => (string) $amount,
            'status' => 'paid',
            'hospital_amount' => '0',
            'doctor_amount' => (string) $amount,
            'user' => $this->ion_auth->get_user_id(),
            'patient_name' => $pat->name,
            'patient_phone' => $pat->phone,
            'patient_address' => $pat->address,
            'doctor_name' => $docname,
            'remarks' => $remarks,
            'payment_from' => 'chamber_desk',
            'appointment_id' => $row->appointment_id ? $row->appointment_id : null,
            'date' => time(),
            'date_string' => date('d-m-Y'),
        );
        $this->finance_model->insertPayment($data_payment);
        $this->chamber_platform_model->logUsage($hid, 'assistant_desk_fee', $row->doctor_id, $this->ion_auth->get_user_id(), array('queue_id' => $id, 'amount' => $amount));
        redirect('assistant_chamber/desk?doctor_id=' . $row->doctor_id . '&chamber_id=' . $row->chamber_id . '&date=' . $row->queue_date);
    }

    public function mark_serving()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $id = (int) $this->input->post('queue_id');
        $hid = $this->session->userdata('hospital_id');
        $row = $this->queue_model->getRow($id, $hid);
        if (!$row) {
            show_404();
        }
        $this->queue_model->updateRow($id, array('status' => 'serving'));
        $this->queue_model->setTicker($hid, $row->doctor_id, $row->chamber_id, $row->queue_date, $id);
        redirect('assistant_chamber/desk?doctor_id=' . $row->doctor_id . '&chamber_id=' . $row->chamber_id . '&date=' . $row->queue_date);
    }

    public function manual_booking()
    {
        $data['settings'] = $this->settings_model->getSettings();
        $data['doctors'] = $this->doctor_model->getDoctor();
        $doctor_id = (int) $this->input->get('doctor_id');
        $chambers = array();
        if ($doctor_id) {
            $doc = $this->db->get_where('doctor', array('id' => $doctor_id), 1)->row();
            if ($doc) {
                $this->portal_model->ensureDefaultChamber($doc->id, $doc->hospital_id);
                $chambers = $this->portal_model->getChambersForDoctor($doc->id, $doc->hospital_id);
            }
        }
        $data['doctor_id'] = $doctor_id;
        $data['chambers'] = $chambers;
        $this->load->view('home/dashboard', $data);
        $this->load->view('assistant/manual_booking', $data);
        $this->load->view('home/footer');
    }

    public function manual_booking_save()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $hid = $this->session->userdata('hospital_id');
        $doctor_id = (int) $this->input->post('doctor_id');
        $chamber_id = (int) $this->input->post('chamber_id');
        $queue_date = $this->input->post('queue_date');
        $patient_id = (int) $this->input->post('patient_id');
        $doc = $this->db->get_where('doctor', array('id' => $doctor_id), 1)->row();
        if (!$doc) {
            show_404();
        }
        if ((int) $doc->hospital_id !== (int) $hid) {
            show_error('Doctor is outside this hospital', 403);
        }
        $chamber = $this->portal_model->getChamberIfOwned($chamber_id, $doctor_id, $hid);
        if (!$chamber) {
            show_error('Invalid chamber for selected doctor', 400);
        }
        if (strtotime((string) $queue_date) === false) {
            show_error('Invalid queue date', 400);
        }
        $pat = null;
        if ($patient_id > 0) {
            $pat = $this->patient_model->getPatientById($patient_id);
            if (!$pat || (int) $pat->hospital_id !== (int) $hid) {
                show_404();
            }
        } else {
            $phone = preg_replace('/\D+/', '', (string) $this->input->post('patient_phone'));
            $name = trim((string) $this->input->post('patient_name'));
            if (strlen($phone) < 10 || $name === '') {
                show_error('Patient name and phone are required for a new phone booking', 400);
            }
            $pat = $this->portal_model->findPatientByPhone($hid, $phone);
            if (!$pat) {
                $pid = $this->portal_model->insertPatientMinimal(
                    $hid,
                    $name,
                    $phone,
                    $this->input->post('age'),
                    $this->input->post('gender')
                );
                $pat = $this->patient_model->getPatientById($pid);
            }
        }
        $patient_id = (int) $pat->id;
        $sn = $this->queue_model->nextSerial($doctor_id, $chamber_id, $queue_date);
        $sp = $this->queue_model->nextSortPosition($doctor_id, $chamber_id, $queue_date);
        $symptom = trim((string) $this->input->post('symptom'));
        $duration = trim((string) $this->input->post('duration'));
        $triage = null;
        if ($symptom !== '' || $duration !== '') {
            $triage = json_encode(array(
                'source' => 'assistant_manual',
                'symptom' => $symptom,
                'duration' => $duration,
                'attachments' => array(),
            ));
        }
        $this->queue_model->insertQueueRow(array(
            'hospital_id' => $hid,
            'doctor_id' => $doctor_id,
            'chamber_id' => $chamber_id,
            'queue_date' => $queue_date,
            'serial_number' => $sn,
            'sort_position' => $sp,
            'status' => 'pending',
            'patient_id' => $patient_id,
            'guest_phone' => $pat->phone,
            'guest_name' => $pat->name,
            'appointment_id' => null,
            'triage_json' => $triage,
            'remarks' => $this->input->post('remarks'),
        ));
        redirect('assistant_chamber/desk?doctor_id=' . $doctor_id . '&chamber_id=' . $chamber_id . '&date=' . $queue_date);
    }

    public function print_prescription()
    {
        $rx = (int) $this->input->get('prescription_id');
        if ($rx <= 0) {
            show_404();
        }
        redirect('prescription/viewPrescriptionPrint?id=' . $rx);
    }

    /**
     * JSON map patient_id => latest prescription id for this doctor/hospital (for desk auto-print poll).
     */
    public function desk_rx_poll()
    {
        $hid = (int) $this->session->userdata('hospital_id');
        $doctor_id = (int) $this->input->get('doctor_id');
        if ($doctor_id < 1) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array()));
            return;
        }
        $raw = (string) $this->input->get('patient_ids');
        $ids = array_filter(array_map('intval', preg_split('/[,\s]+/', $raw, -1, PREG_SPLIT_NO_EMPTY)));
        $out = array();
        foreach ($ids as $pid) {
            if ($pid < 1) {
                continue;
            }
            $this->db->where('hospital_id', $hid);
            $this->db->where('patient', $pid);
            $this->db->where('doctor', $doctor_id);
            $this->db->order_by('id', 'desc');
            $this->db->limit(1);
            $row = $this->db->get('prescription')->row();
            $this->db->reset_query();
            $out[(string) $pid] = ($row && isset($row->id)) ? (int) $row->id : 0;
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($out));
    }

    /**
     * Print latest prescription for this patient with this doctor (chamber print template + signature).
     */
    public function print_latest_rx()
    {
        $patient_id = (int) $this->input->get('patient_id');
        $doctor_id = (int) $this->input->get('doctor_id');
        $chamber_id = (int) $this->input->get('chamber_id');
        $queue_date = $this->input->get('date') ?: date('Y-m-d');
        $hid = (int) $this->session->userdata('hospital_id');
        if ($patient_id < 1 || $doctor_id < 1) {
            show_404();
        }
        $this->db->where('hospital_id', $hid);
        $this->db->where('patient', $patient_id);
        $this->db->where('doctor', $doctor_id);
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $rx = $this->db->get('prescription')->row();
        $back = 'assistant_chamber/desk?doctor_id=' . $doctor_id . '&chamber_id=' . $chamber_id . '&date=' . rawurlencode((string) $queue_date);
        if (!$rx) {
            $this->session->set_flashdata('chamber_desk_msg', 'No prescription found yet for this patient with this doctor.');
            redirect($back);
        }
        redirect('prescription/viewPrescriptionPrint?id=' . (int) $rx->id);
    }
}
