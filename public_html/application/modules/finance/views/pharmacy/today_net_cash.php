<!--sidebar end-->
<!--main content start-->
<div class="content-wrapper bg-light">
    <section class="content-header py-4 bg-white shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="display-4 font-weight-black mb-0">
                        <i class="fas fa-wallet text-primary mr-3"></i>
                        <?php echo lang('pharmacy'); ?> <?php echo lang('today_net_cash'); ?>
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item active"><?php echo lang('today_net_cash'); ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <section class="content py-5">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-lg border-0">
                        <div class="card-body bg-light p-4">
                            <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered" id="">
                        <thead>
                            <tr>
                                <th> <?php echo lang('category'); ?> </th>
                                <th> <?php echo lang('amount'); ?> </th>

                            </tr>
                        </thead>
                        <tbody>
                        


                        <tr class="">
                            <td> <?php echo lang('today_sales'); ?> </td>
                            <td>  <?php echo $settings->currency; ?>  <?php
                                if (!empty($today_sales_amount)) {
                                    echo number_format($today_sales_amount, 2, '.', ',');
                                } else {
                                    echo $today_sales_amount = 0;
                                }
                                ?> 
                            </td>

                        </tr>

                        <tr class="">
                            <td> <?php echo lang('today_expense'); ?> </td>
                            <td>  <?php echo $settings->currency; ?>  <?php
                                if (!empty($today_expenses_amount)) {
                                    echo number_format($today_expenses_amount, 2, '.', ',');
                                } else {
                                    echo $today_expenses_amount = 0;
                                }
                                ?> 
                            </td>


                        </tr>

                        <tr class="total">
                            <td> <?php echo lang('today_net_cash'); ?> </td>
                            <td>  <?php echo $settings->currency; ?> <?php echo number_format($today_sales_amount - $today_expenses_amount, 2, '.', ','); ?> </td>

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
<!--main content end-->
<!--footer start-->
<script src="common/js/codearistos.min.js"></script>

<script src="common/extranal/js/finance/today_net_cash.js"></script>
