<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/application/config/database_bootstrap.php';
require_once dirname(__DIR__, 2) . '/application/config/dotenv_loader.php';

final class DatabaseBootstrapTest extends TestCase
{
    /** @var array<string, string|false> */
    private array $originalEnv = array();

    protected function setUp(): void
    {
        parent::setUp();

        foreach (array('CI_DB_HOST', 'CI_DB_USER', 'CI_DB_PASSWORD', 'CI_DB_NAME', 'CI_DB_PORT') as $key) {
            $this->originalEnv[$key] = getenv($key);
            putenv($key);
            unset($_ENV[$key]);
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->originalEnv as $key => $value) {
            if ($value === false) {
                putenv($key);
                unset($_ENV[$key]);
                continue;
            }

            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }

        parent::tearDown();
    }

    public function test_production_requires_all_database_env_vars(): void
    {
        $missing = ci_database_missing_env_keys('production');

        $this->assertSame(
            array('CI_DB_HOST', 'CI_DB_USER', 'CI_DB_PASSWORD', 'CI_DB_NAME'),
            $missing
        );
    }

    public function test_development_uses_local_defaults_when_env_is_missing(): void
    {
        $settings = ci_database_connection_settings('development');

        $this->assertSame('127.0.0.1', $settings['hostname']);
        $this->assertSame('root', $settings['username']);
        $this->assertSame('', $settings['password']);
        $this->assertSame('democa_hmz_v2', $settings['database']);
        $this->assertSame(3306, $settings['port']);
    }

    public function test_explicit_empty_password_is_accepted_in_production(): void
    {
        putenv('CI_DB_HOST=127.0.0.1');
        putenv('CI_DB_USER=app_user');
        putenv('CI_DB_PASSWORD=');
        putenv('CI_DB_NAME=app_db');

        $missing = ci_database_missing_env_keys('production');
        $settings = ci_database_connection_settings('production');

        $this->assertSame(array(), $missing);
        $this->assertSame('', $settings['password']);
        $this->assertSame(3306, $settings['port']);
    }

    public function test_dotenv_loader_preserves_empty_database_password(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'dotenv');
        $this->assertNotFalse($tmp);

        try {
            file_put_contents(
                $tmp,
                "CI_DB_HOST=127.0.0.1\nCI_DB_USER=test_user\nCI_DB_PASSWORD=\nCI_DB_NAME=test_db\n"
            );

            ci_load_dotenv($tmp);

            $this->assertSame('127.0.0.1', getenv('CI_DB_HOST'));
            $this->assertSame('test_user', getenv('CI_DB_USER'));
            $this->assertSame('', getenv('CI_DB_PASSWORD'));
            $this->assertSame('test_db', getenv('CI_DB_NAME'));
        } finally {
            @unlink($tmp);
        }
    }
}
