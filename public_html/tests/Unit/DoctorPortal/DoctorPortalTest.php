<?php

declare(strict_types=1);

namespace Tests\Unit\DoctorPortal;

use PHPUnit\Framework\TestCase;

/**
 * @group DoctorPortal
 *
 * Static source-file assertions for Phase 5 doctor portal coverage.
 * Run: composer test -- --filter DoctorPortal
 */
final class DoctorPortalTest extends TestCase
{
    public function test_ui09_stat_cards_count(): void
    {
        $html = $this->readFile('application/modules/doctor_chamber/views/doctor/dashboard.php');

        self::assertGreaterThanOrEqual(
            4,
            substr_count($html, 'chamber-stat-value'),
            'dashboard.php must have at least 4 .chamber-stat-value elements'
        );
    }

    public function test_ui09_today_revenue_query(): void
    {
        $src = $this->readFile('application/modules/doctor_chamber/controllers/Doctor_chamber.php');

        self::assertMatchesRegularExpression(
            '/today_revenue|SUM.*doctor_amount/s',
            $src,
            'Doctor_chamber.php must contain $today_revenue or SUM(doctor_amount) query'
        );
    }

    public function test_ui10_queue_json_method_exists(): void
    {
        $src = $this->readFile('application/modules/doctor_chamber/controllers/Doctor_chamber.php');

        self::assertStringContainsString(
            'function queue_json',
            $src,
            'Doctor_chamber.php must define queue_json()'
        );
    }

    public function test_ui10_chart_data_json_method_exists(): void
    {
        $src = $this->readFile('application/modules/doctor_chamber/controllers/Doctor_chamber.php');

        self::assertStringContainsString(
            'function chart_data_json',
            $src,
            'Doctor_chamber.php must define chart_data_json()'
        );
    }

    public function test_ui10_live_queue_body(): void
    {
        $html = $this->readFile('application/modules/doctor_chamber/views/doctor/dashboard.php');

        self::assertStringContainsString(
            'liveQueueBody',
            $html,
            'dashboard.php must contain tbody#liveQueueBody for long-poll population'
        );
    }

    public function test_ui10_no_server_rendered_queue_loop(): void
    {
        $html = $this->readFile('application/modules/doctor_chamber/views/doctor/dashboard.php');

        self::assertStringNotContainsString(
            'foreach ($queue_today',
            $html,
            'dashboard.php must NOT have a PHP foreach($queue_today) loop - queue is JS long-poll only'
        );
    }

    public function test_ui11_search_json_method_exists(): void
    {
        $src = $this->readFile('application/modules/doctor_chamber/controllers/Doctor_chamber.php');

        self::assertStringContainsString(
            'function search_json',
            $src,
            'Doctor_chamber.php must define search_json()'
        );
    }

    public function test_ui11_rxframe(): void
    {
        $html = $this->readFile('application/modules/doctor_chamber/views/doctor/consultation_room.php');

        self::assertStringContainsString(
            'id="rxFrame"',
            $html,
            'consultation_room.php must have an iframe with id="rxFrame"'
        );
    }

    private function readFile(string $relPath): string
    {
        $root = dirname(__DIR__, 3);
        $full = $root . DIRECTORY_SEPARATOR . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $relPath), DIRECTORY_SEPARATOR);
        self::assertFileExists($full, "Expected file not found: {$relPath}");

        return (string) file_get_contents($full);
    }
}
