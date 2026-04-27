<?php
$CI = get_instance();
if (!isset($departments) || !is_array($departments)) {
    $departments = array();
}
?>
<link href="common/extranal/css/doctor/doctor.css" rel="stylesheet">

<div class="content-wrapper bg-light">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('doctor'),
        'icon' => 'fas fa-user-md text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('doctor'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('doctor'); ?></h3>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal">
                                <i class="fa fa-plus mr-1"></i> <?php echo lang('add_new'); ?> <?php echo lang('doctor'); ?>
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="dt-doctor" data-legacy-table="editable-sample" style="width:100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th><?php echo lang('id'); ?></th>
                                        <th><?php echo lang('name'); ?></th>
                                        <th><?php echo lang('email'); ?></th>
                                        <th><?php echo lang('phone'); ?></th>
                                        <th><?php echo lang('department'); ?></th>
                                        <th><?php echo lang('profile'); ?></th>
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

<!-- Include Global Modal Styles -->
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/global-modal-styles.css'); ?>">

<!-- Add Doctor Modal-->
<div class="modal fade modal-enhanced" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-md mr-2"></i>
                    <?php echo lang('add_new_doctor'); ?>
                </h5>
                <a type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">
                    <span aria-hidden="true">&times;</span>
</a>
            </div>
            <div class="modal-body">
                <form action="doctor/addNew" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="row mb-5">
                        <div class="col-12 mb-4">
                            <h3 class="border-bottom border-primary pb-3 text-uppercase font-weight-900">
                                <i class="fas fa-user-circle mr-3 text-primary"></i><?php echo lang('add_new_doctor'); ?>
                            </h3>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('name'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="name" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('email'); ?> <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-lg shadow-sm" name="email" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('password'); ?> <span class="text-danger">*</span></label>
                                <input type="password" class="form-control form-control-lg shadow-sm" name="password" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('address'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="address" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('phone'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="phone" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('department'); ?></label>
                                <select class="form-control form-control-lg shadow-sm" name="department">
                                    <?php foreach ($departments as $department) { ?>
                                        <option value="<?php echo (int) $department->id; ?>"><?php echo htmlspecialchars($department->name, ENT_QUOTES, 'UTF-8'); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('doctor'); ?> <?php echo lang('description'); ?></label>
                                <textarea class="form-control shadow-sm" id="editor1" name="profile" rows="5"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('signature'); ?> <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="signature">
                                    <label class="custom-file-label shadow-sm"><?php echo lang('choose_signature_image'); ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('image'); ?></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="img_url">
                                    <label class="custom-file-label shadow-sm"><?php echo lang('choose_profile_picture'); ?></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-lg btn-block shadow-lg py-3">
                                <i class="fas fa-user-plus mr-3"></i><?php echo lang('submit'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Doctor Modal-->
<div class="modal fade modal-enhanced" id="myModal2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-edit mr-2"></i>
                    <?php echo lang('edit_doctor'); ?>
                </h5>
                <a type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">
                    <span aria-hidden="true">&times;</span>
                                    </a>
            </div>
            <div class="modal-body">
                <form id="editDoctorForm" action="doctor/addNew" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="row mb-5">
                        <div class="col-12 mb-4">
                            <h3 class="border-bottom border-primary pb-3 text-uppercase font-weight-900">
                                <i class="fas fa-user-circle mr-3 text-primary"></i><?php echo lang('update_doctor_details'); ?>
                            </h3>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('name'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="name" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('email'); ?> <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-lg shadow-sm" name="email" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('password'); ?></label>
                                <input type="password" class="form-control form-control-lg shadow-sm" name="password" placeholder="********">
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('address'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="address" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('phone'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="phone" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('department'); ?></label>
                                <select class="form-control form-control-lg shadow-sm department" name="department">
                                    <?php foreach ($departments as $department) { ?>
                                        <option value="<?php echo (int) $department->id; ?>"><?php echo htmlspecialchars($department->name, ENT_QUOTES, 'UTF-8'); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('doctor'); ?> <?php echo lang('description'); ?></label>
                                <textarea class="form-control shadow-sm" id="editor3" name="profile" rows="5"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('signature'); ?> <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="signature">
                                    <label class="custom-file-label shadow-sm"><?php echo lang('choose_signature_image'); ?></label>
                                </div>
                                <div class="mt-2">
                                    <img src="" id="signature" height="100px" alt="" class="img-thumbnail" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('image'); ?></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="img_url">
                                    <label class="custom-file-label shadow-sm"><?php echo lang('choose_profile_picture'); ?></label>
                                </div>
                                <div class="mt-2">
                                    <img src="" id="img" height="100px" alt="" class="img-thumbnail" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="id" id="id_value">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-lg btn-block shadow-lg py-3">
                                <i class="fas fa-user-edit mr-3"></i><?php echo lang('submit'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Info Modal -->
<div class="modal fade modal-enhanced" id="infoModal" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-md mr-2"></i>
                    <?php echo lang('doctor'); ?> <?php echo lang('info'); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row justify-content-center">
                    <div class="col-md-12 text-center mb-4">
                        <img src="" id="img1" class="img-thumbnail" height="200px" alt="" />
                    </div>

                    <div class="col-md-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-4 text-uppercase font-weight-bold text-muted"><?php echo lang('name'); ?></div>
                                    <div class="col-md-8 nameClass"></div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4 text-uppercase font-weight-bold text-muted"><?php echo lang('email'); ?></div>
                                    <div class="col-md-8 emailClass"></div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4 text-uppercase font-weight-bold text-muted"><?php echo lang('address'); ?></div>
                                    <div class="col-md-8 addressClass"></div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4 text-uppercase font-weight-bold text-muted"><?php echo lang('phone'); ?></div>
                                    <div class="col-md-8 phoneClass"></div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4 text-uppercase font-weight-bold text-muted"><?php echo lang('department'); ?></div>
                                    <div class="col-md-8 departmentClass"></div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 text-uppercase font-weight-bold text-muted"><?php echo lang('profile'); ?></div>
                                    <div class="col-md-8 profileClass"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
</script>

<script src="common/assets/tinymce/tinymce.min.js"></script>
<script src="common/extranal/js/doctor/doctor.js"></script>