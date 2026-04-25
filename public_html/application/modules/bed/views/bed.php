<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
if (!isset($bed)) {
    $bed = (object) array();
}
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('bed'),
        'icon' => 'fas fa-bed text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('bed'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-end mb-3">
                <a data-toggle="modal" href="#myModal" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus mr-1"></i> <?php echo lang('add_new'); ?> <?php echo lang('bed'); ?>
                </a>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('All the bed list'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle text-sm datatables mb-0" id="editable-sample" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-uppercase"><?php echo lang('bed_id'); ?></th>
                                            <th><?php echo lang('description'); ?></th>
                                            <th><?php echo lang('status'); ?></th>
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

<!-- Add Bed Modal-->
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold"> <?php echo lang('add_new_bed'); ?> </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body">
                <form role="form" action="bed/addBed" class="clearfix row" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="form-group col-md-12 d-flex">
                        <label for="exampleInputEmail1"><?php echo lang('bed_category'); ?> &#42;</label>
                        <select class="form-control form-control-lg" name="category" value='' required="">
                            <?php foreach ($categories as $category) { ?>
                                <option value="<?php echo $category->category; ?>" <?php
                                                                                    if (!empty($bed->category)) {
                                                                                        if ($category->category == $bed->category) {
                                                                                            echo 'selected';
                                                                                        }
                                                                                    }
                                                                                    ?>> <?php echo $category->category; ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-12 d-flex">
                        <label for="exampleInputEmail1"><?php echo lang('bed_number'); ?> &#42;</label>
                        <input type="text" class="form-control form-control-lg" name="number" value='' placeholder="" required="">
                    </div>
                    <div class="form-group col-md-12 d-flex">
                        <label for="exampleInputEmail1"><?php echo lang('description'); ?> &#42;</label>
                        <input type="text" class="form-control form-control-lg" name="description" value='' placeholder="" required="">
                    </div>

                    <div class="form-group col-md-12 d-flex">
                        <button type="submit" name="submit" class="btn btn-info float-right"><?php echo lang('submit'); ?></button>
                    </div>

                </form>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<!-- Edit Bed Modal-->
<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold"> <?php echo lang('edit_bed'); ?> </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body">
                <form role="form" id="editBedForm" class="clearfix row" action="bed/addBed" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="form-group col-md-12 d-flex">
                        <label for="exampleInputEmail1"><?php echo lang('bed_category'); ?> &#42;</label>
                        <select class="form-control form-control-lg" name="category" value='' required="">
                            <?php foreach ($categories as $category) { ?>
                                <option value="<?php echo $category->category; ?>" <?php
                                                                                    if (!empty($bed->category)) {
                                                                                        if ($category->category == $bed->category) {
                                                                                            echo 'selected';
                                                                                        }
                                                                                    }
                                                                                    ?>> <?php echo $category->category; ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-12 d-flex">
                        <label for="exampleInputEmail1"><?php echo lang('bed_number'); ?> &#42;</label>
                        <input type="text" class="form-control form-control-lg" name="number" value='' placeholder="" required="">
                    </div>
                    <div class="form-group col-md-12 d-flex">
                        <label for="exampleInputEmail1"><?php echo lang('description'); ?> &#42;</label>
                        <input type="text" class="form-control form-control-lg" name="description" value='' placeholder="" required="">
                    </div>

                    <input type="hidden" name="id" value=''>

                    <div class="form-group col-md-12 d-flex">
                        <button type="submit" name="submit" class="btn btn-info float-right"><?php echo lang('submit'); ?></button>
                    </div>

                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<script src="common/js/codearistos.min.js"></script>

<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
</script>

<script src="common/extranal/js/bed/bed.js"></script>
