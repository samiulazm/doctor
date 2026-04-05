<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group(array('superadmin'))) { ?>
    <?php if (in_array('email', $this->super_modules)) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="#">
                <i class="text-secondary nav-icon far fa-envelope"></i>
                <p><?php echo lang('email'); ?><i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><a class="nav-link text-white" href="email/superadminSendView"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('new'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="email/sent"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('sent'); ?></p>
                    </a></li>

                <li class="nav-item"><a class="nav-link text-white" href="email/emailSettings"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('settings'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="email/contactEmailSettings"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('contact'); ?> <?php echo lang('email'); ?></p>
                    </a></li>
            </ul>
        </li>
    <?php } ?>
<?php } ?>
