<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Platform orchestrator (feature flags + release metadata)
|--------------------------------------------------------------------------
| Single place to turn roadmap features on/off per deployment.
|
| Track A — Observability: Health controller exposes platform_release in JSON.
| Track B — Audit: audit_log_enabled writes rows; audit_ui_enabled shows Logs UI.
|
| Order of rollout: run migration 20260401000006 → enable audit_log_enabled
| → verify rows → enable audit_ui_enabled for admins.
*/
$config['platform_release'] = '0.2.0';
$config['audit_log_enabled'] = false;
$config['audit_ui_enabled'] = false;
