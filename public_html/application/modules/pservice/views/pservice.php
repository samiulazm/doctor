<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
if (!isset($pservice)) {
    $pservice = (object) array();
}
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('pservice'),
        'icon' => 'fas fa-concierge-bell text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('pservice'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-end mb-3">
                <a data-toggle="modal" href="#myModal" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus mr-1"></i> <?php echo lang('add_new'); ?>
                </a>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('All the Patient service names and pricing for the In-Patient Department (IPD)'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle text-sm datatables mb-0" id="editable-sample1" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-uppercase"><?php echo lang('no'); ?></th>
                                            <th><?php echo lang('service'); ?> <?php echo lang('code'); ?></th>
                                            <th><?php echo lang('alpha_code'); ?></th>
                                            <th><?php echo lang('service'); ?> <?php echo lang('name'); ?></th>
                                            <th><?php echo lang('price'); ?></th>
                                            <th><?php echo lang('active'); ?></th>
                                            <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                                                <th class="no-print"><?php echo lang('options'); ?></th>
                                            <?php } ?>
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

<!-- Add Pservice Modal-->
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold"> <?php echo lang('add_pservice'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body">
                <form role="form" action="pservice/addNew" class="clearfix row" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">

                    <div class="form-group col-md-6 d-flex">
                        <label for="exampleInputEmail1"> <?php echo lang('service'); ?> <?php echo lang('name'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="name" id="exampleInputEmail1" value='<?php
                                                                                                                            if (!empty($pservice->name)) {
                                                                                                                                echo $pservice->name;
                                                                                                                            }
                                                                                                                            ?>' placeholder="" required="">
                    </div>

                    <div class="form-group col-md-6 d-flex">
                        <label for="exampleInputEmail1"> <?php echo lang('service'); ?> <?php echo lang('code'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="code" id="exampleInputEmail1" value='<?php
                                                                                                                            if (!empty($pservice->code)) {
                                                                                                                                echo $pservice->code;
                                                                                                                            }
                                                                                                                            ?>' placeholder="" required="">
                    </div>
                    <div class="form-group col-md-6 d-flex">
                        <label for="exampleInputEmail1"> <?php echo lang('alpha_code'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="alpha_code" id="exampleInputEmail1" value='<?php
                                                                                                                                    if (!empty($pservice->alpha_code)) {
                                                                                                                                        echo $pservice->alpha_code;
                                                                                                                                    }
                                                                                                                                    ?>' placeholder="">
                    </div>
                    <div class="form-group col-md-6 d-flex">
                        <label for="exampleInputEmail1"> <?php echo lang('price'); ?></label>
                        <input type="text" class="form-control form-control-lg" min="0" name="price" id="exampleInputEmail1" value='<?php
                                                                                                                                    if (!empty($pservice->price)) {
                                                                                                                                        echo $pservice->price;
                                                                                                                                    }
                                                                                                                                    ?>' placeholder="" required="">
                    </div>


                    <div class="form-group col-md-6 d-flex">

                        <input type="checkbox" class="" name="active" id="exampleInputEmail1" value='1' <?php
                                                                                                        if (!empty($pservice->id)) {
                                                                                                            if ($pservice->active == "1") {
                                                                                                                echo "checked";
                                                                                                            }
                                                                                                        }
                                                                                                        ?>>
                        <label for="exampleInputEmail1"> <?php echo lang('active'); ?></label>
                    </div>

                    <div class="form-group col-md-12">
                        <button type="submit" name="submit" class="btn btn-info float-right"> <?php echo lang('submit'); ?></button>
                    </div>

                </form>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<!-- Edit Pservice Modal-->
<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold"> <?php echo lang('edit_pservice'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body">
                <form role="form" id="editPserviceForm" class="clearfix row" action="pservice/addNew" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">

                    <div class="form-group col-md-6 d-flex">
                        <label for="exampleInputEmail1"> <?php echo lang('service'); ?> <?php echo lang('name'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="name" id="exampleInputEmail1" value='<?php
                                                                                                                            if (!empty($pservice->name)) {
                                                                                                                                echo $pservice->name;
                                                                                                                            }
                                                                                                                            ?>' placeholder="" required="">
                    </div>

                    <div class="form-group col-md-6 d-flex">
                        <label for="exampleInputEmail1"> <?php echo lang('service'); ?> <?php echo lang('code'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="code" id="exampleInputEmail1" value='<?php
                                                                                                                            if (!empty($pservice->code)) {
                                                                                                                                echo $pservice->code;
                                                                                                                            }
                                                                                                                            ?>' placeholder="" required="">
                    </div>
                    <div class="form-group col-md-6 d-flex">
                        <label for="exampleInputEmail1"> <?php echo lang('alpha_code'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="alpha_code" id="exampleInputEmail1" value='<?php
                                                                                                                                    if (!empty($pservice->alpha_code)) {
                                                                                                                                        echo $pservice->alpha_code;
                                                                                                                                    }
                                                                                                                                    ?>' placeholder="">
                    </div>
                    <div class="form-group col-md-6 d-flex">
                        <label for="exampleInputEmail1"> <?php echo lang('price'); ?></label>
                        <input type="text" class="form-control form-control-lg" min="0" name="price" id="exampleInputEmail1" value='<?php
                                                                                                                                    if (!empty($pservice->price)) {
                                                                                                                                        echo $pservice->price;
                                                                                                                                    }
                                                                                                                                    ?>' placeholder="" required="">
                    </div>


                    <div class="form-group col-md-6 d-flex">

                        <input type="checkbox" class="" name="active" id="exampleInputEmail1" value='1' <?php
                                                                                                        if (!empty($pservice->id)) {
                                                                                                            if ($pservice->active == "1") {
                                                                                                                echo "checked";
                                                                                                            }
                                                                                                        }
                                                                                                        ?>>
                        <label for="exampleInputEmail1"> <?php echo lang('active'); ?></label>
                    </div>



                    <input type="hidden" name="id" value='<?php
                                                            if (!empty($pservice->id)) {
                                                                echo $pservice->id;
                                                            }
                                                            ?>'>
                    <div class="form-group col-md-12">
                        <button type="submit" name="submit" class="btn btn-info float-right"> <?php echo lang('submit'); ?></button>
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

<script src="common/extranal/js/bed/patient_service.js"></script>
