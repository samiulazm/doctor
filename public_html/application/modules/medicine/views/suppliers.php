<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('medicine') . ' ' . lang('suppliers'),
        'icon' => 'fas fa-truck text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('medicine'), 'url' => 'medicine'),
            array('label' => lang('suppliers'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-end mb-3">
                <a href="medicine/addSupplierView" class="btn btn-sm btn-success">
                    <i class="fas fa-plus-circle mr-1"></i> <?php echo lang('add'); ?> <?php echo lang('new'); ?> <?php echo lang('supplier'); ?>
                </a>
            </div>
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('all'); ?> <?php echo lang('medicine'); ?> <?php echo lang('suppliers'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle text-sm mb-0" id="editable-sample1" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-uppercase"><?php echo lang('id'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('supplier'); ?> <?php echo lang('name'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('company'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('contact'); ?> <?php echo lang('person'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('phone'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('email'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('city'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('credit'); ?> <?php echo lang('limit'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('status'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('actions'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        foreach ($suppliers as $supplier) { ?>
                                            <tr>
                                                <td><?php echo (int) $i++; ?></td>
                                                <td class="font-weight-bold"><?php echo html_escape($supplier->name); ?></td>
                                                <td><?php echo html_escape($supplier->company_name); ?></td>
                                                <td><?php echo html_escape($supplier->contact_person); ?></td>
                                                <td><?php echo html_escape($supplier->phone); ?></td>
                                                <td><?php echo html_escape($supplier->email); ?></td>
                                                <td><?php echo html_escape($supplier->city); ?></td>
                                                <td><?php echo html_escape($settings->currency) . number_format((float) $supplier->credit_limit, 2); ?></td>
                                                <td>
                                                    <?php if ($supplier->status == 'active') { ?>
                                                        <span class="badge badge-success"><?php echo lang('active'); ?></span>
                                                    <?php } else { ?>
                                                        <span class="badge badge-danger"><?php echo lang('inactive'); ?></span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="medicine/editSupplier?id=<?php echo (int) $supplier->id; ?>"
                                                           class="btn btn-primary btn-sm"
                                                           title="<?php echo lang('edit'); ?> <?php echo lang('supplier'); ?>">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <a href="medicine/deleteSupplier?id=<?php echo (int) $supplier->id; ?>"
                                                           class="btn btn-danger btn-sm ml-1"
                                                           onclick="return confirm('<?php echo lang('are_you_sure_you_want_to_delete_this') . ' ' . lang('supplier'); ?>?');"
                                                           title="<?php echo lang('delete'); ?> <?php echo lang('supplier'); ?>">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
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
