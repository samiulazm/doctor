<?php
defined('BASEPATH') or exit('No direct script access allowed');
$CI = get_instance();
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => 'AI ' . lang('settings'),
        'icon' => 'fas fa-robot text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('settings'), 'url' => 'settings'),
            array('label' => 'AI ' . lang('settings'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-7">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-dark font-weight-bold">
                                <i class="fas fa-key mr-2 text-info"></i>
                                OpenAI
                            </h3>
                        </div>
                        <div class="card-body p-4">
                            <?php echo validation_errors(); ?>
                            <?php echo $this->session->flashdata('feedback'); ?>
                            <form role="form" action="settings/chatgptSettings" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">

                                <div class="form-group">
                                    <label for="openaiApiKey">OpenAI API Key <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg" name="api_key" id="openaiApiKey"
                                        value="<?php echo !empty($settings->chatgpt_api_key) ? htmlspecialchars($settings->chatgpt_api_key, ENT_QUOTES, 'UTF-8') : ''; ?>"
                                        placeholder=""
                                        autocomplete="off">
                                </div>

                                <input type="hidden" name="id" value="<?php echo !empty($settings->id) ? (int) $settings->id : ''; ?>">

                                <div class="d-flex flex-wrap align-items-center justify-content-between">
                                    <p class="small text-muted mb-0">
                                        <?php echo lang('login_to_openai_com_and_then_go_to_this_page'); ?>
                                        <a target="_blank" rel="noopener noreferrer" href="https://platform.openai.com/api-keys">https://platform.openai.com/api-keys</a>
                                    </p>
                                    <button type="submit" name="submit" class="btn btn-info mt-3 mt-sm-0">
                                        <i class="fas fa-save mr-1"></i> <?php echo lang('submit'); ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="common/js/codearistos.min.js"></script>
<script src="common/extranal/js/settings/chatgpt.js"></script>
