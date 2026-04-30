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
        // Mitigate intermittent browser reset issues on some networks by disabling HTTP/3 advertisement.
        $CI->output->set_header('Alt-Svc: clear');
    }

    // SEC-08: Compatibility CSP for legacy/public pages that still rely on external CDNs.
    // Keep protections like frame-ancestors/object-src/base-uri while allowing current assets.
    $CI->output->set_header(
        "Content-Security-Policy: " .
        "default-src 'self' https: data: blob:; " .
        "script-src 'self' 'unsafe-inline' 'unsafe-eval' https: data: blob:; " .
        "style-src 'self' 'unsafe-inline' https:; " .
        "font-src 'self' https: data:; " .
        "img-src 'self' https: data: blob:; " .
        "connect-src 'self' https: wss:; " .
        "frame-src 'self' https:; " .
        "form-action 'self' https:; " .
        "frame-ancestors 'self'; " .
        "object-src 'none'; " .
        "base-uri 'self';"
    );
}
