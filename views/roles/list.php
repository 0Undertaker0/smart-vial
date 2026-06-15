<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="d-flex justify-content-between mb-3">
  <h4>Roles</h4>
  <?php if (user_has_permission('role_create')): ?><a class="btn btn-success" href="?c=role&a=create">Crear rol</a><?php endif; ?>
</div>
<table class="table table-bordered">
  <thead><tr><th>ID</th><th>Nombre</th><th>Permisos</th></tr></thead>
  <tbody>
    <?php foreach($roles as $r): ?>
    <tr>
      <td><?= e($r['id']) ?></td>
      <td><?= e($r['nombre']) ?></td>
      <td><?php if (user_has_permission('role_assign')): ?><a class="btn btn-sm btn-primary" href="?c=role&a=permissions&id=<?= $r['id'] ?>">Asignar permisos</a><?php endif; ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php require __DIR__ . '/../layout/footer.php'; ?>
