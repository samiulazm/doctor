<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Booking OTP: generate numeric code, hash for storage, constant-time verify.
 */
class Chamber_otp
{
    /** @var CI_Controller */
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public function ttlSeconds()
    {
        $t = getenv('CHAMBER_OTP_TTL');
        $t = ($t !== false && $t !== '') ? (int) $t : 300;
        return max(60, min(900, $t));
    }

    public function generatePlain()
    {
        return (string) random_int(100000, 999999);
    }

    public function hash($plain)
    {
        return hash('sha256', $plain . $this->CI->config->item('encryption_key'));
    }

    public function verify($plain, $hash)
    {
        return hash_equals($hash, $this->hash($plain));
    }
}
