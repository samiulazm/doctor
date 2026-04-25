"use strict";

$(document).ready(function () {
    "use strict";
    $(".table").on("click", ".editbutton", function () {
        $("#loader").show();
        var iid = $(this).attr('data-id');
        $("#img").attr("src", "uploads/cardiology-patient-icon-vector-6244713.jpg");
        $.ajax({
            url: 'site/slide/editSlideByJason?id=' + encodeURIComponent(iid),
            method: 'GET',
            data: '',
            dataType: 'json',
            success: function (response) {
                if (!response || !response.slide) {
                    return;
                }
                $('#editSlideForm').find('[name="id"]').val(response.slide.id);
                $('#editSlideForm').find('[name="title"]').val(response.slide.title);
                $('#editSlideForm').find('[name="text1"]').val(response.slide.text1);
                $('#editSlideForm').find('[name="text2"]').val(response.slide.text2);
                $('#editSlideForm').find('[name="text3"]').val(response.slide.text3);
                $('#editSlideForm').find('[name="position"]').val(response.slide.position);
                $('#editSlideForm').find('[name="status"]').val(response.slide.status);
                if (typeof response.slide.img_url !== 'undefined' && response.slide.img_url !== '') {
                    $("#img").attr("src", response.slide.img_url);
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
            { extend: 'copyHtml5', exportOptions: { columns: [1, 2, 3, 4, 5, 6] } },
            { extend: 'excelHtml5', exportOptions: { columns: [1, 2, 3, 4, 5, 6] } },
            { extend: 'csvHtml5', exportOptions: { columns: [1, 2, 3, 4, 5, 6] } },
            { extend: 'pdfHtml5', exportOptions: { columns: [1, 2, 3, 4, 5, 6] } },
            { extend: 'print', exportOptions: { columns: [1, 2, 3, 4, 5, 6] } }
        ],
        aLengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        iDisplayLength: -1,
        order: [[1, "asc"]],
        columnDefs: [
            { orderable: false, targets: [0, 7] }
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
