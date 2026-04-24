<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header"><h1>Patient CRM</h1></section>
    <section class="content">
        <form method="get" class="form-inline mb-3">
            <input class="form-control mr-2" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Phone or name">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>
        <table class="table table-striped">
            <thead><tr><th>ID</th><th>Name</th><th>Phone</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($patients as $p) : ?>
                <tr>
                    <td><?php echo (int) $p->id; ?></td>
                    <td><?php echo htmlspecialchars($p->name); ?></td>
                    <td><?php echo htmlspecialchars($p->phone); ?></td>
                    <td>
                        <a class="btn btn-xs btn-default" href="<?php echo site_url('doctor_chamber/consultation_room?patient=' . (int) $p->id); ?>">Room</a>
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
                            <button class="btn btn-xs btn-warning" type="submit">Tag</button>
                        </form>
                        <form method="post" action="<?php echo site_url('doctor_chamber/refer_lab'); ?>" style="display:inline" onsubmit="return confirm('Send lab referral SMS?');">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <input type="hidden" name="patient_id" value="<?php echo (int) $p->id; ?>">
                            <input name="lab_name" class="form-control form-control-sm d-inline w-auto" placeholder="Lab name">
                            <button class="btn btn-xs btn-info" type="submit">Refer to lab</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>
