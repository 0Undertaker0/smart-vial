<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Iniciar sesión</h4>
        <?php if (!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <form method="post" action="">
          <div class="mb-3">
            <label>Email</label>
            <input class="form-control" name="email" type="email" required>
          </div>
          <div class="mb-3">
            <label>Contraseña</label>
            <input class="form-control" name="password" type="password" required>
          </div>
          <button class="btn btn-primary">Entrar</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
