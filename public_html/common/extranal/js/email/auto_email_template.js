"use strict";

tinymce.init({
    selector: '#editor1',
    plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
    menubar: 'file edit view insert format tools table help',
    toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
    branding: false,
    promotion: false
});

function addtext(ele) {
    "use strict";
    var val = (ele && ele.value) ? ele.value : '';
    if (typeof tinymce !== 'undefined' && tinymce.get('editor1')) {
        tinymce.get('editor1').insertContent(val);
    } else {
        var $ta = $('#editor1');
        $ta.val($ta.val() + val);
    }
}

$(document).ready(function () {
    "use strict";
    $(".table").on("click", ".editbutton1", function () {
        $("#loader").show();
        var iid = $(this).attr('data-id');
        $('#divbuttontag').html("");

        $.ajax({
            url: 'email/editAutoEmailTemplate?id=' + encodeURIComponent(iid),
            method: 'GET',
            data: '',
            dataType: 'json',
            success: function (response) {
                "use strict";
                if (!response || !response.autotemplatename) {
                    $("#loader").hide();
                    return;
                }
                $('#emailtemp').find('[name="id"]').val(response.autotemplatename.id);
                $('#emailtemp').find('[name="category"]').val(response.autotemplatename.name);
                var option = '';
                var count = 0;
                $.each(response.autotag, function (index, value) {
                    option += '<input type="button" class="btn btn-secondary btn-sm mb-1 mr-1" name="myBtn" value="' + $('<div/>').text(value.name).html() + '" onClick="addtext(this);">';
                    count += 1;
                    if (count % 7 === 0) {
                        option += '<br>';
                    }
                });
                $('#divbuttontag').html(option);
                $('#status').html(response.status_options);
                var msg = response.autotemplatename.message || '';
                if (typeof tinymce !== 'undefined' && tinymce.get('editor1')) {
                    tinymce.get('editor1').setContent(msg);
                } else {
                    $('#editor1').val(msg);
                }
                $('#myModal1').modal('show');
            },
            complete: function () {
                $("#loader").hide();
            }
        });
    });
});

$(document).ready(function () {
    "use strict";
    var table = $('#editable-sample1').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        searchable: true,
        ajax: {
            url: "email/getAutoEmailTemplateList",
            type: 'POST',
            data: { 'type': 'email' }
        },
        dom: "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4 text-right'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copyHtml5', exportOptions: { columns: [0, 1, 2, 3] } },
            { extend: 'excelHtml5', exportOptions: { columns: [0, 1, 2, 3] } },
            { extend: 'csvHtml5', exportOptions: { columns: [0, 1, 2, 3] } },
            { extend: 'pdfHtml5', exportOptions: { columns: [0, 1, 2, 3] } },
            { extend: 'print', exportOptions: { columns: [0, 1, 2, 3] } }
        ],
        aLengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        iDisplayLength: 100,
        order: [[0, "desc"]],
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
