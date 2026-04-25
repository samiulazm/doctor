<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
if (!isset($leave_types) || !is_array($leave_types)) {
    $leave_types = array();
}
?>
<link href="common/extranal/css/attendance.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('attendance'),
        'icon' => 'fas fa-clipboard-check text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('attendance'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('All the attendance names and related informations'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="row mb-4">
                                <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                                    <div class="col-md-3 mb-3">
                                        <label class="text-uppercase font-weight-bold text-muted small"><?php echo lang('staff'); ?></label>
                                        <select class="form-control form-control-lg shadow-sm" id="staff_select"></select>
                                    </div>
                                <?php } ?>
                                <div class="col-md-3 mb-3">
                                    <label class="text-uppercase font-weight-bold text-muted small"><?php echo lang('month'); ?></label>
                                    <select class="form-control form-control-lg shadow-sm ca_select2" id="attendance_month">
                                        <?php
                                        foreach ($months as $month) {
                                            if ($month == date('F')) {
                                        ?>
                                                <option value="<?php echo html_escape($month); ?>" selected><?php echo html_escape($month); ?></option>
                                            <?php
                                                break;
                                            } else {
                                            ?>
                                                <option value="<?php echo html_escape($month); ?>"><?php echo html_escape($month); ?></option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="text-uppercase font-weight-bold text-muted small"><?php echo lang('year'); ?></label>
                                    <select class="form-control form-control-lg shadow-sm ca_select2" id="attendance_year">
                                        <?php foreach ($years as $year) { ?>
                                            <option value="<?php echo (int) $year; ?>" <?php if ((int) $year === (int) date('Y')) { ?>selected<?php } ?>><?php echo (int) $year; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary btn-block applyAttendanceFilter"><?php echo lang('apply'); ?></button>
                                </div>
                            </div>

                            <div class="row attendanceDiv">
                                <div class="col-md-12 attendanceDiv_col table-responsive">
                                    <table class="table table-bordered table-hover align-middle text-sm mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="text-uppercase"><?php echo lang('staff'); ?></th>
                                                <?php for ($i = 1; $i <= date('t'); $i++) { ?>
                                                    <th class="text-center text-uppercase"><?php echo (int) $i; ?></th>
                                                <?php } ?>
                                                <th class="text-uppercase"><?php echo lang('total'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php for ($j = 0; $j < $total_employees; $j++) { ?>
                                                <tr>
                                                    <?php for ($i = 0; $i < (date('t') + 2); $i++) {
                                                        if ($i == 0) {
                                                    ?>
                                                            <td><?php echo html_escape($details[$j][$i]); ?></td>
                                                        <?php } else if ($i == ((date('t') + 2) - 1)) { ?>
                                                            <td><?php echo html_escape($details[$j][$i]); ?></td>
                                                        <?php } else { ?>
                                                            <td class="text-center"><?php if ($details[$j][$i] == 'no') { ?>
                                                                    <a href="#" class="<?php if ($this->ion_auth->in_group('admin') || $i > date('d') - 1) { ?> markAttandance <?php } ?> markAttendance_css" data-employee="<?php echo (int) $employee[$j]; ?>" data-id="<?php echo (int) $i . '-' . date('m-Y'); ?>"><i class="fas fa-times"></i></a>
                                                                <?php } else if ($details[$j][$i] == 'yes') { ?>
                                                                    <a href="#" class="<?php if (1 == 1) { ?> markAttandance <?php } ?> markAttendance_css" data-employee="<?php echo (int) $employee[$j]; ?>" data-id="<?php echo (int) $i . '-' . date('m-Y'); ?>"><i class="fas fa-check"></i></a>
                                                                <?php } ?>
                                                            </td>
                                                    <?php }
                                                    } ?>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
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
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="font-weight-bold text-muted"><?php echo lang('choose_staff'); ?> &#42;</label>
                            <select name="staff" class="form-control" id="add_leave_staff" required=""></select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="font-weight-bold text-muted"><?php echo lang('leave_type'); ?> &#42;</label>
                            <select name="leave_type" class="ca_select2 w-100" id="ca_select2" required="">
                                <?php foreach ($leave_types as $leaveType) { ?>
                                    <option value="<?php echo html_escape($leaveType->name); ?>"><?php echo html_escape($leaveType->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label class="font-weight-bold text-muted"><?php echo lang('select_duration'); ?></label>
                            <div class="duration_select">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input leave_duration" type="radio" name="duration" id="inlineRadio1" value="single">
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

                        <div class="col-md-6 mb-4 singleDate">
                            <label class="font-weight-bold text-muted"><?php echo lang('date'); ?></label>
                            <input type="text" class="form-control single_date_picker" name="date" id="exampleInputEmail1" value="" placeholder="" readonly required>
                        </div>

                        <div class="col-md-6 mb-4 multiDate">
                            <label class="font-weight-bold text-muted"><?php echo lang('date'); ?></label>
                            <input type="text" class="form-control" name="date2" id="multi_date_picker" placeholder="" readonly multiple>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="font-weight-bold text-muted"><?php echo lang('reason_for_leave'); ?></label>
                            <textarea class="form-control reason_class" name="reason" rows="5" required></textarea>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="font-weight-bold text-muted"><?php echo lang('status'); ?></label>
                            <select name="status" class="ca_select2 w-100" required>
                                <option value="approved"><?php echo lang('approved'); ?></option>
                                <option value="pending"><?php echo lang('pending'); ?></option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" name="submit" class="btn btn-primary float-right"><?php echo lang('submit'); ?></button>
                        </div>
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
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="font-weight-bold text-muted"><?php echo lang('choose_staff'); ?></label>
                            <select name="staff" class="form-control" id="edit_leave_staff"></select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="font-weight-bold text-muted"><?php echo lang('leave_type'); ?></label>
                            <select name="leave_type" class="ca_select2 w-100" id="edit_Leave_select2">
                                <?php foreach ($leave_types as $leaveType) { ?>
                                    <option value="<?php echo html_escape($leaveType->name); ?>"><?php echo html_escape($leaveType->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label class="font-weight-bold text-muted"><?php echo lang('select_duration'); ?></label>
                            <div class="duration_select1">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input edit_leave_duration" type="radio" name="duration" id="single" value="single">
                                    <label class="form-check-label" for="single"><?php echo lang('single'); ?></label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input edit_leave_duration" type="radio" name="duration" id="halfday2" value="halfday">
                                    <label class="form-check-label" for="halfday2"><?php echo lang('halfday'); ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4 singleDate">
                            <label class="font-weight-bold text-muted"><?php echo lang('date'); ?></label>
                            <input type="text" class="form-control single_date_picker" name="date" id="editDate" value="" placeholder="" readonly required>
                        </div>

                        <div class="col-md-6 mb-4 multiDate">
                            <label class="font-weight-bold text-muted"><?php echo lang('date'); ?></label>
                            <input type="text" class="form-control" name="date2" id="edit_multi_date_picker" placeholder="" readonly multiple>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="font-weight-bold text-muted"><?php echo lang('reason_for_leave'); ?></label>
                            <textarea class="form-control reason_class" name="reason" rows="5" required></textarea>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="font-weight-bold text-muted"><?php echo lang('status'); ?></label>
                            <select name="status" id="editLeaveStatus" class="ca_select2 w-100" required>
                                <option value="approved"><?php echo lang('approved'); ?></option>
                                <option value="pending"><?php echo lang('pending'); ?></option>
                            </select>
                        </div>
                        <input type="hidden" name="id" id="editLeaveId">
                        <div class="col-md-12">
                            <button type="submit" name="submit" class="btn btn-primary float-right"><?php echo lang('submit'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="attendanceModal" role="dialog" aria-labelledby="attendanceEditModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h4 class="modal-title text-white font-weight-bold" id="attendanceEditModalLabel"><?php echo lang('attendance'); ?></h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body">
                <form role="form" id="attendanceForm" class="clearfix" action="attendance/updateAttendance" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="font-weight-bold text-muted"><?php echo lang('clock_in'); ?></label>
                            <input type="text" class="form-control timepicker-default readonly" name="clock_in" required autocomplete="off">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="font-weight-bold text-muted"><?php echo lang('clock_out'); ?></label>
                            <input type="text" class="form-control timepicker-default" name="clock_out" readonly>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="halfday" name="halfday" value="halfday" class="custom-control-input">
                                <label class="custom-control-label" for="halfday"><?php echo lang('halfday'); ?></label>
                            </div>
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="late" name="late" value="late" class="custom-control-input">
                                <label class="custom-control-label" for="late"><?php echo lang('late'); ?></label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="font-weight-bold text-muted"><?php echo lang('work_from'); ?></label>
                            <input type="text" class="form-control form-control-lg" name="work_from" value="office" required>
                        </div>

                        <input type="hidden" id="attendanceDate" name="date">
                        <input type="hidden" id="staffId" name="id">

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary float-right"><?php echo lang('submit'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
    var select_staff = <?php echo json_encode(lang('select_staff')); ?>;
</script>
<script src="common/extranal/js/attendance.js"></script>
