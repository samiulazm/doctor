<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<link href="common/extranal/css/notice/notice.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('notice'),
        'icon' => 'fas fa-clipboard-list text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('notice'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                <div class="d-flex flex-wrap justify-content-end mb-3">
                    <a data-toggle="modal" data-target="#myModal" href="#myModal" class="btn btn-sm btn-success">
                        <i class="fa fa-plus mr-1"></i> <?php echo lang('add_new'); ?>
                    </a>
                </div>
            <?php } ?>
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('All the notices'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle text-sm datatables mb-0" id="dt-notice" data-legacy-table="editable-sample" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-uppercase"><?php echo lang('title'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('description'); ?></th>
                                            <th class="text-uppercase text-center"><?php echo lang('notice'); ?> <?php echo lang('for'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('date'); ?></th>
                                            <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                                                <th class="text-uppercase no-print"><?php echo lang('options'); ?></th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($notices as $notice) { ?>
                                            <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                                                <tr>
                                                    <td><?php echo html_escape($notice->title); ?></td>
                                                    <td><?php
                                                        $plain = preg_replace('/\s+/', ' ', trim(strip_tags((string) $notice->description)));
                                                        echo html_escape(strlen($plain) > 160 ? substr($plain, 0, 160) . '…' : $plain);
                                                    ?></td>
                                                    <td class="text-center"><?php echo html_escape($notice->type); ?></td>
                                                    <td><?php
                                                        if (!empty($notice->date)) {
                                                            echo html_escape(date('d-m-Y', (int) $notice->date));
                                                        }
                                                    ?></td>
                                                    <td class="no-print">
                                                        <a type="button" class="btn btn-info btn-sm editbutton" title="<?php echo lang('edit'); ?>" data-id="<?php echo (int) $notice->id; ?>">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <a class="btn btn-danger btn-sm" href="notice/delete?id=<?php echo (int) $notice->id; ?>" title="<?php echo lang('delete'); ?>" onclick="return confirm('<?php echo lang('are_you_sure_you_want_to_delete_this_item'); ?>');">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php } elseif ($this->ion_auth->in_group(array('Patient'))) { ?>
                                                <?php if ($notice->type == 'patient') { ?>
                                                    <tr>
                                                        <td><?php echo html_escape($notice->title); ?></td>
                                                        <td><?php
                                                            $plain = preg_replace('/\s+/', ' ', trim(strip_tags((string) $notice->description)));
                                                            echo html_escape(strlen($plain) > 160 ? substr($plain, 0, 160) . '…' : $plain);
                                                        ?></td>
                                                        <td class="text-center"><?php echo html_escape($notice->type); ?></td>
                                                        <td><?php
                                                            if (!empty($notice->date)) {
                                                                echo html_escape(date('d-m-Y', (int) $notice->date));
                                                            }
                                                        ?></td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <?php if ($notice->type == 'staff') { ?>
                                                    <tr>
                                                        <td><?php echo html_escape($notice->title); ?></td>
                                                        <td><?php
                                                            $plain = preg_replace('/\s+/', ' ', trim(strip_tags((string) $notice->description)));
                                                            echo html_escape(strlen($plain) > 160 ? substr($plain, 0, 160) . '…' : $plain);
                                                        ?></td>
                                                        <td class="text-center"><?php echo html_escape($notice->type); ?></td>
                                                        <td><?php
                                                            if (!empty($notice->date)) {
                                                                echo html_escape(date('d-m-Y', (int) $notice->date));
                                                            }
                                                        ?></td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="addNoticeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h4 class="modal-title text-white font-weight-bold" id="addNoticeModalLabel"><?php echo lang('add_notice'); ?></h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body">
                <form role="form" action="notice/addNew" class="clearfix" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label><?php echo lang('title'); ?> &ast;</label>
                            <input type="text" class="form-control form-control-lg" name="title" value="" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php echo lang('notice_for'); ?></label>
                            <select class="form-control form-control-lg" name="type">
                                <option value="patient"><?php echo lang('patient'); ?></option>
                                <option value="staff"><?php echo lang('staff'); ?></option>
                            </select>
                        </div>
                        <div class="form-group col-md-12 des">
                            <label><?php echo lang('description'); ?> &ast;</label>
                            <textarea class="ckeditor form-control editor" id="editor" name="description" rows="10" required></textarea>
                        </div>
                        <div class="form-group col-md-4">
                            <label><?php echo lang('date'); ?> &ast;</label>
                            <input type="text" class="form-control form-control-lg default-date-picker readonly" name="date" value="" required>
                        </div>
                        <div class="form-group col-md-12">
                            <button type="submit" name="submit" class="btn btn-primary float-right"><?php echo lang('submit'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="editNoticeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h4 class="modal-title text-white font-weight-bold" id="editNoticeModalLabel"><?php echo lang('edit_notice'); ?></h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body">
                <form role="form" id="editNoticeForm" class="clearfix" action="notice/addNew" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label><?php echo lang('title'); ?> &ast;</label>
                            <input type="text" class="form-control form-control-lg" name="title" value="" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php echo lang('notice_for'); ?></label>
                            <select class="form-control form-control-lg" name="type" id="edit_notice_type">
                                <option value="patient"><?php echo lang('patient'); ?></option>
                                <option value="staff"><?php echo lang('staff'); ?></option>
                            </select>
                        </div>
                        <div class="form-group col-md-12 des">
                            <label><?php echo lang('description'); ?> &ast;</label>
                            <textarea class="ckeditor form-control editor" id="editor1" name="description" rows="10" required></textarea>
                        </div>
                        <div class="form-group col-md-4">
                            <label><?php echo lang('date'); ?> &ast;</label>
                            <input type="text" class="form-control form-control-lg default-date-picker" onkeypress="return false;" name="date" value="" required>
                        </div>
                        <input type="hidden" name="id" id="edit_notice_id" value="">
                        <div class="form-group col-md-12">
                            <button type="submit" name="submit" class="btn btn-primary float-right"><?php echo lang('submit'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script src="common/assets/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
</script>
<script src="common/extranal/js/notice.js"></script>
