<?php
require_once __DIR__ . '/../app/config.php';

$email = 'admin@smartvial.local';
$newPlain = 'admin123';
$newHash = password_hash($newPlain, PASSWORD_DEFAULT);

$db = getDb();
$stmt = $db->prepare('UPDATE usuarios SET password = ? WHERE email = ?');
if (!$stmt) { echo "Prepare failed: " . $db->error . "\n"; exit(1); }
$stmt->bind_param('ss', $newHash, $email);
$stmt->execute();
echo "Updated rows: " . $stmt->affected_rows . "\n";
$stmt->close();
