<?php
if (!isset($patient) || !is_object($patient)) {
    $patient = new stdClass();
}
$CI = get_instance();
$__csrf_n = $CI->security->get_csrf_token_name();
$__csrf_h = $CI->security->get_csrf_hash();
?>
<!--sidebar end-->
<!--main content start-->

<div class="content-wrapper bg-light">
    <section class="content-header py-3 border-bottom bg-white">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="h4 mb-1 font-weight-bold">
                        <i class="fas fa-calendar-alt text-primary me-2"></i>
                        <?php echo lang('upcoming'); ?> <?php echo lang('meetings'); ?>
                    </h1>
                    <p class="text-muted small mb-0"><?php echo lang('meeting'); ?> &mdash; <?php echo lang('upcoming'); ?></p>
                </div>
                <div class="col-auto d-flex flex-wrap align-items-center gap-2">
                    <div class="custom_buttons"></div>
                    <?php if (!$this->ion_auth->in_group('Patient')) { ?>
                        <a href="meeting/addNewView" class="btn btn-primary btn-sm shadow-sm">
                            <i class="fas fa-plus-circle me-1"></i> <?php echo lang('add_meeting'); ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0 w-100" id="editable-sample1" width="100%">
                            <thead class="bg-light">
                                <tr>
                                    <th><?php echo lang('topic'); ?></th>
                                    <th><?php echo lang('patient'); ?></th>
                                    <th><?php echo lang('doctor'); ?></th>
                                    <th><?php echo lang('zoom'); ?> <?php echo lang('meeting_id'); ?></th>
                                    <th><?php echo lang('start_time'); ?></th>
                                    <th><?php echo lang('duration'); ?></th>
                                    <th><?php echo lang('status'); ?></th>
                                    <th class="no-print"><?php echo lang('options'); ?></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" role="dialog" id="cmodal" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div id="medical_history">
                    <div class="col-md-12"></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo lang('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Add Meeting Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="myModalLabel"><?php echo lang('add_meeting'); ?></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form role="form" action="meeting/addNew" method="post" class="row g-3 clearfix" enctype="multipart/form-data">
                    <div class="col-md-6">
                        <label class="form-label" for="exampleInputEmail1"><?php echo lang('patient'); ?></label>
                        <select class="form-control form-control-lg m-bot15 pos_select" id="pos_select" name="patient"></select>
                    </div>
                    <div class="pos_client clearfix col-md-6" style="display: none;">
                        <div class="payment pad_bot">
                            <label class="form-label" for="p_name_m1"><?php echo lang('patient'); ?> <?php echo lang('name'); ?></label>
                            <input type="text" class="form-control pay_in" id="p_name_m1" name="p_name" value="" placeholder="">
                        </div>
                        <div class="payment pad_bot">
                            <label class="form-label"><?php echo lang('patient'); ?> <?php echo lang('email'); ?></label>
                            <input type="text" class="form-control pay_in" name="p_email" value="" placeholder="">
                        </div>
                        <div class="payment pad_bot">
                            <label class="form-label"><?php echo lang('patient'); ?> <?php echo lang('phone'); ?></label>
                            <input type="text" class="form-control pay_in" name="p_phone" value="" placeholder="">
                        </div>
                        <div class="payment pad_bot">
                            <label class="form-label"><?php echo lang('patient'); ?> <?php echo lang('age'); ?></label>
                            <input type="text" class="form-control pay_in" name="p_age" value="" placeholder="">
                        </div>
                        <div class="payment pad_bot">
                            <label class="form-label"><?php echo lang('patient'); ?> <?php echo lang('gender'); ?></label>
                            <select class="form-control form-control-lg" name="p_gender">
                                <option value="Male" <?php echo (!empty($patient->sex) && $patient->sex == 'Male') ? 'selected' : ''; ?>> Male </option>
                                <option value="Female" <?php echo (!empty($patient->sex) && $patient->sex == 'Female') ? 'selected' : ''; ?>> Female </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 doctor_div">
                        <label class="form-label"><?php echo lang('doctor'); ?></label>
                        <select class="form-control form-control-lg m-bot15" id="adoctors" name="doctor"></select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><?php echo lang('date'); ?></label>
                        <input type="text" class="form-control form-control-lg default-date-picker" id="date" readonly name="date" value="" placeholder="">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><?php echo lang('available'); ?> <?php echo lang('time'); ?></label>
                        <select class="form-control form-control-lg m-bot15" name="time_slot" id="aslots"></select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><?php echo lang('meeting'); ?> <?php echo lang('status'); ?></label>
                        <select class="form-control form-control-lg m-bot15" name="status">
                            <option value="Pending Confirmation"><?php echo lang('pending_confirmation'); ?></option>
                            <option value="Confirmed"><?php echo lang('confirmed'); ?></option>
                            <option value="Treated"><?php echo lang('treated'); ?></option>
                            <option value="Cancelled"><?php echo lang('cancelled'); ?></option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><?php echo lang('remarks'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="remarks" value="" placeholder="">
                    </div>
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal"><?php echo lang('cancel'); ?></button>
                        <button type="submit" name="submit" class="btn btn-primary"><?php echo lang('submit'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Meeting Modal -->
<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModal2Label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="myModal2Label"><?php echo lang('edit_meeting'); ?></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form role="form" id="editMeetingForm" action="meeting/addNew" class="row g-3 clearfix" method="post" enctype="multipart/form-data">
                    <div class="col-md-6">
                        <label class="form-label"><?php echo lang('patient'); ?></label>
                        <select class="form-control form-control-lg m-bot15 pos_select patient" id="pos_select_edit" name="patient"></select>
                    </div>
                    <div class="pos_client clearfix col-md-6" style="display: none;">
                        <div class="payment pad_bot">
                            <label class="form-label"><?php echo lang('patient'); ?> <?php echo lang('name'); ?></label>
                            <input type="text" class="form-control pay_in" name="p_name" value="" placeholder="">
                        </div>
                        <div class="payment pad_bot">
                            <label class="form-label"><?php echo lang('patient'); ?> <?php echo lang('email'); ?></label>
                            <input type="text" class="form-control pay_in" name="p_email" value="" placeholder="">
                        </div>
                        <div class="payment pad_bot">
                            <label class="form-label"><?php echo lang('patient'); ?> <?php echo lang('phone'); ?></label>
                            <input type="text" class="form-control pay_in" name="p_phone" value="" placeholder="">
                        </div>
                        <div class="payment pad_bot">
                            <label class="form-label"><?php echo lang('patient'); ?> <?php echo lang('age'); ?></label>
                            <input type="text" class="form-control pay_in" name="p_age" value="" placeholder="">
                        </div>
                        <div class="payment pad_bot">
                            <label class="form-label"><?php echo lang('patient'); ?> <?php echo lang('gender'); ?></label>
                            <select class="form-control form-control-lg" name="p_gender">
                                <option value="Male" <?php echo (!empty($patient->sex) && $patient->sex == 'Male') ? 'selected' : ''; ?>> Male </option>
                                <option value="Female" <?php echo (!empty($patient->sex) && $patient->sex == 'Female') ? 'selected' : ''; ?>> Female </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 doctor_div1">
                        <label class="form-label"><?php echo lang('doctor'); ?></label>
                        <select class="form-control form-control-lg m-bot15 doctor" id="adoctors1" name="doctor"></select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><?php echo lang('date'); ?></label>
                        <input type="text" class="form-control form-control-lg default-date-picker" id="date1" readonly name="date" value="" placeholder="">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><?php echo lang('available'); ?> <?php echo lang('time'); ?></label>
                        <select class="form-control form-control-lg m-bot15" name="time_slot" id="aslots1"></select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><?php echo lang('meeting'); ?> <?php echo lang('status'); ?></label>
                        <select class="form-control form-control-lg m-bot15" name="status">
                            <option value="Pending Confirmation"><?php echo lang('pending_confirmation'); ?></option>
                            <option value="Confirmed"><?php echo lang('confirmed'); ?></option>
                            <option value="Treated"><?php echo lang('treated'); ?></option>
                            <option value="Cancelled"><?php echo lang('cancelled'); ?></option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><?php echo lang('remarks'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="remarks" value="" placeholder="">
                    </div>
                    <input type="hidden" name="id" id="meeting_id" value="">
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal"><?php echo lang('cancel'); ?></button>
                        <button type="submit" name="submit" class="btn btn-primary"><?php echo lang('submit'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal4" tabindex="-1" role="dialog" aria-labelledby="myModal4Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="myModal4Label"><i class="fa fa-location-arrow me-1"></i> <?php echo lang('send_sms_to_patient'); ?></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form role="form" id="sendSmsToVolunteer" action="sms/meetingReminder" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <p class="mb-0"><?php echo lang('reminder_message'); ?></p>
                    </div>
                    <input type="hidden" id="id" value="" name="id">
                    <button type="submit" name="submit" class="btn btn-primary submit_button"><?php echo lang('yes'); ?></button>
                    <button type="button" class="btn btn-outline-secondary invoicebutton" data-bs-dismiss="modal"><?php echo lang('cancel'); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var n = <?php echo json_encode($__csrf_n); ?>;
    var h = <?php echo json_encode($__csrf_h); ?>;
    window.meetingUpcomingTableCsrf = function (d) {
        d[n] = h;
        return d;
    };
})();
</script>
<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var select_doctor = "<?php echo lang('select_doctor'); ?>";
</script>
<script type="text/javascript">
    var select_patient = "<?php echo lang('select_patient'); ?>";
</script>
<script src="common/extranal/js/meeting/common.js"></script>
<script src="common/extranal/js/meeting/upcoming.js"></script>
