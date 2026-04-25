<?php
$CI = get_instance();
$currency_label = (isset($settings) && !empty($settings->currency)) ? (string) $settings->currency : '';
$is_admin = $this->ion_auth->in_group('admin');
?>
<div class="content-wrapper bg-light">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('doctor_visit'),
        'icon' => 'fas fa-user-md text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('doctor'), 'url' => 'doctor'),
            array('label' => lang('doctor_visit'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('Comprehensive List of Visit Types and Associated Charges for Each Doctor'); ?></h3>
                            <?php if ($is_admin) { ?>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal">
                                <i class="fa fa-plus mr-1"></i> <?php echo lang('add_doctor_visit'); ?>
                            </button>
                            <?php } ?>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="editable-sample" style="width:100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo lang('doctor'); ?> <?php echo lang('name'); ?></th>
                                            <th><?php echo lang('visit'); ?> <?php echo lang('description'); ?></th>
                                            <th><?php echo lang('visit'); ?> <?php echo lang('charges'); ?></th>
                                            <th><?php echo lang('status'); ?></th>
                                            <?php if ($is_admin) { ?>
                                            <th class="no-print"><?php echo lang('options'); ?></th>
                                            <?php } ?>
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
                <h4 class="modal-title font-weight-bold" id="myModalAddLabel"><?php echo lang('add_doctor_visit'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form role="form" action="doctorvisit/addNew" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label for="adoctors"><?php echo lang('doctor'); ?> <span class="text-danger">*</span></label>
                        <select class="form-control form-control-lg select2" id="adoctors" name="doctor" required>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="visit_description"><?php echo lang('visit'); ?> <?php echo lang('description'); ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" name="visit_description" id="visit_description" required>
                    </div>
                    <div class="form-group">
                        <label for="visit_charges"><?php echo lang('visit'); ?> <?php echo lang('charges'); ?> <span class="text-danger">*</span></label>
                        <input type="number" min="1" class="form-control form-control-lg" name="visit_charges" id="visit_charges" placeholder="<?php echo htmlspecialchars($currency_label, ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="status"><?php echo lang('status'); ?> <span class="text-danger">*</span></label>
                        <select class="form-control form-control-lg select2" name="status" id="status">
                            <option value="active"><?php echo lang('active'); ?></option>
                            <option value="disable"><?php echo lang('in_active'); ?></option>
                        </select>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" name="submit" class="btn btn-primary"><?php echo lang('submit'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="myModalEditLabel" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="myModalEditLabel"><?php echo lang('edit_doctor_visit'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form role="form" id="editDoctorvisitForm" action="doctorvisit/addNew" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label for="adoctors1"><?php echo lang('doctor'); ?> <span class="text-danger">*</span></label>
                        <select class="form-control form-control-lg select2" id="adoctors1" name="doctor" required>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="visit_description_edit"><?php echo lang('visit'); ?> <?php echo lang('description'); ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" name="visit_description" id="visit_description_edit" required>
                    </div>
                    <div class="form-group">
                        <label for="visit_charges_edit"><?php echo lang('visit'); ?> <?php echo lang('charges'); ?> <span class="text-danger">*</span></label>
                        <input type="number" min="1" class="form-control form-control-lg" name="visit_charges" id="visit_charges_edit" placeholder="<?php echo htmlspecialchars($currency_label, ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="status_edit"><?php echo lang('status'); ?> <span class="text-danger">*</span></label>
                        <select class="form-control form-control-lg select2" name="status" id="status_edit">
                            <option value="active"><?php echo lang('active'); ?></option>
                            <option value="disable"><?php echo lang('in_active'); ?></option>
                        </select>
                    </div>
                    <input type="hidden" name="id" value="">
                    <div class="form-group text-right">
                        <button type="submit" name="submit" class="btn btn-primary"><?php echo lang('submit'); ?></button>
                    </div>
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
<script src="common/extranal/js/doctor/doctor_visit.js"></script>
