<?php
defined('BASEPATH') or exit('No direct script access allowed');
$CI = get_instance();
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('payment_gateways'),
        'icon' => 'fas fa-money-bill-wave text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('settings'), 'url' => 'settings'),
            array('label' => lang('payment_gateways'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-dark font-weight-bold">
                                <i class="fas fa-list mr-2 text-info"></i>
                                <?php echo lang('All the Payment gateway names and related informations'); ?>
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="custom_buttons mb-0 px-3 pt-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="pgateway-gateways-table">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="py-3">#</th>
                                            <th class="py-3"><?php echo lang('name'); ?></th>
                                            <th class="py-3"><?php echo lang('options'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 0;
                                        foreach ($pgateways as $pgateway) {
                                            $i = $i + 1;
                                            ?>
                                            <tr>
                                                <td class="py-3"><?php echo (int) $i; ?></td>
                                                <td class="py-3"><?php
                                                    if (!empty($pgateway->name)) {
                                                        echo htmlspecialchars($pgateway->name, ENT_QUOTES, 'UTF-8');
                                                    }
                                                    ?></td>
                                                <td class="py-3">
                                                    <a class="btn btn-info btn-sm" href="pgateway/settings?id=<?php echo (int) $pgateway->id; ?>">
                                                        <i class="fas fa-cog mr-1"></i> <?php echo lang('manage'); ?>
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

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-dark font-weight-bold">
                                <i class="fas fa-check-circle mr-2 text-success"></i>
                                <?php echo lang('select'); ?> <?php echo lang('payment_gateway'); ?>
                            </h3>
                        </div>
                        <div class="card-body p-4">
                            <form role="form" id="editAppointmentForm" action="settings/selectPaymentGateway" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">

                                <?php foreach ($pgateways as $pgateway) { ?>
                                    <div class="form-group mb-4">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="payment_gateway"
                                                id="customRadio<?php echo (int) $pgateway->id; ?>"
                                                value="<?php echo htmlspecialchars($pgateway->name, ENT_QUOTES, 'UTF-8'); ?>"
                                                <?php
                                                if (!empty($pgateway->name) && !empty($settings->payment_gateway) && $settings->payment_gateway == $pgateway->name) {
                                                    echo 'checked';
                                                }
                                                ?>>
                                            <label class="custom-control-label h5" for="customRadio<?php echo (int) $pgateway->id; ?>">
                                                <?php echo htmlspecialchars($pgateway->name, ENT_QUOTES, 'UTF-8'); ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php } ?>
                                <input type="hidden" name="id" value="<?php echo !empty($settings->id) ? (int) $settings->id : ''; ?>">
                                <button type="submit" name="submit" class="btn btn-success btn-lg btn-block mt-4">
                                    <i class="fas fa-check mr-2"></i>
                                    <?php echo lang('submit'); ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    var language = <?php echo json_encode($this->language); ?>;
</script>
<script src="common/js/codearistos.min.js"></script>
<script src="common/extranal/js/pgateway.js"></script>
