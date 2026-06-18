<?php require __DIR__ . '/../layout/header.php'; ?>

<h4>Registrar incidente</h4>
<form method="post" action="" enctype="multipart/form-data">
  <?= csrf_input() ?>
  <div class="mb-3">
    <label>Título</label>
    <input class="form-control" name="titulo" required>
  </div>
  
  <div class="mb-3">
    <label>Descripción</label>
    <textarea class="form-control" name="descripcion" rows="4"></textarea>
  </div>
  
  <div class="mb-3">
    <label>Gravedad</label>
    <select class="form-select" name="gravedad">
      <option value="alta">alta</option>
      <option value="media" selected>media</option>
      <option value="baja">baja</option>
    </select>
  </div>
  
  <div class="mb-3">
    <label>Fotografías</label>
    <input class="form-control" type="file" name="foto[]" accept="image/*" multiple>
  </div>
  
  <div class="mb-3">
    <label>Coordenadas</label>
    <div class="input-group mb-2">
      <input class="form-control" name="lat" id="lat" placeholder="Latitud">
      <input class="form-control" name="lng" id="lng" placeholder="Longitud">
      <button class="btn btn-outline-secondary" type="button" onclick="obtenerUbicacion()">
        <i class="bi bi-geo-alt"></i> Usar GPS
      </button>
    </div>
    <div class="mb-2">
      <label class="form-label">Dirección</label>
      <input class="form-control" name="direccion" id="direccion" placeholder="Calle, Colonia, Ciudad" readonly>
    </div>
    <div id="mapContainer" style="width: 100%; height: 300px; border: 1px solid #ccc; border-radius: 5px; margin-bottom: 15px; display: none;">
      <div id="map" style="width: 100%; height: 100%;"></div>
    </div>
    <small class="text-muted d-block">Presiona "Usar GPS" para obtener tu ubicación actual en el mapa.</small>
  </div>
  
  <button class="btn btn-primary mt-2">Registrar</button>
</form>

<?php require __DIR__ . '/../layout/footer.php'; ?>