<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header"><h1>Chamber SaaS — subscriptions</h1></section>
    <section class="content">
        <h4>Record doctor subscription payment</h4>
        <form method="post" action="<?php echo site_url('saas_platform/subscription_save'); ?>" class="form-inline mb-4">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input class="form-control mr-2" name="hospital_id" placeholder="Hospital ID" required>
            <input class="form-control mr-2" name="doctor_id" placeholder="Doctor ID" required>
            <select name="plan_id" class="form-control mr-2">
                <?php foreach ($plans as $p) : ?>
                    <option value="<?php echo (int) $p->id; ?>"><?php echo htmlspecialchars($p->name); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="starts_at" class="form-control mr-2" value="<?php echo date('Y-m-d'); ?>">
            <button class="btn btn-primary" type="submit">Save</button>
        </form>
        <table class="table table-bordered table-sm">
            <thead><tr><th>Doctor</th><th>Plan</th><th>Status</th><th>Ends</th></tr></thead>
            <tbody>
            <?php foreach ($subscriptions as $s) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($s->doctor_name); ?></td>
                    <td><?php echo htmlspecialchars($s->plan_name); ?></td>
                    <td><?php echo htmlspecialchars($s->status); ?></td>
                    <td><?php echo htmlspecialchars($s->ends_at); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <h4 class="mt-4">Referral loop (lab)</h4>
        <table class="table table-sm">
            <thead><tr><th>ID</th><th>Code</th><th>Patient</th><th>Status</th><th>Mark report ready</th></tr></thead>
            <tbody>
            <?php foreach ($referrals as $ref) : ?>
                <tr>
                    <td><?php echo (int) $ref->id; ?></td>
                    <td><code><?php echo htmlspecialchars($ref->discount_code); ?></code></td>
                    <td><?php echo (int) $ref->patient_id; ?></td>
                    <td><?php echo htmlspecialchars($ref->status); ?></td>
                    <td>
                        <?php if ($ref->status !== 'report_ready') : ?>
                        <form method="post" action="<?php echo site_url('saas_platform/lab_referral_ready'); ?>" style="display:inline">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <input type="hidden" name="referral_id" value="<?php echo (int) $ref->id; ?>">
                            <button class="btn btn-xs btn-success" type="submit">Report ready</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p><a href="<?php echo site_url('saas_platform/sms_credits'); ?>">SMS credits</a> | <a href="<?php echo site_url('saas_platform/analytics'); ?>">Usage analytics</a></p>
    </section>
</div>
