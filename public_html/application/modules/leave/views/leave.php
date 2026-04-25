<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
if (!isset($leave_types) || !is_array($leave_types)) {
    $leave_types = array();
}
?>
<link href="common/extranal/css/leave/leave.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('leave'),
        'icon' => 'fas fa-calendar-alt text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('leave'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-end mb-3">
                <a data-toggle="modal" data-target="#myModal" href="#myModal" class="btn btn-sm btn-success">
                    <i class="fa fa-plus mr-1"></i> <?php echo lang('add_new'); ?> <?php echo lang('leave'); ?>
                </a>
            </div>
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('All the leave names and related informations'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle text-sm datatables mb-0" id="editable-sample" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-uppercase"><?php echo lang('employee'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('leave_date'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('leave_status'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('leave_type'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('leave_reason'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('duration'); ?></th>
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

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="addLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h4 class="modal-title text-white font-weight-bold" id="addLeaveModalLabel"><?php echo lang('add_new_leave'); ?></h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body">
                <form role="form" action="leave/addNew" class="clearfix" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                        <div class="form-group">
                            <label><?php echo lang('choose_staff'); ?> &ast;</label>
                            <select name="staff" class="form-control form-control-lg" id="add_leave_staff" required=""></select>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label><?php echo lang('leave_type'); ?> &ast;</label>
                        <select name="leave_type" class="ca_select2 form-control" id="ca_select2" required="">
                            <?php foreach ($leave_types as $leaveType) { ?>
                                <option value="<?php echo html_escape($leaveType->name); ?>"><?php echo html_escape($leaveType->name); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-12 px-0">
                        <label><?php echo lang('select_duration'); ?> &ast;</label>
                        <div class="check_div">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input leave_duration" type="radio" name="duration" id="inlineRadio1" value="single" checked>
                                <label class="form-check-label" for="inlineRadio1"><?php echo lang('single'); ?></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input leave_duration" type="radio" name="duration" id="inlineRadio2" value="multiple">
                                <label class="form-check-label" for="inlineRadio2"><?php echo lang('multiple'); ?></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input leave_duration" type="radio" name="duration" id="inlineRadio3" value="halfday">
                                <label class="form-check-label" for="inlineRadio3"><?php echo lang('halfday'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group singleDate">
                        <label><?php echo lang('date'); ?> &ast;</label>
                        <input type="text" class="form-control single_date_picker" name="date" value="" placeholder="" autocomplete="off" required>
                    </div>

                    <div class="form-group multiDate">
                        <label><?php echo lang('date'); ?></label>
                        <input type="text" class="form-control" name="date2" id="multi_date_picker" placeholder="" readonly multiple>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('reason_for_leave'); ?> &ast;</label>
                        <textarea class="form-control reason" name="reason" rows="5" required></textarea>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('status'); ?> &ast;</label>
                        <select name="status" class="ca_select2 form-control" required>
                            <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                                <option value="approved"><?php echo lang('approved'); ?></option>
                            <?php } ?>
                            <option value="pending"><?php echo lang('pending'); ?></option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <button type="submit" name="submit" class="btn btn-primary float-right"><?php echo lang('submit'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="editLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h4 class="modal-title text-white font-weight-bold" id="editLeaveModalLabel"><?php echo lang('edit_leave'); ?></h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body">
                <form role="form" id="editLeaveForm" class="clearfix" action="leave/updateLeave" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                        <div class="form-group">
                            <label><?php echo lang('choose_staff'); ?> &ast;</label>
                            <select name="staff" class="form-control form-control-lg" id="edit_leave_staff" required=""></select>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label><?php echo lang('leave_type'); ?> &ast;</label>
                        <select name="leave_type" class="ca_select2 form-control" id="edit_Leave_select2" required="">
                            <?php foreach ($leave_types as $leaveType) { ?>
                                <option value="<?php echo html_escape($leaveType->name); ?>"><?php echo html_escape($leaveType->name); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-12 px-0">
                        <label><?php echo lang('select_duration'); ?> &ast;</label>
                        <div class="check_div">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input edit_leave_duration" type="radio" name="duration" id="edit_dur_single" value="single">
                                <label class="form-check-label" for="edit_dur_single"><?php echo lang('single'); ?></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input edit_leave_duration" type="radio" name="duration" id="edit_dur_halfday" value="halfday">
                                <label class="form-check-label" for="edit_dur_halfday"><?php echo lang('halfday'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group singleDate">
                        <label><?php echo lang('date'); ?> &ast;</label>
                        <input type="text" class="form-control single_date_picker readonly" name="date" id="editDate" value="" placeholder="" required>
                    </div>

                    <div class="form-group multiDate">
                        <label><?php echo lang('date'); ?></label>
                        <input type="text" class="form-control" name="date2" id="edit_multi_date_picker" placeholder="" readonly multiple>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('reason_for_leave'); ?> &ast;</label>
                        <textarea class="form-control reason" name="reason" rows="5" required></textarea>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('status'); ?> &ast;</label>
                        <select name="status" id="editLeaveStatus" class="ca_select2 form-control" required <?php if (!$this->ion_auth->in_group(array('admin'))) { ?>disabled<?php } ?>>
                            <option value="approved"><?php echo lang('approved'); ?></option>
                            <option value="pending"><?php echo lang('pending'); ?></option>
                        </select>
                    </div>
                    <input type="hidden" name="id" id="editLeaveId">
                    <div class="form-group mb-0">
                        <button type="submit" name="submit" class="btn btn-primary float-right"><?php echo lang('submit'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="infoModal" role="dialog" aria-labelledby="doctorInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h4 class="modal-title text-white font-weight-bold" id="doctorInfoModalLabel"><?php echo lang('doctor'); ?> <?php echo lang('info'); ?></h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body">
                <form role="form" id="infoDoctorForm" class="clearfix" action="doctor/addNew" method="post" enctype="multipart/form-data">
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
    var select_staff = <?php echo json_encode(lang('select_staff')); ?>;
    var language = <?php echo json_encode($this->language); ?>;
</script>
<script src="common/extranal/js/leave/leave.js"></script>
