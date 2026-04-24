<?php
/**
 * Router script for PHP built-in development server.
 *
 * Usage:  php -S localhost:8000 server.php
 *
 * This script checks whether the request is for a static file
 * (CSS, JS, images, fonts, etc.). If the file exists on disk it
 * is served directly; otherwise the request is forwarded to
 * CodeIgniter's index.php.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// If the requested path maps to an existing file, let PHP serve it
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    $ext = pathinfo($uri, PATHINFO_EXTENSION);

    // Set correct Content-Type for common static assets
    $mimeTypes = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'json' => 'application/json',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
        'ttf'  => 'font/ttf',
        'eot'  => 'application/vnd.ms-fontobject',
        'map'  => 'application/json',
        'pdf'  => 'application/pdf',
    ];

    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
        readfile(__DIR__ . $uri);
        return true;
    }

    // For other file types, let PHP's built-in server handle it
    return false;
}

// Not a static file — hand off to CodeIgniter
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';
