<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group(array('Nurse', 'Receptionist'))) { ?>
    <?php $render_sidebar_section('hospital_operations_secondary', lang('hospital_operations')); ?>
    <?php if (in_array('bed', $this->modules)) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="#">
                <i class="text-secondary nav-icon fas fa-procedures"></i>
                <p>Beds &amp; Admissions<i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a class="nav-link text-white" href="bed">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('bed_list'); ?></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="bed/bedCategory">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('bed_category'); ?></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="bed/bedAllotment">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('bed_allotments'); ?></p>
                    </a>
                </li>
            </ul>
        </li>
    <?php } ?>
<?php } ?>
