<?php
// Endpoint temporal para resetear OPcache (solo en entorno local)
if (!empty($_GET['secret']) && $_GET['secret'] === 'dev-clear') {
    if (function_exists('opcache_reset')) {
        $ok = opcache_reset();
        echo $ok ? 'OPcache reset OK' : 'OPcache reset FAILED';
    } else {
        echo 'OPcache not available';
    }
} else {
    http_response_code(403);
    echo 'Forbidden';
}
