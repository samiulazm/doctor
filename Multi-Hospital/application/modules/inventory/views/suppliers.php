<div class="content-wrapper bg-gradient-light" style="min-height: 2726.9px;">
    <section class="content-header py-4 bg-white shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="display-4 font-weight-black mb-0">
                        <i class="fas fa-building text-primary mr-3"></i>
                        <?php echo lang('suppliers') ?>
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item"><a href="inventory"><?php echo lang('inventory'); ?></a></li>
                            <li class="breadcrumb-item active"><?php echo lang('suppliers'); ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="col-sm-6 text-right">
                    <a data-bs-toggle="modal" href="#addSupplierModal" class="btn btn-success btn-sm px-4 py-3">
                        <i class="fa fa-plus-circle"></i> <?php echo lang('add_supplier'); ?>
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
            
            <?php if ($this->session->flashdata('warning')) { ?>
                <div class="alert alert-warning alert-dismissible fade show">
                    <button type="button" class="close" data-bs-dismiss="alert">&times;</button>
                    <?php echo $this->session->flashdata('warning'); ?>
                </div>
            <?php } ?>
            
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-lg border-0">
                        <div class="card-header">
                            <h3 class="card-title text-black font-weight-800"><?php echo lang('all') . ' ' . lang('suppliers'); ?></h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body bg-light">
                            <div class="table-responsive">
                                <table class="table table-hover" id="suppliersTable">
                                    <thead>
                                        <tr class="bg-light">
                                            <th class="font-weight-bold text-uppercase"><?php echo lang('name'); ?></th>
                                            <th class="font-weight-bold text-uppercase"><?php echo lang('company'); ?></th>
                                            <th class="font-weight-bold text-uppercase"><?php echo lang('contact_person'); ?></th>
                                            <th class="font-weight-bold text-uppercase"><?php echo lang('email'); ?></th>
                                            <th class="font-weight-bold text-uppercase"><?php echo lang('phone'); ?></th>
                                            <th class="font-weight-bold text-uppercase"><?php echo lang('city'); ?></th>
                                            <th class="font-weight-bold text-uppercase"><?php echo lang('current_balance'); ?></th>
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

<!-- Add Supplier Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" role="dialog" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Enhanced Modal Header -->
            <div class="modal-header bg-gradient-warning text-white border-0">
                <div class="d-flex align-items-center">
                    <div class="modal-icon-wrapper mr-3">
                        <i class="fas fa-truck fa-2x text-white-50"></i>
                    </div>
                    <div>
                        <h4 class="modal-title font-weight-bold mb-1" id="addSupplierModalLabel">
                            <i class="fas fa-plus-circle mr-2"></i><?php echo lang('add_supplier'); ?>
                        </h4>
                        <p class="mb-0 text-white-50 small">Add a new inventory supplier</p>
                    </div>
                </div>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                        
                        <form role="form" action="<?php echo base_url('inventory/supplier/add'); ?>" method="post" id="addSupplierForm">
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <h6 class="mb-3"><i class="fas fa-info-circle mr-2"></i><?php echo lang('basic_information'); ?></h6>
                                    
                                    <div class="form-group">
                                        <label for="name"><?php echo lang('supplier_name'); ?> <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" id="name" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="company_name"><?php echo lang('company_name'); ?></label>
                                        <input type="text" class="form-control" name="company_name" id="company_name">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="contact_person"><?php echo lang('contact_person'); ?></label>
                                        <input type="text" class="form-control" name="contact_person" id="contact_person">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="email"><?php echo lang('email'); ?></label>
                                        <input type="email" class="form-control" name="email" id="email">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="phone"><?php echo lang('phone'); ?></label>
                                        <input type="text" class="form-control" name="phone" id="phone">
                                    </div>
                                </div>
                                
                                <!-- Address & Financial Information -->
                                <div class="col-md-6">
                                    <h6 class="mb-3"><i class="fas fa-map-marker-alt mr-2"></i><?php echo lang('address_information'); ?></h6>
                                    
                                    <div class="form-group">
                                        <label for="address"><?php echo lang('address'); ?></label>
                                        <textarea class="form-control" name="address" id="address" rows="2"></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="city"><?php echo lang('city'); ?></label>
                                        <input type="text" class="form-control" name="city" id="city">
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
                                        <label for="status"><?php echo lang('status'); ?></label>
                                        <select class="form-control" name="status" id="status">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Hidden fields with default values -->
                            <input type="hidden" name="mobile" value="">
                            <input type="hidden" name="state" value="">
                            <input type="hidden" name="country" value="">
                            <input type="hidden" name="postal_code" value="">
                            <input type="hidden" name="tax_number" value="">
                            <input type="hidden" name="bank_name" value="">
                            <input type="hidden" name="bank_account" value="">
                            <input type="hidden" name="credit_limit" value="0">
                            <input type="hidden" name="notes" value="">
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
                <button type="submit" form="addSupplierForm" name="submit" class="btn btn-success">
                    <i class="fas fa-save mr-2"></i><?php echo lang('save'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<!-- Edit Supplier Modal -->
<div class="modal fade" id="editSupplierModal" role="dialog" aria-labelledby="editSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="editSupplierModalLabel"><?php echo lang('edit') . ' ' . lang('supplier'); ?></h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                        
                        <form role="form" action="<?php echo base_url('inventory/supplier/edit'); ?>" method="post" id="editSupplierForm">
                            <input type="hidden" name="supplier_id" id="edit_supplier_id">
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <h6 class="mb-3"><i class="fas fa-info-circle mr-2"></i><?php echo lang('basic_information'); ?></h6>
                                    
                                    <div class="form-group">
                                        <label for="edit_name"><?php echo lang('supplier_name'); ?> <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" id="edit_name" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="edit_company_name"><?php echo lang('company_name'); ?></label>
                                        <input type="text" class="form-control" name="company_name" id="edit_company_name">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="edit_contact_person"><?php echo lang('contact_person'); ?></label>
                                        <input type="text" class="form-control" name="contact_person" id="edit_contact_person">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="edit_email"><?php echo lang('email'); ?></label>
                                        <input type="email" class="form-control" name="email" id="edit_email">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="edit_phone"><?php echo lang('phone'); ?></label>
                                        <input type="text" class="form-control" name="phone" id="edit_phone">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="edit_mobile"><?php echo lang('phone') ?: 'Mobile'; ?></label>
                                        <input type="text" class="form-control" name="mobile" id="edit_mobile">
                                    </div>
                                </div>
                                
                                <!-- Address Information -->
                                <div class="col-md-6">
                                    <h6 class="mb-3"><i class="fas fa-map-marker-alt mr-2"></i><?php echo lang('address_information') ?: 'Address Information'; ?></h6>
                                    
                                    <div class="form-group">
                                        <label for="edit_address"><?php echo lang('address'); ?></label>
                                        <textarea class="form-control" name="address" id="edit_address" rows="3"></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="edit_city">City:</label>
                                        <input type="text" class="form-control" name="city" id="edit_city">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="edit_state">State:</label>
                                        <input type="text" class="form-control" name="state" id="edit_state">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="edit_country">Country:</label>
                                        <input type="text" class="form-control" name="country" id="edit_country">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="edit_postal_code">Postal Code:</label>
                                        <input type="text" class="form-control" name="postal_code" id="edit_postal_code">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="edit_status"><?php echo lang('status'); ?></label>
                                        <select class="form-control" name="status" id="edit_status">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Additional fields -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_tax_number">Tax Number:</label>
                                        <input type="text" class="form-control" name="tax_number" id="edit_tax_number">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="edit_bank_name">Bank Name:</label>
                                        <input type="text" class="form-control" name="bank_name" id="edit_bank_name">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="edit_bank_account">Bank Account:</label>
                                        <input type="text" class="form-control" name="bank_account" id="edit_bank_account">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_payment_terms"><?php echo lang('payment_terms'); ?></label>
                                        <input type="text" class="form-control" name="payment_terms" id="edit_payment_terms">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="edit_credit_limit"><?php echo lang('credit_limit'); ?></label>
                                        <input type="number" class="form-control" name="credit_limit" id="edit_credit_limit" value="0">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="edit_notes">Notes:</label>
                                        <textarea class="form-control" name="notes" id="edit_notes" rows="3"></textarea>
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
                <button type="submit" form="editSupplierForm" name="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i><?php echo lang('update'); ?>
                </button>
            </div>
        </div>
    </div>
</div>