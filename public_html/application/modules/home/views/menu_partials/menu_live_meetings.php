<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if (!$this->ion_auth->in_group('superadmin') && in_array('appointment', $this->modules)) { ?>
    <li class="nav-item">
        <a class="nav-link text-white" href="#">
            <i class="text-secondary nav-icon fas fa-headphones"></i>
            <p><?php echo lang('live'); ?> <?php echo lang('meetings'); ?><i class="right fas fa-angle-left"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <?php if (!$this->ion_auth->in_group(array('Patient'))) { ?>
                <li class="nav-item"><a class="nav-link text-white" href="meeting/addNewView"><i class="text-secondary nav-icon fas fa-plus-circle"></i>
                        <p><?php echo lang('create'); ?> <?php echo lang('meeting'); ?></p>
                    </a></li>
            <?php } ?>
            <li class="nav-item"><a class="nav-link text-white" href="meeting"><i class="text-secondary nav-icon far fa-video"></i>
                    <p><?php echo lang('live'); ?> <?php echo lang('now'); ?></p>
                </a></li>
            <li class="nav-item"><a class="nav-link text-white" href="meeting/upcoming"><i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('upcoming'); ?> <?php echo lang('meetings'); ?></p>
                </a></li>
            <li class="nav-item"><a class="nav-link text-white" href="meeting/previous"><i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('previous'); ?> <?php echo lang('meetings'); ?></p>
                </a></li>
        </ul>
    </li>
<?php } ?>
