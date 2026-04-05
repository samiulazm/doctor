<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group(array('admin', 'Pharmacist'))) { ?>
    <?php if (in_array('pharmacy', $this->modules)) { ?>
        <?php $render_sidebar_section('hospital_operations', lang('hospital_operations')); ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="#">
                <i class="text-secondary nav-icon fas fa-capsules"></i>
                <p><?php echo lang('pharmacy'); ?><i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <?php if (!$this->ion_auth->in_group(array('Pharmacist'))) { ?>
                    <li class="nav-item"><a class="nav-link text-white" href="finance/pharmacy/home"><i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('dashboard'); ?></p>
                        </a></li>
                <?php } ?>
                <li class="nav-item"><a class="nav-link text-white" href="finance/pharmacy/payment"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('sales'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="finance/pharmacy/addPaymentViewEnhanced"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('add_new_sale'); ?> </p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="finance/pharmacy/expense"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('expense'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="finance/pharmacy/addExpenseView"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('add_expense'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="finance/pharmacy/expenseCategory"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('expense_categories'); ?></p>
                    </a></li>
                <?php if ($this->ion_auth->in_group(array('admin', 'Pharmacist'))) { ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#">
                            <i class="text-secondary nav-icon far fa-circle"></i>
                            <p><?php echo lang('reports'); ?><i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a class="nav-link text-white" href="finance/pharmacy/financialReport"><i class="text-secondary nav-icon far fa-circle"></i>
                                    <p><?php echo lang('pharmacy'); ?> <?php echo lang('report'); ?></p>
                                </a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="finance/pharmacy/monthly"><i class="text-secondary nav-icon far fa-circle"></i>
                                    <p><?php echo lang('monthly_sales'); ?></p>
                                </a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="finance/pharmacy/daily"><i class="text-secondary nav-icon far fa-circle"></i>
                                    <p><?php echo lang('daily_sales'); ?></p>
                                </a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="finance/pharmacy/monthlyExpense"><i class="text-secondary nav-icon far fa-circle"></i>
                                    <p><?php echo lang('monthly_expense'); ?></p>
                                </a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="finance/pharmacy/dailyExpense"><i class="text-secondary nav-icon far fa-circle"></i>
                                    <p><?php echo lang('daily_expense'); ?></p>
                                </a></li>

                        </ul>
                    </li>
                <?php } ?>
            </ul>
        </li>
    <?php } ?>
<?php } ?>
