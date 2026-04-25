<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container chamber-public-container py-4">
    <div class="chamber-public-head">
        <div>
            <div class="chamber-kicker">Secure booking</div>
            <h1>Book your serial</h1>
            <p class="chamber-subtitle mb-0">Verify your phone, add visit details, and choose a chamber date.</p>
        </div>
        <a class="btn btn-outline-secondary" href="<?php echo site_url('portal/d/' . rawurlencode($slug)); ?>">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
    </div>
    <div class="chamber-stepper">
        <div class="chamber-step active"><strong>1. Identity</strong><span>Name, age, gender</span></div>
        <div class="chamber-step active"><strong>2. Symptoms</strong><span>Problem and duration</span></div>
        <div class="chamber-step active"><strong>3. Attachments</strong><span>Reports or previous Rx</span></div>
    </div>
    <div class="chamber-public-panel">
        <div class="chamber-panel-header px-0 pt-0">
            <h2 class="chamber-panel-title"><i class="fas fa-mobile-alt text-info mr-1"></i> Mobile verification</h2>
            <?php if (!empty($verified)) : ?>
                <span class="chamber-status done">Verified</span>
            <?php endif; ?>
        </div>
        <div class="pt-3">
            <?php if (!empty($verified)) : ?>
            <p>Verified number: <strong><?php echo htmlspecialchars($phone); ?></strong></p>
            <?php else : ?>
            <p class="text-muted">Enter your mobile number and verify with OTP before continuing.</p>
            <?php endif; ?>
            <div class="form-row">
                <div class="col-md-4 mb-2">
                    <input type="text" id="mobile" class="form-control" value="<?php echo htmlspecialchars($phone ?: ''); ?>" placeholder="Mobile">
                </div>
                <div class="col-md-4 mb-2">
                    <button type="button" id="btnOtp" class="btn btn-outline-primary">Send OTP</button>
                </div>
                <div class="col-md-4 mb-2">
                    <input type="text" id="otp" class="form-control" placeholder="Enter OTP">
                    <button type="button" id="btnVerify" class="btn btn-sm btn-primary mt-1">Verify</button>
                </div>
            </div>
            <div id="otpMsg" class="small text-muted"></div>
            <?php if (empty($chambers)) : ?>
            <p class="text-warning small mb-0 mt-2">Bookings are unavailable until an active chamber is configured for this doctor.</p>
            <?php endif; ?>
        </div>
    </div>
    <form id="bookingForm" method="post" action="<?php echo site_url('portal/complete_booking'); ?>" enctype="multipart/form-data" class="chamber-public-panel" style="<?php echo (empty($verified) || empty($chambers)) ? 'display:none' : ''; ?>">
        <div>
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="slug" value="<?php echo htmlspecialchars($slug); ?>">
            <h2 class="chamber-panel-title mb-3">Step 1 - Identity</h2>
            <div class="form-row">
                <div class="col-md-4 mb-2"><input class="form-control" name="name" required placeholder="Full name"></div>
                <div class="col-md-2 mb-2"><input class="form-control" name="age" placeholder="Age"></div>
                <div class="col-md-3 mb-2">
                    <div class="chamber-radio-group" role="group" aria-label="Gender">
                        <label class="chamber-radio-pill"><input type="radio" name="gender" value="Male" checked><span>Male</span></label>
                        <label class="chamber-radio-pill"><input type="radio" name="gender" value="Female"><span>Female</span></label>
                        <label class="chamber-radio-pill"><input type="radio" name="gender" value="Other"><span>Other</span></label>
                    </div>
                </div>
                <div class="col-md-3 mb-2"><input class="form-control" readonly value="Advance fee: <?php echo (isset($profile->advance_booking_fee) && (float) $profile->advance_booking_fee > 0) ? htmlspecialchars(number_format((float) $profile->advance_booking_fee, 2) . ' BDT') : 'None'; ?>"></div>
            </div>
            <h2 class="chamber-panel-title mt-4 mb-3">Step 2 - Symptoms</h2>
            <div class="form-group">
                <textarea class="form-control" name="symptom" rows="2" placeholder="Problem"></textarea>
            </div>
            <div class="form-group">
                <input class="form-control" name="duration" placeholder="Duration">
            </div>
            <h2 class="chamber-panel-title mt-4 mb-3">Step 3 - Attachments</h2>
            <label class="chamber-upload-zone w-100">
                <i class="fas fa-cloud-upload-alt"></i>
                <span><strong>Upload reports or previous prescriptions</strong><br><small>Images and PDF files are accepted.</small></span>
                <input type="file" name="attachments[]" class="form-control-file" multiple accept="image/*,.pdf">
            </label>
            <h2 class="chamber-panel-title mt-4 mb-3">Chamber and date</h2>
            <div class="form-row">
                <div class="col-md-6 mb-2">
                    <select class="form-control" name="chamber_id" required>
                        <?php foreach ($chambers as $c) : ?>
                            <option value="<?php echo (int) $c->id; ?>"><?php echo htmlspecialchars($c->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-2">
                    <input type="date" class="form-control" name="queue_date" required value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-success mt-3"><i class="fas fa-check-circle mr-1"></i> Confirm booking</button>
        </div>
    </form>
</div>
<script>
(function(){
    var slug = <?php echo json_encode($slug); ?>;
    $('#btnOtp').on('click', function(){
        $.post('<?php echo site_url('portal/request_otp'); ?>', {
            <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>',
            slug: slug, mobile: $('#mobile').val()
        }, function(r){ $('#otpMsg').text(r.ok ? 'OTP sent.' : (r.msg||'Error')); }, 'json');
    });
    $('#btnVerify').on('click', function(){
        $.post('<?php echo site_url('portal/verify_otp'); ?>', {
            <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>',
            slug: slug, mobile: $('#mobile').val(), otp: $('#otp').val()
        }, function(r){
            $('#otpMsg').text(r.ok ? 'Verified. You can complete the form below.' : (r.msg||'Failed'));
            if (r.ok) { $('#bookingForm').show(); }
        }, 'json');
    });
})();
</script>
