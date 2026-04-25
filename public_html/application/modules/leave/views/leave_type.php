<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('leave_type'),
        'icon' => 'fas fa-calendar-alt text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('leave'), 'url' => 'leave'),
            array('label' => lang('leave_type'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-end mb-3">
                <a data-toggle="modal" data-target="#myModal" href="#myModal" class="btn btn-sm btn-success">
                    <i class="fa fa-plus mr-1"></i> <?php echo lang('add_new'); ?> <?php echo lang('leave_type'); ?>
                </a>
            </div>
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('All the Leave type names and related informations'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle text-sm datatables mb-0" id="editable-sample" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-uppercase"><?php echo lang('type'); ?></th>
                                            <th class="text-uppercase no-print"><?php echo lang('options'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="addLeaveTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h4 class="modal-title text-white font-weight-bold" id="addLeaveTypeModalLabel"><?php echo lang('add_new_leave'); ?></h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body">
                <form role="form" action="leave/addNewLeaveType" class="clearfix" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label><?php echo lang('name'); ?> &ast;</label>
                        <input type="text" class="form-control form-control-lg" name="name" value="" placeholder="" required>
                    </div>
                    <div class="form-group mb-0">
                        <button type="submit" name="submit" class="btn btn-primary btn-block"><?php echo lang('submit'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="editLeaveTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h4 class="modal-title text-white font-weight-bold" id="editLeaveTypeModalLabel"><?php echo lang('edit_leave_type'); ?></h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body">
                <form role="form" id="editLeaveTypeForm" class="clearfix" action="leave/updateLeaveType" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label><?php echo lang('name'); ?> &ast;</label>
                        <input type="text" class="form-control form-control-lg" name="name" id="editLeaveTypeName" value="" placeholder="" required>
                    </div>
                    <input type="hidden" name="id" id="editLeaveTypeId" value="">
                    <div class="form-group mb-0">
                        <button type="submit" name="submit" class="btn btn-primary btn-block"><?php echo lang('submit'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="infoModal" role="dialog" aria-labelledby="leaveTypeDoctorInfoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h4 class="modal-title text-white font-weight-bold" id="leaveTypeDoctorInfoLabel"><?php echo lang('doctor'); ?> <?php echo lang('info'); ?></h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body">
                <form role="form" id="infoDoctorFormLeaveType" class="clearfix" action="doctor/addNew" method="post" enctype="multipart/form-data">
                    <div class="form-group last col-md-6">
                        <div class="fileupload fileupload-new" data-provides="fileupload">
                            <div class="fileupload-new thumbnail img_url">
                                <img src="" id="img1" alt="">
                            </div>
                            <div class="fileupload-preview fileupload-exists thumbnail img_class"></div>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php echo lang('name'); ?></label>
                        <div class="nameClass"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php echo lang('email'); ?></label>
                        <div class="emailClass"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php echo lang('address'); ?></label>
                        <div class="addressClass"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php echo lang('phone'); ?></label>
                        <div class="phoneClass"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php echo lang('department'); ?></label>
                        <div class="departmentClass"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php echo lang('profile'); ?></label>
                        <div class="profileClass"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
</script>
<script src="common/extranal/js/leave/leave_type.js"></script>
