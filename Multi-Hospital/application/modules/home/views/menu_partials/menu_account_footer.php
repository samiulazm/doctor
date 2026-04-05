<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php $render_sidebar_section('account', lang('account')); ?>
<li class="nav-item">
    <a class="nav-link text-white" href="profile">
        <i class="text-secondary nav-icon fas fa-user"></i>
        <p> <?php echo lang('profile'); ?> </p>
    </a>
</li>


<li class="nav-item">
    <a class="nav-link text-white" href="auth/logout">
        <i class="text-secondary nav-icon fas fa-sign-out-alt"></i>
        <p> <?php echo lang('log_out'); ?> </p>
    </a>
</li>




<?php if ($this->ion_auth->in_group(array('superadmin'))) { ?>
    <li class="nav-item">
        <a class="nav-link text-white" target="_blank" href="http://support.codearistos.net/help-center/articles/10/11/27/introduction">
            <i class="text-secondary nav-icon fas fa-question-circle"></i>
            <p><?php echo lang('help_center'); ?></p>
        </a>
    </li>


    <li class="nav-item">
        <a class="nav-link text-white" href="mailto:rizvi.mahmud.plabon@gmail.com">
            <i class="text-secondary nav-icon fas fa-envelope"></i>
            <p><?php echo lang('contact_us'); ?></p>
        </a>
    </li>
<?php } ?>
