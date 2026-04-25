<?php
$CI = get_instance();
if (!isset($sents) || $sents === null) {
    $sents = array();
}
?>
<div class="content-wrapper bg-light">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('sent_messages'),
        'icon' => 'fas fa-paper-plane text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('sms'), 'url' => 'sms'),
            array('label' => lang('sent_messages'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('All the Sent Sms names and related informations'); ?></h3>
                            <a href="sms/sendView" class="btn btn-sm btn-primary"><i class="fas fa-sms mr-1"></i><?php echo lang('send_sms'); ?></a>
                        </div>
                        <div class="card-body p-4">
                            <div class="custom_buttons mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="editable-sample" style="width:100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo lang('date'); ?></th>
                                            <th><?php echo lang('message'); ?></th>
                                            <th><?php echo lang('recipient'); ?></th>
                                            <th class="no-print"><?php echo lang('options'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 0;
                                        foreach ($sents as $sent) {
                                            $i = $i + 1;
                                        ?>
                                            <tr>
                                                <td><?php echo (int) $i; ?></td>
                                                <td class="sent_date"><?php echo date('h:i:s a m/d/y', (int) $sent->date); ?></td>
                                                <td><?php
                                                    if (!empty($sent->message)) {
                                                        echo nl2br(htmlspecialchars($sent->message, ENT_QUOTES, 'UTF-8'));
                                                    }
                                                    ?></td>
                                                <td><?php
                                                    if (!empty($sent->recipient)) {
                                                        echo nl2br(htmlspecialchars($sent->recipient, ENT_QUOTES, 'UTF-8'));
                                                    }
                                                    ?></td>
                                                <td>
                                                    <a class="btn btn-danger btn-sm" href="sms/delete?id=<?php echo (int) $sent->id; ?>" title="<?php echo lang('delete'); ?>" onclick="return confirm(<?php echo json_encode(lang('are_you_sure')); ?>);"><i class="fa fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = <?php echo json_encode($this->language); ?>;
</script>
<script src="common/extranal/js/sms/sms.js"></script>
