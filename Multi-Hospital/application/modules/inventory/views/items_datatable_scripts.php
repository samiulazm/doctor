<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<script>
$(document).ready(function() {
    $('#inventoryTable').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "dom": "Bfrtip",
        "ajax": {
            "url": <?php echo json_encode(base_url('inventory/getInventoryItemsList')); ?>,
            "type": "POST",
            "error": function(xhr, error, code) {
                console.error('DataTables AJAX Error:', error);
                console.error('Response:', xhr.responseText);
                alert('Error loading inventory items. Please check the console for details and refresh the page.');
            }
        },
        "columns": [
            { "data": 0 },
            { "data": 1 },
            { "data": 2 },
            { "data": 3 },
            { "data": 4 },
            { "data": 5 },
            { "data": 6, "orderable": false }
        ],
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
        "order": [[ 0, "desc" ]]
    });

    $('#category').select2({
        placeholder: "Select Category",
        allowClear: true
    });

    $('#edit_category').select2({
        placeholder: "Select Category",
        allowClear: true
    });

    $('#status').select2({
        minimumResultsForSearch: Infinity,
        placeholder: "Select Status",
        allowClear: true
    });

    $('#edit_status').select2({
        minimumResultsForSearch: Infinity,
        placeholder: "Select Status",
        allowClear: true
    });

    $('#addItemModal').on('hidden.bs.modal', function () {
        $('#addItemForm')[0].reset();
        $('#category').val(null).trigger('change');
        $('#status').val('active').trigger('change');
        $('#item_code').val('ITM' + new Date().getTime());
    });

    $('#editItemModal').on('hidden.bs.modal', function () {
        $('#editItemForm')[0].reset();
        $('#edit_category').val(null).trigger('change');
        $('#edit_status').val(null).trigger('change');
    });
});

function loadItemData(id, item_code, name, description, category, subcategory, unit_of_measure, minimum_stock, maximum_stock, reorder_level, current_stock, unit_cost, selling_price, storage_location, expiry_tracking, barcode, manufacturer, brand, model_number, specifications, status) {
    $('#edit_item_id').val(id);
    $('#edit_item_code').val(item_code);
    $('#edit_name').val(name);
    $('#edit_description').val(description);
    $('#edit_category').val(category).trigger('change');
    $('#edit_subcategory').val(subcategory);
    $('#edit_unit_of_measure').val(unit_of_measure);
    $('#edit_minimum_stock').val(minimum_stock);
    $('#edit_maximum_stock').val(maximum_stock);
    $('#edit_reorder_level').val(reorder_level);
    $('#edit_current_stock').val(current_stock);
    $('#edit_unit_cost').val(unit_cost);
    $('#edit_selling_price').val(selling_price);
    $('#edit_storage_location').val(storage_location);
    $('#edit_expiry_tracking').val(expiry_tracking);
    $('#edit_barcode').val(barcode);
    $('#edit_manufacturer').val(manufacturer);
    $('#edit_brand').val(brand);
    $('#edit_model_number').val(model_number);
    $('#edit_specifications').val(specifications);
    $('#edit_status').val(status);
}
</script>
