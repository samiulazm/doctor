<?php
/**
 * Document-Style Tables Include
 * 
 * This file should be included in any page that displays tables
 * to apply consistent document-style minimal spacing throughout the system.
 * 
 * Usage: <?php $this->load->view('document_style_tables_include'); ?>
 */
?>

<!-- Document-Style Tables CSS -->
<link rel="stylesheet" href="<?php echo base_url('application/assets/css/document-style-tables.css'); ?>" type="text/css">

<!-- Document-Style Tables JavaScript -->
<script src="<?php echo base_url('application/assets/js/document-style-tables.js'); ?>" type="text/javascript"></script>

<!-- Auto-apply document style to existing tables -->
<script type="text/javascript">
$(document).ready(function() {
    'use strict';
    
    // Apply document style to all existing tables
    if (typeof applyDocumentStyleToTables === 'function') {
        applyDocumentStyleToTables();
    }
    
    // Reapply when new content is loaded
    $(document).on('DOMNodeInserted', function() {
        setTimeout(function() {
            if (typeof applyDocumentStyleToTables === 'function') {
                applyDocumentStyleToTables();
            }
        }, 100);
    });
});
</script>
