<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container py-5 text-center">
    <h2 class="text-success">Booking confirmed</h2>
    <p class="lead">Your serial number is <strong><?php echo (int) $serial; ?></strong></p>
    <p>Doctor: <?php echo htmlspecialchars($doctorname); ?></p>
    <?php if (!empty($queue_date)) : ?>
    <p>Visit date: <strong><?php echo htmlspecialchars((string) $queue_date); ?></strong></p>
    <?php endif; ?>
    <?php if (!empty($advance_fee) && !empty($queue_id)) : ?>
        <p class="mt-3">Advance booking fee: <strong><?php echo htmlspecialchars((string) $advance_fee); ?> <?php echo htmlspecialchars(isset($currency) ? $currency : 'BDT'); ?></strong></p>
        <a class="btn btn-warning mr-2" href="<?php echo site_url('payment_bd/init_sslcommerz?queue_id=' . (int) $queue_id); ?>">Pay with SSLCommerz</a>
        <a class="btn btn-outline-secondary" href="<?php echo site_url('payment_bd/init_bkash?queue_id=' . (int) $queue_id); ?>">Pay with bKash</a>
    <?php endif; ?>
    <p class="mt-3"><a class="btn btn-primary" href="<?php echo site_url('portal/d/' . rawurlencode($slug)); ?>">Back</a></p>
</div>
