<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header"><h1>Diagnostics &amp; lab vault</h1></section>
    <section class="content">
        <p class="text-muted">Partner referrals from your doctor, your hospital lab work, and the full lab portal in one place.</p>
        <p><a class="btn btn-primary" href="<?php echo site_url('lab/myLab'); ?>">Open main lab module</a> (invoices, downloads, detailed workflow)</p>

        <h5 class="mt-4">Chamber partner referrals</h5>
        <?php if (!empty($referrals)) : ?>
        <table class="table table-bordered table-sm">
            <thead><tr><th>Date</th><th>Lab</th><th>Discount code</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($referrals as $r) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($r->created_at); ?></td>
                    <td><?php echo htmlspecialchars((string) $r->lab_name); ?></td>
                    <td><code><?php echo htmlspecialchars($r->discount_code); ?></code></td>
                    <td><?php echo htmlspecialchars($r->status); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else : ?>
            <p class="text-muted small">No partner-lab discount codes from your doctors yet.</p>
        <?php endif; ?>

        <h5 class="mt-4">Hospital lab reports (this account)</h5>
        <?php if (!empty($lab_reports)) : ?>
        <table class="table table-bordered table-sm">
            <thead><tr><th>Record ID</th><th>Date</th><th>Status</th><th>Invoice</th></tr></thead>
            <tbody>
            <?php foreach ($lab_reports as $lb) : ?>
                <tr>
                    <td><?php echo (int) $lb->id; ?></td>
                    <td><?php echo htmlspecialchars((string) $lb->date); ?></td>
                    <td><?php echo htmlspecialchars((string) $lb->status); ?></td>
                    <td><?php echo isset($lb->invoice_id) ? htmlspecialchars((string) $lb->invoice_id) : '—'; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p class="small text-muted">Open <strong>My lab</strong> above to view PDFs, pay, and full report details for these rows.</p>
        <?php else : ?>
            <p class="text-muted small">No lab records linked to this patient profile yet.</p>
        <?php endif; ?>

        <?php if (!empty($ot_lab_reports)) : ?>
        <h5 class="mt-4">OT / procedure labs</h5>
        <table class="table table-bordered table-sm">
            <thead><tr><th>ID</th><th>Date</th></tr></thead>
            <tbody>
            <?php foreach ($ot_lab_reports as $lb) : ?>
                <tr>
                    <td><?php echo (int) $lb->id; ?></td>
                    <td><?php echo htmlspecialchars((string) $lb->date); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </section>
</div>
