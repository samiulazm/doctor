<?php defined('BASEPATH') OR exit('No direct script access allowed');
$currency = isset($settings->currency) ? (string) $settings->currency : '';
?>
<div class="chamber-ui">
    <section class="content-header">
        <div class="chamber-head">
            <div>
                <div class="chamber-kicker">Doctor portal</div>
                <h1>Chamber dashboard</h1>
                <p class="chamber-subtitle mb-0">Today's queue, checked-in patients, and revenue at a glance.</p>
            </div>
            <div class="chamber-actions">
                <a class="btn btn-sm btn-primary" href="<?php echo site_url('doctor_chamber/consultation_room'); ?>">
                    <i class="fas fa-notes-medical mr-1"></i> Consultation room
                </a>
                <a class="btn btn-sm btn-outline-primary" href="<?php echo site_url('ai_patient_overview'); ?>">
                    <i class="fas fa-brain mr-1"></i> AI overview
                </a>
                <a class="btn btn-sm btn-outline-secondary" href="<?php echo site_url('doctor_chamber/portal_profile'); ?>">
                    <i class="fas fa-id-card mr-1"></i> Public settings
                </a>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="chamber-stat-grid">
            <div class="chamber-stat">
                <span class="chamber-stat-icon"><i class="fas fa-calendar-day"></i></span>
                <div class="chamber-stat-value"><?php echo (int) $total_today; ?></div>
                <div class="chamber-stat-label">Total patients today</div>
            </div>
            <div class="chamber-stat">
                <span class="chamber-stat-icon"><i class="fas fa-user-check"></i></span>
                <div class="chamber-stat-value"><?php echo (int) $checked_in; ?></div>
                <div class="chamber-stat-label">Checked in</div>
            </div>
            <div class="chamber-stat">
                <span class="chamber-stat-icon"><i class="fas fa-hourglass-half"></i></span>
                <div class="chamber-stat-value"><?php echo (int) $pending; ?></div>
                <div class="chamber-stat-label">Pending</div>
            </div>
            <div class="chamber-stat">
                <span class="chamber-stat-icon"><i class="fas fa-coins"></i></span>
                <div class="chamber-stat-value"><?php echo htmlspecialchars($currency, ENT_QUOTES, 'UTF-8'); ?> <?php echo number_format((float) $today_revenue, 0); ?></div>
                <div class="chamber-stat-label">Today's revenue</div>
            </div>
        </div>

        <div class="chamber-actions mb-3">
            <a class="btn btn-outline-secondary" href="<?php echo site_url('doctor_chamber/revenue'); ?>"><i class="fas fa-chart-line mr-1"></i> Revenue summary</a>
            <a class="btn btn-outline-secondary" href="<?php echo site_url('doctor_chamber/crm_search'); ?>"><i class="fas fa-search mr-1"></i> Patient CRM</a>
            <a class="btn btn-outline-secondary" href="<?php echo site_url('doctor_chamber/my_chambers'); ?>"><i class="fas fa-clinic-medical mr-1"></i> Chambers</a>
            <a class="btn btn-outline-secondary" href="<?php echo site_url('doctor_chamber/schedule_exceptions'); ?>"><i class="fas fa-calendar-times mr-1"></i> Schedules</a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="chamber-panel">
                    <div class="chamber-panel-header">
                        <h3 class="chamber-panel-title">
                            <i class="fas fa-list-ol mr-2 text-muted"></i>Today's live queue
                        </h3>
                        <span class="chamber-muted small" id="queueLastUpdated"></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm chamber-table mb-0" id="liveQueueTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient</th>
                                    <th>Chamber</th>
                                    <th>Status</th>
                                    <th>Triage</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="liveQueueBody">
                                <tr><td colspan="6" class="text-muted text-center py-4">Loading queue...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="chamber-panel">
                    <div class="chamber-panel-header">
                        <h3 class="chamber-panel-title"><i class="fas fa-redo-alt mr-2 text-muted"></i>Follow-ups today</h3>
                    </div>
                    <div class="chamber-panel-body text-center py-3">
                        <div class="chamber-stat-value"><?php echo (int) $followup_count; ?></div>
                        <div class="chamber-stat-label mt-1">Patients with follow-up tag</div>
                    </div>
                </div>

                <div class="chamber-panel">
                    <div class="chamber-panel-header">
                        <h3 class="chamber-panel-title"><i class="fas fa-exclamation-triangle mr-2 text-muted"></i>High-risk patients</h3>
                    </div>
                    <div class="chamber-panel-body p-0">
                        <ul class="list-unstyled mb-0" id="highRiskList">
                            <?php if (empty($high_risk_patients)) : ?>
                                <li class="px-3 py-3 text-muted small">No high-risk patients in today's queue.</li>
                            <?php else : ?>
                                <?php foreach ($high_risk_patients as $hr) : ?>
                                    <?php $hr_name = !empty($hr->guest_name) ? $hr->guest_name : (!empty($hr->patient_name) ? $hr->patient_name : ('#' . $hr->patient_id)); ?>
                                    <li class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between">
                                        <span class="small font-weight-bold"><?php echo htmlspecialchars($hr_name, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <span class="chamber-status high_risk">High risk</span>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="chamber-panel">
                    <div class="chamber-panel-header">
                        <h3 class="chamber-panel-title"><i class="fas fa-chart-bar mr-2 text-muted"></i>Monthly revenue</h3>
                    </div>
                    <div class="chamber-panel-body">
                        <canvas id="chartMonthlyRevenue" class="chamber-chart-canvas" height="220"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chamber-panel">
                    <div class="chamber-panel-header">
                        <h3 class="chamber-panel-title"><i class="fas fa-users mr-2 text-muted"></i>Monthly patients</h3>
                    </div>
                    <div class="chamber-panel-body">
                        <canvas id="chartMonthlyPatients" class="chamber-chart-canvas" height="220"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="<?php echo base_url('adminlte/plugins/chart.js/Chart.min.js'); ?>"></script>
<script>
(function () {
    var BASE_URL = '<?php echo rtrim(site_url(), '/'); ?>/';
    var POLL_INTERVAL = 12000;

    function escHtml(s) {
        return String(s === null || s === undefined ? '' : s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function statusClass(status) {
        var clean = String(status || 'pending').replace(/[^a-z0-9_-]/gi, '').toLowerCase();
        return clean || 'pending';
    }

    function pollQueue() {
        $.getJSON(BASE_URL + 'doctor_chamber/queue_json', function (r) {
            if (!r || !r.rows) return;
            var rows = r.rows || [];
            var html = '';
            if (rows.length === 0) {
                html = '<tr><td colspan="6" class="text-muted text-center py-4">No active queue yet today.</td></tr>';
            } else {
                rows.forEach(function (q) {
                    var status = statusClass(q.status);
                    var serial = parseInt(q.serial_number, 10);
                    var patientId = parseInt(q.patient_id, 10);
                    html += '<tr>' +
                        '<td>' + (isNaN(serial) ? '' : serial) + '</td>' +
                        '<td>' + escHtml(q.guest_name || (patientId ? ('#' + patientId) : '')) + '</td>' +
                        '<td>' + escHtml(q.chamber_name || '') + '</td>' +
                        '<td><span class="chamber-status ' + status + '">' + escHtml(status) + '</span></td>' +
                        '<td>' + escHtml(q.triage_summary || '') + '</td>' +
                        '<td>' + (patientId ? '<a class="btn btn-xs btn-primary" href="' + BASE_URL + 'doctor_chamber/consultation_room?patient=' + patientId + '"><i class="fas fa-door-open mr-1"></i>Open</a>' : '') + '</td>' +
                        '</tr>';
                });
            }
            $('#liveQueueBody').html(html);
            $('#queueLastUpdated').text('Updated ' + new Date().toLocaleTimeString('en-BD', { hour: '2-digit', minute: '2-digit' }));
        });
    }

    function chartOptions() {
        return {
            responsive: true,
            maintainAspectRatio: false,
            legend: { display: false },
            tooltips: { mode: 'index', intersect: false },
            scales: {
                xAxes: [{
                    gridLines: { color: '#e2e8f0' },
                    ticks: { fontColor: '#475569', fontFamily: 'DM Sans', fontSize: 12 }
                }],
                yAxes: [{
                    gridLines: { color: '#e2e8f0' },
                    ticks: { beginAtZero: true, fontColor: '#475569', fontFamily: 'DM Sans', fontSize: 12, precision: 0 }
                }]
            }
        };
    }

    function renderCharts() {
        if (typeof Chart === 'undefined') return;
        $.getJSON(BASE_URL + 'doctor_chamber/chart_data_json', function (r) {
            r = r || {};
            var labels = r.labels || [];
            var color = 'rgba(15, 118, 110, 0.75)';
            var border = '#0f766e';
            var revenueCanvas = document.getElementById('chartMonthlyRevenue');
            var patientsCanvas = document.getElementById('chartMonthlyPatients');
            if (revenueCanvas) {
                new Chart(revenueCanvas, {
                    type: 'bar',
                    data: { labels: labels, datasets: [{ data: r.revenue || [], backgroundColor: color, borderColor: border, borderWidth: 1 }] },
                    options: chartOptions()
                });
            }
            if (patientsCanvas) {
                new Chart(patientsCanvas, {
                    type: 'bar',
                    data: { labels: labels, datasets: [{ data: r.patients || [], backgroundColor: color, borderColor: border, borderWidth: 1 }] },
                    options: chartOptions()
                });
            }
        });
    }

    pollQueue();
    setInterval(pollQueue, POLL_INTERVAL);
    renderCharts();
}());
</script>
