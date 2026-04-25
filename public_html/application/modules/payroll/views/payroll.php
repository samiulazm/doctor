<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI = get_instance();
?>
<link rel="stylesheet" href="<?php echo asset_url('application/assets/css/appointment-page.css'); ?>">

<div class="content-wrapper bg-light appointment-page">
    <?php
    $CI->load->view('partials/page_header', array(
        'title' => lang('payroll'),
        'icon' => 'fas fa-money-check-alt text-primary mr-2',
        'breadcrumbs' => array(
            array('label' => lang('home'), 'url' => 'home'),
            array('label' => lang('payroll'), 'url' => null),
        ),
    ));
    ?>

    <section class="content py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-sm border-0 appointment-list-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h3 class="card-title h6 mb-0 text-muted text-uppercase"><?php echo lang('All the payroll informations'); ?></h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="text-uppercase font-weight-bold text-muted small"><?php echo lang('month'); ?></label>
                                        <select class="form-control form-control-lg shadow-sm js-example-basic-single" id="payroll_month">
                                            <?php
                                            foreach ($months as $month) {
                                                if ($month == date('F')) {
                                            ?>
                                                    <option value="<?php echo html_escape($month); ?>" selected><?php echo html_escape($month); ?></option>
                                                <?php
                                                    break;
                                                } else {
                                                ?>
                                                    <option value="<?php echo html_escape($month); ?>"><?php echo html_escape($month); ?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="text-uppercase font-weight-bold text-muted small"><?php echo lang('year'); ?></label>
                                        <select class="form-control form-control-lg shadow-sm js-example-basic-single" id="payroll_year">
                                            <?php foreach ($years as $year) { ?>
                                                <option value="<?php echo (int) $year; ?>" <?php if ((int) $year === (int) date('Y')) { ?>selected<?php } ?>><?php echo (int) $year; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <button type="button" class="btn btn-success generatePayroll">
                                    <i class="fas fa-paper-plane mr-2"></i> <?php echo lang('generate'); ?>
                                </button>
                            </div>

                            <div class="custom_buttons mb-3"></div>
                            <div class="payroll_table table-responsive">
                                <table class="table table-hover table-bordered align-middle text-sm datatables mb-0" id="salary-sample" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-uppercase"><?php echo lang('staff'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('salary'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('paid_on'); ?></th>
                                            <th class="text-uppercase"><?php echo lang('status'); ?></th>
                                            <th class="text-uppercase no-print"><?php echo lang('options'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($employees)) {
                                            for ($i = 0; $i < count($employees); $i++) {
                                        ?>
                                            <tr>
                                                <td><?php echo html_escape($employees[$i][0]); ?></td>
                                                <td><?php echo html_escape($employees[$i][1]); ?></td>
                                                <td><?php echo html_escape($employees[$i][2]); ?></td>
                                                <td><?php echo $employees[$i][3]; ?></td>
                                                <td class="no-print"><?php echo $employees[$i][4]; ?></td>
                                            </tr>
                                        <?php
                                            }
                                        }
                                        ?>
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
<script src="common/extranal/js/payroll/payroll.js"></script>
