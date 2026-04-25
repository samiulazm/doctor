<?php
$CI = get_instance();
if (!isset($advice)) {
    $advice = null;
}
?>
<div class="content-wrapper bg-light">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('advice_list'),
        'icon' => 'fas fa-comment-medical text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('advice_list'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('advice_list'); ?></h3>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal">
                                <i class="fa fa-plus mr-1"></i> <?php echo lang('add_new'); ?> <?php echo lang('advice'); ?>
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="editable-sample" style="width:100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo lang('name'); ?></th>
                                            <th><?php echo lang('description'); ?></th>
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

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalAddLabel" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="myModalAddLabel"><?php echo lang('add_advice'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form role="form" action="advice/addNew" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">

                    <div class="form-group">
                        <label for="adviceAddName"><?php echo lang('name'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="name" id="adviceAddName" value="<?php echo htmlspecialchars(
                            (string) set_value('name', $advice && isset($advice->name) ? $advice->name : ''),
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="editor1"><?php echo lang('description'); ?></label>
                        <textarea class="form-control ckeditor" id="editor1" name="description" rows="10" cols="20"><?php echo set_value(
                            'description',
                            $advice && isset($advice->description) ? $advice->description : ''
                        ); ?></textarea>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary float-right"><?php echo lang('submit'); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="myModalEditLabel" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="myModalEditLabel"><?php echo lang('edit_advice'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form role="form" id="editAdviceForm" class="clearfix" action="advice/addNew" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="id" value="">

                    <div class="form-group">
                        <label for="adviceEditName"><?php echo lang('name'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="name" id="adviceEditName" value="<?php echo htmlspecialchars(
                            (string) set_value('name', $advice && isset($advice->name) ? $advice->name : ''),
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="editor3"><?php echo lang('description'); ?></label>
                        <textarea class="form-control form-control-lg" id="editor3" name="description" rows="10" cols="20"><?php echo set_value(
                            'description',
                            $advice && isset($advice->description) ? $advice->description : ''
                        ); ?></textarea>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary float-right"><?php echo lang('submit'); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
    var select_doctor = <?php echo json_encode(lang('select_doctor')); ?>;
</script>
<script src="common/assets/tinymce/tinymce.min.js"></script>
<script src="common/extranal/js/advice.js"></script>
