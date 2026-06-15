<?php
require_once __DIR__ . '/../Model.php';
require_once __DIR__ . '/../config.php';

class RoleController
{
    public function index()
    {
        require_permission('role_view');
        $m = new Model();
        $res = $m->query('SELECT id,nombre FROM roles ORDER BY id');
        $roles = [];
        while ($r = $res->fetch_assoc()) $roles[] = $r;
        require __DIR__ . '/../../views/roles/list.php';
    }

    public function create()
    {
        require_permission('role_create');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $m = new Model();
            $m->query('INSERT INTO roles (nombre) VALUES (?)', 's', [$nombre]);
            header('Location: ../public/?c=role');
            exit;
        }
        require __DIR__ . '/../../views/roles/create.php';
    }

    public function permissions()
    {
        require_permission('role_assign');
        $role_id = $_GET['id'] ?? null;
        $m = new Model();
        $all = [];
        $res = $m->query('SELECT id,clave,descripcion FROM permisos ORDER BY id');
        while ($r = $res->fetch_assoc()) $all[] = $r;

        $assigned = [];
        if ($role_id) {
            $res2 = $m->query('SELECT permiso_id FROM roles_permisos WHERE role_id = ?', 'i', [$role_id]);
            while ($r = $res2->fetch_assoc()) $assigned[] = $r['permiso_id'];
        }

        require __DIR__ . '/../../views/roles/permissions.php';
    }

    public function savePermissions()
    {
        require_permission('role_assign');
        $role_id = $_POST['role_id'] ?? null;
        $perms = $_POST['permisos'] ?? [];
        $m = new Model();
        $m->query('DELETE FROM roles_permisos WHERE role_id = ?', 'i', [$role_id]);
        foreach ($perms as $p) {
            $m->query('INSERT INTO roles_permisos (role_id,permiso_id) VALUES (?,?)', 'ii', [$role_id,$p]);
        }
        header('Location: ../public/?c=role');
        exit;
    }
}
