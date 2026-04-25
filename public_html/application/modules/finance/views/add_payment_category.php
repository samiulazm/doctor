<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
if (!isset($category)) {
    $category = (object) array();
}
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $is_edit = !empty($category->id);
    $CI->load->view('partials/page_header', array(
        'title' => $is_edit ? lang('edit_invoice_items_lab_tests') : lang('create_invoice_items_lab_tests'),
        'icon' => 'fas fa-procedures text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('payment_procedures'), 'url' => 'finance/paymentCategory'),
            array('label' => $is_edit ? lang('edit_invoice_items_lab_tests') : lang('create_invoice_items_lab_tests'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-primary text-white border-0 py-3">
                            <h2 class="card-title h6 mb-0"><?php echo lang('items_created_here_will_be_appeared_at_the_time_of_creating_invoice'); ?></h2>
                        </div>
                        <div class="card-body p-4 p-md-5">
                            <?php echo validation_errors(); ?>
                            <form role="form" action="finance/addPaymentCategory" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">

                                <div class="form-group mb-4">
                                    <label class="text-uppercase text-sm"><?php echo lang('item_lab_test'); ?> <?php echo lang('name'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg" name="category" value='<?php
                                                                                                                    if (!empty($setval)) {
                                                                                                                        echo set_value('category');
                                                                                                                    }
                                                                                                                    if (!empty($category->category)) {
                                                                                                                        echo $category->category;
                                                                                                                    }
                                                                                                                    ?>' required="">
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-uppercase text-sm"><?php echo lang('item'); ?> <?php echo lang('code'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg" name="code" value='<?php
                                                                                                                if (!empty($setval)) {
                                                                                                                    echo set_value('code');
                                                                                                                }
                                                                                                                if (!empty($category->code)) {
                                                                                                                    echo $category->code;
                                                                                                                }
                                                                                                                ?>' required="">
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-uppercase text-sm"><?php echo lang('service_point'); ?> <span class="text-muted">(<?php echo lang('if_applicable'); ?>)</span></label>
                                    <input type="text" class="form-control form-control-lg" name="description" value='<?php
                                                                                                                        if (!empty($setval)) {
                                                                                                                            echo set_value('description');
                                                                                                                        }
                                                                                                                        if (!empty($category->description)) {
                                                                                                                            echo $category->description;
                                                                                                                        }
                                                                                                                        ?>'>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-uppercase text-sm"><?php echo lang('price'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg" name="c_price" value='<?php
                                                                                                                    if (!empty($setval)) {
                                                                                                                        echo set_value('c_price');
                                                                                                                    }
                                                                                                                    if (!empty($category->c_price)) {
                                                                                                                        echo $category->c_price;
                                                                                                                    }
                                                                                                                    ?>' required="">
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-uppercase text-sm"><?php echo lang('doctors_commission'); ?> <?php echo lang('rate'); ?> (%) <span class="text-muted">(<?php echo lang('if_applicable'); ?>)</span></label>
                                    <input type="text" class="form-control form-control-lg" name="d_commission" value='<?php
                                                                                                                        if (!empty($setval)) {
                                                                                                                            echo set_value('d_commission');
                                                                                                                        }
                                                                                                                        if (!empty($category->d_commission)) {
                                                                                                                            echo $category->d_commission;
                                                                                                                        }
                                                                                                                        ?>'>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-uppercase text-sm"><?php echo lang('type'); ?> <span title="For lab tests that require reporting, choose 'Lab Test'. For all others, select 'Other'" data-toggle="tooltip"><i class="fa fa-question-circle"></i></span></label>
                                    <select class="form-control form-control-lg" name="type">
                                        <option value="diagnostic" <?php
                                                                    if (!empty($setval)) {
                                                                        if (set_value('type') == 'diagnostic') {
                                                                            echo 'selected';
                                                                        }
                                                                    }
                                                                    if (!empty($category->type)) {
                                                                        if ($category->type == 'diagnostic') {
                                                                            echo 'selected';
                                                                        }
                                                                    }
                                                                    ?>><?php echo lang('lab_test'); ?></option>
                                        <option value="others" <?php
                                                                if (!empty($setval)) {
                                                                    if (set_value('type') == 'others') {
                                                                        echo 'selected';
                                                                    }
                                                                }
                                                                if (!empty($category->type)) {
                                                                    if ($category->type == 'others') {
                                                                        echo 'selected';
                                                                    }
                                                                }
                                                                ?>><?php echo lang('others'); ?></option>
                                    </select>
                                </div>

                                <input type="hidden" name="id" value='<?php
                                                                        if (!empty($category->id)) {
                                                                            echo $category->id;
                                                                        }
                                                                        ?>'>

                                <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block">
                                    <i class="fas fa-check-circle mr-3"></i><?php echo lang('submit'); ?>
                                </button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="common/js/codearistos.min.js"></script>
