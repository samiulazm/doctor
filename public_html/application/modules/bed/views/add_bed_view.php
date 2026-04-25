<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
if (!isset($bed)) {
    $bed = (object) array();
}
$is_edit = !empty($bed->id);
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => $is_edit ? lang('edit_bed') : lang('add_bed'),
        'icon' => 'fas fa-bed text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('bed'), 'url' => 'bed'),
            array('label' => $is_edit ? lang('edit_bed') : lang('add_bed'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-body p-4 p-md-5">
                            <?php echo validation_errors(); ?>
                            <form role="form" action="bed/addBed" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">

                                <div class="form-group mb-4">
                                    <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('bed_category'); ?> <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-lg shadow-sm" name="category" required="">
                                        <?php foreach ($categories as $category) { ?>
                                            <option value="<?php echo $category->category; ?>" <?php
                                                                                                if (!empty($setval)) {
                                                                                                    if ($category->category == set_value('category')) {
                                                                                                        echo 'selected';
                                                                                                    }
                                                                                                }
                                                                                                if (!empty($bed->category)) {
                                                                                                    if ($category->category == $bed->category) {
                                                                                                        echo 'selected';
                                                                                                    }
                                                                                                }
                                                                                                ?>><?php echo $category->category; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('bed_number'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg shadow-sm" name="number" value='<?php
                                                                                                                            if (!empty($setval)) {
                                                                                                                                echo set_value('number');
                                                                                                                            }
                                                                                                                            if (!empty($bed->number)) {
                                                                                                                                echo $bed->number;
                                                                                                                            }
                                                                                                                            ?>' required="">
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-uppercase font-weight-bold text-muted"><?php echo lang('description'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg shadow-sm" name="description" value='<?php
                                                                                                                                if (!empty($setval)) {
                                                                                                                                    echo set_value('description');
                                                                                                                                }
                                                                                                                                if (!empty($bed->description)) {
                                                                                                                                    echo $bed->description;
                                                                                                                                }
                                                                                                                                ?>' required="">
                                </div>

                                <input type="hidden" name="id" value='<?php
                                                                        if (!empty($bed->id)) {
                                                                            echo $bed->id;
                                                                        }
                                                                        ?>'>

                                <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block">
                                    <i class="fas fa-check-circle mr-3"></i><?php echo lang('submit'); ?>
                                </button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
