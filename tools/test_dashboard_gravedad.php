<?php
require_once __DIR__ . '/../app/config.php';
$db = getDb();

function cnt_total($db) {
    $r = $db->query("SELECT COUNT(*) FROM incidentes WHERE activo = 1");
    $row = $r->fetch_row();
    return (int)($row[0] ?? 0);
}

function cnt_gravedad($db, $g) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM incidentes WHERE activo = 1 AND gravedad = ?");
    $stmt->bind_param('s', $g);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_row();
    return (int)($row[0] ?? 0);
}

$gravities = ['baja','media','alta','fatal'];

echo "Obteniendo conteos iniciales...\n";
$before_total = cnt_total($db);
$before = [];
foreach ($gravities as $g) $before[$g] = cnt_gravedad($db, $g);

print_r(['total_before' => $before_total, 'by_gravedad_before' => $before]);

$ts = date('YmdHis');
$inserted = [];
$user_id = 1;
$lat0 = 13.3444; $lng0 = -88.4392;

$insertStmt = $db->prepare('INSERT INTO incidentes (titulo,descripcion,lat,lng,direccion,gravedad,user_id,fecha,activo) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 1)');
if (!$insertStmt) {
    echo "ERROR: prepare failed: " . $db->error . "\n"; exit(1);
}

$idx = 0;
foreach ($gravities as $g) {
    $title = "TEST_AUTOMATION_{$ts}_{$g}";
    $desc = "Prueba automatizada {$g} {$ts}";
    $lat = $lat0 + ($idx * 0.00123);
    $lng = $lng0 + ($idx * 0.00123);
    $direccion = "Direccion prueba {$g} {$ts}";
    $idx++;

    $insertStmt->bind_param('ssddssi', $title, $desc, $lat, $lng, $direccion, $g, $user_id);
    $ok = $insertStmt->execute();
    if (!$ok) {
        echo "ERROR al insertar gravedad {$g}: " . $insertStmt->error . "\n";
    } else {
        $inserted[] = $db->insert_id;
        echo "Insertado ID={$db->insert_id} gravedad={$g}\n";
    }
}

if (empty($inserted)) { echo "No se insertó ningún registro, abortando.\n"; exit(1); }

echo "Verificando conteos después de inserciones...\n";
$after_total = cnt_total($db);
$after = [];
foreach ($gravities as $g) $after[$g] = cnt_gravedad($db, $g);

print_r(['total_after' => $after_total, 'by_gravedad_after' => $after]);

$expected_total_increase = count($inserted);
$actual_total_increase = $after_total - $before_total;

$ok = true;
if ($actual_total_increase !== $expected_total_increase) {
    echo "FALLA: total incidents increase expected={$expected_total_increase} actual={$actual_total_increase}\n";
    $ok = false;
} else {
    echo "OK: total incidents increased by {$actual_total_increase}\n";
}

foreach ($gravities as $g) {
    $delta = $after[$g] - $before[$g];
    if ($delta !== 1) {
        echo "FALLA: gravedad={$g} expected +1 got +{$delta}\n";
        $ok = false;
    } else {
        echo "OK: gravedad={$g} +1\n";
    }
}

// Verificar que los registros insertados tengan lat/lng/direccion
$ids_list = implode(',', array_map('intval', $inserted));
$res = $db->query("SELECT id, lat, lng, direccion FROM incidentes WHERE id IN ({$ids_list})");
$rows = [];
while ($r = $res->fetch_assoc()) $rows[] = $r;

echo "Registros insertados (id, lat, lng, direccion):\n";
print_r($rows);

// Limpieza: marcar como inactivos los registros creados
$del = $db->query("UPDATE incidentes SET activo = 0 WHERE id IN ({$ids_list})");
if ($del) {
    echo "Limpieza: marcados como inactivos los IDs: {$ids_list}\n";
} else {
    echo "Limpieza FALLIDA: " . $db->error . "\n";
}

// Verificar que los conteos volvieron
$final_total = cnt_total($db);
$final = [];
foreach ($gravities as $g) $final[$g] = cnt_gravedad($db, $g);

print_r(['total_final' => $final_total, 'by_gravedad_final' => $final]);

if ($ok) {
    echo "PRUEBAS COMPLETADAS: OK\n"; exit(0);
} else {
    echo "PRUEBAS COMPLETADAS: FALLARON\n"; exit(2);
}
