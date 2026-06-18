<?php require __DIR__ . '/../layout/header.php'; ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<?php if (!empty($error)): ?>
  <div class="alert alert-warning" role="alert"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="row g-3 mb-3">
  <div class="col-md-4 col-lg-2">
    <div class="card p-3 h-100">
      <h6 class="text-muted mb-1">Total incidentes</h6>
      <h2 class="mb-0"><?= htmlspecialchars((string) $totalIncidentes) ?></h2>
    </div>
  </div>

  <div class="col-md-4 col-lg-2">
    <div class="card p-3 h-100">
      <h6 class="text-muted mb-1">Leves</h6>
      <h2 class="mb-0 text-success"><?= htmlspecialchars((string) $totalLeves) ?></h2>
    </div>
  </div>

  <div class="col-md-4 col-lg-2">
    <div class="card p-3 h-100">
      <h6 class="text-muted mb-1">Medias</h6>
      <h2 class="mb-0 text-info"><?= htmlspecialchars((string) $totalMedias) ?></h2>
    </div>
  </div>

  <div class="col-md-4 col-lg-2">
    <div class="card p-3 h-100">
      <h6 class="text-muted mb-1">Graves</h6>
      <h2 class="mb-0 text-warning"><?= htmlspecialchars((string) $totalGraves) ?></h2>
    </div>
  </div>

  <div class="col-md-4 col-lg-2">
    <div class="card p-3 h-100">
      <h6 class="text-muted mb-1">Fatales</h6>
      <h2 class="mb-0 text-danger"><?= htmlspecialchars((string) $totalFatales) ?></h2>
    </div>
  </div>

  <div class="col-md-4 col-lg-2">
    <div class="card p-3 h-100">
      <h6 class="text-muted mb-1">Este mes</h6>
      <h2 class="mb-0"><?= htmlspecialchars((string) $totalEsteMes) ?></h2>
    </div>
  </div>

  <div class="col-md-4 col-lg-2">
    <div class="card p-3 h-100">
      <h6 class="text-muted mb-1">Cerrados</h6>
      <h2 class="mb-0 text-secondary"><?= htmlspecialchars((string) $totalCerrados) ?></h2>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card p-3 shadow-sm">
      <h5>Mapa de incidentes</h5>
      <div id="map" style="height:350px;background:#eee; border-radius: 8px; z-index: 1;">(Cargando mapa interactivo...)</div>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <div class="col-12 col-md-6">
    <div class="card p-3 shadow-sm h-100">
      <h5 class="card-title">Incidentes por Gravedad</h5>
      <div style="position: relative; height:250px;">
        <canvas id="chartGravedad"></canvas>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6">
    <div class="card p-3 shadow-sm h-100">
      <h5 class="card-title">Incidentes por Departamento</h5>
      <div style="position: relative; height:250px;">
        <canvas id="chartDepto"></canvas>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6">
    <div class="card p-3 shadow-sm h-100">
      <h5 class="card-title">Tendencia Mensual (Año Actual)</h5>
      <div style="position: relative; height:250px;">
        <canvas id="chartMeses"></canvas>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6">
    <div class="card p-3 shadow-sm h-100">
      <h5 class="card-title">Incidentes por Tipo de Evento</h5>
      <div style="position: relative; height:250px;">
        <canvas id="chartEvento"></canvas>
      </div>
    </div>
  </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // ========================================================
    // 1. CONFIGURACIÓN DEL MAPA GEOREFERENCIADO (LEAFLET)
    // ========================================================
    
    // Limpiar el contenedor
    const mapContainer = document.getElementById('map');
    mapContainer.innerHTML = '';

    // Inicializar mapa centrado predeterminadamente
    const map = L.map('map').setView([13.3444, -88.4392], 9);

    // Cargar la capa de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Obtener datos inyectados desde PHP
    const incidentesData = <?= $jsonMapa ?? '[]' ?>;
    const marcadoresArray = [];

    // Recorrer los incidentes y colocar los marcadores
    incidentesData.forEach(incidente => {
        const lat = parseFloat(incidente.lat);
        const lng = parseFloat(incidente.lng);

        if (!isNaN(lat) && !isNaN(lng)) {
            const marker = L.marker([lat, lng]).addTo(map);
            
            // Asignar colores de Bootstrap para las insignias del Popup (acepta sinónimos)
            let badgeColor = 'bg-secondary';
            const gravedadStr = (incidente.gravedad || '').toLowerCase();
            if (['baja','leve'].includes(gravedadStr)) badgeColor = 'bg-success';
            else if (['media','moderada','moderado'].includes(gravedadStr)) badgeColor = 'bg-warning text-dark';
            else if (['alta','grave','fatal'].includes(gravedadStr)) badgeColor = 'bg-danger';

            // Construir el contenido del Popup (ID, Título, Fecha, Gravedad)
            const popupContent = `
                <div style="font-family: inherit; min-width: 180px;">
                    <h6 style="margin-bottom: 5px; font-weight: bold;">ID: ${incidente.id}</h6>
                    <p style="margin: 0 0 5px 0;"><b>Título:</b> ${incidente.titulo}</p>
                    <p style="margin: 0 0 8px 0;"><b>Fecha:</b> ${incidente.fecha}</p>
                    <p style="margin: 0;"><b>Gravedad:</b> <span class="badge ${badgeColor}">${incidente.gravedad.toUpperCase()}</span></p>
                </div>
            `;
            
            marker.bindPopup(popupContent);
            marcadoresArray.push([lat, lng]);
        }
    });

    // Ajustar el zoom y la posición para mostrar todos los marcadores existentes
    if (marcadoresArray.length > 0) {
        map.fitBounds(marcadoresArray, { padding: [40, 40] });
    }


    // ========================================================
    // 2. CONFIGURACIÓN DE GRÁFICAS EXISTENTES (CHART.JS)
    // ========================================================

    // 1. Configuración de Incidentes por Gravedad (Doughnut)
    // (Nota: Asegúrate de inyectar las variables $jsonGravedad, etc. en tu controlador si aún no lo están para que esto funcione)
    if(typeof <?= isset($jsonGravedad) ? 'true' : 'false' ?> !== 'undefined' && <?= isset($jsonGravedad) ? 'true' : 'false' ?>) {
        const dataGravedad = <?= $jsonGravedad ?? '[]' ?>;
        const labelsGravedad = dataGravedad.map(item => item.gravedad || 'No definido');
        const valuesGravedad = dataGravedad.map(item => item.total);
        
        new Chart(document.getElementById('chartGravedad'), {
            type: 'doughnut',
            data: {
                labels: labelsGravedad,
                datasets: [{
                    data: valuesGravedad,
                    backgroundColor: ['#198754', '#ffc107', '#dc3545', '#6c757d'],
                    borderWidth: 1
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    // 2. Configuración de Incidentes por Departamento (Bar Horizontal)
    if(typeof <?= isset($jsonDepto) ? 'true' : 'false' ?> !== 'undefined' && <?= isset($jsonDepto) ? 'true' : 'false' ?>) {
        const dataDepto = <?= $jsonDepto ?? '[]' ?>;
        const labelsDepto = dataDepto.map(item => item.departamento);
        const valuesDepto = dataDepto.map(item => item.total);

        new Chart(document.getElementById('chartDepto'), {
            type: 'bar',
            data: {
                labels: labelsDepto,
                datasets: [{
                    label: 'Cantidad',
                    data: valuesDepto,
                    backgroundColor: '#0d6efd',
                    borderRadius: 4
                }]
            },
            options: { 
                indexAxis: 'y',
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    }

    // 3. Configuración de Tendencia Mensual (Line)
    if(typeof <?= isset($jsonMesesLabels) ? 'true' : 'false' ?> !== 'undefined' && <?= isset($jsonMesesLabels) ? 'true' : 'false' ?>) {
        const labelsMeses = <?= $jsonMesesLabels ?? '[]' ?>;
        const valuesMeses = <?= $jsonMesesData ?? '[]' ?>;

        new Chart(document.getElementById('chartMeses'), {
            type: 'line',
            data: {
                labels: labelsMeses,
                datasets: [{
                    label: 'Total Incidentes',
                    data: valuesMeses,
                    borderColor: '#6f42c1',
                    backgroundColor: 'rgba(111, 66, 193, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // 4. Configuración de Incidentes por Tipo de Evento (Polar Area o Pie)
    if(typeof <?= isset($jsonEvento) ? 'true' : 'false' ?> !== 'undefined' && <?= isset($jsonEvento) ? 'true' : 'false' ?>) {
        const dataEvento = <?= $jsonEvento ?? '[]' ?>;
        const labelsEvento = dataEvento.map(item => item.tipo_evento);
        const valuesEvento = dataEvento.map(item => item.total);

        new Chart(document.getElementById('chartEvento'), {
            type: 'pie',
            data: {
                labels: labelsEvento,
                datasets: [{
                    data: valuesEvento,
                    backgroundColor: ['#0dcaf0', '#fd7e14', '#20c997', '#d63384', '#0a58ca'],
                    borderWidth: 1
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>