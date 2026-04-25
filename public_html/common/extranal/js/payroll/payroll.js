"use strict";

function payrollTableOpts() {
    return {
        responsive: true,
        processing: true,
        searching: true,
        scroller: {
            loadingIndicator: true
        },
        dom:
            "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4 text-right'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: "copyHtml5", exportOptions: { columns: [0, 1, 2, 3] } },
            { extend: "excelHtml5", exportOptions: { columns: [0, 1, 2, 3] } },
            { extend: "csvHtml5", exportOptions: { columns: [0, 1, 2, 3] } },
            { extend: "pdfHtml5", exportOptions: { columns: [0, 1, 2, 3] } },
            { extend: "print", exportOptions: { columns: [0, 1, 2, 3] } }
        ],
        aLengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        iDisplayLength: 10,
        order: [[0, "desc"]],
        language: {
            lengthMenu: "_MENU_",
            search: "_INPUT_",
            searchPlaceholder: "Search...",
            url: "common/assets/DataTables/languages/" + language + ".json"
        }
    };
}

function bindPayrollTable() {
    if ($.fn.DataTable.isDataTable("#salary-sample")) {
        $("#salary-sample").DataTable().destroy();
    }
    var table = $("#salary-sample").DataTable(payrollTableOpts());
    table.buttons().container().appendTo(".custom_buttons");
}

$(document).ready(function () {
    "use strict";

    $("#payroll_year").on("change", function () {
        var month = $("#payroll_month").val();
        var year = $("#payroll_year").val();

        $.ajax({
            url: "payroll/payrollTableByMonthYear?month=" + encodeURIComponent(month) + "&year=" + encodeURIComponent(year),
            method: "GET",
            data: "",
            dataType: "json",
            success: function (response) {
                $(".payroll_table").empty().append(response);
                bindPayrollTable();
            }
        });
    });

    $("#payroll_month").on("change", function () {
        var month = $("#payroll_month").val();
        var year = $("#payroll_year").val();

        $.ajax({
            url: "payroll/payrollTableByMonthYear?month=" + encodeURIComponent(month) + "&year=" + encodeURIComponent(year),
            method: "GET",
            data: "",
            dataType: "json",
            success: function (response) {
                $(".payroll_table").empty().append(response);
                bindPayrollTable();
            }
        });
    });

    $("#payroll_year").on("change", function () {
        $("#payroll_month").empty().trigger("change");
        var months = [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December"
        ];
        var date = new Date();
        var current_year = date.getFullYear();
        var current_month = date.getMonth();
        var year = $("#payroll_year").val();
        if (year < current_year) {
            for (var idx = 0; idx < 12; idx++) {
                var data = {
                    id: months[idx],
                    text: months[idx]
                };
                var newOption = new Option(data.text, data.id, false, false);
                $("#payroll_month").append(newOption).trigger("change");
            }
        } else {
            for (var idx2 = 0; idx2 <= current_month; idx2++) {
                var data2 = {
                    id: months[idx2],
                    text: months[idx2]
                };
                var newOption2 = new Option(data2.text, data2.id, false, false);
                $("#payroll_month").append(newOption2).trigger("change");
            }
        }
    });

    $(".generatePayroll").on("click", function () {
        var month = $("#payroll_month").val();
        var year = $("#payroll_year").val();
        $.ajax({
            url: "payroll/generatePayroll?month=" + encodeURIComponent(month) + "&year=" + encodeURIComponent(year),
            method: "GET",
            data: "",
            dataType: "json",
            success: function (response) {
                $(".payroll_table").empty().append(response);
                bindPayrollTable();
            }
        });
    });
});

$(document).ready(function () {
    "use strict";
    if ($("#salary-sample").length) {
        bindPayrollTable();
    }
});

$(document).ready(function () {
    "use strict";
    $(".table").on("click", ".inffo", function () {
        $("#loader").show();
        var iid = $(this).attr("data-id");

        $("#img1").attr("src", "uploads/cardiology-patient-icon-vector-6244713.jpg");
        $(".nameClass").html("").end();
        $(".emailClass").html("").end();
        $(".addressClass").html("").end();
        $(".phoneClass").html("").end();
        $(".departmentClass").html("").end();
        $(".profileClass").html("").end();
        $.ajax({
            url: "doctor/editDoctorByJason?id=" + iid,
            method: "GET",
            data: "",
            dataType: "json",
            success: function (response) {
                $("#editDoctorForm").find('[name="id"]').val(response.doctor.id).end();
                $(".nameClass").append(response.doctor.name).end();
                $(".emailClass").append(response.doctor.email).end();
                $(".addressClass").append(response.doctor.address).end();
                $(".phoneClass").append(response.doctor.phone).end();
                $(".departmentClass").append(response.doctor.department).end();
                $(".profileClass").append(response.doctor.profile).end();

                if (typeof response.doctor.img_url !== "undefined" && response.doctor.img_url != "") {
                    $("#img1").attr("src", response.doctor.img_url);
                }

                $("#infoModal").modal("show");
            },
            complete: function () {
                $("#loader").hide();
            }
        });
    });
});

$(document).ready(function () {
    "use strict";
    $(".flashmessage").delay(3000).fadeOut(100);
});
