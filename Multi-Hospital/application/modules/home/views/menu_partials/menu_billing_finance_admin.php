<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group('admin')) { ?>
    <?php if (in_array('finance', $this->modules)) { ?>
        <?php $render_sidebar_section('hospital_operations', lang('hospital_operations')); ?>

        <li class="nav-item">
            <a class="nav-link text-white" href="#">
                <i class="text-secondary nav-icon fas fa-money-check"></i>
                <p>
                    Billing &amp; Finance
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><a class="nav-link text-white" href="finance/addPaymentView"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('new_invoice'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="finance/payment"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('all_invoices'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="finance/draftPayment"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('draft_invoices'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="finance/dueCollection"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('due_collection'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="finance/paymentCategory"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('invoice_items_lab_tests'); ?></p>
                    </a></li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="finance/addPaymentCategoryView">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('new_items_lab_tests'); ?></p>
                    </a>
                </li>
                <!-- <li class="nav-item"><a class="nav-link text-white" href="finance/category"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('payment_categories'); ?></p>
                    </a></li> -->
                <li class="nav-item"><a class="nav-link text-white" href="finance/expense"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('expense'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="finance/addExpenseView"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('add_expense'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="finance/expenseCategory"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('expense_categories'); ?></p>
                    </a></li>
                <!-- <li class="nav-item"><a class="nav-link text-white" href="insurance"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('insurance'); ?></p>
                    </a></li> -->


            </ul>
        </li>
        <!-- 
        <li class="nav-item">
            <a class="nav-link text-white" href="#">
                <i class="text-secondary nav-icon fas fa-file"></i>
                <p><?php echo lang('insurance'); ?><i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><a class="nav-link text-white" href="insurance"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('insurance'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="finance/insuranceReport"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('insurance_report'); ?></p>
                    </a></li>

            </ul>
        </li> -->


    <?php } ?>
<?php } ?>
