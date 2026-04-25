<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
$smtp_local = '';
$smtp_domain = '';
if (!empty($settings) && isset($settings->type) && $settings->type == 'Smtp' && !empty($settings->user)) {
    $parts = explode('@', $settings->user, 2);
    $smtp_local = isset($parts[0]) ? $parts[0] : '';
    $smtp_domain = isset($parts[1]) ? $parts[1] : '';
}
$gateway_id = !empty($settings->id) ? (int) $settings->id : 0;
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('email_settings'),
        'icon' => 'fas fa-envelope text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('email'), 'url' => 'email/emailSettings'),
            array('label' => lang('email_settings'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase">
                                <?php echo lang('All the email settings'); ?>
                            </h3>
                            <?php if (!empty($settings) && !empty($settings->type)) { ?>
                                <p class="small text-muted mb-0 mt-2">
                                    <span class="badge badge-info"><?php echo htmlspecialchars($settings->type, ENT_QUOTES, 'UTF-8'); ?></span>
                                </p>
                            <?php } ?>
                        </div>
                        <div class="card-body p-4">
                            <?php echo validation_errors(); ?>
                            <?php echo $this->session->flashdata('feedback'); ?>

                            <?php if (empty($settings) || (isset($settings->type) && $settings->type != 'Domain Email' && $settings->type != 'Smtp')) { ?>
                                <div class="alert alert-warning mb-0"><?php echo lang('error'); ?></div>
                            <?php } else { ?>

                            <form role="form" action="email/addNewSettings" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">

                                <?php if ($settings->type == 'Domain Email') { ?>
                                    <div class="form-group">
                                        <label for="admin_email_field"><?php echo lang('admin'); ?> <?php echo lang('email'); ?> <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-lg" name="email" id="admin_email_field" value="<?php echo !empty($settings->admin_email) ? htmlspecialchars($settings->admin_email, ENT_QUOTES, 'UTF-8') : ''; ?>" placeholder="<?php echo lang('email'); ?>">
                                    </div>
                                    <div class="alert alert-light border small mb-4">
                                        <?php echo lang('email_settings_instruction_1'); ?><br>
                                        <?php echo lang('email_settings_instruction_2'); ?>
                                    </div>
                                <?php } ?>

                                <?php if ($settings->type == 'Smtp') { ?>
                                    <div class="form-group emailSelectCompany">
                                        <label for="emailCompany"><?php echo lang('email'); ?> <?php echo lang('company'); ?> <span class="text-danger">*</span></label>
                                        <select class="form-control form-control-lg pos_select" id="emailCompany" name="email_company" required>
                                            <option value=""><?php echo lang('select'); ?></option>
                                            <option value="gmail" <?php echo (!empty($settings->mail_provider) && $settings->mail_provider == 'gmail') ? 'selected' : ''; ?>><?php echo lang('gmail'); ?></option>
                                            <option value="yahoo" <?php echo (!empty($settings->mail_provider) && $settings->mail_provider == 'yahoo') ? 'selected' : ''; ?>><?php echo lang('yahoo_mail'); ?></option>
                                            <option value="zoho" <?php echo (!empty($settings->mail_provider) && $settings->mail_provider == 'zoho') ? 'selected' : ''; ?>><?php echo lang('zoho_mail'); ?></option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="emailAddress"><?php echo lang('email'); ?> <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-lg">
                                            <input type="text" class="form-control" name="user" id="emailAddress" pattern="[^@,]+" value="<?php echo htmlspecialchars($smtp_local, ENT_QUOTES, 'UTF-8'); ?>" placeholder="user" required autocomplete="username" aria-describedby="mailExtension">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="mailExtension"><?php echo $smtp_domain !== '' ? '@' . htmlspecialchars($smtp_domain, ENT_QUOTES, 'UTF-8') : ''; ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="smtpPassword"><?php echo lang('email'); ?> <?php echo lang('app'); ?> <?php echo lang('password'); ?> <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control form-control-lg" name="password" id="smtpPassword" value="<?php
                                        if (!empty($settings->password)) {
                                            $p = base64_decode($settings->password, true);
                                            echo htmlspecialchars($p !== false ? $p : '', ENT_QUOTES, 'UTF-8');
                                        }
                                        ?>" placeholder="<?php echo lang('email') . ' ' . lang('password'); ?>" required autocomplete="current-password">
                                    </div>
                                <?php } ?>

                                <input type="hidden" name="id" value="<?php echo $gateway_id; ?>">
                                <input type="hidden" name="type" value="<?php echo !empty($settings->type) ? htmlspecialchars($settings->type, ENT_QUOTES, 'UTF-8') : ''; ?>">

                                <div class="alert alert-light border small mb-4">
                                    <?php echo lang('yahoo_mail_password_instruction1'); ?><br>
                                    &gt;&gt;<?php echo lang('yahoo_mail_password_instruction2'); ?><br>
                                    &gt;&gt; <?php echo lang('yahoo_mail_password_instruction3'); ?><br>
                                    <?php echo lang('yahoo_mail_password_instruction4'); ?>
                                </div>

                                <div class="d-flex flex-wrap justify-content-between align-items-center">
                                    <a href="email/emailSettings" class="btn btn-outline-secondary"><?php echo lang('cancel'); ?></a>
                                    <button type="submit" name="submit" class="btn btn-primary px-4"><?php echo lang('submit'); ?></button>
                                </div>
                            </form>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="common/js/codearistos.min.js"></script>
<script src="common/extranal/js/email/settings.js"></script>
