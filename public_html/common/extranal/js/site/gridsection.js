"use strict";

$(document).ready(function () {
    "use strict";
    $(".table").on("click", ".editbutton", function () {
        $("#loader").show();
        var iid = $(this).attr('data-id');
        $("#img").attr("src", "uploads/cardiology-patient-icon-vector-6244713.jpg");
        $.ajax({
            url: 'site/gridsection/editGridsectionByJason?id=' + encodeURIComponent(iid),
            method: 'GET',
            data: '',
            dataType: 'json',
            success: function (response) {
                if (!response || !response.gridsection) {
                    return;
                }
                $('#editSlideForm').find('[name="id"]').val(response.gridsection.id);
                $('#editSlideForm').find('[name="title"]').val(response.gridsection.title);
                $('#editSlideForm').find('[name="category"]').val(response.gridsection.category);
                $('#editSlideForm').find('[name="description"]').val(response.gridsection.description);
                $('#editSlideForm').find('[name="position"]').val(response.gridsection.position);
                $('#editSlideForm').find('[name="status"]').val(response.gridsection.status);
                if (typeof response.gridsection.img !== 'undefined' && response.gridsection.img !== '') {
                    $("#img").attr("src", response.gridsection.img);
                }
                $('#myModal2').modal('show');
            },
            complete: function () {
                $("#loader").hide();
            }
        });
    });
});

$(document).ready(function () {
    "use strict";
    var table = $('#editable-sample').DataTable({
        responsive: true,
        dom: "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4 text-right'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copyHtml5', exportOptions: { columns: [1, 2, 3, 4, 5] } },
            { extend: 'excelHtml5', exportOptions: { columns: [1, 2, 3, 4, 5] } },
            { extend: 'csvHtml5', exportOptions: { columns: [1, 2, 3, 4, 5] } },
            { extend: 'pdfHtml5', exportOptions: { columns: [1, 2, 3, 4, 5] } },
            { extend: 'print', exportOptions: { columns: [1, 2, 3, 4, 5] } }
        ],
        aLengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        iDisplayLength: -1,
        order: [[1, "asc"]],
        columnDefs: [
            { orderable: false, targets: [0, 6] }
        ],
        language: {
            lengthMenu: "_MENU_",
            search: "_INPUT_",
            searchPlaceholder: "Search...",
            url: "common/assets/DataTables/languages/" + language + ".json"
        }
    });
    table.buttons().container()
        .appendTo('.custom_buttons');
});

$(document).ready(function () {
    "use strict";
    $(".flashmessage").delay(3000).fadeOut(100);
});
