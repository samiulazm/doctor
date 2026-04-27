<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
if (!isset($review) || !is_object($review)) {
    $review = (object) array();
}
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('review'),
        'icon' => 'fas fa-star text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('review'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-end mb-3">
                <a data-toggle="modal" data-target="#myModal" href="#myModal" class="btn btn-sm btn-success">
                    <i class="fa fa-plus-circle mr-1"></i> <?php echo lang('add_new'); ?>
                </a>
            </div>
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('All the review names and related informations'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle text-sm datatables mb-0" id="dt-site" data-legacy-table="editable-sample" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-uppercase"><?php echo lang('image'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('name'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('designation'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('review'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('status'); ?></th>
                                            <th class="text-uppercase no-print"><?php echo lang('options'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($reviews as $row) {
                                            $rv = strip_tags((string) $row->review);
                                            $rv_show = strlen($rv) > 100 ? substr($rv, 0, 100) . '…' : $rv;
                                            ?>
                                            <tr>
                                                <td><img class="img-fluid rounded-circle" style="width: 50px; height: 50px;" src="<?php echo html_escape($row->img, ENT_QUOTES, 'UTF-8'); ?>" alt=""></td>
                                                <td><?php echo html_escape($row->name); ?></td>
                                                <td><?php echo html_escape($row->designation); ?></td>
                                                <td><?php echo html_escape($rv_show); ?></td>
                                                <td><?php echo $row->status == 'Active' ? lang('active') : lang('in_active'); ?></td>
                                                <td class="no-print text-nowrap">
                                                    <a type="button" class="btn btn-info btn-sm editbutton" title="<?php echo lang('edit'); ?>" data-id="<?php echo (int) $row->id; ?>"><i class="fa fa-edit"></i></a>
                                                    <a class="btn btn-danger btn-sm ml-1" href="site/review/delete?id=<?php echo (int) $row->id; ?>"
                                                        title="<?php echo lang('delete'); ?>"
                                                        onclick="return confirm('<?php echo lang('are_you_sure_you_want_to_delete_this_item'); ?>');"><i class="fa fa-trash"></i></a>
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

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="addReviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h4 class="modal-title text-white font-weight-bold" id="addReviewLabel"><?php echo lang('add_review'); ?></h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body">
                <form role="form" action="site/review/addNew" class="clearfix" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label><?php echo lang('name'); ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" name="name" required>
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('designation'); ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" name="designation" required>
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('review'); ?> <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-lg" name="review" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('status'); ?> <span class="text-danger">*</span></label>
                        <select class="form-control form-control-lg" name="status" required>
                            <option value="Active" selected><?php echo lang('active'); ?></option>
                            <option value="Inactive"><?php echo lang('in_active'); ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo lang('image'); ?></label>
                        <div class="fileupload fileupload-new" data-provides="fileupload">
                            <div class="fileupload-new thumbnail img_class mb-2">
                                <img src="" height="150" alt="" />
                            </div>
                            <span class="btn btn-white btn-file">
                                <span class="btn fileupload-new badge badge-secondary"><i class="fa fa-paper-clip"></i> <?php echo lang('select_image'); ?></span>
                                <input type="file" class="default" name="img_url">
                            </span>
                        </div>
                    </div>
                    <input type="hidden" name="id" value="">
                    <div class="form-group mb-0">
                        <button type="submit" name="submit" class="btn btn-primary float-right"><?php echo lang('submit'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="editReviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h4 class="modal-title text-white font-weight-bold" id="editReviewLabel"><?php echo lang('edit_review'); ?></h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>">&times;</button>
            </div>
            <div class="modal-body">
                <form role="form" id="editSlideForm" class="clearfix" action="site/review/addNew" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label><?php echo lang('name'); ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" name="name" required>
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('designation'); ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" name="designation" required>
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('review'); ?> <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-lg" name="review" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('status'); ?> <span class="text-danger">*</span></label>
                        <select class="form-control form-control-lg" name="status" required>
                            <option value="Active" <?php echo (!empty($review->status) && $review->status == 'Active') ? 'selected' : ''; ?>><?php echo lang('active'); ?></option>
                            <option value="Inactive" <?php echo (!empty($review->status) && $review->status == 'Inactive') ? 'selected' : ''; ?>><?php echo lang('in_active'); ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo lang('image'); ?></label>
                        <div class="fileupload fileupload-new" data-provides="fileupload">
                            <div class="fileupload-new thumbnail img_class mb-2">
                                <img src="" id="img" height="150" alt="" />
                            </div>
                            <span class="btn btn-white btn-file">
                                <span class="btn fileupload-new badge badge-secondary"><i class="fa fa-paper-clip"></i> <?php echo lang('select_image'); ?></span>
                                <input type="file" class="default" name="img_url">
                            </span>
                        </div>
                    </div>
                    <input type="hidden" name="id" value="">
                    <div class="form-group mb-0">
                        <button type="submit" name="submit" class="btn btn-primary float-right"><?php echo lang('submit'); ?></button>
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
<script src="common/extranal/js/site/review.js"></script>
