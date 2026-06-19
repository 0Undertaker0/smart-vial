<?php
require_once __DIR__ . '/../Model.php';
require_once __DIR__ . '/../config.php';

class IncidentController
{
    public function index()
    {
        require_permission('incident_view');
        $m = new Model();

        // Detect AJAX requests
        $isAjax = (isset($_GET['ajax']) && $_GET['ajax'] == '1') || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

        // Persistir filtros en la sesión del usuario para mantenerlos entre vistas
        $filterKeys = ['departamento','municipio','zona','fecha_inicio','fecha_fin','gravedad','responsable_agente_id','responsable_unidad','estado_tramite','tipo_evento'];
        $storedFilters = $_SESSION['incident_filters'] ?? [];
        $hasAnyGetFilters = false;
        foreach ($filterKeys as $fk) { if (isset($_GET[$fk])) { $hasAnyGetFilters = true; break; } }

        // If clear requested, remove stored filters
        if (isset($_GET['clear_filters']) && ($_GET['clear_filters'] == '1' || $_GET['clear_filters'] === 1)) {
            unset($_SESSION['incident_filters']);
            $storedFilters = [];
        }

        // Build applied filters: GET overrides stored session values
        $filters = [];
        foreach ($filterKeys as $fk) {
            if (isset($_GET[$fk]) && $_GET[$fk] !== '') $filters[$fk] = $_GET[$fk];
            elseif (isset($storedFilters[$fk]) && $storedFilters[$fk] !== '') $filters[$fk] = $storedFilters[$fk];
            else $filters[$fk] = null;
        }

        // Update session if user provided filters (including AJAX apply)
        if ($hasAnyGetFilters) {
            $_SESSION['incident_filters'] = $filters;
        }

        // Check which optional columns exist to keep backward-compatibility
        $cols = [];
        $check = $m->db->query("SHOW COLUMNS FROM incidentes");
        $existing = [];
        if ($check) {
            while ($c = $check->fetch_assoc()) $existing[] = $c['Field'];
        }
        $has = function($name) use ($existing) { return in_array($name, $existing); };

        // Build select list with graceful NULLs if column missing
        $selectParts = [
            'i.id','i.titulo','i.fecha','i.gravedad','u.nombre as reportante'
        ];
        $joins = ['LEFT JOIN usuarios u ON i.user_id = u.id'];
        if ($has('departamento')) $selectParts[] = 'i.departamento'; else $selectParts[] = "NULL as departamento";
        if ($has('municipio')) $selectParts[] = 'i.municipio'; else $selectParts[] = "NULL as municipio";
        if ($has('zona')) $selectParts[] = 'i.zona'; else $selectParts[] = "NULL as zona";
        if ($has('tipo_evento')) $selectParts[] = 'i.tipo_evento'; else $selectParts[] = "NULL as tipo_evento";
        if ($has('estado_tramite')) $selectParts[] = 'i.estado_tramite'; else $selectParts[] = "NULL as estado_tramite";
        if ($has('responsable_agente_id')) {
            $selectParts[] = 'ra.nombre as responsable';
            $joins[] = 'LEFT JOIN usuarios ra ON ra.id = i.responsable_agente_id';
        } else {
            $selectParts[] = "NULL as responsable";
        }
        if ($has('responsable_unidad')) $selectParts[] = 'i.responsable_unidad'; else $selectParts[] = "NULL as responsable_unidad";

        $sql = 'SELECT ' . implode(',', $selectParts) . ' FROM incidentes i ' . implode(' ', $joins);

        // Build dynamic WHERE
        $conds = ['i.activo = 1'];
        $types = '';
        $params = [];

        // Filters (applied from GET or session)
        if ($has('departamento') && !empty($filters['departamento'])) { $conds[] = 'i.departamento = ?'; $types .= 's'; $params[] = $filters['departamento']; }
        if ($has('municipio') && !empty($filters['municipio'])) { $conds[] = 'i.municipio = ?'; $types .= 's'; $params[] = $filters['municipio']; }
        if ($has('zona') && !empty($filters['zona'])) { $conds[] = 'i.zona LIKE ?'; $types .= 's'; $params[] = '%' . $filters['zona'] . '%'; }
        if (!empty($filters['fecha_inicio'])) { $conds[] = 'i.fecha >= ?'; $types .= 's'; $params[] = $filters['fecha_inicio'] . ' 00:00:00'; }
        if (!empty($filters['fecha_fin'])) { $conds[] = 'i.fecha <= ?'; $types .= 's'; $params[] = $filters['fecha_fin'] . ' 23:59:59'; }
        if (!empty($filters['gravedad'])) { $conds[] = 'i.gravedad = ?'; $types .= 's'; $params[] = $filters['gravedad']; }
        if ($has('responsable_agente_id') && !empty($filters['responsable_agente_id'])) { $conds[] = 'i.responsable_agente_id = ?'; $types .= 'i'; $params[] = (int)$filters['responsable_agente_id']; }
        if ($has('responsable_unidad') && !empty($filters['responsable_unidad'])) { $conds[] = 'i.responsable_unidad = ?'; $types .= 's'; $params[] = $filters['responsable_unidad']; }
        if ($has('estado_tramite') && !empty($filters['estado_tramite'])) { $conds[] = 'i.estado_tramite = ?'; $types .= 's'; $params[] = $filters['estado_tramite']; }
        if ($has('tipo_evento') && !empty($filters['tipo_evento'])) { $conds[] = 'i.tipo_evento = ?'; $types .= 's'; $params[] = $filters['tipo_evento']; }

        $sql .= ' WHERE ' . implode(' AND ', $conds) . ' ORDER BY i.fecha DESC';

        // Execute
        $res = null;
        if ($types !== '') $res = $m->query($sql, $types, $params); else $res = $m->query($sql);
        $incidents = [];
        while ($r = $res->fetch_assoc()) $incidents[] = $r;

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode($incidents);
            exit;
        }

        // For non-AJAX render: prepare select lists (only if columns exist)
        $departamentos = [];
        if ($has('departamento')) {
            $rd = $m->query('SELECT DISTINCT departamento FROM incidentes WHERE departamento IS NOT NULL AND departamento<>""');
            while ($row = $rd->fetch_assoc()) $departamentos[] = $row['departamento'];
        }
        $municipios = [];
        if ($has('municipio')) {
            $rm = $m->query('SELECT DISTINCT municipio FROM incidentes WHERE municipio IS NOT NULL AND municipio<>""');
            while ($row = $rm->fetch_assoc()) $municipios[] = $row['municipio'];
        }
        $agentes = [];
        $ra = $m->query('SELECT id,nombre FROM usuarios WHERE role_id = 2');
        while ($row = $ra->fetch_assoc()) $agentes[] = $row;

        require __DIR__ . '/../../views/incidents/list.php';
    }

    public function create()
    {
        require_permission('incident_create');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf();
            $titulo = $_POST['titulo'];
            $descripcion = $_POST['descripcion'];
            $lat = $_POST['lat'] ?? null;
            $lng = $_POST['lng'] ?? null;
            $direccion = $_POST['direccion'] ?? null;
            $gravedad = $_POST['gravedad'] ?? 'media';
            $user_id = $_SESSION['user']['id'] ?? 0;
            $m = new Model();
            $m->query('INSERT INTO incidentes (titulo,descripcion,lat,lng,direccion,gravedad,user_id,fecha,activo) VALUES (?,?,?,?,?,?,? ,NOW(),1)', 'ssddssi', [$titulo,$descripcion,(float)$lat,(float)$lng,$direccion,$gravedad,(int)$user_id]);
            $last = $m->db->insert_id;
            
            // --- AUDITORÍA: 6. Creación de incidentes ---
            $m->query('INSERT INTO auditoria (usuario_id, accion, tabla_afectada, registro_id) VALUES (?, ?, ?, ?)', 'issi', [$user_id, 'Creación de incidente', 'incidentes', $last]);

            // Handle file uploads
            if (!empty($_FILES['foto']['name'][0])) {
                $uploads = __DIR__ . '/../../uploads/';
                if (!is_dir($uploads)) mkdir($uploads, 0755, true);
                $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                foreach ($_FILES['foto']['tmp_name'] as $k => $tmp) {
                    if (!is_uploaded_file($tmp)) continue;
                    $size = filesize($tmp);
                    if ($size > 5 * 1024 * 1024) continue; // skip >5MB
                    $mime = finfo_file($finfo, $tmp);
                    if (!in_array($mime, $allowed)) continue;
                    $ext = '';
                    switch ($mime) {
                        case 'image/jpeg': $ext = '.jpg'; break;
                        case 'image/png': $ext = '.png'; break;
                        case 'image/gif': $ext = '.gif'; break;
                        case 'image/webp': $ext = '.webp'; break;
                    }
                    $safeName = bin2hex(random_bytes(8)) . $ext;
                    move_uploaded_file($tmp, $uploads . $safeName);
                    $m->query('INSERT INTO fotografias (incidente_id,archivo,fecha) VALUES (?,?,NOW())', 'is', [$last,$safeName]);
                }
                finfo_close($finfo);
            }
            header('Location: ?c=incident');
            exit;
        }
        require __DIR__ . '/../../views/incidents/create.php';
    }

    public function view()
    {
        require_permission('incident_view');

        if (!isset($_GET['id']) || empty($_GET['id'])) {
            header('Location: ?c=incident');
            exit;
        }

        $id = (int)$_GET['id'];
        $m = new Model();

        // 1. Obtener detalles del incidente (verificando compatibilidad de columnas)
        $check = $m->db->query("SHOW COLUMNS FROM incidentes");
        $existing = [];
        if ($check) {
            while ($c = $check->fetch_assoc()) $existing[] = $c['Field'];
        }
        $has = function($name) use ($existing) { return in_array($name, $existing); };

        $selectParts = [
            'i.id', 'i.titulo', 'i.descripcion', 'i.fecha', 'i.gravedad', 'i.lat', 'i.lng', 'i.direccion', 'u.nombre as reportante'
        ];
        
        if ($has('departamento')) $selectParts[] = 'i.departamento'; else $selectParts[] = "NULL as departamento";
        if ($has('municipio')) $selectParts[] = 'i.municipio'; else $selectParts[] = "NULL as municipio";
        if ($has('tipo_evento')) $selectParts[] = 'i.tipo_evento'; else $selectParts[] = "NULL as tipo_evento";
        if ($has('estado_tramite')) $selectParts[] = 'i.estado_tramite'; else $selectParts[] = "NULL as estado_tramite";

        $sqlIncidente = 'SELECT ' . implode(',', $selectParts) . ' 
                         FROM incidentes i 
                         LEFT JOIN usuarios u ON i.user_id = u.id 
                         WHERE i.id = ? AND i.activo = 1';
        
        $resIncidente = $m->query($sqlIncidente, 'i', [$id]);
        $incidente = $resIncidente->fetch_assoc();

        if (!$incidente) {
            if (is_ajax_request()) {
                header('Content-Type: application/json', true, 404);
                echo json_encode(['error' => true, 'message' => 'Incidente no encontrado']);
                exit;
            }
            header('Location: ?c=incident');
            exit;
        }

        // If AJAX requested, return JSON for editing
        if (is_ajax_request()) {
            header('Content-Type: application/json');
            echo json_encode($incidente);
            exit;
        }

        // 2. Obtener las fotografías asociadas a este incidente
        $resFotos = $m->query('SELECT id, archivo, fecha FROM fotografias WHERE incidente_id = ? ORDER BY fecha DESC', 'i', [$id]);
        $fotografias = [];
        while ($f = $resFotos->fetch_assoc()) {
            $fotografias[] = $f;
        }

        // Renderizar la vista
        require __DIR__ . '/../../views/incidents/view.php';
    }

    /**
     * NUEVO MÉTODO: Edición de incidente con auditoría (Requisito 7)
     */
    public function edit()
    {
        require_permission('incident_edit');
        $id = (int)($_GET['id'] ?? 0);
        $m = new Model();
        $isAjax = is_ajax_request();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf();
            // Adaptar los campos según los disponibles en la vista real (edit.php)
            $titulo = $_POST['titulo'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $gravedad = $_POST['gravedad'] ?? 'media';
            $lat = $_POST['lat'] ?? null;
            $lng = $_POST['lng'] ?? null;
            $direccion = $_POST['direccion'] ?? null;

            $m->query('UPDATE incidentes SET titulo=?, descripcion=?, gravedad=?, lat=?, lng=?, direccion=? WHERE id=?', 'sssddsi', [$titulo, $descripcion, $gravedad, (float)$lat, (float)$lng, $direccion, $id]);

            // --- AUDITORÍA: 7. Modificación de incidentes ---
            $usuario_actual = $_SESSION['user']['id'] ?? 0;
            $m->query('INSERT INTO auditoria (usuario_id, accion, tabla_afectada, registro_id) VALUES (?, ?, ?, ?)', 'issi', [$usuario_actual, 'Modificación de incidente', 'incidentes', $id]);

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['ok' => true]);
                exit;
            }

            header('Location: ?c=incident');
            exit;
        }
        
        $res = $m->query('SELECT * FROM incidentes WHERE id = ? LIMIT 1', 'i', [$id]);
        $incidente = $res->fetch_assoc();
        require __DIR__ . '/../../views/incidents/edit.php';
    }

    /**
     * NUEVO MÉTODO: Eliminación de incidente con auditoría (Requisito 8)
     */
    public function delete()
    {
        require_permission('incident_delete');
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        $isAjax = is_ajax_request();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf();
        }
        
        $ok = false;
        if ($id) {
            $m = new Model();
            $m->query('UPDATE incidentes SET activo = 0 WHERE id = ?', 'i', [$id]);
            
            // --- AUDITORÍA: 8. Eliminación de incidentes ---
            $usuario_actual = $_SESSION['user']['id'] ?? 0;
            $m->query('INSERT INTO auditoria (usuario_id, accion, tabla_afectada, registro_id) VALUES (?, ?, ?, ?)', 'issi', [$usuario_actual, 'Eliminación de incidente', 'incidentes', $id]);
            $ok = true;
        }

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => $ok]);
            exit;
        }

        header('Location: ?c=incident');
        exit;
    }
}