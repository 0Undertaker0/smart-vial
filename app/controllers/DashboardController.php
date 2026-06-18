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
     * Ejecuta las consultas requeridas incluyendo la del Mapa.
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
                // NUEVO: Ejecuta la consulta para obtener marcadores del mapa
                'jsonMapa' => $this->obtenerDatosMapa(),
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
                'jsonMapa'        => '[]', // Seguro anti-fallos en JS
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

    /**
     * NUEVO: Consulta exclusiva para alimentar el mapa de Leaflet.
     * Ignora cualquier incidente que no tenga coordenadas válidas.
     */
    private function obtenerDatosMapa(): string
    {
        $sql = "SELECT id, titulo, fecha, gravedad, lat, lng 
                FROM incidentes 
                WHERE activo = 1 
                  AND lat IS NOT NULL 
                  AND lng IS NOT NULL";
                  
        $res = $this->model->query($sql);
        $data = [];
        
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                // Doble validación: solo se añaden si son números reales
                if (is_numeric($row['lat']) && is_numeric($row['lng'])) {
                    $data[] = $row;
                }
            }
        }
        
        return json_encode($data);
    }
}