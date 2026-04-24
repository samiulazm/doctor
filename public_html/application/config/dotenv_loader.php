<?php

/**
 * Load Multi-Hospital/.env into getenv() / $_ENV (safe to require from index.php before BASEPATH).
 */
if (function_exists('ci_load_dotenv')) {
	return;
}

if (!function_exists('ci_load_dotenv')) {
	/**
	 * @param string $path Absolute path to .env
	 * @param bool   $overwrite If true, set every key from this file; if false, only keys not already in getenv()
	 */
	function ci_load_dotenv($path, $overwrite = false)
	{
		if (!is_readable($path)) {
			return;
		}
		$lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach ($lines as $line) {
			$line = trim($line);
			if ($line === '' || strpos($line, '#') === 0) {
				continue;
			}
			if (!preg_match('/^([\w\.]+)\s*=\s*(.*)\s*$/', $line, $m)) {
				continue;
			}
			$key = $m[1];
			$val = $m[2];
			if (preg_match('/^"(.*)"$/s', $val, $q)) {
				$val = str_replace(['\\"', '\\n', '\\r'], ['"', "\n", "\r"], $q[1]);
			} elseif (preg_match("/^'(.*)'$/s", $val, $q)) {
				$val = $q[1];
			}
			if ($overwrite || getenv($key) === false) {
				putenv($key . '=' . $val);
				$_ENV[$key] = $val;
			}
		}
	}
}

// 1) Project root (e.g. /home/user/.env next to public_html) — production DB creds, secrets.
// 2) public_html/.env — overrides (e.g. local Laragon dev without touching root file).
$ci_dotenv_root = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . '.env';
$ci_dotenv_public = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env';
ci_load_dotenv($ci_dotenv_root, false);
ci_load_dotenv($ci_dotenv_public, true);
