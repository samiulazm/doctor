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
