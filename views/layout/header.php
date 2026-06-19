<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>SMARTVIAL</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="?c=dashboard">SMARTVIAL</a>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if (!empty($_SESSION['user'])): ?>
          <?php
            $roleMenu = function_exists('role_allows_menu') ? 'role_allows_menu' : null;
          ?>
          <?php if (($roleMenu && role_allows_menu('dashboard')) || user_has_permission('dashboard_view') ): ?><li class="nav-item"><a class="nav-link" href="?c=dashboard">Dashboard</a></li><?php endif; ?>

          <?php if (user_has_permission('incident_view') || ($roleMenu && role_allows_menu('incident'))): ?><li class="nav-item"><a class="nav-link" href="?c=incident">Incidentes</a></li><?php endif; ?>

          <?php if (($roleMenu && role_allows_menu('ciudadano'))): ?><li class="nav-item"><a class="nav-link" href="?c=ciudadano">Portal Ciudadano</a></li><?php endif; ?>

          <?php if (user_has_permission('user_view') || user_has_permission('role_view') || user_has_permission('permiso_view') || ($roleMenu && role_allows_menu('admin'))): ?>
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
  <?php if (!empty(
    $_SESSION['flash']
  )): ?>
    <div class="alert alert-<?php echo e($_SESSION['flash']['type'] ?? 'warning'); ?> alert-dismissible fade show" role="alert">
      <?php echo e($_SESSION['flash']['message'] ?? ''); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>
