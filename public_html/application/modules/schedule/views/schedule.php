<?php
$CI = get_instance();
if (!isset($schedules) || $schedules === null) {
    $schedules = array();
}
if (!isset($doctors) || $doctors === null) {
    $doctors = array();
}
$can_options = $this->ion_auth->in_group(array('admin', 'Doctor'));
?>
<div class="content-wrapper bg-light">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('schedule'),
        'icon' => 'fas fa-calendar-alt text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('schedule'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('schedule'); ?></h3>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal">
                                <i class="fa fa-plus mr-1"></i> <?php echo lang('add_new'); ?> <?php echo lang('schedule'); ?>
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="dt-schedule" data-legacy-table="editable-sample" style="width:100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo lang('doctor'); ?></th>
                                            <th><?php echo lang('weekday'); ?></th>
                                            <th><?php echo lang('start_time'); ?></th>
                                            <th><?php echo lang('end_time'); ?></th>
                                            <th><?php echo lang('duration'); ?></th>
                                            <?php if ($can_options) { ?>
                                            <th class="no-print"><?php echo lang('options'); ?></th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 0;
                                        foreach ($schedules as $schedule) {
                                            if ($this->settings->time_format == 24) {
                                                $schedule->s_time = $this->settings_model->convert_to_24h($schedule->s_time);
                                                $schedule->e_time = $this->settings_model->convert_to_24h($schedule->e_time);
                                            }
                                            $i = $i + 1;
                                            $doc = $this->doctor_model->getDoctorById($schedule->doctor);
                                        ?>
                                    <tr>
                                        <td><?php echo (int) $i; ?></td>
                                        <td><?php echo $doc ? htmlspecialchars($doc->name, ENT_QUOTES, 'UTF-8') : '—'; ?></td>
                                        <td><?php echo htmlspecialchars($schedule->weekday, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($schedule->s_time, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($schedule->e_time, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo (int) $schedule->duration * 5; ?> <?php echo lang('minitues'); ?></td>
                                        <?php if ($can_options) { ?>
                                        <td>
                                            <a class="btn btn-danger btn-sm"
                                                href="schedule/deleteSchedule?id=<?php echo (int) $schedule->id; ?>&amp;doctor=<?php echo (int) $schedule->doctor; ?>&amp;weekday=<?php echo rawurlencode($schedule->weekday); ?>&amp;all=all"
                                                onclick="return confirm(<?php echo json_encode(lang('are_you_sure')); ?>);">
                                                <i class="fa fa-trash"></i> <?php echo lang('delete'); ?>
                                            </a>
                                        </td>
                                        <?php } ?>
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
    </section>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="myModalLabel"><?php echo lang('add'); ?> <?php echo lang('schedule'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form role="form" action="schedule/addSchedule" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="row">
                        <div class="col-md-6 mb-4 doctor_div">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('doctor'); ?>
                                    <span class="text-danger">*</span></label>
                                <select class="form-control form-control-lg shadow-sm" id="doctorchoose" name="doctor" required>
                                    <?php
                                    if (!empty($setval)) {
                                        $doctordetails1 = $this->db->get_where('doctor', array('id' => set_value('doctor')))->row();
                                        if (!empty($doctordetails1)) {
                                    ?>
                                    <option value="<?php echo (int) $doctordetails1->id; ?>" selected>
                                        <?php echo htmlspecialchars($doctordetails1->name, ENT_QUOTES, 'UTF-8'); ?> - <?php echo (int) $doctordetails1->id; ?>
                                    </option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4 weekday_div">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('weekday'); ?>
                                    <span class="text-danger">*</span></label>
                                <select class="form-control form-control-lg shadow-sm" id="weekday" name="weekday" required>
                                    <option value="Friday"><?php echo lang('friday'); ?></option>
                                    <option value="Saturday"><?php echo lang('saturday'); ?></option>
                                    <option value="Sunday"><?php echo lang('sunday'); ?></option>
                                    <option value="Monday"><?php echo lang('monday'); ?></option>
                                    <option value="Tuesday"><?php echo lang('tuesday'); ?></option>
                                    <option value="Wednesday"><?php echo lang('wednesday'); ?></option>
                                    <option value="Thursday"><?php echo lang('thursday'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4 timepickers_time">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('start_time'); ?>
                                    <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text"
                                        class="form-control form-control-lg shadow-sm timepicker-default1" name="s_time"
                                        id="s_time" required autocomplete="off">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-clock"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4 timepickere_time">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('end_time'); ?>
                                    <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text"
                                        class="form-control form-control-lg shadow-sm timepicker-default1" name="e_time"
                                        id="e_time" required autocomplete="off">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-clock"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('appointment'); ?>
                                <?php echo lang('duration'); ?> <span class="text-danger">*</span></label>
                                <select class="form-control form-control-lg shadow-sm" name="duration" required>
                                    <option value="1">5 <?php echo lang('minitues'); ?></option>
                                    <option value="2">10 <?php echo lang('minitues'); ?></option>
                                    <option value="3">15 <?php echo lang('minitues'); ?></option>
                                    <option value="4">20 <?php echo lang('minitues'); ?></option>
                                    <option value="6">30 <?php echo lang('minitues'); ?></option>
                                    <option value="9">45 <?php echo lang('minitues'); ?></option>
                                    <option value="12">60 <?php echo lang('minitues'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <input type="hidden" name="redirect" value="schedule">
                            <input type="hidden" name="id" value="">
                            <button type="submit" id="addSubmit" class="btn btn-primary float-right"><?php echo lang('submit'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var select_doctor = <?php echo json_encode(lang('select_doctor')); ?>;
    var select_patient = <?php echo json_encode(lang('select_patient')); ?>;
    var language = <?php echo json_encode($this->language); ?>;
    var time_format = <?php echo json_encode((string) $this->settings->time_format); ?>;
</script>
<script src="common/extranal/js/schedule/schedule.js"></script>
