<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/application/helpers/chamber_practice_helper.php';

final class ChamberPracticeWeekdayHelperTest extends TestCase
{
    public function test_maps_known_timestamps_to_weekly_hours_keys(): void
    {
        $this->assertSame('mon', chamber_practice_weekday_key_from_timestamp(strtotime('2026-04-27 12:00:00')));
        $this->assertSame('tue', chamber_practice_weekday_key_from_timestamp(strtotime('2026-04-28 12:00:00')));
        $this->assertSame('wed', chamber_practice_weekday_key_from_timestamp(strtotime('2026-04-29 12:00:00')));
        $this->assertSame('thu', chamber_practice_weekday_key_from_timestamp(strtotime('2026-04-30 12:00:00')));
        $this->assertSame('fri', chamber_practice_weekday_key_from_timestamp(strtotime('2026-05-01 12:00:00')));
        $this->assertSame('sat', chamber_practice_weekday_key_from_timestamp(strtotime('2026-05-02 12:00:00')));
        $this->assertSame('sun', chamber_practice_weekday_key_from_timestamp(strtotime('2026-05-03 12:00:00')));
    }

    public function test_invalid_timestamp_returns_false(): void
    {
        $this->assertFalse(chamber_practice_weekday_key_from_timestamp(0));
    }
}
