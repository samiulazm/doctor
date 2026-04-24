<div class="content-wrapper bg-gradient-light" style="min-height: 2726.9px;">
    <section class="content-header py-4 bg-white shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="display-4 font-weight-black mb-0">
                        <i class="fas fa-tags text-primary mr-3"></i>
                        <?php echo lang('inventory_categories') ?>
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item"><a href="inventory"><?php echo lang('inventory'); ?></a></li>
                            <li class="breadcrumb-item active"><?php echo lang('categories'); ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="col-sm-6 text-right">
                    <a data-bs-toggle="modal" href="#addCategoryModal" class="btn btn-success btn-sm px-4 py-3">
                        <i class="fa fa-plus-circle"></i> <?php echo lang('add_inventory_category'); ?>
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
            
            <?php if ($this->session->flashdata('debug')) { ?>
                <div class="alert alert-info alert-dismissible fade show">
                    <button type="button" class="close" data-bs-dismiss="alert">&times;</button>
                    <?php echo $this->session->flashdata('debug'); ?>
                </div>
            <?php } ?>
            
         
            
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-lg border-0">
                        <div class="card-header">
                            <h3 class="card-title text-black font-weight-800"><?php echo lang('all') . ' ' . lang('inventory_categories'); ?></h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body bg-light">
                            <div class="table-responsive">
                                <table class="table table-hover" id="categoriesTable">
                                    <thead>
                                        <tr class="bg-light">
                                            <th class="font-weight-bold text-uppercase"><?php echo lang('name'); ?></th>
                                            <th class="font-weight-bold text-uppercase"><?php echo lang('description'); ?></th>
                                            <th class="font-weight-bold text-uppercase"><?php echo lang('parent_category'); ?></th>
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

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Enhanced Modal Header -->
            <div class="modal-header bg-gradient-success text-white border-0">
                <div class="d-flex align-items-center">
                    <div class="modal-icon-wrapper mr-3">
                        <i class="fas fa-plus-circle fa-2x text-white-50"></i>
                    </div>
                    <div>
                        <h4 class="modal-title font-weight-bold mb-1" id="addCategoryModalLabel">
                            <i class="fas fa-tags mr-2"></i><?php echo lang('add_inventory_category'); ?>
                        </h4>

                    </div>
                </div>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>

            <!-- Enhanced Modal Body -->
            <div class="modal-body p-0">


                <div class="p-4">
                    <?php echo validation_errors('<div class="alert alert-danger alert-dismissible fade show"><button type="button" class="close" data-bs-dismiss="alert">&times;</button>', '</div>'); ?>
                        
                        <form role="form" action="<?php echo base_url('inventory/add_category'); ?>" method="post" id="addCategoryForm">
                        <div class="row">
                            <!-- Category Name -->
                            <div class="col-md-6">
                            <div class="form-group">
                                    <label for="name" class="font-weight-bold text-dark">
                                        <i class="fas fa-tag text-primary mr-2"></i><?php echo lang('name'); ?> 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0">
                                                <i class="fas fa-signature text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="text" 
                                               class="form-control border-left-0 pl-0" 
                                               name="name" 
                                               id="name" 
                                               placeholder="Enter category name"
                                               required>
                                    </div>

                                </div>
                            </div>
                            
                            <!-- Status -->
                            <div class="col-md-6">
                            <div class="form-group">
                                    <label for="status" class="font-weight-bold text-dark">
                                        <i class="fas fa-toggle-on text-success mr-2"></i><?php echo lang('status'); ?>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0">
                                                <i class="fas fa-check-circle text-muted"></i>
                                            </span>
                                        </div>
                                        <select class="form-control border-left-0" name="status" id="status">
                                            <option value="active" selected>
                                                <i class="fas fa-check-circle text-success"></i> Active
                                            </option>
                                            <option value="inactive">
                                                <i class="fas fa-pause-circle text-warning"></i> Inactive
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            </div>
                            
                        <div class="row">
                            <!-- Parent Category -->
                            <div class="col-md-6">
                            <div class="form-group">
                                    <label for="parent_id" class="font-weight-bold text-dark">
                                        <i class="fas fa-sitemap text-info mr-2"></i><?php echo lang('parent_category'); ?>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0">
                                                <i class="fas fa-layer-group text-muted"></i>
                                            </span>
                                        </div>
                                        <select class="form-control border-left-0" name="parent_id" id="parent_id">
                                            <option value="">
                                                <i class="fas fa-minus-circle"></i> <?php echo lang('select') . ' ' . lang('parent_category'); ?>
                                            </option>
                                    <?php if (!empty($categories)) { ?>
                                        <?php foreach ($categories as $category) { ?>
                                                    <option value="<?php echo $category->id; ?>">
                                                        <i class="fas fa-folder"></i> <?php echo $category->name; ?>
                                                    </option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                                    </div>

                                </div>
                            </div>
                            
                            <!-- Description -->
                            <div class="col-md-6">
                            <div class="form-group">
                                    <label for="description" class="font-weight-bold text-dark">
                                        <i class="fas fa-align-left text-secondary mr-2"></i><?php echo lang('description'); ?>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0">
                                                <i class="fas fa-edit text-muted"></i>
                                            </span>
                                        </div>
                                        <textarea class="form-control border-left-0" 
                                                  name="description" 
                                                  id="description" 
                                                  rows="3"
                                                  placeholder="Enter category description (optional)"></textarea>
                                    </div>

                                </div>
                            </div>
                            </div>
                            
                            <!-- Hidden submit field -->
                            <input type="hidden" name="submit" value="1">
                        </form>
                    </div>
                </div>

            <!-- Enhanced Modal Footer -->
            <div class="modal-footer bg-light border-0 px-4 py-3">
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <div class="text-muted small">
                        <i class="fas fa-asterisk text-danger mr-1"></i>
                        Required fields are marked with *
            </div>
                    <div>
                        <button type="button" class="btn btn-light border mr-2" data-bs-dismiss="modal">
                    <i class="fas fa-times mr-2"></i><?php echo lang('cancel'); ?>
                </button>
                        <button type="submit" form="addCategoryForm" name="submit" class="btn btn-success shadow">
                            <i class="fas fa-save mr-2"></i><?php echo lang('save'); ?> Category
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Enhanced Modal Header -->
            <div class="modal-header bg-gradient-primary text-white border-0">
                <div class="d-flex align-items-center">
                    <div class="modal-icon-wrapper mr-3">
                        <i class="fas fa-edit fa-2x text-white-50"></i>
                    </div>
                    <div>
                        <h4 class="modal-title font-weight-bold mb-1" id="editCategoryModalLabel">
                            <i class="fas fa-tags mr-2"></i><?php echo lang('edit'); ?> <?php echo lang('inventory_category'); ?>
                        </h4>
                        <p class="mb-0 text-white-50 small">Update category information</p>
                    </div>
                </div>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>

            <!-- Enhanced Modal Body -->
            <div class="modal-body p-0">
                <div class="bg-light border-bottom">
                    <div class="container-fluid py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-muted mb-0">
                                    <i class="fas fa-edit mr-2"></i>
                                    Modify the category details below
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <form role="form" action="<?php echo base_url('inventory/edit_category'); ?>" method="post" id="editCategoryForm">
                        <div class="row">
                            <!-- Category Name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_name" class="font-weight-bold text-dark">
                                        <i class="fas fa-tag text-primary mr-2"></i><?php echo lang('name'); ?> 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0">
                                                <i class="fas fa-signature text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="text" 
                                               class="form-control border-left-0 pl-0" 
                                               name="name" 
                                               id="edit_name" 
                                               placeholder="Enter category name"
                                               required>
                                    </div>

                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_status" class="font-weight-bold text-dark">
                                        <i class="fas fa-toggle-on text-success mr-2"></i><?php echo lang('status'); ?>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0">
                                                <i class="fas fa-check-circle text-muted"></i>
                                            </span>
                                        </div>
                                        <select class="form-control border-left-0" name="status" id="edit_status">
                                            <option value="active">
                                                <i class="fas fa-check-circle text-success"></i> Active
                                            </option>
                                            <option value="inactive">
                                                <i class="fas fa-pause-circle text-warning"></i> Inactive
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Parent Category -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_parent_id" class="font-weight-bold text-dark">
                                        <i class="fas fa-sitemap text-info mr-2"></i><?php echo lang('parent_category'); ?>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0">
                                                <i class="fas fa-layer-group text-muted"></i>
                                            </span>
                                        </div>
                                        <select class="form-control border-left-0" name="parent_id" id="edit_parent_id">
                                            <option value="">
                                                <i class="fas fa-minus-circle"></i> <?php echo lang('select') . ' ' . lang('parent_category'); ?>
                                            </option>
                                            <?php if (!empty($categories)) { ?>
                                                <?php foreach ($categories as $category) { ?>
                                                    <option value="<?php echo $category->id; ?>">
                                                        <i class="fas fa-folder"></i> <?php echo $category->name; ?>
                                                    </option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>

                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_description" class="font-weight-bold text-dark">
                                        <i class="fas fa-align-left text-secondary mr-2"></i><?php echo lang('description'); ?>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0">
                                                <i class="fas fa-edit text-muted"></i>
                                            </span>
                                        </div>
                                        <textarea class="form-control border-left-0" 
                                                  name="description" 
                                                  id="edit_description" 
                                                  rows="3"
                                                  placeholder="Enter category description (optional)"></textarea>
                                    </div>

                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden fields -->
                        <input type="hidden" name="category_id" id="edit_category_id">
                        <input type="hidden" name="submit" value="1">
                    </form>
                </div>
            </div>

            <!-- Enhanced Modal Footer -->
            <div class="modal-footer bg-light border-0 px-4 py-3">
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <div class="text-muted small">
                      
                    </div>
                    <div>
                        <button type="button" class="btn btn-light border mr-2" data-bs-dismiss="modal">
                            <i class="fas fa-times mr-2"></i><?php echo lang('cancel'); ?>
                        </button>
                        <button type="submit" form="editCategoryForm" name="submit" class="btn btn-primary shadow">
                            <i class="fas fa-save mr-2"></i><?php echo lang('update'); ?> Category
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>