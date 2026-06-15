<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="d-flex justify-content-between mb-3">
  <h4>Permisos</h4>
  <?php if (user_has_permission('permiso_create')): ?><a class="btn btn-success" href="?c=permiso&a=create">Crear permiso</a><?php endif; ?>
</div>
<table class="table table-sm">
  <thead><tr><th>ID</th><th>Clave</th><th>Descripción</th><th></th></tr></thead>
  <tbody>
    <?php foreach($perms as $p): ?>
    <tr>
      <td><?= e($p['id']) ?></td>
      <td><?= e($p['clave']) ?></td>
      <td><?= e($p['descripcion']) ?></td>
      <td>
        <?php if (user_has_permission('permiso_edit')): ?><a class="btn btn-sm btn-primary" href="?c=permiso&a=edit&id=<?= $p['id'] ?>">Editar</a><?php endif; ?>
        <?php if (user_has_permission('permiso_delete')): ?>
          <form method="post" action="?c=permiso&a=delete" style="display:inline" onsubmit="return confirm('Eliminar permiso?')">
            <?= csrf_input() ?>
            <input type="hidden" name="id" value="<?= $p['id'] ?>">
            <button class="btn btn-sm btn-danger">Eliminar</button>
          </form>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php require __DIR__ . '/../layout/footer.php'; ?>
