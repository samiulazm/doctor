<?php
if (!isset($meeting) || !is_object($meeting)) {
    $meeting = new stdClass();
}
if (!isset($payment) || !is_object($payment)) {
    $payment = new stdClass();
}
if (!isset($patient) || !is_object($patient)) {
    $patient = new stdClass();
}
$is_edit = !empty($meeting->id);
$sel_timezone = !empty($meeting->timezone) ? $meeting->timezone : (!empty($settings->timezone) ? $settings->timezone : 'UTC');
$all_timezones = DateTimeZone::listIdentifiers();
?>
<!--sidebar end-->
<!--main content start-->

<div class="content-wrapper bg-light">
    <section class="content-header py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="h3 mb-0 font-weight-bold">
                        <i class="fas fa-video text-primary me-2"></i>
                        <?php echo $is_edit ? lang('edit_meeting') : lang('add_meeting'); ?>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end mb-0">
                        <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo $is_edit ? lang('edit_meeting') : lang('add_meeting'); ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content pb-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-9">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase letter-spacing">
                                <?php echo lang('meeting'); ?> &mdash; <?php echo lang('details'); ?>
                            </h3>
                        </div>
                        <div class="card-body p-4">
                            <?php echo validation_errors(); ?>
                            <?php echo $this->session->flashdata('feedback'); ?>

                            <form role="form" action="meeting/addNew" class="add-meeting-form" method="post" enctype="multipart/form-data">
                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label" for="pos_select"><?php echo lang('patient'); ?></label>
                                    <div class="col-md-9">
                                        <select class="form-control form-control-lg pos_select js-example-basic-single" id="pos_select" name="patient" required>
                                            <?php
                                            if (!$is_edit) {
                                                echo '<option value=""></option>';
                                            }
                                            if (!empty($patients) && is_object($patients) && !empty($patients->id)) {
                                                echo '<option value="' . (int) $patients->id . '" selected>' . htmlspecialchars($patients->name) . ' - ' . (int) $patients->id . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="pos_client border rounded-3 p-3 mb-3 bg-light" style="display: none;">
                                    <h6 class="text-muted text-uppercase small mb-3"><?php echo lang('patient'); ?> &mdash; <?php echo lang('new'); ?></h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="p_name_add"><?php echo lang('patient'); ?> <?php echo lang('name'); ?></label>
                                            <input type="text" class="form-control" id="p_name_add" name="p_name" value="<?php echo !empty($payment->p_name) ? htmlspecialchars($payment->p_name) : ''; ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="p_email_add"><?php echo lang('patient'); ?> <?php echo lang('email'); ?></label>
                                            <input type="text" class="form-control" id="p_email_add" name="p_email" value="<?php echo !empty($payment->p_email) ? htmlspecialchars($payment->p_email) : ''; ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="p_phone_add"><?php echo lang('patient'); ?> <?php echo lang('phone'); ?></label>
                                            <input type="text" class="form-control" id="p_phone_add" name="p_phone" value="<?php echo !empty($payment->p_phone) ? htmlspecialchars($payment->p_phone) : ''; ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="p_age_add"><?php echo lang('patient'); ?> <?php echo lang('age'); ?></label>
                                            <input type="text" class="form-control" id="p_age_add" name="p_age" value="<?php echo !empty($payment->p_age) ? htmlspecialchars($payment->p_age) : ''; ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="p_gender_add"><?php echo lang('patient'); ?> <?php echo lang('gender'); ?></label>
                                            <select class="form-control form-control-lg" id="p_gender_add" name="p_gender">
                                                <option value="Male" <?php echo (!empty($patient->sex) && $patient->sex == 'Male') ? ' selected' : ''; ?>> Male </option>
                                                <option value="Female" <?php echo (!empty($patient->sex) && $patient->sex == 'Female') ? ' selected' : ''; ?>> Female </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <?php if (empty($meeting->id)) { ?>
                                    <?php if (!$this->ion_auth->in_group('Doctor')) { ?>
                                        <div class="row mb-3 align-items-center doctor_div">
                                            <label class="col-md-3 col-form-label" for="adoctors"><?php echo lang('doctor'); ?></label>
                                            <div class="col-md-9">
                                                <select class="form-control form-control-lg m-bot15 js-example-basic-single" id="adoctors" name="doctor" required>
                                                    <?php
                                                    if (!$is_edit) {
                                                        echo '<option value=""></option>';
                                                    }
                                                    if (!empty($doctors) && is_object($doctors) && !empty($doctors->id)) {
                                                        echo '<option value="' . (int) $doctors->id . '" selected>' . htmlspecialchars($doctors->name) . ' - ' . (int) $doctors->id . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <div class="row mb-3">
                                        <label class="col-md-3 col-form-label"><?php echo lang('doctor'); ?></label>
                                        <div class="col-md-9">
                                            <p class="form-control-plaintext mb-0 fw-medium"><?php echo !empty($meeting->doctorname) ? htmlspecialchars($meeting->doctorname) : ''; ?></p>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label" for="topic"><?php echo lang('meeting'); ?> <?php echo lang('topic'); ?></label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control form-control-lg" id="topic" name="topic" placeholder="<?php echo lang('meeting'); ?> <?php echo lang('topic'); ?>" value="<?php echo !empty($meeting->topic) ? htmlspecialchars($meeting->topic) : ''; ?>" required>
                                    </div>
                                </div>

                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label" for="meeting_start_time"><?php echo lang('start_time'); ?></label>
                                    <div class="col-md-9">
                                        <input type="text" class="form_datetime form-control" id="meeting_start_time" name="start_time" placeholder="<?php echo lang('start_time'); ?>" value="<?php echo !empty($meeting->start_time) ? htmlspecialchars($meeting->start_time) : ''; ?>" autocomplete="off" required>
                                    </div>
                                </div>

                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label" for="duration"><?php echo lang('meeting_duration_minutes'); ?></label>
                                    <div class="col-md-9">
                                        <input type="number" min="1" class="form-control form-control-lg" id="duration" name="duration" placeholder="<?php echo lang('meeting_duration_minutes'); ?>" value="<?php echo !empty($meeting->duration) ? (int) $meeting->duration : ''; ?>" required>
                                    </div>
                                </div>

                                <div class="row mb-3 align-items-start">
                                    <label class="col-md-3 col-form-label pt-0" for="timezone"><?php echo lang('meeting'); ?> <?php echo lang('timezone'); ?></label>
                                    <div class="col-md-9">
                                        <select class="form-control js-example-basic-single" id="timezone" name="timezone" data-placeholder="<?php echo lang('timezone'); ?>">
                                            <?php foreach ($all_timezones as $tz) { ?>
                                                <option value="<?php echo htmlspecialchars($tz); ?>"<?php echo ($tz === $sel_timezone) ? ' selected' : ''; ?>><?php echo htmlspecialchars($tz); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label" for="password"><?php echo lang('meeting_password'); ?></label>
                                    <div class="col-md-9">
                                        <input type="password" class="form-control form-control-lg" id="password" name="meeting_password" placeholder="<?php echo lang('meeting_password'); ?>" value="<?php echo !empty($meeting->meeting_password) ? htmlspecialchars($meeting->meeting_password) : ''; ?>" autocomplete="new-password">
                                    </div>
                                </div>

                                <input type="hidden" name="id" id="meeting_id" value="<?php echo !empty($meeting->id) ? (int) $meeting->id : ''; ?>">

                                <div class="row">
                                    <div class="col-md-9 offset-md-3 d-flex justify-content-end gap-2">
                                        <a href="meeting/upcoming" class="btn btn-outline-secondary"><?php echo lang('cancel') ?: 'Cancel'; ?></a>
                                        <button type="submit" name="submit" class="btn btn-primary px-4"><?php echo lang('submit'); ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var select_doctor = "<?php echo lang('select_doctor'); ?>";
</script>
<script type="text/javascript">
    var select_patient = "<?php echo lang('select_patient'); ?>";
</script>
<script src="common/extranal/js/meeting/add_new.js"></script>
<?php if (!empty($meeting->id)) { ?>
    <script src="common/extranal/js/meeting/add_new_with_id.js"></script>
<?php } else { ?>
    <script src="common/extranal/js/meeting/add_new_without_id.js"></script>
<?php } ?>
