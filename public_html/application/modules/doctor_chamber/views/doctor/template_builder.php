<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header"><h1>Prescription print template</h1></section>
    <section class="content">
        <form method="post" action="<?php echo site_url('doctor_chamber/template_save'); ?>">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="form-group"><label>Header HTML</label><textarea name="header_html" class="form-control" rows="6"><?php echo $tpl ? htmlspecialchars($tpl->header_html) : ''; ?></textarea></div>
            <div class="form-group"><label>Footer HTML</label><textarea name="footer_html" class="form-control" rows="6"><?php echo $tpl ? htmlspecialchars($tpl->footer_html) : ''; ?></textarea></div>
            <button class="btn btn-primary" type="submit">Save</button>
        </form>
    </section>
</div>
