<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<link href="common/extranal/css/pharmacist.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('pharmacist'),
        'icon' => 'fas fa-user-md text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('pharmacist'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-end mb-3">
                <a data-toggle="modal" data-target="#myModal" href="#myModal" class="btn btn-sm btn-success">
                    <i class="fa fa-plus mr-1"></i> <?php echo lang('add_new'); ?> <?php echo lang('pharmacist'); ?>
                </a>
            </div>
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('pharmacist'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle text-sm datatables mb-0" id="dt-pharmacist" data-legacy-table="editable-sample" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-uppercase"><?php echo lang('image'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('name'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('email'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('address'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('phone'); ?></th>
                                            <th class="text-uppercase no-print"><?php echo lang('options'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pharmacists as $pharmacist) { ?>
                                            <tr>
                                                <td class="align-middle">
                                                    <img class="img-fluid rounded-circle" style="width: 50px; height: 50px;" src="<?php echo html_escape($pharmacist->img_url); ?>" alt="">
                                                </td>
                                                <td class="align-middle"><?php echo html_escape($pharmacist->name); ?></td>
                                                <td class="align-middle"><?php echo html_escape($pharmacist->email); ?></td>
                                                <td class="align-middle"><?php echo html_escape($pharmacist->address); ?></td>
                                                <td class="align-middle"><?php echo html_escape($pharmacist->phone); ?></td>
                                                <td class="align-middle no-print">
                                                    <a type="button" class="btn btn-info btn-sm editbutton" title="<?php echo lang('edit'); ?>" data-id="<?php echo (int) $pharmacist->id; ?>">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a class="btn btn-danger btn-sm delete_button" href="pharmacist/delete?id=<?php echo (int) $pharmacist->id; ?>" title="<?php echo lang('delete'); ?>" onclick="return confirm('<?php echo lang('are_you_sure_you_want_to_delete_this_item'); ?>');">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
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

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="addPharmacistModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h2 class="modal-title text-white font-weight-800" id="addPharmacistModalLabel"><?php echo lang('add_new_pharmacist'); ?></h2>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body bg-light p-4">
                <form role="form" action="pharmacist/addNew" class="clearfix" method="post" enctype="multipart/form-data">
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
                                <input type="text" class="form-control form-control-lg shadow-sm" name="name" required="">
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('email'); ?> <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-lg shadow-sm" name="email" required="">
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('password'); ?> <span class="text-danger">*</span></label>
                                <input type="password" class="form-control form-control-lg shadow-sm" name="password" required="">
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('address'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="address" required="">
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('phone'); ?> <span class="text-danger">*</span></label>
                                <input type="number" class="form-control form-control-lg shadow-sm" name="phone" required="">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-5">
                        <div class="col-12 mb-4">
                            <h3 class="border-bottom border-danger pb-3 text-uppercase font-weight-900">
                                <i class="fas fa-user-md mr-3 text-danger"></i><?php echo lang('profile_info'); ?>
                            </h3>
                        </div>
                        <div class="col-md-12 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('profile'); ?> <span class="text-danger">*</span></label>
                                <textarea class="form-control shadow-sm ckeditor" id="editor1" name="profile" rows="10"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-5">
                        <div class="col-12 mb-4">
                            <h3 class="border-bottom border-info pb-3 text-uppercase font-weight-900">
                                <i class="fas fa-camera mr-3 text-info"></i><?php echo lang('images'); ?>
                            </h3>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('signature'); ?> <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="signature" required>
                                    <label class="custom-file-label shadow-sm"><?php echo lang('choose_file'); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('image'); ?></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="img_url">
                                    <label class="custom-file-label shadow-sm"><?php echo lang('choose_file'); ?></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block shadow-lg py-3">
                                <i class="fas fa-user-plus mr-3"></i><?php echo lang('submit'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="editPharmacistModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h2 class="modal-title text-white font-weight-800" id="editPharmacistModalLabel"><?php echo lang('edit_pharmacist'); ?></h2>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body bg-light p-4">
                <form role="form" id="editPharmacistForm" class="clearfix" action="pharmacist/addNew" method="post" enctype="multipart/form-data">
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
                                <input type="text" class="form-control form-control-lg shadow-sm" name="name" required="">
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('email'); ?> <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-lg shadow-sm" name="email" required="">
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
                                <input type="text" class="form-control form-control-lg shadow-sm" name="address" required="">
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('phone'); ?> <span class="text-danger">*</span></label>
                                <input type="number" class="form-control form-control-lg shadow-sm" name="phone" required="">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-5">
                        <div class="col-12 mb-4">
                            <h3 class="border-bottom border-danger pb-3 text-uppercase font-weight-900">
                                <i class="fas fa-user-md mr-3 text-danger"></i><?php echo lang('profile_info'); ?>
                            </h3>
                        </div>
                        <div class="col-md-12 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('profile'); ?></label>
                                <textarea class="form-control shadow-sm ckeditor" id="editor3" name="profile" rows="10"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-5">
                        <div class="col-12 mb-4">
                            <h3 class="border-bottom border-info pb-3 text-uppercase font-weight-900">
                                <i class="fas fa-camera mr-3 text-info"></i><?php echo lang('images'); ?>
                            </h3>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('signature'); ?> <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="signature">
                                    <label class="custom-file-label shadow-sm"><?php echo lang('choose_file'); ?></label>
                                </div>
                                <div class="mt-2">
                                    <img src="" id="signature" height="100" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('image'); ?></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="img_url">
                                    <label class="custom-file-label shadow-sm"><?php echo lang('choose_file'); ?></label>
                                </div>
                                <div class="mt-2">
                                    <img src="" id="img" height="100" alt="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="id" id="id_value" value="">

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

<script src="common/js/codearistos.min.js"></script>
<script src="common/assets/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
</script>
<script src="common/extranal/js/pharmacist.js"></script>
