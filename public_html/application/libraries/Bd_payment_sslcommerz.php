<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SSLCommerz Easy Checkout session (sandbox-friendly stub).
 * Set SSLCOMMERZ_STORE_ID, SSLCOMMERZ_STORE_PASSWORD, SSLCOMMERZ_SANDBOX in .env
 */
class Bd_payment_sslcommerz
{
    public static function apiBase()
    {
        $sandbox = getenv('SSLCOMMERZ_SANDBOX');
        if ($sandbox === '0' || $sandbox === 'false') {
            return 'https://securepay.sslcommerz.com';
        }
        return 'https://sandbox.sslcommerz.com';
    }

    /**
     * Resolve store credentials: prefer values already in $postFields (e.g. from paymentGateway), else .env.
     *
     * @param array|string $postFields
     * @return array{0:string,1:string} store_id, store_passwd
     */
    public static function resolveCredentials($postFields)
    {
        $store = '';
        $pass = '';
        if (is_array($postFields)) {
            if (!empty($postFields['store_id'])) {
                $store = (string) $postFields['store_id'];
            }
            if (!empty($postFields['store_passwd'])) {
                $pass = (string) $postFields['store_passwd'];
            }
        }
        if ($store === '') {
            $e = getenv('SSLCOMMERZ_STORE_ID');
            $store = ($e !== false && $e !== '') ? (string) $e : '';
        }
        if ($pass === '') {
            $e = getenv('SSLCOMMERZ_STORE_PASSWORD');
            $pass = ($e !== false && $e !== '') ? (string) $e : '';
        }
        return array($store, $pass);
    }

    /**
     * Server-side validation after redirect (uses val_id).
     *
     * @return array{ok:bool, tran_id?:string, amount?:string, error?:string, payload?:mixed}
     */
    public static function validateTransaction($val_id, $store_id, $store_passwd)
    {
        $val_id = trim((string) $val_id);
        if ($val_id === '' || $store_id === '' || $store_passwd === '') {
            return array('ok' => false, 'error' => 'missing val_id or store credentials');
        }
        $query = http_build_query(array(
            'val_id' => $val_id,
            'store_id' => $store_id,
            'store_passwd' => $store_passwd,
            'v' => 1,
            'format' => 'json',
        ));
        $url = self::apiBase() . '/validator/api/validationserverAPI.php?' . $query;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $out = curl_exec($ch);
        $cerr = curl_error($ch);
        curl_close($ch);
        if ($out === false) {
            return array('ok' => false, 'error' => $cerr ?: 'curl failed');
        }
        $j = json_decode($out, true);
        if (!is_array($j)) {
            return array('ok' => false, 'error' => 'invalid response', 'payload' => $out);
        }
        $st = isset($j['status']) ? (string) $j['status'] : '';
        if ($st !== 'VALID' && $st !== 'VALIDATED') {
            return array('ok' => false, 'error' => $st !== '' ? $st : 'not valid', 'payload' => $j);
        }
        return array(
            'ok' => true,
            'tran_id' => isset($j['tran_id']) ? (string) $j['tran_id'] : '',
            'amount' => isset($j['amount']) ? (string) $j['amount'] : '',
            'payload' => $j,
        );
    }

    /**
     * @return array{ok:bool, redirect_url?:string, sessionkey?:string, error?:string}
     */
    public static function initiateSession($postFields)
    {
        list($store, $pass) = self::resolveCredentials($postFields);
        if ($store === '' || $pass === '') {
            return array('ok' => false, 'error' => 'SSLCommerz credentials not configured');
        }
        if (is_array($postFields)) {
            $postFields['store_id'] = $store;
            $postFields['store_passwd'] = $pass;
        }
        $url = self::apiBase() . '/gwprocess/v4/api.php';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($postFields) ? http_build_query($postFields) : $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $out = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($out === false) {
            return array('ok' => false, 'error' => $err ?: 'curl failed');
        }
        $j = json_decode($out, true);
        if (!empty($j['status']) && $j['status'] === 'SUCCESS' && !empty($j['GatewayPageURL'])) {
            return array('ok' => true, 'redirect_url' => $j['GatewayPageURL'], 'sessionkey' => isset($j['sessionkey']) ? $j['sessionkey'] : null);
        }
        return array('ok' => false, 'error' => isset($j['failedreason']) ? $j['failedreason'] : $out);
    }
}
