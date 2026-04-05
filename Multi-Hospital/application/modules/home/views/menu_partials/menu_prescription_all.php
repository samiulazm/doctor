<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group(array('admin', 'Pharmacist'))) { ?>
    <?php if (in_array('prescription', $this->modules)) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="prescription/all">
                <i class="text-secondary nav-icon fas fa-prescription"></i>
                <p><?php echo lang('prescription'); ?></p>
            </a>
        </li>
    <?php } ?>
<?php } ?>
