<?php require __DIR__ . '/../layout/header.php'; ?>
<h4>Crear usuario</h4>
<form method="post" action="">
  <div class="mb-3"><label>Nombre</label><input class="form-control" name="nombre" required></div>
  <div class="mb-3"><label>Email</label><input class="form-control" name="email" type="email" required></div>
  <div class="mb-3"><label>Contraseña</label><input class="form-control" name="password" type="password" required></div>
  <button class="btn btn-primary">Guardar</button>
</form>
<?php require __DIR__ . '/../layout/footer.php'; ?>
