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

    public function test_ui10_dashboard_uses_bundled_chart_asset(): void
    {
        $html = $this->readFile('application/modules/doctor_chamber/views/doctor/dashboard.php');

        self::assertStringContainsString(
            'adminlte/plugins/chart.js/Chart.min.js',
            $html,
            'dashboard.php must use the bundled Chart.js asset for production-safe chart rendering'
        );
        self::assertStringNotContainsString(
            'cdn.jsdelivr.net/npm/chart.js',
            $html,
            'dashboard.php must not depend on the Chart.js CDN'
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

    public function test_doctor_crm_uses_css_inline_form_class(): void
    {
        $html = $this->readFile('application/modules/doctor_chamber/views/doctor/crm_search.php');

        self::assertStringContainsString('chamber-inline-form', $html);
        self::assertStringNotContainsString('style="display:inline"', $html);
    }

    public function test_location_schedule_exceptions_are_hospital_scoped_and_upserted(): void
    {
        $src = $this->readFile('application/modules/doctor_chamber/controllers/Doctor_chamber.php');

        self::assertMatchesRegularExpression(
            '/function schedule_exceptions\(\).*?where\(\'hospital_id\', \$doc->hospital_id\).*?doctor_schedule_exception/s',
            $src,
            'schedule_exceptions() must scope listed location exceptions by hospital'
        );
        self::assertStringContainsString('normalizeChamberDate', $src);
        self::assertStringContainsString('normalizeChamberTime', $src);
        self::assertStringContainsString("\$existing = \$this->db->get('doctor_schedule_exception')->row();", $src);
        self::assertStringContainsString("update('doctor_schedule_exception'", $src);
    }

    public function test_location_forms_use_time_inputs(): void
    {
        $locations = $this->readFile('application/modules/doctor_chamber/views/doctor/my_chambers.php');
        $exceptions = $this->readFile('application/modules/doctor_chamber/views/doctor/schedule_exceptions.php');

        self::assertStringContainsString('type="time" name="wh_<?php echo $dk; ?>_open"', $locations);
        self::assertStringContainsString('type="time" name="wh_<?php echo $dk; ?>_close"', $locations);
        self::assertStringContainsString('name="open_time" type="time"', $exceptions);
        self::assertStringContainsString('name="close_time" type="time"', $exceptions);
    }

    public function test_doctor_profile_and_template_queries_are_hospital_scoped(): void
    {
        $src = $this->readFile('application/modules/doctor_chamber/controllers/Doctor_chamber.php');

        self::assertMatchesRegularExpression(
            '/function portal_profile\(\).*?where\(\'doctor_id\', \$doc->id\).*?where\(\'hospital_id\', \$doc->hospital_id\).*?doctor_portal_profile/s',
            $src,
            'portal_profile() must scope profile reads by hospital'
        );
        self::assertMatchesRegularExpression(
            '/function template_builder\(\).*?where\(\'doctor_id\', \$doc->id\).*?where\(\'hospital_id\', \$doc->hospital_id\).*?prescription_print_template/s',
            $src,
            'template_builder() must scope template reads by hospital'
        );
        self::assertStringContainsString("'hospital_id' => \$doc->hospital_id", $src);
    }

    public function test_doctor_crm_post_actions_verify_patient_hospital(): void
    {
        $src = $this->readFile('application/modules/doctor_chamber/controllers/Doctor_chamber.php');

        self::assertMatchesRegularExpression(
            '/function tag_patient\(\).*?getPatientById\(\$pid\).*?hospital_id.*?\$doc->hospital_id.*?patient_practice_tag/s',
            $src,
            'tag_patient() must verify posted patient_id belongs to the doctor hospital'
        );
        self::assertMatchesRegularExpression(
            '/function refer_lab\(\).*?getPatientById\(\$pid\).*?hospital_id.*?\$doc->hospital_id.*?insertReferral/s',
            $src,
            'refer_lab() must verify posted patient_id belongs to the doctor hospital'
        );
    }

    public function test_production_json_guard_requireDoctorJson(): void
    {
        $src = $this->readFile('application/modules/doctor_chamber/controllers/Doctor_chamber.php');

        self::assertStringContainsString('function requireDoctorJson', $src);
        self::assertStringContainsString('doctor_profile_required', $src);
        self::assertMatchesRegularExpression(
            '/function vitals_json\(\).*?requireDoctorJson/s',
            $src,
            'vitals_json must call requireDoctorJson before using $doc'
        );
        self::assertMatchesRegularExpression(
            '/function search_json\(\).*?requireDoctorJson/s',
            $src,
            'search_json must call requireDoctorJson before using $doc'
        );
        self::assertMatchesRegularExpression(
            '/function drug_search_json\(\).*?requireDoctorJson/s',
            $src,
            'drug_search_json must call requireDoctorJson before using $doc'
        );
        self::assertMatchesRegularExpression(
            '/function queue_json\(\).*?requireDoctorJson/s',
            $src,
            'queue_json must use requireDoctorJson for missing doctor profile'
        );
        self::assertMatchesRegularExpression(
            '/function chart_data_json\(\).*?requireDoctorJson/s',
            $src,
            'chart_data_json must use requireDoctorJson for missing doctor profile'
        );
        self::assertStringContainsString('normalizeChamberDate', $src);
        self::assertStringContainsString("'error' => 'invalid_patient'", $src);
        self::assertStringContainsString("'error' => 'invalid_date'", $src);
    }

    public function test_dashboard_queue_and_chart_resilience_js(): void
    {
        $html = $this->readFile('application/modules/doctor_chamber/views/doctor/dashboard.php');

        self::assertStringContainsString('queueConnectionStatus', $html);
        self::assertStringContainsString('.fail(function', $html);
        self::assertStringContainsString('queueInflight', $html);
        self::assertStringContainsString('scheduleQueue(POLL_INTERVAL_OK)', $html);
        self::assertStringContainsString('POLL_MAX_BACKOFF', $html);
        self::assertStringNotContainsString('setInterval(pollQueue', $html);
        self::assertStringContainsString('chartPollStatus', $html);
        self::assertStringContainsString('chartInflight', $html);
        self::assertStringContainsString('scheduleCharts', $html);
        self::assertStringContainsString('Stale - retrying', $html);
        self::assertStringContainsString('Stale', $html);
        self::assertStringContainsString('Reconnecting', $html);
    }

    public function test_consultation_room_hardening_js(): void
    {
        $html = $this->readFile('application/modules/doctor_chamber/views/doctor/consultation_room.php');

        self::assertStringContainsString('event.origin !== window.location.origin', $html);
        self::assertStringContainsString('consultationStatusBar', $html);
        self::assertStringContainsString('SAVE_BUSY_MAX_MS', $html);
        self::assertStringContainsString('Prescription frame is not ready', $html);
        self::assertStringContainsString('Cannot access the prescription form', $html);
        self::assertStringContainsString('Prescription form not found', $html);
        self::assertStringContainsString('setActionBusy(false)', $html);
        self::assertStringContainsString('VITALS_INTERVAL_MS = 8000', $html);
        self::assertStringContainsString('vitalsPollStatus', $html);
        self::assertStringContainsString('Vitals feed is stale; retrying automatically.', $html);
        self::assertStringContainsString('scheduleVitals(0)', $html);
        self::assertMatchesRegularExpression(
            '/vitals_json.*?\.fail\(/s',
            $html,
            'vitals_json poll must chain .fail() for stale handling'
        );
    }

    public function test_prescription_embed_postmessage_minimal(): void
    {
        $src = $this->readFile('application/modules/prescription/controllers/Prescription.php');

        self::assertStringContainsString("'type' => 'rx:saved'", $src);
        self::assertStringContainsString("'print_url' =>", $src);
        self::assertStringContainsString('array_intersect_key', $src);
        self::assertStringContainsString('window.parent.postMessage(p, window.location.origin)', $src);
        self::assertStringContainsString('JSON_HEX_TAG', $src);
    }

    public function test_prescription_embed_form_has_print_after_hidden(): void
    {
        $html = $this->readFile('application/modules/prescription/views/add_new_prescription_view.php');

        self::assertStringContainsString('<input type="hidden" name="embed" value="1">', $html);
        self::assertStringContainsString('name="print_after"', $html);
        self::assertStringContainsString('embed_print_after', $html);
    }

    private function readFile(string $relPath): string
    {
        $root = dirname(__DIR__, 3);
        $full = $root . DIRECTORY_SEPARATOR . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $relPath), DIRECTORY_SEPARATOR);
        self::assertFileExists($full, "Expected file not found: {$relPath}");

        return (string) file_get_contents($full);
    }
}
