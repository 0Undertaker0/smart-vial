<?php
// Diagnostic script MySQL de SMARTVIAL. Verifica la conexión y muestra algunos registros de la tabla usuarios.
require_once __DIR__ . '/../app/config.php';

echo "SMARTVIAL DB diagnostic\n";
echo "DB_HOST=" . DB_HOST . " DB_NAME=" . DB_NAME . "\n";
try {
    $db = getDb();
    if ($db->connect_errno) {
        echo "DB connect error: " . $db->connect_error . "\n";
        exit(1);
    }
    echo "Connected to MySQL successfully\n";
    $res = $db->query("SELECT id,nombre,email,password,activo,role_id FROM usuarios LIMIT 20");
    if ($res) {
        echo "Usuarios found:\n";
        while ($r = $res->fetch_assoc()) {
            echo json_encode($r, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
        }
    } else {
        echo "No rows returned or usuarios table not present\n";
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
