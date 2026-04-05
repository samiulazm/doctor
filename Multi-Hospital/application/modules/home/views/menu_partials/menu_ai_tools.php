<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group(array('admin', 'Doctor'))) { ?>
    <?php $render_sidebar_section('clinical_care', lang('clinical_care')); ?>
    <li class="nav-item">
        <a class="nav-link text-white" href="#">
            <i class="text-secondary nav-icon fas fa-robot"></i>
            <p>AI Tools<i class="right fas fa-angle-left"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a class="nav-link text-white" href="ai_patient_overview">
                    <i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('ai_patient_overview'); ?></p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="ai_image_analysis">
                    <i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('ai_image_analysis'); ?></p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="treatment_plan">
                    <i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('ai_treatment_plan'); ?></p>
                </a>
            </li>
        </ul>
    </li>
<?php } ?>
