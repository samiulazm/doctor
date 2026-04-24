"use strict";
$(document).ready(function () {
    "use strict";
    var table = $('#editable-sample1').DataTable({
        responsive: true,

        dom: "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",

        buttons: [
            { extend: 'copyHtml5', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } },
            { extend: 'excelHtml5', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } },
            { extend: 'csvHtml5', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } },
            { extend: 'pdfHtml5', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } },
            { extend: 'print', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } },
        ],

        aLengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        iDisplayLength: -1,
        "order": [[0, "desc"]],

        "language": {
            "lengthMenu": "_MENU_",
            search: "_INPUT_",
        },

    });
});