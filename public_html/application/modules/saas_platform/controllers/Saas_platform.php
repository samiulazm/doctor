<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Saas_platform extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->in_group('superadmin')) {
            redirect('home/permission');
        }
        $this->load->model('portal/chamber_platform_model');
    }

    public function index()
    {
        $data['subscriptions'] = $this->chamber_platform_model->getSubscriptionsOverview();
        $data['plans'] = $this->chamber_platform_model->getPlans();
        $data['referrals'] = $this->chamber_platform_model->getRecentReferrals(40);
        $this->load->view('home/dashboard', $data);
        $this->load->view('saas/dashboard', $data);
        $this->load->view('home/footer');
    }

    public function sms_credits()
    {
        $this->db->order_by('id', 'desc');
        $this->db->limit(300);
        $rows = $this->db->get('sms_credit_ledger')->result();
        $sms_by_doctor = $this->db->query(
            'SELECT t.doctor_id, d.name AS doctor_name, t.hospital_id, t.balance_after, t.created_at '
            . 'FROM sms_credit_ledger t '
            . 'INNER JOIN doctor d ON d.id = t.doctor_id '
            . 'INNER JOIN ( '
            . '  SELECT doctor_id, hospital_id, MAX(id) AS max_id FROM sms_credit_ledger '
            . '  WHERE doctor_id IS NOT NULL GROUP BY doctor_id, hospital_id '
            . ') x ON x.max_id = t.id '
            . 'ORDER BY t.hospital_id, d.name'
        )->result();
        $data['rows'] = $rows;
        $data['sms_by_doctor'] = $sms_by_doctor;
        $this->load->view('home/dashboard', $data);
        $this->load->view('saas/sms_credits', $data);
        $this->load->view('home/footer');
    }

    public function sms_credit_add()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $hid = (int) $this->input->post('hospital_id');
        $delta = (int) $this->input->post('delta');
        $this->chamber_platform_model->adjustSmsCredits($hid, $delta, 'superadmin_topup', null, 'manual', null);
        redirect('saas_platform/sms_credits');
    }

    public function analytics()
    {
        $this->db->order_by('id', 'desc');
        $this->db->limit(500);
        $rows = $this->db->get('usage_analytics_event')->result();
        $since = date('Y-m-d H:i:s', strtotime('-30 days'));
        $doctor_rank = $this->db->query(
            'SELECT e.doctor_id, d.name AS doctor_name, COUNT(*) AS event_count '
            . 'FROM usage_analytics_event e '
            . 'LEFT JOIN doctor d ON d.id = e.doctor_id '
            . 'WHERE e.doctor_id IS NOT NULL AND e.created_at >= ? '
            . 'GROUP BY e.doctor_id, d.name '
            . 'ORDER BY event_count DESC '
            . 'LIMIT 25',
            array($since)
        )->result();
        $data['rows'] = $rows;
        $data['doctor_rank'] = $doctor_rank;
        $data['rank_since'] = $since;
        $this->load->view('home/dashboard', $data);
        $this->load->view('saas/analytics', $data);
        $this->load->view('home/footer');
    }

    public function subscription_save()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $this->db->insert('doctor_subscription', array(
            'hospital_id' => (int) $this->input->post('hospital_id'),
            'doctor_id' => (int) $this->input->post('doctor_id'),
            'plan_id' => (int) $this->input->post('plan_id'),
            'status' => 'active',
            'starts_at' => $this->input->post('starts_at'),
            'ends_at' => $this->input->post('ends_at') ?: null,
            'last_paid_at' => date('Y-m-d'),
        ));
        redirect('saas_platform/index');
    }

    public function lab_referral_ready()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $id = (int) $this->input->post('referral_id');
        $this->chamber_platform_model->markReferralReady($id);
        redirect('saas_platform/index');
    }
}
