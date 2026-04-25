<?php
defined('BASEPATH') or exit('No direct script access allowed');
$CI = get_instance();
$currency = !empty($settings->currency) ? $settings->currency : '';
$stripe_key = (!empty($gateway) && !empty($gateway->publish)) ? $gateway->publish : '';
?>
<div class="content-wrapper bg-light">
    <section class="content-header py-3 border-bottom bg-white">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-8">
                    <h1 class="h3 mb-1 font-weight-bold">
                        <i class="fas fa-file-invoice-dollar text-primary mr-2"></i>
                        <?php echo lang('subscription'); ?> <?php echo lang('details'); ?>
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0 py-0 small">
                            <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item"><a href="settings"><?php echo lang('settings'); ?></a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo lang('subscription'); ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-bottom d-flex flex-wrap align-items-center justify-content-between py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase">
                                <?php echo lang('my_current_plan'); ?> <span class="text-dark">(<?php echo htmlspecialchars($package->name); ?>)</span>
                            </h3>
                            <a href="settings/packages" class="btn btn-sm btn-success"><?php echo lang('change_plan'); ?></a>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless mb-0">
                                    <tbody class="f-15">
                                        <tr>
                                            <td class="text-muted w-50"><?php echo lang('yearly_price'); ?></td>
                                            <td class="font-weight-bold"><?php echo htmlspecialchars($currency . $package->yearly_price); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><?php echo lang('monthly_price'); ?></td>
                                            <td class="font-weight-bold"><?php echo htmlspecialchars($currency . $package->monthly_price); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><?php echo lang('patient'); ?> <?php echo lang('limit'); ?></td>
                                            <td><?php echo isset($subscription->p_limit) ? htmlspecialchars($subscription->p_limit) : ''; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><?php echo lang('doctor'); ?> <?php echo lang('limit'); ?></td>
                                            <td><?php echo isset($subscription->d_limit) ? htmlspecialchars($subscription->d_limit) : ''; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><?php echo lang('my_plan'); ?></td>
                                            <td>
                                                <?php
                                                if (!empty($hospital_payments->package_duration) && $hospital_payments->package_duration == 'yearly') {
                                                    echo lang('yearly');
                                                } else {
                                                    echo lang('monthly');
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                        if (!empty($hospital_payments->next_due_date_stamp) && !empty($hospital_payments->add_date_stamp)) {
                                            $diff_date = $hospital_payments->next_due_date_stamp - $hospital_payments->add_date_stamp;
                                            $remain_day = $diff_date / (24 * 3600);
                                            if ($remain_day == 15) {
                                        ?>
                                        <tr>
                                            <td class="text-muted"><?php echo lang('version'); ?></td>
                                            <td><?php echo lang('trial'); ?></td>
                                        </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td class="text-muted"><?php echo lang('next_due_date'); ?></td>
                                            <td><?php echo !empty($hospital_payments->next_due_date) ? htmlspecialchars($hospital_payments->next_due_date) : '—'; ?></td>
                                        </tr>
                                        <?php if (!empty($hospital_payments->id)) { ?>
                                        <tr>
                                            <td></td>
                                            <td class="selectPackage_div">
                                                <button type="button" data-payment-id="<?php echo (int) $hospital_payments->id; ?>" data-is-free="0" class="btn btn-success selectPackage" title="<?php echo lang('renew'); ?>">
                                                    <i class="fas fa-sync-alt mr-1"></i><span class="d-none d-sm-inline"><?php echo lang('renew'); ?></span>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('payment'); ?> <?php echo lang('history'); ?></h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 custom_buttons"></div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-striped" id="editable-sample">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo lang('package'); ?></th>
                                            <th><?php echo lang('amount'); ?></th>
                                            <th><?php echo lang('date'); ?></th>
                                            <th><?php echo lang('next_payment_date'); ?></th>
                                            <th><?php echo lang('payment_gateway'); ?></th>
                                            <th><?php echo lang('options'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 0;
                                        if (!empty($deposits)) {
                                            foreach ($deposits as $deposit) {
                                                $package_details = $this->db->get_where('package', array('id' => $deposit->package_id))->row();
                                        ?>
                                        <tr>
                                            <td><?php echo $i + 1; ?></td>
                                            <td><?php echo !empty($package_details) ? htmlspecialchars($package_details->name) : '—'; ?></td>
                                            <td><?php echo htmlspecialchars($deposit->deposited_amount); ?></td>
                                            <td><?php echo htmlspecialchars($deposit->add_date); ?></td>
                                            <td><?php echo htmlspecialchars($deposit->next_due_date); ?></td>
                                            <td><?php echo htmlspecialchars($deposit->gateway); ?></td>
                                            <td>
                                                <a class="btn btn-info btn-sm" href="settings/downloadInvoice?id=<?php echo (int) $deposit->id; ?>" title="<?php echo lang('download'); ?>"><i class="fa fa-download"></i></a>
                                            </td>
                                        </tr>
                                        <?php
                                                $i++;
                                            }
                                        }
                                        ?>
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

<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="depositModalLabel" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="depositModalLabel"><?php echo lang('add_deposit'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form role="form" id="editDepositForm" action="settings/changePlanPayment" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label><?php echo lang('package'); ?> <?php echo lang('name'); ?></label>
                            <input type="text" class="form-control package_name" name="package" value="" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php echo lang('package'); ?> <?php echo lang('price'); ?></label>
                            <input type="text" class="form-control pay_in package_price" name="package_price" value="" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php echo lang('package'); ?> <?php echo lang('type'); ?></label>
                            <input type="text" class="form-control pay_in package_type" name="package_type" value="" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php echo lang('next_due_date'); ?></label>
                            <input type="text" class="form-control pay_in next_due_date" name="next_due_date" value="" readonly>
                        </div>
                    </div>
                    <input type="hidden" name="deposit_type" value="Card">
                    <?php
                    $payment_gateway = !empty($settings1) && !empty($settings1->payment_gateway) ? $settings1->payment_gateway : '';
                    if ($payment_gateway == 'PayPal') {
                    ?>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label><?php echo lang('card'); ?></label>
                            <select class="form-control js-example-basic-single" name="card_type">
                                <option value="Mastercard"><?php echo lang('mastercard'); ?></option>
                                <option value="Visa"><?php echo lang('visa'); ?></option>
                                <option value="American Express"><?php echo lang('american_express'); ?></option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php echo lang('cardholder'); ?> <?php echo lang('name'); ?></label>
                            <input type="text" class="form-control form-control-lg" name="cardholder" value="">
                        </div>
                    </div>
                    <?php } ?>
                    <?php if ($payment_gateway != 'Pay U Money' && $payment_gateway != 'Paystack' && $payment_gateway != '') { ?>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="card"><?php echo lang('card'); ?> <?php echo lang('number'); ?></label>
                            <input type="text" class="form-control form-control-lg" id="card" name="card_number" value="" autocomplete="off">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="expire"><?php echo lang('expire'); ?> <?php echo lang('date'); ?></label>
                            <input type="text" class="form-control form-control-lg" id="expire" data-date="" data-date-format="MM YY" placeholder="MM/YY" name="expire_date" maxlength="7" value="" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="cvv"><?php echo lang('cvv'); ?></label>
                            <input type="text" class="form-control form-control-lg" id="cvv" name="cvv_number" value="" maxlength="4" required autocomplete="off">
                        </div>
                    </div>
                    <?php } ?>
                    <div id="token"></div>
                    <input type="hidden" name="hospital_id" id="hospital_id" value="<?php echo !empty($hospital->id) ? (int) $hospital->id : ''; ?>">
                    <input type="hidden" name="id" id="package_id" value="">
                    <input type="hidden" name="renew" value="renew">
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-primary" id="submit-btn">
                            <?php echo lang('submit'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<?php if ($stripe_key !== '') { ?>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<?php } ?>
<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
    var gateway = <?php echo json_encode($stripe_key); ?>;
    var useStripe = <?php echo (!empty($settings1) && $settings1->payment_gateway == 'Stripe' && $stripe_key !== '') ? 'true' : 'false'; ?>;
</script>
<script src="common/extranal/js/settings/subscription.js"></script>
