<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<link href="common/extranal/css/appointment/appointment.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('appointment') . ' ' . lang('calendar'),
        'icon' => 'fas fa-calendar-alt text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('appointment'), 'url' => 'appointment'),
            array('label' => lang('calendar'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('calendar'); ?></h3>
                            <a class="btn btn-sm btn-primary" href="<?php echo site_url('appointment/addNewView'); ?>">
                                <i class="fas fa-plus mr-1"></i> <?php echo lang('add_appointment'); ?>
                            </a>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <div id="calendar" class="has-toolbar calendar_view"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Medical history (FullCalendar `eventClick` in home/footer) -->
<div class="modal fade" id="cmodal" role="dialog" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-xl med_his modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-medical mr-2"></i>
                    <?php echo lang('medical_history'); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="medical_history" class="row">
                    <div class="col-md-12"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> <?php echo lang('close'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
  if (typeof jQuery === 'undefined') {
    return;
  }
  jQuery(function ($) {
    $(document).on('shown.bs.modal', '#cmodal', function () {
      $('#loader').hide();
    });
  });
})();
</script>
