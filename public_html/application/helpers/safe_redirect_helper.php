<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('safe_redirect_target')) {
	/**
	 * Sanitize a redirect URL (e.g. HTTP_REFERER) to same-origin only.
	 * Blocks open redirects, javascript: URLs, and protocol-relative //evil hosts.
	 *
	 * @param string|null $url      Proposed Location (usually HTTP_REFERER)
	 * @param string      $fallback CI URI segment(s) or full URL used when $url is unsafe/empty
	 * @return string Full URL safe for redirect()
	 */
	function safe_redirect_target($url, $fallback = '')
	{
		$CI = &get_instance();
		$base = $CI->config->base_url();
		$base_host = parse_url($base, PHP_URL_HOST);

		if ($fallback === '' || $fallback === null) {
			$fallback = $base;
		} elseif (strpos($fallback, '://') === false && strpos($fallback, '//') !== 0) {
			$fallback = $CI->config->site_url($fallback);
		}

		if ($url === null || $url === '') {
			return $fallback;
		}

		$url = trim((string) $url);
		if ($url === '') {
			return $fallback;
		}

		if (preg_match('#^\s*(javascript|data|vbscript):#i', $url)) {
			return $fallback;
		}

		if (strpos($url, '//') === 0) {
			return $fallback;
		}

		if (!preg_match('#^https?://#i', $url)) {
			if (isset($url[0]) && $url[0] === '/' && strpos($url, '//') !== 0) {
				return $CI->config->site_url(ltrim($url, '/'));
			}
			return $fallback;
		}

		$parts = parse_url($url);
		if (empty($parts['scheme']) || empty($parts['host'])) {
			return $fallback;
		}
		if (!in_array(strtolower($parts['scheme']), array('http', 'https'), true)) {
			return $fallback;
		}

		if (empty($base_host)) {
			return $fallback;
		}

		$ref_host = strtolower($parts['host']);
		$base_host_lc = strtolower((string) $base_host);
		$allowed = ($ref_host === $base_host_lc);
		if (!$allowed) {
			$extra = getenv('CI_ALLOW_REDIRECT_HOSTS');
			if ($extra !== false && $extra !== '') {
				foreach (array_map('trim', explode(',', $extra)) as $h) {
					if ($h !== '' && strtolower($h) === $ref_host) {
						$allowed = true;
						break;
					}
				}
			}
		}
		if (!$allowed) {
			return $fallback;
		}

		return $url;
	}
}

if (!function_exists('safe_ci_redirect')) {
	/**
	 * Sanitize redirect targets from user input (e.g. hidden "redirect" field).
	 * Allows internal CI URIs (e.g. patient/medicalHistory?id=1); blocks external URLs unless same-origin.
	 *
	 * @param string|null $uri      POST/GET redirect value
	 * @param string      $fallback CI URI when empty or unsafe (e.g. 'patient' or 'meeting/upcoming')
	 * @return string Safe first argument for redirect()
	 */
	function safe_ci_redirect($uri, $fallback = 'home')
	{
		$CI = &get_instance();
		$trim = trim((string) $uri);
		if ($trim === '') {
			return $fallback;
		}
		if (preg_match('/[\r\n\x00]/', $trim)) {
			return $fallback;
		}
		if (preg_match('#^\s*(javascript|data|vbscript):#i', $trim)) {
			return $fallback;
		}
		if (strpos($trim, '//') === 0) {
			return $fallback;
		}
		if (preg_match('#^https?://#i', $trim)) {
			return safe_redirect_target($trim, $CI->config->site_url($fallback));
		}
		return $trim;
	}
}
