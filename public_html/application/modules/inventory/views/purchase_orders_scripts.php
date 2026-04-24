<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<script>
var poInventoryItems = [];
(function() {
    var el = document.getElementById('po-inventory-items-json');
    if (el) {
        try { poInventoryItems = JSON.parse(el.textContent); } catch (e) { poInventoryItems = []; }
    }
})();
if (!Array.isArray(poInventoryItems)) {
    poInventoryItems = [];
}

function poEscapeHtml(s) {
    if (s == null) return '';
    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
}

function poInventoryOptionsHtml() {
    var html = '<option value="">Select Item</option>';
    poInventoryItems.forEach(function(item) {
        html += '<option value="' + item.id + '" data-cost="' + item.unit_cost + '">' +
            poEscapeHtml(item.name) + ' (' + poEscapeHtml(item.item_code) + ')</option>';
    });
    return html;
}

var poUrlGetPurchaseOrders = <?php echo json_encode(base_url('inventory/getPurchaseOrders')); ?>;
var poUrlGetPurchaseOrderData = <?php echo json_encode(base_url('inventory/getPurchaseOrderData/')); ?>;
var poUrlPurchaseOrderViewData = <?php echo json_encode(base_url('inventory/get_purchase_order_view_data')); ?>;
var poUrlPrintPo = <?php echo json_encode(base_url('inventory/purchase/print_po/')); ?>;

$(document).ready(function() {
    if ($('#purchaseOrdersTable').length === 0) {
        return;
    }

    $('#purchaseOrdersTable').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": poUrlGetPurchaseOrders,
            "type": "POST",
            "error": function(xhr, err) {
                console.error('DataTables AJAX Error:', err, xhr.responseText);
            }
        },
        "dom": "<'row mb-3'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4 text-right'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": [
            { "extend": "copyHtml5", "exportOptions": { "columns": [0, 1, 2, 3, 4, 5, 6] } },
            { "extend": "excelHtml5", "exportOptions": { "columns": [0, 1, 2, 3, 4, 5, 6] } },
            { "extend": "csvHtml5", "exportOptions": { "columns": [0, 1, 2, 3, 4, 5, 6] } },
            { "extend": "pdfHtml5", "exportOptions": { "columns": [0, 1, 2, 3, 4, 5, 6] } },
            { "extend": "print", "exportOptions": { "columns": [0, 1, 2, 3, 4, 5, 6] } }
        ],
        "lengthMenu": [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        "pageLength": 25,
        "order": [[0, "desc"]],
        "language": {
            "lengthMenu": "_MENU_",
            "search": "_INPUT_",
            "searchPlaceholder": "Search purchase orders..."
        }
    });

    $('#supplier_id').select2({
        placeholder: "Select Supplier",
        allowClear: true,
        dropdownParent: $('#addPurchaseOrderModal')
    });

    $('#payment_terms').select2({
        placeholder: "Select Payment Terms",
        allowClear: true,
        dropdownParent: $('#addPurchaseOrderModal')
    });

    $('.select2-inventory-item').select2({
        placeholder: "Select Item",
        allowClear: true,
        dropdownParent: $('#addPurchaseOrderModal')
    });

    $('#edit_supplier_id').select2({
        placeholder: "Select Supplier",
        allowClear: true,
        dropdownParent: $('#editPurchaseOrderModal')
    });

    $('#edit_payment_terms').select2({
        placeholder: "Select Payment Terms",
        allowClear: true,
        dropdownParent: $('#editPurchaseOrderModal')
    });

    $('#addPurchaseOrderModal').on('hidden.bs.modal', function () {
        $('#addPurchaseOrderForm')[0].reset();
        $('#supplier_id').val(null).trigger('change');
        $('#payment_terms').val(null).trigger('change');
        $('.select2-inventory-item').val(null).trigger('change');
        resetPurchaseItems();
        $('#po_number').val('PO' + new Date().getTime());
        $('#order_date').val(new Date().toISOString().split('T')[0]);
    });

    $('#add-item-btn').click(function() {
        addPurchaseItem();
    });

    $(document).on('change', '.select2-inventory-item', function() {
        var selectedOption = $(this).find('option:selected');
        var unitCost = selectedOption.data('cost') || 0;
        var row = $(this).closest('.purchase-item-row');
        row.find('.item-price').val(unitCost);
        calculateItemTotal(row);
    });

    $(document).on('input', '.item-quantity, .item-price', function() {
        var row = $(this).closest('.purchase-item-row');
        calculateItemTotal(row);
    });

    $(document).on('click', '.remove-item-btn', function() {
        $(this).closest('.purchase-item-row').remove();
        updateRemoveButtons();
        calculateGrandTotal();
    });

    $('#editPurchaseOrderModal').on('hidden.bs.modal', function () {
        $('#editPurchaseOrderForm')[0].reset();
        $('#edit_supplier_id').val(null).trigger('change');
        $('#edit_payment_terms').val(null).trigger('change');
        $('#edit-purchase-items').html('');
        $('#edit-grand-total').text('0.00');
    });

    $('#edit-add-item-btn').click(function() {
        addEditPurchaseItem();
    });

    $(document).on('change', '.edit-select2-inventory-item', function() {
        var selectedOption = $(this).find('option:selected');
        var unitCost = selectedOption.data('cost') || 0;
        var row = $(this).closest('.edit-purchase-item-row');
        row.find('.edit-item-price').val(unitCost);
        calculateEditItemTotal(row);
    });

    $(document).on('input', '.edit-item-quantity, .edit-item-price', function() {
        var row = $(this).closest('.edit-purchase-item-row');
        calculateEditItemTotal(row);
    });

    $(document).on('click', '.edit-remove-item-btn', function() {
        $(this).closest('.edit-purchase-item-row').remove();
        updateEditRemoveButtons();
        calculateEditGrandTotal();
    });
});

var itemIndex = 0;

function addPurchaseItem() {
    itemIndex++;
    var opts = poInventoryOptionsHtml();
    var itemHtml =
        '<div class="purchase-item-row mb-3">' +
            '<div class="row">' +
                '<div class="col-md-4">' +
                    '<label>Inventory Item <span class="text-danger">*</span></label>' +
                    '<select class="form-control select2-inventory-item" name="items[' + itemIndex + '][inventory_item_id]" required>' +
                        opts +
                    '</select>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>Quantity <span class="text-danger">*</span></label>' +
                    '<input type="number" class="form-control item-quantity" name="items[' + itemIndex + '][quantity]" min="1" value="1" required>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>Unit Price</label>' +
                    '<input type="number" class="form-control item-price" name="items[' + itemIndex + '][unit_price]" step="0.01" min="0">' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>Total</label>' +
                    '<input type="text" class="form-control item-total" readonly>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>&nbsp;</label>' +
                    '<button type="button" class="btn btn-danger btn-sm btn-block remove-item-btn">' +
                        '<i class="fas fa-trash"></i> Remove' +
                    '</button>' +
                '</div>' +
            '</div>' +
        '</div>';

    $('#purchase-items').append(itemHtml);

    $('#purchase-items').find('.select2-inventory-item').last().select2({
        placeholder: "Select Item",
        allowClear: true,
        dropdownParent: $('#addPurchaseOrderModal')
    });

    updateRemoveButtons();
}

function resetPurchaseItems() {
    itemIndex = 0;
    var opts = poInventoryOptionsHtml();
    $('#purchase-items').html(
        '<div class="purchase-item-row mb-3">' +
            '<div class="row">' +
                '<div class="col-md-4">' +
                    '<label>Inventory Item <span class="text-danger">*</span></label>' +
                    '<select class="form-control select2-inventory-item" name="items[0][inventory_item_id]" required>' +
                        opts +
                    '</select>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>Quantity <span class="text-danger">*</span></label>' +
                    '<input type="number" class="form-control item-quantity" name="items[0][quantity]" min="1" value="1" required>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>Unit Price</label>' +
                    '<input type="number" class="form-control item-price" name="items[0][unit_price]" step="0.01" min="0">' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>Total</label>' +
                    '<input type="text" class="form-control item-total" readonly>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>&nbsp;</label>' +
                    '<button type="button" class="btn btn-danger btn-sm btn-block remove-item-btn" style="display: none;">' +
                        '<i class="fas fa-trash"></i> Remove' +
                    '</button>' +
                '</div>' +
            '</div>' +
        '</div>'
    );

    $('.select2-inventory-item').select2({
        placeholder: "Select Item",
        allowClear: true,
        dropdownParent: $('#addPurchaseOrderModal')
    });

    calculateGrandTotal();
}

function updateRemoveButtons() {
    var itemRows = $('.purchase-item-row');
    if (itemRows.length > 1) {
        $('.remove-item-btn').show();
    } else {
        $('.remove-item-btn').hide();
    }
}

function calculateItemTotal(row) {
    var quantity = parseFloat(row.find('.item-quantity').val()) || 0;
    var price = parseFloat(row.find('.item-price').val()) || 0;
    var total = quantity * price;
    row.find('.item-total').val(total.toFixed(2));
    calculateGrandTotal();
}

function calculateGrandTotal() {
    var grandTotal = 0;
    $('.item-total').each(function() {
        var total = parseFloat($(this).val()) || 0;
        grandTotal += total;
    });
    $('#grand-total').text(grandTotal.toFixed(2));
}

var editItemIndex = 0;

function loadPurchaseOrderData(poId) {
    $.ajax({
        url: poUrlGetPurchaseOrderData + poId,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                alert(data.error);
                return;
            }
            $('#edit_purchase_order_id').val(data.po.id);
            $('#edit_po_number').val(data.po.po_number);
            $('#edit_supplier_id').val(data.po.supplier_id).trigger('change');
            $('#edit_order_date').val(data.po.order_date);
            $('#edit_expected_delivery_date').val(data.po.expected_delivery_date);
            $('#edit_payment_terms').val(data.po.payment_terms).trigger('change');
            $('#edit_status').val(data.po.status).trigger('change');
            $('#edit_notes').val(data.po.notes);
            loadEditPurchaseItems(data.items);
        },
        error: function() {
            alert('Error loading purchase order data');
        }
    });
}

function loadEditPurchaseItems(items) {
    $('#edit-purchase-items').html('');
    editItemIndex = 0;

    if (items && items.length > 0) {
        items.forEach(function(item) {
            addEditPurchaseItem(item);
        });
    } else {
        addEditPurchaseItem();
    }

    updateEditRemoveButtons();
    calculateEditGrandTotal();
}

function viewPurchaseOrder(poId) {
    $.ajax({
        url: poUrlPurchaseOrderViewData,
        type: 'POST',
        data: { purchase_order_id: poId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var data = response.data;

                $('#view_po_number').text(data.purchase_order.po_number);
                $('#view_status_badge').html(data.purchase_order.status_badge);
                $('#view_order_date').text(data.purchase_order.order_date);
                $('#view_expected_delivery_date').text(data.purchase_order.expected_delivery_date);
                $('#view_payment_terms').text(data.purchase_order.payment_terms);
                $('#view_notes').text(data.purchase_order.notes);

                $('#view_supplier_name').text(data.supplier.name);
                $('#view_supplier_company').text(data.supplier.company_name);
                $('#view_supplier_contact').text(data.supplier.contact_person);
                $('#view_supplier_email').text(data.supplier.email);
                $('#view_supplier_phone').text(data.supplier.phone);

                $('#view_subtotal').text(data.purchase_order.total_amount);
                $('#view_tax_amount').text(data.purchase_order.tax_amount);
                $('#view_discount_amount').text(data.purchase_order.discount_amount);
                $('#view_shipping_amount').text(data.purchase_order.shipping_amount);
                $('#view_grand_total').text(data.purchase_order.grand_total);

                var itemsHtml = '';
                if (data.items && data.items.length > 0) {
                    data.items.forEach(function(item) {
                        var itemStatus = 'Pending';
                        var statusClass = 'secondary';

                        if (item.quantity_received > 0) {
                            if (item.quantity_received >= item.quantity_ordered) {
                                itemStatus = 'Completed';
                                statusClass = 'success';
                            } else {
                                itemStatus = 'Partially Received';
                                statusClass = 'warning';
                            }
                        }

                        itemsHtml += '<tr>' +
                            '<td>' + (item.item_name || '-') + '</td>' +
                            '<td>' + (item.item_code || '-') + '</td>' +
                            '<td>' + item.quantity_ordered + '</td>' +
                            '<td>' + data.currency + ' ' + parseFloat(item.unit_price).toFixed(2) + '</td>' +
                            '<td>' + data.currency + ' ' + (item.quantity_ordered * item.unit_price).toFixed(2) + '</td>' +
                            '<td>' + (item.quantity_received || 0) + '</td>' +
                            '<td><span class="badge badge-' + statusClass + '">' + itemStatus + '</span></td>' +
                        '</tr>';
                    });
                } else {
                    itemsHtml = '<tr><td colspan="7" class="text-center">No items found</td></tr>';
                }

                $('#view_items_tbody').html(itemsHtml);
                $('#view_print_link').attr('href', poUrlPrintPo + data.purchase_order.id);
            } else {
                alert('Error loading purchase order data: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr, status, error) {
            alert('Error loading purchase order data. Please try again.');
            console.error('AJAX Error:', error);
        }
    });
}

function addEditPurchaseItem(itemData) {
    var opts = poInventoryOptionsHtml();
    var itemHtml =
        '<div class="edit-purchase-item-row mb-3">' +
            '<div class="row">' +
                '<div class="col-md-4">' +
                    '<label>Inventory Item <span class="text-danger">*</span></label>' +
                    '<select class="form-control edit-select2-inventory-item" name="items[' + editItemIndex + '][inventory_item_id]" required>' +
                        opts +
                    '</select>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>Quantity <span class="text-danger">*</span></label>' +
                    '<input type="number" class="form-control edit-item-quantity" name="items[' + editItemIndex + '][quantity]" min="1" value="1" required>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>Unit Price</label>' +
                    '<input type="number" class="form-control edit-item-price" name="items[' + editItemIndex + '][unit_price]" step="0.01" min="0">' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>Total</label>' +
                    '<input type="text" class="form-control edit-item-total" readonly>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>&nbsp;</label>' +
                    '<button type="button" class="btn btn-danger btn-sm btn-block edit-remove-item-btn">' +
                        '<i class="fas fa-trash"></i> Remove' +
                    '</button>' +
                '</div>' +
            '</div>' +
        '</div>';

    $('#edit-purchase-items').append(itemHtml);

    $('#edit-purchase-items').find('.edit-select2-inventory-item').last().select2({
        placeholder: "Select Item",
        allowClear: true,
        dropdownParent: $('#editPurchaseOrderModal')
    });

    if (itemData) {
        var row = $('#edit-purchase-items').find('.edit-purchase-item-row').last();
        row.find('.edit-select2-inventory-item').val(itemData.inventory_item_id).trigger('change');
        row.find('.edit-item-quantity').val(itemData.quantity_ordered);
        row.find('.edit-item-price').val(itemData.unit_price);
        calculateEditItemTotal(row);
    }

    editItemIndex++;
    updateEditRemoveButtons();
}

function updateEditRemoveButtons() {
    var itemRows = $('.edit-purchase-item-row');
    if (itemRows.length > 1) {
        $('.edit-remove-item-btn').show();
    } else {
        $('.edit-remove-item-btn').hide();
    }
}

function calculateEditItemTotal(row) {
    var quantity = parseFloat(row.find('.edit-item-quantity').val()) || 0;
    var price = parseFloat(row.find('.edit-item-price').val()) || 0;
    var total = quantity * price;
    row.find('.edit-item-total').val(total.toFixed(2));
    calculateEditGrandTotal();
}

function calculateEditGrandTotal() {
    var grandTotal = 0;
    $('.edit-item-total').each(function() {
        var total = parseFloat($(this).val()) || 0;
        grandTotal += total;
    });
    $('#edit-grand-total').text(grandTotal.toFixed(2));
}
</script>
