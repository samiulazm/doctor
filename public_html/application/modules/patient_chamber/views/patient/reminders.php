<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper chamber-ui">
    <section class="content-header">
        <div class="chamber-head">
            <div>
                <div class="chamber-kicker">Patient portal</div>
                <h1>Medicine reminders</h1>
                <p class="chamber-subtitle mb-0">SMS dose timing reminders for active medicines.</p>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="chamber-panel">
            <div class="table-responsive">
        <table class="table chamber-table"><thead><tr><th>Medicine</th><th>Times</th></tr></thead><tbody>
            <?php foreach ($rows as $r) : ?>
                <tr><td><?php echo htmlspecialchars($r->medicine_label); ?></td><td><code><?php echo htmlspecialchars($r->dose_times_json); ?></code></td></tr>
            <?php endforeach; ?>
        </tbody></table>
            </div>
        </div>
        <div class="chamber-panel">
            <div class="chamber-panel-header"><h3 class="chamber-panel-title">Add reminder</h3></div>
            <div class="chamber-panel-body">
        <form method="post" action="<?php echo site_url('patient_chamber/reminder_save'); ?>">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="form-group"><label>Medicine</label><input name="medicine_label" class="form-control" required></div>
            <div class="form-group"><label>Dose times (comma separated, 24h)</label><input name="dose_times" class="form-control" placeholder="08:00,20:00"></div>
            <button class="btn btn-primary" type="submit"><i class="fas fa-bell"></i> Save</button>
        </form>
            </div>
        </div>
    </section>
</div>
