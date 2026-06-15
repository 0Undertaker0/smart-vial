<?php
require_once __DIR__ . '/../app/config.php';

$email = 'admin@smartvial.local';
$plain = 'admin123';
$db = getDb();
$stmt = $db->prepare('SELECT password FROM usuarios WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    echo "Hash from DB: " . $row['password'] . "\n";
    echo "password_verify('admin123', hash) => ";
    var_export(password_verify($plain, $row['password']));
    echo "\n";
} else {
    echo "No user found for $email\n";
}
$stmt->close();
