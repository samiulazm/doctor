<?php

declare(strict_types=1);

namespace Tests\Support;

/**
 * Small pure helper for view output — mirrors the kind of escaping QC checks expect in views.
 */
final class HtmlEscapeHelper
{
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
