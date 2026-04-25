<?php
$CI = get_instance();
if (!isset($departments) || !is_array($departments)) {
    $departments = array();
}
$edit_mode = !empty($doctor) && is_object($doctor) && !empty($doctor->id);
$page_title = $edit_mode ? lang('edit_doctor') : lang('add_doctor');
$page_icon = $edit_mode ? 'fas fa-user-edit text-primary mr-2' : 'fas fa-user-plus text-primary mr-2';
$dept_value = (string) set_value('department', $edit_mode && !empty($doctor->department) ? (string) $doctor->department : '');
?>
<link href="common/extranal/css/doctor/add_new.css" rel="stylesheet">

<div class="content-wrapper bg-light">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => $page_title,
        'icon' => $page_icon,
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('doctor'), 'url' => 'doctor'),
            array('label' => $page_title, 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-9">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('doctor_registration_form'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                            <?php echo $this->session->flashdata('feedback'); ?>

                            <form role="form" action="doctor/addNew" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">

                                <div class="row mb-5">
                                    <div class="col-12 mb-4">
                                        <h3 class="border-bottom border-primary pb-3 text-uppercase font-weight-900">
                                            <i class="fas fa-user-circle mr-3 text-primary"></i><?php echo lang('personal_details'); ?>
                                        </h3>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('name'); ?> <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg shadow-sm" name="name" value="<?php echo htmlspecialchars(
                                                (string) set_value('name', $edit_mode && isset($doctor->name) ? $doctor->name : ''),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('email'); ?> <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control form-control-lg shadow-sm" name="email" value="<?php echo htmlspecialchars(
                                                (string) set_value('email', $edit_mode && isset($doctor->email) ? $doctor->email : ''),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('password'); ?><?php if (!$edit_mode) { ?> <span class="text-danger">*</span><?php } ?></label>
                                            <input type="password" class="form-control form-control-lg shadow-sm" name="password" placeholder="********"<?php echo $edit_mode ? '' : ' required'; ?>>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('phone'); ?> <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg shadow-sm" name="phone" value="<?php echo htmlspecialchars(
                                                (string) set_value('phone', $edit_mode && isset($doctor->phone) ? $doctor->phone : ''),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('address'); ?> <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg shadow-sm" name="address" value="<?php echo htmlspecialchars(
                                                (string) set_value('address', $edit_mode && isset($doctor->address) ? $doctor->address : ''),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('department'); ?></label>
                                            <select class="form-control form-control-lg shadow-sm" name="department">
                                                <?php foreach ($departments as $department) { ?>
                                                    <option value="<?php echo (int) $department->id; ?>"<?php
                                                    $sel = false;
                                                    if (set_value('department', '') !== '') {
                                                        $sel = ((int) $department->id === (int) set_value('department'));
                                                    } elseif ($edit_mode && !empty($doctor->department)) {
                                                        $sel = ((int) $department->id === (int) $doctor->department);
                                                    }
                                                    echo $sel ? ' selected' : '';
                                                    ?>><?php echo htmlspecialchars($department->name, ENT_QUOTES, 'UTF-8'); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('profile'); ?> <span class="text-danger">*</span></label>
                                            <textarea class="form-control ckeditor" id="editor1" name="profile" rows="6"><?php echo set_value('profile', $edit_mode && !empty($doctor->profile) ? $doctor->profile : ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-12 mb-4">
                                        <h3 class="border-bottom border-info pb-3 text-uppercase font-weight-900">
                                            <i class="fas fa-images mr-3 text-info"></i><?php echo lang('images'); ?>
                                        </h3>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('image'); ?></label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="img_url" id="customFile1">
                                                <label class="custom-file-label" for="customFile1"><?php echo lang('choose_profile_picture'); ?></label>
                                            </div>
                                            <?php if ($edit_mode && !empty($doctor->img_url)) { ?>
                                                <div class="mt-3">
                                                    <img src="<?php echo htmlspecialchars($doctor->img_url, ENT_QUOTES, 'UTF-8'); ?>" class="img-thumbnail" height="100" alt="">
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('signature'); ?><?php if (!$edit_mode) { ?> <span class="text-danger">*</span><?php } ?></label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="signature" id="customFile2"<?php echo $edit_mode ? '' : ' required'; ?>>
                                                <label class="custom-file-label" for="customFile2"><?php echo lang('choose_signature_image'); ?></label>
                                            </div>
                                            <?php if ($edit_mode && !empty($doctor->signature)) { ?>
                                                <div class="mt-3">
                                                    <img src="<?php echo htmlspecialchars($doctor->signature, ENT_QUOTES, 'UTF-8'); ?>" class="img-thumbnail" height="100" alt="">
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="id" value="<?php echo $edit_mode ? (int) $doctor->id : ''; ?>">

                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block shadow-sm py-3">
                                            <i class="fas fa-save mr-3"></i><?php echo lang('submit'); ?>
                                        </button>
                                    </div>
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
<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
</script>
<script src="common/assets/tinymce/tinymce.min.js"></script>
<script src="common/extranal/js/doctor/doctor.js"></script>
