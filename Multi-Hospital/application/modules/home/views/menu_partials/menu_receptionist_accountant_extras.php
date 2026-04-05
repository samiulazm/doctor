<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group('Receptionist')) { ?>
    <?php if (in_array('appointment', $this->modules)) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="appointment/calendar">
                <i class="text-secondary nav-icon far fa-calendar"></i>
                <p> <?php echo lang('calendar'); ?> </p>
            </a>
        </li>
    <?php } ?>
    <?php if (in_array('finance', $this->modules)) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="#">
                <i class="text-secondary nav-icon fas fa-money-check"></i>
                <p><?php echo lang('financial_activities'); ?><i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><a class="nav-link text-white" href="finance/payment"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('payments'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="finance/addPaymentView"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('add_payment'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="finance/dueCollection"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('due_collection'); ?></p>
                    </a></li>
            </ul>
        </li>
    <?php } ?>
<?php } ?>

<?php
if ($this->ion_auth->in_group(array('Accountant', 'Receptionist'))) {
?>
    <?php if (in_array('finance', $this->modules)) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="finance/UserActivityReport">
                <i class="text-secondary nav-icon fas fa-file"></i>
                <p><?php echo lang('user_activity_report'); ?></p>
            </a>
        </li>
    <?php } ?>
<?php
}
?>
