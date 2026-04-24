<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper chamber-ui">
    <section class="content-header">
        <div class="chamber-head">
            <div>
                <div class="chamber-kicker">Patient portal</div>
                <h1>Diagnostics &amp; lab vault</h1>
                <p class="chamber-subtitle mb-0">Reports, referral codes, and lab records linked to your profile.</p>
            </div>
            <a class="btn btn-primary" href="<?php echo site_url('lab/myLab'); ?>"><i class="fas fa-vials"></i> Open main lab module</a>
        </div>
    </section>
    <section class="content">
        <div class="chamber-panel">
            <div class="chamber-panel-header"><h3 class="chamber-panel-title">Chamber partner referrals</h3></div>
            <div class="chamber-panel-body">
        <?php if (!empty($referrals)) : ?>
        <div class="table-responsive"><table class="table table-sm chamber-table">
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
        </table></div>
        <?php else : ?>
            <p class="text-muted small">No partner-lab discount codes from your doctors yet.</p>
        <?php endif; ?>
            </div>
        </div>
        <div class="chamber-panel">
            <div class="chamber-panel-header"><h3 class="chamber-panel-title">Hospital lab reports</h3></div>
            <div class="chamber-panel-body">
        <?php if (!empty($lab_reports)) : ?>
        <div class="table-responsive"><table class="table table-sm chamber-table">
            <thead><tr><th>Record ID</th><th>Date</th><th>Status</th><th>Invoice</th></tr></thead>
            <tbody>
            <?php foreach ($lab_reports as $lb) : ?>
                <tr>
                    <td><?php echo (int) $lb->id; ?></td>
                    <td><?php echo htmlspecialchars((string) $lb->date); ?></td>
                    <td><?php echo htmlspecialchars((string) $lb->status); ?></td>
                    <td><?php echo isset($lb->invoice_id) ? htmlspecialchars((string) $lb->invoice_id) : '-'; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
        <p class="small text-muted">Open <strong>My lab</strong> above to view PDFs, pay, and full report details for these rows.</p>
        <?php else : ?>
            <p class="text-muted small">No lab records linked to this patient profile yet.</p>
        <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($ot_lab_reports)) : ?>
        <div class="chamber-panel">
            <div class="chamber-panel-header"><h3 class="chamber-panel-title">OT / procedure labs</h3></div>
            <div class="table-responsive"><table class="table table-sm chamber-table">
            <thead><tr><th>ID</th><th>Date</th></tr></thead>
            <tbody>
            <?php foreach ($ot_lab_reports as $lb) : ?>
                <tr>
                    <td><?php echo (int) $lb->id; ?></td>
                    <td><?php echo htmlspecialchars((string) $lb->date); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
        </div>
        <?php endif; ?>
    </section>
</div>
