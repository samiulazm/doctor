<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<script>
$(document).ready(function() {
    if ($('#suppliersTable').length === 0) {
        return;
    }

    $('#suppliersTable').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": <?php echo json_encode(base_url('inventory/getSuppliers')); ?>,
            "type": "POST",
            "error": function(xhr, err) {
                console.error('DataTables AJAX Error:', err, xhr.responseText);
            }
        },
        "dom": "<'row mb-3'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4 text-right'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": [
            { "extend": "copyHtml5", "exportOptions": { "columns": [0, 1, 2, 3, 4, 5, 6, 7] } },
            { "extend": "excelHtml5", "exportOptions": { "columns": [0, 1, 2, 3, 4, 5, 6, 7] } },
            { "extend": "csvHtml5", "exportOptions": { "columns": [0, 1, 2, 3, 4, 5, 6, 7] } },
            { "extend": "pdfHtml5", "exportOptions": { "columns": [0, 1, 2, 3, 4, 5, 6, 7] } },
            { "extend": "print", "exportOptions": { "columns": [0, 1, 2, 3, 4, 5, 6, 7] } }
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
            "searchPlaceholder": "Search suppliers..."
        }
    });

    $('#status').select2({
        minimumResultsForSearch: Infinity,
        dropdownParent: $('#addSupplierModal')
    });
    $('#edit_status').select2({
        minimumResultsForSearch: Infinity,
        dropdownParent: $('#editSupplierModal')
    });

    $('#addSupplierModal').on('hidden.bs.modal', function () {
        $('#addSupplierForm')[0].reset();
        $('#status').val('active').trigger('change');
    });

    $('#editSupplierModal').on('hidden.bs.modal', function () {
        $('#editSupplierForm')[0].reset();
        $('#edit_status').val(null).trigger('change');
    });
});

function editSupplier(supplierId) {
    $.ajax({
        url: <?php echo json_encode(base_url('inventory/get_supplier_data')); ?>,
        type: 'POST',
        data: { supplier_id: supplierId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#edit_supplier_id').val(response.data.id);
                $('#edit_name').val(response.data.name);
                $('#edit_company_name').val(response.data.company_name);
                $('#edit_contact_person').val(response.data.contact_person);
                $('#edit_email').val(response.data.email);
                $('#edit_phone').val(response.data.phone);
                $('#edit_mobile').val(response.data.mobile);
                $('#edit_address').val(response.data.address);
                $('#edit_city').val(response.data.city);
                $('#edit_state').val(response.data.state);
                $('#edit_country').val(response.data.country);
                $('#edit_postal_code').val(response.data.postal_code);
                $('#edit_tax_number').val(response.data.tax_number);
                $('#edit_bank_name').val(response.data.bank_name);
                $('#edit_bank_account').val(response.data.bank_account);
                $('#edit_payment_terms').val(response.data.payment_terms);
                $('#edit_credit_limit').val(response.data.credit_limit);
                $('#edit_status').val(response.data.status).trigger('change');
                $('#edit_notes').val(response.data.notes);
            } else {
                alert('Error loading supplier data: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr, status, error) {
            alert('Error loading supplier data. Please try again.');
            console.error('AJAX Error:', error, xhr.responseText);
        }
    });
}

function loadSupplierData(id, name, company_name, contact_person, email, phone, mobile, address, city, state, country, postal_code, tax_number, bank_name, bank_account, payment_terms, credit_limit, status, notes) {
    $('#edit_supplier_id').val(id);
    $('#edit_name').val(name);
    $('#edit_company_name').val(company_name);
    $('#edit_contact_person').val(contact_person);
    $('#edit_email').val(email);
    $('#edit_phone').val(phone);
    $('#edit_mobile').val(mobile);
    $('#edit_address').val(address);
    $('#edit_city').val(city);
    $('#edit_state').val(state);
    $('#edit_country').val(country);
    $('#edit_postal_code').val(postal_code);
    $('#edit_tax_number').val(tax_number);
    $('#edit_bank_name').val(bank_name);
    $('#edit_bank_account').val(bank_account);
    $('#edit_payment_terms').val(payment_terms);
    $('#edit_credit_limit').val(credit_limit);
    $('#edit_status').val(status).trigger('change');
    $('#edit_notes').val(notes);
}
</script>
