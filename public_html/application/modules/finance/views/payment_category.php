<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<link href="common/extranal/css/finance/payment_category.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('invoice_items_lab_tests'),
        'icon' => 'fas fa-procedures text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('payment_procedures'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                <h3 class="card-title h6 mb-0 text-muted text-uppercase flex-grow-1"><?php echo lang('All the invoice items / lab tests name and related informations'); ?></h3>
                                <a href="finance/addPaymentCategoryView" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus mr-1"></i> <?php echo lang('create_invoice_items_lab_tests'); ?>
                                </a>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="small text-muted mb-1 d-block"><?php echo lang('category'); ?></label>
                                    <select class="form-control form-control-sm category js-example-basic-single">
                                        <option value="all"><?php echo lang('select'); ?> <?php echo lang('category'); ?></option>
                                        <option value="all"><?php echo lang('all'); ?></option>
                                        <?php foreach ($paycategories as $paycategory) { ?>
                                            <option value="<?php echo $paycategory->id; ?>">
                                                <?php echo $paycategory->category; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle text-sm datatables mb-0" id="dt-finance" data-legacy-table="editable-sample" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-uppercase"><?php echo lang('name'); ?></th>
                                            <th><?php echo lang('code'); ?></th>
                                            <th><?php echo lang('service_point'); ?></th>
                                            <th><?php echo lang('default'); ?> <?php echo lang('price'); ?> ( <?php echo $settings->currency; ?> )</th>
                                            <th><?php echo lang('doctors_commission'); ?></th>
                                            <th><?php echo lang('type'); ?></th>
                                            <?php if ($this->ion_auth->in_group(array('admin', 'Accountant'))) { ?>
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















  <!--main content end-->
  <!--footer start-->
  <!-- Add Patient Modal-->
  <style>
      .ck-editor__editable:not(.ck-editor__nested-editable) {
          min-height: 400px !important;
      }
  </style>
  <div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
          <div class="modal-content">
              <div class="modal-header">
                  <h4 class="modal-title font-weight-bold"> <?php echo lang('add_template'); ?></h4>
                  <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
              </div>
              <div class="modal-body row">
                  <form role="form" id="addTemplate" action="finance/addPaymentProccedureTemplate" class="clearfix" method="post" enctype="multipart/form-data">
                      <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">

                      <div class="form-group">
                          <label class="control-label"><?php echo lang('template'); ?></label>
                          <textarea class="form-control ckeditor" id="editor1" name="report" value="" rows="50" cols="20"></textarea>
                      </div>

                      <input type="hidden" name="id">

                      <section class="col-md-12">
                          <button type="submit" name="submit" class="btn btn-info float-right"><?php echo lang('submit'); ?></button>
                      </section>
                  </form>

              </div>
          </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
  </div> 
  <!-- Add Patient Modal-->

  <script src="common/js/codearistos.min.js"></script>
  <script type="text/javascript">
      var language = <?php echo json_encode($this->language); ?>;
  </script>
  <script src="common/assets/tinymce/tinymce.min.js"></script>
  <script src="common/extranal/js/finance/payment_category.js"></script>
