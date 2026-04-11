<?php
$ap_settings_saas_ui = $this->ion_auth->in_group('superadmin');
touch('common/js/countrypicker.js');
?>
<?php if ($ap_settings_saas_ui) : ?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/settings-saas-styles.css'); ?>">
<?php endif; ?>
<link href="common/extranal/css/hospital/report_subscription.css" rel="stylesheet">

<div class="content-wrapper <?php echo $ap_settings_saas_ui ? 'ap-settings-saas bg-light ap-settings-saas-hospital-subscription' : 'bg-light'; ?>">

    <!-- Page Header -->
    <section class="content-header <?php echo $ap_settings_saas_ui ? 'ap-settings-saas-hero shadow-none border-0 py-4' : ''; ?>">
        <div class="container-fluid">
            <?php if ($ap_settings_saas_ui) : ?>
            <div class="row align-items-center pl-1">
                <div class="col-12 col-lg-9">
                    <span class="ap-settings-saas-badge"><?php echo lang('superadmin'); ?> · SaaS · <?php echo lang('report-h'); ?></span>
                    <h1 class="display-4 font-weight-black mb-0">
                        <i class="fas fa-chart-line mr-2 ap-settings-saas-report-hero-icon ap-settings-saas-icon-gold"></i><?php echo lang('subscription_report'); ?>
                    </h1>
                    <nav aria-label="breadcrumb" class="mt-2">
                        <ol class="breadcrumb bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item"><?php echo lang('report-h'); ?></li>
                            <li class="breadcrumb-item active"><?php echo lang('subscription_report'); ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
            <?php else : ?>
            <div class="row my-2 pl-1">
                <div class="col-sm-6">
                    <h1 class="font-weight-bold"><i class="fas fa-chart-line mr-2"></i><?php echo lang('subscription_report'); ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                        <li class="breadcrumb-item active"><?php echo lang('subscription_report'); ?></li>
                    </ol>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Main content -->
    <section class="content <?php echo $ap_settings_saas_ui ? 'py-4' : ''; ?>">
        <div class="container-fluid">
            <div class="row">

                <!-- ── Filter column ───────────────────────────────────────────── -->
                <div class="col-md-3 <?php echo $ap_settings_saas_ui ? 'mb-4' : ''; ?>">
                    <div class="card <?php echo $ap_settings_saas_ui ? 'ap-settings-saas-systems-report border-0 shadow' : ''; ?>" style="<?php echo $ap_settings_saas_ui ? 'min-height:100%' : ''; ?>">
                        <div class="card-header <?php echo $ap_settings_saas_ui ? 'ap-settings-saas-systems-card-hd' : ''; ?>">
                            <h3 class="card-title mb-0"><?php echo lang('filter_by'); ?></h3>
                        </div>
                        <div class="card-body">
                            <form role="form" class="form_style" action="hospital/reportSubscription" method="post" enctype="multipart/form-data">

                                <div class="form-group">
                                    <label class="<?php echo $ap_settings_saas_ui ? 'ap-sub-label' : ''; ?>"><?php echo lang('date_from'); ?> — <?php echo lang('date_to'); ?></label>
                                    <div class="input-group input-large" data-date="13/07/2013" data-date-format="mm/dd/yyyy">
                                        <input type="text" class="form-control dpd1" name="date_from"
                                               value="<?php echo !empty($from) ? $from : ''; ?>"
                                               placeholder="<?php echo lang('date_from'); ?>" readonly>
                                        <input type="text" class="form-control dpd2" name="date_to"
                                               value="<?php echo !empty($to) ? $to : ''; ?>"
                                               placeholder="<?php echo lang('date_to'); ?>" readonly>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label><?php echo lang('package'); ?></label>
                                    <select class="form-control form-control-lg m-bot15 pos_select" id="package_select" name="package" required>
                                        <option value="all" <?php echo $package_select == 'all' ? 'selected' : ''; ?>><?php echo lang('all'); ?></option>
                                        <?php foreach ($packages as $package) : ?>
                                            <option value="<?php echo $package->id; ?>" <?php echo $package->id == $package_select ? 'selected' : ''; ?>>
                                                <?php echo $package->name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label><?php echo lang('subscription'); ?> <?php echo lang('type'); ?></label>
                                    <select class="form-control form-control-lg m-bot15 pos_select" id="subscription" name="subscription" required>
                                        <option value="all"   <?php echo $subscription == 'all'   ? 'selected' : ''; ?>><?php echo lang('all'); ?></option>
                                        <option value="new"   <?php echo $subscription == 'new'   ? 'selected' : ''; ?>><?php echo lang('new') . ' ' . lang('subscription'); ?></option>
                                        <option value="renew" <?php echo $subscription == 'renew' ? 'selected' : ''; ?>><?php echo lang('renew'); ?></option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label><?php echo lang('country'); ?></label>
                                    <select class="form-control countrypicker selectpicker m-bot15"
                                            name="country" data-flag="true" data-live-search="true"
                                            <?php if (!empty($country_select)) : ?>data-default="<?php echo $country_select; ?>"<?php endif; ?>>
                                    </select>
                                </div>

                                <div class="form-group <?php echo $ap_settings_saas_ui ? 'ap-sub-btn-row' : 'button_div'; ?>">
                                    <button type="submit" name="submit" value="submit"
                                            class="btn btn-success submit_button <?php echo $ap_settings_saas_ui ? 'ap-settings-saas-subscription-btn-submit' : ''; ?>">
                                        <?php echo lang('submit'); ?>
                                    </button>
                                    <button type="submit" name="submit" value="reset"
                                            class="btn btn-danger submit_button <?php echo $ap_settings_saas_ui ? 'ap-settings-saas-subscription-btn-reset' : ''; ?>">
                                        <?php echo lang('reset'); ?>
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <!-- ── Main table column ───────────────────────────────────────── -->
                <div class="col-md-6 <?php echo $ap_settings_saas_ui ? 'mb-4' : ''; ?>">
                    <div class="card <?php echo $ap_settings_saas_ui ? 'ap-settings-saas-systems-report border-0 shadow' : ''; ?>">
                        <div class="card-header <?php echo $ap_settings_saas_ui ? 'ap-settings-saas-systems-card-hd' : ''; ?>">
                            <h3 class="card-title mb-0"><?php echo lang('subscription_report'); ?></h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-bordered table-hover" id="editable-sample1">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo lang('date'); ?></th>
                                        <th><?php echo lang('hospital'); ?> <?php echo lang('name'); ?></th>
                                        <th><?php echo lang('package'); ?></th>
                                        <th><?php echo lang('country'); ?></th>
                                        <th><?php echo lang('payment_gateway'); ?></th>
                                        <th><?php echo lang('subscription'); ?> <?php echo lang('type'); ?></th>
                                        <th><?php echo lang('amount'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 0;
                                    foreach ($deposits as $deposit) {
                                        if ($package_select != 'all' && !empty($package_select)) {
                                            $hospital_payment_details = $this->db->get_where('hospital_payment', ['id' => $deposit->payment_id])->row();
                                            if (!empty($hospital_payment_details) && $package_select == $deposit->package_id) {
                                                $total[] = $deposit->deposited_amount;
                                                $hospital_payment = $this->db->get_where('hospital_payment', ['hospital_user_id' => $deposit->hospital_user_id])->row();
                                                $package_details  = $this->db->get_where('package',  ['id' => $deposit->package_id])->row();
                                                $hospital         = $this->db->get_where('hospital', ['id' => $hospital_payment->hospital_user_id])->row();
                                    ?>
                                                <tr>
                                                    <td><?php echo $i + 1; ?></td>
                                                    <td><?php echo $deposit->add_date; ?></td>
                                                    <td><?php echo $hospital->name ?? '—'; ?></td>
                                                    <td><?php echo $package_details->name ?? '—'; ?></td>
                                                    <td><?php echo $hospital->country ?? '—'; ?></td>
                                                    <td><?php echo $deposit->gateway; ?></td>
                                                    <td><?php
                                                        if ($deposit->deposited_amount_id) {
                                                            $renew[] = $deposit->deposited_amount;
                                                            echo lang('renew');
                                                        } else {
                                                            $subscription_amount[] = $deposit->deposited_amount;
                                                            echo lang('new') . ' ' . lang('subscription');
                                                        }
                                                    ?></td>
                                                    <td><?php echo $settings->currency . ' ' . $deposit->deposited_amount; ?></td>
                                                </tr>
                                    <?php
                                                $i++;
                                            }
                                        } else {
                                            $total[] = $deposit->deposited_amount;
                                            $hospital_payment1 = $this->db->get_where('hospital_payment', ['hospital_user_id' => $deposit->hospital_user_id])->row();
                                            if (!empty($hospital_payment1)) {
                                                $hospital_payment = $hospital_payment1;
                                                $hospital         = $this->db->get_where('hospital', ['id' => $hospital_payment->hospital_user_id])->row();
                                                $package_details  = $this->db->get_where('package',  ['id' => $deposit->package_id])->row();
                                    ?>
                                                <tr>
                                                    <td><?php echo $i + 1; ?></td>
                                                    <td><?php echo $deposit->add_date; ?></td>
                                                    <td><?php echo $hospital->name ?? '—'; ?></td>
                                                    <td><?php echo $package_details->name ?? '—'; ?></td>
                                                    <td><?php echo !empty($hospital) ? $hospital->country : '—'; ?></td>
                                                    <td><?php echo $deposit->gateway; ?></td>
                                                    <td><?php
                                                        if ($deposit->deposited_amount_id) {
                                                            $renew[] = $deposit->deposited_amount;
                                                            echo lang('renew');
                                                        } else {
                                                            $subscription_amount[] = $deposit->deposited_amount;
                                                            echo lang('new') . ' ' . lang('subscription');
                                                        }
                                                    ?></td>
                                                    <td><?php echo $settings->currency . ' ' . $deposit->deposited_amount; ?></td>
                                                </tr>
                                    <?php
                                            }
                                            $i++;
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ── Summary / totals column ────────────────────────────────── -->
                <div class="col-md-3 <?php echo $ap_settings_saas_ui ? 'mb-4' : ''; ?>">

                    <?php if ($ap_settings_saas_ui) : ?>

                    <div class="card ap-settings-saas-systems-report border-0 shadow">
                        <div class="card-header ap-settings-saas-systems-card-hd">
                            <h3 class="card-title mb-0"><?php echo lang('summary'); ?></h3>
                        </div>
                        <div class="card-body p-3">

                            <!-- New subscriptions -->
                            <div class="ap-sub-stat-card mb-3">
                                <div class="ap-sub-stat-label">
                                    <i class="fas fa-plus-circle mr-1"></i><?php echo lang('new'); ?> <?php echo lang('subscription'); ?>
                                </div>
                                <div class="ap-sub-stat-value">
                                    <?php echo $settings->currency; ?>
                                    <?php echo !empty($subscription_amount) ? number_format(array_sum($subscription_amount), 2) : '0.00'; ?>
                                </div>
                            </div>

                            <!-- Renewals -->
                            <div class="ap-sub-stat-card mb-3">
                                <div class="ap-sub-stat-label">
                                    <i class="fas fa-sync-alt mr-1"></i><?php echo lang('renew'); ?>
                                </div>
                                <div class="ap-sub-stat-value">
                                    <?php echo $settings->currency; ?>
                                    <?php echo !empty($renew) ? number_format(array_sum($renew), 2) : '0.00'; ?>
                                </div>
                            </div>

                            <!-- Total -->
                            <div class="ap-sub-stat-card ap-sub-stat-card--total">
                                <div class="ap-sub-stat-label">
                                    <i class="fas fa-coins mr-1"></i><?php echo lang('total'); ?> <?php echo lang('amount'); ?>
                                </div>
                                <div class="ap-sub-stat-value ap-sub-stat-value--total">
                                    <?php echo $settings->currency; ?>
                                    <?php echo !empty($total) ? number_format(array_sum($total), 2) : '0.00'; ?>
                                </div>
                            </div>

                        </div>
                    </div>

                    <?php else : ?>

                    <div class="section_middle">
                        <section class="card">
                            <div class="weather-bg section_middle_child">
                                <div class="card-body section_middle_child_child">
                                    <div class="row">
                                        <div class="col-xs-4"><?php echo lang('new'); ?> <?php echo lang('subscription'); ?></div>
                                        <div class="col-xs-8">
                                            <div class="degree">
                                                <?php echo $settings->currency; ?>
                                                <?php echo !empty($subscription_amount) ? number_format(array_sum($subscription_amount), 2) : '0'; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <section class="card">
                            <div class="weather-bg section_middle_child">
                                <div class="card-body section_middle_child_child">
                                    <div class="row">
                                        <div class="col-xs-4"><?php echo lang('renew'); ?></div>
                                        <div class="col-xs-8">
                                            <div class="degree">
                                                <?php echo $settings->currency; ?>
                                                <?php echo !empty($renew) ? number_format(array_sum($renew), 2) : '0'; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <section class="card">
                            <div class="weather-bg section_middle_child">
                                <div class="card-body section_middle_child_child">
                                    <div class="row">
                                        <div class="col-xs-4"><?php echo lang('total'); ?> <?php echo lang('amount'); ?></div>
                                        <div class="col-xs-8">
                                            <div class="degree">
                                                <?php echo $settings->currency; ?>
                                                <?php echo !empty($total) ? number_format(array_sum($total), 2) : '0'; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <?php endif; ?>

                </div>
                <!-- /.col -->

            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>

</div><!-- /.content-wrapper -->


<script src="common/js/codearistos.min.js"></script>
<script src="common/extranal/js/hospital/report_subscription.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.dpd1, .dpd2').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayBtn: true,
            showMeridian: true
        });
    });
</script>
