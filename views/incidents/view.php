<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">Detalle del Incidente #<?= htmlspecialchars((string)$incidente['id']) ?></h3>
    <a href="?c=incident" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver al listado
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 py-1">Información General</h5>
            </div>
            <div class="card-body">
                <h5 class="fw-bold mb-3"><?= htmlspecialchars((string)$incidente['titulo']) ?></h5>
                
                <p class="text-muted mb-4">
                    <?= nl2br(htmlspecialchars((string)$incidente['descripcion'])) ?>
                </p>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Fecha del registro:</span>
                        <span class="fw-medium"><?= htmlspecialchars((string)$incidente['fecha']) ?></span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Gravedad:</span>
                        <?php 
                            $badgeColor = 'bg-secondary';
                                $grav = strtolower($incidente['gravedad'] ?? '');
                                // Normalize different possible values
                                if(in_array($grav, ['baja','leve'])) $badgeColor = 'bg-success';
                                if(in_array($grav, ['media','grave'])) $badgeColor = 'bg-warning text-dark';
                                if(in_array($grav, ['alta','fatal'])) $badgeColor = 'bg-danger';
                        ?>
                        <span class="badge <?= $badgeColor ?> px-3 py-2 text-uppercase">
                            <?= htmlspecialchars((string)$incidente['gravedad']) ?>
                        </span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Reportado por:</span>
                        <span class="fw-medium"><?= htmlspecialchars((string)($incidente['reportante'] ?? 'Desconocido')) ?></span>
                    </li>
                    
                    <?php if(isset($incidente['departamento']) && $incidente['departamento']): ?>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Departamento:</span>
                        <span class="fw-medium"><?= htmlspecialchars((string)$incidente['departamento']) ?></span>
                    </li>
                    <?php endif; ?>
                    
                    <?php if(isset($incidente['tipo_evento']) && $incidente['tipo_evento']): ?>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Tipo de Evento:</span>
                        <span class="fw-medium"><?= htmlspecialchars((string)$incidente['tipo_evento']) ?></span>
                    </li>
                    <?php endif; ?>

                    <?php if(isset($incidente['estado_tramite']) && $incidente['estado_tramite']): ?>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Estado Trámite:</span>
                        <span class="fw-medium text-capitalize"><?= htmlspecialchars((string)$incidente['estado_tramite']) ?></span>
                    </li>
                    <?php endif; ?>

                    <?php if($incidente['lat'] && $incidente['lng']): ?>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Coordenadas:</span>
                        <span class="fw-medium text-end" style="font-size: 0.9em;">
                            <?= htmlspecialchars((string)$incidente['lat']) ?>, <br>
                            <?= htmlspecialchars((string)$incidente['lng']) ?>
                        </span>
                    </li>
                    <?php endif; ?>
                        <?php if(!empty($incidente['direccion'])): ?>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Dirección:</span>
                            <span class="fw-medium text-end" style="font-size: 0.9em;">
                                <?= htmlspecialchars((string)$incidente['direccion']) ?>
                            </span>
                        </li>
                        <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 py-1">Evidencias Fotográficas (<?= count($fotografias) ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($fotografias)): ?>
                    <div class="alert alert-light border d-flex align-items-center" role="alert">
                        <i class="bi bi-info-circle me-2 text-primary"></i>
                        No hay fotografías registradas para este incidente.
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($fotografias as $foto): ?>
                            <div class="col-6 col-sm-4">
                                <div class="card h-100 border-0 shadow-sm" style="cursor: zoom-in;" 
                                     onclick="openPhotoModal('uploads/<?= htmlspecialchars(rawurlencode($foto['archivo']), ENT_QUOTES) ?>', '<?= htmlspecialchars($foto['fecha'], ENT_QUOTES) ?>')">
                                    
                                    <div class="ratio ratio-1x1">
                                        <img src="uploads/<?= htmlspecialchars(rawurlencode($foto['archivo'])) ?>" 
                                             class="card-img-top object-fit-cover rounded" 
                                             alt="Evidencia fotográfica">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white border-bottom-0">
                <h5 class="modal-title" id="photoModalLabel">Visor de Evidencia</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center bg-dark p-0">
                <img id="modalImageExpanded" src="" class="img-fluid" style="max-height: 80vh; object-fit: contain;" alt="Evidencia Ampliada">
            </div>
            <div class="modal-footer bg-dark border-top-0 d-flex justify-content-between">
                <span id="modalImageDate" class="text-light small"></span>
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cerrar Visor</button>
            </div>
        </div>
    </div>
</div>

<script>
function openPhotoModal(imagePath, captureDate) {
    // Asignar el path de la imagen al modal
    document.getElementById('modalImageExpanded').src = imagePath;
    
    // Asignar la fecha en el footer del modal
    document.getElementById('modalImageDate').innerText = 'Capturada el: ' + captureDate;
    
    // Instanciar y mostrar el modal nativo de Bootstrap 5
    var photoModal = new bootstrap.Modal(document.getElementById('photoModal'));
    photoModal.show();
}
</script>

<style>
.object-fit-cover {
    object-fit: cover;
}
</style>

<?php require __DIR__ . '/../layout/footer.php'; ?>