<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="chamber-ui">
    <section class="content-header">
        <div class="chamber-head">
            <div>
                <div class="chamber-kicker">Assistant portal</div>
                <h1>Check-in desk &amp; queue</h1>
                <p class="chamber-subtitle mb-0">Arrival, vitals, queue order, billing, SMS, and print workflow.</p>
            </div>
            <div class="chamber-actions">
                <a class="btn btn-primary" href="<?php echo site_url('assistant_chamber/manual_booking'); ?>"><i class="fas fa-phone"></i> Manual booking</a>
                <?php if ($doctor_id && $chamber_id) : ?>
                <a class="btn btn-outline-primary" href="<?php echo site_url('assistant_chamber/bulk_sms?from_desk=1&doctor_id=' . (int) $doctor_id . '&chamber_id=' . (int) $chamber_id . '&date=' . rawurlencode($queue_date)); ?>"><i class="fas fa-sms"></i> Bulk SMS</a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <section class="content">
        <?php if ($this->session->flashdata('chamber_desk_msg')) : ?>
            <div class="alert alert-warning"><?php echo htmlspecialchars($this->session->flashdata('chamber_desk_msg')); ?></div>
        <?php endif; ?>
        <form method="get" class="chamber-toolbar">
            <select name="doctor_id" class="form-control mr-2">
                <option value="">Doctor</option>
                <?php foreach ($doctors as $d) : ?>
                    <option value="<?php echo (int) $d->id; ?>" <?php echo ($doctor_id == $d->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($d->name); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="chamber_id" class="form-control mr-2">
                <option value="">Chamber</option>
                <?php foreach ((isset($chambers) ? $chambers : array()) as $c) : ?>
                    <option value="<?php echo (int) $c->id; ?>" <?php echo ($chamber_id == $c->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c->name); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="date" class="form-control mr-2" value="<?php echo htmlspecialchars($queue_date); ?>">
            <button class="btn btn-primary" type="submit"><i class="fas fa-sync-alt"></i> Load</button>
        </form>
        <?php if (!empty($queue)) : ?>
        <div class="chamber-panel">
            <div class="table-responsive">
        <table class="table chamber-table">
            <thead><tr><th>#</th><th>Serial</th><th>Patient</th><th>Phone</th><th>Status</th><th>Advance</th><th>Triage</th><th>Actions</th></tr></thead>
            <tbody id="queueBody">
            <?php foreach ($queue as $q) : ?>
                <?php
                $triage = !empty($q->triage_json) ? json_decode($q->triage_json, true) : array();
                $triage_summary = '';
                if (is_array($triage)) {
                    $triage_summary = trim((isset($triage['symptom']) ? $triage['symptom'] : '') . ' ' . (isset($triage['duration']) ? '(' . $triage['duration'] . ')' : ''));
                }
                ?>
                <tr data-id="<?php echo (int) $q->id; ?>">
                    <td class="handle" style="cursor:move"><i class="fas fa-grip-vertical"></i></td>
                    <td><?php echo (int) $q->serial_number; ?></td>
                    <td><?php echo htmlspecialchars($q->guest_name ?: ('#' . $q->patient_id)); ?></td>
                    <td><?php echo htmlspecialchars($q->guest_phone); ?></td>
                    <td><span class="chamber-status <?php echo htmlspecialchars($q->status); ?>"><?php echo htmlspecialchars($q->status); ?></span></td>
                    <td>
                        <?php if (!empty($q->advance_fee_amount)) : ?>
                            <?php echo htmlspecialchars((string) $q->advance_fee_amount); ?>
                            <span class="badge badge-<?php echo ($q->advance_payment_status === 'paid') ? 'success' : 'warning'; ?>"><?php echo htmlspecialchars($q->advance_payment_status); ?></span>
                        <?php else : ?>
                            <span class="text-muted">None</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $triage_summary !== '' ? htmlspecialchars($triage_summary) : '<span class="text-muted">-</span>'; ?></td>
                    <td>
                        <div class="chamber-btn-row">
                        <?php if ($q->status === 'pending') : ?>
                        <form method="post" action="<?php echo site_url('assistant_chamber/checkin'); ?>" style="display:inline">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <input type="hidden" name="queue_id" value="<?php echo (int) $q->id; ?>">
                            <button class="btn btn-xs btn-success" type="submit"><i class="fas fa-user-check"></i> Arrived</button>
                        </form>
                        <?php endif; ?>
                        <form method="post" action="<?php echo site_url('assistant_chamber/mark_serving'); ?>" style="display:inline">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <input type="hidden" name="queue_id" value="<?php echo (int) $q->id; ?>">
                            <button class="btn btn-xs btn-primary" type="submit"><i class="fas fa-broadcast-tower"></i> Serving</button>
                        </form>
                        <form method="post" action="<?php echo site_url('assistant_chamber/mark_status'); ?>" style="display:inline">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <input type="hidden" name="queue_id" value="<?php echo (int) $q->id; ?>">
                            <input type="hidden" name="status" value="done">
                            <button class="btn btn-xs btn-secondary" type="submit"><i class="fas fa-check"></i> Done</button>
                        </form>
                        <form method="post" action="<?php echo site_url('assistant_chamber/mark_status'); ?>" style="display:inline" onsubmit="return confirm('Cancel this serial?');">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <input type="hidden" name="queue_id" value="<?php echo (int) $q->id; ?>">
                            <input type="hidden" name="status" value="cancelled">
                            <button class="btn btn-xs btn-danger" type="submit"><i class="fas fa-times"></i> Cancel</button>
                        </form>
                        <form method="post" action="<?php echo site_url('assistant_chamber/emergency_bump'); ?>" style="display:inline">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <input type="hidden" name="queue_id" value="<?php echo (int) $q->id; ?>">
                            <button class="btn btn-xs btn-warning" type="submit"><i class="fas fa-arrow-up"></i> Emergency</button>
                        </form>
                        <button class="btn btn-xs btn-default" type="button" data-toggle="collapse" data-target="#v<?php echo (int) $q->id; ?>"><i class="fas fa-heartbeat"></i> Vitals</button>
                        <?php if (!empty($q->patient_id)) : ?>
                        <a class="btn btn-xs btn-info" target="_blank" title="Latest saved Rx for this patient" href="<?php echo site_url('assistant_chamber/print_latest_rx?patient_id=' . (int) $q->patient_id . '&doctor_id=' . (int) $q->doctor_id . '&chamber_id=' . (int) $chamber_id . '&date=' . rawurlencode($queue_date)); ?>"><i class="fas fa-print"></i> Latest Rx</a>
                        <?php endif; ?>
                        </div>
                        <div id="v<?php echo (int) $q->id; ?>" class="collapse mt-2">
                            <form method="post" action="<?php echo site_url('assistant_chamber/vitals_save'); ?>" class="form-inline">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                <input type="hidden" name="queue_id" value="<?php echo (int) $q->id; ?>">
                                <input class="form-control form-control-sm mr-1" name="bp_sys" placeholder="SYS">
                                <input class="form-control form-control-sm mr-1" name="bp_dia" placeholder="DIA">
                                <input class="form-control form-control-sm mr-1" name="pulse" placeholder="Pulse">
                                <input class="form-control form-control-sm mr-1" name="weight_kg" placeholder="Kg">
                                <button class="btn btn-sm btn-secondary" type="submit">Save vitals</button>
                            </form>
                            <form method="post" action="<?php echo site_url('assistant_chamber/mark_queue_fee_paid'); ?>" class="mt-2 form-inline flex-wrap">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                <input type="hidden" name="queue_id" value="<?php echo (int) $q->id; ?>">
                                <input class="form-control form-control-sm mr-1" style="width:6rem" name="amount" type="number" step="0.01" min="0.01" placeholder="Fee" required>
                                <select class="form-control form-control-sm mr-1" name="fee_type">
                                    <option value="consultation">Consultation</option>
                                    <option value="procedure">Procedure</option>
                                </select>
                                <input class="form-control form-control-sm mr-1" style="width:7rem" name="pay_method" placeholder="Cash / bKash">
                                <input class="form-control form-control-sm mr-1" style="width:8rem" name="remarks" placeholder="Note">
                                <button class="btn btn-sm btn-outline-primary" type="submit">Record fee paid</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
            </div>
        </div>
        <p class="small text-muted">Drag rows to reorder queue (then save).</p>
        <button type="button" class="btn btn-outline-secondary" id="saveOrder"><i class="fas fa-save"></i> Save queue order</button>
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        <script>
        (function(){
            var el = document.getElementById('queueBody');
            if (el && window.Sortable) { Sortable.create(el, { handle: '.handle' }); }
            $('#saveOrder').on('click', function(){
                var ids = [];
                $('#queueBody tr').each(function(){ ids.push($(this).data('id')); });
                $.post('<?php echo site_url('assistant_chamber/queue_reorder'); ?>', {
                    <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>',
                    order: ids
                }, function(){ alert('Order saved'); }, 'json');
            });
        })();
        </script>
        <?php
        $poll_pids = array();
        if (!empty($queue)) {
            foreach ($queue as $_q) {
                if (!empty($_q->patient_id)) {
                    $poll_pids[] = (int) $_q->patient_id;
                }
            }
            $poll_pids = array_values(array_unique($poll_pids));
        }
        ?>
        <?php if ($doctor_id && $chamber_id && !empty($poll_pids)) : ?>
        <p class="small text-muted">Auto-print: when the doctor saves a new prescription for a queued patient, the latest Rx opens in a new tab (poll every ~12s).</p>
        <script>
        (function(){
            var pollUrl = <?php echo json_encode(site_url('assistant_chamber/desk_rx_poll')); ?>;
            var doctorId = <?php echo (int) $doctor_id; ?>;
            var pids = <?php echo json_encode($poll_pids); ?>;
            var lastMap = {};
            function runPoll(isTick) {
                $.getJSON(pollUrl, { doctor_id: doctorId, patient_ids: pids.join(',') }, function(map) {
                    if (!isTick) {
                        for (var k in map) {
                            if (map.hasOwnProperty(k) && map[k] > 0) {
                                lastMap[k] = map[k] | 0;
                            }
                        }
                        return;
                    }
                    for (var k in map) {
                        if (!map.hasOwnProperty(k)) continue;
                        var cur = map[k] | 0;
                        if (cur < 1) continue;
                        if (lastMap[k] === undefined) {
                            lastMap[k] = cur;
                            continue;
                        }
                        if (cur > lastMap[k]) {
                            lastMap[k] = cur;
                            var u = <?php echo json_encode(site_url('prescription/viewPrescriptionPrint')); ?> + '?id=' + cur;
                            window.open(u, '_blank', 'noopener');
                        }
                    }
                });
            }
            runPoll(false);
            setInterval(function() { runPoll(true); }, 12000);
        })();
        </script>
        <?php endif; ?>
        <?php else : ?>
            <?php if ($doctor_id && $chamber_id) : ?>
                <div class="chamber-empty">No queue entries.</div>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</div>
