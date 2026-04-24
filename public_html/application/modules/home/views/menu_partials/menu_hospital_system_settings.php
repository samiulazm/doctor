<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if ($this->ion_auth->in_group(array('admin'))) { ?>
    <?php $render_sidebar_section('system_settings', lang('system_and_settings')); ?>
    <li class="nav-item">
        <a class="nav-link text-white" href="#">
            <i class="text-secondary nav-icon fas fa-cog"></i>
            <p>Settings<i class="right fas fa-angle-left"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item"><a class="nav-link text-white" href="settings"><i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('system_settings'); ?></p>
                </a></li>
            <li class="nav-item"><a class="nav-link text-white" href="pgateway"><i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('payment_gateway'); ?></p>
                </a></li>
            <li class="nav-item"><a class="nav-link text-white" href="settings/chatgpt"><i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('ai_settings'); ?></p>
                </a></li>
            <li class="nav-item"><a class="nav-link text-white" href="settings/language"><i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('language'); ?></p>
                </a></li>
            <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                <li class="nav-item"><a class="nav-link text-white" href="import"><i class="text-secondary nav-icon far fa-circle"></i>
                        <p><?php echo lang('bulk'); ?> <?php echo lang('import'); ?></p>
                    </a></li>
            <?php } ?>
            <li class="nav-item"><a class="nav-link text-white" href="transactionLogs"><i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('transaction_logs'); ?></p>
                </a></li>
            <li class="nav-item"><a class="nav-link text-white" href="logs"><i class="text-secondary nav-icon far fa-circle"></i>
                    <p><?php echo lang('user'); ?> <?php echo lang('login_logs'); ?></p>
                </a></li>
            <?php if ($this->config->item('audit_ui_enabled')) { ?>
                <li class="nav-item"><a class="nav-link text-white" href="auditTrail"><i class="text-secondary nav-icon fas fa-clipboard-list"></i>
                        <p><?php echo lang('audit_trail'); ?></p>
                    </a></li>
            <?php } ?>
            <li class="nav-item">
                <a class="nav-link text-white" href="settings/subscription">
                    <i class="text-secondary nav-icon far fa-user"></i>
                    <p> <?php echo lang('subscription'); ?> </p>
                </a>
            </li>
            <?php if (in_array('file', $this->modules)) { ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">
                        <i class="text-secondary nav-icon far fa-clock"></i>
                        <p><?php echo lang('file_manager'); ?><i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item"><a class="nav-link text-white" href="file"><i class="text-secondary nav-icon far fa-circle"></i>
                                <p><?php echo lang('all'); ?> <?php echo lang('file'); ?></p>
                            </a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="file/addNewView"><i class="text-secondary nav-icon far fa-circle"></i>
                                <p><?php echo lang('add_file'); ?></p>
                            </a></li>
                    </ul>
                </li>
            <?php } ?>
            <li class="nav-item">
                <a class="nav-link text-white" target="_blank" href="http://support.codearistos.net/help-center/articles/10/11/27/introduction">
                    <i class="text-secondary nav-icon fas fa-question-circle"></i>
                    <p><?php echo lang('help_center'); ?></p>
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link text-white" href="mailto:rizvi.mahmud.plabon@gmail.com">
                    <i class="text-secondary nav-icon fas fa-envelope"></i>
                    <p><?php echo lang('contact_us'); ?></p>
                </a>
            </li>
        </ul>
    </li>



<?php } ?>
