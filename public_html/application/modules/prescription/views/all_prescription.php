<?php
$CI = get_instance();
if (!isset($prescription)) {
    $prescription = null;
}
?>
<div class="content-wrapper bg-light">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('prescription'),
        'icon' => 'fas fa-prescription text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('prescription'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('All the prescriptions names and related informations'); ?></h3>
                            <?php if ($this->ion_auth->in_group(array('admin', 'Doctor'))) { ?>
                                <a href="prescription/addPrescriptionView" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus mr-1"></i> <?php echo lang('add_new'); ?> <?php echo lang('prescription'); ?>
                                </a>
                            <?php } ?>
                        </div>

                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="editable-sample1" style="width:100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th><?php echo lang('id'); ?></th>
                                            <th><?php echo lang('date'); ?></th>
                                            <th><?php echo lang('doctor'); ?></th>
                                            <th><?php echo lang('patient'); ?></th>
                                            <th><?php echo lang('medicine'); ?></th>
                                            <th class="no-print"><?php echo lang('options'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>










<!--main content end-->
<!--footer start-->



<?php
$current_user = $this->ion_auth->get_user_id();
if ($this->ion_auth->in_group('Doctor')) {
    $doctor_id = $this->db->get_where('doctor', array('ion_user_id' => $current_user))->row()->id;
}
?>

<!-- Add Prescription Modal-->
<div class="modal fade" id="myModa3" role="dialog" aria-labelledby="myModalAddPrescriptionLabel" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="myModalAddPrescriptionLabel">
                    <i class="fas fa-prescription-bottle mr-2"></i>
                    <?php echo lang('add_prescription'); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form role="form" action="prescription/addNewPrescription" class="clearfix" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">

                    <div class="form-group col-md-4">
                        <label for="exampleInputEmail1"> <?php echo lang('date'); ?></label>
                        <input type="text" class="form-control form-control-inline input-medium default-date-picker" name="date" value='' placeholder="">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="exampleInputEmail1"> <?php echo lang('doctor'); ?></label>
                        <select class="form-control form-control-lg m-bot15 js-example-basic-single" name="doctor" value=''>
                            <option value="">Select .....</option>
                            <?php foreach ($doctors as $doctor) { ?>
                                <option value="<?php echo $doctor->id; ?>"><?php echo $doctor->name; ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="exampleInputEmail1"> <?php echo lang('patient'); ?></label>
                        <select class="form-control form-control-lg m-bot15 js-example-basic-single" name="patient" value=''>
                            <option value="">Select .....</option>
                            <?php foreach ($patients as $patientss) { ?>
                                <option value="<?php echo $patientss->id; ?>"><?php echo $patientss->name; ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('history'); ?></label>
                        <div class="col-md-9">
                            <textarea class="ckeditor form-control" name="symptom" value="" rows="10"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('medication'); ?></label>
                        <div class="col-md-9">
                            <textarea class="ckeditor form-control" name="medicine" value="" rows="10"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('note'); ?></label>
                        <div class="col-md-9">
                            <textarea class="ckeditor form-control" name="note" value="" rows="10"></textarea>
                        </div>
                    </div>

                    <input type="hidden" name="patient_id" value=''>
                    <input type="hidden" name="admin" value='admin'>
                    <input type="hidden" name="id" value=''>
                    <section class="">
                        <button type="submit" name="submit" class="btn btn-primary submit_button float-right"><?php echo lang('submit'); ?></button>
                    </section>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<div class="modal fade" id="myModal5" role="dialog" aria-labelledby="myModalEditPrescriptionLabel" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="myModalEditPrescriptionLabel"><?php echo lang('edit_prescription'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form role="form" id="prescriptionEditForm" class="clearfix" action="prescription/addNewPrescription" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="form-group col-md-4">
                        <label for="exampleInputEmail1"> <?php echo lang('date'); ?></label>
                        <input type="text" class="form-control form-control-inline input-medium default-date-picker" name="date" value='' placeholder="">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="exampleInputEmail1"> <?php echo lang('doctor'); ?></label>
                        <select class="form-control form-control-lg m-bot15 js-example-basic-single doctor" name="doctor" value=''>
                            <option value="">Select .....</option>
                            <?php foreach ($doctors as $doctor) { ?>
                                <option value="<?php echo $doctor->id; ?>" <?php
                                                                            if ($prescription && !empty($prescription->doctor) && (string) $prescription->doctor === (string) $doctor->id) {
                                                                                echo 'selected';
                                                                            }
                                                                            ?>><?php echo $doctor->name; ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="exampleInputEmail1"> <?php echo lang('patient'); ?></label>
                        <select class="form-control form-control-lg m-bot15 js-example-basic-single patient" name="patient" value=''>
                            <option value="">Select .....</option>
                            <?php foreach ($patients as $patientss) { ?>
                                <option value="<?php echo $patientss->id; ?>" <?php
                                                                                if ($prescription && !empty($prescription->patient) && (string) $prescription->patient === (string) $patientss->id) {
                                                                                    echo 'selected';
                                                                                }
                                                                                ?>><?php echo $patientss->name; ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('history'); ?></label>
                        <div class="col-md-9">
                            <textarea class="ckeditor form-control" id="editor1" name="symptom" value="" rows="10"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('medication'); ?></label>
                        <div class="col-md-9">
                            <textarea class="ckeditor form-control" id="editor2" name="medicine" value="" rows="10"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('note'); ?></label>
                        <div class="col-md-9">
                            <textarea class="ckeditor form-control" id="editor3" name="note" value="" rows="10"></textarea>
                        </div>
                    </div>

                    <input type="hidden" name="admin" value='admin'>
                    <input type="hidden" name="id" value=''>
                    <section class="">
                        <button type="submit" name="submit" class="btn btn-primary submit_button float-right"><?php echo lang('submit'); ?></button>
                    </section>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- Edit Prescription Modal-->

<!-- Quick View Prescription Modal -->
<style>
.prescription-quick-view .info-group {
    margin-bottom: 8px;
}

.prescription-quick-view .info-group label {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0;
    display: inline-block;
    min-width: 80px;
}

.prescription-quick-view .card {
    border-radius: 8px;
}

.prescription-quick-view .card-header {
    border-bottom: 1px solid #dee2e6;
    padding: 10px 15px;
}

.prescription-quick-view .card-header h6 {
    font-size: 0.95rem;
}

.prescription-quick-view .symptom-content,
.prescription-quick-view .note-content,
.prescription-quick-view .advice-content {
    font-size: 0.9rem;
    line-height: 1.6;
}

.prescription-quick-view .table th {
    font-size: 0.85rem;
    font-weight: 600;
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.prescription-quick-view .table td {
    font-size: 0.85rem;
    vertical-align: middle;
}

#quickViewModal .modal-dialog {
    max-width: 800px;
}

.quick-view-btn {
    white-space: nowrap;
}
</style>

<div class="modal fade" id="quickViewModal" role="dialog" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="quickViewModalLabel">
                    <i class="fas fa-search-plus mr-2"></i>
                    <?php echo lang('quick_view_prescription'); ?>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="quickViewContent">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2"><?php echo lang('loading_prescription_details'); ?></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i><?php echo lang('close'); ?>
                </button>
                <button type="button" class="btn btn-primary" id="printQuickView">
                    <i class="fas fa-print mr-1"></i><?php echo lang('print'); ?>
                </button>
                <button type="button" class="btn btn-success" id="viewFullPrescription">
                    <i class="fas fa-eye mr-1"></i><?php echo lang('view_full'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
    var prescriptionQuickViewLang = <?php echo json_encode(array(
        'loading_prescription_details' => lang('loading_prescription_details'),
        'medicine' => lang('medicine'),
        'dosage' => lang('dosage'),
        'frequency' => lang('frequency'),
        'instruction' => lang('instruction'),
        'days' => lang('days'),
        'no_medicines_prescribed' => lang('no_medicines_prescribed'),
        'prescription' => lang('prescription'),
        'date' => lang('date'),
        'patient' => lang('patient'),
        'doctor' => lang('doctor'),
        'patient_id' => lang('patient_id'),
        'history_and_symptoms' => lang('history_and_symptoms'),
        'prescribed_medicines' => lang('prescribed_medicines'),
        'notes' => lang('notes'),
        'advice' => lang('advice'),
        'symptom' => lang('symptom'),
        'quick_view_load_failed' => lang('quick_view_load_failed'),
    ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>;
</script>
<script src="common/extranal/js/prescription/all_prescription.js"></script>