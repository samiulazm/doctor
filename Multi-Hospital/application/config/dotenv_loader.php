<?php

/**
 * Load Multi-Hospital/.env into getenv() / $_ENV (safe to require from index.php before BASEPATH).
 */
if (function_exists('ci_load_dotenv')) {
	return;
}

if (!function_exists('ci_load_dotenv')) {
	function ci_load_dotenv($path)
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
			if (getenv($key) === false) {
				putenv($key . '=' . $val);
				$_ENV[$key] = $val;
			}
		}
	}
}

$ci_dotenv_path = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env';
ci_load_dotenv($ci_dotenv_path);
