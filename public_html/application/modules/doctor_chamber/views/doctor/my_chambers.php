<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="chamber-ui">
    <section class="content-header">
        <div class="chamber-head">
            <div>
                <div class="chamber-kicker">Doctor portal</div>
                <h1>Chamber locations</h1>
                <p class="chamber-subtitle mb-0">Locations, contact details, active status, and weekly hours.</p>
            </div>
            <a class="btn btn-outline-primary" href="<?php echo site_url('doctor_chamber/schedule_exceptions'); ?>"><i class="fas fa-calendar-times"></i> Vacation / closed dates</a>
        </div>
    </section>
    <section class="content">
        <p class="text-muted">Names and contact details shown on the public booking page when multiple chambers exist. Sort order controls the dropdown order.</p>
        <div class="chamber-panel">
            <div class="chamber-panel-header"><h3 class="chamber-panel-title">Add chamber</h3></div>
            <div class="chamber-panel-body">
                <form method="post" action="<?php echo site_url('doctor_chamber/chamber_create'); ?>">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="form-row">
                        <div class="col-md-4 mb-2"><label>Display name</label><input class="form-control" name="name" required placeholder="Evening chamber"></div>
                        <div class="col-md-2 mb-2"><label>Sort</label><input class="form-control" type="number" name="sort_order" value="0"></div>
                        <div class="col-md-3 mb-2"><label>Phone</label><input class="form-control" name="phone"></div>
                        <div class="col-md-3 mb-2 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" value="1" id="new_chamber_active" checked>
                                <label for="new_chamber_active"> Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group"><label>Address / directions</label><textarea class="form-control" name="address" rows="2"></textarea></div>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-plus-circle"></i> Create chamber</button>
                </form>
            </div>
        </div>
        <?php foreach ($rows as $c) : ?>
        <div class="chamber-panel">
            <div class="chamber-panel-body">
                <form method="post" action="<?php echo site_url('doctor_chamber/chamber_update'); ?>">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="chamber_id" value="<?php echo (int) $c->id; ?>">
                    <div class="form-row">
                        <div class="col-md-4 mb-2"><label>Display name</label><input class="form-control" name="name" required value="<?php echo htmlspecialchars($c->name); ?>"></div>
                        <div class="col-md-2 mb-2"><label>Sort</label><input class="form-control" type="number" name="sort_order" value="<?php echo (int) $c->sort_order; ?>"></div>
                        <div class="col-md-3 mb-2"><label>Phone</label><input class="form-control" name="phone" value="<?php echo htmlspecialchars((string) $c->phone); ?>"></div>
                        <div class="col-md-3 mb-2 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" value="1" id="act<?php echo (int) $c->id; ?>" <?php echo !empty($c->is_active) ? 'checked' : ''; ?>>
                                <label for="act<?php echo (int) $c->id; ?>"> Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group"><label>Address / directions</label><textarea class="form-control" name="address" rows="2"><?php echo htmlspecialchars((string) $c->address); ?></textarea></div>
                    <h6 class="mt-3">Weekly hours (optional)</h6>
                    <p class="small text-muted">Open / close times per weekday for this location. Leave blank for days with no regular hours.</p>
                    <div class="table-responsive">
                        <table class="table table-sm chamber-table">
                            <thead><tr><th>Day</th><th>Open</th><th>Close</th></tr></thead>
                            <tbody>
                            <?php
                            $daymap = array(
                                'mon' => 'Monday',
                                'tue' => 'Tuesday',
                                'wed' => 'Wednesday',
                                'thu' => 'Thursday',
                                'fri' => 'Friday',
                                'sat' => 'Saturday',
                                'sun' => 'Sunday',
                            );
                            foreach ($daymap as $dk => $dl) :
                                $wo = isset($c->weekly_hours[$dk]['open']) ? $c->weekly_hours[$dk]['open'] : '';
                                $wc = isset($c->weekly_hours[$dk]['close']) ? $c->weekly_hours[$dk]['close'] : '';
                            ?>
                                <tr>
                                    <td><?php echo $dl; ?></td>
                                    <td><input class="form-control form-control-sm" type="time" name="wh_<?php echo $dk; ?>_open" value="<?php echo htmlspecialchars((string) $wo); ?>"></td>
                                    <td><input class="form-control form-control-sm" type="time" name="wh_<?php echo $dk; ?>_close" value="<?php echo htmlspecialchars((string) $wc); ?>"></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Save chamber</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($rows)) : ?>
            <div class="chamber-empty">No chambers found.</div>
        <?php endif; ?>
    </section>
</div>
