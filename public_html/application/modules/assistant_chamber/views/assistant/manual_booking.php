<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header"><h1>Manual booking (phone)</h1></section>
    <section class="content">
        <form method="get" class="form-inline mb-3">
            <select name="doctor_id" class="form-control mr-2" onchange="this.form.submit()">
                <option value="">Select doctor</option>
                <?php foreach ($doctors as $d) : ?>
                    <option value="<?php echo (int) $d->id; ?>" <?php echo (!empty($doctor_id) && (int) $doctor_id === (int) $d->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($d->name); ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-default" type="submit">Load chambers</button>
        </form>
        <form method="post" action="<?php echo site_url('assistant_chamber/manual_booking_save'); ?>">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="form-group"><label>Doctor</label>
                <select name="doctor_id" class="form-control" required>
                    <?php foreach ($doctors as $d) : ?>
                        <option value="<?php echo (int) $d->id; ?>" <?php echo (!empty($doctor_id) && (int) $doctor_id === (int) $d->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($d->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Chamber</label>
                <select name="chamber_id" class="form-control" required>
                    <option value="">Select chamber</option>
                    <?php foreach ((isset($chambers) ? $chambers : array()) as $c) : ?>
                        <option value="<?php echo (int) $c->id; ?>"><?php echo htmlspecialchars($c->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Queue date</label><input type="date" name="queue_date" class="form-control" required value="<?php echo date('Y-m-d'); ?>"></div>
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header"><h3 class="card-title">Patient</h3></div>
                <div class="card-body">
                    <p class="small text-muted">Use an existing patient ID, or leave it blank and create/find by phone.</p>
                    <div class="form-row">
                        <div class="col-md-3 mb-2"><label>Existing patient ID</label><input name="patient_id" class="form-control" type="number"></div>
                        <div class="col-md-3 mb-2"><label>Phone</label><input name="patient_phone" class="form-control" placeholder="Required if no ID"></div>
                        <div class="col-md-3 mb-2"><label>Name</label><input name="patient_name" class="form-control" placeholder="Required if no ID"></div>
                        <div class="col-md-1 mb-2"><label>Age</label><input name="age" class="form-control"></div>
                        <div class="col-md-2 mb-2"><label>Gender</label>
                            <select name="gender" class="form-control">
                                <option value="">-</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header"><h3 class="card-title">Triage note</h3></div>
                <div class="card-body">
                    <div class="form-group"><label>Problem / symptom</label><textarea name="symptom" class="form-control" rows="2"></textarea></div>
                    <div class="form-group"><label>Duration</label><input name="duration" class="form-control"></div>
                    <div class="form-group"><label>Remarks</label><input name="remarks" class="form-control" placeholder="Call notes"></div>
                </div>
            </div>
            <button class="btn btn-primary" type="submit">Add to queue</button>
        </form>
    </section>
</div>
