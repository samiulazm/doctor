<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header"><h1>Prescription favorites</h1></section>
    <section class="content">
        <table class="table table-bordered"><thead><tr><th>Label</th><th>JSON</th></tr></thead><tbody>
            <?php foreach ($rows as $r) : ?>
                <tr><td><?php echo htmlspecialchars($r->label); ?></td><td><code class="small"><?php echo htmlspecialchars(substr($r->medicine_lines_json, 0, 200)); ?>…</code></td></tr>
            <?php endforeach; ?>
        </tbody></table>
        <h4>Add favorite</h4>
        <form method="post" action="<?php echo site_url('doctor_chamber/favorite_save'); ?>">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="form-group"><label>Label</label><input class="form-control" name="label" required></div>
            <div class="form-group"><label>Medicine lines JSON</label><textarea class="form-control" name="medicine_lines_json" rows="4" required>[]</textarea></div>
            <button class="btn btn-primary" type="submit">Save</button>
        </form>
    </section>
</div>
