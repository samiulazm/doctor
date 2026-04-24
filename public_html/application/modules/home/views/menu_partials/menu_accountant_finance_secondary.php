<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group('Accountant')) { ?>
    <?php if (in_array('finance', $this->modules)) { ?>
        <?php $render_sidebar_section('hospital_operations_secondary', lang('hospital_operations')); ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="#">
                <i class="text-secondary nav-icon far fa-money-bill-alt"></i>
                <p>Billing &amp; Finance<i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a class="nav-link text-white" href="finance/payment">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p> <?php echo lang('invoices'); ?> </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="finance/addPaymentView">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p> <?php echo lang('add_new_invoice'); ?> </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="finance/dueCollection"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p> <?php echo lang('due_collection'); ?> </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="finance/paymentCategory">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p> <?php echo lang('invoice_items_lab_tests'); ?> </p>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="finance/expense">
                <i class="text-secondary nav-icon fas fa-money-check"></i>
                <p> <?php echo lang('expense'); ?> </p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="finance/addExpenseView">
                <i class="text-secondary nav-icon fas fa-plus-circle"></i>
                <p> <?php echo lang('add_expense'); ?> </p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="finance/expenseCategory">
                <i class="text-secondary nav-icon far fa-edit"></i>
                <p> <?php echo lang('expense_categories'); ?> </p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="finance/doctorsCommission">
                <i class="text-secondary nav-icon far fa-edit"></i>
                <p> <?php echo lang('doctors_commission'); ?> </p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="finance/financialReport">
                <i class="text-secondary nav-icon fas fa-book"></i>
                <p> <?php echo lang('financial_report'); ?> </p>
            </a>
        </li>
    <?php } ?>
<?php } ?>
