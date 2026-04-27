<?php
$CI = get_instance();
if (!isset($sgateways) || !is_array($sgateways)) {
    $sgateways = array();
}
?>
<div class="content-wrapper bg-light">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('sms_gateways'),
        'icon' => 'fas fa-sms text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('sms_gateways'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-7 mb-4 mb-lg-0">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('All the Sms Gateway names and related informations'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0" id="dt-sms" data-legacy-table="editable-sample">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo lang('name'); ?></th>
                                            <th class="no-print"><?php echo lang('options'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 0;
                                        foreach ($sgateways as $sgateway) {
                                            $i = $i + 1;
                                        ?>
                                            <tr>
                                                <td><?php echo (int) $i; ?></td>
                                                <td><?php
                                                    if (!empty($sgateway->name)) {
                                                        echo htmlspecialchars($sgateway->name, ENT_QUOTES, 'UTF-8');
                                                    }
                                                    ?></td>
                                                <td>
                                                    <a class="btn btn-info btn-sm" href="sms/settings?id=<?php echo (int) $sgateway->id; ?>"><i class="fa fa-cog"></i> <?php echo lang('manage'); ?></a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-5">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('select'); ?> <?php echo lang('sms_gateway'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <form role="form" id="editAppointmentForm" action="settings/selectSmsGateway" class="clearfix" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                                <?php foreach ($sgateways as $sgateway) { ?>
                                    <div class="form-group">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="sms_gateway" id="customRadio<?php echo (int) $sgateway->id; ?>" value="<?php echo htmlspecialchars($sgateway->name, ENT_QUOTES, 'UTF-8'); ?>" <?php
                                            if (!empty($sgateway->name) && !empty($settings->sms_gateway) && $settings->sms_gateway == $sgateway->name) {
                                                echo 'checked';
                                            }
                                            ?>>
                                            <label class="custom-control-label" for="customRadio<?php echo (int) $sgateway->id; ?>"><?php echo htmlspecialchars($sgateway->name, ENT_QUOTES, 'UTF-8'); ?></label>
                                        </div>
                                    </div>
                                <?php } ?>
                                <input type="hidden" name="id" value="<?php echo isset($settings->id) ? (int) $settings->id : ''; ?>">
                                <div class="text-right">
                                    <button type="submit" name="submit" class="btn btn-primary"><?php echo lang('submit'); ?></button>
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
<script src="common/extranal/js/sms/settings.js"></script>
