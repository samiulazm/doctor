<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group(array('Nurse', 'Receptionist'))) { ?>
    <?php if (in_array('appointment', $this->modules)) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="#">
                <i class="text-secondary nav-icon far fa-clock"></i>
                <p><?php echo lang('schedule'); ?><i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
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

<?php if ($this->ion_auth->in_group(array('Doctor'))) { ?>
    <?php if (in_array('appointment', $this->modules)) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="#">
                <i class="text-secondary nav-icon far fa-clock"></i>
                <p><?php echo lang('schedule'); ?><i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><a class="nav-link text-white" href="schedule/timeSchedule"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('all'); ?> <?php echo lang('schedule'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="schedule/holidays"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('holidays'); ?></p>
                    </a></li>
            </ul>
        </li>
    <?php } ?>
<?php } ?>

<?php if ($this->ion_auth->in_group(array('admin', 'Doctor', 'Nurse', 'Receptionist'))) { ?>
    <?php if (in_array('appointment', $this->modules)) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="#">
                <i class="text-secondary nav-icon far fa-calendar-check"></i>
                <p><?php echo lang('appointment'); ?><i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><a class="nav-link text-white" href="appointment"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('all'); ?> <?php echo lang('appointments'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="appointment/addNewView"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('add'); ?> <?php echo lang('appointment'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="appointment/todays"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('todays'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="appointment/upcoming"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('upcoming'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="appointment/calendar"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('calendar'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="appointment/request"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('request'); ?></p>
                    </a></li>
            </ul>
        </li>
    <?php } ?>
<?php } ?>
