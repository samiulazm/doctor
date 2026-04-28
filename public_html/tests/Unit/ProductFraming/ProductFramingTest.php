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

    public function test_login_page_has_no_demo_hms_credentials_or_remote_brand_assets(): void
    {
        $src = $this->readFile('application/views/auth/login.php');

        self::assertStringContainsString('uploads/favicon.png', $src);
        self::assertStringNotContainsString('@hms.com', $src);
        self::assertStringNotContainsString('12345', $src);
        self::assertStringNotContainsString('fillCredentials', $src);
        self::assertStringNotContainsString('toggleSections', $src);
        self::assertStringNotContainsString('cdn-icons-png.flaticon.com', $src);
        self::assertStringNotContainsString('fonts.googleapis.com', $src);
    }

    public function test_frontend_registration_copy_uses_practice_language(): void
    {
        $view = $this->readFile('application/modules/frontend/views/front_end.php');
        $controller = $this->readFile('application/modules/frontend/controllers/Frontend.php');

        self::assertStringContainsString('Register practice', $view);
        self::assertStringContainsString('Practice <?php echo lang(\'name\'); ?>', $view);
        self::assertStringContainsString('New practice created successfully', $view);
        self::assertStringContainsString('Doctor Chamber Practice Management', $controller);
        self::assertStringContainsString('Your practice is registered successfully', $controller);
        self::assertStringContainsString('Practice Registration confirmation', $controller);
        self::assertStringNotContainsString('Hospital, Clinic, Management, Software', $view);
        self::assertStringNotContainsString("lang('register_hospital')", $view);
        self::assertStringNotContainsString('Hospital Not Created', $controller);
        self::assertStringNotContainsString('Hospital management System', $controller);
        self::assertStringNotContainsString('Your hospital is registered successfully', $controller);
        self::assertStringNotContainsString('Hospital Registration confirmation', $controller);
    }

    public function test_settings_and_subscription_copy_use_practice_language(): void
    {
        $settingsView = $this->readFile('application/modules/settings/views/settings.php');
        $invoiceView = $this->readFile('application/modules/settings/views/invoice.php');
        $controller = $this->readFile('application/modules/settings/controllers/Settings.php');

        self::assertStringContainsString('Practice <?php echo lang(\'email\'); ?>', $settingsView);
        self::assertStringContainsString('Practice <?php echo lang(\'name\'); ?>', $invoiceView);
        self::assertStringContainsString('Your practice package has changed successfully', $controller);
        self::assertStringContainsString('Your practice package has renewed successfully', $controller);
        self::assertStringContainsString('Practice Package Updated', $controller);
        self::assertStringContainsString("'hospital_name' => 'Practice Name'", $controller);
        self::assertStringContainsString("'hospital_email' => 'Practice Email'", $controller);
        self::assertStringNotContainsString('hospital_email\'); ?>', $settingsView);
        self::assertStringNotContainsString("lang('hospital'); ?> <?php echo lang('name')", $invoiceView);
        self::assertStringNotContainsString('Your hospital package has changed successfully', $controller);
        self::assertStringNotContainsString('Your hospital package has renewed successfully', $controller);
        self::assertStringNotContainsString('Hospital Package Changed', $controller);
        self::assertStringNotContainsString("'hospital_name' => 'Hospital Name'", $controller);
        self::assertStringNotContainsString("'hospital_email' => 'Hospital Email'", $controller);
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
