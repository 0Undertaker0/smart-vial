<?php
require_once __DIR__ . '/../Model.php';
require_once __DIR__ . '/../config.php';

class AuthController
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            require_csrf();

            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $m = new Model();
            $res = $m->query('SELECT u.id, u.nombre, u.email, u.password, u.role_id, r.nombre as role_name FROM usuarios u LEFT JOIN roles r ON u.role_id = r.id WHERE u.email = ? LIMIT 1', 's', [$email]);

            $authenticated = false;
            if ($row = $res->fetch_assoc()) {
                if (password_verify($password, $row['password'])) {
                    $authenticated = true;
                    // Regenerate session id on login to prevent fixation
                    session_regenerate_id(true);
                    $_SESSION['user'] = ['id'=>$row['id'],'nombre'=>$row['nombre'],'email'=>$row['email'],'role_id'=>$row['role_id'],'role_name'=>($row['role_name'] ?? '')];
                    $_SESSION['last_login'] = date('c');

                    // --- AUDITORÍA: 1. Inicio de sesión ---
                    $m->query('INSERT INTO auditoria (usuario_id, accion, tabla_afectada, registro_id) VALUES (?, ?, ?, ?)', 'issi', [$row['id'], 'Inicio de sesión', 'usuarios', $row['id']]);

                    header('Location: ?c=dashboard');
                    exit;
                }
            }

            // If we reach here, authentication failed
            // Clear any possible residual session user to avoid accidental auth state
            if (isset($_SESSION['user'])) unset($_SESSION['user']);

            // Log failed attempt for audit/debug (usuario_id = 0)
            try {
                $m->query('INSERT INTO auditoria (usuario_id, accion, tabla_afectada, registro_id) VALUES (?, ?, ?, ?)', 'issi', [0, 'Inicio de sesión fallido para ' . $email, 'usuarios', 0]);
            } catch (Exception $e) {
                // ignore audit failures
            }

            $error = 'Credenciales inválidas';
            require __DIR__ . '/../../views/auth/login.php';
            return;
        }
        require __DIR__ . '/../../views/auth/login.php';
    }

    public function logout()
    {
        // --- AUDITORÍA: 2. Cierre de sesión ---
        if (isset($_SESSION['user']['id'])) {
            $m = new Model();
            $m->query('INSERT INTO auditoria (usuario_id, accion, tabla_afectada, registro_id) VALUES (?, ?, ?, ?)', 'issi', [$_SESSION['user']['id'], 'Cierre de sesión', 'usuarios', $_SESSION['user']['id']]);
        }

        session_destroy();
        header('Location: ?c=auth');
        exit;
    }
}