<?php
require_once __DIR__ . '/../Model.php';
require_once __DIR__ . '/../config.php';

class PermisoController
{
    public function index()
    {
        require_permission('permiso_view');
        $m = new Model();
        $res = $m->query('SELECT id,clave,descripcion FROM permisos ORDER BY id');
        $perms = [];
        while ($r = $res->fetch_assoc()) $perms[] = $r;
        require __DIR__ . '/../../views/permisos/list.php';
    }

    public function create()
    {
        require_permission('permiso_create');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clave = $_POST['clave'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $m = new Model();
            $m->query('INSERT INTO permisos (clave,descripcion) VALUES (?,?)', 'ss', [$clave,$descripcion]);
            header('Location: ../public/?c=permiso');
            exit;
        }
        require __DIR__ . '/../../views/permisos/create.php';
    }

    public function edit()
    {
        require_permission('permiso_edit');
        $id = $_GET['id'] ?? null;
        $m = new Model();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clave = $_POST['clave'];
            $descripcion = $_POST['descripcion'];
            $m->query('UPDATE permisos SET clave=?, descripcion=? WHERE id=?', 'ssi', [$clave,$descripcion,$id]);
            header('Location: ../public/?c=permiso');
            exit;
        }
        $res = $m->query('SELECT id,clave,descripcion FROM permisos WHERE id=? LIMIT 1', 'i', [$id]);
        $perm = $res->fetch_assoc();
        require __DIR__ . '/../../views/permisos/edit.php';
    }

    public function delete()
    {
        require_permission('permiso_delete');
        $id = $_GET['id'] ?? null;
        if ($id) {
            $m = new Model();
            $m->query('DELETE FROM permisos WHERE id=?', 'i', [$id]);
        }
        header('Location: ../public/?c=permiso');
    }
}
