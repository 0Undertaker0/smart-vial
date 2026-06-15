<?php
require_once __DIR__ . '/../app/config.php';

$email = 'agente@demo.local';
$plain = 'demo123';
$role_name = 'agente';
$need_perms = ['incident_view','incident_create'];

$db = getDb();
// Ensure role exists
$stmt = $db->prepare('SELECT id FROM roles WHERE nombre = ? LIMIT 1');
$stmt->bind_param('s', $role_name);
$stmt->execute();
$res = $stmt->get_result();
if ($r = $res->fetch_assoc()) {
    $role_id = (int)$r['id'];
} else {
    $stmt2 = $db->prepare('INSERT INTO roles (nombre) VALUES (?)');
    $stmt2->bind_param('s', $role_name);
    $stmt2->execute();
    $role_id = $stmt2->insert_id;
    $stmt2->close();
}
$stmt->close();

// Ensure permissions exist
$perm_ids = [];
foreach ($need_perms as $p) {
    $stmt = $db->prepare('SELECT id FROM permisos WHERE clave = ? LIMIT 1');
    $stmt->bind_param('s', $p);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $perm_ids[$p] = (int)$row['id'];
    } else {
        $stmt2 = $db->prepare('INSERT INTO permisos (clave, descripcion) VALUES (?,?)');
        $desc = 'Auto-created permission ' . $p;
        $stmt2->bind_param('ss', $p, $desc);
        $stmt2->execute();
        $perm_ids[$p] = $stmt2->insert_id;
        $stmt2->close();
    }
    $stmt->close();
}

// Assign perms to role
foreach ($perm_ids as $pkey => $pid) {
    $stmt = $db->prepare('SELECT id FROM roles_permisos WHERE role_id = ? AND permiso_id = ? LIMIT 1');
    $stmt->bind_param('ii', $role_id, $pid);
    $stmt->execute();
    $res = $stmt->get_result();
    if (!$res->fetch_assoc()) {
        $stmt2 = $db->prepare('INSERT INTO roles_permisos (role_id, permiso_id) VALUES (?,?)');
        $stmt2->bind_param('ii', $role_id, $pid);
        $stmt2->execute();
        $stmt2->close();
    }
    $stmt->close();
}

// Create user if not exists
$stmt = $db->prepare('SELECT id,password FROM usuarios WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $uid = (int)$row['id'];
    // update password
    $newHash = password_hash($plain, PASSWORD_DEFAULT);
    $up = $db->prepare('UPDATE usuarios SET password = ?, role_id = ? WHERE id = ?');
    $up->bind_param('sii', $newHash, $role_id, $uid);
    $up->execute();
    $up->close();
    echo "Updated existing user {$email} (id={$uid})\n";
} else {
    $hash = password_hash($plain, PASSWORD_DEFAULT);
    $ins = $db->prepare('INSERT INTO usuarios (nombre,email,password,activo,role_id) VALUES (?,?,?,?,?)');
    $nombre = 'Usuario Agente';
    $activo = 1;
    $ins->bind_param('sssii', $nombre, $email, $hash, $activo, $role_id);
    $ins->execute();
    $uid = $ins->insert_id;
    $ins->close();
    echo "Created user {$email} (id={$uid})\n";
}
$stmt->close();

// Verify password
$stmt = $db->prepare('SELECT password, role_id FROM usuarios WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
echo "Password verify for {$email} with plain '{$plain}': ";
var_export(password_verify($plain, $row['password']));
echo "\n";

// List role permissions
$stmt = $db->prepare('SELECT p.clave FROM permisos p JOIN roles_permisos rp ON rp.permiso_id = p.id WHERE rp.role_id = ?');
$stmt->bind_param('i', $role_id);
$stmt->execute();
$res = $stmt->get_result();
$assigned = [];
while ($r = $res->fetch_assoc()) $assigned[] = $r['clave'];
echo "Role '{$role_name}' (id={$role_id}) assigned permissions: " . implode(', ', $assigned) . "\n";
