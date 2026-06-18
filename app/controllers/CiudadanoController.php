<?php
/**
 * CiudadanoController.php
 * Controlador público del Portal Ciudadano de SMARTVIAL.
 * Independiente del panel administrativo, de solo lectura.
 */
require_once __DIR__ . '/../Model.php';

class CiudadanoController
{
    public function index()
    {
        $m = new Model();
        $incidente = null;
        $error = null;
        $busqueda = '';

        // Procesar la búsqueda si el ciudadano ingresa un número de expediente (ID)
        if (isset($_GET['expediente']) && trim($_GET['expediente']) !== '') {
            $busqueda = trim($_GET['expediente']);
            
            // Validar que el valor ingresado sea numérico (ID)
            if (is_numeric($busqueda)) {
                // Verificar qué columnas opcionales existen para mantener retrocompatibilidad
                $check = $m->db->query("SHOW COLUMNS FROM incidentes");
                $existing = [];
                if ($check) {
                    while ($c = $check->fetch_assoc()) {
                        $existing[] = $c['Field'];
                    }
                }
                $has = function($name) use ($existing) { return in_array($name, $existing); };

                // Construir las partes del SELECT (solo lectura)
                $selectParts = ['id', 'fecha', 'gravedad'];
                if ($has('tipo_evento')) $selectParts[] = 'tipo_evento'; else $selectParts[] = 'NULL as tipo_evento';
                if ($has('estado_tramite')) $selectParts[] = 'estado_tramite'; else $selectParts[] = 'NULL as estado_tramite';

                $sql = "SELECT " . implode(', ', $selectParts) . " FROM incidentes WHERE id = ? AND activo = 1 LIMIT 1";
                
                $res = $m->query($sql, 'i', [(int)$busqueda]);
                $incidente = $res->fetch_assoc();

                if (!$incidente) {
                    $error = 'No se encontró ningún incidente con el número de expediente proporcionado.';
                }
            } else {
                $error = 'El número de expediente debe tener un formato numérico válido.';
            }
        }

        // Renderizar la vista ciudadana (aislada del header/footer administrativo)
        require __DIR__ . '/../../views/ciudadano/index.php';
    }
}