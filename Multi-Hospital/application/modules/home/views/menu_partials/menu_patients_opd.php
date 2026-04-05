<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if (!$this->ion_auth->in_group('superadmin')) { ?>
    <?php $render_sidebar_section('clinical_care', lang('clinical_care')); ?>
    <li class="nav-item">
        <a class="nav-link text-white" href="#">
            <i class="text-secondary nav-icon fas fa-user"></i>
            <p>Patients &amp; OPD<i class="right fas fa-angle-left"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <?php if ($this->ion_auth->in_group(array('Patient'))) { ?>
                <?php if (in_array('patient', $this->modules)) { ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="patient/myCaseList">
                            <i class="text-secondary nav-icon fas fa-file-medical"></i>
                            <p> <?php echo lang('cases'); ?> </p>
                        </a>
                    </li>
                <?php } ?>
                <?php if (in_array('prescription', $this->modules)) { ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="patient/myPrescription">
                            <i class="text-secondary nav-icon fas fa-prescription"></i>
                            <p> <?php echo lang('prescription'); ?> </p>
                        </a>
                    </li>
                <?php } ?>
                <?php if (in_array('patient', $this->modules)) { ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="patient/myDocuments">
                            <i class="text-secondary nav-icon fas fa-file-upload"></i>
                            <p> <?php echo lang('documents'); ?> </p>
                        </a>
                    </li>
                <?php } ?>
                <?php if (in_array('finance', $this->modules)) { ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="patient/myPaymentHistory">
                            <i class="text-secondary nav-icon far fa-money-bill-alt"></i>
                            <p> <?php echo lang('payment'); ?> </p>
                        </a>
                    </li>
                <?php } ?>
            <?php } ?>

            <?php if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Nurse', 'Doctor', 'Laboratorist', 'Receptionist'))) { ?>
                <?php if (in_array('patient', $this->modules)) { ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="patient">
                            <i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('patient_list'); ?></p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="patient/addnewview">
                            <i class="text-secondary nav-icon far fa-circle"></i>
                            <p> <?php echo lang('add_new'); ?></p>
                        </a>
                    </li>
                    <?php if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Doctor', 'Receptionist'))) { ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="patient/patientPayments">
                                <i class="text-secondary nav-icon far fa-circle"></i>
                                <p><?php echo lang('payments'); ?></p>
                            </a>
                        </li>
                    <?php } ?>
                <?php } ?>
            <?php } ?>

            <?php if ($this->ion_auth->in_group(array('admin', 'Nurse', 'Doctor', 'Laboratorist'))) { ?>
                <?php if (in_array('patient', $this->modules)) { ?>
                    <?php if (!$this->ion_auth->in_group(array('Accountant', 'Receptionist'))) { ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="patient/caseList">
                                <i class="text-secondary nav-icon far fa-circle"></i>
                                <p><?php echo lang('All The Cases'); ?> </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="symptom">
                                <i class="text-secondary nav-icon far fa-circle"></i>
                                <p><?php echo lang('Symptoms'); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="diagnosis">
                                <i class="text-secondary nav-icon far fa-circle"></i>
                                <p><?php echo lang('diagnosis'); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="treatment">
                                <i class="text-secondary nav-icon far fa-circle"></i>
                                <p><?php echo lang('treatment'); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="advice">
                                <i class="text-secondary nav-icon far fa-circle"></i>
                                <p><?php echo lang('advice'); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="patient/documents">
                                <i class="text-secondary nav-icon far fa-circle"></i>
                                <p><?php echo lang('documents'); ?></p>
                            </a>
                        </li>
                    <?php } ?>
                <?php } ?>
            <?php } ?>


            <?php if ($this->ion_auth->in_group(array('Doctor'))) { ?>
                <?php if (in_array('prescription', $this->modules)) { ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="prescription">
                            <i class="text-secondary nav-icon fas fa-prescription"></i>
                            <p><?php echo lang('prescription'); ?></p>
                        </a>
                    </li>
                <?php } ?>
            <?php } ?>
        </ul>
    </li>
<?php } ?>
