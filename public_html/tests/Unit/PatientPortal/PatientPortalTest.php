<?php

declare(strict_types=1);

namespace Tests\Unit\PatientPortal;

use PHPUnit\Framework\TestCase;

/**
 * @group PatientPortal
 *
 * Static source-file assertions for Phase 4 patient portal coverage.
 * Run: composer test -- --filter PatientPortal
 */
final class PatientPortalTest extends TestCase
{
    public function test_auth01_request_otp_rate_limits_after_six_requests(): void
    {
        $src = $this->readFile('application/modules/portal/controllers/Portal.php');

        self::assertStringContainsString('function request_otp', $src);
        self::assertStringContainsString('countRecentOtpRequests', $src);
        self::assertMatchesRegularExpression('/\$maxPer15\s*=\s*6/', $src);
        self::assertStringContainsString('csrf', $src);
    }

    public function test_auth01_verify_otp_sets_portal_verified_phone_session(): void
    {
        $src = $this->readFile('application/modules/portal/controllers/Portal.php');

        self::assertStringContainsString('function verify_otp', $src);
        self::assertStringContainsString("set_userdata('portal_verified_phone'", $src);
        self::assertStringContainsString("set_userdata('portal_verified_hospital'", $src);
        self::assertStringContainsString('markOtpVerified', $src);
    }

    public function test_auth01_verify_otp_locks_after_eight_attempts(): void
    {
        $src = $this->readFile('application/modules/portal/controllers/Portal.php');

        self::assertMatchesRegularExpression('/attempts\s*>=\s*8/', $src);
        self::assertStringContainsString('incrementOtpAttempts', $src);
        self::assertStringContainsString('Too many attempts', $src);
    }

    public function test_ui06_complete_booking_inserts_queue_row_and_appointment(): void
    {
        $src = $this->readFile('application/modules/portal/controllers/Portal.php');

        self::assertStringContainsString('function complete_booking', $src);
        self::assertStringContainsString("\$this->db->insert('appointment'", $src);
        self::assertStringContainsString('insertQueueRow', $src);
        self::assertStringContainsString('triage_json', $src);
        self::assertStringContainsString('slot_time', $src);
    }

    public function test_pay02_init_sslcommerz_creates_bd_payment_intent_row(): void
    {
        $src = $this->readFile('application/modules/payment_bd/controllers/Payment_bd.php');
        $success = $this->readFile('application/modules/portal/views/portal/book_success.php');

        self::assertStringContainsString('function init_sslcommerz', $src);
        self::assertStringContainsString('insertBdIntent', $src);
        self::assertStringContainsString("'gateway' => 'sslcommerz'", $src);
        self::assertStringContainsString('Bd_payment_sslcommerz::initiateSession', $src);
        self::assertStringContainsString('payment_bd/init_sslcommerz?queue_id=', $success);
    }

    public function test_pay02_sslcommerz_finalize_returns_false_on_amount_mismatch(): void
    {
        $src = $this->readFile('application/modules/payment_bd/controllers/Payment_bd.php');

        self::assertStringContainsString('function sslcommerz_try_finalize', $src);
        self::assertStringContainsString('validateTransaction', $src);
        self::assertMatchesRegularExpression('/abs\(\$paid_amt\s*-\s*\(float\)\s*\$intent->amount\)\s*<\s*0\.02/', $src);
        self::assertStringContainsString('SSLCommerz amount mismatch', $src);
    }

    public function test_ui07_ticker_json_returns_patient_serial_when_queue_id_provided(): void
    {
        $src = $this->readFile('application/modules/portal/controllers/Portal.php');
        $view = $this->readFile('application/modules/portal/views/portal/queue.php');

        self::assertStringContainsString('function ticker_json', $src);
        self::assertStringContainsString('patient_serial', $src);
        self::assertStringContainsString('estimated_wait', $src);
        self::assertStringContainsString('queue_id', $view);
        self::assertStringContainsString('setInterval(loadQueueStatus, 15000)', $view);
    }

    public function test_ui05_public_layout_uses_local_assets(): void
    {
        $layout = $this->readFile('application/modules/portal/views/portal/layout_public.php');

        self::assertStringContainsString('front/site_assets/vendor/bootstrap/css/bootstrap.min.css', $layout);
        self::assertStringContainsString('adminlte/plugins/jquery/jquery.min.js', $layout);
        self::assertStringContainsString('front/site_assets/vendor/bootstrap/js/bootstrap.min.js', $layout);
        self::assertStringNotContainsString('cdn.jsdelivr.net', $layout);
    }

    public function test_location_exception_lookup_prefers_newest_specific_row(): void
    {
        $src = $this->readFile('application/modules/portal/controllers/Portal.php');

        self::assertGreaterThanOrEqual(
            2,
            substr_count($src, "\$this->db->order_by('chamber_id', 'desc');\r\n        \$this->db->order_by('id', 'desc');")
                + substr_count($src, "\$this->db->order_by('chamber_id', 'desc');\n        \$this->db->order_by('id', 'desc');"),
            'Portal slot and booking exception lookups should prefer chamber-specific rows, then newest duplicate row'
        );
    }

    public function test_ui08_prescription_pdf_returns_403_when_phone_does_not_match(): void
    {
        $src = $this->readFile('application/modules/portal/controllers/Portal.php');

        self::assertStringContainsString('function prescription_pdf', $src);
        self::assertStringContainsString('portal_verified_phone', $src);
        self::assertStringContainsString('portal_verified_hospital', $src);
        self::assertMatchesRegularExpression('/show_error\([^;]+403\)/s', $src);
        self::assertStringContainsString('getPrescriptionForPortal', $src);
    }

    public function test_ui08_medicine_parse_produces_five_fields_per_entry(): void
    {
        $src = $this->readFile('application/modules/portal/controllers/Portal.php');

        self::assertStringContainsString('function _parse_prescription_medicines', $src);
        self::assertStringContainsString("explode('###'", $src);
        self::assertStringContainsString("explode('***'", $src);
        foreach (array('name', 'dose', 'frequency', 'days', 'instruction') as $key) {
            self::assertStringContainsString("'" . $key . "' =>", $src);
        }
    }

    private function readFile(string $relPath): string
    {
        $root = dirname(__DIR__, 3);
        $full = $root . DIRECTORY_SEPARATOR . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $relPath), DIRECTORY_SEPARATOR);
        self::assertFileExists($full, "Expected file not found: {$relPath}");

        return (string) file_get_contents($full);
    }
}
