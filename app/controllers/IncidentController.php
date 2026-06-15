<?php
require_once __DIR__ . '/../Model.php';
require_once __DIR__ . '/../config.php';

class IncidentController
{
    public function index()
    {
        require_permission('incident_view');
        $m = new Model();
        $res = $m->query('SELECT i.id,i.titulo,i.fecha,i.gravedad,u.nombre as reportante FROM incidentes i LEFT JOIN usuarios u ON i.user_id = u.id ORDER BY i.fecha DESC');
        $incidents = [];
        while ($r = $res->fetch_assoc()) $incidents[] = $r;
        require __DIR__ . '/../../views/incidents/list.php';
    }

    public function create()
    {
        require_permission('incident_create');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = $_POST['titulo'];
            $descripcion = $_POST['descripcion'];
            $lat = $_POST['lat'] ?? null;
            $lng = $_POST['lng'] ?? null;
            $gravedad = $_POST['gravedad'] ?? 'media';
            $user_id = $_SESSION['user']['id'] ?? 0;
            $m = new Model();
            $m->query('INSERT INTO incidentes (titulo,descripcion,lat,lng,gravedad,user_id,fecha,activo) VALUES (?,?,?,?,?,? ,NOW(),1)', 'ssddsi', [$titulo,$descripcion,(float)$lat,(float)$lng,$gravedad,(int)$user_id]);
            $last = $m->db->insert_id;
            // Handle file uploads
            if (!empty($_FILES['foto']['name'][0])) {
                $uploads = __DIR__ . '/../../uploads/';
                if (!is_dir($uploads)) mkdir($uploads, 0755, true);
                foreach ($_FILES['foto']['tmp_name'] as $k => $tmp) {
                    $name = time() . '_' . basename($_FILES['foto']['name'][$k]);
                    move_uploaded_file($tmp, $uploads . $name);
                    $m->query('INSERT INTO fotografias (incidente_id,archivo,fecha) VALUES (?,?,NOW())', 'is', [$last,$name]);
                }
            }
            header('Location: ../public/?c=incident');
            exit;
        }
        require __DIR__ . '/../../views/incidents/create.php';
    }
}
