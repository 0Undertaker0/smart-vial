<?php
require_once __DIR__ . '/../Model.php';
require_once __DIR__ . '/../config.php';

class UserController
{
    public function index()
    {
        require_permission('user_view');
        $m = new Model();
        $res = $m->query('SELECT u.id, u.nombre, u.email, u.activo, r.nombre as role_name, u.role_id FROM usuarios u LEFT JOIN roles r ON u.role_id = r.id ORDER BY u.id DESC');
        $users = [];
        while ($r = $res->fetch_assoc()) $users[] = $r;
        require __DIR__ . '/../../views/users/list.php';
    }

    public function create()
    {
        require_permission('user_create');
        $m = new Model();
        $roles = [];
        $res = $m->query('SELECT id,nombre FROM roles ORDER BY id');
        while ($rr = $res->fetch_assoc()) $roles[] = $rr;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf();
            $nombre = $_POST['nombre'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role_id = (int)($_POST['role_id'] ?? 2);
            $m->query('INSERT INTO usuarios (nombre,email,password,activo,role_id) VALUES (?,?,?,?,?)', 'sssii', [$nombre,$email,$password,1,$role_id]);
            header('Location: ?c=user');
            exit;
        }
        require __DIR__ . '/../../views/users/create.php';
    }

    public function delete()
    {
        require_permission('user_delete');
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') require_csrf();
        if ($id) {
            $m = new Model();
            // Soft delete
            $m->query('UPDATE usuarios SET activo = 0 WHERE id = ?', 'i', [$id]);
        }
        header('Location: ?c=user');
    }

    public function edit()
    {
        require_permission('user_edit');
        $m = new Model();
        $id = (int)($_GET['id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf();
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $role_id = (int)($_POST['role_id'] ?? 2);
            if (!empty($_POST['password'])) {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $m->query('UPDATE usuarios SET nombre=?, email=?, password=?, role_id=? WHERE id=?', 'sssii', [$nombre,$email,$password,$role_id,$id]);
            } else {
                $m->query('UPDATE usuarios SET nombre=?, email=?, role_id=? WHERE id=?', 'ssii', [$nombre,$email,$role_id,$id]);
            }
            header('Location: ?c=user');
            exit;
        }

        $res = $m->query('SELECT id,nombre,email,role_id,activo FROM usuarios WHERE id=? LIMIT 1', 'i', [$id]);
        $user = $res->fetch_assoc();
        $roles = [];
        $r2 = $m->query('SELECT id,nombre FROM roles ORDER BY id');
        while ($rr = $r2->fetch_assoc()) $roles[] = $rr;
        require __DIR__ . '/../../views/users/edit.php';
    }
}
