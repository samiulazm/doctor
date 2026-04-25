<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
$mod_medicine = (object) array();
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('medicine') . ' ' . lang('stock') . ' ' . lang('alerts'),
        'icon' => 'fas fa-exclamation-triangle text-warning mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('medicine'), 'url' => 'medicine'),
            array('label' => lang('stock') . ' ' . lang('alerts'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-end mb-3">
                <a data-toggle="modal" data-target="#myModal" href="#myModal" class="btn btn-sm btn-success">
                    <i class="fas fa-plus-circle mr-1"></i> <?php echo lang('add'); ?> <?php echo lang('new'); ?> <?php echo lang('medicine'); ?>
                </a>
            </div>
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('medicines'); ?> <?php echo lang('with'); ?> <?php echo lang('low'); ?> <?php echo lang('stock'); ?> <?php echo lang('levels'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle text-sm mb-0" id="editable-sample" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-uppercase"><?php echo lang('id'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('name'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('category'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('store'); ?> <?php echo lang('box'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('purchase'); ?> <?php echo lang('price'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('selling'); ?> <?php echo lang('price'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('quantity'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('generic'); ?> <?php echo lang('name'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('company'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('effects'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('expiry'); ?> <?php echo lang('date'); ?></th>
                                            <th class="text-uppercase no-print"><?php echo lang('actions'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($p_n)) {
                                            $i = $p_n * 50;
                                        } else {
                                            $i = 0;
                                        }
                                        foreach ($medicines as $medicine) {
                                            $i = $i + 1;
                                        ?>
                                            <tr>
                                                <td class="medici_name"><?php echo (int) $i; ?></td>
                                                <td class="medici_name"><?php echo html_escape($medicine->name); ?></td>
                                                <td><?php echo html_escape($medicine->category); ?></td>
                                                <td><?php echo html_escape($medicine->box); ?></td>
                                                <td><?php echo html_escape($settings->currency); ?> <?php echo html_escape($medicine->price); ?></td>
                                                <td><?php echo html_escape($settings->currency); ?> <?php echo html_escape($medicine->s_price); ?></td>
                                                <td>
                                                    <?php
                                                    if ($medicine->quantity <= 0) {
                                                        echo '<p class="os mb-1">' . lang('stock_out') . '</p>';
                                                    } else {
                                                        echo html_escape($medicine->quantity);
                                                    }
                                                    ?>
                                                    <button type="button" class="btn btn-success btn-sm btn_width load" data-toggle="modal" data-id="<?php echo (int) $medicine->id; ?>"><?php echo lang('load'); ?></button>
                                                </td>
                                                <td><?php echo html_escape($medicine->generic); ?></td>
                                                <td><?php echo html_escape($medicine->company); ?></td>
                                                <td><?php echo html_escape($medicine->effects); ?></td>
                                                <td><?php echo html_escape($medicine->e_date); ?></td>
                                                <td class="no-print">
                                                    <a type="button" class="btn btn-info btn-sm editbutton" data-toggle="modal" data-id="<?php echo (int) $medicine->id; ?>"><i class="fa fa-edit"></i></a>
                                                    <a class="btn btn-danger btn-sm" href="medicine/delete?id=<?php echo (int) $medicine->id; ?>" onclick="return confirm('<?php echo lang('are_you_sure_you_want_to_delete_this_item'); ?>');"><i class="fa fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php } ?>
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

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="addMedStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="addMedStockModalLabel"><?php echo lang('add'); ?> <?php echo lang('medicine'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body">
                <form role="form" action="medicine/addNewMedicine" class="clearfix" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label><?php echo lang('name'); ?> &ast;</label>
                        <input type="text" class="form-control form-control-lg" name="name" value="" required="">
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('category'); ?> &ast;</label>
                        <select class="form-control m-bot15" name="category" required="">
                            <?php foreach ($categories as $category) { ?>
                                <option value="<?php echo html_escape($category->category); ?>" <?php
                                    if (!empty($mod_medicine->category) && $category->category == $mod_medicine->category) {
                                        echo 'selected';
                                    }
                                    ?>><?php echo html_escape($category->category); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('purchase'); ?> <?php echo lang('price'); ?> &ast;</label>
                        <input type="text" class="form-control form-control-lg" name="price" value="" required="">
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('selling'); ?> <?php echo lang('price'); ?> &ast;</label>
                        <input type="text" class="form-control form-control-lg" name="s_price" value="" required="">
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('quantity'); ?> &ast;</label>
                        <input type="text" class="form-control form-control-lg" name="quantity" value="" required="">
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('generic'); ?> <?php echo lang('name'); ?> &ast;</label>
                        <input type="text" class="form-control form-control-lg" name="generic" value="" required="">
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('company'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="company" value="">
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('effects'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="effects" value="">
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('store'); ?> <?php echo lang('box'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="box" value="">
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('expiry'); ?> <?php echo lang('date'); ?> &ast;</label>
                        <input type="text" class="form-control default-date-picker readonly" name="e_date" value="" required="">
                    </div>
                    <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-info float-right"><?php echo lang('submit'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="editMedStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="editMedStockModalLabel"><?php echo lang('edit'); ?> <?php echo lang('medicine'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body">
                <form role="form" id="editMedicineForm" class="clearfix" action="medicine/addNewMedicine" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label><?php echo lang('name'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="name" value="">
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('category'); ?></label>
                        <select class="form-control m-bot15" name="category">
                            <?php foreach ($categories as $category) { ?>
                                <option value="<?php echo html_escape($category->category); ?>"><?php echo html_escape($category->category); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('purchase'); ?> <?php echo lang('price'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="price" value="">
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('selling'); ?> <?php echo lang('price'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="s_price" value="">
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('quantity'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="quantity" value="">
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('generic'); ?> <?php echo lang('name'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="generic" value="">
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('company'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="company" value="">
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('effects'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="effects" value="">
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('store'); ?> <?php echo lang('box'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="box" value="">
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('expiry'); ?> <?php echo lang('date'); ?></label>
                        <input type="text" class="form-control default-date-picker" name="e_date" value="">
                    </div>
                    <input type="hidden" name="id" value="">
                    <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-info float-right"><?php echo lang('submit'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal3" role="dialog" aria-labelledby="loadMedStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="loadMedStockModalLabel"><?php echo lang('load'); ?> <?php echo lang('medicine'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body">
                <form role="form" id="editMedicineForm1" class="clearfix" action="medicine/load" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label><?php echo lang('add'); ?> <?php echo lang('quantity'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="qty" value="">
                    </div>
                    <input type="hidden" name="id" value="">
                    <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-info float-right"><?php echo lang('submit'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
</script>
<script src="common/extranal/js/medicine/medicine_stock_alert.js"></script>
