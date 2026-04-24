<?php defined('BASEPATH') OR exit('No direct script access allowed');
if (!isset($favorites) || !is_array($favorites)) {
    $favorites = array();
}
$fav_map = array();
if (!empty($favorites)) {
    foreach ($favorites as $f) {
        $fav_map[(string) (int) $f->id] = $f->medicine_lines_json;
    }
}
?>
<div class="content-wrapper chamber-ui">
    <section class="content-header">
        <div class="chamber-head">
            <div>
                <div class="chamber-kicker">Doctor portal</div>
                <h1>Consultation room</h1>
                <p class="chamber-subtitle mb-0">Patient context on the left, active digital prescription on the right.</p>
            </div>
            <div class="chamber-actions">
                <a class="btn btn-outline-primary" href="<?php echo site_url('ai_patient_overview'); ?>"><i class="fas fa-brain"></i> AI overview</a>
                <a class="btn btn-outline-primary" href="<?php echo site_url('ai_image_analysis'); ?>"><i class="fas fa-x-ray"></i> Image analysis</a>
                <a class="btn btn-outline-secondary" href="<?php echo site_url('doctor_chamber/dashboard'); ?>"><i class="fas fa-arrow-left"></i> Dashboard</a>
            </div>
        </div>
    </section>
    <section class="content">
        <form method="get" class="chamber-toolbar">
            <input type="number" name="patient" class="form-control mr-2" placeholder="Patient ID" value="<?php echo $patient ? (int) $patient->id : ''; ?>">
            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Load</button>
        </form>
        <div class="row">
            <div class="col-md-6">
                <div class="chamber-panel">
                    <div class="chamber-panel-header"><h3 class="chamber-panel-title">History &amp; context</h3></div>
                    <div class="chamber-panel-body">
                        <?php if ($patient) : ?>
                            <p><strong><?php echo htmlspecialchars($patient->name); ?></strong> - <?php echo htmlspecialchars($patient->phone); ?></p>
                            <a class="btn btn-sm btn-outline-primary" target="_blank" href="<?php echo site_url('patient/medicalHistory?id=' . (int) $patient->id); ?>">Medical history</a>
                            <a class="btn btn-sm btn-outline-primary" target="_blank" href="<?php echo site_url('patient/caseList'); ?>">Cases</a>
                            <a class="btn btn-sm btn-outline-primary" href="<?php echo site_url('ai_patient_overview'); ?>"><i class="fas fa-brain"></i> AI brief</a>
                        <?php else : ?>
                            <p class="text-muted">Enter a patient ID to load history links.</p>
                        <?php endif; ?>
                        <h5 class="mt-3">Live vitals</h5>
                        <div id="vitalsBox">
                            <?php if ($vitals) : ?>
                                <pre class="small"><?php echo htmlspecialchars(print_r($vitals, true)); ?></pre>
                            <?php else : ?>
                                <span class="text-muted">No vitals yet.</span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($triage)) : ?>
                            <h5 class="mt-3">Triage</h5>
                            <p class="mb-1"><strong>Problem:</strong> <?php echo htmlspecialchars(isset($triage['symptom']) ? $triage['symptom'] : ''); ?></p>
                            <p class="mb-1"><strong>Duration:</strong> <?php echo htmlspecialchars(isset($triage['duration']) ? $triage['duration'] : ''); ?></p>
                            <?php if (!empty($triage['attachments']) && is_array($triage['attachments'])) : ?>
                                <div class="small">
                                    <?php foreach ($triage['attachments'] as $att) : ?>
                                        <a target="_blank" href="<?php echo base_url($att); ?>">Attachment</a><br>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if (!empty($patient_tags)) : ?>
                            <h5 class="mt-3">Tags</h5>
                            <?php foreach ($patient_tags as $tag) : ?>
                                <span class="badge badge-warning"><?php echo htmlspecialchars(str_replace('_', ' ', $tag->tag)); ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if (!empty($past_prescriptions)) : ?>
                            <h5 class="mt-3">Recent prescriptions</h5>
                            <ul class="small pl-3">
                                <?php $rx_count = 0; foreach ($past_prescriptions as $rx) : if ($rx_count++ >= 5) break; ?>
                                    <li>
                                        <?php echo !empty($rx->date) ? htmlspecialchars(date('d-m-Y', $rx->date)) : 'Prescription'; ?>
                                        <a target="_blank" href="<?php echo site_url('prescription/viewPrescriptionPrint?id=' . (int) $rx->id); ?>">PDF</a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chamber-panel">
                    <div class="chamber-panel-header"><h3 class="chamber-panel-title">E-Pad (prescription)</h3></div>
                    <div class="chamber-panel-body">
                        <?php if ($patient) : ?>
                            <p class="small text-muted mb-2">Full composer embedded below (same workflow as the main prescription screen).</p>
                            <div class="border rounded bg-white" style="min-height:52rem;">
                                <iframe class="w-100" style="min-height:52rem;border:0;" title="Prescription composer"
                                    src="<?php echo site_url('prescription/addPrescriptionView?embed=1&patient=' . (int) $patient->id); ?>"></iframe>
                            </div>
                            <p class="small mt-2 mb-0"><a class="text-muted" target="_blank" href="<?php echo site_url('prescription/addPrescriptionView?patient=' . (int) $patient->id); ?>">Open in full window</a> if the frame is clipped.</p>
                        <?php else : ?>
                            <p class="text-muted">Load a patient first.</p>
                        <?php endif; ?>
                        <?php if (!empty($favorites)) : ?>
                        <hr>
                        <h5>Favorites</h5>
                        <p class="small text-muted">Click to append lines to the scratch pad, then copy into the composer.</p>
                        <ul class="list-unstyled small mb-2" id="favList">
                            <?php foreach ($favorites as $f) : ?>
                            <li class="mb-1">
                                <button type="button" class="btn btn-sm btn-outline-secondary fav-btn" data-fav="<?php echo (int) $f->id; ?>"><?php echo htmlspecialchars($f->label); ?></button>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <label class="small text-muted">Scratch pad</label>
                        <textarea id="rxScratch" class="form-control small" rows="4" placeholder="Favorite lines appear here..."></textarea>
                        <?php endif; ?>
                        <hr>
                        <h5>Drug search</h5>
                        <input id="drugq" class="form-control mb-2" placeholder="Name, generic, or company">
                        <ul id="drugout" class="list-unstyled small"></ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php if (!empty($patient)) : ?>
<script>
(function(){
    var pid = <?php echo (int) $patient->id; ?>;
    var favMap = <?php echo !empty($favorites) ? json_encode($fav_map) : '{}'; ?>;
    function poll(){
        $.getJSON('<?php echo site_url('doctor_chamber/vitals_json'); ?>', { patient: pid }, function(r){
            if (r.vitals) { $('#vitalsBox').html('<pre class="small">'+JSON.stringify(r.vitals, null, 2)+'</pre>'); }
        });
    }
    setInterval(poll, 8000);
    $('#drugq').on('keyup', function(){
        var t = $(this).val();
        if (t.length < 2) return;
        $.getJSON('<?php echo site_url('doctor_chamber/drug_search_json'); ?>', { term: t }, function(rows){
            var h = '';
            (rows||[]).forEach(function(x){
                var lab = $('<div>').text(x.label || '').html();
                h += '<li><button type="button" class="btn btn-link btn-sm p-0 text-left drug-pick">'+lab+'</button></li>';
            });
            $('#drugout').html(h);
        });
    });
    $(document).on('click', '.fav-btn', function(){
        var id = String($(this).data('fav'));
        var raw = favMap[id] || '';
        var txt = raw;
        try { var j = JSON.parse(raw); if (Array.isArray(j)) txt = j.join(String.fromCharCode(10)); } catch(e) {}
        var ta = $('#rxScratch');
        ta.val((ta.val() ? ta.val() + String.fromCharCode(10) : '') + txt);
    });
    $(document).on('click', '.drug-pick', function(){
        var n = $(this).text();
        var ta = $('#rxScratch');
        ta.val((ta.val() ? ta.val() + String.fromCharCode(10) : '') + n);
    });
})();
</script>
<?php endif; ?>

