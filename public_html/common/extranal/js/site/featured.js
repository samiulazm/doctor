"use strict";

// HTML5 "required" on a TinyMCE textarea fails: the real textarea is empty until
// triggerSave. Sync before submit and rely on server-side validation.
$(document).on("submit", "#myModal form, #editFeaturedForm", function () {
    "use strict";
    if (typeof tinymce !== "undefined") {
        tinymce.triggerSave();
    }
});

tinymce.init({
    selector: '#editor',
    plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
    menubar: 'file edit view insert format tools table help',
    toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
    branding: false,
    promotion: false
});

$(document).ready(function () {
    "use strict";
    $(".table").on("click", ".editbutton", function () {
        $("#loader").show();
        var iid = $(this).attr('data-id');
        $("#img").attr("src", "uploads/cardiology-patient-icon-vector-6244713.jpg");
        $.ajax({
            url: 'site/featured/editFeaturedByJason?id=' + encodeURIComponent(iid),
            method: 'GET',
            data: '',
            dataType: 'json',
            success: function (response) {
                if (!response || !response.featured) {
                    $("#loader").hide();
                    return;
                }
                var f = response.featured;
                $('#editFeaturedForm').find('[name="id"]').val(f.id);
                $('#editFeaturedForm').find('[name="name"]').val(f.name);
                $('#editFeaturedForm').find('[name="profile"]').val(f.profile);
                if (typeof f.img_url !== 'undefined' && f.img_url !== '') {
                    $("#img").attr("src", f.img_url);
                }
                tinymce.remove('#editor1');
                tinymce.init({
                    selector: '#editor1',
                    plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
                    menubar: 'file edit view insert format tools table help',
                    toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
                    branding: false,
                    promotion: false,
                    init_instance_callback: function (editor) {
                        editor.setContent(f.description || '');
                        $('#myModal2').modal('show');
                        $("#loader").hide();
                    }
                });
            },
            error: function () {
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
            { extend: 'copyHtml5', exportOptions: { columns: [1, 2, 3] } },
            { extend: 'excelHtml5', exportOptions: { columns: [1, 2, 3] } },
            { extend: 'csvHtml5', exportOptions: { columns: [1, 2, 3] } },
            { extend: 'pdfHtml5', exportOptions: { columns: [1, 2, 3] } },
            { extend: 'print', exportOptions: { columns: [1, 2, 3] } }
        ],
        aLengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        iDisplayLength: -1,
        order: [[1, "asc"]],
        columnDefs: [
            { orderable: false, targets: [0, 4] }
        ],
        language: {
            lengthMenu: "_MENU_",
            search: "_INPUT_",
            searchPlaceholder: "Search...",
            url: "common/assets/DataTables/languages/" + language + ".json"
        }
    });
    table.buttons().container().appendTo('.custom_buttons');
});

$(document).ready(function () {
    "use strict";
    $(".flashmessage").delay(3000).fadeOut(100);
});
