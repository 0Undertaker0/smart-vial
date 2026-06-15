<?php
require_once __DIR__ . '/../Model.php';
require_once __DIR__ . '/../config.php';

class UserController
{
    public function index()
    {
        require_permission('user_view');
        $m = new Model();
        $res = $m->query('SELECT id, nombre, email, activo FROM usuarios ORDER BY id DESC');
        $users = [];
        while ($r = $res->fetch_assoc()) $users[] = $r;
        require __DIR__ . '/../../views/users/list.php';
    }

    public function create()
    {
        require_permission('user_create');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $m = new Model();
            $m->query('INSERT INTO usuarios (nombre,email,password,activo,role_id) VALUES (?,?,?,1,2)', 'sss', [$nombre,$email,$password]);
            header('Location: ../public/?c=user');
            exit;
        }
        require __DIR__ . '/../../views/users/create.php';
    }

    public function delete()
    {
        require_permission('user_delete');
        $id = $_GET['id'] ?? null;
        if ($id) {
            $m = new Model();
            // Soft delete
            $m->query('UPDATE usuarios SET activo = 0 WHERE id = ?', 'i', [$id]);
        }
        header('Location: ../public/?c=user');
    }
}
