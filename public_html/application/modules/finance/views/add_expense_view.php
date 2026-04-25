<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
if (!isset($expense)) {
    $expense = (object) array();
}
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $is_edit = !empty($expense) && !empty($expense->id);
    $CI->load->view('partials/page_header', array(
        'title' => $is_edit ? lang('edit_expense') : lang('add_expense'),
        'icon' => 'fas fa-receipt text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('expense'), 'url' => 'finance/expense'),
            array('label' => $is_edit ? lang('edit_expense') : lang('add_expense'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-body p-4 p-md-5">
                            <?php echo validation_errors(); ?>
                            <form role="form" action="finance/addExpense" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                                <div class="form-group mb-4">
                                    <label class="text-uppercase text-sm"><?php echo lang('category'); ?> <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-lg select2" name="category" required="">
                                        <?php foreach ($categories as $category) { ?>
                                            <option value="<?php echo $category->category; ?>" <?php
                                                                                                if (!empty($setval)) {
                                                                                                    if ($category->category == set_value('category')) {
                                                                                                        echo 'selected';
                                                                                                    }
                                                                                                }
                                                                                                if (!empty($expense->category)) {
                                                                                                    if ($category->category == $expense->category) {
                                                                                                        echo 'selected';
                                                                                                    }
                                                                                                }
                                                                                                ?>> <?php echo $category->category; ?> </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-uppercase text-sm"><?php echo lang('amount'); ?> <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><?php echo $settings->currency; ?></span>
                                        </div>
                                        <input type="number" step="0.01" class="form-control form-control-lg" name="amount" value='<?php
                                                                                                                                    if (!empty($setval)) {
                                                                                                                                        echo set_value('amount');
                                                                                                                                    }
                                                                                                                                    if (!empty($expense->amount)) {
                                                                                                                                        echo $expense->amount;
                                                                                                                                    }
                                                                                                                                    ?>' required="">
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-uppercase text-sm"><?php echo lang('remarks'); ?></label>
                                    <textarea class="form-control form-control-lg" name="note" rows="3"><?php
                                                                                                        if (!empty($setval)) {
                                                                                                            echo set_value('note');
                                                                                                        }
                                                                                                        if (!empty($expense->note)) {
                                                                                                            echo $expense->note;
                                                                                                        }
                                                                                                        ?></textarea>
                                </div>

                                <input type="hidden" name="id" value='<?php
                                                                        if (!empty($expense->id)) {
                                                                            echo $expense->id;
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
