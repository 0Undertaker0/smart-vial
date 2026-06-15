<?php
// Database configuration - adjust if needed
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'smartvial');
define('DB_CHARSET', 'utf8mb4');

function getDb()
{
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_errno) {
        die('DB connect error: ' . $mysqli->connect_error);
    }
    $mysqli->set_charset(DB_CHARSET);
    return $mysqli;
}

// Secure session cookie parameters
if (session_status() === PHP_SESSION_NONE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

function e($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// CSRF helpers
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_input()
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function validate_csrf($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token ?? '');
}

function require_csrf()
{
    if (!validate_csrf($_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        echo '<div class="container mt-5"><div class="alert alert-danger">CSRF token inválido</div></div>';
        exit;
    }
}

// Authentication / Authorization helpers
function current_user() {
    return $_SESSION['user'] ?? null;
}

function is_logged_in() {
    return !empty($_SESSION['user']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: ../public/');
        exit;
    }
}

function user_has_permission($clave)
{
    if (!is_logged_in()) return false;
    $roleId = $_SESSION['user']['role_id'] ?? 0;
    // Admin role (id=1) has full access
    if ($roleId == 1) return true;
    $db = getDb();
    $stmt = $db->prepare('SELECT 1 FROM permisos p JOIN roles_permisos rp ON rp.permiso_id = p.id WHERE p.clave = ? AND rp.role_id = ? LIMIT 1');
    if (!$stmt) return false;
    $stmt->bind_param('si', $clave, $roleId);
    $stmt->execute();
    $res = $stmt->get_result();
    $has = $res && $res->num_rows > 0;
    $stmt->close();
    return $has;
}

function require_permission($clave)
{
    require_login();
    if (!user_has_permission($clave)) {
        http_response_code(403);
        echo '<div class="container mt-5"><div class="alert alert-danger">Acceso denegado. Permiso requerido: '.e($clave).'</div></div>';
        exit;
    }
}
