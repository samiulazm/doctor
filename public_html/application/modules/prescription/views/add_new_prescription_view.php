<?php
$current_user = $this->ion_auth->get_user_id();
if ($this->ion_auth->in_group('Doctor')) {
    $doctor_id = $this->db->get_where('doctor', array('ion_user_id' => $current_user))->row()->id;
    $doctordetails = $this->db->get_where('doctor', array('id' => $doctor_id))->row();
}
if (!isset($prescription) || $prescription === null) {
    $prescription = new stdClass();
}
if (!isset($embed)) {
    $embed = false;
}
if (!isset($preselect_patient)) {
    $preselect_patient = null;
}
?>

<link href="common/extranal/css/prescription/add_new_prescription_view.css" rel="stylesheet">



<div class="content-wrapper bg-light<?php echo !empty($embed) ? ' ml-0' : ''; ?>">
    <section class="content-header<?php echo !empty($embed) ? ' py-2 bg-white border-bottom mb-0' : ' py-4 bg-white shadow-sm'; ?>">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="<?php echo !empty($embed) ? 'col-12' : 'col-sm-6'; ?>">
                    <?php if (!empty($embed)) : ?>
                        <h2 class="h5 font-weight-bold mb-0">
                            <i class="fas fa-prescription mr-2 text-primary"></i>
                            <?php
                            if (!empty($prescription->id)) {
                                echo lang('edit_prescription');
                            } else {
                                echo lang('add_prescription');
                            }
                            ?>
                            <span class="badge badge-secondary small ml-1">Consultation room</span>
                        </h2>
                    <?php else : ?>
                    <h1 class="display-4 font-weight-black mb-0">
                        <i class="fas fa-prescription mr-3 text-primary"></i>
                        <?php
                        if (!empty($prescription->id))
                            echo lang('edit_prescription');
                        else
                            echo lang('add_prescription');
                        ?>
                    </h1>
                    <?php endif; ?>
                </div>
                <?php if (empty($embed)) : ?>
                <div class="col-sm-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb float-sm-right bg-transparent">
                            <li class="breadcrumb-item"><a href="home" class="text-primary"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item"><a href="prescription" class="text-primary"><?php echo lang('prescription'); ?></a></li>
                            <li class="breadcrumb-item active font-weight-bold"><?php
                                                                                if (!empty($prescription->id))
                                                                                    echo lang('edit_prescription');
                                                                                else
                                                                                    echo lang('add_prescription');
                                                                                ?></li>
                        </ol>
                    </nav>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="content<?php echo !empty($embed) ? ' py-2' : ' py-5'; ?>">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card shadow-lg border-0">

                        <div class="card-body bg-light p-4">
                            <?php echo validation_errors(); ?>
                            <form id="addForm" role="form" action="prescription/addNewPrescription" class="clearfix" method="post" enctype="multipart/form-data">
                                <?php if (!empty($embed)) { ?>
                                    <input type="hidden" name="embed" value="1">
                                    <input type="hidden" name="print_after" value="0" id="embed_print_after">
                                <?php } ?>
                                <div class="row">
                                    <!-- Left Column -->
                                    <div class="col-md-6">
                                        <div class="card shadow-sm border-0 mb-4">
                                            <div class="card-body">
                                                <!-- Basic Information -->
                                                <div class="row mb-5">
                                                    <div class="col-12 mb-4">
                                                        <h3 class="border-bottom border-primary pb-3 text-uppercase font-weight-900">
                                                            <i class="fas fa-info-circle mr-3 text-primary"></i><?php echo lang('basic_information'); ?>
                                                        </h3>
                                                    </div>

                                                    <div class="col-md-12 mb-4">
                                                        <div class="form-group">
                                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('date'); ?> <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control form-control-lg shadow-sm default-date-picker" autocomplete="off" name="date" value='<?php
                                                                                                                                                                                        if (!empty($setval)) {
                                                                                                                                                                                            echo set_value('date');
                                                                                                                                                                                        }
                                                                                                                                                                                        if (!empty($prescription->date)) {
                                                                                                                                                                                            echo date('d-m-Y', $prescription->date);
                                                                                                                                                                                        } else {
                                                                                                                                                                                            echo date('d-m-Y');
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>' required="">
                                                        </div>
                                                    </div>

                                                    <?php if (!$this->ion_auth->in_group('Doctor')) { ?>
                                                        <div class="col-md-12 mb-4">
                                                            <div class="form-group">
                                                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('doctor'); ?> <span class="text-danger">*</span></label>
                                                                <select class="form-control form-control-lg shadow-sm" id="doctorchoose" name="doctor" required>
                                                                    <?php if (!empty($prescription->doctor)) { ?>
                                                                        <option value="<?php echo $doctors->id; ?>" selected="selected"><?php echo $doctors->name; ?> - (<?php echo lang('id'); ?> : <?php echo $doctors->id; ?>)</option>
                                                                    <?php } ?>
                                                                    <?php
                                                                    if (!empty($setval)) {
                                                                        $doctordetails1 = $this->db->get_where('doctor', array('id' => set_value('doctor')))->row();
                                                                    ?>
                                                                        <option value="<?php echo $doctordetails1->id; ?>" selected="selected"><?php echo $doctordetails1->name; ?> -(<?php echo lang('id'); ?> : <?php echo $doctordetails1->id; ?>)</option>
                                                                    <?php }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    <?php } else { ?>
                                                        <div class="col-md-12 mb-4">
                                                            <div class="form-group">
                                                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('doctor'); ?></label>
                                                                <?php if (!empty($prescription->doctor)) { ?>
                                                                    <select class="form-control form-control-lg shadow-sm" name="doctor">
                                                                        <option value="<?php echo $doctors->id; ?>" selected="selected"><?php echo $doctors->name; ?> - (<?php echo lang('id'); ?> : <?php echo $doctors->id; ?>)</option>
                                                                    </select>
                                                                <?php } else { ?>
                                                                    <select class="form-control form-control-lg shadow-sm" id="doctorchoose1" name="doctor">
                                                                        <?php if (!empty($prescription->doctor)) { ?>
                                                                            <option value="<?php echo $doctors->id; ?>" selected="selected"><?php echo $doctors->name; ?> - (<?php echo lang('id'); ?> : <?php echo $doctors->id; ?>)</option>
                                                                        <?php } ?>
                                                                        <?php if (!empty($doctordetails)) { ?>
                                                                            <option value="<?php echo $doctordetails->id; ?>" selected="selected"><?php echo $doctordetails->name; ?> - (<?php echo lang('id'); ?> : <?php echo $doctordetails->id; ?>)</option>
                                                                        <?php } ?>
                                                                        <?php
                                                                        if (!empty($setval)) {
                                                                            $doctordetails1 = $this->db->get_where('doctor', array('id' => set_value('doctor')))->row();
                                                                        ?>
                                                                            <option value="<?php echo $doctordetails1->id; ?>" selected="selected"><?php echo $doctordetails1->name; ?> - (<?php echo lang('id'); ?> : <?php echo $doctordetails->id; ?>)</option>
                                                                        <?php }
                                                                        ?>
                                                                    </select>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>

                                                    <div class="col-md-12 mb-4">
                                                        <div class="form-group">
                                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('patient'); ?> <span class="text-danger">*</span></label>
                                                            <select class="form-control form-control-lg shadow-sm" id="patientchoose" name="patient" required="">
                                                                <?php if (!empty($prescription->patient)) {
                                                                    if (empty($patients->age)) {
                                                                        $dateOfBirth = $patients->birthdate;
                                                                        if (empty($dateOfBirth)) {
                                                                            $age[0] = '0';
                                                                        } else {
                                                                            $today = date("Y-m-d");
                                                                            $diff = date_diff(date_create($dateOfBirth), date_create($today));
                                                                            $age[0] = $diff->format('%y');
                                                                        }
                                                                    } else {
                                                                        $age = explode('-', $patients->age);
                                                                    }
                                                                ?>
                                                                    <option value="<?php echo $patients->id; ?>" selected="selected"><?php echo $patients->name; ?> ( <?php echo lang('id'); ?>: <?php echo $patients->id; ?> - <?php echo lang('phone'); ?>: <?php echo $patients->phone; ?> - <?php echo lang('age'); ?>: <?php echo $age[0]; ?> ) </option>
                                                                <?php } ?>
                                                                <?php
                                                                if (empty($prescription->patient) && !empty($preselect_patient)) {
                                                                    $pd = $preselect_patient;
                                                                    if (empty($pd->age)) {
                                                                        $dateOfBirth = $pd->birthdate;
                                                                        if (empty($dateOfBirth)) {
                                                                            $age = array('0');
                                                                        } else {
                                                                            $today = date('Y-m-d');
                                                                            $diff = date_diff(date_create($dateOfBirth), date_create($today));
                                                                            $age = array($diff->format('%y'));
                                                                        }
                                                                    } else {
                                                                        $age = explode('-', $pd->age);
                                                                    }
                                                                ?>
                                                                    <option value="<?php echo (int) $pd->id; ?>" selected="selected"><?php echo htmlspecialchars($pd->name); ?> ( <?php echo lang('id'); ?>: <?php echo (int) $pd->id; ?> - <?php echo lang('phone'); ?>: <?php echo htmlspecialchars((string) $pd->phone); ?> - <?php echo lang('age'); ?>: <?php echo $age[0]; ?> ) </option>
                                                                <?php } ?>
                                                                <?php
                                                                if (!empty($setval)) {
                                                                    $patientdetails = $this->db->get_where('patient', array('id' => set_value('patient')))->row();
                                                                    if (empty($patientdetails->age)) {
                                                                        $dateOfBirth = $patientdetails->birthdate;
                                                                        if (empty($dateOfBirth)) {
                                                                            $age[0] = '0';
                                                                        } else {
                                                                            $today = date("Y-m-d");
                                                                            $diff = date_diff(date_create($dateOfBirth), date_create($today));
                                                                            $age[0] = $diff->format('%y');
                                                                        }
                                                                    } else {
                                                                        $age = explode('-', $patientdetails->age);
                                                                    }
                                                                ?>
                                                                    <option value="<?php echo $patientdetails->id; ?>" selected="selected"><?php echo $patientdetails->name; ?> ( <?php echo lang('id'); ?>: <?php echo $patientdetails->id; ?> - <?php echo lang('phone'); ?>: <?php echo $patientdetails->phone; ?> - <?php echo lang('age'); ?>: <?php echo $age[0]; ?> ) </option>
                                                                <?php }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 mb-4">
                                                        <div class="form-group">
                                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('history'); ?></label>
                                                            <textarea class="form-control shadow-sm" id="editor1" name="symptom" rows="3"><?php
                                                                                                                                            if (!empty($setval)) {
                                                                                                                                                echo set_value('symptom');
                                                                                                                                            }
                                                                                                                                            if (!empty($prescription->symptom)) {
                                                                                                                                                echo $prescription->symptom;
                                                                                                                                            }
                                                                                                                                            ?></textarea>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 mb-4">
                                                        <div class="form-group">
                                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('note'); ?></label>
                                                            <textarea class="form-control shadow-sm ckeditor" id="editor2" name="note" rows="3"><?php
                                                                                                                                                if (!empty($setval)) {
                                                                                                                                                    echo set_value('note');
                                                                                                                                                }
                                                                                                                                                if (!empty($prescription->note)) {
                                                                                                                                                    echo $prescription->note;
                                                                                                                                                }
                                                                                                                                                ?></textarea>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 mb-4">
                                                        <div class="form-group">
                                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('advice'); ?></label>
                                                            <textarea class="form-control shadow-sm ckeditor" id="editor3" name="advice" rows="3"><?php
                                                                                                                                                    if (!empty($setval)) {
                                                                                                                                                        echo set_value('advice');
                                                                                                                                                    }
                                                                                                                                                    if (!empty($prescription->advice)) {
                                                                                                                                                        echo $prescription->advice;
                                                                                                                                                    }
                                                                                                                                                    ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Column -->
                                    <div class="col-md-6">
                                        <div class="card shadow-sm border-0">
                                            <div class="card-body">


                                                <!-- Medicine Information -->
                                                <div class="row mb-5">
                                                    <div class="col-12 mb-4">
                                                        <h3 class="border-bottom border-danger pb-3 text-uppercase font-weight-900">
                                                            <i class="fas fa-pills mr-3 text-danger"></i><?php echo lang('medicine_information'); ?>
                                                        </h3>

                                                    </div>

                                                    <div class="col-md-12 medicine_block">
                                                        <div class="form-group">
                                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('medicine'); ?></label>
                                                            <div class="medicine_div">
                                                                <?php if (empty($prescription->medicine)) { ?>
                                                                    <select class="form-control form-control-lg shadow-sm medicinee" id="my_select1_disabled" name="category">
                                                                    </select>
                                                                <?php } else { ?>
                                                                    <select name="category" class="form-control form-control-lg shadow-sm medicinee" multiple="multiple" id="my_select1_disabled">
                                                                        <?php
                                                                        if (!empty($prescription->medicine)) {
                                                                            $prescription_medicine = explode('###', $prescription->medicine);
                                                                            foreach ($prescription_medicine as $key => $value) {
                                                                                $prescription_medicine_extended = explode('***', $value);
                                                                                $medicine = $this->medicine_model->getMedicineById($prescription_medicine_extended[0]);
                                                                        ?>
                                                                                <option value="<?php echo $medicine->id . '*' . $medicine->name; ?>" <?php echo 'data-dosage="' . $prescription_medicine_extended[1] . '"' . 'data-frequency="' . $prescription_medicine_extended[2] . '"data-days="' . $prescription_medicine_extended[3] . '"data-instruction="' . $prescription_medicine_extended[4] . '"'; ?> selected="selected">
                                                                                    <?php echo $medicine->name; ?>
                                                                                </option>
                                                                        <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                <?php } ?>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12 medicine_block mt-4">
                                                            <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('selected'); ?> <?php echo lang('medicine'); ?></label>
                                                            <div class="medicine row"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <input type="hidden" name="admin" value='admin'>
                                                <input type="hidden" name="id" value='<?php
                                                                                        if (!empty($prescription->id)) {
                                                                                            echo $prescription->id;
                                                                                        }
                                                                                        ?>'>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="d-flex justify-content-end">
                                                            <button type="submit" name="submit" class="btn btn-primary btn-lg px-4">
                                                                <i class="fas fa-save mr-2"></i>
                                                                <?php echo $prescription->id ? lang('update') : lang('submit'); ?>
                                                            </button>
                                                            <a href="prescription/viewPrescription?id=<?php echo $prescription->id; ?>" type="button" class="btn btn-outline-primary btn-lg px-4 ml-2" id="print">
                                                                <i class="fas fa-print mr-2"></i>
                                                                <?php echo lang('print'); ?>
                                                            </a>
                                                        </div>
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
        </div>
    </section>
</div>













<!--main content end-->
<!--footer start-->



<script src="common/js/codearistos.min.js"></script>
<script src="common/assets/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
    var select_medicine = "<?php echo lang('medicine'); ?>";
</script>
<script type="text/javascript">
    var select_doctor = "<?php echo lang('select_doctor'); ?>";
</script>
<script type="text/javascript">
    var select_patient = "<?php echo lang('select_patient'); ?>";
</script>
<script type="text/javascript">
    var language = "<?php echo $this->language; ?>";
</script>
<script src="common/extranal/js/prescription/add_new_prescription_view.js"></script>
