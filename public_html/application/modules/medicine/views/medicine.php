<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
if (!isset($medicine)) {
    $medicine = (object) array();
}
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('medicine'),
        'icon' => 'fas fa-pills text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('medicine'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-end mb-3">
                <a data-toggle="modal" data-target="#myModal" href="#myModal" class="btn btn-sm btn-success">
                    <i class="fas fa-plus-circle mr-1"></i> <?php echo lang('add_new'); ?>
                </a>
            </div>
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('all'); ?> <?php echo lang('medicine'); ?> — <?php echo lang('names_and_related_information'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle text-sm datatables mb-0" id="editable-sample1" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-uppercase"><?php echo lang('id'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('name'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('category'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('store_box'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('p_price'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('s_price'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('quantity'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('generic_name'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('company'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('effects'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('expiry_date'); ?></th>
                                            <th class="text-uppercase no-print"><?php echo lang('options'); ?></th>
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

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="addMedicineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="addMedicineModalLabel"><?php echo lang('add_medicine'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body">
                <div class="card border-0">
                    <div class="card-body p-3">
                        <?php echo validation_errors(); ?>
                        <form role="form" action="medicine/addNewMedicine" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="text-uppercase text-sm"><?php echo lang('name'); ?> <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-lg" name="name" value='' required="">
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="text-uppercase text-sm"><?php echo lang('category'); ?> <span class="text-danger">*</span></label>
                                        <select class="form-control form-control-lg select2" name="category" required="">
                                            <?php foreach ($categories as $category) { ?>
                                                <option value="<?php echo html_escape($category->category); ?>" <?php
                                                                                                    if (!empty($medicine->category) && $category->category == $medicine->category) {
                                                                                                        echo 'selected';
                                                                                                    }
                                                                                                    ?>><?php echo html_escape($category->category); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="text-uppercase text-sm"><?php echo lang('p_price'); ?> <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control form-control-lg" name="price" value='' required="">
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="text-uppercase text-sm"><?php echo lang('s_price'); ?> <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control form-control-lg" name="s_price" value='' required="">
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="text-uppercase text-sm"><?php echo lang('quantity'); ?> <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control form-control-lg" name="quantity" value='' required="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="text-uppercase text-sm"><?php echo lang('generic_name'); ?> <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-lg" name="generic" value='' required="">
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="text-uppercase text-sm"><?php echo lang('company'); ?></label>
                                        <input type="text" class="form-control form-control-lg" name="company" value=''>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="text-uppercase text-sm"><?php echo lang('effects'); ?></label>
                                        <input type="text" class="form-control form-control-lg" name="effects" value=''>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="text-uppercase text-sm"><?php echo lang('store_box'); ?></label>
                                        <input type="text" class="form-control form-control-lg" name="box" value=''>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="text-uppercase text-sm"><?php echo lang('expiry_date'); ?> <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-lg default-date-picker readonly" name="e_date" value='' required="">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block"><?php echo lang('submit'); ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="editMedicineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="editMedicineModalLabel"><?php echo lang('edit_medicine'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body">
                <div class="card border-0">
                    <div class="card-body p-3">
                        <?php echo validation_errors(); ?>
                        <form role="form" id="editMedicineForm" action="medicine/addNewMedicine" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="text-uppercase text-sm"><?php echo lang('name'); ?> <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-lg" name="name" value='' required="">
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="text-uppercase text-sm"><?php echo lang('category'); ?> <span class="text-danger">*</span></label>
                                        <select class="form-control form-control-lg select2" name="category" required="">
                                            <?php foreach ($categories as $category) { ?>
                                                <option value="<?php echo html_escape($category->category); ?>" <?php
                                                                                                    if (!empty($medicine->category) && $category->category == $medicine->category) {
                                                                                                        echo 'selected';
                                                                                                    }
                                                                                                    ?>><?php echo html_escape($category->category); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="text-uppercase text-sm"><?php echo lang('p_price'); ?> <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control form-control-lg" name="price" value='' required="">
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="text-uppercase text-sm"><?php echo lang('s_price'); ?> <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control form-control-lg" name="s_price" value='' required="">
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="text-uppercase text-sm"><?php echo lang('quantity'); ?> <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control form-control-lg" name="quantity" value='' required="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="text-uppercase text-sm"><?php echo lang('generic_name'); ?> <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-lg" name="generic" value='' required="">
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="text-uppercase text-sm"><?php echo lang('company'); ?></label>
                                        <input type="text" class="form-control form-control-lg" name="company" value=''>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="text-uppercase text-sm"><?php echo lang('effects'); ?></label>
                                        <input type="text" class="form-control form-control-lg" name="effects" value=''>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="text-uppercase text-sm"><?php echo lang('store_box'); ?></label>
                                        <input type="text" class="form-control form-control-lg" name="box" value=''>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="text-uppercase text-sm"><?php echo lang('expiry_date'); ?> <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-lg default-date-picker readonly" name="e_date" value='' required="">
                                    </div>
                                </div>
                                <input type="hidden" name="id" value=''>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block"><?php echo lang('submit'); ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal3" role="dialog" aria-labelledby="loadMedicineModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="loadMedicineModalLabel"><?php echo lang('load_medicine'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body">
                <form role="form" id="editMedicineForm1" class="clearfix" action="medicine/load" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label><?php echo lang('add_quantity'); ?> &ast;</label>
                        <input type="number" step="0.01" class="form-control form-control-lg" name="qty" value='' placeholder="" required="">
                    </div>
                    <input type="hidden" name="id" value=''>
                    <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-info float-right"><?php echo lang('submit'); ?></button>
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
<script src="common/extranal/js/medicine/medicine.js"></script>
