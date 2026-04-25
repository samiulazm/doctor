<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('invoices'),
        'icon' => 'fas fa-money-bill-wave text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('invoices'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('invoices'); ?></h3>
                            <a href="finance/addPaymentView" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus mr-1"></i> <?php echo lang('add_new'); ?> <?php echo lang('invoice'); ?>
                            </a>
                        </div>
                        <div class="card-body p-4">
                            <div class="col-md-4 mb-4 pl-0">
                                <div class="input-group input-large" data-date="13/07/2013" data-date-format="mm/dd/yyyy">
                                    <input type="text" class="form-control form-control-lg shadow-sm default-date-picker" name="date_from" id="date_from" value="" placeholder="<?php echo lang('date_from'); ?>" readonly="">
                                    <span class="input-group-addon mx-2"></span>
                                    <input type="text" class="form-control form-control-lg shadow-sm default-date-picker" name="date_to" id="date_to" value="" placeholder="<?php echo lang('date_to'); ?>" readonly="">
                                </div>
                            </div>

                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle text-sm datatables mb-0" id="editable-sample3" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th><?php echo lang('invoice'); ?> #</th>
                                            <th><?php echo lang('patient'); ?></th>
                                            <th><?php echo lang('date'); ?></th>
                                            <th><?php echo lang('total'); ?></th>
                                            <th><?php echo lang('vat'); ?></th>
                                            <th><?php echo lang('discount'); ?></th>
                                            <th><?php echo lang('grand_total'); ?></th>
                                            <th><?php echo lang('paid'); ?> <?php echo lang('amount'); ?></th>
                                            <th><?php echo lang('due'); ?></th>
                                            <th><?php echo lang('from'); ?></th>
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













<!-- Include Global Modal Styles -->
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/global-modal-styles.css'); ?>">

<div class="modal fade modal-enhanced" id="editPaymentModal" role="dialog" aria-labelledby="editPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPaymentModalLabel">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Payment
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- The edit payment form will be loaded here via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo lang('close'); ?></button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>





<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
</script>
<!-- <script defer type="text/javascript" src="common/assets/DataTables/datatables.min.js"></script> -->
<script src="common/extranal/js/finance/payments.js"></script>
<script>
    $(document).ready(function() {

        $('#date_from').on('change', function() {
            var date_from = $(this).val();
            var date_to = $('#date_to').val();
            var date_from_split = date_from.split('-');
            var date_from_new = date_from_split[1] + '/' + date_from_split[0] + '/' + date_from_split[2]
            if (date_to != '' || date_to != null) {
                var date_to_split = date_to.split('-');
                var date_to_new = date_to_split[1] + '/' + date_to_split[0] + '/' + date_to_split[2];
            }
            if (date_to != '' || date_to != null) {
                if (Date.parse(date_to_new) <= Date.parse(date_from_new)) {

                    alert('Select a Valid Date. End Date should be Greater than Start Date');
                    $(this).val("");
                } else {
                    $('#editable-sample3').DataTable().destroy().clear();
                    "use strict";
                    var table = $('#editable-sample3').DataTable({
                        responsive: true,
                        //   dom: 'lfrBtip',

                        "processing": true,
                        "serverSide": true,
                        "searchable": true,
                        "ajax": {
                            url: "finance/getPayment?start_date=" + date_from + "&end_date=" + date_to,
                            type: 'POST',
                        },
                        scroller: {
                            loadingIndicator: true
                        },
                        dom: "<'row mb-1'<'col-md-3'l><'col-sm-5 text-center'B><'col-sm-4'f>>" +
                            "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-5'i><'col-sm-7'p>>",

                        buttons: [{
                                extend: 'copyHtml5',
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                                }
                            },
                            {
                                extend: 'excelHtml5',
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                                }
                            },
                            {
                                extend: 'csvHtml5',
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                                }
                            },
                            {
                                extend: 'pdfHtml5',
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                                }
                            },
                            {
                                extend: 'print',
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                                }
                            },
                        ],
                        aLengthMenu: [
                            [10, 25, 50, 100, -1],
                            [10, 25, 50, 100, "All"]
                        ],
                        iDisplayLength: 100,

                        "order": [
                            [0, "desc"]
                        ],

                        "language": {
                            "lengthMenu": "_MENU_",
                            search: "_INPUT_",
                            "url": "common/assets/DataTables/languages/" + language + ".json"
                        }
                    });
                    table.buttons().container().appendTo('.custom_buttons');
                }
            }
        })
        $('#date_to').on('change', function() {
            var date_to = $(this).val();
            var date_from = $('#date_from').val();

            var date_to_split = date_to.split('-');
            var date_to_new = date_to_split[1] + '/' + date_to_split[0] + '/' + date_to_split[2];
            if (date_from != '' || date_from != null) {
                var date_from_split = date_from.split('-');
                var date_from_new = date_from_split[1] + '/' + date_from_split[0] + '/' + date_from_split[2];

            }
            if (date_from != '' || date_from != null) {
                if (Date.parse(date_to_new) <= Date.parse(date_from_new)) {

                    alert('Select a Valid Date. End Date should be Greater than Start Date');
                    $(this).val("");
                } else {
                    $('#editable-sample3').DataTable().destroy().clear();
                    "use strict";
                    var table = $('#editable-sample3').DataTable({
                        responsive: true,
                        //   dom: 'lfrBtip',

                        "processing": true,
                        "serverSide": true,
                        "searchable": true,
                        "ajax": {
                            url: "finance/getPayment?start_date=" + date_from + "&end_date=" + date_to,
                            type: 'POST',
                        },
                        scroller: {
                            loadingIndicator: true
                        },
                        dom: "<'row mb-1'<'col-md-3'l><'col-sm-5 text-center'B><'col-sm-4'f>>" +
                            "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-5'i><'col-sm-7'p>>",

                        buttons: [{
                                extend: 'copyHtml5',
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                                }
                            },
                            {
                                extend: 'excelHtml5',
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                                }
                            },
                            {
                                extend: 'csvHtml5',
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                                }
                            },
                            {
                                extend: 'pdfHtml5',
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                                }
                            },
                            {
                                extend: 'print',
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                                }
                            },
                        ],
                        aLengthMenu: [
                            [10, 25, 50, 100, -1],
                            [10, 25, 50, 100, "All"]
                        ],
                        iDisplayLength: 100,

                        "order": [
                            [0, "desc"]
                        ],

                        "language": {
                            "lengthMenu": "_MENU_",
                            search: "_INPUT_",
                            "url": "common/assets/DataTables/languages/" + language + ".json"
                        }
                    });
                    table.buttons().container().appendTo('.custom_buttons');
                }
            }
        })
    })
</script>
