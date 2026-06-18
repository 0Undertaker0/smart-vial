// Manejar GPS
function setupGPS() {
  var btn = document.getElementById('getLocation');
  console.log("setupGPS ejecutada, botón:", btn);
  
  if (btn) {
    btn.addEventListener('click', function(e){
      e.preventDefault();
      console.log("✓ GPS Button clicked");
      
      if (!navigator.geolocation) {
        alert('Geolocalización no soportada en tu navegador');
        return;
      }
      
      var originalText = btn.innerHTML;
      var originalClass = btn.className;
      
      btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Obteniendo ubicación...';
      btn.disabled = true;
      btn.classList.remove('btn-outline-secondary', 'btn-success', 'text-white');
      btn.classList.add('btn-primary');
      
      navigator.geolocation.getCurrentPosition(
        function(pos){
          console.log("✓ Ubicación obtenida:", pos.coords.latitude, pos.coords.longitude);
          
          // Llenar los campos
          var latField = document.getElementById('lat');
          var lngField = document.getElementById('lng');
          
          if (latField) latField.value = pos.coords.latitude;
          if (lngField) lngField.value = pos.coords.longitude;
          
          // Feedback visual de éxito
          btn.innerHTML = '<i class="bi bi-check-circle"></i> ¡Ubicación capturada!';
          btn.classList.remove('btn-primary', 'btn-outline-secondary');
          btn.classList.add('btn-success', 'text-white');
          
          // Restaurar botón después de 3 segundos
          setTimeout(function(){
            btn.innerHTML = originalText;
            btn.disabled = false;
            btn.className = originalClass;
          }, 3000);
        }, 
        function(err){ 
          console.error("✗ Error GPS:", err.code, err.message);
          var msg = "Error al obtener la ubicación";
          
          switch(err.code) {
            case 1:
              msg = "Permiso denegado. Ve a los permisos del navegador y permite el acceso a tu ubicación.";
              break;
            case 2:
              msg = "La información de ubicación no está disponible en este momento.";
              break;
            case 3:
              msg = "Se agotó el tiempo esperando la ubicación. Intenta de nuevo.";
              break;
          }
          
          alert(msg);
          btn.innerHTML = originalText;
          btn.disabled = false;
          btn.className = originalClass;
        },
        { 
          enableHighAccuracy: true, 
          timeout: 10000, 
          maximumAge: 0 
        }
      );
    });
  } else {
    console.log("❌ Botón GPS (getLocation) no encontrado en la página");
  }
}

// Ejecutar cuando DOM esté listo
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', setupGPS);
} else {
  setupGPS();
}

