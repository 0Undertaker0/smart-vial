<?php require __DIR__ . '/../layout/header.php'; ?>
<h4>Permisos para rol <?= e($_GET['id'] ?? '') ?></h4>
<form method="post" action="?c=role&a=savePermissions">
  <?= csrf_input() ?>
  <input type="hidden" name="role_id" value="<?= e($_GET['id'] ?? '') ?>">
  <div class="row">
    <?php foreach($all as $p): ?>
    <div class="col-md-4">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="permisos[]" value="<?= $p['id'] ?>" id="p<?= $p['id'] ?>" <?= in_array($p['id'],$assigned) ? 'checked' : '' ?>>
        <label class="form-check-label" for="p<?= $p['id'] ?>"><?= e($p['clave']) ?> - <?= e($p['descripcion']) ?></label>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <button class="btn btn-primary mt-3">Guardar permisos</button>
</form>
<?php require __DIR__ . '/../layout/footer.php'; ?>
