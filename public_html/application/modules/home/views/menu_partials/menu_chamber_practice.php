<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$chamber_practice_enabled = function_exists('chamber_practice_enabled') ? chamber_practice_enabled($this) : false;
if (!$chamber_practice_enabled && !$this->ion_auth->in_group('superadmin')) {
    return;
}
$chamber_menu_class = $this->router->fetch_class();
$chamber_menu_method = $this->router->fetch_method();
?>
<?php if ($this->ion_auth->in_group(array('Doctor'))) { ?>
    <li class="nav-header">Chamber practice</li>
    <li class="nav-item"><a class="nav-link text-white <?php echo ($chamber_menu_class === 'doctor_chamber' && $chamber_menu_method === 'dashboard') ? 'active' : ''; ?>" href="<?php echo site_url('doctor_chamber/dashboard'); ?>"><i class="text-secondary nav-icon fas fa-clinic-medical"></i><p>Chamber dashboard</p></a></li>
    <li class="nav-item"><a class="nav-link text-white <?php echo ($chamber_menu_class === 'doctor_chamber' && $chamber_menu_method === 'consultation_room') ? 'active' : ''; ?>" href="<?php echo site_url('doctor_chamber/consultation_room'); ?>"><i class="text-secondary nav-icon fas fa-columns"></i><p>Consultation room</p></a></li>
    <li class="nav-item"><a class="nav-link text-white <?php echo ($chamber_menu_class === 'doctor_chamber' && $chamber_menu_method === 'revenue') ? 'active' : ''; ?>" href="<?php echo site_url('doctor_chamber/revenue'); ?>"><i class="text-secondary nav-icon fas fa-coins"></i><p>Revenue summary</p></a></li>
    <li class="nav-item"><a class="nav-link text-white <?php echo ($chamber_menu_class === 'doctor_chamber' && $chamber_menu_method === 'portal_profile') ? 'active' : ''; ?>" href="<?php echo site_url('doctor_chamber/portal_profile'); ?>"><i class="text-secondary nav-icon fas fa-id-card"></i><p>Portal profile</p></a></li>
    <li class="nav-item"><a class="nav-link text-white <?php echo ($chamber_menu_class === 'doctor_chamber' && $chamber_menu_method === 'favorites') ? 'active' : ''; ?>" href="<?php echo site_url('doctor_chamber/favorites'); ?>"><i class="text-secondary nav-icon fas fa-star"></i><p>Rx favorites</p></a></li>
    <li class="nav-item"><a class="nav-link text-white <?php echo ($chamber_menu_class === 'doctor_chamber' && $chamber_menu_method === 'crm_search') ? 'active' : ''; ?>" href="<?php echo site_url('doctor_chamber/crm_search'); ?>"><i class="text-secondary nav-icon fas fa-search"></i><p>Patient CRM</p></a></li>
    <li class="nav-item"><a class="nav-link text-white <?php echo ($chamber_menu_class === 'doctor_chamber' && $chamber_menu_method === 'schedule_exceptions') ? 'active' : ''; ?>" href="<?php echo site_url('doctor_chamber/schedule_exceptions'); ?>"><i class="text-secondary nav-icon fas fa-calendar-times"></i><p>Schedules / vacation</p></a></li>
    <li class="nav-item"><a class="nav-link text-white <?php echo ($chamber_menu_class === 'doctor_chamber' && $chamber_menu_method === 'my_chambers') ? 'active' : ''; ?>" href="<?php echo site_url('doctor_chamber/my_chambers'); ?>"><i class="text-secondary nav-icon fas fa-map-marker-alt"></i><p>Chamber locations</p></a></li>
    <li class="nav-item"><a class="nav-link text-white <?php echo ($chamber_menu_class === 'doctor_chamber' && $chamber_menu_method === 'template_builder') ? 'active' : ''; ?>" href="<?php echo site_url('doctor_chamber/template_builder'); ?>"><i class="text-secondary nav-icon fas fa-file-alt"></i><p>Rx template</p></a></li>
<?php } ?>
<?php if ($this->ion_auth->in_group(array('Receptionist', 'Nurse', 'admin'))) { ?>
    <li class="nav-header">Chamber desk</li>
    <li class="nav-item"><a class="nav-link text-white <?php echo ($chamber_menu_class === 'assistant_chamber' && $chamber_menu_method === 'desk') ? 'active' : ''; ?>" href="<?php echo site_url('assistant_chamber/desk'); ?>"><i class="text-secondary nav-icon fas fa-user-check"></i><p>Assistant desk</p></a></li>
    <li class="nav-item"><a class="nav-link text-white <?php echo ($chamber_menu_class === 'assistant_chamber' && $chamber_menu_method === 'manual_booking') ? 'active' : ''; ?>" href="<?php echo site_url('assistant_chamber/manual_booking'); ?>"><i class="text-secondary nav-icon fas fa-phone"></i><p>Manual booking</p></a></li>
    <li class="nav-item"><a class="nav-link text-white <?php echo ($chamber_menu_class === 'assistant_chamber' && $chamber_menu_method === 'bulk_sms') ? 'active' : ''; ?>" href="<?php echo site_url('assistant_chamber/bulk_sms'); ?>"><i class="text-secondary nav-icon fas fa-sms"></i><p>Bulk SMS</p></a></li>
<?php } ?>
<?php if ($this->ion_auth->in_group(array('Patient'))) { ?>
    <li class="nav-header">Chamber records</li>
    <li class="nav-item"><a class="nav-link text-white <?php echo ($chamber_menu_class === 'patient_chamber' && $chamber_menu_method === 'my_prescriptions') ? 'active' : ''; ?>" href="<?php echo site_url('patient_chamber/my_prescriptions'); ?>"><i class="text-secondary nav-icon fas fa-file-pdf"></i><p>My prescriptions</p></a></li>
    <li class="nav-item"><a class="nav-link text-white <?php echo ($chamber_menu_class === 'patient_chamber' && $chamber_menu_method === 'lab_vault') ? 'active' : ''; ?>" href="<?php echo site_url('patient_chamber/lab_vault'); ?>"><i class="text-secondary nav-icon fas fa-flask"></i><p>Lab reports</p></a></li>
    <li class="nav-item"><a class="nav-link text-white <?php echo ($chamber_menu_class === 'patient_chamber' && $chamber_menu_method === 'reminders') ? 'active' : ''; ?>" href="<?php echo site_url('patient_chamber/reminders'); ?>"><i class="text-secondary nav-icon fas fa-bell"></i><p>Medicine reminders</p></a></li>
<?php } ?>
<?php if ($this->ion_auth->in_group('superadmin')) { ?>
    <li class="nav-header">Chamber SaaS</li>
    <li class="nav-item"><a class="nav-link text-white <?php echo ($chamber_menu_class === 'saas_platform' && $chamber_menu_method === 'index') ? 'active' : ''; ?>" href="<?php echo site_url('saas_platform/index'); ?>"><i class="text-secondary nav-icon fas fa-hand-holding-usd"></i><p>Doctor SaaS billing</p></a></li>
    <li class="nav-item"><a class="nav-link text-white <?php echo ($chamber_menu_class === 'saas_platform' && $chamber_menu_method === 'sms_credits') ? 'active' : ''; ?>" href="<?php echo site_url('saas_platform/sms_credits'); ?>"><i class="text-secondary nav-icon fas fa-comment-dollar"></i><p>SMS credits ledger</p></a></li>
    <li class="nav-item"><a class="nav-link text-white <?php echo ($chamber_menu_class === 'saas_platform' && $chamber_menu_method === 'analytics') ? 'active' : ''; ?>" href="<?php echo site_url('saas_platform/analytics'); ?>"><i class="text-secondary nav-icon fas fa-chart-bar"></i><p>Chamber usage analytics</p></a></li>
<?php } ?>
