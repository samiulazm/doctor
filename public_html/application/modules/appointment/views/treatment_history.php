<?php
$CI = get_instance();
$from_val = !empty($from) ? htmlspecialchars($from) : '';
$to_val = !empty($to) ? htmlspecialchars($to) : '';
$appointments = !empty($appointments) ? $appointments : array();
?>
<div class="content-wrapper bg-light">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('treatment_history'),
        'icon' => 'fas fa-history text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('appointment'), 'url' => 'appointment'),
            array('label' => lang('treatment_history'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-11">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase">
                                <?php echo lang('Total number of appointments each doctor has handled, both overall and within a specified date range'); ?>
                            </h3>
                        </div>

                        <div class="card-body p-4">
                            <div class="row mb-4 no-print">
                                <div class="col-lg-7">
                                    <form role="form" action="appointment/treatmentReport" method="post" enctype="multipart/form-data" class="mb-0">
                                        <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                                        <div class="form-group row align-items-end mb-0">
                                            <div class="col-12 col-md-8">
                                                <label class="d-block small text-muted mb-1"><?php echo lang('date_from'); ?> / <?php echo lang('date_to'); ?></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control dpd1" name="date_from" autocomplete="off" placeholder="<?php echo lang('date_from'); ?>" value="<?php echo $from_val; ?>">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text"><?php echo lang('to'); ?></span>
                                                    </div>
                                                    <input type="text" class="form-control dpd2" name="date_to" autocomplete="off" placeholder="<?php echo lang('date_to'); ?>" value="<?php echo $to_val; ?>">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4 mt-2 mt-md-0">
                                                <button type="submit" name="submit" class="btn btn-primary btn-block px-3"><?php echo lang('submit'); ?></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-lg-5 text-lg-right mt-3 mt-lg-0 no-print">
                                    <button type="button" class="btn btn-outline-secondary" onclick="window.print();">
                                        <i class="fa fa-print mr-1"></i> <?php echo lang('print'); ?>
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered bg-white" id="treatment-doctor-table" style="width:100%">
                                    <thead class="thead-light">
                                    <tr>
                                        <th><?php echo lang('doctor_id'); ?></th>
                                        <th><?php echo lang('doctor'); ?></th>
                                        <th><?php echo lang('number_of_patient_treated'); ?></th>
                                        <th class="no-print"><?php echo lang('actions'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($doctors as $doctor) {
                                        $count = 0;
                                        foreach ($appointments as $appointment) {
                                            if (isset($appointment->doctor) && (int) $appointment->doctor === (int) $doctor->id) {
                                                $count++;
                                            }
                                        }
                                    ?>
                                        <tr>
                                            <td><?php echo (int) $doctor->id; ?></td>
                                            <td><?php echo htmlspecialchars($doctor->name); ?></td>
                                            <td><?php echo (int) $count; ?></td>
                                            <td class="no-print">
                                                <a class="btn btn-primary btn-sm" href="appointment/getAppointmentByDoctorId?id=<?php echo (int) $doctor->id; ?>">
                                                    <i class="fa fa-info-circle"></i> <?php echo lang('details'); ?>
                                                </a>
                                            </td>
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
