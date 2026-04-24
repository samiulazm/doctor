<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container py-4">
    <h3 class="mb-4">Book your serial</h3>
    <div class="card mb-3">
        <div class="card-body">
            <h5>Mobile verification (OTP)</h5>
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
    <form id="bookingForm" method="post" action="<?php echo site_url('portal/complete_booking'); ?>" enctype="multipart/form-data" class="card" style="<?php echo (empty($verified) || empty($chambers)) ? 'display:none' : ''; ?>">
        <div class="card-body">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="slug" value="<?php echo htmlspecialchars($slug); ?>">
            <h5>Step 1 - Identity</h5>
            <div class="form-row">
                <div class="col-md-4 mb-2"><input class="form-control" name="name" required placeholder="Full name"></div>
                <div class="col-md-2 mb-2"><input class="form-control" name="age" placeholder="Age"></div>
                <div class="col-md-3 mb-2">
                    <select class="form-control" name="gender"><option value="Male">Male</option><option value="Female">Female</option><option value="Other">Other</option></select>
                </div>
                <div class="col-md-3 mb-2"><input class="form-control" readonly value="Advance fee: <?php echo (isset($profile->advance_booking_fee) && (float) $profile->advance_booking_fee > 0) ? htmlspecialchars(number_format((float) $profile->advance_booking_fee, 2) . ' BDT') : 'None'; ?>"></div>
            </div>
            <h5 class="mt-3">Step 2 - Symptoms</h5>
            <div class="form-group">
                <textarea class="form-control" name="symptom" rows="2" placeholder="Problem"></textarea>
            </div>
            <div class="form-group">
                <input class="form-control" name="duration" placeholder="Duration">
            </div>
            <h5 class="mt-3">Step 3 - Attachments (optional)</h5>
            <input type="file" name="attachments[]" class="form-control-file" multiple accept="image/*,.pdf">
            <h5 class="mt-3">Chamber &amp; date</h5>
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
            <button type="submit" class="btn btn-success mt-3">Confirm booking</button>
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
