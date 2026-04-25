<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();

if (!function_exists('file_module_preview')) {
    function file_module_preview($file) {
        $ext_parts = explode('.', (string) $file->img_url);
        $len = count($ext_parts);
        $extension = $len > 0 ? strtolower($ext_parts[$len - 1]) : '';
        $title_esc = html_escape($file->title, ENT_QUOTES, 'UTF-8');
        $url_esc = html_escape($file->img_url, ENT_QUOTES, 'UTF-8');
        if (strtolower($extension) === 'pdf') {
            return '<a download class="example-image-link" href="' . $url_esc . '" data-title="' . $title_esc . '" target="_blank" rel="noopener">'
                . '<img class="example-image" src="uploads/image/pdf.png" width="100" height="100" alt=""></a>';
        }
        if (strtolower($extension) === 'docx') {
            return '<a download class="example-image-link" href="' . $url_esc . '" data-title="' . $title_esc . '">'
                . '<img class="example-image" src="uploads/image/docx.png" width="100" height="100" alt=""></a>';
        }
        if (strtolower($extension) === 'doc') {
            return '<a download class="example-image-link" href="' . $url_esc . '" data-title="' . $title_esc . '">'
                . '<img class="example-image" src="uploads/image/doc.png" width="100" height="100" alt=""></a>';
        }
        if (strtolower($extension) === 'odt') {
            return '<a download class="example-image-link" href="' . $url_esc . '" data-title="' . $title_esc . '">'
                . '<img class="example-image" src="uploads/image/odt.png" width="100" height="100" alt=""></a>';
        }
        $light_url = isset($file->url) ? $file->url : $file->img_url;
        $light_esc = html_escape($light_url, ENT_QUOTES, 'UTF-8');
        return '<a download class="example-image-link" href="' . $url_esc . '" data-lightbox="example-1" data-title="' . $title_esc . '">'
            . '<img class="example-image" src="' . $light_esc . '" width="100" height="100" alt=""></a>';
    }
}
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('file_manager'),
        'icon' => 'fas fa-folder text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('file_manager'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                <div class="d-flex flex-wrap justify-content-end mb-3">
                    <a href="file/addNewView" class="btn btn-sm btn-success">
                        <i class="fa fa-plus-circle mr-1"></i> <?php echo lang('add_new'); ?>
                    </a>
                </div>
            <?php } ?>
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('All the files'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle text-sm datatables mb-0" id="editable-sample" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-uppercase"><?php echo lang('title'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('file'); ?></th>
                                            <th class="text-uppercase no-print"><?php echo lang('options'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                                            <?php foreach ($files as $file) {
                                                $preview = file_module_preview($file);
                                            ?>
                                                <tr>
                                                    <td><?php echo html_escape($file->title); ?></td>
                                                    <td><?php echo $preview; ?></td>
                                                    <td class="no-print">
                                                        <a class="btn btn-info btn-sm" href="<?php echo html_escape($file->img_url, ENT_QUOTES, 'UTF-8'); ?>" download>
                                                            <?php echo lang('download'); ?>
                                                        </a>
                                                        <a class="btn btn-danger btn-sm" href="file/delete?id=<?php echo (int) $file->id; ?>"
                                                            title="<?php echo lang('delete'); ?>"
                                                            onclick="return confirm('<?php echo lang('are_you_sure_you_want_to_delete_this_item'); ?>');">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                        <?php
                                        if ($this->ion_auth->in_group(array('Doctor'))) {
                                            foreach ($files as $file) {
                                                $modules = array_map('trim', explode(',', (string) $file->module));
                                                if (!in_array('doctor', $modules, true)) {
                                                    continue;
                                                }
                                                $img_url_esc = html_escape($file->img_url, ENT_QUOTES, 'UTF-8');
                                                ?>
                                                <tr>
                                                    <td><?php echo html_escape($file->title); ?></td>
                                                    <td><img class="img_src_class" src="<?php echo $img_url_esc; ?>" alt=""></td>
                                                    <td class="no-print">
                                                        <a class="btn btn-info btn-sm" href="<?php echo $img_url_esc; ?>" download>
                                                            <?php echo lang('download'); ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                        <?php
                                        if ($this->ion_auth->in_group(array('Nurse'))) {
                                            foreach ($files as $file) {
                                                $modules = array_map('trim', explode(',', (string) $file->module));
                                                if (!in_array('nurse', $modules, true)) {
                                                    continue;
                                                }
                                                $img_url_esc = html_escape($file->img_url, ENT_QUOTES, 'UTF-8');
                                                ?>
                                                <tr>
                                                    <td><?php echo html_escape($file->title); ?></td>
                                                    <td><img class="img_src_class" src="<?php echo $img_url_esc; ?>" alt=""></td>
                                                    <td class="no-print">
                                                        <a class="btn btn-info btn-sm" href="<?php echo $img_url_esc; ?>" download>
                                                            <?php echo lang('download'); ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                        <?php
                                        if ($this->ion_auth->in_group(array('Accountant'))) {
                                            foreach ($files as $file) {
                                                $modules = array_map('trim', explode(',', (string) $file->module));
                                                if (!in_array('accountant', $modules, true)) {
                                                    continue;
                                                }
                                                $img_url_esc = html_escape($file->img_url, ENT_QUOTES, 'UTF-8');
                                                ?>
                                                <tr>
                                                    <td><?php echo html_escape($file->title); ?></td>
                                                    <td><img class="img_src_class" src="<?php echo $img_url_esc; ?>" alt=""></td>
                                                    <td class="no-print">
                                                        <a class="btn btn-info btn-sm" href="<?php echo $img_url_esc; ?>" download>
                                                            <?php echo lang('download'); ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                        <?php
                                        if ($this->ion_auth->in_group(array('Pharmacist'))) {
                                            foreach ($files as $file) {
                                                $modules = array_map('trim', explode(',', (string) $file->module));
                                                if (!in_array('pharmacist', $modules, true)) {
                                                    continue;
                                                }
                                                $img_url_esc = html_escape($file->img_url, ENT_QUOTES, 'UTF-8');
                                                ?>
                                                <tr>
                                                    <td><?php echo html_escape($file->title); ?></td>
                                                    <td><img class="img_src_class" src="<?php echo $img_url_esc; ?>" alt=""></td>
                                                    <td class="no-print">
                                                        <a class="btn btn-info btn-sm" href="<?php echo $img_url_esc; ?>" download>
                                                            <?php echo lang('download'); ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                        <?php
                                        if ($this->ion_auth->in_group(array('Laboratorist'))) {
                                            foreach ($files as $file) {
                                                $modules = array_map('trim', explode(',', (string) $file->module));
                                                if (!in_array('laboratorist', $modules, true)) {
                                                    continue;
                                                }
                                                $img_url_esc = html_escape($file->img_url, ENT_QUOTES, 'UTF-8');
                                                ?>
                                                <tr>
                                                    <td><?php echo html_escape($file->title); ?></td>
                                                    <td><img class="img_src_class" src="<?php echo $img_url_esc; ?>" alt=""></td>
                                                    <td class="no-print">
                                                        <a class="btn btn-info btn-sm" href="<?php echo $img_url_esc; ?>" download>
                                                            <?php echo lang('download'); ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                        <?php
                                        if ($this->ion_auth->in_group(array('Receptionist'))) {
                                            foreach ($files as $file) {
                                                $modules = array_map('trim', explode(',', (string) $file->module));
                                                if (!in_array('receptionist', $modules, true)) {
                                                    continue;
                                                }
                                                $img_url_esc = html_escape($file->img_url, ENT_QUOTES, 'UTF-8');
                                                ?>
                                                <tr>
                                                    <td><?php echo html_escape($file->title); ?></td>
                                                    <td><img class="img_src_class" src="<?php echo $img_url_esc; ?>" alt=""></td>
                                                    <td class="no-print">
                                                        <a class="btn btn-info btn-sm" href="<?php echo $img_url_esc; ?>" download>
                                                            <?php echo lang('download'); ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        }
                                        ?>
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

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
</script>
<script src="common/extranal/js/file.js"></script>
