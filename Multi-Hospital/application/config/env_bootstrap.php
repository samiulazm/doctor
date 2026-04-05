<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once __DIR__ . '/dotenv_loader.php';

if (!function_exists('ci_request_is_https')) {
	/**
	 * True when the request is HTTPS (including common reverse-proxy headers).
	 */
	function ci_request_is_https()
	{
		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
			return true;
		}
		if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
			return true;
		}
		if (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') {
			return true;
		}
		return false;
	}
}
