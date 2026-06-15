<?php require __DIR__ . '/../layout/header.php'; ?>
<h4>Registrar incidente</h4>
<form method="post" action="" enctype="multipart/form-data">
  <?= csrf_input() ?>
  <div class="mb-3"><label>Título</label><input class="form-control" name="titulo" required></div>
  <div class="mb-3"><label>Descripción</label><textarea class="form-control" name="descripcion" rows="4"></textarea></div>
  <div class="mb-3"><label>Gravedad</label>
    <select class="form-select" name="gravedad"><option>alta</option><option selected>media</option><option>baja</option></select>
  </div>
  <div class="mb-3">
    <label>Fotografías</label>
    <input class="form-control" type="file" name="foto[]" accept="image/*" multiple>
  </div>
  <div class="mb-3">
    <label>Coordenadas</label>
    <div class="input-group">
      <input class="form-control" name="lat" id="lat" placeholder="Lat">
      <input class="form-control" name="lng" id="lng" placeholder="Lng">
      <button class="btn btn-outline-secondary" type="button" id="getLocation">Usar GPS</button>
    </div>
  </div>
  <button class="btn btn-primary">Registrar</button>
</form>
<?php require __DIR__ . '/../layout/footer.php'; ?>
