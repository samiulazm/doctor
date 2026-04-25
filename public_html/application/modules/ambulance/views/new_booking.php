<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('new_booking'),
        'icon' => 'fas fa-calendar-plus text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('ambulance'), 'url' => 'ambulance'),
            array('label' => lang('bookings'), 'url' => 'ambulance/bookings'),
            array('label' => lang('new_booking'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('new_booking'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <form role="form" action="ambulance/addBooking" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">

                                <div class="form-group">
                                    <label for="patient_id" class="control-label"><?php echo lang('patient'); ?> <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="patient_id" id="patient_id">
                                        <option value=""><?php echo lang('select_patient'); ?></option>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="pickup_address"><?php echo lang('pickup_address'); ?> <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="pickup_address" id="pickup_address" required placeholder="Enter pickup address..."><?php if (!empty($setval)) echo html_escape($pickup_address); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="destination_address"><?php echo lang('destination_address'); ?> <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="destination_address" id="destination_address" required placeholder="Enter destination address..."><?php if (!empty($setval)) echo html_escape($destination_address); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="ambulance_id"><?php echo lang('ambulance'); ?> <span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="ambulance_id" id="ambulance_id" required>
                                                <option value=""><?php echo lang('select_ambulance'); ?></option>
                                                <?php foreach ($ambulances as $ambulance) { ?>
                                                    <option value="<?php echo (int) $ambulance->id; ?>" <?php if (!empty($setval) && isset($ambulance_id) && $ambulance_id == $ambulance->id) echo 'selected'; ?>>
                                                        <?php echo html_escape($ambulance->vehicle_number); ?> - <?php echo html_escape($ambulance->driver_name); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="booking_type"><?php echo lang('booking_type'); ?> <span class="text-danger">*</span></label>
                                            <select class="form-control" name="booking_type" id="booking_type" required>
                                                <option value=""><?php echo lang('select_booking_type'); ?></option>
                                                <option value="Emergency" <?php if (!empty($setval) && isset($booking_type) && $booking_type == 'Emergency') echo 'selected'; ?>><?php echo lang('emergency'); ?></option>
                                                <option value="Transfer" <?php if (!empty($setval) && isset($booking_type) && $booking_type == 'Transfer') echo 'selected'; ?>><?php echo lang('transfer'); ?></option>
                                                <option value="Discharge" <?php if (!empty($setval) && isset($booking_type) && $booking_type == 'Discharge') echo 'selected'; ?>><?php echo lang('discharge'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="priority"><?php echo lang('priority'); ?> <span class="text-danger">*</span></label>
                                            <select class="form-control" name="priority" id="priority" required>
                                                <option value=""><?php echo lang('select_priority'); ?></option>
                                                <option value="Low" <?php if (!empty($setval) && isset($priority) && $priority == 'Low') echo 'selected'; ?>><?php echo lang('low'); ?></option>
                                                <option value="Medium" <?php if (!empty($setval) && isset($priority) && $priority == 'Medium') echo 'selected'; ?>><?php echo lang('medium'); ?></option>
                                                <option value="High" <?php if (!empty($setval) && isset($priority) && $priority == 'High') echo 'selected'; ?>><?php echo lang('high'); ?></option>
                                                <option value="Critical" <?php if (!empty($setval) && isset($priority) && $priority == 'Critical') echo 'selected'; ?>><?php echo lang('critical'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="pickup_time"><?php echo lang('pickup_time'); ?> <span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control" name="pickup_time" id="pickup_time" value="<?php if (!empty($setval) && isset($pickup_time)) echo html_escape($pickup_time); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="notes"><?php echo lang('notes'); ?></label>
                                            <textarea class="form-control" name="notes" id="notes" placeholder="Additional notes..."><?php if (!empty($setval) && isset($notes)) echo html_escape($notes); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group text-center mb-0">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fa fa-save mr-2"></i>
                                        <?php echo lang('create_booking'); ?>
                                    </button>
                                    <a href="ambulance/bookings" class="btn btn-secondary btn-lg px-5 ml-2">
                                        <i class="fa fa-times mr-2"></i>
                                        <?php echo lang('cancel'); ?>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        $('#patient_id').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Search by patient ID, name, phone, or age...',
            allowClear: true,
            minimumInputLength: 0,
            ajax: {
                url: 'ambulance/getPatientInfo',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { term: params.term };
                },
                processResults: function (data) {
                    return { results: data };
                },
                cache: true
            }
        });
        $('#ambulance_id').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    });
</script>
