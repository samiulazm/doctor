"use strict";
var myEditor;
var myEditor3;
$(document).ready(function () {

    ClassicEditor
        .create(document.querySelector('#editor1'))
        .then(editor => {
            editor.ui.view.editable.element.style.height = '200px';
            myEditor = editor;
        })
        .catch(error => {
            console.error(error);
        });

    ClassicEditor
        .create(document.querySelector('#editor3'))
        .then(editor => {
            editor.ui.view.editable.element.style.height = '200px';
            myEditor3 = editor;
        })
        .catch(error => {
            console.error(error);
        });


});
$(document).ready(function () {
    "use strict";
    $(".table").on("click", ".editPrescription", function () {
        "use strict";
        $("#loader").show();
        var iid = $(this).attr('data-id');
        $.ajax({
            url: 'prescription/editPrescriptionByJason?id=' + iid,
            method: 'GET',
            data: '',
            dataType: 'json',
            success: function (response) {
                "use strict";
                var de = response.prescription.date * 1000;
                var d = new Date(de);
                var da = d.getDate() + '-' + (d.getMonth() + 1) + '-' + d.getFullYear();

                $('#prescriptionEditForm').find('[name="id"]').val(response.prescription.id).end();
                $('#prescriptionEditForm').find('[name="date"]').val(da).end();
                $('#prescriptionEditForm').find('[name="patient"]').val(response.prescription.patient).end();
                $('#prescriptionEditForm').find('[name="doctor"]').val(response.prescription.doctor).end();

                myEditor.setData(response.prescription.symptom);
                myEditor3.setData(response.prescription.note);

                $('.js-example-basic-single.doctor').val(response.prescription.doctor).trigger('change');
                $('.js-example-basic-single.patient').val(response.prescription.patient).trigger('change');
                $('#myModal5').modal('show');
            },
            complete: function () {
                $("#loader").hide();
            }
        })
    });
});



$(document).ready(function () {
    "use strict";
    var table = $('#editable-sample1').DataTable({
        responsive: true,
        //   dom: 'lfrBtip',

        "processing": true,
        "serverSide": true,
        "searchable": true,
        "ajax": {
            url: "prescription/getPrescriptionList",
            type: 'POST',
        },
        scroller: {
            loadingIndicator: true
        },
        dom: "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4 text-right'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",

        buttons: [
            { extend: 'copyHtml5', exportOptions: { columns: [0, 1, 2, 3, 4], } },
            { extend: 'excelHtml5', exportOptions: { columns: [0, 1, 2, 3, 4], } },
            { extend: 'csvHtml5', exportOptions: { columns: [0, 1, 2, 3, 4], } },
            { extend: 'pdfHtml5', exportOptions: { columns: [0, 1, 2, 3, 4], } },
            { extend: 'print', exportOptions: { columns: [0, 1, 2, 3, 4], } },
        ],
        aLengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        iDisplayLength: 100,
        "order": [[0, "desc"]],
        "language": {
            "lengthMenu": "_MENU_",
            search: "_INPUT_",
            searchPlaceholder: "Search...",
            "url": "common/assets/DataTables/languages/" + language + ".json"
        },
    });
    table.buttons().container().appendTo('.custom_buttons');
});

$(document).ready(function () {
    "use strict";
    $(".flashmessage").delay(3000).fadeOut(100);
});

// Quick View Modal Functionality
$(document).ready(function () {
    "use strict";

    var L = window.prescriptionQuickViewLang || {};
    function qv(key, fallback) {
        var v = L[key];
        return (v !== undefined && v !== null && v !== '') ? v : (fallback || key);
    }
    /** Escape text inserted into HTML (medicine rows, names, API errors). */
    function esc(s) {
        if (s === undefined || s === null) {
            return '';
        }
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    var currentPrescriptionId = null;

    // Handle Quick View button click
    $(document).on('click', '.quick-view-btn', function(e) {
        e.preventDefault();
        var prescriptionId = $(this).data('id');
        currentPrescriptionId = prescriptionId;

        // Show loading state
        $('#quickViewContent').html(`
            <div class="text-center">
                <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                <p class="mt-2">${qv('loading_prescription_details', 'Loading prescription details...')}</p>
            </div>
        `);
        
        // Fetch prescription data
        $.ajax({
            url: 'prescription/getPrescriptionForQuickView?id=' + prescriptionId,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    $('#quickViewContent').html(
                        '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle mr-2"></i>' +
                        esc(response.error) +
                        '</div>'
                    );
                    return;
                }
                
                // Build medicines list
                var medicinesHtml = '';
                if (response.medicines && response.medicines.length > 0) {
                    medicinesHtml = `
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>${qv('medicine', 'Medicine')}</th>
                                        <th>${qv('dosage', 'Dosage')}</th>
                                        <th>${qv('frequency', 'Frequency')}</th>
                                        <th>${qv('instruction', 'Instruction')}</th>
                                        <th>${qv('days', 'Days')}</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    response.medicines.forEach(function(medicine) {
                        medicinesHtml += `
                            <tr>
                                <td><strong>${esc(medicine.name)}</strong></td>
                                <td>${esc(medicine.dosage)}</td>
                                <td>${esc(medicine.frequency)}</td>
                                <td>${esc(medicine.instruction)}</td>
                                <td>${esc(medicine.days)}</td>
                            </tr>
                        `;
                    });
                    
                    medicinesHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;
                } else {
                    medicinesHtml = '<p class="text-muted">' + qv('no_medicines_prescribed', 'No medicines prescribed.') + '</p>';
                }
                
                // Build the complete modal content
                var modalContent = `
                    <div class="prescription-quick-view">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 font-weight-bold">
                                            <i class="fas fa-prescription mr-2 text-primary"></i>
                                            ${qv('prescription', 'Prescription')} #${response.prescription.id}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-group mb-2">
                                                    <label class="font-weight-bold text-muted">${qv('date', 'Date')}:</label>
                                                    <span class="ml-2">${response.formatted_date}</span>
                                                </div>
                                                <div class="info-group mb-2">
                                                    <label class="font-weight-bold text-muted">${qv('patient', 'Patient')}:</label>
                                                    <span class="ml-2">${esc(response.patient ? response.patient.name : response.prescription.patientname)}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-2">
                                                    <label class="font-weight-bold text-muted">${qv('doctor', 'Doctor')}:</label>
                                                    <span class="ml-2">${esc(response.doctor ? response.doctor.name : response.prescription.doctorname)}</span>
                                                </div>
                                                <div class="info-group mb-2">
                                                    <label class="font-weight-bold text-muted">${qv('patient_id', 'Patient ID')}:</label>
                                                    <span class="ml-2">${response.prescription.patient}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        ${response.prescription.symptom ? `
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 font-weight-bold">
                                            <i class="fas fa-stethoscope mr-2 text-info"></i>
                                            ${qv('history_and_symptoms', 'History / Symptoms')}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="symptom-content">${response.prescription.symptom}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        ` : ''}
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 font-weight-bold">
                                            <i class="fas fa-pills mr-2 text-success"></i>
                                            ${qv('prescribed_medicines', 'Prescribed Medicines')}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        ${medicinesHtml}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        ${response.prescription.note ? `
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 font-weight-bold">
                                            <i class="fas fa-sticky-note mr-2 text-warning"></i>
                                            ${qv('notes', 'Notes')}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="note-content">${response.prescription.note}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        ` : ''}
                        
                        ${response.prescription.advice ? `
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 font-weight-bold">
                                            <i class="fas fa-lightbulb mr-2 text-primary"></i>
                                            ${qv('advice', 'Advice')}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="advice-content">${response.prescription.advice}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                `;
                
                $('#quickViewContent').html(modalContent);
            },
            error: function() {
                $('#quickViewContent').html(
                    '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle mr-2"></i>' +
                    qv('quick_view_load_failed', 'Error loading prescription details. Please try again.') +
                    '</div>'
                );
            }
        });
    });
    
    // Handle Print button
    $('#printQuickView').on('click', function() {
        if (currentPrescriptionId) {
            window.open('prescription/viewPrescriptionPrint?id=' + currentPrescriptionId, '_blank');
        }
    });
    
    // Handle View Full button
    $('#viewFullPrescription').on('click', function() {
        if (currentPrescriptionId) {
            window.open('prescription/viewPrescription?id=' + currentPrescriptionId, '_blank');
        }
    });
});


