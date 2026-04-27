<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$img = !empty($profile->hero_image) ? base_url($profile->hero_image) : base_url('uploads/default-image.png');
$fee = (isset($profile->advance_booking_fee) && (float) $profile->advance_booking_fee > 0) ? number_format((float) $profile->advance_booking_fee, 2) . ' BDT' : 'No advance fee';
$verified = !empty($verified) ? $verified : '';
$upcoming = !empty($upcoming) ? $upcoming : null;
$prescriptions = !empty($prescriptions) ? $prescriptions : array();
$lab_reports = !empty($lab_reports) ? $lab_reports : array();
?>
<div class="container chamber-public-container py-3">
    <div class="chamber-public-hero">
        <img class="chamber-public-hero-bg" src="<?php echo htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>" alt="">
        <div class="chamber-public-hero-content">
            <div class="chamber-kicker">Doctor serial booking</div>
            <h1><?php echo htmlspecialchars($doctor->name, ENT_QUOTES, 'UTF-8'); ?></h1>
            <p class="lead mb-0"><?php echo htmlspecialchars($profile->specialty_label ?: $doctor->department_name, ENT_QUOTES, 'UTF-8'); ?></p>
            <div class="chamber-public-hero-meta">
                <span class="chamber-public-pill"><i class="fas fa-stethoscope"></i> <?php echo htmlspecialchars($profile->specialty_label ?: $doctor->department_name, ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="chamber-public-pill"><i class="fas fa-credit-card"></i> <?php echo htmlspecialchars($fee, ENT_QUOTES, 'UTF-8'); ?></span>
                <?php if (!empty($verified)) : ?>
                    <span class="chamber-public-pill"><i class="fas fa-check-circle"></i> Verified</span>
                <?php endif; ?>
            </div>
            <a class="btn btn-warning btn-lg font-weight-bold" href="<?php echo site_url('portal/triage/' . rawurlencode($slug)); ?>">
                <i class="fas fa-calendar-check mr-1"></i> Book serial now
            </a>
        </div>
    </div>

    <?php if (empty($verified)) : ?>
    <div class="chamber-public-panel mt-3" id="otpLoginPanel">
        <h2 class="chamber-panel-title mb-3">
            <i class="fas fa-mobile-alt mr-1 text-primary"></i> Verify your phone to access bookings
        </h2>
        <div class="form-group">
            <label for="otpMobile" class="font-weight-bold">Mobile number</label>
            <input type="tel" id="otpMobile" class="form-control"
                   inputmode="numeric" pattern="[0-9+\-\s]+"
                   autocomplete="tel" placeholder="e.g. 01712345678">
        </div>
        <button type="button" id="btnSendOtp" class="btn btn-primary btn-block">Send OTP</button>
        <div id="otpMsg" class="mt-2 small"></div>

        <div id="otpVerifySection" class="mt-3" hidden>
            <div class="form-group">
                <label for="otpInput" class="font-weight-bold">Enter 6-digit OTP</label>
                <input type="text" id="otpInput" class="form-control chamber-otp-input"
                       inputmode="numeric" maxlength="6" pattern="[0-9]{6}"
                       autocomplete="one-time-code" placeholder="------">
            </div>
            <button type="button" id="btnVerifyOtp" class="btn btn-success btn-block">Verify OTP</button>
            <div class="mt-2 small text-muted">
                <span id="resendTimer">Resend in 60s</span>
                <a href="#" id="resendLink" hidden>Resend OTP</a>
            </div>
        </div>

        <div id="otpVerifiedBadge" class="mt-3 text-center" hidden>
            <span class="chamber-status arrived">Verified</span>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($chambers)) : ?>
    <div class="chamber-public-grid">
        <div class="chamber-public-panel">
            <div class="chamber-panel-header px-0 pt-0">
                <h2 class="chamber-panel-title"><i class="fas fa-map-marker-alt text-info mr-1"></i> Chamber availability</h2>
            </div>
            <div class="form-row mt-3">
                <div class="col-md-7 mb-3">
                    <label>Chamber</label>
                    <select id="chamberSel" class="form-control">
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
                <div class="col-md-5 mb-3">
                    <label>Date</label>
                    <input type="date" id="qdate" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            <div id="chamberInfo" class="chamber-muted small"></div>
        </div>
        <div class="chamber-public-panel">
            <h2 class="chamber-panel-title mb-3"><i class="fas fa-broadcast-tower text-warning mr-1"></i> Live queue</h2>
            <div id="ticker" class="chamber-ticker queue-ticker"><span class="dot-pulse"></span><span>Current serial: -</span></div>
            <p class="chamber-muted small mt-3 mb-0">Queue status refreshes automatically while the chamber desk updates the current serial.</p>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($verified)) : ?>
    <div class="chamber-public-panel upcoming-card" id="upcomingAppointment">
        <div class="chamber-panel-header px-0 pt-0">
            <h2 class="chamber-panel-title">
                <i class="fas fa-calendar-check text-success mr-1"></i> Upcoming appointment
            </h2>
        </div>
        <div class="pt-3">
            <?php if (!empty($upcoming)) : ?>
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <div>
                        <p class="mb-1"><strong><?php echo htmlspecialchars(date('d M Y', strtotime($upcoming->queue_date)), ENT_QUOTES, 'UTF-8'); ?></strong></p>
                        <p class="chamber-muted mb-1">
                            Serial #<?php echo (int) $upcoming->serial_number; ?>
                            <?php if (!empty($upcoming->chamber_name)) : ?>
                                &middot; <?php echo htmlspecialchars($upcoming->chamber_name, ENT_QUOTES, 'UTF-8'); ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="chamber-btn-row">
                        <span class="chamber-status <?php echo htmlspecialchars((string) $upcoming->status, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars((string) $upcoming->status, ENT_QUOTES, 'UTF-8'); ?></span>
                        <a href="<?php echo site_url('portal/queue/' . (int) $upcoming->queue_id); ?>" class="btn btn-sm btn-outline-primary">View queue status</a>
                    </div>
                </div>
            <?php else : ?>
                <p class="chamber-muted mb-0">No upcoming appointments.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="chamber-public-panel" id="myPrescriptions">
        <div class="chamber-panel-header px-0 pt-0">
            <h2 class="chamber-panel-title">
                <i class="fas fa-file-medical text-info mr-1"></i> My prescriptions
            </h2>
        </div>
        <div class="pt-3">
            <?php if (!empty($prescriptions)) : ?>
                <?php foreach ($prescriptions as $rx) : ?>
                    <div class="d-flex flex-wrap align-items-center justify-content-between border-bottom py-2">
                        <div>
                            <strong><?php echo htmlspecialchars(!empty($rx->doctor_name) ? $rx->doctor_name : 'Doctor', ENT_QUOTES, 'UTF-8'); ?></strong>
                            <div class="chamber-muted small">
                                <?php echo !empty($rx->date) ? htmlspecialchars(date('d M Y', (int) $rx->date), ENT_QUOTES, 'UTF-8') : 'Date not specified'; ?>
                            </div>
                        </div>
                        <a href="<?php echo site_url('portal/prescription/' . (int) $rx->id); ?>" class="btn btn-sm btn-primary">View</a>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="chamber-muted mb-0">No prescriptions yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="chamber-public-panel" id="myLabReports">
        <div class="chamber-panel-header px-0 pt-0">
            <h2 class="chamber-panel-title">
                <i class="fas fa-flask text-warning mr-1"></i> Lab reports
            </h2>
        </div>
        <div class="pt-3">
            <?php if (!empty($lab_reports)) : ?>
                <?php foreach ($lab_reports as $lab) : ?>
                    <?php $lab_name = !empty($lab->category_name) ? $lab->category_name : (!empty($lab->payment_category) ? $lab->payment_category : 'Lab report #' . (int) $lab->id); ?>
                    <div class="d-flex flex-wrap align-items-center justify-content-between border-bottom py-2">
                        <div>
                            <strong><?php echo htmlspecialchars($lab_name, ENT_QUOTES, 'UTF-8'); ?></strong>
                            <div class="chamber-muted small">
                                <?php echo !empty($lab->date_string) ? htmlspecialchars($lab->date_string, ENT_QUOTES, 'UTF-8') : (!empty($lab->date) ? htmlspecialchars(date('d M Y', (int) $lab->date), ENT_QUOTES, 'UTF-8') : 'Date not specified'); ?>
                            </div>
                        </div>
                        <a href="<?php echo site_url('lab/invoice?id=' . (int) $lab->id); ?>" class="btn btn-sm btn-outline-primary">View</a>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="chamber-muted mb-0">No lab reports yet.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
(function(){
    var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var slug = <?php echo json_encode($slug); ?>;
    var resendInterval = null;

    function escapeHtml(s){
        return $('<div>').text(s || '').html();
    }

    function showOtpMessage(message, isError) {
        $('#otpMsg').removeClass('text-danger text-success').addClass(isError ? 'text-danger' : 'text-success').text(message || '');
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
            mobile: $('#otpMobile').val()
        };
        payload[csrfName] = csrfHash;
        $('#btnSendOtp').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Sending...');
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
            $('#btnSendOtp').prop('disabled', false).html('Send OTP');
        });
    }

    function verifyOtp() {
        var payload = {
            slug: slug,
            mobile: $('#otpMobile').val(),
            otp: $('#otpInput').val()
        };
        payload[csrfName] = csrfHash;
        $('#btnVerifyOtp').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Verifying...');
        $.post('<?php echo site_url('portal/verify_otp'); ?>', payload, function(r){
            if (r.csrf) { csrfHash = r.csrf; }
            if (r.ok) {
                $('#otpVerifiedBadge').prop('hidden', false);
                $('#otpVerifySection').prop('hidden', true);
                showOtpMessage('Verified', false);
                setTimeout(function(){ window.location.reload(); }, 1000);
            } else {
                showOtpMessage(r.msg || 'Verification failed.', true);
            }
        }, 'json').always(function(){
            $('#btnVerifyOtp').prop('disabled', false).html('Verify OTP');
        });
    }

    $('#btnSendOtp').on('click', sendOtp);
    $('#btnVerifyOtp').on('click', verifyOtp);
    $('#resendLink').on('click', function(e){
        e.preventDefault();
        sendOtp();
    });

    function renderChamberInfo(){
        var opt = $('#chamberSel option:selected');
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
        $('#chamberInfo').html(parts.length ? parts.join('<br>') : 'Availability details will appear after the chamber updates its timings.');
    }

    function loadTicker(){
        var cid = $('#chamberSel').val();
        var d = $('#qdate').val();
        if (!cid || !d) return;
        $.getJSON('<?php echo site_url('portal/ticker_json'); ?>', {
            doctor_id: <?php echo (int) $doctor->id; ?>,
            chamber_id: cid,
            date: d
        }, function(r){
            if (r.csrf) { csrfHash = r.csrf; }
            if (r.serial) {
                $('#ticker').html('<span class="dot-pulse"></span><span>Now serving serial: ' + escapeHtml(r.serial) + ' (' + escapeHtml(r.status || '') + ')</span>');
            } else {
                $('#ticker').html('<span class="dot-pulse"></span><span>Current serial: - (queue not started)</span>');
            }
        });
    }

    $('#chamberSel').on('change', function(){ renderChamberInfo(); loadTicker(); });
    $('#qdate').on('change', loadTicker);
    renderChamberInfo();
    loadTicker();
    setInterval(loadTicker, 15000);
})();
</script>
