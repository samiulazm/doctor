<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container chamber-public-container py-4">
    <div class="chamber-public-panel text-center mb-3">
        <div class="chamber-kicker mb-1">Live queue</div>
        <h1 class="chamber-queue-doctor">
            <?php echo htmlspecialchars($doctor ? $doctor->name : 'Doctor', ENT_QUOTES, 'UTF-8'); ?>
        </h1>
        <p class="chamber-muted mb-0">
            <?php echo htmlspecialchars($chamber ? $chamber->name : '', ENT_QUOTES, 'UTF-8'); ?>
            &middot;
            <?php echo htmlspecialchars(date('d M Y', strtotime($queue_date)), ENT_QUOTES, 'UTF-8'); ?>
        </p>
    </div>

    <div class="chamber-queue-board">
        <div class="chamber-queue-cell chamber-queue-cell-yours">
            <div class="chamber-queue-label">Your serial</div>
            <div class="chamber-queue-number" id="yourSerial">
                <?php echo (int) $queue_row->serial_number; ?>
            </div>
        </div>
        <div class="chamber-queue-cell chamber-queue-cell-serving" id="servingCell">
            <div class="chamber-queue-label">Now serving</div>
            <div class="chamber-queue-number" id="nowServing">--</div>
        </div>
        <div class="chamber-queue-cell chamber-queue-cell-wait">
            <div class="chamber-queue-label">Est. wait</div>
            <div class="chamber-queue-number" id="estWait">
                -- <span class="chamber-queue-unit">min</span>
            </div>
        </div>
    </div>

    <div class="chamber-public-panel text-center mt-3">
        <span class="chamber-status pending" id="queueStatusBadge">Waiting</span>
        <div id="nextNotice" class="alert alert-success mt-2 py-2 small mb-0" hidden>
            You are next. Please be ready.
        </div>
        <p class="chamber-muted small mt-2 mb-0">
            Updates automatically. Last refreshed <span id="lastRefreshed">just now</span>.
        </p>
    </div>
</div>

<script>
var doctorId = <?php echo (int) $doctor_id; ?>;
var chamberId = <?php echo (int) $chamber_id; ?>;
var queueDate = '<?php echo htmlspecialchars($queue_date, ENT_QUOTES, 'UTF-8'); ?>';
var queueId = <?php echo (int) $queue_id; ?>;

function updateStatusBadge(status) {
    var badge = document.getElementById('queueStatusBadge');
    badge.className = 'chamber-status';
    if (status === 'arrived' || status === 'serving') {
        badge.classList.add('arrived');
        badge.textContent = 'Being seen';
    } else if (status === 'done') {
        badge.classList.add('done');
        badge.textContent = 'Consultation complete';
    } else {
        badge.classList.add('pending');
        badge.textContent = 'Waiting';
    }
}

function loadQueueStatus() {
    $.getJSON('<?php echo site_url('portal/ticker_json'); ?>', {
        doctor_id: doctorId,
        chamber_id: chamberId,
        date: queueDate,
        queue_id: queueId
    }, function(r) {
        if (r.ok) {
            $('#yourSerial').text(r.patient_serial != null ? r.patient_serial : '--');
            $('#nowServing').text(r.serial != null ? r.serial : '--');
            var wait = (r.estimated_wait != null) ? r.estimated_wait + ' <span class="chamber-queue-unit">min</span>' : '--';
            $('#estWait').html(wait);
            $('#lastRefreshed').text('just now');
            updateStatusBadge(r.status);
            if (r.serial != null && r.patient_serial != null && (r.patient_serial - r.serial) === 1) {
                $('#servingCell').addClass('is-next');
                $('#nextNotice').prop('hidden', false);
            } else {
                $('#servingCell').removeClass('is-next');
                $('#nextNotice').prop('hidden', true);
            }
        }
    });
}

var tickerInterval = null;
function startTicker() {
    stopTicker();
    tickerInterval = setInterval(loadQueueStatus, 15000);
}
function stopTicker() {
    if (tickerInterval) {
        clearInterval(tickerInterval);
        tickerInterval = null;
    }
}

document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        stopTicker();
    } else {
        loadQueueStatus();
        startTicker();
    }
});

loadQueueStatus();
startTicker();
</script>
