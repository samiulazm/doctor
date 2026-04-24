<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper chamber-ui">
    <section class="content-header">
        <div class="chamber-head">
            <div>
                <div class="chamber-kicker">Assistant portal</div>
                <h1>Bulk SMS &amp; AI call queue</h1>
                <p class="chamber-subtitle mb-0">Notify today's queue or queue a call script for telephony integration.</p>
            </div>
            <a class="btn btn-outline-secondary" href="<?php echo site_url('assistant_chamber/desk'); ?>"><i class="fas fa-arrow-left"></i> Desk</a>
        </div>
    </section>
    <section class="content">
        <p class="text-muted small">Outbound SMS sends immediately when the hospital SMS gateway is configured. AI call requests are queued as integration events for a telephony worker.</p>
        <?php if ($this->session->flashdata('chamber_bulk_msg')) : ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($this->session->flashdata('chamber_bulk_msg')); ?></div>
        <?php endif; ?>
        <form method="post" action="<?php echo site_url('assistant_chamber/bulk_sms_send'); ?>" class="chamber-panel">
            <div class="chamber-panel-header"><h3 class="chamber-panel-title">SMS broadcaster</h3></div>
            <div class="chamber-panel-body">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="form-group"><label>Phone numbers (comma or newline separated)</label><textarea name="phones" class="form-control" rows="5" required><?php echo isset($phones_prefill) ? htmlspecialchars($phones_prefill) : ''; ?></textarea></div>
            <div class="form-group"><label>Quick templates</label><br>
                <button type="button" class="btn btn-sm btn-outline-secondary mb-1 tpl" data-tpl="The doctor is on the way to the chamber. Thank you for waiting.">Doctor on the way</button>
                <button type="button" class="btn btn-sm btn-outline-secondary mb-1 tpl" data-tpl="The chamber is closed today. Please contact reception to reschedule. Thank you.">Chamber closed today</button>
                <button type="button" class="btn btn-sm btn-outline-secondary mb-1 tpl" data-tpl="Please arrive 10 minutes before your serial time. Bring any previous reports.">Arrive early reminder</button>
            </div>
            <div class="form-group"><label>Message</label><textarea id="bulkMsg" name="message" class="form-control" rows="3" required></textarea></div>
            <button class="btn btn-primary" type="submit"><i class="fas fa-paper-plane"></i> Send / deduct credits</button>
            </div>
        </form>
        <script>
        (function(){
            $('.tpl').on('click', function(){ $('#bulkMsg').val($(this).data('tpl')); });
        })();
        </script>
        <form method="post" action="<?php echo site_url('assistant_chamber/bulk_call_request'); ?>" class="chamber-panel">
            <div class="chamber-panel-header"><h3 class="chamber-panel-title">AI call request</h3></div>
            <div class="chamber-panel-body">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="form-group"><label>Phone numbers</label><textarea name="phones" class="form-control" rows="4" required><?php echo isset($phones_prefill) ? htmlspecialchars($phones_prefill) : ''; ?></textarea></div>
            <div class="form-group"><label>Call script</label><textarea name="call_script" class="form-control" rows="3" required>The doctor is on the way to the chamber. Thank you for waiting.</textarea></div>
            <button class="btn btn-outline-primary" type="submit"><i class="fas fa-phone-volume"></i> Queue AI call request</button>
            </div>
        </form>
    </section>
</div>
