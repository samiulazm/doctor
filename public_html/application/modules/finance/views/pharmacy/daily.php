<!--sidebar end-->
<!--main content start-->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<link href="common/extranal/css/pharmacy/daily.css" rel="stylesheet">
<?php
$currently_processing_month = date('m', $first_minute);
$currently_processing_year = date('Y', $first_minute);
if ($currently_processing_month < 12) {
    $next_month = $currently_processing_month + 1;
    $next_year = $currently_processing_year;
} else {
    $next_month = 1;
    $next_year = $currently_processing_year + 1;
}

if ($currently_processing_month > 1) {
    $previous_month = $currently_processing_month - 1;
    $previous_year = $currently_processing_year;
} else {
    $previous_month = 12;
    $previous_year = $currently_processing_year - 1;
}
?>

<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => date('F, Y', $first_minute) . ' — ' . lang('pharmacy') . ' ' . lang('sales_report'),
        'icon' => 'fas fa-chart-line text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('pharmacy') . ' ' . lang('sales_report'), 'url' => null),
        ),
    ));
    ?>
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-end mb-2">
            <a href="finance/pharmacy/daily?year=<?php echo (int) $previous_year; ?>&month=<?php echo (int) $previous_month; ?>" class="btn btn-sm btn-warning mr-2">
                <i class="fa fa-arrow-left mr-1"></i> <?php echo lang('previous_month'); ?>
            </a>
            <a href="finance/pharmacy/daily?year=<?php echo (int) $next_year; ?>&month=<?php echo (int) $next_month; ?>" class="btn btn-sm btn-success mr-2">
                <i class="fa fa-arrow-right mr-1"></i> <?php echo lang('next_month'); ?>
            </a>
            <a class="btn btn-sm btn-secondary" href="javascript:window.print();" role="button">
                <i class="fa fa-print mr-1"></i> <?php echo lang('print'); ?>
            </a>
        </div>
    </div>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo date('F, Y', $first_minute) . ' ' . lang('pharmacy') . ' ' . lang('sales_report'); ?></h3>
                        </div>

                        <div class="card-body p-4">
                            <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="editable-sample">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-uppercase"><?php echo lang('date'); ?></th>
                                        <th><?php echo lang('amount'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $number_of_days = date('t', $first_minute);
                                    for ($d = 1; $d <= $number_of_days; $d++) {
                                        $time = mktime(12, 0, 0, $month, $d, $year);
                                        if (!empty($all_payments[date('D d-m-y', $time)])) {
                                            if (date('m', $time) == $month) {
                                                $day = date('d-m-y', $time);
                                                $weekday = date('l', $time);
                                                $amount = $all_payments[date('D d-m-y', $time)];
                                            }
                                        } else {
                                            if (date('m', $time) == $month) {
                                                $day = date('d-m-y', $time);
                                                $weekday = date('l', $time);
                                                $amount = 0;
                                            }
                                        }
                                    ?>
                                        <tr>
                                            <td><?php echo lang(strtolower($weekday)) . ', ' . $day; ?></td>
                                            <td><?php echo $this->currency; ?><?php echo number_format($amount, 2, '.', ','); ?></td>
                                            <?php $total_amount[] = $amount; ?>
                                        </tr>
                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if (!empty($total_amount)) {
                                        $total_amount = array_sum($total_amount);
                                    } else {
                                        $total_amount = 0;
                                    }
                                    ?>

                                    <tr class="total_amount">
                                        <td class="font-weight-bold"><?php echo lang('total'); ?></td>
                                        <td class="font-weight-bold"><?php echo $this->currency; ?><?php echo number_format($total_amount, 2, '.', ','); ?></td>
                                    </tr>
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