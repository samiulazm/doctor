<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
if (!isset($nurse)) {
    $nurse = (object) array();
}
$t = !empty($nurse->id) ? lang('edit_nurse') : lang('add_nurse');
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => $t,
        'icon' => 'fas fa-user-nurse text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('nurse'), 'url' => 'nurse'),
            array('label' => $t, 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo $t; ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <?php echo validation_errors(); ?>
                            <?php echo $this->session->flashdata('feedback'); ?>
                            <form role="form" action="nurse/addNew" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                                <div class="form-group">
                                    <label><?php echo lang('name'); ?> &ast;</label>
                                    <input type="text" class="form-control form-control-lg" name="name" value="<?php
                                    if (!empty($setval)) {
                                        echo set_value('name');
                                    }
                                    if (!empty($nurse->name)) {
                                        echo html_escape($nurse->name);
                                    }
                                    ?>">
                                </div>
                                <div class="form-group">
                                    <label><?php echo lang('email'); ?> &ast;</label>
                                    <input type="email" class="form-control form-control-lg" name="email" value="<?php
                                    if (!empty($setval)) {
                                        echo set_value('email');
                                    }
                                    if (!empty($nurse->email)) {
                                        echo html_escape($nurse->email);
                                    }
                                    ?>">
                                </div>
                                <div class="form-group">
                                    <label><?php echo lang('password'); ?><?php if (empty($nurse->email)) { ?> &ast; <?php } ?></label>
                                    <input type="password" class="form-control form-control-lg" name="password" placeholder="********" <?php if (empty($nurse->email)) { ?> required="" <?php } ?>>
                                </div>
                                <div class="form-group">
                                    <label><?php echo lang('address'); ?> &ast;</label>
                                    <input type="text" class="form-control form-control-lg" name="address" value="<?php
                                    if (!empty($setval)) {
                                        echo set_value('address');
                                    }
                                    if (!empty($nurse->address)) {
                                        echo html_escape($nurse->address);
                                    }
                                    ?>">
                                </div>
                                <div class="form-group">
                                    <label><?php echo lang('phone'); ?> &ast;</label>
                                    <input type="text" class="form-control form-control-lg" name="phone" value="<?php
                                    if (!empty($setval)) {
                                        echo set_value('phone');
                                    }
                                    if (!empty($nurse->phone)) {
                                        echo html_escape($nurse->phone);
                                    }
                                    ?>">
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label><?php echo lang('signature'); ?> &ast; </label>
                                        <div class="fileupload fileupload-new" data-provides="fileupload">
                                            <div class="fileupload-new thumbnail img_class fileupload-preview fileupload-exists thumbnail img_thumb">
                                                <img src="<?php
                                                if (!empty($nurse->signature)) {
                                                    echo html_escape($nurse->signature);
                                                }
                                                ?>" height="100" alt="">
                                            </div>
                                            <div>
                                                <span class="btn btn-white btn-file">
                                                    <span class="btn fileupload-new badge badge-secondary"><i class="fa fa-paper-clip"></i> <?php echo lang('select_image'); ?></span>
                                                    <input type="file" class="default" name="signature" />
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="control-label"><?php echo lang('image'); ?> </label>
                                        <div class="fileupload fileupload-new" data-provides="fileupload">
                                            <div class="fileupload-new thumbnail img_class fileupload-preview fileupload-exists thumbnail img_thumb">
                                                <img src="<?php
                                                if (!empty($nurse->img_url)) {
                                                    echo html_escape($nurse->img_url);
                                                }
                                                ?>" height="100" alt="">
                                            </div>
                                            <div>
                                                <span class="btn btn-white btn-file">
                                                    <span class="btn fileupload-new badge badge-secondary"><i class="fa fa-paper-clip"></i> <?php echo lang('select_image'); ?></span>
                                                    <input type="file" class="default" name="img_url" />
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label><?php echo lang('profile'); ?> &ast; </label>
                                    <textarea class="form-control ckeditor" id="editor1" name="profile" rows="10" cols="20"><?php
                                    if (!empty($setval)) {
                                        echo set_value('profile');
                                    }
                                    if (!empty($nurse->profile)) {
                                        echo $nurse->profile;
                                    }
                                    ?></textarea>
                                </div>

                                <input type="hidden" name="id" value="<?php
                                if (!empty($nurse->id)) {
                                    echo (int) $nurse->id;
                                }
                                ?>">
                                <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block"><?php echo lang('submit'); ?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
</script>
<script src="common/assets/tinymce/tinymce.min.js"></script>
<script src="common/extranal/js/nurse.js"></script>
