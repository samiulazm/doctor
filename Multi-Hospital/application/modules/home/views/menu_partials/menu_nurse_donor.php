<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group(array('Nurse'))) { ?>
    <?php if (in_array('donor', $this->modules)) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="donor">
                <i class="text-secondary nav-icon fas fa-medkit"></i>
                <p> <?php echo lang('donor'); ?> </p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="donor/bloodBank">
                <i class="text-secondary nav-icon fas fa-tint"></i>
                <p> <?php echo lang('blood_bank'); ?> </p>
            </a>
        </li>
    <?php } ?>
<?php } ?>
