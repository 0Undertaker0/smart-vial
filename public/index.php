<?php
require_once __DIR__ . '/../app/config.php';

$c = $_GET['c'] ?? 'auth';
$a = $_GET['a'] ?? null;

// Basic router
switch ($c) {
    case 'auth':
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        $ctl = new AuthController();
        if ($a === 'logout') $ctl->logout(); else $ctl->login();
        break;
        
    case 'dashboard':
        require_once __DIR__ . '/../app/controllers/DashboardController.php';
        $ctl = new DashboardController();
        $ctl->index();
        break;
        
    case 'role':
        require_once __DIR__ . '/../app/controllers/RoleController.php';
        $ctl = new RoleController();
        if ($a === 'create') $ctl->create(); elseif ($a === 'permissions') $ctl->permissions(); elseif ($a === 'savePermissions') $ctl->savePermissions(); else $ctl->index();
        break;
        
    case 'permiso':
        require_once __DIR__ . '/../app/controllers/PermisoController.php';
        $ctl = new PermisoController();
        if ($a === 'create') $ctl->create(); elseif ($a === 'edit') $ctl->edit(); elseif ($a === 'delete') $ctl->delete(); else $ctl->index();
        break;
        
    case 'user':
        require_once __DIR__ . '/../app/controllers/UserController.php';
        $ctl = new UserController();
        if ($a === 'create') $ctl->create(); elseif ($a === 'edit') $ctl->edit(); elseif ($a === 'delete') $ctl->delete(); else $ctl->index();
        break;
        
    case 'incident':
        require_once __DIR__ . '/../app/controllers/IncidentController.php';
        $ctl = new IncidentController();
        if ($a === 'create') $ctl->create(); else $ctl->index();
        break;
        
    // NUEVA RUTA: Módulo Ciudadano (Independiente)
    case 'ciudadano':
        require_once __DIR__ . '/../app/controllers/CiudadanoController.php';
        $ctl = new CiudadanoController();
        $ctl->index();
        break;
        
    default:
        echo 'RUTA NO ENCONTRADA';
}