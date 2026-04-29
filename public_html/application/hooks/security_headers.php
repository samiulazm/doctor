<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Baseline HTTP security headers (safe defaults for a hospital admin app).
 * CSP uses 'unsafe-inline' for Phase 1 compatibility with existing inline scripts.
 * Upgrade to nonce-based CSP after the views are audited.
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

    if (config_item('csrf_protection') && isset($CI->security)) {
        $CI->output->set_header('X-CSRF-Hash: ' . $CI->security->get_csrf_hash());
    }

    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $CI->output->set_header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }

    // SEC-08: Content Security Policy baseline for existing AdminLTE pages.
    $CI->output->set_header(
        "Content-Security-Policy: " .
        "default-src 'self'; " .
        "script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; " .
        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://code.ionicframework.com; " .
        "font-src 'self' https://fonts.gstatic.com https://code.ionicframework.com data:; " .
        "img-src 'self' data: blob:; " .
        "connect-src 'self'; " .
        "frame-ancestors 'self'; " .
        "object-src 'none'; " .
        "base-uri 'self';"
    );
}
