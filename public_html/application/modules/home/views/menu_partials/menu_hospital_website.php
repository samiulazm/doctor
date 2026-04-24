<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group(array('admin'))) { ?>
    <li class="nav-item">
        <a class="nav-link text-white" href="#">
            <i class="text-secondary nav-icon fas fa-globe"></i>
            <p><?php echo lang('website'); ?><i class="right fas fa-angle-left"></i></p>
        </a>
        <?php

        $hospital_username = $this->db->get_where('hospital', array('id' => $this->session->userdata('hospital_id')))->row()->username;
        if (empty($hospital_username)) {
            $hospital_username = '';
        }

        ?>
        <ul class="nav nav-treeview">
            <li class="nav-item"><a class="nav-link text-white" href='site/<?php echo $hospital_username ?>' target="_blank"><i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('visit_site'); ?></p>
                </a></li>
            <li class="nav-item"><a class="nav-link text-white" href="site/settings"><i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('website_settings'); ?></p>
                </a></li>
            <li class="nav-item"><a class="nav-link text-white" href="site/review"><i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('reviews'); ?></p>
                </a></li>
            <li class="nav-item"><a class="nav-link text-white" href="site/gridsection"><i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('gridsections'); ?></p>
                </a></li>
            <li class="nav-item"><a class="nav-link text-white" href="site/gallery"><i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('gallery'); ?></p>
                </a></li>
            <li class="nav-item"><a class="nav-link text-white" href="site/slide"><i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('slides'); ?></p>
                </a></li>
            <li class="nav-item"><a class="nav-link text-white" href="site/service"><i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('services'); ?></p>
                </a></li>
            <li class="nav-item"><a class="nav-link text-white" href="site/featured"><i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('featured_doctors'); ?></p>
                </a></li>
        </ul>
    </li>


<?php } ?>
