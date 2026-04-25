<link href="common/extranal/css/patient/medical_history.css" rel="stylesheet">

<?php
$CI = get_instance();
if (!isset($groups) || !is_array($groups)) {
    $groups = array();
}
?>
<div class="content-wrapper bg-light">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('all') . ' ' . lang('patients'),
        'icon' => 'fas fa-user text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('patient'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('all'); ?> <?php echo lang('patients'); ?></h3>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal">
                                <i class="fas fa-plus mr-1"></i> <?php echo lang('add_new'); ?> <?php echo lang('patient'); ?>
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="editable-sample" style="width:100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="font-weight-bold text-uppercase"><?php echo lang('id'); ?></th>
                                            <th class="font-weight-bold"><?php echo lang('name'); ?></th>
                                            <th class="font-weight-bold"><?php echo lang('phone'); ?></th>
                                            <?php if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist'))) { ?>
                                                <th class="font-weight-bold"><?php echo lang('due_balance'); ?></th>
                                            <?php } ?>
                                            <th class="font-weight-bold no-print"><?php echo lang('options'); ?></th>
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

<!-- Add Patient Modal-->
<div class="modal fade modal-enhanced" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus mr-2"></i>
                    <?php echo lang('register_new_patient'); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form action="patient/addNew" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <!-- Personal Information -->
                    <div class="row mb-5">
                        <div class="col-12 mb-4">
                            <h3 class="border-bottom border-primary pb-3 text-uppercase font-weight-900">
                                <i
                                    class="fas fa-user-circle mr-3 text-primary"></i><?php echo lang('personal_details'); ?>
                            </h3>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('full_name'); ?>
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="name" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('email_address'); ?>
                                    <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-lg shadow-sm" name="email"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('password'); ?>
                                    <span class="text-danger">*</span></label>
                                <input type="password" class="form-control form-control-lg shadow-sm" name="password"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('contact_number'); ?>
                                    <span class="text-danger">*</span></label>
                                <input type="number" class="form-control form-control-lg shadow-sm" name="phone"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('date_of_birth'); ?></label>
                                <input type="text" class="form-control form-control-lg shadow-sm default-date-picker"
                                    name="birthdate">
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('national_id'); ?></label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="national_id"
                                    placeholder="<?php echo lang('government_id_number'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col-12 mb-4">
                            <h3 class="border-bottom border-danger pb-3 text-uppercase font-weight-900">
                                <i class="fas fa-heartbeat mr-3 text-danger"></i><?php echo lang('medical_profile'); ?>
                            </h3>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('sex'); ?></label>
                                <select class="form-control form-control-lg shadow-sm" name="sex">
                                    <option value="Male"><?php echo lang('male'); ?></option>
                                    <option value="Female"><?php echo lang('female'); ?></option>
                                </select>
                            </div>
                        </div>



                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('blood_group'); ?></label>
                                <select class="form-control form-control-lg shadow-sm" name="bloodgroup">
                                    <option value=""><?php echo lang('select_blood_group'); ?></option>
                                    <?php foreach ($groups as $group) { ?>
                                        <option value="<?php echo htmlspecialchars($group->group, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($group->group, ENT_QUOTES, 'UTF-8'); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('height'); ?>
                                    (cm)</label>
                                <input type="number" class="form-control form-control-lg shadow-sm" name="height"
                                    placeholder="175">
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('weight'); ?>
                                    (kg)</label>
                                <input type="number" class="form-control form-control-lg shadow-sm" name="weight"
                                    placeholder="70">
                            </div>
                        </div>



                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('known_allergies'); ?></label>
                                <textarea class="form-control shadow-sm" name="known_allergies" rows="3"
                                    placeholder="<?php echo lang('list_any_known_allergies_or_sensitivities'); ?>"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('medical_history'); ?></label>
                                <textarea class="form-control shadow-sm" name="medical_history" rows="3"
                                    placeholder="<?php echo lang('brief_medical_history_or_ongoing_conditions'); ?>"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('doctor'); ?></label>
                                <select class="form-control form-control-lg shadow-sm" id="doctorchoose" name="doctor">
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="row mb-5">
                        <div class="col-12 mb-4">
                            <h3 class="border-bottom border-success pb-3 text-uppercase font-weight-900">
                                <i
                                    class="fas fa-map-marked-alt mr-3 text-success"></i><?php echo lang('contact_information'); ?>
                            </h3>
                        </div>

                        <div class="col-md-12 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('residential_address'); ?>
                                    <span class="text-danger">*</span></label>
                                <textarea class="form-control shadow-sm" rows="3" name="address" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('emergency_contact_name'); ?></label>
                                <input type="text" class="form-control form-control-lg shadow-sm"
                                    name="emergency_contact_name"
                                    placeholder="<?php echo lang('emergency_contact_person'); ?>">
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('emergency_contact_number'); ?></label>
                                <input type="tel" class="form-control form-control-lg shadow-sm"
                                    name="emergency_contact_number"
                                    placeholder="<?php echo lang('emergency_contact_number'); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Profile Image -->
                    <div class="row mb-5">
                        <div class="col-12 mb-4">
                            <h3 class="border-bottom border-info pb-3 text-uppercase font-weight-900">
                                <i class="fas fa-camera mr-3 text-info"></i> <?php echo lang('profile_photo'); ?>
                            </h3>
                        </div>
                        <div class="col-md-12">
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div
                                    class="fileupload-new thumbnail img_class fileupload-preview fileupload-exists thumbnail img_thumb bg-white p-2">
                                    <img src="" height="100px" alt="" />
                                </div>
                                <div class="mt-2">
                                    <span class="btn btn-white btn-file">
                                        <span class="btn fileupload-new badge badge-secondary"><i
                                                class="fas fa-paper-clip"></i> <?php echo lang('select_image'); ?></span>
                                        <input type="file" class="default" name="img_url" />
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="sms" name="sms">
                                <label class="custom-control-label font-weight-bold"
                                    for="sms"><?php echo lang('send_sms'); ?></label>
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

<!-- Edit Patient Modal-->
<div class="modal fade modal-enhanced" id="myModal2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-edit mr-2"></i>
                    <?php echo lang('edit_patient'); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form role="form" id="editPatientForm" action="patient/addNew" method="post"
                    enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <!-- Personal Information -->
                    <div class="row mb-5">
                        <div class="col-12 mb-4">
                            <h3 class="border-bottom border-primary pb-3 text-uppercase font-weight-900">
                                <i
                                    class="fas fa-user-circle mr-3 text-primary"></i><?php echo lang('personal_details'); ?>
                            </h3>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('full_name'); ?>
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="name" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('email_address'); ?>
                                    <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-lg shadow-sm" name="email"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('password'); ?>
                                    <span class="text-danger">*</span></label>
                                <input type="password" class="form-control form-control-lg shadow-sm" name="password">
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('contact_number'); ?>
                                    <span class="text-danger">*</span></label>
                                <input type="number" class="form-control form-control-lg shadow-sm" name="phone"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('date_of_birth'); ?></label>
                                <input type="text" class="form-control form-control-lg shadow-sm default-date-picker"
                                    name="birthdate">
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('national_id'); ?></label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="national_id"
                                    placeholder="<?php echo lang('government_id_number'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col-12 mb-4">
                            <h3 class="border-bottom border-danger pb-3 text-uppercase font-weight-900">
                                <i class="fas fa-heartbeat mr-3 text-danger"></i><?php echo lang('medical_profile'); ?>
                            </h3>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('sex'); ?></label>
                                <select class="form-control form-control-lg shadow-sm" name="sex">
                                    <option value="Male"><?php echo lang('male'); ?></option>
                                    <option value="Female"><?php echo lang('female'); ?></option>
                                </select>
                            </div>
                        </div>



                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('blood_group'); ?></label>
                                <select class="form-control form-control-lg shadow-sm" name="bloodgroup">
                                    <option value=""><?php echo lang('select_blood_group'); ?></option>
                                    <?php foreach ($groups as $group) { ?>
                                        <option value="<?php echo htmlspecialchars($group->group, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($group->group, ENT_QUOTES, 'UTF-8'); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('height'); ?>
                                    (cm)</label>
                                <input type="number" class="form-control form-control-lg shadow-sm" name="height"
                                    placeholder="175">
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('weight'); ?>
                                    (kg)</label>
                                <input type="number" class="form-control form-control-lg shadow-sm" name="weight"
                                    placeholder="70">
                            </div>
                        </div>



                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('known_allergies'); ?></label>
                                <textarea class="form-control shadow-sm" name="known_allergies" rows="3"
                                    placeholder="<?php echo lang('list_any_known_allergies_or_sensitivities'); ?>"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('medical_history'); ?></label>
                                <textarea class="form-control shadow-sm" name="medical_history" rows="3"
                                    placeholder="<?php echo lang('brief_medical_history_or_ongoing_conditions'); ?>"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('doctor'); ?></label>
                                <select class="form-control form-control-lg shadow-sm js-example-basic-single doctor" id="doctorchoose1" name="doctor">
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="row mb-5">
                        <div class="col-12 mb-4">
                            <h3 class="border-bottom border-success pb-3 text-uppercase font-weight-900">
                                <i
                                    class="fas fa-map-marked-alt mr-3 text-success"></i><?php echo lang('contact_information'); ?>
                            </h3>
                        </div>

                        <div class="col-md-12 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('residential_address'); ?>
                                    <span class="text-danger">*</span></label>
                                <textarea class="form-control shadow-sm" rows="3" name="address" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('emergency_contact_name'); ?></label>
                                <input type="text" class="form-control form-control-lg shadow-sm"
                                    name="emergency_contact_name"
                                    placeholder="<?php echo lang('emergency_contact_person'); ?>">
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label
                                    class="text-uppercase font-weight-bold text-muted"><?php echo lang('emergency_contact_number'); ?></label>
                                <input type="tel" class="form-control form-control-lg shadow-sm"
                                    name="emergency_contact_number"
                                    placeholder="<?php echo lang('emergency_contact_number'); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Profile Image -->
                    <div class="row mb-5">
                        <div class="col-12 mb-4">
                            <h3 class="border-bottom border-info pb-3 text-uppercase font-weight-900">
                                <i class="fas fa-camera mr-3 text-info"></i> <?php echo lang('profile_photo'); ?>
                            </h3>
                        </div>
                        <div class="col-md-12">
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div
                                    class="fileupload-new thumbnail img_class fileupload-preview fileupload-exists thumbnail img_thumb bg-white p-2">
                                    <img src="" height="100px" id="img" alt="" />
                                </div>
                                <div class="mt-2">
                                    <span class="btn btn-white btn-file">
                                        <span class="btn fileupload-new badge badge-secondary"><i
                                                class="fas fa-paper-clip"></i> <?php echo lang('select_image'); ?></span>
                                        <input type="file" class="default" name="img_url" />
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="sms" value="sms">
                                <label
                                    class="custom-control-label font-weight-bold"><?php echo lang('send_sms'); ?></label>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="id" value=''>
                    <input type="hidden" name="p_id" value="<?php echo (isset($patient) && !empty($patient->patient_id)) ? htmlspecialchars($patient->patient_id, ENT_QUOTES, 'UTF-8') : ''; ?>">

                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block shadow-lg py-3">
                                <i class="fas fa-user-edit mr-3"></i><?php echo lang('submit'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>












<div class="modal fade modal-enhanced" id="infoModal" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user mr-2"></i>
                    <?php echo lang('patient'); ?> <?php echo lang('info'); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body row">
                <form role="form" action="patient/addNew" class="form-horizontal" method="post"
                    enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="container-fluid">
                        <div class="justify-content-center" style="display:flex">
                            <div class="col-md-6">
                                <!-- Basic Information Section -->
                                <div class="card mb-4 shadow-sm">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="fas fa-user-circle mr-2"></i><?php echo lang('basic_information'); ?></h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-4">
                                            <img src="" id="img1" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted d-block"><?php echo lang('patient_id'); ?></label>
                                            <div class="patientIdClass"></div>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted d-block"><?php echo lang('full_name'); ?></label>
                                            <div class="nameClass"></div>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted d-block"><?php echo lang('email_address'); ?></label>
                                            <div class="emailClass"></div>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted d-block"><?php echo lang('contact_number'); ?></label>
                                            <div class="phoneClass"></div>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted d-block"><?php echo lang('national_id'); ?></label>
                                            <div class="national_id"></div>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted d-block"><?php echo lang('residential_address'); ?></label>
                                            <div class="addressClass"></div>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted d-block"><?php echo lang('age'); ?></label>
                                            <div class="ageClass"></div>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted d-block"><?php echo lang('gender'); ?></label>
                                            <div class="genderClass"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Medical Information Section -->
                                <div class="card mb-4 shadow-sm">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0"><i class="fas fa-heartbeat mr-2"></i><?php echo lang('medical_information'); ?></h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted d-block"><?php echo lang('blood_group'); ?></label>
                                            <div class="bloodgroupClass"></div>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted d-block"><?php echo lang('birth_date'); ?></label>
                                            <div class="birthdateClass"></div>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted d-block"><?php echo lang('doctor'); ?></label>
                                            <div class="doctorClass"></div>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted d-block"><?php echo lang('height'); ?></label>
                                            <div class="height"></div>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted d-block"><?php echo lang('weight'); ?></label>
                                            <div class="weight"></div>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted d-block"><?php echo lang('known_allergies'); ?></label>
                                            <div class="known_allergies"></div>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted d-block"><?php echo lang('medical_history'); ?></label>
                                            <div class="medical_history"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Emergency Contact Section -->
                                <div class="card shadow-sm">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="mb-0"><i class="fas fa-ambulance mr-2"></i><?php echo lang('emergency_contact'); ?></h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted d-block"><?php echo lang('emergency_contact_name'); ?></label>
                                            <div class="emergency_contact_name"></div>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted d-block"><?php echo lang('emergency_contact_number'); ?></label>
                                            <div class="emergency_contact_number"></div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>




<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var select_doctor = <?php echo json_encode(lang('select_doctor')); ?>;
    var language = <?php echo json_encode($this->language); ?>;
</script>

<script src="common/extranal/js/patient/patient.js"></script>


<!-- <script>
    function openNewWindow() {
        window.open('https://codearistos.net/dev/hmz/v2/patient/medicalHistory?id=24', '_blank', 'toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=300,width=1000,height=600');
    }
</script> -->
