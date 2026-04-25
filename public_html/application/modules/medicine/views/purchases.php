<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('medicine') . ' ' . lang('purchases'),
        'icon' => 'fas fa-shopping-cart text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('medicine'), 'url' => 'medicine'),
            array('label' => lang('purchases'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-end mb-3">
                <a href="medicine/addPurchaseView" class="btn btn-sm btn-success">
                    <i class="fas fa-plus-circle mr-1"></i> <?php echo lang('create'); ?> <?php echo lang('purchase_order'); ?>
                </a>
            </div>
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('all'); ?> <?php echo lang('medicine'); ?> <?php echo lang('purchase_orders'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle text-sm mb-0" id="editable-sample1" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-uppercase"><?php echo lang('purchase_order'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('supplier'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('purchase_date'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('invoice_no'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('total_amount'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('paid_amount'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('balance'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('purchase_status'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('payment_status'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('actions'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($purchases as $purchase) { ?>
                                            <tr>
                                                <td class="font-weight-bold"><?php echo html_escape($purchase->purchase_order_no); ?></td>
                                                <td><?php echo html_escape($purchase->supplier_name); ?></td>
                                                <td><?php echo date('d M Y', strtotime($purchase->purchase_date)); ?></td>
                                                <td><?php echo html_escape($purchase->invoice_number); ?></td>
                                                <td><?php echo html_escape($settings->currency) . number_format((float) $purchase->net_amount, 2); ?></td>
                                                <td><?php echo html_escape($settings->currency) . number_format((float) $purchase->paid_amount, 2); ?></td>
                                                <td><?php echo html_escape($settings->currency) . number_format((float) $purchase->balance_amount, 2); ?></td>
                                                <td>
                                                    <?php
                                                    $status_colors = array(
                                                        'pending' => 'warning',
                                                        'ordered' => 'info',
                                                        'received' => 'success',
                                                        'partial' => 'secondary',
                                                        'cancelled' => 'danger',
                                                    );
                                                    $color = isset($status_colors[$purchase->purchase_status]) ? $status_colors[$purchase->purchase_status] : 'secondary';
                                                    ?>
                                                    <span class="badge badge-<?php echo $color; ?>"><?php echo lang(ucfirst($purchase->purchase_status)); ?></span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $payment_colors = array(
                                                        'pending' => 'warning',
                                                        'partial' => 'info',
                                                        'paid' => 'success',
                                                        'cancelled' => 'danger',
                                                    );
                                                    $color = isset($payment_colors[$purchase->payment_status]) ? $payment_colors[$purchase->payment_status] : 'secondary';
                                                    ?>
                                                    <span class="badge badge-<?php echo $color; ?>"><?php echo lang(ucfirst($purchase->payment_status)); ?></span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <?php if ($purchase->purchase_status == 'pending' || $purchase->purchase_status == 'ordered') { ?>
                                                            <a href="medicine/receivePurchase?id=<?php echo (int) $purchase->id; ?>"
                                                               class="btn btn-success btn-sm"
                                                               title="<?php echo lang('receive'); ?> <?php echo lang('purchase'); ?>">
                                                                <i class="fa fa-check"></i>
                                                            </a>
                                                        <?php } ?>
                                                        <a href="medicine/viewPurchase?id=<?php echo (int) $purchase->id; ?>"
                                                           class="btn btn-info btn-sm"
                                                           title="<?php echo lang('view'); ?> <?php echo lang('purchase'); ?>">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <?php if ($purchase->purchase_status == 'pending') { ?>
                                                            <a href="medicine/editPurchase?id=<?php echo (int) $purchase->id; ?>"
                                                               class="btn btn-primary btn-sm"
                                                               title="<?php echo lang('edit'); ?> <?php echo lang('purchase'); ?>">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        <?php } ?>
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
