<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
if (!isset($gateway) || !is_object($gateway)) {
    $gateway = null;
}
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('appointment') . ' ' . lang('requests'),
        'icon' => 'fas fa-calendar-plus text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('appointment'), 'url' => 'appointment'),
            array('label' => lang('request'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('Appoitments requested from patients'); ?></h3>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal">
                                <i class="fas fa-plus mr-1"></i> <?php echo lang('add_appointment'); ?>
                            </button>
                        </div>

                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive appointment-table-wrap">
                            <table class="table table-hover table-bordered align-middle datatables mb-0" id="editable-sample6" style="width:100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th><?php echo lang('id'); ?></th>
                                        <th><?php echo lang('patient'); ?></th>
                                        <th><?php echo lang('doctor'); ?></th>
                                        <th><?php echo lang('date-time'); ?></th>
                                        <th><?php echo lang('status'); ?></th>
                                        <th class="no-print"><?php echo lang('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>

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




<!-- Add Appointment Modal-->
<div class="modal fade modal-enhanced appointment-add-modal" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class=" modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-calendar-plus mr-2"></i>
                    <?php echo lang('add_appointment'); ?>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body row">
                <form role="form" action="appointment/addNew" id="addAppointmentForm" method="post" class="clearfix" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1"> <?php echo lang('patient'); ?>&#42;</label>
                            <select class="form-control form-control-lg m-bot15 pos_select" id="pos_select" name="patient" value=''>


                            </select>
                        </div>
                        <input type="hidden" name="redirectlink" value="request">
                        <div class="pos_client clearfix col-md-6">
                            <div class="form-group payment pad_bot float-right patient_div">
                                <label for="exampleInputEmail1"> <?php echo lang('patient'); ?> <?php echo lang('name'); ?></label>
                                <input type="text" class="form-control pay_in" name="p_name" value='' placeholder="">
                            </div>
                            <div class="form-group payment pad_bot float-right">
                                <label for="exampleInputEmail1"> <?php echo lang('patient'); ?> <?php echo lang('email'); ?></label>
                                <input type="text" class="form-control pay_in" name="p_email" value='' placeholder="">
                            </div>
                            <div class="form-group payment pad_bot float-right">
                                <label for="exampleInputEmail1"> <?php echo lang('patient'); ?> <?php echo lang('phone'); ?></label>
                                <input type="text" class="form-control pay_in" name="p_phone" value='' placeholder="">
                            </div>
                            <div class="form-group payment pad_bot float-right">
                                <label for="exampleInputEmail1"> <?php echo lang('patient'); ?> <?php echo lang('age'); ?></label>
                                <input type="text" class="form-control pay_in" name="p_age" value='' placeholder="">
                            </div>
                            <div class="form-group payment pad_bot">
                                <label for="exampleInputEmail1"> <?php echo lang('patient'); ?> <?php echo lang('gender'); ?></label>
                                <select class="form-control form-control-lg" name="p_gender" value=''>

                                    <option value="Male" <?php
                                                            if (!empty($patient->sex)) {
                                                                if ($patient->sex == 'Male') {
                                                                    echo 'selected';
                                                                }
                                                            }
                                                            ?>> <?php echo lang('male'); ?> </option>
                                    <option value="Female" <?php
                                                            if (!empty($patient->sex)) {
                                                                if ($patient->sex == 'Female') {
                                                                    echo 'selected';
                                                                }
                                                            }
                                                            ?>> <?php echo lang('female'); ?> </option>

                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6 doctor_div">
                            <label for="exampleInputEmail1"> <?php echo lang('doctor'); ?>&#42;</label>
                            <select class="form-control form-control-lg m-bot15" id="adoctors" name="doctor" value='' required>

                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1"> <?php echo lang('date'); ?>&#42;</label>
                            <input type="text" class="form-control form-control-lg default-date-picker" id="date" required="" onkeypress="return false;" name="date" id="exampleInputEmail1" value='' placeholder="" autocomplete="off">
                        </div>
                        <div class="form-group col-md-6 aslots">
                            <label for="exampleInputEmail1"> <?php echo lang('available_slots'); ?></label>
                            <select class="form-control form-control-lg m-bot15" name="time_slot" id="aslots" value=''>

                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1"> <?php echo lang('appointment'); ?> <?php echo lang('status'); ?></label>
                            <select class="form-control form-control-lg m-bot15" name="status" value=''>
                                <option value="Pending Confirmation" <?php ?>> <?php echo lang('pending_confirmation'); ?> </option>
                                <option value="Confirmed" <?php
                                                            ?>> <?php echo lang('confirmed'); ?> </option>
                                <option value="Treated" <?php
                                                        ?>> <?php echo lang('treated'); ?> </option>
                                <option value="Cancelled" <?php
                                                            ?>> <?php echo lang('cancelled'); ?> </option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1"> <?php echo lang('remarks'); ?></label>
                            <input type="text" class="form-control form-control-lg" name="remarks" id="exampleInputEmail1" value='' placeholder="">
                        </div>
                        <div class="form-group col-md-12">

                            <label class=""><?php echo lang('visit'); ?> <?php echo lang('description'); ?>&#42;</label>

                            <select class="form-control form-control-lg m-bot15" name="visit_description" id="visit_description" value='' required>

                            </select>

                        </div>
                        <div class="form-group col-md-4 form_data">
                            <label for="exampleInputEmail1"> <?php echo lang('visit'); ?> <?php echo lang('charges'); ?></label>
                            <input type="number" class="form-control form-control-lg" name="visit_charges" id="visit_charges" value='' placeholder="" readonly="">
                        </div>
                        <div class="form-group col-md-4 form_data">
                            <label for="exampleInputEmail1"> <?php echo lang('discount'); ?></label>
                            <input type="number" class="form-control form-control-lg" name="discount" id="discount" value='0' placeholder="">
                        </div>
                        <div class="form-group col-md-4 form_data">
                            <label for="exampleInputEmail1"> <?php echo lang('grand_total'); ?></label>
                            <input type="number" class="form-control form-control-lg" name="grand_total" id="grand_total" value='0' placeholder="" readonly="">
                        </div>
                        <?php if (!$this->ion_auth->in_group(array('Nurse', 'Doctor'))) { ?>
                            <div class="form-group col-md-12">
                                <input type="checkbox" id="pay_now_appointment" name="pay_now_appointment" value="pay_now_appointment">
                                <label for=""> <?php echo lang('pay_now'); ?></label><br>
                                <?php if (!$this->ion_auth->in_group(array('Patient'))) { ?>
                                    <span class="info_message"><?php echo lang('if_pay_now_checked_please_select_status_to_confirmed') ?></span>
                                <?php } ?>
                            </div>

                            <div class="form-group payment_label col-md-12 d-none deposit_type">
                                <label for="exampleInputEmail1"> <?php echo lang('deposit_type'); ?></label>

                                <div class="">
                                    <select class="form-control form-control-lg m-bot15 js-example-basic-single selecttype" id="selecttype" name="deposit_type" value=''>
                                        <?php if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist'))) { ?>
                                            <option value="Cash"> <?php echo lang('cash'); ?> </option>
                                            <option value="Card"> <?php echo lang('card'); ?> </option>
                                        <?php } ?>

                                    </select>
                                </div>

                            </div>
                            <div class="form-group col-md-12">
                                <?php
                                $payment_gateway = $settings->payment_gateway;
                                ?>



                                <div class="cardPayment">

                                    <hr>
                                    <?php if ($payment_gateway != 'Paymob') { ?>
                                        <div class="col-md-12 payment pad_bot">
                                            <label for="exampleInputEmail1"> <?php echo lang('accepted'); ?> <?php echo lang('cards'); ?></label>
                                            <div class="payment pad_bot">
                                                <img src="uploads/card.png" width="100%">
                                            </div>
                                        </div>
                                    <?php }
                                    ?>

                                    <?php
                                    if ($payment_gateway == 'PayPal') {
                                    ?>
                                        <div class="form-group col-md-12 payment pad_bot">
                                            <label for="exampleInputEmail1"> <?php echo lang('card'); ?> <?php echo lang('type'); ?></label>
                                            <select class="form-control form-control-lg m-bot15" name="card_type" value=''>

                                                <option value="Mastercard"> <?php echo lang('mastercard'); ?> </option>
                                                <option value="Visa"> <?php echo lang('visa'); ?> </option>
                                                <option value="American Express"> <?php echo lang('american_express'); ?> </option>
                                            </select>
                                        </div>
                                    <?php } ?>
                                    <?php if ($payment_gateway == '2Checkout' || $payment_gateway == 'PayPal') {
                                    ?>
                                        <div class="form-group col-md-12 payment pad_bot">
                                            <label for="exampleInputEmail1"> <?php echo lang('cardholder'); ?> <?php echo lang('name'); ?></label>
                                            <input type="text" id="cardholder" class="form-control pay_in" name="cardholder" value='' placeholder="">
                                        </div>
                                    <?php } ?>
                                    <?php if ($payment_gateway != 'Pay U Money' && $payment_gateway != 'Paystack' && $payment_gateway != 'SSLCOMMERZ' && $payment_gateway != 'Paytm') { ?>
                                        <div class="form-group col-md-12 payment pad_bot">
                                            <label for="exampleInputEmail1"> <?php echo lang('card'); ?> <?php echo lang('number'); ?></label>
                                            <input type="text" id="card" class="form-control pay_in" name="card_number" value='' placeholder="">
                                        </div>



                                        <div class="form-group col-md-8 payment pad_bot">
                                            <label for="exampleInputEmail1"> <?php echo lang('expire'); ?> <?php echo lang('date'); ?></label>
                                            <input type="text" class="form-control pay_in" id="expire" data-date="" data-date-format="MM YY" placeholder="Expiry (MM/YY)" name="expire_date" maxlength="7" aria-describedby="basic-addon1" value='' placeholder="">
                                        </div>
                                        <div class="form-group col-md-4 payment pad_bot">
                                            <label for="exampleInputEmail1"> <?php echo lang('cvv'); ?> </label>
                                            <input type="text" class="form-control pay_in" id="cvv" maxlength="3" name="cvv" value='' placeholder="">
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>


                            </div>
                            <div class="form-group col-md-12">
                                <div class="form-group col-md-3 payment_label">
                                </div>
                                <div class="form-group col-md-9">
                                    <?php $twocheckout = $this->db->get_where('paymentGateway', array('name =' => '2Checkout'))->row(); ?>
                                    <div class="form-group cashsubmit payment  right-six col-md-12">
                                        <button type="submit" name="submit2" id="submit1" class="btn btn-info row float-right"> <?php echo lang('submit'); ?></button>
                                    </div>
                                    <?php $twocheckout = $this->db->get_where('paymentGateway', array('name =' => '2Checkout'))->row(); ?>
                                    <div class="form-group cardsubmit  right-six col-md-12 d-none">
                                        <button type="submit" name="pay_now" id="submit-btn" class="btn btn-info row float-right" <?php if ($settings->payment_gateway == 'Stripe') {
                                                                                                                                    ?>onClick="stripePay(event);" <?php }
                                                                                                                                                                    ?> <?php if ($settings->payment_gateway == '2Checkout' && $twocheckout->status == 'live') {
                                                                                                                                                                        ?>onClick="twoCheckoutPay(event);" <?php }
                                                                                                                                                                                                            ?>> <?php echo lang('submit'); ?></button>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="form-group  payment  right-six col-md-12">
                                <button type="submit" name="submit2" id="submit1" class="btn btn-info row float-right"> <?php echo lang('submit'); ?></button>
                            </div>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div>
</div><!-- /.modal-dialog -->

<!-- Add Appointment Modal-->

<div class="modal fade" role="dialog" id="cmodal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl med_his modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-medical mr-2"></i>
                    <?php echo lang('medical_history'); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="medical_history" class="row">
                    <div class="col-md-12"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> <?php echo lang('close'); ?>
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Edit Event Modal-->
<div class="modal fade modal-enhanced appointment-add-modal" id="myModal2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class=" modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold"> <?php echo lang('edit_appointment'); ?></h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body row">
                <form role="form" id="editAppointmentForm" action="appointment/addNew" class="clearfix" method="post" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1"> <?php echo lang('patient'); ?>&#42;</label>
                            <select class="form-control form-control-lg m-bot15  pos_select1 patient" id="pos_select1" name="patient" value='' required>

                            </select>
                        </div>
                        <div class="pos_client1 clearfix col-md-6">
                            <div class="form-group payment pad_bot float-right">
                                <label for="exampleInputEmail1"> <?php echo lang('patient'); ?> <?php echo lang('name'); ?></label>
                                <input type="text" class="form-control pay_in" name="p_name" value='' placeholder="">
                            </div>
                            <div class="form-group payment pad_bot float-right">
                                <label for="exampleInputEmail1"> <?php echo lang('patient'); ?> <?php echo lang('email'); ?></label>
                                <input type="text" class="form-control pay_in" name="p_email" value='' placeholder="">
                            </div>
                            <div class="form-group payment pad_bot float-right">
                                <label for="exampleInputEmail1"> <?php echo lang('patient'); ?> <?php echo lang('phone'); ?></label>
                                <input type="text" class="form-control pay_in" name="p_phone" value='' placeholder="">
                            </div>
                            <div class="form-group payment pad_bot float-right">
                                <label for="exampleInputEmail1"> <?php echo lang('patient'); ?> <?php echo lang('age'); ?></label>
                                <input type="text" class="form-control pay_in" name="p_age" value='' placeholder="">
                            </div>
                            <div class="form-group payment pad_bot">
                                <label for="exampleInputEmail1"> <?php echo lang('patient'); ?> <?php echo lang('gender'); ?></label>
                                <select class="form-control form-control-lg" name="p_gender" value=''>

                                    <option value="Male" <?php
                                                            if (!empty($patient->sex)) {
                                                                if ($patient->sex == 'Male') {
                                                                    echo 'selected';
                                                                }
                                                            }
                                                            ?>> <?php echo lang('male'); ?> </option>
                                    <option value="Female" <?php
                                                            if (!empty($patient->sex)) {
                                                                if ($patient->sex == 'Female') {
                                                                    echo 'selected';
                                                                }
                                                            }
                                                            ?>> <?php echo lang('female'); ?> </option>

                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6 doctor_div1">
                            <label for="exampleInputEmail1"> <?php echo lang('doctor'); ?>&#42;</label>
                            <select class="form-control form-control-lg m-bot15 doctor" id="adoctors1" name="doctor" value='' required>

                            </select>
                        </div>
                        <input type="hidden" name="redirectlink" value="request">
                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1"> <?php echo lang('date'); ?>&#42;</label>
                            <input type="text" class="form-control form-control-lg default-date-picker" id="date1" required="" onkeypress="return false;" name="date" id="exampleInputEmail1" value='' placeholder="" autocomplete="off">
                        </div>
                        <div class="form-group col-md-6 aslots">
                            <label for="exampleInputEmail1"> <?php echo lang('available_slots'); ?></label>
                            <select class="form-control form-control-lg m-bot15" name="time_slot" id="aslots1" value=''>

                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1"> <?php echo lang('appointment'); ?> <?php echo lang('status'); ?></label>
                            <select class="form-control form-control-lg m-bot15" name="status" value=''>
                                <option value="Pending Confirmation" <?php ?>> <?php echo lang('pending_confirmation'); ?> </option>
                                <option value="Confirmed" <?php ?>> <?php echo lang('confirmed'); ?> </option>
                                <option value="Treated" <?php ?>> <?php echo lang('treated'); ?> </option>
                                <option value="Cancelled" <?php ?>> <?php echo lang('cancelled'); ?> </option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1"> <?php echo lang('remarks'); ?></label>
                            <input type="text" class="form-control form-control-lg" name="remarks" id="exampleInputEmail1" value='' placeholder="">
                        </div>
                        <div class="form-group col-md-12">

                            <label class=""><?php echo lang('visit'); ?> <?php echo lang('description'); ?>&#42;</label>

                            <select class="form-control form-control-lg m-bot15" name="visit_description" id="visit_description1" value='' required>

                            </select>

                        </div>

                        <input type="hidden" name="id" id="appointment_id" value=''>
                        <div class="form-group col-md-4 d-none consultant_fee_div">
                            <label for="exampleInputEmail1"> <?php echo lang('visit'); ?> <?php echo lang('charges'); ?></label>
                            <input type="number" class="form-control form-control-lg" name="visit_charges" id="visit_charges1" value='' placeholder="" readonly="">
                        </div>
                        <div class="form-group col-md-4 d-none consultant_fee_div">
                            <label for="exampleInputEmail1"> <?php echo lang('discount'); ?></label>
                            <input type="number" class="form-control form-control-lg" name="discount" id="discount1" value='0' placeholder="">
                        </div>
                        <div class="form-group col-md-4 d-none consultant_fee_div">
                            <label for="exampleInputEmail1"> <?php echo lang('grand_total'); ?></label>
                            <input type="number" class="form-control form-control-lg" name="grand_total" id="grand_total1" value='0' placeholder="" readonly="">
                        </div>
                        <?php if (!$this->ion_auth->in_group(array('Nurse', 'Doctor'))) { ?>
                            <div class="col-md-12 d-none pay_now">
                                <input type="checkbox" id="pay_now_appointment1" name="pay_now_appointment" value="pay_now_appointment">
                                <label for=""> <?php echo lang('pay_now'); ?></label><br>
                                <span class="info_message"><?php echo lang('if_pay_now_checked_please_select_status_to_confirmed') ?></span>
                            </div>
                            <div class="col-md-12 d-none payment_status form-group">
                                <label for=""> <?php echo lang('payment'); ?> <?php echo lang('status'); ?></label><br>
                                <input type="text" class="form-control form-control-lg" id="pay_now_appointment" name="payment_status_appointment" value="paid" readonly="">


                            </div>
                            <div class="form-group payment_label col-md-12 d-none deposit_type1">
                                <label for="exampleInputEmail1"> <?php echo lang('deposit_type'); ?></label>

                                <div class="">
                                    <select class="form-control form-control-lg m-bot15 js-example-basic-single selecttype1" id="selecttype1" name="deposit_type" value=''>
                                        <?php if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist'))) { ?>
                                            <option value="Cash"> <?php echo lang('cash'); ?> </option>
                                            <option value="Card"> <?php echo lang('card'); ?> </option>
                                        <?php } ?>

                                    </select>
                                </div>

                            </div>
                            <div class="form-group col-md-12">
                                <?php
                                $payment_gateway = $settings->payment_gateway;
                                ?>



                                <div class="card1">

                                    <hr>
                                    <?php if ($payment_gateway != 'Paymob') { ?>
                                        <div class="col-md-12 payment pad_bot">
                                            <label for="exampleInputEmail1"> <?php echo lang('accepted'); ?> <?php echo lang('cards'); ?></label>
                                            <div class="payment pad_bot">
                                                <img src="uploads/card.png" width="100%">
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <?php
                                    if ($payment_gateway == 'PayPal') {
                                    ?>
                                        <div class="form-group col-md-12 payment pad_bot">
                                            <label for="exampleInputEmail1"> <?php echo lang('card'); ?> <?php echo lang('type'); ?></label>
                                            <select class="form-control form-control-lg m-bot15" name="card_type" value=''>

                                                <option value="Mastercard"> <?php echo lang('mastercard'); ?> </option>
                                                <option value="Visa"> <?php echo lang('visa'); ?> </option>
                                                <option value="American Express"> <?php echo lang('american_express'); ?> </option>
                                            </select>
                                        </div>
                                    <?php } ?>
                                    <?php if ($payment_gateway == '2Checkout' || $payment_gateway == 'PayPal') {
                                    ?>
                                        <div class="form-group col-md-12 payment pad_bot">
                                            <label for="exampleInputEmail1"> <?php echo lang('cardholder'); ?> <?php echo lang('name'); ?></label>
                                            <input type="text" id="cardholder1" class="form-control pay_in" name="cardholder" value='' placeholder="">
                                        </div>
                                    <?php } ?>
                                    <?php if ($payment_gateway != 'Pay U Money' && $payment_gateway != 'Paystack' && $payment_gateway != 'SSLCOMMERZ' && $payment_gateway != 'Paytm') { ?>
                                        <div class="form-group col-md-12 payment pad_bot">
                                            <label for="exampleInputEmail1"> <?php echo lang('card'); ?> <?php echo lang('number'); ?></label>
                                            <input type="text" id="card1" class="form-control pay_in" name="card_number" value='' placeholder="">
                                        </div>



                                        <div class="form-group col-md-8 payment pad_bot">
                                            <label for="exampleInputEmail1"> <?php echo lang('expire'); ?> <?php echo lang('date'); ?></label>
                                            <input type="text" class="form-control pay_in" id="expire1" data-date="" data-date-format="MM YY" placeholder="Expiry (MM/YY)" name="expire_date" maxlength="7" aria-describedby="basic-addon1" value='' placeholder="">
                                        </div>
                                        <div class="form-group col-md-4 payment pad_bot">
                                            <label for="exampleInputEmail1"> <?php echo lang('cvv'); ?> </label>
                                            <input type="text" class="form-control pay_in" id="cvv1" maxlength="3" name="cvv" value='' placeholder="">
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>


                            </div>
                            <div class="form-group col-md-12">
                                <div class="form-group col-md-3 payment_label">
                                </div>
                                <div class="form-group col-md-9">
                                    <?php $twocheckout = $this->db->get_where('paymentGateway', array('name =' => '2Checkout'))->row(); ?>
                                    <div class="form-group cashsubmit1 payment  right-six col-md-12">
                                        <button type="submit" name="submit2" id="submit1" class="btn btn-info row float-right"> <?php echo lang('submit'); ?></button>
                                    </div>
                                    <?php $twocheckout = $this->db->get_where('paymentGateway', array('name =' => '2Checkout'))->row(); ?>
                                    <div class="form-group cardsubmit1  right-six col-md-12 d-none">
                                        <button type="submit" name="pay_now" id="submit-btn1" class="btn btn-info row float-right" <?php if ($settings->payment_gateway == 'Stripe') {
                                                                                                                                    ?>onClick="stripePay1(event);" <?php }
                                                                                                                                                                    ?> <?php if ($settings->payment_gateway == '2Checkout' && $twocheckout->status == 'live') {
                                                                                                                                                                        ?>onClick="twoCheckoutPay1(event);" <?php }
                                                                                                                                                                                                            ?>> <?php echo lang('submit'); ?></button>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="form-group  payment  right-six col-md-12">
                                <button type="submit" name="submit2" id="submit1" class="btn btn-info row float-right"> <?php echo lang('submit'); ?></button>
                            </div>
                        <?php } ?>
                    </div>
                </form>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- Edit Event Modal-->

<!-- View Appointment Details Modal -->
<div class="modal fade modal-enhanced" id="viewAppointmentModal" role="dialog" aria-labelledby="viewAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye mr-2"></i>
                    <?php echo lang('appointment'); ?> <?php echo lang('details'); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="appointmentDetails"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> <?php echo lang('close'); ?>
                </button>
                <button type="button" class="btn btn-primary" id="printAppointmentBtn">
                    <i class="fas fa-print mr-1"></i> <?php echo lang('print'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<?php $gateway_stripe = ($gateway && !empty($gateway->publish)) ? $gateway->publish : ''; ?>

<script src="common/js/codearistos.min.js"></script>
<script src="common/js/moment.min.js"></script>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">
    var publish = <?php echo json_encode($gateway_stripe); ?>;
</script>
<script type="text/javascript">
    var payment_gateway = <?php echo json_encode($settings->payment_gateway); ?>;
</script>
<script type="text/javascript">
    var select_doctor = <?php echo json_encode(lang('select_doctor')); ?>;
</script>
<script type="text/javascript">
    var select_patient = <?php echo json_encode(lang('select_patient')); ?>;
</script>
<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
</script>
<script type="text/javascript">
    var no_available_timeslots = <?php echo json_encode(lang('no_available_timeslots')); ?>;
</script>

<script type="text/javascript">
    function viewAppointment(appointmentId) {
        $('#appointmentDetails').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> <?php echo addslashes(lang('loading')); ?>…</div>');
        $.ajax({
            url: 'appointment/getAppointmentDetails',
            type: 'POST',
            data: {
                appointment_id: appointmentId,
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    displayAppointmentDetails(response.data);
                } else {
                    $('#appointmentDetails').html('<div class="alert alert-danger"><?php echo addslashes(lang('error')); ?>: ' + (response.message || '') + '</div>');
                }
            },
            error: function() {
                $('#appointmentDetails').html('<div class="alert alert-danger"><?php echo addslashes(lang('error')); ?>.</div>');
            }
        });
        $('#viewAppointmentModal').modal('show');
    }
    function displayAppointmentDetails(data) {
        var statusClass = 'status-' + data.status.toLowerCase().replace(' ', '-');
        var html = '<div class="row">' +
            '<div class="col-md-6">' +
            '<div class="appointment-detail-row"><div class="detail-label"><?php echo addslashes(lang('id')); ?>:</div><div class="detail-value">' + data.id + '</div></div>' +
            '<div class="appointment-detail-row"><div class="detail-label"><?php echo addslashes(lang('patient')); ?>:</div><div class="detail-value">' + (data.patient_name || '') + '</div></div>' +
            '<div class="appointment-detail-row"><div class="detail-label"><?php echo addslashes(lang('doctor')); ?>:</div><div class="detail-value">' + (data.doctor_name || '') + '</div></div>' +
            '<div class="appointment-detail-row"><div class="detail-label"><?php echo addslashes(lang('date-time')); ?>:</div><div class="detail-value">' + (data.date_time || '') + '</div></div>' +
            '</div><div class="col-md-6">' +
            '<div class="appointment-detail-row"><div class="detail-label"><?php echo addslashes(lang('status')); ?>:</div><div class="detail-value"><span class="status-badge ' + statusClass + '">' + (data.status || '') + '</span></div></div>' +
            '<div class="appointment-detail-row"><div class="detail-label"><?php echo addslashes(lang('remarks')); ?>:</div><div class="detail-value">' + (data.remarks || 'N/A') + '</div></div>' +
            '<div class="appointment-detail-row"><div class="detail-label"><?php echo addslashes(lang('description')); ?>:</div><div class="detail-value">' + (data.description || 'N/A') + '</div></div>' +
            '<div class="appointment-detail-row"><div class="detail-label"><?php echo addslashes(lang('amount')); ?>:</div><div class="detail-value">' + (data.amount || 'N/A') + '</div></div>' +
            '</div></div>';
        if (data.invoice_id) {
            html += '<div class="row"><div class="col-md-12"><div class="appointment-detail-row"><div class="detail-label"><?php echo addslashes(lang('invoice_id')); ?>:</div><div class="detail-value">' + data.invoice_id + '</div></div></div></div>';
        }
        $('#appointmentDetails').html(html);
    }
    $(function () {
        $('#printAppointmentBtn').on('click', function() {
            var printContent = $('#appointmentDetails').html();
            var printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title><?php echo addslashes(lang('appointment')); ?></title><style>body{font-family:Arial,sans-serif;margin:20px}.appointment-detail-row{margin-bottom:15px;padding:10px;border-bottom:1px solid #eee}.detail-label{font-weight:bold;color:#333}.detail-value{color:#666}.status-badge{padding:4px 8px;border-radius:4px;font-size:12px;font-weight:bold}</style></head><body><h2><?php echo addslashes(lang('appointment')); ?></h2>' + printContent + '</body></html>');
            printWindow.document.close();
            printWindow.print();
        });
    });
</script>
