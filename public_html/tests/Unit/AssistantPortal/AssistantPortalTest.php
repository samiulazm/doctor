<?php

declare(strict_types=1);

namespace Tests\Unit\AssistantPortal;

use PHPUnit\Framework\TestCase;

/**
 * @group AssistantPortal
 *
 * Static source-file assertions for Phase 6 assistant portal coverage.
 * Run: composer test -- --filter AssistantPortal
 */
final class AssistantPortalTest extends TestCase
{
    public function test_ui12_queue_ticker_json_method_exists(): void
    {
        $src = $this->readSource('application/modules/assistant_chamber/controllers/Assistant_chamber.php');

        self::assertStringContainsString('function queue_ticker_json', $src);
    }

    public function test_ui12_print_token_method_exists(): void
    {
        $src = $this->readSource('application/modules/assistant_chamber/controllers/Assistant_chamber.php');

        self::assertStringContainsString('function print_token', $src);
    }

    public function test_ui12_emergency_bump_sets_is_emergency(): void
    {
        $src = $this->readSource('application/modules/assistant_chamber/controllers/Assistant_chamber.php');

        self::assertStringContainsString('is_emergency', $src);
    }

    public function test_ui12_desk_view_has_source_tag(): void
    {
        $html = $this->readSource('application/modules/assistant_chamber/views/assistant/desk.php');

        self::assertStringContainsString('chamber-source-tag', $html);
    }

    public function test_ui12_desk_view_has_now_serving_badge(): void
    {
        $html = $this->readSource('application/modules/assistant_chamber/views/assistant/desk.php');

        self::assertStringContainsString('nowServingBadge', $html);
    }

    public function test_ui12_desk_view_uses_csrf_field(): void
    {
        $html = $this->readSource('application/modules/assistant_chamber/views/assistant/desk.php');

        self::assertStringContainsString('csrf_field()', $html);
        self::assertStringNotContainsString('get_csrf_token_name()', $html);
    }

    public function test_ui12_desk_view_has_print_token_link(): void
    {
        $html = $this->readSource('application/modules/assistant_chamber/views/assistant/desk.php');

        self::assertStringContainsString('print_token', $html);
    }

    public function test_ui12_desk_view_has_native_drag_fallback(): void
    {
        $html = $this->readSource('application/modules/assistant_chamber/views/assistant/desk.php');

        self::assertStringContainsString('window.Sortable', $html);
        self::assertStringContainsString('enableNativeDragFallback', $html);
        self::assertStringContainsString('dragover', $html);
    }

    public function test_ui12_migration_adds_is_emergency(): void
    {
        $this->assertSourceFileExists('application/migrations/20260427000012_chamber_queue_is_emergency.php');
    }

    public function test_ui13_mark_queue_fee_paid_ajax_method_exists(): void
    {
        $src = $this->readSource('application/modules/assistant_chamber/controllers/Assistant_chamber.php');

        self::assertStringContainsString('function mark_queue_fee_paid_ajax', $src);
    }

    public function test_ui13_billing_panel_partial_exists(): void
    {
        $this->assertSourceFileExists('application/modules/assistant_chamber/views/assistant/_billing_panel.php');
    }

    public function test_ui13_billing_panel_has_fee_input(): void
    {
        $html = $this->readSource('application/modules/assistant_chamber/views/assistant/_billing_panel.php');

        self::assertStringContainsString('id="fee_', $html);
    }

    public function test_ui13_billing_panel_has_paid_due_toggle(): void
    {
        $html = $this->readSource('application/modules/assistant_chamber/views/assistant/_billing_panel.php');

        self::assertStringContainsString('paidDueToggle_', $html);
    }

    public function test_ui13_billing_panel_has_pay_method_toggle(): void
    {
        $html = $this->readSource('application/modules/assistant_chamber/views/assistant/_billing_panel.php');

        self::assertStringContainsString('payMethodToggle_', $html);
    }

    public function test_pay01_bkash_initiate_desk_method_exists(): void
    {
        $src = $this->readSource('application/modules/payment_bd/controllers/Payment_bd.php');

        self::assertStringContainsString('function bkash_initiate_desk', $src);
    }

    public function test_pay01_bkash_initiate_desk_has_auth_guard(): void
    {
        $src = $this->readSource('application/modules/payment_bd/controllers/Payment_bd.php');

        self::assertStringContainsString('bkash_initiate_desk', $src);
        self::assertStringContainsString('ion_auth->logged_in()', $src);
    }

    public function test_pay01_billing_panel_has_bkash_section(): void
    {
        $html = $this->readSource('application/modules/assistant_chamber/views/assistant/_billing_panel.php');

        self::assertStringContainsString('bkashSection_', $html);
    }

    public function test_queue01_ticker_json_returns_serial_key(): void
    {
        $src = $this->readSource('application/modules/assistant_chamber/controllers/Assistant_chamber.php');

        self::assertStringContainsString("'serial'", $src);
    }

    public function test_queue01_css_has_emergency_class(): void
    {
        $css = $this->readSource('common/css/chamber-practice.css');

        self::assertStringContainsString('.chamber-status.emergency', $css);
    }

    public function test_queue01_css_has_source_tag(): void
    {
        $css = $this->readSource('common/css/chamber-practice.css');

        self::assertStringContainsString('.chamber-source-tag', $css);
    }

    public function test_queue01_css_has_btn_bkash(): void
    {
        $css = $this->readSource('common/css/chamber-practice.css');

        self::assertStringContainsString('.btn-bkash', $css);
    }

    public function test_queue01_token_print_view_exists(): void
    {
        $this->assertSourceFileExists('application/modules/assistant_chamber/views/assistant/token_print.php');
    }

    private function readSource(string $relPath): string
    {
        $full = $this->resolveSourcePath($relPath);
        self::assertFileExists($full, "Source file not found: {$relPath}");

        return (string) file_get_contents($full);
    }

    private function assertSourceFileExists(string $relPath): void
    {
        self::assertFileExists($this->resolveSourcePath($relPath), "Source file not found: {$relPath}");
    }

    private function resolveSourcePath(string $relPath): string
    {
        $root = dirname(__DIR__, 3);
        return $root . DIRECTORY_SEPARATOR . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $relPath), DIRECTORY_SEPARATOR);
    }
}
