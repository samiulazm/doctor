<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if (!$this->ion_auth->in_group('superadmin')) { ?>
    <?php $render_sidebar_section('communication', lang('communication')); ?>
    <li class="nav-item">
        <a class="nav-link text-white" href="#">
            <i class="text-secondary nav-icon fas fa-comments"></i>
            <p>Communication<i class="right fas fa-angle-left"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <?php if ($this->ion_auth->in_group(array('Accountant', 'Receptionist', 'Nurse', 'Laboratorist', 'Pharmacist', 'Doctor'))) { ?>
                <?php if (in_array('notice', $this->modules)) { ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="notice">
                            <i class="text-secondary nav-icon fas fa-bell"></i>
                            <p><?php echo lang('notice'); ?></p>
                        </a>
                    </li>
                <?php } ?>
            <?php } ?>

            <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                <?php if (in_array('notice', $this->modules)) { ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="notice">
                            <i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('notice'); ?></p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="notice/addNewView">
                            <i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('add_new'); ?></p>
                        </a>
                    </li>
                <?php } ?>
            <?php } ?>

            <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                <?php if (in_array('email', $this->modules)) { ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="email/autoEmailTemplate">
                            <i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('autoemailtemplate'); ?></p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="email/sendView">
                            <i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('new'); ?> <?php echo lang('email'); ?></p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="email/sent">
                            <i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('sent'); ?> <?php echo lang('email'); ?></p>
                        </a>
                    </li>
                    <?php if ($this->ion_auth->in_group(array('admin'))) {
                        $email_id = null;
                        $mail_setting = $this->email_model->getHospitalEmailSettings();
                        foreach ($mail_setting as $email_set) {
                            if ($email_set->type == 'Smtp') {
                                $email_id = $email_set->id;
                            }
                        }
                    ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="email/settings?id=<?php echo (int) $email_id; ?>">
                                <i class="text-secondary nav-icon far fa-circle"></i>
                                <p><?php echo lang('email'); ?> <?php echo lang('settings'); ?></p>
                            </a>
                        </li>
                    <?php } ?>
                <?php } ?>
            <?php } ?>

            <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                <?php if (in_array('sms', $this->modules)) { ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="sms/autoSMSTemplate">
                            <i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('autosmstemplate'); ?></p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="sms/sendView">
                            <i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('write_message'); ?></p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="sms/sent">
                            <i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('sent_messages'); ?></p>
                        </a>
                    </li>
                    <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="sms">
                                <i class="text-secondary nav-icon far fa-circle"></i>
                                <p><?php echo lang('sms_settings'); ?></p>
                            </a>
                        </li>
                    <?php } ?>
                <?php } ?>
            <?php } ?>

            <?php if (!$this->ion_auth->in_group(array('admin', 'Patient', 'superadmin'))) { ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="email/sendView">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('new'); ?> <?php echo lang('email'); ?></p>
                    </a>
                </li>
            <?php } ?>

            <?php if ($this->ion_auth->in_group(array('admin', 'Nurse', 'Accountant', 'Pharmacist', 'Doctor', 'Laboratorist', 'Receptionist'))) { ?>
                <?php if (in_array('chat', $this->modules)) { ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="chat">
                            <i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('chat'); ?></p>
                            <p id="chatCount">0</p>
                        </a>
                    </li>
                    <script src="common/js/jquery.js"></script>
                    <script src="common/extranal/js/chat.js"></script>
                <?php } ?>
            <?php } ?>
        </ul>
    </li>
<?php } ?>
