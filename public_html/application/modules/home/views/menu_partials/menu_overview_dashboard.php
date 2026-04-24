<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php $render_sidebar_section('overview', lang('overview')); ?>
<li class="nav-item mt-3">
    <a class="nav-link text-white" href="home">
        <i class="text-secondary nav-icon fas fa-th"></i>
        <p><?php echo lang('dashboard'); ?></p>
    </a>
</li>

<!-- Advanced Dashboards -->
<?php if ($this->ion_auth->in_group(array('admin', 'superadmin', 'Doctor', 'Nurse', 'Accountant'))) { ?>
<!-- <li class="nav-item">
    <a class="nav-link text-white" href="#">
        <i class="text-secondary nav-icon fas fa-chart-line"></i>
        <p>Advanced Dashboards<i class="right fas fa-angle-left"></i></p>
    </a>
    <ul class="nav nav-treeview">
        <?php if ($this->ion_auth->in_group(array('admin', 'superadmin'))) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="dashboard/executive">
                <i class="text-secondary nav-icon fas fa-crown"></i>
                <p>Executive Dashboard</p>
            </a>
        </li>
        <?php } ?>
        <?php if ($this->ion_auth->in_group(array('admin', 'superadmin', 'Doctor', 'Nurse'))) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="dashboard/clinical">
                <i class="text-secondary nav-icon fas fa-user-md"></i>
                <p>Clinical Dashboard</p>
            </a>
        </li>
        <?php } ?>
        <?php if ($this->ion_auth->in_group(array('admin', 'superadmin', 'Accountant'))) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="dashboard/financial">
                <i class="text-secondary nav-icon fas fa-dollar-sign"></i>
                <p>Financial Dashboard</p>
            </a>
        </li>
        <?php } ?>
        <?php if ($this->ion_auth->in_group(array('admin', 'superadmin', 'Nurse', 'Receptionist', 'Doctor'))) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="dashboard/operational">
                <i class="text-secondary nav-icon fas fa-cogs"></i>
                <p>Operational Dashboard</p>
            </a>
        </li>
        <?php } ?>
    </ul>
</li> -->
<?php } ?>
