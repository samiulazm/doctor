<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header"><h1>Usage analytics</h1></section>
    <section class="content">
        <h4 class="mt-0">Most active doctors (last 30 days)</h4>
        <p class="text-muted small">Based on <code>usage_analytics_event</code> rows with a doctor id since <?php echo htmlspecialchars($rank_since); ?>.</p>
        <table class="table table-sm table-bordered mb-4" style="max-width:640px">
            <thead><tr><th>#</th><th>Doctor</th><th>Events</th></tr></thead>
            <tbody>
            <?php $rank = 1; foreach ($doctor_rank as $dr) : ?>
                <tr>
                    <td><?php echo $rank++; ?></td>
                    <td><?php echo htmlspecialchars($dr->doctor_name ?: ('ID ' . (int) $dr->doctor_id)); ?></td>
                    <td><?php echo (int) $dr->event_count; ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($doctor_rank)) : ?>
                <tr><td colspan="3" class="text-muted">No doctor-scoped events in this window.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        <h4>Recent events (latest 500)</h4>
        <table class="table table-sm"><thead><tr><th>When</th><th>Hospital</th><th>Doctor</th><th>Event</th><th>Meta</th></tr></thead><tbody>
            <?php foreach ($rows as $r) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($r->created_at); ?></td>
                    <td><?php echo (int) $r->hospital_id; ?></td>
                    <td><?php echo $r->doctor_id ? (int) $r->doctor_id : '—'; ?></td>
                    <td><?php echo htmlspecialchars($r->event_type); ?></td>
                    <td><code class="small"><?php echo htmlspecialchars(substr((string) $r->meta_json, 0, 120)); ?></code></td>
                </tr>
            <?php endforeach; ?>
        </tbody></table>
    </section>
</div>
