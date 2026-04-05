<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group('Patient')) { ?>

    <?php if (in_array('donor', $this->modules)) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="donor">
                <i class="text-secondary nav-icon far fa-user"></i>
                <p><?php echo lang('donor'); ?></p>
            </a>
        </li>
    <?php } ?>
    <?php if (in_array('notice', $this->modules)) { ?>
        <li class="nav-item"><a class="nav-link text-white" href="notice"><i class="text-secondary nav-icon fas fa-bell"></i>
                <p><?php echo lang('notice'); ?></p>
            </a></li>
    <?php } ?>
<?php } ?>
<?php if ($this->ion_auth->in_group('im')) { ?>
    <li class="nav-item">
        <a class="nav-link text-white" href="patient/addNewView">
            <i class="text-secondary nav-icon far fa-user"></i>
            <p> <?php echo lang('add_patient'); ?> </p>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-white" href="finance/addPaymentView">
            <i class="text-secondary nav-icon far fa-user"></i>
            <p> <?php echo lang('add_payment'); ?> </p>
        </a>
    </li>
<?php } ?>


<?php if ($this->ion_auth->in_group('Doctor')) { ?>
    <li class=" nav-item">
        <a class="nav-link text-white" href="meeting/settings">
            <i class="text-secondary nav-icon fas fa-cog"></i>
            <p>Zoom <?php echo lang('settings'); ?></p>
        </a>
    </li>

<?php } ?>

<?php if ($this->ion_auth->in_group(array('Nurse', 'Accountant', 'Pharmacist', 'Doctor', 'Laboratorist', 'Receptionist'))) { ?>

    <?php if (in_array('file', $this->modules)) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="#">
                <i class="text-secondary nav-icon far fa-clock"></i>
                <p><?php echo lang('file_manager'); ?><i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><a class="nav-link text-white" href="file"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('all'); ?> <?php echo lang('file'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="file/addNewView"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('add_file'); ?></p>
                    </a></li>
            </ul>
        </li>
    <?php } ?>
<?php } ?>

<?php if ($this->ion_auth->in_group(array('Laboratorist'))) { ?>
    <?php if (in_array('donor', $this->modules)) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="#">
                <i class="text-secondary nav-icon fas fa-hand-holding-water"></i>
                <p><?php echo lang('donor') ?><i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><a class="nav-link text-white" href="donor"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('donor_list'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="donor/addDonorView"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('add_donor'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="donor/bloodBank"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('blood_bank'); ?></p>
                    </a></li>
            </ul>
        </li>
    <?php } ?>
<?php } ?>
