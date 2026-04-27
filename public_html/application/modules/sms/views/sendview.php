<?php
$CI = get_instance();
if (!isset($templatename)) {
    $templatename = null;
}
?>
<div class="content-wrapper bg-light">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('send_sms'),
        'icon' => 'fas fa-sms text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('sms'), 'url' => 'sms'),
            array('label' => lang('send_sms'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase">
                                <?php echo lang('Send sms to the recipients'); ?>
                            </h3>
                            <div class="btn-group flex-wrap" role="group">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.href='sms/sent'">
                                    <i class="fas fa-paper-plane mr-1"></i><?php echo lang('sent_messages'); ?>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.href='sms/manualSMSTemplate'">
                                    <i class="fas fa-file-alt mr-1"></i><?php echo lang('template'); ?>
                                </button>
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#smsAddTemplateModal">
                                    <i class="fas fa-plus mr-1"></i><?php echo lang('add'); ?> <?php echo lang('template'); ?>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-4 p-lg-5">
                            <?php echo $this->session->flashdata('feedback'); ?>

                            <form role="form" name="myform" id="myform" action="sms/send" method="post">
                                <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">

                                <label class="font-weight-bold d-block mb-3"><?php echo lang('send_sms_to'); ?></label>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="radio" id="radio_all_patient" value="allpatient" checked>
                                    <label class="form-check-label" for="radio_all_patient"><?php echo lang('all_patient'); ?></label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="radio" id="radio_all_doctor" value="alldoctor">
                                    <label class="form-check-label" for="radio_all_doctor"><?php echo lang('all_doctor'); ?></label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="radio" id="radio_blood" value="bloodgroupwise">
                                    <label class="form-check-label" for="radio_blood"><?php echo lang('donor'); ?> (<?php echo lang('blood_group'); ?>)</label>
                                </div>
                                <div class="form-group pos_client pl-4 mb-3">
                                    <label for="bloodgroup" class="small text-muted mb-1"><?php echo lang('select_blood_group'); ?></label>
                                    <select class="form-control form-control-lg" id="bloodgroup" name="bloodgroup">
                                        <?php foreach ($groups as $group) { ?>
                                            <option value="<?php echo htmlspecialchars($group->group); ?>"><?php echo htmlspecialchars($group->group); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="radio" id="radio_single_patient" value="single_patient">
                                    <label class="form-check-label" for="radio_single_patient"><?php echo lang('single_patient'); ?></label>
                                </div>
                                <div class="form-group single_patient pl-4 mb-4">
                                    <label for="patientchoose" class="small text-muted mb-1"><?php echo lang('select_patient'); ?></label>
                                    <select class="form-control form-control-lg" id="patientchoose" name="patient"></select>
                                </div>

                                <div class="form-group">
                                    <label for="selUser5" class="font-weight-bold"><?php echo lang('select_template'); ?></label>
                                    <select class="form-control form-control-lg" id="selUser5" name="templatess"></select>
                                </div>

                                <div class="form-group">
                                    <label class="control-label d-block font-weight-bold" for="editor1"><?php echo lang('message'); ?> <span class="text-danger">*</span></label>
                                    <div class="d-flex flex-wrap mb-2 shortcode-btns">
                                        <?php
                                        $count = 0;
                                        foreach ($shortcode as $shortcodes) {
                                            $count += 1;
                                        ?>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mb-1 mr-1" name="myBtn" value="<?php echo htmlspecialchars($shortcodes->name); ?>" onclick="addtext(this); return false;"><?php echo htmlspecialchars($shortcodes->name); ?></button>
                                        <?php
                                            if ($count % 7 === 0) {
                                                echo '<br class="d-none d-md-block">';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <textarea class="form-control" id="editor1" name="message" rows="8" required></textarea>
                                </div>
                                <input type="hidden" name="id" value="">

                                <div class="d-flex flex-wrap justify-content-end gap-2 pt-2">
                                    <a href="sms" class="btn btn-outline-secondary"><?php echo lang('cancel'); ?></a>
                                    <button type="submit" name="submit" class="btn btn-primary px-4">
                                        <i class="fa fa-location-arrow mr-1"></i> <?php echo lang('send_sms'); ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="smsAddTemplateModal" role="dialog" aria-labelledby="smsAddTemplateLabel" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold" id="smsAddTemplateLabel"><?php echo lang('add_new'); ?> <?php echo lang('template'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo lang('close'); ?>"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <?php echo validation_errors(); ?>
                <form role="form" name="myform1" id="myform1" action="sms/addNewManualTemplate" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $CI->security->get_csrf_token_name(); ?>" value="<?php echo $CI->security->get_csrf_hash(); ?>">

                    <div class="form-group">
                        <label for="tpl_name"><?php echo lang('templatename'); ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="tpl_name" name="name" value="<?php
                        if (!empty($templatename) && isset($templatename->name)) {
                            echo htmlspecialchars($templatename->name);
                        } elseif (!empty($setval)) {
                            echo htmlspecialchars((string) set_value('name'));
                        }
                        ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="editor2"><?php echo lang('message'); ?> <?php echo lang('template'); ?> <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap mb-2">
                            <?php
                            $count1 = 0;
                            foreach ($shortcode as $shortcodes) {
                                $count1 += 1;
                            ?>
                                <button type="button" class="btn btn-sm btn-outline-secondary mb-1 mr-1" name="myBtn" value="<?php echo htmlspecialchars($shortcodes->name); ?>" onclick="addtext1(this); return false;"><?php echo htmlspecialchars($shortcodes->name); ?></button>
                            <?php
                                if ($count1 % 7 === 0) {
                                    echo '<br class="d-none d-md-block">';
                                }
                            }
                            ?>
                        </div>
                        <textarea class="form-control" id="editor2" name="message" rows="6" required><?php
                        if (!empty($templatename) && isset($templatename->message)) {
                            echo htmlspecialchars($templatename->message);
                        } elseif (!empty($setval)) {
                            echo set_value('message');
                        }
                        ?></textarea>
                    </div>
                    <input type="hidden" name="id" value="<?php echo (!empty($templatename) && !empty($templatename->id)) ? (int) $templatename->id : ''; ?>">
                    <input type="hidden" name="type" value="sms">
                    <div class="d-flex justify-content-end">
                        <button type="submit" name="submit" class="btn btn-primary"><?php echo lang('submit'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var select_patient = <?php echo json_encode(lang('select_patient')); ?>;
    var select_template = <?php echo json_encode(lang('select_template')); ?>;
</script>
<script src="common/extranal/js/sms/sendview.js"></script>
