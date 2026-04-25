<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('ambulance') . ' ' . lang('service') . ' ' . lang('management'),
        'icon' => 'fas fa-ambulance text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('ambulance'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-end mb-3">
                <a href="ambulance/dashboard" class="btn btn-sm btn-info mr-2">
                    <i class="fas fa-tachometer-alt mr-1"></i> <?php echo lang('dashboard'); ?>
                </a>
                <a href="ambulance/newBooking" class="btn btn-sm btn-success mr-2">
                    <i class="fas fa-plus mr-1"></i> <?php echo lang('new_booking'); ?>
                </a>
                <a href="ambulance/addNewView" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus mr-1"></i> <?php echo lang('add_ambulance'); ?>
                </a>
            </div>

            <div class="row mb-3">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo $available_count; ?></h3>
                            <p><?php echo lang('available_ambulances'); ?></p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-ambulance"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?php echo $on_call_count; ?></h3>
                            <p><?php echo lang('on_call'); ?></p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-phone"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?php echo count($ambulances); ?></h3>
                            <p><?php echo lang('total_ambulances'); ?></p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-truck"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>0</h3>
                            <p><?php echo lang('maintenance'); ?></p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-wrench"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom py-0">
                            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#fleet" role="tab" aria-selected="true">
                                        <i class="fas fa-ambulance mr-1"></i> <?php echo lang('fleet_management'); ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-bookings" role="tab">
                                        <i class="fas fa-calendar-check mr-1"></i> <?php echo lang('bookings'); ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-payments" role="tab">
                                        <i class="fas fa-credit-card mr-1"></i> <?php echo lang('payments'); ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-reports" role="tab">
                                        <i class="fas fa-chart-bar mr-1"></i> <?php echo lang('reports'); ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-rates" role="tab">
                                        <i class="fas fa-dollar-sign mr-1"></i> <?php echo lang('rates'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body p-4">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="fleet" role="tabpanel">
                                    <div class="custom_buttons mb-3"></div>
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered align-middle text-sm datatables mb-0" id="ambulance-table" width="100%">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th class="text-uppercase"><?php echo lang('id'); ?></th>
                                                    <th><?php echo lang('vehicle_number'); ?></th>
                                                    <th><?php echo lang('driver_name'); ?></th>
                                                    <th><?php echo lang('driver_phone'); ?></th>
                                                    <th><?php echo lang('ambulance_type'); ?></th>
                                                    <th><?php echo lang('equipment'); ?></th>
                                                    <th><?php echo lang('capacity'); ?></th>
                                                    <th><?php echo lang('status'); ?></th>
                                                    <th class="no-print"><?php echo lang('options'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($ambulances as $ambulance) { ?>
                                                    <tr>
                                                        <td><?php echo $ambulance->id; ?></td>
                                                        <td><?php echo html_escape($ambulance->vehicle_number); ?></td>
                                                        <td><?php echo html_escape($ambulance->driver_name); ?></td>
                                                        <td><?php echo html_escape($ambulance->driver_phone); ?></td>
                                                        <td><?php echo html_escape($ambulance->ambulance_type); ?></td>
                                                        <td><?php echo html_escape($ambulance->equipment); ?></td>
                                                        <td><?php echo html_escape($ambulance->capacity); ?></td>
                                                        <td>
                                                            <?php if ($ambulance->status == 'Available') { ?>
                                                                <span class="badge badge-success"><?php echo $ambulance->status; ?></span>
                                                            <?php } elseif ($ambulance->status == 'On Call') { ?>
                                                                <span class="badge badge-warning"><?php echo $ambulance->status; ?></span>
                                                            <?php } elseif ($ambulance->status == 'Maintenance') { ?>
                                                                <span class="badge badge-info"><?php echo $ambulance->status; ?></span>
                                                            <?php } else { ?>
                                                                <span class="badge badge-danger"><?php echo $ambulance->status; ?></span>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <a class="btn btn-info btn-xs" href="ambulance/editAmbulance?id=<?php echo (int) $ambulance->id; ?>">
                                                                <i class="fas fa-edit"></i> <?php echo lang('edit'); ?>
                                                            </a>
                                                            <a class="btn btn-danger btn-xs" href="ambulance/deleteAmbulance?id=<?php echo (int) $ambulance->id; ?>" onclick="return confirm('<?php echo lang('are_you_sure'); ?>?');">
                                                                <i class="fas fa-trash"></i> <?php echo lang('delete'); ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-bookings" role="tabpanel">
                                    <div class="text-center py-4">
                                        <h4 class="h5 font-weight-bold"><?php echo lang('ambulance_bookings'); ?></h4>
                                        <p class="text-muted small"><?php echo lang('manage_ambulance_bookings_description'); ?></p>
                                        <a href="ambulance/bookings" class="btn btn-primary">
                                            <i class="fas fa-calendar-check mr-1"></i><?php echo lang('view_all_bookings'); ?>
                                        </a>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-payments" role="tabpanel">
                                    <div class="text-center py-4">
                                        <h4 class="h5 font-weight-bold"><?php echo lang('payment_management'); ?></h4>
                                        <p class="text-muted small"><?php echo lang('track_payments_description'); ?></p>
                                        <a href="ambulance/payments" class="btn btn-success">
                                            <i class="fas fa-credit-card mr-1"></i><?php echo lang('view_payments'); ?>
                                        </a>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-reports" role="tabpanel">
                                    <div class="text-center py-4">
                                        <h4 class="h5 font-weight-bold"><?php echo lang('reports_analytics'); ?></h4>
                                        <p class="text-muted small"><?php echo lang('generate_reports_description'); ?></p>
                                        <a href="ambulance/reports" class="btn btn-info">
                                            <i class="fas fa-chart-bar mr-1"></i><?php echo lang('view_reports'); ?>
                                        </a>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-rates" role="tabpanel">
                                    <div class="text-center py-4">
                                        <h4 class="h5 font-weight-bold"><?php echo lang('service_rates'); ?></h4>
                                        <p class="text-muted small"><?php echo lang('configure_pricing_description'); ?></p>
                                        <a href="ambulance/rates" class="btn btn-warning">
                                            <i class="fas fa-dollar-sign mr-1"></i><?php echo lang('manage_rates'); ?>
                                        </a>
                                    </div>
                                </div>
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
<script src="common/extranal/js/ambulance/ambulance_datatables.js"></script>
