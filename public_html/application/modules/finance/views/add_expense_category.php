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
        'title' => $is_edit ? lang('edit_expense_category') : lang('add_expense_category'),
        'icon' => 'fas fa-folder-open text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('expense_categories'), 'url' => 'finance/expenseCategory'),
            array('label' => $is_edit ? lang('edit_expense_category') : lang('add_expense_category'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('expense_categories'); ?></h3>
                        </div>
                        <div class="card-body p-4 p-md-5">
                            <?php echo validation_errors(); ?>
                            <form role="form" action="finance/addExpenseCategory" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">

                                <div class="form-group mb-4">
                                    <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('category'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg shadow-sm" name="category" value='<?php
                                                                                                                                if (!empty($setval)) {
                                                                                                                                    echo set_value('category');
                                                                                                                                }
                                                                                                                                if (!empty($category->category)) {
                                                                                                                                    echo $category->category;
                                                                                                                                }
                                                                                                                                ?>' required="">
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('description'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg shadow-sm" name="description" value='<?php
                                                                                                                                if (!empty($setval)) {
                                                                                                                                    echo set_value('description');
                                                                                                                                }
                                                                                                                                if (!empty($category->description)) {
                                                                                                                                    echo $category->description;
                                                                                                                                }
                                                                                                                                ?>' required="">
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
