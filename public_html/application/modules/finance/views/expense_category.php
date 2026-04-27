<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('expense_categories'),
        'icon' => 'fas fa-folder-open text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('expense_categories'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-end mb-3">
                <a href="finance/addExpenseCategoryView" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus mr-1"></i> <?php echo lang('add_expense_category'); ?>
                </a>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('All the expense categories. Selecting a category is required to create a expense'); ?></h3>
                        </div>

                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle text-sm datatables mb-0" id="dt-finance" data-legacy-table="editable-sample" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-uppercase"><?php echo lang('category'); ?></th>
                                            <th><?php echo lang('description'); ?></th>
                                            <?php if ($this->ion_auth->in_group('admin')) { ?>
                                                <th class="no-print"><?php echo lang('options'); ?></th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($categories as $category) { ?>
                                            <tr class="">
                                                <td><?php echo $category->category; ?></td>
                                                <td><?php echo $category->description; ?></td>
                                                <?php if ($this->ion_auth->in_group('admin')) { ?>
                                                    <td class="no-print">
                                                        <a class="btn btn-primary btn-sm editbutton mr-1" title="<?php echo lang('edit'); ?>" href="finance/editExpenseCategory?id=<?php echo $category->id; ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a class="btn btn-danger btn-sm delete_button" title="<?php echo lang('delete'); ?>" href="finance/deleteExpenseCategory?id=<?php echo $category->id; ?>" onclick="return confirm('Are you sure you want to delete this item?');">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
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
<script src="common/extranal/js/finance/expense_category.js"></script>
