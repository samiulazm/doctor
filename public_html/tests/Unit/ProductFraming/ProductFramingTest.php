<?php

declare(strict_types=1);

namespace Tests\Unit\ProductFraming;

use PHPUnit\Framework\TestCase;

/**
 * Static source-file assertions that keep launch copy focused on doctor chamber/practice management.
 *
 * Run: composer test -- --filter ProductFraming
 */
final class ProductFramingTest extends TestCase
{
    public function test_auth_metadata_uses_doctor_practice_keywords(): void
    {
        foreach (array('forgot_password.php', 'reset_password.php') as $file) {
            $src = $this->readFile('application/views/auth/' . $file);

            self::assertStringContainsString('Doctor Chamber', $src);
            self::assertStringContainsString('Practice Management', $src);
            self::assertStringNotContainsString('Hospital, Clinic, Management, Software', $src);
            self::assertStringNotContainsString('Hms', $src);
        }
    }

    public function test_treatment_plan_prescription_output_uses_practice_language(): void
    {
        $src = $this->readFile('application/modules/treatment_plan/views/index.php');

        self::assertStringContainsString('practice-info', $src);
        self::assertStringContainsString('practice-name', $src);
        self::assertStringContainsString('practice-details', $src);
        self::assertStringContainsString('Contact the practice', $src);
        self::assertStringContainsString('"Practice"', $src);
        self::assertStringNotContainsString('Contact the hospital', $src);
        self::assertStringNotContainsString('hospital-info', $src);
        self::assertStringNotContainsString('hospital-name', $src);
        self::assertStringNotContainsString('hospital-details', $src);
    }

    public function test_treatment_plan_ai_prompt_uses_practice_language(): void
    {
        $src = $this->readFile('application/modules/treatment_plan/controllers/Treatment_plan.php');

        self::assertStringContainsString('practice/chamber information', $src);
        self::assertStringContainsString('[Practice Name/Logo]', $src);
        self::assertStringNotContainsString('clinic/hospital information', $src);
        self::assertStringNotContainsString('[Clinic Name/Logo]', $src);
    }

    private function readFile(string $relPath): string
    {
        $root = dirname(__DIR__, 3);
        $full = $root . DIRECTORY_SEPARATOR . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $relPath), DIRECTORY_SEPARATOR);
        self::assertFileExists($full, "Expected file not found: {$relPath}");

        return (string) file_get_contents($full);
    }
}
