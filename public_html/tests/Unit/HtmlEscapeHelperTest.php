<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tests\Support\HtmlEscapeHelper;

final class HtmlEscapeHelperTest extends TestCase
{
    public function test_escapes_html_special_characters(): void
    {
        $raw = '<script>alert("x")</script>';
        $out = HtmlEscapeHelper::escape($raw);

        $this->assertStringNotContainsString('<script>', $out);
        $this->assertStringContainsString('&lt;script&gt;', $out);
    }

    public function test_plain_text_is_unchanged_except_encoding(): void
    {
        $this->assertSame('Patient 42', HtmlEscapeHelper::escape('Patient 42'));
    }

    public function test_escapes_ampersand_and_quotes_for_attributes(): void
    {
        $raw = 'A & B "quoted"';
        $out = HtmlEscapeHelper::escape($raw);

        $this->assertStringContainsString('&amp;', $out);
        $this->assertStringContainsString('&quot;', $out);
        $this->assertStringNotContainsString('"quoted"', $out);
    }
}
