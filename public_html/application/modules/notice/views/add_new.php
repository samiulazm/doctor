<?php
defined('BASEPATH') OR exit('No direct script access allowed');
if (!isset($notice) || !is_object($notice)) {
    $notice = new stdClass();
}
$CI = get_instance();
$is_edit = !empty($notice->id);
$t = $is_edit ? lang('edit_notice') : lang('add_notice');
?>
<link href="common/extranal/css/notice/add_new.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => $t,
        'icon' => 'fas fa-clipboard-list text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('notice'), 'url' => 'notice'),
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
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('notice'); ?> — <?php echo lang('details'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <?php echo validation_errors(); ?>
                            <?php echo $this->session->flashdata('feedback'); ?>

                            <form role="form" action="notice/addNew" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">

                                <div class="form-group">
                                    <label class="font-weight-bold" for="notice_title"><?php echo lang('title'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg" id="notice_title" name="title" value="<?php echo !empty($notice->title) ? html_escape($notice->title) : ''; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold" for="notice_type"><?php echo lang('notice'); ?> <?php echo lang('for'); ?></label>
                                    <select class="form-control form-control-lg js-example-basic-single" id="notice_type" name="type">
                                        <option value="patient" <?php echo (!empty($notice->type) && $notice->type == 'patient') ? 'selected' : ''; ?>><?php echo lang('patient'); ?></option>
                                        <option value="staff" <?php echo (!empty($notice->type) && $notice->type == 'staff') ? 'selected' : ''; ?>><?php echo lang('staff'); ?></option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold" for="editor"><?php echo lang('description'); ?> <span class="text-danger">*</span></label>
                                    <textarea class="ckeditor form-control editor" id="editor" name="description" rows="8" required><?php
                                    if (!empty($notice->description)) {
                                        echo $notice->description;
                                    }
                                    ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold" for="notice_date"><?php echo lang('date'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg default-date-picker" id="notice_date" name="date" readonly onkeypress="return false;" value="<?php
                                    if (!empty($notice->date)) {
                                        echo html_escape(date('m-d-Y', (int) $notice->date));
                                    }
                                    ?>" placeholder="mm-dd-yyyy" required>
                                </div>

                                <input type="hidden" name="id" value="<?php echo !empty($notice->id) ? (int) $notice->id : ''; ?>">

                                <div class="d-flex justify-content-end">
                                    <a href="notice" class="btn btn-outline-secondary"><?php echo lang('cancel'); ?></a>
                                    <button type="submit" name="submit" class="btn btn-primary ml-2 px-4"><?php echo lang('submit'); ?></button>
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
<script src="common/assets/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
</script>
<script src="common/extranal/js/notice.js"></script>
