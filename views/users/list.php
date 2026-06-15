<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="d-flex justify-content-between mb-3">
  <h4>Usuarios</h4>
  <?php if (user_has_permission('user_create')): ?>
    <a class="btn btn-success" href="?c=user&a=create">Crear usuario</a>
  <?php endif; ?>
</div>
<table class="table table-striped">
  <thead><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Activo</th><th></th></tr></thead>
  <tbody>
    <?php foreach($users as $u): ?>
    <tr>
      <td><?= e($u['id']) ?></td>
      <td><?= e($u['nombre']) ?></td>
      <td><?= e($u['email']) ?></td>
      <td><?= e($u['activo']) ?></td>
      <td><?php if (user_has_permission('user_delete')): ?><a class="btn btn-sm btn-danger" href="?c=user&a=delete&id=<?= $u['id'] ?>">Eliminar</a><?php endif; ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php require __DIR__ . '/../layout/footer.php'; ?>
