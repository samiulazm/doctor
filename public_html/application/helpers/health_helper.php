<?php

if (!function_exists('ci_health_release_meta')) {
    /**
     * Shared release metadata for health responses.
     *
     * @return array<string, string>
     */
    function ci_health_release_meta($release)
    {
        if (is_string($release) && $release !== '') {
            return array('release' => $release);
        }

        return array();
    }
}

if (!function_exists('ci_health_ready_payload')) {
    /**
     * Readiness payload without leaking DB details.
     *
     * @return array<string, string>
     */
    function ci_health_ready_payload($ok, $release = '', $error = null, $time = null)
    {
        $payload = array_merge(array(
            'status' => $ok ? 'ready' : 'not_ready',
            'database' => $ok ? 'connected' : 'disconnected',
            'time' => $time ?: gmdate('c'),
        ), ci_health_release_meta($release));

        if ($error !== null) {
            $payload['error'] = $error;
        }

        return $payload;
    }
}
