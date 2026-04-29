<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Regression: site/index must not assume site_settings has a row (fresh DB or mis-seeded hospital).
 */
final class RequiredHookSiteSettingsTest extends TestCase
{
    public function test_site_settings_language_access_is_null_safe_in_required_hook(): void
    {
        $path = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'hooks' . DIRECTORY_SEPARATOR . 'required.php';
        $this->assertFileExists($path);
        $src = (string) file_get_contents($path);
        $this->assertStringNotContainsString(
            "get('site_settings')->row()->language",
            $src,
            'Chaining ->row()->language without a null check crashes when no site_settings row exists.'
        );
    }
}
