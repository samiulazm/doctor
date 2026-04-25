<?php
defined('BASEPATH') or exit('No direct script access allowed');
$CI = get_instance();
$pg_title = !empty($settings->name) ? htmlspecialchars($settings->name, ENT_QUOTES, 'UTF-8') : lang('payment_gateway');
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => $pg_title . ' ' . lang('settings'),
        'icon' => 'fas fa-money-bill-wave text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('payment_gateways'), 'url' => 'pgateway'),
            array('label' => $pg_title . ' ' . lang('settings'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-7">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-dark font-weight-bold">
                                <?php echo lang('options'); ?> — <?php echo $pg_title; ?>
                            </h3>
                        </div>
                        <div class="card-body p-4">
                            <?php echo validation_errors(); ?>
                            <form role="form" action="pgateway/addNewSettings" class="clearfix" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                                <div class="form-group">
                                    <label for="exampleInputEmail1"> <?php echo lang('payment_gateway'); ?> <?php echo lang('name'); ?> &ast;</label>
                                    <input type="text" class="form-control form-control-lg" name="name" value='<?php
                                                                                                                if (!empty($settings->name)) {
                                                                                                                    echo $settings->name;
                                                                                                                }
                                                                                                                ?>' placeholder="" readonly>
                                </div>
                                <?php if ($settings->name == "Pay U Money") { ?>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"> <?php echo lang('merchant_key'); ?> &ast;</label>
                                        <input type="text" class="form-control form-control-lg" name="merchant_key" value="<?php
                                                                                                                            if (!empty($settings->merchant_key)) {
                                                                                                                                echo $settings->merchant_key;
                                                                                                                            }
                                                                                                                            ?>" placeholder="" required="">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo lang('salt'); ?> &ast;</label>
                                        <input type="text" class="form-control form-control-lg" name="salt" value='<?php
                                                                                                                    if (!empty($settings->salt)) {
                                                                                                                        echo $settings->salt;
                                                                                                                    }
                                                                                                                    ?>' required="">
                                    </div> <?php } ?> <?php if ($settings->name == "Paystack") { ?> <div class="form-group">
                                        <label for="exampleInputEmail1"> <?php echo lang('secretkey'); ?> &ast;</label>
                                        <input type="text" class="form-control form-control-lg" name="secret" value="<?php
                                                                                                                        if (!empty($settings->secret)) {
                                                                                                                            echo $settings->secret;
                                                                                                                        }
                                                                                                                        ?>" placeholder="" required="">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo lang('public_key'); ?> &ast;</label>
                                        <input type="text" class="form-control form-control-lg" name="public_key" value='<?php
                                                                                                                            if (!empty($settings->public_key)) {
                                                                                                                                echo $settings->public_key;
                                                                                                                            }
                                                                                                                            ?>' required="">
                                    </div> <?php } ?> <?php if ($settings->name == "PayPal") { ?> <div class="form-group">
                                        <label for="exampleInputEmail1"> <?php echo lang('api_username'); ?> &ast;</label>
                                        <input type="text" class="form-control form-control-lg" name="APIUsername" value="<?php
                                                                                                                            if (!empty($settings->APIUsername)) {
                                                                                                                                echo $settings->APIUsername;
                                                                                                                            }
                                                                                                                            ?>" placeholder="" required="">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo lang('api_password'); ?> &ast;</label>
                                        <input type="text" class="form-control form-control-lg" name="APIPassword" value='<?php
                                                                                                                            if (!empty($settings->APIPassword)) {
                                                                                                                                echo $settings->APIPassword;
                                                                                                                            }
                                                                                                                            ?>' required="">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo lang('api_signature'); ?> &ast;</label>
                                        <input type="text" class="form-control form-control-lg" name="APISignature" value='<?php
                                                                                                                            if (!empty($settings->APISignature)) {
                                                                                                                                echo $settings->APISignature;
                                                                                                                            }
                                                                                                                            ?>' required="">
                                    </div>
                                <?php } ?>
                                <?php if ($settings->name == "SSLCommerz") { ?>
                                    <div class="form-group">
                                        <label>SSLCommerz Store ID &ast;</label>
                                        <input type="text" class="form-control form-control-lg" name="merchant_key" value="<?php echo !empty($settings->merchant_key) ? htmlspecialchars($settings->merchant_key) : ''; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>SSLCommerz Store Password &ast;</label>
                                        <input type="text" class="form-control form-control-lg" name="salt" value="<?php echo !empty($settings->salt) ? htmlspecialchars($settings->salt) : ''; ?>" required>
                                    </div>
                                <?php } ?>
                                <?php if ($settings->name == "bKash") { ?>
                                    <div class="form-group">
                                        <label>bKash App Key &ast;</label>
                                        <input type="text" class="form-control form-control-lg" name="public_key" value="<?php echo !empty($settings->public_key) ? htmlspecialchars($settings->public_key) : ''; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>bKash App Secret &ast;</label>
                                        <input type="text" class="form-control form-control-lg" name="secret" value="<?php echo !empty($settings->secret) ? htmlspecialchars($settings->secret) : ''; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>bKash Username &ast;</label>
                                        <input type="text" class="form-control form-control-lg" name="APIUsername" value="<?php echo !empty($settings->APIUsername) ? htmlspecialchars($settings->APIUsername) : ''; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>bKash Password &ast;</label>
                                        <input type="text" class="form-control form-control-lg" name="APIPassword" value="<?php echo !empty($settings->APIPassword) ? htmlspecialchars($settings->APIPassword) : ''; ?>" required>
                                    </div>
                                <?php } ?>
                                <?php if ($settings->name == "Stripe") { ?>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"> <?php echo lang('secretkey'); ?> &ast;</label>
                                        <input type="text" class="form-control form-control-lg" name="secret" value='<?php
                                                                                                                        if (!empty($settings->secret)) {
                                                                                                                            echo $settings->secret;
                                                                                                                        }
                                                                                                                        ?>' placeholder="" required="">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"> <?php echo lang('publishkey'); ?> &ast;</label>
                                        <input type="text" class="form-control form-control-lg" name="publish" value='<?php
                                                                                                                        if (!empty($settings->publish)) {
                                                                                                                            echo $settings->publish;
                                                                                                                        }
                                                                                                                        ?>' required="">
                                    </div>
                                <?php } ?>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo lang('status'); ?> &ast;</label>
                                    <select class="form-control col-sm-8 m-bot15" name="status" value='' required="">
                                        <option value="live" <?php
                                                                if (!empty($settings->status)) {
                                                                    if ($settings->status == 'live') {
                                                                        echo 'selected';
                                                                    }
                                                                }
                                                                ?>><?php echo lang('live'); ?> </option>
                                        <option value="test" <?php
                                                                if (!empty($settings->status)) {
                                                                    if ($settings->status == 'test') {
                                                                        echo 'selected';
                                                                    }
                                                                }
                                                                ?>><?php echo lang('test'); ?></option>
                                    </select>
                                </div>
                                <input type="hidden" name="id" value='<?php
                                                                        if (!empty($settings->id)) {
                                                                            echo $settings->id;
                                                                        }
                                                                        ?>'>
                                <div class="form-group clearfix">
                                    <button type="submit" name="submit" class="btn btn-info float-right"><?php echo lang('submit'); ?></button>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>

    <!-- /.content -->
</div>






<!--main content end-->
<!--footer start-->

<script src="common/js/codearistos.min.js"></script>
<script src="common/extranal/js/pgateway.js"></script>