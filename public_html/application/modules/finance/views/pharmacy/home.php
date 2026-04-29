<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<script type="text/javascript" src="common/js/google-loader.js"></script>
<link href="common/extranal/css/pharmacy/home.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('pharmacy') . ' ' . lang('dashboard'),
        'icon' => 'fas fa-pills text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('pharmacy'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="row state-overview">

                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>
                                        <?php echo $settings->currency; ?> <?php echo number_format($today_sales_amount, 2, '.', ','); ?>
                                    </h3>

                                    <p><?php echo lang('today_sales'); ?></p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-social-usd"></i>

                                </div>
                                <?php if ($this->ion_auth->in_group('admin')) { ?>
                                    <a href="finance/pharmacy/todaySales" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                                <?php } ?>
                            </div>
                        </div>


                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>
                                        <?php echo $settings->currency; ?> <?php echo number_format($today_expenses_amount, 2, '.', ','); ?>
                                    </h3>

                                    <p><?php echo lang('today_expense'); ?></p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-social-usd"></i>
                                </div>
                                <?php if ($this->ion_auth->in_group('admin')) { ?>
                                    <a href="finance/pharmacy/todayExpense" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                                <?php } ?>
                            </div>
                        </div>


                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>
                                        <?php echo count($medicines); ?>
                                    </h3>

                                    <p><?php echo lang('medicine'); ?></p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-medkit"></i>
                                </div>
                                <?php if ($this->ion_auth->in_group('admin')) { ?>
                                    <a href="medicine" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                                <?php } ?>
                            </div>
                        </div>


                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-secondary">
                                <div class="inner">
                                    <h3>
                                        <?php echo count($accountants); ?>
                                    </h3>

                                    <p><?php echo lang('staff'); ?></p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <?php if ($this->ion_auth->in_group('admin')) { ?>
                                    <a href="accountant" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                                <?php } ?>
                            </div>
                        </div>


                        <?php if ($this->ion_auth->in_group(array('admin', 'Pharmacist'))) { ?>

                            <div class="col-lg-6 col-sm-12">
                                <div id="chart_div" class="card"></div>
                                <div class="card">
                                    <div class="card-header"> <?php echo lang('latest_sales'); ?></div>
                                    <div class="table-responsive">
                                    <table class="table table-striped table-hover table-bordered" id="">
                                        <thead>
                                            <tr>
                                                <th> <?php echo lang('date'); ?> </th>
                                                <th> <?php echo lang('grand_total'); ?> </th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php
                                            $i = 0;
                                            foreach ($payments as $payment) {
                                                $i = $i + 1;
                                            ?>
                                                <?php $patient_info = $this->db->get_where('patient', array('id' => $payment->patient))->row(); ?>
                                                <tr class="">
                                                    <td><?php echo date('d/m/y', $payment->date); ?></td>
                                                    <td><?php echo $settings->currency; ?> <?php echo number_format($payment->gross_total, 2, '.', ','); ?></td>
                                                </tr>
                                            <?php
                                                if ($i == 10)
                                                    break;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header"> <?php echo lang('latest_expense'); ?></div>
                                    <div class="table-responsive">
                                    <table class="table table-striped table-hover table-bordered" id="">
                                        <thead>
                                            <tr>
                                                <th> <?php echo lang('category'); ?> </th>
                                                <th> <?php echo lang('date'); ?> </th>
                                                <th> <?php echo lang('amount'); ?> </th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php
                                            $i = 0;
                                            foreach ($expenses as $expense) {
                                                $i = $i + 1;
                                            ?>
                                                <tr class="">
                                                    <td><?php echo $expense->category; ?></td>
                                                    <td> <?php echo date('d/m/y', $expense->date); ?></td>
                                                    <td><?php echo $settings->currency; ?> <?php echo number_format($expense->amount, 2, '.', ','); ?></td>
                                                </tr>
                                            <?php
                                                if ($i == 10)
                                                    break;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                    </div>
                                </div>




                            </div>

                        <?php } ?>

                        <div class="col-md-6">
                            <!--work progress start-->
                            <section class="card statistics">
                                <div class="card-body progress-card">
                                    <div class="task-progress">
                                        <h1><?php echo lang('statistics'); ?></h1>
                                        <p><?php echo lang('this_month'); ?></p>
                                    </div>
                                </div>
                                <table class="table table-hover personal-task">
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>
                                                <?php echo lang('number_of_sales'); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-important">
                                                    <?php
                                                    $this->db->where('hospital_id', $this->hospital_id);
                                                    $query_n_o_s = $this->db->get('pharmacy_payment')->result();
                                                    $i = 0;
                                                    foreach ($query_n_o_s as $q_n_o_s) {
                                                        if (date('m/y', time()) == date('m/y', $q_n_o_s->date)) {
                                                            $i = $i + 1;
                                                        }
                                                    }
                                                    echo $i;
                                                    ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div id="work-progress1"><canvas class="work-progress1" width="47" height="20"></canvas></div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>2</td>
                                            <td>
                                                <?php echo lang('total_sales'); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-important">
                                                    <?php echo $settings->currency; ?>
                                                    <?php
                                                    $this->db->where('hospital_id', $this->hospital_id);
                                                    $query = $this->db->get('pharmacy_payment')->result();
                                                    $sales_total = array();
                                                    foreach ($query as $q) {
                                                        if (date('m', time()) == date('m', $q->date)) {
                                                            $sales_total[] = $q->gross_total;
                                                        }
                                                    }
                                                    if (!empty($sales_total)) {
                                                        echo number_format(array_sum($sales_total), 2, '.', ',');
                                                    }
                                                    ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div id="work-progress1"><canvas class="work-progress1" width="47" height="20"></canvas></div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>3</td>
                                            <td>
                                                <?php echo lang('number_of_expenses'); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <?php
                                                    $this->db->where('hospital_id', $this->hospital_id);
                                                    $query_n_o_e = $this->db->get('pharmacy_expense')->result();
                                                    $i = 0;
                                                    foreach ($query_n_o_e as $q_n_o_e) {
                                                        if (date('m', time()) == date('m', $q_n_o_e->date)) {
                                                            $i = $i + 1;
                                                        }
                                                    }
                                                    echo $i;
                                                    ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div id="work-progress2"><canvas class="work-progress2" width="47" height="22"></canvas></div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>4</td>
                                            <td>
                                                <?php echo lang('total_expense'); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <?php echo $settings->currency; ?>
                                                    <?php
                                                    $this->db->where('hospital_id', $this->hospital_id);
                                                    $query_expense = $this->db->get('pharmacy_expense')->result();
                                                    $sales_total = array();
                                                    foreach ($query_expense as $q_expense) {
                                                        if (date('m', time()) == date('m', $q_expense->date)) {
                                                            $expense_total[] = $q_expense->amount;
                                                        }
                                                    }
                                                    if (!empty($expense_total)) {
                                                        echo number_format(array_sum($expense_total), 2, '.', ',');
                                                    }
                                                    ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div id="work-progress2"><canvas class="work-progress2" width="47" height="22"></canvas></div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>5</td>
                                            <td>
                                                <?php echo lang('medicine_number'); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php
                                                    $this->db->where('hospital_id', $this->hospital_id);
                                                    $query_medicine_number = $this->db->get('medicine')->result();
                                                    $i = 0;
                                                    foreach ($query_medicine_number as $q_medicine_number) {
                                                        $i = $i + 1;
                                                    }
                                                    echo $i;
                                                    ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div id="work-progress3"><canvas class="work-progress3" width="47" height="22"></canvas></div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>6</td>
                                            <td>
                                                <?php echo lang('medicine_quantity'); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php
                                                    $this->db->where('hospital_id', $this->hospital_id);
                                                    $query_medicine = $this->db->get('medicine')->result();
                                                    $i = 0;
                                                    foreach ($query_medicine as $q_medicine) {
                                                        if ($q_medicine->quantity > 0) {
                                                            $i = $i + $q_medicine->quantity;
                                                        }
                                                    }
                                                    echo number_format($i, 2, '.', ',');
                                                    ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div id="work-progress3"><canvas class="work-progress3" width="47" height="22"></canvas></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>7</td>
                                            <td>
                                                <?php echo lang('medicine_o_s'); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning">
                                                    <?php
                                                    $this->db->where('hospital_id', $this->hospital_id);
                                                    $query_medicine = $this->db->get('medicine')->result();
                                                    $i = 0;
                                                    foreach ($query_medicine as $q_medicine) {
                                                        if ($q_medicine->quantity == 0) {
                                                            $i = $i + 1;
                                                        }
                                                    }
                                                    echo $i;
                                                    ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div id="work-progress4"><canvas class="work-progress3" width="47" height="22"></canvas></div>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </section>
                            <!--work progress end-->


                            <div class="card">
                                <div class="card-header"> <?php echo lang('latest_medicines'); ?></div>
                                <table class="table table-striped table-hover table-bordered" id="">
                                    <thead>
                                        <tr>
                                            <th> <?php echo lang('name'); ?></th>
                                            <th> <?php echo lang('category'); ?></th>
                                            <th> <?php echo lang('price'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $i = 0;
                                        foreach ($latest_medicines as $latest_medicine) {
                                            $i = $i + 1;
                                        ?>
                                            <tr class="">
                                                <td><?php echo $latest_medicine->name; ?></td>
                                                <td> <?php echo $latest_medicine->category; ?></td>
                                                <td><?php echo $settings->currency; ?> <?php echo number_format($latest_medicine->s_price, 2, '.', ','); ?></td>
                                            </tr>
                                        <?php
                                            if ($i == 10)
                                                break;
                                        }
                                        ?>
                                    </tbody>
                                </table>

                            </div>



                        </div>

                    </div>


                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>

    <!-- /.content -->
</div>



<!--main content end-->

<script type="text/javascript">
    var per_month_income_expense = "<?php echo lang('per_month_income_expense') ?>";
</script>
<script type="text/javascript">
    var currency = "<?php echo $settings->currency ?>";
</script>
<script type="text/javascript">
    var months_lang = "<?php echo lang('months') ?>";
</script>
<script type="text/javascript">
    var this_year = <?php echo json_encode($this_year['payment_per_month']); ?>;
</script>
<script type="text/javascript">
    var this_year_expenses = <?php echo json_encode($this_year['expense_per_month']); ?>;
</script>
<script src="common/extranal/js/pharmacy/home.js"></script>
