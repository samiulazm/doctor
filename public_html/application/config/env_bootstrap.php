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

if (!function_exists('ci_host_without_port')) {
	/**
	 * Hostname from Host / X-Forwarded-Host without trailing :port (best-effort).
	 */
	function ci_host_without_port($host)
	{
		$host = trim($host);
		if ($host === '') {
			return '';
		}
		if ($host[0] === '[') {
			$end = strpos($host, ']');
			if ($end !== false) {
				return substr($host, 0, $end + 1);
			}
		}
		return preg_replace('/:\d+$/', '', $host);
	}
}

if (!function_exists('ci_effective_request_host')) {
	/**
	 * Public hostname for URL building (reverse proxies often set X-Forwarded-Host).
	 */
	function ci_effective_request_host()
	{
		if (!empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
			$first = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_HOST'])[0]);
			if ($first !== '') {
				return ci_host_without_port($first);
			}
		}
		if (!empty($_SERVER['HTTP_HOST'])) {
			return ci_host_without_port($_SERVER['HTTP_HOST']);
		}
		return 'localhost';
	}
}

if (!function_exists('ci_base_url_from_request')) {
	/**
	 * Base URL with trailing slash from the current request (scheme, host, script directory).
	 */
	function ci_base_url_from_request()
	{
		$ht = ci_request_is_https() ? 'https://' : 'http://';
		$host = ci_effective_request_host();
		$script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/index.php';
		$dir = str_replace('\\', '/', dirname($script));
		$path = preg_replace('@/+$@', '', $dir);
		return $ht . $host . $path . '/';
	}
}

if (!function_exists('ci_stale_ci_base_url_should_use_request')) {
	/**
	 * True when CI_BASE_URL still points at an old IP or ~/user URL but this hit uses a real host/path.
	 */
	function ci_stale_ci_base_url_should_use_request($configured)
	{
		if ($configured === false || $configured === '') {
			return false;
		}
		$req = ci_effective_request_host();
		if ($req === '' || $req === 'localhost') {
			return false;
		}
		$parsed = @parse_url($configured);
		if (!is_array($parsed) || empty($parsed['scheme']) || empty($parsed['host'])) {
			return false;
		}
		$chost = $parsed['host'];
		$cpath = isset($parsed['path']) ? $parsed['path'] : '';

		if (filter_var($chost, FILTER_VALIDATE_IP) && !filter_var($req, FILTER_VALIDATE_IP)) {
			return true;
		}

		if (strpos($cpath, '/~') !== false) {
			$sn = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
			if ($sn !== '' && strpos(str_replace('\\', '/', $sn), '/~') === false) {
				return true;
			}
		}

		return false;
	}
}
