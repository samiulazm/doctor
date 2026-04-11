<?php
$ap_settings_saas_ui = $this->ion_auth->in_group('superadmin');
?>
<?php if ($ap_settings_saas_ui) : ?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/settings-saas-styles.css'); ?>">
<?php endif; ?>
<link href="common/extranal/css/service.css" rel="stylesheet">

<div class="content-wrapper <?php echo $ap_settings_saas_ui ? 'ap-settings-saas bg-light' : 'bg-light'; ?>">
    <!-- Content Header (Page header) -->
    <section class="content-header <?php echo $ap_settings_saas_ui ? 'ap-settings-saas-hero shadow-none border-0 py-4' : ''; ?>">
        <div class="container-fluid">
            <?php if ($ap_settings_saas_ui) : ?>
            <div class="row align-items-center pl-1">
                <div class="col-12 col-lg-8">
                    <span class="ap-settings-saas-badge"><?php echo lang('superadmin'); ?> · SaaS</span>
                    <h1 class="display-4 font-weight-black mb-0">
                        <i class="fas fa-star mr-2"></i><?php echo lang('reviews'); ?>
                    </h1>
                    <nav aria-label="breadcrumb" class="mt-2">
                        <ol class="breadcrumb bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item active"><?php echo lang('reviews'); ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
            <?php else : ?>
            <div class="row my-2 pl-1">
                <div class="col-sm-6">
                    <h1 class="font-weight-bold"><i class="fas fa-star mr-2"></i><?php echo lang('reviews'); ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                        <li class="breadcrumb-item active"><?php echo lang('reviews'); ?></li>
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
                    <div class="card <?php echo $ap_settings_saas_ui ? 'ap-settings-saas-service-shell border-0 shadow' : ''; ?>">
                        <div class="card-header <?php echo $ap_settings_saas_ui ? 'ap-settings-saas-service-card-hd d-flex flex-wrap align-items-center justify-content-between gap-2' : 'clearfix'; ?>">
                            <h3 class="card-title mb-0"><?php echo lang('All the reviews names for frontend website'); ?></h3>
                            <div class="<?php echo $ap_settings_saas_ui ? '' : 'float-right'; ?>">
                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#myModal">
                                    <i class="fa fa-plus-circle"></i> <?php echo lang('add_review'); ?>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table class="table table-bordered table-hover" id="editable-sample">
                                <thead>
                                    <tr>
                                        <th><?php echo lang('image'); ?></th>
                                        <th><?php echo lang('client_name'); ?></th>
                                        <th><?php echo lang('review'); ?></th>
                                        <th class="no-print"><?php echo lang('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>


                                    <?php foreach ($services as $service) { ?>
                                        <tr class="">
                                            <td class="img_td"><img class="rounded" width="50px" src="<?php echo $service->img_url; ?>"></td>
                                            <td> <?php echo $service->title; ?></td>
                                            <td><?php echo $service->description; ?></td>
                                            <td class="no-print d-flex gap-1">
                                                <a type="button" class="btn btn-primary btn-sm btn_width editbutton" title="<?php echo lang('edit'); ?>" data-bs-toggle="modal" data-id="<?php echo $service->id; ?>"><i class="fa fa-edit"> </i></a>
                                                <a class="btn btn-danger btn-sm btn_width delete_button" title="<?php echo lang('delete'); ?>" href="service/delete?id=<?php echo $service->id; ?>" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"> </i></a>
                                            </td>
                                        </tr>
                                    <?php } ?>




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

<!-- Add Service Modal-->
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold"> <?php echo lang('add_review'); ?></h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form role="form" action="service/addNew" class="clearfix" method="post" enctype="multipart/form-data">
                    <div class="">
                        <div class="form-group">
                            <label for="exampleInputEmail1"> <?php echo lang('client_name'); ?> &ast;</label>
                            <input type="text" class="form-control form-control-lg" name="title" value='' required="">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1"> <?php echo lang('review'); ?> &ast;</label>
                            <input type="text" class="form-control form-control-lg" name="description" value='' placeholder="" required="">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1"> <?php echo lang('image'); ?></label>
                            <input type="file" class="form-control form-control-lg" name="img_url" required="">
                        </div>

                        <div class="form-group">
                            <button type="submit" name="submit" class="btn btn-info float-right"><?php echo lang('submit'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- Add Service Modal-->







<!-- Edit Event Modal-->
<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold"> <?php echo lang('edit_review'); ?></h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form role="form" id="editServiceForm" class="clearfix" action="service/addNew" method="post" enctype="multipart/form-data">
                    <div class="">
                        <div class="form-group">
                            <label for="exampleInputEmail1"> <?php echo lang('client_name'); ?> &ast;</label>
                            <input type="text" class="form-control form-control-lg" name="title" value='' required="">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1"> <?php echo lang('review'); ?> &ast;</label>
                            <input type="text" class="form-control form-control-lg" name="description" value='' placeholder="" required="">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1"> <?php echo lang('image'); ?></label>
                            <input type="file" class="form-control form-control-lg" name="img_url">
                        </div>
                        <input type="hidden" name="id" value=''>

                        <div class="form-group">
                            <button type="submit" name="submit" class="btn btn-info float-right"><?php echo lang('submit'); ?></button>
                        </div>
                    </div>
                </form>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- /Edit Event Modal -->

</div><!-- /.content-wrapper -->

<!--main content end-->

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = "<?php echo $this->language; ?>";
</script>
<script src="common/extranal/js/service.js"></script>