<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="chamber-ui">
    <section class="content-header">
        <div class="chamber-head">
            <div>
                <div class="chamber-kicker">Doctor portal</div>
                <h1>Prescription print template</h1>
                <p class="chamber-subtitle mb-0">Customize printed prescription header and footer.</p>
            </div>
        </div>
    </section>
    <section class="content">
        <form method="post" action="<?php echo site_url('doctor_chamber/template_save'); ?>" class="chamber-panel">
            <div class="chamber-panel-body">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="form-group"><label>Header HTML</label><textarea name="header_html" class="form-control" rows="6"><?php echo $tpl ? htmlspecialchars($tpl->header_html) : ''; ?></textarea></div>
            <div class="form-group"><label>Footer HTML</label><textarea name="footer_html" class="form-control" rows="6"><?php echo $tpl ? htmlspecialchars($tpl->footer_html) : ''; ?></textarea></div>
            <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Save</button>
            </div>
        </form>
    </section>
</div>
