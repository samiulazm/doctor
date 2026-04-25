<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('medicine') . ' ' . lang('batches'),
        'icon' => 'fas fa-boxes text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('medicine'), 'url' => 'medicine'),
            array('label' => lang('batches'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-end mb-3">
                <a href="medicine/expiringMedicines" class="btn btn-sm btn-warning">
                    <i class="fas fa-exclamation-triangle mr-1"></i> <?php echo lang('expiring'); ?> <?php echo lang('medicines'); ?>
                </a>
            </div>
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('all'); ?> <?php echo lang('medicine'); ?> <?php echo lang('batches'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle text-sm mb-0" id="editable-sample1" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-uppercase"><?php echo lang('medicine'); ?> <?php echo lang('name'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('generic'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('batch'); ?> <?php echo lang('number'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('supplier'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('manufacturing'); ?> <?php echo lang('date'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('expiry'); ?> <?php echo lang('date'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('current'); ?> <?php echo lang('stock'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('unit'); ?> <?php echo lang('cost'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('selling'); ?> <?php echo lang('price'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('status'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('days'); ?> <?php echo lang('to'); ?> <?php echo lang('expiry'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($batches as $batch) {
                                            $expiry_date = new DateTime($batch->expiry_date);
                                            $today = new DateTime();
                                            $days_to_expiry = $today->diff($expiry_date)->days;
                                            $is_expired = $expiry_date < $today;

                                            if ($is_expired) {
                                                $days_to_expiry = -$days_to_expiry;
                                            }
                                        ?>
                                            <tr class="<?php
                                                if ($is_expired) {
                                                    echo 'table-danger';
                                                } elseif ($days_to_expiry <= 30) {
                                                    echo 'table-warning';
                                                } elseif ($days_to_expiry <= 90) {
                                                    echo 'table-info';
                                                }
                                            ?>">
                                                <td class="font-weight-bold"><?php echo html_escape($batch->medicine_name); ?></td>
                                                <td><?php echo html_escape($batch->generic); ?></td>
                                                <td>
                                                    <span class="badge badge-primary"><?php echo html_escape($batch->batch_number); ?></span>
                                                </td>
                                                <td><?php echo html_escape($batch->supplier_name); ?></td>
                                                <td>
                                                    <?php echo $batch->manufacturing_date ? date('d M Y', strtotime($batch->manufacturing_date)) : '-'; ?>
                                                </td>
                                                <td>
                                                    <?php echo date('d M Y', strtotime($batch->expiry_date)); ?>
                                                </td>
                                                <td>
                                                    <?php if ($batch->current_stock <= 0) { ?>
                                                        <span class="text-danger font-weight-bold"><?php echo lang('out_of_stock'); ?></span>
                                                    <?php } else { ?>
                                                        <span class="font-weight-bold"><?php echo html_escape($batch->current_stock); ?></span>
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo html_escape($settings->currency) . number_format((float) $batch->unit_cost, 2); ?></td>
                                                <td><?php echo html_escape($settings->currency) . number_format((float) $batch->selling_price, 2); ?></td>
                                                <td>
                                                    <?php
                                                    if ($is_expired) {
                                                        echo '<span class="badge badge-danger">' . lang('expired') . '</span>';
                                                    } elseif ($batch->current_stock <= 0) {
                                                        echo '<span class="badge badge-dark">' . lang('out_of_stock') . '</span>';
                                                    } elseif ($days_to_expiry <= 30) {
                                                        echo '<span class="badge badge-warning">' . lang('expiring_soon') . '</span>';
                                                    } else {
                                                        echo '<span class="badge badge-success">' . lang('active') . '</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($is_expired) {
                                                        echo '<span class="text-danger font-weight-bold">' . lang('expired') . ' ' . abs($days_to_expiry) . ' ' . lang('days') . ' ' . lang('ago') . '</span>';
                                                    } elseif ($days_to_expiry <= 0) {
                                                        echo '<span class="text-danger font-weight-bold">' . lang('expires') . ' ' . lang('today') . '</span>';
                                                    } elseif ($days_to_expiry <= 30) {
                                                        echo '<span class="text-warning font-weight-bold">' . (int) $days_to_expiry . ' ' . lang('days') . '</span>';
                                                    } else {
                                                        echo '<span class="text-success">' . (int) $days_to_expiry . ' ' . lang('days') . '</span>';
                                                    }
                                                    ?>
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
