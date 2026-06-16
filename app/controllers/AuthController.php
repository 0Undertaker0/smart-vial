<?php
require_once __DIR__ . '/../Model.php';

class AuthController
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $m = new Model();
            $res = $m->query('SELECT id, nombre, email, password, role_id FROM usuarios WHERE email = ? LIMIT 1', 's', [$email]);
            if ($row = $res->fetch_assoc()) {
                if (password_verify($password, $row['password'])) {
                    // Regenerate session id on login to prevent fixation
                        session_regenerate_id(true);
                        $_SESSION['user'] = ['id'=>$row['id'],'nombre'=>$row['nombre'],'email'=>$row['email'],'role_id'=>$row['role_id']];
                        $_SESSION['last_login'] = date('c');
                        header('Location: ?c=dashboard');
                    exit;
                }
            }
            $error = 'Credenciales inválidas';
            require __DIR__ . '/../../views/auth/login.php';
            return;
        }
        require __DIR__ . '/../../views/auth/login.php';
    }

    public function logout()
    {
        session_destroy();
        header('Location: ?c=auth');
        exit;
    }
}
