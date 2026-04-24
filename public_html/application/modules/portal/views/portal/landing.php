<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$img = !empty($profile->hero_image) ? base_url($profile->hero_image) : base_url('uploads/default-image.png');
?>
<div class="container py-4">
    <div class="hero row align-items-center">
        <div class="col-md-4 text-center mb-3 mb-md-0">
            <img src="<?php echo htmlspecialchars($img); ?>" alt="" class="img-fluid rounded shadow" style="max-height:220px;object-fit:cover;">
        </div>
        <div class="col-md-8">
            <h1 class="h2 mb-2"><?php echo htmlspecialchars($doctor->name); ?></h1>
            <p class="lead mb-3"><?php echo htmlspecialchars($profile->specialty_label ?: $doctor->department_name); ?></p>
            <?php if (isset($profile->advance_booking_fee) && (float) $profile->advance_booking_fee > 0) : ?>
                <p class="mb-3">Advance booking fee: <strong><?php echo htmlspecialchars(number_format((float) $profile->advance_booking_fee, 2)); ?> BDT</strong></p>
            <?php endif; ?>
            <a class="btn btn-warning btn-lg font-weight-bold" href="<?php echo site_url('portal/triage/' . rawurlencode($slug)); ?>">Book serial now</a>
        </div>
    </div>
    <?php if (!empty($chambers)) : ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Chamber availability</h5>
            <div class="form-inline mb-2">
                <label class="mr-2">Chamber</label>
                <select id="chamberSel" class="form-control mr-3">
                    <?php foreach ($chambers as $c) : ?>
                        <option value="<?php echo (int) $c->id; ?>"
                                data-address="<?php echo htmlspecialchars((string) $c->address, ENT_QUOTES, 'UTF-8'); ?>"
                                data-phone="<?php echo htmlspecialchars((string) $c->phone, ENT_QUOTES, 'UTF-8'); ?>"
                                data-hours="<?php echo htmlspecialchars(json_encode(isset($c->weekly_hours) ? $c->weekly_hours : array()), ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($c->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label class="mr-2">Date</label>
                <input type="date" id="qdate" class="form-control" value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div id="chamberInfo" class="small text-muted mb-2"></div>
            <div id="ticker" class="ticker">Current serial: -</div>
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
                if (r.serial) { $('#ticker').text('Now serving serial: ' + r.serial + ' (' + (r.status||'') + ')'); }
                else { $('#ticker').text('Current serial: - (queue not started)'); }
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
