<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('low_stock_items'),
        'icon' => 'fas fa-exclamation-triangle text-warning mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('inventory'), 'url' => 'inventory'),
            array('label' => lang('low_stock_items'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-end mb-3">
                <a href="inventory/purchase" class="btn btn-sm btn-primary">
                    <i class="fa fa-list mr-1"></i> <?php echo lang('purchase_orders'); ?>
                </a>
            </div>
            <?php if (!empty($items)) { ?>
                <!-- Alert -->
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Alert!</h5>
                    You have <?php echo count($items); ?> items with low stock levels that require immediate attention.
                </div>
            <?php } ?>
            
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('low_stock_items'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle text-sm mb-0" id="lowStockTable" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-uppercase"><?php echo lang('item_code'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('name'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('category'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('current_stock'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('reorder_level'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('shortage'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('unit_cost'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('suggested_order'); ?></th>
                                            <th class="text-uppercase no-print"><?php echo lang('options'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($items)) { ?>
                                            <?php foreach ($items as $item) { ?>
                                                <?php 
                                                $shortage = $item->reorder_level - $item->current_stock;
                                                $suggested_order = $item->maximum_stock > 0 ? $item->maximum_stock - $item->current_stock : $shortage * 2;
                                                $urgency = $item->current_stock == 0 ? 'danger' : ($item->current_stock <= ($item->reorder_level * 0.5) ? 'warning' : 'info');
                                                ?>
                                                <tr class="table-<?php echo $urgency; ?>">
                                                    <td class="font-weight-bold"><?php echo $item->item_code; ?></td>
                                                    <td>
                                                        <?php echo $item->name; ?>
                                                        <?php if ($item->current_stock == 0) { ?>
                                                            <br><span class="badge badge-danger">OUT OF STOCK</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td><?php echo $item->category; ?></td>
                                                    <td>
                                                        <span class="badge badge-<?php echo $urgency; ?>">
                                                            <?php echo $item->current_stock; ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo $item->reorder_level; ?></td>
                                                    <td>
                                                        <span class="badge badge-danger">
                                                            <?php echo $shortage; ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo $settings->currency . ' ' . number_format($item->unit_cost, 2); ?></td>
                                                    <td>
                                                        <span class="badge badge-success">
                                                            <?php echo $suggested_order; ?>
                                                        </span>
                                                        <br>
                                                        <small class="text-muted">
                                                            Est. Cost: <?php echo $settings->currency . ' ' . number_format($suggested_order * $item->unit_cost, 2); ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group-vertical">
                                                            <a href="inventory/purchase" 
                                                               class="btn btn-primary btn-sm">
                                                                <i class="fas fa-shopping-cart"></i> Create PO
                                                            </a>
                                                            <a href="inventory/adjust_stock/<?php echo $item->id; ?>" 
                                                               class="btn btn-warning btn-sm">
                                                                <i class="fas fa-calculator"></i> Adjust
                                                            </a>
                                                            <a href="inventory/edit_item/<?php echo $item->id; ?>" 
                                                               class="btn btn-info btn-sm">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <tr>
                                                <td colspan="9" class="text-center py-4">
                                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                                    <h5 class="text-success">Great! No Low Stock Items</h5>
                                                    <p class="text-muted">All inventory items are above their reorder levels.</p>
                                                    <a href="inventory/items" class="btn btn-primary">
                                                        <i class="fas fa-list mr-2"></i>
                                                        <?php echo lang('view_all_items'); ?>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <?php if (!empty($items)) { ?>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Legend:</h6>
                                        <span class="badge badge-danger mr-2">Critical (Out of Stock)</span>
                                        <span class="badge badge-warning mr-2">Low (Below 50% of reorder level)</span>
                                        <span class="badge badge-info">Reorder Required</span>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button type="button" class="btn btn-success" onclick="createBulkPurchaseOrder()">
                                            <i class="fas fa-shopping-cart mr-2"></i>
                                            Create Bulk Purchase Order
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
    var language = <?php echo json_encode(isset($CI->language) ? $CI->language : 'english'); ?>;
</script>
<script>
$(document).ready(function() {
    if ($('#lowStockTable tbody tr').first().find('td[colspan]').length) {
        return;
    }
    var lowTable = $('#lowStockTable').DataTable({
        responsive: true,
        dom: "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4 text-right'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        lengthChange: true,
        autoWidth: false,
        buttons: [
            { extend: 'copyHtml5', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } },
            { extend: 'csvHtml5', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } },
            { extend: 'excelHtml5', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } },
            { extend: 'pdfHtml5', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } },
            { extend: 'print', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } }
        ],
        order: [[3, 'asc']],
        language: {
            lengthMenu: '_MENU_',
            search: '_INPUT_',
            searchPlaceholder: 'Search...',
            url: 'common/assets/DataTables/languages/' + language + '.json'
        }
    });
    lowTable.buttons().container().appendTo('.custom_buttons');
});

function createBulkPurchaseOrder() {
    var items = [];
    $('#lowStockTable tbody tr').each(function() {
        if ($(this).find('td').length > 1) {  // Skip the "no data" row
            var row = $(this);
            var itemCode = row.find('td:first').text().trim();
            var suggestedOrder = parseInt(row.find('.badge-success').text().trim());
            
            if (suggestedOrder > 0) {
                items.push({
                    code: itemCode,
                    quantity: suggestedOrder
                });
            }
        }
    });
    
    if (items.length > 0) {
        // Redirect to purchase order creation with items
        var itemsParam = encodeURIComponent(JSON.stringify(items));
        window.location.href = 'inventory/purchase/add?bulk_items=' + itemsParam;
    } else {
        alert('No items selected for bulk order.');
    }
}
</script>