<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper chamber-ui">
    <section class="content-header">
        <div class="chamber-head">
            <div>
                <div class="chamber-kicker">Doctor portal</div>
                <h1>Public landing</h1>
                <p class="chamber-subtitle mb-0">Control the patient-facing booking page, photo, signature, and advance fee.</p>
            </div>
            <?php if ($profile && !empty($profile->public_slug)) : ?>
            <a class="btn btn-outline-primary" target="_blank" href="<?php echo site_url('portal/d/' . rawurlencode($profile->public_slug)); ?>"><i class="fas fa-external-link-alt"></i> View page</a>
            <?php endif; ?>
        </div>
    </section>
    <section class="content">
        <div class="chamber-panel">
            <div class="chamber-panel-body">
        <p>Patient URL: <code><?php echo site_url('portal/d/' . ($profile ? rawurlencode($profile->public_slug) : 'your-slug')); ?></code></p>
        <form method="post" action="<?php echo site_url('doctor_chamber/portal_profile_save'); ?>">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="form-group"><label>URL slug</label><input class="form-control" name="public_slug" required value="<?php echo $profile ? htmlspecialchars($profile->public_slug) : ''; ?>" placeholder="e.g. dr-karim"></div>
            <div class="form-group"><label>Specialty line</label><input class="form-control" name="specialty_label" value="<?php echo $profile ? htmlspecialchars($profile->specialty_label) : ''; ?>"></div>
            <div class="form-group"><label>Hero image path (uploads/...)</label><input class="form-control" name="hero_image" value="<?php echo $profile ? htmlspecialchars($profile->hero_image) : ''; ?>" placeholder="uploads/doctor.jpg"></div>
            <div class="form-group"><label>Signature image path (for printed Rx)</label><input class="form-control" name="signature_image" value="<?php echo $profile && !empty($profile->signature_image) ? htmlspecialchars($profile->signature_image) : ''; ?>" placeholder="uploads/signatures/dr.png"></div>
            <div class="form-group"><label>Advance booking fee</label><input class="form-control" type="number" min="0" step="0.01" name="advance_booking_fee" value="<?php echo $profile && isset($profile->advance_booking_fee) ? htmlspecialchars((string) $profile->advance_booking_fee) : '0.00'; ?>" placeholder="0.00"></div>
            <div class="form-check mb-2"><input type="checkbox" name="booking_enabled" value="1" id="be" <?php echo ($profile && $profile->booking_enabled) ? 'checked' : 'checked'; ?>><label for="be"> Booking enabled</label></div>
            <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Save</button>
        </form>
            </div>
        </div>
    </section>
</div>
