console.log("✓ gps.js cargado");

// Esperar a que el DOM esté listo
window.addEventListener('load', function() {
    console.log("✓ Página completamente cargada, buscando botón GPS...");
    
    const btnGps = document.getElementById('getLocation');
    console.log("btnGps elemento:", btnGps);
    
    if (!btnGps) {
        console.error("❌ No se encontró el botón con id 'getLocation'");
        return;
    }
    
    console.log("✓ Botón GPS encontrado");
    
    btnGps.addEventListener('click', function() {
        console.log("✓✓✓ CLICK EN BOTÓN GPS ✓✓✓");
        alert("¡GPS Activado!");
        
        const latInput = document.getElementById('lat');
        const lngInput = document.getElementById('lng');

        if (!navigator.geolocation) {
            alert("Tu navegador no soporta geolocalización.");
            return;
        }

        const originalText = btnGps.innerHTML;
        btnGps.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Buscando...';
        btnGps.disabled = true;

        navigator.geolocation.getCurrentPosition(
            function(position) {
                console.log("✓ Ubicación obtenida:", position.coords.latitude, position.coords.longitude);
                latInput.value = position.coords.latitude;
                lngInput.value = position.coords.longitude;
                
                btnGps.innerHTML = '<i class="bi bi-check-circle"></i> Ubicación capturada';
                btnGps.classList.remove('btn-outline-secondary');
                btnGps.classList.add('btn-success', 'text-white');
                
                alert("Ubicación capturada: " + position.coords.latitude + ", " + position.coords.longitude);
                
                setTimeout(() => {
                    btnGps.innerHTML = originalText;
                    btnGps.disabled = false;
                    btnGps.classList.remove('btn-success', 'text-white');
                    btnGps.classList.add('btn-outline-secondary');
                }, 3000);
            },
            function(error) {
                console.error("❌ Error GPS código:", error.code);
                let msg = "Error al obtener la ubicación.";
                if (error.code === error.PERMISSION_DENIED) {
                    msg = "Permiso denegado. Debes permitir el acceso a tu ubicación.";
                } else if (error.code === error.POSITION_UNAVAILABLE) {
                    msg = "La información de ubicación no está disponible.";
                } else if (error.code === error.TIMEOUT) {
                    msg = "Tiempo de espera agotado.";
                }
                alert(msg);
                btnGps.innerHTML = originalText;
                btnGps.disabled = false;
            },
            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            }
        );
    });
});
