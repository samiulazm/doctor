<?php
defined('BASEPATH') or exit('No direct script access allowed');
$CI = get_instance();
?>
<script type="text/javascript">
    var language = <?php echo json_encode(isset($CI->language) ? $CI->language : 'english'); ?>;
</script>
<script>
$(document).ready(function() {
    var table = $('#inventoryTable').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        dom: "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4 text-right'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        ajax: {
            url: <?php echo json_encode(base_url('inventory/getInventoryItemsList')); ?>,
            type: 'POST',
            error: function(xhr, error, code) {
                console.error('DataTables AJAX Error:', error);
                console.error('Response:', xhr.responseText);
                alert('Error loading inventory items. Please check the console for details and refresh the page.');
            }
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6, orderable: false }
        ],
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        pageLength: 25,
        autoWidth: false,
        buttons: [
            { extend: 'copyHtml5', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
            { extend: 'csvHtml5', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
            { extend: 'excelHtml5', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
            { extend: 'pdfHtml5', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
            { extend: 'print', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } }
        ],
        order: [[0, 'desc']],
        language: {
            lengthMenu: '_MENU_',
            search: '_INPUT_',
            searchPlaceholder: 'Search...',
            url: 'common/assets/DataTables/languages/' + language + '.json'
        }
    });
    table.buttons().container().appendTo('.custom_buttons');

    $('#category').select2({
        placeholder: 'Select Category',
        allowClear: true
    });
    $('#edit_category').select2({
        placeholder: 'Select Category',
        allowClear: true
    });
    $('#status').select2({
        minimumResultsForSearch: Infinity,
        placeholder: 'Select Status',
        allowClear: true
    });
    $('#edit_status').select2({
        minimumResultsForSearch: Infinity,
        placeholder: 'Select Status',
        allowClear: true
    });
    $('#addItemModal').on('hidden.bs.modal', function() {
        $('#addItemForm')[0].reset();
        $('#category').val(null).trigger('change');
        $('#status').val('active').trigger('change');
        $('#item_code').val('ITM' + new Date().getTime());
    });
    $('#editItemModal').on('hidden.bs.modal', function() {
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
