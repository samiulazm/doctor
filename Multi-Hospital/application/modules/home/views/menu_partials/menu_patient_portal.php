<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group(array('Patient'))) { ?>
    <?php if (in_array('appointment', $this->modules)) { ?>
        <li class="nav-item"><a class="nav-link text-white" href="appointment/myAppointments"><i class="text-secondary nav-icon fas fa-calendar"></i>
                <p><?php echo lang('all'); ?> <?php echo lang('appointments'); ?></p>
            </a>
        </li>
        <li class="nav-item"><a class="nav-link text-white" href="appointment/myTodays"><i class="text-secondary nav-icon fas fa-headphones"></i>
                <p><?php echo lang('todays'); ?> <?php echo lang('appointment'); ?></p>
            </a>
        </li>
    <?php } ?>
    <?php if (in_array('appointment', $this->modules)) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="patient/calendar">
                <i class="text-secondary nav-icon far fa-calendar"></i>
                <p> <?php echo lang('appointment'); ?> <?php echo lang('calendar'); ?> </p>
            </a>
        </li>
    <?php } ?>

    <?php if (in_array('lab', $this->modules)) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="lab/myLab">
                <i class="text-secondary nav-icon fas fa-file-medical-alt"></i>
                <p> <?php echo lang('diagnosis'); ?> <?php echo lang('reports'); ?> </p>
            </a>
        </li>
    <?php } ?>


    <?php if (in_array('report', $this->modules)) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="report/myreports">
                <i class="text-secondary nav-icon fas fa-file-medical-alt"></i>
                <p> <?php echo lang('other'); ?> <?php echo lang('reports'); ?> </p>
            </a>
        </li>
    <?php } ?>
<?php } ?>
