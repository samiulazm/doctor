<link href="common/extranal/css/patient/medical_history.css" rel="stylesheet">

<?php
$CI = get_instance();
$emergency_keywords = array(
    'module' => lang('emergency'),
    'module_plural' => lang('emergencies'),
    'add_new' => lang('add_new'),
    'id' => lang('id'),
    'patient' => lang('patient'),
    'doctor' => lang('doctor'),
    'emergency_type' => lang('emergency_type'),
    'description' => lang('description'),
    'status' => lang('status'),
    'priority' => lang('priority'),
    'options' => lang('options'),
    'all' => lang('all'),
    'home' => lang('home'),
);
$page_title = $emergency_keywords['all'] . ' ' . $emergency_keywords['module_plural'];
?>

<div class="content-wrapper bg-light">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => $page_title,
        'icon' => 'fas fa-exclamation-triangle text-danger mr-2',
        'breadcrumbs' => array(
            array('label' => $emergency_keywords['home'], 'url' => 'home'),
            array('label' => $emergency_keywords['module'], 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase">
                                <?php echo $emergency_keywords['module']; ?>
                            </h3>
                            <a href="emergency/addNewView" class="btn btn-sm btn-danger">
                                <i class="fa fa-plus mr-1"></i>
                                <?php echo $emergency_keywords['add_new'] . ' ' . $emergency_keywords['module']; ?>
                            </a>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="emergency-table" style="width:100%">
                                    <thead class="thead-light">
                                    <tr>
                                        <th class="text-uppercase small"><?php echo $emergency_keywords['id']; ?></th>
                                        <th><?php echo $emergency_keywords['patient']; ?></th>
                                        <th><?php echo $emergency_keywords['doctor']; ?></th>
                                        <th><?php echo $emergency_keywords['emergency_type']; ?></th>
                                        <th><?php echo $emergency_keywords['description']; ?></th>
                                        <th><?php echo $emergency_keywords['status']; ?></th>
                                        <th><?php echo $emergency_keywords['priority']; ?></th>
                                        <th class="no-print"><?php echo $emergency_keywords['options']; ?></th>
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

<script>
    $(document).ready(function() {
        "use strict";

        var emergencyKeywords = {
            module: <?php echo json_encode($emergency_keywords['module']); ?>,
            modulePlural: <?php echo json_encode($emergency_keywords['module_plural']); ?>,
            id: <?php echo json_encode($emergency_keywords['id']); ?>,
            patient: <?php echo json_encode($emergency_keywords['patient']); ?>,
            doctor: <?php echo json_encode($emergency_keywords['doctor']); ?>,
            emergencyType: <?php echo json_encode($emergency_keywords['emergency_type']); ?>,
            description: <?php echo json_encode($emergency_keywords['description']); ?>,
            status: <?php echo json_encode($emergency_keywords['status']); ?>,
            priority: <?php echo json_encode($emergency_keywords['priority']); ?>,
            options: <?php echo json_encode($emergency_keywords['options']); ?>,
            all: <?php echo json_encode($emergency_keywords['all']); ?>,
            addNew: <?php echo json_encode($emergency_keywords['add_new']); ?>,
            records: <?php echo json_encode(lang('records')); ?>,
            search: <?php echo json_encode(lang('search')); ?>,
            first: <?php echo json_encode(lang('first')); ?>,
            last: <?php echo json_encode(lang('last')); ?>,
            next: <?php echo json_encode(lang('next')); ?>,
            previous: <?php echo json_encode(lang('previous')); ?>,
            noRecordsFound: <?php echo json_encode(lang('no_results_found')); ?>,
            noMatchingRecordsFound: <?php echo json_encode(lang('no_matching_records_found')); ?>,
        };

        var table = $("#emergency-table").DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            searchable: true,
            ajax: {
                url: "emergency/getEmergencyData",
                type: "POST",
            },
            scroller: {
                loadingIndicator: true,
            },
            dom: "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4 text-right'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                {
                    extend: "copyHtml5",
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] },
                    className: 'btn btn-sm btn-outline-secondary'
                },
                {
                    extend: "excelHtml5",
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] },
                    className: 'btn btn-sm btn-outline-success',
                    title: emergencyKeywords.modulePlural + '_Data_' + new Date().toISOString().split('T')[0]
                },
                {
                    extend: "csvHtml5",
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] },
                    className: 'btn btn-sm btn-outline-info',
                    title: emergencyKeywords.modulePlural + '_Data_' + new Date().toISOString().split('T')[0]
                },
                {
                    extend: "pdfHtml5",
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] },
                    className: 'btn btn-sm btn-outline-danger',
                    title: emergencyKeywords.modulePlural + '_Data_' + new Date().toISOString().split('T')[0],
                    orientation: 'landscape',
                    pageSize: 'A4'
                },
                {
                    extend: "print",
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] },
                    className: 'btn btn-sm btn-outline-dark',
                    title: emergencyKeywords.modulePlural + ' ' + emergencyKeywords.records + ' - ' + new Date().toLocaleDateString()
                }
            ],
            aLengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"],
            ],
            iDisplayLength: 25,
            order: [[0, "desc"]],
            language: {
                lengthMenu: "_MENU_",
                search: "_INPUT_",
                searchPlaceholder: emergencyKeywords.search + " " + emergencyKeywords.modulePlural.toLowerCase() + " " + emergencyKeywords.records.toLowerCase() + "...",
                processing: "<?php echo lang('loading'); ?> " + emergencyKeywords.modulePlural.toLowerCase() + " " + emergencyKeywords.records.toLowerCase() + "...",
                emptyTable: emergencyKeywords.noRecordsFound,
                zeroRecords: emergencyKeywords.noMatchingRecordsFound,
                info: "Showing _START_ to _END_ of _TOTAL_ " + emergencyKeywords.modulePlural.toLowerCase() + " " + emergencyKeywords.records.toLowerCase(),
                infoEmpty: "Showing 0 to 0 of 0 " + emergencyKeywords.modulePlural.toLowerCase() + " " + emergencyKeywords.records.toLowerCase(),
                infoFiltered: "(filtered from _MAX_ total " + emergencyKeywords.modulePlural.toLowerCase() + " " + emergencyKeywords.records.toLowerCase() + ")",
                paginate: {
                    first: emergencyKeywords.first,
                    last: emergencyKeywords.last,
                    next: emergencyKeywords.next,
                    previous: emergencyKeywords.previous
                }
            },
            drawCallback: function(settings) {
                if (settings.json && settings.json.data) {
                    console.log('Loaded ' + settings.json.data.length + ' ' + emergencyKeywords.modulePlural.toLowerCase() + ' ' + emergencyKeywords.records.toLowerCase());
                }
            }
        });

        table.buttons().container().appendTo(".custom_buttons");
    });
</script>
