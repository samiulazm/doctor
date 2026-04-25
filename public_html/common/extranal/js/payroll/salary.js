"use strict";

$(document).ready(function () {
    "use strict";
    $(".table").on("click", ".editSalary", function () {
        $("#loader").show();
        var iid = $(this).attr("data-id");
        $("#salaryForm").find('[name="salary"], input[name="staff"]').val("");
        $.ajax({
            url: "payroll/getSalaryByStaffId?id=" + iid,
            method: "GET",
            data: "",
            dataType: "json",
            success: function (response) {
                if (response == "not_found") {
                    $("#salaryForm").find('[name="salary"]').val(0).end();
                    $("#salaryForm").find('[name="staff"]').val(iid).end();
                } else {
                    $("#salaryForm").find('[name="salary"]').val(response.salary).end();
                    $("#salaryForm").find('[name="staff"]').val(response.staff).end();
                }
                $("#myModal").modal("show");
            },
            complete: function () {
                $("#loader").hide();
            }
        });
    });
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
    var table = $("#editable-sample").DataTable({
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
            { extend: "copyHtml5", exportOptions: { columns: [0, 1] } },
            { extend: "excelHtml5", exportOptions: { columns: [0, 1] } },
            { extend: "csvHtml5", exportOptions: { columns: [0, 1] } },
            { extend: "pdfHtml5", exportOptions: { columns: [0, 1] } },
            { extend: "print", exportOptions: { columns: [0, 1] } }
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
    });
    table.buttons().container().appendTo(".custom_buttons");
});

$(document).ready(function () {
    "use strict";
    $(".flashmessage").delay(3000).fadeOut(100);
});
