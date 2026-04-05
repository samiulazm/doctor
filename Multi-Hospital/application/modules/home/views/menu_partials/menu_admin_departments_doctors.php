<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group('admin')) { ?>
    <?php if (in_array('department', $this->modules)) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="department">
                <i class="text-secondary nav-icon fas fa-sitemap"></i>
                <p><?php echo lang('departments'); ?></p>
            </a>
        </li>
    <?php } ?>
<?php } ?>
<?php if ($this->ion_auth->in_group(array('admin'))) { ?>
    <?php if (in_array('doctor', $this->modules)) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="#">
                <i class="text-secondary nav-icon fa fa-user-md"></i>
                <p><?php echo lang('doctors'); ?><i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><a class="nav-link text-white" href="doctor"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p> <?php echo lang('list_of_doctors'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="doctor/addnewview"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p> <?php echo lang('add_new'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="appointment/treatmentReport"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p> <?php echo lang('treatment_history'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="doctorvisit"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p> <?php echo lang('doctor_visit'); ?></p>
                    </a></li>

                <li class="nav-item"><a class="nav-link text-white" href="schedule"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('all'); ?> <?php echo lang('schedule'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="schedule/allHolidays"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('holidays'); ?></p>
                    </a></li>
            </ul>
        </li>
    <?php } ?>
<?php } ?>
