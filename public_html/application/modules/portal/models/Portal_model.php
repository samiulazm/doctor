<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Portal_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getProfileBySlug($slug)
    {
        $this->db->where('public_slug', $slug);
        $p = $this->db->get('doctor_portal_profile')->row();
        if (!$p) {
            return null;
        }
        $this->db->where('id', $p->doctor_id);
        $this->db->where('hospital_id', $p->hospital_id);
        $doc = $this->db->get('doctor')->row();
        return array('profile' => $p, 'doctor' => $doc);
    }

    public function getChambersForDoctor($doctor_id, $hospital_id)
    {
        $this->db->where('doctor_id', $doctor_id);
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('is_active', 1);
        $this->db->order_by('sort_order', 'asc');
        $rows = $this->db->get('doctor_chamber')->result();
        foreach ($rows as $row) {
            $decoded = !empty($row->weekly_hours_json) ? @json_decode($row->weekly_hours_json, true) : array();
            $row->weekly_hours = is_array($decoded) ? $decoded : array();
        }
        return $rows;
    }

    /** Active chamber row if it belongs to this doctor and hospital. */
    public function getChamberIfOwned($chamber_id, $doctor_id, $hospital_id)
    {
        return $this->db->get_where('doctor_chamber', array(
            'id' => (int) $chamber_id,
            'doctor_id' => (int) $doctor_id,
            'hospital_id' => (int) $hospital_id,
            'is_active' => 1,
        ), 1)->row();
    }

    /**
     * Portal bookings store triage_json; desk walk-ins leave it null.
     */
    public function countRecentPortalQueueBookings($hospital_id, $phone, $since_ts)
    {
        $this->db->where('hospital_id', (int) $hospital_id);
        $this->db->where('guest_phone', $phone);
        $this->db->where('created_at >=', date('Y-m-d H:i:s', (int) $since_ts));
        $this->db->where('triage_json IS NOT NULL', null, false);
        return (int) $this->db->count_all_results('chamber_serial_queue');
    }

    public function ensureDefaultChamber($doctor_id, $hospital_id)
    {
        $this->db->where('doctor_id', $doctor_id);
        $this->db->where('hospital_id', $hospital_id);
        $n = $this->db->count_all_results('doctor_chamber');
        if ($n > 0) {
            return;
        }
        $this->db->insert('doctor_chamber', array(
            'hospital_id' => $hospital_id,
            'doctor_id' => $doctor_id,
            'name' => 'Main chamber',
            'sort_order' => 0,
            'is_active' => 1,
        ));
    }

    public function countRecentOtpRequests($hospital_id, $mobile, $since_ts)
    {
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('mobile', $mobile);
        $this->db->where('created_at >=', date('Y-m-d H:i:s', $since_ts));
        return (int) $this->db->count_all_results('booking_otp_session');
    }

    public function insertOtpRow($hospital_id, $mobile, $hash, $expires_at)
    {
        $this->db->insert('booking_otp_session', array(
            'hospital_id' => $hospital_id,
            'mobile' => $mobile,
            'otp_hash' => $hash,
            'expires_at' => $expires_at,
            'attempts' => 0,
        ));
        return $this->db->insert_id();
    }

    public function getLatestOtp($hospital_id, $mobile)
    {
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('mobile', $mobile);
        $this->db->where('verified_at IS NULL', null, false);
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        return $this->db->get('booking_otp_session')->row();
    }

    public function markOtpVerified($id)
    {
        $this->db->where('id', $id);
        $this->db->update('booking_otp_session', array('verified_at' => date('Y-m-d H:i:s')));
    }

    public function incrementOtpAttempts($id, $attempts)
    {
        $this->db->where('id', $id);
        $this->db->update('booking_otp_session', array('attempts' => $attempts + 1));
    }

    public function findPatientByPhone($hospital_id, $phone)
    {
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('phone', $phone);
        $this->db->limit(1);
        return $this->db->get('patient')->row();
    }

    public function insertPatientMinimal($hospital_id, $name, $phone, $age, $gender)
    {
        $pid = (int) (rand(100000, 9999999));
        $email = 'portal-' . $pid . '-' . time() . '@patient.local';
        $this->db->insert('patient', array(
            'hospital_id' => $hospital_id,
            'patient_id' => (string) $pid,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'age' => $age,
            'sex' => $gender,
            'add_date' => date('m/d/Y'),
            'registration_time' => time(),
            'how_added' => 'chamber_portal',
        ));
        return $this->db->insert_id();
    }
}
