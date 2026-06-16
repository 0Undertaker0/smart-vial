<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="d-flex justify-content-between mb-3">
  <h4>Incidentes</h4>
  <?php if (user_has_permission('incident_create')): ?><a class="btn btn-success" href="?c=incident&a=create">Registrar incidente</a><?php endif; ?>
</div>

<!-- Filtros avanzados -->
<div class="card mb-3">
  <div class="card-body">
    <form id="incidentFilters" class="row g-2">
      <div class="col-md-3"><label class="form-label">Departamento</label><input class="form-control" name="departamento" list="deps" /></div>
      <div class="col-md-3"><label class="form-label">Municipio</label><input class="form-control" name="municipio" list="muni" /></div>
      <div class="col-md-3"><label class="form-label">Zona</label><input class="form-control" name="zona" placeholder="Texto libre" /></div>
      <div class="col-md-3"><label class="form-label">Fecha inicio</label><input class="form-control" type="date" name="fecha_inicio" /></div>
      <div class="col-md-3"><label class="form-label">Fecha fin</label><input class="form-control" type="date" name="fecha_fin" /></div>
      <div class="col-md-3"><label class="form-label">Gravedad</label>
        <select class="form-select" name="gravedad">
          <option value="">--</option>
          <option value="baja">Leve</option>
          <option value="media">Media</option>
          <option value="alta">Grave</option>
          <option value="fatal">Fatal</option>
        </select>
      </div>
      <div class="col-md-3"><label class="form-label">Responsable (Agente)</label>
        <select class="form-select" name="responsable_agente_id">
          <option value="">--</option>
          <?php foreach($agentes ?? [] as $a): ?><option value="<?= e($a['id']) ?>"><?= e($a['nombre']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3"><label class="form-label">Unidad PNC</label><input class="form-control" name="responsable_unidad" placeholder="Unidad" /></div>
      <div class="col-md-3"><label class="form-label">Estado trámite</label>
        <select class="form-select" name="estado_tramite">
          <option value="">--</option>
          <option>Registrado</option>
          <option>En revisión</option>
          <option>Validado</option>
          <option>Cerrado</option>
        </select>
      </div>
      <div class="col-md-3"><label class="form-label">Tipo de evento</label>
        <select class="form-select" name="tipo_evento">
          <option value="">--</option>
          <option>Colisión</option>
          <option>Atropello</option>
          <option>Fuga</option>
          <option>Otro</option>
        </select>
      </div>
      <div class="col-12 mt-2">
        <button type="button" id="applyFilters" class="btn btn-primary">Aplicar</button>
        <button type="button" id="clearFilters" class="btn btn-secondary">Limpiar</button>
      </div>
    </form>
  </div>
</div>

<!-- Data lists for departamento/municipio (if available) -->
<?php if (!empty($departamentos)): ?>
  <datalist id="deps"><?php foreach($departamentos as $d): ?><option value="<?= e($d) ?>"><?php endforeach; ?></datalist>
<?php endif; ?>
<?php if (!empty($municipios)): ?>
  <datalist id="muni"><?php foreach($municipios as $m): ?><option value="<?= e($m) ?>"><?php endforeach; ?></datalist>
<?php endif; ?>

<table class="table table-hover" id="incidentsTable">
  <thead><tr><th>ID</th><th>Título</th><th>Fecha</th><th>Gravedad</th><th>Reportante</th></tr></thead>
  <tbody>
    <?php foreach($incidents as $it): ?>
    <tr>
      <td><?= e($it['id']) ?></td>
      <td><?= e($it['titulo']) ?></td>
      <td><?= e($it['fecha']) ?></td>
      <td><?= e($it['gravedad']) ?></td>
      <td><?= e($it['reportante']) ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<script src="assets/js/incidents_filters.js"></script>
<?php require __DIR__ . '/../layout/footer.php'; ?>
