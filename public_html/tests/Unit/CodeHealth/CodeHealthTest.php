<?php

declare(strict_types=1);

namespace Tests\Unit\CodeHealth;

use PHPUnit\Framework\TestCase;

/**
 * Static source-file assertions for Phase 2 code health fixes.
 *
 * Run: composer test -- --filter CodeHealth
 */
final class CodeHealthTest extends TestCase
{
    public function test_bug01_no_full_table_scan(): void
    {
        $src = $this->readModule('application/modules/home/controllers/Home.php');

        $this->assertStringNotContainsString(
            "\$this->db->get('patient')->result()",
            $src,
            "BUG-01: bare get('patient')->result() still present in Home.php"
        );
    }

    public function test_bug02_testpkz_controller_deleted(): void
    {
        $this->assertFileDoesNotExist(
            $this->path('application/modules/testpkz/controllers/Testpkz.php'),
            'BUG-02: Testpkz.php controller still exists'
        );
    }

    public function test_bug02_home_backup_deleted(): void
    {
        $this->assertFileDoesNotExist(
            $this->path('application/modules/home/views/home_backup.php'),
            'BUG-02: home_backup.php still exists'
        );
    }

    public function test_bug02_testpkz_removed_from_required(): void
    {
        $src = $this->readModule('application/hooks/required.php');

        $this->assertStringNotContainsString(
            "'testpkz'",
            $src,
            "BUG-02: 'testpkz' still present in required.php"
        );
    }

    public function test_bug03_no_phpexcel_instantiation(): void
    {
        $src = $this->readModule('application/modules/import/controllers/Import.php');

        $this->assertStringNotContainsString(
            'PHPExcel_IOFactory',
            $src,
            'BUG-03: PHPExcel_IOFactory still present in Import.php'
        );
        $this->assertStringNotContainsString(
            'PHPExcel_Cell',
            $src,
            'BUG-03: PHPExcel_Cell still present in Import.php'
        );
        $this->assertStringNotContainsString(
            'getCellByColumnAndRow',
            $src,
            'BUG-03: getCellByColumnAndRow still present in Import.php'
        );
    }

    public function test_bug03_phpspreadsheet_namespace_present(): void
    {
        $src = $this->readModule('application/modules/import/controllers/Import.php');

        $this->assertGreaterThanOrEqual(
            2,
            substr_count($src, 'PhpOffice\\PhpSpreadsheet'),
            'BUG-03: PhpOffice\\PhpSpreadsheet namespace missing in Import.php'
        );
    }

    public function test_bug04_no_die_in_addappointment(): void
    {
        $src = $this->readModule('application/modules/api/controllers/Api.php');
        $methodStart = strpos($src, 'function addAppointment');
        $this->assertNotFalse($methodStart, 'BUG-04: addAppointment() not found in Api.php');

        $nextFunction = strpos($src, "\n    function", $methodStart + 1);
        if ($nextFunction === false) {
            $nextFunction = strlen($src);
        }

        $methodBody = substr($src, $methodStart, $nextFunction - $methodStart);
        $this->assertStringNotContainsString(
            'die()',
            $methodBody,
            'BUG-04: die() still present in Api::addAppointment()'
        );
    }

    public function test_bug05_no_fopen_in_home(): void
    {
        $src = $this->readModule('application/modules/home/controllers/Home.php');

        $this->assertStringNotContainsString('fopen(', $src, 'BUG-05: fopen still present in Home.php');
        $this->assertStringNotContainsString('fwrite(', $src, 'BUG-05: fwrite still present in Home.php');
        $this->assertStringNotContainsString('function timeZone', $src, 'BUG-05: timeZone() still present in Home.php');
    }

    public function test_bug05_no_fopen_in_settings(): void
    {
        $src = $this->readModule('application/modules/settings/controllers/Settings.php');

        $this->assertStringNotContainsString('function timeZone', $src, 'BUG-05: timeZone() still present in Settings.php');
        $this->assertStringNotContainsString('index.tmp', $src, 'BUG-05: index.tmp write still present in Settings.php');
    }

    public function test_bug06_no_error_reporting_in_bed(): void
    {
        $src = $this->readModule('application/modules/bed/controllers/Bed.php');
        $this->assertStringNotContainsString('error_reporting(0)', $src, 'BUG-06: error_reporting(0) still present in Bed.php');
    }

    public function test_bug06_no_error_reporting_in_finance(): void
    {
        $src = $this->readModule('application/modules/finance/controllers/Finance.php');
        $this->assertStringNotContainsString('error_reporting(0)', $src, 'BUG-06: error_reporting(0) still present in Finance.php');
    }

    public function test_bug06_no_error_reporting_in_patient(): void
    {
        $src = $this->readModule('application/modules/patient/controllers/Patient.php');
        $this->assertStringNotContainsString('error_reporting(0)', $src, 'BUG-06: error_reporting(0) still present in Patient.php');
    }

    public function test_bug06_no_error_reporting_in_payroll(): void
    {
        $src = $this->readModule('application/modules/payroll/controllers/Payroll.php');
        $this->assertStringNotContainsString('error_reporting(0)', $src, 'BUG-06: error_reporting(0) still present in Payroll.php');
    }

    public function test_bug06_no_error_reporting_in_prescription(): void
    {
        $src = $this->readModule('application/modules/prescription/controllers/Prescription.php');
        $this->assertStringNotContainsString('error_reporting(0)', $src, 'BUG-06: error_reporting(0) still present in Prescription.php');
    }

    public function test_bug06_no_error_reporting_in_settings(): void
    {
        $src = $this->readModule('application/modules/settings/controllers/Settings.php');
        $this->assertStringNotContainsString('error_reporting(0)', $src, 'BUG-06: error_reporting(0) still present in Settings.php');
    }

    private function readModule(string $relativePath): string
    {
        $full = $this->path($relativePath);
        $this->assertFileExists($full, "Source file not found: {$relativePath}");

        return (string) file_get_contents($full);
    }

    private function path(string $relativePath): string
    {
        $root = dirname(__DIR__, 3);

        return $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
    }
}
