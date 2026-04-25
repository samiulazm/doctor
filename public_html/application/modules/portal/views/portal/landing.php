<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$img = !empty($profile->hero_image) ? base_url($profile->hero_image) : base_url('uploads/default-image.png');
$fee = (isset($profile->advance_booking_fee) && (float) $profile->advance_booking_fee > 0) ? number_format((float) $profile->advance_booking_fee, 2) . ' BDT' : 'No advance fee';
?>
<div class="container chamber-public-container py-3">
    <div class="chamber-public-hero" style="background-image:url('<?php echo htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>');">
        <div class="chamber-public-hero-content">
            <div class="chamber-kicker">Doctor serial booking</div>
            <h1><?php echo htmlspecialchars($doctor->name); ?></h1>
            <p class="lead mb-0"><?php echo htmlspecialchars($profile->specialty_label ?: $doctor->department_name); ?></p>
            <div class="chamber-public-hero-meta">
                <span class="chamber-public-pill"><i class="fas fa-stethoscope"></i> <?php echo htmlspecialchars($profile->specialty_label ?: $doctor->department_name); ?></span>
                <span class="chamber-public-pill"><i class="fas fa-credit-card"></i> <?php echo htmlspecialchars($fee); ?></span>
            </div>
            <a class="btn btn-warning btn-lg font-weight-bold" href="<?php echo site_url('portal/triage/' . rawurlencode($slug)); ?>">
                <i class="fas fa-calendar-check mr-1"></i> Book serial now
            </a>
        </div>
    </div>
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
                                <?php echo htmlspecialchars($c->name); ?>
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
            <div id="ticker" class="chamber-ticker"><span class="dot-pulse"></span><span>Current serial: -</span></div>
            <p class="chamber-muted small mt-3 mb-0">Queue status refreshes automatically while the chamber desk updates the current serial.</p>
        </div>
    </div>
    <script>
    (function(){
        function escapeHtml(s){
            return $('<div>').text(s || '').html();
        }
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
            $.getJSON('<?php echo site_url('portal/ticker_json'); ?>', {
                doctor_id: <?php echo (int) $doctor->id; ?>,
                chamber_id: cid,
                date: d
            }, function(r){
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
    <?php endif; ?>
</div>
