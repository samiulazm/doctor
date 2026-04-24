<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header"><h1>Medicine reminders</h1></section>
    <section class="content">
        <table class="table"><thead><tr><th>Medicine</th><th>Times (JSON)</th></tr></thead><tbody>
            <?php foreach ($rows as $r) : ?>
                <tr><td><?php echo htmlspecialchars($r->medicine_label); ?></td><td><code><?php echo htmlspecialchars($r->dose_times_json); ?></code></td></tr>
            <?php endforeach; ?>
        </tbody></table>
        <h4>Add reminder</h4>
        <form method="post" action="<?php echo site_url('patient_chamber/reminder_save'); ?>">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="form-group"><label>Medicine</label><input name="medicine_label" class="form-control" required></div>
            <div class="form-group"><label>Dose times (comma separated, 24h)</label><input name="dose_times" class="form-control" placeholder="08:00,20:00"></div>
            <button class="btn btn-primary" type="submit">Save</button>
        </form>
    </section>
</div>
