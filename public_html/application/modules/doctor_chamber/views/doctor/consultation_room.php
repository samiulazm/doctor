<?php defined('BASEPATH') OR exit('No direct script access allowed');
if (!isset($favorites) || !is_array($favorites)) {
    $favorites = array();
}
if (!isset($rx_templates) || !is_array($rx_templates)) {
    $rx_templates = $favorites;
}
$fav_map = array();
foreach ($favorites as $f) {
    $fav_map[(string) (int) $f->id] = $f->medicine_lines_json;
}
$vitals_arr = $vitals ? (is_object($vitals) ? get_object_vars($vitals) : (array) $vitals) : array();
$vital_cards = array(
    'bp_sys' => 'SYS',
    'bp_dia' => 'DIA',
    'pulse' => 'Pulse',
    'weight_kg' => 'Weight',
);
?>
<div class="chamber-ui">
    <section class="content-header">
        <div class="chamber-head">
            <div>
                <div class="chamber-kicker">Doctor portal</div>
                <h1>Consultation room</h1>
                <p class="chamber-subtitle mb-0">Patient context on the left, active prescription on the right.</p>
            </div>
            <div class="chamber-actions">
                <a class="btn btn-sm btn-outline-primary" href="<?php echo site_url('ai_patient_overview'); ?>">
                    <i class="fas fa-brain mr-1"></i> AI overview
                </a>
                <a class="btn btn-sm btn-outline-primary" href="<?php echo site_url('ai_image_analysis'); ?>">
                    <i class="fas fa-x-ray mr-1"></i> Image analysis
                </a>
                <a class="btn btn-sm btn-outline-secondary" href="<?php echo site_url('doctor_chamber/dashboard'); ?>">
                    <i class="fas fa-arrow-left mr-1"></i> Dashboard
                </a>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="chamber-toolbar">
            <div class="chamber-radio-group mr-3">
                <label class="chamber-radio-pill">
                    <input type="radio" name="searchMode" value="id" checked>
                    <span>By patient ID</span>
                </label>
                <label class="chamber-radio-pill">
                    <input type="radio" name="searchMode" value="date">
                    <span>By date</span>
                </label>
            </div>
            <input type="number" id="searchPatientId" class="form-control mr-2" placeholder="Patient ID" style="max-width:160px;" value="<?php echo $patient ? (int) $patient->id : ''; ?>">
            <input type="date" id="searchDate" class="form-control mr-2" style="max-width:180px; display:none;" value="<?php echo date('Y-m-d'); ?>">
            <button class="btn btn-primary" id="btnSearchPatient" type="button">
                <i class="fas fa-search mr-1"></i> Load patient
            </button>
        </div>

        <div class="row">
            <div class="col-md-5">
                <div class="chamber-panel">
                    <div class="chamber-panel-header">
                        <h3 class="chamber-panel-title"><i class="fas fa-notes-medical mr-2 text-muted"></i>Patient history</h3>
                    </div>
                    <div class="chamber-panel-body">
                        <div id="searchResultsList" class="mb-3"></div>

                        <?php if ($patient) : ?>
                            <div class="d-flex align-items-start justify-content-between flex-wrap mb-3">
                                <div>
                                    <h4 class="h6 mb-1"><?php echo htmlspecialchars((string) $patient->name, ENT_QUOTES, 'UTF-8'); ?></h4>
                                    <div class="small text-muted">
                                        ID #<?php echo (int) $patient->id; ?>
                                        <?php if (!empty($patient->phone)) : ?>
                                            - <?php echo htmlspecialchars((string) $patient->phone, ENT_QUOTES, 'UTF-8'); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <a class="btn btn-xs btn-outline-primary" target="_blank" href="<?php echo site_url('patient/medicalHistory?id=' . (int) $patient->id); ?>">
                                    <i class="fas fa-file-medical-alt mr-1"></i> History
                                </a>
                            </div>

                            <?php if (!empty($patient_tags)) : ?>
                                <div class="mb-3">
                                    <?php foreach ($patient_tags as $tag) : ?>
                                        <span class="chamber-status <?php echo htmlspecialchars((string) $tag->tag, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars(str_replace('_', ' ', (string) $tag->tag), ENT_QUOTES, 'UTF-8'); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php else : ?>
                            <div class="chamber-empty mb-3">Load a patient to view history, vitals, and prescriptions.</div>
                        <?php endif; ?>

                        <h5 class="mt-3 mb-2">Live vitals</h5>
                        <div id="vitalsBox">
                            <?php if (!empty($vitals_arr)) : ?>
                                <div class="chamber-vitals-grid">
                                    <?php foreach ($vital_cards as $vk => $vl) : ?>
                                        <div class="chamber-vital">
                                            <div class="chamber-vital-label"><?php echo htmlspecialchars($vl, ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="chamber-vital-value"><?php echo isset($vitals_arr[$vk]) && $vitals_arr[$vk] !== '' ? htmlspecialchars((string) $vitals_arr[$vk], ENT_QUOTES, 'UTF-8') : '-'; ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else : ?>
                                <span class="text-muted small">No vitals yet.</span>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($triage)) : ?>
                            <h5 class="mt-3 mb-2">Triage</h5>
                            <p class="mb-1"><strong>Problem:</strong> <?php echo htmlspecialchars(isset($triage['symptom']) ? (string) $triage['symptom'] : '', ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="mb-1"><strong>Duration:</strong> <?php echo htmlspecialchars(isset($triage['duration']) ? (string) $triage['duration'] : '', ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php if (!empty($triage['attachments']) && is_array($triage['attachments'])) : ?>
                                <div class="small mt-2">
                                    <?php foreach ($triage['attachments'] as $att) : ?>
                                        <a target="_blank" href="<?php echo base_url($att); ?>">Attachment</a><br>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <h5 class="mt-3 mb-2">Previous prescriptions</h5>
                        <?php if (!empty($past_prescriptions)) : ?>
                            <ul class="list-unstyled small mb-0">
                                <?php $rx_count = 0; foreach ($past_prescriptions as $rx) : if ($rx_count++ >= 8) break; ?>
                                    <li class="py-2 border-bottom d-flex align-items-center justify-content-between">
                                        <span><?php echo !empty($rx->date) ? htmlspecialchars(date('d-m-Y', (int) $rx->date), ENT_QUOTES, 'UTF-8') : 'Prescription'; ?></span>
                                        <a target="_blank" href="<?php echo site_url('prescription/viewPrescriptionPrint?id=' . (int) $rx->id); ?>">View</a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else : ?>
                            <p class="text-muted small mb-0">No previous prescriptions found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="chamber-panel">
                    <div class="chamber-panel-header d-flex align-items-center justify-content-between">
                        <h3 class="chamber-panel-title"><i class="fas fa-prescription mr-2 text-muted"></i>E-Pad</h3>
                        <?php if (!empty($rx_templates)) : ?>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="templateDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-file-medical-alt mr-1"></i> Templates
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="templateDropdown">
                                    <?php foreach ($rx_templates as $tpl) : ?>
                                        <a class="dropdown-item tpl-apply" href="#" data-tpl-id="<?php echo (int) $tpl->id; ?>" data-tpl-label="<?php echo htmlspecialchars((string) $tpl->label, ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php echo htmlspecialchars((string) $tpl->label, ENT_QUOTES, 'UTF-8'); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="chamber-panel-body" id="epadBody">
                        <?php if ($patient) : ?>
                            <div class="border rounded bg-white">
                                <iframe id="rxFrame" class="w-100" style="min-height:52rem; border:0;" title="Prescription composer" src="<?php echo site_url('prescription/addPrescriptionView?embed=1&patient=' . (int) $patient->id); ?>"></iframe>
                            </div>
                            <p class="small mt-2 mb-0">
                                <a class="text-muted" target="_blank" href="<?php echo site_url('prescription/addPrescriptionView?patient=' . (int) $patient->id); ?>">Open in full window</a> if the frame is clipped.
                            </p>
                        <?php else : ?>
                            <div class="chamber-empty">
                                <i class="fas fa-file-prescription fa-2x mb-2 d-block"></i>
                                Load a patient first to open the prescription pad.
                            </div>
                        <?php endif; ?>

                        <div class="mt-3">
                            <label class="small font-weight-bold text-muted text-uppercase" style="letter-spacing:.04em;">
                                <i class="fas fa-pills mr-1"></i> Medicine search
                            </label>
                            <input id="drugq" class="form-control mb-2" placeholder="Name, generic, or company..." autocomplete="off">
                            <ul id="drugout" class="list-unstyled small mb-0"></ul>
                        </div>

                        <div class="mt-3">
                            <?php if (!empty($favorites)) : ?>
                                <label class="small font-weight-bold text-muted text-uppercase" style="letter-spacing:.04em;">
                                    <i class="fas fa-star mr-1"></i> Favorites
                                </label>
                                <div class="chamber-btn-row mb-2">
                                    <?php foreach ($favorites as $f) : ?>
                                        <button type="button" class="btn btn-sm btn-outline-secondary fav-btn" data-fav="<?php echo (int) $f->id; ?>">
                                            <?php echo htmlspecialchars((string) $f->label, ENT_QUOTES, 'UTF-8'); ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <label class="small text-muted">Scratch pad</label>
                            <textarea id="rxScratch" class="form-control small" rows="4" placeholder="Click a favorite or drug to append here..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($patient) : ?>
            <div class="chamber-panel" id="actionBar">
                <div class="chamber-panel-body d-flex align-items-center justify-content-between flex-wrap py-2">
                    <span class="text-muted small">
                        Prescription for <strong><?php echo htmlspecialchars((string) $patient->name, ENT_QUOTES, 'UTF-8'); ?></strong>
                    </span>
                    <div class="chamber-actions">
                        <button type="button" class="btn btn-secondary" id="btnSaveDraft">
                            <i class="fas fa-save mr-1"></i> Save draft
                        </button>
                        <button type="button" class="btn btn-primary" id="btnSaveAndPrint">
                            <i class="fas fa-print mr-1"></i> Save and print
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>
</div>

<script>
(function () {
    var BASE_URL = '<?php echo rtrim(site_url(), '/'); ?>/';
    var patientId = <?php echo $patient ? (int) $patient->id : 0; ?>;
    var favMap = <?php echo json_encode($fav_map); ?>;
    var drugTimer = null;

    function escText(s) {
        return $('<div>').text(s === null || s === undefined ? '' : String(s)).html();
    }

    function appendScratch(text) {
        var ta = $('#rxScratch');
        ta.val((ta.val() ? ta.val() + String.fromCharCode(10) : '') + text);
    }

    $('input[name="searchMode"]').on('change', function () {
        var mode = $(this).val();
        $('#searchPatientId').toggle(mode === 'id');
        $('#searchDate').toggle(mode === 'date');
    });

    $('#btnSearchPatient').on('click', function () {
        var mode = $('input[name="searchMode"]:checked').val();
        var params = mode === 'id'
            ? { mode: 'id', id: $('#searchPatientId').val() }
            : { mode: 'date', date: $('#searchDate').val() };

        $.getJSON(BASE_URL + 'doctor_chamber/search_json', params, function (r) {
            var rows = (r && r.patients) ? r.patients : [];
            if (rows.length === 0) {
                $('#searchResultsList').html('<p class="text-muted small">No patients found for this search.</p>');
                return;
            }
            var html = '<p class="small text-muted mb-2">Select a patient to load their history:</p><ul class="list-unstyled mb-0">';
            rows.forEach(function (p) {
                var id = parseInt(p.id, 10);
                html += '<li class="mb-1"><button type="button" class="btn btn-sm btn-outline-primary patient-pick w-100 text-left" data-pid="' + id + '">' +
                    escText((p.name || ('#' + id)) + (p.phone ? (' - ' + p.phone) : '')) + '</button></li>';
            });
            html += '</ul>';
            $('#searchResultsList').html(html);
        }).fail(function () {
            $('#searchResultsList').html('<p class="text-danger small">Unable to search. Please try again.</p>');
        });
    });

    $(document).on('click', '.patient-pick', function () {
        var pid = parseInt($(this).data('pid'), 10);
        if (pid > 0) {
            window.location.href = BASE_URL + 'doctor_chamber/consultation_room?patient=' + pid;
        }
    });

    function renderVitals(vitals) {
        var fields = [
            ['bp_sys', 'SYS'],
            ['bp_dia', 'DIA'],
            ['pulse', 'Pulse'],
            ['weight_kg', 'Weight']
        ];
        var html = '<div class="chamber-vitals-grid">';
        fields.forEach(function (field) {
            var val = vitals ? vitals[field[0]] : '';
            val = (val === undefined || val === null || val === '') ? '-' : escText(val);
            html += '<div class="chamber-vital"><div class="chamber-vital-label">' + field[1] + '</div><div class="chamber-vital-value">' + val + '</div></div>';
        });
        html += '</div>';
        $('#vitalsBox').html(html);
    }

    function pollVitals() {
        if (!patientId) return;
        $.getJSON(BASE_URL + 'doctor_chamber/vitals_json', { patient: patientId }, function (r) {
            if (r && r.vitals) {
                renderVitals(r.vitals);
            }
        });
    }

    $('#drugq').on('keyup', function () {
        var term = $(this).val().trim();
        clearTimeout(drugTimer);
        if (term.length < 2) {
            $('#drugout').empty();
            return;
        }
        drugTimer = setTimeout(function () {
            $.getJSON(BASE_URL + 'doctor_chamber/drug_search_json', { term: term }, function (rows) {
                var html = '';
                (rows || []).forEach(function (x) {
                    html += '<li><button type="button" class="btn btn-link btn-sm p-0 text-left drug-pick">' + escText(x.label || '') + '</button></li>';
                });
                $('#drugout').html(html);
            }).fail(function () {
                $('#drugout').empty();
            });
        }, 280);
    });

    $(document).on('click', '.drug-pick', function () {
        appendScratch($(this).text().trim());
        $('#drugout').empty();
        $('#drugq').val('');
    });

    $(document).on('click', '.fav-btn, .tpl-apply', function (e) {
        e.preventDefault();
        var id = String($(this).data('fav') || $(this).data('tpl-id') || '');
        var raw = favMap[id] || '';
        var text = raw || ($(this).data('tpl-label') || $(this).text()).trim();
        try {
            var parsed = JSON.parse(raw);
            if (Array.isArray(parsed)) {
                text = parsed.join(String.fromCharCode(10));
            }
        } catch (err) {}
        if (text) {
            appendScratch(text);
        }
    });

    function setActionBusy(isBusy) {
        $('#btnSaveDraft, #btnSaveAndPrint').prop('disabled', isBusy).toggleClass('disabled', isBusy);
    }

    function setHidden(form, name, value) {
        var input = form.querySelector('input[name="' + name + '"]');
        if (!input) {
            input = form.ownerDocument.createElement('input');
            input.type = 'hidden';
            input.name = name;
            form.appendChild(input);
        }
        input.value = value;
    }

    function submitFrame(printAfter) {
        var frame = document.getElementById('rxFrame');
        if (!frame || !frame.contentWindow || !frame.contentWindow.document) return;
        var doc = frame.contentWindow.document;
        var form = doc.getElementById('addForm') || doc.querySelector('form');
        if (!form) return;
        setActionBusy(true);
        setHidden(form, 'embed', '1');
        setHidden(form, 'print_after', printAfter ? '1' : '0');
        form.submit();
    }

    $('#btnSaveDraft').on('click', function () {
        submitFrame(false);
    });

    $('#btnSaveAndPrint').on('click', function () {
        submitFrame(true);
    });

    window.addEventListener('message', function (event) {
        var data = event.data || {};
        if (data.type !== 'rx:saved') return;
        setActionBusy(false);
        if (data.print_url) {
            window.open(data.print_url, '_blank');
        }
    });

    if (patientId) {
        setInterval(pollVitals, 8000);
    }
}());
</script>
