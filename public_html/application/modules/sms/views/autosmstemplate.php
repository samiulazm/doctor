<?php $CI = get_instance(); ?>
<div class="content-wrapper bg-light">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('autosmstemplate'),
        'icon' => 'fas fa-sms text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('sms'), 'url' => 'sms'),
            array('label' => lang('autosmstemplate'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('All the Auto Sms templates names and related informations'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="editable-sample1" style="width:100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo lang('category'); ?></th>
                                            <th><?php echo lang('message'); ?></th>
                                            <th><?php echo lang('status'); ?></th>
                                            <th class="no-print"><?php echo lang('options'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
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

<div class="modal fade" id="myModal1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="myModalLabel"><?php echo lang('edit'); ?> <?php echo lang('auto'); ?> <?php echo lang('template'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <?php echo validation_errors(); ?>
                <form role="form" id="smstemp" name="myform" action="sms/addNewAutoSMSTemplate" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="autoSmsCategory"><?php echo lang('category'); ?> <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="autoSmsCategory" name="category" value="" required readonly>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="autoSmsMessage"><?php echo lang('message'); ?> <?php echo lang('template'); ?> <span class="text-danger">*</span></label>
                            <div id="divbuttontag" class="d-flex flex-wrap mb-2"></div>
                            <textarea class="form-control" name="message" id="editor1" rows="8" required></textarea>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="status"><?php echo lang('status'); ?></label>
                            <select class="form-control form-control-lg" id="status" name="status">
                            </select>
                        </div>
                        <input type="hidden" name="id" value="">
                        <input type="hidden" name="type" value="sms">
                        <div class="form-group col-md-12 text-right">
                            <button type="submit" name="submit" class="btn btn-primary"><?php echo lang('submit'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
</script>
<script src="common/extranal/js/sms/autosmstemplate.js"></script>
