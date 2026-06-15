<?php require __DIR__ . '/../layout/header.php'; ?>
<h4>Editar permiso</h4>
<form method="post" action="?c=permiso&a=edit&id=<?= e($perm['id']) ?>">
  <?= csrf_input() ?>
  <div class="mb-3"><label>Clave</label><input class="form-control" name="clave" value="<?= e($perm['clave']) ?>" required></div>
  <div class="mb-3"><label>Descripción</label><input class="form-control" name="descripcion" value="<?= e($perm['descripcion']) ?>"></div>
  <button class="btn btn-primary">Actualizar</button>
</form>
<?php require __DIR__ . '/../layout/footer.php'; ?>
