<?php
$CI = get_instance();
if (!isset($holidays) || $holidays === null) {
    $holidays = array();
}
$can_options = $this->ion_auth->in_group(array('admin', 'Doctor'));
?>
<div class="content-wrapper bg-light">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('holiday'),
        'icon' => 'fas fa-calendar-alt text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('schedule'), 'url' => 'schedule'),
            array('label' => lang('holiday'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('All the registered holidays for the doctors'); ?></h3>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal">
                                <i class="fa fa-plus mr-1"></i> <?php echo lang('add_new'); ?> <?php echo lang('holiday'); ?>
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
                                            <th><?php echo lang('date'); ?></th>
                                            <?php if ($can_options) { ?>
                                            <th class="no-print"><?php echo lang('options'); ?></th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $i = 0;
                                    foreach ($holidays as $holiday) {
                                        $i = $i + 1;
                                        $doc = $this->doctor_model->getDoctorById($holiday->doctor);
                                    ?>
                                        <tr>
                                            <td><?php echo (int) $i; ?></td>
                                            <td><?php echo $doc ? htmlspecialchars($doc->name, ENT_QUOTES, 'UTF-8') : '—'; ?></td>
                                            <td><?php echo htmlspecialchars(date('d-m-Y', (int) $holiday->date), ENT_QUOTES, 'UTF-8'); ?></td>
                                            <?php if ($can_options) { ?>
                                                <td>
                                                    <button type="button" class="btn btn-info btn-sm editbutton" data-toggle="modal" data-id="<?php echo (int) $holiday->id; ?>">
                                                        <i class="fa fa-edit"></i> <?php echo lang('edit'); ?>
                                                    </button>
                                                    <a class="btn btn-danger btn-sm delete_button" href="schedule/deleteHoliday?id=<?php echo (int) $holiday->id; ?>&amp;doctor=<?php echo (int) $holiday->doctor; ?>&amp;redirect=schedule/allHolidays" onclick="return confirm(<?php echo json_encode(lang('are_you_sure')); ?>);">
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

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalAddLabel" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5 font-weight-bold" id="myModalAddLabel"><?php echo lang('add'); ?> <?php echo lang('holiday'); ?></h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form role="form" action="schedule/addHoliday" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('doctor'); ?> <span class="text-danger">*</span></label>
                                <select class="form-control form-control-lg shadow-sm" id="doctorchoose" name="doctor" required>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('date'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm default-date-picker" name="date" autocomplete="off" required>
                            </div>
                        </div>

                        <input type="hidden" name="id" value="">
                        <input type="hidden" name="redirect" value="schedule/allHolidays">

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-lg btn-block py-3">
                                <i class="fas fa-check-circle mr-2"></i><?php echo lang('submit'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="myModalEditLabel" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5 font-weight-bold" id="myModalEditLabel"><?php echo lang('edit'); ?> <?php echo lang('holiday'); ?></h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form role="form" id="editHolidayForm" action="schedule/addHoliday" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('doctor'); ?> <span class="text-danger">*</span></label>
                                <select class="form-control form-control-lg shadow-sm js-example-basic-single doctor" id="doctorchoose1" name="doctor" required>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('date'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm default-date-picker" name="date" autocomplete="off" required>
                            </div>
                        </div>

                        <input type="hidden" name="id" value="">
                        <input type="hidden" name="redirect" value="schedule/allHolidays">

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-lg btn-block py-3">
                                <i class="fas fa-check-circle mr-2"></i><?php echo lang('submit'); ?>
                            </button>
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
</script>
<script src="common/extranal/js/schedule/all_holidays.js"></script>
