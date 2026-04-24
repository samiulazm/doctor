<?php

if (!function_exists('ci_database_env_value')) {
    /**
     * Environment variable value or fallback.
     */
    function ci_database_env_value($key, $default)
    {
        $value = getenv($key);
        return $value === false ? $default : $value;
    }
}

if (!function_exists('ci_database_missing_env_keys')) {
    /**
     * Required DB env vars outside development.
     *
     * @return array<int, string>
     */
    function ci_database_missing_env_keys($environment)
    {
        if ($environment === 'development') {
            return array();
        }

        $required = array('CI_DB_HOST', 'CI_DB_USER', 'CI_DB_PASSWORD', 'CI_DB_NAME');
        $missing = array();

        foreach ($required as $key) {
            if (getenv($key) === false) {
                $missing[] = $key;
            }
        }

        return $missing;
    }
}

if (!function_exists('ci_database_missing_env_message')) {
    /**
     * Friendly failure message for missing required DB config.
     */
    function ci_database_missing_env_message($environment, array $missing)
    {
        return sprintf(
            'Missing required database environment variables for %s: %s',
            $environment,
            implode(', ', $missing)
        );
    }
}

if (!function_exists('ci_database_dbdriver_config')) {
	/**
	 * Prefer mysqli; fall back to PDO MySQL when mysqli is not loaded (common misconfigured php.ini).
	 *
	 * @return array{dbdriver: string, subdriver?: string}
	 */
	function ci_database_dbdriver_config()
	{
		if (function_exists('mysqli_init')) {
			return array('dbdriver' => 'mysqli');
		}
		if (extension_loaded('pdo_mysql') && class_exists('PDO', false)) {
			return array('dbdriver' => 'pdo', 'subdriver' => 'mysql');
		}
		throw new RuntimeException(
			'PHP needs either the mysqli extension or PDO MySQL (pdo_mysql). '
			.'Enable extension=mysqli and/or extension=pdo_mysql in php.ini, then restart your web server.'
		);
	}
}

if (!function_exists('ci_database_resolve_port')) {
	/**
	 * TCP port for MySQL (mysqli / PDO MySQL). Default 3306.
	 */
	function ci_database_resolve_port()
	{
		$p = (int) ci_database_env_value('CI_DB_PORT', '3306');
		return $p > 0 ? $p : 3306;
	}
}

if (!function_exists('ci_database_connection_settings')) {
    /**
     * Resolve the DB settings used by application/config/database.php.
     *
     * @return array{hostname: string, username: string, password: string, database: string, port: int}
     */
    function ci_database_connection_settings($environment)
    {
        $defaults = array(
            'hostname' => '127.0.0.1',
            'username' => 'root',
            'password' => '',
            'database' => 'democa_hmz_v2',
        );
		$port = ci_database_resolve_port();

        if ($environment !== 'development') {
            return array(
                'hostname' => ci_database_env_value('CI_DB_HOST', ''),
                'username' => ci_database_env_value('CI_DB_USER', ''),
                'password' => ci_database_env_value('CI_DB_PASSWORD', ''),
                'database' => ci_database_env_value('CI_DB_NAME', ''),
				'port' => $port,
            );
        }

        return array(
            'hostname' => ci_database_env_value('CI_DB_HOST', $defaults['hostname']),
            'username' => ci_database_env_value('CI_DB_USER', $defaults['username']),
            'password' => ci_database_env_value('CI_DB_PASSWORD', $defaults['password']),
            'database' => ci_database_env_value('CI_DB_NAME', $defaults['database']),
			'port' => $port,
        );
    }
}
