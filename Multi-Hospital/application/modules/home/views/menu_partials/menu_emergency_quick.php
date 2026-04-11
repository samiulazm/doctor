<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if (!$this->ion_auth->in_group('superadmin') && $this->ion_auth->in_group(array('admin', 'Doctor', 'Nurse'))) { ?>
    <?php $render_sidebar_section('clinical_care', lang('clinical_care')); ?>
    <li class="nav-item">
        <a class="nav-link text-white" href="emergency">
            <i class="text-secondary nav-icon fas fa-exclamation-triangle"></i>
            <p><?php echo lang('emergency'); ?></p> 
        </a>
    </li>
<?php } ?>
<?php if (!$this->ion_auth->in_group('superadmin') && $this->ion_auth->in_group(array('admin', 'Nurse', 'Doctor'))) { ?>
    <!-- <li class="nav-item">
        <a class="nav-link text-white" href="#">
            <i class="text-secondary nav-icon fas fa-bolt"></i>
            <p><?php echo lang('quick_access'); ?><i class="right fas fa-angle-left"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a class="nav-link text-white" href="appointment/addNewViewQuick">
                    <i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('add_appointment'); ?></p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="patient/addNewViewQuick">
                    <i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('add_patient'); ?></p>
                </a>
            </li>
            <?php if ($this->ion_auth->in_group('Doctor')) { ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="prescription/addNewPrescriptionQuick">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('add_prescription'); ?></p>
                    </a>
                </li>
            <?php } ?>
            <li class="nav-item">
                <a class="nav-link text-white" href="patient/caseListQuick">
                    <i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('add_case'); ?></p>
                </a>
            </li>
            <?php if ($this->ion_auth->in_group('admin')) { ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="finance/addPaymentViewQuick">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('add_payment'); ?></p>
                    </a>
                </li>
            <?php } ?>
            <li class="nav-item">
                <a class="nav-link text-white" href="bed/addAllotmentViewQuick">
                    <i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('new_admission'); ?></p>
                </a>
            </li>
        </ul>
    </li> -->
<?php } ?>
