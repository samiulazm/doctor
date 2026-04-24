<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header"><h1>Schedule exceptions</h1></section>
    <section class="content">
        <p class="mb-3"><a class="btn btn-outline-primary btn-sm" href="<?php echo site_url('doctor_chamber/my_chambers'); ?>">Chamber locations &amp; hours</a> <span class="text-muted small">Per-location weekly open/close times, names, phones, and sort order.</span></p>
        <table class="table"><thead><tr><th>Date</th><th>Closed</th><th>Reason</th></tr></thead><tbody>
            <?php foreach ($rows as $r) : ?>
                <tr><td><?php echo htmlspecialchars($r->exception_date); ?></td><td><?php echo $r->is_closed ? 'Yes' : 'No'; ?></td><td><?php echo htmlspecialchars($r->reason); ?></td></tr>
            <?php endforeach; ?>
        </tbody></table>
        <form method="post" action="<?php echo site_url('doctor_chamber/schedule_save'); ?>">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="form-group"><label>Date</label><input type="date" name="exception_date" class="form-control" required></div>
            <div class="form-check mb-2"><input type="checkbox" name="is_closed" value="1" checked id="cl"><label for="cl"> Chamber closed</label></div>
            <div class="form-group"><label>Reason</label><input name="reason" class="form-control"></div>
            <button class="btn btn-primary" type="submit">Save</button>
        </form>
    </section>
</div>
