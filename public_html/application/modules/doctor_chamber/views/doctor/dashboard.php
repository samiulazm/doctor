<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper chamber-ui">
    <section class="content-header">
        <div class="chamber-head">
            <div>
                <div class="chamber-kicker">Doctor portal</div>
                <h1>Chamber dashboard</h1>
                <p class="chamber-subtitle mb-0">Today queue, checked-in patients, and chamber actions in one view.</p>
            </div>
            <div class="chamber-actions">
                <a class="btn btn-primary" href="<?php echo site_url('doctor_chamber/consultation_room'); ?>"><i class="fas fa-notes-medical"></i> Consultation room</a>
                <a class="btn btn-outline-primary" href="<?php echo site_url('ai_patient_overview'); ?>"><i class="fas fa-brain"></i> AI overview</a>
                <a class="btn btn-outline-secondary" href="<?php echo site_url('doctor_chamber/portal_profile'); ?>"><i class="fas fa-id-card"></i> Public settings</a>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="chamber-stat-grid">
            <div class="chamber-stat">
                <span class="chamber-stat-icon"><i class="fas fa-calendar-day"></i></span>
                <div class="chamber-stat-value"><?php echo (int) $appt_today; ?></div>
                <div class="chamber-stat-label">Appointments today</div>
            </div>
            <div class="chamber-stat">
                <span class="chamber-stat-icon"><i class="fas fa-users"></i></span>
                <div class="chamber-stat-value"><?php echo (int) $queue_open; ?></div>
                <div class="chamber-stat-label">Open queue</div>
            </div>
            <div class="chamber-stat">
                <span class="chamber-stat-icon"><i class="fas fa-hourglass-half"></i></span>
                <div class="chamber-stat-value"><?php echo (int) $queue_pending; ?></div>
                <div class="chamber-stat-label">Pending arrivals</div>
            </div>
            <div class="chamber-stat">
                <span class="chamber-stat-icon"><i class="fas fa-user-check"></i></span>
                <div class="chamber-stat-value"><?php echo (int) $queue_checked_in; ?></div>
                <div class="chamber-stat-label">Checked in</div>
            </div>
        </div>
        <div class="chamber-ai-strip">
            <div><i class="fas fa-magic"></i> Use existing AI tools for patient overview and medical image analysis from the chamber workflow.</div>
            <div class="chamber-actions">
                <a class="btn btn-sm btn-outline-primary" href="<?php echo site_url('ai_patient_overview'); ?>"><i class="fas fa-user-md"></i> Patient overview</a>
                <a class="btn btn-sm btn-outline-primary" href="<?php echo site_url('ai_image_analysis'); ?>"><i class="fas fa-x-ray"></i> Image analysis</a>
            </div>
        </div>
        <div class="chamber-actions mb-3">
            <a class="btn btn-outline-secondary" href="<?php echo site_url('doctor_chamber/revenue'); ?>"><i class="fas fa-chart-line"></i> Revenue summary</a>
            <a class="btn btn-outline-secondary" href="<?php echo site_url('doctor_chamber/crm_search'); ?>"><i class="fas fa-search"></i> Patient CRM</a>
            <a class="btn btn-outline-secondary" href="<?php echo site_url('doctor_chamber/my_chambers'); ?>"><i class="fas fa-clinic-medical"></i> Chambers</a>
            <a class="btn btn-outline-secondary" href="<?php echo site_url('doctor_chamber/schedule_exceptions'); ?>"><i class="fas fa-calendar-times"></i> Schedules</a>
        </div>
        <div class="chamber-panel">
            <div class="chamber-panel-header"><h3 class="chamber-panel-title">Today's live queue</h3></div>
            <div class="table-responsive">
                <table class="table table-sm chamber-table">
                    <thead><tr><th>Serial</th><th>Patient</th><th>Chamber</th><th>Status</th><th>Triage</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ((isset($queue_today) ? $queue_today : array()) as $q) : ?>
                        <?php
                        $triage = !empty($q->triage_json) ? json_decode($q->triage_json, true) : array();
                        $triage_summary = is_array($triage) ? trim((isset($triage['symptom']) ? $triage['symptom'] : '') . ' ' . (isset($triage['duration']) ? '(' . $triage['duration'] . ')' : '')) : '';
                        ?>
                        <tr>
                            <td><?php echo (int) $q->serial_number; ?></td>
                            <td><?php echo htmlspecialchars($q->guest_name ?: ('#' . $q->patient_id)); ?></td>
                            <td><?php echo htmlspecialchars((string) $q->chamber_name); ?></td>
                            <td><span class="chamber-status <?php echo htmlspecialchars($q->status); ?>"><?php echo htmlspecialchars($q->status); ?></span></td>
                            <td><?php echo $triage_summary !== '' ? htmlspecialchars($triage_summary) : '<span class="text-muted">-</span>'; ?></td>
                            <td>
                                <?php if (!empty($q->patient_id)) : ?>
                                    <a class="btn btn-xs btn-primary" href="<?php echo site_url('doctor_chamber/consultation_room?patient=' . (int) $q->patient_id); ?>"><i class="fas fa-door-open"></i> Open room</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($queue_today)) : ?>
                        <tr><td colspan="6" class="text-muted text-center py-4">No active queue yet today.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
