<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group(array('admin'))) { ?>
    <?php if (in_array('donor', $this->modules)) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="#">
                <i class="text-secondary nav-icon fas fa-hand-holding-water"></i>
                <p><?php echo lang('donor') ?><i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><a class="nav-link text-white" href="donor"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('donor_list'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="donor/addDonorView"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('add_donor'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="donor/bloodBank"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('blood_bank'); ?></p>
                    </a></li>
            </ul>
        </li>
    <?php } ?>
<?php } ?>
