<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use PHPUnit\Framework\TestCase;

/**
 * Static source-file assertions for Phase 1 security patches.
 * These tests read PHP source files and assert that dangerous patterns
 * are absent and safe patterns are present. No CI3 bootstrap needed.
 *
 * Run: composer test -- --filter SecurityPatch
 */
final class SecurityPatchTest extends TestCase
{
    public function test_sec01_api_token_uses_random_bytes(): void
    {
        $src = $this->readModule('application/modules/api/controllers/Api.php');

        $this->assertStringNotContainsString(
            'rand(1111, 9999)',
            $src,
            'SEC-01: rand(1111,9999) still present in Api.php'
        );
        $this->assertStringContainsString(
            'random_bytes',
            $src,
            'SEC-01: random_bytes() not found in Api.php'
        );
    }

    public function test_sec02_hospital_id_not_from_get(): void
    {
        $src = $this->readModule('application/modules/api/controllers/Api.php');

        $this->assertStringNotContainsString(
            "input->get('hospital_id')",
            $src,
            "SEC-02: input->get('hospital_id') still present in Api.php"
        );
    }

    public function test_sec03_auth_controller_checks_lockout(): void
    {
        $src = $this->readModule('application/modules/auth/controllers/Auth.php');

        $this->assertStringContainsString(
            'is_time_locked_out',
            $src,
            'SEC-03: is_time_locked_out() not found in Auth.php'
        );
    }

    public function test_sec03_lockout_time_is_300_seconds(): void
    {
        $src = $this->readModule('application/config/ion_auth.php');

        $this->assertStringNotContainsString(
            "lockout_time']               = 50",
            $src,
            "SEC-03: lockout_time is still 50 seconds in ion_auth.php"
        );
        $this->assertMatchesRegularExpression(
            "/lockout_time'\]\s*=\s*300/",
            $src,
            'SEC-03: lockout_time is not set to 300 in ion_auth.php'
        );
    }

    public function test_sec04_debug_session_method_removed(): void
    {
        $src = $this->readModule('application/modules/ai_image_analysis/controllers/Ai_image_analysis.php');

        $this->assertStringNotContainsString(
            'function debugSession',
            $src,
            'SEC-04: debugSession() method still exists in Ai_image_analysis.php'
        );
    }

    public function test_sec05_no_like_string_interpolation_in_modules(): void
    {
        $root = dirname(__DIR__, 3);
        $modulesDir = $root . '/application/modules';

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($modulesDir, \FilesystemIterator::SKIP_DOTS)
        );

        $violations = [];
        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $path = $file->getPathname();
            if (str_contains($path, 'testpkz')) {
                continue;
            }

            $content = file_get_contents($path);
            if ($content !== false && str_contains($content, 'NULL, FALSE')) {
                $violations[] = str_replace($root . DIRECTORY_SEPARATOR, '', $path);
            }
        }

        $this->assertEmpty(
            $violations,
            'SEC-05: NULL, FALSE LIKE pattern still present in: ' . implode(', ', $violations)
        );
    }

    public function test_sec06_no_hardcoded_12345_password(): void
    {
        $files = [
            'application/modules/frontend/controllers/Frontend.php',
            'application/modules/meeting/controllers/Meeting.php',
            'application/modules/payu/controllers/Payu.php',
            'application/modules/request/controllers/Request.php',
        ];

        foreach ($files as $relPath) {
            $src = $this->readModule($relPath);
            $this->assertStringNotContainsString(
                "= '12345'",
                $src,
                "SEC-06: hardcoded '12345' still present in {$relPath}"
            );
        }
    }

    public function test_sec07_no_debug_echo_print_r(): void
    {
        $files = [
            'application/modules/ai_image_analysis/controllers/Ai_image_analysis.php',
            'application/modules/ambulance/controllers/Ambulance.php',
            'application/modules/inventory/controllers/Inventory.php',
        ];

        foreach ($files as $relPath) {
            $src = $this->readModule($relPath);
            $this->assertStringNotContainsString(
                'echo print_r',
                $src,
                "SEC-07: echo print_r still present in {$relPath}"
            );
            $this->assertStringNotContainsString(
                'echo "Database error',
                $src,
                "SEC-07: echo \"Database error still present in {$relPath}"
            );
        }
    }

    public function test_sec08_csp_header_present(): void
    {
        $src = $this->readModule('application/hooks/security_headers.php');

        $this->assertStringContainsString(
            'Content-Security-Policy',
            $src,
            'SEC-08: Content-Security-Policy header not found in security_headers.php'
        );
    }

    public function test_sec09_openai_key_from_env_not_db(): void
    {
        $files = [
            'application/modules/ai_image_analysis/controllers/Ai_image_analysis.php',
            'application/modules/ai_image_analysis/controllers/Ai_patient_condition.php',
            'application/modules/ai_patient_overview/controllers/Ai_patient_overview.php',
            'application/modules/treatment_plan/controllers/Treatment_plan.php',
        ];

        foreach ($files as $relPath) {
            $src = $this->readModule($relPath);
            $this->assertStringNotContainsString(
                'chatgpt_api_key',
                $src,
                "SEC-09: chatgpt_api_key still referenced in {$relPath}"
            );
        }
    }

    public function test_sec09_openai_config_has_env_key(): void
    {
        $src = $this->readModule('application/config/openai.php');

        $this->assertStringContainsString(
            'openai_api_key',
            $src,
            'SEC-09: openai_api_key config key not found in application/config/openai.php'
        );
        $this->assertStringContainsString(
            'OPENAI_API_KEY',
            $src,
            'SEC-09: OPENAI_API_KEY env var not read in application/config/openai.php'
        );
    }

    public function test_sec10_init_categories_has_auth_guard(): void
    {
        $src = $this->readModule('application/modules/inventory/controllers/Inventory.php');

        $pattern = '/function init_categories\(\).*?DROP TABLE/s';
        $this->assertMatchesRegularExpression(
            $pattern,
            $src,
            'SEC-10: init_categories() + DROP TABLE not found in expected structure'
        );

        $methodStart = strpos($src, 'function init_categories()');
        $dropTable = strpos($src, 'DROP TABLE', $methodStart);
        $this->assertNotFalse($methodStart, 'init_categories() not found');
        $this->assertNotFalse($dropTable, 'DROP TABLE not found after init_categories()');

        $methodBody = substr($src, $methodStart, $dropTable - $methodStart);
        $this->assertStringContainsString(
            'in_group',
            $methodBody,
            'SEC-10: in_group() auth guard not found before DROP TABLE in init_categories()'
        );
    }

    private function readModule(string $relativePath): string
    {
        $root = dirname(__DIR__, 3);
        $full = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        $this->assertFileExists($full, "Source file not found: {$relativePath}");

        return (string) file_get_contents($full);
    }
}
