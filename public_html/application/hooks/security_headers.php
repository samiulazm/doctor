<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Baseline HTTP security headers (safe defaults for a hospital admin app).
 * Adjust CSP in a future phase if you add inline scripts from CDNs.
 */
function security_headers()
{
    $CI = &get_instance();
    if (!isset($CI->output)) {
        return;
    }

    $CI->output->set_header('X-Frame-Options: SAMEORIGIN');
    $CI->output->set_header('X-Content-Type-Options: nosniff');
    $CI->output->set_header('Referrer-Policy: strict-origin-when-cross-origin');
    $CI->output->set_header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $CI->output->set_header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}
