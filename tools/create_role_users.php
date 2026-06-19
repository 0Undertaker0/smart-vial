<?php
require_once __DIR__ . '/../app/config.php';
$db = getDb();

$roles = [
    'pnc' => [ 'email' => 'pnc@demo.local', 'pass' => 'pnc123' ],
    'vmt' => [ 'email' => 'vmt@demo.local', 'pass' => 'vmt123' ],
    'ministerio de salud' => [ 'email' => 'salud@demo.local', 'pass' => 'salud123' ],
];

foreach ($roles as $name => $u) {
    // ensure role
    $stmt = $db->prepare('SELECT id FROM roles WHERE LOWER(nombre) = LOWER(?) LIMIT 1');
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $role_id = (int)$row['id'];
    } else {
        $ins = $db->prepare('INSERT INTO roles (nombre) VALUES (?)');
        $ins->bind_param('s', $name);
        $ins->execute();
        $role_id = $ins->insert_id;
        $ins->close();
        echo "Created role '{$name}' id={$role_id}\n";
    }
    $stmt->close();

    // create or update user
    $email = $u['email'];
    $plain = $u['pass'];
    $stmt = $db->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($r = $res->fetch_assoc()) {
        $uid = (int)$r['id'];
        $hash = password_hash($plain, PASSWORD_DEFAULT);
        $up = $db->prepare('UPDATE usuarios SET password = ?, role_id = ? WHERE id = ?');
        $up->bind_param('sii', $hash, $role_id, $uid);
        $up->execute();
        $up->close();
        echo "Updated user {$email} id={$uid} assigned role {$name}\n";
    } else {
        $hash = password_hash($plain, PASSWORD_DEFAULT);
        $ins = $db->prepare('INSERT INTO usuarios (nombre,email,password,activo,role_id) VALUES (?,?,?,?,?)');
        $nombre = ucfirst($name);
        $activo = 1;
        $ins->bind_param('sssii', $nombre, $email, $hash, $activo, $role_id);
        $ins->execute();
        $uid = $ins->insert_id;
        $ins->close();
        echo "Created user {$email} id={$uid} role={$name}\n";
    }
    $stmt->close();
}

echo "Done.\n";
