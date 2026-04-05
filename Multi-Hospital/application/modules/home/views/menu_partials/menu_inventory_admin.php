<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group(array('admin'))) { ?>
        <?php $render_sidebar_section('hospital_operations', lang('hospital_operations')); ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="#">
                <i class="text-secondary nav-icon fas fa-boxes"></i>
                <p><?php echo lang('inventory'); ?><i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><a class="nav-link text-white" href="inventory"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('inventory_dashboard'); ?></p>
                    </a></li>
                                       <li class="nav-item"><a class="nav-link text-white" href="inventory/items"><i class="text-secondary nav-icon far fa-circle"></i>
                               <p><?php echo lang('inventory_items'); ?></p>
                           </a></li>
                       <li class="nav-item"><a class="nav-link text-white" href="inventory/categories"><i class="text-secondary nav-icon far fa-circle"></i>
                               <p><?php echo lang('inventory_categories'); ?></p>
                           </a></li>
                       <li class="nav-item"><a class="nav-link text-white" href="inventory/low_stock"><i class="text-secondary nav-icon far fa-circle"></i>
                               <p><?php echo lang('low_stock_items'); ?></p>
                           </a></li>
                       <li class="nav-item"><a class="nav-link text-white" href="inventory/supplier"><i class="text-secondary nav-icon far fa-circle"></i>
                               <p><?php echo lang('suppliers'); ?></p>
                           </a></li>
                       <li class="nav-item"><a class="nav-link text-white" href="inventory/purchase"><i class="text-secondary nav-icon far fa-circle"></i>
                               <p><?php echo lang('purchase_orders'); ?></p>
                           </a></li>
                       <li class="nav-item"><a class="nav-link text-white" href="inventory/usage"><i class="text-secondary nav-icon far fa-circle"></i>
                               <p><?php echo lang('usage_logs'); ?></p>
                           </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="inventory/reports"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('inventory_reports'); ?></p>
                    </a></li>
            </ul>
        </li>
<?php } ?>
