<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="d-flex justify-content-between mb-3">
  <h4>Incidentes</h4>
  <?php if (user_has_permission('incident_create')): ?><a class="btn btn-success" href="?c=incident&a=create">Registrar incidente</a><?php endif; ?>
</div>
<table class="table table-hover">
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
<?php require __DIR__ . '/../layout/footer.php'; ?>
