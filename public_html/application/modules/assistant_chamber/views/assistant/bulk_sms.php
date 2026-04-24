<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header"><h1>Bulk SMS</h1></section>
    <section class="content">
        <p class="text-muted small">Outbound channel is SMS with credits. <strong>AI / automated voice calls</strong> to this queue are not wired yet — use telephony integration separately if required.</p>
        <?php if ($this->session->flashdata('chamber_bulk_msg')) : ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($this->session->flashdata('chamber_bulk_msg')); ?></div>
        <?php endif; ?>
        <form method="post" action="<?php echo site_url('assistant_chamber/bulk_sms_send'); ?>">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="form-group"><label>Phone numbers (comma or newline separated)</label><textarea name="phones" class="form-control" rows="5" required><?php echo isset($phones_prefill) ? htmlspecialchars($phones_prefill) : ''; ?></textarea></div>
            <div class="form-group"><label>Quick templates</label><br>
                <button type="button" class="btn btn-sm btn-outline-secondary mb-1 tpl" data-tpl="The doctor is on the way to the chamber. Thank you for waiting.">Doctor on the way</button>
                <button type="button" class="btn btn-sm btn-outline-secondary mb-1 tpl" data-tpl="The chamber is closed today. Please contact reception to reschedule. Thank you.">Chamber closed today</button>
                <button type="button" class="btn btn-sm btn-outline-secondary mb-1 tpl" data-tpl="Please arrive 10 minutes before your serial time. Bring any previous reports.">Arrive early reminder</button>
            </div>
            <div class="form-group"><label>Message</label><textarea id="bulkMsg" name="message" class="form-control" rows="3" required></textarea></div>
            <button class="btn btn-primary" type="submit">Send / deduct credits</button>
        </form>
        <script>
        (function(){
            $('.tpl').on('click', function(){ $('#bulkMsg').val($(this).data('tpl')); });
        })();
        </script>
    </section>
</div>
