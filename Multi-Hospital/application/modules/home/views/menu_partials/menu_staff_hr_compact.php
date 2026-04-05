<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group(array('Accountant', 'Receptionist', 'Nurse', 'Laboratorist', 'Pharmacist', 'Doctor'))) { ?>
    <?php $render_sidebar_section('hospital_operations', lang('hospital_operations')); ?>
    <li class="nav-item">
        <a class="nav-link text-white" href="#">
            <i class="text-secondary nav-icon fas fa-users"></i>
            <p>HR &amp; Staff<i class="right fas fa-angle-left"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <?php if (in_array('attendance', $this->modules)) { ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="attendance">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('attendance'); ?></p>
                    </a>
                </li>
            <?php } ?>

            <?php if (in_array('leave', $this->modules)) { ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="leave">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('leave'); ?></p>
                    </a>
                </li>
            <?php } ?>

            <?php if (in_array('payroll', $this->modules)) { ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="payroll/employeePayroll">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('payroll'); ?></p>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </li>
<?php } ?>
