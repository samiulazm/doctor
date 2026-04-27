<?php
$ap_settings_saas_ui = $this->ion_auth->in_group('superadmin');
$CI = get_instance();
?>
<?php if ($ap_settings_saas_ui) : ?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/settings-saas-styles.css'); ?>">
<?php endif; ?>

<style type="text/css">
    .img_thumb,
    .img_class {
        height: 150px;
        width: 150px;
    }
</style>

<div class="content-wrapper <?php echo $ap_settings_saas_ui ? 'ap-settings-saas bg-light' : 'bg-light'; ?>">
    <!-- Content Header (Page header) -->
    <section class="content-header <?php echo $ap_settings_saas_ui ? 'ap-settings-saas-hero shadow-none border-0 py-4' : 'py-3 border-bottom bg-white'; ?>">
        <div class="container-fluid">
            <?php if ($ap_settings_saas_ui) : ?>
            <div class="row align-items-center pl-1">
                <div class="col-12 col-lg-8">
                    <span class="ap-settings-saas-badge"><?php echo lang('superadmin'); ?> · SaaS</span>
                    <h1 class="display-4 font-weight-black mb-0">
                        <i class="fas fa-user-cog mr-2"></i><?php echo lang('manage_profile'); ?>
                    </h1>
                    <nav aria-label="breadcrumb" class="mt-2">
                        <ol class="breadcrumb bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item active"><?php echo lang('profile'); ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
            <?php else : ?>
            <div class="row align-items-center pl-1">
                <div class="col-12 col-lg-8">
                    <h1 class="h3 mb-1 font-weight-bold"><i class="fas fa-user-cog text-primary mr-2"></i><?php echo lang('manage_profile'); ?></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0 py-0 small">
                            <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo lang('profile'); ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
            <?php endif; ?>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content py-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-7">
                    <div class="card <?php echo $ap_settings_saas_ui ? 'ap-settings-saas-profile-card border-0 shadow' : 'shadow-sm border-0'; ?>">
                        <!-- <div class="card-header">
                            <h3 class="card-title">All the department names and related informations</h3>
                        </div> -->
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="">
                                <div class="clearfix">
                                    <?php echo validation_errors(); ?>
                                    <?php if (!$this->ion_auth->in_group(array('Patient', 'Doctor', 'superadmin', 'admin'))) {

                                        $ion_user_id = $this->ion_auth->get_user_id();
                                        $group_id = $this->profile_model->getUsersGroups($ion_user_id)->row()->group_id;
                                        $group_name = $this->profile_model->getGroups($group_id)->row()->name;
                                        $group_name = strtolower($group_name);
                                        $details = $this->profile_model->getUserDetails($ion_user_id, $group_name);

                                    ?>
                                        <form role="form" action="profile/addNew" class="clearfix" method="post" enctype="multipart/form-data">
                                            <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                                            <div class="form-group">
                                                <label for="staff_profile_name"><?php echo lang('name'); ?> <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-lg" id="staff_profile_name" name="name" value="<?php echo !empty($profile->username) ? htmlspecialchars($profile->username) : ''; ?>" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="staff_profile_password"><?php echo lang('change_password'); ?></label>
                                                <input type="password" class="form-control form-control-lg" id="staff_profile_password" name="password" placeholder="********" autocomplete="new-password">
                                            </div>
                                            <div class="form-group">
                                                <label for="staff_profile_email"><?php echo lang('email'); ?> <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control form-control-lg" id="staff_profile_email" name="email" value="<?php echo !empty($profile->email) ? htmlspecialchars($profile->email) : ''; ?>" required autocomplete="email">
                                            </div>

                                            <div class="form-group last col-md-6">
                                                <label class="control-label"><?php echo lang('profile'); ?> <?php echo lang('image'); ?> </label>
                                                <div class="">
                                                    <div class="fileupload fileupload-new" data-provides="fileupload">
                                                        <div class="fileupload-new thumbnail img_class fileupload-preview fileupload-exists thumbnail img_thumb">
                                                            <img src="<?php echo (!empty($details) && !empty($details->img_url)) ? htmlspecialchars($details->img_url) : ''; ?>" id="img_staff" class="img_thumb" height="100" alt="">
                                                        </div>
                                                        <div>
                                                            <span class="btn btn-white btn-file">
                                                                <span class="btn fileupload-new badge badge-secondary"><i class="fa fa-paper-clip"></i> <?php echo lang('select_image'); ?></span>
                                                                <!-- <span class="fileupload-exists"><i class="fa fa-undo"></i> <?php echo lang('change'); ?></span> -->
                                                                <input type="file" class="default" name="img_url" />
                                                            </span>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <input type="hidden" name="id" value='<?php
                                                                                    if (!empty($profile->id)) {
                                                                                        echo $profile->id;
                                                                                    }
                                                                                    ?>'>
                                            <div class="form-group clearfix pt-2">
                                                <button type="submit" name="submit" class="btn btn-primary float-right"><?php echo lang('submit'); ?></button>
                                            </div>
                                        </form>
                                    <?php } else { ?>

                                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="general_info-tab" data-toggle="tab" href="#general_info" role="tab" aria-controls="general_info" aria-selected="true"><?php echo lang('general_info'); ?></a>
                                            </li>
                                            <?php if (!$this->ion_auth->in_group(array('superadmin', 'admin'))) { ?>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="email_notification-tab" data-toggle="tab" href="#email_notification" role="tab" aria-controls="email_notification" aria-selected="false"><?php echo lang('email_confirmation_during_appointment'); ?></a>
                                                </li>
                                            <?php } ?>
                                        </ul>


                                        <div class="card pt-4">
                                            <div class="tab-content col-md-12">
                                                <div id="general_info" class="tab-pane active">
                                                    <form role="form" action="profile/addNew" class="clearfix" method="post" enctype="multipart/form-data">
                                                        <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                                                        <div class="form-group">
                                                            <label for="profile_name"><?php echo lang('name'); ?> <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control form-control-lg" id="profile_name" name="name" value="<?php echo !empty($profile->username) ? htmlspecialchars($profile->username) : ''; ?>" required>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="profile_password"><?php echo lang('change_password'); ?></label>
                                                            <input type="password" class="form-control form-control-lg" id="profile_password" name="password" placeholder="********" autocomplete="new-password">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="profile_email"><?php echo lang('email'); ?> <span class="text-danger">*</span></label>
                                                            <input type="email" class="form-control form-control-lg" id="profile_email" name="email" value="<?php echo !empty($profile->email) ? htmlspecialchars($profile->email) : ''; ?>" required autocomplete="email">
                                                        </div>

                                                        <?php
                                                        if (!$this->ion_auth->in_group(array('superadmin', 'admin'))) {
                                                            $current_user_id = $this->ion_auth->user()->row()->id;
                                                            $group_id = $this->db->get_where('users_groups', array('user_id' => $current_user_id))->row()->group_id;
                                                            $group_name = $this->db->get_where('groups', array('id' => $group_id))->row()->name;
                                                            $group_name = strtolower($group_name);
                                                            $user_language = $this->db->get_where($group_name, array('ion_user_id' => $current_user_id))->row()->language;
                                                        }
                                                        ?>



                                                        <?php if (!$this->ion_auth->in_group(array('superadmin', 'admin'))) { ?>


                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1"> <?php echo lang('language'); ?></label>
                                                                <select class="form-control col-sm-9 js-example-basic-single" name="language">
                                                                    <option value=""> </option>
                                                                    <option value="arabic" <?php
                                                                                            if (!empty($user_language)) {
                                                                                                if ($user_language == 'arabic') {
                                                                                                    echo 'selected';
                                                                                                }
                                                                                            }
                                                                                            ?>><?php echo lang('arabic'); ?>
                                                                    </option>


                                                                    <option value="english" <?php
                                                                                            if (!empty($user_language)) {
                                                                                                if ($user_language == 'english') {
                                                                                                    echo 'selected';
                                                                                                }
                                                                                            }
                                                                                            ?>><?php echo lang('english'); ?>
                                                                    </option>

                                                                    <option value="spanish" <?php
                                                                                            if (!empty($user_language)) {
                                                                                                if ($user_language == 'spanish') {
                                                                                                    echo 'selected';
                                                                                                }
                                                                                            }
                                                                                            ?>><?php echo lang('spanish'); ?>
                                                                    </option>
                                                                    <option value="french" <?php
                                                                                            if (!empty($user_language)) {
                                                                                                if ($user_language == 'french') {
                                                                                                    echo 'selected';
                                                                                                }
                                                                                            }
                                                                                            ?>><?php echo lang('french'); ?>
                                                                    </option>
                                                                    <option value="italian" <?php
                                                                                            if (!empty($user_language)) {
                                                                                                if ($user_language == 'italian') {
                                                                                                    echo 'selected';
                                                                                                }
                                                                                            }
                                                                                            ?>><?php echo lang('italian'); ?>
                                                                    </option>
                                                                    <option value="portuguese" <?php
                                                                                                if (!empty($user_language)) {
                                                                                                    if ($user_language == 'portuguese') {
                                                                                                        echo 'selected';
                                                                                                    }
                                                                                                }
                                                                                                ?>><?php echo lang('portuguese'); ?>
                                                                    </option>

                                                                    <option value="turkish" <?php
                                                                                            if (!empty($user_language)) {
                                                                                                if ($user_language == 'turkish') {
                                                                                                    echo 'selected';
                                                                                                }
                                                                                            }
                                                                                            ?>><?php echo lang('turkish'); ?>
                                                                    </option>




                                                                </select>
                                                            </div>


                                                        <?php } ?>




                                                        <?php
                                                        $ion_user = $this->ion_auth->get_user_id();
                                                        $img_url = null;
                                                        if ($this->ion_auth->in_group(array('Patient'))) {
                                                            $img_url = $this->db->get_where('patient', array('ion_user_id' => $ion_user))->row();
                                                        } elseif ($this->ion_auth->in_group(array('Doctor'))) {
                                                            $img_url = $this->db->get_where('doctor', array('ion_user_id' => $ion_user))->row();
                                                        } elseif ($this->ion_auth->in_group(array('superadmin'))) {
                                                            $img_url = $this->db->get_where('superadmin', array('ion_user_id' => $ion_user))->row();
                                                        } elseif ($this->ion_auth->in_group(array('admin'))) {
                                                            $img_url = $this->db->get_where('users', array('id' => $ion_user))->row();
                                                        }
                                                        ?>



                                                        <div class="form-group">
                                                            <label class="col-sm-3"><?php echo lang('image'); ?> </label>
                                                            <div class="col-sm-9">
                                                                <div class="fileupload fileupload-new" data-provides="fileupload">
                                                                    <div class="fileupload-new thumbnail img_class fileupload-preview fileupload-exists thumbnail img_thumb">
                                                                        <img src="<?php echo (!empty($img_url) && !empty($img_url->img_url)) ? htmlspecialchars($img_url->img_url) : ''; ?>" id="img" class="img_thumb" height="100" alt="">
                                                                    </div>
                                                                    <div>
                                                                        <span class="btn btn-white btn-file">
                                                                            <span class="btn fileupload-new badge badge-secondary"><i class="fa fa-paper-clip"></i> <?php echo lang('select_image'); ?></span>
                                                                            <!-- <span class="fileupload-exists"><i class="fa fa-undo"></i> <?php echo lang('change'); ?></span> -->
                                                                            <input type="file" class="default" name="img_url" />
                                                                        </span>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>





                                                        <input type="hidden" name="id" value='<?php
                                                                                                if (!empty($profile->id)) {
                                                                                                    echo $profile->id;
                                                                                                }
                                                                                                ?>'>
                                                        <div class="form-group clearfix pt-3">
                                                            <button type="submit" name="submit" class="btn btn-primary float-right"><?php echo lang('submit'); ?></button>
                                                        </div>
                                                    </form>
                                                </div>
                                                <?php if (!$this->ion_auth->in_group(array('superadmin', 'admin'))) { ?>
                                                    <div id="email_notification" class="tab-pane">
                                                        <table class="table table-bordered table-hover" id="dt-profile" data-legacy-table="editable-sample">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th><?php echo lang('email_type'); ?></th>
                                                                    <th><?php echo lang('status'); ?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php if ($this->ion_auth->in_group(array('Patient'))) { ?>
                                                                    <tr>
                                                                        <td><?php echo lang('appointment'); ?> <?php echo lang('creation'); ?></td>
                                                                        <td>
                                                                            <select name="appointment_creation" id="appointment_creation" class="form-control col-sm-9 patient_email">
                                                                                <option value="Active" <?php echo (!empty($img_url) && isset($img_url->appointment_creation) && $img_url->appointment_creation === 'Active') ? 'selected' : ''; ?>><?php echo lang('active'); ?></option>
                                                                                <option value="Inactive" <?php echo (!empty($img_url) && isset($img_url->appointment_creation) && $img_url->appointment_creation === 'Inactive') ? 'selected' : ''; ?>><?php echo lang('inactive'); ?></option>
                                                                            </select>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><?php echo lang('appointment'); ?> <?php echo lang('confirmation'); ?></td>
                                                                        <td>
                                                                            <select name="appointment_confirmation" id="appointment_confirmation" class="form-control col-sm-9 patient_email">
                                                                                <option value="Active" <?php echo (!empty($img_url) && isset($img_url->appointment_confirmation) && $img_url->appointment_confirmation === 'Active') ? 'selected' : ''; ?>><?php echo lang('active'); ?></option>
                                                                                <option value="Inactive" <?php echo (!empty($img_url) && isset($img_url->appointment_confirmation) && $img_url->appointment_confirmation === 'Inactive') ? 'selected' : ''; ?>><?php echo lang('inactive'); ?></option>
                                                                            </select>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><?php echo lang('payment'); ?> <?php echo lang('confirmation'); ?></td>
                                                                        <td>
                                                                            <select name="payment_confirmation" id="payment_confirmation" class="form-control col-sm-9 patient_email">
                                                                                <option value="Active" <?php echo (!empty($img_url) && isset($img_url->payment_confirmation) && $img_url->payment_confirmation === 'Active') ? 'selected' : ''; ?>><?php echo lang('active'); ?></option>
                                                                                <option value="Inactive" <?php echo (!empty($img_url) && isset($img_url->payment_confirmation) && $img_url->payment_confirmation === 'Inactive') ? 'selected' : ''; ?>><?php echo lang('inactive'); ?></option>
                                                                            </select>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><?php echo lang('meeting_schedule'); ?></td>
                                                                        <td>
                                                                            <select name="meeting_schedule" id="meeting_schedule" class="form-control col-sm-9 patient_email">
                                                                                <option value="Active" <?php echo (!empty($img_url) && isset($img_url->meeting_schedule) && $img_url->meeting_schedule === 'Active') ? 'selected' : ''; ?>><?php echo lang('active'); ?></option>
                                                                                <option value="Inactive" <?php echo (!empty($img_url) && isset($img_url->meeting_schedule) && $img_url->meeting_schedule === 'Inactive') ? 'selected' : ''; ?>><?php echo lang('inactive'); ?></option>
                                                                            </select>
                                                                        </td>
                                                                    </tr>
                                                                    <tr class="d-none">
                                                                        <td colspan="2">
                                                                            <input type="hidden" value="<?php echo (!empty($img_url) && !empty($img_url->id)) ? (int) $img_url->id : ''; ?>" name="patient_id" id="patient_id">
                                                                        </td>
                                                                    </tr>
                                                                <?php } else { ?>
                                                                    <tr>
                                                                        <td><?php echo lang('appointment'); ?> <?php echo lang('confirmation'); ?></td>
                                                                        <td>
                                                                            <select name="appointment_confirmation" id="doctor_appointment_confirmation" class="form-control col-sm-9 doctor_email">
                                                                                <option value="Active" <?php echo (!empty($img_url) && isset($img_url->appointment_confirmation) && $img_url->appointment_confirmation === 'Active') ? 'selected' : ''; ?>><?php echo lang('active'); ?></option>
                                                                                <option value="Inactive" <?php echo (!empty($img_url) && isset($img_url->appointment_confirmation) && $img_url->appointment_confirmation === 'Inactive') ? 'selected' : ''; ?>><?php echo lang('inactive'); ?></option>
                                                                            </select>
                                                                        </td>
                                                                    </tr>
                                                                    <tr class="d-none">
                                                                        <td colspan="2">
                                                                            <input type="hidden" value="<?php echo (!empty($img_url) && !empty($img_url->id)) ? (int) $img_url->id : ''; ?>" name="doctor_id" id="doctor_id">
                                                                        </td>
                                                                    </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>

    <!-- /.content -->
</div>












<script src="common/js/codearistos.min.js"></script>
<script src="common/extranal/js/profile.js"></script>