<?php
require_once __DIR__ . '/../app/config.php';
$db = getDb();
$res = $db->query("SELECT LOWER(TRIM(gravedad)) as g, COUNT(*) as c FROM incidentes GROUP BY LOWER(TRIM(gravedad)) ORDER BY c DESC");
if (!$res) {
    echo "Error ejecutando consulta: " . $db->error . "\n";
    exit(1);
}
while ($r = $res->fetch_assoc()) {
    echo $r['g'] . ' => ' . $r['c'] . PHP_EOL;
}
?>
