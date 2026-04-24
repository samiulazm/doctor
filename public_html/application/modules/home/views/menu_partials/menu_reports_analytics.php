<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group(array('admin', 'Nurse', 'Laboratorist', 'Doctor'))) { ?>
    <?php $render_sidebar_section('hospital_operations', lang('hospital_operations')); ?>
    <li class="nav-item">
        <a class="nav-link text-white" href="#">
            <i class="text-secondary nav-icon fas fa-file-medical-alt"></i>
            <p>Reports &amp; Analytics<i class="right fas fa-angle-left"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                <?php if (in_array('finance', $this->modules)) { ?>
                    <li class="nav-item"><a class="nav-link text-white" href="finance/financialReport"><i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('financial_report'); ?></p>
                        </a></li>
                    <li class="nav-item"> <a class="nav-link text-white" href="finance/AllUserActivityReport"> <i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('user_activity_report'); ?></p>
                        </a></li>
                    <!-- <li class="nav-item"><a class="nav-link text-white" href="finance/insuranceReport"><i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('insurance_report'); ?></p>
                        </a></li> -->
                <?php } ?>
            <?php } ?>
            <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                <?php if (in_array('finance', $this->modules)) { ?>
                    <li class="nav-item"><a class="nav-link text-white" href="finance/doctorsCommission"><i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('doctors_commission'); ?></p>
                        </a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="finance/monthly"><i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('monthly_sales'); ?></p>
                        </a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="finance/daily"><i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('daily_sales'); ?></p>
                        </a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="finance/monthlyExpense"><i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('monthly_expense'); ?></p>
                        </a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="finance/dailyExpense"><i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('daily_expense'); ?></p>
                        </a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="finance/expenseVsIncome"><i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('expense_vs_income'); ?></p>
                        </a></li>
                <?php } ?>
            <?php } ?>
            <?php if (in_array('report', $this->modules)) { ?>
                <li class="nav-item"><a class="nav-link text-white" href="report/birth"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('birth_report'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="report/operation"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('operation_report'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="report/expire"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('expire_report'); ?></p>
                    </a></li>
            <?php } ?>
        </ul>
    </li>
<?php } ?>
