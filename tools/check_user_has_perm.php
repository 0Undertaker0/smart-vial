<?php
require_once __DIR__ . '/../app/config.php';

// Simulate session user
$_SESSION['user'] = ['id' => 9999, 'role_id' => 9999, 'role_name' => 'testsinpermiso'];

var_export(user_has_permission('permiso_view'));
