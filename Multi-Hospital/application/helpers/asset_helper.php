<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('asset_url')) {
    /**
     * URL to a static file under the site root, with optional cache-busting (?v=).
     * Bump $config['asset_version'] in application/config/config.php on each deploy.
     *
     * @param string $path Path relative to site root, e.g. application/assets/css/app.css
     */
    function asset_url($path)
    {
        $CI =& get_instance();
        $url = base_url($path);
        $v = $CI->config->item('asset_version');
        if ($v !== null && $v !== '') {
            $sep = (strpos($url, '?') !== false) ? '&' : '?';
            $url .= $sep . 'v=' . rawurlencode((string) $v);
        }
        return $url;
    }
}
