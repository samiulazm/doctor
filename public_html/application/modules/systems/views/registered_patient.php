<?php
$ap_settings_saas_ui = $this->ion_auth->in_group('superadmin');
?>
<?php if ($ap_settings_saas_ui) : ?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/settings-saas-styles.css'); ?>">
<?php endif; ?>
<link href="common/extranal/css/systems/active_hospital.css" rel="stylesheet">



<div class="content-wrapper <?php echo $ap_settings_saas_ui ? 'ap-settings-saas bg-light' : 'bg-light'; ?>">
    <!-- Content Header (Page header) -->
    <section class="content-header <?php echo $ap_settings_saas_ui ? 'ap-settings-saas-hero shadow-none border-0 py-4' : ''; ?>">
        <div class="container-fluid">
            <?php if ($ap_settings_saas_ui) : ?>
            <div class="row align-items-center pl-1">
                <div class="col-12 col-lg-9">
                    <span class="ap-settings-saas-badge"><?php echo lang('superadmin'); ?> · SaaS · <?php echo lang('report-h'); ?></span>
                    <h1 class="display-4 font-weight-black mb-0">
                        <i class="fas fa-user-injured mr-2 ap-settings-saas-report-hero-icon"></i><?php echo lang('registered_patient'); ?>
                    </h1>
                    <nav aria-label="breadcrumb" class="mt-2">
                        <ol class="breadcrumb bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item"><?php echo lang('report-h'); ?></li>
                            <li class="breadcrumb-item active"><?php echo lang('registered_patient'); ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
            <?php else : ?>
            <div class="row my-2 pl-1">
                <div class="col-sm-6">
                    <h1 class="font-weight-bold"><i class="fas fa-user-injured mr-2"></i><?php echo lang('registered_patient'); ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                        <li class="breadcrumb-item active"><?php echo lang('department'); ?></li>
                    </ol>
                </div>
            </div>
            <?php endif; ?>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content <?php echo $ap_settings_saas_ui ? 'py-4' : ''; ?>">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card <?php echo $ap_settings_saas_ui ? 'ap-settings-saas-systems-report border-0 shadow' : ''; ?>">
                        <div class="card-header <?php echo $ap_settings_saas_ui ? 'ap-settings-saas-systems-card-hd' : ''; ?>">
                            <h3 class="card-title mb-0"><?php echo lang('All the patient information from all the hospitals'); ?></h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table class="table table-bordered table-hover" id="dt-systems" data-legacy-table="editable-sample">
                                <thead>
                                    <tr>
                                        <th><?php echo lang('patient_id'); ?></th>
                                        <th><?php echo lang('name'); ?></th>
                                        <th><?php echo lang('phone'); ?></th>
                                        <th><?php echo lang('hospital'); ?></th>
                                        <!-- <th><?php echo lang('option'); ?></th> -->
                                    </tr>
                                </thead>
                                <tbody>



                                </tbody>
                            </table>
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



<!--main content end-->
<!--footer start-->

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = "<?php echo $this->language; ?>";
</script>
<script src="common/extranal/js/systems/registered_patient.js"></script>
