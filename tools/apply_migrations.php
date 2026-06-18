<?php
require_once __DIR__ . '/../app/config.php';

echo "Aplicando migraciones desde sql/add_incidentes_columns.sql\n";
$sqlFile = __DIR__ . '/../sql/add_incidentes_columns.sql';
if (!file_exists($sqlFile)) {
    echo "ERROR: archivo de migración no encontrado: $sqlFile\n";
    exit(1);
}

$content = file_get_contents($sqlFile);
$db = getDb();

// Remove line comments and split by semicolon
$lines = explode("\n", $content);
$clean = [];
foreach ($lines as $ln) {
    $t = trim($ln);
    if ($t === '') continue;
    if (strpos($t, '--') === 0) continue;
    // remove inline comments starting with --
    $clean[] = $ln;
}

$statements = preg_split('/;\s*\n/', implode("\n", $clean));
$errors = [];
foreach ($statements as $stmt) {
    $stmt = trim($stmt);
    if ($stmt === '') continue;
    try {
        $res = $db->query($stmt);
        if ($res === false) {
            $errors[] = "(" . $db->errno . ") " . $db->error . " -- Statement: " . substr($stmt,0,200);
        }
    } catch (Exception $e) {
        $errors[] = $e->getMessage() . " -- Statement: " . substr($stmt,0,200);
    }
}

if (empty($errors)) {
    echo "Migraciones aplicadas (sin errores fatales).\n";
} else {
    echo "Migración completada con advertencias:\n";
    foreach ($errors as $err) echo " - " . $err . "\n";
}

?>
