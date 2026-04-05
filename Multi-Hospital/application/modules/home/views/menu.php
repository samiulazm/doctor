<?php
$sidebar_section_headers = array();
$render_sidebar_section = function ($key, $label) use (&$sidebar_section_headers) {
    if (isset($sidebar_section_headers[$key])) {
        return;
    }

    $sidebar_section_headers[$key] = true;
    echo '<li class="nav-header">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</li>';
};
?>
<!-- Sidebar menu (partials in menu_partials/) -->
<?php $this->load->view('menu_partials/menu_overview_dashboard', array('render_sidebar_section' => $render_sidebar_section)); ?>
<?php $this->load->view('menu_partials/menu_emergency_quick', array('render_sidebar_section' => $render_sidebar_section)); ?>
<?php $this->load->view('menu_partials/menu_admin_departments_doctors'); ?>

<?php $this->load->view('menu_partials/menu_patients_opd', array('render_sidebar_section' => $render_sidebar_section)); ?>

<?php $this->load->view('menu_partials/menu_ai_tools', array('render_sidebar_section' => $render_sidebar_section)); ?>

<?php $this->load->view('menu_partials/menu_prescription_all'); ?>

<?php $this->load->view('menu_partials/menu_schedule_appointments'); ?>

<?php $this->load->view('menu_partials/menu_live_meetings'); ?>

<?php $this->load->view('menu_partials/menu_patient_portal'); ?>



<?php $this->load->view('menu_partials/menu_staff_hr_compact', array('render_sidebar_section' => $render_sidebar_section)); ?>

<?php $this->load->view('menu_partials/menu_billing_finance_admin', array('render_sidebar_section' => $render_sidebar_section)); ?>
<?php $this->load->view('menu_partials/menu_reports_analytics', array('render_sidebar_section' => $render_sidebar_section)); ?>

<?php $this->load->view('menu_partials/menu_receptionist_accountant_extras'); ?>

<?php $this->load->view('menu_partials/menu_lab_diagnostics', array('render_sidebar_section' => $render_sidebar_section)); ?>
<?php $this->load->view('menu_partials/menu_beds_admin', array('render_sidebar_section' => $render_sidebar_section)); ?>
<?php $this->load->view('menu_partials/menu_ambulance', array('render_sidebar_section' => $render_sidebar_section)); ?>

<?php $this->load->view('menu_partials/menu_pharmacy_sales', array('render_sidebar_section' => $render_sidebar_section)); ?>
<?php $this->load->view('menu_partials/menu_medicine_admin', array('render_sidebar_section' => $render_sidebar_section)); ?>
<?php $this->load->view('menu_partials/menu_inventory_admin', array('render_sidebar_section' => $render_sidebar_section)); ?>

<?php $this->load->view('menu_partials/menu_pharmacist_medicine'); ?>
<?php $this->load->view('menu_partials/menu_staff_inventory_links'); ?>
<?php $this->load->view('menu_partials/menu_hr_admin_full'); ?>

<?php $this->load->view('menu_partials/menu_communication', array('render_sidebar_section' => $render_sidebar_section)); ?>

<?php $this->load->view('menu_partials/menu_hospital_website'); ?>
<?php $this->load->view('menu_partials/menu_admin_donor'); ?>
<?php $this->load->view('menu_partials/menu_hospital_system_settings', array('render_sidebar_section' => $render_sidebar_section)); ?>




<?php $this->load->view('menu_partials/menu_beds_secondary_nurse_receptionist', array('render_sidebar_section' => $render_sidebar_section)); ?>
<?php $this->load->view('menu_partials/menu_accountant_finance_secondary', array('render_sidebar_section' => $render_sidebar_section)); ?>

<?php $this->load->view('menu_partials/menu_patient_im_doctor_files'); ?>


<?php $this->load->view('menu_partials/menu_superadmin_system', array('render_sidebar_section' => $render_sidebar_section)); ?>
<?php $this->load->view('menu_partials/menu_superadmin_email'); ?>
<?php $this->load->view('menu_partials/menu_superadmin_settings'); ?>
<?php $this->load->view('menu_partials/menu_nurse_donor'); ?>
<?php $this->load->view('menu_partials/menu_account_footer', array('render_sidebar_section' => $render_sidebar_section)); ?>