<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Minimal HTML shell for prescription composer when loaded in an iframe (embed=1).
 * Pairs with home/footer.php when $iframe_embed is true — avoids stray </main> and
 * missing document wrapper that broke layout and footer scripts.
 */
$settings = isset($settings) && is_object($settings) ? $settings : null;
$page_title = lang('prescription');
if ($settings && !empty($settings->system_vendor)) {
    $page_title .= ' | ' . $settings->system_vendor;
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <base href="<?php echo base_url(); ?>">
    <script>window.CI_BASE_URL = <?php echo json_encode(rtrim(base_url(), '/') . '/'); ?>;</script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="adminlte/dist/css/bs4-compat.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;1,9..40,400&display=swap">
    <link rel="stylesheet" href="adminlte/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?php echo asset_url('application/assets/css/app-design-tokens.css'); ?>">
    <link rel="stylesheet" href="adminlte/dist/css/changes.css">
    <link rel="stylesheet" href="common/css/chamber-practice.css">
    <link rel="stylesheet" href="adminlte/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
    <link rel="stylesheet" href="adminlte/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="common/assets/bootstrap-datepicker/css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="common/assets/bootstrap-datetimepicker/css/datetimepicker.css">
    <link rel="stylesheet" href="common/assets/bootstrap-timepicker/compiled/timepicker.css">
    <link rel="stylesheet" type="text/css" href="common/assets/jquery-multi-select/css/multi-select.css">
    <link rel="stylesheet" href="adminlte/plugins/toastr/toastr.min.css">
    <link rel="stylesheet" href="adminlte/plugins/dropzone/min/dropzone.min.css">
    <link rel="stylesheet" href="adminlte/plugins/summernote/summernote-bs4.min.css">
</head>
<body<?php echo ($this->session->userdata('darkMode') == 1) ? ' data-bs-theme="dark"' : ''; ?>>
<div class="prescription-iframe-root">
