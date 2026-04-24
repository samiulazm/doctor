<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header"><h1>My prescriptions (PDF)</h1></section>
    <section class="content">
        <table class="table table-striped">
            <thead><tr><th>Date</th><th>Doctor</th><th>Options</th></tr></thead>
            <tbody>
            <?php foreach ($prescriptions as $rx) : ?>
                <tr>
                    <td><?php echo date('d-m-Y', $rx->date); ?></td>
                    <td><?php echo htmlspecialchars($rx->doctorname); ?></td>
                    <td>
                        <a class="btn btn-xs btn-primary" target="_blank" href="<?php echo site_url('prescription/viewPrescriptionPrint?id=' . (int) $rx->id); ?>">Print / PDF</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>
