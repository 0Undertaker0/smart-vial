<?php
require_once __DIR__ . '/../app/config.php';

// WARNING: development-only helper that signs in as admin for testing
$_SESSION['user'] = [
    'id' => 1,
    'role_id' => 1,
    'role_name' => 'admin'
];

// Redirect to root index with controller param to avoid redirect loops
header('Location: /?c=incident');
exit;
