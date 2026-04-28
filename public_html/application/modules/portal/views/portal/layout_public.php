<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book serial</title>
    <link rel="stylesheet" href="<?php echo base_url('front/site_assets/vendor/bootstrap/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('adminlte/plugins/fontawesome-free/css/all.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('application/assets/css/app-design-tokens.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('common/css/chamber-practice.css'); ?>">
</head>
<body class="chamber-public">
<a class="ap-skip-link" href="#public-main">Skip to main content</a>
<main id="public-main" tabindex="-1">
<?php echo isset($content) ? $content : ''; ?>
</main>
<script src="<?php echo base_url('adminlte/plugins/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('front/site_assets/vendor/jquery/popper.min.js'); ?>"></script>
<script src="<?php echo base_url('front/site_assets/vendor/bootstrap/js/bootstrap.min.js'); ?>"></script>
</body>
</html>
