<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!config_item('csrf_protection')) {
	return;
}
$name = $this->security->get_csrf_token_name();
$hash = $this->security->get_csrf_hash();
?>
<script>
(function () {
  var n = <?php echo json_encode($name); ?>;
  var h = <?php echo json_encode($hash); ?>;
  function addCsrf(form) {
    if (!form || !form.method || form.method.toUpperCase() !== 'POST') {
      return;
    }
    if (form.querySelector('input[name="' + n + '"]')) {
      return;
    }
    var i = document.createElement('input');
    i.type = 'hidden';
    i.name = n;
    i.value = h;
    form.appendChild(i);
  }
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form').forEach(addCsrf);
  });
  document.addEventListener('submit', function (e) {
    if (e.target && e.target.tagName === 'FORM') {
      addCsrf(e.target);
    }
  }, true);
  if (typeof jQuery !== 'undefined') {
    jQuery(function ($) {
      $.ajaxPrefilter(function (options) {
        if (!options.type || options.type.toUpperCase() !== 'POST') {
          return;
        }
        if (options.data instanceof FormData) {
          if (!options.data.has(n)) {
            options.data.append(n, h);
          }
          return;
        }
        var ct = (options.contentType || '').toString().toLowerCase();
        if (ct.indexOf('application/json') !== -1) {
          if (typeof options.data === 'object' && options.data !== null && !Array.isArray(options.data)) {
            options.data[n] = h;
            return;
          }
          if (typeof options.data === 'string') {
            try {
              var parsed = options.data ? JSON.parse(options.data) : {};
              if (parsed && typeof parsed === 'object' && !Array.isArray(parsed)) {
                parsed[n] = h;
                options.data = JSON.stringify(parsed);
              }
            } catch (e) {
              /* leave body unchanged */
            }
            return;
          }
        }
        if (typeof options.data === 'string') {
          if (options.data.indexOf(encodeURIComponent(n) + '=') === -1 && options.data.indexOf(n + '=') === -1) {
            options.data += (options.data ? '&' : '') + encodeURIComponent(n) + '=' + encodeURIComponent(h);
          }
          return;
        }
        if (options.data === undefined || options.data === null) {
          options.data = {};
        }
        if (typeof options.data === 'object' && options.data !== null) {
          options.data[n] = h;
        }
      });
    });
  }
})();
</script>
