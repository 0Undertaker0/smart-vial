<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>SMARTVIAL</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="?c=dashboard">SMARTVIAL</a>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if (!empty($_SESSION['user'])): ?>
          <?php if (user_has_permission('incident_view')): ?><li class="nav-item"><a class="nav-link" href="?c=incident">Incidentes</a></li><?php endif; ?>

          <?php if (user_has_permission('user_view') || user_has_permission('role_view') || user_has_permission('permiso_view')): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="adminMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">Administración</a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminMenu">
              <?php if (user_has_permission('user_view')): ?><li><a class="dropdown-item" href="?c=user">Usuarios</a></li><?php endif; ?>
              <?php if (user_has_permission('role_view')): ?><li><a class="dropdown-item" href="?c=role">Roles</a></li><?php endif; ?>
              <?php if (user_has_permission('permiso_view')): ?><li><a class="dropdown-item" href="?c=permiso">Permisos</a></li><?php endif; ?>
            </ul>
          </li>
          <?php endif; ?>

          <li class="nav-item"><a class="nav-link" href="?c=auth&a=logout">Logout</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-4">
