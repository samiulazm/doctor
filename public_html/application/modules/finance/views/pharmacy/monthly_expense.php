<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<link href="common/extranal/css/pharmacy/daily.css" rel="stylesheet">
<?php
$currently_processing_year = date('Y', $first_minute);
$next_year = $currently_processing_year + 1;
$previous_year = $currently_processing_year - 1;
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => (int) date('Y', $first_minute) . ' — ' . lang('pharmacy') . ' ' . lang('expense_report'),
        'icon' => 'fas fa-chart-line text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('expense_report'), 'url' => null),
        ),
    ));
    ?>
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-end mb-2">
            <a href="finance/pharmacy/monthlyExpense?year=<?php echo (int) $previous_year; ?>" class="btn btn-sm btn-warning mr-2">
                <i class="fa fa-arrow-left mr-1"></i> <?php echo lang('previous_year'); ?>
            </a>
            <a href="finance/pharmacy/monthlyExpense?year=<?php echo (int) $next_year; ?>" class="btn btn-sm btn-success mr-2">
                <i class="fa fa-arrow-right mr-1"></i> <?php echo lang('next_year'); ?>
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
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo date('Y', $first_minute) . ' ' . lang('pharmacy') . ' ' . lang('expense_report'); ?></h3>
                        </div>

                        <div class="card-body p-4">
                            <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="editable-sample1">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-uppercase"><?php echo lang('date'); ?></th>
                                        <th><?php echo lang('amount'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    for ($month = 1; $month <= 12; $month++) {
                                        $time = mktime(12, 0, 0, $month, 1, $year);
                                        if (!empty($all_expenses[date('m-Y', $time)])) {
                                            if (date('Y', $time) == $year) {
                                                $month_name = date('F', $time);
                                                $amount = $all_expenses[date('m-Y', $time)];
                                            }
                                        } else {
                                            if (date('Y', $time) == $year) {
                                                $month_name = date('F', $time);
                                                $amount = 0;
                                            }
                                        }
                                    ?>
                                        <tr>
                                            <td><?php echo lang($month_name); ?></td>
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

<div id="myModal33" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold"><?php echo lang('stock_alert'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script>
    $(window).on('load', function() {
    });
</script>