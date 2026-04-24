<?php

/**
 * Router for PHP's built-in web server (mod_rewrite emulation).
 * Run from Multi-Hospital: php -S localhost:8080 router.php
 */
$_SERVER['CI_ENV'] = 'development';
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
if ($uri !== '/' && is_file(__DIR__ . $uri)) {
	return false;
}
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';
