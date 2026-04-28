<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Bangladesh gateways: SSLCommerz Easy Checkout + bKash Checkout (URL).
 */
class Payment_bd extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!function_exists('chamber_practice_require_enabled')) {
            $this->load->helper('chamber_practice');
        }
        $this->load->model('portal/queue_model');
        $this->load->model('portal/chamber_platform_model');
    }

    /** @return array{0:string,1:string} store_id, store_password */
    protected function sslcommerz_credentials_for_hospital($hospital_id)
    {
        $gw = $this->db->get_where('paymentGateway', array('hospital_id' => (int) $hospital_id, 'name' => 'SSLCommerz'))->row();
        $store_id = ($gw && !empty($gw->merchant_key)) ? $gw->merchant_key : (getenv('SSLCOMMERZ_STORE_ID') ?: '');
        $store_pass = ($gw && !empty($gw->salt)) ? $gw->salt : (getenv('SSLCOMMERZ_STORE_PASSWORD') ?: '');
        return array($store_id, $store_pass);
    }

    /** @return array{0:string,1:string,2:string,3:string} app_key, app_secret, username, password */
    protected function bkash_credentials_for_hospital($hospital_id)
    {
        $gw = $this->db->get_where('paymentGateway', array('hospital_id' => (int) $hospital_id, 'name' => 'bKash'))->row();
        $app_key = ($gw && !empty($gw->public_key)) ? (string) $gw->public_key : (getenv('BKASH_APP_KEY') ?: '');
        $app_secret = ($gw && !empty($gw->secret)) ? (string) $gw->secret : (getenv('BKASH_APP_SECRET') ?: '');
        $user = ($gw && !empty($gw->APIUsername)) ? (string) $gw->APIUsername : (getenv('BKASH_USERNAME') ?: '');
        $pass = ($gw && !empty($gw->APIPassword)) ? (string) $gw->APIPassword : (getenv('BKASH_PASSWORD') ?: '');
        return array($app_key, $app_secret, $user, $pass);
    }

    /**
     * Normalize Execute vs Query Payment response fields (bKash uses different keys in places).
     *
     * @param array $d
     * @return array{amount:float,trx_id:string,merchant_invoice:string}
     */
    protected function bkash_normalize_gateway_data($d)
    {
        $d = is_array($d) ? $d : array();
        $inv = '';
        if (!empty($d['merchantInvoiceNumber'])) {
            $inv = (string) $d['merchantInvoiceNumber'];
        } elseif (!empty($d['merchantInvoice'])) {
            $inv = (string) $d['merchantInvoice'];
        }
        return array(
            'amount' => isset($d['amount']) ? (float) $d['amount'] : 0.0,
            'trx_id' => !empty($d['trxID']) ? (string) $d['trxID'] : '',
            'merchant_invoice' => $inv,
        );
    }

    /**
     * Persist success after bKash Execute or Query confirms Completed (amount + invoice checks already passed).
     *
     * @param object $intent
     * @param array  $d Raw gateway row (execute or query)
     */
    protected function bkash_apply_success_from_gateway($intent, $d)
    {
        $intent_id = (int) $intent->id;
        $norm = $this->bkash_normalize_gateway_data($d);
        $meta = json_decode($intent->meta_json ?: '{}', true);
        $new_meta = is_array($meta) ? $meta : array();
        if ($norm['trx_id'] !== '') {
            $new_meta['bkash_trx_id'] = $norm['trx_id'];
        }
        if ($norm['merchant_invoice'] !== '') {
            $new_meta['bkash_invoice'] = $norm['merchant_invoice'];
        }
        $this->chamber_platform_model->updateBdIntent($intent_id, array(
            'status' => 'success',
            'meta_json' => json_encode($new_meta),
        ));
        $this->queue_model->updateRow($intent->queue_id, array(
            'advance_payment_status' => 'paid',
        ));
        $queue = $this->queue_model->getRowById((int) $intent->queue_id);
        if ($queue && !empty($queue->appointment_id)) {
            $aid = (int) $queue->appointment_id;
            $hid = (int) $intent->hospital_id;
            $this->db->where('id', $aid);
            $this->db->where('hospital_id', $hid);
            $this->db->update('appointment', array('payment_status' => 'paid'));
            $this->db->where('appointment_id', $aid);
            $this->db->where('hospital_id', $hid);
            $this->db->where('status', 'unpaid');
            $this->db->update('payment', array('status' => 'paid'));
        }
    }

    /**
     * Verify amount and merchant invoice from gateway row against intent.
     *
     * @param object $intent
     * @param array  $d
     * @return bool
     */
    protected function bkash_gateway_data_matches_intent($intent, $d)
    {
        $norm = $this->bkash_normalize_gateway_data($d);
        if (abs($norm['amount'] - (float) $intent->amount) >= 0.02) {
            log_message('error', 'bKash amount mismatch intent ' . (int) $intent->id . ' expected ' . $intent->amount . ' got ' . $norm['amount']);
            return false;
        }
        $meta = json_decode($intent->meta_json ?: '{}', true);
        $expected_inv = is_array($meta) && !empty($meta['merchant_invoice']) ? (string) $meta['merchant_invoice'] : '';
        if ($expected_inv !== '' && $norm['merchant_invoice'] !== '' && $norm['merchant_invoice'] !== $expected_inv) {
            log_message('error', 'bKash merchant invoice mismatch for intent ' . (int) $intent->id);
            return false;
        }
        return true;
    }

    /**
     * After bKash success callback: grant token, Execute Payment (authoritative), optional Query fallback, mark paid (idempotent).
     *
     * Security: browser redirect params (status, signature) are not proof of payment. Fulfillment uses only
     * server-side Execute Payment and/or Query Payment with your credentials; amount and invoice must match the intent.
     *
     * @param object $intent bd_payment_intent row
     * @return bool True if intent is success after this call (or was already success).
     */
    protected function bkash_try_finalize($intent, $payment_id)
    {
        if (!$intent || $intent->gateway !== 'bkash') {
            return false;
        }
        $intent_id = (int) $intent->id;
        if ($intent->status === 'success') {
            return true;
        }
        $payment_id = trim((string) $payment_id);
        if ($payment_id === '' || $intent->gateway_session_id !== $payment_id) {
            log_message('error', 'bKash paymentID mismatch for intent ' . $intent_id);
            return false;
        }
        list($app_key, $app_secret, $user, $pass) = $this->bkash_credentials_for_hospital((int) $intent->hospital_id);
        if ($app_key === '' || $app_secret === '' || $user === '' || $pass === '') {
            log_message('error', 'bKash credentials missing for intent ' . $intent_id);
            return false;
        }
        $this->load->library('Bd_payment_bkash');
        $base = Bd_payment_bkash::baseUrl();
        $grant = Bd_payment_bkash::grantToken($base, $user, $pass, $app_key, $app_secret);
        if (empty($grant['ok']) || empty($grant['id_token'])) {
            log_message('error', 'bKash grant failed intent ' . $intent_id . ': ' . (isset($grant['error']) ? $grant['error'] : ''));
            return false;
        }
        $token = $grant['id_token'];
        $exec = Bd_payment_bkash::executePayment($base, $token, $app_key, $payment_id);
        $d = null;
        if (!empty($exec['ok']) && !empty($exec['data'])) {
            $d = $exec['data'];
        } else {
            log_message('error', 'bKash execute failed intent ' . $intent_id . ': ' . (isset($exec['error']) ? $exec['error'] : '') . ' — trying Query Payment');
            $query = Bd_payment_bkash::queryPaymentStatus($base, $token, $app_key, $payment_id);
            if (!empty($query['ok']) && !empty($query['data'])) {
                $d = $query['data'];
            } else {
                log_message('error', 'bKash query payment failed intent ' . $intent_id . ': ' . (isset($query['error']) ? $query['error'] : ''));
                return false;
            }
        }
        if (!$this->bkash_gateway_data_matches_intent($intent, $d)) {
            return false;
        }
        $this->bkash_apply_success_from_gateway($intent, $d);
        return true;
    }

    /**
     * Validate with SSLCommerz and mark intent + queue paid (idempotent).
     *
     * @param object $intent bd_payment_intent row
     * @param bool   $for_ipn If true, never accept payment on status alone (IPN must use val_id validation).
     * @return bool True if intent is success after this call (or was already success).
     */
    protected function sslcommerz_try_finalize($intent, $val_id, $posted_status, $for_ipn = false)
    {
        if (!$intent) {
            return false;
        }
        $intent_id = (int) $intent->id;
        if ($intent->status === 'success') {
            return true;
        }
        $meta = json_decode($intent->meta_json ?: '{}', true);
        $expected_tran = is_array($meta) && !empty($meta['tran_id']) ? (string) $meta['tran_id'] : '';
        list($store_id, $store_pass) = $this->sslcommerz_credentials_for_hospital((int) $intent->hospital_id);
        $this->load->library('Bd_payment_sslcommerz');
        $verified = false;
        if (!empty($val_id) && $store_id !== '' && $store_pass !== '') {
            $v = Bd_payment_sslcommerz::validateTransaction($val_id, $store_id, $store_pass);
            if (!empty($v['ok'])) {
                $tran_ok = true;
                if ($expected_tran !== '') {
                    $tran_ok = !empty($v['tran_id']) && ($v['tran_id'] === $expected_tran);
                    if (!$tran_ok) {
                        log_message('error', 'SSLCommerz tran_id mismatch for intent ' . $intent_id);
                    }
                }
                if ($tran_ok) {
                    $paid_amt = isset($v['amount']) ? (float) $v['amount'] : 0.0;
                    if (abs($paid_amt - (float) $intent->amount) < 0.02) {
                        $verified = true;
                    } else {
                        log_message('error', 'SSLCommerz amount mismatch intent ' . $intent_id . ' expected ' . $intent->amount . ' got ' . $paid_amt);
                    }
                }
            }
        }
        if (!$verified && !$for_ipn && ($posted_status === 'VALID' || $posted_status === 'VALIDATED') && defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            log_message('debug', 'SSLCommerz accepted on status only (development), intent ' . $intent_id);
            $verified = true;
        }
        if ($verified) {
            $new_meta = is_array($meta) ? $meta : array();
            if ($val_id !== null && $val_id !== '') {
                $new_meta['val_id'] = (string) $val_id;
            }
            $this->chamber_platform_model->updateBdIntent($intent_id, array(
                'status' => 'success',
                'meta_json' => json_encode($new_meta),
            ));
            $this->queue_model->updateRow($intent->queue_id, array(
                'advance_payment_status' => 'paid',
            ));
            $queue = $this->queue_model->getRowById((int) $intent->queue_id);
            if ($queue && !empty($queue->appointment_id)) {
                $aid = (int) $queue->appointment_id;
                $hid = (int) $intent->hospital_id;
                $this->db->where('id', $aid);
                $this->db->where('hospital_id', $hid);
                $this->db->update('appointment', array('payment_status' => 'paid'));
                $this->db->where('appointment_id', $aid);
                $this->db->where('hospital_id', $hid);
                $this->db->where('status', 'unpaid');
                $this->db->update('payment', array('status' => 'paid'));
            }
            return true;
        }
        return false;
    }

    /** HTML snippet: link back to public doctor booking page when queue + slug exist. */
    protected function portal_booking_back_link_html($queue_id)
    {
        $queue = $this->queue_model->getRowById((int) $queue_id);
        if (!$queue) {
            return '';
        }
        $prof = $this->db->get_where('doctor_portal_profile', array(
            'doctor_id' => $queue->doctor_id,
            'hospital_id' => $queue->hospital_id,
        ))->row();
        if (!$prof || empty($prof->public_slug)) {
            return '';
        }
        $url = site_url('portal/d/' . rawurlencode($prof->public_slug));
        return '<p class="mt-3"><a class="btn btn-primary" href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">Back to doctor booking page</a></p>';
    }

    public function init_sslcommerz()
    {
        $queue_id = (int) $this->input->get('queue_id');
        $row = $this->queue_model->getRowById($queue_id);
        if (!$row || (float) $row->advance_fee_amount <= 0) {
            show_error('Invalid queue or zero amount', 400);
        }
        $hid = (int) $row->hospital_id;
        chamber_practice_require_enabled($this, $hid, true);
        $amount = (float) $row->advance_fee_amount;
        list($store_id, $store_pass) = $this->sslcommerz_credentials_for_hospital($hid);
        if ($store_id === '' || $store_pass === '') {
            show_error('SSLCommerz is not configured for this practice. Configure SSLCommerz credentials or set SSLCOMMERZ_* in .env.', 501);
        }
        $intent_id = $this->chamber_platform_model->insertBdIntent(array(
            'hospital_id' => $hid,
            'queue_id' => $queue_id,
            'gateway' => 'sslcommerz',
            'amount' => $amount,
            'currency' => 'BDT',
            'status' => 'created',
        ));
        $post = array(
            'store_id' => $store_id,
            'store_passwd' => $store_pass,
            'total_amount' => $amount,
            'currency' => 'BDT',
            'tran_id' => 'CHAMBER_' . $intent_id . '_' . time(),
            'success_url' => site_url('payment_bd/sslcommerz_return?intent=' . $intent_id),
            'fail_url' => site_url('payment_bd/sslcommerz_fail?intent=' . $intent_id),
            'cancel_url' => site_url('payment_bd/sslcommerz_fail?intent=' . $intent_id),
            'ipn_url' => site_url('payment_bd/sslcommerz_ipn'),
            'value_a' => (string) $intent_id,
            'cus_name' => $row->guest_name ?: 'Patient',
            'cus_email' => 'patient@local',
            'cus_add1' => 'Dhaka',
            'cus_phone' => $row->guest_phone ?: '',
            'product_name' => 'Chamber advance booking',
            'product_category' => 'Healthcare',
            'product_profile' => 'general',
        );
        $this->load->library('Bd_payment_sslcommerz');
        $res = Bd_payment_sslcommerz::initiateSession($post);
        if (empty($res['ok'])) {
            show_error(isset($res['error']) ? $res['error'] : 'SSLCommerz init failed', 500);
        }
        $this->chamber_platform_model->updateBdIntent($intent_id, array(
            'gateway_session_id' => isset($res['sessionkey']) ? $res['sessionkey'] : null,
            'meta_json' => json_encode(array('tran_id' => $post['tran_id'])),
        ));
        redirect($res['redirect_url']);
    }

    public function sslcommerz_return()
    {
        $intent_id = (int) $this->input->get('intent');
        $intent = $this->chamber_platform_model->getBdIntent($intent_id);
        if (!$intent) {
            show_error('Invalid intent', 400);
        }
        chamber_practice_require_enabled($this, (int) $intent->hospital_id, true);
        if ($intent->status === 'success') {
            $back = $this->portal_booking_back_link_html($intent->queue_id);
            $this->load->view('portal/layout_public', array(
                'content' => '<div class="container py-5"><h3>Payment successful</h3>' . $back . '<p class="mt-2"><a href="' . htmlspecialchars(site_url('auth/login'), ENT_QUOTES, 'UTF-8') . '">Staff login</a></p></div>',
            ));
            return;
        }
        $val_id = $this->input->post('val_id');
        $posted_status = $this->input->post('status');
        if ($this->sslcommerz_try_finalize($intent, $val_id, $posted_status, false)) {
            $back = $this->portal_booking_back_link_html($intent->queue_id);
            $this->load->view('portal/layout_public', array(
                'content' => '<div class="container py-5"><h3>Payment successful</h3>' . $back . '<p class="mt-2"><a href="' . htmlspecialchars(site_url('auth/login'), ENT_QUOTES, 'UTF-8') . '">Staff login</a></p></div>',
            ));
            return;
        }
        redirect('payment_bd/sslcommerz_fail?intent=' . $intent_id);
    }

    /**
     * SSLCommerz Instant Payment Notification (server-to-server).
     * Configure by passing ipn_url on session init; correlates via value_a (intent id) or tran_id prefix CHAMBER_{id}_.
     */
    public function sslcommerz_ipn()
    {
        $intent_id = (int) $this->input->post('value_a');
        $tran_post = $this->input->post('tran_id');
        if ($intent_id < 1 && $tran_post) {
            if (preg_match('/^CHAMBER_(\d+)_/', (string) $tran_post, $m)) {
                $intent_id = (int) $m[1];
            }
        }
        $intent = $intent_id ? $this->chamber_platform_model->getBdIntent($intent_id) : null;
        if (!$intent || $intent->gateway !== 'sslcommerz') {
            $this->output->set_status_header(400);
            $this->output->set_output('INVALID');
            return;
        }
        if (!chamber_practice_enabled_for_hospital($this, (int) $intent->hospital_id)) {
            $this->output->set_status_header(403);
            $this->output->set_output('DISABLED');
            return;
        }
        $val_id = $this->input->post('val_id');
        $posted_status = $this->input->post('status');
        $ok = $this->sslcommerz_try_finalize($intent, $val_id, $posted_status, true);
        if (!$ok) {
            log_message('debug', 'SSLCommerz IPN: finalize not completed for intent ' . $intent_id);
        }
        $this->output->set_status_header(200);
        $this->output->set_output('OK');
    }

    public function sslcommerz_fail()
    {
        $intent_id = (int) $this->input->get('intent');
        $back = '';
        if ($intent_id) {
            $intent = $this->chamber_platform_model->getBdIntent($intent_id);
            if ($intent) {
                chamber_practice_require_enabled($this, (int) $intent->hospital_id, true);
                $back = $this->portal_booking_back_link_html($intent->queue_id);
            }
        }
        $this->load->view('portal/layout_public', array(
            'content' => '<div class="container py-5"><h3>Payment cancelled or failed</h3>' . $back . '</div>',
        ));
    }

    public function init_bkash()
    {
        $this->load->library('Bd_payment_bkash');
        $queue_id = (int) $this->input->get('queue_id');
        $row = $queue_id ? $this->queue_model->getRowById($queue_id) : null;
        if (!$row || (float) $row->advance_fee_amount <= 0) {
            show_error('Invalid queue or zero amount', 400);
        }
        $hid = (int) $row->hospital_id;
        chamber_practice_require_enabled($this, $hid, true);
        list($app_key, $app_secret, $user, $pass) = $this->bkash_credentials_for_hospital($hid);
        if ($app_key === '' || $app_secret === '' || $user === '' || $pass === '') {
            show_error('bKash is not configured for this practice. Configure bKash credentials or set BKASH_* in .env.', 501);
        }
        $amount = (float) $row->advance_fee_amount;
        $amount_str = number_format($amount, 2, '.', '');
        $intent_id = $this->chamber_platform_model->insertBdIntent(array(
            'hospital_id' => $hid,
            'queue_id' => $queue_id,
            'gateway' => 'bkash',
            'amount' => $amount,
            'currency' => 'BDT',
            'status' => 'created',
        ));
        $merchant_invoice = 'CHAMBER' . $intent_id . 'T' . time();
        $digits = preg_replace('/\D/', '', (string) $row->guest_phone);
        $payer_ref = $digits !== '' ? substr($digits, 0, 11) : ('Q' . $queue_id);
        $callback_base = rtrim(site_url('payment_bd/bkash_callback'), '/');
        $base = Bd_payment_bkash::baseUrl();
        $grant = Bd_payment_bkash::grantToken($base, $user, $pass, $app_key, $app_secret);
        if (empty($grant['ok']) || empty($grant['id_token'])) {
            $this->chamber_platform_model->updateBdIntent($intent_id, array(
                'status' => 'failed',
                'meta_json' => json_encode(array('error' => isset($grant['error']) ? $grant['error'] : 'grant')),
            ));
            show_error(isset($grant['error']) ? $grant['error'] : 'bKash token request failed', 502);
        }
        $create = Bd_payment_bkash::createUrlCheckout(
            $base,
            $grant['id_token'],
            $app_key,
            $payer_ref,
            $callback_base,
            $amount_str,
            $merchant_invoice
        );
        if (empty($create['ok']) || empty($create['bkashURL'])) {
            $this->chamber_platform_model->updateBdIntent($intent_id, array(
                'status' => 'failed',
                'meta_json' => json_encode(array(
                    'error' => isset($create['error']) ? $create['error'] : 'create',
                    'merchant_invoice' => $merchant_invoice,
                )),
            ));
            show_error(isset($create['error']) ? $create['error'] : 'bKash create payment failed', 502);
        }
        $this->chamber_platform_model->updateBdIntent($intent_id, array(
            'gateway_session_id' => $create['paymentID'],
            'meta_json' => json_encode(array('merchant_invoice' => $merchant_invoice)),
        ));
        redirect($create['bkashURL']);
    }

    /**
     * bKash Checkout (URL) return: query params typically include paymentID, status (success|failure|cancel),
     * and sometimes signature (see bKash Create Payment response notes).
     *
     * Do not treat status=success as proof of payment. Authorization is server-side only: Execute Payment, with
     * Query Payment as fallback when Execute does not return (per bKash checkout flow). Redirect signature
     * verification is not implemented here because the public API docs do not publish a stable HMAC recipe;
     * rely on authenticated API responses + amount/invoice checks instead.
     */
    public function bkash_callback()
    {
        $this->load->library('Bd_payment_bkash');
        $payment_id = $this->input->get_post('paymentID');
        $status = strtolower(trim((string) $this->input->get_post('status')));
        $intent = $this->chamber_platform_model->getBdIntentByBkashPaymentId($payment_id);
        if (!$intent) {
            show_error('Unknown or expired payment session', 400);
        }
        chamber_practice_require_enabled($this, (int) $intent->hospital_id, true);
        if ($intent->status === 'success') {
            $back = $this->portal_booking_back_link_html($intent->queue_id);
            $this->load->view('portal/layout_public', array(
                'content' => '<div class="container py-5"><h3>Payment successful</h3>' . $back . '<p class="mt-2"><a href="' . htmlspecialchars(site_url('auth/login'), ENT_QUOTES, 'UTF-8') . '">Staff login</a></p></div>',
            ));
            return;
        }
        if ($status !== 'success') {
            $this->chamber_platform_model->updateBdIntent((int) $intent->id, array('status' => 'failed'));
            $back = $this->portal_booking_back_link_html($intent->queue_id);
            $this->load->view('portal/layout_public', array(
                'content' => '<div class="container py-5"><h3>Payment cancelled or failed</h3>' . $back . '</div>',
            ));
            return;
        }
        if ($this->bkash_try_finalize($intent, $payment_id)) {
            $intent = $this->chamber_platform_model->getBdIntent((int) $intent->id);
            $back = $this->portal_booking_back_link_html($intent->queue_id);
            $this->load->view('portal/layout_public', array(
                'content' => '<div class="container py-5"><h3>Payment successful</h3>' . $back . '<p class="mt-2"><a href="' . htmlspecialchars(site_url('auth/login'), ENT_QUOTES, 'UTF-8') . '">Staff login</a></p></div>',
            ));
            return;
        }
        $back = $this->portal_booking_back_link_html($intent->queue_id);
        $this->load->view('portal/layout_public', array(
            'content' => '<div class="container py-5"><h3>Payment could not be confirmed</h3><p>Please contact the chamber if money was debited.</p>' . $back . '</div>',
        ));
    }

    public function bkash_initiate_desk()
    {
        if ($this->input->method() !== 'post') {
            $this->output->set_status_header(405)->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'Method not allowed', 'csrf_hash' => $this->security->get_csrf_hash())));
            return;
        }
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group(array('Receptionist', 'Nurse', 'admin'))) {
            $this->output->set_status_header(403)->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'Unauthorized', 'csrf_hash' => $this->security->get_csrf_hash())));
            return;
        }

        $queue_id = (int) $this->input->post('queue_id');
        $amount = (float) $this->input->post('amount');
        $hid = $this->session->userdata('hospital_id');
        if ($queue_id < 1 || $amount <= 0) {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'queue_id and amount required', 'csrf_hash' => $this->security->get_csrf_hash())));
            return;
        }

        $row = $this->queue_model->getRowById($queue_id);
        if (!$row || (int) $row->hospital_id !== (int) $hid) {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'Queue row not found', 'csrf_hash' => $this->security->get_csrf_hash())));
            return;
        }

        $this->load->library('Bd_payment_bkash');
        list($app_key, $app_secret, $user, $pass) = $this->bkash_credentials_for_hospital($hid);
        if ($app_key === '' || $app_secret === '' || $user === '' || $pass === '') {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'bKash not configured for this practice', 'csrf_hash' => $this->security->get_csrf_hash())));
            return;
        }

        $amount_str = number_format($amount, 2, '.', '');
        $intent_id = $this->chamber_platform_model->insertBdIntent(array(
            'hospital_id' => $hid,
            'queue_id' => $queue_id,
            'gateway' => 'bkash',
            'amount' => $amount,
            'currency' => 'BDT',
            'status' => 'created',
        ));
        $merchant_invoice = 'DESK' . $intent_id . 'T' . time();
        $payer_ref = preg_replace('/\D/', '', (string) $row->guest_phone);
        $payer_ref = $payer_ref !== '' ? substr($payer_ref, 0, 11) : ('Q' . $queue_id);
        $base = Bd_payment_bkash::baseUrl();
        $callback_base = rtrim(site_url('payment_bd/bkash_callback'), '/');
        $grant = Bd_payment_bkash::grantToken($base, $user, $pass, $app_key, $app_secret);
        if (empty($grant['ok']) || empty($grant['id_token'])) {
            $msg = isset($grant['error']) ? $grant['error'] : 'bKash token grant failed';
            $this->chamber_platform_model->updateBdIntent($intent_id, array('status' => 'failed'));
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => $msg, 'csrf_hash' => $this->security->get_csrf_hash())));
            return;
        }

        $create = Bd_payment_bkash::createUrlCheckout(
            $base,
            $grant['id_token'],
            $app_key,
            $payer_ref,
            $callback_base,
            $amount_str,
            $merchant_invoice
        );
        if (empty($create['ok']) || empty($create['paymentID'])) {
            $msg = isset($create['error']) ? $create['error'] : 'bKash payment create failed';
            $this->chamber_platform_model->updateBdIntent($intent_id, array(
                'status' => 'failed',
                'meta_json' => json_encode(array('error' => $msg, 'merchant_invoice' => $merchant_invoice)),
            ));
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => $msg, 'csrf_hash' => $this->security->get_csrf_hash())));
            return;
        }

        $this->chamber_platform_model->updateBdIntent($intent_id, array(
            'gateway_session_id' => $create['paymentID'],
            'meta_json' => json_encode(array('merchant_invoice' => $merchant_invoice)),
        ));
        $this->output->set_content_type('application/json')->set_output(json_encode(array(
            'success' => true,
            'payment_id' => $create['paymentID'],
            'message' => 'bKash payment initiated.',
            'csrf_hash' => $this->security->get_csrf_hash(),
        )));
    }
}
