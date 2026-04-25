<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<link href="common/extranal/css/finance/daily.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

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
$daily_expense_title = date('F, Y', $first_minute) . ' ' . lang('hospital') . ' ' . lang('expense_report');
?>
<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => $daily_expense_title,
        'icon' => 'fas fa-receipt text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => $daily_expense_title, 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
                <a href="finance/dailyExpense?year=<?php echo $previous_year; ?>&month=<?php echo $previous_month; ?>" class="btn btn-sm btn-warning">
                    <i class="fas fa-arrow-left"></i> <?php echo lang('previous'); ?>
                </a>
                <a href="finance/dailyExpense?year=<?php echo $next_year; ?>&month=<?php echo $next_month; ?>" class="btn btn-sm btn-success">
                    <i class="fas fa-arrow-right"></i> <?php echo lang('next'); ?>
                </a>
                <button type="button" class="btn btn-sm btn-secondary" onclick="window.print();">
                    <i class="fas fa-print"></i> <?php echo lang('print'); ?>
                </button>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo $daily_expense_title; ?></h3>
                        </div>

                        <div class="card-body p-4">
                            <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle mb-0" id="editable-sample">
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
                                        if (!empty($all_expenses[date('D d-m-y', $time)])) {
                                            if (date('m', $time) == $month) {
                                                $day = date('d-m-y', $time);
                                                $weekday = date('l', $time);
                                                $amount = $all_expenses[date('D d-m-y', $time)];
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
