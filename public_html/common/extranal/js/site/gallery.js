"use strict";

$(document).ready(function () {
    "use strict";
    $(".table").on("click", ".editbutton", function () {
        $("#loader").show();
        var iid = $(this).attr('data-id');
        $("#img").attr("src", "uploads/cardiology-patient-icon-vector-6244713.jpg");
        $.ajax({
            url: 'site/gallery/editGalleryByJason?id=' + encodeURIComponent(iid),
            method: 'GET',
            data: '',
            dataType: 'json',
            success: function (response) {
                if (!response || !response.gallery) {
                    return;
                }
                $('#editSlideForm').find('[name="id"]').val(response.gallery.id);
                $('#editSlideForm').find('[name="position"]').val(response.gallery.position);
                $('#editSlideForm').find('[name="status"]').val(response.gallery.status);
                if (typeof response.gallery.img !== 'undefined' && response.gallery.img !== '') {
                    $("#img").attr("src", response.gallery.img);
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
