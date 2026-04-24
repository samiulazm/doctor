<div class="content-wrapper bg-gradient-light" style="min-height: 2726.9px;">
    <section class="content-header py-4 bg-white shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="display-4 font-weight-black mb-0">
                        <i class="fas fa-shopping-cart text-primary mr-3"></i>
                        <?php echo lang('purchase_orders') ?>
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item"><a href="inventory"><?php echo lang('inventory'); ?></a></li>
                            <li class="breadcrumb-item active"><?php echo lang('purchase_orders'); ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="col-sm-6 text-right">
                    <a data-bs-toggle="modal" href="#addPurchaseOrderModal" class="btn btn-success btn-sm px-4 py-3">
                        <i class="fa fa-plus-circle"></i> <?php echo lang('create_purchase_order'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="content py-5">
        <div class="container-fluid">
            <!-- Flash Messages -->
            <?php if ($this->session->flashdata('success')) { ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" data-bs-dismiss="alert">&times;</button>
                    <?php echo $this->session->flashdata('success'); ?>
                </div>
            <?php } ?>
            
            <?php if ($this->session->flashdata('error')) { ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" data-bs-dismiss="alert">&times;</button>
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
            <?php } ?>
            
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-lg border-0">
                        <div class="card-header">
                            <h3 class="card-title text-black font-weight-800"><?php echo lang('all') . ' ' . lang('purchase_orders'); ?></h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body bg-light">
                            <div class="table-responsive">
                                <table class="table table-hover" id="purchaseOrdersTable">
                                    <thead>
                                        <tr class="bg-light">
                                            <th class="font-weight-bold text-uppercase"><?php echo lang('po_number'); ?></th>
                                            <th class="font-weight-bold text-uppercase"><?php echo lang('supplier_name'); ?></th>
                                            <th class="font-weight-bold text-uppercase"><?php echo lang('order_date'); ?></th>
                                            <th class="font-weight-bold text-uppercase"><?php echo lang('expected_delivery_date'); ?></th>
                                            <th class="font-weight-bold text-uppercase">Total Quantity</th>
                                            <th class="font-weight-bold text-uppercase"><?php echo lang('grand_total'); ?></th>
                                            <th class="font-weight-bold text-uppercase"><?php echo lang('status'); ?></th>
                                            <th class="font-weight-bold text-uppercase"><?php echo lang('options'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Server-side DataTables will populate this -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add Purchase Order Modal -->
<div class="modal fade" id="addPurchaseOrderModal" role="dialog" aria-labelledby="addPurchaseOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="addPurchaseOrderModalLabel"><?php echo lang('create_purchase_order'); ?></h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                        
                        <form role="form" action="<?php echo base_url('inventory/add_purchase_order'); ?>" method="post" id="addPurchaseOrderForm">
                            <div class="row">
                                <!-- Order Information -->
                                <div class="col-md-6">
                                    <h6 class="mb-3"><i class="fas fa-info-circle mr-2"></i><?php echo lang('order_information'); ?></h6>
                                    
                                    <div class="form-group">
                                        <label for="po_number"><?php echo lang('po_number'); ?></label>
                                        <input type="text" class="form-control" name="po_number" id="po_number" 
                                               value="<?php echo 'PO' . date('YmdHis'); ?>" readonly>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="supplier_id"><?php echo lang('supplier_name'); ?> <span class="text-danger">*</span></label>
                                        <select class="form-control" name="supplier_id" id="supplier_id" required>
                                            <option value=""><?php echo lang('select') . ' ' . lang('supplier_name'); ?></option>
                                            <?php if (!empty($suppliers)) { ?>
                                                <?php foreach ($suppliers as $supplier) { ?>
                                                    <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->name; ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="order_date"><?php echo lang('order_date'); ?> <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="order_date" id="order_date" 
                                               value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                
                                <!-- Delivery Information -->
                                <div class="col-md-6">
                                    <h6 class="mb-3"><i class="fas fa-truck mr-2"></i><?php echo lang('delivery_information'); ?></h6>
                                    
                                    <div class="form-group">
                                        <label for="expected_delivery_date"><?php echo lang('expected_delivery_date'); ?></label>
                                        <input type="date" class="form-control" name="expected_delivery_date" id="expected_delivery_date">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="payment_terms"><?php echo lang('payment_terms'); ?></label>
                                        <select class="form-control" name="payment_terms" id="payment_terms">
                                            <option value="">Select Payment Terms</option>
                                            <option value="Net 30">Net 30 Days</option>
                                            <option value="Net 15">Net 15 Days</option>
                                            <option value="COD">Cash on Delivery</option>
                                            <option value="Advance">Advance Payment</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="notes"><?php echo lang('notes'); ?></label>
                                        <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Items Section -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6 class="mb-3"><i class="fas fa-list mr-2"></i>Items to Order</h6>
                                    <div class="card border-secondary">
                                        <div class="card-body p-3">
                                            <div id="purchase-items">
                                                <div class="purchase-item-row mb-3">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>Inventory Item <span class="text-danger">*</span></label>
                                                            <select class="form-control select2-inventory-item" name="items[0][inventory_item_id]" required>
                                                                <option value="">Select Item</option>
                                                                <?php if (!empty($inventory_items)) { ?>
                                                                    <?php foreach ($inventory_items as $item) { ?>
                                                                        <option value="<?php echo $item->id; ?>" data-cost="<?php echo $item->unit_cost; ?>">
                                                                            <?php echo $item->name; ?> (<?php echo $item->item_code; ?>)
                                                                        </option>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label>Quantity <span class="text-danger">*</span></label>
                                                            <input type="number" class="form-control item-quantity" name="items[0][quantity]" 
                                                                   min="1" value="1" required>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label>Unit Price</label>
                                                            <input type="number" class="form-control item-price" name="items[0][unit_price]" 
                                                                   step="0.01" min="0">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label>Total</label>
                                                            <input type="text" class="form-control item-total" readonly>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label>&nbsp;</label>
                                                            <button type="button" class="btn btn-danger btn-sm btn-block remove-item-btn" 
                                                                    style="display: none;">
                                                                <i class="fas fa-trash"></i> Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <button type="button" class="btn btn-secondary btn-sm" id="add-item-btn">
                                                        <i class="fas fa-plus mr-2"></i>Add Another Item
                                                    </button>
                                                </div>
                                                <div class="col-md-6 text-right">
                                                    <strong>Grand Total: <span id="grand-total">0.00</span></strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Hidden fields with default values -->
                            <input type="hidden" name="delivery_address" value="">
                            <!-- Hidden submit field -->
                            <input type="hidden" name="submit" value="1">
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times mr-2"></i><?php echo lang('cancel'); ?>
                </button>
                <button type="submit" form="addPurchaseOrderForm" name="submit" class="btn btn-success">
                    <i class="fas fa-save mr-2"></i><?php echo lang('create'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Purchase Order Modal -->
<div class="modal fade" id="editPurchaseOrderModal" role="dialog" aria-labelledby="editPurchaseOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="editPurchaseOrderModalLabel"><?php echo lang('edit') . ' ' . lang('purchase_order'); ?></h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                        
                        <form role="form" action="<?php echo base_url('inventory/edit_purchase_order'); ?>" method="post" id="editPurchaseOrderForm">
                            <input type="hidden" name="purchase_order_id" id="edit_purchase_order_id">
                            
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <h6 class="mb-3"><i class="fas fa-info-circle mr-2"></i><?php echo lang('order_information'); ?></h6>
                                    
                                    <div class="form-group">
                                        <label for="edit_po_number"><?php echo lang('po_number'); ?> <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="po_number" id="edit_po_number" readonly>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="edit_supplier_id"><?php echo lang('supplier'); ?> <span class="text-danger">*</span></label>
                                        <select class="form-control" name="supplier_id" id="edit_supplier_id" required>
                                            <option value="">Select Supplier</option>
                                            <?php if (!empty($suppliers)) { ?>
                                                <?php foreach ($suppliers as $supplier) { ?>
                                                    <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->name; ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="edit_order_date"><?php echo lang('order_date'); ?> <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="order_date" id="edit_order_date" required>
                                    </div>
                                </div>
                                
                                <!-- Delivery Information -->
                                <div class="col-md-6">
                                    <h6 class="mb-3"><i class="fas fa-truck mr-2"></i><?php echo lang('delivery_information'); ?></h6>
                                    
                                    <div class="form-group">
                                        <label for="edit_expected_delivery_date"><?php echo lang('expected_delivery_date'); ?></label>
                                        <input type="date" class="form-control" name="expected_delivery_date" id="edit_expected_delivery_date">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="edit_payment_terms"><?php echo lang('payment_terms'); ?></label>
                                        <select class="form-control" name="payment_terms" id="edit_payment_terms">
                                            <option value="">Select Payment Terms</option>
                                            <option value="Net 30">Net 30 Days</option>
                                            <option value="Net 15">Net 15 Days</option>
                                            <option value="COD">Cash on Delivery</option>
                                            <option value="Advance">Advance Payment</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="edit_status"><?php echo lang('status'); ?> <span class="text-danger">*</span></label>
                                        <select class="form-control" name="status" id="edit_status" required>
                                            <option value="draft">Draft</option>
                                            <option value="sent">Sent</option>
                                            <option value="confirmed">Confirmed</option>
                                            <option value="partially_received">Partially Received</option>
                                            <option value="completed">Completed</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Update the purchase order status
                                        </small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="edit_notes"><?php echo lang('notes'); ?></label>
                                        <textarea class="form-control" name="notes" id="edit_notes" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Items Section -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6 class="mb-3"><i class="fas fa-list mr-2"></i>Items in Order</h6>
                                    <div class="card border-secondary">
                                        <div class="card-body p-3">
                                            <div id="edit-purchase-items">
                                                <!-- Items will be loaded here -->
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <button type="button" class="btn btn-secondary btn-sm" id="edit-add-item-btn">
                                                        <i class="fas fa-plus mr-2"></i>Add Another Item
                                                    </button>
                                                </div>
                                                <div class="col-md-6 text-right">
                                                    <strong>Grand Total: <span id="edit-grand-total">0.00</span></strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Hidden submit field -->
                            <input type="hidden" name="submit" value="1">
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times mr-2"></i><?php echo lang('cancel'); ?>
                </button>
                <button type="submit" form="editPurchaseOrderForm" name="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i><?php echo lang('update'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Purchase Order Modal -->
<div class="modal fade" id="viewPurchaseOrderModal" role="dialog" aria-labelledby="viewPurchaseOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="viewPurchaseOrderModalLabel">
                    <i class="fas fa-eye mr-2"></i><?php echo lang('purchase_order_details'); ?>
                </h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Purchase Order Information -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-info-circle mr-2"></i><?php echo lang('order_information'); ?></h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="font-weight-bold"><?php echo lang('po_number'); ?>:</td>
                                        <td id="view_po_number">-</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold"><?php echo lang('status'); ?>:</td>
                                        <td><span id="view_status_badge">-</span></td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold"><?php echo lang('order_date'); ?>:</td>
                                        <td id="view_order_date">-</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold"><?php echo lang('expected_delivery_date'); ?>:</td>
                                        <td id="view_expected_delivery_date">-</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold"><?php echo lang('payment_terms'); ?>:</td>
                                        <td id="view_payment_terms">-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Supplier Information -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-building mr-2"></i><?php echo lang('supplier_information'); ?></h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="font-weight-bold"><?php echo lang('name'); ?>:</td>
                                        <td id="view_supplier_name">-</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold"><?php echo lang('company'); ?>:</td>
                                        <td id="view_supplier_company">-</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold"><?php echo lang('contact_person'); ?>:</td>
                                        <td id="view_supplier_contact">-</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold"><?php echo lang('email'); ?>:</td>
                                        <td id="view_supplier_email">-</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold"><?php echo lang('phone'); ?>:</td>
                                        <td id="view_supplier_phone">-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-list mr-2"></i><?php echo lang('ordered_items'); ?></h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="viewItemsTable">
                                        <thead>
                                            <tr>
                                                <th><?php echo lang('item_name'); ?></th>
                                                <th><?php echo lang('item_code'); ?></th>
                                                <th><?php echo lang('quantity_ordered'); ?></th>
                                                <th><?php echo lang('unit_price'); ?></th>
                                                <th><?php echo lang('total_price'); ?></th>
                                                <th><?php echo lang('quantity_received'); ?></th>
                                                <th><?php echo lang('status'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody id="view_items_tbody"> 
                                            <!-- Items will be populated via AJAX --> 
                                        </tbody>   
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Summary -->
                <div class="row mt-3">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-sticky-note mr-2"></i><?php echo lang('notes'); ?></h6>
                            </div>
                            <div class="card-body">
                                <div id="view_notes" class="text-muted">No notes available</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-calculator mr-2"></i><?php echo lang('financial_summary'); ?></h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="font-weight-bold"><?php echo lang('subtotal'); ?>:</td>
                                        <td id="view_subtotal" class="text-right">-</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold"><?php echo lang('tax_amount'); ?>:</td>
                                        <td id="view_tax_amount" class="text-right">-</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold"><?php echo lang('discount'); ?>:</td>
                                        <td id="view_discount_amount" class="text-right">-</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold"><?php echo lang('shipping'); ?>:</td>
                                        <td id="view_shipping_amount" class="text-right">-</td>
                                    </tr>
                                    <tr class="border-top">
                                        <td class="font-weight-bold h5"><?php echo lang('grand_total'); ?>:</td>
                                        <td id="view_grand_total" class="text-right font-weight-bold h5 text-primary">-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times mr-2"></i><?php echo lang('close'); ?>
                </button>
                <a id="view_print_link" href="#" target="_blank" class="btn btn-info">
                    <i class="fas fa-print mr-2"></i><?php echo lang('print'); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<script type="application/json" id="po-inventory-items-json"><?php
echo json_encode(array_values(array_map(function ($item) {
    return array(
        'id' => (int) $item->id,
        'name' => $item->name,
        'item_code' => $item->item_code,
        'unit_cost' => isset($item->unit_cost) ? (float) $item->unit_cost : 0.0,
    );
}, !empty($inventory_items) ? $inventory_items : array())));
?></script>