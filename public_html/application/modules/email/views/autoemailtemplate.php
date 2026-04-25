<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('autoemailtemplate'),
        'icon' => 'fas fa-envelope text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('autoemailtemplate'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('Auto generated email templates'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle text-sm datatables mb-0" id="editable-sample1" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-uppercase">#</th>
                                            <th class="text-uppercase"><?php echo lang('category'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('message'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('status'); ?></th>
                                            <th class="text-uppercase no-print"><?php echo lang('options'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="myModal1" role="dialog" aria-labelledby="editAutoEmailTemplateLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h4 class="modal-title text-white font-weight-bold" id="editAutoEmailTemplateLabel"><?php echo lang('edit'); ?> <?php echo lang('auto'); ?> <?php echo lang('template'); ?></h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body p-4">
                <?php echo validation_errors(); ?>
                <form role="form" id="emailtemp" name="myform" action="email/addNewAutoEmailTemplate" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label class="font-weight-bold text-uppercase small"><?php echo lang('category'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="category" value="" placeholder="" readonly required>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-uppercase small"><?php echo lang('message'); ?> <?php echo lang('template'); ?></label>
                        <div id="divbuttontag" class="mb-3"></div>
                        <textarea class="form-control form-control-lg" name="message" id="editor1" rows="10"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-uppercase small"><?php echo lang('status'); ?></label>
                        <select class="form-control form-control-lg select2" id="status" name="status"></select>
                    </div>

                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="type" value="email">

                    <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block"><?php echo lang('submit'); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
</script>
