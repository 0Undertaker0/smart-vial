<?php
require_once __DIR__ . '/../app/config.php';

$sqlDir = __DIR__ . '/../sql';
$files = glob($sqlDir . '/*.sql');
sort($files);

if (empty($files)) {
    echo "No hay archivos SQL en: $sqlDir\n";
    exit(0);
}

echo "Aplicando migraciones desde sql/ (" . count($files) . " archivos)\n";
$db = getDb();
$errors = [];

foreach ($files as $file) {
    echo "-> Ejecutando: " . basename($file) . "\n";
    $content = file_get_contents($file);
    $content = str_replace("\r\n", "\n", $content);
    $lines = explode("\n", $content);
    $clean = [];
    foreach ($lines as $ln) {
        $t = trim($ln);
        if ($t === '') continue;
        if (strpos($t, '--') === 0) continue;
        $clean[] = $ln;
    }
    $statements = preg_split('/;\s*\n/', implode("\n", $clean));
    foreach ($statements as $stmt) {
        $stmt = trim($stmt);
        if ($stmt === '') continue;
        try {
            $res = $db->query($stmt);
            if ($res === false) {
                $errors[] = "(" . $db->errno . ") " . $db->error . " -- File: " . basename($file) . " -- Statement: " . substr($stmt,0,200);
            }
        } catch (Exception $e) {
            $errors[] = $e->getMessage() . " -- File: " . basename($file) . " -- Statement: " . substr($stmt,0,200);
        }
    }
}

if (empty($errors)) {
    echo "Migraciones aplicadas (sin errores fatales).\n";
} else {
    echo "Migración completada con advertencias:\n";
    foreach ($errors as $err) echo " - " . $err . "\n";
}

?>
