<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="chamber-ui">
    <section class="content-header">
        <div class="chamber-head">
            <div>
                <div class="chamber-kicker">Doctor portal</div>
                <h1>Schedule exceptions</h1>
                <p class="chamber-subtitle mb-0">Vacation dates and one-day timing overrides by chamber.</p>
            </div>
            <a class="btn btn-outline-primary" href="<?php echo site_url('doctor_chamber/my_chambers'); ?>"><i class="fas fa-clinic-medical"></i> Chamber locations</a>
        </div>
    </section>
    <section class="content">
        <div class="chamber-panel"><div class="table-responsive">
        <table class="table chamber-table"><thead><tr><th>Date</th><th>Chamber</th><th>Closed</th><th>Open</th><th>Close</th><th>Reason</th></tr></thead><tbody>
            <?php foreach ($rows as $r) : ?>
                <?php
                $chamber_label = 'All chambers';
                foreach ((isset($chambers) ? $chambers : array()) as $c) {
                    if (!empty($r->chamber_id) && (int) $r->chamber_id === (int) $c->id) {
                        $chamber_label = $c->name;
                        break;
                    }
                }
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($r->exception_date); ?></td>
                    <td><?php echo htmlspecialchars($chamber_label); ?></td>
                    <td><?php echo $r->is_closed ? 'Yes' : 'No'; ?></td>
                    <td><?php echo htmlspecialchars((string) $r->open_time); ?></td>
                    <td><?php echo htmlspecialchars((string) $r->close_time); ?></td>
                    <td><?php echo htmlspecialchars($r->reason); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($rows)) : ?>
                <tr><td colspan="6" class="text-muted text-center py-4">No schedule exceptions have been added.</td></tr>
            <?php endif; ?>
        </tbody></table>
        </div></div>
        <form method="post" action="<?php echo site_url('doctor_chamber/schedule_save'); ?>" class="chamber-panel">
            <div class="chamber-panel-header"><h3 class="chamber-panel-title">Add exception</h3></div>
            <div class="chamber-panel-body">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="form-group"><label>Chamber</label>
                <select name="chamber_id" class="form-control">
                    <option value="">All chambers</option>
                    <?php foreach ((isset($chambers) ? $chambers : array()) as $c) : ?>
                        <option value="<?php echo (int) $c->id; ?>"><?php echo htmlspecialchars($c->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Date</label><input type="date" name="exception_date" class="form-control" required></div>
            <div class="form-check mb-2"><input type="checkbox" name="is_closed" value="1" checked id="cl"><label for="cl"> Chamber closed</label></div>
            <div class="form-row">
                <div class="col-md-6 mb-2"><label>Open time override</label><input name="open_time" class="form-control" placeholder="09:00"></div>
                <div class="col-md-6 mb-2"><label>Close time override</label><input name="close_time" class="form-control" placeholder="17:00"></div>
            </div>
            <div class="form-group"><label>Reason</label><input name="reason" class="form-control"></div>
            <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Save</button>
            </div>
        </form>
    </section>
</div>
