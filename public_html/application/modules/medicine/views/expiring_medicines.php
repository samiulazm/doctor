<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('expiring') . ' ' . lang('medicines'),
        'icon' => 'fas fa-exclamation-triangle text-warning mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('medicine'), 'url' => 'medicine'),
            array('label' => lang('batches'), 'url' => 'medicine/batches'),
            array('label' => lang('expiring') . ' ' . lang('medicines'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-end mb-3">
                <a href="medicine/batches" class="btn btn-sm btn-secondary">
                    <i class="fas fa-boxes mr-1"></i> <?php echo lang('all'); ?> <?php echo lang('batches'); ?>
                </a>
            </div>
            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title"><?php echo lang('expired'); ?></h6>
                                    <h3 class="mb-0">
                                        <?php 
                                        $expired_count = 0;
                                        foreach ($expiring_medicines as $medicine) {
                                            if (strtotime($medicine->expiry_date) < time()) {
                                                $expired_count++;
                                            }
                                        }
                                        echo $expired_count;
                                        ?>
                                    </h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-times-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title"><?php echo lang('expiring'); ?> <?php echo lang('in'); ?> 30 <?php echo lang('days'); ?></h6>
                                    <h3 class="mb-0">
                                        <?php 
                                        $expiring_30_count = 0;
                                        foreach ($expiring_medicines as $medicine) {
                                            $days_diff = (strtotime($medicine->expiry_date) - time()) / (60 * 60 * 24);
                                            if ($days_diff > 0 && $days_diff <= 30) {
                                                $expiring_30_count++;
                                            }
                                        }
                                        echo $expiring_30_count;
                                        ?>
                                    </h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title"><?php echo lang('expiring'); ?> <?php echo lang('in'); ?> 90 <?php echo lang('days'); ?></h6>
                                    <h3 class="mb-0">
                                        <?php 
                                        $expiring_90_count = 0;
                                        foreach ($expiring_medicines as $medicine) {
                                            $days_diff = (strtotime($medicine->expiry_date) - time()) / (60 * 60 * 24);
                                            if ($days_diff > 30 && $days_diff <= 90) {
                                                $expiring_90_count++;
                                            }
                                        }
                                        echo $expiring_90_count;
                                        ?>
                                    </h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title"><?php echo lang('total'); ?> <?php echo lang('value'); ?> <?php echo lang('at'); ?> <?php echo lang('risk'); ?></h6>
                                    <h3 class="mb-0">
                                        <?php 
                                        $total_value = 0;
                                        foreach ($expiring_medicines as $medicine) {
                                            $total_value += $medicine->current_stock * $medicine->unit_cost;
                                        }
                                        echo $settings->currency . number_format($total_value, 2);
                                        ?>
                                    </h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('medicines'); ?> <?php echo lang('expiring'); ?> <?php echo lang('within'); ?> 90 <?php echo lang('days'); ?></h3>
                            <span class="badge badge-info">
                                <?php echo lang('total'); ?>: <?php echo count($expiring_medicines); ?> <?php echo lang('batches'); ?>
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle text-sm mb-0" id="expiringMedicinesTable" width="100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="font-weight-bold text-uppercase"><?php echo lang('medicine'); ?> <?php echo lang('name'); ?></th>
                                        <th class="font-weight-bold text-uppercase"><?php echo lang('generic'); ?></th>
                                        <th class="font-weight-bold text-uppercase"><?php echo lang('batch'); ?> <?php echo lang('number'); ?></th>
                                        <th class="font-weight-bold text-uppercase"><?php echo lang('supplier'); ?></th>
                                        <th class="font-weight-bold text-uppercase"><?php echo lang('expiry'); ?> <?php echo lang('date'); ?></th>
                                        <th class="font-weight-bold text-uppercase"><?php echo lang('current'); ?> <?php echo lang('stock'); ?></th>
                                        <th class="font-weight-bold text-uppercase"><?php echo lang('unit'); ?> <?php echo lang('cost'); ?></th>
                                        <th class="font-weight-bold text-uppercase"><?php echo lang('total'); ?> <?php echo lang('value'); ?></th>
                                        <th class="font-weight-bold text-uppercase"><?php echo lang('days'); ?> <?php echo lang('to'); ?> <?php echo lang('expiry'); ?></th>
                                        <th class="font-weight-bold text-uppercase"><?php echo lang('status'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($expiring_medicines as $medicine) { 
                                        $expiry_date = new DateTime($medicine->expiry_date);
                                        $today = new DateTime();
                                        $days_to_expiry = $today->diff($expiry_date)->days;
                                        $is_expired = $expiry_date < $today;
                                        
                                        if ($is_expired) {
                                            $days_to_expiry = -$days_to_expiry;
                                        }
                                        
                                        $total_value = $medicine->current_stock * $medicine->unit_cost;
                                    ?>
                                        <tr class="<?php 
                                            if ($is_expired) {
                                                echo 'table-danger';
                                            } elseif ($days_to_expiry <= 30) {
                                                echo 'table-warning';
                                            } else {
                                                echo 'table-info';
                                            }
                                        ?>">
                                            <td class="font-weight-bold"><?php echo $medicine->medicine_name; ?></td>
                                            <td><?php echo $medicine->generic; ?></td>
                                            <td>
                                                <span class="badge badge-secondary"><?php echo $medicine->batch_number; ?></span>
                                            </td>
                                            <td><?php echo $medicine->supplier_name; ?></td>
                                            <td>
                                                <span class="font-weight-bold">
                                                    <?php echo date('d M Y', strtotime($medicine->expiry_date)); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="font-weight-bold"><?php echo $medicine->current_stock; ?></span>
                                            </td>
                                            <td><?php echo $settings->currency . number_format($medicine->unit_cost, 2); ?></td>
                                            <td>
                                                <span class="font-weight-bold text-primary">
                                                    <?php echo $settings->currency . number_format($total_value, 2); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php 
                                                if ($is_expired) {
                                                    echo '<span class="badge badge-danger">' . lang('expired') . ' ' . abs($days_to_expiry) . ' ' . lang('days') . ' ' . lang('ago') . '</span>';
                                                } elseif ($days_to_expiry <= 0) {
                                                    echo '<span class="badge badge-danger">' . lang('expires') . ' ' . lang('today') . '</span>';
                                                } elseif ($days_to_expiry <= 7) {
                                                    echo '<span class="badge badge-danger">' . $days_to_expiry . ' ' . lang('days') . '</span>';
                                                } elseif ($days_to_expiry <= 30) {
                                                    echo '<span class="badge badge-warning">' . $days_to_expiry . ' ' . lang('days') . '</span>';
                                                } else {
                                                    echo '<span class="badge badge-info">' . $days_to_expiry . ' ' . lang('days') . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                if ($is_expired) {
                                                    echo '<span class="badge badge-danger">' . lang('expired') . '</span>';
                                                } elseif ($days_to_expiry <= 7) {
                                                    echo '<span class="badge badge-danger">' . lang('critical') . '</span>';
                                                } elseif ($days_to_expiry <= 30) {
                                                    echo '<span class="badge badge-warning">' . lang('warning') . '</span>';
                                                } else {
                                                    echo '<span class="badge badge-info">' . lang('watch') . '</span>';
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

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
</script>
<script>
$(document).ready(function() {
    var table = $('#expiringMedicinesTable').DataTable({
        order: [[8, 'asc']],
        pageLength: 25,
        responsive: true,
        dom: "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4 text-right'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copyHtml5', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] } },
            { extend: 'excelHtml5', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] } },
            { extend: 'csvHtml5', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] } },
            { extend: 'pdfHtml5', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] } },
            { extend: 'print', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] } },
        ],
        columnDefs: [
            { targets: [8], type: 'num' }
        ],
        language: {
            lengthMenu: '_MENU_',
            search: '_INPUT_',
            searchPlaceholder: 'Search...',
            url: 'common/assets/DataTables/languages/' + language + '.json'
        }
    });
    table.buttons().container().appendTo('.custom_buttons');
});
</script>

<!--main content end-->
<!--footer start-->
