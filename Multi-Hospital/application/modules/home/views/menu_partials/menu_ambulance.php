<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group(array('admin', 'Doctor', 'Nurse'))) { ?>
    <?php $render_sidebar_section('hospital_operations', lang('hospital_operations')); ?>
    <li class="nav-item">
        <a class="nav-link text-white" href="#">
            <i class="text-secondary nav-icon fas fa-ambulance"></i>
            <p>Ambulance<i class="right fas fa-angle-left"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a class="nav-link text-white" href="ambulance">
                    <i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('fleet_management'); ?></p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="ambulance/bookings">
                    <i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('bookings'); ?></p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="ambulance/newBooking">
                    <i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('new_booking'); ?></p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="ambulance/payments">
                    <i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('payments'); ?></p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="ambulance/reports">
                    <i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('reports'); ?></p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="ambulance/rates">
                    <i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('rates'); ?></p>
                </a>
            </li>
        </ul>
    </li>
<?php } ?>
