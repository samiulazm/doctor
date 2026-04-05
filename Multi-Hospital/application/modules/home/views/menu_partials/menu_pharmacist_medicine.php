<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group('Pharmacist')) { ?>
    <?php if (in_array('medicine', $this->modules)) { ?>
        <li class="nav-item">
            <a class="nav-link text-white" href="medicine">
                <i class="text-secondary nav-icon fas fa-medkit"></i>
                <p> <?php echo lang('medicine_list'); ?> </p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="medicine/addMedicineView">
                <i class="text-secondary nav-icon fas fa-plus-circle"></i>
                <p> <?php echo lang('add_medicine'); ?> </p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="medicine/medicineCategory">
                <i class="text-secondary nav-icon fas fa-medkit"></i>
                <p> <?php echo lang('medicine_category'); ?> </p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="medicine/addCategoryView">
                <i class="text-secondary nav-icon fas fa-plus-circle"></i>
                <p> <?php echo lang('add_medicine_category'); ?> </p>
            </a>
        </li>
        
        <!-- Supplier Management for Pharmacist -->
        <li class="nav-item">
            <a class="nav-link text-white" href="medicine/suppliers">
                <i class="text-secondary nav-icon fas fa-truck"></i>
                <p>Medicine Suppliers</p>
            </a>
        </li>
        
        <!-- Purchase Management for Pharmacist -->
        <li class="nav-item">
            <a class="nav-link text-white" href="medicine/purchases">
                <i class="text-secondary nav-icon fas fa-shopping-cart"></i>
                <p>Purchase Orders</p>
            </a>
        </li>
        
        <!-- Batch Management for Pharmacist -->
        <li class="nav-item">
            <a class="nav-link text-white" href="medicine/batches">
                <i class="text-secondary nav-icon fas fa-boxes"></i>
                <p>Medicine Batches</p>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link text-white" href="medicine/expiringMedicines">
                <i class="text-secondary nav-icon fas fa-exclamation-triangle"></i>
                <p>Expiring Medicines</p>
            </a>
        </li>
    <?php } ?>
<?php } ?>
