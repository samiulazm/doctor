"use strict";

$(document).ready(function () {
    "use strict";
    $(".table").on("click", ".editbutton", function () {
        $("#loader").show();
        var iid = $(this).attr('data-id');
        $.ajax({
            url: 'site/service/editServiceByJason?id=' + encodeURIComponent(iid),
            method: 'GET',
            data: '',
            dataType: 'json',
            success: function (response) {
                if (!response || !response.service) {
                    return;
                }
                $('#editServiceForm').find('[name="id"]').val(response.service.id);
                $('#editServiceForm').find('[name="title"]').val(response.service.title);
                $('#editServiceForm').find('[name="description"]').val(response.service.description);
                if (typeof response.service.img_url !== 'undefined' && response.service.img_url !== '') {
                    $("#img").attr("src", response.service.img_url);
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
            { extend: 'copyHtml5', exportOptions: { columns: [1, 2] } },
            { extend: 'excelHtml5', exportOptions: { columns: [1, 2] } },
            { extend: 'csvHtml5', exportOptions: { columns: [1, 2] } },
            { extend: 'pdfHtml5', exportOptions: { columns: [1, 2] } },
            { extend: 'print', exportOptions: { columns: [1, 2] } }
        ],
        aLengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        iDisplayLength: -1,
        order: [[1, "asc"]],
        columnDefs: [
            { orderable: false, targets: [0, 3] }
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
