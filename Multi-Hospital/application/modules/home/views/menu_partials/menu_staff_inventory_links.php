<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group(array('Nurse', 'Pharmacist', 'Laboratorist'))) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="inventory">
                <i class="text-secondary nav-icon fas fa-boxes"></i>
                <p> <?php echo lang('inventory'); ?> </p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="inventory/items">
                <i class="text-secondary nav-icon fas fa-list"></i>
                <p> <?php echo lang('inventory_items'); ?> </p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="inventory/usage">
                <i class="text-secondary nav-icon fas fa-clipboard-list"></i>
                <p> <?php echo lang('usage_logs'); ?> </p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="inventory/low_stock">
                <i class="text-secondary nav-icon fas fa-exclamation-triangle"></i>
                <p> <?php echo lang('low_stock_items'); ?> </p>
            </a>
        </li>
<?php } ?>
