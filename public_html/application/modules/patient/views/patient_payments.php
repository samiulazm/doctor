<?php
$CI = get_instance();
if (!isset($groups) || !is_array($groups)) {
    $groups = array();
}
if (!isset($doctors) || $doctors === null) {
    $doctors = array();
}
if (!isset($patient)) {
    $patient = null;
}
$can_due = $this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist', 'Laboratorist'));
?>
<div class="content-wrapper bg-light">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('patient') . ' ' . lang('payments'),
        'icon' => 'fas fa-dollar-sign text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('patients'), 'url' => 'patient'),
            array('label' => lang('patient') . ' ' . lang('payments'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('patient'); ?> <?php echo lang('payments'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="editable-sample" style="width:100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th><?php echo lang('patient_id'); ?></th>
                                            <th><?php echo lang('name'); ?></th>
                                            <th><?php echo lang('phone'); ?></th>
                                            <?php if ($can_due) { ?>
                                                <th><?php echo lang('due_balance'); ?></th>
                                            <?php } ?>
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






<!--main content end-->
<!--footer start-->








<!-- Add Patient Modal-->
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold"><?php echo lang('register_new_patient'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body row">
                <form role="form" action="patient/addNew" class="clearfix" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">

                    <div class="form-group col-md-5">
                        <label for="exampleInputEmail1"> <?php echo lang('name'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="name" value='' placeholder="">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="exampleInputEmail1"> <?php echo lang('email'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="email" value='' placeholder="">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="exampleInputEmail1"> <?php echo lang('password'); ?></label>
                        <input type="password" class="form-control form-control-lg" name="password" placeholder="">
                    </div>



                    <div class="form-group col-md-4">
                        <label for="exampleInputEmail1"> <?php echo lang('address'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="address" value='' placeholder="">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="exampleInputEmail1"> <?php echo lang('phone'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="phone" value='' placeholder="">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="exampleInputEmail1"> <?php echo lang('sex'); ?></label>
                        <select class="form-control form-control-lg m-bot15" name="sex" value=''>

                            <option value="Male"<?php if ($patient && !empty($patient->sex) && $patient->sex == 'Male') { echo ' selected'; } ?>><?php echo lang('male'); ?></option>
                            <option value="Female"<?php if ($patient && !empty($patient->sex) && $patient->sex == 'Female') { echo ' selected'; } ?>><?php echo lang('female'); ?></option>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label><?php echo lang('birth_date'); ?></label>
                        <input class="form-control form-control-inline input-medium default-date-picker" type="text" name="birthdate" value="" placeholder="" required="" onkeypress="return false;">
                    </div>


                    <div class="form-group col-md-6">
                        <label for="exampleInputEmail1"> <?php echo lang('blood_group'); ?></label>
                        <select class="form-control form-control-lg m-bot15" name="bloodgroup" value=''>
                            <?php foreach ($groups as $group) { ?>
                                <option value="<?php echo htmlspecialchars($group->group, ENT_QUOTES, 'UTF-8'); ?>"<?php
                                if ($patient && !empty($patient->bloodgroup) && $group->group == $patient->bloodgroup) {
                                    echo ' selected';
                                }
                                ?>> <?php echo htmlspecialchars($group->group, ENT_QUOTES, 'UTF-8'); ?> </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="exampleInputEmail1"> <?php echo lang('doctor'); ?></label>
                        <select class="form-control js-example-basic-single" name="doctor" value=''>
                            <option value=""> </option>
                            <?php foreach ($doctors as $doctor) { ?>
                                <option value="<?php echo (int) $doctor->id; ?>"><?php echo htmlspecialchars($doctor->name, ENT_QUOTES, 'UTF-8'); ?> </option>
                            <?php } ?>
                        </select>
                    </div>



                    <div class="form-group last col-md-8">
                        <label class="control-label">Image Upload</label>
                        <div class="">
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-new thumbnail img_class">
                                    <img src="" alt="" />

                                </div>
                                <div class="fileupload-preview fileupload-exists thumbnail img_thumb"></div>
                                <div>
                                    <span class="btn btn-white btn-file">
                                        <span class="fileupload-new"><i class="fa fa-paper-clip"></i> Select image</span>
                                        <span class="fileupload-exists"><i class="fa fa-undo"></i> Change</span>
                                        <input type="file" class="default" name="img_url" />
                                    </span>
                                    <a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload"><i class="fa fa-trash"></i> Remove</a>
                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="form-group last col-md-4">
                        <div class="saved_image">
                            <video id="video" width="200" height="200" autoplay></video>
                            <div class="snap" id="snap">Capture Photo</div>
                            <canvas id="canvas" width="200" height="200"></canvas>
                            Right click on the captured image and save. Then select the saved image from the left side's Select Image button.
                        </div>

                    </div>


                    <div class="form-group col-md-3">
                        <input type="checkbox" name="sms" value="sms"> <?php echo lang('send_sms') ?><br>
                    </div>


                    <section class="col-md-12">
                        <button type="submit" name="submit" class="btn btn-info float-right"><?php echo lang('submit'); ?></button>
                    </section>
                </form>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- Add Patient Modal-->







<!-- Edit Patient Modal-->
<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold"><?php echo lang('edit_patient'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body row">
                <form role="form" id="editPatientForm" action="patient/addNew" class="clearfix" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">

                    <div class="form-group col-md-5">
                        <label for="exampleInputEmail1"> <?php echo lang('name'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="name" value='' placeholder="">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="exampleInputEmail1"> <?php echo lang('email'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="email" value='' placeholder="">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="exampleInputEmail1"> <?php echo lang('change'); ?><?php echo lang('password'); ?></label>
                        <input type="password" class="form-control form-control-lg" name="password" placeholder="">
                    </div>



                    <div class="form-group col-md-4">
                        <label for="exampleInputEmail1"> <?php echo lang('address'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="address" value='' placeholder="">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="exampleInputEmail1"> <?php echo lang('phone'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="phone" value='' placeholder="">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="exampleInputEmail1"> <?php echo lang('sex'); ?></label>
                        <select class="form-control form-control-lg m-bot15" name="sex" value=''>

                            <option value="Male"<?php if ($patient && !empty($patient->sex) && $patient->sex == 'Male') { echo ' selected'; } ?>><?php echo lang('male'); ?></option>
                            <option value="Female"<?php if ($patient && !empty($patient->sex) && $patient->sex == 'Female') { echo ' selected'; } ?>><?php echo lang('female'); ?></option>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label><?php echo lang('birth_date'); ?></label>
                        <input class="form-control form-control-inline input-medium default-date-picker" type="text" name="birthdate" value="" placeholder="" readonly="">
                    </div>


                    <div class="form-group col-md-6">
                        <label for="exampleInputEmail1"> <?php echo lang('blood_group'); ?></label>
                        <select class="form-control form-control-lg m-bot15" name="bloodgroup" value=''>
                            <?php foreach ($groups as $group) { ?>
                                <option value="<?php echo htmlspecialchars($group->group, ENT_QUOTES, 'UTF-8'); ?>"<?php
                                if ($patient && !empty($patient->bloodgroup) && $group->group == $patient->bloodgroup) {
                                    echo ' selected';
                                }
                                ?>> <?php echo htmlspecialchars($group->group, ENT_QUOTES, 'UTF-8'); ?> </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="exampleInputEmail1"> <?php echo lang('doctor'); ?></label>
                        <select class="form-control js-example-basic-single doctor" name="doctor" value=''>
                            <option value=""> </option>
                            <?php foreach ($doctors as $doctor) { ?>
                                <option value="<?php echo (int) $doctor->id; ?>"><?php echo htmlspecialchars($doctor->name, ENT_QUOTES, 'UTF-8'); ?> </option>
                            <?php } ?>
                        </select>
                    </div>



                    <div class="form-group last col-md-8">
                        <label class="control-label">Image Upload</label>
                        <div class="">
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-new thumbnail img_class">
                                    <img src="" id="img" alt="" />

                                </div>
                                <div class="fileupload-preview fileupload-exists thumbnail img_thumb"></div>
                                <div>
                                    <span class="btn btn-white btn-file">
                                        <span class="fileupload-new"><i class="fa fa-paper-clip"></i> Select image</span>
                                        <span class="fileupload-exists"><i class="fa fa-undo"></i> Change</span>
                                        <input type="file" class="default" name="img_url" />
                                    </span>
                                    <a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload"><i class="fa fa-trash"></i> Remove</a>
                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="form-group last col-md-4">
                        <div class="saved_image">
                            <video id="video" width="200" height="200" autoplay></video>
                            <div class="snap" id="snap">Capture Photo</div>
                            <canvas id="canvas" width="200" height="200"></canvas>
                            Right click on the captured image and save. Then select the saved image from the left side's Select Image button.
                        </div>
                    </div>








                    <div class="form-group col-md-3">
                        <input type="checkbox" name="sms" value="sms"> <?php echo lang('send_sms') ?><br>
                    </div>

                    <input type="hidden" name="id" value=''>
                    <input type="hidden" name="p_id" value="<?php echo ($patient && !empty($patient->patient_id)) ? htmlspecialchars($patient->patient_id, ENT_QUOTES, 'UTF-8') : ''; ?>">





                    <section class="col-md-12">
                        <button type="submit" name="submit" class="btn btn-info float-right"><?php echo lang('submit'); ?></button>
                    </section>

                </form>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
</div>
<!-- Edit Patient Modal-->

<script src="common/js/codearistos.min.js"></script>

<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
</script>

<script src="common/extranal/js/patient/patient_payments.js"></script>