<?php
/**
 * DashboardController.php
 * Controlador del Dashboard Ejecutivo de SMARTVIAL.
 *
 * Responsabilidad única: obtener los conteos de incidentes desde MySQL
 * (vía Model::query / mysqli) y enviarlos a views/dashboard/admin.php.
 *
 * NO modifica login, roles, permisos ni ningún otro módulo existente.
 * Reutiliza Model.php y getDb()/require_login() ya definidos en config.php.
 *
 * SUPUESTOS A VERIFICAR (ajustar solo si no coinciden con tus datos reales):
 *  1. Se filtra activo = 1 en todas las tarjetas (asumiendo que así se excluyen
 *     incidentes eliminados/desactivados en el resto del sistema).
 *  2. "Cerrados" se calcula con estado_tramite = 'cerrado'. Si el valor real
 *     guardado es otro (ej. 'Finalizado', 'Archivado'), cambia esa línea.
 *     Tip: SELECT DISTINCT estado_tramite FROM incidentes; para confirmar.
 *  3. gravedad usa 'leve' / 'grave' / 'fatal'. La comparación es insensible a
 *     mayúsculas porque la tabla usa collation utf8mb4_unicode_ci.
 */

require_once __DIR__ . '/../Model.php';

class DashboardController
{
    private Model $model;

    public function __construct()
    {
        $this->model = new Model();
    }

    /**
     * Carga los KPIs del dashboard y renderiza views/dashboard/admin.php
     */
    public function index(): void
    {
        require_login();

        $data = $this->obtenerKpis();
        extract($data);
        require __DIR__ . '/../../views/dashboard/admin.php';
    }

    /**
     * Ejecuta las 6 consultas de conteo requeridas.
     * Si falla alguna consulta, retorna '--' en cada tarjeta en lugar de
     * interrumpir la carga de la vista.
     */
    private function obtenerKpis(): array
    {
        try {
            return [
                'totalIncidentes' => $this->contar("
                    SELECT COUNT(*) FROM incidentes WHERE activo = 1
                "),
                'totalLeves' => $this->contar("
                    SELECT COUNT(*) FROM incidentes
                    WHERE activo = 1 AND gravedad = 'leve'
                "),
                'totalGraves' => $this->contar("
                    SELECT COUNT(*) FROM incidentes
                    WHERE activo = 1 AND gravedad = 'grave'
                "),
                'totalFatales' => $this->contar("
                    SELECT COUNT(*) FROM incidentes
                    WHERE activo = 1 AND gravedad = 'fatal'
                "),
                'totalEsteMes' => $this->contar("
                    SELECT COUNT(*) FROM incidentes
                    WHERE activo = 1
                      AND MONTH(fecha) = MONTH(CURDATE())
                      AND YEAR(fecha) = YEAR(CURDATE())
                "),
                'totalCerrados' => $this->contar("
                    SELECT COUNT(*) FROM incidentes
                    WHERE activo = 1 AND estado_tramite = 'cerrado'
                "),
                'error' => null,
            ];
        } catch (Exception $e) {
            error_log('DashboardController::obtenerKpis - ' . $e->getMessage());

            return [
                'totalIncidentes' => '--',
                'totalLeves'      => '--',
                'totalGraves'     => '--',
                'totalFatales'    => '--',
                'totalEsteMes'    => '--',
                'totalCerrados'   => '--',
                'error'           => 'No se pudo cargar la información del dashboard.',
            ];
        }
    }

    /**
     * Ejecuta un COUNT(*) vía Model::query() (mysqli) y retorna el entero.
     */
    private function contar(string $sql): int
    {
        $res = $this->model->query($sql);
        $row = $res->fetch_row();
        return (int) ($row[0] ?? 0);
    }
}