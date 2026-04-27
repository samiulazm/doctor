<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$advance_fee = (isset($profile->advance_booking_fee) && (float) $profile->advance_booking_fee > 0) ? (float) $profile->advance_booking_fee : 0.0;
$advance_text = $advance_fee > 0 ? number_format($advance_fee, 2) . ' BDT' : 'No advance fee required';
$first_chamber = !empty($chambers) ? $chambers[0] : null;
?>
<div class="container chamber-public-container py-4">
    <div class="chamber-public-head">
        <div>
            <div class="chamber-kicker">Secure booking</div>
            <h1>Book your serial</h1>
            <p class="chamber-subtitle mb-0">Verify your phone, choose a slot, and confirm your visit.</p>
        </div>
        <a class="btn btn-outline-secondary" href="<?php echo site_url('portal/d/' . rawurlencode($slug)); ?>">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
    </div>

    <div class="chamber-stepper chamber-stepper-5">
        <div class="chamber-step active" data-step="1"><strong>1</strong><span>Identity</span></div>
        <div class="chamber-step" data-step="2"><strong>2</strong><span>Chamber</span></div>
        <div class="chamber-step" data-step="3"><strong>3</strong><span>Date & slot</span></div>
        <div class="chamber-step" data-step="4"><strong>4</strong><span>Verify</span></div>
        <div class="chamber-step" data-step="5"><strong>5</strong><span>Confirm</span></div>
    </div>

    <?php if (empty($chambers)) : ?>
        <div class="chamber-public-panel">
            <p class="text-warning small mb-0">Bookings are unavailable until an active chamber is configured for this doctor.</p>
        </div>
    <?php else : ?>
    <form id="bookingForm" method="post" action="<?php echo site_url('portal/complete_booking'); ?>" enctype="multipart/form-data" class="chamber-public-panel">
        <input type="hidden" id="csrfField" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="slug" value="<?php echo htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="slot_time" id="selectedSlot" value="">

        <div class="chamber-step-panel active" data-step="1">
            <div class="chamber-panel-header px-0 pt-0">
                <h2 class="chamber-panel-title"><i class="fas fa-user text-info mr-1"></i> Identity</h2>
            </div>
            <div class="form-row pt-3">
                <div class="col-md-5 mb-3">
                    <label class="font-weight-bold">Full name</label>
                    <input class="form-control" name="name" id="patientName" required placeholder="Full name">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="font-weight-bold">Age</label>
                    <input class="form-control" name="age" id="patientAge" placeholder="Age">
                </div>
                <div class="col-md-5 mb-3">
                    <label class="font-weight-bold d-block">Gender</label>
                    <div class="chamber-radio-group" role="group" aria-label="Gender">
                        <label class="chamber-radio-pill"><input type="radio" name="gender" value="Male" checked><span>Male</span></label>
                        <label class="chamber-radio-pill"><input type="radio" name="gender" value="Female"><span>Female</span></label>
                        <label class="chamber-radio-pill"><input type="radio" name="gender" value="Other"><span>Other</span></label>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold">Problem</label>
                    <textarea class="form-control" name="symptom" id="patientSymptom" rows="2" placeholder="Main concern"></textarea>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="font-weight-bold">Duration</label>
                    <input class="form-control" name="duration" id="patientDuration" placeholder="e.g. 3 days">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="font-weight-bold">Photo or report</label>
                    <input type="file" name="attachments[]" class="form-control-file" multiple accept="image/*,.pdf">
                </div>
            </div>
            <div class="chamber-btn-row justify-content-end">
                <button type="button" class="btn btn-primary js-next" data-next="2">Next</button>
            </div>
        </div>

        <div class="chamber-step-panel" data-step="2">
            <div class="chamber-panel-header px-0 pt-0">
                <h2 class="chamber-panel-title"><i class="fas fa-map-marker-alt text-info mr-1"></i> Chamber</h2>
            </div>
            <div class="form-group pt-3">
                <label class="font-weight-bold">Chamber</label>
                <select class="form-control" name="chamber_id" id="chamberSelect" required>
                    <?php foreach ($chambers as $c) : ?>
                        <option value="<?php echo (int) $c->id; ?>"
                                data-address="<?php echo htmlspecialchars((string) $c->address, ENT_QUOTES, 'UTF-8'); ?>"
                                data-phone="<?php echo htmlspecialchars((string) $c->phone, ENT_QUOTES, 'UTF-8'); ?>"
                                data-hours="<?php echo htmlspecialchars(json_encode(isset($c->weekly_hours) ? $c->weekly_hours : array()), ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($c->name, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div id="selectedChamberInfo" class="chamber-muted small mb-3"></div>
            <div class="chamber-btn-row justify-content-between">
                <button type="button" class="btn btn-outline-secondary js-back" data-prev="1">Back</button>
                <button type="button" class="btn btn-primary js-next" data-next="3">Next</button>
            </div>
        </div>

        <div class="chamber-step-panel" data-step="3">
            <div class="chamber-panel-header px-0 pt-0">
                <h2 class="chamber-panel-title"><i class="fas fa-calendar-alt text-info mr-1"></i> Date & slot</h2>
            </div>
            <div class="form-group pt-3">
                <label class="font-weight-bold">Date</label>
                <input type="date" class="form-control" name="queue_date" id="bookingDate" required value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="chamber-slot-grid" id="slotGrid"></div>
            <div class="chamber-btn-row justify-content-between mt-3">
                <button type="button" class="btn btn-outline-secondary js-back" data-prev="2">Back</button>
                <button type="button" class="btn btn-primary js-next" data-next="4">Next</button>
            </div>
        </div>

        <div class="chamber-step-panel" data-step="4">
            <div class="chamber-panel-header px-0 pt-0">
                <h2 class="chamber-panel-title"><i class="fas fa-mobile-alt text-info mr-1"></i> Verify</h2>
                <?php if (!empty($verified)) : ?>
                    <span class="chamber-status arrived">Verified</span>
                <?php endif; ?>
            </div>
            <div class="pt-3">
                <div class="form-group">
                    <label for="mobile" class="font-weight-bold">Mobile number</label>
                    <input type="tel" id="mobile" class="form-control" name="mobile"
                           inputmode="numeric" pattern="[0-9+\-\s]+" autocomplete="tel"
                           value="<?php echo htmlspecialchars($phone ?: '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="e.g. 01712345678">
                </div>
                <button type="button" id="btnOtp" class="btn btn-outline-primary">Send OTP</button>
                <div id="otpMsg" class="small mt-2 <?php echo !empty($verified) ? 'text-success' : 'text-muted'; ?>">
                    <?php echo !empty($verified) ? 'Verified' : ''; ?>
                </div>
                <div id="otpVerifySection" class="mt-3" hidden>
                    <label for="otp" class="font-weight-bold">Enter 6-digit OTP</label>
                    <input type="text" id="otp" class="form-control chamber-otp-input"
                           inputmode="numeric" maxlength="6" pattern="[0-9]{6}"
                           autocomplete="one-time-code" placeholder="------">
                    <button type="button" id="btnVerify" class="btn btn-success mt-2">Verify OTP</button>
                    <div class="mt-2 small text-muted">
                        <span id="resendTimer">Resend in 60s</span>
                        <a href="#" id="resendLink" hidden>Resend OTP</a>
                    </div>
                </div>
            </div>
            <div class="chamber-btn-row justify-content-between mt-3">
                <button type="button" class="btn btn-outline-secondary js-back" data-prev="3">Back</button>
                <button type="button" class="btn btn-primary js-next" data-next="5">Next</button>
            </div>
        </div>

        <div class="chamber-step-panel" data-step="5">
            <div class="chamber-panel-header px-0 pt-0">
                <h2 class="chamber-panel-title"><i class="fas fa-check-circle text-success mr-1"></i> Confirm</h2>
            </div>
            <div class="chamber-public-panel bg-light shadow-none mt-3">
                <div class="row">
                    <div class="col-md-6 mb-2"><strong>Name:</strong> <span id="sumName">-</span></div>
                    <div class="col-md-6 mb-2"><strong>Doctor:</strong> <?php echo htmlspecialchars($doctor->name, ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="col-md-6 mb-2"><strong>Chamber:</strong> <span id="sumChamber">-</span></div>
                    <div class="col-md-6 mb-2"><strong>Date:</strong> <span id="sumDate">-</span></div>
                    <div class="col-md-6 mb-2"><strong>Slot:</strong> <span id="sumSlot">-</span></div>
                    <div class="col-md-6 mb-2"><strong>Advance fee:</strong> <span id="sumFee"><?php echo htmlspecialchars($advance_text, ENT_QUOTES, 'UTF-8'); ?></span></div>
                </div>
            </div>
            <div class="chamber-btn-row justify-content-between mt-3">
                <button type="button" class="btn btn-outline-secondary js-back" data-prev="4">Back</button>
                <button type="submit" class="btn btn-success btn-lg btn-block">Confirm booking</button>
            </div>
        </div>
    </form>
    <?php endif; ?>
</div>

<script>
(function(){
    var slug = <?php echo json_encode($slug); ?>;
    var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var isVerified = <?php echo !empty($verified) ? 'true' : 'false'; ?>;
    var resendInterval = null;

    function escapeHtml(s) {
        return $('<div>').text(s || '').html();
    }

    function goToStep(n) {
        $('.chamber-step-panel').removeClass('active').hide();
        $('.chamber-step-panel[data-step="' + n + '"]').addClass('active').show();
        $('.chamber-step').removeClass('active');
        $('.chamber-step[data-step="' + n + '"]').addClass('active');
        if (n === 5) {
            updateSummary();
        }
    }

    function showOtpMessage(message, isError) {
        $('#otpMsg').removeClass('text-muted text-danger text-success').addClass(isError ? 'text-danger' : 'text-success').text(message || '');
    }

    function startResendCountdown() {
        var remaining = 60;
        clearInterval(resendInterval);
        $('#resendLink').prop('hidden', true);
        $('#resendTimer').prop('hidden', false).text('Resend in ' + remaining + 's');
        resendInterval = setInterval(function(){
            remaining -= 1;
            if (remaining <= 0) {
                clearInterval(resendInterval);
                $('#resendTimer').prop('hidden', true);
                $('#resendLink').prop('hidden', false);
                return;
            }
            $('#resendTimer').text('Resend in ' + remaining + 's');
        }, 1000);
    }

    function sendOtp() {
        var payload = {
            slug: slug,
            mobile: $('#mobile').val()
        };
        payload[csrfName] = csrfHash;
        $('#btnOtp').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Sending...');
        $.post('<?php echo site_url('portal/request_otp'); ?>', payload, function(r){
            if (r.csrf) { csrfHash = r.csrf; }
            if (r.ok) {
                $('#otpVerifySection').prop('hidden', false);
                showOtpMessage('OTP sent.', false);
                startResendCountdown();
            } else {
                showOtpMessage(r.msg || 'Unable to send OTP.', true);
            }
        }, 'json').always(function(){
            $('#btnOtp').prop('disabled', false).html('Send OTP');
        });
    }

    function verifyOtp() {
        var payload = {
            slug: slug,
            mobile: $('#mobile').val(),
            otp: $('#otp').val()
        };
        payload[csrfName] = csrfHash;
        $('#btnVerify').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Verifying...');
        $.post('<?php echo site_url('portal/verify_otp'); ?>', payload, function(r){
            if (r.csrf) { csrfHash = r.csrf; }
            if (r.ok) {
                isVerified = true;
                $('#otpVerifySection').prop('hidden', true);
                showOtpMessage('Verified', false);
                setTimeout(function(){ goToStep(5); }, 500);
            } else {
                showOtpMessage(r.msg || 'Verification failed.', true);
            }
        }, 'json').always(function(){
            $('#btnVerify').prop('disabled', false).html('Verify OTP');
        });
    }

    function renderChamberInfo() {
        var opt = $('#chamberSelect option:selected');
        var parts = [];
        var address = opt.data('address') || '';
        var phone = opt.data('phone') || '';
        if (address) parts.push('<strong>Address:</strong> ' + escapeHtml(address));
        if (phone) parts.push('<strong>Phone:</strong> ' + escapeHtml(phone));
        var rawHours = opt.attr('data-hours') || '{}';
        try {
            var hours = JSON.parse(rawHours);
            var dayNames = {mon:'Mon', tue:'Tue', wed:'Wed', thu:'Thu', fri:'Fri', sat:'Sat', sun:'Sun'};
            var h = [];
            Object.keys(dayNames).forEach(function(k){
                if (hours[k] && (hours[k].open || hours[k].close)) {
                    h.push(dayNames[k] + ' ' + (hours[k].open || '?') + '-' + (hours[k].close || '?'));
                }
            });
            if (h.length) parts.push('<strong>Hours:</strong> ' + escapeHtml(h.join(', ')));
        } catch(e) {}
        $('#selectedChamberInfo').html(parts.length ? parts.join('<br>') : 'Availability details will appear after the chamber updates its timings.');
    }

    function loadSlots() {
        var chamberId = $('#chamberSelect').val();
        var bookingDate = $('#bookingDate').val();
        $('#selectedSlot').val('');
        if (!chamberId || !bookingDate) return;
        $('#slotGrid').html('<p class="text-muted small">Loading slots...</p>');
        $.getJSON('<?php echo site_url('portal/slots_json'); ?>', {
            chamber_id: chamberId,
            date: bookingDate
        }, function(r) {
            if (!r.ok) {
                $('#slotGrid').html('<p class="text-danger small">' + escapeHtml(r.msg || 'No slots available.') + '</p>');
                return;
            }
            var html = '';
            $.each(r.slots, function(i, s) {
                var cls = s.available ? 'chamber-slot-pill' : 'chamber-slot-pill full';
                html += '<button type="button" class="' + cls + '" data-time="' + escapeHtml(s.time) + '" data-label="' + escapeHtml(s.label) + '">' + escapeHtml(s.label) + '</button>';
            });
            $('#slotGrid').html(html || '<p class="text-danger small">No slots available.</p>');
        });
    }

    function updateSummary() {
        var selectedSlot = $('.chamber-slot-pill.selected');
        $('#sumName').text($('#patientName').val() || '-');
        $('#sumChamber').text($('#chamberSelect option:selected').text() || '-');
        $('#sumDate').text($('#bookingDate').val() || '-');
        $('#sumSlot').text(selectedSlot.data('label') || $('#selectedSlot').val() || '-');
    }

    function validateBeforeStep(next) {
        if (next === 2 && !$('#patientName').val()) {
            $('#patientName').focus();
            return false;
        }
        if (next === 4 && !$('#selectedSlot').val()) {
            $('#slotGrid').prepend('<p class="text-danger small js-slot-error">Please choose a slot.</p>');
            setTimeout(function(){ $('.js-slot-error').fadeOut(160, function(){ $(this).remove(); }); }, 2000);
            return false;
        }
        if (next === 5 && !isVerified) {
            showOtpMessage('Please verify your phone before confirming.', true);
            return false;
        }
        return true;
    }

    $(document).on('click', '.js-next', function(){
        var next = parseInt($(this).data('next'), 10);
        if (validateBeforeStep(next)) {
            goToStep(next);
        }
    });

    $(document).on('click', '.js-back', function(){
        goToStep(parseInt($(this).data('prev'), 10));
    });

    $('#btnOtp').on('click', sendOtp);
    $('#btnVerify').on('click', verifyOtp);
    $('#resendLink').on('click', function(e){
        e.preventDefault();
        sendOtp();
    });
    $('#chamberSelect').on('change', function(){
        renderChamberInfo();
        loadSlots();
    });
    $('#bookingDate').on('change', loadSlots);
    $(document).on('click', '.chamber-slot-pill:not(.full)', function(){
        $('.chamber-slot-pill').removeClass('selected');
        $(this).addClass('selected');
        $('#selectedSlot').val($(this).data('time'));
    });
    $('#bookingForm').on('submit', function(e){
        if (!isVerified) {
            e.preventDefault();
            goToStep(4);
            showOtpMessage('Please verify your phone before confirming.', true);
            return false;
        }
        if (!$('#selectedSlot').val()) {
            e.preventDefault();
            goToStep(3);
            return false;
        }
        $('#csrfField').val(csrfHash);
    });

    renderChamberInfo();
    loadSlots();
    goToStep(1);
})();
</script>
