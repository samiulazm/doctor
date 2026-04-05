    </main><!-- /.app-main -->

<footer class="app-footer no-print">
    <div class="text-center">
        <?php echo date('Y'); ?> &copy;
        <?php
        $this->db->where('hospital_id', $this->hospital_id);
        echo $this->db->get('settings')->row()->footer_message;
        ?>
        <a href="<?php echo current_url() . '#'; ?>" class="go-top">
            <i class="fa fa-angle-up"></i>
        </a>
    </div>
</footer>
<!--footer end-->

<?php

// FullCalendar is only used on hospital dashboard (home/index) and appointment/calendar — skip ~200KB+ JS/CSS elsewhere.
$need_fullcalendar = (
    ($this->router->fetch_class() === 'home' && $this->router->fetch_method() === 'index')
    || ($this->router->fetch_class() === 'appointment' && $this->router->fetch_method() === 'calendar')
);

$language = $this->language;


if ($language == 'english') {
    $lang = 'en-ca';
    $langdate = 'en-CA';
} elseif ($language == 'spanish') {
    $lang = 'es';
    $langdate = 'es';
} elseif ($language == 'french') {
    $lang = 'fr';
    $langdate = 'fr';
} elseif ($language == 'portuguese') {
    $lang = 'pt';
    $langdate = 'pt';
} elseif ($language == 'arabic') {
    $lang = 'ar';
    $langdate = 'ar';
} elseif ($language == 'italian') {
    $lang = 'it';
    $langdate = 'it';
} elseif ($language == 'zh_cn') {
    $lang = 'zh-cn';
    $langdate = 'zh-CN';
} elseif ($language == 'japanese') {
    $lang = 'ja';
    $langdate = 'ja';
} elseif ($language == 'russian') {
    $lang = 'ru';
    $langdate = 'ru';
} elseif ($language == 'turkish') {
    $lang = 'tr';
    $langdate = 'tr';
} elseif ($language == 'indonesian') {
    $lang = 'id';
    $langdate = 'id';
}


?>

<!-- jQuery first: inline scripts and plugins below depend on $. -->
<script src="adminlte/plugins/jquery/jquery.min.js"></script>
<script src="adminlte/plugins/jquery-ui/jquery-ui.min.js"></script>

<script type="text/javascript">
    var langdate = "<?php echo htmlspecialchars($langdate, ENT_QUOTES, 'UTF-8'); ?>";
    $(document).ready(function() {
        $('.readonly').keydown(function(e) {
            e.preventDefault();
        });

    })
</script>

<script type="text/javascript">
    var time_format = "<?php echo htmlspecialchars(isset($this->settings->time_format) ? $this->settings->time_format : '', ENT_QUOTES, 'UTF-8'); ?>";
</script>


<script src="common/js/respond.min.js"></script>
<!-- <script type="text/javascript" src="common/assets/ckeditor/build/ckeditor.js"></script> -->
<script type="text/javascript" src="common/assets/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>
<script type="text/javascript" src="common/assets/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script src="common/js/advanced-form-components.js"></script>
<script src="common/js/jquery.cookie.js"></script>
<!--common script for all pages-->
<script src="common/js/common-scripts.js"></script>
<script class="include" type="text/javascript" src="common/js/jquery.dcjqaccordion.2.7.js"></script>
<!--script for this page only-->
<script src="common/js/editable-table.js"></script>
<script src="common/js/bootstrap-select-country.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.26.1/axios.min.js"></script>

<script src="common/assets/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="common/assets/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>


<script src="common/assets/bootstrap-datepicker/locales/bootstrap-datepicker.<?php echo $langdate; ?>.min.js"></script>

<script src="common/assets/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.<?php echo $langdate; ?>.min.js"></script>



<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<script src="adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
// jQuery bridge for Bootstrap 5 components
// Allows existing code like $('#myModal').modal('show') to work with BS5
(function($) {
    if (!$ || !bootstrap) return;
    ['Modal','Tooltip','Popover','Alert','Tab','Collapse','Dropdown','Offcanvas'].forEach(function(comp) {
        var name = comp.toLowerCase();
        if (bootstrap[comp]) {
            $.fn[name] = function(option) {
                return this.each(function() {
                    var inst = bootstrap[comp].getOrCreateInstance(this);
                    if (typeof option === 'string' && typeof inst[option] === 'function') {
                        inst[option]();
                    }
                });
            };
        }
    });
})(jQuery);
</script>
<script src="adminlte/dist/js/adminlte.min.js"></script>
<?php $this->load->view('csrf_inject'); ?>
<script src="adminlte/plugins/moment/moment.min.js"></script>
<script src="adminlte/plugins/chart.js/Chart.min.js"></script>
<script src="adminlte/plugins/sparklines/sparkline.js"></script>
<!-- AdminLTE 4: dashboard.js removed -->
<script src="adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="adminlte/plugins/datatables-bs4/js/dataTables.bootstrap5.min.js"></script>
<script src="adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="adminlte/plugins/datatables-responsive/js/responsive.bootstrap5.min.js"></script>
<script src="adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="adminlte/plugins/datatables-buttons/js/buttons.bootstrap5.min.js"></script>
<script src="adminlte/plugins/jszip/jszip.min.js"></script>
<script src="adminlte/plugins/pdfmake/pdfmake.min.js"></script>
<script src="adminlte/plugins/pdfmake/vfs_fonts.js"></script>
<script src="adminlte/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="adminlte/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="adminlte/plugins/select2/js/select2.full.min.js"></script>
<?php
$_inv = strtolower((string) $this->router->fetch_class()) === 'inventory';
$_m = strtolower((string) $this->router->fetch_method());
if ($_inv && $_m === 'items') {
    $this->load->view('inventory/items_datatable_scripts');
}
if ($_inv && $_m === 'categories') {
    $this->load->view('inventory/categories_datatable_scripts');
}
if ($_inv && ($_m === 'supplier' || $_m === 'suppliers')) {
    $this->load->view('inventory/suppliers_datatable_scripts');
}
if ($_inv && ($_m === 'purchase' || $_m === 'purchase_orders')) {
    $this->load->view('inventory/purchase_orders_scripts');
}
$_em = strtolower((string) $this->router->fetch_class()) === 'email';
if ($_em && $_m === 'autoemailtemplate') {
    echo '<script src="common/js/codearistos.min.js"></script>' . "\n";
    echo '<script src="common/assets/tinymce/tinymce.min.js"></script>' . "\n";
    echo '<script src="common/extranal/js/email/auto_email_template.js"></script>' . "\n";
}
?>
<?php
// Appointment views included these scripts before jQuery/Select2 in the footer; $.fn.select2 was undefined.
if ($this->router->fetch_class() === 'appointment') {
    $m = $this->router->fetch_method();
    if (in_array($m, ['index', 'request'], true)) {
        echo '<script src="common/extranal/js/appointment/appointment.js"></script>' . "\n";
    }
    echo '<script src="common/extranal/js/appointment/appointment_select2.js"></script>' . "\n";
}
?>
<script src="adminlte/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
<script src="adminlte/plugins/inputmask/jquery.inputmask.min.js"></script>
<script src="adminlte/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<script type="text/javascript" src="common/assets/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
<script type="text/javascript" src="common/assets/bootstrap-fileupload/bootstrap-fileupload.js"></script>
<script type="text/javascript" src="common/assets/jquery-multi-select/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="common/assets/jquery-multi-select/js/jquery.quicksearch.js"></script>
<script src="common/js/lightbox.js"></script>
<?php if (!empty($need_fullcalendar)) : ?>
<script src="adminlte/plugins/fullcalendar/main.js"></script>
<script src="adminlte/plugins/fullcalendar/locales/<?php echo $lang; ?>.js"></script>
<?php endif; ?>
<script src="adminlte/plugins/dropzone/min/dropzone.min.js"></script>
<!-- SweetAlert2 -->
<script src="adminlte/plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toastr -->
<script src="adminlte/plugins/toastr/toastr.min.js"></script>

<script src="adminlte/plugins/daterangepicker/daterangepicker.js"></script>

<!-- bootstrap-switch removed: use Bootstrap 5 native form-check form-switch -->



<?php if (!empty($need_fullcalendar)) : ?>
<script>
    $(document).ready(function() {
        "use strict";

        var calendarEl = document.getElementById('calendar');
        if (!calendarEl || typeof FullCalendar === 'undefined') {
            return;
        }

        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: "<?php echo $lang; ?>",
            themeSystem: 'bootstrap5',
            events: "appointment/getAppointmentByJason",
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,timeGridWeek,timeGridDay"
            },
            firstDay: 1,
            eventTimeFormat: {
                hour: 'numeric',
                minute: '2-digit',
                meridiem: 'short'
            },
            eventContent: function(arg) {
                var bgColor;
                switch (arg.event.extendedProps.status) {
                    case 'Pending Confirmation':
                        bgColor = "linear-gradient(135deg, #5E35B1, #8E24AA)";
                        bgColor = '#6C5B7B';

                        bgColor = '#FFD54F';


                        break;
                    case 'Confirmed':
                        bgColor = "linear-gradient(160deg, #6C5B7B, #C06C84)";
                        bgColor = "#5E35B1";
                        break;
                    case 'Cancelled':
                        bgColor = "linear-gradient(145deg, #83a4d4, #b6fbff)";
                        bgColor = "#8B0000";
                        break;
                    case 'Requested':
                        bgColor = "#36b9cc";
                        break;
                    case 'Treated':
                        bgColor = "#858796";
                        break;
                    default:
                        bgColor = "#4e73df";
                }
                return {
                    html: `<div style="background: ${bgColor}; padding: 10px; font-size: 10px; border-radius: 5px; overflow: hidden; word-wrap: break-word; text-overflow: ellipsis; max-width: 100%;">
    <span style="color: white;">${arg.timeText}</span><br/>
    <span style="color: white;">${arg.event.title}</span>
</div>`
                };
            },



            eventClick: function(info) {
                $("#medical_history").html("");
                $("#loader").show();
                if (info.event.id) {
                    $.ajax({
                        url: "patient/getMedicalHistoryByJason?id=" + info.event.id + "&from_where=calendar",
                        method: "GET",
                        dataType: "json",
                        success: function(response) {
                            "use strict";
                            $("#medical_history").html(response.view);
                            $("#loader").hide();
                        }
                    });
                }

                var cmodalEl = document.getElementById('cmodal');
                if (cmodalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(cmodalEl).show();
                }
            },
            slotDuration: "00:05:00",
            businessHours: false,
            slotEventOverlap: false,
            editable: false,
            selectable: false,
            lazyFetching: true,
            initialView: "dayGridMonth", // default view
            timeZone: false
        });

        calendar.render();
    });
</script>
<?php endif; ?>

<script src="common/extranal/js/footer.js"></script>





<script>
    Dropzone.autoDiscover = false

    // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
    var previewNode = document.querySelector("#template")
    previewNode.id = ""
    var previewTemplate = previewNode.parentNode.innerHTML
    previewNode.parentNode.removeChild(previewNode)

    var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
        url: "/target-url", // Set the url
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        parallelUploads: 20,
        previewTemplate: previewTemplate,
        autoQueue: false, // Make sure the files aren't queued until manually added
        previewsContainer: "#previews", // Define the container to display the previews
        clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
    })

    myDropzone.on("addedfile", function(file) {
        // Hookup the start button
        file.previewElement.querySelector(".start").onclick = function() {
            myDropzone.enqueueFile(file)
        }
    })
</script>



<script>
    $(".default-date-picker").datepicker({
        format: "dd-mm-yyyy",
        autoclose: true,
        todayHighlight: true,
        startDate: "01-01-1900",
        clearBtn: true,
        language: langdate,
    });
</script>


<?php if ($this->session->flashdata('swal_message')) { ?>
    <script>
        $(function() {
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
            <?php
            if ($this->session->flashdata('swal_message')) { ?>
                Toast.fire({
                    icon: '<?= $this->session->flashdata('swal_type') ?>',
                    title: '<?= $this->session->flashdata('swal_title') ?> ',
                    text: '<?= $this->session->flashdata('swal_message') ?> ',
                });
            <?php } ?>
        });
    </script>
<?php } ?>

<?php
$this->session->unset_userdata('swal_message');
$this->session->unset_userdata('swal_type');
$this->session->unset_userdata('swal_title');
?>

<script>
    $('.collapse-server').on('click', function() {
        var sidebarCollapsed = $('body').hasClass('sidebar-collapse') ? 1 : 0;
        $.ajax({
            url: 'home/updateSidebarState',
            method: 'POST',
            data: {
                sidebar: sidebarCollapsed
            },
            success: function(response) {
                console.log('Sidebar state updated successfully.');
            },
            error: function(error) {
                console.error('Error updating sidebar state:', error);
            }
        });
    });
</script>

<!-- Auto-hide flash messages -->
<script>
    $(document).ready(function() {
        // Auto-hide flash messages after 5 seconds
        $('.alert').each(function() {
            var alertEl = this;
            setTimeout(function() {
                var bsAlert = bootstrap.Alert.getOrCreateInstance(alertEl);
                if (bsAlert) bsAlert.close();
            }, 5000);
        });
    });
</script>

<?php
// Clear flash messages after they've been displayed
if ($this->session->flashdata('success') || $this->session->flashdata('error') || $this->session->flashdata('warning') || $this->session->flashdata('debug') || $this->session->flashdata('info')) {
    $this->session->unset_userdata('success');
    $this->session->unset_userdata('error');
    $this->session->unset_userdata('warning');
    $this->session->unset_userdata('debug');
    $this->session->unset_userdata('info');
}
?>

<!-- load avaiable_js.php -->

<?php $this->load->view('available_js'); ?>




<script>
    $(document).ready(function() {
        $('#darkModeToggle').change(function() {
            var isDark = $(this).is(':checked');
            if (isDark) {
                document.body.setAttribute('data-bs-theme', 'dark');
            } else {
                document.body.removeAttribute('data-bs-theme');
            }
            $('.custom-control-label i').toggleClass('fa-moon fa-sun');

            if (typeof drawChartTopServices === 'function') drawChartTopServices();
            if (typeof drawChartTopDiagnoses === 'function') drawChartTopDiagnoses();
            if (typeof drawChartBedOccupancy === 'function') drawChartBedOccupancy();
            if (typeof drawChartTopTreatments === 'function') drawChartTopTreatments();
            if (typeof drawSalesExpenseChart === 'function') drawSalesExpenseChart();

            var darkModeValue = isDark ? 1 : 0;

            $.ajax({
                url: 'home/updateDarkMode',
                method: 'POST',
                data: {
                    darkMode: darkModeValue
                }
            });



        });
    });
</script>


  </div><!-- /.app-wrapper -->
</body>

</html>