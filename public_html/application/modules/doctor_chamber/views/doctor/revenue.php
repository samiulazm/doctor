<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper chamber-ui">
    <section class="content-header">
        <div class="chamber-head">
            <div>
                <div class="chamber-kicker">Doctor portal</div>
                <h1>Revenue summary</h1>
                <p class="chamber-subtitle mb-0">Collections from consultation, procedure, and legacy rows.</p>
            </div>
        </div>
    </section>
    <section class="content">
        <form method="get" class="chamber-toolbar">
            <input type="date" name="from" value="<?php echo htmlspecialchars($from); ?>" class="form-control mr-2">
            <input type="date" name="to" value="<?php echo htmlspecialchars($to); ?>" class="form-control mr-2">
            <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i> Filter</button>
        </form>
        <div class="chamber-panel"><div class="chamber-panel-body">
        <p class="lead">Consultation fees: <strong><?php echo htmlspecialchars($settings->currency); ?> <?php echo htmlspecialchars((string) $consultation); ?></strong></p>
        <p class="lead">Procedure fees: <strong><?php echo htmlspecialchars($settings->currency); ?> <?php echo htmlspecialchars((string) $procedure); ?></strong></p>
        <p class="lead">Total (linked doctor id): <strong><?php echo htmlspecialchars($settings->currency); ?> <?php echo htmlspecialchars((string) $total); ?></strong></p>
        <?php if (!empty($legacy_total)) : ?>
        <p class="lead">Legacy rows (name only, no doctor id): <strong><?php echo htmlspecialchars($settings->currency); ?> <?php echo htmlspecialchars((string) $legacy_total); ?></strong></p>
        <p class="lead">Combined: <strong><?php echo htmlspecialchars($settings->currency); ?> <?php echo htmlspecialchars((string) $total_combined); ?></strong></p>
        <?php endif; ?>
        <p class="text-muted small">Consultation / procedure split counts only payments linked to your doctor id. Combined adds older payments that matched your name but had no doctor id.</p>
        </div></div>
    </section>
</div>
