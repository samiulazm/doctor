<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group('admin')) { ?>
    <li class="nav-item">
        <a class="nav-link text-white" href="#">
            <i class="text-secondary nav-icon fas fa-users"></i>
            <p><?php echo lang('human_resources'); ?><i class="right fas fa-angle-left"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <?php if (in_array('nurse', $this->modules) || in_array('pharmacist', $this->modules) || in_array('laboratorist', $this->modules) || in_array('accountant', $this->modules) || in_array('receptionist', $this->modules)) { ?>
                <?php if (in_array('nurse', $this->modules)) { ?>
                    <li class="nav-item"><a class="nav-link text-white" href="nurse"><i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('nurse'); ?></p>
                        </a></li>
                <?php } ?>
                <?php if (in_array('pharmacist', $this->modules)) { ?>
                    <li class="nav-item"><a class="nav-link text-white" href="pharmacist"><i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('pharmacist'); ?></p>
                        </a></li>
                <?php } ?>
                <?php if (in_array('laboratorist', $this->modules)) { ?>
                    <li class="nav-item"><a class="nav-link text-white" href="laboratorist"><i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('laboratorist'); ?></p>
                        </a></li>
                <?php } ?>
                <?php if (in_array('accountant', $this->modules)) { ?>
                    <li class="nav-item"><a class="nav-link text-white" href="accountant"><i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('accountant'); ?></p>
                        </a></li>
                <?php } ?>
                <?php if (in_array('receptionist', $this->modules)) { ?>
                    <li class="nav-item"><a class="nav-link text-white" href="receptionist"><i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('receptionist'); ?></p>
                        </a></li>
                <?php } ?>
            <?php } ?>

            <?php if (in_array('payroll', $this->modules)) { ?>
                <li class="nav-item"><a class="nav-link text-white" href="payroll"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('payroll'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="payroll/salary"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('salary'); ?></p>
                    </a></li>
            <?php } ?>

            <?php if (in_array('attendance', $this->modules)) { ?>
                <li class="nav-item"><a class="nav-link text-white" href="attendance"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('attendance'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="attendance/report"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('attendance'); ?> <?php echo lang('report'); ?></p>
                    </a></li>
            <?php } ?>

            <?php if (in_array('leave', $this->modules)) { ?>
                <li class="nav-item"><a class="nav-link text-white" href="leave"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('leave'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="leave/leaveType"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('leave_type'); ?></p>
                    </a></li>
            <?php } ?>
        </ul>
    </li>
<?php } ?>
