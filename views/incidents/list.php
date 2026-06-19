<?php require __DIR__ . '/../layout/header.php'; ?>
  <div class="d-flex justify-content-between mb-3">
  <h4>Incidentes</h4>
  <?php if (user_has_permission('incident_create') || (function_exists('role_allows_menu') && role_allows_menu('incident'))): ?><a class="btn btn-success" href="?c=incident&a=create">Registrar incidente</a><?php endif; ?>
</div>

<!-- Filtros avanzados -->
<div class="card mb-3">
  <div class="card-body">
    <form id="incidentFilters" class="row g-2">
      <div class="col-md-3"><label class="form-label">Departamento</label><input class="form-control" name="departamento" list="deps" value="<?= e($filters['departamento'] ?? '') ?>" /></div>
      <div class="col-md-3"><label class="form-label">Municipio</label><input class="form-control" name="municipio" list="muni" value="<?= e($filters['municipio'] ?? '') ?>" /></div>
      <div class="col-md-3"><label class="form-label">Zona</label><input class="form-control" name="zona" placeholder="Texto libre" value="<?= e($filters['zona'] ?? '') ?>" /></div>
      <div class="col-md-3"><label class="form-label">Fecha inicio</label><input class="form-control" type="date" name="fecha_inicio" value="<?= e($filters['fecha_inicio'] ?? '') ?>" /></div>
      <div class="col-md-3"><label class="form-label">Fecha fin</label><input class="form-control" type="date" name="fecha_fin" value="<?= e($filters['fecha_fin'] ?? '') ?>" /></div>
      <div class="col-md-3"><label class="form-label">Gravedad</label>
        <select class="form-select" name="gravedad">
          <option value="">--</option>
          <option value="baja" <?= (isset($filters['gravedad']) && $filters['gravedad']==='baja')? 'selected':'' ?>>Leve</option>
          <option value="media" <?= (isset($filters['gravedad']) && $filters['gravedad']==='media')? 'selected':'' ?>>Media</option>
          <option value="alta" <?= (isset($filters['gravedad']) && $filters['gravedad']==='alta')? 'selected':'' ?>>Grave</option>
          <option value="fatal" <?= (isset($filters['gravedad']) && $filters['gravedad']==='fatal')? 'selected':'' ?>>Fatal</option>
        </select>
      </div>
      <div class="col-md-3"><label class="form-label">Responsable (Agente)</label>
        <select class="form-select" name="responsable_agente_id">
          <option value="">--</option>
          <?php foreach($agentes ?? [] as $a): ?><option value="<?= e($a['id']) ?>" <?= (isset($filters['responsable_agente_id']) && (string)$filters['responsable_agente_id'] === (string)$a['id'])? 'selected':'' ?>><?= e($a['nombre']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3"><label class="form-label">Unidad PNC</label><input class="form-control" name="responsable_unidad" placeholder="Unidad" value="<?= e($filters['responsable_unidad'] ?? '') ?>" /></div>
      <div class="col-md-3"><label class="form-label">Estado trámite</label>
        <select class="form-select" name="estado_tramite">
          <option value="">--</option>
          <option <?= (isset($filters['estado_tramite']) && $filters['estado_tramite']==='Registrado')? 'selected':'' ?>>Registrado</option>
          <option <?= (isset($filters['estado_tramite']) && $filters['estado_tramite']==='En revisión')? 'selected':'' ?>>En revisión</option>
          <option <?= (isset($filters['estado_tramite']) && $filters['estado_tramite']==='Validado')? 'selected':'' ?>>Validado</option>
          <option <?= (isset($filters['estado_tramite']) && $filters['estado_tramite']==='Cerrado')? 'selected':'' ?>>Cerrado</option>
        </select>
      </div>
      <div class="col-md-3"><label class="form-label">Tipo de evento</label>
        <select class="form-select" name="tipo_evento">
          <option value="">--</option>
          <option <?= (isset($filters['tipo_evento']) && $filters['tipo_evento']==='Colisión')? 'selected':'' ?>>Colisión</option>
          <option <?= (isset($filters['tipo_evento']) && $filters['tipo_evento']==='Atropello')? 'selected':'' ?>>Atropello</option>
          <option <?= (isset($filters['tipo_evento']) && $filters['tipo_evento']==='Fuga')? 'selected':'' ?>>Fuga</option>
          <option <?= (isset($filters['tipo_evento']) && $filters['tipo_evento']==='Otro')? 'selected':'' ?>>Otro</option>
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
  <thead><tr><th>ID</th><th>Título</th><th>Fecha</th><th>Gravedad</th><th>Reportante</th><th>Acciones</th></tr></thead>
  <tbody>
    <?php foreach($incidents as $it): ?>
    <tr>
      <td><?= e($it['id']) ?></td>
      <td><?= e($it['titulo']) ?></td>
      <td><?= e($it['fecha']) ?></td>
      <td><?= e($it['gravedad']) ?></td>
      <td><?= e($it['reportante']) ?></td>
      <td>
        <?php if (user_has_permission('incident_edit')): ?><a class="btn btn-sm btn-primary edit-btn" href="?c=incident&a=edit&id=<?= e($it['id']) ?>" data-id="<?= e($it['id']) ?>">Editar</a><?php endif; ?>
          <?php if (user_has_permission('incident_delete')): ?>
          <form method="post" action="?c=incident&a=delete" style="display:inline" onsubmit="return confirm('¿Eliminar este incidente? Esta acción puede revertirse desde administración.');">
            <?= csrf_input() ?>
            <input type="hidden" name="id" value="<?= e($it['id']) ?>">
            <button class="btn btn-sm btn-danger">Eliminar</button>
          </form>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<script>
  window.__PERMS = {
    incident_edit: <?= user_has_permission('incident_edit') ? 'true' : 'false' ?>,
    incident_delete: <?= user_has_permission('incident_delete') ? 'true' : 'false' ?>
  };
  window.__CSRF = '<?= csrf_token() ?>';
</script>
<!-- Edit modal -->
<div class="modal fade" id="editIncidentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar incidente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editIncidentForm">
          <input type="hidden" name="id" id="edit_id">
          <div class="mb-3">
            <label>Título</label>
            <input class="form-control" name="titulo" id="edit_titulo" required>
          </div>
          <div class="mb-3">
            <label>Descripción</label>
            <textarea class="form-control" name="descripcion" id="edit_descripcion" rows="4"></textarea>
          </div>
          <div class="mb-3">
            <label>Gravedad</label>
            <select class="form-select" name="gravedad" id="edit_gravedad">
              <option value="baja">baja</option>
              <option value="media">media</option>
              <option value="alta">alta</option>
              <option value="fatal">fatal</option>
            </select>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3"><label>Latitud</label><input class="form-control" name="lat" id="edit_lat"></div>
            <div class="col-md-6 mb-3"><label>Longitud</label><input class="form-control" name="lng" id="edit_lng"></div>
          </div>
          <div class="mb-3">
            <label>Dirección</label>
            <input class="form-control" name="direccion" id="edit_direccion">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" id="saveEditBtn" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
<script src="assets/js/incidents_filters.js"></script>
<?php require __DIR__ . '/../layout/footer.php'; ?>
