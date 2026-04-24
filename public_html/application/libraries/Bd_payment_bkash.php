<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * bKash Checkout (URL) — grant token, create payment (mode 0011), execute after callback.
 * Credentials: Finance → Payment gateways → bKash or BKASH_APP_KEY, BKASH_APP_SECRET, BKASH_USERNAME, BKASH_PASSWORD in .env.
 */
class Bd_payment_bkash
{
    public static function sandboxBase()
    {
        return 'https://checkout.sandbox.bka.sh/v1.2.0-beta';
    }

    public static function liveBase()
    {
        return 'https://checkout.pay.bka.sh/v1.2.0-beta';
    }

    public static function baseUrl()
    {
        $s = getenv('BKASH_SANDBOX');
        if ($s === '0' || $s === 'false') {
            return self::liveBase();
        }
        return self::sandboxBase();
    }

    public static function isConfigured()
    {
        return (bool) (getenv('BKASH_APP_KEY') && getenv('BKASH_APP_SECRET')
            && getenv('BKASH_USERNAME') && getenv('BKASH_PASSWORD'));
    }

    /**
     * @return array{ok:bool, id_token?:string, error?:string, raw?:string}
     */
    public static function grantToken($base, $username, $password, $app_key, $app_secret)
    {
        $url = rtrim($base, '/') . '/tokenized/checkout/token/grant';
        $body = json_encode(array(
            'app_key' => $app_key,
            'app_secret' => $app_secret,
        ));
        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
            'username: ' . $username,
            'password: ' . $password,
        );
        $res = self::httpPostJson($url, $headers, $body);
        $j = $res['json'];
        if (is_array($j) && !empty($j['id_token'])) {
            return array('ok' => true, 'id_token' => (string) $j['id_token'], 'raw' => $res['raw']);
        }
        $err = 'Grant token failed';
        if (is_array($j)) {
            if (!empty($j['errorMessage'])) {
                $err = (string) $j['errorMessage'];
            } elseif (!empty($j['statusMessage'])) {
                $err = (string) $j['statusMessage'];
            }
        }
        if ($res['curl_error'] !== '') {
            $err = $res['curl_error'];
        }
        return array('ok' => false, 'error' => $err, 'raw' => $res['raw']);
    }

    /**
     * Checkout URL flow (mode 0011): returns bkashURL for customer redirect.
     *
     * @return array{ok:bool, paymentID?:string, bkashURL?:string, error?:string, raw?:string, data?:array}
     */
    public static function createUrlCheckout($base, $id_token, $app_key, $payer_reference, $callback_url, $amount_str, $merchant_invoice_number)
    {
        $url = rtrim($base, '/') . '/tokenized/checkout/create';
        $body = json_encode(array(
            'mode' => '0011',
            'payerReference' => $payer_reference,
            'callbackURL' => $callback_url,
            'amount' => $amount_str,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $merchant_invoice_number,
        ));
        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: ' . $id_token,
            'X-App-Key: ' . $app_key,
        );
        $res = self::httpPostJson($url, $headers, $body);
        $j = $res['json'];
        if (!is_array($j)) {
            return array('ok' => false, 'error' => 'Invalid bKash response', 'raw' => $res['raw']);
        }
        $code = isset($j['statusCode']) ? (string) $j['statusCode'] : '';
        if ($code === '0000' && !empty($j['paymentID']) && !empty($j['bkashURL'])) {
            return array(
                'ok' => true,
                'paymentID' => (string) $j['paymentID'],
                'bkashURL' => (string) $j['bkashURL'],
                'data' => $j,
                'raw' => $res['raw'],
            );
        }
        $msg = isset($j['errorMessage']) ? (string) $j['errorMessage'] : (isset($j['statusMessage']) ? (string) $j['statusMessage'] : 'Create payment failed');
        if ($res['curl_error'] !== '') {
            $msg = $res['curl_error'];
        }
        return array('ok' => false, 'error' => $msg, 'data' => $j, 'raw' => $res['raw']);
    }

    /**
     * Query current payment status (bKash Query Payment API).
     * Use when Execute did not return (timeout/network) per bKash checkout flow notes.
     *
     * @return array{ok:bool, data?:array, error?:string, raw?:string}
     */
    public static function queryPaymentStatus($base, $id_token, $app_key, $payment_id)
    {
        $url = rtrim($base, '/') . '/tokenized/checkout/payment/status';
        $body = json_encode(array('paymentID' => $payment_id));
        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: ' . $id_token,
            'X-App-Key: ' . $app_key,
        );
        $res = self::httpPostJson($url, $headers, $body);
        $j = $res['json'];
        if (!is_array($j)) {
            return array('ok' => false, 'error' => 'Invalid bKash response', 'raw' => $res['raw']);
        }
        $code = isset($j['statusCode']) ? (string) $j['statusCode'] : '';
        $txn = isset($j['transactionStatus']) ? (string) $j['transactionStatus'] : '';
        if ($code === '0000' && $txn === 'Completed') {
            return array('ok' => true, 'data' => $j, 'raw' => $res['raw']);
        }
        $msg = isset($j['errorMessage']) ? (string) $j['errorMessage'] : (isset($j['statusMessage']) ? (string) $j['statusMessage'] : 'Payment not completed');
        if ($res['curl_error'] !== '') {
            $msg = $res['curl_error'];
        }
        return array('ok' => false, 'error' => $msg, 'data' => $j, 'raw' => $res['raw']);
    }

    /**
     * Finalize after customer returns from bKash (success callback).
     *
     * @return array{ok:bool, data?:array, error?:string, raw?:string}
     */
    public static function executePayment($base, $id_token, $app_key, $payment_id)
    {
        $url = rtrim($base, '/') . '/tokenized/checkout/execute';
        $body = json_encode(array('paymentID' => $payment_id));
        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: ' . $id_token,
            'X-App-Key: ' . $app_key,
        );
        $res = self::httpPostJson($url, $headers, $body);
        $j = $res['json'];
        if (!is_array($j)) {
            return array('ok' => false, 'error' => 'Invalid bKash response', 'raw' => $res['raw']);
        }
        $code = isset($j['statusCode']) ? (string) $j['statusCode'] : '';
        $txn = isset($j['transactionStatus']) ? (string) $j['transactionStatus'] : '';
        if ($code === '0000' && $txn === 'Completed') {
            return array('ok' => true, 'data' => $j, 'raw' => $res['raw']);
        }
        $msg = isset($j['errorMessage']) ? (string) $j['errorMessage'] : (isset($j['statusMessage']) ? (string) $j['statusMessage'] : 'Execute failed');
        if ($res['curl_error'] !== '') {
            $msg = $res['curl_error'];
        }
        return array('ok' => false, 'error' => $msg, 'data' => $j, 'raw' => $res['raw']);
    }

    /**
     * @return array{raw:string, json:mixed, curl_error:string}
     */
    protected static function httpPostJson($url, $headers, $body)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $out = curl_exec($ch);
        $cerr = curl_error($ch);
        curl_close($ch);
        if ($out === false) {
            return array('raw' => '', 'json' => null, 'curl_error' => $cerr ?: 'curl failed');
        }
        $j = json_decode($out, true);
        return array('raw' => $out, 'json' => $j, 'curl_error' => $cerr ?: '');
    }
}
