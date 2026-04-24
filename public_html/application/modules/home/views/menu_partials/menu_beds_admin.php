<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group(array('admin'))) { ?>
    <?php $render_sidebar_section('hospital_operations', lang('hospital_operations')); ?>
    <li class="nav-item">
        <a class="nav-link text-white" href="#">
            <i class="text-secondary nav-icon fas fa-bed"></i>
            <p>Beds &amp; Admissions<i class="right fas fa-angle-left"></i></p>
        </a>
        <?php if (in_array('bed', $this->modules)) { ?>
            <ul class="nav nav-treeview">
                <li class="nav-item"><a class="nav-link text-white" href="bed/bedAllotment"><i class="text-secondary nav-icon fas fa-bed"></i>
                        <p><?php echo lang('all_admissions'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="bed/addAllotmentView"><i class="text-secondary nav-icon fas fa-plus-circle"></i>
                        <p><?php echo lang('add_admission'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="bed"><i class="text-secondary nav-icon fas fa-list"></i>
                        <p><?php echo lang('bed_list'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="bed/addBedView"><i class="text-secondary nav-icon fas fa-plus"></i>
                        <p><?php echo lang('add_bed'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="bed/bedCategory"><i class="text-secondary nav-icon fas fa-th-list"></i>
                        <p><?php echo lang('bed_category'); ?></p>
                    </a></li>
                <li class="nav-item"><a class="nav-link text-white" href="pservice"><i class="text-secondary nav-icon fas fa-paw"></i>
                        <p><?php echo lang('patient'); ?> <?php echo lang('service'); ?></p>
                    </a></li>
            </ul>
        <?php } ?>
    </li>
<?php } ?>
