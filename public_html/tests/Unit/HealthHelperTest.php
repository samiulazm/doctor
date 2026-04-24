<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/application/helpers/health_helper.php';

final class HealthHelperTest extends TestCase
{
    public function test_ready_payload_reports_connected_database(): void
    {
        $payload = ci_health_ready_payload(true, '2026.04.21', null, '2026-04-21T10:00:00Z');

        $this->assertSame('ready', $payload['status']);
        $this->assertSame('connected', $payload['database']);
        $this->assertSame('2026.04.21', $payload['release']);
        $this->assertArrayNotHasKey('error', $payload);
    }

    public function test_ready_payload_reports_disconnected_database_without_secrets(): void
    {
        $payload = ci_health_ready_payload(false, '', 'db_error', '2026-04-21T10:00:00Z');

        $this->assertSame('not_ready', $payload['status']);
        $this->assertSame('disconnected', $payload['database']);
        $this->assertSame('db_error', $payload['error']);
        $this->assertArrayNotHasKey('release', $payload);
    }
}
