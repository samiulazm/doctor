<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Public patient portal: doctor landing, OTP, triage, serial booking, live ticker JSON.
 */
class Portal extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!function_exists('chamber_practice_enabled_for_hospital')) {
            $this->load->helper('chamber_practice');
        }
        $this->load->model('portal/portal_model');
        $this->load->model('portal/queue_model');
        $this->load->model('portal/chamber_platform_model');
        $this->load->library('Chamber_otp');
    }

    public function d($slug = '')
    {
        $slug = trim($slug);
        if ($slug === '') {
            show_404();
        }
        $ctx = $this->portal_model->getProfileBySlug($slug);
        if (!$ctx || !$ctx['doctor'] || !chamber_practice_enabled_for_hospital($this, (int) $ctx['profile']->hospital_id) || empty($ctx['profile']->booking_enabled)) {
            show_404();
        }
        $hospital_id = (int) $ctx['profile']->hospital_id;
        $this->session->set_userdata(array(
            'site_id' => $hospital_id,
            'hospital_id' => $hospital_id,
        ));
        $this->portal_model->ensureDefaultChamber($ctx['doctor']->id, $hospital_id);
        $chambers = $this->portal_model->getChambersForDoctor($ctx['doctor']->id, $hospital_id);
        $verified_phone = $this->session->userdata('portal_verified_phone');
        $verified_hospital = (int) $this->session->userdata('portal_verified_hospital');
        $verified = ($verified_phone && $verified_hospital === $hospital_id) ? $verified_phone : '';
        $upcoming = null;
        $prescriptions = array();
        $lab_reports = array();
        if ($verified) {
            $patient = $this->portal_model->findPatientByPhone($hospital_id, $verified);
            if ($patient) {
                $upcoming = $this->portal_model->getUpcomingAppointment($hospital_id, (int) $patient->id);
                $prescriptions = $this->portal_model->getPrescriptionsForPatient($hospital_id, (int) $patient->id);
                $lab_reports = $this->portal_model->getLabReportsForPatient($hospital_id, (int) $patient->id);
            }
        }
        $data = array(
            'slug' => $slug,
            'profile' => $ctx['profile'],
            'doctor' => $ctx['doctor'],
            'chambers' => $chambers,
            'hospital_id' => $hospital_id,
            'verified' => $verified,
            'upcoming' => $upcoming,
            'prescriptions' => $prescriptions,
            'lab_reports' => $lab_reports,
        );
        $this->load->view('portal/layout_public', array('content' => $this->load->view('portal/landing', $data, true)));
    }

    public function ticker_json()
    {
        $doctor_id = (int) $this->input->get('doctor_id');
        $chamber_id = (int) $this->input->get('chamber_id');
        $date = $this->input->get('date');
        if (!$doctor_id || !$chamber_id || !$date) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('ok' => false)));
            return;
        }
        $chamber = $this->db->select('hospital_id')->get_where('doctor_chamber', array(
            'id' => $chamber_id,
            'doctor_id' => $doctor_id,
        ), 1)->row();
        if (!$chamber || !chamber_practice_enabled_for_hospital($this, (int) $chamber->hospital_id)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('ok' => false)));
            return;
        }
        $row = $this->queue_model->getTicker($doctor_id, $chamber_id, $date);
        $out = array(
            'ok' => true,
            'serial' => $row ? (int) $row->serial_number : null,
            'status' => $row ? $row->status : null,
            'csrf' => $this->security->get_csrf_hash(),
        );
        $queue_id_param = (int) $this->input->get('queue_id');
        if ($queue_id_param) {
            $patient_row = $this->queue_model->getRowById($queue_id_param);
            $patient_serial = null;
            $estimated_wait = null;
            if ($patient_row
                && (int) $patient_row->hospital_id === (int) $chamber->hospital_id
                && (int) $patient_row->doctor_id === $doctor_id
                && (int) $patient_row->chamber_id === $chamber_id
                && (string) $patient_row->queue_date === (string) $date) {
                $patient_serial = (int) $patient_row->serial_number;
                if ($out['serial'] !== null) {
                    $estimated_wait = max(0, $patient_serial - (int) $out['serial']) * 5;
                }
            }
            $out['patient_serial'] = $patient_serial;
            $out['estimated_wait'] = $estimated_wait;
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($out));
    }

    public function queue($queue_id = 0)
    {
        $queue_id = (int) $queue_id;
        if (!$queue_id) {
            show_404();
        }
        $row = $this->queue_model->getRowById($queue_id);
        if (!$row || !chamber_practice_enabled_for_hospital($this, (int) $row->hospital_id)) {
            show_404();
        }
        $doctor = $this->db->get_where('doctor', array('id' => $row->doctor_id), 1)->row();
        $chamber = $this->db->get_where('doctor_chamber', array('id' => $row->chamber_id), 1)->row();
        $data = array(
            'queue_row' => $row,
            'doctor' => $doctor,
            'chamber' => $chamber,
            'queue_id' => $queue_id,
            'doctor_id' => (int) $row->doctor_id,
            'chamber_id' => (int) $row->chamber_id,
            'queue_date' => $row->queue_date,
        );
        $this->load->view('portal/layout_public', array('content' => $this->load->view('portal/queue', $data, true)));
    }

    public function slots_json()
    {
        $chamber_id = (int) $this->input->get('chamber_id');
        $date = $this->input->get('date');
        if (!$chamber_id || !$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('ok' => false)));
            return;
        }
        $chamber = $this->db->get_where('doctor_chamber', array('id' => $chamber_id, 'is_active' => 1), 1)->row();
        if (!$chamber || !chamber_practice_enabled_for_hospital($this, (int) $chamber->hospital_id)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('ok' => false)));
            return;
        }
        $this->db->where('hospital_id', (int) $chamber->hospital_id);
        $this->db->where('doctor_id', (int) $chamber->doctor_id);
        $this->db->where('exception_date', $date);
        $this->db->group_start();
        $this->db->where('chamber_id', $chamber_id);
        $this->db->or_where('chamber_id IS NULL', null, false);
        $this->db->group_end();
        $this->db->order_by('chamber_id', 'desc');
        $this->db->limit(1);
        $exception = $this->db->get('doctor_schedule_exception')->row();
        if ($exception && (int) $exception->is_closed === 1) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array(
                'ok' => false,
                'msg' => 'This chamber is closed on the selected date. Please choose another date.',
            )));
            return;
        }
        $hours = !empty($chamber->weekly_hours_json) ? @json_decode($chamber->weekly_hours_json, true) : array();
        $day_num = (int) date('w', strtotime($date));
        $day_key = strtolower(date('D', strtotime($date)));
        $day_map = array('mon' => 'mon', 'tue' => 'tue', 'wed' => 'wed', 'thu' => 'thu', 'fri' => 'fri', 'sat' => 'sat', 'sun' => 'sun');
        $day_key = isset($day_map[$day_key]) ? $day_map[$day_key] : (string) $day_num;
        $day_hours = null;
        if (is_array($hours)) {
            if (!empty($hours[$day_key])) {
                $day_hours = $hours[$day_key];
            } elseif (!empty($hours[(string) $day_num])) {
                $day_hours = $hours[(string) $day_num];
            }
        }
        $start = is_array($day_hours) ? (isset($day_hours['open']) ? $day_hours['open'] : (isset($day_hours['s_time']) ? $day_hours['s_time'] : '')) : '';
        $end = is_array($day_hours) ? (isset($day_hours['close']) ? $day_hours['close'] : (isset($day_hours['e_time']) ? $day_hours['e_time'] : '')) : '';
        if ($exception && !empty($exception->open_time) && !empty($exception->close_time)) {
            $start = $exception->open_time;
            $end = $exception->close_time;
        }
        if (!$start || !$end) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array(
                'ok' => false,
                'msg' => 'No regular hours are available for this chamber on the selected day.',
            )));
            return;
        }
        $s_time = strtotime($date . ' ' . $start);
        $e_time = strtotime($date . ' ' . $end);
        if ($s_time === false || $e_time === false || $e_time <= $s_time) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array(
                'ok' => false,
                'msg' => 'No regular hours are available for this chamber on the selected day.',
            )));
            return;
        }
        $slots = array();
        for ($t = $s_time; $t < $e_time; $t += 1800) {
            $slots[] = array(
                'time' => date('H:i', $t),
                'label' => date('g:i A', $t),
                'available' => true,
            );
        }
        $this->output->set_content_type('application/json')->set_output(json_encode(array('ok' => true, 'slots' => $slots)));
    }

    public function request_otp()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $slug = $this->input->post('slug');
        $mobile = preg_replace('/\D+/', '', $this->input->post('mobile'));
        $ctx = $this->portal_model->getProfileBySlug($slug);
        if (!$ctx || !chamber_practice_enabled_for_hospital($this, (int) $ctx['profile']->hospital_id) || strlen($mobile) < 10) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array(
                'ok' => false,
                'msg' => 'Invalid request',
                'csrf' => $this->security->get_csrf_hash(),
            )));
            return;
        }
        $hid = (int) $ctx['profile']->hospital_id;
        $maxPer15 = 6;
        $recent = $this->portal_model->countRecentOtpRequests($hid, $mobile, time() - 900);
        if ($recent >= $maxPer15) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array(
                'ok' => false,
                'msg' => 'Too many requests. Please wait a few minutes and try again.',
                'csrf' => $this->security->get_csrf_hash(),
            )));
            return;
        }
        $otpLib = $this->chamber_otp;
        $plain = $otpLib->generatePlain();
        $hash = $otpLib->hash($plain);
        $exp = date('Y-m-d H:i:s', time() + $otpLib->ttlSeconds());
        $this->portal_model->insertOtpRow($hid, $mobile, $hash, $exp);
        $this->_send_otp_sms($hid, $mobile, $plain);
        $this->session->set_userdata('portal_otp_mobile', $mobile);
        $this->session->set_userdata('portal_slug', $slug);
        $this->output->set_content_type('application/json')->set_output(json_encode(array(
            'ok' => true,
            'csrf' => $this->security->get_csrf_hash(),
        )));
    }

    public function verify_otp()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $slug = $this->input->post('slug');
        $mobile = preg_replace('/\D+/', '', $this->input->post('mobile'));
        $code = trim($this->input->post('otp'));
        $ctx = $this->portal_model->getProfileBySlug($slug);
        if (!$ctx || !chamber_practice_enabled_for_hospital($this, (int) $ctx['profile']->hospital_id)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array(
                'ok' => false,
                'csrf' => $this->security->get_csrf_hash(),
            )));
            return;
        }
        $hid = (int) $ctx['profile']->hospital_id;
        $row = $this->portal_model->getLatestOtp($hid, $mobile);
        if (!$row || strtotime($row->expires_at) < time()) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array(
                'ok' => false,
                'msg' => 'This code has expired. Please request a new OTP.',
                'csrf' => $this->security->get_csrf_hash(),
            )));
            return;
        }
        if ((int) $row->attempts >= 8) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array(
                'ok' => false,
                'msg' => 'Too many attempts. Please request a new OTP.',
                'csrf' => $this->security->get_csrf_hash(),
            )));
            return;
        }
        if (!$this->chamber_otp->verify($code, $row->otp_hash)) {
            $this->portal_model->incrementOtpAttempts($row->id, (int) $row->attempts);
            $this->output->set_content_type('application/json')->set_output(json_encode(array(
                'ok' => false,
                'msg' => 'Incorrect code. Please try again.',
                'csrf' => $this->security->get_csrf_hash(),
            )));
            return;
        }
        $this->portal_model->markOtpVerified($row->id);
        $this->session->set_userdata('portal_verified_phone', $mobile);
        $this->session->set_userdata('portal_verified_hospital', $hid);
        $this->session->set_userdata('portal_slug', $slug);
        $this->output->set_content_type('application/json')->set_output(json_encode(array(
            'ok' => true,
            'csrf' => $this->security->get_csrf_hash(),
        )));
    }

    public function triage($slug = '')
    {
        $ctx = $this->portal_model->getProfileBySlug($slug);
        if (!$ctx || !chamber_practice_enabled_for_hospital($this, (int) $ctx['profile']->hospital_id)) {
            show_404();
        }
        $hid = (int) $ctx['profile']->hospital_id;
        $this->session->set_userdata(array('site_id' => $hid, 'hospital_id' => $hid));
        $phone = $this->session->userdata('portal_verified_phone');
        if ((int) $this->session->userdata('portal_verified_hospital') !== $hid) {
            $phone = '';
        }
        $this->portal_model->ensureDefaultChamber($ctx['doctor']->id, $hid);
        $data = array(
            'slug' => $slug,
            'profile' => $ctx['profile'],
            'doctor' => $ctx['doctor'],
            'chambers' => $this->portal_model->getChambersForDoctor($ctx['doctor']->id, $hid),
            'phone' => $phone,
            'verified' => !empty($phone),
        );
        $this->load->view('portal/layout_public', array('content' => $this->load->view('portal/triage', $data, true)));
    }

    public function complete_booking()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $slug = $this->input->post('slug');
        $ctx = $this->portal_model->getProfileBySlug($slug);
        if (!$ctx || !chamber_practice_enabled_for_hospital($this, (int) $ctx['profile']->hospital_id)) {
            show_404();
        }
        $phone = $this->session->userdata('portal_verified_phone');
        if (!$phone || (int) $this->session->userdata('portal_verified_hospital') !== (int) $ctx['profile']->hospital_id) {
            show_error('Session expired', 403);
        }
        $hid = (int) $ctx['profile']->hospital_id;
        $this->session->set_userdata(array('site_id' => $hid, 'hospital_id' => $hid));
        $doctor = $ctx['doctor'];
        $chamber_id = (int) $this->input->post('chamber_id');
        $queue_date = $this->input->post('queue_date');
        if (!$chamber_id || !$queue_date) {
            redirect('portal/triage/' . rawurlencode($slug));
        }
        $chamber = $this->portal_model->getChamberIfOwned($chamber_id, $doctor->id, $hid);
        if (!$chamber) {
            show_error('Invalid chamber', 400);
        }
        $qd_ts = strtotime((string) $queue_date);
        if ($qd_ts === false) {
            show_error('Invalid queue date', 400);
        }
        if ($qd_ts < strtotime('today')) {
            show_error('Queue date cannot be in the past', 400);
        }
        $s_time = '09:00';
        $e_time = '09:30';
        $this->db->where('hospital_id', $hid);
        $this->db->where('doctor_id', $doctor->id);
        $this->db->where('exception_date', date('Y-m-d', $qd_ts));
        $this->db->group_start();
        $this->db->where('chamber_id', $chamber_id);
        $this->db->or_where('chamber_id IS NULL', null, false);
        $this->db->group_end();
        $this->db->order_by('chamber_id', 'desc');
        $this->db->limit(1);
        $exception = $this->db->get('doctor_schedule_exception')->row();
        if ($exception && (int) $exception->is_closed === 1) {
            show_error('This chamber is closed on the selected date. Please choose another date.', 400);
        }
        if ($exception && !empty($exception->open_time) && !empty($exception->close_time)) {
            $s_time = $exception->open_time;
            $e_time = $exception->close_time;
        } elseif (!empty($chamber->weekly_hours_json)) {
            $weekly = @json_decode($chamber->weekly_hours_json, true);
            if (is_array($weekly) && !empty($weekly)) {
                $day_key = strtolower(date('D', $qd_ts));
                $day_key = array('mon' => 'mon', 'tue' => 'tue', 'wed' => 'wed', 'thu' => 'thu', 'fri' => 'fri', 'sat' => 'sat', 'sun' => 'sun')[$day_key];
                if (empty($weekly[$day_key]) || empty($weekly[$day_key]['open']) || empty($weekly[$day_key]['close'])) {
                    show_error('This chamber has no regular hours on the selected date.', 400);
                }
                $s_time = $weekly[$day_key]['open'];
                $e_time = $weekly[$day_key]['close'];
            }
        }
        $slot_time = $this->input->post('slot_time');
        if (!empty($slot_time)) {
            if (!preg_match('/^\d{2}:\d{2}$/', $slot_time)) {
                show_error('Invalid slot time', 400);
            }
            $range_start = strtotime(date('Y-m-d', $qd_ts) . ' ' . $s_time);
            $range_end = strtotime(date('Y-m-d', $qd_ts) . ' ' . $e_time);
            $slot_start = strtotime(date('Y-m-d', $qd_ts) . ' ' . $slot_time);
            if ($slot_start === false || $range_start === false || $range_end === false || $slot_start < $range_start || $slot_start >= $range_end) {
                show_error('Invalid slot time', 400);
            }
            $s_time = date('H:i', $slot_start);
            $e_time = date('H:i', $slot_start + 1800);
        }
        $maxBookingsPer10Min = 5;
        $recentBookings = $this->portal_model->countRecentPortalQueueBookings($hid, $phone, time() - 600);
        if ($recentBookings >= $maxBookingsPer10Min) {
            show_error('Too many bookings from this number recently. Please try again in a few minutes.', 429);
        }
        $name = $this->input->post('name');
        $age = $this->input->post('age');
        $gender = $this->input->post('gender');
        $symptom = $this->input->post('symptom');
        $duration = $this->input->post('duration');
        $triage = array(
            'symptom' => $symptom,
            'duration' => $duration,
            'attachments' => array(),
        );
        if (!empty($_FILES['attachments']['name'])) {
            $this->load->helper(array('file', 'security'));
            $uploadDir = FCPATH . 'uploads/chamber_triage/';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }
            $names = $_FILES['attachments']['name'];
            if (is_array($names)) {
                foreach ($names as $i => $nm) {
                    if (empty($_FILES['attachments']['tmp_name'][$i])) {
                        continue;
                    }
                    $ext = pathinfo($nm, PATHINFO_EXTENSION);
                    $safe = 't_' . time() . '_' . $i . '.' . preg_replace('/[^a-zA-Z0-9]/', '', $ext);
                    $dest = $uploadDir . $safe;
                    if (@move_uploaded_file($_FILES['attachments']['tmp_name'][$i], $dest)) {
                        $triage['attachments'][] = 'uploads/chamber_triage/' . $safe;
                    }
                }
            }
        }
        $patient = $this->portal_model->findPatientByPhone($hid, $phone);
        if (!$patient) {
            $pid = $this->portal_model->insertPatientMinimal($hid, $name, $phone, $age, $gender);
        } else {
            $pid = (int) $patient->id;
        }
        $add_date = date('m/d/Y');
        $registration_time = time();
        $date_ts = strtotime($queue_date);
        $time_slot = $s_time . ' To ' . $e_time;
        $patientname = $name;
        $doctorname = $doctor->name;
        $room_id = 'chamber-' . $phone . '-' . rand(1000, 9999);
        $app_time = strtotime(date('d-m-Y', $date_ts) . ' ' . $s_time);
        $app_time_full_format = date('d-m-Y', $date_ts) . ' ' . $s_time . '-' . $e_time;
        $advance_fee = (isset($ctx['profile']->advance_booking_fee) && $this->db->field_exists('advance_booking_fee', 'doctor_portal_profile')) ? (float) $ctx['profile']->advance_booking_fee : 0.0;
        $visit_charges = (string) max(0, $advance_fee);
        $feeAmt = (float) str_replace(array(',', ' '), array('', ''), (string) $visit_charges);
        $appt = array(
            'patient' => $pid,
            'patientname' => $patientname,
            'doctor' => $doctor->id,
            'doctorname' => $doctorname,
            'date' => $date_ts,
            's_time' => $s_time,
            'e_time' => $e_time,
            'time_slot' => $time_slot,
            'remarks' => 'Chamber portal serial',
            'add_date' => $add_date,
            'registration_time' => $registration_time,
            'status' => 'Confirmed',
            's_time_key' => $s_time,
            'user' => 0,
            'request' => 'Yes',
            'room_id' => $room_id,
            'live_meeting_link' => '',
            'app_time' => $app_time,
            'app_time_full_format' => $app_time_full_format,
            'visit_description' => 'Walk-in serial',
            'visit_charges' => $visit_charges,
            'discount' => '0',
            'grand_total' => $visit_charges,
            'payment_status' => 'unpaid',
        );
        $this->db->insert('appointment', array_merge($appt, array('hospital_id' => $hid)));
        $appointment_id = (int) $this->db->insert_id();
        if ($feeAmt > 0) {
            $this->load->model('patient/patient_model');
            $this->load->model('finance/finance_model');
            $pat_row = $this->patient_model->getPatientById($pid);
            $fee_str = (string) $feeAmt;
            $data_payment = array(
                'category_name' => 'Consultant Fee',
                'patient' => $pid,
                'amount' => $fee_str,
                'doctor' => $doctor->id,
                'discount' => '0',
                'flat_discount' => '0',
                'gross_total' => $fee_str,
                'status' => 'unpaid',
                'hospital_amount' => '0',
                'doctor_amount' => $fee_str,
                'user' => 0,
                'patient_name' => $pat_row ? $pat_row->name : $name,
                'patient_phone' => $pat_row ? $pat_row->phone : $phone,
                'patient_address' => ($pat_row && $pat_row->address) ? $pat_row->address : '',
                'doctor_name' => $doctorname,
                'remarks' => 'Chamber portal serial',
                'payment_from' => 'appointment',
                'appointment_id' => $appointment_id,
                'date' => time(),
                'date_string' => date('d-m-Y'),
            );
            $this->finance_model->insertPayment($data_payment);
            $payment_id = (int) $this->db->insert_id();
            $this->db->where('id', $appointment_id);
            $this->db->update('appointment', array('payment_id' => $payment_id));
        }
        $sn = $this->queue_model->nextSerial($doctor->id, $chamber_id, $queue_date);
        $sp = $this->queue_model->nextSortPosition($doctor->id, $chamber_id, $queue_date);
        $advFee = $feeAmt > 0 ? $feeAmt : null;
        $advStat = $feeAmt > 0 ? 'pending' : 'none';
        $qid = $this->queue_model->insertQueueRow(array(
            'hospital_id' => $hid,
            'doctor_id' => $doctor->id,
            'chamber_id' => $chamber_id,
            'queue_date' => $queue_date,
            'serial_number' => $sn,
            'sort_position' => $sp,
            'status' => 'pending',
            'patient_id' => $pid,
            'guest_phone' => $phone,
            'guest_name' => $name,
            'appointment_id' => $appointment_id,
            'triage_json' => json_encode($triage),
            'advance_fee_amount' => $advFee,
            'advance_payment_status' => $advStat,
        ));
        $this->chamber_platform_model->logUsage($hid, 'portal_booking', $doctor->id, null, array('queue_id' => $qid));
        $sms_body = 'Serial booking confirmed: #' . (int) $sn . ' with ' . $doctorname . ' on ' . $queue_date . '.';
        $this->chamber_platform_model->sendSmsMessage($hid, $phone, $sms_body);
        $this->session->unset_userdata(array('portal_verified_phone', 'portal_verified_hospital', 'portal_otp_mobile'));
        $this->load->model('settings/settings_model');
        $st = $this->settings_model->getSettings();
        $currency = ($st && !empty($st->currency)) ? $st->currency : 'BDT';
        $data = array(
            'serial' => $sn,
            'slug' => $slug,
            'doctorname' => $doctorname,
            'queue_id' => $qid,
            'queue_date' => $queue_date,
            'advance_fee' => $advFee,
            'currency' => $currency,
        );
        $this->load->view('portal/layout_public', array('content' => $this->load->view('portal/book_success', $data, true)));
    }

    public function prescription($id = 0)
    {
        $id = (int) $id;
        $phone = $this->session->userdata('portal_verified_phone');
        if (!$phone || !$id) {
            show_404();
        }
        $hospital_id = (int) $this->session->userdata('portal_verified_hospital');
        $rx = $this->portal_model->getPrescriptionForPortal($id);
        if (!$rx || (int) $rx->hospital_id !== $hospital_id) {
            show_404();
        }
        $patient = $this->portal_model->findPatientByPhone($hospital_id, $phone);
        if (!$patient || (int) $rx->patient !== (int) $patient->id) {
            show_error('Access denied', 403);
        }
        $data = array(
            'rx' => $rx,
            'medicines' => $this->_parse_prescription_medicines($rx),
            'patient' => $patient,
            'rx_id' => $id,
        );
        $this->load->view('portal/layout_public', array('content' => $this->load->view('portal/rx_detail', $data, true)));
    }

    public function prescription_pdf($id = 0)
    {
        $id = (int) $id;
        $phone = $this->session->userdata('portal_verified_phone');
        if (!$phone || !$id) {
            show_404();
        }
        $hospital_id = (int) $this->session->userdata('portal_verified_hospital');
        $rx = $this->portal_model->getPrescriptionForPortal($id);
        if (!$rx || (int) $rx->hospital_id !== $hospital_id) {
            show_404();
        }
        $patient = $this->portal_model->findPatientByPhone($hospital_id, $phone);
        if (!$patient || (int) $rx->patient !== (int) $patient->id) {
            show_error('Access denied', 403);
        }
        $data = array(
            'rx' => $rx,
            'medicines' => $this->_parse_prescription_medicines($rx),
            'patient' => $patient,
            'rx_id' => $id,
        );
        $html = $this->load->view('portal/prescription_pdf_template', $data, true);
        $tmp_dir = APPPATH . '../files/mpdf-tmp';
        if (!is_dir($tmp_dir)) {
            mkdir($tmp_dir, 0755, true);
        }
        $mpdf = new \Mpdf\Mpdf(array('format' => 'A4', 'tempDir' => $tmp_dir));
        $mpdf->setAutoTopMargin = 'stretch';
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->WriteHTML($html);
        $mpdf->Output('prescription-' . $id . '.pdf', 'D');
        exit;
    }

    protected function _parse_prescription_medicines($rx)
    {
        $medicines = array();
        if (empty($rx->medicine)) {
            return $medicines;
        }
        $entries = explode('###', $rx->medicine);
        foreach ($entries as $entry) {
            $entry = trim($entry);
            if ($entry === '') {
                continue;
            }
            $fields = explode('***', $entry);
            if (count($fields) < 4) {
                continue;
            }
            $name = trim($fields[0]);
            if (is_numeric($name)) {
                $med_row = $this->db->get_where('medicine', array('id' => (int) $name), 1)->row();
                if ($med_row) {
                    $name = !empty($med_row->name) ? $med_row->name : (!empty($med_row->brand_name) ? $med_row->brand_name : $name);
                }
            }
            $medicines[] = array(
                'name' => $name,
                'dose' => isset($fields[1]) ? trim($fields[1]) : '',
                'frequency' => isset($fields[2]) ? trim($fields[2]) : '',
                'days' => isset($fields[3]) ? trim($fields[3]) : '',
                'instruction' => isset($fields[4]) ? trim($fields[4]) : '',
            );
        }
        return $medicines;
    }

    protected function _send_otp_sms($hospital_id, $mobile, $plain)
    {
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            log_message('info', 'Chamber OTP (dev): ' . $plain . ' to ' . $mobile);
        }
        $msg = 'Your chamber booking code is ' . $plain;
        $this->chamber_platform_model->sendSmsMessage($hospital_id, $mobile, $msg);
    }
}
