<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper chamber-ui">
    <section class="content-header">
        <div class="chamber-head">
            <div>
                <div class="chamber-kicker">Doctor portal</div>
                <h1>Patient CRM</h1>
                <p class="chamber-subtitle mb-0">Search patient history, tag risk, and send lab referrals.</p>
            </div>
        </div>
    </section>
    <section class="content">
        <form method="get" class="chamber-toolbar">
            <input class="form-control mr-2" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Phone or name">
            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Search</button>
        </form>
        <div class="chamber-panel"><div class="table-responsive">
        <table class="table table-striped chamber-table">
            <thead><tr><th>ID</th><th>Name</th><th>Phone</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($patients as $p) : ?>
                <tr>
                    <td><?php echo (int) $p->id; ?></td>
                    <td><?php echo htmlspecialchars($p->name); ?></td>
                    <td><?php echo htmlspecialchars($p->phone); ?></td>
                    <td>
                        <a class="btn btn-xs btn-default" href="<?php echo site_url('doctor_chamber/consultation_room?patient=' . (int) $p->id); ?>"><i class="fas fa-door-open"></i> Room</a>
                        <form method="post" action="<?php echo site_url('doctor_chamber/tag_patient'); ?>" style="display:inline">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <input type="hidden" name="patient_id" value="<?php echo (int) $p->id; ?>">
                            <input type="hidden" name="redirect_q" value="<?php echo htmlspecialchars($q); ?>">
                            <select name="tag" class="form-control form-control-sm d-inline w-auto">
                                <option value="high_risk">High risk</option>
                                <option value="follow_up">Follow-up</option>
                                <option value="vip">VIP</option>
                            </select>
                            <input name="notes" class="form-control form-control-sm d-inline w-auto" placeholder="note">
                            <button class="btn btn-xs btn-warning" type="submit"><i class="fas fa-tag"></i> Tag</button>
                        </form>
                        <form method="post" action="<?php echo site_url('doctor_chamber/refer_lab'); ?>" style="display:inline" onsubmit="return confirm('Send lab referral SMS?');">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <input type="hidden" name="patient_id" value="<?php echo (int) $p->id; ?>">
                            <input name="lab_name" class="form-control form-control-sm d-inline w-auto" placeholder="Lab name">
                            <button class="btn btn-xs btn-info" type="submit"><i class="fas fa-vial"></i> Refer to lab</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div></div>
    </section>
</div>
