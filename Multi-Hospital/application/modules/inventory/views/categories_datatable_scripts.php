<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<script>
$(document).ready(function() {
    if ($('#categoriesTable').length === 0) {
        return;
    }

    var table = $('#categoriesTable').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": <?php echo json_encode(base_url('inventory/getCategories')); ?>,
            "type": "POST",
            "error": function(xhr, error, code) {
                console.error('DataTables AJAX Error:', error);
                console.error('Response:', xhr.responseText);
                alert('Error loading categories. Please check the console for details and refresh the page.');
            }
        },
        "dom": "<'row mb-3'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4 text-right'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": [
            { "extend": "copyHtml5", "exportOptions": { "columns": [0, 1, 2, 3] } },
            { "extend": "excelHtml5", "exportOptions": { "columns": [0, 1, 2, 3] } },
            { "extend": "csvHtml5", "exportOptions": { "columns": [0, 1, 2, 3] } },
            { "extend": "pdfHtml5", "exportOptions": { "columns": [0, 1, 2, 3] } },
            { "extend": "print", "exportOptions": { "columns": [0, 1, 2, 3] } }
        ],
        "lengthMenu": [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        "pageLength": 25,
        "order": [[ 0, "desc" ]],
        "language": {
            "processing": "Loading categories...",
            "search": "Search categories:",
            "lengthMenu": "Show _MENU_ categories per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ categories",
            "infoEmpty": "No categories found",
            "infoFiltered": "(filtered from _MAX_ total categories)",
            "emptyTable": "No categories available",
            "zeroRecords": "No matching categories found"
        }
    });

    $('#parent_id').select2({
        placeholder: "Select Parent Category",
        allowClear: true,
        dropdownParent: $('#addCategoryModal')
    });

    $('#status').select2({
        minimumResultsForSearch: Infinity,
        dropdownParent: $('#addCategoryModal')
    });

    $('#addCategoryModal').on('hidden.bs.modal', function () {
        $('#addCategoryForm')[0].reset();
        $('#parent_id').val(null).trigger('change');
        $('#status').val('active').trigger('change');
    });

    $('#edit_parent_id').select2({
        placeholder: "Select Parent Category",
        allowClear: true,
        dropdownParent: $('#editCategoryModal')
    });

    $('#edit_status').select2({
        minimumResultsForSearch: Infinity,
        dropdownParent: $('#editCategoryModal')
    });

    $('#editCategoryModal').on('hidden.bs.modal', function () {
        $('#editCategoryForm')[0].reset();
        $('#edit_parent_id').val(null).trigger('change');
        $('#edit_status').val('active').trigger('change');
    });

    $('#editCategoryForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: <?php echo json_encode(base_url('inventory/edit_category')); ?>,
            type: 'POST',
            data: $(this).serialize(),
            success: function() {
                $('#editCategoryModal').modal('hide');
                table.ajax.reload();
                showNotification('Category updated successfully', 'success');
            },
            error: function(xhr) {
                console.error('Response:', xhr.responseText);
                showNotification('Error updating category. Please try again.', 'error');
            }
        });
    });
});

function editCategory(categoryId) {
    $.ajax({
        url: <?php echo json_encode(base_url('inventory/get_category_data')); ?>,
        type: 'POST',
        data: { category_id: categoryId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#edit_category_id').val(response.data.id);
                $('#edit_name').val(response.data.name);
                $('#edit_description').val(response.data.description);
                $('#edit_parent_id').val(response.data.parent_id).trigger('change');
                $('#edit_status').val(response.data.status).trigger('change');
            } else {
                alert('Error loading category data: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr, status, error) {
            alert('Error loading category data. Please try again.');
            console.error('AJAX Error:', error, xhr.responseText);
        }
    });
}

function showNotification(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';

    const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="${icon} mr-2"></i>
            ${message}
            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `);

    $('.content').first().prepend(notification);

    setTimeout(function() {
        notification.alert('close');
    }, 5000);
}
</script>
