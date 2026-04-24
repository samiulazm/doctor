<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper chamber-ui">
    <section class="content-header">
        <div class="chamber-head">
            <div>
                <div class="chamber-kicker">Doctor portal</div>
                <h1>Prescription favorites</h1>
                <p class="chamber-subtitle mb-0">Reusable medicine lines for faster prescribing.</p>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="chamber-panel"><div class="table-responsive">
        <table class="table chamber-table"><thead><tr><th>Label</th><th>JSON</th></tr></thead><tbody>
            <?php foreach ($rows as $r) : ?>
                <tr><td><?php echo htmlspecialchars($r->label); ?></td><td><code class="small"><?php echo htmlspecialchars(substr($r->medicine_lines_json, 0, 200)); ?>...</code></td></tr>
            <?php endforeach; ?>
        </tbody></table>
        </div></div>
        <form method="post" action="<?php echo site_url('doctor_chamber/favorite_save'); ?>" class="chamber-panel">
            <div class="chamber-panel-header"><h3 class="chamber-panel-title">Add favorite</h3></div>
            <div class="chamber-panel-body">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="form-group"><label>Label</label><input class="form-control" name="label" required></div>
            <div class="form-group"><label>Medicine lines JSON</label><textarea class="form-control" name="medicine_lines_json" rows="4" required>[]</textarea></div>
            <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Save</button>
            </div>
        </form>
    </section>
</div>
