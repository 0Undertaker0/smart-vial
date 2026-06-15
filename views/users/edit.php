<?php require __DIR__ . '/../layout/header.php'; ?>
<h4>Editar usuario</h4>
<form method="post" action="?c=user&a=edit&id=<?= e($user['id']) ?>">
  <?= csrf_input() ?>
  <div class="mb-3"><label>Nombre</label><input class="form-control" name="nombre" value="<?= e($user['nombre']) ?>" required></div>
  <div class="mb-3"><label>Email</label><input class="form-control" name="email" type="email" value="<?= e($user['email']) ?>" required></div>
  <div class="mb-3"><label>Contraseña (dejar vacío para no cambiar)</label><input class="form-control" name="password" type="password"></div>
  <div class="mb-3"><label>Rol</label>
    <select class="form-select" name="role_id">
      <?php foreach($roles as $r): ?>
        <option value="<?= $r['id'] ?>" <?= ($r['id'] == ($user['role_id'] ?? 0)) ? 'selected' : '' ?>><?= e($r['nombre']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <button class="btn btn-primary">Actualizar</button>
</form>
<?php require __DIR__ . '/../layout/footer.php'; ?>
