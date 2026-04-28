<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="chamber-ui">
    <section class="content-header">
        <div class="chamber-head">
            <div>
                <div class="chamber-kicker">Assistant portal</div>
                <h1>Check-in desk &amp; queue</h1>
                <p class="chamber-subtitle mb-0">Arrival, vitals, queue order, billing, and print workflow.</p>
            </div>
            <div class="chamber-actions">
                <a class="btn btn-sm btn-primary" href="<?php echo site_url('assistant_chamber/manual_booking'); ?>">
                    <i class="fas fa-user-plus mr-1"></i> Manual booking
                </a>
                <?php if ($doctor_id && $chamber_id) : ?>
                    <a class="btn btn-sm btn-outline-secondary" href="<?php echo site_url('assistant_chamber/bulk_sms?from_desk=1&doctor_id=' . (int) $doctor_id . '&chamber_id=' . (int) $chamber_id . '&date=' . rawurlencode($queue_date)); ?>">
                        <i class="fas fa-sms mr-1"></i> Bulk SMS
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="content">
        <?php if ($this->session->flashdata('chamber_desk_msg')) : ?>
            <div class="alert alert-warning"><?php echo htmlspecialchars($this->session->flashdata('chamber_desk_msg'), ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form method="get" class="chamber-toolbar">
            <select name="doctor_id" class="form-control mr-2" style="max-width:200px;">
                <option value="">Doctor</option>
                <?php foreach ($doctors as $d) : ?>
                    <option value="<?php echo (int) $d->id; ?>" <?php echo ($doctor_id == $d->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($d->name, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="chamber_id" class="form-control mr-2" style="max-width:180px;">
                <option value="">Chamber</option>
                <?php foreach ((isset($chambers) ? $chambers : array()) as $c) : ?>
                    <option value="<?php echo (int) $c->id; ?>" <?php echo ($chamber_id == $c->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c->name, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="date" class="form-control mr-2" style="max-width:160px;" value="<?php echo htmlspecialchars($queue_date, ENT_QUOTES, 'UTF-8'); ?>">
            <button class="btn btn-sm btn-primary" type="submit">
                <i class="fas fa-sync-alt mr-1"></i> Load
            </button>
        </form>

        <?php if ($doctor_id && $chamber_id) : ?>
            <div class="chamber-panel">
                <div class="chamber-panel-header d-flex align-items-center justify-content-between">
                    <h3 class="chamber-panel-title">
                        <i class="fas fa-list-ol mr-2 text-muted"></i>
                        Today's queue
                        <span id="nowServingBadge" class="chamber-status serving ml-2 small" style="display:none;"></span>
                    </h3>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="saveOrder">
                        <i class="fas fa-save mr-1"></i> Save order
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm chamber-table mb-0" id="queueTable">
                        <thead>
                            <tr>
                                <th style="width:28px;"></th>
                                <th>#</th>
                                <th>Patient</th>
                                <th>Source</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Triage</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="queueBody">
                            <?php if (!empty($queue)) : ?>
                                <?php foreach ($queue as $q) : ?>
                                    <?php
                                    $triage = !empty($q->triage_json) ? json_decode($q->triage_json, true) : array();
                                    $triage_source = is_array($triage) && !empty($triage['source']) ? (string) $triage['source'] : 'manual';
                                    $triage_summary = '';
                                    if (is_array($triage)) {
                                        $triage_summary = trim((isset($triage['symptom']) ? $triage['symptom'] : '') . ' ' . (!empty($triage['duration']) ? '(' . $triage['duration'] . ')' : ''));
                                    }
                                    $source_class = 'manual';
                                    if (strpos($triage_source, 'portal') !== false || strpos($triage_source, 'website') !== false) {
                                        $source_class = 'website';
                                    } elseif (strpos($triage_source, 'phone') !== false || strpos($triage_source, 'call') !== false) {
                                        $source_class = 'phone';
                                    }
                                    $source_icon = array('manual' => 'fa-user-plus', 'phone' => 'fa-phone', 'website' => 'fa-globe');
                                    $source_label = array('manual' => 'Walk-in', 'phone' => 'Phone', 'website' => 'Online');
                                    $status_class = htmlspecialchars((string) $q->status, ENT_QUOTES, 'UTF-8');
                                    if (!empty($q->is_emergency)) {
                                        $status_class = 'emergency';
                                    }
                                    $patient_label = !empty($q->guest_name) ? $q->guest_name : ('#' . $q->patient_id);
                                    ?>
                                    <tr data-id="<?php echo (int) $q->id; ?>">
                                        <td class="handle" style="cursor:move; color:#94a3b8;">
                                            <i class="fas fa-grip-vertical"></i>
                                        </td>
                                        <td class="font-weight-bold"><?php echo (int) $q->serial_number; ?></td>
                                        <td><?php echo htmlspecialchars($patient_label, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <span class="chamber-source-tag <?php echo htmlspecialchars($source_class, ENT_QUOTES, 'UTF-8'); ?>">
                                                <i class="fas <?php echo $source_icon[$source_class]; ?>"></i>
                                                <?php echo $source_label[$source_class]; ?>
                                            </span>
                                        </td>
                                        <td class="text-muted small"><?php echo htmlspecialchars((string) $q->guest_phone, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><span class="chamber-status <?php echo $status_class; ?>"><?php echo $status_class; ?></span></td>
                                        <td class="small text-muted">
                                            <?php echo $triage_summary !== '' ? htmlspecialchars($triage_summary, ENT_QUOTES, 'UTF-8') : '<span class="text-muted">-</span>'; ?>
                                        </td>
                                        <td>
                                            <div class="chamber-btn-row">
                                                <?php if ($q->status === 'pending') : ?>
                                                    <form method="post" action="<?php echo site_url('assistant_chamber/checkin'); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <input type="hidden" name="queue_id" value="<?php echo (int) $q->id; ?>">
                                                        <button class="btn btn-xs btn-success" type="submit" title="Mark as arrived">
                                                            <i class="fas fa-user-check"></i> Check in
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <form method="post" action="<?php echo site_url('assistant_chamber/mark_serving'); ?>">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="queue_id" value="<?php echo (int) $q->id; ?>">
                                                    <button class="btn btn-xs btn-primary" type="submit" title="Now serving">
                                                        <i class="fas fa-broadcast-tower"></i> Serving
                                                    </button>
                                                </form>

                                                <form method="post" action="<?php echo site_url('assistant_chamber/emergency_bump'); ?>">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="queue_id" value="<?php echo (int) $q->id; ?>">
                                                    <button class="btn btn-xs btn-danger" type="submit" title="Emergency - move to top">
                                                        <i class="fas fa-arrow-up"></i> Emergency
                                                    </button>
                                                </form>

                                                <form method="post" action="<?php echo site_url('assistant_chamber/mark_status'); ?>">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="queue_id" value="<?php echo (int) $q->id; ?>">
                                                    <input type="hidden" name="status" value="done">
                                                    <button class="btn btn-xs btn-secondary" type="submit">
                                                        <i class="fas fa-check"></i> Done
                                                    </button>
                                                </form>

                                                <form method="post" action="<?php echo site_url('assistant_chamber/mark_status'); ?>" onsubmit="return confirm('Cancel this serial? This removes the patient from today queue.');">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="queue_id" value="<?php echo (int) $q->id; ?>">
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button class="btn btn-xs btn-outline-danger" type="submit">
                                                        <i class="fas fa-times"></i> Cancel
                                                    </button>
                                                </form>

                                                <a class="btn btn-xs btn-outline-secondary"
                                                   href="<?php echo site_url('assistant_chamber/print_token?queue_id=' . (int) $q->id); ?>"
                                                   target="_blank" title="Print serial token">
                                                    <i class="fas fa-print"></i> Token
                                                </a>

                                                <button class="btn btn-xs btn-outline-primary" type="button"
                                                        data-toggle="collapse"
                                                        data-target="#billing<?php echo (int) $q->id; ?>"
                                                        title="Open billing">
                                                    <i class="fas fa-file-invoice-dollar"></i> Bill
                                                </button>

                                                <button class="btn btn-xs btn-default" type="button"
                                                        data-toggle="collapse"
                                                        data-target="#vitals<?php echo (int) $q->id; ?>">
                                                    <i class="fas fa-heartbeat"></i> Vitals
                                                </button>

                                                <?php if (!empty($q->patient_id)) : ?>
                                                    <a class="btn btn-xs btn-info" target="_blank" title="Latest saved Rx for this patient" href="<?php echo site_url('assistant_chamber/print_latest_rx?patient_id=' . (int) $q->patient_id . '&doctor_id=' . (int) $q->doctor_id . '&chamber_id=' . (int) $chamber_id . '&date=' . rawurlencode($queue_date)); ?>">
                                                        <i class="fas fa-print"></i> Latest Rx
                                                    </a>
                                                <?php endif; ?>
                                            </div>

                                            <div id="billing<?php echo (int) $q->id; ?>" class="collapse mt-2">
                                                <?php $this->load->view('assistant/_billing_panel', array('q' => $q, 'settings' => $settings)); ?>
                                            </div>

                                            <div id="vitals<?php echo (int) $q->id; ?>" class="collapse mt-2">
                                                <form method="post" action="<?php echo site_url('assistant_chamber/vitals_save'); ?>" class="form-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="queue_id" value="<?php echo (int) $q->id; ?>">
                                                    <input class="form-control form-control-sm mr-1" name="bp_sys" placeholder="SYS">
                                                    <input class="form-control form-control-sm mr-1" name="bp_dia" placeholder="DIA">
                                                    <input class="form-control form-control-sm mr-1" name="pulse" placeholder="Pulse">
                                                    <input class="form-control form-control-sm mr-1" name="weight_kg" placeholder="Kg">
                                                    <button class="btn btn-sm btn-secondary" type="submit">Save vitals</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr><td colspan="8" class="text-muted text-center py-4">No queue entries for this date.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <p class="small text-muted">Drag rows to reorder queue.</p>

            <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
            <script>
            (function () {
                var BASE_URL = '<?php echo rtrim(site_url(), '/'); ?>/';

                function notify(type, message) {
                    if (window.toastr && typeof window.toastr[type] === 'function') {
                        window.toastr[type](message);
                    } else if (window.console) {
                        window.console.log(message);
                    }
                }

                function collectOrder() {
                    var ids = [];
                    document.querySelectorAll('#queueBody tr[data-id]').forEach(function (tr) {
                        ids.push(tr.getAttribute('data-id'));
                    });
                    return ids;
                }

                function postOrder(successText) {
                    $.post(BASE_URL + 'assistant_chamber/queue_reorder', { order: collectOrder() }, function (r) {
                        if (r && r.ok) {
                            notify('success', successText);
                        } else {
                            notify('warning', 'Reorder saved locally - server sync failed.');
                        }
                    }, 'json').fail(function () {
                        notify('warning', 'Reorder saved locally - server sync failed.');
                    });
                }

                var el = document.getElementById('queueBody');
                if (el && window.Sortable) {
                    Sortable.create(el, {
                        handle: '.handle',
                        animation: 150,
                        onEnd: function () {
                            postOrder('Queue reordered.');
                        }
                    });
                }

                $('#saveOrder').on('click', function () {
                    postOrder('Order saved.');
                });

                (function () {
                    if (!<?php echo json_encode((bool) ($doctor_id && $chamber_id)); ?>) return;
                    var tickerUrl = BASE_URL + 'assistant_chamber/queue_ticker_json';
                    var params = {
                        doctor_id: <?php echo (int) $doctor_id; ?>,
                        chamber_id: <?php echo (int) $chamber_id; ?>,
                        date: <?php echo json_encode($queue_date); ?>
                    };
                    function pollTicker() {
                        $.getJSON(tickerUrl, params, function (r) {
                            var badge = document.getElementById('nowServingBadge');
                            if (!badge || !r) return;
                            if (r.serial) {
                                badge.textContent = 'Now serving: #' + parseInt(r.serial, 10);
                                badge.style.display = '';
                            } else {
                                badge.textContent = 'No one serving';
                                badge.style.display = '';
                            }
                        });
                    }
                    pollTicker();
                    setInterval(pollTicker, 12000);
                }());

                $(document).on('click', '[id^="paidDueToggle_"] button', function () {
                    var $btn = $(this);
                    var $group = $btn.closest('.btn-group');
                    var id = $group.attr('id').replace('paidDueToggle_', '');
                    var status = $btn.data('status');
                    $group.find('button').removeClass('active');
                    $btn.addClass('active');
                    $('#payStatus_' + id).val(status);
                    $('#paid_status_' + id).val(status);
                });

                $(document).on('click', '[id^="payMethodToggle_"] button', function () {
                    var $btn = $(this);
                    var $group = $btn.closest('.btn-group');
                    var id = $group.attr('id').replace('payMethodToggle_', '');
                    var method = $btn.data('method');
                    $group.find('button').removeClass('active');
                    $btn.addClass('active');
                    $('#payMethod_' + id).val(method);
                    $('#payment_method_' + id).val(method);
                    $('#bkashSection_' + id).toggleClass('d-none', method !== 'bkash');
                });

                $(document).on('click', '[id^="initiateBkash_"]', function () {
                    var qid = $(this).data('queue-id');
                    var amount = $('#fee_' + qid).val();
                    if (!amount || parseFloat(amount) <= 0) {
                        notify('warning', 'Enter a fee amount first.');
                        return;
                    }
                    var $btn = $(this);
                    var $status = $('#bkashStatus_' + qid);
                    $btn.prop('disabled', true);
                    $status.text('Initiating...').removeClass('text-success text-danger').addClass('text-muted');
                    $.post(BASE_URL + 'payment_bd/bkash_initiate_desk', {
                        queue_id: qid,
                        amount: amount
                    }, function (r) {
                        $btn.prop('disabled', false);
                        if (r && r.success) {
                            $status.text('Initiated - ' + (r.payment_id || 'OK')).removeClass('text-muted text-danger').addClass('text-success');
                        } else {
                            $status.text(r && r.message ? r.message : 'bKash error. Try again.').removeClass('text-muted text-success').addClass('text-danger');
                        }
                    }, 'json').fail(function () {
                        $btn.prop('disabled', false);
                        $status.text('Request failed. Check connection.').removeClass('text-muted text-success').addClass('text-danger');
                    });
                });

                $(document).on('click', '[id^="saveBilling_"]', function () {
                    var qid = $(this).data('queue-id');
                    var $btn = $(this);
                    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
                    $.post(BASE_URL + 'assistant_chamber/mark_queue_fee_paid_ajax', {
                        queue_id: qid,
                        amount: $('#fee_' + qid).val(),
                        fee_type: $('#feetype_' + qid).val(),
                        pay_status: $('#payStatus_' + qid).val(),
                        pay_method: $('#payMethod_' + qid).val(),
                        remarks: $('#remarks_' + qid).val()
                    }, function (r) {
                        $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Record fee');
                        if (r && r.success) {
                            notify('success', 'Fee recorded.');
                            $('#billing' + qid).collapse('hide');
                        } else {
                            notify('error', r && r.message ? r.message : 'Unable to record fee.');
                        }
                    }, 'json').fail(function () {
                        $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Record fee');
                        notify('error', 'Request failed. Please try again.');
                    });
                });
            }());
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
            <?php if (!empty($poll_pids)) : ?>
                <p class="small text-muted">Auto-print: when the doctor saves a new prescription for a queued patient, the latest Rx opens in a new tab every 12 seconds.</p>
                <script>
                (function () {
                    var pollUrl = <?php echo json_encode(site_url('assistant_chamber/desk_rx_poll')); ?>;
                    var doctorId = <?php echo (int) $doctor_id; ?>;
                    var pids = <?php echo json_encode($poll_pids); ?>;
                    var lastMap = {};
                    function runPoll(isTick) {
                        $.getJSON(pollUrl, { doctor_id: doctorId, patient_ids: pids.join(',') }, function (map) {
                            var k;
                            if (!isTick) {
                                for (k in map) {
                                    if (Object.prototype.hasOwnProperty.call(map, k) && map[k] > 0) {
                                        lastMap[k] = map[k] | 0;
                                    }
                                }
                                return;
                            }
                            for (k in map) {
                                if (!Object.prototype.hasOwnProperty.call(map, k)) continue;
                                var cur = map[k] | 0;
                                if (cur < 1) continue;
                                if (lastMap[k] === undefined) {
                                    lastMap[k] = cur;
                                    continue;
                                }
                                if (cur > lastMap[k]) {
                                    lastMap[k] = cur;
                                    var url = <?php echo json_encode(site_url('prescription/viewPrescriptionPrint')); ?> + '?id=' + cur;
                                    window.open(url, '_blank', 'noopener');
                                }
                            }
                        });
                    }
                    runPoll(false);
                    setInterval(function () { runPoll(true); }, 12000);
                }());
                </script>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</div>
