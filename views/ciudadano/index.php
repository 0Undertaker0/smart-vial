<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Ciudadana - SMARTVIAL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .citizen-hero { 
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); 
            color: white; 
            padding: 3rem 0; 
            margin-bottom: 2rem; 
            border-radius: 0 0 1.5rem 1.5rem; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
        }
        .search-card, .result-card { border: none; border-radius: 1rem; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <header class="citizen-hero text-center">
        <div class="container">
            <h1 class="display-5 fw-bold"><i class="bi bi-shield-check"></i> SMARTVIAL</h1>
            <p class="lead mb-0">Portal de Consulta Ciudadana de Siniestros Viales</p>
        </div>
    </header>

    <main class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                
                <div class="card search-card mb-4 p-2">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-4">Rastrear Incidente</h5>
                        <form action="" method="GET" class="d-flex flex-column align-items-center">
                            <input type="hidden" name="c" value="ciudadano">
                            
                            <div class="input-group input-group-lg mb-3">
                                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" name="expediente" 
                                       placeholder="Ingrese el N° de Expediente (ID)" 
                                       value="<?= htmlspecialchars($busqueda) ?>" required>
                                <button class="btn btn-primary px-4" type="submit">Consultar</button>
                            </div>
                            <small class="text-muted">El número de expediente le fue proporcionado al momento del registro.</small>
                        </form>
                    </div>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-warning text-center rounded-3 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if ($incidente): ?>
                    <div class="card result-card border-top border-4 border-success p-2 animate__animated animate__fadeIn">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="card-title text-success mb-0">
                                    <i class="bi bi-check-circle-fill me-2"></i>Expediente #<?= htmlspecialchars((string)$incidente['id']) ?>
                                </h5>
                            </div>
                            
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i class="bi bi-calendar-event me-2"></i>Fecha de registro:</span>
                                    <span class="fw-medium"><?= htmlspecialchars((string)$incidente['fecha']) ?></span>
                                </li>
                                
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i class="bi bi-activity me-2"></i>Estado del trámite:</span>
                                    <?php $estado = $incidente['estado_tramite'] ?? 'En proceso'; ?>
                                    <span class="badge bg-primary rounded-pill px-3 py-2 text-capitalize">
                                        <?= htmlspecialchars((string)$estado) ?>
                                    </span>
                                </li>

                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i class="bi bi-car-front me-2"></i>Tipo de evento:</span>
                                    <span class="fw-medium"><?= htmlspecialchars((string)($incidente['tipo_evento'] ?? 'No especificado')) ?></span>
                                </li>

                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i class="bi bi-exclamation-octagon me-2"></i>Gravedad:</span>
                                    <?php 
                                        $badgeColor = 'bg-secondary';
                                        $grav = strtolower($incidente['gravedad'] ?? '');
                                        if (in_array($grav, ['baja','leve'])) $badgeColor = 'bg-success';
                                        if (in_array($grav, ['media','moderada','moderado'])) $badgeColor = 'bg-warning text-dark';
                                        if (in_array($grav, ['alta','grave','fatal'])) $badgeColor = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $badgeColor ?> px-3 py-2 text-uppercase">
                                        <?= htmlspecialchars((string)$incidente['gravedad']) ?>
                                    </span>
                                </li>
                            </ul>
                            
                            <div class="mt-4 text-center">
                                <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i>Para más detalles, preséntese a la unidad correspondiente con su documento de identidad.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </main>

    <footer class="text-center py-4 mt-5 text-muted small">
        <div class="container">
            &copy; <?= date('Y') ?> SMARTVIAL. Todos los derechos reservados.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>