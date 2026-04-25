<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('draft_payments'),
        'icon' => 'fas fa-file-invoice text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('draft_payments'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('All the draft payments names and related informations'); ?></h3>
                            <a href="finance/addPaymentView" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus mr-1"></i> <?php echo lang('add_new'); ?>
                            </a>
                        </div>

                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle datatables mb-0" id="editable-sample3" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th><?php echo lang('date'); ?></th>
                                            <th><?php echo lang('patient'); ?></th>
                                            <th><?php echo lang('doctor'); ?></th>
                                            <th><?php echo lang('sub_total'); ?></th>
                                            <th><?php echo lang('discount'); ?></th>
                                            <th><?php echo lang('grand_total'); ?></th>
                                            <th><?php echo lang('remarks'); ?></th>
                                            <th class="no-print"><?php echo lang('options'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

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









<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
</script>
<script src="common/extranal/js/finance/draft_payment.js"></script>
