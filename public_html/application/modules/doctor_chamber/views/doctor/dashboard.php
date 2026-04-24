<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header"><h1>Chamber dashboard</h1></section>
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <div class="small-box bg-info"><div class="inner"><h3><?php echo (int) $appt_today; ?></h3><p>Appointments today</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning"><div class="inner"><h3><?php echo (int) $queue_open; ?></h3><p>Open queue (pending + arrived + serving)</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-secondary"><div class="inner"><h3><?php echo (int) $queue_pending; ?></h3><p>Queue pending (not arrived)</p></div></div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success"><div class="inner"><h3><?php echo (int) $queue_checked_in; ?></h3><p>Checked-in (arrived or serving)</p></div></div>
            </div>
        </div>
        <a class="btn btn-primary" href="<?php echo site_url('doctor_chamber/consultation_room'); ?>">Consultation room</a>
        <a class="btn btn-default" href="<?php echo site_url('doctor_chamber/revenue'); ?>">Revenue summary</a>
        <a class="btn btn-default" href="<?php echo site_url('doctor_chamber/crm_search'); ?>">Patient CRM</a>
        <a class="btn btn-default" href="<?php echo site_url('doctor_chamber/portal_profile'); ?>">Public landing settings</a>
        <div class="card mt-3">
            <div class="card-header"><h3 class="card-title">Today's live queue</h3></div>
            <div class="card-body p-0">
                <table class="table table-sm table-striped mb-0">
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
                            <td><?php echo htmlspecialchars($q->status); ?></td>
                            <td><?php echo $triage_summary !== '' ? htmlspecialchars($triage_summary) : '<span class="text-muted">-</span>'; ?></td>
                            <td>
                                <?php if (!empty($q->patient_id)) : ?>
                                    <a class="btn btn-xs btn-primary" href="<?php echo site_url('doctor_chamber/consultation_room?patient=' . (int) $q->patient_id); ?>">Open room</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($queue_today)) : ?>
                        <tr><td colspan="6" class="text-muted">No active queue yet today.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
