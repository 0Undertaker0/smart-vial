<?php
require_once __DIR__ . '/../app/config.php';

// Simulate a logged-in user without permissions
$_SESSION['user'] = [
    'id' => 9999,
    'role_id' => 9999,
    'role_name' => 'testsinpermiso'
];

// Attempt to access a page that requires permiso_view
require_permission('permiso_view');

// If allowed, show OK (should not happen for this test)
echo "PERM_OK";
