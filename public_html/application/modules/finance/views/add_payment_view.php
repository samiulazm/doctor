<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<link href="common/css/bootstrap-reset.css" rel="stylesheet">
<link href="common/extranal/css/finance/add_payment_view.css" rel="stylesheet">

<div class="content-wrapper add-payment-page">

    <!-- ── Header ── -->
    <section class="pay-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col">
                    <h1>
                        <span class="header-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                        <?php
                        if (!empty($payment) && !empty($payment->id)) {
                            echo lang('edit_invoice') . ' #' . $payment->id;
                        } elseif (!empty($draft) && !empty($draft->id)) {
                            echo lang('edit_draft_invoice');
                        } else {
                            echo lang('add_new_invoice');
                        }
                        ?>
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item"><a href="finance/payment"><?php echo lang('all') ?> <?php echo lang('invoices') ?></a></li>
                            <li class="breadcrumb-item active">
                                <?php
                                if (!empty($payment) && !empty($payment->id)) {
                                    echo lang('edit_invoice') . ' #' . $payment->id;
                                } elseif (!empty($draft) && !empty($draft->id)) {
                                    echo lang('edit_draft_invoice');
                                } else {
                                    echo lang('add_new_invoice');
                                }
                                ?>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Content ── -->
    <section class="content py-4">
        <div class="container-fluid px-2 px-md-3">
            <form role="form" id="editPaymentForm" class="add-payment-form row g-3 g-lg-4 w-100 mx-0" action="finance/addPayment" method="post" enctype="multipart/form-data">
                <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">

                <!-- ═══════════════════════════════════════════
                     COLUMN 1 — Patient · Doctor · Items
                     ═══════════════════════════════════════════ -->
                <div class="col-12 col-lg-5 col-xl-4">
                    <div class="pay-card">
                        <div class="pay-card-header">
                            <span class="card-step step-1">1</span>
                            <h2 class="card-title"><?php echo lang('patient'); ?> &amp; <?php echo lang('item'); ?></h2>
                        </div>
                        <div class="pay-card-body">

                            <!-- Patient Selection -->
                            <div class="mb-4">
                                <label class="field-label"><?php echo lang('patient'); ?><span class="text-danger">*</span></label>
                                <select class="form-control pos_select" id="pos_select" name="patient" value='' required="">
                                    <?php if (!empty($payment)) {
                                        if (empty($patients->age)) {
                                            $dateOfBirth = $patients->birthdate;
                                            if (empty($dateOfBirth)) {
                                                $age[0] = '0';
                                            } else {
                                                $today = date("Y-m-d");
                                                $diff = date_diff(date_create($dateOfBirth), date_create($today));
                                                $age[0] = $diff->format('%y');
                                            }
                                        } else {
                                            $age = explode('-', $patients->age);
                                        }
                                    ?>
                                        <option value="<?php echo $patients->id; ?>" selected="selected">
                                            <?php echo $patients->name; ?> ( <?php echo lang('id'); ?>:
                                            <?php echo $patients->id; ?> - <?php echo lang('phone'); ?>:
                                            <?php echo $patients->phone; ?> - <?php echo lang('age'); ?>:
                                            <?php echo $age[0]; ?> ) </option>
                                    <?php } elseif (!empty($draft) && !empty($draft->patient)) {
                                        if ($draft->patient == 'add_new') { ?>
                                            <option value="<?php echo 'add_new'; ?>" selected="selected">
                                                <?php echo lang('add_new'); ?></option>
                                        <?php } else {
                                            $patients = $this->patient_model->getPatientById($draft->patient);
                                            $age = explode('-', $patients->age);
                                        ?>
                                            <option value="<?php echo $patients->id; ?>" selected="selected">
                                                <?php echo $patients->name; ?> ( <?php echo lang('id'); ?>:
                                                <?php echo $patients->id; ?> - <?php echo lang('phone'); ?>:
                                                <?php echo $patients->phone; ?> - <?php echo lang('age'); ?>:
                                                <?php echo $age[0]; ?> ) </option>
                                        <?php }
                                    } else { ?>
                                        <option value="" selected="selected"><?php echo lang('select'); ?></option>
                                        <option value="<?php echo 'add_new'; ?>"><?php echo lang('add_new'); ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <?php
                            if (!isset($patients)) {
                                $patients = (object) array(
                                    'sex' => '',
                                    'patient_gender' => '',
                                    'name' => '',
                                    'email' => '',
                                    'phone' => '',
                                    'birthdate' => '',
                                    'age' => '',
                                );
                            }
                            if (!isset($age) || !is_array($age)) {
                                $age = array('0', '0', '0');
                            }
                            ?>

                            <!-- New Patient Form -->
                            <div class="pos_client">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <label class="field-label"><?php echo lang('patient') . ' ' . lang('name'); ?> <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="p_name" id="p_name" value='<?php
                                                if (!empty($payment)) { echo $patients->name; }
                                                elseif (!empty($draft->patient_name)) { echo $draft->patient_name; }
                                            ?>'>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <label class="field-label"><?php echo lang('patient') . ' ' . lang('email'); ?> <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="p_email" id="p_email" value='<?php
                                                if (!empty($payment)) { echo $patients->email; }
                                                elseif (!empty($draft->patient_email)) { echo $draft->patient_email; }
                                            ?>'>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <label class="field-label"><?php echo lang('patient') . ' ' . lang('phone'); ?> <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="p_phone" id="p_phone" value='<?php
                                                if (!empty($payment)) { echo $patients->phone; }
                                                elseif (!empty($draft->patient_phone)) { echo $draft->patient_phone; }
                                            ?>'>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <label class="field-label"><?php echo lang('patient') . ' ' . lang('birth_date'); ?></label>
                                            <input type="text" class="form-control datepicker" id="p_birth" name="p_birth" value='<?php
                                                if (!empty($payment)) { echo $patients->birthdate; }
                                                elseif (!empty($draft->birthdate)) { echo $draft->birthdate; }
                                            ?>' readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="mb-2">
                                            <label class="field-label"><?php echo lang('patient') . ' ' . lang('age'); ?></label>
                                            <div class="input-group">
                                                <input type="number" min="0" max="150" class="form-control" name="years" placeholder="<?php echo lang('years'); ?>" value='<?php
                                                    if (!empty($payment)) { echo $age[0]; }
                                                    elseif (!empty($draft->age)) { echo $age[0]; }
                                                ?>'>
                                                <span class="input-group-text"><?php echo lang('y'); ?></span>
                                                <input type="number" min="0" max="12" class="form-control" name="months" placeholder="<?php echo lang('months'); ?>" value='<?php
                                                    if (!empty($payment)) { echo $age[1]; }
                                                    elseif (!empty($draft->age)) { echo $age[1]; }
                                                ?>'>
                                                <span class="input-group-text"><?php echo lang('m'); ?></span>
                                                <input type="number" min="0" max="29" class="form-control" name="days" placeholder="<?php echo lang('days'); ?>" value='<?php
                                                    if (!empty($payment)) { echo $age[2]; }
                                                    elseif (!empty($draft->age)) { echo $age[2]; }
                                                ?>'>
                                                <span class="input-group-text"><?php echo lang('d'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <label class="field-label"><?php echo lang('gender'); ?></label>
                                            <select class="form-control" id="p_gender" name="p_gender">
                                                <option value="Male" <?php
                                                    if (!empty($patients->sex) && $patients->sex == 'Male') { echo 'selected'; }
                                                    elseif (!empty($patients->patient_gender) && $patients->patient_gender == 'Male') { echo 'selected'; }
                                                ?>><?php echo lang('male'); ?></option>
                                                <option value="Female" <?php
                                                    if (!empty($patients->sex) && $patients->sex == 'Female') { echo 'selected'; }
                                                    elseif (!empty($patients->patient_gender) && $patients->patient_gender == 'Female') { echo 'selected'; }
                                                ?>><?php echo lang('female'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Doctor Selection -->
                            <div class="mb-4">
                                <label class="field-label"><?php echo lang('doctor'); ?><span class="text-danger">*</span></label>
                                <select class="form-control add_doctor" id="add_doctor" name="doctor" value='' required>
                                    <?php if (!empty($payment)) { ?>
                                        <option value="<?php echo $doctors->id; ?>" selected="selected">
                                            <?php echo $doctors->name; ?> - <?php echo $doctors->id; ?>
                                        </option>
                                    <?php } elseif (!empty($draft->doctor)) {
                                        if ($draft->doctor == 'add_new') { ?>
                                            <option value="<?php echo 'add_new'; ?>" selected="selected">
                                                <?php echo lang('add_new'); ?></option>
                                        <?php } else {
                                            $doctor_name = $this->doctor_model->getDoctorById($draft->doctor)->name;
                                        ?>
                                            <option value="<?php echo $draft->doctor; ?>" selected="selected">
                                                <?php echo $doctor_name . ' (' . lang('id') . ': ' . $draft->doctor . ')'; ?>
                                            </option>
                                        <?php }
                                    } ?>
                                </select>
                            </div>

                            <!-- New Doctor Form -->
                            <div class="pos_doctor">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <label class="field-label"><?php echo lang('doctor') . ' ' . lang('name'); ?></label>
                                            <input type="text" class="form-control" name="d_name" id="d_name" value="<?php echo !empty($draft->doctor) && $draft->doctor == 'add_new' && !empty($draft->doctor_name) ? $draft->doctor_name : ''; ?>" placeholder="<?php echo lang('doctor') . ' ' . lang('name'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <label class="field-label"><?php echo lang('doctor') . ' ' . lang('email'); ?></label>
                                            <input type="email" class="form-control" name="d_email" id="d_email" value="<?php echo !empty($draft->doctor) && $draft->doctor == 'add_new' && !empty($draft->doctor_email) ? $draft->doctor_email : ''; ?>" placeholder="<?php echo lang('doctor') . ' ' . lang('email'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <label class="field-label"><?php echo lang('doctor') . ' ' . lang('phone'); ?></label>
                                            <input type="tel" class="form-control" name="d_phone" id="d_phone" value="<?php echo !empty($draft->doctor) && $draft->doctor == 'add_new' && !empty($draft->doctor_phone) ? $draft->doctor_phone : ''; ?>" placeholder="<?php echo lang('doctor') . ' ' . lang('phone'); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Item Selection -->
                            <div>
                                <label class="field-label"><?php echo lang('item'); ?><span class="text-danger">*</span></label>
                                <select name="category_name[]" class="form-control multi-select option_select" multiple="" id="my_multi_select3" required>
                                    <?php foreach ($categories as $category) { ?>
                                        <option class="ooppttiioonn" data-id="<?php echo $category->c_price; ?>" data-idd="<?php echo $category->id; ?>" data-cat_name="<?php echo $category->category; ?>" value="<?php echo $category->id; ?>" <?php
                                            if (!empty($payment->category_name)) {
                                                $category_name = $payment->category_name;
                                                $category_name1 = explode(',', $category_name);
                                                foreach ($category_name1 as $category_name2) {
                                                    $category_name3 = explode('*', $category_name2);
                                                    if ($category_name3[0] == $category->id) {
                                                        echo 'data-qtity=' . $category_name3[3];
                                                    }
                                                }
                                            } elseif (!empty($draft->category_name)) {
                                                $category_name = $draft->category_name;
                                                $category_name1 = explode(',', $category_name);
                                                foreach ($category_name1 as $category_name2) {
                                                    $category_name3 = explode('*', $category_name2);
                                                    if ($category_name3[0] == $category->id) {
                                                        echo 'data-qtity=' . $category_name3[3];
                                                    }
                                                }
                                            }
                                        ?> <?php
                                            if (!empty($payment->category_name)) {
                                                $category_name = $payment->category_name;
                                                $category_name1 = explode(',', $category_name);
                                                foreach ($category_name1 as $category_name2) {
                                                    $category_name3 = explode('*', $category_name2);
                                                    if ($category_name3[0] == $category->id) {
                                                        echo 'selected';
                                                    }
                                                }
                                            } elseif (!empty($draft->category_name)) {
                                                $category_name = $draft->category_name;
                                                $category_name1 = explode(',', $category_name);
                                                foreach ($category_name1 as $category_name2) {
                                                    $category_name3 = explode('*', $category_name2);
                                                    if ($category_name3[0] == $category->id) {
                                                        echo 'selected';
                                                    }
                                                }
                                            }
                                        ?>>
                                            <?php echo $category->category . ' - ' . $settings->currency . '' . $category->c_price; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <a target="_blank" href="finance/addPaymentCategoryView" class="add-new-link">
                                    <i class="fas fa-plus-circle"></i><?php echo lang('add_new') ?> <?php echo lang('item') ?>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- ═══════════════════════════════════════════
                     COLUMN 2 — Selected Items & Quantities
                     ═══════════════════════════════════════════ -->
                <div class="col-12 col-lg-3 col-xl-4">
                    <div class="pay-card">
                        <div class="pay-card-header">
                            <span class="card-step step-2">2</span>
                            <h2 class="card-title"><?php echo lang('items') ?></h2>
                        </div>
                        <div class="pay-card-body">
                            <div class="col-md-12 qfloww">
                                <div class="items-header">
                                    <label><?php echo lang('items') ?></label>
                                    <label><?php echo lang('qty') ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ═══════════════════════════════════════════
                     COLUMN 3 — Totals & Payment
                     ═══════════════════════════════════════════ -->
                <div class="col-12 col-lg-4 col-xl-4">
                    <div class="pay-card add-payment-totals-card">
                        <div class="pay-card-header">
                            <span class="card-step step-3">3</span>
                            <h2 class="card-title"><?php echo lang('payment'); ?></h2>
                        </div>
                        <div class="pay-card-body">

                            <!-- Sub Total -->
                            <div class="totals-row">
                                <div class="totals-label"><?php echo lang('sub_total'); ?></div>
                                <div class="totals-value">
                                    <input type="text" class="form-control pay_in" name="subtotal" id="subtotal" value='<?php
                                        if (!empty($payment->amount)) { echo $payment->amount; }
                                        elseif (!empty($draft->amount)) { echo $draft->amount; }
                                    ?>' placeholder="0.00" disabled>
                                </div>
                            </div>

                            <!-- Discount -->
                            <div class="totals-row">
                                <div class="totals-label"><?php echo lang('discount'); ?><?php if ($discount_type == 'percentage') { echo ' (%)'; } ?></div>
                                <div class="totals-value">
                                    <div class="input-group">
                                        <input type="number" class="form-control pay_in percent_input" min="0" max="100" step="0.01" name="percent_discount" id="dis_id_percent" value='<?php
                                            if (!empty($payment->percent_discount)) {
                                                $percent_discount = explode('*', $payment->percent_discount);
                                                echo $percent_discount[0];
                                            } elseif (!empty($draft->percent_discount)) {
                                                $percent_discount = explode('*', $draft->percent_discount);
                                                echo $percent_discount[0];
                                            } else {
                                                echo $settings->discount_percent;
                                            }
                                        ?>' placeholder="">
                                        <span class="input-group-text percent_amount">%</span>
                                        <input type="number" class="form-control pay_in percent_input" step="0.01" name="discount" id="dis_id" value='<?php
                                            if (!empty($payment->discount)) {
                                                $discount = explode('*', $payment->discount);
                                                echo $discount[0];
                                            } elseif (!empty($draft->discount)) {
                                                $discount = explode('*', $draft->discount);
                                                echo $discount[0];
                                            } else {
                                                echo '0';
                                            }
                                        ?>' placeholder="">
                                        <span class="input-group-text percent_amount"><?php echo $settings->currency; ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- VAT -->
                            <div class="totals-row">
                                <div class="totals-label"><?php echo lang('vat'); ?></div>
                                <div class="totals-value">
                                    <div class="input-group">
                                        <input type="number" class="form-control pay_in percent_input" min="0" max="100" step="0.01" name="vat" id="vat" value='<?php
                                            if (!empty($payment->vat_amount_percent)) { echo $payment->vat_amount_percent; }
                                            elseif (!empty($draft->vat_amount_percent)) { echo $draft->vat_amount_percent; }
                                            else { echo $settings->vat; }
                                        ?>' placeholder="">
                                        <span class="input-group-text percent_amount">%</span>
                                        <input type="number" class="form-control pay_in percent_input" step="0.01" name="vat_amount" id="vat_amount" value='<?php
                                            if (!empty($payment->vat)) { echo $payment->vat; }
                                            elseif (!empty($draft->vat)) { echo $draft->vat; }
                                            else { echo '0'; }
                                        ?>' placeholder="">
                                        <span class="input-group-text percent_amount"><?php echo $settings->currency; ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Gross Total -->
                            <div class="totals-row totals-gross">
                                <div class="totals-label"><?php echo lang('gross_total'); ?></div>
                                <div class="totals-value">
                                    <input type="text" class="form-control pay_in" name="grsss" id="gross" value='<?php
                                        if (!empty($payment->gross_total)) { echo $payment->gross_total; }
                                        elseif (!empty($draft->gross_total)) { echo $draft->gross_total; }
                                    ?>' placeholder="0.00" disabled>
                                </div>
                            </div>

                            <!-- Note -->
                            <div class="totals-row">
                                <div class="totals-label"><?php echo lang('note'); ?></div>
                                <div class="totals-value">
                                    <textarea class="form-control" name="remarks" rows="2" cols="20"><?php
                                        if (!empty($payment->remarks)) { echo $payment->remarks; }
                                        elseif (!empty($draft->remarks)) { echo $draft->remarks; }
                                    ?></textarea>
                                </div>
                            </div>

                            <!-- Deposited Amount -->
                            <div class="totals-row">
                                <div class="totals-label">
                                    <?php
                                    if (empty($payment)) {
                                        echo lang('deposited_amount');
                                    } else {
                                        echo lang('deposit') . ' 1 &middot; ' . date('d/m/Y', $payment->date);
                                    }
                                    ?>
                                </div>
                                <div class="totals-value">
                                    <input type="text" class="form-control pay_in" name="amount_received" id="amount_received" value='<?php
                                        if (!empty($payment->amount_received)) { echo $payment->amount_received; }
                                    ?>' placeholder="0.00" <?php
                                        if (!empty($payment->deposit_type)) {
                                            if ($payment->deposit_type == 'Card') { echo 'readonly'; }
                                        }
                                    ?>>
                                </div>
                            </div>

                            <!-- Due -->
                            <div class="totals-row totals-due">
                                <div class="totals-label"><?php echo lang('due'); ?></div>
                                <div class="totals-value">
                                    <input type="text" class="form-control pay_in" name="due" id="due" value='<?php
                                        if (!empty($payment)) {
                                            $deposit = $this->finance_model->getDepositByInvoiceId($payment->id);
                                            $deposits = array();
                                            if (!empty($deposit)) {
                                                foreach ($deposit as $depos) {
                                                    $deposits[] = $depos->deposited_amount;
                                                }
                                                $depos_amount = array_sum($deposits);
                                            } else {
                                                $depos_amount = 0;
                                            }
                                            echo $depos_amount;
                                        } elseif (!empty($draft->gross_total)) {
                                            if (!empty($draft->amount_received)) {
                                                echo ($draft->gross_total - $draft->amount_received);
                                            } else {
                                                echo $draft->gross_total;
                                            }
                                        } else {
                                            echo '0';
                                        }
                                    ?>' placeholder="0.00" disabled>
                                </div>
                            </div>

                            <?php if (empty($payment) || empty($payment->id)) { ?>
                            <!-- Payment Type -->
                            <div class="payment-type-section">
                                <label class="field-label"><?php echo lang('type'); ?></label>
                                <select class="form-control selecttype" id="selecttype" name="deposit_type" value=''>
                                    <?php if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist'))) { ?>
                                        <option value="Cash"><?php echo lang('cash'); ?></option>
                                        <option value="Card"><?php echo lang('card'); ?></option>
                                    <?php } ?>
                                </select>

                                <?php $payment_gateway = $settings->payment_gateway; ?>

                                <!-- Insurance -->
                                <div class="mt-3 <?php if (empty($payment) || empty($payment->deposit_type) || $payment->deposit_type !== 'Insurance') { echo 'hidden'; } ?> insurance_div">
                                    <label class="field-label"><?php echo lang('insurance'); ?></label>
                                    <div class="company_div mb-2">
                                        <select class="form-control w-100 js-example-basic-single" name="insurance_company" id="insurance_company" value=''>
                                            <option value="">Company name</option>
                                            <?php foreach ($insurance_companys as $insurance_company) { ?>
                                                <option value="<?php echo $insurance_company->id; ?>" <?php
                                                    if (!empty($setval)) {
                                                        if ($insurance_company->id == set_value('insurance_company')) { echo 'selected'; }
                                                    }
                                                    if (!empty($payment->insurance_company)) {
                                                        if ($insurance_company->id == $payment->insurance_company) { echo 'selected'; }
                                                    }
                                                ?>> <?php echo $insurance_company->name; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <label class="field-label mt-2"><?php echo lang('insurance_details'); ?></label>
                                    <textarea class="form-control" name="insurance_details" rows="2" cols="20"><?php
                                        if (!empty($payment->insurance_details)) { echo $payment->insurance_details; }
                                        elseif (!empty($draft->insurance_details)) { echo $draft->insurance_details; }
                                    ?></textarea>
                                </div>

                                <!-- Card Payment -->
                                <div class="cardPayment">
                                    <div class="payment pad_bot mt-3">
                                        <label class="field-label"><?php echo lang('accepted'); ?> <?php echo lang('cards'); ?></label>
                                        <div class="payment pad_bot">
                                            <img src="uploads/card.png" width="100%">
                                        </div>
                                    </div>

                                    <?php if ($payment_gateway == 'PayPal') { ?>
                                        <div class="mb-2">
                                            <label class="field-label"><?php echo lang('card'); ?> <?php echo lang('type'); ?></label>
                                            <select class="form-control" name="card_type" value=''>
                                                <option value="Mastercard"><?php echo lang('mastercard'); ?></option>
                                                <option value="Visa"><?php echo lang('visa'); ?></option>
                                                <option value="American Express"><?php echo lang('american_express'); ?></option>
                                            </select>
                                        </div>
                                    <?php } ?>

                                    <?php if ($payment_gateway == 'PayPal') { ?>
                                        <div class="mb-2">
                                            <label class="field-label"><?php echo lang('cardholder_name'); ?></label>
                                            <input type="text" id="cardholder" class="form-control pay_in" name="cardholder" value='' placeholder="">
                                        </div>
                                    <?php } ?>

                                    <?php if ($payment_gateway != 'Pay U Money' && $payment_gateway != 'Paystack' && $payment_gateway != 'SSLCOMMERZ' && $payment_gateway != 'Paytm') { ?>
                                        <div class="mb-2">
                                            <label class="field-label"><?php echo lang('card'); ?> <?php echo lang('number'); ?></label>
                                            <input type="text" id="card" class="form-control pay_in" name="card_number" value='' placeholder="">
                                        </div>
                                        <div class="mb-2">
                                            <label class="field-label"><?php echo lang('expire'); ?> <?php echo lang('date'); ?></label>
                                            <input type="text" class="form-control pay_in" id="expire" data-date="" data-date-format="MM YY" placeholder="Expiry (MM/YY)" name="expire_date" maxlength="7" aria-describedby="basic-addon1" value=''>
                                        </div>
                                        <div class="mb-2">
                                            <label class="field-label"><?php echo lang('cvv'); ?></label>
                                            <input type="text" class="form-control pay_in" id="cvv" maxlength="3" name="cvv" value='' placeholder="">
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php } ?>

                            <?php
                            if (!empty($payment)) {
                                $deposits = $this->finance_model->getDepositByPaymentId($payment->id);
                                $i = 1;
                                foreach ($deposits as $deposit) {
                                    if (empty($deposit->amount_received_id)) {
                                        $i = $i + 1; ?>
                                        <div class="totals-row deposit-row">
                                            <div class="totals-label">
                                                <?php echo lang('deposit') . ' ' . $i . ' &middot; ' . date('d/m/Y', $deposit->date); ?>
                                            </div>
                                            <div class="totals-value">
                                                <input type="text" class="form-control pay_in" name="deposit_edit_amount[]" id="amount_received" value='<?php echo $deposit->deposited_amount; ?>' <?php
                                                    if ($deposit->deposit_type == 'Card') { echo 'readonly'; }
                                                ?>>
                                                <input type="hidden" class="form-control pay_in" name="deposit_edit_id[]" id="amount_received" value='<?php echo $deposit->id; ?>' placeholder=" ">
                                            </div>
                                        </div>
                            <?php
                                    }
                                }
                            }
                            ?>

                            <input type="hidden" name="id" id="id_pay" value='<?php if (!empty($payment->id)) { echo $payment->id; } ?>'>
                            <input type="hidden" name="draft_id" id="draft_id" value='<?php if (!empty($draft) && !empty($draft->id)) { echo $draft->id; } ?>'>

                            <!-- Action Buttons -->
                            <div class="pay-actions">
                                <div class="form-group cashsubmit">
                                    <button type="submit" name="form_submit" value="save" id="submit1" class="btn btn-primary">
                                        <i class="fas fa-check"></i> <?php echo lang('save'); ?>
                                    </button>
                                </div>
                                <div class="form-group cardsubmit d-none">
                                    <button type="submit" name="form_submit" value="save" id="submit-btn" class="btn btn-primary" <?php if ($settings->payment_gateway == 'Stripe') { ?>onClick="stripePay(event);"<?php } ?>>
                                        <i class="fas fa-check"></i> <?php echo lang('save'); ?>
                                    </button>
                                </div>
                                <div class="form-group cashsubmit2">
                                    <button type="submit" name="form_submit" value="saveandprint" id="submit2" class="btn btn-info">
                                        <i class="fas fa-print"></i> <?php echo lang('save_and_print'); ?>
                                    </button>
                                </div>
                                <div class="form-group cardsubmit3 d-none">
                                    <button type="submit" name="form_submit" value="saveandprint" id="submit-btn2" class="btn btn-info" <?php if ($settings->payment_gateway == 'Stripe') { ?>onClick="stripePay(event);"<?php } ?>>
                                        <i class="fas fa-print"></i> <?php echo lang('save_and_print'); ?>
                                    </button>
                                </div>
                                <?php if (empty($payment)) { ?>
                                    <div class="form-group">
                                        <button type="submit" name="form_submit" value="save_as_draft" id="save_as_draft" class="btn btn-warning">
                                            <i class="fas fa-file-alt"></i> <?php echo lang('save_as_draft'); ?>
                                        </button>
                                    </div>
                                <?php } ?>
                            </div>

                        </div>
                    </div>
                </div>

            </form>

            <?php if (!empty($draft) && !empty($draft->doctor)) {
                if ($draft->doctor == 'add_new') {
                    $add_doctor = 'yes';
                } else {
                    $add_doctor = 'no';
                }
            } else {
                $add_doctor = 'no';
            } ?>
        </div>
    </section>

</div>

<?php if (!empty($gateway->publish)) {
    $gateway_stripe = $gateway->publish;
} else {
    $gateway_stripe = '';
} ?>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">
    var select_doctor = "<?php echo lang('select_doctor'); ?>";
</script>
<script type="text/javascript">
    var select_patient = "<?php echo lang('select_patient'); ?>";
</script>
<script type="text/javascript">
    var discount_type = "<?php echo $discount_type; ?>";
</script>
<script type="text/javascript">
    var add_doctor = "<?php echo $add_doctor; ?>";
</script>
<script type="text/javascript">
    var currency = "<?php echo $settings->currency; ?>";
</script>
<script type="text/javascript">
    var publish = "<?php echo $gateway_stripe; ?>";
</script>
<script src="common/js/moment.min.js"></script>
<script type="text/javascript">
    var payment_gateway = "<?php echo $settings->payment_gateway; ?>";
</script>
<script src="common/extranal/js/finance/add_payment_view.js"></script>
