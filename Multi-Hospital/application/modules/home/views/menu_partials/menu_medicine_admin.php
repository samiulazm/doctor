<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group(array('admin'))) { ?>
    <?php if (in_array('medicine', $this->modules)) { ?>
        <?php $render_sidebar_section('hospital_operations', lang('hospital_operations')); ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="#">
                <i class="text-secondary nav-icon fas  fa-medkit"></i>
                <p><?php echo lang('medicine'); ?><i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><a class="nav-link text-white" href="medicine"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('medicine_list'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="medicine/addMedicineView"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('add_medicine'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="medicine/medicineCategory"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('medicine_category'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="medicine/addCategoryView"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('add_medicine_category'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="medicine/medicineStockAlert"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('medicine_stock_alert'); ?></p>
                    </a></li>
                
                <!-- Supplier Management -->
                <li class="nav-item"><a class="nav-link text-white" href="medicine/suppliers"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p>Medicine Suppliers</p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="medicine/addSupplierView"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p>Add Supplier</p>
                    </a></li>

                <!-- Purchase Management -->
                <li class="nav-item"><a class="nav-link text-white" href="medicine/purchases"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p>Purchase Orders</p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="medicine/addPurchaseView"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p>Create Purchase Order</p>
                    </a></li>

                <!-- Batch Management -->
                <li class="nav-item"><a class="nav-link text-white" href="medicine/batches"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p>Medicine Batches</p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="medicine/expiringMedicines"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p>Expiring Medicines</p>
                    </a></li>

            </ul>
        </li>
    <?php } ?>
<?php } ?>
