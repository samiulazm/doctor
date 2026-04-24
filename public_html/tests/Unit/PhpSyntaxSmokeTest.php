<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

final class PhpSyntaxSmokeTest extends TestCase
{
    public function test_lint_php_script_exists_and_passes_syntax_check(): void
    {
        $this->assertFilePassesPhpLint('scripts' . DIRECTORY_SEPARATOR . 'lint-php.php');
    }

    public function test_index_php_exists_and_passes_syntax_check(): void
    {
        $this->assertFilePassesPhpLint('index.php');
    }

    public function test_asset_helper_exists_and_passes_syntax_check(): void
    {
        $this->assertFilePassesPhpLint('application' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'asset_helper.php');
    }

    public function test_health_controller_exists_and_passes_syntax_check(): void
    {
        $this->assertFilePassesPhpLint('application' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'Health.php');
    }

    public function test_audit_log_model_exists_and_passes_syntax_check(): void
    {
        $this->assertFilePassesPhpLint('application' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'Audit_log_model.php');
    }

    public function test_logs_module_controller_exists_and_passes_syntax_check(): void
    {
        $this->assertFilePassesPhpLint('application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'Logs.php');
    }

    private function assertFilePassesPhpLint(string $relativeToProjectRoot): void
    {
        $root = dirname(__DIR__, 2);
        $file = $root . DIRECTORY_SEPARATOR . $relativeToProjectRoot;
        $this->assertFileExists($file);

        $php = \defined('PHP_BINARY') ? PHP_BINARY : 'php';
        $cmd = sprintf('%s -l %s', escapeshellarg($php), escapeshellarg($file));
        $output = [];
        $exitCode = 0;
        exec($cmd . ' 2>&1', $output, $exitCode);

        $this->assertSame(0, $exitCode, implode("\n", $output));
    }
}
