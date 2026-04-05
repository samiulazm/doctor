<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php
if ($this->ion_auth->in_group(array('Receptionist'))) {
?>
    <?php $render_sidebar_section('hospital_operations', lang('hospital_operations')); ?>
    <li class="nav-item">
        <a class="nav-link text-white" href="#">
            <i class="text-secondary nav-icon fas fa-flask"></i>
            <p>Lab &amp; Diagnostics<i class="right fas fa-angle-left"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <?php if (in_array('lab', $this->modules)) { ?>
                <li class="nav-item"><a class="nav-link text-white" href="lab/testStatus"><i class="text-secondary nav-icon fas fa-vial"></i>
                        <p><?php echo lang('lab_tests'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="lab"><i class="text-secondary nav-icon fas fa-file-medical-alt"></i>
                        <p><?php echo lang('lab_reports'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="lab/reportDelivery"><i class="text-secondary nav-icon fas fa-truck"></i>
                        <p><?php echo lang('report') . " " . lang('delivery'); ?></p>
                    </a></li>
            <?php } ?>
        </ul>
    </li>
<?php
}
?>

<?php if ($this->ion_auth->in_group(array('admin', 'Doctor', 'Laboratorist'))) { ?>
    <?php $render_sidebar_section('hospital_operations', lang('hospital_operations')); ?>
    <li class="nav-item">
        <a class="nav-link text-white" href="#">
            <i class="text-secondary nav-icon fas fa-flask"></i>
            <p>Lab &amp; Diagnostics<i class="right fas fa-angle-left"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <?php if (in_array('lab', $this->modules)) { ?>
                <li class="nav-item"><a class="nav-link text-white" href="lab/testStatus"><i class="text-secondary nav-icon fas fa-vial"></i>
                        <p><?php echo lang('lab_tests'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="lab"><i class="text-secondary nav-icon fas fa-file-medical-alt"></i>
                        <p><?php echo lang('lab_reports'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="lab/reportDelivery"><i class="text-secondary nav-icon fas fa-truck"></i>
                        <p><?php echo lang('report') . " " . lang('delivery'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="lab/template"><i class="text-secondary nav-icon fas fa-file-invoice"></i>
                        <p><?php echo lang('template'); ?></p>
                    </a></li>
            <?php } ?>
        </ul>
    </li>
<?php } ?>
