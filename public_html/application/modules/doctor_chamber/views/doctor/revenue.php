<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header"><h1>Revenue summary</h1></section>
    <section class="content">
        <form method="get" class="form-inline mb-3">
            <input type="date" name="from" value="<?php echo htmlspecialchars($from); ?>" class="form-control mr-2">
            <input type="date" name="to" value="<?php echo htmlspecialchars($to); ?>" class="form-control mr-2">
            <button class="btn btn-primary" type="submit">Filter</button>
        </form>
        <p class="lead">Consultation fees: <strong><?php echo htmlspecialchars($settings->currency); ?> <?php echo htmlspecialchars((string) $consultation); ?></strong></p>
        <p class="lead">Procedure fees: <strong><?php echo htmlspecialchars($settings->currency); ?> <?php echo htmlspecialchars((string) $procedure); ?></strong></p>
        <p class="lead">Total (linked doctor id): <strong><?php echo htmlspecialchars($settings->currency); ?> <?php echo htmlspecialchars((string) $total); ?></strong></p>
        <?php if (!empty($legacy_total)) : ?>
        <p class="lead">Legacy rows (name only, no doctor id): <strong><?php echo htmlspecialchars($settings->currency); ?> <?php echo htmlspecialchars((string) $legacy_total); ?></strong></p>
        <p class="lead">Combined: <strong><?php echo htmlspecialchars($settings->currency); ?> <?php echo htmlspecialchars((string) $total_combined); ?></strong></p>
        <?php endif; ?>
        <p class="text-muted small">Consultation / procedure split counts only payments linked to your doctor id. Combined adds older payments that matched your name but had no doctor id.</p>
    </section>
</div>
