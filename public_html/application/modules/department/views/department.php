<?php
$CI = get_instance();
$departments = !empty($departments) ? $departments : array();
?>
<div class="content-wrapper bg-light">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('departments'),
        'icon' => 'fas fa-hospital text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('department'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('department'); ?></h3>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal">
                                <i class="fa fa-plus mr-1"></i> <?php echo lang('add_new'); ?> <?php echo lang('department'); ?>
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="editable-sample" style="width:100%">
                                    <thead class="thead-light">
                                    <tr>
                                        <th><?php echo lang('name'); ?></th>
                                        <th><?php echo lang('description'); ?></th>
                                        <th class="no-print"><?php echo lang('options'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($departments as $department) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($department->name); ?></td>
                                            <td><?php echo !empty($department->description) ? htmlspecialchars($department->description) : ''; ?></td>
                                            <td class="no-print">
                                                <a type="button" class="btn btn-primary btn-sm editbutton" title="<?php echo lang('edit'); ?>" data-id="<?php echo (int) $department->id; ?>"><i class="fa fa-edit"></i></a>
                                                <a class="btn btn-success btn-sm" title="<?php echo lang('doctor_directory'); ?>" href="department/doctorDirectory?id=<?php echo (int) $department->id; ?>"><i class="fa fa-users"></i></a>
                                                <a class="btn btn-danger btn-sm" title="<?php echo lang('delete'); ?>" href="department/delete?id=<?php echo (int) $department->id; ?>" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
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

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="deptAddModalLabel" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h3 class="modal-title font-weight-bold" id="deptAddModalLabel"><?php echo lang('add_department'); ?></h3>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body p-4">
                <form action="department/addNew" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label class="font-weight-bold text-muted small text-uppercase"><?php echo lang('department'); ?> <?php echo lang('name'); ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" name="name" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-muted small text-uppercase"><?php echo lang('description'); ?> <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="description" id="editor" rows="8" required></textarea>
                    </div>
                    <input type="hidden" name="id" value="">
                    <button type="submit" name="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-check-circle mr-1"></i><?php echo lang('submit'); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="deptEditModalLabel" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h3 class="modal-title font-weight-bold" id="deptEditModalLabel"><?php echo lang('edit_department'); ?></h3>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body p-4">
                <form id="departmentEditForm" action="department/addNew" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label class="font-weight-bold text-muted small text-uppercase"><?php echo lang('department'); ?> <?php echo lang('name'); ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" name="name" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-muted small text-uppercase"><?php echo lang('description'); ?> <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="editor1" name="description" rows="8" required></textarea>
                    </div>
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="p_id" value="">
                    <button type="submit" name="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-check-circle mr-1"></i><?php echo lang('submit'); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
</script>
<script src="common/assets/tinymce/tinymce.min.js"></script>
<script src="common/extranal/js/department.js"></script>
