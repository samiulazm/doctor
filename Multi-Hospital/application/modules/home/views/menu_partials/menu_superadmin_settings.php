<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group(array('superadmin'))) { ?>
    <li class="nav-item">
        <a class="nav-link text-white" href="settings">
            <i class="text-secondary nav-icon fas fa-cog"></i>
            <p><?php echo lang('system_settings'); ?></p>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-white" href="settings/googleReCaptcha">
            <i class="text-secondary nav-icon fas fa-cog"></i>
            <p>Google reCAPTCHA</p>
        </a>
    </li>
    <?php if (in_array('pgateway', $this->super_modules)) { ?>
        <li class="nav-item"><a class="nav-link text-white" href="pgateway"><i class="text-secondary nav-icon far fa-credit-card"></i>
                <p><?php echo lang('payment_gateway'); ?></p>
            </a>
        </li>
    <?php } ?>
    <li class="nav-item"><a class="nav-link text-white" href="settings/language"><i class="text-secondary nav-icon fas fa-language"></i>
            <p><?php echo lang('language'); ?></p>
        </a></li>
    <?php if ($this->config->item('audit_ui_enabled')) { ?>
        <li class="nav-item"><a class="nav-link text-white" href="auditTrail"><i class="text-secondary nav-icon fas fa-clipboard-list"></i>
                <p><?php echo lang('audit_trail'); ?></p>
            </a></li>
    <?php } ?>
    <!-- <li class="nav-item"><a class="nav-link text-white" href="settings/verifyPurchase"><i class="text-secondary nav-icon far fa-arrow-right"></i> <p></p> <?php echo lang('purchase_code'); ?></a></li> -->
<?php } ?>
