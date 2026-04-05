<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group('superadmin')) { ?>
    <?php $render_sidebar_section('system_settings', lang('system_and_settings')); ?>
    <?php if (in_array('superadmin', $this->super_modules)) { ?>
        <li class=" nav-item">
            <a class="nav-link text-white" href="superadmin">
                <i class="text-secondary nav-icon fas fa-users"></i>
                <p><?php echo lang('superadmin'); ?></p>
            </a>
        </li>
    <?php } ?>

    <li class="nav-item">
        <a class="nav-link text-white" href="#">
            <i class="text-secondary nav-icon fas fa-sitemap"></i>
            <p><?php echo lang('subscription'); ?><i class="right fas fa-angle-left"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <?php if (in_array('hospital', $this->super_modules)) { ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="hospital">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('all_hospitals'); ?></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="hospital/addNewView">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('create_new_hospital'); ?></p>
                    </a>
                </li>
            <?php } ?>
            <?php if (in_array('package', $this->super_modules)) { ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="hospital/package">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('packages'); ?></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="hospital/package/addNewView">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('add_new_package'); ?></p>
                    </a>
                </li>
            <?php } ?>
            <?php if (in_array('request', $this->super_modules)) { ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="request">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('subscription'); ?> <?php echo lang('requests'); ?></p>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </li>

    <li class="nav-item">
        <a class="nav-link text-white" href="#">
            <i class="text-secondary nav-icon fas fa-chart-line"></i>
            <p><?php echo lang('report-h'); ?><i class="right fas fa-angle-left"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <?php if (in_array('systems', $this->super_modules)) { ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="systems/activeHospitals">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('active_hospitals'); ?></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="systems/inactiveHospitals">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('inactive_hospitals'); ?></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="systems/expiredHospitals">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('expired'); ?></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="systems/registeredPatient">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('registered_patient'); ?></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="systems/registeredDoctor">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('registered_doctor'); ?></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="hospital/reportSubscription">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('subscription_report'); ?></p>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </li>

    <li class="nav-item">
        <a class="nav-link text-white" href="#">
            <i class="text-secondary nav-icon fas fa-globe"></i>
            <p><?php echo lang('website_management'); ?><i class="right fas fa-angle-left"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a class="nav-link text-white" href="frontend" target="_blank">
                    <i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('visit_site'); ?></p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="frontend/settings">
                    <i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('website_settings'); ?></p>
                </a>
            </li>
            <?php if (in_array('slide', $this->super_modules)) { ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="slide">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('slides'); ?></p>
                    </a>
                </li>
            <?php } ?>
            <?php if (in_array('service', $this->super_modules)) { ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="service">
                        <i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('reviews'); ?></p>
                    </a>
                </li>
            <?php } ?>
            <li class="nav-item">
                <a class="nav-link text-white" href="faq">
                    <i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('faqs'); ?></p>
                </a>
            </li>
        </ul>
    </li>

<?php } ?>
