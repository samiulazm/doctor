<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('inventory_dashboard'),
        'icon' => 'fas fa-boxes text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('inventory'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-end mb-3">
                <a href="inventory/items" class="btn btn-sm btn-success">
                    <i class="fa fa-list mr-1"></i> <?php echo lang('manage_inventory_items'); ?>
                </a>
            </div>
            <div class="row mb-4">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo (int) $total_items; ?></h3>
                            <p><?php echo lang('inventory_items'); ?></p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <a href="inventory/items" class="small-box-footer">
                            <?php echo lang('more_info'); ?> <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?php echo (int) $low_stock_items; ?></h3>
                            <p><?php echo lang('low_stock_items'); ?></p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <a href="inventory/low_stock" class="small-box-footer">
                            <?php echo lang('more_info'); ?> <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?php echo (int) $pending_orders; ?></h3>
                            <p><?php echo lang('pending_deliveries'); ?></p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <a href="inventory/purchase/pending_deliveries" class="small-box-footer">
                            <?php echo lang('more_info'); ?> <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?php echo (int) $overdue_deliveries; ?></h3>
                            <p><?php echo lang('overdue_deliveries'); ?></p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <a href="inventory/purchase/pending_deliveries" class="small-box-footer">
                            <?php echo lang('more_info'); ?> <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase">
                                <i class="fas fa-clipboard-list mr-2"></i>
                                <?php echo lang('recent') . ' ' . lang('usage_logs'); ?>
                            </h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered text-sm align-middle mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th><?php echo lang('item_name'); ?></th>
                                            <th><?php echo lang('quantity_used'); ?></th>
                                            <th><?php echo lang('usage_date'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recent_usage)) { ?>
                                            <?php foreach ($recent_usage as $usage) { ?>
                                                <tr>
                                                    <td><?php echo html_escape($usage->item_name); ?></td>
                                                    <td><?php echo html_escape($usage->quantity_used); ?></td>
                                                    <td><?php echo date('Y-m-d H:i', strtotime($usage->usage_date)); ?></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <tr>
                                                <td colspan="3" class="text-center"><?php echo lang('no_data_available'); ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3">
                                <a href="inventory/usage" class="btn btn-primary btn-sm"><?php echo lang('view_all'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase">
                                <i class="fas fa-calendar-times mr-2"></i>
                                <?php echo lang('expiring_items'); ?> (<?php echo '30 ' . lang('days'); ?>)
                            </h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered text-sm align-middle mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th><?php echo lang('item_name'); ?></th>
                                            <th><?php echo lang('batch_number'); ?></th>
                                            <th><?php echo lang('expiry_date'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($expiring_items)) { ?>
                                            <?php foreach ($expiring_items as $item) { ?>
                                                <tr>
                                                    <td><?php echo html_escape($item->item_name); ?></td>
                                                    <td><?php echo html_escape($item->batch_number); ?></td>
                                                    <td class="text-warning"><?php echo date('Y-m-d', strtotime($item->expiry_date)); ?></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <tr>
                                                <td colspan="3" class="text-center"><?php echo lang('no_data_available'); ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                <?php echo lang('quick_access'); ?>
                            </h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <a href="inventory/items" class="btn btn-outline-primary btn-block">
                                        <i class="fas fa-list mr-2"></i>
                                        <?php echo lang('inventory_items'); ?>
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="inventory/usage" class="btn btn-outline-success btn-block">
                                        <i class="fas fa-clipboard-list mr-2"></i>
                                        <?php echo lang('usage_logs'); ?>
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="inventory/purchase" class="btn btn-outline-info btn-block">
                                        <i class="fas fa-shopping-cart mr-2"></i>
                                        <?php echo lang('purchase_orders'); ?>
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="inventory/reports" class="btn btn-outline-warning btn-block">
                                        <i class="fas fa-chart-bar mr-2"></i>
                                        <?php echo lang('inventory_reports'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
