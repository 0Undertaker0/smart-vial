<?php require __DIR__ . '/../layout/header.php'; ?>

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

<div class="row">
  <div class="col-12">
    <div class="card p-3">
      <h5>Mapa de incidentes</h5>
      <div id="map" style="height:300px;background:#eee;">(Mapa interactivo en versión completa)</div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>