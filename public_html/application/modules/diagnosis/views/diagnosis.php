<?php
$CI = get_instance();
if (!isset($diagnosis)) {
    $diagnosis = null;
}
?>
<div class="content-wrapper bg-light">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('diagnosis_list'),
        'icon' => 'fas fa-stethoscope text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('diagnosis_list'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('Comprehensive List of Diagnosis'); ?></h3>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal">
                                <i class="fa fa-plus mr-1"></i> <?php echo lang('add_diagnosis'); ?>
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="dt-diagnosis" data-legacy-table="editable-sample" style="width:100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo lang('disease'); ?> <?php echo lang('name'); ?></th>
                                            <th><?php echo lang('icd 10'); ?> <?php echo lang('code'); ?></th>
                                            <th><?php echo lang('description'); ?></th>
                                            <th><?php echo lang('Disease With Outbreak Potential'); ?></th>
                                            <th><?php echo lang('Maximum Expected Number Of Patient In A Week'); ?></th>
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

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalAddLabel" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="myModalAddLabel"><?php echo lang('add_diagnosis'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form role="form" action="diagnosis/addNew" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">

                    <div class="form-group">
                        <label for="diagAddName"><?php echo lang('disease'); ?> <?php echo lang('name'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="name" id="diagAddName" value="<?php echo htmlspecialchars(
                            (string) set_value('name', $diagnosis && isset($diagnosis->name) ? $diagnosis->name : ''),
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="diagAddCode"><?php echo lang('icd 10'); ?> <?php echo lang('code'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="code" id="diagAddCode" value="<?php echo htmlspecialchars(
                            (string) set_value('code', $diagnosis && isset($diagnosis->code) ? $diagnosis->code : ''),
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="editor1"><?php echo lang('description'); ?></label>
                        <textarea class="form-control col-sm-9 ckeditor" id="editor1" name="description" rows="10" cols="20"><?php echo set_value(
                            'description',
                            $diagnosis && isset($diagnosis->description) ? $diagnosis->description : ''
                        ); ?></textarea>
                    </div>
                    <div class="form-group d-flex disease_div">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input disease_with_outbreak_potential" id="diseaseOutbreakPotential" name="disease_with_outbreak_potential" value="1">
                            <label class="custom-control-label mr-2 mb-2" for="diseaseOutbreakPotential"><?php echo lang('Disease With Outbreak Potential'); ?></label>
                        </div>
                    </div>
                    <div id="maximum">
                        <div class="form-group">
                            <label for="diagAddMax"><?php echo lang('Maximum Expected Number Of Patient In A Week'); ?></label>
                            <input type="number" class="form-control form-control-lg" name="maximum_expected_number_of_patient_in_a_week" id="diagAddMax" value="<?php echo htmlspecialchars(
                                (string) set_value(
                                    'maximum_expected_number_of_patient_in_a_week',
                                    $diagnosis && isset($diagnosis->maximum_expected_number_of_patient_in_a_week) ? $diagnosis->maximum_expected_number_of_patient_in_a_week : ''
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>" placeholder="">
                        </div>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary float-right"><?php echo lang('submit'); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="myModalEditLabel" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="myModalEditLabel"><?php echo lang('edit_diagnosis'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form role="form" id="editDiagnosisForm" class="clearfix" action="diagnosis/addNew" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="id" value="">

                    <div class="form-group">
                        <label for="diagEditName"><?php echo lang('disease'); ?> <?php echo lang('name'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="name" id="diagEditName" value="<?php echo htmlspecialchars(
                            (string) set_value('name', $diagnosis && isset($diagnosis->name) ? $diagnosis->name : ''),
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="diagEditCode"><?php echo lang('icd 10'); ?> <?php echo lang('code'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="code" id="diagEditCode" value="<?php echo htmlspecialchars(
                            (string) set_value('code', $diagnosis && isset($diagnosis->code) ? $diagnosis->code : ''),
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="editor3"><?php echo lang('description'); ?></label>
                        <textarea class="form-control form-control-lg" id="editor3" name="description" rows="10" cols="20"><?php echo set_value(
                            'description',
                            $diagnosis && isset($diagnosis->description) ? $diagnosis->description : ''
                        ); ?></textarea>
                    </div>
                    <div class="form-group d-flex disease_div1">
                        <input type="checkbox" name="disease_with_outbreak_potential" value="1" id="diseaseOutbreakEdit" class="disease_with_outbreak_potential1">
                        <label for="diseaseOutbreakEdit" class="ml-2"> <?php echo lang('Disease With Outbreak Potential'); ?> </label>
                    </div>
                    <div id="maximum1">
                        <div class="form-group">
                            <label for="diagEditMax"><?php echo lang('Maximum Expected Number Of Patient In A Week'); ?></label>
                            <input type="number" class="form-control form-control-lg" name="maximum_expected_number_of_patient_in_a_week" id="diagEditMax" value="<?php echo htmlspecialchars(
                                (string) set_value(
                                    'maximum_expected_number_of_patient_in_a_week',
                                    $diagnosis && isset($diagnosis->maximum_expected_number_of_patient_in_a_week) ? $diagnosis->maximum_expected_number_of_patient_in_a_week : ''
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>" placeholder="">
                        </div>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary float-right"><?php echo lang('submit'); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
    var select_doctor = <?php echo json_encode(lang('select_doctor')); ?>;
</script>
<script src="common/assets/tinymce/tinymce.min.js"></script>
<script src="common/extranal/js/diagnosis.js"></script>

<script>
    $(document).ready(function() {
        $("#maximum").hide();

        $('.disease_with_outbreak_potential').on('change', function() {
            if ($(this).is(':checked')) {
                $('#maximum').show();
            } else {
                $('#maximum').hide();
            }
        });

        $('.disease_with_outbreak_potential1').on('change', function() {
            if ($(this).is(':checked')) {
                $('#maximum1').show();
            } else {
                $('#maximum1').hide();
            }
        });
    });
</script>
