<?php

declare(strict_types=1);

namespace Tests\Unit\DesignSystem;

use PHPUnit\Framework\TestCase;

/**
 * Static source-file assertions for Phase 3 admin design system.
 * Run: composer test -- --filter DesignSystem
 */
final class DesignSystemTest extends TestCase
{
    public function test_ui01_no_bg_gradient_light_in_list_views(): void
    {
        $matches = $this->grepModules('/content-wrapper\s+bg-gradient-light/');

        self::assertSame([], $matches, 'UI-01: Views still using bg-gradient-light wrapper: ' . implode(', ', $matches));
    }

    public function test_ui01_no_inline_style_layout_divs(): void
    {
        $matches = $this->grepModules('/<div[^>]+style\s*=/i');

        self::assertSame([], $matches, 'UI-01: Views with inline style= on div elements: ' . implode(', ', $matches));
    }

    public function test_ui02_no_editable_sample_in_list_views(): void
    {
        $matches = $this->grepModules('/id=["\']editable-sample["\']/');

        self::assertSame([], $matches, 'UI-02: Views still using id="editable-sample": ' . implode(', ', $matches));
    }

    public function test_ui02_datatables_controllers_return_records_total(): void
    {
        $matches = $this->grepModules('/recordsTotal/', true);

        self::assertGreaterThanOrEqual(25, count($matches), 'UI-02: Expected at least 25 controllers returning recordsTotal');
    }

    public function test_ui03_csrf_regenerate_is_true(): void
    {
        $src = $this->readFile('application/config/config.php');

        self::assertMatchesRegularExpression('/csrf_regenerate.*=.*true/', $src, 'UI-03: csrf_regenerate is not set to true in config.php');
    }

    public function test_ui03_csrf_inject_exposes_global_hash(): void
    {
        $src = $this->readFile('application/views/csrf_inject.php');

        self::assertStringContainsString('window.CI_CSRF_HASH', $src, 'UI-03: csrf_inject.php does not expose window.CI_CSRF_HASH');
    }

    public function test_ui04_no_raw_badge_classes(): void
    {
        $matches = $this->grepModules('/badge-(success|warning|danger|info)/');

        self::assertSame([], $matches, 'UI-04: Views with raw badge-* classes: ' . implode(', ', $matches));
    }

    public function test_ui04_ap_status_classes_defined(): void
    {
        $src = $this->readFile('application/assets/css/app-design-tokens.css');

        self::assertStringContainsString('.ap-status-success', $src, 'UI-04: .ap-status-success is not defined');
        self::assertStringContainsString('.ap-status-warning', $src, 'UI-04: .ap-status-warning is not defined');
        self::assertStringContainsString('.ap-status-danger', $src, 'UI-04: .ap-status-danger is not defined');
        self::assertStringContainsString('.ap-status-info', $src, 'UI-04: .ap-status-info is not defined');
    }

    private function readFile(string $relPath): string
    {
        $root = dirname(__DIR__, 3);
        $full = $root . DIRECTORY_SEPARATOR . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $relPath), DIRECTORY_SEPARATOR);
        self::assertFileExists($full, "Expected file not found: {$relPath}");

        return (string) file_get_contents($full);
    }

    /**
     * @return list<string>
     */
    private function grepModules(string $pattern, bool $controllersOnly = false): array
    {
        $root   = dirname(__DIR__, 3);
        $modDir = $root . '/application/modules';
        $matches = [];

        $rit = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($modDir, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($rit as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $path = $file->getPathname();
            if ($controllersOnly && !str_contains(str_replace('\\', '/', $path), '/controllers/')) {
                continue;
            }

            $contents = file_get_contents($path);
            if ($contents !== false && preg_match($pattern, $contents)) {
                $matches[] = str_replace($root . DIRECTORY_SEPARATOR, '', $path);
            }
        }

        return $matches;
    }
}
