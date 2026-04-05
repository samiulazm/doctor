<div class="content-wrapper audit-orchestrator">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row my-2 pl-1 align-items-center">
                <div class="col-lg-7">
                    <h1 class="font-weight-bold mb-1 audit-orchestrator-title">
                        <i class="fas fa-project-diagram mr-2 text-primary"></i><?php echo lang('audit_trail'); ?>
                    </h1>
                    <p class="text-muted mb-0 small"><?php echo lang('audit_orchestrator_subtitle'); ?></p>
                </div>
                <div class="col-lg-5">
                    <ol class="breadcrumb float-lg-right mb-0 bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                        <li class="breadcrumb-item active"><?php echo lang('audit_trail'); ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content pt-0">
        <div class="container-fluid">
            <div class="audit-orchestrator-hero mb-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 audit-hero-top">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <span class="badge badge-pill badge-primary px-3 py-2 audit-orchestrator-pill"><?php echo lang('audit_pipeline'); ?></span>
                        <span class="audit-live-pill small text-muted"><i class="fas fa-broadcast-tower text-success mr-1"></i><?php echo lang('audit_live_feed'); ?></span>
                    </div>
                    <div class="audit-hero-stats d-flex flex-wrap">
                        <?php if (!empty($audit_table_ready) && $audit_total_count !== null) { ?>
                            <div class="audit-stat-tile">
                                <span class="audit-stat-label"><?php echo lang('audit_stat_events'); ?></span>
                                <span class="audit-stat-value" id="audit-total-count"><?php echo number_format((int) $audit_total_count); ?></span>
                            </div>
                        <?php } else { ?>
                            <div class="audit-stat-tile audit-stat-tile-muted">
                                <span class="audit-stat-label"><?php echo lang('audit_stat_events'); ?></span>
                                <span class="audit-stat-value">—</span>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <ol class="audit-pipeline-stepper list-unstyled mb-3" aria-label="<?php echo lang('audit_pipeline'); ?>">
                    <li class="audit-pipeline-step">
                        <span class="audit-step-num">1</span>
                        <span class="audit-step-body">
                            <span class="audit-step-title"><?php echo lang('audit_step_collect'); ?></span>
                            <span class="audit-step-desc small text-muted"><?php echo lang('audit_step_collect_desc'); ?></span>
                        </span>
                    </li>
                    <li class="audit-pipeline-connector" aria-hidden="true"></li>
                    <li class="audit-pipeline-step">
                        <span class="audit-step-num">2</span>
                        <span class="audit-step-body">
                            <span class="audit-step-title"><?php echo lang('audit_step_store'); ?></span>
                            <span class="audit-step-desc small text-muted"><?php echo lang('audit_step_store_desc'); ?></span>
                        </span>
                    </li>
                    <li class="audit-pipeline-connector" aria-hidden="true"></li>
                    <li class="audit-pipeline-step audit-pipeline-step-current">
                        <span class="audit-step-num">3</span>
                        <span class="audit-step-body">
                            <span class="audit-step-title"><?php echo lang('audit_step_review'); ?></span>
                            <span class="audit-step-desc small text-muted"><?php echo lang('audit_step_review_desc'); ?></span>
                        </span>
                    </li>
                </ol>

                <div class="row align-items-stretch g-3">
                    <div class="col-lg-8">
                        <p class="mb-0 small text-secondary audit-scope-hint">
                            <i class="fas fa-info-circle mr-1 text-primary"></i>
                            <?php echo isset($audit_scope) && $audit_scope === 'all' ? lang('audit_scope_all_hint') : lang('audit_scope_hospital_hint'); ?>
                        </p>
                    </div>
                    <div class="col-lg-4">
                        <div class="audit-meta-panel h-100 d-flex flex-column justify-content-center">
                            <div class="d-flex justify-content-between align-items-center mb-2 audit-meta-row">
                                <span class="text-muted small text-uppercase"><?php echo lang('release'); ?></span>
                                <span class="font-weight-bold font-monospace text-truncate ml-2" style="max-width:55%"><?php echo html_escape(is_string($platform_release) && $platform_release !== '' ? $platform_release : '—'); ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2 audit-meta-row">
                                <span class="text-muted small text-uppercase"><?php echo lang('scope'); ?></span>
                                <?php if (isset($audit_scope) && $audit_scope === 'all') { ?>
                                    <span class="badge badge-dark"><?php echo lang('audit_scope_all'); ?></span>
                                <?php } else { ?>
                                    <span class="badge badge-secondary"><?php echo lang('audit_scope_hospital'); ?></span>
                                <?php } ?>
                            </div>
                            <div class="d-flex justify-content-between align-items-center audit-meta-row">
                                <span class="text-muted small text-uppercase"><?php echo lang('logging'); ?></span>
                                <?php if (!empty($audit_log_enabled)) { ?>
                                    <span class="badge badge-success"><i class="fas fa-circle mr-1 audit-pulse-dot"></i><?php echo lang('active'); ?></span>
                                <?php } else { ?>
                                    <span class="badge badge-light border"><i class="fas fa-pause mr-1"></i><?php echo lang('inactive'); ?></span>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (empty($audit_table_ready)) { ?>
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-start mb-3" role="alert">
                    <i class="fas fa-exclamation-triangle mt-1 mr-3 fa-lg"></i>
                    <div>
                        <strong><?php echo lang('audit_trail'); ?></strong>
                        <div class="small mb-0"><?php echo lang('audit_table_missing_hint'); ?></div>
                    </div>
                </div>
            <?php } ?>

            <div class="card card-outline card-primary audit-table-card shadow-sm">
                <div class="card-header border-0 d-flex flex-wrap align-items-center justify-content-between audit-card-head">
                    <h3 class="card-title mb-0 font-weight-bold d-flex align-items-center">
                        <span class="audit-card-icon-wrap mr-2"><i class="fas fa-table text-primary"></i></span><?php echo lang('audit_events'); ?>
                    </h3>
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-2 mt-md-0 audit-card-actions">
                        <button type="button" class="btn btn-sm btn-outline-secondary audit-reload-btn" id="audit-reload-table" title="<?php echo lang('audit_refresh_grid'); ?>">
                            <i class="fas fa-sync-alt mr-1"></i><?php echo lang('audit_refresh_grid'); ?>
                        </button>
                        <div class="custom_buttons"></div>
                    </div>
                </div>
                <div class="card-body pt-2 px-2 px-md-3">
                    <div class="table-responsive audit-table-wrap">
                        <table class="table table-sm table-hover table-striped align-middle mb-0 audit-data-table" id="audit-table" style="width:100%">
                            <thead class="thead-light">
                                <tr>
                                    <th><?php echo lang('date-time'); ?></th>
                                    <th><?php echo lang('action'); ?></th>
                                    <th><?php echo lang('type'); ?></th>
                                    <th><?php echo lang('id'); ?></th>
                                    <th><?php echo lang('user'); ?> ID</th>
                                    <th><?php echo lang('ip_address'); ?></th>
                                    <th><?php echo lang('details'); ?></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = "<?php echo $this->language; ?>";
</script>
<script src="common/extranal/js/audit.js"></script>
