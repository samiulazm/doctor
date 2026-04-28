<?php defined('BASEPATH') OR exit('No direct script access allowed');
$currency = isset($settings->currency) && $settings->currency !== '' ? $settings->currency : 'BDT';
$patient_label = !empty($q->guest_name) ? $q->guest_name : ('#' . $q->patient_id);
?>
<div class="chamber-billing-panel p-3 border rounded bg-white">
    <p class="small font-weight-bold text-muted text-uppercase mb-2" style="letter-spacing:.04em;">
        <i class="fas fa-file-invoice-dollar mr-1"></i>
        Billing - <?php echo htmlspecialchars($patient_label, ENT_QUOTES, 'UTF-8'); ?>
    </p>

    <div class="form-row mb-2">
        <div class="col-auto">
            <label class="small font-weight-bold text-muted">Fee amount</label>
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <span class="input-group-text"><?php echo htmlspecialchars($currency, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <input type="number" class="form-control" id="fee_<?php echo (int) $q->id; ?>"
                       name="amount" step="0.01" min="0.01" placeholder="0.00"
                       style="max-width:100px;" required>
            </div>
        </div>
        <div class="col-auto">
            <label class="small font-weight-bold text-muted">Fee type</label>
            <select class="form-control form-control-sm" id="feetype_<?php echo (int) $q->id; ?>" name="fee_type">
                <option value="consultation">Consultation</option>
                <option value="procedure">Procedure</option>
            </select>
        </div>
    </div>

    <div class="mb-2">
        <label class="small font-weight-bold text-muted d-block">Payment status</label>
        <div class="btn-group btn-group-sm" id="paidDueToggle_<?php echo (int) $q->id; ?>" role="group" aria-label="Paid or due">
            <button type="button" class="btn btn-success active" data-status="paid">
                <i class="fas fa-check-circle mr-1"></i> Paid
            </button>
            <button type="button" class="btn btn-warning" data-status="due">
                <i class="fas fa-clock mr-1"></i> Due
            </button>
        </div>
        <input type="hidden" id="payStatus_<?php echo (int) $q->id; ?>" name="pay_status" value="paid">
        <input type="hidden" id="paid_status_<?php echo (int) $q->id; ?>" name="paid_status" value="paid">
    </div>

    <div class="mb-2">
        <label class="small font-weight-bold text-muted d-block">Payment method</label>
        <div class="btn-group btn-group-sm" id="payMethodToggle_<?php echo (int) $q->id; ?>" role="group" aria-label="Payment method">
            <button type="button" class="btn btn-outline-secondary active" data-method="cash">
                <i class="fas fa-money-bill-wave mr-1"></i> Cash
            </button>
            <button type="button" class="btn btn-outline-secondary" data-method="bkash">
                <i class="fas fa-mobile-alt mr-1"></i> bKash
            </button>
        </div>
        <input type="hidden" id="payMethod_<?php echo (int) $q->id; ?>" name="pay_method" value="cash">
        <input type="hidden" id="payment_method_<?php echo (int) $q->id; ?>" name="payment_method" value="cash">
    </div>

    <div id="bkashSection_<?php echo (int) $q->id; ?>" class="d-none mb-2">
        <button type="button" class="btn btn-sm btn-bkash"
                id="initiateBkash_<?php echo (int) $q->id; ?>"
                data-queue-id="<?php echo (int) $q->id; ?>">
            <i class="fas fa-mobile-alt mr-1"></i> Initiate bKash
        </button>
        <span id="bkashStatus_<?php echo (int) $q->id; ?>" class="small ml-2 text-muted"></span>
    </div>

    <div class="mb-2">
        <input class="form-control form-control-sm" id="remarks_<?php echo (int) $q->id; ?>"
               name="remarks" placeholder="Remarks (optional)" style="max-width:220px;">
    </div>

    <button type="button" class="btn btn-sm btn-primary"
            id="saveBilling_<?php echo (int) $q->id; ?>"
            data-action="record_fee"
            data-queue-id="<?php echo (int) $q->id; ?>">
        <i class="fas fa-save mr-1"></i> Record fee
    </button>
</div>
