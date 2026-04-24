<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper chamber-ui">
    <section class="content-header">
        <div class="chamber-head">
            <div>
                <div class="chamber-kicker">Patient portal</div>
                <h1>My prescriptions</h1>
                <p class="chamber-subtitle mb-0">Your saved digital prescriptions in PDF/print format.</p>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="chamber-panel">
            <div class="table-responsive">
        <table class="table table-striped chamber-table">
            <thead><tr><th>Date</th><th>Doctor</th><th>Options</th></tr></thead>
            <tbody>
            <?php foreach ($prescriptions as $rx) : ?>
                <tr>
                    <td><?php echo date('d-m-Y', $rx->date); ?></td>
                    <td><?php echo htmlspecialchars($rx->doctorname); ?></td>
                    <td>
                        <a class="btn btn-xs btn-primary" target="_blank" href="<?php echo site_url('prescription/viewPrescriptionPrint?id=' . (int) $rx->id); ?>"><i class="fas fa-file-pdf"></i> Print / PDF</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
            </div>
        </div>
    </section>
</div>
