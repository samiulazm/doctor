<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container chamber-public-container py-5">
    <div class="chamber-public-panel text-center">
        <div class="mb-3">
            <span class="chamber-stat-icon" style="height:54px;width:54px;margin-bottom:0;"><i class="fas fa-check"></i></span>
        </div>
    <h2 class="text-success">Booking confirmed</h2>
    <p class="lead">Your serial number is <strong><?php echo (int) $serial; ?></strong></p>
    <p>Doctor: <?php echo htmlspecialchars($doctorname); ?></p>
    <?php if (!empty($queue_date)) : ?>
    <p>Visit date: <strong><?php echo htmlspecialchars((string) $queue_date); ?></strong></p>
    <?php endif; ?>
    <?php if (!empty($advance_fee) && !empty($queue_id)) : ?>
        <p class="mt-3">Advance booking fee: <strong><?php echo htmlspecialchars((string) $advance_fee); ?> <?php echo htmlspecialchars(isset($currency) ? $currency : 'BDT'); ?></strong></p>
        <div class="chamber-btn-row justify-content-center">
            <a class="btn btn-warning" href="<?php echo site_url('payment_bd/init_sslcommerz?queue_id=' . (int) $queue_id); ?>"><i class="fas fa-credit-card mr-1"></i> Pay with SSLCommerz</a>
            <a class="btn btn-outline-secondary" href="<?php echo site_url('payment_bd/init_bkash?queue_id=' . (int) $queue_id); ?>"><i class="fas fa-wallet mr-1"></i> Pay with bKash</a>
        </div>
    <?php endif; ?>
    <?php if (!empty($queue_id)) : ?>
        <p class="mt-3 mb-2">
            <a class="btn btn-outline-primary" href="<?php echo site_url('portal/queue/' . (int) $queue_id); ?>">
                <i class="fas fa-clock mr-1"></i> View my queue status
            </a>
        </p>
    <?php endif; ?>
    <p class="mt-3"><a class="btn btn-primary" href="<?php echo site_url('portal/d/' . rawurlencode($slug)); ?>">Back</a></p>
    </div>
</div>
