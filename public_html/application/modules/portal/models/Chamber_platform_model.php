<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chamber_platform_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function logUsage($hospital_id, $event_type, $doctor_id = null, $user_id = null, $meta = null)
    {
        $this->db->insert('usage_analytics_event', array(
            'hospital_id' => $hospital_id,
            'doctor_id' => $doctor_id,
            'user_id' => $user_id,
            'event_type' => $event_type,
            'meta_json' => $meta ? json_encode($meta) : null,
        ));
    }

    public function getSmsBalance($hospital_id, $doctor_id = null)
    {
        $this->db->where('hospital_id', $hospital_id);
        if ($doctor_id !== null) {
            $this->db->group_start();
            $this->db->where('doctor_id', $doctor_id);
            $this->db->or_where('doctor_id IS NULL', null, false);
            $this->db->group_end();
        }
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $r = $this->db->get('sms_credit_ledger')->row();
        return $r ? (int) $r->balance_after : 0;
    }

    public function adjustSmsCredits($hospital_id, $delta, $reason, $doctor_id = null, $ref_type = null, $ref_id = null)
    {
        $bal = $this->getSmsBalance($hospital_id, $doctor_id);
        $newb = max(0, $bal + (int) $delta);
        $this->db->insert('sms_credit_ledger', array(
            'hospital_id' => $hospital_id,
            'doctor_id' => $doctor_id,
            'delta' => (int) $delta,
            'balance_after' => $newb,
            'reason' => $reason,
            'ref_type' => $ref_type,
            'ref_id' => $ref_id,
        ));
        return $newb;
    }

    public function getPlans()
    {
        $this->db->where('is_active', 1);
        return $this->db->get('subscription_plan')->result();
    }

    public function getSubscriptionsOverview()
    {
        $this->db->select('ds.*, d.name as doctor_name, d.hospital_id, sp.name as plan_name, sp.interval_unit, sp.price');
        $this->db->from('doctor_subscription ds');
        $this->db->join('doctor d', 'd.id = ds.doctor_id', 'left');
        $this->db->join('subscription_plan sp', 'sp.id = ds.plan_id', 'left');
        $this->db->order_by('ds.id', 'desc');
        $this->db->limit(200);
        return $this->db->get()->result();
    }

    public function insertReferral($data)
    {
        $this->db->insert('referral_lab_event', $data);
        return $this->db->insert_id();
    }

    public function markReferralReady($id)
    {
        $id = (int) $id;
        $this->db->where('id', $id);
        $ref = $this->db->get('referral_lab_event')->row();
        if (!$ref) {
            return false;
        }
        if ($ref->status === 'report_ready') {
            return true;
        }
        $this->db->where('id', $id);
        $this->db->update('referral_lab_event', array(
            'status' => 'report_ready',
            'report_ready_at' => date('Y-m-d H:i:s'),
        ));
        $hid = (int) $ref->hospital_id;
        $code = (string) $ref->discount_code;
        $lab = trim((string) $ref->lab_name);
        $doc = $this->db->get_where('doctor', array('id' => (int) $ref->doctor_id))->row();
        if ($doc && !empty($doc->phone)) {
            $msg = 'Lab report ready for referral ' . $code . ($lab ? (' — ' . $lab) : '') . '.';
            $this->sendSmsMessage($hid, $doc->phone, $msg);
        }
        if (!empty($ref->patient_phone)) {
            $msg2 = 'Your lab report is ready. Your referral code was ' . $code . '.';
            $this->sendSmsMessage($hid, $ref->patient_phone, $msg2);
        }
        $this->logUsage($hid, 'referral_report_ready', (int) $ref->doctor_id, null, array('referral_id' => $id));
        return true;
    }

    public function insertBdIntent($row)
    {
        $this->db->insert('bd_payment_intent', $row);
        return $this->db->insert_id();
    }

    public function updateBdIntent($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('bd_payment_intent', $data);
    }

    public function getBdIntent($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('bd_payment_intent')->row();
    }

    /** Resolve chamber bKash intent after customer returns from bKash (gateway_session_id stores paymentID). */
    public function getBdIntentByBkashPaymentId($payment_id)
    {
        $payment_id = trim((string) $payment_id);
        if ($payment_id === '') {
            return null;
        }
        $this->db->where('gateway', 'bkash');
        $this->db->where('gateway_session_id', $payment_id);
        return $this->db->get('bd_payment_intent')->row();
    }

    public function getRecentReferrals($limit = 50)
    {
        $this->db->order_by('id', 'desc');
        $this->db->limit($limit);
        return $this->db->get('referral_lab_event')->result();
    }

    public function getReferralsForPatient($patient_id, $limit = 100)
    {
        $this->db->where('patient_id', (int) $patient_id);
        $this->db->order_by('id', 'desc');
        $this->db->limit($limit);
        return $this->db->get('referral_lab_event')->result();
    }

    /**
     * Send a plain SMS using the hospital’s configured gateway (same providers as appointment cron SMS).
     *
     * @return bool True if dispatch was attempted via a known provider; false if misconfigured or unknown gateway.
     */
    public function sendSmsMessage($hospital_id, $phone, $message)
    {
        $phone = preg_replace('/\D+/', '', $phone);
        if (strlen($phone) < 10 || $message === '') {
            return false;
        }
        $this->db->where('hospital_id', $hospital_id);
        $settings = $this->db->get('settings')->row();
        if (!$settings || empty($settings->sms_gateway)) {
            return false;
        }
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('name', $settings->sms_gateway);
        $smsSettings = $this->db->get('sms_settings')->row();
        if (!$smsSettings) {
            return false;
        }
        $gw = $smsSettings->name;
        if ($gw === 'MSG91' && !empty($smsSettings->authkey) && !empty($smsSettings->sender)) {
            $enc = rawurlencode($message);
            @file_get_contents('http://world.msg91.com/api/v2/sendsms?authkey=' . $smsSettings->authkey . '&mobiles=' . $phone . '&message=' . $enc . '&sender=' . $smsSettings->sender . '&route=4&country=0');
            return true;
        }
        if ($gw === 'Twilio' && !empty($smsSettings->sid) && !empty($smsSettings->token) && !empty($smsSettings->sendernumber)) {
            if (!class_exists('\Twilio\Rest\Client')) {
                log_message('error', 'Chamber SMS: Twilio SDK not loaded');
                return false;
            }
            try {
                $client = new \Twilio\Rest\Client($smsSettings->sid, $smsSettings->token);
                $client->messages->create(
                    $phone,
                    array(
                        'from' => $smsSettings->sendernumber,
                        'body' => $message,
                    )
                );
                return true;
            } catch (\Exception $e) {
                log_message('error', 'Chamber SMS Twilio: ' . $e->getMessage());
                return false;
            }
        }
        if ($gw === '80Kobo' && !empty($smsSettings->email) && !empty($smsSettings->password) && !empty($smsSettings->sender_name)) {
            $payload = array(
                'email' => $smsSettings->email,
                'password' => $smsSettings->password,
                'message' => $message,
                'sender_name' => $smsSettings->sender_name,
                'recipients' => $phone,
            );
            $data_string = json_encode($payload);
            $ch = curl_init('https://api.80kobosms.com/v2/app/sms');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string),
            ));
            $result = curl_exec($ch);
            $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return ($result !== false && ($code === 200 || $code === 201));
        }
        if ($gw === 'Clickatell' && !empty($smsSettings->username) && !empty($smsSettings->password) && !empty($smsSettings->api_id)) {
            $url = 'https://api.clickatell.com/http/sendmsg?user=' . rawurlencode($smsSettings->username)
                . '&password=' . rawurlencode($smsSettings->password)
                . '&api_id=' . rawurlencode($smsSettings->api_id)
                . '&to=' . rawurlencode($phone)
                . '&text=' . rawurlencode($message);
            $r = @file_get_contents($url);
            return ($r !== false);
        }
        return false;
    }
}
