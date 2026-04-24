<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header"><h1>SMS credit ledger</h1></section>
    <section class="content">
        <h4 class="mt-0">Latest balance by doctor</h4>
        <p class="text-muted small">One row per doctor/hospital from the most recent ledger entry that carries a <code>doctor_id</code> (hospital-wide rows are in the table below).</p>
        <table class="table table-sm table-bordered mb-4" style="max-width:800px">
            <thead><tr><th>Hospital</th><th>Doctor</th><th>Balance</th><th>As of</th></tr></thead>
            <tbody>
            <?php foreach ($sms_by_doctor as $r) : ?>
                <tr>
                    <td><?php echo (int) $r->hospital_id; ?></td>
                    <td><?php echo htmlspecialchars($r->doctor_name); ?> <span class="text-muted">(#<?php echo (int) $r->doctor_id; ?>)</span></td>
                    <td><?php echo (int) $r->balance_after; ?></td>
                    <td><?php echo htmlspecialchars($r->created_at); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($sms_by_doctor)) : ?>
                <tr><td colspan="4" class="text-muted">No doctor-scoped SMS ledger rows yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        <h4>Hospital ledger (latest 300)</h4>
        <form method="post" action="<?php echo site_url('saas_platform/sms_credit_add'); ?>" class="form-inline mb-3">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input class="form-control mr-2" name="hospital_id" placeholder="Hospital ID" required>
            <input class="form-control mr-2" name="delta" placeholder="Credits +/- " type="number" required>
            <button class="btn btn-primary" type="submit">Apply</button>
        </form>
        <table class="table table-sm"><thead><tr><th>When</th><th>Hospital</th><th>Doctor</th><th>Delta</th><th>Balance</th><th>Reason</th></tr></thead><tbody>
            <?php foreach ($rows as $r) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($r->created_at); ?></td>
                    <td><?php echo (int) $r->hospital_id; ?></td>
                    <td><?php echo $r->doctor_id ? (int) $r->doctor_id : '—'; ?></td>
                    <td><?php echo (int) $r->delta; ?></td>
                    <td><?php echo (int) $r->balance_after; ?></td>
                    <td><?php echo htmlspecialchars($r->reason); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody></table>
    </section>
</div>
