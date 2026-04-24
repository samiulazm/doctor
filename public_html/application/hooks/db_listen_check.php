<?php

if (!function_exists('db_listen_check')) {
	/**
	 * In development, fail early with a clear page if nothing is listening on the DB host/port
	 * (avoids mysqli_sql_exception "actively refused" with a long stack trace).
	 * Set CI_SKIP_DB_LISTEN_CHECK=1 in .env to bypass (e.g. non-TCP setups).
	 */
	function db_listen_check()
	{
		if (ENVIRONMENT !== 'development') {
			return;
		}
		$skip = getenv('CI_SKIP_DB_LISTEN_CHECK');
		if ($skip !== false && $skip !== '' && $skip !== '0' && strtolower((string) $skip) !== 'false') {
			return;
		}

		require_once APPPATH . 'config/database_bootstrap.php';
		$settings = ci_database_connection_settings(ENVIRONMENT);
		$host = $settings['hostname'];
		$port = isset($settings['port']) ? (int) $settings['port'] : 3306;

		// Socket path: TCP probe does not apply.
		if (isset($host[0]) && $host[0] === '/') {
			return;
		}

		$errno = 0;
		$errstr = '';
		$target = $host;
		if (filter_var($host, FILTER_VALIDATE_IP) === false && function_exists('gethostbyname')) {
			$resolved = @gethostbyname($host);
			if ($resolved !== $host) {
				$target = $resolved;
			}
		}

		$fp = @fsockopen($target, $port, $errno, $errstr, 2);
		if (is_resource($fp)) {
			fclose($fp);
			return;
		}

		header('HTTP/1.1 503 Service Unavailable', true, 503);
		header('Content-Type: text/html; charset=utf-8');
		$h = htmlspecialchars($host, ENT_QUOTES, 'UTF-8');
		$p = htmlspecialchars((string) $port, ENT_QUOTES, 'UTF-8');
		echo '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>MySQL not running</title>';
		echo '<style>body{font-family:system-ui,sans-serif;max-width:42rem;margin:2rem auto;padding:0 1rem;line-height:1.5;}code{background:#f3f4f6;padding:0 .25rem;border-radius:4px;}</style>';
		echo '</head><body>';
		echo '<h1>MySQL is not reachable</h1>';
		echo '<p>Nothing accepted a TCP connection to <strong>', $h, '</strong> port <strong>', $p, '</strong> ("actively refused" usually means the database server is stopped).</p>';
		echo '<p><strong>Laragon:</strong> Open Laragon → <strong>Start All</strong> (or start <strong>MySQL</strong> only). Wait until it shows as running, then refresh.</p>';
		echo '<p>Confirm <code>CI_DB_HOST</code> and <code>CI_DB_PORT</code> in <code>public_html/.env</code> match your <code>my.ini</code> (<code>port=...</code>). You can set <code>CI_SKIP_DB_LISTEN_CHECK=1</code> to skip this check.</p>';
		echo '</body></html>';
		exit(1);
	}
}
