<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tests\Support\HtmlEscapeHelper;

final class BootstrapTest extends TestCase
{
    public function test_autoload_loads_tests_namespace(): void
    {
        $this->assertTrue(class_exists(HtmlEscapeHelper::class));
    }
}
