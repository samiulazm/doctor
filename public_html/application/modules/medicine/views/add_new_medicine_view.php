<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
if (!isset($medicine)) {
    $medicine = (object) array();
}
$medicine_page_title = !empty($medicine->id) ? (lang('edit') . ' ' . lang('medicine')) : (lang('add') . ' ' . lang('new') . ' ' . lang('medicine'));
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => $medicine_page_title,
        'icon' => 'fas fa-pills text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('medicine'), 'url' => 'medicine'),
            array('label' => $medicine_page_title, 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo $medicine_page_title; ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <?php echo validation_errors(); ?>
                            <form role="form" action="medicine/addNewMedicine" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label class="text-uppercase text-sm"><?php echo lang('name'); ?> <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg" name="name" value='<?php
                                                                                                                        if (!empty($medicine->name)) {
                                                                                                                            echo $medicine->name;
                                                                                                                        }
                                                                                                                        ?>' required="">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label class="text-uppercase text-sm"><?php echo lang('category'); ?> <span class="text-danger">*</span></label>
                                            <select class="form-control form-control-lg select2" name="category" required="">
                                                <?php foreach ($categories as $category) { ?>
                                                    <option value="<?php echo $category->category; ?>" <?php
                                                                                                        if (!empty($medicine->category)) {
                                                                                                            if ($category->category == $medicine->category) {
                                                                                                                echo 'selected';
                                                                                                            }
                                                                                                        }
                                                                                                        ?>> <?php echo $category->category; ?> </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label class="text-uppercase text-sm"><?php echo lang('purchase'); ?> <?php echo lang('price'); ?> <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" class="form-control form-control-lg" name="price" value='<?php
                                                                                                                                        if (!empty($medicine->price)) {
                                                                                                                                            echo $medicine->price;
                                                                                                                                        }
                                                                                                                                        ?>' required="">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label class="text-uppercase text-sm"><?php echo lang('selling'); ?> <?php echo lang('price'); ?> <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" class="form-control form-control-lg" name="s_price" value='<?php
                                                                                                                                        if (!empty($medicine->s_price)) {
                                                                                                                                            echo $medicine->s_price;
                                                                                                                                        }
                                                                                                                                        ?>' required="">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label class="text-uppercase text-sm"><?php echo lang('store'); ?> <?php echo lang('box'); ?></label>
                                            <input type="text" class="form-control form-control-lg" name="box" value='<?php
                                                                                                                        if (!empty($medicine->box)) {
                                                                                                                            echo $medicine->box;
                                                                                                                        }
                                                                                                                        ?>'>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label class="text-uppercase text-sm"><?php echo lang('quantity'); ?> <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" class="form-control form-control-lg" name="quantity" value='<?php
                                                                                                                                            if (!empty($medicine->quantity)) {
                                                                                                                                                echo $medicine->quantity;
                                                                                                                                            }
                                                                                                                                            ?>' required="">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label class="text-uppercase text-sm"><?php echo lang('generic'); ?> <?php echo lang('name'); ?> <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg" name="generic" value='<?php
                                                                                                                            if (!empty($medicine->generic)) {
                                                                                                                                echo $medicine->generic;
                                                                                                                            }
                                                                                                                            ?>' required="">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label class="text-uppercase text-sm"><?php echo lang('company'); ?></label>
                                            <input type="text" class="form-control form-control-lg" name="company" value='<?php
                                                                                                                            if (!empty($medicine->company)) {
                                                                                                                                echo $medicine->company;
                                                                                                                            }
                                                                                                                            ?>'>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label class="text-uppercase text-sm"><?php echo lang('effects'); ?></label>
                                            <input type="text" class="form-control form-control-lg" name="effects" value='<?php
                                                                                                                            if (!empty($medicine->effects)) {
                                                                                                                                echo $medicine->effects;
                                                                                                                            }
                                                                                                                            ?>'>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label class="text-uppercase text-sm"><?php echo lang('expiry'); ?> <?php echo lang('date'); ?> <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg default-date-picker readonly" name="e_date" value='<?php
                                                                                                                                                        if (!empty($medicine->e_date)) {
                                                                                                                                                            echo $medicine->e_date;
                                                                                                                                                        }
                                                                                                                                                        ?>' required="">
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="id" value='<?php
                                                                        if (!empty($medicine->id)) {
                                                                            echo $medicine->id;
                                                                        }
                                                                        ?>'>

                                <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block"><?php echo lang('submit'); ?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
