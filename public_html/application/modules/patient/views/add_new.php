<?php
$CI = get_instance();
if (!isset($groups) || !is_array($groups)) {
    $groups = array();
}
?>
<link href="common/extranal/css/patient/add_new.css" rel="stylesheet">

<div class="content-wrapper bg-light">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('new_patient_registration'),
        'icon' => 'fas fa-user-plus text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('patients'), 'url' => 'patient'),
            array('label' => lang('new_registration'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-9">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('patient_enrollment_form'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                            <form role="form" action="patient/addNew" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">

                                <!-- Personal Information -->
                                <div class="row mb-5">
                                    <div class="col-12 mb-4">
                                        <h3 class="border-bottom border-primary pb-3 text-uppercase font-weight-900">
                                            <i class="fas fa-user-circle mr-3 text-primary"></i><?php echo lang('personal_details'); ?>
                                        </h3>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('full_name'); ?> <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg shadow-sm" name="name" placeholder="<?php echo lang('enter_patient_s_full_name'); ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('email_address'); ?> <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control form-control-lg shadow-sm" name="email" placeholder="example@domain.com" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('contact_number'); ?> <span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control form-control-lg shadow-sm" name="phone" placeholder="+1 (234) 567-8900" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('date_of_birth'); ?></label>
                                            <input type="date" class="form-control form-control-lg shadow-sm" name="birthdate">
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('national_id'); ?></label>
                                            <input type="text" class="form-control form-control-lg shadow-sm" name="national_id" placeholder="<?php echo lang('government_id_number'); ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Medical Information -->
                                <div class="row mb-5">
                                    <div class="col-12 mb-4">
                                        <h3 class="border-bottom border-danger pb-3 text-uppercase font-weight-900">
                                            <i class="fas fa-heartbeat mr-3 text-danger"></i><?php echo lang('medical_profile'); ?>
                                        </h3>
                                    </div>

                                    <div class="col-md-3 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('blood_type'); ?></label>
                                            <select class="form-control form-control-lg shadow-sm" name="bloodgroup">
                                                <option value=""><?php echo lang('select_blood_group'); ?></option>
                                                <?php foreach ($groups as $group) { ?>
                                                    <option value="<?php echo htmlspecialchars($group->group, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($group->group, ENT_QUOTES, 'UTF-8'); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('height'); ?> (cm)</label>
                                            <input type="number" class="form-control form-control-lg shadow-sm" name="height" placeholder="175">
                                        </div>
                                    </div>

                                    <div class="col-md-3 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('weight'); ?> (kg)</label>
                                            <input type="number" class="form-control form-control-lg shadow-sm" name="weight" placeholder="70">
                                        </div>
                                    </div>

                                    <div class="col-md-3 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('gender'); ?></label>
                                            <select class="form-control form-control-lg shadow-sm" name="sex">
                                                <option value="Male"><?php echo lang('male'); ?></option>
                                                <option value="Female"><?php echo lang('female'); ?></option>
                                                <option value="Other"><?php echo lang('other'); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('known_allergies'); ?></label>
                                            <textarea class="form-control shadow-sm" name="known_allergies" rows="3" placeholder="<?php echo lang('list_any_known_allergies_or_sensitivities'); ?>"></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('medical_history'); ?></label>
                                            <textarea class="form-control shadow-sm" name="medical_history" rows="3" placeholder="<?php echo lang('brief_medical_history_or_ongoing_conditions'); ?>"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="row mb-5">
                                    <div class="col-12 mb-4">
                                        <h3 class="border-bottom border-success pb-3 text-uppercase font-weight-900">
                                            <i class="fas fa-map-marked-alt mr-3 text-success"></i><?php echo lang('contact_information'); ?>
                                        </h3>
                                    </div>

                                    <div class="col-md-12 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('residential_address'); ?> <span class="text-danger">*</span></label>
                                            <textarea class="form-control shadow-sm" name="address" rows="3" placeholder="<?php echo lang('complete_residential_address'); ?>" required minlength="2"></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('emergency_contact_name'); ?></label>
                                            <input type="text" class="form-control form-control-lg shadow-sm" name="emergency_contact_name" placeholder="<?php echo lang('emergency_contact_person'); ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="form-group">
                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('emergency_contact_number'); ?></label>
                                            <input type="tel" class="form-control form-control-lg shadow-sm" name="emergency_contact_number" placeholder="<?php echo lang('emergency_contact_number'); ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Profile Image -->
                                <div class="row mb-5">
                                    <div class="col-12 mb-4">
                                        <h3 class="border-bottom border-info pb-3 text-uppercase font-weight-900">
                                            <i class="fas fa-camera mr-3 text-info"></i><?php echo lang('profile_photo'); ?>
                                        </h3>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="img_url" id="customFile">
                                            <label class="custom-file-label shadow-sm" for="customFile"><?php echo lang('choose_profile_picture'); ?></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block shadow-sm py-3">
                                            <i class="fas fa-user-plus mr-3"></i><?php echo lang('register_patient'); ?>
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

<!--main content end-->
<!--footer start-->