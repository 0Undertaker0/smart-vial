<?php require __DIR__ . '/../layout/header.php'; ?>
<h4>Crear permiso</h4>
<form method="post" action="">
  <?= csrf_input() ?>
  <div class="mb-3"><label>Clave</label><input class="form-control" name="clave" required></div>
  <div class="mb-3"><label>Descripción</label><input class="form-control" name="descripcion"></div>
  <button class="btn btn-primary">Guardar</button>
</form>
<?php require __DIR__ . '/../layout/footer.php'; ?>
