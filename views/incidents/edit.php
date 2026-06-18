<?php require __DIR__ . '/../layout/header.php'; ?>

<h4>Editar incidente #<?= htmlspecialchars((string)$incidente['id']) ?></h4>
<form method="post" action="?c=incident&a=edit&id=<?= (int)$incidente['id'] ?>" enctype="multipart/form-data">
  <?= csrf_input() ?>
  <div class="mb-3">
    <label>Título</label>
    <input class="form-control" name="titulo" required value="<?= htmlspecialchars((string)($incidente['titulo'] ?? '')) ?>">
  </div>
  
  <div class="mb-3">
    <label>Descripción</label>
    <textarea class="form-control" name="descripcion" rows="4"><?= htmlspecialchars((string)($incidente['descripcion'] ?? '')) ?></textarea>
  </div>
  
  <div class="mb-3">
    <label>Gravedad</label>
    <select class="form-select" name="gravedad">
      <option value="alta" <?= (isset($incidente['gravedad']) && $incidente['gravedad']==='alta')? 'selected':'' ?>>alta</option>
      <option value="media" <?= (isset($incidente['gravedad']) && $incidente['gravedad']==='media')? 'selected':'' ?>>media</option>
      <option value="baja" <?= (isset($incidente['gravedad']) && $incidente['gravedad']==='baja')? 'selected':'' ?>>baja</option>
    </select>
  </div>
  
  <div class="mb-3">
    <label>Fotografías (subir nuevas)</label>
    <input class="form-control" type="file" name="foto[]" accept="image/*" multiple>
  </div>
  
  <div class="mb-3">
    <label>Coordenadas</label>
    <div class="input-group mb-2">
      <input class="form-control" name="lat" id="lat" placeholder="Latitud" value="<?= htmlspecialchars((string)($incidente['lat'] ?? '')) ?>">
      <input class="form-control" name="lng" id="lng" placeholder="Longitud" value="<?= htmlspecialchars((string)($incidente['lng'] ?? '')) ?>">
      <button id="getLocation" class="btn btn-outline-secondary" type="button" onclick="obtenerUbicacion()">
        <i class="bi bi-geo-alt"></i> Usar GPS
      </button>
    </div>
    <div class="mb-2">
      <label class="form-label">Dirección</label>
      <input class="form-control" name="direccion" id="direccion" placeholder="Calle, Colonia, Ciudad" readonly value="<?= htmlspecialchars((string)($incidente['direccion'] ?? '')) ?>">
    </div>
    <div id="mapContainer" style="width: 100%; height: 300px; border: 1px solid #ccc; border-radius: 5px; margin-bottom: 15px; <?= (!empty($incidente['lat']) && !empty($incidente['lng'])) ? '' : 'display: none;' ?>">
      <div id="map" style="width: 100%; height: 100%;"></div>
    </div>
    <small class="text-muted d-block">Presiona "Usar GPS" para obtener tu ubicación actual en el mapa.</small>
  </div>
  
  <button class="btn btn-primary mt-2">Guardar cambios</button>
</form>

<?php require __DIR__ . '/../layout/footer.php'; ?>
