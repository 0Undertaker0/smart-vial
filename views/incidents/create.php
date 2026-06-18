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
    <div class="input-group">
      <input class="form-control" name="lat" id="lat" placeholder="Latitud">
      <input class="form-control" name="lng" id="lng" placeholder="Longitud">
      <button class="btn btn-outline-secondary" type="button" id="getLocation">
        <i class="bi bi-geo-alt"></i> Usar GPS
      </button>
    </div>
    <small class="text-muted mt-1 d-block">Presiona "Usar GPS" y acepta los permisos de tu navegador para obtener tu ubicación exacta.</small>
  </div>
  
  <button class="btn btn-primary mt-2">Registrar</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnGps = document.getElementById('getLocation');
    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');

    if (btnGps) {
        btnGps.addEventListener('click', function() {
             console.log("CLICK GPS");// Validar soporte del navegador
            if (!navigator.geolocation) {
                alert("Tu navegador no soporta geolocalización.");
                return;
            }

            // Efecto de carga en el botón
            const originalText = btnGps.innerHTML;
            btnGps.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Buscando...';
            btnGps.disabled = true;

            // Solicitar ubicación
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    // Éxito: llenar los inputs
                    latInput.value = position.coords.latitude;
                    lngInput.value = position.coords.longitude;
                    
                    // Retroalimentación visual
                    btnGps.innerHTML = '<i class="bi bi-check-circle"></i> Ubicación capturada';
                    btnGps.classList.remove('btn-outline-secondary');
                    btnGps.classList.add('btn-success', 'text-white');
                    
                    // Restaurar botón después de unos segundos
                    setTimeout(() => {
                        btnGps.innerHTML = originalText;
                        btnGps.disabled = false;
                        btnGps.classList.remove('btn-success', 'text-white');
                        btnGps.classList.add('btn-outline-secondary');
                    }, 3000);
                },
                function(error) {
                    // Manejo de errores
                    let msg = "Error desconocido al obtener la ubicación.";
                    if (error.code === error.PERMISSION_DENIED) {
                        msg = "Permiso denegado. Debes permitir el acceso a tu ubicación en el navegador.";
                    } else if (error.code === error.POSITION_UNAVAILABLE) {
                        msg = "La información de ubicación no está disponible.";
                    } else if (error.code === error.TIMEOUT) {
                        msg = "Tiempo de espera agotado al conectar con el satélite GPS.";
                    }
                    alert(msg);
                    
                    // Restaurar botón
                    btnGps.innerHTML = originalText;
                    btnGps.disabled = false;
                },
                {
                    enableHighAccuracy: true, // Forzar la mejor precisión posible
                    timeout: 15000,
                    maximumAge: 0
                }
            );
        });
    }
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>