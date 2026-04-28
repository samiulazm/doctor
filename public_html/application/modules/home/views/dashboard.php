<!DOCTYPE html>
<html lang="en" <?php
                if (!$this->ion_auth->in_group(array('superadmin'))) {

                  $this->db->where('hospital_id', $this->hospital_id);
                  $settings_lang = $this->db->get('settings')->row()->language;
                  if ($this->language == 'arabic') {
                ?> dir="rtl" <?php } else { ?> dir="ltr" <?php
                                                        }
                                                      } else {
                                                        $this->db->where('hospital_id', 'superadmin');
                                                        $settings_lang = $this->db->get('settings')->row()->language;
                                                        if ($this->language == 'arabic') {
                                                          ?> dir="rtl" <?php } else { ?> dir="ltr" <?php
                                                                                                  }
                                                                                                }
                                                                                                    ?>>

<head>
  <base href="<?php echo base_url(); ?>"> 
  <script>window.CI_BASE_URL = <?php echo json_encode(rtrim(base_url(), '/') . '/'); ?>;</script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php
  $class_name = $this->router->fetch_class();
  $class_name_lang = lang($class_name);
  if (empty($class_name_lang)) {
    $class_name_lang = $class_name;
  }
  ?>

  <title> <?php echo $class_name_lang; ?> | 
    <?php
    if ($this->ion_auth->in_group(array('superadmin'))) {
      $this->db->where('hospital_id', 'superadmin');
    } else {
      $this->db->where('hospital_id', $this->hospital_id);
    }
    ?>
    <?php
    $settings = $this->db->get('settings')->row();
    if ($settings) {
        $settings->currency = defined('HOSPITAL_CURRENCY_SYMBOL') ? HOSPITAL_CURRENCY_SYMBOL : '৳';
    }
    echo $settings->system_vendor;
    ?>
  </title>

  <!-- <link rel="stylesheet" href="common/css/bootstrap-select.min.css"> -->

  <!-- Google Fonts -->

  <!-- design the sidebar with more professional css  -->



  <!-- AdminLTE 4 (includes Bootstrap 5.3) -->
  <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="adminlte/dist/css/bs4-compat.css">

  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Outfit:wght@400;500;600;700;800&display=swap">
  <link rel="stylesheet" href="adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="adminlte/plugins/jqvmap/jqvmap.min.css">
  <link rel="stylesheet" href="adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="<?php echo asset_url('application/assets/css/app-design-tokens.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('application/assets/css/enhanced-sidebar-styles.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('application/assets/css/enhanced-components-styles.css'); ?>">
  <link rel="stylesheet" href="adminlte/plugins/daterangepicker/daterangepicker.css">
  <link rel="stylesheet" href="adminlte/plugins/summernote/summernote-bs4.min.css">

  <!-- DataTables BS5 -->
  <link rel="stylesheet" href="adminlte/plugins/datatables-bs4/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="adminlte/plugins/datatables-responsive/css/responsive.bootstrap5.min.css">
  <link rel="stylesheet" href="adminlte/plugins/datatables-buttons/css/buttons.bootstrap5.min.css">

  <link rel="stylesheet" href="adminlte/dist/css/changes.css">
  <link rel="stylesheet" href="common/css/chamber-practice.css">

  <link rel="stylesheet" href="adminlte/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
  <link rel="stylesheet" href="adminlte/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

  <?php
  $_ap_need_fullcalendar = (
      ($this->router->fetch_class() === 'home' && $this->router->fetch_method() === 'index')
      || ($this->router->fetch_class() === 'appointment' && $this->router->fetch_method() === 'calendar')
  );
  ?>
  <?php if (!empty($_ap_need_fullcalendar)) : ?>
  <link rel="stylesheet" href="adminlte/plugins/fullcalendar/main.css">
  <?php endif; ?>
  <link rel="stylesheet" href="adminlte/plugins/flag-icon-css/css/flag-icon.min.css">

  <link rel="stylesheet" href="common/assets/bootstrap-datepicker/css/bootstrap-datepicker.css" />
  <link rel="stylesheet" type="text/css" href="common/assets/bootstrap-daterangepicker/daterangepicker-bs3.css" />
  <link rel="stylesheet" type="text/css" href="common/assets/bootstrap-datetimepicker/css/datetimepicker.css" />
  <link rel="stylesheet" href="common/assets/bootstrap-timepicker/compiled/timepicker.css" />
  <link rel="stylesheet" type="text/css" href="common/assets/jquery-multi-select/css/multi-select.css" />
  <link rel="stylesheet" type="text/css" href="common/css/lightbox.css" />

  <!-- Toastr -->
  <link rel="stylesheet" href="adminlte/plugins/toastr/toastr.min.css">

  <!-- dropzonejs -->
  <link rel="stylesheet" href="adminlte/plugins/dropzone/min/dropzone.min.css">





  <?php

  if ($this->language == 'arabic') { ?>
    <link rel="stylesheet" href="adminlte/dist/css/adminlte.rtl.min.css">
  <?php } ?>

  <!-- <link rel="stylesheet" href="common/css/bootstrap-select-country.min.css"> -->


</head>

<body class="layout-fixed sidebar-expand-lg sidebar-mini <?php
                                          if ($this->ion_auth->user()->row()->sidebar != 1) {
                                            echo 'sidebar-collapse';
                                          }
                                          ?>" <?php if ($this->session->userdata('darkMode') == 1) { echo 'data-bs-theme="dark"'; } ?>>



  <div id="loader" class="loader"></div>



  <a class="ap-skip-link" href="#app-main-content"><?php echo lang('skip_to_main_content') ?: 'Skip to main content'; ?></a>

  <div class="app-wrapper">

    <!-- Navbar -->
    <nav class="app-header navbar navbar-expand navbar-light py-3">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link collapse-server nav-link-enhanced" data-lte-toggle="sidebar" href="#" role="button" title="<?php echo lang('toggle_sidebar'); ?>">
            <i class="fas fa-bars nav-icon"></i>
          </a>
        </li>


        <!-- <li class="nav-item d-none d-sm-inline-block">
          <a href="#" class="nav-link">Contact</a>
        </li> -->
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- Search Section -->
        <li class="nav-item search-item">
          <div class="form-inline">
            <div class="input-group search-container" data-lte-toggle="sidebar-search">
              <input class="form-control form-control-sidebar search-input" type="search" placeholder="<?php echo lang('search'); ?>" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-sidebar search-btn" title="<?php echo lang('search'); ?>">
                  <i class="fas fa-search search-icon"></i>
                </button>
              </div>
            </div>
          </div>
        </li>

        <!-- Timezone Section -->
        <?php if (!$this->ion_auth->in_group(array('superadmin'))) { ?>
        <li class="nav-item">
          <a href="#" id="timezone-display" class="nav-link nav-link-enhanced timezone-link" data-bs-toggle="modal" data-bs-target="#timezoneModal" title="<?php echo lang('timezone'); ?>">
            <i class="fas fa-clock nav-icon"></i>
            <span id="current-timezone" class="timezone-text"><?php echo $settings->timezone ?? 'UTC'; ?></span>
            <i class="fas fa-edit timezone-edit-icon"></i>
          </a>
        </li>
        <?php } ?>

        <?php $this->load->view('available'); ?>

        <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
          <li class="nav-item dropdown d-none d-md-block">
            <a class="nav-link nav-link-enhanced quick-actions-link" data-bs-toggle="dropdown" href="#" title="<?php echo lang('quick_actions'); ?>">
              <i class="fas fa-bolt nav-icon"></i>
              <span class="ap-status ap-status-info navbar-badge quick-actions-badge"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

              <?php if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist'))) { ?>
                <?php if (in_array('finance', $this->modules)) { ?>
                  <a href="finance/addPaymentView" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                      <div class="media-body">
                        <h3 class="dropdown-item-title">
                          <?php echo lang('add_payment'); ?>
                          <span class="float-right text-sm text-danger"><i class="fas fa-money-check"></i></span>
                        </h3>
                      </div>
                    </div>
                    <!-- Message End -->
                  </a>
                  <div class="dropdown-divider"></div>
                <?php } ?>
              <?php } ?>



              <?php if ($this->ion_auth->in_group(array('admin', 'Nurse', 'Accountant', 'Pharmacist', 'Doctor', 'Laboratorist', 'Receptionist'))) { ?>
                <?php if (in_array('appointment', $this->modules)) { ?>
                  <a href="appointment/addNewView" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                      <div class="media-body">
                        <h3 class="dropdown-item-title">
                          <?php echo lang('add_appointment'); ?>
                          <span class="float-right text-sm text-danger"><i class="fas fa-calendar-check"></i></span>
                        </h3>
                      </div>
                    </div>
                    <!-- Message End -->
                  </a>
                  <div class="dropdown-divider"></div>
                <?php } ?>
              <?php } ?>



              <?php if ($this->ion_auth->in_group(array('admin', 'Nurse', 'Accountant', 'Pharmacist', 'Doctor', 'Laboratorist', 'Receptionist'))) { ?>
                <?php if (in_array('patient', $this->modules)) { ?>
                  <a href="patient/addNewView" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                      <div class="media-body">
                        <h3 class="dropdown-item-title">
                          <?php echo lang('add_patient'); ?>
                          <span class="float-right text-sm text-danger"><i class="fas fa-user"></i></span>
                        </h3>
                      </div>
                    </div>
                    <!-- Message End -->
                  </a>
                  <div class="dropdown-divider"></div>
                <?php } ?>
              <?php } ?>



              <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                <?php if (in_array('doctor', $this->modules)) { ?>
                  <a href="doctor/addNewView" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                      <div class="media-body">
                        <h3 class="dropdown-item-title">
                          <?php echo lang('add_doctor'); ?>
                          <span class="float-right text-sm text-danger"><i class="fas fa-user"></i></span>
                        </h3>
                      </div>
                    </div>
                    <!-- Message End -->
                  </a>
                  <div class="dropdown-divider"></div>
                <?php } ?>
              <?php } ?>



              <?php if ($this->ion_auth->in_group(array('Doctor'))) { ?>
                <?php if (in_array('prescription', $this->modules)) { ?>
                  <a href="prescription/addPrescriptionView" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                      <div class="media-body">
                        <h3 class="dropdown-item-title">
                          <?php echo lang('add_prescription'); ?>
                          <span class="float-right text-sm text-danger"><i class="fas fa-user"></i></span>
                        </h3>
                      </div>
                    </div>
                    <!-- Message End -->
                  </a>
                  <div class="dropdown-divider"></div>
                <?php } ?>
              <?php } ?>

              <?php if ($this->ion_auth->in_group(array('admin', 'Nurse', 'Accountant', 'Pharmacist', 'Doctor', 'Laboratorist', 'Receptionist'))) { ?>
                <?php if (in_array('lab', $this->modules)) { ?>
                  <a href="lab" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                      <div class="media-body">
                        <h3 class="dropdown-item-title">
                          <?php echo lang('lab'); ?> <?php echo lang('reports'); ?>
                          <span class="float-right text-sm text-danger"><i class="fas fa-flask"></i></span>
                        </h3>
                      </div>
                    </div>
                    <!-- Message End -->
                  </a>
                  <div class="dropdown-divider"></div>
                <?php } ?>
              <?php } ?>

              <?php if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist'))) { ?>
                <?php if (in_array('finance', $this->modules)) { ?>
                  <a href="finance/dueCollection" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                      <div class="media-body">
                        <h3 class="dropdown-item-title">
                          <?php echo lang('due_collection'); ?>
                          <span class="float-right text-sm text-danger"><i class="fas fa-money-check"></i></span>
                        </h3>
                      </div>
                    </div>
                    <!-- Message End -->
                  </a>
                  <div class="dropdown-divider"></div>
                <?php } ?>
              <?php } ?>




            </div>
          </li>
        <?php } ?>



        <!-- Messages Dropdown Menu -->

        <?php if ($this->ion_auth->in_group(array('admin', 'Nurse', 'Accountant', 'Pharmacist', 'Doctor', 'Laboratorist', 'Receptionist'))) { ?>
          <?php if (in_array('chat', $this->modules)) { ?>
            <li class="nav-item dropdown d-none d-md-block">
              <a class="nav-link nav-link-enhanced chat-link" href="chat" title="<?php echo lang('chat'); ?>">
                <i class="far fa-comments nav-icon"></i>
                <span class="ap-status ap-status-info navbar-badge chat-badge" id="chatCount"></span>
              </a>
            </li>
          <?php } ?>
        <?php } ?>
        <!-- Notifications Dropdown Menu -->





        <?php if ($this->ion_auth->in_group(array('admin', 'Accountant'))) : ?>
          <?php if (in_array('finance', $this->modules)) : ?>
            <li class="nav-item dropdown">
              <a class="nav-link nav-link-enhanced payment-link" data-bs-toggle="dropdown" href="#" title="<?php echo lang('payment'); ?>">
                <i class="fas fa-credit-card nav-icon"></i>
                <?php
                $this->db->where('hospital_id', $this->hospital_id);
                $query = $this->db->get('payment');
                $query = $query->result();
                $payment_number = 0;
                foreach ($query as $payment) {
                  $payment_date = date('y/m/d', $payment->date);
                  if ($payment_date == date('y/m/d')) {
                    $payment_number++;
                  }
                }
                ?>
                <span class="ap-status ap-status-danger navbar-badge payment-badge"><?= $payment_number; ?></span>
              </a>
              <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header"><?= $payment_number; ?>
                  <?php if ($payment_number <= 1) {
                    echo lang('payment_today');
                  } else {
                    echo lang('payments_today');
                  } ?>
                </span>
                <div class="dropdown-divider"></div>
                <a href="finance/payment" class="dropdown-item">
                  <?= lang('see_all_payments'); ?>
                  <span class="float-right text-muted text-sm"><?= ($payment_number > 0) ? 'Available' : 'Not Available'; ?></span>
                </a>
                <!-- Add more notifications or a footer link similar to "See All Notifications" if needed -->
              </div>
            </li>
          <?php endif; ?>
        <?php endif; ?>

        <?php if ($this->ion_auth->in_group(['admin', 'Accountant', 'Doctor', 'Nurse', 'Laboratorist'])) : ?>
          <?php if (in_array('patient', $this->modules)) : ?>
            <li class="nav-item dropdown">
              <a class="nav-link nav-link-enhanced patient-link" data-bs-toggle="dropdown" href="#" title="<?php echo lang('patient'); ?>">
                <i class="fas fa-user-plus nav-icon"></i>
                <?php
                $this->db->where('hospital_id', $this->hospital_id);
                $this->db->where('add_date', date('m/d/y'));
                $query = $this->db->get('patient');
                $query = $query->result();
                $patient_number = count($query);
                ?>
                <span class="ap-status ap-status-warning navbar-badge patient-badge"><?= $patient_number; ?></span>
              </a>
              <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">
                  <?= $patient_number; ?>
                  <?php if ($patient_number <= 1) : ?>
                    <?= lang('patient_registerred_today'); ?>
                  <?php else : ?>
                    <?= lang('patients_registerred_today'); ?>
                  <?php endif; ?>
                </span>
                <div class="dropdown-divider"></div>
                <a href="patient" class="dropdown-item">
                  <?= lang('see_all_patients'); ?>
                  <span class="float-right text-muted text-sm"><?= ($patient_number > 0) ? 'Available' : 'Not Available'; ?></span>
                </a>
                <!-- Add more notifications or a footer link similar to "See All Notifications" if needed -->
              </div>
            </li>
          <?php endif; ?>
        <?php endif; ?>

        <?php


        $languages = $this->db->get('language')->result();

        foreach ($languages as $language) {
          if ($this->language == $language->language) {
            $flagIcon = $language->flag_icon;
          }
        }

        ?>



        <!-- Language Dropdown Menu -->
        <?php if ($this->ion_auth->in_group(array('admin', 'superadmin'))) { ?>
          <li class="nav-item dropdown">
            <a class="nav-link nav-link-enhanced language-link" data-bs-toggle="dropdown" href="#" title="<?php echo lang('language'); ?>">
              <i class="flag-icon flag-icon-<?php echo $flagIcon; ?> language-flag"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right p-0">

              <?php

              foreach ($languages as $language) {

              ?>
                <a href="settings/changeLanguageFlag?lang=<?php echo $language->language; ?>" class="dropdown-item <?php if ($this->language ==  $language->language) {
                                                                                                                      echo 'active';
                                                                                                                    } ?>">
                  <i class="flag-icon flag-icon-<?php echo $language->flag_icon; ?> mr-2"></i> <?php echo $language->language; ?>
                </a>
              <?php } ?>


            </div>
          </li>
        <?php } ?>

        <?php if ($this->ion_auth->in_group(array('Patient', 'Doctor'))) { ?>
          <li class="nav-item dropdown">
            <a class="nav-link" data-bs-toggle="dropdown" href="">
              <i class="flag-icon flag-icon-<?php echo $flagIcon; ?>"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right p-0">


              <?php
              $languages = $this->db->get('language')->result();
              foreach ($languages as $language) {

              ?>
                <a href="profile/changeLanguageFlag?lang=<?php echo $language->language; ?>" class="dropdown-item <?php if ($this->language == $language->language) {
                                                                                                                    echo 'active';
                                                                                                                  } ?>">
                  <i class="flag-icon flag-icon-<?php echo $language->flag_icon; ?> mr-2"></i> عربى
                </a>


              <?php } ?>


            </div>
          </li>
        <?php } ?>

        <li class="nav-item d-none d-md-block">
          <a class="nav-link nav-link-enhanced fullscreen-link" title="<?php echo lang('full_screen'); ?>" data-lte-toggle="fullscreen" role="button">
            <i class="fas fa-expand-arrows-alt nav-icon"></i>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link nav-link-enhanced logout-link" title="<?php echo lang('log_out'); ?>" href="auth/logout" role="button">
            <i class="fas fa-sign-out-alt nav-icon"></i>
          </a>
        </li>
        <!-- <li class="nav-item d-flex align-items-center">
          <div class="custom-control custom-switch d-flex align-items-center">
            <input type="checkbox" class="custom-control-input me-2" id="darkModeToggle" <?php if ($this->session->userdata('darkMode') == 1) echo 'checked'; ?>>
            <label class="custom-control-label d-flex align-items-center" for="darkModeToggle">
              <i class="fas fa-moon text-secondary"></i>
            </label>
          </div>
        </li> -->



      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
      <!--begin::Sidebar Brand-->
      <div class="sidebar-brand">
        <a href="home" class="brand-link">
          <?php if (!$this->ion_auth->in_group(array('superadmin'))) { ?>
            <img src="<?php echo $settings->logo_title; ?>" alt="Practice logo" class="brand-image opacity-75 shadow" style="width: 33px; height: 33px; object-fit: cover;">
            <span class="brand-text fw-light"><?php echo $settings->title; ?></span>
          <?php } else { ?>
            <img src="<?php echo $settings->logo_title; ?>" alt="Practice logo" class="brand-image opacity-75 shadow" style="width: 33px; height: 33px; object-fit: cover;">
            <span class="brand-text fw-light"><?php echo $settings->title; ?></span>
          <?php } ?>
        </a>
      </div>
      <!--end::Sidebar Brand-->

      <!--begin::Sidebar Wrapper-->
      <div class="sidebar-wrapper">
        <nav class="mt-2">
          <!--begin::Sidebar Menu-->
          <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation" data-accordion="false">

            <?php $this->load->view('menu'); ?>

          </ul>
          <!--end::Sidebar Menu-->
        </nav>
      </div>
      <!--end::Sidebar Wrapper-->
    </aside>

    <!-- Main Content Wrapper -->
    <main class="app-main" id="app-main-content" tabindex="-1">
  <!-- loader styles defined in <head> -->

  <!-- Enhanced Timezone Selector Styles -->
  <style>
    /* Enhanced Timezone - Consistent with Other Elements */
    .timezone-link {
      display: flex;
      align-items: center;
      gap: 6px;
      text-decoration: none !important;
      color: inherit !important;
    }

    .timezone-text {
      font-size: 0.9rem;
      font-weight: 500;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 100px;
    }

    .timezone-edit-icon {
      font-size: 0.7rem;
      opacity: 0.7;
      transition: all 0.3s ease;
    }

    .timezone-link:hover .timezone-edit-icon {
      opacity: 1;
      transform: scale(1.1);
    }

    /* Modal Enhancements */
    .timezone-modal .modal-content {
      border: none;
      border-radius: 15px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
      overflow: hidden;
    }

    .timezone-modal .modal-header {
      background: linear-gradient(135deg, #3b82f6, #8b5cf6);
      color: white;
      border: none;
      padding: 20px 25px;
    }

    .timezone-modal .modal-title {
      font-weight: 600;
      font-size: 1.2rem;
    }

    .timezone-modal .close {
      color: white;
      opacity: 0.8;
      font-size: 1.5rem;
    }

    .timezone-modal .close:hover {
      opacity: 1;
    }

    .timezone-modal .modal-body {
      padding: 25px;
    }

    .timezone-search-container {
      position: relative;
      margin-bottom: 20px;
    }

    .timezone-search {
      width: 100%;
      padding: 12px 45px 12px 15px;
      border: 2px solid #e5e7eb;
      border-radius: 10px;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .timezone-search:focus {
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
      outline: none;
    }

    .timezone-search-icon {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #9ca3af;
      font-size: 1.1rem;
    }

    .timezone-select {
      width: 100%;
      padding: 12px 15px;
      border: 2px solid #e5e7eb;
      border-radius: 10px;
      font-size: 1rem;
      background: white;
      transition: all 0.3s ease;
      max-height: 200px;
      overflow-y: auto;
    }

    .timezone-select:focus {
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
      outline: none;
    }

    .timezone-preview {
      background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
      border: 1px solid #bae6fd;
      border-radius: 10px;
      padding: 15px;
      margin-top: 15px;
    }

    .timezone-preview-title {
      font-weight: 600;
      color: #0369a1;
      margin-bottom: 8px;
      font-size: 0.9rem;
    }

    .timezone-preview-time {
      font-size: 1.1rem;
      color: #0c4a6e;
      font-weight: 500;
      font-family: 'Courier New', monospace;
    }

    .timezone-preview-location {
      font-size: 0.85rem;
      color: #0369a1;
      margin-top: 5px;
    }

    .timezone-modal .modal-footer {
      background: #f8fafc;
      border: none;
      padding: 20px 25px;
    }

    .timezone-btn {
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 500;
      transition: all 0.3s ease;
      border: none;
    }

    .timezone-btn-primary {
      background: linear-gradient(135deg, #3b82f6, #1d4ed8);
      color: white;
    }

    .timezone-btn-primary:hover {
      background: linear-gradient(135deg, #1d4ed8, #1e40af);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }

    .timezone-btn-secondary {
      background: #f1f5f9;
      color: #64748b;
      border: 1px solid #e2e8f0;
    }

    .timezone-btn-secondary:hover {
      background: #e2e8f0;
      color: #475569;
    }

    /* Loading Animation */
    .timezone-loading {
      display: inline-block;
      width: 16px;
      height: 16px;
      border: 2px solid #ffffff;
      border-radius: 50%;
      border-top-color: transparent;
      animation: timezone-spin 1s ease-in-out infinite;
    }

    @keyframes timezone-spin {
      to { transform: rotate(360deg); }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .timezone-value {
        max-width: 80px;
        font-size: 0.8rem;
      }
      
      .timezone-label {
        font-size: 0.7rem;
      }
      
      .timezone-modal .modal-dialog {
        margin: 10px;
      }
    }

    /* Dark Mode Support */
    @media (prefers-color-scheme: dark) {
      .timezone-link {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(147, 51, 234, 0.2));
        border-color: rgba(59, 130, 246, 0.3);
      }
      
      .timezone-value {
        color: #f9fafb;
      }
      
      .timezone-label {
        color: #d1d5db;
      }
    }

    /* Enhanced Header Elements - Individual Improvements */
    .app-header {
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }

    .app-header .navbar-nav {
      align-items: center;
    }

    .app-header .navbar-nav .nav-item {
      margin: 0 2px;
    }

    /* Enhanced Navigation Links - Subtle Improvements */
    .nav-link-enhanced {
      position: relative;
      transition: all 0.3s ease;
      border-radius: 6px;
      margin: 0 1px;
      padding: 8px 12px !important;
      text-decoration: none !important;
      font-weight: 500;
    }

    .nav-link-enhanced:hover {
      background-color: rgba(0, 0, 0, 0.05);
      transform: translateY(-1px);
      text-decoration: none !important;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .nav-link-enhanced:active {
      transform: translateY(0);
    }

    .nav-icon {
      font-size: 1.1rem;
      transition: all 0.3s ease;
      position: relative;
    }

    .nav-link-enhanced:hover .nav-icon {
      transform: scale(1.1);
    }

    /* Enhanced Search Container - Subtle Improvements */
    .search-item {
      margin-right: 15px;
    }

    .search-container {
      position: relative;
      display: flex;
      align-items: center;
    }

    .search-input:focus {
      border-color: #007bff;
      box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
      outline: none;
      transform: scale(1.02);
    }

    .search-input::placeholder {
      color: #999;
      font-weight: 400;
    }

    .search-btn {
      border-radius: 0 20px 20px 0;
      border: 1px solid #ddd;
      background-color: #f8f9fa;
      color: #333;
      transition: all 0.3s ease;
      padding: 8px 12px;
      border-left: none;
    }

    .search-btn:hover {
      background-color: #e9ecef;
      transform: scale(1.05);
      color: #333;
      border-color: #007bff;
    }

    .search-icon {
      font-size: 0.9rem;
    }

    /* Enhanced Notification Elements - Subtle Improvements */
    .quick-actions-link {
      background: rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(0, 0, 0, 0.1);
      color: inherit !important;
    }

    .quick-actions-link:hover {
      background: rgba(0, 0, 0, 0.1);
      border-color: rgba(0, 0, 0, 0.2);
      color: inherit !important;
    }

    .quick-actions-badge {
      background: linear-gradient(135deg, #10b981, #059669) !important;
      animation: pulse 2s infinite;
      box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
    }

    /* Enhanced Notification Links - Subtle Improvements */
    .chat-link {
      background: rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(0, 0, 0, 0.1);
      color: inherit !important;
    }

    .chat-link:hover {
      background: rgba(0, 0, 0, 0.1);
      border-color: rgba(0, 0, 0, 0.2);
      color: inherit !important;
    }

    .chat-badge {
      background: linear-gradient(135deg, #6366f1, #4f46e5) !important;
      animation: bounce 1s infinite;
      box-shadow: 0 2px 4px rgba(99, 102, 241, 0.3);
    }

    .payment-link {
      background: rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(0, 0, 0, 0.1);
      color: inherit !important;
    }

    .payment-link:hover {
      background: rgba(0, 0, 0, 0.1);
      border-color: rgba(0, 0, 0, 0.2);
      color: inherit !important;
    }

    .payment-badge {
      background: linear-gradient(135deg, #ef4444, #dc2626) !important;
      animation: shake 0.5s ease-in-out infinite alternate;
      box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
    }

    .patient-link {
      background: rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(0, 0, 0, 0.1);
      color: inherit !important;
    }

    .patient-link:hover {
      background: rgba(0, 0, 0, 0.1);
      border-color: rgba(0, 0, 0, 0.2);
      color: inherit !important;
    }

    .patient-badge {
      background: linear-gradient(135deg, #f59e0b, #d97706) !important;
      animation: glow 2s ease-in-out infinite alternate;
      box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);
    }

    .language-link {
      background: rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(0, 0, 0, 0.1);
      color: inherit !important;
    }

    .language-link:hover {
      background: rgba(0, 0, 0, 0.1);
      border-color: rgba(0, 0, 0, 0.2);
      color: inherit !important;
    }

    .language-flag {
      font-size: 1.2rem;
      border-radius: 3px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .fullscreen-link {
      background: rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(0, 0, 0, 0.1);
      color: inherit !important;
    }

    .fullscreen-link:hover {
      background: rgba(0, 0, 0, 0.1);
      border-color: rgba(0, 0, 0, 0.2);
      color: inherit !important;
    }

    .logout-link {
      background: rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(0, 0, 0, 0.1);
      color: inherit !important;
    }

    .logout-link:hover {
      background: rgba(0, 0, 0, 0.1);
      border-color: rgba(0, 0, 0, 0.2);
      color: inherit !important;
    }

    /* Badge Animations */
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
    }

    @keyframes bounce {
      0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
      40% { transform: translateY(-3px); }
      60% { transform: translateY(-2px); }
    }

    @keyframes shake {
      0% { transform: translateX(0); }
      100% { transform: translateX(2px); }
    }

    @keyframes glow {
      0% { box-shadow: 0 0 5px rgba(245, 158, 11, 0.5); }
      100% { box-shadow: 0 0 15px rgba(245, 158, 11, 0.8); }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .nav-link-enhanced {
        padding: 6px 8px !important;
        margin: 0 1px;
      }
      
      .nav-icon {
        font-size: 1rem;
      }
      
      .search-container {
        margin-right: 10px;
      }

      .search-input {
        width: 180px;
        font-size: 0.85rem;
      }

      .timezone-text {
        max-width: 80px;
        font-size: 0.8rem;
      }
    }

    @media (max-width: 576px) {
      .search-input {
        width: 150px;
      }

      .timezone-text {
        max-width: 60px;
        font-size: 0.75rem;
      }

      .timezone-link {
        gap: 4px;
      }
    }

    /* Dark mode adjustments */
    .navbar-dark .nav-link-enhanced {
      color: inherit !important;
    }

    .navbar-dark .nav-link-enhanced:hover {
      color: inherit !important;
    }

    .navbar-dark .timezone-text {
      color: rgba(255, 255, 255, 0.9);
    }

    .navbar-dark .timezone-edit-icon {
      color: rgba(255, 255, 255, 0.7);
    }
  </style>

  <!-- Include Timezone Component -->
  <?php 
  // Ensure language is loaded for timezone component
  $this->load->helper('language');
  
  // Load timezone data if not already loaded
  if (!isset($timezones)) {
    $this->load->model('settings_model');
    $settings = $this->settings_model->getSettings();
    
    // Load timezone list
    $timezones = array(
      'Pacific/Midway' => "(GMT-11:00) Midway Island",
      'US/Samoa' => "(GMT-11:00) Samoa", 
      'US/Hawaii' => "(GMT-10:00) Hawaii",
      'US/Alaska' => "(GMT-09:00) Alaska",
      'US/Pacific' => "(GMT-08:00) Pacific Time (US & Canada)",
      'US/Arizona' => "(GMT-07:00) Arizona",
      'US/Mountain' => "(GMT-07:00) Mountain Time (US & Canada)",
      'US/Central' => "(GMT-06:00) Central Time (US & Canada)",
      'US/Eastern' => "(GMT-05:00) Eastern Time (US & Canada)",
      'US/East-Indiana' => "(GMT-05:00) Indiana (East)",
      'Canada/Atlantic' => "(GMT-04:00) Atlantic Time (Canada)",
      'America/La_Paz' => "(GMT-04:00) La Paz",
      'America/Santiago' => "(GMT-04:00) Santiago",
      'Canada/Newfoundland' => "(GMT-03:30) Newfoundland",
      'America/Buenos_Aires' => "(GMT-03:00) Buenos Aires",
      'Greenland' => "(GMT-03:00) Greenland",
      'Atlantic/Stanley' => "(GMT-02:00) Stanley",
      'Atlantic/Azores' => "(GMT-01:00) Azores",
      'Atlantic/Cape_Verde' => "(GMT-01:00) Cape Verde Is.",
      'Africa/Casablanca' => "(GMT) Casablanca",
      'Europe/Dublin' => "(GMT) Dublin",
      'Europe/Lisbon' => "(GMT) Lisbon",
      'Europe/London' => "(GMT) London",
      'Africa/Monrovia' => "(GMT) Monrovia",
      'Europe/Amsterdam' => "(GMT+01:00) Amsterdam",
      'Europe/Belgrade' => "(GMT+01:00) Belgrade",
      'Europe/Berlin' => "(GMT+01:00) Berlin",
      'Europe/Bratislava' => "(GMT+01:00) Bratislava",
      'Europe/Brussels' => "(GMT+01:00) Brussels",
      'Europe/Budapest' => "(GMT+01:00) Budapest",
      'Europe/Copenhagen' => "(GMT+01:00) Copenhagen",
      'Europe/Ljubljana' => "(GMT+01:00) Ljubljana",
      'Europe/Madrid' => "(GMT+01:00) Madrid",
      'Europe/Paris' => "(GMT+01:00) Paris",
      'Europe/Prague' => "(GMT+01:00) Prague",
      'Europe/Rome' => "(GMT+01:00) Rome",
      'Europe/Sarajevo' => "(GMT+01:00) Sarajevo",
      'Europe/Skopje' => "(GMT+01:00) Skopje",
      'Europe/Stockholm' => "(GMT+01:00) Stockholm",
      'Europe/Vienna' => "(GMT+01:00) Vienna",
      'Europe/Warsaw' => "(GMT+01:00) Warsaw",
      'Europe/Zagreb' => "(GMT+01:00) Zagreb",
      'Europe/Athens' => "(GMT+02:00) Athens",
      'Europe/Bucharest' => "(GMT+02:00) Bucharest",
      'Africa/Cairo' => "(GMT+02:00) Cairo",
      'Africa/Harare' => "(GMT+02:00) Harare",
      'Europe/Helsinki' => "(GMT+02:00) Helsinki",
      'Europe/Istanbul' => "(GMT+02:00) Istanbul",
      'Asia/Jerusalem' => "(GMT+02:00) Jerusalem",
      'Europe/Kiev' => "(GMT+02:00) Kyiv",
      'Europe/Minsk' => "(GMT+02:00) Minsk",
      'Europe/Riga' => "(GMT+02:00) Riga",
      'Europe/Sofia' => "(GMT+02:00) Sofia",
      'Europe/Tallinn' => "(GMT+02:00) Tallinn",
      'Europe/Vilnius' => "(GMT+02:00) Vilnius",
      'Asia/Baghdad' => "(GMT+03:00) Baghdad",
      'Asia/Kuwait' => "(GMT+03:00) Kuwait",
      'Africa/Nairobi' => "(GMT+03:00) Nairobi",
      'Asia/Riyadh' => "(GMT+03:00) Riyadh",
      'Asia/Tehran' => "(GMT+03:30) Tehran",
      'Europe/Moscow' => "(GMT+03:00) Moscow",
      'Asia/ Dubai' => "(GMT+04:00) Abu Dhabi",
      'Asia/Muscat' => "(GMT+04:00) Muscat",
      'Asia/Baku' => "(GMT+04:00) Baku",
      'Asia/Yerevan' => "(GMT+04:00) Yerevan",
      'Asia/Kabul' => "(GMT+04:30) Kabul",
      'Asia/Yekaterinburg' => "(GMT+05:00) Yekaterinburg",
      'Asia/Karachi' => "(GMT+05:00) Karachi",
      'Asia/Tashkent' => "(GMT+05:00) Tashkent",
      'Asia/Kolkata' => "(GMT+05:30) Chennai",
      'Asia/Kolkata' => "(GMT+05:30) Mumbai",
      'Asia/Kolkata' => "(GMT+05:30) New Delhi",
      'Asia/Kathmandu' => "(GMT+05:45) Kathmandu",
      'Asia/Dhaka' => "(GMT+06:00) Almaty",
      'Asia/Dhaka' => "(GMT+06:00) Astana",
      'Asia/Dhaka' => "(GMT+06:00) Dhaka",
      'Asia/Novosibirsk' => "(GMT+06:00) Novosibirsk",
      'Asia/Rangoon' => "(GMT+06:30) Yangon (Rangoon)",
      'Asia/Bangkok' => "(GMT+07:00) Bangkok",
      'Asia/Hanoi' => "(GMT+07:00) Hanoi",
      'Asia/Jakarta' => "(GMT+07:00) Jakarta",
      'Asia/Krasnoyarsk' => "(GMT+07:00) Krasnoyarsk",
      'Asia/Novosibirsk' => "(GMT+07:00) Novosibirsk",
      'Asia/Shanghai' => "(GMT+08:00) Beijing",
      'Asia/Chongqing' => "(GMT+08:00) Chongqing",
      'Asia/Hong_Kong' => "(GMT+08:00) Hong Kong",
      'Asia/Krasnoyarsk' => "(GMT+08:00) Krasnoyarsk",
      'Asia/Kuala_Lumpur' => "(GMT+08:00) Kuala Lumpur",
      'Australia/Perth' => "(GMT+08:00) Perth",
      'Asia/Singapore' => "(GMT+08:00) Singapore",
      'Asia/Taipei' => "(GMT+08:00) Taipei",
      'Asia/Ulaanbaatar' => "(GMT+08:00) Ulaan Bataar",
      'Asia/Urumqi' => "(GMT+08:00) Urumqi",
      'Asia/Irkutsk' => "(GMT+08:00) Irkutsk",
      'Asia/Seoul' => "(GMT+09:00) Seoul",
      'Asia/Tokyo' => "(GMT+09:00) Tokyo",
      'Asia/Tokyo' => "(GMT+09:00) Osaka",
      'Asia/Tokyo' => "(GMT+09:00) Sapporo",
      'Asia/Yakutsk' => "(GMT+09:00) Yakutsk",
      'Australia/Adelaide' => "(GMT+09:30) Adelaide",
      'Australia/Darwin' => "(GMT+09:30) Darwin",
      'Asia/Vladivostok' => "(GMT+10:00) Vladivostok",
      'Pacific/Port_Moresby' => "(GMT+10:00) Guam",
      'Pacific/Port_Moresby' => "(GMT+10:00) Port Moresby",
      'Australia/Sydney' => "(GMT+10:00) Sydney",
      'Australia/Brisbane' => "(GMT+10:00) Brisbane",
      'Australia/Melbourne' => "(GMT+10:00) Melbourne",
      'Asia/Magadan' => "(GMT+10:00) Magadan",
      'Pacific/Noumea' => "(GMT+11:00) New Caledonia",
      'Pacific/Guadalcanal' => "(GMT+11:00) Solomon Is.",
      'Asia/Magadan' => "(GMT+11:00) Magadan",
      'Pacific/Auckland' => "(GMT+12:00) Auckland",
      'Pacific/Fiji' => "(GMT+12:00) Fiji",
      'Pacific/Kwajalein' => "(GMT+12:00) International Date Line West",
      'Asia/Kamchatka' => "(GMT+12:00) Kamchatka",
      'Pacific/Tongatapu' => "(GMT+13:00) Nuku'alofa"
    );
  }
  ?>
  <?php $this->load->view('timezone_component', array('timezones' => $timezones, 'settings' => $settings ?? null)); ?>
