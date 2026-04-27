<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('bed_categories'),
        'icon' => 'fas fa-bed text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('bed_categories'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-end mb-3">
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal">
                    <i class="fa fa-plus-circle mr-1"></i> <?php echo lang('add_new'); ?>
                </button>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle text-sm datatables mb-0" id="dt-bed" data-legacy-table="editable-sample" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th><?php echo lang('category'); ?></th>
                                            <th><?php echo lang('description'); ?></th>
                                            <th class="no-print"><?php echo lang('options'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($categories as $category) { ?>
                                            <tr>
                                                <td class="align-middle"><?php echo $category->category; ?></td>
                                                <td class="align-middle"><?php echo $category->description; ?></td>
                                                <td class="align-middle no-print">
                                                    <a type="button" class="btn btn-info btn-sm editbutton" title="<?php echo lang('edit'); ?>" data-toggle="modal" data-id="<?php echo $category->id; ?>">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a class="btn btn-danger btn-sm delete_button" href="bed/deleteBedCategory/delete?id=<?php echo $category->id; ?>" title="<?php echo lang('delete'); ?>" onclick="return confirm('Are you sure you want to delete this item?');">
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

<!-- Add Bed Category Modal-->
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white border-0">
                <h2 class="modal-title h5 font-weight-bold mb-0"><?php echo lang('add_bed_category'); ?></h2>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body bg-light p-4">
                <form role="form" action="bed/addCategory" class="clearfix" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="row mb-5">
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('category'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="category" required="">
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('description'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="description" required="">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block shadow-lg py-3">
                                <i class="fas fa-plus-circle mr-3"></i><?php echo lang('submit'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Bed Category Modal-->
<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white border-0">
                <h2 class="modal-title h5 font-weight-bold mb-0"><?php echo lang('edit_bed_category'); ?></h2>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body bg-light p-4">
                <form role="form" id="editBedCategoryForm" class="clearfix" action="bed/addCategory" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="row mb-5">
                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('category'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="category" required="">
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('description'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="description" required="">
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="id" value=''>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block shadow-lg py-3">
                                <i class="fas fa-edit mr-3"></i><?php echo lang('submit'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
</script>
<script src="common/extranal/js/bed/bed_category.js"></script>
