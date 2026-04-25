"use strict";
$(document).ready(function () {
    if (!$("#editable-sample1").length) {
        return;
    }
    var table = $("#editable-sample1").DataTable({
        responsive: true,
        dom:
            "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4 text-right'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: "copyHtml5", exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } },
            { extend: "excelHtml5", exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } },
            { extend: "csvHtml5", exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } },
            { extend: "pdfHtml5", exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } },
            {
                extend: "print",
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            }
        ],
        aLengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        iDisplayLength: -1,
        order: [[0, "asc"]],
        language: {
            lengthMenu: "_MENU_",
            search: "_INPUT_",
            searchPlaceholder: "Search...",
            url: "common/assets/DataTables/languages/" + language + ".json"
        }
    });
    table.buttons().container().appendTo(".custom_buttons");
});
