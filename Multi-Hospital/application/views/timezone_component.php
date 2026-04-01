<!-- Enhanced Timezone Selection Modal -->
<?php 
// Ensure language helper is loaded for this component
if (!function_exists('lang')) {
    $CI =& get_instance();
    $CI->load->helper('language');
}

// Debug: Check if language keys are working
// Uncomment the line below to test if language keys are working
// echo "<!-- Debug: timezone=" . lang('timezone') . ", settings=" . lang('settings') . " -->";
?>
<div class="modal fade timezone-modal" id="timezoneModal" tabindex="-1" role="dialog" aria-labelledby="timezoneModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="timezoneModalLabel">
                    <i class="fas fa-clock mr-2"></i>
                    <?php echo lang('timezone'); ?> <?php echo lang('settings'); ?>
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="<?php echo lang('close'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="timezoneForm"> 
                    <!-- Search Container -->
                    <div class="timezone-search-container">
                        <input type="text" id="timezoneSearch" class="timezone-search" placeholder="<?php echo lang('search_timezones'); ?>">
                        <i class="fas fa-search timezone-search-icon"></i>
                    </div>
                    
                    <!-- Timezone Selection -->
                    <div class="form-group">
                        <label for="timezoneSelect" class="font-weight-bold">
                            <i class="fas fa-globe mr-2"></i>
                            <?php echo lang('select_timezone'); ?>
                        </label>
                        <select class="timezone-select" id="timezoneSelect" name="timezone" required size="8">
                            <?php if (isset($timezones)): ?>
                                <?php foreach ($timezones as $key => $timezone): ?>
                                    <option value="<?php echo $key; ?>" <?php echo ($key == ($settings->timezone ?? 'UTC')) ? 'selected' : ''; ?>>
                                        <?php echo $timezone; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="UTC" selected><?php echo lang('utc_london'); ?></option>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <!-- Real-time Preview -->
                    <div class="timezone-preview" id="timezonePreview">
                        <div class="timezone-preview-title">
                            <i class="fas fa-eye mr-1"></i>
                            <?php echo lang('preview'); ?>
                        </div>
                        <div class="timezone-preview-time" id="previewTime">
                            <?php echo date('Y-m-d H:i:s T'); ?>
                        </div>
                        <div class="timezone-preview-location" id="previewLocation">
                            <?php echo $settings->timezone ?? 'UTC'; ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a type="button" class="btn timezone-btn timezone-btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>
                    <?php echo lang('cancel'); ?>
                            </a>
                <a type="button" class="btn timezone-btn timezone-btn-primary" id="updateTimezoneBtn">
                    <i class="fas fa-save mr-1"></i>
                    <?php echo lang('update_timezone'); ?>
                            </a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let timezoneUpdateInterval;
    let originalTimezones = [];
    
    // Store original timezones for search functionality
    $('#timezoneSelect option').each(function() {
        originalTimezones.push({
            value: $(this).val(),
            text: $(this).text(),
            element: $(this)
        });
    });

    // Update timezone display
    function updateTimezoneDisplay() {
        var selectedTimezone = $('#timezoneSelect').val();
        var selectedText = $('#timezoneSelect option:selected').text();
        $('#current-timezone').text(selectedTimezone);
    }

    // Update timezone preview
    function updateTimezonePreview() {
        var selectedTimezone = $('#timezoneSelect').val();
        var selectedText = $('#timezoneSelect option:selected').text();
        
        if (selectedTimezone) {
            $('#previewLocation').text(selectedText);
            
            // Create a date in the selected timezone
            try {
                var now = new Date();
                var timeInTimezone = new Date(now.toLocaleString("en-US", {timeZone: selectedTimezone}));
                var formattedTime = timeInTimezone.toLocaleString('en-US', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    timeZoneName: 'short'
                });
                $('#previewTime').text(formattedTime);
            } catch (e) {
                $('#previewTime').text('<?php echo lang('invalid_timezone'); ?>');
            }
        }
    }

    // Search functionality
    $('#timezoneSearch').on('input', function() {
        var searchTerm = $(this).val().toLowerCase();
        var $select = $('#timezoneSelect');
        
        // Clear current options
        $select.empty();
        
        // Filter and add matching options
        originalTimezones.forEach(function(timezone) {
            if (timezone.text.toLowerCase().includes(searchTerm)) {
                $select.append(timezone.element.clone());
            }
        });
        
        // If no search term, show all
        if (!searchTerm) {
            originalTimezones.forEach(function(timezone) {
                $select.append(timezone.element.clone());
            });
        }
        
        // Restore selection if it exists
        var currentValue = $('#timezoneSelect').data('current-value');
        if (currentValue) {
            $('#timezoneSelect').val(currentValue);
        }
    });

    // Update timezone
    $('#updateTimezoneBtn').click(function() {
        var timezone = $('#timezoneSelect').val();
        
        if (!timezone) {
            showNotification('<?php echo lang('please_select_timezone'); ?>', 'warning');
            return;
        }

        // Show loading state
        var $btn = $(this);
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="timezone-loading mr-2"></span><?php echo lang('updating'); ?>...');

        $.ajax({
            url: '<?php echo site_url('home/updateTimezone'); ?>',
            type: 'POST',
            data: { timezone: timezone },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update display
                    updateTimezoneDisplay();
                    
                    // Show success notification
                    showNotification('<?php echo lang('timezone_updated_successfully'); ?>', 'success');
                    
                    // Close modal
                    $('#timezoneModal').modal('hide');
                    
                    // Reload page to apply timezone changes
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showNotification(response.message || '<?php echo lang('failed_to_update_timezone'); ?>', 'error');
                }
            },
            error: function() {
                showNotification('<?php echo lang('an_error_occurred'); ?>', 'error');
            },
            complete: function() {
                // Reset button state
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // Update timezone display and preview when selection changes
    $('#timezoneSelect').change(function() {
        updateTimezoneDisplay();
        updateTimezonePreview();
        $(this).data('current-value', $(this).val());
    });

    // Update time display every second
    function updateTimeDisplays() {
        var now = new Date();
        $('#current-time-display').text(now.toLocaleString());
        updateTimezonePreview();
    }

    // Start time updates
    timezoneUpdateInterval = setInterval(updateTimeDisplays, 1000);

    // Initialize preview on modal show
    $('#timezoneModal').on('shown.bs.modal', function() {
        updateTimezonePreview();
        $('#timezoneSelect').data('current-value', $('#timezoneSelect').val());
    });

    // Clear interval when modal is hidden
    $('#timezoneModal').on('hidden.bs.modal', function() {
        if (timezoneUpdateInterval) {
            clearInterval(timezoneUpdateInterval);
        }
    });

    // Notification function
    function showNotification(message, type) {
        // Remove existing notifications
        $('.timezone-notification').remove();
        
        var alertClass = 'alert-info';
        var iconClass = 'fas fa-info-circle';
        
        switch(type) {
            case 'success':
                alertClass = 'alert-success';
                iconClass = 'fas fa-check-circle';
                break;
            case 'warning':
                alertClass = 'alert-warning';
                iconClass = 'fas fa-exclamation-triangle';
                break;
            case 'error':
                alertClass = 'alert-danger';
                iconClass = 'fas fa-times-circle';
                break;
        }
        
        var notification = $('<div class="timezone-notification alert ' + alertClass + ' alert-dismissible fade show" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
            '<i class="' + iconClass + ' mr-2"></i>' + message +
            '<button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '</div>');
        
        $('body').append(notification);
        
        // Auto remove after 5 seconds
        setTimeout(function() {
            notification.alert('close');
        }, 5000);
    }

    // Keyboard shortcuts
    $(document).keydown(function(e) {
        // Ctrl/Cmd + T to open timezone modal
        if ((e.ctrlKey || e.metaKey) && e.which === 84) {
            e.preventDefault();
            $('#timezoneModal').modal('show');
        }
        
        // Escape to close modal
        if (e.which === 27) {
            $('#timezoneModal').modal('hide');
        }
    });

    // Initialize
    updateTimezoneDisplay();
    updateTimezonePreview();
});
</script>
